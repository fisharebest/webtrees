<?php
// Privacy Functions
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Can we display a level 1 record?
// Assume we have already called canDisplayRecord() to check the parent level 0 object
function canDisplayFact($xref, $ged_id, $gedrec, $access_level=WT_USER_ACCESS_LEVEL) {
	// TODO - use the privacy settings for $ged_id, not the default gedcom.
	global $HIDE_LIVE_PEOPLE, $person_facts, $global_facts;

	// This setting would better be called "$ENABLE_PRIVACY"
	if (!$HIDE_LIVE_PEOPLE) {
		return true;
	}
	// We should always be able to see details of our own record (unless an admin is applying download restrictions)
	if ($xref==WT_USER_GEDCOM_ID && $ged_id==WT_GED_ID && $access_level==WT_USER_ACCESS_LEVEL) {
		return true;
	}

	// Does this record have a RESN?
	if (strpos($gedrec, "\n2 RESN confidential")) {
		return WT_PRIV_NONE>=$access_level;
	}
	if (strpos($gedrec, "\n2 RESN privacy")) {
		return WT_PRIV_USER>=$access_level;
	}
	if (strpos($gedrec, "\n2 RESN none")) {
		return true;
	}

	// Does this record have a default RESN?
	if (preg_match('/^\n?1 ('.WT_REGEX_TAG.')/', $gedrec, $match)) {
		$tag=$match[1];
		if (isset($person_facts[$xref][$tag])) {
			return $person_facts[$xref][$tag]>=$access_level;
		}
		if (isset($global_facts[$tag])) {
			return $global_facts[$tag]>=$access_level;
		}
	}

	// No restrictions - it must be public
	return true;
}

