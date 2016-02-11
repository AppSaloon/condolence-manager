<?php

namespace cm\includes\controller;

class Templates{

    public function __construct()
    {
        add_filter( 'template_include', array($this, 'cm_template_chooser') );
    }

    public function cm_template_chooser( $template ) {

        // Post ID
        $post_id = get_the_ID();

        // For all other CPT
        if ( get_post_type( $post_id ) != 'condolences' ) {
            return $template;
        }

        // Else use custom template
        if ( is_single() ) {
            return $this->cm_get_template_hierarchy( 'single' );
        }

    }

    public function cm_get_template_hierarchy( $template ) {

        // Get the template slug
        $template_slug = rtrim( $template, '.php' );
        $template = $template_slug . '.php';

        // Check if a custom template exists in the theme folder, if not, load the plugin template file
        if ( $theme_file = locate_template( array( 'condolence_manager/' . $template ) ) ) {
            $file = $theme_file;
        }
        else {
            $file = CM_BASE_DIR . '/includes/templates/' . $template;
        }

        return $file;
    }

}