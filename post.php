<?php
include "header.php";

if (isset($_GET['id'])){
    $id = $_GET['id'];
}else{
    $id = 0;
}

?>


    <div class="user_details column">
        <a href="<?php echo $userLoggedIn;?>">
            <img src="<?php echo $user['profile_pic']?>" alt="<?php echo $user['first_name']?>">
        </a>

        <div class="user_details_left_right">
            <a href="<?php echo $userLoggedIn;?>">
                <?php
                echo $user['first_name']." ".$user['last_name'];
                ?>
            </a>
            <br>
            <?php
            echo "Posts: ". $user['num_posts']."<br>".
                "Likes: ".$user['num_likes'];
            ?>
        </div>
    </div>

    <div class="main_column column" id="main_column">
        <div class="posts_area">
            <?php
                $post = new Post($con, $userLoggedIn);
                $post->getSinglePost($id);
            ?>
        </div>
    </div>


<style>
    /* style list of posts */


    .status_post a:hover{
        text-decoration: none;
    }


    .post_profile_pic img{
        width: 50px;
        border-radius: 50px;
        transition: width .5s, border-radius .5s, box-shadow .5s;
    }

    .post_profile_pic img:hover{
        width: 100px;
        border-radius: 0;
        box-shadow: 0 5px 20px gray;
    }

    /**** end of style list of posts */
</style>
