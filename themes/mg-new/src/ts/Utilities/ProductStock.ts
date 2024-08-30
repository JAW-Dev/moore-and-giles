export class ProductStock {

	/**
	 * Init
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	init() {
		this.wishlistLink();
		this.addToCart();
	}

	/**
     * Togle the wishlist link
     *
     * @author Jason Witt
	 *
	 * @return void
	 */
	addToCart() {
		jQuery( document.body ).on('quick-view-displayed', () => {
			jQuery( document.body ).on( 'found_variation', ( event, variation ) => {
				const summary = jQuery('#jckqv_summary');

				summary.removeClass('out-of-stock');

				if (variation.is_in_stock === false) {
					summary.addClass('out-of-stock');
				}
			});
		});
	}

	/**
     * Togle the wishlist link
     *
     * @author Jason Witt
	 *
	 * @return void
	 */
	wishlistLink() {
		jQuery( document.body ).on('quick-view-displayed', () => {
			jQuery( document.body ).on( 'found_variation', ( event, variation ) => {
				const buttons = jQuery('.woocommerce_waitlist');

				if (variation.is_in_stock === false) {
					buttons.each((index, value) => {
						const button_id = jQuery(value).attr('data-product-id');

						if(Number(button_id) === variation.variation_id) {
							jQuery(value).removeClass('hide');
						} else {
							jQuery(value).addClass('hide');
						}
					});
				} else {
					buttons.each((index, value) => {
						const button_id = jQuery(value).attr('data-product-id');

						if(Number(button_id) !== variation.variation_id) {
							jQuery(value).addClass('hide');
						}
					});
				}

			});
		});
	}
}
