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
 * Class WT_Gedcom_Code_Quay - Functions and logic for GEDCOM "QUAY" codes
 */
class WT_Gedcom_Code_Quay {
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
				WT_I18N::translate('primary evidence');
		case '2':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 2” */
				WT_I18N::translate('secondary evidence');
		case '1':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 1” */
				WT_I18N::translate('questionable evidence');
		case '0':
			return
				/* I18N: Quality of source information - GEDCOM tag “QUAY 0” */
				WT_I18N::translate('unreliable evidence');
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
