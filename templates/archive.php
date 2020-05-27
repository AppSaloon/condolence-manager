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
                            <div class="deceased-img" style="min-height: 252px;">
								<?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
										<?php the_post_thumbnail( 'medium' ); ?>
                                    </a>
								<?php endif; ?>
                            </div>
                            <div class="deceased-info">
                                <div class="deceased-name">
									<?php echo esc_html( $post_meta["name"][0] ) . '&nbsp;' . esc_html( $post_meta["familyname"][0] ); ?>
                                </div>
								<?php
								if ( isset( $post_meta["residence"][0] ) && $post_meta["residence"][0] ) { ?>
                                    <div class="deceased-subtitle">
										<?php echo esc_html__( 'Resident of', 'cm_translate' ); ?>
                                        : <?php echo esc_html( $post_meta["residence"][0] ); ?>
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
												echo esc_html__( 'Beloved husband of', 'cm_translate' );
												echo ':&nbsp;';
												echo esc_html( $relation['name'] ) . '&nbsp;' . esc_html( $relation['familyname'] );
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female' ) {
												echo '<p class="alive">';
												echo esc_html__( 'Beloved wife of', 'cm_translate' );
												echo ':&nbsp;';
												echo esc_html( $relation['name'] ) . '&nbsp;' . esc_html( $relation['familyname'] );
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male' ) {
												echo '<p class="alive">';
												echo esc_html__( 'Beloved husband of the late', 'cm_translate' );
												echo ':&nbsp;';
												echo esc_html( $relation['name'] ) . '&nbsp;' . esc_html( $relation['familyname'] );
												echo '</p>';
											} elseif ( $relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female' ) {
												echo '<p class="alive">';
												echo esc_html__( 'Beloved wife of the late', 'cm_translate' );
												echo ':&nbsp;';
												echo esc_html( $relation['name'] ) . '&nbsp;' . esc_html( $relation['familyname'] );
												echo '</p>';
											} elseif ( $relation['type'] == 'Other' ) {
												echo '<p class="alive">';
												echo esc_html( $relation['other'] ) . '&nbsp;' . esc_html( $relation['name'] ) . '&nbsp;' . esc_html( $relation['familyname'] );
												echo '</p>';
											}
										}
									}
									?></div>
	                            <?php if ( isset( $post_meta["birthdate"][0] ) && $post_meta["birthdate"][0] != '' ): ?>
                                    <div class="deceased-partner">
			                            <?php
			                            $date            = DateTime::createFromFormat( 'Y-m-d', $post_meta["birthdate"][0] )->getTimestamp();
			                            $translated_date = date_i18n( get_option( 'date_format' ), $date );
			                            echo esc_html__( 'Born', 'cm_translate' ) . '&nbsp;';
			                            echo esc_html__( 'in', 'cm_translate' ) . ':&nbsp;';
			                            echo esc_html( $post_meta["birthplace"][0] ) . '&nbsp;';
			                            echo esc_html__( 'on', 'cm_translate' ) . ':&nbsp;';
			                            echo esc_html( $translated_date );
			                            ?>
                                    </div>
	                            <?php endif; ?>
	                            <?php if ( isset( $post_meta["dateofdeath"][0] ) && $post_meta["dateofdeath"][0] != '' ): ?>
                                    <div class="deceased-death-place-and-date">
			                            <?php
			                            echo esc_html__( 'Passed away', 'cm_translate' ) . '&nbsp;';
			                            echo esc_html__( 'in', 'cm_translate' ) . ':&nbsp;';
			                            echo esc_html( $post_meta["placeofdeath"][0] ) . '&nbsp;';
			                            echo esc_html__( 'on', 'cm_translate' ) . ':&nbsp;';
			                            $date            = DateTime::createFromFormat( 'Y-m-d', $post_meta["dateofdeath"][0] )->getTimestamp();
			                            $translated_date = date_i18n( get_option( 'date_format' ), $date );
			                            echo esc_html( $translated_date );
			                            ?>
                                </div>
                                <?php endif; ?>
	                            <?php if ( isset( $post_meta["funeraldate"][0] ) && $post_meta["funeraldate"][0] != '' ): ?>
                                    <div class="deceased-funeral-date">
			                            <?php
			                            echo esc_html__( 'Funeral date', 'cm_translate' ) . '&nbsp;';
			                            echo esc_html__( 'on', 'cm_translate' ) . ':&nbsp;';
			                            $date            = DateTime::createFromFormat( 'Y-m-d', $post_meta["funeraldate"][0] )->getTimestamp();
			                            $translated_date = date_i18n( get_option( 'date_format' ), $date );
			                            echo esc_html( $translated_date );
			                            ?>
                                    </div>
	                            <?php endif; ?>
                                <br/>
								<?php
								if ( isset( $post_meta["_cm_linked_location"] ) ) {
									$location_id = current( $post_meta["_cm_linked_location"] );
									if ( $location_id != 0 ) {
										$location = get_the_title( $location_id );
										if ( ! empty( $location ) ) { ?>
                                            <div class="_cm_linked_location">
												<?php echo esc_html__( "Laid out at", "cm_translate" ); ?>:
												<?php echo esc_html( $location ); ?>
                                            </div>
											<?php
										}
									}
								}
								?>
								<?php if ( $post_meta["funeralinformation"][0] ) { ?>
                                    <div class="deceased-uitvaart">
										<?php echo esc_html__( 'Funeral information', 'cm_translate' ); ?>:
										<?php echo esc_html( $post_meta["funeralinformation"][0] ); ?>
                                    </div>
								<?php } ?>
								<?php if ( $post_meta["prayervigilinformation"][0] ) { ?>
                                    <div class="deceased-wake">
										<?php echo esc_html__( 'Prayer vigil information', 'cm_translate' ); ?>:
										<?php echo esc_html( $post_meta["prayervigilinformation"][0] ); ?>
                                    </div>
								<?php } ?>
								<?php if ( $post_meta["greetinginformation"][0] ) { ?>
                                    <div class="deceased-greetings">
										<?php echo esc_html__( 'Greeting information', 'cm_translate' ); ?>:
										<?php echo esc_html( $post_meta["greetinginformation"][0] ); ?>
                                    </div>
								<?php } ?>
                                <input type="button"
                                       onclick="location.href='<?php the_permalink(); ?>?comments'"
                                       value="<?php echo esc_attr__( 'Condole', 'cm_translate' ); ?>"
                                />
								<?php
								if ( $post_meta["flowers"][0] ) {
									if ( isset( $post_meta["flowers"][0] ) ) {
										$string = $post_meta["flowers"][0];
									}
									if ( $string != '0' ) {
									    $can_order_flower = \appsaloon\cm\register\Order_Type::verify_order_funeral_date(get_the_ID() );
									    ?>
                                        <input type="button"
                                               onclick="location.href='<?php the_permalink(); ?>?cm-products&cm-order-form'"
                                               value="<?php echo esc_attr__( 'Flowers', 'cm_translate' ); ?>"
                                               <?php echo ($can_order_flower) ? '': 'disabled'; ?>
                                        />
									<?php }
								}

								if ( $post_meta['coffee_table'][0] == 'yes' ) {
									?>
                                    <input type="button"
                                           onclick="location.href='<?php the_permalink(); ?>?ct_form'"
                                           value="<?php echo esc_attr__( 'Coffee Table', 'cm_translate' ); ?>"
                                    />
									<?php
								}

								if ( $post_meta["masscard"][0] ) { ?>
                                    <a target="_blank" href="<?php echo esc_attr( $post_meta["masscard"][0] ); ?>">
                                        <input type="button"
                                               value="<?php echo esc_attr__( 'Mass card', 'cm_translate' ); ?>"/>
                                    </a>
									<?php
								}
								?>
                            </div>

                        </div>
                        <footer class="entry-meta">
							<?php edit_post_link( esc_html__( 'Edit', 'cm-translation' ), '<span class="edit-link">', '</span>' ); ?>
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
