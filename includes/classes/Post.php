<?php

class Post{
    private $user_obj;
    private $con;

    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    // submit post
    public function submitPost($body, $user_to){
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);
        $check_empty = preg_replace('/\s+/', '', $body);

        if ($check_empty != ""){

            // Current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get username
            $added_by = $this->user_obj->getUserName();

            // if user is on own profile, user_to is 'none' يبعت للكل
            if ($user_to == $added_by){
                $user_to = "none";
            }

            // insert post
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0')");

            // get the id of the new post
            $returned_id = mysqli_insert_id($this->con);

            // Insert notifications
            if ($user_to != 'none'){
                $notify = new Notification($this->con, $added_by);
                $notify->insertNotification($returned_id, $user_to, 'profile_post');
            }

            // Update post count for user
            $num_posts = $this->user_obj->getNumPosts();
            $num_posts++;
            $update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
        }
    }

    /**
     *  load posts from the db
     */
    public function loadPostsFriends($data, $limit)
    {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUserName();

        if($page == 1){
            $start = 0;
        }else{
            $start = ($page - 1)*$limit;
        }

        $str = ""; // String to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");

        if(mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0; // number of result checked
            $count = 1;


            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];

                // Prepare user_to string so it can be included even if not posted to a user
                if ($row['user_to'] == "none") {
                    $user_to = "";
                } else {
                    $user_to_obj = new User($this->con, $row['user_to']);
                    $user_to_name = $user_to_obj->getFirstAndLastName();
                    $user_to = "to <a href='" . $row['user_to'] . "'>" . $user_to_name . "</a>";
                }

                // Check if user who posted has their account closed
                $added_by_obj = new User($this->con, $added_by);
                if ($added_by_obj->isClosed()) {
                    continue;
                }
                // view post from friends only
                $userLogged_obj = new User($this->con, $userLoggedIn);
                if($userLogged_obj->isFriend($added_by)) {

                    // do this until reach the last number of downloaded posts and start after that
                    if ($num_iterations++ < $start) {
                        continue;
                    }

                    //Once 10 posts have been loaded break
                    if ($count > $limit) {
                        break;
                    } else {
                        $count++;
                    }

                    // add delete post button form the owner of post
                    if ($userLoggedIn == $added_by) {
                        $delete_button = "<button class='delete_button danger' id='post$id'>X</button>";
                    }else{
                        $delete_button = "";
                    }
                    // user added info
                    $user_row = new User($this->con, $added_by);
                    $profile_pic = $user_row->getAll()['profile_pic'];
                    $firstName = $user_row->getAll()['first_name'];
                    $lastName = $user_row->getAll()['last_name'];
                    ?>

                    <script>
                        function toggle<?php echo $id; ?>() {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");
                            var target = $(event.target);

                            if(!target.is("a")) {
                                if (element.style.display === "block") {
                                    element.style.display = "none";
                                } else {
                                    element.style.display = "block";
                                }
                            }
                        }

                    </script>

                    <?php
                    // check for comment for each post
                    $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);
                    // Time frame
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time);
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

                    // prepare string to echo
                    $str .= "
                        <div class='post_layout' style='margin-top: 10px;' onclick='javascript:toggle$id()'>
                            <div class='status_post'>
                                <div class='posted_by' style='color: #acacac;'>

                                    <a class='post_profile_pic' href='$added_by'>
                                        <img src='$profile_pic' alt=''>
                                    </a>
                                
                                    <a href='$added_by'>$firstName $lastName</a>
                                    $user_to
                                    <br>
                                    <span>$time_message</span>
                                    
                                </div>
                                $delete_button
                            </div>
                            
                            <div class='post_body'>
                                $body
                            </div>
                            <div class='newsfeddPostOptions'>
                                    Comments($comments_check_num) &nbsp; &nbsp;
                                    <iframe src='like.php?post_id=$id' scrolling='no' id='comment_like_post'></iframe>
                            </div>
                        </div>
                        <div class='post_comment' id='toggleComment$id' style='display: none;'>
                            <iframe src='comment_frame.php?post_id=$id'
                            id='comment_iframe' ></iframe>
                        </div>
                    ";
                }// end of if user friend

                ?>

<!--                    delete button event-->
                <script>
                    $(document).ready(function () {

                        <?php

                        ?>


                        $('#post<?php echo $id;?>').on('click', function () {

                            let post_id = <?php echo $id; ?>;

                            bootbox.confirm("Are you sure you want to delete this post?", function (result) {

                                $.post("includes/form_handlers/delete_post.php?post_id="+post_id, {result:result}, function (){
                                    alert("success");
                                });


                                if (result) {
                                    location.reload();
                                }

                            });
                        });

                    });
                </script>

                <?php

            }// while loop


            if ($count > $limit){
                $str .= "
                    <input type='hidden' class='nextPage' value='".($page + 1)."'>
                    <input type='hidden' class='noMorePosts' value='false'>
                ";
            }else{
                $str .= "
                        <input type='hidden' class='noMorePosts' value='true'>
                        <p style='text-align: center;'>No More posts to show!</p>
                        ";
            }
        }// end of  if statement check num rows

        echo $str;
    }

    /*
     * load profile posts
     */
    public function loadPostsProfile($data, $limit)
    {
        $page = $data['page'];
        $profileUser = $data['profileUsername'];

        $userLoggedIn = $this->user_obj->getUserName();

        if($page == 1){
            $start = 0;
        }else{
            $start = ($page - 1)*$limit;
        }

        $str = ""; // String to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");

        if(mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0; // number of result checked
            $count = 1;


            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];



                // view post from friends only


                // do this until reach the last number of downloaded posts and start after that
                if ($num_iterations++ < $start) {
                    continue;
                }

                //Once 10 posts have been loaded break
                if ($count > $limit) {
                    break;
                } else {
                    $count++;
                }

                // add delete post button form the owner of post
                if ($userLoggedIn == $added_by) {
                    $delete_button = "<button class='delete_button danger' id='post$id'>X</button>";
                }else{
                    $delete_button = "";
                }
                // user added info
                $user_row = new User($this->con, $added_by);
                $profile_pic = $user_row->getAll()['profile_pic'];
                $firstName = $user_row->getAll()['first_name'];
                $lastName = $user_row->getAll()['last_name'];
                ?>

                <script>
                    function toggle<?php echo $id; ?>() {
                        var element = document.getElementById("toggleComment<?php echo $id; ?>");
                        var target = $(event.target);

                        if(!target.is("a")) {
                            if (element.style.display === "block") {
                                element.style.display = "none";
                            } else {
                                element.style.display = "block";
                            }
                        }
                    }

                </script>

                <?php
                // check for comment for each post
                $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);
                // Time frame
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time);
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

                // prepare string to echo
                $str .= "
                    <div class='post_layout' style='margin-top: 10px;' onclick='javascript:toggle$id()'>
                        <div class='status_post'>
                            <div class='posted_by' style='color: #acacac;'>

                                <a class='post_profile_pic' href='$added_by'>
                                    <img src='$profile_pic' alt=''>
                                </a>
                            
                                <a href='$added_by'>$firstName $lastName</a>
                                <br>
                                <span>$time_message</span>
                                
                            </div>
                            $delete_button
                        </div>
                        
                        <div class='post_body'>
                            $body
                        </div>
                        <div class='newsfeddPostOptions'>
                                Comments($comments_check_num) &nbsp; &nbsp;
                                <iframe src='like.php?post_id=$id' scrolling='no' id='comment_like_post'></iframe>
                        </div>
                    </div>
                    <div class='post_comment' id='toggleComment$id' style='display: none;'>
                        <iframe src='comment_frame.php?post_id=$id'
                        id='comment_iframe' ></iframe>
                    </div>
                ";
            // end of if user friend

                ?>

                <!--                    delete button event-->
                <script>
                    $(document).ready(function () {

                        <?php

                        ?>


                        $('#post<?php echo $id;?>').on('click', function () {

                            let post_id = <?php echo $id; ?>;

                            bootbox.confirm("Are you sure you want to delete this post?", function (result) {

                                $.post("includes/form_handlers/delete_post.php?post_id="+post_id, {result:result}, function (){
                                    alert("success");
                                });


                                if (result) {
                                    location.reload();
                                }

                            });
                        });

                    });
                </script>

                <?php

            }// while loop


            if ($count > $limit){
                $str .= "
                    <input type='hidden' class='nextPage' value='".($page + 1)."'>
                    <input type='hidden' class='noMorePosts' value='false'>
                ";
            }else{
                $str .= "
                        <input type='hidden' class='noMorePosts' value='true'>
                        <p style='text-align: center;'>No More posts to show!</p>
                        ";
            }
        }// end of  if statement check num rows

        echo $str;
    }



}


?>
<!-- comments style-->
<style>
    .delete_button{
        border: none;
        color: #e74c3c;
        font-family: "Bellota-BI", sans-serif;
        font-weight: bold;
        background-color: transparent;
        border-radius: 50px;
        box-shadow: 0 0 5px gray;
        transition: box-shadow .5s;
    }
    .delete_button:hover{
        box-shadow: 0 5px 20px gray;
    }
    .status_post{
        display: flex;
        justify-content: space-between;
        align-items:  center;
    }
    #comment_iframe{
        width: 100%;
        max-height: 300px;
    }

    #comment_form textarea{
        resize: none;
        font-family: 'Bellota-LI', sans-serif;
        height: 40px;
        width: 100%;
        border-radius: 10px;
        outline: none;
        padding: 5px;
        border: none;
        box-shadow: 0 0 5px gray;
        transition: height .5s, box-shadow .5s, width .5s;
    }
    #comment_form textarea:focus{
        width: 100%;
        outline: 0;
        height: 90px;
        resize: none;
        box-shadow: 0 0 20px gray;
    }


    #comment_form input[type="submit"]{
        font-size: 20px;
        float: right;
        margin-right: 10px;
        border: none;
        border-radius: 7px;
        background-color: #3498db;
        font-family: "Bellota-BI", sans-serif;
        color: #1E75CA;
        text-shadow: #73B6E2;
        outline: 0;
        box-shadow: 5px 5px 5px gray;
        transition: font-size .5s, background-color 1s;
    }

    #comment_form input[type="submit"]:hover{
        padding: 5px 4px 4px 5px;
        font-size: 30px;
        color: white;
        box-shadow: 5px 5px 10px gray;
    }

    .comment_section{
        margin:20px 10px 0 20px;
        display: flex;
    }

    .comment_section img{
        border-radius: 50%;
        margin-right: 10px;
    }

    .comment_div{
        box-shadow: 0 0 3px gray;
        width: 100%;
        padding: 10px;
        border-radius: 30px;
    }

    .comment_div a{
        font-family: "Bellota-BI", sans-serif;
        font-size: 15px;
    }
    .comment_div span{
        font-family: "Bellota-LI", sans-serif;
        font-size: 13px;
        color: #3498db;
    }
    .comment_div div{
        font-family: "Bellota-LI", sans-serif;
        font-size: 18px;
        color: gray;
    }
</style>
