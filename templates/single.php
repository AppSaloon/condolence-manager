<?php
if ( ! is_admin() ) {
	require_once ABSPATH . 'wp-admin/includes/post.php';
}
$password = isset( $_GET['code'] ) ? $_GET['code'] : '';

// we use the output buffer because some code inside do_shortcode('[cm_products]') requires headers not to be sent yet
ob_start();

get_header();

$post_meta = get_post_meta( get_the_ID() );
?>

<div id="cm-content-wrapper" class="alignwide">
	<div class="cm-content">
		<?php
		$is_single = true;
		require 'deceased.php';
		?>

		<?php
		$check_password = get_post_meta( get_the_ID(), 'password', true );
		if ( ! empty( $password ) && $password === $check_password ) :
			?>

			<div class="comments-list family_page">
				<h3><?php echo esc_html__( 'Condolences for the family', 'cm_translate' ); ?></h3>
				<?php
				comment_form(
					array(
						'title_reply'       => __( 'Reply to this condolence', 'cm_translate' ),
						'title_reply_after' => '</h3><p id="info_text">' . __( 'This message will be send by mail to the author of the condolence.', 'cm_translate' ) . '</p>',
						'label_submit'      => __( 'Reply', 'cm_translate' ),
					)
				);

				//Gather comments for a specific page/post
				$current_page      = ( get_query_var( 'cpage' ) ) ? get_query_var( 'cpage' ) : 1;
				$current_page      = is_numeric( $current_page ) ? $current_page : 1;
				$comments_per_page = 100;
				$offset            = ( ( $current_page - 1 ) * $comments_per_page );

				$total_comments           = get_comments(
					array(
						'post_id' => get_the_ID(),
						'status'  => 'approve',
						'count'   => true,
					)
				);
				$comments_on_current_page = get_comments(
					array(
						'post_id' => get_the_ID(),
						'status'  => 'approve',
						'number'  => $comments_per_page,
						'offset'  => $offset,
					)
				);

				echo '<ol class="commentlist">';
				wp_list_comments(
					array(
						'reverse_top_level' => false,
					),
					$comments_on_current_page
				);
				echo '</ol>';

				$args = array(
					'base'  => add_query_arg( 'cpage', '%#%' ),
					'total' => ceil( $total_comments / $comments_per_page ),
				);
				paginate_comments_links( $args );
				?>
			</div>

		<?php else : ?>
			<div class="comments" <?php echo cm_get_display_value( 'comments' ); ?>>
				<?php
				$comment_form_fields = array(
					'author' =>
						'<p class="comment-form-author"><label for="author">' . __( 'Naam', 'cm_translate' ) . ' ' .
						'<span class="required">*</span></label>' .
						'<input id="author" name="author" type="text" value="" size="30" maxlength="245" aria-required="true" required="required"/></p>',

					'email' =>
						'<p class="comment-form-email"><label for="email">' . __( 'Email', 'cm_translate' ) . ' ' .
						'<span class="required">*</span></label>' .
						'<input id="email" name="email" type="text" value="" size="30" maxlength="100" aria-required="true" aria-describedby="email-notes" required="required"/></p>',

				);

				comment_form(
					array(
						'title_reply'       => __( 'Leave your condolences for the family', 'cm_translate' ),
						'title_reply_after' => '</h3><p id="info_text">' . __( 'This message is only visible for the family', 'cm_translate' ) . '</p>',
						'label_submit'      => __( 'Condolence', 'cm_translate' ),
						'fields'            => apply_filters( 'comment_form_default_fields', $comment_form_fields ),
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php
	$live_stream_is_enabled  = isset( $post_meta['live_stream'][0] ) && $post_meta['live_stream'][0];
	$live_stream_has_url     = isset( $post_meta['live_stream_url'][0] ) && $post_meta['live_stream_url'][0];
	$live_stream_is_embedded = isset( $post_meta['live_stream_embed'] ) && $post_meta['live_stream_embed'][0];
	$page_is_live_stream     = isset( $_GET['livestream'] );
	?>
	<?php if ( $live_stream_is_enabled && $live_stream_has_url && $live_stream_is_embedded && $page_is_live_stream ) : ?>
		<div id="stream-embed">
			<div>
				<div>
					<iframe
						src="<?php echo esc_attr( $post_meta['live_stream_url'][0] ); ?>"
						allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					></iframe>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php
	echo do_shortcode( '[cm_products]' );
	echo do_shortcode( '[cm_order_form]' );
	?>
</div>

<?php
get_footer();

// we use the output buffer because some code inside do_shortcode('[cm_products]') requires headers not to be sent yet
echo ob_get_clean();
?>
