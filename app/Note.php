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
namespace Fisharebest\Webtrees;

/**
 * A GEDCOM note (NOTE) object.
 */
class Note extends GedcomRecord {
	const RECORD_TYPE = 'NOTE';
	const URL_PREFIX  = 'note.php?nid=';

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
	 * Each object type may have its own special rules, and re-implement this function.
	 *
	 * @param int $access_level
	 *
	 * @return bool
	 */
	protected function canShowByType($access_level) {
		// Hide notes if they are attached to private records
		$linked_ids = Database::prepare(
			"SELECT l_from FROM `##link` WHERE l_to=? AND l_file=?"
		)->execute(array(
			$this->xref, $this->tree->getTreeId(),
		))->fetchOneColumn();
		foreach ($linked_ids as $linked_id) {
			$linked_record = GedcomRecord::getInstance($linked_id, $this->tree);
			if ($linked_record && !$linked_record->canShow($access_level)) {
				return false;
			}
		}

		// Apply default behaviour
		return parent::canShowByType($access_level);
	}

	/**
	 * Generate a private version of this record
	 *
	 * @param int $access_level
	 *
	 * @return string
	 */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . '@ NOTE ' . I18N::translate('Private');
	}

	/**
	 * Fetch data from the database
	 *
	 * @param string $xref
	 * @param int    $tree_id
	 *
	 * @return null|string
	 */
	protected static function fetchGedcomRecord($xref, $tree_id) {
		return Database::prepare(
			"SELECT o_gedcom FROM `##other` WHERE o_id = :xref AND o_file = :tree_id AND o_type = 'NOTE'"
		)->execute(array(
			'xref'    => $xref,
			'tree_id' => $tree_id,
		))->fetchOne();
	}

	/**
	 * Create a name for this note - apply (and remove) markup, then take
	 * a maximum of 100 characters from the first line.
	 */
	public function extractNames() {
		$text = $this->getNote();

		if ($text) {
			switch ($this->getTree()->getPreference('FORMAT_TEXT')) {
			case 'markdown':
				$text = Filter::markdown($text);
				$text = Filter::unescapeHtml($text);
				break;
			}

			list($text) = explode("\n", $text);
			$this->addName('NOTE', strlen($text) > 100 ? mb_substr($text, 0, 100) . I18N::translate('…') : $text, $this->getGedcom());
		}
	}
}
