<?php
/**
 * Source Codes.
 *
 * @package    MG_Source_Codes
 * @subpackage MG_Source_Codes/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Source_Codes\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Source_Codes' ) ) {

	/**
	 * Source Codes
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Source_Codes {

		/**
		 * Options.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $options;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version         The plugin version.
		 *     @type string $plugin_dir_url  The plugin directory URL.
		 *     @type string $plugin_dir_path The plugin Directory Path.
		 *     @type array  $options         The plugin options.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->options = $args['options'];
			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'add_order_field' ) );
		}

		/**
		 * Add order field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $order_id The oder ID.
		 *
		 * @return void
		 */
		public function add_order_field( $order_id ) {
			$order       = wc_get_order( $order_id );
			$coupons     = $this->get_coupons();
			$used_coupon = $this->get_used_coupon( $order );

			// Bail if there is no coupon.
			if ( ! $used_coupon ) {
				return;
			}

			if ( $this->coupon_is_source_code( $coupons, $used_coupon ) && ! empty( $used_coupon ) ) {
				$order->update_meta_data( 'catalog_source_code', $used_coupon );
				$order->save();
			}
		}

		/**
		 * Coupon is source code.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array  $coupons The array of coupons.
		 * @param string $used    The used coupon in the order.
		 *
		 * @return boolean
		 */
		public function coupon_is_source_code( $coupons, $used ) {

			// Bail if used coupons isn't a source code.
			if ( ! $this->is_source_code( $used ) ) {
				return false;
			}

			foreach ( $coupons as $coupon ) {
				$bulk_codes = $coupon['bulk_codes'];

				if ( in_array( strtolower( $used ), $bulk_codes, true ) ) {
					return true;
				}

				if ( strtolower( $used ) === $coupon['post_title'] ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Is Source Code.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $used The used coupons in the order.
		 *
		 * @return boolean
		 */
		public function is_source_code( $used ) {

			$codes = $this->get_source_codes();

			if ( in_array( strtolower( $used ), $codes, true ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Get Source Codes.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_source_codes() {
			$codes = isset( $this->options['source_codes_list'] ) ? $this->options['source_codes_list'] : '';
			$codes = explode( PHP_EOL, $codes );

			$codes_array = array();
			foreach ( $codes as $code ) {
				$format_code = explode( ':', $code );

				$codes_array[] = trim( strtolower( $format_code[0] ) );
			}

			return $codes_array;
		}

		/**
		 * Get used Coupons.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param Object $order The oder object.
		 *
		 * @return array
		 */
		public function get_used_coupon( $order ) {
			$coupon = $order->get_used_coupons();

			if ( $coupon && ! empty( $coupon ) ) {
				return $coupon[0];
			}

			return '';
		}

		/**
		 * Get Coupons.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_coupons() {
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
			);

			$coupons     = get_posts( $args );
			$coupon_list = array();

			foreach ( $coupons as $coupon ) {
				$bulk_codes = get_post_meta( $coupon->ID, 'bulk_codes', true );
				$bulk_array = array();

				if ( ! $bulk_codes ) {
					return array();
				}

				foreach ( $bulk_codes as $bulk_code ) {
					$bulk_array[] = strtolower( $bulk_code );
				}

				$coupon_list[] = array(
					'post_title' => strtolower( $coupon->post_title ),
					'bulk_codes' => $bulk_array ? $bulk_array : array(),
				);
			}

			if ( ! empty( $coupon_list ) ) {
				return $coupon_list;
			}

			return array();
		}
	}
}
