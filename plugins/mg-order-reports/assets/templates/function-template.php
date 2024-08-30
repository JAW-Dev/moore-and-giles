<?php
/**
 * Template.
 *
 * @package    MG_Order_Reports
 * @subpackage MG_Order_Reports/Inlcudes/Functions/Example
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'mg_order_reports_template' ) ) {
	/**
	 * Template.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	function mg_order_reports_template() {
		echo 'This is an example function template tag';
	}
}
