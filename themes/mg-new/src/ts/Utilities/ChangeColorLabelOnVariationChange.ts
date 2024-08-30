export class ChangeColorLabelOnVariationChange {
	/**
	 * Init
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public init(): void {
		this.setSwatchName(this.getName());
		this.onVariationChange();
	}

	/**
	 * Init
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public category(): void {
		jQuery(document).on("facetwp-loaded", () => {
			const products = jQuery(".tease-product");

			products.each((index, value) => {
				const name = jQuery(value)
					.find(
						'ul.variable-items-wrapper[data-attribute_name="attribute_pa_color"] .variable-item.selected:visible'
					)
					.first()
					.data("wvstooltip");

				this.setSwatchName(name, value);
				this.onVariationChange(value);
			});
		});
	}

	/**
	 * Set Selected Color Swatch Name
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public setSwatchName(name: string, product = null): void {
		let label: JQuery =
			product !== null
				? jQuery(product).find(".media-content__color")
				: jQuery(".selected-pa_color");

		label.html(name);
	}

	/**
	 * Change Selected Color Swatch Name
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public onVariationChange(product = null): void {
		if (product === null) {
			jQuery(document.body).on("found_variation", (event, variation) => {
				this.setSwatchName(
					jQuery(
						`li.variable-item[data-value="${variation.attributes.attribute_pa_color}"]`
					).data("wvstooltip")
				);
			});
		} else {
			const items = jQuery(product).find(".variable-item");

			items.each((index, value) => {
				jQuery(value).on("click", (e) => {
					const target = e.currentTarget;

					this.setSwatchName(
						jQuery(target).attr("data-wvstooltip"),
						product
					);
				});
			});
		}
	}

	/**
	 * Get Selected Color Swatch Name
	 *
	 * @author Jason Witt
	 *
	 * @return string
	 */
	public getName() {
		return <string>(
			jQuery(
				'ul.variable-items-wrapper[data-attribute_name="attribute_pa_color"] .variable-item.selected:visible'
			)
				.first()
				.data("wvstooltip")
		);
	}
}
