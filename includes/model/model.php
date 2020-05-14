<?php

namespace cm\includes\model;

use RuntimeException;

abstract class Model {
	/**
	 * @param      $property
	 * @param null $prefix
	 *
	 * @return false|string
	 */
	public function get_property_html( $property, $prefix = null ) {
		$field = static::get_field_by_property( $property );

		if ( null === $prefix ) {
			$prefix = static::get_prefix();
		}

		if ( null === $field ) {
			throw new RuntimeException( 'No HTML found for that field.' );
		}

		$value = $this->get( $property );
		$value = $field->get_value_or_default( $value );
		$value = $field->normalize( $value );

		return $field->get_control_html( $prefix, $value );
	}

	/**
	 * @param $property
	 *
	 * @return Field|null
	 */
	public static function get_field_by_property( $property ) {
		$schema = static::schema();

		if ( !isset( $schema[$property] ) ) {
			return null;
		}

		return $schema[$property];
	}

	/**
	 * @return array
	 */
	public static function schema() {
		return array();
	}

	protected static function get_prefix() {
		return '';
	}

	/**
	 * Get $property.
	 *
	 * @param $property
	 *
	 * @return mixed
	 */
	public function get( $property ) {
		$method_name = "get_{$property}";
		$value       = null;

		if ( method_exists( $this, $method_name ) ) {
			$value = $this->$method_name();
		} elseif ( property_exists( $this, $property ) ) {
			$value = $this->$property;
		}

		return $value;
	}

	/**
	 * Set $property to $value.
	 *
	 * @param $property
	 * @param $value
	 */
	public function set( $property, $value ) {
		$method_name = "set_{$property}";

		if ( method_exists( $this, $method_name ) ) {
			$this->$method_name( $value );
		} elseif ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		}
	}

	/**
	 * Returns validation errors.
	 * @return array
	 */
	public function validate() {
		$errors = $this->validate_model();

		// todo field level validation?
		/*foreach(static::schema() as $property => $field) {
			$value = $this->get($property);
			$errors = array_merge($errors, $field->validate($value));
		}*/

		return $errors;
	}

	/**
	 * Function to override for model-wide validations.
	 *
	 * @return array
	 */
	protected function validate_model() {
		return [];
	}

	public static function is_deserializable($class) {
		return in_array(Deserializable::class, class_implements($class));
	}
}
