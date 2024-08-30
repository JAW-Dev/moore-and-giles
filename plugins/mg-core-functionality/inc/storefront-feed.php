<?php

class MG_Storefront_Feed {
	public function __construct() {
		add_action('wp_ajax_nopriv_storefront_feed', array($this, 'feed') );
	}

	function feed() {
		shopp( 'storefront.catalog-products','load=true' );

		$Collection = ShoppCollection();

		$base = shopp_setting('base_operations');

		add_filter( 'shopp_rss_description', 'wptexturize' );
		add_filter( 'shopp_rss_description', 'convert_chars' );
		add_filter( 'shopp_rss_description', 'make_clickable', 9 );
		add_filter( 'shopp_rss_description', 'force_balance_tags', 25 );
		add_filter( 'shopp_rss_description', 'convert_smilies', 20 );
		add_filter( 'shopp_rss_description', 'wpautop', 30 );
		add_filter( 'shopp_rss_description', 'ent2ncr' );

		do_action_ref_array( 'shopp_collection_feed', array($Collection) );

		$rss = array( 'title' => trim( get_bloginfo('name') . ' ' . $Collection->name ),
						'link' => shopp($Collection, 'get-feed-url'),
						'description' => $Collection->description,
						'sitename' => get_bloginfo('name') . ' (' . get_bloginfo('url') . ')',
						'xmlns' => array( 'shopp'=>'http://shopplugin.net/xmlns',
							'g' => 'http://base.google.com/ns/1.0',
							'atom' => 'http://www.w3.org/2005/Atom',
							'content' => 'http://purl.org/rss/1.0/modules/content/')
						);
		$rss = apply_filters('shopp_rss_meta', $rss);

		$tax_inclusive = shopp_setting_enabled('tax_inclusive');

		$template = locate_shopp_template( array('feed-' . $Collection->slug . '.php', 'feed.php') );
		if ( ! $template ) $template = SHOPP_ADMIN_PATH . '/categories/feed.php';

		header('Content-type: application/rss+xml; charset=' . get_option('blog_charset') );

		ob_start();
		include($template);
		$content = ob_get_clean();

		echo preg_replace('!<big>.*?</big>!is', '', $content);

		wp_die();
	}
}
