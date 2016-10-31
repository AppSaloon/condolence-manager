<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Controller
{
    function __construct()
    {
	    add_filter('gform_after_submission', array( $this, 'add_button' ));

    }

    function add_button($content)
    {

		var_dump($content);die;

        return $content;
    }


}