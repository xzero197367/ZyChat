<?php

class Post{
    private $user_obj;
    private $con;

    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    // submit post
    public function submitPost($body, $user_to, $imageName){
        $body = strip_tags($body);
        $body = mysqli_real_escape_string($this->con, $body);
        $check_empty = preg_replace('/\s+/', '', $body);

        if ($check_empty != ""){
            //check for youtube links
            $body_array = preg_split("/\s+/", $body);
            foreach ($body_array as $key => $value){
                if (strpos($value, "www.youtube.com/watch?v=") !== false){
                    $link = preg_split("!&!", $value);
                    $value = preg_replace("!watch\?v=!", "embed/", $value);
                    $value = "<br><iframe width=\'420\' height=\'315\' src=\'".$value."\'></iframe><br>";
                    $body_array[$key] = $value;
                }
            }

            $body = implode(" ", $body_array);


            // Current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get username
            $added_by = $this->user_obj->getUserName();

            // if user is on own profile, user_to is 'none' يبعت للكل
            if ($user_to == $added_by){
                $user_to = "none";
            }

            // insert post
            $query = mysqli_query($this->con, "INSERT INTO posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");

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
                $imagePath = $row['image'];

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
                        $delete_button = "<button class='delete_button' id='post$id'>X</button>";
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

                    // process the image before load it
                    if ($imagePath != ''){
                        $imageDiv = "
                            <div class='postedImage'>
                                <img src='$imagePath' alt='' style='width: 100%;'>
                            </div>
                        ";
                    }else{
                        $imageDiv = "";
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
                            $imageDiv
                            
                            <div class='newsfeedPostOptions'>
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
                    $delete_button = "<button class='delete_button' id='post$id'>X</button>";
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

                                $.post("includes/form_handlers/delete_post.php?post_id="+post_id, {result:result});

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

    public function getSinglePost($post_id) {

        $userLoggedIn = $this->user_obj->getUsername();

        $opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

        $str = ""; //String to return
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$post_id'");

        if(mysqli_num_rows($data_query) > 0) {
            $row = mysqli_fetch_array($data_query);
            $id = $row['id'];
            $body = $row['body'];
            $added_by = $row['added_by'];
            $date_time = $row['date_added'];

            //Prepare user_to string so it can be included even if not posted to a user
            if($row['user_to'] == "none") {
                $user_to = "";
            }
            else {
                $user_to_obj = new User($this->con, $row['user_to']);
                $user_to_name = $user_to_obj->getFirstAndLastName();
                $user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
            }

            //Check if user who posted, has their account closed
            $added_by_obj = new User($this->con, $added_by);
            if($added_by_obj->isClosed()) {
                return;
            }

            $user_logged_obj = new User($this->con, $userLoggedIn);
            if($user_logged_obj->isFriend($added_by)){


                if($userLoggedIn == $added_by)
                    $delete_button = "<button class='delete_button-' id='post$id'>X</button>";
                else
                    $delete_button = "";


                $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];


                ?>
                <script>
                    function toggle<?php echo $id; ?>() {

                        var target = $(event.target);
                        if (!target.is("a")) {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if(element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }
                    }

                </script>
                <?php

                $comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                $comments_check_num = mysqli_num_rows($comments_check);


                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
                $end_date = new DateTime($date_time_now); //Current time
                $interval = $start_date->diff($end_date); //Difference between dates
                if($interval->y >= 1) {
                    if($interval == 1)
                        $time_message = $interval->y . " year ago"; //1 year ago
                    else
                        $time_message = $interval->y . " years ago"; //1+ year ago
                }
                else if ($interval->m >= 1) {
                    if($interval->d == 0) {
                        $days = " ago";
                    }
                    else if($interval->d == 1) {
                        $days = $interval->d . " day ago";
                    }
                    else {
                        $days = $interval->d . " days ago";
                    }


                    if($interval->m == 1) {
                        $time_message = $interval->m . " month". $days;
                    }
                    else {
                        $time_message = $interval->m . " months". $days;
                    }

                }
                else if($interval->d >= 1) {
                    if($interval->d == 1) {
                        $time_message = "Yesterday";
                    }
                    else {
                        $time_message = $interval->d . " days ago";
                    }
                }
                else if($interval->h >= 1) {
                    if($interval->h == 1) {
                        $time_message = $interval->h . " hour ago";
                    }
                    else {
                        $time_message = $interval->h . " hours ago";
                    }
                }
                else if($interval->i >= 1) {
                    if($interval->i == 1) {
                        $time_message = $interval->i . " minute ago";
                    }
                    else {
                        $time_message = $interval->i . " minutes ago";
                    }
                }
                else {
                    if($interval->s < 30) {
                        $time_message = "Just now";
                    }
                    else {
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
                            
                                <a href='$added_by'>$first_name $last_name</a>
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


                ?>
                <script>

                    $(document).ready(function() {

                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?", function(result) {

                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

                                if(result)
                                    location.reload();

                            });
                        });


                    });

                </script>
                <?php
            }
            else {
                echo "<p>You cannot see this post because you are not friends with this user.</p>";
                return;
            }
        }
        else {
            echo "<p>No post found. If you clicked a link, it may be broken.</p>";
            return;
        }

        echo $str;
    }

}


?>
