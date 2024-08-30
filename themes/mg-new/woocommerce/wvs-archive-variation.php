<?php
	defined( 'ABSPATH' ) or die( 'Keep Silent' );

	$attributes = $product->get_variation_attributes();

	$attribute_keys       = array_keys( $attributes );
	$get_variations       = count( $product->get_children() ) <= apply_filters( 'woo_variation_swatches_archive_ajax_variation_threshold', 30, $product );
	$available_variations = $get_variations ? $product->get_available_variations() : false;
	$product              = $args['product'];

	if ( empty( $available_variations ) && false !== $available_variations ) {
		return;
	}

	$show_clear        = wc_string_to_bool( woo_variation_swatches()->get_option( 'show_clear_on_archive' ) );
	$catalog_mode      = wc_string_to_bool( woo_variation_swatches()->get_option( 'enable_catalog_mode' ) );
	$catalog_attribute = wc_variation_attribute_name( woo_variation_swatches()->get_option( 'catalog_mode_attribute' ) );
?>

<div class="variations_form wvs-archive-variation-wrapper" data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
    <ul class="variations">
		<?php foreach ( $attributes as $attribute_name => $options ) :
			if ( $attribute_name !== 'pa_color' ) {
				continue;
			}

			$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );

			if ( isset( $_GET['fwp_color_family'] ) ) {
				$color_family = $_GET['fwp_color_family'];

				/** @var \WC_Product_Data_Store_CPT $data_store */
				$data_store   = \WC_Data_Store::load( 'product' );

				if ( ! $product->is_type( 'variable') ) {
					continue;
				}

				$variations  = $product->get_available_variations();

				foreach( $variations as $variation ) {
					if ( isset( $variation['attributes']['attribute_pa_color-family'] ) && $variation['attributes']['attribute_pa_color-family'] == $color_family ) {
						$selected = $variation['attributes']['attribute_pa_color'];
						break;
					}
				}
			}

			if ( $catalog_mode ) {
				$product_settings = (array) get_post_meta( $product->get_id(), '_wvs_product_attributes', true );
				if ( isset( $product_settings[ 'catalog_attribute' ] ) && ! empty( $product_settings[ 'catalog_attribute' ] ) ) {
					$catalog_attribute = trim( $product_settings[ 'catalog_attribute' ] );
				}
				if ( $catalog_attribute == $attribute_name ) {
					echo '<li>';
					wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected, 'is_archive' => true ) );
					echo '</li>';
				}
			} else {
				echo '<li>';
				wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected, 'is_archive' => true ) );
				echo '</li>';
			}
		endforeach;


			if ( $show_clear && ! $catalog_mode ):
				echo apply_filters( 'woocommerce_reset_variations_link', '<li class="reset_variations woo_variation_swatches_archive_reset_variations"><a href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a></li>' );
			endif;
		?>
    </ul>
</div>

