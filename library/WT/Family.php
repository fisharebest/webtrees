<?php
// Class file for a Family
//
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

class WT_Family extends WT_GedcomRecord {
	const RECORD_TYPE = 'FAM';
	const URL_PREFIX  = 'family.php?famid=';

	private $husb = null;
	private $wife = null;

	// Create a Family object from either raw GEDCOM data or a database row
	function __construct($xref, $gedcom, $pending, $gedcom_id) {
		parent::__construct($xref, $gedcom, $pending, $gedcom_id);

		// Fetch husband and wife
		if (preg_match('/^1 HUSB @(.+)@/m', $gedcom.$pending, $match)) {
			$this->husb = WT_Individual::getInstance($match[1]);
		}
		if (preg_match('/^1 WIFE @(.+)@/m', $gedcom.$pending, $match)) {
			$this->wife = WT_Individual::getInstance($match[1]);
		}

		// Make sure husb/wife are the right way round.
		if ($this->husb && $this->husb->getSex()=='F' || $this->wife && $this->wife->getSex()=='M') {
			list($this->husb, $this->wife) = array($this->wife, $this->husb);
		}
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		$rec='0 @'.$this->xref.'@ FAM';
		// Just show the 1 CHIL/HUSB/WIFE tag, not any subtags, which may contain private data
		preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $this->gedcom, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$rela=WT_Individual::getInstance($match[1]);
			if ($rela && ($SHOW_PRIVATE_RELATIONSHIPS || $rela->canShow($access_level))) {
				$rec.=$match[0];
			}
		}
		return $rec;
	}

	// Fetch the record from the database
	protected static function fetchGedcomRecord($xref, $gedcom_id) {
		static $statement=null;

		if ($statement===null) {
			$statement=WT_DB::prepare("SELECT f_gedcom FROM `##families` WHERE f_id=? AND f_file=?");
		}

		return $statement->execute(array($xref, $gedcom_id))->fetchOne();
	}

	// Get the male partner of the family
	function getHusband() {
		if ($this->husb && $this->husb->canShowName()) {
			return $this->husb;
		} else {
			return null;
		}
	}
	// Get the female partner of the family
	function getWife() {
		if ($this->wife && $this->wife->canShowName()) {
			return $this->wife;
		} else {
			return null;
		}
	}

	// Implement family-specific privacy logic
	protected function canShowByType($access_level) {
		// Hide a family if any member is private
		preg_match_all('/\n1 (?:CHIL|HUSB|WIFE) @('.WT_REGEX_XREF.')@/', $this->gedcom, $matches);
		foreach ($matches[1] as $match) {
			$person=WT_Individual::getInstance($match);
			if ($person && !$person->canShow($access_level)) {
				return false;
			}
		}
		return true;
	}

	// Can the name of this record be shown?
	public function canShowName($access_level=WT_USER_ACCESS_LEVEL) {
		// We can always see the name (Husband-name + Wife-name), however,
		// the name will often be "private + private"
		return true;
	}

	/**
	 * Find the spouse of a person.
	 *
	 * @param WT_Individual $person
	 *
	 * @return WT_Individual|null
	 */
	function getSpouse(WT_Individual $person) {
		if ($person === $this->wife) {
			return $this->husb;
		} else {
			return $this->wife;
		}
	}

	/**
	 * Get the (zero, one or two) spouses from this family.
	 *
	 * @param int $access_level
	 *
	 * @return WT_Individual[]
	 */
	function getSpouses($access_level=WT_USER_ACCESS_LEVEL) {
		$spouses=array();
		if ($this->husb && $this->husb->canShowName($access_level)) {
			$spouses[] = $this->husb;
		}
		if ($this->wife && $this->wife->canShowName($access_level)) {
			$spouses[] = $this->wife;
		}
		return $spouses;
	}

	/**
	 * Get a list of this family’s children.
	 *
	 * @param int $access_level
	 *
	 * @return WT_Individual[]
	 */
	function getChildren($access_level=WT_USER_ACCESS_LEVEL) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		$children = array();
		foreach ($this->getFacts('CHIL', false, $access_level, $SHOW_PRIVATE_RELATIONSHIPS) as $fact) {
			$child = $fact->getTarget();
			if ($child && ($SHOW_PRIVATE_RELATIONSHIPS || $child->canShowName($access_level))) {
				$children[] = $child;
			}
		}
		return $children;
	}

	/**
	 * Static helper function to sort an array of families by marriage date
	 *
	 * @param WT_Family $x
	 * @param WT_Family $y
	 *
	 * @return int
	 */
	public static function compareMarrDate(WT_Family $x, WT_Family $y) {
		return WT_Date::Compare($x->getMarriageDate(), $y->getMarriageDate());
	}

	/**
	 * Number of children - for the individual list
	 *
	 * @return int
	 */
	function getNumberOfChildren() {
		$nchi = count($this->getChildren());
		foreach ($this->getFacts('NCHI') as $fact) {
			$nchi = max($nchi, (int)$fact->getValue());
		}
		return $nchi;
	}

	/**
	 * get the marriage event
	 *
	 * @return WT_Fact
	 */
	function getMarriage() {
		return $this->getFirstFact('MARR');
	}

	/**
	 * Get marriage date
	 *
	 * @return WT_Date
	 */
	function getMarriageDate() {
		$marriage = $this->getMarriage();
		if ($marriage) {
			return $marriage->getDate();
		} else {
			return new WT_Date('');
		}
	}

	/**
	 * Get the marriage year - displayed on lists of families
	 *
	 * @return int
	 */
	function getMarriageYear() {
		return $this->getMarriageDate()->MinDate()->y;
	}

	/**
	 * Get the type for this marriage
	 *
	 * @return string|null
	 */
	function getMarriageType() {
		$marriage = $this->getMarriage();
		if ($marriage) {
			return $marriage->getAttribute('TYPE');
		} else {
			return null;
		}
	}

	/**
	 * Get the marriage place
	 *
	 * @return WT_Place
	 */
	function getMarriagePlace() {
		$marriage = $this->getMarriage();
		return $marriage->getPlace();
	}

	// Get all the dates/places for marriages - for the FAM lists
	function getAllMarriageDates() {
		foreach (explode('|', WT_EVENTS_MARR) as $event) {
			if ($array=$this->getAllEventDates($event)) {
				return $array;
			}
		}
		return array();
	}
	function getAllMarriagePlaces() {
		foreach (explode('|', WT_EVENTS_MARR) as $event) {
			if ($array=$this->getAllEventPlaces($event)) {
				return $array;
			}
		}
		return array();
	}

	// Get an array of structures containing all the names in the record
	public function getAllNames() {
		global $UNKNOWN_NN, $UNKNOWN_PN;

		if (is_null($this->_getAllNames)) {
			// Check the script used by each name, so we can match cyrillic with cyrillic, greek with greek, etc.
			if ($this->husb) {
				$husb_names=$this->husb->getAllNames();
			} else {
				$husb_names = array(
					0 => array(
						'type' => 'BIRT',
						'sort' => '@N.N.',
						'full' => $UNKNOWN_PN, ' ', $UNKNOWN_NN,
					),
				);
			}
			foreach ($husb_names as $n=>$husb_name) {
				$husb_names[$n]['script']=WT_I18N::textScript($husb_name['full']);
			}
			if ($this->wife) {
				$wife_names=$this->wife->getAllNames();
			} else {
				$wife_names = array(
					0 => array(
						'type' => 'BIRT',
						'sort' => '@N.N.',
						'full' => $UNKNOWN_PN, ' ', $UNKNOWN_NN,
					),
				);
			}
			foreach ($wife_names as $n=>$wife_name) {
				$wife_names[$n]['script']=WT_I18N::textScript($wife_name['full']);
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
