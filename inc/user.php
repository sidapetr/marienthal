<?php
session_start();

require_once 'db.php';
require_once __DIR__.'/../vendor/autoload.php';

if(!empty($_SESSION['user_id'])){
    $kontrolaPrihlaseni=$db->prepare('SELECT id FROM mt_user WHERE id=:id LIMIT 1;');
    $kontrolaPrihlaseni->execute([':id' => $_SESSION['user_id']]);
    if($kontrolaPrihlaseni->rowCount()!=1){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_nation']);

        header('Location: index.php');
        exit();
    }
}