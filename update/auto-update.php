<?php
namespace cm\update;

use cm\update\classes\stash\Arpu_Stash_Plugin_Updater;

if ( ! defined( 'ARPU_DIR' ) ) {
    define( 'ARPU_DIR', dirname(__FILE__).'/' );
}

Class Auto_Update{

    public function __construct()
    {
        add_action( 'admin_init', array($this, 'arpu_stash_handle_updates') );
    }

    public function arpu_stash_handle_updates(){
        $stash_plugin = array(
            'plugin_file' => CM_DIR . 'condolatie-manager.php',
            'stash_host' => 'git.appsaloon.be',
            'stash_owner' => 'updater',
            'stash_password' => 'sAbhLuwiBr78HoVTRiGG',
            'stash_project_name' => 'AS',
            'stash_repo_name' => 'condolatie-manager-plugin'
        );

        new Arpu_Stash_Plugin_Updater( $stash_plugin );

    }
}