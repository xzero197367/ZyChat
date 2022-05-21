<?php

class Message{
    private $user_obj;
    private $con;

    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getMostRecentUser(){
        $userLoggedIn = $this->user_obj->getUserName();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn'  OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1 ");

        if (mysqli_num_rows($query) == 0){
            return false;
        }

        $row = mysqli_fetch_array($query);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if ($user_to != $userLoggedIn)
            return $user_to;
        else
            return $user_from;
    }

    public function sendMessage($user_to, $body, $date){
        if ($body != ""){
            $userLoggedIn = $this->user_obj->getUserName();
            $query = mysqli_query($this->con, "INSERT INTO messages VALUES('', '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");

        }
    }

    public function getMessages($otherUser){
        $userLoggedIn = $this->user_obj->getUserName();
        $data = "";

        $query = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");

        $get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')");

        while ($row = mysqli_fetch_array($get_messages_query)){
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $body = $row['body'];

            $div_top = ($user_to == $userLoggedIn)? "<div class='message' id='green'>": "<div class='message' id='blue'>";
            $data = $data . $div_top . $body . "</div><br><br>";
        }

        return $data;
    }

    public function getLatestMessage($userLoggedIn, $user2){
        $details_array = array();

        $query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");
        $row = mysqli_fetch_array($query);
        $sent_by = ($row['user_to'] == $userLoggedIn)? "They said: ": "You said: ";

        // Time frame
        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DateTime($row['date']);
        $end_date = new DateTime($date_time_now);
        $interval = $start_date->diff($end_date);

        if ($interval->y >= 1) {
            if ($interval == 1) {
                $time_message = $interval->y . " year ago";// 1 year ago
            } else {
                $time_message = $interval->y . " years ago";// 1+ years ago
            }
        } elseif ($interval->m >= 1) {
            if ($interval->d == 0) {
                $days = "ago";
            } else if ($interval->d == 1) {
                $days = $interval->d . " day ago";
            } else {
                $days = $interval->d . " days ago";
            }

            if ($interval->m == 1) {
                $time_message = $interval->m . " month " . $days;
            } else {
                $time_message = $interval->m . " months " . $days;
            }
        } elseif ($interval->d >= 1) {
            if ($interval->d == 1) {
                $time_message = "Yesterday";
            } else {
                $time_message = $interval->d . " days ago";
            }
        } elseif ($interval->h >= 1) {
            if ($interval->h == 1) {
                $time_message = $interval->h . " hour ago";
            } else {
                $time_message = $interval->h . " hours ago";
            }
        } elseif ($interval->i >= 1) {
            if ($interval->i == 1) {
                $time_message = $interval->i . " minute ago";
            } else {
                $time_message = $interval->i . " minutes ago";
            }
        } else {
            if ($interval->s < 30) {
                $time_message = "Just now";
            } else {
                $time_message = $interval->s . " seconds ago";
            }
        }

        array_push($details_array, $sent_by);
        array_push($details_array, $row['body']);
        array_push($details_array, $time_message);

        return $details_array;
    }

    public function getConvos()
    {
        $userLoggedIn = $this->user_obj->getUserName();
        $return_string = "";
        $convos = array();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE (user_to='$userLoggedIn' OR user_from='$userLoggedIn') ORDER BY DATE DESC");

        while ($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn)? $row['user_to'] : $row['user_from'];
            if (!in_array($user_to_push, $convos)){
                array_push($convos, $user_to_push);
            }
        }

        foreach ($convos as $username){
            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = (strlen($latest_message_details[1])>=12)?"...." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;

            $return_string .= "
                        <a href='messages.php?u=$username'>
                            <div class='user_found_messages'>
                                <div style='display: flex; flex-direction: column;'>
                                    <img src='".$user_found_obj->getAll()['profile_pic']."' alt=''>
                                    <span>".$user_found_obj->getFirstAndLastName()."</span>
                                </div>
                                
                                
                              
                                <p id='grey' style='margin: 0;'>
                                    ".$latest_message_details[0].$split."
                                    <br>
                                    <span class='conv_time'>".$latest_message_details[2]."</span>
                                </p>
                            </div>
                        </a>
                        <hr>
                    ";
        }

        return $return_string;

    }

    public function getConvosDropdown($data, $limit){
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUserName();
        $return_string = "";
        $convos = array();

        if ($page == 1){
            $start = 0;
        }else{
            $start = ($page - 1)*$limit;
        }


        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY DATE DESC ");

        while ($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn)? $row['user_to'] : $row['user_from'];
            if (!in_array($user_to_push, $convos)){
                array_push($convos, $user_to_push);
            }
        }

        $num_iterations = 0; // number of messages checked
        $count = 1; // number of messages posted

        foreach ($convos as $username){
            if ($num_iterations++ < $start)
                continue;

            if ($count > $limit)
                break;
            else
                $count++;

            $set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn' AND user_from='$username'");
            $is_unread_query = mysqli_query($this->con, "SELECT opened FROM messages WHERE user_to='$userLoggedIn' AND user_from='$username' ORDER BY date DESC");
            $style = "";
            if (mysqli_num_rows($is_unread_query) > 0){
                $new_row = mysqli_fetch_array($is_unread_query);
                $style = ($new_row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";
            }


            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);

            $dots = (strlen($latest_message_details[1])>=12)?"...." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;

            $return_string .= "
                        <a href='messages.php?u=$username' >
                            <div class='user_found_messages' style='".$style."margin-bottom:10px;display:flex;justify-content: space-evenly;align-items: center; padding: 3px; box-shadow: 0 5px 20px gray; border-radius: 10px;'>
                                <div style='display: flex; flex-direction: column;align-items: center;'>
                                    <img width='60px' src='".$user_found_obj->getAll()['profile_pic']."' alt='' style='border-radius: 50%; text-align: center'>
                                    <span>".$user_found_obj->getFirstAndLastName()."</span>
                                </div>
                                <p id='grey' style='margin: 0;'>
                                    ".$latest_message_details[0].$split."
                                    <br>
                                    <span class='conv_time'>".$latest_message_details[2]."</span>
                                </p>
                            </div>
                        </a>
                 
                    ";
        }

        if ($count > $limit){
            $return_string .= "<input type='hidden' class='nextPageDropDownData' value='".($page + 1)."'><input type='hidden' class='noMoreDropdownData' value='false'>";
        }else{
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align: center;'>No more messages to load!</p>";
        }

        return $return_string;

    }

    public function getUnreadNumber(){
        $userLoggedIn = $this->user_obj->getUserName();
        $query = mysqli_query($this->con, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }
}











