<?php
// Returns data for autocompletion
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'autocomplete.php');
require './includes/session.php';

header('Content-Type: text/plain; charset=UTF-8');

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

//-- args
$FILTER=safe_GET('term', WT_REGEX_UNSAFE); // we can search on '"><& etc.
$OPTION=safe_GET('option');
$FIELD =safe_GET('field');

switch ($FIELD) {
case 'INDI':
	$data=autocomplete_INDI($FILTER, $OPTION);
	break;
case 'FAM':
	$data=autocomplete_FAM($FILTER, $OPTION);
	break;
case 'NOTE':
	$data=autocomplete_NOTE($FILTER);
	break;
case 'SOUR':
	$data=autocomplete_SOUR($FILTER);
	break;
case 'SOUR_TITL':
	$data=autocomplete_SOUR($FILTER);
	break;
case 'INDI_BURI_CEME':
	$data=autocomplete_INDI_BURI_CEME($FILTER);
	break;
case 'INDI_SOUR_PAGE':
	$data=autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION);
	break;
case 'FAM_SOUR_PAGE':
	$data=autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION);
	break;
case 'SOUR_PAGE':
	$data=autocomplete_SOUR_PAGE($FILTER, $OPTION);
	break;
case 'REPO':
	$data=autocomplete_REPO($FILTER);
	break;
case 'REPO_NAME':
	$data=autocomplete_REPO_NAME($FILTER);
	break;
case 'OBJE':
	$data=autocomplete_OBJE($FILTER);
	break;
case 'IFSRO':
	$data=autocomplete_IFSRO($FILTER);
	break;
case 'SURN':
	$data=autocomplete_SURN($FILTER);
	break;
case 'GIVN':
	$data=autocomplete_GIVN($FILTER);
	break;
case 'NAME':
	$data=autocomplete_NAME($FILTER);
	break;
case 'PLAC':
	$data=autocomplete_PLAC($FILTER, $OPTION);
	break;
default:
	die("Bad arg: field={$FIELD}");
}

$results=array();
foreach ($data as $value=>$label) {
	$results[]=array('value'=>$value, 'label'=>$label);
}
echo json_encode($results);
exit;

/**
* returns INDIviduals matching filter
* @return Array of string
*/
function autocomplete_INDI($FILTER, $OPTION) {
	global $MAX_ALIVE_AGE;

	// when adding ASSOciate $OPTION may contain :
	// current INDI/FAM [, current event date]
	if ($OPTION) {
		list($pid, $event_date) = explode("|", $OPTION."|");
		$record=WT_GedcomRecord::getInstance($pid); // INDI or FAM
		$tmp=new WT_Date($event_date);
		$event_jd=$tmp->JD();
		// INDI
		$indi_birth_jd = 0;
		if ($record && $record->getType()=="INDI") {
			$indi_birth_jd=$record->getEstimatedBirthDate()->minJD();
		}
		// HUSB & WIFE
		$husb_birth_jd = 0;
		$wife_birth_jd = 0;
		if ($record && $record->getType()=="FAM") {
			$husb=$record->getHusband();
			if ($husb) {
				$husb_birth_jd = $husb->getEstimatedBirthDate()->minJD();
			}
			$wife=$record->getWife();
			if ($wife) {
				$wife_birth_jd = $wife->getEstimatedBirthDate()->minJD();
			}
		}
	}
	$rows=get_autocomplete_INDI($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$person=WT_Person::getInstance($row);
		if ($person->canDisplayName()) {
			// filter ASSOciate
			if ($OPTION && $event_jd) {
				// no self-ASSOciate
				if ($pid && $person->getXref()==$pid) {
					continue;
				}
				// filter by birth date
				$person_birth_jd=$person->getEstimatedBirthDate()->minJD();
				if ($person_birth_jd) {
					// born after event or not a contemporary
					if ($event_jd && $person_birth_jd>$event_jd) {
						continue;
					} elseif ($indi_birth_jd && abs($indi_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					} elseif ($husb_birth_jd && $wife_birth_jd && abs($husb_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365 && abs($wife_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					} elseif ($husb_birth_jd && abs($husb_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					} elseif ($wife_birth_jd && abs($wife_birth_jd-$person_birth_jd)>$MAX_ALIVE_AGE*365) {
						continue;
					}
				}
				// filter by death date
				$person_death_jd=$person->getEstimatedDeathDate()->MaxJD();
				if ($person_death_jd) {
					// dead before event or not a contemporary
					if ($event_jd && $person_death_jd<$event_jd) {
						continue;
					} elseif ($indi_birth_jd && $person_death_jd<$indi_birth_jd) {
						continue;
					} elseif ($husb_birth_jd && $wife_birth_jd && $person_death_jd<$husb_birth_jd && $person_death_jd<$wife_birth_jd) {
						continue;
					} elseif ($husb_birth_jd && $person_death_jd<$husb_birth_jd) {
						continue;
					} elseif ($wife_birth_jd && $person_death_jd<$wife_birth_jd) {
						continue;
					}
				}
			}
			// display
			$data[$person->getXref()]=$person->getFullName();
			if ($OPTION && $event_date && $person->getBirthDate()->isOK()) {
				$data[$person->getXref()].=" <span class=\"age\">(".WT_I18N::translate('Age')." ".$person->getBirthDate()->MinDate()->getAge(false, $event_jd).")</span>";
			} else {
				$data[$person->getXref()].=" <u>".ltrim($person->getBirthYear(), "0")."-".ltrim($person->getDeathYear(), "0")."</u>";
			}
		}
	}
	return $data;
}

/**
* returns FAMilies matching filter
* @return Array of string
*/
function autocomplete_FAM($FILTER, $OPTION) {

	//-- search for INDI names
	$ids=array_keys(autocomplete_INDI($FILTER, $OPTION));

	$rows=get_autocomplete_FAM($FILTER, $ids);
	$data=array();
	foreach ($rows as $row) {
		$family = WT_Family::getInstance($row);
		if ($family->canDisplayName()) {
			$data[$row["xref"]] =
				$family->getFullName().
				" <u>".
				ltrim($family->getMarriageYear(), "0").
				"</u>";
		}
	}
	return $data;
}

/**
* returns NOTEs (Shared) matching filter
* @return Array of string
*/
function autocomplete_NOTE($FILTER) {

	$rows=get_autocomplete_NOTE($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$note = WT_Note::getInstance($row);
		if ($note->canDisplayName()) {
			$data[$row["xref"]] = $note->getFullName();
		}
	}
	return $data;
}

/**
* returns SOURces matching filter
* @return Array of string
*/
function autocomplete_SOUR($FILTER) {

	$rows=get_autocomplete_SOUR($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$source = WT_Source::getInstance($row);
		if ($source->canDisplayName()) {
			$data[$row["xref"]] = $source->getFullName();
		}
	}
	return $data;
}

/**
* returns SOUR:TITL matching filter
* @return Array of string
*/
function autocomplete_SOUR_TITL($FILTER) {

	$rows=get_autocomplete_SOUR_TITL($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$source = WT_Source::getInstance($row);
		if ($source->canDisplayName()) {
			$data[] = $source->getFullName();
		}
	}
	return $data;
}

/**
* returns INDI_BURI_CEME matching filter
* @return Array of string
*/
function autocomplete_INDI_BURI_CEME($FILTER) {

	$rows=get_autocomplete_INDI_BURI_CEME($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$person = WT_Person::getInstance($row);
		if ($person->canDisplayDetails()) {
			$i = 1;
			do {
				$srec = get_sub_record("BURI", 1, $person->getGedcomRecord(), $i++);
				$ceme = get_gedcom_value("CEME", 2, $srec);
				if (stripos($ceme, $FILTER)!==false || empty($FILTER)) {
					$data[] = $ceme;
				}
			} while ($srec);
		}
	}
	return $data;
}

/**
* returns INDI:SOUR:PAGE matching filter
* @return Array of string
*/
function autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION) {

	$rows=get_autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION);
	$data=array();
	foreach ($rows as $row) {
		$person = WT_Person::getInstance($row);
		if ($person->canDisplayDetails()) {
			// a single INDI may have multiple level 1 and level 2 sources
			for ($level=1; $level<=2; $level++) {
				$i = 1;
				do {
					$srec = get_sub_record("SOUR @{$OPTION}@", $level, $person->getGedcomRecord(), $i++);
					$page = get_gedcom_value("PAGE", $level+1, $srec);
					if (stripos($page, $FILTER)!==false || empty($FILTER)) {
						$data[] = $page;
					}
				} while ($srec);
			}
		}
	}
	return $data;
}

/**
* returns FAM:SOUR:PAGE matching filter
* @return Array of string
*/
function autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION) {

	$rows=get_autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION);
	$data=array();
	foreach ($rows as $row) {
		$family = WT_Family::getInstance($row);
		if ($family->canDisplayDetails()) {
			// a single FAM may have multiple level 1 and level 2 sources
			for ($level=1; $level<=2; $level++) {
				$i = 1;
				do {
					$srec = get_sub_record("SOUR @{$OPTION}@", $level, $family->getGedcomRecord(), $i++);
					$page = get_gedcom_value("PAGE", $level+1, $srec);
					if (stripos($page, $FILTER)!==false || empty($FILTER)) {
						$data[] = $page;
					}
				} while ($srec);
			}
		}
	}
	return $data;
}

/**
* returns SOUR:PAGE matching filter
* @return Array of string
*/
function autocomplete_SOUR_PAGE($FILTER, $OPTION) {
	return array_merge(
		autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION),
		autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION));
}

/**
* returns REPOsitories matching filter
* @return Array of string
*/
function autocomplete_REPO($FILTER) {

	$rows=get_autocomplete_REPO($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$repository = WT_Repository::getInstance($row);
		if ($repository->canDisplayName()) {
			$data[$row["xref"]] = $repository->getFullName();
		}
	}
	return $data;
}

/**
* returns REPO:NAME matching filter
* @return Array of string
*/
function autocomplete_REPO_NAME($FILTER) {

	$rows=get_autocomplete_REPO_NAME($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$repository = WT_Repository::getInstance($row);
		if ($repository->canDisplayName()) {
			$data[] = $repository->getFullName();
		}
	}
	return $data;
}

/**
* returns OBJEcts matching filter
* @return Array of string
*/
function autocomplete_OBJE($FILTER) {

	$rows=get_autocomplete_OBJE($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$media = WT_Media::getInstance($row["m_media"]);
		if ($media && $media->canDisplayDetails()) {
			$data[$row["m_media"]] =
				"<img alt=\"".
				$media->getXref().
				"\" src=\"".
				$media->getThumbnail().
				"\" width=\"40\"> ".
				$media->getFullName();
		}
	}
	return $data;
}

/**
* returns INDI FAM SOUR NOTE REPO OBJE matching filter
* @return Array of string
*/
function autocomplete_IFSRO() {
	global $GEDCOM_ID_PREFIX, $FAM_ID_PREFIX, $SOURCE_ID_PREFIX, $NOTE_ID_PREFIX, $REPO_ID_PREFIX, $MEDIA_ID_PREFIX, $FILTER;

	// is input text a gedcom xref ?
	$prefix = strtoupper(substr($FILTER, 0, 1));
	if (ctype_digit(substr($FILTER, 1))) {
		if ($prefix == $GEDCOM_ID_PREFIX) {
			return autocomplete_INDI($FILTER, '');
		} elseif ($prefix == $FAM_ID_PREFIX) {
			return autocomplete_FAM($FILTER, '');
		} elseif ($prefix == $SOURCE_ID_PREFIX) {
			return autocomplete_SOUR($FILTER);
		} elseif ($prefix == $NOTE_ID_PREFIX) {
			return autocomplete_NOTE($FILTER);
		} elseif ($prefix == $REPO_ID_PREFIX) {
			return autocomplete_REPO($FILTER);
		} elseif ($prefix == $MEDIA_ID_PREFIX) {
			return autocomplete_OBJE($FILTER);
		}
	}
	return array_merge(
		autocomplete_INDI($FILTER, ''),
		autocomplete_FAM($FILTER, ''),
		autocomplete_SOUR($FILTER),
		autocomplete_NOTE($FILTER),
		autocomplete_REPO($FILTER),
		autocomplete_OBJE($FILTER)
		);
}

/**
* returns SURNames matching filter
* @return Array of string
*/
function autocomplete_SURN($FILTER) {
	return get_autocomplete_SURN($FILTER);
}

/**
* returns GIVenNames matching filter
* @return Array of string
*/
function autocomplete_GIVN($FILTER) {

	$rows=get_autocomplete_GIVN($FILTER);
	$data=array();
	foreach ($rows as $row) {
		$givn=$row->n_givn;
		list($givn) = explode("/", $givn);
		list($givn) = explode(",", $givn);
		list($givn) = explode("*", $givn);
		list($givn) = explode(" ", $givn);
		if ($givn) {
			$data[]=$row->n_givn;
		}
	}
	return $data;
}

/**
* returns NAMEs matching filter
* @return Array of string
*/
function autocomplete_NAME($FILTER) {
	return array_merge(autocomplete_GIVN($FILTER), autocomplete_SURN($FILTER));
}

/**
* returns PLACes matching filter
* @return Array of string City, County, State/Province, Country
*/
function autocomplete_PLAC($FILTER, $OPTION) {
	global $USE_GEONAMES;

	$data=get_autocomplete_PLAC($FILTER);

	//-- no match => perform a geoNames query if enabled
	if (empty($data) && $USE_GEONAMES) {
		$url = "http://ws.geonames.org/searchJSON".
					"?name_startsWith=".urlencode($FILTER).
					"&lang=".WT_LOCALE.
					"&fcode=CMTY&fcode=ADM4&fcode=PPL&fcode=PPLA&fcode=PPLC".
					"&style=full";
		// try to use curl when file_get_contents not allowed
		if (ini_get('allow_url_fopen')) {
			$json = file_get_contents($url);
		} elseif (function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$json = curl_exec($ch);
			curl_close($ch);
		} else {
			return $data;
		}
		$places = json_decode($json, true);
		if ($places["geonames"]) {
			foreach ($places["geonames"] as $k => $place) {
				$data[] = $place["name"].", ".
									$place["adminName2"].", ".
									$place["adminName1"].", ".
									$place["countryName"];
			}
		}
	}

	// split ?
	if ($OPTION=="split") {
		foreach ($data as $k=>$v) {
			list($data[$k]) = explode(",", $v);
		}
	}

	return $data;
}

function get_autocomplete_INDI($FILTER, $ged_id=WT_GED_ID) {
	// search for ids first and request the exact id from FILTER and ids with one additional digit
	$rows=
		WT_DB::prepare(
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec".
			" FROM `##individuals`, `##name`".
			" WHERE (i_id=? OR i_id LIKE ?)".
			" AND i_id=n_id AND i_file=n_file AND i_file=?".
			" ORDER BY i_id"
		)
		->execute(array("{$FILTER}", "{$FILTER}_", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
	// if the number of rows is not zero, the input is an id and you don't need to search the names for
	if (count($rows)==0) {
		return
			WT_DB::prepare(
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec".
			" FROM `##individuals`, `##name`".
			" WHERE (n_sort LIKE ? OR n_full LIKE ?)".
			" AND i_id=n_id AND i_file=n_file AND i_file=?".
			" ORDER BY n_sort"
			)
			->execute(array("%{$FILTER}%", "%{$FILTER}%", $ged_id))
			->fetchAll(PDO::FETCH_ASSOC);
	} else {
		return $rows;
	}
}

function get_autocomplete_FAM($FILTER, $ids, $ged_id=WT_GED_ID) {
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
	$vars[]=$ged_id;
	return
		WT_DB::prepare(
			"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec".
			" FROM `##families`".
			" WHERE {$where} AND f_file=?"
		)
		->execute($vars)
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_NOTE($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
		"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
		" FROM `##other`".
		" WHERE o_gedcom LIKE ? AND o_type='NOTE' AND o_file=?"
		)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_SOUR($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec".
			" FROM `##sources`".
			" WHERE (s_name LIKE ? OR s_id LIKE ?) AND s_file=? ORDER BY s_name"
		)
		->execute(array("%{$FILTER}%", "{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_SOUR_TITL($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT 'SOUR' AS type, s_id AS xref, s_file AS ged_id, s_gedcom AS gedrec".
			" FROM `##sources`".
			" WHERE s_name LIKE ? AND s_file=? ORDER BY s_name"
		)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_INDI_BURI_CEME($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec".
			" FROM `##individuals`".
			" WHERE i_gedcom LIKE ? AND i_file=?"
		)
		->execute(array("%1 BURI%2 CEME %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_INDI_SOUR_PAGE($FILTER, $OPTION, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec".
			" FROM `##individuals`".
			" WHERE i_gedcom LIKE ? AND i_file=?"
		)
		->execute(array("% SOUR @{$OPTION}@% PAGE %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_FAM_SOUR_PAGE($FILTER, $OPTION, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec".
			" FROM `##families`".
			" WHERE f_gedcom LIKE ? AND f_file=?"
		)
		->execute(array("% SOUR @{$OPTION}@% PAGE %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_REPO($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
			" FROM `##other`".
			" WHERE (o_gedcom LIKE ? OR o_id LIKE ?) AND o_file=? AND o_type='REPO'"
		)
		->execute(array("%1 NAME %{$FILTER}%", "{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_REPO_NAME($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec".
			" FROM `##other`".
			" WHERE o_gedcom LIKE ? AND o_file=? AND o_type='REPO'"
		)
		->execute(array("%1 NAME %{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_OBJE($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT m_media".
			" FROM `##media`".
			" WHERE (m_titl LIKE ? OR m_media LIKE ?) AND m_gedfile=?"
		)
		->execute(array("%{$FILTER}%", "{$FILTER}%", $ged_id))
		->fetchAll(PDO::FETCH_ASSOC);
}

function get_autocomplete_SURN($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT DISTINCT n_surname".
			" FROM `##name`".
			" WHERE n_surname LIKE ? AND n_file=? ORDER BY n_surname"
		)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchOneColumn();
}

function get_autocomplete_GIVN($FILTER, $ged_id=WT_GED_ID) {
	return
		WT_DB::prepare(
			"SELECT DISTINCT n_givn".
			" FROM `##name`".
			" WHERE n_givn LIKE ? AND n_file=? ORDER BY n_givn"
		)
		->execute(array("%{$FILTER}%", $ged_id))
		->fetchAll();
}
//PLAC autocomplete - three functions
function get_autocomplete_PLAC($FILTER, $ged_id=WT_GED_ID) {

        // Retrieve the maximum number of levels
	$nbLevels = get_number_levels_PLAC($ged_id) + 1;
	
        // Generate the SQL statement
	$sql = generate_place_sql($nbLevels);
	
	$arrayFilter = array();
	for ($i = 0; $i < $nbLevels; $i++) {
		$arrayFilter[] = "%{$FILTER}%";
		$arrayFilter[] =  $ged_id;
	}
	
	return WT_DB::prepare($sql)->execute($arrayFilter)->fetchOneColumn();
}

function get_number_levels_PLAC($ged_id=WT_GED_ID){
	return
		WT_DB::prepare("SELECT MAX(p_level) FROM `##places` WHERE p_file=?")
		->execute(array($ged_id))
		->fetchOne(0);
}

function generate_place_sql($nbLevels) { 
	if($nbLevels<=1){ 
    	return 
        	"SELECT p1.p_place". 
            " FROM `##places` p1". 
            " WHERE p1.p_place like ? and p1.p_parent_id=0 AND p1.p_file=?"; 
	}
	else {
		$select = "p1.p_place"; 
		$from = "`##places` p1";
		for($i = 2; $i <= $nbLevels ; $i++) { 
			$select .= ", ', ', p".$i.".p_place";
			$from .= " JOIN `##places` p".$i." ON (p".($i-1).".p_parent_id=p".$i.".p_id AND p".($i-1).".p_file=p".$i.".p_file)"; 
		}
		return
			"SELECT CONCAT(".$select.")".
			" FROM ".$from.
			" WHERE p1.p_place like ? and p".$nbLevels.".p_parent_id=0 AND p1.p_file=?".
			" UNION ".
			generate_place_sql($nbLevels - 1);
	}
//end of PLAC autocomplete functions
} 
