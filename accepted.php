<?php
require_once 'inc/user.php';
$pageTitle = 'Accepted students list page';

if(!isset($_SESSION['user_id']) || ($_SESSION['user_role'] == "student")){
    header('Location:auth/login.php');
    exit();
}

include 'inc/header.php';

if (empty($_GET['id'])) {

    echo '<h1>Students without workshop</h1>';

    $studentQuery = $db->query('SELECT st.name, st.nation, w1.name as workshop1, rl1.name as role1, w2.name as workshop2, rl2.name as role2
                                          FROM mt_user as st 
                                          left join mt_role as rl1 on (st.w_prefered=rl1.id) 
                                          left join mt_workshop as w1 on (rl1.workshop_id=w1.id)
                                          left join mt_role as rl2 on (st.w_prefered=rl2.id)
                                          left join mt_workshop as w2 on (rl2.workshop_id=w2.id)
                                          WHERE st.w_accepted is null AND st.role="student";');
    $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);

    echo '<table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Nation</th>
                        <th>First choice</th>
                        <th>Second choice</th>
                    </tr>
                </thead><tbody>';
    foreach ($students as $student) {
        echo '<tr>
                    <td>' .htmlspecialchars($student['name'])  . '</td>
                    <td>' .htmlspecialchars($student['nation'])  . '</td>
                    <td>' .htmlspecialchars($student['workshop1']) .' - '.htmlspecialchars($student['role1']) . '</td>
                    <td>' .htmlspecialchars($student['workshop2']) .' - '.htmlspecialchars($student['role2']) . '</td>
                  </tr>';
    }
    echo '</tbody></table>';

} else {

    $roleQuery = $db->prepare('SELECT * FROM mt_role WHERE workshop_id=:workshop;');
    $roleQuery->execute([':workshop'=>$_GET['id']]);
    $roles = $roleQuery->fetchAll(PDO::FETCH_ASSOC);

    echo '<h1>Marienthal Workshops</h1>';

    foreach ($roles as $role) {

        $studentQuery = $db->prepare('SELECT st.name, st.nation FROM mt_user as st join mt_role as rl on (st.w_accepted=rl.id) 
                                            WHERE rl.id=:role AND st.role="student";');
        $studentQuery->execute([':role'=>$role['id']]);
        $students = $studentQuery->fetchAll(PDO::FETCH_ASSOC);
        if(empty($students)){
            echo '<h3>'.htmlspecialchars($role['name']).' ('.htmlspecialchars($role['capacity']).')'.'</h3>
              <p>There are no students accepted.</p>';
        }else{
            echo '<h3>'.htmlspecialchars($role['name']).' ('.htmlspecialchars($role['capacity']).')'.'</h3>
        <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Nation</th>
            </tr>
        </thead><tbody>';
            foreach ($students as $student){
                echo '<tr>
                    <td>'.htmlspecialchars($student['name']).'</td>
                    <td>'.htmlspecialchars($student['nation']).'</td>
                  </tr>';
            }
            echo '</tbody></table>';
        }
    }
}




include 'inc/footer.php';
