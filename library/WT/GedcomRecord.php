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
	const SQL_FETCH   = "SELECT o_gedcom FROM `##other` WHERE o_id=? AND o_file=?";
	const URL_PREFIX  = 'gedrecord.php?pid=';

	protected $xref        = null;  // The record identifier
	protected $gedcom_id   = null;  // The gedcom file
	protected $gedcom      = null;  // GEDCOM data (before any pending edits)
	protected $pending     = null;  // GEDCOM data (after any pending edits)

	protected $facts       = null;  // Array of WT_Fact objects (from $gedcom/$pending)

	private   $disp_public = null;  // Can we display details of this record to WT_PRIV_PUBLIC
	private   $disp_user   = null;  // Can we display details of this record to WT_PRIV_USER
	private   $disp_none   = null;  // Can we display details of this record to WT_PRIV_NONE

	// Cached results from various functions.
	protected $_getAllNames      = null;
	protected $_getPrimaryName   = null;
	protected $_getSecondaryName = null;

	// Allow getInstance() to return references to existing objects
	private static $gedcom_record_cache;
	// Fetch all pending edits in one database query
	private static $pending_record_cache;

	// Create a GedcomRecord object from raw GEDCOM data.
	// $gedcom is an empty string for new/pending records
	// $pending is null for a record with no pending edits
	// $pending is an empty string for records with pending deletions
	public function __construct($xref, $gedcom, $pending, $gedcom_id) {
		$this->xref      = $xref;
		$this->gedcom    = $gedcom;
		$this->pending   = $pending;
		$this->gedcom_id = $gedcom_id;

		// Split the record into facts
		if ($gedcom) {
			$gedcom_facts = preg_split('/\n(?=1)/s', $gedcom);
			array_shift($gedcom_facts);
		} else {
			$gedcom_facts = array();
		}
		if ($pending) {
			$pending_facts = preg_split('/\n(?=1)/s', $pending);
			array_shift($pending_facts);
		} else {
			$pending_facts = array();
		}

		$this->facts = array();

		foreach ($gedcom_facts as $gedcom_fact) {
			$fact = new WT_Fact($gedcom_fact, $this, md5($gedcom_fact));
			if ($pending !== null && !in_array($gedcom_fact, $pending_facts)) {
				$fact->setIsOld();
			}
			$this->facts[] = $fact;
		}
		foreach ($pending_facts as $pending_fact) {
			if (!in_array($pending_fact, $gedcom_facts)) {
				$fact = new WT_Fact($pending_fact, $this, md5($pending_fact));
				$fact->setIsNew();
				$this->facts[] = $fact;
			}
		}
	}

	// Get an instance of a GedcomRecord object.  For single records,
	// we just receive the XREF.  For bulk records (such as lists
	// and search results) we can receive the GEDCOM data as well.
	static public function getInstance($xref, $gedcom_id=WT_GED_ID, $gedcom=null) {
		// Is this record already in the cache?
		if (isset(self::$gedcom_record_cache[$xref][$gedcom_id])) {
			return self::$gedcom_record_cache[$xref][$gedcom_id];
		}

		// Do we need to fetch the record from the database?
		if ($gedcom === null) {
			$gedcom = static::fetchGedcomRecord($xref, $gedcom_id);
		}

		// If we can edit, then we also need to be able to see pending records.
		if (WT_USER_CAN_EDIT) {
			if (!isset(self::$pending_record_cache[$gedcom_id])) {
				// Fetch all pending records in one database query
				self::$pending_record_cache[$gedcom_id]=array();
				$rows = WT_DB::prepare(
					"SELECT xref, new_gedcom FROM `##change` WHERE status='pending' AND gedcom_id=?"
				)->execute(array($gedcom_id))->fetchAll();
				foreach ($rows as $row) {
					self::$pending_record_cache[$gedcom_id][$row->xref] = $row->new_gedcom;
				}
			}

			if (isset(self::$pending_record_cache[$gedcom_id][$xref])) {
				// A pending edit exists for this record
				$pending = self::$pending_record_cache[$gedcom_id][$xref];
			} else {
				$pending = null;
			}
		} else {
			// There are no pending changes for this record
			$pending = null;
		}

		// No such record exists - create a dummy one
		if ($gedcom === null && $pending === null) {
			// Is this a good idea?
			//$xref      = self::DUMMY_XREF;
			//$gedcom    = "0 @$xref@ " . static::RECORD_TYPE . "\n1 RESN none";
			//$gedcom_id = 0;
			return null;
		}

		// Create the object
		if (preg_match('/^0 @' . WT_REGEX_XREF . '@ (' . WT_REGEX_TAG . ')/', $gedcom.$pending, $match)) {
			$type = $match[1];
		} elseif (preg_match('/^0 (HEAD|TRLR)/', $gedcom.$pending, $match)) {
			$type = $match[1];
		} else {
			throw new Exception('Unrecognised GEDCOM record: ' . $gedcom);
		}

		switch($type) {
		case 'INDI':
			$record = new WT_Individual($xref, $gedcom, $pending, $gedcom_id);
			break;
		case 'FAM':
			$record = new WT_Family($xref, $gedcom, $pending, $gedcom_id);
			break;
		case 'SOUR':
			$record = new WT_Source($xref, $gedcom, $pending, $gedcom_id);
			break;
		case 'OBJE':
			$record = new WT_Media($xref, $gedcom, $pending, $gedcom_id);
			break;
		case 'REPO':
			$record = new WT_Repository($xref, $gedcom, $pending, $gedcom_id);
			break;
		case 'NOTE':
			$record = new WT_Note($xref, $gedcom, $pending, $gedcom_id);
			break;
		case 'HEAD':
		case 'TRLR':
		case 'SUBM':
		case 'SUBN':
			$record = new WT_GedcomRecord($xref, $gedcom, $pending, $gedcom_id);
			break;
		default:
			throw new Exception('No support for GEDCOM record type: ' . $type);
		}

		// Store it in the cache
		self::$gedcom_record_cache[$xref][$gedcom_id] = $record;

		return $record;
	}

	private static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement=null;

		// We don't know what type of object this is.  Try each one in turn.
		$data = WT_Individual::fetchGedcomRecord($xref, $gedcom_id);
		if ($data) {
			return $data;
		}
		$data = WT_Family::fetchGedcomRecord($xref, $gedcom_id);
		if ($data) {
			return $data;
		}
		$data = WT_Source::fetchGedcomRecord($xref, $gedcom_id);
		if ($data) {
			return $data;
		}
		$data = WT_Repository::fetchGedcomRecord($xref, $gedcom_id);
		if ($data) {
			return $data;
		}
		$data = WT_Media::fetchGedcomRecord($xref, $gedcom_id);
		if ($data) {
			return $data;
		}
		$data = WT_Note::fetchGedcomRecord($xref, $gedcom_id);
		if ($data) {
			return $data;
		}
		// Some other type of record...
		if (is_null($statement)) {
			$statement=WT_DB::prepare("SELECT o_gedcom FROM `##other` WHERE o_id=? AND o_file=?");
		}
		return $statement->execute(array($xref, $gedcom_id))->fetchOne();

	}

	// XREF
	public function getXref() {
		return $this->xref;
	}
	
	// GEDCOM ID
	public function getGedcomId() {
		return $this->gedcom_id;
	}

	// Application code should access data via WT_Fact objects
	public function getGedcom() {
		if ($this->pending === null) {
			return $this->gedcom;
		} else {
			return $this->pending;
		}
	}

	// Does this record have a pending change?
	public function isNew() {
		return $this->pending !== null;
	}

	// Does this record have a pending deletion?
	public function isOld() {
		return $this->pending === '';
	}

	// Are two records the same?
	public function equals($obj) {
		return $obj && $this->xref==$obj->getXref();
	}

	// Generate a URL to this record, suitable for use in HTML, etc.
	public function getHtmlUrl() {
		return $this->_getLinkUrl(static::URL_PREFIX, '&amp;');
	}
	// Generate a URL to this record, suitable for use in javascript, HTTP headers, etc.
	public function getRawUrl() {
		return $this->_getLinkUrl(static::URL_PREFIX, '&');
	}

	// Generate an absolute URL for this record, suitable for sitemap.xml, RSS feeds, etc.
	public function getAbsoluteLinkUrl() {
		return WT_SERVER_NAME . WT_SCRIPT_PATH . $this->getHtmlUrl();
	}

	private function _getLinkUrl($link, $separator) {
		if ($this->gedcom_id == WT_GED_ID) {
			return $link . $this->getXref() . $separator . 'ged=' . WT_GEDURL;
		} elseif ($this->gedcom_id == 0) {
			return '#';
		} else {
			return $link . $this->getXref() . $separator . 'ged=' . rawurlencode(get_gedcom_from_id($this->gedcom_id));
		}
	}

	// Work out whether this record can be shown to a user with a given access level
	private function _canShow($access_level) {
		global $person_privacy, $HIDE_LIVE_PEOPLE;

		// This setting would better be called "$ENABLE_PRIVACY"
		if (!$HIDE_LIVE_PEOPLE) {
			return true;
		}

		// We should always be able to see our own record (unless an admin is applying download restrictions)
		if ($this->getXref()==WT_USER_GEDCOM_ID && $this->getGedcomId()==WT_GED_ID && $access_level==WT_USER_ACCESS_LEVEL) {
			return true;
		}

		// Does this record have a RESN?
		if (strpos($this->gedcom, "\n1 RESN confidential")) {
			return WT_PRIV_NONE>=$access_level;
		}
		if (strpos($this->gedcom, "\n1 RESN privacy")) {
			return WT_PRIV_USER>=$access_level;
		}
		if (strpos($this->gedcom, "\n1 RESN none")) {
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
		return $this->_canShowByType($access_level);
	}

	// Each object type may have its own special rules, and re-implement this function.
	protected function _canShowByType($access_level) {
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
	public function canShow($access_level=WT_USER_ACCESS_LEVEL) {
		// CACHING: this function can take three different parameters, 
		// and therefore needs three different caches for the result.
		switch ($access_level) {
		case WT_PRIV_PUBLIC: // visitor
			if ($this->disp_public===null) {
				$this->disp_public=$this->_canShow(WT_PRIV_PUBLIC);
			}
			return $this->disp_public;
		case WT_PRIV_USER: // member
			if ($this->disp_user===null) {
				$this->disp_user=$this->_canShow(WT_PRIV_USER);
			}
			return $this->disp_user;
		case WT_PRIV_NONE: // admin
			if ($this->disp_none===null) {
				$this->disp_none=$this->_canShow(WT_PRIV_NONE);
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
	public function canShowName($access_level=WT_USER_ACCESS_LEVEL) {
		return $this->canShow($access_level);
	}

	// Can we edit this record?
	public function canEdit() {
		return WT_USER_GEDCOM_ADMIN || WT_USER_CAN_EDIT && strpos($this->gedcom, "\n1 RESN locked")===false;
	}

	// Remove private data from the raw gedcom record.
	// Return both the visible and invisible data.  We need the invisible data when editing.
	public function privatizeGedcom($access_level) {
		global $global_facts, $person_facts;

		if ($access_level==WT_PRIV_HIDE) {
			// We may need the original record, for example when downloading a GEDCOM or clippings cart
			return $this->gedcom;
		} elseif ($this->canShow($access_level)) {
			// The record is not private, but the individual facts may be.

			// Include the entire first line (for NOTE records)
			list($gedrec)=explode("\n", $this->gedcom, 2);

			// Check each of the sub facts for access
			preg_match_all('/\n1 .*(?:\n[2-9].*)*/', $this->gedcom, $matches);
			foreach ($matches[0] as $match) {
				if (canDisplayFact($this->xref, $this->gedcom_id, $match, $access_level)) {
					$gedrec.=$match;
				}
			}
			return $gedrec;
		} else {
			// We cannot display the details, but we may be able to display
			// limited data, such as links to other records.
			return $this->createPrivateGedcomRecord($access_level);
		}
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . '@ ' . static::RECORD_TYPE . "\n1 NOTE " . WT_I18N::translate('Private');
	}

	// Convert a name record into sortable and full/display versions.  This default
	// should be OK for simple record types.  INDI/FAM records will need to redefine it.
	protected function _addName($type, $value, $gedcom) {
		$this->_getAllNames[]=array(
			'type'=>$type,
			'sort'=>preg_replace_callback('/([0-9]+)/', function($matches) { return str_pad($matches[0], 10, '0', STR_PAD_LEFT); }, $value),
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
			if ($this->canShowName()) {
				$sublevel=$level+1;
				$subsublevel=$sublevel+1;
				if (preg_match_all("/^{$level} ({$fact}) (.+)((\n[{$sublevel}-9].+)*)/m", $this->getGedcom(), $matches, PREG_SET_ORDER)) {
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
		return $this->xref.'@'.$this->gedcom_id;
	}

	// Static helper function to sort an array of objects by name
	// Records whose names cannot be displayed are sorted at the end.
	static function Compare($x, $y) {
		if ($x->canShowName()) {
			if ($y->canShowName()) {
				return utf8_strcasecmp($x->getSortName(), $y->getSortName());
			} else {
				return -1; // only $y is private
			}
		} else {
			if ($y->canShowName()) {
				return 1; // only $x is private
			} else {
				return 0; // both $x and $y private
			}
		}
	}

	// Get variants of the name
	public function getFullName() {
		if ($this->canShowName()) {
			$tmp = $this->getAllNames();
			return $tmp[$this->getPrimaryName()]['full'];
		} else {
			return WT_I18N::translate('Private');
		}
	}
	public function getSortName() {
		// The sortable name is never displayed, no need to call canShowName()
		$tmp = $this->getAllNames();
		return $tmp[$this->getPrimaryName()]['sort'];
	}
	// Get the fullname in an alternative character set
	public function getAddName() {
		if ($this->canShowName() && $this->getPrimaryName()!=$this->getSecondaryName()) {
			$all_names = $this->getAllNames();
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
		foreach ($this->getFacts($facts) as $event) {
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
		return fetch_linked_indi($this->getXref(), static::RECORD_TYPE, $this->gedcom_id);
	}
	public function fetchLinkedFamilies() {
		return fetch_linked_fam($this->getXref(), static::RECORD_TYPE, $this->gedcom_id);
	}
	public function fetchLinkedNotes() {
		return fetch_linked_note($this->getXref(), static::RECORD_TYPE, $this->gedcom_id);
	}
	public function fetchLinkedSources() {
		return fetch_linked_sour($this->getXref(), static::RECORD_TYPE, $this->gedcom_id);
	}
	public function fetchLinkedRepositories() {
		return fetch_linked_repo($this->getXref(), static::RECORD_TYPE, $this->gedcom_id);
	}
	public function fetchLinkedMedia() {
		return fetch_linked_obje($this->getXref(), static::RECORD_TYPE, $this->gedcom_id);
	}

	// Get all attributes (e.g. DATE or PLAC) from an event (e.g. BIRT or MARR).
	// This is used to display multiple events on the individual/family lists.
	// Multiple events can exist because of uncertainty in dates, dates in different
	// calendars, place-names in both latin and hebrew character sets, etc.
	// It also allows us to combine dates/places from different events in the summaries.
	public function getAllEventDates($event) {
		$dates = array();
		foreach ($this->getFacts($event) as $event) {
			if ($event->getDate()->isOK() && $event->canShow()) {
				$dates[] = $event->getDate();
			}
		}
		return $dates;
	}
	public function getAllEventPlaces($event) {
		$places = array();
		foreach ($this->getFacts($event) as $event) {
			if (preg_match_all('/\n(?:2 PLAC|3 (?:ROMN|FONE|_HEB)) +(.+)/', $event->getGedcom(), $ged_places) && $event->canShow()) {
				foreach ($ged_places[1] as $ged_place) {
					$places[] = $ged_place;
				}
			}
		}
		return $places;
	}

	// Get the first WT_Fact for the given fact type
	public function getFactByType($tag) {
		foreach ($this->getFacts() as $fact) {
			if ($fact->getTag() == $tag && $fact->canShow()) {
				return $fact;
			}
		}
		return null;
	}

	// The facts and events for this record
	public function getFacts($filter=null) {
		if ($filter === null) {
			return $this->facts;
		} else {
			$facts=array();
			foreach ($this->facts as $fact) {
				if (preg_match('/^' . $filter . '$/', $fact->getTag())) {
					$facts[] = $fact;
				}
			}
			return $facts;
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// Get the last-change timestamp for this record, either as a formatted string
	// (for display) or as a unix timestamp (for sorting)
	//////////////////////////////////////////////////////////////////////////////
	public function LastChangeTimestamp($sorting=false) {
		$chan = $this->getFactByType('CHAN');

		if ($chan) {
			// The record does have a CHAN event
			$d = $chan->getDate()->MinDate();
			if (preg_match('/\n3 TIME (\d\d):(\d\d):(\d\d)/', $chan->getGedcom(), $match)) {
				$t=mktime((int)$match[1], (int)$match[2], (int)$match[3], (int)$d->Format('%n'), (int)$d->Format('%j'), (int)$d->Format('%Y'));
			} elseif (preg_match('/\n3 TIME (\d\d):(\d\d)/', $chan->getGedcom(), $match)) {
				$t=mktime((int)$match[1], (int)$match[2], 0, (int)$d->Format('%n'), (int)$d->Format('%j'), (int)$d->Format('%Y'));
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
		$chan = $this->getFactByType('CHAN');

		if (is_null($chan)) {
			return '&nbsp;';
		}

		$chan_user = $chan->getAttribute('_WT_USER');
		if (empty($chan_user)) {
			return '&nbsp;';
		}
		return $chan_user;
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// CRUD operations
	//////////////////////////////////////////////////////////////////////////////

	// Replace a (possibly empty) fact with a new (possibly empty) fact.
	// This allows create/update/delete of individual facts
	public function updateFact($fact_id, $gedcom, $update_chan) {
		if (strpos("\r", $gedcom)!==false) {
			// MSDOS line endings will break things in horrible ways
			throw new Exception('Evil line endings found in WT_GedcomRecord::updateRecord(' . $gedcom . ')');
		}
		if ($this->pending==='') {
			throw new Exception('Cannot edit a deleted record');
		}
		if ($gedcom && !preg_match('/^1 ' . WT_REGEX_TAG . '/', $gedcom)) {
			throw new Exception('Invalid GEDCOM data passed to WT_GedcomRecord::updateFact()');
		}

		if ($this->pending) {
			$old_gedcom = $this->pending;
		} else {
			$old_gedcom = $this->gedcom;
		}

		$new_gedcom = '0 @' . $this->getXref() . '@ ' . static::RECORD_TYPE;
		$old_chan = $this->getFactByType('CHAN');
		// Replacing (or deleting) an existing fact
		foreach ($this->getFacts() as $fact) {
			if ($fact->getFactId() == $fact_id) {
				if ($gedcom) {
					$new_gedcom .= "\n" . $gedcom;
				}
			} elseif ($fact->getTag()!='CHAN' || !$update_chan) {
				$new_gedcom .= "\n" . $fact->getGedcom();
			}
		}
		if ($update_chan) {
			$new_gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . WT_USER_NAME;
		}
		
		// Adding a new fact
		if (!$fact_id) {
			$new_gedcom .= "\n" . $gedcom;
		}

		if ($new_gedcom != $old_gedcom) {
			// Save the changes
			WT_DB::prepare(
				"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, ?, ?)"
			)->execute(array(
				$this->gedcom_id,
				$this->xref,
				$old_gedcom,
				$new_gedcom,
				WT_USER_ID
			));

			// Clear the cache
			self::$gedcom_record_cache = null;
			self::$pending_record_cache = null;

			if (get_user_setting(WT_USER_ID, 'auto_accept')) {
				accept_all_changes($xref, $ged_id);
			}
		}
	}
	
	static public function createRecord($gedcom, $gedcom_id) {
		if (preg_match('/^0 @(' . WT_REGEX_XREF . ')@ (' . WT_REGEX_TAG . ')/', $gedcom, $match)) {
			$xref = $match[1];
			$type = $match[2];
		} else {
			throw new Exception('Invalid argument to WT_GedcomRecord::createRecord(' . $gedcom . ')');
		}
		if (strpos("\r", $gedcom)!==false) {
			// MSDOS line endings will break things in horrible ways
			throw new Exception('Evil line endings found in WT_GedcomRecord::createRecord(' . $gedcom . ')');
		}

		// webtrees creates XREFs containing digits.  Anything else (e.g. “new”) is just a placeholder.
		if (!preg_match('/\d/', $xref)) {
			$xref   = get_new_xref($type);
			$gedcom = preg_replace('/^0 @(' . WT_REGEX_XREF . ')@/', '0 @' . $xref . '@', $gedcom);
		}

		// Create a change record, if not already present
		if (!preg_match('/\n1 CHAN/', $gedcom)) {
			$gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . WT_USER_NAME;
		}

		// Create a pending change
		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
		)->execute(array(
			$gedcom_id,
			$xref,
			$gedcom,
			$gedcom_id
		));

		// Accept this pending change
		if (get_user_setting(WT_USER_ID, 'auto_accept')) {
			accept_all_changes($this->xref, $this->gedcom_id);
		}

		// Clear this record from the cache
		self::$pending_record_cache = null;

		AddToLog('Create: ' . $type . ' ' . $xref, 'edit');

		// Return the newly created record
		return WT_GedcomRecord::getInstance($xref);
	}

	private static function readRecord($xref, $gedcom_id) {
		return WT_DB::prepare(static::SQL_FETCH)->execute(array($xref, $gedcom_id))->fetchOne();
	}

	public function updateRecord($gedcom, $update_chan) {
		if (strpos("\r", $gedcom)!==false) {
			// MSDOS line endings will break things in horrible ways
			throw new Exception('Evil line endings found in WT_GedcomRecord::updateRecord(' . $gedcom . ')');
		}

		// Update the CHAN record
		if ($update_chan) {
			$gedcom = preg_replace('/\n1 CHAN(\n[2-9].*)*/', '', $gedcom);
			$gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . WT_USER_NAME;
		}

		// Create a pending change
		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, ?, ?)"
		)->execute(array(
			$this->gedcom_id,
			$this->xref,
			$this->getGedcom(),
			$gedcom,
			WT_USER_ID
		));

		// Accept this pending change
		if (get_user_setting(WT_USER_ID, 'auto_accept')) {
			accept_all_changes($this->xref, $this->gedcom_id);
		}

		// Clear the cache
		self::$gedcom_record_cache = null;
		self::$pending_record_cache = null;

		AddToLog('Update: ' . static::RECORD_TYPE . ' ' . $this->xref, 'edit');
	}

	public function deleteRecord() {
		// Create a pending change
		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, '', ?)"
		)->execute(array(
			$this->gedcom_id,
			$this->xref,
			$this->getGedcom(),
			WT_USER_ID
		));

		// Accept this pending change
		if (get_user_setting(WT_USER_ID, 'auto_accept')) {
			accept_all_changes($this->xref, $this->gedcom_id);
		}

		// Clear the cache
		self::$gedcom_record_cache = null;
		self::$pending_record_cache = null;

		AddToLog('Delete: ' . static::RECORD_TYPE . ' ' . $this->xref, 'edit');
	}
}
