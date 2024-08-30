<?php
/**
 * Display Fields
 *
 * Display the addon fields on the product page.
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

if ( ! class_exists( 'DisplayFields' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class DisplayFields {

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
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'localized_scripts' ), 20 );
			add_action( 'wp_ajax_show_personaliztion', array( $this, 'ajax_show_field' ) );
			add_action( 'wp_ajax_nopriv_show_personaliztion', array( $this, 'ajax_show_field' ) );
		}

		/**
		 * Render.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function render() {
			/** <label class="product-addon-label"></label> */
			printf(
				'<div class="addon-field-container personalization-addon-field-container">
					<div class="addon-field-wrapper personalization checkbox-wrap">
						<input id="personalization-checkbox" name="has_addon" class="styled-checkbox product-addon add-product-addon product-addon-field-personalization checkbox" type="checkbox">
						<div class="addon-subfield-wrapper personalization_addon-addon-subfield-wrapper">
							<div class="addon-subfield personalization_addon-text-wrap text-wrap">
								<label for="personalization-checkbox">%2$s</label>
								<input name="%1$s[one]" maxlength="1" class="addon-subfield personalization_addon-addon-subfield text first" type="text">
								<input name="%1$s[two]" maxlength="1" class="addon-subfield personalization_addon-addon-subfield text second" type="text">
								<input name="%1$s[three]" maxlength="1" class="addon-subfield personalization_addon-addon-subfield text last" type="text">
							</div>
						</div>
						<span class="help-icon personalization-tooltip" tabindex="0"></span>
						<span class="addon-field-price">+$%4$s</span>
					</div>
				</div>',
				esc_html( $this->args['personalization_slug'] ),
				esc_html( $this->args['personalization_label'] ),
				esc_html( $this->args['personalization_sublabel'] ),
				esc_html( $this->args['personalization_price'] )
			);
		}

		/**
		 * Enqueue Localized Personalization Scripts.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function localized_scripts() {
			wp_localize_script( 'product_addons_scripts', 'personalizationEnabled', $this->show_field() );
			wp_localize_script( 'product_addons_scripts', 'personalizationTooltip', $this->tooltip() );
		}

		/**
		 * Tooltip.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function tooltip() {
			global $product;

			// Bail if there is no product.
			if ( ! $product ) {
				return;
			}

			$data = array();

			// Set the default data.
			$data['default']['message'] = isset( $this->args['personalization_tooltip'] ) ? $this->args['personalization_tooltip'] : '';
			// $data['default']['image']   = isset( wp_get_attachment_image_src( $this->args['personalization_tooltip_image'], 'medium' )[0] ) ? wp_get_attachment_image_src( $this->args['personalization_tooltip_image'], 'medium' )[0] : '' ;

			// Set the variants data.
			if ( $product->is_type( 'variable' ) ) {
				$variants = $product->get_available_variations() ? $product->get_available_variations() : array();

				if ( ! empty( $variants ) ) {
					foreach ( $variants as $variant ) {
						$variant_id = $variant['variation_id'];
						$slug       = $variant['attributes']['attribute_pa_color'];

						$message = get_post_meta( $variant_id, 'variant_personalization_tooltip', true ) ? get_post_meta( $variant_id, 'variant_personalization_tooltip', true ) : '';
						$message = empty( $message ) ? get_post_meta( $product->get_id(), 'personalization_tooltip', true ) : $message;

						$set_color   = get_post_meta( $variant_id, 'attribute_pa_color', true );
						$_product    = wc_get_product( $variant_id );
						$_product_id = $product->get_id();
						$colors      = get_the_terms( $_product_id, 'pa_color' );
						$image_src   = '';

						if ( ! is_array( $colors ) ) {
							continue;
						}

						foreach ( $colors as $color ) {
							if ( $color->slug === $set_color ) {
								$term_id   = $color->term_id ? $color->term_id : '';
								$image_id  = get_term_meta( $color->term_id, 'personalization_attribute_image', true );
								$image_src = $image_id ? wp_get_attachment_image_src( $image_id, 'thumbnail' )[0] : '/wp-content/uploads/2020/01/image-4-315x0-c-default.png';
								break;
							}
						}

						$data['variations'][] = array(
							'id'      => $variant_id,
							'slug'    => $slug,
							'message' => $message,
							'image'   => $image_src,
						);
					}
				}
			}

			// Set the simple product data.
			if ( $product->is_type( 'simple' ) ) {
				$product_id = $product->get_id();
				$message    = get_post_meta( $product_id, 'personalization_tooltip', true ) ? get_post_meta( $product_id, 'personalization_tooltip', true ) : '';
				$color      = get_the_terms( $product_id, 'pa_color' );
				$term_id    = $color && isset( $color[0] ) ? $color[0]->term_id : '';
				$image_id   = get_term_meta( $term_id, 'personalization_attribute_image', true );
				$image_src  = $image_id ? wp_get_attachment_image_src( $image_id, 'thumbnail' )[0] : '/wp-content/uploads/2020/01/image-4-315x0-c-default.png';

				$data['simple'] = array(
					'id'      => $product_id,
					'message' => $message,
					'image'   => $image_src,
				);
			}

			return $data;
		}

		/**
		 * Show Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function show_field() {
			if ( is_product() ) {
				global $product;

				// Bail if there is no product.
				if ( ! $product ) {
					return;
				}

				// Set the simple product data.
				$product_id = $product->get_id();

				return $this->get_showed_fields( $product, $product_id );
			}
		}

		/**
		 * Ajax Show Field
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function ajax_show_field() {
			$product_id = sanitize_text_field( wp_unslash( $_POST['productId'] ?? '' ) );

			if ( empty( $product_id ) ) {
				echo '';
				wp_die();
			}

			$product = wc_get_product( $product_id );

			echo wp_json_encode( $this->get_showed_fields( $product, $product_id ) );
			wp_die();
		}

		/**
		 * Get Showed Fields
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_showed_fields( $product, $product_id ) {
			$data    = array();
			$enabled = get_post_meta( $product_id, 'disable_personalization', true );

			// Set the variants data.
			if ( $product->is_type( 'variable' ) ) {
				$variants = $product->get_available_variations() ? $product->get_available_variations() : array();

				if ( ! empty( $variants ) ) {
					foreach ( $variants as $variant ) {
						$variant_id = $variant['variation_id'];
						$slug       = $variant['attributes']['attribute_pa_color'];
						$v_enabled  = get_post_meta( $variant_id, 'varaint_disable_personalization', true );
						if ( ! $v_enabled && ! $enabled ) {
							$data['variations'][] = $variant_id;
						}
					}
				}
			} else {
				if ( ! $enabled ) {
					$data['simple'] = array(
						'id' => $product_id,
					);
				}
			}

			return $data;
		}
	}
}
