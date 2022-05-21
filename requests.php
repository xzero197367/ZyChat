<?php

include("header.php");

?>

<div class="title settings column" id="main_column">
    <h4>Friend Requests</h4>

    <?php

    $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
    if(mysqli_num_rows($query) == 0){
        echo "You have no friend requests at this time";
    }else{

        while ($row = mysqli_fetch_array($query)){
            $user_from = $row['user_from'];

            $user_from_obj = new User($con, $user_from);
            $user_from_pic = $user_from_obj->getAll()['profile_pic'];
            ?>
            <div class="request_friend">
            <?php
            echo "<a href='$user_from'><img width='50' src='$user_from_pic' alt=''></a>";
            echo "<p><a href='$user_from'>".$user_from_obj->getFirstAndLastName()."</a> send you a friend request!</p>";

            $user_from_friend_array = $user_from_obj->getAll()['friend_array'];

            if (isset($_POST['accept_request'. $user_from])){
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

                $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "You are now friends";
                header("Location: requests.php");
            }

            if (isset($_POST['ignore_request'. $user_from])){
                $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "Request ignored!";
                header("Location: requests.php");
            }

            ?>

                <form action="requests.php" method="post">
                    <input type="submit" name="accept_request<?php echo $user_from;?>" id="accept_button" value="Accept">
                    <input type="submit" name="ignore_request<?php echo $user_from;?>" id="ignore_button" value="Ignore">
                </form>
            </div>
            <?php
        }
    }

    ?>
</div>

<style>
    .request_friend{
        margin-top: 20px;
        border-radius: 10px;
        padding: 5px;
        box-shadow: 0 5px 20px gray;
        background-color: transparent;
        font-family: "Bellota-BI", sans-serif;
        display: flex;
        align-items: center;
        justify-content: space-evenly;
        transition: box-shadow 1s;
    }
    .request_friend:hover{
        box-shadow: 0 5px 20px #2ecc71;
    }
    .request_friend p{
        margin-right: 10px;
    }
    .request_friend img{
        border-radius: 50%;
        transition: width .5s, border-radius .5s;
    }
    .request_friend img:hover{
        border-radius: 0;
        width: 100px;
     }
    .request_friend input[type="submit"]{
        margin-top: 5px;
        margin-right: 5px;
        border-radius: 20px;
        border: none;
        padding: 5px 10px;
        color: white;
        transition: box-shadow .5s, padding .5s;
    }
    .request_friend input[type="submit"]:hover{
        box-shadow: 0 5px 20px gray;
        padding: 7px 20px;
    }
    #accept_button{
        background-color: #2ecc71;
    }
    #ignore_button{
        background-color: #e74c3c;
    }
</style>
