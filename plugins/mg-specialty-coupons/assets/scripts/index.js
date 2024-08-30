/* global document, jQuery, mgShippingCoupons */

const ajaxHandler = () => {
	const cfwCoupon = jQuery('#cfw-promo-code').val();

	// Bail if there is no coupon code set.
	if (!cfwCoupon) {
		return;
	}

	const data = {
		action: 'check_shipping',
		coupon_code: cfwCoupon
	};

	jQuery.ajax({
		url: mgShippingCoupons.ajax_url,
		type: 'post',
		data,
		success(response) {
			const selectedShipping = jQuery('.shipping_method');
			jQuery(selectedShipping).each((key, value) => {
				jQuery(value).prop('checked', false);

				if (jQuery(value).val() === response) {
					jQuery(value).prop('checked', true);
				}
			});
		},
		error: error => {
			console.error('error', error); // eslint-disable-line
		}
	});
};

const onAppliedCoupon = () => {
	jQuery(document).ajaxComplete((event, xhr, settings) => {
		if (settings.url.indexOf('cfw_apply_coupon') > -1) {
			console.log('cfw_apply_coupon'); // eslint-disable-line
			setTimeout(() => {
				ajaxHandler();
			}, 3000);
		}

		if (settings.url.indexOf('update_checkout') > -1) {
			console.log('update_checkout'); // eslint-disable-line
		}

		if (settings.url.indexOf('update_payment_method') > -1) {
			console.log('update_payment_method'); // eslint-disable-line
		}
	});
};

onAppliedCoupon();
