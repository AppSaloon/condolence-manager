<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Model
{
	/**
	 * @var post_id witch is related to koffie-tafel event
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

	/**
	 * @param $meta_value is result of get_meta_data function
	 * example meta value name-surname-telephonenumber-exapmleemail.com
	 */
    public function set_properties_from_metavalue($meta_value)
    {
        $string = $meta_value->participants;

    	$values = explode('-', $string);

    	$this->set_name($values[0]);
    	$this->set_surname($values[1]);
    	$this->set_telephone($values[2]);
    	$this->set_email($values[3]);

    }

	/**
	 * save properties of this object as metadata
	 * add timestamp to date to make it unique
	 */
	public function save_as_metavalue_string()
	{
		$tmp_string = $this->name . "-" . $this->surname . "-" . $this->email . "-" . $this->telephone;
		$meta_key = '_koffie_tafel_'.time();
		update_metadata('post', $this->post_id, $meta_key, $tmp_string);
	}

    public function set_post_id( $post_id )
    {
    	$this->post_id = $post_id;
    }

	public function set_name( $name )
	{
		$this->name = $name;
	}

	public function set_surname( $surname )
	{
		$this->surname = $surname;
	}

	public function set_telephone( $telephone )
	{
		$this->telephone = $telephone;
	}

	public function set_email( $emil )
	{
		$this->email = $emil;
	}


}