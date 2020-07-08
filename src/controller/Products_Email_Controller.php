<?php namespace appsaloon\cm\controller;

use appsaloon\cm\model\Order;
use WP_Post;

class Products_Email_Controller {
	public function __construct() {
		add_action( 'after_save_order_metadata', array( $this, 'send_email_notification_to_wp_admin' ), 10, 4 );
	}


	public function send_email_notification_to_wp_admin( Order $order, int $post_ID, WP_Post $post, bool $update ) {
		if ( $update == false ) { // only send email for new orders
			$post_meta = get_post_meta( $post_ID );
			error_log( var_export( [ "post_meta" => $post_meta ], 1 ) );
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
				$to                         = get_option( 'admin_email' );
				$subject                    = sprintf( esc_html__( 'A new order has been placed for the funeral of %s', 'cm_translate' ), $condolence_post->post_title );
				ob_start();
				include CM_BASE_DIR . "/templates/new_order_email_to_site_admin.php";
				$message = ob_get_clean();
				$headers = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
				wp_mail( $to, $subject, $message, $headers );
			}
		}
	}
}