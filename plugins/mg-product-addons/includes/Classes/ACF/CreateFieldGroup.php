<?php
/**
 * Create Field Group
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

if ( ! class_exists( __NAMESPACE__ . '\\CreateFieldGroup' ) ) {

	/**
	 * Create Field Group
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CreateFieldGroup {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			add_action( 'acf/init', array( $this, 'add_group' ) );
			$this->add_group();
		}

		/**
		 * Add Group.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function add_group() {
			if ( function_exists( 'acf_add_local_field_group' ) ) {
				// phpcs:disable
				acf_add_local_field_group (array(
					'key' => 'group_5bae0dd026a47',
					'title' => 'WooCommerce – Product Addons',
					'fields' => array(
					array(
						'key' => 'field_5bae0ddf4fc99',
						'label' => 'Create Addon Products',
						'name' => 'woo_mg_products_product_addons',
						'type' => 'repeater',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
						),
						'collapsed' => '',
						'min' => 0,
						'max' => 0,
						'layout' => 'block',
						'button_label' => 'Add Product Addon',
						'sub_fields' => array(
						array(
							'key' => 'field_5bae4f8058cb8',
							'label' => 'Addon Product',
							'name' => '',
							'type' => 'message',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
							),
							'message' => '',
							'new_lines' => 'wpautop',
							'esc_html' => 0,
						),
						array(
							'key' => 'field_5bae0e454fc9a',
							'label' => 'Product',
							'name' => 'product',
							'type' => 'post_object',
							'instructions' => 'Select the product to be an addon.',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
							'width' => '33',
							'class' => '',
							'id' => '',
							),
							'post_type' => array(
							0 => 'product',
							),
							'taxonomy' => array(
							),
							'allow_null' => 0,
							'multiple' => 0,
							'return_format' => 'id',
							'ui' => 1,
						),
						array(
							'key' => 'field_5bae0e894fc9b',
							'label' => 'Product label',
							'name' => 'product_label',
							'type' => 'text',
							'instructions' => 'The text that will be used as the label for the product.',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
							'width' => '33',
							'class' => '',
							'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
						),
						array(
							'key' => 'field_5bae0f084fc9c',
							'label' => 'Location',
							'name' => 'location',
							'type' => 'radio',
							'instructions' => 'The location to add the product addon.',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
							'width' => '33',
							'class' => '',
							'id' => '',
							),
							'choices' => array(
							'product' => 'Product Page',
							'cart' => 'The Cart',
							),
							'allow_null' => 0,
							'other_choice' => 0,
							'save_other_choice' => 0,
							'default_value' => '',
							'layout' => 'horizontal',
							'return_format' => 'value',
						),
						array(
							'key' => 'field_5bae15304fcac',
							'label' => 'Include Additional Fields',
							'name' => 'include_additional_fields',
							'type' => 'true_false',
							'instructions' => 'You can add additional fields to add options to the product.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
							),
							'message' => '',
							'default_value' => 0,
							'ui' => 0,
							'ui_on_text' => '',
							'ui_off_text' => '',
						),
						array(
							'key' => 'field_5bae125d4fc9f',
							'label' => 'Additional Field Inputs',
							'name' => 'additional_field_inputs',
							'type' => 'repeater',
							'instructions' => 'Include additional field inputs.',
							'required' => 0,
							'conditional_logic' => array(
							array(
								array(
								'field' => 'field_5bae15304fcac',
								'operator' => '==',
								'value' => '1',
								),
							),
							),
							'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
							),
							'collapsed' => '',
							'min' => 0,
							'max' => 0,
							'layout' => 'block',
							'button_label' => 'Add Additional Field',
							'sub_fields' => array(
							array(
								'key' => 'field_5bae12c54fca0',
								'label' => 'Field Type',
								'name' => 'field_type',
								'type' => 'radio',
								'instructions' => 'Choose the Field Type',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
								),
								'choices' => array(
								'text' => 'Text Field',
								'textarea' => 'Text Area Field',
								'select' => 'Select Dropdown',
								'radio' => 'Radio Buttons',
								'checkbox' => 'Checkboxes',
								),
								'allow_null' => 0,
								'other_choice' => 0,
								'save_other_choice' => 0,
								'default_value' => '',
								'layout' => 'horizontal',
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5bae13604fca1',
								'label' => 'Field Label',
								'name' => 'label',
								'type' => 'text',
								'instructions' => 'The text that will be used as the label for the additional field.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
							),
							array(
								'key' => 'field_5bae13a84fca2',
								'label' => 'Maximum Characters',
								'name' => 'max_characters',
								'type' => 'number',
								'instructions' => 'The maximum amount of characters allowed.',
								'required' => 0,
								'conditional_logic' => array(
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'text',
									),
								),
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'textarea',
									),
								),
								),
								'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'min' => '',
								'max' => '',
								'step' => '',
							),
							array(
								'key' => 'field_5bae13e04fca4',
								'label' => 'Minimum Characters',
								'name' => 'min_characters',
								'type' => 'number',
								'instructions' => 'The minimum amount of characters required. Leave blank for no minimum.',
								'required' => 0,
								'conditional_logic' => array(
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'text',
									),
								),
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'textarea',
									),
								),
								),
								'wrapper' => array(
								'width' => '50',
								'class' => '',
								'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'min' => '',
								'max' => '',
								'step' => '',
							),
							array(
								'key' => 'field_5bae14214fca6',
								'label' => 'Options',
								'name' => 'options',
								'type' => 'textarea',
								'instructions' => 'Add the options for the field. One option per line.',
								'required' => 1,
								'conditional_logic' => array(
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'select',
									),
								),
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'radio',
									),
								),
								array(
									array(
									'field' => 'field_5bae12c54fca0',
									'operator' => '==',
									'value' => 'checkbox',
									),
								),
								),
								'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'example-value : Example Label',
								'maxlength' => '',
								'rows' => '',
								'new_lines' => '',
							),
							),
						),
						),
					),
					),
					'location' => array(
					array(
						array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'mg-product-addons',
						),
					),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'seamless',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => 1,
					'description' => '',
				));
				// phpcs:enable
			}
		}
	}
}
