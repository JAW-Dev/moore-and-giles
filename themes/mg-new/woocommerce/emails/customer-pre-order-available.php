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

if ( 'pending' === $order->get_status() && ! WC_Pre_Orders_Manager::is_zero_cost_order( $order ) ) {
	$sub_text = 'Your pre-order is now available, but requires payment.<br/>Please pay for your pre-order now.';
} elseif ( 'failed' === $order->get_status() || 'on-hold' === $order->get_status() ) {
	$sub_text = 'Your pre-order is now available, but automatic payment failed.<br/>Please update your payment information now.';
} else {
	$sub_text = 'Your pre-order is now available.<br/>Your order details are shown below for your reference.';
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
 * Output the Message section
 */
do_action(
	'mg_woo_email_general_text',
	$message
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
