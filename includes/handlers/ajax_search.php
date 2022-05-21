<?php
include "../../config/config.php";
include "../classes/User.php";

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

// if query contains an underscore, assume user is searching for usernames
if (strpos($query, '_') !== false){
    $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");

}// If there are two words, assume the are first and last names respectively
else if (count($names) == 2){
    $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
}// if query has one word only search first names or last names
else{
    $userReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");
}

if ($query != ""){
    while ($row = mysqli_fetch_array($userReturnedQuery)){
        $user = new User($con, $userLoggedIn);

        if ($row['username'] != $userLoggedIn){
            $mutual_friends = $user->getMutualFriends($row['username'])." friends in common";
        }else{
            $mutual_friends = "";
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
                </a>    
            </div>
        ";
    }
}