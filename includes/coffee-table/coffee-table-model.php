<?php
namespace cm\includes\coffee_table;

class Coffee_Table_Model
{
	/**
	 * @var post_id witch is related to coffee-table event
	 */
    public $post_id;

	/**
	 * @var participant $name
	 */
    public $name;

	/**
	 * @var participant surname
	 */
    public $surname;

	/**
	 * @var participant telephone number
	 */
    public $telephone;

	/**
	 * @var participant email
	 */
    public $email;

    public $address;
    /**
     * @var number of people who can come on coffee table with participant
     */
    public  $otherparticipants = 0;

	/**
	 * @param $meta_value is result of get_meta_data functions
	 * example meta value name-surname-telephonenumber-exapmleemail.com-address-otherparticipants
	 */
    public function set_properties_from_metavalue($meta_value)
    {
        $string = $meta_value->participants;

    	$values = explode('#', $string);

    	$this->set_name($values[0]);
    	$this->set_surname($values[1]);
    	$this->set_email($values[2]);
    	$this->set_telephone($values[3]);
    	$this->set_address($values[4]);
    	$this->set_participants($values[5]);

    }

	/**
	 * save properties of this object as metadata
	 * add timestamp to date to make it unique
	 */
	public function save_as_metavalue_string()
	{
		$tmp_string = $this->name . "#" . $this->surname . "#" . $this->email . "#" . $this->telephone. "#" . $this->address. "#" . $this->otherparticipants;
		$meta_key = '_coffee_table_' . time();
		$result = update_metadata('post', $this->post_id, $meta_key, $tmp_string);

		return $result;
	}

    public function set_post_id( $post_id )
    {
    	$this->post_id = $this->check_if_null( $post_id );
    }

	public function set_name( $name )
	{
		$this->name = $this->check_if_null( $name );
	}

	public function set_surname( $surname )
	{
		$this->surname = $this->check_if_null( $surname );
	}

	public function set_telephone( $telephone )
	{
		$this->telephone = $this->check_if_null( $telephone );
	}

	public function set_email( $email )
	{
		$this->email = $this->check_if_null( $email );
	}

	public function set_address( $address )
    {

        $this->address = $this->check_if_null( $address );
    }

    public function set_participants( $participants )
    {
        if( $this->otherparticipants < $participants ){
            $this->otherparticipants = $participants;
        }
    }

    public function check_if_null( $check )
    {
        if ( ! $check ){
            return '';
        }else{
            return $check;
        }
    }


}