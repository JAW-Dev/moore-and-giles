import { ActionSet }                        from "../Base/ActionSet";
import { MooreAndGiles }                    from "../../../site-meta";

export class Context {

    /**
     * The context passed in from Wordpress
     *
     * @type {string}
     * @private
     */
    private _base: string = "";

    /**
     * The parameters to pass to the context
     *
     * @type {any[]}
     * @private
     */
    private _params: Array<any> = [];

    /**
     * @type {ActionSet}
     * @private
     */
    private _currentlyLoadedActionSet: ActionSet;

    /**
     * @param {string} base
     * @param {any[]} params
     */
    constructor(base: string, params: Array<any>) {
        this.base = base;
        this.params = params;

        this.loadActionSet();
    }

    /**
     * Instantiate the appropriate class based on the action set class name.
     *
     * This IS where the magic happens.
     */
    loadActionSet(): void {
        /**
         * If the class context we are looking for exists (not undefined and not null) create it with
         * the passed params
         */
        if(MooreAndGiles[this.actionSetClassName] !== undefined && MooreAndGiles[this.actionSetClassName] !== null) {
            this.currentlyLoadedActionSet = new MooreAndGiles[this.actionSetClassName](this.params);
        }
    }

    /**
     *
     * @returns {string}
     */
    get base(): string {
        return this._base;
    }

    /**
     *
     * @param {string} value
     */
    set base(value: string) {
        this._base = value;
    }

    /**
     * @returns {Array<any>}
     */
    get params(): Array<any> {
        return this._params;
    }

    /**
     * @param {Array<any>} value
     */
    set params(value: Array<any>) {
        this._params = value;
    }

    /**
     * Takes the lower case hyphenated context from Wordpress and transforms it into the appropriate TS class name to call
     *
     * @returns {string}
     */
    get actionSetClassName(): string {
        const postFix = "ActionSet";

        let upperPiece = piece => piece.charAt(0).toUpperCase() + piece.slice(1);
        let pieces = this.base.split("-");

        return pieces.map(upperPiece).join('') + postFix;
    }

    /**
     * @returns {ActionSet}
     */
    get currentlyLoadedActionSet(): ActionSet {
        return this._currentlyLoadedActionSet;
    }

    /**
     * @param {ActionSet} value
     */
    set currentlyLoadedActionSet(value: ActionSet) {
        this._currentlyLoadedActionSet = value;
    }
}