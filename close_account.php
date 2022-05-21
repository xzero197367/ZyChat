<?php
include "header.php";

if (isset($_POST['cancel'])){
    header("Location: settings.php");
}

if (isset($_POST['close_account'])){
    $close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
    session_destroy();
    header("Location: register.php");
}
?>


<div class="main_column column settings" >
    <h4 class="settings_title">Close Account</h4>

    Are you sure you want to close your account?<br><br>

    Closing your account will hide your profile and all your activity from other users.<br><br>

    You can re-open your account at any time by simply logging in.<br><br>

    <form action="close_account.php" method="post">
        <input style="background-color: red;" type="submit" name="close_account" class="close_account settings_btn" value="Yes! Close it">
        <input type="submit" name="cancel" class="update_details settings_btn" value="Cancel">
    </form>

</div>
