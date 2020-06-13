<?php
require_once 'inc/user.php';
$pageTitle = 'Workshop assign page';


if(!isset($_SESSION['user_id']) || ($_SESSION['user_role']!="student")){
    header('Location: index.php');
    exit();
}
$error = null;

if(!empty($_POST['answer'])){
    switch ($_GET['no']){
        case 1:
            $userQuery = $db->prepare('UPDATE mt_user SET w_prefered=:role, a_prefered=:answer WHERE id=:id;');
            break;
        case 2:
            $userQuery = $db->prepare('UPDATE mt_user SET w_alternative=:role, a_alternative=:answer WHERE id=:id;');
            break;
        default:
            header('Location: index.php');
            exit();
    }
    $userQuery->execute([':role'=>$_GET['id'], ':id'=>$_SESSION['user_id'], ':answer'=>$_POST['answer']]);
    header('Location: index.php');
    exit();
}

if(!empty($_GET['id'] && !empty($_GET['no']))){

    $userQuery=$db->prepare('SELECT * FROM mt_user WHERE id=:id LIMIT 1');
    $userQuery->execute([':id'=>$_SESSION['user_id']]);
    $user=$userQuery->fetch(PDO::FETCH_ASSOC);

    switch ($_GET['no']){
        case 1:
            if($user['w_alternative'] == $_GET['id']){      //overeni rozdilnosti prefered a alternative
                $error = 'Prefered and alternative workshops cannot be same.';
            }else if($user['w_prefered'] == $_GET['id']){
            $error = 'Workshop already assigned.';
            }
            break;
        case 2:
            if($user['w_prefered'] == $_GET['id']){      //overeni rozdilnosti prefered a alternative
                $error = 'Prefered and alternative workshops cannot be same.';
            }else if($user['w_alternative'] == $_GET['id']){
                $error = 'Workshop already assigned.';
            }
            break;
        default:
            header('Location: index.php');
            exit();
    }
    if(is_null($error)){
        $roleQuery = $db->prepare('SELECT * FROM mt_role WHERE id=:id LIMIT 1;');
        $roleQuery->execute([':id'=>$_GET['id']]);
        $role = $roleQuery->fetch(PDO::FETCH_ASSOC);

        if(empty($role['note'])){                   //zapise rovnou a neukazuje pole odpovedi
            switch ($_GET['no']){
                case 1:
                    $userQuery = $db->prepare('UPDATE mt_user SET w_prefered=:role, a_prefered=:answer WHERE id=:id;');
                    break;
                case 2:
                    $userQuery = $db->prepare('UPDATE mt_user SET w_alternative=:role, a_alternative=:answer WHERE id=:id;');
                    break;
                default:
                    header('Location: index.php');
                    exit();
            }
            $userQuery->execute([':role'=>$_GET['id'], ':id'=>$_SESSION['user_id'], ':answer'=>null]);
            header('Location: index.php');
            exit();
        }
    }
} else {
    header('Location: index.php');
    exit();
}

include 'inc/header.php';
?>
    <h2>Workshop assignment</h2>

<?php
    if($error){
        echo '<p class="formError">'.$error.'</p>
        <a href="javascript:history.back(1);" class="button">back</a>';
    } else {
        echo'<p>Please fill in a brief answer to following question.</p>
            <p>'.htmlspecialchars(@$role['note']).'</p>
            <form method="post">
                <div>
                    <label for="answer"></label>
                    <textarea name="answer"></textarea>
                </div>
                <input type="submit" value="Assign">
            </form>';
    }

include 'inc/footer.php';