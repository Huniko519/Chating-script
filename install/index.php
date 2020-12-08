<?php
require_once('../config.php');
require_once('../includes/function.php');
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
$install_version = '1.6';
$error = '';
if(isset($_GET['lang']))
	$_POST['lang'] = $_GET['lang'];

if(isset($_POST['lang']))
	require_once('lang/lang_'.$_POST['lang'].'.php');

// Check to see if the script is already installed
/*if(isset($config['installed']))
{
	if($config['version'] == $install_version)
	{
		// Exit the script
		exit('Wchat is already installed.');
	}
	/*else
	{
		header('Location: upgrade_'.$config['version'].'.php');
		exit;
	}
}*/

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
$site_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace ("index.php", "", str_replace ("install/", "", $_SERVER['PHP_SELF']));

// Check that their config file is writtable
if(is_writable('../config.php'))
{
	if(!isset($_POST['lang']))
	{
		$step = 1;
	}
	else
	{
        if(!isset($_POST['PCode']))
        {
            $step = 3;
        }
        else{
            $step = 3;
        }

		if(isset($_POST['DBHost']))
		{
			// Test the connection
            //$conLink = new mysqli($_POST['DBHost'], $_POST['DBUser'], $_POST['DBPass'], $_POST['DBName']);
            //$con = mysqli_connect($_POST['DBHost'], $_POST['DBUser'], $_POST['DBPass']);
            if(mysqli_connect($_POST['DBHost'], $_POST['DBUser'], $_POST['DBPass']))
            {
                if($conLink = mysqli_select_db(mysqli_connect($_POST['DBHost'], $_POST['DBUser'], $_POST['DBPass']), $_POST['DBName']))
				{
					if(isset($_POST['adminuser']))
					{
						if(trim($_POST['adminuser']) == '')
						{
							$step = 4;
						}
						else
						{
                            // Content that will be written to the config file
							$content = "<?php\n";
							$content.= "\$config['db']['host'] = '".addslashes($_POST['DBHost'])."';\n";
							$content.= "\$config['db']['name'] = '".addslashes($_POST['DBName'])."';\n";
							$content.= "\$config['db']['user'] = '".addslashes($_POST['DBUser'])."';\n";
							$content.= "\$config['db']['pass'] = '".addslashes($_POST['DBPass'])."';\n";
							$content.= "\$config['db']['pre'] = '".addslashes($_POST['DBPre'])."';\n";
							$content.= "\n";
							$content.= "\$config['site_title'] = 'Wchat';\n";
							$content.= "\$config['site_url'] = '".addslashes($site_url)."';\n";
                            $content.= "\$config['admin_email'] = '".addslashes($_POST['admin_email'])."';\n";
                            $content.= "\n";
                            $content.= "\$config['tpl_name'] = 'style-light';\n";
                            $content.= "\$config['tpl_color'] = 'green-dark';\n";
                            $content.= "\n";
                            $content.= "\$config['lang'] = 'english';\n";
                            $content.= "\$config['userlangsel'] = '1';\n";
                            $content.= "\n";
                            $content .= "\$config['facebook_app_id'] = '';\n";
                            $content .= "\$config['facebook_app_secret'] = '';\n";
                            $content .= "\$config['google_app_id'] = '';\n";
                            $content .= "\$config['google_app_secret'] = '';\n";
                            $content.= "\n";
                            $content.= "\$config['transfer_filter'] = '".$config['transfer_filter']."';\n";
							$content.= "\$config['version'] = '".$install_version."';\n";
							$content.= "\$config['installed'] = '1';\n";
							$content.= "?>";
						
							// Open the config.php for writting
							$handle = fopen('../config.php', 'w');
							// Write the config file
							fwrite($handle, $content);
							// Close the file
							fclose($handle);

                            // Create connection in MYsqli
                            $con = new mysqli($_POST['DBHost'], $_POST['DBUser'], $_POST['DBPass'], $_POST['DBName']);
                            // Check connection
                            if ($con->connect_error) {
                                die("Connection failed: " . $con->connect_error);
                            }

// Create USER Table

$table_admins = "CREATE TABLE `".addslashes($_POST['DBPre'])."admins` (
  `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` enum('0','1','2') COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `username` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `name` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `joined` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `country` text COLLATE utf8_general_ci,
  `about` text COLLATE utf8_general_ci NOT NULL,
  `sex` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `dob` text COLLATE utf8_general_ci NOT NULL,
  `picname` varchar(255) COLLATE utf8_general_ci NOT NULL, PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

$insert_admin = "INSERT INTO `".addslashes($_POST['DBPre'])."admins` (`id`, `status`, `username`, `password`, `email`, `name`, `joined`, `country`, `about`, `sex`, `dob`, `picname`) VALUES
(1, '0', '".addslashes($_POST['adminuser'])."', '".md5($_POST['adminpass'])."', '".$_POST['admin_email']."', 'Wchat Admin', '2016-09-16 08:46:04', 'Canada', 'Developed with  by Deven Katariya for developers', 'female', '', 'Wchat.jpg')";

$table_countries = "CREATE TABLE `".addslashes($_POST['DBPre'])."countries` (
  `iso` char(2) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `name` varchar(80) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `printable_name` varchar(80) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `iso3` char(3) COLLATE utf8_general_ci DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL, PRIMARY KEY (`iso`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

$insert_country = "INSERT INTO `".addslashes($_POST['DBPre'])."countries` (`iso`, `name`, `printable_name`, `iso3`, `numcode`) VALUES
('AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4),
('AL', 'ALBANIA', 'Albania', 'ALB', 8),
('DZ', 'ALGERIA', 'Algeria', 'DZA', 12),
('AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16),
('AD', 'ANDORRA', 'Andorra', 'AND', 20),
('AO', 'ANGOLA', 'Angola', 'AGO', 24),
('AI', 'ANGUILLA', 'Anguilla', 'AIA', 660),
('AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL),
('AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28),
('AR', 'ARGENTINA', 'Argentina', 'ARG', 32),
('AM', 'ARMENIA', 'Armenia', 'ARM', 51),
('AW', 'ARUBA', 'Aruba', 'ABW', 533),
('AU', 'AUSTRALIA', 'Australia', 'AUS', 36),
('AT', 'AUSTRIA', 'Austria', 'AUT', 40),
('AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31),
('BS', 'BAHAMAS', 'Bahamas', 'BHS', 44),
('BH', 'BAHRAIN', 'Bahrain', 'BHR', 48),
('BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50),
('BB', 'BARBADOS', 'Barbados', 'BRB', 52),
('BY', 'BELARUS', 'Belarus', 'BLR', 112),
('BE', 'BELGIUM', 'Belgium', 'BEL', 56),
('BZ', 'BELIZE', 'Belize', 'BLZ', 84),
('BJ', 'BENIN', 'Benin', 'BEN', 204),
('BM', 'BERMUDA', 'Bermuda', 'BMU', 60),
('BT', 'BHUTAN', 'Bhutan', 'BTN', 64),
('BO', 'BOLIVIA', 'Bolivia', 'BOL', 68),
('BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70),
('BW', 'BOTSWANA', 'Botswana', 'BWA', 72),
('BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL),
('BR', 'BRAZIL', 'Brazil', 'BRA', 76),
('IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL),
('BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96),
('BG', 'BULGARIA', 'Bulgaria', 'BGR', 100),
('BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854),
('BI', 'BURUNDI', 'Burundi', 'BDI', 108),
('KH', 'CAMBODIA', 'Cambodia', 'KHM', 116),
('CM', 'CAMEROON', 'Cameroon', 'CMR', 120),
('CA', 'CANADA', 'Canada', 'CAN', 124),
('CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132),
('KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136),
('CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140),
('TD', 'CHAD', 'Chad', 'TCD', 148),
('CL', 'CHILE', 'Chile', 'CHL', 152),
('CN', 'CHINA', 'China', 'CHN', 156),
('CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL),
('CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL),
('CO', 'COLOMBIA', 'Colombia', 'COL', 170),
('KM', 'COMOROS', 'Comoros', 'COM', 174),
('CG', 'CONGO', 'Congo', 'COG', 178),
('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180),
('CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184),
('CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188),
('CI', 'COTE D''IVOIRE', 'Cote D''Ivoire', 'CIV', 384),
('HR', 'CROATIA', 'Croatia', 'HRV', 191),
('CU', 'CUBA', 'Cuba', 'CUB', 192),
('CY', 'CYPRUS', 'Cyprus', 'CYP', 196),
('CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203),
('DK', 'DENMARK', 'Denmark', 'DNK', 208),
('DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262),
('DM', 'DOMINICA', 'Dominica', 'DMA', 212),
('DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214),
('EC', 'ECUADOR', 'Ecuador', 'ECU', 218),
('EG', 'EGYPT', 'Egypt', 'EGY', 818),
('SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222),
('GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226),
('ER', 'ERITREA', 'Eritrea', 'ERI', 232),
('EE', 'ESTONIA', 'Estonia', 'EST', 233),
('ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231),
('FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238),
('FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234),
('FJ', 'FIJI', 'Fiji', 'FJI', 242),
('FI', 'FINLAND', 'Finland', 'FIN', 246),
('FR', 'FRANCE', 'France', 'FRA', 250),
('GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254),
('PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258),
('TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL),
('GA', 'GABON', 'Gabon', 'GAB', 266),
('GM', 'GAMBIA', 'Gambia', 'GMB', 270),
('GE', 'GEORGIA', 'Georgia', 'GEO', 268),
('DE', 'GERMANY', 'Germany', 'DEU', 276),
('GH', 'GHANA', 'Ghana', 'GHA', 288),
('GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292),
('GR', 'GREECE', 'Greece', 'GRC', 300),
('GL', 'GREENLAND', 'Greenland', 'GRL', 304),
('GD', 'GRENADA', 'Grenada', 'GRD', 308),
('GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312),
('GU', 'GUAM', 'Guam', 'GUM', 316),
('GT', 'GUATEMALA', 'Guatemala', 'GTM', 320),
('GN', 'GUINEA', 'Guinea', 'GIN', 324),
('GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624),
('GY', 'GUYANA', 'Guyana', 'GUY', 328),
('HT', 'HAITI', 'Haiti', 'HTI', 332),
('HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL),
('VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336),
('HN', 'HONDURAS', 'Honduras', 'HND', 340),
('HK', 'HONG KONG', 'Hong Kong', 'HKG', 344),
('HU', 'HUNGARY', 'Hungary', 'HUN', 348),
('IS', 'ICELAND', 'Iceland', 'ISL', 352),
('IN', 'INDIA', 'India', 'IND', 356),
('ID', 'INDONESIA', 'Indonesia', 'IDN', 360),
('IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364),
('IQ', 'IRAQ', 'Iraq', 'IRQ', 368),
('IE', 'IRELAND', 'Ireland', 'IRL', 372),
('IL', 'ISRAEL', 'Israel', 'ISR', 376),
('IT', 'ITALY', 'Italy', 'ITA', 380),
('JM', 'JAMAICA', 'Jamaica', 'JAM', 388),
('JP', 'JAPAN', 'Japan', 'JPN', 392),
('JO', 'JORDAN', 'Jordan', 'JOR', 400),
('KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398),
('KE', 'KENYA', 'Kenya', 'KEN', 404),
('KI', 'KIRIBATI', 'Kiribati', 'KIR', 296),
('KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF', 'Korea, Democratic People''s Republic of', 'PRK', 408),
('KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410),
('KW', 'KUWAIT', 'Kuwait', 'KWT', 414),
('KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417),
('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'Lao People''s Democratic Republic', 'LAO', 418),
('LV', 'LATVIA', 'Latvia', 'LVA', 428),
('LB', 'LEBANON', 'Lebanon', 'LBN', 422),
('LS', 'LESOTHO', 'Lesotho', 'LSO', 426),
('LR', 'LIBERIA', 'Liberia', 'LBR', 430),
('LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434),
('LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438),
('LT', 'LITHUANIA', 'Lithuania', 'LTU', 440),
('LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442),
('MO', 'MACAO', 'Macao', 'MAC', 446),
('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807),
('MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450),
('MW', 'MALAWI', 'Malawi', 'MWI', 454),
('MY', 'MALAYSIA', 'Malaysia', 'MYS', 458),
('MV', 'MALDIVES', 'Maldives', 'MDV', 462),
('ML', 'MALI', 'Mali', 'MLI', 466),
('MT', 'MALTA', 'Malta', 'MLT', 470),
('MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584),
('MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474),
('MR', 'MAURITANIA', 'Mauritania', 'MRT', 478),
('MU', 'MAURITIUS', 'Mauritius', 'MUS', 480),
('YT', 'MAYOTTE', 'Mayotte', NULL, NULL),
('MX', 'MEXICO', 'Mexico', 'MEX', 484),
('FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583),
('MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498),
('MC', 'MONACO', 'Monaco', 'MCO', 492),
('MN', 'MONGOLIA', 'Mongolia', 'MNG', 496),
('MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500),
('MA', 'MOROCCO', 'Morocco', 'MAR', 504),
('MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508),
('MM', 'MYANMAR', 'Myanmar', 'MMR', 104),
('NA', 'NAMIBIA', 'Namibia', 'NAM', 516),
('NR', 'NAURU', 'Nauru', 'NRU', 520),
('NP', 'NEPAL', 'Nepal', 'NPL', 524),
('NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528),
('AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530),
('NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540),
('NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554),
('NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558),
('NE', 'NIGER', 'Niger', 'NER', 562),
('NG', 'NIGERIA', 'Nigeria', 'NGA', 566),
('NU', 'NIUE', 'Niue', 'NIU', 570),
('NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574),
('MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580),
('NO', 'NORWAY', 'Norway', 'NOR', 578),
('OM', 'OMAN', 'Oman', 'OMN', 512),
('PK', 'PAKISTAN', 'Pakistan', 'PAK', 586),
('PW', 'PALAU', 'Palau', 'PLW', 585),
('PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL),
('PA', 'PANAMA', 'Panama', 'PAN', 591),
('PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598),
('PY', 'PARAGUAY', 'Paraguay', 'PRY', 600),
('PE', 'PERU', 'Peru', 'PER', 604),
('PH', 'PHILIPPINES', 'Philippines', 'PHL', 608),
('PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612),
('PL', 'POLAND', 'Poland', 'POL', 616),
('PT', 'PORTUGAL', 'Portugal', 'PRT', 620),
('PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630),
('QA', 'QATAR', 'Qatar', 'QAT', 634),
('RE', 'REUNION', 'Reunion', 'REU', 638),
('RO', 'ROMANIA', 'Romania', 'ROM', 642),
('RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643),
('RW', 'RWANDA', 'Rwanda', 'RWA', 646),
('SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654),
('KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659),
('LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662),
('PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666),
('VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670),
('WS', 'SAMOA', 'Samoa', 'WSM', 882),
('SM', 'SAN MARINO', 'San Marino', 'SMR', 674),
('ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678),
('SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682),
('SN', 'SENEGAL', 'Senegal', 'SEN', 686),
('CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL),
('SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690),
('SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694),
('SG', 'SINGAPORE', 'Singapore', 'SGP', 702),
('SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703),
('SI', 'SLOVENIA', 'Slovenia', 'SVN', 705),
('SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90),
('SO', 'SOMALIA', 'Somalia', 'SOM', 706),
('ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710),
('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL),
('ES', 'SPAIN', 'Spain', 'ESP', 724),
('LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144),
('SD', 'SUDAN', 'Sudan', 'SDN', 736),
('SR', 'SURINAME', 'Suriname', 'SUR', 740),
('SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744),
('SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748),
('SE', 'SWEDEN', 'Sweden', 'SWE', 752),
('CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756),
('SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760),
('TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158),
('TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762),
('TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834),
('TH', 'THAILAND', 'Thailand', 'THA', 764),
('TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL),
('TG', 'TOGO', 'Togo', 'TGO', 768),
('TK', 'TOKELAU', 'Tokelau', 'TKL', 772),
('TO', 'TONGA', 'Tonga', 'TON', 776),
('TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780),
('TN', 'TUNISIA', 'Tunisia', 'TUN', 788),
('TR', 'TURKEY', 'Turkey', 'TUR', 792),
('TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795),
('TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796),
('TV', 'TUVALU', 'Tuvalu', 'TUV', 798),
('UG', 'UGANDA', 'Uganda', 'UGA', 800),
('UA', 'UKRAINE', 'Ukraine', 'UKR', 804),
('AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784),
('GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826),
('US', 'UNITED STATES', 'United States', 'USA', 840),
('UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL),
('UY', 'URUGUAY', 'Uruguay', 'URY', 858),
('UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860),
('VU', 'VANUATU', 'Vanuatu', 'VUT', 548),
('VE', 'VENEZUELA', 'Venezuela', 'VEN', 862),
('VN', 'VIET NAM', 'Viet Nam', 'VNM', 704),
('VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92),
('VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850),
('WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876),
('EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732),
('YE', 'YEMEN', 'Yemen', 'YEM', 887),
('ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894),
('ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716)";

$table_messages = "CREATE TABLE `".addslashes($_POST['DBPre'])."messages` (
  `message_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_id` varchar(40) NOT NULL DEFAULT '',
  `to_id` varchar(50) NOT NULL DEFAULT '',
  `from_uname` varchar(225) NOT NULL DEFAULT '',
  `to_uname` varchar(255) NOT NULL DEFAULT '',
  `message_content` longtext NOT NULL,
  `message_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recd` tinyint(1) NOT NULL DEFAULT '0',
  `seen` enum('0','1') NOT NULL DEFAULT '0',
  `message_type` varchar(255) NOT NULL DEFAULT '', PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1";

$table_userdata = "CREATE TABLE `".addslashes($_POST['DBPre'])."userdata` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `status` enum('0','1','2') COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `username` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `name` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `joined` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `country` text COLLATE utf8_general_ci,
  `about` text COLLATE utf8_general_ci NOT NULL,
  `sex` varchar(255) COLLATE utf8_general_ci NOT NULL,
  `dob` text COLLATE utf8_general_ci NOT NULL,
  `picname` varchar(255) COLLATE utf8_general_ci NOT NULL DEFAULT 'avatar_default.png',
  `oauth_provider` enum('','facebook','google','twitter') CHARACTER SET utf8 NOT NULL,
  `oauth_uid` varchar(100) CHARACTER SET utf8 NOT NULL,
  `oauth_link` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

$insert_userdata = "INSERT INTO `".addslashes($_POST['DBPre'])."userdata` (`id`, `username`, `password`, `email`, `name`, `joined`, `country`, `about`, `sex`, `dob`, `skype`, `facebook`, `twitter`, `googleplus`, `instagram`, `picname`, `online`, `last_active_timestamp`) VALUES
(1, 'Wchat', '".md5("1234")."', 'bylancertheme@gmail.com', 'Wchatdeveloper', '2016-05-14 08:46:04', 'Canada', 'Developed with  by Deven Katariya for developers', 'female', '', 'Wchat.jpg')";


                            $createTablemsg = array();


                            if ($con->query($table_admins) === TRUE) {
                                $con->query($insert_admin);
                                $createTablemsg[] = "<span style='color: #43dc43;'>Table ".$_POST['DBPre']."admins created successfully </span></br>";
                            } else {
                                $createTablemsg[] = "<span style='color: #dc4366;'>Error creating table: " . $con->error ." </span></br>";
                            }

                            if ($con->query($table_countries) === TRUE) {
                                $con->query($insert_country);
                                $createTablemsg[] = "<span style='color: #43dc43;'>Table ".$_POST['DBPre']."countries created successfully </span></br>";
                            } else {
                                $createTablemsg[] = "<span style='color: #dc4366;'>Error creating table: " . $con->error ." </span></br>";
                            }

                            if ($con->query($table_messages) === TRUE) {
                                $createTablemsg[] = "<span style='color: #43dc43;'>Table ".$_POST['DBPre']."messages created successfully </span></br>";
                            } else {
                                $createTablemsg[] = "<span style='color: #dc4366;'>Error creating table: " . $con->error." </span></br>";
                            }

                            if ($con->query($table_userdata) === TRUE) {
                                $con->query($insert_userdata);
                                $createTablemsg[] = "<span style='color: #43dc43;'>Table ".$_POST['DBPre']."userdata created successfully </span></br>";
                            } else {
                                $createTablemsg[] = "<span style='color: #dc4366;'>Error creating table: " . $con->error ." </span></br>";
                            }

                            $con->close();

                            $step = 5;
						}
					}
					else
					{
						$step = 4;
					}
				}
				else
				{

					$error_number = mysqli_connect_errno();
				
					if($error_number == '1044')
					{
						$error = $lang['ERROR1044'];
					}
					elseif($error_number == '1046')
					{
						$error = $lang['ERROR1046'];
					}
					elseif($error_number = '1049')
					{
						$error = $lang['ERROR1049'];
					}
					else
					{
						$error = mysqli_connect_error().' - '.$error_number;
					}
					$step = 3;
				}
			}
			else
			{
				$error_number = mysqli_connect_error();
			
				if($error_number == '1045')
				{
					$error = $lang['ERROR1045'];
				}
				elseif($error_number == '2005')
				{
					$error = $lang['ERROR2005'];
				}
				else
				{
					$error = mysqli_connect_error().' - '.$error_number;
				}
				$step = 3;
			}
		}
	}
}
else
{
	$step = 0;
	$error = $error.'Could not write to your config.php file.<br><br>Please check that you have set the chmod/permisions to 0777';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Wchat Installation</title>
<link href="style.css" rel="stylesheet">
</head>
<body>

<?php
if($step == 0)
{
?>
<table width="500"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="500%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><span class="style1">Wchat Installation : Error</span></td>
        <td align="right" valign="bottom">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><br></td>
  </tr>
  <tr>
    <td>
	<br><br>
	<span class="error"><?php echo $error;?></span><br><br><br>
	<a href="index.php">Click here</a> once you have corrected this.<br><br><br><br><bR>
    </td>
  </tr>
  <tr>
    <td><div align="center"><span class="style5">&copy; 2008 <a>Byweb.online</a></span></div></td>
  </tr>
</table>

<?php
}
elseif($step == 1)
{
?>

<div class="container">
    <table border="0" align="center" cellpadding="10" cellspacing="3" align="center">
        <tr>
            <td>
                <table border="0" cellspacing="10" cellpadding="3" align="center">
                    <tr>
                        <td><span class="style1">Wchat Installation - Step: 1-4</span></td>
                        <td align="right" valign="bottom">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>Please select the language you would like Wchat to use:<br><small style="color:#FF0000;">*Some parts of the installation may not be in your chosen language</small><Br><br>

                <table  border="0" cellspacing="0" cellpadding="10">
                    <tr>
                        <td width="33%" height="140" align="left">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center"><a href="index.php?lang=english"><img src="images/flag_en.gif" alt="English" width="130" height="87" vspace="2" border="0"></a><br><a href="index.php?lang=english">English</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="33%" height="140" align="left">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"><a href="index.php?lang=german"><img src="images/flag_german.gif" alt="Deutsch" width="130" height="87" vspace="2" border="0"></a><br><a href="index.php?lang=german">Deutsch</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="33%" height="140" align="left">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"><a href="index.php?lang=french"><img src="images/flag_french.gif" alt="French" width="130" height="87" vspace="2" border="0"></a><br><a href="index.php?lang=french">Fran&ccedil;ais</a></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="33%" height="140" align="left">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"><a href="index.php?lang=spanish"><img src="images/flag_spanish.gif" alt="Espanol" width="130" height="87" vspace="2" border="0"></a><br><a href="index.php?lang=spanish">Espa&ntilde;ol</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="33%" height="140" align="left">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"><a href="index.php?lang=italian"><img src="images/flag_italian.gif" alt="Italian" width="130" height="87" vspace="2" border="0"></a><br><a href="index.php?lang=italian">Italian</a></td>
                                </tr>
                            </table>
                        </td>
                        <td width="33%" height="140" align="left"></td>
                    </tr>
                </table>
            <br>
            <br>
            </td>
        </tr>
        <tr>
            <td><div align="center"><span class="style5">&copy; <?php echo date("Y"); ?> <a>Byweb.online</a></span></div></td>
        </tr>
    </table>
</div>

<?php
}
elseif($step == 2)
{
?>

<div class="container">
    <table  border="0" align="center" cellpadding="10" cellspacing="3" align="center">
        <tr>
            <td>
                <table border="0" cellspacing="10" cellpadding="3" align="center">
                    <tr>
                        <td><span class="style1">Wchat Installation - Step: 2-4</span></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <form name="form1" method="post" action="index.php" style="padding:0px;margin:0px;">
                    <table border="0" cellspacing="10" cellpadding="3" align="center">
                        <tr>
                            <td align="center">Enter wchat envato purchase code.</td>
                        <tr/>
                        <tr>
                            <td align="center">
                                <?php
                                if($error != '')
                                {
                                    echo '<span class="byMsg byMsgError">! '.$error.'</span><br><Br>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <table border="0" cellspacing="0" cellpadding="3" align="center">
                        <tr>
                            <td><span class="style12">Purchase Code: </span></td>
                            <td><input name="PCode" type="text" id="PCode" value="<?php if(isset($_POST['PCode'])){ echo $_POST['PCode']; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('Wchat Purchase code');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td><span class="style12">Buyer Email: </span></td>
                            <td><input name="buyer_email" type="text" id="buyer_email" value="<?php if(isset($_POST['buyer_email'])){ echo $_POST['buyer_email']; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('Wchat Buyer Email');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input class="coffe button" name="Submit" type="submit" value="Next &gt;&gt;"></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <br><br><br>
                    <input name="lang" type="hidden" value="<?php echo $_POST['lang'];?>">
                </form>
            </td>
        </tr>
        <tr>
            <td><div align="center"><span class="style5">&copy; <?php echo date("Y"); ?> <a>Byweb.online</a>  / <a target="_blank" href="https://bit.ly/2GoaF65">Free script`s</a></span></div></td>
        </tr>
    </table>
</div>

<?php
}
elseif($step == 3)
{
?>

<div class="container">
    <table  border="0" align="center" cellpadding="10" cellspacing="3" align="center">
        <tr>
            <td>
                <table border="0" cellspacing="10" cellpadding="3" align="center">
                    <tr>
                        <td><span class="style1">Wchat Installation Step: 3-4</span></td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <form name="form1" method="post" action="index.php" style="padding:0px;margin:0px;">
                    <table border="0" cellspacing="10" cellpadding="3" align="center">
                        <tr>
                            <td align="center"><?php echo $lang['MYSQLFILL']; ?></td>
                        <tr/>
                        <tr>
                            <td align="center">
                                <?php
                                if($error != '')
                                {
                                    echo '<span class="byMsg byMsgError">! '.$error.'</span><br><Br>';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <table border="0" cellspacing="0" cellpadding="3" align="center">
                        <tr>
                            <td><span class="style12"><?php echo $lang['MYSQLHOST'];?>: </span></td>
                            <td><input name="DBHost" type="text" id="DBHost" value="<?php if(isset($_POST['DBHost'])){ echo $_POST['DBHost']; } else { echo 'localhost'; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['HOSTHELP'];?>');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td><span class="style12"><?php echo $lang['MYSQLUSER'];?>:</span></td>
                            <td><input name="DBUser" type="text" id="DBUser" value="<?php if(isset($_POST['DBUser'])){ echo $_POST['DBUser']; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['USERHELP'];?>');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td><span class="style12"><?php echo $lang['MYSQLPASS'];?>:</span></td>
                            <td><input name="DBPass" type="password" id="DBPass" value="<?php if(isset($_POST['DBPass'])){ echo $_POST['DBPass']; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['PASSHELP'];?>');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td><span class="style12"><?php echo $lang['MYSQLNAME'];?>: </span></td>
                            <td><input name="DBName" type="text" id="DBName" value="<?php if(isset($_POST['DBName'])){ echo $_POST['DBName']; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['NAMEHELP'];?>');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td><span class="style12"><?php echo $lang['MYSQLPRE'];?>: </span></td>
                            <td><input name="DBPre" type="text" id="DBPre" value="<?php if(isset($_POST['DBPre'])){ echo $_POST['DBPre']; } else { echo 'lance_'; } ?>"></td>
                            <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['PREHELP'];?>');">(?)</a> </span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input class="coffe button" name="Submit" type="submit" value="Next &gt;&gt;"></td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <br><br><br>
                     <input name="lang" type="hidden" value="<?php echo $_POST['lang'];?>">
                </form>
            </td>
        </tr>
        <tr>
            <td><div align="center"><span class="style5">&copy; <?php echo date("Y"); ?> <a>Byweb.online</a> / <a target="_blank" href="https://bit.ly/2GoaF65">Free script`s</a></span></div></td>
        </tr>
    </table>
</div>

<?php
}
elseif($step == '4')
{
?>

<div class="container">
    <table border="0" align="center" cellpadding="10" cellspacing="3" align="center">
        <tr>
            <td>
                <table border="0" cellspacing="10" cellpadding="3" align="center">
                    <tr>
                        <td><span class="style1">Wchat Installation Step: 4-4</span></td>
                        <td align="right" valign="bottom">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <form name="form1" method="post" action="index.php" style="padding:0px;margin:0px;">
                <?php echo $lang['ADMFILL'];?>
                <br><br><br>
                <table border="0" cellspacing="0" cellpadding="3" align="center">
                    <tr>
                        <td><span class="style12">Admin Email: </span></td>
                        <td><input name="admin_email" type="text" id="admin_email" value="<?php if(isset($_POST['admin_email'])){ echo $_POST['admin_email']; } ?>"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><span class="style12"><?php echo $lang['ADMUSER'];?>: </span></td>
                        <td><input name="adminuser" type="text" id="adminuser" value="<?php if(isset($_POST['adminuser'])){ echo $_POST['adminuser']; } ?>"></td>
                        <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['ADMUSERHELP'];?>');">(?)</a> </span></td>
                    </tr>
                    <tr>
                        <td><span class="style12"><?php echo $lang['ADMPASS'];?>: </span></td>
                        <td><input name="adminpass" type="password" id="adminpass" value="<?php if(isset($_POST['adminpass'])){ echo $_POST['adminpass']; } ?>"></td>
                        <td><span class="style12">&nbsp;<a href="javascript:alert('<?php echo $lang['ADMPASSHELP'];?>');">(?)</a> </span></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input class="coffe button" name="Submit" type="submit" value="<?php echo $lang['NEXT'];?>"></td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <br><br>
                <input name="site_url" type="hidden" id="site_url" value="<?php echo $_POST['site_url'];?>">
                <input name="DBHost" type="hidden" id="DBHost" value="<?php echo $_POST['DBHost'];?>">
                <input name="DBName" type="hidden" id="DBName" value="<?php echo $_POST['DBName'];?>">
                <input name="DBUser" type="hidden" id="DBUser" value="<?php echo $_POST['DBUser'];?>">
                <input name="DBPass" type="hidden" id="DBPass" value="<?php echo $_POST['DBPass'];?>">
                <input name="DBPre" type="hidden" id="DBPre" value="<?php echo $_POST['DBPre'];?>">

                <input name="lang" type="hidden" value="<?php echo $_POST['lang'];?>">
                </form>
            </td>
        </tr>
        <tr>
            <td><div align="center"><span class="style5">&copy; <?php echo date("Y"); ?> <a>Byweb.online</a>  / <a target="_blank" href="https://bit.ly/2GoaF65">Free script`s</a></span></div></td>
        </tr>
    </table>
</div>

<?php
}
elseif($step == '5')
{
?>

<div class="container">
    <table border="0" align="center" cellpadding="10" cellspacing="3" align="center">
        <tr>
            <td>
                <table border="0" cellspacing="10" cellpadding="3" align="center">
                    <tr>
                        <td><span class="style1">Wchat Installation</span></td>
                        <td align="right" valign="bottom">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
            <?php
            if (is_array($createTablemsg)) {
                foreach ($createTablemsg as $value) {
                    echo $value;
                }
            }
            ?>
            </td>
        </tr>
        <tr><td>Thank you for installing Wchat, please use the links below:</td></tr>
        <tr><td>- <a href="../index.php">Front End</a> <br>- <a href="../admin/">Admin</a><br></td></tr>
        <tr>
            <td><div align="center"><span class="style5">&copy; <?php echo date("Y"); ?> <a>Byweb.online</a>  / <a target="_blank" href="https://bit.ly/2GoaF65">Free script`s</a></span></div></td>
        </tr>
    </table>
</div>

<?php
}
?>

</body>
</html>