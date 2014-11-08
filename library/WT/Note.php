<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2009 PGV Development Team.  All rights reserved.
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
 * Class WT_Note - Class file for a Shared Note (NOTE) object
 */
class WT_Note extends WT_GedcomRecord {
	const RECORD_TYPE = 'NOTE';
	const URL_PREFIX = 'note.php?nid=';

	/**
	 * Get an instance of a note object.  For single records,
	 * we just receive the XREF.  For bulk records (such as lists
	 * and search results) we can receive the GEDCOM data as well.
	 *
	 * @param string       $xref
	 * @param integer|null $gedcom_id
	 * @param string|null  $gedcom
	 *
	 * @return WT_Note|null
	 */
	public static function getInstance($xref, $gedcom_id = WT_GED_ID, $gedcom = null) {
		$record = parent::getInstance($xref, $gedcom_id, $gedcom);

		if ($record instanceof WT_Note) {
			return $record;
		} else {
			return null;
		}
	}

	/**
	 * Get the text contents of the note
	 *
	 * @return string|null
	 */
	public function getNote() {
		if (preg_match('/^0 @' . WT_REGEX_XREF . '@ NOTE ?(.*(?:\n1 CONT ?.*)*)/', $this->gedcom . $this->pending, $match)) {
			return preg_replace("/\n1 CONT ?/", "\n", $match[1]);
		} else {
			return null;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function canShowByType($access_level) {
		// Hide notes if they are attached to private records
		$linked_ids = WT_DB::prepare(
			"SELECT l_from FROM `##link` WHERE l_to=? AND l_file=?"
		)->execute(array($this->xref, $this->gedcom_id))->fetchOneColumn();
		foreach ($linked_ids as $linked_id) {
			$linked_record = WT_GedcomRecord::getInstance($linked_id);
			if ($linked_record && !$linked_record->canShow($access_level)) {
				return false;
			}
		}

		// Apply default behaviour
		return parent::canShowByType($access_level);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . '@ NOTE ' . WT_I18N::translate('Private');
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement = null;

		if ($statement === null) {
			$statement = WT_DB::prepare("SELECT o_gedcom FROM `##other` WHERE o_id=? AND o_file=? AND o_type='NOTE'");
		}

		return $statement->execute(array($xref, $gedcom_id))->fetchOne();
	}

	/**
	 * Create a name for this note - apply (and remove) markup, then take
	 * a maximum of 100 characters from the first line.
	 *
	 * {@inheritdoc}
	 */
	public function extractNames() {
		global $WT_TREE;

		$text = $this->getNote();

		if ($text) {
			switch ($WT_TREE->getPreference('FORMAT_TEXT')) {
			case 'markdown':
				$text = WT_Filter::markdown($text);
				$text = WT_Filter::unescapeHtml($text);
				break;
			}

			list($text) = explode("\n", $text);
			$this->addName('NOTE', strlen($text) > 100 ? mb_substr($text, 0, 100) . WT_I18N::translate('…') : $text, $this->getGedcom());
		}
	}
}
