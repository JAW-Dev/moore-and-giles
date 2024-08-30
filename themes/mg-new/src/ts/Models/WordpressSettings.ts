export type ContextSettings = { base: string, params: Array<any> }

export class WordpressSettings {
    public context: ContextSettings;
    public siteUrl: string;
    public siteTitle: string;
}