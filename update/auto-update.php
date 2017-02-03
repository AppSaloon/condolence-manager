<?php
namespace cm\update;

use cm\update\classes\bb\Arpu_Bitbucket_Plugin_Updater;

if ( ! defined( 'ARPU_DIR' ) ) {
    define( 'ARPU_DIR', dirname(__FILE__).'/' );
}

Class Auto_Update{

    public function __construct()
    {
        add_action( 'admin_init', array($this, 'arpu_bb_handle_updates') );
    }

    public function arpu_bb_handle_updates(){
        $bb_plugin = array(
            'plugin_file' => CM_DIR . 'condolatie-manager.php',
            'bb_host' => 'https://api.bitbucket.org',
            'bb_download_host' => 'http://bitbucket.org',
            'bb_owner' => 'appsaloonupdater',
            'bb_password' => 'aLdNmRqZwVvL32',
            'bb_project_name' => 'appsaloon',
            'bb_repo_name' => 'condolatie-manager-plugin'
        );

        if( $this->git_repository_is_live($bb_plugin) && $this->licensekey_is_valid()){
            new Arpu_Bitbucket_Plugin_Updater( $bb_plugin );
        }
    }

    public function git_repository_is_live($url){
        $headers = array( 'Authorization' => 'Basic ' . base64_encode( $url["bb_owner"].":".$url['bb_password'] ) );
        $new_url = $url['bb_host']."/2.0/repositories/".$url['bb_project_name']."/".$url['bb_repo_name'];

        $request = wp_remote_get($new_url, array( 'headers' => $headers ));

        if( !is_wp_error($request) && $request['response']['code'] == 200 ){
            return true;
        }

        return false;
    }

    private function licensekey_is_valid(){
        $license_key = get_option('license_key_cm', false);

        if( $license_key ){
            return true;
        }

        return false;
    }
}