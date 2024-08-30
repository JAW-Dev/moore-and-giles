import { Context }                                      from "./Utilities/Context";
import { WordpressSettings }                            from "./Models/WordpressSettings";
import { SiteSettings }                                 from "./Models/SiteSettings";

export class Main {

    /**
     * The instance for Main
     *
     * @type {null}
     * @private
     */
    private static _instance: Main = null;

    /**
     * The data for the site / current page loaded
     *
     * @type {WordpressSettings}
     * @private
     */
    private _wordpressSettings: any = null;

    /**
     * The shared settings used site-wide between SCSS and TS
     *
     * @type {SiteSettings}
     * @private
     */
    private _siteSettings: SiteSettings;

    /**
     * The contexts running currently
     *
     * @type {any[]}
     * @private
     */
    private _contexts: Context[] = [];

    /**
     * The constructor for Main. Takes in wordpressSettings and stores it
     *
     * @param {WordpressSettings} wordpressSettings
     * @param {any} siteSettings
     */
    constructor(wordpressSettings: WordpressSettings, siteSettings: any) {
        // Save the site settings in case we need to reference it later
        this.wordpressSettings = wordpressSettings;
        this.siteSettings = new SiteSettings(siteSettings);

        // Static reference to main for easy access in other objects.
        Main.instance = this;

        // Create the contexts required for the site. Typically it's the Global context and whatever page context is loaded
        this.contexts = [
            new Context('global', []),
            new Context(this.wordpressSettings.context.base, this.wordpressSettings.context.params)
        ];
    }

    /**
     * Return site settings
     *
     * @returns {WordpressSettings}
     */
    get wordpressSettings(): WordpressSettings {
        return this._wordpressSettings;
    }

    /**
     * Set site settings
     *
     * @param {WordpressSettings} value
     */
    set wordpressSettings(value: WordpressSettings) {
        this._wordpressSettings = value;
    }

    /**
     * @returns {SiteSettings}
     */
    get siteSettings(): SiteSettings {
        return this._siteSettings;
    }

    /**
     * @param {SiteSettings} value
     */
    set siteSettings(value: SiteSettings) {
        this._siteSettings = value;
    }

    /**
     * @returns {Context[]}
     */
    get contexts(): Context[] {
        return this._contexts;
    }

    /**
     * @param {Context[]} value
     */
    set contexts(value: Context[]) {
        this._contexts = value;
    }

    /**
     * Get the Main instance
     *
     * @returns {Main}
     */
    static get instance(): Main {
        return this._instance;
    }

    /**
     * If Main doesn't exist, set it. Otherwise discard it.
     *
     * @param {Main} value
     */
    static set instance(value: Main) {
        this._instance = (this._instance) ? this._instance : value;
    }
}