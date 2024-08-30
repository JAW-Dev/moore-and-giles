<?php
/**
 * Admin failed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-failed-order.php
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

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

/**
 * Output the "Payment Failed" section
 */
do_action(
	'mg_woo_email_top_hero',
	'Payment Failed',
	'Payment for the following order has failed.',
	array(
		'title' => 'View Order',
		'url'   => $order->get_edit_order_url(),
	)
);

/**
 * Output the order details section
 */
do_action( 'mg_woo_email_order_details', $order );

/**
 * Output the
 */

do_action( 'mg_woo_admin_order_details', $order );

/**
 * Executes the email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
