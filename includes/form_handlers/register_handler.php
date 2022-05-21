<?php
// declaring variables
$fname = "";
$lname = "";
$em = "";
$em2 = "";
$password = "";
$password2 = "";
$date = "";
$error_array = array();

if(isset($_POST['register_button'])){
    // register form values

    // first name
    $fname = strip_tags($_POST['reg_fname']); // remove html tags
    $fname = str_replace(' ', '', $fname); // remove spaces
    $fname = ucfirst(strtolower($fname)); // uppercase first letter
    $_SESSION['reg_fname'] = $fname;

    // last name
    $lname = strip_tags($_POST['reg_lname']); // remove html tags
    $lname = str_replace(' ', '', $lname); // remove spaces
    $lname = ucfirst(strtolower($lname)); // uppercase first letterd
    $_SESSION['reg_lname'] = $lname;

    // email
    $em = strip_tags($_POST['reg_email']); // remove html tags
    $em = str_replace(' ', '', $em); // remove spaces
    //$em = ucfirst(strtolower($em)); // uppercase first letterd
    $_SESSION['reg_email'] = $em;

    // email 2
    $em2 = strip_tags($_POST['reg_email2']); // remove html tags
    $em2 = str_replace(' ', '', $em2); // remove spaces
    //$em2 = ucfirst(strtolower($em2)); // uppercase first letterd
    $_SESSION['reg_email2'] = $em2;

    // password
    $password = strip_tags($_POST['reg_password']); // remove html tags

    // password 2
    $password2 = strip_tags($_POST['reg_password2']); // remove html tags

    // date
    $date = date("Y-m-d"); // current date

    // check email validate
    if($em == $em2){

        if(filter_var($em, FILTER_VALIDATE_EMAIL)){
            $em = filter_var($em, FILTER_VALIDATE_EMAIL);

            //check if email already exists
            $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");

            // count the number of rows returned
            $num_rows = mysqli_num_rows($e_check);
            if($num_rows > 0){
                array_push($error_array,"Email already in use");
            }
        }else {
            array_push($error_array, "Invalid email format");
        }

    }else{
        array_push($error_array, "Emails don't match");
    }


    // check first name
    if(strlen($fname) > 25 || strlen($fname) < 2){
        array_push($error_array, "Your first name must be between 2 and 25 characters");
    }

    // check last name
    if(strlen($lname) > 25 || strlen($lname) < 2){
        array_push($error_array, "Your last name must be between 2 and 25 characters");
    }

    // check password
    if($password != $password2){
        array_push($error_array, "Your passwords do not match");
    }else{
//        if(preg_match('/[^A-Za-z0-9]/',$password)){
//
//        }
    }

    if(strlen($password) < 5){
        array_push($error_array, "Your password must be more than 5 charachters");
    }

    // send info to database
    if(empty($error_array)){
        $password = md5($password); // Encrypt password before sending to database

        // Generate username by concatenating first name and last name
        $username = strtolower($fname."_".$lname);
        $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
        $i = 0;

        // if username exists add number to username
        $usernameNew = '';
        if (mysqli_num_rows($check_username_query) != 0) {
            while (mysqli_num_rows($check_username_query) != 0) {
                $i++;
                $usernameNew = $username . "_" . $i;
                $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$usernameNew'");
            }
        }else{
            $usernameNew = $username;
        }

        // choose profile picture for the account
        $rand = rand(1, 16);
        for ($i=1;$i<17;$i++){
            if($rand == $i){
                $profile_pic = "assets/images/profile_pics/defaults/$i.png";
            }
        }

        // insert data to the database
        $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$usernameNew', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");

        array_push($error_array, "<span style='color: #14C800;'>You're all set! Go ahead and login!</span>");

        // clear session variables
        $_SESSION['reg_fname'] = "";
        $_SESSION['reg_lname'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_email2'] = "";
    }
}
