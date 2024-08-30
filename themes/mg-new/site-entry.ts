// Import styles
import './src/scss/comment-header.scss';
import './src/scss/style.scss';

// Import main
import { Main }                                     from "./src/ts/Main";
import { WordpressSettings }                        from "./src/ts/Models/WordpressSettings";

import * as siteSettingsJson                        from './site-settings.json';

// Register our global data needed to connect the backend to TS
declare let mgData: any;

/**
 * The moore and giles TS initialize event. Called from document.ready. Allows for encapsulation because main is created,
 * then rendered only accessible via Main.instance. Main is not global, ergo we have pseudo app scope.
 */
let wordpressSettings: WordpressSettings = <WordpressSettings>mgData;
let siteSettings = <any>siteSettingsJson;
let main: Main = new Main(wordpressSettings, siteSettings);