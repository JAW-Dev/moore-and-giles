<?php
/**
 * Services.
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

if ( ! class_exists( __NAMESPACE__ . '\\Services' ) ) {

	/**
	 * Services
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Services {

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
			$all_services = new Shipping\Methods();
			$get_services = $all_services->get_services();

			$services = array( '' => ' ' );
			$fields   = array();
			$instance = '';

			// Bail if empty.
			if ( empty( $get_services ) ) {
				return;
			}

			foreach ( $get_services as $service_key => $service_value ) {
				$services_count = count( $service_value );

				foreach ( $service_value as $key => $value ) {

					$value_name   = isset( $value['name'] ) ? $value['name'] : '';
					$value_title  = isset( $value['title'] ) ? $value['title'] : '';
					$service_name = $value_name ? $value_name : $value_title;

					if ( array_key_exists( 'instance', $value ) ) {
						$instance = isset( $value['instance'] ) ? $value['instance'] : '';
					} else {
						$name          = str_replace( array( '_', '-' ), ' ', $service_name );
						$formated_name = ucwords( $name );

						// Continue if not set.
						if ( ! $formated_name ) {
							continue;
						}

						if ( $services_count >= 2 ) {

							$services[ $services_count . ':' . $key ] = $formated_name;
						} else {
							$services[ $key ] = $formated_name;
						}
					}
				}


				if ( $services_count >= 2 ) {
					$fields[] = woocommerce_wp_select(
						array(
							'id'            => 'objectiv_shipping_coupons_service_' . $service_key,
							'class'         => 'shipping-service-select select short',
							'wrapper_class' => 'objectiv_shipping_coupons_service objectiv_shipping_coupons_service_' . $service_key,
							'desc_tip'      => true,
							'description'   => __( 'Select the shippping method to apply the coupon to', 'moore-and-giles' ),
							'label'         => __( 'Shipping Service', 'moore-and-giles' ),
							'options'       => $services,
						)
					);
				} else {
					$fields[] = woocommerce_wp_hidden_input(
						array(
							'id'    => 'objectiv_shipping_coupons_service_' . $service_key,
							'class' => 'no-shipping-services',
							'value' => $instance,
						)
					);
				}
			}

			return $fields;
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
			$post         = Classes\Post::request();
			$all_services = new Shipping\Methods();
			$get_services = $all_services->get_services();

			// Bail if post is empty.
			if ( empty( $post ) ) {
				return;
			}

			// Bail if empty.
			if ( empty( $get_services ) ) {
				return;
			}

			foreach ( $get_services as $service_key => $service_value ) {
				$field = 'objectiv_shipping_coupons_service_' . $service_key;

				if ( isset( $post[ $field ] ) ) {
					update_post_meta( $post_id, $field, $post[ $field ] );
				}
			}
		}
	}
}
