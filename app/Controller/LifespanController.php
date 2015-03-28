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

	const baseHueAngle   = 15;
	const baseSaturation = 100;
	const baseLightness  = 30;
	const baseLuminance  = 0.25;
	const chartTop       = 10; // px
	const barSpacing     = 25; // px
	const yearSpan       = 5;  // No. years per scale section
	const pixelsPerYear  = 10; // how many pixels to shift per year

	public $people    = array();
	public $place     = '';
	public $beginYear = 0;
	public $endYear   = 0;
	
	private $pids = array();
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

		// Set up base color parameters
		$angle = 0; // 0Deg = Red, 120Deg = green, 240Deg = blue
		foreach (array('F', 'U', 'M') as $sex) {
			$tmp                = new \stdClass();
			$tmp->luminance     = $sex === 'U' ? 0 : self::baseLuminance;
			$tmp->saturation    = self::baseSaturation;
			$tmp->lightness     = self::baseLightness;
			$tmp->baseHue       = $angle;
			$tmp->currentHue    = $angle;
			$this->colors[$sex] = $tmp;
			$angle += 120;
		}

		$this->currentYear = (int)date('Y');

		// Request parameters
		$clear     = Filter::getBool('clear');
		$addfam    = Filter::getBool('addFamily');
		$newpid    = Filter::get('newpid', WT_REGEX_XREF);
		$place     = Filter::get('place');
		$beginYear = Filter::getInteger('beginYear', 0, $this->currentYear + 100, 0);
		$endYear   = Filter::getInteger('endYear', 0, $this->currentYear + 100, 0);

		$new_person = Individual::getInstance($newpid, $WT_TREE);

		if ($clear) {
			// Empty list
			$this->pids = array();
		} elseif ($place) {
			// All records found in a place
			$wt_place    = new Place($place, $WT_TREE);
			$this->pids  = Database::prepare(
				"SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id = :place_id AND pl_file = :tree_id"
			)->execute(array(
				'place_id'=>$wt_place->getPlaceId(),
				'tree_id'=>$WT_TREE->getTreeId()
			 ))->fetchOneColumn();
			$this->place = $place;
		} else {
			// Modify an existing list of records
			if (is_array($WT_SESSION->timeline_pids)) {
				$this->pids = $WT_SESSION->timeline_pids;
			} else {
				$this->pids = array();
			}
			if ($new_person) {
				$this->addFamily($new_person, $addfam);
			} elseif (!$this->pids) {
				$this->addFamily($this->getSignificantIndividual(), false);
			}
		}
		$WT_SESSION->timeline_pids = $this->pids;

		$this->beginYear = $beginYear;
		$this->endYear   = $endYear;
		if ($beginYear == 0 || $endYear == 0) {
			//-- cleanup user input
			$this->pids = array_unique($this->pids); //removes duplicates
			foreach ($this->pids as $key => $value) {
				$this->pids[$key] = $value;
				$person           = Individual::getInstance($value, $WT_TREE);
				// list of linked records includes families as well as individuals.
				if ($person) {
					$bdate = $person->getEstimatedBirthDate();
					if ($bdate->isOK() && $person->canShow()) {
						$this->people[] = $person;
					}
				}
			}
		} else {
			//--Finds if the begin year and end year textboxes are not empty
			//-- reset the people array when doing a year range search
			$this->people = array();
			//Takes the begining year and end year passed by the postback and modifies them and uses them to populate
			//the time line

			//Variables to restrict the person boxes to the year searched.
			//--Searches for individuals who had an event between the beginning and end years
			$indis = self::searchIndividualsInYearRange($beginYear, $endYear);
			//--Populates an array of people that had an event within those years

			foreach ($indis as $person) {
				if (empty($searchplace) || in_array($person->getXref(), $this->pids)) {
					$bdate = $person->getEstimatedBirthDate();
					if ($bdate->isOK() && $person->canShow()) {
						$this->people[] = $person;
					}
				}
			}
			$WT_SESSION->timeline_pids = null;
		}

		$minChartLength = $WT_TREE->getPreference('MAX_ALIVE_AGE'); // Ensure the minimum chart span is this long
		// Set starting values for the timeline length
		$this->timelineMinYear = $this->currentYear - $minChartLength;
		$this->timelineMaxYear = 0;

		if ($this->people) {
			// Sort the array in order of birth year
			uasort($this->people, function (Individual $a, Individual $b) {
				return Date::compare($a->getEstimatedBirthDate(), $b->getEstimatedBirthDate());
			});

			//Find the maximum death year and mimimum birth year from the individuals returned in the array.
			foreach ($this->people as $value) {
				$bdate                 = $value->getEstimatedBirthDate();
				$ddate                 = $value->getEstimatedDeathDate();
				$this->timelineMinYear = min($this->timelineMinYear, $bdate->minimumDate()->y);
				$this->timelineMaxYear = max($this->timelineMaxYear, $ddate->maximumDate()->y);

			}
			$this->timelineMaxYear = min($this->timelineMaxYear, $this->currentYear);
			$this->timelineMinYear = min($this->timelineMinYear, $this->timelineMaxYear - $minChartLength);
		} else {
			$this->timelineMaxYear = $this->currentYear;
		}
		$this->timelineMinYear = (int)floor($this->timelineMinYear / 5) * 5; // round down to multiple of 5
		$this->timelineMaxYear = (int)ceil($this->timelineMaxYear / 5) * 5; // round up to multiple of 5
	}

	/**
	 * Add a person (and optionally their immediate family members) to the pids array
	 *
	 * @param Individual $person
	 * @param boolean $add_family
	 */
	private function addFamily(Individual $person, $add_family) {
		$this->pids[] = $person->getXref();
		if ($add_family) {
			foreach ($person->getSpouseFamilies() as $family) {
				$spouse = $family->getSpouse($person);
				if ($spouse) {
					$this->pids[] = $spouse->getXref();
					foreach ($family->getChildren() as $child) {
						$this->pids[] = $child->getXref();
					}
				}
			}
			foreach ($person->getChildFamilies() as $family) {
				foreach ($family->getSpouses() as $parent) {
					$this->pids[] = $parent->getXref();
				}
				foreach ($family->getChildren() as $sibling) {
					if ($person !== $sibling) {
						$this->pids[] = $sibling->getXref();
					}
				}
			}
		}
	}

	/**
	 * Prints the time line scale
	 *
	 */
	public function printTimeline() {
		$startYear = $this->timelineMinYear;
		while ($startYear < $this->timelineMaxYear) {
			$date = new Date($startYear);
			echo $date->display(false, '%Y');
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
		//base case
		if (!$this->people) {
			return self::chartTop;
		}

		$maxY = self::chartTop;
		foreach ($this->people as $person) {

			$bdate = $person->getEstimatedBirthDate();
			$ddate = $person->getEstimatedDeathDate();
			$birthYear = $bdate->minimumDate()->y;

			// truncate the bar at the current year
			$length = min($ddate->maximumDate()->y, $this->currentYear) - $birthYear;

			//set minimum width for single year lifespans
			$width = max(self::pixelsPerYear, $length * self::pixelsPerYear);

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
						$Y+=self::barSpacing;
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

			$eventinformation = array();
			foreach ($acceptedFacts as $val) {
				$tag = $val->getTag();
				//-- if the fact is a generic EVENt then get the qualifying TYPE
				if ($tag == "EVEN") {
					$tag = $val->getAttribute('TYPE');
				}
				$event              = new \stdClass();
				$event->date        = $val->getDate()->display();
				$event->label       = GedcomTag::getLabel($tag);
				$event->place       = $val->getPlace()->getFullName();
				$eventinformation[] = $event;
			}

			$direction  = I18N::direction() === 'ltr' ? 'left' : 'right';
			$lifespan   = $person->getLifeSpan();
			$sex        = $person->getSex();
			$popupClass = "person_box";
			if ($sex !== 'M') {
				$popupClass .= $sex;
			}

			// following line is a nasty method of approximating
			// the width of a string in pixels from the character count
			$minlength = mb_strlen(strip_tags($person->getFullName() . ' ' . $lifespan)) * 8;

			if ($width > $minlength) {
				$printName    = $person->getFullName();
				$abbrLifespan = $lifespan;
			} elseif ($width > 50) {
				$printName    = $person->getShortName();
				$abbrLifespan = '';
			} else {
				$printName    = '';
				$abbrLifespan = '';
			}

			printf("<div class='%s' style='top:%spx; %s:%spx; width:%spx; background-color:%s;'>",
			       $popupClass, $Y, $direction, $startPos, $width, $this->colorCycle($sex));

			printf("<div class='itr'>%s %s %s<div class='popup %s'><div><a href='%s'>%s %s</a></div>",
			       $person->getSexImage(), $printName, $abbrLifespan, $popupClass, $person->getHtmlUrl(), $person->getFullName(), $lifespan);

			foreach ($eventinformation as $event) {
				printf("<div>%s: %s %s</div>", $event->label, $event->date, $event->place);
			}
			echo "</div>" . // class='popup'
				"</div>" . // class='itr'
				"</div>";  // class=$popupclass

			$maxY = max($maxY, $Y);
		}

		return $maxY;
	}

	/**
	 * The significant individual on this page is the first one.
	 *
	 * @return Individual
	 */
	public function getSignificantIndividual() {
		if ($this->people) {
			return $this->people[0];
		} else {
			return parent::getSignificantIndividual();
		}
	}

	/**
	 * Search for people who had events in a given year range
	 *
	 * @param integer $startyear
	 * @param integer $endyear
	 *
	 * @return Individual[]
	 */
	private static function searchIndividualsInYearRange($startyear, $endyear) {
		global $WT_TREE;

		$startDate = new Date($startyear);
		$endDate   = new Date($endyear);

		$rows = Database::prepare(
			"SELECT DISTINCT i_id AS xref, i_file AS gedcom_id, i_gedcom AS gedcom" .
			" FROM `##individuals`" .
			" JOIN `##dates` ON i_id=d_gid AND i_file=d_file" .
			" WHERE i_file=:tree_id AND d_julianday1 BETWEEN :date1 AND :date2"
		)->execute(array(
			'tree_id'=>$WT_TREE->getTreeId(),
			'date1'=>$startDate->minimumJulianDay(),
			'date2'=>$endDate->maximumJulianDay()
		))->fetchAll();

		$list = array();
		foreach ($rows as $row) {
			$list[] = Individual::getInstance($row->xref, $WT_TREE, $row->gedcom);
		}

		return $list;
	}

	/**
	 * Function colorCycle
	 *
	 * $lightness cycles through 40%, 50%, 60%, 70%, 80%, 90%
	 * $hue reduces angle by baseHueAngle degrees on each complete $lightness cycle
	 * but limits its value to stay within 120Deg of base i.e
	 * Female tends from Red to Green, Male tends from Blue to Green
	 *
	 * @param string $sex
	 * @return string
	 */
	private function colorCycle($sex = 'U') {
		$lightness = ($this->colors[$sex]->lightness + 10) % 100;
		$hue       = $this->colors[$sex]->currentHue;

		if ($lightness === 0) {
			$lightness = self::baseLightness;
			if ($sex === 'F') {
				$hue += self::baseHueAngle;
				if ($hue > 120) {
					$hue = $this->colors[$sex]->baseHue;
				}
			} else {
				$hue -= self::baseHueAngle;
				if ($hue < 120) {
					$hue = $this->colors[$sex]->baseHue;
				}
			}
		}

		$this->colors[$sex]->currentHue = $hue;
		$this->colors[$sex]->lightness  = $lightness;

		return sprintf("hsla(%s, %s%%, %s%%, %s)",
		               $this->colors[$sex]->currentHue,
		               $this->colors[$sex]->saturation,
		               $this->colors[$sex]->lightness,
		               $this->colors[$sex]->luminance);
	}

}
