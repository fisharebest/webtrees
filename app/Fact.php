<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Functions\FunctionsPrint;

/**
 * A GEDCOM fact or event object.
 */
class Fact {
	/** @var string Unique identifier for this fact (currently implemented as a hash of the raw data). */
	private $fact_id;

	/** @var GedcomRecord The GEDCOM record from which this fact is taken */
	private $parent;

	/** @var string The raw GEDCOM data for this fact */
	private $gedcom;

	/** @var string The GEDCOM tag for this record */
	private $tag;

	/** @var bool Is this a recently deleted fact, pending approval? */
	private $pending_deletion = false;

	/** @var bool Is this a recently added fact, pending approval? */
	private $pending_addition = false;

	/** @var Date The date of this fact, from the “2 DATE …” attribute */
	private $date;

	/** @var Place The place of this fact, from the “2 PLAC …” attribute */
	private $place;

	/** @var int Temporary(!) variable Used by Functions::sortFacts() */
	public $sortOrder;

	/**
	 * Create an event object from a gedcom fragment.
	 * We need the parent object (to check privacy) and a (pseudo) fact ID to
	 * identify the fact within the record.
	 *
	 * @param string          $gedcom
	 * @param GedcomRecord $parent
	 * @param string          $fact_id
	 *
	 * @throws \InvalidArgumentException
	 */
	public function __construct($gedcom, GedcomRecord $parent, $fact_id) {
		if (preg_match('/^1 (' . WT_REGEX_TAG . ')/', $gedcom, $match)) {
			$this->gedcom  = $gedcom;
			$this->parent  = $parent;
			$this->fact_id = $fact_id;
			$this->tag     = $match[1];
		} else {
			throw new \InvalidArgumentException('Invalid GEDCOM data passed to Fact::_construct(' . $gedcom . ')');
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
	 * @return Individual|Family|Source|Repository|Media|Note|null
	 */
	public function getTarget() {
		$xref = trim($this->getValue(), '@');
		switch ($this->tag) {
		case 'FAMC':
		case 'FAMS':
			return Family::getInstance($xref, $this->getParent()->getTree());
		case 'HUSB':
		case 'WIFE':
		case 'CHIL':
			return Individual::getInstance($xref, $this->getParent()->getTree());
		case 'SOUR':
			return Source::getInstance($xref, $this->getParent()->getTree());
		case 'OBJE':
			return Media::getInstance($xref, $this->getParent()->getTree());
		case 'REPO':
			return Repository::getInstance($xref, $this->getParent()->getTree());
		case 'NOTE':
			return Note::getInstance($xref, $this->getParent()->getTree());
		default:
			return GedcomRecord::getInstance($xref, $this->getParent()->getTree());
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
	 * @param int|null $access_level
	 *
	 * @return bool
	 */
	public function canShow($access_level = null) {
		if ($access_level === null) {
			$access_level = Auth::accessLevel($this->getParent()->getTree());
		}

		// Does this record have an explicit RESN?
		if (strpos($this->gedcom, "\n2 RESN confidential")) {
			return Auth::PRIV_NONE >= $access_level;
		}
		if (strpos($this->gedcom, "\n2 RESN privacy")) {
			return Auth::PRIV_USER >= $access_level;
		}
		if (strpos($this->gedcom, "\n2 RESN none")) {
			return true;
		}

		// Does this record have a default RESN?
		$xref                    = $this->parent->getXref();
		$fact_privacy            = $this->parent->getTree()->getFactPrivacy();
		$individual_fact_privacy = $this->parent->getTree()->getIndividualFactPrivacy();
		if (isset($individual_fact_privacy[$xref][$this->tag])) {
			return $individual_fact_privacy[$xref][$this->tag] >= $access_level;
		}
		if (isset($fact_privacy[$this->tag])) {
			return $fact_privacy[$this->tag] >= $access_level;
		}

		// No restrictions - it must be public
		return true;
	}

	/**
	 * Check whether this fact is protected against edit
	 *
	 * @return bool
	 */
	public function canEdit() {
		// Managers can edit anything
		// Members cannot edit RESN, CHAN and locked records
		return
			$this->parent->canEdit() && !$this->isPendingDeletion() && (
				Auth::isManager($this->parent->getTree()) ||
				Auth::isEditor($this->parent->getTree()) && strpos($this->gedcom, "\n2 RESN locked") === false && $this->getTag() != 'RESN' && $this->getTag() != 'CHAN'
			);
	}

	/**
	 * The place where the event occured.
	 *
	 * @return Place
	 */
	public function getPlace() {
		if ($this->place === null) {
			$this->place = new Place($this->getAttribute('PLAC'), $this->getParent()->getTree());
		}

		return $this->place;
	}

	/**
	 * Get the date for this fact.
	 * We can call this function many times, especially when sorting,
	 * so keep a copy of the date.
	 *
	 * @return Date
	 */
	public function getDate() {
		if ($this->date === null) {
			$this->date = new Date($this->getAttribute('DATE'));
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
	 * The Person/Family record where this Fact came from
	 *
	 * @return Individual|Family|Source|Repository|Media|Note|GedcomRecord
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
				return I18N::translate(Filter::escapeHtml($this->getAttribute('TYPE')));
			}
			// no break - drop into next case
		default:
			return GedcomTag::getLabel($this->tag, $this->parent);
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
	 * @return bool
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
	 * @return bool
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
			$source = Source::getInstance($match[2], $this->getParent()->getTree());
			if ($source->canShow()) {
				$citations[] = $match[1];
			}
		}

		return $citations;
	}

	/**
	 * Notes (inline and objects) linked to this fact
	 *
	 * @return string[]|Note[]
	 */
	public function getNotes() {
		$notes = array();
		preg_match_all('/\n2 NOTE ?(.*(?:\n3.*)*)/', $this->getGedcom(), $matches);
		foreach ($matches[1] as $match) {
			$note = preg_replace("/\n3 CONT ?/", "\n", $match);
			if (preg_match('/@(' . WT_REGEX_XREF . ')@/', $note, $nmatch)) {
				$note = Note::getInstance($nmatch[1], $this->getParent()->getTree());
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
	 * @return Media[]
	 */
	public function getMedia() {
		$media = array();
		preg_match_all('/\n2 OBJE @(' . WT_REGEX_XREF . ')@/', $this->getGedcom(), $matches);
		foreach ($matches[1] as $match) {
			$obje = Media::getInstance($match, $this->getParent()->getTree());
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
		$attributes = array();
		$target     = $this->getTarget();
		if ($target) {
			$attributes[] = $target->getFullName();
		} else {
			$value = $this->getValue();
			if ($value && $value != 'Y') {
				$attributes[] = '<span dir="auto">' . Filter::escapeHtml($value) . '</span>';
			}
			$date = $this->getDate();
			if ($this->getTag() == 'BIRT' && $this->getParent() instanceof Individual && $this->getParent()->getTree()->getPreference('SHOW_PARENTS_AGE')) {
				$attributes[] = $date->display() . FunctionsPrint::formatParentsAges($this->getParent(), $date);
			} else {
				$attributes[] = $date->display();
			}
			$place = $this->getPlace()->getShortName();
			if ($place) {
				$attributes[] = $place;
			}
		}

		$class = 'fact_' . $this->getTag();
		if ($this->isPendingAddition()) {
			$class .= ' new';
		} elseif ($this->isPendingDeletion()) {
			$class .= ' old';
		}

		return
			'<div class="' . $class . '">' .
			/* I18N: a label/value pair, such as “Occupation: Farmer”.  Some languages may need to change the punctuation. */
			I18N::translate('<span class="label">%1$s:</span> <span class="field" dir="auto">%2$s</span>', $this->getLabel(), implode(' — ', $attributes)) .
			'</div>';
	}

	/**
	 * Static Helper functions to sort events
	 *
	 * @param Fact $a Fact one
	 * @param Fact $b Fact two
	 *
	 * @return int
	 */
	public static function compareDate(Fact $a, Fact $b) {
		if ($a->getDate()->isOK() && $b->getDate()->isOK()) {
			// If both events have dates, compare by date
			$ret = Date::compare($a->getDate(), $b->getDate());

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
	 * @param Fact $a Fact one
	 * @param Fact $b Fact two
	 *
	 * @return int
	 */
	public static function compareType(Fact $a, Fact $b) {
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
		if ($a->parent instanceof Family && $b->parent instanceof Family && $a->parent !== $b->parent) {
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
		if ($a->getAttribute('DATE') !== null && $factsort[$atag] > $factsort['BURI'] && $factsort[$atag] < $factsort['CHAN']) {
			$atag = 'BURI';
		}

		if ($b->getAttribute('DATE') !== null && $factsort[$btag] > $factsort['BURI'] && $factsort[$btag] < $factsort['CHAN']) {
			$btag = 'BURI';
		}

		$ret = $factsort[$atag] - $factsort[$btag];

		// If facts are the same then put dated facts before non-dated facts
		if ($ret == 0) {
			if ($a->getAttribute('DATE') !== null && $b->getAttribute('DATE') === null) {
				return -1;
			}

			if ($b->getAttribute('DATE') !== null && $a->getAttribute('DATE') === null) {
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
