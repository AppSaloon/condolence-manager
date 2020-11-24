<?php

use appsaloon\cm\register\Order_Type;

/* @var $is_single bool */
/* @var $post_meta array */

$honorary_title = current( $post_meta['honoraryitle'] );
$name           = current( $post_meta['name'] );
$family_name    = current( $post_meta['familyname'] );
$gender         = current( $post_meta['gender'] );
$full_name      = trim( $honorary_title . ' ' . $name . ' ' . $family_name );
$relations      = unserialize( current( $post_meta['relations'] ) );
?>

<div class="cm-entry-content">
	<article>
		<div class="deceased-img">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php if ( $is_single ) : ?>
					<?php the_post_thumbnail( 'medium' ); ?>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'medium' ); ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="deceased-info">
			<h2 class="deceased-name">
				<?php if ( $is_single ) : ?>
					<?php echo esc_html( $full_name ); ?>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php echo esc_html( $full_name ); ?>
					</a>
				<?php endif; ?>
			</h2>
			<?php if ( isset( $post_meta['residence'][0] ) && $post_meta['residence'][0] ) : ?>
				<h3 class="deceased-subtitle">
					<?php echo esc_html__( 'Resident of', 'cm_translate' ) . ': ' . esc_html( $post_meta['residence'][0] ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! empty( $relations ) ) : ?>
				<div class="deceased-partner">
					<p>
						<?php foreach ( $relations as $relation ) : ?>
							<?php
							$relation_type = false;
							if ( $relation['type'] === 'Married' && $relation['alive'] === '1' && $gender === 'Male' ) {
								$relation_type = esc_html__( 'Beloved husband of', 'cm_translate' );
							} elseif ( $relation['type'] === 'Married' && $relation['alive'] === '1' && $gender === 'Female' ) {
								$relation_type = esc_html__( 'Beloved wife of', 'cm_translate' );
							} elseif ( $relation['type'] === 'Married' && $relation['alive'] === '0' && $gender === 'Male' ) {
								$relation_type = esc_html__( 'Beloved husband of the late', 'cm_translate' );
							} elseif ( $relation['type'] === 'Married' && $relation['alive'] === '0' && $gender === 'Female' ) {
								$relation_type = esc_html__( 'Beloved wife of the late', 'cm_translate' );
							} elseif ( $relation['type'] === 'Other' ) {
								$relation_type = $relation['other'];
							}
							?>
							<?php if ( $relation_type !== false ) : ?>
								<span class="deceased-info-header">
									<?php echo esc_html( $relation_type ); ?>
								</span>
								<span class="deceased-info-body">
									<?php echo esc_html( $relation['name'] . ' ' . $relation['familyname'] ); ?>
								</span>
								<br>
							<?php endif; ?>
						<?php endforeach; ?>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $post_meta['birthdate'][0] ) ) : ?>
				<?php
				$date_of_birth            = DateTime::createFromFormat( 'Y-m-d', $post_meta['birthdate'][0] )->getTimestamp();
				$translated_date_of_birth = date_i18n( get_option( 'date_format' ), $date_of_birth );
				?>
				<div class="deceased-born-place-and-date">
					<p>
						<span
							class="deceased-info-header"><?php echo esc_html__( 'Born', 'cm_translate' ) . ' ' . esc_html__( 'in', 'cm_translate' ); ?></span>
						<span class="deceased-info-body">
							<?php echo esc_html( $post_meta['birthplace'][0] ); ?>
							<?php echo esc_html__( 'on', 'cm_translate' ); ?>
							<?php echo esc_html( $translated_date_of_birth ); ?>
						</span>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $post_meta['dateofdeath'][0] ) ) : ?>
				<?php
				$date_of_death            = DateTime::createFromFormat( 'Y-m-d', $post_meta['dateofdeath'][0] )->getTimestamp();
				$translated_date_of_death = date_i18n( get_option( 'date_format' ), $date_of_death );
				?>
				<div class="deceased-death-place-and-date">
					<p>
						<span
							class="deceased-info-header"><?php echo esc_html__( 'Passed away', 'cm_translate' ) . ' ' . esc_html__( 'in', 'cm_translate' ); ?></span>
						<span class="deceased-info-body">
							<?php echo esc_html( $post_meta['placeofdeath'][0] ); ?>
							<?php echo esc_html__( 'on', 'cm_translate' ); ?>
							<?php echo esc_html( $translated_date_of_death ); ?>
						</span>
					</p>
				</div>
			<?php endif; ?>

			<?php
			$show_funeraldate = isset( $post_meta['show_funeraldate'][0] ) & $post_meta['show_funeraldate'][0] === '1';
			$has_funeraldate  = ! empty( $post_meta['funeraldate'][0] );
			if ( $show_funeraldate && $has_funeraldate ) :
				?>
				<?php
				$date_of_funeral            = DateTime::createFromFormat( 'Y-m-d', $post_meta['funeraldate'][0] )->getTimestamp();
				$translated_date_of_funeral = date_i18n( get_option( 'date_format' ), $date_of_funeral );
				?>
				<div class="deceased-funeral-date">
					<p>
						<span
							class="deceased-info-header"><?php echo esc_html__( 'Funeral date', 'cm_translate' ); ?></span>
						<span class="deceased-info-body">
							<?php echo esc_html( $translated_date_of_funeral ); ?>
						</span>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( isset( $post_meta['_cm_linked_location'] ) ) : ?>
				<?php
				$location_id = current( $post_meta['_cm_linked_location'] );
				?>
				<?php if ( $location_id !== '0' ) : ?>
					<?php
					$location = get_the_title( $location_id );
					?>
					<?php if ( ! empty( $location ) ) : ?>
						<div class="deceased-linked-location-header">
							<p>
								<span
									class="deceased-info-header"><?php echo esc_html__( 'Laid out at', 'cm_translate' ); ?></span>
								<span class="deceased-info-body">
									<?php echo esc_html( $location ); ?>
								</span>
							</p>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $post_meta['funeralinformation'][0] ) : ?>
				<div class="deceased-funeral-info">
					<p>
						<span
							class="deceased-info-header"><?php echo esc_html__( 'Funeral information', 'cm_translate' ); ?></span>
						<span class="deceased-info-body">
							<?php echo esc_html( $post_meta['funeralinformation'][0] ); ?>
						</span>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $post_meta['prayervigilinformation'][0] ) : ?>
				<div class="deceased-wake">
					<p>
						<span
							class="deceased-info-header"><?php echo esc_html__( 'Prayer vigil information', 'cm_translate' ); ?></span>
						<span class="deceased-info-body">
							<?php echo esc_html( $post_meta['prayervigilinformation'][0] ); ?>
						</span>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $post_meta['greetinginformation'][0] ) : ?>
				<div class="deceased-greetings">
					<p>
						<span
							class="deceased-info-header"><?php echo esc_html__( 'Greeting information', 'cm_translate' ); ?></span>
						<span class="deceased-info-body">
							<?php echo esc_html( $post_meta['greetinginformation'][0] ); ?>
						</span>
					</p>
				</div>
			<?php endif; ?>

			<?php if ( $is_single ) : /* Only show the livestream information on the single page  */ ?>
				<?php
				$should_show_live_stream     = isset( $post_meta['live_stream'][0] ) && $post_meta['live_stream'][0] === '1';
				$has_live_stream_url         = ! empty( $post_meta['live_stream_url'][0] );
				$has_lives_tream_description = ! empty( $post_meta['live_stream_description'][0] );
				?>
				<?php if ( $should_show_live_stream && $has_live_stream_url && $has_lives_tream_description ) : ?>
					<div class="deceased-live-stream">
						<p>
							<strong>
								<?php echo esc_html__( 'Live-stream information', 'cm_translate' ); ?>:
							</strong>
							<?php echo esc_html( $post_meta['live_stream_description'][0] ); ?>
						</p>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! ( $is_single && ! empty( $password ) ) ) : ?>
				<div class="cm-buttons-wrapper">
					<input
						type="button"
						onclick="location.href='<?php the_permalink(); ?>?comments'"
						value="<?php echo esc_attr__( 'Condole', 'cm_translate' ); ?>"
						<?php echo ( comments_open() ) ? '' : 'disabled'; ?>
					>

					<?php if ( is_array( $post_meta['flowers'] ) && isset( $post_meta['flowers'][0] ) ) : ?>
						<?php $string = $post_meta['flowers'][0]; ?>
						<?php if ( $string !== '0' ) : ?>
							<?php $can_order_flower = Order_Type::verify_order_funeral_date( get_the_ID() ); ?>
							<input
								type="button"
								onclick="location.href='<?php the_permalink(); ?>?cm-products&cm-order-form'"
								value="<?php echo esc_attr__( 'Flowers', 'cm_translate' ); ?>"
								<?php echo ( $can_order_flower ) ? '' : 'disabled'; ?>
							>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( isset( $post_meta['coffee_table'][0] ) && $post_meta['coffee_table'][0] === 'yes' ) : ?>
						<input
							type="button"
							onclick="location.href='<?php the_permalink(); ?>?ct_form'"
							value="<?php echo esc_attr__( 'Coffee Table', 'cm_translate' ); ?>"
						>
					<?php endif; ?>

					<?php if ( ! empty( $post_meta['masscard'][0] ) ) : ?>
						<input
							type="button"
							onclick="window.open('<?php echo esc_attr( $post_meta['masscard'][0] ); ?>', '_blank')"
							value="<?php echo esc_attr__( 'Mass card', 'cm_translate' ); ?>"
						>
					<?php endif; ?>

					<?php if ( $is_single ) : /* Only show livestream button in single */ ?>
						<?php if ( ! empty( $post_meta['live_stream'][0] ) && ! empty( $post_meta['live_stream_url'][0] ) ) : ?>
							<?php
							$is_embedded     = isset( $post_meta['live_stream_embed'] ) && $post_meta['live_stream_embed'][0];
							$live_stream_url = $is_embedded ? '?livestream' : $post_meta['live_stream_url'][0];
							?>
							<?php if ( $is_embedded ) : ?>
								<input
									type="button"
									onclick="location.href='<?php echo esc_attr( the_permalink() . $live_stream_url ); ?>'"
									value="<?php echo esc_attr__( 'Funeral live-stream', 'cm_translate' ); ?>"
								>
							<?php else : ?>
								<input
									type="button"
									onclick="window.open('<?php echo esc_attr( $live_stream_url ); ?>', '_blank')"
									value="<?php echo esc_attr__( 'Funeral live-stream', 'cm_translate' ); ?>"
								>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<footer class="entry-meta">
		<?php edit_post_link( esc_html__( 'Edit', 'cm-translation' ), '<span class="edit-link">', '</span>' ); ?>
	</footer>
</div>
