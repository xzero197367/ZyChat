<?php
include "header.php";
include "includes/form_handlers/settings_handler.php";
?>

<div class="column title settings">

    <h4 class="settings_title">Account Settings</h4>
    <?php
    echo "<img src='".$user['profile_pic']."' class='small_profile_pics'>";
    ?>
    <br>
    <a href="upload.php" style="text-decoration: none;" class="settings_btn">Upload new profile picture</a>
    <hr>



    <?php
        $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
        $row = mysqli_fetch_array($user_data_query);

        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
    ?>
    <form action="settings.php" method="post" class="settings_form">
        <h4 class="settings_title">Modify the values and click 'Update Details'</h4>
        First Name: <input class="settings_input" type="text" name="first_name" value="<?php echo $first_name;?>">
        <br>
        Last Name: <input class="settings_input" type="text" name="last_name" value="<?php echo $last_name;?>">
        <br>
        Email: <input class="settings_input" style="margin-left: 35px;" type="email" name="email" value="<?php echo $email;?>">
        <br>
        <?php echo $message;?>
        <input type="submit" class="settings_btn update_details_btn" name="update_details" id="save_details" value="Update Details">
    </form>

    <hr>

    <form action="settings.php" method="post" class="settings_form">
        <h4 class="settings_title">Change Password</h4>
        Old Password: <input class="settings_input" style="margin-left: 55px;" type="password" name="old_password" >
        <br>
        New Password: <input class="settings_input" style="margin-left: 50px;" type="password" name="new_password_1" >
        <br>
        New Password Again: <input class="settings_input"  type="password" name="new_password_2">
        <br>
        <?php
            echo $password_message;
        ?>
        <input type="submit" name="update_password" class="settings_btn save_new_password" value="Update Password">
    </form>
    <hr>

    <form action="settings.php" method="post" class="settings_form">
        <h4 class="settings_title">Close Account</h4>
        <input style="background-color: red;" type="submit" name="close_account" class="settings_btn close_account_btn" value="Close Account">
    </form>

</div>
