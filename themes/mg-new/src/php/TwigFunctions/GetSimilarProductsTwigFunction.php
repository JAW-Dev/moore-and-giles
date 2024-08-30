<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class GetSimilarProductsTwigFunction extends TwigFunction {
	/**
	 * Get Similar Products
	 *
	 * @param int $product_id The product ID.
	 *
	 * @return array
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 */
	public function action( $product_id ) {
		$product        = wc_get_product( $product_id );
		$product_data   = $product->get_data();
		$product_type   = $product->get_type();
		$product_skus   = [];
		$recomended_ids = [];
		$transient_name = 'skafos_recomendations_for_' . $product_id;
		$transient      = get_transient( $transient_name );

		if ( ! empty( $transient ) ) {
			return $transient;
		} else {
			if ( $product_type === 'variable' ) {
				$children = $product->get_children();

				foreach ( $children as $child_id ) {
					$child = wc_get_product( $child_id );

					if ( ! empty( $child ) ) {
						$child_data = $child->get_data();
						$child_sku  = $child_data['sku'];

						if ( ! empty( $child_sku ) ) {
							$product_skus[] = $child_sku;
						}
					}
				}
			} else {
				if ( ! empty( $product_data['sku'] ) ) {
					$product_skus[] = $product_data['sku'];
				}
			}

			try {
				if ( class_exists( '\Skafos\ProductRecommender' ) ) {
					$solution        = new \Skafos\ProductRecommender( 'product-recommender' );
					$recommendations = $solution->get_similar_products( 7, $product_skus );

					foreach ( $recommendations as $recommendation ) {
						$args = array(
							'post_type'  => 'product_variation',
							'meta_query' => array(
								array(
									'key'   => '_sku',
									'value' => $recommendation->id,
								)
							)
						);

						$the_post         = get_posts( $args );
						$recomendation_id = isset( $the_post[0] ) ? $the_post[0]->ID : '';

						if ( ! empty( $recomendation_id ) ) {
							$recomended_ids[] = $recomendation_id;
						}
					}
					set_transient( $transient_name, $recomended_ids, 4 * HOUR_IN_SECONDS );

					return $recomended_ids;
				} else {
					$wc_related = wc_get_related_products( $product_id, 9 );
					return $wc_related;
				}
			} catch ( \GuzzleHttp\Exception\ClientException $e ) {
				$wc_related = wc_get_related_products( $product_id, 9 );
				return $wc_related;
			}
		}
	}
}
