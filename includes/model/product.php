<?php

namespace cm\includes\model;

use cm\includes\register\Product_Type;

class Product extends Custom_Post {
	/** @var Price */
	private $price;

	/**
	 * @inheritDoc
	 */
	public static function get_type() {
		return Product_Type::POST_TYPE;
	}

	/**
	 * @inheritDoc
	 */
	public static function schema() {
		return array(
				'price' => new Field( 'price', true, array(
						'type'           => 'object',
						'class'          => Price::class,
						'serialize_cb'   => 'json_encode',
						'deserialize_cb' => static function ( $value ) {
							$decoded_price = json_decode( $value, true );
							return new Price( $decoded_price['amount'], $decoded_price['currency'] );
						},
						'label'          => __( 'Product price', 'cm_translate' ),
				) ),
		);
	}

	/**
	 * @return Price
	 */
	public function get_price() {
		return $this->price;
	}

	/**
	 * @param Price $price
	 *
	 * @return Product
	 */
	public function set_price( $price ) {
		$this->price = $price;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return get_the_title( $this->get_post() );
	}
}
