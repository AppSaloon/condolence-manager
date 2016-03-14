<?php

namespace cm\includes\controller;

class Comment_Email{

    public function __construct()
    {
        add_action( 'comment_post', array($this, 'send_comment_email_notification'), 11, 2 );
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
            $this->set_comment_status($comment_ID);
            $parent_comment = get_comment( $comment->parent );
            $message = '<p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">New <a href="' . get_permalink( $postid ) . '">reply</a>&nbsp;';
            $message .= 'on your condolence from '.$comment->comment_author.':</p>';
            $message .= '<p style="font-size: 16px; font-weight: normal; margin: 16px 0;">'.nl2br($comment->comment_content).'</p>';
            add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
            wp_mail( $parent_comment->comment_author_email, 'New Comment', $message );
        }
    }

    private function set_comment_status($comment_ID){
        clean_comment_cache($comment_ID);
    }
}