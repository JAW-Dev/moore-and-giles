/* global document, jQuery */

/**
 *Methods select handler
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @param [object] $inputfield The target input field.
 *
 * @return void
 */
const methodsSelectHandler = inputFields => {
	jQuery(document.body).on('change', '#objectiv_shipping_coupons_method', () => {
		const selectFieldVal = jQuery('#objectiv_shipping_coupons_method').val();
		const hidden = jQuery('.no-shipping-services');

		inputFields.each((key, value) => {
			const service = jQuery('.shipping-service-select');
			service.each((index, item) => {
				jQuery(item).attr('value', '');
			});
			if (jQuery(value).hasClass(`objectiv_shipping_coupons_service_${selectFieldVal}`)) {
				jQuery(value).css('display', 'block');
			} else {
				jQuery(value).css('display', 'none');
			}
		});

		hidden.each((index, value) => {
			if (hidden.attr('name') !== `objectiv_shipping_coupons_service_${selectFieldVal}`) {
				jQuery(value).attr('disabled', true);
			} else {
				jQuery(value).attr('disabled', false);
			}
		});
	});
};

/**
 * Togle services select field
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @return void
 */
const toggleServices = () => {
	const inputFields = jQuery('.objectiv_shipping_coupons_service');
	const selectFieldVal = jQuery('#objectiv_shipping_coupons_method').val();
	const hidden = jQuery('.no-shipping-services');

	inputFields.each((key, value) => {
		jQuery(value).css('display', 'none');
		if (jQuery(value).hasClass(`objectiv_shipping_coupons_service_${selectFieldVal}`)) {
			if (selectFieldVal !== '') {
				jQuery(value).css('display', 'block');
			}
		} else {
			jQuery(value).css('display', 'none');
		}
	});

	hidden.each((index, value) => {
		if (hidden.attr('name') !== `objectiv_shipping_coupons_service_${selectFieldVal}`) {
			jQuery(value).attr('disabled', true);
		} else {
			jQuery(value).attr('disabled', false);
		}
	});

	methodsSelectHandler(inputFields);
};

/**
 * Services select handler
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @param [object] $inputfield The target input field.
 *
 * @return void
 */
const servicesSelectHandler = () => {
	jQuery(document.body).on('change', '.shipping-service-select', e => {
		const selectValue = e.target;
		const hidden = jQuery('.no-shipping-services');

		hidden.each((index, value) => {
			const hiddenName = jQuery(value).attr('name');
			hiddenName.replace(/disabled-/g, '');
			jQuery(value).attr('name', hiddenName);

			if (jQuery(selectValue).val() !== '') {
				jQuery(value).attr('name', `disabled-${hiddenName}`);
			}
		});
	});
};

const toggleShippingFields = () => {
	toggleServices();
	servicesSelectHandler();
};

export default toggleShippingFields;
