<?php
/**
 * Personalization Tooltip.
 *
 * @package    MG_WooCommerce_Custom_Fields
 * @subpackage MG_WooCommerce_Custom_Fields/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_WooCommerce_Custom_Fields\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Personalization_Tooltip' ) ) {

	/**
	 * Personalization Tooltip
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Personalization_Tooltip {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->fields();
			$this->variant_fields();
		}

		/**
		 * Fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function fields() {
			$field_args = array(
				'label'         => 'Personalization Tooltip',
				'id'            => 'personalization_tooltip',
				'wrapper_class' => 'woocommerce_product_options_advanced__personalization_tooltip',
				'class'         => 'short personalization_tooltip',
				'desc_tip'      => true,
				'description'   => __( 'The text used for the personalization tooltip.', 'moore-and-giles' ),
			);

			$args = array(
				'hook'       => 'woocommerce_product_options_advanced',
				'field_type' => 'textarea',
				'arguments'  => $field_args,
			);

			mg_wcf_create_field( $args );
		}

		/**
		 * Variant Fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function variant_fields() {
			$field_args = array(
				'label'         => 'Personalization Tooltip',
				'id'            => 'variant_personalization_tooltip',
				'wrapper_class' => 'form-field form-row form-row-full woocommerce_variation_options_pricing__personalization_tooltip',
				'class'         => 'short personalization_tooltip',
				'desc_tip'      => true,
				'description'   => __( 'The text used for the personalization tooltip.', 'moore-and-giles' ),
			);

			$args = array(
				'hook'       => 'woocommerce_variation_options_pricing',
				'field_type' => 'textarea',
				'arguments'  => $field_args,
			);

			mg_wcf_create_field( $args );
		}
	}
}
