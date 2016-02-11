<?php

namespace cm\includes\register;

use cm\includes\form\Metabox;

class Custom_Post_Type{
    const POST_TYPE = 'condolences';

    public function __construct()
    {
        add_action( 'init', array($this, 'register_post_type') );
    }

    public function register_post_type(){
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'Condolences', 'Post Type General Name', 'cm_translate' ),
            'singular_name'       => _x( 'Condolence', 'Post Type Singular Name', 'cm_translate' ),
            'menu_name'           => __( 'Condolences', 'cm_translate' ),
            'parent_item_colon'   => __( 'Parent Condolence', 'cm_translate' ),
            'all_items'           => __( 'All Condolences', 'cm_translate' ),
            'view_item'           => __( 'View Condolence', 'cm_translate' ),
            'add_new_item'        => __( 'Add New Condolence', 'cm_translate' ),
            'add_new'             => __( 'Add New', 'cm_translate' ),
            'edit_item'           => __( 'Edit Condolence', 'cm_translate' ),
            'update_item'         => __( 'Update Condolence', 'cm_translate' ),
            'search_items'        => __( 'Search Condolence', 'cm_translate' ),
            'not_found'           => __( 'Not Found', 'cm_translate' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'cm_translate' ),
        );

        // Set other options for Custom Post Type
        $args = array(
            'label'               => _x( 'Condolences', 'Post Type Label Name', 'cm_translate'),
            'description'         => _x( 'Condolences', 'Post Type Description', 'cm_translate' ),
            'labels'              => $labels,
            'supports'            => array( 'title' ),
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
        );

        register_post_type( static::POST_TYPE, $args );

        $add_meta_boxes = new Metabox();
    }
}