import 'slick-carousel';

export class Slick {

    /**
     * @private
     * @type {JQuery}
     */
    private _carousel: JQuery;

    /**
     * @private
     * @type {JQuery}
     */
    private _prevArrow: JQuery;

    /**
     * @private
     * @type {JQuery}
     */
    private _nextArrow: JQuery;

    /**
     * @private
     * @type {JQuery}
     */
    private _options: JQuerySlickOptions;

    /**
     * @param {JQuery} carousel
     * @param {JQuerySlickOptions} options
     * @param {JQuery} prevArrow
     * @param {JQuery} nextArrow
     */
    constructor(carousel: JQuery, options: JQuerySlickOptions, prevArrow?: JQuery, nextArrow?: JQuery) {
        this.carousel = carousel;
        this.options = options;
        this.prevArrow = (prevArrow) ? prevArrow : null;
        this.nextArrow = (nextArrow) ? nextArrow : null;

        // If arrows weren't passed in (both), don't assign them to options
        if(this.prevArrow && this.nextArrow) {
            this.options.prevArrow = this.prevArrow;
            this.options.nextArrow = this.nextArrow;
        }
    }

    /**
     * Kick off Slick on the container
     */
    run(): void {
		this.carousel.slick(this.options);
	}

	runInit(): void {
		this.carousel.not('.slick-initialized').slick(this.options);
	}

    end(): void {
        this.carousel.slick("unslick");
    }

    /**
     * @returns {JQuery}
     */
    get carousel(): JQuery {
        return this._carousel;
    }

    /**
     * @param {JQuery} value
     */
    set carousel(value: JQuery) {
        this._carousel = value;
    }

    /**
     * @returns {JQuery}
     */
    get prevArrow(): JQuery {
        return this._prevArrow;
    }

    /**
     * @param {JQuery} value
     */
    set prevArrow(value: JQuery) {
        this._prevArrow = value;
    }

    /**
     * @returns {JQuery}
     */
    get nextArrow(): JQuery {
        return this._nextArrow;
    }

    /**
     * @param {JQuery} value
     */
    set nextArrow(value: JQuery) {
        this._nextArrow = value;
    }

    /**
     * @returns {JQuerySlickOptions}
     */
    get options(): JQuerySlickOptions {
        return this._options;
    }

    /**
     * @param {JQuerySlickOptions} value
     */
    set options(value: JQuerySlickOptions) {
        this._options = value;
    }
}
