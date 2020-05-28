<?php
require_once 'inc/user.php';
$pageTitle = 'workshop details page';
include 'inc/header.php';
if(!empty($_GET['id'])){
    $workshopQuery = $db->prepare('SELECT * FROM mt_workshop WHERE id=:workshop;');
    $workshopQuery->execute([':workshop'=>$_GET['id']]);
    $workshop = $workshopQuery->fetch(PDO::FETCH_ASSOC);
    echo '<h2>'.htmlspecialchars($workshop['name']).'</h2>';

    if($workshop['leader_id']==$_SESSION['user_id']){
        echo '<a href="edit.php?id='.$workshop['id'].'">Edit</a>';
    }
    echo '<p class="description">'.htmlspecialchars($workshop['description']).'</p>';

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
                'INSERT INTO mt_role (name, workshop_id, description, capacity, note) 
                                        VALUES(:name, :workshop, :description, :capacity, :note)');
            $createQuery->execute([':name'=>$name,
                                   ':description'=>$description,
                                   ':workshop'=>$workshop['id'],
                                   ':capacity'=>$capacity,
                                   ':note'=>$note]);
            unset($capacity);
            unset($name);
            unset($description);
            unset($note);
        }
    }

    if(isset($_SESSION['user_id'])&&($workshop['leader_id']==$_SESSION['user_id'])){
        echo '
        <h3>Add new role</h3>
        <form method="post" id="addRole">
            <div>
                <label for="capacity">Capacity</label>
                <input type="number" name="capacity" required value="'.htmlspecialchars(@$capacity).'">
                <div class="formError">'.@$errors['capacity'].'</div>
            </div>
            <div>
                <label for="name">Name</label>
                <input type="text" name="name" required value="'.htmlspecialchars(@$name).'">
                <div class="formError">'.@$errors['name'].'</div>
            </div>
            <div>
                <label for="description">Description</label>
                <textarea name="description" required>'.htmlspecialchars(@$description).'</textarea>
                <div class="formError">'.@$errors['description'].'</div>
            </div>
            <div>
                <label for="note">Note</label>
                <textarea name="note">'.htmlspecialchars(@$note).'</textarea>
            </div>
            <input type="submit" value="Add">
        </form>
        ';
    }

    $roleQuery = $db->prepare('SELECT * FROM mt_role WHERE workshop_id=:workshop;');
    $roleQuery->execute([':workshop'=>$_GET['id']]);
    if($roleQuery->rowCount()>0){
        $roles = $roleQuery->fetchAll(PDO::FETCH_ASSOC);
        echo '<h3>Current roles</h3>
              <table class="roles">
                <tr>
                    <th>Workshop role</th>
                    <th>Description</th>
                    <th>Capacity</th>
                    <th>Note</th>
                </tr>';
        foreach ($roles as $role){
            echo '<tr>
                    <td>'.htmlspecialchars($role['name']).'</td>
                    <td>'.htmlspecialchars($role['description']).'</td>
                    <td>'.htmlspecialchars($role['capacity']).'</td>
                    <td>'.htmlspecialchars($role['note']).'</td>
                  </tr>';
        }
        echo '</table>';
    } else {
        echo '<h3>Current roles</h3>
              There are no roles for this workshop yet.';
    }
}

//TODO: stránka se zobrazením detailu worshopu včetně výpisu rolí
?>

<?php
include 'inc/footer.php';