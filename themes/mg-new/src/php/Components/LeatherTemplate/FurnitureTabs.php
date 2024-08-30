<?php
/**
 * Furniture Tabs
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Components\LeatherTemplate;

/**
 * Furniture Tabs
 *
 * @author Jason Witt
 */
class FurnitureTabs {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Render Markup.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function render() {
		?>
		<div class="furniture-tabs">
			<div id="furniture-tabs-tab-left" class="furniture-tabs__tab furniture-tabs__tab-left">
				<div class="furniture-tabs__tab-wrap">
					<div class="furniture-tabs__tab-icon"></div>
					<div class="furniture-tabs__tab-label">Customize</div>
				</div>
				<div class="furniture-tabs__indicator hide"></div>
			</div>
			<div id="furniture-tabs-tab-right" class="furniture-tabs__tab furniture-tabs__tab-right selected">
				<div class="furniture-tabs__tab-wrap">
					<div class="furniture-tabs__tab-label">In Stock</div>
				</div>
				<div class="furniture-tabs__indicator"></div>
			</div>
		</div>
		<?php
	}
}
