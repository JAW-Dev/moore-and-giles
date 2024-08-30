/* global jQuery, mgShippingCoupons */

class shippingCoupons {

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
		this.onAppliedCoupon();
	}

	onAppliedCoupon() {
		const that = this;
		jQuery( document ).ajaxComplete(function( event, xhr, settings ) {

			if( settings.url.indexOf('cfw_apply_coupon') > -1 ) {
				console.log('cfw_apply_coupon'); // eslint-disable-line

			}

			if( settings.url.indexOf('update_checkout') > -1 ) {
				console.log('update_checkout'); // eslint-disable-line

			}

			if( settings.url.indexOf('update_payment_method') > -1 ) {
				console.log('update_payment_method'); // eslint-disable-line
				setTimeout(() => {
					that.ajaxHandler();
				}, 500);
			}
		});
	}

	ajaxHandler() {
		const selectedShipping = jQuery('.shipping_method');
		const cfwCoupon = jQuery('#cfw-promo-code').val();

		// Bail if there is no coupon code set.
		if (!cfwCoupon) {
			return;
		}

		const data = {
			action : 'check_shipping',
			coupon_code: cfwCoupon
		};

		jQuery.ajax({
			url : mgShippingCoupons.ajax_url,
			type : 'post',
			data : data,
			success : function(response) {

				if ( response ) {
					jQuery(selectedShipping).each((key, value) => {
						jQuery(value).prop('checked', false);
						if (jQuery(value).val() === response) {
							console.log(response, jQuery(value).val()); // eslint-disable-line
							jQuery(value).prop('checked', true);
						}
					});
				}
			},
			error: (error) => {
				console.error('error', error); // eslint-disable-line
			}
		});
	}
}

// new shippingCoupons();