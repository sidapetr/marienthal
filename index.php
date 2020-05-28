<?php
require_once 'inc/user.php';
$pageTitle = 'main page';

if(!isset($_SESSION['user_id'])){
    header('Location:login.php');
    exit();
}elseif (!isset($_SESSION['user_nation'])){
    header('Location:setNation.php');
    exit();
}else{
    $workshopQuery=$db->prepare('SELECT * FROM mt_workshop WHERE seminar_year=:year;');
    $workshopQuery->execute([':year'=>date("Y")]);
    $workshops=$workshopQuery->fetchAll(PDO::FETCH_ASSOC);

    include 'inc/header.php';

    echo '<h1>Marienthal Workshops</h1>';

    foreach ($workshops as $workshop){
        echo '<a href="workshop.php?id='.$workshop['id'].'"><div class="icon">'.$workshop['name'].'</div></a>';
    }
    if($_SESSION['user_role']!="student"){
        echo '<a href="new.php"><div class="icon newWorkshop">Add new</div></a>';
    }
}

include 'inc/footer.php';
