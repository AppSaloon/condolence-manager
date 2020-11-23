<?php

namespace appsaloon\cm\controller;

use appsaloon\cm\register\Custom_Post_Type;
use appsaloon\cm\settings\Admin_Options_Page;
use WP_Query;

class Search {

	public function register_hooks() {
		add_shortcode( 'cm_search', array( $this, 'shortcode_callback' ) );
		add_action( 'pre_get_posts', array( $this, 'filter_posts' ) );
	}

	public function shortcode_callback( $arguments ) {
		$cm_search_action           = get_post_type_archive_link( Custom_Post_Type::post_type() );
		$cm_search_placeholder_text = apply_filters( 'cm_search_label_text', __( 'Search' ) );
		$cm_search_button_text      = apply_filters( 'cm_search_button_text', __( 'Search' ) );
		$cm_search_value            = isset( $_GET['q'] ) ? $_GET['q'] : '';

		ob_start();
		include CM_BASE_DIR . '/templates/search.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	public function filter_posts( WP_Query $wp_query ) {
		$q = isset( $_GET['q'] ) ? $_GET['q'] : '';
		if ( empty( $q ) ) {
			return;
		}
		if ( $wp_query->get( 'post_type' !== Custom_Post_Type::post_type() ) ) {
			return;
		}
		$wp_query->set(
			'meta_query',
			array(
				'relation' => 'OR',
				array(
					'key'     => 'name',
					'value'   => $q,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'familyname',
					'value'   => $q,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'birthplace',
					'value'   => $q,
					'compare' => 'LIKE',
				),
				array(
					'key'     => 'placeofdeath',
					'value'   => $q,
					'compare' => 'LIKE',
				),
			)
		);
	}
}
