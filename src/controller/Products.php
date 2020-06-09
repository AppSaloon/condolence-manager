<?php
// product-related utilities.
use appsaloon\cm\model\Order;
use appsaloon\cm\model\Order_Line;
use appsaloon\cm\model\Product;
use appsaloon\cm\register\Order_Type;
use appsaloon\cm\register\Product_Type;
use appsaloon\cm\settings\Admin_Options_Page;

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
		$order_id = $order->update();
		header("Location:./?cm-order-form&cm-products&order_id=$order_id#cm-order-form");
		die();
	}

	return $errors;
}

function cm_submit_order_form(Order $order, WP_Post $deceased) {
    $errors = null;

    if (!wp_verify_nonce($_POST['cm_order_nonce'], 'cm_place_order')) {
        $errors[] = __('The order form has expired.', 'cm_translate');
    } else {
        $errors = cm_place_order($order, $deceased, $_POST);
    }

    if (is_array($errors) && count($errors) > 0) {
        ?>
        <div class="cm-order-error">
            <?php _e('There were some errors with your order, please try again.', 'cm_translate'); ?>
            <ul class="cm-order-error--list">
                <?php foreach ($errors as $key => $value): ?>
                    <li><?= $value ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

/**
 * @param string $order_id
 */
function cm_display_order_form_submitted (string $order_id) {
    $order = new Order($order_id);
    $order->load_fields();
    ?>
    <div class="cm-order-success" style="white-space: pre;"><?php
        echo Admin_Options_Page::get_current_or_default_option('cm_option_confirmation_order_text');
    ?></div>
    <div class="cm-order-summary">
        <h3><?= __( 'Products', 'cm_translate' ) ?></h3>
        <?= strip_tags( $order->get_summary(), 'li ul' ) ?>
        <h3><?= __( 'Total', 'cm_translate' ) ?></h3>
        <?= $order->get_total()->display( true ) ?>
    </div>
    <?php
    return;
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

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        cm_submit_order_form($order, $deceased);
    }

	if ( isset( $_GET['order_id'] ) ) {
	    cm_display_order_form_submitted($_GET['order_id']);
	    return;
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
    <form action="?cm-order-form&cm-products#cm-order-form" method="post">
        <input type="hidden" name="cm_order_deceased_id" value="<?php echo get_the_ID(); ?>">
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

function cm_get_display_value($id) {
    $query = array();
    parse_str($_SERVER['QUERY_STRING'], $query);

    if (!is_single() || isset($query[$id])) {
        return 'block';
    }

    return 'none';
}

function cm_display_products( $title = '', $products_query_arguments = array(), $hide_order_buttons = false ) {
    $current_page_number = (int) (isset($_GET['cm-products-page']) ? $_GET['cm-products-page'] : 1);

    $products_query_arguments = wp_parse_args(
		$products_query_arguments,
		array(
			'post_type'      => Product_Type::POST_TYPE,
			'orderby'        => 'post_title',
			'order'          => 'asc',
            'posts_per_page' => 15,
            'offset'         => ($current_page_number - 1) * 15
		)
	);

	/**
	 * Allow final filter for product query arguments.
	 *
	 * @since 1.5.0
	 */
	$products_query_arguments = apply_filters( 'cm/products_query_args', $products_query_arguments );
	$products_query           = new WP_Query( $products_query_arguments );
    $max_num_pages = (int) $products_query->max_num_pages;
    if($current_page_number < 1 || $current_page_number > $max_num_pages) {
        $products_query_arguments['offset'] = 0;
        $products_query = new WP_Query( $products_query_arguments );
    }

	if ( ! $products_query->have_posts() ) {
		return '';
	}

	// Enqueue stylesheet
	wp_enqueue_style( 'cm/products' );

	ob_start();
	?>
    <div id="cm-products" class="cm-products rouw" style="display: <?php echo cm_get_display_value('cm-products'); ?>;">
		<?php if ( ! empty( $title ) ): ?>
            <h2 class="cm-products--title"><?= esc_html( $title ); ?></h2>
		<?php endif; ?>
        <div class="cm-products--list">
			<?php while ( $products_query->have_posts() ): $products_query->the_post();
				$product = Product::from_id( get_the_id() ); ?>
                <div class="cm-product--wrapper">
                    <div class="cm-product">
	                    <?php if ( has_post_thumbnail() ): ?>
	                    <figure class="cm-product--image-wrapper">
		                    <?php the_post_thumbnail( 'medium', array(
			                    'class' => 'cm-product--image',
		                    ) ); ?>
	                    </figure>
	                    <?php endif; ?>
                        <header class="cm-product--header">
                            <h3 class="cm-product--title"><?php the_title(); ?></h3>
                            <span class="cm-product--price"><?= $product->get_price()->display( true ) ?></span>
                        </header>
                        <main class="cm-product--content">
                            <p class="cm-product-excerpt"><?php the_excerpt(); ?></p>
                        </main>
                        <?php
                        if (! $hide_order_buttons) {
                           ?>
                            <footer class="cm-product--footer">
                                <a href="?cm-products&cm-order-form&cm_order_product=<?php the_ID(); ?>#cm-order-form" class="cm-product--order-link"
                                   data-product="<?php the_ID(); ?>">
                                    <?php _e( 'Order', 'cm_translate' ); ?>
                                </a>
                            </footer>
                            <?php
                        }
                        ?>
                    </div>
                </div>
			<?php endwhile;
			wp_reset_postdata(); ?>
        </div>
        <?php
        if($max_num_pages > 1) { ?>
            <form action="./" method="get" class="cm-products-pagination">
                <?php
                if($hide_order_buttons === false) { ?>
                    <input type="hidden" name="cm-products"/>
                    <input type="hidden" name="cm-order-form"/>
                    <?php
                }
                ?>
                <?php
                for ($page_number = 1; $page_number <= $max_num_pages; $page_number++) {
                    $style = ($current_page_number === $page_number) ? "background: #f5f5f5; border-color: #bbb #bbb #aaa #" : "";
                    ?>
                    <button name="cm-products-page" value="<?=$page_number?>" style="<?=$style?>"><?= $page_number; ?></button>
                    <?php
                }
                ?>
            </form>
            <?php
        }
        ?>
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
        'hide_order_buttons' => false
	), $atts );

	$deceased = get_post( $atts['deceased'] );
    $hide_order_buttons = $atts['hide_order_buttons'] === 'true';

    //ignore deceased and orders if hide_order_buttons is true
    if ( ! $hide_order_buttons) {
        if ( ! $deceased instanceof WP_Post ) {
            // No deceased linked.
            return '';
        }

        if ( ! cm_orders_allowed( $deceased ) ) {
            // Placing orders is not allowed.
            return '';
        }
    }

	/**
	 * Filter to adjust the product query arguments.
	 *
	 * @since 1.5.0
	 */
	$products_query_arguments = apply_filters( 'cm/products_shortcode_query', array(
		'post_type'      => Product_Type::POST_TYPE,
		'orderby'        => 'post_title',
		'order'          => 'asc',
	) );

	return cm_display_products( $atts['title'], $products_query_arguments, $hide_order_buttons );
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
    <div id="cm-order-form" class="cm-order-form rouw" style="display: <?php echo cm_get_display_value('cm-order-form'); ?>;">
        <h2 class="cm-order-form--title"><?= $atts['title']; ?></h2>
        <p>
		<?php
		$dummy_date = ( new DateTime() )->setTime( 0, 0, 0 );
		$order_time = $dummy_date->modify( Order_Type::get_close_offset() )->format( 'H:i' );
		printf(
			__( 'Ordering is possible up until the day before the funeral at %s.', 'cm_translate' ),
			$order_time
		);
		?>
        </p>
        <?php cm_display_order_form( $atts['button'], $atts['deceased'] );?>
    </div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'cm_order_form', 'cm_order_form_shortcode' );
