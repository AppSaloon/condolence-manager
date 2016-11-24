<?php
namespace cm\includes\coffee_table;

class Coffee_Table_Controller
{
	/**
	 * headers for csv file
	 */
	public $headers;

    function __construct( array $headers = ['Name', 'Surname', 'Email', 'Telephone number',  'Address', 'How many people?'])
    {
	    add_filter('gform_after_submission', array( $this, 'gf_data_saver' ));
	    add_action('init', array($this, 'download_csv_by_id'));
        add_filter( 'gform_pre_send_email', array( $this, 'to_family_email') );
        add_action( 'wp_ajax_coffee_form_submission',array($this, 'receive_form_data'));
        add_action( 'wp_ajax_nopriv_coffee_form_submission',array($this, 'receive_form_data'));
        $this->headers = $headers;

    }

    /**
     * take data from ajax call and save in database
     * call function to send email to family about participant
     */
    public function receive_form_data()
    {
        $name =  sanitize_text_field($_POST['name']);
        $surname =  sanitize_text_field( $_POST['surname']);
        $street =  sanitize_text_field( $_POST['street'] );
        $str_number = sanitize_text_field( $_POST['number'] );
        $city =  sanitize_text_field( $_POST['city'] );
        $zipcode =  sanitize_text_field( $_POST['zipcode']);
        $country = sanitize_text_field( $_POST['country'] );
        $email =  sanitize_email($_POST['email']);
        $telephone =  sanitize_text_field( $_POST['gsm'] );
        $post_id =  sanitize_text_field( $_POST['post_id']);

        $address = $street . ' ' . $str_number . ' ' . $zipcode . ' ' . $city . ' ' . $country ;
        $more_people =   intval( sanitize_text_field($_POST['more_people']));


        if( $name && $surname && $telephone ){

            $participant = new Coffee_Table_Model();
            $participant->set_name($name);
            $participant->set_surname($surname);
            $participant->set_post_id($post_id);
            $participant->set_telephone($telephone);
            $participant->set_email($email);
            $participant->set_address($address);
            $participant->set_participants($more_people);
            $result = $participant->save_as_metavalue_string();
        }

        ob_start();
        if(  $result  ){
            include ( CM_DIR . '/includes/coffee-table/coffee-table-form/templates/success-submission.php' );
        }else{
            include ( CM_DIR . '/includes/coffee-table/coffee-table-form/templates/failure-submission.php' );
        }

        $response = ob_get_clean();

        wp_send_json($response);
    }

	/**
	 * if receive request data from menu page
	 * download csv by id
	 */
	public function download_csv_by_id()
	{
		$check = ( isset( $_REQUEST['btn_coffee_table_csv'] ) ) ? true : false;

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
			$email = trim(sanitize_text_field($content[4]));
			$gsm = trim(sanitize_text_field($content[3]));
			$post_id = trim(sanitize_text_field($content[5]));

			$address = $content['6.1'] .' '. $content['6.2'] .' '. $content['6.3'] .' '. $content['6.5'] .' '. $content['6.6'];

			$participant = new Coffee_Table_Model();
			$participant->set_post_id($post_id);
			$participant->set_name($name);
			$participant->set_surname($surname);
			$participant->set_email($email);
			$participant->set_telephone($gsm);
			$participant->set_address(trim($address));
			$participant->set_participants($content[7]);


			$participant->save_as_metavalue_string();
			unset($participant);

		}

        return $content;
    }

	/**
	 * @param $id
	 *
	 * @return array|null|object
	 * find all participants of coffee table related to post_id
	 */
    public function all_participants_by_id($id)
    {
    	global $wpdb;

    	$query = "SELECT meta_value as participants FROM " . $wpdb->postmeta . " WHERE meta_key LIKE '_coffee_table%'  AND post_id='" . $id . "'";
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
			$participant = new Coffee_Table_Model();
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
	 * find all posts with coffee table option
	 */
    public function all_coffee_posts()
    {
	    global $wpdb;
	    $query = "SELECT post_id as ID FROM " . $wpdb->postmeta . " WHERE meta_key = 'coffee_table'  AND meta_value = 'yes'  ";
	    $result = $wpdb->get_results($query);

	    $tmp_arary = array();
	    foreach ( $result as $ID ){
	    	$tmp_arary[] = $ID->ID;
	    }
	    $result = $tmp_arary;
	    unset($tmp_arary);

	    return $result;
    }

	/**
	 * @param null $id
	 * download csv file when receive correct ID
	 */
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
			    $tmp_array[] = $obj->telephone;
			    $tmp_array[] = $obj->email;
			    $tmp_array[] = $obj->address;
			    $tmp_array[] = $obj->otherparticipants;
			    $data[] = $tmp_array;

			    unset($tmp_array);
		    }
		    if( ! $data ){
		        return;
            }

		    $this->export_csv( $this->headers , $data );
	    }
    }

	/**
	 * @param $headers
	 * @param $data
	 * @param string $filename
	 * necessary function to download csv file
	 */
	public function export_csv($headers, $data, $filename = 'coffee table'){
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

	public function to_family_email( $email )
    {
        $email_address =  get_post_meta(get_the_ID(), 'coffee_table_email', true);

        if( $email_address ){
            $email['to'] = $email_address;
        }

        return $email;
    }

}