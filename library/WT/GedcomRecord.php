<?php
// Base class for all gedcom records
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_event.php';

class WT_GedcomRecord {
	protected $xref       =null;  // The record identifier
	protected $type       =null;  // INDI, FAM, etc.
	public    $ged_id     =null;  // The gedcom file, only set if this record comes from the database
	protected $gedrec     =null;  // Raw gedcom text (privatised)
	protected $facts      =null;
	protected $changeEvent=null;
	public    $disp       =true;  // Can we display details of this object
	private   $can_edit   =null;
	public    $dispname   =true;  // Can we display the name of this object
	private   $changed    =false; // Is this a new record, pending approval

	// Cached results from various functions.
	protected $_getAllNames     =null;
	protected $_getPrimaryName  =null;
	protected $_getSecondaryName=null;

	// Create a GedcomRecord object from either raw GEDCOM data or a database row
	public function __construct($data) {
		if (is_array($data)) {
			// Construct from a row from the database
			$this->xref  =$data['xref'];
			$this->type  =$data['type'];
			$this->ged_id=$data['ged_id'];
			$this->gedrec=$data['gedrec'];
		} else {
			// Construct from raw GEDCOM data
			$this->gedrec=$data;
			if (preg_match('/^0 (?:@('.WT_REGEX_XREF.')@ )?('.WT_REGEX_TAG.')/', $data, $match)) {
				$this->xref=$match[1];
				$this->type=$match[2];
				$this->ged_id=WT_GED_ID;
			}
		}

		//-- set the gedcom record a privatized version
		$this->disp  =canDisplayRecord($this->ged_id, $this->gedrec);
		$this->gedrec=privatize_gedcom($this->gedrec);
	}

	// Get an instance of a GedcomRecord.  We either specify
	// an XREF (in the current gedcom), or we can provide a row
	// from the database (if we anticipate the record hasn't
	// been fetched previously).
	static public function &getInstance($data) {
		global $gedcom_record_cache, $GEDCOM;

		$is_pending=false; // Did this record come from a pending edit

		if (is_array($data)) {
			$ged_id=$data['ged_id'];
			$pid   =$data['xref'];
		} else {
			$ged_id=get_id_from_gedcom($GEDCOM);
			$pid   =$data;
		}

		// Check the cache first
		if (isset($gedcom_record_cache[$pid][$ged_id])) {
			return $gedcom_record_cache[$pid][$ged_id];
		}

		// Look for the record in the database
		if (!is_array($data)) {
			if (version_compare(PHP_VERSION, '5.3', '>=')) {
				// If we know what sort of object we are, we can query the table directly.
				switch (get_called_class()) {
				case 'Person':
					$data=fetch_person_record($pid, $ged_id);
					break;
				case 'Family':
					$data=fetch_family_record($pid, $ged_id);
					break;
				case 'Source':
					$data=fetch_source_record($pid, $ged_id);
					break;
				case 'Media':
					$data=fetch_media_record($pid, $ged_id);
					break;
				case 'Repository':
				case 'Note':
					$data=fetch_other_record($pid, $ged_id);
					break;
				default:
					// Type unknown - try each of the five tables in turn....
					$data=fetch_gedcom_record($pid, $ged_id);
					break;
				}
			} else {
				// Late-static-binding is unavailable in PHP 5.2, so we do not what what
				// sort of object we are - try each of the five tables in turn....
				$data=fetch_gedcom_record($pid, $ged_id);
			}

			// If we didn't find the record in the database, it may be new/pending
			if (!$data && WT_USER_CAN_EDIT && ($data=find_gedcom_record($pid, $ged_id, true))!='') {
				$is_pending=true;
			}

			// If we still didn't find it, it doesn't exist
			if (!$data) {
				return null;
			}
		}

		// Create the object
		if (is_array($data)) {
			$type=$data['type'];
		} elseif (preg_match('/^0 @'.WT_REGEX_XREF.'@ ('.WT_REGEX_TAG.')/', $data, $match)) {
			$type=$match[1];
		} else {
			$type='';
		}
		switch($type) {
		case 'INDI':
			$object=new WT_Person($data);
			break;
		case 'FAM':
			$object=new WT_Family($data);
			break;
		case 'SOUR':
			$object=new WT_Source($data);
			break;
		case 'OBJE':
			$object=new WT_Media($data);
			break;
		case 'REPO':
			$object=new WT_Repository($data);
			break;
		case 'NOTE':
			$object=new WT_Note($data);
			break;
		default:
			$object=new WT_GedcomRecord($data);
			break;
		}

		// This is an object from the database, so indicate which gedcom it comes from.
		$object->ged_id=$ged_id;

		if ($is_pending) {
			$object->setChanged(true);
		}

		// Store it in the cache
		$gedcom_record_cache[$object->xref][$object->ged_id]=&$object;
		return $object;
	}

	/**
	* get the xref
	* @return string returns the person ID
	*/
	public function getXref() {
		return $this->xref;
	}
	/**
	* get the gedcom file
	* @return string returns the person ID
	*/
	public function getGedId() {
		return $this->ged_id;
	}
	/**
	* get the object type
	* @return string returns the type of this object 'INDI','FAM', etc.
	*/
	public function getType() {
		return $this->type;
	}
	/**
	* get gedcom record
	*/
	public function getGedcomRecord() {
		return $this->gedrec;
	}
	/**
	* set gedcom record
	*/
	public function setGedcomRecord($gcRec) {
		$this->gedrec = $gcRec;
	}
	/**
	* set if this is a changed record from the gedcom file
	* @param boolean $changed
	*/
	public function setChanged($changed) {
		$this->changed = $changed;
	}
	/**
	* get if this is a changed record from the gedcom file
	* @return boolean
	*/
	public function getChanged() {
		return $this->changed;
	}

	/**
	* check if this object is equal to the given object
	* @param GedcomRecord $obj
	*/
	public function equals(&$obj) {
		return !is_null($obj) && $this->xref==$obj->getXref();
	}

	// Generate a URL to this record, suitable for use in HTML
	public function getHtmlUrl() {
		return self::_getLinkUrl('gedcomrecord.php?famid=', '&amp;');
	}
	// Generate a URL to this record, suitable for use in javascript, HTTP headers, etc.
	public function getRawUrl() {
		return self::_getLinkUrl('gedcomrecord.php?famid=', '&');
	}

	protected function _getLinkUrl($link, $separator) {
		if ($this->ged_id) {
			// If the record was created from the database, we know the gedcom
			return $link.$this->getXref().$separator.'ged='.rawurlencode(get_gedcom_from_id($this->ged_id));
		} else {
			// If the record was created from a text string, assume the current gedcom
			return $link.$this->getXref().$separator.'ged='.WT_GEDURL;
		}
	}

	// Get an HTML link to this object, for use in sortable lists.
	public function getXrefLink($target='') {
		global $SEARCH_SPIDER;
		if (empty($SEARCH_SPIDER)) {
			if ($target) {
				$target='target="'.$target.'"';
			}
			return '<a href="'.$this->getHtmlUrl().'#content" name="'.preg_replace('/\D/','',$this->getXref()).'" '.$target.'>'.$this->getXref().'</a>';
		} else {
			return $this->getXref();
		}
	}

	/**
	* return an absolute url for linking to this record from another site
	*
	*/
	public function getAbsoluteLinkUrl() {
		return WT_SERVER_NAME.WT_SCRIPT_PATH.$this->getHtmlUrl();
	}

	/**
	* check if this record has been marked for deletion
	* @return boolean
	*/
	public function isMarkedDeleted() {
		$tmp=WT_DB::prepare(
			"SELECT new_gedcom".
			" FROM `##change`".
			" WHERE status='pending' AND gedcom_id=? AND xref=?".
			" ORDER BY change_id desc".
			" LIMIT 1"
		)->execute(array($this->ged_id, $this->xref))->fetchOne();

		return $tmp==='';
	}

	/**
	* Can the details of this record be shown?
	* @return boolean
	*/
	public function canDisplayDetails() {
		return $this->disp;
	}

	/**
	* Can the name of this record be shown?
	* @return boolean
	*/
	public function canDisplayName() {
		return $this->dispname;
	}

	// Can we edit this record?
	public function canEdit() {
		if ($this->can_edit===null) {
			$this->can_edit=
				get_gedcom_setting($this->ged_id, 'ALLOW_EDIT_GEDCOM') && (
					WT_USER_GEDCOM_ADMIN ||
					WT_USER_CAN_EDIT && strpos($this->gedrec, "\n1 RESN locked")===false
				);
		}
		return  $this->can_edit;
	}

	// Convert a name record into sortable and listable versions.  This default
	// should be OK for simple record types.  INDI records will need to redefine it.
	protected function _addName($type, $value, $gedrec) {
		$this->_getAllNames[]=array(
			'type'=>$type,
			'full'=>$value,
			'list'=>$value,
			'sort'=>preg_replace('/([0-9]+)/e', 'substr("000000000\\1", -10)', $value)
		);
	}

	// Get all the names of a record, including ROMN, FONE and _HEB alternatives.
	// Records without a name (e.g. FAM) will need to redefine this function.
	//
	// Parameters: the level 1 fact containing the name.
	// Return value: an array of name structures, each containing
	// ['type'] = the gedcom fact, e.g. NAME, TITL, FONE, _HEB, etc.
	// ['full'] = the name as specified in the record, e.g. 'Vincent van Gogh' or 'John Unknown'
	// ['list'] = a version of the name as might appear in lists, e.g. 'van Gogh, Vincent' or 'Unknown, John'
	// ['sort'] = a sortable version of the name (not for display), e.g. 'Gogh, Vincent' or '@N.N., John'
	protected function _getAllNames($fact='!', $level=1) {
		global $WORD_WRAPPED_NOTES;

		if (is_null($this->_getAllNames)) {
			$this->_getAllNames=array();
			if ($this->canDisplayName()) {
				$sublevel=$level+1;
				$subsublevel=$sublevel+1;
				if (preg_match_all("/^{$level} ({$fact}) (.+)((\n[{$sublevel}-9].+)*)/m", $this->gedrec, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$this->_addName($match[1], $match[2] ? $match[2] : $this->getFallBackName(), $match[0]);
						if ($match[3] && preg_match_all("/^{$sublevel} (ROMN|FONE|_\w+) (.+)((\n[{$subsublevel}-9].+)*)/m", $match[3], $submatches, PREG_SET_ORDER)) {
							foreach ($submatches as $submatch) {
								$this->_addName($submatch[1], $submatch[2] ? $submatch[2] : $this->getFallBackName(), $submatch[0]);
							}
						}
					}
				} else {
					$this->_addName($this->getType(), $this->getFallBackName(), null);
				}
			} else {
				$this->_addName($this->getType(), i18n::translate('Private'), null);
			}
		}
		return $this->_getAllNames;
	}

	// Derived classes should redefine this function, otherwise the object will have no name
	public function getAllNames() {
		return $this->_getAllNames('!', 1);
	}

	// If this object has no name, what do we call it?
	public function getFallBackName() {
		return $this->getXref();
	}

	// Which of the (possibly several) names of this record is the primary one.
	public function getPrimaryName() {
		if (is_null($this->_getPrimaryName)) {
			// Generally, the first name is the primary one....
			$this->_getPrimaryName=0;
			// ....except when the language/name use different character sets
			if (count($this->getAllNames())>1) {
				switch (WT_LOCALE) {
				case 'el':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='greek') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'ru':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='russian') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'he':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='hebrew') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'ar':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='arabic') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				default:
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='latin') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				}
			}
		}
		return $this->_getPrimaryName;
	}

	// Which of the (possibly several) names of this record is the secondary one.
	public function getSecondaryName() {
		if (is_null($this->_getSecondaryName)) {
			// Generally, the primary and secondary names are the same
			$this->_getSecondaryName=$this->getPrimaryName();
			// ....except when there are names with different character sets
			$all_names=$this->getAllNames();
			if (count($all_names)>1) {
				$primary_script=utf8_script($all_names[$this->getPrimaryName()]['sort']);
				foreach ($all_names as $n=>$name) {
					if ($n!=$this->getPrimaryName() && $name['type']!='_MARNM' && utf8_script($name['sort'])!=$primary_script) {
						$this->_getSecondaryName=$n;
						break;
					}
				}
			}
		}
		return $this->_getSecondaryName;
	}

	// Allow the choice of primary name to be overidden, e.g. in a search result
	public function setPrimaryName($n) {
		$this->_getPrimaryName=$n;
		$this->_getSecondaryName=null;
	}

	// Allow native PHP functions such as array_intersect() to work with objects
	public function __toString() {
		return $this->xref.'@'.$this->ged_id;
	}

	// Static helper function to sort an array of objects by name
	// Records whose names cannot be displayed are sorted at the end.
	static function Compare($x, $y) {
		if ($x->canDisplayName()) {
			if ($y->canDisplayName()) {
				return utf8_strcmp($x->getSortName(), $y->getSortName());
			} else {
				return -1; // only $y is private
			}
		} else {
			if ($y->canDisplayName()) {
				return 1; // only $x is private
			} else {
				return 0; // both $x and $y private
			}
		}
	}

	// Static helper function to sort an array of objects by ID
	static function CompareId($x, $y) {
		return strnatcasecmp($x->getXref(), $y->getXref());
	}

	// Static helper function to sort an array of objects by Change Date
	static function CompareChanDate($x, $y) {
		$chan_x = $x->getChangeEvent();
		$chan_y = $y->getChangeEvent();
		$tmp=WT_Date::Compare($chan_x->getDate(), $chan_y->getDate());
		if ($tmp) {
			return $tmp;
		} else {
			if (
				preg_match('/^\d\d:\d\d:\d\d/', get_gedcom_value('DATE:TIME', 2, $chan_x->getGedcomRecord(), '', false).':00', $match_x) &&
				preg_match('/^\d\d:\d\d:\d\d/', get_gedcom_value('DATE:TIME', 2, $chan_y->getGedcomRecord(), '', false).':00', $match_y)
			) {
				return strcmp($match_x[0], $match_y[0]);
			} else {
				return 0;
			}
		}
	}

	// Get the three variants of the name
	public function getFullName() {
		if ($this->canDisplayName()) {
			$tmp=$this->getAllNames();
			return $tmp[$this->getPrimaryName()]['full'];
		} else {
			return i18n::translate('Private');
		}
	}
	public function getSortName() {
		// The sortable name is never displayed, no need to call canDisplayName()
		$tmp=$this->getAllNames();
		return $tmp[$this->getPrimaryName()]['sort'];
	}
	public function getListName() {
		if ($this->canDisplayName()) {
			$tmp=$this->getAllNames();
			return $tmp[$this->getPrimaryName()]['list'];
		} else {
			return i18n::translate('Private');
		}
	}
	// Get the fullname in an alternative character set
	public function getAddName() {
		if ($this->canDisplayName() && $this->getPrimaryName()!=$this->getSecondaryName()) {
			$all_names=$this->getAllNames();
			return $all_names[$this->getSecondaryName()]['full'];
		} else {
			return null;
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// Format this object for display in a list
	// If $find is set, then we are displaying items from a selection list.
	// $name allows us to use something other than the record name.
	//////////////////////////////////////////////////////////////////////////////
	public function format_list($tag='li', $find=false, $name=null) {
		if (is_null($name)) {
			$name=($tag=='li') ? $this->getListName() : $this->getFullName();
		}
		$dir=begRTLText($name) ? 'rtl' : 'ltr';
		$html='<a href="'.$this->getHtmlUrl().'"';
		if ($find) {
			$html.=' onclick="pasteid(\''.$this->getXref().'\');"';
		}
		$html.=' class="list_item"><b>'.$name.'</b>';
		$html.=$this->format_list_details();
		$html='<'.$tag.' class="'.$dir.'" dir="'.$dir.'">'.$html.'</a></'.$tag.'>';
		return $html;
	}

	// This function should be redefined in derived classes to show any major
	// identifying characteristics of this record.
	public function format_list_details() {
		return '';
	}

	// Extract/format the first fact from a list of facts.
	public function format_first_major_fact($facts, $style) {
		foreach ($this->getAllFactsByType(explode('|', $facts)) as $event) {
			// Only display if it has a date or place (or both)
			if (($event->getDate() || $event->getPlace()) && $event->canShow()) {
				switch ($style) {
				case 1:
					return '<br /><i>'.$event->getLabel().' '.format_fact_date($event).format_fact_place($event).'</i>';
				case 2:
					return '<dl><dt class="label">'.$event->getLabel().'</dt><dd class="field">'.format_fact_date($event).format_fact_place($event).'</dd></dl>';
				case 3:
					return $event->getDate()->MinDate()->Format('%Y');
				}
			}
		}
		return '';
	}

	// Count the number of records that link to this one
	public function countLinkedIndividuals() {
		return count_linked_indi($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function countLinkedFamilies() {
		return count_linked_fam($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function countLinkedNotes() {
		return count_linked_note($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function countLinkedSources() {
		return count_linked_sour($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function countLinkedMedia() {
		return count_linked_obje($this->getXref(), $this->getType(), $this->ged_id);
	}

	// Fetch the records that link to this one
	public function fetchLinkedIndividuals() {
		return fetch_linked_indi($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function fetchLinkedFamilies() {
		return fetch_linked_fam($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function fetchLinkedNotes() {
		return fetch_linked_note($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function fetchLinkedSources() {
		return fetch_linked_sour($this->getXref(), $this->getType(), $this->ged_id);
	}
	public function fetchLinkedMedia() {
		return fetch_linked_obje($this->getXref(), $this->getType(), $this->ged_id);
	}

	// Get all attributes (e.g. DATE or PLAC) from an event (e.g. BIRT or MARR).
	// This is used to display multiple events on the individual/family lists.
	// Multiple events can exist because of uncertainty in dates, dates in different
	// calendars, place-names in both latin and hebrew character sets, etc.
	// It also allows us to combine dates/places from different events in the summaries.
	public function getAllEventDates($event) {
		$dates=array();
		foreach ($this->getAllFactsByType($event) as $event) {
			if ($event->getDate()->isOK()) {
				$dates[]=$event->getDate();
			}
		}
		return $dates;
	}
	public function getAllEventPlaces($event) {
		$places=array();
		foreach ($this->getAllFactsByType($event) as $event) {
			if (preg_match_all('/\n(?:2 PLAC|3 (?:ROMN|FONE|_HEB)) +(.+)/', $event->getGedcomRecord(), $ged_places)) {
				foreach ($ged_places[1] as $ged_place) {
					$places[]=$ged_place;
				}
			}
		}
		return $places;
	}

	/**
	* Get the first Event for the given Fact type
	*
	* @param string $fact
	* @return Event
	*/
	public function getFactByType($factType) {
		$this->parseFacts();
		if (empty($this->facts)) {
			return null;
		}
		foreach ($this->facts as $f=>$fact) {
			if ($fact->getTag()==$factType || $fact->getType()==$factType) {
				return $fact;
			}
		}
		return null;
	}

	/**
	* Return an array of events that match the given types
	*
	* @param mixed $factTypes  may be a single string or an array of strings
	* @return Event
	*/
	public function getAllFactsByType($factTypes) {
		$this->parseFacts();
		if (is_string($factTypes)) {
			$factTypes = array($factTypes);
		}
		$facts = array();
		foreach ($factTypes as $factType) {
			foreach ($this->facts as $fact) {
				if ($fact->getTag()==$factType) {
					$facts[]=$fact;
				}
			}
		}
		return $facts;
	}

	/**
	* returns an array of all of the facts
	* @return Array
	*/
	public function getFacts($nfacts=NULL) {
		$this->parseFacts($nfacts);
		return $this->facts;
	}

	/**
	* Get the CHAN event for this record
	*
	* @return Event
	*/
	public function getChangeEvent() {
		if (is_null($this->changeEvent)) {
			$this->changeEvent = $this->getFactByType('CHAN');
		}
		return $this->changeEvent;
	}

	/**
	* Parse the facts from the record
	*/
	public function parseFacts($nfacts=NULL) {
		//-- only run this function once
		if (!is_null($this->facts) && is_array($this->facts)) {
			return;
		}
		$this->facts=array();
		//-- don't run this function if privacy does not allow viewing of details
		if (!$this->canDisplayDetails()) {
			return;
		}
		//-- must trim the record here because the record is trimmed in edit and it could mess up line numbers
		$this->gedrec = trim($this->gedrec);
		//-- find all the fact information
		$indilines = explode("\n", $this->gedrec);   // -- find the number of lines in the individuals record
		$lct = count($indilines);
		$factrec = ''; // -- complete fact record
		$line = '';   // -- temporary line buffer
		$linenum=1;
		for ($i=1; $i<=$lct; $i++) {
			if ($i<$lct) {
				$line = $indilines[$i];
			} else {
				$line=' ';
			}
			if (empty($line)) {
				$line=' ';
			}
			if ($i==$lct||$line{0}==1) {
				if ($i>1) {
					$event = new Event($factrec, $linenum);
					$fact = $event->getTag();
					if ($nfacts==NULL || !in_array($fact, $nfacts)) {
						$event->setParentObject($this);
						$this->facts[] = $event;
					}
				}
				$factrec = $line;
				$linenum = $i;
			}
			else $factrec .= "\n".$line;
		}
	}

	/**
	* Merge the facts from another GedcomRecord object into this object
	* for generating a diff view
	* @param GedcomRecord $diff the record to compare facts with
	*/
	public function diffMerge(&$diff) {
		if (is_null($diff)) {
			return;
		}
		$this->parseFacts();
		$diff->parseFacts();

		//-- update old facts
		foreach ($this->facts as $key=>$event) {
			$found = false;
			foreach ($diff->facts as $indexval => $newevent) {
				$newfact = $newevent->getGedcomRecord();
				$newfact=preg_replace("/\\\/", '/', $newfact);
				if (trim($newfact)==trim($event->getGedcomRecord())) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$this->facts[$key]->gedcomRecord.="\nWT_OLD\n";
			}
		}
		//-- look for new facts
		foreach ($diff->facts as $key=>$newevent) {
			$found = false;
			foreach ($this->facts as $indexval => $event) {
				$newfact = $newevent->getGedcomRecord();
				$newfact=preg_replace("/\\\/", '/', $newfact);
				if (trim($newfact)==trim($event->getGedcomRecord())) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				$newevent->gedcomRecord.="\nWT_NEW\n";
				$this->facts[]=$newevent;
			}
		}
	}

	public function getEventDate($event) {
		$srec = $this->getAllEvents($event);
		if (!$srec) {
			return '';
		}
		$srec = $srec[0];
		return get_gedcom_value('DATE', 2, $srec);
	}
	public function getEventSource($event) {
		$srec = $this->getAllEvents($event);
		if (!$srec) {
			return '';
		}
		$srec = $srec[0];
		return get_sub_record('SOUR', 2, $srec);
	}

	//////////////////////////////////////////////////////////////////////////////
	// Get the last-change timestamp for this record - optionally wrapped in a
	// link to ourself.
	//////////////////////////////////////////////////////////////////////////////
	public function LastChangeTimestamp($add_url) {
		global $DATE_FORMAT, $TIME_FORMAT;

		$chan = $this->getChangeEvent();

		if (is_null($chan)) {
			return '&nbsp;';
		}

		$d = $chan->getDate();
		if (preg_match('/^(\d\d):(\d\d):(\d\d)/', get_gedcom_value('DATE:TIME', 2, $chan->getGedcomRecord(), '', false).':00', $match)) {
			$t=mktime($match[1], $match[2], $match[3]);
			$sort=$d->MinJD().$match[1].$match[2].$match[3];
			$text=strip_tags($d->Display(false, "{$DATE_FORMAT} - ", array()).date(str_replace('%', '', $TIME_FORMAT), $t));
		} else {
			$sort=$d->MinJD().'000000';
			$text=strip_tags($d->Display(false, "{$DATE_FORMAT}", array()));
		}
		if ($add_url) {
			$text='<a name="'.$sort.'" href="'.$this->getHtmlUrl().'">'.$text.'</a>';
		}
		return $text;
	}

	//////////////////////////////////////////////////////////////////////////////
	// Get the last-change user for this record
	//////////////////////////////////////////////////////////////////////////////
	public function LastchangeUser() {
		$chan = $this->getChangeEvent();

		if (is_null($chan)) {
			return '&nbsp;';
		}

		$chan_user = $chan->getValue('_WT_USER');
		if (empty($chan_user)) {
			return '&nbsp;';
		}
		return $chan_user;
	}
}
