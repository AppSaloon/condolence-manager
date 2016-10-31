<?php
namespace cm\includes\koffie_tafel;

class Koffie_Tafel_Controller
{
    function __construct()
    {
	    add_filter('the_post', array( $this, 'add_button' ));

    }

    function add_button($content)
    {



        return $content;
    }


}