<?php
namespace cm\includes\script;

Class Post_Type{

    private $old_post_type;
    private $post_type;
    private $post;

    public function __construct()
    {
        add_action( 'wp_ajax_migrate_posttype', array( $this, 'migrate_posttype') );

        add_action( 'wp_ajax_change_posttype', array($this, 'change_posttype') );
    }

    /**
     * Change post type slug
     */
    public function change_posttype(){
        update_option( 'condolence_cpt_base', sanitize_title_with_dashes( $_POST['post_type'] ) );
    }

    /**
     * Migrate posts from one post type to another
     */
    public function migrate_posttype(){
        $this->old_post_type = $_POST['old_post_type'];
        $this->post_type = $_POST['post_type'];

        $this->get_post();
        $this->change_post_type();
    }

    public function get_post()
    {
        $args = array(
            'post_type' => $this->old_post_type,
            'post_status' => 'publish',
            'posts_per_page' => '1',
        );

        $post = get_posts($args);

        if (sizeof($post) != 0) {
            $this->post = current($post);
        }
    }

    public function change_post_type(){
        set_post_type($this->post->ID, $this->post_type);
    }
}