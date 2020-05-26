<?php
require_once 'inc/user.php';
$pageTitle = 'Country selection page';

if(isset($_SESSION['user_nation'])){
    header('Location: index.php');
    exit();
}

$userQuery = $db->prepare('SELECT * FROM mt_user WHERE id=:id LIMIT 1;');
$userQuery->execute([':id'=>$_SESSION['user_id']]);
if($userQuery->rowCount()!=1) {                         //prihlaseny uzivatel neexistuje, odhlaseni
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_role']);
    header('Location: index.php');
    exit();
}else{
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);
    if($user['nation']!="FB"){
        $_SESSION['user_nation']=$user['nation'];
    }
}

$errors=[];
if(!empty($_POST)){

    $country=trim(@$_POST['country']);
    if(!in_array($country, array("BIH","CZE","DEU","ITA","LVA","LTU","POL","ESP","SWE"))){
        $errors['country']='You must select country from these options.';
    }

    if(empty($errors)){
        $registration=$db->prepare('UPDATE mt_user SET nation=:nation WHERE id=:id LIMIT 1;');
        $registration->execute([':nation'=>$country, ':id'=>$user['id']]);

        $_SESSION['user_nation']=$country;

        header('Location: index.php');
        exit();
    }
}


include 'inc/header.php';
?>
<h2>Select your country to finnish the registration</h2>
    <form method="post">
        <div>
            <label for="country">Choose your country:</label>
            <select name="country" required>
                <option value="BIH" <?php echo (@$country=='BIH')?'selected="selected"':'' ?>>Bosnia and Herzegovina</option>
                <option value="CZE" <?php echo (@$country=='CZE')?'selected="selected"':'' ?>>Czech Republic</option>
                <option value="DEU" <?php echo (@$country=='DEU')?'selected="selected"':'' ?>>Germany</option>
                <option value="ITA" <?php echo (@$country=='ITA')?'selected="selected"':'' ?>>Italy</option>
                <option value="LVA" <?php echo (@$country=='LVA')?'selected="selected"':'' ?>>Latvia</option>
                <option value="LTU" <?php echo (@$country=='LTU')?'selected="selected"':'' ?>>Lithuania</option>
                <option value="POL" <?php echo (@$country=='POL')?'selected="selected"':'' ?>>Poland</option>
                <option value="ESP" <?php echo (@$country=='ESP')?'selected="selected"':'' ?>>Spain</option>
                <option value="SWE" <?php echo (@$country=='SWE')?'selected="selected"':'' ?>>Sweeden</option>
            </select>
        </div>
        <?php
        echo(empty($errors['country']))?'':'<div class="formError">'.$errors['country'].'</div>';
        ?>
        <input type="submit" value="Register">
    </form>
<?php
include 'inc/footer.php';