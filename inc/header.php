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
                if($_SESSION['user_role']=="student"){
                    $query=$db->prepare(
                        'SELECT pref.name as prefered, alt.name as alternative FROM mt_user as user 
                                    left join mt_role as pref on (user.w_prefered=pref.id) 
                                    left join mt_role as alt on (user.w_alternative=alt.id)
                                    WHERE user.id=:id;');
                    $query->execute([':id'=>$_SESSION['user_id']]);
                    $user=$query->fetch(PDO::FETCH_ASSOC);
                    echo '<span id="choosen">my workshops: prefered - '.htmlspecialchars($user['prefered']).
                        '| alternative - '.htmlspecialchars($user['alternative']).'</span>';
                }
                echo '<ul>
                        <li id="loggedAs">logged in as '.htmlspecialchars($_SESSION['user_name']).'</li>
                        <a href="logout.php"><li>logout</li></a>
                       </ul>';
            }else{
                echo '<h1>Marienthal workshops</h1>
                       <ul>
                           <a href="login.php"><li>sign in</li></a>
                           <a href="register.php"><li>sign up</li></a>
                       </ul>';
            }
            ?>
        </nav>
        <main>
