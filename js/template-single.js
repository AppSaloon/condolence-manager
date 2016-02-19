/**
 * Created by miekenijs on 15/02/16.
 */


(function($) {
$(document).ready(function(){
    $("#toggle_comment").click(function(e){
        e.preventDefault();
        $("div.comments").toggle();
    });
});

})(jQuery);