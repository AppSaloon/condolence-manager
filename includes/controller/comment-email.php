<?php

namespace cm\includes\controller;

use cm\includes\form\Metabox;

class Comment_Email{

    public function __construct()
    {
        add_action( 'comment_unapproved_to_approved', array($this, 'send_email_notification_to_family'), 1, 1 );
        add_action( 'comment_post', array($this, 'send_email_notification_to_original_poster'), 1, 1 );
    }

    public function send_email_notification_to_family($comment_ID) {
        $comment = get_comment( $comment_ID );
        $postid = $comment->comment_post_ID;

        $master_email_checked = get_post_meta($postid, 'check_email', true);
        $master_email = get_post_meta($postid, 'email', true);
        if( $master_email_checked === 'check_email' && isset( $master_email ) && is_email( $master_email ) && $comment->parent == 0) {
            $permalink = get_the_permalink($postid);
            $password = get_post_meta($postid, 'password', true);
            $url = (strpos($permalink, '?') !== false) ? $permalink . '&code=' . $password : $permalink . '?code=' . $password;
            ob_start();
            ?>
            <p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">
                New <a href="<?php echo $url; ?>">condolence</a> from: <?php echo $comment->comment_author; ?>
            </p>
            <p style="font-size: 16px; font-weight: normal; margin: 16px 0;">
                <?php echo nl2br($comment->comment_content); ?>
            </p>
            <?php
            $message = ob_get_clean();
            add_filter( 'wp_mail_content_type', function(){ return "text/html"; } );
            wp_mail( $master_email, 'New Condolence', $message );
        }
    }

    public function send_email_notification_to_original_poster( $comment_ID ) {
        $comment = get_comment( $comment_ID );

        if(  $comment->comment_parent != 0) {
            $this->approve_comment($comment_ID);
            $parent_comment = get_comment( $comment->comment_parent );
            ob_start();
            ?>
            <p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">
                <?php echo sprintf(__('New reply on your condolence from %s:'), $comment->comment_author); ?>
            </p>
            <p style="font-size: 16px; font-weight: normal; margin: 16px 0;">
                <?php echo nl2br($comment->comment_content); ?>
            </p>
            <?php
            $message = ob_get_clean();
            add_filter( 'wp_mail_content_type', function(){ return "text/html";} );
            wp_mail( $parent_comment->comment_author_email, __('New Comment'), $message );
        }
    }

    private function approve_comment($comment_ID){
        global $wpdb;

        $query = "UPDATE ".$wpdb->prefix."comments SET comment_approved='1' WHERE comment_ID=".$comment_ID;
        $wpdb->query($query);

        clean_comment_cache($comment_ID);
    }
}