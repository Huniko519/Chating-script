$(document).ready(function() {

    $(".e1").click(function(event){
        var client = $('.chat.active-chat').attr('client');

        var prevMsg = $('#chatFrom .chatboxtextarea').val();
        var shortname = $(this).data('shortname');

        $('#chatFrom .chatboxtextarea').val(prevMsg+' '+shortname+' ');
        //$('#chatFrom .chatboxtextarea').focus();
    });
    $(".chat-head .personName").click(function(){
        var personName = $(this).text();
    });



    $("#launchProfile").click(function(){
        var usname = $(this).find('img').attr('alt');

        $('#wchat .wchat').removeClass('two');
        $('#wchat .wchat').addClass('three');
        $('.wchat-three').slideDown(50);
        $('.wchat-three').toggleClass("shw-rside");

        $("#userProfile").html('<div class="preloader"><div class="cssload-speeding-wheel"></div></div>');
        userProfile(usname);
    });

    $(".header-close").click(function(){
        $('#wchat .wchat').removeClass('three');
        $('#wchat .wchat').addClass('two');
        $('.wchat-three').css({'display':'none'});

    });

    $(".scroll-down").click(function(){
        scrollDown();
    });

    $("#mute-sound").click(function(){
        if(eval(localStorage.sound)){
            localStorage.sound = false;
            $("#mute-sound").html('<i class="icon icon-volume-off"></i>');
        }
        else{
            localStorage.sound = true;
            $("#mute-sound").html('<i class="icon icon-volume-2"></i>');
            audiomp3.play();
            audioogg.play();
        }
    });
    $("#MobileChromeplaysound").click(function(){
        if(eval(localStorage.sound)){
            audiomp3.play();
            audioogg.play();
        }
    });
    if(eval(localStorage.sound)){
        $("#mute-sound").html('<i class="icon icon-volume-2"></i>');
    }
    else{
        $("#mute-sound").html('<i class="icon icon-volume-off"></i>');
    }

    //For Mobile on keyboard show/hide

    /*var _originalSize = $(window).width() + $(window).height()
    $(window).resize(function(){
        if($(window).width() + $(window).height() != _originalSize){
            //alert("keyboard show up");
            $(".target-emoji").css({'display':'none'});
            $('.wchat-filler').css({'height':0+'px'});

        }else{
            //alert("keyboard closed");
            $('#chatFrom .chatboxtextarea').blur();
        }
    });*/
});


function ShowProfile() {
    var usname = $('.right .top').attr("data-user");
    $('#wchat .wchat').removeClass('two');
    $('#wchat .wchat').addClass('three');
    $('.wchat-three').slideDown(50);
    $('.wchat-three').toggleClass("shw-rside");

    $("#userProfile").html('<div class="preloader"><div class="cssload-speeding-wheel"></div></div>');
    userProfile(usname);
}

function chatemoji() {
    $(".target-emoji").slideToggle( 'fast', function(){

        if ($(".target-emoji").css('display') == 'block') {
            //alert($(window).height());
            //$('.chat-list').css({'height':(($(window).height())-279)+'px'});
            $('.wchat-filler').css({'height':225+'px'});
            $('.btn-emoji').removeClass('ti-face-smile').addClass('ti-arrow-circle-down');
        } else {
            //$('.chat-list').css({'height':(($(window).height())-179)+'px'});
            $('.wchat-filler').css({'height':0+'px'});
            $('.btn-emoji').removeClass('ti-arrow-circle-down').addClass('ti-face-smile');
        }
    });
    var heit = $('#resultchat').css('max-height');
}

function typePlace() {

    if(!$('#textarea').html() == '')
    {
        $(".input-placeholder").css({'visibility':'hidden'});
    }
    else{
        $(".input-placeholder").css({'visibility':'visible'});
    }

}

/*Get get on scroll
$("#resultchat").scrollTop($("#resultchat")[0].scrollHeight);
Assign scroll function to chatBox DIV*/
$('.wchat-chat-msgs ').scroll(function(){
    if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
        $(".scroll-down").css({'visibility':'hidden'});
    }
    if ($('.wchat-chat-msgs ').scrollTop() == 0){

        $(".scroll-down").css({'visibility':'visible'});

        var client = $('.chat.active-chat').attr('client');

        if($("#chatbox_"+client+" .pagenum:first").val() != $("#chatbox_"+client+" .total-page").val()) {

            $('#loader').show();
            var pagenum = parseInt($("#chatbox_"+client+" .pagenum:first").val()) + 1;

            var URL = siteurl+'chat.php?page='+pagenum+'&action=get_all_msg&client='+client;

            get_all_msg(URL);                                       // Calling get_all_msg function

            $('#loader').hide();									// Hide loader on success

            if(pagenum != $("#chatbox_"+client+" .total-page").val()) {
                setTimeout(function () {										//Simulate server delay;

                    $('.wchat-chat-msgs').scrollTop(100);							// Reset scroll
                }, 458);
            }
        }

    }
});
/*Get get on scroll*/

//Inbox User search
$(document).ready(function(){
    $('.live-search-list li').each(function(){
        $(this).attr('data-search-term', $(this).text().toLowerCase());
    });

    $('.live-search-box').on('keyup', function(){
        var searchTerm = $(this).val().toLowerCase();
        $('.live-search-list li').each(function(){

            if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

$(window).bind("load", function() {
    $('.person:first').trigger('click');
    var personName = $('.person:first').find('.personName').text();
    $('.right .top .personName').html(personName);
    //$('.right .top').attr("data-user",personName);
    var userImage = $('.person:first').find('.userimage').html();
    $('.right .top .userimage').html(userImage);
    var personStatus = $('.person:first').find('.personStatus').html();
    $('.right .top .personStatus').html(personStatus);
    var hideContent = $('.person:first').find('.hidecontent').html();
    $('.right .hidecontent').html(hideContent);

    /*$('[contenteditable]').on('paste',function(e) {
        e.preventDefault();
        var text = (e.originalEvent || e).clipboardData.getData('text/plain')
        document.execCommand('insertText', false, text);
    });
*/
    $('.chatboxtextarea').on('focus',function(e) {
        $(".target-emoji").css({'display':'none'});
        $('.wchat-filler').css({'height':0+'px'});
    });
});


$('.left .person').mousedown(function(){
    if ($(this).hasClass('.active')) {
        return false;
    } else {
        var findChat = $(this).attr('data-chat');
        var personName = $(this).find('.personName').text();
        $('.right .top .personName').html(personName);
        //$('.right .top').attr("data-user",personName);
        var userImage = $(this).find('.userimage').html();
        $('.right .top .userimage').html(userImage);
        var personStatus = $(this).find('.personStatus').html();
        $('.right .top .personStatus').html(personStatus);
        var hideContent = $(this).find('.hidecontent').html();
        $('.right .hidecontent').html(hideContent);
        $('.chat').removeClass('active-chat');
        $('.left .person').removeClass('active');
        $(this).addClass('active');
        $('.chat[data-chat = '+findChat+']').addClass('active-chat');
    }
});


//Uploading Image And files
// Initialize the widget when the DOM is ready
$(".uploadFile").click(function(){

    $(function() {
        var touname = $('.chat.active-chat').attr('client');

        $("#uploader").plupload({
            // General settings
            runtimes : 'html5,flash,silverlight,html4',
            url: siteurl+"upload.php?tun="+touname,

            // User can upload no more then 20 files in one go (sets multiple_queues to false)
            max_file_count: 10,

            chunk_size: '1mb',

            // Resize images on clientside if we can
            /*resize : {
             width : 200,
             height : 200,
             quality : 90,
             crop: false // crop to exact dimensions
             },*/

            filters : {
                // Maximum file size
                max_file_size : '10mb',
                // Specify what files to browse for
                mime_types: [
                    {title : "Image files", extensions : "jpg,gif,png"},
                    {title : "Zip files", extensions : "zip,rar,mp3,mp4,txt,doc,docx,pdf,ppt,psd,xls,xlsx,xml"}
                ]
            },

            // Rename files by clicking on their titles
            rename: false,

            // Sort files
            sortable: true,

            // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
            dragdrop: true,

            // Views to activate
            views: {
                list: true,
                thumbs: true, // Show thumbs
                active: 'thumbs'
            },

            // Flash settings
            flash_swf_url : 'plugins/uploader/Moxie.swf',

            // Silverlight settings
            silverlight_xap_url : 'plugins/uploader/Moxie.xap',

            init: {

                FileUploaded: function(up, file, info) {
                    // Called when file has finished uploading
                    log('[FileUploaded] File:', file, "Info:", info);
                },
                Destroy: function(up) {
                    // Called when uploader is destroyed
                    //log('[Destroy] ');
                },
                Error: function(up, err) {
                    document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                }
            }

        });

        $('#close_uploadFile').click(function(){
            $('#uploader').plupload('destroy');
            $('#uploader').css({'display':'none'});
            //console.clear();
        });
        $('#uploader').on('complete', function() {
            $('#uploader').plupload('destroy');
            $('#uploader').css({'display':'none'});
            //console.clear();
        });
    });
    $('#uploader').css({'display':'block'});
});
function log() {
    plupload.each(arguments, function(arg) {
        if (typeof(arg) != "string") {
            plupload.each(arg, function(value, key) {
                if (typeof(value) != "function") {
                    if(key == "response"){
                        var json_var = JSON.parse(value);
                        var id = json_var.id;
                        var toName = json_var.toName;
                        var username = json_var.username;
                        var picname = json_var.picname;
                        var file_name = json_var.file_name;
                        var file_path = json_var.file_path;
                        var file_type = json_var.file_type;

                        if (file_type == "image"){
                            var message_content = "<a url='"+file_path+"' onclick='trigq(this)' style='cursor: pointer;'><img src='"+file_path+"' class='userfiles'/></a>";
                        }
                        else if(file_type == "video"){
                            message_content = '<video class="userfiles" controls>' +
                            '<source src="'+file_path+'" type="video/mp4">'+
                            'Your browser does not support HTML5 video.'+
                            '</video>';
                            // message_content = "<a href='"+fileUPath+getfilename+"' class='download-link'></a>";
                        }
                        else{
                            message_content = "<a href='"+file_path+"' class='download-link' download></a>";
                        }
                        $("#chatbox_"+toName).append('<div class="col-xs-12 p-b-10 odd">' +
                        '<div class="chat-image">' +
                        '<img alt="male" src="'+siteurl+'storage/user_image/'+picname+'">' +
                        '</div>' +
                        '<div class="chat-body">' +
                        '<div class="chat-text">' +
                        '<h4>'+username+'</h4>' +
                        '<p>'+message_content+'</p>' +
                        '<b>Just Now</b><span class="msg-status msg-'+toName+'"><i class="fa fa-check"></i></span>' +
                        '</div>' +
                        '</div>' +
                        '</div>');

                        /*clearTimeout(chkSeenInterval);
                        chkSeenInterval = setTimeout('checkMsgSeen('+id+',"'+toName+'");',3000);*/

                    }
                }
            });
        } else {

        }
    });
}



/*
function start() {
    add = setInterval("chatfrindList();",3000);
}start();*/
