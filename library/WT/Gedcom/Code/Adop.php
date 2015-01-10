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
 * Class WT_Gedcom_Code_Adop - Functions and logic for GEDCOM "PEDI" codes
 */
class WT_Gedcom_Code_Adop {
	/** @var string[] A list of possible adoption codes */
	private static $TYPES = array('BOTH', 'HUSB', 'WIFE');

	/**
	 * Translate a code, for an (optional) record
	 *
	 * @param string               $type
	 * @param WT_GedcomRecord|null $record
	 *
	 * @return string
	 */
	public static function getValue($type, WT_GedcomRecord $record = null) {
		if ($record instanceof WT_Individual) {
			$sex = $record->getSex();
		} else {
			$sex = 'U';
		}

		switch ($type) {
		case 'BOTH':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('MALE', 'Adopted by both parents');
			case 'F':
				return WT_I18N::translate_c('FEMALE', 'Adopted by both parents');
			default:
				return WT_I18N::translate('Adopted by both parents');
			}
		case 'HUSB':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('MALE', 'Adopted by father');
			case 'F':
				return WT_I18N::translate_c('FEMALE', 'Adopted by father');
			default:
				return WT_I18N::translate('Adopted by father');
			}
		case 'WIFE':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('MALE', 'Adopted by mother');
			case 'F':
				return WT_I18N::translate_c('FEMALE', 'Adopted by mother');
			default:
				return WT_I18N::translate('Adopted by mother');
			}
		default:
			return $type;
		}
	}

	/**
	 * A list of all possible values for PEDI
	 *
	 * @param null WT_GedcomRecord|null $record
	 *
	 * @return string[]
	 */
	public static function getValues(WT_GedcomRecord $record = null) {
		$values = array();
		foreach (self::$TYPES as $type) {
			$values[$type] = self::getValue($type, $record);
		}

		// Don't sort these.  We want the order: both parents, father, mother
		return $values;
	}
}
