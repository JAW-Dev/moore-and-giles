<?php
/*
Plugin Name: Core Functionality
Plugin URI: http://www.mooreandgiles.com
Description:  Core Functionality for MG.
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2009-2014 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'MG_CF_PATH', dirname( __FILE__ ) );
define( 'MG_CF_URL', plugins_url( '/', __FILE__ ) );
define( 'MG_CF_VERSION', '1.0.2' );

class MG_CoreFunctionality {
	var $blog_id = null;

	function __construct() {
		global $blog_id;

		$this->blog_id = $blog_id;

		add_action( 'shopp_init', array( $this, 'register_shopp_taxonomies_for_blog_' . $this->blog_id ) );
		add_action( 'import_start', array( $this, 'disable_kses' ) );

		add_action( 'wp_update_nav_menu', array( $this, 'save_global_footer' ), 100 );
		add_filter( 'widget_update_callback', array( $this, 'save_global_footer' ), 100, 1 );

		add_action( 'p2p_init', array( $this, 'p2p_connections' ) );

		add_filter( 'optin_monster_pre_optin_mailchimp', array( $this, 'set_mailchimp_segment' ) );

		// Alter home breadcrumb to always be main site
		add_filter( 'genesis_build_crumbs', array( $this, 'alter_genesis_home_crumb' ), 10, 2 );

		// Mark as Paid
		add_action( 'shopp_order_admin_script', array( $this, 'add_mark_as_paid' ) );
		add_action( 'admin_init', array( $this, 'process_mark_as_paid' ) );

		// Add product metaboxes
		add_action( 'cmb2_meta_boxes', array( $this, 'product_metaboxes' ) );
		add_action( 'cmb2_meta_boxes', array( $this, 'product_detail_accordion_metaboxes' ) );

		// Pulling in CMB2 JS/CSS Files for Product Pages
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );

		// Allow for the ability to create single templates off of ID
		add_filter( 'single_template', array( $this, 'single_post_template' ) );

		// Allow single product templates based on ID and slug
		add_filter( 'shopp_shopp-product_storefront_page_templates', array( $this, 'single_product_page_templates' ) );

		// Add data as meta
		add_action( 'shopp_order_success', array( $this, 'convert_data_to_meta' ) );
		add_action( 'shopp_order_success', array( $this, 'convert_discounts_to_meta' ) );
		add_action( 'shopp_order_success', array( $this, 'track_additional_order_data' ) );

		// Holiday 2018 Shipping Override
		//add_action( 'shopp_order_success', array($this, 'free_expedited_shipping_label') );

		// Load in modules for other functionality not in the master class
		add_action( 'init', array( $this, 'load_modules' ), 1 );

		// Custom Product Type Taxonomy
		add_action( 'shopp_init', array( $this, 'product_type_taxonomy' ) );

		// Genesis SEO Title Override
		add_filter( 'get_post_metadata', array( $this, 'genesis_seo_title' ), 100, 4 );

		// Remove Totals / Add Status
		add_filter( 'manage_toplevel_page_shopp-orders_columns', array( $this, 'alter_leather_order_columns' ), 100, 1 );
		add_action( 'shopp_manage_orders_info_column', array( $this, 'leather_order_info_column' ), 10, 2 );

		// Zopim
		add_action( 'shopp_init', array( $this, 'shopp_actions' ) );

		// Theme Tags
		add_filter( 'shopp_themeapi_cart_haspersonalization', array( $this, 'has_personalization' ), 10, 3 );

		// Instant signup endpoint
		add_action( 'wp_loaded', array( $this, 'catch_mailchimp_signup' ) );

		add_shortcode( 'adhoc-collection', array( $this, 'adhoc_collection' ) );

		// Exclude Secret Stash Products from Index
		//add_action('shopp_product_saved', array($this, 'prevent_secret_stash_index'), 1, 1);

		// Members Plugin Santization workaround
		add_filter( 'members_sanitize_role', array( $this, 'fix_shopp_roles' ), 10, 2 );

		// Zopim
		add_action( 'wp_head', array( $this, 'zopim_script' ) );

		// Restrict Dashboard Access
		add_action( 'admin_init', array( $this, 'allow_admin_area_to_admins_only' ) );

		// Decline Status
		add_action( 'shopp_auth-fail_order_event', array( $this, 'update_decline_status' ), 10, 1 );

		if ( is_main_site() ) {
			// Special Session Handling Overrides
			add_filter(
				'shopp_session_cook', function( $cook ) {
					// Only cook a new session if we're on Shopp checkout page, or we're doing AJAX stuffs
				if ( stripos( $_SERVER['REQUEST_URI'], '/shop/cart' ) === false && stripos( $_SERVER['REQUEST_URI'], '/shop/checkout' ) === false && stripos( $_SERVER['REQUEST_URI'], '/shop/account' ) === false && ! wp_doing_ajax() ) { //phpcs:ignore
						$cook = false; // only cook if we are in an AJAX request
					}

					return $cook;
				}, 10, 1
			);

			add_action( 'wp_footer', array( $this, 'facebook_conversion_tracking' ) );

			// Modify bag sort options
			add_filter( 'shopp_category_sortoptions', array( $this, 'modify_bag_sort_options' ) );

			// Google Shopping Fixes
			add_filter( 'shopp_rss_item', array( $this, 'cgd_google_merchant_feed_fixes' ), 10, 2 );

			// New Sales Report
			add_filter( 'shopp_reports', array( $this, 'add_reports' ) );

			// Always allow duplicate email addresses
			add_filter( 'shopp_email_exists', '__return_false' );

			add_filter(
				'shopp_timeframes_menu', function( $_ ) {
					$units = array(
						'd' => 11,
						'w' => 20,
						'm' => 12,
					);
					$_     = array();
					$min   = 0;

					foreach ( $units as $u => $count ) {
						for ( $i = $min; $i < $count; $i++ ) {
							switch ( $u ) {
								case 'd':
									$_[ $i . $u ] = sprintf( _n( '%d day', '%d days', $i, 'Shopp' ), $i );
									break;
								case 'w':
									$_[ $i . $u ] = sprintf( _n( '%d week', '%d weeks', $i, 'Shopp' ), $i );
									break;
								case 'm':
									$_[ $i . $u ] = sprintf( _n( '%d month', '%d months', $i, 'Shopp' ), $i );
									break;
								break;
							}
						}
						$min = ( 0 === $min ) ? ++$min : $min; // Increase the min number of units to one after the first loop (allow 0 days but not 0 weeks)
					}

					return $_;
				}
			);

			// Authorize.net auto-charge orders.
			add_action( 'shopp_init', array( $this, 'authorizedotnet_autocharge' ), 99 );

			// Variant widget out of stock label
			add_filter( 'shopp_themeapi_product_variation', array( $this, 'variant_widget_out_of_stock_label' ), 10, 3 );

			// Gift Note Prefix.
			add_action( 'shopp_order_success', array( $this, 'set_gift_note_prefix' ) );

			// Black Friday 2019
			add_action( 'shopp_order_success', array( $this, 'maybe_generate_promo' ), 100, 3 );
			add_action( 'shopp_order_success', array( $this, 'maybe_invalidate_promo' ), 100, 3 );
			add_filter( 'shopp_discount_cart_item_matches', array( $this, 'limit_cart_item_discount_applications' ), 10, 3 );
			add_filter( 'shopp_discount_item_amount', array( $this, 'limit_cart_item_discount_amount' ), 10, 3 );
			add_action( 'wp', array( $this, 'maybe_apply_discounts_from_url' ), 100 );
			add_action( 'wp', array( $this, 'maybe_redirect_after_adding_discount_from_url' ), 1000 );
		}
	}

	function load_modules() {
		/**
		 * Bag Site Only Modules
		 */
		if ( is_main_site() ) {
			// Checkout Error Message Filters
			include 'inc/checkout-error-filters.php';
			$MG_CheckoutErrorFilters = new MG_CheckoutErrorFilters();

			// Storefront Feed
			include 'inc/storefront-feed.php';
			$MG_Storefront_Feed = new MG_Storefront_Feed();

			// Checkout Validation Overrides
			include 'inc/checkout-validation.php';
			$MG_CheckoutValidation = new MG_CheckoutValidation();

			// Leather Info Post Type for Bag Accordion
			include 'inc/leather-info.php';
			$MG_LeatherInformation = new MG_LeatherInformation();

			// Product Bundles
			include 'inc/class-product-bundles.php';
			$MG_ProductBundles = new MG_ProductBundles();

			// Impersonation
			include 'inc/class-impersonation.php';
			$MG_Impersonation = new MG_Impersonation();

			// EchoPost
			//          include('inc/class-echopost.php');
			//          $MG_EchoPost = new MG_EchoPost();

			// Gift Wrapping
			include 'inc/class-gift-wrapping.php';
			global $MG_GiftWrapping;
			$MG_GiftWrapping = new MG_GiftWrapping();

			// MG VIP
			include 'inc/class-vip-program.php';
			$MG_VIP_Program = new MG_VIP_Program();

			// MG View All Colors Link
			include 'inc/class-mg-view-colors.php';
			$MG_ViewColors = new MG_ViewColors();

			// MG Virtual Variants
			include 'inc/class-mg-virtual-variants.php';
			$MG_Virtual_Variants = new MG_Virtual_Variants();

			// Preorder / Backorder Delayed Items Email
			include 'inc/class-preorder-notices.php';
			$MG_PreorderNotices = new MG_PreorderNotices();

			// Disable New User Notifications
			include 'inc/class-disable-new-user-notices.php';
			$MG_DisableNewUserNotices = new MG_DisableNewUserNotices();

			// Category Sync for new Site
			include 'inc/class-product-sync-categories.php';
			$MG_ProductSyncCategories = new MG_ProductSyncCategories();
		} /**
		 * Leather Site Only Modules
		 */
		elseif ( mg_is_leather_site() ) {
			include 'inc/class-variation-categories.php';
			$MG_VariationCategories = new MG_VariationCategories();

			// Hide Type Taxonomies
			include 'inc/class-hide-types.php';
			$MG_HideTypes = new MG_HideTypes();

			// Sort Purchased
			include 'inc/class-sampling-enhancements.php';
			$MG_Sampling_Enhancements = new MG_Sampling_Enhancements();

			include 'inc/limit-cart-quantity-leather.php';
			$MG_LimitCartQuantityLeather = new MG_LimitCartQuantityLeather();
		}

		/**
		 * All Sites
		 */
		include 'inc/class-customer-matching.php';
		$MG_CustomerMatching = new MG_CustomerMatching();

		// Shopp Order Admin Features
		include 'inc/class-shopp-order-admin-features.php';
		$MG_ShoppOrderAdmin_Features = new MG_ShoppOrderAdmin_Features();

		include 'inc/class-admin-customer-merge.php';
		$MG_Admin_Customer_Merge = new MG_Admin_Customer_Merge();
	}

	function register_shopp_taxonomies_for_blog_3() {
		shopp_register_taxonomy(
			'country', array(
				'hierarchical' => true,
				'labels'       => array(
					'name'                  => 'Countries',
					'singular_name'         => 'Country',
					'search_items'          => 'Search Countries',
					'popular_items'         => 'Popular Countries',
					'all_items'             => 'Show All Countries',
					'parent_item'           => 'Parent Country',
					'parent_item_colon'     => 'Parent Country:',
					'edit_item'             => 'Edit Country',
					'update_item'           => 'Update Country',
					'add_new_item'          => 'New Country',
					'new_item_name'         => 'New Country Name',
					'add_or_remove_items'   => 'Add or remove countries',
					'choose_from_most_used' => 'Choose from the most used countries',
				),
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'country' ),
			)
		);
	}

	function disable_kses() {
		if ( function_exists( 'kses_remove_filters' ) ) {
			kses_remove_filters();
		}
	}

	function save_global_footer( $result ) {
		$blog_id = get_current_blog_id();

		// if ( ! is_main_site() ) {
		// 	return $result;
		// }

		if ( $blog_id !== '3' ) {
			return $result;
		}

		if ( ! ( $mg_footer_widgets = get_site_option( 'mg_footer_widgets' ) ) || is_admin() ) {
			ob_start();

			genesis_footer_widget_areas();

			$mg_footer_widgets = ob_get_clean();

			update_site_option( 'mg_footer_widgets', $mg_footer_widgets );
		}

		return $result;
	}

	function get_global_footer() {
		$sidebar_widgets = wp_get_sidebars_widgets();

		$widget_areas = array(
			'footer-1',
			'footer-2',
			'footer-3',
			'footer-4',
		);

		$is_widgets_set = false;

		if ( ! empty( $sidebar_widgets['footer-1'] ) && ! empty( $sidebar_widgets['footer-2'] ) && ! empty( $sidebar_widgets['footer-3'] ) && ! empty( $sidebar_widgets['footer-4'] ) ) {
			$is_widgets_set = true;
		}

		if ( $is_widgets_set ) {
			genesis_footer_widget_areas();
		} else {
			return get_site_option( 'mg_footer_widgets' );
		}
	}

	function p2p_connections() {
		p2p_register_connection_type(
			array(
				'name' => 'portfolio_to_products',
				'from' => 'portfolio',
				'to'   => 'shopp_product',
			)
		);

		p2p_register_connection_type(
			array(
				'name' => 'press_to_products',
				'from' => 'press',
				'to'   => 'shopp_product',
			)
		);

		if ( mg_is_leather_site() ) {
			p2p_register_connection_type(
				array(
					'name'       => 'similar_leathers',
					'from'       => 'shopp_product',
					'to'         => 'shopp_product',
					'reciprocal' => true,
					'title'      => 'Similar Leathers',
				)
			);
		}
	}

	function set_mailchimp_segment( $data ) {

		switch ( $this->blog_id ) {
			case 3:
				$data['Source'] = 'Leather';
				break;
			case 1:
				$data['Source'] = 'Bags';
				break;
			default:
		}

		return $data;
	}

	function alter_genesis_home_crumb( $crumbs, $args ) {

		if ( ! is_main_site() ) {

			$crumbs[0] = str_replace( 'Home', get_bloginfo( 'name' ), $crumbs[0] );

			// Add new home crumb
			array_unshift( $crumbs, '<a href="' . network_site_url() . '" title="View Home">Home</a>' );
		}

		return $crumbs;
	}

	/**
	 * Adds "mark as paid" feature to Shopp Orders backend.
	 * @param $Purchase the purchase object
	 * @return void
	**/
	// TODO: Move to plugin
	function add_mark_as_paid( $Purchase ) {
		if ( current_user_can( 'mg_mark_as_paid' ) ) {

			if ( $Purchase->captured < $Purchase->total ) : ?>
				jQuery("#major-publishing-actions div.alignleft").append('<input type="submit" id="markpaid-button" name="mark-paid" value="Mark As Paid" class="button-secondary">');

				jQuery("#markpaid-button").click(function() {

					if(confirm("This will mark the order as paid, bypassing Authorize.net.  Only do this when you are going to collect payment manually for an exceptional circumstances. Please also make note of this on the order. Are you sure you want to proceed?"))
					{
						return true;
					}
					return false;
				});
			<?php endif; ?>
			<?php
		}
	}

	function process_mark_as_paid() {
		global $wpdb;

		if ( isset( $_POST['mark-paid'] ) && is_admin() && current_user_can( 'shopp_capture' ) ) {
			$Purchase = shopp_order( $_GET['id'] );
			ShoppPurchase( $Purchase );

			$current_user  = wp_get_current_user();
			$author_byline = sprintf(
				__( 'by <a href="%1$s">%2$s</a> (<a href="%3$s">%4$s</a>)', 'Shopp' ),
				"mailto:$user->user_email?subject=RE: Order #{$Purchase->id}",
				"$current_user->user_firstname $current_user->user_lastname",
				add_query_arg(
					array( 'user_id' => $current_user->ID ),
					admin_url( 'user-edit.php' )
				), $current_user->user_login
			);

			// Create Customer if Neccessary
			if ( empty( $Purchase->customer ) ) {
				$destination_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}shopp_customer WHERE email = %s ORDER BY id ASC LIMIT 1", $Purchase->email ) );

				// Do we have an existing customer?
				if ( ! empty( $destination_id ) ) {
					// We found an existing customer
					$Purchase->customer = $destination_id;
				} else {
					// Create one
					$registration = $Purchase->registration();

					$data = array(
						'wpuser'    => false,
						'firstname' => $Purchase->firstname,
						'lastname'  => $Purchase->lastname,
						'email'     => $Purchase->email,
						'phone'     => $Purchase->phone,
						'company'   => $Purchase->company,
						'marketing' => isset( $registration['Customer'] ) ? $registration['Customer']->marketing : 'no',
						'type'      => 'Guest',
						'saddress'  => $Purchase->shipaddress,
						'sxaddress' => $Purchase->shipxaddress,
						'scity'     => $Purchase->shipcity,
						'sstate'    => $Purchase->shipstate,
						'scountry'  => $Purchase->shipcountry,
						'spostcode' => $Purchase->shippostcode,
						'baddress'  => $Purchase->address,
						'bxaddress' => $Purchase->xaddress,
						'bcity'     => $Purchase->city,
						'bstate'    => $Purchase->state,
						'bcountry'  => $Purchase->country,
						'bpostcode' => $Purchase->postcode,
					);

					$Purchase->customer = shopp_add_customer( $data );
				}
			}

			// Save the changes
			$Purchase->save();

			// Add the Payment
			shopp_add_order_event(
				$Purchase->id, 'authed', array(
					'txnid'     => 'Manual payment ' . $author_byline,  // Transaction ID from payment gateway, in some cases will be in $Event->txnid
					'amount'    => $Purchase->total,                  // Gross amount captured
					'gateway'   => 'Manual Override',                 // Gateway handler name (module name from @subpackage)
					'paymethod' => 'Manual Override',
					'paytype'   => 'manual',
					'payid'     => 'manual',
					'capture'   => true,
				)
			);

			// Notifications
			ShoppPurchase()->success( ShoppPurchase() );
		}
	}

	function product_metaboxes( $meta_boxes ) {
		$prefix = '_mg_';

		$extra_product_info = new_cmb2_box(
			array(
				'id'           => 'extra_product_info',
				'title'        => __( 'Extra Product Info', 'cmb2' ),
				'object_types' => array( 'shopp_product' ), // Post type
				'priority'     => 'high',
			)
		);

		$extra_product_info->add_field(
			array(
				'id'          => $prefix . 'product_embed',
				'name'        => 'Embed',
				'type'        => 'oembed',
				'description' => __( 'Enter a YouTube, Vimeo, Twitter, or Instagram URL. Supports services listed at <a href="http://codex.wordpress.org/Embeds">http://codex.wordpress.org/Embeds</a>.', 'cmb2' ),
			)
		);

		$lookbook = new_cmb2_box(
			array(
				'id'           => 'Enable Lookbook',
				'title'        => __( 'Lookbook', 'cmb2' ),
				'object_types' => array( 'shopp_product' ), // Post type
				'context'      => 'side',
				'priority'     => 'low',
			)
		);

		$lookbook->add_field(
			array(
				'id'          => $prefix . 'product_lookbook_enable',
				'type'        => 'checkbox',
				'description' => __( 'Enable the Lookbox widget.', 'cmb2' ),
				'context'     => 'side',
			)
		);

	}

	function scripts() {
		if ( class_exists( 'CMB2_hookup' ) ) {
			CMB2_hookup::enqueue_cmb_css();
			CMB2_hookup::enqueue_cmb_js();
		}
	}

	function single_post_template( $located_template ) {
		global $post;

		$templates = array( sprintf( 'single-%d.php', $post->ID ), sprintf( 'single-%s.php', $post->post_name ), 'single-' . $post->post_type . '.php', 'single.php' );

		return locate_template( $templates );
	}

	function single_product_page_templates( $templates ) {
		global $post;

		array_unshift( $templates, sprintf( 'single-%d.php', $post->ID ) );
		array_unshift( $templates, sprintf( 'single-%s.php', $post->post_name ) );

		return $templates;
	}

	function convert_data_to_meta( $Purchase ) {
		if ( empty( $Purchase->id ) ) {
			return;
		}

		$Purchase = shopp_order( $Purchase->id );

		foreach ( $Purchase->data as $key => $value ) {
			shopp_set_meta( $Purchase->id, 'purchase', $key, $value, 'order-data' );
		}
	}

	function convert_discounts_to_meta( $Purchase ) {
		$discounts = $Purchase->discounts();

		if ( empty( $discounts ) ) {
			return;
		}

		$bulk_promos = get_option( 'mg_free_shipping_bulk_discounts' );
		$bulk_promos = explode( PHP_EOL, $bulk_promos );
		$bulk_promos = array_map( 'trim', $bulk_promos );

		foreach ( $discounts as $id => $Discount ) {

			if ( in_array( strtoupper( $Discount->code ), $bulk_promos ) ) {

				shopp_set_meta( $Purchase->id, 'purchase', 'source-code', strtoupper( $Discount->code ), 'meta' );
			}
		}
	}

	function track_additional_order_data( $Purchase ) {
		// Track User ID
		shopp_set_meta( $Purchase->id, 'purchase', 'user_id', get_current_user_id(), 'meta' );
	}

	function free_expedited_shipping_label( $Purchase ) {
		$Purchase->shipoption = str_replace( ' - Get it by Christmas! (excludes personalized items)', '', $Purchase->shipoption );
		$Purchase->shipoption = str_replace( ' (Not Guaranteed for Christmas)', '', $Purchase->shipoption );
		$Purchase->save();
	}

	function product_type_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Product Types', 'product_types' ),
			'singular_name'              => _x( 'Product Type', 'product_types' ),
			'search_items'               => _x( 'Search Product Types', 'product_types' ),
			'popular_items'              => _x( 'Popular Product Types', 'product_types' ),
			'all_items'                  => _x( 'All Product Types', 'product_types' ),
			'parent_item'                => _x( 'Parent Product Type', 'product_types' ),
			'parent_item_colon'          => _x( 'Parent Product Type:', 'product_types' ),
			'edit_item'                  => _x( 'Edit Product Type', 'product_types' ),
			'update_item'                => _x( 'Update Product Type', 'product_types' ),
			'add_new_item'               => _x( 'Add New Product Type', 'product_types' ),
			'new_item_name'              => _x( 'New Product Type', 'product_types' ),
			'separate_items_with_commas' => _x( 'Separate product types with commas', 'product_types' ),
			'add_or_remove_items'        => _x( 'Add or remove product types', 'product_types' ),
			'choose_from_most_used'      => _x( 'Choose from the most used product types', 'product_types' ),
			'menu_name'                  => _x( 'Product Types', 'product_types' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => false,
			'show_in_nav_menus' => false,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'show_admin_column' => false,
			'hierarchical'      => true,

			'rewrite'           => false,
			'query_var'         => false,
		);

		shopp_register_taxonomy( 'product_types', $args );
	}

	function genesis_seo_title( $result, $object_id, $meta_key, $single ) {

		if ( is_admin() || $meta_key != '_genesis_title' ) {
			return $result;
		}
		if ( mg_is_leather_site() ) {
			return $result;
		}
		if ( has_term( 'Furniture', 'shopp_category', $object_id ) ) {
			return $result;
		}

		// Respect Override
		remove_filter( 'get_post_metadata', array( $this, 'genesis_seo_title' ), 100, 4 );
		$override = get_post_meta( $object_id, '_genesis_title', true );
		if ( ! empty( $override ) ) {
			return $override;
		}
		add_filter( 'get_post_metadata', array( $this, 'genesis_seo_title' ), 100, 4 );

		if ( is_singular() && get_post_type() == 'shopp_product' ) {
			$first_name  = shopp( 'product.get-first-name' );
			$middle_name = shopp( 'product.get-middle-name' );
			$last_name   = shopp( 'product.get-last-name' );

			$string_prefix = 'Moore &amp; Giles';

			if ( ! empty( $middle_name ) && ! empty( $last_name ) ) {
				$result = sprintf( '%s %s Leather %s in %s', $string_prefix, $first_name, $middle_name, $last_name );
			} elseif ( ! empty( $middle_name ) && empty( $last_name ) ) {
				$result = sprintf( '%s %s Leather %s', $string_prefix, $first_name, $middle_name );
			} elseif ( empty( $middle_name ) && ! empty( $last_name ) ) {
				$result = sprintf( '%s Leather %s in %s', $string_prefix, $first_name, $last_name );
			} else { // first name only
				$result = sprintf( '%s Leather %s', $string_prefix, $first_name );
			}

			if ( stripos( $first_name, 'Leather' ) !== false || stripos( $first_name, 'Book' ) !== false ) {
				$result = sprintf( 'Moore &amp; Giles | %s', $first_name );
			}

			if ( stripos( $middle_name, 'Leather' ) !== false && ! empty( $last_name ) ) {
				$result = sprintf( '%s %s %s in %s', 'Moore &amp; Giles', $first_name, $middle_name, $last_name );
			}
		}

		return $result;
	}

	function alter_leather_order_columns( $columns ) {
		if ( ! mg_is_leather_site() ) {
			return $columns;
		}

		// Remove Total Column
		unset( $columns['total'] );

		$columns['info'] = 'Info';

		return $columns;
	}

	function leather_order_info_column( $column, $Order ) {

		$format = '<br/><b>%s</b>';

		if ( ! empty( $Order->shipoption ) ) {
			$shipping = stripos( $Order->shipoption, 'overnight' ) !== false || stripos( $Order->shipoption, '2 day' ) !== false ? "<span style='color:red'>{$Order->shipoption}</span>" : $Order->shipoption;
		} else {
			$shipping = 'Ground';
		}

		echo date( 'g:i A', mktimestamp( $Order->created ) );
		printf( $format, $shipping );
	}

	function shopp_actions() {
		// Prevent Access to Shopp Login
		add_action( 'wp', array( $this, 'prevent_login_access' ) );
	}

	function zopim_script() {
		if ( ! is_page_template( 'template-home-gateway.php' ) ) {
			?>
			<!--Start of Zopim Live Chat Script-->
			<script type="text/javascript">
			window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
			d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
			_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
			$.src="//v2.zopim.com/?32GgPj7RX7WA5djrv7R2uAesgwAZfttz";z.t=+new Date;$.
			type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
			</script>
			<!--End of Zopim Live Chat Script-->
			<?php
		}
	}

	function has_personalization( $result, $options, $Cart ) {
		$result = false;

		foreach ( $Cart as $id => $Item ) {

			if ( isset( $Item->data['Personalization Initials'] ) ) {
				$result = true;
				break;
			}
		}

		return $result;
	}


	function catch_mailchimp_signup() {
		if ( isset( $_GET['mailchimp-signup'] ) && isset( $_GET['email'] ) && isset( $_GET['fname'] ) && isset( $_GET['lname'] ) && isset( $_GET['list'] ) ) {
			global $ShoppChimp;

			$retval = $ShoppChimp->mcapi->call(
				'lists/subscribe', array(
					'id'                => $_GET['list'],
					'email'             => array( 'email' => sanitize_email( $_GET['email'] ) ),
					'merge_vars'        => array(
						'FNAME' => $_GET['fname'],
						'LNAME' => $_GET['lname'],
					),
					'double_optin'      => false,
					'update_existing'   => true,
					'replace_interests' => false,
					'send_welcome'      => false,
				)
			);

			wp_redirect( 'https://www.mooreandgiles.com/newsletter-thanks/' );
			exit();
		}
	}

	function product_detail_accordion_metaboxes() {
		if ( mg_is_leather_site() ) {
			return;
		}

		$prefix = '_cgd_';

		$accordion_box = new_cmb2_box(
			array(
				'id'           => $prefix . 'product_detail_accordion_metabox',
				'title'        => __( 'Product Details', 'cmb2' ),
				'object_types' => array( 'shopp_product' ), // Post type
				'priority'     => 'high',
			)
		);

		$group_field_id = $accordion_box->add_field(
			array(
				'id'          => $prefix . 'detail_group',
				'type'        => 'group',
				'description' => __( 'Product detail section.', 'cmb2' ),
				'options'     => array(
					'group_title'   => __( 'Section {#}', 'cmb2' ), // {#} gets replaced by row number
					'add_button'    => __( 'Add Another Section', 'cmb2' ),
					'remove_button' => __( 'Remove Section', 'cmb2' ),
					'sortable'      => true, // beta
				),
			)
		);

		$accordion_box->add_group_field(
			$group_field_id, array(
				'name' => __( 'Title', 'cmb2' ),
				'id'   => 'title',
				'type' => 'text',
			)
		);

		$accordion_box->add_group_field(
			$group_field_id, array(
				'name' => __( 'Title', 'cmb2' ),
				'id'   => 'title',
				'type' => 'text',
			)
		);

		$accordion_box->add_group_field(
			$group_field_id, array(
				'name' => __( 'Content', 'cmb2' ),
				'id'   => 'content',
				'type' => 'wysiwyg',
			)
		);
	}

	function adhoc_collection( $atts ) {
		global $wpdb;

		// Straight Products
		if ( isset( $atts['products'] ) ) {
			$product_ids = $atts['products'];

			if ( isset( $atts['coolgrid'] ) && ( $atts['coolgrid'] == true ) ) {
				if ( ! empty( $product_ids ) ) {
					ob_start();
					echo '<div class="category">';
					echo "<div class='products'>";
					$prod_ids = explode( ',', $product_ids );
					foreach ( $prod_ids as $pi ) {
						// Look for rote name listings, e.g.: Brompton - Midnight
						if ( empty( $pi ) ) {
							continue;
						}

						ShoppProduct( shopp_product( $pi ) );

						$image_id = shopp( 'product.get-cover-image', 'property=id' );

						?>
						<div class="single-product _4_columns" itemscope="" itemtype="http://schema.org/Product">
							<a href="<?php shopp( 'product.url' ); ?>" class="catalog-thumbnail" itemprop="url">
								<div><img src="
								<?php
								echo ShoppStorefrontThemeAPI::image(
									$result, array(
										'id'       => $image_id,
										'width'    => '180',
										'quality'  => '60',
										'height'   => '180',
										'fit'      => 'crop',
										'property' => 'src',
									), ShoppProduct()
								);
								?>
" /></div>
							</a>

							<div class="details">
								<div class="name">
									<a href="<?php shopp( 'product.url' ); ?>">
										<span itemprop="name">
											<br>
											<h3><?php shopp( 'product.name' ); ?></h3>
										</span>
									</a>
								</div>
							</div>
						</div>
						<?php
					}
					echo '</div>';
					echo '</div>';
					return ob_get_clean();
				}
			} else {
				ShoppCollection( new ProductCollection() );
				ShoppCollection()->slug = 'adhoc-collection';
				ShoppCollection()->load(
					array(
						'where' => array( 'p.id IN (' . $product_ids . ')' ),
					)
				);

				ob_start();
				shopp( 'storefront.collection', 'controls=off' );
				return ob_get_clean();
			}
		} elseif ( isset( $atts['prices'] ) ) {
			ob_start();
			$prices = explode( ',', $atts['prices'] );
			echo '<div class="category">';
			echo "<div class='products'>";
			foreach ( $prices as $price ) {
				// Look for rote name listings, e.g.: Brompton - Midnight
				if ( ! is_numeric( $price ) ) {
					$price_pieces = explode( ' - ', $price );

					$price = $wpdb->get_row( $wpdb->prepare( "SELECT pr.product,pr.id,pr.label FROM {$wpdb->prefix}shopp_price pr LEFT JOIN {$wpdb->prefix}posts p ON pr.product = p.ID WHERE p.post_title = %s AND pr.label = %s", $price_pieces[0], $price_pieces[1] ) );
				} else {
					$price = $wpdb->get_row( $wpdb->prepare( "SELECT pr.product,pr.id,pr.label FROM {$wpdb->prefix}shopp_price pr WHERE pr.id = %d", $price ) );
				}

				if ( empty( $price ) ) {
					continue;
				}

				ShoppProduct( shopp_product( $price->product ) );

				$image_id = $wpdb->get_var( $wpdb->prepare( "SELECT parent FROM {$wpdb->prefix}shopp_meta as nm WHERE context = 'image' AND type = 'meta' AND name = 'price' AND value = %s", $price->id ) );
				?>
				<div class="single-product _4_columns" itemscope="" itemtype="http://schema.org/Product">
					<a href="<?php shopp( 'product.url' ); ?>" class="catalog-thumbnail" itemprop="url">
						<div><img src="
						<?php
						echo ShoppStorefrontThemeAPI::image(
							$result, array(
								'id'       => $image_id,
								'width'    => '180',
								'quality'  => '60',
								'height'   => '180',
								'fit'      => 'crop',
								'property' => 'src',
							), ShoppProduct()
						);
						?>
" /></div>
					</a>

					<div class="details">
						<div class="name">
							<a href="<?php shopp( 'product.url' ); ?>">
								<span itemprop="name">
									<br>
									<h3><?php shopp( 'product.name' ); ?></h3>
									<h4 class="product-color"><?php echo $price->label; ?></h4>
								</span>
							</a>
						</div>
					</div>
				</div>
				<?php
			}
			echo '</div>';
			echo '</div>';
			return ob_get_clean();
		}
	}

	function prevent_secret_stash_index( $Product ) {
		if ( has_term( 'secret-stash', 'shopp_category', $Product->id ) ) {
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}shopp_index WHERE product = %d", $Product->id ) );
			$this->remove_filters_for_anonymous_class( 'shopp_product_saved', 'ShoppAdminWarehouse', 'index', 99 );

		}
	}

	function remove_filters_for_anonymous_class( $hook_name = '', $class_name = '', $method_name = '', $priority = 0 ) {
		global $wp_filter;

		// Take only filters on right hook name and priority
		if ( ! isset( $wp_filter[ $hook_name ][ $priority ] ) || ! is_array( $wp_filter[ $hook_name ][ $priority ] ) ) {
			return false;
		}

		// Loop on filters registered
		foreach ( (array) $wp_filter[ $hook_name ][ $priority ] as $unique_id => $filter_array ) {
			// Test if filter is an array ! (always for class/method)
			if ( isset( $filter_array['function'] ) && is_array( $filter_array['function'] ) ) {
				// Test if object is a class, class and method is equal to param !
				if ( is_object( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) && get_class( $filter_array['function'][0] ) == $class_name && $filter_array['function'][1] == $method_name ) {
					unset( $wp_filter[ $hook_name ][ $priority ][ $unique_id ] );
				}
			}
		}

		return false;
	}

	// Allow Hyphens in Shopp Roles
	function fix_shopp_roles( $result, $role ) {
		if ( stripos( $role, 'shopp-' ) !== false ) {
			return $role;
		}

		return $result;
	}

	function prevent_login_access() {
		if ( ! is_shopp_account_page() ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_redirect( get_site_url( null, '/login', 'https' ) );
			exit();
		}
	}

	function allow_admin_area_to_admins_only() {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			//Allow ajax calls
			return;
		}

		$user = wp_get_current_user();

		if ( empty( $user ) || in_array( 'customer', (array) $user->roles ) ) {
			// Redirect to Shopp account page if no user or user has 'customer' role.
			wp_redirect( get_site_url() . '/shop/account' );
			exit();
		}
	}

	function update_decline_status( $event ) {
		if ( $event->order == false ) {
			return;
		}

		$Purchase            = shopp_order( $event->order );
		$Purchase->txnstatus = 'declined';
		$Purchase->save();
		unset( $Purchase );
	}

	function shopp_add_order_line( $order = false, $data = array() ) {
		$item_fields = array(
			'product', // product id of line item
			'price', // variant id of line item
			'download', // download asset id for line item
			'dkey', // unique download key to assign to download item
			'name', // name of item
			'description', // description of item
			'optionlabel', // string label of variant combination of this item
			'sku', // sku of item
			'quantity', // quantity of items on this line
			'unitprice', // unit price
			'unittax', // unit tax
			'shipping', // line item shipping cost
			'total', // line item total cost
			'type', // Shipped, Download, Virtual, Membership, Subscription
			'addons', // array of addons
			'variation', // array of key => value (optionmenu => option) pairs for the variant combination
			'data', // associative array of item "data" key value pairs
		);

		if ( ! $Purchase = shopp_order_exists( $order ) ) {
			shopp_debug( __FUNCTION__ . ' failed: Invalid order id.' );
			return false;
		}

		// Create and save a new ShoppPurchased item object
		$Purchased = new ShoppPurchased();
		if ( is_object( $data ) && is_a( $data, 'Item' ) ) {
			$Purchased->copydata( $data );
			if ( $data->inventory ) {
				$data->unstock();
			}
		} else {
			// build purchased line item
			$Purchased->unitprice = $Purchased->unittax = $Purchased->shipping = $Purchased->total = 0;
			foreach ( $data as $key => $value ) {
				if ( ! in_array( $key, $item_fields ) ) {
					continue;
				}
				$Purchased->{$key} = $value;
			}
			if ( ! isset( $Purchased->type ) ) {
				$Purchase->type = 'Shipped';
			}
		}
		$Purchased->purchase = $order;
		if ( ! empty( $Purchased->download ) ) {
			$Purchased->keygen();
		}
		$Purchased->save();

		// Update the Purchase
		$Purchase->subtotal += $Purchased->unitprice * $Purchased->quantity;

		$Purchase->tax     += $Purchased->unittax * $Purchased->quantity;
		$Purchase->freight += $Purchased->shipping;

		$total_added      = $Purchased->total + ( $Purchased->unittax * $Purchased->quantity ) + $Purchased->shipping;
		$Purchase->total += $total_added;
		$Purchase->save();

		return ( ! empty( $Purchased->id ) ? $Purchased : false );
	}

	function shopp_rmv_order_line( $order = false, $line = 0 ) {
		$Lines = shopp_order_lines( $order );

		if ( ! isset( $Lines[ $line ] ) ) {
			return false;
		}

		$Purchased = new ShoppPurchased();
		$Purchased->populate( $Lines[ $line ] );
		$Purchase = shopp_order( $order );

		$Purchase->subtotal -= $Purchased->unitprice * $Purchased->quantity;
		$Purchase->tax      -= $Purchased->unittax * $Purchased->quantity;
		$Purchase->freight  -= $Purchased->shipping;
		$total_removed       = $Purchased->total + ( $Purchased->unittax * $Purchased->quantity ) + $Purchased->shipping;
		$Purchase->total    -= $total_removed;

		$Purchased->delete();
		$Purchase->save();

		// if ( $Purchase->balance && $Purchase->balance >= $total_removed ) {
		// 	// invoice new amount
		// 	shopp_add_order_event($Purchase->id,'amt-voided',array(
		// 		'amount' => $total_removed					// Capture of entire order amount
		// 	));
		// }

		return true;
	}

	function facebook_conversion_tracking() {

		// Facebook tracking for completed purchase
		if ( is_shopp_thanks_page() ) :
			$total = shopp( 'purchase.get-total', 'money=off' );
			if ( ! empty( $total ) ) {
				$value    = (float) $total;
				$contents = array();

				while ( shopp( 'purchase', 'items' ) ) :
					$product_id       = shopp( 'purchase', 'get-item-product' );
					$product_quantity = shopp( 'purchase', 'get-item-quantity' );
					$product_price    = shopp( 'purchase', 'get-item-total', 'money=off' );
					$product          = array(
						'id'         => $product_id,
						'quantity'   => $product_quantity,
						'item_price' => $product_price,
					);
					array_push( $contents, $product );
				endwhile;

				?>
				<script type="text/javascript">
					if ( typeof fbq !== "undefined" ) {
						fbq('track', 'Purchase',
							{
								value: <?php echo $value; ?>,
								currency: 'USD',
								contents: <?php echo wp_json_encode( $contents ); ?>,
								content_type: 'product'
							}
						);
					}
				</script>
				<?php
			}
		endif;

		// Facebook tracking for add to cart on single product page
		if ( is_shopp_product() ) :
			$product_id   = shopp( 'product.get-id' );
			$product_name = shopp( 'product.get-name' );
			$product_cat  = shopp( 'product.get-category' );
			$price        = shopp( 'product.get-price', 'money=off&taxes=false&number=1' );
			$value        = (float) $price;
			?>
			<script type="text/javascript">
				if ( typeof fbq !== "undefined" ) {
					jQuery('.addtocart.ajax-html').on('click', function(){
						fbq('track', 'AddToCart', {
							content_name: '<?php echo $product_name; ?>',
							content_category: '<?php echo $product_cat; ?>',
							content_ids: [<?php echo $product_id; ?>],
							content_type: 'product',
							value: <?php echo $value; ?>,
							currency: 'USD'
						});
					});
				}
			</script>
			<?php
		endif;
	}

	function modify_bag_sort_options( $sort_options ) {
		unset( $sort_options['random'] );
		unset( $sort_options['newest'] );
		unset( $sort_options['oldest'] );
		unset( $sort_options['custom'] );

		return $sort_options;
	}

	function cgd_google_merchant_feed_fixes( $item, $product ) {
		// Sku
		$sku = shopp( $product, 'get-sku' );
		$sku = explode( ',', $sku );

		$item['g:mpn']   = $sku[0];
		$item['g:brand'] = 'Moore & Giles, Inc.';

		// Shipping weight
		$item['g:shipping_weight'] = '0 lb';

		return $item;
	}

	function add_reports( $reports ) {
		include_once 'inc/class-sales-units-report.php';
		include_once 'inc/class-locations-report.php';
		include_once 'inc/class-state-report.php';
		include_once 'inc/class-zip-report.php';
		include_once 'inc/class-source-code-report.php';
		include_once 'inc/class-top-products-report.php';

		$reports['sales']        = array(
			'class' => 'MG_SalesReport',
			'name'  => Shopp::__( 'Sales Report' ),
			'label' => Shopp::__( 'Sales' ),
		);
		$reports['locations']    = array(
			'class' => 'MG_LocationsReport',
			'name'  => Shopp::__( 'Locations Report' ),
			'label' => Shopp::__( 'Locations' ),
		);
		$reports['source_codes'] = array(
			'class' => 'MG_SourceCodeReport',
			'name'  => Shopp::__( 'Source Codes' ),
			'label' => Shopp::__( 'Source Codes' ),
		);
		$reports['products']     = array(
			'class' => 'MG_ProductsReport',
			'name'  => Shopp::__( 'Products' ),
			'label' => Shopp::__( 'Products' ),
		);

		if ( ( ! empty( $_GET['report'] ) && $_GET['report'] == 'locations_state' ) && ! empty( $_GET['country'] ) ) {
			$reports['locations_state'] = array(
				'class' => 'MG_LocationsStateReport',
				'name'  => Shopp::__( 'Locations State Report' ),
				'label' => Shopp::__( 'Locations State Report' ),
			);
		}

		if ( ( ! empty( $_GET['report'] ) && $_GET['report'] == 'locations_zip' ) && ! empty( $_GET['state'] ) ) {
			$reports['locations_zip'] = array(
				'class' => 'MG_LocationsZipReport',
				'name'  => Shopp::__( 'Locations Zip Report' ),
				'label' => Shopp::__( 'Locations Zip Report' ),
			);
		}

		return $reports;
	}

	/**
	 * Authorize.net Auto Charge
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function authorizedotnet_autocharge() {
		add_filter(
			'shopp_purchase_order_' . strtolower( 'AuthorizeNet' ) . '_processing',
			function( $processing, $purchase ) {
				$order_id = $purchase->id;
				$status   = shopp_meta( $order_id, 'purchase', 'flp_status' );

				if ( 'APPROVE' !== $status ) {
					return 'auth';
				}

				return 'sale';
			},
			99,
			2
		);
	}

	/**
	 * Variant widget out of stock label
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function variant_widget_out_of_stock_label( $result, $options, $object ) {

		// Bail if not a product page.
		if ( ! is_shopp_product() ) {
			return $results;
		}

		$product = ShoppProduct();
		$prices  = $product->prices ? $product->prices : array();

		// Bail if prices array is empty.
		if ( empty( $prices ) ) {
			return $results;
		}

		foreach ( $prices as $price ) {

			if ( '0' === $price->stock && 'variation' === $price->context && 'on' === $price->inventory ) {
				$name          = $price->label;
				$newname       = $name . ' (Out of Stock)';
				$options_array = isset( $object->options['v'][1]['options'] ) ? $object->options['v'][1]['options'] : array();

				// Bail if options array is empty.
				if ( empty( $options_array ) ) {
					return $results;
				}

				foreach ( $options_array as $key => $value ) {
					if ( $price->label === $value['name'] ) {
						$object->options['v'][1]['options'][ $key ]['name'] = $newname;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Set Gift Note Prefix
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param object $purchase The purchase object.
	 *
	 * @return void
	 */
	public function set_gift_note_prefix( $purchase ) {
		$gift_note = shopp_meta( $purchase->id, 'purchase', 'Gift Note', 'order-data' );

		if ( ! empty( $gift_note ) ) {
			shopp_set_meta( $purchase->id, 'purchase', 'Gift Note', 'Gift Note: ' . $gift_note, 'order-data' );
		}
	}

	/**
	 * @param \ShoppPurchase $purchase
	 *
	 * @throws Exception
	 */
	function maybe_generate_promo( $purchase ) {
		$timezone = new \DateTimeZone( get_option( 'timezone_string' ) );
		$date     = new DateTime( 'now', $timezone );
		$from     = new DateTime( '2019-11-29', $timezone );
		$to       = new DateTime( '2019-12-03', $timezone );
		$to->setTime( 3, 0 );

		if ( $date->getTimestamp() >= $from->getTimestamp() && $date->getTimestamp() <= $to->getTimestamp() ) {
			if ( $purchase->subtotal >= 250 ) {
				$promo_template = new ShoppPromo( 128 );
				$new_promo      = $promo_template->duplicate();

				$new_promo_code  = $this->generate_promo_code();
				$new_promo->name = 'Black Friday 2019 - ' . $new_promo_code;

				foreach ( $new_promo->rules as $key => $rule ) {
					if ( 'item' === $key ) {
						continue;
					}

					if ( 'Promo code' === $rule['property'] ) {
						$new_promo->rules[ $key ]['value'] = $new_promo_code;
					}
				}

				$new_promo->status = 'enabled';
				$new_promo->save();

				shopp_set_meta( $purchase->id, 'purchase', 'black_friday_promo_code', $new_promo_code, 'order-data' );

				// Send email
				$purchase->email(
					"$purchase->firstname $purchase->lastname",
					$purchase->email,
					'Your Free Moore & Giles Gift',
					array( 'email-black-friday.php' )
				);
			}
		}
	}

	/**
	 * @param \ShoppPurchase $purchase
	 */
	function maybe_invalidate_promo( $purchase ) {
		foreach ( $purchase->discounts as $discount_id => $discount ) {
			if ( stripos( $discount->name, 'Black Friday' ) !== false ) {
				$discount         = new ShoppPromo( $discount_id );
				$discount->status = 'disabled';
				$discount->save();
			}
		}
	}

	function generate_promo_code() {
		$chars          = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$new_promo_code = '';
		for ( $i = 0; $i < 10; $i ++ ) {
			$new_promo_code .= $chars[ mt_rand( 0, strlen( $chars ) - 1 ) ];
		}

		return $new_promo_code;
	}

	/**
	 * @param bool $item_matches
	 * @param ShoppCartItem $item
	 * @param ShoppOrderDiscount $discount
	 *
	 * @return bool
	 */
	function limit_cart_item_discount_applications( $item_matches, $item, $discount ) {
		if ( $item_matches ) {
			// Check if any other item has already matched
			$items = $discount->items();

			if ( count( $items ) > 0 ) {
				$item_matches = false;
			}
		}

		return $item_matches;
	}

	/**
	 * @param float $amount
	 * @param ShoppCartItem $item
	 * @param ShoppOrderDiscount $discount
	 *
	 * @return float|int
	 */
	function limit_cart_item_discount_amount( $amount, $item, $discount ) {
		if ( stripos( $discount->name(), 'Black Friday' ) === false ) {
			return $amount;
		}

		if ( $amount > 0 && $item->addons ) {
			foreach ( $item->addons as $addon ) {
				$amount -= $addon->price;
			}
		}

		return $amount > 0 ? $amount : 0;
	}

	function maybe_apply_discounts_from_url() {
		if ( ! empty( $_REQUEST['discount'] ) && ! empty( $_REQUEST['redirect'] ) ) { // phpcs:ignore
			ShoppOrder()->Cart->totals();
		}
	}

	function maybe_redirect_after_adding_discount_from_url() {
		if ( ! empty( $_REQUEST['discount'] ) && ! empty( $_REQUEST['redirect'] ) ) { // phpcs:ignore
            wp_safe_redirect( $_REQUEST['redirect'] ); //phpcs:ignore
			exit();
		}
	}
}

$MG_CoreFunctionality = new MG_CoreFunctionality();
