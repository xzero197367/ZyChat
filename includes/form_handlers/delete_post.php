<?php
require '../../config/config.php';


if (isset($_GET['post_id'])){
    $post_id = $_GET['post_id'];
}
if (isset($_POST['result'])){
    if ($_POST['result'] == 'true'){

        $get_added_by = mysqli_query($con, "SELECT * FROM posts WHERE id='$post_id'");
        $row = mysqli_fetch_array($get_added_by);
        $added_by = $row['added_by'];

        $get_num_posts = mysqli_query($con, "SELECT num_posts FROM users WHERE username='$added_by'");
        $row2 = mysqli_fetch_array($get_num_posts);
        $num_posts = $row2['num_posts'];
        $num_posts--;

        mysqli_query($con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

        $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
    }
}

