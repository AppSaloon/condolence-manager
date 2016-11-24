jQuery(function ($) {


    $('#ct_form').submit(function(e){
        e.preventDefault();

        var name = $('#ct_name').val();
        var surname = $('#ct_surname').val();
        var street = $('#ct_street').val();
        var city = $('#ct_city').val();
        var zipcode = $('#ct_zipcode').val();
        var email = $('#ct_email').val();
        var gsm = $('#ct_gsm').val();
        var post_id = $('#ct_post_id').val();

        var url =   '/wp-admin/admin-ajax.php';

        var data =
            {
                'action'  : 'coffe_form_submition',
                'name'    : name,
                'surname' : surname,
                'street'  : street,
                'city'    : city,
                'zipcode' : zipcode,
                'email'   : email,
                'gsm'     : gsm,
                'post_id' : post_id
            };

        $.post( url, data, function( response, status, cos ){
            console.log(response);

        }, 'JSON');
    });

});