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

use Fisharebest\ExtCalendar\GregorianCalendar;
use Fisharebest\Webtrees\GedcomCode\GedcomCodePedi;

/**
 * A GEDCOM individual (INDI) object.
 */
class Individual extends GedcomRecord {
	const RECORD_TYPE = 'INDI';
	const URL_PREFIX  = 'individual.php?pid=';

	/** @var int used in some lists to keep track of this individual’s generation in that list */
	public $generation;

	/** @var Date The estimated date of birth */
	private $_getEstimatedBirthDate;

	/** @var Date The estimated date of death */
	private $_getEstimatedDeathDate;

	/**
	 * Sometimes, we'll know in advance that we need to load a set of records.
	 * Typically when we load families and their members.
	 *
	 * @param Tree  $tree
	 * @param string[] $xrefs
	 */
	public static function load(Tree $tree, array $xrefs) {
		$args = array(
			'tree_id' => $tree->getTreeId(),
		);
		$placeholders = array();

		foreach (array_unique($xrefs) as $n => $xref) {
			if (!isset(self::$gedcom_record_cache[$tree->getTreeId()][$xref])) {
				$placeholders[] = ':x' . $n;
				$args['x' . $n] = $xref;
			}
		}

		if (!empty($placeholders)) {
			$rows = Database::prepare(
				"SELECT i_id AS xref, i_gedcom AS gedcom" .
				" FROM `##individuals`" .
				" WHERE i_file = :tree_id AND i_id IN (" . implode(',', $placeholders) . ")"
			)->execute(
				$args
			)->fetchAll();

			foreach ($rows as $row) {
				self::getInstance($row->xref, $tree, $row->gedcom);
			}
		}
	}

	/**
	 * Can the name of this record be shown?
	 *
	 * @param int|null $access_level
	 *
	 * @return bool
	 */
	public function canShowName($access_level = null) {
		if ($access_level === null) {
			$access_level = Auth::accessLevel($this->tree);
		}

		return $this->tree->getPreference('SHOW_LIVING_NAMES') >= $access_level || $this->canShow($access_level);
	}

	/**
	 * Can this individual be shown?
	 *
	 * @param int $access_level
	 *
	 * @return bool
	 */
	protected function canShowByType($access_level) {
		global $WT_TREE;

		// Dead people...
		if ($this->tree->getPreference('SHOW_DEAD_PEOPLE') >= $access_level && $this->isDead()) {
			$keep_alive             = false;
			$KEEP_ALIVE_YEARS_BIRTH = $this->tree->getPreference('KEEP_ALIVE_YEARS_BIRTH');
			if ($KEEP_ALIVE_YEARS_BIRTH) {
				preg_match_all('/\n1 (?:' . WT_EVENTS_BIRT . ').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $this->gedcom, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$date = new Date($match[1]);
					if ($date->isOK() && $date->gregorianYear() + $KEEP_ALIVE_YEARS_BIRTH > date('Y')) {
						$keep_alive = true;
						break;
					}
				}
			}
			$KEEP_ALIVE_YEARS_DEATH = $this->tree->getPreference('KEEP_ALIVE_YEARS_DEATH');
			if ($KEEP_ALIVE_YEARS_DEATH) {
				preg_match_all('/\n1 (?:' . WT_EVENTS_DEAT . ').*(?:\n[2-9].*)*(?:\n2 DATE (.+))/', $this->gedcom, $matches, PREG_SET_ORDER);
				foreach ($matches as $match) {
					$date = new Date($match[1]);
					if ($date->isOK() && $date->gregorianYear() + $KEEP_ALIVE_YEARS_DEATH > date('Y')) {
						$keep_alive = true;
						break;
					}
				}
			}
			if (!$keep_alive) {
				return true;
			}
		}
		// Consider relationship privacy (unless an admin is applying download restrictions)
		$user_path_length = $this->tree->getUserPreference(Auth::user(), 'RELATIONSHIP_PATH_LENGTH');
		$gedcomid         = $this->tree->getUserPreference(Auth::user(), 'gedcomid');
		if ($gedcomid && $user_path_length && $this->tree->getTreeId() == $WT_TREE->getTreeId() && $access_level = Auth::accessLevel($this->tree)) {
			return self::isRelated($this, $user_path_length);
		}

		// No restriction found - show living people to members only:
		return Auth::PRIV_USER >= $access_level;
	}

	/**
	 * For relationship privacy calculations - is this individual a close relative?
	 *
	 * @param Individual $target
	 * @param int        $distance
	 *
	 * @return bool
	 */
	private static function isRelated(Individual $target, $distance) {
		static $cache = null;

		$user_individual = self::getInstance($target->tree->getUserPreference(Auth::user(), 'gedcomid'), $target->tree);
		if ($user_individual) {
			if (!$cache) {
				$cache = array(
					0 => array($user_individual),
					1 => array(),
				);
				foreach ($user_individual->getFacts('FAM[CS]', false, Auth::PRIV_HIDE) as $fact) {
					$family = $fact->getTarget();
					if ($family) {
						$cache[1][] = $family;
					}
				}
			}
		} else {
			// No individual linked to this account?  Cannot use relationship privacy.
			return true;
		}

		// Double the distance, as we count the INDI-FAM and FAM-INDI links separately
		$distance *= 2;

		// Consider each path length in turn
		for ($n = 0; $n <= $distance; ++$n) {
			if (array_key_exists($n, $cache)) {
				// We have already calculated all records with this length
				if ($n % 2 == 0 && in_array($target, $cache[$n], true)) {
					return true;
				}
			} else {
				// Need to calculate these paths
				$cache[$n] = array();
				if ($n % 2 == 0) {
					// Add FAM->INDI links
					foreach ($cache[$n - 1] as $family) {
						foreach ($family->getFacts('HUSB|WIFE|CHIL', false, Auth::PRIV_HIDE) as $fact) {
							$individual = $fact->getTarget();
							// Don’t backtrack
							if ($individual && !in_array($individual, $cache[$n - 2], true)) {
								$cache[$n][] = $individual;
							}
						}
					}
					if (in_array($target, $cache[$n], true)) {
						return true;
					}
				} else {
					// Add INDI->FAM links
					foreach ($cache[$n - 1] as $individual) {
						foreach ($individual->getFacts('FAM[CS]', false, Auth::PRIV_HIDE) as $fact) {
							$family = $fact->getTarget();
							// Don’t backtrack
							if ($family && !in_array($family, $cache[$n - 2], true)) {
								$cache[$n][] = $family;
							}
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Generate a private version of this record
	 *
	 * @param int $access_level
	 *
	 * @return string
	 */
	protected function createPrivateGedcomRecord($access_level) {
		$SHOW_PRIVATE_RELATIONSHIPS = $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

		$rec = '0 @' . $this->xref . '@ INDI';
		if ($this->tree->getPreference('SHOW_LIVING_NAMES') >= $access_level) {
			// Show all the NAME tags, including subtags
			foreach ($this->getFacts('NAME') as $fact) {
				$rec .= "\n" . $fact->getGedcom();
			}
		}
		// Just show the 1 FAMC/FAMS tag, not any subtags, which may contain private data
		preg_match_all('/\n1 (?:FAMC|FAMS) @(' . WT_REGEX_XREF . ')@/', $this->gedcom, $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$rela = Family::getInstance($match[1], $this->tree);
			if ($rela && ($SHOW_PRIVATE_RELATIONSHIPS || $rela->canShow($access_level))) {
				$rec .= $match[0];
			}
		}
		// Don’t privatize sex.
		if (preg_match('/\n1 SEX [MFU]/', $this->gedcom, $match)) {
			$rec .= $match[0];
		}

		return $rec;
	}

	/**
	 * Fetch data from the database
	 *
	 * @param string $xref
	 * @param int    $tree_id
	 *
	 * @return null|string
	 */
	protected static function fetchGedcomRecord($xref, $tree_id) {
		return Database::prepare(
			"SELECT i_gedcom FROM `##individuals` WHERE i_id = :xref AND i_file = :tree_id"
		)->execute(array(
			'xref'    => $xref,
			'tree_id' => $tree_id,
		))->fetchOne();
	}

	/**
	 * Static helper function to sort an array of people by birth date
	 *
	 * @param Individual $x
	 * @param Individual $y
	 *
	 * @return int
	 */
	public static function compareBirthDate(Individual $x, Individual $y) {
		return Date::compare($x->getEstimatedBirthDate(), $y->getEstimatedBirthDate());
	}

	/**
	 * Static helper function to sort an array of people by death date
	 *
	 * @param Individual $x
	 * @param Individual $y
	 *
	 * @return int
	 */
	public static function compareDeathDate(Individual $x, Individual $y) {
		return Date::compare($x->getEstimatedDeathDate(), $y->getEstimatedDeathDate());
	}

	/**
	 * Calculate whether this individual is living or dead.
	 * If not known to be dead, then assume living.
	 *
	 * @return bool
	 */
	public function isDead() {
		$MAX_ALIVE_AGE = $this->tree->getPreference('MAX_ALIVE_AGE');

		// "1 DEAT Y" or "1 DEAT/2 DATE" or "1 DEAT/2 PLAC"
		if (preg_match('/\n1 (?:' . WT_EVENTS_DEAT . ')(?: Y|(?:\n[2-9].+)*\n2 (DATE|PLAC) )/', $this->gedcom)) {
			return true;
		}

		// If any event occured more than $MAX_ALIVE_AGE years ago, then assume the individual is dead
		if (preg_match_all('/\n2 DATE (.+)/', $this->gedcom, $date_matches)) {
			foreach ($date_matches[1] as $date_match) {
				$date = new Date($date_match);
				if ($date->isOK() && $date->maximumJulianDay() <= WT_CLIENT_JD - 365 * $MAX_ALIVE_AGE) {
					return true;
				}
			}
			// The individual has one or more dated events.  All are less than $MAX_ALIVE_AGE years ago.
			// If one of these is a birth, the individual must be alive.
			if (preg_match('/\n1 BIRT(?:\n[2-9].+)*\n2 DATE /', $this->gedcom)) {
				return false;
			}
		}

		// If we found no conclusive dates then check the dates of close relatives.

		// Check parents (birth and adopted)
		foreach ($this->getChildFamilies(Auth::PRIV_HIDE) as $family) {
			foreach ($family->getSpouses(Auth::PRIV_HIDE) as $parent) {
				// Assume parents are no more than 45 years older than their children
				preg_match_all('/\n2 DATE (.+)/', $parent->gedcom, $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date = new Date($date_match);
					if ($date->isOK() && $date->maximumJulianDay() <= WT_CLIENT_JD - 365 * ($MAX_ALIVE_AGE + 45)) {
						return true;
					}
				}
			}
		}

		// Check spouses
		foreach ($this->getSpouseFamilies(Auth::PRIV_HIDE) as $family) {
			preg_match_all('/\n2 DATE (.+)/', $family->gedcom, $date_matches);
			foreach ($date_matches[1] as $date_match) {
				$date = new Date($date_match);
				// Assume marriage occurs after age of 10
				if ($date->isOK() && $date->maximumJulianDay() <= WT_CLIENT_JD - 365 * ($MAX_ALIVE_AGE - 10)) {
					return true;
				}
			}
			// Check spouse dates
			$spouse = $family->getSpouse($this);
			if ($spouse) {
				preg_match_all('/\n2 DATE (.+)/', $spouse->gedcom, $date_matches);
				foreach ($date_matches[1] as $date_match) {
					$date = new Date($date_match);
					// Assume max age difference between spouses of 40 years
					if ($date->isOK() && $date->maximumJulianDay() <= WT_CLIENT_JD - 365 * ($MAX_ALIVE_AGE + 40)) {
						return true;
					}
				}
			}
			// Check child dates
			foreach ($family->getChildren(Auth::PRIV_HIDE) as $child) {
				preg_match_all('/\n2 DATE (.+)/', $child->gedcom, $date_matches);
				// Assume children born after age of 15
				foreach ($date_matches[1] as $date_match) {
					$date = new Date($date_match);
					if ($date->isOK() && $date->maximumJulianDay() <= WT_CLIENT_JD - 365 * ($MAX_ALIVE_AGE - 15)) {
						return true;
					}
				}
				// Check grandchildren
				foreach ($child->getSpouseFamilies(Auth::PRIV_HIDE) as $child_family) {
					foreach ($child_family->getChildren(Auth::PRIV_HIDE) as $grandchild) {
						preg_match_all('/\n2 DATE (.+)/', $grandchild->gedcom, $date_matches);
						// Assume grandchildren born after age of 30
						foreach ($date_matches[1] as $date_match) {
							$date = new Date($date_match);
							if ($date->isOK() && $date->maximumJulianDay() <= WT_CLIENT_JD - 365 * ($MAX_ALIVE_AGE - 30)) {
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
	 * Find the highlighted media object for an individual
	 * 1. Ignore all media objects that are not displayable because of Privacy rules
	 * 2. Ignore all media objects with the Highlight option set to "N"
	 * 3. Pick the first media object that matches these criteria, in order of preference:
	 *    (a) Level 1 object with the Highlight option set to "Y"
	 *    (b) Level 1 object with the Highlight option missing or set to other than "Y" or "N"
	 *    (c) Level 2 or higher object with the Highlight option set to "Y"
	 *
	 * @return null|Media
	 */
	public function findHighlightedMedia() {
		$objectA = null;
		$objectB = null;
		$objectC = null;

		// Iterate over all of the media items for the individual
		preg_match_all('/\n(\d) OBJE @(' . WT_REGEX_XREF . ')@/', $this->getGedcom(), $matches, PREG_SET_ORDER);
		foreach ($matches as $match) {
			$media = Media::getInstance($match[2], $this->tree);
			if (!$media || !$media->canShow() || $media->isExternal()) {
				continue;
			}
			$level = $match[1];
			$prim  = $media->isPrimary();
			if ($prim == 'N') {
				continue;
			}
			if ($level == 1) {
				if ($prim == 'Y') {
					if (empty($objectA)) {
						$objectA = $media;
					}
				} else {
					if (empty($objectB)) {
						$objectB = $media;
					}
				}
			} else {
				if ($prim == 'Y') {
					if (empty($objectC)) {
						$objectC = $media;
					}
				}
			}
		}

		if ($objectA) {
			return $objectA;
		}
		if ($objectB) {
			return $objectB;
		}
		if ($objectC) {
			return $objectC;
		}

		return null;
	}

	/**
	 * Display the prefered image for this individual.
	 * Use an icon if no image is available.
	 *
	 * @return string
	 */
	public function displayImage() {
		$media = $this->findHighlightedMedia();
		if ($media) {
			// Thumbnail exists - use it.
			return $media->displayImage();
		} elseif ($this->tree->getPreference('USE_SILHOUETTE')) {
			// No thumbnail exists - use an icon
			return '<i class="icon-silhouette-' . $this->getSex() . '"></i>';
		} else {
			return '';
		}
	}

	/**
	 * Get the date of birth
	 *
	 * @return Date
	 */
	public function getBirthDate() {
		foreach ($this->getAllBirthDates() as $date) {
			if ($date->isOK()) {
				return $date;
			}
		}

		return new Date('');
	}

	/**
	 * Get the place of birth
	 *
	 * @return string
	 */
	public function getBirthPlace() {
		foreach ($this->getAllBirthPlaces() as $place) {
			if ($place) {
				return $place;
			}
		}

		return '';
	}

	/**
	 * Get the year of birth
	 *
	 * @return string the year of birth
	 */
	public function getBirthYear() {
		return $this->getBirthDate()->minimumDate()->format('%Y');
	}

	/**
	 * Get the date of death
	 *
	 * @return Date
	 */
	public function getDeathDate() {
		foreach ($this->getAllDeathDates() as $date) {
			if ($date->isOK()) {
				return $date;
			}
		}

		return new Date('');
	}

	/**
	 * Get the place of death
	 *
	 * @return string
	 */
	public function getDeathPlace() {
		foreach ($this->getAllDeathPlaces() as $place) {
			if ($place) {
				return $place;
			}
		}

		return '';
	}

	/**
	 * get the death year
	 *
	 * @return string the year of death
	 */
	public function getDeathYear() {
		return $this->getDeathDate()->minimumDate()->format('%Y');
	}

	/**
	 * Get the range of years in which a individual lived.  e.g. “1870–”, “1870–1920”, “–1920”.
	 * Provide the full date using a tooltip.
	 * For consistent layout in charts, etc., show just a “–” when no dates are known.
	 * Note that this is a (non-breaking) en-dash, and not a hyphen.
	 *
	 * @return string
	 */
	public function getLifeSpan() {
		return
			/* I18N: A range of years, e.g. “1870–”, “1870–1920”, “–1920” */ I18N::translate(
				'%1$s–%2$s',
				'<span title="' . strip_tags($this->getBirthDate()->display()) . '">' . $this->getBirthDate()->minimumDate()->format('%Y') . '</span>',
				'<span title="' . strip_tags($this->getDeathDate()->display()) . '">' . $this->getDeathDate()->minimumDate()->format('%Y') . '</span>'
			);
	}

	/**
	 * Get all the birth dates - for the individual lists.
	 *
	 * @return Date[]
	 */
	public function getAllBirthDates() {
		foreach (explode('|', WT_EVENTS_BIRT) as $event) {
			$tmp = $this->getAllEventDates($event);
			if ($tmp) {
				return $tmp;
			}
		}

		return array();
	}

	/**
	 * Gat all the birth places - for the individual lists.
	 *
	 * @return string[]
	 */
	public function getAllBirthPlaces() {
		foreach (explode('|', WT_EVENTS_BIRT) as $event) {
			$tmp = $this->getAllEventPlaces($event);
			if ($tmp) {
				return $tmp;
			}
		}

		return array();
	}

	/**
	 * Get all the death dates - for the individual lists.
	 *
	 * @return Date[]
	 */
	public function getAllDeathDates() {
		foreach (explode('|', WT_EVENTS_DEAT) as $event) {
			$tmp = $this->getAllEventDates($event);
			if ($tmp) {
				return $tmp;
			}
		}

		return array();
	}

	/**
	 * Get all the death places - for the individual lists.
	 *
	 * @return string[]
	 */
	public function getAllDeathPlaces() {
		foreach (explode('|', WT_EVENTS_DEAT) as $event) {
			$tmp = $this->getAllEventPlaces($event);
			if ($tmp) {
				return $tmp;
			}
		}

		return array();
	}

	/**
	 * Generate an estimate for the date of birth, based on dates of parents/children/spouses
	 *
	 * @return Date
	 */
	public function getEstimatedBirthDate() {
		if (is_null($this->_getEstimatedBirthDate)) {
			foreach ($this->getAllBirthDates() as $date) {
				if ($date->isOK()) {
					$this->_getEstimatedBirthDate = $date;
					break;
				}
			}
			if (is_null($this->_getEstimatedBirthDate)) {
				$min = array();
				$max = array();
				$tmp = $this->getDeathDate();
				if ($tmp->isOK()) {
					$min[] = $tmp->minimumJulianDay() - $this->tree->getPreference('MAX_ALIVE_AGE') * 365;
					$max[] = $tmp->maximumJulianDay();
				}
				foreach ($this->getChildFamilies() as $family) {
					$tmp = $family->getMarriageDate();
					if ($tmp->isOK()) {
						$min[] = $tmp->maximumJulianDay() - 365 * 1;
						$max[] = $tmp->minimumJulianDay() + 365 * 30;
					}
					if ($parent = $family->getHusband()) {
						$tmp = $parent->getBirthDate();
						if ($tmp->isOK()) {
							$min[] = $tmp->maximumJulianDay() + 365 * 15;
							$max[] = $tmp->minimumJulianDay() + 365 * 65;
						}
					}
					if ($parent = $family->getWife()) {
						$tmp = $parent->getBirthDate();
						if ($tmp->isOK()) {
							$min[] = $tmp->maximumJulianDay() + 365 * 15;
							$max[] = $tmp->minimumJulianDay() + 365 * 45;
						}
					}
					foreach ($family->getChildren() as $child) {
						$tmp = $child->getBirthDate();
						if ($tmp->isOK()) {
							$min[] = $tmp->maximumJulianDay() - 365 * 30;
							$max[] = $tmp->minimumJulianDay() + 365 * 30;
						}
					}
				}
				foreach ($this->getSpouseFamilies() as $family) {
					$tmp = $family->getMarriageDate();
					if ($tmp->isOK()) {
						$min[] = $tmp->maximumJulianDay() - 365 * 45;
						$max[] = $tmp->minimumJulianDay() - 365 * 15;
					}
					$spouse = $family->getSpouse($this);
					if ($spouse) {
						$tmp = $spouse->getBirthDate();
						if ($tmp->isOK()) {
							$min[] = $tmp->maximumJulianDay() - 365 * 25;
							$max[] = $tmp->minimumJulianDay() + 365 * 25;
						}
					}
					foreach ($family->getChildren() as $child) {
						$tmp = $child->getBirthDate();
						if ($tmp->isOK()) {
							$min[] = $tmp->maximumJulianDay() - 365 * ($this->getSex() == 'F' ? 45 : 65);
							$max[] = $tmp->minimumJulianDay() - 365 * 15;
						}
					}
				}
				if ($min && $max) {
					$gregorian_calendar = new GregorianCalendar;

					list($year)                   = $gregorian_calendar->jdToYmd((int) ((max($min) + min($max)) / 2));
					$this->_getEstimatedBirthDate = new Date('EST ' . $year);
				} else {
					$this->_getEstimatedBirthDate = new Date(''); // always return a date object
				}
			}
		}

		return $this->_getEstimatedBirthDate;
	}

	/**
	 * Generate an estimated date of death.
	 *
	 * @return Date
	 */
	public function getEstimatedDeathDate() {
		if ($this->_getEstimatedDeathDate === null) {
			foreach ($this->getAllDeathDates() as $date) {
				if ($date->isOK()) {
					$this->_getEstimatedDeathDate = $date;
					break;
				}
			}
			if ($this->_getEstimatedDeathDate === null) {
				if ($this->getEstimatedBirthDate()->minimumJulianDay()) {
					$this->_getEstimatedDeathDate = $this->getEstimatedBirthDate()->addYears($this->tree->getPreference('MAX_ALIVE_AGE'), 'BEF');
				} else {
					$this->_getEstimatedDeathDate = new Date(''); // always return a date object
				}
			}
		}

		return $this->_getEstimatedDeathDate;
	}

	/**
	 * Get the sex - M F or U
	 * Use the un-privatised gedcom record.  We call this function during
	 * the privatize-gedcom function, and we are allowed to know this.
	 *
	 * @return string
	 */
	public function getSex() {
		if (preg_match('/\n1 SEX ([MF])/', $this->gedcom . $this->pending, $match)) {
			return $match[1];
		} else {
			return 'U';
		}
	}

	/**
	 * Get the individual’s sex image
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function getSexImage($size = 'small') {
		return self::sexImage($this->getSex(), $size);
	}

	/**
	 * Generate a sex icon/image
	 *
	 * @param string $sex
	 * @param string $size
	 *
	 * @return string
	 */
	public static function sexImage($sex, $size = 'small') {
		return '<i class="icon-sex_' . strtolower($sex) . '_' . ($size == 'small' ? '9x9' : '15x15') . '"></i>';
	}

	/**
	 * Generate the CSS class to be used for drawing this individual
	 *
	 * @return string
	 */
	public function getBoxStyle() {
		$tmp = array('M' => '', 'F' => 'F', 'U' => 'NN');

		return 'person_box' . $tmp[$this->getSex()];
	}

	/**
	 * Get a list of this individual’s spouse families
	 *
	 * @param int|null $access_level
	 *
	 * @return Family[]
	 */
	public function getSpouseFamilies($access_level = null) {
		if ($access_level === null) {
			$access_level = Auth::accessLevel($this->tree);
		}

		$SHOW_PRIVATE_RELATIONSHIPS = $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

		$families = array();
		foreach ($this->getFacts('FAMS', false, $access_level, $SHOW_PRIVATE_RELATIONSHIPS) as $fact) {
			$family = $fact->getTarget();
			if ($family && ($SHOW_PRIVATE_RELATIONSHIPS || $family->canShow($access_level))) {
				$families[] = $family;
			}
		}

		return $families;
	}

	/**
	 * Get the current spouse of this individual.
	 *
	 * Where an individual has multiple spouses, assume they are stored
	 * in chronological order, and take the last one found.
	 *
	 * @return Individual|null
	 */
	public function getCurrentSpouse() {
		$tmp    = $this->getSpouseFamilies();
		$family = end($tmp);
		if ($family) {
			return $family->getSpouse($this);
		} else {
			return null;
		}
	}

	/**
	 * Count the children belonging to this individual.
	 *
	 * @return int
	 */
	public function getNumberOfChildren() {
		if (preg_match('/\n1 NCHI (\d+)(?:\n|$)/', $this->getGedcom(), $match)) {
			return $match[1];
		} else {
			$children = array();
			foreach ($this->getSpouseFamilies() as $fam) {
				foreach ($fam->getChildren() as $child) {
					$children[$child->getXref()] = true;
				}
			}

			return count($children);
		}
	}

	/**
	 * Get a list of this individual’s child families (i.e. their parents).
	 *
	 * @param int|null $access_level
	 *
	 * @return Family[]
	 */
	public function getChildFamilies($access_level = null) {
		if ($access_level === null) {
			$access_level = Auth::accessLevel($this->tree);
		}

		$SHOW_PRIVATE_RELATIONSHIPS = $this->tree->getPreference('SHOW_PRIVATE_RELATIONSHIPS');

		$families = array();
		foreach ($this->getFacts('FAMC', false, $access_level, $SHOW_PRIVATE_RELATIONSHIPS) as $fact) {
			$family = $fact->getTarget();
			if ($family && ($SHOW_PRIVATE_RELATIONSHIPS || $family->canShow($access_level))) {
				$families[] = $family;
			}
		}

		return $families;
	}

	/**
	 * Get the preferred parents for this individual.
	 *
	 * An individual may multiple parents (e.g. birth, adopted, disputed).
	 * The preferred family record is:
	 * (a) the first one with an explicit tag "_PRIMARY Y"
	 * (b) the first one with a pedigree of "birth"
	 * (c) the first one with no pedigree (default is "birth")
	 * (d) the first one found
	 *
	 * @return Family|null
	 */
	public function getPrimaryChildFamily() {
		$families = $this->getChildFamilies();
		switch (count($families)) {
		case 0:
			return null;
		case 1:
			return reset($families);
		default:
			// If there is more than one FAMC record, choose the preferred parents:
			// a) records with '2 _PRIMARY'
			foreach ($families as $famid => $fam) {
				if (preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 _PRIMARY Y)/", $this->getGedcom())) {
					return $fam;
				}
			}
			// b) records with '2 PEDI birt'
			foreach ($families as $famid => $fam) {
				if (preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 PEDI birth)/", $this->getGedcom())) {
					return $fam;
				}
			}
			// c) records with no '2 PEDI'
			foreach ($families as $famid => $fam) {
				if (!preg_match("/\n1 FAMC @{$famid}@\n(?:[2-9].*\n)*(?:2 PEDI)/", $this->getGedcom())) {
					return $fam;
				}
			}

			// d) any record
			return reset($families);
		}
	}

	/**
	 * Get a list of step-parent families.
	 *
	 * @return Family[]
	 */
	public function getChildStepFamilies() {
		$step_families = array();
		$families      = $this->getChildFamilies();
		foreach ($families as $family) {
			$father = $family->getHusband();
			if ($father) {
				foreach ($father->getSpouseFamilies() as $step_family) {
					if (!in_array($step_family, $families, true)) {
						$step_families[] = $step_family;
					}
				}
			}
			$mother = $family->getWife();
			if ($mother) {
				foreach ($mother->getSpouseFamilies() as $step_family) {
					if (!in_array($step_family, $families, true)) {
						$step_families[] = $step_family;
					}
				}
			}
		}

		return $step_families;
	}

	/**
	 * Get a list of step-parent families.
	 *
	 * @return Family[]
	 */
	public function getSpouseStepFamilies() {
		$step_families = array();
		$families      = $this->getSpouseFamilies();
		foreach ($families as $family) {
			$spouse = $family->getSpouse($this);
			if ($spouse) {
				foreach ($family->getSpouse($this)->getSpouseFamilies() as $step_family) {
					if (!in_array($step_family, $families, true)) {
						$step_families[] = $step_family;
					}
				}
			}
		}

		return $step_families;
	}

	/**
	 * A label for a parental family group
	 *
	 * @param Family $family
	 *
	 * @return string
	 */
	public function getChildFamilyLabel(Family $family) {
		if (preg_match('/\n1 FAMC @' . $family->getXref() . '@(?:\n[2-9].*)*\n2 PEDI (.+)/', $this->getGedcom(), $match)) {
			// A specified pedigree
			return GedcomCodePedi::getChildFamilyLabel($match[1]);
		} else {
			// Default (birth) pedigree
			return GedcomCodePedi::getChildFamilyLabel('');
		}
	}

	/**
	 * Create a label for a step family
	 *
	 * @param Family $step_family
	 *
	 * @return string
	 */
	public function getStepFamilyLabel(Family $step_family) {
		foreach ($this->getChildFamilies() as $family) {
			if ($family !== $step_family) {
				// Must be a step-family
				foreach ($family->getSpouses() as $parent) {
					foreach ($step_family->getSpouses() as $step_parent) {
						if ($parent === $step_parent) {
							// One common parent - must be a step family
							if ($parent->getSex() == 'M') {
								// Father’s family with someone else
								if ($step_family->getSpouse($step_parent)) {
									return
										/* I18N: A step-family.  %s is an individual’s name */
										I18N::translate('Father’s family with %s', $step_family->getSpouse($step_parent)->getFullName());
								} else {
									return
										/* I18N: A step-family. */
										I18N::translate('Father’s family with an unknown individual');
								}
							} else {
								// Mother’s family with someone else
								if ($step_family->getSpouse($step_parent)) {
									return
										/* I18N: A step-family.  %s is an individual’s name */
										I18N::translate('Mother’s family with %s', $step_family->getSpouse($step_parent)->getFullName());
								} else {
									return
										/* I18N: A step-family. */
										I18N::translate('Mother’s family with an unknown individual');
								}
							}
						}
					}
				}
			}
		}

		// Perahps same parents - but a different family record?
		return I18N::translate('Family with parents');
	}

	/**
	 * get primary parents names for this individual
	 *
	 * @param string $classname optional css class
	 * @param string $display   optional css style display
	 *
	 * @return string a div block with father & mother names
	 */
	public function getPrimaryParentsNames($classname = '', $display = '') {
		$fam = $this->getPrimaryChildFamily();
		if (!$fam) {
			return '';
		}
		$txt = '<div';
		if ($classname) {
			$txt .= " class=\"$classname\"";
		}
		if ($display) {
			$txt .= " style=\"display:$display\"";
		}
		$txt .= '>';
		$husb = $fam->getHusband();
		if ($husb) {
			// Temporarily reset the 'prefered' display name, as we always
			// want the default name, not the one selected for display on the indilist.
			$primary = $husb->getPrimaryName();
			$husb->setPrimaryName(null);
			$txt .=
				/* I18N: %s is the name of an individual’s father */
				I18N::translate('Father: %s', $husb->getFullName()) . '<br>';
			$husb->setPrimaryName($primary);
		}
		$wife = $fam->getWife();
		if ($wife) {
			// Temporarily reset the 'prefered' display name, as we always
			// want the default name, not the one selected for display on the indilist.
			$primary = $wife->getPrimaryName();
			$wife->setPrimaryName(null);
			$txt .=
				/* I18N: %s is the name of an individual’s mother */
				I18N::translate('Mother: %s', $wife->getFullName());
			$wife->setPrimaryName($primary);
		}
		$txt .= '</div>';

		return $txt;
	}

	/** {@inheritdoc} */
	public function getFallBackName() {
		return '@P.N. /@N.N./';
	}

	/**
	 * Convert a name record into ‘full’ and ‘sort’ versions.
	 * Use the NAME field to generate the ‘full’ version, as the
	 * gedcom spec says that this is the individual’s name, as they would write it.
	 * Use the SURN field to generate the sortable names.  Note that this field
	 * may also be used for the ‘true’ surname, perhaps spelt differently to that
	 * recorded in the NAME field. e.g.
	 *
	 * 1 NAME Robert /de Gliderow/
	 * 2 GIVN Robert
	 * 2 SPFX de
	 * 2 SURN CLITHEROW
	 * 2 NICK The Bald
	 *
	 * full=>'Robert de Gliderow 'The Bald''
	 * sort=>'CLITHEROW, ROBERT'
	 *
	 * Handle multiple surnames, either as;
	 *
	 * 1 NAME Carlos /Vasquez/ y /Sante/
	 * or
	 * 1 NAME Carlos /Vasquez y Sante/
	 * 2 GIVN Carlos
	 * 2 SURN Vasquez,Sante
	 *
	 * @param string $type
	 * @param string $full
	 * @param string $gedcom
	 */
	protected function addName($type, $full, $gedcom) {
		////////////////////////////////////////////////////////////////////////////
		// Extract the structured name parts - use for "sortable" names and indexes
		////////////////////////////////////////////////////////////////////////////

		$sublevel = 1 + (int) $gedcom[0];
		$NPFX     = preg_match("/\n{$sublevel} NPFX (.+)/", $gedcom, $match) ? $match[1] : '';
		$GIVN     = preg_match("/\n{$sublevel} GIVN (.+)/", $gedcom, $match) ? $match[1] : '';
		$SURN     = preg_match("/\n{$sublevel} SURN (.+)/", $gedcom, $match) ? $match[1] : '';
		$NSFX     = preg_match("/\n{$sublevel} NSFX (.+)/", $gedcom, $match) ? $match[1] : '';
		$NICK     = preg_match("/\n{$sublevel} NICK (.+)/", $gedcom, $match) ? $match[1] : '';

		// SURN is an comma-separated list of surnames...
		if ($SURN) {
			$SURNS = preg_split('/ *, */', $SURN);
		} else {
			$SURNS = array();
		}
		// ...so is GIVN - but nobody uses it like that
		$GIVN = str_replace('/ *, */', ' ', $GIVN);

		////////////////////////////////////////////////////////////////////////////
		// Extract the components from NAME - use for the "full" names
		////////////////////////////////////////////////////////////////////////////

		// Fix bad slashes.  e.g. 'John/Smith' => 'John/Smith/'
		if (substr_count($full, '/') % 2 == 1) {
			$full = $full . '/';
		}

		// GEDCOM uses "//" to indicate an unknown surname
		$full = preg_replace('/\/\//', '/@N.N./', $full);

		// Extract the surname.
		// Note, there may be multiple surnames, e.g. Jean /Vasquez/ y /Cortes/
		if (preg_match('/\/.*\//', $full, $match)) {
			$surname = str_replace('/', '', $match[0]);
		} else {
			$surname = '';
		}

		// If we don’t have a SURN record, extract it from the NAME
		if (!$SURNS) {
			if (preg_match_all('/\/([^\/]*)\//', $full, $matches)) {
				// There can be many surnames, each wrapped with '/'
				$SURNS = $matches[1];
				foreach ($SURNS as $n => $SURN) {
					// Remove surname prefixes, such as "van de ", "d'" and "'t " (lower case only)
					$SURNS[$n] = preg_replace('/^(?:[a-z]+ |[a-z]+\' ?|\'[a-z]+ )+/', '', $SURN);
				}
			} else {
				// It is valid not to have a surname at all
				$SURNS = array('');
			}
		}

		// If we don’t have a GIVN record, extract it from the NAME
		if (!$GIVN) {
			$GIVN = preg_replace(
				array(
					'/ ?\/.*\/ ?/', // remove surname
					'/ ?".+"/', // remove nickname
					'/ {2,}/', // multiple spaces, caused by the above
					'/^ | $/', // leading/trailing spaces, caused by the above
				),
				array(
					' ',
					' ',
					' ',
					'',
				),
				$full
			);
		}

		// Add placeholder for unknown given name
		if (!$GIVN) {
			$GIVN = '@P.N.';
			$pos  = strpos($full, '/');
			$full = substr($full, 0, $pos) . '@P.N. ' . substr($full, $pos);
		}

		// GEDCOM nicknames should be specificied in a NICK field, or in the
		// NAME filed, surrounded by ASCII quotes (or both).
		if ($NICK && strpos($full, '"' . $NICK . '"') === false) {
			// A NICK field is present, but not included in the NAME.
			$pos = strpos($full, '/');
			if ($pos === false) {
				// No surname - just append it
				$full .= ' "' . $NICK . '"';
			} else {
				// Insert before surname
				$full = substr($full, 0, $pos) . '"' . $NICK . '" ' . substr($full, $pos);
			}
		}

		// Remove slashes - they don’t get displayed
		// $fullNN keeps the @N.N. placeholders, for the database
		// $full is for display on-screen
		$fullNN = str_replace('/', '', $full);

		// Insert placeholders for any missing/unknown names
		$full = str_replace('@N.N.', I18N::translateContext('Unknown surname', '…'), $full);
		$full = str_replace('@P.N.', I18N::translateContext('Unknown given name', '…'), $full);
		// Localise quotation marks around the nickname
		$full = preg_replace_callback('/"([^"]*)"/', function ($matches) { return I18N::translate('“%s”', $matches[1]); }, $full);
		// Format for display
		$full = '<span class="NAME" dir="auto" translate="no">' . preg_replace('/\/([^\/]*)\//', '<span class="SURN">$1</span>', Filter::escapeHtml($full)) . '</span>';

		// A suffix of “*” indicates a preferred name
		$full = preg_replace('/([^ >]*)\*/', '<span class="starredname">\\1</span>', $full);

		// Remove prefered-name indicater - they don’t go in the database
		$GIVN   = str_replace('*', '', $GIVN);
		$fullNN = str_replace('*', '', $fullNN);

		foreach ($SURNS as $SURN) {
			// Scottish 'Mc and Mac ' prefixes both sort under 'Mac'
			if (strcasecmp(substr($SURN, 0, 2), 'Mc') == 0) {
				$SURN = substr_replace($SURN, 'Mac', 0, 2);
			} elseif (strcasecmp(substr($SURN, 0, 4), 'Mac ') == 0) {
				$SURN = substr_replace($SURN, 'Mac', 0, 4);
			}

			$this->_getAllNames[] = array(
				'type'    => $type,
				'sort'    => $SURN . ',' . $GIVN,
				'full'    => $full, // This is used for display
				'fullNN'  => $fullNN, // This goes into the database
				'surname' => $surname, // This goes into the database
				'givn'    => $GIVN, // This goes into the database
				'surn'    => $SURN, // This goes into the database
			);
		}
	}

	/**
	 * Extract names from the GEDCOM record.
	 */
	public function extractNames() {
		$this->extractNamesFromFacts(1, 'NAME', $this->getFacts('NAME', false, Auth::accessLevel($this->tree), $this->canShowName()));
	}

	/**
	 * Extra info to display when displaying this record in a list of
	 * selection items or favorites.
	 *
	 * @return string
	 */
	public function formatListDetails() {
		return
			$this->formatFirstMajorFact(WT_EVENTS_BIRT, 1) .
			$this->formatFirstMajorFact(WT_EVENTS_DEAT, 1);
	}

	/**
	 * Create a short name for compact display on charts
	 *
	 * @return string
	 */
	public function getShortName() {
		global $bwidth;

		// Estimate number of characters that can fit in box. Calulates to 28 characters in webtrees theme, or 34 if no thumbnail used.
		if ($this->tree->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
			$char = intval(($bwidth - 40) / 6.5);
		} else {
			$char = ($bwidth / 6.5);
		}
		if ($this->canShowName()) {
			$tmp        = $this->getAllNames();
			$givn       = $tmp[$this->getPrimaryName()]['givn'];
			$surn       = $tmp[$this->getPrimaryName()]['surname'];
			$new_givn   = explode(' ', $givn);
			$count_givn = count($new_givn);
			$len_givn   = mb_strlen($givn);
			$len_surn   = mb_strlen($surn);
			$len        = $len_givn + $len_surn;
			$i          = 1;
			while ($len > $char && $i <= $count_givn) {
				$new_givn[$count_givn - $i] = mb_substr($new_givn[$count_givn - $i], 0, 1);
				$givn                       = implode(' ', $new_givn);
				$len_givn                   = mb_strlen($givn);
				$len                        = $len_givn + $len_surn;
				$i++;
			}
			$max_surn = $char - $i * 2;
			if ($len_surn > $max_surn) {
				$surn = substr($surn, 0, $max_surn) . '…';
			}
			$shortname = str_replace(
				array('@P.N.', '@N.N.'),
				array(I18N::translateContext('Unknown given name', '…'), I18N::translateContext('Unknown surname', '…')),
				$givn . ' ' . $surn
			);

			return $shortname;
		} else {
			return I18N::translate('Private');
		}
	}
}
