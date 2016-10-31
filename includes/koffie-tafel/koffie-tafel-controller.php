<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Controller
{
    function __construct()
    {
	    add_filter('gform_after_submission', array( $this, 'add_button' ));

    }

    function gf_data_saver($content)
    {

		if( $content ){
			$name = trim(sanitize_text_field($content[1]));
			$surname = trim(sanitize_text_field($content[2]));
			$email = trim(sanitize_text_field($content[3]));
			$gsm = trim(sanitize_text_field($content[4]));
			$post_id = trim(sanitize_text_field($content[5]));


			$tmp_string = $name . "-" . $surname . "-" . $email . "-" . $gsm;

				$meta_key = '_koffie_tafel_'.time();
				update_metadata('post', $post_id, $meta_key, $tmp_string);
				unset ($tmp_string);
		}


        return $content;
    }
	

}