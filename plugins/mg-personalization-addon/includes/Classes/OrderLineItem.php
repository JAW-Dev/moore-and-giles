<?php
/**
 * Order Line Item.
 *
 * Show the addon in the order and email.
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

if ( ! class_exists( 'OrderLineItem' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class OrderLineItem {

		/**
		 * Arguments.
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
			$this->args = $args;
			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'item' ), 0, 4 );
		}

		/**
		 * Update Total.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $item          The item object.
		 * @param int    $cart_item_key The cart item key.
		 * @param array  $values        The values array.
		 * @param object $order         The order object.
		 *
		 * @return void
		 */
		public function item( $item, $cart_item_key, $values, $order ) {

			$addon =
				isset( $values['product_addons']['addons'][ $this->args['personalization_slug'] ] ) &&
				! empty( $values['product_addons']['addons'][ $this->args['personalization_slug'] ] ) ?
				$values['product_addons']['addons'][ $this->args['personalization_slug'] ] :
				array(); // Get the values addon data else empty array.

			$addon_value = ! empty( $addon ) && isset( $addon['addon_value'] ) ? $addon['addon_value'] : false; // Get the addon value else false.

			// If addon value is set.
			if ( $addon_value ) {
				// Set the addon to the order.
				$item->add_meta_data( $this->args['personalization_title'], $addon_value, true );
			}

		}
	}
}
