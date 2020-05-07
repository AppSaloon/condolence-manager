<?php

namespace cm\includes\settings;

use cm\includes\coffee_table\Coffee_Table_Controller;

class Select_Fields_To_Show {
    const MENU_SLUG = 'condolence-manager';
    const MENU_OPTIONS_SLUG = 'condolence-manager_options';


	public function __construct() {

		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		add_action( 'wp_ajax_set_fields', array( $this, 'ajax_set_fields' ) );
	}

	public function ajax_set_fields() {
		$tableArray = isset( $_REQUEST['tableArray'] ) ? $_REQUEST['tableArray'] : array();
		update_option( 'cm_fields', $tableArray );
		die();
	}

	public static function get_confirmation_settings() {
	    return array(
	            'type' => get_option('cm_option_confirmation_type', 'text'),
	            'text' => get_option(
	                    'cm_option_confirmation_text',
	                    __( 'Thanks for your comment. We appreciate your response.', 'cm_translate')
                ),
	            'page' => get_option('cm_option_confirmation_page'),
	    );
	}

	public function add_admin_page() {
        add_menu_page(
            __( 'Condolence manager', 'cm_translate' ),
            __( 'Condolence manager', 'cm_translate' ),
            'edit_posts',
            static::MENU_SLUG,
            '',
            'dashicons-businessperson',
            10
        );

         add_submenu_page(
            static::MENU_SLUG,
            __( 'Condolence manager options', 'cm_translate' ),
            __( 'Options' ),
            'manage_options',
            static::MENU_OPTIONS_SLUG,
            array($this, 'my_plugin_function'),
            99
        );

		wp_register_script( 'my-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js' );
		wp_enqueue_script( 'my-jquery-ui' );
		wp_enqueue_style( 'style-my-jquery-ui',
			'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );

		wp_register_script( 'drag-and-drop', CM_URL . '/js/drag-and-drop.js', array(), CM_VERSION, true );
		wp_localize_script( 'drag-and-drop', 'dragAndDrop', array( 'ajaxUrl' => get_admin_url() . 'admin-ajax.php' ) );
		wp_enqueue_script( 'drag-and-drop' );

		wp_register_style( 'drag-and-drop-css', CM_URL . 'css/drag-and-drop.css', false, CM_VERSION );
		wp_enqueue_style( 'drag-and-drop-css' );


	}

	public static function get_default_fields() {
	    return array(
			"full_name"              => __( 'Honorary title + full name', 'cm_translate' ),
			"birthplace"             => __( 'Birthplace', 'cm_translate' ),
			"birthdate"              => __( 'Birthdate', 'cm_translate' ),
			"placeofdeath"           => __( 'Place of death', 'cm_translate' ),
			"dateofdeath"            => __( 'Date of death', 'cm_translate' ),
			"funeralinformation"     => __( 'Funeral information', 'cm_translate' ),
			"prayervigilinformation" => __( 'Prayer Vigil information', 'cm_translate' ),
			"greetinginformation"    => __( 'Greeting information', 'cm_translate' ),
			"residence"              => __( 'Residence', 'cm_translate' ),
			"relations"              => __( 'Relations', 'cm_translate' ),
			"_cm_linked_location"    => __( 'Funeral home', 'cm_translate' ),
			"live_stream_description" => __( 'Live-stream description', 'cm_translate' ),
		);
	}

	public static function get_saved_fields() {
	    $saved_fields = get_option( 'cm_fields' );
		return array_filter($saved_fields, function($saved_field) {
		    return in_array($saved_field, array_keys(self::get_default_fields()));
		}, ARRAY_FILTER_USE_KEY);
	}

	public function my_plugin_function() {

		$obj = new Coffee_Table_Controller();
		$obj->all_coffee_posts();
		$saved_fields = self::get_saved_fields();
		$fields     = ( $saved_fields ) ? $saved_fields : self::get_default_fields();
		/**
		 * filter hook to add eventually fildes on backend site
		 */
		$fields = apply_filters( 'conman_prerender_admin_page', $fields );
		?>

        <h2><?php _e( 'Condolence manager', 'cm_translate' ); ?></h2>

        <h3 class="nav-tab-wrapper">
			<?php
			$this->generate_tab( __( 'Field Mapping', 'cm_translate' ), 'field_mapping', true );
			$this->generate_tab( __( 'Confirmation', 'cm_translate' ), 'confirmation' );
			$this->generate_tab( __( 'Rename CPT', 'cm_translate' ), 'rename_cpt' );
			$this->generate_tab( __( 'Update', 'cm_translate' ), 'update' );
			?>
        </h3>

        <div class="wrap">
		<?php if ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'field_mapping' ) ): ?>

            <p class="info"><?php _e( 'Change the layout of the condolence post by reordering the items listed below.',
					'cm_translate' ); ?></p>
            <p class="info"><?php _e( 'Drag and drop the items to your desired order or delete and add items to the list. If the order is as you wish submit the changes.',
					'cm_translate' ); ?></p>
            <div class="field_wrap">
                <ul class="ui-sortable hide <?php if ( $saved_fields ) {
					echo 'border';
				} ?>">
					<?php
					$result = ( $saved_fields ) ? array_diff( self::get_default_fields(), $saved_fields ) : '';
					if ( $result ) {
						foreach ( $result as $key => $value ) {
							?>
                            <li class="ui-state-default ui-sortable-handle"
                                data-value="<?php echo $key; ?>"><?php echo $value; ?><span class="cm_field_mapping_add">+</span></li> <?php
						}
					}
					?>

                </ul>

                <ul id="sortable" class="ui-sortable show">
					<?php
					foreach ( $fields as $key => $value ) {
						?>
                        <li class="ui-state-default ui-sortable-handle"
                            data-value="<?php echo $key; ?>"><?php echo $value; ?><span class="cm_field_mapping_delete">X</span></li> <?php
					}
					?>
                </ul>

                <input class="button btn-set-fields" type="submit"
                       value="<?php _e( 'Submit changes', 'cm_translate' ); ?>">
            </div>

		<?php endif; ?>

        <?php if ( ( isset( $_GET['tab'] ) && $_GET['tab'] == 'confirmation' ) ):

        if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_confirmation'])) {
            update_option(
                    'cm_option_confirmation_type',
                    $_POST['cm_option_confirmation_type'] === 'page' ? 'page' : 'text'
            );
            update_option('cm_option_confirmation_text', sanitize_textarea_field($_POST['cm_option_confirmation_text']));
            update_option('cm_option_confirmation_page', esc_url($_POST['cm_option_confirmation_page']));
        }
        ?>
        <p class="info"><?php _e('Manage the condolence confirmation. This can be a thank you text, or a page to redirect to.', 'cm_translate'); ?></p>
        <form method="post" action="">
            <table class="form-table">
            <tr>
                <th>
                    <label for="cm_option_confirmation_type"><?php _e('Confirmation type','cm_translate'); ?></label>
                </th>
                <td>
                    <?php
                    $confirmation_type = static::get_confirmation_settings()['type'];
 ?>
                    <p><label><input type="radio" name="cm_option_confirmation_type" value="page" <?php checked('page', $confirmation_type);?>><?php _e('Page','cm_translate'); ?></label></p>
                    <p><label><input type="radio" name="cm_option_confirmation_type" value="text" <?php checked('text', $confirmation_type);?>><?php _e('Text','cm_translate'); ?></label></p>
                    <p class="description">
                    <?php _e('Choose what will happen after a user writes a condolence.','cm_translate'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="cm_option_confirmation_page"><?php _e('Confirmation page','cm_translate'); ?></label>
                </th>
                <td>
                   <input type="url" value="<?php echo esc_attr(static::get_confirmation_settings()['page'])?>" name="cm_option_confirmation_page" class="regular-text" id="cm_option_confirmation_page" placeholder="https://www.example.com/thank-you">
                    <p class="description">
                    <?php _e('If you selected "page" as the confirmation type, please enter the URL to the page your users will be redirected to after writing a condolence.','cm_translate'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="cm_option_confirmation_text"><?php _e('Confirmation text','cm_translate'); ?></label>
                </th>
                <td>
                    <textarea name="cm_option_confirmation_text" class="regular-text" id="cm_option_confirmation_text" placeholder="<?php _e('Thank you note', 'cm_translate'); ?>"><?php
                    echo esc_textarea(static::get_confirmation_settings()['text']);
                    ?></textarea>
                    <p class="description">
                    <?php _e('If you selected "text" as the confirmation type, the text in the field above will be displayed to a user after writing a condolence.','cm_translate'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th></th>
                <td>
                    <input type="submit" class="button-primary" value="<?php _e('Submit') ?>" name="btn_confirmation">
                </td>
            </tr>

            </table>

        </form>
        <?php endif; ?>

		<?php if ( ( isset( $_GET['tab'] ) && $_GET['tab'] == 'rename_cpt' ) ): ?>
            <div class="change_post_type">
				<?php
				$post_type = get_option( 'condolence_cpt_base' );
				$posts     = wp_count_posts( $post_type );
				?>
                <form>
                    <p class="info"><?php _e( 'You can change the <b>custom post type slug</b>.',
							'cm_translate' ) ?></p>
                    <label for="post_type"><?php _e( 'Slug name', 'cm_translate' ); ?></label>
                    <input type="hidden" name="old_post_type" id="old_post_type" value="<?php echo $post_type; ?>">
                    <input id="post_type" type='text' name='post_type' value="<?php echo $post_type; ?>">
                    <input id="btn-posttype" type="submit" class="button"
                           value="<?php _e( 'Change post type', 'cm_translate' ); ?>">
					<?php
					if ( $posts->publish !== 0 ) {
						?>
                        <progress id="progress_posttype" max="<?php echo $posts->publish; ?>" value="0"></progress>
						<?php
					}
					?>
                </form>
            </div>


			<?php
			// show migrate script only if there are old posts
			$old_posts       = wp_count_posts( 'cm_obi' );
			$check_old_posts = get_object_vars( $old_posts ) ? true : false;
			if ( $check_old_posts ) {
				if ( $old_posts->publish !== null && $old_posts->publish !== 0 ) {
					?>
                    <br><br>
                    <hr>

                    <div class="migrating">
						<?php
						?>
                        <form method="post">
                            <p class="info"><?php _e( 'Before starting to migrate, be sure that <b>custom post type slug</b> is correct! Because the changes made in this step can\'t be reversed.' ) ?></p>
                            <input type="hidden" id="max_posts" value="<?php echo $old_posts->publish; ?>"/>
                            <input id="btn-migrating" class="button" type="submit" value="Start migrating">
                            <progress id="progress_migrating" max="<?php echo $old_posts->publish; ?>"
                                      value="0"></progress>
                        </form>
                    </div>
					<?php

				}
			}

		endif;

		if ( ( isset( $_GET['tab'] ) && $_GET['tab'] == 'update' ) ):
			?>
            <div class="add_license_key">
				<?php
				if ( isset( $_POST['license_key'] ) ) {
					if ( $this->is_license_key_valid( $_POST['license_key'] ) ) {
						update_option( 'license_key_cm', $_POST['license_key'] );
					} else {
						delete_option( 'license_key_cm' );
						echo '<div class="error">License key <b>' . $_POST['license_key'] . '</b> is not valid!</div>';
					}
				}
				$license_key = ( $license_key = get_option( 'license_key_cm', false ) ) ? $license_key : '';
				?>
                <form method="post">
                    <label for="license_key"><?php _e( 'Add your license key', 'cm_translate' ) ?></label>
                    <input id="license_key" type='text' name='license_key'
                           value="<?php echo $license_key; ?>">
                    <input id="btn-license_key" type="submit" class="button"
                           value="<?php _e( 'Add license key', 'cm_translate' ); ?>">
                </form>
            </div>
            <hr>
            <form method="post" name="cm_additional_btn">
                <button class="cm_add_btn_btn_js">Add button</button>
                <div class="btn_pocket">
					<?php $additional_buttons = get_option( 'cm_additional_btn' );
					if ( empty( $additional_buttons ) ) {
						$additional_buttons = array( array( 'href' => '', 'caption' => '' ) );
					}
					/**
					 * create additional buttons inputs on settings page
					 */
					foreach ( $additional_buttons as $button ) {
						?>
                        <div class="additional_btn_container">
                        <p><label>Insert custom button link</label><input name="additional_btn_href[]"
                                                                          placeholder="http://condolencemanager.com/condolences/"
                                                                          value="<?php echo $button['href']; ?>"
                                                                          type="url">
                        </p>
                        <p><label>Insert custom button caption</label><input name="additional_btn_caption[]"
                                                                             value="<?php echo $button['caption']; ?>"
                                                                             placeholder="Click me!" type="text"></p>
                        </div><?php
					}
					?>
                </div>
                <p><input name="cm_sbm_add_btn" type="submit" class="button button-primary button-large"></p>
            </form>
		<?php
		endif;
		echo '</div>';
	}

	private function generate_tab( $name, $tab_name, $first = false ) {
		$tab = ( ( isset( $_GET['tab'] ) && $_GET['tab'] == $tab_name ) ) ? 'nav-tab-active' : '';

		if ( $first && ! isset( $_GET['tab'] ) ) {
			$tab = 'nav-tab-active';
		}

		?>
        <a href="admin.php?page=<?=static::MENU_OPTIONS_SLUG?>&tab=<?php echo $tab_name; ?>"
           class="nav-tab <?php echo $tab; ?>"><?php echo $name; ?></a>
		<?php
	}

	private function is_license_key_valid( $license_key ) {
		$store_url  = 'http://condolencemanager.com';
		$item_name  = 'Condolence manager plugin';
		$license    = $license_key;
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $item_name ),
			'url'        => home_url(),
		);
		$response   = wp_remote_post( $store_url, array(
			'body'      => $api_params,
			'timeout'   => 15,
			'sslverify' => false,
		) );
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $license_data->license == 'valid' ) {
			return true;
		} else {
			return false;
		}
	}

}
