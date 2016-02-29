<?php

namespace cm\includes\controller;

class Templates{

    public function __construct()
    {
        add_filter( 'template_include', array($this, 'cm_template_chooser') );
        add_action( 'comment_post', array($this, 'send_comment_email_notification'), 11, 2 );
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
        // Add some parameters for the JS.
        wp_localize_script(
            'template-single',
            'cm',
            array(
                'blank_fields' => __( 'You might have left one of the fields blank, or be posting too quickly', 'cm_translate'),
                'thanks' => __( 'Thanks for your comment. We appreciate your response.', 'cm_translate'),
                'wait' => __( 'Please wait a while before posting your next comment.', 'cm_translate'),
                'not_send' => __( 'Your message is not send. You might have left one of the fields blank.', 'cm_translate')
            )
        );
    }

    public function send_comment_email_notification( $comment_ID, $commentdata ) {
            $comment = get_comment( $comment_ID );
            $postid = $comment->comment_post_ID;
            $master_email = get_post_meta( $postid, 'email', true);

            // mail to family
            if( isset( $master_email ) && is_email( $master_email ) && $comment->parent == 0) {
                $message = '<p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">New <a href="' . get_permalink( $postid ) . '/?password='.get_post_meta($postid, 'password', true).'">condolence</a>&nbsp;';
                $message .= 'from '.$comment->comment_author. ':</p>';
                $message .= '<p style="font-size: 16px; font-weight: normal; margin: 16px 0;">'.nl2br($comment->comment_content).'</p>';
                add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
                wp_mail( $master_email, 'New Condolence', $message );
            }

            // mail to person
            if( isset( $master_email ) && is_email( $master_email ) && $comment->parent != 0) {
                $parent_comment = get_comment( $comment->parent );
                $message = '<p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">New <a href="' . get_permalink( $postid ) . '">reply</a>&nbsp;';
                $message .= 'on your condolence from '.$comment->comment_author.':</p>';
                $message .= '<p style="font-size: 16px; font-weight: normal; margin: 16px 0;">'.nl2br($comment->comment_content).'</p>';
                add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
                wp_mail( $parent_comment->comment_author_email, 'New Comment', $message );
            }
    }
}