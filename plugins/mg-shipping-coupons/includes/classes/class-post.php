<?php
/**
 * Get Post
 *
 * Get the submitted $_POST.
 *
 * @package    MG_Shipping_Coupons
 * @subpackage MG_Shipping_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Shipping_Coupons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Post' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Post {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {}

		/**
		 * Get Post.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $post The post request.
		 *
		 * @return array
		 */
		public static function request( $post = null ) {

			if ( null === $post ) {
				$post = $_POST;
			}

			// Bail and return an empty array if there is no post data.
			if ( ! $_POST || empty( $_POST ) ) { // @codingStandardsIgnoreLine
				return array();
			}

			// Ge the sanitized $_POST data.
			$sanitized = self::sanitize_array( $post ); // @codingStandardsIgnoreLine

			// If there is sanitized data return it else return false.
			return ! empty( $sanitized ) ? $sanitized : false;
		}

		/**
		 * Sanitize the $_POST array values.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $array The $post array.
		 *
		 * @return array
		 */
		public static function sanitize_array( $array ) {

			// If the $array is an array.
			if ( is_array( $array ) ) {

				// Loop through the array.
				foreach ( $array as $entry ) {

					// If entry is not an array.
					if ( ! is_array( $entry ) ) {

						// Sanitize the data.
						$entry = sanitize_text_field( $entry );
					} else { // Else if entry is an array.

						// Run through the loop again.
						self::sanitize_array( $entry );
					}
				}

				// Return the sanitized array.
				return $array;
			}
			return array();
		}
	}
}
