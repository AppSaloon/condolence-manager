<?php
/**
 * Plugin Name: Condolence Manager
 * Plugin URI: http://www.appsaloon.be
 * Description: This plugin allows visitors to condole the family of the deceased.
 * Version: 1.5.2
 * Text Domain: cm_translate
 * Author: AppSaloon
 * Author URI: http://www.appsaloon.be
 * Translators: fr - Jean François Dejonghe
 * License: GPLv2 or later
 */

/*  Copyright 2014	AppSaloon  (email : info@appsaloon.be)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace cm;


use cm\includes\controller\Additional_Buttons_Controller;
use cm\includes\controller\Comment_Email;
use cm\includes\controller\Templates;
use cm\includes\register\Custom_Post_Type;
use cm\includes\register\Location_Type;
use cm\includes\register\Order_Type;
use cm\includes\register\Product_Type;
use cm\includes\register\Translation;
use cm\includes\script\Migrate;
use cm\includes\script\Post_Type;
use cm\includes\settings\Select_Fields_To_Show;
use cm\update\Auto_Update;
use cm\includes\coffee_table\Coffee_Table_Controller;
use cm\includes\coffee_table\coffee_table_form\Form_Filter_Controller;


define( 'CM_BASE_FILE', __FILE__ );
define( 'CM_BASE_DIR', dirname( CM_BASE_FILE ) );
define( 'CM_URL', plugin_dir_url( __FILE__ ));
define( 'CM_DIR', plugin_dir_path( __FILE__ ));
define( 'CM_BASE_NAME', dirname( plugin_basename( __FILE__) ) );
define( 'CM_VERSION', '1.5.2' );


Class Condolatie_Manager{

    public function __construct()
    {
        $this->autoloader();
        $this->run();
        add_action('wp_enqueue_scripts', array($this, 'assets'));
        $this->includes();
    }

    public function autoloader(){
        spl_autoload_register( array($this, 'cm_autoload') );
    }

    public function cm_autoload( $class ) {
        if( strpos( $class, 'cm\\' ) === 0 ) {
            $path = substr( $class, strlen( 'cm\\' ) );
            $path = strtolower( $path );
            $path = str_replace( '_', '-', $path );
            $path = str_replace( '\\', DIRECTORY_SEPARATOR, $path ) . '.php';
            $path = __DIR__ . DIRECTORY_SEPARATOR . $path;

            if ( file_exists( $path ) ) {
                include $path;
            }
        }
    }

    /**
     * Run the plugin
     */
    public function run(){
        $this->register();
        $this->settings();
        $this->scripts();

        new Templates();
        new Comment_Email();
	    new Coffee_Table_Controller();
        new Auto_Update();
        new Additional_Buttons_Controller();
    }

    /**
     * Register:
     * - Custom post type
     * - translation
     */
    public function register(){
            new Custom_Post_Type();
            new Location_Type();
            new Product_Type();
	        new Order_Type();
            new Translation();
            new Form_Filter_Controller();
    }

    /**
     * Settings page
     */
    public function settings(){
        new Select_Fields_To_Show();
    }

    /**
     * scripts:
     * - Migrate from old version to new one
     * - Translate custom post type slug and move the posts
     */
    public function scripts(){
        new Migrate();
        new Post_Type();
    }

    public function assets(){
	    wp_register_style('cm/forms', CM_URL . 'css/forms.css', null, CM_VERSION );
	    wp_register_style('cm/products', CM_URL . 'css/products.css', null, CM_VERSION );
    }
    private function includes(){
	    require_once CM_DIR . '/includes/controller/products.php';
    }
}

 new Condolatie_Manager();

// CREATE LOG TABLE
//register_activation_hook(__FILE__, array('cm\Install', 'run') );
// REMOVE LOG TABLE
//register_deactivation_hook(__FILE__, array('cm\Deinstall', 'run') );
