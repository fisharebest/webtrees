<?php
/**
* Privacy Functions
*
* See http://www.phpgedview.net/privacy.php for more information on privacy in webtrees
*
* webtrees: Web based Family History software
* Copyright (C) 2010 webtrees development team.
*
* Derived from PhpGedView
* Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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
* @subpackage Privacy
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_PRIVACY_PHP', '');

if ($USE_RELATIONSHIP_PRIVACY) {
	/**
	* store relationship paths in a cache
	*
	* the <var>$NODE_CACHE</var> is an array of nodes that have been previously checked
	* by the relationship calculator.  This cache greatly speed up the relationship privacy
	* checking on charts as many relationships on charts are in the same relationship path.
	*
	* See the documentation for the get_relationship() function in the functions.php file.
	*/
	$NODE_CACHE = array();
}

//-- allow users to overide functions in privacy file
if (!function_exists("is_dead")) {
/**
* check if a person is dead
*
* this function will read a person's gedcom record and try to determine whether the person is
* dead or not.  It checks several parameters to determine death status in the following order:
* 1. a DEAT record returns dead
* 2. a BIRT record less than <var>$MAX_ALIVE_AGE</var> returns alive
* 3. Any date in the record that would make them older than <var>$MAX_ALIVE_AGE</var>
* 4. A date in the parents record that makes the parents older than <var>$MAX_ALIVE_AGE</var>+40
* 5. A marriage record with a date greater than <var>$MAX_ALIVE_AGE</var>-10
* 6. A date in the spouse record greater than <var>$MAX_ALIVE_AGE</var>
* 7. A date in the children's record that is greater than <var>$MAX_ALIVE_AGE</var>-10
* 8. A date in the grand children's record that is greater than <var>$MAX_ALIVE_AGE</var>-30
*
* This function should only be called once per individual.  In index mode this is called during
* the Gedcom import.  In MySQL mode this is called the first time the individual is accessed
* and then the database table is updated.
* @author John Finlay (yalnifj)
* @param string $indirec the raw gedcom record
* @return bool true if dead false if alive
*/
function is_dead($indirec, $current_year='', $import=false) {
	global $MAX_ALIVE_AGE, $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	if (preg_match('/^0 @('.WT_REGEX_XREF.')@ INDI/', $indirec, $match)) {
		$pid=$match[1];
	} else {
		return false;
	}
	
	// Allow "current year" to be modified, for countries where deaths do not become
	// public until a certain time period has elapsed.
	if (empty($current_year)) {
		// If we're not redefining this, then we can do a quick check for undated deaths
		if (preg_match('/\n1 (?:'.WT_EVENTS_DEAT.')(?: Y|(?:\n[2-9].+)*\n2 PLAC )/', $indirec)) {
			return update_isdead($pid, WT_GED_ID, true);
		}
		// Base the calculations against the current year
		$current_year=date('Y');
	}

	// Check for a death record occuring on/before the current year.
	preg_match_all('/\n1 (?:'.WT_EVENTS_DEAT.')(?:\n[2-9].+)*\n2 DATE (.+)/', $indirec, $date_matches);
	foreach ($date_matches[1] as $date_match) {
		$date=new GedcomDate($date_match);
		if ($date->isOK()) {
			$death_year=$date->gregorianYear();
			return update_isdead($pid, WT_GED_ID, $death_year<=$current_year);
		}
	}

	// If any event occured more than $MAX_ALIVE_AGE years ago, then assume the person is dead
	preg_match_all('/\n2 DATE (.+)/', $indirec, $date_matches);
	foreach ($date_matches[1] as $date_match) {
		$date=new GedcomDate($date_match);
		if ($date->isOK()) {
			$event_year=$date->gregorianYear();
			if ($current_year-$event_year >= $MAX_ALIVE_AGE) {
				return update_isdead($pid, WT_GED_ID, true);
			}
		}
	}

	//-- during import we can't check child dates
	if ($import) {
		return -1;
	}

	// If we found no dates then check the dates of close relatives.
	// Check parents (birth and adopted)
	preg_match_all('/\n1 FAMC @('.WT_REGEX_XREF.')@/', $indirec, $famc_matches);
	foreach ($famc_matches[1] as $famc_match) {
		$parents=find_parents($famc_match);
		if ($parents) {
			if (!empty($parents['HUSB'])) {
				preg_match_all('/\n2 DATE (.+)/', find_person_record($parents['HUSB'], $ged_id), $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date=new GedcomDate($date_match);
					if ($date->isOK()) {
						$event_year=$date->gregorianYear();
						// Assume fathers are no more than 40 years older than their children
						if ($current_year-$event_year >= $MAX_ALIVE_AGE+40) {
							return update_isdead($pid, WT_GED_ID, true);
						}
					}
				}
			}
			if (!empty($parents['WIFE'])) {
				preg_match_all('/\n2 DATE (.+)/', find_person_record($parents['WIFE'], $ged_id), $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date=new GedcomDate($date_match);
					if ($date->isOK()) {
						$event_year=$date->gregorianYear();
						// Assume fathers are no more than 40 years older than their children
						if ($current_year-$event_year >= $MAX_ALIVE_AGE+40) {
							return update_isdead($pid, WT_GED_ID, true);
						}
					}
				}
			}
		}
	}
	$children = array();
	// Check spouses
	preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $indirec, $fams_matches);
	foreach ($fams_matches[1] as $fams_match) {
		$famrec=find_family_record($fams_match, $ged_id);
		// Check all marriage events
		preg_match_all('/\n1 (?:'.WT_EVENTS_MARR.')(?:\n[2-9].+)*\n2 DATE (.+)/', $indirec, $date_matches);
		foreach ($date_matches[1] as $date_match) {
			$date=new GedcomDate($date_match);
			if ($date->isOK()) {
				$event_year=$date->gregorianYear();
				// Assume marriage occurs after age of 10
				if ($current_year-$event_year >= $MAX_ALIVE_AGE-10) {
					return update_isdead($pid, WT_GED_ID, true);
				}
			}
		}
		// Check spouse dates
		$parents = find_parents_in_record($famrec);
		if ($parents) {
			if ($parents['HUSB']!=$pid) {
				$spid = $parents['HUSB'];
			} else {
				$spid = $parents['WIFE'];
			}
			preg_match_all('/\n2 DATE (.+)/', find_person_record($spid, $ged_id), $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date=new GedcomDate($date_match);
				if ($date->isOK()) {
					$event_year=$date->gregorianYear();
					// Assume max age difference between spouses of 40 years
					if ($current_year-$event_year >= $MAX_ALIVE_AGE+40) {
						return update_isdead($pid, WT_GED_ID, true);
					}
				}
			}
		}
		// Check child dates
		preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', $famrec, $chil_matches);
		foreach ($chil_matches[1] as $chil_match) {
			$childrec=find_person_record($chil_match, $ged_id);
			preg_match_all('/\n2 DATE (.+)/', $childrec, $date_matches);
			// Assume children born after age of 15
			foreach ($date_matches[1] as $date_match) {
				$date=new GedcomDate($date_match);
				if ($date->isOK()) {
					$event_year=$date->gregorianYear();
					if ($current_year-$event_year >= $MAX_ALIVE_AGE-15) {
						return update_isdead($pid, WT_GED_ID, true);
					}
				}
			}
			// Check grandchildren
			preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $childrec, $fams2_matches);
			foreach ($fams2_matches[1] as $fams2_match) {
				preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', find_family_record($fams2_match, $ged_id), $chil2_matches);
				foreach ($chil2_matches[1] as $chil2_match) {
					$grandchildrec=find_person_record($chil2_match, $ged_id);
					preg_match_all('/\n2 DATE (.+)/', $grandchildrec, $date_matches);
					// Assume grandchildren born after age of 30
					foreach ($date_matches[1] as $date_match) {
						$date=new GedcomDate($date_match);
						if ($date->isOK()) {
							$event_year=$date->gregorianYear();
							if ($current_year-$event_year >= $MAX_ALIVE_AGE-30) {
								return update_isdead($pid, WT_GED_ID, true);
							}
						}
					}
				}
			}
		}
	}
	return update_isdead($pid, WT_GED_ID, false);
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("displayDetailsById")) {

/**
* checks if the person has died recently before showing their data
* @param string $pid the id of the person to check
* @return boolean
*/
function checkPrivacyByYear($pid) {
	global $MAX_ALIVE_AGE;
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	$cyear = date("Y");
	$indirec = find_person_record($pid, $ged_id);
	//-- check death record
	$deatrec = get_sub_record(1, "1 DEAT", $indirec);
	$ct = preg_match("/2 DATE .*(\d\d\d\d).*/", $deatrec, $match);
	if ($ct>0) {
		$dyear = $match[1];
		if (($cyear-$dyear) <= $MAX_ALIVE_AGE-25) {
			return false;
		}
	}

	//-- check marriage records
	$famids = find_families_in_record($indirec, "FAMS");
	foreach($famids as $indexval => $famid) {
		$famrec = find_family_record($famid, $ged_id);
		//-- check death record
		$marrrec = get_sub_record(1, "1 MARR", $indirec);
		$ct = preg_match("/2 DATE .*(\d\d\d\d).*/", $marrrec, $match);
		if ($ct>0) {
			$myear = $match[1];
			if (($cyear-$myear) <= $MAX_ALIVE_AGE-15) {
				return false;
			}
		}
	}

	//-- check birth record
	$birtrec = get_sub_record(1, "1 BIRT", $indirec);
	$ct = preg_match("/2 DATE .*(\d\d\d\d).*/", $birtrec, $match);
	if ($ct>0) {
		$byear = $match[1];
		if (($cyear-$byear) <= $MAX_ALIVE_AGE) {
			return false;
		}
	}

	return true;
}


/**
* check if details for a GEDCOM XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.
*
* @author yalnifj
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings for
* @param string $type the GEDCOM type represented by the $pid.  This setting is used so that
* different gedcom types can be handled slightly different. (ie. a source cannot be dead)
* The possible values of $type are:
* - "INDI" record is an individual
* - "FAM" record is a family
* - "SOUR" record is a source
*          - "REPO" record is a repository
* @return boolean return true to show the persons details, return false to keep them private
*/
function displayDetailsById($pid, $type='', $gedrec='') {
	global $USE_RELATIONSHIP_PRIVACY, $CHECK_MARRIAGE_RELATIONS, $MAX_RELATION_PATH_LENGTH;
	global $person_privacy, $person_facts, $global_facts, $HIDE_LIVE_PEOPLE, $GEDCOM, $SHOW_DEAD_PEOPLE, $MAX_ALIVE_AGE, $PRIVACY_BY_YEAR;
	global $PRIVACY_CHECKS, $SHOW_LIVING_NAMES;

	$ged_id=get_id_from_gedcom($GEDCOM);

	if ($_SESSION["wt_user"]==WT_USER_ID) {
		// Normal operation
		$pgv_GEDCOM            = WT_GEDCOM;
		$pgv_GED_ID            = WT_GED_ID;
		$pgv_USER_ID           = WT_USER_ID;
		$pgv_USER_NAME         = WT_USER_NAME;
		$pgv_USER_GEDCOM_ADMIN = WT_USER_GEDCOM_ADMIN;
		$pgv_USER_CAN_ACCESS   = WT_USER_CAN_ACCESS;
		$pgv_USER_ACCESS_LEVEL = WT_USER_ACCESS_LEVEL;
		$pgv_USER_GEDCOM_ID    = WT_USER_GEDCOM_ID;
	} else {
		// We're in the middle of a Download -- get overriding information from cache
		$pgv_GEDCOM            = $_SESSION["pgv_GEDCOM"];
		$pgv_GED_ID            = $_SESSION["pgv_GED_ID"];
		$pgv_USER_ID           = $_SESSION["pgv_USER_ID"];
		$pgv_USER_NAME         = $_SESSION["pgv_USER_NAME"];
		$pgv_USER_GEDCOM_ADMIN = $_SESSION["pgv_USER_GEDCOM_ADMIN"];
		$pgv_USER_CAN_ACCESS   = $_SESSION["pgv_USER_CAN_ACCESS"];
		$pgv_USER_ACCESS_LEVEL = $_SESSION["pgv_USER_ACCESS_LEVEL"];
		$pgv_USER_GEDCOM_ID    = $_SESSION["pgv_USER_GEDCOM_ID"];
	}

	// Missing data or broken link?
	if (!$pid) {
		return true;
	}

	//-- keep a count of how many times we have checked for privacy
	++$PRIVACY_CHECKS;

	// This setting would better be called "$ENABLE_PRIVACY"
	if (!$HIDE_LIVE_PEOPLE) {
		return true;
	}

	// We should always be able to see our own record
	if ($pid==$pgv_USER_GEDCOM_ID) {
		return true;
	}

	// Need to examine the raw gedcom record
	if (!$type) {
		$type=gedcom_record_type($pid, $ged_id);
	}
	if (!$gedrec) {
		switch ($type) {
		case 'INDI': $gedrec=find_person_record($pid, $ged_id); break;
		case 'FAM':  $gedrec=find_family_record($pid, $ged_id); break;
		case 'SOUR': $gedrec=find_source_record($pid, $ged_id); break;
		case 'OBJE': $gedrec=find_media_record ($pid, $ged_id); break;
		default:     $gedrec=find_other_record ($pid, $ged_id); break;
		}
	}

	// Does this record have a RESN?
	if (strpos($gedrec, "\n1 RESN none")) {
		return true;
	}
	if (strpos($gedrec, "\n1 RESN privacy")) {
		return WT_PRIV_USER>=$pgv_USER_ACCESS_LEVEL;
	}
	if (strpos($gedrec, "\n1 RESN confidential")) {
		return WT_PRIV_NONE>=$pgv_USER_ACCESS_LEVEL;
	}

	// Does this record have a default RESN?
	if (isset($person_privacy[$pid])) {
		return $person_privacy[$pid]>=$pgv_USER_ACCESS_LEVEL;
	}

	// Privacy rules do not apply to admins
	if ($pgv_USER_GEDCOM_ADMIN) {
		return true;
	}

	// Different types of record have different privacy rules
	switch ($type) {
	case 'INDI':
		// Dead people...
		if (is_dead($gedrec) && $SHOW_DEAD_PEOPLE>=$pgv_USER_ACCESS_LEVEL) {
			if (!$PRIVACY_BY_YEAR || checkPrivacyByYear($pid)) {
				return true;
			}
		}
		// Consider relationship privacy
		if ($pgv_USER_GEDCOM_ID && get_user_setting($pgv_USER_ID, 'relationship_privacy', $USE_RELATIONSHIP_PRIVACY)) {
			$path_length=get_user_setting($pgv_USER_ID, 'max_relation_path', $MAX_RELATION_PATH_LENGTH);
			$relationship=get_relationship($pgv_USER_GEDCOM_ID, $pid, $CHECK_MARRIAGE_RELATIONS, $path_length);
			return $relationship!==false;
		}
		// No restriction found - show living people to authenticated users only:
		return WT_PRIV_USER>=$pgv_USER_ACCESS_LEVEL;
	case 'FAM':
		// Hide a family if either spouse is private
		$parents=find_parents($pid);
		return displayDetailsById($parents["HUSB"]) && displayDetailsById($parents["WIFE"]);
	case 'OBJE':
		// Hide media objects that are linked to private records
		foreach (get_media_relations($pid) as $gid=>$type2) {
			if (!displayDetailsById($gid, $type2)) {
				return false;
			}
		}
		break;
	case 'SOUR':
		// Hide sources if they are attached to private repositories.
		$repoid = get_gedcom_value("REPO", 1, find_source_record($pid, $ged_id));
		if (!displayDetailsById($repoid, "REPO")) {
			return false;
		}
		break;
	}

	// Level 1 tags (except INDI and FAM) can be controlled by global tag settings
	if (isset($global_facts[$type])) {
		return $global_facts[$type]>=$pgv_USER_ACCESS_LEVEL;
	}
	
	// No restriction found - must be public:
	return true;
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("showLivingNameById")) {
/**
* check if the name for a GEDCOM XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.  It first checks the
* <var>$SHOW_LIVING_NAMES</var> variable to see if names are shown to the public.  If they are
* then this function will always return true.  If the name is hidden then all relationships
* connected with the individual are also hidden such that arriving at this record results in a dead
* end.
*
* @author yalnifj
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings for
* @return boolean return true to show the person's name, return false to keep it private
*/
function showLivingNameById($pid) {
	global $SHOW_LIVING_NAMES;

	if ($_SESSION["wt_user"]==WT_USER_ID) {
		// Normal operation
		$pgv_USER_ACCESS_LEVEL = WT_USER_ACCESS_LEVEL;
	} else {
		// We're in the middle of a Download -- get overriding information from cache
		$pgv_USER_ACCESS_LEVEL = $_SESSION["pgv_USER_ACCESS_LEVEL"];
	}

	return $SHOW_LIVING_NAMES>=$pgv_USER_ACCESS_LEVEL || displayDetailsById($pid, 'INDI');
}
}

//-- allow users to overide functions in privacy file
if (!function_exists("showFact")) {
/**
* check if the given GEDCOM fact for the given individual, family, or source XRef ID should be shown
*
* This function uses the settings in the global variables above to determine if the current user
* has sufficient privileges to access the GEDCOM resource.  It first checks the $global_facts array
* for admin override settings for the fact.
*
* @author yalnifj
* @param string $fact the GEDCOM fact tag to check the privacy settings
* @param string $pid the GEDCOM XRef ID for the entity to check privacy settings
* @return boolean return true to show the fact, return false to keep it private
*/
function showFact($fact, $pid, $type='INDI') {
	global $global_facts, $person_facts;

	if ($_SESSION["wt_user"]==WT_USER_ID) {
		// Normal operation
		$pgv_USER_ACCESS_LEVEL	= WT_USER_ACCESS_LEVEL;
	} else {
		// We're in the middle of a Download -- get overriding information from cache
		$pgv_USER_ACCESS_LEVEL	= $_SESSION["pgv_USER_ACCESS_LEVEL"];
	}

	//-- check the person facts array
	if (isset($person_facts[$pid][$fact])) {
		return $pgv_USER_ACCESS_LEVEL>=$person_facts[$pid][$fact];
	}

	//-- check the global facts array
	if (isset($global_facts[$fact])) {
		return $pgv_USER_ACCESS_LEVEL>=$global_facts[$fact];
	}

	if ($fact!="NAME") {
		return displayDetailsById($pid, $type);
	} else {
		if (!displayDetailsById($pid, $type))
			return showLivingNameById($pid);
		else
			return true;
	}
}
}

/**
* remove all private information from a gedcom record
*
* this function will analyze and gedcom record and privatize it by removing all private
* information that should be hidden from the user trying to access it.
* @param string $gedrec the raw gedcom record to privatize
* @return string the privatized gedcom record
*/
function privatize_gedcom($gedrec) {
	global $SHOW_PRIVATE_RELATIONSHIPS, $pgv_private_records;
	global $global_facts, $person_facts;

	if (preg_match('/^0 @('.WT_REGEX_XREF.')@ ('.WT_REGEX_TAG.')(.*)/', $gedrec, $match)) {
		$gid = $match[1];
		$type = $match[2];
		$data = $match[3];
		if (displayDetailsById($gid, $type)) {
			// The record is not private, but the individual facts may be.
			if (
				!strpos($gedrec, "\n2 RESN") &&
				!isset($person_facts[$gid]) &&
				!preg_match('/\n1 (?:'.implode('|', array_keys($global_facts)).')/', $gedrec) &&
				!preg_match('/\n2 TYPE (?:'.implode('|', array_keys($global_facts)).')/', $gedrec)
			) {
				// Nothing to indicate fact privacy needed
				return $gedrec;
			}

			$newrec="0 @{$gid}@ {$type}{$data}";
			$private_record='';
			// Check each of the sub facts for access
			if (preg_match_all('/\n1 ('.WT_REGEX_TAG.').*(?:\n[2-9].*)*/', $gedrec, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					if (($match[1]=='FACT' || $match[1]=='EVEN') && preg_match('/\n2 TYPE ([A-Z]{3,5})/', $match[0], $tmatch)) {
						$tag=$tmatch[1];
					} else {
						$tag=$match[1];
					}
					if (!FactViewRestricted($gid, $match[0]) && showFact($tag, $gid)) {
						$newrec.=$match[0];
					} else {
						$private_record.=$match[0];
					}
				}
			}
			// Store the private data, so we can add it back in after an edit.
			$pgv_private_records[$gid]=$private_record;
			return $newrec;
		} else {
			// The whole record is private - although there are a few things we need to show.
			switch($type) {
			case 'INDI':
				$newrec="0 @{$gid}@ INDI";
				if (showLivingNameById($gid)) {
					// Show all the NAME tags, including subtags
					if (preg_match_all('/\n1 (NAME|_HNM).*(\n[2-9].*)*/', $gedrec, $matches, PREG_SET_ORDER)) {
						foreach ($matches as $match) {
							$newrec.=$match[0];
						}
					}
				} else {
					$newrec.="\n1 NAME ".i18n::translate('Private');
				}
				// Just show the 1 FAMC/FAMS tag, not any subtags, which may contain private data
				if (preg_match_all('/\n1 FAM[CS] @('.WT_REGEX_XREF.')@/', $gedrec, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						if ($SHOW_PRIVATE_RELATIONSHIPS || displayDetailsById($match[1], 'FAM')) {
							$newrec.=$match[0];
						}
					}
				}
				// Don't privatize sex
				if (preg_match('/\n1 SEX [MFU]/', $gedrec, $match)) {
					$newrec.=$match[0];
				}
				$newrec .= "\n1 NOTE ".i18n::translate('Details about this person are private. Personal details will not be included.');
				break;
			case 'FAM':
				$newrec="0 @{$gid}@ FAM";
				// Just show the 1 CHIL/HUSB/WIFE tag, not any subtags, which may contain private data
				if (preg_match_all('/\n1 (CHIL|HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $gedrec, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						if ($SHOW_PRIVATE_RELATIONSHIPS || displayDetailsById($match[1], 'INDI')) {
							$newrec.=$match[0];
						}
					}
				}
				$newrec .= "\n1 NOTE ".i18n::translate('Details about this family are private. Family details will not be included.');
				break;
			case 'SOUR':
				$newrec="0 @{$gid}@ SOUR\n1 TITL ".i18n::translate('Private');
				break;
			case 'OBJE':
				$newrec="0 @{$gid}@ OBJE\n1 NOTE ".i18n::translate('Details about this media are private. Media details will not be included.');
				break;
			default:
				$newrec="0 @{$gid}@ {$type}\n1 NOTE ".i18n::translate('Private');
			}
			return $newrec;
		}
	} else {
		// Invalid gedcom record, so nothing to privatize.
		return $gedrec;
	}
}

function get_last_private_data($gid) {
	global $pgv_private_records;

	if (!isset($pgv_private_records[$gid])) return false;
	return $pgv_private_records[$gid];
}

/**
* Check fact record for editing restrictions
*
* Checks if the user is allowed to change fact information,
* based on the existence of the RESN tag in the fact record.
*
* @return int Allowed or not allowed
*/
function FactEditRestricted($pid, $factrec) {
	if (WT_USER_GEDCOM_ADMIN) {
		return false;
	}

	if (preg_match("/2 RESN (.*)/", $factrec, $match)) {
		$match[1] = strtolower(trim($match[1]));
		if ($match[1] == "privacy" || $match[1]=="locked") {
			$myindi=WT_USER_GEDCOM_ID;
			if ($myindi == $pid) {
				return false;
			}
			if (gedcom_record_type($pid, WT_GED_ID)=='FAM') {
				$famrec = find_family_record($pid, WT_GED_ID);
				$parents = find_parents_in_record($famrec);
				if ($myindi == $parents["HUSB"] || $myindi == $parents["WIFE"]) {
					return false;
				}
			}
			return true;
		}
	}
	return false;
}

/**
* Check fact record for viewing restrictions
*
* Checks if the user is allowed to view fact information,
* based on the existence of the RESN tag in the fact record.
*
* @return int Allowed or not allowed
*/
function FactViewRestricted($pid, $factrec) {
	if ($_SESSION['wt_user']==WT_USER_ID) {
		// Normal operation
		$pgv_GED_ID				= WT_GED_ID;
		$pgv_USER_GEDCOM_ADMIN	= WT_USER_GEDCOM_ADMIN;
		$pgv_USER_GEDCOM_ID		= WT_USER_GEDCOM_ID;
	} else {
		// We're in the middle of a Download -- get overriding information from cache
		$pgv_GED_ID           =$_SESSION['pgv_GED_ID'];
		$pgv_USER_GEDCOM_ADMIN=$_SESSION['pgv_USER_GEDCOM_ADMIN'];
		$pgv_USER_GEDCOM_ID   =$_SESSION['pgv_USER_GEDCOM_ID'];
	}

	if ($pgv_USER_GEDCOM_ADMIN) {
		return false;
	}

	if (preg_match('/2 RESN (.*)/', $factrec, $match)) {
		$match[1] = strtolower(trim($match[1]));
		if ($match[1] == 'confidential') {
			return true;
		}
		if ($match[1] == 'privacy') {
			$myindi=$pgv_USER_GEDCOM_ID;
			if ($myindi == $pid) {
				return false;
			}
			if (gedcom_record_type($pid, $pgv_GED_ID)=='FAM') {
				$famrec = find_family_record($pid, $pgv_GED_ID);
				$parents = find_parents_in_record($famrec);
				if ($myindi == $parents['WIFE'] || $myindi == $parents['HUSB']) {
					return false;
				}
			}
			return true;
		}
	}
	return false;
}

?>
