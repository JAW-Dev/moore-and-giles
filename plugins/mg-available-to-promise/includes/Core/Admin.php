<?php

namespace MG_ATP\Core;

use MG_ATP\Main;

/**
 * Class Admin
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package MG_ATP\Core
 * @author Clifton Griffin <clif@objectiv.co>
 */

class Admin {
	/** @var Main $plugin_instance */
	var $plugin_instance;

	public function __construct( $main ) {
		$this->plugin_instance = $main;
		$this->hooks();
	}

	/**
	 * Hooks
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'init' ) );

		// Styles and Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
	}

	/**
	 * Admin Menu
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_options_page( 'ATP', 'ATP', 'manage_options', 'mg_atp', array( $this, 'mg_atp_settings_page' ) );
		add_options_page( 'ATP Blackout Dates', 'ATP Blackout Dates', 'manage_options', 'mg_atp_blackout_dates', array( $this, 'mg_atp_blackout_settings_page' ) );
	}

	/**
	 * Init
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function init() {
		register_setting( 'mg_atp_options', '_mg_atp_settings' );

		add_settings_section(
			'mg_atp_mg_atp_options_section',
			'',
			'',
			'mg_atp_options'
		);

		add_settings_field(
			'mg_atp_text_field_0',
			__( 'Excluded Ship Dates', 'mg_atp' ),
			array( $this, 'mg_atp_text_field_0_render' ),
			'mg_atp_options',
			'mg_atp_mg_atp_options_section'
		);
	}

	/**
	 * Date Fields
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function mg_atp_text_field_0_render() {
		$options = get_option( '_mg_atp_settings' );
		?>
		<p><em>Add dates that orders will not ship, such as closures and holidays.</em></p>
		<p><em>The to date may be left blank.</em></p>
		<br>
		<div class="repeatable-wrap">
			<table id="repeatable-fields-list" class="repeatable-fields-list">
				<?php if ( ! empty( $options ) ) { ?>
					<?php
					$i     = 0;
					$count = count( $options );
					?>
					<?php foreach ( $options as $option ) { ?>
						<tr id="field_group_<?php echo esc_attr( $i ); ?>" data-index="<?php echo esc_attr( $i ); ?>">
							<td>
								<input type="text" class="datepicker" name="_mg_atp_settings[<?php echo esc_attr( $i ); ?>][from_date]" value="<?php echo esc_attr( $option['from_date'] ); ?>" placeholder="From">
							</td>
							<td>
								<input type="text" class="datepicker" name="_mg_atp_settings[<?php echo esc_attr( $i ); ?>][to_date]" value="<?php echo esc_attr( $option['to_date'] ); ?>" placeholder="To">
							</td>
							<td class="buttons">
								<button class="remove repeatable-field-remove"></button>
								<button class="add repeatable-field-add"></button>
							</td>
						</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr id="field_group_0" data-index="0">
						<td>
							<input type="text" class="datepicker" name="_mg_atp_settings[0][from_date]" value="" placeholder="From">
						</td>
						<td>
							<input type="text" class="datepicker" name="_mg_atp_settings[0][to_date]" value="" placeholder="To">
						</td>
						<td class="buttons">
							<button class="remove repeatable-field-remove"></button>
							<button class="add repeatable-field-add"></button>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
		<?php
	}

	public function mg_atp_settings_page() {
		?>
		<div class="wrap">
			<h2>ATP Settings</h2>

			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; // phpcs:ignore ?>">
				<?php $this->plugin_instance->get_settings_manager()->the_nonce(); ?>
				<h3>General Settings</h3>
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row" valign="top">Carrier</th>
						<td>
							<label>
								<select name="<?php echo esc_attr( $this->plugin_instance->get_settings_manager()->get_field_name( 'carrier' ) ); ?>">
									<?php
									foreach ( apply_filters( 'mg_atp_carriers', array() ) as $carrier ) :
										$checked = $this->plugin_instance->get_settings_manager()->get_setting( 'carrier' ) === strtolower( $carrier ) ? 'selected' : '';
										?>
										<option value="<?php echo esc_attr( strtolower( $carrier ) ); ?>" <?php echo esc_html( $checked ); ?>><?php echo esc_html( $carrier ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						</td>
					</tr>
					</tbody>
				</table>

				<?php do_action( 'mg_atp_carrier_settings', $this ); ?>

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Options Page
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function mg_atp_blackout_settings_page() {
		?>
		<div class="wrap">
			<form id="atp_settings" action="options.php" method='POST' autocomplete="off">
				<h2>MG Available to Promise Settings</h2>
				<?php
				settings_fields( 'mg_atp_options' );
				do_settings_sections( 'mg_atp_options' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue Admin Scripts.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $hook The admin page hook.
	 *
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		$file = 'dist/scripts/admin.js';

		if ( 'settings_page_mg_atp_blackout_dates' === $hook ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );

			if ( file_exists( MG_ATP_PLUGIN_DIR_PATH . $file ) ) {
				wp_enqueue_script( MG_ATP_PLUGIN_PREFIX . '_scripts', MG_ATP_PLUGIN_DIR_URL . $file, array( 'jquery' ), filemtime( MG_ATP_PLUGIN_DIR_PATH . $file ), true );
			}
		}
	}

	/**
	 * Enqueue Admin Styles.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $hook The admin page hook.
	 *
	 * @return void
	 */
	public function admin_styles( $hook ) {
		$file = 'dist/styles/admin.css';

		if ( 'settings_page_mg_atp_blackout_dates' === $hook ) {
			wp_enqueue_style( 'jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css', array(), MG_ATP_PLUGIN_VERSION );

			if ( file_exists( MG_ATP_PLUGIN_DIR_PATH . $file ) ) {
				wp_enqueue_style( MG_ATP_PLUGIN_PREFIX . '_stylesheet', MG_ATP_PLUGIN_DIR_URL . $file, array(), filemtime( MG_ATP_PLUGIN_DIR_PATH . $file ) );
			}
		}
	}
}
