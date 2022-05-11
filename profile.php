<?php
include ("header.php");




if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
}


if (isset($_POST['remove_friend'])){
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
}
if (isset($_POST['add_friend'])){
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
}
if (isset($_POST['respond_request'])){
    $user = new User($con, $userLoggedIn);
    header("Location: requests.php");
}

$message_obj = new Message($con, $userLoggedIn);

if (isset($_POST['post_message'])){
    if (isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($username, $body, $date);
    }

    $link = "#myTab button[data-bs-target='#message']";
    echo '<script>
            
        </script>';
}

?>


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
                            <input type='submit' name='request' class='default' value='Request Sent'><br>
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

            <!-- Nav tabs -->
            <ul class="nav nav-pills nav-fill" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Home</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="message-tab" data-bs-toggle="tab" data-bs-target="#message" type="button" role="tab" aria-controls="message" aria-selected="false">Messages</button>
                </li>
            </ul>





            <div class="tab-content">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="posts_area"></div>
                    <img src="assets/images/icons/loading.gif" alt="" id="loading">
                </div>
                <div class="tab-pane fade" id="message" role="tabpanel" aria-labelledby="contact-tab">
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

<style>
    .wrapper{
        margin-left: 0;
        padding-left: 0;
    }

    .title{
        text-align: center;
    }
    .profile_left{
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 100%;
    }
    .profile_left img{
        min-width: 80px;
        width: 55%;
        margin: 0;
        border: 5px solid #83D6fe;
        border-radius: 100px;
    }

    /* popup style*/
    .popup_model_post{
        display: none;
        font-family: "Bellota-BI", sans-serif;
        padding: 10px;
        width: 500px;
        position: fixed;
        top: 20%;
        right: 35%;
        box-shadow: 0 5px 20px gray;
        border-radius: 20px;
    }

    .header{
        position: relative;
        display: flex;
        text-align: center;
        justify-content: center;
    }
    .header button{
        height: 10px;
        padding: 0;
        font-family: "Bellota-BI", sans-serif;
        background-color: transparent;
        color: red;
        font-size: 30px;
        border: none;
        margin: 0 10px;
        position: absolute;
        right: 0;
    }
    .header button:hover{
        box-shadow: 0 5px 10px gray;
    }
    .mydata{
        margin: 0 5px;
        display: flex;
    }
    .mydata img{
        width: 50px;
        border-radius: 50%;
        transition: width .5s, box-shadow .5s;
    }
    .mydata img:hover{
        box-shadow: 0 0 20px gray;
        width: 100px;
    }
    .details{
        display: flex;
        flex-direction: column;
    }
    .details input[type='submit']{
        border: none;
        font-size: 12px;
    }

    .popup_model_post form{
        display: flex;
        flex-direction: column;
        margin: 5px;
    }
    .post_details_field{
        resize: none;
        outline-color: #3498db;
        border-radius: 5px;
        padding: 5px;
        margin: 10px 0;
        height: 100px;
        border: none;
        box-shadow: 0 0 5px gray;
        transition: height .5s;
    }
    .post_details_field:focus{
        height: 200px;
    }
    form input[type='submit']{
        border-radius: 10px;
        border: none;
        color: white;
        font-size: 20px;
        padding: 5px;
        transition: box-shadow .5s, font-size .5s;
    }
    form input[type='submit']:hover{
        box-shadow: 0 0 20px gray;
        font-size: 30px;
    }
    /* end of popup style */

    /* add friend button style*/
    .danger{
        background-color: #e74c3c;
    }
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

    .warning{
        background-color: #f0ad4e;
    }

    .default{
        background-color: #bdc3c7;
    }

    .success{
        background-color: #2ecc71;
    }

    .info{
        background-color: #3498db;
    }

    .deep_blue{
        background-color: #0043f0;
    }

    .profile_left input[type="submit"]{
        font-family: "Bellota-BI", sans-serif;
        width: 90%;
        border-radius: 10px;
        padding: 5px;
        margin: 7px 3px 3px 7px;
        border: none;
        color: white;
        transition: padding .5s, box-shadow .5s, font-size .5s;
    }

    .profile_left input[type="submit"]:hover{
        padding: 10px;
        box-shadow: 0 5px 50px black;
        font-size: 20px;
    }


    /* message style*/
    #message_textarea{
        font-family: 'Bellota-LI', sans-serif;
        resize: none;
        padding: 5px;
        width: 70%;
        outline-color: #3498db;
        border: 1px solid gray;
        box-shadow: 0 5px 5px gray;
        border-radius: 10px;
        transition: height .5s, box-shadow .5s;
    }
    #message_textarea:focus{
        box-shadow: 0 5px 20px #3498db;
        height: 100px;
    }
    #message_submit{
        font-size: 30px;
        font-family: "Bellota-BI", sans-serif;
        width: 20%;
        border-radius: 20px;
        border: none;
        color: #fff;
        background-color: #3498db;
        box-shadow: 0 5px 10px gray;
        transition: box-shadow .5s;
    }
    #message_submit:hover{
        box-shadow: 10px 5px 30px gray;
    }
    .message{
        font-size: 20px;
        border-radius: 20px;
        padding: 5px 10px;
        display: inline-block;
        color: white;
        font-family: 'Bellota-LI', sans-serif;
    }
    #green{
        box-shadow: 1px 5px 5px #2ecc71;
        background-color: #2ecc71;
    }
    #blue{
        box-shadow: 1px 5px 5px #3498db;
        background-color: #3498db;
        float: right;
    }
    .loaded_messages{
        margin-bottom: 10px;
        padding: 10px;
        height: 80%;
        min-height: 300px;
        max-height: 600px;
        overflow: scroll;
    }
    /******* message style*/
    /**/
</style>