<?php

namespace cm\includes\register;

use cm\includes\comments\Inline_Comment_Error;
use cm\includes\form\Metabox;

class Location {
	const POST_TYPE = 'location';

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_address_metabox' ) );
		add_action( 'save_post', array( $this, 'save_address_metadata' ) );
	}

	public function register_post_type() {
		// Set UI labels for Custom Post Type
		$labels = array(
				'name'               => _x( 'Locations', 'Post Type General Name', 'cm_translate' ),
				'singular_name'      => _x( 'Location', 'Post Type Singular Name', 'cm_translate' ),
				'menu_name'          => __( 'Locations', 'cm_translate' ),
				'parent_item_colon'  => __( 'Parent Location', 'cm_translate' ),
				'all_items'          => __( 'All Locations', 'cm_translate' ),
				'view_item'          => __( 'View Location', 'cm_translate' ),
				'add_new_item'       => __( 'Add New Location', 'cm_translate' ),
				'add_new'            => __( 'Add New', 'cm_translate' ),
				'edit_item'          => __( 'Edit Location', 'cm_translate' ),
				'update_item'        => __( 'Update Location', 'cm_translate' ),
				'search_items'       => __( 'Search Location', 'cm_translate' ),
				'not_found'          => __( 'Not Found', 'cm_translate' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'cm_translate' ),
		);

		// Set other options for Custom Post Type
		$args = array(
				'label'               => _x( 'Locations', 'Post Type Label Name', 'cm_translate' ),
				'description'         => _x( 'Locations', 'Post Type Description', 'cm_translate' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'thumbnail', 'editor', 'comments' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest'        => true
		);

		register_post_type( static::POST_TYPE, $args );

		// todo this should not be called every time.
		flush_rewrite_rules();
	}

	public function add_address_metabox() {
		$screens = array( static::POST_TYPE );

		foreach ( $screens as $screen ) {
			add_meta_box(
					'cm_location_address',
					__( 'Address details', 'cm_translate' ),
					array( $this, 'address_metabox_content' ),
					$screen
			);
		}
	}

	public function save_address_metadata( $post_id ) {
		$address_keys = array(
				'cm_location_address_line',
				'cm_location_address_postal_code',
				'cm_location_address_city',
		);

		foreach ( $address_keys as $address_key ) {
			if ( isset( $_POST[$address_key] ) ) {
				update_post_meta(
						$post_id,
						$address_key,
						sanitize_text_field( $_POST[$address_key] )
				);
			}
		}
	}

	public function address_metabox_content(\WP_Post $post) {
		$cm_location_address_line = get_post_meta($post->ID, 'cm_location_address_line', true);
		$cm_location_address_postal_code = get_post_meta($post->ID, 'cm_location_address_postal_code', true);
		$cm_location_address_city = get_post_meta($post->ID, 'cm_location_address_city', true);
		?>
      <div class="form-wrap">
          <label for="cm_location_address_line"><?= __( 'Address line', 'cm_translate' ) ?></label>
          <div class="form-field">
              <input type="text"
                     placeholder="<?= __( 'Street name', 'cm_translate' ) ?>"
                     name="cm_location_address_line"
                     id="cm_location_address_line"
                     value="<?= esc_attr( $cm_location_address_line ) ?>"
              />
          </div>
      </div>
      <div class="form-wrap">
          <label for="cm_location_address_postal_code"><?= __( 'Postal code', 'cm_translate' ) ?></label>
          <div class="form-field">
              <input type="text"
                     placeholder="<?= __( 'Postal code', 'cm_translate' ) ?>"
                     name="cm_location_address_postal_code"
                     id="cm_location_address_postal_code"
                     value="<?= esc_attr( $cm_location_address_postal_code ) ?>"
              />
          </div>
      </div>
      <div class="form-wrap">
          <label for="cm_location_address_city"><?= __( 'City', 'cm_translate' ) ?></label>
          <div class="form-field">
              <input type="text"
                     placeholder="<?= __( 'City', 'cm_translate' ) ?>"
                     name="cm_location_address_city"
                     id="cm_location_address_city"
                     value="<?= esc_attr( $cm_location_address_city ) ?>"
              />
          </div>
      </div>

		<?php
	}
}
