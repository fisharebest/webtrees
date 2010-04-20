<?php
/**
* Functions to query the database.
*
* This file implements the datastore functions necessary for webtrees
* to use an SQL database as its datastore.
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
*
* Modifications Copyright (c) 2010 Greg Roach
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @version $Id$
* @package webtrees
* @subpackage DB
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_DB_PHP', '');

//-- gets the first record in the gedcom
function get_first_xref($type, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	switch ($type) {
	case "INDI":
		return
			WT_DB::prepare("SELECT MIN(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=?")
			->execute(array($ged_id))
			->fetchOne();
		break;
	case "FAM":
		return
			WT_DB::prepare("SELECT MIN(f_id) FROM {$TBLPREFIX}families WHERE f_file=?")
			->execute(array($ged_id))
			->fetchOne();
	case "SOUR":
		return
			WT_DB::prepare("SELECT MIN(s_id) FROM {$TBLPREFIX}sources WHERE s_file=?")
			->execute(array($ged_id))
			->fetchOne();
	case "OBJE":
		return
			WT_DB::prepare("SELECT MIN(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=?")
			->execute(array($ged_id))
			->fetchOne();
	default:
		return
			WT_DB::prepare("SELECT MIN(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=?")
			->execute(array($ged_id, $type))
			->fetchOne();
	}
}

//-- gets the last record in the gedcom
function get_last_xref($type, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	switch ($type) {
	case "INDI":
		return
			WT_DB::prepare("SELECT MAX(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=?")
			->execute(array($ged_id))
			->fetchOne();
		break;
	case "FAM":
		return
			WT_DB::prepare("SELECT MAX(f_id) FROM {$TBLPREFIX}families WHERE f_file=?")
			->execute(array($ged_id))
			->fetchOne();
	case "SOUR":
		return
			WT_DB::prepare("SELECT MAX(s_id) FROM {$TBLPREFIX}sources WHERE s_file=?")
			->execute(array($ged_id))
			->fetchOne();
	case "OBJE":
		return
			WT_DB::prepare("SELECT MAX(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=?")
			->execute(array($ged_id))
			->fetchOne();
	default:
		return
			WT_DB::prepare("SELECT MAX(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=?")
			->execute(array($ged_id, $type))
			->fetchOne();
	}
}

//-- gets the next person in the gedcom
function get_next_xref($pid, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$type=gedcom_record_type($pid, $ged_id);
	switch ($type) {
	case "INDI":
		return
			WT_DB::prepare("SELECT MIN(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_id>?")
			->execute(array($ged_id, $pid))
			->fetchOne();
		break;
	case "FAM":
		return
			WT_DB::prepare("SELECT MIN(f_id) FROM {$TBLPREFIX}families WHERE f_file=? AND f_id>?")
			->execute(array($ged_id, $pid))
			->fetchOne();
	case "SOUR":
		return
			WT_DB::prepare("SELECT MIN(s_id) FROM {$TBLPREFIX}sources WHERE s_file=? AND s_id>?")
			->execute(array($ged_id, $pid))
			->fetchOne();
	case "OBJE":
		return
			WT_DB::prepare("SELECT MIN(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=? AND m_media>?")
			->execute(array($ged_id, $pid))
			->fetchOne();
	default:
		return
			WT_DB::prepare("SELECT MIN(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=? AND o_id>?")
			->execute(array($ged_id, $type, $pid))
			->fetchOne();
	}
}

//-- gets the previous person in the gedcom
function get_prev_xref($pid, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$type=gedcom_record_type($pid, $ged_id);
	switch ($type) {
	case "INDI":
		return
			WT_DB::prepare("SELECT MAX(i_id) FROM {$TBLPREFIX}individuals WHERE i_file=? AND i_id<?")
			->execute(array($ged_id, $pid))
			->fetchOne();
		break;
	case "FAM":
		return
			WT_DB::prepare("SELECT MAX(f_id) FROM {$TBLPREFIX}families WHERE f_file=? AND f_id<?")
			->execute(array($ged_id, $pid))
			->fetchOne();
	case "SOUR":
		return
			WT_DB::prepare("SELECT MAX(s_id) FROM {$TBLPREFIX}sources WHERE s_file=? AND s_id<?")
			->execute(array($ged_id, $pid))
			->fetchOne();
	case "OBJE":
		return
			WT_DB::prepare("SELECT MAX(m_media) FROM {$TBLPREFIX}media WHERE m_gedfile=? AND m_media<?")
			->execute(array($ged_id, $pid))
			->fetchOne();
	default:
		return
			WT_DB::prepare("SELECT MAX(o_id) FROM {$TBLPREFIX}other WHERE o_file=? AND o_type=? AND o_id<?")
			->execute(array($ged_id, $type, $pid))
			->fetchOne();
	}
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of initial surname letters for indilist.php and famlist.php
// $marnm - if set, include married names
// $fams - if set, only consider individuals with FAMS records
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_indilist_salpha($marnm, $fams, $ged_id) {
	global $TBLPREFIX;

	$alphas=array();
	// This logic relies on the database's collation rules to ensure that accented letters
	// and digraphs appear in the correct listing.
	foreach (explode(' ', i18n::$alphabet) as $letter) {
		$query="SELECT COUNT(DISTINCT i_id) FROM {$TBLPREFIX}individuals";
		if ($marnm) {
			$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
		} else {
			$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file AND n_type!='_MARNM')";
		}
		if ($fams) {
			$query.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
		}
		$query.=" WHERE n_file=? AND n_sort LIKE '{$letter}%' COLLATE '".i18n::$collation."'";
		foreach (explode(' ', i18n::$alphabet) as $letter2) {
			if ($letter!=$letter2 && strpos($letter, $letter2)!==0) {
				$query.=" AND n_sort NOT LIKE '{$letter2}%' COLLATE '".i18n::$collation."'";
			}
		}
		$alphas[$letter]=WT_DB::prepare($query)->execute(array(WT_GED_ID))->fetchOne();
	}
	// Now repeat for all letters not in our alphabet.
	// This includes "@" (unknown) and "," (none)
	$query=
		"SELECT LEFT(n_sort, 1), COUNT(DISTINCT i_id)".
		" FROM {$TBLPREFIX}individuals";
	if ($marnm) {
		$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
	} else {
		$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file AND n_type!='_MARNM')";
	}
	if ($fams) {
		$query.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
	}
	$query.=" WHERE n_file=?";
	foreach (explode(' ', i18n::$alphabet) as $letter) {
		$query.=" AND n_surn NOT LIKE '{$letter}%' COLLATE '".i18n::$collation."'";
	}
	$query.=" GROUP BY LEFT(n_surn, 1)";
	foreach (WT_DB::prepare($query)->execute(array(WT_GED_ID))->fetchAssoc() as $letter=>$count) {
		$alphas[$letter]=$count;
	}
	// Force "," and "@" first to the end of the list
	if (array_key_exists(',', $alphas)) {
		$count=$alphas[','];
		unset($alphas[',']);
		$alphas[',']=$count;
	}
	if (array_key_exists('@', $alphas)) {
		$count=$alphas['@'];
		unset($alphas['@']);
		$alphas['@']=$count;
	}
	return $alphas;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of initial given name letters for indilist.php and famlist.php
// $surn - if set, only consider people with this surname
// $salpha - if set, only consider surnames starting with this letter
// $marnm - if set, include married names
// $fams - if set, only consider individuals with FAMS records
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_indilist_galpha($surn, $salpha, $marnm, $fams, $ged_id) {
	global $TBLPREFIX;

	$alphas=array();
	// This logic relies on the database's collation rules to ensure that accented letters
	// and digraphs appear in the correct listing.
	foreach (explode(' ', i18n::$alphabet) as $letter) {
		$query="SELECT COUNT(DISTINCT i_id) FROM {$TBLPREFIX}individuals";
		if ($marnm) {
			$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
		} else {
			$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file AND n_type!='_MARNM')";
		}
		if ($fams) {
			$query.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
		}
		$query.=" WHERE n_file=?";
		if ($surn) {
			$query.=" AND n_sort LIKE ".WT_DB::quote("{$surn},%")." COLLATE '".i18n::$collation."'";
		} elseif ($salpha) {
			$query.=" AND n_sort LIKE ".WT_DB::quote("{$salpha}%,%")." COLLATE '".i18n::$collation."'";
		}
		$query.=" AND n_givn LIKE '".$letter."%' COLLATE '".i18n::$collation."'";
		foreach (explode(' ', i18n::$alphabet) as $letter2) {
			if ($letter!=$letter2 && strpos($letter, $letter2)!==0) {
				$query.=" AND n_givn NOT LIKE '{$letter2}%' COLLATE '".i18n::$collation."'";
			}
		}
		$alphas[$letter]=WT_DB::prepare($query)->execute(array(WT_GED_ID))->fetchOne();
	}
	// Now repeat for all letters not in our alphabet.
	// This includes "@" (unknown) and "," (none)
	$query=
		"SELECT LEFT(n_givn, 1), COUNT(DISTINCT i_id)".
		" FROM {$TBLPREFIX}individuals";
	if ($marnm) {
		$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
	} else {
		$query.=" JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file AND n_type!='_MARNM')";
	}
	if ($fams) {
		$query.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
	}
	$query.=" WHERE n_file=?";
	if ($surn) {
		$query.=" AND n_sort LIKE ".WT_DB::quote("{$surn},%")." COLLATE '".i18n::$collation."'";
	} elseif ($salpha) {
		$query.=" AND n_sort LIKE ".WT_DB::quote("{$salpha}%,%")." COLLATE '".i18n::$collation."'";
	}
	$query.=" AND n_givn LIKE '".$letter."%' COLLATE '".i18n::$collation."'";
	foreach (explode(' ', i18n::$alphabet) as $letter) {
		$query.=" AND n_givn NOT LIKE '{$letter}%' COLLATE '".i18n::$collation."'";
	}
	$query.=" GROUP BY LEFT(n_givn, 1)";
	foreach (WT_DB::prepare($query)->execute(array(WT_GED_ID))->fetchAssoc() as $letter=>$count) {
		$alphas[$letter]=$count;
	}
	// Force "," and "@" first to the end of the list
	if (array_key_exists(',', $alphas)) {
		$count=$alphas[','];
		unset($alphas[',']);
		$alphas[',']=$count;
	}
	if (array_key_exists('@', $alphas)) {
		$count=$alphas['@'];
		unset($alphas['@']);
		$alphas['@']=$count;
	}
	return $alphas;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of surnames for indilist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only consider surnames starting with this letter
// $marnm - if set, include married names
// $fams - if set, only consider individuals with FAMS records
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_indilist_surns($surn, $salpha, $marnm, $fams, $ged_id) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT n_surn, n_surname, n_id FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
	if ($fams) {
		$sql.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
	}
	$where=array("n_file={$ged_id}");
	if (!$marnm) {
		$where[]="n_type!='_MARNM'";
	}
	if ($surn) {
		// Match a surname
		$where[]="n_surn LIKE ".WT_DB::quote("{$surn}")." COLLATE '".i18n::$collation."'";
	} elseif ($salpha==',') {
		// Match a surname-less name
		$where[]="n_surn = ''";
	} elseif ($salpha) {
		// Match a surname initial
		$where[]="n_surn LIKE ".WT_DB::quote("{$salpha}%")." COLLATE '".i18n::$collation."'";
	} else {
		// Match all individuals
		$where[]="n_surn <>'@N.N.'";
		$where[]="n_surn <> ''";
	}

	$sql.=" WHERE ".implode(' AND ', $where)." ORDER BY n_surn";

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll();
	foreach ($rows as $row) {
		$list[$row->n_surn][$row->n_surname][$row->n_id]=true;
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of surnames for indilist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only consider surnames starting with this letter
// $marnm - if set, include married names
// $ged_id - only consider individuals from this gedcom
////////////////////////////////////////////////////////////////////////////////
function get_famlist_surns($surn, $salpha, $marnm, $ged_id) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT n_surn, n_surname, l_to FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file) JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file AND l_type='FAMS')";
	$where=array("n_file={$ged_id}");
	if (!$marnm) {
		$where[]="n_type!='_MARNM'";
	}

	if ($surn) {
		// Match a surname
		$where[]="n_surn LIKE ".WT_DB::quote("{$surn}")." COLLATE '".i18n::$collation."'";
	} elseif ($salpha==',') {
		// Match a surname-less name
		$where[]="n_surn = ''";
	} elseif ($salpha) {
		// Match a surname initial
		$where[]="n_surn LIKE ".WT_DB::quote("{$salpha}%")." COLLATE '".i18n::$collation."'";
	} else {
		// Match all individuals
		$where[]="n_surn <> '@N.N.'";
		$where[]="n_surn <> ''";
	}

	$sql.=" WHERE ".implode(' AND ', $where)." ORDER BY n_surn";

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll();
	foreach ($rows as $row) {
		$list[$row->n_surn][$row->n_surname][$row->l_to]=true;
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of individuals for indilist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only fetch surnames starting with this letter
// $galpha - if set, only fetch given names starting with this letter
// $marnm - if set, include married names
// $fams - if set, only fetch individuals with FAMS records
// $ged_id - if set, only fetch individuals from this gedcom
//
// All parameters must be in upper case.  We search against a database column
// that contains uppercase values. This will allow non utf8-aware database
// to match diacritics.
//
// To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
// To search for names with no surnames, use $salpha=","
////////////////////////////////////////////////////////////////////////////////
function get_indilist_indis($surn='', $salpha='', $galpha='', $marnm=false, $fams=false, $ged_id=null) {
	global $TBLPREFIX;

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex, n_surn, n_surname, n_num FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON (i_id=n_id AND i_file=n_file)";
	if ($fams) {
		$sql.=" JOIN {$TBLPREFIX}link ON (i_id=l_from AND i_file=l_file)";
	}
	$where=array();
	if ($ged_id) {
		$where[]="n_file={$ged_id}";
	}
	if (!$marnm) {
		$where[]="n_type!='_MARNM'";
	}

	if ($surn) {
		// Match a surname, with or without a given initial
		if ($galpha) {
			$where[]="n_sort LIKE ".WT_DB::quote("{$surn},{$galpha}%");
		} else {
			$where[]="n_sort LIKE ".WT_DB::quote("{$surn},%");
		}
	} elseif ($salpha==',') {
		// Match a surname-less name, with or without a given initial
		if ($galpha) {
			$where[]="n_sort LIKE ".WT_DB::quote(",{$galpha}%");
		} else {
			$where[]="n_sort LIKE ".WT_DB::quote(",%");
		}
	} elseif ($salpha) {
		// Match a surname initial, with or without a given initial
		if ($galpha) {
			$where[]="n_sort LIKE ".WT_DB::quote("{$salpha}%,{$galpha}%");
		} else {
			$where[]="n_sort LIKE ".WT_DB::quote("{$salpha}%");
		}
	} elseif ($galpha) {
		// Match all surnames with a given initial
		$where[]="n_sort LIKE ".WT_DB::quote("%,{$galpha}%");
	} else {
		// Match all individuals
	}

	$sql.=" WHERE ".implode(' AND ', $where)." ORDER BY CASE n_surn WHEN '@N.N.' THEN 1 ELSE 0 END, n_surn, CASE n_givn WHEN '@P.N.' THEN 1 ELSE 0 END, n_givn";

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $row) {
		$person=Person::getInstance($row);
		$person->setPrimaryName($row['n_num']);
		// We need to clone $person, as we may have multiple references to the
		// same person in this list, and the "primary name" would otherwise
		// be shared amongst all of them.  This has some performance/memory
		// implications, and there is probably a better way.  This, however,
		// is clean, easy and works.
		$list[]=clone $person;
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of families for famlist.php
// $surn - if set, only fetch people with this surname
// $salpha - if set, only fetch surnames starting with this letter
// $galpha - if set, only fetch given names starting with this letter
// $marnm - if set, include married names
// $ged_id - if set, only fetch individuals from this gedcom
//
// All parameters must be in upper case.  We search against a database column
// that contains uppercase values. This will allow non utf8-aware database
// to match diacritics.
//
// To search for unknown names, use $surn="@N.N.", $salpha="@" or $galpha="@"
// To search for names with no surnames, use $salpha=","
////////////////////////////////////////////////////////////////////////////////
function get_famlist_fams($surn='', $salpha='', $galpha='', $marnm, $ged_id=null) {
	global $TBLPREFIX;

	$list=array();
	foreach (get_indilist_indis($surn, $salpha, $galpha, $marnm, true, $ged_id) as $indi) {
		foreach ($indi->getSpouseFamilies() as $family) {
			$list[$family->getXref()]=$family;
		}
	}
	// If we're searching for "Unknown surname", we also need to include families
	// with missing spouses
	if ($surn=='@N.N.' || $salpha=='@') {
		$rows=
			WT_DB::prepare("SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families f WHERE f_file={$ged_id} AND (f_husb='' OR f_wife='')")
			->execute(array($ged_id))
			->fetchAll(PDO::FETCH_ASSOC);

		foreach ($rows as $row) {
			$list[]=Family::getInstance($row);
		}
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Fetch a list of children for an individual, from all their partners.
////////////////////////////////////////////////////////////////////////////////
function fetch_child_ids($parent_id, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare("SELECT DISTINCT child.l_from AS xref FROM {$TBLPREFIX}link child, {$TBLPREFIX}link spouse WHERE child.l_type=? AND spouse.l_type=? AND child.l_file=spouse.l_file AND child.l_to=spouse.l_to AND spouse.l_from=? AND child.l_file=?");
	}

	return $statement->execute(array('FAMC', 'FAMS', $parent_id, $ged_id))->fetchOneColumn();
}

////////////////////////////////////////////////////////////////////////////////
// Count the number of records of each type in the database.  Return an array
// of 'type'=>count for each type where records exist.
////////////////////////////////////////////////////////////////////////////////
function count_all_records($ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare(
			"SELECT 'INDI' AS type, COUNT(*) AS num FROM {$TBLPREFIX}individuals WHERE i_file=?".
			" UNION ALL ".
			"SELECT 'FAM'  AS type, COUNT(*) AS num FROM {$TBLPREFIX}families    WHERE f_file=?".
			" UNION ALL ".
			"SELECT 'SOUR' AS type, COUNT(*) AS num FROM {$TBLPREFIX}sources     WHERE s_file=?".
			" UNION ALL ".
			"SELECT 'OBJE' AS type, COUNT(*) AS num FROM {$TBLPREFIX}media       WHERE m_gedfile=?".
			" UNION ALL ".
			"SELECT o_type AS type, COUNT(*) as num FROM {$TBLPREFIX}other       WHERE o_file=? GROUP BY type"
		)
		->execute(array($ged_id, $ged_id, $ged_id, $ged_id, $ged_id))
		->fetchAssoc();
}

////////////////////////////////////////////////////////////////////////////////
// Count the number of records linked to a given record
////////////////////////////////////////////////////////////////////////////////
function count_linked_indi($xref, $link, $ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}individuals WHERE i_file=l_file AND i_id=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_fam($xref, $link, $ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}families WHERE f_file=l_file AND f_id=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_note($xref, $link, $ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}other WHERE o_file=l_file AND o_id=l_from AND o_type=? AND l_file=? AND l_type=? AND l_to=?")
		->execute(array('NOTE', $ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_sour($xref, $link, $ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}sources WHERE s_file=l_file AND s_id=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}
function count_linked_obje($xref, $link, $ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}link, {$TBLPREFIX}media WHERE m_gedfile=l_file AND m_media=l_from AND l_file=? AND l_type=? AND l_to=?")
		->execute(array($ged_id, $link, $xref))
		->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Fetch records linked to a given record
////////////////////////////////////////////////////////////////////////////////
function fetch_linked_indi($xref, $link, $ged_id) {
	global $TBLPREFIX;

	$rows=WT_DB::prepare(
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
		" FROM {$TBLPREFIX}individuals".
		" JOIN {$TBLPREFIX}link ON (i_file=l_file AND i_id=l_from)".
		" LEFT JOIN {$TBLPREFIX}name ON (i_file=n_file AND i_id=n_id AND n_num=0)".
		" WHERE i_file=? AND l_type=? AND l_to=?".
		" ORDER BY n_sort"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Person::getInstance($row);
	}
	return $list;
}
function fetch_linked_fam($xref, $link, $ged_id) {
	global $TBLPREFIX;

	$rows=WT_DB::prepare(
		"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil".
		" FROM {$TBLPREFIX}families".
		" JOIN {$TBLPREFIX}link ON (f_file=l_file AND f_id=l_from)".
		" LEFT JOIN {$TBLPREFIX}name ON (f_file=n_file AND f_id=n_id AND n_num=0)".
		" WHERE f_file=? AND l_type=? AND l_to=?".
		" ORDER BY n_sort"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Family::getInstance($row);
	}
	return $list;
}
function fetch_linked_note($xref, $link, $ged_id) {
	global $TBLPREFIX;

	$rows=WT_DB::prepare(
		"SELECT 'NOTE' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
		" FROM {$TBLPREFIX}other".
		" JOIN {$TBLPREFIX}link ON (o_file=l_file AND o_id=l_from)".
		" LEFT JOIN {$TBLPREFIX}name ON (o_file=n_file AND o_id=n_id AND n_num=0)".
		" WHERE o_file=? AND o_type='NOTE' AND l_type=? AND l_to=?".
		" ORDER BY n_sort"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Note::getInstance($row);
	}
	return $list;
}
function fetch_linked_sour($xref, $link, $ged_id) {
	global $TBLPREFIX;

	$rows=WT_DB::prepare(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec".
			" FROM {$TBLPREFIX}sources".
			" JOIN {$TBLPREFIX}link ON (s_file=l_file AND s_id=l_from)".
			" LEFT JOIN {$TBLPREFIX}name ON (s_file=n_file AND s_id=n_id AND n_num=0)".
			" WHERE s_file=? AND l_type=? AND l_to=?".
			" ORDER BY n_sort"
		)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Source::getInstance($row);
	}
	return $list;
}
function fetch_linked_obje($xref, $link, $ged_id) {
	global $TBLPREFIX;

	$rows=WT_DB::prepare(
		"SELECT 'OBJE' AS type, m_media AS xref, m_gedfile AS ged_id, m_gedrec AS gedrec, m_titl, m_file".
		" FROM {$TBLPREFIX}media".
		" JOIN {$TBLPREFIX}link ON (m_gedfile=l_file AND m_media=l_from)".
		" LEFT JOIN {$TBLPREFIX}name ON (m_gedfile=n_file AND m_media=n_id AND n_num=0)".
		" WHERE m_gedfile=? AND l_type=? AND l_to=? AND n_num=?".
		" ORDER BY n_sort"
	)->execute(array($ged_id, $link, $xref))->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Media::getInstance($row);
	}
	return $list;
}

////////////////////////////////////////////////////////////////////////////////
// Fetch all records linked to a record - when deleting an object, we must
// also delete all links to it.
////////////////////////////////////////////////////////////////////////////////
function fetch_all_links($xref, $ged_id) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT l_from FROM {$TBLPREFIX}link WHERE l_file=? AND l_to=?")
		->execute(array($ged_id, $xref))
		->fetchOneColumn();
}

////////////////////////////////////////////////////////////////////////////////
// Fetch a row from the database, corresponding to a gedcom record.
// These functions are used to create gedcom objects.
// To simplify common processing, the xref, gedcom id and gedcom record are
// renamed consistently.  The other columns are fetched as they are.
////////////////////////////////////////////////////////////////////////////////
function fetch_person_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex ".
			"FROM {$TBLPREFIX}individuals WHERE i_id=? AND i_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
}
function fetch_family_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil ".
			"FROM {$TBLPREFIX}families WHERE f_id=? AND f_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
}
function fetch_source_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec ".
			"FROM {$TBLPREFIX}sources WHERE s_id=? AND s_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
}
function fetch_note_record($xref, $ged_id) {
	// Notes are (currently) stored in the other table
	return fetch_other_record($xref, $ged_id);
}
function fetch_media_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT 'OBJE' AS type, m_media AS xref, m_gedfile AS ged_id, m_gedrec AS gedrec, m_titl, m_file ".
			"FROM {$TBLPREFIX}media WHERE m_media=? AND m_gedfile=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
}
function fetch_other_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
			"FROM {$TBLPREFIX}other WHERE o_id=? AND o_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
}
function fetch_gedcom_record($xref, $ged_id) {
	// We don't know the type of the record, so use the prefix to suggest the likely type.
	global $GEDCOM_ID_PREFIX, $FAM_ID_PREFIX, $SOURCE_ID_PREFIX, $MEDIA_ID_PREFIX;

	if       (strpos($xref, $GEDCOM_ID_PREFIX)===0) {
		$row=fetch_person_record($xref, $ged_id);
	} elseif (strpos($xref, $FAM_ID_PREFIX   )===0) {
		$row=fetch_family_record($xref, $ged_id);
	} elseif (strpos($xref, $SOURCE_ID_PREFIX)===0) {
		$row=fetch_source_record($xref, $ged_id);
	} elseif (strpos($xref, $MEDIA_ID_PREFIX )===0) {
		$row=fetch_media_record ($xref, $ged_id);
	} else {
		$row=fetch_other_record ($xref, $ged_id);
	}

	if ($row) {
		// If we found it, good
		return $row;
	} else {
		// Otherwise, try the other types
		if       (strpos($xref, $GEDCOM_ID_PREFIX)!==0 && $row=fetch_person_record($xref, $ged_id)) {
			return $row;
		} elseif (strpos($xref, $FAM_ID_PREFIX   )!==0 && $row=fetch_family_record($xref, $ged_id)) {
			return $row;
		} elseif (strpos($xref, $SOURCE_ID_PREFIX)!==0 && $row=fetch_source_record($xref, $ged_id)) {
			return $row;
		} elseif (strpos($xref, $MEDIA_ID_PREFIX )!==0 && $row=fetch_media_record ($xref, $ged_id)) {
			return $row;
		} else {
			return fetch_other_record($xref, $ged_id);
		}
	}
}

/**
* find the gedcom record for a family
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#family
* @param string $famid the unique gedcom xref id of the family record to retrieve
* @return string the raw gedcom record is returned
*/
function find_family_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT f_gedcom FROM {$TBLPREFIX}families WHERE f_id=? AND f_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

/**
* find the gedcom record for an individual
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#indi
* @param string $pid the unique gedcom xref id of the individual record to retrieve
* @return string the raw gedcom record is returned
*/
function find_person_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_id=? AND i_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

/**
* find the gedcom record for a source
*
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#source
* @param string $sid the unique gedcom xref id of the source record to retrieve
* @return string the raw gedcom record is returned
*/
function find_source_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT s_gedcom FROM {$TBLPREFIX}sources WHERE s_id=? AND s_file=?"
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
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT o_gedcom FROM {$TBLPREFIX}other WHERE o_id=? AND o_file=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

/**
* Find a media record by its ID
* @param string $rid the record id
*/
function find_media_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT m_gedrec FROM {$TBLPREFIX}media WHERE m_media=? AND m_gedfile=?"
		);
	}
	return $statement->execute(array($xref, $ged_id))->fetchOne();
}

// Find the gedcom data for a record. Optionally include pending changes.
function find_gedcom_record($xref, $ged_id, $pending=false) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_id   =? AND i_file   =? UNION ALL ".
			"SELECT f_gedcom FROM {$TBLPREFIX}families    WHERE f_id   =? AND f_file   =? UNION ALL ".
			"SELECT s_gedcom FROM {$TBLPREFIX}sources     WHERE s_id   =? AND s_file   =? UNION ALL ".
			"SELECT m_gedrec FROM {$TBLPREFIX}media       WHERE m_media=? AND m_gedfile=? UNION ALL ".
			"SELECT o_gedcom FROM {$TBLPREFIX}other       WHERE o_id   =? AND o_file   =?"
		);
	}

	if ($pending) {
		// This will return NULL if no record exists, or an empty string if the record has been deleted.
		$gedcom=find_updated_record($xref, $ged_id);
	} else {
		$gedcom=null;
	}
	
	if (is_null($gedcom)) {
		return
			$statement
			->execute(array($xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id, $xref, $ged_id))
			->fetchOne();
	} else {
		return $gedcom;
	}
}

/**
 * find and return an updated gedcom record
 * @param string $gid	the id of the record to find
 * @param string $gedfile	the gedcom file to get the record from.. defaults to currently active gedcom
 */
function find_updated_record($xref, $ged_id) {
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT new_gedcom FROM {$TBLPREFIX}change WHERE gedcom_id=? AND xref=? AND status='pending' ".
			"ORDER BY change_id DESC LIMIT 1"
		);
	}

	// This will return NULL if no record exists, or an empty string if the record has been deleted.
	return $gedcom=$statement->execute(array($ged_id, $xref))->fetchOne();
}

// Find the type of a gedcom record. Check the cache before querying the database.
// Returns 'INDI', 'FAM', etc., or null if the record does not exist.
function gedcom_record_type($xref, $ged_id) {
	global $TBLPREFIX, $gedcom_record_cache;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare(
			"SELECT 'INDI' FROM {$TBLPREFIX}individuals WHERE i_id   =? AND i_file   =? UNION ALL ".
			"SELECT 'FAM'  FROM {$TBLPREFIX}families    WHERE f_id   =? AND f_file   =? UNION ALL ".
			"SELECT 'SOUR' FROM {$TBLPREFIX}sources     WHERE s_id   =? AND s_file   =? UNION ALL ".
			"SELECT 'OBJE' FROM {$TBLPREFIX}media       WHERE m_media=? AND m_gedfile=? UNION ALL ".
			"SELECT o_type FROM {$TBLPREFIX}other       WHERE o_id   =? AND o_file   =?"
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
	global $TBLPREFIX;

	if (userCanAccept($user_id, $ged_id)) {
		return
			WT_DB::prepare(
				"SELECT 1".
				" FROM {$TBLPREFIX}change".
				" WHERE status='pending' AND gedcom_id=?"
			)->execute(array($ged_id))->fetchOne();
	} else {
		return false;
	}
}

/**
* update the is_dead status in the database
*
* this function will update the is_dead field in the individuals table with the correct value
* calculated by the is_dead() function.  To improve import performance, the is_dead status is first
* set to -1 during import.  The first time the is_dead status is retrieved this function is called to update
* the database.  This makes the first request for a person slower, but will speed up all future requests.
* @param string $xref id of individual to update
* @param string $ged_id gedcom to update
* @param bool $isdead true=dead
*/
function update_isdead($xref, $ged_id, $isdead) {
	global $TBLPREFIX;

	$isdead=$isdead ? 1 : 0; // DB uses int, not bool
	WT_DB::prepare("UPDATE {$TBLPREFIX}individuals SET i_isdead=? WHERE i_id=? AND i_file=?")->execute(array($isdead, $xref, $ged_id));
	return $isdead;
}

// Reset the i_isdead status for individuals
// This is necessary when we change the MAX_ALIVE_YEARS value
function reset_isdead($ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	WT_DB::prepare("UPDATE {$TBLPREFIX}individuals SET i_isdead=-1 WHERE i_file=?")->execute(array($ged_id));
}

/**
* get a list of all the sources
*
* returns an array of all of the sources in the database.
* @link http://phpgedview.sourceforge.net/devdocs/arrays.php#sources
* @return array the array of sources
*/
function get_source_list($ged_id) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}sources s WHERE s_file=?")
		->execute(array($ged_id))
		->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Source::getInstance($row);
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}

// Get a list of repositories from the database
// $ged_id - the gedcom to search
function get_repo_list($ged_id) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT 'REPO' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE o_type='REPO' AND o_file=?")
		->execute(array($ged_id))
		->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Repository::getInstance($row);
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}

//-- get the shared note list from the datastore
function get_note_list($ged_id) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT 'NOTE' AS type, o_id AS xref, {$ged_id} AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE o_type=? AND o_file=?")
		->execute(array('NOTE', $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);

	$list=array();
	foreach ($rows as $row) {
		$list[]=Note::getInstance($row);
	}
	usort($list, array('GedcomRecord', 'Compare'));
	return $list;
}


// Search for INDIs using custom SQL generated by the report engine
function search_indis_custom($join, $where, $order) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals ".implode(' ', $join).' WHERE '.implode(' AND ', $where);
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
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$list[]=Person::getInstance($row);
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search for FAMs using custom SQL generated by the report engine
function search_fams_custom($join, $where, $order) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families ".implode(' ', $join).' WHERE '.implode(' AND ', $where);
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
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$list[]=Family::getInstance($row);
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search the gedcom records of indis
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_indis($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION;

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
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="i_gedcom LIKE ".WT_DB::quote("%{$q}%");
		} else {
			$querysql[]="(i_gedcom LIKE ".WT_DB::quote("%{$q}%")." OR i_gedcom LIKE ".WT_DB::quote("%".utf8_strtoupper($q)."%")." OR i_gedcom LIKE ".WT_DB::quote("%".utf8_strtolower($q)."%").")";
		}
	}

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals WHERE (".implode(" {$match} ", $querysql).') AND i_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (WT_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Person::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($indi->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/\n\d\ '.WT_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}
		$list[]=$indi;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search the names of indis
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
function search_indis_names($query, $geds, $match) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	foreach ($query as $q) {
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="n_full LIKE ".WT_DB::quote("%{$q}%");
		} else {
			$querysql[]="(n_full LIKE ".WT_DB::quote("%{$q}%")." OR n_full LIKE ".WT_DB::quote("%".utf8_strtoupper($q)."%")." OR n_full LIKE ".WT_DB::quote("%".utf8_strtolower($q)."%").")";
		}
	}
	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex, n_num FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}name ON i_id=n_id AND i_file=n_file WHERE (".implode(" {$match} ", $querysql).') AND i_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Person::getInstance($row);
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
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search for individuals names/places using soundex
// $soundex - standard or dm
// $lastname, $firstname, $place - search terms
// $geds - array of gedcoms to search
function search_indis_soundex($soundex, $lastname, $firstname, $place, $geds) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals";
	if ($place) {
		$sql.=" JOIN {$TBLPREFIX}placelinks ON (pl_file=i_file AND pl_gid=i_id)";
		$sql.=" JOIN {$TBLPREFIX}places ON (p_file=pl_file AND pl_p_id=p_id)";
	}
	if ($firstname || $lastname) {
		$sql.=" JOIN {$TBLPREFIX}name ON (i_file=n_file AND i_id=n_id)";
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
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Person::getInstance($row);
		if ($indi->canDisplayName()) {
			$list[]=$indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

/**
* get recent changes since the given julian day inclusive
* @author yalnifj
* @param int $jd, leave empty to include all
*/
function get_recent_changes($jd=0, $allgeds=false) {
	global $TBLPREFIX;

	$sql="SELECT d_gid FROM {$TBLPREFIX}dates WHERE d_fact='CHAN' AND d_julianday1>=? AND d_gid NOT LIKE ?";
	$vars=array($jd, '%:%');
	if (!$allgeds) {
		$sql.=" AND d_file=?";
		$vars[]=WT_GED_ID;
	}
	$sql.=" ORDER BY d_julianday1 DESC";

	return WT_DB::prepare($sql)->execute($vars)->fetchOneColumn();
}

// Seach for individuals with events on a given day
function search_indis_dates($day, $month, $year, $facts) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}dates ON i_id=d_gid AND i_file=d_file WHERE i_file=?";
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
		$list[]=Person::getInstance($row);
	}
	return $list;
}

// Seach for individuals with events in a given date range
function search_indis_daterange($start, $end, $facts) {
	global $TBLPREFIX;

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex FROM {$TBLPREFIX}individuals JOIN {$TBLPREFIX}dates ON i_id=d_gid AND i_file=d_file WHERE i_file=? AND d_julianday1 BETWEEN ? AND ?";
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
		$list[]=Person::getInstance($row);
	}
	return $list;
}

// Search for people who had events in a given year range
function search_indis_year_range($startyear, $endyear) {
	// TODO: We should use Julian-days, rather than gregorian years,
	// to allow
	// the lifespan chart, etc., to use other calendars.
	$startjd=GregorianDate::YMDtoJD($startyear, 1, 1);
	$endjd  =GregorianDate::YMDtoJD($endyear+1, 1, 1)-1;

	return search_indis_daterange($startjd, $endjd, '');
}

// Search the gedcom records of families
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_fams($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION;

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

		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="f_gedcom LIKE ".WT_DB::quote("%{$q}%");
		} else {
			$querysql[]="(f_gedcom LIKE ".WT_DB::quote("%{$q}%")." OR f_gedcom LIKE ".WT_DB::quote("%".utf8_strtoupper($q)."%")." OR f_gedcom LIKE ".WT_DB::quote("%".utf8_strtolower($q)."%").")";
		}
	}

	$sql="SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families WHERE (".implode(" {$match} ", $querysql).') AND f_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (WT_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$family=Family::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($family->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/\n\d\ '.WT_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}

		$list[]=$family;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search the names of the husb/wife in a family
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
function search_fams_names($query, $geds, $match) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION;

	// No query => no results
	if (!$query) {
		return array();
	}

	// Convert the query into a SQL expression
	$querysql=array();
	foreach ($query as $q) {
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="(husb.n_full LIKE ".WT_DB::quote("%{$q}%")." OR wife.n_full LIKE ".WT_DB::quote("%{$q}%").")";
		} else {
			$querysql[]="(husb.n_full LIKE ".WT_DB::quote("%{$q}%")." OR wife.n_full LIKE '%{$q}%' OR husb.n_full LIKE ".WT_DB::quote(utf8_strtoupper("%{$q}%"))." OR husb.n_full LIKE ".WT_DB::quote(utf8_strtolower("%{$q}%"))." OR wife.n_full LIKE ".WT_DB::quote(utf8_strtoupper("%{$q}%"))." OR wife.n_full LIKE ".WT_DB::quote(utf8_strtolower("%{$q}%")).")";
		}
	}

	$sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil FROM {$TBLPREFIX}families LEFT OUTER JOIN {$TBLPREFIX}name husb ON f_husb=husb.n_id AND f_file=husb.n_file LEFT OUTER JOIN {$TBLPREFIX}name wife ON f_wife=wife.n_id AND f_file=wife.n_file WHERE (".implode(" {$match} ", $querysql).') AND f_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$indi=Family::getInstance($row);
		if ($indi->canDisplayName()) {
			$list[]=$indi;
		}
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search the gedcom records of sources
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_sources($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION;

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
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="s_gedcom LIKE ".WT_DB::quote("%{$q}%");
		} else {
			$querysql[]="(s_gedcom LIKE ".WT_DB::quote("%{$q}%")." OR s_gedcom LIKE ".WT_DB::quote(utf8_strtoupper("%{$q}%"))." OR s_gedcom LIKE ".WT_DB::quote(utf8_strtolower("%{$q}%")).")";
		}
	}

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec FROM {$TBLPREFIX}sources WHERE (".implode(" {$match} ", $querysql).') AND s_file IN ('.implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (WT_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$source=Source::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($source->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/\n\d\ '.WT_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}
		$list[]=$source;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
	}
	return $list;
}

// Search the gedcom records of shared notes
// $query - array of search terms
// $geds - array of gedcoms to search
// $match - AND or OR
// $skip - ignore data in certain tags
function search_notes($query, $geds, $match, $skip) {
	global $TBLPREFIX, $GEDCOM, $DB_UTF8_COLLATION;

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
		if ($DB_UTF8_COLLATION || !has_utf8($q)) {
			$querysql[]="o_gedcom LIKE ".WT_DB::quote("%{$q}%");
		} else {
			$querysql[]="(o_gedcom LIKE ".WT_DB::quote("%{$q}%")." OR o_gedcom LIKE ".WT_DB::quote(utf8_strtoupper("%{$q}%"))." OR o_gedcom LIKE ".WT_DB::quote(utf8_strtolower("%{$q}%")).")";
		}
	}

	$sql="SELECT 'NOTE' AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec FROM {$TBLPREFIX}other WHERE (".implode(" {$match} ", $querysql).") AND o_type='NOTE' AND o_file IN (".implode(',', $geds).')';

	// Group results by gedcom, to minimise switching between privacy files
	$sql.=' ORDER BY ged_id';

	// Tags we might not want to search
	if (WT_USER_IS_ADMIN) {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN) .*('.implode('|', $queryregex).')/im';
	} else {
		$skipregex='/^\d (_UID|_PGVU|FILE|FORM|TYPE|CHAN|SUBM|REFN|RESN) .*('.implode('|', $queryregex).')/im';
	}

	$list=array();
	$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
	$GED_ID=WT_GED_ID;
	foreach ($rows as $row) {
		// Switch privacy file if necessary
		if ($row['ged_id']!=$GED_ID) {
			$GEDCOM=get_gedcom_from_id($row['ged_id']);
			load_privacy_file($row['ged_id']);
			$GED_ID=$row['ged_id'];
		}
		$note=Note::getInstance($row);
		// SQL may have matched on private data or gedcom tags, so check again against privatized data.
		$gedrec=utf8_strtoupper($note->getGedcomRecord());
		foreach ($queryregex as $q) {
			if (!preg_match('/(\n\d|^0 @'.WT_REGEX_XREF.'@) '.WT_REGEX_TAG.' .*'.$q.'/i', $gedrec)) {
				continue 2;
			}
		}
		if ($skip && preg_match($skipregex, $gedrec)) {
			continue;
		}
		$list[]=$note;
	}
	// Switch privacy file if necessary
	if ($GED_ID!=WT_GED_ID) {
		$GEDCOM=WT_GEDCOM;
		load_privacy_file(WT_GED_ID);
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
	global $TBLPREFIX;
	static $statement=null;

	if (is_null($statement)) {
		$statement=WT_DB::prepare("SELECT p_id FROM {$TBLPREFIX}places WHERE p_level=? AND p_parent_id=? AND p_place LIKE ? AND p_file=?");
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
	global $TBLPREFIX;

	// --- find all of the place in the file
	if ($level==0) {
		return
			WT_DB::prepare("SELECT p_place FROM {$TBLPREFIX}places WHERE p_level=? AND p_file=? ORDER BY p_place")
			->execute(array(0, WT_GED_ID))
			->fetchOneColumn();
	} else {
		return
			WT_DB::prepare("SELECT p_place FROM {$TBLPREFIX}places WHERE p_level=? AND p_parent_id=? AND p_file=? ORDER BY p_place")
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
function get_place_positions($parent, $level='') {
	global $TBLPREFIX;

	// TODO: this function needs splitting into two

	if ($level!=='') {
		return
			WT_DB::prepare("SELECT DISTINCT pl_gid FROM {$TBLPREFIX}placelinks WHERE pl_p_id=? AND pl_file=?")
			->execute(array(get_place_parent_id($parent, $level), WT_GED_ID))
			->fetchOneColumn();
	} else {
		//-- we don't know the level so get the any matching place
		return
			WT_DB::prepare("SELECT DISTINCT pl_gid FROM {$TBLPREFIX}placelinks, {$TBLPREFIX}places WHERE p_place LIKE ? AND p_file=pl_file AND p_id=pl_p_id AND p_file=?")
			->execute(array($parent, WT_GED_ID))
			->fetchOneColumn();
	}
}

//-- find all of the places
function find_place_list($place) {
	global $TBLPREFIX;

	$rows=
		WT_DB::prepare("SELECT p_id, p_place, p_parent_id  FROM {$TBLPREFIX}places WHERE p_file=? ORDER BY p_parent_id, p_id")
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
	global $TBLPREFIX;

	$xref=
		WT_DB::prepare("SELECT i_id FROM {$TBLPREFIX}individuals WHERE i_rin=? AND i_file=?")
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
	global $TBLPREFIX;

	$ged=get_gedcom_from_id($ged_id);

	// Don't delete the logs.
	WT_DB::prepare("UPDATE {$TBLPREFIX}log SET gedcom_id=NULL   WHERE gedcom_id =?")->execute(array($ged_id));

	WT_DB::prepare("DELETE FROM {$TBLPREFIX}blocks              WHERE b_username=?")->execute(array($ged   ));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}news                WHERE n_username=?")->execute(array($ged   ));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}dates               WHERE d_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}families            WHERE f_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}favorites           WHERE fv_file   =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}user_gedcom_setting WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}gedcom_setting      WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}individuals         WHERE i_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}link                WHERE l_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}media               WHERE m_gedfile =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}media_mapping       WHERE mm_gedfile=?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}module_privacy      WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}name                WHERE n_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}nextid              WHERE ni_gedfile=?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}other               WHERE o_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}placelinks          WHERE pl_file   =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}places              WHERE p_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}sources             WHERE s_file    =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}hit_counter         WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}change              WHERE gedcom_id =?")->execute(array($ged_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}gedcom              WHERE gedcom_id =?")->execute(array($ged_id));

	if (get_site_setting('DEFAULT_GEDCOM')==$ged) {
		set_site_setting('DEFAULT_GEDCOM', '');
	}
}

/**
* get the top surnames
* @param int $ged_id	fetch surnames from this gedcom
* @param int $min	only fetch surnames occuring this many times
* @param int $max only fetch this number of surnames (0=all)
* @return array
*/
function get_top_surnames($ged_id, $min, $max) {
	global $TBLPREFIX;

	// Use n_surn, rather than n_surname, as it is used to generate url's for
	// the inid-list, etc.
	return
		WT_DB::prepareLimit("SELECT n_surn, COUNT(n_surn) FROM {$TBLPREFIX}name WHERE n_file=? AND n_type!=? AND n_surn NOT IN (?, ?, ?, ?) GROUP BY n_surn HAVING COUNT(n_surn)>=".$min." ORDER BY 2 DESC", $max)
		->execute(array($ged_id, '_MARNM', '@N.N.', '', '?', 'UNKNOWN'))
		->fetchAssoc();
}

/**
* get next unique id for the given table
* @param string $table  the name of the table
* @param string $field the field to get the next number for
* @return int the new id
*/
function get_next_id($table, $field) {
	global $TBLPREFIX, $TABLE_IDS;

	if (!isset($TABLE_IDS)) {
		$TABLE_IDS = array();
	}
	if (isset($TABLE_IDS[$table][$field])) {
		$TABLE_IDS[$table][$field]++;
		return $TABLE_IDS[$table][$field];
	}
	$newid=WT_DB::prepare("SELECT MAX({$field}) FROM {$TBLPREFIX}{$table}")->fetchOne();
	$newid++;
	$TABLE_IDS[$table][$field] = $newid;
	return $newid;
}

/**
* get a list of remote servers
*/
function get_server_list($ged_id=WT_GED_ID){
	global $TBLPREFIX;

	$sitelist = array();

	$rows=WT_DB::prepare("SELECT s_id, s_name, s_gedcom, s_file FROM {$TBLPREFIX}sources WHERE s_file=? AND s_dbid=? ORDER BY s_name")
		->execute(array($ged_id, 'Y'))
		->fetchAll();
	foreach ($rows as $row) {
		$source = array();
		$source["name"] = $row->s_name;
		$source["gedcom"] = $row->s_gedcom;
		$source["gedfile"] = $row->s_file;
		$source["url"] = get_gedcom_value("URL", 1, $row->s_gedcom);
		$sitelist[$row->s_id] = $source;
	}

	return $sitelist;
}

/**
* Retrieve the array of faqs from the DB table blocks
* @param int $id The FAQ ID to retrieve
* @return array $faqs The array containing the FAQ items
*/
function get_faq_data($id='') {
	global $TBLPREFIX, $GEDCOM;

	$faqs = array();
	// Read the faq data from the DB
	$sql="SELECT b_id, b_location, b_order, b_config, b_username FROM {$TBLPREFIX}blocks WHERE b_username IN (?, ?) AND b_name=?";
	$vars=array($GEDCOM, '*all*', 'faq');
	if ($id!='') {
		$sql.=" AND b_order=?";
		$vars[]=$id;
	} else {
		$sql.=' ORDER BY b_order';
	}
	$rows=WT_DB::prepare($sql)->execute($vars)->fetchAll();

	foreach ($rows as $row) {
		$faqs[$row->b_order][$row->b_location]["text"  ]=unserialize($row->b_config);
		$faqs[$row->b_order][$row->b_location]["pid"   ]=$row->b_id;
		$faqs[$row->b_order][$row->b_location]["gedcom"]=$row->b_username;
	}
	return $faqs;
}

function delete_fact($linenum, $pid, $gedrec) {
	global $linefix;

	if (!empty($linenum)) {
		if ($linenum==0) {
			delete_gedrec($pid, WT_GED_ID);
			print i18n::translate('GEDCOM record successfully deleted.');
		} else {
			$gedlines = explode("\n", $gedrec);
			// NOTE: The array_pop is used to kick off the last empty element on the array
			// NOTE: To prevent empty lines in the GEDCOM
			// DEBUG: Records without line breaks are imported as 1 big string
			if ($linefix > 0) {
				array_pop($gedlines);
			}
			$newged = "";
			// NOTE: Add all lines that are before the fact to be deleted
			for ($i=0; $i<$linenum; $i++) {
				$newged .= trim($gedlines[$i])."\n";
			}
			if (isset($gedlines[$linenum])) {
				$fields = explode(' ', $gedlines[$linenum]);
				$glevel = $fields[0];
				$ctlines = count($gedlines);
				$i++;
				if ($i<$ctlines) {
					// Remove the fact
					while ((isset($gedlines[$i]))&&($gedlines[$i]{0}>$glevel)) {
						$i++;
					}
					// Add the remaining lines
					while ($i<$ctlines) {
						$newged .= $gedlines[$i]."\n";
						$i++;
					}
				}
			}
			if ($newged != "") {
				return $newged;
			}
		}
	}
}

/**
* get_remote_id Recieves a RFN key and returns a Stub ID if the RFN exists
*
* @param mixed $rfn RFN number to see if it exists
* @access public
* @return gid Stub ID that contains the RFN number. Returns false if it didn't find anything
*/
function get_remote_id($rfn) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT r_gid FROM {$TBLPREFIX}remotelinks WHERE r_linkid=? AND r_file=?")
		->execute(array($rfn, WT_GED_ID))
		->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Get a list of events whose anniversary occured on a given julian day.
// Used on the on-this-day/upcoming blocks and the day/month calendar views.
// $jd     - the julian day
// $facts  - restrict the search to just these facts or leave blank for all
// $ged_id - the id of the gedcom to search
////////////////////////////////////////////////////////////////////////////////
function get_anniversary_events($jd, $facts='', $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	// If no facts specified, get all except these
	$skipfacts = "CHAN,BAPL,SLGC,SLGS,ENDL,CENS,RESI,NOTE,ADDR,OBJE,SOUR,PAGE,DATA,TEXT";
	if ($facts!='_TODO') {
		$skipfacts.=',_TODO';
	}

	$found_facts=array();
	foreach (array(new GregorianDate($jd), new JulianDate($jd), new FrenchRDate($jd), new JewishDate($jd), new HijriDate($jd)) as $anniv) {
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
					$tmp=new JewishDate(array($anniv->y, 'csh', 1));
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
					$tmp=new JewishDate($anniv->y, 'ksl', 1);
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
		$ind_sql="SELECT DISTINCT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex, d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}individuals {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_day ASC, d_year DESC";
		$fam_sql="SELECT DISTINCT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil, d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}families {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_day ASC, d_year DESC";
		foreach (array($ind_sql, $fam_sql) as $sql) {
			$rows=WT_DB::prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
			foreach ($rows as $row) {
				if ($row['type']=='INDI') {
					$record=Person::getInstance($row);
				} else {
					$record=Family::getInstance($row);
				}
				// Generate a regex to match the retrieved date - so we can find it in the original gedcom record.
				// TODO having to go back to the original gedcom is lame.  This is why it is so slow, and needs
				// to be cached.  We should store the level1 fact here (or somewhere)
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
						$date=new GedcomDate($match[1]);
						if (preg_match('/2 PLAC (.+)/', $factrec, $match)) {
							$plac=$match[1];
						} else {
							$plac='';
						}
						if (showFactDetails($row['d_fact'], $row['xref']) && !FactViewRestricted($row['xref'], $factrec)) {
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
	global $TBLPREFIX;

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
	$ind_sql="SELECT d_gid, i_gedcom, 'INDI', d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}individuals {$where} AND d_gid=i_id AND d_file=i_file ORDER BY d_julianday1";
	$fam_sql="SELECT d_gid, f_gedcom, 'FAM',  d_type, d_day, d_month, d_year, d_fact, d_type FROM {$TBLPREFIX}dates, {$TBLPREFIX}families    {$where} AND d_gid=f_id AND d_file=f_file ORDER BY d_julianday1";
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
					$date=new GedcomDate($match[1]);
					if (preg_match('/2 PLAC (.+)/', $factrec, $match)) {
						$plac=$match[1];
					} else {
						$plac='';
					}
					if (showFactDetails($row[7], $row[0]) && !FactViewRestricted($row[0], $factrec)) {
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
* pages.  It is also used by the RSS feed.
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
	global $TBLPREFIX;

	return
		(bool)WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}media WHERE m_file LIKE ? AND m_gedfile<>?")
		->execute(array("%{$file_name}", $ged_id))
		->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_SITE_SETTING table
// We can't cache/reuse prepared statements here, as we need to call these
// functions after performing DDL statements, and these invalidate any
// existing prepared statement handles in some databases.
////////////////////////////////////////////////////////////////////////////////
function get_site_setting($setting_name, $default=null) {
	global $TBLPREFIX;

	return WT_DB::prepare(
		"SELECT setting_value FROM {$TBLPREFIX}site_setting WHERE setting_name=?"
	)->execute(array($setting_name))->fetchOne($default);
}

function set_site_setting($setting_name, $setting_value) {
	global $TBLPREFIX;

	if (empty($setting_value)) {
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}site_setting WHERE setting_name=?")
			->execute(array($setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO {$TBLPREFIX}site_setting (setting_name, setting_value) VALUES (?, ?)")
			->execute(array($setting_name, $setting_value));
	}
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_GEDCOM table
////////////////////////////////////////////////////////////////////////////////

function get_all_gedcoms() {
	global $TBLPREFIX;
	
	return
		WT_DB::prepare("SELECT gedcom_id, gedcom_name FROM {$TBLPREFIX}gedcom")
		->fetchAssoc();
}

function get_gedcom_titles() {
	global $TBLPREFIX;
	
	return
		WT_DB::prepare(
			"SELECT g.gedcom_id, g.gedcom_name, COALESCE(gs.setting_value, g.gedcom_name) AS gedcom_title".
			" FROM {$TBLPREFIX}gedcom g".
			" LEFT JOIN {$TBLPREFIX}gedcom_setting gs ON (g.gedcom_id=gs.gedcom_id AND gs.setting_name=?)".
			" ORDER BY 3"
		)
		->execute(array('title'))
		->fetchAll();
}

function get_gedcom_from_id($ged_id) {
	global $TBLPREFIX;

	// No need to look up the default gedcom
	if (defined('WT_GED_ID') && defined('WT_GEDCOM') && $ged_id==WT_GED_ID) {
		return WT_GEDCOM;
	}

	return
		WT_DB::prepare("SELECT gedcom_name FROM {$TBLPREFIX}gedcom WHERE gedcom_id=?")
		->execute(array($ged_id))
		->fetchOne();
}

// Convert an (external) gedcom name to an (internal) gedcom ID.
// Optionally create an entry for it, if it does not exist.
function get_id_from_gedcom($ged_name, $create=false) {
	global $TBLPREFIX;

	// No need to look up the default gedcom
	if (defined('WT_GED_ID') && defined('WT_GEDCOM') && $ged_name==WT_GEDCOM) {
		return WT_GED_ID;
	}

	if ($create) {
		try {
			WT_DB::prepare("INSERT INTO {$TBLPREFIX}gedcom (gedcom_name) VALUES (?)")
				->execute(array($ged_name));
			$ged_id=WT_DB::getInstance()->lastInsertId();
			require_once WT_ROOT.'includes/classes/class_module.php';
			WT_Module::setDefaultAccess($ged_id);
			return $ged_id;
		} catch (PDOException $ex) {
			// The gedcom already exists - can't create
		}
	}

	return
		WT_DB::prepare("SELECT gedcom_id FROM {$TBLPREFIX}gedcom WHERE gedcom_name=?")
		->execute(array($ged_name))
		->fetchOne();
}


////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_GEDCOM_SETTING table
////////////////////////////////////////////////////////////////////////////////

function get_gedcom_setting($ged_id, $setting_name) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT setting_value FROM {$TBLPREFIX}gedcom_setting WHERE gedcom_id=? AND setting_name=?")
		->execute(array($ged_id, $setting_name))
		->fetchOne();
}

function set_gedcom_setting($ged_id, $setting_name, $setting_value) {
	global $TBLPREFIX;

	if (empty($setting_value)) {
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}gedcom_setting WHERE gedcom_id=? AND setting_name=?")
			->execute(array($ged_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO {$TBLPREFIX}gedcom_setting (gedcom_id, setting_name, setting_value) VALUES (?, ?, ?)")
			->execute(array($ged_id, $setting_name, $setting_value));
	}
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_USER table
////////////////////////////////////////////////////////////////////////////////

function create_user($username, $realname, $password) {
	global $TBLPREFIX;

	try {
		WT_DB::prepare("INSERT INTO {$TBLPREFIX}user (user_name, real_name, password) VALUES (?, ?, ?)")
			->execute(array($username, $realname, $password));
	} catch (PDOException $ex) {
		// User already exists?
	}
	return
		WT_DB::prepare("SELECT user_id FROM {$TBLPREFIX}user WHERE user_name=?")
		->execute(array($username))->fetchOne();
}

function rename_user($old_username, $new_username) {
	global $TBLPREFIX;

	WT_DB::prepare("UPDATE {$TBLPREFIX}user      SET user_name=?   WHERE user_name  =?")->execute(array($new_username, $old_username));
	WT_DB::prepare("UPDATE {$TBLPREFIX}blocks    SET b_username =? WHERE b_username =?")->execute(array($new_username, $old_username));
	WT_DB::prepare("UPDATE {$TBLPREFIX}favorites SET fv_username=? WHERE fv_username=?")->execute(array($new_username, $old_username));
	WT_DB::prepare("UPDATE {$TBLPREFIX}messages  SET m_from     =? WHERE m_from     =?")->execute(array($new_username, $old_username));
	WT_DB::prepare("UPDATE {$TBLPREFIX}messages  SET m_to       =? WHERE m_to       =?")->execute(array($new_username, $old_username));
	WT_DB::prepare("UPDATE {$TBLPREFIX}news      SET n_username =? WHERE n_username =?")->execute(array($new_username, $old_username));
}

function delete_user($user_id) {
	global $TBLPREFIX;

	$user_name=get_user_name($user_id);

	WT_DB::prepare("DELETE FROM {$TBLPREFIX}user_gedcom_setting WHERE user_id =?"        )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}user_setting        WHERE user_id =?"        )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}user                WHERE user_id =?"        )->execute(array($user_id));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}blocks              WHERE b_username =?"     )->execute(array($user_name));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}favorites           WHERE fv_username=?"     )->execute(array($user_name));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}messages            WHERE m_from=? OR m_to=?")->execute(array($user_name, $user_name));
	WT_DB::prepare("DELETE FROM {$TBLPREFIX}news                WHERE n_username =?"     )->execute(array($user_name));
}

function get_all_users($order='ASC', $key='realname') {
	global $TBLPREFIX;

	if ($key=='username') {
		return
			WT_DB::prepare("SELECT user_id, user_name FROM {$TBLPREFIX}user ORDER BY user_name")
			->fetchAssoc();
	} elseif ($key=='realname') {
		return
			WT_DB::prepare("SELECT user_id, user_name FROM {$TBLPREFIX}user ORDER BY real_name")
			->fetchAssoc();
	} else {
		return
			WT_DB::prepare(
				"SELECT u.user_id, user_name".
				" FROM {$TBLPREFIX}user u".
				" LEFT JOIN {$TBLPREFIX}user_setting us1 ON (u.user_id=us1.user_id AND us1.setting_name=?)".
				" ORDER BY us1.setting_value {$order}"
			)->execute(array($key))
			->fetchAssoc();
	}
}

function get_user_count() {
	global $TBLPREFIX;

	return
			WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}user")
			->fetchOne();
}

function get_admin_user_count() {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}user_setting WHERE setting_name=? AND setting_value=?")
		->execute(array('canadmin', 'Y'))
		->fetchOne();
}

function get_non_admin_user_count() {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT COUNT(*) FROM {$TBLPREFIX}user_setting WHERE  setting_name=? AND setting_value<>?")
		->execute(array('canadmin', 'Y'))
		->fetchOne();
}

// Get a list of logged-in users
function get_logged_in_users() {
	global $TBLPREFIX;

	return
		WT_DB::prepare(
			"SELECT u.user_id, user_name".
			" FROM {$TBLPREFIX}user u".
			" JOIN {$TBLPREFIX}user_setting us USING (user_id)".
			" WHERE setting_name=? AND setting_value=?"
		)
		->execute(array('loggedin', 'Y'))
		->fetchAssoc();
}

// Get a list of logged-in users who haven't been active recently
function get_idle_users($time) {
	global $TBLPREFIX, $DBTYPE;

	// Convert string column to numeric
	switch ($DBTYPE) {
	case 'mysql':
		$expr='CAST(us2.setting_value AS UNSIGNED)';
		break;
	default:
		$expr='us2.setting_value';
		break;
	}

	return
		WT_DB::prepare(
			"SELECT u.user_id, user_name".
			" FROM {$TBLPREFIX}user u".
			" JOIN {$TBLPREFIX}user_setting us1 USING (user_id)".
			" JOIN {$TBLPREFIX}user_setting us2 USING (user_id)".
			" WHERE us1.setting_name=? AND us1.setting_value=? AND us2.setting_name=?".
			" AND {$expr} BETWEEN 1 AND ?"
		)
		->execute(array('loggedin', 'Y', 'sessiontime', $time))
		->fetchAssoc();
}

// Get the ID for a username
function get_user_id($username) {
	global $TBLPREFIX;

	return WT_DB::prepare("SELECT user_id FROM {$TBLPREFIX}user WHERE user_name=?")
		->execute(array($username))
		->fetchOne();
}

// Get the username for a user ID
function get_user_name($user_id) {
	global $TBLPREFIX;

	return WT_DB::prepare("SELECT user_name FROM {$TBLPREFIX}user WHERE user_id=?")
		->execute(array($user_id))
		->fetchOne();
}

function get_newest_registered_user() {
	global $TBLPREFIX;

	return WT_DB::prepareLimit(
		"SELECT u.user_id".
		" FROM {$TBLPREFIX}user u".
		" LEFT JOIN {$TBLPREFIX}user_setting us ON (u.user_id=us.user_id AND us.setting_name=?) ".
		" ORDER BY us.setting_value DESC",
		1
	)->execute(array('reg_timestamp'))
		->fetchOne();
}

function set_user_password($user_id, $password) {
	global $TBLPREFIX;

	WT_DB::prepare("UPDATE {$TBLPREFIX}user SET password=? WHERE user_id=?")
		->execute(array($password, $user_id));
}

function get_user_password($user_id) {
	global $TBLPREFIX;

	return WT_DB::prepare("SELECT password FROM {$TBLPREFIX}user WHERE user_id=?")
		->execute(array($user_id))
		->fetchOne();
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_USER_SETTING table
////////////////////////////////////////////////////////////////////////////////

function get_user_setting($user_id, $setting_name) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT setting_value FROM {$TBLPREFIX}user_setting WHERE user_id=? AND setting_name=?")
		->execute(array($user_id, $setting_name))
		->fetchOne();
}

function set_user_setting($user_id, $setting_name, $setting_value) {
	global $TBLPREFIX;

	if (empty($setting_value)) {
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}user_setting WHERE user_id=? AND setting_name=?")
			->execute(array($user_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO {$TBLPREFIX}user_setting (user_id, setting_name, setting_value) VALUES (?, ?, ?)")
			->execute(array($user_id, $setting_name, $setting_value));
	}
}

function admin_user_exists() {
	return get_admin_user_count()>0;
}

////////////////////////////////////////////////////////////////////////////////
// Functions to access the WT_USER_GEDCOM_SETTING table
////////////////////////////////////////////////////////////////////////////////

function get_user_gedcom_setting($user_id, $ged_id, $setting_name) {
	global $TBLPREFIX;

	return
		WT_DB::prepare("SELECT setting_value FROM {$TBLPREFIX}user_gedcom_setting WHERE user_id=? AND gedcom_id=? AND setting_name=?")
		->execute(array($user_id, $ged_id, $setting_name))
		->fetchOne();
}

function set_user_gedcom_setting($user_id, $ged_id, $setting_name, $setting_value) {
	global $TBLPREFIX;

	if (empty($setting_value)) {
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}user_gedcom_setting WHERE user_id=? AND gedcom_id=? AND setting_name=?")
			->execute(array($user_id, $ged_id, $setting_name));
	} else {
		WT_DB::prepare("REPLACE INTO {$TBLPREFIX}user_gedcom_setting (user_id, gedcom_id, setting_name, setting_value) VALUES (?, ?, ?, ?)")
			->execute(array($user_id, $ged_id, $setting_name, $setting_value));
	}
}

function get_user_from_gedcom_xref($ged_id, $xref) {
	global $TBLPREFIX;

	return
		WT_DB::prepare(
			"SELECT user_id FROM {$TBLPREFIX}user_gedcom_setting".
			" WHERE gedcom_id=? AND setting_name=? AND setting_value=?"
		)->execute(array($ged_id, 'gedcomid', $xref))->fetchOne();
}

/**
* update favorites regarding a merge of records
*
* @param string $xref_from id to update
* @param string $xref_to id to update to
* @param string $ged_id gedcom to update
*/
function update_favorites($xref_from, $xref_to, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

$ged_name=get_gedcom_from_id($ged_id);

	return
		WT_DB::prepare("UPDATE {$TBLPREFIX}favorites SET fv_gid=? WHERE fv_gid=? AND fv_file=?")
		->execute(array($xref_to, $xref_from, $ged_name))
		->rowCount();
}
////////////////////////////////////////////////////////////////////////////////
// Autocomplete functions
////////////////////////////////////////////////////////////////////////////////

function get_autocomplete_INDI($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	// search for ids first and request the exact id from FILTER and ids with one additional digit
	$sql=
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
		" FROM {$TBLPREFIX}individuals, {$TBLPREFIX}name".
		" WHERE (i_id=? OR i_id LIKE ?)".
		" AND i_id=n_id AND i_file=n_file AND i_file=?".
		" ORDER BY i_id";
	$rows=
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("{$FILTER}", "{$FILTER}_", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
	// if the number of rows is not zero, the input is an id and you don't need to search the names for
	if (count($rows) ==0) {
		$sql=
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex".
			" FROM {$TBLPREFIX}individuals, {$TBLPREFIX}name".
			" WHERE n_sort LIKE ?".
			" AND i_id=n_id AND i_file=n_file AND i_file=?".
			" ORDER BY n_sort";
		return
			WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
			->execute(array("%{$FILTER}%", $ged_id))
			->fetchAll(PDO::FETCH_ASSOC);
	}
	else {
		return $rows;
	}
}

function get_autocomplete_FAM($FILTER, $ids, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$vars=array();
	if (empty($ids)) {
		//-- no match : search for FAM id
		$where = "f_id LIKE ?";
		$vars[]="%{$FILTER}%";
	} else {
		//-- search for spouses
		$qs=implode(',', array_fill(0, count($ids), '?'));
		$where = "(f_husb IN ($qs) OR f_wife IN ($qs))";
		$vars=array_merge($vars, $ids, $ids);
	}
	$sql="SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil ".
			 "FROM {$TBLPREFIX}families ".
			 "WHERE {$where} AND f_file=?";
	$vars[]=$ged_id;
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute($vars)
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_NOTE($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
			 "FROM {$TBLPREFIX}other ".
			 "WHERE o_gedcom LIKE ? AND o_type='NOTE' AND o_file=?";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_SOUR($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec ".
			 "FROM {$TBLPREFIX}sources ".
			 "WHERE (s_name LIKE ? OR s_id LIKE ?) AND s_file=? ORDER BY s_name";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", "{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_SOUR_TITL($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec ".
			 "FROM {$TBLPREFIX}sources ".
			 "WHERE s_name LIKE ? AND s_file=? ORDER BY s_name";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_INDI_BURI_CEME($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql=
		"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex ".
		"FROM {$TBLPREFIX}individuals ".
		"WHERE i_gedcom LIKE ? AND i_file=?";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%1 BURI%2 CEME %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec, i_isdead, i_sex ".
			 "FROM {$TBLPREFIX}individuals ".
			 "WHERE i_gedcom LIKE ? AND i_file=?";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("% SOUR @{$OPTION}@% PAGE %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql=
		"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec, f_husb, f_wife, f_chil, f_numchil ".
		"FROM {$TBLPREFIX}families ".
		"WHERE f_gedcom LIKE ? AND f_file=?";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("% SOUR @{$OPTION}@% PAGE %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_REPO($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql=
		"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
		"FROM {$TBLPREFIX}other ".
		"WHERE (o_gedcom LIKE ? OR o_id LIKE ?) AND o_file=? AND o_type='REPO'";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%1 NAME %{$FILTER}%", "{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_REPO_NAME($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql=
		"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
		"FROM {$TBLPREFIX}other ".
		"WHERE o_gedcom LIKE ? AND o_file=? AND o_type='REPO'";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%1 NAME %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_OBJE($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT m_media ".
			 "FROM {$TBLPREFIX}media ".
			 "WHERE (m_titl LIKE ? OR m_media LIKE ?) AND m_gedfile=?";
	return
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", "{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_SURN($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT n_surname ".
			 "FROM {$TBLPREFIX}name ".
			 "WHERE n_surname LIKE ? AND n_file=? ORDER BY n_surname";
	return 
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchOneColumn();
}

function get_autocomplete_GIVN($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX;

	$sql="SELECT DISTINCT n_givn ".
			 "FROM {$TBLPREFIX}name ".
			 "WHERE n_givn LIKE ? AND n_file=? ORDER BY n_givn";
	return 
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchAll();
}

function get_autocomplete_PLAC($FILTER, $ged_id=WT_GED_ID) {
	global $TBLPREFIX, $DBTYPE;

	$sql=
		"select p1.p_place".
		" from {$TBLPREFIX}places p1".
		" where p1.p_place like ? and p1.p_parent_id=0 AND p1.p_file=?".
		" union ".
		"select CONCAT(p1.p_place, ', ', p2.p_place)".
		" from {$TBLPREFIX}places p1".
		" join {$TBLPREFIX}places p2 ON (p1.p_parent_id=p2.p_id AND p1.p_file=p2.p_file)".
		" where p1.p_place like ? and p2.p_parent_id=0 AND p1.p_file=?".
		" union ".
		"select CONCAT(p1.p_place, ', ', p2.p_place, ', ', p3.p_place)".
		" from {$TBLPREFIX}places p1".
		" join {$TBLPREFIX}places p2 ON (p1.p_parent_id=p2.p_id AND p1.p_file=p2.p_file)".
		" join {$TBLPREFIX}places p3 ON (p2.p_parent_id=p3.p_id AND p2.p_file=p3.p_file)".
		" where p1.p_place like ? and p3.p_parent_id=0 AND p1.p_file=?".
		" union ".
		"select CONCAT(p1.p_place, ', ', p2.p_place, ', ', p3.p_place, ', ', p4.p_place)".
		" from {$TBLPREFIX}places p1".
		" join {$TBLPREFIX}places p2 ON (p1.p_parent_id=p2.p_id AND p1.p_file=p2.p_file)".
		" join {$TBLPREFIX}places p3 ON (p2.p_parent_id=p3.p_id AND p2.p_file=p3.p_file)".
		" join {$TBLPREFIX}places p4 ON (p3.p_parent_id=p4.p_id AND p3.p_file=p4.p_file)".
		" where p1.p_place like ? and p4.p_parent_id=0 AND p1.p_file=?".
		" union ".
		"select CONCAT(p1.p_place, ', ', p2.p_place, ', ', p3.p_place, ', ', p4.p_place, ', ', p5.p_place)".
		" from {$TBLPREFIX}places p1".
		" join {$TBLPREFIX}places p2 ON (p1.p_parent_id=p2.p_id AND p1.p_file=p2.p_file)".
		" join {$TBLPREFIX}places p3 ON (p2.p_parent_id=p3.p_id AND p2.p_file=p3.p_file)".
		" join {$TBLPREFIX}places p4 ON (p3.p_parent_id=p4.p_id AND p3.p_file=p4.p_file)".
		" join {$TBLPREFIX}places p5 ON (p4.p_parent_id=p5.p_id AND p4.p_file=p5.p_file)".
		" where p1.p_place like ? and p5.p_parent_id=0 AND p1.p_file=?";

	return 
		WT_DB::prepareLimit($sql, WT_AUTOCOMPLETE_LIMIT)
		->execute(array("%{$FILTER}%", $ged_id, "%{$FILTER}%", $ged_id, "%{$FILTER}%", $ged_id, "%{$FILTER}%", $ged_id, "%{$FILTER}%", $ged_id))
		->fetchOneColumn();
}

?>
