jQuery(document).ready(function ($) {
    $('#generate').on('click', function(e){
        e.preventDefault();

        var password = makeid();

        $('#password').val(password);
    });
});

function makeid()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 10; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}