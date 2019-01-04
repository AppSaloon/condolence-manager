<?php

namespace cm\includes\controller;

use cm\includes\form\Metabox;

class Comment_Email{

    public function __construct()
    {
        add_action( 'comment_post', array($this, 'send_comment_email_notification'), 1, 2 );
    }

    public function send_comment_email_notification( $comment_ID, $commentdata ) {
        $comment = get_comment( $comment_ID );
        $postid = $comment->comment_post_ID;

        // mail to family
        $master_email_checked = get_post_meta($postid, 'check_email', true);
        $master_email = get_post_meta($postid, 'email', true);
        if( $master_email_checked === 'check_email' && isset( $master_email ) && is_email( $master_email ) && $comment->parent == 0) {
            $permalink = get_the_permalink($postid);
            $password = get_post_meta($postid, 'password', true);
            $url = (strpos($permalink, '?') !== false) ? $permalink . '&code=' . $password : $permalink . '?code=' . $password;
            $message = '<p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">New <a href="' . $url.'">condolence</a>&nbsp;';
            $message .= 'from '.$comment->comment_author. ':</p>';
            $message .= '<p style="font-size: 16px; font-weight: normal; margin: 16px 0;">'.nl2br($comment->comment_content).'</p>';
            add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
            wp_mail( $master_email, 'New Condolence', $message );
        }

        // mail to person
        if(  $comment->comment_parent != 0) {
            $this->set_comment_status($comment_ID);
            $parent_comment = get_comment( $comment->comment_parent );
            $message = '<p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">'. __("New") .' <a href="' . get_permalink( $postid ) . '">reply</a>&nbsp;';
            $message .= sprintf(__('on your condolence from %s:'), $comment->comment_author).'</p>';
            $message .= '<p style="font-size: 16px; font-weight: normal; margin: 16px 0;">'.nl2br($comment->comment_content).'</p>';
            add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
            wp_mail( $parent_comment->comment_author_email, __('New Comment'), $message );
        }
    }

    private function set_comment_status($comment_ID){
        global $wpdb;

        $query = "UPDATE ".$wpdb->prefix."comments SET comment_approved='1' WHERE comment_ID=".$comment_ID;
        $wpdb->query($query);

        clean_comment_cache($comment_ID);
    }
}