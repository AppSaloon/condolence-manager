<?php
namespace cm\includes\settings;

class Permalinks{

    public function __construct()
    {
        add_action( 'load-options-permalink.php', array($this, 'condolence_load_permalinks') );
    }

    public function condolence_load_permalinks()
    {
        if( isset( $_POST['condolence_cpt_base'] ) )
        {
            update_option( 'condolence_cpt_base', sanitize_title_with_dashes( $_POST['condolence_cpt_base'] ) );
        }

        // Add a settings field to the permalink page
        add_settings_field( 'condolence_cpt_base', __( 'Condolences Base' ), array($this, 'condolence_field_callback'), 'permalink', 'optional' );
    }

    function condolence_field_callback()
    {
        $value = get_option( 'condolence_cpt_base' );
        echo '<input type="text" value="' . esc_attr( $value ) . '" name="condolence_cpt_base" id="condolence_cpt_base" class="regular-text" />';
    }
}