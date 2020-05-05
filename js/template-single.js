/**
 * Created by miekenijs on 15/02/16.
 */

function toggleQueryParameter(param, onlyDelete = false) {
    const query = new URLSearchParams(window.location.search)
    if(query.has(param) || onlyDelete) {
        query.delete(param)
    } else {
        query.append(param, '')
    }
    const queryString = query.toString().replace('=&', '&').replace(/=$/, '')
    const url = `${window.location.href.split('?')[0]}?${queryString}`.replace(/\?$/, '')
    history.pushState({url}, '', url)
}

(function($) {
$(document).ready(function(){

    var blank_fields = cm.blank_fields;
    var thanks = cm.thanks;
    var confirmation = cm.confirmation;
    var wait = cm.wait;
    var not_send = cm.not_send;
    var scrollDuration = 200;


    $("#toggle_comment").click(function(e){
        e.preventDefault();
        toggleQueryParameter('comments')
        $("div.comments").toggle();
    });

    $("#toggle_products").click(function(e){
        e.preventDefault();
        toggleQueryParameter('cm-products')
        toggleQueryParameter('cm-order-form')
        toggleQueryParameter('cm_order_product', true)
        $("div#cm-products").toggle();
        $("div#cm-order-form").toggle();
    });

    $("#toggle_coffee_table").click(function(e){
        e.preventDefault();
        toggleQueryParameter('ct_form')
        $("#coffee-table-form").toggle();
    });

    // Smooth scroll to form ( Coffee table )
    $("#toggle_coffee_table").click(function () {
        $('html, body').animate({
            scrollTop: $("#ct_form").offset().top
        }, scrollDuration);
    });
    // Smooth scroll to form ( condolences )
    $("#toggle_comment").click(function () {
        $('html, body').animate({
            scrollTop: $(".comments").offset().top
        }, scrollDuration);
    });
    // Smooth scroll to flowers list
    $("#toggle_products").click(function () {
        $('html, body').animate({
            scrollTop: $("#cm-products").offset().top
        }, scrollDuration);
    });

    // if exist error message from gform or succes message
    // than show div with that content and hide coffee_table_button
     if ($("#gform_confirmation_wrapper_1").length || $(".validation_error").length ){
         $("##coffee-table-form").show();
         $("#toggle_coffee_table").hide();
     }else{
         $("#toggle_coffee_table").show();
     }

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
                           if(confirmation.type === 'page') {
                               window.location = confirmation.page;
                           } else {
                               statusdiv.html('<p class="ajax-success" >'+confirmation.text+'</p>');
                               $('.comment-form > *:not(#comment-status)').hide();
                               $('.error_box').hide();
                           }
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
