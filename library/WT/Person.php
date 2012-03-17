<?php
// Class file for a person
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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

class WT_Person extends WT_GedcomRecord {
	var $indifacts = array();
	var $otherfacts = array();
	var $globalfacts = array();
	var $mediafacts = array();
	var $facts_parsed = false;
	var $label = '';
	var $highlightedimage = null;
	var $file = '';
	var $age = null;
	var $sex=null;
	var $generation; // used in some lists to keep track of this Person's generation in that list

	// Cached results from various functions.
	private $_spouseFamilies=null;
	private $_childFamilies=null;
	private $_getBirthDate=null;
	private $_getBirthPlace=null;
	private $_getAllBirthDates=null;
	private $_getAllBirthPlaces=null;
	private $_getEstimatedBirthDate=null;
	private $_getDeathDate=null;
	private $_getDeathPlace=null;
	private $_getAllDeathDates=null;
	private $_getAllDeathPlaces=null;
	private $_getEstimatedDeathDate=null;

	// Can the name of this record be shown?
	public function canDisplayName($access_level=WT_USER_ACCESS_LEVEL) {
		global $SHOW_LIVING_NAMES;

		return $SHOW_LIVING_NAMES>=$access_level || $this->canDisplayDetails($access_level);
	}

	// Implement person-specific privacy logic
	protected function _canDisplayDetailsByType($access_level) {
		global $SHOW_DEAD_PEOPLE, $KEEP_ALIVE_YEARS_BIRTH, $KEEP_ALIVE_YEARS_DEATH;

		// Dead people...
		if ($SHOW_DEAD_PEOPLE>=$access_level && $this->isDead()) {
			$keep_alive=false;
			if ($KEEP_ALIVE_YEARS_BIRTH) {
				preg_match_all('/\n1 (?:'.WT_EVENTS_BIRT.').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $this->_gedrec, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$date=new WT_Date($match[1]);
					if ($date->isOK() && $date->gregorianYear()+$KEEP_ALIVE_YEARS_BIRTH > date('Y')) {
						$keep_alive=true;
						break;
					}
				}
			}
			if ($KEEP_ALIVE_YEARS_DEATH) {
				preg_match_all('/\n1 (?:'.WT_EVENTS_DEAT.').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $this->_gedrec, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$date=new WT_Date($match[1]);
					if ($date->isOK() && $date->gregorianYear()+$KEEP_ALIVE_YEARS_DEATH > date('Y')) {
						$keep_alive=true;
						break;
					}
				}
			}
			if (!$keep_alive) {
				return true;
			}
		}
		// Consider relationship privacy (unless an admin is applying download restrictions)
		if (WT_USER_GEDCOM_ID && WT_USER_PATH_LENGTH && $this->getGedId()==WT_GED_ID && $access_level=WT_USER_ACCESS_LEVEL) {
			return get_relationship(WT_USER_GEDCOM_ID, $this->getXref(), true, WT_USER_PATH_LENGTH)!==false;
		}
		// No restriction found - show living people to members only:
		return WT_PRIV_USER>=$access_level;
	}

	// Generate a private version of this record
	protected function createPrivateGedcomRecord($access_level) {
		global $SHOW_PRIVATE_RELATIONSHIPS, $SHOW_LIVING_NAMES;

		$rec='0 @'.$this->xref.'@ INDI';
		if ($SHOW_LIVING_NAMES>=$access_level) {
			// Show all the NAME tags, including subtags
			preg_match_all('/\n1 NAME.*(?:\n[2-9].*)*/', $this->_gedrec, $matches);
			foreach ($matches[0] as $match) {
				if (canDisplayFact($this->xref, $this->ged_id, $match, $access_level)) {
					$rec.=$match;
				}
			}
		} else {
			$rec.="\n1 NAME ".WT_I18N::translate('Private');
		}
		// Just show the 1 FAMC/FAMS tag, not any subtags, which may contain private data
		preg_match_all('/\n1 (?:FAMC|FAMS) @('.WT_REGEX_XREF.')@/', $this->_gedrec, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$rela=WT_Family::getInstance($match[1]);
			if ($rela && ($SHOW_PRIVATE_RELATIONSHIPS || $rela->canDisplayDetails($access_level))) {
				$rec.=$match[0];
			}
		}
		// Don't privatize sex.
		if (preg_match('/\n1 SEX [MFU]/', $this->_gedrec, $match)) {
			$rec.=$match[0];
		}
		return $rec;
	}

	// Fetch the record from the database
	protected static function fetchGedcomRecord($xref, $ged_id) {
		static $statement=null;

		if ($statement===null) {
			$statement=WT_DB::prepare(
				"SELECT 'INDI' AS type, i_id AS xref, i_file AS ged_id, i_gedcom AS gedrec ".
				"FROM `##individuals` WHERE i_id=? AND i_file=?"
			);
		}
		return $statement->execute(array($xref, $ged_id))->fetchOneRow(PDO::FETCH_ASSOC);
	}

	// Static helper function to sort an array of people by birth date
	static function CompareBirtDate($x, $y) {
		return WT_Date::Compare($x->getEstimatedBirthDate(), $y->getEstimatedBirthDate());
	}

	// Static helper function to sort an array of people by death date
	static function CompareDeatDate($x, $y) {
		return WT_Date::Compare($x->getEstimatedDeathDate(), $y->getEstimatedDeathDate());
	}

	// Calculate whether this person is living or dead.
	// If not known to be dead, then assume living.
	// NOTE: this function checks both parents and children.  Therefore we cannot
	// use any function - e.g. getChildFamilies() - that calls canDisplayDetails(),
	// as this will cause an infinite loop.  Also, we need to bypass privacy checks,
	// as we are allowed to check the dates of the children.
	// Therefore we must access the raw gedcom data directly.
	public function isDead() {
		global $MAX_ALIVE_AGE;

		// "1 DEAT Y" or "1 DEAT/2 DATE" or "1 DEAT/2 PLAC"
		if (preg_match('/\n1 (?:'.WT_EVENTS_DEAT.')(?: Y|(?:\n[2-9].+)*\n2 (DATE|PLAC) )/', $this->_gedrec)) {
			return true;
		}

		// If any event occured more than $MAX_ALIVE_AGE years ago, then assume the person is dead
		preg_match_all('/\n2 DATE (.+)/', $this->_gedrec, $date_matches);
		foreach ($date_matches[1] as $date_match) {
			$date=new WT_Date($date_match);
			if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*$MAX_ALIVE_AGE) {
				return true;
			}
		}

		// If we found no dates then check the dates of close relatives.

		// Check parents (birth and adopted)
		foreach ($this->getChildFamilies(WT_PRIV_HIDE) as $family) {
			foreach ($family->getSpouses(WT_PRIV_HIDE) as $parent) {
				// Assume parents are no more than 45 years older than their children
				preg_match_all('/\n2 DATE (.+)/', $parent->_gedrec, $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date=new WT_Date($date_match);
					if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE+45)) {
						return true;
					}
				}
			}
		}

		// Check spouses
		foreach ($this->getSpouseFamilies(WT_PRIV_HIDE) as $family) {
			preg_match_all('/\n2 DATE (.+)/', $family->_gedrec, $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date=new WT_Date($date_match);
				// Assume marriage occurs after age of 10
				if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE-10)) {
					return true;
				}
			}
			// Check spouse dates
			$spouse=$family->getSpouse($this, WT_PRIV_HIDE);
			if ($spouse) {
				preg_match_all('/\n2 DATE (.+)/', $spouse->_gedrec, $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date=new WT_Date($date_match);
					// Assume max age difference between spouses of 40 years
					if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE+40)) {
						return true;
					}
				}
			}
			// Check child dates
			foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
				preg_match_all('/\n2 DATE (.+)/', $child->_gedrec, $date_matches);
				// Assume children born after age of 15
				foreach ($date_matches[1] as $date_match) {
					$date=new WT_Date($date_match);
					if ($date->isOK() && $date->MaxJD() <= WT_SERVER_JD - 365*($MAX_ALIVE_AGE-15)) {
						return true;
					}
				}
				// Check grandchildren
				foreach ($child->getSpouseFamilies(WT_PRIV_HIDE) as $child_family) {
					foreach ($child_family->getChildren(WT_PRIV_HIDE) as $grandchild) {
						preg_match_all('/\n2 DATE (.+)/', $grandchild->_gedrec, $date_matches);
						// Assume grandchildren born after age of 30
						foreach ($date_matches[1] as $date_match) {
							$date=new WT_Date($date_match);
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
	* get highlighted media
	* @return array
	*/
	function findHighlightedMedia() {
		if (is_null($this->highlightedimage)) {
			$this->highlightedimage = find_highlighted_object($this->xref, $this->ged_id, $this->getGedcomRecord());
		}
		return $this->highlightedimage;
	}

	/**
	* get birth date
	* @return WT_Date the birth date
	*/
	function getBirthDate() {
		if (is_null($this->_getBirthDate)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllBirthDates() as $date) {
					if ($date->isOK()) {
						$this->_getBirthDate=$date;
						break;
					}
				}
				if (is_null($this->_getBirthDate)) {
					$this->_getBirthDate=new WT_Date('');
				}
			} else {
				$this->_getBirthDate=new WT_Date("(".WT_I18N::translate('Private').")");
			}
		}
		return $this->_getBirthDate;
	}

	/**
	* get the birth place
	* @return string
	*/
	function getBirthPlace() {
		if (is_null($this->_getBirthPlace)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllBirthPlaces() as $place) {
					if ($place) {
						$this->_getBirthPlace=$place;
						break;
					}
				}
				if (is_null($this->_getBirthPlace)) {
					$this->_getBirthPlace='';
				}
			} else {
				$this->_getBirthPlace=WT_I18N::translate('Private');
			}
		}
		return $this->_getBirthPlace;
	}

	/**
	* get the Census birth place (Town and County (reversed))
	* @return string
	*/
	function getCensBirthPlace() {
		if (is_null($this->_getBirthPlace)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllBirthPlaces() as $place) {
					if ($place) {
						$this->_getBirthPlace=$place;
						break;
					}
				}
				if (is_null($this->_getBirthPlace)) {
					$this->_getBirthPlace='';
				}
			} else {
				$this->_getBirthPlace=WT_I18N::translate('Private');
			}
		}
		$censbirthplace = $this->_getBirthPlace;
		$censbirthplace = explode(', ', $censbirthplace);
		$censbirthplace = array_reverse($censbirthplace);
		$censbirthplace = array_slice($censbirthplace, 1);
		$censbirthplace = array_slice($censbirthplace, 0, 2);
		$censbirthplace = implode(', ', $censbirthplace);
		return $censbirthplace;
	}

	/**
	* get the birth year
	* @return string the year of birth
	*/
	function getBirthYear() {
		return $this->getBirthDate()->MinDate()->Format('%Y');
	}

	/**
	* get death date
	* @return WT_Date the death date in the GEDCOM format of '1 JAN 2006'
	*/
	function getDeathDate($estimate = true) {
		if (is_null($this->_getDeathDate)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllDeathDates() as $date) {
					if ($date->isOK()) {
						$this->_getDeathDate=$date;
						break;
					}
				}
				if (is_null($this->_getDeathDate)) {
					$this->_getDeathDate=new WT_Date('');
				}
			} else {
				$this->_getDeathDate=new WT_Date("(".WT_I18N::translate('Private').")");
			}
		}
		return $this->_getDeathDate;
	}

	/**
	* get the death place
	* @return string
	*/
	function getDeathPlace() {
		if (is_null($this->_getDeathPlace)) {
			if ($this->canDisplayDetails()) {
				foreach ($this->getAllDeathPlaces() as $place) {
					if ($place) {
						$this->_getDeathPlace=$place;
						break;
					}
				}
				if (is_null($this->_getDeathPlace)) {
					$this->_getDeathPlace='';
				}
			} else {
				$this->_getDeathPlace=WT_I18N::translate('Private');
			}
		}
		return $this->_getDeathPlace;
	}

	/**
	* get the death year
	* @return string the year of death
	*/
	function getDeathYear() {
		return $this->getDeathDate()->MinDate()->Format('%Y');
	}

	/**
	* get the birth and death years
	* @return string
	*/
	function getBirthDeathYears($age_at_death=true, $classname='details1') {
		if (!$this->getBirthYear()) {
			return '';
		}
		$tmp = '<span dir="ltr" title="'.strip_tags($this->getBirthDate()->Display()).'">'.$this->getBirthYear();
			if (strip_tags($this->getDeathYear()) =='') { $tmp .= '</span>'; } else { $tmp .= '-</span>'; } 		
		$tmp .= '<span title="'.strip_tags($this->getDeathDate()->Display()).'">'.$this->getDeathYear().'</span>';
		// display age only for exact dates (empty date qualifier)
		if ($age_at_death
			&& $this->getBirthYear() && empty($this->getBirthDate()->qual1)
			&& $this->getDeathYear() && empty($this->getDeathDate()->qual1)) {
			$age = get_age_at_event(WT_Date::GetAgeGedcom($this->getBirthDate(), $this->getDeathDate()), false);
			if (!empty($age)) {
				$tmp .= '<span class="age"> ('.WT_I18N::translate('Age').' '.$age.')</span>';
			}
		}
		if ($classname) {
			return '<span class="'.$classname.'">'.$tmp.'</span>';
		}
		return $tmp;
	}

	// Get the range of years in which a person lived.  e.g. "1870–", "1870–1920", "–1920".
	// Provide the full date using a tooltip.
	// For consistent layout in charts, etc., show just a "–" when no dates are known.
	// Note that this is a (non-breaking) en-dash, and not a hyphen.
	public function getLifeSpan() {
		return
			/* I18N: A range of years, e.g. "1870–", "1870–1920", "–1920" */ WT_I18N::translate(
				'%1$s–%2$s',
				'<span title="'.htmlspecialchars(strip_tags($this->getBirthDate()->Display())).'">'.$this->getBirthDate()->MinDate()->Format('%Y').'</span>',
				'<span title="'.htmlspecialchars(strip_tags($this->getDeathDate()->Display())).'">'.$this->getDeathDate()->MinDate()->Format('%Y').'</span>'
			);
	}

	// Get all the dates/places for births/deaths - for the INDI lists
	function getAllBirthDates() {
		if (is_null($this->_getAllBirthDates)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', WT_EVENTS_BIRT) as $event) {
					if ($this->_getAllBirthDates=$this->getAllEventDates($event)) {
						break;
					}
				}
			} else {
				$this->_getAllBirthDates=array();
			}
		}
		return $this->_getAllBirthDates;
	}
	function getAllBirthPlaces() {
		if (is_null($this->_getAllBirthPlaces)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', WT_EVENTS_BIRT) as $event) {
					if ($this->_getAllBirthPlaces=$this->getAllEventPlaces($event)) {
						break;
					}
				}
			} else {
				$this->_getAllBirthPlaces=array();
			}
		}
		return $this->_getAllBirthPlaces;
	}
	function getAllDeathDates() {
		if (is_null($this->_getAllDeathDates)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', WT_EVENTS_DEAT) as $event) {
					if ($this->_getAllDeathDates=$this->getAllEventDates($event)) {
						break;
					}
				}
			} else {
				$this->_getAllDeathDates=array();
			}
		}
		return $this->_getAllDeathDates;
	}
	function getAllDeathPlaces() {
		if (is_null($this->_getAllDeathPlaces)) {
			if ($this->canDisplayDetails()) {
				foreach (explode('|', WT_EVENTS_DEAT) as $event) {
					if ($this->_getAllDeathPlaces=$this->getAllEventPlaces($event)) {
						break;
					}
				}
			} else {
				$this->_getAllDeathPlaces=array();
			}
		}
		return $this->_getAllDeathPlaces;
	}

	// Generate an estimate for birth/death dates, based on dates of parents/children/spouses
	function getEstimatedBirthDate() {
		if (is_null($this->_getEstimatedBirthDate)) {
			foreach ($this->getAllBirthDates() as $date) {
				if ($date->isOK()) {
					$this->_getEstimatedBirthDate=$date;
					break;
				}
			}
			if (is_null($this->_getEstimatedBirthDate)) {
				$min=array();
				$max=array();
				$tmp=$this->getDeathDate();
				if ($tmp->MinJD()) {
					global $MAX_ALIVE_AGE;
					$min[]=$tmp->MinJD()-$MAX_ALIVE_AGE*365;
					$max[]=$tmp->MaxJD();
				}
				foreach ($this->getChildFamilies() as $family) {
					$tmp=$family->getMarriageDate();
					if (is_object($tmp) && $tmp->MinJD()) {
						$min[]=$tmp->MaxJD()-365*1;
						$max[]=$tmp->MinJD()+365*30;
					}
					if ($parent=$family->getHusband()) {
						$tmp=$parent->getBirthDate();
						if (is_object($tmp) && $tmp->MinJD()) {
							$min[]=$tmp->MaxJD()+365*15;
							$max[]=$tmp->MinJD()+365*65;
						}
					}
					if ($parent=$family->getWife()) {
						$tmp=$parent->getBirthDate();
						if (is_object($tmp) && $tmp->MinJD()) {
							$min[]=$tmp->MaxJD()+365*15;
							$max[]=$tmp->MinJD()+365*45;
						}
					}
					foreach ($family->getChildren() as $child) {
						$tmp=$child->getBirthDate();
						if ($tmp->MinJD()) {
							$min[]=$tmp->MaxJD()-365*30;
							$max[]=$tmp->MinJD()+365*30;
						}
					}
				}
				foreach ($this->getSpouseFamilies() as $family) {
					$tmp=$family->getMarriageDate();
					if (is_object($tmp) && $tmp->MinJD()) {
						$min[]=$tmp->MaxJD()-365*45;
						$max[]=$tmp->MinJD()-365*15;
					}
					if ($spouse=$family->getSpouse($this)) {
						$tmp=$spouse->getBirthDate();
						if (is_object($tmp) && $tmp->MinJD()) {
							$min[]=$tmp->MaxJD()-365*25;
							$max[]=$tmp->MinJD()+365*25;
						}
					}
					foreach ($family->getChildren() as $child) {
						$tmp=$child->getBirthDate();
						if ($tmp->MinJD()) {
							$min[]=$tmp->MaxJD()-365*($this->getSex()=='F'?45:65);
							$max[]=$tmp->MinJD()-365*15;
						}
					}
				}
				if ($min && $max) {
					list($y)=WT_Date_Gregorian::JDtoYMD(floor((max($min)+min($max))/2));
					$this->_getEstimatedBirthDate=new WT_Date("EST {$y}");
				} else {
					$this->_getEstimatedBirthDate=new WT_Date(''); // always return a date object
				}
			}
		}
		return $this->_getEstimatedBirthDate;
	}
	function getEstimatedDeathDate() {
		if (is_null($this->_getEstimatedDeathDate)) {
			foreach ($this->getAllDeathDates() as $date) {
				if ($date->isOK()) {
					$this->_getEstimatedDeathDate=$date;
					break;
				}
			}
			if (is_null($this->_getEstimatedDeathDate)) {
				$tmp=$this->getEstimatedBirthDate();
				if ($tmp->MinJD()) {
					global $MAX_ALIVE_AGE;
					$tmp2=$tmp->AddYears($MAX_ALIVE_AGE, 'bef');
					if ($tmp2->MaxJD()<WT_SERVER_JD) {
						$this->_getEstimatedDeathDate=$tmp2;
					} else {
						$this->_getEstimatedDeathDate=new WT_Date(''); // always return a date object
					}
				} else {
					$this->_getEstimatedDeathDate=new WT_Date(''); // always return a date object
				}
			}
		}
		return $this->_getEstimatedDeathDate;
	}

	/**
	* get the sex
	* @return string  return M, F, or U
	*/
	// Use the un-privatised gedcom record.  We call this function during
	// the privatize-gedcom function, and we are allowed to know this.
	function getSex() {
		if (is_null($this->sex)) {
			if (preg_match('/\n1 SEX ([MF])/', $this->_gedrec, $match)) {
				$this->sex=$match[1];
			} else {
				$this->sex='U';
			}
		}
		return $this->sex;
	}

	/**
	* get the person's sex image
	* @return string  <img ...>
	*/
	function getSexImage($size='small', $style='', $title='') {
		return self::sexImage($this->getSex(), $size, $style, $title);
	}

	static function sexImage($sex, $size='small', $style='', $title='') {
		global $WT_IMAGES;

		if ($size=='small') {
			$image='sex_'.strtolower($sex).'_9x9';
		} else {
			$image='sex_'.strtolower($sex).'_15x15';
		}

		if ($title) {
			$title=' title="'.$title.'"';
		}

		switch ($sex) {
		case 'M':
			if (isset($WT_IMAGES[$image])) {
				return '<img src="'.$WT_IMAGES[$image].'" class="gender_image" style="'.$style.'" alt="'.WT_I18N::translate('Male').'"'.$title.'>';
			} else {
				return '<span style="size:'.$size.'">'.WT_UTF8_MALE.'</span>';
			}
		case 'F':
			if (isset($WT_IMAGES[$image])) {
				return '<img src="'.$WT_IMAGES[$image].'" class="gender_image" style="'.$style.'" alt="'.WT_I18N::translate('Female').'"'.$title.'>';
			} else {
				return '<span style="size:'.$size.'">'.WT_UTF8_FEMALE.'</span>';
			}
		default:
			if (isset($WT_IMAGES[$image])) {
				return '<img src="'.$WT_IMAGES[$image].'" class="gender_image" style="'.$style.'" alt="'.WT_I18N::translate_c('unknown gender', 'Unknown').'"'.$title.'>';
			} else {
				return '<span style="size:'.$size.'">?</span>';
			}
		}
	}

	function getBoxStyle() {
		$tmp=array('M'=>'','F'=>'F', 'U'=>'NN');
		return 'person_box'.$tmp[$this->getSex()];
	}

	/**
	* set a label for this person
	* The label can be used when building a list of people
	* to display the relationship between this person
	* and the person listed on the page
	* @param string $label
	*/
	function setLabel($label) {
		$this->label = $label;
	}
	/**
	* get the label for this person
	* The label can be used when building a list of people
	* to display the relationship between this person
	* and the person listed on the page
	* @param string $elderdate optional elder sibling birthdate to calculate gap
	* @param int $counter optional children counter
	* @return string
	*/
	function getLabel($elderdate='', $counter=0) {
		global $WT_IMAGES;

		$label = '';
		$gap = 0;
		if (is_object($elderdate) && $elderdate->isOK()) {
			$p2 = $this->getBirthDate();
			if ($p2->isOK()) {
				$gap = $p2->MinJD()-$elderdate->MinJD(); // days
				$label .= "<div class=\"elderdate age\">";
				// warning if negative gap : wrong order
				if ($gap<0 && $counter>0) $label .= '<img alt="" src="'.$WT_IMAGES['warning'].'"> ';
				// warning if gap<6 months
				if ($gap>1 && $gap<180 && $counter>0) $label .= '<img alt="" src="'.$WT_IMAGES['warning'].'"> ';
				// children with same date means twin
				/**if ($gap==0 && $counter>1) {
					if ($this->getSex()=='M') $label .= WT_I18N::translate('Twin brother');
					else if ($this->getSex()=='F') $label .= WT_I18N::translate('Twin sister');
					else $label .= WT_I18N::translate('Twin');
					}**/
				// gap in years or months
				$gap = round($gap*12/365.25); // months
				if (($gap==12)||($gap==-12)) {
					$label .= WT_I18N::plural('%d year', '%d years', round($gap/12), round($gap/12));
				} elseif ($gap>23 or $gap<-23) {
					$label .= WT_I18N::plural('%d year', '%d years', round($gap/12), round($gap/12));
				} elseif ($gap!=0) {
					$label .= WT_I18N::plural('%d month', '%d months', $gap, $gap);
				}
				$label .= '</div>';
			}
		}
		// I18N: This is an abbreviation for a number.  i.e. #7 means number 7
		if ($counter) $label .= '<div>'.WT_I18N::translate('#%d', $counter).'</div>';
		$label .= $this->label;
		if ($gap!=0 && $counter<1) $label .= '<br>&nbsp;';
		return $label;
	}

	// Get a list of this person's spouse families
	function getSpouseFamilies($access_level=WT_USER_ACCESS_LEVEL) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		if ($access_level==WT_PRIV_HIDE) {
			// special case, (temporary - cannot make this generic as other code depends on the private cached values)
			$families=array();
			preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $this->_gedrec, $match);
			foreach ($match[1] as $pid) {
				$family=WT_Family::getInstance($pid);
				if ($family) {
					$families[]=$family;
				}
			}
			return $families;
		}

		if ($this->_spouseFamilies===null) {
			$this->_spouseFamilies=array();
			preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $this->_gedrec, $match);
			foreach ($match[1] as $pid) {
				$family=WT_Family::getInstance($pid);
				if ($family && ($SHOW_PRIVATE_RELATIONSHIPS || $family->canDisplayDetails($access_level))) {
					$this->_spouseFamilies[]=$family;
				}
			}
		}
		return $this->_spouseFamilies;
	}

	/**
	* get the current spouse of this person
	* The current spouse is defined as the spouse from the latest family.
	* The latest family is defined as the last family in the GEDCOM record
	* @return Person  this person's spouse
	*/
	function getCurrentSpouse() {
		$tmp=$this->getSpouseFamilies();
		$family = end($tmp);
		if ($family) {
			return $family->getSpouse($this);
		} else {
			return null;
		}
	}

	// Get a count of the children for this individual
	function getNumberOfChildren() {
		if (preg_match('/\n1 NCHI (\d+)(?:\n|$)/', $this->getGedcomRecord(), $match)) {
			return $match[1];
		} else {
			$children=array();
			foreach ($this->getSpouseFamilies() as $fam) {
				foreach ($fam->getChildren() as $child) {
					$children[$child->getXref()]=true;
				}
			}
			return count($children);
		}
	}

	// Get a list of this person's child families (i.e. their parents)
	function getChildFamilies($access_level=WT_USER_ACCESS_LEVEL) {
		global $SHOW_PRIVATE_RELATIONSHIPS;

		if ($access_level==WT_PRIV_HIDE) {
			// special case, (temporary - cannot make this generic as other code depends on the private cached values)
			$families=array();
			preg_match_all('/\n1 FAMC @('.WT_REGEX_XREF.')@/', $this->_gedrec, $match);
			foreach ($match[1] as $pid) {
				$family=WT_Family::getInstance($pid);
				if ($family) {
					$families[]=$family;
				}
			}
			return $families;
		}

		if ($this->_childFamilies===null) {
			$this->_childFamilies=array();
			preg_match_all('/\n1 FAMC @('.WT_REGEX_XREF.')@/', $this->_gedrec, $match);
			foreach ($match[1] as $pid) {
				$family=WT_Family::getInstance($pid);
				if ($family && ($SHOW_PRIVATE_RELATIONSHIPS || $family->canDisplayDetails($access_level))) {
					$this->_childFamilies[]=$family;
				}
			}
		}
		return $this->_childFamilies;
	}

	/**
	* get primary family with parents
	* @return Family object
	*/
	function getPrimaryChildFamily() {
		$families=$this->getChildFamilies();
		switch (count($families)) {
		case 0:
			return null;
		case 1:
			return reset($families);
		default:
			// If there is more than one FAMC record, choose the preferred parents:
			// a) records with '2 _PRIMARY'
			foreach ($families as $famid=>$fam) {
				if (preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 _PRIMARY Y)/", $this->getGedcomRecord())) {
					return $fam;
				}
			}
			// b) records with '2 PEDI birt'
			foreach ($families as $famid=>$fam) {
				if (preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 PEDI birth)/", $this->getGedcomRecord())) {
					return $fam;
				}
			}
			// c) records with no '2 PEDI'
			foreach ($families as $famid=>$fam) {
				if (!preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 PEDI)/", $this->getGedcomRecord())) {
					return $fam;
				}
			}
			// d) any record
			return reset($families);
		}
	}
	/**
	* get family with child pedigree
	* @return string FAMC:PEDI value [ adopted | birth | foster | sealing ]
	*/
	function getChildFamilyPedigree($famid) {
		$subrec = get_sub_record(1, '1 FAMC @'.$famid.'@', $this->getGedcomRecord());
		$pedi = get_gedcom_value('PEDI', 2, $subrec, '', false);
		// birth=default => return an empty string
		return ($pedi=='birth') ? '' : $pedi;
	}

	// Get a list of step-parent families
	function getChildStepFamilies() {
		$step_families=array();
		$families=$this->getChildFamilies();
		foreach ($families as $family) {
			$father=$family->getHusband();
			if ($father) {
				foreach ($father->getSpouseFamilies() as $step_family) {
					if (!in_array($step_family, $families, true)) {
						$step_families[]=$step_family;
					}
				}
			}
			$mother=$family->getWife();
			if ($mother) {
				foreach ($mother->getSpouseFamilies() as $step_family) {
					if (!in_array($step_family, $families, true)) {
						$step_families[]=$step_family;
					}
				}
			}
		}
		return $step_families;
	}

	// Get a list of step-child families
	function getSpouseStepFamilies() {
		$step_families=array();
		$families=$this->getSpouseFamilies();
		foreach ($families as $family) {
			$spouse=$family->getSpouse($this);
			if ($spouse) {
				foreach ($spouse->getSpouseFamilies() as $step_family) {
					if (!in_array($step_family, $families, true)) {
						$step_families[]=$step_family;
					}
				}
			}
		}
		return $step_families;
	}

	/**
	* get global facts
	* @return array
	*/
	function getGlobalFacts() {
		$this->parseFacts();
		return $this->globalfacts;
	}
	/**
	* get indi facts
	* @return array
	*/
	function getIndiFacts($nfacts=NULL) {
		$this->parseFacts($nfacts);
		return $this->indifacts;
	}

	/**
	* get other facts
	* @return array
	*/
	function getOtherFacts() {
		$this->parseFacts();
		return $this->otherfacts;
	}
	
	// A label for a parental family group
	function getChildFamilyLabel(WT_Family $family) {
		if (preg_match('/\n1 FAMC @'.$family->getXref().'@(?:\n[2-9].*)*\n2 PEDI (.+)/', $this->getGedcomRecord(), $match)) {
			// A specified pedigree
			return WT_Gedcom_Code_Pedi::getChildFamilyLabel($match[1]);
		} else {
			// Default (birth) pedigree
			return WT_Gedcom_Code_Pedi::getChildFamilyLabel('');
		}
	}

	/**
	* get the correct label for a step family
	* @param Family $family the family to get the label for
	* @return string
	*/
	function getStepFamilyLabel($family) {
		$label = 'Unknown Family';
		if (is_null($family)) return $label;
		$childfams = $this->getChildFamilies();
		$mother = $family->getWife();
		$father = $family->getHusband();
		foreach ($childfams as $fam) {
			if (!$fam->equals($family)) {
				$wife = $fam->getWife();
				$husb = $fam->getHusband();
				if ((is_null($husb) || !$husb->equals($father)) && (is_null($wife)||$wife->equals($mother))) {
					if ($mother->getSex()=='M') $label = WT_I18N::translate('Father\'s Family with ');
					else $label = WT_I18N::translate('Mother\'s Family with ');
					if (!is_null($father)) $label .= $father->getFullName();
					else $label .= WT_I18N::translate('unknown person');
				}
				else if ((is_null($wife) || !$wife->equals($mother)) && (is_null($husb)||$husb->equals($father))) {
					if ($father->getSex()=='F') $label = WT_I18N::translate('Mother\'s Family with ');
					else $label = WT_I18N::translate('Father\'s Family with ');
					if (!is_null($mother)) $label .= $mother->getFullName();
					else $label .= WT_I18N::translate('unknown person');
				}
				if ($label!='Unknown Family') return $label;
			}
		}
		return $label;
	}
	/**
	* get the correct label for a family
	* @param Family $family the family to get the label for
	* @return string
	*/
	function getSpouseFamilyLabel($family) {
		if (is_null($family)) {
			$spouse=WT_I18N::translate('unknown person');
		} else {
			$husb = $family->getHusband();
			$wife = $family->getWife();
			if ($this->equals($husb) && !is_null($wife)) {
				$spouse = $wife->getFullName();
			} elseif ($this->equals($wife) && !is_null($husb)) {
				$spouse = $husb->getFullName();
			} else {
				$spouse = WT_I18N::translate('unknown person');
			}
		}
		// I18N: %s is the spouse name
		return WT_I18N::translate('Family with %s', $spouse);
	}
	/**
	* get updated Person
	* If there is an updated individual record in the gedcom file
	* return a new person object for it
	* @return Person
	*/
	function getUpdatedPerson() {
		if ($this->getChanged()) {
			return null;
		}
		if (WT_USER_CAN_EDIT && $this->canDisplayDetails()) {
			$newrec = find_updated_record($this->xref, $this->ged_id);
			if (!is_null($newrec)) {
				$new = new WT_Person($newrec);
				$new->setChanged(true);
				return $new;
			}
		}
		return null;
	}
	/**
	* Parse the facts from the individual record
	*/
	function parseFacts($nfacts=NULL) {
		global $nonfacts;
		parent::parseFacts();
		if ($nfacts!=NULL) $nonfacts = $nfacts;
		//-- only run this function once
		if ($this->facts_parsed) return;
		//-- don't run this function if privacy does not allow viewing of details
		if (!$this->canDisplayDetails()) return;
		$sexfound = false;
		//-- run the parseFacts() method from the parent class
		$this->facts_parsed = true;

		//-- sort the fact info into different categories for people
		foreach ($this->facts as $f=>$event) {
			$fact = $event->getTag();
			// -- handle special name fact case
			if ($fact=='NAME') {
				$this->globalfacts[] = $event;
			}
			// -- handle special source fact case
			else if ($fact=='SOUR') {
				$this->otherfacts[] = $event;
			}
			// -- handle special note fact case
			else if ($fact=='NOTE') {
				$this->otherfacts[] = $event;
			}
			// -- handle special sex case
			else if ($fact=='SEX') {
				$this->globalfacts[] = $event;
				$sexfound = true;
			}
			else if ($fact=='OBJE') {}
			else if (!isset($nonfacts) || !in_array($fact, $nonfacts)) {
				$this->indifacts[] = $event;
			}
		}
		//-- add a new sex fact if one was not found
		if (!$sexfound) {
			$this->globalfacts[] = new WT_Event('1 SEX U', $this, 'new');
		}
	}
	/**
	* add facts from the family record
	* @param boolean $otherfacts whether or not to add other related facts such as parents facts, associated people facts, and historical facts
	*/
	function add_family_facts($otherfacts = true) {
		global $GEDCOM, $nonfacts, $nonfamfacts;

		if (!isset($nonfacts)) $nonfacts = array();
		if (!isset($nonfamfacts)) $nonfamfacts = array();

		if (!$this->canDisplayDetails()) return;
		$this->parseFacts();
		//-- Get the facts from the family with spouse (FAMS)
		foreach ($this->getSpouseFamilies() as $family) {
			$updfamily = $family->getUpdatedFamily(); //-- updated family ?
			$spouse = $family->getSpouse($this);

			if ($updfamily) {
				$family->diffMerge($updfamily);
			}
			$facts = $family->getFacts();
			$hasdiv = false;
			/* @var $event WT_Event */
			foreach ($facts as $event) {
				$fact = $event->getTag();
				if ($fact=='DIV') $hasdiv = true;
				// -- handle special source fact case
				if (($fact!='SOUR') && ($fact!='NOTE') && ($fact!='CHAN') && ($fact!='_UID') && ($fact!='RIN')) {
					if ((!in_array($fact, $nonfacts))&&(!in_array($fact, $nonfamfacts))) {
						$factrec = $event->getGedcomRecord();
						if (!is_null($spouse)) $factrec.="\n2 _WTS @".$spouse->getXref().'@';
						$factrec.="\n2 _WTFS @".$family->getXref()."@\n";
						$event->gedcomRecord = $factrec;
						if ($fact!='OBJE') {
							$this->indifacts[]=$event;
						} else {
							$this->otherfacts[]=$event;
						}
					}
				}
			}
			if ($otherfacts) {
				if (!$hasdiv && !is_null($spouse)) $this->add_spouse_facts($spouse, $family->getGedcomRecord());
				$this->add_children_facts($family, '_CHIL', '');
			}
		}
		if ($otherfacts) {
			$this->add_parents_facts($this, 1);
			$this->add_historical_facts();
			$this->add_asso_facts();
		}
	}

	// Add parents' (and parents' relatives') events to individual facts array
	function add_parents_facts($person, $sosa) {
		global $SHOW_RELATIVES_EVENTS;

		switch ($sosa) {
		case 1:
			foreach ($person->getChildFamilies() as $family) {
				// Add siblings
				$this->add_children_facts($family, '_SIBL', '');
				foreach ($family->getSpouses() as $spouse) {
					foreach ($spouse->getSpouseFamilies() as $sfamily) {
						if (!$family->equals($sfamily)) {
							// Add half-siblings
							$this->add_children_facts($sfamily, '_HSIB', 'par');
						}
					}
					// Add grandparents
					$this->add_parents_facts($spouse, $spouse->getSex()=='F' ? 3 : 2);
				}
			}

			$rela='';
			break;
		case 2:
			$rela='fat';
			break;
		case 3:
			$rela='mot';
			break;
		}

		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();

		foreach ($person->getChildFamilies() as $famid=>$family) {
			foreach ($family->getSpouses() as $parent) {
				if (strstr($SHOW_RELATIVES_EVENTS, '_DEAT'.($sosa==1 ? '_PARE' : '_GPAR'))) {
					foreach ($parent->getAllFactsByType(explode('|', WT_EVENTS_DEAT)) as $sEvent) {
						if ($sEvent->getDate()->isOK() && WT_Date::Compare($bDate, $sEvent->getDate())<=0 && WT_Date::Compare($sEvent->getDate(), $dDate)<=0) {
							switch ($sosa) {
							case 1:
								// Convert the event to a close relatives event
								$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1_PARE ', $sEvent->getGedcomRecord()); // Full
								$tmp_rec="1 _".$sEvent->getTag()."_PARE\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
								break;
							case 2:
								// Convert the event to a close relatives event
								$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1_GPA1 ', $sEvent->getGedcomRecord()); // Full
								$tmp_rec="1 _".$sEvent->getTag()."_GPA1\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
								break;
							case 3:
								// Convert the event to a close relatives event
								$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1_GPA2 ', $sEvent->getGedcomRecord()); // Full
								$tmp_rec="1 _".$sEvent->getTag()."_GPA2\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
								break;
							}
							// Create a new event
							$this->indifacts[]=new WT_Event($tmp_rec."\n2 ASSO @".$parent->getXref()."@", $parent, 0);
						}
					}
				}
			}

			if ($sosa==1 && strstr($SHOW_RELATIVES_EVENTS, '_MARR_PARE')) {
				// add father/mother marriages
				foreach ($family->getSpouses() as $parent) {
					foreach ($parent->getSpouseFamilies() as $sfamily) {
						foreach ($sfamily->getAllFactsByType(explode('|', WT_EVENTS_MARR)) as $sEvent) {
							if ($sEvent->getDate()->isOK() && WT_Date::Compare($bDate, $sEvent->getDate())<=0 && WT_Date::Compare($sEvent->getDate(), $dDate)<=0) {
								if ($sfamily->equals($family)) {
									if ($parent->getSex()=='F') {
										// show current family marriage only once
										continue;
									}
									// marriage of parents (to each other)
									// Convert the event to a close relatives event
									$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_MARR.')/', '1 _$1_FAMC ', $sEvent->getGedcomRecord()); // Full
									$tmp_rec="1 _".$sEvent->getTag()."_FAMC\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
								} else {
									// marriage of a parent (to another spouse)
									// Convert the event to a close relatives event
									$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_MARR.')/', '1 _$1_PARE ', $sEvent->getGedcomRecord()); // Full
									$tmp_rec="1 _".$sEvent->getTag()."_PARE\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
								}
								// Create a new event
								$this->indifacts[]=new WT_Event($tmp_rec."\n2 ASSO @".$parent->getXref()."@\n2 ASSO @".$sEvent->getSpouseId().'@', $sfamily, 0);
							}
						}
					}
				}
			}
		}
	}

	/**
	* add children events to individual facts array
	*
	* @param string $family   Family object
	* @param string $option   Family level indicator
	* @param string $relation Relationship path indicator
	* @return records added to indifacts array
	*/
	function add_children_facts($family, $option, $relation) {
		global $SHOW_RELATIVES_EVENTS;

		// Deal with recursion.
		switch ($option) {
		case '_CHIL':
			// Add grandchildren
			foreach ($family->getChildren() as $child) {
				foreach ($child->getSpouseFamilies() as $cfamily) {
					switch ($child->getSex()) {
					case 'M':
						$this->add_children_facts($cfamily, '_GCHI', 'son');
						break;
					case 'F':
						$this->add_children_facts($cfamily, '_GCHI', 'dau');
						break;
					case 'U':
						$this->add_children_facts($cfamily, '_GCHI', 'chi');
						break;
					}
				}
			}
			break;
		}

		// For each child in the family
		foreach ($family->getChildren() as $child) {
			if ($child->getXref()==$this->getXref()) {
				// We are not our own sibling!
				continue;
			}
			// add child's birth
			if (strpos($SHOW_RELATIVES_EVENTS, '_BIRT'.str_replace('_HSIB', '_SIBL', $option))!==false) {
				foreach ($child->getAllFactsByType(explode('|', WT_EVENTS_BIRT)) as $sEvent) {
					$sgdate=$sEvent->getDate();
					// Always show _BIRT_CHIL, even if the dates are not known
					if ($option=='_CHIL' || $sgdate->isOK() && WT_Date::Compare($this->getEstimatedBirthDate(), $sgdate)<=0 && WT_Date::Compare($sgdate, $this->getEstimatedDeathDate())<=0) {
						if ($option=='_GCHI' && $relation=='son') {
							// Convert the event to a close relatives event.
							$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_BIRT.')/', '1 _$1_GCH1', $sEvent->getGedcomRecord()); // Full
							$tmp_rec="1 _".$sEvent->getTag()."_GCH1\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
						} elseif ($option=='_GCHI' && $relation=='dau') {
							// Convert the event to a close relatives event.
							$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_BIRT.')/', '1 _$1_GCH2', $sEvent->getGedcomRecord()); // Full
							$tmp_rec="1 _".$sEvent->getTag()."_GCH2\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
						} else {
							// Convert the event to a close relatives event.
							$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_BIRT.')/', '1 _$1'.$option, $sEvent->getGedcomRecord()); // Full
							$tmp_rec="1 _".$sEvent->getTag().$option."\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
						}
						$event=new WT_Event($tmp_rec."\n2 ASSO @".$child->getXref()."@", $child, 0);
						if (!in_array($event, $this->indifacts)) {
							$this->indifacts[]=$event;
						}
					}
				}
			}
			// add child's death
			if (strpos($SHOW_RELATIVES_EVENTS, '_DEAT'.str_replace('_HSIB', '_SIBL', $option))!==false) {
				foreach ($child->getAllFactsByType(explode('|', WT_EVENTS_DEAT)) as $sEvent) {
					$sgdate=$sEvent->getDate();
					$srec = $sEvent->getGedcomRecord();
					if ($sgdate->isOK() && WT_Date::Compare($this->getEstimatedBirthDate(), $sgdate)<=0 && WT_Date::Compare($sgdate, $this->getEstimatedDeathDate())<=0) {
						if ($option=='_GCHI' && $relation=='son') {
							// Convert the event to a close relatives event.
							$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1_GCH1', $sEvent->getGedcomRecord()); // Full
							$tmp_rec="1 _".$sEvent->getTag()."_GCH1\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
						} elseif ($option=='_GCHI' && $relation=='dau') {
							// Convert the event to a close relatives event.
							$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1_GCH2', $sEvent->getGedcomRecord()); // Full
							$tmp_rec="1 _".$sEvent->getTag()."_GCH2\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
						} else {
							// Convert the event to a close relatives event.
							$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1'.$option, $sEvent->getGedcomRecord()); // Full
							$tmp_rec="1 _".$sEvent->getTag().$option."\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
						}
						$event=new WT_Event($tmp_rec."\n2 ASSO @".$child->getXref()."@", $child, 0);
						if (!in_array($event, $this->indifacts)) {
							$this->indifacts[]=$event;
						}
					}
				}
			}
			// add child's marriage
			if (strstr($SHOW_RELATIVES_EVENTS, '_MARR'.str_replace('_HSIB', '_SIBL', $option))) {
				foreach ($child->getSpouseFamilies() as $sfamily) {
					foreach ($sfamily->getAllFactsByType(explode('|', WT_EVENTS_MARR)) as $sEvent) {
						$sgdate=$sEvent->getDate();
						if ($sgdate->isOK() && WT_Date::Compare($this->getEstimatedBirthDate(), $sgdate)<=0 && WT_Date::Compare($sgdate, $this->getEstimatedDeathDate())<=0) {
							if ($option=='_GCHI' && $relation=='son') {
								// Convert the event to a close relatives event.
								$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_MARR.')/', '1 _$1_GCH1', $sEvent->getGedcomRecord()); // Full
								$tmp_rec="1 _".$sEvent->getTag()."_GCH1\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
							} elseif ($option=='_GCHI' && $relation=='dau') {
								// Convert the event to a close relatives event.
								$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_MARR.')/', '1 _$1_GCH2', $sEvent->getGedcomRecord()); // Full
								$tmp_rec="1 _".$sEvent->getTag()."_GCH2\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
							} else {
								// Convert the event to a close relatives event.
								$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_MARR.')/', '1 _$1'.$option, $sEvent->getGedcomRecord()); // Full
								$tmp_rec="1 _".$sEvent->getTag().$option."\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
							}
							$event=new WT_Event($tmp_rec."\n2 ASSO @".$child->getXref()."@\n2 ASSO @".$sEvent->getSpouseId()."@", $child, 0);
							if (!in_array($event, $this->indifacts)) {
								$this->indifacts[]=$event;
							}
						}
					}
				}
			}
		}
	}
	/**
	* add spouse events to individual facts array
	*
	* bdate = indi birth date record
	* ddate = indi death date record
	*
	* @param string $spouse Person object
	* @param string $famrec family Gedcom record
	* @return records added to indifacts array
	*/
	function add_spouse_facts($spouse, $famrec='') {
		global $SHOW_RELATIVES_EVENTS;

		// do not show if divorced
		if (preg_match('/\n1 (?:'.WT_EVENTS_DIV.')\b/', $famrec)) {
			return;
		}
		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();

		// add spouse death
		if ($spouse && strstr($SHOW_RELATIVES_EVENTS, '_DEAT_SPOU')) {
			foreach ($spouse->getAllFactsByType(explode('|', WT_EVENTS_DEAT)) as $sEvent) {
				$sdate=$sEvent->getDate();
				if ($sdate->isOK() && WT_Date::Compare($this->getEstimatedBirthDate(), $sdate)<=0 && WT_Date::Compare($sdate, $this->getEstimatedDeathDate())<=0) {
					// Convert the event to a close relatives event.
					$tmp_rec=preg_replace('/^1 ('.WT_EVENTS_DEAT.')/', '1 _$1_SPOU ', $sEvent->getGedcomRecord()); // Full
					$tmp_rec="1 _".$sEvent->getTag()."_SPOU\n2 DATE ".$sEvent->getValue('DATE')."\n2 PLAC ".$sEvent->getValue('PLAC'); // Abbreviated
					// Create a new event
					$this->indifacts[]=new WT_Event($tmp_rec."\n2 ASSO @".$spouse->getXref()."@", $spouse, 0);
				}
			}
		}
	}

	/**
	* add historical events to individual facts array
	*
	* @return records added to indifacts array
	*
	* Historical facts are imported from optional language file : histo.xx.php
	* where xx is language code
	* This file should contain records similar to :
	*
	* $histo[]="1 EVEN\n2 TYPE History\n2 DATE 11 NOV 1918\n2 NOTE WW1 Armistice";
	* $histo[]="1 EVEN\n2 TYPE History\n2 DATE 8 MAY 1945\n2 NOTE WW2 Armistice";
	* etc...
	*
	*/
	function add_historical_facts() {
		global $SHOW_RELATIVES_EVENTS;
		if (!$SHOW_RELATIVES_EVENTS) return;

		// Only include events between birth and death
		$bDate=$this->getEstimatedBirthDate();
		$dDate=$this->getEstimatedDeathDate();
		if (!$bDate->isOK()) return;

		if (file_exists(get_site_setting('INDEX_DIRECTORY').'histo.'.WT_LOCALE.'.php')) {
			require get_site_setting('INDEX_DIRECTORY').'histo.'.WT_LOCALE.'.php';
			foreach ($histo as $indexval=>$hrec) {
				$sdate=new WT_Date(get_gedcom_value('DATE', 2, $hrec, '', false));
				if ($sdate->isOK() && WT_Date::Compare($this->getEstimatedBirthDate(), $sdate)<=0 && WT_Date::Compare($sdate, $this->getEstimatedDeathDate())<=0) {
					$event = new WT_Event($hrec, null, -1);
					$this->indifacts[] = $event;
				}
			}
		}
	}
	/**
	* add events where pid is an ASSOciate
	*
	* @return records added to indifacts array
	*
	*/
	function add_asso_facts() {
		$associates=array_merge(
			fetch_linked_indi($this->getXref(), 'ASSO', $this->ged_id),
			fetch_linked_fam ($this->getXref(), 'ASSO', $this->ged_id)
		);
		foreach ($associates as $associate) {
			foreach ($associate->getFacts() as $event) {
				$srec = $event->getGedcomRecord();
				$arec = get_sub_record(2, '2 ASSO @'.$this->getXref().'@', $srec);
				if ($arec) {
					// Extract the important details from the fact
					$factrec='1 '.$event->getTag();
					if (preg_match('/\n2 DATE .*/', $srec, $match)) {
						$factrec.=$match[0];
					}
					if (preg_match('/\n2 PLAC .*/', $srec, $match)) {
						$factrec.=$match[0];
					}
					if ($associate instanceof WT_Family) {
						foreach ($associate->getSpouses() as $spouse) {
							$factrec.="\n2 ASSO @".$spouse->getXref().'@';
						}
					} else {
						$factrec.="\n2 ASSO @".$associate->getXref().'@';
						// CHR/BAPM events are commonly used.  Generate the reverse relationship
						if ($event->getTag()=='CHR' || $event->getTag()=='BAPM') {
							switch ($associate->getSex()) {
							case 'M':
								$factrec.="\n3 RELA godson";
								break;
							case 'F':
								$factrec.="\n3 RELA goddaughter";
								break;
							case 'U':
								$factrec.="\n3 RELA godchild";
								break;
							}
						}
						$this->indifacts[] = new WT_Event($factrec, $associate, 0);
					}
				}
			}
		}
	}

	/**
	* Merge the facts from another Person object into this object
	* for generating a diff view
	* @param Person $diff the person to compare facts with
	*/
	function diffMerge($diff) {
		if (is_null($diff)) return;
		$this->parseFacts();
		$diff->parseFacts();
		//-- loop through new facts and add them to the list if they are any changes
		//-- compare new and old facts of the Personal Fact and Details tab 1
		for ($i=0; $i<count($this->indifacts); $i++) {
			$found=false;
			$oldfactrec = $this->indifacts[$i]->getGedcomRecord();
			foreach ($diff->indifacts as $newfact) {
				$newfactrec = $newfact->getGedcomRecord();
				//-- remove all whitespace for comparison
				$tnf = preg_replace('/\s+/', ' ', $newfactrec);
				$tif = preg_replace('/\s+/', ' ', $oldfactrec);
				if ($tnf==$tif) {
					$this->indifacts[$i] = $newfact; //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			//-- fact was deleted?
			if (!$found) {
				$this->indifacts[$i]->gedcomRecord.="\nWT_OLD\n";
			}
		}
		//-- check for any new facts being added
		foreach ($diff->indifacts as $newfact) {
			$found=false;
			foreach ($this->indifacts as $fact) {
				$tif = preg_replace('/\s+/', ' ', $fact->getGedcomRecord());
				$tnf = preg_replace('/\s+/', ' ', $newfact->getGedcomRecord());
				if ($tif==$tnf) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact->gedcomRecord.="\nWT_NEW\n";
				$this->indifacts[]=$newfact;
			}
		}
		//-- compare new and old facts of the Notes Sources and Media tab 2
		for ($i=0; $i<count($this->otherfacts); $i++) {
			$found=false;
			foreach ($diff->otherfacts as $newfact) {
				if (trim($newfact->getGedcomRecord())==trim($this->otherfacts[$i]->getGedcomRecord())) {
					$this->otherfacts[$i] = $newfact; //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->otherfacts[$i]->gedcomRecord.="\nWT_OLD\n";
			}
		}
		foreach ($diff->otherfacts as $indexval => $newfact) {
			$found=false;
			foreach ($this->otherfacts as $indexval => $fact) {
				if (trim($fact->getGedcomRecord())==trim($newfact->getGedcomRecord())) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact->gedcomRecord.="\nWT_NEW\n";
				$this->otherfacts[]=$newfact;
			}
		}

		//-- compare new and old facts of the Global facts
		for ($i=0; $i<count($this->globalfacts); $i++) {
			$found=false;
			foreach ($diff->globalfacts as $indexval => $newfact) {
				if (trim($newfact->getGedcomRecord())==trim($this->globalfacts[$i]->getGedcomRecord())) {
					$this->globalfacts[$i] = $newfact; //-- make sure the correct linenumber is used
					$found=true;
					break;
				}
			}
			if (!$found) {
				$this->globalfacts[$i]->gedcomRecord.="\nWT_OLD\n";
			}
		}
		foreach ($diff->globalfacts as $indexval => $newfact) {
			$found=false;
			foreach ($this->globalfacts as $indexval => $fact) {
				if (trim($fact->getGedcomRecord())==trim($newfact->getGedcomRecord())) {
					$found=true;
					break;
				}
			}
			if (!$found) {
				$newfact->gedcomRecord.="\nWT_NEW\n";
				$this->globalfacts[]=$newfact;
			}
		}

		foreach ($diff->getChildFamilies() as $diff_family) {
			$exists=false;
			foreach ($this->getChildFamilies() as $family) {
				if ($family->equals($diff_family)) {
					$exists=true;
					break;
				}
			}
			if (!$exists) {
				$this->_childFamilies[]=$diff_family;
			}
		}

		foreach ($diff->getSpouseFamilies() as $diff_family) {
			$exists=false;
			foreach ($this->getSpouseFamilies() as $family) {
				if ($family->equals($diff_family)) {
					$exists=true;
					break;
				}
			}
			if (!$exists) {
				$this->_spouseFamilies[]=$diff_family;
			}
		}
	}

	/**
	* get primary parents names for this person
	* @param string $classname optional css class
	* @param string $display optional css style display
	* @return string a div block with father & mother names
	*/
	function getPrimaryParentsNames($classname='', $display='') {
		$fam = $this->getPrimaryChildFamily();
		if (!$fam) return '';
		$txt = '<div';
		if ($classname) $txt .= " class=\"$classname\"";
		if ($display) $txt .= " style=\"display:$display\"";
		$txt .= '>';
		$husb = $fam->getHusband();
		if ($husb) {
			// Temporarily reset the 'prefered' display name, as we always
			// want the default name, not the one selected for display on the indilist.
			$primary=$husb->getPrimaryName();
			$husb->setPrimaryName(null);
			$txt .= /* I18N: %s is the name of a person's father */ WT_I18N::translate('Father: %s', $husb->getFullName()).'<br>';
			$husb->setPrimaryName($primary);
		}
		$wife = $fam->getWife();
		if ($wife) {
			// Temporarily reset the 'prefered' display name, as we always
			// want the default name, not the one selected for display on the indilist.
			$primary=$wife->getPrimaryName();
			$wife->setPrimaryName(null);
			$txt .= /* I18N: %s is the name of a person's mother */ WT_I18N::translate('Mother: %s', $wife->getFullName());
			$wife->setPrimaryName($primary);
		}
		$txt .= '</div>';
		return $txt;
	}

	// Generate a URL to this record, suitable for use in HTML
	public function getHtmlUrl() {
		return parent::_getLinkUrl('individual.php?pid=', '&amp;');
	}
	// Generate a URL to this record, suitable for use in javascript, HTTP headers, etc.
	public function getRawUrl() {
		return parent::_getLinkUrl('individual.php?pid=', '&');
	}

	// If this object has no name, what do we call it?
	function getFallBackName() {
		return '@P.N. /@N.N./';
	}

	// Convert a name record into 'full' and 'sort' versions.
	// Use the NAME field to generate the 'full' version, as the
	// gedcom spec says that this is the person's name, as they would write it.
	// Use the SURN field to generate the sortable names.  Note that this field
	// may also be used for the 'true' surname, perhaps spelt differently to that
	// recorded in the NAME field. e.g.
	//
	// 1 NAME Robert /de Gliderow/
	// 2 GIVN Robert
	// 2 SPFX de
	// 2 SURN CLITHEROW
	// 2 NICK The Bald
	//
	// full=>'Robert de Gliderow 'The Bald''
	// sort=>'CLITHEROW, ROBERT'
	//
	// Handle multiple surnames, either as;
	// 1 NAME Carlos /Vasquez/ y /Sante/
	// or
	// 1 NAME Carlos /Vasquez y Sante/
	// 2 GIVN Carlos
	// 2 SURN Vasquez,Sante
	protected function _addName($type, $full, $gedrec) {
		global $UNKNOWN_NN, $UNKNOWN_PN, $TEXT_DIRECTION;

		////////////////////////////////////////////////////////////////////////////
		// Extract the structured name parts - use for "sortable" names and indexes
		////////////////////////////////////////////////////////////////////////////

		$sublevel=1+(int)$gedrec[0];
		$NPFX=preg_match("/\n{$sublevel} NPFX (.+)/", $gedrec, $match) ? $match[1] : '';
		$GIVN=preg_match("/\n{$sublevel} GIVN (.+)/", $gedrec, $match) ? $match[1] : '';
		$SURN=preg_match("/\n{$sublevel} SURN (.+)/", $gedrec, $match) ? $match[1] : '';
		$NSFX=preg_match("/\n{$sublevel} NSFX (.+)/", $gedrec, $match) ? $match[1] : '';
		$NICK=preg_match("/\n{$sublevel} NICK (.+)/", $gedrec, $match) ? $match[1] : '';

		// SURN is an comma-separated list of surnames...
		if ($SURN) {
			$SURNS=preg_split('/ *, */', $SURN);
		} else {
			$SURNS=array();
		}
		// ...so is GIVN - but nobody uses it like that
		$GIVN=str_replace('/ *, */', ' ', $GIVN);

		////////////////////////////////////////////////////////////////////////////
		// Extract the components from NAME - use for the "full" names
		////////////////////////////////////////////////////////////////////////////

		// Fix bad slashes.  e.g. 'John/Smith' => 'John/Smith/'
		if (substr_count($full, '/')%2==1) {
			$full=$full.'/';
		} else {
			$full=$full;
		}

		// GEDCOM uses "//" to indicate an unknown surname
		$full=preg_replace('/\/\//', '/@N.N./', $full);

		// Extract the surname.
		// Note, there may be multiple surnames, e.g. Jean /Vasquez/ y /Cortes/
		if (preg_match('/\/.*\//', $full, $match)) {
			$surname=str_replace('/', '', $match[0]);
		} else {
			$surname='';
		}

		// If we don't have a SURN record, extract it from the NAME
		if (!$SURNS) {
			if (preg_match_all('/\/([^\/]*)\//', $full, $matches)) {
				// There can be many surnames, each wrapped with '/'
				$SURNS=$matches[1];
				foreach ($SURNS as $n=>$SURN) {
					// Remove surname prefix
					$SURNS[$n]=preg_replace('/^(?:a |aan |ab |af |al |ap |as |auf |av |bat |bij |bin |bint |d\'|da |de |del |della |dem |den |der |di |du |el |fitz |het |ibn |l\'|la |las |le |les |los |onder |op |over |\'s |st |\'t |te |ten |ter |till |tot |uit |uijt |van |vanden |von |voor |vor )+/', '', $SURN);
				}
			} else {
				// It is valid not to have a surname at all
				$SURNS=array('');
			}
		}

		// If we don't have a GIVN record, extract it from the NAME
		if (!$GIVN) {
			// remove any prefix
			$GIVN=preg_replace('/(?:(?:ADM|AMB|BRIG|CAN|CAPT|CHAN|CHAPLN|CMDR|COL|CPL|CPT|DR|GEN|GOV|HON|LADY|LORD|LT|MR|MRS|MS|MSGR|PFC|PRES|PROF|PVT|RABBI|REP|REV|SEN|SGT|SIR|SR|SRA|SRTA|VEN) )+$/', '', $full);
			// remove any suffix
			$GIVN=preg_replace('/(?: (?:ESQ|ESQUIRE|JR|JUNIOR|SR|SENIOR|[IVX]+))+$/', '', $GIVN);
			// remove surname
			$GIVN=preg_replace('/ ?\/.*\/ ?/', '', $GIVN);
			// remove nickname
			$GIVN=preg_replace('/ ?".+"/', '', $GIVN);
		}

		// Add placeholder for unknown given name
		if (!$GIVN) {
			$GIVN='@P.N.';
			$pos=strpos($full, '/');
			$full=substr($full, 0, $pos).'@P.N. '.substr($full, $pos);
		}

		// The NPFX field might be present, but not appear in the NAME
		if ($NPFX && strpos($full, "$NPFX ")!==0) {
			$full="$NPFX $full";
		}

		// The NSFX field might be present, but not appear in the NAME
		if ($NSFX && strrpos($full, " $NSFX")!==strlen($full)-strlen(" $NSFX")) {
			$full="$full $NSFX";
		}

		// GEDCOM nicknames should be specificied in a NICK field, or in the
		// NAME filed, surrounded by ASCII quotes (or both).
		if ($NICK) {
			// NICK field found.  Add localised quotation marks.

			// GREG 28/Jan/12 - these localised quotation marks apparantly cause problems with LTR names on RTL
			// pages and vice-versa.  Just use straight ASCII quotes.  Keep the old code, so that we keep the
			// translations.
			if (false) {
				$QNICK=/* I18N: Place a nickname in quotation marks */ WT_I18N::translate('“%s”', $NICK);
			} else {
				$QNICK='"'.$NICK.'"';
			}

			if (preg_match('/(^| |"|«|“|\'|‹|‘|„)'.preg_quote($NICK, '/').'( |"|»|”|\'|›|’|”|$)/', $full)) {
				// NICK present in name.  Localise ASCII quotes (but leave others).
				// GREG 28/Jan/12 - redundant - see comment above.
				// $full=str_replace('"'.$NICK.'"', $QNICK, $full);
			} else {
				// NICK not present in NAME.
				$pos=strpos($full, '/');
				if ($pos===false) {
					// No surname - append it
					$full.=' '.$QNICK;
				} else {
					// Insert before surname
					$full=substr($full, 0, $pos).$QNICK.' '.substr($full, $pos);
				}
			}
		}

		// Remove slashes - they don't get displayed
		// $fullNN keeps the @N.N. placeholders, for the database
		// $full is for display on-screen
		$fullNN=str_replace('/', '', $full);

		// Insert placeholders for any missing/unknown names
		if (strpos($full, '@N.N.')!==false) {
			$full=str_replace('@N.N.', $UNKNOWN_NN, $full);
		}
		if (strpos($full, '@P.N.')!==false) {
			$full=str_replace('@P.N.', $UNKNOWN_PN, $full);
		}
		$full='<span class="NAME" dir="auto">'.preg_replace('/\/([^\/]*)\//', '<span class="SURN">$1</span>', htmlspecialchars($full)).'</span>';

		// The standards say you should use a suffix of '*' for preferred name
		$full=preg_replace('/([^ >]*)\*/', '<span class="starredname">\\1</span>', $full);

		// Remove prefered-name indicater - they don't go in the database
		$GIVN  =str_replace('*', '', $GIVN);
		$fullNN=str_replace('*', '', $fullNN);

		foreach ($SURNS AS $SURN) {
			// Scottish 'Mc and Mac ' prefixes both sort under 'Mac'
			if (strcasecmp(substr($SURN, 0, 2), 'Mc')==0) {
				$SURN=substr_replace($SURN, 'Mac', 0, 2);
			} elseif (strcasecmp(substr($SURN, 0, 4), 'Mac ')==0) {
				$SURN=substr_replace($SURN, 'Mac', 0, 4);
			}

			$this->_getAllNames[]=array(
				'type'=>$type,
				'sort'=>$SURN.','.$GIVN,
				'full'=>$full,       // This is used for display
				'fullNN'=>$fullNN,   // This goes into the database
				'surname'=>$surname, // This goes into the database
				'givn'=>$GIVN,       // This goes into the database
				'surn'=>$SURN,       // This goes into the database
			);
		}
	}

	// Get an array of structures containing all the names in the record
	public function getAllNames() {
		return $this->_getAllNames('NAME', 1);
	}

	// Extra info to display when displaying this record in a list of
	// selection items or favorites.
	function format_list_details() {
		return
		$this->format_first_major_fact(WT_EVENTS_BIRT, 1).
		$this->format_first_major_fact(WT_EVENTS_DEAT, 1);
	}

	// create a short name for compact display on charts
	public function getShortName() {
		global $bwidth, $SHOW_HIGHLIGHT_IMAGES, $UNKNOWN_NN, $UNKNOWN_PN;
		// Estimate number of characters that can fit in box. Calulates to 28 characters in webtrees theme, or 34 if no thumbnail used.
		if ($SHOW_HIGHLIGHT_IMAGES) {
			$char = intval(($bwidth-40)/6.5); 
		} else {
			$char = ($bwidth/6.5);
		}
		if ($this->canDisplayName()) {
			$tmp=$this->getAllNames();
			$givn = $tmp[$this->getPrimaryName()]['givn'];
			$surn = $tmp[$this->getPrimaryName()]['surname'];
			$new_givn = explode(' ', $givn);
			$count_givn = count($new_givn);
			$len_givn = utf8_strlen($givn);
			$len_surn = utf8_strlen($surn);
			$len = $len_givn + $len_surn;
			$i = 1;
			while ($len > $char && $i<=$count_givn) {
				$new_givn[$count_givn-$i] = utf8_substr($new_givn[$count_givn-$i],0,1);
				$givn = implode(' ', $new_givn);
				$len_givn = utf8_strlen($givn);
				$len = $len_givn + $len_surn;
				$i++;
			}
			$max_surn = $char-$i*2;
			if ($len_surn > $max_surn) {
				$surn = substr($surn, 0, $max_surn).'...';
				$len_surn = utf8_strlen($surn);
			}
			$shortname =  str_replace(
				array('@P.N.', '@N.N.'),
				array($UNKNOWN_PN, $UNKNOWN_NN),
				$givn.' '.$surn
			);
			return $shortname;
		} else {
			return WT_I18N::translate('Private');
		}
	}

}
