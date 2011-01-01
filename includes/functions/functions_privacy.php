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
function is_dead($indirec, $gedcom_id) {
	global $MAX_ALIVE_AGE;

	if (preg_match('/^0 @('.WT_REGEX_XREF.')@ INDI/', $indirec, $match)) {
		$pid=$match[1];
	} else {
		return false;
	}

	// "1 DEAT Y" or "1 DEAT/2 DATE" or "1 DEAT/2 PLAC"
	if (preg_match('/\n1 (?:'.WT_EVENTS_DEAT.')(?: Y|(?:\n[2-9].+)*\n2 (DATE|PLAC) )/', $indirec)) {
		return true;
	}

	// If any event occured more than $MAX_ALIVE_AGE years ago, then assume the person is dead
	preg_match_all('/\n2 DATE (.+)/', $indirec, $date_matches);
	foreach ($date_matches[1] as $date_match) {
		$date=new GedcomDate($date_match);
		if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*$MAX_ALIVE_AGE) {
			return true;
		}
	}

	// If we found no dates then check the dates of close relatives.
	// Check parents (birth and adopted)
	preg_match_all('/\n1 FAMC @('.WT_REGEX_XREF.')@/', $indirec, $famc_matches);
	foreach ($famc_matches[1] as $famc_match) {
		$famrec=find_family_record($famc_match, $gedcom_id);
		$parents=find_parents_in_record($famrec);
		if (!empty($parents['HUSB'])) {
			preg_match_all('/\n2 DATE (.+)/', find_person_record($parents['HUSB'], $gedcom_id), $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date=new GedcomDate($date_match);
				// Assume fathers are no more than 40 years older than their children
				if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE+40)) {
					return true;
				}
			}
		}
		if (!empty($parents['WIFE'])) {
			preg_match_all('/\n2 DATE (.+)/', find_person_record($parents['WIFE'], $gedcom_id), $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date=new GedcomDate($date_match);
				// Assume mothers are no more than 40 years older than their children
				if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE+40)) {
					return true;
				}
			}
		}
	}

	// Check spouses
	preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $indirec, $fams_matches);
	foreach ($fams_matches[1] as $fams_match) {
		$famrec=find_family_record($fams_match, $gedcom_id);
		// Check all marriage events
		preg_match_all('/\n1 (?:'.WT_EVENTS_MARR.')(?:\n[2-9].+)*\n2 DATE (.+)/', $indirec, $date_matches);
		foreach ($date_matches[1] as $date_match) {
			$date=new GedcomDate($date_match);
			// Assume marriage occurs after age of 10
			if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE-10)) {
				return true;
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
			preg_match_all('/\n2 DATE (.+)/', find_person_record($spid, $gedcom_id), $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date=new GedcomDate($date_match);
				// Assume max age difference between spouses of 40 years
				if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE+40)) {
					return true;
				}
			}
		}
		// Check child dates
		preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', $famrec, $chil_matches);
		foreach ($chil_matches[1] as $chil_match) {
			$childrec=find_person_record($chil_match, $gedcom_id);
			preg_match_all('/\n2 DATE (.+)/', $childrec, $date_matches);
			// Assume children born after age of 15
			foreach ($date_matches[1] as $date_match) {
				$date=new GedcomDate($date_match);
				if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE-15)) {
					return true;
				}
			}
			// Check grandchildren
			preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $childrec, $fams2_matches);
			foreach ($fams2_matches[1] as $fams2_match) {
				preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', find_family_record($fams2_match, $gedcom_id), $chil2_matches);
				foreach ($chil2_matches[1] as $chil2_match) {
					$grandchildrec=find_person_record($chil2_match, $gedcom_id);
					preg_match_all('/\n2 DATE (.+)/', $grandchildrec, $date_matches);
					// Assume grandchildren born after age of 30
					foreach ($date_matches[1] as $date_match) {
						$date=new GedcomDate($date_match);
						if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE-30)) {
							return true;
						}
					}
				}
			}
		}
	}
	return false;
}

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
		$pgv_GED_ID            = WT_GED_ID;
		$pgv_USER_ACCESS_LEVEL = WT_USER_ACCESS_LEVEL;
	} else {
		// We're in the middle of a Download -- get overriding information from cache
		$pgv_GED_ID            = $_SESSION["pgv_GED_ID"];
		$pgv_USER_ACCESS_LEVEL = $_SESSION["pgv_USER_ACCESS_LEVEL"];
	}

	return $SHOW_LIVING_NAMES>=$pgv_USER_ACCESS_LEVEL || canDisplayRecord($pgv_GED_ID, find_person_record($pid, $pgv_GED_ID));
}


// Can we display a level 0 record?
function canDisplayRecord($ged_id, $gedrec) {
	// TODO - use the privacy settings for $ged_id, not the default gedcom.
	global $person_privacy, $person_facts, $global_facts, $HIDE_LIVE_PEOPLE, $GEDCOM, $SHOW_DEAD_PEOPLE, $MAX_ALIVE_AGE;
	global $PRIVACY_CHECKS, $SHOW_LIVING_NAMES, $KEEP_ALIVE_YEARS_BIRTH, $KEEP_ALIVE_YEARS_DEATH;

	// Only need to check each record once.
	static $cache; if ($cache===null) {$cache=array();}

	if ($_SESSION["wt_user"]==WT_USER_ID) {
		// Normal operation
		$pgv_GED_ID            = WT_GED_ID;
		$pgv_USER_ACCESS_LEVEL = WT_USER_ACCESS_LEVEL;
		$pgv_USER_GEDCOM_ID    = WT_USER_GEDCOM_ID;
	} else {
		// We're in the middle of a Download -- get overriding information from cache
		$pgv_GED_ID            = $_SESSION["pgv_GED_ID"];
		$pgv_USER_ACCESS_LEVEL = $_SESSION["pgv_USER_ACCESS_LEVEL"];
		$pgv_USER_GEDCOM_ID    = 0; // dummy users do not have an associated gedcom record
	}

	if (preg_match('/^0 @('.WT_REGEX_XREF.')@ ('.WT_REGEX_TAG.')/', $gedrec, $match)) {
		$xref=$match[1];
		$type=$match[2];
		$cache_key="$xref@$ged_id";
		if (array_key_exists($cache_key, $cache)) {
			return $cache[$cache_key];
		}
	} else {
		// Missing data or broken link?
		return true;
	}

	//-- keep a count of how many times we have checked for privacy
	++$PRIVACY_CHECKS;

	// This setting would better be called "$ENABLE_PRIVACY"
	if (!$HIDE_LIVE_PEOPLE) {
		return $cache[$cache_key]=true;
	}

	// We should always be able to see our own record
	if ($xref==WT_USER_GEDCOM_ID && $ged_id=WT_GED_ID) {
		return $cache[$cache_key]=true;
	}

	// Does this record have a RESN?
	if (strpos($gedrec, "\n1 RESN confidential")) {
		return $cache[$cache_key]=(WT_PRIV_NONE>=$pgv_USER_ACCESS_LEVEL);
	}
	if (strpos($gedrec, "\n1 RESN privacy")) {
		return $cache[$cache_key]=(WT_PRIV_USER>=$pgv_USER_ACCESS_LEVEL);
	}
	if (strpos($gedrec, "\n1 RESN none")) {
		return $cache[$cache_key]=true;
	}

	// Does this record have a default RESN?
	if (isset($person_privacy[$xref])) {
		return $cache[$cache_key]=($person_privacy[$xref]>=$pgv_USER_ACCESS_LEVEL);
	}

	// Privacy rules do not apply to admins
	if (WT_PRIV_NONE>=$pgv_USER_ACCESS_LEVEL) {
		return $cache[$cache_key]=true;
	}

	// Different types of record have different privacy rules
	switch ($type) {
	case 'INDI':
		// Dead people...
		if ($SHOW_DEAD_PEOPLE>=$pgv_USER_ACCESS_LEVEL && is_dead($gedrec, $ged_id)) {
			$keep_alive=false;
			if ($KEEP_ALIVE_YEARS_BIRTH) {
				preg_match_all('/\n1 (?:'.WT_EVENTS_BIRT.').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $gedrec, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$date=new GedcomDate($match[1]);
					if ($date->isOK() && $date->gregorianYear()+$KEEP_ALIVE_YEARS_BIRTH > date('Y')) {
						$keep_alive=true;
						break;
					}
				}
			}
			if ($KEEP_ALIVE_YEARS_DEATH) {
				preg_match_all('/\n1 (?:'.WT_EVENTS_DEAT.').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $gedrec, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$date=new GedcomDate($match[1]);
					if ($date->isOK() && $date->gregorianYear()+$KEEP_ALIVE_YEARS_DEATH > date('Y')) {
						$keep_alive=true;
						break;
					}
				}
			}
			if (!$keep_alive) {
				return $cache[$cache_key]=true;
			}
		}
		// Consider relationship privacy
		if ($pgv_USER_GEDCOM_ID && WT_USER_PATH_LENGTH) {
			$relationship=get_relationship($pgv_USER_GEDCOM_ID, $xref, true, WT_USER_PATH_LENGTH);
			return $cache[$cache_key]=($relationship!==false);
		}
		// No restriction found - show living people to members only:
		return WT_PRIV_USER>=$pgv_USER_ACCESS_LEVEL;
	case 'FAM':
		// Hide a family if either spouse is private
		if (preg_match_all('/\n1 (?:HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $gedrec, $matches)) {
			foreach ($matches[1] as $spouse_id) {
				if (!canDisplayRecord($ged_id, find_person_record($spouse_id, $ged_id))) {
					return $cache[$cache_key]=false;
				}
			}
		}
		return $cache[$cache_key]=true;
	case 'OBJE':
		// Hide media objects that are linked to private records
		foreach (get_media_relations($xref) as $gid=>$type2) {
			if (!canDisplayRecord($ged_id, find_gedcom_record($gid, $ged_id))) {
				return $cache[$cache_key]=false;
			}
		}
		break;
	case 'SOUR':
		// Hide sources if they are attached to private repositories.
		$repoid = get_gedcom_value("REPO", 1, $gedrec);
		if ($repoid && !canDisplayRecord($ged_id, find_other_record($repoid, $ged_id))) {
			return $cache[$cache_key]=false;
		}
		break;
	case 'NOTE':
		// Hide notes if they are attached to private records
		$linked_gids=WT_DB::prepare(
			"SELECT l_from FROM `##link` WHERE l_to=? AND l_file=?"
		)->execute(array($xref, $ged_id))->fetchOneColumn();
		foreach ($linked_gids as $linked_gid) {
			$linked_record=WT_GedcomRecord::getInstance($linked_gid);
			if (!$linked_record->canDisplayDetails()) {
				return $cache[$cache_key]=false;
			}
		}
	}

	// Level 0 tags (except INDI and FAM) can be controlled by global tag settings
	if (isset($global_facts[$type])) {
		return $cache[$cache_key]=($global_facts[$type]>=$pgv_USER_ACCESS_LEVEL);
	}

	// No restriction found - must be public:
	return $cache[$cache_key]=true;
}

// Can we display a level 1 record?
// Assume we have already called canDisplayRecord() to check the parent level 0 object
function canDisplayFact($xref, $ged_id, $gedrec) {
	// TODO - use the privacy settings for $ged_id, not the default gedcom.
	global $HIDE_LIVE_PEOPLE, $person_facts, $global_facts;

	// This setting would better be called "$ENABLE_PRIVACY"
	if (!$HIDE_LIVE_PEOPLE) {
		return true;
	}
	// We should always be able to see details of our own record
	if ($xref==WT_USER_GEDCOM_ID && $ged_id=WT_GED_ID) {
		return true;
	}

	// Does this record have a RESN?
	if (strpos($gedrec, "\n2 RESN confidential")) {
		return WT_PRIV_NONE>=WT_USER_ACCESS_LEVEL;
	}
	if (strpos($gedrec, "\n2 RESN privacy")) {
		return WT_PRIV_USER>=WT_USER_ACCESS_LEVEL;
	}
	if (strpos($gedrec, "\n2 RESN none")) {
		return true;
	}

	// Does this record have a default RESN?
	if (preg_match('/^1 ('.WT_REGEX_TAG.')/', $gedrec, $match)) {
		$tag=$match[1];
		if (isset($person_facts[$xref][$tag])) {
			return $person_facts[$xref][$tag]>=WT_USER_ACCESS_LEVEL;
		}
		if (isset($global_facts[$tag])) {
			return $global_facts[$tag]>=WT_USER_ACCESS_LEVEL;
		}
	}

	// No restrictions - it must be public
	return true;
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
	global $global_facts, $person_facts, $GEDCOM;
	$gedcom_id=get_id_from_gedcom($GEDCOM);

	if (preg_match('/^0 @('.WT_REGEX_XREF.')@ ('.WT_REGEX_TAG.')(.*)/', $gedrec, $match)) {
		$gid = $match[1];
		$type = $match[2];
		$data = $match[3];
		if (canDisplayRecord($gedcom_id, $gedrec)) {
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
					if (canDisplayFact($gid, $gedcom_id, $match[0])) {
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
						if ($SHOW_PRIVATE_RELATIONSHIPS || canDisplayRecord($gedcom_id, find_family_record($match[1], $gedcom_id))) {
							$newrec.=$match[0];
						}
					}
				}
				// Don't privatize sex
				if (preg_match('/\n1 SEX [MFU]/', $gedrec, $match)) {
					$newrec.=$match[0];
				}
				break;
			case 'FAM':
				$newrec="0 @{$gid}@ FAM";
				// Just show the 1 CHIL/HUSB/WIFE tag, not any subtags, which may contain private data
				if (preg_match_all('/\n1 (CHIL|HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $gedrec, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						if ($SHOW_PRIVATE_RELATIONSHIPS || canDisplayRecord($gedcom_id, find_person_record($match[1], $gedcom_id))) {
							$newrec.=$match[0];
						}
					}
				}
				break;
			case 'NOTE':
				$newrec="0 @{$gid}@ {$type} ".i18n::translate('Private');
				break;
			case 'SOUR':
				$newrec="0 @{$gid}@ {$type}\n1 TITL ".i18n::translate('Private');
				break;
			case 'REPO':
			case 'SUBM':
				$newrec="0 @{$gid}@ {$type}\n1 NAME ".i18n::translate('Private');
				break;
			case 'SUBN':
				$newrec="0 @{$gid}@ {$type}\n1 FAMF ".i18n::translate('Private');
				break;
			case 'OBJE':
			default:
				// Other objects have no name/title, so add an inline note
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
