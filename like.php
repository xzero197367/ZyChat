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
    <title></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <style>
        form{
            position: absolute;
            top: 8px;
        }

    </style>

</head>

<body>
    <?php


    // Get id of post
    if (isset($_GET['post_id'])){
        $post_id = $_GET['post_id'];
    }

    // get post likes
    $get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
    $row = mysqli_fetch_array($get_likes);
    $total_likes = $row['likes'];
    $user_liked = $row['added_by'];

    // get info about post writer (added_by)
    $user_details_query1 = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
    $row = mysqli_fetch_array($user_details_query1);
    $total_user_likes = $row['num_likes'];

    // like button
    if(isset($_POST['like_button'])){
        $total_likes++;
        $update_post_likes = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
        $total_user_likes++;
        $update_user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$userLoggedIn'");
        $insert_user_to_likes_table = mysqli_query($con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");

        // Insert Notifications
        if ($user_liked != $userLoggedIn){
            $notify = new Notification($con, $userLoggedIn);
            $notify->insertNotification($post_id, $user_liked, 'like');
        }
    }
    // unlike button
    if(isset($_POST['unlike_button'])){
        $total_likes--;
        $update_post_likes = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
        $total_user_likes--;
        $update_user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$userLoggedIn'");
        $insert_user_to_likes_table = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");


    }

    // check for previous likes
    $check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    $num_rows = mysqli_num_rows($check_query);

    if($num_rows > 0){
        echo "
            <form action='like.php?post_id=$post_id' method='post'>
                <input type='submit' class='comment_like unlike' name='unlike_button' value='Unlike'>
                <div class='like_value'>
                    $total_likes Likes
                </div>
            </form>
        ";
    }else{
        echo "
            <form action='like.php?post_id=$post_id' method='post'>
                <input type='submit' class='comment_like' name='like_button' value='Like'>
                <div class='like_value'>
                    $total_likes Likes
                </div>
            </form>
        ";
    }

    ?>

</body>

</html>