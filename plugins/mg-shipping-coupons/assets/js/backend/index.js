/* global jQuery */

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
		this.toggleShippingMethods();
		this.toggleServices();
		this.servicesSelectHandler();
	}

	/**
	 * Toggle Shipping Methods Select
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	toggleShippingMethods() {
		const inputField = jQuery('#objectiv_shipping_coupons_method');
		const isChecked = jQuery('#objectiv_shipping_coupons_enable').attr('checked');
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
		jQuery(document.body).on( 'click', '#objectiv_shipping_coupons_enable', () => {
			const isChecked = jQuery('#objectiv_shipping_coupons_enable').attr('checked');
			jQuery(inputField).each((key, value) => {
				if ( isChecked ) {
					jQuery(value).prop('disabled', false);
				} else {
					jQuery(value).prop('disabled', true);
				}
			});
		});
	}

	/**
	 * Togle services select field
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	toggleServices() {
		const inputFields = jQuery('.objectiv_shipping_coupons_service');
		const selectField = jQuery('.objectiv_shipping_coupons_method select');
		const selectFieldVal = jQuery('#objectiv_shipping_coupons_method').val();
		const hidden = jQuery('.no-shipping-services');

		inputFields.each((key, value) => {
			const isChecked = jQuery('#objectiv_shipping_coupons_enable').attr('checked');
			if ( isChecked ) {
				jQuery(value).prop('disabled', false);
			} else {
				jQuery(value).prop('disabled', true);
			}
			jQuery(value).css('display', 'none');
			if (jQuery(value).hasClass('objectiv_shipping_coupons_service_' + selectFieldVal) ) {
				if (selectFieldVal !== '' ) {
					jQuery(value).css('display', 'block');
				}
			} else {
				jQuery(value).css('display', 'none');
			}
		});

		hidden.each(function(index, value) {
			if(hidden.attr('name') !== 'objectiv_shipping_coupons_service_' + selectFieldVal ) {
				jQuery(value).attr('disabled', true);
			} else {
				jQuery(value).attr('disabled', false);
			}
		})

		this.enabledClickHandler(selectField);
		this.methodsSelectHandler(inputFields);
	}

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
	methodsSelectHandler(inputFields) {
		jQuery(document.body).on('change', '#objectiv_shipping_coupons_method', () => {
			const selectFieldVal = jQuery('#objectiv_shipping_coupons_method').val();
			const hidden = jQuery('.no-shipping-services');

			inputFields.each((key, value) => {
				const service = jQuery('.shipping-service-select');
				service.each((index, value) => {
					jQuery(value).attr('value', '');
				})
				if (jQuery(value).hasClass('objectiv_shipping_coupons_service_' + selectFieldVal) ) {
					jQuery(value).css('display', 'block');
				} else {
					jQuery(value).css('display', 'none');
				}
			});

			hidden.each(function(index, value) {
				if(hidden.attr('name') !== 'objectiv_shipping_coupons_service_' + selectFieldVal ) {
					jQuery(value).attr('disabled', true);
				} else {
					jQuery(value).attr('disabled', false);
				}
			})
		});
	}

	/**
	 *Services select handler
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param [object] $inputfield The target input field.
	 *
	 * @return void
	 */
	servicesSelectHandler() {
		jQuery(document.body).on('change', '.shipping-service-select', (e) => {
			const selectValue = e.target;
			const hidden = jQuery('.no-shipping-services');

			hidden.each(function(index, value) {
				let hiddenName = jQuery(value).attr('name');
				console.log('OG name', hiddenName); // eslint-disable-line
				hiddenName.replace(/disabled-/g, '');
				jQuery(value).attr('name', hiddenName);
				console.log('Reset name', jQuery(value).attr('name')); // eslint-disable-line

				if(jQuery(selectValue).val() !== '') {
					jQuery(value).attr('name', 'disabled-' + hiddenName);
				}
				console.log('New name', jQuery('.no-shipping-services').attr('name')); // eslint-disable-line
			})
		});
	}
}

new shippingCoupons();