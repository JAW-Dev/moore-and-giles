<?php
/**
 * Render Fields.
 *
 * This class outputs the markup for the addon.
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

if ( ! class_exists( __NAMESPACE__ . '\\RenderFields' ) ) {

	/**
	 * Render Fields
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class RenderFields {

		/**
		 * Properties.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 */
		/** @var array */
		protected $args;
		/** @var int */
		protected $cart_item_product_id;
		/** @var array */
		protected $cart_item;
		/** @var int */
		protected $cart_item_key;
		/** @var array  */
		protected $addons;
		/** @var int */
		protected $addon_count;
		/** @var array */
		protected $addon_args;


		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version         The plugin version.
		 *     @type string $plugin_dir_url  The plugin directory URL.
		 *     @type string $plugin_dir_path The plugin Directory Path.
		 *     @type array  $addons          The addons array.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			// Bail if the arguments are not set.
			if ( empty( $args ) ) {
				return;
			}
			$this->args = $args;
			$this->hooks();
		}

		/**
		 * Render Fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function render_fields() {
			if ( $this->addons ) {
				foreach ( $this->addons as $addon ) {
					// Bail if there is no product ID.
					if ( ! isset( $addon['product'] ) ) {
						return;
					}
					$this->addon_args = $this->addon_arguments( $addon );
					echo $this->product_addon_container( $addon );
				}
			}
		}

		/**
		 * Addon Arguments.
		 *
		 * Setup the arguments needed for the markup.
		 *
		 * @author Jason Witt
		 *
		 * @param array $addon The addon.
		 *
		 * @return array
		 */
		public function addon_arguments( $addon ) {
			global $post;

			// Get the product ID from the cart.
			$cart_product_id = ( $this->cart_item_product_id !== null ) ? $this->cart_item_product_id : null;

			// Get the addon product information.
			$addon_product_id = ( isset( $addon['product'] ) ) ? $addon['product'] : '';
			$addon_product    = ( function_exists( 'wc_get_product' ) ) ? wc_get_product( $addon_product_id ) : false;

			// Add to the $addon array.
			$addon['price'] = ( method_exists( $addon_product, 'get_price' ) && $addon_product ) ? $addon_product->get_price() : '';
			$addon['sku']   = ( method_exists( $addon_product, 'get_sku' ) && $addon_product ) ? $addon_product->get_sku() : '';
			$addon['label'] = ( isset( $addon['product_label'] ) ) ? $addon['product_label'] : '';
			$addon['slug']  = ( method_exists( $addon_product, 'get_slug' ) && $addon_product ) ? $addon_product->get_slug() : '';

			// The addon.
			$field_id = apply_filters( 'objectiv_addon_container_field_id', $addon['slug'] . '_addon', $addon['slug'] );

			// Get the additional fileds if any are set in the Addon Settings.
			$additional_fields = ( isset( $addon['additional_field_inputs'] ) && ! empty( $addon['additional_field_inputs'] ) ) ? $addon['additional_field_inputs'] : '';

			// The number of addon fields to render. Default 1. Adds any additional fields.
			$count = ( isset( $addon['additional_field_inputs'] ) && ! empty( $addon['additional_field_inputs'] ) ) ? count( $addon['additional_field_inputs'] ) : 1;

			$addon_arguments = array(
				'addon'             => $addon,
				'cart_product_id'   => $cart_product_id,
				'field_id'          => $field_id ,
				'additional_fields' => $additional_fields,
				'count'             => $count,
			);

			return $addon_arguments;
		}

		/**
		 * Product Addon Container.
		 *
		 * The main wrapper container for the markup.
		 *
		 * @author Jason Witt
		 *
		 * @return string
		 */
		public function product_addon_container( $addon ) {

			do_action( "before_addon_field_container_{$this->addon_args['addon']['slug']}" );

			$html  = '<div class="addon-field-container">';
			$html .= $this->hidden_fields();
			$html .= '<div class="addon-field-wrapper ' . $this->addon_args['addon']['slug'] . ' checkbox-wrap">';
			$html .= '<input type="checkbox" name="product_addons[' . $this->cart_item_key . '][addons][product_addon]" class="styled-checkbox product-addon add-product-addon product-addon-field-' . $this->addon_args['addon']['slug'] . ' checkbox">';
			// $html .= '<label class="product-addon-label><span class="product-addon-label-text">' . $this->addon_args['addon']['label'] . '</span></label>';
			$html .= '</div>';
			$html .= $this->get_additional_fields();
			$html .= '</div>';
			do_action( "after_addon_field_container_{$this->addon_args['addon']['slug']}" );

			$args = array(
				'addon_args'        => $this->addon_args,
				'hidden_fields'     => $this->hidden_fields(),
				'additional_fields' => $this->get_additional_fields(),
				'cart_item'         => $this->cart_item,
			);

			return apply_filters( 'objectiv_addon_field_container', $html, $args );
		}

		/**
		 * Hidden Fields.
		 *
		 * The hidden fields needed for the $_POST
		 * when selecting the addon.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return string
		 */
		public function hidden_fields() {
			$field_args = array(
				'addon_id'    => 'product_addons[' . $this->cart_item_key . '][addon_id]',
				'addon_slug'  => 'product_addons[' . $this->cart_item_key . '][addon_slug]',
				'addon_label' => 'product_addons[' . $this->cart_item_key . '][addon_label]',
				'addon_price' => 'product_addons[' . $this->cart_item_key . '][addon_price]',
				'addon_sku'   => 'product_addons[' . $this->cart_item_key . '][addon_sku]',
				'product_id'  => 'product_addons[' . $this->cart_item_key . '][product_id]',
				'product_key' => 'product_addons[' . $this->cart_item_key . '][product_key]',
			);

			$html = '';

			$html .= apply_filters(
				'objectiv_addon_hidden_addon_id',
				'<input id="product-' . $this->cart_item_key . '" name="' . $field_args['addon_id'] . '" value="' . $this->addon_args['addon']['product'] . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			$html .= apply_filters(
				'objectiv_addon_hidden_addon_label',
				'<input id="addon_label-' . $this->cart_item_key . '" name="' . $field_args['addon_label'] . '" value="' . $this->addon_args['addon']['label'] . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			$html .= apply_filters(
				'objectiv_addon_hidden_addon_slug',
				'<input id="addon_slug-' . $this->cart_item_key . '" name="' . $field_args['addon_slug'] . '" value="' . $this->addon_args['addon']['slug'] . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			$html .= apply_filters(
				'objectiv_addon_hidden_addon_price',
				'<input id="addon_price-' . $this->cart_item_key . '" name="' . $field_args['addon_price'] . '" value="' . $this->addon_args['addon']['price'] . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			$html .= apply_filters(
				'objectiv_addon_hidden_addon_sku',
				'<input id="addon_sku-' . $this->cart_item_key . '" name="' . $field_args['addon_sku'] . '" value="' . $this->addon_args['addon']['sku'] . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			$html .= apply_filters(
				'objectiv_addon_hidden_product_id',
				'<input id="product_id-' . $this->cart_item_key . '" name=' . $field_args['product_id'] . ' value="' . $this->addon_args['cart_product_id'] . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			$html .= apply_filters(
				'objectiv_addon_hidden_product_key',
				'<input id="product_key-' . $this->cart_item_key . '" name="' . $field_args['product_key'] . '" value="' . $this->cart_item_key . '" type="hidden">',
				$field_args,
				$this->addon_args,
				$this->cart_item_key
			);

			return apply_filters( 'objectiv_addon_hidden_fields', $html, $field_args, $this->addon_args, $this->cart_item_key );
		}

		/**
		 * Get additional fields.
		 *
		 * If there are additional fields setup,
		 * render the markup.
		 *
		 * @author Jason Witt
		 *
		 * @return string
		 */
		private function get_additional_fields() {
			// Bail if fields is null.
			if ( ! isset( $this->addon_args['addon']['additional_field_inputs'] ) || empty( $this->addon_args['addon']['additional_field_inputs'] ) ) {
				return;
			}

			// Set the count to 0 by default.
			$count = 0;

			// Loop through the additional fields.
			foreach ( $this->addon_args['addon']['additional_field_inputs'] as $field ) {
				$type    = ( isset( $field['field_type'] ) ) ? $field['field_type'] : ''; // Get $field['field_type'] if is set else empty string.
				$label   = ( isset( $field['label'] ) ) ? $field['label'] : ''; // Get $field['label'] if is set else empty string.
				$max     = ( isset( $field['max_characters'] ) ) ? $field['max_characters'] : ''; // Get $field['max_characters'] if is set else empty string.
				$min     = ( isset( $field['min_characters'] ) ) ? $field['min_characters'] : ''; // Get $field['min_characters'] if is set else empty string.
				$options = ( isset( $field['options'] ) ) ? $field['options'] : ''; // Get $field['option'] if is set else empty string.

				// Format the label for the markup attributes.
				$format_label = apply_filters( 'objectiv_addon_subfield_wrapper_format_label', strtolower( str_replace( ' ', '_', $label ) ), $label);

				// Filter for the formated label.
				$formatted_label = apply_filters( "objectiv_addon_subfield_wrapper_formatted_label_{$format_label}", $format_label );
				$name            = 'product_addons[addons][fields][' . $count . ']';
				$options_items   = array();

				// Get the fields options and format them.
				if ( $options !== null || ! empty( $options ) ) {
					$options_items = $this->format_options( $options );
				}

				// Set up the arguments array.
				$fields_args = array(
					'type'            => $type,
					'label'           => $label,
					'max'             => $max,
					'min'             => $min,
					'format_label'    => $format_label,
					'formatted_label' => $formatted_label,
					'name'            => $name,
					'options_items'   => $options_items,
				);

				$html  = '';
				$select_markup   = '';
				$radio_markup    = '';
				$checkbox_markup = '';
				$count++;

				do_action( "before_addon-subfield-wrapper_{$this->addon_args['addon_slug']}" );

				// this begins the markup for the field types.
				$html .= '<div class="addon-subfield-wrapper ' . $this->addon_args['field_id'] . '-addon-subfield-wrapper">';

				// The select options markup.
				if ( $type === 'select' ) {
					foreach ( $fields_args['options_items'] as $key => $value ) {
						$select_markup .= apply_filters( 'objectiv_addon_select_options', '<option value="' . $key . '">' . $value . '</option>', $key, $value );
					}
				}

				// Radio button markup.
				if ( $type === 'radio' ) {
					foreach ( $fields_args['options_items'] as $key => $value ) {
						$radio_markup .= apply_filters(
							'objectiv_addon_radio_buttons',
							'<div class="addon-subfield ' . $this->addon_args['field_id'] . '-radio-wrap radio-wrap">
							<input name="' . $fields_args['name'] . '[label]" value="' . $fields_args['label'] . '"type="hidden">
							<input name="' . $fields_args['name'] . '[type]" value="' . $fields_args['type'] . '"type="hidden">
							<input type="radio" name="' . $fields_args['name'] . '[value]" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield radio" value="' . $key . '">
							<label>' . $value . '</label>
							</div>',
							$this->addon_args,
							$key,
							$value
						);
					}
				}

				// Checkbox markup.
				if ( $fields_args['type'] === 'checkbox' ) {
					foreach ( $fields_args['options_items'] as $key => $value ) {
						$checkbox_markup .= apply_filters(
							'objectiv_addon_checkboxes',
							'<div class="addon-subfield ' . $this->addon_args['field_id'] . '-checkbox-wrap checkbox-wrap">
							<input type="checkbox" name="' . $fields_args['name'] . '[value][]" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield checkbox" value="' . $key . '">
							<label>' . $value . '</label>
							</div>',
							$this->addon_args,
							$key,
							$value
						);
					}
				}

				// Render the markup by field type.
				switch ( $fields_args['type'] ) {

					// Text field markup.
					case 'text':
						$html .= apply_filters(
							'objectiv_addon_text_field',
							'<div class="addon-subfield ' . $this->addon_args['field_id'] . '-text-wrap text-wrap">
							<label>' . $fields_args['label'] . '</label>
							<input type="text" name="' . $fields_args['name'] . '[value]" min="' . $fields_args['min'] . '" max="' . $fields_args['max'] . '" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield text">
							<input name="' . $fields_args['name'] . '[type]" value="' . $fields_args['type'] . '"type="hidden">
							<input name="' . $fields_args['name'] . '[label]" value="' . $fields_args['label'] . '"type="hidden">
							</div>',
							$this->addon_args,
							$fields_args
						);
						break;

					// Text area Markup.
					case 'textarea':
						$html .= apply_filters(
							'objectiv_addon_textarea',
							'<div class="addon-subfield ' . $this->addon_args['field_id'] . '-textarea-wrap textarea-wrap">
							<label>' . $fields_args['label'] . '</label>
							<input name="' . $fields_args['name'] . '[label]" value="' . $fields_args['label'] . '"type="hidden">
							<input name="' . $fields_args['name'] . '[type]" value="' . $fields_args['type'] . '"type="hidden">
							<textarea name="' . $fields_args['name'] . '[value]" min="' . $fields_args['min'] . '" max="' . $fields_args['max'] . '" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield textarea"></textarea>
							</div>',
							$this->addon_args,
							$fields_args
						);
						break;

					// Select markup.
					case 'select':
						$html .= apply_filters(
							'objectiv_addon_select',
							'<div class="addon-subfield ' . $this->addon_args['field_id'] . '-select-wrap select-wrap">
							<label>' . $fields_args['label'] . '</label>
							<input name="' . $fields_args['name'] . '[label]" value="' . $fields_args['label'] . '"type="hidden">
							<input name="' . $fields_args['name'] . '[type]" value="' . $fields_args['type'] . '"type="hidden">
							<select name="' . $fields_args['name'] . '[value]" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield select">' . $select_markup . '</select>
							</div>',
							$this->addon_args,
							$fields_args
						);
						break;

					// Radio markup.
					case 'radio':
						$html .= $radio_markup;
					break;

					// Checkbox markup.
					case 'checkbox':
						$html .= '<input name="' . $fields_args['name'] . '[label]" value="' . $fields_args['label'] . '"type="hidden"><input name="' . $fields_args['name'] . '[type]" value="' . $fields_args['type'] . '"type="hidden">' . $checkbox_markup;
						break;

					// Defalts to text field.
					default:
						$html .= apply_filters(
							'objectiv_addon_default',
							'<div class="addon-subfield ' . $this->addon_args['field_id'] . '-text-wrap text-wrap">
							<label>' . $fields_args['label'] . '</label>
							<input type="text" name="' . $fields_args['name'] . '[value]" min="' . $fields_args['min'] . '" max="' . $fields_args['max'] . '" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield text">
							<input name="' . $fields_args['name'] . '[type]" value="' . $fields_args['type'] . '"type="hidden">
							<input type="text" name="' . $fields_args['name'] . '[label]" class="addon-subfield ' . $this->addon_args['field_id'] . '-addon-subfield text">
							</div>',
							$this->addon_args,
							$fields_args
						);
						$count++;
				}

				$html .= '</div>';
			}

			do_action( "after_addon-subfield-wrapper_{$this->addon_args['addon_slug']}" );

			return apply_filters( 'objectiv_addon_additional_fields', $html, $this->addon_args, $fields_args );
		}

		/**
		 * Format the raw options text into an array.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		private function format_options( $options ) {

			// Bail if options is null.
			if ( null === $options || empty( $options ) ) {
				return;
			}

			$raw_data = explode( PHP_EOL, $options );
			$array    = array();

			foreach ( $raw_data as $data ) {
				$formatted                      = explode( ':', $data );
				$array[ trim( $formatted[0] ) ] = trim( $formatted[1] );
			}

			return $array;
		}
	}
}
