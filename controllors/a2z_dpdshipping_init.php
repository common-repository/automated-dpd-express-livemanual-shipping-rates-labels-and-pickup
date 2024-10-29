<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
use Google\Cloud\Translate\TranslateClient;
if (!class_exists('A2Z_Dpdshipping')) {
	class A2Z_Dpdshipping extends WC_Shipping_Method
	{
		/**
		 * Constructor for your shipping class
		 *
		 * @access public
		 * @return void
		 */
		public function __construct()
		{
			$this->id                 = 'az_dpdshipping';
			$this->method_title       = __('DPD Shipping');  // Title shown in admin
			$this->title       = __('DPD Shipping Shipping');
			$this->method_description = __(''); //
			$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
			$this->init();
		}

		/**
		 * Init your settings
		 *
		 * @access public
		 * @return void
		 */
		function init()
		{
			// Load the settings API
			$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
			$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

			// Save settings in admin if you have any defined
			add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
		}

		/**
		 * calculate_shipping function.
		 *
		 * @access public
		 * @param mixed $package
		 * @return void
		 */
		public function calculate_shipping($package = array())
		{
			// $Curr = get_option('woocommerce_currency');
			//      	global $WOOCS;
			//      	if ($WOOCS->default_currency) {
			// $Curr = $WOOCS->default_currency;
			//      	print_r($Curr);
			//      	}else{
			//      		print_r("No");
			//      	}
			//      	die();

			$order_total=WC()->cart->subtotal;

			$rate_cost_data = apply_filters('hitstacks_dpdshipping_rate_cost', [], $order_total);
			if (!empty($rate_cost_data)) {
				$name = isset($rate_cost_data['name']) ? $rate_cost_data['name'] : "DPD Shipping";
				$rate_cost = isset($rate_cost_data['cost']) ? $rate_cost_data['cost'] : 0;
				if ($rate_cost < 1) {
					$name .= ' - Free';
				}
				$rate = array(
							'id'       => 'a2z_dpd_shipping',
							'label'    => $name,
							'cost'     => apply_filters("hitstacks_shipping_cost_conversion", $rate_cost),
							'meta_data' => array()
						);
				// Register the rate
				$this->add_rate($rate);
			}
			return;
			$execution_status = get_option('a2z_dpd_express_working_status');
			if(!empty($execution_status)){
				if($execution_status == 'stop_working'){
					return;
				}
			}

			$pack_aft_hook = apply_filters('a2z_dpdshipping_rate_packages', $package);

			if (empty($pack_aft_hook)) {
				return;
			}

			$general_settings = get_option('a2z_dpd_main_settings');
			$general_settings = empty($general_settings) ? array() : $general_settings;

			if (!is_array($general_settings)) {
				return;
			}

			//excluded Countries
			if(isset($general_settings['a2z_dpdshipping_exclude_countries'])){

				if(in_array($pack_aft_hook['destination']['country'],$general_settings['a2z_dpdshipping_exclude_countries'])){
					return;
				}
				}

			$dpd_core = array();
			$dpd_core['AD'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['AE'] = array('region' => 'AP', 'currency' => 'AED', 'weight' => 'KG_CM');
			$dpd_core['AF'] = array('region' => 'AP', 'currency' => 'AFN', 'weight' => 'KG_CM');
			$dpd_core['AG'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['AI'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['AL'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['AM'] = array('region' => 'AP', 'currency' => 'AMD', 'weight' => 'KG_CM');
			$dpd_core['AN'] = array('region' => 'AM', 'currency' => 'ANG', 'weight' => 'KG_CM');
			$dpd_core['AO'] = array('region' => 'AP', 'currency' => 'AOA', 'weight' => 'KG_CM');
			$dpd_core['AR'] = array('region' => 'AM', 'currency' => 'ARS', 'weight' => 'KG_CM');
			$dpd_core['AS'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['AT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['AU'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dpd_core['AW'] = array('region' => 'AM', 'currency' => 'AWG', 'weight' => 'LB_IN');
			$dpd_core['AZ'] = array('region' => 'AM', 'currency' => 'AZN', 'weight' => 'KG_CM');
			$dpd_core['AZ'] = array('region' => 'AM', 'currency' => 'AZN', 'weight' => 'KG_CM');
			$dpd_core['GB'] = array('region' => 'EU', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dpd_core['BA'] = array('region' => 'AP', 'currency' => 'BAM', 'weight' => 'KG_CM');
			$dpd_core['BB'] = array('region' => 'AM', 'currency' => 'BBD', 'weight' => 'LB_IN');
			$dpd_core['BD'] = array('region' => 'AP', 'currency' => 'BDT', 'weight' => 'KG_CM');
			$dpd_core['BE'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['BF'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['BG'] = array('region' => 'EU', 'currency' => 'BGN', 'weight' => 'KG_CM');
			$dpd_core['BH'] = array('region' => 'AP', 'currency' => 'BHD', 'weight' => 'KG_CM');
			$dpd_core['BI'] = array('region' => 'AP', 'currency' => 'BIF', 'weight' => 'KG_CM');
			$dpd_core['BJ'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['BM'] = array('region' => 'AM', 'currency' => 'BMD', 'weight' => 'LB_IN');
			$dpd_core['BN'] = array('region' => 'AP', 'currency' => 'BND', 'weight' => 'KG_CM');
			$dpd_core['BO'] = array('region' => 'AM', 'currency' => 'BOB', 'weight' => 'KG_CM');
			$dpd_core['BR'] = array('region' => 'AM', 'currency' => 'BRL', 'weight' => 'KG_CM');
			$dpd_core['BS'] = array('region' => 'AM', 'currency' => 'BSD', 'weight' => 'LB_IN');
			$dpd_core['BT'] = array('region' => 'AP', 'currency' => 'BTN', 'weight' => 'KG_CM');
			$dpd_core['BW'] = array('region' => 'AP', 'currency' => 'BWP', 'weight' => 'KG_CM');
			$dpd_core['BY'] = array('region' => 'AP', 'currency' => 'BYR', 'weight' => 'KG_CM');
			$dpd_core['BZ'] = array('region' => 'AM', 'currency' => 'BZD', 'weight' => 'KG_CM');
			$dpd_core['CA'] = array('region' => 'AM', 'currency' => 'CAD', 'weight' => 'LB_IN');
			$dpd_core['CF'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dpd_core['CG'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dpd_core['CH'] = array('region' => 'EU', 'currency' => 'CHF', 'weight' => 'KG_CM');
			$dpd_core['CI'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['CK'] = array('region' => 'AP', 'currency' => 'NZD', 'weight' => 'KG_CM');
			$dpd_core['CL'] = array('region' => 'AM', 'currency' => 'CLP', 'weight' => 'KG_CM');
			$dpd_core['CM'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dpd_core['CN'] = array('region' => 'AP', 'currency' => 'CNY', 'weight' => 'KG_CM');
			$dpd_core['CO'] = array('region' => 'AM', 'currency' => 'COP', 'weight' => 'KG_CM');
			$dpd_core['CR'] = array('region' => 'AM', 'currency' => 'CRC', 'weight' => 'KG_CM');
			$dpd_core['CU'] = array('region' => 'AM', 'currency' => 'CUC', 'weight' => 'KG_CM');
			$dpd_core['CV'] = array('region' => 'AP', 'currency' => 'CVE', 'weight' => 'KG_CM');
			$dpd_core['CY'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['CZ'] = array('region' => 'EU', 'currency' => 'CZK', 'weight' => 'KG_CM');
			$dpd_core['DE'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['DJ'] = array('region' => 'EU', 'currency' => 'DJF', 'weight' => 'KG_CM');
			$dpd_core['DK'] = array('region' => 'AM', 'currency' => 'DKK', 'weight' => 'KG_CM');
			$dpd_core['DM'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['DO'] = array('region' => 'AP', 'currency' => 'DOP', 'weight' => 'LB_IN');
			$dpd_core['DZ'] = array('region' => 'AM', 'currency' => 'DZD', 'weight' => 'KG_CM');
			$dpd_core['EC'] = array('region' => 'EU', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['EE'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['EG'] = array('region' => 'AP', 'currency' => 'EGP', 'weight' => 'KG_CM');
			$dpd_core['ER'] = array('region' => 'EU', 'currency' => 'ERN', 'weight' => 'KG_CM');
			$dpd_core['ES'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['ET'] = array('region' => 'AU', 'currency' => 'ETB', 'weight' => 'KG_CM');
			$dpd_core['FI'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['FJ'] = array('region' => 'AP', 'currency' => 'FJD', 'weight' => 'KG_CM');
			$dpd_core['FK'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dpd_core['FM'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['FO'] = array('region' => 'AM', 'currency' => 'DKK', 'weight' => 'KG_CM');
			$dpd_core['FR'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['GA'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dpd_core['GB'] = array('region' => 'EU', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dpd_core['GD'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['GE'] = array('region' => 'AM', 'currency' => 'GEL', 'weight' => 'KG_CM');
			$dpd_core['GF'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['GG'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dpd_core['GH'] = array('region' => 'AP', 'currency' => 'GHS', 'weight' => 'KG_CM');
			$dpd_core['GI'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dpd_core['GL'] = array('region' => 'AM', 'currency' => 'DKK', 'weight' => 'KG_CM');
			$dpd_core['GM'] = array('region' => 'AP', 'currency' => 'GMD', 'weight' => 'KG_CM');
			$dpd_core['GN'] = array('region' => 'AP', 'currency' => 'GNF', 'weight' => 'KG_CM');
			$dpd_core['GP'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['GQ'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dpd_core['GR'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['GT'] = array('region' => 'AM', 'currency' => 'GTQ', 'weight' => 'KG_CM');
			$dpd_core['GU'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['GW'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['GY'] = array('region' => 'AP', 'currency' => 'GYD', 'weight' => 'LB_IN');
			$dpd_core['HK'] = array('region' => 'AM', 'currency' => 'HKD', 'weight' => 'KG_CM');
			$dpd_core['HN'] = array('region' => 'AM', 'currency' => 'HNL', 'weight' => 'KG_CM');
			$dpd_core['HR'] = array('region' => 'AP', 'currency' => 'HRK', 'weight' => 'KG_CM');
			$dpd_core['HT'] = array('region' => 'AM', 'currency' => 'HTG', 'weight' => 'LB_IN');
			$dpd_core['HU'] = array('region' => 'EU', 'currency' => 'HUF', 'weight' => 'KG_CM');
			$dpd_core['IC'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['ID'] = array('region' => 'AP', 'currency' => 'IDR', 'weight' => 'KG_CM');
			$dpd_core['IE'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['IL'] = array('region' => 'AP', 'currency' => 'ILS', 'weight' => 'KG_CM');
			$dpd_core['IN'] = array('region' => 'AP', 'currency' => 'INR', 'weight' => 'KG_CM');
			$dpd_core['IQ'] = array('region' => 'AP', 'currency' => 'IQD', 'weight' => 'KG_CM');
			$dpd_core['IR'] = array('region' => 'AP', 'currency' => 'IRR', 'weight' => 'KG_CM');
			$dpd_core['IS'] = array('region' => 'EU', 'currency' => 'ISK', 'weight' => 'KG_CM');
			$dpd_core['IT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['JE'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dpd_core['JM'] = array('region' => 'AM', 'currency' => 'JMD', 'weight' => 'KG_CM');
			$dpd_core['JO'] = array('region' => 'AP', 'currency' => 'JOD', 'weight' => 'KG_CM');
			$dpd_core['JP'] = array('region' => 'AP', 'currency' => 'JPY', 'weight' => 'KG_CM');
			$dpd_core['KE'] = array('region' => 'AP', 'currency' => 'KES', 'weight' => 'KG_CM');
			$dpd_core['KG'] = array('region' => 'AP', 'currency' => 'KGS', 'weight' => 'KG_CM');
			$dpd_core['KH'] = array('region' => 'AP', 'currency' => 'KHR', 'weight' => 'KG_CM');
			$dpd_core['KI'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dpd_core['KM'] = array('region' => 'AP', 'currency' => 'KMF', 'weight' => 'KG_CM');
			$dpd_core['KN'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['KP'] = array('region' => 'AP', 'currency' => 'KPW', 'weight' => 'LB_IN');
			$dpd_core['KR'] = array('region' => 'AP', 'currency' => 'KRW', 'weight' => 'KG_CM');
			$dpd_core['KV'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['KW'] = array('region' => 'AP', 'currency' => 'KWD', 'weight' => 'KG_CM');
			$dpd_core['KY'] = array('region' => 'AM', 'currency' => 'KYD', 'weight' => 'KG_CM');
			$dpd_core['KZ'] = array('region' => 'AP', 'currency' => 'KZF', 'weight' => 'LB_IN');
			$dpd_core['LA'] = array('region' => 'AP', 'currency' => 'LAK', 'weight' => 'KG_CM');
			$dpd_core['LB'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['LC'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'KG_CM');
			$dpd_core['LI'] = array('region' => 'AM', 'currency' => 'CHF', 'weight' => 'LB_IN');
			$dpd_core['LK'] = array('region' => 'AP', 'currency' => 'LKR', 'weight' => 'KG_CM');
			$dpd_core['LR'] = array('region' => 'AP', 'currency' => 'LRD', 'weight' => 'KG_CM');
			$dpd_core['LS'] = array('region' => 'AP', 'currency' => 'LSL', 'weight' => 'KG_CM');
			$dpd_core['LT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['LU'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['LV'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['LY'] = array('region' => 'AP', 'currency' => 'LYD', 'weight' => 'KG_CM');
			$dpd_core['MA'] = array('region' => 'AP', 'currency' => 'MAD', 'weight' => 'KG_CM');
			$dpd_core['MC'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['MD'] = array('region' => 'AP', 'currency' => 'MDL', 'weight' => 'KG_CM');
			$dpd_core['ME'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['MG'] = array('region' => 'AP', 'currency' => 'MGA', 'weight' => 'KG_CM');
			$dpd_core['MH'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['MK'] = array('region' => 'AP', 'currency' => 'MKD', 'weight' => 'KG_CM');
			$dpd_core['ML'] = array('region' => 'AP', 'currency' => 'COF', 'weight' => 'KG_CM');
			$dpd_core['MM'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['MN'] = array('region' => 'AP', 'currency' => 'MNT', 'weight' => 'KG_CM');
			$dpd_core['MO'] = array('region' => 'AP', 'currency' => 'MOP', 'weight' => 'KG_CM');
			$dpd_core['MP'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['MQ'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['MR'] = array('region' => 'AP', 'currency' => 'MRO', 'weight' => 'KG_CM');
			$dpd_core['MS'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['MT'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['MU'] = array('region' => 'AP', 'currency' => 'MUR', 'weight' => 'KG_CM');
			$dpd_core['MV'] = array('region' => 'AP', 'currency' => 'MVR', 'weight' => 'KG_CM');
			$dpd_core['MW'] = array('region' => 'AP', 'currency' => 'MWK', 'weight' => 'KG_CM');
			$dpd_core['MX'] = array('region' => 'AM', 'currency' => 'MXN', 'weight' => 'KG_CM');
			$dpd_core['MY'] = array('region' => 'AP', 'currency' => 'MYR', 'weight' => 'KG_CM');
			$dpd_core['MZ'] = array('region' => 'AP', 'currency' => 'MZN', 'weight' => 'KG_CM');
			$dpd_core['NA'] = array('region' => 'AP', 'currency' => 'NAD', 'weight' => 'KG_CM');
			$dpd_core['NC'] = array('region' => 'AP', 'currency' => 'XPF', 'weight' => 'KG_CM');
			$dpd_core['NE'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['NG'] = array('region' => 'AP', 'currency' => 'NGN', 'weight' => 'KG_CM');
			$dpd_core['NI'] = array('region' => 'AM', 'currency' => 'NIO', 'weight' => 'KG_CM');
			$dpd_core['NL'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['NO'] = array('region' => 'EU', 'currency' => 'NOK', 'weight' => 'KG_CM');
			$dpd_core['NP'] = array('region' => 'AP', 'currency' => 'NPR', 'weight' => 'KG_CM');
			$dpd_core['NR'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dpd_core['NU'] = array('region' => 'AP', 'currency' => 'NZD', 'weight' => 'KG_CM');
			$dpd_core['NZ'] = array('region' => 'AP', 'currency' => 'NZD', 'weight' => 'KG_CM');
			$dpd_core['OM'] = array('region' => 'AP', 'currency' => 'OMR', 'weight' => 'KG_CM');
			$dpd_core['PA'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['PE'] = array('region' => 'AM', 'currency' => 'PEN', 'weight' => 'KG_CM');
			$dpd_core['PF'] = array('region' => 'AP', 'currency' => 'XPF', 'weight' => 'KG_CM');
			$dpd_core['PG'] = array('region' => 'AP', 'currency' => 'PGK', 'weight' => 'KG_CM');
			$dpd_core['PH'] = array('region' => 'AP', 'currency' => 'PHP', 'weight' => 'KG_CM');
			$dpd_core['PK'] = array('region' => 'AP', 'currency' => 'PKR', 'weight' => 'KG_CM');
			$dpd_core['PL'] = array('region' => 'EU', 'currency' => 'PLN', 'weight' => 'KG_CM');
			$dpd_core['PR'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['PT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['PW'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['PY'] = array('region' => 'AM', 'currency' => 'PYG', 'weight' => 'KG_CM');
			$dpd_core['QA'] = array('region' => 'AP', 'currency' => 'QAR', 'weight' => 'KG_CM');
			$dpd_core['RE'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['RO'] = array('region' => 'EU', 'currency' => 'RON', 'weight' => 'KG_CM');
			$dpd_core['RS'] = array('region' => 'AP', 'currency' => 'RSD', 'weight' => 'KG_CM');
			$dpd_core['RU'] = array('region' => 'AP', 'currency' => 'RUB', 'weight' => 'KG_CM');
			$dpd_core['RW'] = array('region' => 'AP', 'currency' => 'RWF', 'weight' => 'KG_CM');
			$dpd_core['SA'] = array('region' => 'AP', 'currency' => 'SAR', 'weight' => 'KG_CM');
			$dpd_core['SB'] = array('region' => 'AP', 'currency' => 'SBD', 'weight' => 'KG_CM');
			$dpd_core['SC'] = array('region' => 'AP', 'currency' => 'SCR', 'weight' => 'KG_CM');
			$dpd_core['SD'] = array('region' => 'AP', 'currency' => 'SDG', 'weight' => 'KG_CM');
			$dpd_core['SE'] = array('region' => 'EU', 'currency' => 'SEK', 'weight' => 'KG_CM');
			$dpd_core['SG'] = array('region' => 'AP', 'currency' => 'SGD', 'weight' => 'KG_CM');
			$dpd_core['SH'] = array('region' => 'AP', 'currency' => 'SHP', 'weight' => 'KG_CM');
			$dpd_core['SI'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['SK'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['SL'] = array('region' => 'AP', 'currency' => 'SLL', 'weight' => 'KG_CM');
			$dpd_core['SM'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['SN'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['SO'] = array('region' => 'AM', 'currency' => 'SOS', 'weight' => 'KG_CM');
			$dpd_core['SR'] = array('region' => 'AM', 'currency' => 'SRD', 'weight' => 'KG_CM');
			$dpd_core['SS'] = array('region' => 'AP', 'currency' => 'SSP', 'weight' => 'KG_CM');
			$dpd_core['ST'] = array('region' => 'AP', 'currency' => 'STD', 'weight' => 'KG_CM');
			$dpd_core['SV'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['SY'] = array('region' => 'AP', 'currency' => 'SYP', 'weight' => 'KG_CM');
			$dpd_core['SZ'] = array('region' => 'AP', 'currency' => 'SZL', 'weight' => 'KG_CM');
			$dpd_core['TC'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['TD'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dpd_core['TG'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dpd_core['TH'] = array('region' => 'AP', 'currency' => 'THB', 'weight' => 'KG_CM');
			$dpd_core['TJ'] = array('region' => 'AP', 'currency' => 'TJS', 'weight' => 'KG_CM');
			$dpd_core['TL'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['TN'] = array('region' => 'AP', 'currency' => 'TND', 'weight' => 'KG_CM');
			$dpd_core['TO'] = array('region' => 'AP', 'currency' => 'TOP', 'weight' => 'KG_CM');
			$dpd_core['TR'] = array('region' => 'AP', 'currency' => 'TRY', 'weight' => 'KG_CM');
			$dpd_core['TT'] = array('region' => 'AM', 'currency' => 'TTD', 'weight' => 'LB_IN');
			$dpd_core['TV'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dpd_core['TW'] = array('region' => 'AP', 'currency' => 'TWD', 'weight' => 'KG_CM');
			$dpd_core['TZ'] = array('region' => 'AP', 'currency' => 'TZS', 'weight' => 'KG_CM');
			$dpd_core['UA'] = array('region' => 'AP', 'currency' => 'UAH', 'weight' => 'KG_CM');
			$dpd_core['UG'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dpd_core['US'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['UY'] = array('region' => 'AM', 'currency' => 'UYU', 'weight' => 'KG_CM');
			$dpd_core['UZ'] = array('region' => 'AP', 'currency' => 'UZS', 'weight' => 'KG_CM');
			$dpd_core['VC'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['VE'] = array('region' => 'AM', 'currency' => 'VEF', 'weight' => 'KG_CM');
			$dpd_core['VG'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['VI'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dpd_core['VN'] = array('region' => 'AP', 'currency' => 'VND', 'weight' => 'KG_CM');
			$dpd_core['VU'] = array('region' => 'AP', 'currency' => 'VUV', 'weight' => 'KG_CM');
			$dpd_core['WS'] = array('region' => 'AP', 'currency' => 'WST', 'weight' => 'KG_CM');
			$dpd_core['XB'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'LB_IN');
			$dpd_core['XC'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'LB_IN');
			$dpd_core['XE'] = array('region' => 'AM', 'currency' => 'ANG', 'weight' => 'LB_IN');
			$dpd_core['XM'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'LB_IN');
			$dpd_core['XN'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dpd_core['XS'] = array('region' => 'AP', 'currency' => 'SIS', 'weight' => 'KG_CM');
			$dpd_core['XY'] = array('region' => 'AM', 'currency' => 'ANG', 'weight' => 'LB_IN');
			$dpd_core['YE'] = array('region' => 'AP', 'currency' => 'YER', 'weight' => 'KG_CM');
			$dpd_core['YT'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dpd_core['ZA'] = array('region' => 'AP', 'currency' => 'ZAR', 'weight' => 'KG_CM');
			$dpd_core['ZM'] = array('region' => 'AP', 'currency' => 'ZMW', 'weight' => 'KG_CM');
			$dpd_core['ZW'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');

			$custom_settings = array();
			$custom_settings['default'] = array(
				'a2z_dpdshipping_site_id' => isset($general_settings['a2z_dpdshipping_site_id'])? $general_settings['a2z_dpdshipping_site_id'] : '',
				'a2z_dpdshipping_site_pwd' => isset($general_settings['a2z_dpdshipping_site_pwd'])? $general_settings['a2z_dpdshipping_site_pwd'] : '',
				'a2z_dpdshipping_acc_no' => isset($general_settings['a2z_dpdshipping_acc_no'])? $general_settings['a2z_dpdshipping_acc_no'] : '',
				'a2z_dpdshipping_import_no' => isset($general_settings['a2z_dpdshipping_import_no'])? $general_settings['a2z_dpdshipping_import_no']: '',
				'a2z_dpdshipping_shipper_name' => isset($general_settings['a2z_dpdshipping_shipper_name'])?$general_settings['a2z_dpdshipping_site_pwd'] : '',
				'a2z_dpdshipping_company' => isset($general_settings['a2z_dpdshipping_company'])?$general_settings['a2z_dpdshipping_company'] : '',
				'a2z_dpdshipping_mob_num' => isset($general_settings['a2z_dpdshipping_mob_num'])?$general_settings['a2z_dpdshipping_mob_num'] : '',
				'a2z_dpdshipping_email' => isset($general_settings['a2z_dpdshipping_email'])?$general_settings['a2z_dpdshipping_email'] : '',
				'a2z_dpdshipping_address1' => isset($general_settings['a2z_dpdshipping_address1'])?$general_settings['a2z_dpdshipping_address1'] : '',
				'a2z_dpdshipping_address2' => isset($general_settings['a2z_dpdshipping_address2'])?$general_settings['a2z_dpdshipping_address2'] : '',
				'a2z_dpdshipping_city' => isset($general_settings['a2z_dpdshipping_city'])?$general_settings['a2z_dpdshipping_city'] : '',
				'a2z_dpdshipping_state' => isset($general_settings['a2z_dpdshipping_state'])? $general_settings['a2z_dpdshipping_state']: '',
				'a2z_dpdshipping_zip' => isset($general_settings['a2z_dpdshipping_zip'])?$general_settings['a2z_dpdshipping_zip'] : '',
				'a2z_dpdshipping_country' => isset($general_settings['a2z_dpdshipping_country'])?$general_settings['a2z_dpdshipping_country'] : '',
				'a2z_dpdshipping_gstin' => isset($general_settings['a2z_dpdshipping_gstin'])?$general_settings['a2z_dpdshipping_gstin'] : '',
				'a2z_dpdshipping_con_rate' => isset($general_settings['a2z_dpdshipping_con_rate'])? $general_settings['a2z_dpdshipping_con_rate']: '',
			);
			$vendor_settings = array();

			if (isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes' && isset($general_settings['a2z_dpdshipping_v_rates']) && $general_settings['a2z_dpdshipping_v_rates'] == 'yes') {
				// Multi Vendor Enabled
				foreach ($pack_aft_hook['contents'] as $key => $value) {
					$product_id = $value['product_id'];
					$dpd_account = get_post_meta($product_id, 'dpd_express_address', true);
					if (empty($dpd_account) || $dpd_account == 'default') {
						$dpd_account = 'default';
						if (!isset($vendor_settings[$dpd_account])) {
							$vendor_settings[$dpd_account] = $custom_settings['default'];
						}

						$vendor_settings[$dpd_account]['products'][] = $value;
					}

					if ($dpd_account != 'default') {
						$user_account = get_post_meta($dpd_account, 'a2z_dpd_vendor_settings', true);
						$user_account = empty($user_account) ? array() : $user_account;
						if (!empty($user_account)) {
							if (!isset($vendor_settings[$dpd_account])) {

								$vendor_settings[$dpd_account] = $custom_settings['default'];

								if ($user_account['a2z_dpdshipping_site_id'] != '' && $user_account['a2z_dpdshipping_site_pwd'] != '' && $user_account['a2z_dpdshipping_acc_no'] != '') {

									$vendor_settings[$dpd_account]['a2z_dpdshipping_site_id'] = $user_account['a2z_dpdshipping_site_id'];

									if ($user_account['a2z_dpdshipping_site_pwd'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_site_pwd'] = $user_account['a2z_dpdshipping_site_pwd'];
									}

									if ($user_account['a2z_dpdshipping_acc_no'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_acc_no'] = $user_account['a2z_dpdshipping_acc_no'];
									}

									$vendor_settings[$dpd_account]['a2z_dpdshipping_import_no'] = !empty($user_account['a2z_dpdshipping_import_no']) ? $user_account['a2z_dpdshipping_import_no'] : '';
								}

								if ($user_account['a2z_dpdshipping_address1'] != '' && $user_account['a2z_dpdshipping_city'] != '' && $user_account['a2z_dpdshipping_state'] != '' && $user_account['a2z_dpdshipping_zip'] != '' && $user_account['a2z_dpdshipping_country'] != '' && $user_account['a2z_dpdshipping_shipper_name'] != '') {

									if ($user_account['a2z_dpdshipping_shipper_name'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_shipper_name'] = $user_account['a2z_dpdshipping_shipper_name'];
									}

									if ($user_account['a2z_dpdshipping_company'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_company'] = $user_account['a2z_dpdshipping_company'];
									}

									if ($user_account['a2z_dpdshipping_mob_num'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_mob_num'] = $user_account['a2z_dpdshipping_mob_num'];
									}

									if ($user_account['a2z_dpdshipping_email'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_email'] = $user_account['a2z_dpdshipping_email'];
									}

									if ($user_account['a2z_dpdshipping_address1'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_address1'] = $user_account['a2z_dpdshipping_address1'];
									}

									$vendor_settings[$dpd_account]['a2z_dpdshipping_address2'] = $user_account['a2z_dpdshipping_address2'];

									if ($user_account['a2z_dpdshipping_city'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_city'] = $user_account['a2z_dpdshipping_city'];
									}

									if ($user_account['a2z_dpdshipping_state'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_state'] = $user_account['a2z_dpdshipping_state'];
									}

									if ($user_account['a2z_dpdshipping_zip'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_zip'] = $user_account['a2z_dpdshipping_zip'];
									}

									if ($user_account['a2z_dpdshipping_country'] != '') {
										$vendor_settings[$dpd_account]['a2z_dpdshipping_country'] = $user_account['a2z_dpdshipping_country'];
									}

									$vendor_settings[$dpd_account]['a2z_dpdshipping_gstin'] = $user_account['a2z_dpdshipping_gstin'];
									$vendor_settings[$dpd_account]['a2z_dpdshipping_con_rate'] = $user_account['a2z_dpdshipping_con_rate'];
								}
							}

							$vendor_settings[$dpd_account]['products'][] = $value;
						}
					}
				}
			}

			if (empty($vendor_settings)) {
				$custom_settings['default']['products'] = $pack_aft_hook['contents'];
			} else {
				$custom_settings = $vendor_settings;
			}

			$mesage_time = date('c');
			$message_date = date('Y-m-d');
			$weight_unit = $dim_unit = '';
			if (!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM') {
				$weight_unit = 'KG';
				$dim_unit = 'CM';
			} else {
				$weight_unit = 'LB';
				$dim_unit = 'IN';
			}

			if (!isset($general_settings['a2z_dpdshipping_packing_type'])) {
				return;
			}


			$woo_weight_unit = get_option('woocommerce_weight_unit');
			$woo_dimension_unit = get_option('woocommerce_dimension_unit');

			$dpd_mod_weight_unit = $dpd_mod_dim_unit = '';

			if (!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM') {
				$dpd_mod_weight_unit = 'kg';
				$dpd_mod_dim_unit = 'cm';
			} elseif (!empty($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'LB_IN') {
				$dpd_mod_weight_unit = 'lbs';
				$dpd_mod_dim_unit = 'in';
			} else {
				$dpd_mod_weight_unit = 'kg';
				$dpd_mod_dim_unit = 'cm';
			}

			$shipping_rates = array();
			if (isset($general_settings['a2z_dpdshipping_developer_rate']) && $general_settings['a2z_dpdshipping_developer_rate'] == 'yes') {
				echo "<pre>";
			}

			foreach ($custom_settings as $key => $value) {

			if (isset($general_settings['a2z_dpdshipping_auto_con_rate']) && $general_settings['a2z_dpdshipping_auto_con_rate'] == "yes") {
				$current_date = date('m-d-Y', time());
				$ex_rate_data = get_option('a2z_dpd_ex_rate'.$key);
				$ex_rate_data = !empty($ex_rate_data) ? $ex_rate_data : array();
				if (empty($ex_rate_data) || (isset($ex_rate_data['date']) && $ex_rate_data['date'] != $current_date) ) {
					if (isset($general_settings['a2z_dpdshipping_country']) && !empty($general_settings['a2z_dpdshipping_country']) && isset($general_settings['a2z_dpdshipping_integration_key']) && !empty($general_settings['a2z_dpdshipping_integration_key'])) {
						$frm_curr = get_option('woocommerce_currency');
						$to_curr = isset($dpd_core[$general_settings['a2z_dpdshipping_country']]) ? $dpd_core[$general_settings['a2z_dpdshipping_country']]['currency'] : '';
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
			}
				$to_city = $pack_aft_hook['destination']['city'];
				if (isset($general_settings['a2z_dpdshipping_translation']) && $general_settings['a2z_dpdshipping_translation'] == "yes" ) {
					if (isset($general_settings['a2z_dpdshipping_translation_key']) && !empty($general_settings['a2z_dpdshipping_translation_key'])) {
						include_once('classes/gtrans/vendor/autoload.php');
						if (!empty($to_city)) {
			                if (!preg_match('%^[ -~]+$%', $to_city))      //Cheks english or not  /[^A-Za-z0-9]+/
			                {
			                  $response =array();
			                  try{
			                    $translate = new TranslateClient(['key' => $general_settings['a2z_dpdshipping_translation_key']]);
			                    // Tranlate text
			                    $response = $translate->translate($to_city, [
			                        'target' => 'en',
			                    ]);
			                  }catch(exception $e){
			                    // echo "\n Exception Caught" . $e->getMessage(); //Error handling
			                  }
			                  if (!empty($response) && isset($response['text']) && !empty($response['text'])) {
			                    $to_city = $response['text'];
			                  }
			                }
			            }
					}
				}

				$shipping_rates[$key] = array();

				$orgin_postalcode_or_city = $this->a2z_get_zipcode_or_city($value['a2z_dpdshipping_country'], $value['a2z_dpdshipping_city'], $value['a2z_dpdshipping_zip']);

				$destination_postcode_city = $this->a2z_get_zipcode_or_city($pack_aft_hook['destination']['country'], $to_city, $pack_aft_hook['destination']['postcode']);

				$general_settings['a2z_dpdshipping_currency'] = isset($dpd_core[(isset($value['a2z_dpdshipping_country']) ? $value['a2z_dpdshipping_country'] : 'A2Z')]) ? $dpd_core[$value['a2z_dpdshipping_country']]['currency'] : '';

				$dpd_packs = $this->hit_get_dpd_packages($value['products'], $general_settings, $general_settings['a2z_dpdshipping_currency']);

				$pieces = "";
				$index = 0;
				if ($dpd_packs) {
					foreach ($dpd_packs as $parcel) {
						$index = $index + 1;
						$pieces .= '<Piece><PieceID>' . $index . '</PieceID>';
						$pieces .= '<PackageTypeCode>' . $parcel['packtype'] . '</PackageTypeCode>';

						if (isset($parcel['Dimensions']['Height']) && !empty($parcel['Dimensions']['Height']) && !empty($parcel['Dimensions']['Length']) && !empty($parcel['Dimensions']['Width'])) {

							if ($woo_dimension_unit != $dpd_mod_dim_unit) {
								//wc_get_dimension( $dimension, $to_unit, $from_unit );
								$pieces .= '<Height>' . round(wc_get_dimension($parcel['Dimensions']['Height'], $dpd_mod_dim_unit, $woo_dimension_unit), 2) . '</Height>';
								$pieces .= '<Depth>' . round(wc_get_dimension($parcel['Dimensions']['Length'], $dpd_mod_dim_unit, $woo_dimension_unit), 2) . '</Depth>';
								$pieces .= '<Width>' . round(wc_get_dimension($parcel['Dimensions']['Width'], $dpd_mod_dim_unit, $woo_dimension_unit), 2) . '</Width>';
							} else {
								$pieces .= '<Height>' . $parcel['Dimensions']['Height'] . '</Height>';
								$pieces .= '<Depth>' . $parcel['Dimensions']['Length'] . '</Depth>';
								$pieces .= '<Width>' . $parcel['Dimensions']['Width'] . '</Width>';
							}
						}
						$total_weight   = (string) $parcel['Weight']['Value'];
						$total_weight   = str_replace(',', '.', $total_weight);
						if ($total_weight < 0.001) {
							$total_weight = 0.001;
						} else {
							$total_weight = round((float)$total_weight, 3);
						}
						if ($woo_weight_unit != $dpd_mod_weight_unit) {
							$pieces .= '<Weight>' . round(wc_get_weight($total_weight, $dpd_mod_weight_unit, $woo_weight_unit), 2) . '</Weight></Piece>';
						} else {
							$pieces .= '<Weight>' . $total_weight . '</Weight></Piece>';
						}
					}
				}

				$fetch_accountrates = (isset($general_settings['a2z_dpdshipping_account_rates']) && $general_settings['a2z_dpdshipping_account_rates'] == "yes") ? "<PaymentAccountNumber>" . $value['a2z_dpdshipping_acc_no'] . "</PaymentAccountNumber>" : "";
// || $this->a2z_dpd_is_eu_country($value['a2z_dpdshipping_country'], $pack_aft_hook['destination']['country'])
				$dutiable = ( ($pack_aft_hook['destination']['country'] == $value['a2z_dpdshipping_country']) ) ? "N" : "Y";
				if($pack_aft_hook['destination']['country'] == 'AT' && $value['a2z_dpdshipping_country'] == 'CZ'){
					$dutiable = "N";
				}
				if($pack_aft_hook['destination']['country'] == 'NL' && $value['a2z_dpdshipping_country'] == 'SE'){
					$dutiable = "N";
				}
				$cart_total = 0;

				if (isset($pack_aft_hook['cart_subtotal'])) {
					$cart_total += $pack_aft_hook['cart_subtotal'];
				}else{
					foreach ($pack_aft_hook['contents'] as $item_id => $values) {
						$cart_total += (float) $values['line_subtotal'];
					}
				}

				if ($general_settings['a2z_dpdshipping_currency'] != get_option('woocommerce_currency')) {
					if (isset($general_settings['a2z_dpdshipping_auto_con_rate']) && $general_settings['a2z_dpdshipping_auto_con_rate'] == "yes") {
						$get_ex_rate = get_option('a2z_dpd_ex_rate'.$key, '');
						$get_ex_rate = !empty($get_ex_rate) ? $get_ex_rate : array();
						$exchange_rate = ( !empty($get_ex_rate) && isset($get_ex_rate['ex_rate']) ) ? $get_ex_rate['ex_rate'] : 0;
					}else{
						$exchange_rate = $value['a2z_dpdshipping_con_rate'];
					}

					if ($exchange_rate && $exchange_rate > 0) {
						$cart_total *= $exchange_rate;
					}
				}


				$dutiable_content = ($dutiable == "Y") ? "<Dutiable><DeclaredCurrency>" . $general_settings['a2z_dpdshipping_currency'] . "</DeclaredCurrency><DeclaredValue>" . $cart_total . "</DeclaredValue></Dutiable>" : "";

				$insurance_details = (isset($general_settings['a2z_dpdshipping_insure']) && $general_settings['a2z_dpdshipping_insure'] == 'yes')  ? "<QtdShp><QtdShpExChrg><SpecialServiceType>II</SpecialServiceType><LocalSpecialServiceType>XCH</LocalSpecialServiceType></QtdShpExChrg></QtdShp><InsuredValue>" . round($cart_total, 2) . "</InsuredValue><InsuredCurrency>" . $general_settings['a2z_dpdshipping_currency'] . "</InsuredCurrency>" : ""; //insurance type

				$xmlRequest =  '';
				$order_total = 0;
				foreach ($pack_aft_hook['contents'] as $item_id => $values) {
					$order_total += (float) $values['line_subtotal'];
				}

				$pay_con = $value['a2z_dpdshipping_country'];

				if (isset($general_settings['a2z_dpdshipping_pay_con']) && $general_settings['a2z_dpdshipping_pay_con'] == "R") {
					$pay_con = $pack_aft_hook['destination']['country'];
				}elseif (isset($general_settings['a2z_dpdshipping_pay_con']) && $general_settings['a2z_dpdshipping_pay_con'] == "C") {
					if (isset($general_settings['a2z_dpdshipping_cus_pay_con']) && !empty($general_settings['a2z_dpdshipping_cus_pay_con'])) {
						$pay_con = $general_settings['a2z_dpdshipping_cus_pay_con'];
					}
				}

				$xmlRequest = str_replace('{mesage_time}', $mesage_time, $xmlRequest);
				$xmlRequest = str_replace('{siteid}', $value['a2z_dpdshipping_site_id'], $xmlRequest);
				$xmlRequest = str_replace('{pwd}', $value['a2z_dpdshipping_site_pwd'], $xmlRequest);
				$xmlRequest = str_replace('{base_co}', $value['a2z_dpdshipping_country'], $xmlRequest);
				$xmlRequest = str_replace('{pay_con}', $pay_con, $xmlRequest);
				$xmlRequest = str_replace('{org_pos}', $orgin_postalcode_or_city, $xmlRequest);
				$xmlRequest = str_replace('{mail_date}', $message_date, $xmlRequest);
				$xmlRequest = str_replace('{dim_unit}', $dim_unit, $xmlRequest);
				$xmlRequest = str_replace('{weight_unit}', $weight_unit, $xmlRequest);
				$xmlRequest = str_replace('{pieces}', $pieces, $xmlRequest);
				$xmlRequest = str_replace('{fetch_accountrates}', $fetch_accountrates, $xmlRequest);
				$xmlRequest = str_replace('{is_dutiable}', $dutiable, $xmlRequest);
				$xmlRequest = str_replace('{additional_insurance_details}', '', $xmlRequest);
				$xmlRequest = str_replace('{insurance_details}', $insurance_details, $xmlRequest);
				$xmlRequest = str_replace('{customerAddressIso}', $pack_aft_hook['destination']['country'], $xmlRequest);
				$xmlRequest = str_replace('{destination_postcode_city}', $destination_postcode_city, $xmlRequest);
				$xmlRequest = str_replace('{dutiable_content}', $dutiable_content, $xmlRequest);

				$request_url = (isset($general_settings['a2z_dpdshipping_test']) && $general_settings['a2z_dpdshipping_test'] != 'yes') ? 'https://xmlpi-ea.dpd.com/XMLShippingServlet?isUTF8Support=true' : 'https://xmlpitest-ea.dpd.com/XMLShippingServlet?isUTF8Support=true';
				$result = wp_remote_post($request_url, array(
					'method' => 'POST',
					'timeout' => 70,
					'sslverify' => 0,
					'body' => $xmlRequest,
					'sslverify'   => FALSE
				));

				libxml_use_internal_errors(true);
				if (is_array($result) && isset($result['body'])) {
					@$xml = simplexml_load_string(utf8_encode($result['body']));
				}

				if (isset($general_settings['a2z_dpdshipping_developer_rate']) && $general_settings['a2z_dpdshipping_developer_rate'] == 'yes') {
					echo "<h1> Request </h1><br/>";
					print_r(htmlspecialchars($xmlRequest));
					echo "<br/><h1> Response </h1><br/>";
					print_r($xml);
				}

				if ($xml && !empty($xml->GetQuoteResponse->BkgDetails->QtdShp)) {
					$rate = array();
					foreach ($xml->GetQuoteResponse->BkgDetails->QtdShp as $quote) {
						$rate_code = ((string) $quote->GlobalProductCode);
						$rate_cost = (float)((string) $quote->ShippingCharge);

						if (isset($general_settings['a2z_dpdshipping_excul_tax']) && $general_settings['a2z_dpdshipping_excul_tax'] == "yes") {
							$rate_tax = (float)((string) $quote->TotalTaxAmount);
							if (!empty($rate_tax) && $rate_tax > 0) {
								$rate_cost -= $rate_tax;
							}
						}

						if ($general_settings['a2z_dpdshipping_currency'] != get_option('woocommerce_currency')) {
							if (isset($general_settings['a2z_dpdshipping_auto_con_rate']) && $general_settings['a2z_dpdshipping_auto_con_rate'] == "yes") {
								$get_ex_rate = get_option('a2z_dpd_ex_rate'.$key, '');
								$get_ex_rate = !empty($get_ex_rate) ? $get_ex_rate : array();
								$exchange_rate = ( !empty($get_ex_rate) && isset($get_ex_rate['ex_rate']) ) ? $get_ex_rate['ex_rate'] : 0;
							}else{
								$exchange_rate = $value['a2z_dpdshipping_con_rate'];
							}
								if ($exchange_rate && $exchange_rate > 0) {
									$rate_cost /= $exchange_rate;
								}

						}
						$quote_cur_code = (string)$quote->CurrencyCode;

						if ($general_settings['a2z_dpdshipping_currency'] != $quote_cur_code) {
							foreach ($quote->QtdSInAdCur as $c => $con) {
								$con_curr_code = (string)$con->CurrencyCode;
								if (isset($con_curr_code) && $con_curr_code == $general_settings['a2z_dpdshipping_currency']) {
									$rate_cost = (float)(string)$con->TotalAmount;
								}
							}
						}
						$rate[$rate_code] = $rate_cost;
						$etd_time = '';
						if (isset($quote->DeliveryDate) && isset($quote->DeliveryTime)) {

							$formated_date = DateTime::createFromFormat('Y-m-d h:i:s', (string)$quote->DeliveryDate->DlvyDateTime);
							$etd_date = $formated_date->format('d/m/Y');
							$etd = apply_filters('hitstacks_dpd_delivery_date', " (Etd.Delivery " . $etd_date . ")", $etd_date, $etd_time);
							// print_r($etd_date);print_r($etd_time);
							// print_r($etd);

							// die();
						}
					}

					$shipping_rates[$key] = $rate;
				}
			}

			if (isset($general_settings['a2z_dpdshipping_developer_rate']) && $general_settings['a2z_dpdshipping_developer_rate'] == 'yes') {
				die();
			}

			// Rate Processing



			if (!empty($shipping_rates)) {
				$i = 0;
				$final_price = array();
				foreach ($shipping_rates as $mkey => $rate) {
					$cheap_p = 0;
					$cheap_s = '';
					foreach ($rate as $key => $cvalue) {
						if ($i > 0) {

							if (!in_array($key, array('C', 'Q'))) {
								if ($cheap_p == 0 && $cheap_s == '') {
									$cheap_p = $cvalue;
									$cheap_s = $key;
								} else if ($cheap_p > $cvalue) {
									$cheap_p = $cvalue;
									$cheap_s = $key;
								}
							}
						} else {
							$final_price[] = array('price' => $cvalue, 'code' => $key, 'multi_v' => $mkey . '_' . $key);
						}
					}

					if ($cheap_p != 0 && $cheap_s != '') {
						foreach ($final_price as $key => $value) {
							$value['price'] = $value['price'] + $cheap_p;
							$value['multi_v'] = $value['multi_v'] . '|' . $mkey . '_' . $cheap_s;
							$final_price[$key] = $value;
						}
					}

					$i++;
				}

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

				foreach ($final_price as $key => $value) {

					$rate_cost = $value['price'];
					$rate_code = $value['code'];
					$multi_ven = $value['multi_v'];

					if (!empty($general_settings['a2z_dpdshipping_carrier_adj_percentage'][$rate_code])) {
						$rate_cost += $rate_cost * ($general_settings['a2z_dpdshipping_carrier_adj_percentage'][$rate_code] / 100);
					}
					if (!empty($general_settings['a2z_dpdshipping_carrier_adj'][$rate_code])) {
						$rate_cost += $general_settings['a2z_dpdshipping_carrier_adj'][$rate_code];
					}

					$rate_cost = round($rate_cost, 2);

					$carriers_available = isset($general_settings['a2z_dpdshipping_carrier']) && is_array($general_settings['a2z_dpdshipping_carrier']) ? $general_settings['a2z_dpdshipping_carrier'] : array();

					$carriers_name_available = isset($general_settings['a2z_dpdshipping_carrier_name']) && is_array($general_settings['a2z_dpdshipping_carrier']) ? $general_settings['a2z_dpdshipping_carrier_name'] : array();
					echo"<pre>";print_r($rate_cost);die();
					if (array_key_exists($rate_code, $carriers_available)) {
						$name = isset($carriers_name_available[$rate_code]) && !empty($carriers_name_available[$rate_code]) ? $carriers_name_available[$rate_code] : $_dpd_carriers[$rate_code];

						$rate_cost = apply_filters('hitstacks_dpdshipping_rate_cost', $rate_cost, $rate_code, $order_total);
						if ($rate_cost < 1) {
							$name .= ' - Free';
						}
						if (isset($general_settings['a2z_dpdshipping_etd_date']) && $general_settings['a2z_dpdshipping_etd_date'] == 'yes') {

							$name .= $etd;
						}

						if (!isset($general_settings['a2z_dpdshipping_v_rates']) || $general_settings['a2z_dpdshipping_v_rates'] != 'yes') {
							$multi_ven = '';
						}




						// This is where you'll add your rates
						$rate = array(
							'id'       => 'a2z' . $rate_code,
							'label'    => $name,
							'cost'     => apply_filters("hitstacks_shipping_cost_conversion", $rate_cost),
							'meta_data' => array('a2z_multi_ven' => $multi_ven, 'a2z_dpd_service' => $rate_code)
						);

						// Register the rate

						$this->add_rate($rate);
					}
				}
			}
		}

		public function hit_get_dpd_packages($package, $general_settings, $orderCurrency, $chk = false)
		{
			switch ($general_settings['a2z_dpdshipping_packing_type']) {
				case 'box':
					return $this->box_shipping($package, $general_settings, $orderCurrency, $chk);
					break;
				case 'weight_based':
					return $this->weight_based_shipping($package, $general_settings, $orderCurrency, $chk);
					break;
				case 'per_item':
				default:
					return $this->per_item_shipping($package, $general_settings, $orderCurrency, $chk);
					break;
			}
		}
		private function weight_based_shipping($package, $general_settings, $orderCurrency, $chk = false)
		{
			// echo '<pre>';
			// print_r($package);
			// die();
			if (!class_exists('WeightPack')) {
				include_once 'classes/weight_pack/class-hit-weight-packing.php';
			}
			$max_weight = isset($general_settings['a2z_dpdshipping_max_weight']) && $general_settings['a2z_dpdshipping_max_weight'] != ''  ? $general_settings['a2z_dpdshipping_max_weight'] : 10;
			$weight_pack = new WeightPack('pack_ascending');
			$weight_pack->set_max_weight($max_weight);

			$package_total_weight = 0;
			$insured_value = 0;

			$ctr = 0;
			foreach ($package as $item_id => $values) {
				$ctr++;
				$product = $values['data'];
				$product_data = $product->get_data();

				$get_prod = wc_get_product($values['product_id']);

				if (!isset($product_data['weight']) || empty($product_data['weight'])) {

					if ($get_prod->is_type('variable')) {
						$parent_prod_data = $product->get_parent_data();

						if (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) {
							$product_data['weight'] = !empty($parent_prod_data['weight'] ? $parent_prod_data['weight'] : 0.001);
						} else {
							$product_data['weight'] = 0.001;
						}
					} else {
						$product_data['weight'] = 0.001;
					}
				}

				$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];

				$weight_pack->add_item($product_data['weight'], $values, $chk_qty);
			}

			$pack   =   $weight_pack->pack_items();
			$errors =   $pack->get_errors();
			if (!empty($errors)) {
				//do nothing
				return;
			} else {
				$boxes    =   $pack->get_packed_boxes();
				$unpacked_items =   $pack->get_unpacked_items();

				$insured_value        =   0;

				$packages      =   array_merge($boxes, $unpacked_items); // merge items if unpacked are allowed
				$package_count  =   sizeof($packages);
				// get all items to pass if item info in box is not distinguished
				$packable_items =   $weight_pack->get_packable_items();
				$all_items    =   array();
				if (is_array($packable_items)) {
					foreach ($packable_items as $packable_item) {
						$all_items[]    =   $packable_item['data'];
					}
				}
				//pre($packable_items);
				$order_total = '';

				$to_ship  = array();
				$group_id = 1;
				foreach ($packages as $package) { //pre($package);
					$packed_products = array();
					if (($package_count  ==  1) && isset($order_total)) {
						$insured_value  =  (isset($product_data['product_price']) ? $product_data['product_price'] : $product_data['price']) * (isset($values['product_quantity']) ? $values['product_quantity'] : $values['quantity']);
					} else {
						$insured_value  =   0;
						if (!empty($package['items'])) {
							foreach ($package['items'] as $item) {

								$insured_value        =   $insured_value; //+ $item->price;
							}
						} else {
							if (isset($order_total) && $package_count) {
								$insured_value  =   $order_total / $package_count;
							}
						}
					}
					$packed_products    =   isset($package['items']) ? $package['items'] : $all_items;
					// Creating package request
					$package_total_weight   = $package['weight'];

					$insurance_array = array(
						'Amount' => $insured_value,
						'Currency' => $orderCurrency
					);

					$group = array(
						'GroupNumber' => $group_id,
						'GroupPackageCount' => 1,
						'Weight' => array(
							'Value' => round($package_total_weight, 3),
							'Units' => (isset($general_settings['weg_dim']) && $general_settings['weg_dim'] === 'yes') ? 'KG' : 'LBS'
						),
						'packed_products' => $packed_products,
					);
					$group['InsuredValue'] = $insurance_array;
					$group['packtype'] = 'BOX';

					$to_ship[] = $group;
					$group_id++;
				}
			}
			return $to_ship;
		}
		private function box_shipping($package, $general_settings, $orderCurrency, $chk = false)
		{
			if (!class_exists('Shipi_Boxpack')) {
				include_once 'classes/hit-box-packing.php';
			}
			$boxpack = new Shipi_Boxpack();
			$boxes = isset($general_settings['a2z_dpdshipping_boxes']) ? $general_settings['a2z_dpdshipping_boxes'] : array();
			if (empty($boxes)) {
				return false;
			}
			// $boxes = unserialize($boxes);
			// Define boxes
			foreach ($boxes as $key => $box) {
				if (!$box['enabled']) {
					continue;
				}
				$box['pack_type'] = !empty($box['pack_type']) ? $box['pack_type'] : 'BOX';

				$newbox = $boxpack->add_box($box['length'], $box['width'], $box['height'], $box['box_weight'], $box['pack_type']);

				if (isset($box['id'])) {
					$newbox->set_id(current(explode(':', $box['id'])));
				}

				if ($box['max_weight']) {
					$newbox->set_max_weight($box['max_weight']);
				}

				if ($box['pack_type']) {
					$newbox->set_packtype($box['pack_type']);
				}
			}

			// Add items
			foreach ($package as $item_id => $values) {

				$product = $values['data'];
				$product_data = $product->get_data();
				$get_prod = wc_get_product($values['product_id']);
				$parent_prod_data = [];

				if ($get_prod->is_type('variable')) {
					$parent_prod_data = $product->get_parent_data();
				}

				if (isset($product_data['weight']) && !empty($product_data['weight'])) {
					$item_weight = round($product_data['weight'] > 0.001 ? $product_data['weight'] : 0.001, 3);
				} else {
					$item_weight = (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) ? (round($parent_prod_data['weight'] > 0.001 ? $parent_prod_data['weight'] : 0.001, 3)) : 0.001;
				}

				if (isset($product_data['width']) && isset($product_data['height']) && isset($product_data['length']) && !empty($product_data['width']) && !empty($product_data['height']) && !empty($product_data['length'])) {
					$item_dimension = array(
						'Length' => max(1, round($product_data['length'], 3)),
						'Width' => max(1, round($product_data['width'], 3)),
						'Height' => max(1, round($product_data['height'], 3))
					);
				} elseif (isset($parent_prod_data['width']) && isset($parent_prod_data['height']) && isset($parent_prod_data['length']) && !empty($parent_prod_data['width']) && !empty($parent_prod_data['height']) && !empty($parent_prod_data['length'])) {
					$item_dimension = array(
						'Length' => max(1, round($parent_prod_data['length'], 3)),
						'Width' => max(1, round($parent_prod_data['width'], 3)),
						'Height' => max(1, round($parent_prod_data['height'], 3))
					);
				}

				if (isset($item_weight) && isset($item_dimension)) {

					// $dimensions = array($values['depth'], $values['height'], $values['width']);
					$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];
					for ($i = 0; $i < $chk_qty; $i++) {
						$boxpack->add_item($item_dimension['Width'], $item_dimension['Height'], $item_dimension['Length'], $item_weight, round($product_data['price']), array(
							'data' => $values
						));
					}
				} else {
					//    $this->debug(sprintf(__('Product #%s is missing dimensions. Aborting.', 'wf-shipping-dpd'), $item_id), 'error');
					return;
				}
			}

			// Pack it
			$boxpack->pack();
			$packages = $boxpack->get_packages();
			$to_ship = array();
			$group_id = 1;
			foreach ($packages as $package) {
				if ($package->unpacked === true) {
					//$this->debug('Unpacked Item');
				} else {
					//$this->debug('Packed ' . $package->id);
				}

				$dimensions = array($package->length, $package->width, $package->height);

				sort($dimensions);
				$insurance_array = array(
					'Amount' => round($package->value),
					'Currency' => $orderCurrency
				);


				$group = array(
					'GroupNumber' => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => round($package->weight, 3),
						'Units' => (isset($general_settings['weg_dim']) && $general_settings['weg_dim'] === 'yes') ? 'KG' : 'LBS'
					),
					'Dimensions' => array(
						'Length' => max(1, round($dimensions[2], 3)),
						'Width' => max(1, round($dimensions[1], 3)),
						'Height' => max(1, round($dimensions[0], 3)),
						'Units' => (isset($general_settings['weg_dim']) && $general_settings['weg_dim'] === 'yes') ? 'CM' : 'IN'
					),
					'InsuredValue' => $insurance_array,
					'packed_products' => array(),
					'package_id' => $package->id,
					'packtype' => 'BOX'
				);

				if (!empty($package->packed) && is_array($package->packed)) {
					foreach ($package->packed as $packed) {
						$group['packed_products'][] = $packed->get_meta('data');
					}
				}

				if (!$package->packed) {
					foreach ($package->unpacked as $unpacked) {
						$group['packed_products'][] = $unpacked->get_meta('data');
					}
				}

				$to_ship[] = $group;

				$group_id++;
			}

			return $to_ship;
		}
		private function per_item_shipping($package, $general_settings, $orderCurrency, $chk = false)
		{
			$to_ship = array();
			$group_id = 1;

			// Get weight of order
			foreach ($package as $item_id => $values) {
				$product = $values['data'];
				$product_data = $product->get_data();
				$get_prod = wc_get_product($values['product_id']);
				$parent_prod_data = [];

				if ($get_prod->is_type('variable')) {
					$parent_prod_data = $product->get_parent_data();
				}

				$group = array();
				$insurance_array = array(
					'Amount' => round($product_data['price']),
					'Currency' => $orderCurrency
				);

				if (isset($product_data['weight']) && !empty($product_data['weight'])) {
					$dpd_per_item_weight = round($product_data['weight'] > 0.001 ? $product_data['weight'] : 0.001, 3);
				} else {
					$dpd_per_item_weight = (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) ? (round($parent_prod_data['weight'] > 0.001 ? $parent_prod_data['weight'] : 0.001, 3)) : 0.001;
				}

				$group = array(
					'GroupNumber' => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => $dpd_per_item_weight,
						'Units' => (isset($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM') ? 'KG' : 'LBS'
					),
					'packed_products' => $product
				);

				if (isset($product_data['width']) && isset($product_data['height']) && isset($product_data['length']) && !empty($product_data['width']) && !empty($product_data['height']) && !empty($product_data['length'])) {

					$group['Dimensions'] = array(
						'Length' => max(1, round($product_data['length'], 3)),
						'Width' => max(1, round($product_data['width'], 3)),
						'Height' => max(1, round($product_data['height'], 3)),
						'Units' => (isset($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM') ? 'CM' : 'IN'
					);
				} elseif (isset($parent_prod_data['width']) && isset($parent_prod_data['height']) && isset($parent_prod_data['length']) && !empty($parent_prod_data['width']) && !empty($parent_prod_data['height']) && !empty($parent_prod_data['length'])) {
					$group['Dimensions'] = array(
						'Length' => max(1, round($parent_prod_data['length'], 3)),
						'Width' => max(1, round($parent_prod_data['width'], 3)),
						'Height' => max(1, round($parent_prod_data['height'], 3)),
						'Units' => (isset($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM') ? 'CM' : 'IN'
					);
				}

				$group['packtype'] = 'BOX';

				$group['InsuredValue'] = $insurance_array;

				$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];

				for ($i = 0; $i < $chk_qty; $i++)
					$to_ship[] = $group;

				$group_id++;
			}

			return $to_ship;
		}
		private function a2z_get_zipcode_or_city($country, $city, $postcode)
		{
			$no_postcode_country = array(
				'AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
				'CL', 'CM', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
				'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
				'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
				'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW'
			);

			$postcode_city = !in_array($country, $no_postcode_country) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
			if (!empty($city)) {
				$postcode_city .= "<City>{$city}</City>";
			}
			return $postcode_city;
		}
		public function a2z_dpd_is_eu_country ($countrycode, $destinationcode) {
			$eu_countrycodes = array(
				'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE',
				'ES', 'FI', 'FR', 'GB', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV',
				'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
				'HR', 'GR'

			);
			return(in_array($countrycode, $eu_countrycodes) && in_array($destinationcode, $eu_countrycodes));
		}
		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields()
		{
			$this->form_fields = array('a2z_dpdshipping' => array('type' => 'a2z_dpdshipping'));
		}
		public function generate_a2z_dpdshipping_html()
		{
			$general_settings = get_option('a2z_dpd_main_settings');
			$general_settings = empty($general_settings) ? array() : $general_settings;
			if(!empty($general_settings)){
				wp_redirect(admin_url('options-general.php?page=hit-dpd-express-configuration'));
			}

			if(isset($_POST['configure_the_plugin'])){
				global $woocommerce;
				$countries_obj   = new WC_Countries();
				$countries   = $countries_obj->__get('countries');
				$default_country = $countries_obj->get_base_country();

				if(!isset($general_settings['a2z_dpdshipping_country'])){
					$general_settings['a2z_dpdshipping_country'] = $default_country;
					update_option('a2z_dpd_main_settings', $general_settings);

				}
				wp_redirect(admin_url('options-general.php?page=hit-dpd-express-configuration'));
			}
		?>
			<style>

			.card {
				background-color: #fff;
				border-radius: 5px;
				width: 800px;
				max-width: 800px;
				height: auto;
				text-align:center;
				margin: 10px auto 100px auto;
				box-shadow: 0px 1px 20px 1px hsla(213, 33%, 68%, .6);
			}

			.content {
				padding: 20px 20px;
			}


			h2 {
				text-transform: uppercase;
				color: #000;
				font-weight: bold;
			}


			.boton {
				text-align: center;
			}

			.boton button {
				font-size: 18px;
				border: none;
				outline: none;
				color: #166DB4;
				text-transform: capitalize;
				background-color: #fff;
				cursor: pointer;
				font-weight: bold;
			}

			button:hover {
				text-decoration: underline;
				text-decoration-color: #166DB4;
			}
						</style>
						<!-- Fuente Mulish -->


			<div class="card">
				<div class="content">
					<div class="logo">
					<img src="<?php echo plugin_dir_url(__FILE__); ?>views/dpd.png" style="width:150px;" alt="logo DELL" />
					</div>
					<h2><strong>Shipi + DPD Shipping</strong></h2>
					<p style="font-size: 14px;line-height: 27px;">
					<?php _e('Welcome to Shipi! You are at just one-step ahead to configure the DPD Shipping with Shipi.','a2z_dpdshipping') ?><br>
					<?php _e('We have lot of features that will take your e-commerce store to another level.','a2z_dpdshipping') ?><br><br>
					<?php _e('Shipi helps you to save time, reduce errors, and worry less when you automate your tedious, manual tasks. Shipi + our plugin can generate shipping labels, Commercial invoice, display real time rates, track orders, audit shipments, and supports both domestic & international DPD services.','a2z_dpdshipping') ?><br><br>
					<?php _e('Make your customers happier by reacting faster and handling their service requests in a timely manner, meaning higher store reviews and more revenue.','a2z_dpdshipping') ?><br>
					</p>

				</div>
				<div class="boton" style="padding-bottom:10px;">
				<button class="button-primary" name="configure_the_plugin" style="padding:8px;">Configure the plugin</button>
				</div>
				</div>
			<?php
			echo '<style>button.button-primary.woocommerce-save-button{display:none;}</style>';
		}
	}
}
