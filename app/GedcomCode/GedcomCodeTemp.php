<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\GedcomCode;

use Fisharebest\Webtrees\I18N;

/**
 * Class GedcomCodeTemp - Functions and logic for GEDCOM "TEMP" codes
 */
class GedcomCodeTemp
{
    /**
     * A list of GEDCOM tags that require a TEMP subtag
     *
     * @param string $tag
     *
     * @return bool
     */
    public static function isTagLDS(string $tag): bool
    {
        return $tag === 'BAPL' || $tag === 'CONL' || $tag === 'ENDL' || $tag === 'SLGC' || $tag === 'SLGS';
    }

    /**
     * A list of all temple codes, from the GEDCOM 5.5.1 specification
     *
     * Note that this list is out-of-date. We could add recently built
     * temples, but what codes would we use?
     *
     * @link http://en.wikipedia.org/wiki/List_of_temples_of_The_Church_of_Jesus_Christ_of_Latter-day_Saints
     * @link http://www.ldschurchtemples.com/codes/
     *
     * @return array<string>
     */
    public static function templeCodes(): array
    {
        return [
            'ABA',
            'ACCRA',
            'ADELA',
            'ALBER',
            'ALBUQ',
            'ANCHO',
            'ARIZO',
            'ASUNC',
            'ATLAN',
            'BAIRE',
            'BILLI',
            'BIRMI',
            'BISMA',
            'BOGOT',
            'BOISE',
            'BOSTO',
            'BOUNT',
            'BRIGH',
            'BRISB',
            'BROUG',
            'CALGA',
            'CAMPI',
            'CARAC',
            'CEBUP',
            'CHICA',
            'CIUJU',
            'COCHA',
            'COLJU',
            'COLSC',
            'COLUM',
            'COPEN',
            'CORDO',
            'CRIVE',
            'CURIT',
            'DALLA',
            'DENVE',
            'DETRO',
            'DRAPE',
            'EDMON',
            'EHOUS',
            'FORTL',
            'FRANK',
            'FREIB',
            'FRESN',
            'FUKUO',
            'GILAV',
            'GILBE',
            'GUADA',
            'GUATE',
            'GUAYA',
            'HAGUE',
            'HALIF',
            'HARTF',
            'HAWAI',
            'HELSI',
            'HERMO',
            'HKONG',
            'HOUST',
            'IFALL',
            'INDIA',
            'JOHAN',
            'JRIVE',
            'KANSA',
            'KONA',
            'KYIV',
            'LANGE',
            'LIMA',
            'LOGAN',
            'LONDO',
            'LOUIS',
            'LUBBO',
            'LVEGA',
            'MADRI',
            'MANAU',
            'MANHA',
            'MANIL',
            'MANTI',
            'MEDFO',
            'MELBO',
            'MEMPH',
            'MERID',
            'MEXIC',
            'MNTVD',
            'MONTE',
            'MONTI',
            'MONTR',
            'MTIMP',
            'NASHV',
            'NAUV2',
            'NAUVO',
            'NBEAC',
            'NUKUA',
            'NYORK',
            'NZEAL',
            'OAKLA',
            'OAXAC',
            'OGDEN',
            'OKLAH',
            'OQUIR',
            'ORLAN',
            'PALEG',
            'PALMY',
            'PANAM',
            'PAPEE',
            'PAYSO',
            'PERTH',
            'PHOEN',
            'POFFI',
            'PORTL',
            'PREST',
            'PROCC',
            'PROVO',
            'QUETZ',
            'RALEI',
            'RECIF',
            'REDLA',
            'REGIN',
            'RENO',
            'REXBU',
            'SACRA',
            'SAMOA',
            'SANTI',
            'SANSA',
            'SANTO',
            'SDIEG',
            'SDOMI',
            'SEATT',
            'SEOUL',
            'SGEOR',
            'SJOSE',
            'SLAKE',
            'SLOUI',
            'SNOWF',
            'SPAUL',
            'SPMIN',
            'SPOKA',
            'STOCK',
            'SUVA',
            'SWISS',
            'SYDNE',
            'TAIPE',
            'TAMPI',
            'TEGUC',
            'TGUTI',
            'TIHUA',
            'TOKYO',
            'TORNO',
            'TRUJI',
            'TWINF',
            'VANCO',
            'VERAC',
            'VERNA',
            'VILLA',
            'WASHI',
            'WINTE',
        ];
    }

    /**
     * Get the localized name for a temple code
     *
     * @param string $temple_code
     *
     * @return string
     */
    public static function templeName(string $temple_code): string
    {
        switch ($temple_code) {
            case 'ABA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Aba, Nigeria');
            case 'ACCRA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Accra, Ghana');
            case 'ADELA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Adelaide, Australia');
            case 'ALBER':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Cardston, Alberta, Canada');
            case 'ALBUQ':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Albuquerque, New Mexico, United States');
            case 'ANCHO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Anchorage, Alaska, United States');
            case 'APIA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Apia, Samoa');
            case 'ARIZO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Mesa, Arizona, United States');
            case 'ASUNC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Asuncion, Paraguay');
            case 'ATLAN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Atlanta, Georgia, United States');
            case 'BAIRE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Buenos Aires, Argentina');
            case 'BILLI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Billings, Montana, United States');
            case 'BIRMI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Birmingham, Alabama, United States');
            case 'BISMA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Bismarck, North Dakota, United States');
            case 'BOGOT':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Bogota, Colombia');
            case 'BOISE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Boise, Idaho, United States');
            case 'BOSTO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Boston, Massachusetts, United States');
            case 'BOUNT':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Bountiful, Utah, United States');
            case 'BRIGH':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Brigham City, Utah, United States');
            case 'BRISB':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Brisbane, Australia');
            case 'BROUG':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Baton Rouge, Louisiana, United States');
            case 'CALGA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Calgary, Alberta, Canada');
            case 'CAMPI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Campinas, Brazil');
            case 'CARAC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Caracas, Venezuela');
            case 'CEBUP':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Cebu City, Philippines');
            case 'CHICA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Chicago, Illinois, United States');
            case 'CIUJU':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Ciudad Juarez, Mexico');
            case 'COCHA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Cochabamba, Bolivia');
            case 'COLJU':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Colonia Juarez, Mexico');
            case 'COLSC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Columbia, South Carolina, United States');
            case 'COLUM':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Columbus, Ohio, United States');
            case 'COPEN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Copenhagen, Denmark');
            case 'CORDO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Cordoba, Argentina');
            case 'CRIVE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Columbia River, Washington, United States');
            case 'CURIT':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Curitiba, Brazil');
            case 'DALLA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Dallas, Texas, United States');
            case 'DENVE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Denver, Colorado, United States');
            case 'DETRO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Detroit, Michigan, United States');
            case 'DRAPE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Draper, Utah, United States');
            case 'EDMON':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Edmonton, Alberta, Canada');
            case 'EHOUS':
                /* I18N: Location of an historic LDS church temple - http://en.wikipedia.org/wiki/Endowment_house */
                return I18N::translate('Endowment House');
            case 'FORTL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Fort Lauderdale, Florida, United States');
            case 'FRANK':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Frankfurt am Main, Germany');
            case 'FREIB':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Freiburg, Germany');
            case 'FRESN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Fresno, California, United States');
            case 'FUKUO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Fukuoka, Japan');
            case 'GILAV':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Gila Valley, Arizona, United States');
            case 'GILBE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Gilbert, Arizona, United States');
            case 'GUADA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Guadalajara, Mexico');
            case 'GUATE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Guatemala City, Guatemala');
            case 'GUAYA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Guayaquil, Ecuador');
            case 'HAGUE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('The Hague, Netherlands');
            case 'HALIF':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Halifax, Nova Scotia, Canada');
            case 'HARTF':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Hartford, Connecticut, United States');
            case 'HAWAI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Laie, Hawaii, United States');
            case 'HELSI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Helsinki, Finland');
            case 'HERMO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Hermosillo, Mexico');
            case 'HKONG':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Hong Kong');
            case 'HOUST':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Houston, Texas, United States');
            case 'IFALL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Idaho Falls, Idaho, United States');
            case 'INDIA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Indianapolis, Indiana, United States');
            case 'JOHAN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Johannesburg, South Africa');
            case 'JRIVE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Jordan River, Utah, United States');
            case 'KANSA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Kansas City, Missouri, United States');
            case 'KONA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Kona, Hawaii, United States');
            case 'KYIV':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Kiev, Ukraine');
            case 'LANGE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Los Angeles, California, United States');
            case 'LIMA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Lima, Peru');
            case 'LOGAN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Logan, Utah, United States');
            case 'LONDO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('London, England');
            case 'LOUIS':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Louisville, Kentucky, United States');
            case 'LUBBO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Lubbock, Texas, United States');
            case 'LVEGA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Las Vegas, Nevada, United States');
            case 'MADRI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Madrid, Spain');
            case 'MANAU':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Manaus, Brazil');
            case 'MANHA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Manhattan, New York, United States');
            case 'MANIL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Manila, Philippines');
            case 'MANTI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Manti, Utah, United States');
            case 'MEDFO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Medford, Oregon, United States');
            case 'MELBO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Melbourne, Australia');
            case 'MEMPH':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Memphis, Tennessee, United States');
            case 'MERID':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Merida, Mexico');
            case 'MEXIC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Mexico City, Mexico');
            case 'MNTVD':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Montevideo, Uruguay');
            case 'MONTE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Monterrey, Mexico');
            case 'MONTI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Monticello, Utah, United States');
            case 'MONTR':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Montreal, Quebec, Canada');
            case 'MTIMP':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Mount Timpanogos, Utah, United States');
            case 'NASHV':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Nashville, Tennessee, United States');
            case 'NAUV2':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Nauvoo (new), Illinois, United States');
            case 'NAUVO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Nauvoo (original), Illinois, United States');
            case 'NBEAC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Newport Beach, California, United States');
            case 'NUKUA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Nuku’Alofa, Tonga');
            case 'NYORK':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('New York, New York, United States');
            case 'NZEAL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Hamilton, New Zealand');
            case 'OAKLA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Oakland, California, United States');
            case 'OAXAC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Oaxaca, Mexico');
            case 'OGDEN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Ogden, Utah, United States');
            case 'OKLAH':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Oklahoma City, Oklahoma, United States');
            case 'OQUIR':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Oquirrh Mountain, Utah, United States');
            case 'ORLAN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Orlando, Florida, United States');
            case 'PALEG':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Porto Alegre, Brazil');
            case 'PALMY':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Palmyra, New York, United States');
            case 'PANAM':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Panama City, Panama');
            case 'PAPEE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Papeete, Tahiti');
            case 'PAYSO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Payson, Utah, United States');
            case 'PERTH':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Perth, Australia');
            case 'PHOEN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Phoenix, Arizona, United States');
            case 'POFFI':
                /* I18N: I18N: Location of an historic LDS church temple - http://en.wikipedia.org/wiki/President_of_the_Church */
                return I18N::translate('President’s Office');
            case 'PORTL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Portland, Oregon, United States');
            case 'PREST':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Preston, England');
            case 'PROCC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Provo City Center, Utah, United States');
            case 'PROVO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Provo, Utah, United States');
            case 'QUETZ':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Quetzaltenango, Guatemala');
            case 'RALEI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Raleigh, North Carolina, United States');
            case 'RECIF':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Recife, Brazil');
            case 'REDLA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Redlands, California, United States');
            case 'REGIN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Regina, Saskatchewan, Canada');
            case 'RENO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Reno, Nevada, United States');
            case 'REXBU':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Rexburg, Idaho, United States');
            case 'SACRA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Sacramento, California, United States');
            case 'SANSA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('San Salvador, El Salvador');
            case 'SANTI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Santiago, Chile');
            case 'SANTO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('San Antonio, Texas, United States');
            case 'SDIEG':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('San Diego, California, United States');
            case 'SDOMI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Santo Domingo, Dominican Republic');
            case 'SEATT':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Seattle, Washington, United States');
            case 'SEOUL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Seoul, Korea');
            case 'SGEOR':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('St. George, Utah, United States');
            case 'SJOSE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('San Jose, Costa Rica');
            case 'SLAKE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Salt Lake City, Utah, United States');
            case 'SLOUI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('St. Louis, Missouri, United States');
            case 'SNOWF':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Snowflake, Arizona, United States');
            case 'SPAUL':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Sao Paulo, Brazil');
            case 'SPMIN':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('St. Paul, Minnesota, United States');
            case 'SPOKA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Spokane, Washington, United States');
            case 'STOCK':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Stockholm, Sweden');
            case 'SUVA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Suva, Fiji');
            case 'SWISS':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Bern, Switzerland');
            case 'SYDNE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Sydney, Australia');
            case 'TAIPE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Taipei, Taiwan');
            case 'TAMPI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Tampico, Mexico');
            case 'TEGUC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Tegucigalpa, Honduras');
            case 'TGUTI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Tuxtla Gutierrez, Mexico');
            case 'TIJUA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Tijuana, Mexico');
            case 'TOKYO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Tokyo, Japan');
            case 'TORNO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Toronto, Ontario, Canada');
            case 'TRUJI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Trujillo, Peru');
            case 'TWINF':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Twin Falls, Idaho, United States');
            case 'VANCO':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Vancouver, British Columbia, Canada');
            case 'VERAC':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Veracruz, Mexico');
            case 'VERNA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Vernal, Utah, United States');
            case 'VILLA':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Villa Hermosa, Mexico');
            case 'WASHI':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Washington, District of Columbia, United States');
            case 'WINTE':
                /* I18N: Location of an LDS church temple */
                return I18N::translate('Winter Quarters, Nebraska, United States');
            default:
                return $temple_code;
        }
    }

    /**
     * A sorted list of all temple names
     *
     * @return array<string>
     */
    public static function templeNames(): array
    {
        $temple_names = [];
        foreach (self::templeCodes() as $temple_code) {
            $temple_names[$temple_code] = self::templeName($temple_code);
        }
        uasort($temple_names, '\Fisharebest\Webtrees\I18N::strcasecmp');

        return $temple_names;
    }
}
