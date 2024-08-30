<?php

class MG_Virtual_Variants {
	public function __construct() {
		add_filter( 'shopp_themeapi_product_variations', array( $this, 'non_variant_variations' ), 1000, 3 );
	}

	public function get_virtual_variations( $shopp_product ) {
		$has_no_actual_variants = ! Shopp::str_true( $shopp_product->variants );
		$has_last_name          = shopp( $shopp_product, 'has-last-name' );

		if ( $has_no_actual_variants && $has_last_name ) {
			$first_name = shopp( $shopp_product, 'get-first-name' );

			$products = get_posts(
				array(
					'post_type'        => 'shopp_product',
					'numberposts'      => -1,
					'fields'           => 'ids',
					'suppress_filters' => true,
					'tax_query'        => array(
						array(
							'taxonomy' => ProductTag::$taxon,
							'field'    => 'name',
							'terms'    => $first_name,
						),
					),
				)
			);

			return $products;
		} else {
			return false;
		}
	}

	function non_variant_variations( $result, $initial_options, $original_product ) {
		$virtual_variations = $this->get_virtual_variations( $original_product );

		if ( $virtual_variations ) {
			if ( count( $virtual_variations ) < 2 ) {
				return '';
			}

			$select_options = [];

			foreach ( $virtual_variations as $variation_id ) {
				$variation = new ShoppProduct( $variation_id, 'id' );
				$variation->load_data( array( 'prices' ) );

				$variant_price       = shopp( $variation, 'get-price', 'money=off' );
				$original_price      = shopp( $original_product, 'get-price', 'money=off' );
				$has_different_price = $variant_price !== $original_price;

				if ( $has_different_price ) {
					if ( $variant_price < $original_price ) {
						$price_differential = sprintf( '(%s%s)', '-', Shopp::money( $original_price - $variant_price ) );
					} else {
						$price_differential = sprintf( '(%s%s)', '+', Shopp::money( $variant_price - $original_price ) );
					}
				} else {
					$price_differential = '';
				}

				$selected                           = $original_product->id == $variation->id ? 'selected' : '';
				$select_options[ $variation->name ] = "<option value='" . shopp( $variation, 'get-url' ) . "' $selected>" . shopp( $variation, 'get-last-name' ) . " $price_differential</option>";

				unset( $variation );
			}

			ksort( $select_options );

			$string = '';

			$defaults = array(
				'before_menu' => '',
				'after_menu'  => '',
				'label'       => 'on',
			);

			$options = array_merge( $defaults, $initial_options );

			if ( ! empty( $options['before_menu'] ) ) {
				$string .= $options['before_menu'] . "\n";
			}
			if ( Shopp::str_true( $options['label'] ) ) {
				$string .= '<label for="product-options' . (int) $original_product->id . '">' . Shopp::esc_html__( 'Colors' ) . ': </label> ' . "\n";
			}

			$string .= '<select name="products[' . (int) $original_product->id . '][price]" id="product-options' . (int) $original_product->id . '" class="non-variant-selector product' . (int) $original_product->id . ' options">';
			foreach ( $select_options as $so ) {
				$string .= $so . PHP_EOL;
			}
			$string .= '</select>';

			$string .= "
			<script type='text/javascript'>
			jQuery('.non-variant-selector').change( function() {
			    window.location = jQuery(this).val();
			} );
			</script>";

			if ( ! empty( $options['after_menu'] ) ) {
				$string .= $options['after_menu'] . "\n";
			}

			$result = $string;

			unset( $string );
		} else {

		}

		return $result;
	}
}
