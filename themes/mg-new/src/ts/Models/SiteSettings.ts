import { UnitType }                                 from "../Utilities/UnitType";

export interface Breakpoints {
    tablet: UnitType;
    "tablet-large": UnitType;
    desktop: UnitType;
    "desktop-large": UnitType;
}

export interface Wraps {
    mobile: string | UnitType;
    desktop: string | UnitType;
    "woo-archive": string | UnitType;
}

export interface Colors {
    accent: {
        1: string;
        2: string;
        3: string;
    };
    dark: {
        1: string;
        2: string;
        3: string;
        4: string;
    };
    light: {
        1: string;
        2: string;
        3: string;
    };
}

export interface Fonts {
    "sans-serif": string;
    serif: string;
}

export interface Spacing {
    mobile: UnitType;
    desktop: UnitType;
    "mobile-section": UnitType;
    "desktop-section": UnitType;
}

export class SiteSettings {

    /**
     * @type {UnitType}
     * @private
     */
    private "_base-font-size": UnitType;

    /**
     * @type {UnitType}
     * @private
     */
    private "_base-desktop-font-size": UnitType;

    /**
     * @type {UnitType}
     * @private
     */
    private "_front-page-custom-breakpoint-cat-blocks": UnitType;

    /**
     * @type {UnitType}
     * @private
     */
    private "_header-border-padding": UnitType;

    /**
     * @type {UnitType}
     * @private
     */
    private "_base-duration": string;

    /**
     * @type {string}
     * @private
     */
    private "_base-timing": string;

    /**
     * @type {string}
     * @private
     */
    private "_base-border-color": string;

    /**
     * @type {string}
     * @private
     */
    private "_base-border": string;

    /**
     * @type {Breakpoints}
     * @private
     */
    private _breakpoints: Breakpoints;

    /**
     * @type {Wraps}
     * @private
     */
    private _wraps: Wraps;

    /**
     * @type {Colors}
     * @private
     */
    private _colors: Colors;

    /**
     * @type {Fonts}
     * @private
     */
    private _fonts: Fonts;

    /**
     * @type {Spacing}
     * @private
     */
    private _spacing: Spacing;

    /**
     * @param {any} settings
     */
    constructor(settings: any) {
        this["base-font-size"] = new UnitType(settings["base-font-size"]);
        this["base-desktop-font-size"] = new UnitType(settings["base-desktop-font-size"]);
        this["front-page-custom-breakpoint-cat-blocks"] = new UnitType(settings["front-page-custom-breakpoint-cat-blocks"]);
        this["header-border-padding"] = new UnitType(settings["header-border-padding"]);
        this["base-duration"] = settings["base-duration"];
        this["base-timing"] = settings["base-timing"];
        this["base-border-color"] = settings["base-border-color"];
        this["base-border"] = settings["base-border"];
        this["breakpoints"] = <Breakpoints>{
            "tablet": new UnitType(settings["breakpoints"]["tablet"]),
            "tablet-large": new UnitType(settings["breakpoints"]["tablet-large"]),
            "desktop": new UnitType(settings["breakpoints"]["desktop"]),
            "desktop-large": new UnitType(settings["breakpoints"]["desktop-large"])
        };
        this["wraps"] = <Wraps>{
            "mobile": <string>settings["wraps"]["mobile"],
            "desktop": new UnitType(settings["wraps"]["desktop"]),
            "woo-archive": new UnitType(settings["wraps"]["woo-archive"])
        };
        this["colors"] = <Colors>settings["colors"];
        this["fonts"] = <Fonts>settings["fonts"];
        this["spacing"] = <Spacing>{
            "mobile": new UnitType(settings["spacing"]["mobile"]),
            "desktop": new UnitType(settings["spacing"]["desktop"]),
            "mobile-section": new UnitType(settings["spacing"]["mobile-section"]),
            "desktop-section": new UnitType(settings["spacing"]["desktop-section"])
        };
    }

    /**
     * @returns {UnitType}
     */
    get "base-font-size"(): UnitType {
        return this["_base-font-size"];
    }

    /**
     * @param {UnitType} value
     */
    set "base-font-size"(value: UnitType) {
        this["_base-font-size"] = value;
    }

    /**
     * @returns {UnitType}
     */
    get "base-desktop-font-size"(): UnitType {
        return this["_base-desktop-font-size"];
    }

    /**
     * @param {UnitType} value
     */
    set "base-desktop-font-size"(value: UnitType) {
        this["_base-desktop-font-size"] = value;
    }

    /**
     * @returns {UnitType}
     */
    get "front-page-custom-breakpoint-cat-blocks"(): UnitType {
        return this["_front-page-custom-breakpoint-cat-blocks"];
    }

    /**
     * @param {UnitType} value
     */
    set "front-page-custom-breakpoint-cat-blocks"(value: UnitType) {
        this["_front-page-custom-breakpoint-cat-blocks"] = value;
    }

    /**
     * @returns {UnitType}
     */
    get "header-border-padding"(): UnitType {
        return this["_header-border-padding"];
    }

    /**
     * @param {UnitType} value
     */
    set "header-border-padding"(value: UnitType) {
        this["_header-border-padding"] = value;
    }

    /**
     * @returns {string}
     */
    get "base-duration"(): string {
        return this["_base-duration"];
    }

    /**
     * @param {string} value
     */
    set "base-duration"(value: string) {
        this["_base-duration"] = value;
    }

    /**
     * @returns {string}
     */
    get "base-timing"(): string {
        return this["_base-timing"];
    }

    /**
     * @param {string} value
     */
    set "base-timing"(value: string) {
        this["_base-timing"] = value;
    }

    /**
     * @returns {string}
     */
    get "base-border-color"(): string {
        return this["_base-border-color"];
    }

    /**
     * @param {string} value
     */
    set "base-border-color"(value: string) {
        this["_base-border-color"] = value;
    }

    /**
     * @returns {string}
     */
    get "base-border"(): string {
        return this["_base-border"];
    }

    /**
     * @param {string} value
     */
    set "base-border"(value: string) {
        this["_base-border"] = value;
    }

    /**
     * @returns {Breakpoints}
     */
    get breakpoints(): Breakpoints {
        return this._breakpoints;
    }

    /**
     * @param {Breakpoints} value
     */
    set breakpoints(value: Breakpoints) {
        this._breakpoints = value;
    }

    /**
     * @returns {Wraps}
     */
    get wraps(): Wraps {
        return this._wraps;
    }

    /**
     * @param {Wraps} value
     */
    set wraps(value: Wraps) {
        this._wraps = value;
    }

    /**
     * @returns {Colors}
     */
    get colors(): Colors {
        return this._colors;
    }

    /**
     * @param {Colors} value
     */
    set colors(value: Colors) {
        this._colors = value;
    }

    /**
     * @returns {Fonts}
     */
    get fonts(): Fonts {
        return this._fonts;
    }

    /**
     * @param {Fonts} value
     */
    set fonts(value: Fonts) {
        this._fonts = value;
    }

    /**
     * @returns {Spacing}
     */
    get spacing(): Spacing {
        return this._spacing;
    }

    /**
     * @param {Spacing} value
     */
    set spacing(value: Spacing) {
        this._spacing = value;
    }
}