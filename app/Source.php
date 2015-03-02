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
 * Class Source - A GEDCOM source (SOUR) object
 */
class Source extends GedcomRecord {
	const RECORD_TYPE = 'SOUR';
	const URL_PREFIX = 'source.php?sid=';

	/**
	 * Get an instance of a source object.  For single records,
	 * we just receive the XREF.  For bulk records (such as lists
	 * and search results) we can receive the GEDCOM data as well.
	 *
	 * @param string       $xref
	 * @param integer|null $gedcom_id
	 * @param string|null  $gedcom
	 *
	 * @return Source|null
	 */
	public static function getInstance($xref, $gedcom_id = WT_GED_ID, $gedcom = null) {
		$record = parent::getInstance($xref, $gedcom_id, $gedcom);

		if ($record instanceof Source) {
			return $record;
		} else {
			return null;
		}
	}

	/** {@inheritdoc} */
	protected function canShowByType($access_level) {
		// Hide sources if they are attached to private repositories ...
		preg_match_all('/\n1 REPO @(.+)@/', $this->gedcom, $matches);
		foreach ($matches[1] as $match) {
			$repo = Repository::getInstance($match);
			if ($repo && !$repo->canShow($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
		return parent::canShowByType($access_level);
	}

	/** {@inheritdoc} */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . "@ SOUR\n1 TITL " . I18N::translate('Private');
	}

	/** {@inheritdoc} */
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement = null;

		if ($statement === null) {
			$statement = Database::prepare("SELECT s_gedcom FROM `##sources` WHERE s_id=? AND s_file=?");
		}

		return $statement->execute(array($xref, $gedcom_id))->fetchOne();
	}

	/** {@inheritdoc} */
	public function extractNames() {
		parent::extractNamesFromFacts(1, 'TITL', $this->getFacts('TITL'));
	}
}
