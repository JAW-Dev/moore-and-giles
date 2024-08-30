import { LeatherSampleForm } from './LeatherSampleForm';

declare let mgData: any;

export class LeatherSample {
	varationList;
	selectedVariation;

	init() {
		this.varationList = jQuery('.standard-product-template .variable-items-wrapper.image-variable-wrapper li');
		this.selectedVariation = jQuery('.standard-product-template .variable-items-wrapper.image-variable-wrapper li.selected');

		if (jQuery('body').hasClass('standard-product-template')) {
			this.setInitalLeather();
			this.switchLeather();
			new LeatherSampleForm().init();
		}
	}

	setInitalLeather() {
		if (mgData.productType === 'variable') {
			const selected_name = this.selectedVariation.data('wvstooltip');
			const selected_slug = this.selectedVariation.data('value');

			this.leatherAjaxHadler(selected_name, selected_slug);
		}
	}

	switchLeather() {
		this.varationList.each((index, swatch) => {
			jQuery(swatch).on('click', () => {
				const selected_name = jQuery(swatch).data('wvstooltip');
				const selected_slug = jQuery(swatch).data('value');

				this.leatherAjaxHadler(selected_name, selected_slug);
			});
		});
	}

	leatherAjaxHadler(selected_name, selected_slug) {
		jQuery.ajax({
			async: false,
			type: 'POST',
			url: mgData.adminAjax,
			data: {
				action: 'product_leather_sample',
				sample_name: selected_name,
				sample_slug: selected_slug,
				product_id: mgData.mgProductCommentData.singleProductID
			},
			success: response => {
				if (response) {
					const json = JSON.parse(response);
					const image = jQuery('#about-the-leather-image');
					const title = jQuery('#about-the-leather-title');
					const content = jQuery('#about-the-leather-content');

					image.css('background-image', `url(${json.image})`);
					title.html(json.title)
					content.html(json.content)
				}
			}
		});
	}
}
