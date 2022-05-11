
<?php
require 'config/config.php';
include "includes/classes/User.php";
include "includes/classes/Post.php";
include "includes/classes/Notification.php";

if (isset($_SESSION['username'])){
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
}else{
    header("Location: register.php");
}
?>

<html>
<head>
    <link rel="stylesheet" href="assets/css/style.css">
    <title></title>
</head>
<body>


    <script>
        function toggle() {
            var element = document.getElementById("comment_section");

            if(element.style.display === "block"){
                element.style.display = "none";
            }else{
                element.style.display = "block";
            }
        }
    </script>

    <?php
        // Get id of post
        if (isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
        }

        $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($user_query);

        $post_to = $row['added_by'];
        $user_to = $row['user_to'];

        if (isset($_POST['postComment'.$post_id])){
            $post_body = $_POST['post_body'];
            $check_empty = preg_replace('/\s+/', '', $post_body);
            if($check_empty != '') {
                $post_body = mysqli_escape_string($con, $post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $insert_comment = mysqli_query($con, "INSERT INTO comments VALUES('', '$post_body', '$userLoggedIn', '$post_to', '$date_time_now', 'no', '$post_id')");

                // notifications
                if ($post_to != $userLoggedIn){
                    $notify = new Notification($con, $userLoggedIn);
                    $notify->insertNotification($post_id, $post_to, 'comment');
                }
                if ($user_to != 'none' && $user_to != $userLoggedIn){
                    $notify = new Notification($con, $userLoggedIn);
                    $notify->insertNotification($post_id, $user_to, 'profile_comment');
                }

                // ///// ///
                $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
                $notified_users = array();
                while ($row = mysqli_fetch_array($get_commenters)){
                    if ($row['posted_by'] != $post_to && $row['posted_by'] != $user_to && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)){

                        $notify = new Notification($con, $userLoggedIn);
                        $notify->insertNotification($post_id, $user_to, 'comment_non_owner');

                    }
                }

                echo "<p>Comment Posted!</p>";
            }
        }
    ?>

    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="post">
        <textarea name="post_body" placeholder="Write a comment...">
        </textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
    </form>

<!-- load comments -->
    <?php
        $get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' ORDER  BY id DESC ");
        $count = mysqli_num_rows($get_comments);

        if ($count != 0){
            while($comment = mysqli_fetch_array($get_comments)){
                $comment_body = $comment['post_body'];
                $posted_to = $comment['posted_to'];
                $posted_by = $comment['posted_by'];
                $date_added = $comment['date_added'];
                $removed = $comment['removed'];

                // Time frame
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_added);
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


                $user_obj = new User($con, $posted_by);

                ?>
                <div class="comment_section">
                    <a target="_parent" href="<?php echo $posted_by;?>">
                        <img src="<?php echo $user_obj->getAll()['profile_pic']?>"
                             alt="" title="<?php echo $posted_by;?>"
                             style="float: left;" height="30">
                    </a>
                    <div class="comment_div">
                        <a target="_parent" href="<?php echo $posted_by;?>">
                            <?php echo $user_obj->getFirstAndLastName(); ?>
                        </a>
                        &nbsp; &nbsp; &nbsp; &nbsp;
                        <span><?php echo $time_message;?></span>

                        <div><?php echo $comment_body?></div>
                    </div>
                </div>
                <?php
            }
        }else{
            echo "<center><br><br>No Comments to Show!</center>";
        }
    ?>


</body>
</html>