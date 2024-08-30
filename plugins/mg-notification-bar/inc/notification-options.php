<?php
/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class MG_Notification_Bar_Admin {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'mg_notification_bar_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'mg_notification_bar_option_metabox';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'Notification Bar', 'mg_notification_bar' );
	}

	/**
	 * Initiate our hooks
	 * @since 0.1.0
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
	}


	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	/**
	 * Add menu options page
	 * @since 0.1.0
	 */
	public function add_options_page() {
		$this->options_page = add_submenu_page( 'options-general.php', $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ) );

		// Include CMB CSS in the head to avoid FOUT
		add_action( "admin_print_styles-{$this->options_page}", array( 'CMB2_hookup', 'enqueue_cmb_css' ) );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

		$cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		$cmb->add_field( array(
		    'name'       => 'Enable Notification Bar',
		    'id'         => 'enable_notification_bar',
		    'type'       => 'checkbox',
		) );

		$cmb->add_field( array(
			'name'	=> 'Enter the text to display in the notification bar.',
			'id'	=> 'notification_bar_text',
			'type'	=> 'textarea',
		) );

		$cmb->add_field( array(
			'name'	=> 'Enter the link to show in the notification bar.',
			'id'	=> 'notification_bar_link',
			'type'	=> 'text_url',
		) );

		$cmb->add_field( array(
			'name'	=> 'Enter the link text to show in the notification bar.',
			'id'	=> 'notification_bar_link_text',
			'type'	=> 'text',
		) );

		$cmb->add_field( array(
			'name'	=> 'Link Color',
			'id'	=> 'notification_bar_link_color',
			'type'	=> 'colorpicker',
		) );

		$cmb->add_field( array(
			'name'	=> 'Notification Bar Color',
			'id'	=> 'notification_bar_color',
			'type'	=> 'colorpicker',
		) );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the MG_Notification_Bar_Admin object
 * @since  0.1.0
 * @return MG_Notification_Bar_Admin object
 */
function mg_notification_bar_admin() {
	static $object = null;
	if ( is_null( $object ) ) {
		$object = new MG_Notification_Bar_Admin();
		$object->hooks();
	}

	return $object;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function mg_notification_bar_get_option( $key = '' ) {
	if ( function_exists( 'cmb2_get_option') ) {
		return cmb2_get_option( mg_notification_bar_admin()->key, $key );
	}
}
