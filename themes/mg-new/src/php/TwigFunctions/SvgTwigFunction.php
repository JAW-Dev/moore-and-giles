<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;
use \Objectiv\Site\Factories\TwigFunctionFactory;

class SvgTwigFunction extends TwigFunction {
	public function action($args) {
		if(empty($args) || $args == null)
			return '';

		$source = "";
		$name = $args;

		$response = wp_remote_get( get_stylesheet_directory_uri() . "/views/components/icons/$name.svg", array( 'sslverify' => false ) );

		if ( ! is_wp_error( $response ) ) {
			$source = $response['body'];
		} else {
			return '';
		}

		return $source;
	}
}
