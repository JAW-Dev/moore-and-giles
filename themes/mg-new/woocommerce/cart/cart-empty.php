<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
<div class="sidebar-cart__container" id="mg_cart_wrap">
	<div class="empty-cart woocommerce">
		<div class="cart-header">
			<div class="inner-row">
				<button class="sidebar-cart__close">Continue Shopping</button>
				<?php do_action( 'woocommerce_before_cart' ); ?>
				<h2 class="empty-cart__heading">Your Cart</h2>
			</div>
		</div>
		<div class="empty-cart__body">
			<div class="inner-row">
				<h3>Oops There's Nothing Here</h3>
				<p>Your Cart is Currently Empty</p>
			</div>
		</div>
		<div class="cart-footer">
			<div class="inner-row">
				<button class="empty-cart__button sidebar-cart__close button button__orange cart-button sidebar-cart__button">Continue Shopping</button>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>
