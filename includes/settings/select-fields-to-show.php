<?php

namespace cm\includes\settings;

class Select_Fields_To_Show{

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'add_admin_page') );
    }

    public function add_admin_page(){
        add_menu_page(__('Condolence manager'), __('Condolence manager'), 'manage_options', 'condolence-manager', array($this, 'my_plugin_function'));
    }

    public function my_plugin_function(){
        ?>

        <h2><?php _e('Condolence manager'); ?></h2>
        <?php
    }
}