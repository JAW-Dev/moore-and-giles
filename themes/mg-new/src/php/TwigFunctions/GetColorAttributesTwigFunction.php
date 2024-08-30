<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class GetColorAttributesTwigFunction extends TwigFunction {
	/**
	 * Get Similar Products
	 *
	 * @param int $product_id The product ID.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function action( $product_id ) {

		// Bail if product ID isn't set.
		if ( empty( $product_id ) ) {
			return array();
		}

		$woo_product = wc_get_product( $product_id );
		$attributes  = ! empty( $woo_product ) && method_exists( $woo_product, 'get_attributes' ) ? $woo_product->get_attributes() : '';
		$size_count  = '';
		$color_count = '';
		$style_count = '';

		if ( ! empty( $attributes ) ) {
			if ( isset( $attributes['pa_color'] ) ) {
				/** @var \WC_Product_Attribute $colors */
				$colors = $attributes['pa_color'];

				$color_count = method_exists( $colors, 'get_options' ) ? count( $colors->get_options() ) : 0;

				if ( $color_count > 1 ) {
					if ( ! empty( $product->custom['color_count'] ) ) {
						$product->custom['color_count'] = $color_count;
					}
				}
			}

			if ( isset( $attributes['size'] ) ) {
				/** @var \WC_Product_Attribute $sizes */
				$sizes = $attributes['size'];

				$size_count = method_exists( $sizes, 'get_options' ) ? count( $sizes->get_options() ) : 0;

				if ( $size_count > 1 ) {
					if ( ! empty( $product->custom['size_count'] ) ) {
						$product->custom['size_count'] = $size_count;
					}
				}
			}

			if ( isset( $attributes['style'] ) ) {
				/** @var \WC_Product_Attribute $styles */
				$styles = $attributes['style'];

				$style_count =  method_exists( $styles, 'get_options' ) ? count( $styles->get_options() ) : 0;

				if ( $style_count > 1 ) {
					if ( ! empty( $product->custom['style_count'] ) ) {
						$product->custom['style_count'] = $style_count;
					}
				}
			}

			return array(
				'color_count' => $color_count,
				'size_count'  => $size_count,
				'style_count' => $style_count,
			);
		}

		return array();
	}
}
