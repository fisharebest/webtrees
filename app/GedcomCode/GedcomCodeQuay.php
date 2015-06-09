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
 * Class GedcomCodeQuay - Functions and logic for GEDCOM "QUAY" codes
 */
class GedcomCodeQuay {
	/** @var string[] Valid values for a QUAY tag. */
	private static $TYPES = array('3', '2', '1', '0');

	/**
	 * Translate a code, for an optional record
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	public static function getValue($type) {
		switch ($type) {
		case '3':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 3” */
				I18N::translate('primary evidence');
		case '2':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 2” */
				I18N::translate('secondary evidence');
		case '1':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 1” */
				I18N::translate('questionable evidence');
		case '0':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 0” */
				I18N::translate('unreliable evidence');
		default:
			return $type;
		}
	}

	/**
	 * A list of all possible values for QUAY
	 *
	 * @return string[]
	 */
	public static function getValues() {
		$values = array();
		foreach (self::$TYPES as $type) {
			$values[$type] = self::getValue($type);
		}

		return $values;
	}
}
