<?php
require_once 'inc/user.php';
include 'inc/header.php';
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

            header('Location: index.php');
            exit();
        }else{
            $errors=true;
        }
    }else{
        $errors=true;
    }
}

/*TODO: vypisovani chyby pod formular
        zapomenute heslo
        facebook link
        mazani forgotten passwd timestampu z DB
*/
?>

<h2>Sign in</h2>
<form method="post">
    <div>
        <label for="mail">e-mail</label>
        <input name="mail" type="email" required>
    </div>
    <div>
        <label for="passwd">password</label>
        <input name="passwd" type="password" required>
    </div>
    <input type="submit" value="log in">
    <a href="index.php">cancel</a>
    <a href="">Log in with facebook instead</a>
</form>
<div>
    Don't have an account? <a href="register.php">Sign up</a>
</div>


<?php
include 'inc/footer.php';