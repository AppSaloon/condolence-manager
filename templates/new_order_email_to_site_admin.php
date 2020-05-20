<h3><?php echo $subject; ?></h3>
<p>
    <?php echo esc_html__( 'The following products were ordered:', 'cm_translate' ) ?>
</p>
<dl>
    <dt><?php echo esc_html__( 'Description:', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( $order_lines->description ) ?></dd>
    <dt><?php echo esc_html__( 'Quantity:', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( $order_lines->qty ) ?></dd>
    <dt><?php echo esc_html__( 'Price per unit:', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( $order_lines->price->amount ) ?><?php echo esc_html( $order_lines->price->currency ) ?></dd>
    <dt><?php echo esc_html__( 'Total price:', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( (int) $order_lines->price->amount * (int) $order_lines->qty ) ?><?php echo esc_html( $order_lines->price->currency ) ?></dd>
</dl>
<p>
	<?php echo esc_html__( 'This order has been placed by:', 'cm_translate' ) ?>
</p>
<dl>
    <dt><?php echo esc_html__( 'Name', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( $order_contact_name ); ?></dd>
    <dt><?php echo esc_html__( 'Address', 'cm_translate' ); ?></dt>
    <dd>
		<?php echo esc_html( $order_address_line ); ?><br/>
		<?php echo esc_html( $order_address_postal_code ); ?>, <?php echo esc_html( $order_address_city ); ?>
    </dd>
    <dt><?php echo esc_html__( 'email', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( $order_contact_email ); ?></dd>
    <dt><?php echo esc_html__( 'telephone', 'cm_translate' ); ?></dt>
    <dd><?php echo esc_html( $order_contact_phone ); ?></dd>
</dl>
<p>
	<?php echo esc_html__( 'You can view the order here:', 'cm_translate' ); ?><br/>
    <a href="<?php echo esc_attr( $order_href ); ?>"><?php echo esc_attr( $order_href ); ?></a>
</p>
