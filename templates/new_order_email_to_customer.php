<?php
/* @var $subject string */
/* @var $order_lines object */
/* @var $ribbon_text string */
/* @var $order_contact_name string */
/* @var $order_address_line string */
/* @var $order_address_house_number string */
/* @var $order_address_postal_code string */
/* @var $order_address_city string */
/* @var $order_contact_email string */
/* @var $order_contact_phone string */
/* @var $order_company_name string */
/* @var $order_company_vat string */
/* @var $order_remarks string */

?>
<h3><?php echo $subject; ?></h3>
<p><?php echo esc_html__('Dear sir/madam', 'cm_translate'); ?></p>
<p><?php echo esc_html__('Thank you for your order.', 'cm_translate'); ?></p>
<p><?php echo esc_html__('You can find the details of your order below:', 'cm_translate'); ?></p>
<hr>
<p>
    <strong><?php echo esc_html__( 'The following products were ordered', 'cm_translate' ) ?>:</strong>
</p>
<style>
    #cm_order_overview tr td {
        padding: 6px;
    }
</style>
<table id="cm_order_overview">
    <tr>
        <td><?php echo esc_html__( 'Description', 'cm_translate' ); ?>:</td>
        <td><?php echo esc_html( $order_lines->description ) ?></td>
    </tr>
    <tr>
        <td><?php echo esc_html__( 'Quantity', 'cm_translate' ); ?>:</td>
        <td><?php echo esc_html( $order_lines->qty ) ?></td>
    </tr>
    <tr>
        <td><?php echo esc_html__( 'Price per unit', 'cm_translate' ); ?>:</td>
        <td><?php echo esc_html( $order_lines->price->amount ) ?><?php echo esc_html( $order_lines->price->currency ) ?></td>
    </tr>
    <tr>
        <td><?php echo esc_html__( 'Total price', 'cm_translate' ); ?>:</td>
        <td><?php echo esc_html( (int) $order_lines->price->amount * (int) $order_lines->qty ) ?><?php echo esc_html( $order_lines->price->currency ) ?></td>
    </tr>
    <tr>
        <td><?php echo esc_html__( 'Ribbon text', 'cm_translate' ); ?>:</td>
        <td><pre><?php echo esc_html( $ribbon_text ); ?></pre></td>
    </tr>
</table>
<hr>
<p><?php echo esc_html__('Thank you.', 'cm_translate'); ?></p>
<p><?php echo esc_html__('Kind regards', 'cm_translate'); ?></p>