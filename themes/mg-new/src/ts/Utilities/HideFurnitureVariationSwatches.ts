export class HideFurnitureVariationSwatches {

	public init() {
		const furniture = jQuery('.is_single_product').hasClass('product_cat-furniture');

		if ( furniture ) {
			this.setDefaultAttributes();
		}
	}

	/**
	 * Go through attributes and select the first instock combination
	 */
	public setDefaultAttributes() {
		jQuery(document.body).one('woo_variation_swatches_init', () => {
			setTimeout( function() {
				jQuery( 'table.variations' ).find( '.woo-variation-raw-select' ).each( function( index, element ) {
					jQuery( element ).find( 'option[value!=""]').first().attr( 'selected', 'selected' );
					jQuery( element ).trigger('change');
				} );
			} );
		} );
	}
}
