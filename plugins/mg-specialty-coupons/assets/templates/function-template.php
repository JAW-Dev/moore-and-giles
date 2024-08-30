<?php
/**
 * Template.
 *
 * @package    MG_Specialty_Coupons
 * @subpackage MG_Specialty_Coupons/Inlcudes/Functions/Example
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'mg-specialty-coupons_template' ) ) {
	/**
	 * Template.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function mg-specialty-coupons_template() {
		echo 'This is an example function template tag';
	}
}
