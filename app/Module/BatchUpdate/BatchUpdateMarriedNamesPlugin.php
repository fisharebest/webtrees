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
namespace Fisharebest\Webtrees\Module\BatchUpdate;

use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\BatchUpdateModule;

/**
 * Class BatchUpdateMarriedNamesPlugin Batch Update plugin: add missing 2 _MARNM records
 */
class BatchUpdateMarriedNamesPlugin extends BatchUpdateBasePlugin {
	/** @var string User option: add or replace husband’s surname */
	private $surname;

	/**
	 * User-friendly name for this plugin.
	 *
	 * @return string
	 */
	public function getName() {
		return I18N::translate('Add missing married names');
	}

	/**
	 * Description / help-text for this plugin.
	 *
	 * @return string
	 */
	public function getDescription() {
		return I18N::translate('You can make it easier to search for married women by recording their married name.<br>However not all women take their husband’s surname, so beware of introducing incorrect information into your database.');
	}

	/**
	 * Does this record need updating?
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return bool
	 */
	public function doesRecordNeedUpdate($xref, $gedrec) {
		return preg_match('/^1 SEX F/m', $gedrec) && preg_match('/^1 NAME /m', $gedrec) && self::surnamesToAdd($xref, $gedrec);
	}

	/**
	 * Apply any updates to this record
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return string
	 */
	public function updateRecord($xref, $gedrec) {
		global $WT_TREE;

		$SURNAME_TRADITION = $WT_TREE->getPreference('SURNAME_TRADITION');

		preg_match('/^1 NAME (.*)/m', $gedrec, $match);
		$wife_name     = $match[1];
		$married_names = array();
		foreach (self::surnamesToAdd($xref, $gedrec) as $surname) {
			switch ($this->surname) {
			case 'add':
				$married_names[] = "\n2 _MARNM " . str_replace('/', '', $wife_name) . ' /' . $surname . '/';
				break;
			case 'replace':
				if ($SURNAME_TRADITION === 'polish') {
					$surname = preg_replace(array('/ski$/', '/cki$/', '/dzki$/'), array('ska', 'cka', 'dzka'), $surname);
				}
				$married_names[] = "\n2 _MARNM " . preg_replace('!/.*/!', '/' . $surname . '/', $wife_name);
				break;
			}
		}

		return preg_replace('/(^1 NAME .*([\r\n]+[2-9].*)*)/m', '\\1' . implode('', $married_names), $gedrec, 1);
	}

	/**
	 * Generate a list of married surnames that are not already present.
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return string[]
	 */
	private function surnamesToAdd($xref, $gedrec) {
		$wife_surnames    = self::surnames($xref, $gedrec);
		$husb_surnames    = array();
		$missing_surnames = array();
		preg_match_all('/^1 FAMS @(.+)@/m', $gedrec, $fmatch);
		foreach ($fmatch[1] as $famid) {
			$famrec = BatchUpdateModule::getLatestRecord($famid, 'FAM');
			if (preg_match('/^1 MARR/m', $famrec) && preg_match('/^1 HUSB @(.+)@/m', $famrec, $hmatch)) {
				$husbrec       = BatchUpdateModule::getLatestRecord($hmatch[1], 'INDI');
				$husb_surnames = array_unique(array_merge($husb_surnames, self::surnames($hmatch[1], $husbrec)));
			}
		}
		foreach ($husb_surnames as $husb_surname) {
			if (!in_array($husb_surname, $wife_surnames)) {
				$missing_surnames[] = $husb_surname;
			}
		}

		return $missing_surnames;
	}

	/**
	 * Extract a list of surnames from a GEDCOM record.
	 *
	 * @param string $xref
	 * @param string $gedrec
	 *
	 * @return string[]
	 */
	private function surnames($xref, $gedrec) {
		if (preg_match_all('/^(?:1 NAME|2 _MARNM) .*\/(.+)\//m', $gedrec, $match)) {
			return $match[1];
		} else {
			return array();
		}
	}

	/**
	 * Process the user-supplied options.
	 */
	public function getOptions() {
		parent::getOptions();
		$this->surname = Filter::get('surname', 'add|replace', 'replace');
	}

	/**
	 * Generate a form to ask the user for options.
	 *
	 * @return string
	 */
	public function getOptionsForm() {
		return
			'<div class="form-group">' .
			'<label class="control-label col-sm-3">' . I18N::translate('Surname option') . '</label>' .
			'<div class="col-sm-9">' .
			'<select class="form-control" name="surname" onchange="reset_reload();">' .
			'<option value="replace" ' .
			($this->surname == 'replace' ? 'selected' : '') .
			'">' . I18N::translate('Wife’s surname replaced by husband’s surname') . '</option><option value="add" ' .
			($this->surname == 'add' ? 'selected' : '') .
			'">' . I18N::translate('Wife’s maiden surname becomes new given name') . '</option>' .
			'</select>' .
			'</div></div>' .
			parent::getOptionsForm();
	}
}
