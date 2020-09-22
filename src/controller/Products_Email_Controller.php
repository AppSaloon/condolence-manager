<?php namespace appsaloon\cm\controller;

use appsaloon\cm\model\Custom_Post;
use appsaloon\cm\model\Order;
use appsaloon\cm\register\Custom_Post_Type;
use Exception;
use WP_Post;

class Products_Email_Controller {
	/**
	 * @var Templates
	 */
	private $templates;

	public function __construct( Templates $templates ) {
		$this->templates = $templates;
		add_action( 'cm_after_save_order_metadata', array( $this, 'send_email_notification_to_wp_admin' ), 10, 4 );
		add_action( 'cm_after_save_order_metadata', array( $this, 'send_email_notification_to_customer' ), 10, 4 );
	}

	/**
	 * @param int $post_ID
	 * @param string $template
	 * @param string $subject
	 *
	 * @return array
	 * @throws Exception
	 */
	private function get_email_content( int $post_ID, string $template, string $subject ) {
		if ( file_exists( $template ) ) {
			$post_meta          = get_post_meta( $post_ID );
			$condolence_exists  = isset( $post_meta['cm_order_deceased_id'][0] ) && $post_meta['cm_order_deceased_id'][0];
			$order_lines_exists = isset( $post_meta['cm_order_order_lines'][0] ) && $post_meta['cm_order_order_lines'][0];
			if ( $condolence_exists && $order_lines_exists ) {
				$condolence_post_ID         = $post_meta['cm_order_deceased_id'][0];
				$condolence_post            = get_post( $condolence_post_ID );
				$order_lines                = json_decode( $post_meta['cm_order_order_lines'][0] );
				$ribbon_text                = $post_meta['cm_order_ribbon_text'][0];
				$order_contact_name         = $post_meta['cm_order_contact_first_name'][0] . " " . $post_meta['cm_order_contact_last_name'][0];
				$order_address_house_number = $post_meta['cm_order_address_house_number'][0];
				$order_address_line         = $post_meta['cm_order_address_line'][0];
				$order_address_postal_code  = $post_meta['cm_order_address_postal_code'][0];
				$order_address_city         = $post_meta['cm_order_address_city'][0];
				$order_contact_email        = $post_meta['cm_order_contact_email'][0];
				$order_contact_phone        = $post_meta['cm_order_contact_phone'][0];
				$order_company_name         = $post_meta['cm_order_company_name'][0];
				$order_company_vat          = $post_meta['cm_order_company_vat'][0];
				$order_remarks              = $post_meta['cm_order_remarks'][0];
				$order_href                 = htmlspecialchars_decode( get_edit_post_link( $post_ID ) );
				ob_clean();
				ob_start();
				include $template;
				$message = ob_get_clean();

				return array(
					'message' => $message,
					'headers' => array(
						'MIME-Version: 1.0' . "\r\n",
						'Content-Type: text/html; charset=UTF-8',
					),
				);
			}
		}
		throw new Exception( 'Could not get email content' );
	}

	/**
	 * @param Order $order
	 * @param int $post_ID
	 * @param WP_Post $post
	 * @param bool $update
	 */
	public function send_email_notification_to_wp_admin( Order $order, int $post_ID, WP_Post $post, bool $update ) {
		if ( $update == false ) {
			try {
				$template      = $this->templates->cm_get_template_hierarchy( 'new_order_email_to_site_admin' );
				$to            = get_option( 'admin_email' );
				$deceased_id   = $order->get_deceased_id();
				$deceased      = get_post( $deceased_id );
				$subject       = sprintf(
					esc_html__(
						'A new order has been placed for the funeral of %s',
						'cm_translate'
					),
					$deceased->post_title
				);
				$email_content = $this->get_email_content( $post_ID, $template, $subject );
				wp_mail( $to, $subject, $email_content['message'], $email_content['headers'] );
			} catch ( Exception $exception ) {
			}
		}
	}

	/**
	 * @param Order $order
	 * @param int $post_ID
	 * @param WP_Post $post
	 * @param bool $update
	 */
	public function send_email_notification_to_customer( Order $order, int $post_ID, WP_Post $post, bool $update ) {
		$should_send_email_to_customer = get_option( 'cm_option_order_confirmation_email_to_customer', false );
		if ( $update == false && $should_send_email_to_customer ) {
			try {
				$template = $this->templates->cm_get_template_hierarchy( 'new_order_email_to_customer' );
				$to       = $order->get_contact_email();
				$subject  = esc_html__( 'Your order confirmation', 'cm_translate' );
				$order->get_post()->post_title;
				$email_content = $this->get_email_content( $post_ID, $template, $subject );
				wp_mail( $to, $subject, $email_content['message'], $email_content['headers'] );
			} catch ( Exception $exception ) {
			}
		}
	}
}