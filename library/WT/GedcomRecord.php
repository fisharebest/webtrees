<?php
// Base class for all gedcom records
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

class WT_GedcomRecord {
	const RECORD_TYPE = 'UNKNOWN';
	const URL_PREFIX  = 'gedrecord.php?pid=';

	protected $xref       =null;  // The record identifier
	public    $ged_id     =null;  // The gedcom file, only set if this record comes from the database
	protected $_gedrec    =null;  // Raw gedcom text (unprivatised)
	private   $gedrec     =null;  // Raw gedcom text (privatised)
	protected $facts      =null;
	protected $changeEvent=null;
	private   $disp_public=null;  // Can we display details of this record to WT_PRIV_PUBLIC
	private   $disp_user  =null;  // Can we display details of this record to WT_PRIV_USER
	private   $disp_none  =null;  // Can we display details of this record to WT_PRIV_NONE
	private   $changed    =false; // Is this a new record, pending approval

	// Cached results from various functions.
	protected $_getAllNames     =null;
	protected $_getPrimaryName  =null;
	protected $_getSecondaryName=null;

	// Create a GedcomRecord object from either raw GEDCOM data or a database row
	public function __construct($data) {
		if (is_array($data)) {
			// Construct from a row from the database
			$this->xref   =$data['xref'];
			$this->ged_id =$data['ged_id'];
			$this->_gedrec=$data['gedrec'];
		} else {
			// Construct from raw GEDCOM data
			$this->_gedrec=$data;
			if (preg_match('/^0 (?:@('.WT_REGEX_XREF.')@ )?('.WT_REGEX_TAG.')/', $data, $match)) {
				$this->xref=$match[1];
				$this->ged_id=WT_GED_ID;
			}
		}
	}

	// Get an instance of a GedcomRecord.  We either specify
	// an XREF (in the current gedcom), or we can provide a row
	// from the database (if we anticipate the record hasn't
	// been fetched previously).
	static public function getInstance($data) {
		global $gedcom_record_cache, $GEDCOM;
		static $pending_record_cache;

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
			$data=static::fetchGedcomRecord($pid, $ged_id);

			// If we can edit, then we also need to be able to see pending records.
			// Otherwise relationship privacy rules will not allow us to see
			// newly added records.
			if (WT_USER_CAN_EDIT) {
				if (!isset($pending_record_cache[$ged_id])) {
					// Fetch all pending records in one database query
					$pending_record_cache[$ged_id]=array();
					$rows = WT_DB::prepare(
						"SELECT xref, new_gedcom FROM `##change` WHERE status='pending' AND gedcom_id=?"
					)->execute(array($ged_id))->fetchAll();
					foreach ($rows as $row) {
						$pending_record_cache[$ged_id][$row->xref] = $row->new_gedcom;
					}
				}

				if (isset($pending_record_cache[$ged_id][$pid])) {
					// A pending edit exists for this record
					$tmp = $pending_record_cache[$ged_id][$pid];
					// $tmp can be an empty string, indicating the record has
					// a pending deletion.  Ignore this, as we handle pending
					// deletions separately.
					if ($tmp) {
						$is_pending=true;
						$data=$tmp;
					}
				}
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

	private static function fetchGedcomRecord($xref, $ged_id) {
		static $statement=null;

		// We don't know what type of object this is.  Try each one in turn.
		$row=WT_Person::fetchGedcomRecord($xref, $ged_id);
		if ($row) {
			return $row;
		}
		$row=WT_Family::fetchGedcomRecord($xref, $ged_id);
		if ($row) {
			return $row;
		}
		$row=WT_Source::fetchGedcomRecord($xref, $ged_id);
		if ($row) {
			return $row;
		}
		$row=WT_Repository::fetchGedcomRecord($xref, $ged_id);
		if ($row) {
			return $row;
		}
		$row=WT_Media::fetchGedcomRecord($xref, $ged_id);
		if ($row) {
			return $row;
		}
		$row=WT_Note::fetchGedcomRecord($xref, $ged_id);
		if ($row) {
			return $row;
		}
		// Some other type of record...
		if (is_null($statement)) {
			$statement=WT_DB::prepare(
				"SELECT o_type AS type, o_id AS xref, o_file AS ged_id, o_gedcom AS gedrec ".
				"FROM `##other` WHERE o_id=? AND o_file=? AND o_type NOT IN ('REPO', 'NOTE')"
			);
		}
		return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);

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
	* get gedcom record
	*/
	public function getGedcomRecord() {
		if ($this->gedrec===null) {
			list($this->gedrec)=$this->privatizeGedcom(WT_USER_ACCESS_LEVEL);
		}
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
	public function equals($obj) {
		return !is_null($obj) && $this->xref==$obj->getXref();
	}

	// Generate a URL to this record, suitable for use in HTML
	public function getHtmlUrl() {
		return self::_getLinkUrl(static::URL_PREFIX, '&amp;');
	}
	// Generate a URL to this record, suitable for use in javascript, HTTP headers, etc.
	public function getRawUrl() {
		return self::_getLinkUrl(static::URL_PREFIX, '&');
	}

	private function _getLinkUrl($link, $separator) {
		if ($this->ged_id) {
			// If the record was created from the database, we know the gedcom
			return $link.$this->getXref().$separator.'ged='.rawurlencode(get_gedcom_from_id($this->ged_id));
		} else {
			// If the record was created from a text string, assume the current gedcom
			return $link.$this->getXref().$separator.'ged='.WT_GEDURL;
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

	// Work out whether this record can be shown to a user with a given access level
	private function _canDisplayDetails($access_level) {
		global $person_privacy, $HIDE_LIVE_PEOPLE, $PRIVACY_CHECKS;

		//-- keep a count of how many times we have checked for privacy
		++$PRIVACY_CHECKS;
		// This setting would better be called "$ENABLE_PRIVACY"
		if (!$HIDE_LIVE_PEOPLE) {
			return true;
		}

		// We should always be able to see our own record (unless an admin is applying download restrictions)
		if ($this->getXref()==WT_USER_GEDCOM_ID && $this->getGedId()==WT_GED_ID && $access_level==WT_USER_ACCESS_LEVEL) {
			return true;
		}

		// Does this record have a RESN?
		if (strpos($this->_gedrec, "\n1 RESN confidential")) {
			return WT_PRIV_NONE>=$access_level;
		}
		if (strpos($this->_gedrec, "\n1 RESN privacy")) {
			return WT_PRIV_USER>=$access_level;
		}
		if (strpos($this->_gedrec, "\n1 RESN none")) {
			return true;
		}

		// Does this record have a default RESN?
		if (isset($person_privacy[$this->getXref()])) {
			return $person_privacy[$this->getXref()]>=$access_level;
		}

		// Privacy rules do not apply to admins
		if (WT_PRIV_NONE>=$access_level) {
			return true;
		}

		// Different types of record have different privacy rules
		return $this->_canDisplayDetailsByType($access_level);
	}

	// Each object type may have its own special rules, and re-implement this function.
	protected function _canDisplayDetailsByType($access_level) {
		global $global_facts;

		if (isset($global_facts[static::RECORD_TYPE])) {
			// Restriction found
			return $global_facts[static::RECORD_TYPE]>=$access_level;
		} else {
			// No restriction found - must be public:
			return true;
		}
	}

	// Can the details of this record be shown?
	public function canDisplayDetails($access_level=WT_USER_ACCESS_LEVEL) {
		// CACHING: this function can take three different parameters, 
		// and therefore needs three different caches for the result.
		switch ($access_level) {
		case WT_PRIV_PUBLIC: // visitor
			if ($this->disp_public===null) {
				$this->disp_public=$this->_canDisplayDetails(WT_PRIV_PUBLIC);
			}
			return $this->disp_public;
		case WT_PRIV_USER: // member
			if ($this->disp_user===null) {
				$this->disp_user=$this->_canDisplayDetails(WT_PRIV_USER);
			}
			return $this->disp_user;
		case WT_PRIV_NONE: // admin
			if ($this->disp_none===null) {
				$this->disp_none=$this->_canDisplayDetails(WT_PRIV_NONE);
			}
			return $this->disp_none;
		case WT_PRIV_HIDE: // hidden from admins
			// We use this value to bypass privacy checks.  For example,
			// when downloading data or when calculating privacy itself.
			return true;
		default:
			// Should never get here.
			return false;
		}
	}

	// Can the name of this record be shown?
	public function canDisplayName($access_level=WT_USER_ACCESS_LEVEL) {
		return $this->canDisplayDetails($access_level);
	}

	// Can we edit this record?
	// We use the unprivatized _gedrec as we must take account
	// of the RESN tag, even if we are not permitted to see it.
	public function canEdit() {
		return WT_USER_GEDCOM_ADMIN || WT_USER_CAN_EDIT && strpos($this->_gedrec, "\n1 RESN locked")===false;
	}

	// Remove private data from the raw gedcom record.
	// Return both the visible and invisible data.  We need the invisible data when editing.
	public function privatizeGedcom($access_level) {
		global $global_facts, $person_facts;

		if ($access_level==WT_PRIV_HIDE) {
			// We may need the original record, for example when downloading a GEDCOM or clippings cart
			return array($this->_gedrec, '');
		} elseif ($this->canDisplayDetails($access_level)) {
			// The record is not private, but the individual facts may be.

			// Include the entire first line (for NOTE records)
			list($gedrec)=explode("\n", $this->_gedrec, 2);

			$private_gedrec='';
			// Check each of the sub facts for access
			preg_match_all('/\n1 .*(?:\n[2-9].*)*/', $this->_gedrec, $matches);
			foreach ($matches[0] as $match) {
				if (canDisplayFact($this->xref, $this->ged_id, $match, $access_level)) {
					$gedrec.=$match;
				} else {
					$private_gedrec.=$match;
				}
			}
			return array($gedrec, $private_gedrec);
		} else {
			// We cannot display the details, but we may be able to display
			// limited data, such as links to other records.
			return array($this->createPrivateGedcomRecord($access_level), '');
		}
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . '@ ' . static::RECORD_TYPE . "\n1 NOTE " . WT_I18N::translate('Private');
	}

	// Convert a name record into sortable and full/display versions.  This default
	// should be OK for simple record types.  INDI/FAM records will need to redefine it.
	protected function _addName($type, $value, $gedrec) {
		$this->_getAllNames[]=array(
			'type'=>$type,
			'sort'=>preg_replace('/([0-9]+)/e', 'substr("000000000\\1", -10)', $value),
			'full'=>'<span dir="auto">'.htmlspecialchars($value).'</span>',    // This is used for display
			'fullNN'=>$value, // This goes into the database
		);
	}

	// Get all the names of a record, including ROMN, FONE and _HEB alternatives.
	// Records without a name (e.g. FAM) will need to redefine this function.
	//
	// Parameters: the level 1 fact containing the name.
	// Return value: an array of name structures, each containing
	// ['type'] = the gedcom fact, e.g. NAME, TITL, FONE, _HEB, etc.
	// ['full'] = the name as specified in the record, e.g. 'Vincent van Gogh' or 'John Unknown'
	// ['sort'] = a sortable version of the name (not for display), e.g. 'Gogh, Vincent' or '@N.N., John'
	protected function _getAllNames($fact='!', $level=1) {
		global $WORD_WRAPPED_NOTES;

		if (is_null($this->_getAllNames)) {
			$this->_getAllNames=array();
			if ($this->canDisplayName()) {
				$sublevel=$level+1;
				$subsublevel=$sublevel+1;
				if (preg_match_all("/^{$level} ({$fact}) (.+)((\n[{$sublevel}-9].+)*)/m", $this->getGedcomRecord(), $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						// Treat 1 NAME / 2 TYPE married the same as _MARNM
						if ($match[1]=='NAME' && strpos($match[3], "\n2 TYPE married")!==false) {
							$this->_addName('_MARNM', $match[2] ? $match[2] : $this->getFallBackName(), $match[0]);
						} else {
							$this->_addName($match[1], $match[2] ? $match[2] : $this->getFallBackName(), $match[0]);
						}
						if ($match[3] && preg_match_all("/^{$sublevel} (ROMN|FONE|_\w+) (.+)((\n[{$subsublevel}-9].+)*)/m", $match[3], $submatches, PREG_SET_ORDER)) {
							foreach ($submatches as $submatch) {
								$this->_addName($submatch[1], $submatch[2] ? $submatch[2] : $this->getFallBackName(), $submatch[0]);
							}
						}
					}
				} else {
					$this->_addName(static::RECORD_TYPE, $this->getFallBackName(), null);
				}
			} else {
				$this->_addName(static::RECORD_TYPE, WT_I18N::translate('Private'), null);
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
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='Grek') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'ru':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='Cyrl') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'he':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='Hebr') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'ar':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='Arab') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				default:
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && utf8_script($name['sort'])=='Latn') {
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
				return utf8_strcasecmp($x->getSortName(), $y->getSortName());
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

	// Static helper function to sort an array of objects by Change Date
	static function CompareChanDate($x, $y) {
		$chan_x = $x->getChangeEvent();
		$chan_y = $y->getChangeEvent();
		$tmp=WT_Date::Compare($chan_x->getDate(), $chan_y->getDate());
		if ($tmp) {
			return $tmp;
		} else {
			if (
				preg_match('/^\d\d:\d\d:\d\d/', get_gedcom_value('DATE:TIME', 2, $chan_x->getGedcomRecord()).':00', $match_x) &&
				preg_match('/^\d\d:\d\d:\d\d/', get_gedcom_value('DATE:TIME', 2, $chan_y->getGedcomRecord()).':00', $match_y)
			) {
				return strcmp($match_x[0], $match_y[0]);
			} else {
				return 0;
			}
		}
	}

	// Get variants of the name
	public function getFullName() {
		if ($this->canDisplayName()) {
			$tmp=$this->getAllNames();
			return $tmp[$this->getPrimaryName()]['full'];
		} else {
			return WT_I18N::translate('Private');
		}
	}
	public function getSortName() {
		// The sortable name is never displayed, no need to call canDisplayName()
		$tmp=$this->getAllNames();
		return $tmp[$this->getPrimaryName()]['sort'];
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
			$name=$this->getFullName();
		}
		$html='<a href="'.$this->getHtmlUrl().'"';
		if ($find) {
			$html.=' onclick="pasteid(\''.$this->getXref().'\', \'' . htmlentities($name) . '\');"';
		}
		$html.=' class="list_item"><b>'.$name.'</b>';
		$html.=$this->format_list_details();
		$html='<'.$tag.'>'.$html.'</a></'.$tag.'>';
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
			if (($event->getDate()->isOK() || $event->getPlace()) && $event->canShow()) {
				switch ($style) {
				case 1:
					return '<br><em>'.$event->getLabel().' '.format_fact_date($event, $this, false, false).' '.format_fact_place($event).'</em>';
				case 2:
					return '<dl><dt class="label">'.$event->getLabel().'</dt><dd class="field">'.format_fact_date($event, $this, false, false).' '.format_fact_place($event).'</dd></dl>';
				}
			}
		}
		return '';
	}

	// Fetch the records that link to this one
	public function fetchLinkedIndividuals() {
		return fetch_linked_indi($this->getXref(), static::RECORD_TYPE, $this->ged_id);
	}
	public function fetchLinkedFamilies() {
		return fetch_linked_fam($this->getXref(), static::RECORD_TYPE, $this->ged_id);
	}
	public function fetchLinkedNotes() {
		return fetch_linked_note($this->getXref(), static::RECORD_TYPE, $this->ged_id);
	}
	public function fetchLinkedSources() {
		return fetch_linked_sour($this->getXref(), static::RECORD_TYPE, $this->ged_id);
	}
	public function fetchLinkedRepositories() {
		return fetch_linked_repo($this->getXref(), static::RECORD_TYPE, $this->ged_id);
	}
	public function fetchLinkedMedia() {
		return fetch_linked_obje($this->getXref(), static::RECORD_TYPE, $this->ged_id);
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

	// Get the first WT_Event for the given fact type
	public function getFactByType($tag) {
		foreach ($this->getFacts() as $fact) {
			if ($fact->getTag()==$tag) {
				return $fact;
			}
		}
		return null;
	}

	// Return an array of events that match the given types
	public function getAllFactsByType($tags) {
		if (is_string($tags)) {
			$tags = array($tags);
		}
		$facts = array();
		foreach ($tags as $tag) {
			foreach ($this->getFacts() as $fact) {
				if ($fact->getTag()==$tag) {
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
	public function getFacts() {
		$this->parseFacts();
		return $this->facts;
	}

	/**
	* Get the CHAN event for this record
	*
	* @return WT_Event
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
	public function parseFacts() {
		//-- only run this function once
		if (!is_null($this->facts) && is_array($this->facts)) {
			return;
		}
		$this->facts=array();
		//-- don't run this function if privacy does not allow viewing of details
		if (!$this->canDisplayDetails()) {
			return;
		}
		//-- find all the fact information
		$indilines = explode("\n", $this->getGedcomRecord());   // -- find the number of lines in the individuals record
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
					$this->facts[] = new WT_Event($factrec, $this, $linenum);
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
	public function diffMerge($diff) {
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
				$this->facts[$key]->setIsOld();
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
				$newevent->setIsNew();
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
	// Get the last-change timestamp for this record, either as a formatted string
	// (for display) or as a unix timestamp (for sorting)
	//////////////////////////////////////////////////////////////////////////////
	public function LastChangeTimestamp($sorting=false) {
		$chan = $this->getChangeEvent();

		if ($chan) {
			// The record does have a CHAN event
			$d = $chan->getDate()->MinDate();
			if (preg_match('/^(\d\d):(\d\d):(\d\d)/', get_gedcom_value('DATE:TIME', 2, $chan->getGedcomRecord()).':00', $match)) {
				$t=mktime((int)$match[1], (int)$match[2], (int)$match[3], (int)$d->Format('%n'), (int)$d->Format('%j'), (int)$d->Format('%Y'));
			} else {
				$t=mktime(0, 0, 0, (int)$d->Format('%n'), (int)$d->Format('%j'), (int)$d->Format('%Y'));
			}
			if ($sorting) {
				return $t;
			} else {
				return strip_tags(format_timestamp($t));
			}
		} else {
			// The record does not have a CHAN event
			if ($sorting) {
				return 0;
			} else {
				return '&nbsp;';
			}
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// Get the last-change user for this record
	//////////////////////////////////////////////////////////////////////////////
	public function LastChangeUser() {
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
