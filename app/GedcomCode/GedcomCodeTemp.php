<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\GedcomCode;

use Fisharebest\Webtrees\I18N;

/**
 * Class GedcomCodeTemp - Functions and logic for GEDCOM "TEMP" codes
 */
class GedcomCodeTemp {
	/**
	 * A list of GEDCOM tags that require a TEMP subtag
	 *
	 * @param string $tag
	 *
	 * @return bool
	 */
	public static function isTagLDS($tag) {
		return $tag === 'BAPL' || $tag === 'CONL' || $tag === 'ENDL' || $tag === 'SLGC' || $tag === 'SLGS';
	}

	/**
	 * A list of all temple codes, from the GEDCOM 5.5.1 specification
	 *
	 * Note that this list is out-of-date.  We could add recently built
	 * temples, but what codes would we use?
	 *
	 * @link http://en.wikipedia.org/wiki/List_of_temples_of_The_Church_of_Jesus_Christ_of_Latter-day_Saints
	 *
	 * @return string[]
	 */
	public static function templeCodes() {
		return array(
			'ABA', 'ACCRA', 'ADELA', 'ALBER', 'ALBUQ', 'ANCHO', 'ARIZO', 'ASUNC',
			'ATLAN', 'BAIRE', 'BILLI', 'BIRMI', 'BISMA', 'BOGOT', 'BOISE', 'BOSTO',
			'BOUNT', 'BRISB', 'BROUG', 'CAMPI', 'CARAC', 'CHICA', 'CIUJU', 'COCHA',
			'COLJU', 'COLSC', 'COLUM', 'COPEN', 'CRIVE', 'DALLA', 'DENVE', 'DETRO',
			'EDMON', 'EHOUS', 'FRANK', 'FREIB', 'FRESN', 'FUKUO', 'GUADA', 'GUATE',
			'GUAYA', 'HAGUE', 'HALIF', 'HARTF', 'HAWAI', 'HELSI', 'HERMO', 'HKONG',
			'HOUST', 'IFALL', 'JOHAN', 'JRIVE', 'KIEV', 'KONA', 'LANGE', 'LIMA',
			'LOGAN', 'LONDO', 'LOUIS', 'LUBBO', 'LVEGA', 'MADRI', 'MANIL', 'MANTI',
			'MEDFO', 'MELBO', 'MEMPH', 'MERID', 'MEXIC', 'MNTVD', 'MONTE', 'MONTI',
			'MONTR', 'MTIMP', 'NASHV', 'NAUV2', 'NAUVO', 'NBEAC', 'NUKUA', 'NYORK',
			'NZEAL', 'OAKLA', 'OAXAC', 'OGDEN', 'OKLAH', 'ORLAN', 'PALEG', 'PALMY',
			'PAPEE', 'PERTH', 'POFFI', 'PORTL', 'PREST', 'PROVO', 'RALEI', 'RECIF',
			'REDLA', 'REGIN', 'RENO', 'SACRA', 'SAMOA', 'SANTI', 'SANTO', 'SDIEG',
			'SDOMI', 'SEATT', 'SEOUL', 'SGEOR', 'SJOSE', 'SLAKE', 'SLOUI', 'SNOWF',
			'SPAUL', 'SPMIN', 'SPOKA', 'STOCK', 'SUVA', 'SWISS', 'SYDNE', 'TAIPE',
			'TAMPI', 'TGUTI', 'TOKYO', 'TORNO', 'VERAC', 'VERNA', 'VILLA', 'WASHI',
			'WINTE',
		);
	}

	/**
	 * Get the localized name for a temple code
	 *
	 * @param string $temple_code
	 *
	 * @return string
	 */
	public static function templeName($temple_code) {
		switch ($temple_code) {
		case 'ABA':
			return I18N::translate('Aba, Nigeria');
		case 'ACCRA':
			return I18N::translate('Accra, Ghana');
		case 'ADELA':
			return I18N::translate('Adelaide, Australia');
		case 'ALBER':
			return I18N::translate('Cardston, Alberta, Canada');
		case 'ALBUQ':
			return I18N::translate('Albuquerque, New Mexico');
		case 'ANCHO':
			return I18N::translate('Anchorage, Alaska');
		case 'ARIZO':
			return I18N::translate('Mesa, Arizona');
		case 'ASUNC':
			return I18N::translate('Asuncion, Paraguay');
		case 'ATLAN':
			return I18N::translate('Atlanta, Georgia');
		case 'BAIRE':
			return I18N::translate('Buenos Aires, Argentina');
		case 'BILLI':
			return I18N::translate('Billings, Montana');
		case 'BIRMI':
			return I18N::translate('Birmingham, Alabama');
		case 'BISMA':
			return I18N::translate('Bismarck, North Dakota');
		case 'BOGOT':
			return I18N::translate('Bogota, Colombia');
		case 'BOISE':
			return I18N::translate('Boise, Idaho');
		case 'BOSTO':
			return I18N::translate('Boston, Massachusetts');
		case 'BOUNT':
			return I18N::translate('Bountiful, Utah');
		case 'BRISB':
			return I18N::translate('Brisbane, Australia');
		case 'BROUG':
			return I18N::translate('Baton Rouge, Louisiana');
		case 'CAMPI':
			return I18N::translate('Campinas, Brazil');
		case 'CARAC':
			return I18N::translate('Caracas, Venezuela');
		case 'CHICA':
			return I18N::translate('Chicago, Illinois');
		case 'CIUJU':
			return I18N::translate('Ciudad Juarez, Mexico');
		case 'COCHA':
			return I18N::translate('Cochabamba, Bolivia');
		case 'COLJU':
			return I18N::translate('Colonia Juarez, Mexico');
		case 'COLSC':
			return I18N::translate('Columbia, South Carolina');
		case 'COLUM':
			return I18N::translate('Columbus, Ohio');
		case 'COPEN':
			return I18N::translate('Copenhagen, Denmark');
		case 'CRIVE':
			return I18N::translate('Columbia River, Washington');
		case 'DALLA':
			return I18N::translate('Dallas, Texas');
		case 'DENVE':
			return I18N::translate('Denver, Colorado');
		case 'DETRO':
			return I18N::translate('Detroit, Michigan');
		case 'EDMON':
			return I18N::translate('Edmonton, Alberta, Canada');
		case 'EHOUS':
			return
				/* I18N: http://en.wikipedia.org/wiki/Endowment_house */
				I18N::translate('Endowment House');
		case 'FRANK':
			return I18N::translate('Frankfurt am Main, Germany');
		case 'FREIB':
			return I18N::translate('Freiburg, Germany');
		case 'FRESN':
			return I18N::translate('Fresno, California');
		case 'FUKUO':
			return I18N::translate('Fukuoka, Japan');
		case 'GUADA':
			return I18N::translate('Guadalajara, Mexico');
		case 'GUATE':
			return I18N::translate('Guatemala City, Guatemala');
		case 'GUAYA':
			return I18N::translate('Guayaquil, Ecuador');
		case 'HAGUE':
			return I18N::translate('The Hague, Netherlands');
		case 'HALIF':
			return I18N::translate('Halifax, Nova Scotia, Canada');
		case 'HARTF':
			return I18N::translate('Hartford, Connecticut');
		case 'HAWAI':
			return I18N::translate('Laie, Hawaii');
		case 'HELSI':
			return I18N::translate('Helsinki, Finland');
		case 'HERMO':
			return I18N::translate('Hermosillo, Mexico');
		case 'HKONG':
			return I18N::translate('Hong Kong');
		case 'HOUST':
			return I18N::translate('Houston, Texas');
		case 'IFALL':
			return I18N::translate('Idaho Falls, Idaho');
		case 'JOHAN':
			return I18N::translate('Johannesburg, South Africa');
		case 'JRIVE':
			return I18N::translate('Jordan River, Utah');
		case 'KIEV':
			return I18N::translate('Kiev, Ukraine');
		case 'KONA':
			return I18N::translate('Kona, Hawaii');
		case 'LANGE':
			return I18N::translate('Los Angeles, California');
		case 'LIMA':
			return I18N::translate('Lima, Peru');
		case 'LOGAN':
			return I18N::translate('Logan, Utah');
		case 'LONDO':
			return I18N::translate('London, England');
		case 'LOUIS':
			return I18N::translate('Louisville, Kentucky');
		case 'LUBBO':
			return I18N::translate('Lubbock, Texas');
		case 'LVEGA':
			return I18N::translate('Las Vegas, Nevada');
		case 'MADRI':
			return I18N::translate('Madrid, Spain');
		case 'MANIL':
			return I18N::translate('Manila, Philippines');
		case 'MANTI':
			return I18N::translate('Manti, Utah');
		case 'MEDFO':
			return I18N::translate('Medford, Oregon');
		case 'MELBO':
			return I18N::translate('Melbourne, Australia');
		case 'MEMPH':
			return I18N::translate('Memphis, Tennessee');
		case 'MERID':
			return I18N::translate('Merida, Mexico');
		case 'MEXIC':
			return I18N::translate('Mexico City, Mexico');
		case 'MNTVD':
			return I18N::translate('Montevideo, Uruguay');
		case 'MONTE':
			return I18N::translate('Monterrey, Mexico');
		case 'MONTI':
			return I18N::translate('Monticello, Utah');
		case 'MONTR':
			return I18N::translate('Montreal, Quebec, Canada');
		case 'MTIMP':
			return I18N::translate('Mount Timpanogos, Utah');
		case 'NASHV':
			return I18N::translate('Nashville, Tennessee');
		case 'NAUV2':
			return I18N::translate('Nauvoo, Illinois (new)');
		case 'NAUVO':
			return I18N::translate('Nauvoo, Illinois (original)');
		case 'NBEAC':
			return I18N::translate('Newport Beach, California');
		case 'NUKUA':
			return I18N::translate('Nuku’Alofa, Tonga');
		case 'NYORK':
			return I18N::translate('New York, New York');
		case 'NZEAL':
			return I18N::translate('Hamilton, New Zealand');
		case 'OAKLA':
			return I18N::translate('Oakland, California');
		case 'OAXAC':
			return I18N::translate('Oaxaca, Mexico');
		case 'OGDEN':
			return I18N::translate('Ogden, Utah');
		case 'OKLAH':
			return I18N::translate('Oklahoma City, Oklahoma');
		case 'ORLAN':
			return I18N::translate('Orlando, Florida');
		case 'PALEG':
			return I18N::translate('Porto Alegre, Brazil');
		case 'PALMY':
			return I18N::translate('Palmyra, New York');
		case 'PAPEE':
			return I18N::translate('Papeete, Tahiti');
		case 'PERTH':
			return I18N::translate('Perth, Australia');
		case 'POFFI':
			return
				/* I18N: http://en.wikipedia.org/wiki/President_of_the_Church */
				I18N::translate('President’s Office');
		case 'PORTL':
			return I18N::translate('Portland, Oregon');
		case 'PREST':
			return I18N::translate('Preston, England');
		case 'PROVO':
			return I18N::translate('Provo, Utah');
		case 'RALEI':
			return I18N::translate('Raleigh, North Carolina');
		case 'RECIF':
			return I18N::translate('Recife, Brazil');
		case 'REDLA':
			return I18N::translate('Redlands, California');
		case 'REGIN':
			return I18N::translate('Regina, Saskatchewan, Canada');
		case 'RENO':
			return I18N::translate('Reno, Nevada');
		case 'SACRA':
			return I18N::translate('Sacramento, California');
		case 'SAMOA':
			return I18N::translate('Apia, Samoa');
		case 'SANTI':
			return I18N::translate('Santiago, Chile');
		case 'SANTO':
			return I18N::translate('San Antonio, Texas');
		case 'SDIEG':
			return I18N::translate('San Diego, California');
		case 'SDOMI':
			return I18N::translate('Santo Domingo, Dominican Republic');
		case 'SEATT':
			return I18N::translate('Seattle, Washington');
		case 'SEOUL':
			return I18N::translate('Seoul, Korea');
		case 'SGEOR':
			return I18N::translate('St. George, Utah');
		case 'SJOSE':
			return I18N::translate('San Jose, Costa Rica');
		case 'SLAKE':
			return I18N::translate('Salt Lake City, Utah');
		case 'SLOUI':
			return I18N::translate('St. Louis, Missouri');
		case 'SNOWF':
			return I18N::translate('Snowflake, Arizona');
		case 'SPAUL':
			return I18N::translate('Sao Paulo, Brazil');
		case 'SPMIN':
			return I18N::translate('St. Paul, Minnesota');
		case 'SPOKA':
			return I18N::translate('Spokane, Washington');
		case 'STOCK':
			return I18N::translate('Stockholm, Sweden');
		case 'SUVA':
			return I18N::translate('Suva, Fiji');
		case 'SWISS':
			return I18N::translate('Bern, Switzerland');
		case 'SYDNE':
			return I18N::translate('Sydney, Australia');
		case 'TAIPE':
			return I18N::translate('Taipei, Taiwan');
		case 'TAMPI':
			return I18N::translate('Tampico, Mexico');
		case 'TGUTI':
			return I18N::translate('Tuxtla Gutierrez, Mexico');
		case 'TOKYO':
			return I18N::translate('Tokyo, Japan');
		case 'TORNO':
			return I18N::translate('Toronto, Ontario, Canada');
		case 'VERAC':
			return I18N::translate('Veracruz, Mexico');
		case 'VERNA':
			return I18N::translate('Vernal, Utah');
		case 'VILLA':
			return I18N::translate('Villa Hermosa, Mexico');
		case 'WASHI':
			return I18N::translate('Washington, DC');
		case 'WINTE':
			return I18N::translate('Winter Quarters, Nebraska');
		default:
			return $temple_code;
		}
	}

	/**
	 * A sorted list of all temple names
	 *
	 * @return string[]
	 */
	public static function templeNames() {
		$temple_names = array();
		foreach (self::templeCodes() as $temple_code) {
			$temple_names[$temple_code] = self::templeName($temple_code);
		}
		uasort($temple_names, '\Fisharebest\Webtrees\I18N::strcasecmp');

		return $temple_names;
	}
}
