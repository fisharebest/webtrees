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

namespace Fisharebest\Webtrees\Census;

use Fisharebest\Webtrees\Individual;

/**
 * The individual's birth place.
 */
class CensusColumnBirthPlaceUnitedStates extends CensusColumnBirthPlace implements CensusColumnInterface {
	private static $states = array(
		'Alabama'              => 'AL',
		'Alaska'               => 'AK',
		'Arizona'              => 'AZ',
		'Arkansas'             => 'AR',
		'California'           => 'CA',
		'Colorado'             => 'CO',
		'Connecticut'          => 'CT',
		'Delaware'             => 'DE',
		'District of Columbia' => 'DC',
		'Florida'              => 'FL',
		'Georgia'              => 'GA',
		'Hawaii'               => 'HI',
		'Idaho'                => 'ID',
		'Illinois'             => 'IL',
		'Indiana'              => 'IN',
		'Iowa'                 => 'IA',
		'Kansas'               => 'KS',
		'Kentucky'             => 'KY',
		'Louisiana'            => 'LA',
		'Maine'                => 'ME',
		'Maryland'             => 'MD',
		'Massachusetts'        => 'MA',
		'Michigan'             => 'MI',
		'Minnesota'            => 'MN',
		'Mississippi'          => 'MS',
		'Missouri'             => 'MO',
		'Montana'              => 'MT',
		'Nebraska'             => 'NE',
		'Nevada'               => 'NV',
		'New Hampshire'        => 'NH',
		'New Jersey'           => 'NJ',
		'New Mexico'           => 'NM',
		'New York'             => 'NY',
		'North Carolina'       => 'NC',
		'North Dakota'         => 'ND',
		'Ohio'                 => 'OH',
		'Oklahoma'             => 'OK',
		'Oregon'               => 'OR',
		'Pennsylvania'         => 'PA',
		'Rhode Island'         => 'RI',
		'South Carolina'       => 'SC',
		'South Dakota'         => 'SD',
		'Tennessee'            => 'TN',
		'Texas'                => 'TX',
		'Utah'                 => 'UT',
		'Vermont'              => 'VT',
		'Virginia'             => 'VA',
		'Washington'           => 'WA',
		'West Virginia'        => 'WV',
		'Wisconsin'            => 'WI',
		'Wyoming'              => 'WY',
	);

	/**
	 * Generate the likely value of this census column, based on available information.
	 *
	 * @param Individual      $individual
	 * @param Individual|null $head
	 *
	 * @return string
	 */
	public function generate(Individual $individual, Individual $head = null) {
		$birth_place = parent::generate($individual, $head);
		$tmp         = explode(', ', $birth_place);
		$birth_place = end($tmp);

		if (array_key_exists($birth_place, self::$states)) {
			return self::$states[$birth_place];
		} else {
			return $birth_place;
		}
	}
}
