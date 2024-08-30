<?php
namespace Objectiv\Site\Woo;

use Objectiv\Site\TwigFunctions\ProductReviewsTotalTwigFunction;
use Objectiv\Site\TwigFunctions\StashWooProduct;
use Objectiv\Site\TwigFunctions\UnStashWooProduct;
use \Timber\Timber;
use \Objectiv\Site\TwigFunctions\SetWooProductTwigFunction;

class WooSetup {

	/**
	 * WooSetup constructor.
	 */
	public function __construct() {
		$this->load_actions();
		$this->load_filters();
	}

	/**
	 * Load the filters that manipulate the context
	 */
	public function load_actions() {
		do_action( 'objectiv_site_timber_woo_setup' );
	}

	/**
	 * Load helper filters
	 */
	public function load_filters() {
		add_filter('objectiv_site_twig_functions', array($this, 'add_product_loaders' ));
	}

	/**
	 * Adds a twig function specific to woocommerce that allows the archive page to loop properly. See the timber docs
	 * for more information
	 *
	 * @param $twig_functions
	 *
	 * @return array
	 */
	public function add_product_loaders( $twig_functions ) {

		$twig_functions[] = new SetWooProductTwigFunction( 'set_woo_product' );
		$twig_functions[] = new StashWooProduct( 'stash_woo_product' );
		$twig_functions[] = new UnStashWooProduct( 'unstash_woo_product' );

		return $twig_functions;
	}

	/**
	 * Set the proper context for typescript otherwise they all end up being woocommerce-single
	 *
	 * @param $template_file
	 *
	 * @return string
	 */
	public function get_js_template_file($template_file) {
		// If the template file name is woocommerce (which it is for the archive and product single)
		if ( $template_file == "woocommerce" ) {
			if(is_singular('product')) {
				$template_file .= "-single";
			}
		}

		// If cart page...
		if(is_cart()) {
			$template_file = "woocommerce-cart";
		}

		// If checkout page...
		if(is_checkout()) {
			$template_file = "woocommerce-checkout";
		}

		// If order received page...
		if(is_order_received_page()) {
			$template_file = "woocommerce-order-received";
		}

		return $template_file;
	}

	/**
	 * The base set of manipulations to the context that we need to setup WooCommerce. Called from Main typically
	 *
	 * @param $context
	 */
	public function load_context($context) {
		if ( is_singular( 'product' ) ) {
			$context = apply_filters( 'objectiv_site_timber_woo_single_product_context', $context );
		} else {
			$context = apply_filters( 'objectiv_site_timber_woo_archive_context', $context );

			if ( is_product_category() ) {
				$context = apply_filters( 'objectiv_site_timber_woo_category_context', $context );
			}
		}

		$context = apply_filters( 'objectiv_site_timber_after_woo_template_context', $context );

		// Restore the context and loop back to the main query loop.
		wp_reset_postdata();

		if(is_singular('product')) {
			Timber::render( 'views/woo/single-product.twig', $context );
		} else {
			Timber::render( 'views/woo/archive.twig', $context );
		}
	}
}
