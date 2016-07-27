<?php

namespace cm\includes\controller;

use cm\includes\register\Custom_Post_Type;

class Templates{

    public function __construct()
    {
        add_filter( 'template_include', array($this, 'cm_template_chooser') );
    }

    public function cm_template_chooser( $template ) {

        // Post ID
        $post_id = get_the_ID();

        // For all other CPT
        if ( get_post_type( $post_id ) != Custom_Post_Type::post_type() ) {
            return $template;
        }

        // Else use custom template
        if ( is_single() ) {
            return $this->cm_get_template_hierarchy( 'single' );
        }

        if (is_archive()){
            return $this->cm_get_template_hierarchy( 'archive' );
        }

    }

    public function cm_get_template_hierarchy( $template ) {
        // Get the template slug
        $template_slug = rtrim( $template, '.php' );
        $template = $template_slug . '.php';
        $templates = CM_BASE_DIR . '/includes/templates/' . $template;

        // Check if a custom template exists in the theme folder, if not, load the plugin template file
        if ( $theme_file = locate_template( array( 'condolatie-manager-plugin/' . $template , $templates ), false )) {
            $file = $theme_file;
            add_action('wp_enqueue_scripts', array($this, 'hook_css'));
        }
        else {
            $file = CM_BASE_DIR . '/includes/templates/' . $template;
            add_action('wp_enqueue_scripts', array($this, 'hook_css'));
        }

        return $file;
    }

    public function hook_css() {
        if( is_singular(Custom_Post_Type::post_type()) ){
            wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
            wp_enqueue_script('jquery');
            wp_enqueue_style('condolence-single', CM_URL . 'css/template-single.css');
            wp_enqueue_script('condolence-single', CM_URL . 'js/template-single.js');
            // Add some parameters for the JS.
            wp_localize_script(
                'condolence-single',
                'cm',
                array(
                    'blank_fields' => __( 'You might have left one of the fields blank, or be posting too quickly', 'cm_translate'),
                    'thanks' => __( 'Thanks for your comment. We appreciate your response.', 'cm_translate'),
                    'wait' => __( 'Please wait a while before posting your next comment.', 'cm_translate'),
                    'not_send' => __( 'Your message is not send. You might have left one of the fields blank.', 'cm_translate')
                )
            );
        }

        if( is_archive() ){
            wp_enqueue_style('condolence-archive', CM_URL . 'css/condolence-archive.css');
        }

    }
}