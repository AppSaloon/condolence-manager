<?php

namespace cm\includes\register;

Class Translation{

    public function __construct()
    {
        add_action( 'plugins_loaded', array($this, 'myplugin_load_textdomain') );
    }

    public function myplugin_load_textdomain(){
        load_plugin_textdomain( 'cm_translate', false, CM_BASE_NAME . '/languages' );
    }
}