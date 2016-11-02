<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Controller
{
	const headers = array('name', 'surname', 'gsm', 'email');

    function __construct()
    {
	    add_filter('gform_after_submission', array( $this, 'gf_data_saver' ));
	    add_action('init', array($this, 'download_csv_by_id'));

    }

	/**
	 * if receive request data from menu page
	 * download csv by id
	 */
	public function download_csv_by_id()
	{
		$check = ( isset( $_REQUEST['btn_koffie_tafel_csv'] ) ) ? true : false;

		if ( $check ){
			$id = $_REQUEST['post_ID'];
			$this->download_csv($id);
		}
	}

	/**
	 * @param $content
	 *
	 * @return mixed
	 * save data from gravity form as meta value string
	 */
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
			unset($participant);

		}

        return $content;
    }

	/**
	 * @param $id
	 *
	 * @return array|null|object
	 * find all participants of koffie tafel related to post_id
	 */
    public function all_participants_by_id($id)
    {
    	global $wpdb;

    	$query = "SELECT meta_value as participants FROM " . $wpdb->postmeta . " WHERE meta_key LIKE '_koffie_tafel%'  AND post_id='" . $id . "'";
    	$result = $wpdb->get_results($query);

    	return $result;

    }

	/**
	 * @param $query_result
	 * @param null $id
	 *
	 * @return array
	 * proccesing result of query into objects kofie-tafel-model
	 * and return in array
	 */
    public function result_to_array_objects($query_result, $id = null)
    {

    	$participants_array = array();
	    foreach ($query_result as $object){
			$participant = new Koffie_Tafel_Model();
			if( $id ){
				$participant->set_post_id( $id );
			}
			$participant->set_properties_from_metavalue($object);

		    $participants_array[] = $participant;
		    unset ( $participant );
	    }

	    return $participants_array;
    }

	/**
	 * @return array|null|object
	 * find all posts with koffie tafel option
	 */
    public function all_koffie_posts()
    {
	    global $wpdb;
	    $query = "SELECT post_id as ID FROM " . $wpdb->postmeta . " WHERE meta_key = 'koffie_tafel'  AND meta_value = 'ja'  ";
	    $result = $wpdb->get_results($query);

	    $tmp_arary = array();
	    foreach ( $result as $ID ){
	    	$tmp_arary[] = $ID->ID;
	    }
	    $result = $tmp_arary;
	    unset($tmp_arary);

	    return $result;
    }
    public function download_csv($id = null)
    {
    	if ( $id ){
		    $result = $this->all_participants_by_id($id);
		    $array = $this->result_to_array_objects($result);

		    $data = array();

		    foreach ( $array as $obj ){
			    $tmp_array = array();

				$tmp_array[] = $obj->name;
			    $tmp_array[] = $obj->surname;
			    $tmp_array[] = $obj->telefon;
			    $tmp_array[] = $obj->email;
			    $data[] = $tmp_array;

			    unset($tmp_array);
		    }

		    $this->export_csv(static::headers, $data);
	    }

    }

	public function export_csv($headers, $data, $filename = 'koffie tafel'){
		header('Content-Description: File Transfer');
		header('Content-Encoding: UTF-8');
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename='.$filename.'.csv');
		header('Content-Transfer-Encoding: binary');
		echo "\xEF\xBB\xBF";

		$output = fopen('php://output', 'w');

		fputcsv($output, $headers);

		foreach( $data as $fields ){
			fputcsv($output, $fields);
		}

		fclose($output);
		die;
	}

}