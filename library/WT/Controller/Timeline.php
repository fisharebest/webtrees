<?php
// Controller for the timeline chart
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_Timeline extends WT_Controller_Base {
	var $bheight = 30;
	var $placements = array();
	var $familyfacts = array();
	var $indifacts = array(); // array to store the fact records in for sorting and displaying
	var $birthyears=array();
	var $birthmonths=array();
	var $birthdays=array();
	var $baseyear=0;
	var $topyear=0;
	var $pids = array();
	var $people = array();
	var $pidlinks = "";
	var $scale = 2;

	// GEDCOM elements that may have DATE data, but should not be displayed
	private $nonfacts = array('BAPL', 'ENDL', 'SLGC', 'SLGS', '_TODO', 'CHAN');

	function __construct() {
		parent::__construct();

		$this->setPageTitle(WT_I18N::translate('Timeline'));
	
		$this->baseyear = date("Y");
		//-- new pid
		$newpid=safe_GET_xref('newpid');

		if (safe_GET('clear', '1')=='1') {
			unset($_SESSION['timeline_pids']);
		} else {
			if (isset($_SESSION['timeline_pids'])) $this->pids = $_SESSION['timeline_pids'];
			//-- pids array
			$this->pids=safe_GET_xref('pids');
		}
		if (!is_array($this->pids)) {
			$this->pids = array();
		} else {
			//-- make sure that arrays are indexed by numbers
			$this->pids = array_values($this->pids);
		}
		if (!empty($newpid) && !in_array($newpid, $this->pids)) {
			$this->pids[] = $newpid;
		}
		if (count($this->pids)==0) $this->pids[] = $this->getSignificantIndividual()->getXref();
		$remove = safe_GET_xref('remove');
		//-- cleanup user input
		$newpids = array();
		foreach ($this->pids as $value) {
			if ($value!=$remove) {
				$newpids[] = $value;
				$person = WT_Person::getInstance($value);
				if ($person) {
					$this->people[] = $person;
				}
			}
		}
		$this->pids = $newpids;
		$this->pidlinks = "";
		/* @var $indi Person */
		foreach ($this->people as $p=>$indi) {
			if (!is_null($indi) && $indi->canDisplayDetails()) {
				//-- setup string of valid pids for links
				$this->pidlinks .= "pids%5B%5D=".$indi->getXref()."&amp;";
				$bdate = $indi->getBirthDate();
				if ($bdate->isOK()) {
					$date = $bdate->MinDate();
					$date = $date->convert_to_cal('gregorian');
					if ($date->y) {
						$this->birthyears [$indi->getXref()] = $date->y;
						$this->birthmonths[$indi->getXref()] = max(1, $date->m);
						$this->birthdays  [$indi->getXref()] = max(1, $date->d);
					}
				}
				// find all the fact information
				$indi->add_family_facts(false);
				foreach ($indi->getIndiFacts() as $event) {
					//-- get the fact type
					$fact = $event->getTag();
					if (!in_array($fact, $this->nonfacts)) {
						//-- check for a date
						$date = $event->getDate();
						$date=$date->MinDate();
						$date=$date->convert_to_cal('gregorian');
						if ($date->y) {
							$this->baseyear=min($this->baseyear, $date->y);
							$this->topyear =max($this->topyear,  $date->y);

							if (!$indi->isDead())
								$this->topyear=max($this->topyear, date('Y'));
							$event->temp = $p;
							//-- do not add the same fact twice (prevents marriages from being added multiple times)
							if (!in_array($event, $this->indifacts, true)) $this->indifacts[] = $event;
						}
					}
				}
			}
		}
		$_SESSION['timeline_pids'] = $this->pids;
		$scale=safe_GET_integer('scale', 0, 200, 0);
		if ($scale==0) {
			$this->scale = round(($this->topyear-$this->baseyear)/20 * count($this->indifacts)/4);
			if ($this->scale<6) $this->scale = 6;
		}
		else $this->scale = $scale;
		if ($this->scale<2) $this->scale=2;
		$this->baseyear -= 5;
		$this->topyear += 5;
	}

	/**
	* check the privacy of the incoming people to make sure they can be shown
	*/
	function checkPrivacy() {
		$printed = false;
		for ($i=0; $i<count($this->people); $i++) {
			if (!is_null($this->people[$i])) {
				if (!$this->people[$i]->canDisplayDetails()) {
					if ($this->people[$i]->canDisplayName()) {
						echo "&nbsp;<a href=\"".$this->people[$i]->getHtmlUrl()."\">".$this->people[$i]->getFullName()."</a>";
						print_privacy_error();
						echo "<br>";
						$printed = true;
					}
					else if (!$printed) {
						print_privacy_error();
						echo "<br>";
					}
				}
			}
		}
	}

	function print_time_fact($event) {
		global $basexoffset, $baseyoffset, $factcount, $TEXT_DIRECTION, $WT_IMAGES, $SHOW_PEDIGREE_PLACES, $placements, $familyfacts;

		/* @var $event Event */
		$factrec = $event->getGedComRecord();
			$fact = $event->getTag();
			$desc = $event->getDetail();
			if ($fact=="EVEN" || $fact=="FACT") {
				$fact = $event->getType();
			}
				//-- check if this is a family fact
				$famid = $event->getFamilyId();
				if ($famid!=null) {
					//-- if we already showed this family fact then don't print it
					if (isset($familyfacts[$famid.$fact])&&($familyfacts[$famid.$fact]!=$event->temp)) return;
					$familyfacts[$famid.$fact] = $event->temp;
				}
				$gdate=$event->getDate();
				$date=$gdate->MinDate();
				$date=$date->convert_to_cal('gregorian');
				$year  = $date->y;
				$month = max(1, $date->m);
				$day   = max(1, $date->d);
				$xoffset = $basexoffset+22;
				$yoffset = $baseyoffset+(($year-$this->baseyear) * $this->scale)-($this->scale);
				$yoffset = $yoffset + (($month / 12) * $this->scale);
				$yoffset = $yoffset + (($day / 30) * ($this->scale/12));
				$yoffset = floor($yoffset);
				$place = round($yoffset / $this->bheight);
				$i=1;
				$j=0;
				$tyoffset = 0;
				while (isset($placements[$place])) {
					if ($i==$j) {
						$tyoffset = $this->bheight * $i;
						$i++;
					}
					else {
						$tyoffset = -1 * $this->bheight * $j;
						$j++;
					}
					$place = round(($yoffset+$tyoffset) / ($this->bheight));
				}
				$yoffset += $tyoffset;
				$xoffset += abs($tyoffset);
				$placements[$place] = $yoffset;

				echo "<div id=\"fact$factcount\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($xoffset):"right: ".($xoffset))."px; top:".($yoffset)."px; font-size: 8pt; height: ".($this->bheight)."px;\" onmousedown=\"factMD(this, '".$factcount."', ".($yoffset-$tyoffset).");\">";
				echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"cursor: hand;\"><tr><td>";
				echo "<img src=\"".$WT_IMAGES["hline"]."\" name=\"boxline$factcount\" id=\"boxline$factcount\" height=\"3\" align=\"left\" width=\"10\" alt=\"\" style=\"padding-";
				if ($TEXT_DIRECTION=="ltr") echo "left";
				else echo "right";
				echo ": 3px;\">";
				$col = $event->temp % 6;
				echo "</td><td valign=\"top\" class=\"person".$col."\">";
				if (count($this->pids) > 6) echo $event->getParentObject()->getFullName()." - ";
				$record=$event->getParentObject();
				echo $event->getLabel();
				echo " -- ";
				if ($record instanceof WT_Person) {
					echo format_fact_date($event, $record, false, false);
				} elseif ($record instanceof WT_Family) {
					echo $gdate->Display(false);
					if ($record->getHusband() && $record->getHusband()->getBirthDate()->isOK()) {
						$ageh=get_age_at_event(WT_Date::GetAgeGedcom($record->getHusband()->getBirthDate(), $gdate), false);
					} else {
						$ageh=null;
					}
					if ($record->getWife() && $record->getWife()->getBirthDate()->isOK()) {
						$agew=get_age_at_event(WT_Date::GetAgeGedcom($record->getWife()->getBirthDate(), $gdate), false);
					} else {
						$agew=null;
					}
					if ($ageh && $agew) {
						echo '<span class="age"> ', WT_I18N::translate('Husband\'s age'), ' ', $ageh, ' ', WT_I18N::translate('Wife\'s age'), ' ', $agew, '</span>';
					} elseif ($ageh) {
						echo '<span class="age"> ', WT_I18N::translate('Age'), ' ', $ageh, '</span>';
					} elseif ($agew) {
						echo '<span class="age"> ', WT_I18N::translate('Age'), ' ', $ageh, '</span>';
					}
				}
				echo " ".htmlspecialchars($desc);
				if ($SHOW_PEDIGREE_PLACES>0) {
					$place = $event->getPlace();
					if ($place!=null) {
						if ($desc!=null) echo " - ";
						$plevels = explode(',', $place);
						for ($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
							if (!empty($plevels[$plevel])) {
								if ($plevel>0) echo ", ";
								echo htmlspecialchars($plevels[$plevel]);
							}
						}
					}
				}
				//-- print spouse name for marriage events
				$spouse = WT_Person::getInstance($event->getSpouseId());
				if ($spouse) {
					for ($p=0; $p<count($this->pids); $p++) {
						if ($this->pids[$p]==$spouse->getXref()) break;
					}
					if ($p==count($this->pids)) $p = $event->temp;
					$col = $p % 6;
					if ($spouse->getXref()!=$this->pids[$p]) {
						echo ' <a href="', $spouse->getHtmlUrl(), '">', $spouse->getFullName(), '</a>';
					}
					else {
						$ct = preg_match("/2 _WTFS @(.*)@/", $factrec, $match);
						if ($ct>0) {
							echo " <a href=\"family.php?famid={$match[1]}&amp;ged=".WT_GEDURL."\">";
							echo $event->getParentObject()->getFullName();
							echo "</a>";
						}
					}
				}
				echo "</td></tr></table>";
				echo "</div>";
				if ($TEXT_DIRECTION=='ltr') {
					$img = "dline2";
					$ypos = "0%";
				}
				else {
					$img = "dline";
					$ypos = "100%";
				}
				$dyoffset = ($yoffset-$tyoffset)+$this->bheight/3;
				if ($tyoffset<0) {
					$dyoffset = $yoffset+$this->bheight/3;
					if ($TEXT_DIRECTION=='ltr') {
						$img = "dline";
						$ypos = "100%";
					}
					else {
						$img = "dline2";
						$ypos = "0%";
					}
				}
				//-- print the diagnal line
				echo "<div id=\"dbox$factcount\" style=\"position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+25):"right: ".($basexoffset+25))."px; top:".($dyoffset)."px; font-size: 8pt; height: ".(abs($tyoffset))."px; width: ".(abs($tyoffset))."px;";
				echo " background-image: url('".$WT_IMAGES[$img]."');";
				echo " background-position: 0% $ypos;\">";
				echo "</div>";
	}

	public function getSignificantIndividual() {
		if ($this->pids) {
			return WT_Person::getInstance($this->pids[0]);
		} else {
			return parent::getSignificantIndividual();
		}
	}
}
