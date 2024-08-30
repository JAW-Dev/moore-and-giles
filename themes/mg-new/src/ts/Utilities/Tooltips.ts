// Import Libraries
import tippy from "tippy.js";

// Declare Variables
declare let shippingTooltip;

export class Tooltips {
	/**
     * Tooltips
	 *
	 * @author Jason Witt
	 *
	 * @return void
     */
    public static init(): void {
		const tooltips: NodeListOf<Element> = document.querySelectorAll('.tooltip');
		tippy.setDefaults(<any>{
			allowHTML: true,
			interactive: true,
			arrow: true,
			arrowType: 'round',
			duration: 0,
			animation: 'shift-away',
			theme: 'light',
			zIndex: '999999',
			content(reference) {
				const id = reference.getAttribute('data-template');
				const template = document.getElementById(id);
				return template.innerHTML;
			}
		});

		if ( tooltips.length ) {
			tippy(tooltips);
		}
	}
}
