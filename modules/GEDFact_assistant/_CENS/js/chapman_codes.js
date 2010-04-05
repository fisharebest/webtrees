/**
 * Census Assistant module for phpGedView
 *
 * Chapman Code information
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Census Assistant
 * @version $Id$
 * @author Brian Holland (windmillway)
 */
 
 function getChapmanCode(location) {
	// Countries
		   if (location == 'All')										 { var ChapmanCode = 'ALL';
	} else if (location == 'Anywhere')									 { var ChapmanCode = 'ANY';
	} else if (location == 'At Sea')									 { var ChapmanCode = 'SEA';
	} else if (location == 'Unknown')									 { var ChapmanCode = 'UNK';
	} else if (location == 'UNK')										 { var ChapmanCode = '-';
	} else if (location == '')											 { var ChapmanCode = '-';

	} else if (location == 'Afghanistan')								 { var ChapmanCode = 'AFG';
	} else if (location == 'Albania')									 { var ChapmanCode = 'ALB';
	} else if (location == 'Algeria')									 { var ChapmanCode = 'DZA';
	} else if (location == 'American Samoa')							 { var ChapmanCode = 'ASM';
	} else if (location == 'Andorra')									 { var ChapmanCode = 'AND';
	} else if (location == 'Angola')									 { var ChapmanCode = 'AGO';
	} else if (location == 'Anguilla')									 { var ChapmanCode = 'AIA';
	} else if (location == 'Antarctica')								 { var ChapmanCode = 'ATA';
	} else if (location == 'Antigua and Barbuda')						 { var ChapmanCode = 'ATG';
	} else if (location == 'Argentina')									 { var ChapmanCode = 'ARG';
	} else if (location == 'Armenia')									 { var ChapmanCode = 'ARM';
	} else if (location == 'Aruba')										 { var ChapmanCode = 'ABW';
	} else if (location == 'Australia')									 { var ChapmanCode = 'AUS';
	} else if (location == 'Austria')									 { var ChapmanCode = 'AUT';
	} else if (location == 'Azerbaijan')								 { var ChapmanCode = 'AZE';
 	} else if (location == 'Bahamas')									 { var ChapmanCode = 'BHS';
	} else if (location == 'Bahrain')									 { var ChapmanCode = 'BHR';
	} else if (location == 'Bangladesh')								 { var ChapmanCode = 'BGD';
	} else if (location == 'Barbados')									 { var ChapmanCode = 'BRB';
	} else if (location == 'Belarus')									 { var ChapmanCode = 'BLR';
	} else if (location == 'Belgium')									 { var ChapmanCode = 'BEL';
	} else if (location == 'Belize')									 { var ChapmanCode = 'BLZ';
	} else if (location == 'Benin')										 { var ChapmanCode = 'BEN';
	} else if (location == 'Bermuda')									 { var ChapmanCode = 'BMU';
	} else if (location == 'Bhutan')									 { var ChapmanCode = 'BTN';
	} else if (location == 'Bolivia')									 { var ChapmanCode = 'BOL';
	} else if (location == 'Bosnia and Herzegovina')					 { var ChapmanCode = 'BIH';
	} else if (location == 'Botswana')									 { var ChapmanCode = 'BWA';
	} else if (location == 'Bouvet Island')								 { var ChapmanCode = 'BVT';
	} else if (location == 'Brazil')									 { var ChapmanCode = 'BRA';
	} else if (location == 'British Indian Ocean Territory')			 { var ChapmanCode = 'IOT';
	} else if (location == 'British West Indies')						 { var ChapmanCode = 'BWI';
	} else if (location == 'Brunei Darussalam')							 { var ChapmanCode = 'BRN';
	} else if (location == 'Bulgaria')									 { var ChapmanCode = 'BGR';
	} else if (location == 'Burkina Faso')								 { var ChapmanCode = 'BFA';
	} else if (location == 'Burma')										 { var ChapmanCode = 'MMR';
	} else if (location == 'Burundi')									 { var ChapmanCode = 'BDI';
	} else if (location == 'Cambodia')									 { var ChapmanCode = 'KHM';
	} else if (location == 'Cameroon')									 { var ChapmanCode = 'CMR';
	} else if (location == 'Canada')									 { var ChapmanCode = 'CAN';
	} else if (location == 'Cape Verde')								 { var ChapmanCode = 'CPV';
	} else if (location == 'Cayman Islands')							 { var ChapmanCode = 'CYM';
	} else if (location == 'Central African Republic')					 { var ChapmanCode = 'CAF';
	} else if (location == 'Central America')							 { var ChapmanCode = 'CAM';
	} else if (location == 'Chad')										 { var ChapmanCode = 'TCD';
	} else if (location == 'Channel Islands')							 { var ChapmanCode = 'CHI';
	} else if (location == 'Chile')										 { var ChapmanCode = 'CHL';
	} else if (location == 'China')										 { var ChapmanCode = 'CHN';
	} else if (location == 'Christmas Island')							 { var ChapmanCode = 'CXR';
	} else if (location == 'Cocos (Keeling) Islands')					 { var ChapmanCode = 'CCK';
	} else if (location == 'Colombia')									 { var ChapmanCode = 'COL';
	} else if (location == 'Comoros')									 { var ChapmanCode = 'COM';
	} else if (location == 'Congo')										 { var ChapmanCode = 'COG';
	} else if (location == 'Cook Islands')								 { var ChapmanCode = 'COK';
	} else if (location == 'Costa Rica')								 { var ChapmanCode = 'CRI';
	} else if (location == 'Cote D\'ivoire')							 { var ChapmanCode = 'CIV';
	} else if (location == 'Croatia')									 { var ChapmanCode = 'HRV';
	} else if (location == 'Hrvatska)')									 { var ChapmanCode = 'HRV';
	} else if (location == 'Cuba')										 { var ChapmanCode = 'CUB';
	} else if (location == 'Cyprus')									 { var ChapmanCode = 'CYP';
	} else if (location == 'Czechoslovakia')							 { var ChapmanCode = 'CSK';
	} else if (location == 'Czech Republic')							 { var ChapmanCode = 'CZE';
	} else if (location == 'Denmark')									 { var ChapmanCode = 'DNK';
	} else if (location == 'Deutschland')								 { var ChapmanCode = 'DEU';
	} else if (location == 'Djibouti')									 { var ChapmanCode = 'DJI';
	} else if (location == 'Dominica')									 { var ChapmanCode = 'DMA';
	} else if (location == 'Dominican Republic')						 { var ChapmanCode = 'DOM';
	} else if (location == 'Dutch New Guinea')							 { var ChapmanCode = 'DNG';
	} else if (location == 'East Indies')								 { var ChapmanCode = 'IDN';
	} else if (location == 'East Timor')								 { var ChapmanCode = 'TLS';
	} else if (location == 'Ecuador')									 { var ChapmanCode = 'ECU';
	} else if (location == 'Egypt')										 { var ChapmanCode = 'EGY';
	} else if (location == 'Eire')										 { var ChapmanCode = 'EIR';
	} else if (location == 'El Salvador')								 { var ChapmanCode = 'SLV';
	} else if (location == 'England')									 { var ChapmanCode = 'ENG';
	} else if (location == 'Equatorial Guinea')							 { var ChapmanCode = 'GNQ';
	} else if (location == 'Eritrea')									 { var ChapmanCode = 'ERI';
	} else if (location == 'Estonia')									 { var ChapmanCode = 'EST';
	} else if (location == 'Ethiopia')									 { var ChapmanCode = 'ETH';
	} else if (location == 'Falkland Islands')							 { var ChapmanCode = 'FLK';
	} else if (location == 'Malvinas')									 { var ChapmanCode = 'FLK';
	} else if (location == 'Faroe Islands')								 { var ChapmanCode = 'FRO';
	} else if (location == 'Fiji')										 { var ChapmanCode = 'FJI';
	} else if (location == 'Finland')									 { var ChapmanCode = 'FIN';
	} else if (location == 'France')									 { var ChapmanCode = 'FRA';
	} else if (location == 'France, Metropolitan')						 { var ChapmanCode = 'FXX';
	} else if (location == 'French Guiana')								 { var ChapmanCode = 'GUF';
	} else if (location == 'French Polynesia')							 { var ChapmanCode = 'PYF';
	} else if (location == 'French Southern Territories')				 { var ChapmanCode = 'ATF';
	} else if (location == 'Gabon')										 { var ChapmanCode = 'GAB';
	} else if (location == 'Gambia')									 { var ChapmanCode = 'GMB';
	} else if (location == 'Georgia')									 { var ChapmanCode = 'GEO';
	} else if (location == 'Germany')									 { var ChapmanCode = 'DEU';
	} else if (location == 'Ghana')										 { var ChapmanCode = 'GHA';
	} else if (location == 'Gibraltar')									 { var ChapmanCode = 'GIB';
	} else if (location == 'Great Britain')								 { var ChapmanCode = 'GBR';
	} else if (location == 'Greece')									 { var ChapmanCode = 'GRC';
	} else if (location == 'Greenland')									 { var ChapmanCode = 'GRL';
	} else if (location == 'Grenada')									 { var ChapmanCode = 'GRD';
	} else if (location == 'Guadeloupe')								 { var ChapmanCode = 'GLP';
	} else if (location == 'Guam')										 { var ChapmanCode = 'GUM';
	} else if (location == 'Guatemala')									 { var ChapmanCode = 'GTM';
	} else if (location == 'Guinea')									 { var ChapmanCode = 'GIN';
	} else if (location == 'Guinea-Bissau')								 { var ChapmanCode = 'GNB';
	} else if (location == 'Guyana')									 { var ChapmanCode = 'GUY';
	} else if (location == 'Haiti')										 { var ChapmanCode = 'HTI';
	} else if (location == 'Heard Island & McDonald Islands')			 { var ChapmanCode = 'HMD';
	} else if (location == 'Holland')									 { var ChapmanCode = 'HOL';
	} else if (location == 'Honduras')									 { var ChapmanCode = 'HND';
	} else if (location == 'Hong Kong')									 { var ChapmanCode = 'HKG';
	} else if (location == 'Hungary')									 { var ChapmanCode = 'HUN';
	} else if (location == 'Iceland')									 { var ChapmanCode = 'ISL';
	} else if (location == 'India')										 { var ChapmanCode = 'IND';
	} else if (location == 'Indonesia')									 { var ChapmanCode = 'IDN';
	} else if (location == 'Iran')										 { var ChapmanCode = 'IRN';
	} else if (location == 'Islamic Republic of Iran')					 { var ChapmanCode = 'IRN';
	} else if (location == 'Iraq')										 { var ChapmanCode = 'IRQ';
	} else if (location == 'Ireland')									 { var ChapmanCode = 'IRL';
	} else if (location == 'Israel')									 { var ChapmanCode = 'ISR';
	} else if (location == 'Italy')										 { var ChapmanCode = 'ITA';
	} else if (location == 'Jamaica')									 { var ChapmanCode = 'JAM';
	} else if (location == 'Japan')										 { var ChapmanCode = 'JPN';
	} else if (location == 'Jordan')									 { var ChapmanCode = 'JOR';
	} else if (location == 'Kazakhstan')								 { var ChapmanCode = 'KAZ';
	} else if (location == 'Kenya')										 { var ChapmanCode = 'KEN';
	} else if (location == 'Kiribati')									 { var ChapmanCode = 'KIR';
	} else if (location == 'North Korea')								 { var ChapmanCode = 'PRK';
	} else if (location == 'Korea, Democratic People\'s Republic of')	 { var ChapmanCode = 'PRK';
	} else if (location == 'South Korea')								 { var ChapmanCode = 'KOR';
	} else if (location == 'Korea, Republic of')						 { var ChapmanCode = 'KOR';
	} else if (location == 'Kuwait')									 { var ChapmanCode = 'KWT';
	} else if (location == 'Kyrgyzstan')								 { var ChapmanCode = 'KGZ';
	} else if (location == 'Laos')										 { var ChapmanCode = 'LAO';
	} else if (location == 'Lao People\'s Democratic Republic')			 { var ChapmanCode = 'LAO';
	} else if (location == 'Latvia')									 { var ChapmanCode = 'LVA';
	} else if (location == 'Lebanon')									 { var ChapmanCode = 'LBN';
	} else if (location == 'Lesotho')									 { var ChapmanCode = 'LSO';
	} else if (location == 'Liberia')									 { var ChapmanCode = 'LBR';
	} else if (location == 'Libya')										 { var ChapmanCode = 'LBY';
	} else if (location == 'Libyan Arab Jamahiriya')					 { var ChapmanCode = 'LBY';
	} else if (location == 'Liechtenstein')								 { var ChapmanCode = 'LIE';
	} else if (location == 'Lithuania')									 { var ChapmanCode = 'LTU';
	} else if (location == 'Luxembourg')								 { var ChapmanCode = 'LUX';
	} else if (location == 'Macau')										 { var ChapmanCode = 'MAC';
	} else if (location == 'Macedonia')									 { var ChapmanCode = 'MKD';
	} else if (location == 'Madagascar')								 { var ChapmanCode = 'MDG';
	} else if (location == 'Malawi')									 { var ChapmanCode = 'MWI';
	} else if (location == 'Malaysia')									 { var ChapmanCode = 'MYS';
	} else if (location == 'Maldives')									 { var ChapmanCode = 'MDV';
	} else if (location == 'Mali')										 { var ChapmanCode = 'MLI';
	} else if (location == 'Malta')										 { var ChapmanCode = 'MLT';
	} else if (location == 'Marshall Islands')							 { var ChapmanCode = 'MHL';
	} else if (location == 'Martinique')								 { var ChapmanCode = 'MTQ';
	} else if (location == 'Mauritania')								 { var ChapmanCode = 'MRT';
	} else if (location == 'Mauritius')									 { var ChapmanCode = 'MUS';
	} else if (location == 'Mayotte')									 { var ChapmanCode = 'MYT';
	} else if (location == 'Mexico')									 { var ChapmanCode = 'MEX';
	} else if (location == 'Micronesia')								 { var ChapmanCode = 'FSM';
	} else if (location == 'Micronesia, Federated States of')			 { var ChapmanCode = 'FSM';
	} else if (location == 'Moldova')									 { var ChapmanCode = 'MDA';
	} else if (location == 'Moldova, Republic of')						 { var ChapmanCode = 'MDA';
	} else if (location == 'Monaco')									 { var ChapmanCode = 'MCO';
	} else if (location == 'Mongolia')									 { var ChapmanCode = 'MNG';
	} else if (location == 'Montserrat')								 { var ChapmanCode = 'MSR';
	} else if (location == 'Morocco')									 { var ChapmanCode = 'MAR';
	} else if (location == 'Mozambique')								 { var ChapmanCode = 'MOZ';
	} else if (location == 'Myanmar')									 { var ChapmanCode = 'MMR';
	} else if (location == 'Namibia')									 { var ChapmanCode = 'NAM';
	} else if (location == 'Nauru')										 { var ChapmanCode = 'NRU';
	} else if (location == 'Nepal')										 { var ChapmanCode = 'NPL';
	} else if (location == 'Nederland')									 { var ChapmanCode = 'NLD';
	} else if (location == 'Netherlands')								 { var ChapmanCode = 'NLD';
	} else if (location == 'Netherlands Antilles')						 { var ChapmanCode = 'ANT';
	} else if (location == 'New Caledonia')								 { var ChapmanCode = 'NCL';
	} else if (location == 'New Zealand')								 { var ChapmanCode = 'NZL';
	} else if (location == 'Nicaragua')									 { var ChapmanCode = 'NIC';
	} else if (location == 'Niger')										 { var ChapmanCode = 'NER';
	} else if (location == 'Nigeria')									 { var ChapmanCode = 'NGA';
	} else if (location == 'Niue')										 { var ChapmanCode = 'NIU';
	} else if (location == 'Norfolk Island')							 { var ChapmanCode = 'NFK';
	} else if (location == 'Northern Ireland')							 { var ChapmanCode = 'NIR';
	} else if (location == 'Northern Mariana Islands')					 { var ChapmanCode = 'MNP';
	} else if (location == 'Norway')									 { var ChapmanCode = 'NOR';
	} else if (location == 'Oman')										 { var ChapmanCode = 'OMN';
	} else if (location == 'Pakistan')									 { var ChapmanCode = 'PAK';
	} else if (location == 'Palau')										 { var ChapmanCode = 'PLW';
	} else if (location == 'Panama')									 { var ChapmanCode = 'PAN';
	} else if (location == 'Panama Canal Zone')							 { var ChapmanCode = 'PCZ';
	} else if (location == 'Papua New Guinea')							 { var ChapmanCode = 'PNG';
	} else if (location == 'Paraguay')									 { var ChapmanCode = 'PRY';
	} else if (location == 'Peru')										 { var ChapmanCode = 'PER';
	} else if (location == 'Philippines')								 { var ChapmanCode = 'PHL';
	} else if (location == 'Pitcairn')									 { var ChapmanCode = 'PCN';
	} else if (location == 'Poland')									 { var ChapmanCode = 'POL';
	} else if (location == 'Portugal')									 { var ChapmanCode = 'PRT';
	} else if (location == 'Puerto Rico')								 { var ChapmanCode = 'PRI';
	} else if (location == 'Qatar')										 { var ChapmanCode = 'QAT';
	} else if (location == 'Reunion')									 { var ChapmanCode = 'REU';
	} else if (location == 'Romania')									 { var ChapmanCode = 'ROU';
	} else if (location == 'Russia')									 { var ChapmanCode = 'RUS';
	} else if (location == 'Russian Federation')						 { var ChapmanCode = 'RUS';
	} else if (location == 'Rwanda')									 { var ChapmanCode = 'RWA';
	} else if (location == 'Saint Kitts and Nevis')						 { var ChapmanCode = 'KNA';
	} else if (location == 'Saint Lucia')								 { var ChapmanCode = 'LCA';
	} else if (location == 'Saint Vincent and the Grenadines')			 { var ChapmanCode = 'VCT';
	} else if (location == 'Samoa')										 { var ChapmanCode = 'WSM';
	} else if (location == 'San Marino')								 { var ChapmanCode = 'SMR';
	} else if (location == 'Sao Tome and Principe')						 { var ChapmanCode = 'STP';
	} else if (location == 'Saudi Arabia')								 { var ChapmanCode = 'SAU';
	} else if (location == 'Scotland')									 { var ChapmanCode = 'SCT';
	} else if (location == 'Senegal')									 { var ChapmanCode = 'SEN';
	} else if (location == 'Seychelles')								 { var ChapmanCode = 'SYC';
	} else if (location == 'Sicily')									 { var ChapmanCode = 'SIC';
	} else if (location == 'Sierra Leone')								 { var ChapmanCode = 'SLE';
	} else if (location == 'Singapore')									 { var ChapmanCode = 'SGP';
	} else if (location == 'Slovakia')									 { var ChapmanCode = 'SVK';
	} else if (location == 'Slovak Republic')							 { var ChapmanCode = 'SVK';
	} else if (location == 'Slovenia')									 { var ChapmanCode = 'SVN';
	} else if (location == 'Solomon Islands')							 { var ChapmanCode = 'SLB';
	} else if (location == 'Somalia')									 { var ChapmanCode = 'SOM';
	} else if (location == 'South Africa')								 { var ChapmanCode = 'ZAF';
	} else if (location == 'Zuid Afrika')								 { var ChapmanCode = 'ZAF';
	} else if (location == 'South America')								 { var ChapmanCode = 'SAM';
	} else if (location == 'Spain')										 { var ChapmanCode = 'ESP';
	} else if (location == 'Espana')									 { var ChapmanCode = 'ESP';
	} else if (location == 'Sri Lanka')									 { var ChapmanCode = 'LKA';
	} else if (location == 'Saint Helena')								 { var ChapmanCode = 'SHN';
	} else if (location == 'Saint Pierre and Miquelon')					 { var ChapmanCode = 'SPM';
	} else if (location == 'Sudan')										 { var ChapmanCode = 'SDN';
	} else if (location == 'Suriname')									 { var ChapmanCode = 'SUR';
	} else if (location == 'Svalbard and Jan Mayen Islands')			 { var ChapmanCode = 'SJM';
	} else if (location == 'Swaziland')									 { var ChapmanCode = 'SWZ';
	} else if (location == 'Sweden')									 { var ChapmanCode = 'SWE';
	} else if (location == 'Switzerland')								 { var ChapmanCode = 'CHE';
	} else if (location == 'Syria')										 { var ChapmanCode = 'SYR';
	} else if (location == 'Syrian Arab Republic')						 { var ChapmanCode = 'SYR';
	} else if (location == 'Taiwan')									 { var ChapmanCode = 'TWN';
	} else if (location == 'Taiwan, Province of China')					 { var ChapmanCode = 'TWN';
	} else if (location == 'Tajikistan')								 { var ChapmanCode = 'TJK';
	} else if (location == 'Tanzania')									 { var ChapmanCode = 'TZA';
	} else if (location == 'Tanzania, United Republic of')				 { var ChapmanCode = 'TZA';
	} else if (location == 'United Republic of Tanzania')				 { var ChapmanCode = 'TZA';
	} else if (location == 'Thailand')									 { var ChapmanCode = 'THA';
	} else if (location == 'Togo')										 { var ChapmanCode = 'TGO';
	} else if (location == 'Tokelau')									 { var ChapmanCode = 'TKL';
	} else if (location == 'Tonga')										 { var ChapmanCode = 'TON';
	} else if (location == 'Trinidad and Tobago')						 { var ChapmanCode = 'TTO';
	} else if (location == 'Tunisia')									 { var ChapmanCode = 'TUN';
	} else if (location == 'Turkey')									 { var ChapmanCode = 'TUR';
	} else if (location == 'Turkmenistan')								 { var ChapmanCode = 'TKM';
	} else if (location == 'Turks and Caicos Islands')					 { var ChapmanCode = 'TCA';
	} else if (location == 'Tuvalu')									 { var ChapmanCode = 'TUV';
	} else if (location == 'Uganda')									 { var ChapmanCode = 'UGA';
	} else if (location == 'Ukraine')									 { var ChapmanCode = 'UKR';
	} else if (location == 'United Arab Emirates')						 { var ChapmanCode = 'ARE';
	} else if (location == 'Arab Emirates')								 { var ChapmanCode = 'ARE';
	} else if (location == 'United Kingdom')							 { var ChapmanCode = 'GBR';
	} else if (location == 'United States Of America')					 { var ChapmanCode = 'USA';
	} else if (location == 'United States')								 { var ChapmanCode = 'USA';
	} else if (location == 'United States, Minor Outlying Islands')		 { var ChapmanCode = 'UMI';
	} else if (location == 'Uruguay')									 { var ChapmanCode = 'URY';
	} else if (location == 'Uzbekistan')								 { var ChapmanCode = 'UZB';
	} else if (location == 'Vanuatu')									 { var ChapmanCode = 'VUT';
	} else if (location == 'Vatican City State')						 { var ChapmanCode = 'VAT';
	} else if (location == 'Venezuela')									 { var ChapmanCode = 'VEN';
	} else if (location == 'Viet Nam')									 { var ChapmanCode = 'VNM';
	} else if (location == 'Virgin Islands (British)')					 { var ChapmanCode = 'VGB';
	} else if (location == 'Virgin Islands (U.S.)')						 { var ChapmanCode = 'VIR';
	} else if (location == 'Wales')										 { var ChapmanCode = 'WLS';
	} else if (location == 'Wallis and Futuna Islands')					 { var ChapmanCode = 'WLF';
	} else if (location == 'West Africa')								 { var ChapmanCode = 'WAF';
	} else if (location == 'West Indies')								 { var ChapmanCode = 'BWI';
	} else if (location == 'West Indies, British')						 { var ChapmanCode = 'BWI';
	} else if (location == 'Western Sahara')							 { var ChapmanCode = 'ESH';
	} else if (location == 'Yemen')										 { var ChapmanCode = 'YEM';
	} else if (location == 'Yugoslavia')								 { var ChapmanCode = 'YUG';
	} else if (location == 'Zaire')										 { var ChapmanCode = 'ZAR';
	} else if (location == 'Zambia')									 { var ChapmanCode = 'ZMB';
	} else if (location == 'Zimbabwe')									 { var ChapmanCode = 'ZWE';

	// USA States
	} else if (location == 'AL' || location == 'Alabama')				 { var ChapmanCode = 'AL'; 
	} else if (location == 'AK' || location == 'Alaska')				 { var ChapmanCode = 'AK'; 
	} else if (location == 'AZ' || location == 'Arizona')				 { var ChapmanCode = 'AZ'; 
	} else if (location == 'AR' || location == 'Arkansas')				 { var ChapmanCode = 'AR'; 
	} else if (location == 'CA' || location == 'California')			 { var ChapmanCode = 'CA'; 
	} else if (location == 'CO' || location == 'Colorado')				 { var ChapmanCode = 'CO'; 
	} else if (location == 'CT' || location == 'Connecticut')			 { var ChapmanCode = 'CT'; 
	} else if (location == 'DE' || location == 'Delaware')				 { var ChapmanCode = 'DE'; 
	} else if (location == 'DC' || location == 'District of Columbia')	 { var ChapmanCode = 'DC'; 
	} else if (location == 'FL' || location == 'Florida')				 { var ChapmanCode = 'FL'; 
	} else if (location == 'GA' || location == 'Georgia')				 { var ChapmanCode = 'GA'; 
	} else if (location == 'HI' || location == 'Hawaii')				 { var ChapmanCode = 'HI'; 
	} else if (location == 'ID' || location == 'Idaho')					 { var ChapmanCode = 'ID'; 
	} else if (location == 'IL' || location == 'Illinois')				 { var ChapmanCode = 'IL'; 
	} else if (location == 'IN' || location == 'Indiana')				 { var ChapmanCode = 'IN'; 
	} else if (location == 'IA' || location == 'Iowa')					 { var ChapmanCode = 'IA'; 
	} else if (location == 'KS' || location == 'Kansas')				 { var ChapmanCode = 'KS'; 
	} else if (location == 'KY' || location == 'Kentucky')				 { var ChapmanCode = 'KY'; 
	} else if (location == 'LA' || location == 'Louisiana')				 { var ChapmanCode = 'LA'; 
	} else if (location == 'ME' || location == 'Maine')					 { var ChapmanCode = 'ME'; 
	} else if (location == 'MD' || location == 'Maryland')				 { var ChapmanCode = 'MD'; 
	} else if (location == 'MA' || location == 'Massachusetts')			 { var ChapmanCode = 'MA'; 
	} else if (location == 'MI' || location == 'Michigan')				 { var ChapmanCode = 'MI'; 
	} else if (location == 'MN' || location == 'Minnesota')				 { var ChapmanCode = 'MN'; 
	} else if (location == 'MS' || location == 'Mississippi')			 { var ChapmanCode = 'MS'; 
	} else if (location == 'MO' || location == 'Missouri')				 { var ChapmanCode = 'MO'; 
	} else if (location == 'MT' || location == 'Montana')				 { var ChapmanCode = 'MT'; 
	} else if (location == 'NE' || location == 'Nebraska')				 { var ChapmanCode = 'NE'; 
	} else if (location == 'NV' || location == 'Nevada')				 { var ChapmanCode = 'NV'; 
	} else if (location == 'NH' || location == 'New Hampshire')			 { var ChapmanCode = 'NH'; 
	} else if (location == 'NJ' || location == 'New Jersey')			 { var ChapmanCode = 'NJ'; 
	} else if (location == 'NM' || location == 'New Mexico')			 { var ChapmanCode = 'NM'; 
	} else if (location == 'NY' || location == 'New York')				 { var ChapmanCode = 'NY'; 
	} else if (location == 'NC' || location == 'North Carolina')		 { var ChapmanCode = 'NC'; 
	} else if (location == 'ND' || location == 'North Dakota')			 { var ChapmanCode = 'ND'; 
	} else if (location == 'OH' || location == 'Ohio')					 { var ChapmanCode = 'OH'; 
	} else if (location == 'OK' || location == 'Oklahoma')				 { var ChapmanCode = 'OK'; 
	} else if (location == 'OR' || location == 'Oregon')				 { var ChapmanCode = 'OR'; 
	} else if (location == 'PA' || location == 'Pennsylvania')			 { var ChapmanCode = 'PA'; 
	} else if (location == 'PR' || location == '(Puerto Rico)')			 { var ChapmanCode = 'PR'; 
	} else if (location == 'RI' || location == 'Rhode Island')			 { var ChapmanCode = 'RI'; 
	} else if (location == 'SC' || location == 'South Carolina')		 { var ChapmanCode = 'SC'; 
	} else if (location == 'SD' || location == 'South Dakota')			 { var ChapmanCode = 'SD'; 
	} else if (location == 'TN' || location == 'Tennessee')				 { var ChapmanCode = 'TN'; 
	} else if (location == 'TX' || location == 'Texas')					 { var ChapmanCode = 'TX'; 
	} else if (location == 'UT' || location == 'Utah')					 { var ChapmanCode = 'UT'; 
	} else if (location == 'VT' || location == 'Vermont')				 { var ChapmanCode = 'VT'; 
	} else if (location == 'VA' || location == 'Virginia')				 { var ChapmanCode = 'VA'; 
	} else if (location == 'VI' || location == '(Virgin Islands)')		 { var ChapmanCode = 'VI'; 
	} else if (location == 'WA' || location == 'Washington')			 { var ChapmanCode = 'WA'; 
	} else if (location == 'WV' || location == 'West Virginia')			 { var ChapmanCode = 'WV'; 
	} else if (location == 'WI' || location == 'Wisconsin')				 { var ChapmanCode = 'WI'; 
	} else if (location == 'WY' || location == 'Wyoming')				 { var ChapmanCode = 'WY'; 

	// Canadian Provinces
	} else if (location == 'AB' || location == 'Alberta')					 { var ChapmanCode = 'AB';
	} else if (location == 'BC' || location == 'British Columbia')			 { var ChapmanCode = 'BC';
	} else if (location == 'NL' || location == 'Labrador')					 { var ChapmanCode = 'NL';
	} else if (location == 'MB' || location == 'Manitoba')					 { var ChapmanCode = 'MB';
	} else if (location == 'NB' || location == 'New Brunswick')				 { var ChapmanCode = 'NB';
	} else if (location == 'NL' || location == 'Newfoundland')				 { var ChapmanCode = 'NL';
	} else if (location == 'NL' || location == 'Newfoundland and Labrador')	 { var ChapmanCode = 'NL';
	} else if (location == 'NT' || location == 'Northwest Territories')		 { var ChapmanCode = 'NT';
	} else if (location == 'NS' || location == 'Nova Scotia')				 { var ChapmanCode = 'NS';
	} else if (location == 'NU' || location == 'Nunavut')					 { var ChapmanCode = 'NU';
	} else if (location == 'ON' || location == 'Ontario')					 { var ChapmanCode = 'ON';
	} else if (location == 'PE' || location == 'Prince Edward Island')		 { var ChapmanCode = 'PE';
	} else if (location == 'QC' || location == 'Quebec')					 { var ChapmanCode = 'QC';
	} else if (location == 'SK' || location == 'Saskatchewan')				 { var ChapmanCode = 'SK';
	} else if (location == 'YT' || location == 'Yukon')						 { var ChapmanCode = 'YT';
	
	// Default ChapmanCode
	} else {
		var ChapmanCode = '?NA'; 
	}
	return ChapmanCode;
}
