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
namespace Fisharebest\Webtrees\Controller;

use Fisharebest\Webtrees\ColorGenerator;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\FrenchDate;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Date\HijriDate;
use Fisharebest\Webtrees\Date\JalaliDate;
use Fisharebest\Webtrees\Date\JewishDate;
use Fisharebest\Webtrees\Date\JulianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Session;

/**
 * Controller for the timeline chart
 */
class LifespanController extends PageController {
	// Base color parameters
	const RANGE           = 120; // degrees
	const SATURATION      = 100; // percent
	const LIGHTNESS       = 30;  // percent
	const ALPHA           = 0.25;
	const CHART_TOP       = 10; // pixels
	const BAR_SPACING     = 22; // pixels
	const YEAR_SPAN       = 10; // Number of years per scale section
	const PIXELS_PER_YEAR = 7;  // Number of pixels to shift per year
	const SESSION_DATA    = 'lifespan_data';

	/** @var string|null Chart parameter */
	public $place     = null;

	/** @var int|null Chart parameter */
	public $beginYear = null;

	/** @var int|null Chart parameter */
	public $endYear   = null;

	/** @var string Chart parameter */
	public $subtitle  = '&nbsp;';

	/** @var Individual[] A list of individuals to display. */
	private $people = array();

	/** @var string The default calendar to use. */
	private $defaultCalendar;

	/** @var string Which calendar to use. */
	private $calendar;

	/** @var string Which calendar escape to use. */
	private $calendarEscape;

	/** @var int The earliest year to show. */
	private $timelineMinYear;

	/** @var int That latest year to show. */
	private $timelineMaxYear;

	/** @var int The current year. */
	private $currentYear;

	/** @var string[] A list of colors to use. */
	private $colors = array();

	/** @todo This attribute is public to support the PHP5.3 closure workaround. */
	/** @var Place|null A place to serarh. */
	public $place_obj = null;

	/** @todo This attribute is public to support the PHP5.3 closure workaround. */
	/** @var Date|null Start of the date range. */
	public $startDate = null;

	/** @todo This attribute is public to support the PHP5.3 closure workaround. */
	/** @var Date|null End of the date range. */
	public $endDate = null;

	/** @var bool Only match dates in the chosen calendar. */
	private $strictDate;

	/** @todo This attribute is public to support the PHP5.3 closure workaround. */
	/** @var string[] List of facts/events to include. */
	public $facts;

	/** @var string[] Facts and events to exclude from the chart */
	private $nonfacts = array(
		'FAMS', 'FAMC', 'MAY', 'BLOB', 'OBJE', 'SEX', 'NAME', 'SOUR', 'NOTE', 'BAPL', 'ENDL',
		'SLGC', 'SLGS', '_TODO', '_WT_OBJE_SORT', 'CHAN', 'HUSB', 'WIFE', 'CHIL', 'OCCU', 'ASSO',
	);

	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();
		$this->setPageTitle(I18N::translate('Lifespans'));

		$this->facts           = explode('|', WT_EVENTS_BIRT . '|' . WT_EVENTS_DEAT . '|' . WT_EVENTS_MARR . '|' . WT_EVENTS_DIV);
		$tmp                   = explode('\\', get_class(I18N::defaultCalendar()));
		$cal                   = strtolower(array_pop($tmp));
		$this->defaultCalendar = str_replace('calendar', '', $cal);
		$filterPids            = false;

		// Request parameters
		$clear            = Filter::getBool('clear');
		$newpid           = Filter::get('newpid', WT_REGEX_XREF);
		$addfam           = Filter::getBool('addFamily');
		$this->place      = Filter::get('place');
		$this->beginYear  = Filter::getInteger('beginYear', 0, PHP_INT_MAX, null);
		$this->endYear    = Filter::getInteger('endYear', 0, PHP_INT_MAX, null);
		$this->calendar   = Filter::get('calendar', null, $this->defaultCalendar);
		$this->strictDate = Filter::getBool('strictDate');

		// Set up base color parameters
		$this->colors['M'] = new ColorGenerator(240, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE * -1);
		$this->colors['F'] = new ColorGenerator(000, self::SATURATION, self::LIGHTNESS, self::ALPHA, self::RANGE);

		// Build a list of people based on the input parameters
		if ($clear) {
			// Empty list & reset form
			$xrefs           = array();
			$this->place     = null;
			$this->beginYear = null;
			$this->endYear   = null;
			$this->calendar  = $this->defaultCalendar;
		} elseif ($this->place) {
			// Get all individual & family records found for a place
			$this->place_obj = new Place($this->place, $WT_TREE);
			$xrefs           = Database::prepare(
				"SELECT DISTINCT `i_id` FROM `##placelinks`" .
				" JOIN `##individuals` ON `pl_gid`=`i_id` AND `pl_file`=`i_file`" .
				" WHERE `i_file`=:tree_id" .
				" AND `pl_p_id`=:place_id" .
				" UNION" .
				" SELECT DISTINCT `f_id` FROM `##placelinks`" .
				" JOIN `##families` ON `pl_gid`=`f_id` AND `pl_file`=`f_file`" .
				" WHERE `f_file`=:tree_id" .
				" AND `pl_p_id`=:place_id"
			)->execute(array(
				'tree_id'  => $WT_TREE->getTreeId(),
				'place_id' => $this->place_obj->getPlaceId(),
			))->fetchOneColumn();
		} else {
			// Modify an existing list of records
			$xrefs = Session::get(self::SESSION_DATA, array());
			if ($newpid) {
				$xrefs = array_merge($xrefs, $this->addFamily(Individual::getInstance($newpid, $WT_TREE), $addfam));
				$xrefs = array_unique($xrefs);
			} elseif (!$xrefs) {
				$xrefs = $this->addFamily($this->getSignificantIndividual(), false);
			}
		}

		$tmp               = $this->getCalendarDate(unixtojd());
		$this->currentYear = $tmp->today()->y;

		$tmp = strtoupper(strtr($this->calendar,
			array('jewish' => 'hebrew',
			      'french' => 'french r',
			)));
		$this->calendarEscape = sprintf('@#D%s@', $tmp);

		if ($xrefs) {
			// ensure date ranges are valid in preparation for filtering list
			if ($this->beginYear || $this->endYear) {
				$filterPids = true;
				if (!$this->beginYear) {
					$tmp             = new Date($this->calendarEscape . ' 1');
					$this->beginYear = $tmp->minimumDate()->y;
				}
				if (!$this->endYear) {
					$this->endYear = $this->currentYear;
				}
				$this->startDate = new Date($this->calendarEscape . $this->beginYear);
				$this->endDate   = new Date($this->calendarEscape . $this->endYear);
			}

			// Test each xref to see if the search criteria are met
			foreach ($xrefs as $key => $xref) {
				$valid  = false;
				$person = Individual::getInstance($xref, $WT_TREE);
				if ($person) {
					if ($person->canShow()) {
						foreach ($person->getFacts() as $fact) {
							if ($this->checkFact($fact)) {
								$this->people[] = $person;
								$valid          = true;
								break;
							}
						}
					}
				} else {
					$family = Family::getInstance($xref, $WT_TREE);
					if ($family && $family->canShow() && $this->checkFact($family->getMarriage())) {
						$valid          = true;
						$this->people[] = $family->getHusband();
						$this->people[] = $family->getWife();
					}
				}
				if (!$valid) {
					unset($xrefs[$key]); // no point in storing a xref if we can't use it
				}
			}
			Session::put(self::SESSION_DATA, $xrefs);
		} else {
			Session::forget(self::SESSION_DATA);
		}

		$this->people = array_filter(array_unique($this->people));
		$count        = count($this->people);
		if ($count) {
			// Build the subtitle
			if ($this->place && $filterPids) {
				$this->subtitle = I18N::plural(
					'%s individual with events in %s between %s and %s',
					'%s individuals with events in %s between %s and %s',
					$count, I18N::number($count),
					$this->place, $this->startDate->display(false, '%Y'), $this->endDate->display(false, '%Y')
				);
			} elseif ($this->place) {
				$this->subtitle = I18N::plural(
					'%s individual with events in %s',
					'%s individuals with events in %s',
					$count, I18N::number($count),
					$this->place
				);
			} elseif ($filterPids) {
				$this->subtitle = I18N::plural(
					'%s individual with events between %s and %s',
					'%s individuals with events between %s and %s',
					$count, I18N::number($count),
					$this->startDate->display(false, '%Y'), $this->endDate->display(false, '%Y')
				);
			} else {
				$this->subtitle = I18N::plural(
					'%s individual',
					'%s individuals',
					$count, I18N::number($count));
			}

			// Sort the array in order of birth year
			usort($this->people, function (Individual $a, Individual $b) {
				return Date::compare($a->getEstimatedBirthDate(), $b->getEstimatedBirthDate());
			});

			//Find the mimimum birth year and maximum death year from the individuals in the array.
			$bdate   = $this->getCalendarDate($this->people[0]->getEstimatedBirthDate()->minimumJulianDay());
			$minyear = $bdate->y;

			$that    = $this; // PHP5.3 cannot access $this inside a closure
			$maxyear = array_reduce($this->people, function ($carry, Individual $item) use ($that) {
				$date = $that->getCalendarDate($item->getEstimatedDeathDate()->maximumJulianDay());

				return max($carry, $date->y);
			}, 0);
		} elseif ($filterPids) {
			$minyear = $this->endYear;
			$maxyear = $this->endYear;
		} else {
			$minyear = $this->currentYear;
			$maxyear = $this->currentYear;
		}

		$maxyear = min($maxyear, $this->currentYear); // Limit maximum year to current year as we can't forecast the future
		$minyear = min($minyear, $maxyear - $WT_TREE->getPreference('MAX_ALIVE_AGE')); // Set default minimum chart length

		$this->timelineMinYear = (int) floor($minyear / 10) * 10; // round down to start of the decade
		$this->timelineMaxYear = (int) ceil($maxyear / 10) * 10; // round up to start of next decade
	}

	/**
	 * Add a person (and optionally their immediate family members) to the pids array
	 *
	 * @param Individual $person
	 * @param bool $add_family
	 *
	 * @return array
	 */
	private function addFamily(Individual $person, $add_family) {
		$xrefs   = array();
		$xrefs[] = $person->getXref();
		if ($add_family) {
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$xrefs[] = $spouse->getXref();
					foreach ($family->getChildren() as $child) {
						$xrefs[] = $child->getXref();
					}
				}
			}
			foreach ($person->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					$xrefs[] = $parent->getXref();
				}
				foreach ($family->getChildren() as $sibling) {
					if ($person !== $sibling) {
						$xrefs[] = $sibling->getXref();
					}
				}
			}
		}

		return $xrefs;
	}

	/**
	 * Prints the time line scale
	 */
	public function printTimeline() {
		$startYear = $this->timelineMinYear;
		while ($startYear < $this->timelineMaxYear) {
			$date = new Date($this->calendarEscape . $startYear);
			echo $date->display(false, '%Y', false);
			$startYear += self::YEAR_SPAN;
		}
	}

	/**
	 * Populate the timeline
	 *
	 * @return int
	 */
	public function fillTimeline() {
		$rows = array();
		$maxY = self::CHART_TOP;
		//base case
		if (!$this->people) {
			return $maxY;
		}

		foreach ($this->people as $person) {

			$bdate     = $this->getCalendarDate($person->getEstimatedBirthDate()->minimumJulianDay());
			$ddate     = $this->getCalendarDate($person->getEstimatedDeathDate()->maximumJulianDay());
			$birthYear = $bdate->y;
			$age       = min($ddate->y, $this->currentYear) - $birthYear; // truncate the bar at the current year
			$width     = max(9, $age * self::PIXELS_PER_YEAR); // min width is width of sex icon
			$startPos  = ($birthYear - $this->timelineMinYear) * self::PIXELS_PER_YEAR;

			//-- calculate a good Y top value
			$Y     = self::CHART_TOP;
			$ready = false;
			while (!$ready) {
				if (!isset($rows[$Y])) {
					$ready          = true;
					$rows[$Y]['x1'] = $startPos;
					$rows[$Y]['x2'] = $startPos + $width;
				} else {
					if ($rows[$Y]['x1'] > $startPos + $width) {
						$ready          = true;
						$rows[$Y]['x1'] = $startPos;
					} elseif ($rows[$Y]['x2'] < $startPos) {
						$ready          = true;
						$rows[$Y]['x2'] = $startPos + $width;
					} else {
						//move down a line
						$Y += self::BAR_SPACING;
					}
				}
			}

			$facts = $person->getFacts();
			foreach ($person->getSpouseFamilies() as $family) {
				foreach ($family->getFacts() as $fact) {
					$facts[] = $fact;
				}
			}
			Functions::sortFacts($facts);

			$that          = $this; // PHP5.3 cannot access $this inside a closure
			$acceptedFacts = array_filter($facts, function (Fact $fact) use ($that) {
				return (in_array($fact->getTag(), $that->facts) && $fact->getDate()->isOK()) ||
				       (($that->place_obj || $that->startDate) && $that->checkFact($fact));
			});

			$eventList = array();
			foreach ($acceptedFacts as $fact) {
				$tag = $fact->getTag();
				//-- if the fact is a generic EVENt then get the qualifying TYPE
				if ($tag == "EVEN") {
					$tag = $fact->getAttribute('TYPE');
				}
				$eventList[] = array(
					'label' => GedcomTag::getLabel($tag),
					'date'  => $fact->getDate()->display(),
					'place' => $fact->getPlace()->getFullName(),
				);
			}
			$direction  = I18N::direction() === 'ltr' ? 'left' : 'right';
			$lifespan   = ' ' . $person->getLifeSpan(); // put the space here so its included in the length calcs
			$sex        = $person->getSex();
			$popupClass = strtr($sex, array('M' => '', 'U' => 'NN'));
			$color      = $sex === 'U' ? '' : sprintf("background-color: %s", $this->colors[$sex]->getNextColor());

			// following lines are a nasty method of approximating
			// the width of a string in pixels from the character count
			$name_length       = mb_strlen(strip_tags($person->getFullName())) * 6.5;
			$short_name_length = mb_strlen(strip_tags($person->getShortName())) * 6.5;
			$lifespan_length   = mb_strlen(strip_tags($lifespan)) * 6.5;

			if ($width > $name_length + $lifespan_length) {
				$printName    = $person->getFullName();
				$abbrLifespan = $lifespan;
			} elseif ($width > $name_length) {
				$printName    = $person->getFullName();
				$abbrLifespan = '&hellip;';
			} elseif ($width > $short_name_length) {
				$printName    = $person->getShortName();
				$abbrLifespan = '';
			} else {
				$printName    = '';
				$abbrLifespan = '';
			}

			// Bar framework
			printf('
                <div class="person_box%s" style="top:%spx; %s:%spx; width:%spx; %s">
                        <div class="itr">%s %s %s
                            <div class="popup person_box%s">
                                <div>
                                    <a href="%s">%s%s</a>
                                </div>',
				$popupClass, $Y, $direction, $startPos, $width, $color,
				$person->getSexImage(), $printName, $abbrLifespan,
				$popupClass,
				$person->getHtmlUrl(), $person->getFullName(), $lifespan
			);

			// Add events to popup
			foreach ($eventList as $event) {
				printf("<div>%s: %s %s</div>", $event['label'], $event['date'], $event['place']);
			}
			echo
				'</div>' . // class="popup"
				'</div>' .  // class="itr"
				'</div>';   // class=$popupclass

			$maxY = max($maxY, $Y);
		}

		return $maxY;
	}

	/**
	 * Function checkFact
	 *
	 * Does this fact meet the search criteria?
	 *
	 * @todo This function is public to support the PHP5.3 closure workaround.
	 *
	 * @param  Fact $fact
	 *
	 * @return bool
	 */
	public function checkFact(Fact $fact) {
		$valid = !in_array($fact->getTag(), $this->nonfacts);
		if ($valid && $this->place_obj) {
			$valid = stripos($fact->getPlace()->getGedcomName(), $this->place_obj->getGedcomName()) !== false;
		}
		if ($valid && $this->startDate) {
			if ($this->strictDate && $this->calendar !== $this->defaultCalendar) {
				$valid = stripos($fact->getAttribute('DATE'), $this->calendar) !== false;
			}
			if ($valid) {
				$date  = $fact->getDate();
				$valid = $date->isOK() && Date::compare($date, $this->startDate) >= 0 && Date::compare($date, $this->endDate) <= 0;
			}
		}

		return $valid;
	}

	/**
	 * Function getCalendarDate
	 *
	 * @todo This function is public to support the PHP5.3 closure workaround.
	 *
	 * @param int $date
	 *
	 * @return object
	 */
	public function getCalendarDate($date) {
		switch ($this->calendar) {
			case 'julian':
				$caldate = new JulianDate($date);
				break;
			case 'french':
				$caldate = new FrenchDate($date);
				break;
			case 'jewish':
				$caldate = new JewishDate($date);
				break;
			case 'hijri':
				$caldate = new HijriDate($date);
				break;
			case 'jalali':
				$caldate = new JalaliDate($date);
				break;
			default:
				$caldate = new GregorianDate($date);
		}

		return $caldate;
	}

	/**
	 * Function getCalendarOptionList
	 *
	 * @return string
	 */
	public function getCalendarOptionList() {
		$html = '';
		foreach (Date::calendarNames() as $calendar => $name) {
			$selected = $this->calendar === $calendar ? 'selected' : '';
			$html .= sprintf('<option dir="auto" value="%s" %s>%s</option>', $calendar, $selected, $name);
		}

		return $html;
	}
}
