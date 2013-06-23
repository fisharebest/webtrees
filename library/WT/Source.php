<?php
// Class file for a Source (SOUR) object
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Source extends WT_GedcomRecord {
	const RECORD_TYPE = 'SOUR';
	const URL_PREFIX  = 'source.php?sid=';

	// Implement source-specific privacy logic
	protected function _canDisplayDetailsByType($access_level) {
		// Hide sources if they are attached to private repositories ...
		preg_match_all('/\n1 REPO @(.+)@/', $this->_gedrec, $matches);
		foreach ($matches[1] as $match) {
			$repo=WT_Repository::getInstance($match);
			if ($repo && !$repo->canDisplayDetails($access_level)) {
				return false;
			}
		}

		// ... otherwise apply default behaviour
		return parent::_canDisplayDetailsByType($access_level);
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		return "0 @".$this->xref."@ ".$this->type."\n1 TITL ".WT_I18N::translate('Private');
	}

	// Fetch the record from the database
	protected static function fetchGedcomRecord($xref, $ged_id) {
		static $statement=null;

		if ($statement===null) {
			$statement=WT_DB::prepare(
				"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec ".
				"FROM `##sources` WHERE s_id=? AND s_file=?"
			);
		}
		return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
	}

	public function getAuth() {
		return get_gedcom_value('AUTH', 1, $this->getGedcomRecord());
	}

	// Get an array of structures containing all the names in the record
	public function getAllNames() {
		return parent::_getAllNames('TITL', 1);
	}
}
