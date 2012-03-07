<?php
// Functions to query the database.
//
// This file implements the datastore functions necessary for webtrees
// to use an SQL database as its datastore.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
//
// Modifications Copyright (c) 2010 Greg Roach
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

////////////////////////////////////////////////////////////////////////////////
// Count the number of records linked to a given record
////////////////////////////////////////////////////////////////////////////////
function count_linked_indi($xref, $link, $ged_id) {
	return
		WT_DB::prepare("SELECT COUNT(*) FROM `##link`, `##individuals` WHERE i_file=l_file AND i_id=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_fam($xref, $link, $ged_id) {
	return
		WT_DB::prepare("SELECT COUNT(*) FROM `##link`, `##families` WHERE f_file=l_file AND f_id=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_note($xref, $link, $ged_id) {
	return
		WT_DB::prepare("SELECT COUNT(*) FROM `##link`, `##other` WHERE o_file=l_file AND o_id=l_from AND o_type=? AND l_file=? AND l_type=? AND l_to=?")
		->execute(array('NOTE', $ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_sour($xref, $link, $ged_id) {
	return
		WT_DB::prepare("SELECT COUNT(*) FROM `##link`, `##sources` WHERE s_file=l_file AND s_id=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_repo($xref, $link, $ged_id) {
	return
		WT_DB::prepare("SELECT COUNT(*) FROM `##link`, `##other` WHERE o_file=l_file AND o_id=l_from AND o_type=? AND l_file=? AND l_type=? AND l_to=?")
		->execute(array('REPO', $ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_obje($xref, $link, $ged_id) {
	return
		WT_DB::prepare("SELECT COUNT(*) FROM `##link`, `##media` WHERE m_gedfile=l_file AND m_media=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Fetch records linked to a given record
////////////////////////////////////////////////////////////////////////////////
function fetch_linked_indi($xref, $link, $ged_id) {
	$rows=WT_DB::prepare(
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec".
		" FROM `##individuals`".
		" JOIN `##link` ON (i_file=l_file AND i_id=l_from)".
		" LEFT JOIN `##name` ON (i_file=n_file AND i_id=n_id AND n_num=0)".
		" WHERE i_file=? AND l_type=? AND l_to=?".
		" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Person::getInstance($row);
	}
	return $list;
}
function fetch_linked_fam($xref, $link, $ged_id) {
	$rows=WT_DB::prepare(
		"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec".
		" FROM `##families`".
		" JOIN `##link` ON (f_file=l_file AND f_id=l_from)".
		" LEFT JOIN `##name` ON (f_file=n_file AND f_id=n_id AND n_num=0)".
		" WHERE f_file=? AND l_type=? AND l_to=?".
		" ORDER BY n_sort" // n_sort is not used for families.  Sorting here has no effect???
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Family::getInstance($row);
	}
	return $list;
}
function fetch_linked_note($xref, $link, $ged_id) {
	$rows=WT_DB::prepare(
		"SELECT 'NOTE' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
		" FROM `##other`".
		" JOIN `##link` ON (o_file=l_file AND o_id=l_from)".
		" LEFT JOIN `##name` ON (o_file=n_file AND o_id=n_id AND n_num=0)".
		" WHERE o_file=? AND o_type='NOTE' AND l_type=? AND l_to=?".
		" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Note::getInstance($row);
	}
	return $list;
}
function fetch_linked_sour($xref, $link, $ged_id) {
	$rows=WT_DB::prepare(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec".
			" FROM `##sources`".
			" JOIN `##link` ON (s_file=l_file AND s_id=l_from)".
			" LEFT JOIN `##name` ON (s_file=n_file AND s_id=n_id AND n_num=0)".
			" WHERE s_file=? AND l_type=? AND l_to=?".
			" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
		)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Source::getInstance($row);
	}
	return $list;
}
function fetch_linked_repo($xref, $link, $ged_id) {
	$rows=WT_DB::prepare(
		"SELECT 'REPO' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
		" FROM `##other`".
		" JOIN `##link` ON (o_file=l_file AND o_id=l_from)".
		" LEFT JOIN `##name` ON (o_file=n_file AND o_id=n_id AND n_num=0)".
		" WHERE o_file=? AND o_type='REPO' AND l_type=? AND l_to=?".
		" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Note::getInstance($row);
	}
	return $list;
}
function fetch_linked_obje($xref, $link, $ged_id) {
	$rows=WT_DB::prepare(
		"SELECT 'OBJE' AS type, m_media AS xref, m_gedfile AS ged_id, m_gedrec AS gedrec, m_titl, m_file".
		" FROM `##media`".
		" JOIN `##link` ON (m_gedfile=l_file AND m_media=l_from)".
		" LEFT JOIN `##name` ON (m_gedfile=n_file AND m_media=n_id AND n_num=0)".
		" WHERE m_gedfile=? AND l_type=? AND l_to=?".
		" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Media::getInstance($row);
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Fetch all records linked to a record - when deleting an object, we must
// also delete all links to it.
////////////////////////////////////////////////////////////////////////////////
function fetch_all_links($xref, $ged_id) {
	return
		WT_DB::prepare("SELECT l_from FROM `##link` WHERE l_file=? AND l_to=?")
		->execute(array($ged_id, $xref))
		->fetchOneColumn();
}

// find the gedcom record for a family
function find_family_record($xref, $ged_id) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT f_gedcom FROM `##families` WHERE f_id=? AND f_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

// find the gedcom record for an individual
function find_person_record($xref, $ged_id) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT i_gedcom FROM `##individuals` WHERE i_id=? AND i_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

// find the gedcom record for a source
function find_source_record($xref, $ged_id) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT s_gedcom FROM `##sources` WHERE s_id=? AND s_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

/**
* Find a repository record by its ID
* @param string $rid the record id
* @param string $gedfile the gedcom file id
*/
function find_other_record($xref, $ged_id) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT o_gedcom FROM `##other` WHERE o_id=? AND o_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

/**
* Find a media record by its ID
* @param string $rid the record id
*/
function find_media_record($xref, $ged_id) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT m_gedrec FROM `##media` WHERE m_media=? AND m_gedfile=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

// Find the gedcom data for a record. Optionally include pending changes.
function find_gedcom_record($xref, $ged_id, $pending=false) {
	if ($pending) {
		// This will return NULL if no record exists, or an empty string if the record has been deleted.
		$gedcom=find_updated_record($xref, $ged_id);
	} else {
		$gedcom=null;
	}

	if (is_null($gedcom)) {
		$gedcom=find_person_record($xref, $ged_id);
	}
	if (is_null($gedcom)) {
		$gedcom=find_family_record($xref, $ged_id);
	}
	if (is_null($gedcom)) {
		$gedcom=find_source_record($xref, $ged_id);
	}
	if (is_null($gedcom)) {
		$gedcom=find_media_record($xref, $ged_id);
	}
	if (is_null($gedcom)) {
		$gedcom=find_other_record($xref, $ged_id);
	}
	return $gedcom;
}

/**
 * find and return an updated gedcom record
 * @param string $gid the id of the record to find
 * @param string $gedfile the gedcom file to get the record from.. defaults to currently active gedcom
 */
function find_updated_record($xref, $ged_id) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT new_gedcom FROM `##change` WHERE gedcom_id=? AND xref=? AND status='pending' ".
			"ORDER BY change_id DESC LIMIT 1"
		);
	}

	// This will return NULL if no record exists, or an empty string if the record has been deleted.
	return $gedcom=$statement->execute(array($ged_id, $xref))->fetchOne();
}

// Find the type of a gedcom record. Check the cache before querying the database.
// Returns 'INDI', 'FAM', etc., or null if the record does not exist.
function gedcom_record_type($xref, $ged_id) {
	global $gedcom_record_cache;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT 'INDI' FROM `##individuals` WHERE i_id   =? AND i_file   =? UNION ALL ".
			"SELECT 'FAM'  FROM `##families`    WHERE f_id   =? AND f_file   =? UNION ALL ".
			"SELECT 'SOUR' FROM `##sources`     WHERE s_id   =? AND s_file   =? UNION ALL ".
			"SELECT 'OBJE' FROM `##media`       WHERE m_media=? AND m_gedfile=? UNION ALL ".
			"SELECT o_type FROM `##other`       WHERE o_id   =? AND o_file   =?"
		);
	}

	if (isset($gedcom_record_cache[$xref][$ged_id])) {
		return $gedcom_record_cache[$xref][$ged_id]->getType();
	} else {
		return $statement->execute(array($xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id))->fetchOne();
	}
}

// Find out if there are any pending changes that a given user may accept
function exists_pending_change($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	if (userCanAccept($user_id, $ged_id)) {
		return
			WT_DB::prepare(
				"SELECT 1".
				" FROM `##change`".
				" WHERE status='pending' AND gedcom_id=?"
			)->execute(array($ged_id))->fetchOne();
	} else {
		return false;
	}
}

// get a list of all the sources
function get_source_list($ged_id) {
	$rows=
		WT_DB::prepare("SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM `##sources` s WHERE s_file=?")
		->execute(array($ged_id))
		->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Source::getInstance($row);
	}
	usort($list, array('WT_GedcomRecord', 'Compare'));
	return $list;
}

// Get a list of repositories from the database
// $ged_id - the gedcom to search
function get_repo_list($ged_id) {
	$rows=
		WT_DB::prepare("SELECT 'REPO' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM `##other` WHERE o_type='REPO' AND o_file=?")
		->execute(array($ged_id))
		->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Repository::getInstance($row);
	}
	usort($list, array('WT_GedcomRecord', 'Compare'));
	return $list;
}

//-- get the shared note list from the datastore
function get_note_list($ged_id) {
	$rows=
		WT_DB::prepare("SELECT 'NOTE' AS type, o_id AS xref, {$ged_id} AS ged_id, o_gedcom AS gedrec FROM `##other` WHERE o_type=? AND o_file=?")
		->execute(array('NOTE', $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=WT_Note::getInstance($row);
	}
	usort($list, array('WT_GedcomRecord', 'Compare'));
	return $list;
}


// Search for INDIs using custom SQL generated by the report engine
function search_indis_custom($join, $where, $order) {
	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec FROM `##individuals` ".implode(' ', $join).' WHERE '.implode(' AND ', $where);
	if ($order) {
		$sql.=' ORDER BY '.implode(' ', $order);
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$list[]=WT_Person::getInstance($row);
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search for FAMs using custom SQL generated by the report engine
function search_fams_custom($join, $where, $order) {
	$sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec FROM `##families` ".implode(' ', $join).' WHERE '.implode(' AND ', $where);
	if ($order) {
		$sql.=' ORDER BY '.implode(' ', $order);
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$list[]=WT_Family::getInstance($row);
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search the gedcom records of indis
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_indis($query, $geds, $match, $skip) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(utf8_strtoupper($q), '/');
		$querysql[]="i_gedcom LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."'";
	}

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec FROM `##individuals` WHERE (".implode(" {$match} ", $querysql).') AND i_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$record=WT_Person::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($record->getGedcomRecord());
		if ($skip) {
			$gedrec=preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*/', '', $gedrec);
		}
		foreach ($queryregex as $regex) {
			if (!preg_match('/\n\d '.WT_REGEX_TAG.' .*'.$regex.'/', $gedrec)) {
				continue 2;
			}
		}
		$list[]=$record;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search the names of indis
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
function search_indis_names($query, $geds, $match) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	foreach ($query as $q) {
		$querysql[]="n_full LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."'";
	}
	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, n_num FROM `##individuals` JOIN `##name` ON i_id=n_id AND i_file=n_file WHERE (".implode(" {$match} ", $querysql).') AND i_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=WT_Person::getInstance($row);
		if ($indi->canDisplayName()) {
			$indi->setPrimaryName($row['n_num']);
			// We need to clone $indi, as we may have multiple references to the
			// same person in this list, and the "primary name" would otherwise
			// be shared amongst all of them.  This has some performance/memory
			// implications, and there is probably a better way.  This, however,
			// is clean, easy and works.
			$list[]=clone $indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search for individuals names/places using soundex
// $soundex - standard or dm
// $lastname, $firstname, $place - search terms
// $geds - array of gedcoms to search
function search_indis_soundex($soundex, $lastname, $firstname, $place, $geds) {
	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec FROM `##individuals`";
	if ($place) {
		$sql.=" JOIN `##placelinks` ON (pl_file=i_file AND pl_gid=i_id)";
		$sql.=" JOIN `##places` ON (p_file=pl_file AND pl_p_id=p_id)";
	}
	if ($firstname || $lastname) {
		$sql.=" JOIN `##name` ON (i_file=n_file AND i_id=n_id)";
			}
	$sql.=' WHERE i_file IN ('.implode(',', $geds).')';
	switch ($soundex) {
	case 'Russell':
		$givn_sdx=explode(':', soundex_std($firstname));
		$surn_sdx=explode(':', soundex_std($lastname));
		$plac_sdx=explode(':', soundex_std($place));
		$field='std';
		break;
	default:
	case 'DaitchM':
		$givn_sdx=explode(':', soundex_dm($firstname));
		$surn_sdx=explode(':', soundex_dm($lastname));
		$plac_sdx=explode(':', soundex_dm($place));
		$field='dm';
		break;
	}
	if ($firstname && $givn_sdx) {
		foreach ($givn_sdx as $k=>$v) {
			$givn_sdx[$k]="n_soundex_givn_{$field} LIKE ".WT_DB::quote("%{$v}%");
	}
		$sql.=' AND ('.implode(' OR ', $givn_sdx).')';
		}
	if ($lastname && $surn_sdx) {
		foreach ($surn_sdx as $k=>$v) {
			$surn_sdx[$k]="n_soundex_surn_{$field} LIKE ".WT_DB::quote("%{$v}%");
		}
		$sql.=' AND ('.implode(' OR ', $surn_sdx).')';
			}
	if ($place && $plac_sdx) {
		foreach ($plac_sdx as $k=>$v) {
			$plac_sdx[$k]="p_{$field}_soundex LIKE ".WT_DB::quote("%{$v}%");
		}
		$sql.=' AND ('.implode(' OR ', $plac_sdx).')';
	}

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=WT_Person::getInstance($row);
		if ($indi->canDisplayName()) {
			$list[]=$indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

/**
* get recent changes since the given julian day inclusive
* @author yalnifj
* @param int $jd, leave empty to include all
*/
function get_recent_changes($jd=0, $allgeds=false) {
	$sql="SELECT d_gid FROM `##dates` WHERE d_fact='CHAN' AND d_julianday1>=?";
	$vars=array($jd);
	if (!$allgeds) {
		$sql.=" AND d_file=?";
		$vars[]=WT_GED_ID;
	}
	$sql.=" ORDER BY d_julianday1 DESC";

	return WT_DB::prepare($sql)->execute($vars)->fetchOneColumn();
}

// Seach for individuals with events on a given day
function search_indis_dates($day, $month, $year, $facts) {
	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec FROM `##individuals` JOIN `##dates` ON i_id=d_gid AND i_file=d_file WHERE i_file=?";
	$vars=array(WT_GED_ID);
	if ($day) {
		$sql.=" AND d_day=?";
		$vars[]=$day;
	}
	if ($month) {
		$sql.=" AND d_month=?";
		$vars[]=$month;
	}
	if ($year) {
		$sql.=" AND d_year=?";
		$vars[]=$year;
	}
	if ($facts) {
		$facts=preg_split('/[, ;]+/', $facts);
		foreach ($facts as $key=>$value) {
			if ($value[0]=='!') {
				$facts[$key]="d_fact!=?";
				$vars[]=substr($value,1);
			} else {
				$facts[$key]="d_fact=?";
				$vars[]=$value;
			}
		}
		$sql.=' AND '.implode(' AND ', $facts);
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->execute($vars)->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $row) {
		$list[]=WT_Person::getInstance($row);
	}
	return $list;
}

// Seach for individuals with events in a given date range
function search_indis_daterange($start, $end, $facts) {
	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec FROM `##individuals` JOIN `##dates` ON i_id=d_gid AND i_file=d_file WHERE i_file=? AND d_julianday1 BETWEEN ? AND ?";
	$vars=array(WT_GED_ID, $start, $end);

	if ($facts) {
		$facts=explode(',', $facts);
		foreach ($facts as $key=>$value) {
			$facts[$key]="?";
			$vars[]=$value;
		}
		$sql.=' AND d_fact IN ('.implode(',', $facts).')';
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->execute($vars)->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $row) {
		$list[]=WT_Person::getInstance($row);
	}
	return $list;
}

// Search for people who had events in a given year range
function search_indis_year_range($startyear, $endyear) {
	// TODO: We should use Julian-days, rather than gregorian years,
	// to allow
	// the lifespan chart, etc., to use other calendars.
	$startjd=WT_Date_Gregorian::YMDtoJD($startyear, 1, 1);
	$endjd  =WT_Date_Gregorian::YMDtoJD($endyear+1, 1, 1)-1;

	return search_indis_daterange($startjd, $endjd, '');
}

// Search the gedcom records of families
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_fams($query, $geds, $match, $skip) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(utf8_strtoupper($q), '/');
		$querysql[]="f_gedcom LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."'";
	}

	$sql="SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec FROM `##families` WHERE (".implode(" {$match} ", $querysql).') AND f_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$record=WT_Family::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($record->getGedcomRecord());
		if ($skip) {
			$gedrec=preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*/', '', $gedrec);
		}
		foreach ($queryregex as $regex) {
			if (!preg_match('/\n\d '.WT_REGEX_TAG.' .*'.$regex.'/', $gedrec)) {
				continue 2;
			}
		}
		$list[]=$record;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search the names of the husb/wife in a family
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
function search_fams_names($query, $geds, $match) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	foreach ($query as $q) {
		$querysql[]="(husb.n_full LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."' OR wife.n_full LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."')";
	}

	$sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec FROM `##families` LEFT OUTER JOIN `##name` husb ON f_husb=husb.n_id AND f_file=husb.n_file LEFT OUTER JOIN `##name` wife ON f_wife=wife.n_id AND f_file=wife.n_file WHERE (".implode(" {$match} ", $querysql).') AND f_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=WT_Family::getInstance($row);
		if ($indi->canDisplayName()) {
			$list[]=$indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search the gedcom records of sources
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_sources($query, $geds, $match, $skip) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(utf8_strtoupper($q), '/');
		$querysql[]="s_gedcom LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."'";
	}

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM `##sources` WHERE (".implode(" {$match} ", $querysql).') AND s_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$record=WT_Source::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($record->getGedcomRecord());
		if ($skip) {
			$gedrec=preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*/', '', $gedrec);
		}
		foreach ($queryregex as $regex) {
			if (!preg_match('/\n\d '.WT_REGEX_TAG.' .*'.$regex.'/', $gedrec)) {
				continue 2;
			}
		}
		$list[]=$record;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

// Search the gedcom records of shared notes
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_notes($query, $geds, $match, $skip) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(utf8_strtoupper($q), '/');
		$querysql[]="o_gedcom LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."'";
	}

	$sql="SELECT 'NOTE' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM `##other` WHERE (".implode(" {$match} ", $querysql).") AND o_type='NOTE' AND o_file IN (".implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$record=WT_Note::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($record->getGedcomRecord());
		if ($skip) {
			$gedrec=preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*/', '', $gedrec);
		}
		foreach ($queryregex as $regex) {
			if (!preg_match('/\n\d '.WT_REGEX_TAG.' .*'.$regex.'/', $gedrec)) {
				continue 2;
			}
		}
		$list[]=$record;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}


// Search the gedcom records of repositories
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_repos($query, $geds, $match, $skip) {
	global $GEDCOM;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	// Convert the query into a regular expression
	$queryregex=array();

	foreach ($query as $q) {
		$queryregex[]=preg_quote(utf8_strtoupper($q), '/');
		$querysql[]="o_gedcom LIKE ".WT_DB::quote("%{$q}%")." COLLATE '".WT_I18N::$collation."'";
	}

	$sql="SELECT 'REPO' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM `##other` WHERE (".implode(" {$match} ", $querysql).") AND o_type='REPO' AND o_file IN (".implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_gedcom_settings($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$record=WT_Note::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($record->getGedcomRecord());
		if ($skip) {
			$gedrec=preg_replace('/\n\d (_UID|_WT_USER|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*/', '', $gedrec);
		}
		foreach ($queryregex as $regex) {
			if (!preg_match('/\n\d '.WT_REGEX_TAG.' .*'.$regex.'/', $gedrec)) {
				continue 2;
			}
		}
		$list[]=$record;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_gedcom_settings(WT_GED_ID);
	}
	return $list;
}

/**
* get place parent ID
* @param array $parent
* @param int $level
* @return int
*/
function get_place_parent_id($parent, $level) {
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare("SELECT p_id FROM `##places` WHERE p_level=? AND p_parent_id=? AND p_place LIKE ? AND p_file=?");
	}

	$parent_id=0;
	for ($i=0; $i<$level; $i++) {
		$p_id=$statement->execute(array($i, $parent_id, $parent[$i], WT_GED_ID))->fetchOne();
		if (is_null($p_id)) {
			break;
		}
		$parent_id = $p_id;
	}
	return $parent_id;
}

/**
* find all of the places in the hierarchy
* The $parent array holds the parent hierarchy of the places
* we want to get.  The level holds the level in the hierarchy that
* we are at.
*/
function get_place_list($parent, $level) {
	// --- find all of the place in the file
	if ($level==0) {
		return
			WT_DB::prepare("SELECT p_place FROM `##places` WHERE p_level=? AND p_file=? ORDER BY p_place")
			->execute(array(0, WT_GED_ID))
			->fetchOneColumn();
	} else {
		return
			WT_DB::prepare("SELECT p_place FROM `##places` WHERE p_level=? AND p_parent_id=? AND p_file=? ORDER BY p_place")
			->execute(array($level, get_place_parent_id($parent, $level), WT_GED_ID))
			->fetchOneColumn();
	}
}

/**
* get all of the place connections
* @param array $parent
* @param int $level
* @return array
*/
function get_place_positions($parent, $level=null) {
	// TODO: this function needs splitting into two

	if ($level!==null) {
		// placelist.php - we know the exact hierarchy
		$rows=
			WT_DB::prepare("SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id=? AND pl_file=?")
			->execute(array(get_place_parent_id($parent, $level), WT_GED_ID))
			->fetchOneColumn();
		$place_regex='\n2 PLAC '.'(.*)'.preg_quote(implode(', ', array_reverse($parent)), '/').'(\n|$)';
		// The placelinks table does not take account of private records.
		$xrefs=array();
		foreach ($rows as $row) {
			$record=WT_GedcomRecord::getInstance($row);
			if ($record && preg_match('/'.$place_regex.'/i', $record->getGedcomRecord())) {
				$xrefs[]=$row;
			}
		}
	} else {
		// lifespan.php - we don't know the level so get the any matching place
		$rows=
			WT_DB::prepare("SELECT DISTINCT pl_gid FROM `##placelinks`, `##places` WHERE p_place LIKE ? AND p_file=pl_file AND p_id=pl_p_id AND p_file=?")
			->execute(array($parent, WT_GED_ID))
			->fetchOneColumn();
		$place_regex='\n2 PLAC '.preg_quote($parent, '/').'(\n|,|$)';
		// The placelinks table does not take account of private person records.
		$xrefs=array();
		foreach ($rows as $row) {
			$indi=WT_Person::getInstance($row);
			if ($indi && preg_match('/'.$place_regex.'/i', $indi->getGedcomRecord())) {
				$xrefs[]=$row;
			}
		}
	}
	return $xrefs;
}

//-- find all of the places
function find_place_list($place) {
	$rows=
		WT_DB::prepare("SELECT p_id, p_place, p_parent_id  FROM `##places` WHERE p_file=? ORDER BY p_parent_id, p_id")
		->execute(array(WT_GED_ID))
		->fetchAll();

	$placelist=array();
	foreach ($rows as $row) {
		if ($row->p_parent_id==0) {
			$placelist[$row->p_id] = $row->p_place;
		} else {
			$placelist[$row->p_id] = $placelist[$row->p_parent_id].", ".$row->p_place;
		}
	}
	if (!empty($place)) {
		$found = array();
		foreach ($placelist as $indexval => $pplace) {
			if (stripos($pplace, $place)!==false) {
				$upperplace = utf8_strtoupper($pplace);
				if (!isset($found[$upperplace])) {
					$found[$upperplace] = $pplace;
				}
			}
		}
		$placelist = array_values($found);
	}
	return $placelist;
}

//-- function to find the gedcom id for the given rin
function find_rin_id($rin) {
	$xref=
		WT_DB::prepare("SELECT i_id FROM `##individuals` WHERE i_rin=? AND i_file=?")
		->execute(array($rin, WT_GED_ID))
		->fetchOne();

	return $xref ? $xref : $rin;
}

/**
* Delete a gedcom from the database and the system
* Does not delete the file from the file system
* @param string $ged  the filename of the gedcom to delete
*/
function delete_gedcom($ged_id) {
	// If this is the current default, then unset it
	if (get_site_setting('DEFAULT_GEDCOM')==get_gedcom_from_id($ged_id)) {
		set_site_setting('DEFAULT_GEDCOM', '');
	}
	// Don't delete the logs.
	WT_DB::prepare("UPDATE `##log` SET gedcom_id=NULL   WHERE gedcom_id =?")->execute(array($ged_id));

	WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE gedcom_id=?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##block`               WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##dates`               WHERE d_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##families`            WHERE f_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##user_gedcom_setting` WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##gedcom_setting`      WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##individuals`         WHERE i_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##link`                WHERE l_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##media`               WHERE m_gedfile =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##media_mapping`       WHERE mm_gedfile=?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##module_privacy`      WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##name`                WHERE n_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##next_id`             WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##other`               WHERE o_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##placelinks`          WHERE pl_file   =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##places`              WHERE p_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##sources`             WHERE s_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##hit_counter`         WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##change`              WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##default_resn`        WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##gedcom_chunk`        WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM `##gedcom`              WHERE gedcom_id =?")->execute(array($ged_id));
}

/**
* get the top surnames
* @param int $ged_id fetch surnames from this gedcom
* @param int $min only fetch surnames occuring this many times
* @param int $max only fetch this number of surnames (0=all)
* @return array
*/
function get_top_surnames($ged_id, $min, $max) {
	// Use n_surn, rather than n_surname, as it is used to generate url's for
	// the indi-list, etc.
	$max=(int)$max;
	if ($max==0) {
		return
			WT_DB::prepare("SELECT SQL_CACHE n_surn, COUNT(n_surn) FROM `##name` WHERE n_file=? AND n_type!=? AND n_surn NOT IN (?, ?, ?, ?) GROUP BY n_surn HAVING COUNT(n_surn)>=? ORDER BY 2 DESC")
			->execute(array($ged_id, '_MARNM', '@N.N.', '', '?', 'UNKNOWN', $min))
			->fetchAssoc();
	} else {
		return
			WT_DB::prepare("SELECT SQL_CACHE n_surn, COUNT(n_surn) FROM `##name` WHERE n_file=? AND n_type!=? AND n_surn NOT IN (?, ?, ?, ?) GROUP BY n_surn HAVING COUNT(n_surn)>=? ORDER BY 2 DESC LIMIT ".$max)
			->execute(array($ged_id, '_MARNM', '@N.N.', '', '?', 'UNKNOWN', $min))
			->fetchAssoc();
	}
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of events whose anniversary occured on a given julian day.
// Used on the on-this-day/upcoming blocks and the day/month calendar views.
// $jd     - the julian day
// $facts  - restrict the search to just these facts or leave blank for all
// $ged_id - the id of the gedcom to search
////////////////////////////////////////////////////////////////////////////////
function get_anniversary_events($jd, $facts='', $ged_id=WT_GED_ID) {
	// If no facts specified, get all except these
	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";
	if ($facts!='_TODO') {
		$skipfacts.=',_TODO';
	}

	$found_facts=array();
	foreach (array(new WT_Date_Gregorian($jd), new WT_Date_Julian($jd), new WT_Date_French($jd), new WT_Date_Jewish($jd), new WT_Date_Hijri($jd), new WT_Date_Jalali($jd)) as $anniv) {
		// Build a SQL where clause to match anniversaries in the appropriate calendar.
		$where="WHERE d_type='".$anniv->CALENDAR_ESCAPE()."'";
		// SIMPLE CASES:
		// a) Non-hebrew anniversaries
		// b) Hebrew months TVT, SHV, IYR, SVN, TMZ, AAV, ELL
		if ($anniv->CALENDAR_ESCAPE()!='@#DHEBREW@' || in_array($anniv->m, array(1, 5, 9, 10, 11, 12, 13))) {
			// Dates without days go on the first day of the month
			// Dates with invalid days go on the last day of the month
			if ($anniv->d==1) {
				$where.=" AND d_day<=1";
			} else
				if ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d}";
				} else {
					$where.=" AND d_day={$anniv->d}";
				}
			$where.=" AND d_mon={$anniv->m}";
		} else {
			// SPECIAL CASES:
			switch ($anniv->m) {
			case 2:
				// 29 CSH does not include 30 CSH (but would include an invalid 31 CSH if there were no 30 CSH)
				if ($anniv->d==1) {
					$where.=" AND d_day<=1 AND d_mon=2";
				} elseif ($anniv->d==30) {
					$where.=" AND d_day>=30 AND d_mon=2";
				} elseif ($anniv->d==29 && $anniv->DaysInMonth()==29) {
					$where.=" AND (d_day=29 OR d_day>30) AND d_mon=2";
				} else {
					$where.=" AND d_day={$anniv->d} AND d_mon=2";
				}
				break;
			case 3:
				// 1 KSL includes 30 CSH (if this year didn't have 30 CSH)
				// 29 KSL does not include 30 KSL (but would include an invalid 31 KSL if there were no 30 KSL)
				if ($anniv->d==1) {
					$tmp=new WT_Date_Jewish(array($anniv->y, 'csh', 1));
					if ($tmp->DaysInMonth()==29) {
						$where.=" AND (d_day<=1 AND d_mon=3 OR d_day=30 AND d_mon=2)";
					} else {
						$where.=" AND d_day<=1 AND d_mon=3";
					}
				} else
					if ($anniv->d==30) {
						$where.=" AND d_day>=30 AND d_mon=3";
					} elseif ($anniv->d==29 && $anniv->DaysInMonth()==29) {
						$where.=" AND (d_day=29 OR d_day>30) AND d_mon=3";
					} else {
						$where.=" AND d_day={$anniv->d} AND d_mon=3";
					}
				break;
			case 4:
				// 1 TVT includes 30 KSL (if this year didn't have 30 KSL)
				if ($anniv->d==1) {
					$tmp=new WT_Date_Jewish($anniv->y, 'ksl', 1);
					if ($tmp->DaysInMonth()==29) {
						$where.=" AND (d_day<=1 AND d_mon=4 OR d_day=30 AND d_mon=3)";
					} else {
						$where.=" AND d_day<=1 AND d_mon=4";
					}
				} else
					if ($anniv->d==$anniv->DaysInMonth()) {
						$where.=" AND d_day>={$anniv->d} AND d_mon=4";
					} else {
						$where.=" AND d_day={$anniv->d} AND d_mon=4";
					}
				break;
			case 6: // ADR (non-leap) includes ADS (leap)
				if ($anniv->d==1) {
					$where.=" AND d_day<=1";
				} elseif ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d}";
				} else {
					$where.=" AND d_day={$anniv->d}";
				}
				if ($anniv->IsLeapYear()) {
					$where.=" AND (d_mon=6 AND MOD(7*d_year+1, 19)<7)";
				} else {
					$where.=" AND (d_mon=6 OR d_mon=7)";
				}
				break;
			case 7: // ADS includes ADR (non-leap)
				if ($anniv->d==1) {
					$where.=" AND d_day<=1";
				} elseif ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d}";
				} else {
					$where.=" AND d_day={$anniv->d}";
				}
				$where.=" AND (d_mon=6 AND MOD(7*d_year+1, 19)>=7 OR d_mon=7)";
				break;
			case 8: // 1 NSN includes 30 ADR, if this year is non-leap
				if ($anniv->d==1) {
					if ($anniv->IsLeapYear()) {
						$where.=" AND d_day<=1 AND d_mon=8";
					} else {
						$where.=" AND (d_day<=1 AND d_mon=8 OR d_day=30 AND d_mon=6)";
					}
				} elseif ($anniv->d==$anniv->DaysInMonth()) {
					$where.=" AND d_day>={$anniv->d} AND d_mon=8";
				} else {
					$where.=" AND d_day={$anniv->d} AND d_mon=8";
				}
				break;
			}
		}
		// Only events in the past (includes dates without a year)
		$where.=" AND d_year<={$anniv->y}";
		// Restrict to certain types of fact
		if (empty($facts)) {
			$excl_facts="'".preg_replace('/\W+/', "','", $skipfacts)."'";
			$where.=" AND d_fact NOT IN ({$excl_facts})";
		} else {
			$incl_facts="'".preg_replace('/\W+/', "','", $facts)."'";
			$where.=" AND d_fact IN ({$incl_facts})";
		}
		// Only get events from the current gedcom
		$where.=" AND d_file=".$ged_id;

		// Now fetch these anniversaries
		$ind_sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, d_type, d_day, d_month, d_year, d_fact FROM `##dates`, `##individuals` {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_day ASC, d_year DESC";
		$fam_sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, d_type, d_day, d_month, d_year, d_fact FROM `##dates`, `##families` {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_day ASC, d_year DESC";
		foreach (array($ind_sql, $fam_sql) as $sql) {
			$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				if ($row['type']=='INDI') {
					$record=WT_Person::getInstance($row);
				} else {
					$record=WT_Family::getInstance($row);
				}
				if ($record->canDisplayDetails()) {
					// Generate a regex to match the retrieved date - so we can find it in the original gedcom record.
					// TODO having to go back to the original gedcom is lame.  This is why it is so slow.
					// We should store the level1 fact here (or in a "facts" table)
					if ($row['d_type']=='@#DJULIAN@') {
						if ($row['d_year']<0) {
							$year_regex=$row['d_year'].' ?[Bb]\.? ?[Cc]\.\ ?';
						} else {
							$year_regex="({$row['d_year']}|".($row['d_year']-1)."\/".($row['d_year']%100).")";
						}
					} else
						$year_regex="0*".$row['d_year'];
					$ged_date_regex="/2 DATE.*(".($row['d_day']>0 ? "0?{$row['d_day']}\s*" : "").$row['d_month']."\s*".($row['d_year']!=0 ? $year_regex : "").")/i";
					foreach (get_all_subrecords($row['gedrec'], $skipfacts, false, false) as $factrec) {
						if (preg_match("/(^1 {$row['d_fact']}|^1 (FACT|EVEN).*\n2 TYPE {$row['d_fact']})/s", $factrec) && preg_match($ged_date_regex, $factrec) && preg_match('/2 DATE (.+)/', $factrec, $match)) {
							$date=new WT_Date($match[1]);
							if (preg_match('/2 PLAC (.+)/', $factrec, $match)) {
								$plac=$match[1];
							} else {
								$plac='';
							}
							if (canDisplayFact($row['xref'], $ged_id, $factrec)) {
								$found_facts[]=array(
									'record'=>$record,
									'id'=>$row['xref'],
									'objtype'=>$row['type'],
									'fact'=>$row['d_fact'],
									'factrec'=>$factrec,
									'jd'=>$jd,
									'anniv'=>($row['d_year']==0?0:$anniv->y-$row['d_year']),
									'date'=>$date,
									'plac'=>$plac
								);
							}
						}
					}
				}
			}
		}
	}
	return $found_facts;
}


////////////////////////////////////////////////////////////////////////////////
// Get a list of events which occured during a given date range.
// TODO: Used by the recent-changes block and the calendar year view.
// $jd1, $jd2 - the range of julian day
// $facts     - restrict the search to just these facts or leave blank for all
// $ged_id    - the id of the gedcom to search
////////////////////////////////////////////////////////////////////////////////
function get_calendar_events($jd1, $jd2, $facts='', $ged_id=WT_GED_ID) {
	// If no facts specified, get all except these
	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";
	if ($facts!='_TODO') {
		$skipfacts.=',_TODO';
	}

	$found_facts=array();

	// This where clause gives events that start/end/overlap the period
	// e.g. 1914-1918 would show up on 1916
	//$where="WHERE d_julianday1 <={$jd2} AND d_julianday2>={$jd1}";
	// This where clause gives only events that start/end during the period
	$where="WHERE (d_julianday1>={$jd1} AND d_julianday1<={$jd2} OR d_julianday2>={$jd1} AND d_julianday2<={$jd2})";

	// Restrict to certain types of fact
	if (empty($facts)) {
		$excl_facts="'".preg_replace('/\W+/', "','", $skipfacts)."'";
		$where.=" AND d_fact NOT IN ({$excl_facts})";
	} else {
		$incl_facts="'".preg_replace('/\W+/', "','", $facts)."'";
		$where.=" AND d_fact IN ({$incl_facts})";
	}
	// Only get events from the current gedcom
	$where.=" AND d_file=".$ged_id;

	// Now fetch these events
	$ind_sql="SELECT d_gid, i_gedcom, 'INDI', d_type, d_day, d_month, d_year, d_fact, d_type FROM `##dates`, `##individuals` {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_julianday1";
	$fam_sql="SELECT d_gid, f_gedcom, 'FAM',  d_type, d_day, d_month, d_year, d_fact, d_type FROM `##dates`, `##families`    {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_julianday1";
	foreach (array($ind_sql, $fam_sql) as $sql) {
		$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_NUM);
		foreach ($rows as $row) {
			// Generate a regex to match the retrieved date - so we can find it in the original gedcom record.
			// TODO having to go back to the original gedcom is lame.  This is why it is so slow, and needs
			// to be cached.  We should store the level1 fact here (or somewhere)
			if ($row[8]=='@#DJULIAN@') {
				if ($row[6]<0) {
					$year_regex=$row[6].' ?[Bb]\.? ?[Cc]\.\ ?';
				} else {
					$year_regex="({$row[6]}|".($row[6]-1)."\/".($row[6]%100).")";
				}
			} else {
				$year_regex="0*".$row[6];
			}
			$ged_date_regex="/2 DATE.*(".($row[4]>0 ? "0?{$row[4]}\s*" : "").$row[5]."\s*".($row[6]!=0 ? $year_regex : "").")/i";
			foreach (get_all_subrecords($row[1], $skipfacts, false, false) as $factrec) {
				if (preg_match("/(^1 {$row[7]}|^1 (FACT|EVEN).*\n2 TYPE {$row[7]})/s", $factrec) && preg_match($ged_date_regex, $factrec) && preg_match('/2 DATE (.+)/', $factrec, $match)) {
					$date=new WT_Date($match[1]);
					if (preg_match('/2 PLAC (.+)/', $factrec, $match)) {
						$plac=$match[1];
					} else {
						$plac='';
					}
					if (canDisplayFact($row[0], $ged_id, $factrec)) {
						$found_facts[]=array(
							'id'=>$row[0],
							'objtype'=>$row[2],
							'fact'=>$row[7],
							'factrec'=>$factrec,
							'jd'=>$jd1,
							'anniv'=>0,
							'date'=>$date,
							'plac'=>$plac
						);
					}
				}
			}
		}
	}
	return $found_facts;
}


/**
* Get the list of current and upcoming events, sorted by anniversary date
*
* This function is used by the Todays and Upcoming blocks on the Index and Portal
* pages.
*
* Special note on unknown day-of-month:
* When the anniversary date is imprecise, the sort will pretend that the day-of-month
* is either tomorrow or the first day of next month.  These imprecise anniversaries
* will sort to the head of the chosen day.
*
* Special note on Privacy:
* This routine does not check the Privacy of the events in the list.  That check has
* to be done by the routine that makes use of the event list.
*/
function get_events_list($jd1, $jd2, $events='') {
	$found_facts=array();
	for ($jd=$jd1; $jd<=$jd2; ++$jd) {
		$found_facts=array_merge($found_facts, get_anniversary_events($jd, $events));
	}
	return $found_facts;
}

////////////////////////////////////////////////////////////////////////////////
// Check if a media file is shared (i.e. used by another gedcom)
////////////////////////////////////////////////////////////////////////////////
function is_media_used_in_other_gedcom($file_name, $ged_id) {
	return
		(bool)WT_DB::prepare("SELECT COUNT(*) FROM `##media` WHERE m_file LIKE ? AND m_gedfile<>?")
		->execute(array("%{$file_name}", $ged_id))
		->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_SITE_SETTING table
// We can't cache/reuse prepared statements here, as we need to call these
// functions after performing DDL statements, and these invalidate any
// existing prepared statement handles in some databases.
////////////////////////////////////////////////////////////////////////////////
function get_site_setting($setting_name, $default_value=null) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE setting_value FROM `##site_setting` WHERE setting_name=?"
		)->execute(array($setting_name))->fetchOne($default_value);
}

function set_site_setting($setting_name, $setting_value) {
	if (get_site_setting($setting_name)!=$setting_value) {
		AddToLog('Site setting "'.$setting_name.'" set to "'.$setting_value.'"', 'config');
	}
	if (is_null($setting_value)) {
		WT_DB::prepare("DELETE FROM `##site_setting` WHERE setting_name=?")
			->execute(array($setting_name));
	} else {
		$rowcount=WT_DB::prepare("REPLACE INTO `##site_setting` (setting_name, setting_value) VALUES (?, LEFT(?, 255))")
			->execute(array($setting_name, $setting_value));
	}
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_GEDCOM table
////////////////////////////////////////////////////////////////////////////////

function get_all_gedcoms() {
	return
		WT_DB::prepare("SELECT SQL_CACHE gedcom_id, gedcom_name FROM `##gedcom` WHERE gedcom_id>0 ORDER BY gedcom_name")
		->fetchAssoc();
}

function get_gedcom_count() {
	return
		WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##gedcom` WHERE gedcom_id>0")
		->fetchOne();
}

function get_gedcom_titles() {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE g.gedcom_id, g.gedcom_name, COALESCE(gs.setting_value, g.gedcom_name) AS gedcom_title".
			" FROM `##gedcom` g".
			" LEFT JOIN `##gedcom_setting` gs ON (g.gedcom_id=gs.gedcom_id AND gs.setting_name=?)".
			" WHERE g.gedcom_id>0".
			" ORDER BY g.sort_order, 3"
		)
		->execute(array('title'))
		->fetchAll();
}

function get_gedcom_from_id($ged_id) {
	// No need to look up the default gedcom
	if (defined('WT_GED_ID') && defined('WT_GEDCOM') && $ged_id==WT_GED_ID) {
		return WT_GEDCOM;
	}

	return
		WT_DB::prepare("SELECT SQL_CACHE gedcom_name FROM `##gedcom` WHERE gedcom_id=?")
		->execute(array($ged_id))
		->fetchOne();
}

// Convert an (external) gedcom name to an (internal) gedcom ID.
// Optionally create an entry for it, if it does not exist.
function get_id_from_gedcom($ged_name, $create=false) {
	// No need to look up the default gedcom
	if (defined('WT_GED_ID') && defined('WT_GEDCOM') && $ged_name==WT_GEDCOM) {
		return WT_GED_ID;
	}

	if ($create) {
		try {
			WT_DB::prepare("INSERT INTO `##gedcom` (gedcom_name) VALUES (?)")
				->execute(array($ged_name));
			$ged_id=WT_DB::getInstance()->lastInsertId();
			require WT_ROOT.'includes/set_gedcom_defaults.php';

			// Set the initial block layot
			WT_DB::prepare(
				"INSERT INTO `##block` (gedcom_id, location, block_order, module_name)".
				" SELECT ?, location, block_order, module_name".
				" FROM `##block`".
				" WHERE gedcom_id=-1"
			)->execute(array($ged_id));
			return $ged_id;
		} catch (PDOException $ex) {
			// The gedcom already exists - can't create
		}
	}

	return
		WT_DB::prepare("SELECT SQL_CACHE gedcom_id FROM `##gedcom` WHERE gedcom_name=?")
		->execute(array($ged_name))
		->fetchOne();
}


////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_GEDCOM_SETTING table
////////////////////////////////////////////////////////////////////////////////

function get_gedcom_setting($gedcom_id, $setting_name, $default_value=null) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE setting_value FROM `##gedcom_setting` WHERE gedcom_id=? AND setting_name=?"
		)->execute(array($gedcom_id, $setting_name))->fetchOne($default_value);
}

function set_gedcom_setting($ged_id, $setting_name, $setting_value) {
	if (get_gedcom_setting($ged_id, $setting_name)!=$setting_value) {
		AddToLog('Gedcom setting "'.$setting_name.'" set to "'.$setting_value.'"', 'config');
	}
	if (is_null($setting_value)) {
		WT_DB::prepare("DELETE FROM `##gedcom_setting` WHERE gedcom_id=? AND setting_name=?")
			->execute(array($ged_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO `##gedcom_setting` (gedcom_id, setting_name, setting_value) VALUES (?, ?, LEFT(?, 255))")
			->execute(array($ged_id, $setting_name, $setting_value));
	}
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_USER table
////////////////////////////////////////////////////////////////////////////////

function create_user($username, $realname, $email, $password) {
	try {
		WT_DB::prepare("INSERT INTO `##user` (user_name, real_name, email, password) VALUES (?, ?, ?, ?)")
			->execute(array($username, $realname, $email, crypt($password)));
		// Set the initial block layot
		$user_id=WT_DB::getInstance()->lastInsertId();
		WT_DB::prepare(
			"INSERT INTO `##block` (user_id, location, block_order, module_name)".
			" SELECT LAST_INSERT_ID(), location, block_order, module_name".
			" FROM `##block`".
			" WHERE user_id=-1"
		)->execute(array($user_id));
	} catch (PDOException $ex) {
		// User already exists?
	}
	$user_id=
		WT_DB::prepare("SELECT SQL_CACHE user_id FROM `##user` WHERE user_name=?")
		->execute(array($username))->fetchOne();
	return $user_id;
}

function rename_user($user_id, $new_username) {
	WT_DB::prepare("UPDATE `##user` SET user_name=?   WHERE user_id  =?")->execute(array($new_username, $user_id));
}

function delete_user($user_id) {
	// Don't delete the logs.
	WT_DB::prepare("UPDATE `##log` SET user_id=NULL   WHERE user_id =?")->execute(array($user_id));
	// Take over the user's pending changes.
	// TODO: perhaps we should prevent deletion of users with pending changes?
	WT_DB::prepare("DELETE FROM `##change` WHERE user_id=? AND status='accepted'")->execute(array($user_id));
	WT_DB::prepare("UPDATE `##change` SET user_id=? WHERE user_id=?")->execute(array(WT_USER_ID, $user_id));

	WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE user_id=?")->execute(array($user_id));
	WT_DB::prepare("DELETE FROM `##block`               WHERE user_id=?"    )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM `##user_gedcom_setting` WHERE user_id=?"    )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM `##user_setting`        WHERE user_id=?"    )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM `##message`             WHERE user_id=?"    )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM `##user`                WHERE user_id=?"    )->execute(array($user_id));
}

function get_all_users($order='ASC', $key='realname') {
	if ($key=='username') {
		return
			WT_DB::prepare("SELECT SQL_CACHE user_id, user_name FROM `##user` WHERE user_id>0 ORDER BY user_name")
			->fetchAssoc();
	} elseif ($key=='realname') {
		return
			WT_DB::prepare("SELECT SQL_CACHE user_id, user_name FROM `##user` WHERE user_id>0 ORDER BY real_name")
			->fetchAssoc();
	} else {
		return
			WT_DB::prepare(
				"SELECT SQL_CACHE u.user_id, user_name".
				" FROM `##user` u".
				" LEFT JOIN `##user_setting` us1 ON (u.user_id=us1.user_id AND us1.setting_name=?)".
				" WHERE u.user_id>0".
				" ORDER BY us1.setting_value {$order}"
			)->execute(array($key))
			->fetchAssoc();
	}
}

function get_user_count() {
	return
			WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##user` WHERE user_id>0")
			->fetchOne();
}

function get_user_by_email($email) {
	return
		WT_DB::prepare("SELECT SQL_CACHE user_id FROM `##user` WHERE email=?")
		->execute(array($email))
		->fetchOne();
}

function get_admin_user_count() {
	return
		WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##user_setting` WHERE setting_name=? AND setting_value=? AND user_id>0")
		->execute(array('canadmin', '1'))
		->fetchOne();
}

function get_non_admin_user_count() {
	return
		WT_DB::prepare("SELECT SQL_CACHE COUNT(*) FROM `##user_setting` WHERE  setting_name=? AND setting_value<>? AND user_id>0")
		->execute(array('canadmin', '1'))
		->fetchOne();
}

// Get a list of logged-in users
function get_logged_in_users() {
	// If the user is logged in on multiple times, this query would fetch
	// multiple rows.  fetchAssoc() will eliminate the duplicates
	return
		WT_DB::prepare(
			"SELECT SQL_NO_CACHE user_id, user_name".
			" FROM `##user` u".
			" JOIN `##session` USING (user_id)"
		)
		->fetchAssoc();
}

// Get the ID for a username
function get_user_id($username) {
	return WT_DB::prepare("SELECT SQL_CACHE user_id FROM `##user` WHERE user_name=?")
		->execute(array($username))
		->fetchOne();
}

// Get the username for a user ID
function get_user_name($user_id) {
	return WT_DB::prepare("SELECT SQL_CACHE user_name FROM `##user` WHERE user_id=?")
		->execute(array($user_id))
		->fetchOne();
}

function get_newest_registered_user() {
	return WT_DB::prepare(
		"SELECT SQL_CACHE u.user_id".
		" FROM `##user` u".
		" LEFT JOIN `##user_setting` us ON (u.user_id=us.user_id AND us.setting_name=?) ".
		" ORDER BY us.setting_value DESC LIMIT 1"
	)->execute(array('reg_timestamp'))
		->fetchOne();
}

function set_user_password($user_id, $password) {
	if (version_compare(PHP_VERSION, '5.3')>0) {
		// Some PHP5.2 implementations of crypt() appear to be broken - #802316
		// PHP5.3 will always support BLOWFISH - see php.net/crypt
		// This salt will select the BLOWFISH algorithm with 2^12 rounds
		$salt='$2a$12$';
		$salt_chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
		for ($i=0;$i<22;++$i) {
			$salt.=substr($salt_chars, mt_rand(0,63), 1);
		}
		$password_hash=crypt($password, $salt);
	} else {
		// Our prefered hash algorithm is not available.  Use the default.
		$password_hash=crypt($password);
	}
	WT_DB::prepare("UPDATE `##user` SET password=? WHERE user_id=?")
		->execute(array($password_hash, $user_id));
	AddToLog('User ID: '.$user_id. ' ('.get_user_name($user_id).') changed password', 'auth');
}

function check_user_password($user_id, $password) {
	// crypt() needs the password-hash to use as a salt
	$password_hash=
		WT_DB::prepare("SELECT SQL_CACHE password FROM `##user` WHERE user_id=?")
		->execute(array($user_id))
		->fetchOne();
	if (crypt($password, $password_hash)==$password_hash) {
		// Update older passwords to use BLOWFISH with 2^12 rounds
		if (version_compare(PHP_VERSION, '5.3')>0 && substr($password_hash, 0, 7)!='$2a$12$') {
			set_user_password($user_id, $password);
		}
		return true;
	} else {
		return false;
	}
}
////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_USER_SETTING table
////////////////////////////////////////////////////////////////////////////////

function get_user_setting($user_id, $setting_name, $default_value=null) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE setting_value FROM `##user_setting` WHERE user_id=? AND setting_name=?"
		)->execute(array($user_id, $setting_name))->fetchOne($default_value);
}

function set_user_setting($user_id, $setting_name, $setting_value) {
	if (is_null($setting_value)) {
		WT_DB::prepare("DELETE FROM `##user_setting` WHERE user_id=? AND setting_name=?")
			->execute(array($user_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO `##user_setting` (user_id, setting_name, setting_value) VALUES (?, ?, LEFT(?, 255))")
			->execute(array($user_id, $setting_name, $setting_value));
	}
}

function admin_user_exists() {
	return get_admin_user_count()>0;
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_USER_GEDCOM_SETTING table
////////////////////////////////////////////////////////////////////////////////

function get_user_gedcom_setting($user_id, $gedcom_id, $setting_name, $default_value=null) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE setting_value FROM `##user_gedcom_setting` WHERE user_id=? AND gedcom_id=? AND setting_name=?"
		)->execute(array($user_id, $gedcom_id, $setting_name))->fetchOne($default_value);
}

function set_user_gedcom_setting($user_id, $ged_id, $setting_name, $setting_value) {
	if (is_null($setting_value)) {
		WT_DB::prepare("DELETE FROM `##user_gedcom_setting` WHERE user_id=? AND gedcom_id=? AND setting_name=?")
			->execute(array($user_id, $ged_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO `##user_gedcom_setting` (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, LEFT(?, 255))")
			->execute(array($user_id, $ged_id, $setting_name, $setting_value));
	}
}

function get_user_from_gedcom_xref($ged_id, $xref) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE user_id FROM `##user_gedcom_setting`".
			" WHERE gedcom_id=? AND setting_name=? AND setting_value=?"
		)->execute(array($ged_id, 'gedcomid', $xref))->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_BLOCK table
////////////////////////////////////////////////////////////////////////////////

function get_user_blocks($user_id) {
	$blocks=array('main'=>array(), 'side'=>array());
	$rows=WT_DB::prepare(
		"SELECT SQL_CACHE location, block_id, module_name".
		" FROM  `##block`".
		" JOIN  `##module` USING (module_name)".
		" WHERE user_id=?".
		" AND   status='enabled'".
		" ORDER BY location, block_order"
	)->execute(array($user_id))->fetchAll();
	foreach ($rows as $row) {
		$blocks[$row->location][$row->block_id]=$row->module_name;
	}
	return $blocks;
}

function get_gedcom_blocks($gedcom_id) {
	$blocks=array('main'=>array(), 'side'=>array());
	$rows=WT_DB::prepare(
		"SELECT SQL_CACHE location, block_id, module_name".
		" FROM  `##block`".
		" JOIN  `##module` USING (module_name)".
		" WHERE gedcom_id=?".
		" AND status='enabled'".
		" ORDER BY location, block_order"
	)->execute(array($gedcom_id))->fetchAll();
	foreach ($rows as $row) {
		$blocks[$row->location][$row->block_id]=$row->module_name;
	}
	return $blocks;
}

function get_block_setting($block_id, $setting_name, $default_value=null) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE setting_value FROM `##block_setting` WHERE block_id=? AND setting_name=?"
		)->execute(array($block_id, $setting_name))->fetchOne($default_value);

}

function set_block_setting($block_id, $setting_name, $setting_value) {
	if (is_null($setting_value)) {
		WT_DB::prepare("DELETE FROM `##block_setting` WHERE block_id=? AND setting_name=?")
			->execute(array($block_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO `##block_setting` (block_id, setting_name, setting_value) VALUES (?, ?, ?)")
			->execute(array($block_id, $setting_name, $setting_value));
	}
}

function get_module_setting($module_name, $setting_name, $default_value=null) {
	return
		WT_DB::prepare(
			"SELECT SQL_CACHE setting_value FROM `##module_setting` WHERE module_name=? AND setting_name=?"
		)->execute(array($module_name, $setting_name))->fetchOne($default_value);
}

function set_module_setting($module_name, $setting_name, $setting_value) {
	if (is_null($setting_value)) {
		WT_DB::prepare("DELETE FROM `##module_setting` WHERE module_name=? AND setting_name=?")
			->execute(array($module_name, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO `##module_setting` (module_name, setting_name, setting_value) VALUES (?, ?, ?)")
			->execute(array($module_name, $setting_name, $setting_value));
	}
}

// update favorites after merging records
function update_favorites($xref_from, $xref_to, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare("UPDATE `##favorite` SET xref=? WHERE xref=? AND gedcom_id=?")
		->execute(array($xref_to, $xref_from, $ged_id))
		->rowCount();
}
