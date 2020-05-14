<?php
namespace cm\includes\coffee_table;

class Coffee_Table_Controller
{
	/**
	 * headers for csv file
	 */
	public $headers;

    function __construct( array $headers = array('Name', 'Surname', 'Email', 'Telephone number',  'Address', 'How many people?'))
    {
	    add_action('init', array($this, 'download_csv_by_id'));
        add_action( 'wp_ajax_coffee_form_submission',array($this, 'receive_form_data'));
        add_action( 'wp_ajax_nopriv_coffee_form_submission',array($this, 'receive_form_data'));
        add_action('send_email_to_family', array( $this, 'send_email_to_family' ));
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
        $email =   isset( $_POST['email'] ) ?  sanitize_email($_POST['email']) : '----' ;
        $telephone =  isset( $_POST['gsm'] ) ? sanitize_text_field( $_POST['gsm'] ) : '----';
        $post_id =  sanitize_text_field( $_POST['post_id']);

        $address = $street . ' ' . $str_number . ' ' . $zipcode . ' ' . $city ;
        $more_people =   intval( sanitize_text_field($_POST['more_people']));


        if( $name && $surname  ){

            $participant = new Coffee_Table_Model();
            $participant->set_name($name);
            $participant->set_surname($surname);
            $participant->set_post_id($post_id);
            $participant->set_telephone($telephone);
            $participant->set_email($email);
            $participant->set_address($address);
            $participant->set_participants(intval($more_people));
            $result = $participant->save_as_metavalue_string();
        }

        do_action( 'conman_handle_form', $_POST );

        ob_start();
        if(  $result  ){
	        include ( CM_BASE_DIR . '/templates/coffee_table_submit_success.php' );
        } else {
            include ( CM_BASE_DIR . '/templates/coffee_table_submit_failed.php' );
        }

        $response = ob_get_clean();

        do_action('send_email_to_family', $participant);

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

    private function get_all_participants_in_array( $post_id = null )
    {
        $result = $this->all_participants_by_id( $post_id );
        return  $this->result_to_array_objects( $result );
    }

    /**
     * @param $post_id
     * @return int
     * I use this function to sum all participants from all of
     * email send to coffee table
     */
    public function  get_sum_of_otherparticipants( $post_id )
    {
        $participants = $this->get_all_participants_in_array( $post_id );

        $sum = 0;

        foreach ( $participants as $participant ){
            if( isset( $participant->otherparticipants ) && is_numeric( $participant->otherparticipants ) ){
                $sum += $participant->otherparticipants;
            }
        }

        return $sum;
    }

    /**
     * i use this function to check how many
     * people have already send email for C. T. meeting
     * and how many people will participate in meeting
     */
    public function get_sum_of_posts( $post_id = null )
    {
        if ( ! $post_id ){
            return;
        }

        global $wpdb;

        $number_of_rows = $wpdb->get_var(
            "
            SELECT COUNT(*)
            FROM ". $wpdb->postmeta ."
            WHERE post_id = '".$post_id."'
            AND   meta_key LIKE '_coffee_table_%'      
            "
        );

        return $number_of_rows;
    }

/**
	 * @param null post_id
	 * download csv file when receive correct ID
	 */
    public function download_csv($post_id = null)
    {
    	if ( $post_id ){
		    $array = $this->get_all_participants_in_array( $post_id );

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
	public function export_csv($headers, $data, $filename = 'coffee table')
    {
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        header('Content-Transfer-Encoding: binary');
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        fputcsv($output, $headers);

        foreach ($data as $fields) {
            fputcsv($output, $fields);
        }

        fclose($output);
        die;
    }

    public function send_email_to_family( $participant )
    {
        /**
     * i get extra information about person who passed away
     * because i need this in email
     * email /
     */
        $detail_to_email = $this->get_meta_for_email( intval( $participant->post_id ) );

        if (  empty( $detail_to_email ) ){
            return;
        }

        /**
         * extract 3 varibles to space of this function
         * dead_man_name / dead_man_surname / to
         */
        extract( $detail_to_email );

        include ( CM_BASE_DIR . '/templates/coffee_table_email.php' );
        $body = ob_get_clean();
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail( $to, $subject, $body, $headers );
    }

    public function get_meta_for_email( $post_id = null )
    {
        if ( ! $post_id || ! is_numeric( $post_id )  ){
            error_log('post_id passed as argument to function get_meta_for_email is empty or not numeric');
            return;
        }

        global $wpdb;

        $post_meta_for_email = $wpdb->get_results(
            "
            SELECT meta_key, meta_value
            FROM ". $wpdb->postmeta ."
            WHERE post_id = '".$post_id."'
            AND ( meta_key = 'familyname' OR meta_key = 'coffee_table_email' OR meta_key = 'name' )     
            "
        );

        if ( empty( $post_meta_for_email ) || ! is_array( $post_meta_for_email ) ){
            return;
        }

        $to = '';
        $dead_man_name = '';
        $dead_man_surname = '';

        foreach ( $post_meta_for_email as $details ){
            if ( $details->meta_key == 'coffee_table_email' ){
                $to = $details->meta_value;
            }

            if ( $details->meta_key == 'familyname' ){
                $dead_man_surname = $details->meta_value;
            }

            if( $details->meta_key == 'name' ){
                $dead_man_name = $details->meta_value;
            }
        }

        unset($post_meta_for_email);

        $result = array( 'to' => $to, 'dead_man_name' => $dead_man_name, 'dead_man_surname' => $dead_man_surname );

        return $result;
    }


}