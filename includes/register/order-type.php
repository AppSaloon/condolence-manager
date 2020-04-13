<?php

namespace cm\includes\register;

use cm\includes\model\Order;
use cm\includes\settings\Select_Fields_To_Show;
use WP_Post;

class Order_Type {
	const POST_TYPE = 'cm_order';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_order_metaboxes' ) );
		add_action( 'do_meta_boxes', array( $this, 'remove_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_order_metadata' ) );

		$post_type = static::POST_TYPE;
		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'add_columns' ) );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_content' ), 10, 2 );

		add_action( 'admin_head', function () use ( $post_type ) {
			$current_screen = get_current_screen();

			if ( null !== $current_screen && $current_screen->id === "edit-$post_type" ) {
				$this->add_styling();
			}
		} );

	}

	public function add_styling() {
		?>
		<style type="text/css">
			th#cm_price,
			td.column-cm_price {
				width: 100px;
				text-align: right;
			}
		</style>
		<?php
	}

	public function add_columns( $columns ) {
		return array(
			'cb'          => '<input type="checkbox" />',
			'cm_customer' => __( 'Order', 'cm_translate' ),
			'cm_deceased' => __( 'Condolence', 'cm_translate' ),
			'cm_summary'  => __( 'Order summary', 'cm_translate' ),
			'cm_price'    => __( 'Order total', 'cm_translate' ),
			'date'        => __( 'Date' )
		);
	}

	public static function order_customer_link(Order $order) {
	    return sprintf(
		    '<a href="%s"><strong>%s</strong></a>',
		    get_edit_post_link($order->get_id()),
		    $order->get_customer()
	    );;
    }

	public function column_content( $column, $post_id ) {
		$order = Order::from_id( $post_id );

		switch ( $column ) {
			case 'cm_customer':
				echo static::order_customer_link($order);
				break;
			case 'cm_deceased':
				echo get_the_title( $order->get_deceased_id() );
				break;
			case 'cm_summary':
				echo $order->get_summary();
				break;
			case 'cm_price':
				echo $order->get_total()->display(true);
				break;
			default:
		}
	}

	public static function is_order_editable( Order $order ) {
		$post = $order->get_post();

		return !$post instanceof WP_Post || $post->post_status !== 'publish';
	}

	public function register_post_type() {
		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                   => _x( 'Orders', 'Post Type General Name', 'cm_translate' ),
			'singular_name'          => _x( 'Order', 'Post Type Singular Name', 'cm_translate' ),
				'menu_name'          => __( 'Orders', 'cm_translate' ),
				'parent_item_colon'  => __( 'Parent Order', 'cm_translate' ),
				'all_items'          => __( 'Orders', 'cm_translate' ),
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
				'show_in_menu'        => Select_Fields_To_Show::MENU_SLUG,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 10,
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

		if(static::is_order_editable($order)) {
			echo $order->render_lines_form();
			return;
		}

		?>
		<div>
			<h3><?=__('Products', 'cm_translate')?></h3>
			<?= $order->get_summary() ?>
			<h3><?=__('Total', 'cm_translate')?></h3>
			<?= $order->get_total()->display(true) ?>
		</div>
<?php
	}

	public function details_metabox_content( WP_Post $post ) {
		$order = Order::from_id( $post->ID );

		echo $order->render_details_form();
	}
}
