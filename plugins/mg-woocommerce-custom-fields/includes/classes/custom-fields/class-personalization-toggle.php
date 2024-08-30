<?php
/**
 * Personalization Toggle.
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

if ( ! class_exists( __NAMESPACE__ . '\\Personalization_Toggle' ) ) {

	/**
	 * Personalization Toggle
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Personalization_Toggle {

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
				'label'         => 'Disable Personalization',
				'id'            => 'disable_personalization',
				'wrapper_class' => 'woocommerce_product_options_advanced__personalization',
				'class'         => 'disable_personalization',
			);

			$args = array(
				'hook'       => 'woocommerce_product_options_advanced',
				'field_type' => 'checkbox',
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
				'label'         => 'Disable Personalization',
				'id'            => 'varaint_disable_personalization',
				'wrapper_class' => 'woocommerce_variation_options_pricing__personalization',
				'class'         => 'checkbox disable_personalization',
			);

			$args = array(
				'hook'       => 'woocommerce_variation_options_pricing',
				'field_type' => 'checkbox',
				'arguments'  => $field_args,
			);

			mg_wcf_create_field( $args );
		}
	}
}
