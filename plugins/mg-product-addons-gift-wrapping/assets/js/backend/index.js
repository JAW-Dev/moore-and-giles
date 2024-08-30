/* global jQuery */

class giftWrappingCoupons {

	/**
	 * constructor
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	constructor() {
		this.init()
	}

	/**
	 * Init
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	init() {
		this.toggleBoxSizes();
	}

	/**
	 * Toggle Shipping Methods Select
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	toggleBoxSizes() {
		const inputField = jQuery('#objectiv_gift_wrapping_coupons_box');
		const isChecked = jQuery('#objectiv_gift_wrapping_coupons_enable').attr('checked');
		if ( isChecked ) {
			jQuery(inputField).prop('disabled', false);
		} else {
			jQuery(inputField).prop('disabled', true);
		}
		this.enabledClickHandler(inputField);
	}

	/**
	 * Enable checkbox click handler
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param [object] $inputfield The target input field.
	 *
	 * @return void
	 */
	enabledClickHandler(inputField) {
		jQuery(document.body).on( 'click', '#objectiv_gift_wrapping_coupons_enable', () => {
			const isChecked = jQuery('#objectiv_gift_wrapping_coupons_enable').attr('checked');
			jQuery(inputField).each((key, value) => {
				if ( isChecked ) {
					jQuery(value).prop('disabled', false);
				} else {
					jQuery(value).prop('disabled', true);
				}
			});
		});
	}
}

new giftWrappingCoupons();