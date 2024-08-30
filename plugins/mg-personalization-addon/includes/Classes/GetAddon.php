<?php
/**
 * Get Addon.
 *
 * @package    MG_Personalization_Addon
 * @subpackage MG_Personalization_Addon/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Personalization_Addon\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( 'GetAddon' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class GetAddon {

		/**
		 * Arguments.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected static $args;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version                       The plugin version.
		 *     @type string $plugin_dir_url                The plugin directory URL.
		 *     @type string $plugin_dir_path               The plugin Directory Path.
		 *     @type string $personalization_slug          The slug for the addon.
		 *     @type string $personalization_title         The title for the addon.
		 *     @type int    $personalization_price         The price of the addon.
		 *     @type string $personalization_label         The label for the addon field.
		 *     @type string $personalization_sublabel      The sublabel for the addon field.
		 *     @type string $personalization_tooltip       The text for the tooltip.
		 *     @type int    $personalization_tooltip_image The tooltip image ID.
		 *     @type string $field_id_code                 The code field ID.
		 *     @type string $field_id_enable               The enable field ID.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args ) {
			self::$args = $args;
		}

		/**
		 * Get Addon Data.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public static function data() {

			$cart = WC()->cart->get_cart_contents();

			foreach ( $cart as $cart_item ) {
				$addon = isset( $cart_item['product_addons']['addons'][ self::$args['personalization_slug'] ] ) ? $cart_item['product_addons']['addons'][ self::$args['personalization_slug'] ] : false; // Get the addon array else false.

				// If the addon is set.
				if ( $addon ) {
					return $addon;
				}
			}

			return array();
		}
	}
}
