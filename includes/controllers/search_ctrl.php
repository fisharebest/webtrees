<?php

/**
 * Controller for the Search Page
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009	PGV Development Team.  All rights reserved.
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
 *
 * @package webtrees
 * @subpackage Display
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_SEARCH_CTRL_PHP', '');

require_once WT_ROOT.'includes/controllers/basecontrol.php';

/**
 * Main controller class for the search page.
 */
class SearchControllerRoot extends BaseController {
	var $isPostBack = false;
	var $topsearch;
	var $srfams;
	var $srindi;
	var $srnote;
	var $srsour;
	var $resultsPageNum = 0;
	var $resultsPerPage = 50;
	var $totalResults = -1;
	var $totalGeneralResults = -1;
	var $indiResultsPrinted = -1;
	var $famResultsPrinted = -1;
	var $srcResultsPrinted = -1;
	var $multiResultsPerPage = -1;
	var $multiTotalResults = -1;
	var $query;
	var $myquery = "";
//	var $soundex = "Russell";
	var $soundex = "DaitchM";
	var $subaction = "";
	var $nameprt = "";
	var $tagfilter = "on";
	var $showasso = "off";
	var $multiquery="";
	var $mymultiquery;
	var $name="";
	var $myname;
	var $birthdate="";
	var $mybirthdate;
	var $birthplace="";
	var $mybirthplace;
	var $deathdate="";
	var $mydeathdate;
	var $deathplace="";
	var $mydeathplace;
	var $gender="";
	var $mygender;
	var $firstname="";
	var $myfirstname;
	var $lastname="";
	var $mylastname;
	var $place="";
	var $myplace;
	var $year="";
	var $myyear;
	var $sgeds = array ();
	var $Sites = array ();
	var $myindilist = array ();
	var $mysourcelist = array ();
	var $myfamlist = array ();
	var $mynotelist = array ();
	var $multisiteResults = array ();
	var $inputFieldNames = array ();
	var $replace = false;
	var $replaceNames = false;
	var $replacePlaces = false;
	var $replaceAll = false;
	var $replacePlacesWord = false;
	var $printplace = array();

	/**
	 * constructor
	 */
	function SearchControllerRoot() {
		parent :: BaseController();
	}
	/**
	 * Initialization function
	 */
	function init() {
		global $ALLOW_CHANGE_GEDCOM, $GEDCOM;

		if ($this->action=='') {
			$this->action='general';
		}

		if (!empty ($_REQUEST["topsearch"])) {
			$this->topsearch = true;
			$this->isPostBack = true;
			$this->srfams = 'yes';
			$this->srindi = 'yes';
			$this->srsour = 'yes';
			$this->srnote = 'yes';
		}

		// Get the query and remove slashes
		if (isset ($_REQUEST["query"])) {
			// Reset the "Search" text from the page header
			if ($_REQUEST["query"] == i18n::translate('Search') || strlen($_REQUEST["query"])<2 || preg_match("/^\.+$/", $_REQUEST["query"])>0) {
				$this->query="";
				$this->myquery="";
			} else {
				$this->query = $_REQUEST["query"];
				$this->myquery = htmlspecialchars($this->query,ENT_COMPAT,'UTF-8');
			}
		}
		if (isset ($_REQUEST["replace"])) {
			$this->replace = $_REQUEST["replace"];

			if(isset($_REQUEST["replaceNames"])) $this->replaceNames = true;
			if(isset($_REQUEST["replacePlaces"])) $this->replacePlaces = true;
			if(isset($_REQUEST["replacePlacesWord"])) $this->replacePlacesWord = true;
			if(isset($_REQUEST["replaceAll"])) $this->replaceAll = true;
		}

		// Aquire all the variables values from the $_REQUEST
		$varNames = array ("isPostBack", "action", "topsearch", "srfams", "srindi", "srsour", "srnote", "view", "soundex", "subaction", "nameprt", "tagfilter", "showasso", "resultsPageNum", "resultsPerPage", "totalResults", "totalGeneralResults", "indiResultsPrinted", "famResultsPrinted", "multiTotalResults", "srcResultsPrinted", "multiResultsPerPage", "myindilist", "mysourcelist", "mynotelist", "myfamlist");
		$this->setRequestValues($varNames);

		if (!$this->isPostBack) {
			// Enable the default gedcom for search
			$str = str_replace(array (".", "-", " "), array ("_", "_", "_"), $GEDCOM);
			$_REQUEST["$str"] = $str;
		}

		// Retrieve the gedcoms to search in
		$all_gedcoms=get_all_gedcoms();
		if ($ALLOW_CHANGE_GEDCOM && count($all_gedcoms)>1) {
			foreach ($all_gedcoms as $ged_id=>$gedcom) {
				$str = str_replace(array (".", "-", " "), array ("_", "_", "_"), $gedcom);
				if (isset ($_REQUEST["$str"]) || isset ($this->topsearch)) {
					$this->sgeds[$ged_id] = $gedcom;
					$_REQUEST["$str"] = 'yes';
				}
			}
		} else {
			$this->sgeds[WT_GED_ID] = $GEDCOM;
		}

		// Retrieve the sites that can be searched
		$this->Sites = get_server_list();

		// vars use for soundex search
		if (!empty ($_REQUEST["firstname"])) {
			$this->firstname = $_REQUEST["firstname"];
			$this->myfirstname = $this->firstname;
		} else {
			$this->firstname="";
			$this->myfirstname = "";
		}
		if (!empty ($_REQUEST["lastname"])) {
			$this->lastname = $_REQUEST["lastname"];
			$this->mylastname = $this->lastname;
		} else {
			$this->lastname="";
			$this->mylastname = "";
		}
		if (!empty ($_REQUEST["place"])) {
			$this->place = $_REQUEST["place"];
			$this->myplace = $this->place;
		} else {
			$this->place="";
			$this->myplace = "";
		}
		if (!empty ($_REQUEST["year"])) {
			$this->year = $_REQUEST["year"];
			$this->myyear = $this->year;
		} else {
			$this->year="";
			$this->myyear = "";
		}
		// Set the search result titles for soundex searches
		if ($this->firstname || $this->lastname || $this->place) {
			$this->myquery=htmlspecialchars(implode(' ', array($this->firstname, $this->lastname, $this->place)));
		};

		// vars use for multisite search
		if (!empty ($_REQUEST["multiquery"])) {
			$this->multiquery = $_REQUEST["multiquery"];
			$this->mymultiquery = $this->multiquery;
		} else {
			$this->multiquery="";
			$this->mymultiquery = "";
		}
		if (!empty ($_REQUEST["name"])) {
			$this->name = $_REQUEST["name"];
			$this->myname = $this->name;
		} else {
			$this->name="";
			$this->myname = "";
		}
		if (!empty ($_REQUEST["birthdate"])) {
			$this->birthdate = $_REQUEST["birthdate"];
			$this->mybirthdate = $this->birthdate;
		} else {
			$this->birthdate="";
			$this->mybirthdate = "";
		}
		if (!empty ($_REQUEST["birthplace"])) {
			$this->birthplace = $_REQUEST["birthplace"];
			$this->mybirthplace = $this->birthplace;
		} else {
			$this->birthplace="";
			$this->mybirthplace = "";
		}
		if (!empty ($_REQUEST["deathdate"])) {
			$this->deathdate = $_REQUEST["deathdate"];
			$this->mydeathdate = $this->deathdate;
		} else {
			$this->deathdate="";
			$this->mydeathdate = "";
		}
		if (!empty ($_REQUEST["deathplace"])) {
			$this->deathplace = $_REQUEST["deathplace"];
			$this->mydeathplace = $this->deathplace;
		} else {
			$this->deathplace="";
			$this->mydeathplace = "";
		}
		if (!empty ($_REQUEST["gender"])) {
			$this->gender = $_REQUEST["gender"];
			$this->mygender = $this->gender;
		} else {
			$this->gender="";
			$this->mygender = "";
		}

		$this->inputFieldNames[] = "action";
		$this->inputFieldNames[] = "isPostBack";
		$this->inputFieldNames[] = "resultsPerPage";
		$this->inputFieldNames[] = "query";
		$this->inputFieldNames[] = "srindi";
		$this->inputFieldNames[] = "srfams";
		$this->inputFieldNames[] = "srsour";
		$this->inputFieldNames[] = "srnote";
		$this->inputFieldNames[] = "showasso";
		$this->inputFieldNames[] = "firstname";
		$this->inputFieldNames[] = "lastname";
		$this->inputFieldNames[] = "place";
		$this->inputFieldNames[] = "year";
		$this->inputFieldNames[] = "soundex";
		$this->inputFieldNames[] = "nameprt";
		$this->inputFieldNames[] = "subaction";
		$this->inputFieldNames[] = "multiquery";
		$this->inputFieldNames[] = "name";
		$this->inputFieldNames[] = "birthdate";
		$this->inputFieldNames[] = "birthplace";
		$this->inputFieldNames[] = "deathdate";
		$this->inputFieldNames[] = "deathplace";
		$this->inputFieldNames[] = "gender";
		$this->inputFieldNames[] = "tagfilter";

		// Get the search results based on the action
		if (isset ($this->topsearch)) {
			$this->TopSearch();
		}
		// If we want to show associated persons, build the list
		switch ($this->action) {
		case 'general':
			$this->GeneralSearch();
			break;
		case 'soundex':
			$this->SoundexSearch();
			break;
		case 'replace':
			$this->SearchAndReplace();
			return;
		case 'multisite':
			$this->MultiSiteSearch();
			break;

		}
	}

	function getPageTitle() {
		switch ($this->action) {
		case 'general':
			return i18n::translate('General Search');
		case 'soundex':
			return i18n::translate('Soundex Search');
		case 'replace':
			return i18n::translate('Search and Replace');
		case 'multisite':
			return i18n::translate('Multi Site Search');
		}
	}

	/**
	 * setRequestValues - Checks if the variable names ($varNames) are in
	 * 					  the $_REQUEST and if so assigns their values to
	 * 					  $this based on the variable name ($this->$varName).
	 *
	 * @param array $varNames - Array of variable names(strings).
	 */
	function setRequestValues($varNames) {
		foreach ($varNames as $key => $varName) {
			if (isset ($_REQUEST[$varName]))
			{
				if($varName == "action")
				if($_REQUEST[$varName] == "replace")
				if(!WT_USER_CAN_ACCEPT)
				{
					$this->action = "general";
					continue;
				}
				$this-> $varName = $_REQUEST[$varName];
			}
		}
	}

	/**
	 * setRequestValues - Prints out all of the variable names and their
	 * 					  values based on the variable name ($this->$varName).
	 *
	 * @param array $varNames - Array of variable names(strings).
	 */
	function printVars($varNames) {
		foreach ($varNames as $key => $varName) {
			print $varName.": ".$this-> $varName."<br/>";
		}
	}

	/**
	 * Handles searches entered in the top search box in the themes and
	 * prepares the search to do a general search on indi's, fams, and sources.
	 */
	function TopSearch() {
		global $SHOW_SOURCES, $GEDCOM;
		// first set some required variables. Search only in current gedcom, only in indi's.
		$this->srindi = "yes";

		// Enable the default gedcom for search
		$str = str_replace(array (".", "-", " "), array ("_", "_", "_"), $GEDCOM);
		$_REQUEST["$str"] = "yes";

		// Then see if an ID is typed in. If so, we might want to jump there.
		if (isset ($this->query)) {
			$record=GedcomRecord::getInstance($this->query);
			if ($record && $record->canDisplayDetails()) {
				header("Location: ".encode_url($record->getLinkUrl(), false));
				exit;
			}
		}
	}

	/**
	 * 	Gathers results for a general search
	 */
	function GeneralSearch() {
		global $GEDCOM;
		$oldged = $GEDCOM;

		// Split search terms into an array
		$query_terms=array();
		$query=$this->query;
		// Words in double quotes stay together
		while (preg_match('/"([^"]+)"/', $query, $match)) {
			$query_terms[]=trim($match[1]);
			$query=str_replace($match[0], '', $query);
		}
		// Other words get treated separately
		while (preg_match('/[\S]+/', $query, $match)) {
			$query_terms[]=trim($match[0]);
			$query=str_replace($match[0], '', $query);
		}

		//-- perform the search
		if ($query_terms && $this->sgeds) {
			// Write a log entry
			$logstring = "Type: General<br />Query: ".$this->query;
			AddToSearchlog($logstring, $this->sgeds);

			// Search the indi's
			if (isset ($this->srindi)) {
				$this->myindilist=search_indis($query_terms, array_keys($this->sgeds), 'AND', $this->tagfilter=='on');
			} else {
				$this->myindilist=array();
			}

			// Search the fams
			if (isset ($this->srfams)) {
				$this->myfamlist=array_merge(
					search_fams($query_terms, array_keys($this->sgeds), 'AND', $this->tagfilter=='on'),
					search_fams_names($query_terms, array_keys($this->sgeds), 'AND')
				);
				$this->myfamlist=array_unique($this->myfamlist);
			} else {
				$this->myfamlist=array();
			}

			// Search the sources
			if (isset ($this->srsour)) {
				if (!empty ($this->query))
				$this->mysourcelist=search_sources($query_terms, array_keys($this->sgeds), 'AND', $this->tagfilter=='on');
			} else {
				$this->mysourcelist=array();
			}

			// Search the notes
			if (isset ($this->srnote)) {
				if (!empty ($this->query))
				$this->mynotelist=search_notes($query_terms, array_keys($this->sgeds), 'AND', $this->tagfilter=='on');
			} else {
				$this->mynotelist=array();
			}

			// If only 1 item is returned, automatically forward to that item
			// If ID cannot be displayed, continue to the search page.
			if (count($this->myindilist)==1 && !$this->myfamlist && !$this->mysourcelist && !$this->mynotelist) {
				$indi=$this->myindilist[0];
				if (!count_linked_indi($indi->getXref(), 'ASSO', $indi->getGedId()) && !count_linked_fam($indi->getXref(), 'ASSO', $indi->getGedId()) && $indi->canDisplayName()) {
					header("Location: ".encode_url($indi->getLinkUrl(), false));
					exit;
				}
			}
			if (!$this->myindilist && count($this->myfamlist)==1 && !$this->mysourcelist && !$this->mynotelist) {
				$fam=$this->myfamlist[0];
				if ($fam->canDisplayName()) {
					header("Location: ".encode_url($fam->getLinkUrl(), false));
					exit;
				}
			}
			if (!$this->myindilist && !$this->myfamlist && count($this->mysourcelist)==1 && !$this->mynotelist) {
				$sour=$this->mysourcelist[0];
				if ($sour->canDisplayName()) {
					header("Location: ".encode_url($sour->getLinkUrl(), false));
					exit;
				}
			}
			if (!$this->myindilist && !$this->myfamlist && !$this->mysourcelist && count($this->mynotelist)==1) {
				$note=$this->mynotelist[0];
				if ($note->canDisplayName()) {
					header("Location: ".encode_url($note->getLinkUrl(), false));
					exit;
				}
			}
		}
	}

	/**
	 *  Preforms a search and replace
	 */
	function SearchAndReplace()
	{
		global $GEDCOM, $pgv_changes, $manual_save, $STANDARD_NAME_FACTS, $ADVANCED_NAME_FACTS;

		$this->sgeds = array($GEDCOM);
		$this->srindi = "yes";
		$this->srfams = "yes";
		$this->srsour = "yes";
		$this->srnote = "yes";
		$oldquery = $this->query;
		$this->GeneralSearch();

		//-- don't try to make any changes if nothing was found
		if (!$this->myindilist && !$this->myfamlist && !$this->mysourcelist && !$this->mynotelist) {
			return;
		}

		AddToLog("Search And Replace old:".$oldquery." new:".$this->replace, 'edit');
		$manual_save = true;
		// Include edit functions.
		require_once WT_ROOT.'includes/functions/functions_edit.php';
		// These contain the search query and the replace string
		// $this->replace;
		// $this->query;

		// These contain the search results
		// We need to iterate through them and do the replaces
		//$this->myindilist;
		$adv_name_tags = preg_split("/[\s,;: ]+/", $ADVANCED_NAME_FACTS);
		$name_tags = array_unique(array_merge($STANDARD_NAME_FACTS, $adv_name_tags));
		$name_tags[] = "_MARNM";
		foreach($this->myindilist as $id=>$individual) {
			if (isset($pgv_changes[$individual->getXref().'_'.$GEDCOM])) {
				$indirec=find_updated_record($individual->getXref(), WT_GED_ID);
			} else {
				$indirec=$individual->getGedcomRecord();
			}
			$oldRecord = $indirec;
			$newRecord = $indirec;
			if($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			} else {
				if($this->replaceNames) {
					foreach($name_tags as $f=>$tag) {
						$newRecord = preg_replace("~(\d) ".$tag." (.*)".$oldquery."(.*)~i",	"$1 ".$tag." $2".$this->replace."$3", $newRecord);
					}
				}
				if($this->replacePlaces) {
					if ($this->replacePlacesWord) $newRecord = preg_replace('~(\d) PLAC (.*)([,\W\s])'.$oldquery.'([,\W\s])~i',	"$1 PLAC $2$3".$this->replace."$4",$newRecord);
					else $newRecord = preg_replace("~(\d) PLAC (.*)".$oldquery."(.*)~i",	"$1 PLAC $2".$this->replace."$3",$newRecord);
				}
			}
			//-- if the record changed replace the record otherwise remove it from the search results
			if($newRecord != $oldRecord) {
				replace_gedrec($individual->getXref(), $newRecord);
			} else {
				unset($this->myindilist[$id]);
			}
		}

		foreach($this->myfamlist as $id=>$family) {
			if (isset($pgv_changes[$family->getXref().'_'.$GEDCOM])) {
				$indirec=find_updated_record($family->getXref(), WT_GED_ID);
			} else {
				$indirec=$family->getGedcomRecord();
			}
			$oldRecord = $indirec;
			$newRecord = $indirec;

			if($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			}
			else {
				if($this->replacePlaces) {
					if ($this->replacePlacesWord) $newRecord = preg_replace('~(\d) PLAC (.*)([,\W\s])'.$oldquery.'([,\W\s])~i',	"$1 PLAC $2$3".$this->replace."$4",$newRecord);
					else $newRecord = preg_replace("~(\d) PLAC (.*)".$oldquery."(.*)~i",	"$1 PLAC $2".$this->replace."$3",$newRecord);
				}
			}
			//-- if the record changed replace the record otherwise remove it from the search results
			if($newRecord != $oldRecord) {
				replace_gedrec($family->getXref(), $newRecord);
			} else {
				unset($this->myfamlist[$id]);
			}
		}

		foreach ($this->mysourcelist as $id=>$source) {
			if (isset($pgv_changes[$source->getXref().'_'.$GEDCOM])) {
				$indirec=find_updated_record($source->getXref(), WT_GED_ID);
			} else {
				$indirec=$source->getGedcomRecord();
			}
			$oldRecord = $indirec;
			$newRecord = $indirec;

			if ($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			} else {
				if($this->replaceNames) {
					$newRecord = preg_replace("~(\d) TITL (.*)".$oldquery."(.*)~i",	"$1 TITL $2".$this->replace."$3", $newRecord);
					$newRecord = preg_replace("~(\d) ABBR (.*)".$oldquery."(.*)~i",	"$1 ABBR $2".$this->replace."$3", $newRecord);
				}
				if($this->replacePlaces) {
					if ($this->replacePlacesWord) $newRecord = preg_replace('~(\d) PLAC (.*)([,\W\s])'.$oldquery.'([,\W\s])~i',	"$1 PLAC $2$3".$this->replace."$4",$newRecord);
					else $newRecord = preg_replace("~(\d) PLAC (.*)".$oldquery."(.*)~i",	"$1 PLAC $2".$this->replace."$3",$newRecord);
				}
			}
			//-- if the record changed replace the record otherwise remove it from the search results
			if($newRecord != $oldRecord) {
				replace_gedrec($source->getXref(), $newRecord);
			}	else {
				unset($this->mysourcelist[$id]);
			}
		}

		foreach ($this->mynotelist as $id=>$note) {
			if (isset($pgv_changes[$note->getXref().'_'.$GEDCOM])) {
				$indirec=find_updated_record($note->getXref(), WT_GED_ID);
			} else {
				$indirec=$note->getGedcomRecord();
			}
			$oldRecord = $indirec;
			$newRecord = $indirec;

			if ($this->replaceAll) {
				$newRecord = preg_replace("~".$oldquery."~i", $this->replace, $newRecord);
			}
			//-- if the record changed replace the record otherwise remove it from the search results
			if($newRecord != $oldRecord) {
				replace_gedrec($note->getXref(), $newRecord);
			}	else {
				unset($this->mynotelist[$id]);
			}
		}

		write_changes();
	}

	/**
	 * 	Gathers results for a soundex search
	 *
	 *  TODO
	 *  ====
	 *  Does not search on the selected gedcoms, searches on all the gedcoms
	 *  Does not work on first names, instead of the code, value array is used in the search
	 *  Returns all the names even when Names with hit selected
	 *  Does not sort results by first name
	 *  Does not work on separate double word surnames
	 *  Does not work on duplicate code values of the searched text and does not give the correct code
	 *     Cohen should give DM codes 556000, 456000, 460000 and 560000, in 4.1 we search only on 560000??
	 *
	 *  The names' Soundex SQL table contains all the soundex values twice
	 *  The places table contains only one value
	 *
	 *  The code should be improved - see RFE
	 *
	 */
	function SoundexSearch() {
		if (((!empty ($this->lastname)) || (!empty ($this->firstname)) || (!empty ($this->place))) && (count($this->sgeds) > 0)) {
			$logstring = "Type: Soundex<br />";
			if (!empty ($this->lastname))
			$logstring .= "Last name: ".$this->lastname."<br />";
			if (!empty ($this->firstname))
			$logstring .= "First name: ".$this->firstname."<br />";
			if (!empty ($this->place))
			$logstring .= "Place: ".$this->place."<br />";
			if (!empty ($this->year))
			$logstring .= "Year: ".$this->year."<br />";
			AddToSearchlog($logstring, $this->sgeds);

			if ($this->sgeds) {
				$this->myindilist=search_indis_soundex($this->soundex, $this->lastname, $this->firstname, $this->place, array_keys($this->sgeds));
			} else {
				$this->myindilist=array();
			}
		}

		// Now we have the final list of indi's to be printed.
		// We may add the assos at this point.

		if ($this->showasso == "on") {
			foreach ($this->myindilist as $indi) {
				foreach (fetch_linked_indi($indi->getXref(), 'ASSO', $indi->getGedId()) as $asso) {
					$this->myindilist[]=$asso;
				}
				foreach (fetch_linked_fam($indi->getXref(), 'ASSO', $indi->getGedId()) as $asso) {
					$this->myfamlist[]=$asso;
				}
			}
		}

		//-- if only 1 item is returned, automatically forward to that item
		if (count($this->myindilist)==1 && $this->action!="replace") {
			$indi=$this->myindilist[0];
			header("Location: ".encode_url($indi->getLinkUrl(), false));
			exit;
		}
		usort($this->myindilist, array('GedcomRecord', 'Compare'));
		usort($this->myfamlist, array('GedcomRecord', 'Compare'));
	}

	/**
	 *
	 */
	function MultiSiteSearch() {
		require_once WT_ROOT.'includes/classes/class_serviceclient.php';

		if (!empty ($this->Sites) && count($this->Sites) > 0) {
			$this->myindilist = array ();
			// This first tests to see if it just a basic site search
			if (!empty ($this->multiquery) && ($this->subaction == "basic")) {
				// Find out if the string is longer then one char if dont perform the search
				if (strlen($this->multiquery) > 1) {
					$my_query = $this->multiquery;
					// Now see if there is a query left after the cleanup
					if (trim($my_query) != "") {
						// Cleanup the querystring so it can be used in a database query
						$my_query = "%".preg_replace("/\s+/", "%", $my_query)."%";
					}
				}

			} else
			if (($this->subaction == "advanced") && (!empty ($this->myname) || !empty ($this->mybirthdate) || !empty ($this->mybirthplace) || !empty ($this->deathdate) || !empty ($this->mydeathplace) || !empty ($this->mygender))) {
				//Building the query string up
				$my_query = '';
				if (!empty ($this->myname)) {
					$my_query .= "NAME=".$this->myname;
				}
				if (!empty ($this->mybirthdate)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "BIRTHDATE=".$this->mybirthdate;
				}
				if (!empty ($this->mybirthplace)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "BIRTHPLACE=".$this->mybirthplace;
				}
				if (!empty ($this->deathdate)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "DEATHDATE=".$this->deathdate;
				}
				if (!empty ($this->mydeathplace)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query."DEATHPLACE=".$this->mydeathplace;
				}
				if (!empty ($this->mygender)) {
					if ($my_query != '')
					$my_query .= '&';
					$my_query .= "GENDER=".$this->mygender;
				}
			}

			if (!empty ($my_query)) {
				$this->multisiteResults = array ();
				// loop through the selected site to search
				$i = 0;
				foreach ($this->Sites as $key => $site) {
					$vartemp = "server".$i;
					if (isset ($_REQUEST["$vartemp"])) {
						$serviceClient = ServiceClient :: getInstance($key);
						$result = $serviceClient->search($my_query);
						$this->multisiteResults[$key] = $result;
					}
					$i++;
				}
			}
		}
	}

	function printResults() {
		require_once WT_ROOT.'includes/functions/functions_print_lists.php';
		global $GEDCOM, $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $global_facts;
		//-- all privacy settings must be global if we are going to load up privacy files
		global $SHOW_DEAD_PEOPLE,$SHOW_LIVING_NAMES,$SHOW_SOURCES,$MAX_ALIVE_AGE,$USE_RELATIONSHIP_PRIVACY,$MAX_RELATION_PATH_LENGTH;
		global $CHECK_MARRIAGE_RELATIONS,$PRIVACY_BY_YEAR,$PRIVACY_BY_RESN,$SHOW_PRIVATE_RELATIONSHIPS,$person_privacy,$user_privacy;
		global $global_facts,$person_facts;
		$somethingPrinted = false;	// whether anything printed
		// ---- section to search and display results on a general keyword search
		if ($this->action=="general" || $this->action=="soundex" || $this->action=="replace") {
			if ($this->myindilist || $this->myfamlist || $this->mysourcelist || $this->mynotelist) {
				echo '<br />';

				$OLD_GEDCOM=$GEDCOM;
				// Split individuals by gedcom
				foreach ($this->sgeds as $ged_id=>$gedcom) {
					$datalist = array();
					foreach ($this->myindilist as $individual) {
						if ($individual->getGedId()==get_id_from_gedcom($gedcom)) {
							$datalist[]=$individual;
						}
					}
					if ($datalist) {
						$somethingPrinted = true;
						usort($datalist, array('GedcomRecord', 'Compare'));
						$GEDCOM=$gedcom;
						print_indi_table($datalist, i18n::translate('Individuals').' : &laquo;'.$this->myquery.'&raquo; @ '.PrintReady(get_gedcom_setting($ged_id, 'title'), true));
					}
				}
				// Split families by gedcom
				foreach ($this->sgeds as $ged_id=>$gedcom) {
					$datalist = array();
					foreach ($this->myfamlist as $family) {
						if ($family->getGedId()==get_id_from_gedcom($gedcom)) {
							$datalist[]=$family;
						}
					}
					if ($datalist) {
						$somethingPrinted = true;
						usort($datalist, array('GedcomRecord', 'Compare'));
						$GEDCOM=$gedcom;
						print_fam_table($datalist, i18n::translate('Families').' : &laquo;'.$this->myquery.'&raquo; @ '.PrintReady(get_gedcom_setting($ged_id, 'title'), true));
					}
				}
				// Split sources by gedcom
				foreach ($this->sgeds as $ged_id=>$gedcom) {
					$datalist = array();
					foreach ($this->mysourcelist as $source) {
						if ($source->getGedId()==get_id_from_gedcom($gedcom)) {
							$datalist[]=$source;
						}
					}
					if ($datalist) {
						$somethingPrinted = true;
						usort($datalist, array('GedcomRecord', 'Compare'));
						$GEDCOM=$gedcom;
						print_sour_table($datalist, i18n::translate('Sources').' : &laquo;'.$this->myquery.'&raquo; @ '.PrintReady(get_gedcom_setting($ged_id, 'title'), true));
					}
				}
				// Split notes by gedcom
				foreach ($this->sgeds as $ged_id=>$gedcom) {
					$datalist = array();
					foreach ($this->mynotelist as $note) {
						if ($note->getGedId()==get_id_from_gedcom($gedcom)) {
							$datalist[]=$note;
						}
					}
					if ($datalist) {
						$somethingPrinted = true;
						usort($datalist, array('GedcomRecord', 'Compare'));
						$GEDCOM=$gedcom;
						print_note_table($datalist, i18n::translate('Notes').' : &laquo;'.$this->myquery.'&raquo; @ '.PrintReady(get_gedcom_setting($ged_id, 'title'), true));
					}
				}
				$GEDCOM=$OLD_GEDCOM;
			} else
			if (isset ($this->query)) {
				print "<br /><div class=\"warning\" style=\" text-align: center;\"><i>".i18n::translate('No results found.')."</i><br />";
				if (!isset ($this->srindi) && !isset ($this->srfams) && !isset ($this->srsour) && !isset ($this->srnote)) {
					print "<i>".i18n::translate('Be sure to select an option to search for.')."</i><br />";
				}
				echo '</div>';
			}
			// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
			if ($this->resultsPerPage >= 1 && $this->totalGeneralResults > $this->resultsPerPage) {
				$this->printPageResultsLinks($this->inputFieldNames, $this->totalGeneralResults, $this->resultsPerPage);
			}
		}

		// ----- section to search and display results for multisite
		if ($this->action == "multisite") {
			// Only Display 5 results per 2 sites if the total results per page is 10
			$sitesChecked = 0;
			$i = 0;
			foreach ($this->Sites as $server) {
				$siteName = "server".$i;
				if (isset ($_REQUEST["$siteName"]))
				$sitesChecked ++;
				$i ++;
			}
			if ($sitesChecked >= 1) {
				$this->multiResultsPerPage = $this->resultsPerPage / $sitesChecked;

				if (!empty ($this->Sites) && count($this->Sites) > 0) {
					$no_results_found = false;
					// Start output here, because from the indi's we may have printed some fams which need the column header.
					print "<br />";
					print "<div class=\"center\">";

					if (isset ($this->multisiteResults) && (count($this->multisiteResults) > 0)) {
						$this->totalResults = 0;
						$this->multiTotalResults = 0;
						$somethingPrinted = true;
						foreach ($this->multisiteResults as $key => $siteResults) {
							require_once WT_ROOT.'includes/classes/class_serviceclient.php';
							$serviceClient = ServiceClient :: getInstance($key);
							$siteName = $serviceClient->getServiceTitle();
							$siteURL = dirname($serviceClient->getURL());

							print "<table id=\"multiResultsOutTbl\" class=\"list_table, $TEXT_DIRECTION\" align=\"center\">";

							if (isset ($siteResults) && !empty ($siteResults->persons)) {
								$displayed_once = false;
								$personlist = $siteResults->persons;

								/***************************************************** PAGING HERE **********************************************************************/

								//set the total results and only get the results for this page
								$this->multiTotalResults += count($personlist);
								if ($this->totalResults < $this->multiTotalResults)
								$this->totalResults = $this->multiTotalResults;
								$personlist = $this->getPagedResults($personlist, $this->multiResultsPerPage);
								$pageResultsNum = 0;
								foreach ($personlist as $index => $person) {
									//if there is a name to display then diplay it
									if (!empty ($person->gedcomName)) {
										if (!$displayed_once) {
											if (!$no_results_found) {
												$no_results_found = true;
												print "<tr><td class=\"list_label\" colspan=\"2\" width=\"100%\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["indis"]["large"]."\" border=\"0\" width=\"25\" alt=\"\" /> ".i18n::translate('People')."</td></tr>";
												print "<tr><td><table id=\"multiResultsInTbl\" align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" ><tr>";
											}
											$displayed_once = true;
											print "<td class=\"list_label\" colspan=\"2\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >".i18n::translate('Site: ')."<a href=\"".encode_url($siteURL)."\" target=\"_blank\">".$siteName."</a>".i18n::translate(' contained the following')."</td></tr>";
										}
										print "<tr><td class=\"list_value $TEXT_DIRECTION\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" valign=\"center\" ><ul>";
										print "<li class=\"$TEXT_DIRECTION\" dir=\"$TEXT_DIRECTION\">";
										print "<a href=\"".encode_url("{$siteURL}/individual.php?pid={$person->PID}&ged={$serviceClient->gedfile}")."\" target=\"_blank\">";
										$pageResultsNum += 1;
										print "<b>".$person->getFullName()."</b>";
										if (!empty ($person->PID)) {
											print " (".$person->PID.")";
										}
										if (!empty ($person->birthDate) || !empty ($person->birthPlace)) {
											print " -- <i>";
											if (!empty ($person->birthDate)) {
												print " ".$person->birthDate;
											}
											if (!empty ($person->birthPlace)) {
												print " ".$person->birthPlace;
											}
											print "</i>";
										}
										print "</a></li></ul></td>";

										/*******************************  Remote Links Per Result *************************************************/
										if (WT_USER_CAN_EDIT) {
											print "<td class=\"list_value $TEXT_DIRECTION\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" >"."<ul style=\"list-style: NONE\"><li><a href=\"javascript:;\" "."onclick=\"return open_link('".$key."', '".$person->PID."', '".$person->getFullName()."');\">"."<b>".i18n::translate('Add Local Link')."</b></a></ul></li></td></tr>\n";
										}
									}
								}

								print "</table>";

								print "\n\t\t&nbsp;</td></tr></table>";
							}
							if ($this->multiTotalResults > 0) {
								print "</tr><tr><td align=\"left\">Displaying individuals ";
								print (($this->multiResultsPerPage * $this->resultsPageNum) + 1)." ".i18n::translate('to')." ". (($this->multiResultsPerPage * $this->resultsPageNum) + $pageResultsNum);
								print " ".i18n::translate('of')." ". ($this->multiTotalResults)."</td></tr></table>";
								$this->multiTotalResults = 0;
							} else
							print "</tr></table>";
						}
						print "</table>";
					}
					echo '</div>';
					if (!$no_results_found && $this->multiTotalResults == 0 && (isset ($this->multiquery) || isset ($this->name) || isset ($this->birthdate) || isset ($this->birthplace) || isset ($this->deathdate) || isset ($this->deathplace) || isset ($this->gender))) {
						print "<table align=\"center\" \><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".i18n::translate('No results found.')."</i></b></font><br /></td></tr></table>";
					}
				}
			} else
			if ($sitesChecked < 1 && $this->isPostBack) {
				print "<table align=\"center\" \><tr><td class=\"warning\" style=\" text-align: center;\"><font color=\"red\"><b><i>".i18n::translate('Be sure to select at least one remote site.')."</i></b></font><br /></td></tr></table>";
			}

			print "</table>";
			// Prints the Paged Results: << 1 2 3 4 >> links if there are more than $this->resultsPerPage results
			if ($this->resultsPerPage > 1 && $this->totalResults > $this->resultsPerPage) {
				$this->printPageResultsLinks($this->inputFieldNames, $this->totalResults, $this->multiResultsPerPage);
			}
		}
		return $somethingPrinted;	// whether anything printed
	}

	/************************************************   Helper Methods ****************************************************************/

	/**
	 * Function that returns only the results for the current page
	 * i.e. if $controller->resultsPageNum == 2 and $resultsPerPage == 10 this
	 * function would return results 11 - 20.
	 *
	 * @param array() $results - the original results.
	 * @param int $resultsPerPage - If $results count is less
	 * than $resultsPerPage it will simply return $results.
	 * @return array - the filtered results i.e. 11-20.
		*/
	function getPagedResults($results, $resultsPerPage) {
		$len = count($results);
		if ($len <= $resultsPerPage) {
			if ($this->resultsPageNum==0) return $results;
			else return array();
		}
		$pagedResults = array ();
		$startPosition = $this->resultsPageNum * $resultsPerPage;
		$endPosition = ($this->resultsPageNum + 1) * $resultsPerPage;
		$i = 0;
		if (isset ($results) && $len > 0) {
			foreach ($results as $key => $value) {
				if ($i >= $startPosition)
				$pagedResults[$key] = $value;
				$i ++;
				if ($i >= $endPosition)
				break;
			}
			return $pagedResults;
		}
		return array();
	}

	/**
	 * prints out the paging links for a page with many results i.e.  Result Page:   << 1 2 3 4 5 >>
	 *
	 * @param $this->inputFieldNames - an array of strings representing the names of the variables to include
	 * in the query string usually from input values in a form i.e. 'action', 'query', 'showasso' etc.
	 */
	function printPageResultsLinks($inputFieldNames, $totalResults, $resultsPerPage) {
		print "<br /><table align='center'><tr><td>".i18n::translate('Result Page')." &nbsp;&nbsp;";
		// Prints the '<<' linking to the previous page if it's not on the first page
		if ($this->resultsPageNum > 0) {
			print " <a href='";
			$this->printQueryString($inputFieldNames, 0);
			print "'>&lt;&lt;</a> ";
			print " <a href='";
			$this->printQueryString($inputFieldNames, ($this->resultsPageNum - 1));
			print "'>&lt;</a>";
		}

		// Prints out each number linking to that page number.
		// If it's on that page number it is printed out bold instead of a link
		for ($i = 1; $i < (($totalResults / $resultsPerPage) + 1); $i ++) {
			if ($i != $this->resultsPageNum + 1) {
				print " <a href='";
				$this->printQueryString($inputFieldNames, ($i -1));
				print "'>".$i."</a>";
			} else
			print " <b>".$i."</b>";
		}

		// Prints the '>>' linking to the next page if it's not on the last page
		if ($this->resultsPageNum < (($totalResults / $resultsPerPage) - 1)) {
			print " <a href='";
			$this->printQueryString($inputFieldNames, ($this->resultsPageNum + 1));
			print "'>&gt;</a>";
			print " <a href='";
			$this->printQueryString($inputFieldNames, (int)($totalResults / $resultsPerPage));
			print "'>&gt;&gt;</a>";
		}

		print "</td></tr></table>";
	}

	/**
	 * Prints the query string that goes ... <a href'  HERE   '> for each paging result link
	 *
	 * @param $inputFieldNames - an array of strings representing the names of the variables to include
	 * in the query string usually from input values in a form i.e. 'action', 'query', 'showasso' etc.
	 * @param $pageNum - the page number to link to in the paged results
		*/
	function printQueryString($inputFieldNames, $pageNum) {
		global $GEDCOM;
		$tempURL = "search.php?ged={$GEDCOM}";
		foreach ($inputFieldNames as $key => $value) {
			$controllerVar = $this->getValue($value);
			if (!empty ($controllerVar)) {
				$tempURL .= "&{$value}={$controllerVar}";
			}
		}
		$tempURL .= "&resultsPageNum={$pageNum}";
		foreach($this->sgeds as $i=>$key) {
			$str = str_replace(array (".", "-", " "), array ("_", "_", "_"), $key);
			$tempURL .= "&{$str}=yes";
		}
		print encode_url($tempURL);
	}

	function getValue($varName) {
		if (isset ($this-> $varName)) {
			$value = $this-> $varName;
			return $value;
		} else
		return "";
	}
}
// -- end of class

//-- load a user extended class if one exists
if (file_exists(WT_ROOT.'includes/controllers/search_ctrl_user.php')) {
	require_once WT_ROOT.'includes/controllers/search_ctrl_user.php';
} else {
	class SearchController extends SearchControllerRoot {
	}
}

?>
