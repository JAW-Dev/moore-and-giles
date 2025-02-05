<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden quantity-counter__input">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} else {
	/* translators: %s: Quantity. */
	$labelledby = ! empty( $args['product_name'] ) ? sprintf( __( '%s quantity', 'woocommerce' ), strip_tags( $args['product_name'] ) ) : '';
	?>
	<div class="quantity-wrapper">
		<div class="quantity-counter">
			<div class="quantity quantity-counter__input">
				<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
				<div class="quantity-amount-button quantity-counter__buttons">
					<button class="minus"><svg xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 384 512" fill="currentColor"><path d="M368 224H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h352c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"/></svg></button>
					<div class="input-text-wrap">
						<input
							type="number"
							id="<?php echo esc_attr( $input_id ); ?>"
							class="input-text qty text"
							step="<?php echo esc_attr( $step ); ?>"
							min="<?php echo esc_attr( $min_value ); ?>"
							max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
							name="<?php echo esc_attr( $input_name ); ?>"
							value="<?php echo esc_attr( $input_value ); ?>"
							title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
							size="4"
							pattern="<?php echo esc_attr( $pattern ); ?>"
							inputmode="<?php echo esc_attr( $inputmode ); ?>"
							aria-labelledby="<?php echo esc_attr( $labelledby ); ?>" />
					</div>
					<button class="plus"><svg xmlns="http://www.w3.org/2000/svg" width="12" viewBox="0 0 384 512" fill="currentColor"><path d="M368 224H224V80c0-8.84-7.16-16-16-16h-32c-8.84 0-16 7.16-16 16v144H16c-8.84 0-16 7.16-16 16v32c0 8.84 7.16 16 16 16h144v144c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V288h144c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16z"/></svg></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
