<?php
$password = isset($_GET['code']) ? $_GET['code'] : '';
$born = false;
$deseased = false;
?>
<?php get_header(); ?>
<?php if (is_single()){ ?>

<table>
    <tr>
        <td class="img">
            <?php
            if (has_post_thumbnail()) {
                the_post_thumbnail();
            }
            ?>
        </td>
        <td class="text">
            <?php
            $required_fields = get_option('cm_fields');
            $fields = get_post_meta(get_the_ID());
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

            if (!$required_fields) {
                $required_fields = cm\includes\settings\Select_Fields_To_Show::$defaultFields;
            }

            foreach ($required_fields as $required => $value) {

                $required_str = strtolower($required);
                $required_str = preg_replace('/\s+/', '', $required_str);
                $gender = current($fields['gender']);

                if ($fields[$required_str]) {
                    switch ($required_str) {


                        case 'masscard':
                            if(!empty(current($fields[$required_str]))) {
                                echo '<a class="btn" target="_blank" href="' . current($fields[$required_str]) . '" >';
                                echo __('Mass card', 'cm_translate');
                                echo '</a>';
                            }
                            break;
                        case 'name':
                            echo '<p class="' . $required_str . '" id="name">';
                            echo current($fields[$required_str]);
                            echo '</p>';
                            break;
                        case 'familyname':
                            echo '<p class="' . $required_str . '" id="name">';
                            echo current($fields[$required_str]);
                            echo '</p>';
                            break;
                        case 'honoraryitle':
                            echo '<p class="' . $required_str . '" id="honoraryitle">';
                            echo current($fields[$required_str]);
                            echo '</p>';
                            break;
                        case 'birthdate':
                            $bornOn = '';
                            $date = current($fields[$required_str]);
                            $pieces = explode("-", $date);
                            $num = intval($pieces[1]);
                            $month = $arraymonth[$num - 1];

                            if ($born == false) {
                                $born = true;
                                $bornOn = __('Born', 'cm_translate');
                            }

                            if(isset($date) && $date != ''){
                                echo '<p class="' . $required_str . '" id="birth">';
                                echo $bornOn . '&nbsp;' . __('on', 'cm_translate') . '&nbsp;' . $pieces[2] . '&nbsp;' . $month . '&nbsp;' . $pieces[0];
                                echo '</p>';
                            }else{
                                echo '</br>';
                            }

                            break;
                        case 'birthplace':
                            $bornOn = '';
                            if ($born == false) {
                                $born = true;
                                $bornOn = __('Born', 'cm_translate');
                            }
                            echo '<p class="' . $required_str . '" id="birth">';
                            echo $bornOn . '&nbsp;' . __('in', 'cm_translate') . '&nbsp;' . current($fields[$required_str]);
                            echo '</p>';
                            break;
                        case 'dateofdeath':
                            $passedAway = '';
                            $date = current($fields[$required_str]);
                            $pieces = explode("-", $date);
                            $num = intval($pieces[1]);
                            $month = $arraymonth[$num - 1];
                            if ($deseased == false) {
                                $deseased = true;
                                $passedAway = __('Passed away', 'cm_translate');
                            }
                            if(isset($date) && $date != ''){
                                echo '<p class="' . $required_str . '" id="death">';
                                echo $passedAway . '&nbsp;' . __('on', 'cm_translate') . '&nbsp;' . $pieces[2] . '&nbsp;' . $month . '&nbsp;' . $pieces[0];
                                echo '</p>';
                            }else{
                                echo '</br>';
                            }
                            break;
                        case 'placeofdeath':
                            $passedAway = '';
                            if ($deseased == false) {
                                $deseased = true;
                                $passedAway = __('Passed away', 'cm_translate');
                            }
                            echo '<p class="' . $required_str . '" id="death">';
                            echo $passedAway . '&nbsp;' . __('in', 'cm_translate') . '&nbsp;' . current($fields[$required_str]);
                            echo '</p>';
                            break;
                        case 'residence':
                            echo '<p class="deceased-subtitle">';
                            echo current($fields[$required_str]);
                            echo '</p>';
                            break;
                        case 'funeralinformation':
                            if (isset($fields[$required_str][0]) && current($fields[$required_str]) != '') {
                                echo '<p class="' . $required_str . '">';
                                echo '<strong>'. __("Funeral information", "cm_translate").': </strong>' . current($fields[$required_str]);
                                echo '</p>';
                            }
                            break;
                        case 'prayervigilinformation':
                            if (isset($fields[$required_str][0]) && current($fields[$required_str]) != '') {
                                echo '<p class="' . $required_str . '">';
                                echo '<strong>'. __("Prayer vigil information", "cm_translate").': </strong>' . current($fields[$required_str]);
                                echo '</p>';
                            }
                            break;
                        case 'greetinginformation':
                            if (isset($fields[$required_str][0]) && current($fields[$required_str]) != '') {
                                echo '<p class="' . $required_str . '">';
                                echo '<strong>'. __("Greeting information", "cm_translate").': </strong>' . current($fields[$required_str]);
                                echo '</p>';
                            }
                            break;
                        case 'relations':
                            $raw_data = current($fields[$required_str]);
                            if (!empty($raw_data)) {
                                $relations = unserialize($raw_data);
                                foreach ($relations as $relation) {
                                    if ($relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Male') {
                                        echo '<p class="alive">';
                                        _e('Beloved husband of', 'cm_translate');
                                        echo '&nbsp;';
                                        echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Married' && $relation['alive'] == '1' && $gender == 'Female') {
                                        echo '<p class="alive">';
                                        _e('Beloved wife of', 'cm_translate');
                                        echo '&nbsp;';
                                        echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Male') {
                                        echo '<p class="alive">';
                                        _e('Beloved husband of the late', 'cm_translate');
                                        echo '&nbsp;';
                                        echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Married' && $relation['alive'] == '0' && $gender == 'Female') {
                                        echo '<p class="alive">';
                                        _e('Beloved wife of the late', 'cm_translate');
                                        echo '&nbsp;';
                                        echo $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Other' && $relation['alive'] == '1' && $gender == 'Male') {
                                        echo '<p class="alive">';
                                        echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Other' && $relation['alive'] == '1' && $gender == 'Female') {
                                        echo '<p class="alive">';
                                        echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Other' && $relation['alive'] == '0' && $gender == 'Male') {
                                        echo '<p class="alive">';
                                        echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    } elseif ($relation['type'] == 'Other' && $relation['alive'] == '0' && $gender == 'Female') {
                                        echo '<p class="alive">';
                                        echo $relation['other'] . '&nbsp;' . $relation['name'] . '&nbsp;' . $relation['familyname'];
                                        echo '</p>';
                                    }

                                }
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
            }
            /**
             * custom hook to add field on single page
             */
            do_action('conman_single_render');
            ?>

             <?php if ($password == '') { 
                           if ( comments_open() !== false ){ ?>
                            <a href="#" class="btn" id="toggle_comment"><?php _e('Condole', 'cm_translate'); ?></a>
                            <?php }

                            if ($fields['coffee_table'][0] == 'yes') {
                                $coffee_gravity_form = true;
                                ?>
                                <a href="#" class="btn" id="toggle_coffee_table"><?php _e('Coffee Table', 'cm_translate'); ?></a>
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
                'per_page' => -1, //Allow comment pagination
                'reverse_top_level' => false //Show the latest comments at the top of the list
            ), $comments);
            ?>
        </ol>
    </div>

<?php } else { ?>

    <div class="comments" style="display: none;">
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

        $fields = array(
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
                'fields' => apply_filters('comment_form_default_fields', $fields)
            )
        );
        ?>
    </div>
<?php } ?>

<?php get_footer(); ?>
