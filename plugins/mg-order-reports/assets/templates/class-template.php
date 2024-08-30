<?php
/**
 * Template.
 *
 * @package    MG_Order_Reports
 * @subpackage MG_Order_Reports/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace MGOrderReports\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Template' ) ) {

	/**
	 * Template.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Template {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {}
	}
}
