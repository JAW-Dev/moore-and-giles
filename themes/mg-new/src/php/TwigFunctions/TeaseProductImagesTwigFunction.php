<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;
use \Objectiv\Site\Factories\TwigFunctionFactory;

/**
 * Get the tease product images
 */
class TeaseProductImagesTwigFunction extends TwigFunction {

	/**
	 * Action
	 *
	 * @param object $args The arguments.
	 *
	 * @return object
	 */
	public function action( $args ) {
		$product_id    = $args['product']->id;
		$_product      = wc_get_product( $product_id );
		$parent_id     = $_product->get_parent_id();

		$cover_image = ! empty( $args['cover_image'] ) ? $args['cover_image'] : '';
		$hover_image = ! empty( $args['hover_image'] ) ? $args['hover_image'] : '';

		if ( empty( $cover_image ) && $parent_id > 0 ) {
			$parent_id   = $_product->get_parent_id();
			$parent      = wc_get_product( $parent_id );

			if ( $parent ) {
				$cover_image = new \Timber\Image( $parent->get_image_id(), 'all' );
			}
		}

		if ( empty( $hover_image ) && $parent_id > 0 ) {
			$parent       = wc_get_product( $parent_id );

			if ( $parent ) {
				$post_gallery = $parent->get_gallery_image_ids();
				$hover_image  = ! empty( $post_gallery[0] ) ? $post_gallery[0] : '';
			}
		}

		$images              = new \stdClass();
		$images->cover_image = ! empty( $cover_image ) ? $cover_image : '';
		$images->hover_image = ! empty( $hover_image ) ? $hover_image : '';

		return $images;
	}
}
