/**
 * Created by miekenijs on 12/02/16.
 */


(function($) {
    $( document ).ready(function() {
        $("#sortable").sortable({
            revert: true
        });
        $("#draggable").draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid"
        });
        $("ul, li").disableSelection();


        $("span#delete").on('click', function(){
            if(!$('ul.hide').hasClass('border')){
                $('ul.hide').addClass('border');
            }
            $('ul.hide').append($(this).parent('li'));
            $(this).parent('li').append('<span id="add">+</span>');
            $(this).remove();
        });

        $('li').on('click','#add', function(){
            $('ul.show').append($(this).parent('li'));
            $(this).parent('li').append('<span id="delete">X</span>');
            $(this).remove();
            if($('ul.border li').length < 1){
                $('ul.hide').removeClass('border');
            }
        });


        $("input.btn-set-fields").on('click', function(e){
            var table = [];
            e.preventDefault();
            $( "ul.show li.ui-sortable-handle" ).each(function( index ) {
                var text = $( this ).text();
                table.push( text.substr(0, text.length -1) );
            });
            var dataSet = JSON.stringify(table);
            $.ajax(
                {
                    url: dragAndDrop.ajaxUrl,
                    type: "POST",
                    data: {
                        tableArray: table,
                        action: 'set_fields'
                    },
                    success: function (result) {
                    }
                }
            );
        });

        $('#btn-migrating').on('click', function(e){
            e.preventDefault();
            var max_posts = $('#max_posts').val();

            if( max_posts != 0 ){
                // function migrate post
                migrate_post(max_posts);
            }
        });

    });

    function migrate_post(max_posts, processed_posts){
        if(processed_posts === undefined) { processed_posts = 0; }

        if( max_posts != processed_posts ){
            migrate_post_ajax(max_posts, processed_posts);
        }
    }

    function migrate_post_ajax(max_posts, processed_posts){
        $.ajax(
            {
                url: dragAndDrop.ajaxUrl,
                type: "POST",
                data: {
                    action: 'migrate_post'
                },
                success: function (result) {
                    console.log( processed_posts );
                    migrate_post(max_posts, processed_posts + 1);
                }
            }
        );
    }

})(jQuery);
