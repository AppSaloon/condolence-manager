jQuery(function ($) {

    function fill_form_for_tests()
    {
         $('#ct_name').val('krzysztof');
         $('#ct_surname').val('test');
         $('#ct_street').val('wlesie');
         $('#ct_city').val('sosnowiec');
         $('#ct_zipcode').val(3530);
         $('#ct_email').val('sejmaks@gmail.com');
         $('#ct_gsm').val(352564);
         $('#ct_more_people').val(1);
         $('#ct_str_number').val(102);
         $('#ct_country').val('Polska');


    }
    // only for tests
    fill_form_for_tests();


    $('#toggle_coffee_table').click(function(){
        $('#ct_div').toggle();
    });

    $('#ct_form').submit(function(e){
        e.preventDefault();

        // disable send button after click

        $('#ct_form_btn').prop('disabled', true);


        var name = $('#ct_name').val();
        var surname = $('#ct_surname').val();
        var street = $('#ct_street').val();
        var number = $('#ct_str_number').val();
        var city = $('#ct_city').val();
        var zipcode = $('#ct_zipcode').val();
        var country = $('#ct_country').val();
        var email = $('#ct_email').val();
        var gsm = $('#ct_gsm').val();
        var post_id = $('#ct_post_id').val();
        var more_people = $('#ct_more_people').val();

        // hide form after submission

        $(this).hide();

        // url for ajax

        var url =   '/wp-admin/admin-ajax.php';

        // data for ajaxcall

        var data =
            {
                'action'      : 'coffee_form_submission',
                'name'        : name,
                'surname'     : surname,
                'street'      : street,
                'number'      : number,
                'city'        : city,
                'zipcode'     : zipcode,
                'country'     : country,
                'email'       : email,
                'gsm'         : gsm,
                'post_id'     : post_id,
                'more_people' : more_people,
            };

        $.post( url, data, function( response ){

            // append response from backend

            $('#ct_form_message').append(response);

        }, 'JSON');
    });

});