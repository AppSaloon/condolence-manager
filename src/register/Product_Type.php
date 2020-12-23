<?php

namespace appsaloon\cm\register;

use appsaloon\cm\model\Price;
use appsaloon\cm\model\Product;
use appsaloon\cm\settings\Admin_Options_Page;

class Product_Type {
	const POST_TYPE = 'cm_product';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_product_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_product_metadata' ) );

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
          th#featured_image,
          td.column-featured_image {
              width: 50px;
              overflow: hidden;
          }

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
				'cb'             => '<input type="checkbox" />',
				'featured_image' => __( 'Image', 'cm_translate' ),
				'title'          => __( 'Product', 'cm_translate' ),
				'cm_price'       => __( 'Price', 'cm_translate' ),
				'date'           => __( 'Date' )
		);
	}

	public function column_content( $column, $post_id ) {
		$product = Product::from_id( $post_id );

		switch ( $column ) {
			case 'cm_price':
				echo $product->get( 'price' )->display();
				break;
			case 'featured_image':
				the_post_thumbnail( 'thumbnail', array( 'style' => 'max-height: 50px; width: auto;' ) );
				break;
			default:
		}
	}

	public function register_post_type() {
		// Set UI labels for Custom Post Type
		$labels = array(
				'name'               => _x( 'Products', 'Post Type General Name', 'cm_translate' ),
				'singular_name'      => _x( 'Product', 'Post Type Singular Name', 'cm_translate' ),
				'menu_name'          => __( 'Products', 'cm_translate' ),
				'parent_item_colon'  => __( 'Parent Product', 'cm_translate' ),
				'all_items'          => __( 'Products', 'cm_translate' ),
				'view_item'          => __( 'View Product', 'cm_translate' ),
				'add_new_item'       => __( 'Add New Product', 'cm_translate' ),
				'add_new'            => __( 'Add New', 'cm_translate' ),
				'edit_item'          => __( 'Edit Product', 'cm_translate' ),
				'update_item'        => __( 'Update Product', 'cm_translate' ),
				'search_items'       => __( 'Search Product', 'cm_translate' ),
				'not_found'          => __( 'Not Found', 'cm_translate' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'cm_translate' ),
		);

		// Set other options for Custom Post Type
		$args = array(
				'label'               => _x( 'Products', 'Post Type Label Name', 'cm_translate' ),
				'description'         => _x( 'Products', 'Post Type Description', 'cm_translate' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'thumbnail', 'editor' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => Admin_Options_Page::MENU_SLUG,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 2,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest'        => true
		);

		register_post_type( static::POST_TYPE, $args );
	}

	public function add_product_metaboxes() {
		$screens = array( static::POST_TYPE );

		foreach ( $screens as $screen ) {
			add_meta_box(
					'cm_product_price',
					__( 'Product price', 'cm_translate' ),
					array( $this, 'price_metabox_content' ),
					$screen,
					'side',
					'high'
			);
		}
	}

	public function save_product_metadata( $post_id ) {
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

		if ( is_wp_error( $post ) || $post->post_type !== static::POST_TYPE ) {
			return;
		}

		$product = Product::from_id( $post_id );
		$product->set_fields_from_input( $_POST );

		$errors = $product->validate();
		if ( empty( $errors ) ) {
			$product->update();
		}
	}

    /**
     * @param int $post_id
     */
    public static function set_sortable_price_metadata(int $post_id)
    {
        $price = json_decode(get_post_meta($post_id, 'cm_product_price', true));
        $amount = isset($price->amount) ? (float) $price->amount : 0;
        update_post_meta($post_id, 'cm_product_price_amount', $amount);
    }

	/**
	 * Price metabox
	 */
	public function price_metabox_content( $post ) {
		$product = Product::from_id( $post->ID );

		echo $product->get_property_html( 'price' );
	}
}
