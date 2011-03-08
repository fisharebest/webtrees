<?php
// Functions and logic for GEDCOM "PEDI" codes
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Gedcom_Code_Pedi {
	
	private static $TYPES=array('adopted', 'birth', 'foster', 'sealing');

	// Translate a code, for an (optional) record
	public static function getValue($type, $record=null) {
		if ($record instanceof WT_Person) {
			$sex=$record->getSex();
		} else {
			$sex='U';
		}

		switch ($type) {
		case 'birth':
			switch ($sex) {
			case 'M': return WT_I18N::translate_c('Male pedigree',   'Birth');
			case 'F': return WT_I18N::translate_c('Female pedigree', 'Birth');
			default:  return WT_I18N::translate_c('Pedigree',        'Birth');
			}
		case 'adopted':
			switch ($sex) {
			case 'M': return WT_I18N::translate_c('Male pedigree',   'Adopted');
			case 'F': return WT_I18N::translate_c('Female pedigree', 'Adopted');
			default:  return WT_I18N::translate_c('Pedigree',        'Adopted');
			}
		case 'foster':
			switch ($sex) {
			case 'M': return WT_I18N::translate_c('Male pedigree',   'Foster');
			case 'F': return WT_I18N::translate_c('Female pedigree', 'Foster');
			default:  return WT_I18N::translate_c('Pedigree',        'Foster');
			}
		case 'sealing':
			switch ($sex) {
			case 'M': return WT_I18N::translate_c('Male pedigree',   'Sealing');
			case 'F': return WT_I18N::translate_c('Female pedigree', 'Sealing');
			default:  return WT_I18N::translate_c('Pedigree',        'Sealing');
			}
		default:
			return $type;
		}
	}

	// A list of all possible values for PEDI
	public static function getValues($record=null) {
		$values=array();
		foreach (self::$TYPES as $type) {
			$values[$type]=self::getValue($type, $record);
		}
		uasort($values, 'utf8_strcasecmp');
		return $values;
	}
}
