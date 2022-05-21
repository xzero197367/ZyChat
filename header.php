<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");


if (isset($_SESSION['username'])){
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
}else{
    header("Location: register.php");
}

?>

<html lang="en">
<head>
    <title>Welcome to ZyChat</title>


    <!-- Javascript -->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>-->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/jquery.Jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <script src="assets/js/zychat.js"></script>


    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css">
    <link rel="stylesheet" href="assets/css/responsive.css">

</head>
<body>

    <div class="top_bar">
        <div class="logo">
            <a href="index.php">ZyChat</a>
        </div>

        <div class="search">
            <form action="search.php" method="get" name="search_form" id="search_form_submit">
                <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn;?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">

                <div class="button_holder">
                    <img src="assets/images/icons/magnifying_glass.png" alt="">
                </div>
            </form>

            <div class="search_result">

            </div>

            <div class="search_result_footer_empty">

            </div>
        </div>

        <nav class="menu_show">
            <?php
            // un read messages
            $messages = new Message($con, $userLoggedIn);
            $num_messages = $messages->getUnreadNumber();

            // un read notifications
            $notify = new Notification($con, $userLoggedIn);
            $num_notify = $notify->getUnreadNumber();

            // unread requests
            $user_obj = new User($con, $userLoggedIn);
            $num_request = $user_obj->getNumberOfFriendsRequests();
            ?>
            <a href="<?php echo $userLoggedIn; ?>">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php">
                <i class="fa fa-home fa-lg"></i>
            </a>
            <a href="javascript:void(0)" onclick="getDropdownData('<?php echo $userLoggedIn;?>', 'message')">
                <i class="fa fa-envelope fa-lg"></i>
                <?php
                if ($num_messages > 0){
                    echo '<span class="notification_badge" id="unread_message">'.$num_messages.'</span>';
                }
                ?>
            </a>
            <a href="javascript:void(0)" onclick="getDropdownData('<?php echo $userLoggedIn;?>', 'notifications')">
                <i class="fa fa-bell fa-lg"></i>
                <?php
                if ($num_notify > 0){
                    echo '<span class="notification_badge" id="unread_notify">'.$num_notify.'</span>';
                }
                ?>
            </a>
            <a href="requests.php">
                <i class="fa fa-users fa-lg"></i>
                <?php
                if ($num_request > 0){
                    echo '<span class="notification_badge" id="unread_requesets">'.$num_request.'</span>';
                }
                ?>
            </a>
            <a href="settings.php">
                <i class="fa fa-cog fa-lg"></i>
            </a>
            <a href="includes/handlers/logout.php">
                <i class="fa fa-sign-out fa-lg"></i>
            </a>



        </nav>

        <!-- Menu -->
        <div class="menu" onclick="showHideMenu()">
            <div class="line line-1"></div>
            <div class="line line-2"></div>
            <div class="line line-3"></div>
        </div>
        <!-- End of Menu -->

        <div class="dropdwon_data_window" style="height: 0px;
            font-family: 'Bellota-LI', sans-serif;
             background-color: #fff;
             border: none;
             border-radius: 20px;
             width: 300px;
             position: absolute;
             right: 10px;
             top: 40px;
             box-shadow: 0 5px 20px #3498db;
             overflow-y: scroll; transition: height .5s;"></div>
        <input type="hidden" id="dropdown_data_type">

    </div>



    <script>
        var b = true;
        function showHideMenu(){
            if (b){
                $('.menu_show').css({'display': 'flex', 'flex-direction':'column'});
                b=false;
            }else{
                $('.menu_show').css({'display': 'none'});
                b=true;
            }
        }

        var userLoggedIn = '<?php echo $userLoggedIn;?>';

        $(document).ready(function (){

            $('.dropdwon_data_window').scroll(function (){
                var inner_height = $('.dropdwon_data_window').innerHeight();
                var scroll_top = $('.dropdwon_data_window').scrollTop();
                var page = $('.dropdwon_data_window').find('.nextPageDropDownData').val();
                var noMoreData = $('.dropdwon_data_window').find('.noMoreDropdownData').val();

                if((scroll_top + inner_height >= $('.dropdwon_data_window')[0].scrollHeight) && noMoreData === 'false'){

                    var pageName; // holds name of page to send ajax request to
                    var type = $('#dropdown_data_type').val();

                    if (type === 'notifications')
                        pageName = "ajax_load_notifications.php";
                    else if(type === 'message')
                        pageName = "ajax_load_messages.php";

                    var ajaxReq = $.ajax({
                        url: "includes/handlers/"+pageName,
                        type: "POST",
                        data: "page="+page+"&user="+userLoggedIn,
                        cache: false,

                        success: function (data){
                            $('.dropdwon_data_window').find('.nextPageDropDownData').remove();
                            $('.dropdwon_data_window').find('.noMoreDropdownData').remove();

                            $('.dropdwon_data_window').append(data);
                        }
                    });
                }

                return false;
            });


        });
    </script>

    <div class="wrapper">




