<?php
/**
 * Code.
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Inlcudes/Classes/Admin/Fields/Gift_Wrapping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Gift_Wrapping;

use MGSpecialtyCoupons\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Code' ) ) {

	/**
	 * Code
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Code {

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
			global $post;
			$coupon = new \WC_Coupon( $post->ID );
			$code   = $coupon->get_code();

			return woocommerce_wp_hidden_input(
				array(
					'id'    => 'gift_wrapping_coupons_code',
					'value' => $code,
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

			$field = 'gift_wrapping_coupons_code';

			if ( isset( $post[ $field ] ) ) {
				update_post_meta( $post_id, $field, $post[ $field ] );
			}
		}
	}
}
