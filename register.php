<?php
require_once 'inc/db.php';
include 'inc/header.php';
$pageTitle = 'registration page';
?>
<div id="registration">
    <form method="post">
        <div>
            <label for="name">Name</label>
            <input type="text" name="name" required>
        </div>
        <div>
            <label for="country">Choose your country:</label>
            <select id="country" required>
                <option value="BIH">Bosnia and Herzegovina</option>
                <option value="CZE">Czech Republic</option>
                <option value="DEU">Germany</option>
                <option value="ITA">Italy</option>
                <option value="LVA">Latvia</option>
                <option value="LTU">Lithuania</option>
                <option value="POL">Poland</option>
                <option value="ESP">Spain</option>
                <option value="SWE">Sweeden</option>
            </select>
        </div>
        <div>
            <label for="mail">e-mail</label>
            <input type="email" name="mail" required>
        </div>
        <div>
            <label for="passwd">Password</label>
            <input type="password" required>
        </div>
        <div>
            <label for="passwd2">Retype your password</label>
            <input type="password" required>
        </div>

    </form>
</div>


<?php
include 'inc/footer.php';
