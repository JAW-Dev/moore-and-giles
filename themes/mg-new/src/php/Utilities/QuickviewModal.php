<?php
/**
 * Quickview Modal Get Images
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * QuickviewModal
 *
 * @author Jason Witt
 */
class QuickviewModal {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * New Tag.
	 *
	 * @author Jason Witt
	 *
	 * @param object $product The product.
	 *
	 * @return array
	 */
	public static function get_product_images( $product ) {
		$prod_images = array();
		$product_id  = $product->get_id();

		// !If is Varibale product/
		if ( $product->is_type( 'variable' ) ) {
			$request_id         = isset( $_REQUEST['product_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) ) : false;
			$ids                = explode( ':', $request_id );
			$product_id         = isset( $ids[1] ) ? $ids[1] : false;
			$product_variations = $product->get_available_variations();

			if ( ! empty( $product_variations ) ) {
				foreach ( $product_variations as $product_variation ) {
					if ( $product_id == $product_variation['variation_id'] && has_post_thumbnail( $product_variation['variation_id'] ) ) {
						$variation_thumbnail_id = get_post_thumbnail_id( $product_variation['variation_id'] );
						$img_src                = wp_get_attachment_image_src( $variation_thumbnail_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
						$img_thumb_src          = wp_get_attachment_image_src( $variation_thumbnail_id, 'thumbnail' );

						$prod_images[ $variation_thumbnail_id ]['slideId'][]     = '-' . $product_variation['variation_id'] . '-';
						$prod_images[ $variation_thumbnail_id ]['img_src']       = $img_src[0];
						$prod_images[ $variation_thumbnail_id ]['img_width']     = $img_src[1];
						$prod_images[ $variation_thumbnail_id ]['img_height']    = $img_src[2];
						$prod_images[ $variation_thumbnail_id ]['img_thumb_src'] = $img_thumb_src[0];
					}
				}
				$attachment_ids = explode( ',', get_post_meta( $product_id, 'variation_image_gallery', true ) );

				if ( ! empty( $attachment_ids ) ) {
					foreach ( $attachment_ids as $attachment_id ) {
						$img_src       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
						$img_thumb_src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

						$prod_images[ $attachment_id ]['slideId'][]     = '-0-';
						$prod_images[ $attachment_id ]['img_src']       = $img_src[0];
						$prod_images[ $attachment_id ]['img_width']     = $img_src[1];
						$prod_images[ $attachment_id ]['img_height']    = $img_src[2];
						$prod_images[ $attachment_id ]['img_thumb_src'] = $img_thumb_src[0];
					}
				}
			}
		} else {
			if ( has_post_thumbnail( $product_id ) ) {
				$img_id        = get_post_thumbnail_id( $product_id );
				$img_src       = wp_get_attachment_image_src( $img_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
				$img_thumb_src = wp_get_attachment_image_src( $img_id, 'thumbnail' );

				$prod_images[ $img_id ]['slideId'][]     = '-0-';
				$prod_images[ $img_id ]['img_src']       = $img_src[0];
				$prod_images[ $img_id ]['img_width']     = $img_src[1];
				$prod_images[ $img_id ]['img_height']    = $img_src[2];
				$prod_images[ $img_id ]['img_thumb_src'] = $img_thumb_src[0];
			} else {
				$prod_images[0]['slideId'][]     = '-0-';
				$prod_images[0]['img_src']       = woocommerce_placeholder_img_src();
				$prod_images[0]['img_width']     = 800;
				$prod_images[0]['img_height']    = 800;
				$prod_images[0]['img_thumb_src'] = woocommerce_placeholder_img_src();
			}

			// Additional Images.
			$attachment_ids   = \Iconic_WQV_Product::get_gallery_image_ids( $product );
			$attachment_count = count( $attachment_ids );

			if ( ! empty( $attachment_ids ) ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$img_src       = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
					$img_thumb_src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );

					$prod_images[ $attachment_id ]['slideId'][]     = '-0-';
					$prod_images[ $attachment_id ]['img_src']       = $img_src[0];
					$prod_images[ $attachment_id ]['img_width']     = $img_src[1];
					$prod_images[ $attachment_id ]['img_height']    = $img_src[2];
					$prod_images[ $attachment_id ]['img_thumb_src'] = $img_thumb_src[0];
				}
			}
		}

		return $prod_images;
	}
}
