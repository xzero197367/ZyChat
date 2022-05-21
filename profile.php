<?php
include ("header.php");



if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
}

$user = new User($con, $userLoggedIn);
if (isset($_POST['remove_friend'])){

    $user->removeFriend($username);
}
if (isset($_POST['add_friend'])){
    $user->sendRequest($username);
}
if (isset($_POST['respond_request'])){
    header("Location: requests.php");
}

if (isset($_POST['rm_request'])){
    $user->rmFriendRequest($username);
}

$message_obj = new Message($con, $userLoggedIn);

if (isset($_POST['post_message'])){
    if (isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($username, $body, $date);
    }

//    $link = "#profileTab button[data-bs-target='#message']";
//    echo "
//        <script>
//            const firstTabEl = document.querySelector('#myTab li:last-child button');
//            const firstTab = new bootstrap.Tab(firstTabEl);
//
//            firstTab.show();
//        </script>
//    ";
}

?>


<style>
    .wrapper{
        margin-left: 0;
        padding-left: 0;
    }
</style>

<!--        profile left side info-->
        <div class="profile_left">
            <img src="<?php echo $user_array['profile_pic']?>" alt="">
            <p class="title"><?php echo $user_array['first_name']." ".$user_array['last_name'];?></p>

            <div class="profile_info">
                <p><?php echo "Posts: ".$user_array['num_posts'];?></p>
                <p><?php echo "Likes: ".$user_array['num_likes'];?></p>
                <p><?php echo "Friends: ".$num_friends;?></p>
            </div>

            <form action="<?php echo $username; ?>" method="post">
                <?php

                $profile_user_obj = new User($con, $userLoggedIn);
                if ($profile_user_obj->isClosed()){
                    header("Location: user_closed.php");
                }

                // current user object to check if he is friend
                $logged_in_user_obj = new User($con, $userLoggedIn);
                if ($userLoggedIn != $username){

                    if ($logged_in_user_obj->isFriend($username)){
                        echo "
                            <input type='submit' name='remove_friend' class='danger' value='Remove Friend'><br>
                        ";
                    }else if ($logged_in_user_obj->didReceiveRequest($username)){
                        echo "
                            <input type='submit' name='respond_request' class='warning' value='Respond to Request'><br>
                        ";
                    }else if ($logged_in_user_obj->didSendRequest($username)){
                        echo "
                            <input type='submit' name='rm_request' class='default' value='Request Sent'><br>
                        ";
                    }else{
                        echo "
                            <input type='submit' name='add_friend' class='success' value='Add Friend'><br>
                        ";
                    }

                }

                ?>

            </form>

            <input onclick="hideShow()" type="submit" class="deep_blue" data-toggle="modal" data-target="#post_from" value="Post Something">


            <?php


            if ($userLoggedIn != $username){
                echo "<div class='profile_info_bottom'>";
                    echo $logged_in_user_obj->getMutualFriends($username)." Mutual Friends";
                echo "</div>";
            }

            ?>
        </div>

<!--        main column comment and others-->
        <div class="main_column column">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Messages</button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="home" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                    <div class="posts_area"></div>
                    <img src="assets/images/icons/loading.gif" alt="" id="loading">
                </div>
                <div class="tab-pane" id="messages" role="tabpanel" aria-labelledby="messages-tab" tabindex="0">

                    <?php


                    echo "<h4>You and <a href='".$username."'>".$profile_user_obj->getFirstAndLastName()."</a> </h4><hr><br>";
                    echo "<div class='loaded_messages' id='scroll_messages'>";
                    echo $message_obj->getMessages($username);
                    echo "</div>";

                    ?>

                    <div class="message_post">
                        <form action="" method="post">
                            <textarea name='message_body' id='message_textarea' placeholder='Write your message....'></textarea>
                            <input type='submit' name='post_message' class='info' id='message_submit' value='Send'>
                        </form>
                    </div>
                    <script>
                        $(document).ready(function (){
                            if (document.getElementById("scroll_messages")) {
                                let div = document.getElementById("scroll_messages");
                                div.scrollTop = div.scrollHeight;
                            }
                        });

                    </script>

                </div>
            </div>

            <script>
                const firstTabEl = document.querySelector('#myTab li:last-child button')
                const firstTab = new bootstrap.Tab(firstTabEl)

                firstTab.show()
            </script>


        </div>

<!--        popup post publisher -->
        <div class="popup_model_post" id="popup_post">
            <div class="header">
                <h2>Create post</h2>
                <button onclick="closePopupPost()" class="close_post_btn">&times;</button>
            </div>
            <hr>

            <div class="mydata">
                <img width="40" src="<?php echo $user_array['profile_pic']; ?>" alt="">
                <div class="details">
                    <a href="<?php echo $userLoggedIn;?>"><?php echo $user_array['first_name']." ".$user_array['last_name'];?></a>
                    <input type="submit" value="Friends">
                </div>
            </div>

<!--            to do upload video and pictures-->
            <form class="profile_post" method="post">
                <textarea placeholder="Write Your post here..." name="post_body" class="post_details_field" id="post_text_entered"></textarea>
                <input type="hidden" name="user_from" value="<?php echo $userLoggedIn;?>">
                <input type="hidden" name="user_to" value="<?php echo $username;?>">
                <input class="deep_blue" type="submit" name="post_btn" id="submit_profile_post" value="Post">
            </form>
        </div>


<script>
    var element = document.getElementById("popup_post");
    function hideShow() {
        if (element.style.display === "block") {
            element.style.display = "none";
        } else {
            element.style.display = "block";
        }

    }
    function closePopupPost(){
        element.style.display = "none";
    }
</script>


<script>
    var userLoggedIn = '<?php echo $userLoggedIn;?>';
    var profileUsername = '<?php echo $username;?>'

    $(document).ready(function (){
        $("#loading").show();

        // original ajax request for loading first posts
        $.ajax({
            url: "includes/handlers/ajax_load_profile_posts.php",
            type: "POST",
            data: "page=1&userLoggedIn="+userLoggedIn+"&profileUsername="+profileUsername,
            cache: false,

            success: function (data){
                $('#loading').hide();
                $('.posts_area').html(data);
            }
        });

        $(window).scroll(function (){
            var height = $('.posts_area').height();
            var scroll_top = $(this).scrollTop();
            var page = $('.posts_area').find('.nextPage').val();
            var noMorePosts = $('.posts_area').find('.noMorePosts').val();

            if((document.body.scrollHeight === document.body.scrollTop + window.innerHeight) && noMorePosts === 'false'){
                $('#loading').show();

                var ajaxReq = $.ajax({
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page="+page+"&userLoggedIn="+userLoggedIn+"&profileUsername="+profileUsername,
                    cache: false,

                    success: function (data){
                        $('.posts_area').find('.nextPage').remove();
                        $('.posts_area').find('.noMorePosts').remove();

                        $('#loading').hide();
                        $('.posts_area').append(data);
                    }
                });
            }

            return false;
        });


    });
</script>

    </div>
</body>
</html>

