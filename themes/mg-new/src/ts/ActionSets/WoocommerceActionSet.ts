// Import Core Modules
import { ActionSet } from "../Base/ActionSet";

// Import Modules.
import { Slick } from "../Utilities/Slick";
import { Facets } from "../Utilities/Facets";
import { Tooltips } from "../Utilities/Tooltips";
import { QuickviewPagination } from "../Utilities/QuickviewPagination";
import { CategoryPagination } from '../Utilities/CategoryPagination';
import { ChangeColorLabelOnVariationChange } from "../Utilities/ChangeColorLabelOnVariationChange";
import { ProductStock } from "../Utilities/ProductStock";

declare var FWP: any;

export class WoocommerceActionSet extends ActionSet {

	/**
     * @private
     * @type {Slick}
     */
	private _productGallerySlick: Slick;

    constructor() {
        super();
    }

    public load(): void {
		Tooltips.init();
		this.initFacetWpVarationCheckboxes();
		this.initCategoryPagination();
		this.initQuickviewPagination();
		this.changeColorLabelOnVariationChangeInit();
		this.initProductStock();
	}

	/**
	 * Product stock functionality.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public initProductStock() {
		new ProductStock().init();
	}


	/**
	 * Category Pagination.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public initCategoryPagination() {
		const pagination = new CategoryPagination();
		pagination.clases();

		jQuery(document).on('facetwp-loaded', function() {
			if (FWP.loaded) {
				jQuery('html, body').scrollTop(jQuery('.archive-page__header').position().top);
			}
		});
	}

	/**
	 * Change the color label.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public changeColorLabelOnVariationChangeInit() {
		new ChangeColorLabelOnVariationChange().category();
	}

	/**
	 * Init code for moving the quickview gallery pagination.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public initQuickviewPagination(): void {
		const quickviewPagination = new QuickviewPagination();
		quickviewPagination.moveElements();
	}

	/**
	 * Init code for facetWP color swatches facet.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public initFacetWpVarationCheckboxes(): void {
		const facets = new Facets();
		facets.facetWpBlocker();
		jQuery(window).on('load', () => {
			facets.updateFacetWpSwatches();
			facets.clearSearchTitle();
		});
		jQuery(document).on('facetwp-loaded', () => {
			facets.updateFacetWpSwatches();
			facets.facetLabels();
			facets.clearSearchTitle();
		});
		facets.showClear();
		facets.accordion();
	}

	/**
     * @returns {Slick}
     */
    get productGallerySlick(): Slick {
        return this._productGallerySlick;
    }

    /**
     * @param {Slick} value
     */
    set productGallerySlick(value: Slick) {
        this._productGallerySlick = value;
    }
}
