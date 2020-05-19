<?php
if ( ! is_admin() ) {
    require_once( ABSPATH . 'wp-admin/includes/post.php' );
}
$password = isset($_GET['code']) ? $_GET['code'] : '';
$born = false;
$deseased = false;
ob_start();
?>
<?php get_header(); ?>
<?php if (is_single()){
$fields = get_post_meta(get_the_ID()); ?>
<div id="primary" class="content-area">
    <div id="main" class="site-content" role="main">

        <div class="rouw entry-post">
            <div class="rouw-single">
                <table>
                    <tr>
                        <td class="img deceased-img">
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail();
                            }
                            ?>
                        </td>
                        <td class="text deceased-info">
                            <?php
                            $arraymonth = array(
                                __("January", "cm_translate"),
                                __("February", "cm_translate"),
                                __("March", "cm_translate"),
                                __("April", "cm_translate"),
                                __("May", "cm_translate"),
                                __("June", "cm_translate"),
                                __("July", "cm_translate"),
                                __("August", "cm_translate"),
                                __("September", "cm_translate"),
                                __("October", "cm_translate"),
                                __("November", "cm_translate"),
                                __("December", "cm_translate"),
                            );

                            $required_fields = [
                                "full_name",
                                "line_break",
                                "residence",
                                "relations",
                                "line_break",
                                "birthplace",
                                "birthdate",
                                "placeofdeath",
                                "dateofdeath",
                                "line_break",
                                "_cm_linked_location",
                                "funeralinformation",
                                "prayervigilinformation",
                                "greetinginformation",
                                "line_break",
                                "live_stream_description",
                            ];

                            foreach ($required_fields as $required) {

                                $required_str = strtolower($required);
                                $required_str = preg_replace('/\s+/', '', $required_str);
                                $gender = current($fields['gender']);

                                switch ($required_str) {
                                    case 'line_break':
                                        echo '<br/>';
                                        break;
                                    case 'full_name':
                                        $honorary_title = current($fields['honoraryitle']);
                                        echo '<p class="' . $required_str . '" id="name">';
                                        if(!empty($honorary_title)) {
                                            echo $honorary_title . '&nbsp;';
                                        }
                                        echo current($fields['name']) . '&nbsp;';
                                        echo current($fields['familyname']);
                                        echo '</p>';
                                        break;
                                    case 'birthdate':
                                        $bornOn = '';
                                        if ($born == false) {
                                            $born = true;
                                            $bornOn = __('Born', 'cm_translate');
                                        }
                                        echo '<p class="' . $required_str . '" id="birth">';
                                        echo $bornOn . '&nbsp;' . __('on', 'cm_translate') . ':&nbsp;' . current($fields[$required_str]);
                                        echo '</p>';
                                        break;
                                    case 'birthplace':
                                        $bornOn = '';
                                        if ($born == false) {
                                            $born = true;
                                            $bornOn = __('Born', 'cm_translate');
                                        }
                                        echo '<p class="' . $required_str . '" id="birth">';
                                        echo $bornOn . '&nbsp;' . __('in', 'cm_translate') . ':&nbsp;' . current($fields[$required_str]);
                                        echo '</p>';
                                        break;
                                    case 'dateofdeath':
                                        $passedAway = '';
                                        $date = current($fields[$required_str]);
                                        if ($deseased == false) {
                                            $deseased = true;
                                            $passedAway = __('Passed away', 'cm_translate');
                                        }
                                        echo '<p class="' . $required_str . '" id="death">';
                                        echo $passedAway . '&nbsp;' . __('on', 'cm_translate') . ':&nbsp;' . $date;
                                        echo '</p>';
                                        break;
                                    case 'placeofdeath':
                                        $passedAway = '';
                                        if ($deseased == false) {
                                            $deseased = true;
                                            $passedAway = __('Passed away', 'cm_translate');
                                        }
                                        echo '<p class="' . $required_str . '" id="death">';
                                        echo $passedAway . '&nbsp;' . __('in', 'cm_translate') . ':&nbsp;' . current($fields[$required_str]);
                                        echo '</p>';
                                        break;
                                    case 'residence':
                                        if(isset($fields[$required_str][0]) && $fields[$required_str][0]) {
                                            echo '<p class="deceased-subtitle">';
                                            echo __('Resident of', 'cm_translate') . ': ' . current($fields[$required_str]);
                                            echo '</p>';
                                        }
                                        break;
                                    case 'funeralinformation':
                                        if (isset($fields[$required_str][0]) && current($fields[$required_str]) != '') {
                                            echo '<p>';
                                            echo _e("Funeral information", "cm_translate") . ':&nbsp;' . current($fields[$required_str]);
                                            echo '</p>';
                                        }
                                        break;
                                    case 'prayervigilinformation':
                                        if (isset($fields[$required_str][0]) && current($fields[$required_str]) != '') {
                                            echo '<p class="' . $required_str . '">';
                                            echo _e("Prayer vigil information", "cm_translate") . ':&nbsp;' . current($fields[$required_str]);
                                            echo '</p>';
                                        }
                                        break;
                                    case 'greetinginformation':
                                        if (isset($fields[$required_str][0]) && current($fields[$required_str]) != '') {
                                            echo '<p class="' . $required_str . '">';
                                            echo _e("Greeting information", "cm_translate") . ':&nbsp;' . current($fields[$required_str]);
                                            echo '</p>';
                                        }
                                        break;
                                    case 'relations':
                                        $raw_data = current($fields[$required_str]);
                                        if (!empty($raw_data)) {
                                            $relations = unserialize($raw_data);
                                            foreach ($relations as $relation) {
                                                if ($relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Male') {
                                                    echo '<p>';
                                                    _e('Beloved husband of', 'cm_translate');
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female') {
                                                    echo '<p>';
                                                    _e('Beloved wife of', 'cm_translate');
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male') {
                                                    echo '<p>';
                                                    _e('Beloved husband of the late', 'cm_translate');
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female') {
                                                    echo '<p>';
                                                    _e('Beloved wife of the late', 'cm_translate');
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Other' && $relation['alive'] == '1' && $gender == 'Male') {
                                                    echo '<p>';
                                                    echo $relation['other'];
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Other' && $relation['alive'] == '1' && $gender == 'Female') {
                                                    echo '<p>';
                                                    echo $relation['other'];
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Other' && $relation['alive'] == '0' && $gender == 'Male') {
                                                    echo '<p>';
                                                    echo $relation['other'];
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                } elseif ($relation['type'] == 'Other' && $relation['alive'] == '0' && $gender == 'Female') {
                                                    echo '<p>';
                                                    echo $relation['other'];
                                                    echo ':&nbsp;';
                                                    echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                                    echo '</p>';
                                                }

                                            }
                                        }
                                        break;
                                    case '_cm_linked_location':
                                        if(isset($fields[$required_str])) {
                                            $location_id = current($fields[$required_str]);
                                            if($location_id != 0) {
                                                $location = get_the_title($location_id);
                                                if (!empty($location)) {
                                                    echo '<p class="' . $required_str . '">';
                                                    echo _e("Laid out at", "cm_translate") . ':&nbsp;' . $location;
                                                    echo '</p>';
                                                }
                                            }
                                        }
                                        break;
                                    case 'live_stream_description':
                                        if (isset($fields['live_stream'][0]) && $fields['live_stream'][0] == 1
                                            &&  isset($fields['live_stream_url'][0]) && $fields['live_stream_url'][0]
                                            &&  isset($fields['live_stream_description'][0]) && $fields['live_stream_description'][0]
                                        ) {?>
                                            <p>
                                                <?php _e('Live-stream information', 'cm_translate'); ?>:&nbsp;
                                                <?php echo current($fields['live_stream_description']); ?>
                                            </p>
                                            <?php
                                        }
                                        break;
                                    default:
                                        echo '<p class="' . $required_str . '">';
                                        echo current($fields[$required_str]);
                                        echo '</p>';
                                        break;
                                    }
                                }
                            }
                            ?>
                            <?php if ($password == '') { ?>
                                <a href="?comments" class="btn">
                                    <?php _e('Condole', 'cm_translate'); ?>
                                </a>
                                <?php

                                if ($fields['flowers'][0] === '1') { ?>
                                    <a href="?cm-products&cm-order-form&cm_order_product" class="btn">
                                        <?php _e('Flowers', 'cm_translate'); ?>
                                    </a>
                                    <?php
                                }

                                if ($fields['coffee_table'][0] == 'yes') {
                                    ?>
                                    <a href="?ct_form" class="btn">
                                        <?php _e('Coffee Table', 'cm_translate'); ?>
                                    </a>
                                    <?php
                                }

                                if (isset($fields['masscard'][0])) { ?>
                                    <a target="_blank" href="<?= $fields['masscard'][0] ?>" class="btn"
                                       id="toggle_flowers"><?php _e('Mass card', 'cm_translate'); ?></a>
                                    <?php
                                }

                                if (isset($fields['live_stream'][0]) && $fields['live_stream'][0] && isset($fields['live_stream_url'][0]) && $fields['live_stream_url'][0]) { ?>
                                    <a target="_blank" href="<?= $fields['live_stream_url'][0] ?>" class="btn"
                                       id="live_stream_url"><?php _e('Funeral live-stream', 'cm_translate'); ?></a>
                                    <?php
                                }
                            } ?>
                        </td>
                    </tr>
                </table>


                <?php
                $check_password = get_post_meta(get_the_ID(), 'password', true);
                if (!empty($password) && $password == $check_password) { ?>


                    <div class="comments-list family_page">
                        <h3><?php _e('Condolences for the family', 'cm_translate'); ?></h3>
                        <ol class="commentlist">
                            <?php comment_form(array('title_reply' => __('Reply to this condolence', 'cm_translate'), 'title_reply_after' => '</h3><p id="info_text">' . __('This message will be send by mail to the author of the condolence.', 'cm_translate') . '</p>', 'label_submit' => __('Reply', 'cm_translate'))); ?>

                            <?php
                            //Gather comments for a specific page/post
                            $comments = get_comments(array(
                                'post_id' => get_the_ID(),
                                'status' => 'approve' //Change this to the type of comments to be displayed
                            ));

                            //Display the list of comments
                            wp_list_comments(array(
                                'per_page' => 100, //Allow comment pagination
                                'reverse_top_level' => false //Show the latest comments at the top of the list
                            ), $comments);
                            ?>
                        </ol>
                    </div>

                <?php } else { ?>

                    <div class="comments" style="display: <?=cm_get_display_value('comments')?>;">
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
            <?= do_shortcode('[cm_products]'); ?>
            <?= do_shortcode('[cm_order_form]'); ?>
        </div>
    </div>
</div>

<?php
get_footer();

echo ob_get_clean();
?>
