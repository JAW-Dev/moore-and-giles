<?php
/**
 * Coupon.
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

if ( ! class_exists( __NAMESPACE__ . '\\Coupon' ) ) {

	/**
	 * Coupon
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon {

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
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'apply_discount' ) );
		}

		/**
		 * Apply Discount
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $cart_object The Cart Object.
		 *
		 * @return void
		 */
		public function apply_discount( $cart_object ) {
			if ( $this->apply_discount_to_user() ) {
				$amount = $this->get_discount_amount();
				$cart_object->add_fee( __( 'VIP Discount', 'moore-and-giles' ), -$amount, true, '' );
			}
		}

		/**
		 * Get Discount Amount.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return int
		 */
		public function get_discount_amount() {
			$amount          = 0;
			$applied_coupons = $this->get_vip_coupons();

			foreach ( \WC()->cart->get_cart_contents() as $cart_item ) {
				$amount += $this->get_coupons( $cart_item, $applied_coupons );
			}
			return $amount;
		}

		/**
		 * Set VIP Coupons.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param boolean $apply_to If to apply discount to user.
		 *
		 * @return boolean
		 */
		public function apply_discount_to_user() {
			$post     = Post::request();
			$email    = ( $post && isset( $post['billing_email'] ) ) && $post['billing_email'] ? $post['billing_email'] : '';
			$apply_to = false;

			if ( $this->args['vip_enabled'] ) {
				$vip_users = $this->args['vip_members'];
				$vip_users = explode( PHP_EOL, $vip_users );

				foreach ( $vip_users as $user ) {
					if ( ! $email ) {
						$current_user = wp_get_current_user();
						if ( $current_user->user_email && is_user_logged_in() ) {
							$email = $current_user->user_email;
						}
					}
					if ( $email === $user ) {
						$apply_to = true;
						break;
					}
				}
			}
			return $apply_to;
		}

		/**
		 * Get VIP Coupons.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_vip_coupons() {
			$args = array(
				'post__in'       => $this->args['vip_included_coupons'],
				'posts_per_page' => -1,
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
			);

			$coupons     = get_posts( $args );
			$vip_coupons = array();

			foreach ( $coupons as $coupon ) {
				if ( $this->args['vip_enabled'] ) {
					$vip_coupons[ $coupon->post_name ] = 'yes';
				}
			}

			return $vip_coupons;
		}

		/**
		 * Get Coupons.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $cart_item      The cart item object.
		 * @param array  $applied_coupons The applied coupons array.
		 *
		 * @return int
		 */
		public function get_coupons( $cart_item, $applied_coupons ) {
			$discount = 0;
			// Personalization Coupon.
			$personalization = $this->get_custom_coupons( $cart_item, $applied_coupons, 'MG_Personalization_Addon' );
			if ( is_array( $personalization ) && ! empty( $personalization ) ) {
				if ( isset( $personalization['code'] ) && null !== $personalization['code'] ) {
					unset( $applied_coupons[ $personalization['code'] ] );
				}
				if ( isset( $personalization['price'] ) && $personalization['price'] > 0 ) {
					$discount += $personalization['price'];
				}
			}

			// Gift Wrapping Coupon.
			$gift_wrapping = $this->get_custom_coupons( $cart_item, $applied_coupons, 'MG_Product_Addons_Gift_Wrapping' );
			if ( is_array( $gift_wrapping ) && ! empty( $gift_wrapping ) ) {
				if ( isset( $gift_wrapping['code'] ) && null !== $gift_wrapping['code'] ) {
					unset( $applied_coupons[ $gift_wrapping['code'] ] );
				}

				if ( isset( $gift_wrapping['price'] ) && $gift_wrapping['price'] > 0 ) {
					$discount += $gift_wrapping['price'];
				}
			}

			// Shipping Coupon.
			$shipping = $this->get_custom_coupons( $cart_item, $applied_coupons, 'MG_Shipping_Coupons' );
			if ( is_array( $shipping ) && ! empty( $shipping ) ) {
				if ( isset( $shipping['code'] ) ) {
					foreach ( $applied_coupons as $key => $value ) {
						if ( class_exists( '\MG_Shipping_Coupons\Includes\Classes\Coupon_Apply' ) || ! class_exists( '\MG_Shipping_Coupons\Includes\Classes\Coupon_Settings' ) ) {
							$coupon_settings = new \MG_Shipping_Coupons\Includes\Classes\Coupon_Settings();
							$coupon          = new \WC_Coupon( $key );
							$coupon_id       = $coupon->get_id();
							$settings        = $coupon_settings->get( $coupon_id );

							if ( isset( $settings['code'] ) && $settings['code'] === $key ) {
								$coupon_apply = new \MG_Shipping_Coupons\Includes\Classes\Coupon_Apply();
								$coupon_apply->apply( $key );
							}
						}
					}
					unset( $applied_coupons[ $shipping['code'] ] );
				}
				$discount += 0;
			}

			// Other Coupons.
			$other = $this->get_other_coupons( $cart_item, $applied_coupons );
			if ( is_array( $other ) && ! empty( $other ) ) {
				if ( isset( $other['price'] ) && $other['price'] > 0 ) {
					$discount += $other['price'];
				}
			}

			return $discount;
		}

		/**
		 * Get Personalization Price.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $cart_item       The cart item object.
		 * @param array  $applied_coupons The applied coupons array.
		 * @param string $class           The custom coupon class.
		 *
		 * @return int
		 */
		public function get_custom_coupons( $cart_item, $applied_coupons, $class ) {
			$price = 0;
			$array = array(
				'price' => $price,
				'code'  => null,
			);

			foreach ( $applied_coupons as $key => $value ) {
				if ( ! class_exists( '\\' . $class . '\Includes\Classes\Coupon_Discount' ) && ! class_exists( '\\' . $class . '\Includes\Classes\Coupon_Settings' ) ) {
					return $price;
				}

				$settings_class  = '\\' . $class . '\Includes\Classes\Coupon_Settings';
				$coupon_settings = new $settings_class();
				$coupon          = new \WC_Coupon( $key );
				$coupon_id       = $coupon->get_id();
				$settings        = $coupon_settings->get( $coupon_id );

				if ( isset( $settings['code'] ) && $settings['code'] === $key ) {
					$discount_class  = '\\' . $class . '\Includes\Classes\Coupon_Discount';
					$coupon_discount = new $discount_class();
					$price           = $coupon_discount->get( $price, $cart_item, $coupon ) ? $coupon_discount->get( $price, $cart_item, $coupon ) : '';

					// Break out of loop if has a price.
					if ( $price > 0 ) {
						$array = array(
							'price' => $price,
							'code'  => $key,
						);
						break;
					} else {
						$array = array(
							'price' => 0,
							'code'  => $key,
						);
						break;
					}
				}
			}
			return $array;
		}

		/**
		 * Get Other Coupons.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $cart_item      The cart item object.
		 * @param array  $applied_coupons The applied coupons array.
		 *
		 * @return int
		 */
		public function get_other_coupons( $cart_item, $applied_coupons ) {
			$price = 0;
			$array = array();

			if ( empty( $applied_coupons ) ) {
				return $price;
			}

			foreach ( $applied_coupons as $key => $value ) {
				$coupon      = new \WC_Coupon( $key );
				$price       = $coupon->get_amount();
				$total_items = count( \WC()->cart->get_cart_contents() );

				if ( 'percent' === $coupon->get_discount_type() ) {
					$price = round( $price / 100, wc_get_rounding_precision() ) / $total_items;
				} elseif ( 'fixed_cart' === $coupon->get_discount_type() ) {
					$price = $price / $total_items;
				}

				if ( $price > 0 ) {
					$array = array(
						'price' => $price,
						'code'  => $key,
					);
					break;
				}
			}

			return ! empty( $array ) ? $array : array();
		}
	}
}
