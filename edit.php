<?php
require_once 'inc/user.php';
$pageTitle = 'edit workshop page';


if(empty($_GET['id']) || (!isset($_SESSION['user_id'])) || ($_SESSION['user_role']=="student")){
    header('Location: index.php');
    exit();
}else{
    $Query = $db->prepare('SELECT * FROM mt_workshop WHERE id=:id LIMIT 1;');
    $Query->execute([':id'=>$_GET['id']]);
    $workshop = $Query->fetch(PDO::FETCH_ASSOC);
}

if($_SESSION['user_id'] != $workshop['leader_id']){
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
        $updateQuery = $db->prepare('UPDATE mt_workshop SET name=:name, description=:description WHERE id=:id LIMIT 1;');
        $updateQuery->execute([':name'=>$name,
                               ':description'=>$description,
                               ':id'=>$workshop['id']]);

        header('Location: workshop.php?id='.$workshop['id'].'');          //vratit na workshop
        exit();
    }
}
include 'inc/header.php';
?>
    <h2>Edit workshop informations</h2>
    <form method="post">
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($workshop['name']); ?>">
        </div>
        <?php
        echo(empty($errors['name']))?'':'<div class="formError">'.$errors['name'].'</div>';
        ?>
        <div>
            <label for="description">Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($workshop['description']); ?></textarea>
        </div>
        <?php
        echo(empty($errors['description']))?'':'<div class="formError">'.$errors['description'].'</div>';
        ?>
        <input type="submit" value="Change">
        <a href="index.php">Cancel</a>
    </form>
<?php
include 'inc/footer.php';