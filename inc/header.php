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
            <h1>Get your workshop!</h1>
            <ul>
            <?php
            if(isset($_SESSION['user_name'])){
                echo ('<li>logged in as '.$_SESSION['user_name'].'</li>
                       <a href="logout.php"><li>logout</li></a>');
            }else{
                echo ('<a href="login.php"><li>sign in</li></a>
                       <a href="register.php"><li>sign up</li></a>');
            }
            ?>


            </ul>
        </nav>
        <main>
