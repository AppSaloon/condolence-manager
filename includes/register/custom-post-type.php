<?php

namespace cm\includes\register;

use cm\includes\comments\Inline_Comment_Error;
use cm\includes\form\Metabox;
use cm\includes\settings\Admin_Options_Page;

class Custom_Post_Type{
    public $post_type;

    public function __construct()
    {
        $this->default_value();
        add_action( 'init', array($this, 'register_post_type') );
        add_shortcode( 'condolence_overview',  array($this,'condolence_shortcode'));
    }

    public function default_value(){
        $value = get_option( 'condolence_cpt_base' );

        if( empty( $value ) ){
            update_option('condolence_cpt_base', 'condolences');
        }
    }

    public static function post_type(){
	    return get_option( 'condolence_cpt_base' );
    }

    public function register_post_type(){
        // Set UI labels for Custom Post Type
        $labels = array(
            'name'                => _x( 'Condolences', 'Post Type General Name', 'cm_translate' ),
            'singular_name'       => _x( 'Condolence', 'Post Type Singular Name', 'cm_translate' ),
            'menu_name'           => __( 'Condolences', 'cm_translate' ),
            'parent_item_colon'   => __( 'Parent Condolence', 'cm_translate' ),
            'all_items'           => __( 'Condolences', 'cm_translate' ),
            'view_item'           => __( 'View Condolence', 'cm_translate' ),
            'add_new_item'        => __( 'Add New Condolence', 'cm_translate' ),
            'add_new'             => __( 'Add New', 'cm_translate' ),
            'edit_item'           => __( 'Edit Condolence', 'cm_translate' ),
            'update_item'         => __( 'Update Condolence', 'cm_translate' ),
            'search_items'        => __( 'Search Condolence', 'cm_translate' ),
            'not_found'           => __( 'Not Found', 'cm_translate' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'cm_translate' ),
        );

        // Set other options for Custom Post Type
        $args = array(
            'label'               => _x( 'Condolences', 'Post Type Label Name', 'cm_translate'),
            'description'         => _x( 'Condolences', 'Post Type Description', 'cm_translate' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'thumbnail' , 'comments'),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => Admin_Options_Page::MENU_SLUG,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 1,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'capability_type'     => 'post',
            'rewrite'             => array('slug'=>'','with_front'=>false),
            'show_in_rest'        => true
        );

        register_post_type( static::post_type(), $args );

        flush_rewrite_rules();

        new Metabox();

        new Inline_Comment_Error();
    }

    public function removeWhitespace($buffer)
    {
        return preg_replace('~>\s*\n\s*<~', '><', $buffer);
    }

    public function condolence_shortcode( $atts ) {
        if(!is_admin()) {
            $permalink = get_permalink();
            $paged = 1;
            $page = '';
            $arg = shortcode_atts(array(
                'posts_per_page' => get_option('posts_per_page'),
                'location' => 'all',
                'pagination' => false,
            ), $atts);


            $args = array(
            	'post_type' => static::post_type(),
	            'posts_per_page' => $arg['posts_per_page'],
            );

            if($arg['location'] !== 'all') {
            	$args['meta_key'] = Location_Type::META_KEY;
            	$args['meta_value'] = $arg['location'];
            }

            // todo wut?
	        if($arg['pagination'] === 'true'){
		        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		        if(substr($url, -1) === '/'){
			        $url = substr($url, 0, -1);
		        }

		        $end = explode('/', $url);

		        if(is_numeric(end($end)) ){
			        $paged = end($end);
			        $args['paged'] = $end;
		        }

	        }

            $posts = query_posts($args);
            ob_start();
            include CM_BASE_DIR . '/includes/templates/archive.php';
            $content = ob_get_clean();
            wp_reset_query();
            $result = preg_replace('!\s+!smi', ' ', $content);

            return $result;
        }
    }


}
