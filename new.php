<?php
require_once 'inc/user.php';
$pageTitle = 'new workshop page';

if(!isset($_SESSION['user_id'])||($_SESSION['user_role']=="student")){
    header('Location: index.php');
    exit();
}

if(!empty($_POST)){
    $errors = [];
    $name = trim(@$_POST['name']);
    if(empty($name)){
        $errors['name'] = "This field is required.";
    }
    $description = trim(@$_POST['description']);
    if(empty($description)){
        $errors['description'] = "This field is required";
    }
    if(empty($errors)){
        $createQuery = $db->prepare(
            'INSERT INTO mt_workshop (name, description, seminar_year, leader_id) VALUES (:name, :description, :year, :leader);');
        $createQuery->execute([':name'=>$name,
                               ':description'=>$description,
                               ':year'=>date('Y'),
                               ':leader'=>$_SESSION['user_id']]);

        header('Location: index.php');
        exit();
    }
}
include 'inc/header.php';
?>
<h2>Create new workshop</h2>
<form method="post">
    <div>
        <label for="name">Name</label>
        <input type="text" name="name" required value="<?php echo htmlspecialchars(@$name); ?>">
    </div>
    <?php
    echo(empty($errors['name']))?'':'<div class="formError">'.$errors['name'].'</div>';
    ?>
    <div>
        <label for="description">Description</label>
        <textarea name="description" required><?php echo htmlspecialchars(@$description); ?></textarea>
    </div>
    <?php
    echo(empty($errors['description']))?'':'<div class="formError">'.$errors['description'].'</div>';
    ?>
    <input type="submit" value="Create">
    <a href="index.php">Cancel</a>
</form>
<?php
include 'inc/footer.php';