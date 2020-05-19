<?php
require_once 'inc/user.php';
$pageTitle = 'login page';

if(!empty($_SESSION['user_id'])){
    header('Location: index.php');
    exit();
}

if(!empty($_POST)){
    $errors=false;
    $login=$db->prepare('SELECT * FROM mt_user WHERE email=:mail LIMIT 1;');
    $login->execute([':mail'=>trim($_POST['mail'])]);
    if($user=$login->fetch(PDO::FETCH_ASSOC)){
        if(password_verify($_POST['passwd'], $user['password'])){
            $_SESSION['user_id']=$user['id'];
            $_SESSION['user_name']=$user['name'];
            $_SESSION['user_role']=$user['role'];

            $forgottenDelete = $db->prepare('DELETE FROM mt_forgotten_passwords WHERE user_id = :user_id;');
            $forgottenDelete ->execute([':user_id'=> $user['id']]);

            header('Location: index.php');
            exit();
        }else{
            $errors=true;
        }
    }else{
        $errors=true;
    }
}

include 'inc/header.php';
?>

<h1>Sign in</h1>
    <a href="facebookLogin.php">Log in with facebook instead</a>
<form method="post">
    <div>
        <label for="mail">e-mail</label>
        <input name="mail" type="email" required>
    </div>
    <div>
        <label for="passwd">password</label>
        <input name="passwd" type="password" required>
    </div>
    <?php
        if(@$errors){
            echo ('<div class="formError">
                        A combination of e-mail and password is incorrect.
                        <a href="forgottenPassword.php">Forgot your password?</a>
                    </div>');
        }
    ?>
    <input type="submit" value="log in">
</form>
<div>
    Don't have an account? <a href="register.php">Sign up</a>
</div>


<?php
include 'inc/footer.php';