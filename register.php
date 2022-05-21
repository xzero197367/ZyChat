<?php

require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';


?>

<html>

<head>
    <title>Welcome to ZyChat</title>
    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/register.js"></script>
</head>

<body>
<?php
    if (isset($_POST['register_button'])){
        echo '
        <script>
            $(document).ready(function (){
               alert("You had successfully registrations. You can login in now!");
               $("#first").hide();
               $("#second").show();
                $("#second").slideUp("slow", function (){
                    $("#first").slideDown("slow");
                });
            });
        </script>
        ';
    }

?>


    <div class="wrapper">
        <div class="login_box">
<!--            header text-->
            <div class="login_header">
                <h1>ZyChat</h1>
                Login or sign up below
            </div>
            <div id="first">
                <!--    Login form-->
                <form action="register.php" method="post">
                    <input type="email" name="log_email" placeholder="Email Address" value="
        <?php
                    if(isset($_SESSION['email'])){
                        echo $_SESSION['email'];
                    }
                    ?>
        " required>
                    <br>
                    <input type="password" name="log_password" placeholder="Password">
                    <br>
                    <input type="submit" name="login_button" value="Login">
                    <?php
                    if(in_array("Email or password was incorrect", $error_array)){
                        echo "<br>Email or password was incorrect";
                    }
                    ?>
                    <br>
                    <a href="#" id="signup" class="signup">Create New Account? Register Here</a>
                </form>
            </div>

            <div id="second">
                <!--    Register form-->
                <form action="register.php" method="post">
                    <input type="text" name="reg_fname" placeholder="First Name" required value="
        <?php
                    if(isset($_SESSION['reg_fname'])){
                        echo $_SESSION['reg_fname'];
                    }
                    ?>">
                    <?php
                    if (in_array('Your first name must be between 2 and 25 characters', $error_array)){
                        echo "<br>Your first name must be between 2 and 25 characters";
                    }
                    ?>
                    <br>

                    <input type="text" name="reg_lname" placeholder="Last Name" required value="
        <?php
                    if(isset($_SESSION['reg_lname'])){
                        echo $_SESSION['reg_lname'];
                    }
                    ?>">
                    <?php
                    if (in_array('Your last name must be between 2 and 25 characters', $error_array)){
                        echo "<br>Your last name must be between 2 and 25 characters";
                    }
                    ?>
                    <br>

                    <input type="email" name="reg_email" placeholder="Email" required value="
        <?php
                    if(isset($_SESSION['reg_email'])){
                        echo $_SESSION['reg_email'];
                    }
                    ?>">
                    <?php
                    if (in_array('Email already in use', $error_array)){
                        echo "<br>Email already in use";
                    }else if (in_array('Invalid email format', $error_array)){
                        echo "<br>Invalid email format";
                    }else if (in_array("Emails don't match", $error_array)){
                        echo "<br>Emails don't match";
                    }
                    ?>
                    <br>

                    <input type="email" name="reg_email2" placeholder="Confirm Email" required value="
        <?php
                    if(isset($_SESSION['reg_email2'])){
                        echo $_SESSION['reg_email2'];
                    }
                    ?>">
                    <br>

                    <input type="password" name="reg_password" placeholder="Password" required>
                    <?php
                    if (in_array("Your passwords do not match", $error_array)){
                        echo "<br>Your passwords do not match";
                    }else if(in_array("Your password must be more than 5 characters", $error_array)){
                        echo "<br>Your password must be more than 5 characters";
                    }
                    ?>
                    <br>

                    <input type="password" name="reg_password2" placeholder="Confirm Password" required>
                    <br>

                    <input type="submit" name="register_button" placeholder="Register" required>
                    <br>

                    <?php
                    if (in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span>", $error_array)){
                        echo "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>";
                    }
                    ?>
                    <a href="#" id="signin" class="signin">Already Have an Account? Sign in Here</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
