<?php
/**
 * Field Container Markup
 *
 * @package    MG_Product_Addons_Gift_Wrapping
 * @subpackage MG_Product_Addons_Gift_Wrapping/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons_Gift_Wrapping\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\FieldContainerMarkup' ) ) {

	/**
	 * Field Container Markup
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class FieldContainerMarkup {

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
		 * Field Container markup.
		 *
		 * Custom Addon markup.
		 *
		 * @author Jason Witt
		 *
		 * @param array $args {
		 *      The arguments.
		 *
		 *      @type string $addon_id        The addon ID.
		 *      @type string $addon_slug      The addon slug.
		 *      @type string $addon_label     The addon label.
		 *      @type string $tooltip         The tooltip text.
		 *      @type string $cart_product_id The cart item product ID.
		 *      @type array $arguments = array(
		 *          @type array  $addon_args        The addon arguments.
		 *          @type string $hidden_fields     The hidden fields markup.
		 *          @type string $additional_fields The additional fields markup.
		 *          @type array  $cart_item         The cart item array.
		 *      )
		 * }
		 * @param int   $price The price.
		 *
		 * @return string
		 */
		public static function render( $args, $price ) {
			$currency_symbol   = ( function_exists( 'get_woocommerce_currency_symbol' ) ) ? get_woocommerce_currency_symbol() : '';
			$cart_item         = isset( $args['arguments']['cart_item'] ) ? $args['arguments']['cart_item'] : array();
			$addon_slug        = isset( $args['addon_slug'] ) ? $args['addon_slug'] : '';
			$hidden_fields     = isset( $args['arguments']['hidden_fields'] ) ? $args['arguments']['hidden_fields'] : '';
			$addon_label       = isset( $args['addon_label'] ) ? $args['addon_label'] : '';
			$additional_fields = isset( $args['arguments']['additional_fields'] ) ? $args['arguments']['hidden_fiadditional_fieldselds'] : '';
			$tooltip           = isset( $args['tooltip'] ) ? $args['tooltip'] : '';
			$checked           = '';

			// If the addon checkbox is checked set checked attribute.
			if ( isset( $cart_item['product_addons']['addons'][ 'addon-' . $addon_slug ] ) ) {
				$checked = ' checked="checked" ';
			}

			$html  = '<div id="addon-field-container-' . $cart_item['key'] . '" class="addon-field-container gift-wrapping-addon-field-container">';
			$html .= ( $hidden_fields ) ? $hidden_fields : '';
			$html .= '<input id="gift-wrapping-name-' . $cart_item['key'] . '" type="hidden" name="product_addons[' . $cart_item['key'] . '][addon_name]" value="Gift Wrapping">';
			$html .= '<input id="gift-wrapping-sku-name-' . $cart_item['key'] . '" type="hidden" name="product_addons[' . $cart_item['key'] . '][addon_sku_name]" value="Gift Wrapping Box">';
			$html .= '<div id="addon-field-wrapper-' . $cart_item['key'] . '" class="addon-field-wrapper ' . $addon_slug . ' checkbox-wrap">';
			$html .= '<input id="gift-wrapping-checkbox-' . $cart_item['key'] . '" type="checkbox"' . $checked . ' name="product_addons[' . $cart_item['key'] . '][has_addon]" class="styled-checkbox product-addon add-product-addon product-addon-field-' . $addon_slug . ' checkbox">';
			$html .= '<label class="product-addon-label" for="gift-wrapping-checkbox-' . $cart_item['key'] . '"><span class="product-addon-label-text">' . $addon_label . '</span></label>';
			$html .= ( $additional_fields ) ? $additional_fields : '';
			$html .= ( $tooltip ) ? '<span class="help-icon tooltip" data-tippy-content="' . $tooltip . '"></span>' : '';
			$html .= '<span class="addon-field-price">+' . $currency_symbol . $price . '</span>';
			$html .= '</div>';
			$html .= '</div>';

			return $html;
		}

		/**
		 * Is in array.
		 *
		 * Verify if the target is set in the array.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $array    The array to test against.
		 * @param array $term_ids The IDs to test against the $array.
		 *
		 * @return boolean
		 */
		public static function is_in_array( $array, $term_ids ) {
			if ( empty( $array ) || empty( $term_ids ) ) {
				return false;
			}
			$includes = array_intersect( $term_ids, $array );
			return ( ! empty( $includes ) ) ? true : false;
		}
	}
}
