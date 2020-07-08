<?php
/**
 * The template for displaying Archive pages.
 */

get_header(); ?>

<div id="cm-content-wrapper" class="alignwide">
	<div class="cm-content">
		<?php 
		if ( have_posts() ) : while ( have_posts() ) : the_post();
		$post_meta = get_post_meta( get_the_ID() );
		?>
		<div class="cm-entry-content">
			<article>
				<div class="deceased-img">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
				<?php endif; ?>
				</div>
				<div class="deceased-info">
					<h2 class="deceased-name"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo (isset($post_meta['honoraryitle']) && !empty( $post_meta['honoraryitle'][0]) ? $post_meta['honoraryitle'][0].' ' : '') . esc_html( $post_meta['name'][0] ) . ' ' . esc_html( $post_meta['familyname'][0] ); ?></a></h2>
					<?php if ( isset( $post_meta['residence'][0] ) && $post_meta['residence'][0] ) { ?>
					<h3 class="deceased-subtitle"><?php echo esc_html__( 'Resident of', 'cm_translate' ).': '.esc_html( $post_meta['residence'][0] ); ?></h3>
					<?php 
					}

					$relations = unserialize( current( $post_meta["relations"] ) );
					if ( ! empty( $relations ) ) {
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
					} ?>
					
					<div class="cm-buttons-wrapper">
						<input type="button" onclick="location.href='<?php the_permalink(); ?>?comments'" value="<?php echo esc_attr__( 'Condole', 'cm_translate' ); ?>">

						<?php
						if ( is_array( $post_meta['flowers'] ) && isset( $post_meta['flowers'][0] ) ) {
							$string = $post_meta["flowers"][0];
							if ( $string != '0' ) {
								$can_order_flower = \appsaloon\cm\register\Order_Type::verify_order_funeral_date(get_the_ID() ); ?>
								<input type="button" onclick="location.href='<?php the_permalink(); ?>?cm-products&cm-order-form'" value="<?php echo esc_attr__( 'Flowers', 'cm_translate' ); ?>" <?php echo ($can_order_flower) ? '': 'disabled'; ?>>
							<?php 
							}
						}

						if ( is_array( $post_meta['coffee_table'] ) && isset( $post_meta['coffee_table'][0] ) && $post_meta['coffee_table'][0] == 'yes' ) { ?>
							<input type="button" onclick="location.href='<?php the_permalink(); ?>?ct_form'" value="<?php echo esc_attr__( 'Coffee Table', 'cm_translate' ); ?>">
							<?php
						}

						if ( is_array( $post_meta['masscard'] ) && isset( $post_meta['masscard'][0] ) && $post_meta['masscard'][0] ) { ?>
							<input type="button" onclick="window.open('<?php echo esc_attr( $post_meta["masscard"][0] ); ?>', '_blank')" value="<?php echo esc_attr__( 'Mass card', 'cm_translate' ); ?>">
						<?php
						}
						?>
					</div>
				</div>
			</article>
			<footer class="entry-meta">
				<?php edit_post_link( esc_html__( 'Edit', 'cm-translation' ), '<span class="edit-link">', '</span>' ); ?>
			</footer>
		</div>
	<?php 
	endwhile;
	endif;
	?>
	</div>
</div>

<?php get_footer(); ?>
