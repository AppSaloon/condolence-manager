<?php

namespace cm\includes\controller;

use WP_Comment;

/**
 * Class Comment_Email
 * @package cm\includes\controller
 */
class Comment_Email
{

    /**
     * Comment_Email constructor.
     */
    public function __construct()
    {
        add_action('comment_unapproved_to_approved', array($this, 'send_email_notification_to_family'), 1, 1);
        add_action('comment_post', array($this, 'send_email_notification_to_family'), 1, 1);
        add_action('comment_post', array($this, 'send_email_notification_to_original_poster'), 1, 1);
    }

    /**
     * @param $comment_ID
     */
    public function send_email_notification_to_family($comment_ID)
    {
        $comment = get_comment($comment_ID);
        $post_ID = $comment->comment_post_ID;

        $master_email_checked = get_post_meta($post_ID, 'check_email', true);
        $master_email = get_post_meta($post_ID, 'email', true);
        if ($master_email_checked === 'check_email' && isset($master_email) && is_email($master_email) && $comment->parent == 0 && $comment->comment_approved == 1) {
            $permalink = get_the_permalink($post_ID);
            $password = get_post_meta($post_ID, 'password', true);
            $url = (strpos($permalink, '?') !== false) ? $permalink . '&code=' . $password : $permalink . '?code=' . $password;
            ob_start();
            ?>
            <p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">
                <?php echo sprintf(__('New condolence from %s'), $comment->comment_author); ?>
                <br/>
                <a href="<?php echo $url; ?>">click here to view the condolence</a>
            </p>
            <?php
            self::render_condolence_info($post_ID);
            self::render_comment_content($comment);
            $message = ob_get_clean();
            add_filter('wp_mail_content_type', function () {
                return "text/html";
            });
            wp_mail($master_email, 'New Condolence', $message);
        }
    }

    /**
     * @param $comment_ID
     */
    public function send_email_notification_to_original_poster($comment_ID)
    {
        $comment = get_comment($comment_ID);
        $post_ID = $comment->comment_post_ID;

        if ($comment->comment_parent != 0) {
            self::approve_comment($comment_ID);
            $parent_comment = get_comment($comment->comment_parent);
            ob_start();
            ?>
            <p style="font-size: 22px; font-weight: bold; line-height: 26px; vertical-align: 20px; margin-top: 50px;">
                <?php echo sprintf(__('New reply on your condolence from %s:'), $comment->comment_author); ?>
            </p>
            <?php
            self::render_condolence_info($post_ID);
            self::render_comment_content($parent_comment, $comment);
            $message = ob_get_clean();
            add_filter('wp_mail_content_type', function () {
                return "text/html";
            });
            wp_mail($parent_comment->comment_author_email, __('New Comment'), $message);
        }
    }

    /**
     * @param $comment_ID
     */
    private static function approve_comment($comment_ID)
    {
        global $wpdb;

        $query = "UPDATE " . $wpdb->prefix . "comments SET comment_approved='1' WHERE comment_ID=" . $comment_ID;
        $wpdb->query($query);

        clean_comment_cache($comment_ID);
    }

    /**
     * @param $post_ID
     */
    public static function render_condolence_info($post_ID)
    {
        ?>
        <p style="font-size: 16px; font-weight: normal; margin: 16px 0 0 0; background-color: #FAFAFA; color: #999; padding: 6px;">
            <?php echo get_the_title($post_ID); ?>
        </p>
        <?php
    }

    /**
     * @param WP_Comment $comment
     * @param WP_Comment|null $nested_comment
     */
    public static function render_comment_content(WP_Comment $comment, WP_Comment $nested_comment = null)
    {
        ?>
        <div style="font-size: 16px; font-weight: normal; margin: 16px 6px; padding: 0 6px; border-left: 4px solid #BBBBBB;">
            <?php echo nl2br($comment->comment_content); ?>
            <div style="color: #AAAAAA; font-style: italic; font-size: 12px; margin-top: 4px;">
                - <?php echo $comment->comment_author; ?>
            </div>
            <?php
            if ($nested_comment !== null) {
                self::render_comment_content($nested_comment);
            }
            ?>
        </div>
        <?php
    }
}