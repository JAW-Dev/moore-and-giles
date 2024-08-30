<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Iglobal_Plugin_Settings {

	/**
	 * The single instance of Iglobal_Plugin_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'iglobal_';

		// Initialise settings
		add_action( 'init', array( $this, 'zonos_init_settings' ), 11 );

		// Register plugin settings
		add_action( 'admin_init' , array( $this, 'zonos_register_settings' ) );

        // Check for Uploaded welcome mat or css file
        add_action( 'init' , array( $this, 'zonos_check_welcome_mat_uploads' ) );

        // Check for removal of custom welcome mat files
        add_action( 'init' , array( $this, 'zonos_remove_custom_welcome_mat' ) );

		// Add settings page to menu
		add_action( 'admin_menu' , array( $this, 'zonos_add_menu_item' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'zonos_add_settings_link' ) );
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function zonos_init_settings () {
		$this->settings = $this->zonos_settings_fields();
	}

    /**
    * Check for iglobal welcome mat js and css uploads
    * @return void
    */
    public function zonos_check_welcome_mat_uploads () {
        //check for file upload js
        if ( isset( $_FILES[$this->base . 'welcome_mat_javascript'] ) ){
            // get file name
            $welcome_mat_file = stripslashes( $_FILES[$this->base . 'welcome_mat_javascript']['name'] );
            $welcome_mat_field = $_FILES[$this->base . 'welcome_mat_javascript'];
            $welcome_mat_option = $this->base . 'welcome_mat_javascript_url';

            $this->zonos_upload_welcome_mat_file( $welcome_mat_file, $welcome_mat_field, "js", $welcome_mat_option );

        }

        //check for custom welcome mat css upload
        if ( isset( $_FILES[$this->base . 'welcome_mat_styling'] ) ){
            // get file name
            $welcome_mat_css = stripslashes( $_FILES[$this->base . 'welcome_mat_styling']['name'] );
            $welcome_mat_field = $_FILES[$this->base . 'welcome_mat_styling'];
            $welcome_mat_option = $this->base . 'welcome_mat_css_url';

            $this->zonos_upload_welcome_mat_file( $welcome_mat_css, $welcome_mat_field, "css", $welcome_mat_option );

        }
    }

    /**
    * File uploader utility method
    * @param string $file_name  Name of file to be uploaded
    * @param array  $file_field reference to form file field
    * @param string $valid_extension  validate file type
    * @param string $option_name Name of option to be created to reference file path url
    * @return void
    */
    private function zonos_upload_welcome_mat_file( $file_name, $file_field, $valid_extension, $option_name ) {
        // validate extension
        $file_type = wp_check_filetype( $file_name );
        if ( $file_type['ext'] == $valid_extension ) {
            $complete_file_path = wp_upload_dir()['path'] . "/" . $file_name;
            $upload_dir = wp_upload_dir();

            // check if file exists, if so keep incrementing file name
            while( file_exists( $complete_file_path ) ) {
                $file_name_arr = explode( ".", $file_name );
                $file_name_split = explode( "_", $file_name_arr[0] );

                if( count( $file_name_split ) > 0 ) {
                    $last_elem = count( $file_name_split ) - 1;  // get last elem in file name seperated by '_'
                    if( is_numeric( $file_name_split[$last_elem] ) ){
                        $file_name_split[$last_elem] = ( int )$file_name_split[$last_elem] + 1;
                    } else{
                        array_push( $file_name_split, "1" );
                    }
                    $file_name = implode( "_", $file_name_split );
                } else {  // in case no '_' in file name
                    $file_name = $file_name_arr[0] . "_1";
                }
                $file_name .= "." . $file_name_arr[1];  // add back extension
                $complete_file_path = wp_upload_dir()['path'] . "/" . $file_name;
            }

            $copied = copy( $file_field['tmp_name'], $complete_file_path );

            // Successfully copied so update our option field with the url
            if( true === $copied ) {
                update_option( $option_name, $upload_dir['url'] . "/" . $file_name );
            }
        }
    }

    /**
     * Check for removal of custom welcome mat files
     * @return void
    */
    public function zonos_remove_custom_welcome_mat () {
        $remove_welcome_js = get_option( $this->base . 'welcome_mat_javascript_remove' );
        $remove_welcome_css = get_option( $this->base . 'welcome_mat_styling_remove' );

        // Remove Js
        if( isset( $remove_welcome_js ) && "on" == $remove_welcome_js ){
            delete_option( $this->base . 'welcome_mat_javascript_url' );
            delete_option( $this->base . 'welcome_mat_javascript_remove' );
        }

        // Remove Css
        if( isset( $remove_welcome_css ) && "on" == $remove_welcome_css ){
            delete_option( $this->base . 'welcome_mat_css_url' );
            delete_option( $this->base . 'welcome_mat_styling_remove' );
        }
    }

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	public function zonos_add_menu_item () {
		$page = add_options_page( __( 'Zonos Settings', 'iglobal' ) , __( 'Zonos Settings', 'iglobal' ) , 'manage_options' , $this->parent->_token . '_settings' ,  array( $this, 'zonos_settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'zonos_settings_assets' ) );
	}

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function zonos_settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function zonos_add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __( 'Settings', 'iglobal' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function zonos_settings_fields () {
		$country_array = array(
			"AF"=>"Afghanistan","AL"=>"Albania","DZ"=>"Algeria","AS"=>"American Samoa","AD"=>"Andorra","AO"=>"Angola","AI"=>"Anguilla","AG"=>"Antigua","AR"=>"Argentina","AM"=>"Armenia","AW"=>"Aruba","AU"=>"Australia","AT"=>"Austria","AZ"=>"Azerbaijan","BS"=>"Bahamas","BH"=>"Bahrain","BD"=>"Bangladesh","BB"=>"Barbados","BY"=>"Belarus","BE"=>"Belgium","BZ"=>"Belize","BJ"=>"Benin","BM"=>"Bermuda","BT"=>"Bhutan","BO"=>"Bolivia","BQ"=>"Bonaire, St. Eustatius & Saba","BA"=>"Bosnia & Herzegovina","BW"=>"Botswana","BR"=>"Brazil","BN"=>"Brunei","BG"=>"Bulgaria","BF"=>"Burkina Faso","BI"=>"Burundi","KH"=>"Cambodia","CM"=>"Cameroon","CA"=>"Canada","IC"=>"Canary Islands","CV"=>"Cape Verde","KY"=>"Cayman Islands","CF"=>"Central African Republic","TD"=>"Chad","CL"=>"Chile","CN"=>"China - People's Republic of","CO"=>"Colombia","KM"=>"Comoros","CG"=>"Congo","CK"=>"Cook Islands","CR"=>"Costa Rica","HR"=>"Croatia","CW"=>"Curaçao","CY"=>"Cyprus","CZ"=>"Czech Republic","DK"=>"Denmark","DJ"=>"Djibouti","DM"=>"Dominica","DO"=>"Dominican Republic","EC"=>"Ecuador","EG"=>"Egypt","SV"=>"El Salvador","GQ"=>"Equatorial Guinea","ER"=>"Eritrea","EE"=>"Estonia","ET"=>"Ethiopia","FK"=>"Falkland Islands","FO"=>"Faroe Islands (Denmark)","FJ"=>"Fiji","FI"=>"Finland","FR"=>"France","GF"=>"French Guiana","GA"=>"Gabon","GM"=>"Gambia","GE"=>"Georgia","DE"=>"Germany","GH"=>"Ghana","GI"=>"Gibraltar","GR"=>"Greece","GL"=>"Greenland (Denmark)","GD"=>"Grenada","GP"=>"Guadeloupe","GU"=>"Guam","GT"=>"Guatemala","GG"=>"Guernsey","GN"=>"Guinea","GW"=>"Guinea-Bissau","GY"=>"Guyana","HT"=>"Haiti","HN"=>"Honduras","HK"=>"Hong Kong","HU"=>"Hungary","IS"=>"Iceland","IN"=>"India","ID"=>"Indonesia","IQ"=>"Iraq","IE"=>"Ireland - Republic Of","IL"=>"Israel","IT"=>"Italy","CI"=>"Ivory Coast","JM"=>"Jamaica","JP"=>"Japan","JE"=>"Jersey","JO"=>"Jordan","KZ"=>"Kazakhstan","KE"=>"Kenya","KI"=>"Kiribati","KR"=>"Korea, Republic of (South Korea)","KW"=>"Kuwait","KG"=>"Kyrgyzstan","LA"=>"Laos","LV"=>"Latvia","LB"=>"Lebanon","LS"=>"Lesotho","LR"=>"Liberia","LI"=>"Liechtenstein","LT"=>"Lithuania","LU"=>"Luxembourg","MO"=>"Macau","MK"=>"Macedonia","MG"=>"Madagascar","MW"=>"Malawi","MY"=>"Malaysia","MV"=>"Maldives","ML"=>"Mali","MT"=>"Malta","MH"=>"Marshall Islands","MQ"=>"Martinique","MR"=>"Mauritania","MU"=>"Mauritius","YT"=>"Mayotte","MX"=>"Mexico","FM"=>"Micronesia - Federated States of","MD"=>"Moldova","MC"=>"Monaco","MN"=>"Mongolia","ME"=>"Montenegro","MS"=>"Montserrat","MA"=>"Morocco","MZ"=>"Mozambique","MM"=>"Myanmar","NA"=>"Namibia","NR"=>"Nauru, Republic of","NP"=>"Nepal","NL"=>"Netherlands (Holland)","NV"=>"Nevis","NC"=>"New Caledonia","NZ"=>"New Zealand","NI"=>"Nicaragua","NE"=>"Niger","NG"=>"Nigeria","NU"=>"Niue Island","NF"=>"Norfolk Island","MP"=>"Northern Mariana Islands","NO"=>"Norway","OM"=>"Oman","PK"=>"Pakistan","PW"=>"Palau","PA"=>"Panama","PG"=>"Papua New Guinea","PY"=>"Paraguay","PE"=>"Peru","PH"=>"Philippines","PL"=>"Poland","PT"=>"Portugal","PR"=>"Puerto Rico","QA"=>"Qatar","RE"=>"Reunion","RO"=>"Romania","RU"=>"Russia","RW"=>"Rwanda","SM"=>"San Marino","ST"=>"Sao Tome & Principe","SA"=>"Saudi Arabia","SN"=>"Senegal","RS"=>"Serbia & Montenegro","SC"=>"Seychelles","SL"=>"Sierra Leone","SG"=>"Singapore","SK"=>"Slovakia","SI"=>"Slovenia","SB"=>"Solomon Islands","ZA"=>"South Africa","SS"=>"South Sudan","ES"=>"Spain","LK"=>"Sri Lanka","BL"=>"St. Barthelemy","EU"=>"St. Eustatius","KN"=>"St. Kitts and Nevis","LC"=>"St. Lucia","MF"=>"St. Maarten","VC"=>"St. Vincent","SD"=>"Sudan","SR"=>"Suriname","SZ"=>"Swaziland","SE"=>"Sweden","CH"=>"Switzerland","PF"=>"Tahiti","TW"=>"Taiwan","TJ"=>"Tajikistan","TZ"=>"Tanzania","TH"=>"Thailand","TL"=>"Timor-Leste","TG"=>"Togo","TO"=>"Tonga","TT"=>"Trinidad and Tobago","TN"=>"Tunisia","TR"=>"Turkey","TM"=>"Turkmenistan","TC"=>"Turks and Caicos Islands","TV"=>"Tuvalu","UG"=>"Uganda","UA"=>"Ukraine","AE"=>"United Arab Emirates","GB"=>"United Kingdom","US"=>"United States","UY"=>"Uruguay","UZ"=>"Uzbekistan","VU"=>"Vanuatu","VE"=>"Venezuela","VN"=>"Vietnam","VG"=>"Virgin Islands (British)","VI"=>"Virgin Islands (U.S.)","WS"=>"Western Samoa","YE"=>"Yemen","ZM"=>"Zambia","ZW"=>"Zimbabwe"
		);
		$default_countries = array(
			"AF","AL","DZ","AS","AD","AO","AI","AG","AR","AM","AW","AU","AT","AZ","BS","BH","BD","BB","BY","BE","BZ","BJ","BM","BT","BO","BQ","BA","BW","BR","BN","BG","BF","BI","KH","CM","CA","IC","CV","KY","CF","TD","CL","CN","CO","KM","CG","CK","CR","HR","CW","CY","CZ","DK","DJ","DM","DO","EC","EG","SV","GQ","ER","EE","ET","FK","FO","FJ","FI","FR","GF","GA","GM","GE","DE","GH","GI","GR","GL","GD","GP","GU","GT","GG","GN","GW","GY","HT","HN","HK","HU","IS","IN","ID","IQ","IE","IL","IT","CI","JM","JP","JE","JO","KZ","KE","KI","KR","KW","KG","LA","LV","LB","LS","LR","LI","LT","LU","MO","MK","MG","MW","MY","MV","ML","MT","MH","MQ","MR","MU","YT","MX","FM","MD","MC","MN","ME","MS","MA","MZ","MM","NA","NR","NP","NL","NV","NC","NZ","NI","NE","NG","NU","NF","MP","NO","OM","PK","PW","PA","PG","PE","PH","PL","PT","PR","QA","RE","RO","RU","RW","SM","ST","SA","SN","RS","SC","SL","SG","SK","SI","SB","ZA","SS","ES","LK","BL","EU","KN","LC","MF","VC","SD","SR","SZ","SE","CH","PF","TW","TJ","TZ","TH","TL","TG","TO","TT","TN","TR","TM","TC","TV","UG","UA","AE","GB","US","UY","UZ","VU","VE","VN","VG","VI","WS","YE","ZM","ZW"
		);
		$statuses = wc_get_order_statuses();
		$settings['General'] = array(
			'title'					=> __( 'General Settings', 'zonos' ),
			'description'			=> __( 'General Settings.', 'zonos' ),
			'fields'				=> array(
				array(
					'id' 			=> 'is_active',
					'label'			=> __( 'Activate Zonos' , 'zonos' ),
					'description'	=> __( 'Turn on International Checkout.', 'zonos' ),
					'type'			=> 'radio',
					'options'   => array( 'active' =>'Active', 'test' => 'Test Mode', 'disabled' => 'Disabled'),
					'default'		=> 'disabled',
				),
				array(
					'id' 			=> 'email_receipt',
					'label'			=> __( 'Use Zonos email Confirmation', 'zonos' ),
					'description'	=> __( 'Check if you wish to use Zonos email confirmation or leave unchecked for WooCommerce emails.', 'zonos' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'default_status',
					'label'			=> __( 'Default import order status', 'zonos' ),
					'description'	=> __( 'The status to set after an order is validated from Zonos.', 'zonos' ),
					'type'			=> 'select',
					'options' => $statuses,
					'default'		=> 'wc-validated'
				),
				array(
					'id' 			=> 'require_login',
					'label'			=> __( 'Require Login', 'zonos' ),
					'description'	=> __( 'Check if you wish to require login for Zonos Checkout.', 'zonos' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'import_tests',
					'label'			=> __( 'Import Test Orders', 'zonos' ),
					'type'			=> 'checkbox',
					'description'   => __('', 'zonos'),
					'default'		=> ''
				),
				array(
					'id' => 'noship_countries',
					'label' => __( 'No Ship Countries', 'zonos'),
					'description' => __( 'Countries you do not ship to - Hold down control (or cmd for Mac) and click to select multiple options.', 'zonos' ),
					'type' => 'select_multi',
					'options' => $country_array,
					'default' => array('AF','BY','IQ','LB','MP','NF','SD')
				),
				array(
					'id' => 'domestic_countries',
					'label' => __( 'Domestic Countries', 'zonos'),
					'description' => __( 'Countries you consider Domestic - Hold down control (or cmd for Mac) and click to select multiple options.', 'zonos' ),
					'type' => 'select_multi',
					'options' => $country_array,
					'default' => array('US'),
				)
			)
		);

		$settings['account'] = array(
			'title'					=> __( 'Account Settings', 'zonos' ),
			'description'			=> __( 'Zonos Account Settings.', 'zonos' ),
			'fields'				=> array(
				array(
					'id' 			=> 'store_id',
					'label'			=> __( 'Store ID Number' , 'zonos' ),
					'description'	=> __( 'Paste the provided store number here.', 'zonos' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Store ID', 'zonos' )
				),
				array(
					'id' 			=> 'store_key',
					'label'			=> __( 'API Key' , 'zonos' ),
					'description'	=> __( 'Paste the provided API secret security key here.', 'zonos' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Store Key', 'zonos' )
				),
				array(
					'id' 			=> 'subdomain',
					'label'			=> __( 'Hosted Checkout Subdomain' , 'zonos' ),
					'description'	=> __( 'Paste the provided subdomain here.', 'zonos' ),
					'type'			=> 'text',
					'default'		=> 'checkout',
					'placeholder'	=> __( 'My Subdomain', 'zonos' )
				),
			)
		);

		$settings['zonos_hello'] = array (
			'title' => __( 'Zonos Hello', 'zonos' ),
			'description' => __( 'Zonos Hello Settings' ),
			'fields' => array(
                array(
                    'id' 			=> 'zonos_is_active',
                    'label'			=> __( 'Enable Zonos Hello' , 'zonos' ),
                    'description'   => '',
                    'type'			=> 'radio',
                    'options'   => array( 'enabled' =>'Enable', 'disabled' => 'Disable'),
                    'default'		=> 'disabled',
                ),
                array(
                    'id' => 'site_key',
                    'label' => __( 'Site Key', 'zonos' ),
                    'description'   => '',
                    'type' => 'text',
                    'default' => '',
                    'placeholder' => 'Site key from Zonos Hello'
                ),
				array(
					'id' 			=> 'checkout_selectors',
					'label'			=> __( 'Checkout Selectors' , 'zonos' ),
					'description'	=> __( 'Comma separated css selectors for checkout buttons', 'zonos' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> '.wc-proceed-to-checkout'
				),
                array(
                    'id' 			=> 'product_sub_directory',
                    'label'			=> __( 'Product URL Directory' , 'zonos' ),
                    'description'	=> __( 'Should match your products root url directory. By default this is /product/', 'zonos' ),
                    'type'			=> 'text',
                    'default'		=> '/product/',
                    'placeholder'	=> '/product/'
                ),
                array(
                    'id' 			=> 'zonos_price_selectors',
                    'label'			=> __( 'Currency Conversion' , 'zonos' ),
                    'description'	=> __( 'To enable currency conversion add comma separated CSS classes here.', 'zonos' ),
                    'type'			=> 'text',
                    'placeholder'   => '.price, #amount, etc',
                    'default'		=> ''
                ),
                array(
                    'id'          => 'welcome_mat_javascript',
                    'label'       => __( 'Upload Custom JS File ' ),
                    'description' => '',
                    'type'        => 'file',
                    'default'     => '',
                ),
                array(
                    'id'           => 'welcome_mat_javascript_remove',
                    'label'        => null,
                    'description'  => ( get_option( $this->base . 'welcome_mat_javascript_url' ) ) ? __( 'Remove? ' . get_option( 'iglobal_welcome_mat_javascript_url', 'zonos' ) ) : '',
                    'type'         => ( get_option( $this->base . 'welcome_mat_javascript_url' ) ) ? 'checkbox' : 'hidden',
                    'default'      => '',
                    'placeholder'  => '',
                ),
			)
		);

		$settings = apply_filters( $this->parent->_token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function zonos_register_settings () {
		if ( is_array( $this->settings ) ) {

			// Check posted/selected tab
			$current_section = '';
			if ( isset( $_POST['tab'] ) && $_POST['tab'] ) {
				$current_section = $_POST['tab'];
			} else {
				if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
					$current_section = $_GET['tab'];
				}
			}

			foreach ( $this->settings as $section => $data ) {

				if ( $current_section && $current_section != $section ) continue;

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'zonos_settings_section' ), $this->parent->_token . '_settings' );
				foreach ( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if ( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( $this->parent->_token . '_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this->parent->admin, 'zonos_display_field' ), $this->parent->_token . '_settings', $section, array( 'field' => $field, 'prefix' => $this->base ) );
				}

				if ( ! $current_section ) break;
			}
		}
	}

	public function zonos_settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function zonos_settings_page () {

		$html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
			$html .= '<h2>' . __( 'Plugin Settings' , 'iglobal' ) . '</h2>' . "\n";

			$tab = '';
			if ( isset( $_GET['tab'] ) && $_GET['tab'] ) {
				$tab .= $_GET['tab'];
			}

			// Show page tabs
			if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

				$html .= '<h2 class="nav-tab-wrapper">' . "\n";

				$c = 0;
				foreach ( $this->settings as $section => $data ) {

					// Set tab class
					$class = 'nav-tab';
					if ( ! isset( $_GET['tab'] ) ) {
						if ( 0 == $c ) {
							$class .= ' nav-tab-active';
						}
					} else {
						if ( isset( $_GET['tab'] ) && $section == $_GET['tab'] ) {
							$class .= ' nav-tab-active';
						}
					}

					// Set tab link
					$tab_link = add_query_arg( array( 'tab' => $section ) );
					if ( isset( $_GET['settings-updated'] ) ) {
						$tab_link = remove_query_arg( 'settings-updated', $tab_link );
					}

					// Output tab
					$html .= '<a href="' . $tab_link . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";

					++$c;
				}

				$html .= '</h2>' . "\n";
			}

			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( $this->parent->_token . '_settings' );
				do_settings_sections( $this->parent->_token . '_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'iglobal' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Iglobal_Plugin_Settings Instance
	 *
	 * Ensures only one instance of Iglobal_Plugin_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Iglobal_Plugin()
	 * @return Main Iglobal_Plugin_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}
