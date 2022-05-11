$(document).ready(function (){

    // Button for profile post
    $("#submit_profile_post").click(function (){
        $.ajax({
            type: "POST",
            url: "includes/handlers/ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),

            success: function (msg){
                $("#popup_post").modal('hide');
                location.reload();
            },
            error: function (){
                alert('Failure');
            }
        });

    });

});

function getUser(value, user){
    $.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function (data) {
        $('.results').html(data);
    });
}

function getDropdownData(user, type){

    if ($(".dropdwon_data_window").css("height") == "0px"){

        var pageName;
        if (type === 'notifications'){
            pageName = "ajax_load_notifications.php";
            $("span").remove("#unread_notify");
        }else if (type === 'message'){
            pageName = "ajax_load_messages.php";
            $("span").remove("#unread_message");
        }

        var ajaxreq = $.ajax({
            url: "includes/handlers/"+pageName,
            type: "POST",
            data: "page=1&user="+user,
            cache:false,

            success: function (response){
                $(".dropdwon_data_window").html(response);
                $(".dropdwon_data_window").css({"padding":"0px", "height":"280px", "border": "1px solid #dadada"});
                $("#dropdown_data_type").val(type);
            }
        });

    }else{
        $(".dropdwon_data_window").html('');
        $(".dropdwon_data_window").css({"padding":"0px", "height":"0", "border": "none"});
    }

}