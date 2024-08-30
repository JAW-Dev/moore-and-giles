// Import Core Modules
import { Slick } from "./Slick";

export class SimpleCarousel {
	/**
	 * @private
	 * @type {Slick}
	 */
	private _slick: Slick;

    public init( args ): void {
		const selector = args.selector;
		const prevArrow = args.prevArrow;
		const nextArrow = args.nextArrow;

		this.slick = new Slick(<JQuery>jQuery(selector), args.carouselArgs, <JQuery>jQuery(prevArrow), <JQuery>jQuery(nextArrow));

		this.slick.run();
	}

	/**
	 * @returns {Slick}
	 */
	get slick(): Slick {
		return this._slick;
	}

	/**
	 * @param {Slick} value
	 */
	set slick(value: Slick) {
		this._slick = value;
	}
}
