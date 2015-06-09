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
 * Class GedcomCodeStat - Functions and logic for GEDCOM "STAT" codes
 */
class GedcomCodeStat {
	/**
	 * Get a list of status codes that can be used on a given LDS tag
	 *
	 * @param string $tag
	 *
	 * @return string[]
	 */
	public static function statusCodes($tag) {
		switch ($tag) {
		case 'BAPL':
		case 'CONL':
			// LDS_BAPTISM_DATE_STATUS
			return array('CHILD', 'COMPLETED', 'EXCLUDED', 'INFANT', 'PRE-1970', 'STILLBORN', 'SUBMITTED', 'UNCLEARED');
		case 'ENDL':
			// LDS_ENDOWMENT_DATE_STATUS
			return array('CHILD', 'COMPLETED', 'EXCLUDED', 'INFANT', 'PRE-1970', 'STILLBORN', 'SUBMITTED', 'UNCLEARED');
		case 'SLGC':
			// LDS_CHILD_SEALING_DATE_STATUS
			return array('BIC', 'COMPLETED', 'EXCLUDED', 'PRE-1970', 'STILLBORN', 'SUBMITTED', 'UNCLEARED');
		case 'SLGS':
			// LDS_SPOUSE_SEALING_DATE_STATUS
			return array('CANCELED', 'COMPLETED', 'DNS', 'DNS/CAN', 'EXCLUDED', 'PRE-1970', 'SUBMITTED', 'UNCLEARED');
		default:
			throw new \InvalidArgumentException('Internal error - bad argument to GedcomCodeStat::statusCodes("' . $tag . '")');
		}
	}

	/**
	 * Get the localized name for a status code
	 *
	 * @param string $status_code
	 *
	 * @return string
	 */
	public static function statusName($status_code) {
		switch ($status_code) {
		case 'BIC':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Born in the covenant');
		case 'CANCELED':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Sealing cancelled (divorce)');
		case 'CHILD':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Died as a child: exempt');
		case 'CLEARED':
			// This status appears in PhpGedView, but not in the GEDCOM 5.5.1 specification.
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Cleared but not yet completed');
		case 'COMPLETED':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Completed; date unknown');
		case 'DNS':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Do not seal: unauthorized');
		case 'DNS/CAN':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Do not seal, previous sealing cancelled');
		case 'EXCLUDED':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Excluded from this submission');
		case 'INFANT':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Died as an infant: exempt');
		case 'PRE-1970':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Completed before 1970; date not available');
		case 'STILLBORN':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Stillborn: exempt');
		case 'SUBMITTED':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Submitted but not yet cleared');
		case 'UNCLEARED':
			return
				/* I18N: LDS sealing status; see http://en.wikipedia.org/wiki/Sealing_(Latter_Day_Saints) */
				I18N::translate('Uncleared: insufficient data');
		default:
			return $status_code;
		}
	}

	/**
	 * A sorted list of all status names, for a given GEDCOM tag
	 *
	 * @param string $tag
	 *
	 * @return string[]
	 */
	public static function statusNames($tag) {
		$status_names = array();
		foreach (self::statusCodes($tag) as $status_code) {
			$status_names[$status_code] = self::statusName($status_code);
		}
		uasort($status_names, '\Fisharebest\Webtrees\I18N::strcasecmp');

		return $status_names;
	}
}
