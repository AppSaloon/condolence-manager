<?php

namespace cm\includes\model;

class Field {
	/** @var array */
	protected $options;
	/** @var string */
	private $name;
	/** @var boolean */
	private $required = false;
	/** @var boolean */
	private $single;

	/**
	 * Field constructor.
	 *
	 * @param string  $name
	 * @param boolean $required
	 * @param array   $options
	 * @param bool    $single
	 *
	 * <code>
	 * <?php
	 * $field = new Field('field_name', true, array(
	 *   'type'  => 'text',
	 *   'class'  => null,
	 *   'label' => 'Field Name',
	 *   'description'   => null,
	 *   'default_value' => null,
	 *   'placeholder'   => null,
	 *   'schema'   => null,
	 *   'hidden'   => false,
	 *   'attributes'   => false,
	 *   'sanitize_cb'   => null,
	 *   'validate_cb'   => null,
	 *   'serialize_cb'   => null,
	 *   'deserialize_cb'   => null,
	 *   'normalize_cb'   => null,
	 *   'denormalize_cb'   => null,
	 * ));
	 * ?>
	 * </code>
	 */
	public function __construct( $name, $required = false, $options = array(), $single = true ) {
		$this->set_name( $name );
		$this->set_required( (bool) $required );
		$this->set_single( $single );

		$this->options = wp_parse_args( $options, array(
				'type'           => 'text',
				'class'          => null,
				'label'          => ucfirst( $name ),
				'description'    => null,
				'default_value'  => null,
				'placeholder'    => null,
				'schema'         => null,
				'hidden'         => false,
				'attributes'     => array(),
				'sanitize_cb'    => null,
				'validate_cb'    => null,
				'serialize_cb'   => null,
				'deserialize_cb' => null,
				'normalize_cb'   => null,
				'denormalize_cb' => null,
		) );
	}

	/**
	 * Validation happens before storing data in database.
	 *
	 * @param      $value
	 * @param null $model
	 *
	 * @return bool|mixed
	 */
	public function validate( $value, $model = null ) {
		// "Required" validation.
		if ( $this->is_required() && empty( $value ) ) {
			return false;
		}


		$validate_cb = $this->options['validate_cb'];

		if ( is_callable( $validate_cb ) ) {
			return $validate_cb( $value, $model );
		}

		switch ( $this->get_type() ) {
			case 'email':
				return filter_var( $value, FILTER_VALIDATE_EMAIL );
				break;
			default:
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function is_required() {
		return $this->required;
	}

	/**
	 * @param bool $required
	 *
	 * @return static
	 */
	public function set_required( $required ) {
		$this->required = $required;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_type() {
		return $this->options['type'];
	}

	/**
	 * @return bool
	 */
	public function is_single() {
		return $this->single;
	}

	/**
	 * @param bool $single
	 *
	 * @return static
	 */
	public function set_single( $single ) {
		$this->single = $single;
		return $this;
	}

	/**
	 * Serialization happens before storing values in the database.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function serialize( $input ) {
		if ( ! $this->is_single() && wp_is_numeric_array( $input ) ) {
			return array_map( array( $this, 'serialize' ), $input );
		}

		if ( $this->has_callback( 'serialize_cb' ) ) {
			$this->do_callback( $input, 'serialize_cb' );
		}

		if ( $this->get_type() === 'object' ) {
			return json_encode( $input, JSON_OBJECT_AS_ARRAY );
		}

		return $input;
	}

	/**
	 * Do callback if one exists.
	 *
	 * @param $value
	 * @param $callback_name
	 *
	 * @return mixed
	 */
	protected function do_callback( $value, $callback_name ) {
		$callback = $this->options[$callback_name];

		return is_callable( $callback ) ? $callback( $value ) : $value;
	}

	/**
	 * Deserialize the field value after retrieving from the database.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function deserialize( $value ) {
		if ( ! $this->is_single() ) {
			if ( wp_is_numeric_array( $value ) ) {
				return array_map( array( $this, 'deserialize' ), $value );
			}
		}

		if ( $this->has_callback( 'deserialize_cb' ) ) {
			return $this->do_callback( $value, 'deserialize_cb' );
		}

		if ( $this->get_type() === 'object' ) {
			// Deserialize object
			$decoded = @json_decode( $value, true );

			if ( is_array( $decoded ) ) {
				$value = $decoded;

				if ( ( $class = $this->get_class() ) && Model::is_deserializable( $class ) ) {
					/** @var $class Deserializable */
					$value = $class::deserialize( $value );
				}
			}
		}

		return $value;
	}

	/**
	 * Checks if callback exists.
	 *
	 * @param $callback_name
	 *
	 * @return boolean
	 */
	protected function has_callback( $callback_name ) {
		$callback = $this->options[$callback_name];

		return is_callable( $callback );
	}

	/**
	 * @return bool
	 */
	public function has_sub_fields() {
		return ! empty( $this->options['class'] ) && is_subclass_of( $this->options['class'], Model::class );
	}

	/**
	 * @return array
	 */
	public function get_schema() {
		$class = $this->get_class();

		return isset( $class ) ? $class::schema() : null;
	}

	/**
	 * @return Model
	 */
	public function get_class() {
		return $this->options['class'];
	}

	/**
	 * Denormalize user input into field value.
	 *
	 * @param $input
	 *
	 * @return mixed
	 */
	public function denormalize( $input ) {
		if ( ! $this->is_single() ) {
			$input = (array) $input;

			if ( wp_is_numeric_array( $input ) ) {
				return array_map( array( $this, 'denormalize' ), $input );
			}
		}

		if ( $this->has_callback( 'denormalize_cb' ) ) {
			return $this->do_callback( $input, 'denormalize_cb' );
		}

		// Denormalize sub fields.
		if ( $this->has_sub_fields() ) {
			$schema     = $this->get_schema();
			$properties = array_keys( $schema );

			$fields = array_combine(
				$properties,
				array_map( static function ( $property ) use ( &$schema, &$input ) {
					/** @var Field $field */
					$field = $schema[ $property ];
					$value = isset( $input[ $property ] ) ? $input[ $property ]  : null;

					return $field->denormalize( $value );
				}, $properties )
			);

			if ( ( $class = $this->get_class() ) && Model::is_deserializable( $class ) ) {
				/** @var $class Deserializable */
				return $class::deserialize( $fields );
			}

			return $fields;
		}

		return $input;
	}

	/**
	 * Sanitize the user input.
	 *
	 * @param $input
	 *
	 * @return string|null
	 */
	public function sanitize( $input ) {

		if ( $this->has_callback( 'sanitize_cb' ) ) {
			return $this->do_callback( $input, 'sanitize_cb' );
		}

		switch ( $this->get_type() ) {
			case 'string':
				$input = sanitize_text_field( $input );
				break;
		}

		// Set to null
		$input = $input === '' ? null : $input;

		return $input;
	}

	/**
	 * @param string $prefix
	 * @param null $value
	 *
	 * @param string $parent
	 *
	 * @param null $field_key
	 *
	 * @return false|string
	 */
	public function get_control_html( $prefix = '', $value = null, $parent = '', $field_key = null ) {
		wp_enqueue_style( 'cm/forms' );

		$field_name = $prefix . $this->get_name();

		if ( null !== $field_key ) {
			$field_name .= "[{$field_key}]";
		}

		if ( ! empty( $parent ) ) {
			$field_name = sprintf( '%s[%s]', $parent, $field_name );
		}

		$field_id = str_replace( array( '[', ']' ), array( '_', '_' ), $field_name );

		// Field not singular.
		if ( ! $this->is_single() && wp_is_numeric_array( $value ) ) {
			$values = $value;
			if ( $this->is_required() && count( $values ) === 0 ) {
				$values = array( null );
			}

			return array_reduce( array_keys( $values ), function ( $carry, $key ) use ( &$values, $parent, $prefix ) {
				$carry .= $this->get_control_html( $prefix, $values[ $key ], $parent, $key );

				return $carry;
			}, '' );
		}

		$value = $this->get_value_or_default( $value );

		ob_start();
		?>
        <div
                class="form-wrap cm-form-wrap cm-form-wrap--<?= esc_attr( $this->get_name() ) ?><?= $this->is_required() ? ' required' : '' ?>">
			<?php if ( ( $label = $this->get_label() ) && ! empty( $label ) ): ?>
                <label for="<?= esc_attr( $field_id ) ?>"><?= esc_html( $label ) ?></label>
			<?php endif; ?>
            <div class="form-field cm-form-field cm-form-field--<?= esc_attr( $this->get_name() ) ?>">
				<?php if ( $this->has_sub_fields() ): ?>
                    <div class="cm-form-field--sub-fields">
						<?php
						/** @var $field Field */
						foreach ( $this->get_schema() as $property => $field ) {
							if ( $field->is_hidden() ) {
								continue;
							}

							$field_value = isset( $value[ $property ] ) ? $value[ $property ] : null;
							echo $field->get_control_html( null, $field_value, $field_name );
						}
						?>
                    </div>
				<?php else: ?>
					<?= $this->get_field_control( $field_name, $value ) ?>
				<?php endif; ?>
				<?php if ( ( $description = $this->get_description() ) && ! empty( $description ) ): ?>
                    <p class="description"><?= esc_html( $description ) ?></p>
				<?php endif; ?>
            </div>
        </div>
		<?php

		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return static
	 */
	public function set_name( $name ) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_label() {
		return $this->options['label'];
	}

	/**
	 * @return bool
	 */
	public function is_hidden() {
		return (bool) $this->options['hidden'];
	}

	/**
	 * Get HTML for the field control.
	 *
	 * @param $field_name
	 * @param $value
	 *
	 * @return false|string
	 */
	public function get_field_control( $field_name, $value ) {
		ob_start();

		$field_id = str_replace( array( '[', ']' ), array( '_', '_' ), $field_name );

		$type_map = array(
				'text'   => 'text',
				'number' => 'number',
				'email'  => 'email',
				'url'    => 'url',
		);

		$attributes = array(
				'type'        => isset( $type_map[$this->get_type()] ) ? $type_map[$this->get_type()] : 'text',
				'placeholder' => !empty( $this->get_placeholder() ) ? $this->get_placeholder() : null,
				'name'        => $field_name,
				'id'          => $field_id,
				'value'       => $value,
				'required'    => $this->is_required() ? 'required' : null,
		);

		$attributes = array_merge($attributes, $this->options['attributes']);

		if ( $this->get_type() === 'longtext' ):?>
        <textarea <?= static::get_tag_attributes( $attributes ) ?>><?= esc_textarea( $value ); ?></textarea>
		<?php else: ?>
        <input <?= static::get_tag_attributes( $attributes ) ?> />
		<?php endif;
		return ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function get_placeholder() {
		return $this->options['placeholder'];
	}

	/**
	 * Helper function to compile the attributes for a tag.
	 *
	 * @param array $attributes
	 *
	 * @return string
	 */
	protected static function get_tag_attributes( $attributes = array() ) {
		return array_reduce( array_keys( $attributes ), static function ( $carry, $attribute ) use ( $attributes ) {
			$value = $attributes[$attribute];

			if ( isset( $value ) ) {
				$carry .= sprintf( ' %s="%s"', $attribute, esc_attr( $value ) );
			}

			return $carry;
		}, '' );
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->options['description'];
	}

	/**
	 * Normalize the field value for form fields.
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function normalize( $value ) {
		if ( ! $this->is_single() && wp_is_numeric_array( $value ) ) {
			return array_map( array( $this, 'normalize' ), $value );
		}

		if ( $this->has_callback( 'normalize_cb' ) ) {
			return $this->do_callback( $value, 'normalize_cb' );
		}

		// Normalize sub fields.
		if ( $this->has_sub_fields() ) {
			$schema     = $this->get_schema();
			$properties = array_keys( $schema );

			return array_combine(
					$properties,
					array_map( static function ( $property ) use ( $schema, $value ) {
						/** @var Field $field */
						$field       = $schema[$property];
						$field_value = $value instanceof Model ? $value->get( $property ) : null;
						$field_value = $field->get_value_or_default( $field_value );

						return $field->normalize( $field_value );
					}, $properties )
			);
		}

		return $value;
	}

	/**
	 * Either get the value, or return the default value if a required field.
	 *
	 * @param mixed $value
	 *
	 * @return string|null
	 */
	public function get_value_or_default( $value = null ) {
		if ( !empty( $value ) ) {
			return $value;
		}

		if ( $this->is_required() && !empty( $this->get_default_value() ) ) {
			return $this->get_default_value();
		}

		return $value;
	}

	/**
	 * @return string
	 */
	public function get_default_value() {
		return $this->options['default_value'];
	}
}
