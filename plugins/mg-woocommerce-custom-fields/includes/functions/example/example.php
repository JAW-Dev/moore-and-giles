<?php
/**
 * Example Function.
 *
 * Load: true
 *
 * The function name should be "taxdomain"_"the file name"
 * If you want the function to always be loaded include "Load: true" in this comment block.
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

if ( ! function_exists( 'function_example' ) ) {
	/**
	 * Example Function.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function function_example() {
		echo 'This is an example function template tag';
		die;
	}
}
