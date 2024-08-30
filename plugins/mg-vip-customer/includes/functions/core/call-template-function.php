<?php
/**
 * Call Template Function.
 *
 * Load: true
 *
 * @package    MG_VIP_Customer
 * @subpackage MG_VIP_Customer/Inlcudes/Functions/Core
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'mg_call_template_function' ) ) {
	/**
	 * Example Function.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $callback The function to callback.
	 * @param mixed  ...$args  The arguments for the function.
	 *
	 * @return string
	 */
	function mg_call_template_function( $callback, ...$args ) {
		$template_function = new MG_Product_Addons\Includes\Classes\Call_Template_Function();
		return $template_function->init( $callback, $args );
	}
}
