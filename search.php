<?php
include "header.php";

if (isset($_GET['q'])){
    $query = $_GET['q'];
}else{
    $query = "";
}

?>

<div class="main_column column" id="main_column">
    <?php

    if ($query == ""){
        echo "You must enter something in the search box.";
    }else{

        if (strpos($query, '_') !== false){
            $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no'");
        }else{
            $names = explode(" ", $query);
            if (count($names) == 3) {
                $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
            }else if (count($names) == 2){
                $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
            }else{
                $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
            }
        }

        // check if result were found
        if (mysqli_num_rows($userReturnedQuery) == 0){
            echo "We can't find anyone like: ".$query;
        }else{
            echo mysqli_num_rows($userReturnedQuery)." results found";
        }



        while ($row = mysqli_fetch_array($userReturnedQuery)){
            $user_obj = new User($con, $user['username']);

            $button = "";
            $mutual_friends = "";

            if ($user['username'] != $row['username']){
                // Generate button depending on friendship status
                if ($user_obj->isFriend($row['username'])){
                    $button = "<input type='submit' name='".$row['username']."' class='danger' value='Remove Friend'>";
                }else if ($user_obj->didReceiveRequest($row['username'])){
                    $button = "<input type='submit' name='".$row['username']."' class='warning' value='Respond to request'>";
                }else if ($user_obj->didSendRequest($row['username'])){
                    $button = "<input type='submit' name='".$row['username']."' class='default' value='Request sent'>";
                }else{
                    $button = "<input type='submit' name='".$row['username']."' class='success' value='Add Friend'>";
                }

                $mutual_friends = $user_obj->getMutualFriends($row['username'])." friends in common";

                $username = $row['username'];

                // buttons form
                if (isset($_POST[$username])){
                    if($user_obj->isFriend($row['username'])) {
                        $user_obj->removeFriend($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    } else if($user_obj->didReceiveRequest($row['username'])) {
                        header("Location: requests.php");
                    } else if ($user_obj->didSendRequest($row['username'])){
                        $user_obj->rmFriendRequest($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }else {
                        $user_obj->sendRequest($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                }
            }


            echo "
                    <div class='resultDisplay'>
                        <a href='".$row['username']."' style='color: #1485bd;'>
                            <div class='liveSearchProfilePic'>
                                <img src='".$row['profile_pic']."'>
                            </div>
                            
                            <div class='liveSearchText'>
                                ".$row['first_name']." ".$row['last_name']."
                                <p>".$row['username']."</p>
                                <span id='grey'>".$mutual_friends."</span>
                            </div>
                            
                            <div class='searchPageFriendbuttons'>
                                <form action='' method='post'>
                                    ".$button."
                                </form>
                            </div>
                        </a>    
                    </div>
                ";
        }// end while

    }

    ?>
</div>
