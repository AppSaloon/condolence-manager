<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Model
{
    public $post_id;
    public $name;
    public $surname;
    public $telefon;
    public $email;

    public function set_properties_from_metavalue($meta_value)
    {
        $string = $meta_value->participants;

    	$values = explode('-', $string);

    	$this->set_name($values[0]);
    	$this->set_surname($values[1]);
    	$this->set_telefon($values[2]);
    	$this->set_email($values[3]);

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

	public function set_telefon( $telefon )
	{
		$this->telefon = $telefon;
	}

	public function set_email( $emil )
	{
		$this->email = $emil;
	}

	public function save_as_metavalue_string()
	{
		$tmp_string = $this->name . "-" . $this->surname . "-" . $this->email . "-" . $this->telefon;
		$meta_key = '_koffie_tafel_'.time();
		update_metadata('post', $this->post_id, $meta_key, $tmp_string);
	}
}