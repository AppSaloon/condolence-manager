<?php

namespace cm\includes\coffee_table\coffee_table_form;


class Form_Filter_Controller
{
    public function __construct()
    {
        add_action('get_footer', array( $this, 'add_form' ));
        add_action('wp_enqueue_scripts', array( $this, 'add_javascript'));
    }

    public function add_form()
    {
       if ( $this->check_page() ){
           global $post;
           include ( CM_DIR . '/includes/coffee-table/coffee-table-form/templates/form-template.php' );
       }
    }

    public function add_javascript()
    {
        if( $this->check_page() ){
            wp_enqueue_script('coffee_table_script', CM_URL . '/includes/coffee-table/coffee-table-form/coffee-table-script.js', array( 'jquery' ), true);
            wp_enqueue_style( 'scoffee_table_stylesheet', CM_URL . '/includes/coffee-table/coffee-table-form/coffee-table-stylesheet.css' );
        }
    }

    private function check_page()
    {
        if( is_single() ){

            global $post;

            $post_id = $post->ID;
            $is_coffee_table = get_post_meta($post_id, 'coffee_table', true);

            if( $is_coffee_table && $is_coffee_table == 'yes'){
               return true;
            }
        }

        return false;
    }

}
