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
 * A GEDCOM repository (REPO) object.
 */
class Repository extends GedcomRecord {
	const RECORD_TYPE = 'REPO';
	const URL_PREFIX  = 'repo.php?rid=';

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
			"SELECT o_gedcom FROM `##other` WHERE o_id = :xref AND o_file = :tree_id AND o_type = 'REPO'"
		)->execute(array(
			'xref'    => $xref,
			'tree_id' => $tree_id,
		))->fetchOne();
	}

	/**
	 * Generate a private version of this record
	 *
	 * @param int $access_level
	 *
	 * @return string
	 */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . "@ REPO\n1 NAME " . I18N::translate('Private');
	}

	/**
	 * Extract names from the GEDCOM record.
	 */
	public function extractNames() {
		parent::extractNamesFromFacts(1, 'NAME', $this->getFacts('NAME'));
	}
}
