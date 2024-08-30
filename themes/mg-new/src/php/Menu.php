<?php

namespace Objectiv\Site;

class Menu extends \Timber\Menu {

	public function __construct( $slug = 0 ) {

		if ( is_multisite() && $this->useNetworkMenu() && ! is_main_site() ) {

		}

		parent::__construct( $slug );
	}

	/**
	 * @return bool
	 */
	public function useNetworkMenu() {
		return get_option( 'objectiv_use_network_menu' );
	}
}
