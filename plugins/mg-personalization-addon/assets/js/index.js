/* global personalizationEnabled, personalizationTooltip, jQuery, jckqv_vars */

import tippy from 'tippy.js'

class personalization {

	/**
	 * Class Constructor
	 */
	constructor() {
		this.autoTab();
		this.backspace()
		this.checkbox();
		this.tooltip();
		this.showField();
		this.capitalize();
		jQuery(document.body).on('jckqv_open', () => {
			this.autoTab();
			this.backspace()
			this.checkbox();
			this.tooltip();
			this.jckqvOpenshowField();
			this.capitalize()
		});
	}

	/**
	 * Personalization Tooltip
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	tooltip() {
		const tooltip = document.querySelector('.personalization-tooltip');
		const instance = tippy(tooltip, {
			arrow: true,
			arrowType: 'round',
			duration: 0,
			animation: 'shift-away',
			theme: 'light',
			zIndex: '9999999',
			maxWidth: 300
		});

		let imageMarkup = '';
		let messageMarkup = '';

		// Bail if personalizationTooltip is not set.
		if (personalizationTooltip === null) {
			return;
		}

		// Bail if personalizationTooltip is not set.
		if (typeof personalizationTooltip === 'undefined') {
			return;
		}

		let message = personalizationTooltip.default.message;
		let image = null;

		// Set the default tooltip.
		if (personalizationTooltip.simple) {
			message = personalizationTooltip.simple.message;
			image = personalizationTooltip.simple.image;
		}

		if (message) {
			messageMarkup = `<div class="personalization-tooltip-text"><p>${message}</p></div>`;
		}

		if (image) {
			imageMarkup = `<div class="personaliztion-tooltip-image"><img src="${image}"/></div>`;
		}

		if (typeof instance.setContent === 'undefined') {
			return;
		}

		instance.setContent(messageMarkup);

		// Set the variants tooltips.
		if (personalizationTooltip.variations) {
			const varations = personalizationTooltip.variations;
			this.tooltipVariant(varations, instance, message);

			// Set the message for the selected variant.
			jQuery(document.body).on('woocommerce_variation_has_changed', () => {
				this.tooltipVariant(varations, instance, message);
			});
		} else if (personalizationTooltip.simple) {
			instance.setContent(messageMarkup + imageMarkup);
		}
	}

	/**
	 * Capitalize characters
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	capitalize() {
		const field = jQuery('.personalization_addon-addon-subfield');
		field.on('keyup', function (e) {
			jQuery(this).val(function (i, val) {
				return val.toUpperCase();
			})
		})
	}

	/**
	 * The tooltip variant
	 *
	 * @author Jason Witt
	 *
	 * @param [array]  $varations The varations array
	 * @param [object] $instance  The tooltip object
	 * @param [string] $message   The default message
	 *
	 * @return void
	 */
	tooltipVariant(varations, instance, message) {
		let image;
		let imageMarkup = '';
		let messageMarkup = '';

		if (typeof instance === 'undefined') {
			return;
		}

		Object.keys(varations).forEach(function (key) {
			const slug = varations[key].slug;
			const selected = jQuery('#pa_color').val()
			if (slug === selected && varations[key].message) {
				message = varations[key].message;
			}
			if (slug === selected && varations[key].image) {
				image = varations[key].image;
			}
		})

		if (message) {
			messageMarkup = `<div class="personalization-tooltip-text"><p>${message}</p></div>`;
		}

		if (image) {
			imageMarkup = `<div class="personaliztion-tooltip-image"><img src="${image}"/></div>`;
		}

		instance.setContent(messageMarkup + imageMarkup);
	}

	jckqvOpenshowField() {
		const productId = jQuery('.iconic-woothumbs-all-images-wrap').data('parentid');
		jQuery.ajax({
			type: 'post',
			url: mgData.adminAjax,
			data: {
				action: 'show_personaliztion',
				productId
			},
			success: response => {
				if (response) {
					const showingId = jQuery('.iconic-woothumbs-all-images-wrap').data('showing');
					this.showField(JSON.parse(response), 'quickview', showingId);
				}
			},
			fail: err => {
				console.error(`There was an error: ${err}`);
			}
		});
	}

	/**
	 * Show the personalization field
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	showField(data = null, type = null, showing = null) {

		if (data === null) {
			data = personalizationEnabled;
		}

		if (data === null) {
			return false;
		}

		const isSimple = 'simple' in data;
		const isVariable = 'variations' in data;
		const field = jQuery('.personalization-addon-field-container');

		if (isSimple && Object.keys(data.simple).length > 0) {
			jQuery(field).css('display', 'block');
		}

		// If the varaiants have enabled persinalization enabled.
		if (isVariable && Object.keys(data.variations).length > 0) {
			const varations = data.variations;

			this.variantShowField(varations, field, type, showing);
			const that = this;

			// After variation selection.
			jQuery(document.body).on('woocommerce_variation_has_changed', function () {
				that.variantShowField(varations, field, type, showing);
			})
		}
	}

	/**
	 * Variant Show field
	 *
	 * @author Jason Witt
	 *
	 * @param [array]  $varations The variations array.
	 * @param [object] $field     The feld object.
	 *
	 * @return void
	 */
	variantShowField(varations, field, type, showing) {
		if (type === 'quickview') {
			if (varations.includes(showing)) {
				jQuery(field).css('display', 'block');
			} else {
				jQuery(field).css('display', 'none');
			}

			jQuery('form.variations_form').on('show_variation', function (event, data) {

				if (typeof data.variation_id === 'number') {
					if (varations.includes(data.variation_id)) {
						jQuery(field).css('display', 'block');
					} else {
						setTimeout(() => {
							jQuery(field).css('display', 'none');
						}, 100);
					}
				}
			});
		} else {
			jQuery('form.variations_form').on('show_variation', function (event, data) {
				if (varations.includes(data.variation_id)) {
					jQuery(field).css('display', 'block');
				} else {
					jQuery(field).css('display', 'none');
				}
			});
		}

	}

	/**
	 * Autotab to the next field.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	autoTab() {
		const field = jQuery('.personalization_addon-addon-subfield');
		const maxLength = field.attr('maxLength');

		field.on('keyup', function (event) {
			const fieldvalue = (jQuery(this).val()) ? jQuery(this).val().toString() : '';
			const fieldLength = fieldvalue.length;
			const allowedCharacters = event.target.value.match(/^[0-9a-zA-Z]+$/);

			if (!allowedCharacters) {
				jQuery(this).val('');
				jQuery(this).focus();
				return;
			} else {
				if (jQuery(this).hasClass('last')) {
					jQuery(this).focus();
				} else {
					if (Number(fieldLength) == Number(maxLength)) {
						let $next = jQuery(this).next('.personalization_addon-addon-subfield');
						if ($next.length)
							jQuery(this).next('.personalization_addon-addon-subfield').focus();
						else
							jQuery(this).blur();
					}
				}
			}
		});
	}

	/**
	 * Backspace to previous field.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	backspace() {
		const field = jQuery('.personalization_addon-addon-subfield');
		field.on('keyup', function (e) {
			const fieldvalue = (jQuery(this).val()) ? jQuery(this).val().toString() : '';

			if (fieldvalue.length == 0 && e.which == 8) {
				if (jQuery(this).hasClass('first')) {
					jQuery(this).focus();
				} else {
					jQuery(this).prev(field).focus();
				}
			}
		});
	}

	/**
	 * Hide/Show the personalization fields.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	checkbox() {
		const checkbox = jQuery('.cart').find('.addon-field-wrapper.personalization .checkbox');
		jQuery.each(checkbox, function () {
			jQuery(this).on('change', function () {
				if (jQuery(this).is(':checked')) {
					jQuery(this).siblings('.addon-subfield-wrapper').find('label').addClass('hide');
					jQuery(this).siblings('.addon-subfield-wrapper').find('.text').addClass('show');
					jQuery(this).siblings('.help-icon').addClass('hide');
				} else {
					jQuery(this).siblings('.addon-subfield-wrapper').find('label').removeClass('hide');
					jQuery(this).siblings('.addon-subfield-wrapper').find('.text').removeClass('show');
					jQuery(this).siblings('.help-icon').removeClass('hide');
				}
			});
		});
	}
}

new personalization();
