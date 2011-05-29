<?php
// Functions and logic for GEDCOM "TYPE" codes
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

class WT_Gedcom_Code_Type {

	// Get a list of type codes that can be used on a given main tag
	public static function typeCodes($tag) {
		switch ($tag) {
		case 'NAME':
			return array('adopted', 'aka', 'birth', 'change', 'immigrant', 'maiden', 'married', 'religious');
		default:
			return '';
		}
	}

	// Get the localized name for a type code
	public static function typeName($type_name) {
		switch ($type_name) {
		case 'adopted':
			return WT_I18N::translate('adopted');
		case 'aka':
			return WT_I18N::translate('aka');
		case 'birth':
			return WT_I18N::translate('birth');
		case 'change':
			return WT_I18N::translate('change');
		case 'immigrant':
			return WT_I18N::translate('immigrant');
		case 'maiden':
			return WT_I18N::translate('maiden');
		case 'married':
			return WT_I18N::translate('married');
		case 'religious':
			return WT_I18N::translate('religious');
		default:
			return $type_name;
		}
	}

	// A sorted list of all type names, for a given GEDCOM tag
	public static function typeNames($tag) {
		$type_names=array();
		foreach (self::typeCodes($tag) as $type_name) {
			$type_names[$type_name]=self::typeName($type_name);
		}
		uasort($type_names, 'utf8_strcasecmp');
		return $type_names;
	}
}
