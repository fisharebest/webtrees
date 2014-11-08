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
 * Class WT_Gedcom_Code_Name - Functions and logic for GEDCOM "NAME" codes
 */
class WT_Gedcom_Code_Name {
	/** @var string[] A list of possible types of name */
	private static $TYPES = array('adopted', 'aka', 'birth', 'change', 'estate', 'immigrant', 'maiden', 'married', 'religious');

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
		case 'adopted':
			switch ($sex) {
			case 'M':
				/* I18N: The name given to a child by its adoptive parents */
				return WT_I18N::translate_c('MALE', 'adopted name');
			case 'F':
				/* I18N: The name given to a child by its adoptive parents */
				return WT_I18N::translate_c('FEMALE', 'adopted name');
			default:
				/* I18N: The name given to a child by its adoptive parents */
				return WT_I18N::translate('adopted name');
			}
		case 'aka':
			switch ($sex) {
			case 'M':
				/* I18N: The name by which an individual is also known.  e.g. a professional name or a stage name */
				return WT_I18N::translate_c('MALE', 'also known as');
			case 'F':
				/* I18N: The name by which an individual is also known.  e.g. a professional name or a stage name */
				return WT_I18N::translate_c('FEMALE', 'also known as');
			default:
				/* I18N: The name by which an individual is also known.  e.g. a professional name or a stage name */
				return WT_I18N::translate('also known as');
			}
		case 'birth':
			switch ($sex) {
			case 'M':
				/* I18N: The name given to an individual at their birth */
				return WT_I18N::translate_c('MALE', 'birth name');
			case 'F':
				/* I18N: The name given to an individual at their birth */
				return WT_I18N::translate_c('FEMALE', 'birth name');
			default:
				/* I18N: The name given to an individual at their birth */
				return WT_I18N::translate('birth name');
			}
		case 'change':
			switch ($sex) {
			case 'M':
				/* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
				return WT_I18N::translate_c('MALE', 'change of name');
			case 'F':
				/* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
				return WT_I18N::translate_c('FEMALE', 'change of name');
			default:
				/* I18N: A name chosen by an individual, to replace their existing name (whether legal or otherwise) */
				return WT_I18N::translate('change of name');
			}
		case 'estate':
			switch ($sex) {
			case 'M':
				/* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
				return WT_I18N::translate_c('MALE', 'estate name');
			case 'F':
				/* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
				return WT_I18N::translate_c('FEMALE', 'estate name');
			default:
				/* I18N: A name given to an individual, from the farm or estate on which they lived or worked */
				return WT_I18N::translate('estate name');
			}
		case 'immigrant':
			switch ($sex) {
			case 'M':
				/* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
				return WT_I18N::translate_c('MALE', 'immigration name');
			case 'F':
				/* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
				return WT_I18N::translate_c('FEMALE', 'immigration name');
			default:
				/* I18N: A name taken on immigration - e.g. migrants to the USA frequently anglicized their names */
				return WT_I18N::translate('immigration name');
			}
		case 'maiden':
			// Only women have “maiden” names!
			return
				/* I18N: A woman’s name, before she marries (in cultures where women take their new husband’s name on marriage) */
				WT_I18N::translate('maiden name');
		case 'married':
			switch ($sex) {
			case 'M':
				/* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
				return WT_I18N::translate_c('MALE', 'married name');
			case 'F':
				/* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
				return WT_I18N::translate_c('FEMALE', 'married name');
			default:
				/* I18N: A name taken on marriage - usually the wife takes the husband’s surname */
				return WT_I18N::translate('married name');
			}
		case 'religious':
			switch ($sex) {
			case 'M':
				/* I18N: A name taken when entering a religion or a religious order */
				return WT_I18N::translate_c('MALE', 'religious name');
			case 'F':
				/* I18N: A name taken when entering a religion or a religious order */
				return WT_I18N::translate_c('FEMALE', 'religious name');
			default:
				/* I18N: A name taken when entering a religion or a religious order */
				return WT_I18N::translate('religious name');
			}
		default:
			return $type;
		}
	}

	/**
	 * A list of all possible values for NAME types
	 *
	 * @param WT_GedcomRecord|null $record
	 *
	 * @return string[]
	 */
	public static function getValues(WT_GedcomRecord $record = null) {
		$values = array();
		foreach (self::$TYPES as $type) {
			$values[$type] = self::getValue($type, $record);
		}
		uasort($values, array('WT_I18N', 'strcasecmp'));

		return $values;
	}
}
