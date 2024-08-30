<?php
/**
 * Template.
 *
 * @package    Shipping_ATP
 * @subpackage Shipping_ATP/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace ShippingATP\Includes\Classes;

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
