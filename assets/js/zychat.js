$(document).ready(function (){
    // search button header
    $("#search_text_input").focus(function (){
        // console.log('focus');
        if (window.matchMedia("(max-width: 1000px)").matches){
            $(this).animate({width:'250px'}, 500);
        }
    });
    $('.button_holder').on('click', function (){
        console.log('hi');
        document.search_form.submit();
    });
    // //// end of  search button header

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

    // menu


});

$(document).click(function (e){

    if (e.target.class != "search_result" && e.target.id != "search_text_input"){
        $(".search_result").html("");
        $('.search_result_footer').html("");
        $('.search_result_footer').toggleClass("search_result_footer_empty");
        $('.search_result_footer').toggleClass("search_result_footer");
    }

    if (e.target.class != "dropdwon_data_window"){
        $(".dropdwon_data_window").html("");
        $(".dropdwon_data_window").css({"padding":"0px", "height":"0px", "border": "none"})
    }
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

function getLiveSearchUsers(value, user){
    $.post("includes/handlers/ajax_search.php", {query: value, userLoggedIn: user}, function (data){

        if ($(".search_result_footer_empty")[0]){
            $(".search_result_footer_empty").toggleClass("search_result_footer");
            $(".search_result_footer_empty").toggleClass("search_result_footer_empty");
        }

        $('.search_result').html(data);
        $('.search_result_footer').html("<a href='search.php?q="+value+"'>See All Results</a>");

        if (data == ""){
            $('.search_result_footer').html("");
            $('.search_result_footer').toggleClass("search_result_footer_empty");
            $('.search_result_footer').toggleClass("search_result_footer");
        }

    });
}





