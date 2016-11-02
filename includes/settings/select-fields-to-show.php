<?php

namespace cm\includes\settings;

use cm\includes\koffie_tafel\Koffie_Tafel_Controller;
use cm\includes\koffie_tafel\Koffie_Tafel_Model;
use cm\includes\koffie_tafel\Menu_Page_View;


class Select_Fields_To_Show{

    public static $defaultFields = array('Gender', 'Name', 'Family name', 'Birthplace', 'Birthdate', 'Place of death', 'Date of death', 'Funeral information', 'Prayer Vigil information', 'Greeting information', 'Residence', 'Mass card', 'Relations' );

    public function __construct()
    {
        add_action( 'admin_menu', array($this, 'add_admin_page') );

        add_action( 'wp_ajax_set_fields', array( $this, 'ajax_set_fields') );
    }

    public function ajax_set_fields(){
        $tableArray = isset($_REQUEST['tableArray']) ? $_REQUEST['tableArray'] : array();
        update_option('cm_fields', $tableArray);
        die();
    }
    public function add_admin_page(){
        add_menu_page(__('Condolence manager'), __('Condolence manager'), 'manage_options', 'condolence-manager', array($this, 'my_plugin_function'));
        wp_register_script('my-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
        wp_enqueue_script( 'my-jquery-ui' );
        wp_enqueue_style( 'style-my-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );

        wp_register_script( 'drag-and-drop', CM_URL .  '/js/drag-and-drop.js', array(), false, true  );
        wp_localize_script( 'drag-and-drop', 'dragAndDrop', array( 'ajaxUrl' => get_admin_url() . 'admin-ajax.php') );
        wp_enqueue_script( 'drag-and-drop' );

        wp_register_style( 'drag-and-drop-css', CM_URL . 'css/drag-and-drop.css', false, '1.0.0'  );
        wp_enqueue_style( 'drag-and-drop-css' );


    }

    public function my_plugin_function(){
        $obj = new Koffie_Tafel_Controller();
        $obj->all_koffie_posts();
	    $tableArray = get_option('cm_fields');
        $fields = ($tableArray) ? $tableArray : self::$defaultFields;
        ?>

        <h2><?php _e('Condolence manager'); ?></h2>
        <p class="info"><?php _e('Change the layout of the condolence post by reordering the items listed below.'); ?></p>
        <p class="info"><?php _e('Drag and drop the items to your desired order or delete and add items to the list. If the order is as you wish submit the changes.'); ?></p>
        <div class="field_wrap">
            <ul class="ui-sortable hide <?php if( $tableArray ){echo 'border'; } ?>">
                <?php
                $result = ($tableArray) ? array_diff(self::$defaultFields, $tableArray) : '';
                if( $result ){
                    foreach ($result as $value) {
                        ?> <li class="ui-state-default ui-sortable-handle"><?php echo $value; ?><span id="add">+</span></li> <?php
                    }
                }
                ?>

            </ul>

            <ul id="sortable" class="ui-sortable show">
            <?php
            foreach ($fields as $value) {
                ?> <li class="ui-state-default ui-sortable-handle"><?php echo $value; ?><span id="delete">X</span></li> <?php
            }
            ?>
            </ul>

            <input class="button btn-set-fields" type="submit" value="Submit changes">
        </div>

        <br><br>
        <hr>

        <div class="change_post_type">
            <?php
            $post_type = get_option( 'condolence_cpt_base' );
            $posts = wp_count_posts($post_type);
                ?>
                <form>
                    <p class="info"><?php _e('You can change the <b>custom post type slug</b>.')?></p>
                    <label for="post_type"><?php _e('Slug name'); ?></label>
                    <input type="hidden" name="old_post_type" id="old_post_type" value="<?php echo $post_type; ?>">
                    <input id="post_type" type='text' name='post_type' value="<?php echo $post_type; ?>">
                    <input id="btn-posttype" type="submit" class="button" value="<?php _e('Change post type'); ?>">
                    <?php
                    if( $posts->publish !== 0 ){
                        ?>
                        <progress id="progress_posttype" max="<?php echo $posts->publish; ?>" value="0"></progress>
                        <?php
                    }
                    ?>
                </form>
        </div>

        <?php
        // show migrate script only if there are old posts
        $old_posts = wp_count_posts('cm_obi');

        if( $old_posts->publish !== NULL && $old_posts->publish !== 0 ){
            ?>
            <br><br>
            <hr>

            <div class="migrating">
                <?php
                  ?>
                <form method="post">
                    <p class="info"><?php _e('Before starting to migrate, be sure that <b>custom post type slug</b> is correct! Because the changes made in this step can\'t be reversed.')?></p>
                    <input type="hidden" id="max_posts" value="<?php echo $old_posts->publish; ?>" />
                    <input id="btn-migrating" class="button" type="submit" value="Start migrating">
                    <progress id="progress_migrating" max="<?php echo $old_posts->publish; ?>" value="0"></progress>
                </form>
            </div>
            <?php

        }
	    $menu_page = new Menu_Page_View();
	    $menu_page->cm_sub_menu_callback();
    }

}