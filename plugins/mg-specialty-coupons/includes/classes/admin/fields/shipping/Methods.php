<?php
/**
 * Methods.
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Inlcudes/Classes/Admin/Fields/Shipping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping;

use MGSpecialtyCoupons\Includes\Classes as Classes;
use MGSpecialtyCoupons\Includes\Classes\Shipping as Shipping;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Methods' ) ) {

	/**
	 * Methods
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Methods {

		/**
		 * Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $args;


		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
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
			add_action( 'woocommerce_coupon_options_save', array( $this, 'save' ) );
		}

		/**
		 * Render Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return string
		 */
		public function render() {
			$all_methods = new Shipping\Methods();
			$get_methods = $all_methods->get_settings();

			foreach ( $get_methods as $key => $value ) {
				$title = isset( $value['title'] ) ? $value['title'] : '';

				// Continue if not set.
				if ( ! $title ) {
					continue;
				}

				if ( 'flat_rate' === $key ) {
					$title = __( 'Flat rate', 'mg-specialty-coupons' );
				}

				$methods[ $key ] = $title;
			}

			return woocommerce_wp_select(
				array(
					'id'          => 'objectiv_shipping_coupons_method',
					'desc_tip'    => true,
					'description' => __( 'Select the shippping method to apply the coupon to', 'moore-and-giles' ),
					'label'       => __( 'Shipping Method', 'moore-and-giles' ),
					'options'     => $methods,
				)
			);
		}

		/**
		 * Save.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $post_id the post ID.
		 *
		 * @return void
		 */
		public function save( $post_id ) {
			$post = Classes\Post::request();

			// Bail if post is empty.
			if ( empty( $post ) ) {
				return;
			}

			$field = 'objectiv_shipping_coupons_method';

			if ( isset( $post[ $field ] ) ) {
				update_post_meta( $post_id, $field, $post[ $field ] );
			}
		}
	}
}
