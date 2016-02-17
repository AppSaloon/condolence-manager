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
            add_action('wp_enqueue_scripts', array($this, 'hook_css'));
        }

        return $file;
    }

    public function hook_css() {
        wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
        wp_enqueue_script('jquery');
        wp_enqueue_style('template-single', CM_URL . 'css/template-single.css');
        wp_enqueue_script('template-single', CM_URL . 'js/template-single.js');
    }

}