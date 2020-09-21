<?php
/**
 * Plugin Name: Condolence Manager
 * Plugin URI: http://www.appsaloon.be
 * Description: This plugin allows visitors to condole the family of the deceased.
 * Version: 2.6.0
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

namespace appsaloon\cm;


use appsaloon\cm\controller\Comment_Sanitizer;
use appsaloon\cm\controller\Comment_Email;
use appsaloon\cm\controller\Products_Email_Controller;
use appsaloon\cm\controller\Templates;
use appsaloon\cm\register\Custom_Post_Type;
use appsaloon\cm\register\Location_Type;
use appsaloon\cm\register\Order_Type;
use appsaloon\cm\register\Product_Type;
use appsaloon\cm\register\Translation;
use appsaloon\cm\settings\Admin_Options_Page;
use appsaloon\cm\coffee_table\Coffee_Table_Controller;
use appsaloon\cm\coffee_table\coffee_table_form\Form_Filter_Controller;


define( 'CM_BASE_FILE', __FILE__ );
define( 'CM_BASE_DIR', dirname( CM_BASE_FILE ) );
define( 'CM_URL', plugin_dir_url( __FILE__ ));
define( 'CM_DIR', plugin_dir_path( __FILE__ ));
define( 'CM_BASE_NAME', dirname( plugin_basename( __FILE__) ) );
define( 'CM_VERSION', '2.6.0' );

/**
 * Register autoloader to load files/classes dynamically
 */
include_once CM_BASE_DIR . '/vendor/autoload.php';

Class Condolatie_Manager{

    /**
     * @var Templates
     */
    private $templates;

    public function __construct()
    {
        $this->run();
        $this->includes();
    }

    /**
     * Run the plugin
     */
    public function run(){
        $this->templates = new Templates();
        new Comment_Email();
        new Coffee_Table_Controller();

        $this->register();
        $this->settings();
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
            new Products_Email_Controller(
                $this->templates
            );
            new Comment_Sanitizer();
    }

    /**
     * Settings page
     */
    public function settings(){
        new Admin_Options_Page();
    }
    private function includes(){
	    require_once CM_BASE_DIR . '/src/controller/Products.php';
    }
}

 new Condolatie_Manager();

// CREATE LOG TABLE
//register_activation_hook(__FILE__, array('cm\Install', 'run') );
// REMOVE LOG TABLE
//register_deactivation_hook(__FILE__, array('cm\Deinstall', 'run') );
