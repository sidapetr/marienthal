<?php
require_once 'inc/user.php';
$pageTitle = 'edit workshop role page';


if(empty($_GET['id']) || (!isset($_SESSION['user_id'])) || ($_SESSION['user_role']=="student")){
    header('Location: index.php');
    exit();
}else{
    $roleQuery = $db->prepare('SELECT * FROM mt_role WHERE id=:id LIMIT 1;');
    $roleQuery->execute([':id'=>$_GET['id']]);
    $role = $roleQuery->fetch(PDO::FETCH_ASSOC);

    $workshopQuery = $db->prepare('SELECT * FROM mt_workshop WHERE id=:id LIMIT 1;');
    $workshopQuery->execute([':id'=>$role['workshop_id']]);
    $workshop = $workshopQuery->fetch(PDO::FETCH_ASSOC);
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
    $note = trim(@$_POST['note']);
    $capacity = trim(@$_POST['capacity']);
    if(empty($capacity)){
        $errors['capacity'] = "This field is required";
    }
    if(empty($errors)){
        $createQuery = $db->prepare(
            'UPDATE mt_role SET name=:name, description=:description, capacity=:capacity, note=:note WHERE id=:id;');
        $createQuery->execute([':name'=>$name,
                               ':description'=>$description,
                               ':capacity'=>$capacity,
                               ':note'=>$note,
                               ':id'=>$role['id']]);
        header('Location: workshop.php?id='.$workshop['id'].'');          //vratit na workshop
        exit();
    }
}
include 'inc/header.php';
?>
    <h3>Edit role</h3>
    <form method="post">
        <div>
            <label for="capacity">Capacity</label>
            <input type="number" name="capacity" required value="<?php echo htmlspecialchars($role['capacity']);?>">
            <div class="formError"><?php echo @$errors['capacity'];?></div>
        </div>
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($role['name']);?>">
            <div class="formError"><?php echo @$errors['name'];?></div>
        </div>
        <div>
            <label for="description">Description</label>
            <textarea name="description" required><?php echo htmlspecialchars($role['description']);?></textarea>
            <div class="formError"><?php echo @$errors['description'];?></div>
        </div>
        <div>
            <label for="note">Note</label>
            <textarea name="note"><?php echo htmlspecialchars(@$role['note']);?></textarea>
        </div>
        <input type="submit" value="Save">
    </form>
<?php
include 'inc/footer.php';