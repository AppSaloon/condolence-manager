<?php

namespace cm\includes\register;

use cm\includes\comments\Inline_Comment_Error;
use cm\includes\form\Metabox;

class Location {
    const POST_TYPE = 'location';

    public function __construct()
    {
        add_action( 'init', array($this, 'register_post_type') );
    }

    public function register_post_type(){
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'Locations', 'Post Type General Name', 'cm_translate' ),
            'singular_name'       => _x( 'Location', 'Post Type Singular Name', 'cm_translate' ),
            'menu_name'           => __( 'Locations', 'cm_translate' ),
            'parent_item_colon'   => __( 'Parent Location', 'cm_translate' ),
            'all_items'           => __( 'All Locations', 'cm_translate' ),
            'view_item'           => __( 'View Location', 'cm_translate' ),
            'add_new_item'        => __( 'Add New Location', 'cm_translate' ),
            'add_new'             => __( 'Add New', 'cm_translate' ),
            'edit_item'           => __( 'Edit Location', 'cm_translate' ),
            'update_item'         => __( 'Update Location', 'cm_translate' ),
            'search_items'        => __( 'Search Location', 'cm_translate' ),
            'not_found'           => __( 'Not Found', 'cm_translate' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'cm_translate' ),
        );

        // Set other options for Custom Post Type
        $args = array(
            'label'               => _x( 'Locations', 'Post Type Label Name', 'cm_translate'),
            'description'         => _x( 'Locations', 'Post Type Description', 'cm_translate' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'thumbnail' , 'editor', 'comments'),
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

        new Metabox();

        new Inline_Comment_Error();
    }
}
