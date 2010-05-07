<?php

/**
* Controller for the timeline chart
*
* webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
* Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* @package webtrees
* @subpackage Charts
* @version $Id$
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_LIFESPAN_CTRL_PHP', '');

require_once WT_ROOT.'includes/functions/functions_charts.php';
require_once WT_ROOT.'includes/controllers/basecontrol.php';
require_once WT_ROOT.'includes/classes/class_person.php';

function compare_people($a, $b) {
	return GedcomDate::Compare($a->getEstimatedBirthDate(), $b->getEstimatedBirthDate());
}


// GEDCOM elements that will be found but should not be displayed
$nonfacts = array("FAMS", "FAMC", "MAY", "BLOB", "OBJE", "SEX", "NAME", "SOUR", "NOTE", "BAPL", "ENDL", "SLGC", "SLGS", "_TODO", "_WT_OBJE_SORT", "CHAN", "HUSB", "WIFE", "CHIL", "BIRT", "DEAT", "BURI");// DEATH OF SIBLING:  DEATH OF HALF SIBLING DEATH OF MOTHER DEATH OF FATHER DEATH OF CHILD
$nonfamfacts = array("CHAN", "HUSB", "WIFE", "CHIL");

/**
* Main controller class for the timeline page.
*/
class LifespanControllerRoot extends BaseController {
	var $pids = array ();
	var $people = array();
	var $scale = 2;
	var $YrowLoc = 125;
	var $minYear = 0;

	// The following colours are deliberately omitted from the $colors list:
	// Blue, Red, Black, White, Green
	var $colors = array ('Aliceblue', 'Antiquewhite', 'Aqua', 'Aquamarine', 'Azure', 'Beige', 'Bisque', 'Blanchedalmond', 'Blueviolet', 'Brown', 'Burlywood', 'Cadetblue', 'Chartreuse', 'Chocolate', 'Coral', 'Cornflowerblue', 'Cornsilk', 'Crimson', 'Cyan', 'Darkcyan', 'Darkgoldenrod', 'Darkgray', 'Darkgreen', 'Darkkhaki', 'Darkmagenta', 'Darkolivegreen', 'Darkorange', 'Darkorchid', 'Darkred', 'Darksalmon', 'Darkseagreen', 'Darkslateblue', 'Darkturquoise', 'Darkviolet', 'Deeppink', 'Deepskyblue', 'Dimgray', 'Dodgerblue', 'Firebrick', 'Floralwhite', 'Forestgreen', 'Fuchsia', 'Gainsboro', 'Ghostwhite', 'Gold', 'Goldenrod', 'Gray', 'Greenyellow', 'Honeydew', 'Hotpink', 'Indianred', 'Ivory', 'Khaki', 'Lavender', 'Lavenderblush', 'Lawngreen', 'Lemonchiffon', 'Lightblue', 'Lightcoral', 'Lightcyan', 'Lightgoldenrodyellow', 'Lightgreen', 'Lightgrey', 'Lightpink', 'Lightsalmon', 'Lightseagreen', 'Lightskyblue', 'Lightslategray', 'Lightsteelblue', 'Lightyellow', 'Lime', 'Limegreen', 'Linen', 'Magenta', 'Maroon', 'Mediumaqamarine', ' Mediumblue', 'Mediumorchid', 'Mediumpurple', 'Mediumseagreen', 'Mediumslateblue', 'Mediumspringgreen', 'Mediumturquoise', 'Mediumvioletred', 'Mintcream', 'Mistyrose', 'Moccasin', 'Navajowhite', 'Oldlace', 'Olive', 'Olivedrab', 'Orange', 'Orangered', 'Orchid', 'Palegoldenrod', 'Palegreen', 'Paleturquoise', 'Palevioletred', 'Papayawhip', 'Peachpuff', 'Peru', 'Pink', 'Plum', 'Powderblue', 'Purple', 'Rosybrown', 'Royalblue', 'Saddlebrown', 'Salmon', 'Sandybrown', 'Seagreen', 'Seashell', 'Sienna', 'Silver', 'Skyblue', 'Slateblue', 'Slategray', 'Snow', 'Springgreen', 'Steelblue', 'Tan', 'Teal', 'Thistle', 'Tomato', 'Turquoise', 'Violet', 'Wheat', 'Whitesmoke', 'Yellow', 'YellowGreen');
	var $malecolorR = array('000', ' 010', ' 020', ' 030', ' 040', ' 050', ' 060', ' 070', ' 080', ' 090', ' 100', ' 110', ' 120', ' 130', ' 140', ' 150', ' 160', ' 170', ' 180', ' 190', ' 200', ' 210', ' 220', ' 230', ' 240', ' 250');
	var $malecolorG = array('000', ' 010', ' 020', ' 030', ' 040', ' 050', ' 060', ' 070', ' 080', ' 090', ' 100', ' 110', ' 120', ' 130', ' 140', ' 150', ' 160', ' 170', ' 180', ' 190', ' 200', ' 210', ' 220', ' 230', ' 240', ' 250');
	var $malecolorB = 255;
	var $femalecolorR = 255;
	var $femalecolorG = array('000', ' 010', ' 020', ' 030', ' 040', ' 050', ' 060', ' 070', ' 080', ' 090', ' 100', ' 110', ' 120', ' 130', ' 140', ' 150', ' 160', ' 170', ' 180', ' 190', ' 200', ' 210', ' 220', ' 230', ' 240', ' 250');
	var $femalecolorB = array('250', ' 240', ' 230', ' 220', ' 210', ' 200', ' 190', ' 180', ' 170', ' 160', ' 150', ' 140', ' 130', ' 120', ' 110', ' 100', ' 090', ' 080', ' 070', ' 060', ' 050', ' 040', ' 030', ' 020', ' 010', '000');
	var $color;
	var $colorindex;
	var $Fcolorindex;
	var $Mcolorindex;
	var $zoomfactor;
	var $timelineMinYear;
	var $timelineMaxYear;
	var $birthMod;
	var $deathMod;
	var $endMod = 0;
	var $modTest;
	var $currentYear;
	var $endDate;
	var $startDate;
	var $currentsex;
	/**
	* constructor
	*/
	function TimelineRootController() {
		parent :: BaseController();
	}

	/**
	* Initialization function
	*/
	function init() {
		global $GEDCOM_ID_PREFIX;
		$this->colorindex = 0;
		$this->Fcolorindex = 0;
		$this->Mcolorindex = 0;
		$this->zoomfactor = 10;
		$this->color = "#0000FF";
		$this->currentYear = date("Y");
		$this->deathMod = 0;
		$this->endDate = $this->currentYear;


		//--new pid
		$newpid=safe_GET_xref('newpid');
		if ($newpid) {
			$person = Person::getInstance($newpid);
			if (is_null($person) && $GEDCOM_ID_PREFIX) {
				//-- allow the user to enter the id without the "I" prefix
				$newpid = $GEDCOM_ID_PREFIX.$newpid;
				$person = Person::getInstance($newpid);
			}
			//-- make sure we have the id from the gedcom record
			else $newpid = $person->getXref();
		}

		if (safe_GET('clear', '1')=='1') {
			unset($_SESSION['timeline_pids']);
		} else {
			if (isset($_SESSION['timeline_pids']))
				$this->pids = $_SESSION['timeline_pids'];

			if (!empty ($newpid))
				$this->pids[] = $newpid;

			//-- pids array
			$pids=safe_GET_xref('pids');
			if ($pids) {
				$this->pids = $pids;
				if (!empty ($newpid))
					$this->pids[] = $newpid;
			}

			//-- gets the immediate family for the individual being added if the include immediate family checkbox is checked.
			if (safe_GET('addFamily', 'yes')=='yes'){
				if (isset($newpid)) $this->addFamily($newpid);
			}

			$remove = safe_GET_xref('remove');

			//-- always start with someone on the chart
			if (count($this->pids)==0) {
				$this->pids[] = $this->addFamily(check_rootid(""));
			}

			//-- limit to a certain place
			$searchplace=safe_GET('place');
			if (!empty($searchplace)) {
				$place_pids = get_place_positions($searchplace);
				if (count($place_pids)>0) {
					$this->pids = $place_pids;
				}
			}

			//-- store the people in the session
			$_SESSION['timeline_pids'] = $this->pids;

			$beginYear  =safe_GET_integer('beginYear', 0, date('Y')+100, 0);
			$endYear    =safe_GET_integer('endYear',   0, date('Y')+100, 0);
			if ($beginYear==0 || $endYear==0) {
			//-- cleanup user input
			$this->pids = array_unique($this->pids);  //removes duplicates
				foreach ($this->pids as $key => $value) {
					if ($value != $remove) {
						$this->pids[$key] = $value;
						$person = Person::getInstance($value);
						// get_place_positions() returns families as well as individuals.
						if ($person && $person->getType()=='INDI') {
							$bdate = $person->getEstimatedBirthDate();
							$ddate = $person->getEstimatedDeathDate();

							//--Checks to see if the details of that person can be viewed
							if ($bdate->isOK() && $person->canDisplayDetails()) {
								$this->people[] = $person;
							}
						}
					}
				}
			}


			//--Finds if the begin year and end year textboxes are not empty
			else {
				//-- reset the people array when doing a year range search
				$this->people = array();
				//Takes the begining year and end year passed by the postback and modifies them and uses them to populate
				//the time line

				//Variables to restrict the person boxes to the year searched.
				//--Searches for individuals who had an even between the year begin and end years
				$indis = search_indis_year_range($beginYear, $endYear);
				//--Populates an array of people that had an event within those years

				foreach ($indis as $person) {
					if (empty($searchplace) || in_array($person->getXref(), $this->pids)) {
						$bdate = $person->getEstimatedBirthDate();
						$ddate = $person->getEstimatedDeathDate();
						//--Checks to see if the details of that person can be viewed
						if ($bdate->isOK() && $person->canDisplayDetails()) {
							$this->people[] = $person;
						}
					}
				}
				unset($_SESSION['timeline_pids']);
			}

			//--Sort the arrar in order of being year
			uasort($this->people, "compare_people");
			//If there is people in the array posted back this if occurs
			if (isset ($this->people[0])) {
				//Find the maximum Death year and mimimum Birth year for each individual returned in the array.
				$bdate = $this->people[0]->getEstimatedBirthDate();
				$ddate = $this->people[0]->getEstimatedDeathDate();
				$this->timelineMinYear=$bdate->gregorianYear();
				$this->timelineMaxYear=$ddate->gregorianYear() ? $ddate->gregorianYear() : date('Y');
				foreach ($this->people as $key => $value) {
					$bdate = $value->getEstimatedBirthDate();
					$ddate = $value->getEstimatedDeathDate();
					$this->timelineMinYear=min($this->timelineMinYear, $bdate->gregorianYear());
					$this->timelineMaxYear=max($this->timelineMaxYear, $ddate->gregorianYear() ? $ddate->gregorianYear() : date('Y'));
				}

				if($this->timelineMaxYear > $this->currentYear){
					$this->timelineMaxYear = $this->currentYear;
				}

			}
			else {
				// Sets the default timeline length
				$this->timelineMinYear = date("Y") - 101;
				$this->timelineMaxYear = date("Y");
			}
		}
	}

	/**
	* Add a person and his or her immediate family members to
	* the pids array
	* @param string $newpid
	*/
	function addFamily($newpid, $gen=0) {
		if (!empty ($newpid)) {
			$person = Person::getInstance($newpid);
			if (is_null($person)) return;
			$this->pids[] = $newpid;
			$families = $person->getSpouseFamilies();
			//-- foreach gets the spouse and children of the individual.
			foreach($families as $famID => $family){
				if($newpid != $family->getHusbId()) {
					if ($gen>0) $this->pids[] = addFamily($family->getHusbId(), $gen-1);
					else $this->pids[] = $family->getHusbId();
				}
				if($newpid != $family->getWifeId()) {
					if ($gen>0) $this->pids[] = addFamily($family->getWifeId(), $gen-1);
					else $this->pids[] = $family->getWifeId();
				}
				$children = $family->getChildren();
				foreach($children as $childID => $child){
					if ($gen>0) $this->pids[] = addFamily($child->getXref(), $gen-1);
					else $this->pids[] = $child->getXref();
				}
			}
			$families = $person->getChildFamilies();
			//-- foreach gets the father, mother and sibblings of the individual.
			foreach($families as $famID => $family){
				if ($gen>0) $this->pids[] = addFamily($family->getHusbId(), $gen-1);
				else $this->pids[] = $family->getHusbId();
				if ($gen>0) $this->pids[] = addFamily($family->getWifeId(), $gen-1);
				else $this->pids[] = $family->getWifeId();
				$children = $family->getChildren();
				foreach($children as $childID => $child){
					if($newpid != $child->getXref()) {
						if ($gen>0) $this->pids[] = addFamily($child->getXref(), $gen-1);
						else $this->pids[] = $child->getXref();
					}
				}
			}
		}
	}

	// sets the start year and end year to a factor of 5
	function ModifyYear($year, $key) {
		$temp = $year;
		switch ($key) {
			case 1 : //rounds beginning year
				$this->birthMod = ($year % 5);
				$year = $year - ($this->birthMod);
				if($temp == $year){
					$this->modTest = 0;
				}
				else $this->modTest = 1;
				break;
			case 2 : //rounds end year
				$this->deathMod = ($year % 5);
				//Only executed if the year needs to be modified
				if($this->deathMod > 0) {
					$this->endMod = (5 - ($this->deathMod));
				}
				else {
					$this->endMod = 0;
				}
				$year = $year + ($this->endMod);
				break;
		}
		return $year;
	}
	//Prints the time line
	function PrintTimeline($startYear, $endYear) {
		$leftPosition = 14; //start point
		$width = 8; //base width
		$height = 10; //standard height
		$tickDistance = 50; //length of one timeline section
		$top = 65; //top starting position
		$yearSpan = 5; //default zoom level
		$newStartYear = $this->ModifyYear($startYear, 1); //starting date for timeline
		$this->timelineMinYear = $newStartYear;
		$newEndYear = $this->ModifyYear($endYear, 2); //ending date for timeline
		$totalYears = $newEndYear - $newStartYear; //length of timeline
		$timelineTick = $totalYears / $yearSpan; //calculates the length of the timeline

		for ($i = 0; $i < $timelineTick; $i ++) { //prints the timeline
			echo "<div class=\"sublinks_cell\" style=\"text-align: left; position: absolute; top: ", $top, "px; left: ", $leftPosition, "px; width: ", $tickDistance, "px;\">$newStartYear<img src=\"images/timelineChunk.gif\"  alt=\"\" /></div>";  //onclick="zoomToggle('100px', '100px', '200px', '200px', this);"
			$leftPosition += $tickDistance;
			$newStartYear += $yearSpan;

		}
		echo "<div class=\"sublinks_cell\" style=\"text-align: left; position: absolute; top: ", $top, "px; left: ", $leftPosition, "px; width: ", $tickDistance, "px;\">$newStartYear</div>";
	}

	//method used to place the person boxes onto the timeline
	function fillTL($ar, $int, $top) {
		global $maxX, $zindex;

		$zindex = count($ar);

		$rows = array();
		$modFix = 0;
		if($this->modTest == 1){
			$modFix = (9 * $this->birthMod);
		}
		//base case
		if (count($ar) == 0) return $top;
		$maxY = $top;

		foreach ($ar as $key => $value) {
			//Creates appropriate color scheme to show relationships
			$this->currentsex = $value->getSex();
			if ($this->currentsex == "M"){
				$this->Mcolorindex++;
				if (!isset($this->malecolorR[$this->Mcolorindex])) $this->Mcolorindex=0;
				$this->malecolorR[$this->Mcolorindex];
				$this->Mcolorindex++;
				if (!isset($this->malecolorG[$this->Mcolorindex])) $this->Mcolorindex=0;
				$this->malecolorG[$this->Mcolorindex];
				$red = dechex($this->malecolorR[$this->Mcolorindex]);
				$green =dechex($this->malecolorR[$this->Mcolorindex]);
				if(strlen($red)<2){
					$red = "0".$red;
				}
				if(strlen($green)<2){
					$green = "0".$green;
				}

				$this->color = "#".$red.$green.dechex($this->malecolorB);
			}
			else if($this->currentsex == "F"){
				$this->Fcolorindex++;
				if (!isset($this->femalecolorG[$this->Fcolorindex])) $this->Fcolorindex = 0;
				$this->femalecolorG[$this->Fcolorindex];
				$this->Fcolorindex++;
				if (!isset($this->femalecolorB[$this->Fcolorindex])) $this->Fcolorindex = 0;
				$this->femalecolorB[$this->Fcolorindex];
				$this->color = "#".dechex($this->femalecolorR).dechex($this->femalecolorG[$this->Fcolorindex]).dechex($this->femalecolorB[$this->Fcolorindex]);
			}
			else{
				$this->color = $this->colors[$this->colorindex];
			}

			//set start position and size of person-box according to zoomfactor
			/* @var $value Person */
				$bdate=$value->getEstimatedBirthDate();
				$ddate=$value->getEstimatedDeathDate();
				$birthYear = $bdate->gregorianYear();
				$deathYear = $ddate->gregorianYear() ? $ddate->gregorianYear() : date('Y');

				$width = ($deathYear - $birthYear) * $this->zoomfactor;
				$height = 2 * $this->zoomfactor;

				$startPos = (($birthYear - $this->timelineMinYear) * $this->zoomfactor) + 14 + $modFix;
				if (stristr($value->getFullName(), "starredname"))
					$minlength = (utf8_strlen($value->getFullName())-34) * $this->zoomfactor;
				else
					$minlength = utf8_strlen($value->getFullName()) * $this->zoomfactor;

				if ($startPos > 15) {
					$startPos = (($birthYear - $this->timelineMinYear) * $this->zoomfactor) + 15 + $modFix;
					$startPos = (($birthYear - $this->timelineMinYear) * $this->zoomfactor) + 15;
					$width = (($deathYear - $birthYear) * $this->zoomfactor) - 2;
				}
				//set start position to deathyear
				$int = $deathYear;
				//set minimum width for single year lifespans
				if ($width < 10)
				{
					$width = 10;
					$int = $birthYear+1;
				}

				$lifespan = "<span dir=\"ltr\">$birthYear-</span>";
				$deathReal = $value->getDeathDate()->isOK();
				$birthReal = $value->getBirthDate()->isOK();
				if ($value->isDead() && $deathReal) $lifespan .= "<span dir=\"ltr\">$deathYear</span>";
				$lifespannumeral = $deathYear - $birthYear;

				//-- calculate a good Y top value
				$Y = $top;
				$Z = $zindex;
				$ready = false;
				while(!$ready) {
					if (!isset($rows[$Y])) {
						$ready = true;
						$rows[$Y]["x1"] = $startPos;
						$rows[$Y]["x2"] = $startPos+$width;
						$rows[$Y]["z"] = $zindex;
					}
					else {
						if ($rows[$Y]["x1"] > $startPos+$width) {
							$ready = true;
							$rows[$Y]["x1"] = $startPos;
							$Z = $rows[$Y]["z"];
						}
						else if ($rows[$Y]["x2"] < $startPos) {
							$ready = true;
							$rows[$Y]["x2"] = $startPos+$width;
							$Z = $rows[$Y]["z"];
						}
						else {
							//move down 25 pixels
							if ($this->zoomfactor > 10)$Y += 25 + $this->zoomfactor;
							else $Y += 25;
						}
					}
				}

				//Need to calculate each event and the spacing between them
				// event1 distance will be event - birthyear   that will be the distance. then each distance will chain off that

				//$event[][]  = {"Cell 1 will hold events"}{"cell2 will hold time between that and the next value"};
				//$value->add_historical_facts();
				$value->add_family_facts(false);
				$unparsedEvents = $value->getIndiFacts();
				sort_facts($unparsedEvents);

				$eventinformation = Array();
				$eventspacing = Array();
				foreach($unparsedEvents as $index=>$val) {
					$date = $val->getDate();
					if (!empty($date)) {
						$fact = $val->getTag();
						$yearsin = $date->date1->y-$birthYear;
						if ($lifespannumeral==0) {
							$lifespannumeral = 1;
						}
						$eventwidth = ($yearsin/$lifespannumeral)* 100; // percent of the lifespan before the event occured used for determining div spacing
						// figure out some schema
						$evntwdth = $eventwidth."%";
						//-- if the fact is a generic EVENt then get the qualifying TYPE
						if ($fact=="EVEN") {
							$fact = $val->getType();
						}
						$place = $val->getPlace();
						$trans = translate_fact($fact);
						if (isset($eventinformation[$evntwdth])) {
							$eventinformation[$evntwdth] .= "<br />\n".$trans."<br />\n".strip_tags($date->Display(false, '', NULL, false))." ".$place;
						} else {
							$eventinformation[$evntwdth]= $fact."-fact, ".$trans."<br />\n".strip_tags($date->Display(false, '', NULL, false))." ".$place;
						}
					}
				}

				$bdate=$value->getEstimatedBirthDate();
				$ddate=$value->getEstimatedDeathDate();
				if ($width > ($minlength +110)) {
					echo "\n<div id=\"bar_", $value->getXref(), "\" style=\"position: absolute; top:", $Y, "px; left:", $startPos, "px; width:", $width, "px; height:", $height, "px; background-color:", $this->color, "; border: solid blue 1px; z-index:$Z;\">";
					foreach($eventinformation as $evtwidth=>$val){
						echo "<div style=\"position:absolute; left:", $evtwidth, ";\"><a class=\"showit\" href=\"#\" style=\"top:-2px; font-size:10px;\"><b>";
						$text = explode("-fact, ", $val);
						$fact = $text[0];
						$val = $text[1];
						echo abbreviate_fact($fact);
						echo "</b><span>", PrintReady($val), "</span></a></div>";
					}
					$indiName = PrintReady(str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $value->getFullName()));
					echo "\n\t<table><tr>\n\t\t<td width=\"15\"><a class=\"showit\" href=\"#\"><b>";
					echo abbreviate_fact('BIRT');
					echo "</b><span>", $value->getSexImage(), $indiName, "<br/>", i18n::translate('BIRT'), " ", strip_tags($bdate->Display(false)), " ", PrintReady($value->getBirthPlace()), "</span></a></td>" ,
						"\n\t\t<td align=\"left\" width=\"100%\"><a href=\"", encode_url($value->getLinkUrl()), "\">", $value->getSexImage(), $indiName, ":  $lifespan </a></td>" ,
						"\n\t\t<td width=\"15\">";
					if ($value->isDead()) {
						if ($deathReal || $value->isDead()) {
							print "<a class=\"showit\" href=\"#\"><b>";
							echo abbreviate_fact('DEAT');
							if (!$deathReal) print "*";
							print "</b><span>".$value->getSexImage().$indiName."<br/>".i18n::translate('DEAT')." ".strip_tags($ddate->Display(false))." ".PrintReady($value->getDeathPlace())."</span></a>";
						}
					}
					print "</td></tr></table>";
					echo '</div>';

				} else {
					if ($width > $minlength +5) {
						echo "\n<div style=\"text-align: left; position: absolute; top:", $Y, "px; left:", $startPos, "px; width:", $width, "px; height:", $height, "px; background-color:", $this->color, "; border: solid blue 1px; z-index:$Z;\">";
						foreach($eventinformation as $evtwidth=>$val){
							print "<div style=\"position:absolute; left:".$evtwidth." \"><a class=\"showit\" href=\"#\" style=\"top:-2px; font-size:10px;\"><b>";
							$text = explode("-fact,", $val);
							$fact = $text[0];
							$val = $text[1];
							echo abbreviate_fact($fact);
							print "</b><span>".PrintReady($val)."</span></a></div>";
						}
						$indiName = PrintReady(str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $value->getFullName()));
						print "\n\t<table dir=\"ltr\"><tr>\n\t\t<td width=\"15\"><a class=\"showit\" href=\"#\"><b>";
						echo abbreviate_fact('BIRT');
						if (!$birthReal) print "*";
						print "</b><span>".$value->getSexImage().$indiName."<br/>".i18n::translate('BIRT')." ".strip_tags($bdate->Display(false))." ".PrintReady($value->getBirthPlace())."</span></a></td>" .
						"<td align=\"left\" width=\"100%\"><a href=\"".encode_url($value->getLinkUrl())."\">".$value->getSexImage().$indiName."</a></td>" .
						"\n\t\t<td width=\"15\">";
						if ($value->isDead()) {
							if ($deathReal || $value->isDead()) {
								print "<a class=\"showit\" href=\"#\"><b>";
								echo abbreviate_fact('DEAT');
								if (!$deathReal) print "*";
								print "</b><span>".$value->getSexImage().$indiName."<br/>".i18n::translate('DEAT')." ".strip_tags($ddate->Display(false))." ".PrintReady($value->getDeathPlace())."</span></a>";
							}
						}
						print "</td></tr></table>";
						echo '</div>';
					} else {
						echo "\n<div style=\"text-align: left; position: absolute;top:", $Y, "px; left:", $startPos, "px;width:", $width, "px; height:", $height, "px; background-color:", $this->color, "; border: solid blue 1px; z-index:$Z;\">" ;

						$indiName = PrintReady(str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $value->getFullName()));
						print "<a class=\"showit\" href=\"".encode_url($value->getLinkUrl())."\"><b>";
						echo abbreviate_fact('BIRT');
						print "</b><span>".$value->getSexImage().$indiName."<br/>".i18n::translate('BIRT')." ".strip_tags($bdate->Display(false))." ".PrintReady($value->getBirthPlace())."<br/>";
						foreach($eventinformation as $evtwidth=>$val){
							$text = explode("-fact,", $val);
							$val = $text[1];
							print $val."<br />\n";
						}
						if ($value->isDead() && $deathReal) print i18n::translate('DEAT')." ".strip_tags($ddate->Display(false))." ".PrintReady($value->getDeathPlace());
						print "</span></a>";
						echo '</div>';
					}
				}
				$zindex--;

				if ($maxX < $startPos + $width)
					$maxX = $startPos + $width;
				if ($maxY < $Y) $maxY = $Y;
		}
		return $maxY;
	}

	/**
	* check the privacy of the incoming people to make sure they can be shown
	*/
	function checkPrivacy() {
		$printed = false;
		for ($i = 0; $i < count($this->people); $i ++) {
			if (!$this->people[$i]->canDisplayDetails()) {
				if ($this->people[$i]->canDisplayName()) {
					$indiName = PrintReady(str_replace(array('<span class="starredname">', '</span>'), array('<u>', '</u>'), $this->people[$i]->getFullName()));
					print "&nbsp;<a href=\"".encode_url($this->people[$i]->getLinkUrl())."\">".$indiName."</a>";
					print_privacy_error();
					print "<br />";
					$printed = true;
				} else
					if (!$printed) {
						print_privacy_error();
						print "<br />";
					}
			}
		}
	}
}
// -- end of class
//-- load a user extended class if one exists
if (file_exists(WT_ROOT.'includes/controllers/timeline_ctrl_user.php')) {
	require_once WT_ROOT.'includes/controllers/timeline_ctrl_user.php';
} else {
	class LifespanController extends LifespanControllerRoot {
	}
}

?>
