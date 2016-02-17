/**
 * Created by miekenijs on 15/02/16.
 */


(function($) {
$(document).ready(function(){
    $("#toggle_comment").click(function(e){
        console.log('teslt');
        e.preventDefault();
        $("div.comments").toggle();
    });
});

})(jQuery);