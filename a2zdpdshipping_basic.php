<?php
/**
 * Plugin Name: DPD Rates & Labels
 * Plugin URI: https://myshipi.com/
 * Description: Shipping label, commercial invoice automation included.
 * Version: 2.0.1
 * Author: Shipi
 * Author URI: https://myshipi.com/
 * Developer: aarsiv
 * Developer URI: https://myshipi.com/
 * Text Domain: a2z_dpdshipping
 * Domain Path: /i18n/languages/
 *
 * WC requires at least: 2.6
 * WC tested up to: 6.4
 *
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'A2Z_DPD_PLUGIN_FILE' ) ) {
	define( 'A2Z_DPD_PLUGIN_FILE', __FILE__ );
}

// set HPOS feature compatible by plugin
add_action(
    'before_woocommerce_init',
    function () {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }
);

function shipi_woo_dpd_express_plugin_activation( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        $setting_value = version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
    	// Don't forget to exit() because wp_redirect doesn't exit automatically
    	exit( wp_redirect( admin_url( 'admin.php?page=' . $setting_value  . '&tab=shipping&section=az_dpdshipping' ) ) );
    }
}
add_action( 'activated_plugin', 'shipi_woo_dpd_express_plugin_activation' );

// Include the main WooCommerce class.
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	if( !class_exists('a2z_dpdshipping_parent') ){
		Class a2z_dpdshipping_parent
		{

			public $hpos_enabled = false;
			public $new_prod_editor_enabled = false;
			private $errror = '';
			public function __construct() {
				if (get_option("woocommerce_custom_orders_table_enabled") === "yes") {
					$this->hpos_enabled = true;
				}
				if (get_option("woocommerce_feature_product_block_editor_enabled") === "yes") {
					$this->new_prod_editor_enabled = true;
				}
				add_action( 'woocommerce_shipping_init', array($this,'a2z_dpdshipping_init') );
				add_action( 'init', array($this,'hit_order_status_update') );
				add_filter( 'woocommerce_shipping_methods', array($this,'a2z_dpdshipping_method') );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'a2z_dpdshipping_plugin_action_links' ) );
				add_action( 'add_meta_boxes', array($this, 'create_dpd_shipping_meta_box' ));
				if ($this->hpos_enabled) {
					add_action( 'woocommerce_process_shop_order_meta', array($this, 'hit_create_dpd_shipping'), 10, 1 );
					// add_action( 'woocommerce_process_shop_order_meta', array($this, 'hit_create_dpd_return_shipping'), 10, 1 );
				} else {
					add_action( 'save_post', array($this, 'hit_create_dpd_shipping'), 10, 1 );
					// add_action( 'save_post', array($this, 'hit_create_dpd_return_shipping'), 10, 1 );
				}
				
				// add_action( 'save_post', array($this, 'hit_create_dpd_return_shipping'), 10, 1 );
				// add_filter( 'bulk_actions-edit-shop_order', array($this, 'hit_bulk_order_menu'), 10, 1 );
				// add_filter( 'handle_bulk_actions-edit-shop_order', array($this, 'hit_bulk_create_order'), 10, 3 );
				// add_action( 'admin_notices', array($this, 'shipo_bulk_label_action_admin_notice' ) );
				add_filter( 'woocommerce_product_data_tabs', array($this,'hit_product_data_tab') );
				add_action( 'woocommerce_process_product_meta', array($this,'hit_save_product_options' ));
				add_filter( 'woocommerce_product_data_panels', array($this,'hit_product_option_view') );
				add_action( 'admin_menu', array($this, 'hit_dpd_menu_page' ));
				add_filter( 'manage_edit-shop_order_columns', array($this, 'a2z_wc_new_order_column') );
				// add_action( 'woocommerce_checkout_order_processed', array( $this, 'hit_wc_checkout_order_processed' ) );
				add_action( 'woocommerce_thankyou', array( $this, 'hit_wc_checkout_order_processed' ) );
				add_action( 'woocommerce_order_status_processing', array( $this, 'hit_wc_checkout_order_processed' ) );
				// add_action('woocommerce_order_details_after_order_table', array( $this, 'dpd_track' ) );
				add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_buttons_to_downlaod_shipping_label') );
				add_action('admin_print_styles', array($this, 'hits_admin_scripts'));

				$general_settings = get_option('a2z_dpd_main_settings');
				$general_settings = empty($general_settings) ? array() : $general_settings;

				if(isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes' ){
					add_action( 'woocommerce_product_options_shipping', array($this,'hit_choose_vendor_address' ));
					add_action( 'woocommerce_process_product_meta', array($this,'hit_save_product_meta' ));

					// Edit User Hooks
					add_action( 'edit_user_profile', array($this,'hit_define_dpd_credentails') );
					add_action( 'edit_user_profile_update', array($this, 'save_user_fields' ));

				}

			}
			public function hits_admin_scripts() {
		        global $wp_scripts;
		        wp_enqueue_script('wc-enhanced-select');
		        wp_enqueue_script('chosen');
		        wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');

		    }

			function a2z_wc_new_order_column( $columns ) {
				$columns['hit_dpdshipping'] = 'DPD Express';
				return $columns;
			}

			function show_buttons_to_downlaod_shipping_label( $column ) {
				global $post;

				if ( 'hit_dpdshipping' === $column ) {

					$order    = wc_get_order( $post->ID );
					$json_data = get_option('hit_dpd_values_'.$post->ID);

					if(!empty($json_data)){
						$array_data = json_decode( $json_data, true );
						// echo '<pre>';print_r($array_data);die();
						if(isset($array_data[0])){
							foreach ($array_data as $key => $value) {
								echo '<a href="'.$value['label'].'" target="_blank" class="button button-secondary"><span class="dashicons dashicons-printer" style="vertical-align:sub;"></span></a> ';
								echo ' <a href="'.$value['invoice'].'" target="_blank" class="button button-secondary"><span class="dashicons dashicons-pdf" style="vertical-align:sub;"></span></a><br/>';
							}
						}else{
							echo '<a href="'.$array_data['label'].'" target="_blank" class="button button-secondary"><span class="dashicons dashicons-printer" style="vertical-align:sub;"></span></a> ';
							echo ' <a href="'.$array_data['invoice'].'" target="_blank" class="button button-secondary"><span class="dashicons dashicons-pdf" style="vertical-align:sub;"></span></a>';
						}
					}else{
						echo '-';
					}
				}
			}

			function hit_dpd_menu_page() {
				$general_settings = get_option('a2z_dpd_main_settings');
				if (isset($general_settings['a2z_dpdshipping_integration_key']) && !empty($general_settings['a2z_dpdshipping_integration_key'])) {
					add_menu_page(__( 'DPD Labels', 'a2z_dpdshipping' ), 'DPD Labels', 'manage_options', 'hit-dpd-labels', array($this,'my_label_page_contents'), '', 6);
				}
				add_submenu_page( 'options-general.php', 'DPD Express Config', 'DPD Express Config', 'manage_options', 'hit-dpd-express-configuration', array($this, 'my_admin_page_contents') );

			}
			function my_label_page_contents(){
				$general_settings = get_option('a2z_dpd_main_settings');
				$url = site_url();
				if (isset($general_settings['a2z_dpdshipping_integration_key']) && !empty($general_settings['a2z_dpdshipping_integration_key'])) {
					echo "<iframe style='width: 100%;height: 100vh;' src='https://app.myshipi.com/embed/label.php?shop=".$url."&key=".$general_settings['a2z_dpdshipping_integration_key']."&show=ship'></iframe>";
				}
            }

			function my_admin_page_contents(){
				include_once('controllors/views/a2z_dpdshipping_settings_view.php');
			}

			public function hit_product_data_tab( $tabs) {

				$tabs['hits_dpd_product_options'] = array(
					'label'		=> __( 'Shipi - DPD Options', 'a2z_dpdshipping' ),
					'target'	=> 'hit_dpd_product_options',
					// 'class'		=> array( 'show_if_simple', 'show_if_variable' ),
				);

				return $tabs;

			}

			public function hit_save_product_options( $post_id ){
				if( isset($_POST['hits_dpd_cc']) ){
					$cc = sanitize_text_field($_POST['hits_dpd_cc']);
					update_post_meta( $post_id, 'hits_dpd_cc', (string) esc_html( $cc ) );
					// print_r($post_id);die();
				}
			}

			public function hit_product_option_view(){
				global $woocommerce, $post;
				if ($this->hpos_enabled) {
					$hpos_prod_data = wc_get_product($post->ID);
					$hits_dpd_saved_cc = $hpos_prod_data->get_meta("hits_dpd_cc");
				} else {
					$hits_dpd_saved_cc = get_post_meta( $post->ID, 'hits_dpd_cc', true);
				}
				?>
				<div id='hit_dpd_product_options' class='panel woocommerce_options_panel'>
					<div class='options_group'>
						<p class="form-field">
							<label for="hits_dpd_cc"><?php _e( 'Enter Commodity code', 'a2z_dpdshipping' ); ?></label>
							<span class='woocommerce-help-tip' data-tip="<?php _e('Enter commodity code for product (20 charcters max).','a2z_dpdshipping') ?>"></span>
							<input type='text' id='hits_dpd_cc' name='hits_dpd_cc' maxlength="20" <?php echo (!empty($hits_dpd_saved_cc) ? 'value="'.$hits_dpd_saved_cc.'"' : '');?> style="width: 30%;">
						</p>
					</div>
				</div>
				<?php
			}


			public function save_user_fields($user_id){
				if(isset($_POST['a2z_dpdshipping_country'])){
					$general_settings['a2z_dpdshipping_site_id'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_site_id']) ? $_POST['a2z_dpdshipping_site_id'] : '');
					$general_settings['a2z_dpdshipping_site_pwd'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_site_pwd']) ? $_POST['a2z_dpdshipping_site_pwd'] : '');
					$general_settings['a2z_dpdshipping_acc_no'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_acc_no']) ? $_POST['a2z_dpdshipping_acc_no'] : '');
					$general_settings['a2z_dpdshipping_basic_tok'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_basic_tok']) ? $_POST['a2z_dpdshipping_basic_tok'] : '');
					$general_settings['a2z_dpdshipping_import_no'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_import_no']) ? $_POST['a2z_dpdshipping_import_no'] : '');
					$general_settings['a2z_dpdshipping_shipper_name'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_shipper_name']) ? $_POST['a2z_dpdshipping_shipper_name'] : '');
					$general_settings['a2z_dpdshipping_company'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_company']) ? $_POST['a2z_dpdshipping_company'] : '');
					$general_settings['a2z_dpdshipping_mob_num'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_mob_num']) ? $_POST['a2z_dpdshipping_mob_num'] : '');
					$general_settings['a2z_dpdshipping_email'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_email']) ? $_POST['a2z_dpdshipping_email'] : '');
					$general_settings['a2z_dpdshipping_address1'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_address1']) ? $_POST['a2z_dpdshipping_address1'] : '');
					$general_settings['a2z_dpdshipping_address2'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_address2']) ? $_POST['a2z_dpdshipping_address2'] : '');
					$general_settings['a2z_dpdshipping_city'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_city']) ? $_POST['a2z_dpdshipping_city'] : '');
					$general_settings['a2z_dpdshipping_state'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_state']) ? $_POST['a2z_dpdshipping_state'] : '');
					$general_settings['a2z_dpdshipping_zip'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_zip']) ? $_POST['a2z_dpdshipping_zip'] : '');
					$general_settings['a2z_dpdshipping_country'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_country']) ? $_POST['a2z_dpdshipping_country'] : '');
					$general_settings['a2z_dpdshipping_gstin'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_gstin']) ? $_POST['a2z_dpdshipping_gstin'] : '');
					$general_settings['a2z_dpdshipping_con_rate'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_con_rate']) ? $_POST['a2z_dpdshipping_con_rate'] : '');
					$general_settings['a2z_dpdshipping_def_dom'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_def_dom']) ? $_POST['a2z_dpdshipping_def_dom'] : '');

					$general_settings['a2z_dpdshipping_def_inter'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_def_inter']) ? $_POST['a2z_dpdshipping_def_inter'] : '');

					update_post_meta($user_id,'a2z_dpd_vendor_settings',$general_settings);
				}

			}

			public function hit_define_dpd_credentails( $user ){
				global $dpd_core;
				$main_settings = get_option('a2z_dpd_main_settings');
				$main_settings = empty($main_settings) ? array() : $main_settings;
				$allow = false;

				if(!isset($main_settings['a2z_dpdshipping_v_roles'])){
					return;
				}else{
					foreach ($user->roles as $value) {
						if(in_array($value, $main_settings['a2z_dpdshipping_v_roles'])){
							$allow = true;
						}
					}
				}

				if(!$allow){
					return;
				}

				$general_settings = get_post_meta($user->ID,'a2z_dpd_vendor_settings',true);
				$general_settings = empty($general_settings) ? array() : $general_settings;
				$countires =  array(
									'AF' => 'Afghanistan',
									'AL' => 'Albania',
									'DZ' => 'Algeria',
									'AS' => 'American Samoa',
									'AD' => 'Andorra',
									'AO' => 'Angola',
									'AI' => 'Anguilla',
									'AG' => 'Antigua and Barbuda',
									'AR' => 'Argentina',
									'AM' => 'Armenia',
									'AW' => 'Aruba',
									'AU' => 'Australia',
									'AT' => 'Austria',
									'AZ' => 'Azerbaijan',
									'BS' => 'Bahamas',
									'BH' => 'Bahrain',
									'BD' => 'Bangladesh',
									'BB' => 'Barbados',
									'BY' => 'Belarus',
									'BE' => 'Belgium',
									'BZ' => 'Belize',
									'BJ' => 'Benin',
									'BM' => 'Bermuda',
									'BT' => 'Bhutan',
									'BO' => 'Bolivia',
									'BA' => 'Bosnia and Herzegovina',
									'BW' => 'Botswana',
									'BR' => 'Brazil',
									'VG' => 'British Virgin Islands',
									'BN' => 'Brunei',
									'BG' => 'Bulgaria',
									'BF' => 'Burkina Faso',
									'BI' => 'Burundi',
									'KH' => 'Cambodia',
									'CM' => 'Cameroon',
									'CA' => 'Canada',
									'CV' => 'Cape Verde',
									'KY' => 'Cayman Islands',
									'CF' => 'Central African Republic',
									'TD' => 'Chad',
									'CL' => 'Chile',
									'CN' => 'China',
									'CO' => 'Colombia',
									'KM' => 'Comoros',
									'CK' => 'Cook Islands',
									'CR' => 'Costa Rica',
									'HR' => 'Croatia',
									'CU' => 'Cuba',
									'CY' => 'Cyprus',
									'CZ' => 'Czech Republic',
									'DK' => 'Denmark',
									'DJ' => 'Djibouti',
									'DM' => 'Dominica',
									'DO' => 'Dominican Republic',
									'TL' => 'East Timor',
									'EC' => 'Ecuador',
									'EG' => 'Egypt',
									'SV' => 'El Salvador',
									'GQ' => 'Equatorial Guinea',
									'ER' => 'Eritrea',
									'EE' => 'Estonia',
									'ET' => 'Ethiopia',
									'FK' => 'Falkland Islands',
									'FO' => 'Faroe Islands',
									'FJ' => 'Fiji',
									'FI' => 'Finland',
									'FR' => 'France',
									'GF' => 'French Guiana',
									'PF' => 'French Polynesia',
									'GA' => 'Gabon',
									'GM' => 'Gambia',
									'GE' => 'Georgia',
									'DE' => 'Germany',
									'GH' => 'Ghana',
									'GI' => 'Gibraltar',
									'GR' => 'Greece',
									'GL' => 'Greenland',
									'GD' => 'Grenada',
									'GP' => 'Guadeloupe',
									'GU' => 'Guam',
									'GT' => 'Guatemala',
									'GG' => 'Guernsey',
									'GN' => 'Guinea',
									'GW' => 'Guinea-Bissau',
									'GY' => 'Guyana',
									'HT' => 'Haiti',
									'HN' => 'Honduras',
									'HK' => 'Hong Kong',
									'HU' => 'Hungary',
									'IS' => 'Iceland',
									'IN' => 'India',
									'ID' => 'Indonesia',
									'IR' => 'Iran',
									'IQ' => 'Iraq',
									'IE' => 'Ireland',
									'IL' => 'Israel',
									'IT' => 'Italy',
									'CI' => 'Ivory Coast',
									'JM' => 'Jamaica',
									'JP' => 'Japan',
									'JE' => 'Jersey',
									'JO' => 'Jordan',
									'KZ' => 'Kazakhstan',
									'KE' => 'Kenya',
									'KI' => 'Kiribati',
									'KW' => 'Kuwait',
									'KG' => 'Kyrgyzstan',
									'LA' => 'Laos',
									'LV' => 'Latvia',
									'LB' => 'Lebanon',
									'LS' => 'Lesotho',
									'LR' => 'Liberia',
									'LY' => 'Libya',
									'LI' => 'Liechtenstein',
									'LT' => 'Lithuania',
									'LU' => 'Luxembourg',
									'MO' => 'Macao',
									'MK' => 'Macedonia',
									'MG' => 'Madagascar',
									'MW' => 'Malawi',
									'MY' => 'Malaysia',
									'MV' => 'Maldives',
									'ML' => 'Mali',
									'MT' => 'Malta',
									'MH' => 'Marshall Islands',
									'MQ' => 'Martinique',
									'MR' => 'Mauritania',
									'MU' => 'Mauritius',
									'YT' => 'Mayotte',
									'MX' => 'Mexico',
									'FM' => 'Micronesia',
									'MD' => 'Moldova',
									'MC' => 'Monaco',
									'MN' => 'Mongolia',
									'ME' => 'Montenegro',
									'MS' => 'Montserrat',
									'MA' => 'Morocco',
									'MZ' => 'Mozambique',
									'MM' => 'Myanmar',
									'NA' => 'Namibia',
									'NR' => 'Nauru',
									'NP' => 'Nepal',
									'NL' => 'Netherlands',
									'NC' => 'New Caledonia',
									'NZ' => 'New Zealand',
									'NI' => 'Nicaragua',
									'NE' => 'Niger',
									'NG' => 'Nigeria',
									'NU' => 'Niue',
									'KP' => 'North Korea',
									'MP' => 'Northern Mariana Islands',
									'NO' => 'Norway',
									'OM' => 'Oman',
									'PK' => 'Pakistan',
									'PW' => 'Palau',
									'PA' => 'Panama',
									'PG' => 'Papua New Guinea',
									'PY' => 'Paraguay',
									'PE' => 'Peru',
									'PH' => 'Philippines',
									'PL' => 'Poland',
									'PT' => 'Portugal',
									'PR' => 'Puerto Rico',
									'QA' => 'Qatar',
									'CG' => 'Republic of the Congo',
									'RE' => 'Reunion',
									'RO' => 'Romania',
									'RU' => 'Russia',
									'RW' => 'Rwanda',
									'SH' => 'Saint Helena',
									'KN' => 'Saint Kitts and Nevis',
									'LC' => 'Saint Lucia',
									'VC' => 'Saint Vincent and the Grenadines',
									'WS' => 'Samoa',
									'SM' => 'San Marino',
									'ST' => 'Sao Tome and Principe',
									'SA' => 'Saudi Arabia',
									'SN' => 'Senegal',
									'RS' => 'Serbia',
									'SC' => 'Seychelles',
									'SL' => 'Sierra Leone',
									'SG' => 'Singapore',
									'SK' => 'Slovakia',
									'SI' => 'Slovenia',
									'SB' => 'Solomon Islands',
									'SO' => 'Somalia',
									'ZA' => 'South Africa',
									'KR' => 'South Korea',
									'SS' => 'South Sudan',
									'ES' => 'Spain',
									'LK' => 'Sri Lanka',
									'SD' => 'Sudan',
									'SR' => 'Suriname',
									'SZ' => 'Swaziland',
									'SE' => 'Sweden',
									'CH' => 'Switzerland',
									'SY' => 'Syria',
									'TW' => 'Taiwan',
									'TJ' => 'Tajikistan',
									'TZ' => 'Tanzania',
									'TH' => 'Thailand',
									'TG' => 'Togo',
									'TO' => 'Tonga',
									'TT' => 'Trinidad and Tobago',
									'TN' => 'Tunisia',
									'TR' => 'Turkey',
									'TC' => 'Turks and Caicos Islands',
									'TV' => 'Tuvalu',
									'VI' => 'U.S. Virgin Islands',
									'UG' => 'Uganda',
									'UA' => 'Ukraine',
									'AE' => 'United Arab Emirates',
									'GB' => 'United Kingdom',
									'US' => 'United States',
									'UY' => 'Uruguay',
									'UZ' => 'Uzbekistan',
									'VU' => 'Vanuatu',
									'VE' => 'Venezuela',
									'VN' => 'Vietnam',
									'YE' => 'Yemen',
									'ZM' => 'Zambia',
									'ZW' => 'Zimbabwe',
								);
				 $_dpd_carriers = array();

				 echo '<hr><h3 class="heading">DPD Express - <a href="https://myshipi.com/" target="_blank">Shipi</a></h3>';
				    ?>

				    <table class="form-table">
						<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DPD Integration Team will give this details to you.','a2z_dpdshipping') ?>"></span>	<?php _e('DPD Delis ID / User','a2z_dpdshipping') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','a2z_dpdshipping') ?> </p>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_site_id" value="<?php echo (isset($general_settings['a2z_dpdshipping_site_id'])) ? $general_settings['a2z_dpdshipping_site_id'] : ''; ?>">
						</td>

					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DPD Integration Team will give this details to you.','a2z_dpdshipping') ?>"></span>	<?php _e('DPD Password','a2z_dpdshipping') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','a2z_dpdshipping') ?> </p>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_site_pwd" value="<?php echo (isset($general_settings['a2z_dpdshipping_site_pwd'])) ? $general_settings['a2z_dpdshipping_site_pwd'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DPD Integration Team will give this details to you.','a2z_dpdshipping') ?>"></span>	<?php _e('DPD Basic Token','a2z_dpdshipping') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','a2z_dpdshipping') ?> </p>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_basic_tok" value="<?php echo (isset($general_settings['a2z_dpdshipping_basic_tok'])) ? $general_settings['a2z_dpdshipping_basic_tok'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DPD Integration Team will give this details to you.','a2z_dpdshipping') ?>"></span>	<?php _e('DPD Customer Number','a2z_dpdshipping') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','a2z_dpdshipping') ?> </p>
						</td>
						<td>

							<input type="text" name="a2z_dpdshipping_acc_no" value="<?php echo (isset($general_settings['a2z_dpdshipping_acc_no'])) ? $general_settings['a2z_dpdshipping_acc_no'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('This is for proceed with return labels.','a2z_dpdshipping') ?>"></span>	<?php _e('DPD Import Account Number','a2z_dpdshipping') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','a2z_dpdshipping') ?> </p>
						</td>
						<td>

							<input type="text" name="a2z_dpdshipping_import_no" value="<?php echo (isset($general_settings['a2z_dpdshipping_import_no'])) ? $general_settings['a2z_dpdshipping_import_no'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipping Person Name','a2z_dpdshipping') ?>"></span>	<?php _e('Shipper Name','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_shipper_name" value="<?php echo (isset($general_settings['a2z_dpdshipping_shipper_name'])) ? $general_settings['a2z_dpdshipping_shipper_name'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Company Name.','a2z_dpdshipping') ?>"></span>	<?php _e('Company Name','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_company" value="<?php echo (isset($general_settings['a2z_dpdshipping_company'])) ? $general_settings['a2z_dpdshipping_company'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Mobile / Contact Number.','a2z_dpdshipping') ?>"></span>	<?php _e('Contact Number','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_mob_num" value="<?php echo (isset($general_settings['a2z_dpdshipping_mob_num'])) ? $general_settings['a2z_dpdshipping_mob_num'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Email Address of the Shipper.','a2z_dpdshipping') ?>"></span>	<?php _e('Email Address','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_email" value="<?php echo (isset($general_settings['a2z_dpdshipping_email'])) ? $general_settings['a2z_dpdshipping_email'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 1 of the Shipper from Address.','a2z_dpdshipping') ?>"></span>	<?php _e('Address Line 1','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_address1" value="<?php echo (isset($general_settings['a2z_dpdshipping_address1'])) ? $general_settings['a2z_dpdshipping_address1'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 2 of the Shipper from Address.','a2z_dpdshipping') ?>"></span>	<?php _e('Address Line 2','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_address2" value="<?php echo (isset($general_settings['a2z_dpdshipping_address2'])) ? $general_settings['a2z_dpdshipping_address2'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%;padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the Shipper from address.','a2z_dpdshipping') ?>"></span>	<?php _e('City','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_city" value="<?php echo (isset($general_settings['a2z_dpdshipping_city'])) ? $general_settings['a2z_dpdshipping_city'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the Shipper from address.','a2z_dpdshipping') ?>"></span>	<?php _e('State (Two Digit String)','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_state" value="<?php echo (isset($general_settings['a2z_dpdshipping_state'])) ? $general_settings['a2z_dpdshipping_state'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal/Zip Code.','a2z_dpdshipping') ?>"></span>	<?php _e('Postal/Zip Code','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_zip" value="<?php echo (isset($general_settings['a2z_dpdshipping_zip'])) ? $general_settings['a2z_dpdshipping_zip'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the Shipper from Address.','a2z_dpdshipping') ?>"></span>	<?php _e('Country','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<select name="a2z_dpdshipping_country" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($countires as $key => $value)
								{

									if(isset($general_settings['a2z_dpdshipping_country']) && ($general_settings['a2z_dpdshipping_country'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value." [". $dpd_core[$key]['currency'] ."]</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value." [". $dpd_core[$key]['currency'] ."]</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('GSTIN/VAT No.','a2z_dpdshipping') ?>"></span>	<?php _e('GSTIN/VAT No','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_gstin" value="<?php echo (isset($general_settings['a2z_dpdshipping_gstin'])) ? $general_settings['a2z_dpdshipping_gstin'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Conversion Rate from Site Currency to DPD Currency.','a2z_dpdshipping') ?>"></span>	<?php _e('Conversion Rate from Site Currency to DPD Currency ( Ignore if auto conversion is Enabled )','a2z_dpdshipping') ?></h4>
						</td>
						<td>
							<input type="text" name="a2z_dpdshipping_con_rate" value="<?php echo (isset($general_settings['a2z_dpdshipping_con_rate'])) ? $general_settings['a2z_dpdshipping_con_rate'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Default Domestic Express Shipping.','a2z_dpdshipping') ?>"></span>	<?php _e('Default Domestic Service','a2z_dpdshipping') ?></h4>
							<p><?php _e('This will be used while shipping label generation.','a2z_dpdshipping') ?></p>
						</td>
						<td>
							<select name="a2z_dpdshipping_def_dom" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($_dpd_carriers as $key => $value)
								{
									if(isset($general_settings['a2z_dpdshipping_def_dom']) && ($general_settings['a2z_dpdshipping_def_dom'] == $key))
									{
										echo "<option value=".$key." selected='true'>[".$key."] ".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">[".$key."] ".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Default International Shipping.','a2z_dpdshipping') ?>"></span>	<?php _e('Default International Service','a2z_dpdshipping') ?></h4>
							<p><?php _e('This will be used while shipping label generation.','a2z_dpdshipping') ?></p>
						</td>
						<td>
							<select name="a2z_dpdshipping_def_inter" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($_dpd_carriers as $key => $value)
								{
									if(isset($general_settings['a2z_dpdshipping_def_inter']) && ($general_settings['a2z_dpdshipping_def_inter'] == $key))
									{
										echo "<option value=".$key." selected='true'>[".$key."] ".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">[".$key."] ".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
				    </table>
				    <hr>
				    <?php
			}
			public function hit_save_product_meta( $post_id ){
				if(isset( $_POST['dpd_express_shipment'])){
					$dpd_express_shipment = sanitize_text_field($_POST['dpd_express_shipment']);
					if( !empty( $dpd_express_shipment ) )
					update_post_meta( $post_id, 'dpd_express_address', (string) esc_html( $dpd_express_shipment ) );
				}

			}
			public function hit_choose_vendor_address(){
				global $woocommerce, $post;
				$hit_multi_vendor = get_option('hit_multi_vendor');
				$hit_multi_vendor = empty($hit_multi_vendor) ? array() : $hit_multi_vendor;
				if ($this->hpos_enabled) {
					$hpos_prod_data = wc_get_product($post->ID);
					$selected_addr = $hpos_prod_data->get_meta("dpd_express_address");
				} else {
					$selected_addr = get_post_meta( $post->ID, 'dpd_express_address', true);
				}

				$main_settings = get_option('a2z_dpd_main_settings');
				$main_settings = empty($main_settings) ? array() : $main_settings;
				if(!isset($main_settings['a2z_dpdshipping_v_roles']) || empty($main_settings['a2z_dpdshipping_v_roles'])){
					return;
				}
				$v_users = get_users( [ 'role__in' => $main_settings['a2z_dpdshipping_v_roles'] ] );

				?>
				<div class="options_group">
				<p class="form-field dpd_express_shipment">
					<label for="dpd_express_shipment"><?php _e( 'DPD Express Account', 'woocommerce' ); ?></label>
					<select id="dpd_express_shipment" style="width:240px;" name="dpd_express_shipment" class="wc-enhanced-select" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>">
						<option value="default" >Default Account</option>
						<?php
							if ( $v_users ) {
								foreach ( $v_users as $value ) {
									echo '<option value="' .  $value->data->ID  . '" '.($selected_addr == $value->data->ID ? 'selected="true"' : '').'>' . $value->data->display_name . '</option>';
								}
							}
						?>
					</select>
					</p>
				</div>
				<?php
			}

			public function a2z_dpdshipping_init()
			{
				include_once("controllors/a2z_dpdshipping_init.php");
			}
			public function hit_order_status_update(){
				global $woocommerce;
				if(isset($_GET['shipi_key'])){
					$shipi_key = sanitize_text_field($_GET['shipi_key']);
					if($shipi_key == 'fetch'){
						echo json_encode(array(get_transient('hitshipo_dpd_express_nonce_temp')));
						die();
					}
				}

				if(isset($_GET['hitshipo_integration_key']) && isset($_GET['hitshipo_action'])){
					$integration_key = sanitize_text_field($_GET['hitshipo_integration_key']);
					$hitshipo_action = sanitize_text_field($_GET['hitshipo_action']);
					$general_settings = get_option('a2z_dpd_main_settings');
					$general_settings = empty($general_settings) ? array() : $general_settings;
					if(isset($general_settings['a2z_dpdshipping_integration_key']) && $integration_key == $general_settings['a2z_dpdshipping_integration_key']){
						if($hitshipo_action == 'stop_working'){
							update_option('a2z_dpd_express_working_status', 'stop_working');
						}else if ($hitshipo_action = 'start_working'){
							update_option('a2z_dpd_express_working_status', 'start_working');
						}
					}

				}

				if(isset($_GET['h1t_updat3_0rd3r']) && isset($_GET['key']) && isset($_GET['action'])){

					$order_id = sanitize_text_field($_GET['h1t_updat3_0rd3r']);
					$key = sanitize_text_field($_GET['key']);
					$action = sanitize_text_field($_GET['action']);
					$order_ids = explode(",",$order_id);
					$general_settings = get_option('a2z_usps_main_settings',array());

					if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] == $key){
						if($action == 'processing'){
							foreach ($order_ids as $order_id) {
								$order = wc_get_order( $order_id );
								$order->update_status( 'processing' );
							}
						}else if($action == 'completed'){
							foreach ($order_ids as $order_id) {
								  $order = wc_get_order( $order_id );
								  $order->update_status( 'completed' );

							}
						}
					}
					die();
				}

				if(isset($_GET['h1t_updat3_sh1pp1ng']) && isset($_GET['key']) && isset($_GET['user_id']) && isset($_GET['carrier']) && isset($_GET['track'])){

					$order_id = sanitize_text_field($_GET['h1t_updat3_sh1pp1ng']);
					$key = sanitize_text_field($_GET['key']);
					$general_settings = get_option('a2z_dpd_main_settings',array());
					$user_id = sanitize_text_field($_GET['user_id']);
					$carrier = sanitize_text_field($_GET['carrier']);
					$track = sanitize_text_field($_GET['track']);
					$labels = urldecode(sanitize_text_field($_GET['labels']));
					$labels = rtrim($labels,",");
					// $labels = explode(",",$labels);
					$output['status'] = 'success';
					$output['tracking_num'] = $track;
					$output['label'] = $labels;//"https://app.myshipi.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_label.pdf";
					$output['invoice'] = "https://app.myshipi.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_invoice.pdf";
					$output['label_count'] = sanitize_text_field($_GET['label_count']);
					$output['user_id'] = 'default';
					$result_arr = array();
					if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] == $key){

						if(false){//isset($_GET['label'])
							$output['user_id'] = sanitize_text_field($user_id);
							if(isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes'){
								$result_arr = json_decode(get_option('hit_dpd_values_'.$order_id, array()));
							}

							$result_arr[] = $output;


						}else{
							$result_arr[] = $output;
						}

						update_option('hit_dpd_values_'.$order_id, json_encode($result_arr));


					}

					die();
				}
			}
			public function a2z_dpdshipping_method( $methods )
			{
				if (is_admin() && !is_ajax() || apply_filters('a2z_shipping_method_enabled', true)) {
					$methods['a2z_dpdshipping'] = 'a2z_dpdshipping';
				}

				return $methods;
			}

			public function a2z_dpdshipping_plugin_action_links($links)
			{
				$setting_value = version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=' . $setting_value  . '&tab=shipping&section=az_dpdshipping' ) . '" style="color:green;">' . __( 'Configure', 'a2z_dpdshipping' ) . '</a>',
					'<a href="https://app.myshipi.com/support" target="_blank" >' . __('Support', 'a2z_dpdshipping') . '</a>'
					);
				return array_merge( $plugin_links, $links );
			}
			public function create_dpd_shipping_meta_box() {
				$meta_scrn = $this->hpos_enabled ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
	       		add_meta_box( 'hit_create_dpd_shipping', __('DPD Shipping Label','a2z_dpdshipping'), array($this, 'create_dpd_shipping_label_genetation'), $meta_scrn, 'side', 'core' );
	       		// add_meta_box( 'hit_create_dpd_return_shipping', __('DPD Return Label','a2z_dpdshipping'), array($this, 'create_dpd_return_label_genetation'), $meta_scrn, 'side', 'core' );
		    }
		    public function create_dpd_shipping_label_genetation($post){
		    	// print_r('expression');
		    	// die();
		        if(!$this->hpos_enabled && $post->post_type !='shop_order' ){
		    		return;
		    	}
		    	$order = (!$this->hpos_enabled) ? wc_get_order( $post->ID ) : $post;
		    	$order_id = $order->get_id();
		        $_dpd_carriers = array(
								//"Public carrier name" => "technical name",

							);

		        $general_settings = get_option('a2z_dpd_main_settings',array());

		        $items = $order->get_items();

    		    $custom_settings = array();
		    	$custom_settings['default'] =  array();
		    	$vendor_settings = array();

		    	$pack_products = array();

				foreach ( $items as $item ) {
					$product_data = $item->get_data();
				    $product = array();
				    $product['product_name'] = $product_data['name'];
				    $product['product_quantity'] = $product_data['quantity'];
				    $product['product_id'] = $product_data['product_id'];

				    $pack_products[] = $product;

				}

				if(isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes' && isset($general_settings['a2z_dpdshipping_v_labels']) && $general_settings['a2z_dpdshipping_v_labels'] == 'yes'){
					// Multi Vendor Enabled
					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];

						if ($this->hpos_enabled) {
							$hpos_prod_data = wc_get_product($product_id);
							$dpd_account = $hpos_prod_data->get_meta("dpd_express_address");
						} else {
							$dpd_account = get_post_meta($product_id,'dpd_express_address', true);
						}
						if(empty($dpd_account) || $dpd_account == 'default'){
							$dpd_account = 'default';
							if (!isset($vendor_settings[$dpd_account])) {
								$vendor_settings[$dpd_account] = $custom_settings['default'];
							}

							$vendor_settings[$dpd_account]['products'][] = $value;
						}

						if($dpd_account != 'default'){
							$user_account = get_post_meta($dpd_account,'a2z_dpd_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dpd_account])){

									$vendor_settings[$dpd_account] = $custom_settings['default'];
									unset($value['product_id']);
									$vendor_settings[$dpd_account]['products'][] = $value;
								}
							}else{
								$dpd_account = 'default';
								$vendor_settings[$dpd_account] = $custom_settings['default'];
								$vendor_settings[$dpd_account]['products'][] = $value;
							}
						}

					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}

		       	$json_data = get_option('hit_dpd_values_'.$order_id);
		       	$pickup_json_data = get_option('hit_dpd_pickup_values_'.$order_id);
		       	// echo $pickup_json_data;
		       	$notice = get_option('hit_dpd_status_'.$order_id, null);
		        if($notice && $notice != 'success'){
		        	echo "<p style='color:red'>".$notice."</p>";
		        	delete_option('hit_dpd_status_'.$order_id);
		        }
		        if(!empty($json_data)){
   					$array_data = json_decode( $json_data, true );
   					// echo '<pre>';print_r($array_data);die();
		       		if(isset($array_data[0])){
		       			foreach ($array_data as $key => $value) {
		       				if(isset($value['user_id'])){
		       					unset($custom_settings[$value['user_id']]);
		       				}
		       				if(isset($value['user_id']) && $value['user_id'] == 'default'){
		       					echo '<br/><b>Default Account</b><br/>';
		       				}else{
		       					$user = get_user_by( 'id', $value['user_id'] );
		       					echo '<br/><b>Account:</b> <small>'.$user->display_name.'</small><br/>';
							   }
							//    print_r();
							//    die();
							   $label_arr = explode(",",$value['label']);
							   foreach($label_arr as $k=>$labl){
								echo '<a href="'.$labl.'" target="_blank" style="background:#d30b2a; color: #ffffff;border-color: #d30b2a;box-shadow: 0px 1px 0px #d30b2a;text-shadow: 0px 1px 0px #ffffff;margin-top:3px;" class="button button-primary"> Shipping Label</a> ';
							   }
							   echo ' <a href="'.$value['invoice'].'" target="_blank" class="button button-primary" style="margin-top:3px;"> Invoice </a><br/>';
							   echo '<br/><button name="hit_dpd_reset" class="button button-secondary" style="margin-top:3px;"> Reset Shipments</button>';

		       			}
		       		}else{
		       			$custom_settings = array();
		       			echo '<a href="'.$array_data['label'].'" target="_blank" style="background:#d30b2a; color: #ffffff;border-color: #d30b2a;box-shadow: 0px 1px 0px #d30b2a;text-shadow: 0px 1px 0px #ffffff;" class="button button-primary"> Shipping Label</a> ';
							   echo ' <a href="'.$array_data['invoice'].'" target="_blank" class="button button-primary"> Invoice </a>';
							   echo '<br/><button name="hit_dpd_reset" class="button button-secondary" style="margin-top:3px;"> Reset Shipments</button>';
		       		}
   				}
	       		foreach ($custom_settings as $ukey => $value) {
	       			if($ukey == 'default'){
	       				echo '<br/><b>Default Account</b>';
				        // echo '<br/><select name="hit_dpd_express_service_code_default">';
				        // if(!empty($general_settings['a2z_dpdshipping_carrier'])){
				        // 	foreach ($general_settings['a2z_dpdshipping_carrier'] as $key => $value) {
				        // 		echo "<option value='".$key."'>".$key .' - ' .$_dpd_carriers[$key]."</option>";
				        // 	}
				        // }
				        // echo '</select>';
				        echo '<br/><b>Shipment Content</b>';

				        echo '<br/><input type="text" style="width:250px;margin-bottom:10px;"  name="hit_dpd_shipment_content_default" placeholder="Shipment content" value="' . (($general_settings['a2z_dpdshipping_ship_content']) ? $general_settings['a2z_dpdshipping_ship_content'] : "") . '" >';

				        // echo '<input type="checkbox" name="hit_dpd_add_pickup_default" checked> <b>Create Pickup along with shipment.</b><br/><br/>';
				        echo '<button name="hit_dpd_create_label" value="default" style="background:#d30b2a; color: #ffffff;border-color: #d30b2a;box-shadow: 0px 1px 0px #d30b2a;text-shadow: 0px 1px 0px #ffffff;" class="button button-primary">Create Shipment</button>';

	       			}else{

	       				$user = get_user_by( 'id', $ukey );
		       			echo '<br/><b>Account:</b> <small>'.$user->display_name.'</small>';
				        // echo '<br/><select name="hit_dpd_express_service_code_'.$ukey.'">';
				        // if(!empty($general_settings['a2z_dpdshipping_carrier'])){
				        // 	foreach ($general_settings['a2z_dpdshipping_carrier'] as $key => $value) {
				        // 		echo "<option value='".$key."'>".$key .' - ' .$_dpd_carriers[$key]."</option>";
				        // 	}
				        // }
				        // echo '</select>';
				        echo '<br/><b>Shipment Content</b>';

				        echo '<br/><input type="text" style="width:250px;margin-bottom:10px;"  name="hit_dpd_shipment_content_'.$ukey.'" placeholder="Shipment content" value="' . (($general_settings['a2z_dpdshipping_ship_content']) ? $general_settings['a2z_dpdshipping_ship_content'] : "") . '" >';

				        // echo '<input type="checkbox" name="hit_dpd_add_pickup_'.$ukey.'" checked> <b>Create Pickup along with shipment.</b><br/><br/>';
				        echo '<button name="hit_dpd_create_label" value="'.$ukey.'" style="background:#d30b2a; color: #ffffff;border-color: #d30b2a;box-shadow: 0px 1px 0px #d30b2a;text-shadow: 0px 1px 0px #ffffff;" class="button button-primary">Create Shipment</button><br/>';

	       			}

	       		}

		        // if (!empty($pickup_json_data) && empty($json_data)) {
		        // 	$pickup_array_data = json_decode( $pickup_json_data, true );
		        // 	if (isset($pickup_array_data['status']) && $pickup_array_data['status'] == "failed") {
		        // 		echo "<br/>Pickup creation failed<br/>";
		        // 	}else{
		        // 	echo '<h4>DPD pickup details:</h4>';
		        // 	echo '<b>Confirmation No : </b>'.$pickup_array_data['confirm_no'].'<br/>';
		        // 	echo '<b>Ready By Time : </b>'.$pickup_array_data['ready_time'].'<br/>';
		        // 	echo '<b>Pickup Date : </b>'.$pickup_array_data['pickup_date'].'<br/>';
		        // 	}
		        // }else {
		        // 	echo '<h4>Pickup request can only be created with shipment request</h4>';
		        // }

		       	// if(!empty($json_data)){

		       	// 	echo '<br/><button name="hit_dpd_reset" class="button button-secondary" style="margin-top:3px;"> Reset Shipments</button>';
		       	// 	if (!empty($pickup_json_data)) {
			    //     	$pickup_array_data = json_decode( $pickup_json_data, true );
			    //     	if (isset($pickup_array_data['status']) && $pickup_array_data['status'] == "failed") {
			    //     		echo "<br/><p style='color:red'>(Note: Manual pickup scheduling is currently not available. Recreating the shipment will reduce HITShippo balance.)</p>";
			    //     	}else{
			    //     	echo '<h4>DPD pickup details:</h4>';
			    //     	echo '<b>Confirmation No :</b>'.$pickup_array_data['confirm_no'].'<br/>';
			    //     	echo '<b>Ready By Time :</b>'.$pickup_array_data['ready_time'].'<br/>';
			    //     	echo '<b>Pickup Date :</b>'.$pickup_array_data['pickup_date'].'<br/>';
			    //     	}
			    //     }else {
			    //     	echo '<br/><br/>Pickup Not Created';
			    //     }
		       	// }

		    }

		    public function create_dpd_return_label_genetation($post){
		    	// print_r('expression');
		    	// die();
		        if(!$this->hpos_enabled && $post->post_type !='shop_order' ){
		    		return;
		    	}
		    	$order = (!$this->hpos_enabled) ? wc_get_order( $post->ID ) : $post;
		    	$order_id = $order->get_id();
		        $_dpd_carriers = array(
								//"Public carrier name" => "technical name",
								'1'                    => 'DOMESTIC EXPRESS 12:00',
								'2'                    => 'B2C',
								'3'                    => 'B2C',
								'4'                    => 'JETLINE',
								'5'                    => 'SPRINTLINE',
								'7'                    => 'EXPRESS EASY',
								'8'                    => 'EXPRESS EASY',
								'9'                    => 'EUROPACK',
								'B'                    => 'BREAKBULK EXPRESS',
								'C'                    => 'MEDICAL EXPRESS',
								'D'                    => 'EXPRESS WORLDWIDE',
								'E'                    => 'EXPRESS 9:00',
								'F'                    => 'FREIGHT WORLDWIDE',
								'G'                    => 'DOMESTIC ECONOMY SELECT',
								'H'                    => 'ECONOMY SELECT',
								'I'                    => 'DOMESTIC EXPRESS 9:00',
								'J'                    => 'JUMBO BOX',
								'K'                    => 'EXPRESS 9:00',
								'L'                    => 'EXPRESS 10:30',
								'M'                    => 'EXPRESS 10:30',
								'N'                    => 'DOMESTIC EXPRESS',
								'O'                    => 'DOMESTIC EXPRESS 10:30',
								'P'                    => 'EXPRESS WORLDWIDE',
								'Q'                    => 'MEDICAL EXPRESS',
								'R'                    => 'GLOBALMAIL BUSINESS',
								'S'                    => 'SAME DAY',
								'T'                    => 'EXPRESS 12:00',
								'U'                    => 'EXPRESS WORLDWIDE',
								'V'                    => 'EUROPACK',
								'W'                    => 'ECONOMY SELECT',
								'X'                    => 'EXPRESS ENVELOPE',
								'Y'                    => 'EXPRESS 12:00'
							);

		        $general_settings = get_option('a2z_dpd_main_settings',array());

		       	$json_data = get_option('hit_dpd_return_values_'.$order_id);
		       	if(empty($json_data)){

			        echo '<b>Choose Service to Return</b>';
			        echo '<br/><select name="hit_dpd_express_return_service_code">';
			        if(!empty($general_settings['a2z_dpdshipping_carrier'])){
			        	foreach ($general_settings['a2z_dpdshipping_carrier'] as $key => $value) {
			        		echo "<option value='".$key."'>".$key .' - ' .$_dpd_carriers[$key]."</option>";
			        	}
			        }
			        echo '</select>';


			        echo '<br/><b>Products to return</b>';
			        echo '<br/>';
			        echo '<table>';
			        $items = $order->get_items();
					foreach ( $items as $item ) {
						$product_data = $item->get_data();

					    $product_variation_id = $item->get_variation_id();
					    $product_id = $product_data['product_id'];
					    if(!empty($product_variation_id) && $product_variation_id != 0){
					    	$product_id = $product_variation_id;
					    }

					    echo "<tr><td><input type='checkbox' name='return_products[]' checked value='".$product_id."'>
					    	</td>";
					    echo "<td style='width:150px;'><small title='".$product_data['name']."'>". substr($product_data['name'],0,7)."</small></td>";
					    echo "<td><input type='number' name='qty_products[".$product_id."]' style='width:50px;' value='".$product_data['quantity']."'></td>";
					    echo "</tr>";


					}
			        echo '</table><br/>';

			        $notice = get_option('hit_dpd_return_status_'.$order_id, null);
			        if($notice && $notice != 'success'){
			        	echo "<p style='color:red'>".$notice."</p>";
			        	delete_option('hit_dpd_return_status_'.$order_id);
			        }

			        echo '<button name="hit_dpd_create_return_label" style="background:#d30b2a; color: #ffffff;border-color: #d30b2a;box-shadow: 0px 1px 0px #d30b2a;text-shadow: 0px 1px 0px #ffffff;" class="button button-primary">Create Return Shipment</button>';

		       	} else{
		       		$array_data = json_decode( $json_data, true );
		       		echo '<a href="'.$array_data['label'].'" target="_blank" style="background:#d30b2a; color: #ffffff;border-color: #d30b2a;box-shadow: 0px 1px 0px #d30b2a;text-shadow: 0px 1px 0px #ffffff;" class="button button-primary"> Return Label </a> ';
		       		echo ' <a href="'.$array_data['invoice'].'" target="_blank" class="button button-primary"> Invoice </a>';

		       	}

		    }

		    public function hit_wc_checkout_order_processed($order_id){

				if ($this->hpos_enabled) {
					if ('shop_order' !== Automattic\WooCommerce\Utilities\OrderUtil::get_order_type($order_id)) {
						return;
					}
				} else {
					$post = get_post($order_id);
	
					if($post->post_type !='shop_order' ){
						return;
					}
				}

		    	$ship_content = !empty($_POST['hit_dpd_shipment_content']) ? sanitize_text_field($_POST['hit_dpd_shipment_content']) : 'Shipment Content';
		        $order = wc_get_order( $order_id );

		        $service_code = $multi_ven = '';
		        foreach( $order->get_shipping_methods() as $item_id => $item ){
					$service_code = $item->get_meta('a2z_dpd_service');
					$multi_ven = $item->get_meta('a2z_multi_ven');

				}

				$general_settings = get_option('a2z_dpd_main_settings',array());
				$order_data = $order->get_data();
		    	$items = $order->get_items();
				
		    	if(!isset($general_settings['a2z_dpdshipping_label_automation']) || $general_settings['a2z_dpdshipping_label_automation'] != 'yes'){
		    		return;
		    	}

		    	$custom_settings = array();
				$custom_settings['default'] = array(
									'a2z_dpdshipping_site_id' => $general_settings['a2z_dpdshipping_site_id'],
									'a2z_dpdshipping_site_pwd' => $general_settings['a2z_dpdshipping_site_pwd'],
									'a2z_dpdshipping_basic_tok' => $general_settings['a2z_dpdshipping_basic_tok'],
									'a2z_dpdshipping_acc_no' => $general_settings['a2z_dpdshipping_acc_no'],
									'a2z_dpdshipping_import_no' => $general_settings['a2z_dpdshipping_import_no'],
									'a2z_dpdshipping_shipper_name' => $general_settings['a2z_dpdshipping_shipper_name'],
									'a2z_dpdshipping_company' => $general_settings['a2z_dpdshipping_company'],
									'a2z_dpdshipping_mob_num' => $general_settings['a2z_dpdshipping_mob_num'],
									'a2z_dpdshipping_email' => $general_settings['a2z_dpdshipping_email'],
									'a2z_dpdshipping_address1' => $general_settings['a2z_dpdshipping_address1'],
									'a2z_dpdshipping_address2' => $general_settings['a2z_dpdshipping_address2'],
									'a2z_dpdshipping_city' => $general_settings['a2z_dpdshipping_city'],
									'a2z_dpdshipping_state' => $general_settings['a2z_dpdshipping_state'],
									'a2z_dpdshipping_zip' => $general_settings['a2z_dpdshipping_zip'],
									'a2z_dpdshipping_country' => $general_settings['a2z_dpdshipping_country'],
									'a2z_dpdshipping_gstin' => $general_settings['a2z_dpdshipping_gstin'],
									'a2z_dpdshipping_con_rate' => $general_settings['a2z_dpdshipping_con_rate'],
									'service_code' => $service_code,
									'a2z_dpdshipping_label_email' => $general_settings['a2z_dpdshipping_label_email'],
								);
				$vendor_settings = array();



				if(!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM')
				{
					$dpd_mod_weight_unit = 'kg';
					$dpd_mod_dim_unit = 'cm';
				}elseif(!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'LB_IN')
				{
					$dpd_mod_weight_unit = 'lbs';
					$dpd_mod_dim_unit = 'in';
				}
				else
				{
					$dpd_mod_weight_unit = 'kg';
					$dpd_mod_dim_unit = 'cm';
				}


				$pack_products = array();

				foreach ( $items as $item ) {
					$product_data = $item->get_data();

				    $product = array();
				    $product['product_name'] = str_replace('"', '', $product_data['name']);
				    $product['product_quantity'] = $product_data['quantity'];
				    $product['product_id'] = $product_data['product_id'];

					if ($this->hpos_enabled) {
						$hpos_prod_data = wc_get_product($product_data['product_id']);
						$saved_cc = $hpos_prod_data->get_meta("hits_dpd_cc");
					} else {
						$saved_cc = get_post_meta( $product_data['product_id'], 'hits_dpd_cc', true);
					}
					if(!empty($saved_cc)){
						$product['commodity_code'] = $saved_cc;
					}

				    $product_variation_id = $item->get_variation_id();
				    if(empty($product_variation_id) || $product_variation_id == 0){
				    	$getproduct = wc_get_product( $product_data['product_id'] );
				    }else{
				    	$getproduct = wc_get_product( $product_variation_id );
				    }
				    $woo_weight_unit = get_option('woocommerce_weight_unit');
					$woo_dimension_unit = get_option('woocommerce_dimension_unit');

					$dpd_mod_weight_unit = $dpd_mod_dim_unit = '';

				    $product['price'] = $getproduct->get_price();

				    if(!$product['price']){
						$product['price'] = (isset($product_data['total']) && isset($product_data['quantity'])) ? number_format(($product_data['total'] / $product_data['quantity']), 2) : 0;
					}
					
				    if ($woo_dimension_unit != $dpd_mod_dim_unit) {
				    	$prod_width = !empty($getproduct->get_width()) ? round($getproduct->get_width(), 3) : 0.1;
				    	$prod_height = !empty($getproduct->get_height()) ? round($getproduct->get_height(), 3) : 0.1;
				    	$prod_depth = !empty($getproduct->get_length()) ? round($getproduct->get_length(), 3) : 0.1;

				    	//wc_get_dimension( $dimension, $to_unit, $from_unit );
				    	$product['width'] = (!empty($prod_width) && $prod_width > 0) ? round(wc_get_dimension( $prod_width, $dpd_mod_dim_unit, $woo_dimension_unit ), 3) : 0.1 ;
				    	$product['height'] = (!empty($prod_height) && $prod_height > 0) ? round(wc_get_dimension( $prod_height, $dpd_mod_dim_unit, $woo_dimension_unit ), 3) : 0.1 ;
						$product['depth'] = (!empty($prod_depth) && $prod_depth > 0) ? round(wc_get_dimension( $prod_depth, $dpd_mod_dim_unit, $woo_dimension_unit ), 3) : 0.1 ;

				    }else {
				    	$product['width'] = !empty($getproduct->get_width()) ? round($getproduct->get_width(),3) : 0.1;
				    	$product['height'] = !empty($getproduct->get_height()) ? round($getproduct->get_height(),3) : 0.1;
				    	$product['depth'] = !empty($getproduct->get_length()) ? round($getproduct->get_length(),3) : 0.1;
				    }

				    if ($woo_weight_unit != $dpd_mod_weight_unit) {
				    	$prod_weight = $getproduct->get_weight();
				    	$product['weight'] = (!empty($prod_weight) && $prod_weight > 0) ? round(wc_get_weight( $prod_weight, $dpd_mod_weight_unit, $woo_weight_unit ), 3) : 0.1 ;
				    }else{
				    	$product['weight'] = !empty($getproduct->get_weight()) ? round($getproduct->get_weight(),3) : 0.1;
					}
				    $pack_products[] = $product;

				}

				if(isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes' && isset($general_settings['a2z_dpdshipping_v_labels']) && $general_settings['a2z_dpdshipping_v_labels'] == 'yes'){
					// Multi Vendor Enabled
					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];
						if ($this->hpos_enabled) {
							$hpos_prod_data = wc_get_product($product_id);
							$dpd_account = $hpos_prod_data->get_meta("dpd_express_address");
						} else {
							$dpd_account = get_post_meta($product_id,'dpd_express_address', true);
						}
						if(empty($dpd_account) || $dpd_account == 'default'){
							$dpd_account = 'default';
							if (!isset($vendor_settings[$dpd_account])) {
								$vendor_settings[$dpd_account] = $custom_settings['default'];
							}

							$vendor_settings[$dpd_account]['products'][] = $value;
						}

						if($dpd_account != 'default'){
							$user_account = get_post_meta($dpd_account,'a2z_dpd_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dpd_account])){

									$vendor_settings[$dpd_account] = $custom_settings['default'];

									if($user_account['a2z_dpdshipping_site_id'] != '' && $user_account['a2z_dpdshipping_site_pwd'] != '' && $user_account['a2z_dpdshipping_acc_no'] != ''){

										$vendor_settings[$dpd_account]['a2z_dpdshipping_site_id'] = $user_account['a2z_dpdshipping_site_id'];

										if($user_account['a2z_dpdshipping_site_pwd'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_site_pwd'] = $user_account['a2z_dpdshipping_site_pwd'];
										}

										if($user_account['a2z_dpdshipping_acc_no'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_acc_no'] = $user_account['a2z_dpdshipping_acc_no'];
										}

										if (isset($user_account['a2z_dpdshipping_basic_tok']) && !empty($user_account['a2z_dpdshipping_basic_tok'])) {
											$vendor_settings[$dpd_account]['a2z_dpdshipping_basic_tok'] = $user_account['a2z_dpdshipping_basic_tok'];
										}

										$vendor_settings[$dpd_account]['a2z_dpdshipping_import_no'] = !empty($user_account['a2z_dpdshipping_import_no']) ? $user_account['a2z_dpdshipping_import_no'] : '';

									}

									if ($user_account['a2z_dpdshipping_address1'] != '' && $user_account['a2z_dpdshipping_city'] != '' && $user_account['a2z_dpdshipping_state'] != '' && $user_account['a2z_dpdshipping_zip'] != '' && $user_account['a2z_dpdshipping_country'] != '' && $user_account['a2z_dpdshipping_shipper_name'] != '') {

										if($user_account['a2z_dpdshipping_shipper_name'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_shipper_name'] = $user_account['a2z_dpdshipping_shipper_name'];
										}

										if($user_account['a2z_dpdshipping_company'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_company'] = $user_account['a2z_dpdshipping_company'];
										}

										if($user_account['a2z_dpdshipping_mob_num'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_mob_num'] = $user_account['a2z_dpdshipping_mob_num'];
										}

										if($user_account['a2z_dpdshipping_email'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_email'] = $user_account['a2z_dpdshipping_email'];
										}

										if ($user_account['a2z_dpdshipping_address1'] != '') {
											$vendor_settings[$dpd_account]['a2z_dpdshipping_address1'] = $user_account['a2z_dpdshipping_address1'];
										}

										$vendor_settings[$dpd_account]['a2z_dpdshipping_address2'] = $user_account['a2z_dpdshipping_address2'];

										if($user_account['a2z_dpdshipping_city'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_city'] = $user_account['a2z_dpdshipping_city'];
										}

										if($user_account['a2z_dpdshipping_state'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_state'] = $user_account['a2z_dpdshipping_state'];
										}

										if($user_account['a2z_dpdshipping_zip'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_zip'] = $user_account['a2z_dpdshipping_zip'];
										}

										if($user_account['a2z_dpdshipping_country'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_country'] = $user_account['a2z_dpdshipping_country'];
										}

										$vendor_settings[$dpd_account]['a2z_dpdshipping_gstin'] = $user_account['a2z_dpdshipping_gstin'];
										$vendor_settings[$dpd_account]['a2z_dpdshipping_con_rate'] = $user_account['a2z_dpdshipping_con_rate'];
									}

									if(isset($general_settings['a2z_dpdshipping_v_email']) && $general_settings['a2z_dpdshipping_v_email'] == 'yes'){
										$user_dat = get_userdata($dpd_account);
										$vendor_settings[$dpd_account]['a2z_dpdshipping_label_email'] = $user_dat->data->user_email;
									}

									if($multi_ven !=''){
										$array_ven = explode('|',$multi_ven);
										$scode = '';
										foreach ($array_ven as $key => $svalue) {
											$ex_service = explode("_", $svalue);
											if($ex_service[0] == $dpd_account){
												$vendor_settings[$dpd_account]['service_code'] = $ex_service[1];
											}
										}

										if($scode == ''){
											if($order_data['shipping']['country'] != $vendor_settings[$dpd_account]['a2z_dpdshipping_country']){
												$vendor_settings[$dpd_account]['service_code'] = $user_account['a2z_dpdshipping_def_inter'];
											}else{
												$vendor_settings[$dpd_account]['service_code'] = $user_account['a2z_dpdshipping_def_dom'];
											}
										}

									}else{
										if($order_data['shipping']['country'] != $vendor_settings[$dpd_account]['a2z_dpdshipping_country']){
											$vendor_settings[$dpd_account]['service_code'] = $user_account['a2z_dpdshipping_def_inter'];
										}else{
											$vendor_settings[$dpd_account]['service_code'] = $user_account['a2z_dpdshipping_def_dom'];
										}

									}
								}
								$vendor_settings[$dpd_account]['products'][] = $value;
							}
						}

					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}

				$order_id = $order_data['id'];
	       		$order_currency = $order_data['currency'];

	       		// $order_shipping_first_name = $order_data['shipping']['first_name'];
				// $order_shipping_last_name = $order_data['shipping']['last_name'];
				// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
				// $order_shipping_address_1 = $order_data['shipping']['address_1'];
				// $order_shipping_address_2 = $order_data['shipping']['address_2'];
				// $order_shipping_city = $order_data['shipping']['city'];
				// $order_shipping_state = $order_data['shipping']['state'];
				// $order_shipping_postcode = $order_data['shipping']['postcode'];
				// $order_shipping_country = $order_data['shipping']['country'];
				// $order_shipping_phone = $order_data['billing']['phone'];
				// $order_shipping_email = $order_data['billing']['email'];

				$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
                $order_shipping_first_name = $shipping_arr['first_name'];
                $order_shipping_last_name = $shipping_arr['last_name'];
                $order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
                $order_shipping_address_1 = $shipping_arr['address_1'];
                $order_shipping_address_2 = $shipping_arr['address_2'];
                $order_shipping_city = $shipping_arr['city'];
                $order_shipping_state = $shipping_arr['state'];
                $order_shipping_postcode = $shipping_arr['postcode'];
                $order_shipping_country = $shipping_arr['country'];
                $order_shipping_phone = $order_data['billing']['phone'];
                $order_shipping_email = $order_data['billing']['email'];

				if(!empty($general_settings) && isset($general_settings['a2z_dpdshipping_integration_key'])){
					$mode = 'live';
					if(isset($general_settings['a2z_dpdshipping_test']) && $general_settings['a2z_dpdshipping_test']== 'yes'){
						$mode = 'test';
					}
					$execution = 'manual';
					if(isset($general_settings['a2z_dpdshipping_label_automation']) && $general_settings['a2z_dpdshipping_label_automation']== 'yes'){
						$execution = 'auto';
					}

					$boxes_to_shipo = array();
					if (isset($general_settings['a2z_dpdshipping_packing_type']) && $general_settings['a2z_dpdshipping_packing_type'] == "box") {
						if (isset($general_settings['a2z_dpdshipping_boxes']) && !empty($general_settings['a2z_dpdshipping_boxes'])) {
							foreach ($general_settings['a2z_dpdshipping_boxes'] as $box) {
								if ($box['enabled'] != 1) {
									continue;
								}else {
									$boxes_to_shipo[] = $box;
								}
							}
						}
					}


					foreach ($custom_settings as $key => $cvalue) {
						global $dpd_core;
						$frm_curr = get_option('woocommerce_currency');
						$to_curr = isset($dpd_core[$cvalue['a2z_dpdshipping_country']]) ? $dpd_core[$cvalue['a2z_dpdshipping_country']]['currency'] : '';
						$curr_con_rate = ( isset($cvalue['a2z_dpdshipping_con_rate']) && !empty($cvalue['a2z_dpdshipping_con_rate']) ) ? $cvalue['a2z_dpdshipping_con_rate'] : 0;

						if (!empty($frm_curr) && !empty($to_curr) && ($frm_curr != $to_curr) ) {
							if (isset($general_settings['a2z_dpdshipping_auto_con_rate']) && $general_settings['a2z_dpdshipping_auto_con_rate'] == "yes") {
								$current_date = date('m-d-Y', time());
								$ex_rate_data = get_option('a2z_dpd_ex_rate'.$key);
								$ex_rate_data = !empty($ex_rate_data) ? $ex_rate_data : array();
								if (empty($ex_rate_data) || (isset($ex_rate_data['date']) && $ex_rate_data['date'] != $current_date) ) {
									if (isset($cvalue['a2z_dpdshipping_country']) && !empty($cvalue['a2z_dpdshipping_country']) && isset($general_settings['a2z_dpdshipping_integration_key']) && !empty($general_settings['a2z_dpdshipping_integration_key'])) {

										$ex_rate_Request = json_encode(array('integrated_key' => $general_settings['a2z_dpdshipping_integration_key'],
															'from_curr' => $frm_curr,
															'to_curr' => $to_curr));

										$ex_rate_url = "https://app.myshipi.com/get_exchange_rate.php";
										$ex_rate_response = wp_remote_post( $ex_rate_url , array(
														'method'      => 'POST',
														'timeout'     => 45,
														'redirection' => 5,
														'httpversion' => '1.0',
														'blocking'    => true,
														'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
														'body'        => $ex_rate_Request,
														'sslverify'   => FALSE
														)
													);

										$ex_rate_result = ( is_array($ex_rate_response) && isset($ex_rate_response['body'])) ? json_decode($ex_rate_response['body'], true) : array();

										if ( !empty($ex_rate_result) && isset($ex_rate_result['ex_rate']) && $ex_rate_result['ex_rate'] != "Not Found" ) {
											$ex_rate_result['date'] = $current_date;
											update_option('a2z_dpd_ex_rate'.$key, $ex_rate_result);
										}else {
											if (!empty($ex_rate_data)) {
												$ex_rate_data['date'] = $current_date;
												update_option('a2z_dpd_ex_rate'.$key, $ex_rate_data);
											}
										}
									}
								}
								$get_ex_rate = get_option('a2z_dpd_ex_rate'.$key, '');
								$get_ex_rate = !empty($get_ex_rate) ? $get_ex_rate : array();
								$curr_con_rate = ( !empty($get_ex_rate) && isset($get_ex_rate['ex_rate']) ) ? $get_ex_rate['ex_rate'] : 0;
							}
						}

						$c_codes = [];

						foreach($cvalue['products'] as $prod_to_shipo_key => $prod_to_shipo){
							$saved_cc = get_post_meta( $prod_to_shipo['product_id'], 'hits_dpd_cc', true);
							if(!empty($saved_cc)){
								$c_codes[] = $saved_cc;
							}

							if (!empty($frm_curr) && !empty($to_curr) && ($frm_curr != $to_curr) ) {
								if ($curr_con_rate > 0) {
									$cvalue['products'][$prod_to_shipo_key]['price'] = $prod_to_shipo['price'] * $curr_con_rate;
								}
							}
						}

						$freight_charge = array();
						foreach($general_settings['a2z_dpdshipping_rule_shipping_cost'] as $ky=>$ship_cst){
							$freight_charge[$ky]['toweight'] = $general_settings['a2z_dpdshipping_rule_weight_to'][$ky];
							$freight_charge[$ky]['fromweight'] = $general_settings['a2z_dpdshipping_rule_weight_from'][$ky];
							$freight_charge[$ky]['shipcost'] = $ship_cst;
						}
						$frieght_default_charge = isset($general_settings['a2z_dpdshipping_default_ship_cost']) ? $general_settings['a2z_dpdshipping_default_ship_cost'] : '';
						$dutiable = 'N';
						if($order_shipping_country != $cvalue['a2z_dpdshipping_country']){
							$dutiable = 'Y';
						}

						//For Automatic Label Generation

						$data = array();
						$data['integrated_key'] = $general_settings['a2z_dpdshipping_integration_key'];
						$data['order_id'] = $order_id;
						$data['exec_type'] = $execution;
						$data['mode'] = $mode;
						$data['carrier_type'] = "dpd";
						$data['ship_price'] = $order_data['shipping_total'];
						$data['meta'] = array(
							"site_id" => $cvalue['a2z_dpdshipping_site_id'],
							"password"  => $cvalue['a2z_dpdshipping_site_pwd'],
							"site_acess"=> isset($cvalue['a2z_dpdshipping_basic_tok']) ? $cvalue['a2z_dpdshipping_basic_tok'] : "",
							"accountnum" => $cvalue['a2z_dpdshipping_acc_no'],
							"t_company" => $order_shipping_company,
							"t_address1" => str_replace('"', '', $order_shipping_address_1),
							"t_address2" => str_replace('"', '', $order_shipping_address_2),
							"t_city" => $order_shipping_city,
							"t_state" => $order_shipping_state,
							"t_postal" => $order_shipping_postcode,
							"t_country" => $order_shipping_country,
							"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
							"t_phone" => $order_shipping_phone,
							"t_email" => $order_shipping_email,
							"dutiable" => $dutiable,
							"insurance" => $general_settings['a2z_dpdshipping_insure'],
							'freight_charge' => $freight_charge,
							'freight_default' => $frieght_default_charge,
							"products" => $cvalue['products'],
							"pack_algorithm" => $general_settings['a2z_dpdshipping_packing_type'],
							"boxes" => $boxes_to_shipo,
							"BOX44" => isset($general_settings['a2z_dpdshipping_default_box44'])? $general_settings['a2z_dpdshipping_default_box44'] : '',
							"max_weight" => $general_settings['a2z_dpdshipping_max_weight'],
							"sd" => ($general_settings['a2z_dpdshipping_sat'] == 'yes') ? "Y" : "N",
							"cod" => ($general_settings['a2z_dpdshipping_cod'] == 'yes') ? "Y" : "N",
							"service_code" => $service_code,
							"shipment_content" => $ship_content,
							"email_alert" => ( isset($general_settings['a2z_dpdshipping_email_alert']) && ($general_settings['a2z_dpdshipping_email_alert'] == 'yes') ) ? "Y" : "N",
							"s_company" => $cvalue['a2z_dpdshipping_company'],
							"s_address1" => $cvalue['a2z_dpdshipping_address1'],
							"s_address2" => $cvalue['a2z_dpdshipping_address2'],
							"s_city" => $cvalue['a2z_dpdshipping_city'],
							"s_state" => $cvalue['a2z_dpdshipping_state'],
							"s_postal" => $cvalue['a2z_dpdshipping_zip'],
							"s_country" => $cvalue['a2z_dpdshipping_country'],
							"gstin" => $cvalue['a2z_dpdshipping_gstin'],
							"s_name" => $cvalue['a2z_dpdshipping_shipper_name'],
							"s_phone" => $cvalue['a2z_dpdshipping_mob_num'],
							"s_email" => $cvalue['a2z_dpdshipping_email'],
							"label_format" => $general_settings['a2z_dpdshipping_print_size'],
							"sent_email_to" => $cvalue['a2z_dpdshipping_label_email'],
							"duty_payer"=> isset($general_settings['a2z_dpdshipping_duty_payment'])?$general_settings['a2z_dpdshipping_duty_payment'] : 'EDAP',
							"label" => $key,
							"payment_con" => (isset($general_settings['a2z_dpdshipping_pay_con']) ? $general_settings['a2z_dpdshipping_pay_con'] : 'S'),
							"cus_payment_con" => (isset($general_settings['a2z_dpdshipping_cus_pay_con']) ? $general_settings['a2z_dpdshipping_cus_pay_con'] : ''),
							"translation" => ( (isset($general_settings['a2z_dpdshipping_translation']) && $general_settings['a2z_dpdshipping_translation'] == "yes" ) ? 'Y' : 'N'),
							"translation_key" => (isset($general_settings['a2z_dpdshipping_translation_key']) ? $general_settings['a2z_dpdshipping_translation_key'] : ''),
							"commodity_code" => $c_codes,

						);
						// echo '<pre>';print_r(json_encode($data));die();
						//Auto Shipment
						$auto_ship_url = "https://app.myshipi.com/label_api/create_shipment.php";
						wp_remote_post( $auto_ship_url , array(
							'method'      => 'POST',
							'timeout'     => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking'    => false,
							'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
							'body'        => json_encode($data),
							'sslverify'   => FALSE
							)
						);

					}

				}
		    }

		    // Save the data of the Meta field
			public function hit_create_dpd_shipping( $order_id ) {

		    	if ($this->hpos_enabled) {
					if ('shop_order' !== Automattic\WooCommerce\Utilities\OrderUtil::get_order_type($order_id)) {
						return;
					}
				} else {
					$post = get_post($order_id);
					if($post->post_type !='shop_order' ){
						return;
					}
				}

		    	if (  isset( $_POST[ 'hit_dpd_reset' ] ) ) {
		    		delete_option('hit_dpd_values_'.$order_id);
		    	}

		    	if (  isset( $_POST['hit_dpd_create_label']) ) {
		    		$create_shipment_for = sanitize_text_field($_POST['hit_dpd_create_label']);
		           $service_code = '';//sanitize_text_field($_POST['hit_dpd_express_service_code_'.$create_shipment_for]);
		           $ship_content = !empty($_POST['hit_dpd_shipment_content_'.$create_shipment_for]) ? sanitize_text_field($_POST['hit_dpd_shipment_content_'.$create_shipment_for]) : 'Shipment Content';
		           $pickup_mode = (isset($_POST['hit_dpd_add_pickup_'.$create_shipment_for]) && $_POST['hit_dpd_add_pickup_'.$create_shipment_for]) ? 'auto' : 'manual';
		           $order = wc_get_order( $order_id );
			       if($order){
		       		$order_data = $order->get_data();
			       		$order_id = $order_data['id'];
			       		$order_currency = $order_data['currency'];

			       		// $order_shipping_first_name = $order_data['shipping']['first_name'];
						// $order_shipping_last_name = $order_data['shipping']['last_name'];
						// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
						// $order_shipping_address_1 = $order_data['shipping']['address_1'];
						// $order_shipping_address_2 = $order_data['shipping']['address_2'];
						// $order_shipping_city = $order_data['shipping']['city'];
						// $order_shipping_state = $order_data['shipping']['state'];
						// $order_shipping_postcode = $order_data['shipping']['postcode'];
						// $order_shipping_country = $order_data['shipping']['country'];
						// $order_shipping_phone = $order_data['billing']['phone'];
						// $order_shipping_email = $order_data['billing']['email'];

						$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
						$order_shipping_first_name = $shipping_arr['first_name'];
						$order_shipping_last_name = $shipping_arr['last_name'];
						$order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
						$order_shipping_address_1 = $shipping_arr['address_1'];
						$order_shipping_address_2 = $shipping_arr['address_2'];
						$order_shipping_city = $shipping_arr['city'];
						$order_shipping_state = $shipping_arr['state'];
						$order_shipping_postcode = $shipping_arr['postcode'];
						$order_shipping_country = $shipping_arr['country'];
						$order_shipping_phone = $order_data['billing']['phone'];
						$order_shipping_email = $order_data['billing']['email'];

						$items = $order->get_items();
						$pack_products = array();
						$general_settings = get_option('a2z_dpd_main_settings',array());

						foreach ( $items as $item ) {
							$product_data = $item->get_data();
						    $product = array();
						    $product['product_name'] = str_replace('"', '', $product_data['name']);
						    $product['product_quantity'] = $product_data['quantity'];
						   	$product['product_id'] = $product_data['product_id'];
						   	if ($this->hpos_enabled) {
								$hpos_prod_data = wc_get_product($product_data['product_id']);
								$saved_cc = $hpos_prod_data->get_meta("hits_dpd_cc");
							} else {
								$saved_cc = get_post_meta( $product_data['product_id'], 'hits_dpd_cc', true);
							}
							if(!empty($saved_cc)){
								$product['commodity_code'] = $saved_cc;
							}

						    $product_variation_id = $item->get_variation_id();
						    if(empty($product_variation_id)){
						    	$getproduct = wc_get_product( $product_data['product_id'] );
						    }else{
						    	$getproduct = wc_get_product( $product_variation_id );
						    }

						    $woo_weight_unit = get_option('woocommerce_weight_unit');
							$woo_dimension_unit = get_option('woocommerce_dimension_unit');

							$dpd_mod_weight_unit = $dpd_mod_dim_unit = '';

							if(!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM')
							{
								$dpd_mod_weight_unit = 'kg';
								$dpd_mod_dim_unit = 'cm';
							}elseif(!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'LB_IN')
							{
								$dpd_mod_weight_unit = 'lbs';
								$dpd_mod_dim_unit = 'in';
							}
							else
							{
								$dpd_mod_weight_unit = 'kg';
								$dpd_mod_dim_unit = 'cm';
							}

						    $product['price'] = $getproduct->get_price();

						    if(!$product['price']){
								$product['price'] = (isset($product_data['total']) && isset($product_data['quantity'])) ? number_format(($product_data['total'] / $product_data['quantity']), 2) : 0;
							}

						    if ($woo_dimension_unit != $dpd_mod_dim_unit) {
					    	$prod_width = $getproduct->get_width();
					    	$prod_height = $getproduct->get_height();
					    	$prod_depth = $getproduct->get_length();

					    	//wc_get_dimension( $dimension, $to_unit, $from_unit );
					    	$product['width'] = (!empty($prod_width) && $prod_width > 0) ?  round(wc_get_dimension( $prod_width, $dpd_mod_dim_unit, $woo_dimension_unit ), 2): 0.1 ;
					    	$product['height'] = (!empty($prod_height) && $prod_height > 0) ?  round(wc_get_dimension( $prod_height, $dpd_mod_dim_unit, $woo_dimension_unit ), 2): 0.1 ;
							$product['depth'] = (!empty($prod_depth) && $prod_depth > 0) ?  round(wc_get_dimension( $prod_depth, $dpd_mod_dim_unit, $woo_dimension_unit ), 2): 0.1 ;

						    }else {
						    	$product['width'] = !empty($getproduct->get_width()) ? $getproduct->get_width() : 0.1;
						    	$product['height'] = !empty($getproduct->get_height()) ? $getproduct->get_height() : 0.1;
						    	$product['depth'] = !empty($getproduct->get_length()) ? $getproduct->get_length() : 0.1;
						    }

						    if ($woo_weight_unit != $dpd_mod_weight_unit) {
						    	$prod_weight = $getproduct->get_weight();
						    	$product['weight'] = (!empty($prod_weight) && $prod_weight > 0) ?  round(wc_get_weight( $prod_weight, $dpd_mod_weight_unit, $woo_weight_unit ), 2): 0.1 ;
						    }else{
						    	$product['weight'] = $getproduct->get_weight();
							}

						    $pack_products[] = $product;

						}

						$custom_settings = array();
						$custom_settings['default'] = array(
											'a2z_dpdshipping_site_id' => $general_settings['a2z_dpdshipping_site_id'],
											'a2z_dpdshipping_site_pwd' => $general_settings['a2z_dpdshipping_site_pwd'],
											'a2z_dpdshipping_acc_no' => $general_settings['a2z_dpdshipping_acc_no'],
											'a2z_dpdshipping_basic_tok' => $general_settings['a2z_dpdshipping_basic_tok'],
											'a2z_dpdshipping_import_no' => $general_settings['a2z_dpdshipping_import_no'],
											'a2z_dpdshipping_shipper_name' => $general_settings['a2z_dpdshipping_shipper_name'],
											'a2z_dpdshipping_company' => $general_settings['a2z_dpdshipping_company'],
											'a2z_dpdshipping_mob_num' => $general_settings['a2z_dpdshipping_mob_num'],
											'a2z_dpdshipping_email' => $general_settings['a2z_dpdshipping_email'],
											'a2z_dpdshipping_address1' => $general_settings['a2z_dpdshipping_address1'],
											'a2z_dpdshipping_address2' => $general_settings['a2z_dpdshipping_address2'],
											'a2z_dpdshipping_city' => $general_settings['a2z_dpdshipping_city'],
											'a2z_dpdshipping_state' => $general_settings['a2z_dpdshipping_state'],
											'a2z_dpdshipping_zip' => $general_settings['a2z_dpdshipping_zip'],
											'a2z_dpdshipping_country' => $general_settings['a2z_dpdshipping_country'],
											'a2z_dpdshipping_gstin' => $general_settings['a2z_dpdshipping_gstin'],
											'a2z_dpdshipping_con_rate' => $general_settings['a2z_dpdshipping_con_rate'],
											'service_code' => $service_code,
											'a2z_dpdshipping_label_email' => $general_settings['a2z_dpdshipping_label_email'],
										);
						$vendor_settings = array();
						if(isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes' && isset($general_settings['a2z_dpdshipping_v_labels']) && $general_settings['a2z_dpdshipping_v_labels'] == 'yes'){
						// Multi Vendor Enabled
						foreach ($pack_products as $key => $value) {
							$product_id = $value['product_id'];
							if ($this->hpos_enabled) {
								$hpos_prod_data = wc_get_product($product_id);
								$dpd_account = $hpos_prod_data->get_meta("dpd_express_address");
							} else {
								$dpd_account = get_post_meta($product_id,'dpd_express_address', true);
							}
							if(empty($dpd_account) || $dpd_account == 'default'){
								$dpd_account = 'default';
								if (!isset($vendor_settings[$dpd_account])) {
									$vendor_settings[$dpd_account] = $custom_settings['default'];
								}

								$vendor_settings[$dpd_account]['products'][] = $value;
							}

							if($dpd_account != 'default'){
								$user_account = get_post_meta($dpd_account,'a2z_dpd_vendor_settings', true);
								$user_account = empty($user_account) ? array() : $user_account;
								if(!empty($user_account)){
									if(!isset($vendor_settings[$dpd_account])){

										$vendor_settings[$dpd_account] = $custom_settings['default'];

									if($user_account['a2z_dpdshipping_site_id'] != '' && $user_account['a2z_dpdshipping_site_pwd'] != '' && $user_account['a2z_dpdshipping_acc_no'] != ''){

										$vendor_settings[$dpd_account]['a2z_dpdshipping_site_id'] = $user_account['a2z_dpdshipping_site_id'];

										if($user_account['a2z_dpdshipping_site_pwd'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_site_pwd'] = $user_account['a2z_dpdshipping_site_pwd'];
										}

										if($user_account['a2z_dpdshipping_acc_no'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_acc_no'] = $user_account['a2z_dpdshipping_acc_no'];
										}
										
										if(isset($user_account['a2z_dpdshipping_basic_tok']) && !empty($user_account['a2z_dpdshipping_basic_tok'])){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_basic_tok'] = $user_account['a2z_dpdshipping_basic_tok'];
										}

										$vendor_settings[$dpd_account]['a2z_dpdshipping_import_no'] = !empty($user_account['a2z_dpdshipping_import_no']) ? $user_account['a2z_dpdshipping_import_no'] : '';

									}

									if ($user_account['a2z_dpdshipping_address1'] != '' && $user_account['a2z_dpdshipping_city'] != '' && $user_account['a2z_dpdshipping_state'] != '' && $user_account['a2z_dpdshipping_zip'] != '' && $user_account['a2z_dpdshipping_country'] != '' && $user_account['a2z_dpdshipping_shipper_name'] != '') {

										if($user_account['a2z_dpdshipping_shipper_name'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_shipper_name'] = $user_account['a2z_dpdshipping_shipper_name'];
										}

										if($user_account['a2z_dpdshipping_company'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_company'] = $user_account['a2z_dpdshipping_company'];
										}

										if($user_account['a2z_dpdshipping_mob_num'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_mob_num'] = $user_account['a2z_dpdshipping_mob_num'];
										}

										if($user_account['a2z_dpdshipping_email'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_email'] = $user_account['a2z_dpdshipping_email'];
										}

										if ($user_account['a2z_dpdshipping_address1'] != '') {
											$vendor_settings[$dpd_account]['a2z_dpdshipping_address1'] = $user_account['a2z_dpdshipping_address1'];
										}

										$vendor_settings[$dpd_account]['a2z_dpdshipping_address2'] = $user_account['a2z_dpdshipping_address2'];

										if($user_account['a2z_dpdshipping_city'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_city'] = $user_account['a2z_dpdshipping_city'];
										}

										if($user_account['a2z_dpdshipping_state'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_state'] = $user_account['a2z_dpdshipping_state'];
										}

										if($user_account['a2z_dpdshipping_zip'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_zip'] = $user_account['a2z_dpdshipping_zip'];
										}

										if($user_account['a2z_dpdshipping_country'] != ''){
											$vendor_settings[$dpd_account]['a2z_dpdshipping_country'] = $user_account['a2z_dpdshipping_country'];
										}

										$vendor_settings[$dpd_account]['a2z_dpdshipping_gstin'] = $user_account['a2z_dpdshipping_gstin'];
										$vendor_settings[$dpd_account]['a2z_dpdshipping_con_rate'] = $user_account['a2z_dpdshipping_con_rate'];

									}

										if(isset($general_settings['a2z_dpdshipping_v_email']) && $general_settings['a2z_dpdshipping_v_email'] == 'yes'){
											$user_dat = get_userdata($dpd_account);
											$vendor_settings[$dpd_account]['a2z_dpdshipping_label_email'] = $user_dat->data->user_email;
										}


										if($order_data['shipping']['country'] != $vendor_settings[$dpd_account]['a2z_dpdshipping_country']){
											$vendor_settings[$dpd_account]['service_code'] = empty($service_code) ? $user_account['a2z_dpdshipping_def_inter'] : $service_code;
										}else{
											$vendor_settings[$dpd_account]['service_code'] = empty($service_code) ? $user_account['a2z_dpdshipping_def_dom'] : $service_code;
										}
									}
									$vendor_settings[$dpd_account]['products'][] = $value;
								}
							}

						}

					}

					if(empty($vendor_settings)){
						$custom_settings['default']['products'] = $pack_products;
					}else{
						$custom_settings = $vendor_settings;
					}

					if(!empty($general_settings) && isset($general_settings['a2z_dpdshipping_integration_key']) && isset($custom_settings[$create_shipment_for])){
						$mode = 'live';
						if(isset($general_settings['a2z_dpdshipping_test']) && $general_settings['a2z_dpdshipping_test']== 'yes'){
							$mode = 'test';
						}

						$execution = 'manual';

						$boxes_to_shipo = array();
						if (isset($general_settings['a2z_dpdshipping_packing_type']) && $general_settings['a2z_dpdshipping_packing_type'] == "box") {
							if (isset($general_settings['a2z_dpdshipping_boxes']) && !empty($general_settings['a2z_dpdshipping_boxes'])) {
								foreach ($general_settings['a2z_dpdshipping_boxes'] as $box) {
									if ($box['enabled'] != 1) {
										continue;
									}else {
										$boxes_to_shipo[] = $box;
									}
								}
							}
						}

						global $dpd_core;
						$frm_curr = get_option('woocommerce_currency');
						$to_curr = isset($dpd_core[$custom_settings[$create_shipment_for]['a2z_dpdshipping_country']]) ? $dpd_core[$custom_settings[$create_shipment_for]['a2z_dpdshipping_country']]['currency'] : '';
						$curr_con_rate = ( isset($custom_settings[$create_shipment_for]['a2z_dpdshipping_con_rate']) && !empty($custom_settings[$create_shipment_for]['a2z_dpdshipping_con_rate']) ) ? $custom_settings[$create_shipment_for]['a2z_dpdshipping_con_rate'] : 0;

						if (!empty($frm_curr) && !empty($to_curr) && ($frm_curr != $to_curr) ) {
							if (isset($general_settings['a2z_dpdshipping_auto_con_rate']) && $general_settings['a2z_dpdshipping_auto_con_rate'] == "yes") {
								}
						}

						$c_codes = [];

						foreach($custom_settings[$create_shipment_for]['products'] as $prod_to_shipo_key => $prod_to_shipo){
							$saved_cc = get_post_meta( $prod_to_shipo['product_id'], 'hits_dpd_cc', true);
							if(!empty($saved_cc)){
								$c_codes[] = $saved_cc;
							}

							if (!empty($frm_curr) && !empty($to_curr) && ($frm_curr != $to_curr) ) {
								if ($curr_con_rate > 0) {
									$custom_settings[$create_shipment_for]['products'][$prod_to_shipo_key]['price'] = $prod_to_shipo['price'] * $curr_con_rate;
								}
							}
						}

						$freight_charge = array();
						foreach($general_settings['a2z_dpdshipping_rule_shipping_cost'] as $ky=>$ship_cst){
							$freight_charge[$ky]['toweight'] = $general_settings['a2z_dpdshipping_rule_weight_to'][$ky];
							$freight_charge[$ky]['fromweight'] = $general_settings['a2z_dpdshipping_rule_weight_from'][$ky];
							$freight_charge[$ky]['shipcost'] = $ship_cst;
						}
						$frieght_default_charge = isset($general_settings['a2z_dpdshipping_default_ship_cost']) ? $general_settings['a2z_dpdshipping_default_ship_cost'] : '';
						$dutiable = 'N';
						if($order_shipping_country != $custom_settings[$create_shipment_for]['a2z_dpdshipping_country']){
							$dutiable = 'Y';
						}
						// weight_from = $general_settings['a2z_dpdshipping_rule_weight_from']
						// $general_settings['a2z_dpdshipping_rule_weight_to']
						// $general_settings['a2z_dpdshipping_rule_shipping_cost']
						$data = array();
						$data['integrated_key'] = $general_settings['a2z_dpdshipping_integration_key'];
						$data['order_id'] = $order_id;
						$data['exec_type'] = $execution;
						$data['mode'] = $mode;
						$data['carrier_type'] = "dpd";
						$data['meta'] = array(
							"site_id" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_site_id'],
							"password"  => $custom_settings[$create_shipment_for]['a2z_dpdshipping_site_pwd'],
							"site_acess" => isset($custom_settings[$create_shipment_for]['a2z_dpdshipping_basic_tok']) ? $custom_settings[$create_shipment_for]['a2z_dpdshipping_basic_tok'] : "",
							"accountnum"=> $custom_settings[$create_shipment_for]['a2z_dpdshipping_acc_no'],
							"t_company" => $order_shipping_company,
							"t_address1" => str_replace('"', '', $order_shipping_address_1),
							"t_address2" => str_replace('"', '', $order_shipping_address_2),
							"t_city" => $order_shipping_city,
							"t_state" => $order_shipping_state,
							"t_postal" => $order_shipping_postcode,
							"t_country" => $order_shipping_country,
							"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
							"t_phone" => $order_shipping_phone,
							"t_email" => $order_shipping_email,
							"dutiable" => $dutiable,
							"insurance" => $general_settings['a2z_dpdshipping_insure'],
							'freight_charge' => $freight_charge,
							'freight_default' => $frieght_default_charge,
							"products" => $custom_settings[$create_shipment_for]['products'],
							"pack_algorithm" => $general_settings['a2z_dpdshipping_packing_type'],
							"boxes" => $boxes_to_shipo,
							"BOX44" => isset($general_settings['a2z_dpdshipping_default_box44'])? $general_settings['a2z_dpdshipping_default_box44'] : '',
							"max_weight" => $general_settings['a2z_dpdshipping_max_weight'],
							"sd" => ($general_settings['a2z_dpdshipping_sat'] == 'yes') ? "Y" : "N",
							"cod" => ($general_settings['a2z_dpdshipping_cod'] == 'yes') ? "Y" : "N",
							"service_code" => $custom_settings[$create_shipment_for]['service_code'],
							"shipment_content" => $ship_content,
							"email_alert" => ( isset($general_settings['a2z_dpdshipping_email_alert']) && ($general_settings['a2z_dpdshipping_email_alert'] == 'yes') ) ? "Y" : "N",
							"s_company" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_company'],
							"s_address1" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_address1'],
							"s_address2" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_address2'],
							"s_city" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_city'],
							"s_state" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_state'],
							"s_postal" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_zip'],
							"s_country" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_country'],
							"gstin" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_gstin'],
							"s_name" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_shipper_name'],
							"s_phone" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_mob_num'],
							"s_email" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_email'],
							"label_format" => $general_settings['a2z_dpdshipping_print_size'],
							"sent_email_to" => $custom_settings[$create_shipment_for]['a2z_dpdshipping_label_email'],
							"duty_payer"=> isset($general_settings['a2z_dpdshipping_duty_payment'])?$general_settings['a2z_dpdshipping_duty_payment'] : 'EDAP',
							"translation" => ( (isset($general_settings['a2z_dpdshipping_translation']) && $general_settings['a2z_dpdshipping_translation'] == "yes" ) ? 'Y' : 'N'),
							"translation_key" => (isset($general_settings['a2z_dpdshipping_translation_key']) ? $general_settings['a2z_dpdshipping_translation_key'] : ''),
							"commodity_code" => $c_codes,
							 "label" => $create_shipment_for
						);
						// echo '<pre>';print_r(json_encode($data));die();
						//Manual Shipment
						$manual_ship_url = "https://app.myshipi.com/label_api/create_shipment.php";
						$response = wp_remote_post( $manual_ship_url , array(
							'method'      => 'POST',
							'timeout'     => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking'    => true,
							'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
							'body'        => json_encode($data),
							'sslverify'   => FALSE
							)
						);

						$output = (is_array($response) && isset($response['body'])) ? json_decode($response['body'],true) : [];
						// echo"<pre>";print_r($output);die();
							if($output){
								if(isset($output['status'])){

									if(isset($output['status']) && $output['status'] != 'success'){
										   update_option('hit_dpd_status_'.$order_id, $output['status']);

									}else if(isset($output['status']) && $output['status'] == 'success'){
										$output['user_id'] = $create_shipment_for;
										$result_arr = !empty(get_option('hit_dpd_values_'.$order_id, array())) ? json_decode(get_option('hit_dpd_values_'.$order_id, array())) : [];
										$result_arr[] = $output;

										update_option('hit_dpd_values_'.$order_id, json_encode($result_arr));

									}

								}else{
									update_option('hit_dpd_status_'.$order_id, 'Site not Connected with Shipi. Contact Shipi Team.');
																		}
							}else{
								update_option('hit_dpd_status_'.$order_id, 'Site not Connected with Shipi. Contact Shipi Team.');
							}
						}
			       }
		        }
		    }

		}

		$dpd_core = array();
		$dpd_core['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
		$dpd_core['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
		$dpd_core['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
		$dpd_core['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
		$dpd_core['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
		$dpd_core['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
		$dpd_core['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$dpd_core['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
		$dpd_core['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$dpd_core['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$dpd_core['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$dpd_core['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
		$dpd_core['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
		$dpd_core['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
		$dpd_core['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
		$dpd_core['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
		$dpd_core['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
		$dpd_core['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
		$dpd_core['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
		$dpd_core['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
		$dpd_core['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
		$dpd_core['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
		$dpd_core['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
		$dpd_core['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
		$dpd_core['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
		$dpd_core['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
		$dpd_core['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
		$dpd_core['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$dpd_core['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$dpd_core['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
		$dpd_core['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$dpd_core['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
		$dpd_core['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$dpd_core['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
		$dpd_core['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
		$dpd_core['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
		$dpd_core['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
		$dpd_core['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
		$dpd_core['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['CZ'] = array('region' => 'EU', 'currency' =>'CZK', 'weight' => 'KG_CM');
		$dpd_core['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
		$dpd_core['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$dpd_core['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
		$dpd_core['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
		$dpd_core['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
		$dpd_core['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
		$dpd_core['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
		$dpd_core['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
		$dpd_core['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$dpd_core['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$dpd_core['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$dpd_core['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$dpd_core['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
		$dpd_core['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$dpd_core['GH'] = array('region' => 'AP', 'currency' =>'GHS', 'weight' => 'KG_CM');
		$dpd_core['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$dpd_core['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$dpd_core['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
		$dpd_core['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
		$dpd_core['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$dpd_core['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
		$dpd_core['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
		$dpd_core['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
		$dpd_core['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
		$dpd_core['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
		$dpd_core['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
		$dpd_core['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
		$dpd_core['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
		$dpd_core['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
		$dpd_core['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
		$dpd_core['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
		$dpd_core['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
		$dpd_core['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
		$dpd_core['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$dpd_core['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
		$dpd_core['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
		$dpd_core['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
		$dpd_core['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
		$dpd_core['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
		$dpd_core['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
		$dpd_core['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$dpd_core['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
		$dpd_core['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
		$dpd_core['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
		$dpd_core['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
		$dpd_core['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
		$dpd_core['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
		$dpd_core['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
		$dpd_core['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
		$dpd_core['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
		$dpd_core['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
		$dpd_core['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
		$dpd_core['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
		$dpd_core['LT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
		$dpd_core['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
		$dpd_core['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
		$dpd_core['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
		$dpd_core['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
		$dpd_core['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
		$dpd_core['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
		$dpd_core['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
		$dpd_core['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
		$dpd_core['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
		$dpd_core['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
		$dpd_core['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
		$dpd_core['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
		$dpd_core['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
		$dpd_core['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
		$dpd_core['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
		$dpd_core['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$dpd_core['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
		$dpd_core['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
		$dpd_core['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
		$dpd_core['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
		$dpd_core['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$dpd_core['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$dpd_core['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$dpd_core['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
		$dpd_core['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
		$dpd_core['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$dpd_core['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
		$dpd_core['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
		$dpd_core['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
		$dpd_core['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
		$dpd_core['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
		$dpd_core['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
		$dpd_core['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
		$dpd_core['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
		$dpd_core['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
		$dpd_core['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
		$dpd_core['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
		$dpd_core['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
		$dpd_core['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
		$dpd_core['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
		$dpd_core['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
		$dpd_core['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
		$dpd_core['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
		$dpd_core['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
		$dpd_core['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
		$dpd_core['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
		$dpd_core['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
		$dpd_core['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
		$dpd_core['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
		$dpd_core['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
		$dpd_core['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$dpd_core['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$dpd_core['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
		$dpd_core['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
		$dpd_core['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
		$dpd_core['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
		$dpd_core['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
		$dpd_core['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
		$dpd_core['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$dpd_core['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
		$dpd_core['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
		$dpd_core['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
		$dpd_core['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$dpd_core['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
		$dpd_core['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
		$dpd_core['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
		$dpd_core['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$dpd_core['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
		$dpd_core['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
		$dpd_core['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
		$dpd_core['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$dpd_core['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$dpd_core['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$dpd_core['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$dpd_core['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$dpd_core['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
		$dpd_core['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$dpd_core['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
		$dpd_core['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$dpd_core['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
		$dpd_core['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
		$dpd_core['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');

	}
	$a2z_dpdshipping = new a2z_dpdshipping_parent();
}