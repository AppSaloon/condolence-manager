/**
 * Created by miekenijs on 15/02/16.
 */


(function($) {
$(document).ready(function(){

    var blank_fields = cm.blank_fields;
    var thanks = cm.thanks;
    var wait = cm.wait;
    var not_send = cm.not_send;


    $("#toggle_comment").click(function(e){
        e.preventDefault();
        $("div.comments").toggle();
    });
    $("#toggle_koffie_tafel").click(function(e){
        e.preventDefault();
        $("#koffie-tafel-form").toggle();
    });

    if($(".comment-form-error-box").length > 0){
        $("div.comments").show();
    }


    var commentform=$('#commentform');
    commentform.prepend('<div id="comment-status" ></div>');
    var statusdiv=$('#comment-status');
    var list ;
    $('a.comment-reply-link').click(function(){
        list = $(this).parent().parent().parent().attr('id');
    });

    commentform.submit(function(){
        if($('textarea#comment').val().length > 0){
            var formdata=commentform.serialize();
            statusdiv.html('<p>Processing...</p>');
            var formurl=commentform.attr('action');
            $.ajax({
                type: 'post',
                url: formurl,
                data: formdata,
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    statusdiv.html('<p class="ajax-error" >'+blank_fields+'</p>');
                },
                success: function(data, textStatus){
                    if(data == "success" || textStatus == "success"){
                       if($('.comments-list').hasClass('family_page')){
                           location.reload();
                       }else{
                           statusdiv.html('<p class="ajax-success" >'+thanks+'</p>');
                           $('.comment-form-comment').hide();
                           $('.error_box').hide();
                       }

                        if($(".comments-list").has("ol.commentlist").length > 0){
                            if(list != null){
                                $('div.rounded').prepend(data);
                            }
                            else{
                                $('ol.commentlist').append(data);
                            }
                        } else{
                            $("#commentsbox").find('div.post-info').prepend('<ol class="commentlist"> </ol>');
                            $('ol.commentlist').html(data);
                        }
                    }else{
                        statusdiv.html('<p class="ajax-error" >'+ wait +'</p>');
                        commentform.find('textarea[name=comment]').val('');
                    }
                }
            });
            return false;
        }
        else if($('textarea#comment').val().length == 0 && $('.comments-list').hasClass('family_page')){
            $.ajax({
                type: 'post',
                url: formurl,
                data: formdata,
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    statusdiv.html('<p class="ajax-error" >'+blank_fields+'</p>');
                },
                success: function(data, textStatus){
                    if(data == "success" || textStatus == "success"){
                            statusdiv.html('<p class="ajax-error" >'+not_send+'</p>');

                    }else{
                        statusdiv.html('<p class="ajax-error" >'+wait+'</p>');
                        commentform.find('textarea[name=comment]').val('');
                    }
                }
            });
            return false;
        }

    });
});



})(jQuery);