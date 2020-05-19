<?php
require_once 'inc/user.php';
$pageTitle = 'password renew page';

if(!empty($_SESSION['user_id'])){
    header('Location: index.php');
    exit();
}
$errors=[];
$codeError=false;

if(!empty($_REQUEST['code'])){
    $query=$db->prepare('SELECT * FROM mt_forgotten_passwords WHERE id=:id AND code=:code AND user_id=:user_id LIMIT 1');
    $query->execute([':user_id'=>$_REQUEST['user'], ':code'=>$_REQUEST['code'], ':id'=>$_REQUEST['request']]);

    if($request=$query->fetch(PDO::FETCH_ASSOC)){
        if(strtotime($request['created'])<(time()-7200)){           //platnost linku 2 hodiny
            $codeError=true;
        }
    }else{
        $codeError=true;
    }
}

if(!empty($_POST)&&!$codeError){
    if(empty($_POST['passwd']) || (strlen($_POST['passwd'])<6)){
        $errors['passwd']='A password with minimal length of 6 symbols is required.';
    }
    if($_POST['passwd']!=$_POST['passwd2']){
        $errors['passwd2']='Password fields cannot differ.';
    }

    if(empty($errors)){
        $save=$db->prepare('UPDATE mt_user SET password=:passwd WHERE id=:user_id LIMIT 1;');
        $save->execute([':passwd'=>password_hash($_POST['passwd'],PASSWORD_DEFAULT),':user_id'=>$request['user_id']]);

        $forgottenDelete = $db->prepare('DELETE FROM mt_forgotten_passwords WHERE user_id = :user_id;');
        $forgottenDelete ->execute([':user_id'=> $request['user_id']]);

        $login=$db->prepare('SELECT * FROM mt_user WHERE id=:user_id LIMIT 1;');
        $login->execute([':user_id'=>$request['user_id']]);
        $user=$login->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id']=$user['id'];
        $_SESSION['user_name']=$user['name'];
        $_SESSION['user_role']=$user['role'];

        header('Location: index.php');
        exit();
    }
}

include 'inc/header.php';
?>

    <h1>Save new password</h1>
    <?php
    if($codeError){
        echo '<div>Password recovery code timed out and is no longer valid.</div>
              <a href="index.php">Back to homepage</a>';
    }else{
    ?>
        <form method="post">
            <div>
                <label for="passwd">New password (minimal lenght 6)</label>
                <input type="password" name="passwd" required>
            </div>
            <?php
            echo(empty($errors['passwd']))?'':'<div class="formError">'.$errors['passwd'].'</div>';
            ?>
            <div>
                <label for="passwd2">Retype your password</label>
                <input type="password" name="passwd2" required>
            </div>
            <?php
            echo(empty($errors['passwd2']))?'':'<div class="formError">'.$errors['passwd2'].'</div>';
            ?>
            <input type="submit" value="Save">
            <a href="index.php">Cancel</a>
        </form>
        <?php
        }
        ?>

<?php
include 'inc/footer.php';