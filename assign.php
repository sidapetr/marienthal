<?php
require_once 'inc/user.php';
$pageTitle = 'Workshop assign page';

if(!isset($_SESSION['user_id']) || ($_SESSION['user_role']!="student")){
    header('Location: index.php');
    exit();
}

include 'inc/header.php';
?>
    <h2>Workshop assignment</h2>
    <form method="post">
        <div>
            <label for="answer"></label>
            <textarea name="answer"></textarea>
        </div>
        <input name="no" value="" hidden>
        <input type="submit" value="Assign">
    </form>
<?php
var_dump($_GET['id']);
var_dump($_GET['no']);
include 'inc/footer.php';