<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Statistics\Service;

use Fisharebest\Webtrees\I18N;

/**
 * Functions for managing country codes and names.
 */
class CountryService
{
    /**
     * Country codes and names
     *
     * @return string[]
     */
    public function getAllCountries(): array
    {
        return [
            /* I18N: Name of a country or state */
            '???' => I18N::translate('Unknown'),
            /* I18N: Name of a country or state */
            'ABW' => I18N::translate('Aruba'),
            /* I18N: Name of a country or state */
            'AFG' => I18N::translate('Afghanistan'),
            /* I18N: Name of a country or state */
            'AGO' => I18N::translate('Angola'),
            /* I18N: Name of a country or state */
            'AIA' => I18N::translate('Anguilla'),
            /* I18N: Name of a country or state */
            'ALA' => I18N::translate('Aland Islands'),
            /* I18N: Name of a country or state */
            'ALB' => I18N::translate('Albania'),
            /* I18N: Name of a country or state */
            'AND' => I18N::translate('Andorra'),
            /* I18N: Name of a country or state */
            'ARE' => I18N::translate('United Arab Emirates'),
            /* I18N: Name of a country or state */
            'ARG' => I18N::translate('Argentina'),
            /* I18N: Name of a country or state */
            'ARM' => I18N::translate('Armenia'),
            /* I18N: Name of a country or state */
            'ASM' => I18N::translate('American Samoa'),
            /* I18N: Name of a country or state */
            'ATA' => I18N::translate('Antarctica'),
            /* I18N: Name of a country or state */
            'ATF' => I18N::translate('French Southern Territories'),
            /* I18N: Name of a country or state */
            'ATG' => I18N::translate('Antigua and Barbuda'),
            /* I18N: Name of a country or state */
            'AUS' => I18N::translate('Australia'),
            /* I18N: Name of a country or state */
            'AUT' => I18N::translate('Austria'),
            /* I18N: Name of a country or state */
            'AZE' => I18N::translate('Azerbaijan'),
            /* I18N: Name of a country or state */
            'AZR' => I18N::translate('Azores'),
            /* I18N: Name of a country or state */
            'BDI' => I18N::translate('Burundi'),
            /* I18N: Name of a country or state */
            'BEL' => I18N::translate('Belgium'),
            /* I18N: Name of a country or state */
            'BEN' => I18N::translate('Benin'),
            // BES => Bonaire, Sint Eustatius and Saba
            /* I18N: Name of a country or state */
            'BFA' => I18N::translate('Burkina Faso'),
            /* I18N: Name of a country or state */
            'BGD' => I18N::translate('Bangladesh'),
            /* I18N: Name of a country or state */
            'BGR' => I18N::translate('Bulgaria'),
            /* I18N: Name of a country or state */
            'BHR' => I18N::translate('Bahrain'),
            /* I18N: Name of a country or state */
            'BHS' => I18N::translate('Bahamas'),
            /* I18N: Name of a country or state */
            'BIH' => I18N::translate('Bosnia and Herzegovina'),
            // BLM => Saint Barthélemy
            /* I18N: Name of a country or state */
            'BLR' => I18N::translate('Belarus'),
            /* I18N: Name of a country or state */
            'BLZ' => I18N::translate('Belize'),
            /* I18N: Name of a country or state */
            'BMU' => I18N::translate('Bermuda'),
            /* I18N: Name of a country or state */
            'BOL' => I18N::translate('Bolivia'),
            /* I18N: Name of a country or state */
            'BRA' => I18N::translate('Brazil'),
            /* I18N: Name of a country or state */
            'BRB' => I18N::translate('Barbados'),
            /* I18N: Name of a country or state */
            'BRN' => I18N::translate('Brunei Darussalam'),
            /* I18N: Name of a country or state */
            'BTN' => I18N::translate('Bhutan'),
            /* I18N: Name of a country or state */
            'BVT' => I18N::translate('Bouvet Island'),
            /* I18N: Name of a country or state */
            'BWA' => I18N::translate('Botswana'),
            /* I18N: Name of a country or state */
            'CAF' => I18N::translate('Central African Republic'),
            /* I18N: Name of a country or state */
            'CAN' => I18N::translate('Canada'),
            /* I18N: Name of a country or state */
            'CCK' => I18N::translate('Cocos (Keeling) Islands'),
            /* I18N: Name of a country or state */
            'CHE' => I18N::translate('Switzerland'),
            /* I18N: Name of a country or state */
            'CHL' => I18N::translate('Chile'),
            /* I18N: Name of a country or state */
            'CHN' => I18N::translate('China'),
            /* I18N: Name of a country or state */
            'CIV' => I18N::translate('Cote d’Ivoire'),
            /* I18N: Name of a country or state */
            'CMR' => I18N::translate('Cameroon'),
            /* I18N: Name of a country or state */
            'COD' => I18N::translate('Democratic Republic of the Congo'),
            /* I18N: Name of a country or state */
            'COG' => I18N::translate('Republic of the Congo'),
            /* I18N: Name of a country or state */
            'COK' => I18N::translate('Cook Islands'),
            /* I18N: Name of a country or state */
            'COL' => I18N::translate('Colombia'),
            /* I18N: Name of a country or state */
            'COM' => I18N::translate('Comoros'),
            /* I18N: Name of a country or state */
            'CPV' => I18N::translate('Cape Verde'),
            /* I18N: Name of a country or state */
            'CRI' => I18N::translate('Costa Rica'),
            /* I18N: Name of a country or state */
            'CUB' => I18N::translate('Cuba'),
            // CUW => Curaçao
            /* I18N: Name of a country or state */
            'CXR' => I18N::translate('Christmas Island'),
            /* I18N: Name of a country or state */
            'CYM' => I18N::translate('Cayman Islands'),
            /* I18N: Name of a country or state */
            'CYP' => I18N::translate('Cyprus'),
            /* I18N: Name of a country or state */
            'CZE' => I18N::translate('Czech Republic'),
            /* I18N: Name of a country or state */
            'DEU' => I18N::translate('Germany'),
            /* I18N: Name of a country or state */
            'DJI' => I18N::translate('Djibouti'),
            /* I18N: Name of a country or state */
            'DMA' => I18N::translate('Dominica'),
            /* I18N: Name of a country or state */
            'DNK' => I18N::translate('Denmark'),
            /* I18N: Name of a country or state */
            'DOM' => I18N::translate('Dominican Republic'),
            /* I18N: Name of a country or state */
            'DZA' => I18N::translate('Algeria'),
            /* I18N: Name of a country or state */
            'ECU' => I18N::translate('Ecuador'),
            /* I18N: Name of a country or state */
            'EGY' => I18N::translate('Egypt'),
            /* I18N: Name of a country or state */
            'ENG' => I18N::translate('England'),
            /* I18N: Name of a country or state */
            'ERI' => I18N::translate('Eritrea'),
            /* I18N: Name of a country or state */
            'ESH' => I18N::translate('Western Sahara'),
            /* I18N: Name of a country or state */
            'ESP' => I18N::translate('Spain'),
            /* I18N: Name of a country or state */
            'EST' => I18N::translate('Estonia'),
            /* I18N: Name of a country or state */
            'ETH' => I18N::translate('Ethiopia'),
            /* I18N: Name of a country or state */
            'FIN' => I18N::translate('Finland'),
            /* I18N: Name of a country or state */
            'FJI' => I18N::translate('Fiji'),
            /* I18N: Name of a country or state */
            'FLD' => I18N::translate('Flanders'),
            /* I18N: Name of a country or state */
            'FLK' => I18N::translate('Falkland Islands'),
            /* I18N: Name of a country or state */
            'FRA' => I18N::translate('France'),
            /* I18N: Name of a country or state */
            'FRO' => I18N::translate('Faroe Islands'),
            /* I18N: Name of a country or state */
            'FSM' => I18N::translate('Micronesia'),
            /* I18N: Name of a country or state */
            'GAB' => I18N::translate('Gabon'),
            /* I18N: Name of a country or state */
            'GBR' => I18N::translate('United Kingdom'),
            /* I18N: Name of a country or state */
            'GEO' => I18N::translate('Georgia'),
            /* I18N: Name of a country or state */
            'GGY' => I18N::translate('Guernsey'),
            /* I18N: Name of a country or state */
            'GHA' => I18N::translate('Ghana'),
            /* I18N: Name of a country or state */
            'GIB' => I18N::translate('Gibraltar'),
            /* I18N: Name of a country or state */
            'GIN' => I18N::translate('Guinea'),
            /* I18N: Name of a country or state */
            'GLP' => I18N::translate('Guadeloupe'),
            /* I18N: Name of a country or state */
            'GMB' => I18N::translate('Gambia'),
            /* I18N: Name of a country or state */
            'GNB' => I18N::translate('Guinea-Bissau'),
            /* I18N: Name of a country or state */
            'GNQ' => I18N::translate('Equatorial Guinea'),
            /* I18N: Name of a country or state */
            'GRC' => I18N::translate('Greece'),
            /* I18N: Name of a country or state */
            'GRD' => I18N::translate('Grenada'),
            /* I18N: Name of a country or state */
            'GRL' => I18N::translate('Greenland'),
            /* I18N: Name of a country or state */
            'GTM' => I18N::translate('Guatemala'),
            /* I18N: Name of a country or state */
            'GUF' => I18N::translate('French Guiana'),
            /* I18N: Name of a country or state */
            'GUM' => I18N::translate('Guam'),
            /* I18N: Name of a country or state */
            'GUY' => I18N::translate('Guyana'),
            /* I18N: Name of a country or state */
            'HKG' => I18N::translate('Hong Kong'),
            /* I18N: Name of a country or state */
            'HMD' => I18N::translate('Heard Island and McDonald Islands'),
            /* I18N: Name of a country or state */
            'HND' => I18N::translate('Honduras'),
            /* I18N: Name of a country or state */
            'HRV' => I18N::translate('Croatia'),
            /* I18N: Name of a country or state */
            'HTI' => I18N::translate('Haiti'),
            /* I18N: Name of a country or state */
            'HUN' => I18N::translate('Hungary'),
            /* I18N: Name of a country or state */
            'IDN' => I18N::translate('Indonesia'),
            /* I18N: Name of a country or state */
            'IND' => I18N::translate('India'),
            /* I18N: Name of a country or state */
            'IOM' => I18N::translate('Isle of Man'),
            /* I18N: Name of a country or state */
            'IOT' => I18N::translate('British Indian Ocean Territory'),
            /* I18N: Name of a country or state */
            'IRL' => I18N::translate('Ireland'),
            /* I18N: Name of a country or state */
            'IRN' => I18N::translate('Iran'),
            /* I18N: Name of a country or state */
            'IRQ' => I18N::translate('Iraq'),
            /* I18N: Name of a country or state */
            'ISL' => I18N::translate('Iceland'),
            /* I18N: Name of a country or state */
            'ISR' => I18N::translate('Israel'),
            /* I18N: Name of a country or state */
            'ITA' => I18N::translate('Italy'),
            /* I18N: Name of a country or state */
            'JAM' => I18N::translate('Jamaica'),
            //'JEY' => Jersey
            /* I18N: Name of a country or state */
            'JOR' => I18N::translate('Jordan'),
            /* I18N: Name of a country or state */
            'JPN' => I18N::translate('Japan'),
            /* I18N: Name of a country or state */
            'KAZ' => I18N::translate('Kazakhstan'),
            /* I18N: Name of a country or state */
            'KEN' => I18N::translate('Kenya'),
            /* I18N: Name of a country or state */
            'KGZ' => I18N::translate('Kyrgyzstan'),
            /* I18N: Name of a country or state */
            'KHM' => I18N::translate('Cambodia'),
            /* I18N: Name of a country or state */
            'KIR' => I18N::translate('Kiribati'),
            /* I18N: Name of a country or state */
            'KNA' => I18N::translate('Saint Kitts and Nevis'),
            /* I18N: Name of a country or state */
            'KOR' => I18N::translate('Korea'),
            /* I18N: Name of a country or state */
            'KWT' => I18N::translate('Kuwait'),
            /* I18N: Name of a country or state */
            'LAO' => I18N::translate('Laos'),
            /* I18N: Name of a country or state */
            'LBN' => I18N::translate('Lebanon'),
            /* I18N: Name of a country or state */
            'LBR' => I18N::translate('Liberia'),
            /* I18N: Name of a country or state */
            'LBY' => I18N::translate('Libya'),
            /* I18N: Name of a country or state */
            'LCA' => I18N::translate('Saint Lucia'),
            /* I18N: Name of a country or state */
            'LIE' => I18N::translate('Liechtenstein'),
            /* I18N: Name of a country or state */
            'LKA' => I18N::translate('Sri Lanka'),
            /* I18N: Name of a country or state */
            'LSO' => I18N::translate('Lesotho'),
            /* I18N: Name of a country or state */
            'LTU' => I18N::translate('Lithuania'),
            /* I18N: Name of a country or state */
            'LUX' => I18N::translate('Luxembourg'),
            /* I18N: Name of a country or state */
            'LVA' => I18N::translate('Latvia'),
            /* I18N: Name of a country or state */
            'MAC' => I18N::translate('Macau'),
            // MAF => Saint Martin
            /* I18N: Name of a country or state */
            'MAR' => I18N::translate('Morocco'),
            /* I18N: Name of a country or state */
            'MCO' => I18N::translate('Monaco'),
            /* I18N: Name of a country or state */
            'MDA' => I18N::translate('Moldova'),
            /* I18N: Name of a country or state */
            'MDG' => I18N::translate('Madagascar'),
            /* I18N: Name of a country or state */
            'MDV' => I18N::translate('Maldives'),
            /* I18N: Name of a country or state */
            'MEX' => I18N::translate('Mexico'),
            /* I18N: Name of a country or state */
            'MHL' => I18N::translate('Marshall Islands'),
            /* I18N: Name of a country or state */
            'MKD' => I18N::translate('Macedonia'),
            /* I18N: Name of a country or state */
            'MLI' => I18N::translate('Mali'),
            /* I18N: Name of a country or state */
            'MLT' => I18N::translate('Malta'),
            /* I18N: Name of a country or state */
            'MMR' => I18N::translate('Myanmar'),
            /* I18N: Name of a country or state */
            'MNG' => I18N::translate('Mongolia'),
            /* I18N: Name of a country or state */
            'MNP' => I18N::translate('Northern Mariana Islands'),
            /* I18N: Name of a country or state */
            'MNT' => I18N::translate('Montenegro'),
            /* I18N: Name of a country or state */
            'MOZ' => I18N::translate('Mozambique'),
            /* I18N: Name of a country or state */
            'MRT' => I18N::translate('Mauritania'),
            /* I18N: Name of a country or state */
            'MSR' => I18N::translate('Montserrat'),
            /* I18N: Name of a country or state */
            'MTQ' => I18N::translate('Martinique'),
            /* I18N: Name of a country or state */
            'MUS' => I18N::translate('Mauritius'),
            /* I18N: Name of a country or state */
            'MWI' => I18N::translate('Malawi'),
            /* I18N: Name of a country or state */
            'MYS' => I18N::translate('Malaysia'),
            /* I18N: Name of a country or state */
            'MYT' => I18N::translate('Mayotte'),
            /* I18N: Name of a country or state */
            'NAM' => I18N::translate('Namibia'),
            /* I18N: Name of a country or state */
            'NCL' => I18N::translate('New Caledonia'),
            /* I18N: Name of a country or state */
            'NER' => I18N::translate('Niger'),
            /* I18N: Name of a country or state */
            'NFK' => I18N::translate('Norfolk Island'),
            /* I18N: Name of a country or state */
            'NGA' => I18N::translate('Nigeria'),
            /* I18N: Name of a country or state */
            'NIC' => I18N::translate('Nicaragua'),
            /* I18N: Name of a country or state */
            'NIR' => I18N::translate('Northern Ireland'),
            /* I18N: Name of a country or state */
            'NIU' => I18N::translate('Niue'),
            /* I18N: Name of a country or state */
            'NLD' => I18N::translate('Netherlands'),
            /* I18N: Name of a country or state */
            'NOR' => I18N::translate('Norway'),
            /* I18N: Name of a country or state */
            'NPL' => I18N::translate('Nepal'),
            /* I18N: Name of a country or state */
            'NRU' => I18N::translate('Nauru'),
            /* I18N: Name of a country or state */
            'NZL' => I18N::translate('New Zealand'),
            /* I18N: Name of a country or state */
            'OMN' => I18N::translate('Oman'),
            /* I18N: Name of a country or state */
            'PAK' => I18N::translate('Pakistan'),
            /* I18N: Name of a country or state */
            'PAN' => I18N::translate('Panama'),
            /* I18N: Name of a country or state */
            'PCN' => I18N::translate('Pitcairn'),
            /* I18N: Name of a country or state */
            'PER' => I18N::translate('Peru'),
            /* I18N: Name of a country or state */
            'PHL' => I18N::translate('Philippines'),
            /* I18N: Name of a country or state */
            'PLW' => I18N::translate('Palau'),
            /* I18N: Name of a country or state */
            'PNG' => I18N::translate('Papua New Guinea'),
            /* I18N: Name of a country or state */
            'POL' => I18N::translate('Poland'),
            /* I18N: Name of a country or state */
            'PRI' => I18N::translate('Puerto Rico'),
            /* I18N: Name of a country or state */
            'PRK' => I18N::translate('North Korea'),
            /* I18N: Name of a country or state */
            'PRT' => I18N::translate('Portugal'),
            /* I18N: Name of a country or state */
            'PRY' => I18N::translate('Paraguay'),
            /* I18N: Name of a country or state */
            'PSE' => I18N::translate('Occupied Palestinian Territory'),
            /* I18N: Name of a country or state */
            'PYF' => I18N::translate('French Polynesia'),
            /* I18N: Name of a country or state */
            'QAT' => I18N::translate('Qatar'),
            /* I18N: Name of a country or state */
            'REU' => I18N::translate('Reunion'),
            /* I18N: Name of a country or state */
            'ROM' => I18N::translate('Romania'),
            /* I18N: Name of a country or state */
            'RUS' => I18N::translate('Russia'),
            /* I18N: Name of a country or state */
            'RWA' => I18N::translate('Rwanda'),
            /* I18N: Name of a country or state */
            'SAU' => I18N::translate('Saudi Arabia'),
            /* I18N: Name of a country or state */
            'SCT' => I18N::translate('Scotland'),
            /* I18N: Name of a country or state */
            'SDN' => I18N::translate('Sudan'),
            /* I18N: Name of a country or state */
            'SEA' => I18N::translate('At sea'),
            /* I18N: Name of a country or state */
            'SEN' => I18N::translate('Senegal'),
            /* I18N: Name of a country or state */
            'SER' => I18N::translate('Serbia'),
            /* I18N: Name of a country or state */
            'SGP' => I18N::translate('Singapore'),
            /* I18N: Name of a country or state */
            'SGS' => I18N::translate('South Georgia and the South Sandwich Islands'),
            /* I18N: Name of a country or state */
            'SHN' => I18N::translate('Saint Helena'),
            /* I18N: Name of a country or state */
            'SJM' => I18N::translate('Svalbard and Jan Mayen'),
            /* I18N: Name of a country or state */
            'SLB' => I18N::translate('Solomon Islands'),
            /* I18N: Name of a country or state */
            'SLE' => I18N::translate('Sierra Leone'),
            /* I18N: Name of a country or state */
            'SLV' => I18N::translate('El Salvador'),
            /* I18N: Name of a country or state */
            'SMR' => I18N::translate('San Marino'),
            /* I18N: Name of a country or state */
            'SOM' => I18N::translate('Somalia'),
            /* I18N: Name of a country or state */
            'SPM' => I18N::translate('Saint Pierre and Miquelon'),
            /* I18N: Name of a country or state */
            'SSD' => I18N::translate('South Sudan'),
            /* I18N: Name of a country or state */
            'STP' => I18N::translate('Sao Tome and Principe'),
            /* I18N: Name of a country or state */
            'SUR' => I18N::translate('Suriname'),
            /* I18N: Name of a country or state */
            'SVK' => I18N::translate('Slovakia'),
            /* I18N: Name of a country or state */
            'SVN' => I18N::translate('Slovenia'),
            /* I18N: Name of a country or state */
            'SWE' => I18N::translate('Sweden'),
            /* I18N: Name of a country or state */
            'SWZ' => I18N::translate('Swaziland'),
            // SXM => Sint Maarten
            /* I18N: Name of a country or state */
            'SYC' => I18N::translate('Seychelles'),
            /* I18N: Name of a country or state */
            'SYR' => I18N::translate('Syria'),
            /* I18N: Name of a country or state */
            'TCA' => I18N::translate('Turks and Caicos Islands'),
            /* I18N: Name of a country or state */
            'TCD' => I18N::translate('Chad'),
            /* I18N: Name of a country or state */
            'TGO' => I18N::translate('Togo'),
            /* I18N: Name of a country or state */
            'THA' => I18N::translate('Thailand'),
            /* I18N: Name of a country or state */
            'TJK' => I18N::translate('Tajikistan'),
            /* I18N: Name of a country or state */
            'TKL' => I18N::translate('Tokelau'),
            /* I18N: Name of a country or state */
            'TKM' => I18N::translate('Turkmenistan'),
            /* I18N: Name of a country or state */
            'TLS' => I18N::translate('Timor-Leste'),
            /* I18N: Name of a country or state */
            'TON' => I18N::translate('Tonga'),
            /* I18N: Name of a country or state */
            'TTO' => I18N::translate('Trinidad and Tobago'),
            /* I18N: Name of a country or state */
            'TUN' => I18N::translate('Tunisia'),
            /* I18N: Name of a country or state */
            'TUR' => I18N::translate('Turkey'),
            /* I18N: Name of a country or state */
            'TUV' => I18N::translate('Tuvalu'),
            /* I18N: Name of a country or state */
            'TWN' => I18N::translate('Taiwan'),
            /* I18N: Name of a country or state */
            'TZA' => I18N::translate('Tanzania'),
            /* I18N: Name of a country or state */
            'UGA' => I18N::translate('Uganda'),
            /* I18N: Name of a country or state */
            'UKR' => I18N::translate('Ukraine'),
            /* I18N: Name of a country or state */
            'UMI' => I18N::translate('US Minor Outlying Islands'),
            /* I18N: Name of a country or state */
            'URY' => I18N::translate('Uruguay'),
            /* I18N: Name of a country or state */
            'USA' => I18N::translate('United States'),
            /* I18N: Name of a country or state */
            'UZB' => I18N::translate('Uzbekistan'),
            /* I18N: Name of a country or state */
            'VAT' => I18N::translate('Vatican City'),
            /* I18N: Name of a country or state */
            'VCT' => I18N::translate('Saint Vincent and the Grenadines'),
            /* I18N: Name of a country or state */
            'VEN' => I18N::translate('Venezuela'),
            /* I18N: Name of a country or state */
            'VGB' => I18N::translate('British Virgin Islands'),
            /* I18N: Name of a country or state */
            'VIR' => I18N::translate('US Virgin Islands'),
            /* I18N: Name of a country or state */
            'VNM' => I18N::translate('Vietnam'),
            /* I18N: Name of a country or state */
            'VUT' => I18N::translate('Vanuatu'),
            /* I18N: Name of a country or state */
            'WLF' => I18N::translate('Wallis and Futuna'),
            /* I18N: Name of a country or state */
            'WLS' => I18N::translate('Wales'),
            /* I18N: Name of a country or state */
            'WSM' => I18N::translate('Samoa'),
            /* I18N: Name of a country or state */
            'YEM' => I18N::translate('Yemen'),
            /* I18N: Name of a country or state */
            'ZAF' => I18N::translate('South Africa'),
            /* I18N: Name of a country or state */
            'ZMB' => I18N::translate('Zambia'),
            /* I18N: Name of a country or state */
            'ZWE' => I18N::translate('Zimbabwe'),
        ];
    }

    /**
     * ISO3166 3 letter codes, with their 2 letter equivalent.
     * NOTE: this is not 1:1. ENG/SCO/WAL/NIR => GB
     * NOTE: this also includes champman codes and others. Should it?
     *
     * @return string[]
     */
    public function iso3166(): array
    {
        return [
            'ABW' => 'AW',
            'AFG' => 'AF',
            'AGO' => 'AO',
            'AIA' => 'AI',
            'ALA' => 'AX',
            'ALB' => 'AL',
            'AND' => 'AD',
            'ARE' => 'AE',
            'ARG' => 'AR',
            'ARM' => 'AM',
            'ASM' => 'AS',
            'ATA' => 'AQ',
            'ATF' => 'TF',
            'ATG' => 'AG',
            'AUS' => 'AU',
            'AUT' => 'AT',
            'AZE' => 'AZ',
            'BDI' => 'BI',
            'BEL' => 'BE',
            'BEN' => 'BJ',
            'BFA' => 'BF',
            'BGD' => 'BD',
            'BGR' => 'BG',
            'BHR' => 'BH',
            'BHS' => 'BS',
            'BIH' => 'BA',
            'BLR' => 'BY',
            'BLZ' => 'BZ',
            'BMU' => 'BM',
            'BOL' => 'BO',
            'BRA' => 'BR',
            'BRB' => 'BB',
            'BRN' => 'BN',
            'BTN' => 'BT',
            'BVT' => 'BV',
            'BWA' => 'BW',
            'CAF' => 'CF',
            'CAN' => 'CA',
            'CCK' => 'CC',
            'CHE' => 'CH',
            'CHL' => 'CL',
            'CHN' => 'CN',
            'CIV' => 'CI',
            'CMR' => 'CM',
            'COD' => 'CD',
            'COG' => 'CG',
            'COK' => 'CK',
            'COL' => 'CO',
            'COM' => 'KM',
            'CPV' => 'CV',
            'CRI' => 'CR',
            'CUB' => 'CU',
            'CXR' => 'CX',
            'CYM' => 'KY',
            'CYP' => 'CY',
            'CZE' => 'CZ',
            'DEU' => 'DE',
            'DJI' => 'DJ',
            'DMA' => 'DM',
            'DNK' => 'DK',
            'DOM' => 'DO',
            'DZA' => 'DZ',
            'ECU' => 'EC',
            'EGY' => 'EG',
            'ENG' => 'GB',
            'ERI' => 'ER',
            'ESH' => 'EH',
            'ESP' => 'ES',
            'EST' => 'EE',
            'ETH' => 'ET',
            'FIN' => 'FI',
            'FJI' => 'FJ',
            'FLK' => 'FK',
            'FRA' => 'FR',
            'FRO' => 'FO',
            'FSM' => 'FM',
            'GAB' => 'GA',
            'GBR' => 'GB',
            'GEO' => 'GE',
            'GHA' => 'GH',
            'GIB' => 'GI',
            'GIN' => 'GN',
            'GLP' => 'GP',
            'GMB' => 'GM',
            'GNB' => 'GW',
            'GNQ' => 'GQ',
            'GRC' => 'GR',
            'GRD' => 'GD',
            'GRL' => 'GL',
            'GTM' => 'GT',
            'GUF' => 'GF',
            'GUM' => 'GU',
            'GUY' => 'GY',
            'HKG' => 'HK',
            'HMD' => 'HM',
            'HND' => 'HN',
            'HRV' => 'HR',
            'HTI' => 'HT',
            'HUN' => 'HU',
            'IDN' => 'ID',
            'IND' => 'IN',
            'IOT' => 'IO',
            'IRL' => 'IE',
            'IRN' => 'IR',
            'IRQ' => 'IQ',
            'ISL' => 'IS',
            'ISR' => 'IL',
            'ITA' => 'IT',
            'JAM' => 'JM',
            'JOR' => 'JO',
            'JPN' => 'JA',
            'KAZ' => 'KZ',
            'KEN' => 'KE',
            'KGZ' => 'KG',
            'KHM' => 'KH',
            'KIR' => 'KI',
            'KNA' => 'KN',
            'KOR' => 'KO',
            'KWT' => 'KW',
            'LAO' => 'LA',
            'LBN' => 'LB',
            'LBR' => 'LR',
            'LBY' => 'LY',
            'LCA' => 'LC',
            'LIE' => 'LI',
            'LKA' => 'LK',
            'LSO' => 'LS',
            'LTU' => 'LT',
            'LUX' => 'LU',
            'LVA' => 'LV',
            'MAC' => 'MO',
            'MAR' => 'MA',
            'MCO' => 'MC',
            'MDA' => 'MD',
            'MDG' => 'MG',
            'MDV' => 'MV',
            'MEX' => 'MX',
            'MHL' => 'MH',
            'MKD' => 'MK',
            'MLI' => 'ML',
            'MLT' => 'MT',
            'MMR' => 'MM',
            'MNG' => 'MN',
            'MNP' => 'MP',
            'MNT' => 'ME',
            'MOZ' => 'MZ',
            'MRT' => 'MR',
            'MSR' => 'MS',
            'MTQ' => 'MQ',
            'MUS' => 'MU',
            'MWI' => 'MW',
            'MYS' => 'MY',
            'MYT' => 'YT',
            'NAM' => 'NA',
            'NCL' => 'NC',
            'NER' => 'NE',
            'NFK' => 'NF',
            'NGA' => 'NG',
            'NIC' => 'NI',
            'NIR' => 'GB',
            'NIU' => 'NU',
            'NLD' => 'NL',
            'NOR' => 'NO',
            'NPL' => 'NP',
            'NRU' => 'NR',
            'NZL' => 'NZ',
            'OMN' => 'OM',
            'PAK' => 'PK',
            'PAN' => 'PA',
            'PCN' => 'PN',
            'PER' => 'PE',
            'PHL' => 'PH',
            'PLW' => 'PW',
            'PNG' => 'PG',
            'POL' => 'PL',
            'PRI' => 'PR',
            'PRK' => 'KP',
            'PRT' => 'PO',
            'PRY' => 'PY',
            'PSE' => 'PS',
            'PYF' => 'PF',
            'QAT' => 'QA',
            'REU' => 'RE',
            'ROM' => 'RO',
            'RUS' => 'RU',
            'RWA' => 'RW',
            'SAU' => 'SA',
            'SCT' => 'GB',
            'SDN' => 'SD',
            'SEN' => 'SN',
            'SER' => 'RS',
            'SGP' => 'SG',
            'SGS' => 'GS',
            'SHN' => 'SH',
            'SJM' => 'SJ',
            'SLB' => 'SB',
            'SLE' => 'SL',
            'SLV' => 'SV',
            'SMR' => 'SM',
            'SOM' => 'SO',
            'SPM' => 'PM',
            'STP' => 'ST',
            'SUR' => 'SR',
            'SVK' => 'SK',
            'SVN' => 'SI',
            'SWE' => 'SE',
            'SWZ' => 'SZ',
            'SYC' => 'SC',
            'SYR' => 'SY',
            'TCA' => 'TC',
            'TCD' => 'TD',
            'TGO' => 'TG',
            'THA' => 'TH',
            'TJK' => 'TJ',
            'TKL' => 'TK',
            'TKM' => 'TM',
            'TLS' => 'TL',
            'TON' => 'TO',
            'TTO' => 'TT',
            'TUN' => 'TN',
            'TUR' => 'TR',
            'TUV' => 'TV',
            'TWN' => 'TW',
            'TZA' => 'TZ',
            'UGA' => 'UG',
            'UKR' => 'UA',
            'UMI' => 'UM',
            'URY' => 'UY',
            'USA' => 'US',
            'UZB' => 'UZ',
            'VAT' => 'VA',
            'VCT' => 'VC',
            'VEN' => 'VE',
            'VGB' => 'VG',
            'VIR' => 'VI',
            'VNM' => 'VN',
            'VUT' => 'VU',
            'WLF' => 'WF',
            'WLS' => 'GB',
            'WSM' => 'WS',
            'YEM' => 'YE',
            'ZAF' => 'ZA',
            'ZMB' => 'ZM',
            'ZWE' => 'ZW',
        ];
    }

    /**
     * Returns the translated country name based on the given two letter country code.
     *
     * @param string $twoLetterCode The two letter country code
     *
     * @return string
     */
    public function mapTwoLetterToName(string $twoLetterCode): string
    {
        $threeLetterCode = array_search($twoLetterCode, $this->iso3166(), true);
        $threeLetterCode = $threeLetterCode ?: '???';

        return $this->getAllCountries()[$threeLetterCode];
    }
}
