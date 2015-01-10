<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
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

/**
 * Class WT_Gedcom_Code_Temp - Functions and logic for GEDCOM "TEMP" codes
 */
class WT_Gedcom_Code_Temp {
	/**
	 * A list of GEDCOM tags that require a TEMP subtag
	 *
	 * @param string $tag
	 *
	 * @return boolean
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
			return WT_I18N::translate('Aba, Nigeria');
		case 'ACCRA':
			return WT_I18N::translate('Accra, Ghana');
		case 'ADELA':
			return WT_I18N::translate('Adelaide, Australia');
		case 'ALBER':
			return WT_I18N::translate('Cardston, Alberta, Canada');
		case 'ALBUQ':
			return WT_I18N::translate('Albuquerque, New Mexico');
		case 'ANCHO':
			return WT_I18N::translate('Anchorage, Alaska');
		case 'ARIZO':
			return WT_I18N::translate('Mesa, Arizona');
		case 'ASUNC':
			return WT_I18N::translate('Asuncion, Paraguay');
		case 'ATLAN':
			return WT_I18N::translate('Atlanta, Georgia');
		case 'BAIRE':
			return WT_I18N::translate('Buenos Aires, Argentina');
		case 'BILLI':
			return WT_I18N::translate('Billings, Montana');
		case 'BIRMI':
			return WT_I18N::translate('Birmingham, Alabama');
		case 'BISMA':
			return WT_I18N::translate('Bismarck, North Dakota');
		case 'BOGOT':
			return WT_I18N::translate('Bogota, Colombia');
		case 'BOISE':
			return WT_I18N::translate('Boise, Idaho');
		case 'BOSTO':
			return WT_I18N::translate('Boston, Massachusetts');
		case 'BOUNT':
			return WT_I18N::translate('Bountiful, Utah');
		case 'BRISB':
			return WT_I18N::translate('Brisbane, Australia');
		case 'BROUG':
			return WT_I18N::translate('Baton Rouge, Louisiana');
		case 'CAMPI':
			return WT_I18N::translate('Campinas, Brazil');
		case 'CARAC':
			return WT_I18N::translate('Caracas, Venezuela');
		case 'CHICA':
			return WT_I18N::translate('Chicago, Illinois');
		case 'CIUJU':
			return WT_I18N::translate('Ciudad Juarez, Mexico');
		case 'COCHA':
			return WT_I18N::translate('Cochabamba, Bolivia');
		case 'COLJU':
			return WT_I18N::translate('Colonia Juarez, Mexico');
		case 'COLSC':
			return WT_I18N::translate('Columbia, South Carolina');
		case 'COLUM':
			return WT_I18N::translate('Columbus, Ohio');
		case 'COPEN':
			return WT_I18N::translate('Copenhagen, Denmark');
		case 'CRIVE':
			return WT_I18N::translate('Columbia River, Washington');
		case 'DALLA':
			return WT_I18N::translate('Dallas, Texas');
		case 'DENVE':
			return WT_I18N::translate('Denver, Colorado');
		case 'DETRO':
			return WT_I18N::translate('Detroit, Michigan');
		case 'EDMON':
			return WT_I18N::translate('Edmonton, Alberta, Canada');
		case 'EHOUS':
			return
				/* I18N: http://en.wikipedia.org/wiki/Endowment_house */
				WT_I18N::translate('Endowment House');
		case 'FRANK':
			return WT_I18N::translate('Frankfurt am Main, Germany');
		case 'FREIB':
			return WT_I18N::translate('Freiburg, Germany');
		case 'FRESN':
			return WT_I18N::translate('Fresno, California');
		case 'FUKUO':
			return WT_I18N::translate('Fukuoka, Japan');
		case 'GUADA':
			return WT_I18N::translate('Guadalajara, Mexico');
		case 'GUATE':
			return WT_I18N::translate('Guatemala City, Guatemala');
		case 'GUAYA':
			return WT_I18N::translate('Guayaquil, Ecuador');
		case 'HAGUE':
			return WT_I18N::translate('The Hague, Netherlands');
		case 'HALIF':
			return WT_I18N::translate('Halifax, Nova Scotia, Canada');
		case 'HARTF':
			return WT_I18N::translate('Hartford, Connecticut');
		case 'HAWAI':
			return WT_I18N::translate('Laie, Hawaii');
		case 'HELSI':
			return WT_I18N::translate('Helsinki, Finland');
		case 'HERMO':
			return WT_I18N::translate('Hermosillo, Mexico');
		case 'HKONG':
			return WT_I18N::translate('Hong Kong');
		case 'HOUST':
			return WT_I18N::translate('Houston, Texas');
		case 'IFALL':
			return WT_I18N::translate('Idaho Falls, Idaho');
		case 'JOHAN':
			return WT_I18N::translate('Johannesburg, South Africa');
		case 'JRIVE':
			return WT_I18N::translate('Jordan River, Utah');
		case 'KIEV':
			return WT_I18N::translate('Kiev, Ukraine');
		case 'KONA':
			return WT_I18N::translate('Kona, Hawaii');
		case 'LANGE':
			return WT_I18N::translate('Los Angeles, California');
		case 'LIMA':
			return WT_I18N::translate('Lima, Peru');
		case 'LOGAN':
			return WT_I18N::translate('Logan, Utah');
		case 'LONDO':
			return WT_I18N::translate('London, England');
		case 'LOUIS':
			return WT_I18N::translate('Louisville, Kentucky');
		case 'LUBBO':
			return WT_I18N::translate('Lubbock, Texas');
		case 'LVEGA':
			return WT_I18N::translate('Las Vegas, Nevada');
		case 'MADRI':
			return WT_I18N::translate('Madrid, Spain');
		case 'MANIL':
			return WT_I18N::translate('Manila, Philippines');
		case 'MANTI':
			return WT_I18N::translate('Manti, Utah');
		case 'MEDFO':
			return WT_I18N::translate('Medford, Oregon');
		case 'MELBO':
			return WT_I18N::translate('Melbourne, Australia');
		case 'MEMPH':
			return WT_I18N::translate('Memphis, Tennessee');
		case 'MERID':
			return WT_I18N::translate('Merida, Mexico');
		case 'MEXIC':
			return WT_I18N::translate('Mexico City, Mexico');
		case 'MNTVD':
			return WT_I18N::translate('Montevideo, Uruguay');
		case 'MONTE':
			return WT_I18N::translate('Monterrey, Mexico');
		case 'MONTI':
			return WT_I18N::translate('Monticello, Utah');
		case 'MONTR':
			return WT_I18N::translate('Montreal, Quebec, Canada');
		case 'MTIMP':
			return WT_I18N::translate('Mount Timpanogos, Utah');
		case 'NASHV':
			return WT_I18N::translate('Nashville, Tennessee');
		case 'NAUV2':
			return WT_I18N::translate('Nauvoo, Illinois (new)');
		case 'NAUVO':
			return WT_I18N::translate('Nauvoo, Illinois (original)');
		case 'NBEAC':
			return WT_I18N::translate('Newport Beach, California');
		case 'NUKUA':
			return WT_I18N::translate('Nuku’Alofa, Tonga');
		case 'NYORK':
			return WT_I18N::translate('New York, New York');
		case 'NZEAL':
			return WT_I18N::translate('Hamilton, New Zealand');
		case 'OAKLA':
			return WT_I18N::translate('Oakland, California');
		case 'OAXAC':
			return WT_I18N::translate('Oaxaca, Mexico');
		case 'OGDEN':
			return WT_I18N::translate('Ogden, Utah');
		case 'OKLAH':
			return WT_I18N::translate('Oklahoma City, Oklahoma');
		case 'ORLAN':
			return WT_I18N::translate('Orlando, Florida');
		case 'PALEG':
			return WT_I18N::translate('Porto Alegre, Brazil');
		case 'PALMY':
			return WT_I18N::translate('Palmyra, New York');
		case 'PAPEE':
			return WT_I18N::translate('Papeete, Tahiti');
		case 'PERTH':
			return WT_I18N::translate('Perth, Australia');
		case 'POFFI':
			return
				/* I18N: http://en.wikipedia.org/wiki/President_of_the_Church */
				WT_I18N::translate('President’s Office');
		case 'PORTL':
			return WT_I18N::translate('Portland, Oregon');
		case 'PREST':
			return WT_I18N::translate('Preston, England');
		case 'PROVO':
			return WT_I18N::translate('Provo, Utah');
		case 'RALEI':
			return WT_I18N::translate('Raleigh, North Carolina');
		case 'RECIF':
			return WT_I18N::translate('Recife, Brazil');
		case 'REDLA':
			return WT_I18N::translate('Redlands, California');
		case 'REGIN':
			return WT_I18N::translate('Regina, Saskatchewan, Canada');
		case 'RENO':
			return WT_I18N::translate('Reno, Nevada');
		case 'SACRA':
			return WT_I18N::translate('Sacramento, California');
		case 'SAMOA':
			return WT_I18N::translate('Apia, Samoa');
		case 'SANTI':
			return WT_I18N::translate('Santiago, Chile');
		case 'SANTO':
			return WT_I18N::translate('San Antonio, Texas');
		case 'SDIEG':
			return WT_I18N::translate('San Diego, California');
		case 'SDOMI':
			return WT_I18N::translate('Santo Domingo, Dominican Republic');
		case 'SEATT':
			return WT_I18N::translate('Seattle, Washington');
		case 'SEOUL':
			return WT_I18N::translate('Seoul, Korea');
		case 'SGEOR':
			return WT_I18N::translate('St. George, Utah');
		case 'SJOSE':
			return WT_I18N::translate('San Jose, Costa Rica');
		case 'SLAKE':
			return WT_I18N::translate('Salt Lake City, Utah');
		case 'SLOUI':
			return WT_I18N::translate('St. Louis, Missouri');
		case 'SNOWF':
			return WT_I18N::translate('Snowflake, Arizona');
		case 'SPAUL':
			return WT_I18N::translate('Sao Paulo, Brazil');
		case 'SPMIN':
			return WT_I18N::translate('St. Paul, Minnesota');
		case 'SPOKA':
			return WT_I18N::translate('Spokane, Washington');
		case 'STOCK':
			return WT_I18N::translate('Stockholm, Sweden');
		case 'SUVA':
			return WT_I18N::translate('Suva, Fiji');
		case 'SWISS':
			return WT_I18N::translate('Bern, Switzerland');
		case 'SYDNE':
			return WT_I18N::translate('Sydney, Australia');
		case 'TAIPE':
			return WT_I18N::translate('Taipei, Taiwan');
		case 'TAMPI':
			return WT_I18N::translate('Tampico, Mexico');
		case 'TGUTI':
			return WT_I18N::translate('Tuxtla Gutierrez, Mexico');
		case 'TOKYO':
			return WT_I18N::translate('Tokyo, Japan');
		case 'TORNO':
			return WT_I18N::translate('Toronto, Ontario, Canada');
		case 'VERAC':
			return WT_I18N::translate('Veracruz, Mexico');
		case 'VERNA':
			return WT_I18N::translate('Vernal, Utah');
		case 'VILLA':
			return WT_I18N::translate('Villa Hermosa, Mexico');
		case 'WASHI':
			return WT_I18N::translate('Washington, DC');
		case 'WINTE':
			return WT_I18N::translate('Winter Quarters, Nebraska');
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
		uasort($temple_names, array('WT_I18N', 'strcasecmp'));

		return $temple_names;
	}
}
