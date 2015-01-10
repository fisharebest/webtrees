// Census Assistant module for webtrees
//
// Chapman Code information
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

"use strict";

function getChapmanCode(place_name) {
	switch (place_name) {
	// Countries
	case "Afghanistan":
		return "AFG";
	case "Albania":
		return "ALB";
	case "Algeria":
		return "DZA";
	case "American Samoa":
		return "ASM";
	case "Andorra":
		return "AND";
	case "Angola":
		return "AGO";
	case "Anguilla":
		return "AIA";
	case "Antarctica":
		return "ATA";
	case "Antigua and Barbuda":
		return "ATG";
	case "Argentina":
		return "ARG";
	case "Armenia":
		return "ARM";
	case "Aruba":
		return "ABW";
	case "Australia":
		return "AUS";
	case "Austria":
		return "AUT";
	case "Azerbaijan":
		return "AZE";
	case "Bahamas":
		return "BHS";
	case "Bahrain":
		return "BHR";
	case "Bangladesh":
		return "BGD";
	case "Barbados":
		return "BRB";
	case "Belarus":
		return "BLR";
	case "Belgium":
		return "BEL";
	case "Belize":
		return "BLZ";
	case "Benin":
		return "BEN";
	case "Bermuda":
		return "BMU";
	case "Bhutan":
		return "BTN";
	case "Bolivia":
		return "BOL";
	case "Bosnia and Herzegovina":
		return "BIH";
	case "Botswana":
		return "BWA";
	case "Bouvet Island":
		return "BVT";
	case "Brazil":
		return "BRA";
	case "British Indian Ocean Territory":
		return "IOT";
	case "British West Indies":
		return "BWI";
	case "Brunei Darussalam":
		return "BRN";
	case "Bulgaria":
		return "BGR";
	case "Burkina Faso":
		return "BFA";
	case "Burma":
		return "MMR";
	case "Burundi":
		return "BDI";
	case "Cambodia":
		return "KHM";
	case "Cameroon":
		return "CMR";
	case "Canada":
		return "CAN";
	case "Cape Verde":
		return "CPV";
	case "Cayman Islands":
		return "CYM";
	case "Central African Republic":
		return "CAF";
	case "Central America":
		return "CAM";
	case "Chad":
		return "TCD";
	case "Channel Islands":
		return "CHI";
	case "Chile":
		return "CHL";
	case "China":
		return "CHN";
	case "Christmas Island":
		return "CXR";
	case "Cocos (Keeling) Islands":
		return "CCK";
	case "Colombia":
		return "COL";
	case "Comoros":
		return "COM";
	case "Congo":
		return "COG";
	case "Cook Islands":
		return "COK";
	case "Costa Rica":
		return "CRI";
	case "Cote D\'ivoire":
		return "CIV";
	case "Croatia":
		return "HRV";
	case "Hrvatska)":
		return "HRV";
	case "Cuba":
		return "CUB";
	case "Cyprus":
		return "CYP";
	case "Czechoslovakia":
		return "CSK";
	case "Czech Republic":
		return "CZE";
	case "Denmark":
		return "DNK";
	case "Deutschland":
		return "DEU";
	case "Djibouti":
		return "DJI";
	case "Dominica":
		return "DMA";
	case "Dominican Republic":
		return "DOM";
	case "Dutch New Guinea":
		return "DNG";
	case "East Indies":
		return "IDN";
	case "East Timor":
		return "TLS";
	case "Ecuador":
		return "ECU";
	case "Egypt":
		return "EGY";
	case "Eire":
		return "EIR";
	case "El Salvador":
		return "SLV";
	case "England":
		return "ENG";
	case "Equatorial Guinea":
		return "GNQ";
	case "Eritrea":
		return "ERI";
	case "Estonia":
		return "EST";
	case "Ethiopia":
		return "ETH";
	case "Falkland Islands":
		return "FLK";
	case "Malvinas":
		return "FLK";
	case "Faroe Islands":
		return "FRO";
	case "Fiji":
		return "FJI";
	case "Finland":
		return "FIN";
	case "France":
		return "FRA";
	case "France, Metropolitan":
		return "FXX";
	case "French Guiana":
		return "GUF";
	case "French Polynesia":
		return "PYF";
	case "French Southern Territories":
		return "ATF";
	case "Gabon":
		return "GAB";
	case "Gambia":
		return "GMB";
	case "Georgia":
		return "GEO";
	case "Germany":
		return "DEU";
	case "Ghana":
		return "GHA";
	case "Gibraltar":
		return "GIB";
	case "Great Britain":
		return "GBR";
	case "Greece":
		return "GRC";
	case "Greenland":
		return "GRL";
	case "Grenada":
		return "GRD";
	case "Guadeloupe":
		return "GLP";
	case "Guam":
		return "GUM";
	case "Guatemala":
		return "GTM";
	case "Guinea":
		return "GIN";
	case "Guinea-Bissau":
		return "GNB";
	case "Guyana":
		return "GUY";
	case "Haiti":
		return "HTI";
	case "Heard Island & McDonald Islands":
		return "HMD";
	case "Holland":
		return "HOL";
	case "Honduras":
		return "HND";
	case "Hong Kong":
		return "HKG";
	case "Hungary":
		return "HUN";
	case "Iceland":
		return "ISL";
	case "India":
		return "IND";
	case "Indonesia":
		return "IDN";
	case "Iran":
		return "IRN";
	case "Islamic Republic of Iran":
		return "IRN";
	case "Iraq":
		return "IRQ";
	case "Ireland":
		return "IRL";
	case "Israel":
		return "ISR";
	case "Italy":
		return "ITA";
	case "Jamaica":
		return "JAM";
	case "Japan":
		return "JPN";
	case "Jordan":
		return "JOR";
	case "Kazakhstan":
		return "KAZ";
	case "Kenya":
		return "KEN";
	case "Kiribati":
		return "KIR";
	case "North Korea":
		return "PRK";
	case "Korea, Democratic People\'s Republic of":
		return "PRK";
	case "South Korea":
		return "KOR";
	case "Korea, Republic of":
		return "KOR";
	case "Kuwait":
		return "KWT";
	case "Kyrgyzstan":
		return "KGZ";
	case "Laos":
		return "LAO";
	case "Lao People\'s Democratic Republic":
		return "LAO";
	case "Latvia":
		return "LVA";
	case "Lebanon":
		return "LBN";
	case "Lesotho":
		return "LSO";
	case "Liberia":
		return "LBR";
	case "Libya":
		return "LBY";
	case "Libyan Arab Jamahiriya":
		return "LBY";
	case "Liechtenstein":
		return "LIE";
	case "Lithuania":
		return "LTU";
	case "Luxembourg":
		return "LUX";
	case "Macau":
		return "MAC";
	case "Macedonia":
		return "MKD";
	case "Madagascar":
		return "MDG";
	case "Malawi":
		return "MWI";
	case "Malaysia":
		return "MYS";
	case "Maldives":
		return "MDV";
	case "Mali":
		return "MLI";
	case "Malta":
		return "MLT";
	case "Marshall Islands":
		return "MHL";
	case "Martinique":
		return "MTQ";
	case "Mauritania":
		return "MRT";
	case "Mauritius":
		return "MUS";
	case "Mayotte":
		return "MYT";
	case "Mexico":
		return "MEX";
	case "Micronesia":
		return "FSM";
	case "Micronesia, Federated States of":
		return "FSM";
	case "Moldova":
		return "MDA";
	case "Moldova, Republic of":
		return "MDA";
	case "Monaco":
		return "MCO";
	case "Mongolia":
		return "MNG";
	case "Montserrat":
		return "MSR";
	case "Morocco":
		return "MAR";
	case "Mozambique":
		return "MOZ";
	case "Myanmar":
		return "MMR";
	case "Namibia":
		return "NAM";
	case "Nauru":
		return "NRU";
	case "Nepal":
		return "NPL";
	case "Nederland":
		return "NLD";
	case "Netherlands":
		return "NLD";
	case "Netherlands Antilles":
		return "ANT";
	case "New Caledonia":
		return "NCL";
	case "New Zealand":
		return "NZL";
	case "Nicaragua":
		return "NIC";
	case "Niger":
		return "NER";
	case "Nigeria":
		return "NGA";
	case "Niue":
		return "NIU";
	case "Norfolk Island":
		return "NFK";
	case "Northern Ireland":
		return "NIR";
	case "Northern Mariana Islands":
		return "MNP";
	case "Norway":
		return "NOR";
	case "Oman":
		return "OMN";
	case "Pakistan":
		return "PAK";
	case "Palau":
		return "PLW";
	case "Panama":
		return "PAN";
	case "Panama Canal Zone":
		return "PCZ";
	case "Papua New Guinea":
		return "PNG";
	case "Paraguay":
		return "PRY";
	case "Peru":
		return "PER";
	case "Philippines":
		return "PHL";
	case "Pitcairn":
		return "PCN";
	case "Poland":
		return "POL";
	case "Portugal":
		return "PRT";
	case "Puerto Rico":
		return "PRI";
	case "Qatar":
		return "QAT";
	case "Reunion":
		return "REU";
	case "Romania":
		return "ROU";
	case "Russia":
		return "RUS";
	case "Russian Federation":
		return "RUS";
	case "Rwanda":
		return "RWA";
	case "Saint Kitts and Nevis":
		return "KNA";
	case "Saint Lucia":
		return "LCA";
	case "Saint Vincent and the Grenadines":
		return "VCT";
	case "Samoa":
		return "WSM";
	case "San Marino":
		return "SMR";
	case "Sao Tome and Principe":
		return "STP";
	case "Saudi Arabia":
		return "SAU";
	case "Scotland":
		return "SCT";
	case "Senegal":
		return "SEN";
	case "Seychelles":
		return "SYC";
	case "Sicily":
		return "SIC";
	case "Sierra Leone":
		return "SLE";
	case "Singapore":
		return "SGP";
	case "Slovakia":
		return "SVK";
	case "Slovak Republic":
		return "SVK";
	case "Slovenia":
		return "SVN";
	case "Solomon Islands":
		return "SLB";
	case "Somalia":
		return "SOM";
	case "South Africa":
		return "ZAF";
	case "Zuid Afrika":
		return "ZAF";
	case "South America":
		return "SAM";
	case "Spain":
		return "ESP";
	case "Espana":
		return "ESP";
	case "Sri Lanka":
		return "LKA";
	case "Saint Helena":
		return "SHN";
	case "Saint Pierre and Miquelon":
		return "SPM";
	case "Sudan":
		return "SDN";
	case "Suriname":
		return "SUR";
	case "Svalbard and Jan Mayen Islands":
		return "SJM";
	case "Swaziland":
		return "SWZ";
	case "Sweden":
		return "SWE";
	case "Switzerland":
		return "CHE";
	case "Syria":
		return "SYR";
	case "Syrian Arab Republic":
		return "SYR";
	case "Taiwan":
		return "TWN";
	case "Taiwan, Province of China":
		return "TWN";
	case "Tajikistan":
		return "TJK";
	case "Tanzania":
		return "TZA";
	case "Tanzania, United Republic of":
		return "TZA";
	case "United Republic of Tanzania":
		return "TZA";
	case "Thailand":
		return "THA";
	case "Togo":
		return "TGO";
	case "Tokelau":
		return "TKL";
	case "Tonga":
		return "TON";
	case "Trinidad and Tobago":
		return "TTO";
	case "Tunisia":
		return "TUN";
	case "Turkey":
		return "TUR";
	case "Turkmenistan":
		return "TKM";
	case "Turks and Caicos Islands":
		return "TCA";
	case "Tuvalu":
		return "TUV";
	case "Uganda":
		return "UGA";
	case "Ukraine":
		return "UKR";
	case "United Arab Emirates":
		return "ARE";
	case "Arab Emirates":
		return "ARE";
	case "United Kingdom":
		return "GBR";
	case "United States Of America":
		return "USA";
	case "United States":
		return "USA";
	case "United States, Minor Outlying Islands":
		return "UMI";
	case "Uruguay":
		return "URY";
	case "Uzbekistan":
		return "UZB";
	case "Vanuatu":
		return "VUT";
	case "Vatican City State":
		return "VAT";
	case "Venezuela":
		return "VEN";
	case "Viet Nam":
		return "VNM";
	case "Virgin Islands (British)":
		return "VGB";
	case "Virgin Islands (U.S.)":
		return "VIR";
	case "Wales":
		return "WLS";
	case "Wallis and Futuna Islands":
		return "WLF";
	case "West Africa":
		return "WAF";
	case "West Indies":
		return "BWI";
	case "West Indies, British":
		return "BWI";
	case "Western Sahara":
		return "ESH";
	case "Yemen":
		return "YEM";
	case "Yugoslavia":
		return "YUG";
	case "Zaire":
		return "ZAR";
	case "Zambia":
		return "ZMB";
	case "Zimbabwe":
		return "ZWE";

	// USA States
	case "AL":
	case "Alabama":
		return "AL"; 
	case "AK":
	case "Alaska":
		return "AK"; 
	case "AZ":
	case "Arizona":
		return "AZ"; 
	case "AR":
	case "Arkansas":
		return "AR"; 
	case "CA":
	case "California":
		return "CA"; 
	case "CO":
	case "Colorado":
		return "CO"; 
	case "CT":
	case "Connecticut":
		return "CT"; 
	case "DE":
	case "Delaware":
		return "DE"; 
	case "DC":
	case "District of Columbia":
		return "DC"; 
	case "FL":
	case "Florida":
		return "FL"; 
	case "GA":
	// case "Georgia": // Georgia is the country GEO
		return "GA"; 
	case "HI":
	case "Hawaii":
		return "HI"; 
	case "ID":
	case "Idaho":
		return "ID"; 
	case "IL":
	case "Illinois":
		return "IL"; 
	case "IN":
	case "Indiana":
		return "IN"; 
	case "IA":
	case "Iowa":
		return "IA"; 
	case "KS":
	case "Kansas":
		return "KS"; 
	case "KY":
	case "Kentucky":
		return "KY"; 
	case "LA":
	case "Louisiana":
		return "LA"; 
	case "ME":
	case "Maine":
		return "ME"; 
	case "MD":
	case "Maryland":
		return "MD"; 
	case "MA":
	case "Massachusetts":
		return "MA"; 
	case "MI":
	case "Michigan":
		return "MI"; 
	case "MN":
	case "Minnesota":
		return "MN"; 
	case "MS":
	case "Mississippi":
		return "MS"; 
	case "MO":
	case "Missouri":
		return "MO"; 
	case "MT":
	case "Montana":
		return "MT"; 
	case "NE":
	case "Nebraska":
		return "NE"; 
	case "NV":
	case "Nevada":
		return "NV"; 
	case "NH":
	case "New Hampshire":
		return "NH"; 
	case "NJ":
	case "New Jersey":
		return "NJ"; 
	case "NM":
	case "New Mexico":
		return "NM"; 
	case "NY":
	case "New York":
		return "NY"; 
	case "NC":
	case "North Carolina":
		return "NC"; 
	case "ND":
	case "North Dakota":
		return "ND"; 
	case "OH":
	case "Ohio":
		return "OH"; 
	case "OK":
	case "Oklahoma":
		return "OK"; 
	case "OR":
	case "Oregon":
		return "OR"; 
	case "PA":
	case "Pennsylvania":
		return "PA"; 
	case "PR":
	case "(Puerto Rico)":
		return "PR"; 
	case "RI":
	case "Rhode Island":
		return "RI"; 
	case "SC":
	case "South Carolina":
		return "SC"; 
	case "SD":
	case "South Dakota":
		return "SD"; 
	case "TN":
	case "Tennessee":
		return "TN"; 
	case "TX":
	case "Texas":
		return "TX"; 
	case "UT":
	case "Utah":
		return "UT"; 
	case "VT":
	case "Vermont":
		return "VT"; 
	case "VA":
	case "Virginia":
		return "VA"; 
	case "VI":
	case "(Virgin Islands)":
		return "VI"; 
	case "WA":
	case "Washington":
		return "WA"; 
	case "WV":
	case "West Virginia":
		return "WV"; 
	case "WI":
	case "Wisconsin":
		return "WI"; 
	case "WY":
	case "Wyoming":
		return "WY"; 

	// Canadian Provinces
	case "AB":
	case "Alberta":
		return "AB";
	case "BC":
	case "British Columbia":
		return "BC";
	case "MB":
	case "Manitoba":
		return "MB";
	case "NB":
	case "New Brunswick":
		return "NB";
	case "NL":
	case "Newfoundland":
	case "Labrador":
	case "Newfoundland and Labrador":
		return "NL";
	case "NT":
	case "Northwest Territories":
		return "NT";
	case "NS":
	case "Nova Scotia":
		return "NS";
	case "NU":
	case "Nunavut":
		return "NU";
	case "ON":
	case "Ontario":
		return "ON";
	case "PE":
	case "Prince Edward Island":
		return "PE";
	case "QC":
	case "Quebec":
		return "QC";
	case "SK":
	case "Saskatchewan":
		return "SK";
	case "YT":
	case "Yukon":
		return "YT";	

	// Unknown or at sea
	case "All":
		return "ALL";
	case "Anywhere":
		return "ANY";
	case "At sea":
		return "SEA";
	case "Unknown":
	case "UNK":
	case "":
		return "UNK";
		
	// Unrecognised
	default:
		return "?NA"; 
	}
}
