// Import Core Modules
import 'modaal';

export class Modals {
	public init(selector): void {
		(<any>jQuery(selector)).modaal({
			after_open: () => {
				jQuery('#modaal-close').prependTo('.modaal-content-container');
			}
		});
	}

    public video( selector ): void {
		(<any>jQuery(selector)).modaal({
            type: 'video'
        });
	}
}
