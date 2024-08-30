<?php
/**
 * Template.
 *
 * @package    MG_VIP_Customer
 * @subpackage MG_VIP_Customer/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_VIP_Customer\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Template' ) ) {

	/**
	 * Template
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Template {

		/**
		 * Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $args;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version                   The plugin version.
		 *     @type string $plugin_dir_url            The plugin directory URL.
		 *     @type string $plugin_dir_path           The plugin Directory Path.
		 *     @type string $field_id_code             The code field ID.
		 *     @type string $field_id_enable           The enable field ID.
		 *     @type string $field_id_included_coupons The included coupons field ID.
		 *     @type string $field_id_members          The members field ID.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->args = $args;
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {}
	}
}
