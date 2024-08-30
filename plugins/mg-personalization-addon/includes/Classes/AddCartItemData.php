<?php
/**
 * Add Cart Item Data
 *
 * Insert the addon data into the cart.
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

if ( ! class_exists( 'AddCartItemData' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class AddCartItemData {

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
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'item_data' ), 10, 3 );
		}

		/**
		 * Item Data.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $cart_item_data The cart item data.
		 * @param int   $product_id     The product ID.
		 * @param int   $variation_id   The variation ID.
		 *
		 * @return array
		 */
		public function item_data( $cart_item_data, $product_id, $variation_id ) {
			$post      = Post::request(); // Get the $_POST array.
			$has_addon = isset( $post['has_addon'] ) && $post['has_addon'] ? $post['has_addon'] : false;
			$addons    = ! empty( $post[ $this->args['personalization_slug'] ] ) ? $post[ $this->args['personalization_slug'] ] : false;
			$product   = function_exists( 'wc_get_product' ) && wc_get_product( $product_id ) ? wc_get_product( $product_id ) : false;
			$price     = $product && method_exists( $product, 'get_price' ) && $product->get_price() ? $product->get_price() : 0;
			$submition = '';

			// Bail and return empty array if not set.
			if ( empty( $post ) || ! $has_addon || ! $addons ) {
				return $cart_item_data;
			}

			// Loop through the addons.
			foreach ( $addons as $addon ) {
				if ( isset( $addon ) ) {
					$submition .= $addon;
				}
			}

			if ( ! empty( $submition ) ) {

				// Set the addon data to the cart item data.
				$cart_item_data['product_addons']['addons'][ $this->args['personalization_slug'] ] = array(
					'addon_label' => $this->args['personalization_title'],
					'addon_slug'  => $this->args['personalization_slug'],
					'addon_value' => strtoupper( $submition ),
					'addon_price' => $this->args['personalization_price'],
					'has_addon'   => str_replace( 'on', 'true', $has_addon ),
				);

				$cart_item_data['total_price'] = $price + $this->args['personalization_price'];
			}

			return $cart_item_data;
		}
	}
}
