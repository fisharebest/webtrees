<?php
// Class file for a Source (SOUR) object
//
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

class WT_Source extends WT_GedcomRecord {
	const RECORD_TYPE = 'SOUR';
	const SQL_FETCH   = "SELECT s_gedcom FROM `##sources` WHERE s_id=? AND s_file=?";
	const URL_PREFIX  = 'source.php?sid=';

	// Implement source-specific privacy logic
	protected function _canShowByType($access_level) {
		// Hide sources if they are attached to private repositories ...
		preg_match_all('/\n1 REPO @(.+)@/', $this->gedcom, $matches);
		foreach ($matches[1] as $match) {
			$repo=WT_Repository::getInstance($match);
			if ($repo && !$repo->canShow($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
		return parent::_canShowByType($access_level);
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . "@ SOUR\n1 TITL " . WT_I18N::translate('Private');
	}

	// Fetch the record from the database
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement=null;

		if ($statement===null) {
			$statement=WT_DB::prepare("SELECT s_gedcom FROM `##sources` WHERE s_id=? AND s_file=?");
		}

		return $statement->execute(array($xref, $gedcom_id))->fetchOne();
	}

	// Get an array of structures containing all the names in the record
	public function extractNames() {
		parent::_extractNames(1, 'TITL', $this->getFacts('TITL'));
	}
}
