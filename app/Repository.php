<?php
namespace Fisharebest\Webtrees;

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

/**
 * Class Repository - Class file for a Repository (REPO) object
 */
class Repository extends GedcomRecord {
	const RECORD_TYPE = 'REPO';
	const URL_PREFIX = 'repo.php?rid=';

	/**
	 * Get an instance of a repository object.  For single records,
	 * we just receive the XREF.  For bulk records (such as lists
	 * and search results) we can receive the GEDCOM data as well.
	 *
	 * @param string       $xref
	 * @param integer|null $gedcom_id
	 * @param string|null  $gedcom
	 *
	 * @return Repository|null
	 */
	public static function getInstance($xref, $gedcom_id = WT_GED_ID, $gedcom = null) {
		$record = parent::getInstance($xref, $gedcom_id, $gedcom);

		if ($record instanceof Repository) {
			return $record;
		} else {
			return null;
		}
	}

	/** {@inheritdoc} */
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement = null;

		if ($statement === null) {
			$statement = Database::prepare("SELECT o_gedcom FROM `##other` WHERE o_id=? AND o_file=?");
		}

		return $statement->execute(array($xref, $gedcom_id))->fetchOne();
	}

	/** {@inheritdoc} */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . "@ REPO\n1 NAME " . I18N::translate('Private');
	}

	/** {@inheritdoc} */
	public function extractNames() {
		parent::extractNamesFromFacts(1, 'NAME', $this->getFacts('NAME'));
	}
}
