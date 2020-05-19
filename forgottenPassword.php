<?php
require_once 'inc/user.php';
$pageTitle = 'password recovery page';
use PHPMailer\PHPMailer\PHPMailer;

if(!empty($_SESSION['user_id'])){
    header('Location: index.php');
    exit();
}

if(!empty($_POST['mail'])){
    $forgotten=$db->prepare('SELECT * FROM mt_user WHERE email=:mail LIMIT 1;');
    $forgotten->execute([':mail'=>trim($_POST['mail'])]);

    if($user=$forgotten->fetch(PDO::FETCH_ASSOC)){

        #region odkaz
        $code='mt'.rand(100000, 200000);
        $save=$db->prepare('INSERT INTO mt_forgotten_passwords (user_id, code) VALUES (:user_id, :code);');
        $save->execute([':user_id'=>$user['id'], ':code'=>$code]);

        $read=$db->prepare('SELECT * FROM mt_forgotten_passwords WHERE user_id=:user_id AND code=:code ORDER BY id DESC LIMIT 1;');
        $read->execute([':user_id'=>$user['id'],":code"=>$code]);
        $request=$read->fetch(PDO::FETCH_ASSOC);

        $link='https://eso.vse.cz/~sidp00/marienthal/renewPassword.php';
        $link.='?user='.$request['user_id'].'&code='.$request['code'].'&request='.$request['id'];

        #endregion odkaz

        #region mailer
        $mailer=new PHPMailer(false);
        $mailer->isSendmail();

        $mailer->addAddress($user['email'], $user['name']);
        $mailer->setFrom('sidp00@vse.cz');              //nahradit za neco jako app@network-marienthal.eu

        $mailer->CharSet='utf-8';
        $mailer->Subject='Forgotten password recovery';
        $mailer->isHTML(true);
        $mailer->Body='<html>
                            <head>
                                <meta charset="utf-8"/>
                            </head>
                            <body>
                                Click <a href="'.htmlspecialchars($link).'">this link</a> to recover your Marienthal workshop app password.
                            </body>
                       </html>';
        $mailer->AltBody='Click this link: '.$link.' to recover your Marienthal workshop app password.';
        $mailer-> send();
        #endregion mailer

        header('Location: forgottenPassword?mailed=mailed');
    }else{
        $error=true;
    }
}

include 'inc/header.php';
?>

<h1>Password recovery</h1>
<?php
if(@$_GET['mailed']=='mailed'){
    echo '<div>Please check your inbox to complete the password recovery process.</div>';
    echo '<a href="index.php">Back to the homepage</a>';
}else{
?>
    <form method="post" id="forgotten">
        <div>
        <label for="mail">e-mail</label>
        <input name="mail" type="email" value="<?php echo htmlspecialchars(@$_POST['mail']);?>" required>
        </div>
        <?php echo (@$error?'<div>Invalid e-mail</div>':'');?>
        <input type="submit" value="Submit">
        <a href="login.php">Login</a>
        <a href="index.php">Cancel</a>
    </form>
<?php
}
?>


<?php
include 'inc/footer.php';