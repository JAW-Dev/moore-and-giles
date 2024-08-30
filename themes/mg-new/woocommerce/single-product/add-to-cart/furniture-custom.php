<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php Objectiv\Site\Components\LeatherTemplate\FurnitureTabs::render(); ?>

	<?php foreach ( $attributes as $attribute_name => $options ) : ?>
		<?php if ( $attribute_name === 'pa_color' ): ?>
			<div class="selected-swatch-mobile">
				<?php echo wp_kses_post( Objectiv\Site\Components\LeatherTemplate\LeatherSample::render_selected_text( $attribute_name ) ); ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php else : ?>
		<table class="variations" cellspacing="0">
			<tbody>
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<?php if ( $attribute_name === 'pa_color' ): ?>
						<tr>
							<td class="value available-swatches">
								<div>
									<?php
									// Set the varation option on stack and if allowed backorder.
									$new_options = array();
									foreach ( $available_variations as $variation ) {
										$color = ! empty( $variation['attributes']['attribute_pa_color'] ) ? $variation['attributes']['attribute_pa_color'] : '';
										if ( $variation['backorders_allowed'] || $variation['stock_quantity'] >= 1 ) {
											$new_options[] = $color;
										}
									}

									wc_dropdown_variation_attribute_options( array(
										'options'   => $new_options,
										'attribute' => $attribute_name,
										'product'   => $product,
									) );
									echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
									?>
								</div>
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class="see-more-mobile">
			<span id="see-more-mobile-closed-text" class="see-more-mobile__closed-text" >See <span class="see-more-mobile-num"></span>More Options</span>
			<span id="see-more-mobile-opened-text" class="see-more-mobile__opened-text">See Fewer Options</span>
		</div>
		<?php
		foreach ( $attributes as $attribute_name => $options ) :
			if ( $attribute_name === 'pa_color' ) :
				new Objectiv\Site\Components\LeatherTemplate\LeatherSample();
				Objectiv\Site\Components\LeatherTemplate\LeatherSample::render_info_block( $attribute_name );
				Objectiv\Site\Components\LeatherTemplate\LeatherSample::render_info_modal();
				Objectiv\Site\Components\LeatherTemplate\LeatherSample::render_form_block();
			endif;
		endforeach;
		?>

		<div class="single_variation_wrap">
			<?php
				/**
				 * Hook: woocommerce_before_single_variation.
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
				 *
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * Hook: woocommerce_after_single_variation.
				 */
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php
	foreach ( $attributes as $attribute_name => $options ) :
		if ( $attribute_name === 'pa_color' ) :
			?>
			<div class="woocommerce-variation single_variation below">
				<div class="woocommerce-variation-availability">
					<p class="stock available-on-backorder"></p>
				</div>
			</div>
			<?php
		endif;
	endforeach;
	do_action( 'woocommerce_after_variations_form' );
	?>
</form>

<?php
foreach ( $attributes as $attribute_name => $options ) :
	if ( $attribute_name === 'pa_color' ) :
		Objectiv\Site\Components\LeatherTemplate\LeatherSample::render_form_modal();
	endif;
endforeach;
?>
