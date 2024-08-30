<?php
/**
 * Cart item data (when outputting non-flat)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-item-data.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 	2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="variation">
	<?php foreach ( $item_data as $data ) : ?>
		<?php if ( $data['key'] === 'Gift Wrapping' ) : ?>
			<span class="<?php echo sanitize_html_class( 'variation-' . $data['key'] ); ?>">Includes <?php echo wp_kses_post( $data['key'] ); ?></span><br/>
		<?php elseif ( $data['key'] === 'Last Chance' ) :?>
			<span class="<?php echo sanitize_html_class( 'variation-' . $data['key'] ); ?>">Final Sale**</span><br/>
		<?php else : ?>
			<span class="<?php echo sanitize_html_class( 'variation-' . $data['key'] ); ?>"><?php echo wp_kses_post( $data['key'] ); ?>: <?php echo wp_kses_post( $data['display'] ); ?></span><br/>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
