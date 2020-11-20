<?php
if ( ! is_admin() ) {
	require_once( ABSPATH . 'wp-admin/includes/post.php' );
}
$password = isset($_GET['code']) ? $_GET['code'] : '';
$born = $deseased = false;

// we use the output buffer because some code inside do_shortcode('[cm_products]') requires headers not to be sent yet
ob_start();

get_header();

$post_meta = get_post_meta( get_the_ID() );
?>

<div id="cm-content-wrapper" class="alignwide">
	<div class="cm-content">
		<?php
        $is_single = true;
        require('deceased.php');
        ?>

		<?php
		$check_password = get_post_meta(get_the_ID(), 'password', true);
		if ( !empty($password) && $password == $check_password): ?>

			<div class="comments-list family_page">
				<h3><?php _e('Condolences for the family', 'cm_translate'); ?></h3>
				<?php comment_form(array(
					'title_reply' => __('Reply to this condolence', 'cm_translate'), 
					'title_reply_after' => '</h3><p id="info_text">' . __('This message will be send by mail to the author of the condolence.', 'cm_translate') . '</p>', 
					'label_submit' => __('Reply', 'cm_translate')
				));

				//Gather comments for a specific page/post
				$comments = get_comments(array(
					'post_id' => get_the_ID(),
					'status' => 'approve' //Change this to the type of comments to be displayed
				));

				//Display the list of comments
				echo '<ol class="commentlist">';
				wp_list_comments(array(
					'per_page' => 100, //Allow comment pagination
					'reverse_top_level' => false //Show the latest comments at the top of the list
				), $comments);
				echo '</ol>';

				$args = array(
					'base' => add_query_arg( 'cpage', '%#%' ),
					'total' => ceil( count( $comments ) / 100 ),
					'add_fragment' => ''
				);
				paginate_comments_links( $args );
				?>
			</div>

		<?php else: ?>
			<div class="comments" <?=cm_get_display_value('comments')?>>
				<?php
				$comment_form_fields = array(
					'author' =>
						'<p class="comment-form-author"><label for="author">' . __('Naam', 'cm_translate') . ' ' .
						'<span class="required">*</span></label>' .
						'<input id="author" name="author" type="text" value="" size="30" maxlength="245" aria-required="true" required="required"/></p>',

					'email' =>
						'<p class="comment-form-email"><label for="email">' . __('Email', 'cm_translate') . ' ' .
						'<span class="required">*</span></label>' .
						'<input id="email" name="email" type="text" value="" size="30" maxlength="100" aria-required="true" aria-describedby="email-notes" required="required"/></p>',

				);

				comment_form(
					array(
						'title_reply' => __('Leave your condolences for the family', 'cm_translate'),
						'title_reply_after' => '</h3><p id="info_text">' . __('This message is only visible for the family', 'cm_translate') . '</p>',
						'label_submit' => __('Condolence', 'cm_translate'),
						'fields' => apply_filters('comment_form_default_fields', $comment_form_fields)
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php
	$live_stream_is_enabled = isset($post_meta['live_stream'][0]) && $post_meta['live_stream'][0];
	$live_stream_has_url = isset($post_meta['live_stream_url'][0]) && $post_meta['live_stream_url'][0];
	$live_stream_is_embedded = isset($post_meta['live_stream_embed']) && $post_meta['live_stream_embed'][0];
	$page_is_live_stream = isset($_GET['livestream']);
	if ( $live_stream_is_enabled && $live_stream_has_url && $live_stream_is_embedded && $page_is_live_stream ): ?>
		<div id="stream-embed">
			<div>
				<div>
					<iframe src="<?php echo $post_meta['live_stream_url'][0]; ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>
			</div>
		</div>
	<?php endif;
	echo do_shortcode('[cm_products]');
	echo do_shortcode('[cm_order_form]');
	?>
</div>

<?php
get_footer();

// we use the output buffer because some code inside do_shortcode('[cm_products]') requires headers not to be sent yet
echo ob_get_clean();
?>
