// Declare Variables
export class ProductQuantity {

	/**
	 * Increase and Decrease the amount value
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public productAmountButtons(): void {
		jQuery(document.body).on('click', '.quantity-wrapper .minus, .quantity-wrapper .plus', function (e) {
			e.preventDefault();
			let button = jQuery(this);
			let qty_control = button.parents('.quantity-wrapper').first();
			let qty_input = qty_control.find('input.qty');

			if (qty_input) {
				let qtyStepAttr: string = qty_input.attr('step');
				let qtyValue: number = Number(qty_input.val());
				let update: number = 'undefined' !== typeof (qtyStepAttr) ? parseInt(qtyStepAttr) : 1;

				if (button.hasClass('minus')) {
					if (qtyValue > 1) {
						qtyValue = isNaN(qtyValue) ? 0 : qtyValue - update;
						qty_input.val(qtyValue.toString()).trigger('change');
						qty_input.css('width', qtyValue.toString().length * 8 + 'px');
					}
				} else {
					qtyValue = isNaN(qtyValue) ? 0 : qtyValue + update;
					qty_input.val(qtyValue.toString()).trigger('change');
					qty_input.css('width', qtyValue.toString().length * 8 + 'px');
				}
			}
		});
	}

	resizeInputs() {
		jQuery('.quantity-wrapper input.qty').each(function (i, element) {
			let qty_input = jQuery(element);
			let qtyValue: number = Number(qty_input.val());
			qty_input.css('width', qtyValue.toString().length * 8 + 'px');
		});
	}

	hideStepper() {
		jQuery( document.body ).on( 'found_variation woocommerce_variation_has_changed', function( event ) {
				const wrapper = jQuery('.quantity-wrapper');

				if (wrapper.hasClass('hide')) {
					wrapper.removeClass('hide');
				}

				if (jQuery('input.qty').attr('max') === '1') {
					wrapper.addClass('hide');
				}
		});
	}
}
