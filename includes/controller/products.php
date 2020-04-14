<?php
// product-related utilities.
use cm\includes\model\Order;
use cm\includes\model\Order_Line;
use cm\includes\model\Product;
use cm\includes\register\Product_Type;

/**
 * Check whether orders are allowed.
 *
 * @param $deceased
 *
 * @return string
 */
function cm_orders_allowed( $deceased ) {
	$allow_orders = (bool) intval( get_post_meta( $deceased->ID, 'flowers', true ) );

	/**
	 * Allow orders being placed for this person.
	 *
	 * @since 1.5.0
	 */
	return apply_filters( 'cm/allow_orders', $allow_orders, $deceased );
}

/**
 * Places an order.
 *
 * @param Order $order
 * @param $deceased
 * @param $order_data
 *
 * @return array
 */
function cm_place_order( Order $order, WP_Post $deceased, $order_data ) {
	$order_data[ Order::prefix_key( 'deceased_id' ) ] = $deceased->ID;
	$order->set_fields_from_input( $order_data );
	$errors = $order->validate();

	if ( count( $errors ) === 0 ) {
		$order->update();
	}

	return $errors;
}

/**
 * Function to display order form.
 *
 * @param $btn_text
 * @param null $deceased
 */
function cm_display_order_form( $btn_text, $deceased = null ) {
	if ( null !== $deceased ) {
		$deceased = get_post();
	}

	$order = Order::create_new();
	$errors = null;

	if($_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['cm_order_nonce'] )) {
	    if(! wp_verify_nonce( $_POST['cm_order_nonce'], 'cm_place_order' )) {
            $errors[] = __('The order form has expired.', 'cm_translate');
        } else {
		    $errors = cm_place_order( $order, $deceased, $_POST );
	    }
    }

	if(is_array($errors) && count($errors) === 0) {
		$order->load_fields();
		?>
        <div class="cm-order-success">
			<?php _e( 'Your order has been successfully placed!', 'cm_translate' ); ?>
        </div>
        <div class="cm-order-summary">
            <h3><?= __( 'Products', 'cm_translate' ) ?></h3>
			<?= $order->get_summary() ?>
            <h3><?= __( 'Total', 'cm_translate' ) ?></h3>
			<?= $order->get_total()->display( true ) ?>
        </div>
		<?php
		return;
	} else {
		?>
        <div class="cm-order-error">
			<?php _e( 'There were some errors with your order, please try again.', 'cm_translate' ); ?>
            <ul class="cm-order-error--list">
				<?php foreach ( $errors as $key => $value ): ?>
                    <li><?= $value ?></li>
				<?php endforeach; ?>
            </ul>
        </div>
		<?php
	}

	if ( isset( $_GET['cm_order_product'] ) ) {
		$product = get_post( $_GET['cm_order_product'] );

		if ( $product instanceof WP_Post ) {
			$order->set_order_lines( array(
				new Order_Line( $product->ID, 1 ),
			) );
		}
	}
	?>
    <form action="" method="post">
		<?= $order->render_lines_form() ?>
		<?= $order->render_details_form() ?>
		<?php wp_nonce_field( 'cm_place_order', 'cm_order_nonce' ); ?>
        <div class="form-wrap form-wrap--submit">
            <input type="submit" name="cm_order_submit" value="<?= esc_attr( $btn_text ) ?>"
                   class="cm-order-form--button"/>
        </div>
    </form>
	<?php
}

function cm_display_products( $title = '', $products_query_arguments = array() ) {
	$products_query_arguments = wp_parse_args(
		$products_query_arguments,
		array(
			'post_type'      => Product_Type::POST_TYPE,
			'posts_per_page' => - 1,
			'orderby'        => 'post_title',
			'order'          => 'asc',
		)
	);

	/**
	 * Allow final filter for product query arguments.
	 *
	 * @since 1.5.0
	 */
	$products_query_arguments = apply_filters( 'cm/products_query_args', $products_query_arguments );
	$products_query           = new WP_Query( $products_query_arguments );

	if ( ! $products_query->have_posts() ) {
		return '';
	}

	ob_start();
	?>
    <div id="cm-products" class="cm-products">
		<?php if ( ! empty( $title ) ): ?>
            <h2 class="cm-products--title"><?= esc_html( $title ); ?></h2>
		<?php endif; ?>
        <div class="cm-products--list">
			<?php while ( $products_query->have_posts() ): $products_query->the_post();
				$product = Product::from_id( get_the_id() ); ?>
                <div class="cm-product-wrapper">
                    <article class="cm-product">
                        <header class="cm-product--header">
							<?php if ( has_post_thumbnail() ): ?>
                                <figure class="cm-product--image-wrapper">
									<?php the_post_thumbnail( 'thumbnail', array(
										'class' => 'cm-product--image',
									) ); ?>
                                </figure>
							<?php endif; ?>
                            <h3 class="cm-product--title"><?php the_title(); ?></h3>
                            <span class="cm-product--price"><?= $product->get_price()->display( true ) ?></span>
                        </header>
                        <main class="cm-product--content">
                            <p class="cm-product-excerpt"><?php the_excerpt(); ?></p>
                        </main>
                        <footer class="cm-product--footer">
                            <a href="?cm_order_product=<?php the_ID(); ?>#cm-order-form" class="cm-product--order-link"
                               data-product="<?php the_ID(); ?>">
								<?php _e( 'Order', 'cm_translate' ); ?>
                            </a>
                        </footer>
                    </article>
                </div>
			<?php endwhile;
			wp_reset_postdata(); ?>
        </div>
    </div>
	<?php
	return ob_get_clean();
}

/*
 * Shortcodes.
 */

/**
 * Display products.
 *
 * @param $atts
 *
 * @return false|string
 */
function cm_products_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'title'    => __( 'Flowers', 'cm_translate' ),
		'deceased' => get_the_ID(),
	), $atts );

	$deceased = get_post( $atts['deceased'] );

	if ( ! $deceased instanceof WP_Post ) {
		// No deceased linked.
		return '';
	}

	if ( ! cm_orders_allowed( $deceased ) ) {
		// Placing orders is not allowed.
		return '';
	}

	/**
	 * Filter to adjust the product query arguments.
	 *
	 * @since 1.5.0
	 */
	$products_query_arguments = apply_filters( 'cm/products_shortcode_query', array(
		'post_type'      => Product_Type::POST_TYPE,
		'posts_per_page' => - 1,
		'orderby'        => 'post_title',
		'order'          => 'asc',
	) );

	return cm_display_products( $atts['title'], $products_query_arguments );
}

add_shortcode( 'cm_products', 'cm_products_shortcode' );

/**
 * Display order form.
 *
 * @param $atts
 *
 * @return false|string
 */
function cm_order_form_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'title'    => __( 'Order', 'cm_translate' ),
		'button'   => __( 'Place Order', 'cm_translate' ),
		'deceased' => get_the_ID(),
	), $atts );

	$deceased = get_post( $atts['deceased'] );

	if ( ! cm_orders_allowed( $deceased ) ) {
		return '';
	}

	ob_start();
	?>
    <div id="cm-order-form" class="cm-order-form">
        <h2 class="cm-order-form--title"><?= $atts['title']; ?></h2>
		<?php cm_display_order_form( $atts['button'], $atts['deceased'] ); ?>
    </div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'cm_order_form', 'cm_order_form_shortcode' );
