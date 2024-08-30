<?php
/**
 * Monthly Products
 *
 * @package    Package_Name
 * @subpackage Package_Name/Subpackage_Name
 * @author     Author_Name
 * @copyright  Copyright (c) Date, Author_Name
 * @license    GPL-2.0
 * @version    1.0.0
 * @since      1.0.0
 */

namespace Objectiv\Site\Utilities;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Monthly Products
 *
 * @author Jason Witt
 * @since  1.0.0
 */
class MonthlyProducts {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Get
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public static function get() {
		global $post;

		$monthly_products = get_field( 'monthly_products', $post->ID );

		// Return empty array if no products are set.
		if ( empty( $monthly_products ) ) {
			return array();
		}

		$return_products = array(
			'current'  => '',
			'previous' => array(),
			'next'     => array(),
		);

		foreach ( $monthly_products as $monthly_product ) {
			$monthly_product_id    = $monthly_product['product']->ID;
			$monthly_product_date  = $monthly_product['date'];
			$monthly_product_image = wp_get_attachment_url( $monthly_product['past_and_up_coming_image'] );

			$product_month     = date( 'm', strtotime( $monthly_product_date ) );
			$is_current_month  = $product_month === date( 'm' );
			$is_previous_month = $product_month < date( 'm' );
			$is_next_month     = $product_month > date( 'm' );

			$product     = wc_get_product( $monthly_product_id );
			$set_product = array(
				'date'        => $monthly_product_date,
				'month'       => date( 'm', strtotime( $monthly_product_date ) ),
				'year'        => date( 'Y', strtotime( $monthly_product_date ) ),
				'product'     => $product,
				'description' => $monthly_product['short_description'],
				'image'       => $monthly_product_image,
			);

			// Set the current month's product.
			if ( $is_current_month ) {
				$return_products['current'] = $set_product;
			}

			// Set the previous menth's products.
			if ( $is_previous_month ) {
				array_push( $return_products['previous'], $set_product );
			}

			// Set the next month's product.
			if ( $is_next_month ) {
				array_push( $return_products['next'], $set_product );
			}
		}

		return $return_products;
	}

	/**
	 * Is Monthly Product Page
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return boolean
	 */
	public static function is_mothly_product_page() {
		$monthly_products = ! empty( self::get() ) ? self::get() : array();

		return ! empty( $monthly_products );
	}

	/**
	 * Get Current Product
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return WC_Product
	 */
	public static function get_current_product() {
		$monthly_products = self::get();
		return $monthly_products['current'];
	}

	/**
	 * Get Next Product
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return WC_Product
	 */
	public static function get_next_products() {
		$monthly_products = self::get();
		return $monthly_products['next'];
	}

	/**
	 * Get Previous Products
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return WC_Product
	 */
	public static function get_previous_products() {
		$monthly_products = self::get();
		return $monthly_products['previous'];
	}

	/**
	 * Get Current Product URL
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public static function get_current_product_url() {
		$monthly_products       = ! empty( self::get() ) ? self::get() : array();
		$current_months_product = ! empty( $monthly_products ) && isset( $monthly_products['current']['product'] ) ? $monthly_products['current']['product'] : '';
		$id                     = ! empty( $current_months_product ) ? $current_months_product->get_id() : '';

		return ! empty( $id ) ? get_the_permalink( $id ) : 0;
	}
}
