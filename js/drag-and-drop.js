/**
 * Created by miekenijs on 12/02/16.
 */


(function ($) {
    $(document).ready(function () {
        let lastSortableChildren = getSortableChildren();
        function getSortableChildren () {
            return Array.from(document.querySelector('#sortable').children).map((child) => {
                return child.dataset.value
            }).filter((value) => typeof value !== 'undefined')
        }
        function sortableChildrenIsDifferent () {
            const sortableChildren = getSortableChildren();
            return sortableChildren.length !== lastSortableChildren.length || sortableChildren.some((sortableChild, index) => {
                return sortableChild !== lastSortableChildren[index]
            });
        }
        function toggleSubmitButtonDisabled () {
            if(sortableChildrenIsDifferent()) {
                $("input.btn-set-fields").removeAttr('disabled');
            } else {
                $("input.btn-set-fields").attr('disabled', true)
            }
        }

        $("#sortable").sortable({
            revert: true,
            stop: toggleSubmitButtonDisabled
        });
        $("#draggable").draggable({
            connectToSortable: "#sortable",
            helper: "clone",
            revert: "invalid",
        });
        $("ul, li").disableSelection();

        $('li')
          .on('mousedown', '.cm_field_mapping_delete', function (event) {
              if(event.which === 1) {
                  if (!$('ul.hide').hasClass('border')) {
                      $('ul.hide').addClass('border')
                  }
                  $('ul.hide').append($(this).parent('li'))
                  $(this).parent('li').append('<span class="cm_field_mapping_add">+</span>')
                  $(this).remove()
                  toggleSubmitButtonDisabled()
              }
          })
          .on('mousedown', '.cm_field_mapping_add', function () {
            $('ul.show').append($(this).parent('li'));
            $(this).parent('li').append('<span class="cm_field_mapping_delete">X</span>');
            $(this).remove();
            if ($('ul.border li').length < 1) {
                $('ul.hide').removeClass('border');
            }
            toggleSubmitButtonDisabled()
        });


        $("input.btn-set-fields")
          .attr('disabled', true)
          .on('click', function (e) {
            $(this).attr('disabled', true);
            var table = {};
            e.preventDefault();
            $("ul.show li.ui-sortable-handle").each(function (index) {
                var attr = $(this).attr('data-value');
                var text = $(this).text();
                text = text.substr(0, text.length - 1);
                table[attr] = text;
            });

            $.ajax(
                {
                    url: dragAndDrop.ajaxUrl,
                    type: "POST",
                    data: {
                        tableArray: table,
                        action: 'set_fields'
                    },
                    success: function (result) {
                        lastSortableChildren = getSortableChildren()
                    }
                }
            );
        });

        $('#btn-migrating').on('click', function (e) {
            e.preventDefault();
            var max_posts = $('#max_posts').val();

            if (max_posts != 0) {
                // function migrate post
                migrate_post(max_posts);
            }
        });

        $('#btn-posttype').on('click', function (e) {
            e.preventDefault();
            var max_posts = $('#progress_posttype').prop('max');

            if (max_posts !== undefined) {
                migrate_posttype(max_posts, 0);
            } else {
                change_posttype();
            }
        });
    });

    function migrate_post(max_posts, processed_posts) {
        if (processed_posts === undefined) {
            processed_posts = 0;
        }

        if (max_posts != processed_posts) {
            migrate_post_ajax(max_posts, processed_posts);
        }
    }

    function migrate_post_ajax(max_posts, processed_posts) {
        $.ajax(
            {
                url: dragAndDrop.ajaxUrl,
                type: "POST",
                data: {
                    action: 'migrate_post',
                },
                success: function (result) {
                    $('#progress_migrating').val(processed_posts);
                    migrate_post(max_posts, processed_posts + 1);
                }
            }
        );
    }

    function migrate_posttype(max_posts, processed_posts) {
        if (max_posts != processed_posts) {
            migrate_posttype_ajax(max_posts, processed_posts);
        } else {
            change_posttype();
        }
    }

    function migrate_posttype_ajax(max_posts, processed_posts) {
        var old_post_type = $('#old_post_type').val();
        var post_type = $('#post_type').val();

        $.ajax(
            {
                url: dragAndDrop.ajaxUrl,
                type: "POST",
                data: {
                    action: 'migrate_posttype',
                    old_post_type: old_post_type,
                    post_type: post_type
                },
                success: function (result) {
                    $('#progress_posttype').val(processed_posts);
                    migrate_posttype(max_posts, processed_posts + 1);
                }
            }
        );
    }

    function change_posttype() {
        var post_type = $('#post_type').val();

        $.ajax(
            {
                url: dragAndDrop.ajaxUrl,
                type: "POST",
                data: {
                    action: 'change_posttype',
                    post_type: post_type
                },
                success: function (result) {
                    location.reload();
                }
            }
        );
    }

    /**
     * add event for button to add more additional buttons in setting page
     */
    $('.cm_add_btn_btn_js').on('click', function (e) {
        e.preventDefault();
        var btn_html = '<div class="additional_btn_container">' +
            '<p><label>Insert custom button link</label><input name="additional_btn_href[]"' +
            ' placeholder="http://condolencemanager.com/condolences/"' +
            ' value="" type="url"></p>' +
            '<p><label>Insert custom button caption</label><input name="additional_btn_caption[]"' +
            ' value="" placeholder="Click me!" type="text"></p>' +
            '</div>';
        console.log(btn_html);
        $('.btn_pocket').append(btn_html);
    });

})(jQuery);
