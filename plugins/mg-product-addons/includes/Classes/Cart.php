<?php
/**
 * Save Cart Addons
 *
 * @package    MG_Product_Addons
 * @subpackage MG_Product_Addons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Cart' ) ) {

	/**
	 * Save Cart Addons
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class Cart {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
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
			add_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'save_cart_addon' ), 10, 1 );
		}

		/**
		 * Save addon to cart.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param boolena $cart_updated If the cart has been updated.
		 *
		 * @return boolean $cart_updated
		 */
		public function save_cart_addon( $cart_updated ) {
			remove_filter( 'woocommerce_update_cart_action_cart_updated', array( $this, 'save_cart_addon' ), 10, 1 );
			$post   = GetPost::request(); // Get the $_POST request.
			$addons = ! empty( $post['product_addons'] ) ? $post['product_addons'] : array(); // Get the project_addons array if set else empty array.

			// If $addons is not an empty array.
			if ( ! empty( $addons ) ) {
				// Loop through the addons array.
				foreach ( $addons as $addon ) {
					$cart = WC()->cart->get_cart_contents(); // Get the current cart data.

					// Loop through the cart items.
					foreach ( $cart as $cart_item ) {
						$product_quantity = $cart_item['quantity']; // Get the product quantity.
						$has_addon        = isset( $addon['has_addon'] ) ? $addon['has_addon'] : false;
						$addon_id         = isset( $addon['addon_id'] ) ? $addon['addon_id'] : false;
						$addon_slug       = isset( $addon['addon_slug'] ) ? $addon['addon_slug'] : '';
						$addon_name       = isset( $addon['addon_name'] ) ? $addon['addon_name'] : '';
						$product_key      = isset( $addon['product_key'] ) ? $addon['product_key'] : false;
						$cart_item_key    = isset( $cart_item['key'] ) ? $cart_item['key'] : false;
						$validated        = apply_filters( 'woocommerce_add_to_cart_validation', true, $addon_id, $product_quantity );
						$cart_item_addon  = isset( $cart_item['product_addons']['addons'][ 'addon-' . $addon_slug ]['addon_id'] ) ? $cart_item['product_addons']['addons'][ 'addon-' . $addon_slug ]['addon_id'] : false;
						$target_addon     = isset( $cart[ $cart_item_key ]['product_addons']['addons'][ 'addon-' . $addon_slug ] ) ? $cart[ $cart_item_key ]['product_addons']['addons'][ 'addon-' . $addon_slug ] : array();

						$addon_items = array(
							'addon'           => $addon,
							'addon_slug'      => $addon_slug,
							'addon_name'      => $addon_name,
							'product_key'     => $product_key,
							'cart_item_key'   => $cart_item_key,
							'validated'       => $validated,
							'cart_item_addon' => $cart_item_addon,
							'target_addon'    => $target_addon,
						);

						// If the $this->product_key and $this->cart_item_key match.
						if ( $product_key == $cart_item_key ) { // phpcs:ignore
							// If has_addon is set and cart item doesn't have the addon set, Insert the addon.
							if ( $has_addon && ! $cart_item_addon ) {
								$this->insert_addon( $addon_items );
							} elseif ( ! $has_addon && $cart_item_addon ) { // If has_addon is not set and cat iteam has the addon. Remove the addon.
								$this->remove_addon( $addon_items );
							}

							$cart_updated = true;
						}
					}
				}
			}

			// Always return true!!!
			// There's a bug with this filter where in the method this filter exists
			// calls a method in wp-includes/class-wp-list-util.php WP_List_Util() class.
			// The method in the class requies an array to be set else it throws an error.
			// Returning anything but True here will not send the array to the WP_List_Util() class method.
			return $cart_updated;
		}

		/**
		 * Insert Addon.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $addon_items The addon array.
		 *
		 * @return void
		 */
		public function insert_addon( $addon_items ) {
			$addon_id = isset( $addon_items['addon']['addon_id'] ) ? $addon_items['addon']['addon_id'] : false;

			// If cart item key and addon product key match and
			// If the cart item addon ID and addon ID do not match and
			// is validated.
			if ( $addon_items['cart_item_key'] == $addon_items['product_key'] && $addon_items['cart_item_addon'] != $addon_id && $addon_items['validated'] ) { // phpcs:ignore

				// Unset unwanted array keys.
				unset( $addon_items['addon']['product_id'] );
				unset( $addon_items['addon']['product_key'] );

				// If the target addon is set and is not an empty array.
				if ( ! isset( $addon_items['target_addon'] ) || empty( $addon_items['target_addon'] ) ) {

					// Add the addon to the cart item meta.
					WC()->cart->cart_contents[ $addon_items['cart_item_key'] ]['product_addons']['addons'][ 'addon-' . $addon_items['addon_slug'] ] = $addon_items['addon'];

					// Set the cart session.
					WC()->cart->set_session();
				}
			}
		}

		/**
		 * Remove Addon.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $addon_items The addon items.
		 *
		 * @return void
		 */
		public function remove_addon( $addon_items ) {

			// If cart item key and addon product key match and
			// If the cart item addon ID and addon ID do not match and
			// is validated.
			if ( $addon_items['cart_item_key'] === $addon_items['product_key'] && isset( $addon_items['cart_item_addon'] ) && $addon_items['validated'] ) {

				// If the target addon is not set.
				if ( ! empty( $addon_items['target_addon'] ) ) {

					// Unset the addon from the cart item.
					unset( WC()->cart->cart_contents[ $addon_items['cart_item_key'] ]['product_addons']['addons'][ 'addon-' . $addon_items['addon_slug'] ] );

					// Set the cart session.
					WC()->cart->set_session();
				}
			}
		}
	}
}
