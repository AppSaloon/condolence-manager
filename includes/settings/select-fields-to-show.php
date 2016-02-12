<?php

namespace cm\includes\settings;

class Select_Fields_To_Show{

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
        ?>
        <?php
        $fields = array('Gender', 'Name', 'Family name', 'Birthplace', 'Birthdate', 'Place of death', 'funeral information', 'Prayer Vigil information', 'Greeting information', 'Residence', 'Mass card', 'Relations' );
        ?>

        <h2><?php _e('Condolence manager'); ?></h2>
        <p id="info"><?php _e('Change the layout of the condolence post by reordering the items listed below.'); ?></p>
        <p id="info"><?php _e('Drag and drop the items to your desired order or delete and add items to the list. If the order is as you wish submit the changes.'); ?></p>
        <div class="field_wrap">
                <ul class="ui-sortable hide">
                </ul>

        <ul id="sortable" class="ui-sortable show">
                    <?php

        foreach ($fields as $value) { ?>
            <li class="ui-state-default ui-sortable-handle"><?php echo $value; ?><span id="delete">X</span></li>
        <?php }
        ?>


        </ul>

        <input class="button btn-set-fields" type="submit" value="Submit changes">
        </div>
        <?php




    }
}