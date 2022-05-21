<?php

class User{
    private $user;
    private $con;

    public function  __construct($con, $user){
        $this->con = $con;
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
        $this->user = mysqli_fetch_array($user_details_query);
    }

    // get username
    public function getUserName(){
        return $this->user['username'];
    }

    // get number of posts
    public function getNumPosts(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$username'");
        $row = mysqli_fetch_array($query);
        return $row['num_posts'];
    }

    //get first and last name
    public function getFirstAndLastName()
    {
        // fetch data from database
        return $this->user['first_name']." ".$this->user['last_name'];
    }

    /**
     * check if user closed
     */
    public function isClosed()
    {
        $closed = $this->user['user_closed'];
        if($closed == "yes"){
            return true;
        }else{
            return false;
        }
    }

    /**
     * get all info about user
     */
    public function getAll()
    {
        return $this->user;
    }

    /**
     * is friend function
     */
    public function isFriend($username_to_check)
    {
        $usernameComma = ",".$username_to_check.",";

        if((strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username'])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * check friend request receive
     */
    public function didReceiveRequest($user_from)
    {
        $user_to = $this->user['username'];
        $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");

        if (mysqli_num_rows($check_request_query) > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * check friend request send
     */
    public function didSendRequest($user_to)
    {
        $user_from = $this->user['username'];
        $check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");

        if (mysqli_num_rows($check_request_query) > 0){
            return true;
        }else{
            return false;
        }
    }


    /**
     * remove friend
     */
    public function removeFriend($user_to_remove)
    {
        // current user
        $logged_in_user = $this->user['username'];
        // remove the friend from the current user
        $new_fried_array= str_replace($user_to_remove.",", '', $this->user['friend_array']);
        // update the current user database
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_fried_array' WHERE username='$logged_in_user'");

        // remove me from his friend array list first get his friend array list
        $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
        $row = mysqli_fetch_array($query);
        $his_friend_array = $row['friend_array'];
        // remove me from his array list
        $new_fried_array = str_replace($logged_in_user.',', '', $his_friend_array);
        $remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_fried_array' WHERE username='$user_to_remove'");

    }


    // remover friend request
    public function rmFriendRequest($user_to){
        $user_from = $this->user['username'];
        mysqli_query($this->con, "DELETE FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
    }

    /*
     * send add friend request
     */
    public function sendRequest($user_to){
        $user_from = $this->user['username'];
        $date = date("Y-m-d H:i:s");
//        $check_exist = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND  user_from='$user_from'")
        $query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES('', '$user_to', '$user_from', '$date')");
    }

    /*
   * get Mutual friend for the user
   */
    public function getMutualFriends($user_to_check){
        $mutualFriends = 0;
        $user_array = $this->user['friend_array'];
        $user_array_explode = explode(',', $user_array);

        $query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
        $row = mysqli_fetch_array($query);
        $user_to_check_array = $row['friend_array'];
        $user_to_check_array_explode = explode(',', $user_to_check_array);

        foreach ($user_array_explode as $i){
            foreach ($user_to_check_array_explode as $j){
                if ($i == $j && $i != ''){
                    $mutualFriends++;
                }
            }
        }

        return $mutualFriends;
    }

    public function getNumberOfFriendsRequests(){
        $username = $this->user['username'];
        $query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
        return mysqli_num_rows($query);
    }
}


?>