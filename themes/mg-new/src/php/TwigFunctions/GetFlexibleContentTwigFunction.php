<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class GetFlexibleContentTwigFunction extends TwigFunction {
	/**
	 * Get Similar Products
	 *
	 * @param $product_id
	 *
	 * @return array
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 */
	public function action( $args ) {
		$post_id = isset( $args['post_id'] ) ? $args['post_id'] : get_the_ID();
		$flex_id = isset( $args['flexible_id'] ) ? $args['flexible_id'] : '';

		if ( $flex_id && $post_id ) {
			return function_exists( 'get_field' ) ? get_field( $flex_id, $post_id ) : array();
		}

		return array();
	}
}
