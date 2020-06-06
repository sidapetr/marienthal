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

include 'inc/header.php';
echo '<h1>My workshop</h1>';           //TODO: nazev workshopu

$roleQuery = $db->prepare('SELECT * FROM mt_role WHERE workshop_id=:workshop;');
$roleQuery->execute([':workshop'=>$_GET['id']]);
$roles = $roleQuery->fetchAll(PDO::FETCH_ASSOC);

echo '<h2>Prefered roles</h2>';
foreach ($roles as $role) {

    $studentQuery = $db->prepare('SELECT st.name, st.nation FROM mt_user as st join mt_role as rl on (st.w_prefered=rl.id) 
                                            WHERE rl.id=:role AND st.role="student";');
    $studentQuery->execute([':role'=>$role['id']]);
    $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);
    if(empty($students)){
        echo '<h3>'.$role['name'].' ('.$role['capacity'].')'.'</h3>
              <p>There are no students assigned for this workshop role yet.</p>';
    }else{
        echo '<h3>'.$role['name'].' ('.$role['capacity'].')'.'</h3>
        <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Nation</th>
            </tr>
        </thead><tbody>';
        foreach ($students as $student){
            echo '<tr>
                    <td>'.$student['name'].'</td>
                    <td>'.$student['nation'].'</td>
                  </tr>';
        }
        echo '</tbody></table>';
    }
}

echo '<br/><hr/><h2>Alternative roles</h2>';
foreach ($roles as $role) {

    $studentQuery = $db->prepare('SELECT st.name, st.nation FROM mt_user as st join mt_role as rl on (st.w_alternative=rl.id) 
                                            WHERE rl.id=:role AND st.role="student";');
    $studentQuery->execute([':role'=>$role['id']]);
    $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);

    if(empty($students)){
        echo '<h3>'.$role['name'].' ('.$role['capacity'].')'.'</h3>
              <p>There are no students assigned for this workshop role yet.</p>';
    }else{
        echo '<h3>'.$role['name'].' ('.$role['capacity'].')'.'</h3>
        <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Nation</th>
            </tr>
        </thead><tbody>';
        foreach ($students as $student){
            echo '<tr>
                    <td>'.$student['name'].'</td>
                    <td>'.$student['nation'].'</td>
                  </tr>';
        }
        echo '</tbody></table>';
    }
}


include 'inc/footer.php';
