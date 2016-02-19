/**
 * Created by miekenijs on 15/02/16.
 */


(function($) {
$(document).ready(function(){
    $("#toggle_comment").click(function(e){
        e.preventDefault();
        $("div.comments").toggle();
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
            console.log('sjfiewhfow');
            var formdata=commentform.serialize();
            statusdiv.html('<p>Processing...</p>');
            var formurl=commentform.attr('action');
            $.ajax({
                type: 'post',
                url: formurl,
                data: formdata,
                error: function(XMLHttpRequest, textStatus, errorThrown)
                {
                    statusdiv.html('<p class="ajax-error" >You might have left one of the fields blank, or be posting too quickly</p>');
                },
                success: function(data, textStatus){
                    if(data == "success" || textStatus == "success"){
                       if($('.comments-list').hasClass('family_page')){
                           location.reload();
                       }else{
                           statusdiv.html('<p class="ajax-success" >Thanks for your comment. We appreciate your response.</p>');
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
                            console.log(data);
                            $("#commentsbox").find('div.post-info').prepend('<ol class="commentlist"> </ol>');
                            $('ol.commentlist').html(data);
                        }
                    }else{
                        statusdiv.html('<p class="ajax-error" >Please wait a while before posting your next comment</p>');
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
                    statusdiv.html('<p class="ajax-error" >You might have left one of the fields blank, or be posting too quickly</p>');
                },
                success: function(data, textStatus){
                    if(data == "success" || textStatus == "success"){
                            statusdiv.html('<p class="ajax-error" >Your message is not send. You might have left one of the fields blank.</p>');

                    }else{
                        statusdiv.html('<p class="ajax-error" >Please wait a while before posting your next comment</p>');
                        commentform.find('textarea[name=comment]').val('');
                    }
                }
            });
            return false;
        }

    });
});



})(jQuery);