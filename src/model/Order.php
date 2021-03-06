<?php

namespace appsaloon\cm\model;

use appsaloon\cm\register\Custom_Post_Type;
use appsaloon\cm\register\Order_Type;
use WP_Post;

class Order extends Custom_Post {
	/** @var Order_Line[] */
	private $order_lines = array();

    /** @var string */
	private $ribbon_text;

	/** @var integer */
	private $deceased_id;

	/** @var string */
	private $contact_email;

	/** @var string */
	private $contact_phone;

	/** @var string */
	private $contact_first_name;

	/** @var string */
	private $contact_last_name;

	/** @var string */
	private $address_house_number;

	/** @var string */
	private $address_line;

	/** @var string */
	private $address_postal_code;

	/** @var string */
	private $address_city;

	/** @var string */
	private $company_name;

	/** @var string */
	private $company_vat;

	/** @var string */
	private $remarks;

	public function __construct($id = null)
    {
        if (isset($id)) {
            parent::__construct($id);
        }
    }

    /**
	 * @inheritDoc
	 */
	public static function get_type() {
		return Order_Type::POST_TYPE;
	}

	/**
	 * @inheritDoc
	 */
	public static function schema() {
		return array(
				'order_lines'         => new Field( 'order_lines', true, array(
						'type'           => 'object',
						'class'          => Order_Line::class,
						'label'          => __( 'Products', 'cm_translate' ),
				), false ),
                'ribbon_text'         => new Field( 'ribbon_text', false, array(
                    'type'  => 'text',
                    'label' => __( 'Ribbon text', 'cm_translate' ),
                ) ),
				'deceased_id'         => new Select_Field( 'deceased_id', true, array(
						'label'       => __( 'Linked condolence', 'cm_translate' ),
						'description' => __( 'Select the person linked to this order in the select box.', 'cm_translate' ),
						'placeholder' => __( 'Please pick a condolence', 'cm_translate' ),
						'choices'     => static function () {
							$people = get_posts( array(
									'post_type'      => Custom_Post_Type::post_type(),
									'posts_per_page' => - 1,
									'orderby'        => 'post_name',
									'order'          => 'asc',
							) );

							return array_reduce( $people, static function ( $carry, WP_Post $person ) {
								$carry[$person->ID] = get_the_title( $person->ID );

								return $carry;
							}, array() );
						}
				) ),
				'contact_email'       => new Field( 'contact_email', true, array(
						'type'  => 'email',
						'label' => __( 'Email Address', 'cm_translate' ),
				) ),
				'contact_phone'       => new Field( 'contact_phone', false, array(
						'label' => __( 'Phone number', 'cm_translate' ),
				) ),
				'contact_first_name'  => new Field( 'contact_first_name', true, array(
						'label' => __( 'First name', 'cm_translate' ),
				) ),
				'contact_last_name'   => new Field( 'contact_last_name', true, array(
						'label' => __( 'Last name', 'cm_translate' ),
				) ),
				'address_line'        => new Field( 'address_line', true, array(
						'label' => __( 'Address line', 'cm_translate' ),
				) ),
				'address_house_number' => new Field( 'address_house_number', true, array(
						'label' => __( 'House number', 'cm_translate' ),
				) ),
				'address_postal_code' => new Field( 'address_postal_code', true, array(
						'label' => __( 'Postal code', 'cm_translate' ),
				) ),
				'address_city'        => new Field( 'address_city', true, array(
						'label' => __( 'City', 'cm_translate' ),
				) ),
				'company_name'        => new Field( 'company_name', false, array(
						'label' => __( 'Company name', 'cm_translate' ),
						'description' => __( 'Enter the company name if applicable.', 'cm_translate' ),
				) ),
				'company_vat'         => new Field( 'company_vat', false, array(
						'label' => __( 'Company Vat', 'cm_translate' ),
						'description' => __( 'Enter the company vat number if applicable.', 'cm_translate' ),
				) ),
				'remarks'             => new Field( 'remarks', false, array(
						'type'  => 'longtext',
						'label' => __( 'Remarks', 'cm_translate' ),
				) ),
		);
	}

	/**
	 * @return Order_Line[]
	 */
	public function get_order_lines() {
		return $this->order_lines;
	}

	/**
	 * @param Order_Line[] $order_lines
	 *
	 * @return Order
	 */
	public function set_order_lines( $order_lines ) {
		$this->order_lines = $order_lines;
		return $this;
	}

    /**
     * @return string
     */
	public function get_ribbon_text() {
	    return $this->ribbon_text;
    }

    /**
     * @param $ribbon_text
     *
     * @return $this
     */
    public function set_ribbon_text($ribbon_text) {
	    $this->ribbon_text = $ribbon_text;
	    return $this;
    }

	/**
	 * @return int
	 */
	public function get_deceased_id() {
		return $this->deceased_id;
	}

	/**
	 * @param int $deceased_id
	 *
	 * @return Order
	 */
	public function set_deceased_id( $deceased_id ) {
		$this->deceased_id = $deceased_id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_contact_email() {
		return $this->contact_email;
	}

	/**
	 * @param string $contact_email
	 *
	 * @return Order
	 */
	public function set_contact_email( $contact_email ) {
		$this->contact_email = $contact_email;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_contact_phone() {
		return $this->contact_phone;
	}

	/**
	 * @param string $contact_phone
	 *
	 * @return Order
	 */
	public function set_contact_phone( $contact_phone ) {
		$this->contact_phone = $contact_phone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_contact_first_name() {
		return $this->contact_first_name;
	}

	/**
	 * @param string $contact_first_name
	 *
	 * @return Order
	 */
	public function set_contact_first_name( $contact_first_name ) {
		$this->contact_first_name = $contact_first_name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_contact_last_name() {
		return $this->contact_last_name;
	}

	/**
	 * @param string $contact_last_name
	 *
	 * @return Order
	 */
	public function set_contact_last_name( $contact_last_name ) {
		$this->contact_last_name = $contact_last_name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_address_house_number() {
		return $this->address_house_number;
	}

	/**
	 * @param string $address_house_number
	 *
	 * @return Order
	 */
	public function set_address_house_number( $address_house_number ) {
		$this->address_house_number = $address_house_number;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_address_line() {
		return $this->address_line;
	}

	/**
	 * @param string $address_line
	 *
	 * @return Order
	 */
	public function set_address_line( $address_line ) {
		$this->address_line = $address_line;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_address_postal_code() {
		return $this->address_postal_code;
	}

	/**
	 * @param string $address_postal_code
	 *
	 * @return Order
	 */
	public function set_address_postal_code( $address_postal_code ) {
		$this->address_postal_code = $address_postal_code;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_address_city() {
		return $this->address_city;
	}

	/**
	 * @param string $address_city
	 *
	 * @return Order
	 */
	public function set_address_city( $address_city ) {
		$this->address_city = $address_city;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_company_name() {
		return $this->company_name;
	}

	/**
	 * @param string $company_name
	 *
	 * @return Order
	 */
	public function set_company_name( $company_name ) {
		$this->company_name = $company_name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_company_vat() {
		return $this->company_vat;
	}

	/**
	 * @param string $company_vat
	 *
	 * @return Order
	 */
	public function set_company_vat( $company_vat ) {
		$this->company_vat = $company_vat;
		return $this;
	}

	/**
	 * @return string
	 */
	public function get_remarks() {
		return $this->remarks;
	}

	/**
	 * @param string $remarks
	 *
	 * @return Order
	 */
	public function set_remarks( $remarks ) {
		$this->remarks = $remarks;
		return $this;
	}


	public function render_lines_form() {
		ob_start();
		?>
	<div class="form-wrap cm-form-wrap--<?= esc_attr( static::get_type() ) ?>">
		<?= $this->get_property_html( 'order_lines' );?>
	</div>
		<?php
		return ob_get_clean();
	}

	public function render_details_form() {
		ob_start();
?>
	<div class="form-wrap cm-form-wrap--<?= esc_attr( static::get_type() ) ?>">
        <?= $this->get_property_html( 'ribbon_text' ) ?>
		<div class="form-wrap form-wrap--name cm-form-grid">
			<?= $this->get_property_html( 'contact_first_name' ) ?>
			<?= $this->get_property_html( 'contact_last_name' ) ?>
		</div>
		<div class="form-wrap form-wrap--contact cm-form-grid">
			<?= $this->get_property_html( 'contact_email' ) ?>
			<?= $this->get_property_html( 'contact_phone' ) ?>
		</div>
		<div class="form-wrap form-wrap--company cm-form-grid">
			<?= $this->get_property_html( 'company_name' ) ?>
			<?= $this->get_property_html( 'company_vat' ) ?>
		</div>
		<div class="form-wrap form-wrap--address cm-form-grid">
			<div class="cm-form-grid">
				<?= $this->get_property_html( 'address_city' ) ?>
				<?= $this->get_property_html( 'address_postal_code' ) ?>
            </div>
			<div class="cm-form-grid">
			    <?= $this->get_property_html( 'address_line' ) ?>
			    <?= $this->get_property_html( 'address_house_number' ) ?>
            </div>
        </div>
		<?= $this->get_property_html( 'remarks' ) ?>
    </div>
		<?php
		return ob_get_clean();
	}

	public function get_customer_name() {
		return "{$this->get_contact_first_name()} {$this->get_contact_last_name()}";
	}

	public function get_customer() {
		$output = $this->get_customer_name();

		if ( ( $company = $this->get_company_name() ) && ! empty( $company ) ) {
			$output .= " ({$company})";
		}

		return $output;
	}

	public function get_total() {
		$total_amount = array_reduce( $this->get_order_lines(), static function ( $total, Order_Line $order_line ) {
			$total += $order_line->get_total()->get_amount();

			return $total;
		}, 0 );

		return new Price( $total_amount );
	}

	public function get_summary() {
		$list = array_reduce( $this->get_order_lines(), static function ( $list, Order_Line $order_line ) {
			$list .= sprintf(
				'<li>%d &times; <a href="%s">%s</a> = %s',
				$order_line->get_qty(),
				get_edit_post_link( $order_line->get_product_id() ),
				$order_line->get_description(),
				$order_line->get_total()->display( true )
			);

			return $list;
		}, '' );
		if ( ! empty( $list ) ) {
			$list = "<ul>{$list}</ul>";
		}

		return $list;
	}

	protected function editable_fields() {
		if ( ! Order_Type::is_order_editable( $this ) ) {
			return array_diff( parent::editable_fields(), array( 'order_lines' ) );
		}

		return parent::editable_fields();
	}

	protected function validate_model() {
		$errors = [];

		if ( empty( $this->get_order_lines() ) ) {
			$errors[] = __( 'No products selected.', 'cm_translate' );
		} else {
			foreach ( $this->get_order_lines() as $order_line ) {
				if ( empty( $order_line->get_product_id() ) ) {
					$errors[] = __( 'No product selected.', 'cm_translate' );
					continue;
				}

				if ( empty( $order_line->get_qty() ) || ! ctype_digit( $order_line->get_qty() ) || intval( $order_line->get_qty() ) <= 0 ) {
					$errors[] = __( 'Invalid product quantity.', 'cm_translate' );
				}
			}
		}

		/**
		 * @var $field Field
		 * @var $property string
		 */
		foreach ( static::schema() as $property => $field ) {
			$value = $this->get( $property );

			if ( $field->is_required() && empty( $value ) ) {
				$errors[] = sprintf( __( 'Field "%s" is required.', 'cm_translate' ), $property );
				continue;
			}
		}

		if ( ! filter_var( $this->get_contact_email(), FILTER_VALIDATE_EMAIL ) ) {
			$errors[] = __( 'Please enter a valid email.', 'cm_translate' );
		}

		return $errors;
	}
}
