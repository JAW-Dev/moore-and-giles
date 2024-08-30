export class Addons {

	/**
     * Reset the checkbox on page reload.
     *
     * @author Jason Witt
	 *
	 * @return void
     *
	 */
	resetCheckboxes() {
		const checkbox = jQuery('.is_single_product .product-addon[type="checkbox"]');
		checkbox.each(function () {
			if (jQuery(this).is(':checked')) {
				jQuery(this).prop('checked', false);
			}
		});
	}
}
