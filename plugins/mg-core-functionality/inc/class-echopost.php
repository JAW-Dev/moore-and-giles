<?php

class MG_EchoPost {
	public function __construct() {
		add_action('init', array($this, 'init') );
	}

	function init() {
		if ( ! is_user_logged_in() || ! current_user_can('mg_impersonate_user') ) {
			add_action('wp_footer', array($this, 'add_pixel') );
		}
	}

	function add_pixel() {
		?>
		<img src="//pixel.locker2.com/mooreandgiles.com/px.png" style="position: absolute; ">
		<?php
	}
}
