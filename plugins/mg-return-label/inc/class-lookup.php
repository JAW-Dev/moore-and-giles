<?php

class MG_ReturnLabel_Lookup {
	/**
	 * Returns a lookup table of supported country defaults
	 *
	 * The information in the following table has been derived from
	 * the ISO standard documents including ISO-3166 for 2-letter country
	 * codes and ISO-4217 for currency codes
	 *
	 * @see wiki ISO_3166-1 & ISO_4217
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return array
	 **/
	public static function countries () {
		$_ = array();
		$_['CA'] = array('name' => __('Canada'), 'currency' => array('code' => 'CAD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['US'] = array('name' => __('USA'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'imperial', 'region' => 0);

		// Specialized countries for US Armed Forces and US Territories
		$_['USAF'] = array('name' => __('US Armed Forces'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'imperial', 'region' => 0);
		$_['USAT'] = array('name' => __('US Territories'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'imperial', 'region' => 0);

		$_['GB'] = array('name' => __('United Kingdom'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['AF'] = array('name' => __('Afghanistan'), 'currency' => array('code' => 'AFN', 'format' => '؋ #,###.##'), 'units' => 'metric', 'region' => 6);
		$_['AX'] = array('name' => __('Åland Islands'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['AL'] = array('name' => __('Albania'), 'currency' => array('code' => 'ALL', 'format' => 'Lek #,###.##'), 'units' => 'metric', 'region' => 3);
		$_['DZ'] = array('name' => __('Algeria'), 'currency' => array('code' => 'DZD', 'format' => '#,###.## د.ج'), 'units' => 'metric', 'region' => 5);
		$_['AS'] = array('name' => __('American Samoa'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['AD'] = array('name' => __('Andorra'), 'currency' => array('code' => 'EUR', 'format' => '€#.###,##'), 'units' => 'metric', 'region' => 3);
		$_['AO'] = array('name' => __('Angola'), 'currency' => array('code' => 'AOA', 'format' => '# ###,## Kz'), 'units' => 'metric', 'region' => 5);
		$_['AI'] = array('name' => __('Anguilla'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['AG'] = array('name' => __('Antigua and Barbuda'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['AR'] = array('name' => __('Argentina'), 'currency' => array('code' => 'ARS', 'format' => '$#.###,##'), 'units' => 'metric', 'region' => 2);
		$_['AM'] = array('name' => __('Armenia'), 'currency' => array('code' => 'AMD', 'format' => '####,## Դրամ'), 'units' => 'metric', 'region' => 6);
		$_['AW'] = array('name' => __('Aruba'), 'currency' => array('code' => 'AWG', 'format' => 'ƒ#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['AU'] = array('name' => __('Australia'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['AT'] = array('name' => __('Austria'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['AZ'] = array('name' => __('Azerbaijan'), 'currency' => array('code' => 'AZN', 'format' => 'man. #.###,##'), 'units' => 'metric', 'region' => 6);
		$_['BD'] = array('name' => __('Bangladesh'), 'currency' => array('code' => 'BDT', 'format' => '#,###.##৳'), 'units' => 'metric', 'region' => 6);
		$_['BB'] = array('name' => __('Barbados'), 'currency' => array('code' => 'BBD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['BS'] = array('name' => __('Bahamas'), 'currency' => array('code' => 'BSD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['BH'] = array('name' => __('Bahrain'), 'currency' => array('code' => 'BHD', 'format' => 'ب.د #,###.##'), 'units' => 'metric', 'region' => 0);
		$_['BY'] = array('name' => __('Belarus'), 'currency' => array('code' => 'BYR', 'format' => 'BYR# ###,##'), 'units' => 'metric', 'region' => 3);
		$_['BE'] = array('name' => __('Belgium'), 'currency' => array('code' => 'EUR', 'format' => '#.###,## €'), 'units' => 'metric', 'region' => 3);
		$_['BZ'] = array('name' => __('Belize'), 'currency' => array('code' => 'BZD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['BJ'] = array('name' => __('Benin'), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['BM'] = array('name' => __('Bermuda'), 'currency' => array('code' => 'BMD', 'format' => 'BD$#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['BT'] = array('name' => __('Bhutan'), 'currency' => array('code' => 'BTN', 'format' => 'Nu. #,###.##'), 'units' => 'metric', 'region' => 6);
		$_['BO'] = array('name' => __('Bolivia'), 'currency' => array('code' => 'BOB', 'format' => 'Bs #.###,##'), 'units' => 'metric', 'region' => 2);
		$_['BA'] = array('name' => __('Bosnia and Herzegovina'), 'currency' => array('code' => 'BAM', 'format' => 'KM #.###,##'), 'units' => 'metric', 'region' => 3);
		$_['BW'] = array('name' => __('Botswana'), 'currency' => array('code' => 'BWP', 'format' => 'P#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['BR'] = array('name' => __('Brazil'), 'currency' => array('code' => 'BRL', 'format' => 'R$#.###,##'), 'units' => 'metric', 'region' => 2);
		$_['IO'] = array('name' => __('British Indian Ocean Territory'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['VG'] = array('name' => __('British Virgin Islands'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['BN'] = array('name' => __('Brunei Darussalam'), 'currency' => array('code' => 'BND', 'format' => '$ #.###,##'), 'units' => 'metric', 'region' => 6);
		$_['BG'] = array('name' => __('Bulgaria'), 'currency' => array('code' => 'BGN', 'format' => '# ###,## лв.'), 'units' => 'metric', 'region' => 3);
		$_['BF'] = array('name' => __('Burkina Faso'), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['MM'] = array('name' => __('Burma'), 'currency' => array('code' => 'MMK', 'format' => 'K #,###.##'), 'units' => 'metric', 'region' => 6);
		$_['BI'] = array('name' => __('Burundi'), 'currency' => array('code' => 'BIF', 'format' => '# ###,## FBu'), 'units' => 'metric', 'region' => 5);
		$_['KH'] = array('name' => __('Cambodia'), 'currency' => array('code' => 'KHR', 'format' => '#.###,##៛'), 'units' => 'metric', 'region' => 6);
		$_['CM'] = array('name' => __('Cameroon'), 'currency' => array('code' => 'XAF', 'format' => '# ### FCFA'), 'units' => 'metric', 'region' => 5);
		$_['CV'] = array('name' => __('Cape Verde'), 'currency' => array('code' => 'CVE', 'format' => 'CV$#.###,##'), 'units' => 'metric', 'region' => 5);
		$_['KY'] = array('name' => __('Cayman Islands'), 'currency' => array('code' => 'KYD', 'format' => 'CI$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['CF'] = array('name' => __('Central African Republic'), 'currency' => array('code' => 'XAF', 'format' => '# ### FCFA'), 'units' => 'metric', 'region' => 5);
		$_['TD'] = array('name' => __('Chad'), 'currency' => array('code' => 'XAF', 'format' => '# ### FCFA'), 'units' => 'metric', 'region' => 5);
		$_['CL'] = array('name' => __('Chile'), 'currency' => array('code' => 'CLP', 'format' => '$#.###,##'), 'units' => 'metric', 'region' => 2);
		$_['CN'] = array('name' => __('China'), 'currency' => array('code' => 'CNY', 'format' => '¥#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['CX'] = array('name' => __('Christmas Island'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['CC'] = array('name' => __('Cocos (Keeling) Islands'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['CO'] = array('name' => __('Colombia'), 'currency' => array('code' => 'COP', 'format' => '$#.###,##'), 'units' => 'metric', 'region' => 2);
		$_['KM'] = array('name' => __('Comoros'), 'currency' => array('code' => 'KMF', 'format' => '# ### FC'), 'units' => 'metric', 'region' => 5);
		$_['CG'] = array('name' => __('Congo-Brazzaville'), 'currency' => array('code' => 'XAF', 'format' => '# ### FCFA'), 'units' => 'metric', 'region' => 5);
		$_['CD'] = array('name' => __('Congo-Kinshasa'), 'currency' => array('code' => 'CDF', 'format' => '# ###,## FrCD'), 'units' => 'metric', 'region' => 5);
		$_['CK'] = array('name' => __('Cook Islands'), 'currency' => array('code' => 'NZD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['CR'] = array('name' => __('Costa Rica'), 'currency' => array('code' => 'CRC', 'format' => '₡#.###,##'), 'units' => 'metric', 'region' => 1);
		$_['CI'] = array('name' => __("Côte d'Ivoire"), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['HR'] = array('name' => __('Croatia'), 'currency' => array('code' => 'HRK', 'format' => '#.###,## kn'), 'units' => 'metric', 'region' => 3);
		$_['CU'] = array('name' => __('Cuba'), 'currency' => array('code' => 'CUP', 'format' => 'CUP#.###,##'), 'units' => 'metric', 'region' => 1);
		$_['CW'] = array('name' => __('Curaçao'), 'currency' => array('code' => 'ANG', 'format' => 'ƒ#.###,##'), 'units' => 'metric', 'region' => 0);
		$_['CY'] = array('name' => __('Cyprus'), 'currency' => array('code' => 'EUR', 'format' => '€#.###,##'), 'units' => 'metric', 'region' => 3);
		$_['CZ'] = array('name' => __('Czech Republic'), 'currency' => array('code' => 'CZK', 'format' => '# ###,## Kč'), 'units' => 'metric', 'region' => 3);
		$_['DK'] = array('name' => __('Denmark'), 'currency' => array('code' => 'DKK', 'format' => '#.###,## kr'), 'units' => 'metric', 'region' => 3);
		$_['DJ'] = array('name' => __('Djibouti'), 'currency' => array('code' => 'DJF', 'format' => '# ### Fdj'), 'units' => 'metric', 'region' => 5);
		$_['DM'] = array('name' => __('Dominica'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['DO'] = array('name' => __('Dominican Republic'), 'currency' => array('code' => 'DOP', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['TL'] = array('name' => __('East Timor'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['EC'] = array('name' => __('Ecuador'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['SV'] = array('name' => __('El Salvador'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['EG'] = array('name' => __('Egypt'), 'currency' => array('code' => 'EGP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['GQ'] = array('name' => __('Equatorial Guinea'), 'currency' => array('code' => 'XAF', 'format' => '# ### FCFA'), 'units' => 'metric', 'region' => 5);
		$_['ER'] = array('name' => __('Eritrea'), 'currency' => array('code' => 'ERN', 'format' => 'Nfk,###.##'), 'units' => 'metric', 'region' => 5);
		$_['EE'] = array('name' => __('Estonia'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['ET'] = array('name' => __('Ethiopia'), 'currency' => array('code' => 'ETB', 'format' => 'Br#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['FK'] = array('name' => __('Falkland Islands'), 'currency' => array('code' => 'FKP', 'format' => 'FK£#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['FO'] = array('name' => __('Faroe Islands'), 'currency' => array('code' => 'DKK', 'format' => 'kr#.###,##'), 'units' => 'metric', 'region' => 3);
		$_['FM'] = array('name' => __('Federated States of Micronesia'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['FJ'] = array('name' => __('Fiji'), 'currency' => array('code' => 'FJD', 'format' => 'FJ$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['FI'] = array('name' => __('Finland'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 3);
		$_['FR'] = array('name' => __('France'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 3);
		$_['GF'] = array('name' => __('French Guiana'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 2);
		$_['PF'] = array('name' => __('French Polynesia'), 'currency' => array('code' => 'XPF', 'format' => '#,###.##F'), 'units' => 'metric', 'region' => 7);
		$_['TF'] = array('name' => __('French Southern Lands'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 7);
		$_['GA'] = array('name' => __('Gabon'), 'currency' => array('code' => 'XAF', 'format' => '# ### FCFA'), 'units' => 'metric', 'region' => 5);
		$_['GM'] = array('name' => __('Gambia'), 'currency' => array('code' => 'GMD', 'format' => 'GMD#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['GE'] = array('name' => __('Georgia'), 'currency' => array('code' => 'GEL', 'format' => 'GEL #.###,##'), 'units' => 'metric', 'region' => 6);
		$_['DE'] = array('name' => __('Germany'), 'currency' => array('code' => 'EUR', 'format' => '#,###.## €'), 'units' => 'metric', 'region' => 3);
		$_['GH'] = array('name' => __('Ghana'), 'currency' => array('code' => 'GHS', 'format' => '₵#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['GI'] = array('name' => __('Gibraltar'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['GR'] = array('name' => __('Greece'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['GL'] = array('name' => __('Greenland'), 'currency' => array('code' => 'DKK', 'format' => 'kr#.###,##'), 'units' => 'metric', 'region' => 1);
		$_['GD'] = array('name' => __('Grenada'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['GP'] = array('name' => __('Guadeloupe'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['GU'] = array('name' => __('Guam'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['GT'] = array('name' => __('Guatemala'), 'currency' => array('code' => 'GTQ', 'format' => 'Q#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['GG'] = array('name' => __('Guernsey'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['GN'] = array('name' => __('Guinea'), 'currency' => array('code' => 'GNF', 'format' => '# ### FG'), 'units' => 'metric', 'region' => 5);
		$_['GW'] = array('name' => __('Guinea-Bissau'), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['GY'] = array('name' => __('Guyana'), 'currency' => array('code' => 'GYD', 'format' => 'G$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['HT'] = array('name' => __('Haiti'), 'currency' => array('code' => 'HTG', 'format' => '# ###,## HTG'), 'units' => 'metric', 'region' => 1);
		$_['HM'] = array('name' => __('Heard and McDonald Islands'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['HN'] = array('name' => __('Honduras'), 'currency' => array('code' => 'HNL', 'format' => 'L #,###.##'), 'units' => 'metric', 'region' => 1);
		$_['HK'] = array('name' => __('Hong Kong'), 'currency' => array('code' => 'HKD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['HU'] = array('name' => __('Hungary'), 'currency' => array('code' => 'HUF', 'format' => '# ### ### Ft', 'decimals' => ', '), 'units' => 'metric', 'region' => 3);
		$_['IS'] = array('name' => __('Iceland'), 'currency' => array('code' => 'ISK', 'format' => '#.###.###, kr'), 'units' => 'metric', 'region' => 3);
		$_['IN'] = array('name' => __('India'), 'currency' => array('code' => 'INR', 'format' => '₨#,##,###.##'), 'units' => 'metric', 'region' => 6);
		$_['ID'] = array('name' => __('Indonesia'), 'currency' => array('code' => 'IDR', 'format' => 'Rp #.###,##'), 'units' => 'metric', 'region' => 7);
		$_['IR'] = array('name' => __('Iran'), 'currency' => array('code' => 'IRR', 'format' => '#.###,##﷼'), 'units' => 'metric', 'region' => 4);
		$_['IQ'] = array('name' => __('Iraq'), 'currency' => array('code' => 'IQD', 'format' => '#.###,##د.ع'), 'units' => 'metric', 'region' => 4);
		$_['IE'] = array('name' => __('Ireland'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['IM'] = array('name' => __('Isle of Man'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['IL'] = array('name' => __('Israel'), 'currency' => array('code' => 'ILS', 'format' => '#,###.## ₪'), 'units' => 'metric', 'region' => 4);
		$_['IT'] = array('name' => __('Italy'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['JM'] = array('name' => __('Jamaica'), 'currency' => array('code' => 'JMD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['JP'] = array('name' => __('Japan'), 'currency' => array('code' => 'JPY', 'format' => '¥#,###,###.'), 'units' => 'metric', 'region' => 6);
		$_['JE'] = array('name' => __('Jersey'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['JO'] = array('name' => __('Jordan'), 'currency' => array('code' => 'JOD', 'format' => '#.###,## JD'), 'units' => 'metric', 'region' => 4);
		$_['KZ'] = array('name' => __('Kazakhstan'), 'currency' => array('code' => 'KZT', 'format' => '# ###,## 〒'), 'units' => 'metric', 'region' => 6);
		$_['KE'] = array('name' => __('Kenya'), 'currency' => array('code' => 'KES', 'format' => 'Ksh#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['KI'] = array('name' => __('Kiribati'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['KW'] = array('name' => __('Kuwait'), 'currency' => array('code' => 'KWD', 'format' => '#.###,## د.ك'), 'units' => 'metric', 'region' => 4);
		$_['KG'] = array('name' => __('Kyrgyzstan'), 'currency' => array('code' => 'KGS', 'format' => '# ###,## som'), 'units' => 'metric', 'region' => 6);
		$_['LA'] = array('name' => __('Laos'), 'currency' => array('code' => 'LAK', 'format' => '#,###.## ₭'), 'units' => 'metric', 'region' => 6);
		$_['LV'] = array('name' => __('Latvia'), 'currency' => array('code' => 'LVL', 'format' => '# ###.## Ls'), 'units' => 'metric', 'region' => 3);
		$_['LB'] = array('name' => __('Lebanon'), 'currency' => array('code' => 'LBP', 'format' => '#.### ل.ل'), 'units' => 'metric', 'region' => 4);
		$_['LS'] = array('name' => __('Lesotho'), 'currency' => array('code' => 'LSL', 'format' => 'M# ###,##'), 'units' => 'metric', 'region' => 5);
		$_['LR'] = array('name' => __('Liberia'), 'currency' => array('code' => 'LRD', 'format' => 'LD$#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['LY'] = array('name' => __('Libya'), 'currency' => array('code' => 'LYD', 'format' => '#.###,## ل.د'), 'units' => 'metric', 'region' => 5);
		$_['LI'] = array('name' => __('Liechtenstein'), 'currency' => array('code' => 'CHF', 'format' => "CHF #'###.##"), 'units' => 'metric', 'region' => 3);
		$_['LT'] = array('name' => __('Lithuania'), 'currency' => array('code' => 'LTL', 'format' => '#.###,## Lt'), 'units' => 'metric', 'region' => 3);
		$_['LU'] = array('name' => __('Luxembourg'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['MO'] = array('name' => __('Macau'), 'currency' => array('code' => 'MOP', 'format' => 'MOP$#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['MK'] = array('name' => __('Macedonia'), 'currency' => array('code' => 'MKD', 'format' => 'MKD #.###,##'), 'units' => 'metric', 'region' => 3);
		$_['MG'] = array('name' => __('Madagascar'), 'currency' => array('code' => 'MGA', 'format' => '# ### MGA'), 'units' => 'metric', 'region' => 5);
		$_['MW'] = array('name' => __('Malawi'), 'currency' => array('code' => 'MWK', 'format' => 'MK #,###.##'), 'units' => 'metric', 'region' => 5);
		$_['MY'] = array('name' => __('Malaysia'), 'currency' => array('code' => 'MYR', 'format' => 'RM#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['MV'] = array('name' => __('Maldives'), 'currency' => array('code' => 'MVR', 'format' => 'Rf#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['ML'] = array('name' => __('Mali'), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['MT'] = array('name' => __('Malta'), 'currency' => array('code' => 'MTL', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['MH'] = array('name' => __('Marshall Islands'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['MQ'] = array('name' => __('Martinique'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 1);
		$_['MR'] = array('name' => __('Mauritania'), 'currency' => array('code' => 'MRO', 'format' => '#,###.## UM'), 'units' => 'metric', 'region' => 5);
		$_['MU'] = array('name' => __('Mauritius'), 'currency' => array('code' => 'MUR', 'format' => 'MU₨#,###'), 'units' => 'metric', 'region' => 5);
		$_['YT'] = array('name' => __('Mayotte'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 5);
		$_['MX'] = array('name' => __('Mexico'), 'currency' => array('code' => 'MXN', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 0);
		$_['MD'] = array('name' => __('Moldova'), 'currency' => array('code' => 'MDL', 'format' => '#.###,## MDL'), 'units' => 'metric', 'region' => 3);
		$_['MC'] = array('name' => __('Monaco'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 3);
		$_['MN'] = array('name' => __('Mongolia'), 'currency' => array('code' => 'MNT', 'format' => '# ###,##₮'), 'units' => 'metric', 'region' => 6);
		$_['ME'] = array('name' => __('Montenegro'), 'currency' => array('code' => 'EUR', 'format' => '€ #,###.##'), 'units' => 'metric', 'region' => 3);
		$_['MS'] = array('name' => __('Montserrat'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['MA'] = array('name' => __('Morocco'), 'currency' => array('code' => 'MAD', 'format' => '#.###,## د.م.'), 'units' => 'metric', 'region' => 5);
		$_['MZ'] = array('name' => __('Mozambique'), 'currency' => array('code' => 'MZN', 'format' => 'MTn#.###,##'), 'units' => 'metric', 'region' => 5);
		$_['NA'] = array('name' => __('Namibia'), 'currency' => array('code' => 'NAD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['NR'] = array('name' => __('Nauru'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['NP'] = array('name' => __('Nepal'), 'currency' => array('code' => 'NPR', 'format' => 'रू. #,###.##'), 'units' => 'metric', 'region' => 6);
		$_['NL'] = array('name' => __('Netherlands'), 'currency' => array('code' => 'EUR', 'format' => '€#.###,##'), 'units' => 'metric', 'region' => 3);
		$_['NC'] = array('name' => __('New Caledonia'), 'currency' => array('code' => 'XPF', 'format' => '#,###.##F'), 'units' => 'metric', 'region' => 7);
		$_['NZ'] = array('name' => __('New Zealand'), 'currency' => array('code' => 'NZD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['NI'] = array('name' => __('Nicaragua'), 'currency' => array('code' => 'NIO', 'format' => 'C$ #,###.##'), 'units' => 'metric', 'region' => 1);
		$_['NE'] = array('name' => __('Niger'), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['NG'] = array('name' => __('Nigeria'), 'currency' => array('code' => 'NGN', 'format' => '₦#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['NU'] = array('name' => __('Niue'), 'currency' => array('code' => 'NZD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['NF'] = array('name' => __('Norfolk Island'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['MP'] = array('name' => __('Northern Mariana Islands'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['NO'] = array('name' => __('Norway'), 'currency' => array('code' => 'NOK', 'format' => 'kr # ###,##'), 'units' => 'metric', 'region' => 3);
		$_['OM'] = array('name' => __('Oman'), 'currency' => array('code' => 'OMR', 'format' => '#.###,## ر.ع'), 'units' => 'metric', 'region' => 4);
		$_['PK'] = array('name' => __('Pakistan'), 'currency' => array('code' => 'PKR', 'format' => '₨#,###.##'), 'units' => 'metric', 'region' => 4);
		$_['PW'] = array('name' => __('Palau'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['PA'] = array('name' => __('Panama'), 'currency' => array('code' => 'USD', 'format' => '$ #,###.##'), 'units' => 'metric', 'region' => 1);
		$_['PG'] = array('name' => __('Papua New Guinea'), 'currency' => array('code' => 'PGK', 'format' => 'K#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['PY'] = array('name' => __('Paraguay'), 'currency' => array('code' => 'PYG', 'format' => '₲#.###'), 'units' => 'metric', 'region' => 2);
		$_['PE'] = array('name' => __('Peru'), 'currency' => array('code' => 'PEN', 'format' => 'S/. #,###.##'), 'units' => 'metric', 'region' => 2);
		$_['PH'] = array('name' => __('Philippines'), 'currency' => array('code' => 'PHP', 'format' => 'Php #,###.##'), 'units' => 'metric', 'region' => 6);
		$_['PN'] = array('name' => __('Pitcairn Islands'), 'currency' => array('code' => 'NZD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['PL'] = array('name' => __('Poland'), 'currency' => array('code' => 'PLN', 'format' => '#.###,## zł'), 'units' => 'metric', 'region' => 3);
		$_['PT'] = array('name' => __('Portugal'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['PR'] = array('name' => __('Puerto Rico'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'imperial', 'region' => 0);
		$_['QA'] = array('name' => __('Qatar'), 'currency' => array('code' => 'QAR', 'format' => '####,## ر.ق'), 'units' => 'metric', 'region' => 4);
		$_['RE'] = array('name' => __('Réunion'), 'currency' => array('code' => '', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 5);
		$_['RO'] = array('name' => __('Romania'), 'currency' => array('code' => 'RON', 'format' => '#.###,## lei'), 'units' => 'metric', 'region' => 3);
		$_['RU'] = array('name' => __('Russia'), 'currency' => array('code' => 'RUB', 'format' => '# ###,## руб'), 'units' => 'metric', 'region' => 6);
		$_['RW'] = array('name' => __('Rwanda'), 'currency' => array('code' => 'RWF', 'format' => 'RF #.###'), 'units' => 'metric', 'region' => 5);
		$_['BL'] = array('name' => __('Saint Barthélemy'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 1);
		$_['SH'] = array('name' => __('Saint Helena'), 'currency' => array('code' => 'SHP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 5);
		$_['KN'] = array('name' => __('Saint Kitts and Nevis'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['LC'] = array('name' => __('Saint Lucia'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['MF'] = array('name' => __('Saint Martin'), 'currency' => array('code' => 'EUR', 'format' => '€ #,###.##'), 'units' => 'metric', 'region' => 1);
		$_['PM'] = array('name' => __('Saint Pierre and Miquelon'), 'currency' => array('code' => 'EUR', 'format' => '# ###,## €'), 'units' => 'metric', 'region' => 0);
		$_['VC'] = array('name' => __('Saint Vincent'), 'currency' => array('code' => 'XCD', 'format' => 'EC$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['WS'] = array('name' => __('Samoa'), 'currency' => array('code' => 'WST', 'format' => 'WS$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['SM'] = array('name' => __('San Marino'), 'currency' => array('code' => 'EUR', 'format' => '€ #,###.##'), 'units' => 'metric', 'region' => 3);
		$_['ST'] = array('name' => __('São Tomé and Príncipe'), 'currency' => array('code' => 'STD', 'format' => 'Db #,###.##'), 'units' => 'metric', 'region' => 5);
		$_['SA'] = array('name' => __('Saudi Arabia'), 'currency' => array('code' => 'SAR', 'format' => '####,## ر.س'), 'units' => 'metric', 'region' => 4);
		$_['SN'] = array('name' => __('Senegal'), 'currency' => array('code' => 'XOF', 'format' => '# ### CFA'), 'units' => 'metric', 'region' => 5);
		$_['RS'] = array('name' => __('Serbia'), 'currency' => array('code' => 'RSD', 'format' => 'din. #,###'), 'units' => 'metric', 'region' => 3);
		$_['SC'] = array('name' => __('Seychelles'), 'currency' => array('code' => 'SCR', 'format' => '₨#,###'), 'units' => 'metric', 'region' => 5);
		$_['SL'] = array('name' => __('Sierra Leone'), 'currency' => array('code' => 'SLL', 'format' => 'Le #,###.##'), 'units' => 'metric', 'region' => 5);
		$_['SG'] = array('name' => __('Singapore'), 'currency' => array('code' => 'SGD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['SX'] = array('name' => __('Sint Maarten'), 'currency' => array('code' => 'ANG', 'format' => 'ƒ#.###,##'), 'units' => 'metric', 'region' => 1);
		$_['SK'] = array('name' => __('Slovakia'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['SI'] = array('name' => __('Slovenia'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['SB'] = array('name' => __('Solomon Islands'), 'currency' => array('code' => 'SBD', 'format' => 'SI$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['SO'] = array('name' => __('Somalia'), 'currency' => array('code' => 'SOS', 'format' => 'Ssh#,###'), 'units' => 'metric', 'region' => 5);
		$_['ZA'] = array('name' => __('South Africa'), 'currency' => array('code' => 'ZAR', 'format' => 'R# ###,##'), 'units' => 'metric', 'region' => 5);
		$_['GS'] = array('name' => __('South Georgia'), 'currency' => array('code' => 'GBP', 'format' => '£#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['KR'] = array('name' => __('South Korea'), 'currency' => array('code' => 'KRW', 'format' => '₩#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['SS'] = array('name' => __('South Sudan'), 'currency' => array('code' => 'SSP', 'format' => 'SSP #,###.##'), 'units' => 'metric', 'region' => 5);
		$_['ES'] = array('name' => __('Spain'), 'currency' => array('code' => 'EUR', 'format' => '#.###,## €'), 'units' => 'metric', 'region' => 3);
		$_['LK'] = array('name' => __('Sri Lanka'), 'currency' => array('code' => 'LKR', 'format' => 'SL₨ #,###.##'), 'units' => 'metric', 'region' => 6);
		$_['SD'] = array('name' => __('Sudan'), 'currency' => array('code' => 'SDG', 'format' => 'SDG #.###,##'), 'units' => 'metric', 'region' => 5);
		$_['SR'] = array('name' => __('Suriname'), 'currency' => array('code' => 'SRD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['SJ'] = array('name' => __('Svalbard and Jan Mayen'), 'currency' => array('code' => 'NOK', 'format' => 'kr # ###,##'), 'units' => 'metric', 'region' => 3);
		$_['SE'] = array('name' => __('Sweden'), 'currency' => array('code' => 'SEK', 'format' => '# ### ###, kr'), 'units' => 'metric', 'region' => 3);
		$_['SZ'] = array('name' => __('Swaziland'), 'currency' => array('code' => 'SZL', 'format' => 'E# ###,##'), 'units' => 'metric', 'region' => 5);
		$_['CH'] = array('name' => __('Switzerland'), 'currency' => array('code' => 'CHF', 'format' => "CHF #'###.##"), 'units' => 'metric', 'region' => 3);
		$_['SY'] = array('name' => __('Syria'), 'currency' => array('code' => 'SYP', 'format' => '£S#,###.##'), 'units' => 'metric', 'region' => 4);
		$_['TW'] = array('name' => __('Taiwan'), 'currency' => array('code' => 'TWD', 'format' => 'NT$#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['TJ'] = array('name' => __('Tajikistan'), 'currency' => array('code' => 'TJS', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['TZ'] = array('name' => __('Tanzania'), 'currency' => array('code' => 'TZS', 'format' => '#,###.## TSh'), 'units' => 'metric', 'region' => 5);
		$_['TH'] = array('name' => __('Thailand'), 'currency' => array('code' => 'THB', 'format' => '#,###.##฿'), 'units' => 'metric', 'region' => 6);
		$_['TG'] = array('name' => __('Togo'), 'currency' => array('code' => 'XOF', 'format' => 'CFA#,###'), 'units' => 'metric', 'region' => 5);
		$_['TK'] = array('name' => __('Tokelau'), 'currency' => array('code' => 'NZD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['TO'] = array('name' => __('Tonga'), 'currency' => array('code' => 'TOP', 'format' => 'T$ #,###.##'), 'units' => 'metric', 'region' => 7);
		$_['TT'] = array('name' => __('Trinidad and Tobago'), 'currency' => array('code' => 'TTD', 'format' => 'TT$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['TN'] = array('name' => __('Tunisia'), 'currency' => array('code' => 'TND', 'format' => '####,### د.ت'), 'units' => 'metric', 'region' => 5);
		$_['TR'] = array('name' => __('Turkey'), 'currency' => array('code' => 'TRY', 'format' => '#.###,## TL'), 'units' => 'metric', 'region' => 6);
		$_['TM'] = array('name' => __('Turkmenistan'), 'currency' => array('code' => 'TMT', 'format' => '#,###.## m'), 'units' => 'metric', 'region' => 6);
		$_['TC'] = array('name' => __('Turks and Caicos Islands'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 1);
		$_['TV'] = array('name' => __('Tuvalu'), 'currency' => array('code' => 'AUD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['UG'] = array('name' => __('Uganda'), 'currency' => array('code' => 'UGX', 'format' => '#,###.## USh'), 'units' => 'metric', 'region' => 5);
		$_['UA'] = array('name' => __('Ukraine'), 'currency' => array('code' => 'UAH', 'format' => '# ###,## ₴'), 'units' => 'metric', 'region' => 3);
		$_['AE'] = array('name' => __('United Arab Emirates'), 'currency' => array('code' => 'AED', 'format' => 'Dhs. #,###.##'), 'units' => 'metric', 'region' => 4);
		$_['UY'] = array('name' => __('Uruguay'), 'currency' => array('code' => 'UYU', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 2);
		$_['UZ'] = array('name' => __('Uzbekistan'), 'currency' => array('code' => 'UZS', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 6);
		$_['VU'] = array('name' => __('Vanuatu'), 'currency' => array('code' => 'VUV', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 7);
		$_['VA'] = array('name' => __('Vatican City'), 'currency' => array('code' => 'EUR', 'format' => '€#,###.##'), 'units' => 'metric', 'region' => 3);
		$_['VN'] = array('name' => __('Vietnam'), 'currency' => array('code' => 'VND', 'format' => '#.###,## ₫'), 'units' => 'metric', 'region' => 6);
		$_['VE'] = array('name' => __('Venezuela'), 'currency' => array('code' => 'VEF', 'format' => 'Bs. #,###.##'), 'units' => 'metric', 'region' => 2);
		$_['WF'] = array('name' => __('Wallis and Futuna'), 'currency' => array('code' => 'XPF', 'format' => '#,###.##F'), 'units' => 'metric', 'region' => 7);
		$_['EH'] = array('name' => __('Western Sahara'), 'currency' => array('code' => 'MAD', 'format' => '#.###,## درهم'), 'units' => 'metric', 'region' => 5);
		$_['YE'] = array('name' => __('Yemen'), 'currency' => array('code' => 'YER', 'format' => '#.###,## .ر.ي'), 'units' => 'metric', 'region' => 6);
		$_['ZM'] = array('name' => __('Zambia'), 'currency' => array('code' => 'ZMK', 'format' => '#,###.## ZK'), 'units' => 'metric', 'region' => 5);
		$_['ZW'] = array('name' => __('Zimbabwe'), 'currency' => array('code' => 'USD', 'format' => '$#,###.##'), 'units' => 'metric', 'region' => 5);

		return apply_filters('shopp_countries', $_);
	}

	/**
	 * Provides a lookup table of country zones (states/provinces)
	 *
	 * @see wiki ISO_3166-2
	 * @author Jonathan Davis
	 * @since 1.1
	 *
	 * @return array
	 **/
	public static function country_zones () {
		$_ = array();
		$_['AR'] = array();
		$_['AR']['B'] = __('Buenos Aires');
		$_['AR']['K'] = __('Catmarca');
		$_['AR']['H'] = __('Chaco');
		$_['AR']['U'] = __('Chubut');
		$_['AR']['C'] = __('Córdoba');
		$_['AR']['W'] = __('Corrientes');
		$_['AR']['E'] = __('Entre Ríos');
		$_['AR']['P'] = __('Formosa');
		$_['AR']['Y'] = __('Jujuy');
		$_['AR']['L'] = __('La Pampa');
		$_['AR']['F'] = __('La Rioja');
		$_['AR']['M'] = __('Mendoza');
		$_['AR']['N'] = __('Misiones');
		$_['AR']['Q'] = __('Neuquén');
		$_['AR']['R'] = __('Río Negro');
		$_['AR']['A'] = __('Salta');
		$_['AR']['J'] = __('San Juan');
		$_['AR']['D'] = __('San Luis');
		$_['AR']['Z'] = __('Santa Cruz');
		$_['AR']['S'] = __('Santa Fe');
		$_['AR']['G'] = __('Santiago del Estero');
		$_['AR']['V'] = __('Tierra del Fuego');
		$_['AR']['T'] = __('Tucumán');

		$_['AT'] = array();
		$_['AT']['1'] = __('Burgenland');
		$_['AT']['2'] = __('Kärnten');
		$_['AT']['3'] = __('Niederösterreich');
		$_['AT']['4'] = __('Oberösterreich');
		$_['AT']['5'] = __('Salzburg');
		$_['AT']['6'] = __('Steiermark');
		$_['AT']['7'] = __('Tirol');
		$_['AT']['8'] = __('Vorarlberg');
		$_['AT']['9'] = __('Wien');

		$_['AU'] = array();
		$_['AU']['ACT'] = 'Australian Capital Territory';
		$_['AU']['NSW'] = 'New South Wales';
		$_['AU']['NT']  = 'Northern Territory';
		$_['AU']['QLD'] = 'Queensland';
		$_['AU']['SA']  = 'South Australia';
		$_['AU']['TAS'] = 'Tasmania';
		$_['AU']['VIC'] = 'Victoria';
		$_['AU']['WA']  = 'Western Australia';

		$_['BE'] = array();
		$_['BE']['VAN'] = __('Antwerpen');
		$_['BE']['WBR'] = __('Brabant Wallon');
		$_['BE']['BRU'] = __('Brussels Capital');
		$_['BE']['WHT'] = __('Hainaut');
		$_['BE']['WLG'] = __('Liège');
		$_['BE']['VLI'] = __('Limburg');
		$_['BE']['WLX'] = __('Luxembourg');
		$_['BE']['WNA'] = __('Namur');
		$_['BE']['VOV'] = __('Oost-Vlaanderen');
		$_['BE']['VBR'] = __('Vlaams Brabant');
		$_['BE']['VWV'] = __('West-Vlaanderen');

		$_['DE']['BW'] = 'Baden-Württemberg';
		$_['DE']['BY'] = 'Bayern';
		$_['DE']['BE'] = 'Berlin';
		$_['DE']['BB'] = 'Brandenburg';
		$_['DE']['HB'] = 'Bremen';
		$_['DE']['HH'] = 'Hamburg';
		$_['DE']['HE'] = 'Hessen';
		$_['DE']['MV'] = 'Mecklenburg-Vorpommern';
		$_['DE']['NI'] = 'Niedersachsen';
		$_['DE']['NW'] = 'Nordrhein-Westfalen';
		$_['DE']['RP'] = 'Rheinland-Pfalz';
		$_['DE']['SL'] = 'Saarland';
		$_['DE']['SN'] = 'Sachsen';
		$_['DE']['ST'] = 'Sachsen-Anhalt';
		$_['DE']['SH'] = 'Schleswig-Holstein';
		$_['DE']['TH'] = 'Thüringen';

		$_['CA'] = array();
		$_['CA']['AB'] = 'Alberta';
		$_['CA']['BC'] = 'British Columbia';
		$_['CA']['MB'] = 'Manitoba';
		$_['CA']['NB'] = 'New Brunswick';
		$_['CA']['NL'] = 'Newfoundland';
		$_['CA']['NT'] = 'Northwest Territories';
		$_['CA']['NS'] = 'Nova Scotia';
		$_['CA']['NU'] = 'Nunavut';
		$_['CA']['ON'] = 'Ontario';
		$_['CA']['PE'] = 'Prince Edward Island';
		$_['CA']['QC'] = 'Quebec';
		$_['CA']['SK'] = 'Saskatchewan';
		$_['CA']['YT'] = 'Yukon Territory';

		$_['NL'] = array();
		$_['NL']['DR'] = __('Drenthe');
		$_['NL']['FL'] = __('Flevoland');
		$_['NL']['FR'] = __('Friesland');
		$_['NL']['GE'] = __('Gelderland');
		$_['NL']['GR'] = __('Groningen');
		$_['NL']['LI'] = __('Limburg');
		$_['NL']['NB'] = __('Noord-Brabant');
		$_['NL']['NH'] = __('Noord-Holland');
		$_['NL']['OV'] = __('Overijssel');
		$_['NL']['UT'] = __('Utrecht');
		$_['NL']['ZE'] = __('Zeeland');
		$_['NL']['ZH'] = __('Zuid-Holland');

		$_['US'] = array();
		$_['US']['AL'] = 'Alabama';
		$_['US']['AK'] = 'Alaska';
		$_['US']['AZ'] = 'Arizona';
		$_['US']['AR'] = 'Arkansas';
		$_['US']['CA'] = 'California';
		$_['US']['CO'] = 'Colorado';
		$_['US']['CT'] = 'Connecticut';
		$_['US']['DE'] = 'Delaware';
		$_['US']['DC'] = 'District Of Columbia';
		$_['US']['FL'] = 'Florida';
		$_['US']['GA'] = 'Georgia';
		$_['US']['HI'] = 'Hawaii';
		$_['US']['ID'] = 'Idaho';
		$_['US']['IL'] = 'Illinois';
		$_['US']['IN'] = 'Indiana';
		$_['US']['IA'] = 'Iowa';
		$_['US']['KS'] = 'Kansas';
		$_['US']['KY'] = 'Kentucky';
		$_['US']['LA'] = 'Louisiana';
		$_['US']['ME'] = 'Maine';
		$_['US']['MD'] = 'Maryland';
		$_['US']['MA'] = 'Massachusetts';
		$_['US']['MI'] = 'Michigan';
		$_['US']['MN'] = 'Minnesota';
		$_['US']['MS'] = 'Mississippi';
		$_['US']['MO'] = 'Missouri';
		$_['US']['MT'] = 'Montana';
		$_['US']['NE'] = 'Nebraska';
		$_['US']['NV'] = 'Nevada';
		$_['US']['NH'] = 'New Hampshire';
		$_['US']['NJ'] = 'New Jersey';
		$_['US']['NM'] = 'New Mexico';
		$_['US']['NY'] = 'New York';
		$_['US']['NC'] = 'North Carolina';
		$_['US']['ND'] = 'North Dakota';
		$_['US']['OH'] = 'Ohio';
		$_['US']['OK'] = 'Oklahoma';
		$_['US']['OR'] = 'Oregon';
		$_['US']['PA'] = 'Pennsylvania';
		$_['US']['RI'] = 'Rhode Island';
		$_['US']['SC'] = 'South Carolina';
		$_['US']['SD'] = 'South Dakota';
		$_['US']['TN'] = 'Tennessee';
		$_['US']['TX'] = 'Texas';
		$_['US']['UT'] = 'Utah';
		$_['US']['VT'] = 'Vermont';
		$_['US']['VA'] = 'Virginia';
		$_['US']['WA'] = 'Washington';
		$_['US']['WV'] = 'West Virginia';
		$_['US']['WI'] = 'Wisconsin';
		$_['US']['WY'] = 'Wyoming';

		$_['USAF']['AA'] = 'Americas';
		$_['USAF']['AE'] = 'Europe';
		$_['USAF']['AP'] = 'Pacific';

		$_['USAT']['AS'] = 'American Samoa';
		$_['USAT']['GU'] = 'Guam';
		$_['USAT']['MP'] = 'Northern Mariana Islands';
		$_['USAT']['PR'] = 'Puerto Rico';
		$_['USAT']['UM'] = 'US Minor Outlying Islands';
		$_['USAT']['VI'] = 'Virgin Islands';

		return apply_filters('shopp_country_zones', $_);
	}
}