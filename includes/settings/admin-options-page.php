<?php

namespace cm\includes\settings;

use cm\includes\coffee_table\Coffee_Table_Controller;

class Admin_Options_Page {
    const MENU_SLUG = 'condolence-manager';
    const MENU_OPTIONS_SLUG = 'condolence-manager_options';

    private $tabs;
    private $current_tab;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
		$this->set_current_tab();
	}

	private function set_current_tab() {
        $this->tabs = array(
		        "confirmation" => __( 'Confirmation', 'cm_translate' ),
		        "rename_cpt" => __( 'Rename CPT', 'cm_translate' ),
		);
        $current_tab = isset($_GET['tab'])
            ? sanitize_text_field( $_GET['tab'] )
            : false;
		$this->current_tab =  in_array($current_tab, array_keys($this->tabs))
		    ? $current_tab
		    : array_keys($this->tabs)[0];
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

         $current_submenu_page = add_submenu_page(
            static::MENU_SLUG,
            __( 'Condolence manager options', 'cm_translate' ),
            __( 'Options' ),
            'manage_options',
            static::MENU_OPTIONS_SLUG,
            array($this, 'render_options_page'),
            99
        );

         add_action('load-'.$current_submenu_page, array($this, 'enqueue_script_on_admin_page') );
	}

	public function enqueue_script_on_admin_page() {
		wp_register_style( 'admin-options-page', CM_URL . 'assets/css/admin-options-page.css', false, CM_VERSION );
		wp_enqueue_style( 'admin-options-page' );
	}

	private static function slug_exists($slug) {
    global $wpdb;
    $query = 'SELECT * FROM ' . $wpdb->posts . ' WHERE post_type=%s OR post_name=%s LIMIT 1';
    $sql = $wpdb->prepare($query, $slug, $slug);
    if($wpdb->get_row($sql, 'ARRAY_A')) {
        return true;
    } else {
        return false;
    }
}

	private static function rename_CPT ($new_slug) {
	    global $wpdb;
	    $old_slug = get_option( 'condolence_cpt_base' );
	    $new_slug_sanitized = sanitize_title_with_dashes( $new_slug );
	    if($new_slug == false || $new_slug != $new_slug_sanitized || strlen( $new_slug_sanitized ) > 20) {
	        return new \WP_Error(
	                400,
	                sprintf( esc_html__('The new slug is not valid. %s', 'cm_translate'), "<a href='https://wordpress.com/support/slugs/'>https://wordpress.com/support/slugs/</a>" )
	                );
	    }
	    if($old_slug == $new_slug_sanitized) {
	        return new \WP_Error(
	                400,
	                    esc_html__('The new slug is identical to the old slug.', 'cm_translate')
	                );
	    }
	    $slug_already_existed = self::slug_exists($new_slug_sanitized);
	    if($slug_already_existed) {
	        return new \WP_Error(
	                400,
	                    esc_html__('The new slug already exists for another post type or page slug.', 'cm_translate')
	                );
	    }
	    $query = "UPDATE $wpdb->posts SET post_type=%s WHERE post_type=%s";
	    $sql = $wpdb->prepare( $query, $new_slug_sanitized, $old_slug );
	    $success = $wpdb->query($sql);
	    if($success) {
	        update_option('condolence_cpt_base', $new_slug_sanitized);
	        return true;
	    }
	    return new \WP_Error(
	            500,
	                    esc_html__('The operation failed.', 'cm_translate')
	                );
	}

	public function render_options_page() {
	    $rename_CPT_submit = isset($_POST['rename_CPT_submit']);
	    if ($rename_CPT_submit) {
	        $new_slug = isset($_POST['new-slug']) ? sanitize_text_field($_POST['new-slug']) : false;
	        $result = self::rename_CPT($new_slug);
	        if(is_wp_error($result)) {
	            ?>
                <div class="error notice">
                    <p><?php echo $result->get_error_message(); ?></p>
                </div>
                <?php
	        } else {
	            ?>
                <div class="updated notice">
                    <p><?php echo esc_html__('The slug has been updated.', 'cm_translate'); ?></p>
                </div>
                <?php
	        }
	    }

		$obj = new Coffee_Table_Controller();
		$obj->all_coffee_posts();
		?>

        <h2><?php _e( 'Condolence manager', 'cm_translate' ); ?></h2>

        <h3 class="nav-tab-wrapper">
			<?php
			foreach($this->tabs as $tab_id=>$tab_name) {
			    $this->generate_tab( $tab_id, $tab_name );
			}
			?>
        </h3>

        <div class="wrap">
        <?php if ( $this->current_tab == 'confirmation' ):

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

		<?php if ( $this->current_tab == 'rename_cpt' ): ?>
            <div class="change_post_type">
				<?php
				$post_type = get_option( 'condolence_cpt_base' );
	            ?>
                <div class="notice-warning notice">
                    <p><?php echo esc_html__('Please create a database backup before changing the slug.', 'cm_translate'); ?></p>
                </div>
                <form method="post">
                    <p class="info"><?php _e( 'You can change the <b>custom post type slug</b>.',
							'cm_translate' ) ?></p>
                    <label for="post_type"><?php _e( 'Slug name', 'cm_translate' ); ?></label>
                    <input id="post_type" type='text' name='new-slug' value="<?php echo $post_type; ?>" maxlength="20" required>
                    <input id="btn-posttype" type="submit" class="button" name="rename_CPT_submit"
                           value="<?php _e( 'Change post type', 'cm_translate' ); ?>">
                </form>
            </div>
			<?php
		endif;

		echo '</div>';
	}

	private function generate_tab( $tab_id, $tab_name ) {
		$class = ( $this->current_tab == $tab_id ) ? 'nav-tab-active' : '';
		?>
        <a href="admin.php?page=<?=static::MENU_OPTIONS_SLUG?>&tab=<?php echo $tab_id; ?>"
           class="nav-tab <?php echo $class; ?>">
           <?php echo $tab_name; ?>
        </a>
		<?php
	}
}