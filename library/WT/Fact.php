<?php
// Class that defines an event details object
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2008 PGV Development Team.  All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Fact {
	private $fact_id  = null;  // Unique identifier for this fact
	private $parent   = null;  // The GEDCOM record from which this fact is taken.
	private $gedcom   = null;  // The raw GEDCOM data for this fact
	private $tag      = null;  // The GEDCOM tag for this record

	private $is_old   = false; // Is this a pending record?
	private $is_new   = false; // Is this a pending record?

	private $date     = null;  // The WT_Date object for the "2 DATE ..." attribute
	private $place    = null;  // The WT_Place object for the "2 PLAC ..." attribute

	// Temporary(!) variables that are used by other scripts
	public $temp      = null; // Timeline controller
	public $sortOrder = 0;    // sort_facts()

	// Create an event objects from a gedcom fragment.
	// We need the parent object (to check privacy) and a (pseudo) fact ID to
	// identify the fact within the record.
	function __construct($gedcom, WT_GedcomRecord $parent, $fact_id) {
		if (preg_match('/^1 ('.WT_REGEX_TAG.')/', $gedcom, $match)) {
			$this->gedcom  = $gedcom;
			$this->parent  = $parent;
			$this->fact_id = $fact_id;
			$this->tag     = $match[1];
		} else {
			// TODO need to rewrite code that passes dummy data to this function
			//throw new Exception('Invalid GEDCOM data passed to WT_Fact::_construct('.$gedcom.')');
		}
	}

	// Get the value of level 1 data in the fact
	// Allow for multi-line values
	function getValue() {
		if (preg_match('/^1 (?:' . $this->tag . ') ?(.*(?:(?:\n2 CONT .*)*))/', $this->gedcom, $match)) {
			return str_replace("\n2 CONT ", "\n", $match[1]);
		} else {
			return null;
		}
	}

	// Get the record to which this fact links
	function getTarget() {
		$xref = trim($this->getValue(), '@');
		switch ($this->tag) {
		case 'FAMC':
		case 'FAMS':
			return WT_Family::getInstance($xref);
		case 'HUSB':
		case 'WIFE':
		case 'CHIL':
			return WT_Individual::getInstance($xref);
		case 'SOUR':
			return WT_Source::getInstance($xref);
		case 'OBJE':
			return WT_Media::getInstance($xref);
		case 'REPO':
			return WT_Repository::getInstance($xref);
		case 'NOTE':
			return WT_Note::getInstance($xref);
		default:
			return WT_GedcomRecord::getInstance($xref);
		}
	}

	// Get the value of level 2 data in the fact
	function getAttribute($tag) {
		if (preg_match('/\n2 (?:' . $tag . ') ?(.*(?:(?:\n3 CONT .*)*)*)/', $this->gedcom, $match)) {
			return str_replace("\n3 CONT ", "\n", $match[1]);
		} else {
			return null;
		}
	}

	// Do the privacy rules allow us to display this fact to the current user
	function canShow($access_level=WT_USER_ACCESS_LEVEL) {
		// TODO - use the privacy settings for $this->gedcom_id, not the default gedcom.
		global $person_facts, $global_facts;

		// Does this record have an explicit RESN?
		if (strpos($this->gedcom, "\n2 RESN confidential")) {
			return WT_PRIV_NONE >= $access_level;
		}
		if (strpos($this->gedcom, "\n2 RESN privacy")) {
			return WT_PRIV_USER >= $access_level;
		}
		if (strpos($this->gedcom, "\n2 RESN none")) {
			return true;
		}

		// Does this record have a default RESN?
		if (!$this->parent) die($eek);
		$xref = $this->parent->getXref();
		if (isset($person_facts[$xref][$this->tag])) {
			return $person_facts[$xref][$this->tag] >= $access_level;
		}
		if (isset($global_facts[$this->tag])) {
			return $global_facts[$this->tag] >= $access_level;
		}

		// No restrictions - it must be public
		return true;
	}

	// Check whether this fact is protected against edit
	public function canEdit() {
		// Managers can edit anything
		// Members cannot edit RESN, CHAN and locked records
		return
			$this->parent->canEdit() && !$this->isOld() && (
				WT_USER_GEDCOM_ADMIN ||
				WT_USER_CAN_EDIT && strpos($this->gedcom, "\n2 RESN locked")===false && $this->getTag()!='RESN' && $this->getTag()!='CHAN'
			);
	}

	// The place where the event occured.
	function getPlace() {
		if ($this->place === null) {
			$this->place = $this->getAttribute('PLAC');
		}
		return $this->place;
	}

	// We can call this function many times, especially when sorting,
	// so keep a copy of the date.
	function getDate() {
		if ($this->date === null) {
			$this->date = new WT_Date($this->getAttribute('DATE'));
		}
		return $this->date;
	}

	// The raw GEDCOM data for this fact
	function getGedcom() {
		return $this->gedcom;
	}

	// Unique identifier for the fact
	function getFactId() {
		return $this->fact_id;
	}

	// What sort of fact is this?
	function getTag() {
		return $this->tag;
	}

	// Used to convert a real fact (e.g. BIRT) into a close-relative’s fact (e.g. _BIRT_CHIL)
	function setTag($tag) {
		$this->tag = $tag;
	}

	// The Person/Family record where this WT_Fact came from
	function getParent() {
		return $this->parent;
	}

	function getLabel($abbreviate=false) {
		if ($abbreviate) {
			return WT_Gedcom_Tag::getAbbreviation($this->tag);
		} else {
			switch($this->tag) {
			case 'EVEN':
			case 'FACT':
				if ($this->getAttribute('TYPE')) {
					// Custom FACT/EVEN - with a TYPE
					return WT_I18N::translate(WT_Filter::escapeHtml($this->getAttribute('TYPE')));
				}
				// no break - drop into next case
			default:
				return WT_Gedcom_Tag::getLabel($this->tag, $this->parent);
			}
		}
	}

	// Is this a pending edit?
	public function setIsOld() {
		$this->is_old = true;
		$this->is_new = false;
	}
	public function isOld() {
		return $this->is_old;
	}
	public function setIsNew() {
		$this->is_new = true;
		$this->is_old = false;
	}
	public function isNew() {
		return $this->is_new;
	}

	// Print a simple fact version of this event
	function print_simple_fact($return=false, $anchor=false) {
		global $ABBREVIATE_CHART_LABELS;

		$value = $this->getValue();

		$data = '<span class="details_label">'.$this->getLabel($ABBREVIATE_CHART_LABELS).'</span>';
		// Don't display "yes", because format_fact_date() does this for us.  (Should it?)
		if ($value && $value != 'Y') {
			$data .= ' <span dir="auto">' . WT_Filter::escapeHtml($value) . '</span>';
		}
		$data .= ' '.format_fact_date($this, $this->getParent(), $anchor, false);
		$data .= ' '.format_fact_place($this, $anchor, false, false);
		$data .= '<br>';
		if ($return) {
			return $data;
		} else {
			echo $data;
		}
	}

	// Display an icon for this fact.
	// Icons are held in a theme subfolder.  Not all themes provide icons.
	function Icon() {
		$dir=WT_THEME_DIR.'images/facts/';
		$tag=$this->getTag();
		$file=$tag.'.png';
		if (file_exists($dir.$file)) {
			return '<img src="'.WT_STATIC_URL.$dir.$file.'" title="'.WT_Gedcom_Tag::getLabel($tag).'">';
		} elseif (file_exists($dir.'NULL.png')) {
			// Spacer image - for alignment - until we move to a sprite.
			return '<img src="'.WT_STATIC_URL.$dir.'NULL.png">';
		} else {
			return '';
		}
	}

	// Static Helper functions to sort events
	static function CompareDate($a, $b) {
		if ($a->getDate()->isOK() && $b->getDate()->isOK()) {
			// If both events have dates, compare by date
			$ret=WT_Date::Compare($a->getDate(), $b->getDate());
			if ($ret==0) {
				// If dates are the same, compare by fact type
				$ret=self::CompareType($a, $b);
				// If the fact type is also the same, retain the initial order
				if ($ret==0) {
					$ret=$a->sortOrder - $b->sortOrder;
				}
			}
			return $ret;
		} else {
			// One or both events have no date - retain the initial orde
			return $a->sortOrder - $b->sortOrder;
		}
	}

	// Static method to Compare two events by their type
	static function CompareType($a, $b) {
		global $factsort;

		if (empty($factsort))
			$factsort=array_flip(array(
				"BIRT",
				"_HNM",
				"ALIA", "_AKA", "_AKAN",
				"ADOP", "_ADPF", "_ADPF",
				"_BRTM",
				"CHR", "BAPM",
				"FCOM",
				"CONF",
				"BARM", "BASM",
				"EDUC",
				"GRAD",
				"_DEG",
				"EMIG", "IMMI",
				"NATU",
				"_MILI", "_MILT",
				"ENGA",
				"MARB", "MARC", "MARL", "_MARI", "_MBON",
				"MARR", "MARR_CIVIL", "MARR_RELIGIOUS", "MARR_PARTNERS", "MARR_UNKNOWN", "_COML",
				"_STAT",
				"_SEPR",
				"DIVF",
				"MARS",
				"_BIRT_CHIL",
				"DIV", "ANUL",
				"_BIRT_", "_MARR_", "_DEAT_","_BURI_", // other events of close relatives
				"CENS",
				"OCCU",
				"RESI",
				"PROP",
				"CHRA",
				"RETI",
				"FACT", "EVEN",
				"_NMR", "_NMAR", "NMR",
				"NCHI",
				"WILL",
				"_HOL",
				"_????_",
				"DEAT",
				"_FNRL", "BURI", "CREM", "_INTE",
				"_YART",
				"_NLIV",
				"PROB",
				"TITL",
				"COMM",
				"NATI",
				"CITN",
				"CAST",
				"RELI",
				"SSN", "IDNO",
				"TEMP",
				"SLGC", "BAPL", "CONL", "ENDL", "SLGS",
				"ADDR", "PHON", "EMAIL", "_EMAIL", "EMAL", "FAX", "WWW", "URL", "_URL",
				"AFN", "REFN", "_PRMN", "REF", "RIN", "_UID",
				"CHAN", "_TODO",
				"NOTE", "SOUR", "OBJE"
			));

		// Facts from same families stay grouped together
		// Keep MARR and DIV from the same families from mixing with events from other FAMs
		// Use the original order in which the facts were added
		if ($a->parent instanceof WT_Family && $b->parent instanceof WT_Family && $a->parent !== $b->parent) {
			return $a->sortOrder - $b->sortOrder;
		}

		$atag = $a->getTag();
		$btag = $b->getTag();

		// Events not in the above list get mapped onto one that is.
		if (!array_key_exists($atag, $factsort)) {
			if (preg_match('/^(_(BIRT|MARR|DEAT|BURI)_)/', $atag, $match)) {
				$atag=$match[1];
			} else {
				$atag="_????_";
			}
		}
		if (!array_key_exists($btag, $factsort)) {
			if (preg_match('/^(_(BIRT|MARR|DEAT|BURI)_)/', $btag, $match)) {
				$btag=$match[1];
			} else {
				$btag="_????_";
			}
		}

		//-- don't let dated after DEAT/BURI facts sort non-dated facts before DEAT/BURI
		//-- treat dated after BURI facts as BURI instead
		if ($a->getAttribute('DATE')!=NULL && $factsort[$atag]>$factsort['BURI'] && $factsort[$atag]<$factsort['CHAN']) $atag='BURI';
		if ($b->getAttribute('DATE')!=NULL && $factsort[$btag]>$factsort['BURI'] && $factsort[$btag]<$factsort['CHAN']) $btag='BURI';
		$ret = $factsort[$atag]-$factsort[$btag];
		//-- if facts are the same then put dated facts before non-dated facts
		if ($ret==0) {
			if ($a->getAttribute('DATE')!=NULL && $b->getAttribute('DATE')==NULL) return -1;
			if ($b->getAttribute('DATE')!=NULL && $a->getAttribute('DATE')==NULL) return 1;
			//-- if no sorting preference, then keep original ordering
			$ret = $a->sortOrder - $b->sortOrder;
		}
		return $ret;
	}

	// Allow native PHP functions such as array_unique() to work with objects
	public function __toString() {
		return $this->fact_id . '@' . $this->parent->getXref();
	}

}
