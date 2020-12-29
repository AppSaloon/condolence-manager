<?php

namespace appsaloon\cm\controller;

use appsaloon\cm\register\Custom_Post_Type;
use appsaloon\cm\settings\Admin_Options_Page;

class Templates {

	public function __construct() {
		add_filter( 'template_include', array( $this, 'cm_template_chooser' ) );
	}

	public function cm_template_chooser( $template ) {

		// Post ID
		global $post;
		$post_id = get_the_ID();

		if ( $post_id ) {
			if ( has_shortcode( $post->post_content, 'condolence_overview' ) ) {
				wp_enqueue_style( 'condolence-css', CM_URL . 'assets/css/condolence.css', null, CM_VERSION );
			}

			// For all other CPT
			if ( get_post_type( $post_id ) != Custom_Post_Type::post_type() ) {
				return $template;
			}
		}

		// Else use custom template
		if ( is_single() ) {
			return $this->cm_get_template_hierarchy( 'single' );
		}

		if ( is_archive() ) {
			return $this->cm_get_template_hierarchy( 'archive' );
		}

		return $template;
	}

	/**
	 * @param string $template
	 * @param bool $hook_css
	 * @return string
	 */
	public function cm_get_template_hierarchy( string $template, bool $hook_css = true ) {
		// Get the template slug
		$template_slug = rtrim( $template, '.php' );
		$template      = $template_slug . '.php';
		$templates     = CM_BASE_DIR . '/templates/' . $template;

		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( $theme_file = locate_template( array( 'condolatie-manager-plugin/' . $template, $templates ), false ) ) {
			$file = $theme_file;
		} else {
			$file = CM_BASE_DIR . '/templates/' . $template;
		}

		if ( $hook_css ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'hook_css' ) );
		}

		return $file;
	}

	public function hook_css() {
		// load 1 (one) css for archive & single view, because we have the same html
		wp_enqueue_style( 'condolence-css', CM_URL . 'assets/css/condolence.css', null, CM_VERSION );

		if ( is_singular( Custom_Post_Type::post_type() ) ) {
			wp_register_script( 'jquery', 'http' . ( $_SERVER['SERVER_PORT'] == 443 ? 's' : '' ) . '://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', false, null );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'condolence-single', CM_URL . 'assets/js/template-single.js', array( 'jquery' ), CM_VERSION );
			// Add some parameters for the JS.
			wp_localize_script(
				'condolence-single',
				'cm',
				array(
					'blank_fields' => esc_html__( 'You might have left one of the fields blank, or be posting too quickly', 'cm_translate' ),
					'thanks'       => esc_html__( 'Thanks for your comment. We appreciate your response.', 'cm_translate' ),
					'confirmation' => Admin_Options_Page::get_confirmation_settings(),
					'wait'         => esc_html__( 'Please wait a while before posting your next comment.', 'cm_translate' ),
					'not_send'     => esc_html__( 'Your message is not send. You might have left one of the fields blank.', 'cm_translate' ),
				)
			);
		}
	}
}
