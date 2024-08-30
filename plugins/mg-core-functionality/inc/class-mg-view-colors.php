<?php

class MG_ViewColors {

	/**
	 * MG_ViewColors constructor.
	 */
	public function __construct() {
		add_filter('shopp_themeapi_product_viewallstyles', array($this, 'view_all_styles'), 10, 3);
	}

	function view_all_styles($result, $options, $Product) {
		if ( shopp('product.has-last-name') && ! shopp('product.has-variations') && ! is_shopp_product() && (is_shopp_category() || is_shopp_catalog_frontpage() ) ) {
			return '<p class="all-styles-p"><a class="all-styles" href="/shop/tag/' . sanitize_title_with_dashes( shopp('product.get-first-name') ) . '">View All Colors</a></p>';
		}

		if ( shopp('product.has-variations') ) {
			return '<p class="all-styles-p"><a class="all-styles" href="' . shopp('product.get-url') . '">View All Colors</a></p>';
		}
	}
}
