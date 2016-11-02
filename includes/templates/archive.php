<?php
/**
 * The template for displaying Archive pages.
 */

get_header(); ?>

<div id="primary" class="content-area">
    <div id="rouw-content" class="site-content" role="main">
        <?php if ( have_posts() ) : ?>

            <?php while ( have_posts() ) : the_post();
                $values = get_post_meta(get_the_ID());
                $arraymaand = array(
                    "Januari",
                    "Februari",
                    "Maart",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Augustus",
                    "September",
                    "Oktober",
                    "November",
                    "December"
                );
                ?>
                <div class="rouw entry-content clear">
                    <article>
                        <div class="embed clear">
                            <div class="deceased-img" style="min-height: 230px;">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                        <?php the_post_thumbnail(); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="deceased-info">
                                <div class="deceased-name"><?php echo $values["name"][0] . ' ' . $values["familyname"][0]; ?></div>
                                <div class="deceased-subtitle"><?php echo $values["residence"][0]; ?></div>
                                <div class="deceased-partner">
                                    <?php
                                    $relations = unserialize( current($values["relations"]) );

                                    if( !empty( $relations ) ){
                                        foreach( $relations as $relation){
                                            if($relation['type'] == 'Married' && $relation['alive'] == '1' && $relation['gender'] == 'Male'){
                                                echo '<p class="alive">';
                                                _e('Beloved husband of ', 'cm_translate');
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                echo '</p>';
                                            }elseif($relation['type'] == 'Married' && $relation['alive'] == '1' && $relation['gender'] == 'Female'){
                                                echo '<p class="alive">';
                                                _e('Beloved wife of ', 'cm_translate');
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                echo '</p>';
                                            }elseif($relation['type'] == 'Married' && $relation['alive'] == '0' && $relation['gender'] == 'Male'){
                                                echo '<p class="alive">';
                                                _e('Beloved husband of the late ', 'cm_translate');
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                echo '</p>';
                                            } elseif($relation['type'] == 'Married' && $relation['alive'] == '0' && $relation['gender'] == 'Female'){
                                                echo '<p class="alive">';
                                                _e('Beloved wife of the late ', 'cm_translate');
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                echo '</p>';
                                            } elseif($relation['type'] == 'Other' && $relation['alive'] == '1' && $relation['gender'] == 'Male'){
                                                echo '<p class="alive">';
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                _e(' his ', 'cm_translate');
                                                echo $relation['other'];
                                                echo '</p>';
                                            } elseif($relation['type'] == 'Other' && $relation['alive'] == '1' && $relation['gender'] == 'Female'){
                                                echo '<p class="alive">';
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                _e(' her ', 'cm_translate');
                                                echo $relation['other'];
                                                echo '</p>';
                                            }elseif($relation['type'] == 'Other' && $relation['alive'] == '0' && $relation['gender'] == 'Male'){
                                                echo '<p class="alive">';
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                _e(' his late ', 'cm_translate');
                                                echo $relation['other'];
                                                echo '</p>';
                                            } elseif($relation['type'] == 'Other' && $relation['alive'] == '0' && $relation['gender'] == 'Female'){
                                                echo '<p class="alive">';
                                                echo  $relation['name'] . ' ' . $relation['familyname'];
                                                _e(' her late ', 'cm_translate');
                                                echo $relation['other'];
                                                echo '</p>';
                                            }
                                        }
                                    }
                                    ?></div>
                                <div class="deceased-partner">Geboren te <?php echo $values["birthplace"][0]; ?> op <?php
                                    $date = $values["birthdate"][0];
                                    $pieces = explode("-", $date);
                                    $num = intval($pieces[1]);
                                    $month = $arraymaand[$num - 1];
                                    echo $pieces[2] . ' ' . $month . ' ' . $pieces[0];
                                    ?>
                                </div>
                                <div class="deceased-place-died">Overleden te <?php echo $values["placeofdeath"][0]; ?> op <?php
                                    $date = $values["dateofdeath"][0];
                                    $pieces = explode("-", $date);
                                    $num = intval($pieces[1]);
                                    $month = $arraymaand[$num - 1];
                                    echo $pieces[2] . ' ' . $month . ' ' . $pieces[0];
                                    ?></div>
                                <div class="deceased-uitvaart"><?php echo $values["funeralinformation"][0]; ?></div>
                                <div class="deceased-wake"><?php echo $values["prayervigilinformation"][0]; ?></div>
                                <div class="deceased-greetings"><?php echo $values["greetinginformation"][0]; ?></div>
                            </div>
                            <input type="button" onclick="location.href='<?php the_permalink(); ?>'" value="Condoleren">
                            <input type="button" onclick="location.href='/bloemen'" value="Bloemen">


                            <?php
                            if($values['koffie_tafel'][0] == 'ja'){
	                           ?>
                                    <input type="button" onclick="location.href='<?php the_permalink(); ?>'"  value="Coffie Tafel">
                            <?php
                            }

                            if($values["masscard"][0]) {
                                $string = $values["masscard"][0];
                                echo '<a class="btn-masscard" target="_blank" href="' . $string . '" >';
                                echo __('Mass card', 'cm_translate');
                                echo '</a>';
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
    jQuery(document).ready( function() {
        var deceasedIMG = jQuery('.deceased-img');
        var deceasedInfoHeight = jQuery('.deceased-info');
        for (var i = deceasedInfoHeight.length - 1; i >= 0; i--) {
            deceasedIMG[i].style.minHeight = deceasedInfoHeight[i].offsetHeight + 32 + 'px';
            console.log(deceasedIMG[i].offsetHeight);
        }
    });
</script>
<?php get_footer(); ?>
