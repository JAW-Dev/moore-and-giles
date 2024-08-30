<?php
/**
 * Create Field.
 *
 * Load: true
 *
 * @package    MG_WooCommerce_Custom_Fields
 * @subpackage MG_WooCommerce_Custom_Fields/Inlcudes/Functions/Example
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'mg_wcf_create_field' ) ) {
	/**
	 * Create Field.
	 *
	 * Create a new custom WooCommerce field.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $args {
	 *     Setup the custom field.
	 *
	 *     @type string  $hook       The WooCommerce field action hook.
	 *     @type string  $field_type The type of field to create.
	 *     @type array   $arguments  The arguments for the field.
	 * }
	 *
	 * @return array
	 */
	function mg_wcf_create_field( $args = array() ) {
		return new MG_WooCommerce_Custom_Fields\Includes\Classes\Field( $args );
	}
}
