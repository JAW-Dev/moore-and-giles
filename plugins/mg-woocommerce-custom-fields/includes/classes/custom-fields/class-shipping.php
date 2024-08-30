<?php
/**
 * Processing.
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

/**
 * Shipping
 *
 * @author Clifton Griffin
 * @since  1.0.0
 */
class Shipping {

	/**
	 * Initialize the class
	 *
	 * @author Clifton Griffin
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		$this->add_options();
	}


	/**
	 * Add shipping custom fields
	 *
	 * @author Clifton Griffin
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function add_options() {
		$tooltip_args = array(
			'hook'       => 'woocommerce_product_options_shipping',
			'field_type' => 'text',
			'arguments'  => array(
				'label'         => 'Shipping Tooltip',
				'id'            => 'woo_help_shipping_tooltip',
				'wrapper_class' => 'woocommerce-product-options-shipping-tooltip',
				'class'         => 'short processing-time__prefix',
				'placeholder'   => __( '' ),
				'description'   => __( 'Override the shipping tooltip. Leave blank for default.', 'moore-and-giles' ),
			),
		);

		mg_wcf_create_field( $tooltip_args );
	}
}