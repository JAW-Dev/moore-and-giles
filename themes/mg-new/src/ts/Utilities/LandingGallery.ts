// Import Core Modules
import { Main } from "../Main";

// Import Modules.
import { Slick } from "../Utilities/Slick";

export class LandingGallery {

	/**
     * @private
     * @type {Slick}
     */
    private _landingPageSlick: Slick;

	/**
     * Landing Gallery Slider
	 * 
	 * @author Jason Witt
	 * 
	 * @returns void
     */
    public init(): void {
		const tabletBreakpoint: number = Main.instance.siteSettings.breakpoints["tablet-large"].num;
		const carousel: JQuery = jQuery('.products-carousel .carousel');
		const options: JQuerySlickOptions = {
			slidesToShow: 3,
			dots: false,
			infinite: true,
			variableWidth: true,
			centerMode: true,
			prevArrow: '<div class="arrow prev-arrow"></div>',
			nextArrow: '<div class="arrow next-arrow"></div>',
			responsive: [
				{
					breakpoint: tabletBreakpoint,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						variableWidth: false,
						centerMode: false,
					}
				}
			]
		};
		this.landingPageSlick = new Slick(carousel, options);
		this.landingPageSlick.run();
		this.isInBounds();
		jQuery('.products-carousel__container').on('setPosition', () => {
			this.isInBounds();
		});
	}

	/**
     * Landing Gallery Slider
	 * 
	 * @author Jason Witt
	 * 
	 * @returns void
     */
    public isInBounds(): void {
		const elements: JQuery = jQuery('.slick-slide');
		
		elements.each( function(index, element) {
			const bounding = element.getBoundingClientRect();
			const media: JQuery = jQuery(this).find('.media-content');
			let isOff: boolean = false;
			if (bounding.left < 50 || bounding.right > (window.innerWidth || document.documentElement.clientWidth) - 50) {
				isOff = true;
			}
			if ( isOff ) {
				media.hide();
			} else {
				media.show();
			}
		})
	}

	/**
     * @returns {Slick}
     */
    get landingPageSlick(): Slick {
        return this._landingPageSlick;
	}
	
	/**
     * @param {Slick} value
     */
    set landingPageSlick(value: Slick) {
        this._landingPageSlick = value;
    }
}
