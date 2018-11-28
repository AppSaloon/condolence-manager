<?php
/**
 * The template for displaying Archive pages.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <div id="rouw-content" class="site-content" role="main">
		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post();
				$values     = get_post_meta( get_the_ID() );
				$arraymonth = array(
					__( "January", "cm_translate" ),
					__( "February", "cm_translate" ),
					__( "March", "cm_translate" ),
					__( "April", "cm_translate" ),
					__( "May", "cm_translate" ),
					__( "June", "cm_translate" ),
					__( "July", "cm_translate" ),
					__( "August", "cm_translate" ),
					__( "September", "cm_translate" ),
					__( "October", "cm_translate" ),
					__( "November", "cm_translate" ),
					__( "December", "cm_translate" ),
				);
				?>
                <div class="rouw entry-content cdm_clear">
                    <article>
                        <div class="embed cdm_clear">
                            <div class="deceased-img" style="min-height: 230px;">
								<?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php the_post_thumbnail(); ?>
                                    </a>
								<?php endif; ?>
                            </div>
                            <div class="deceased-info">
                                <div class="deceased-name"><?php echo $values["name"][0] . '&nbsp;' . $values["familyname"][0]; ?></div>
                                <div class="deceased-subtitle"><?php echo $values["residence"][0]; ?></div>
                                <div class="deceased-partner">
									<?php
									$gender = current( $values['gender'] );

									$relations = unserialize( current( $values["relations"] ) );

									if ( ! empty( $relations ) ) {
										foreach ( $relations as $relation ) {
											if ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Male' ) {
												echo '<p class="alive">';
												_e( 'Beloved husband of', 'cm_translate' );
												echo '&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female' ) {
												echo '<p class="alive">';
												_e( 'Beloved wife of', 'cm_translate' );
												echo '&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male' ) {
												echo '<p class="alive">';
												_e( 'Beloved husband of the late', 'cm_translate' );
												echo '&nbsp;';
												echo $relation['name'] . '&nbsp;' . $relation['familyname'];
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female' ) {
												echo '<p class="alive">';
												_e( 'Beloved wife of the late', 'cm_translate' );
												echo '&nbsp;';
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
                                <div class="deceased-partner"><?php echo __( 'Born in', 'cm_translate' ) . '&nbsp;' . $values["birthplace"][0]; ?>
									<?php if ( isset( $values["birthdate"][0] ) && $values["birthdate"][0] != '' ) {
										echo __( 'on', 'cm_translate' ) . ' ';
									}
									$date   = $values["birthdate"][0];
									$pieces = explode( "-", $date );
									$num    = intval( $pieces[1] );
									$month  = $arraymonth[ $num - 1 ];
									echo $pieces[2] . '&nbsp;' . $month . '&nbsp;' . $pieces[0];
									?>
                                </div>
                                <div class="deceased-place-died"><?php echo __( 'Passed away in', 'cm_translate' ) . '&nbsp;' . $values["placeofdeath"][0]; ?>
									<?php if ( isset( $values["dateofdeath"][0] ) && $values["dateofdeath"][0] != '' ) {
										echo __( 'on', 'cm_translate' ) . ' ';
									}
									$date   = $values["dateofdeath"][0];
									$pieces = explode( "-", $date );
									$num    = intval( $pieces[1] );
									$month  = $arraymonth[ $num - 1 ];
									echo $pieces[2] . '&nbsp;' . $month . '&nbsp;' . $pieces[0];
									?></div>
								<?php if ( $values["funeralinformation"][0] ) { ?>
                                    <div class="deceased-uitvaart">
                                        <strong><?php _e( 'Funeral information', 'cm_translate' ); ?>
                                            : </strong><?php echo $values["funeralinformation"][0]; ?></div>
								<?php } ?>
								<?php if ( $values["prayervigilinformation"][0] ) { ?>
                                    <div class="deceased-wake">
                                        <strong><?php _e( 'Prayer vigil information', 'cm_translate' ); ?>
                                            : </strong><?php echo $values["prayervigilinformation"][0]; ?>
                                    </div>
								<?php } ?>
								<?php if ( $values["greetinginformation"][0] ) { ?>
                                    <div class="deceased-greetings">
                                        <strong><?php _e( 'Greeting information', 'cm_translate' ); ?>
                                            : </strong><?php echo $values["greetinginformation"][0]; ?>
                                    </div>
								<?php } ?>
                            </div>

							<?php if ( $values["masscard"][0] ) {
								$string = $values["masscard"][0]; ?>
                                <input type="button"
                                       onclick="location.href='<?php echo $string; ?>'"
                                       value="<?php _e( 'Mass card', 'cm_translate' ); ?>">
							<?php }
							?>

							<?php
							/**
							 *  action hook to render extra field
							 */
							do_action( 'conman_archive_render' ); ?>
                            <input type="button" onclick="location.href='<?php the_permalink(); ?>'"
                                   value="<?php _e( 'Condole', 'cm_translate' ); ?>">

							<?php

							if ( $values['coffee_table'][0] == 'yes' ) {
								?>
                                <input type="button" onclick="location.href='<?php the_permalink(); ?>'"
                                       value="<?php _e( 'Coffee Table', 'cm_translate' ); ?>">
								<?php
							}
							if ( $values["flowers"][0] !== 0 ) {

								$string = $values["flowers"][0];

								if ( $string != '0' ) { ?>
                                    <input type="button"
                                           onclick="location.href='/<?php _e( 'Flowers', 'cm_translate' ); ?>'"
                                           value="<?php _e( 'Flowers', 'cm_translate' ); ?>">
								<?php }
							}
							/**
							 * add additional buttons if are added on backend in condolatie-manager menu page
							 */
							if ( isset( $values["cm_additional_btn"] ) && $values["cm_additional_btn"][0] != 0 ) {
								$additional_btn = get_option( 'cm_additional_btn' );
								if ( ! empty( $additional_btn ) ) {
									foreach ( $additional_btn as $btn ) {
										?><input type="button"
                                                 onclick="location.href='<?php echo $btn['href']; ?>'"
                                                 value="<?php echo $btn['caption']; ?>">
									<?php }
								}
							}
							?>
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
        var deceasedIMG = jQuery('.deceased-img');
        var deceasedInfoHeight = jQuery('.deceased-info');
        for (var i = deceasedInfoHeight.length - 1; i >= 0; i--) {
            deceasedIMG[i].style.minHeight = deceasedInfoHeight[i].offsetHeight + 32 + 'px';
            console.log(deceasedIMG[i].offsetHeight);
        }
    });
</script>
<?php get_footer(); ?>
