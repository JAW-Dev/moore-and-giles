/* global jQuery */

export class Addons {

	constructor() {
		this.addGiftWrapping();
	}

	addGiftWrapping() {
		jQuery(document).on('change', '.sidebar-cart__container .product-addon-field-gift-wrapping', function() {
			jQuery('button[name=update_cart]').prop('disabled', false).trigger('click');
		});
	}
}

new Addons();