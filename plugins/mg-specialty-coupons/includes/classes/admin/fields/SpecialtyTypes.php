<?php
/**
 * Specialty Types.
 *
 * @package    MG_Specialty_Coupons
 * @subpackage MG_Specialty_Coupons/Inlcudes/Classes/Admin/Fields
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Admin\Fields;

use MGSpecialtyCoupons\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\SpecialtyTypes' ) ) {

	/**
	 * Specialty Types.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class SpecialtyTypes {

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
		 * Hooks
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'woocommerce_coupon_options', array( $this, 'coupon_type_field' ), 10, 2 );
			add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'save' ) );
		}

		/**
		 * Type Field
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int    $coupon_id The ID of the coupon.
		 * @parma object $coupon    The coupon object.
		 *
		 * @return string
		 */
		public function coupon_type_field( $coupon_id, $coupon ) {
			$options = array(
				'general'         => __( 'General', 'mg-specialty-coupons' ),
				'black-friday'    => __( 'Black Friday', 'mg-specialty-coupons' ),
				'personalization' => __( 'Personalization', 'mg-specialty-coupons' ),
				'gift-wrapping'   => __( 'Gift Wrapping', 'mg-specialty-coupons' ),
				'shipping'        => __( 'Shipping', 'mg-specialty-coupons' ),
			);

			return woocommerce_wp_select(
				array(
					'id'          => 'coupon_type',
					'desc_tip'    => true,
					'description' => __( 'Select type of coupon', 'moore-and-giles' ),
					'label'       => __( 'Coupon Type', 'moore-and-giles' ),
					'options'     => $options,
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

			$field = 'coupon_type';

			if ( isset( $post[ $field ] ) ) {
				update_post_meta( $post_id, $field, $post[ $field ] );
			}
		}
	}
}
