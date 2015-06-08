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
 * A GEDCOM source (SOUR) object.
 */
class Source extends GedcomRecord {
	const RECORD_TYPE = 'SOUR';
	const URL_PREFIX  = 'source.php?sid=';

	/**
	 * Each object type may have its own special rules, and re-implement this function.
	 *
	 * @param int $access_level
	 *
	 * @return bool
	 */
	protected function canShowByType($access_level) {
		// Hide sources if they are attached to private repositories ...
		preg_match_all('/\n1 REPO @(.+)@/', $this->gedcom, $matches);
		foreach ($matches[1] as $match) {
			$repo = Repository::getInstance($match, $this->tree);
			if ($repo && !$repo->canShow($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
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
		return '0 @' . $this->xref . "@ SOUR\n1 TITL " . I18N::translate('Private');
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
			"SELECT s_gedcom FROM `##sources` WHERE s_id = :xref AND s_file = :tree_id"
		)->execute(array(
			'xref'    => $xref,
			'tree_id' => $tree_id,
		))->fetchOne();
	}

	/**
	 * Extract names from the GEDCOM record.
	 */
	public function extractNames() {
		parent::extractNamesFromFacts(1, 'TITL', $this->getFacts('TITL'));
	}
}
