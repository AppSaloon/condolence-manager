<?php
/**
 * The template for displaying Archive pages.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <div id="main" class="site-content rouw-main" role="main">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post();
				$post_meta = get_post_meta( get_the_ID() );
				?>
                <div class="rouw entry-content clear">
                    <article>
                        <div class="embed clear">
                            <div class="deceased-img" <?php echo 'style="min-height: 252px;"' ?>>
								<?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php the_post_thumbnail( 'medium' ); ?>
                                    </a>
								<?php endif; ?>
                            </div>
                            <div class="deceased-info">
                                <div class="deceased-name">
									<?php echo $post_meta["name"][0] . '&nbsp;' . $post_meta["familyname"][0]; ?>
                                </div>
								<?php
								if ( isset( $post_meta["residence"][0] ) && $post_meta["residence"][0] ) { ?>
                                    <div class="deceased-subtitle">
										<?php _e( 'Resident of', 'cm_translate' ); ?>
                                        : <?php echo $post_meta["residence"][0]; ?>
                                    </div>
									<?php
								}
								?>
                                <div class="deceased-partner">
									<?php
									$gender = current( $post_meta['gender'] );

									$relations = unserialize( current( $post_meta["relations"] ) );

									if ( ! empty( $relations ) ) {
										foreach ( $relations as $relation ) {
											if ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Male' ) {
												echo '<p class="alive">';
												_e( 'Beloved husband of', 'cm_translate' );
												echo ':&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female' ) {
												echo '<p class="alive">';
												_e( 'Beloved wife of', 'cm_translate' );
												echo ':&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male' ) {
												echo '<p class="alive">';
												_e( 'Beloved husband of the late', 'cm_translate' );
												echo ':&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female' ) {
												echo '<p class="alive">';
												_e( 'Beloved wife of the late', 'cm_translate' );
												echo ':&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Other' && $relation['alive'] == '1' && $gender == 'Male' ) {
												echo '<p class="alive">';
												echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Other' && $relation['alive'] == '1' && $gender == 'Female' ) {
												echo '<p class="alive">';
												echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Other' && $relation['alive'] == '0' && $gender == 'Male' ) {
												echo '<p class="alive">';
												echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Other' && $relation['alive'] == '0' && $gender == 'Female' ) {
												echo '<p class="alive">';
												echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											}
										}
									}
									?></div>
                                <div class="deceased-partner">
									<?php _e( 'Born', 'cm_translate' ); ?>
									<?php _e( 'in', 'cm_translate' ); ?>:
									<?php echo $post_meta["birthplace"][0]; ?>
									<?php if ( isset( $post_meta["birthdate"][0] ) && $post_meta["birthdate"][0] != '' ) {
										echo __( 'on', 'cm_translate' ) . ':&nbsp;';
									}
									$date = $post_meta["birthdate"][0];
									echo $date;
									?>
                                </div>
                                <div class="deceased-place-died">
									<?php _e( 'Passed away', 'cm_translate' ); ?>
									<?php _e( 'in', 'cm_translate' ); ?>:
									<?php echo $post_meta["placeofdeath"][0]; ?>
									<?php if ( isset( $post_meta["dateofdeath"][0] ) && $post_meta["dateofdeath"][0] != '' ) {
										echo __( 'on', 'cm_translate' ) . ':&nbsp;';
									}
									$date = $post_meta["dateofdeath"][0];
									echo $date;
									?></div>
								<?php
								if ( isset( $post_meta["_cm_linked_location"] ) ) {
									$location_id = current( $post_meta["_cm_linked_location"] );
									if ( $location_id != 0 ) {
										$location = get_the_title( $location_id );
										if ( ! empty( $location ) ) { ?>
                                            <div class="_cm_linked_location">
												<?php _e( "Laid out at", "cm_translate" ); ?>:
												<?php echo $location; ?>
                                            </div>
											<?php
										}
									}
								}
								?>
								<?php if ( $post_meta["funeralinformation"][0] ) { ?>
                                    <div class="deceased-uitvaart">
										<?php _e( 'Funeral information', 'cm_translate' ); ?>:
										<?php echo $post_meta["funeralinformation"][0]; ?>
                                    </div>
								<?php } ?>
								<?php if ( $post_meta["prayervigilinformation"][0] ) { ?>
                                    <div class="deceased-wake">
										<?php _e( 'Prayer vigil information', 'cm_translate' ); ?>:
										<?php echo $post_meta["prayervigilinformation"][0]; ?>
                                    </div>
								<?php } ?>
								<?php if ( $post_meta["greetinginformation"][0] ) { ?>
                                    <div class="deceased-greetings">
										<?php _e( 'Greeting information', 'cm_translate' ); ?>:
										<?php echo $post_meta["greetinginformation"][0]; ?>
                                    </div>
								<?php } ?>
                                <input type="button" onclick="location.href='<?php the_permalink(); ?>?comments'"
                                       value="<?php _e( 'Condole', 'cm_translate' ); ?>">
								<?php
								if ( $post_meta["flowers"][0] ) {
									if ( isset( $post_meta["flowers"][0] ) ) {
										$string = $post_meta["flowers"][0];
									}
									if ( $string != '0' ) { ?>
                                        <input type="button"
                                               onclick="location.href='<?php the_permalink(); ?>?cm-products&cm-order-form'"
                                               value="<?php _e( 'Flowers', 'cm_translate' ); ?>">
									<?php }
								}

								if ( $post_meta['coffee_table'][0] == 'yes' ) {
									?>
                                    <input type="button" onclick="location.href='<?php the_permalink(); ?>?ct_form'"
                                           value="<?php _e( 'Coffee Table', 'cm_translate' ); ?>">
									<?php
								}

								if ( $post_meta["masscard"][0] ) { ?>
                                    <a target="_blank" href="<?php echo $post_meta["masscard"][0]; ?>">
                                        <input type="button" value="<?php _e( 'Mass card', 'cm_translate' ); ?>"/>
                                    </a>
									<?php
								}

								/* TODO: should we show this in the archive?
								if (isset($values['live_stream'][0]) && $values['live_stream'][0] && isset($values['live_stream_url'][0]) && $values['live_stream_url'][0]) { ?>
									<a target="_blank" href="<?= $values['live_stream_url'][0] ?>">
										<input type="button" value="<?php _e('Funeral live-stream', 'cm_translate'); ?>" />
									</a>
									<?php
								}
								*/
								?>
                            </div>

                        </div>
                        <footer class="entry-meta">
							<?php edit_post_link( __( 'Edit', 'cm-translation' ), '<span class="edit-link">', '</span>' ); ?>
                        </footer><!-- .entry-meta -->
                    </article>
                </div> <!-- #content -->
			<?php endwhile; ?>

		<?php endif; ?>
    </div><!-- #main -->
</div><!-- #primary -->

<script>
  jQuery(document).ready(function () {
    var deceasedIMG = jQuery('.deceased-img')
    var deceasedInfoHeight = jQuery('.deceased-info')
    for (var i = deceasedInfoHeight.length - 1; i >= 0; i--) {
      deceasedIMG[i].style.minHeight = deceasedInfoHeight[i].offsetHeight + 32 + 'px'
      //console.log(deceasedIMG[i].offsetHeight);
    }
  })
</script>
<?php get_footer(); ?>
