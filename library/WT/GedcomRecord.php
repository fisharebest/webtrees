<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\Log;

/**
 * Class WT_GedcomRecord - Base class for all gedcom records
 */
class WT_GedcomRecord {
	const RECORD_TYPE = 'UNKNOWN';
	const URL_PREFIX  = 'gedrecord.php?pid=';

	protected $xref        = null;  // The record identifier
	protected $gedcom_id   = null;  // The gedcom file
	protected $gedcom      = null;  // GEDCOM data (before any pending edits)
	protected $pending     = null;  // GEDCOM data (after any pending edits)

	/** @var WT_Fact[] facts extracted from $gedcom/$pending */
	protected $facts       = null;

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

	/**
	 * Create a GedcomRecord object from raw GEDCOM data.
	 *
	 * @param string      $xref
	 * @param string      $gedcom  an empty string for new/pending records
	 * @param string|null $pending null for a record with no pending edits,
	 *                             empty string for records with pending deletions
	 * @param integer     $gedcom_id
	 */
	public function __construct($xref, $gedcom, $pending, $gedcom_id) {
		$this->xref      = $xref;
		$this->gedcom    = $gedcom;
		$this->pending   = $pending;
		$this->gedcom_id = $gedcom_id;

		$this->parseFacts();
	}

	/**
	 * Split the record into facts
	 */
	private function parseFacts() {
		// Split the record into facts
		if ($this->gedcom) {
			$gedcom_facts = preg_split('/\n(?=1)/s', $this->gedcom);
			array_shift($gedcom_facts);
		} else {
			$gedcom_facts = array();
		}
		if ($this->pending) {
			$pending_facts = preg_split('/\n(?=1)/s', $this->pending);
			array_shift($pending_facts);
		} else {
			$pending_facts = array();
		}

		$this->facts = array();

		foreach ($gedcom_facts as $gedcom_fact) {
			$fact = new WT_Fact($gedcom_fact, $this, md5($gedcom_fact));
			if ($this->pending !== null && !in_array($gedcom_fact, $pending_facts)) {
				$fact->setPendingDeletion();
			}
			$this->facts[] = $fact;
		}
		foreach ($pending_facts as $pending_fact) {
			if (!in_array($pending_fact, $gedcom_facts)) {
				$fact = new WT_Fact($pending_fact, $this, md5($pending_fact));
				$fact->setPendingAddition();
				$this->facts[] = $fact;
			}
		}
	}

	/**
	 * Get an instance of a GedcomRecord object.  For single records,
	 * we just receive the XREF.  For bulk records (such as lists
	 * and search results) we can receive the GEDCOM data as well.
	 *
	 * @param string       $xref
	 * @param integer|null $gedcom_id
	 * @param string|null  $gedcom
	 *
	 * @return WT_GedcomRecord|null
	 * @throws Exception
	 */
	public static function getInstance($xref, $gedcom_id=WT_GED_ID, $gedcom=null) {
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

		// No such record exists
		if ($gedcom === null && $pending === null) {
			return null;
		}

		// Create the object
		if (preg_match('/^0 @(' . WT_REGEX_XREF . ')@ (' . WT_REGEX_TAG . ')/', $gedcom.$pending, $match)) {
			$xref = $match[1]; // Collation - we may have requested I123 and found i123
			$type = $match[2];
		} elseif (preg_match('/^0 (HEAD|TRLR)/', $gedcom.$pending, $match)) {
			$xref = $match[1];
			$type = $match[1];
		} elseif ($gedcom.$pending) {
			throw new Exception('Unrecognized GEDCOM record: ' . $gedcom);
		} else {
			// A record with both pending creation and pending deletion
			$type = static::RECORD_TYPE;
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
		default:
			$record = new WT_GedcomRecord($xref, $gedcom, $pending, $gedcom_id);
			break;
		}

		// Store it in the cache
		self::$gedcom_record_cache[$xref][$gedcom_id] = $record;

		return $record;
	}

	/**
	 * Fetch data from the database
	 *
	 * @param string  $xref
	 * @param integer $gedcom_id
	 *
	 * @return null|string
	 */
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
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

	/**
	 * Get the XREF for this record
	 *
	 * @return string
	 */
	public function getXref() {
		return $this->xref;
	}

	/**
	 * Get the tree ID for this record
	 *
	 * @return integer
	 */
	public function getGedcomId() {
		return $this->gedcom_id;
	}

	/**
	 * Application code should access data via WT_Fact objects.
	 * This function exists to support old code.
	 *
	 * @return string
	 */
	public function getGedcom() {
		if ($this->pending === null) {
			return $this->gedcom;
		} else {
			return $this->pending;
		}
	}

	/**
	 * Does this record have a pending change?
	 *
	 * @return boolean
	 */
	public function isPendingAddtion() {
		return $this->pending !== null;
	}

	/**
	 * Does this record have a pending deletion?
	 *
	 * @return boolean
	 */
	public function isPendingDeletion() {
		return $this->pending === '';
	}

	/**
	 * Generate a URL to this record, suitable for use in HTML, etc.
	 *
	 * @return string
	 */
	public function getHtmlUrl() {
		return $this->getLinkUrl(static::URL_PREFIX, '&amp;');
	}

	/**
	 * Generate a URL to this record, suitable for use in javascript, HTTP headers, etc.
	 *
	 * @return string
	 */
	public function getRawUrl() {
		return $this->getLinkUrl(static::URL_PREFIX, '&');
	}

	/**
	 * Generate an absolute URL for this record, suitable for sitemap.xml, RSS feeds, etc.
	 *
	 * @return string
	 */
	public function getAbsoluteLinkUrl() {
		return WT_SERVER_NAME . WT_SCRIPT_PATH . $this->getHtmlUrl();
	}

	/**
	 * Generate a URL to this record.
	 *
	 * @param string $link
	 * @param string $separator
	 *
	 * @return string
	 */
	private function getLinkUrl($link, $separator) {
		if ($this->gedcom_id == WT_GED_ID) {
			return $link . $this->getXref() . $separator . 'ged=' . WT_GEDURL;
		} elseif ($this->gedcom_id == 0) {
			return '#';
		} else {
			return $link . $this->getXref() . $separator . 'ged=' . rawurlencode(get_gedcom_from_id($this->gedcom_id));
		}
	}

	/**
	 * Work out whether this record can be shown to a user with a given access level
	 *
	 * @param integer $access_level
	 *
	 * @return boolean
	 */
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
		return $this->canShowByType($access_level);
	}

	/**
	 * Each object type may have its own special rules, and re-implement this function.
	 *
	 * @param integer $access_level
	 *
	 * @return boolean
	 */
	protected function canShowByType($access_level) {
		global $global_facts;

		if (isset($global_facts[static::RECORD_TYPE])) {
			// Restriction found
			return $global_facts[static::RECORD_TYPE]>=$access_level;
		} else {
			// No restriction found - must be public:
			return true;
		}
	}

	/**
	 * Can the details of this record be shown?
	 *
	 * @param integer $access_level
	 *
	 * @return boolean
	 */
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

	/**
	 * Can the name of this record be shown?
	 *
	 * @param integer $access_level
	 *
	 * @return boolean
	 */
	public function canShowName($access_level=WT_USER_ACCESS_LEVEL) {
		return $this->canShow($access_level);
	}

	/**
	 * Can we edit this record?
	 *
	 * @return boolean
	 */
	public function canEdit() {
		return WT_USER_GEDCOM_ADMIN || WT_USER_CAN_EDIT && strpos($this->gedcom, "\n1 RESN locked")===false;
	}

	/**
	 * Remove private data from the raw gedcom record.
	 * Return both the visible and invisible data.  We need the invisible data when editing.
	 *
	 * @param integer $access_level
	 *
	 * @return string
	 */
	public function privatizeGedcom($access_level) {
		if ($access_level==WT_PRIV_HIDE) {
			// We may need the original record, for example when downloading a GEDCOM or clippings cart
			return $this->gedcom;
		} elseif ($this->canShow($access_level)) {
			// The record is not private, but the individual facts may be.

			// Include the entire first line (for NOTE records)
			list($gedrec)=explode("\n", $this->gedcom, 2);

			// Check each of the facts for access
			foreach ($this->getFacts(null, false, $access_level) as $fact) {
				$gedrec .= "\n" . $fact->getGedcom();
			}
			return $gedrec;
		} else {
			// We cannot display the details, but we may be able to display
			// limited data, such as links to other records.
			return $this->createPrivateGedcomRecord($access_level);
		}
	}

	/**
	 * Generate a private version of this record
	 *
	 * @param integer $access_level
	 *
	 * @return string
	 */
	protected function createPrivateGedcomRecord($access_level) {
		return '0 @' . $this->xref . '@ ' . static::RECORD_TYPE . "\n1 NOTE " . WT_I18N::translate('Private');
	}

	/**
	 * Convert a name record into sortable and full/display versions.  This default
	 * should be OK for simple record types.  INDI/FAM records will need to redefine it.
	 *
	 * @param string $type
	 * @param string $value
	 * @param string $gedcom
	 */
	protected function addName($type, $value, $gedcom) {
		$this->_getAllNames[] = array(
			'type'   => $type,
			'sort'   => preg_replace_callback('/([0-9]+)/', function($matches) { return str_pad($matches[0], 10, '0', STR_PAD_LEFT); }, $value),
			'full'   => '<span dir="auto">'.WT_Filter::escapeHtml($value).'</span>',    // This is used for display
			'fullNN' => $value, // This goes into the database
		);
	}

	/**
	 * Get all the names of a record, including ROMN, FONE and _HEB alternatives.
	 * Records without a name (e.g. FAM) will need to redefine this function.
	 * Parameters: the level 1 fact containing the name.
	 * Return value: an array of name structures, each containing
	 * ['type'] = the gedcom fact, e.g. NAME, TITL, FONE, _HEB, etc.
	 * ['full'] = the name as specified in the record, e.g. 'Vincent van Gogh' or 'John Unknown'
	 * ['sort'] = a sortable version of the name (not for display), e.g. 'Gogh, Vincent' or '@N.N., John'
	 *
	 * @param integer    $level
	 * @param string    $fact_type
	 * @param WT_Fact[] $facts
	 */
	protected function _extractNames($level, $fact_type, $facts) {
		$sublevel    = $level + 1;
		$subsublevel = $sublevel + 1;
		foreach ($facts as $fact) {
			if (preg_match_all("/^{$level} ({$fact_type}) (.+)((\n[{$sublevel}-9].+)*)/m", $fact->getGedcom(), $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					// Treat 1 NAME / 2 TYPE married the same as _MARNM
					if ($match[1]=='NAME' && strpos($match[3], "\n2 TYPE married")!==false) {
						$this->addName('_MARNM', $match[2], $fact->getGedcom());
					} else {
						$this->addName($match[1], $match[2], $fact->getGedcom());
					}
					if ($match[3] && preg_match_all("/^{$sublevel} (ROMN|FONE|_\w+) (.+)((\n[{$subsublevel}-9].+)*)/m", $match[3], $submatches, PREG_SET_ORDER)) {
						foreach ($submatches as $submatch) {
							$this->addName($submatch[1], $submatch[2], $match[3]);
						}
					}
				}
			}
		}
	}

	/**
	 * Default for "other" object types
	 */
	public function extractNames() {
		$this->addName(static::RECORD_TYPE, $this->getFallBackName(), null);
	}

	/**
	 * Derived classes should redefine this function, otherwise the object will have no name
	 *
	 * @return string[][]
	 */
	public function getAllNames() {
		if ($this->_getAllNames === null) {
			$this->_getAllNames = array();
			if ($this->canShowName()) {
				// Ask the record to extract its names
				$this->extractNames();
				// No name found?  Use a fallback.
				if (!$this->_getAllNames) {
					$this->addName(static::RECORD_TYPE, $this->getFallBackName(), null);
				}
			} else {
				$this->addName(static::RECORD_TYPE, WT_I18N::translate('Private'), null);
			}
		}
		return $this->_getAllNames;
	}

	/**
	 * If this object has no name, what do we call it?
	 *
	 * @return string
	 */
	public function getFallBackName() {
		return $this->getXref();
	}

	/**
	 * Which of the (possibly several) names of this record is the primary one.
	 *
	 * @return integer
	 */
	public function getPrimaryName() {
		if (is_null($this->_getPrimaryName)) {
			// Generally, the first name is the primary one....
			$this->_getPrimaryName=0;
			// ....except when the language/name use different character sets
			if (count($this->getAllNames())>1) {
				switch (WT_LOCALE) {
				case 'el':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && WT_I18N::textScript($name['sort'])=='Grek') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'ru':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && WT_I18N::textScript($name['sort'])=='Cyrl') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'he':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && WT_I18N::textScript($name['sort'])=='Hebr') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				case 'ar':
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && WT_I18N::textScript($name['sort'])=='Arab') {
							$this->_getPrimaryName=$n;
							break;
						}
					}
					break;
				default:
					foreach ($this->getAllNames() as $n=>$name) {
						if ($name['type']!='_MARNM' && WT_I18N::textScript($name['sort'])=='Latn') {
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

	/**
	 * Which of the (possibly several) names of this record is the secondary one.
	 *
	 * @return integer
	 */
	public function getSecondaryName() {
		if (is_null($this->_getSecondaryName)) {
			// Generally, the primary and secondary names are the same
			$this->_getSecondaryName=$this->getPrimaryName();
			// ....except when there are names with different character sets
			$all_names=$this->getAllNames();
			if (count($all_names)>1) {
				$primary_script=WT_I18N::textScript($all_names[$this->getPrimaryName()]['sort']);
				foreach ($all_names as $n=>$name) {
					if ($n!=$this->getPrimaryName() && $name['type']!='_MARNM' && WT_I18N::textScript($name['sort'])!=$primary_script) {
						$this->_getSecondaryName=$n;
						break;
					}
				}
			}
		}
		return $this->_getSecondaryName;
	}

	/**
	 * Allow the choice of primary name to be overidden, e.g. in a search result
	 *
	 * @param integer $n
	 */
	public function setPrimaryName($n) {
		$this->_getPrimaryName   = $n;
		$this->_getSecondaryName = null;
	}

	/**
	 * Allow native PHP functions such as array_unique() to work with objects
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->xref . '@' . $this->gedcom_id;
	}

	/**
	 * Static helper function to sort an array of objects by name
	 * Records whose names cannot be displayed are sorted at the end.
	 *
	 * @param WT_GedcomRecord $x
	 * @param WT_GedcomRecord $y
	 *
	 * @return integer
	 */
	public static function compare(WT_GedcomRecord $x, WT_GedcomRecord $y) {
		if ($x->canShowName()) {
			if ($y->canShowName()) {
				return WT_I18N::strcasecmp($x->getSortName(), $y->getSortName());
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

	/**
	 * Get variants of the name
	 *
	 * @return string
	 */
	public function getFullName() {
		if ($this->canShowName()) {
			$tmp = $this->getAllNames();
			return $tmp[$this->getPrimaryName()]['full'];
		} else {
			return WT_I18N::translate('Private');
		}
	}

	/**
	 * Get a sortable version of the name.  Do not display this!
	 *
	 * @return string
	 */
	public function getSortName() {
		// The sortable name is never displayed, no need to call canShowName()
		$tmp = $this->getAllNames();
		return $tmp[$this->getPrimaryName()]['sort'];
	}

	/**
	 * Get the full name in an alternative character set
	 *
	 * @return null|string
	 */
	public function getAddName() {
		if ($this->canShowName() && $this->getPrimaryName()!=$this->getSecondaryName()) {
			$all_names = $this->getAllNames();
			return $all_names[$this->getSecondaryName()]['full'];
		} else {
			return null;
		}
	}

	/**
	 * Format this object for display in a list
	 * If $find is set, then we are displaying items from a selection list.
	 * $name allows us to use something other than the record name.
	 *
	 * @param string  $tag
	 * @param boolean $find
	 * @param null    $name
	 *
	 * @return string
	 */
	public function format_list($tag='li', $find=false, $name=null) {
		if (is_null($name)) {
			$name=$this->getFullName();
		}
		$html='<a href="'.$this->getHtmlUrl().'"';
		if ($find) {
			$html.=' onclick="pasteid(\''.$this->getXref().'\', \'' . htmlentities($name) . '\');"';
		}
		$html.=' class="list_item"><b>'.$name.'</b>';
		$html.=$this->formatListDetails();
		$html='<'.$tag.'>'.$html.'</a></'.$tag.'>';
		return $html;
	}

	/**
	 * This function should be redefined in derived classes to show any major
	 * identifying characteristics of this record.
	 *
	 * @return string
	 */
	public function formatListDetails() {
		return '';
	}

	/**
	 * Extract/format the first fact from a list of facts.
	 *
	 * @param string  $facts
	 * @param integer $style
	 *
	 * @return string
	 */
	public function format_first_major_fact($facts, $style) {
		foreach ($this->getFacts($facts, true) as $event) {
			// Only display if it has a date or place (or both)
			if ($event->getDate()->isOK() || !$event->getPlace()->isEmpty()) {
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

	/**
	 * Find individuals linked to this record.
	 *
	 * @param string $link
	 *
	 * @return array
	 */
	public function linkedIndividuals($link) {
		$rows = WT_DB::prepare(
			"SELECT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom" .
			" FROM `##individuals`" .
			" JOIN `##link` ON (i_file=l_file AND i_id=l_from)" .
			" LEFT JOIN `##name` ON (i_file=n_file AND i_id=n_id AND n_num=0)" .
			" WHERE i_file=? AND l_type=? AND l_to=?" .
			" ORDER BY n_sort COLLATE '" . WT_I18N::$collation . "'"
		)->execute(array($this->gedcom_id, $link, $this->xref))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$record = WT_Individual::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($record->canShowName()) {
				$list[] = $record;
			}
		}
		return $list;
	}

	/**
	 * Find families linked to this record.
	 *
	 * @param string $link
	 *
	 * @return array
	 */
	public function linkedFamilies($link) {
		$rows = WT_DB::prepare(
			"SELECT f_id AS xref, f_file AS gedcom_id, f_gedcom AS gedcom" .
			" FROM `##families`" .
			" JOIN `##link` ON (f_file=l_file AND f_id=l_from)" .
			" LEFT JOIN `##name` ON (f_file=n_file AND f_id=n_id AND n_num=0)" .
			" WHERE f_file=? AND l_type=? AND l_to=?"
		)->execute(array($this->gedcom_id, $link, $this->xref))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$record = WT_Family::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($record->canShowName()) {
				$list[] = $record;
			}
		}
		return $list;
	}

	/**
	 * Find sources linked to this record.
	 *
	 * @param string $link
	 *
	 * @return array
	 */
	public function linkedSources($link) {
		$rows = WT_DB::prepare(
				"SELECT s_id AS xref, s_file AS gedcom_id, s_gedcom AS gedcom" .
				" FROM `##sources`" .
				" JOIN `##link` ON (s_file=l_file AND s_id=l_from)" .
				" WHERE s_file=? AND l_type=? AND l_to=?" .
				" ORDER BY s_name COLLATE '" . WT_I18N::$collation . "'"
			)->execute(array($this->gedcom_id, $link, $this->xref))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$record = WT_Source::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($record->canShowName()) {
				$list[] = $record;
			}
		}
		return $list;
	}

	/**
	 * Find media objects linked to this record.
	 *
	 * @param string $link
	 *
	 * @return array
	 */
	public function linkedMedia($link) {
		$rows = WT_DB::prepare(
			"SELECT m_id AS xref, m_file AS gedcom_id, m_gedcom AS gedcom" .
			" FROM `##media`" .
			" JOIN `##link` ON (m_file=l_file AND m_id=l_from)" .
			" WHERE m_file=? AND l_type=? AND l_to=?" .
			" ORDER BY m_titl COLLATE '" . WT_I18N::$collation . "'"
		)->execute(array($this->gedcom_id, $link, $this->xref))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$record = WT_Media::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($record->canShowName()) {
				$list[] = $record;
			}
		}
		return $list;
	}

	/**
	 * Find notes linked to this record.
	 *
	 * @param string $link
	 *
	 * @return WT_Note[]
	 */
	public function linkedNotes($link) {
		$rows=WT_DB::prepare(
			"SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom".
			" FROM `##other`".
			" JOIN `##link` ON (o_file=l_file AND o_id=l_from)".
			" LEFT JOIN `##name` ON (o_file=n_file AND o_id=n_id AND n_num=0)".
			" WHERE o_file=? AND o_type='NOTE' AND l_type=? AND l_to=?".
			" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
		)->execute(array($this->gedcom_id, $link, $this->xref))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$record = WT_Note::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($record->canShowName()) {
				$list[] = $record;
			}
		}
		return $list;
	}

	/**
	 * Find repositories linked to this record.
	 *
	 * @param string $link
	 *
	 * @return WT_Repository
	 */
	public function linkedRepositories($link) {
		$rows=WT_DB::prepare(
			"SELECT o_id AS xref, o_file AS gedcom_id, o_gedcom AS gedcom".
			" FROM `##other`".
			" JOIN `##link` ON (o_file=l_file AND o_id=l_from)".
			" LEFT JOIN `##name` ON (o_file=n_file AND o_id=n_id AND n_num=0)".
			" WHERE o_file=? AND o_type='REPO' AND l_type=? AND l_to=?".
			" ORDER BY n_sort COLLATE '".WT_I18N::$collation."'"
		)->execute(array($this->gedcom_id, $link, $this->xref))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$record = WT_Repository::getInstance($row->xref, $row->gedcom_id, $row->gedcom);
			if ($record->canShowName()) {
				$list[] = $record;
			}
		}
		return $list;
	}

	/**
	 * Get all attributes (e.g. DATE or PLAC) from an event (e.g. BIRT or MARR).
	 * This is used to display multiple events on the individual/family lists.
	 * Multiple events can exist because of uncertainty in dates, dates in different
	 * calendars, place-names in both latin and hebrew character sets, etc.
	 * It also allows us to combine dates/places from different events in the summaries.
	 *
	 * @param string $event_type
	 *
	 * @return WT_Date[]
	 */
	public function getAllEventDates($event_type) {
		$dates = array();
		foreach ($this->getFacts($event_type) as $event) {
			if ($event->getDate()->isOK()) {
				$dates[] = $event->getDate();
			}
		}

		return $dates;
	}

	/**
	 * Get all the places for a particular type of event
	 *
	 * @param string $event_type
	 *
	 * @return array
	 */
	public function getAllEventPlaces($event_type) {
		$places = array();
		foreach ($this->getFacts($event_type) as $event) {
			if (preg_match_all('/\n(?:2 PLAC|3 (?:ROMN|FONE|_HEB)) +(.+)/', $event->getGedcom(), $ged_places)) {
				foreach ($ged_places[1] as $ged_place) {
					$places[] = $ged_place;
				}
			}
		}

		return $places;
	}

	/**
	 * Get the first (i.e. prefered) WT_Fact for the given fact type
	 *
	 * @param $tag
	 *
	 * @return null|WT_Fact
	 */
	public function getFirstFact($tag) {
		foreach ($this->getFacts() as $fact) {
			if ($fact->getTag() == $tag) {
				return $fact;
			}
		}

		return null;
	}

	/**
	 * The facts and events for this record.
	 *
	 * @param string  $filter
	 * @param boolean $sort
	 * @param integer $access_level
	 * @param boolean $override     Include private records, to allow us to implement $SHOW_PRIVATE_RELATIONSHIPS and $SHOW_LIVING_NAMES.
	 *
	 * @return WT_Fact[]
	 */
	public function getFacts($filter=null, $sort=false, $access_level=WT_USER_ACCESS_LEVEL, $override=false) {
		$facts=array();
		if ($this->canShow($access_level) || $override) {
			foreach ($this->facts as $fact) {
				if (($filter==null || preg_match('/^' . $filter . '$/', $fact->getTag())) && $fact->canShow($access_level)) {
					$facts[] = $fact;
				}
			}
		}
		if ($sort) {
			sort_facts($facts);
		}
		return $facts;
	}

	/**
	 * Get the last-change timestamp for this record, either as a formatted string
	 * (for display) or as a unix timestamp (for sorting)
	 *
	 * @param boolean $sorting
	 *
	 * @return string
	 */
	public function lastChangeTimestamp($sorting=false) {
		$chan = $this->getFirstFact('CHAN');

		if ($chan) {
			// The record does have a CHAN event
			$d = $chan->getDate()->MinDate();
			if (preg_match('/\n3 TIME (\d\d):(\d\d):(\d\d)/', $chan->getGedcom(), $match)) {
				$t=mktime((int)$match[1], (int)$match[2], (int)$match[3], (int)$d->format('%n'), (int)$d->format('%j'), (int)$d->format('%Y'));
			} elseif (preg_match('/\n3 TIME (\d\d):(\d\d)/', $chan->getGedcom(), $match)) {
				$t=mktime((int)$match[1], (int)$match[2], 0, (int)$d->format('%n'), (int)$d->format('%j'), (int)$d->format('%Y'));
			} else {
				$t=mktime(0, 0, 0, (int)$d->format('%n'), (int)$d->format('%j'), (int)$d->format('%Y'));
			}
			if ($sorting) {
				return $t;
			} else {
				return strip_tags(format_timestamp($t));
			}
		} else {
			// The record does not have a CHAN event
			if ($sorting) {
				return '0';
			} else {
				return '&nbsp;';
			}
		}
	}

	/**
	 * Get the last-change user for this record
	 *
	 * @return string
	 */
	public function lastChangeUser() {
		$chan = $this->getFirstFact('CHAN');

		if ($chan === null) {
			return '&nbsp;';
		}

		$chan_user = $chan->getAttribute('_WT_USER');
		if (empty($chan_user)) {
			return '&nbsp;';
		}
		return $chan_user;
	}

	/**
	 * Add a new fact to this record
	 *
	 * @param string  $gedcom
	 * @param boolean $update_chan
	 */
	public function createFact($gedcom, $update_chan) {
		$this->updateFact(null, $gedcom, $update_chan);
	}

	/**
	 * Delete a fact from this record
	 *
	 * @param string  $fact_id
	 * @param boolean $update_chan
	 */
	public function deleteFact($fact_id, $update_chan) {
		$this->updateFact($fact_id, null, $update_chan);
	}

	/**
	 * Replace a fact with a new gedcom data.
	 *
	 * @param string  $fact_id
	 * @param string  $gedcom
	 * @param boolean $update_chan
	 *
	 * @throws Exception
	 */
	public function updateFact($fact_id, $gedcom, $update_chan) {
		// MSDOS line endings will break things in horrible ways
		$gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
		$gedcom = trim($gedcom);

		if ($this->pending==='') {
			throw new Exception('Cannot edit a deleted record');
		}
		if ($gedcom && !preg_match('/^1 ' . WT_REGEX_TAG . '/', $gedcom)) {
			throw new Exception('Invalid GEDCOM data passed to WT_GedcomRecord::updateFact(' . $gedcom . ')');
		}

		if ($this->pending) {
			$old_gedcom = $this->pending;
		} else {
			$old_gedcom = $this->gedcom;
		}

		// First line of record may contain data - e.g. NOTE records.
		list($new_gedcom) = explode("\n", $old_gedcom, 2);

		// Replacing (or deleting) an existing fact
		foreach ($this->getFacts(null, false, WT_PRIV_HIDE) as $fact) {
			if (!$fact->isPendingDeletion()) {
				if ($fact->getFactId() === $fact_id) {
					if ($gedcom) {
						$new_gedcom .= "\n" . $gedcom;
					}
					$fact_id = true; // Only replace/delete one copy of a duplicate fact
				} elseif ($fact->getTag()!='CHAN' || !$update_chan) {
					$new_gedcom .= "\n" . $fact->getGedcom();
				}
			}
		}
		if ($update_chan) {
			$new_gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();
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
				Auth::id()
			));

			$this->pending = $new_gedcom;

			if (Auth::user()->getPreference('auto_accept')) {
				accept_all_changes($this->xref, $this->gedcom_id);
				$this->gedcom  = $new_gedcom;
				$this->pending = null;
			}
		}
		$this->parseFacts();
	}

	/**
	 * Create a new record from GEDCOM data.
	 *
	 * @param string  $gedcom
	 * @param integer $gedcom_id
	 *
	 * @return WT_GedcomRecord
	 * @throws Exception
	 */
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
			$gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();
		}

		// Create a pending change
		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, '', ?, ?)"
		)->execute(array(
			$gedcom_id,
			$xref,
			$gedcom,
			Auth::id()
		));

		// Accept this pending change
		if (Auth::user()->getPreference('auto_accept')) {
			accept_all_changes($xref, $gedcom_id);
		}

		// Clear this record from the cache
		self::$pending_record_cache = null;

		Log::addEditLog('Create: ' . $type . ' ' . $xref);

		// Return the newly created record
		return WT_GedcomRecord::getInstance($xref);
	}

	/**
	 * Update this record
	 *
	 * @param string  $gedcom
	 * @param boolean $update_chan
	 */
	public function updateRecord($gedcom, $update_chan) {
		// MSDOS line endings will break things in horrible ways
		$gedcom = preg_replace('/[\r\n]+/', "\n", $gedcom);
		$gedcom = trim($gedcom);

		// Update the CHAN record
		if ($update_chan) {
			$gedcom = preg_replace('/\n1 CHAN(\n[2-9].*)*/', '', $gedcom);
			$gedcom .= "\n1 CHAN\n2 DATE " . date('d M Y') . "\n3 TIME " . date('H:i:s') . "\n2 _WT_USER " . Auth::user()->getUserName();
		}

		// Create a pending change
		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, ?, ?)"
		)->execute(array(
			$this->gedcom_id,
			$this->xref,
			$this->getGedcom(),
			$gedcom,
			Auth::id()
		));

		// Clear the cache
		$this->pending = $gedcom;

		// Accept this pending change
		if (Auth::user()->getPreference('auto_accept')) {
			accept_all_changes($this->xref, $this->gedcom_id);
			$this->gedcom  = $gedcom;
			$this->pending = null;
		}

		$this->parseFacts();

		Log::addEditLog('Update: ' . static::RECORD_TYPE . ' ' . $this->xref);
	}

	/**
	 * Delete this record
	 */
	public function deleteRecord() {
		// Create a pending change
		WT_DB::prepare(
			"INSERT INTO `##change` (gedcom_id, xref, old_gedcom, new_gedcom, user_id) VALUES (?, ?, ?, '', ?)"
		)->execute(array(
			$this->gedcom_id,
			$this->xref,
			$this->getGedcom(),
			Auth::id(),
		));

		// Accept this pending change
		if (Auth::user()->getPreference('auto_accept')) {
			accept_all_changes($this->xref, $this->gedcom_id);
		}

		// Clear the cache
		self::$gedcom_record_cache = null;
		self::$pending_record_cache = null;

		Log::addEditLog('Delete: ' . static::RECORD_TYPE . ' ' . $this->xref);
	}

	/**
	 * Remove all links from this record to $xref
	 *
	 * @param string  $xref
	 * @param boolean $update_chan
	 */
	public function removeLinks($xref, $update_chan) {
		$value = '@' . $xref . '@';

		foreach ($this->getFacts() as $fact) {
			if ($fact->getValue() == $value) {
				$this->deleteFact($fact->getFactId(), $update_chan);
			} elseif (preg_match_all('/\n(\d) ' . WT_REGEX_TAG . ' ' . $value . '/', $fact->getGedcom(), $matches, PREG_SET_ORDER)) {
				$gedcom = $fact->getGedcom();
				foreach ($matches as $match) {
					$next_level = $match[1] + 1;
					$next_levels = '[' . $next_level . '-9]';
					$gedcom = preg_replace('/' . $match[0] . '(\n' . $next_levels .'.*)*/', '', $gedcom);
				}
				$this->updateFact($fact->getFactId(), $gedcom, $update_chan);
			}
		}
	}
}
