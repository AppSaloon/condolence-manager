<?php
/**
 * Plugin Name: Condolence Manager
 * Plugin URI: http://www.appsaloon.be
 * Description: This plugin allows visitors to condole the family of the deceased.
 * Version: 1.0
 * Text Domain: cm_translate
 * Author: AppSaloon
 * Author URI: http://www.appsaloon.be
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


define( 'CM_BASE_FILE', __FILE__ );
define( 'CM_BASE_DIR', dirname( CM_BASE_FILE ) );
define( 'CM_URL', plugin_dir_url( __FILE__ ));
define( 'CM_DIR', plugin_dir_path( __FILE__ ));
define( 'CM_BASE_NAME', dirname( plugin_basename( __FILE__) ) );

spl_autoload_register( 'cm_autoload' );
function cm_autoload( $class ) {
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

// REGISTER CUSTOM POST TYPE
new cm\includes\register\Custom_Post_Type();
// REGISTER TRANSLATIONS
new cm\includes\register\Translation();
// SETTINGS
new cm\includes\settings\Select_Fields_To_Show();
// TEMPLATE
new cm\includes\controller\Templates();
// COMMENT EMAIL
new cm\includes\controller\Comment_Email();

// CREATE LOG TABLE
//register_activation_hook(__FILE__, array('cm\Install', 'run') );
// REMOVE LOG TABLE
//register_deactivation_hook(__FILE__, array('cm\Deinstall', 'run') );