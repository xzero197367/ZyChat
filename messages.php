<?php
include ("header.php");

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['u'])){
    $user_to = $_GET['u'];
}else{
    $user_to = $message_obj->getMostRecentUser();
    if ($user_to == false){
        $user_to = 'new';
    }
}

if ($user_to != "new"){
    $user_to_obj = new User($con, $user_to);
}

if (isset($_POST['post_message'])){
    if (isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);

        header("Location: messages.php?$user_to");
        exit();
    }
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



    <div class="main_column column">
        <?php
        if ($user_to != "new"){
            echo "<h4>You and <a href='$user_to'>".$user_to_obj->getFirstAndLastName()."</a> </h4><hr><br>";
            echo "<div class='loaded_messages' id='scroll_messages'>";
                echo $message_obj->getMessages($user_to);
            echo "</div>";
        }else{
            echo "<h4>New Message</h4>";
        }
        ?>

        <div class="message_post">
            <form action="" method="post">
                <?php

                if ($user_to == "new"){
                    echo "Select the friend you would like to message <br>";
                    ?>

                    To: <input class="search_friends_field" type="text" placeholder="Name" onkeyup="getUser(this.value, '<?php echo $userLoggedIn;?>')" name="q" autocomplete="off" id="search_text_input">

                    <?php

                }else{
                    echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message....'></textarea>";
                    echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
                }

                ?>
            </form>

            <?php
            echo "<div class='results'></div>";
            ?>
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


    <div class="user_details column" id="conversations">
        <h4>Conversations</h4>

        <div class="load_conversations">
            <?php  echo $message_obj->getConvos();?>
        </div>
        <br>
        <a href="messages.php?u=new">New Message</a>
    </div>




</div>

</body>

</html>



<style>

    .user_details{
        width: 400px;
    }

    .message_post form{
        display: flex;
        justify-content: space-evenly;
        transition: height 1s;
    }
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
    /*conversations style*/
    #conversations{
        height: 600px;
    }
    .load_conversations{
        margin-bottom: 10px;
        padding: 5px;
        min-height: 450px;
        max-height: 500px;
        height: 60%;
        overflow: scroll;
    }
    .load_conversations a{
        text-decoration: none;
    }
    .user_found_messages{
        font-size: 15px;
        display: flex;
        text-decoration: none;
    }
    .conv_time{
        font-size: 12px;
        color: gray;
        text-decoration: none;
    }

    .user_found_messages img{
        height: 50px;
        width: 50px;
        border-radius: 50%;
    }
    .user_found_messages img:hover{
        width: 100px;
        height: 100px;
    }

    .results{
        position: absolute;
        z-index: 10;
        text-align: center;
        box-shadow: 0 5px 20px #34ef;
        border-radius: 20px;
        padding: 5px;
        right: 10px;
    }
    .search_friends_field{
        border: none;
        padding: 3px;
        outline-color: #3dd5f3;
        border-radius: 10px;
        box-shadow: 0 0 5px gray;
        transition: box-shadow .5s;
    }
    .search_friends_field:focus{
        box-shadow: 0 0 10px #3dd5f3;
    }
/*    end of conversations style*/
</style>
















