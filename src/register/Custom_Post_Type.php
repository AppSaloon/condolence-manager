<?php

namespace appsaloon\cm\register;

use appsaloon\cm\controller\Templates;
use appsaloon\cm\form\Metabox;
use appsaloon\cm\settings\Admin_Options_Page;

class Custom_Post_Type {
	public $post_type;

	public function __construct() {
		$this->default_value();
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_shortcode( 'condolence_overview', array( $this, 'condolence_shortcode' ) );
	}

	public function default_value() {
		$value = get_option( 'condolence_cpt_base' );

		if ( empty( $value ) ) {
			update_option( 'condolence_cpt_base', 'condolences' );
		}
	}

	public static function post_type() {
		return get_option( 'condolence_cpt_base' );
	}

	public function register_post_type() {
		// Set UI labels for Custom Post Type
		$labels = array(
			'name'               => _x( 'Condolences', 'Post Type General Name', 'cm_translate' ),
			'singular_name'      => _x( 'Condolence', 'Post Type Singular Name', 'cm_translate' ),
			'menu_name'          => __( 'Condolences', 'cm_translate' ),
			'parent_item_colon'  => __( 'Parent Condolence', 'cm_translate' ),
			'all_items'          => __( 'Condolences', 'cm_translate' ),
			'view_item'          => __( 'View Condolence', 'cm_translate' ),
			'add_new_item'       => __( 'Add New Condolence', 'cm_translate' ),
			'add_new'            => __( 'Add New', 'cm_translate' ),
			'edit_item'          => __( 'Edit Condolence', 'cm_translate' ),
			'update_item'        => __( 'Update Condolence', 'cm_translate' ),
			'search_items'       => __( 'Search Condolence', 'cm_translate' ),
			'not_found'          => __( 'Not Found', 'cm_translate' ),
			'not_found_in_trash' => __( 'Not found in Trash', 'cm_translate' ),
		);

		// Set other options for Custom Post Type
		$args = array(
			'label'               => _x( 'Condolences', 'Post Type Label Name', 'cm_translate' ),
			'description'         => _x( 'Condolences', 'Post Type Description', 'cm_translate' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'thumbnail', 'comments' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => Admin_Options_Page::MENU_SLUG,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 1,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rewrite'             => array(
				'slug'       => '',
				'with_front' => false,
			),
			'show_in_rest'        => true,
		);

		register_post_type( static::post_type(), $args );

		flush_rewrite_rules();

		new Metabox();
	}

	public function removeWhitespace( $buffer ) {
		return preg_replace( '~>\s*\n\s*<~', '><', $buffer );
	}

	public function condolence_shortcode( $atts ) {
		if ( ! is_admin() ) {
			$archive_template = ( new Templates() )->cm_get_template_hierarchy( 'archive', true );
			if ( file_exists( $archive_template ) ) {
				include $archive_template;
			}
		}
	}


}
