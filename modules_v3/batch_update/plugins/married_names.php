<?php
// Batch Update plugin for phpGedView - add missing 2 _MARNM records
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class married_names_bu_plugin extends base_plugin {
	var $surname=null; // User option: add or replace husband’s surname

	static function getName() {
		return WT_I18N::translate('Add missing married names');
	}

	static function getDescription() {
		return WT_I18N::translate('You can make it easier to search for married women by recording their married name.<br>However not all women take their husband’s surname, so beware of introducing incorrect information into your database.');
	}

	function doesRecordNeedUpdate($xref, $gedrec) {
		return preg_match('/^1 SEX F/m', $gedrec) && preg_match('/^1 NAME /m', $gedrec) && self::_surnames_to_add($xref, $gedrec);
	}

	function updateRecord($xref, $gedrec) {
		$SURNAME_TRADITION=get_gedcom_setting(WT_GED_ID, 'SURNAME_TRADITION');

		preg_match('/^1 NAME (.*)/m', $gedrec, $match);
		$wife_name=$match[1];
		$married_names=array();
		foreach (self::_surnames_to_add($xref, $gedrec) as $surname) {
			switch ($this->surname) {
			case 'add':
				$married_names[]="\n2 _MARNM ".str_replace('/', '', $wife_name).' /'.$surname.'/';
				break;
			case 'replace':
				if ($SURNAME_TRADITION=='polish') {
					$surname=preg_replace(array('/ski$/','/cki$/','/dzki$/'), array('ska', 'cka', 'dzka'), $surname);
				}
				$married_names[]="\n2 _MARNM ".preg_replace('!/.*/!', '/'.$surname.'/', $wife_name);
				break;
			}
		}
		return preg_replace('/(^1 NAME .*([\r\n]+[2-9].*)*)/m', '\\1'.implode('', $married_names), $gedrec, 1);
	}

	static function _surnames_to_add($xref, $gedrec) {
		$wife_surnames=self::_surnames($xref, $gedrec);
		$husb_surnames=array();
		$missing_surnames=array();
		preg_match_all('/^1 FAMS @(.+)@/m', $gedrec, $fmatch);
		foreach ($fmatch[1] as $famid) {
			$famrec=batch_update::getLatestRecord($famid, 'FAM');
			if (preg_match('/^1 MARR/m', $famrec) && preg_match('/^1 HUSB @(.+)@/m', $famrec, $hmatch)) {
				$husbrec=batch_update::getLatestRecord($hmatch[1], 'INDI');
				$husb_surnames=array_unique(array_merge($husb_surnames, self::_surnames($hmatch[1], $husbrec)));
			}
		}
		foreach ($husb_surnames as $husb_surname) {
			if (!in_array($husb_surname, $wife_surnames)) {
				$missing_surnames[]=$husb_surname;
			}
		}
		return $missing_surnames;
	}

	static function _surnames($xref, $gedrec) {
		if (preg_match_all('/^(?:1 NAME|2 _MARNM) .*\/(.+)\//m', $gedrec, $match)) {
			return $match[1];
		} else {
			return array();
		}
	}

	// Add an option for different surname styles
	function getOptions() {
		parent::getOptions();
		$this->surname = WT_Filter::get('surname', 'add|replace', 'replace');
	}

	function getOptionsForm() {
		return
			parent::getOptionsForm().
			'<tr valign="top"><th>'.WT_I18N::translate('Surname option').'</th>'.
			'<td class="optionbox"><select name="surname" onchange="reset_reload();"><option value="replace"'.
			($this->surname=='replace' ? ' selected="selected"' : '').
			'">'.WT_I18N::translate('Wife’s surname replaced by husband’s surname').'</option><option value="add"'.
			($this->surname=='add' ? ' selected="selected"' : '').
			'">'.WT_I18N::translate('Wife’s maiden surname becomes new given name').'</option></select></td></tr>';
	}
}