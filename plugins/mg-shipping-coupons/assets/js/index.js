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
		const inputField = jQuery('#objective_shipping_methods_select');
		const isChecked = jQuery('#objective_enable_shipping_coupon').attr('checked');
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
		jQuery(document.body).on( 'click', '#objective_enable_shipping_coupon', () => {
			const isChecked = jQuery('#objective_enable_shipping_coupon').attr('checked');
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
		const inputFields = jQuery('.objective_shipping_methods_select');
		const selectField = jQuery('.objective_shipping_methods_select select');

		inputFields.each((key, value) => {
			const isChecked = jQuery('#objective_enable_shipping_coupon').attr('checked');
			const selectFieldVal = jQuery('#objective_shipping_methods_select').val();
			if ( isChecked ) {
				jQuery(value).prop('disabled', false);
			} else {
				jQuery(value).prop('disabled', true);
			}
			jQuery(value).css('display', 'none');
			if (jQuery(value).hasClass('objective_shipping_methods_select_' + selectFieldVal) ) {
				if (selectFieldVal !== '' ) {
					jQuery(value).css('display', 'block');
				}
			} else {
				jQuery(value).css('display', 'none');
			}
		});
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
		jQuery(document.body).on('change', '#objective_shipping_methods_select', () => {
			inputFields.each((key, value) => {
				const selectFieldVal = jQuery('#objective_shipping_methods_select').val();
				if (jQuery(value).hasClass('objective_shipping_methods_select_' + selectFieldVal) ) {
					jQuery(value).css('display', 'block');
				} else {
					jQuery(value).css('display', 'none');
				}
			});
		});
	}
}

new shippingCoupons();