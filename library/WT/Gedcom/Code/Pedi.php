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
 * Class WT_Gedcom_Code_Pedi - Functions and logic for GEDCOM "PEDI" codes
 */
class WT_Gedcom_Code_Pedi {
	/** @var string[] Possible values for pedigree field */
	private static $TYPES = array('adopted', 'birth', 'foster', 'rada', 'sealing');

	/**
	 * Translate a code, for an optional record
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
		case 'birth':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('Male pedigree', 'Birth');
			case 'F':
				return WT_I18N::translate_c('Female pedigree', 'Birth');
			default:
				return WT_I18N::translate_c('Pedigree', 'Birth');
			}
		case 'adopted':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('Male pedigree', 'Adopted');
			case 'F':
				return WT_I18N::translate_c('Female pedigree', 'Adopted');
			default:
				return WT_I18N::translate_c('Pedigree', 'Adopted');
			}
		case 'foster':
			switch ($sex) {
			case 'M':
				return WT_I18N::translate_c('Male pedigree', 'Foster');
			case 'F':
				return WT_I18N::translate_c('Female pedigree', 'Foster');
			default:
				return WT_I18N::translate_c('Pedigree', 'Foster');
			}
		case 'sealing':
			switch ($sex) {
			case 'M':
				return
					/* I18N: “sealing” is a ceremony in the Mormon church. */
					WT_I18N::translate_c('Male pedigree', 'Sealing');
			case 'F':
				return
					/* I18N: “sealing” is a ceremony in the Mormon church. */
					WT_I18N::translate_c('Female pedigree', 'Sealing');
			default:
				return
					/* I18N: “sealing” is a ceremony in the Mormon church. */
					WT_I18N::translate_c('Pedigree', 'Sealing');
			}
		case 'rada':
			// Not standard GEDCOM - a webtrees extension
			// This is an arabic word which does not exist in other languages.
			// So, it will not have any inflected forms.
			return
				/* I18N: This is an Arabic word, pronounced “ra DAH”.  It is child-to-parent pedigree, established by wet-nursing. */
				WT_I18N::translate('Rada');
		default:
			return $type;
		}
	}

	/**
	 * A list of all possible values for PEDI
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

	/**
	 * A label for a parental family group
	 *
	 * @param string $pedi
	 *
	 * @return string
	 */
	public static function getChildFamilyLabel($pedi) {
		switch ($pedi) {
		case '':
		case 'birth':
			return WT_I18N::translate('Family with parents');
		case 'adopted':
			return WT_I18N::translate('Family with adoptive parents');
		case 'foster':
			return WT_I18N::translate('Family with foster parents');
		case 'sealing':
			return
				/* I18N: “sealing” is a Mormon ceremony. */
				WT_I18N::translate('Family with sealing parents');
		case 'rada':
			return
				/* I18N: “rada” is an Arabic word, pronounced “ra DAH”.  It is child-to-parent pedigree, established by wet-nursing. */
				WT_I18N::translate('Family with rada parents');
		default:
			return WT_I18N::translate('Family with parents') . ' - ' . $pedi;
		}
	}

	/**
	 * Create GEDCOM for a new child-family pedigree
	 *
	 * @param $pedi
	 * @param $xref
	 *
	 * @return string
	 */
	public static function createNewFamcPedi($pedi, $xref) {
		switch ($pedi) {
		case '':
			return "1 FAMC @$xref@";
		case 'adopted':
			return "1 FAMC @$xref@\n2 PEDI $pedi\n1 ADOP\n2 FAMC @$xref@\n3 ADOP BOTH";
		case 'sealing':
			return "1 FAMC @$xref@\n2 PEDI $pedi\n1 SLGC\n2 FAMC @$xref@";
		case 'foster':
			return "1 FAMC @$xref@\n2 PEDI $pedi\n1 EVEN\n2 TYPE $pedi";
		default:
			return "1 FAMC @$xref@\n2 PEDI $pedi";
		}
	}
}
