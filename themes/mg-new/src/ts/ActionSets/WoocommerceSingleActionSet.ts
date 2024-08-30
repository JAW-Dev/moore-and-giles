// Import Core Modules
import { ActionSet } from "../Base/ActionSet";
import { Main } from "../Main";

// Import Modules.
import { Slick } from "../Utilities/Slick";
import { CommentsLoadMore } from "../Utilities/CommentsLoadMore";
import { Addons } from "../Utilities/Addons";
import { ChangeColorLabelOnVariationChange } from "../Utilities/ChangeColorLabelOnVariationChange";
import { HideFurnitureVariationSwatches } from "../Utilities/HideFurnitureVariationSwatches";
import { CustomFurniture } from "../Utilities/CustomFurniture";
import { LeatherSample } from "../Utilities/LeatherSample";

// Navigation ID's for the Other Products carousels. Used to determine which slick to run on click
type OtherProductCarouselNames =
	| "similar-nav-item"
	| "also-in-nav-item"
	| "pairs-well-nav-item";

// Type for the product carousel aggregate properties
type OtherProductCarousel = {
	container: JQuery;
	dots: JQuery;
	arrows: Array<JQuery>;
	name: OtherProductCarouselNames;
};

declare var mgData: any;

export class WoocommerceSingleActionSet extends ActionSet {
	/**
	 * @private
	 * @type {Slick}
	 */
	private _hashTagSlick: Slick;

	/**
	 * @private
	 * @type {Slick}
	 */
	private _similarSlick: Slick;

	/**
	 * @private
	 * @type {Slick}
	 */
	private _alsoInSlick: Slick;

	/**
	 * @private
	 * @type {Slick}
	 */
	private _pairsWellSlick: Slick;

	/**
	 * @private
	 * @type {Slick}
	 */
	private _productGallerySlick: Slick;

	/**
	 *
	 */
	constructor() {
		super();
	}

	/**
	 *
	 */
	public load(): void {
		this.faq();
		this.hashtag();
		this.commentsModal();
		this.otherProducts();
		this.initAddons();
		this.changeColorLabelOnVariationChangeInit();
		this.initHideFurnitureVariationSwatches();
		this.customFurnitureInit();
		this.leatherSampleInit();
		CommentsLoadMore.init();
	}

	public leatherSampleInit() {
		new LeatherSample().init();
	}

	public customFurnitureInit() {
		new CustomFurniture().init();
	}

	public changeColorLabelOnVariationChangeInit() {
		new ChangeColorLabelOnVariationChange().init();
	}

	/**
	 * FAQ Accordion logic
	 */
	public faq(): void {
		let items: JQuery = jQuery(
			".single-product-faq .single-product-faq-item"
		);
		let duration: 300;

		items.each((index, item) => {
			let $item: JQuery = jQuery(item);
			let $title_bar: JQuery = $item.find(
				".single-product-faq-title-bar"
			);
			let $content: JQuery = $item.find(
				".single-product-faq-content-container"
			);

			$title_bar.on("click", () => {
				items.each((index, item) => {
					let $sd_item: JQuery = jQuery(item);
					let $sd_content: JQuery = $sd_item.find(
						".single-product-faq-content-container"
					);

					if ($sd_item.hasClass("active") && !$sd_item.is($item)) {
						$sd_item.removeClass("active");
						$sd_content.slideUp(duration);
					}
				});

				$item.toggleClass("active");
				$content.slideToggle(duration);
			});
		});
	}

	/**
	 * Swatches
	 */
	public initHideFurnitureVariationSwatches(): void {
		new HideFurnitureVariationSwatches().init();
	}

	/**
	 * Comments modal logic
	 */
	public commentsModal(): void {
		// What makes the menus slide (goes on site wrap)
		const menuActiveClass: string = 'comments-menu-active';

		// Modal control buttons and site wrap
		let siteWrap: JQuery = jQuery('.site-wrap');
		let closeCommentsModalBtn: JQuery = jQuery('.mobile-comments-modal-controls .close-icon-wrap');
		let readReviewsModalBtn: JQuery = jQuery('#read-reviews-modal-btn');
		let writeReviewsModalBtn: JQuery = jQuery("#write-reviews-modal-btn");

		// Modal open control btn
		let writeReviewsModalOpenBtn: JQuery = jQuery("#write-reviews-modal-open-btn");

		// Desktop control buttons
		let writeReviewsDesktopBtn: JQuery = jQuery("#write-reviews-desktop-btn");

		// Read more reviews button
		// TODO: Wire the read more reviews button
		let readMoreReviewsBtn: JQuery = null;

		// Review form
		let reviewForm: JQuery = jQuery("#review_form_wrapper");

		// Comments template
		let commentsTemplate: JQuery = jQuery(".comments-template");

		// Open modal
		readReviewsModalBtn.on('click', () => {
			siteWrap.addClass(menuActiveClass);
			commentsTemplate.scrollTop();
		});

		// Close the modal on click the X
		closeCommentsModalBtn.on('click', () => {
			siteWrap.removeClass(menuActiveClass);
		});

		// Write reviews for desktop
		writeReviewsDesktopBtn.on('click', () => {
			reviewForm.slideDown(50, () => {
				jQuery(window).scrollTop(jQuery("#review_form_wrapper").position().top);
			});
		});

		// Write reviews for open comments modal
		writeReviewsModalOpenBtn.on('click', () => {
			reviewForm.slideDown(50, () => {
				jQuery(commentsTemplate).scrollTop(jQuery("#review_form_wrapper").position().top);
			});
		});

		// Write reviews from mobile and open comments modal
		writeReviewsModalBtn.on('click', () => {
			siteWrap.addClass(menuActiveClass);
			reviewForm.slideDown(50, () => {
				jQuery(commentsTemplate).scrollTop(jQuery("#review_form_wrapper").position().top);
			});
		});

		// If the window goes beyond the tablet-large setting, kill the modal because we switch to desktop styles
		jQuery(window).on('resize', () => {
			let tabletLargeSize: number = Main.instance.siteSettings.breakpoints["tablet-large"].num;

			if (window.innerWidth >= tabletLargeSize) {
				siteWrap.removeClass(menuActiveClass);
			}
		});
	}

	/**
	 * Hashtag section slick carousel
	 */
	public hashtag(): void {
		let prevArrow: JQuery = jQuery(".hashtag-gallery .prev-arrow");
		let nextArrow: JQuery = jQuery(".hashtag-gallery .next-arrow");
		let carousel: JQuery = jQuery(".hashtag-gallery .carousel");
		let options: JQuerySlickOptions = {
			slidesToShow: 6,
			slidesToScroll: 1,
			dots: false
		};

		this.hashTagSlick = new Slick(carousel, options, prevArrow, nextArrow);
		this.hashTagSlick.run();
	}

	/**
	 * The other products slick sliders
	 */
	public otherProducts(): void {
		// Nav items
		let similarNav: JQuery = jQuery(".similar-nav-item");
		let alsoInNav: JQuery = jQuery(".also-in-nav-item");
		let pairsWellNav: JQuery = jQuery(".pairs-well-nav-item");

		// Dots containers
		let similarDots: JQuery = jQuery(
			".other-products .carousel-nav-similar .dots"
		);
		let alsoInDots: JQuery = jQuery(
			".other-products .carousel-nav-also-in .dots"
		);
		let pairsWellDots: JQuery = jQuery(
			".other-products .carousel-nav-pairs-well .dots"
		);

		// Carousels
		let similarCarousel: JQuery = jQuery(
			".other-products .carousel-similar"
		);
		let alsoInCarousel: JQuery = jQuery(
			".other-products .carousel-also-in"
		);
		let pairsWellCarousel: JQuery = jQuery(
			".other-products .carousel-pairs-well"
		);

		// Arrows
		let similarPrevArrow: JQuery = jQuery(
			".other-products .carousel-nav-similar .prev-arrow"
		);
		let similarNextArrow: JQuery = jQuery(
			".other-products .carousel-nav-similar .next-arrow"
		);
		let alsoInPrevArrow: JQuery = jQuery(
			".other-products .carousel-nav-also-in .prev-arrow"
		);
		let alsoInNextArrow: JQuery = jQuery(
			".other-products .carousel-nav-also-in .next-arrow"
		);
		let pairsWellPrevArrow: JQuery = jQuery(
			".other-products .carousel-nav-pairs-well .prev-arrow"
		);
		let pairsWellNextArrow: JQuery = jQuery(
			".other-products .carousel-nav-pairs-well .next-arrow"
		);

		// Aggregates
		let navItems: Array<JQuery> = [similarNav, alsoInNav, pairsWellNav];
		let carousels: Array<OtherProductCarousel> = [
			<OtherProductCarousel>{
				container: similarCarousel,
				dots: similarDots,
				arrows: [similarPrevArrow, similarNextArrow],
				name: "similar-nav-item"
			},
			<OtherProductCarousel>{
				container: alsoInCarousel,
				dots: alsoInDots,
				arrows: [alsoInPrevArrow, alsoInNextArrow],
				name: "also-in-nav-item"
			},
			<OtherProductCarousel>{
				container: pairsWellCarousel,
				dots: pairsWellDots,
				arrows: [pairsWellPrevArrow, pairsWellNextArrow],
				name: "pairs-well-nav-item"
			}
		];

		// Loops over each navigation item and adds a click event that runs the other products "brain"
		navItems.forEach((item: JQuery) =>
			item.on("click", () =>
				this.otherProductsBrain(item, false, navItems, carousels)
			)
		);

		// Initializes the slick object for each of the carousels but does not run it
		carousels.forEach(carousel =>
			this.otherProductsSlick(
				carousel.container,
				carousel.dots,
				carousel.arrows,
				carousel.name
			)
		);

		// The first carousel loaded on page load is always the similar products carousel. So run it first.
		this.otherProductsBrain(similarNav, true, navItems, carousels);
	}

	/**
	 * Handles the majority of the removing / adding the required active id's
	 *
	 * @param item
	 * @param pageLoad
	 * @param navItems
	 * @param carousels
	 */
	public otherProductsBrain(
		item: JQuery,
		pageLoad: boolean,
		navItems: Array<JQuery>,
		carousels: Array<OtherProductCarousel>
	): void {
		let similarCarousel = carousels[0].container;
		let similarDots = carousels[0].dots;

		let alsoInCarousel = carousels[1].container;
		let alsoInDots = carousels[1].dots;

		let pairsWellCarousel = carousels[2].container;
		let pairsWellDots = carousels[2].dots;

		let id = item.prop("id");

		let remove = remItem => {
			let remId = remItem.prop("id");

			if (remItem.hasClass("active")) {
				// Remove the active class
				remItem.removeClass("active");

				switch (remId) {
					case "similar-nav-item":
						similarCarousel.removeClass("active");
						similarDots.parent().removeClass("active");

						this.similarSlick.end();
						break;
					case "also-in-nav-item":
						alsoInCarousel.removeClass("active");
						alsoInDots.parent().removeClass("active");

						this.alsoInSlick.end();
						break;
					case "pairs-well-nav-item":
						pairsWellCarousel.removeClass("active");
						pairsWellDots.parent().removeClass("active");

						this.pairsWellSlick.end();
						break;
				}
			}
		};

		if (!item.hasClass(".active") || pageLoad) {
			if (!pageLoad) {
				// Remove actives from items
				navItems.forEach(remove);
			}

			// Add actives to clicked item
			item.addClass("active");

			switch (id) {
				case "similar-nav-item":
					// Add active classes so it can get all the widths / heights it needs
					similarCarousel.addClass("active");
					similarDots.parent().addClass("active");

					this.similarSlick.run();
					break;
				case "also-in-nav-item":
					// Add active classes so it can get all the widths / heights it needs
					alsoInCarousel.addClass("active");
					alsoInDots.parent().addClass("active");

					this.alsoInSlick.run();
					break;
				case "pairs-well-nav-item":
					// Add active classes so it can get all the widths / heights it needs
					pairsWellCarousel.addClass("active");
					pairsWellDots.parent().addClass("active");

					this.pairsWellSlick.run();
					break;
			}
		}
	}

	/**
	 * Returns the options for the other products slick sliders. Since they are all the same it makes sense to keep
	 * it here. Requires the dots navigation in question so it takes this in as a parameter
	 *
	 * @param dots
	 */
	public otherProductsSlickOptions(dots: JQuery): JQuerySlickOptions {
		let tabletBreakpoint: number =
			Main.instance.siteSettings.breakpoints["tablet"].num;

		return <JQuerySlickOptions>{
			slidesToShow: 3,
			slidesToScroll: 3,
			dots: true,
			appendDots: dots,
			responsive: [
				{
					breakpoint: tabletBreakpoint,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
					}
				}
			]
		};
	}

	/**
	 * Calls slick and assigns it to the appropriate object property based on the called nav item name
	 */
	public otherProductsSlick(
		container: JQuery,
		dots: JQuery,
		arrows: Array<JQuery>,
		whichSlick: OtherProductCarouselNames
	): void {
		switch (whichSlick) {
			case "similar-nav-item":
				this.similarSlick = new Slick(
					container,
					this.otherProductsSlickOptions(dots),
					arrows[0],
					arrows[1]
				);
				break;
			case "also-in-nav-item":
				this.alsoInSlick = new Slick(
					container,
					this.otherProductsSlickOptions(dots),
					arrows[0],
					arrows[1]
				);
				break;
			case "pairs-well-nav-item":
				this.pairsWellSlick = new Slick(
					container,
					this.otherProductsSlickOptions(dots),
					arrows[0],
					arrows[1]
				);
				break;
		}
	}

	/**
	 * Addons
	 *
	 * @author Jason Witt
	 *
	 * @returns void
	 */
	public initAddons(): void {
		const addons = new Addons();
		// addons.resetProductCheckboxes();
		addons.resetCheckboxes();
	}

	/**
	 * @returns {Slick}
	 */
	get hashTagSlick(): Slick {
		return this._hashTagSlick;
	}

	/**
	 * @param {Slick} value
	 */
	set hashTagSlick(value: Slick) {
		this._hashTagSlick = value;
	}

	/**
	 * @returns {Slick}
	 */
	get similarSlick(): Slick {
		return this._similarSlick;
	}

	/**
	 * @param {Slick} value
	 */
	set similarSlick(value: Slick) {
		this._similarSlick = value;
	}

	/**
	 * @returns {Slick}
	 */
	get alsoInSlick(): Slick {
		return this._alsoInSlick;
	}

	/**
	 * @param {Slick} value
	 */
	set alsoInSlick(value: Slick) {
		this._alsoInSlick = value;
	}

	/**
	 * @returns {Slick}
	 */
	get pairsWellSlick(): Slick {
		return this._pairsWellSlick;
	}

	/**
	 * @param {Slick} value
	 */
	set pairsWellSlick(value: Slick) {
		this._pairsWellSlick = value;
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
