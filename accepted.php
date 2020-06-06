<?php
require_once 'inc/user.php';
$pageTitle = 'Accepted students list page';

if(!isset($_SESSION['user_id'])){
    header('Location:auth/login.php');
    exit();
}

include 'inc/header.php';

echo '<h1>Marienthal Workshops</h1>';

include 'inc/footer.php';
