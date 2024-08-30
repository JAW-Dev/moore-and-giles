// Import Modules
import 'modaal';

import { LeatherSampleForm } from './LeatherSampleForm';

declare let mgData: any;

export class CustomFurniture {
	swatches;
	tabs;
	backordered;
	available;

	init() {
		if (jQuery('body').hasClass('custom-furniture-template')) {
			this.swatches = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li');
			this.tabs = jQuery('.furniture-tabs__tab');

			this.setAvailabilityClass();

			this.backordered = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li.backordered');
			this.available = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li:not(.backordered)');

			this.maybeDisableInStock();
			this.setDefaultSwatch();
			this.directLink();
			this.toggleIndicators();
			this.toggleSwatches();
			this.populateLeatherSample();
			this.swatchesClickSelectedHandler();
			this.seeMoreSwatchesClickHandler();
			this.seeMoreSwatchesCount();
			this.swatchHoverSelectedHandler();
			this.resetMobileDesktop();
			this.addStockStyles();
			new LeatherSampleForm().init();
		}
	}

	setAvailabilityClass() {
		const formData = jQuery('.variations_form').data('product_variations');
		const json = JSON.stringify(formData);
		const parsedJson = JSON.parse(json);

		if (formData.length > 0) {
			jQuery(parsedJson).each((index, item) => {
				const slug = item.attributes['attribute_pa_color'].length > 0 ? item.attributes['attribute_pa_color'] : '';

				if (item['stock_quantity'] <= 0 && slug.length > 0) {
					this.swatches.each((listIndex, listItem) => {
						const dataValue = jQuery(listItem).data('value');

						if (dataValue.length > 0) {
							if (dataValue === slug) {
								jQuery(listItem).addClass('backordered');
							}
						}
					});
				} else {
					this.swatches.each((listIndex, listItem) => {
						const dataValue = jQuery(listItem).data('value');

						if (dataValue.length > 0) {
							if (dataValue === slug) {
								jQuery(listItem).addClass('in-stock');
							}
						}
					});
				}
			});
		}
	}

	maybeDisableInStock() {
		const tab = jQuery('#furniture-tabs-tab-left');
		if (this.available <= 0) {
			tab.find('.furniture-tabs__tab-label').css('color', '#D7D7D7').css('cursor', 'initial');
			tab.find('.furniture-tabs__tab-wrap').css('cursor', 'initial');
		}
	}

	setDefaultSwatch() {
		if (this.directLink()) {
			return;
		}

		jQuery(document).on('woo_variation_swatches_init', () => {
			setTimeout(( object ) => {
				const firstOutOfStock = object.backordered.first();
				const firstAvailable = object.available.first();

				if (firstAvailable.length > 0) {
					object.clear();
					firstAvailable.addClass('selected').trigger('click');
					firstAvailable.trigger( 'woocommerce_variation_has_changed' );
					object.setTab('left');
					object.showSwatches(object.swatches);
				} else {
					object.clear();
					firstOutOfStock.addClass('selected').trigger('click');
					firstOutOfStock.trigger( 'woocommerce_variation_has_changed' );
					object.setTab('right');
					object.showSwatches(object.swatches, 'backordered');
				}
			}, 0, this );
		});
	}

	directLink() {
		const urlParams = new URLSearchParams(window.location.search);
		const share = urlParams.get('share');
		const color = urlParams.get('attribute_pa_color');

		if (share) {
			this.swatches.each((index, swatch) => {
				const theSwatch = jQuery(swatch);
				if(theSwatch.data('value') === color) {
					this.clear();
					theSwatch.addClass('selected').trigger('click');
					theSwatch.trigger( 'woocommerce_variation_has_changed' );

					this.addStockStyles();
					if (theSwatch.hasClass('backordered')) {
						this.setTab('right');
						this.showSwatches(this.swatches, 'backordered');
					} else {
						this.setTab('left');
						this.showSwatches(this.swatches);
					}
				}
			});

			return true;
		}

		return false;
	}

	toggleIndicators() {
		const indicators = jQuery('.furniture-tabs__indicator');
		const inStockCount = this.getInstockCount();

		if (inStockCount === 0) {
			jQuery('#furniture-tabs-tab-right .furniture-tabs__tab-label').css('color', '#D7D7D7');
			jQuery('#furniture-tabs-tab-right .furniture-tabs__tab-label').css('cursor', 'initial');
			jQuery('#furniture-tabs-tab-right .furniture-tabs__tab-wrap').css('cursor', 'initial');
		}

		this.tabs.each((index, tab) => {
			const indicator = jQuery(tab).find('.furniture-tabs__indicator');

			if (inStockCount !== 0) {
				jQuery(tab).on('click', e => {
					indicators.addClass('hide');
					indicator.removeClass('hide');
				});
			}
		});
	}

	toggleSwatches() {
		const customize = jQuery('#furniture-tabs-tab-left');
		const inStock = jQuery('#furniture-tabs-tab-right');
		const inStockCount = this.getInstockCount();

		if (inStockCount !== 0) {
			customize.on('click', e => {
				this.setFirstSwatchSeleted(true);
				this.addStockStyles();
				this.swatches.each((index, swatch) => {
					const theSwatch = jQuery(swatch);
					this.showSwatches(theSwatch,'backordered');
				});
			});

			inStock.on('click', e => {
				this.setFirstSwatchSeleted();
				this.addStockStyles();
				this.swatches.each((index, swatch) => {
					const theSwatch = jQuery(swatch);
					this.showSwatches(theSwatch);
				});
			});
		}
	}

	populateLeatherSample() {
		const trigger = jQuery('#swatches-label-sample');
		const trigger2 = jQuery('#swatches-label-sample2');
		const nonce = trigger.data('nonce');

		this.populateLeatherSampleClickHandler(trigger, nonce);
		this.populateLeatherSampleClickHandler(trigger2, nonce);
	}

	swatchesClickSelectedHandler() {
		const shown = jQuery('.custom-furniture-template .selected-pa_color');

		this.swatches.each((index, swatch) => {
			jQuery(swatch).on('click', e => {
				this.addStockStyles();
				const name = jQuery(e.target).data('wvstooltip');
				shown.text(name);
			})
		});
	}

	addStockStyles() {
		setTimeout(() => {
			const available = jQuery('.stock.available-on-backorder');
			const wrap = jQuery('.woocommerce-variation-availability');

			if (available.text().trim().length) {
				wrap.css({
					borderTop: '1px solid #e7e6e6',
					marginBottom: '0',
					marginTop: '2rem',
					paddingTop: '1.5rem',
				})
			}
		}, 1000);
	}

	seeMoreSwatchesClickHandler() {
		const swatchWrap = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper');
		const trigger = jQuery('.see-more-mobile');

		trigger.on('click', () => {
			swatchWrap.toggleClass('opened');
			trigger.toggleClass('opened');
		});
	}

	seeMoreSwatchesCount() {
		jQuery(document).on('woo_variation_swatches_init', () => {
			const swatchWrap = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper');
			const backorderedOverCount = this.getHiddenSwatches(swatchWrap, this.backordered);
			const availableOverCount = this.getHiddenSwatches(swatchWrap, jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li.in-stock'));

			this.getAvailableCount(backorderedOverCount, availableOverCount);

			this.tabs.each((index, tab) => {
				jQuery(tab).on('click', () => {
					const backorderedOverCount = this.getHiddenSwatches(swatchWrap, this.backordered);
					const availableOverCount = this.getHiddenSwatches(swatchWrap, jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li.in-stock'));

					this.tabs.each((index, tab) => {
						jQuery(tab).toggleClass('selected');
					});

					this.getAvailableCount(backorderedOverCount, availableOverCount);
				});
			});
		});

		jQuery(window).on('resize', () => {
			const swatchWrap = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper');
			const backorderedOverCount = this.getHiddenSwatches(swatchWrap, this.backordered);
			const availableOverCount = this.getHiddenSwatches(swatchWrap, jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li.in-stock'));
			this.getAvailableCount(backorderedOverCount, availableOverCount);
		});
	}

	swatchHoverSelectedHandler() {
		const shown = jQuery('.custom-furniture-template .selected-pa_color');
		let timeout = null;

		this.swatches.each((index, swatch) => {
			const theSwatch = jQuery(swatch);

			theSwatch.on({
				mouseover: e => {
					const name = jQuery(e.target).data('wvstooltip');
					shown.text(name);

					if (timeout !== null) {
						clearTimeout(timeout);
						timeout = null;
					}
				},
				mouseleave: () => {
					const selected = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper li.selected');
					const selectedText = selected.data('wvstooltip');
					timeout = setTimeout(() => {
						shown.text(selectedText);
					}, 500);
				}
			});
		})
	}

	resetMobileDesktop() {
		const mediaQuery = window.matchMedia('(min-width: 900px)');

		jQuery(window).on('resize', () => {

			if (mediaQuery.matches) {
				const swatchWrap = jQuery('.custom-furniture-template .variable-items-wrapper.image-variable-wrapper');
				const trigger = jQuery('.see-more-mobile');

				if (swatchWrap.hasClass('opened')) {
					swatchWrap.removeClass('opened');
					trigger.removeClass('opened');
				}

			} else {
				setTimeout(() => {
					this.seeMoreSwatchesCount();
				}, 500);
			}
		});
	}

	getHiddenSwatches(parent, swatches) {
		const swatchesWrap = jQuery(parent);
		const swatchesWrapTop = swatchesWrap.offset().top;
		const swatchesWrapBottom = swatchesWrapTop + swatchesWrap.outerHeight();
		const swatched = jQuery(swatches);
		let count = 0;

		swatched.each((index, swatch) => {
			if (jQuery(swatch).offset().top >= swatchesWrapBottom) {
				count++;
			}
		});

		return count;
	}

	getAvailableCount(backorderedOverCount, availableOverCount) {
		const seeMore = jQuery('.see-more-mobile');
		const seeMoreNum = jQuery('.see-more-mobile-num');

		if (availableOverCount > 0) {
			this.showMorButton('.furniture-tabs__tab-right', availableOverCount, seeMoreNum, seeMore);
		} else {
			this.showMorButton('.furniture-tabs__tab-left', backorderedOverCount, seeMoreNum, seeMore);
		}
	}

	showMorButton(selector, count, seeMoreNum, seeMore) {
		if (count > 0) {
			seeMoreNum.text(`${count} `);
			seeMore.addClass('show');
			seeMore.removeClass('hide');
		} else {
			seeMore.addClass('hide');
			seeMore.removeClass('show');
		}
	}

	setFirstSwatchSeleted(isBackordered = false) {
		const shown = jQuery('.selected-pa_color');
		let inStock = [];
		let name = '';

		this.swatches.each((index, swatch) => {
			const theSwatch = jQuery(swatch);

			if (theSwatch.hasClass('selected')) {
				theSwatch.removeClass('selected');
			}

			if (!theSwatch.hasClass('backordered')) {
				inStock.push(swatch);
			}
		});

		if (isBackordered) {
			this.backordered.first().addClass('selected').trigger('click');
		} else {
			jQuery(inStock).first().addClass('selected').trigger('click');
		}

		this.swatches.each((index, swatch) => {
			const theSwatch = jQuery(swatch);

			if (theSwatch.hasClass('selected')) {
				name = jQuery(theSwatch).data('wvstooltip');
			}
		});

		shown.text(name);
	}

	getSelectedSwatch() {
		let data: any = {};

		this.swatches.each((index, swatch) => {
			if (jQuery(swatch).hasClass('selected')) {
				data.selected_name = jQuery(swatch).data('wvstooltip');
				data.selected_slug = jQuery(swatch).data('value');
			}
		});

		return data;
	}

	populateLeatherSampleClickHandler(trigger, nonce) {
		trigger.on('click', async () => {
			const data = this.getSelectedSwatch();
			this.populateLeatherSampleAjaxHandler(trigger, data.selected_name, data.selected_slug, nonce);
		});
	}

	async populateLeatherSampleAjaxHandler(trigger, selected_name, selected_slug, nonce) {
		return new Promise(resolve => {
			try {
				jQuery.ajax({
					async: false,
					type: 'POST',
					url: mgData.adminAjax,
					data: {
						action: 'leather_sample_data',
						sample_name: selected_name,
						sample_slug: selected_slug,
						product_id: mgData.mgProductCommentData.singleProductID,
						nonce
					},
					success: response => {
						if (response) {
							const json = JSON.parse(response);
							const image = jQuery('#leather-sample-modal-image');
							const title = jQuery('#leather-sample-modal-title');
							const body = jQuery('#leather-sample-modal-body');
							let updated = false;

							if (json.content !== undefined && json.content.length > 0) {
								image.css('display', 'inline-block');
								title.css('display', 'inline-block');
								image.css('background', `url(${json.image})`);
								image.attr('src', json.image);
								title.html(json.title);
								body.html(json.content);
								updated = true;
							} else {
								image.css('display', 'none');
								title.css('display', 'none');
								body.html('<p>No information is available for this leather</p>');
								updated = true;
							}

							if (updated) {
								(<any>jQuery(trigger)).modaal({
									start_open: true,
									custom_class: 'leather-sample',
									width: 380,
									after_open: () => {
										jQuery('#modaal-close').prependTo('.modaal-content-container');
									}
								});
							}

							resolve();
						}
					}
				});
			} catch (e) {
				console.error(e);
			}
		});
	}

	getInstockCount() {
		let inStockCount = 0;

		this.swatches.each((index, swatch) => {
			const item = jQuery(swatch);

			if (!item.hasClass('backordered')) {
				inStockCount++;
			}
		});

		return inStockCount;
	}

	setTab(tab = 'right') {
		let theTab;

		if (tab === 'right') {
			theTab = jQuery('#furniture-tabs-tab-left');
		} else {
			theTab = jQuery('#furniture-tabs-tab-right');
		}

		theTab.find('.furniture-tabs__indicator').removeClass('hide');
	}

	showSwatches(swatches, type = 'instock') {
		swatches.each((index, swatch) => {
			const theSwatch = jQuery(swatch);

			if (type === 'backordered') {
				if (theSwatch.hasClass('backordered')) {
					theSwatch.css('display', 'flex');
				} else {
					theSwatch.css('display', 'none');
				}
			} else {
				if (!theSwatch.hasClass('backordered')) {
					theSwatch.css('display', 'flex');
				} else {
					theSwatch.css('display', 'none');
				}
			}
		})
	}

	clear() {
		const indicators = jQuery('.furniture-tabs__indicator');

		this.swatches.each((index, swatch) => {
			if (jQuery(swatch).hasClass('selected')) {
				jQuery(swatch).removeClass('selected');
			}
		});
		indicators.each((index, indicator) => {
			jQuery(indicator).addClass('hide');
		});
	}
}
