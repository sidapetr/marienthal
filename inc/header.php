<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo (!empty($pageTitle)?$pageTitle.' - ':'')?>Marienthal workshop app</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="sidp00">
    <meta name="keywords" content="Marienthal, intercultural, seminar, workshop">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div id="container">
        <nav>
            <?php
            if(isset($_SESSION['user_id'])){
                require_once 'user.php';
                $query=$db->prepare('SELECT * FROM mt_user WHERE id=:id LIMIT 1;');
                $query->execute([':id'=>$_SESSION['user_id']]);
                $user=$query->fetch(PDO::FETCH_ASSOC);
                echo ('<span id="choosen">my workshops: prefered - '.htmlspecialchars($user['w_prefered']).
                                        '| alternative - '.htmlspecialchars($user['w_alternative']).'</span>
                       <ul>
                        <li id="loggedAs">logged in as '.htmlspecialchars($_SESSION['user_name']).'</li>
                        <a href="logout.php"><li>logout</li></a>
                       </ul>');
            }else{
                require_once 'facebook.php';
                $fbHelper = $fb->getRedirectLoginHelper();  //helper pro vytvoreni odkazu
                $permisions = ['email'];
                $callbackUrl = htmlspecialchars('https://eso.vse.cz/~sidp00/marienthal/fb-callback.php');
                $fbLoginUrl = $fbHelper->getLoginUrl($callbackUrl, $permisions);
                echo ('<h1>Marienthal workshops</h1>
                       <ul>
                           <a href="login.php"><li>sign in</li></a>
                           <a href="'.$fbLoginUrl.'"><li>sign in with facebook</li></a>
                           <a href="register.php"><li>sign up</li></a>
                       </ul>');
            }
            ?>
        </nav>
        <main>
