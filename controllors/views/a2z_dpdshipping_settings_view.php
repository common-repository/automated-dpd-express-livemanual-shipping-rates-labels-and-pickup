<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

wp_enqueue_script("jquery");

//$this->init_settings(); 
global $woocommerce, $wp_roles;
$error = $success =  '';
$_carriers = array();
$print_size = array('pdf'=>'PDF');
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
$duty_payment_type = array('EDAP' =>'Shipper','DAP' =>'Recipient');
$payment_country = array('S' =>'Shipper','R' =>'Recipient', 'C' => 'Custom');
$pickup_loc_type = array('B' =>'B (Business)','R' =>'R (Residence)','C' =>'C (Business/Residence)');
$pickup_del_type = array('DD' => 'DD (DoorToDoor)','DA' => 'DA (DoorToAirport)','DC' => 'DC (DoorToDoor non-complaint)');
$pickup_type = array('S' => 'S-SameDayPickup','A' => 'A-AdvancedPickup');
		$value = array();
		$value['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
		$value['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
		$value['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
		$value['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
		$value['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
		$value['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
		$value['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
		$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
		$value['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
		$value['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
		$value['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
		$value['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
		$value['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
		$value['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
		$value['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
		$value['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
		$value['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
		$value['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
		$value['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
		$value['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
		$value['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
		$value['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
		$value['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
		$value['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
		$value['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
		$value['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
		$value['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
		$value['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
		$value['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
		$value['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
		$value['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['CZ'] = array('region' => 'EU', 'currency' =>'CZK', 'weight' => 'KG_CM');
		$value['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
		$value['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
		$value['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
		$value['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
		$value['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
		$value['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
		$value['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
		$value['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
		$value['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GH'] = array('region' => 'AP', 'currency' =>'GHS', 'weight' => 'KG_CM');
		$value['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
		$value['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
		$value['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
		$value['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
		$value['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
		$value['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
		$value['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
		$value['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
		$value['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
		$value['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
		$value['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
		$value['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
		$value['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
		$value['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
		$value['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
		$value['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
		$value['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
		$value['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
		$value['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
		$value['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
		$value['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
		$value['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
		$value['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
		$value['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
		$value['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
		$value['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
		$value['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
		$value['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
		$value['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
		$value['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
		$value['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
		$value['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
		$value['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
		$value['LT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
		$value['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
		$value['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
		$value['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
		$value['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
		$value['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
		$value['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
		$value['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
		$value['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
		$value['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
		$value['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
		$value['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
		$value['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
		$value['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
		$value['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
		$value['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
		$value['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$value['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
		$value['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
		$value['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
		$value['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
		$value['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
		$value['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
		$value['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$value['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
		$value['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
		$value['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
		$value['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
		$value['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
		$value['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
		$value['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
		$value['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
		$value['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
		$value['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
		$value['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
		$value['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
		$value['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
		$value['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
		$value['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
		$value['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
		$value['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
		$value['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
		$value['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
		$value['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
		$value['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
		$value['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
		$value['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
		$value['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
		$value['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
		$value['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
		$value['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
		$value['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
		$value['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
		$value['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
		$value['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
		$value['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
		$value['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
		$value['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
		$value['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
		$value['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
		$value['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
		$value['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
		$value['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
		$value['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$value['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
		$value['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$value['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
		$value['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
		$value['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
		$value['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$currencys = $value;
	
	$packing_type = array("per_item" => "Pack Items Induviually", "weight_based" => "Weight Based Packing", "box" => "Box Packing");
	$boxes = include_once('data_helper/default_boxes.php');
	$package_type = array('BOX' => 'DPD Box','FLY' => 'Flyer','YP' => 'Your Pack');
	$weight_dim_unit = array("KG_CM" => "KG_CM", "LB_IN" => "LB_IN");
	$general_settings = get_option('a2z_dpd_main_settings');
	$general_settings = empty($general_settings) ? array() : $general_settings;
	// echo "<pre>";
	// print_r($general_settings);
	// die();

	function hitshipo_sanitize_array($arr_to_san = []){
		$sanitized_data = [];
		if (!empty($arr_to_san) && is_array($arr_to_san)) {
			foreach ($arr_to_san as $key => $value) {
				$sanitized_data[$key] = sanitize_text_field($value);
			}
		}
		return $sanitized_data;
	}
	if(isset($_POST['save']))
	{	
		// echo "<pre>";
		// print_r($_POST);
		// die();
		$a2z_dpdshipping_shipo_password = '';
		if(isset($_POST['a2z_dpdshipping_site_id'])){
			
			$boxes_id = isset($_POST['boxes_id']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_id'])) : array();
			$boxes_name = isset($_POST['boxes_name']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_name'])) : array();
			$boxes_length = isset($_POST['boxes_length']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_length'])) : array();
			$boxes_width = isset($_POST['boxes_width']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_width'])) : array();
			$boxes_height = isset($_POST['boxes_height']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_height'])) : array();
			$boxes_box_weight = isset($_POST['boxes_box_weight']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_box_weight'])) : array();
			$boxes_max_weight = isset($_POST['boxes_max_weight']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_max_weight'])) : array();
			$boxes_enabled = isset($_POST['boxes_enabled']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_enabled'])) : array();
			$boxes_pack_type = isset($_POST['boxes_pack_type']) ? hitshipo_sanitize_array(wp_unslash($_POST['boxes_pack_type'])) : array();

			$all_boxes = array();
			if (!empty($boxes_name)) {
				// if (isset($boxes_name['filter'])) { //Using sanatize_post() it's adding filter type. Have to unset otherwise it will display as box
				// 	unset($boxes_name['filter']);
				// }
				// if (isset($boxes_name['ID'])) {
				// 	unset($boxes_name['ID']);
				// }
				foreach ($boxes_name as $key => $value) {
					if (empty($value)) {
						continue;
					}
					$ind_box_id = $boxes_id[$key];
					$ind_box_name = empty($boxes_name[$key]) ? "New Box" : $boxes_name[$key];
					$ind_box_length = empty($boxes_length[$key]) ? 0 : $boxes_length[$key];
					$ind_boxes_width = empty($boxes_width[$key]) ? 0 : $boxes_width[$key];
					$ind_boxes_height = empty($boxes_height[$key]) ? 0 : $boxes_height[$key];
					$ind_boxes_box_weight = empty($boxes_box_weight[$key]) ? 0 : $boxes_box_weight[$key];
					$ind_boxes_max_weight = empty($boxes_max_weight[$key]) ? 0 : $boxes_max_weight[$key];
					$ind_box_enabled = isset($boxes_enabled[$key]) ? true : false;

					$all_boxes[$key] = array(
						'id' => $ind_box_id,
						'name' => $ind_box_name,
						'length' => $ind_box_length,
						'width' => $ind_boxes_width,
						'height' => $ind_boxes_height,
						'box_weight' => $ind_boxes_box_weight,
						'max_weight' => $ind_boxes_max_weight,
						'enabled' => $ind_box_enabled,
						'pack_type' => $boxes_pack_type[$key]
					);
				}
			}

			// echo '<pre>';print_r($all_boxes); die();
			$general_settings['a2z_dpdshipping_integration_key'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_integration_key']) ? $_POST['a2z_dpdshipping_integration_key'] : '');
			$general_settings['a2z_dpdshipping_site_id'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_site_id']) ? $_POST['a2z_dpdshipping_site_id'] : '');
			$general_settings['a2z_dpdshipping_site_pwd'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_site_pwd']) ? $_POST['a2z_dpdshipping_site_pwd'] : '');
			$general_settings['a2z_dpdshipping_acc_no'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_acc_no']) ? $_POST['a2z_dpdshipping_acc_no'] : '');
			$general_settings['a2z_dpdshipping_basic_tok'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_basic_tok']) ? $_POST['a2z_dpdshipping_basic_tok'] : '');
			$general_settings['a2z_dpdshipping_import_no'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_import_no']) ? $_POST['a2z_dpdshipping_import_no'] : '');

			$general_settings['a2z_dpdshipping_test'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_test']) ? 'yes' : 'no');
			// $general_settings['a2z_dpdshipping_rates'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_rates']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_etd_date'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_etd_date']) ? 'yes' : 'no');
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
			
			$general_settings['a2z_dpdshipping_account_rates'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_account_rates']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_excul_tax'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_excul_tax']) ? 'yes' : 'no');
			// $general_settings['a2z_dpdshipping_developer_rate'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_developer_rate']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_insure'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_insure']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_pay_con'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pay_con']) ? $_POST['a2z_dpdshipping_pay_con'] : '');
			$general_settings['a2z_dpdshipping_cus_pay_con'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_cus_pay_con']) ? $_POST['a2z_dpdshipping_cus_pay_con'] : '');
			
			$general_settings['a2z_dpdshipping_translation'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_translation']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_translation_key'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_translation_key']) ? $_POST['a2z_dpdshipping_translation_key'] : '');


			$general_settings['a2z_dpdshipping_uostatus'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_uostatus']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_trk_status_cus'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_trk_status_cus']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_email_alert'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_email_alert']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_aabill'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_aabill']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_cod'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_cod']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_sat'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_sat']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_ppt'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_ppt']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_label_automation'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_label_automation']) ? 'yes' :'no');

			$general_settings['a2z_dpdshipping_packing_type'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_packing_type']) ? $_POST['a2z_dpdshipping_packing_type'] : 'per_item');
			$general_settings['a2z_dpdshipping_max_weight'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_max_weight']) ? $_POST['a2z_dpdshipping_max_weight'] : '100');
			
			$general_settings['a2z_dpdshipping_label_email'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_label_email']) ? $_POST['a2z_dpdshipping_label_email'] : '');
			$general_settings['a2z_dpdshipping_ship_content'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_ship_content']) ? $_POST['a2z_dpdshipping_ship_content'] : 'No shipment content');
			$general_settings['a2z_dpdshipping_print_size'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_print_size']) ? $_POST['a2z_dpdshipping_print_size'] : '6X4_PDF');
			$general_settings['a2z_dpdshipping_duty_payment'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_duty_payment']) ? $_POST['a2z_dpdshipping_duty_payment'] : 'none');
			$general_settings['a2z_dpdshipping_weight_unit'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_weight_unit']) ? $_POST['a2z_dpdshipping_weight_unit'] : 'KG_CM');
			$general_settings['a2z_dpdshipping_con_rate'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_con_rate']) ? $_POST['a2z_dpdshipping_con_rate'] : '');
			$general_settings['a2z_dpdshipping_auto_con_rate'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_auto_con_rate']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_rule_weight_from'] = isset($_POST['a2z_dpdshipping_rule_weight_from']) ? hitshipo_sanitize_array(wp_unslash($_POST['a2z_dpdshipping_rule_weight_from'])) : '';
			$general_settings['a2z_dpdshipping_rule_weight_to'] = isset($_POST['a2z_dpdshipping_rule_weight_to']) ? hitshipo_sanitize_array(wp_unslash($_POST['a2z_dpdshipping_rule_weight_to'])) : '';
			$general_settings['a2z_dpdshipping_rule_shipping_cost'] = isset($_POST['a2z_dpdshipping_rule_shipping_cost']) ? hitshipo_sanitize_array(wp_unslash($_POST['a2z_dpdshipping_rule_shipping_cost'])) : '';
			$general_settings['a2z_dpdshipping_default_ship_cost'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_default_ship_cost']) ? $_POST['a2z_dpdshipping_default_ship_cost'] : '');
			$general_settings['a2z_dpdshipping_default_box44'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_default_box44']) ? $_POST['a2z_dpdshipping_default_box44'] : '');


			$general_settings['a2z_dpdshipping_pickup_automation'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_automation']) ? 'yes' :'no');
			$general_settings['a2z_dpdshipping_pickup_loc_type'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_loc_type']) ? $_POST['a2z_dpdshipping_pickup_loc_type'] : 'none');
			$general_settings['a2z_dpdshipping_pickup_pac_loc'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_pac_loc']) ? $_POST['a2z_dpdshipping_pickup_pac_loc'] : '');
			$general_settings['a2z_dpdshipping_pickup_per_name'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_per_name']) ? $_POST['a2z_dpdshipping_pickup_per_name'] : '');
			$general_settings['a2z_dpdshipping_pickup_per_contact_no'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_per_contact_no']) ? $_POST['a2z_dpdshipping_pickup_per_contact_no'] : '');
			$general_settings['a2z_dpdshipping_pickup_door_to'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_door_to']) ? $_POST['a2z_dpdshipping_pickup_door_to'] : 'none');
			$general_settings['a2z_dpdshipping_pickup_type'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_type']) ? $_POST['a2z_dpdshipping_pickup_type'] : 'none');
			$general_settings['a2z_dpdshipping_pickup_date'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_date']) ? $_POST['a2z_dpdshipping_pickup_date'] : '');
			$general_settings['a2z_dpdshipping_pickup_open_time'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_open_time']) ? $_POST['a2z_dpdshipping_pickup_open_time'] : '');
			$general_settings['a2z_dpdshipping_pickup_close_time'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_pickup_close_time']) ? $_POST['a2z_dpdshipping_pickup_close_time'] : '');

			// Multi Vendor Settings

			$general_settings['a2z_dpdshipping_v_enable'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_v_enable']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_v_rates'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_v_rates']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_v_labels'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_v_labels']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_v_roles'] = isset($_POST['a2z_dpdshipping_v_roles']) ? hitshipo_sanitize_array(wp_unslash($_POST['a2z_dpdshipping_v_roles'])) : array();
			$general_settings['a2z_dpdshipping_v_email'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_v_email']) ? 'yes' : 'no');
			
			$general_settings['a2z_dpdshipping_track_audit'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_track_audit']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_daily_report'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_daily_report']) ? 'yes' : 'no');
			$general_settings['a2z_dpdshipping_monthly_report'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_monthly_report']) ? 'yes' : 'no');

			$general_settings['a2z_dpdshipping_shipo_signup'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_shipo_signup']) ? $_POST['a2z_dpdshipping_shipo_signup'] : '');
			$a2z_dpdshipping_shipo_password = sanitize_text_field(isset($_POST['a2z_dpdshipping_shipo_password']) ? $_POST['a2z_dpdshipping_shipo_password'] : '');
// echo '<pre>';print_r($general_settings);die();
			// boxes
			$general_settings['a2z_dpdshipping_boxes'] = !empty($all_boxes) ? $all_boxes : array();
			update_option('a2z_dpd_main_settings', $general_settings);
			$success = 'Settings Saved Successfully.';
		}
		if ((!isset($general_settings['a2z_dpdshipping_integration_key']) || empty($general_settings['a2z_dpdshipping_integration_key'])) && isset($_POST['shipo_link_type']) && $_POST['shipo_link_type'] == "WITH") {
			$general_settings['a2z_dpdshipping_integration_key'] = sanitize_text_field(isset($_POST['a2z_dpdshipping_integration_key']) ? $_POST['a2z_dpdshipping_integration_key'] : '');
			update_option('a2z_dpd_main_settings', $general_settings);
			update_option('a2z_dpd_express_working_status', 'start_working');
			$success = 'Site Linked Successfully.<br><br> It\'s great to have you here.';
		}

		if(!isset($general_settings['a2z_dpdshipping_integration_key']) || empty($general_settings['a2z_dpdshipping_integration_key'])){
			$random_nonce = wp_generate_password(16, false);
			set_transient( 'hitshipo_dpd_express_nonce_temp', $random_nonce, HOUR_IN_SECONDS );
			$a2z_dpdshipping_shipo_password = base64_encode($a2z_dpdshipping_shipo_password);
			
			$link_hitshipo_request = json_encode(array('site_url' => site_url(),
				'site_name' => get_bloginfo('name'),
				'email_address' => $general_settings['a2z_dpdshipping_shipo_signup'],
				'password' => $a2z_dpdshipping_shipo_password,
				'nonce' => $random_nonce,
				'audit' => $general_settings['a2z_dpdshipping_track_audit'],
				'd_report' => $general_settings['a2z_dpdshipping_daily_report'],
				'm_report' => $general_settings['a2z_dpdshipping_monthly_report'],
				'pulgin' => 'DPD',
				'platfrom' => 'Woocommerce',
			));
			
			$link_site_url = "https://app.myshipi.com/api/link-site.php";
			$link_site_response = wp_remote_post( $link_site_url , array(
					'method'      => 'POST',
					'timeout'     => 45,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking'    => true,
					'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
					'body'        => $link_hitshipo_request,
					'sslverify'   => FALSE
					)
				);
				
				$link_site_response = ( is_array($link_site_response) && isset($link_site_response['body'])) ? json_decode($link_site_response['body'], true) : array();
				if($link_site_response){
					if($link_site_response['status'] != 'error'){
						$general_settings['a2z_dpdshipping_integration_key'] = sanitize_text_field($link_site_response['integration_key']);
						update_option('a2z_dpd_main_settings', $general_settings);
						update_option('a2z_dpd_express_working_status', 'start_working');
						$success = 'Site Linked Successfully.<br><br> It\'s great to have you here. ' . (isset($link_site_response['trail']) ? 'Your 60days Trail period is started. To know about this more, please check your inbox.' : '' ) . '<br><br><button class="button" type="submit">Back to Settings</button>';
					}else{
						$error = '<p style="color:red;">'. $link_site_response['message'] .'</p>';
						$success = '';
					}
				}else{
					$error = '<p style="color:red;">Failed to connect with Shipi</p>';
					$success = '';
				}
				
		
		}
		
	}
		$general_settings['a2z_dpdshipping_currency'] = isset($value[(isset($general_settings['a2z_dpdshipping_country']) ? $general_settings['a2z_dpdshipping_country'] : 'A2Z')]) ? $value[$general_settings['a2z_dpdshipping_country']]['currency'] : '';
		$general_settings['a2z_dpdshipping_woo_currency'] = get_option('woocommerce_currency');


?>
<style>
.notice{display:none;}
#multistepsform {
  width: 80%;
  margin: 50px auto;
  text-align: center;
  position: relative;
}
#multistepsform fieldset {
  background: white;
  text-align:left;
  border: 0 none;
  border-radius: 5px;
  box-shadow: 0 0 15px 1px rgba(0, 0, 0, 0.4);
  padding: 20px 30px;
  box-sizing: border-box;
  position: relative;
}
#multistepsform fieldset:not(:first-of-type) {
  display: none;
}
#multistepsform input[type=text], #multistepsform input[type=password], #multistepsform input[type=number], #multistepsform input[type=email], 
#multistepsform textarea {
  padding: 5px;
  width: 95%;
}
#multistepsform input:focus,
#multistepsform textarea:focus {
  border-color: #679b9b;
  outline: none;
  color: #637373;
}
#multistepsform .action-button {
  width: 100px;
  background: #d30b2a;
  font-weight: bold;
  color: #fff;
  transition: 150ms;
  border: 0 none;
  float:right;
  border-radius: 1px;
  cursor: pointer;
  padding: 10px 5px;
  margin: 10px 5px;
}
.button-rules {
  width: 100px;
  background: #3f3f42;
  font-weight: bold;
  color: #fff;
  transition: 150ms;
  border: 0 none;
  float:right;
  border-radius: 30px;
  cursor: pointer;
  padding: 5px 5px;
  margin: 0px 5px;
}
#multistepsform .action-button:hover,
#multistepsform .action-button:focus {
  box-shadow: 0 0 0 2px #f08a5d, 0 0 0 3px #ff976;
  color: #fff;
}
#multistepsform .fs-title {
  font-size: 15px;
  text-transform: uppercase;
  color: #2c3e50;
  margin-bottom: 10px;
}
#multistepsform .fs-subtitle {
  font-weight: normal;
  font-size: 13px;
  color: #666;
  margin-bottom: 20px;
}
#multistepsform #progressbar {
  margin-bottom: 30px;
  overflow: hidden;
  counter-reset: step;
}
#multistepsform #progressbar li {
  list-style-type: none;
  color: #3f3f42;
  text-transform: uppercase;
  font-size: 9px;
  width: 16.5%;
  float: left;
  position: relative;
}
#multistepsform #progressbar li:before {
  content: counter(step);
  counter-increment: step;
  width: 20px;
  line-height: 20px;
  display: block;
  font-size: 10px;
  color: #fff;
  background: #3f3f42;
  border-radius: 3px;
  margin: 0 auto 5px auto;
}
#multistepsform #progressbar li:after {
  content: "";
  width: 100%;
  height: 2px;
  background: #3f3f42;
  position: absolute;
  left: -50%;
  top: 9px;
  z-index: -1;
}
#multistepsform #progressbar li:first-child:after {
  content: none;
}
#multistepsform #progressbar li.active {
  color: #d30b2a;
}
#multistepsform #progressbar li.active:before, #multistepsform #progressbar li.active:after {
  background: #d30b2a;
  color: white;
}
.setting{
	cursor: pointer;
	border: 0px;
	padding: 10px 5px;
  	margin: 10px 5px;
 	background-color: #d30b2a!important;
	font-weight: bold; 
	color:#ffffff!important;
	border-radius: 3px;
}
		</style>
<div style="text-align:center;margin-top:20px;"><img src="<?php echo plugin_dir_url(__FILE__); ?>dpd.png" style="width:150px;"></div>

<?php if($success != ''){
	echo '<form id="multistepsform" method="post"><fieldset>
    <center><h2 class="fs-title" style="line-height:27px;">'. $success .'</h2>
	<input type="button" class="setting" value="GO TO SETTINGS" onClick="window.location.href=window.location.href">
	</center></form>';
}else{
	?>
<!-- multistep form -->
<form id="multistepsform" method="post">
	
  <!-- progressbar -->
  <ul id="progressbar">
    <li class="active">Integration</li>
    <li>Setup</li>
    <li>Packing</li>
    <!-- <li>Rates</li> -->
    <li>Shipping Label</li>
    <li>Shipi</li>

  </ul>
  <?php if($error == ''){

  ?>
  <!-- fieldsets -->
 <fieldset>
    <center><h2 class="fs-title">DPD Account Information</h2>
	
	<table style="padding-left:10px;padding-right:10px;">
	<tr>

	<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_test" <?php echo (isset($general_settings['a2z_dpdshipping_test']) && $general_settings['a2z_dpdshipping_test'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Test Mode.</small></span></td>
	<!-- <td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_rates" <?php echo (isset($general_settings['a2z_dpdshipping_rates']) && $general_settings['a2z_dpdshipping_rates'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Live Shipping Rates.</small></span></td> -->
	<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_label_automation" <?php echo (isset($general_settings['a2z_dpdshipping_label_automation']) && $general_settings['a2z_dpdshipping_label_automation'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Create Label automatically.</small></span></td>
	<!-- <td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_developer_rate" <?php echo (isset($general_settings['a2z_dpdshipping_developer_rate']) && $general_settings['a2z_dpdshipping_developer_rate'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Debug Mode.</small></span></td> -->
	</tr>
	</table>
	</center>
	<table style="width:100%;">
	<tr><td colspan="2" style="padding:10px;"><hr>
	<center><a href="https://esolutions.dpd.com">Register here</a> to get the Delis ID and password</center>
</td></tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Delis ID / User','a2z_dpdshipping') ?>
				<input type="text" class="input-text regular-input" name="a2z_dpdshipping_site_id" id="a2z_dpdshipping_site_id" value="<?php echo (isset($general_settings['a2z_dpdshipping_site_id'])) ? $general_settings['a2z_dpdshipping_site_id'] : ''; ?>">
				
			</td>
			<td style="padding:10px;">
			<?php _e('Password','a2z_dpdshipping') ?><font style="color:red;">*</font>
			<input type="text" name="a2z_dpdshipping_site_pwd" id="a2z_dpdshipping_site_pwd" value="<?php echo (isset($general_settings['a2z_dpdshipping_site_pwd'])) ? $general_settings['a2z_dpdshipping_site_pwd'] : ''; ?>">
			
			</td>

		</tr>
		<tr>
				<td style="padding:10px;">
				<?php _e('Customer Number','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_acc_no" id="a2z_dpdshipping_acc_no" value="<?php echo (isset($general_settings['a2z_dpdshipping_acc_no'])) ? $general_settings['a2z_dpdshipping_acc_no'] : ''; ?>">	
			</td>
			<!-- <td style="padding:10px;">
				<?php _e('DPD Weight Unit','a2z_dpdshipping') ?><br>
					<select name="a2z_dpdshipping_weight_unit" class="wc-enhanced-select" style="width:95%;padding:5px;">
						<option value="LB_IN" <?php echo (isset($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'LB_IN') ? 'Selected="true"' : ''; ?>> LB & IN </option>
						<option value="KG_CM" <?php echo (isset($general_settings['a2z_dpdshipping_weight_unit']) && $general_settings['a2z_dpdshipping_weight_unit'] == 'KG_CM') ? 'Selected="true"' : ''; ?>> KG & CM </option>
					</select>
				</td> -->
			
			
			<td style="padding:10px;">
			<?php _e('DPD Weight Unit','a2z_dpdshipping') ?><br>
				<select name="a2z_dpdshipping_weight_unit" class="wc-enhanced-select" style="width:95%;padding:5px;">
					<?php foreach($weight_dim_unit as $key => $value)
					{
						if(isset($general_settings['a2z_dpdshipping_weight_unit']) && ($general_settings['a2z_dpdshipping_weight_unit'] == $key))
						{
							echo "<option value=".$key." selected='true'>".$value."</option>";
						}
						else
						{
							echo "<option value=".$key.">".$value."</option>";
						}
					} ?>
				</select>
			</td>
		</tr>
		<tr>
			<td style="padding:10px;">
				<?php _e('Basic Token','a2z_dpdshipping') ?>
				<input type="text" name="a2z_dpdshipping_basic_tok" id="a2z_dpdshipping_basic_tok" value="<?php echo (isset($general_settings['a2z_dpdshipping_basic_tok'])) ? $general_settings['a2z_dpdshipping_basic_tok'] : ''; ?>">	
			</td>
			<td style="padding:10px;"></td>
		</tr>
			<tr>
				
				<td style="padding:10px;">
					<?php //_e('Change DPD currency','a2z_dpdshipping') ?>
					<select name="a2z_dpdshipping_currency" style="width:95%;padding:5px; display:none;">
							
						<?php foreach($currencys as  $currency)
						{
							if(isset($general_settings['a2z_dpdshipping_currency']) && ($general_settings['a2z_dpdshipping_currency'] == $currency['currency']))
							{
								echo "<option value=".$currency['currency']." selected='true'>".$currency['currency']."</option>";
							}
							else
							{
								echo "<option value=".$currency['currency'].">".$currency['currency']."</option>";
							}
						}

						if (!isset($general_settings['a2z_dpdshipping_currency']) || ($general_settings['a2z_dpdshipping_currency'] != "NMP")) {
								echo "<option value=NMP>NMP</option>";
						}elseif (isset($general_settings['a2z_dpdshipping_currency']) && ($general_settings['a2z_dpdshipping_currency'] == "NMP")) {
								echo "<option value=NMP selected='true'>NMP</option>";
						} ?>
					</select>
				</td>
				
			</tr>
			<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
			<?php if ($general_settings['a2z_dpdshipping_woo_currency'] != $general_settings['a2z_dpdshipping_currency'] ){
				?>
					<tr><td colspan="2" style="text-align:center;"><small><?php _e(' Your Website Currency is ','a2z_dpdshipping') ?> <b><?php echo $general_settings['a2z_dpdshipping_woo_currency'];?></b> and your DPD currency is <b><?php echo (isset($general_settings['a2z_dpdshipping_currency'])) ? $general_settings['a2z_dpdshipping_currency'] : '(Choose country)'; ?></b>. <?php echo ($general_settings['a2z_dpdshipping_woo_currency'] != $general_settings['a2z_dpdshipping_currency'] ) ? 'So you have to consider the converstion rate.' : '' ?></small>
						</td>
					</tr>
					
					<tr><td colspan="2" style="text-align:center;">
					
						
					</td>
					</tr>
					
					<tr>
						<td style="padding:10px;text-align:center;" colspan="2" class="con_rate" >
							<?php _e('Exchange Rate','a2z_dpdshipping') ?><font style="color:red;">*</font> <?php echo "( ".$general_settings['a2z_dpdshipping_woo_currency']."->".$general_settings['a2z_dpdshipping_currency']." )"; ?>
							<br><input type="text" style="width:240px;" name="a2z_dpdshipping_con_rate" value="<?php echo (isset($general_settings['a2z_dpdshipping_con_rate'])) ? $general_settings['a2z_dpdshipping_con_rate'] : ''; ?>">
							<br><small style="color:gray;"><?php _e('Enter conversion rate.','a2z_dpdshipping') ?></small>
						</td>
					</tr>
					<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
				<?php
			}
			?>
		
		
		
	</table>
	<?php if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] !=''){
		echo '<input type="submit" name="save" class="action-button save_change" style="width:auto;float:left;" value="Save Changes" />';
	}

	?>
    <input type="button" name="next" class="next action-button" value="Next" />
  </fieldset>

  <fieldset>
  	<center><h2 class="fs-title">Shipping Address Information</h2></center>
	
	<table style="width:100%;">
		<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Shipper Name','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_shipper_name" id="a2z_dpdshipping_shipper_name" value="<?php echo (isset($general_settings['a2z_dpdshipping_shipper_name'])) ? $general_settings['a2z_dpdshipping_shipper_name'] : ''; ?>">
			</td>
			<td style="padding:10px;">
			<?php _e('Company Name','a2z_dpdshipping') ?><font style="color:red;">*</font>
			<input type="text" name="a2z_dpdshipping_company" id="a2z_dpdshipping_company" value="<?php echo (isset($general_settings['a2z_dpdshipping_company'])) ? $general_settings['a2z_dpdshipping_company'] : ''; ?>">
			</td>
		</tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Shipper Mobile / Contact Number','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_mob_num" id="a2z_dpdshipping_mob_num" value="<?php echo (isset($general_settings['a2z_dpdshipping_mob_num'])) ? $general_settings['a2z_dpdshipping_mob_num'] : ''; ?>">
			</td>
			<td style="padding:10px;">
			<?php _e('Email Address of the Shipper','a2z_dpdshipping') ?><font style="color:red;">*</font>
			<input type="text" name="a2z_dpdshipping_email" id="a2z_dpdshipping_email" value="<?php echo (isset($general_settings['a2z_dpdshipping_email'])) ? $general_settings['a2z_dpdshipping_email'] : ''; ?>">
			</td>
		</tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Address Line 1','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_address1" id="a2z_dpdshipping_address1" value="<?php echo (isset($general_settings['a2z_dpdshipping_address1'])) ? $general_settings['a2z_dpdshipping_address1'] : ''; ?>">
			</td>
			<td style="padding:10px;">
			<?php _e('Address Line 2','a2z_dpdshipping') ?>
			<input type="text" name="a2z_dpdshipping_address2" value="<?php echo (isset($general_settings['a2z_dpdshipping_address2'])) ? $general_settings['a2z_dpdshipping_address2'] : ''; ?>">
			</td>
		</tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('City of the Shipper from address','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_city" id="a2z_dpdshipping_city" value="<?php echo (isset($general_settings['a2z_dpdshipping_city'])) ? $general_settings['a2z_dpdshipping_city'] : ''; ?>">
			</td>
			<td style="padding:10px;">
			<?php _e('State (Two digit ISO code accepted.)','a2z_dpdshipping') ?><font style="color:red;">*</font>
			<input type="text" name="a2z_dpdshipping_state" id="a2z_dpdshipping_state" value="<?php echo (isset($general_settings['a2z_dpdshipping_state'])) ? $general_settings['a2z_dpdshipping_state'] : ''; ?>">
			</td>
		</tr>
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Postal/Zip Code','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_zip" id="a2z_dpdshipping_zip" value="<?php echo (isset($general_settings['a2z_dpdshipping_zip'])) ? $general_settings['a2z_dpdshipping_zip'] : ''; ?>">
			</td>
			<td style="padding:10px;">
			<?php _e('Country of the Shipper from Address','a2z_dpdshipping') ?><font style="color:red;">*</font>
			<select name="a2z_dpdshipping_country" class="wc-enhanced-select" style="width:95%;padding:5px;">
					<?php foreach($countires as $key => $value)
					{
						if(isset($general_settings['a2z_dpdshipping_country']) && ($general_settings['a2z_dpdshipping_country'] == $key))
						{
							echo "<option value=".$key." selected='true'>".$value."</option>";
						}
						else
						{
							echo "<option value=".$key.">".$value."</option>";
						}
					} ?>
				</select>
			</td>
		</tr>
		<!-- <tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('GSTIN/VAT No','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_gstin" value="<?php echo (isset($general_settings['a2z_dpdshipping_gstin'])) ? $general_settings['a2z_dpdshipping_gstin'] : ''; ?>">
			</td>
			
		</tr> -->
		<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
	</table>
	<center><h2 class="fs-title">Are you gonna use Multi Vendor?</h2></center><br>
	<table style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_v_enable" <?php echo (isset($general_settings['a2z_dpdshipping_v_enable']) && $general_settings['a2z_dpdshipping_v_enable'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Use Multi-Vendor.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_v_rates" <?php echo (isset($general_settings['a2z_dpdshipping_v_rates']) && $general_settings['a2z_dpdshipping_v_rates'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Get rates from vendor address.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_v_labels" <?php echo (isset($general_settings['a2z_dpdshipping_v_labels']) && $general_settings['a2z_dpdshipping_v_labels'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Create Label from vendor address.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_v_email" <?php echo (isset($general_settings['a2z_dpdshipping_v_email']) && $general_settings['a2z_dpdshipping_v_email'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Email the shipping labels to vendors.</small></span></td>
		</table>
	<table style="width:100%">
						
						
						<tr>
							<td style=" width: 50%;padding:10px;text-align:center;">
								<?php _e('Vendor role','a2z_dpdshipping') ?></h4><br>
								<select name="a2z_dpdshipping_v_roles[]" style="padding:5px;width:240px;">

									<?php foreach (get_editable_roles() as $role_name => $role_info){
										if(isset($general_settings['a2z_dpdshipping_v_roles']) && in_array($role_name, $general_settings['a2z_dpdshipping_v_roles'])){
											echo "<option value=".$role_name." selected='true'>".$role_info['name']."</option>";
										}else{
											echo "<option value=".$role_name.">".$role_info['name']."</option>";	
										}
										
									}
								?>

								</select><br>
								<small style="color:gray;"> To this role users edit page, you can find the new<br>fields to enter the ship from address.</small>
								
							</td>
						</tr>
						<tr><td style="padding:10px;"><hr></td></tr>
					</table>
	<?php if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] !=''){
		echo '<input type="submit" name="save" class="action-button save_change" style="width:auto;float:left;" value="Save Changes" />';
	}

	?>
		<input type="button" name="next" class="next action-button" value="Next" />
		<input type="button" name="previous" class="previous action-button" value="Previous" />

  </fieldset>

<fieldset>
	<center><h2 class="fs-title">Choose Packing ALGORITHM</h2></center><br/>
	<table style="width:100%">
	
						<tr>
							<td style=" width: 50%;padding:10px;">
								<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Integration key Created from HIT Shipo','a2z_dpdshipping') ?>"></span>	<?php _e('Select Package Type','a2z_dpdshipping') ?><font style="color:red;">*</font></h4>
							</td>
							<td style="padding:10px;">
								<select name="a2z_dpdshipping_packing_type" style="padding:5px; width:95%;" id = "a2z_dpdshipping_packing_type" class="wc-enhanced-select" style="width:153px;" onchange="changepacktype(this)">
									<?php foreach($packing_type as $key => $value)
									{
										if(isset($general_settings['a2z_dpdshipping_packing_type']) && ($general_settings['a2z_dpdshipping_packing_type'] == $key))
										{
											echo "<option value=".$key." selected='true'>".$value."</option>";
										}
										else
										{
											echo "<option value=".$key.">".$value."</option>";
										}
									} ?>
								</select>
							</td>
						</tr>
						<tr style=" display:none;" id="weight_based">
							<td style=" width: 50%;padding:10px;">
								<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('To email address, the shipping label, Commercial invoice will sent.') ?>"></span>	<?php _e('What is the Maximum weight to one package?','a2z_dpdshipping') ?><font style="color:red;">*</font></h4>
							</td>
							<td style="padding:10px;">
								<input type="number" name="a2z_dpdshipping_max_weight" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_max_weight'])) ? $general_settings['a2z_dpdshipping_max_weight'] : ''; ?>">
							</td>
						</tr>
					</table>
					<div id="box_pack" style="width: 100%;">
					<h4 style="font-size: 16px;">Box packing configuration</h4><p>( Saved boxes are used when package type is "BOX". )</p>
					<table id="box_pack_t">
						<tr>
							<th style="padding:3px;"></th>
							<th style="padding:3px;"><?php _e('Name','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Length','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Width','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Height','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Box Weight','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Max Weight','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Enabled','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
							<th style="padding:3px;"><?php _e('Package Type','a2z_dpdshipping') ?><font style="color:red;">*</font></th>
						</tr>
						<tbody id="box_pack_tbody">
							<?php

							$boxes = ( isset($general_settings['a2z_dpdshipping_boxes']) ) ? $general_settings['a2z_dpdshipping_boxes'] : $boxes;
								if (!empty($boxes)) {//echo '<pre>';print_r($general_settings['a2z_dpdshipping_boxes']);die();
									foreach ($boxes as $key => $box) {
										echo '<tr>
												<td class="check-column" style="padding:3px;"><input type="checkbox" /></td>
												<input type="hidden" size="1" name="boxes_id['.$key.']" value="'.$box["id"].'"/>
												<td style="padding:3px;"><input type="text" size="25" name="boxes_name['.$key.']" value="'.$box["name"].'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_length['.$key.']" value="'.$box["length"].'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_width['.$key.']" value="'.$box["width"].'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_height['.$key.']" value="'.$box["height"].'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_box_weight['.$key.']" value="'.$box["box_weight"].'" /></td>
												<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_max_weight['.$key.']" value="'.$box["max_weight"].'" /></td>';
												if ($box['enabled'] == true) {
													echo '<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled['.$key.']" checked/></center></td>';
												}else {
													echo '<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled['.$key.']" /></center></td>';
												}
												
										echo '<td style="padding:3px;"><select name="boxes_pack_type['.$key.']">';
											foreach ($package_type as $k => $v) {
												$selected = ($k==$box['pack_type']) ? "selected='true'" : '';
												echo '<option value="'.$k.'" ' .$selected. '>'.$v.'</option>';
											}
										echo '</select></td>
											</tr>';
									}
								}
							?>
							<tfoot>
							<tr>
								<th colspan="6">
									<a href="#" class="button button-secondary" id="add_box"><?php _e('Add Box','a2z_dpdshipping') ?></a>
									<a href="#" class="button button-secondary" id="remove_box"><?php _e('Remove selected box(es)','a2z_dpdshipping') ?></a>
								</th>
							</tr>
						</tfoot>
						</tbody>
					</table>
				</div>
	<?php if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] !=''){
		echo '<input type="submit" name="save" class="action-button save_change" style="width:auto;float:left;" value="Save Changes" />';
	}

	?>
	<input type="button" name="next" class="next action-button" value="Next" />
	<input type="button" name="previous" class="previous action-button" value="Previous" />

</fieldset>

  <!-- <fieldset>
  <center><h2 class="fs-title">Rates</h2><br/>
  	<table style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_etd_date" <?php echo (isset($general_settings['a2z_dpdshipping_etd_date']) && $general_settings['a2z_dpdshipping_etd_date'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Show delivery date.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_account_rates" <?php echo (isset($general_settings['a2z_dpdshipping_account_rates']) && $general_settings['a2z_dpdshipping_account_rates'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Fetch DPD account rates.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_excul_tax" <?php echo (isset($general_settings['a2z_dpdshipping_excul_tax']) && $general_settings['a2z_dpdshipping_excul_tax'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Exclude tax.</small></span></td>
		</table></center>

  	<table style="width:100%">
					
  		<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
			<tr>
				<td style="padding:10px;" >
				<?php _e('Payment Country','a2z_dpdshipping') ?><font style="color:red;">*</font><br>
					<select name="a2z_dpdshipping_pay_con" style="padding:5px;width:95%" id="a2z_dpdshipping_pay_con" class="wc-enhanced-select" style="width:100px;" onchange="changepaycon(this)">
						<?php foreach($payment_country as $key => $value)
						{
							if(isset($general_settings['a2z_dpdshipping_pay_con']) && ($general_settings['a2z_dpdshipping_pay_con'] == $key))
							{
								echo "<option value=".$key." selected='true'>".$value."</option>";
							}
							else
							{
								echo "<option value=".$key.">".$value."</option>";
							}
						} ?>
					</select>
				</td>
				<td style=" width: 50%;padding:10px;">
				<input type="checkbox" name="a2z_dpdshipping_translation" id="a2z_dpdshipping_translation" <?php echo (isset($general_settings['a2z_dpdshipping_translation']) && $general_settings['a2z_dpdshipping_translation'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Address translation any language to english.','a2z_dpdshipping') ?><br>
					<small style="color:gray">Use this if you have your own language to checkout.</small>
				</td>
				
			</tr>
			
			<tr>
				
				<td style="padding:10px;" >
				<div id="cus_pay_con" style="width:100%;">
				<?php _e('Choose Payment country','a2z_dpdshipping') ?><font style="color:red;">*</font><br>
				<select name="a2z_dpdshipping_cus_pay_con" style="padding:5px;width:95%;">
						<?php foreach($countires as $key => $value)
						{
							if(isset($general_settings['a2z_dpdshipping_cus_pay_con']) && ($general_settings['a2z_dpdshipping_cus_pay_con'] == $key))
							{
								echo "<option value=".$key." selected='true'>".$value."</option>";
							}
							else
							{
								echo "<option value=".$key.">".$value."</option>";
							}
						} ?>
					</select>
					</div>
				</td>
				<td style=" width: 50%;padding:10px;" >
					<div id="translation_key">
					<?php _e('Google\'s Cloud API Key','a2z_dpdshipping') ?><br>
					<input type="text" name="a2z_dpdshipping_translation_key" value="<?php echo (isset($general_settings['a2z_dpdshipping_translation_key'])) ? $general_settings['a2z_dpdshipping_translation_key'] : ''; ?>">
					</div>
				</td>
			</tr>
			<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
			<tr><td colspan="2" style="padding:10px;"><center><h2 class="fs-title">Do you wants to exclude countries?</h2></center></td></tr>
				
			<tr>
				<td colspan="2" style="text-align:center;padding:10px;">
					<?php _e('Exclude Countries','a2z_dpdshipping') ?><br>
					<select name="a2z_dpdshipping_exclude_countries[]" multiple="true" class="wc-enhanced-select" style="padding:5px;width:600px;">

					<?php
					$general_settings['a2z_dpdshipping_exclude_countries'] = empty($general_settings['a2z_dpdshipping_exclude_countries'])? array() : $general_settings['a2z_dpdshipping_exclude_countries'];
					foreach ($countires as $key => $county){
						if(in_array($key,$general_settings['a2z_dpdshipping_exclude_countries'])){
							echo "<option value=".$key." selected='true'>".$county."</option>";
						}else{
							echo "<option value=".$key.">".$county."</option>";	
						}
						
					}
					?>

					</select>
				</td>
				<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
				
			</tr>
			
		</table>
				<center><h2 class="fs-title">Shipping Services & Price adjustment</h2></center>
				<table style="width:100%;">
				
					<tr>
						<td>
							<h3 style="font-size: 1.10em;"><?php _e('Carries','a2z_dpdshipping') ?></h3>
						</td>
						<td>
							<h3 style="font-size: 1.10em;"><?php _e('Alternate Name for Carrier','a2z_dpdshipping') ?></h3>
						</td>
						<td>
							<h3 style="font-size: 1.10em;"><?php _e('Price adjustment','a2z_dpdshipping') ?></h3>
						</td>
						<td>
							<h3 style="font-size: 1.10em;"><?php _e('Price adjustment (%)','a2z_dpdshipping') ?></h3>
						</td>
					</tr>
							<?php foreach($_carriers as $key => $value)
							{
								if($value == 'GLOBALMAIL BUSINESS'){
									echo ' <tr><td colspan="4" style="padding:10px;"><hr></td></tr><tr ><td colspan="4" style="text-align:center;"><div style="padding:10px;border:1px solid gray;"><b><u>INTERNATIONAL SERVICES</u><br>
									This all are the services provided by DPD to ship domestic.<br>
									<span style="background:#d30b2a;color:#3f3f42;">Some of the carrier names are repeated </span>. Please don\'t get confuse on that. <span style="background:#d30b2a;color:#3f3f42;">Select both</span>.This our <span style="background:#d30b2a;color:#3f3f42;">plugin handle this based on ship to country</span>.
								</b></div></td></tr> <tr><td colspan="4" style="padding:10px;"><hr></td></tr>';
								}else if($value == "DOMESTIC EXPRESS"){
									echo ' <tr><td colspan="4" style="padding:10px;"><hr></td></tr><tr ><td colspan="4" style="text-align:center;"><div style="padding:10px;border:1px solid gray;"><b><u>DOMESTIC SERVICES</u><br>
										This all are the services provided by DPD to ship international.<br>
										<span style="background:#d30b2a;color:#3f3f42;">Some of the carrier names are repeated </span>. Please don\'t get confuse on that. <span style="background:#d30b2a;color:#3f3f42;">Select both</span>.This our <span style="background:#d30b2a;color:#3f3f42;">plugin handle this based on ship to country</span>.
									</b></div>
									</td></tr> <tr><td colspan="4" style="padding:10px;"><hr></td></tr>';
								}else if ($value == 'JUMBO BOX'){
									echo ' <tr><td colspan="4" style="padding:10px;"><hr></td></tr><tr ><td colspan="4" style="text-align:center;"><b><u>OTHER SPACIAL SERVICES</u><br>
										
									</b>
									</td></tr> <tr><td colspan="4" style="padding:10px;"><hr></td></tr>';
								}
								
								echo '	<tr>
										<td>
										<input type="checkbox" value="yes" name="a2z_dpdshipping_carrier['.$key.']" '. ((isset($general_settings['a2z_dpdshipping_carrier'][$key]) && $general_settings['a2z_dpdshipping_carrier'][$key] == 'yes') ? 'checked="true"' : '') .' > <small>'.__($value,"a2z_dpdshipping").' - [ '.$key.' ]</small>
										</td>
										<td>
											<input type="text" name="a2z_dpdshipping_carrier_name['.$key.']" value="'.((isset($general_settings['a2z_dpdshipping_carrier_name'][$key])) ? __($general_settings['a2z_dpdshipping_carrier_name'][$key],"a2z_dpdshipping") : '').'">
										</td>
										<td>
											<input type="text" name="a2z_dpdshipping_carrier_adj['.$key.']" value="'.((isset($general_settings['a2z_dpdshipping_carrier_adj'][$key])) ? $general_settings['a2z_dpdshipping_carrier_adj'][$key] : '').'">
										</td>
										<td>
											<input type="text" name="a2z_dpdshipping_carrier_adj_percentage['.$key.']" value="'.((isset($general_settings['a2z_dpdshipping_carrier_adj_percentage'][$key])) ? $general_settings['a2z_dpdshipping_carrier_adj_percentage'][$key] : '').'">
										</td>
										</tr>';
							} ?>
							 <tr><td colspan="4" style="padding:10px;"><hr></td></tr>
				</table>
				<?php if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] !=''){
					echo '<input type="submit" name="save" class="action-button save_change" style="width:auto;float:left;" value="Save Changes" />';
				}

				?>
			    <input type="button" name="next" class="next action-button" value="Next" />

  			<input type="button" name="previous" class="previous action-button" value="Previous" />

	
 </fieldset>  -->

 <fieldset>
 <center><h2 class="fs-title">Configure Shipping Label</h2><br/>
  	<!-- <table style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_ppt" <?php echo (isset($general_settings['a2z_dpdshipping_ppt']) && $general_settings['a2z_dpdshipping_ppt'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable Paperless Trade.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_sat" <?php echo (isset($general_settings['a2z_dpdshipping_sat']) && $general_settings['a2z_dpdshipping_sat'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Saturday Delivery.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_cod" <?php echo (isset($general_settings['a2z_dpdshipping_cod']) && $general_settings['a2z_dpdshipping_cod'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Cash on Delivery.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_aabill" <?php echo (isset($general_settings['a2z_dpdshipping_aabill']) && $general_settings['a2z_dpdshipping_aabill'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Archive Airway Bill.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_email_alert" <?php echo (isset($general_settings['a2z_dpdshipping_email_alert']) && $general_settings['a2z_dpdshipping_email_alert'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> DPD Email Notification.</small></span></td>
		</table>--></center> 
  <table style="width:100%">
  	<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
		
	  <tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Shipment Content','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<input type="text" name="a2z_dpdshipping_ship_content" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_ship_content'])) ? $general_settings['a2z_dpdshipping_ship_content'] : ''; ?>">
			</td>
			<td style="padding:10px;">
				<?php _e('Shipping Label Format (PDF)','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<select name="a2z_dpdshipping_print_size" style="width:95%;padding:5px;">
					<?php foreach($print_size as $key => $value)
					{
						if(isset($general_settings['a2z_dpdshipping_print_size']) && ($general_settings['a2z_dpdshipping_print_size'] == $key))
						{
							echo "<option value=".$key." selected='true'>".$value."</option>";
						}
						else
						{
							echo "<option value=".$key.">".$value."</option>";
						}
					} ?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td style=" width: 50%;padding:10px;">
				<?php _e('Who will pay Duty Payments?','a2z_dpdshipping') ?><font style="color:red;">*</font>
				<select name="a2z_dpdshipping_duty_payment" style="width:95%;padding:5px;">
					<?php foreach($duty_payment_type as $key => $value)
					{
						if(isset($general_settings['a2z_dpdshipping_duty_payment']) && ($general_settings['a2z_dpdshipping_duty_payment'] == $key))
						{
							echo "<option value=".$key." selected='true'>".$value."</option>";
						}
						else
						{
							echo "<option value=".$key.">".$value."</option>";
						}
					} ?>
				</select><br>
				<small style="color:gray;">This is for who gonna pay the duty payment and taxes. This is based on your DPD agreement. </small>
			</td>
			<td>
			<?php _e('Email address to sent Shipping label','a2z_dpdshipping') ?><font style="color:red;">*</font>
			<input type="text" name="a2z_dpdshipping_label_email" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_label_email'])) ? $general_settings['a2z_dpdshipping_label_email'] : ''; ?>"><br>
			<small style="color:gray;"> While Shipi created the shipping label, It will sent the label, invoice to the given email. If you don't need this thenleave it empty.</small>
			</td>
		</tr>
		<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
		
		<table id="rules_tbl" width="100%" style="text-align: center;">
		
		<tr><center><?php _e('Configure the Price for Automated Shipment','a2z_dpdshipping') ?></center><tr>
			<tr>
				<th></th>
				<th>Weight from</th>
				<th>Weight to</th>
				<th>shipping Cost</th>
				
			</tr>
			<tbody id="rules_tbdy">
			<?php
			
			$rules_weight_from = isset($general_settings['a2z_dpdshipping_rule_weight_from'])? $general_settings['a2z_dpdshipping_rule_weight_from'] : '';
			$rules_weight_to = isset($general_settings['a2z_dpdshipping_rule_weight_to']) ? $general_settings['a2z_dpdshipping_rule_weight_to'] : '';
			$rules_ship_cost = isset($general_settings['a2z_dpdshipping_rule_shipping_cost']) ? $general_settings['a2z_dpdshipping_rule_shipping_cost'] : '';
			
			if(isset($rules_ship_cost) && !empty($rules_ship_cost)){
				foreach($rules_ship_cost as $ky=>$rule_shp_cst){
					echo '<tr id="ruletr'.$ky.'">
					<td><input type="checkbox" name="a2z_dpdshipping_rule_chkbx" value="'.$ky.'" ></td>
					<td><input type="text" name="a2z_dpdshipping_rule_weight_from['.$ky.']" value="'.$rules_weight_from[$ky].'"></td>
					<td><input type="text" name="a2z_dpdshipping_rule_weight_to['.$ky.']" value="'.$rules_weight_to[$ky].'"></td>
					<td><input type="text" name="a2z_dpdshipping_rule_shipping_cost['.$ky.']" value="'.$rule_shp_cst.'"></td>';
				}
			}else{
				echo '<tr>
					<td><input type="checkbox" value="" ></td>
					<td><input type="text" name="a2z_dpdshipping_rule_weight_from[0]" value=""></td>
					<td><input type="text" name="a2z_dpdshipping_rule_weight_to[0]" value=""></td>
					<td><input type="text" name="a2z_dpdshipping_rule_shipping_cost[0]" value=""></td>';
			}
			
			
				?>
			</tr>
			</tbody>
		</table><br>
		<td><small><button class="button-rules" id="rules_add">ADD</button></small></td>
		<td><small><button class="button-rules" id="rules_del">DEL</button></small></td><br><br>
		
		

		<td>
			<?php _e('Default Shipping Cost','a2z_dpdshipping') ?><font style="color:red;">*</font><br>
			<input style="width: 40%;" type="text" name="a2z_dpdshipping_default_ship_cost" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_default_ship_cost'])) ? $general_settings['a2z_dpdshipping_default_ship_cost'] : ''; ?>"><br><br>
		</td>
		<td>
			<?php _e('BOX44 Code','a2z_dpdshipping') ?><font style="color:red;">*</font><br>
			<input style="width: 40%;" type="text" name="a2z_dpdshipping_default_box44" placeholder="Y921" value="<?php echo (isset($general_settings['a2z_dpdshipping_default_box44'])) ? $general_settings['a2z_dpdshipping_default_box44'] : ''; ?>"><br>
		</td>
		<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
		</table>

		<!-- // SHIPPING LABEL AUTOMATION -->

	<!-- <center><h2 class="fs-title">SHIPPING LABEL AUTOMATION</h2><br/>
  	<table style="padding-left:10px;padding-right:10px;">
		<tr>
			<small style="color:red; text-align: justify;">Note: </small><small style="color:gray;"> when our plugin shipping services are choosed, The selected service taken as a service to generated tp shipping label.</small>
		<br>
			<small style="color:red;text-align: justify;">Note: </small><small style="color:gray;"> considered when you are using the DPD automation for other shipping plugins.</small>
		</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<p> <span class="" ></span>	<?php _e('Default Domestic Service','a2z_dpdshipping') ?><p>
				</td>
				<td style="padding:10px;">
					<select name="a2z_dpdshipping_Domestic_service" style="padding:5px; width:95%;" id = "a2z_dpdshipping_Domestic_service" class="wc-enhanced-select" style="width:153px;" onchange="changepacktype(this)">
						<?php 
							foreach($_carriers as $key => $values){
								if(isset($general_settings['a2z_dpdshipping_Domestic_service']) && $general_settings['a2z_dpdshipping_Domestic_service'] == $key)
								{
									echo "<option value=".$key." selected ='true'>".$values."-[".$key."]"."</option>";
								}
								else{
									echo "<option value=".$key.">".$values."-[".$key."]"."</option>";
								}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td style=" width: 50%;padding:10px;">
					<p> <span class="" ></span>	<?php _e('Default International Service','a2z_dhlexpress') ?></p>
				</td>
				<td style="padding:10px;">
					<select name="a2z_dpdshipping_international_service" style="padding:5px; width:95%;" id = "a2z_dpdshipping_international_service" class="wc-enhanced-select" style="width:153px;" onchange="changepacktype(this)">
						<?php									
							foreach($_carriers as $key => $values){
								if(isset($general_settings['a2z_dpdshipping_international_service']) && $general_settings['a2z_dpdshipping_international_service'] == $key)
								{
									echo "<option value=".$key." selected ='true'>".$values."-[".$key."]"."</option>";
								}
								else{
									echo "<option value=".$key.">".$values."-[".$key."]"."</option>";
								}
							}
						
							?>
							
					</select>
				</td>
			</tr>						
	</table>
		</center> -->
		
		<!-- <center><h2 class="fs-title">Shippment Tracking</h2><br/>
  	<table style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_uostatus" <?php echo (isset($general_settings['a2z_dpdshipping_uostatus']) && $general_settings['a2z_dpdshipping_uostatus'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Update the order status by tracking.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_trk_status_cus" <?php echo (isset($general_settings['a2z_dpdshipping_trk_status_cus']) && $general_settings['a2z_dpdshipping_trk_status_cus'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Enable tracking in user my account section.</small></span></td>
		</table>
		</center>
		<table style="width:100%">
		<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
		</table>
		<center><h2 class="fs-title">Pickup</h2><br/>
  	<table style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_pickup_automation" <?php echo (isset($general_settings['a2z_dpdshipping_pickup_automation']) && $general_settings['a2z_dpdshipping_pickup_automation'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Automatically assign pickup.</small></span></td>
		</table>
		</center><br>
		
				<table style="width:100%">
				
					<tr>
						<td style=" width: 50%;padding:10px;">
							<?php _e('Location type of the pickup place','a2z_dpdshipping') ?>
							<select name="a2z_dpdshipping_pickup_loc_type" style="width:95%;padding:5px;" class="wc-enhanced-select">
								<?php foreach($pickup_loc_type as $key => $value)
								{
									if(isset($general_settings['a2z_dpdshipping_pickup_loc_type']) && ($general_settings['a2z_dpdshipping_pickup_loc_type'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
						<td style="padding:10px;">
						<?php _e('Package Location','a2z_dpdshipping') ?><font style="color:red;">*</font>
						<input type="text" name="a2z_dpdshipping_pickup_pac_loc" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_pickup_pac_loc'])) ? $general_settings['a2z_dpdshipping_pickup_pac_loc'] : 'Front desk'; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%;padding:10px;">
							<?php _e('Contact person name','a2z_dpdshipping') ?><font style="color:red;">*</font>
							<input type="text" name="a2z_dpdshipping_pickup_per_name" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_pickup_per_name'])) ? $general_settings['a2z_dpdshipping_pickup_per_name'] : ''; ?>">
						</td>
						<td style="padding:10px;">
						<?php _e('Contact number','a2z_dpdshipping') ?><font style="color:red;">*</font>
						<input type="text" name="a2z_dpdshipping_pickup_per_contact_no" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_pickup_per_contact_no'])) ? $general_settings['a2z_dpdshipping_pickup_per_contact_no'] : ''; ?>">
						</td>
					</tr>
					
					<tr>
						<td style=" width: 50%;padding:10px;">
							<?php _e('Door To','a2z_dpdshipping') ?><font style="color:red;">*</font>
							<select name="a2z_dpdshipping_pickup_door_to" class="wc-enhanced-select" style="width:95%;padding:5px;">
								<?php foreach($pickup_del_type as $key => $value)
								{
									if(isset($general_settings['a2z_dpdshipping_pickup_door_to']) && ($general_settings['a2z_dpdshipping_pickup_door_to'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
						<td style="padding:10px;">
						<?php _e('Pickup Type','a2z_dpdshipping') ?><font style="color:red;">*</font>
						<select name="a2z_dpdshipping_pickup_type" class="wc-enhanced-select" id="a2z_dpdshipping_pickup_type" style="width:95%;padding:5px;">
								<?php foreach($pickup_type as $key => $value)
								{
									if(isset($general_settings['a2z_dpdshipping_pickup_type']) && ($general_settings['a2z_dpdshipping_pickup_type'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%;padding:10px;">
						<?php _e('Pickup open time','a2z_dpdshipping') ?><font style="color:red;">*</font>
						<input type="text" name="a2z_dpdshipping_pickup_open_time" placeholder="e.g. 14:20 (for 2.20 pm)" value="<?php echo (isset($general_settings['a2z_dpdshipping_pickup_open_time'])) ? $general_settings['a2z_dpdshipping_pickup_open_time'] : ''; ?>">
						</td>
						<td style="padding:10px;" id="pickup_date_row">
							<?php _e('Schedule pickup after how many days?','a2z_dpdshipping') ?><font style="color:red;">*</font>
							<select name="a2z_dpdshipping_pickup_date" class="wc-enhanced-select" style="width:95%;padding:5px;">
								<?php
								for ($i = 1; $i <= 10; $i++) {
									if(isset($general_settings['a2z_dpdshipping_pickup_date']) && $general_settings['a2z_dpdshipping_pickup_date'] == $i)
									{
										echo "<option value=".$i." selected='true'>".$i."</option>";
									}
									else
									{
										echo "<option value=".$i.">".$i."</option>";
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr id="pickup_close_time_row">
						<td style=" width: 50%;padding:10px;">
							<?php _e('Pickup close time','a2z_dpdshipping') ?><font style="color:red;">*</font>
							<input type="text" name="a2z_dpdshipping_pickup_close_time" placeholder="e.g. 14:20 (for 2.20 pm)" value="<?php echo (isset($general_settings['a2z_dpdshipping_pickup_close_time'])) ? $general_settings['a2z_dpdshipping_pickup_close_time'] : ''; ?>">
						</td>
						<td style="padding:10px;">
							
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<h4 style="color: red;"> ( Note: For "SameDay" pickup type, the pickup will be scheduled based on povided open time. If Pickup open time on shipper's location not greater than 2 hours of Scheduling/Order Placement then the pickup will be scheduled for next day. Shipper's account details and address were utlized for pickup location and shipment fields. )</h4>
						</td>
					</tr>
					<tr><td colspan="2" style="padding:10px;"><hr></td></tr>
					
				</table> -->
				<?php if(isset($general_settings['a2z_dpdshipping_integration_key']) && $general_settings['a2z_dpdshipping_integration_key'] !=''){
					echo '<input type="submit" name="save" class="action-button save_change" style="width:auto;float:left;" value="Save Changes" />';
				}

				?>
				<input type="button" name="next" class="next action-button" value="Next" />

 			<input type="button" name="previous" class="previous action-button" value="Previous" />

	
 </fieldset>
 <?php
  }
  ?>
  <fieldset>
    <center><h2 class="fs-title">LINK Shipi</h2><br>
	<img src="<?php echo plugin_dir_url(__FILE__); ?>hdpd.png">
	<h3 class="fs-subtitle">Shipi is performs all the operations in its own server. So it won't affect your page speed or server usage.</h3>
	<?php 
		if(!isset($general_settings['a2z_dpdshipping_integration_key']) || empty($general_settings['a2z_dpdshipping_integration_key'])){
		?>
		<input type="radio" name="shipo_link_type" id="WITHOUT" value="WITHOUT" checked>I don't have Shipi account  &nbsp; &nbsp; &nbsp;
		<input type="radio" name="shipo_link_type" id="WITH" value="WITH">I have Shipi integration key
<br><hr>
		<table class="with_shipo_acc" style="width:100%;text-align:center;display: none;">
		<tr>
			<td style="width: 50%;padding:10px;">
				<?php _e('Enter Intergation Key', 'a2z_dpdshipping') ?><font style="color:red;">*</font><br>
				
				<input type="text" style="width:330px;" class="intergration" id="shipo_intergration"  name="a2z_dpdshipping_integration_key" value="">
			</td>
		</tr>
	</table>
	<table class="without_shipo_acc" style="padding-left:10px;padding-right:10px;">
		<td><span style="float:left;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_track_audit" <?php echo (isset($general_settings['a2z_dpdshipping_track_audit']) && $general_settings['a2z_dpdshipping_track_audit'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Track shipments everyday & Update the order status with Audit shipments.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_daily_report" <?php echo (isset($general_settings['a2z_dpdshipping_daily_report']) && $general_settings['a2z_dpdshipping_daily_report'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Daily Report.</small></span></td>
		<td><span style="float:right;padding-right:10px;"><input type="checkbox" name="a2z_dpdshipping_monthly_report" <?php echo (isset($general_settings['a2z_dpdshipping_monthly_report']) && $general_settings['a2z_dpdshipping_monthly_report'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" ><small style="color:gray"> Monthly Report.</small></span></td>
		</table></center>
    <table class="without_shipo_acc" style="width:100%;text-align:center;">
	<tr><td style="padding:10px;"></td></tr>
	
	<tr>
		<td style=" width: 50%;padding:10px;">
			<?php _e('Email address to signup / check the registered email.','a2z_dpdshipping') ?><font style="color:red;">*</font><br>
			<input type="email"  id="shipo_mail" style="width:330px;" placeholder="Enter email address" name="a2z_dpdshipping_shipo_signup"  placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_shipo_signup'])) ? $general_settings['a2z_dpdshipping_shipo_signup'] : ''; ?>">
		</td>
		
	</tr>
	<tr>
		<td style=" width: 50%;padding:10px;">
			<?php _e('Email Password') ?><font style="color:red;">*</font><br>
			<input type="password" id="shipo_password" style="width:330px;" placeholder="Enter Password" name="a2z_dpdshipping_shipo_password"  placeholder="" value="">
		</td>
		
	</tr>
	
	<tr><td style="padding:10px;"><hr></td></tr>
	
	</table>

	<?php }else{
		?>
		<tr>
				<td style="padding:10px;">
					<?php _e('Hitshipo Integration Key', 'a2z_dpdshipping') ?><br><br>
				</td>
			</tr>
			<tr>
				<td><span style="padding-right:10px; text-align:center;"><input type="checkbox" id='intergration_ckeck_box'><small style="color:gray">Edit intergration key</small></span></td>
			</tr>
			<tr>
				<td>
					<input style="width:24%; text-align:center; pointer-events:none;" required type="text" id="intergration" name="a2z_dpdshipping_integration_key" placeholder="" value="<?php echo (isset($general_settings['a2z_dpdshipping_integration_key'])) ? $general_settings['a2z_dpdshipping_integration_key'] : ''; ?>">
				</td>
			</tr>
		<p style="font-size:14px;line-height:24px;">
			Site Linked Successfully. <br><br>
		It's great to have you here. Your account has been linked successfully with Shipi. <br><br>
Make your customers happier by reacting faster and handling their service requests in a timely manner, meaning higher store reviews and more revenue.</p>
		<?php
		echo '</center>';
	}
	?>
	<?php echo '<center>' . $error . '</center>'; ?>
	
	<input type="submit" name="save" class="action-button save_change" style="width:auto;" value="Save & Start" />
	 <?php if (empty($error)) { ?>
    <input type="button" name="previous" class="previous action-button" value="Previous" />
	 <?php }else{ ?>
		<button type="button" style="padding:11px;" name="previous" class="previous action-button"  onclick="location.reload();">Previous</button>
	<?php } ?>
  </fieldset>
</form>
<center><a href="https://app.myshipi.com/support" target="_blank" style="width:auto;margin-right :20px;" class="button button-primary">Trouble in configuration? / not working? Email us.</a>
<a href="https://calendly.com/aarsivgroups/meeting" target="_blank" style="width:auto;" class="button button-primary">Looking for demo ? Book your slot with our expert</a></center>
<?php } ?>
		<script>
			var current_fs, next_fs, previous_fs;
var left, opacity, scale;
var animating;
jQuery(".next").click(function () {
  if (animating) return false;
  animating = true;

  current_fs = jQuery(this).parent();
  next_fs = jQuery(this).parent().next();
  jQuery("#progressbar li").eq(jQuery("fieldset").index(next_fs)).addClass("active");
  next_fs.show();
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; 
  current_fs.animate(
    { opacity: 0 },
    {
      step: function (now, mx) {
        scale = 1 - (1 - now) * 0.2;
        left = now * 50 + "%";
        opacity = 1 - now;
        current_fs.css({
          transform: "scale(" + scale + ")"});
        next_fs.css({ left: left, opacity: opacity });
      },
      duration: 0,
      complete: function () {
        current_fs.hide();
        animating = false;
      },
      //easing: "easeInOutBack"
    }
  );
});

jQuery(".previous").click(function () {
  if (animating) return false;
  animating = true;

  current_fs = jQuery(this).parent();
  previous_fs = jQuery(this).parent().prev();
  jQuery("#progressbar li")
    .eq(jQuery("fieldset").index(current_fs))
    .removeClass("active");

  previous_fs.show();
  current_fs.animate(
    { opacity: 0 },
    {
      step: function (now, mx) {
        scale = 0.8 + (1 - now) * 0.2;
        left = (1 - now) * 50 + "%";
        opacity = 1 - now;
        current_fs.css({ left: left });
        previous_fs.css({
          transform: "scale(" + scale + ")",
          opacity: opacity
        });
      },
      duration: 0,
      complete: function () {
        current_fs.hide();
        animating = false;
      },
      //easing: "easeInOutBack"
    }
  );
});

jQuery(".submit").click(function () {
  return false;
});

jQuery(document).ready(function(){
	var dpd_curr = '<?php echo $general_settings['a2z_dpdshipping_currency']; ?>';
	var woo_curr = '<?php echo $general_settings['a2z_dpdshipping_woo_currency']; ?>';
	// console.log(dpd_curr);
	// console.log(woo_curr);

	if (dpd_curr != null && dpd_curr == woo_curr) {
		jQuery('.con_rate').each(function(){
		jQuery('.con_rate').hide();
	    });
	}else{
		if(jQuery("#auto_con").prop('checked') == true){
			jQuery('.con_rate').hide();
		}else{
			jQuery('.con_rate').each(function(){
			jQuery('.con_rate').show();
		    });
		}
	}

	jQuery('#a2z_dpdshipping_pickup_type').change(function(){
		if(jQuery(this).val() == 'S') {
			jQuery('#pickup_date_row').hide();
	      }else{
	        jQuery('#pickup_date_row').show();
	      }
	}).change();

	jQuery('#add_box').click( function() {
		var pack_type_options = '<option value="BOX">DPD Box</option><option value="FLY">Flyer</option><option value="YP" selected="selected" >Your Pack</option>';
		var tbody = jQuery('#box_pack_t').find('#box_pack_tbody');
		var size = tbody.find('tr').size();
		var code = '<tr class="new">\
			<td  style="padding:3px;" class="check-column"><input type="checkbox" /></td>\
			<input type="hidden" size="1" name="boxes_id[' + size + ']" value="box_id_' + size + '"/>\
			<td style="padding:3px;"><input type="text" size="25" name="boxes_name[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_length[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_width[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_height[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_box_weight[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_max_weight[' + size + ']" /></td>\
			<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled[' + size + ']" /></center></td>\
			<td style="padding:3px;"><select name="boxes_pack_type[' + size + ']" >' + pack_type_options + '</select></td>\
	        </tr>';
		tbody.append( code );
		return false;
	});

	jQuery('#remove_box').click(function() {
		var tbody = jQuery('#box_pack_t').find('#box_pack_tbody');console.log(tbody);
		tbody.find('.check-column input:checked').each(function() {
			jQuery(this).closest('tr').remove().find('input').val('');
		});
		return false;
	});

	var payment_cun = "<?php echo isset($general_settings['a2z_dpdshipping_pay_con']) ? $general_settings['a2z_dpdshipping_pay_con'] : ''; ?>";
	if (payment_cun != null && payment_cun == 'C') {
		jQuery('#cus_pay_con').show();
	}else{
		jQuery('#cus_pay_con').hide();
	}

	var translation = "<?php echo ( isset($general_settings['a2z_dpdshipping_translation']) && !empty($general_settings['a2z_dpdshipping_translation']) ) ? $general_settings['a2z_dpdshipping_translation'] : ''; ?>";
	if (translation != null && translation == "yes") {
		jQuery('#translation_key').show();
	}else{
		jQuery('#translation_key').hide();
	}

	jQuery('#a2z_dpdshipping_translation').click(function() {
		if (jQuery(this).is(":checked")) {
			jQuery('#translation_key').show();
		}else{
			jQuery('#translation_key').hide();
		}
	});
	jQuery('.save_change').click(function() {
		var site_id = jQuery('#a2z_dpdshipping_site_id').val();
		var site_pwd = jQuery('#a2z_dpdshipping_site_pwd').val();
		var shipper_name = jQuery('#a2z_dpdshipping_shipper_name').val();
		var shipper_company = jQuery('#a2z_dpdshipping_company').val();
		var mob_no = jQuery('#a2z_dpdshipping_mob_num').val();
		var email_address = jQuery('#a2z_dpdshipping_email').val();
		var shipper_address = jQuery('#a2z_dpdshipping_address1').val();
		var shipper_city = jQuery('#a2z_dpdshipping_city').val();
		var shipper_state = jQuery('#a2z_dpdshipping_state').val();
		var shipper_zip = jQuery('#a2z_dpdshipping_zip').val();
		var shipo_mail = jQuery('#shipo_mail').val();
		var shipo_password = jQuery('#shipo_password').val();
		var shipo_intergration = jQuery('#shipo_intergration').val();
			
			if(site_id == ''){
				alert('User Name is empty');
				return false;
			}	
			if(site_pwd == ''){
				alert('Password is empty');
				return false;
			}	
		
			if(shipper_name == ''){
				alert('Shipper Name is empty');
				return false;
			}
			if(shipper_company == ''){
				alert('Company Name is empty');
				return false;
			}
			if(mob_no == ''){
				alert('Shipper Mobile / Contact Number is empty');
				return false;
			}
			if(email_address == ''){
				alert('Email Address of the Shipper is empty');
				return false;
			}
			if(shipper_address == ''){
				alert('Address Line 1 is empty');
				return false;
			}
			if(shipper_city == ''){
				alert('City of the Shipper from address is empty');
				return false;
			}
			if(shipper_state == ''){
				alert('State of the Shipper from address is empty');
				return false;
			}
			if(shipper_zip == ''){
				alert('Postal/Zip Code is empty');
				return false;
			}
			var link_type = jQuery("input[name='shipo_link_type']:checked").val();
			if (link_type === 'WITHOUT') {
				if(shipo_mail == ''){
						alert('Enter Shipi Email');
						return false;
					}
					if(shipo_password == ''){
						alert('Enter Shipi Password');
						return false;
					}
			} else {
				if(shipo_intergration == ''){
						alert('Enter Shipi intergtraion Key');
						return false;
					}
			}
		
			
	});

});
function changepacktype(selectbox){
	var box = document.getElementById("box_pack");
	var weight = document.getElementById("weight_based");
	var box_type = selectbox.value;
	if(box_type == "weight_based"){			
		weight.style.display = "table-row";
	}else{
		weight.style.display = "none";
	}
	if (box_type == "box") {
	    box.style.display = "block";
	  } else {
	    box.style.display = "none";
	  }
		// alert(box_type);
}
	var box_type = jQuery('#a2z_dpdshipping_packing_type').val();
	// var weight = document.getElementById("weight_based");
	// var box = document.getElementById("box_pack");
	if (box_type != "box") {
		// box.style.display = "none";
		jQuery('#box_pack').hide();
	}
	if (box_type != "weight_based") {
		// weight.style.display = "none";
		jQuery('#weight_based').hide();
	}else{
		// weight.style.display = "table-row";
		jQuery('#weight_based').show();
	}

	jQuery("#auto_con").change(function() {
	    if(this.checked) {
	        jQuery('.con_rate').hide();
	    }else{
	    	jQuery('.con_rate').show();
	    }
	});

function changepaycon(selectbox){
	// var payment_cun = document.getElementById("a2z_dpdshipping_pay_con");
	var sel_pay_cun = selectbox.value;
	var cus_pay = document.getElementById("cus_pay_con");
	if (sel_pay_cun == "C") {
	    cus_pay.style.display = "table-cell";
	  } else {
	    cus_pay.style.display = "none";
	  }
		// alert(box_type);
}

jQuery('#rules_add').click( function() {
	var $tbody = jQuery('#rules_tbl').find('#rules_tbdy');
	var size = $tbody.find('tr').size();
	var code = '<tr id="ruletr'+ size +'"><td><input type="checkbox" name="a2z_dpdshipping_rule_chkbx" value="'+ size +'" ></td>\
					<td><input type="text" name="a2z_dpdshipping_rule_weight_from['+ size +']" value=""></td>\
					<td><input type="text" name="a2z_dpdshipping_rule_weight_to['+ size +']" value=""></td>\
					<td><input type="text" name="a2z_dpdshipping_rule_shipping_cost['+ size +']" value=""></td></tr>'
	$tbody.append( code );
	return false;
});

jQuery('#rules_del').click( function() {
	jQuery("input:checkbox[name=a2z_dpdshipping_rule_chkbx]:checked").each(function(){
    // yourArray.push($(this).val());
	var checked = jQuery(this).val();
	jQuery('#ruletr'+checked).remove();
	// alert(checked);
});
return false;
});
jQuery(function () {
	jQuery("#intergration_ckeck_box").click(function () {
            if (jQuery(this).is(":checked")) {
                jQuery("#intergration").css("pointer-events", "auto");
            } else {
				jQuery("#intergration").css("pointer-events", "none");
            }
        });
    });
	jQuery(document).ready(function() {
			jQuery("input[name='shipo_link_type']").change(function() {
			if (jQuery(this).val() == "WITHOUT") {
				jQuery(".without_shipo_acc").show();
				jQuery(".with_shipo_acc").hide();
			} else if (jQuery(this).val() == "WITH") {
				jQuery(".without_shipo_acc").hide();
				jQuery(".with_shipo_acc").show();
			}
		});
	});
</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/671925bb4304e3196ad6b676/1iat3mpss';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->