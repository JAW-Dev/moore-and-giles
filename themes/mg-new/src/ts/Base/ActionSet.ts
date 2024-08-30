/**
 * Template class
 */
export abstract class ActionSet {

    protected constructor(params: any[] = [], load: boolean = true) {
        if(load) {
            this.load(...params);
        }
    }

    /**
     * Literally anything function. Runs user code. Takes in params (any amount)
     *
     * @param params
     */
    abstract load(...params): void;
}
