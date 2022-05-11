<?php
include("header.php");



if (isset($_POST['post'])){
    $post = new Post($con, $userLoggedIn);
    $post->submitPost($_POST['post_text'], 'none');
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

<!--        main column comment and others-->
        <div class="main_column column">
            <form action="index.php" method="post" class="post_form">
                <a href="<?php echo $userLoggedIn;?>">
                    <img src="<?php echo $user['profile_pic']?>" alt="<?php echo $user['first_name']?>">
                </a>
                <textarea name="post_text" id="post_text" placeholder="Post Something..."></textarea>
                <input type="submit" name="post" id="post_button" value="Post">
            </form>

<!--            --><?php
//
//            $post = new Post($con, $userLoggedIn);
//            $post->loadPostsFriends();
//
//            ?>

            <div class="posts_area"></div>
            <img src="assets/images/icons/loading.gif" alt="" id="loading">

        </div>

        <script>
            var userLoggedIn = '<?php echo $userLoggedIn;?>';

            $(document).ready(function (){
               $("#loading").show();

               // original ajax request for loading first posts
                $.ajax({
                    url: "includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn="+userLoggedIn,
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
                          url: "includes/handlers/ajax_load_posts.php",
                          type: "POST",
                          data: "page="+page+"&userLoggedIn="+userLoggedIn,
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
