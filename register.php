<?php
require_once 'inc/user.php';
$pageTitle = 'registration page';

if(isset($_SESSION['user_id'])){
    header('Location: index.php');
    exit();
}else{
    $errors=[];
    if(!empty($_POST)){

        $name=trim(@$_POST['name']);
        if(empty($name)){
            $errors['name']='Name is required';
        }

        $mail=trim(@$_POST['mail']);
        if(!filter_var($mail,FILTER_VALIDATE_EMAIL)){
            $errors['mail']='E-mail is empty or invalid';
        }else{
            $mailCheck=$db->prepare('SELECT * FROM mt_user WHERE email=:mail LIMIT 1;');
            $mailCheck->execute([':mail'=>$mail]);
            if($mailCheck->rowCount()!=0){
                $errors['mail']='An account associated with this e-mail adress already exists.';
            }
        }

        $country=trim(@$_POST['country']);
        if(!in_array($country, array("BIH","CZE","DEU","ITA","LVA","LTU","POL","ESP","SWE"))){
            $errors['country']='You must select country from these options.';
        }

        if(empty($_POST['passwd'])||strlen($_POST['passwd'])<6){
            $errors['passwd']='A password with minimal length of 6 symbols is required.';
        }
        if($_POST['passwd']!=$_POST['passwd2']){
            $errors['passwd2']='Password fields cannot differ.';
        }

        if(empty($errors)){
            $passwd=password_hash($_POST['passwd'],PASSWORD_DEFAULT);
            $registration=$db->prepare('INSERT INTO mt_user (name, email, password, nation) 
                                                               VALUES (:name, :mail, :passwd, :country);');
            $registration->execute([
                ':name'=>$name,
                ':mail'=>$mail,
                ':passwd'=>$passwd,
                ':country'=>$country
            ]);

            $_SESSION['user_id']=$db->lastInsertId();
            $_SESSION['user_name']=$name;
            $_SESSION['user_role']='student';

            header('Location: index.php');
            exit();
        }
    }
}

//TODO: otestovat zadani chyby do formulare

include 'inc/header.php';
?>
<section id="registration">
    <h2>Sign up</h2>
    <div>
        Already have an account? <a href="login.php">sign in</a>
    </div>
    <div>
        <a href="">Sign up with facebook instead</a>
    </div>
    <form method="post">
        <div>
            <label for="name">Name and surname</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars(@$name); ?>">
        </div>
        <?php
            echo(empty($errors['name']))?'':'<div class="formError">'.$errors['name'].'</div>';
        ?>
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
        <div>
            <label for="mail">e-mail</label>
            <input type="email" name="mail" required value="<?php echo htmlspecialchars(@$mail); ?>">
        </div>
        <?php
        echo(empty($errors['mail']))?'':'<div class="formError">'.$errors['mail'].'</div>';
        ?>
        <div>
            <label for="passwd">Password (minimal lenght 6)</label>
            <input type="password" name="passwd" required>
        </div>
        <?php
        echo(empty($errors['passwd']))?'':'<div class="formError">'.$errors['passwd'].'</div>';
        ?>
        <div>
            <label for="passwd2">Retype your password</label>
            <input type="password" name="passwd2" required>
        </div>
        <?php
        echo(empty($errors['passwd2']))?'':'<div class="formError">'.$errors['passwd2'].'</div>';
        ?>
        <input type="submit" value="Register">
        <a href="index.php">Cancel</a>
    </form>
</section>


<?php
include 'inc/footer.php';
