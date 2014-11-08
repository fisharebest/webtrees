<?php
// Core Functions
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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
 * Check with the webtrees.net server for the latest version of webtrees.
 * Fetching the remote file can be slow, so check infrequently, and cache the result.
 * Pass the current versions of webtrees, PHP and MySQL, as the response
 * may be different for each.  The server logs are used to generate
 * installation statistics which can be found at http://svn.webtrees.net/statistics.html
 *
 * @return null|string
 */
function fetch_latest_version() {
	$last_update_timestamp = WT_Site::getPreference('LATEST_WT_VERSION_TIMESTAMP');
	if ($last_update_timestamp < WT_TIMESTAMP - 24 * 60 * 60) {
		$row = WT_DB::prepare("SHOW VARIABLES LIKE 'version'")->fetchOneRow();
		$params = '?w=' . WT_VERSION . '&p=' . PHP_VERSION . '&m=' . $row->value . '&o=' . (DIRECTORY_SEPARATOR === '/' ? 'u' : 'w');
		$latest_version_txt = WT_File::fetchUrl('http://svn.webtrees.net/build/latest-version.txt' . $params);
		if ($latest_version_txt) {
			WT_Site::setPreference('LATEST_WT_VERSION', $latest_version_txt);
			WT_Site::setPreference('LATEST_WT_VERSION_TIMESTAMP', WT_TIMESTAMP);

			return $latest_version_txt;
		} else {
			// Cannot connect to server - use cached version (if we have one)
			return WT_Site::getPreference('LATEST_WT_VERSION');
		}
	} else {
		return WT_Site::getPreference('LATEST_WT_VERSION');
	}
}

/**
 * Convert a file upload PHP error code into user-friendly text.
 *
 * @param integer $error_code
 *
 * @return string
 */
function file_upload_error_text($error_code) {
	switch ($error_code) {
	case UPLOAD_ERR_OK:
		return WT_I18N::translate('File successfully uploaded');
	case UPLOAD_ERR_INI_SIZE:
	case UPLOAD_ERR_FORM_SIZE:
		// I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
		return WT_I18N::translate('Uploaded file exceeds the allowed size');
	case UPLOAD_ERR_PARTIAL:
		// I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
		return WT_I18N::translate('File was only partially uploaded, please try again');
	case UPLOAD_ERR_NO_FILE:
		// I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
		return WT_I18N::translate('No file was received.  Please upload again.');
	case UPLOAD_ERR_NO_TMP_DIR:
		// I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
		return WT_I18N::translate('Missing PHP temporary directory');
	case UPLOAD_ERR_CANT_WRITE:
		// I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
		return WT_I18N::translate('PHP failed to write to disk');
	case UPLOAD_ERR_EXTENSION:
		// I18N: PHP internal error message - php.net/manual/en/features.file-upload.errors.php
		return WT_I18N::translate('PHP blocked file by extension');
	default:
		return 'Error: ' . $error_code;
	}
}

/**
 * Load the configuration settings into global scope
 *
 * @todo some of these are used infrequently - just load them when we need them
 *
 * @param integer $ged_id
 */
function load_gedcom_settings($ged_id) {
	$tree = WT_Tree::get($ged_id);
	global $ADVANCED_NAME_FACTS;          $ADVANCED_NAME_FACTS          = $tree->getPreference('ADVANCED_NAME_FACTS');
	global $ADVANCED_PLAC_FACTS;          $ADVANCED_PLAC_FACTS          = $tree->getPreference('ADVANCED_PLAC_FACTS');
	global $CALENDAR_FORMAT;              $CALENDAR_FORMAT              = $tree->getPreference('CALENDAR_FORMAT');
	global $CHART_BOX_TAGS;               $CHART_BOX_TAGS               = $tree->getPreference('CHART_BOX_TAGS');
	global $CONTACT_USER_ID;              $CONTACT_USER_ID              = $tree->getPreference('CONTACT_USER_ID');
	global $DEFAULT_PEDIGREE_GENERATIONS; $DEFAULT_PEDIGREE_GENERATIONS = $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS');
	global $EXPAND_NOTES;                 $EXPAND_NOTES                 = $tree->getPreference('EXPAND_NOTES');
	global $EXPAND_RELATIVES_EVENTS;      $EXPAND_RELATIVES_EVENTS      = $tree->getPreference('EXPAND_RELATIVES_EVENTS');
	global $EXPAND_SOURCES;               $EXPAND_SOURCES               = $tree->getPreference('EXPAND_SOURCES');
	global $FULL_SOURCES;                 $FULL_SOURCES                 = $tree->getPreference('FULL_SOURCES');
	global $GEDCOM_MEDIA_PATH;            $GEDCOM_MEDIA_PATH            = $tree->getPreference('GEDCOM_MEDIA_PATH');
	global $GENERATE_UIDS;                $GENERATE_UIDS                = $tree->getPreference('GENERATE_UIDS');
	global $HIDE_GEDCOM_ERRORS;           $HIDE_GEDCOM_ERRORS           = $tree->getPreference('HIDE_GEDCOM_ERRORS');
	global $HIDE_LIVE_PEOPLE;             $HIDE_LIVE_PEOPLE             = $tree->getPreference('HIDE_LIVE_PEOPLE');
	global $KEEP_ALIVE_YEARS_BIRTH;       $KEEP_ALIVE_YEARS_BIRTH       = $tree->getPreference('KEEP_ALIVE_YEARS_BIRTH');
	global $KEEP_ALIVE_YEARS_DEATH;       $KEEP_ALIVE_YEARS_DEATH       = $tree->getPreference('KEEP_ALIVE_YEARS_DEATH');
	global $LANGUAGE;                     $LANGUAGE                     = $tree->getPreference('LANGUAGE');
	global $MAX_ALIVE_AGE;                $MAX_ALIVE_AGE                = $tree->getPreference('MAX_ALIVE_AGE');
	global $MAX_DESCENDANCY_GENERATIONS;  $MAX_DESCENDANCY_GENERATIONS  = $tree->getPreference('MAX_DESCENDANCY_GENERATIONS');
	global $MAX_PEDIGREE_GENERATIONS;     $MAX_PEDIGREE_GENERATIONS     = $tree->getPreference('MAX_PEDIGREE_GENERATIONS');
	global $MEDIA_DIRECTORY;              $MEDIA_DIRECTORY              = $tree->getPreference('MEDIA_DIRECTORY');
	global $NO_UPDATE_CHAN;               $NO_UPDATE_CHAN               = $tree->getPreference('NO_UPDATE_CHAN');
	global $PEDIGREE_FULL_DETAILS;        $PEDIGREE_FULL_DETAILS        = $tree->getPreference('PEDIGREE_FULL_DETAILS');
	global $PEDIGREE_LAYOUT;              $PEDIGREE_LAYOUT              = $tree->getPreference('PEDIGREE_LAYOUT');
	global $PEDIGREE_SHOW_GENDER;         $PEDIGREE_SHOW_GENDER         = $tree->getPreference('PEDIGREE_SHOW_GENDER');
	global $PREFER_LEVEL2_SOURCES;        $PREFER_LEVEL2_SOURCES        = $tree->getPreference('PREFER_LEVEL2_SOURCES');
	global $QUICK_REQUIRED_FACTS;         $QUICK_REQUIRED_FACTS         = $tree->getPreference('QUICK_REQUIRED_FACTS');
	global $QUICK_REQUIRED_FAMFACTS;      $QUICK_REQUIRED_FAMFACTS      = $tree->getPreference('QUICK_REQUIRED_FAMFACTS');
	global $REQUIRE_AUTHENTICATION;       $REQUIRE_AUTHENTICATION       = $tree->getPreference('REQUIRE_AUTHENTICATION');
	global $SAVE_WATERMARK_IMAGE;         $SAVE_WATERMARK_IMAGE         = $tree->getPreference('SAVE_WATERMARK_IMAGE');
	global $SAVE_WATERMARK_THUMB;         $SAVE_WATERMARK_THUMB         = $tree->getPreference('SAVE_WATERMARK_THUMB');
	global $SHOW_AGE_DIFF;                $SHOW_AGE_DIFF                = $tree->getPreference('SHOW_AGE_DIFF');
	global $SHOW_DEAD_PEOPLE;             $SHOW_DEAD_PEOPLE             = $tree->getPreference('SHOW_DEAD_PEOPLE');
	global $SHOW_FACT_ICONS;              $SHOW_FACT_ICONS              = $tree->getPreference('SHOW_FACT_ICONS');
	global $SHOW_GEDCOM_RECORD;           $SHOW_GEDCOM_RECORD           = $tree->getPreference('SHOW_GEDCOM_RECORD');
	global $SHOW_HIGHLIGHT_IMAGES;        $SHOW_HIGHLIGHT_IMAGES        = $tree->getPreference('SHOW_HIGHLIGHT_IMAGES');
	global $SHOW_LAST_CHANGE;             $SHOW_LAST_CHANGE             = $tree->getPreference('SHOW_LAST_CHANGE');
	global $SHOW_LDS_AT_GLANCE;           $SHOW_LDS_AT_GLANCE           = $tree->getPreference('SHOW_LDS_AT_GLANCE');
	global $SHOW_LEVEL2_NOTES;            $SHOW_LEVEL2_NOTES            = $tree->getPreference('SHOW_LEVEL2_NOTES');
	global $SHOW_LIVING_NAMES;            $SHOW_LIVING_NAMES            = $tree->getPreference('SHOW_LIVING_NAMES');
	global $SHOW_MEDIA_DOWNLOAD;          $SHOW_MEDIA_DOWNLOAD          = $tree->getPreference('SHOW_MEDIA_DOWNLOAD');
	global $SHOW_NO_WATERMARK;            $SHOW_NO_WATERMARK            = $tree->getPreference('SHOW_NO_WATERMARK');
	global $SHOW_PARENTS_AGE;             $SHOW_PARENTS_AGE             = $tree->getPreference('SHOW_PARENTS_AGE');
	global $SHOW_PEDIGREE_PLACES;         $SHOW_PEDIGREE_PLACES         = $tree->getPreference('SHOW_PEDIGREE_PLACES');
	global $SHOW_PEDIGREE_PLACES_SUFFIX;  $SHOW_PEDIGREE_PLACES_SUFFIX  = $tree->getPreference('SHOW_PEDIGREE_PLACES_SUFFIX');
	global $SHOW_PRIVATE_RELATIONSHIPS;   $SHOW_PRIVATE_RELATIONSHIPS   = $tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');
	global $SHOW_RELATIVES_EVENTS;        $SHOW_RELATIVES_EVENTS        = $tree->getPreference('SHOW_RELATIVES_EVENTS');
	global $THUMBNAIL_WIDTH;              $THUMBNAIL_WIDTH              = $tree->getPreference('THUMBNAIL_WIDTH');
	global $USE_RIN;                      $USE_RIN                      = $tree->getPreference('USE_RIN');
	global $USE_SILHOUETTE;               $USE_SILHOUETTE               = $tree->getPreference('USE_SILHOUETTE');
	global $WATERMARK_THUMB;              $WATERMARK_THUMB              = $tree->getPreference('WATERMARK_THUMB');
	global $WEBMASTER_USER_ID;            $WEBMASTER_USER_ID            = $tree->getPreference('WEBMASTER_USER_ID');
	global $WEBTREES_EMAIL;               $WEBTREES_EMAIL               = $tree->getPreference('WEBTREES_EMAIL');
	global $WORD_WRAPPED_NOTES;           $WORD_WRAPPED_NOTES           = $tree->getPreference('WORD_WRAPPED_NOTES');

	global $person_privacy; $person_privacy=array();
	global $person_facts;   $person_facts  =array();
	global $global_facts;   $global_facts  =array();

	$rows = WT_DB::prepare(
		"SELECT SQL_CACHE xref, tag_type, CASE resn WHEN 'none' THEN ? WHEN 'privacy' THEN ? WHEN 'confidential' THEN ? WHEN 'hidden' THEN ? END AS resn FROM `##default_resn` WHERE gedcom_id=?"
	)->execute(array(WT_PRIV_PUBLIC, WT_PRIV_USER, WT_PRIV_NONE, WT_PRIV_HIDE, $ged_id))->fetchAll();

	foreach ($rows as $row) {
		if ($row->xref !== null) {
			if ($row->tag_type !== null) {
				$person_facts[$row->xref][$row->tag_type] = (int)$row->resn;
			} else {
				$person_privacy[$row->xref] = (int)$row->resn;
			}
		} else {
			$global_facts[$row->tag_type] = (int)$row->resn;
		}
	}
}

/**
 * get a gedcom subrecord
 *
 * searches a gedcom record and returns a subrecord of it.  A subrecord is defined starting at a
 * line with level N and all subsequent lines greater than N until the next N level is reached.
 * For example, the following is a BIRT subrecord:
 * <code>1 BIRT
 * 2 DATE 1 JAN 1900
 * 2 PLAC Phoenix, Maricopa, Arizona</code>
 * The following example is the DATE subrecord of the above BIRT subrecord:
 * <code>2 DATE 1 JAN 1900</code>
 *
 * @param integer $level  the N level of the subrecord to get
 * @param string  $tag    a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
 * @param string  $gedrec the parent gedcom record to search in
 * @param integer $num    this allows you to specify which matching <var>$tag</var> to get.  Oftentimes a
 *                        gedcom record will have more that 1 of the same type of subrecord.  An individual may have
 *                        multiple events for example.  Passing $num=1 would get the first 1.  Passing $num=2 would get the
 *                        second one, etc.
 *
 * @return string the subrecord that was found or an empty string "" if not found.
 */
function get_sub_record($level, $tag, $gedrec, $num = 1) {
	if (empty($gedrec)) {
		return '';
	}
	// -- adding \n before and after gedrec
	$gedrec = "\n" . $gedrec . "\n";
	$tag = trim($tag);
	$searchTarget = "~[\n]" . $tag . "[\s]~";
	$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
	if ($ct == 0) {
		return '';
	}
	if ($ct < $num) {
		return '';
	}
	$pos1 = $match[$num - 1][0][1];
	$pos2 = strpos($gedrec, "\n$level", $pos1 + 1);
	if (!$pos2) {
		$pos2 = strpos($gedrec, "\n1", $pos1 + 1);
	}
	if (!$pos2) {
		$pos2 = strpos($gedrec, "\nWT_", $pos1 + 1); // WT_SPOUSE, WT_FAMILY_ID ...
	}
	if (!$pos2) {
		return ltrim(substr($gedrec, $pos1));
	}
	$subrec = substr($gedrec, $pos1, $pos2 - $pos1);

	return ltrim($subrec);
}

/**
 * get CONT lines
 *
 * get the N+1 CONT or CONC lines of a gedcom subrecord
 *
 * @param integer $nlevel the level of the CONT lines to get
 * @param string  $nrec   the gedcom subrecord to search in
 *
 * @return string a string with all CONT or CONC lines merged
 */
function get_cont($nlevel, $nrec) {
	global $WORD_WRAPPED_NOTES;
	$text = "";

	$subrecords = explode("\n", $nrec);
	foreach ($subrecords as $thisSubrecord) {
		if (substr($thisSubrecord, 0, 2) !== $nlevel . " ") {
			continue;
		}
		$subrecordType = substr($thisSubrecord, 2, 4);
		if ($subrecordType == "CONT") {
			$text .= "\n";
		}
		if ($subrecordType == "CONC" && $WORD_WRAPPED_NOTES) {
			$text .= " ";
		}
		if ($subrecordType == "CONT" || $subrecordType == "CONC") {
			$text .= rtrim(substr($thisSubrecord, 7));
		}
	}

	return rtrim($text, " ");
}

/**
 * Sort a list events for the today/upcoming blocks
 *
 * @param array $a
 * @param array $b
 *
 * @return integer
 */
function event_sort($a, $b) {
	if ($a['jd'] == $b['jd']) {
		if ($a['anniv'] == $b['anniv']) {
			return WT_I18N::strcasecmp($a['fact'], $b['fact']);
		} else {
			return $a['anniv'] - $b['anniv'];
		}
	} else {
		return $a['jd'] - $b['jd'];
	}
}

/**
 * Sort a list events for the today/upcoming blocks
 *
 * @param array $a
 * @param array $b
 *
 * @return integer
 */
function event_sort_name($a, $b) {
	if ($a['jd'] == $b['jd']) {
		return WT_GedcomRecord::compare($a['record'], $b['record']);
	} else {
		return $a['jd'] - $b['jd'];
	}
}

/**
 * A multi-key sort
 * 1. First divide the facts into two arrays one set with dates and one set without dates
 * 2. Sort each of the two new arrays, the date using the compare date function, the non-dated
 * using the compare type function
 * 3. Then merge the arrays back into the original array using the compare type function
 *
 * @param WT_Fact[] $arr
 */
function sort_facts(&$arr) {
	$dated = array();
	$nondated = array();
	//-- split the array into dated and non-dated arrays
	$order = 0;
	foreach ($arr as $event) {
		$event->sortOrder = $order;
		$order++;
		if ($event->getDate()->isOk()) {
			$dated[] = $event;
		} else {
			$nondated[] = $event;
		}
	}

	//-- sort each type of array
	usort($dated, array("WT_Fact", "compareDate"));
	usort($nondated, array("WT_Fact", "compareType"));

	//-- merge the arrays back together comparing by Facts
	$dc = count($dated);
	$nc = count($nondated);
	$i = 0;
	$j = 0;
	$k = 0;
	// while there is anything in the dated array continue merging
	while ($i < $dc) {
		// compare each fact by type to merge them in order
		if ($j < $nc && WT_Fact::compareType($dated[$i], $nondated[$j]) > 0) {
			$arr[$k] = $nondated[$j];
			$j++;
		} else {
			$arr[$k] = $dated[$i];
			$i++;
		}
		$k++;
	}

	// get anything that might be left in the nondated array
	while ($j < $nc) {
		$arr[$k] = $nondated[$j];
		$j++;
		$k++;
	}

}

/**
 * For close family relationships, such as the families tab and the family navigator
 * Display a tick if both individuals are the same.
 * Stop after 3 steps, because pending edits may mean that there is no longer a
 * relationship to find.
 *
 * @param WT_Individual $person1
 * @param WT_Individual $person2
 *
 * @return string
 */
function get_close_relationship_name(WT_Individual $person1, WT_Individual $person2) {
	if ($person1 === $person2) {
		$label = '<i class="icon-selected" title="' . WT_I18N::translate('self') . '"></i>';
	} else {
		$label = get_relationship_name(get_relationship($person1, $person2, true, 3));
	}

	return $label;
}

/**
 * For facts on the individual/family pages.
 * Stop after 4 steps, as distant relationships may take a long time to find.
 * Review the limit of 4 if/when the performance of the function is improved.
 *
 * @param WT_Individual $person1
 * @param WT_Individual $person2
 *
 * @return string
 */
function get_associate_relationship_name(WT_Individual $person1, WT_Individual $person2) {
	if ($person1 === $person2) {
		$label = WT_I18N::translate('self');
	} else {
		$label = get_relationship_name(get_relationship($person1, $person2, true, 4));
	}

	return $label;
}

/**
 * Get relationship between two individuals in the gedcom
 *
 * @param WT_Individual $person1      the person to compute the relationship from
 * @param WT_Individual $person2      the person to compute the relatiohip to
 * @param boolean       $followspouse whether to add spouses to the path
 * @param integer       $maxlength    the maximum length of path
 * @param integer       $path_to_find which path in the relationship to find, 0 is the shortest path, 1 is the next shortest path, etc
 *
 * @return array|bool An array of nodes on the relationship path, or false if no path found
 */
function get_relationship(WT_Individual $person1, WT_Individual $person2, $followspouse = true, $maxlength = 0, $path_to_find = 0) {
	if ($person1 === $person2) {
		return false;
	}

	//-- current path nodes
	$p1nodes = array();
	//-- ids visited
	$visited = array();

	//-- set up first node for person1
	$node1 = array(
		'path'      => array($person1),
		'length'    => 0,
		'indi'      => $person1,
		'relations' => array('self'),
	);
	$p1nodes[] = $node1;

	$visited[$person1->getXref()] = true;

	$found = false;
	while (!$found) {
		//-- search the node list for the shortest path length
		$shortest = -1;
		foreach ($p1nodes as $index => $node) {
			if ($shortest == -1) {
				$shortest = $index;
			} else {
				$node1 = $p1nodes[$shortest];
				if ($node1['length'] > $node['length']) {
					$shortest = $index;
				}
			}
		}
		if ($shortest === -1) {
			return false;
		}
		$node = $p1nodes[$shortest];
		if ($maxlength == 0 || count($node['path']) <= $maxlength) {
			$indi = $node['indi'];
			//-- check all parents and siblings of this node
			foreach ($indi->getChildFamilies(WT_PRIV_HIDE) as $family) {
				$visited[$family->getXref()] = true;
				foreach ($family->getSpouses(WT_PRIV_HIDE) as $spouse) {
					if (!isset($visited[$spouse->getXref()])) {
						$node1 = $node;
						$node1['length']++;
						$node1['path'][] = $spouse;
						$node1['indi'] = $spouse;
						$node1['relations'][] = 'parent';
						$p1nodes[] = $node1;
						if ($spouse === $person2) {
							if ($path_to_find > 0) {
								$path_to_find--;
							} else {
								$found = true;
								$resnode = $node1;
							}
						} else {
							$visited[$spouse->getXref()] = true;
						}
					}
				}
				foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
					if (!isset($visited[$child->getXref()])) {
						$node1 = $node;
						$node1['length']++;
						$node1['path'][] = $child;
						$node1['indi'] = $child;
						$node1['relations'][] = 'sibling';
						$p1nodes[] = $node1;
						if ($child === $person2) {
							if ($path_to_find > 0) {
								$path_to_find--;
							} else {
								$found = true;
								$resnode = $node1;
							}
						} else {
							$visited[$child->getXref()] = true;
						}
					}
				}
			}
			//-- check all spouses and children of this node
			foreach ($indi->getSpouseFamilies(WT_PRIV_HIDE) as $family) {
				$visited[$family->getXref()] = true;
				if ($followspouse) {
					foreach ($family->getSpouses(WT_PRIV_HIDE) as $spouse) {
						if (!in_array($spouse->getXref(), $node1) || !isset($visited[$spouse->getXref()])) {
							$node1 = $node;
							$node1['length']++;
							$node1['path'][] = $spouse;
							$node1['indi'] = $spouse;
							$node1['relations'][] = 'spouse';
							$p1nodes[] = $node1;
							if ($spouse === $person2) {
								if ($path_to_find > 0) {
									$path_to_find--;
								} else {
									$found = true;
									$resnode = $node1;
								}
							} else {
								$visited[$spouse->getXref()] = true;
							}
						}
					}
				}
				foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
					if (!isset($visited[$child->getXref()])) {
						$node1 = $node;
						$node1['length']++;
						$node1['path'][] = $child;
						$node1['indi'] = $child;
						$node1['relations'][] = 'child';
						$p1nodes[] = $node1;
						if ($child === $person2) {
							if ($path_to_find > 0) {
								$path_to_find--;
							} else {
								$found = true;
								$resnode = $node1;
							}
						} else {
							$visited[$child->getXref()] = true;
						}
					}
				}
			}
		}
		unset($p1nodes[$shortest]);
	}

	// Convert generic relationships into sex-specific ones.
	foreach ($resnode['path'] as $n => $indi) {
		switch ($resnode['relations'][$n]) {
		case 'parent':
			switch ($indi->getSex()) {
			case 'M':
				$resnode['relations'][$n] = 'father';
				break;
			case 'F':
				$resnode['relations'][$n] = 'mother';
				break;
			}
			break;
		case 'child':
			switch ($indi->getSex()) {
			case 'M':
				$resnode['relations'][$n] = 'son';
				break;
			case 'F':
				$resnode['relations'][$n] = 'daughter';
				break;
			}
			break;
		case 'spouse':
			switch ($indi->getSex()) {
			case 'M':
				$resnode['relations'][$n] = 'husband';
				break;
			case 'F':
				$resnode['relations'][$n] = 'wife';
				break;
			}
			break;
		case 'sibling':
			switch ($indi->getSex()) {
			case 'M':
				$resnode['relations'][$n] = 'brother';
				break;
			case 'F':
				$resnode['relations'][$n] = 'sister';
				break;
			}
			break;
		}
	}

	return $resnode;
}

/**
 * Convert the result of get_relationship() into a relationship name.
 *
 * @param mixed[][] $nodes
 *
 * @return string
 */
function get_relationship_name($nodes) {
	if (!is_array($nodes)) {
		return '';
	}
	$person1 = $nodes['path'][0];
	$person2 = $nodes['path'][count($nodes['path']) - 1];
	$path = array_slice($nodes['relations'], 1);
	// Look for paths with *specific* names first.
	// Note that every combination must be listed separately, as the same English
	// name can be used for many different relationships.  e.g.
	// brother’s wife & husband’s sister = sister-in-law.
	//
	// $path is an array of the 12 possible gedcom family relationships:
	// mother/father/parent
	// brother/sister/sibling
	// husband/wife/spouse
	// son/daughter/child
	//
	// This is always the shortest path, so “father, daughter” is “half-sister”, not “sister”.
	//
	// This is very repetitive in English, but necessary in order to handle the
	// complexities of other languages.

	// Make each relationship parts the same length, for simpler matching.
	$combined_path = '';
	foreach ($path as $rel) {
		$combined_path .= substr($rel, 0, 3);
	}

	return get_relationship_name_from_path($combined_path, $person1, $person2);
}

/**
 * @param integer $n
 * @param string  $sex
 *
 * @return string
 */
function cousin_name($n, $sex) {
	switch ($sex) {
	case 'M':
		switch ($n) {
		case  1:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'first cousin');
		case  2:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'second cousin');
		case  3:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'third cousin');
		case  4:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'fourth cousin');
		case  5:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'fifth cousin');
		case  6:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'sixth cousin');
		case  7:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'seventh cousin');
		case  8:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'eighth cousin');
		case  9:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'ninth cousin');
		case 10:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'tenth cousin');
		case 11:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'eleventh cousin');
		case 12:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'twelfth cousin');
		case 13:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'thirteenth cousin');
		case 14:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'fourteenth cousin');
		case 15:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', 'fifteenth cousin');
		default:
			/* I18N: Note that for Italian and Polish, “N’th cousins” are different from English “N’th cousins”, and the software has already generated the correct “N” for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers. */
			return WT_I18N::translate_c('MALE', '%d × cousin', $n);
		}
	case 'F':
		switch ($n) {
		case  1:
			return WT_I18N::translate_c('FEMALE', 'first cousin');
		case  2:
			return WT_I18N::translate_c('FEMALE', 'second cousin');
		case  3:
			return WT_I18N::translate_c('FEMALE', 'third cousin');
		case  4:
			return WT_I18N::translate_c('FEMALE', 'fourth cousin');
		case  5:
			return WT_I18N::translate_c('FEMALE', 'fifth cousin');
		case  6:
			return WT_I18N::translate_c('FEMALE', 'sixth cousin');
		case  7:
			return WT_I18N::translate_c('FEMALE', 'seventh cousin');
		case  8:
			return WT_I18N::translate_c('FEMALE', 'eighth cousin');
		case  9:
			return WT_I18N::translate_c('FEMALE', 'ninth cousin');
		case 10:
			return WT_I18N::translate_c('FEMALE', 'tenth cousin');
		case 11:
			return WT_I18N::translate_c('FEMALE', 'eleventh cousin');
		case 12:
			return WT_I18N::translate_c('FEMALE', 'twelfth cousin');
		case 13:
			return WT_I18N::translate_c('FEMALE', 'thirteenth cousin');
		case 14:
			return WT_I18N::translate_c('FEMALE', 'fourteenth cousin');
		case 15:
			return WT_I18N::translate_c('FEMALE', 'fifteenth cousin');
		default:
			return WT_I18N::translate_c('FEMALE', '%d × cousin', $n);
		}
	default:
		switch ($n) {
		case  1:
			return WT_I18N::translate_c('MALE/FEMALE', 'first cousin');
		case  2:
			return WT_I18N::translate_c('MALE/FEMALE', 'second cousin');
		case  3:
			return WT_I18N::translate_c('MALE/FEMALE', 'third cousin');
		case  4:
			return WT_I18N::translate_c('MALE/FEMALE', 'fourth cousin');
		case  5:
			return WT_I18N::translate_c('MALE/FEMALE', 'fifth cousin');
		case  6:
			return WT_I18N::translate_c('MALE/FEMALE', 'sixth cousin');
		case  7:
			return WT_I18N::translate_c('MALE/FEMALE', 'seventh cousin');
		case  8:
			return WT_I18N::translate_c('MALE/FEMALE', 'eighth cousin');
		case  9:
			return WT_I18N::translate_c('MALE/FEMALE', 'ninth cousin');
		case 10:
			return WT_I18N::translate_c('MALE/FEMALE', 'tenth cousin');
		case 11:
			return WT_I18N::translate_c('MALE/FEMALE', 'eleventh cousin');
		case 12:
			return WT_I18N::translate_c('MALE/FEMALE', 'twelfth cousin');
		case 13:
			return WT_I18N::translate_c('MALE/FEMALE', 'thirteenth cousin');
		case 14:
			return WT_I18N::translate_c('MALE/FEMALE', 'fourteenth cousin');
		case 15:
			return WT_I18N::translate_c('MALE/FEMALE', 'fifteenth cousin');
		default:
			return WT_I18N::translate_c('MALE/FEMALE', '%d × cousin', $n);
		}
	}
}

/**
 * A variation on cousin_name(), for constructs such as “sixth great-nephew”
 * Currently used only by Spanish relationship names.
 *
 * @param integer $n
 * @param string  $sex
 * @param string  $relation
 *
 * @return string
 */
function cousin_name2($n, $sex, $relation) {
	switch ($sex) {
	case 'M':
		switch ($n) {
		case  1: // I18N: A Spanish relationship name, such as third great-nephew
			return WT_I18N::translate_c('MALE', 'first %s', $relation);
		case  2:
			return WT_I18N::translate_c('MALE', 'second %s', $relation);
		case  3:
			return WT_I18N::translate_c('MALE', 'third %s', $relation);
		case  4:
			return WT_I18N::translate_c('MALE', 'fourth %s', $relation);
		case  5:
			return WT_I18N::translate_c('MALE', 'fifth %s', $relation);
		default: // I18N: A Spanish relationship name, such as third great-nephew
			return WT_I18N::translate_c('MALE', '%1$d × %2$s', $n, $relation);
		}
	case 'F':
		switch ($n) {
		case  1: // I18N: A Spanish relationship name, such as third great-nephew
			return WT_I18N::translate_c('FEMALE', 'first %s', $relation);
		case  2:
			return WT_I18N::translate_c('FEMALE', 'second %s', $relation);
		case  3:
			return WT_I18N::translate_c('FEMALE', 'third %s', $relation);
		case  4:
			return WT_I18N::translate_c('FEMALE', 'fourth %s', $relation);
		case  5:
			return WT_I18N::translate_c('FEMALE', 'fifth %s', $relation);
		default: // I18N: A Spanish relationship name, such as third great-nephew
			return WT_I18N::translate_c('FEMALE', '%1$d × %2$s', $n, $relation);
		}
	default:
		switch ($n) {
		case  1: // I18N: A Spanish relationship name, such as third great-nephew
			return WT_I18N::translate_c('MALE/FEMALE', 'first %s', $relation);
		case  2:
			return WT_I18N::translate_c('MALE/FEMALE', 'second %s', $relation);
		case  3:
			return WT_I18N::translate_c('MALE/FEMALE', 'third %s', $relation);
		case  4:
			return WT_I18N::translate_c('MALE/FEMALE', 'fourth %s', $relation);
		case  5:
			return WT_I18N::translate_c('MALE/FEMALE', 'fifth %s', $relation);
		default: // I18N: A Spanish relationship name, such as third great-nephew
			return WT_I18N::translate_c('MALE/FEMALE', '%1$d × %2$s', $n, $relation);
		}
	}
}

/**
 * @param string        $path
 * @param WT_Individual $person1
 * @param WT_Individual $person2
 *
 * @return string
 */
function get_relationship_name_from_path($path, WT_Individual $person1 = null, WT_Individual $person2 = null) {
	if (!preg_match('/^(mot|fat|par|hus|wif|spo|son|dau|chi|bro|sis|sib)*$/', $path)) {
		// TODO: Update all the “3 RELA ” values in class_person
		return '<span class="error">' . $path . '</span>';
	}
	// The path does not include the starting person.  In some languages, the
	// translation for a man’s (relative) is different to a woman’s (relative),
	// due to inflection.
	$sex1 = $person1 ? $person1->getSex() : 'U';

	// The sex of the last person in the relationship determines the name in
	// many cases.  e.g. great-aunt / great-uncle
	if (preg_match('/(fat|hus|son|bro)$/', $path)) {
		$sex2 = 'M';
	} elseif (preg_match('/(mot|wif|dau|sis)$/', $path)) {
		$sex2 = 'F';
	} else {
		$sex2 = 'U';
	}

	switch ($path) {
	case '':
		return WT_I18N::translate('self');
		//  Level One relationships
	case 'mot':
		return WT_I18N::translate('mother');
	case 'fat':
		return WT_I18N::translate('father');
	case 'par':
		return WT_I18N::translate('parent');
	case 'hus':
		if ($person1 && $person2) {
			foreach ($person1->getSpouseFamilies() as $family) {
				if ($person2 === $family->getSpouse($person1)) {
					if ($family->getFacts('_NMR')) {
						return WT_I18N::translate_c('MALE', 'partner');
					} elseif ($family->getFacts(WT_EVENTS_DIV)) {
						return WT_I18N::translate('ex-husband');
					}
				}
			}
		}

		return WT_I18N::translate('husband');
	case 'wif':
		if ($person1 && $person1) {
			foreach ($person1->getSpouseFamilies() as $family) {
				if ($person2 === $family->getSpouse($person1)) {
					if ($family->getFacts('_NMR')) {
						return WT_I18N::translate_c('FEMALE', 'partner');
					} elseif ($family->getFacts(WT_EVENTS_DIV)) {
						return WT_I18N::translate('ex-wife');
					}
				}
			}
		}

		return WT_I18N::translate('wife');
	case 'spo':
		if ($person1 && $person2) {
			foreach ($person1->getSpouseFamilies() as $family) {
				if ($person2 === $family->getSpouse($person1)) {
					if ($family->getFacts('_NMR')) {
						return WT_I18N::translate_c('MALE/FEMALE', 'partner');
					} elseif ($family->getFacts(WT_EVENTS_DIV)) {
						return WT_I18N::translate('ex-spouse');
					}
				}
			}
		}

		return WT_I18N::translate('spouse');
	case 'son':
		return WT_I18N::translate('son');
	case 'dau':
		return WT_I18N::translate('daughter');
	case 'chi':
		return WT_I18N::translate('child');
	case 'bro':
		if ($person1 && $person2) {
			$dob1 = $person1->getBirthDate();
			$dob2 = $person2->getBirthDate();
			if ($dob1->isOK() && $dob2->isOK()) {
				if (abs($dob1->JD() - $dob2->JD()) < 2 && !$dob1->qual1 && !$dob2->qual1) { // Exclude BEF, AFT, etc.
					return WT_I18N::translate('twin brother');
				} elseif ($dob1->MaxJD() < $dob2->MinJD()) {
					return WT_I18N::translate('younger brother');
				} elseif ($dob1->MinJD() > $dob2->MaxJD()) {
					return WT_I18N::translate('elder brother');
				}
			}
		}

		return WT_I18N::translate('brother');
	case 'sis':
		if ($person1 && $person2) {
			$dob1 = $person1->getBirthDate();
			$dob2 = $person2->getBirthDate();
			if ($dob1->isOK() && $dob2->isOK()) {
				if (abs($dob1->JD() - $dob2->JD()) < 2 && !$dob1->qual1 && !$dob2->qual1) { // Exclude BEF, AFT, etc.
					return WT_I18N::translate('twin sister');
				} elseif ($dob1->MaxJD() < $dob2->MinJD()) {
					return WT_I18N::translate('younger sister');
				} elseif ($dob1->MinJD() > $dob2->MaxJD()) {
					return WT_I18N::translate('elder sister');
				}
			}
		}

		return WT_I18N::translate('sister');
	case 'sib':
		if ($person1 && $person2) {
			$dob1 = $person1->getBirthDate();
			$dob2 = $person2->getBirthDate();
			if ($dob1->isOK() && $dob2->isOK()) {
				if (abs($dob1->JD() - $dob2->JD()) < 2 && !$dob1->qual1 && !$dob2->qual1) { // Exclude BEF, AFT, etc.
					return WT_I18N::translate('twin sibling');
				} elseif ($dob1->MaxJD() < $dob2->MinJD()) {
					return WT_I18N::translate('younger sibling');
				} elseif ($dob1->MinJD() > $dob2->MaxJD()) {
					return WT_I18N::translate('elder sibling');
				}
			}
		}

		return WT_I18N::translate('sibling');

		// Level Two relationships
	case 'brochi':
		return WT_I18N::translate_c('brother’s child', 'nephew/niece');
	case 'brodau':
		return WT_I18N::translate_c('brother’s daughter', 'niece');
	case 'broson':
		return WT_I18N::translate_c('brother’s son', 'nephew');
	case 'browif':
		return WT_I18N::translate_c('brother’s wife', 'sister-in-law');
	case 'chichi':
		return WT_I18N::translate_c('child’s child', 'grandchild');
	case 'chidau':
		return WT_I18N::translate_c('child’s daughter', 'granddaughter');
	case 'chihus':
		return WT_I18N::translate_c('child’s husband', 'son-in-law');
	case 'chison':
		return WT_I18N::translate_c('child’s son', 'grandson');
	case 'chispo':
		return WT_I18N::translate_c('child’s spouse', 'son/daughter-in-law');
	case 'chiwif':
		return WT_I18N::translate_c('child’s wife', 'daughter-in-law');
	case 'dauchi':
		return WT_I18N::translate_c('daughter’s child', 'grandchild');
	case 'daudau':
		return WT_I18N::translate_c('daughter’s daughter', 'granddaughter');
	case 'dauhus':
		return WT_I18N::translate_c('daughter’s husband', 'son-in-law');
	case 'dauson':
		return WT_I18N::translate_c('daughter’s son', 'grandson');
	case 'fatbro':
		return WT_I18N::translate_c('father’s brother', 'uncle');
	case 'fatchi':
		return WT_I18N::translate_c('father’s child', 'half-sibling');
	case 'fatdau':
		return WT_I18N::translate_c('father’s daughter', 'half-sister');
	case 'fatfat':
		return WT_I18N::translate_c('father’s father', 'paternal grandfather');
	case 'fatmot':
		return WT_I18N::translate_c('father’s mother', 'paternal grandmother');
	case 'fatpar':
		return WT_I18N::translate_c('father’s parent', 'paternal grandparent');
	case 'fatsib':
		return WT_I18N::translate_c('father’s sibling', 'aunt/uncle');
	case 'fatsis':
		return WT_I18N::translate_c('father’s sister', 'aunt');
	case 'fatson':
		return WT_I18N::translate_c('father’s son', 'half-brother');
	case 'fatwif':
		return WT_I18N::translate_c('father’s wife', 'step-mother');
	case 'husbro':
		return WT_I18N::translate_c('husband’s brother', 'brother-in-law');
	case 'huschi':
		return WT_I18N::translate_c('husband’s child', 'step-child');
	case 'husdau':
		return WT_I18N::translate_c('husband’s daughter', 'step-daughter');
	case 'husfat':
		return WT_I18N::translate_c('husband’s father', 'father-in-law');
	case 'husmot':
		return WT_I18N::translate_c('husband’s mother', 'mother-in-law');
	case 'hussib':
		return WT_I18N::translate_c('husband’s sibling', 'brother/sister-in-law');
	case 'hussis':
		return WT_I18N::translate_c('husband’s sister', 'sister-in-law');
	case 'husson':
		return WT_I18N::translate_c('husband’s son', 'step-son');
	case 'motbro':
		return WT_I18N::translate_c('mother’s brother', 'uncle');
	case 'motchi':
		return WT_I18N::translate_c('mother’s child', 'half-sibling');
	case 'motdau':
		return WT_I18N::translate_c('mother’s daughter', 'half-sister');
	case 'motfat':
		return WT_I18N::translate_c('mother’s father', 'maternal grandfather');
	case 'mothus':
		return WT_I18N::translate_c('mother’s husband', 'step-father');
	case 'motmot':
		return WT_I18N::translate_c('mother’s mother', 'maternal grandmother');
	case 'motpar':
		return WT_I18N::translate_c('mother’s parent', 'maternal grandparent');
	case 'motsib':
		return WT_I18N::translate_c('mother’s sibling', 'aunt/uncle');
	case 'motsis':
		return WT_I18N::translate_c('mother’s sister', 'aunt');
	case 'motson':
		return WT_I18N::translate_c('mother’s son', 'half-brother');
	case 'parbro':
		return WT_I18N::translate_c('parent’s brother', 'uncle');
	case 'parchi':
		return WT_I18N::translate_c('parent’s child', 'half-sibling');
	case 'pardau':
		return WT_I18N::translate_c('parent’s daughter', 'half-sister');
	case 'parfat':
		return WT_I18N::translate_c('parent’s father', 'grandfather');
	case 'parmot':
		return WT_I18N::translate_c('parent’s mother', 'grandmother');
	case 'parpar':
		return WT_I18N::translate_c('parent’s parent', 'grandparent');
	case 'parsib':
		return WT_I18N::translate_c('parent’s sibling', 'aunt/uncle');
	case 'parsis':
		return WT_I18N::translate_c('parent’s sister', 'aunt');
	case 'parson':
		return WT_I18N::translate_c('parent’s son', 'half-brother');
	case 'parspo':
		return WT_I18N::translate_c('parent’s spouse', 'step-parent');
	case 'sibchi':
		return WT_I18N::translate_c('sibling’s child', 'nephew/niece');
	case 'sibdau':
		return WT_I18N::translate_c('sibling’s daughter', 'niece');
	case 'sibson':
		return WT_I18N::translate_c('sibling’s son', 'nephew');
	case 'sibspo':
		return WT_I18N::translate_c('sibling’s spouse', 'brother/sister-in-law');
	case 'sischi':
		return WT_I18N::translate_c('sister’s child', 'nephew/niece');
	case 'sisdau':
		return WT_I18N::translate_c('sister’s daughter', 'niece');
	case 'sishus':
		return WT_I18N::translate_c('sister’s husband', 'brother-in-law');
	case 'sisson':
		return WT_I18N::translate_c('sister’s son', 'nephew');
	case 'sonchi':
		return WT_I18N::translate_c('son’s child', 'grandchild');
	case 'sondau':
		return WT_I18N::translate_c('son’s daughter', 'granddaughter');
	case 'sonson':
		return WT_I18N::translate_c('son’s son', 'grandson');
	case 'sonwif':
		return WT_I18N::translate_c('son’s wife', 'daughter-in-law');
	case 'spobro':
		return WT_I18N::translate_c('spouse’s brother', 'brother-in-law');
	case 'spochi':
		return WT_I18N::translate_c('spouse’s child', 'step-child');
	case 'spodau':
		return WT_I18N::translate_c('spouse’s daughter', 'step-daughter');
	case 'spofat':
		return WT_I18N::translate_c('spouse’s father', 'father-in-law');
	case 'spomot':
		return WT_I18N::translate_c('spouse’s mother', 'mother-in-law');
	case 'sposis':
		return WT_I18N::translate_c('spouse’s sister', 'sister-in-law');
	case 'sposon':
		return WT_I18N::translate_c('spouse’s son', 'step-son');
	case 'spopar':
		return WT_I18N::translate_c('spouse’s parent', 'mother/father-in-law');
	case 'sposib':
		return WT_I18N::translate_c('spouse’s sibling', 'brother/sister-in-law');
	case 'wifbro':
		return WT_I18N::translate_c('wife’s brother', 'brother-in-law');
	case 'wifchi':
		return WT_I18N::translate_c('wife’s child', 'step-child');
	case 'wifdau':
		return WT_I18N::translate_c('wife’s daughter', 'step-daughter');
	case 'wiffat':
		return WT_I18N::translate_c('wife’s father', 'father-in-law');
	case 'wifmot':
		return WT_I18N::translate_c('wife’s mother', 'mother-in-law');
	case 'wifsib':
		return WT_I18N::translate_c('wife’s sibling', 'brother/sister-in-law');
	case 'wifsis':
		return WT_I18N::translate_c('wife’s sister', 'sister-in-law');
	case 'wifson':
		return WT_I18N::translate_c('wife’s son', 'step-son');

		// Level Three relationships
		// I have commented out some of the unknown-sex relationships that are unlikely to to occur.
		// Feel free to add them in, if you think they might be needed
	case 'brochichi':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s child’s child', 'great-nephew/niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s child’s child', 'great-nephew/niece');
		}
	case 'brochidau':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s child’s daughter', 'great-niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s child’s daughter', 'great-niece');
		}
	case 'brochison':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s child’s son', 'great-nephew');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s child’s son', 'great-nephew');
		}
	case 'brodauchi':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s daughter’s child', 'great-nephew/niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s daughter’s child', 'great-nephew/niece');
		}
	case 'brodaudau':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s daughter’s daughter', 'great-niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s daughter’s daughter', 'great-niece');
		}
	case 'brodauhus':
		return WT_I18N::translate_c('brother’s daughter’s husband', 'nephew-in-law');
	case 'brodauson':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s daughter’s son', 'great-nephew');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s daughter’s son', 'great-nephew');
		}
	case 'brosonchi':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s son’s child', 'great-nephew/niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s son’s child', 'great-nephew/niece');
		}
	case 'brosondau':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s son’s daughter', 'great-niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s son’s daughter', 'great-niece');
		}
	case 'brosonson':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) brother’s son’s son', 'great-nephew');
		} else {
			return WT_I18N::translate_c('(a woman’s) brother’s son’s son', 'great-nephew');
		}
	case 'brosonwif':
		return WT_I18N::translate_c('brother’s son’s wife', 'niece-in-law');
	case 'browifbro':
		return WT_I18N::translate_c('brother’s wife’s brother', 'brother-in-law');
	case 'browifsib':
		return WT_I18N::translate_c('brother’s wife’s sibling', 'brother/sister-in-law');
	case 'browifsis':
		return WT_I18N::translate_c('brother’s wife’s sister', 'sister-in-law');
	case 'chichichi':
		return WT_I18N::translate_c('child’s child’s child', 'great-grandchild');
	case 'chichidau':
		return WT_I18N::translate_c('child’s child’s daughter', 'great-granddaughter');
	case 'chichison':
		return WT_I18N::translate_c('child’s child’s son', 'great-grandson');
	case 'chidauchi':
		return WT_I18N::translate_c('child’s daughter’s child', 'great-grandchild');
	case 'chidaudau':
		return WT_I18N::translate_c('child’s daughter’s daughter', 'great-granddaughter');
	case 'chidauhus':
		return WT_I18N::translate_c('child’s daughter’s husband', 'granddaughter’s husband');
	case 'chidauson':
		return WT_I18N::translate_c('child’s daughter’s son', 'great-grandson');
	case 'chisonchi':
		return WT_I18N::translate_c('child’s son’s child', 'great-grandchild');
	case 'chisondau':
		return WT_I18N::translate_c('child’s son’s daughter', 'great-granddaughter');
	case 'chisonson':
		return WT_I18N::translate_c('child’s son’s son', 'great-grandson');
	case 'chisonwif':
		return WT_I18N::translate_c('child’s son’s wife', 'grandson’s wife');
	case 'dauchichi':
		return WT_I18N::translate_c('daughter’s child’s child', 'great-grandchild');
	case 'dauchidau':
		return WT_I18N::translate_c('daughter’s child’s daughter', 'great-granddaughter');
	case 'dauchison':
		return WT_I18N::translate_c('daughter’s child’s son', 'great-grandson');
	case 'daudauchi':
		return WT_I18N::translate_c('daughter’s daughter’s child', 'great-grandchild');
	case 'daudaudau':
		return WT_I18N::translate_c('daughter’s daughter’s daughter', 'great-granddaughter');
	case 'daudauhus':
		return WT_I18N::translate_c('daughter’s daughter’s husband', 'granddaughter’s husband');
	case 'daudauson':
		return WT_I18N::translate_c('daughter’s daughter’s son', 'great-grandson');
	case 'dauhusfat':
		return WT_I18N::translate_c('daughter’s husband’s father', 'son-in-law’s father');
	case 'dauhusmot':
		return WT_I18N::translate_c('daughter’s husband’s mother', 'son-in-law’s mother');
	case 'dauhuspar':
		return WT_I18N::translate_c('daughter’s husband’s parent', 'son-in-law’s parent');
	case 'dausonchi':
		return WT_I18N::translate_c('daughter’s son’s child', 'great-grandchild');
	case 'dausondau':
		return WT_I18N::translate_c('daughter’s son’s daughter', 'great-granddaughter');
	case 'dausonson':
		return WT_I18N::translate_c('daughter’s son’s son', 'great-grandson');
	case 'dausonwif':
		return WT_I18N::translate_c('daughter’s son’s wife', 'grandson’s wife');
	case 'fatbrochi':
		return WT_I18N::translate_c('father’s brother’s child', 'first cousin');
	case 'fatbrodau':
		return WT_I18N::translate_c('father’s brother’s daughter', 'first cousin');
	case 'fatbroson':
		return WT_I18N::translate_c('father’s brother’s son', 'first cousin');
	case 'fatbrowif':
		return WT_I18N::translate_c('father’s brother’s wife', 'aunt');
	case 'fatfatbro':
		return WT_I18N::translate_c('father’s father’s brother', 'great-uncle');
	case 'fatfatfat':
		return WT_I18N::translate_c('father’s father’s father', 'great-grandfather');
	case 'fatfatmot':
		return WT_I18N::translate_c('father’s father’s mother', 'great-grandmother');
	case 'fatfatpar':
		return WT_I18N::translate_c('father’s father’s parent', 'great-grandparent');
	case 'fatfatsib':
		return WT_I18N::translate_c('father’s father’s sibling', 'great-aunt/uncle');
	case 'fatfatsis':
		return WT_I18N::translate_c('father’s father’s sister', 'great-aunt');
	case 'fatmotbro':
		return WT_I18N::translate_c('father’s mother’s brother', 'great-uncle');
	case 'fatmotfat':
		return WT_I18N::translate_c('father’s mother’s father', 'great-grandfather');
	case 'fatmotmot':
		return WT_I18N::translate_c('father’s mother’s mother', 'great-grandmother');
	case 'fatmotpar':
		return WT_I18N::translate_c('father’s mother’s parent', 'great-grandparent');
	case 'fatmotsib':
		return WT_I18N::translate_c('father’s mother’s sibling', 'great-aunt/uncle');
	case 'fatmotsis':
		return WT_I18N::translate_c('father’s mother’s sister', 'great-aunt');
	case 'fatparbro':
		return WT_I18N::translate_c('father’s parent’s brother', 'great-uncle');
	case 'fatparfat':
		return WT_I18N::translate_c('father’s parent’s father', 'great-grandfather');
	case 'fatparmot':
		return WT_I18N::translate_c('father’s parent’s mother', 'great-grandmother');
	case 'fatparpar':
		return WT_I18N::translate_c('father’s parent’s parent', 'great-grandparent');
	case 'fatparsib':
		return WT_I18N::translate_c('father’s parent’s sibling', 'great-aunt/uncle');
	case 'fatparsis':
		return WT_I18N::translate_c('father’s parent’s sister', 'great-aunt');
	case 'fatsischi':
		return WT_I18N::translate_c('father’s sister’s child', 'first cousin');
	case 'fatsisdau':
		return WT_I18N::translate_c('father’s sister’s daughter', 'first cousin');
	case 'fatsishus':
		return WT_I18N::translate_c('father’s sister’s husband', 'uncle');
	case 'fatsisson':
		return WT_I18N::translate_c('father’s sister’s son', 'first cousin');
	case 'fatwifchi':
		return WT_I18N::translate_c('father’s wife’s child', 'step-sibling');
	case 'fatwifdau':
		return WT_I18N::translate_c('father’s wife’s daughter', 'step-sister');
	case 'fatwifson':
		return WT_I18N::translate_c('father’s wife’s son', 'step-brother');
	case 'husbrowif':
		return WT_I18N::translate_c('husband’s brother’s wife', 'sister-in-law');
	case 'hussishus':
		return WT_I18N::translate_c('husband’s sister’s husband', 'brother-in-law');
	case 'motbrochi':
		return WT_I18N::translate_c('mother’s brother’s child', 'first cousin');
	case 'motbrodau':
		return WT_I18N::translate_c('mother’s brother’s daughter', 'first cousin');
	case 'motbroson':
		return WT_I18N::translate_c('mother’s brother’s son', 'first cousin');
	case 'motbrowif':
		return WT_I18N::translate_c('mother’s brother’s wife', 'aunt');
	case 'motfatbro':
		return WT_I18N::translate_c('mother’s father’s brother', 'great-uncle');
	case 'motfatfat':
		return WT_I18N::translate_c('mother’s father’s father', 'great-grandfather');
	case 'motfatmot':
		return WT_I18N::translate_c('mother’s father’s mother', 'great-grandmother');
	case 'motfatpar':
		return WT_I18N::translate_c('mother’s father’s parent', 'great-grandparent');
	case 'motfatsib':
		return WT_I18N::translate_c('mother’s father’s sibling', 'great-aunt/uncle');
	case 'motfatsis':
		return WT_I18N::translate_c('mother’s father’s sister', 'great-aunt');
	case 'mothuschi':
		return WT_I18N::translate_c('mother’s husband’s child', 'step-sibling');
	case 'mothusdau':
		return WT_I18N::translate_c('mother’s husband’s daughter', 'step-sister');
	case 'mothusson':
		return WT_I18N::translate_c('mother’s husband’s son', 'step-brother');
	case 'motmotbro':
		return WT_I18N::translate_c('mother’s mother’s brother', 'great-uncle');
	case 'motmotfat':
		return WT_I18N::translate_c('mother’s mother’s father', 'great-grandfather');
	case 'motmotmot':
		return WT_I18N::translate_c('mother’s mother’s mother', 'great-grandmother');
	case 'motmotpar':
		return WT_I18N::translate_c('mother’s mother’s parent', 'great-grandparent');
	case 'motmotsib':
		return WT_I18N::translate_c('mother’s mother’s sibling', 'great-aunt/uncle');
	case 'motmotsis':
		return WT_I18N::translate_c('mother’s mother’s sister', 'great-aunt');
	case 'motparbro':
		return WT_I18N::translate_c('mother’s parent’s brother', 'great-uncle');
	case 'motparfat':
		return WT_I18N::translate_c('mother’s parent’s father', 'great-grandfather');
	case 'motparmot':
		return WT_I18N::translate_c('mother’s parent’s mother', 'great-grandmother');
	case 'motparpar':
		return WT_I18N::translate_c('mother’s parent’s parent', 'great-grandparent');
	case 'motparsib':
		return WT_I18N::translate_c('mother’s parent’s sibling', 'great-aunt/uncle');
	case 'motparsis':
		return WT_I18N::translate_c('mother’s parent’s sister', 'great-aunt');
	case 'motsischi':
		return WT_I18N::translate_c('mother’s sister’s child', 'first cousin');
	case 'motsisdau':
		return WT_I18N::translate_c('mother’s sister’s daughter', 'first cousin');
	case 'motsishus':
		return WT_I18N::translate_c('mother’s sister’s husband', 'uncle');
	case 'motsisson':
		return WT_I18N::translate_c('mother’s sister’s son', 'first cousin');
	case 'parbrowif':
		return WT_I18N::translate_c('parent’s brother’s wife', 'aunt');
	case 'parfatbro':
		return WT_I18N::translate_c('parent’s father’s brother', 'great-uncle');
	case 'parfatfat':
		return WT_I18N::translate_c('parent’s father’s father', 'great-grandfather');
	case 'parfatmot':
		return WT_I18N::translate_c('parent’s father’s mother', 'great-grandmother');
	case 'parfatpar':
		return WT_I18N::translate_c('parent’s father’s parent', 'great-grandparent');
	case 'parfatsib':
		return WT_I18N::translate_c('parent’s father’s sibling', 'great-aunt/uncle');
	case 'parfatsis':
		return WT_I18N::translate_c('parent’s father’s sister', 'great-aunt');
	case 'parmotbro':
		return WT_I18N::translate_c('parent’s mother’s brother', 'great-uncle');
	case 'parmotfat':
		return WT_I18N::translate_c('parent’s mother’s father', 'great-grandfather');
	case 'parmotmot':
		return WT_I18N::translate_c('parent’s mother’s mother', 'great-grandmother');
	case 'parmotpar':
		return WT_I18N::translate_c('parent’s mother’s parent', 'great-grandparent');
	case 'parmotsib':
		return WT_I18N::translate_c('parent’s mother’s sibling', 'great-aunt/uncle');
	case 'parmotsis':
		return WT_I18N::translate_c('parent’s mother’s sister', 'great-aunt');
	case 'parparbro':
		return WT_I18N::translate_c('parent’s parent’s brother', 'great-uncle');
	case 'parparfat':
		return WT_I18N::translate_c('parent’s parent’s father', 'great-grandfather');
	case 'parparmot':
		return WT_I18N::translate_c('parent’s parent’s mother', 'great-grandmother');
	case 'parparpar':
		return WT_I18N::translate_c('parent’s parent’s parent', 'great-grandparent');
	case 'parparsib':
		return WT_I18N::translate_c('parent’s parent’s sibling', 'great-aunt/uncle');
	case 'parparsis':
		return WT_I18N::translate_c('parent’s parent’s sister', 'great-aunt');
	case 'parsishus':
		return WT_I18N::translate_c('parent’s sister’s husband', 'uncle');
	case 'parspochi':
		return WT_I18N::translate_c('parent’s spouse’s child', 'step-sibling');
	case 'parspodau':
		return WT_I18N::translate_c('parent’s spouse’s daughter', 'step-sister');
	case 'parsposon':
		return WT_I18N::translate_c('parent’s spouse’s son', 'step-brother');
	case 'sibchichi':
		return WT_I18N::translate_c('sibling’s child’s child', 'great-nephew/niece');
	case 'sibchidau':
		return WT_I18N::translate_c('sibling’s child’s daughter', 'great-niece');
	case 'sibchison':
		return WT_I18N::translate_c('sibling’s child’s son', 'great-nephew');
	case 'sibdauchi':
		return WT_I18N::translate_c('sibling’s daughter’s child', 'great-nephew/niece');
	case 'sibdaudau':
		return WT_I18N::translate_c('sibling’s daughter’s daughter', 'great-niece');
	case 'sibdauhus':
		return WT_I18N::translate_c('sibling’s daughter’s husband', 'nephew-in-law');
	case 'sibdauson':
		return WT_I18N::translate_c('sibling’s daughter’s son', 'great-nephew');
	case 'sibsonchi':
		return WT_I18N::translate_c('sibling’s son’s child', 'great-nephew/niece');
	case 'sibsondau':
		return WT_I18N::translate_c('sibling’s son’s daughter', 'great-niece');
	case 'sibsonson':
		return WT_I18N::translate_c('sibling’s son’s son', 'great-nephew');
	case 'sibsonwif':
		return WT_I18N::translate_c('sibling’s son’s wife', 'niece-in-law');
	case 'sischichi':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s child’s child', 'great-nephew/niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s child’s child', 'great-nephew/niece');
		}
	case 'sischidau':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s child’s daughter', 'great-niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s child’s daughter', 'great-niece');
		}
	case 'sischison':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s child’s son', 'great-nephew');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s child’s son', 'great-nephew');
		}
	case 'sisdauchi':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s daughter’s child', 'great-nephew/niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s daughter’s child', 'great-nephew/niece');
		}
	case 'sisdaudau':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s daughter’s daughter', 'great-niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s daughter’s daughter', 'great-niece');
		}
	case 'sisdauhus':
		return WT_I18N::translate_c('sisters’s daughter’s husband', 'nephew-in-law');
	case 'sisdauson':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s daughter’s son', 'great-nephew');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s daughter’s son', 'great-nephew');
		}
	case 'sishusbro':
		return WT_I18N::translate_c('sister’s husband’s brother', 'brother-in-law');
	case 'sishussib':
		return WT_I18N::translate_c('sister’s husband’s sibling', 'brother/sister-in-law');
	case 'sishussis':
		return WT_I18N::translate_c('sister’s husband’s sister', 'sister-in-law');
	case 'sissonchi':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s son’s child', 'great-nephew/niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s son’s child', 'great-nephew/niece');
		}
	case 'sissondau':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s son’s daughter', 'great-niece');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s son’s daughter', 'great-niece');
		}
	case 'sissonson':
		if ($sex1 === 'M') {
			return WT_I18N::translate_c('(a man’s) sister’s son’s son', 'great-nephew');
		} else {
			return WT_I18N::translate_c('(a woman’s) sister’s son’s son', 'great-nephew');
		}
	case 'sissonwif':
		return WT_I18N::translate_c('sisters’s son’s wife', 'niece-in-law');
	case 'sonchichi':
		return WT_I18N::translate_c('son’s child’s child', 'great-grandchild');
	case 'sonchidau':
		return WT_I18N::translate_c('son’s child’s daughter', 'great-granddaughter');
	case 'sonchison':
		return WT_I18N::translate_c('son’s child’s son', 'great-grandson');
	case 'sondauchi':
		return WT_I18N::translate_c('son’s daughter’s child', 'great-grandchild');
	case 'sondaudau':
		return WT_I18N::translate_c('son’s daughter’s daughter', 'great-granddaughter');
	case 'sondauhus':
		return WT_I18N::translate_c('son’s daughter’s husband', 'granddaughter’s husband');
	case 'sondauson':
		return WT_I18N::translate_c('son’s daughter’s son', 'great-grandson');
	case 'sonsonchi':
		return WT_I18N::translate_c('son’s son’s child', 'great-grandchild');
	case 'sonsondau':
		return WT_I18N::translate_c('son’s son’s daughter', 'great-granddaughter');
	case 'sonsonson':
		return WT_I18N::translate_c('son’s son’s son', 'great-grandson');
	case 'sonsonwif':
		return WT_I18N::translate_c('son’s son’s wife', 'grandson’s wife');
	case 'sonwiffat':
		return WT_I18N::translate_c('son’s wife’s father', 'daughter-in-law’s father');
	case 'sonwifmot':
		return WT_I18N::translate_c('son’s wife’s mother', 'daughter-in-law’s mother');
	case 'sonwifpar':
		return WT_I18N::translate_c('son’s wife’s parent', 'daughter-in-law’s parent');
	case 'wifbrowif':
		return WT_I18N::translate_c('wife’s brother’s wife', 'sister-in-law');
	case 'wifsishus':
		return WT_I18N::translate_c('wife’s sister’s husband', 'brother-in-law');

		// Some “special case” level four relationships that have specific names in certain languages
	case 'fatfatbrowif':
		return WT_I18N::translate_c('father’s father’s brother’s wife', 'great-aunt');
	case 'fatfatsibspo':
		return WT_I18N::translate_c('father’s father’s sibling’s spouse', 'great-aunt/uncle');
	case 'fatfatsishus':
		return WT_I18N::translate_c('father’s father’s sister’s husband', 'great-uncle');
	case 'fatmotbrowif':
		return WT_I18N::translate_c('father’s mother’s brother’s wife', 'great-aunt');
	case 'fatmotsibspo':
		return WT_I18N::translate_c('father’s mother’s sibling’s spouse', 'great-aunt/uncle');
	case 'fatmotsishus':
		return WT_I18N::translate_c('father’s mother’s sister’s husband', 'great-uncle');
	case 'fatparbrowif':
		return WT_I18N::translate_c('father’s parent’s brother’s wife', 'great-aunt');
	case 'fatparsibspo':
		return WT_I18N::translate_c('father’s parent’s sibling’s spouse', 'great-aunt/uncle');
	case 'fatparsishus':
		return WT_I18N::translate_c('father’s parent’s sister’s husband', 'great-uncle');
	case 'motfatbrowif':
		return WT_I18N::translate_c('mother’s father’s brother’s wife', 'great-aunt');
	case 'motfatsibspo':
		return WT_I18N::translate_c('mother’s father’s sibling’s spouse', 'great-aunt/uncle');
	case 'motfatsishus':
		return WT_I18N::translate_c('mother’s father’s sister’s husband', 'great-uncle');
	case 'motmotbrowif':
		return WT_I18N::translate_c('mother’s mother’s brother’s wife', 'great-aunt');
	case 'motmotsibspo':
		return WT_I18N::translate_c('mother’s mother’s sibling’s spouse', 'great-aunt/uncle');
	case 'motmotsishus':
		return WT_I18N::translate_c('mother’s mother’s sister’s husband', 'great-uncle');
	case 'motparbrowif':
		return WT_I18N::translate_c('mother’s parent’s brother’s wife', 'great-aunt');
	case 'motparsibspo':
		return WT_I18N::translate_c('mother’s parent’s sibling’s spouse', 'great-aunt/uncle');
	case 'motparsishus':
		return WT_I18N::translate_c('mother’s parent’s sister’s husband', 'great-uncle');
	case 'parfatbrowif':
		return WT_I18N::translate_c('parent’s father’s brother’s wife', 'great-aunt');
	case 'parfatsibspo':
		return WT_I18N::translate_c('parent’s father’s sibling’s spouse', 'great-aunt/uncle');
	case 'parfatsishus':
		return WT_I18N::translate_c('parent’s father’s sister’s husband', 'great-uncle');
	case 'parmotbrowif':
		return WT_I18N::translate_c('parent’s mother’s brother’s wife', 'great-aunt');
	case 'parmotsibspo':
		return WT_I18N::translate_c('parent’s mother’s sibling’s spouse', 'great-aunt/uncle');
	case 'parmotsishus':
		return WT_I18N::translate_c('parent’s mother’s sister’s husband', 'great-uncle');
	case 'parparbrowif':
		return WT_I18N::translate_c('parent’s parent’s brother’s wife', 'great-aunt');
	case 'parparsibspo':
		return WT_I18N::translate_c('parent’s parent’s sibling’s spouse', 'great-aunt/uncle');
	case 'parparsishus':
		return WT_I18N::translate_c('parent’s parent’s sister’s husband', 'great-uncle');
	case 'fatfatbrodau':
		return WT_I18N::translate_c('father’s father’s brother’s daughter', 'first cousin once removed ascending');
	case 'fatfatbroson':
		return WT_I18N::translate_c('father’s father’s brother’s son', 'first cousin once removed ascending');
	case 'fatfatbrochi':
		return WT_I18N::translate_c('father’s father’s brother’s child', 'first cousin once removed ascending');
	case 'fatfatsisdau':
		return WT_I18N::translate_c('father’s father’s sister’s daughter', 'first cousin once removed ascending');
	case 'fatfatsisson':
		return WT_I18N::translate_c('father’s father’s sister’s son', 'first cousin once removed ascending');
	case 'fatfatsischi':
		return WT_I18N::translate_c('father’s father’s sister’s child', 'first cousin once removed ascending');
	case 'fatmotbrodau':
		return WT_I18N::translate_c('father’s mother’s brother’s daughter', 'first cousin once removed ascending');
	case 'fatmotbroson':
		return WT_I18N::translate_c('father’s mother’s brother’s son', 'first cousin once removed ascending');
	case 'fatmotbrochi':
		return WT_I18N::translate_c('father’s mother’s brother’s child', 'first cousin once removed ascending');
	case 'fatmotsisdau':
		return WT_I18N::translate_c('father’s mother’s sister’s daughter', 'first cousin once removed ascending');
	case 'fatmotsisson':
		return WT_I18N::translate_c('father’s mother’s sister’s son', 'first cousin once removed ascending');
	case 'fatmotsischi':
		return WT_I18N::translate_c('father’s mother’s sister’s child', 'first cousin once removed ascending');
	case 'motfatbrodau':
		return WT_I18N::translate_c('mother’s father’s brother’s daughter', 'first cousin once removed ascending');
	case 'motfatbroson':
		return WT_I18N::translate_c('mother’s father’s brother’s son', 'first cousin once removed ascending');
	case 'motfatbrochi':
		return WT_I18N::translate_c('mother’s father’s brother’s child', 'first cousin once removed ascending');
	case 'motfatsisdau':
		return WT_I18N::translate_c('mother’s father’s sister’s daughter', 'first cousin once removed ascending');
	case 'motfatsisson':
		return WT_I18N::translate_c('mother’s father’s sister’s son', 'first cousin once removed ascending');
	case 'motfatsischi':
		return WT_I18N::translate_c('mother’s father’s sister’s child', 'first cousin once removed ascending');
	case 'motmotbrodau':
		return WT_I18N::translate_c('mother’s mother’s brother’s daughter', 'first cousin once removed ascending');
	case 'motmotbroson':
		return WT_I18N::translate_c('mother’s mother’s brother’s son', 'first cousin once removed ascending');
	case 'motmotbrochi':
		return WT_I18N::translate_c('mother’s mother’s brother’s child', 'first cousin once removed ascending');
	case 'motmotsisdau':
		return WT_I18N::translate_c('mother’s mother’s sister’s daughter', 'first cousin once removed ascending');
	case 'motmotsisson':
		return WT_I18N::translate_c('mother’s mother’s sister’s son', 'first cousin once removed ascending');
	case 'motmotsischi':
		return WT_I18N::translate_c('mother’s mother’s sister’s child', 'first cousin once removed ascending');
	}

	// Some “special case” level five relationships that have specific names in certain languages
	if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandfather’s brother’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandfather’s brother’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandfather’s brother’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandfather’s sister’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandfather’s sister’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandfather’s sister’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandfather’s sibling’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandfather’s sibling’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandfather’s sibling’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motbro(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandmother’s brother’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motbro(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandmother’s brother’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motbro(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandmother’s brother’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motsis(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandmother’s sister’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motsis(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandmother’s sister’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motsis(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandmother’s sister’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motsib(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandmother’s sibling’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motsib(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandmother’s sibling’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)motsib(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandmother’s sibling’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parbro(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandparent’s brother’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parbro(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandparent’s brother’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parbro(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandparent’s brother’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parsis(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandparent’s sister’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parsis(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandparent’s sister’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parsis(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandparent’s sister’s grandchild', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parsib(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandparent’s sibling’s granddaughter', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parsib(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandparent’s sibling’s grandson', 'second cousin');
	} elseif (preg_match('/^(mot|fat|par)parsib(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandparent’s sibling’s grandchild', 'second cousin');
	}

	// Look for generic/pattern relationships.
	// TODO: these are heavily based on English relationship names.
	// We need feedback from other languages to improve this.
	// Dutch has special names for 8 generations of great-great-..., so these need explicit naming
	// Spanish has special names for four but also has two different numbering patterns

	if (preg_match('/^((?:mot|fat|par)+)(bro|sis|sib)$/', $path, $match)) {
		// siblings of direct ancestors
		$up = strlen($match[1]) / 3;
		$bef_last = substr($path, -6, 3);
		switch ($up) {
		case 3:
			switch ($sex2) {
			case 'M':
				if ($bef_last === 'fat') {
					return WT_I18N::translate_c('great-grandfather’s brother', 'great-great-uncle');
				} elseif ($bef_last === 'mot') {
					return WT_I18N::translate_c('great-grandmother’s brother', 'great-great-uncle');
				} else {
					return WT_I18N::translate_c('great-grandparent’s brother', 'great-great-uncle');
				}
			case 'F':
				return WT_I18N::translate('great-great-aunt');
			default:
				return WT_I18N::translate('great-great-aunt/uncle');
			}
		case 4:
			switch ($sex2) {
			case 'M':
				if ($bef_last === 'fat') {
					return WT_I18N::translate_c('great-great-grandfather’s brother', 'great-great-great-uncle');
				} elseif ($bef_last === 'mot') {
					return WT_I18N::translate_c('great-great-grandmother’s brother', 'great-great-great-uncle');
				} else {
					return WT_I18N::translate_c('great-great-grandparent’s brother', 'great-great-great-uncle');
				}
			case 'F':
				return WT_I18N::translate('great-great-great-aunt');
			default:
				return WT_I18N::translate('great-great-great-aunt/uncle');
			}
		case 5:
			switch ($sex2) {
			case 'M':
				if ($bef_last === 'fat') {
					return WT_I18N::translate_c('great-great-great-grandfather’s brother', 'great ×4 uncle');
				} elseif ($bef_last === 'mot') {
					return WT_I18N::translate_c('great-great-great-grandmother’s brother', 'great ×4 uncle');
				} else {
					return WT_I18N::translate_c('great-great-great-grandparent’s brother', 'great ×4 uncle');
				}
			case 'F':
				return WT_I18N::translate('great ×4 aunt');
			default:
				return WT_I18N::translate('great ×4 aunt/uncle');
			}
		case 6:
			switch ($sex2) {
			case 'M':
				if ($bef_last === 'fat') {
					return WT_I18N::translate_c('great ×4 grandfather’s brother', 'great ×5 uncle');
				} elseif ($bef_last === 'mot') {
					return WT_I18N::translate_c('great ×4 grandmother’s brother', 'great ×5 uncle');
				} else {
					return WT_I18N::translate_c('great ×4 grandparent’s brother', 'great ×5 uncle');
				}
			case 'F':
				return WT_I18N::translate('great ×5 aunt');
			default:
				return WT_I18N::translate('great ×5 aunt/uncle');
			}
		case 7:
			switch ($sex2) {
			case 'M':
				if ($bef_last === 'fat') {
					return WT_I18N::translate_c('great ×5 grandfather’s brother', 'great ×6 uncle');
				} elseif ($bef_last === 'mot') {
					return WT_I18N::translate_c('great ×5 grandmother’s brother', 'great ×6 uncle');
				} else {
					return WT_I18N::translate_c('great ×5 grandparent’s brother', 'great ×6 uncle');
				}
			case 'F':
				return WT_I18N::translate('great ×6 aunt');
			default:
				return WT_I18N::translate('great ×6 aunt/uncle');
			}
		case 8:
			switch ($sex2) {
			case 'M':
				if ($bef_last === 'fat') {
					return WT_I18N::translate_c('great ×6 grandfather’s brother', 'great ×7 uncle');
				} elseif ($bef_last === 'mot') {
					return WT_I18N::translate_c('great ×6 grandmother’s brother', 'great ×7 uncle');
				} else {
					return WT_I18N::translate_c('great ×6 grandparent’s brother', 'great ×7 uncle');
				}
			case 'F':
				return WT_I18N::translate('great ×7 aunt');
			default:
				return WT_I18N::translate('great ×7 aunt/uncle');
			}
		default:
			// Different languages have different rules for naming generations.
			// An English great ×12 uncle is a Danish great ×10 uncle.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'da':
				switch ($sex2) {
				case 'M':
					return WT_I18N::translate('great ×%d uncle', $up - 4);
				case 'F':
					return WT_I18N::translate('great ×%d aunt', $up - 4);
				default:
					return WT_I18N::translate('great ×%d aunt/uncle', $up - 4);
				}
			case 'pl':
				switch ($sex2) {
				case 'M':
					if ($bef_last === 'fat') {
						return WT_I18N::translate_c('great ×(%d-1) grandfather’s brother', 'great ×%d uncle', $up - 2);
					} elseif ($bef_last === 'mot') {
						return WT_I18N::translate_c('great ×(%d-1) grandmother’s brother', 'great ×%d uncle', $up - 2);
					} else {
						return WT_I18N::translate_c('great ×(%d-1) grandparent’s brother', 'great ×%d uncle', $up - 2);
					}
				case 'F':
					return WT_I18N::translate('great ×%d aunt', $up - 2);
				default:
					return WT_I18N::translate('great ×%d aunt/uncle', $up - 2);
				}
			case 'it': // Source: Michele Locati
			case 'en_AU':
			case 'en_GB':
			case 'en_US':
			default:
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
					return WT_I18N::translate('great ×%d uncle', $up - 1);
				case 'F':
					return WT_I18N::translate('great ×%d aunt', $up - 1);
				default:
					return WT_I18N::translate('great ×%d aunt/uncle', $up - 1);
				}
			}
		}
	}
	if (preg_match('/^(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match)) {
		// direct descendants of siblings
		$down = strlen($match[1]) / 3 + 1; // Add one, as we count generations from the common ancestor
		$first = substr($path, 0, 3);
		switch ($down) {
		case 4:
			switch ($sex2) {
			case 'M':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-grandson', 'great-great-nephew');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-grandson', 'great-great-nephew');
				} else {
					return WT_I18N::translate_c('(a woman’s) great-great-nephew', 'great-great-nephew');
				}
			case 'F':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-granddaughter', 'great-great-niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-granddaughter', 'great-great-niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great-great-niece', 'great-great-niece');
				}
			default:
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-grandchild', 'great-great-nephew/niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-grandchild', 'great-great-nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great-great-nephew/niece', 'great-great-nephew/niece');
				}
			}
		case 5:
			switch ($sex2) {
			case 'M':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-great-grandson', 'great-great-great-nephew');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-great-grandson', 'great-great-great-nephew');
				} else {
					return WT_I18N::translate_c('(a woman’s) great-great-great-nephew', 'great-great-great-nephew');
				}
			case 'F':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-great-granddaughter', 'great-great-great-niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-great-granddaughter', 'great-great-great-niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great-great-great-niece', 'great-great-great-niece');
				}
			default:
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-great-grandchild', 'great-great-great-nephew/niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-great-grandchild', 'great-great-great-nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great-great-great-nephew/niece', 'great-great-great-nephew/niece');
				}
			}
		case 6:
			switch ($sex2) {
			case 'M':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-great-great-grandson', 'great ×4 nephew');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-great-great-grandson', 'great ×4 nephew');
				} else {
					return WT_I18N::translate_c('(a woman’s) great ×4 nephew', 'great ×4 nephew');
				}
			case 'F':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-great-great-granddaughter', 'great ×4 niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-great-great-granddaughter', 'great ×4 niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great ×4 niece', 'great ×4 niece');
				}
			default:
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great-great-great-grandchild', 'great ×4 nephew/niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great-great-great-grandchild', 'great ×4 nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great ×4 nephew/niece', 'great ×4 nephew/niece');
				}
			}
		case 7:
			switch ($sex2) {
			case 'M':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great ×4 grandson', 'great ×5 nephew');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great ×4 grandson', 'great ×5 nephew');
				} else {
					return WT_I18N::translate_c('(a woman’s) great ×5 nephew', 'great ×5 nephew');
				}
			case 'F':
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great ×4 granddaughter', 'great ×5 niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great ×4 granddaughter', 'great ×5 niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great ×5 niece', 'great ×5 niece');
				}
			default:
				if ($first === 'bro' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) brother’s great ×4 grandchild', 'great ×5 nephew/niece');
				} elseif ($first === 'sis' && $sex1 === 'M') {
					return WT_I18N::translate_c('(a man’s) sister’s great ×4 grandchild', 'great ×5 nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman’s) great ×5 nephew/niece', 'great ×5 nephew/niece');
				}
			}
		default:
			// Different languages have different rules for naming generations.
			// An English great ×12 nephew is a Polish great ×11 nephew.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'pl': // Source: Lukasz Wilenski
				switch ($sex2) {
				case 'M':
					if ($first === 'bro' && $sex1 === 'M') {
						return WT_I18N::translate_c('(a man’s) brother’s great ×(%d-1) grandson', 'great ×%d nephew', $down - 3);
					} elseif ($first === 'sis' && $sex1 === 'M') {
						return WT_I18N::translate_c('(a man’s) sister’s great ×(%d-1) grandson', 'great ×%d nephew', $down - 3);
					} else {
						return WT_I18N::translate_c('(a woman’s) great ×%d nephew', 'great ×%d nephew', $down - 3);
					}
				case 'F':
					if ($first === 'bro' && $sex1 === 'M') {
						return WT_I18N::translate_c('(a man’s) brother’s great ×(%d-1) granddaughter', 'great ×%d niece', $down - 3);
					} elseif ($first === 'sis' && $sex1 === 'M') {
						return WT_I18N::translate_c('(a man’s) sister’s great ×(%d-1) granddaughter', 'great ×%d niece', $down - 3);
					} else {
						return WT_I18N::translate_c('(a woman’s) great ×%d niece', 'great ×%d niece', $down - 3);
					}
				default:
					if ($first === 'bro' && $sex1 === 'M') {
						return WT_I18N::translate_c('(a man’s) brother’s great ×(%d-1) grandchild', 'great ×%d nephew/niece', $down - 3);
					} elseif ($first === 'sis' && $sex1 === 'M') {
						return WT_I18N::translate_c('(a man’s) sister’s great ×(%d-1) grandchild', 'great ×%d nephew/niece', $down - 3);
					} else {
						return WT_I18N::translate_c('(a woman’s) great ×%d nephew/niece', 'great ×%d nephew/niece', $down - 3);
					}
				}
			case 'he': // Source: Meliza Amity
				switch ($sex2) {
				case 'M':
					return WT_I18N::translate('great ×%d nephew', $down - 1);
				case 'F':
					return WT_I18N::translate('great ×%d niece', $down - 1);
				default:
					return WT_I18N::translate('great ×%d nephew/niece', $down - 1);
				}
			case 'it': // Source: Michele Locati.
			case 'en_AU':
			case 'en_GB':
			case 'en_US':
			default:
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
					return WT_I18N::translate('great ×%d nephew', $down - 2);
				case 'F':
					return WT_I18N::translate('great ×%d niece', $down - 2);
				default:
					return WT_I18N::translate('great ×%d nephew/niece', $down - 2);
				}
			}
		}
	}
	if (preg_match('/^((?:mot|fat|par)*)$/', $path, $match)) {
		// direct ancestors
		$up = strlen($match[1]) / 3;
		switch ($up) {
		case 4:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great-great-grandfather');
			case 'F':
				return WT_I18N::translate('great-great-grandmother');
			default:
				return WT_I18N::translate('great-great-grandparent');
			}
		case 5:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great-great-great-grandfather');
			case 'F':
				return WT_I18N::translate('great-great-great-grandmother');
			default:
				return WT_I18N::translate('great-great-great-grandparent');
			}
		case 6:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×4 grandfather');
			case 'F':
				return WT_I18N::translate('great ×4 grandmother');
			default:
				return WT_I18N::translate('great ×4 grandparent');
			}
		case 7:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×5 grandfather');
			case 'F':
				return WT_I18N::translate('great ×5 grandmother');
			default:
				return WT_I18N::translate('great ×5 grandparent');
			}
		case 8:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×6 grandfather');
			case 'F':
				return WT_I18N::translate('great ×6 grandmother');
			default:
				return WT_I18N::translate('great ×6 grandparent');
			}
		case 9:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×7 grandfather');
			case 'F':
				return WT_I18N::translate('great ×7 grandmother');
			default:
				return WT_I18N::translate('great ×7 grandparent');
			}
		default:
			// Different languages have different rules for naming generations.
			// An English great ×12 grandfather is a Danish great ×11 grandfather.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'da': // Source: Patrick Sorensen
				switch ($sex2) {
				case 'M':
					return WT_I18N::translate('great ×%d grandfather', $up - 3);
				case 'F':
					return WT_I18N::translate('great ×%d grandmother', $up - 3);
				default:
					return WT_I18N::translate('great ×%d grandparent', $up - 3);
				}
			case 'it': // Source: Michele Locati
			case 'es': // Source: Wes Groleau
				switch ($sex2) {
				case 'M':
					return WT_I18N::translate('great ×%d grandfather', $up);
				case 'F':
					return WT_I18N::translate('great ×%d grandmother', $up);
				default:
					return WT_I18N::translate('great ×%d grandparent', $up);
				}
			case 'fr': // Source: Jacqueline Tetreault
			case 'fr_CA':
				switch ($sex2) {
				case 'M':
					return WT_I18N::translate('great ×%d grandfather', $up - 1);
				case 'F':
					return WT_I18N::translate('great ×%d grandmother', $up - 1);
				default:
					return WT_I18N::translate('great ×%d grandparent', $up - 1);
				}
			case 'nn': // Source: Hogne Røed Nilsen (https://bugs.launchpad.net/webtrees/+bug/1168553)
			case 'nb':
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
					return WT_I18N::translate('great ×%d grandfather', $up - 3);
				case 'F':
					return WT_I18N::translate('great ×%d grandmother', $up - 3);
				default:
					return WT_I18N::translate('great ×%d grandparent', $up - 3);
				}
			case 'en_AU':
			case 'en_GB':
			case 'en_US':
			default:
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
					return WT_I18N::translate('great ×%d grandfather', $up - 2);
				case 'F':
					return WT_I18N::translate('great ×%d grandmother', $up - 2);
				default:
					return WT_I18N::translate('great ×%d grandparent', $up - 2);
				}
			}
		}
	}
	if (preg_match('/^((?:son|dau|chi)*)$/', $path, $match)) {
		// direct descendants
		$up = strlen($match[1]) / 3;
		switch ($up) {
		case 4:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great-great-grandson');
			case 'F':
				return WT_I18N::translate('great-great-granddaughter');
			default:
				return WT_I18N::translate('great-great-grandchild');
			}
			break;
		case 5:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great-great-great-grandson');
			case 'F':
				return WT_I18N::translate('great-great-great-granddaughter');
			default:
				return WT_I18N::translate('great-great-great-grandchild');
			}
			break;
		case 6:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×4 grandson');
			case 'F':
				return WT_I18N::translate('great ×4 granddaughter');
			default:
				return WT_I18N::translate('great ×4 grandchild');
			}
			break;
		case 7:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×5 grandson');
			case 'F':
				return WT_I18N::translate('great ×5 granddaughter');
			default:
				return WT_I18N::translate('great ×5 grandchild');
			}
			break;
		case 8:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×6 grandson');
			case 'F':
				return WT_I18N::translate('great ×6 granddaughter');
			default:
				return WT_I18N::translate('great ×6 grandchild');
			}
			break;
		case 9:
			switch ($sex2) {
			case 'M':
				return WT_I18N::translate('great ×7 grandson');
			case 'F':
				return WT_I18N::translate('great ×7 granddaughter');
			default:
				return WT_I18N::translate('great ×7 grandchild');
			}
			break;
		default:
			// Different languages have different rules for naming generations.
			// An English great ×12 grandson is a Danish great ×11 grandson.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'nn': // Source: Hogne Røed Nilsen
			case 'nb':
			case 'da': // Source: Patrick Sorensen
				switch ($sex2) {
				case 'M':
					return WT_I18N::translate('great ×%d grandson', $up - 3);
				case 'F':
					return WT_I18N::translate('great ×%d granddaughter', $up - 3);
				default:
					return WT_I18N::translate('great ×%d grandchild', $up - 3);
				}
			case 'it': // Source: Michele Locati
			case 'es': // Source: Wes Groleau (adding doesn’t change behavior, but needs to be better researched)
			case 'en_AU':
			case 'en_GB':
			case 'en_US':
			default:
				switch ($sex2) {

				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
					return WT_I18N::translate('great ×%d grandson', $up - 2);
				case 'F':
					return WT_I18N::translate('great ×%d granddaughter', $up - 2);
				default:
					return WT_I18N::translate('great ×%d grandchild', $up - 2);
				}
			}
		}
	}
	if (preg_match('/^((?:mot|fat|par)+)(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match)) {
		// cousins in English
		$ascent = $match[1];
		$descent = $match[2];
		$up = strlen($ascent) / 3;
		$down = strlen($descent) / 3;
		$cousin = min($up, $down);  // Moved out of switch (en/default case) so that
		$removed = abs($down - $up);  // Spanish (and other languages) can use it, too.

		// Different languages have different rules for naming cousins.  For example,
		// an English “second cousin once removed” is a Polish “cousin of 7th degree”.
		//
		// Need to find out which languages use which rules.
		switch (WT_LOCALE) {
		case 'pl': // Source: Lukasz Wilenski
			return cousin_name($up + $down + 2, $sex2);
		case 'it':
			// Source: Michele Locati.  See italian_cousins_names.zip
			// http://webtrees.net/forums/8-translation/1200-great-xn-grandparent?limit=6&start=6
			return cousin_name($up + $down - 3, $sex2);
		case 'es':
			// Source: Wes Groleau.  See http://UniGen.us/Parentesco.html & http://UniGen.us/Parentesco-D.html
			if ($down == $up) {
				return cousin_name($cousin, $sex2);
			} elseif ($down < $up) {
				return cousin_name2($cousin + 1, $sex2, get_relationship_name_from_path('sib' . $descent, null, null));
			} else {
				switch ($sex2) {
				case 'M':
					return cousin_name2($cousin + 1, $sex2, get_relationship_name_from_path('bro' . $descent, null, null));
				case 'F':
					return cousin_name2($cousin + 1, $sex2, get_relationship_name_from_path('sis' . $descent, null, null));
				default:
					return cousin_name2($cousin + 1, $sex2, get_relationship_name_from_path('sib' . $descent, null, null));
				}
			}
		case 'en_AU': // See: http://en.wikipedia.org/wiki/File:CousinTree.svg
		case 'en_GB':
		case 'en_US':
		default:
			switch ($removed) {
			case 0:
				return cousin_name($cousin, $sex2);
			case 1:
				if ($up > $down) {
					/* I18N: %s=“fifth cousin”, etc. http://www.ancestry.com/learn/library/article.aspx?article=2856 */
					return WT_I18N::translate('%s once removed ascending', cousin_name($cousin, $sex2));
				} else {
					/* I18N: %s=“fifth cousin”, etc. http://www.ancestry.com/learn/library/article.aspx?article=2856 */
					return WT_I18N::translate('%s once removed descending', cousin_name($cousin, $sex2));
				}
			case 2:
				if ($up > $down) {
					/* I18N: %s=“fifth cousin”, etc. */
					return WT_I18N::translate('%s twice removed ascending', cousin_name($cousin, $sex2));
				} else {
					/* I18N: %s=“fifth cousin”, etc. */
					return WT_I18N::translate('%s twice removed descending', cousin_name($cousin, $sex2));
				}
			case 3:
				if ($up > $down) {
					/* I18N: %s=“fifth cousin”, etc. */
					return WT_I18N::translate('%s three times removed ascending', cousin_name($cousin, $sex2));
				} else {
					/* I18N: %s=“fifth cousin”, etc. */
					return WT_I18N::translate('%s three times removed descending', cousin_name($cousin, $sex2));
				}
			default:
				if ($up > $down) {
					/* I18N: %1$s=“fifth cousin”, etc., %2$d>=4 */
					return WT_I18N::translate('%1$s %2$d times removed ascending', cousin_name($cousin, $sex2), $removed);
				} else {
					/* I18N: %1$s=“fifth cousin”, etc., %2$d>=4 */
					return WT_I18N::translate('%1$s %2$d times removed descending', cousin_name($cousin, $sex2), $removed);
				}
			}
		}
	}

	// Split the relationship into sub-relationships, e.g., third-cousin’s great-uncle.
	// Try splitting at every point, and choose the path with the shorted translated name.

	$relationship = null;
	$path1 = substr($path, 0, 3);
	$path2 = substr($path, 3);
	while ($path2) {
		$tmp = WT_I18N::translate(
		// I18N: A complex relationship, such as “third-cousin’s great-uncle”
			'%1$s’s %2$s',
			get_relationship_name_from_path($path1, null, null), // TODO: need the actual people
			get_relationship_name_from_path($path2, null, null)
		);
		if (!$relationship || strlen($tmp) < strlen($relationship)) {
			$relationship = $tmp;
		}
		$path1 .= substr($path2, 0, 3);
		$path2 = substr($path2, 3);
	}

	return $relationship;
}

/**
 * get theme names
 *
 * function to get the names of all of the themes as an array
 * it searches the themes folder and reads the name from the theme_name variable
 * in the theme.php file.
 *
 * @throws Exception
 *
 * @return string[] An array of theme names and their corresponding folder
 */
function get_theme_names() {
	static $themes;

	if ($themes === null) {
		$themes = array();
		$d = dir(WT_ROOT . WT_THEMES_DIR);
		while (false !== ($folder = $d->read())) {
			if ($folder[0] !== '.' && $folder[0] !== '_' && is_dir(WT_ROOT . WT_THEMES_DIR . $folder) && file_exists(WT_ROOT . WT_THEMES_DIR . $folder . '/theme.php')) {
				$themefile = implode('', file(WT_ROOT . WT_THEMES_DIR . $folder . '/theme.php'));
				if (preg_match('/theme_name\s*=\s*"(.*)";/', $themefile, $match)) {
					$theme_name = WT_I18N::translate($match[1]);
					if (array_key_exists($theme_name, $themes)) {
						throw new Exception('More than one theme with the same name: ' . $theme_name);
					} else {
						$themes[$theme_name] = $folder;
					}
				}
			}
		}
		$d->close();
		uksort($themes, array('WT_I18N', 'strcasecmp'));
	}

	return $themes;
}

/**
 * Function to build an URL querystring from GET variables
 * Optionally, add/replace specified values
 *
 * @param null|string[] $overwrite
 * @param null|string  $separator
 *
 * @return string
 */
function get_query_url($overwrite = null, $separator = '&') {
	if (empty($_GET)) {
		$get = array();
	} else {
		$get = $_GET;
	}
	if (is_array($overwrite)) {
		foreach ($overwrite as $key => $value) {
			$get[$key] = $value;
		}
	}

	$query_string = '';
	foreach ($get as $key => $value) {
		if (!is_array($value)) {
			$query_string .= $separator . rawurlencode($key) . '=' . rawurlencode($value);
		} else {
			foreach ($value as $k => $v) {
				$query_string .= $separator . rawurlencode($key) . '%5B' . rawurlencode($k) . '%5D=' . rawurlencode($v);
			}
		}
	}
	$query_string = substr($query_string, strlen($separator)); // Remove leading “&amp;”
	if ($query_string) {
		return WT_SCRIPT_NAME . '?' . $query_string;
	} else {
		return WT_SCRIPT_NAME;
	}
}

/**
 * Generate a new XREF, unique across all family trees
 *
 * @param string  $type
 * @param integer $ged_id
 *
 * @return string
 */
function get_new_xref($type = 'INDI', $ged_id = WT_GED_ID) {
	global $WT_TREE;

	/** @var string[] Which tree preference is used for which record type */
	static $type_to_preference = array(
		'INDI' => 'GEDCOM_ID_PREFIX',
		'FAM'  => 'FAM_ID_PREFIX',
		'OBJE' => 'MEDIA_ID_PREFIX',
		'NOTE' => 'NOTE_ID_PREFIX',
		'SOUR' => 'SOURCE_ID_PREFIX',
		'REPO' => 'REPO_ID_PREFIX',
		'REPO' => 'REPO_ID_PREFIX',
	);

	if (array_key_exists($type, $type_to_preference)) {
		$prefix = $WT_TREE->getPreference($type_to_preference[$type]);
	} else {
		// Use the first non-underscore character
		$prefix = substr(trim($type, '_'), 0, 1);
	}

	$num = WT_DB::prepare("SELECT next_id FROM `##next_id` WHERE record_type=? AND gedcom_id=?")
		->execute(array($type, $ged_id))
		->fetchOne();

	// TODO?  If a gedcom file contains *both* inline and object based media, then
	// we could be generating an XREF that we will find later.  Need to scan the
	// entire gedcom for them?

	if (is_null($num)) {
		$num = 1;
		WT_DB::prepare("INSERT INTO `##next_id` (gedcom_id, record_type, next_id) VALUES(?, ?, 1)")
			->execute(array($ged_id, $type));
	}

	$statement = WT_DB::prepare(
		"SELECT i_id FROM `##individuals` WHERE i_id = ?" .
		" UNION ALL " .
		"SELECT f_id FROM `##families` WHERE f_id = ?" .
		" UNION ALL " .
		"SELECT s_id FROM `##sources` WHERE s_id = ?" .
		" UNION ALL " .
		"SELECT m_id FROM `##media` WHERE m_id = ?" .
		" UNION ALL " .
		"SELECT o_id FROM `##other` WHERE o_id = ?" .
		" UNION ALL " .
		"SELECT xref FROM `##change` WHERE xref = ?"
	);

	while ($statement->execute(array_fill(0, 6, $prefix . $num))->fetchOne()) {
		// Applications such as ancestry.com generate XREFs with numbers larger than
		// PHP’s signed integer.  MySQL can handle large integers.
		$num = WT_DB::prepare("SELECT 1+?")->execute(array($num))->fetchOne();
	}

	//-- update the next id number in the DB table
	WT_DB::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type=? AND gedcom_id=?")
		->execute(array($num + 1, $type, $ged_id));

	return $prefix . $num;
}

/**
 * Determines whether the passed in filename is a link to an external source (i.e. contains “://”)
 *
 * @param string $file
 *
 * @return boolean
 */
function isFileExternal($file) {
	return strpos($file, '://') !== false;
}
