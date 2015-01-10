<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class WT_Fact - Class that defines an event details object
 */
class WT_Fact {
	/** @var string Unique identifier for this fact (currently implemented as a hash of the raw data). */
	private $fact_id;

	/** @var WT_GedcomRecord The GEDCOM record from which this fact is taken */
	private $parent;

	/** @var string The raw GEDCOM data for this fact */
	private $gedcom;

	/** @var string The GEDCOM tag for this record */
	private $tag;

	/** @var boolean Is this a recently deleted fact, pending approval? */
	private $pending_deletion = false;

	/** @var boolean Is this a recently added fact, pending approval? */
	private $pending_addition = false;

	/** @var WT_Date The date of this fact, from the “2 DATE …” attribute */
	private $date;

	/** @var WT_Place The place of this fact, from the “2 PLAC …” attribute */
	private $place;

	/** @var integer Temporary(!) variable Used by sort_facts() */
	public $sortOrder;

	/**
	 * Create an event object from a gedcom fragment.
	 * We need the parent object (to check privacy) and a (pseudo) fact ID to
	 * identify the fact within the record.
	 *
	 * @param string          $gedcom
	 * @param WT_GedcomRecord $parent
	 * @param string          $fact_id
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct($gedcom, WT_GedcomRecord $parent, $fact_id) {
		if (preg_match('/^1 (' . WT_REGEX_TAG . ')/', $gedcom, $match)) {
			$this->gedcom  = $gedcom;
			$this->parent  = $parent;
			$this->fact_id = $fact_id;
			$this->tag     = $match[1];
		} else {
			throw new InvalidArgumentException('Invalid GEDCOM data passed to WT_Fact::_construct('.$gedcom.')');
		}
	}

	/**
	 * Get the value of level 1 data in the fact
	 * Allow for multi-line values
	 *
	 * @return string|null
	 */
	public function getValue() {
		if (preg_match('/^1 (?:' . $this->tag . ') ?(.*(?:(?:\n2 CONT ?.*)*))/', $this->gedcom, $match)) {
			return preg_replace("/\n2 CONT ?/", "\n", $match[1]);
		} else {
			return null;
		}
	}

	/**
	 * Get the record to which this fact links
	 *
	 * @return WT_GedcomRecord|null
	 */
	public function getTarget() {
		$xref = trim($this->getValue(), '@');
		switch ($this->tag) {
		case 'FAMC':
		case 'FAMS':
			return WT_Family::getInstance($xref, $this->getParent()->getGedcomId());
		case 'HUSB':
		case 'WIFE':
		case 'CHIL':
			return WT_Individual::getInstance($xref, $this->getParent()->getGedcomId());
		case 'SOUR':
			return WT_Source::getInstance($xref, $this->getParent()->getGedcomId());
		case 'OBJE':
			return WT_Media::getInstance($xref, $this->getParent()->getGedcomId());
		case 'REPO':
			return WT_Repository::getInstance($xref, $this->getParent()->getGedcomId());
		case 'NOTE':
			return WT_Note::getInstance($xref, $this->getParent()->getGedcomId());
		default:
			return WT_GedcomRecord::getInstance($xref, $this->getParent()->getGedcomId());
		}
	}

	/**
	 * Get the value of level 2 data in the fact
	 *
	 * @param string $tag
	 *
	 * @return string|null
	 */
	public function getAttribute($tag) {
		if (preg_match('/\n2 (?:' . $tag . ') ?(.*(?:(?:\n3 CONT ?.*)*)*)/', $this->gedcom, $match)) {
			return preg_replace("/\n3 CONT ?/", "\n", $match[1]);
		} else {
			return null;
		}
	}

	/**
	 * Do the privacy rules allow us to display this fact to the current user
	 *
	 * @param integer $access_level
	 *
	 * @return boolean
	 */
	public function canShow($access_level = WT_USER_ACCESS_LEVEL) {
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

	/**
	 * Check whether this fact is protected against edit
	 *
	 * @return boolean
	 */
	public function canEdit() {
		// Managers can edit anything
		// Members cannot edit RESN, CHAN and locked records
		return
			$this->parent->canEdit() && !$this->isPendingDeletion() && (
				WT_USER_GEDCOM_ADMIN ||
				WT_USER_CAN_EDIT && strpos($this->gedcom, "\n2 RESN locked") === false && $this->getTag() != 'RESN' && $this->getTag() != 'CHAN'
			);
	}

	/**
	 * The place where the event occured.
	 *
	 * @return WT_Place
	 */
	public function getPlace() {
		if ($this->place === null) {
			$this->place = new WT_Place($this->getAttribute('PLAC'), $this->getParent()->getGedcomId());
		}

		return $this->place;
	}

	/**
	 * Get the date for this fact.
	 * We can call this function many times, especially when sorting,
	 * so keep a copy of the date.
	 *
	 * @return WT_Date
	 */
	public function getDate() {
		if ($this->date === null) {
			$this->date = new WT_Date($this->getAttribute('DATE'));
		}

		return $this->date;
	}

	/**
	 * The raw GEDCOM data for this fact
	 *
	 * @return string
	 */
	public function getGedcom() {
		return $this->gedcom;
	}

	/**
	 * Get a (pseudo) primary key for this fact.
	 *
	 * @return string
	 */
	public function getFactId() {
		return $this->fact_id;
	}

	// What sort of fact is this?
	/**
	 * What is the tag (type) of this fact, such as BIRT, MARR or DEAT.
	 *
	 * @return string
	 */
	public function getTag() {
		return $this->tag;
	}

	/**
	 * Used to convert a real fact (e.g. BIRT) into a close-relative’s fact (e.g. _BIRT_CHIL)
	 *
	 * @param string $tag
	 */
	public function setTag($tag) {
		$this->tag = $tag;
	}

	//
	/**
	 * The Person/Family record where this WT_Fact came from
	 *
	 * @return WT_GedcomRecord
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * Get the name of this fact type, for use as a label.
	 *
	 * @return string
	 */
	public function getLabel() {
		switch ($this->tag) {
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

	/**
	 * This is a newly deleted fact, pending approval.
	 */
	public function setPendingDeletion() {
		$this->pending_deletion = true;
		$this->pending_addition = false;
	}

	/**
	 * Is this a newly deleted fact, pending approval.
	 *
	 * @return boolean
	 */
	public function isPendingDeletion() {
		return $this->pending_deletion;
	}

	/**
	 * This is a newly added fact, pending approval.
	 */
	public function setPendingAddition() {
		$this->pending_addition = true;
		$this->pending_deletion = false;
	}

	/**
	 * Is this a newly added fact, pending approval.
	 *
	 * @return boolean
	 */
	public function isPendingAddition() {
		return $this->pending_addition;
	}

	/**
	 * Source citations linked to this fact
	 *
	 * @return string[]
	 */
	public function getCitations() {
		preg_match_all('/\n(2 SOUR @(' . WT_REGEX_XREF . ')@(?:\n[3-9] .*)*)/', $this->getGedcom(), $matches, PREG_SET_ORDER);
		$citations = array();
		foreach ($matches as $match) {
			$source = WT_Source::getInstance($match[2], $this->getParent()->getGedcomId());
			if ($source->canShow()) {
				$citations[] = $match[1];
			}
		}

		return $citations;
	}

	/**
	 * Notes (inline and objects) linked to this fact
	 *
	 * @return string[]|WT_Note[]
	 */
	public function getNotes() {
		$notes = array();
		preg_match_all('/\n2 NOTE ?(.*(?:\n3.*)*)/', $this->getGedcom(), $matches);
		foreach ($matches[1] as $match) {
			$note = preg_replace("/\n3 CONT ?/", "\n", $match);
			if (preg_match('/@(' . WT_REGEX_XREF . ')@/', $note, $nmatch)) {
				$note = WT_Note::getInstance($nmatch[1], $this->getParent()->getGedcomId());
				if ($note && $note->canShow()) {
					// A note object
					$notes[] = $note;
				}
			} else {
				// An inline note
				$notes[] = $note;
			}
		}

		return $notes;
	}

	/**
	 * Media objects linked to this fact
	 *
	 * @return WT_Media[]
	 */
	public function getMedia() {
		$media = array();
		preg_match_all('/\n2 OBJE @(' . WT_REGEX_XREF . ')@/', $this->getGedcom(), $matches);
		foreach ($matches[1] as $match) {
			$obje = WT_Media::getInstance($match, $this->getParent()->getGedcomId());
			if ($obje->canShow()) {
				$media[] = $obje;
			}
		}

		return $media;
	}

	/**
	 * A one-line summary of the fact - for charts, etc.
	 *
	 * @return string
	 */
	public function summary() {
		global $SHOW_PARENTS_AGE;

		$attributes = array();
		$target     = $this->getTarget();
		if ($target) {
			$attributes[] = $target->getFullName();
		} else {
			$value = $this->getValue();
			if ($value && $value != 'Y') {
				$attributes[] = '<span dir="auto">' . WT_Filter::escapeHtml($value) . '</span>';
			}
			$date = $this->getDate();
			if ($this->getTag() == 'BIRT' && $SHOW_PARENTS_AGE && $this->getParent() instanceof WT_Individual) {
				$attributes[] = $date->display() . format_parents_age($this->getParent(), $date);
			} else {
				$attributes[] = $date->display();
			}
			$place = $this->getPlace()->getShortName();
			if ($place) {
				$attributes[] = $place;
			}
		}
		$html = WT_Gedcom_Tag::getLabelValue($this->getTag(), implode(' — ', $attributes), $this->getParent());
		if ($this->isPendingAddition()) {
			return '<div class="new">' . $html . '</div>';
		} elseif ($this->isPendingDeletion()) {
			return '<div class="old">' . $html . '</div>';
		} else {
			return $html;
		}
	}

	/**
	 * Display an icon for this fact.
	 * Icons are held in a theme subfolder.  Not all themes provide icons.
	 *
	 * @return string
	 */
	public function icon() {
		$icon = 'images/facts/' . $this->getTag() . '.png';
		$dir  = substr(WT_CSS_URL, strlen(WT_STATIC_URL));
		if (file_exists($dir . $icon)) {
			return '<img src="' . WT_CSS_URL . $icon . '" title="' . WT_Gedcom_Tag::getLabel($this->getTag()) . '">';
		} elseif (file_exists($dir . 'images/facts/NULL.png')) {
			// Spacer image - for alignment - until we move to a sprite.
			return '<img src="' . WT_CSS_URL . 'images/facts/NULL.png">';
		} else {
			return '';
		}
	}

	/**
	 * Static Helper functions to sort events
	 *
	 * @param WT_Fact $a Fact one
	 * @param WT_Fact $b Fact two
	 *
	 * @return integer
	 */
	public static function compareDate(WT_Fact $a, WT_Fact $b) {
		if ($a->getDate()->isOK() && $b->getDate()->isOK()) {
			// If both events have dates, compare by date
			$ret = WT_Date::Compare($a->getDate(), $b->getDate());

			if ($ret == 0) {
				// If dates are the same, compare by fact type
				$ret = self::compareType($a, $b);

				// If the fact type is also the same, retain the initial order
				if ($ret == 0) {
					$ret = $a->sortOrder - $b->sortOrder;
				}
			}

			return $ret;
		} else {
			// One or both events have no date - retain the initial order
			return $a->sortOrder - $b->sortOrder;
		}
	}

	/**
	 * Static method to compare two events by their type.
	 *
	 * @param WT_Fact $a Fact one
	 * @param WT_Fact $b Fact two
	 *
	 * @return integer
	 */
	public static function compareType(WT_Fact $a, WT_Fact $b) {
		global $factsort;

		if (empty($factsort)) {
			$factsort = array_flip(
				array(
					'BIRT',
					'_HNM',
					'ALIA', '_AKA', '_AKAN',
					'ADOP', '_ADPF', '_ADPF',
					'_BRTM',
					'CHR', 'BAPM',
					'FCOM',
					'CONF',
					'BARM', 'BASM',
					'EDUC',
					'GRAD',
					'_DEG',
					'EMIG', 'IMMI',
					'NATU',
					'_MILI', '_MILT',
					'ENGA',
					'MARB', 'MARC', 'MARL', '_MARI', '_MBON',
					'MARR', 'MARR_CIVIL', 'MARR_RELIGIOUS', 'MARR_PARTNERS', 'MARR_UNKNOWN', '_COML',
					'_STAT',
					'_SEPR',
					'DIVF',
					'MARS',
					'_BIRT_CHIL',
					'DIV', 'ANUL',
					'_BIRT_', '_MARR_', '_DEAT_', '_BURI_', // other events of close relatives
					'CENS',
					'OCCU',
					'RESI',
					'PROP',
					'CHRA',
					'RETI',
					'FACT', 'EVEN',
					'_NMR', '_NMAR', 'NMR',
					'NCHI',
					'WILL',
					'_HOL',
					'_????_',
					'DEAT',
					'_FNRL', 'CREM', 'BURI', '_INTE',
					'_YART',
					'_NLIV',
					'PROB',
					'TITL',
					'COMM',
					'NATI',
					'CITN',
					'CAST',
					'RELI',
					'SSN', 'IDNO',
					'TEMP',
					'SLGC', 'BAPL', 'CONL', 'ENDL', 'SLGS',
					'ADDR', 'PHON', 'EMAIL', '_EMAIL', 'EMAL', 'FAX', 'WWW', 'URL', '_URL',
					'FILE', // For media objects
					'AFN', 'REFN', '_PRMN', 'REF', 'RIN', '_UID',
					'OBJE', 'NOTE', 'SOUR',
					'CHAN', '_TODO',
				)
			);
		}

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
				$atag = $match[1];
			} else {
				$atag = "_????_";
			}
		}

		if (!array_key_exists($btag, $factsort)) {
			if (preg_match('/^(_(BIRT|MARR|DEAT|BURI)_)/', $btag, $match)) {
				$btag = $match[1];
			} else {
				$btag = "_????_";
			}
		}

		// - Don't let dated after DEAT/BURI facts sort non-dated facts before DEAT/BURI
		// - Treat dated after BURI facts as BURI instead
		if ($a->getAttribute('DATE') != null && $factsort[$atag] > $factsort['BURI'] && $factsort[$atag] < $factsort['CHAN']) {
			$atag = 'BURI';
		}

		if ($b->getAttribute('DATE') != null && $factsort[$btag] > $factsort['BURI'] && $factsort[$btag] < $factsort['CHAN']) {
			$btag = 'BURI';
		}

		$ret = $factsort[$atag] - $factsort[$btag];

		// If facts are the same then put dated facts before non-dated facts
		if ($ret == 0) {
			if ($a->getAttribute('DATE') != null && $b->getAttribute('DATE') == null) {
				return -1;
			}

			if ($b->getAttribute('DATE') != null && $a->getAttribute('DATE') == null) {
				return 1;
			}

			// If no sorting preference, then keep original ordering
			$ret = $a->sortOrder - $b->sortOrder;
		}

		return $ret;
	}

	/**
	 * Allow native PHP functions such as array_unique() to work with objects
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->fact_id . '@' . $this->parent->getXref();
	}
}
