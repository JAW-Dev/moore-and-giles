<?php
/**
* Plugin Name: MG Zonos Checkout
* Plugin URI:
* Description: This plugin integrates woocomerce with Zonos
* Version: 1.3.0
* Author: Zonos
* Author URI: http://www.zonos.com/
* License: GPL2
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Iglobal_Plugin to prevent the need to use globals.
 *
 * @since  1.2.4
 * @return object Iglobal_Plugin
 */
function Iglobal_Plugin() {
	$instance = Iglobal_Plugin::instance( __FILE__, '1.1.7' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Iglobal_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

add_action( 'plugins_loaded', 'mg_zonos_init' );

function mg_zonos_init() {
	// Bail if WooCommerce is not active
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	// Load plugin class files
	if ( ! class_exists( 'Iglobal_Plugin' ) ) {
		require_once( 'includes/class-iglobal.php' );}
	if ( ! class_exists( 'Iglobal_Plugin_Settings' ) ) {
		require_once( 'includes/class-iglobal-settings.php' );}

	// Load plugin libraries
	if ( ! class_exists( 'Iglobal_Plugin_Admin_API' ) ) {
		require_once( 'includes/lib/class-iglobal-admin-api.php' );}
	if ( ! class_exists( 'IgWC' ) ) {
		require_once( 'includes/lib/igWC.php' );}

	add_action( 'wp_head', 'add_zonos_hello' );
	Iglobal_Plugin();
}

function add_zonos_hello() {
	if ( get_option( 'iglobal_zonos_is_active' ) == 'enabled' && get_option( 'iglobal_site_key' ) !== '' ) {
		echo '<script src="https://hello.zonos.com/hello.js?siteKey=' . get_option( 'iglobal_site_key' ) . '"></script>';
		echo '<script>setTimeout(function(){ zonos.config({currencySelectors: "' . get_option( 'iglobal_zonos_price_selectors' ) . '"}); },200);</script>';
		//look for product page and add zonos.config here if found?

		$protocol            = is_ssl() ? 'https://' : 'http://';
		$url                 = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$productSubDirectory = get_option( 'iglobal_product_sub_directory' );
		if ( $productSubDirectory === '' ) {
			$productSubDirectory = '/product/';
		}

		if ( strpos( $url, $productSubDirectory ) !== false ) {//add a selector for product subdirectory? bb uses shop instead of product
			try {
				$title = get_string_between( $url, $productSubDirectory, '/' );
				if ( ! $title ) {
					$title = end( explode( $productSubDirectory, $url ) );
				}

				// Using our custom function to get an instance of the WC_Product object
				$product_obj = get_wc_product_by_title( $title );
				if ( $product_obj !== false ) {
					$imageUrl = get_the_post_thumbnail_url( $product_obj->get_id() );
					echo '<script>';
					echo ' zonos.config({';
					echo '    items: [{';
					echo '        price: ' . $product_obj->get_price() . ',';
					echo "        name: '" . $product_obj->get_name() . "',";
					echo "        url: '" . $url . "',";
					echo "        image: '" . $imageUrl . "',";
					echo '    }]';
					echo '  });';
					echo '</script>';
				}
			} catch ( Exception $e ) {
			}
		}
	}
}

function get_wc_product_by_title( $title ) {
	global $wpdb;
	$post_title = strval( $title );

	$post_table = $wpdb->prefix . 'posts';
	$result     = $wpdb->get_col( "SELECT ID FROM $post_table WHERE post_name LIKE '$post_title' AND post_type LIKE 'product';" );

	// We exit if title doesn't match
	if ( empty( $result[0] ) ) {
		  return false;
	} else {
		return wc_get_product( intval( $result[0] ) );
	}
}

function get_string_between( $string, $start, $end ) {
	$string = ' ' . $string;
	$ini    = strpos( $string, $start );
	if ( $ini == 0 ) {
		return '';
	}
	$ini += strlen( $start );
	$len  = strpos( $string, $end, $ini ) - $ini;
	return substr( $string, $ini, $len );
}
