<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 */

namespace MG_Core\Admin;
use MG_Core\Main;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @author     Your Name <email@example.com>
 */
class Controller {
	var $plugin;

	public function __construct( Main $plugin ) {
		$this->plugin = $plugin;
	}

	public function add_settings_page() {
		add_options_page( 'MG Core Modules', 'Modules', 'manage_options', 'mg-core-modules', array($this, 'settings_page') );
	}

	function settings_page() {
		?>
		<div class="wrap">
			<h2>MG Core Modules</h2>

            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <?php $this->plugin->the_nonce(); ?>
                <table class="wp-list-table widefat plugins">
                    <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="name" class="manage-column column-name column-primary">Module</th><th scope="col" id="description" class="manage-column column-description">Description</th>	</tr>
                    </thead>
                    <tbody id="the-list">
                        <?php foreach( $this->plugin->get_available_modules() as $module ): ?>
                            <tr class="<?php echo $this->plugin->get_setting('module_states')[$module->get_id()] == "yes" ? 'active' : 'inactive'; ?>">
                                <th scope="row" class="check-column">
                                    <input type="hidden" name="<?php echo $this->plugin->get_field_name('module_states'); ?>[<?php echo $module->get_id(); ?>]" value="no" />
                                    <input type="checkbox" id="<?php echo $module->get_id(); ?>_check" name="<?php echo $this->plugin->get_field_name('module_states'); ?>[<?php echo $module->get_id(); ?>]" value="yes" <?php if ( $this->plugin->get_setting('module_states')[$module->get_id()] == "yes") echo 'checked="checked"'; ?> />
                                </th>
                                <td class="plugin-title column-primary">
                                    <strong>
                                        <label for="<?php echo $module->get_id(); ?>_check">
                                            <?php echo $module->get_name(); ?>
                                        </label>
                                    </strong>
                                </td>
                                <td class="column-description desc">
                                    <div class="plugin-description">
                                        <p><?php echo $module->get_description(); ?></p>
                                    </div>
                                    <div class="active second plugin-version-author-uri">
                                        Version <?php echo $module->get_version(); ?> | By <a href="<?php echo $module->get_author_uri(); ?>" target="_blank"><?php echo $module->get_author(); ?></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php submit_button( 'Save' ); ?>
            </form>
		</div>
		<?php
	}
}