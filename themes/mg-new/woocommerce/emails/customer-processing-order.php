<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.7.4
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
 * Output the "Thank You For Your Order" section
 */
do_action(
	'mg_woo_email_top_hero',
	'Thank You For Your Order',
	'We Know You\'re Going to Love it!',
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
 * Output the separator line
 */
do_action( 'mg_woo_email_separator_line' );

/**
 * Output the simple cta section
 * "Questions about shipping?"
 */
do_action(
	'mg_woo_email_simple_cta',
	'Question About Shipping?',
	'We will send you a confirmation email as soon as your order ships, but if you have questions about shipping methods or processing times read our FAQ’s.',
	array(
		'title' => 'Learn More',
		'url'   => get_home_url() . '/bag-frequently-asked-questions/',
	)
);

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
