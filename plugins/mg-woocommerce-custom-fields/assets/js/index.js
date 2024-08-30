/* global jQuery */

class releaseDate {
	constructor() {
		this.init();
	}

	init() {
		this.customDatepicker();
		jQuery(document).on('woocommerce_variations_loaded', () => {
			this.customDatepicker();
		});
	}

	customDatepicker() {
		jQuery('.release-date').datepicker({
			dateFormat: 'mm/dd/yy'
		});
	}
}

new releaseDate();