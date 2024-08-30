<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class IsProductCategoryTerm extends TwigFunction {
	/**
	 * Error Log
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return boolean
	 */
	public function action( $args = '' ) {

		if ( ! empty( $args ) ) {
			$term    = get_term_by( 'slug', $args, 'product_cat' );

			if ( ! empty( $term ) ) {
				$term_id = get_queried_object()->term_id ?? '';

				if ( $term->term_id !== $term_id ) {
					$children = get_term_children( $term->term_id, 'product_cat' );
					return in_array( $term_id, $children, true );
				} else {
					return is_product_category( $args );
				}
			}
		}

		return is_product_category();
	}
}
