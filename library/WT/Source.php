<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
 * Class WT_Source - A GEDCOM source (SOUR) object
 */
class WT_Source extends WT_GedcomRecord {
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
	 * @return WT_Source|null
	 */
	public static function getInstance($xref, $gedcom_id = WT_GED_ID, $gedcom = null) {
		$record = parent::getInstance($xref, $gedcom_id, $gedcom);

		if ($record instanceof WT_Source) {
			return $record;
		} else {
			return null;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function canShowByType($access_level) {
		// Hide sources if they are attached to private repositories ...
		preg_match_all('/\n1 REPO @(.+)@/', $this->gedcom, $matches);
		foreach ($matches[1] as $match) {
			$repo = WT_Repository::getInstance($match);
			if ($repo && !$repo->canShow($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
		return parent::canShowByType($access_level);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . "@ SOUR\n1 TITL " . WT_I18N::translate('Private');
	}

	/**
	 * {@inheritdoc}
	 */
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement = null;

		if ($statement === null) {
			$statement = WT_DB::prepare("SELECT s_gedcom FROM `##sources` WHERE s_id=? AND s_file=?");
		}

		return $statement->execute(array($xref, $gedcom_id))->fetchOne();
	}

	/**
	 * {@inheritdoc}
	 */
	public function extractNames() {
		parent::_extractNames(1, 'TITL', $this->getFacts('TITL'));
	}
}
