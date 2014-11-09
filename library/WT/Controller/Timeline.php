<?php
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

/**
 * Class WT_Controller_Timeline - Controller for the timeline chart
 */
class WT_Controller_Timeline extends WT_Controller_Page {
	var $bheight = 30;
	var $placements = array();
	var $indifacts = array(); // array to store the fact records in for sorting and displaying
	var $birthyears = array();
	var $birthmonths = array();
	var $birthdays = array();
	var $baseyear = 0;
	var $topyear = 0;
	var $pids = array();
	var $people = array();
	var $pidlinks = '';
	var $scale = 2;

	// GEDCOM elements that may have DATE data, but should not be displayed
	private $nonfacts = array('BAPL', 'ENDL', 'SLGC', 'SLGS', '_TODO', 'CHAN');

	/**
	 * Startup activity
	 */
	function __construct() {
		parent::__construct();

		$this->setPageTitle(WT_I18N::translate('Timeline'));

		$this->baseyear = (int)date('Y');
		// new pid
		$newpid = WT_Filter::get('newpid', WT_REGEX_XREF);

		// pids array
		$this->pids = WT_Filter::getArray('pids', WT_REGEX_XREF);
		// make sure that arrays are indexed by numbers
		$this->pids = array_values($this->pids);
		if (!empty($newpid) && !in_array($newpid, $this->pids)) {
			$this->pids[] = $newpid;
		}
		if (count($this->pids) == 0) {
			$this->pids[] = $this->getSignificantIndividual()->getXref();
		}
		$remove = WT_Filter::get('remove', WT_REGEX_XREF);
		// cleanup user input
		$newpids = array();
		foreach ($this->pids as $value) {
			if ($value != $remove) {
				$newpids[] = $value;
				$person    = WT_Individual::getInstance($value);
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
					$date                                = new WT_Date_Gregorian($bdate->MinDate()->minJD);
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
							$date           = new WT_Date_Gregorian($date->MinDate()->minJD);
							$this->baseyear = min($this->baseyear, $date->y);
							$this->topyear  = max($this->topyear, $date->y);

							if (!$indi->isDead()) {
								$this->topyear = max($this->topyear, (int)date('Y'));
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
		$scale = WT_Filter::getInteger('scale', 0, 200);
		if ($scale === 0) {
			$this->scale = (int)(($this->topyear - $this->baseyear) / 20 * count($this->indifacts) / 4);
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
	 * @param WT_Fact $event
	 */
	function print_time_fact(WT_Fact $event) {
		global $basexoffset, $baseyoffset, $factcount, $TEXT_DIRECTION, $WT_IMAGES, $placements;

		$desc = $event->getValue();
		// check if this is a family fact
		$gdate    = $event->getDate();
		$date     = $gdate->MinDate();
		$date     = $date->convertToCalendar('gregorian');
		$year     = $date->y;
		$month    = max(1, $date->m);
		$day      = max(1, $date->d);
		$xoffset  = $basexoffset + 22;
		$yoffset  = $baseyoffset + (($year - $this->baseyear) * $this->scale) - ($this->scale);
		$yoffset  = $yoffset + (($month / 12) * $this->scale);
		$yoffset  = $yoffset + (($day / 30) * ($this->scale / 12));
		$yoffset  = (int)($yoffset);
		$place    = (int)($yoffset / $this->bheight);
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
			$place = (int)(($yoffset + $tyoffset) / ($this->bheight));
		}
		$yoffset += $tyoffset;
		$xoffset += abs($tyoffset);
		$placements[$place] = $yoffset;

		echo "<div id=\"fact$factcount\" style=\"position:absolute; " . ($TEXT_DIRECTION == "ltr" ? "left: " . ($xoffset) : "right: " . ($xoffset)) . "px; top:" . ($yoffset) . "px; font-size: 8pt; height: " . ($this->bheight) . "px;\" onmousedown=\"factMouseDown(this, '" . $factcount . "', " . ($yoffset - $tyoffset) . ");\">";
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"cursor: hand;\"><tr><td>";
		echo "<img src=\"" . $WT_IMAGES["hline"] . "\" name=\"boxline$factcount\" id=\"boxline$factcount\" height=\"3\" align=\"left\" width=\"10\" alt=\"\" style=\"padding-";
		if ($TEXT_DIRECTION == 'ltr') {
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
		if ($record instanceof WT_Individual) {
			echo format_fact_date($event, $record, false, false);
		} elseif ($record instanceof WT_Family) {
			echo $gdate->display();
			if ($record->getHusband() && $record->getHusband()->getBirthDate()->isOK()) {
				$ageh = get_age_at_event(WT_Date::GetAgeGedcom($record->getHusband()->getBirthDate(), $gdate), false);
			} else {
				$ageh = null;
			}
			if ($record->getWife() && $record->getWife()->getBirthDate()->isOK()) {
				$agew = get_age_at_event(WT_Date::GetAgeGedcom($record->getWife()->getBirthDate(), $gdate), false);
			} else {
				$agew = null;
			}
			if ($ageh && $agew) {
				echo '<span class="age"> ', WT_I18N::translate('Husband’s age'), ' ', $ageh, ' ', WT_I18N::translate('Wife’s age'), ' ', $agew, '</span>';
			} elseif ($ageh) {
				echo '<span class="age"> ', WT_I18N::translate('Age'), ' ', $ageh, '</span>';
			} elseif ($agew) {
				echo '<span class="age"> ', WT_I18N::translate('Age'), ' ', $ageh, '</span>';
			}
		}
		echo ' ' . WT_Filter::escapeHtml($desc);
		if (!$event->getPlace()->isEmpty()) {
			echo ' — ' . $event->getPlace()->getShortName();
		}
		// Print spouses names for family events
		if ($event->getParent() instanceof WT_Family) {
			echo ' — <a href="', $event->getParent()->getHtmlUrl(), '">', $event->getParent()->getFullName(), '</a>';
		}
		echo '</td></tr></table>';
		echo '</div>';
		if ($TEXT_DIRECTION === 'ltr') {
			$img  = 'dline2';
			$ypos = '0%';
		} else {
			$img  = 'dline';
			$ypos = '100%';
		}
		$dyoffset = ($yoffset - $tyoffset) + $this->bheight / 3;
		if ($tyoffset < 0) {
			$dyoffset = $yoffset + $this->bheight / 3;
			if ($TEXT_DIRECTION === 'ltr') {
				$img  = 'dline';
				$ypos = '100%';
			} else {
				$img  = 'dline2';
				$ypos = '0%';
			}
		}
		// Print the diagonal line
		echo '<div id="dbox' . $factcount . '" style="position:absolute; ' . ($TEXT_DIRECTION == 'ltr' ? 'left: ' . ($basexoffset + 25) : 'right: ' . ($basexoffset + 25)) . 'px; top:' . ($dyoffset) . 'px; font-size: 8pt; height: ' . abs($tyoffset) . 'px; width: ' . abs($tyoffset) . 'px;';
		echo ' background-image: url(\'' . $WT_IMAGES[$img] . '\');';
		echo ' background-position: 0% ' . $ypos . ';">';
		echo '</div>';
	}

	/**
	 * Get significant information from this page, to allow other pages such as
	 * charts and reports to initialise with the same records
	 *
	 * @return WT_Individual
	 */
	public function getSignificantIndividual() {
		if ($this->pids) {
			return WT_Individual::getInstance($this->pids[0]);
		} else {
			return parent::getSignificantIndividual();
		}
	}
}
