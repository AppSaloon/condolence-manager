<?php

namespace cm\includes\model;

use cm\includes\register\Product_Type;
use JsonSerializable;
use WP_Post;

class Order_Line extends Model implements JsonSerializable, Deserializable {
	/** @var integer|null */
	private $product_id;
	/** @var string */
	private $description;
	/** @var integer */
	private $qty;
	/** @var Price */
	private $price;

	/**
	 * Order_Line constructor.
	 *
	 * @param int|null $product_id
	 * @param int      $qty
	 * @param mixed    $price
	 * @param string   $description
	 */
	public function __construct( $product_id, $qty = 1, $price = null, $description = '' ) {
		$this->product_id  = $product_id;
		$this->qty         = $qty;
		$this->price       = $price;
		$this->description = $description;
	}

	/**
	 * @inheritDoc
	 */
	public static function schema() {
		return array(
				'product_id'  => new Select_Field( 'product_id', true, array(
						'label'   => __( 'Product', 'cm_translate' ),
						'description'         => __( 'Please select the product you wish to order from the list.', 'cm_translate' ),
						'choices' => static function () {
							$products = get_posts( array(
									'post_type'      => Product_Type::POST_TYPE,
									'posts_per_page' => - 1,
									'orderby'        => 'post_name',
									'order'          => 'asc',
							) );

							return array_reduce( $products, static function ( $carry, WP_Post $post ) {
								$product = Product::from_id($post->ID);
								$carry[$post->ID] = sprintf(
									'%s (%s)',
									get_the_title( $post->ID ),
									$product instanceof Product ? $product->get_price()->display(true) : ''
								);

								return $carry;
							}, array() );
						},
				) ),
				'description' => new Field( 'description', false, array(
						'label' => __( 'Description', 'cm_translate' ),
						'hidden'         => true,
				) ),
				'qty'         => new Field( 'qty', true, array(
					'type'          => 'number',
					'default_value' => 1,
					'label'         => __( 'Quantity', 'cm_translate' ),
					'attributes'    => array( 'min' => '1', 'step' => '1', 'max' => '99' ),
				) ),
				'price'       => new Field( 'price', true, array(
						'type'           => 'object',
						'hidden'         => true,
						'class'          => Price::class,
						'label'          => __( 'Product price', 'cm_translate' ),
				) ),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize() {
		$return = array(
			'product_id'  => $this->get_product_id(),
			'qty'         => $this->get_qty(),
			'description' => $this->get_description(),
			'price'       => $this->get_price(),
		);


		try {
			$product = Product::from_id($this->get_product_id());
			$return['description'] = $product->get_description();
			$return['price'] = $product->get_price();
		} catch(\Exception $e) {
			// Product might have been deleted.
		}

		return $return;
	}

	public static function deserialize( $input = array() ) {
		return new static(
			$input['product_id'],
			$input['qty'],
            $input['price'] instanceof Price ? $input['price'] : Price::deserialize($input['price']),
			$input['description']
		);
	}

	/**
	 * @return int|null
	 */
	public function get_product_id() {
		return $this->product_id;
	}

	/**
	 * @param int|null $product_id
	 *
	 * @return Order_Line
	 */
	public function set_product_id( $product_id ) {
		$this->product_id = $product_id;
		return $this;
	}

	/**
	 * @return int
	 */
	public function get_qty() {
		return $this->qty;
	}

	/**
	 * @param int $qty
	 *
	 * @return Order_Line
	 */
	public function set_qty( $qty ) {
		$this->qty = $qty;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return Order_Line
	 */
	public function set_description( $description ) {
		$this->description = $description;
		return $this;
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
	 * @return Order_Line
	 */
	public function set_price( $price ) {
		$this->price = $price;
		return $this;
	}

	/**
	 * Get total price.
	 *
	 * @return Price
	 */
	public function get_total() {
		return new Price( $this->get_price()->get_amount() * $this->get_qty(), $this->get_price()->get_currency() );
	}

	public function render_form() {
		ob_start();
		?>
		<div class="form-wrap cm-form-wrap--<?= esc_attr( static::get_type() ) ?>">
			<div class="form-wrap form-wrap--name cm-form-grid">
				<?= $this->get_property_html( 'contact_first_name' ) ?>
				<?= $this->get_property_html( 'contact_last_name' ) ?>
			</div>
			<?= $this->get_property_html( 'remarks' ) ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
