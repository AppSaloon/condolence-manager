<?php

namespace cm\includes\model;

use RuntimeException;
use WP_Error;
use WP_Post;

class Custom_Post extends Model {
	/**
	 * @var array repo of entities we're tracking
	 */
	protected static $_repository = array();

	/**
	 * @var integer Post ID
	 */
	protected $id;

	/**
	 * @var boolean
	 */
	private $fields_loaded = false;

	/**
	 * @var array
	 */
	private $updated_fields = array();

	protected function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 * @param int  $post_id
	 * @param bool $load_fields
	 *
	 * @return static
	 */
	public static function from_id( $post_id, $load_fields = true ) {
		if ( isset( static::$_repository[$post_id] ) ) {
			$instance = static::$_repository[$post_id];

			if ( $load_fields && !$instance->is_loaded() ) {
				$instance->load_fields();
			}

			return $instance;
		}

		$post = get_post( $post_id );

		if ( is_wp_error( $post ) ) {
			throw new RuntimeException( 'Unable to find that post.' );
		}

		if ( $post->post_type !== static::get_type() ) {
			throw new RuntimeException( 'Unexpected post type.' );
		}

		$instance = new static( $post->ID );

		if ( $load_fields ) {
			$instance->load_fields();
		}

		static::$_repository[$post->ID] = $instance;
		return $instance;
	}

	/**
	 * Get post type name.
	 *
	 * @return string
	 */
	public static function get_type() {
		return 'post';
	}

	/**
	 * Load fields from database.
	 */
	protected function load_fields() {
		/**
		 * @var  $property string
		 * @var  $field    Field
		 */
		foreach ( static::schema() as $property => $field ) {
			$this->load_meta_field( $property, $field );
		}

		$this->fields_loaded = true;
	}

	/**
	 * Load a meta_field.
	 *
	 * @param string $property
	 * @param Field  $field
	 * @param null   $prefix
	 */
	private function load_meta_field( $property, Field $field, $prefix = null ) {
		$meta_key = static::prefix_key( $field->get_name(), $prefix );
		$value    = get_post_meta( $this->get_id(), $meta_key, $field->is_single() );
		$value    = $field->deserialize( $value );

		$this->set( $property, $value );
	}

	/**
	 * @param string $field_name
	 * @param null   $existing_prefix
	 *
	 * @return string
	 */
	public static function prefix_key( $field_name, $existing_prefix = null ) {
		$prefix = null === $existing_prefix ? static::get_prefix() : $existing_prefix . '_';

		return $prefix . $field_name;
	}

	/**
	 * Get prefix for meta keys.
	 *
	 * @return string
	 */
	protected static function get_prefix() {
		return static::get_type() . '_';
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set fields from input (can be $_POST).
	 *
	 * @param array $input
	 */
	public function set_fields_from_input( $input ) {
		/**
		 * @var  $property string
		 * @var  $field    Field
		 */
		foreach ( static::schema() as $property => $field ) {
			$this->set_field_from_input( $property, $field, $input );
		}

		$this->updated_fields = array_unique( $this->updated_fields );
	}

	/**
	 * Set a field from input array.
	 *
	 * @param string $property
	 * @param Field  $field
	 * @param array  $input
	 * @param null   $prefix
	 */
	private function set_field_from_input( $property, Field $field, $input, $prefix = null ) {
		$field_key = static::prefix_key( $field->get_name(), $prefix );

		if ( ! isset( $input[ $field_key ] ) && ! $this->is_field_editable( $property ) ) {
			return;
		}


		$value = isset( $input[ $field_key ] ) ? $input[ $field_key ] : null;
		$value = $field->denormalize( $value );

		$this->set( $property, $value );
		$this->updated_fields[] = $property;
	}

	/**
	 * @return WP_Post
	 */
	public function get_post() {
		return get_post( $this->get_id() );
	}

	/**
	 * Update fields in database.
	 */
	public function update() {
		// Dealing with a new entity here.
		if ( null === $this->get_id() ) {
			$new_id = static::create();

			if ( is_wp_error( $new_id ) ) {
				throw new RuntimeException( sprintf( 'Unable to create post of type "%s".', static::get_type() ) );
			}

			$this->id = $new_id;
		}

		/**
		 * @var  $property string
		 * @var  $field    Field
		 */
		foreach ( static::schema() as $property => $field ) {
			$this->update_meta_field( $property, $field );
		}
	}

	/**
	 * Create post.
	 *
	 * @param array $postarr
	 *
	 * @return int|WP_Error
	 */
	protected static function create( $postarr = array() ) {
		$postarr = wp_parse_args( $postarr, array(
				'post_status' => 'publish',
				'post_type'   => static::get_type(),
		) );

		return wp_insert_post( $postarr );
	}

	/**
	 * Update a meta_field.
	 *
	 * @param string $property
	 * @param Field  $field
	 * @param null   $prefix
	 */
	private function update_meta_field( $property, Field $field, $prefix = null ) {
		$meta_key = static::prefix_key( $field->get_name(), $prefix );
		$value    = $this->get( $property );

		if ( null === $value ) {
			// Cleanup.
			delete_post_meta( $this->get_id(), $meta_key );

			return;
		}

		$value = $field->serialize( $value );

		if ( ! $field->is_single() ) {
			delete_post_meta( $this->get_id(), $meta_key );

			foreach ( $value as $sub_value ) {
				add_post_meta( $this->get_id(), $meta_key, $sub_value );
			}
		} else {
			update_post_meta( $this->get_id(), $meta_key, $value );
		}
	}

	/**
	 * @return bool
	 */
	private function is_loaded() {
		return $this->fields_loaded;
	}

	private function is_field_editable( $property ) {
		return in_array( $property, $this->editable_fields() );
	}

	protected function editable_fields() {
		return array_keys( static::schema() );
	}
}
