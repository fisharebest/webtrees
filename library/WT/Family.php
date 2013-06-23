<?php
// Class file for a Family
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

class WT_Family extends WT_GedcomRecord {
	const RECORD_TYPE = 'FAM';
	const URL_PREFIX  = 'family.php?famid=';

	private $husb = null;
	private $wife = null;
	private $marriage = null;

	// Create a Family object from either raw GEDCOM data or a database row
	function __construct($data) {
		if (is_array($data)) {
			// Construct from a row from the database
			if (preg_match('/^1 HUSB @(.+)@/m', $data['gedrec'], $match)) {
				$this->husb=WT_Person::getInstance($match[1]);
			}
			if (preg_match('/^1 WIFE @(.+)@/m', $data['gedrec'], $match)) {
				$this->wife=WT_Person::getInstance($match[1]);
			}
		} else {
			// Construct from raw GEDCOM data
			if (preg_match('/^1 HUSB @(.+)@/m', $data, $match)) {
				$this->husb=WT_Person::getInstance($match[1]);
			}
			if (preg_match('/^1 WIFE @(.+)@/m', $data, $match)) {
				$this->wife=WT_Person::getInstance($match[1]);
			}
		}

		// Make sure husb/wife are the right way round.
		if ($this->husb && $this->husb->getSex()=='F' || $this->wife && $this->wife->getSex()=='M') {
			list($this->husb, $this->wife)=array($this->wife, $this->husb);
		}

		parent::__construct($data);
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		$rec='0 @'.$this->xref.'@ FAM';
		// Just show the 1 CHIL/HUSB/WIFE tag, not any subtags, which may contain private data
		preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $this->_gedrec, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$rela=WT_Person::getInstance($match[1]);
			if ($rela && ($SHOW_PRIVATE_RELATIONSHIPS || $rela->canDisplayDetails($access_level))) {
				$rec.=$match[0];
			}
		}
		return $rec;
	}

	// Fetch the record from the database
	protected static function fetchGedcomRecord($xref, $ged_id) {
		static $statement=null;

		if ($statement===null) {
			$statement=WT_DB::prepare(
				"SELECT 'FAM' AS type, f_id AS xref, f_file AS ged_id, f_gedcom AS gedrec ".
				"FROM `##families` WHERE f_id=? AND f_file=?"
			);
		}
		return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
	}

	/**
	 * get the husband's person object
	 * @return Person
	 */
	function getHusband() {
		return $this->husb;
	}
	/**
	 * get the wife's person object
	 * @return Person
	 */
	function getWife() {
		return $this->wife;
	}

	// Implement family-specific privacy logic
	protected function _canDisplayDetailsByType($access_level) {
		// Hide a family if any member is private
		preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $this->_gedrec, $matches);
		foreach ($matches[1] as $match) {
			$person=WT_Person::getInstance($match);
			if ($person && !$person->canDisplayDetails($access_level)) {
				return false;
			}
		}
		return true;
	}

	// Can the name of this record be shown?
	public function canDisplayName($access_level=WT_USER_ACCESS_LEVEL) {
		// We can always see the name (Husband-name + Wife-name), however,
		// the name will often be "private + private"
		return true;
	}

	/**
	 * return the spouse of the given person
	 * @param Person $person
	 * @return Person
	 */
	function getSpouse($person, $access_level=WT_USER_ACCESS_LEVEL) {
		if (is_null($this->wife) || is_null($this->husb)) {
			return null;
		}
		if ($this->wife->equals($person) && $this->husb->canDisplayDetails($access_level)) {
			return $this->husb;
		}
		if ($this->husb->equals($person) && $this->wife->canDisplayDetails($access_level)) {
			return $this->wife;
		}
		return null;
	}

	function getSpouses($access_level=WT_USER_ACCESS_LEVEL) {
		$spouses=array();
		if ($this->husb && $this->husb->canDisplayDetails($access_level)) {
			$spouses[]=$this->husb;
		}
		if ($this->wife && $this->wife->canDisplayDetails($access_level)) {
			$spouses[]=$this->wife;
		}
		return $spouses;
	}

	/**
	 * get the children
	 * @return array array of children Persons
	 */
	function getChildren($access_level=WT_USER_ACCESS_LEVEL) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		$children=array();
		preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', $this->_gedrec, $match);
		foreach ($match[1] as $pid) {
			$child=WT_Person::getInstance($pid);
			if ($child && ($SHOW_PRIVATE_RELATIONSHIPS || $child->canDisplayDetails($access_level))) {
				$children[]=$child;
			}
		}
		return $children;
	}

	// Static helper function to sort an array of families by marriage date
	static function CompareMarrDate($x, $y) {
		return WT_Date::Compare($x->getMarriageDate(), $y->getMarriageDate());
	}

	/**
	 * get the number of children in this family
	 * @return int the number of children
	 */
	function getNumberOfChildren() {

		$nchi1=(int)get_gedcom_value('NCHI', 1, $this->getGedcomRecord());
		$nchi2=(int)get_gedcom_value('NCHI', 2, $this->getGedcomRecord());
		$nchi3=count($this->getChildren());
		return max($nchi1, $nchi2, $nchi3);
	}

	/**
	 * get updated Family
	 * If there is an updated family record in the gedcom file
	 * return a new family object for it
	 */
	function getUpdatedFamily() {
		if ($this->getChanged()) {
			return $this;
		}
		if (WT_USER_CAN_EDIT && $this->canDisplayDetails()) {
			$newrec = find_updated_record($this->xref, $this->ged_id);
			if (!is_null($newrec)) {
				$newfamily = new WT_Family($newrec);
				$newfamily->setChanged(true);
				return $newfamily;
			}
		}
		return null;
	}
	/**
	 * check if this family has the given person
	 * as a parent in the family
	 * @param Person $person
	 */
	function hasParent($person) {
		if (is_null($person)) return false;
		if ($person->equals($this->husb)) return true;
		if ($person->equals($this->wife)) return true;
		return false;
	}
	/**
	 * check if this family has the given person
	 * as a child in the family
	 * @param Person $person
	 */
	function hasChild($person) {
		if ($person) {
			foreach ($this->getChildren() as $child) {
				if ($person->equals($child)) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * parse marriage record
	 */
	function _parseMarriageRecord() {
		$this->marriage = new WT_Event(get_sub_record(1, '1 MARR', $this->getGedcomRecord()), $this, 0);
	}

	/**
	 * get the marriage event
	 *
	 * @return WT_Event
	 */
	function getMarriage() {
		if (is_null($this->marriage)) $this->_parseMarriageRecord();
		return $this->marriage;
	}

	/**
	 * get marriage record
	 * @return string
	 */
	function getMarriageRecord() {
		if (is_null($this->marriage)) $this->_parseMarriageRecord();
		return $this->marriage->getGedcomRecord();
	}

	// Return whether or not this family ended in a divorce or was never married.
	// Note that this is calculated using unprivatized data, so we can
	// always distinguish spouses from ex-spouses.
	function isDivorced() {
		return (bool)preg_match('/\n1 ('.WT_EVENTS_DIV.')( Y|\n)/', $this->_gedrec);
	}
	function isNotMarried() {
		return (bool)preg_match('/\n1 _NMR( Y|\n)/', $this->_gedrec);
	}

	/**
	 * get marriage date
	 * @return string
	 */
	function getMarriageDate() {
		if (!$this->canDisplayDetails()) {
			return new WT_Date('');
		}
		if (is_null($this->marriage)) {
			$this->_parseMarriageRecord();
		}
		return $this->marriage->getDate();
	}

	// Get the marriage year - displayed on lists of families
	function getMarriageYear() {
		return $this->getMarriageDate()->MinDate()->y;
	}

	/**
	 * get the type for this marriage
	 * @return string
	 */
	function getMarriageType() {
		if (is_null($this->marriage)) $this->_parseMarriageRecord();
		return $this->marriage->getType();
	}

	/**
	 * get the marriage place
	 * @return string
	 */
	function getMarriagePlace() {
		$marriage = $this->getMarriage();
		return $marriage->getPlace();
	}

	// Get all the dates/places for marriages - for the FAM lists
	function getAllMarriageDates() {
		if ($this->canDisplayDetails()) {
			foreach (explode('|', WT_EVENTS_MARR) as $event) {
				if ($array=$this->getAllEventDates($event)) {
					return $array;
				}
			}
		}
		return array();
	}
	function getAllMarriagePlaces() {
		if ($this->canDisplayDetails()) {
			foreach (explode('|', WT_EVENTS_MARR) as $event) {
				if ($array=$this->getAllEventPlaces($event)) {
					return $array;
				}
			}
		}
		return array();
	}

	// Get an array of structures containing all the names in the record
	public function getAllNames() {
		if (is_null($this->_getAllNames)) {
			$husb=$this->husb ? $this->husb : new WT_Person('1 SEX M');
			$wife=$this->wife ? $this->wife : new WT_Person('1 SEX F');
			// Check the script used by each name, so we can match cyrillic with cyrillic, greek with greek, etc.
			$husb_names=$husb->getAllNames();
			foreach ($husb_names as $n=>$husb_name) {
				$husb_names[$n]['script']=utf8_script($husb_name['full']);
			}
			$wife_names=$wife->getAllNames();
			foreach ($wife_names as $n=>$wife_name) {
				$wife_names[$n]['script']=utf8_script($wife_name['full']);
			}
			// Add the matched names first
			foreach ($husb_names as $husb_name) {
				foreach ($wife_names as $wife_name) {
					if ($husb_name['type']!='_MARNM' && $wife_name['type']!='_MARNM' && $husb_name['script']==$wife_name['script']) {
						$this->_getAllNames[]=array(
							'type'=>$husb_name['type'],
							'sort'=>$husb_name['sort'].' + '.$wife_name['sort'],
							'full'=>$husb_name['full'].' + '.$wife_name['full'],
							// No need for a fullNN entry - we do not currently store FAM names in the database
						);
					}
				}
			}
			// Add the unmatched names second (there may be no matched names)
			foreach ($husb_names as $husb_name) {
				foreach ($wife_names as $wife_name) {
					if ($husb_name['type']!='_MARNM' && $wife_name['type']!='_MARNM'  && $husb_name['script']!=$wife_name['script']) {
						$this->_getAllNames[]=array(
							'type'=>$husb_name['type'],
							'sort'=>$husb_name['sort'].' + '.$wife_name['sort'],
							'full'=>$husb_name['full'].' + '.$wife_name['full'],
							// No need for a fullNN entry - we do not currently store FAM names in the database
						);
					}
				}
			}
		}
		return $this->_getAllNames;
	}

	// Extra info to display when displaying this record in a list of
	// selection items or favorites.
	function format_list_details() {
		return
			$this->format_first_major_fact(WT_EVENTS_MARR, 1).
			$this->format_first_major_fact(WT_EVENTS_DIV, 1);
	}
}
