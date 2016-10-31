<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Controller
{
    function __construct()
    {
	    add_filter('gform_after_submission', array( $this, 'gf_data_saver' ));

    }

    public function gf_data_saver($content)
    {
		if( $content ){
			$name = trim(sanitize_text_field($content[1]));
			$surname = trim(sanitize_text_field($content[2]));
			$email = trim(sanitize_text_field($content[3]));
			$gsm = trim(sanitize_text_field($content[4]));
			$post_id = trim(sanitize_text_field($content[5]));

			$participant = new Koffie_Tafel_Model();
			$participant->set_post_id($post_id);
			$participant->set_name($name);
			$participant->set_surname($surname);
			$participant->set_email($email);
			$participant->set_telefon($gsm);

			$participant->save_as_metavalue_string();

		}

        return $content;
    }
    public function all_participants_by_id($id)
    {
    	global $wpdb;

    	$query = "SELECT meta_value as participants FROM " . $wpdb->postmeta . " WHERE meta_key LIKE '_koffie_tafel%'  AND post_id='" . $id . "'";
    	$result = $wpdb->get_results($query);

    	return $result;


    }
    public function result_to_array_objects($query_result)
    {
    	$tmp_array = array();
	    foreach ($query_result as $object){
			$participant = new Koffie_Tafel_Model();
			$participant->set_properties_from_metavalue($object->participants);
			$tmp_array[] = $participant;


	    }
    }

}