<?php
        $password = isset($_GET['password']) ? $_GET['password'] : '';
?>
<?php get_header(); ?>
  <?php if ( is_single() ){ ?>

<table>
    <tr>
        <td class="img">
            <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail();
                }
            ?>
        </td>
        <td class="text">
            <?php

                $required_fields = get_option('cm_fields');
                $fields = get_post_meta(get_the_ID());

                if( !$required_fields ){
                    $required_fields = cm\includes\settings\Select_Fields_To_Show::$defaultFields;
                }

                foreach ($required_fields as $required){

                    $required_str = strtolower($required);
                    $required_str = preg_replace('/\s+/', '', $required_str);
                    $gender = current($fields['gender']);


                    if( $fields[$required_str] ){
                        switch( $required_str ){
                            case 'masscard':
                                echo '<a class="btn" target="_blank" href="'.current($fields[$required_str]).'" >';
                                echo $required;
                                echo '</a>';
                                break;
                            case 'name':
                                echo '<p class="'. $required_str . '" id="name">';
                                echo current($fields[$required_str]);
                                echo '</p>';
                                break;
                            case 'familyname':
                                echo '<p class="'. $required_str . '" id="name">';
                                echo current($fields[$required_str]);
                                echo '</p>';
                                break;
                            case 'birthdate':
                                echo '<p class="'. $required_str . '" id="birth">';
                                echo 'Born on '.current($fields[$required_str]);
                                echo '</p>';
                                break;
                            case 'birthplace':
                                echo '<p class="'. $required_str . '" id="birth">';
                                echo 'in '.current($fields[$required_str]);
                                echo '</p>';
                                break;
                            case 'dateofdeath':
                                echo '<p class="'. $required_str . '" id="death">';
                                echo 'Passed away on '.current($fields[$required_str]);
                                echo '</p>';
                                break;
                            case 'placeofdeath':
                                echo '<p class="'. $required_str . '" id="death">';
                                echo 'in '.current($fields[$required_str]);
                                echo '</p>';
                                break;
                            case 'relations':
                                $pieces = explode("a:6:", current($fields[$required_str]));
                                $count = explode(':', $pieces[0])[1];
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
                                        echo 'Beloved husband of ';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo '</p>';
                                    }elseif($arr[1] == 'Married' && $arr[9] == '1' && $gender == 'Female'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo 'Beloved wife of ';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo '</p>';
                                    }elseif($arr[1] == 'Married' && $arr[9] == '0' && $gender == 'Male'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo 'Beloved husband of the late ';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo '</p>';
                                    } elseif($arr[1] == 'Married' && $arr[9] == '0' && $gender == 'Female'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo 'Beloved wife of the late ';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo '</p>';
                                    } elseif($arr[1] == 'Other' && $arr[9] == '1' && $gender == 'Male'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo ' his ' . $arr[1];
                                        echo '</p>';
                                    } elseif($arr[1] == 'Other' && $arr[9] == '1' && $gender == 'Female'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo ' her ' . $arr[3];
                                        echo '</p>';
                                    }elseif($arr[1] == 'Other' && $arr[9] == '0' && $gender == 'Male'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo ' his late ' . $arr[1];
                                        echo '</p>';
                                    } elseif($arr[1] == 'Other' && $arr[9] == '0' && $gender == 'Female'){
                                        echo '<p class="'. $arr[8] . '">';
                                        echo  $arr[5] . ' ' . $arr[6];
                                        echo ' her late ' . $arr[3];
                                        echo '</p>';
                                    }


                                }


                                break;
                            default:
                                echo '<p class="'. $required_str . '">';
                                echo current($fields[$required_str]);
                                echo '</p>';
                                break;
                        }
                    }

                }
            }
            ?>
            <?php if($password == ''){ ?>
            <a href="#" class="btn" id="toggle_comment"><?php _e('Condole', 'cm_translate'); ?></a>
            <?php } ?>
        </td>
    </tr>
</table>




<?php
 $check_password = get_post_meta(get_the_ID(), 'password', true);
if( !empty($password) && $password == $check_password){ ?>



<div class="comments-list">
    <h3><?php _e('Condolences for the family', 'cm_translation'); ?></h3>
    <ol class="commentlist">
        <?php comment_form( array( 'title_reply' => __('Reply to this condolence', 'cm_translation'), 'title_reply_after'    => '</h3><p id="info_text">'. __('This message will be send by mail to the author of the condolence.', 'cm_translation'). '</p>', 'label_submit' => __('Reply', 'cm_translation') ) ); ?>

        <?php
        //Gather comments for a specific page/post
        $comments = get_comments(array(
            'post_id' => get_the_ID(),
            'status' => 'approve' //Change this to the type of comments to be displayed
        ));

        //Display the list of comments
        wp_list_comments(array(
            'per_page' => 10, //Allow comment pagination
            'reverse_top_level' => false //Show the latest comments at the top of the list
        ), $comments);
        ?>
    </ol>
</div>

<?php }else{ ?>

    <div class="comments" style="display: none;">
        <?php
        $errors = apply_filters('wpice_get_comment_form_errors_as_list',''); // call template tag to print the error list
        if( $errors ){
            echo "<h1 class=\"secondarypage\">"._e('Comment Error', 'cm_translate')."</h1>"; // print the page title
            echo $errors;
        }
        ?>
        <?php comment_form( array( 'title_reply' => __('Leave your condolences for the family', 'cm_translation'), 'title_reply_after'    => '</h3><p id="info_text">'. __('This message is only visible for the family', 'cm_translation'). '</p>', 'label_submit' => __('Condolence', 'cm_translation') ) ); ?>
    </div>

<?php } ?>





<?php get_footer(); ?>
