<?php

namespace cm\includes\form;

use cm\includes\register\Custom_Post_Type;

class Metabox{

    public function __construct()
    {
        add_action( 'add_meta_boxes', array($this, 'add_metaboxes') );
        add_action( 'save_post', array($this, 'save_condolence_person'), 1, 2 );
        add_action( 'admin_enqueue_scripts', array($this, 'metabox_css_jquery') );
        //add_action( 'publish_'.Custom_Post_Type::POST_TYPE, array($this, 'send_mail_to_family') );
    }

    /**
     * Add metabox information about departed soul and metabox about information for the family
     */
    public function add_metaboxes(){
        add_meta_box('wpt_condolence_person_location', __('Information about deseaded'), array($this, 'deceased_callback'), Custom_Post_Type::post_type(), 'normal', 'high');
        add_meta_box('wpt_condolence_person_location_side', __('View comments'), array($this, 'password_callback'), Custom_Post_Type::post_type(), 'side', 'default');
    }

    /**
     * Define fields in metabox Information about departed soul
     */
    public function deceased_callback(){
        global $post;

        // Use nonce for verification
        wp_nonce_field( plugin_basename( __FILE__ ), 'deceased_noncename' );
        ?>
        <table class="form-table">
            <tr>
                <td><?php _e('Gender'); ?></td>
                <td class="form-field">
                    <select name="gender">
                        <option>Select gender</option>
                        <option value="Male" <?php echo ($this->get_field_value('gender', $post->ID) == 'Male') ? 'selected' : ''; ?>><?php _e('Male'); ?></option>
                        <option value="Female" <?php echo ($this->get_field_value('gender', $post->ID) == 'Female') ? 'selected' : ''; ?>><?php _e('Female'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php _e('Name'); ?></td>
                <td class="form-field"><input type="text" name="name" value="<?php echo $this->get_field_value('name', $post->ID); ?>"></td>
            </tr>
            <tr>
                <td><?php _e('Family name'); ?></td>
                <td class="form-field"><input type="text" name="familyname" value="<?php echo $this->get_field_value('familyname', $post->ID); ?>"></td>
            </tr>
            <tr>
                <td><?php _e('Birthplace'); ?></td>
                <td class="form-field"><input type="text" name="birthplace" value="<?php echo $this->get_field_value('birthplace', $post->ID); ?>"></td>
            </tr>
            <tr>
                <td><?php _e('Birthdate'); ?></td>
                <td class="form-field"><input type="date" name="birthdate" value="<?php echo $this->get_field_value('birthdate', $post->ID); ?>"></td>
            </tr>
            <tr>
                <td><?php _e('Place of death'); ?></td>
                <td class="form-field"><input type="text" name="placeofdeath" value="<?php echo $this->get_field_value('placeofdeath', $post->ID); ?>"></td>
            </tr>
            <tr>
                <td><?php _e('Date of death'); ?></td>
                <td class="form-field"><input type="date" name="dateofdeath" value="<?php echo $this->get_field_value('dateofdeath', $post->ID); ?>"></td>
            </tr>
            <tr>
                <td><?php _e('Funeral information'); ?></td>
                <td class="form-field"><textarea rows="3" name="funeralinformation" "><?php echo $this->get_field_value('funeralinformation', $post->ID); ?></textarea></td>
            </tr>
            <tr>
                <td><?php _e('Prayer Vigil information'); ?></td>
                <td class="form-field"><textarea rows="3" name="prayervigilinformation"><?php echo $this->get_field_value('prayervigilinformation', $post->ID); ?></textarea></td>
            </tr>
            <tr>
                <td><?php _e('Greeting information'); ?></td>
                <td class="form-field"><textarea rows="3" name="greetinginformation"><?php echo $this->get_field_value('greetinginformation', $post->ID); ?></textarea></td>
            </tr>
            <tr>
                <td><?php _e('Residence'); ?></td>
                <td class="form-field"><textarea rows="3" name="residence"><?php echo $this->get_field_value('residence', $post->ID); ?></textarea></td>
            </tr>
            <tr>
                <td><?php _e('Mass card'); ?></td>
                <td class="form-field">
                    <input id="upload_media" type="text" name="masscard" value="<?php echo $this->get_field_value('masscard', $post->ID); ?>">
                    <input id="upload_media_button" class="button" type="button" value="<?php _e('Choose mass card'); ?>" />
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><b>Relations</b><hr></td>
            </tr>
            <?php
            $relations = get_post_meta( $post->ID, 'relations', false );

            if( !$relations || ( is_array($relations) && empty( $relations[0] ) ) ) {
                ?>
                <tr>
                    <td valign="top"><?php _e('Relation 1'); ?></td>
                    <td class="form-field">
                        <select name="relation_id1" id="relation_id1">
                            <option><?php _e('Select relation type'); ?></option>
                            <option value="Single"><?php _e('Single'); ?></option>
                            <option value="Married"><?php _e('Married'); ?></option>
                            <option value="Other"><?php _e('Other'); ?></option>
                        </select>
                        <button class="add_more"><span class="dashicons dashicons-plus-alt" title="<?php _e('Add more relation'); ?>"></span></button>
                        <div class="relation1_more_information">
                            <input type="text" class="relation1_other" name="relation1_other"
                                   placeholder="<?php _e('Relation name'); ?>">
                            <input type="text" name="relation1_name" placeholder="<?php _e('Name'); ?>">
                            <input type="text" name="relation1_familyname" placeholder="<?php _e('Family name'); ?>">
                            <?php _e('Is alive?'); ?> <input type="checkbox" name="relation1_alive">
                            <select name="relation1_gender">
                                <option>Select gender</option>
                                <option value="Male"><?php _e('Male'); ?></option>
                                <option value="Female"><?php _e('Female'); ?></option>
                            </select>
                        </div>
                    </td>
                </tr>
                <?php
            }else{
                $count = 1;
                $relations = current( $relations );
                foreach( $relations as $relation ){
                    ?>
                    <tr>
                        <td valign="top"><?php _e('Relation '.$count); ?></td>
                        <td class="form-field">
                            <select name="relation_id<?php echo $count; ?>" id="relation_id<?php echo $count; ?>">
                                <option><?php _e('Select relation type'); ?></option>
                                <option value="Single" <?php echo ($relation['type'] == 'Single') ? 'selected' : ''; ?>><?php _e('Single'); ?></option>
                                <option value="Married" <?php echo ($relation['type'] == 'Married') ? 'selected' : ''; ?>><?php _e('Married'); ?></option>
                                <option value="Other" <?php echo ($relation['type'] == 'Other') ? 'selected' : ''; ?>><?php _e('Other'); ?></option>
                            </select>
                            <?php
                            if( $count == 1 ){
                                ?> <button class="add_more"><span class="dashicons dashicons-plus-alt" title="<?php _e('Add more relation'); ?>"></span></button> <?php
                            }else{
                                ?> <span class="dashicons dashicons-dismiss" title="<?php _e('Remove relation'); ?>"></span> <?php
                            }
                            ?>


                            <div class="relation<?php echo $count; ?>_more_information" <?php echo ($relation['type'] == 'Single') ? 'style="display:none;"' : ''; ?>>
                                <input type="text" class="relation<?php echo $count; ?>_other" name="relation<?php echo $count; ?>_other" placeholder="<?php _e('Relation name'); ?>" value="<?php echo $relation['other']; ?>" <?php echo ($relation['type'] == 'Married') ? 'style="display:none;"' : ''; ?>>
                                <input type="text" name="relation<?php echo $count; ?>_name" placeholder="<?php _e('Name'); ?>" value="<?php echo $relation['name']; ?>">
                                <input type="text" name="relation<?php echo $count; ?>_familyname" placeholder="<?php _e('Family name'); ?>" value="<?php echo $relation['familyname']; ?>">
                                <?php _e('Is alive?'); ?> <input type="checkbox" name="relation<?php echo $count; ?>_alive" <?php echo ($relation['alive'] == '1') ? 'checked': ''; ?>>
                                <select name="relation<?php echo $count; ?>_gender">
                                    <option>Select gender</option>
                                    <option value="Male" <?php echo ($relation['gender'] == 'Male') ? 'selected' : ''; ?>><?php _e('Male'); ?></option>
                                    <option value="Female" <?php echo ($relation['gender'] == 'Female') ? 'selected' : ''; ?>><?php _e('Female'); ?></option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <?php
                    $count++;
                }
            }
            ?>

        </table>

        <script>
            jQuery(document).ready(function ($) {
                $('#upload_media_button').click(function (e) {

                    e.preventDefault();

                    //Extend the wp.media object
                    custom_uploader = wp.media.frames.file_frame = wp.media({
                        title: '<?php _e('Choose mass card'); ?>',
                        button: {
                            text: '<?php _e('Choose mass card'); ?>'
                        },
                        multiple: false
                    });

                    //When a file is selected, grab the URL and set it as the text field's value
                    custom_uploader.on('select', function () {
                        attachment = custom_uploader.state().get('selection').first().toJSON();
                        console.log( attachment );
                        jQuery('#upload_media').val(attachment.url);
                    });

                    //Open the uploader dialog
                    custom_uploader.open();

                });

                $(document).on('click', '.add_more', function(e){
                    e.preventDefault();
                    var nummer = $(".form-table tr:last-child select").attr('id').replace ( /[^\d.]/g, '' );
                    nummer = parseInt( nummer ) + 1;

                    $(".form-table").find('tbody')
                        .append($('<tr>')
                            .append($('<td>')
                                .append($('<label>')
                                    .text('<?php _e('Relation'); ?> '+nummer)
                                )
                            )
                            .append($('<td>')
                                .attr('class', 'form-field')

                                .append($('<select>')
                                    .attr('name', 'relation_id'+nummer)
                                    .attr('id', 'relation_id'+nummer)
                                    .append($('<option>')
                                        .attr('value', '')
                                        .html('<?php _e('Select relation type'); ?>')
                                    )
                                    .append($('<option>')
                                        .attr('value', 'Single')
                                        .html('<?php _e('Single'); ?>')
                                    )
                                    .append($('<option>')
                                        .attr('value', 'Married')
                                        .html('<?php _e('Married'); ?>')
                                    )
                                    .append($('<option>')
                                        .attr('value', 'Other')
                                        .html('<?php _e('Other'); ?>')
                                    )
                                )
                                .append($('<span>')
                                    .attr('class', 'dashicons dashicons-dismiss')
                                    .attr('title', '<?php _e('Remove relation'); ?>' )
                                )

                                .append($('<div>')
                                    .attr('class', 'relation'+nummer+'_more_information')

                                    .append($('<input>')
                                        .attr('type', 'text')
                                        .attr('class', 'relation'+nummer+'_other')
                                        .attr('name', 'relation'+nummer+'_other')
                                        .attr('placeholder', '<?php _e('Relation name'); ?>')
                                        //.attr('style', 'display:none')
                                    )
                                    .append($('<input>')
                                        .attr('type', 'text')
                                        .attr('class', 'relation'+nummer+'_name')
                                        .attr('name', 'relation'+nummer+'_name')
                                        .attr('placeholder', '<?php _e('Name'); ?>')
                                        //.attr('style', 'display:none')
                                    )
                                    .append($('<input>')
                                        .attr('type', 'text')
                                        .attr('class', 'relation'+nummer+'_familyname')
                                        .attr('name', 'relation'+nummer+'_familyname')
                                        .attr('placeholder', '<?php _e('Family name'); ?>')
                                        //.attr('style', 'display:none')
                                    )
                                    .append('<?php _e('Is alive?'); ?> ')
                                    .append($('<input>')
                                        .attr('type', 'checkbox')
                                        .attr('class', 'relation'+nummer+'_alive')
                                        .attr('name', 'relation'+nummer+'_alive')
                                    )
                                    .append($('<select>')
                                        .attr('name', 'relation'+nummer+'_gender')
                                        .append($('<option>')
                                            .attr('value', '')
                                            .html('<?php _e('Select a gender'); ?>')
                                        )

                                        .append($('<option>')
                                            .attr('value', 'Male')
                                            .html('<?php _e('Male'); ?>')
                                        )
                                        .append($('<option>')
                                            .attr('value', 'Female')
                                            .html('<?php _e('Female'); ?>')
                                        )
                                    )
                                )
                            )
                        )
                });

                $(document).on('click', '.dashicons-dismiss', function(e){
                    e.preventDefault();
                    if( confirm('<?php _e('Are you sure to delete this relation?'); ?>') ){
                        $(this).parent().parent().remove();
                    }

                });

                $(document).on('load', 'select[id^="relation_id"]', function(e){
                    e.preventDefault();
                    var select_value = $(this).val();
                    var nummer = parseInt( $(this).attr('id').replace ( /[^\d.]/g, '' ) );

                    switch( select_value ){
                        case 'Single':
                            $('.relation'+nummer+'_more_information').hide();
                            break;
                        case 'Married':
                            $('.relation'+nummer+'_more_information').show();
                            $('.relation'+nummer+'_other').hide();
                            break;
                        case 'Other':
                            $('.relation'+nummer+'_more_information').show();
                            $('.relation'+nummer+'_other').show();
                            break;
                        default:
                            $('.relation'+nummer+'_more_information').hide();
                            break;
                    }
                });

                $(document).on('change', 'select[id^="relation_id"]', function(e){
                    e.preventDefault();
                    var select_value = $(this).val();
                    var nummer = parseInt( $(this).attr('id').replace ( /[^\d.]/g, '' ) );

                    switch( select_value ){
                        case 'Single':
                            $('.relation'+nummer+'_more_information').hide();
                            break;
                        case 'Married':
                            $('.relation'+nummer+'_more_information').show();
                            $('.relation'+nummer+'_other').hide();
                            break;
                        case 'Other':
                            $('.relation'+nummer+'_more_information').show();
                            $('.relation'+nummer+'_other').show();
                            break;
                        default:
                            $('.relation'+nummer+'_more_information').hide();
                            break;
                    }
                });
            });

        </script>

        <?php
    }

    /**
     * Define fields in metabox View comments
     */
    public function password_callback(){
        global $post;

        ?>
        <label><?php _e('E-mail', 'cm_translate'); ?></label>
        <input type="text" name="email" value="<?php echo $this->get_field_value('email', $post->ID); ?>">
        <label><?php _e('Password', 'cm_translate'); ?> <a id="generate" href=""><?php _e('Create token', 'cm_translate'); ?></a></label>
        <input id="password" type="text" name="password" value="<?php echo $this->get_field_value('password', $post->ID); ?>">

        <label><?php _e('View comments', 'cm_translate'); ?></label>
        <?php
            $permalink = get_post_permalink( $post->ID );

            if( substr($permalink, -1) == '/' ){
                $permalink = substr($permalink, 0, strlen( $permalink ) - 1 );
            }

            if( $this->get_field_value('password', $post->ID) ){
                $permalink .= '?code='.$this->get_field_value('password', $post->ID);
            }
        ?>
        <input type="text" readonly value="<?php echo $permalink; ?>">
        <a href="<?php echo $permalink ?>" target="_blank"><?php _e('Link to view comments', 'cm_translate'); ?></a>
        <?php
    }

    /**
     * Saving meta fields
     *
     * @param $post_id
     * @param $post
     */
    public function save_condolence_person($post_id, $post){
        global $wpdb;

        // Verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !wp_verify_nonce( $_POST['deceased_noncename'], plugin_basename(__FILE__) )) {
            return $post_id;
        }

        // Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
        // to do anything
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $post_id;


        // Check permissions to edit pages and/or posts
        if ( Custom_Post_Type::post_type() == $_POST['post_type']) {
            if ( !current_user_can( 'edit_page', $post_id ) || !current_user_can( 'edit_post', $post_id ))
                return $post_id;
        }

        // save meta fields
        $postfields = array(
            'name',
            'familyname',
            'birthdate',
            'birthplace',
            'placeofdeath',
            'dateofdeath',
            'funeralinformation',
            'prayervigilinformation',
            'greetinginformation',
            'residence',
            'gender',
            'masscard',
            'password',
            'email'
        );

        foreach( $postfields as $field ){
            update_post_meta( $post_id, $field, $_POST[$field] );
        }

        $relations = array();
        // save relations
        foreach($_POST as $key => $value) {
            if (strpos($key, 'relation_id') === 0) {
                $relation_number = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
                $current_relation = array();
                $current_relation['type'] = $value;
                $current_relation['other'] = $_POST['relation'.$relation_number.'_other'];
                $current_relation['name'] = $_POST['relation'.$relation_number.'_name'];
                $current_relation['familyname'] = $_POST['relation'.$relation_number.'_familyname'];
                $current_relation['alive'] = ($_POST['relation'.$relation_number.'_alive']) ? '1' : '0';
                $current_relation['gender'] = $_POST['relation'.$relation_number.'_gender'];
                $relations[] = $current_relation;
            }
        }

        if( !empty( $relations) ){
            update_post_meta( $post_id, 'relations', $relations );
        }

        $post_title = $_POST['dateofdeath'] .' - '. $_POST['name'] .' '. $_POST['familyname'];

        $post_title_sanitize = sanitize_title($post_title);

        $query = "UPDATE ".$wpdb->posts." SET post_title='".$post_title."', post_name='".$post_title_sanitize."' WHERE ID=".$post_id;

        $wpdb->query($query);

        clean_post_cache( $post_id );
    }

    /**
     * TODO automatisch versturen? voorlopig is action gehide
     * Send email after creating/updating post
     * @param $post_id
     */
    public function send_mail_to_family($post_id) {
        $mail = get_post_meta($post_id, 'email', true);
        $url = get_the_permalink($post_id);
        $password = get_post_meta($post_id, 'password', true);

        if( !empty( $mail ) ){
            wp_mail($mail, __('Condolences', 'cm_translate'), $url.'?code='.$password);
        }

    }

    /**
     * Load css and jquery for metabox
     */
    public function metabox_css_jquery(){
        global $post;
        if ( $post->post_type == Custom_Post_Type::post_type() ) {
            wp_register_style( 'metabox_css', CM_URL . 'css/metabox.css', false, '1.0.0'  );
            wp_enqueue_style( 'metabox_css' );

            wp_register_script( 'metabox_js', CM_URL . 'js/metabox.js', array(), false, true );
            wp_localize_script( 'metabox_js', 'metabox', array( 'ajaxUrl' => get_admin_url() . 'admin-ajax.php') );
            wp_enqueue_script( 'metabox_js' );
            wp_enqueue_media();
        }
    }

    /**
     * Checks meta value
     *
     * @param $field_name meta key
     * @param $post_id post id
     * @return mixed|string returns field value or empty string
     */
    public function get_field_value($field_name, $post_id){
        $meta_value = get_post_meta( $post_id, $field_name, false);

        if( $meta_value ){
            return current($meta_value);
        }

        return '';
    }


}