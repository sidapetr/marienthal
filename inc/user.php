<?php
session_start();

require_once 'db.php';
require_once __DIR__.'../vendor/autoload.php';

if(!empty($_SESSION['user_id'])){
    $kontrolaPrihlaseni=$db->prepare('SELECT id FROM user WHERE id=:id LIMIT=1;');
    $kontrolaPrihlaseni->execute([':id' => $_SESSION['user_ID']]);
    if($kontrolaPrihlaseni->rowCount()!=1){
        unset($_SESSION['user_ID']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        //TODO: zkontrolovat a případně doplnit unset na všechny další hodnoty $_SESSION

        header('Location: index.php');
        exit();
    }
}