declare let mgData: any;

export class LeatherSampleForm {
	varationList;
	selectedVariation;

	init() {
		if (jQuery('body').hasClass('standard-product-template') || jQuery('body').hasClass('custom-furniture-template')) {
			this.moveSwatches();
			this.moveSwatchesChoices();
			this.addFormSwatches();
			this.onValidation();
			this.formModal();
			this.swatchesChoicesClickHandler();
			this.afterValidateReinit();
		}
	}

	afterValidateReinit() {
		jQuery(document).on('gform_post_render', () => {
			const swatches = jQuery('.modal-sample-request .gfield_checkbox li input');
			let swatchIds = [];

			const choices = jQuery('#gform-footer-choices');
			const choices2 = jQuery('#gform-footer-choices2');

			choices.empty();
			choices2.empty();

			const hasError = jQuery('.validation_error');

			if (hasError.length !== 0) {
				swatches.each((index, swatch) => {
					const id = jQuery(swatch).attr('id');
					if ( jQuery(swatch).is( ':checked' ) ) {
						swatchIds.push(id);
					}
				})

				this.moveSwatchesChoices();
				this.moveSwatches();

				swatchIds.forEach((swatchId, index) => {
					const swatch  = jQuery(`#${swatchId}`);
					const heading = jQuery('.gform_footer__choices-heading');
					const name    = swatch.data('wvstooltip');

					heading.css('display', 'block');

					if ( swatch.is( ':checked' ) ) {
						swatch.addClass('is_checked');
					} else {
						swatch.removeClass('is_checked');
					}

					if ( swatch.hasClass('is_checked') ) {
						if (index % 2) {
							choices2.append(`<li>${name}</li>`);
						} else {
							choices.append(`<li>${name}</li>`);
						}
					}
				});

				this.swatchesChoicesClickHandler();
			}
		});
	}

	moveSwatches() {
		const formWrapper = jQuery('.modal-sample-request_wrapper');
		const headerWrapper = formWrapper.find('.gform_heading');
		const targetSwatches = formWrapper.find('.target-swatches');
		const swatchesWrap = formWrapper.find('.gfield_checkbox');

		headerWrapper.append(targetSwatches);
		swatchesWrap.addClass('variable-items-wrapper');
	}

	moveSwatchesChoices() {
		const formWrapper = jQuery('.modal-sample-request_wrapper');
		const targetSwatches = formWrapper.find('.target-swatches');
		const choices = formWrapper.find('#swatch-choices');
		const headerWrapper = formWrapper.find('.gform_heading');

		setTimeout(() => {
			headerWrapper.append(choices)
			choices.insertAfter(targetSwatches);
		}, 500);
	}

	addFormSwatches() {
		const checkboxes = jQuery('.gfield_checkbox li');

		// Remove the faux-swatch on the label.
		if (jQuery('.gfield_label').siblings('.faux-swatch').length) {
			jQuery('.gfield_label').siblings('.faux-swatch').remove();
		}

		checkboxes.each((index, checkbox) => {
			const imageSrc = jQuery(checkbox).find('input').data('src');
			const fauxSwatch = jQuery(checkbox).find('.faux-swatch');

			fauxSwatch.css('background-image', `url(${imageSrc})`);
		});
	}

	onValidation() {
		jQuery(document).on('gform_post_render', () => {
			this.addFormSwatches();
			this.moveSwatches();
		})
	}

	formModal() {
		(<any>jQuery('.request-sample__trigger')).modaal({
			custom_class: 'leather-sample-request',
			fullscreen: true,
			after_open: () => {
				jQuery('#modaal-close').prependTo('.modaal-content-container');
			}
		});
	}

	swatchesChoicesClickHandler() {
		const choiceWrap = jQuery('#gform_footer-choices-wrap');
		const choices = jQuery('#gform-footer-choices');
		const choices2 = jQuery('#gform-footer-choices2');
		const swatches = jQuery('.modal-sample-request .gfield_checkbox li input');
		const heading = jQuery('.gform_footer__choices-heading');
		let selectedCount = 0;
		let selected2Count = 0;

		swatches.each((index, swatch) => {
			const theSwatch = jQuery(swatch);

			theSwatch.on('click', function() {
				const name = theSwatch.data('wvstooltip');
				const selected = jQuery('#gform-footer-choices li');
				const selected2 = jQuery('#gform-footer-choices2 li');
				const checkbox = jQuery( this );

				heading.css('display', 'block');

				choiceWrap.toggleClass('odd');

				if ( checkbox.is( ':checked'  ) ) {
					theSwatch.addClass('is_checked');
				} else {
					theSwatch.removeClass('is_checked');
				}

				if ( theSwatch.hasClass('is_checked') ) {
					if (choiceWrap.hasClass('odd')) {
						choices.append(`<li>${name}</li>`);
						selectedCount++;
					} else {
						choices2.append(`<li>${name}</li>`);
						selected2Count++
					}

				} else {
					selected.each((index, select) => {
						if (jQuery(select).text() === name) {
							jQuery(select).remove();
							selectedCount--;
						}
					})
					selected2.each((index, select) => {
						if (jQuery(select).text() === name) {
							jQuery(select).remove();
							selected2Count--;
						}
					})
				}

				const totalChoices = selectedCount + selected2Count;

				if (totalChoices % 2 === 0) {
					if (choiceWrap.hasClass('odd')) {
						choiceWrap.toggleClass('odd');
					}
				} else {
					if (!choiceWrap.hasClass('odd')) {
						choiceWrap.toggleClass('odd');
					}
				}

				if (selectedCount <= 0 && selected2Count > 0) {
					jQuery('#gform-footer-choices').append(jQuery('#gform-footer-choices2 li:nth-child(odd)'));
				}
			})
		})
	}
}
