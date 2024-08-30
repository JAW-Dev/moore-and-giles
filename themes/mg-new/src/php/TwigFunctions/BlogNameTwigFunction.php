<?php
namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class BlogNameTwigFunction extends TwigFunction {

	/**
	 * Blog Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function action( $args = '' ) {
		echo esc_html( get_bloginfo( 'name' ) );
	}
}
