<?php
/**
 * Created by PhpStorm.
 * User: sebastian
 * Date: 10/24/17
 * Time: 2:19 PM
 */

namespace cm\includes\controller;


class Additional_Buttons_Controller {

	/**
	 * Additional_Buttons_Controller constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'save_additional_btn_settings' ) );
	}

	/**
	 * settings of additional buttons can be set on condolance manager admin menu page
	 * save settings in option
	 * additional_btn_href is array of all href's of additional buttons
	 * additional_btn_captions is array of all captions that apear as value in submit button on frontend
	 */
	public function save_additional_btn_settings() {
		if ( isset( $_REQUEST['additional_btn_href'] ) && isset( $_REQUEST['additional_btn_caption'] ) ) {
			$tmp_array = array();
			foreach ( $_REQUEST['additional_btn_href'] as $key => $button ) {
				if ( ! empty( $button ) && ! empty( $_REQUEST['additional_btn_caption'][ $key ] ) ) {
					$tmp_array[] = array(
						'caption' => $_REQUEST['additional_btn_caption'][ $key ],
						'href'    => $button
					);
				}
			}
			/**
			 * update option
			 * this option is loaded in archive template
			 */
			update_option( 'cm_additional_btn', $tmp_array );
		}
	}
}