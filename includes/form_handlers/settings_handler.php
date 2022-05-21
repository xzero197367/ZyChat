<?php
if (isset($_POST['update_details'])){
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];

    // prepare first name
    $firstName = strip_tags($firstName);
    $firstName = str_replace(' ', '', $firstName);
    $firstName = ucfirst(strtolower($firstName));

    // prepare last name
    $lastName = strip_tags($lastName);
    $lastName = str_replace(' ', '', $lastName);
    $lastName = ucfirst(strtolower($lastName));

    // prepare email
    $email = strip_tags($email);
    $email = str_replace(' ', '', $email);


    $email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    $row = mysqli_fetch_array($email_check);
    $matched_user = $row['username'];

    if(!$firstName != '' || !$lastName != '' || !$email != ''){
        $message = "empty fields not allowed<br>";
        return;
    }

    if ($matched_user == "" || $matched_user == $userLoggedIn){

        $message = "Details updated!<br><br>";

        $query = mysqli_query($con, "UPDATE users SET first_name='$firstName', last_name='$lastName', email='$email' WHERE username='$userLoggedIn'");
    }else{
        $message = "That email is already in user! <br><br>";
    }
}else{
    $message = "";
}


/******************   change password   ************/
$password_message = "";
if (isset($_POST['update_password'])){
    $old_password = strip_tags($_POST['old_password']);
    $new_password_1 = strip_tags($_POST['new_password_1']);
    $new_password_2 = strip_tags($_POST['new_password_2']);

    $password_query = mysqli_query($con, "SELECT password FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($password_query);
    $db_password = $row['password'];

    if (md5($old_password) == $db_password){
        if ($new_password_1 == $new_password_2){

            if (strlen($new_password_1) <= 4){
                $password_message = "Sorry, your password must be greater than 4 characters <br><br>";
            }else{
                $new_pass_md5 = md5($new_password_1);
                $password_query = mysqli_query($con, "UPDATE users SET password='$new_pass_md5' WHERE username='$userLoggedIn'");
                $password_message = "Password has been changed! <br><br>";
            }

        }else{
            $password_message = "Password does not match!<br><br>";
        }
    }else{
        $password_message = "The old password is incorrect <br><br>";
    }
}else{
    $password_message = '';
}


/******************************   close account   ************************************/
if (isset($_POST['close_account'])){
    header("Location: close_account.php");
}








