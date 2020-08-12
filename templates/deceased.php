<?php

use appsaloon\cm\register\Order_Type;

$decased_full_name = ( isset( $post_meta['honoraryitle'] ) && ! empty( $post_meta['honoraryitle'][0] ) ? $post_meta['honoraryitle'][0] . ' ' : '' ) . esc_html( $post_meta['name'][0] ) . ' ' . esc_html( $post_meta['familyname'][0] );
$relations         = unserialize( current( $post_meta["relations"] ) );
$gender            = current( $post_meta['gender'] );
?>

<div class="cm-entry-content">
    <article>
        <div class="deceased-img">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php if ( $is_single ): ?>
					<?php the_post_thumbnail( 'medium' ); ?>
				<?php else: ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail( 'medium' ); ?>
                    </a>
				<?php endif; ?>
			<?php endif; ?>
        </div>
        <div class="deceased-info">
            <h2 class="deceased-name">
				<?php if ( $is_single ): ?>
					<?php echo $decased_full_name ?>
				<?php else: ?>
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
						<?php echo $decased_full_name ?>
                    </a>
				<?php endif; ?>
            </h2>
			<?php if ( isset( $post_meta['residence'][0] ) && $post_meta['residence'][0] ): ?>
                <h3 class="deceased-subtitle">
					<?php echo esc_html__( 'Resident of', 'cm_translate' ) . ': ' . esc_html( $post_meta['residence'][0] ); ?>
                </h3>
			<?php endif; ?>

			<?php if ( ! empty( $relations ) ): ?>
                <div class="deceased-partner">
                    <p>
						<?php foreach ( $relations as $relation ): ?>
							<?php
							$relation_type = false;
							if ($relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Male') {
								 $relation_type = esc_html__( 'Beloved husband of', 'cm_translate' );
                            } else if ($relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female') {
								 $relation_type = esc_html__( 'Beloved wife of', 'cm_translate' );
                            } else if ($relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male') {
								 $relation_type = esc_html__( 'Beloved husband of the late', 'cm_translate' );
                            } else if ($relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female') {
								 $relation_type = esc_html__( 'Beloved wife of the late', 'cm_translate' );
                            }
							?>
							<?php if ( $relation_type !== false ): ?>
                                <strong><?php echo $relation_type ?>:</strong>
								<?php echo esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] ); ?>
							<?php elseif ( $relation['type'] == 'Other' ): ?>
								<?php echo esc_html( $relation['other'] ) . ' ' . esc_html( $relation['name'] ) . ' ' . esc_html( $relation['familyname'] ); ?>
							<?php endif; ?>
						<?php endforeach; ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( isset( $post_meta["birthdate"][0] ) && $post_meta["birthdate"][0] != '' ) : ?>
				<?php
				$date_of_birth            = DateTime::createFromFormat( 'Y-m-d', $post_meta["birthdate"][0] )->getTimestamp();
				$translated_date_of_birth = date_i18n( get_option( 'date_format' ), $date_of_birth );
				?>
                <div class="deceased-born-place-and-date">
                    <p>
                        <strong>
							<?php echo esc_html__( 'Born', 'cm_translate' ) . ' ' . esc_html__( 'in', 'cm_translate' ) . ':'; ?>
                        </strong>
						<?php echo esc_html( $post_meta["birthplace"][0] ); ?> <?php echo esc_html__( 'on', 'cm_translate' ); ?>
						<?php echo esc_html( $translated_date_of_birth ); ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( isset( $post_meta["dateofdeath"][0] ) && $post_meta["dateofdeath"][0] != '' ) : ?>
				<?php
				$date_of_death            = DateTime::createFromFormat( 'Y-m-d', $post_meta["dateofdeath"][0] )->getTimestamp();
				$translated_date_of_death = date_i18n( get_option( 'date_format' ), $date_of_death );
				?>
                <div class="deceased-death-place-and-date">
                    <p>
                        <strong>
							<?php echo esc_html__( 'Passed away', 'cm_translate' ) . ' ' . esc_html__( 'in', 'cm_translate' ) . ':'; ?>
                        </strong>
						<?php echo esc_html( $post_meta["placeofdeath"][0] ); ?> <?php echo esc_html__( 'on', 'cm_translate' ); ?>
						<?php echo esc_html( $translated_date_of_death ); ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( isset( $post_meta["funeraldate"][0] ) && $post_meta["funeraldate"][0] != '' ): ?>
				<?php
				$date_of_funeral            = DateTime::createFromFormat( 'Y-m-d', $post_meta["funeraldate"][0] )->getTimestamp();
				$translated_date_of_funeral = date_i18n( get_option( 'date_format' ), $date_of_funeral );
				?>
                <div class="deceased-funeral-date">
                    <p>
                        <strong>
							<?php echo esc_html__( 'Funeral date', 'cm_translate' ) . ':'; ?>
                        </strong>
						<?php echo esc_html( $translated_date_of_funeral ); ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( isset( $post_meta["_cm_linked_location"] ) ) : ?>
				<?php
				$location_id = current( $post_meta["_cm_linked_location"] );
				?>
				<?php if ( $location_id != 0 ) : ?>
					<?php
					$location = get_the_title( $location_id );
					?>
					<?php if ( ! empty( $location ) ) : ?>
                        <div class="deceased-linked-location">
                            <p>
                                <strong><?php echo esc_html__( "Laid out at", "cm_translate" ); ?>:</strong>
								<?php echo esc_html( $location ); ?>
                            </p>
                        </div>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( $post_meta["funeralinformation"][0] ) : ?>
                <div class="deceased-funeral-info">
                    <p>
                        <strong><?php echo esc_html__( 'Funeral information', 'cm_translate' ); ?>:</strong>
						<?php echo esc_html( $post_meta["funeralinformation"][0] ); ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( $post_meta["prayervigilinformation"][0] ) : ?>
                <div class="deceased-wake">
                    <p>
                        <strong><?php echo esc_html__( 'Prayer vigil information', 'cm_translate' ); ?>:</strong>
						<?php echo esc_html( $post_meta["prayervigilinformation"][0] ); ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( $post_meta["greetinginformation"][0] ) : ?>
                <div class="deceased-greetings">
                    <p>
                        <strong><?php echo esc_html__( 'Greeting information', 'cm_translate' ); ?>:</strong>
						<?php echo esc_html( $post_meta["greetinginformation"][0] ); ?>
                    </p>
                </div>
			<?php endif; ?>

			<?php if ( $is_single ): /* Only show the livestream information on the single page  */ ?>
				<?php if ( isset( $post_meta['live_stream'][0] ) && $post_meta['live_stream'][0] == 1 && isset( $post_meta['live_stream_url'][0] ) && $post_meta['live_stream_url'][0] && isset( $post_meta['live_stream_description'][0] ) && $post_meta['live_stream_description'][0] ) : ?>
                    <div class="deceased-live-stream">
                        <p>
                            <strong><?php echo __( 'Live-stream information', 'cm_translate' ); ?>:</strong>
							<?php echo current( $post_meta['live_stream_description'] ); ?>
                        </p>
                    </div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! $is_single || ( isset( $password ) && $password === '' ) ) : /* Do not show buttons when page is single AND password is empty */ ?>
                <div class="cm-buttons-wrapper">
                    <input type="button" onclick="location.href='<?php the_permalink(); ?>?comments'"
                           value="<?php echo esc_attr__( 'Condole', 'cm_translate' ); ?>"
	                        <?php echo ( comments_open() ) ? '' : 'disabled'; ?>>

					<?php if ( is_array( $post_meta['flowers'] ) && isset( $post_meta['flowers'][0] ) ) : ?>
						<?php $string = $post_meta["flowers"][0]; ?>
						<?php if ( $string != '0' ) : ?>
							<?php $can_order_flower = Order_Type::verify_order_funeral_date( get_the_ID() ); ?>
                            <input type="button"
                                   onclick="location.href='<?php the_permalink(); ?>?cm-products&cm-order-form'"
                                   value="<?php echo esc_attr__( 'Flowers', 'cm_translate' ); ?>"
								<?php echo ( $can_order_flower ) ? '' : 'disabled'; ?>>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( is_array( $post_meta['coffee_table'] ) && isset( $post_meta['coffee_table'][0] ) && $post_meta['coffee_table'][0] == 'yes' ) : ?>
                        <input type="button" onclick="location.href='<?php the_permalink(); ?>?ct_form'"
                               value="<?php echo esc_attr__( 'Coffee Table', 'cm_translate' ); ?>">
					<?php endif; ?>

					<?php if ( is_array( $post_meta['masscard'] ) && isset( $post_meta['masscard'][0] ) && $post_meta['masscard'][0] ) : ?>
                        <input type="button"
                               onclick="window.open('<?php echo esc_attr( $post_meta["masscard"][0] ); ?>', '_blank')"
                               value="<?php echo esc_attr__( 'Mass card', 'cm_translate' ); ?>">
					<?php endif; ?>

					<?php if ( $is_single ) : /* Only show livestream button in single */ ?>
						<?php if ( isset( $post_meta['live_stream'][0] ) && $post_meta['live_stream'][0] && isset( $post_meta['live_stream_url'][0] ) && $post_meta['live_stream_url'][0] ) : ?>
							<?php
							$is_embedded     = isset( $post_meta['live_stream_embed'] ) && $post_meta['live_stream_embed'][0];
							$live_stream_url = $is_embedded ? '?livestream' : $post_meta['live_stream_url'][0];
							?>
							<?php if ( $is_embedded ) : ?>
                                <input type="button"
                                       onclick="location.href='<?php echo the_permalink() . $live_stream_url; ?>'"
                                       value="<?php _e( 'Funeral live-stream', 'cm_translate' );
								       ?>">
							<?php else: ?>
                                <input type="button" onclick="window.open('<?php echo $live_stream_url; ?>', '_blank')"
                                       value="<?php _e( 'Funeral live-stream', 'cm_translate' ); ?>">
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