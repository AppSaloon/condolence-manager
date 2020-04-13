<?php

namespace cm\includes\model;

use JsonSerializable;
use RuntimeException;

class Price extends Model implements JsonSerializable, Deserializable {
	/** @var string Fallback currency. */
	const DEFAULT_CURRENCY = 'EUR';
	/** @var array|null */
	private static $currencies = null;
	/** @var float */
	private $amount;
	/** @var string */
	private $currency;

	/**
	 * Price constructor.
	 *
	 * @param float  $amount
	 * @param string $currency
	 *
	 * @return void;
	 */
	public function __construct( $amount, $currency = null ) {
		if ( null === $currency ) {
			$currency = static::get_default_currency();
		}

		$this->amount   = $amount;
		$this->currency = $currency;
	}

	/**
	 * @return string
	 */
	public static function get_default_currency() {
		return get_option( 'cm_currency', static::DEFAULT_CURRENCY );
	}

	/**
	 * Runs arguments through `static::display_price()`
	 * and converts symbols to html entities.
	 *
	 * @param float      $amount       the price amount
	 * @param string     $currency_id  currency (3-letter ISO 4217)
	 *
	 * @param array|null $display_args Optional. Display arguments for the price.
	 *
	 * @return string
	 */
	public static function display_price_html( $amount, $currency_id = 'EUR', array $display_args = null ) {
		$display_args = wp_parse_args( $display_args, array(
				'add_symbol'    => true,
				'format_string' => '%1$s%2$s' // 1 = symbol, 2 = price
		) );

		$display_price = static::display_price( $amount, $currency_id, $display_args );

		return htmlentities( $display_price );
	}

	/**
	 * Formats $amount and then prepares it for displaying.
	 *
	 * @param float      $amount       the price amount
	 * @param string     $currency_id  currency (3-letter ISO 4217)
	 *
	 * @param array|null $display_args Optional. Display arguments for the price.
	 *
	 * @return string
	 */
	public static function display_price( $amount, $currency_id = 'EUR', array $display_args = null ) {
		$display_args = wp_parse_args( $display_args, array(
				'add_symbol'    => true,
				'format_string' => '%1$s%2$s' // 1 = symbol, 2 = price
		) );

		$currency        = static::get_currency_by_id( $currency_id );
		$formatted_price = static::format_price( $amount, $currency_id );

		return sprintf(
				$display_args['format_string'],
				$display_args['add_symbol'] ? "{$currency['symbol']} " : '',
				$formatted_price
		);
	}

	/**
	 * Retrieves currency information based on the `$currency_id`.
	 *
	 * @param string $currency_id the currency you need.
	 *
	 * @return array
	 */
	public static function get_currency_by_id( $currency_id ) {
		$currencies = static::get_currencies();

		if ( !array_key_exists( $currency_id, $currencies ) ) {
			throw new RuntimeException( sprintf( 'Invalid currency "%s"', $currency_id ) );
		}

		return $currencies[$currency_id];
	}

	/**
	 * Retrieves currency information from config.
	 *
	 * @return null
	 * @see `plugin_root`/config/currencies.php
	 *
	 */
	public static function get_currencies() {
		/**
		 * Enable filtering this list before potential retrieval.
		 * Useful for adding a caching layer to prevent disk io.
		 *
		 * @param array|null $currencies an array of currency details, or null if not loaded yet.
		 *
		 * @since 1.5.0
		 */
		$currencies = apply_filters( 'cm/currencies', static::$currencies );

		if ( null === $currencies ) {
			$currency_file = CM_BASE_DIR . '/config/currencies.php';

			if ( !is_readable( $currency_file ) ) {
				throw new RuntimeException( sprintf( 'Unable to open %s.', $currency_file ) );
			}

			static::$currencies = include $currency_file;

			return static::get_currencies();
		}

		return $currencies;
	}

	/**
	 * @param float  $amount      amount
	 * @param string $currency_id currency id
	 *
	 * @return string
	 */
	public static function format_price( $amount, $currency_id = 'EUR' ) {
		$currency = static::get_currency_by_id( $currency_id );

		// Could be replaced w/ better alternatives, but might break BC
		$formatted = number_format(
				(float) $amount,
				$currency['decimal_digits'],
				$currency['dec_point'],
				$currency['thousands_sep']
		);

		/**
		 * Price formatting.
		 *
		 * @param string $formatted formatted price.
		 * @param string $amount    raw amount.
		 * @param array  $currency  currency information.
		 *
		 * @since 1.5.0
		 */
		return apply_filters( 'cm/format_price', $formatted, $amount, $currency );
	}

	/**
	 * @inheritDoc
	 */
	public static function schema() {
		return array(
				'amount'   => new Field( 'amount', true, array(
						'type'           => 'float',
						'label'          => __( 'Amount', 'cm_translate' ),
						'normalize_cb'   => static function ( $value ) {
							return static::format_price( $value );
						},
						'denormalize_cb' => static function ( $input ) {
							return static::parse_price( $input );
						}
				) ),
				'currency' => new Select_Field( 'currency', true, array(
						'label'   => __( 'Currency', 'cm_translate' ),
						'choices' => static function () {
							$currencies = static::get_enabled_currencies();

							return array_combine(
									array_keys( $currencies ),
									array_column( $currencies, 'name' )
							);
						}
				) ),
		);
	}

	/**
	 * @param string $amount amount
	 *
	 * @return float
	 */
	public static function parse_price( $amount ) {
		$normalized_price = static::normalize_price( $amount );

		return (float) $normalized_price;
	}

	private static function normalize_price( $price ) {
		// Remove thousand separators and convert to float.
		$comma_pos = strpos( $price, ',' );
		$point_pos = strpos( $price, '.' );

		switch ( strnatcmp( $comma_pos, $point_pos ) ) {
			case 1: // comma as dec. separator
				return str_replace( array( '.', ',' ), array( '', '.' ), $price );
			case - 1: // point as dec. separator
				return str_replace( ',', '', $price );
			case 0:
			default:
				return $price;
		}
	}

	/**
	 * Get the list of enabled currencies.
	 *
	 * @return array
	 */
	public static function get_enabled_currencies() {
		$currencies = static::get_currencies();

		/**
		 * Enabled currencies, used to filter the list of currencies.
		 * An empty array results in showing all of them.
		 *
		 * @param array $currencies_to_filter an array of enabled currencies.
		 *
		 * @since 1.5.0
		 */
		$currencies_to_filter = apply_filters( 'cm/currencies_to_filter', array( 'EUR', 'USD', 'CAD', 'GBP' ) );

		// Return all currencies.
		if ( empty( $currencies_to_filter ) ) {
			return $currencies;
		}

		// Only return filtered currencies.
		return array_filter( $currencies, static function ( $currency ) use ( $currencies_to_filter ) {
			return in_array( $currency['code'], $currencies_to_filter, true );
		} );
	}

	/**
	 * @param bool $escape_html
	 *
	 * @return string
	 */
	public function display( $escape_html = false ) {
		return static::display_price( $this->get_amount(), $this->get_currency() );
	}

	/**
	 * @return float
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * @param mixed $amount
	 *
	 * @return Price
	 */
	public function set_amount( $amount ) {
		$this->amount = static::parse_price( $amount );
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * @param string $currency
	 *
	 * @return Price
	 */
	public function set_currency( $currency ) {
		$this->currency = $currency;
		return $this;
	}

	/**
	 * @return string
	 */
	public function format() {
		return static::format_price( $this->get_amount(), $this->get_currency() );
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize() {
		return array(
			'amount'   => $this->get_amount(),
			'currency' => $this->get_currency(),
		);
	}

	/**
	 * @inheritDoc
	 */
	public static function deserialize( $input = array() ) {
		return new static( $input['amount'], $input['currency'] );
	}
}
