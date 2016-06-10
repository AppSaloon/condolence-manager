<?php

namespace cm\includes\script;

use cm\includes\register\Custom_Post_Type;

Class Migrate{

    protected $post;

    protected $meta_keys = array(
        'deceased-first-name-text' => 'name',
        'deceased-last-name-text' => 'familyname',
        'hash-code' => 'password',
        'deceased-place-of-birth-text' => 'birthplace',
        'deceased-date-of-birth-date' => 'birthdate',
        'deceased-place-of-death-text' => 'placeofdeath',
        'deceased-date-of-death-date' => 'dateofdeath',
        'deceased-funeral-info-text' => 'funeralinformation',
        'deceased-wake-info-text' => 'prayervigilinformation',
        'deceased-greeting-info-text' => 'greetinginformation',
        'deceased-city-text' => 'residence',
        'deceased-gender-select' => 'gender',
        'ad_media' => 'masscard'
    );

    public function __construct()
    {
        add_action( 'wp_ajax_migrate_post', array( $this, 'migrate_post') );
    }

    public function migrate_post(){
        $args = array(
            'post_type' => 'cm_obi',
            'post_status' => 'publish',
            'posts_per_page' => '1',
        );

        $post = get_posts($args);

        if( sizeof( $post ) != 0 ){
            $this->post = current($post);

            $this->change_post_type();
            $this->array_meta_fields();
            $this->single_meta_fields();
            $this->update_post_name();
        }

    }

    public function update_post_name(){
        $this->post->post_name = sanitize_title($this->post->post_title);
        $args = array(
            'ID' => $this->post->ID,
            'post_name' => $this->post->post_name
        );

        wp_update_post( $args );
    }

    /**
     * Switch meta key from ad_media to masscard
     */
    public function single_meta_fields(){
        $meta_values = get_post_meta($this->post->ID);

        foreach( $meta_values as $meta_key => $meta_value ){
            $meta_value = current( $meta_value );

            if( $this->meta_keys[$meta_key] == 'gender' ){
                $meta_value = $this->set_first_letter_to_uppercase($meta_value);
            }

            if( $this->meta_keys[$meta_key] == 'birthdate' || $this->meta_keys[$meta_key] == 'dateofdeath'){
                $meta_value = date('Y-m-d', strtotime($meta_value) );
            }

            update_post_meta($this->post->ID, $this->meta_keys[$meta_key], $meta_value);
        }
    }

    /**
     * Make an array from previous meta fields
     */
    public function array_meta_fields(){
        $relations = array();

        $meta_keys = $this->get_meta_keys_used_for_relation();

        if( count( $meta_keys ) != 0 ){
            foreach( $meta_keys as $key ){
                $tmp_array = array();

                $int = preg_replace('/\D+/', '', $key);

                $relation = get_post_meta($this->post->ID, $key, true);

                if( !empty($relation) ){
                    if( $int == "" ){
                        $gender = get_post_meta($this->post->ID, 'partner-gender-select', true);
                        $alive = get_post_meta($this->post->ID, 'partner-passed-away-checkbox', true);

                        $tmp_array['type'] = $this->set_first_letter_to_uppercase($relation);
                        $tmp_array['other'] = get_post_meta($this->post->ID, 'deceased-other-rel-type', true);
                        $tmp_array['name'] = get_post_meta($this->post->ID, 'partner-first-name-text', true);
                        $tmp_array['familyname'] = get_post_meta($this->post->ID, 'partner-last-name-text', true);
                        $tmp_array['alive'] = (!empty($alive)) ? $this->set_alive($alive) : '';
                        $tmp_array['gender'] = (!empty($gender)) ? $this->set_first_letter_to_uppercase($gender) : '';
                    }else{
                        $gender = get_post_meta($this->post->ID, 'partner-gender-select'.$int, true);
                        $alive = get_post_meta($this->post->ID, 'partner-passed-away-checkbox'.$int, true);

                        $tmp_array['type'] = $this->set_first_letter_to_uppercase($relation);
                        $tmp_array['other'] = get_post_meta($this->post->ID, 'deceased-other-rel-type'.$int, true);
                        $tmp_array['name'] = get_post_meta($this->post->ID, 'partner-first-name-text'.$int, true);
                        $tmp_array['familyname'] = get_post_meta($this->post->ID, 'partner-last-name-text'.$int, true);
                        $tmp_array['alive'] = (!empty($alive)) ? $this->set_alive($alive) : '';
                        $tmp_array['gender'] = (!empty($gender)) ? $this->set_first_letter_to_uppercase($gender) : '';
                    }
                    array_push($relations, $tmp_array);
                }
            }
            update_post_meta($this->post->ID, 'relations', $relations);
        }
    }

    public function get_meta_keys_used_for_relation(){
        global $wpdb;
        $meta_keys = array();

        $query = "SELECT distinct(meta_key)
                  FROM ".$wpdb->prefix."postmeta
                  WHERE meta_key LIKE '%deceased-relation-select%'";

        $result = $wpdb->get_results($query);

        if( sizeof($result) != 0 ){
            foreach( $result as $row ){
                array_push($meta_keys, $row->meta_key);
            }
        }

        return $meta_keys;
    }

    /**
     * Change post type to condolences
     */
    public function change_post_type(){
        set_post_type($this->post->ID, Custom_Post_Type::POST_TYPE);
    }

    public function set_first_letter_to_uppercase($gender){
        return ucfirst($gender);
    }

    public function set_alive($alive){
        if( $alive == 'on' ){
            return 0;
        }

        if( $alive == 'off'){
            return 1;
        }
    }
}