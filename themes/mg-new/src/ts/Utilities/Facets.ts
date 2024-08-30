
// Declare Global variables
declare let mgData;
declare let FWP;

export class Facets {

	/**
	 * Clear search title when keyword facet is closed.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public clearSearchTitle(): void {
		const button = jQuery('li[data-facet="keywords_filter"]');
		const title = jQuery('.archive-page__header');
		const field = 'fwp_keywords_filter';
		const url = window.location.href;
		const isSearch = (url.indexOf('?' + field + '=') != -1) || (url.indexOf('&' + field + '=') != -1);

		if (isSearch) {
			jQuery(button).on('click', () => {
				jQuery(document).on('facetwp-loaded', () => {
					jQuery(title).remove();
				});
			});
		}
	}

	/**
	 * Add the background style to the color swatches facet.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public updateFacetWpSwatches(): void {
		let colorVariants = mgData.variationColors;
		colorVariants.forEach(color => {
			let colorSlug: string = color.slug;
			const element = jQuery('.facetwp-checkbox[data-value="' + colorSlug + '"]');

			if ( color.hasOwnProperty('background_image') ) {
				if ( element.length > 0 ) {
					element.html('');
					element.css('background-image', 'url(' + color.background_image + ')' );
				}
			} else if ( color.hasOwnProperty('background_color') ) {
				if ( element.length > 0 ) {
					element.html('');
					element.css('background-color', color.background_color );
				}
			}
		})
	}

	/**
	 * Add labels to selection pills.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public facetLabels(): void {
		let colorVariants = mgData.variationColors;
		setTimeout(() => {
			colorVariants.forEach(color => {
				const label: HTMLElement = document.querySelector('.facetwp-selection-value[data-value="' + color.slug + '"]');
				if( label ) {
					label.innerHTML = color.name;
				}
			});
		}, 500);
	}

	/**
	 * Add blocker for facets.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public facetWpBlocker(): void {
		const that = this;
		jQuery(document).on('facetwp-refresh', () => {
			(jQuery('.woocommerce-archive-products-container') as any).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6,
					fadeIn: 0,
					fadeOut: 0,
				}
			});

		});
		jQuery(document).on('facetwp-loaded', () => {
			(jQuery('.woocommerce-archive-products-container') as any).unblock();
		});
	}

	/**
	 * Show the facet selections clear button.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	showClear(): void {
		jQuery(document).on('facetwp-loaded', function() {
			if ('' == FWP.build_query_string()) {
				jQuery('.facetwp-selections ul.filters-clear').css('display', 'none');
			} else {
				setTimeout(() => {
					jQuery('.filters-clear').clone().appendTo(jQuery('.facetwp-selections ul'));
					jQuery('.facetwp-selections ul .filters-clear').css('display', 'inline-block');
				}, 500);
			};
		});
	}

	/**
	 * Widget Accorion
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	accordion() {
		const facetWidgets: JQuery = jQuery('.widget-accordian');

		facetWidgets.each((index, element) => {
			const heading: JQuery = jQuery(element).find('.widgettitle');
			const widget: JQuery = jQuery(element).find('.textwidget');
			const urlQueryParams = this.getQueryParams(document.location.search);
			const child = jQuery(widget).find('.facetwp-facet');
			const filterName = child.attr('data-name');

			// Make sure widget is open if filter is set on page reload.
			Object.keys(urlQueryParams).forEach(item => {
				const setFilter = item.substring( 'fwp_'.length )
				if (setFilter === filterName) {
					heading.toggleClass('opened');
					widget.toggleClass('show');
				}
			});

			heading.on('click', function() {
				heading.toggleClass('opened');
				widget.toggleClass('show');
			});
		});
	}

	getQueryParams(string) {
		string = string.split('+').join(' ');

		const params = {};
		const pattern = /[?&]?([^=]+)=([^&]*)/g;
		let tokens;

		while (tokens = pattern.exec(string)) {
			params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
		}

		return params;
	}
}
