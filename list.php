<?php
require_once 'inc/user.php';
$pageTitle = 'Student lists page';

if(!isset($_SESSION['user_id'])){
    header('Location:auth/login.php');
    exit();
}

if(empty($_GET['id']) || ($_SESSION['user_role'] == "student")){
    header('Location: index.php');
    exit();
}

if(!empty($_POST)){
    if(is_numeric($_POST['student']) && is_numeric($_POST['role'])){

        $controlQuery = $db->prepare('SELECT * FROM mt_user WHERE id=:id AND role="student" AND w_accepted is null LIMIT 1;');
        $controlQuery->execute([':id'=>$_POST['student']]);
        $control = $controlQuery->fetch(PDO::FETCH_ASSOC);

        if(!empty($control)){                   //student nema zapsany workshop

            $updateQuery = $db->prepare('UPDATE mt_user SET w_accepted=:role WHERE id=:id;');
            $updateQuery ->execute([':id'=>$_POST['student'], ':role'=>$_POST['role']]);
            var_dump($_POST);
        }
    }
}


include 'inc/header.php';
echo '<h1>My workshop</h1>';           //TODO: nazev workshopu

$roleQuery = $db->prepare('SELECT * FROM mt_role WHERE workshop_id=:workshop;');
$roleQuery->execute([':workshop'=>$_GET['id']]);
$roles = $roleQuery->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Prefered roles</h2>';
foreach ($roles as $role) {

    $studentQuery = $db->prepare('SELECT st.id, st.name, st.nation, st.w_accepted
                                            FROM mt_user as st join mt_role as rl on (st.w_prefered=rl.id) 
                                            WHERE rl.id=:role AND st.role="student";');
    $studentQuery->execute([':role'=>$role['id']]);
    $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);
    if(empty($students)){
        echo '<h3>'.htmlspecialchars($role['name']).' ('.htmlspecialchars($role['capacity']).')'.'</h3>
              <p>There are no students assigned for this workshop role yet.</p>';
    }else{
        echo '<h3>'.htmlspecialchars($role['name']).' ('.htmlspecialchars($role['capacity']).')'.'</h3>
        <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Nation</th>
                <th>Actions</th>
            </tr>
        </thead><tbody>';
        foreach ($students as $student){
            if($student['w_accepted']==null || $student['w_accepted']==$role['id']) {
                echo '<tr>
                    <td>'.htmlspecialchars($student['name']).'</td>
                    <td>'.htmlspecialchars($student['nation']).'</td>';
                if($student['w_accepted']==$role['id']){
                    echo'<td>☑</td>';                   //je zapsan a potvrzen na tuto roli
                }else {
                    echo '<td><form method="post">
                                <input type="number" name="student" value="'.$student['id'].'" hidden>
                                <input type="number" name="role" value="'.$role['id'].'" hidden>
                                <input type="submit" value="accept">
                              </form></td>';             //je zapsan a zatim nepotvrzen
                }
                echo'</tr>';
            }
        }
        echo '</tbody></table>';
    }
}

echo '<br/><hr/><h2>Alternative roles</h2>';
foreach ($roles as $role) {

    $studentQuery = $db->prepare('SELECT st.id, st.name, st.nation, st.w_accepted
                                            FROM mt_user as st join mt_role as rl on (st.w_alternative=rl.id) 
                                            WHERE rl.id=:role AND st.role="student";');
    $studentQuery->execute([':role'=>$role['id']]);
    $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);

    if(empty($students)){
        echo '<h3>'.htmlspecialchars($role['name']).' ('.htmlspecialchars($role['capacity']).')'.'</h3>
              <p>There are no students assigned for this workshop role yet.</p>';
    }else{
        echo '<h3>'.htmlspecialchars($role['name']).' ('.htmlspecialchars($role['capacity']).')'.'</h3>

        <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Nation</th>
                <th>Actions</th>
            </tr>
        </thead><tbody>';
        foreach ($students as $student){
            if($student['w_accepted']==null || $student['w_accepted']==$role['id']) {
                echo '<tr>
                    <td>'.htmlspecialchars($student['name']).'</td>
                    <td>'.htmlspecialchars($student['nation']).'</td>';
                if($student['w_accepted']==$role['id']){
                    echo'<td>☑</td>';                   //je zapsan a potvrzen na tuto roli
                }else {
                    echo '<td><form method="post">
                                <input type="number" name="student" value="'.$student['id'].'" hidden>
                                <input type="number" name="role" value="'.$role['id'].'" hidden>
                                <input type="submit" value="accept">
                              </form></td>';             //je zapsan a zatim nepotvrzen
                }
                echo'</tr>';
            }
        }
        echo '</tbody></table>';
    }
}


include 'inc/footer.php';
