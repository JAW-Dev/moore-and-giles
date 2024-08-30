<?php
/**
 * Customer invoice email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
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

$_product            = wc_get_product( $product_id );
$product_url         = get_the_permalink( $product_id );
$product_image       = get_the_post_thumbnail_url( $product_id, 'medium' ) ? get_the_post_thumbnail_url( $product_id, 'medium' ) : '';
$product_description = $_product->get_description() ? $_product->get_description() : '';

/**
 * Output the "Your wait is Over!" section
 */
do_action(
	'mg_woo_email_top_hero_image',
	'Your wait is over!',
	'Our ' . $product_title . ' is back in stock!',
	$product_image,
	array(
		'title' => 'Buy Now',
		'url'   => $product_link,
	)
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
