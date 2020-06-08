<?php
if ( ! is_admin() ) {
	require_once( ABSPATH . 'wp-admin/includes/post.php' );
}
$password = isset($_GET['code']) ? $_GET['code'] : '';
$born = $deseased = false;
//ob_start();

get_header();

$post_meta = get_post_meta( get_the_ID() );
?>

<div id="cm-content-wrapper" class="alignwide">
	<div class="cm-content">
		<div class="cm-entry-content">
			<article>
				<div class="deceased-img">
				<?php 
				if ( has_post_thumbnail() ) :
					the_post_thumbnail( 'medium' );
				endif;
				 ?>
				</div>
				<div class="deceased-info">
					<h2 class="deceased-name"><?php echo (isset($post_meta['honoraryitle']) && !empty( $post_meta['honoraryitle'][0]) ? $post_meta['honoraryitle'][0].' ' : '') . esc_html( $post_meta['name'][0] ) . ' ' . esc_html( $post_meta['familyname'][0] ); ?></h2>
					<?php if ( isset( $post_meta['residence'][0] ) && $post_meta['residence'][0] ) { ?>
					<h3 class="deceased-subtitle"><?php echo esc_html__( 'Resident of', 'cm_translate' ).': '.esc_html( $post_meta['residence'][0] ); ?></h3>
					<?php 
					}

					$relations = unserialize( current( $post_meta["relations"] ) );
					if ( ! empty( $relations ) &&  $relations['type'] != 'Single' ) {
						$gender = current( $post_meta['gender'] );
						echo '<div class="deceased-partner">';
						echo '<p>';
						foreach ( $relations as $relation ) {
							if ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Male' ) {
								echo '<strong>'.esc_html__( 'Beloved husband of', 'cm_translate' ).':</strong> ';
								echo esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] );
							} 
							elseif ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female' ) {
								echo '<strong>'.esc_html__( 'Beloved wife of', 'cm_translate' );':</strong> ';
								echo esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] );
							} 
							elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male' ) {
								echo '<strong>'.esc_html__( 'Beloved husband of the late', 'cm_translate' ).':</strong> ';
								echo esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] );
							} 
							elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female' ) {
								echo '<strong>'.esc_html__( 'Beloved wife of the late', 'cm_translate' ).':</strong> ';
								echo esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] );
							} 
							elseif ( $relation['type'] == 'Other' ) {
								echo esc_html( $relation['other'] ) . ' ' . esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] );
							}
						}
						echo '</p>';
						echo '</div>';
					}

					if ( isset( $post_meta["birthdate"][0] ) && $post_meta["birthdate"][0] != '' ) :
						echo '<div class="deceased-born-place-and-date">';
						echo '<p>';
							$date            = DateTime::createFromFormat( 'Y-m-d', $post_meta["birthdate"][0] )->getTimestamp();
							$translated_date = date_i18n( get_option( 'date_format' ), $date );
							echo '<strong>'.esc_html__( 'Born', 'cm_translate' ) . ' '.esc_html__( 'in', 'cm_translate' ) . ':</strong> ';
							echo esc_html( $post_meta["birthplace"][0] ) . ' ';
							echo '<br>';
							echo '<strong>'.esc_html__( 'on', 'cm_translate' ) . ':</strong> ';
							echo esc_html( $translated_date );
						echo '</p>';
						echo '</div>';
					endif;

					if ( isset( $post_meta["dateofdeath"][0] ) && $post_meta["dateofdeath"][0] != '' ):
						echo '<div class="deceased-death-place-and-date">';
						echo '<p>';
							echo '<strong>'.esc_html__( 'Passed away', 'cm_translate' ) . ' '.esc_html__( 'in', 'cm_translate' ) . ':</strong> ';
							echo esc_html( $post_meta["placeofdeath"][0] ) . ' ';
							echo '<br>';
							echo '<strong>'.esc_html__( 'on', 'cm_translate' ) . ':</strong> ';
							$date            = DateTime::createFromFormat( 'Y-m-d', $post_meta["dateofdeath"][0] )->getTimestamp();
							$translated_date = date_i18n( get_option( 'date_format' ), $date );
							echo esc_html( $translated_date );
						echo '</p>';
						echo '</div>';
					endif;

					if ( isset( $post_meta["funeraldate"][0] ) && $post_meta["funeraldate"][0] != '' ):
						echo '<div class="deceased-funeral-date">';
						echo '<p>';
							echo '<strong>'.esc_html__( 'Funeral date', 'cm_translate' ) . ' '.esc_html__( 'on', 'cm_translate' ) . ':</strong> ';
							$date            = DateTime::createFromFormat( 'Y-m-d', $post_meta["funeraldate"][0] )->getTimestamp();
							$translated_date = date_i18n( get_option( 'date_format' ), $date );
							echo esc_html( $translated_date );
						echo '</p>';
						echo '</div>';
					endif;

					if ( isset( $post_meta["_cm_linked_location"] ) ) {
						$location_id = current( $post_meta["_cm_linked_location"] );
						if ( $location_id != 0 ) {
							$location = get_the_title( $location_id );
							if ( ! empty( $location ) ) {
								echo '<div class="deceased-linked-location">';
								echo '<p>';
								echo '<strong>'.esc_html__( "Laid out at", "cm_translate" ).':</strong> ';
								echo esc_html( $location );
								echo '</p>';
								echo '</div>';
							}
						}
					}

					if ( $post_meta["funeralinformation"][0] ) {
						echo '<div class="deceased-funeral-info">';
						echo '<p>';
						echo '<strong>'.esc_html__( 'Funeral information', 'cm_translate' ).':</strong> ';
						echo esc_html( $post_meta["funeralinformation"][0] );
						echo '</p>';
						echo '</div>';
					}

					if ( $post_meta["prayervigilinformation"][0] ) {
						echo '<div class="deceased-wake">';
						echo '<p>';
						echo '<strong>'.esc_html__( 'Prayer vigil information', 'cm_translate' ).':</strong> ';
						echo esc_html( $post_meta["prayervigilinformation"][0] );
						echo '</p>';
						echo '</div>';
					}
					
					if ( $post_meta["greetinginformation"][0] ) {
						echo '<div class="deceased-greetings">';
						echo '<p>';
						echo '<strong>'.esc_html__( 'Greeting information', 'cm_translate' ).':</strong> ';
						echo esc_html( $post_meta["greetinginformation"][0] );
						echo '</p>';
						echo '</div>';
					}

					if ( isset($post_meta['live_stream'][0]) && $post_meta['live_stream'][0] == 1 &&  isset($post_meta['live_stream_url'][0]) && $post_meta['live_stream_url'][0] &&  isset($post_meta['live_stream_description'][0]) && $post_meta['live_stream_description'][0] ) {
						echo '<div class="deceased-live-stream">';
						echo '<p>';
						echo '<strong>'.__('Live-stream information', 'cm_translate').':</strong> '.current($post_meta['live_stream_description']);
						echo '</p>';
						echo '</div>';
					}

					if ($password == '') {
						echo '<div class="cm-buttons-wrapper">';

						echo '<a href="?comments" class="btn">';
						_e('Condole', 'cm_translate');
						echo '</a>';
	
						if ($post_meta['flowers'][0] === '1') {
							$can_order_flower = \appsaloon\cm\register\Order_Type::verify_order_funeral_date(get_the_ID() );
							$flower_link = ($can_order_flower) ? '?cm-products&cm-order-form&cm_order_product' : '#';
							echo '<a href="'.esc_attr($flower_link).'>" class="btn'.($can_order_flower ? '': ' disabled').'">';
							_e('Flowers', 'cm_translate');
							echo '</a>';
						}

						if ($post_meta['coffee_table'][0] == 'yes') {
							echo '<a href="?ct_form" class="btn">';
							_e('Coffee Table', 'cm_translate');
							echo '</a>';
						}

						if (isset($post_meta['masscard'][0])) {
							echo '<a target="_blank" href="'.$post_meta['masscard'][0].'" class="btn" id="toggle_flowers">';
							_e('Mass card', 'cm_translate');
							echo '</a>';
						}

						if (isset($post_meta['live_stream'][0]) && $post_meta['live_stream'][0] && isset($post_meta['live_stream_url'][0]) && $post_meta['live_stream_url'][0]) {
							$is_embedded = isset($post_meta['live_stream_embed']) && $post_meta['live_stream_embed'][0];
							$live_stream_url = $is_embedded ? '?livestream' : $post_meta['live_stream_url'][0];
							echo '<a '.($is_embedded ? '': 'target="_blank"').' href="'.$live_stream_url.'" class="btn" id="live_stream_url">';
							_e('Funeral live-stream', 'cm_translate');
							echo '</a>';
						}

						echo '</div>';
					}
					?>
			</article>
		</div>

		<?php
		$check_password = get_post_meta(get_the_ID(), 'password', true);
		if ( !empty($password) && $password == $check_password) { ?>

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
				?>
			</div>

		<?php } else { ?>
			<div class="comments" <?=cm_get_display_value('comments')?>">
				<?php
				$errors = apply_filters('wpice_get_comment_form_errors_as_list', ''); // call template tag to print the error list
				if ($errors) {
					echo '<div class="error_box">';
					echo '<h3 class="secondarypage">';
					_e("Comment Error", "cm_translate");
					echo '</h3>';
					echo $errors;
					echo '</div>';
				}

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
		<?php } ?>
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

//echo ob_get_clean();
?>
