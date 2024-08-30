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

/**
 * Output the "Thank You For Your Order!" section
 */
do_action(
	'mg_woo_email_top_hero',
	'Your Pre-Order Has Been Cancelled!',
	'Your order details are shown below for your reference.',
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
