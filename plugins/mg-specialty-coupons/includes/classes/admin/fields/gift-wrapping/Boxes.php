<?php
/**
 * Boxes.
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

if ( ! class_exists( __NAMESPACE__ . '\\Boxes' ) ) {

	/**
	 * Boxes
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Boxes {

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
			$boxes     = function_exists( 'get_field' ) ? get_field( 'mg_box_sizes', 'option' ) : array();
			$box_sizes = array( '' => ' ' );

			// Bail if boxes is empty.
			if ( empty( $boxes ) ) {
				return;
			}

			foreach ( $boxes as $box ) {
				$size = ucfirst( $box['size'] );
				$sku  = $box['sku'];

				// Continue if no size or sku.
				if ( ! $size || ! $sku ) {
					continue;
				}

				$box_sizes[ $sku ] = $size;
			}

			$box_sizes['all'] = 'All Boxes';

			return woocommerce_wp_select(
				array(
					'id'          => 'gift_wrapping_coupons_box',
					'desc_tip'    => true,
					'description' => __( 'Select the gift box size to apply the coupon to', 'moore-and-giles' ),
					'label'       => __( 'Box Sizes', 'moore-and-giles' ),
					'options'     => $box_sizes,
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

			$field = 'gift_wrapping_coupons_box';

			if ( isset( $post[ $field ] ) ) {
				update_post_meta( $post_id, $field, $post[ $field ] );
			}
		}
	}
}
