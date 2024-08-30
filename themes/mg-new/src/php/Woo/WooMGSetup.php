<?php

namespace Objectiv\Site\Woo;

use Mpdf\Tag\P;
use \Timber\Timber;
use \Objectiv\Site\Woo\Emails;

class WooMGSetup {

	/**
	 * WooMGSetup constructor.
	 */
	public function __construct() {
		$this->remove_actions();
		$this->remove_filters();
		$this->load_filters();
		$this->load_actions();
		$this->load_email_actions();
	}

	/**
	 * Load the filters relative to MG WooCommerce
	 */
	public function load_filters() {
		add_filter( 'objectiv_site_timber_woo_single_product_context', array( $this, 'single_product_context' ) );

		add_filter( 'objectiv_site_timber_woo_archive_context', array( $this, 'archive_context' ) );

		add_filter( 'objectiv_site_timber_woo_category_context', array( $this, 'category_context' ) );

		add_filter( 'objectiv_site_timber_after_woo_template_context', array( $this, 'after_template_context' ) );

		// Product Summary.
		add_filter( 'get_variants_radio_pa_color', array( 'Objectiv\Site\Utilities\ProductVariationSwatches', 'render' ), 99, 2 );

		// Change the default price text for most things and regular products.
		add_filter( 'formatted_woocommerce_price', array( 'Objectiv\Site\Utilities\CustomPrice', 'output' ), 10, 2 );

		// Availability text filter.
		add_filter( 'woocommerce_get_availability', array( 'Objectiv\Site\Utilities\StockAvailability', 'stock_text' ), 10, 2 );

		// Add to cart button label filter
		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'change_add_to_cart_label_for_backorders' ), 10, 2 );

		// Index multiple color data sources for facetWP.
		add_filter( 'facetwp_index_row', array( 'Objectiv\Site\Utilities\FacetwpColorIndexing', 'index' ), 10, 2 );

		// Remove counts from dropdowns.
		add_filter( 'facetwp_facet_dropdown_show_counts', '__return_false' );

		// Custom sort facet Options.
		add_filter( 'facetwp_sort_options', array( 'Objectiv\Site\Utilities\FacetSortOptions', 'options' ), 10, 2 );
		add_filter( 'pre_get_posts', array( 'Objectiv\Site\Utilities\FacetSortOptions', 'catalog_pre_get_posts' ) );

		// Cart button fragment refresh.
		add_filter( 'woocommerce_add_to_cart_fragments', array( 'Objectiv\Site\Utilities\CartButtonFragment', 'refresh' ), 10, 1 );

		// Cart menu count fragment refresh.
		add_filter( 'woocommerce_add_to_cart_fragments', array( 'Objectiv\Site\Utilities\CartMenuCountFragment', 'refresh' ), 10, 1 );

		// Move variation data.
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
		add_action( 'woocommerce_before_variations_form', 'woocommerce_single_variation', 10 );

		// Add cart open parameter to add to cart forms
		add_filter( 'woocommerce_add_to_cart_form_action', array( $this, 'add_open_cart_parameter_to_add_to_cart_url' ), 10, 1 );

		add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'custom_add_to_cart_redirect' ) );

		// Show variation prices.
		add_filter(
			'woocommerce_show_variation_price',
			function() {
				return true;
			}
		);

		// Single post filters.
		add_action( 'wp', array( $this, 'single_post_filters' ) );

		// Similar products filters.
		add_filter( 'woocommerce_get_related_product_cat_terms', array( $this, 'filter_related_product_categories' ), 10, 1 );

		// Don't shuffle related products.
		add_filter( 'woocommerce_product_related_posts_shuffle', '__return_false' );

		// Enrich variation data with hover image.
		add_filter( 'woocommerce_available_variation', array( $this, 'add_hover_to_variation_data' ), 10, 3 );
		add_filter( 'woocommerce_available_variation', array( $this, 'add_alternate_add_to_cart_label_to_variation_date' ), 10, 3 );

		add_filter( 'body_class', array( $this, 'add_product_slug_to_body_class' ) );

		// Remove Products breadcrumb
		add_filter( 'wpseo_breadcrumb_single_link', array( $this, 'remove_products_breadcrumb_link' ), 10, 2 );

		// Add max length to gift note field
		add_filter( 'woocommerce_checkout_fields', array( $this, 'limit_gift_note_field' ), 1001, 1 );

		// Filter out irrelevant swatch options
		add_filter( 'gform_pre_render_37', array( $this, 'remove_colors_from_leather_swatch_form' ), 10, 1 );

		// Send swatch request to leather site
		add_filter( 'gform_after_submission_37', array( $this, 'send_swatch_request_to_leather_site' ), 10, 1 );
		add_filter( 'gform_after_submission_' . mg_get_leater_swatch_form_id(), array( $this, 'send_swatch_request_to_leather_site' ), 10, 1 );

		// Hide Gift Wrapping Box from thank you page
		add_filter(
			'cfw_include_order_item_meta',
			function( $display, $meta ) {
				if ( $meta->display_key == 'Gift Wrapping Box' || $meta->display_key == 'Color Family' ) {
					$display = false;
				}

				return $display;
			},
			10,
			2
		);

		// Show Gift Wrapping as 'Includes Gift Wrapping'
		add_filter(
			'cfw_order_item_meta_output',
			function( $output ) {
				$output = str_replace( 'Gift Wrapping: true', 'Includes Gift Wrapping', $output );

				return $output;
			},
			10,
			1
		);

		// Hide out of stock variations on furniture products
		add_filter( 'woocommerce_available_variation', array( $this, 'hide_out_of_stock_furniture_variations' ), 101, 3 );

		// Hide color family in checkout
		add_filter(
			'woocommerce_get_item_data',
			function ( $item_data ) {
				foreach ( $item_data as $key => $item ) {
					if ( $item['key'] == 'Color Family' ) {
						unset( $item_data[ $key ] );
					}
				}

				return $item_data;
			}
		);

		// Cart Ajax Fragment
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'update_cart_contents' ), 11, 1 );

		// Last Chance cart line item.
		add_filter( 'woocommerce_add_cart_item_data', array( 'Objectiv\Site\Utilities\CartLineItemData', 'last_chance' ), 10, 3 );
		add_filter( 'woocommerce_get_item_data', array( 'Objectiv\Site\Utilities\CartLineItemData', 'show_last_chance_in_cart' ), 10, 2 );
		add_filter( 'woocommerce_display_item_meta', array( 'Objectiv\Site\Utilities\CartLineItemData', 'show_last_chance_in_cart_thank_you' ), 10, 2 );

		add_action( 'init', array( $this, 'mg_fix_gift_card_length' ) );

		add_filter( 'woocommerce_rest_prepare_shop_order_object', array( 'Objectiv\Site\Utilities\GiftCardOnOrderAPI', 'add_info' ), 10, 2 );

		/**
		 * Prevent PO box shipping
		 */
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'deny_pobox_postcode' ) );

		// Add order item meta.
		add_action( 'woocommerce_new_order_item', array( 'Objectiv\Site\Utilities\OrderItemMeta', 'backorder_release_date' ), 10, 3 );

		$leather_sample_form = mg_get_leater_swatch_form_id();

		// Leather Custom Template.
		add_filter( 'gform_pre_render_' . $leather_sample_form, array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'populate_sample_checkboxes' ) );
		add_filter( 'gform_pre_validation_' . $leather_sample_form, array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'populate_sample_checkboxes' ) );
		add_filter( 'gform_admin_pre_render_' . $leather_sample_form, array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'populate_sample_checkboxes' ) );
		add_filter( 'gform_pre_submission_filter_' . $leather_sample_form, array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'populate_sample_checkboxes' ) );

		add_filter( 'gform_field_content_' . $leather_sample_form, array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'swatch_fields' ), 10, 2 );
		add_filter( 'gform_submit_button_' . $leather_sample_form, array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'render_footer' ), 10, 2 );

		// Validate product added to cart is actually a valid product
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 100, 5 );
	}

	function deny_pobox_postcode( $posted ) {
		$address  = ( isset( $posted['shipping_address_1'] ) ) ? $posted['shipping_address_1'] : $posted['billing_address_1'];
		$postcode = ( isset( $posted['shipping_postcode'] ) ) ? $posted['shipping_postcode'] : $posted['billing_postcode'];

		$replace  = array( ' ', '.', ',' );
		$address  = strtolower( str_replace( $replace, '', $address ) );
		$postcode = strtolower( str_replace( $replace, '', $postcode ) );

		if ( strstr( $address, 'pobox' ) || strstr( $postcode, 'pobox' ) ) {
			wc_add_notice( sprintf( __( 'Sorry, we cannot ship to PO BOX addresses.' ) ), 'error' );
		}
	}

	function mg_fix_gift_card_length() {
		if ( ! empty( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );

			if ( ! empty( $post_data['wc_gc_cart_code'] ) && strlen( $post_data['wc_gc_cart_code'] ) < 19 ) {
				$post_data['wc_gc_cart_code'] = str_pad( $post_data['wc_gc_cart_code'], 19, '0', STR_PAD_LEFT );

				$_POST['post_data'] = http_build_query( $post_data );
			}
		}
	}

	/**
	 * Load Actions
	 */
	public function load_actions() {
		// Product Summary Actions.
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 5 );
		add_action( 'woocommerce_single_product_summary', array( 'Objectiv\Site\Utilities\ProductNewTag', 'render' ), 6 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 7 );
		add_action( 'woocommerce_single_product_summary', array( 'Objectiv\Site\Utilities\ColorLastName', 'render' ), 8 );
		add_action( 'woocommerce_single_product_summary', array( 'Objectiv\Site\Utilities\BundleProductPrice', 'render' ), 9 );

		// Widgets.
		add_action( 'widgets_init', array( 'Objectiv\Site\Widgets\ProductCategoryLinks', 'register' ) );
		add_action( 'widgets_init', array( 'Objectiv\Site\Widgets\CustomTitle', 'register' ) );

		// Products Review Load More.
		add_action( 'wp_ajax_comments_load_more_response', array( 'Objectiv\Site\Utilities\CommentsLoadMore', 'comments_load_more_response' ) );
		add_action( 'wp_ajax_nopriv_comments_load_more_response', array( 'Objectiv\Site\Utilities\CommentsLoadMore', 'comments_load_more_response' ) );

		// Modal Plugin.
		add_action( 'jckqv-after-addtocart', array( $this, 'add_view_product_link_to_quick_view' ) );
		add_action( 'jckqv-before-title', array( 'Objectiv\Site\Utilities\ProductNewTag', 'render' ) );
		add_action( 'jckqv-after-title', array( 'Objectiv\Site\Utilities\VariantColorLabel', 'get_label' ) );

		// Cart page redirect.
		add_action( 'template_redirect', array( 'Objectiv\Site\Utilities\CartPageRedirect', 'redirect' ) );

		// Shipping Order Meta Data.
		add_action( 'woocommerce_checkout_create_order_line_item', array( 'Objectiv\Site\Utilities\OrderShippingLineItem', 'set' ), 20, 4 );

		// Add expedited shipping notice
		add_action( 'cfw_shipping_method_tab', array( $this, 'add_expedited_shipping_notice' ), 16 );
		add_action( 'cfw_payment_tab_content', array( $this, 'add_shipping_waiver_notice' ), 24 );

		// Bundle wrapper
		add_action( 'woocommerce_before_bundled_items', array( $this, 'bundle_wrap_open' ) );
		add_action( 'woocommerce_after_bundled_items', array( $this, 'bundle_wrap_close' ) );

		// Bundle quantity / button wrapper
		add_action( 'woocommerce_bundles_add_to_cart_button', array( $this, 'counter_button_wrapper_open' ), 0 );
		add_action( 'woocommerce_bundles_add_to_cart_button', array( $this, 'counter_button_wrapper_close' ), 20 );

		// Cart Ajax
		add_action( 'wp_loaded', array( $this, 'fix_ajax_cart_redirect' ) );

		// After add to cart button message.
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'after_add_to_cart_button_message' ) );

		if ( class_exists( 'WC_Affirm_Loader' ) ) {
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'show_affirm' ) );
		}

		// Special offer banner.
		add_action( 'wp_special_offer_banner', array( 'Objectiv\Site\Utilities\SpecialOfferBanner', 'render' ) );

		// Leather Custom Template.
		add_action( 'wp_ajax_leather_sample_data', array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'get_sample_data' ) );
		add_action( 'wp_ajax_nopriv_leather_sample_data', array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'get_sample_data' ) );
		add_action( 'gform_pre_submission_39', array( 'Objectiv\Site\Components\LeatherTemplate\LeatherSample', 'submbition_swatches' ) );

		// Product Leather Info.
		add_action( 'wp_ajax_product_leather_sample', array( 'Objectiv\Site\Components\Products\LeatherSamples', 'leather_sample_ajax' ) );
		add_action( 'wp_ajax_nopriv_product_leather_sample', array( 'Objectiv\Site\Components\Products\LeatherSamples', 'leather_sample_ajax' ) );

		add_filter( 'woocommerce_email_enabled_customer_completed_order', array( $this, 'maybe_suppress_order_completed_email' ), 10, 2 );
	}

	/**
	 * Template pieces that are more hassle than they are worth to just manipulate in place
	 */
	public function remove_actions() {
		if ( class_exists( 'WC_Affirm_Loader' ) ) {
			remove_action( 'woocommerce_single_product_summary', array( $GLOBALS['wc_affirm_loader'], 'woocommerceSingleProductSummary' ), 15 );
			remove_action( 'mg_single_product_summary', array( $GLOBALS['wc_affirm_loader'], 'woocommerceSingleProductSummary' ), 15 );
		}
		// Remove flash sale template from single product and product archive.
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

		// Comments Actions.
		remove_action( 'woocommerce_review_before', 'woocommerce_review_display_gravatar', 10 );

		// Product Summary Actions.
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	}

	public function remove_filters() {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	}

	public function load_email_actions() {
		new Emails();
	}

	function add_view_product_link_to_quick_view() {
		$view_product = ( function_exists( 'get_field' ) ) ? obj_get_acf_field( 'woo_modal_view_product_link', 'option' ) : '';
		echo '<a href="' . esc_url( get_post_permalink( get_the_ID() ) ) . '" class="quickview-modal__more">' . $view_product . '</a>';
	}

	/**
	 * Single Product Meta Context.
	 *
	 * @param array $context The Timber context.
	 *
	 * @return mixed
	 */
	public function single_product_context( $context ) {
		// Product.
		/** \WC_Product */
		$product_id         = $context['post']->ID;
		$context['product'] = wc_get_product( $product_id );
		$is_simple_product  = $context['product']->is_type( 'simple' ) ? true : false;

		// Tootips meta array.
		$tooltips = array(
			'shipping'     => array(
				'label' => ! empty( get_post_meta( $product_id, 'tips_shipping_label_tooltip', true ) ) ? get_post_meta( $product_id, 'tips_shipping_label_tooltip', true ) : obj_get_acf_field( 'woo_help_shipping_label', 'option' ),
				'copy'  => ! empty( get_post_meta( $product_id, 'tips_shipping_tooltip', true ) ) ? get_post_meta( $product_id, 'tips_shipping_tooltip', true ) : obj_get_acf_field( 'woo_help_shipping_tooltip', 'option' ),
			),
			'product_care' => array(
				'label' => ! empty( get_post_meta( $product_id, 'tips_product_care_label_tooltip', true ) ) ? get_post_meta( $product_id, 'tips_product_care_label_tooltip', true ) : obj_get_acf_field( 'woo_help_product_care_label', 'option' ),
				'copy'  => ! empty( get_post_meta( $product_id, 'tips_product_care_tooltip', true ) ) ? get_post_meta( $product_id, 'tips_product_care_tooltip', true ) : obj_get_acf_field( 'woo_help_product_care_tooltip', 'option' ),
			),
			'help'         => array(
				'label' => ! empty( get_post_meta( $product_id, 'tips_help_label_tooltip', true ) ) ? get_post_meta( $product_id, 'tips_help_label_tooltip', true ) : obj_get_acf_field( 'woo_help_help_label', 'option' ),
				'copy'  => ! empty( get_post_meta( $product_id, 'tips_help_tooltip', true ) ) ? get_post_meta( $product_id, 'tips_help_tooltip', true ) : obj_get_acf_field( 'woo_help_help_tooltip', 'option' ),
			),
		);

		$context['acf']['product'] = array(
			// Information Banner.
			'information_banner'         => obj_get_acf_field( 'woo_product_information_banner', get_the_ID() ),
			// Feature List.
			'features'                   => obj_get_acf_field( 'woo_product_features_list', get_the_ID(), true, true ),
			// FAQ.
			'faq'                        => obj_get_acf_field( 'woo_product_frequently_asked_questions', get_the_ID(), true, true ),
			// Tri Gallery.
			'enable_tri_gallery'         => obj_get_acf_field( 'woo_enable_tri_gallery', get_the_ID() ),
			'tri_gallery_images'         => obj_get_acf_field( 'woo_product_tri_gallery', get_the_ID(), true, true ),
			// 50/50 Image.
			'enable_5050_image'          => obj_get_acf_field( 'woo_enable_5050_image', get_the_ID() ),
			'fifty_fifty_image'          => obj_get_acf_field( 'woo_5050_image', get_the_ID(), true, true ),
			// Hashtag Gallery.
			'enable_hashtag_gallery'     => obj_get_acf_field( 'woo_enable_hashtag_gallery', get_the_ID() ),
			'hashtag_gallery'            => obj_get_acf_field( 'woo_proudct_hashtag_gallery', get_the_ID() ),
			// 50/50 About.
			'enable_5050_highlight'      => obj_get_acf_field( 'woo_enable_5050_highlight', get_the_ID() ),
			'about_highlight'            => obj_get_acf_field( 'woo_product_fifty_fifty_about_highlight', get_the_ID(), true, true ),
			// About Leather.
			'enable_about_leather'       => $is_simple_product ? false : obj_get_acf_field( 'woo_enable_about_the_leather', get_the_ID() ),
			'about_leather_image'        => obj_get_acf_field( 'woo_leather_image', get_the_ID() ),
			// Care and Cleaning.
			'enable_care_cleaning'       => obj_get_acf_field( 'woo_enable_care_and_cleaning', get_the_ID() ),
			'care_cleaning_title'        => obj_get_acf_field( 'woo_care_and_cleaning_title', get_the_ID() ),
			'care_cleaning_blurb'        => obj_get_acf_field( 'woo_care_and_cleaning_blurb', get_the_ID() ),
			// 50/50 Text.
			'enable_5050_text'           => obj_get_acf_field( 'enable_fifty_fifty_text', get_the_ID() ),
			'fifty_fifty_text'           => obj_get_acf_field( 'woo_product_fifty_fifty_text', get_the_ID(), true, true ),
			// Product Associations.
			'enable_product_assoc'       => obj_get_acf_field( 'woo_enable_similar_pairs_well_with', get_the_ID() ),
			'other_products'             => obj_get_acf_field( 'woo_product_other_product_associations', get_the_ID() ),
			'border'                     => obj_get_acf_field( 'woo_product_other_product_associations_border', get_the_ID() ),
			// Retail Dimensions
			'enable_customer_dimensions' => obj_get_acf_field( 'woo_enable_dimensions', get_the_ID() ),
			'retail_dimensions'          => obj_get_acf_field( 'retail_dimensions', get_the_ID() ),
			'furniture_template'         => obj_get_acf_field( 'mg_furniture_template', get_the_ID() ),
		);

		// Adds leather to the context if there is a match.
		$leather_info = false;
		$colors       = get_the_terms( get_the_ID(), 'pa_color' );

		if ( is_array( $colors ) && ! empty( $colors ) ) {
			// Get all leather info posts.
			$leather_info_pages = get_posts(
				array(
					'post_type'      => 'leather_information',
					'posts_per_page' => -1,
				)
			);

			// Find a matching leather info post.
			foreach ( $leather_info_pages as $lip ) {
				foreach ( $colors as $color ) {
					$color_name = $color->name;

					if ( stripos( $color_name, $lip->post_title ) !== false ) {
						$leather_image                              = get_term_meta( $color->term_id, 'product_attribute_image', true );
						$leather_info[ $color->term_id ]['color']   = $color;
						$leather_info[ $color->term_id ]['leather'] = $lip;
						$leather_info[ $color->term_id ]['image']   = $leather_image;
					}
				}
			}
		}

		$context['leather_info'] = $leather_info;

		$context['acf']['product']['tooltips'] = $tooltips;

		return $context;
	}

	/**
	 * @param $context
	 *
	 * @return mixed
	 */
	public function archive_context( $context ) {
		$context['products']            = isset( $context['posts'] ) ? $context['posts'] : array();
		$context['category']            = isset( $context['product_category'] ) ? $context['product_category'] : '';
		$context['is_product_category'] = isset( $context['is_product_category'] ) ? $context['is_product_category'] : false;
		$context['is_search']           = isset( $context['is_search'] ) ? $context['is_search'] : false;
		$context['search_value']        = isset( $context['search_value'] ) ? $context['search_value'] : '';
		$context['showthumb']           = true;
		$context['acf']['shop']         = array(
			'descriptors' => obj_get_acf_field( 'woo_process_descriptors', 'option', true, true ),
			'fifty_fifty' => obj_get_acf_field( 'woo_shop_fifty_fifty', 'option', true, true ),
			'color'       => obj_get_acf_field( 'woo_product_leather_color', get_the_ID() ),
		);

		foreach ( $context['products'] as $key => $product ) {

			// Filter which images are shown based on which facets are selected
			if ( isset( $_GET['fwp_color_family'] ) && $product->is_type( 'variable' ) ) {
				$color_family = $_GET['fwp_color_family'];

				$variations = $product->get_available_variations();

				foreach ( $variations as $variation ) {
					if ( isset( $variation['attributes']['attribute_pa_color-family'] ) && $variation['attributes']['attribute_pa_color-family'] == $color_family ) {
						/** @var \WC_Product_Variation $variation */
						$variation                                  = wc_get_product( $variation['variation_id'] );
						$variation_additional_images                = get_post_meta( $variation->get_id(), 'variation_image_gallery', true );
						$variation_additional_images                = explode( ',', $variation_additional_images );
						$product->custom['replacement_cover_image'] = get_timber_image_src( new \Timber\Image( $variation->get_image_id() ), 'thumbnail' );
						$product->custom['replacement_hover_image'] = get_timber_image_src( new \Timber\Image( current( $variation_additional_images ) ), 'thumbnail' );
					}
				}
			}
		}

		return $context;
	}

	/**
	 * @param $context
	 *
	 * @return mixed
	 */
	public function category_context( $context ) {
		$queried_object      = get_queried_object();
		$term_id             = $queried_object->term_id;
		$context['category'] = get_term( $term_id, 'product_cat' );
		$context['title']    = single_term_title( '', false );

		return $context;
	}

	/**
	 * @param $context
	 *
	 * @return mixed
	 */
	public function after_template_context( $context ) {
		$context['sidebar'] = Timber::get_widgets( 'woo-sidebar' );

		return $context;
	}

	function single_post_filters() {
		if ( is_product() ) {
			add_filter( 'wvs_pro_show_archive_variation_template', '__return_false' );
		}
	}

	function filter_related_product_categories( $terms ) {
		$gift_card_term_id = false;

		// Filter out gift guide
		foreach ( $terms as $index => $term_id ) {
			$term = get_term( $term_id );

			if ( $term->name == 'Gift Guide' ) {
				$gift_card_term_id = $term_id;
				unset( $terms[ $index ] );
			}
		}

		// Also filter out child categories of gift guide
		if ( $gift_card_term_id !== false ) {
			foreach ( $terms as $index => $term_id ) {
				$term = get_term( $term_id );

				if ( $term->parent == $gift_card_term_id ) {
					unset( $terms[ $index ] );
				}
			}
		}

		return $terms;
	}

	/**
	 * @param array                $data
	 * @param $object
	 * @param \WC_Product_Variable $variation
	 *
	 * @return array
	 */
	function add_hover_to_variation_data( $data, $object, $variation ) {
		$additional_images = get_post_meta( $variation->get_id(), 'variation_image_gallery', true );
		$additional_images = explode( ',', $additional_images );

		if ( ! empty( $additional_images ) ) {
			$data['hover_image'] = wc_get_product_attachment_props( $additional_images[0] );
		}

		return $data;
	}

	/**
	 * @param array                $data
	 * @param $object
	 * @param \WC_Product_Variable $variation
	 *
	 * @return array
	 */
	function add_alternate_add_to_cart_label_to_variation_date( $data, $object, $variation ) {
		$data['add_to_cart_text'] = 'Add to Cart';

		if ( $variation->is_on_backorder() ) {
			$data['add_to_cart_text'] = 'Pre-Order';
		}

		return $data;
	}

	function add_open_cart_parameter_to_add_to_cart_url( $url ) {
		$url = add_query_arg(
			array(
				'open-cart' => 'true',
			),
			$url
		);

		return $url;
	}

	/**
	 * Redirect to current page after add to cart.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	function custom_add_to_cart_redirect( $url ) {
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			return $url;
		}

		if ( is_product() ) {
			return $url;
		}

		if ( isset( $_SERVER[ 'HTTP_REFERER' ] ) ) { // phpcs:ignore
			return $this->add_open_cart_parameter_to_add_to_cart_url( $_SERVER[ 'HTTP_REFERER' ] ); // phpcs:ignore
		}
	}

	function add_product_slug_to_body_class( $classes ) {
		global $product;

		if ( is_product() ) {
			$classes[] = 'product-' . $product;
		}

		return $classes;
	}

	/**
	 * @param string      $label
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	function change_add_to_cart_label_for_backorders( $label, $product ) {
		if ( $product->is_type( 'simple' ) && $product->is_on_backorder() ) {
			$label = 'Pre-Order';
		}

		return $label;
	}

	/**
	 * @param $link_output
	 * @param $link
	 *
	 * @return string
	 */
	function remove_products_breadcrumb_link( $link_output, $link ) {
		$text_to_remove = 'Products';
		if ( $link['text'] == $text_to_remove ) {
			$link_output = '';
		}
		return $link_output;
	}

	function limit_gift_note_field( $fields ) {
		if ( ! empty( $fields['order'] ) && ! empty( $fields['order']['gift_note'] ) ) {
			$fields['order']['gift_note']['maxlength'] = 100;
		}

		return $fields;
	}

	function add_expedited_shipping_notice() {
		echo '<p><strong>Expedited Orders:</strong> Overnight and next day orders placed before 11AM EST on business days will ship same day. Orders placed after 11AM will ship the next business day.</p>';
	}

	function add_shipping_waiver_notice() {
		echo '<p style="margin-bottom: 0.5em;"><small><strong>Signature:</strong> Waiving your signature requirement voids the responsibility of Moore &amp; Giles to replace lost or stolen packages.</small></p>';
	}

	function remove_colors_from_leather_swatch_form( $form ) {
		global $product;

		if ( empty( $product ) || is_string( $product ) ) {
			return $form;
		}

		$colors         = explode( ', ', $product->get_attribute( 'color' ) );
		$leathers_field = $this->get_gf_field_by_admin_label( 'Leather Samples Requested', $form );
		$field          = $leathers_field['field'];
		$field_key      = $leathers_field['key'];
		$new_choices    = array();
		$new_inputs     = array();

		foreach ( $field['choices'] as $key => $choice ) {
			if ( in_array( $choice['text'], $colors ) ) {
				$new_choices[ $key ] = $choice;
			}
		}

		foreach ( $field['inputs'] as $key => $input ) {
			if ( ! in_array( $input['label'], $colors ) ) {
				$new_inputs[ $key ] = $input;
			}
		}

		$field['choices']             = $new_choices;
		$field['inputs']              = $new_inputs;
		$form['fields'][ $field_key ] = $field;

		return $form;
	}

	function get_gf_field_by_admin_label( $admin_label, $form ) {
		$filtered  = array_filter(
			$form['fields'],
			function( $field ) use ( $admin_label ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
				return $admin_label === $field->adminLabel;
			}
		);
		$field_key = key( $filtered );
		$field     = array_shift( $filtered );
		return array(
			'key'   => $field_key,
			'field' => $field,
		);
	}

	function send_swatch_request_to_leather_site( $entry ) {
		$secret          = 'Eao2HnLbFDAZtJwHbKG82qarqns2OOhty849GnDNpApFvqZmevc9wXnUxkZyud01';
		$use_ssl_verify  = defined( 'WP_LOCAL_DEV' ) && ! WP_LOCAL_DEV;
		$is_leather_form = '37' === $entry['form_id'] || (int) mg_get_leater_swatch_form_id() === (int) $entry['form_id'];

		if ( $is_leather_form ) { // Bag swatch request form
			$first_name             = $entry['9.3'];
			$last_name              = $entry['9.6'];
			$email                  = $entry['2'];
			$phone                  = $entry['10'];
			$address_line_1         = $entry['3.1'];
			$address_line_2         = $entry['3.2'];
			$address_city           = $entry['3.3'];
			$address_state          = $entry['3.4'];
			$address_zip            = $entry['3.5'];
			$address_country        = 'US'; // $entry['3.6']; currently only domestic orders
			$product_requested_from = $entry['11'];

			$leather_field_number = $is_leather_form ? '6' : '7';

			$is_a_selected_leather = function( $leather_name, $field_key ) use ( $leather_field_number ) {
				$first_char = substr( $field_key, 0, 1 );
				return $leather_field_number === $first_char && '' !== $leather_name;
			};

			$leathers_requested = array_filter( $entry, $is_a_selected_leather, ARRAY_FILTER_USE_BOTH );

			$response = wp_remote_post(
				$this->get_swatch_request_endpoint(),
				array(
					'sslverify' => $use_ssl_verify,
					'headers'   => array( 'secret' => $secret ),
					'body'      => array(
						'leathers'               => $leathers_requested,
						'first_name'             => $first_name,
						'last_name'              => $last_name,
						'email'                  => $email,
						'phone'                  => $phone,
						'address_line_1'         => $address_line_1,
						'address_line_2'         => $address_line_2,
						'address_city'           => $address_city,
						'address_state'          => $address_state,
						'address_zip'            => $address_zip,
						'address_country'        => $address_country,
						'product_requested_from' => $product_requested_from,
					),
				)
			);

			$foo = 'bar';
		}
	}

	function get_swatch_request_endpoint() {
		if ( defined( 'WP_LOCAL_DEV' ) ) {
			if ( WP_LOCAL_DEV ) {
				if ( defined( 'MG_LEATHER_LOCAL_DEV_DOMAIN' ) ) {
					$leather_domain = MG_LEATHER_LOCAL_DEV_DOMAIN;
				} else {
					return false;
				}
			} else {
				$leather_domain = 'https://www.mooreandgiles.com/leather/';
			}
		} else {
			return false;
		}

		return $leather_domain . 'wp-json/mgshopprest/v1/mgshopprest';
	}

	function bundle_wrap_open() {
		echo '<div class="bundle-images-wrap">';
	}

	function bundle_wrap_close() {
		echo '</div>';
	}

	function counter_button_wrapper_open() {
		echo '<div class="counter-button-wrapper">';
	}

	function counter_button_wrapper_close() {
		echo '</div>';
	}

	/**
	 * @param $variation
	 * @param \WC_Product           $productObject
	 * @param \WC_Product_Variation $variationObject
	 *
	 * @return bool
	 */
	function hide_out_of_stock_furniture_variations( $variation, $productObject, $variationObject ) {
		if ( has_term( 'Shop Home', 'product_cat', $productObject->get_id() ) ) {
			return $variationObject->is_in_stock() ? $variation : false;
		}

		return $variation;
	}

	/**
	 * When the WooCommerce cart is fired from a product page
	 * It tries to redirect to the product page which nukes the
	 * cart because the cart contents are not on the product page
	 * on page load.
	 *
	 * FML.
	 */
	function fix_ajax_cart_redirect() {
		if ( ! ( isset( $_REQUEST['apply_coupon'] ) || isset( $_REQUEST['remove_coupon'] ) || isset( $_REQUEST['remove_item'] ) || isset( $_REQUEST['undo_item'] ) || isset( $_REQUEST['update_cart'] ) || isset( $_REQUEST['proceed'] ) ) ) {
			return;
		}

		$_SERVER['HTTP_REFERER'] = false;
	}

	/**
	 * @param array $fragments
	 *
	 * @return array
	 */
	function update_cart_contents( $fragments ) {
		$fragments['#mg_cart_wrap'] = do_shortcode( '[woocommerce_cart]' );

		return $fragments;
	}

	function show_affirm() {
		call_user_func( array( $GLOBALS['wc_affirm_loader'], 'woocommerceSingleProductSummary' ) );
	}

	/**
	 * After Add to Cart Button Message
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function after_add_to_cart_button_message() {
		$options = obj_get_acf_field( 'mg_after_add_to_cart_button_message', 'options' );
		$global  = ! empty( $options['mg_add_to_cart_button_message'] ) ? wp_strip_all_tags( $options['mg_add_to_cart_button_message'] ) : '';
		$product = obj_get_acf_field( 'mg_add_to_cart_button_message', get_the_ID() );
		$message = $product ? $product : $global;

		// Maybe this will be used in the future.
		$is_category = is_product_category( 'last_chance' );

		if ( ! empty( $message ) ) {
			echo wp_kses_post( '<p class="below-button-message">' . $message . '</p>' );
		}
	}

	/**
	 * @param bool $send_email
	 * @param \WC_Abstract_Order $order
	 *
	 * @return bool
	 */
	function maybe_suppress_order_completed_email( $send_email, $order ) {
		// get order items = each product in the order
		$items = $order->get_items();

		// Set variable
		$only_has_gift_card = true;

		foreach ( $items as $item ) {
			// Get product id
			$product = wc_get_product( $item['product_id'] );

			if ( ! class_exists( '\\WC_GC_Gift_Card_Product' ) || ! \WC_GC_Gift_Card_Product::is_gift_card( $product ) ) {
				$only_has_gift_card = false;
			}
		}

		if ( $only_has_gift_card ) {
			$send_email = false;
		}

		return $send_email;
	}

	/**
	 * @param bool $valid
	 * @param $item
	 * @param $quantity
	 * @param $variation_id
	 * @param $variations
	 *
	 * @return bool
	 */
	function validate_add_to_cart( bool $valid, $item, $quantity, $variation_id = false, $variations = false ): bool {
		if ( ! empty( $variations ) && empty( $variation_id ) ) {
			$valid = false;
		}

		return $valid;
	}
}
