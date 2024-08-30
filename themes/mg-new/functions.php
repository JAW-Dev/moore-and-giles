<?php
$GLOBALS['objectiv_prefix'] = 'mg';

/**
 * Current version of the theme.
 *
 * @since 1.0
 */
define( 'MG_THEME_VERSION', '1.0.2' );

/**
 * Template's directory with trailing slash
 *
 * @since 1.0.0
 * @uses get_template_directory()
 * @uses trailingslashit()
 */
define( 'MG_THEME_DIR', trailingslashit( get_stylesheet_directory() ) );

/**
 * Template's URI with trailing slash
 *
 * @since 1.0.0
 * @uses get_template_directory_uri()
 * @uses trailingslashit()
 */
define( 'MG_THEME_URI', trailingslashit( get_stylesheet_directory_uri() ) );

/**
 * Current version of the stylesheet
 *
 * @since 1.0
 */
define( 'MG_STYLESHEET_VERSION', filemtime( MG_THEME_DIR . 'style.css' ) );

/**
 * Admin images directory with trailing slash
 *
 * @since 1.0.0
 * @uses tralingslashit()
 */
define( 'MG_THEME_ADMIN_IMAGES_DIR', trailingslashit( MG_THEME_URI . 'admin_assets/images/admin' ) );

/**
 * Themes LIB URL
 *
 * @since 1.0.0
 * @uses trailingslashit()
 */
define( 'MG_THEME_LIB_DIR', trailingslashit( MG_THEME_DIR . 'lib' ) );

/**
 * Add Theme Support
 *
 * @author Jason Witt
 *
 * @return void
 */
function mg_theme_setup() {
	// The answer to all my woocommerce problems apparently
	add_theme_support(
		'woocommerce',
		array(
			'single_image_width'            => 412,
			'thumbnail_image_width'         => 60,
			'gallery_thumbnail_image_width' => 60,
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_editor_style();
}
add_action( 'after_setup_theme', 'mg_theme_setup' );

/**
 * Require all needed files for the theme to work
 *
 * @since 1.0.0
 */
require_once MG_THEME_DIR . 'vendor/autoload.php';

use Objectiv\Site\Main;
use \Timber\Timber;

$timber          = new Timber();
Timber::$dirname = array( 'views' );

// Cache generated Timber php
// This does not apply if WP_DEBUG is set to true
Timber::$cache = true;

if ( ! empty( $_GET['mg_flush_timber_cache'] ) ) {
	$loader = new \Timber\Loader();
	$loader->clear_cache_twig();
	die( 'Success' );
}

// Timmy Image
new Timmy\Timmy();

// Timmy Image Sizes
add_filter(
	'timmy/sizes',
	function ( $sizes ) {
		return $sizes + array(
			'thumbnail'                     => array(
				'resize'     => array( 305 ),
				'name'       => 'Thumbnail',
				'post_types' => array( 'all' ),
			),
			'medium'                        => array(
				'resize'     => array( 700 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'Medium',
				'post_types' => array( 'all' ),
			),
			'post_archive'                  => array(
				'resize'     => array( 420, 228 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'Post Archive',
				'post_types' => array( 'post' ),
			),
			'category_banner_image'         => array(
				'resize'     => array( 1600, 800 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'Artist Image',
				'post_types' => array( 'all' ),
			),
			'large'                         => array(
				'resize'     => array( 1400 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'Large',
				'post_types' => array( 'all' ),
			),
			'woocommerce_gallery_thumbnail' => array(
				'resize'     => array( 60 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'WooCommerce Gallery Thumbnail',
				'post_types' => array( 'product', 'product_variation' ),
			),
			'woocommerce_single'            => array(
				'resize'     => array( 801 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'WooCommerce Single',
				'post_types' => array( 'product', 'product_variation' ),
			),
			'homepage_category_block'       => array(
				'resize'     => array( 844 ),
				'srcset'     => array( 0.3, 0.5, 2 ),
				'name'       => 'Home Page Category Block',
				'post_types' => array( 'all' ),
			),
		);
	}
);

add_filter( 'woocommerce_resize_images', '__return_false' );

// Disable WP 5.3+ image scaling
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Performance Boost for ACF images
 *
 * @author Jason Witt
 */
add_filter(
	'acf/load_field/type=image',
	function( $field ) {
		$field['return_format'] = 'id';

		return $field;
	}
);

add_filter(
	'timmy/oversize',
	function ( $oversize ) {
		$oversize['allow'] = true;

		return $oversize;
	}
);

add_filter(
	'image_size_names_choose',
	function ( $sizes ) {
		unset( $sizes['full'] );
		return $sizes;
	},
	11
);

// Add WP functions to Timber Context.
function add_is_home_to_context( $data ) {
	$data['is_home'] = is_home();
	return $data;
}
add_filter( 'timber_context', 'add_is_home_to_context' );

function add_is_front_page_to_context( $data ) {
	$data['is_front_page'] = is_front_page();
	return $data;
}
add_filter( 'timber_context', 'add_is_front_page_to_context' );

function add_signup_forms_to_context( $data ) {
	$sign_up_block  = obj_get_acf_field( 'mg_sign_up_block', 'option' );
	$sign_up_footer = obj_get_acf_field( 'mg_sign_up_footer', 'option' );

	$data['sign_up_block'] = array(
		'title'       => $sign_up_block['title'],
		'blurb'       => $sign_up_block['blurb'],
		'button'      => $sign_up_block['button'],
		'placeholder' => $sign_up_block['placeholder'],
	);

	$data['sign_up_footer'] = array(
		'title'       => $sign_up_footer['title'],
		'blurb'       => $sign_up_footer['blurb'],
		'button'      => $sign_up_footer['button'],
		'placeholder' => $sign_up_footer['placeholder'],
	);
	return $data;
}
add_filter( 'timber_context', 'add_signup_forms_to_context' );

function shop_reg_sidebar() {
	register_sidebar(
		array(
			'name'          => __( 'Woo Sidebar', 'moore-and-giles' ),
			'id'            => 'woo-sidebar-id',
			'description'   => '',
			'class'         => 'woo-sidebar-el',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		)
	);
}

add_action( 'widgets_init', 'shop_reg_sidebar' );

set_post_thumbnail_size( 0, 0 );

function typekit_fonts( $fonts ) {
	$page_template = mg_get_page_template();

	if ( 'template-reclaimed.php' === $page_template ) {
		$fonts[ "{$GLOBALS['objectiv_prefix']}google" ] = 'https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;500;700;900&display=swap';
	}

	$fonts[ "{$GLOBALS['objectiv_prefix']}typekit" ] = 'https://use.typekit.net/jqp6wia.css';

	return $fonts;

}
add_filter( 'objectiv_site_fonts', 'typekit_fonts' );

function mg_get_page_template() {
	$id = get_queried_object_id();
	return get_page_template_slug();
}

/**
 * Navigation Locations
 */
add_filter(
	'objectiv_site_nav_menus',
	function() {
		return array(
			'site_woo_bags'          => 'Mobile Flyout Menu Main Menu Links',
			'site_mobile_middle'     => 'Mobile Flyout Menu Middle Links',
			'site_mobile_bottom'     => 'Mobile Flyout Menu Bottom Links',
			'site_meta'              => 'Mobile Header Row',
			'site_footer_desktop'    => 'Site Footer Desktop',
			'site_footer_mobile'     => 'Site Footer Mobile',
			'site_footer_disclaimer' => 'Site Footer Disclaimer',
		);
	}
);

function ovdump( $data ) {
	print( '<pre>' . print_r( $data, true ) . '</pre>' );
}

add_action( 'admin_init', 'hide_editor' );
function hide_editor() {
	// Get the Post ID.
	$post_id = isset( $_GET['post'] ) ? $_GET['post'] : isset( $_POST['post_ID'] );
	if ( ! isset( $post_id ) ) {
		return;
	}

	// Get the name of the Page Template file.
	$template_file = get_post_meta( $post_id, '_wp_page_template', true );

	// Add a template file to hide the editor on that template.
	$template_file_array = array(
		'template-branding-resources.php',
		'template-films.php',
		'template-refurbishment.php',
		'template-return-exchange.php',
		'template-monogram.php',
		'template-corporate-catalog.php',
		'template-faqs.php',
		'template-wysiwyg-sections.php',
		'template-login.php',
		'template-reclaimed.php',
	);

	if ( in_array( $template_file, $template_file_array ) ) {
		remove_post_type_support( 'page', 'editor' );
	}
}

function objectiv_id_from_string( $string = null, $rand = true ) {
	if ( ! empty( $string ) ) {
		if ( $rand ) {
			$whoa = substr( md5( microtime() ), rand( 0, 26 ), 5 );
			return strtolower( preg_replace( '/[\s\(\)]/', '-', $string ) . $whoa );
		} else {
			return strtolower( preg_replace( '/[\s\(\)]/', '-', $string ) );
		}
	} else {
		return null;
	}
}

// Helper to display gravity form with an id
function objectiv_gform( $form_id = null ) {
	if ( ! empty( $form_id ) ) {
		gravity_form_enqueue_scripts( $form_id, true );
		gravity_form( $form_id, false, false, false, '', false, 1 );
	}
}

function objectiv_echo_get_file_contents( $file = null ) {
	if ( ! empty( $file ) ) {
		if ( ! empty( $file ) ) {

			$response = wp_remote_get( $file, array( 'sslverify' => false ) );

			if ( ! is_wp_error( $response ) ) {
				$file = $response['body'];
			} else {
				return '';
			}
			echo $file;
		}
	}
}

// Set a content width which is used for a few things... particularly videos embedded in wysiwyg content.
if ( ! isset( $content_width ) ) {
	$content_width = 940;
}

// Helps Woocommerce Behave.
add_action( 'the_post', 'wc_setup_product_data_fix', 11 );
function wc_setup_product_data_fix() {

	remove_action( 'the_post', 'wc_setup_product_data' );

	return;
}

/**
 * This is where we load the Moore and Giles specific woo setup (Outside of the Main class so we can pull out the
 * framework / mg specific items later)
 */
add_action(
	'objectiv_site_timber_woo_setup',
	function() {
		$mg_woo_setup = new \Objectiv\Site\Woo\WooMGSetup();
	}
);


/**
 * Post Types
 *
 * This file registers custom post types.
 *
 * @package     MG_Genesis_Child
 * @since       1.0
 * @author      Clif Griffin <clif@cgd.io>
 */

add_action( 'init', 'register_cpt' );

function register_cpt() {

	register_post_type(
		'press',
		array(
			'public'      => true,
			'show_ui'     => true,
			'has_archive' => true,
			'rewrite'     => array(
				'with_front' => false,
				'slug'       => 'press',
			),
			'supports'    => array(
				'title',
				'thumbnail',
			),
			'menu_icon'   => 'dashicons-media-document',
			'labels'      => array(
				'name'          => 'Press',
				'label'         => __( 'Press' ),
				'add_new'       => __( 'Add New', 'press' ),
				'add_new_item'  => __( 'Add New Press Item' ),
				'new_item'      => __( 'New Press Item' ),
				'singular_name' => __( 'Press Item' ),
			),
		)
	);

}

function multiple_post_thumbnails() {
	if ( class_exists( 'MultiPostThumbnails' ) ) {
		new MultiPostThumbnails(
			array(
				'label'     => 'Inside Image',
				'id'        => 'inside-image',
				'post_type' => 'press',
			)
		);
	}
}
add_action( 'init', 'multiple_post_thumbnails', 100 );

function objectiv_get_press_secondary_image_id( $post_id = null, $post_type = null, $id = null ) {
	$secondary_image = null;

	if ( class_exists( 'MultiPostThumbnails' ) ) {
		$secondary_image = get_post_meta( $post_id, "{$post_type}_{$id}_thumbnail_id", true );
	}

	return $secondary_image;
}


// Make sure this is the last thing in functions. Clears up any problems with filters / actions not being ready
$GLOBALS['objectiv_site'] = new Main( MG_THEME_DIR );

// Number of returned
function obj_searchwp_live_search_posts_per_page() {
	return 20; // return 20 results
}
add_filter( 'searchwp_live_search_posts_per_page', 'obj_searchwp_live_search_posts_per_page' );

// Filter order recieved message. Handling elsewhere.
add_filter( 'woocommerce_thankyou_order_received_text', 'obj_thankyou_text' );
function obj_thankyou_text() {
	return '';
}

add_filter(
	'woocommerce_cart_shipping_method_full_label',
	function( $label, $method ) {
		$id           = $method->get_id();
		$method_id    = $method->get_method_id();
		$service_code = str_replace( 'ups:3:', '', $id );
		$delivery     = function_exists( 'mg_get_atp' ) ? mg_get_atp( $service_code, $method_id ) : '';

		if ( $delivery ) {
			$label .= '<small class="delivery-by" style="">Delivers On or Before: ' . esc_html( $delivery->format( 'F j, Y' ) ) . '</small>';
		}

		return $label;
	},
	100,
	2
);

/**
 * Get meta and options ACF data.
 *
 * @param string  $selector     The ACF field ID.
 * @param int     $post_id      The post ID.
 * @param string  $format_value The type of meta to get.
 * @param boolean $use_acf      True to use native ACF get_field().
 */
function obj_get_acf_field( $selector = null, $post_id = null, $format_value = true, $use_acf = false ) {
	$result = null;

	if ( ! function_exists( 'acf_get_valid_post_id' ) || ! function_exists( 'get_field' ) ) {
		return false;
	}

	if ( $use_acf ) {
		// Allow for just using ACF.
		$result = get_field( $selector, $post_id, $format_value );
	} else {

		// A helpful little function that doesn't do too much but returns an id we can use.
		$post_id = acf_get_valid_post_id( $post_id );

		// Grab options setting if that is what is set, otherwise get post meta.
		if ( $post_id === 'options' ) {
			$result = get_option( 'options_' . $selector );
		} else {
			$result = get_post_meta( $post_id, $selector, true );
		}

		// Fall back to ACF field selector if we don't have anything.
		if ( empty( $result ) ) {
			$result = get_field( $selector, $post_id, $format_value );
		}
	}

	if ( empty( $result ) ) {
		$result = false;
	}

	return $result;

}

function obj_get_cat_pop_products( $category_id = null ) {
	if ( empty( $category_id ) ) {
		return null;
	}

	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'meta_key'       => 'total_sales',
		'orderby'        => 'meta_value_num',
		'order'          => 'desc',
		'posts_per_page' => 4,
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $category_id,
				'operator' => 'IN',
			),
		),
	);

	$products = new WP_Query( $args );

	return $products->posts;
}


function is_current_page( $url = null ) {
	if ( empty( $url ) ) {
		return false;
	}

	$actual_link = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$url_parts         = wp_parse_url( $url );
	$actual_link_parts = wp_parse_url( $actual_link );

	if ( array_key_exists( 'path', $url_parts ) && array_key_exists( 'path', $actual_link_parts ) ) {
		return $url_parts['path'] === $actual_link_parts['path'];
	} else {
		return false;
	}
}

add_filter(
	'wc_get_template',
	function( $template, $template_name, $args, $template_path, $default_path ) {
		if ( 'wvs-archive-variation.php' == $template_name ) {
			return MG_THEME_DIR . '/woocommerce/' . $template_name;
		}

		return $template;
	},
	10,
	5
);


// Only allow ACF fields to be edited on development
if ( ! defined( 'WP_LOCAL_DEV' ) || ! WP_LOCAL_DEV ) {
	add_filter( 'acf/settings/show_admin', '__return_false' );
}

add_filter( 'woocommerce_account_menu_items', 'mg_remove_downloads_account_link', 999 );
function mg_remove_downloads_account_link( $items ) {
	unset( $items['downloads'] );
	return $items;
}


/**
 * Get the order item attribues for the Ne Order Email
 *
 * @author Jason Witt
 *
 * @param object $order_item The order Item.
 *
 * @return array
 */
function get_order_item_attributes( $order_item ) {
	$item_data  = $order_item->get_data();
	$item_meta  = $item_data['meta_data'];
	$product    = wc_get_product( $item_data['product_id'] );
	$attributes = $product->get_attributes();
	$array      = array();

	foreach ( $item_meta as $meta ) {
		$meta_data = $meta->get_data();

		foreach ( $attributes as $key => $value ) {
			if ( $key === $meta_data['key'] ) {
				$name  = ucwords( str_replace( 'pa_', '', $meta_data['key'] ) );
				$value = ucwords( str_replace( array( '_', '-' ), ' ', $meta_data['value'] ) );

				$array[ $key ] = array(
					'name'  => $name,
					'value' => $value,
				);
			}
		}
	}

	return $array;
}

add_action( 'wp_enqueue_scripts', 'mg_dequeue_assets', 1000 );

function mg_dequeue_assets() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		wp_dequeue_style( 'angelleye-express-checkout-css' );
	}
}

add_filter( 'posts_clauses', 'order_by_stock_status' );
/**
 * Sort the catalog by stock status.
 *
 * @param array $posts_clauses The post query clauses.
 *
 * @return array
 * @author Jason Witt
 *
 */
function order_by_stock_status( array $posts_clauses ) {
	global $wpdb;

	if ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$posts_clauses['join']   .= " INNER JOIN $wpdb->postmeta istockstatus ON ($wpdb->posts.ID = istockstatus.post_id) ";
		$posts_clauses['orderby'] = ' istockstatus.meta_value ASC, ' . $posts_clauses['orderby'];
		$posts_clauses['where']   = " AND istockstatus.meta_key = '_stock_status' AND istockstatus.meta_value <> '' " . $posts_clauses['where'];
	}

	return $posts_clauses;
}

add_action( 'init', 'mg_add_coupon_to_session' );

function mg_add_coupon_to_session() {
	if ( isset( $_GET['discount'] ) ) {
		// Ensure that customer session is started
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		// Check and register coupon code in a custom session variable
		$coupon_code = WC()->session->get( 'coupon_code' );
		if ( empty( $coupon_code ) ) {
			$coupon_code = esc_attr( $_GET['discount'] );
			WC()->session->set( 'coupon_code', $coupon_code ); // Set the coupon code in session
		}

		if ( isset( $_GET['redirect'] ) ) {
			wp_safe_redirect( $_GET['redirect'] );
			exit();
		}
	}
}

add_action( 'woocommerce_before_checkout_form', 'mg_add_discount_to_checkout_from_session', 10, 0 );

function mg_add_discount_to_checkout_from_session() {
	// Set coupon code
	$coupon_code = WC()->session->get( 'coupon_code' );
	if ( ! empty( $coupon_code ) && ! WC()->cart->has_discount( $coupon_code ) ) {
		WC()->cart->add_discount( $coupon_code ); // apply the coupon discount
		WC()->session->__unset( 'coupon_code' ); // remove coupon code from session
	}
}

/**
 * Only use the WooCommerce SearchWP Engine for search autocomplete.
 *
 * @param array $configs The SearchWP Live Ajax Search configs.
 *
 * @return array
 */
function my_searchwp_live_search_configs( $configs ) {
	$configs['default']['engine'] = 'wooproducts';
	return $configs;
}

add_filter( 'searchwp_live_search_configs', 'my_searchwp_live_search_configs' );

/**
 * Add the stock quantity to the varation json data.
 *
 * @param array               $array     The array of varation data.
 * @param WC_Product_Variable $product   The variable product.
 * @param WC_Product          $variation The product varation.
 */
function variation_json_array( $array, $product, $variation ) {
	$array['stock_quantity'] = $variation->get_stock_quantity();

	return $array;
}
add_filter( 'woocommerce_available_variation', 'variation_json_array', 10, 3 );

/**
 * Add body class to products
 *
 * @param array $classes The body classes.
 *
 * @return array
 */
function add_custom_product_template_classes( $classes ) {
	$template = obj_get_acf_field( 'mg_furniture_template', get_the_ID() );

	if ( get_post_type() === 'product' && $template === 'custom' ) {
		$classes[] = 'custom-furniture-template';
	}

	if ( get_post_type() === 'product' && $template === 'standard' ) {
		$classes[] = 'standard-furniture-template';
		$classes[] = 'standard-product-template';
	}

	if ( get_post_type() === 'product' && empty( $template ) ) {
		$classes[] = 'standard-product-template';
	}

	return $classes;
}
add_action( 'body_class', 'add_custom_product_template_classes' );

/**
 * Set the form ID for the leather swatch sample form
 *
 * @return int
 */
function mg_get_leater_swatch_form_id() {
	$leather_sample_swatch_form = obj_get_acf_field( 'global_swatches_form_for_modal', 'option' );

	if ( empty( $leather_sample_swatch_form ) ) {
		$leather_sample_swatch_form = 39;
	} else {
		$leather_sample_swatch_form = (int) $leather_sample_swatch_form;
	}

	return $leather_sample_swatch_form;
}

/**
 * Force Username to be email
 */
function mg_set_username_to_email_address( $username, $email, $new_user_args, $suffix ) {
    return $email;
}

add_filter( 'woocommerce_new_customer_username', 'mg_set_username_to_email_address', 10, 4 );


/**
 * Add gift card info to checkout cart
 */
function gift_card_needs_calculation( $needs_calculation ) {
	$applied_giftcards = WC_GC()->giftcards->get_applied_giftcards_from_session();

	if ( ! empty( $applied_giftcards ) ) {
		return true;
	}

	return $needs_calculation;
}

add_filter( 'woocommerce_gc_cart_needs_calculation', 'gift_card_needs_calculation' );

add_filter( 'wpsm_show_emails_capability', function() {
	return 'view_woocommerce_reports';
} );

add_filter(
	'wdp_calculate_totals_hook_priority',
	function( $priority ) {
		return 10;
	}
);
