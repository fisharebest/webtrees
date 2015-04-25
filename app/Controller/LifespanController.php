<?php
namespace Fisharebest\Webtrees;

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

/**
 * Class LifespanController - Controller for the timeline chart
 */
class LifespanController extends PageController {

	// Base color parameters
	const range        = 120;   // degrees
	const saturation   = 100;   // percent
	const lightness    = 30;    // percent
	const alpha        = 0.25;
	
	const chartTop             = 10; // px
	const barSpacing           = 22; // px
	const yearSpan             = 10; // No. years per scale section
	const pixelsPerYear        = 7;  // how many pixels to shift per year
	const extCalendarNamespace = 'Fisharebest\\ExtCalendar\\';

	public $place     = '';
	public $beginYear = null;
	public $endYear   = null;
	public $count     = 0;

	private $people = array();
	private $calendar;
	private $timelineMinYear;
	private $timelineMaxYear;
	private $currentYear;
	private $colors = array();
	private $nonfacts = array(
		'FAMS', 'FAMC', 'MAY', 'BLOB', 'OBJE', 'SEX', 'NAME', 'SOUR', 'NOTE', 'BAPL', 'ENDL', 'SLGC', 'SLGS', '_TODO', '_WT_OBJE_SORT', 'CHAN', 'HUSB', 'WIFE', 'CHIL', 'BURI', 'OCCU', 'ASSO'
	);

	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_SESSION, $WT_TREE;

		parent::__construct();
		$this->setPageTitle(I18N::translate('Lifespans'));

		// Request parameters
		$clear           = Filter::getBool('clear');
		$addfam          = Filter::postBool('addFamily');
		$newpid          = Filter::post('newpid', WT_REGEX_XREF);
		$this->beginYear = Filter::postInteger('beginYear', 0, PHP_INT_MAX, null);
		$this->endYear   = Filter::postInteger('endYear', 0, PHP_INT_MAX, null);
		$this->place     = Filter::post('place');
		$calendar        = Filter::post('calendar');

		$new_person = Individual::getInstance($newpid, $WT_TREE);
		// Set up base color parameters
		// 0Deg = Red, 120Deg = green, 240Deg = blue
		$this->colors['M'] = new colorGenerator(240, self::saturation, self::lightness, self::alpha, self::range * -1);
		$this->colors['F'] = new colorGenerator(0, self::saturation, self::lightness, self::alpha, self::range);

		if (!$calendar) {
			$this->calendar = I18N::defaultCalendar();
		} else {
			$calendarClass  = sprintf('%s%sCalendar', self::extCalendarNamespace, $calendar);
			$this->calendar = new $calendarClass;
		}
		$tmp               = new Date($this->calendar->gedcomCalendarEscape() . date('c'));
		$this->currentYear = $tmp->maximumDate()->y;

		// Build a list of people based on the input parameters
		if ($clear) {
			// Empty list
			$pids = array();
		} elseif ($this->place) {
			// All records found in a place
			$wt_place = new Place($this->place, $WT_TREE);
			$pids     = Database::prepare(
				"SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id = :place_id AND pl_file = :tree_id"
			)->execute(array(
				'place_id' => $wt_place->getPlaceId(),
				'tree_id'  => $WT_TREE->getTreeId()
			))->fetchOneColumn();
		} elseif ($this->beginYear !== null && $this->endYear !== null) {
			// All records in date range
			$startDate = new Date($this->calendar->gedcomCalendarEscape() . $this->beginYear);
			$endDate   = new Date($this->calendar->gedcomCalendarEscape() . $this->endYear);
			$pids      = Database::prepare(
				"SELECT DISTINCT i_id AS xref" .
				" FROM `##individuals`" .
				" JOIN `##dates` ON i_id=d_gid AND i_file=d_file" .
				" WHERE i_file=:tree_id AND d_julianday1 BETWEEN :start_date AND :end_date"
			)->execute(array(
				'tree_id'    => $WT_TREE->getTreeId(),
				'start_date' => $startDate->minimumJulianDay(),
				'end_date'   => $endDate->maximumJulianDay()
			))->fetchOneColumn();
		} else {
			// Modify an existing list of records
			if ($WT_SESSION->timeline_pids) {
				$pids = $WT_SESSION->timeline_pids;
			} else {
				$pids = array();
			}
			if ($new_person) {
				$pids = array_merge($pids, $this->addFamily($new_person, $addfam));
			} elseif (!$pids) {
				$pids = $this->addFamily($this->getSignificantIndividual(), false);
			}

		}
		//-- cleanup user input
		$pids                      = array_unique($pids); //removes duplicates
		$WT_SESSION->timeline_pids = $pids;

		foreach ($pids as $xref) {
			$person = Individual::getInstance($xref, $WT_TREE);
			// list of linked records includes families as well as individuals.
			if ($person) {
				$bdate = $person->getEstimatedBirthDate();
				if ($bdate->isOK() && $person->canShow()) {
					$this->people[] = $person;
				}
			}
		}

		if ($this->people) {
			$this->count = count($this->people);
			// Sort the array in order of birth year
			usort($this->people, function (Individual $a, Individual $b) {
				return Date::compare($a->getEstimatedBirthDate(), $b->getEstimatedBirthDate());
			});
			//Find the mimimum birth year and maximum death year from the individuals in the array.
			$bdate   = $this->people[0]->getEstimatedBirthDate();
			$minyear = $bdate->minimumDate()->y;

			$maxyear = array_reduce($this->people, function ($carry, $item) {
				$date = $item->getEstimatedDeathDate();
				return max($carry, $date->maximumDate()->y);
			}, 0);
		} else {
			$minyear = $this->currentYear;
			$maxyear = $this->currentYear;
		}

		$maxyear = min($maxyear, $this->currentYear); // Limit maximum year to current year as we can't forecast the future
		$minyear = min($minyear, $maxyear - $WT_TREE->getPreference('MAX_ALIVE_AGE')); // Set default minimum chart length

		$this->timelineMinYear = (int)floor($minyear / 10) * 10; // round down to start of the decade
		$this->timelineMaxYear = (int) ceil($maxyear / 10) * 10; // round up to start of next decade
	}

	/**
	 * Add a person (and optionally their immediate family members) to the pids array
	 *
	 * @param Individual $person
	 * @param boolean $add_family
	 * @return array
	 */
	private function addFamily(Individual $person, $add_family) {
		$pids   = array();
		$pids[] = $person->getXref();
		if ($add_family) {
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$pids[] = $spouse->getXref();
					foreach ($family->getChildren() as $child) {
						$pids[] = $child->getXref();
					}
				}
			}
			foreach ($person->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					$pids[] = $parent->getXref();
				}
				foreach ($family->getChildren() as $sibling) {
					if ($person !== $sibling) {
						$pids[] = $sibling->getXref();
					}
				}
			}
		}
		return $pids;
	}

	/**
	 * Prints the time line scale
	 *
	 */
	public function printTimeline() {
		$startYear = $this->timelineMinYear;
		while ($startYear < $this->timelineMaxYear) {
			$date = new Date($this->calendar->gedcomCalendarEscape() . $startYear);
			echo $date->display(false, '%Y', false);
			$startYear += self::yearSpan;
		}
	}

	/**
	 * Populate the timeline
	 *
	 * @return integer
	 */
	public function fillTimeline() {

		$rows = array();
		$maxY = self::chartTop;
		//base case
		if (!$this->people) {
			return $maxY;
		}

		foreach ($this->people as $person) {

			$bdate     = $person->getEstimatedBirthDate();
			$ddate     = $person->getEstimatedDeathDate();
			$birthYear = $bdate->minimumDate()->y;
			$length    = min($ddate->maximumDate()->y, $this->currentYear) - $birthYear; // truncate the bar at the current year
			$width     = max(9, $length * self::pixelsPerYear); // min width is width of sex icon
			$startPos  = ($birthYear - $this->timelineMinYear) * self::pixelsPerYear;

			//-- calculate a good Y top value
			$Y     = self::chartTop;
			$ready = false;
			while (!$ready) {
				if (!isset($rows[$Y])) {
					$ready          = true;
					$rows[$Y]["x1"] = $startPos;
					$rows[$Y]["x2"] = $startPos + $width;
				} else {
					if ($rows[$Y]["x1"] > $startPos + $width) {
						$ready          = true;
						$rows[$Y]["x1"] = $startPos;
					} elseif ($rows[$Y]["x2"] < $startPos) {
						$ready          = true;
						$rows[$Y]["x2"] = $startPos + $width;
					} else {
						//move down a line
						$Y += self::barSpacing;
					}
				}
			}

			$facts = $person->getFacts();
			foreach ($person->getSpouseFamilies() as $family) {
				foreach ($family->getFacts() as $fact) {
					$facts[] = $fact;
				}
			}
			sort_facts($facts);

			$acceptedFacts = array_filter($facts, function ($item) {
				return !in_array($item->getTag(), $this->nonfacts) && $item->getDate()->isOK();
			});

			$eventList = array();
			foreach ($acceptedFacts as $val) {
				$tag = $val->getTag();
				//-- if the fact is a generic EVENt then get the qualifying TYPE
				if ($tag == "EVEN") {
					$tag = $val->getAttribute('TYPE');
				}
				$eventList[] = array('label' => GedcomTag::getLabel($tag),
				                     'date'  => $val->getDate()->display(),
				                     'place' => $val->getPlace()->getFullName()
				);
			}

			$direction  = I18N::direction() === 'ltr' ? 'left' : 'right';
			$lifespan   = ' ' . $person->getLifeSpan(); // put the space here so its included in the length calcs
			$sex        = $person->getSex();
			$popupClass = "person_box" . strtr($sex, array('M' => '', 'U' => 'NN'));
			$color = $sex === 'U' ? '' : sprintf("background-color: %s", $this->colors[$sex]->getNextColor());

			// following lines are a nasty method of approximating
			// the width of a string in pixels from the character count
			$name_length     = mb_strlen(strip_tags($person->getFullName())) * 7;
			$lifespan_length = mb_strlen(strip_tags($lifespan)) * 7;

			if ($width > $name_length + $lifespan_length) {
				$printName    = $person->getFullName();
				$abbrLifespan = $lifespan;
			} elseif ($width > $name_length) {
				$printName    = $person->getFullName();
				$abbrLifespan = '&hellip;';
			} elseif ($width > 50) {
				$printName    = $person->getShortName();
				$abbrLifespan = '';
			} else {
				$printName    = '';
				$abbrLifespan = '';
			}

			printf("<div class='%s' style='top:%spx; %s:%spx; width:%spx; %s'>",
			       $popupClass, $Y, $direction, $startPos, $width, $color);

			printf("<div class='itr'>%s %s %s<div class='popup %s'><div><a href='%s'>%s%s</a></div>",
			       $person->getSexImage(), $printName, $abbrLifespan, $popupClass, $person->getHtmlUrl(), $person->getFullName(), $lifespan);

			foreach ($eventList as $event) {
				printf("<div>%s: %s %s</div>", $event['label'], $event['date'], $event['place']);
			}
			echo "</div>" . // class='popup'
				"</div>" .  // class='itr'
				"</div>";   // class=$popupclass

			$maxY = max($maxY, $Y);
		}

		return $maxY;
	}

	public function getCalendarOptionList() {
		$html    = '';
		$default = get_class($this->calendar);
		foreach (array('Arabic', 'French', 'Gregorian', 'Jewish', 'Persian') as $calendar) {
			$selected = stripos($default, $calendar) !== false ? 'selected' : '';
			$html .= sprintf("<option dir='auto' value='%s' %s>%s</option>", $calendar, $selected, I18N::translate($calendar));
		}
		return $html;
	}
}

/**
 * Class colorGenerator
 * @package Fisharebest\Webtrees
  */
class colorGenerator {

	private $hue;
	private $basehue;
	private $saturation;
	private $lightness;
	private $baselightness;
	private $alpha;
	private $range;

	/**
	 * @param integer $hue
	 * @param integer $saturation
	 * @param integer $lightness
	 * @param integer $alpha
	 * @param integer $range
	 */
	public function __construct($hue, $saturation, $lightness, $alpha, $range) {
		$this->hue           = $hue;
		$this->basehue       = $hue;
		$this->saturation    = $saturation;
		$this->lightness     = $lightness;
		$this->baselightness = $lightness;
		$this->alpha         = $alpha;
		$this->range         = $range;
	}

	/**
	 * Function getNextColor
	 *
	 * $lightness cycles between baselightness and 100% in $lightnessStep steps
	 * $hue cycles between $basehue and $basehue + $range degrees in $hueStep degrees
	 * on each complete $lightness cycle
	 *
	 * @param int $lightnessStep
	 * @param int $hueStep
	 * @return string
	 */
	public function getNextColor($lightnessStep = 10, $hueStep = 15) {
		$lightness = $this->lightness + $lightnessStep;
		$hue       = $this->hue;

		if ($lightness >= 100) {
			$lightness = $this->baselightness;
			$hue += $hueStep * (abs($this->range) / $this->range);
			if (($hue - $this->basehue) * ($hue - ($this->basehue + $this->range)) >= 0) {
				$hue = $this->basehue;
			}
			$this->hue = $hue;
		}
		$this->lightness = $lightness;

		return sprintf("hsla(%s, %s%%, %s%%, %s)",
		               $this->hue,
		               $this->saturation,
		               $this->lightness,
		               $this->alpha);
	}

}
