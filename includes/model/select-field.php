<?php

namespace cm\includes\model;

class Select_Field extends Field {
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
	 * $field = new Select_Field('field_name', true, array(
	 *   'options' => array('value' => 'Label')
	 * ));
	 * ?>
	 * </code>
	 */
	public function __construct( $name, $required = false, $options = array(), $single = true ) {
		$options = wp_parse_args( $options, array(
				'type'    => 'select',
				'choices' => array(),
		) );

		parent::__construct( $name, $required, $options, $single );
	}

	/**
	 * @inheritDoc
	 */
	public function sanitize( $input ) {
		return array_key_exists( $input, $this->get_choices() ) ? $input : null;
	}

	/**
	 * Get list of choices.
	 *
	 * @return array
	 */
	public function get_choices() {
        $choices = $this->options['choices'];

        if ( is_callable( $choices ) ) {
            // late initialization
            $choices = (array) $choices();
        }

		return $choices;
	}

	/**
	 * @inheritDoc
	 */
	public function display( $value ) {
		$choices = $this->get_choices();

		return isset( $choices[$value] ) ? $choices[$value] : $value;
	}

	/**
	 * @inheritDoc
	 */
	public function denormalize( $input ) {
		$choices = $this->get_choices();

		return isset( $choices[$input] ) ? $input : null;
	}

	public function get_field_control( $field_name, $value ) {
		ob_start();
		$attributes = array(
				'name'     => $field_name,
				'id'       => $field_name,
				'required' => $this->is_required() ? 'required' : null,
		);

		$attributes = array_merge($attributes, $this->options['attributes']);

		$choices = $this->get_choices();

		?>
      <select <?= static::get_tag_attributes( $attributes ) ?>>
        <?php if(!$this->is_required() && !empty( $this->get_placeholder() )): ?>
            <option value=""><?= $this->get_placeholder() ?></option>
        <?php endif; ?>
        <?php foreach ( $choices as $choice => $label ): ?>
            <option value="<?= esc_attr( $choice ) ?>" <?= selected( $choice, $value, false ) ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
		<?php
		return ob_get_clean();
	}
}
