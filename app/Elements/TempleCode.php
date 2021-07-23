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

namespace Fisharebest\Webtrees\Elements;

use Fisharebest\Webtrees\I18N;

use function strtoupper;

/**
 * TEMPLE_CODE := {Size=4:5}
 * An abbreviation of the temple in which the LDS ordinances were performed.
 * (See Appendix B, page 96.)
 */
class TempleCode extends AbstractElement
{
    /**
     * Convert a value to a canonical form.
     *
     * @param string $value
     *
     * @return string
     */
    public function canonical(string $value): string
    {
        return strtoupper(parent::canonical($value));
    }

    /**
     * A list of controlled values for this element
     *
     * @return array<int|string,string>
     */
    public function values(): array
    {
        $values = [
            'ABA'   => /* I18N: Location of an LDS church temple */ I18N::translate('Aba, Nigeria'),
            'ACCRA' => /* I18N: Location of an LDS church temple */ I18N::translate('Accra, Ghana'),
            'ADELA' => /* I18N: Location of an LDS church temple */ I18N::translate('Adelaide, Australia'),
            'ALBER' => /* I18N: Location of an LDS church temple */ I18N::translate('Cardston, Alberta, Canada'),
            'ALBUQ' => /* I18N: Location of an LDS church temple */ I18N::translate('Albuquerque, New Mexico, United States'),
            'ANCHO' => /* I18N: Location of an LDS church temple */ I18N::translate('Anchorage, Alaska, United States'),
            'APIA'  => /* I18N: Location of an LDS church temple */ I18N::translate('Apia, Samoa'),
            'ARIZO' => /* I18N: Location of an LDS church temple */ I18N::translate('Mesa, Arizona, United States'),
            'ASUNC' => /* I18N: Location of an LDS church temple */ I18N::translate('Asuncion, Paraguay'),
            'ATLAN' => /* I18N: Location of an LDS church temple */ I18N::translate('Atlanta, Georgia, United States'),
            'BAIRE' => /* I18N: Location of an LDS church temple */ I18N::translate('Buenos Aires, Argentina'),
            'BILLI' => /* I18N: Location of an LDS church temple */ I18N::translate('Billings, Montana, United States'),
            'BIRMI' => /* I18N: Location of an LDS church temple */ I18N::translate('Birmingham, Alabama, United States'),
            'BISMA' => /* I18N: Location of an LDS church temple */ I18N::translate('Bismarck, North Dakota, United States'),
            'BOGOT' => /* I18N: Location of an LDS church temple */ I18N::translate('Bogota, Colombia'),
            'BOISE' => /* I18N: Location of an LDS church temple */ I18N::translate('Boise, Idaho, United States'),
            'BOSTO' => /* I18N: Location of an LDS church temple */ I18N::translate('Boston, Massachusetts, United States'),
            'BOUNT' => /* I18N: Location of an LDS church temple */ I18N::translate('Bountiful, Utah, United States'),
            'BRIGH' => /* I18N: Location of an LDS church temple */ I18N::translate('Brigham City, Utah, United States'),
            'BRISB' => /* I18N: Location of an LDS church temple */ I18N::translate('Brisbane, Australia'),
            'BROUG' => /* I18N: Location of an LDS church temple */ I18N::translate('Baton Rouge, Louisiana, United States'),
            'CALGA' => /* I18N: Location of an LDS church temple */ I18N::translate('Calgary, Alberta, Canada'),
            'CAMPI' => /* I18N: Location of an LDS church temple */ I18N::translate('Campinas, Brazil'),
            'CARAC' => /* I18N: Location of an LDS church temple */ I18N::translate('Caracas, Venezuela'),
            'CEBUP' => /* I18N: Location of an LDS church temple */ I18N::translate('Cebu City, Philippines'),
            'CHICA' => /* I18N: Location of an LDS church temple */ I18N::translate('Chicago, Illinois, United States'),
            'CIUJU' => /* I18N: Location of an LDS church temple */ I18N::translate('Ciudad Juarez, Mexico'),
            'COCHA' => /* I18N: Location of an LDS church temple */ I18N::translate('Cochabamba, Bolivia'),
            'COLJU' => /* I18N: Location of an LDS church temple */ I18N::translate('Colonia Juarez, Mexico'),
            'COLSC' => /* I18N: Location of an LDS church temple */ I18N::translate('Columbia, South Carolina, United States'),
            'COLUM' => /* I18N: Location of an LDS church temple */ I18N::translate('Columbus, Ohio, United States'),
            'COPEN' => /* I18N: Location of an LDS church temple */ I18N::translate('Copenhagen, Denmark'),
            'CORDO' => /* I18N: Location of an LDS church temple */ I18N::translate('Cordoba, Argentina'),
            'CRIVE' => /* I18N: Location of an LDS church temple */ I18N::translate('Columbia River, Washington, United States'),
            'CURIT' => /* I18N: Location of an LDS church temple */ I18N::translate('Curitiba, Brazil'),
            'DALLA' => /* I18N: Location of an LDS church temple */ I18N::translate('Dallas, Texas, United States'),
            'DENVE' => /* I18N: Location of an LDS church temple */ I18N::translate('Denver, Colorado, United States'),
            'DETRO' => /* I18N: Location of an LDS church temple */ I18N::translate('Detroit, Michigan, United States'),
            'DRAPE' => /* I18N: Location of an LDS church temple */ I18N::translate('Draper, Utah, United States'),
            'EDMON' => /* I18N: Location of an LDS church temple */ I18N::translate('Edmonton, Alberta, Canada'),
            'EHOUS' => /* I18N: Location of an historic LDS church temple - https://en.wikipedia.org/wiki/Endowment_house */ I18N::translate('Endowment House'),
            'FORTL' => /* I18N: Location of an LDS church temple */ I18N::translate('Fort Lauderdale, Florida, United States'),
            'FRANK' => /* I18N: Location of an LDS church temple */ I18N::translate('Frankfurt am Main, Germany'),
            'FREIB' => /* I18N: Location of an LDS church temple */ I18N::translate('Freiburg, Germany'),
            'FRESN' => /* I18N: Location of an LDS church temple */ I18N::translate('Fresno, California, United States'),
            'FUKUO' => /* I18N: Location of an LDS church temple */ I18N::translate('Fukuoka, Japan'),
            'GILAV' => /* I18N: Location of an LDS church temple */ I18N::translate('Gila Valley, Arizona, United States'),
            'GILBE' => /* I18N: Location of an LDS church temple */ I18N::translate('Gilbert, Arizona, United States'),
            'GUADA' => /* I18N: Location of an LDS church temple */ I18N::translate('Guadalajara, Mexico'),
            'GUATE' => /* I18N: Location of an LDS church temple */ I18N::translate('Guatemala City, Guatemala'),
            'GUAYA' => /* I18N: Location of an LDS church temple */ I18N::translate('Guayaquil, Ecuador'),
            'HAGUE' => /* I18N: Location of an LDS church temple */ I18N::translate('The Hague, Netherlands'),
            'HALIF' => /* I18N: Location of an LDS church temple */ I18N::translate('Halifax, Nova Scotia, Canada'),
            'HARTF' => /* I18N: Location of an LDS church temple */ I18N::translate('Hartford, Connecticut, United States'),
            'HAWAI' => /* I18N: Location of an LDS church temple */ I18N::translate('Laie, Hawaii, United States'),
            'HELSI' => /* I18N: Location of an LDS church temple */ I18N::translate('Helsinki, Finland'),
            'HERMO' => /* I18N: Location of an LDS church temple */ I18N::translate('Hermosillo, Mexico'),
            'HKONG' => /* I18N: Location of an LDS church temple */ I18N::translate('Hong Kong'),
            'HOUST' => /* I18N: Location of an LDS church temple */ I18N::translate('Houston, Texas, United States'),
            'IFALL' => /* I18N: Location of an LDS church temple */ I18N::translate('Idaho Falls, Idaho, United States'),
            'INDIA' => /* I18N: Location of an LDS church temple */ I18N::translate('Indianapolis, Indiana, United States'),
            'JOHAN' => /* I18N: Location of an LDS church temple */ I18N::translate('Johannesburg, South Africa'),
            'JRIVE' => /* I18N: Location of an LDS church temple */ I18N::translate('Jordan River, Utah, United States'),
            'KANSA' => /* I18N: Location of an LDS church temple */ I18N::translate('Kansas City, Missouri, United States'),
            'KONA'  => /* I18N: Location of an LDS church temple */ I18N::translate('Kona, Hawaii, United States'),
            'KYIV'  => /* I18N: Location of an LDS church temple */ I18N::translate('Kyiv, Ukraine'),
            'LANGE' => /* I18N: Location of an LDS church temple */ I18N::translate('Los Angeles, California, United States'),
            'LIMA'  => /* I18N: Location of an LDS church temple */ I18N::translate('Lima, Peru'),
            'LOGAN' => /* I18N: Location of an LDS church temple */ I18N::translate('Logan, Utah, United States'),
            'LONDO' => /* I18N: Location of an LDS church temple */ I18N::translate('London, England'),
            'LOUIS' => /* I18N: Location of an LDS church temple */ I18N::translate('Louisville, Kentucky, United States'),
            'LUBBO' => /* I18N: Location of an LDS church temple */ I18N::translate('Lubbock, Texas, United States'),
            'LVEGA' => /* I18N: Location of an LDS church temple */ I18N::translate('Las Vegas, Nevada, United States'),
            'MADRI' => /* I18N: Location of an LDS church temple */ I18N::translate('Madrid, Spain'),
            'MANAU' => /* I18N: Location of an LDS church temple */ I18N::translate('Manaus, Brazil'),
            'MANHA' => /* I18N: Location of an LDS church temple */ I18N::translate('Manhattan, New York, United States'),
            'MANIL' => /* I18N: Location of an LDS church temple */ I18N::translate('Manila, Philippines'),
            'MANTI' => /* I18N: Location of an LDS church temple */ I18N::translate('Manti, Utah, United States'),
            'MEDFO' => /* I18N: Location of an LDS church temple */ I18N::translate('Medford, Oregon, United States'),
            'MELBO' => /* I18N: Location of an LDS church temple */ I18N::translate('Melbourne, Australia'),
            'MEMPH' => /* I18N: Location of an LDS church temple */ I18N::translate('Memphis, Tennessee, United States'),
            'MERID' => /* I18N: Location of an LDS church temple */ I18N::translate('Merida, Mexico'),
            'MEXIC' => /* I18N: Location of an LDS church temple */ I18N::translate('Mexico City, Mexico'),
            'MNTVD' => /* I18N: Location of an LDS church temple */ I18N::translate('Montevideo, Uruguay'),
            'MONTE' => /* I18N: Location of an LDS church temple */ I18N::translate('Monterrey, Mexico'),
            'MONTI' => /* I18N: Location of an LDS church temple */ I18N::translate('Monticello, Utah, United States'),
            'MONTR' => /* I18N: Location of an LDS church temple */ I18N::translate('Montreal, Quebec, Canada'),
            'MTIMP' => /* I18N: Location of an LDS church temple */ I18N::translate('Mount Timpanogos, Utah, United States'),
            'NASHV' => /* I18N: Location of an LDS church temple */ I18N::translate('Nashville, Tennessee, United States'),
            'NAUV2' => /* I18N: Location of an LDS church temple */ I18N::translate('Nauvoo (new), Illinois, United States'),
            'NAUVO' => /* I18N: Location of an LDS church temple */ I18N::translate('Nauvoo (original), Illinois, United States'),
            'NBEAC' => /* I18N: Location of an LDS church temple */ I18N::translate('Newport Beach, California, United States'),
            'NUKUA' => /* I18N: Location of an LDS church temple */ I18N::translate('Nuku’Alofa, Tonga'),
            'NYORK' => /* I18N: Location of an LDS church temple */ I18N::translate('New York, New York, United States'),
            'NZEAL' => /* I18N: Location of an LDS church temple */ I18N::translate('Hamilton, New Zealand'),
            'OAKLA' => /* I18N: Location of an LDS church temple */ I18N::translate('Oakland, California, United States'),
            'OAXAC' => /* I18N: Location of an LDS church temple */ I18N::translate('Oaxaca, Mexico'),
            'OGDEN' => /* I18N: Location of an LDS church temple */ I18N::translate('Ogden, Utah, United States'),
            'OKLAH' => /* I18N: Location of an LDS church temple */ I18N::translate('Oklahoma City, Oklahoma, United States'),
            'OQUIR' => /* I18N: Location of an LDS church temple */ I18N::translate('Oquirrh Mountain, Utah, United States'),
            'ORLAN' => /* I18N: Location of an LDS church temple */ I18N::translate('Orlando, Florida, United States'),
            'PALEG' => /* I18N: Location of an LDS church temple */ I18N::translate('Porto Alegre, Brazil'),
            'PALMY' => /* I18N: Location of an LDS church temple */ I18N::translate('Palmyra, New York, United States'),
            'PANAM' => /* I18N: Location of an LDS church temple */ I18N::translate('Panama City, Panama'),
            'PAPEE' => /* I18N: Location of an LDS church temple */ I18N::translate('Papeete, Tahiti'),
            'PAYSO' => /* I18N: Location of an LDS church temple */ I18N::translate('Payson, Utah, United States'),
            'PERTH' => /* I18N: Location of an LDS church temple */ I18N::translate('Perth, Australia'),
            'PHOEN' => /* I18N: Location of an LDS church temple */ I18N::translate('Phoenix, Arizona, United States'),
            'POFFI' => /* I18N: Location of an historic LDS church temple - https://en.wikipedia.org/wiki/President_of_the_Church */ I18N::translate('President’s Office'),
            'PORTL' => /* I18N: Location of an LDS church temple */ I18N::translate('Portland, Oregon, United States'),
            'PREST' => /* I18N: Location of an LDS church temple */ I18N::translate('Preston, England'),
            'PROCC' => /* I18N: Location of an LDS church temple */ I18N::translate('Provo City Center, Utah, United States'),
            'PROVO' => /* I18N: Location of an LDS church temple */ I18N::translate('Provo, Utah, United States'),
            'QUETZ' => /* I18N: Location of an LDS church temple */ I18N::translate('Quetzaltenango, Guatemala'),
            'RALEI' => /* I18N: Location of an LDS church temple */ I18N::translate('Raleigh, North Carolina, United States'),
            'RECIF' => /* I18N: Location of an LDS church temple */ I18N::translate('Recife, Brazil'),
            'REDLA' => /* I18N: Location of an LDS church temple */ I18N::translate('Redlands, California, United States'),
            'REGIN' => /* I18N: Location of an LDS church temple */ I18N::translate('Regina, Saskatchewan, Canada'),
            'RENO'  => /* I18N: Location of an LDS church temple */ I18N::translate('Reno, Nevada, United States'),
            'REXBU' => /* I18N: Location of an LDS church temple */ I18N::translate('Rexburg, Idaho, United States'),
            'SACRA' => /* I18N: Location of an LDS church temple */ I18N::translate('Sacramento, California, United States'),
            'SANSA' => /* I18N: Location of an LDS church temple */ I18N::translate('San Salvador, El Salvador'),
            'SANTI' => /* I18N: Location of an LDS church temple */ I18N::translate('Santiago, Chile'),
            'SANTO' => /* I18N: Location of an LDS church temple */ I18N::translate('San Antonio, Texas, United States'),
            'SDIEG' => /* I18N: Location of an LDS church temple */ I18N::translate('San Diego, California, United States'),
            'SDOMI' => /* I18N: Location of an LDS church temple */ I18N::translate('Santo Domingo, Dominican Republic'),
            'SEATT' => /* I18N: Location of an LDS church temple */ I18N::translate('Seattle, Washington, United States'),
            'SEOUL' => /* I18N: Location of an LDS church temple */ I18N::translate('Seoul, Korea'),
            'SGEOR' => /* I18N: Location of an LDS church temple */ I18N::translate('St. George, Utah, United States'),
            'SJOSE' => /* I18N: Location of an LDS church temple */ I18N::translate('San Jose, Costa Rica'),
            'SLAKE' => /* I18N: Location of an LDS church temple */ I18N::translate('Salt Lake City, Utah, United States'),
            'SLOUI' => /* I18N: Location of an LDS church temple */ I18N::translate('St. Louis, Missouri, United States'),
            'SNOWF' => /* I18N: Location of an LDS church temple */ I18N::translate('Snowflake, Arizona, United States'),
            'SPAUL' => /* I18N: Location of an LDS church temple */ I18N::translate('Sao Paulo, Brazil'),
            'SPMIN' => /* I18N: Location of an LDS church temple */ I18N::translate('St. Paul, Minnesota, United States'),
            'SPOKA' => /* I18N: Location of an LDS church temple */ I18N::translate('Spokane, Washington, United States'),
            'STOCK' => /* I18N: Location of an LDS church temple */ I18N::translate('Stockholm, Sweden'),
            'SUVA'  => /* I18N: Location of an LDS church temple */ I18N::translate('Suva, Fiji'),
            'SWISS' => /* I18N: Location of an LDS church temple */ I18N::translate('Bern, Switzerland'),
            'SYDNE' => /* I18N: Location of an LDS church temple */ I18N::translate('Sydney, Australia'),
            'TAIPE' => /* I18N: Location of an LDS church temple */ I18N::translate('Taipei, Taiwan'),
            'TAMPI' => /* I18N: Location of an LDS church temple */ I18N::translate('Tampico, Mexico'),
            'TEGUC' => /* I18N: Location of an LDS church temple */ I18N::translate('Tegucigalpa, Honduras'),
            'TGUTI' => /* I18N: Location of an LDS church temple */ I18N::translate('Tuxtla Gutierrez, Mexico'),
            'TIJUA' => /* I18N: Location of an LDS church temple */ I18N::translate('Tijuana, Mexico'),
            'TOKYO' => /* I18N: Location of an LDS church temple */ I18N::translate('Tokyo, Japan'),
            'TORNO' => /* I18N: Location of an LDS church temple */ I18N::translate('Toronto, Ontario, Canada'),
            'TRUJI' => /* I18N: Location of an LDS church temple */ I18N::translate('Trujillo, Peru'),
            'TWINF' => /* I18N: Location of an LDS church temple */ I18N::translate('Twin Falls, Idaho, United States'),
            'VANCO' => /* I18N: Location of an LDS church temple */ I18N::translate('Vancouver, British Columbia, Canada'),
            'VERAC' => /* I18N: Location of an LDS church temple */ I18N::translate('Veracruz, Mexico'),
            'VERNA' => /* I18N: Location of an LDS church temple */ I18N::translate('Vernal, Utah, United States'),
            'VILLA' => /* I18N: Location of an LDS church temple */ I18N::translate('Villa Hermosa, Mexico'),
            'WASHI' => /* I18N: Location of an LDS church temple */ I18N::translate('Washington, District of Columbia, United States'),
            'WINTE' => /* I18N: Location of an LDS church temple */ I18N::translate('Winter Quarters, Nebraska, United States'),
        ];

        uasort($values, I18N::comparator());
        $values = ['' => I18N::translate('No temple - living ordinance')] + $values;

        return $values;
    }
}
