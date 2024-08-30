<?php
/**
 * Customer Preorder email
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Executes the e-mail header.
 *
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

$availability_date = '';
if ( class_exists( 'WC_Pre_Orders_Product' ) ) {
	$availability_date = WC_Pre_Orders_Product::get_localized_availability_date( WC_Pre_Orders_Order::get_pre_order_product( $order ) );
}
$availability_date_text = ( ! empty( $availability_date ) ) ? sprintf( __( ' on %s.', 'wc-pre-orders' ), $availability_date ) : '.';
$sub_text               = '';

if ( WC_Pre_Orders_Order::order_will_be_charged_upon_release( $order ) ) {
	if ( WC_Pre_Orders_Order::order_has_payment_token( $order ) ) {
		$sub_text = 'You will be automatically charged for your order via your selected payment method when your pre-order is released ' . $availability_date_text . '<br/>Your order details are shown below for your reference.';
	} else {
		$sub_text = 'You will be prompted for payment for your order when your pre-order is released ' . $availability_date_text . '<br/>Your order details are shown below for your reference.';
	}
} else {
	$sub_text = 'You will be notified when your pre-order is released ' . $availability_date_text . '<br/>Your order details are shown below for your reference.';
}

/**
 * Output the "Thank You For Your Order!" section
 */
do_action(
	'mg_woo_email_top_hero',
	'Thank You For Your Order!',
	$sub_text,
	array(
		'title' => 'View Order',
		'url'   => $order->get_view_order_url(),
	)
);

/**
 * Output the order details section
 */
do_action( 'mg_woo_email_order_details', $order );

/**
 * Output the simple cta section
 * "Still have Questions?"
 */
do_action(
	'mg_woo_email_simple_cta',
	'Still Have Questions?',
	'Our Customer Care Team is Here to Help',
	array(
		'title' => 'Contact',
		'url'   => get_home_url() . '/contact/',
	),
	true
);

/**
 * Outputs the IG Adventure section
 */
do_action( 'mg_woo_email_ig_adventure' );

/**
 * Executes the email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
