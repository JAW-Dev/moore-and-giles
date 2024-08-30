<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="sidebar-cart__container" id="mg_cart_wrap">
	<div class="woocommerce">
		<div class="cart-header">
			<div class="inner-row">
				<button class="sidebar-cart__close">Continue Shopping</button>

				<?php do_action( 'woocommerce_before_cart' ); ?>
			</div>
		</div>

		<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
			<div class="inner-row">
				<div class="woocommerce woocommerce-cart-form__contents">
					<!-- Shim. This fools the WooCommerce native cart script into working but prevents it from doing bad stuff. -->
				</div>
				<?php do_action( 'woocommerce_before_cart_table' ); ?>
				<div class="shop_table shop_table_responsive cart mg-woocommerce-cart-form__contents">
					<div class="cart mg-woocommerce-cart-form__contents">
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>
						<?php
						$addon_items = ( function_exists( 'get_field' ) ) ? obj_get_acf_field( 'woo_mg_products_product_addons', 'option' ) : null;
						$addon_array = array();

						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

							if ( ! empty( $addon_items ) && is_array( $addon_items ) ) {
								foreach ( $addon_items as $addon_item ) {
									$addon_array[] = ( isset( $addon_item['product'] ) ) ? $addon_item['product'] : '';
								}
							}

							if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
								?>
								<div id="cart-product-<?php echo esc_attr( $product_id ); ?>" class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item cart__item product', $cart_item, $cart_item_key ) ); ?>">

									<div class="cart__content">
										<div class="product-thumbnail cart__thumbnail">
											<?php
											$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
											if ( ! $product_permalink ) {
												echo wp_kses_post( $thumbnail );
											} else {
												printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), wp_kses_post( $thumbnail ) );
											}
											?>
										</div>
										<div class="cart__summary">
											<div class="product-remove cart__remove">
												<?php
												// Updated to allow Ajax deletion in the sidebar cart.
												// @codingStandardsIgnoreLine
												echo apply_filters(
													'woocommerce_cart_item_remove_link',
													sprintf(
														'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_item_key="%s"></a>',
														esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
														esc_html__( 'Remove this item', 'deepsoul' ),
														esc_attr( $product_id ),
														esc_attr( $_product->get_sku() ),
														esc_attr( $cart_item_key )
													),
													$cart_item_key
												);
												?>
											</div>
											<div class="product-name cart__name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
												<?php
												if ( ! $product_permalink ) {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;' );
												} else {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title() ), $cart_item, $cart_item_key ) );
												}
												?>
											</div>
											<div class="cart-item-meta">
												<?php
												// Meta data.
												echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore
												echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name_addon', '', $cart_item, $cart_item_key ) );
												if ( ! in_array( $cart_item['product_id'], $addon_array, true ) ) :
													do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
												endif;

												// Backorder notification.
												if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
													$backorder_text = Objectiv\Site\Utilities\StockAvailability::stock_text( array( 'availability' => '', 'class' => 'available-on-backorder' ), $_product );
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . $backorder_text['availability'] . '</p>' ) );
												}
												?>

												<?php if ( ! in_array( $cart_item['product_id'], $addon_array, true ) && function_exists( 'objectiv_render_cart_addons' ) ) :
													objectiv_render_cart_addons( $product_id, $cart_item );
												endif; ?>

												<div class="cart-item-bottom">
													<?php if ( ! in_array( $cart_item['product_id'], $addon_array, true ) ) : ?>
														<?php
														if ( $_product->is_sold_individually() || $_product->get_stock_quantity() === 1 ) {
															$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
														} else {
															$product_quantity = woocommerce_quantity_input(
																array(
																	'input_name'   => "cart[{$cart_item_key}][qty]",
																	'input_value'  => $cart_item['quantity'],
																	'max_value'    => $_product->get_max_purchase_quantity(),
																	'min_value'    => '0',
																	'product_name' => $_product->get_name(),
																),
																$_product,
																false
															);
														}

														echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
														?>
													<?php endif; ?>

													<div class="product-price cart__price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
														<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php
							}
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>

						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</div>
				</div>
				<?php do_action( 'woocommerce_after_cart_table' ); ?>

				<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce', false ); ?>
			</div>
		</form>

		<div class="cart-footer">
			<div class="inner-row">
				<?php
				$cart_message = ( function_exists( 'get_field' ) ) ? obj_get_acf_field( 'woo_custom_cart_message', 'option' ) : '';
				foreach ( WC()->cart->get_cart() as $cart_item ) :
					$has_personalization = isset( $cart_item['product_addons']['addons']['addon-personalization'] ) ? true : false;
					if ( $has_personalization ) :
						 if ( $cart_message && $has_personalization ) :
							?>
							<div class="cart-message">
								<?php echo esc_html( $cart_message ); ?>
							</div>
							<?php
						endif;
						break;
					endif;
				endforeach;
				?>

				<div class="cart-collaterals">
					<?php  do_action( 'woocommerce_cart_collaterals' ); ?>
				</div>

				<?php if ( WC()->cart->get_cart_contents_count() == 0 ): ?>
					<div class="cart-button"></div>
				<?php else: ?>
					<div class="cart-button">
						<a href="<?php echo wc_get_checkout_url(); ?>" class="button button__orange cart-button sidebar-cart__button">Proceed to checkout</a>
					</div>
				<?php endif; ?>

				<?php do_action( 'woocommerce_after_cart' ); ?>
			</div>
		</div>
	</div>
</div>
