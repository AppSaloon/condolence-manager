<?php

namespace cm\includes\register;

use cm\includes\model\Order;
use WP_Post;

class Order_Type {
	const POST_TYPE = 'cm_order';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_order_metaboxes' ) );
		add_action( 'do_meta_boxes', array( $this, 'remove_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_order_metadata' ) );
	}

	private static function is_post_editable( WP_Post $post ) {
		return $post instanceof WP_Post && in_array( $post->post_status, array( 'auto-draft', 'draft' ) );
	}

	public function register_post_type() {
		// Set UI labels for Custom Post Type
		$labels = array(
				'name'               => _x( 'Orders', 'Post Type General Name', 'cm_translate' ),
				'singular_name'      => _x( 'Order', 'Post Type Singular Name', 'cm_translate' ),
				'menu_name'          => __( 'Orders', 'cm_translate' ),
				'parent_item_colon'  => __( 'Parent Order', 'cm_translate' ),
				'all_items'          => __( 'All Orders', 'cm_translate' ),
				'view_item'          => __( 'View Order', 'cm_translate' ),
				'add_new_item'       => __( 'Add New Order', 'cm_translate' ),
				'add_new'            => __( 'Add New', 'cm_translate' ),
				'edit_item'          => __( 'Edit Order', 'cm_translate' ),
				'update_item'        => __( 'Update Order', 'cm_translate' ),
				'search_items'       => __( 'Search Order', 'cm_translate' ),
				'not_found'          => __( 'Not Found', 'cm_translate' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'cm_translate' ),
		);

		// Set other options for Custom Post Type
		$args = array(
				'label'               => _x( 'Orders', 'Post Type Label Name', 'cm_translate' ),
				'description'         => _x( 'Orders', 'Post Type Description', 'cm_translate' ),
				'labels'              => $labels,
				'supports'            => array( 'comments' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'post',
				'show_in_rest'        => false
		);

		register_post_type( static::POST_TYPE, $args );
	}

	public function remove_metaboxes() {
		remove_meta_box( 'commentstatusdiv', static::POST_TYPE, 'normal' );
	}

	public function add_order_metaboxes() {
		$screens = array( static::POST_TYPE );

		foreach ( $screens as $screen ) {
			add_meta_box(
					'cm_order_products',
					__( 'Order', 'cm_translate' ),
					array( $this, 'products_metabox_content' ),
					$screen
			);

			add_meta_box(
					'cm_order_client_details',
					__( 'Order details', 'cm_translate' ),
					array( $this, 'details_metabox_content' ),
					$screen
			);

			add_meta_box(
					'cm_order_condolence',
					__( 'Linked condolence', 'cm_translate' ),
					array( $this, 'condolence_metabox_content' ),
					$screen,
					'side'
			);
		}
	}

	public function save_order_metadata( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
			return;
		}

		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$post = get_post( $post_id );

		if ( is_wp_error( $post ) || $post->post_type !== Order::get_type() ) {
			return;
		}

		$order = Order::from_id( $post_id );
		$order->set_fields_from_input( $_POST );

		if ( $order->validate() ) {
			$order->update();
		}
	}

	/**
	 * Condolence metabox
	 */
	public function condolence_metabox_content( $post ) {
		$order = Order::from_id( $post->ID );
		echo $order->get_property_html( 'deceased_id' );
	}

	public function products_metabox_content( WP_Post $post ) {
		$order = Order::from_id( $post->ID );

		echo $order->render_lines_form();
	}

	public function details_metabox_content( WP_Post $post ) {
		$order = Order::from_id( $post->ID );

		echo $order->render_details_form();
	}
}
