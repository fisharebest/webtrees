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

use Fisharebest\Webtrees\Date;
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\Functions\FunctionsDate;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Theme;

/**
 * Controller for the timeline chart
 */
class TimelineController extends PageController {
	/** @var int Height of the age box */
	public $bheight = 30;

	/** @var Fact[] The facts to display on the chart */
	public $indifacts = array(); // array to store the fact records in for sorting and displaying

	/** @var int[] Numeric birth years of each individual */
	public $birthyears = array();

	/** @var int[] Numeric birth months of each individual */
	public $birthmonths = array();

	/** @var int[] Numeric birth days of each individual */
	public $birthdays = array();

	/** @var int Lowest year to display */
	public $baseyear = 0;

	/** @var int Highest year to display */
	public $topyear = 0;

	/** @var string[] List of individual XREFs to display */
	private $pids = array();

	/** @var Individual[] List of individuals to display */
	public $people = array();

	/** @var string URL-encoded list of XREFs */
	public $pidlinks = '';

	/** @var int Vertical scale */
	public $scale = 2;

	/** @var string[] GEDCOM elements that may have DATE data, but should not be displayed */
	private $nonfacts = array('BAPL', 'ENDL', 'SLGC', 'SLGS', '_TODO', 'CHAN');

	/**
	 * Startup activity
	 */
	public function __construct() {
		global $WT_TREE;

		parent::__construct();

		$this->setPageTitle(I18N::translate('Timeline'));

		$this->baseyear = (int) date('Y');
		// new pid
		$newpid = Filter::get('newpid', WT_REGEX_XREF);

		// pids array
		$this->pids = Filter::getArray('pids', WT_REGEX_XREF);
		// make sure that arrays are indexed by numbers
		$this->pids = array_values($this->pids);
		if (!empty($newpid) && !in_array($newpid, $this->pids)) {
			$this->pids[] = $newpid;
		}
		if (count($this->pids) == 0) {
			$this->pids[] = $this->getSignificantIndividual()->getXref();
		}
		$remove = Filter::get('remove', WT_REGEX_XREF);
		// cleanup user input
		$newpids = array();
		foreach ($this->pids as $value) {
			if ($value != $remove) {
				$newpids[] = $value;
				$person    = Individual::getInstance($value, $WT_TREE);
				if ($person) {
					$this->people[] = $person;
				}
			}
		}
		$this->pids     = $newpids;
		$this->pidlinks = '';

		foreach ($this->people as $indi) {
			if (!is_null($indi) && $indi->canShow()) {
				// setup string of valid pids for links
				$this->pidlinks .= 'pids%5B%5D=' . $indi->getXref() . '&amp;';
				$bdate = $indi->getBirthDate();
				if ($bdate->isOK()) {
					$date                                = new GregorianDate($bdate->minimumJulianDay());
					$this->birthyears [$indi->getXref()] = $date->y;
					$this->birthmonths[$indi->getXref()] = max(1, $date->m);
					$this->birthdays  [$indi->getXref()] = max(1, $date->d);
				}
				// find all the fact information
				$facts = $indi->getFacts();
				foreach ($indi->getSpouseFamilies() as $family) {
					foreach ($family->getFacts() as $fact) {
						$facts[] = $fact;
					}
				}
				foreach ($facts as $event) {
					// get the fact type
					$fact = $event->getTag();
					if (!in_array($fact, $this->nonfacts)) {
						// check for a date
						$date = $event->getDate();
						if ($date->isOK()) {
							$date           = new GregorianDate($date->minimumJulianDay());
							$this->baseyear = min($this->baseyear, $date->y);
							$this->topyear  = max($this->topyear, $date->y);

							if (!$indi->isDead()) {
								$this->topyear = max($this->topyear, (int) date('Y'));
							}

							// do not add the same fact twice (prevents marriages from being added multiple times)
							if (!in_array($event, $this->indifacts, true)) {
								$this->indifacts[] = $event;
							}
						}
					}
				}
			}
		}
		$scale = Filter::getInteger('scale', 0, 200);
		if ($scale === 0) {
			$this->scale = (int) (($this->topyear - $this->baseyear) / 20 * count($this->indifacts) / 4);
			if ($this->scale < 6) {
				$this->scale = 6;
			}
		} else {
			$this->scale = $scale;
		}
		if ($this->scale < 2) {
			$this->scale = 2;
		}
		$this->baseyear -= 5;
		$this->topyear += 5;
	}

	/**
	 * Print a fact for an individual.
	 *
	 * @param Fact $event
	 */
	public function printTimeFact(Fact $event) {
		global $basexoffset, $baseyoffset, $factcount, $placements;

		$desc = $event->getValue();
		// check if this is a family fact
		$gdate    = $event->getDate();
		$date     = $gdate->minimumDate();
		$date     = $date->convertToCalendar('gregorian');
		$year     = $date->y;
		$month    = max(1, $date->m);
		$day      = max(1, $date->d);
		$xoffset  = $basexoffset + 22;
		$yoffset  = $baseyoffset + (($year - $this->baseyear) * $this->scale) - ($this->scale);
		$yoffset  = $yoffset + (($month / 12) * $this->scale);
		$yoffset  = $yoffset + (($day / 30) * ($this->scale / 12));
		$yoffset  = (int) ($yoffset);
		$place    = (int) ($yoffset / $this->bheight);
		$i        = 1;
		$j        = 0;
		$tyoffset = 0;
		while (isset($placements[$place])) {
			if ($i === $j) {
				$tyoffset = $this->bheight * $i;
				$i++;
			} else {
				$tyoffset = -1 * $this->bheight * $j;
				$j++;
			}
			$place = (int) (($yoffset + $tyoffset) / ($this->bheight));
		}
		$yoffset += $tyoffset;
		$xoffset += abs($tyoffset);
		$placements[$place] = $yoffset;

		echo "<div id=\"fact$factcount\" style=\"position:absolute; " . (I18N::direction() === 'ltr' ? 'left: ' . ($xoffset) : 'right: ' . ($xoffset)) . 'px; top:' . ($yoffset) . "px; font-size: 8pt; height: " . ($this->bheight) . "px;\" onmousedown=\"factMouseDown(this, '" . $factcount . "', " . ($yoffset - $tyoffset) . ");\">";
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"cursor: hand;\"><tr><td>";
		echo "<img src=\"" . Theme::theme()->parameter('image-hline') . "\" name=\"boxline$factcount\" id=\"boxline$factcount\" height=\"3\" align=\"left\" width=\"10\" alt=\"\" style=\"padding-";
		if (I18N::direction() === 'ltr') {
			echo 'left: 3px;">';
		} else {
			echo 'right: 3px;">';
		}

		$col = array_search($event->getParent(), $this->people);
		if ($col === false) {
			// Marriage event - use the color of the husband
			$col = array_search($event->getParent()->getHusband(), $this->people);
		}
		if ($col === false) {
			// Marriage event - use the color of the wife
			$col = array_search($event->getParent()->getWife(), $this->people);
		}
		$col = $col % 6;
		echo '</td><td valign="top" class="person' . $col . '">';
		if (count($this->pids) > 6) {
			echo $event->getParent()->getFullName() . ' — ';
		}
		$record = $event->getParent();
		echo $event->getLabel();
		echo ' — ';
		if ($record instanceof Individual) {
			echo FunctionsPrint::formatFactDate($event, $record, false, false);
		} elseif ($record instanceof Family) {
			echo $gdate->display();
			if ($record->getHusband() && $record->getHusband()->getBirthDate()->isOK()) {
				$ageh = FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($record->getHusband()->getBirthDate(), $gdate), false);
			} else {
				$ageh = null;
			}
			if ($record->getWife() && $record->getWife()->getBirthDate()->isOK()) {
				$agew = FunctionsDate::getAgeAtEvent(Date::getAgeGedcom($record->getWife()->getBirthDate(), $gdate), false);
			} else {
				$agew = null;
			}
			if ($ageh && $agew) {
				echo '<span class="age"> ', I18N::translate('Husband’s age'), ' ', $ageh, ' ', I18N::translate('Wife’s age'), ' ', $agew, '</span>';
			} elseif ($ageh) {
				echo '<span class="age"> ', I18N::translate('Age'), ' ', $ageh, '</span>';
			} elseif ($agew) {
				echo '<span class="age"> ', I18N::translate('Age'), ' ', $ageh, '</span>';
			}
		}
		echo ' ' . Filter::escapeHtml($desc);
		if (!$event->getPlace()->isEmpty()) {
			echo ' — ' . $event->getPlace()->getShortName();
		}
		// Print spouses names for family events
		if ($event->getParent() instanceof Family) {
			echo ' — <a href="', $event->getParent()->getHtmlUrl(), '">', $event->getParent()->getFullName(), '</a>';
		}
		echo '</td></tr></table>';
		echo '</div>';
		if (I18N::direction() === 'ltr') {
			$img  = 'image-dline2';
			$ypos = '0%';
		} else {
			$img  = 'image-dline';
			$ypos = '100%';
		}
		$dyoffset = ($yoffset - $tyoffset) + $this->bheight / 3;
		if ($tyoffset < 0) {
			$dyoffset = $yoffset + $this->bheight / 3;
			if (I18N::direction() === 'ltr') {
				$img  = 'image-dline';
				$ypos = '100%';
			} else {
				$img  = 'image-dline2';
				$ypos = '0%';
			}
		}
		// Print the diagonal line
		echo '<div id="dbox' . $factcount . '" style="position:absolute; ' . (I18N::direction() === 'ltr' ? 'left: ' . ($basexoffset + 25) : 'right: ' . ($basexoffset + 25)) . 'px; top:' . ($dyoffset) . 'px; font-size: 8pt; height: ' . abs($tyoffset) . 'px; width: ' . abs($tyoffset) . 'px;';
		echo ' background-image: url(\'' . Theme::theme()->parameter($img) . '\');';
		echo ' background-position: 0% ' . $ypos . ';">';
		echo '</div>';
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return Individual
	 */
	public function getSignificantIndividual() {
		global $WT_TREE;

		if ($this->pids) {
			return Individual::getInstance($this->pids[0], $WT_TREE);
		} else {
			return parent::getSignificantIndividual();
		}
	}
}
