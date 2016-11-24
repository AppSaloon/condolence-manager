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
    }

    fill_form_for_tests();

    $('#ct_form').submit(function(e){
        e.preventDefault();
        $('#ct_form_btn').prop('disabled', true);


        var name = $('#ct_name').val();
        var surname = $('#ct_surname').val();
        var street = $('#ct_street').val();
        var city = $('#ct_city').val();
        var zipcode = $('#ct_zipcode').val();
        var email = $('#ct_email').val();
        var gsm = $('#ct_gsm').val();
        var post_id = $('#ct_post_id').val();
        var more_people = $('#ct_more_people').val();

        $(this).hide();

        var url =   '/wp-admin/admin-ajax.php';

        var data =
            {
                'action'  : 'coffee_form_submission',
                'name'    : name,
                'surname' : surname,
                'street'  : street,
                'city'    : city,
                'zipcode' : zipcode,
                'email'   : email,
                'gsm'     : gsm,
                'post_id' : post_id,
                'more_people' : more_people,
            };

        $.post( url, data, function( response ){
            console.log(response);

        }, 'JSON');
    });

});