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
                                    $pieces = explode("a:6:", $values["relations"][0]);
                                    $count = explode(':', $pieces[0])[1];
                                    $gender = $values["gender"][0];
                                    for($i=1; $i<=$count; $i++){
                                        $string = $pieces[$i];
                                        $d = explode(';', $string);
                                        $arr = array();
                                        foreach($d as $index=>$item){
                                            list($key,$value) = explode('"', $item);
                                            $arr[$index] = $value;
                                        }


                                        if($arr[1] == 'Married' && $arr[9] == '1' && $gender == 'Male'){
                                            echo '<p class="'. $arr[8] . '">';
                                            _e('Echtgenoot van ', 'cm_translate');
                                            echo  $arr[5] . ' ' . $arr[7];
                                            echo '</p>';
                                        }elseif($arr[1] == 'Married' && $arr[9] == '1' && $gender == 'Female'){
                                            echo '<p class="'. $arr[8] . '">';
                                            _e('Echtgenote van ', 'cm_translate');
                                            echo  $arr[5] . ' ' . $arr[7];
                                            echo '</p>';
                                        }elseif($arr[1] == 'Married' && $arr[9] == '0' && $gender == 'Male'){
                                            echo '<p class="'. $arr[8] . '">';
                                            _e('Echtgenoot van ', 'cm_translate');
                                            echo  $arr[5] . ' ' . $arr[6];
                                            echo '</p>';
                                        } elseif($arr[1] == 'Married' && $arr[9] == '0' && $gender == 'Female'){
                                            echo '<p class="'. $arr[8] . '">';
                                            _e('Echtgenote van ', 'cm_translate');
                                            echo  $arr[5] . ' ' . $arr[6];
                                            echo '</p>';
                                        } elseif($arr[1] == 'Other' && $arr[9] == '1' && $gender == 'Male'){
                                            echo '<p class="'. $arr[8] . '">';
                                            echo  $arr[5] . ' ' . $arr[6];
                                            _e(' zijn ', 'cm_translate');
                                            echo $arr[1];
                                            echo '</p>';
                                        } elseif($arr[1] == 'Other' && $arr[9] == '1' && $gender == 'Female'){
                                            echo '<p class="'. $arr[8] . '">';
                                            echo  $arr[5] . ' ' . $arr[6];
                                            _e(' haar ', 'cm_translate');
                                            echo $arr[3];
                                            echo '</p>';
                                        }elseif($arr[1] == 'Other' && $arr[9] == '0' && $gender == 'Male'){
                                            echo '<p class="'. $arr[8] . '">';
                                            echo  $arr[5] . ' ' . $arr[6];
                                            _e(' zijn overlede ', 'cm_translate');
                                            echo $arr[1];
                                            echo '</p>';
                                        } elseif($arr[1] == 'Other' && $arr[9] == '0' && $gender == 'Female'){
                                            echo '<p class="'. $arr[8] . '">';
                                            echo  $arr[5] . ' ' . $arr[6];
                                            _e(' haar overlede ', 'cm_translate');
                                            echo $arr[3];
                                            echo '</p>';
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
