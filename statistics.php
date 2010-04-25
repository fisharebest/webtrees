<?php
/**
 * Creates some statistics out of the GEDCOM information.
 * We will start with the following possibilities
 * number of persons -> periodes of 50 years from 1700-2000
 * age -> periodes of 10 years (different for 0-1,1-5,5-10,10-20 etc)
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @version $Id$
 * @package webtrees
 * @subpackage Lists
 */

define('WT_SCRIPT_NAME', 'statistics.php');
require './includes/session.php';
require_once WT_ROOT.'includes/classes/class_stats.php';

// check for on demand content loading
if (isset($_REQUEST['tab'])) {
	$tab = $_REQUEST['tab'];
	if ($tab>3) $tab = 0;
} else {
	$tab = 0;
}
$content = safe_GET('content');

if (isset($content) && $content==1) {
	/*
	* Initiate the stats object.
	*/
	$stats = new stats($GEDCOM);

	if ($tab==0) { ?>
	  <div id="pagetab0" class="<?php echo $TEXT_DIRECTION; ?>">
		<fieldset>
			<legend><?php echo i18n::translate('Total individuals'), ': ', $stats->totalIndividuals();?></legend>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Total males');?></td>
						<td class="facts_label"><?php echo i18n::translate('Total females');?></td>
						<td class="facts_label"><?php echo i18n::translate('Total living');?></td>
						<td class="facts_label"><?php echo i18n::translate('Total dead');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="center"><?php echo $stats->totalSexMales();?></td>
						<td class="facts_value" align="center"><?php echo $stats->totalSexFemales();?></td>
						<td class="facts_value" align="center"><?php echo $stats->totalLiving();?></td>
						<td class="facts_value" align="center"><?php echo $stats->totalDeceased();?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart" colspan="2"><?php echo $stats->chartSex();?></td>
						<td class="facts_value statistics_chart" colspan="2"><?php echo $stats->chartMortality();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Events');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Total births');?></td>
						<td class="facts_label"><?php echo i18n::translate('Total deaths');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="center"><?php echo $stats->totalBirths();?></td>
						<td class="facts_value" align="center"><?php echo $stats->totalDeaths();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Births by century');?></td>
						<td class="facts_label"><?php echo i18n::translate('Deaths by century');?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart"><?php echo $stats->statsBirth();?></td>
						<td class="facts_value statistics_chart"><?php echo $stats->statsDeath();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Earliest birth');?></td>
						<td class="facts_label"><?php echo i18n::translate('Earliest death');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->firstBirth();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->firstDeath();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Latest birth');?></td>
						<td class="facts_label"><?php echo i18n::translate('Latest death');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->lastBirth();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->lastDeath();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Lifespan');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Average age at death');?></td>
						<td class="facts_label"><?php echo i18n::translate('Males');?></td>
						<td class="facts_label"><?php echo i18n::translate('Females');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="center"><?php echo $stats->averageLifespan(true);?></td>
						<td class="facts_value" align="center"><?php echo $stats->averageLifespanMale(true);?></td>
						<td class="facts_value" align="center"><?php echo $stats->averageLifespanFemale(true);?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart" colspan="3"><?php echo $stats->statsAge();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Greatest age at death');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Males');?></td>
						<td class="facts_label"><?php echo i18n::translate('Females');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topTenOldestMaleList();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topTenOldestFemaleList();?></td>
					</tr>
				</table>
				<br />
				<?php
				if (WT_USER_ID) {
				?>
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Oldest living people');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Males');?></td>
						<td class="facts_label"><?php echo i18n::translate('Females');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topTenOldestMaleListAlive();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topTenOldestFemaleListAlive();?></td>
					</tr>
				</table>
				<br />
				<?php
				}
				?>
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Names');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Total surnames');?></td>
						<td class="facts_label"><?php echo i18n::translate('Total given names');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="center"><?php echo $stats->totalSurnames();?></td>
						<td class="facts_value" align="center"><?php echo $stats->totalGivennames();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Top surnames');?></td>
						<td class="facts_label"><?php echo i18n::translate('Top given names');?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart"><?php echo $stats->chartCommonSurnames();?></td>
						<td class="facts_value statistics_chart"><?php echo $stats->chartCommonGiven();?></td>
					</tr>
				</table>
			</fieldset>
		<br />
	  </div>
	<?php }
	if ($tab==1) { ?>
	  <div id="pagetab1" class="<?php echo $TEXT_DIRECTION; ?>">
		<fieldset>
			<legend><?php echo i18n::translate('Total families'), ': ', $stats->totalFamilies();?></legend>
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Events');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Total marriages');?></td>
						<td class="facts_label"><?php echo i18n::translate('Total divorces');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="center"><?php echo $stats->totalMarriages();?></td>
						<td class="facts_value" align="center"><?php echo $stats->totalEventsDivorce();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Marriages by century');?></td>
						<td class="facts_label"><?php echo i18n::translate('Divorces by century');?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart"><?php echo $stats->statsMarr();?></td>
						<td class="facts_value statistics_chart"><?php echo $stats->statsDiv();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Earliest marriage');?></td>
						<td class="facts_label"><?php echo i18n::translate('Earliest divorce');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->firstMarriage();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->firstDivorce();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Latest marriage');?></td>
						<td class="facts_label"><?php echo i18n::translate('Latest divorce');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->lastMarriage();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->lastDivorce();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Length of marriage');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Longest marriage')." - ".$stats->topAgeOfMarriage());?></td>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Shortest marriage')." - ".$stats->minAgeOfMarriage());?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topAgeOfMarriageFamily();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->minAgeOfMarriageFamily();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Age in year of marriage');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Youngest male')." - ".$stats->youngestMarriageMaleAge(true));?></td>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Youngest female')." - ".$stats->youngestMarriageFemaleAge(true));?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->youngestMarriageMale();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->youngestMarriageFemale();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Oldest male')." - ".$stats->oldestMarriageMaleAge(true));?></td>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Oldest female')." - ".$stats->oldestMarriageFemaleAge(true));?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->oldestMarriageMale();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->oldestMarriageFemale();?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart" colspan="2"><?php echo $stats->statsMarrAge();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Age at birth of child');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Youngest father')." - ".$stats->youngestFatherAge(true));?></td>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Youngest mother')." - ".$stats->youngestMotherAge(true));?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->youngestFather();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->youngestMother();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Oldest father')." - ".$stats->oldestFatherAge(true));?></td>
						<td class="facts_label"><?php echo PrintReady(i18n::translate('Oldest mother')." - ".$stats->oldestMotherAge(true));?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->oldestFather();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->oldestMother();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Children in family');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Average number of children per family');?></td>
						<td class="facts_label"><?php echo i18n::translate('Number of families without children');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="center"><?php echo $stats->averageChildren();?></td>
						<td class="facts_value" align="center"><?php echo $stats->noChildrenFamilies();?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart"><?php echo $stats->statsChildren();?></td>
						<td class="facts_value statistics_chart"><?php echo $stats->chartNoChildrenFamilies();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Largest families');?></td>
						<td class="facts_label"><?php echo i18n::translate('Largest number of grandchildren');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topTenLargestFamilyList();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topTenLargestGrandFamilyList();?></td>
					</tr>
					<tr>
						<td class="facts_value statistics_chart" colspan="2"><?php echo $stats->chartLargestFamilies();?></td>
					</tr>
				</table>
				<br />
				<div align="<?php echo $TEXT_DIRECTION;?>"><b><?php echo i18n::translate('Age difference');?></b></div>
				<table class="facts_table">
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Age between siblings');?></td>
						<td class="facts_label"><?php echo i18n::translate('Greatest age between siblings');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topAgeBetweenSiblingsList();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->topAgeBetweenSiblingsFullName();?></td>
					</tr>
					<tr>
						<td class="facts_label"><?php echo i18n::translate('Age between husband and wife');?></td>
						<td class="facts_label"><?php echo i18n::translate('Age between wife and husband');?></td>
					</tr>
					<tr>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->ageBetweenSpousesMFList();?></td>
						<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->ageBetweenSpousesFMList();?></td>
					</tr>
				</table>
		</fieldset>
		<br />
	  </div>
	<?php }
	else if ($tab==2) { ?>
	  <div id="pagetab2" class="<?php echo $TEXT_DIRECTION; ?>">
		<fieldset>
			<legend><?php echo i18n::translate('Records'), ': ', $stats->totalRecords();?></legend>
				<table class="facts_table">
				<tr>
					<td class="facts_label"><?php echo i18n::translate('Media objects');?></td>
					<td class="facts_label"><?php echo i18n::translate('Sources');?></td>
					<td class="facts_label"><?php echo i18n::translate('Notes');?></td>
					<td class="facts_label"><?php echo i18n::translate('Other records');?></td>
				</tr>
				<tr>
					<td class="facts_value" align="center"><?php echo $stats->totalMedia();?></td>
					<td class="facts_value" align="center"><?php echo $stats->totalSources();?></td>
					<td class="facts_value" align="center"><?php echo $stats->totalNotes();?></td>
					<td class="facts_value" align="center"><?php echo $stats->totalOtherRecords();?></td>
				</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo i18n::translate('Total events'), ': ', $stats->totalEvents();?></legend>
				<table class="facts_table">
				<tr>
					<td class="facts_label"><?php echo i18n::translate('First event'), ' - ', $stats->firstEventType();?></td>
					<td class="facts_label"><?php echo i18n::translate('Last event'), ' - ', $stats->lastEventType();?></td>
				</tr>
				<tr>
					<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->firstEvent();?></td>
					<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->lastEvent();?></td>
				</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo i18n::translate('Media objects'), ': ', $stats->totalMedia();?></legend>
				<table class="facts_table">
				<tr>
					<td class="facts_label"><?php echo i18n::translate('Media');?></td>
				</tr>
				<tr>
					<td class="facts_value statistics_chart"><?php echo $stats->chartMedia();?></td>
				</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo i18n::translate('Sources'), ': ', $stats->totalSources();?></legend>
				<table class="facts_table">
				<tr>
					<td class="facts_label"><?php echo i18n::translate('Individuals with sources'); ?></td>
					<td class="facts_label"><?php echo i18n::translate('Families with sources'); ?></td>
				</tr>
				<tr>
					<td class="facts_value" align="center"><?php echo $stats->totalIndisWithSources();?></td>
					<td class="facts_value" align="center"><?php echo $stats->totalFamsWithSources();?></td>
				</tr>
				<tr>
					<td class="facts_value statistics_chart"><?php echo $stats->chartIndisWithSources();?></td>
					<td class="facts_value statistics_chart"><?php echo $stats->chartFamsWithSources();?></td>
				</tr>
				</table>
			</fieldset>
			<fieldset>
				<legend><?php echo i18n::translate('Places'), ': ', $stats->totalPlaces();?></legend>
				<table class="facts_table">
				<tr>
					<td class="facts_label"><?php echo i18n::translate('Birth places');?></td>
					<td class="facts_label"><?php echo i18n::translate('Death places');?></td>
				</tr>
				<tr>
					<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->commonBirthPlacesList();?></td>
					<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->commonDeathPlacesList();?></td>
				</tr>
				<tr>
					<td class="facts_label"><?php echo i18n::translate('Marriage places');?></td>
					<td class="facts_label"><?php echo i18n::translate('Events in countries');?></td>
				</tr>
				<tr>
					<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->commonMarriagePlacesList();?></td>
					<td class="facts_value" align="<?php echo $TEXT_DIRECTION;?>"><?php echo $stats->commonCountriesList();?></td>
				</tr>
				<tr>
					<td class="facts_value" colspan="2"><?php echo $stats->chartDistribution();?></td>
				</tr>
			</table>
		</fieldset>
		<br />
	  </div>
	<?php }
	else if ($tab==3) { ?>
	  <div id="pagetab3" class="<?php echo $TEXT_DIRECTION; ?>">
		<fieldset>
		<legend><?php echo i18n::translate('Create your own chart'); ?></legend>
		<?php 
		require_once WT_ROOT.'includes/functions/functions_places.php';

		if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
		?>
		<script language="JavaScript" type="text/javascript">
		<!--
			function statusHide(sel) {
				var box = document.getElementById(sel);
				box.style.display = "none";
				var box_m = document.getElementById(sel+"_m");
				if (box_m) box_m.style.display = "none";
				if (sel=="map_opt") {
					var box_axes = document.getElementById("axes");
					if (box_axes) box_axes.style.display = "";
					var box_zyaxes = document.getElementById("zyaxes");
					if (box_zyaxes) box_zyaxes.style.display = "";
				}
			}
			function statusShow(sel) {
				var box = document.getElementById(sel);
				box.style.display = "";
				var box_m = document.getElementById(sel+"_m");
				if (box_m) box_m.style.display = "none";
				if (sel=="map_opt") {
					var box_axes = document.getElementById("axes");
					if (box_axes) box_axes.style.display = "none";
					var box_zyaxes = document.getElementById("zyaxes");
					if (box_zyaxes) box_zyaxes.style.display = "none";
				}
			}
			function statusShowSurname(x) {
				if (x.value == "surname_distribution_chart") {
					var box = document.getElementById("surname_opt");
					box.style.display = "";
				}
				else if (x.value !== "surname_distribution_chart") {
					var box = document.getElementById("surname_opt");
					box.style.display = "none";
				}
			}
			function openPopup(){
				window.open("", "_popup", "top=50, left=50, width=950, height=480, scrollbars=0, scrollable=0");
				return true;
			}
		//-->
		</script>
		<?php

		if (!isset($_SESSION[$GEDCOM."nrpers"])) {
			$nrpers = 0;
		}
		else {
			$nrpers = $_SESSION[$GEDCOM."nrpers"];
			$nrfam = $_SESSION[$GEDCOM."nrfam"];
			$nrmale = $_SESSION[$GEDCOM."nrmale"];
			$nrfemale = $_SESSION[$GEDCOM."nrfemale"];
		}

		$_SESSION[$GEDCOM."nrpers"] = $stats->totalIndividuals();
		$_SESSION[$GEDCOM."nrfam"] = $stats->totalFamilies();
		$_SESSION[$GEDCOM."nrmale"] = $stats->totalSexMales();
		$_SESSION[$GEDCOM."nrfemale"] = $stats->totalSexFemales();

		echo "\n";
		echo '<form method="post" name="form" action="statisticsplot.php?action=newform" target="_popup" onsubmit="return openPopup()">';
		echo "\n";
		echo '<input type="hidden" name="action" value="update" />';
		echo "\n";
		echo '<table width="100%">';

		if (!isset($plottype)) $plottype = 11;
		if (!isset($charttype)) $charttype = 1;
		if (!isset($plotshow)) $plotshow = 302;
		if (!isset($plotnp)) $plotnp = 201;

		if (isset($_SESSION[$GEDCOM."statTicks"])) {
			$xasGrLeeftijden = $_SESSION[$GEDCOM."statTicks"]["xasGrLeeftijden"];
			$xasGrMaanden = $_SESSION[$GEDCOM."statTicks"]["xasGrMaanden"];
			$xasGrAantallen = $_SESSION[$GEDCOM."statTicks"]["xasGrAantallen"];
			$zasGrPeriode = $_SESSION[$GEDCOM."statTicks"]["zasGrPeriode"];
		}
		else {
			$xasGrLeeftijden = "1,5,10,20,30,40,50,60,70,80,90,100";
			$xasGrMaanden = "-24,-12,0,8,12,18,24,48";
			$xasGrAantallen = "1,2,3,4,5,6,7,8,9,10";
			$zasGrPeriode = "1700,1750,1800,1850,1900,1950,2000";
		}
		if (isset($_SESSION[$GEDCOM."statTicks1"])) {
			$chart_shows = $_SESSION[$GEDCOM."statTicks1"]["chart_shows"];
			$chart_type = $_SESSION[$GEDCOM."statTicks1"]["chart_type"];
			$surname = $_SESSION[$GEDCOM."statTicks1"]["surname"];
		}
		else {
			$chart_shows = "world";
			$chart_type = "indi_distribution_chart";
			$surname = $stats->getCommonSurname();
		}

		?>
			<tr>
				<td class="descriptionbox" colspan="4"><?php echo i18n::translate('Fill in the following parameters for the plot'), help_link('stat'); ?></td>
			</tr>
			<tr>
			<td class="descriptionbox width25 wrap"><?php echo i18n::translate('Select chart type:'), help_link('stat_x'); ?></td>
			<td class="optionbox">
			<input type="radio" id="stat_11" name="x-as" value="11"
			<?php
			if ($plottype == "11") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_11\">", i18n::translate('Month of birth'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_12\" name=\"x-as\" value=\"12\"";
			if ($plottype == "12") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_12\">", i18n::translate('Month of death'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_13\" name=\"x-as\" value=\"13\"";
			if ($plottype == "13") echo " checked=\"checked\"";
			echo " onclick=\"{statusChecked('z_none'); statusDisable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_13\">", i18n::translate('Month of marriage'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_15\" name=\"x-as\" value=\"15\"";
			if ($plottype == "15") echo " checked=\"checked\"";
			echo " onclick=\"{statusChecked('z_none'); statusDisable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_15\">", i18n::translate('Month of first marriage'), "</label><br />";
		//	echo "<input type=\"radio\" id=\"stat_14\" name=\"x-as\" value=\"14\"";
		//	if ($plottype == "14") echo " checked=\"checked\"";
		//	echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
		//	echo "\" /><label for=\"stat_14\">", i18n::translate('Month of birth of first child in a relation'), "</label><br />";
		//	echo "<input type=\"radio\" id=\"stat_16\" name=\"x-as\" value=\"16\"";
		//	if ($plottype == "16") echo " checked=\"checked\"";
		//	echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusShow('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
		//	echo "\" /><label for=\"stat_16\">", i18n::translate('Months between marriage and first child'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_17\" name=\"x-as\" value=\"17\"";
			if ($plottype == "17") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusShow('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_17\">", i18n::translate('Age related to birth year'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_18\" name=\"x-as\" value=\"18\"";
			if ($plottype == "18") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusShow('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_18\">", i18n::translate('Age related to death year'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_19\" name=\"x-as\" value=\"19\"";
			if ($plottype == "19") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusShow('x_years_m'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_19\">", i18n::translate('Age in year of marriage'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_20\" name=\"x-as\" value=\"20\"";
			if ($plottype == "20") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusShow('x_years_m'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_20\">", i18n::translate('Age in year of first marriage'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_21\" name=\"x-as\" value=\"21\"";
			if ($plottype == "21") echo " checked=\"checked\"";
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusShow('x_numbers'); statusHide('map_opt');}";
			echo "\" /><label for=\"stat_21\">", i18n::translate('Number of children'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_1\" name=\"x-as\" value=\"1\"";
			if ($plottype == "1") echo " checked=\"checked\"";
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusShow('chart_type'); statusHide('axes');}";
			echo "\" /><label for=\"stat_1\">", i18n::translate('Individual distribution'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_2\" name=\"x-as\" value=\"2\"";
			if ($plottype == "2") echo " checked=\"checked\"";
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');}";
			echo "\" /><label for=\"stat_2\">", i18n::translate('Birth by country'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_4\" name=\"x-as\" value=\"4\"";
			if ($plottype == "4") echo " checked=\"checked\"";
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');}";
			echo "\" /><label for=\"stat_4\">", i18n::translate('Marriage by country'), "</label><br />";
			echo "<input type=\"radio\" id=\"stat_3\" name=\"x-as\" value=\"3\"";
			if ($plottype == "3") echo " checked=\"checked\"";
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');}";
			echo "\" /><label for=\"stat_3\">", i18n::translate('Death by country'), "</label><br />";
			?>
			<br />
			<div id="x_years" style="display:none;">
			<?php
			echo i18n::translate('boundaries for ages:'), help_link('stat_gax');
			?>
			<br /><select id="xas-grenzen-leeftijden" name="xas-grenzen-leeftijden">
				<option value="1,5,10,20,30,40,50,60,70,80,90,100" selected="selected"><?php
					echo i18n::plural('interval %d year', 'interval %d years', 10, 10);?></option>
				<option value="5,20,40,60,75,80,85,90"><?php
					echo i18n::plural('interval %d year', 'interval %d years', 20, 20);?></option>
				<option value="10,25,50,75,100"><?php
					echo i18n::plural('interval %d year', 'interval %d years', 25, 25);?></option>
			</select><br />
			</div>
			<div id="x_years_m" style="display:none;">
			<?php
			echo i18n::translate('boundaries for ages:'), help_link('stat_gbx');
			?>
			<br /><select id="xas-grenzen-leeftijden_m" name="xas-grenzen-leeftijden_m">
				<option value="16,18,20,22,24,26,28,30,32,35,40,50" selected="selected"><?php
					echo i18n::plural('interval %d year', 'interval %d years', 2, 2);?></option>
				<option value="20,25,30,35,40,45,50"><?php
					echo i18n::plural('interval %d year', 'interval %d years', 5, 5);?></option>
			</select><br />
			</div>
			<div id="x_months" style="display:none;">
			<?php
			echo i18n::translate('boundaries for month:'), help_link('stat_gwx');
			?>
			<br /><select id="xas-grenzen-maanden" name="xas-grenzen-maanden">
				<option value="0,8,12,15,18,24,48" selected="selected"><?php echo i18n::translate('months after marriage');?></option>
				<option value="-24,-12,0,8,12,18,24,48"><?php echo i18n::translate('months before and after marriage');?></option>
				<option value="0,6,9,12,15,18,21,24"><?php echo i18n::translate('quarters after marriage');?></option>
				<option value="0,6,12,18,24"><?php echo i18n::translate('half-year after marriage');?></option>
			</select><br />
			</div>
			<div id="x_numbers" style="display:none;">
			<?php
			echo i18n::translate('boundaries for numbers:'), help_link('stat_gcx');
			?>
			<br /><select id="xas-grenzen-aantallen" name="xas-grenzen-aantallen">
				<option value="1,2,3,4,5,6,7,8,9,10" selected="selected"><?php echo i18n::translate('interval one child');?></option>
				<option value="2,4,6,8,10,12"><?php echo i18n::translate('interval two children');?></option>
			</select>
			<br />
			</div>
			<div id="map_opt" style="display:none;">
			<div id="chart_type">
			<?php
			echo i18n::translate('Map type'), help_link('chart_type');
			?>
			<br /><select name="chart_type" onchange="statusShowSurname(this);">
				<option value="indi_distribution_chart" selected="selected">
					<?php echo i18n::translate('Individual distribution chart'); ?></option>
				<option value="surname_distribution_chart">
					<?php echo i18n::translate('Surname distribution chart'); ?></option>
			</select>
			<br />
			</div>
			<div id="surname_opt" style="display:none;">
			<?php
			echo translate_fact('SURN'), help_link('google_chart_surname'), '<br /><input type="text" name="SURN" size="20" />';
			?>
			<br />
			</div>
			<?php
			echo i18n::translate('Geographical area'), help_link('chart_area');
			?>
			<br /><select id="chart_shows" name="chart_shows">
				<option value="world" selected="selected"><?php echo i18n::translate('World'); ?></option>
				<option value="europe"><?php echo i18n::translate('Europe'); ?></option>
				<option value="south_america"><?php echo i18n::translate('South America'); ?></option>
				<option value="asia"><?php echo i18n::translate('Asia'); ?></option>
				<option value="middle_east"><?php echo i18n::translate('Middle East'); ?></option>
				<option value="africa"><?php echo i18n::translate('Africa'); ?></option>
			</select>
			</div>
			</td>
			<td class="descriptionbox width20 wrap" id="axes"><?php echo i18n::translate('Categories:'), help_link('stat_z'); ?></td>
			<td class="optionbox width30" id="zyaxes">
			<input type="radio" id="z_none" name="z-as" value="300"
			<?php
			if ($plotshow == "300") echo " checked=\"checked\"";
			echo " onclick=\"statusDisable('zas-grenzen-periode');";
			echo "\" /><label for=\"z_none\">", i18n::translate('overall'), "</label><br />";
			echo "<input type=\"radio\" id=\"z_sex\" name=\"z-as\" value=\"301\"";
			if ($plotshow == "301") echo " checked=\"checked\"";
			echo " onclick=\"statusDisable('zas-grenzen-periode');";
			echo "\" /><label for=\"z_sex\">", i18n::translate('gender'), "</label><br />";
			echo "<input type=\"radio\" id=\"z_time\" name=\"z-as\" value=\"302\"";
			if ($plotshow == "302") echo " checked=\"checked\"";
			echo " onclick=\"statusEnable('zas-grenzen-periode');";
			echo "\" /><label for=\"z_time\">", i18n::translate('date periods'), "</label><br /><br />";
			echo i18n::translate('boundaries for date periods:'), help_link('stat_gwz'), '<br />';

			?>
			<select id="zas-grenzen-periode" name="zas-grenzen-periode">
				<option value="1700,1750,1800,1850,1900,1950,2000" selected="selected"><?php
					// I18N: from 1700 interval 50 years
					echo i18n::plural('from %2$d interval %1$d year', 'from %2$d interval %1$d years', 50, 50, 1700);?></option>
				<option value="1800,1840,1880,1920,1950,1970,2000"><?php
					echo i18n::plural('from %2$d interval %1$d year', 'from %2$d interval %1$d years', 40, 40, 1800);?></option>
				<option value="1800,1850,1900,1950,2000"><?php
					echo i18n::plural('from %2$d interval %1$d year', 'from %2$d interval %1$d years', 50, 50, 1800);?></option>
				<option value="1900,1920,1940,1960,1980,1990,2000"><?php
					echo i18n::plural('from %2$d interval %1$d year', 'from %2$d interval %1$d years', 20, 20, 1900);?></option>
				<option value="1900,1925,1950,1975,2000"><?php
					echo i18n::plural('from %2$d interval %1$d year', 'from %2$d interval %1$d years', 25, 25, 1900);?></option>
				<option value="1940,1950,1960,1970,1980,1990,2000"><?php
					echo i18n::plural('from %2$d interval %1$d year', 'from %2$d interval %1$d years', 10, 10, 1940);?></option>
			</select>
			<br /><br />
			<?php
			echo i18n::translate('results:'), help_link('stat_y'), '<br />';
			?>
			<input type="radio" id="y_num" name="y-as" value="201"
			<?php
			if ($plotnp == "201") echo " checked=\"checked\"";
			echo " /><label for=\"y_num\">", i18n::translate('numbers'), "</label><br />";
			echo "<input type=\"radio\" id=\"y_perc\" name=\"y-as\" value=\"202\"";
			if ($plotnp == "202") echo " checked=\"checked\"";
			echo " /><label for=\"y_perc\">", i18n::translate('percentage'), "</label><br />";
			?>
			</td>
			</tr>
			</table>
			<table width="100%">
			<tr align="center"><td>
				<br/>
				<input type="submit" value="<?php echo i18n::translate('show the plot'); ?> " onclick="closeHelp();" />
				<input type="reset"  value=" <?php echo i18n::translate('reset'); ?> " onclick="{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}" /><br/>
			</td>
			</tr>
		</table>
		</form>
		<?php
		$_SESSION["plottype"]=$plottype;
		$_SESSION["plotshow"]=$plotshow;
		$_SESSION["plotnp"]=$plotnp;
		?>
		</fieldset>
		<br />
	  </div>
	<?php } 
} else {
	print_header(i18n::translate('Statistics'));
	$ble = false;
	?>
	<h2 class="center"><?php echo i18n::translate('Statistics'); ?></h2>
	<?php global $TEXT_DIRECTION;
	if ($TEXT_DIRECTION=='rtl') $align='right';
	else $align='left';	
	?>
	<script type="text/javascript" src="js/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery-ui.min.js"></script>
	<link type="text/css" href="js/jquery/css/jquery-ui.custom.css" rel="Stylesheet" />
	<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme.css" rel="Stylesheet" />
	<?php if ($TEXT_DIRECTION=='rtl') {?>
	<link type="text/css" href="<?php echo WT_THEME_DIR?>jquery/jquery-ui_theme_rtl.css" rel="Stylesheet" />
	<?php }?>
	<script type="text/javascript">
	//<![CDATA[
	  jQuery.noConflict();
	  jQuery(document).ready(function(){
	  jQuery("#tabbar").tabs();
	  });
	//]]>
	</script>
	<script type="text/javascript">
	//<![CDATA[
	var selectedTab = "";
	if (selectedTab != "" && selectedTab != "undefined" && selectedTab != null) {
		var selectedTab = selectedTab;
	}else{
		var selectedTab = <?php echo $tab; ?>;
	}
	var tabCache = new Array();

	jQuery(document).ready(function(){
		// TODO: change images directory when the common images will be deleted.
		// jQuery('#tabs').tabs({ spinner: '<img src=\"<?php echo $WT_IMAGE_DIR; ?>/loading.gif\" height=\"18\" border=\"0\" />' });
		jQuery('#tabs').tabs({ spinner: '<img src=\"images/loading.gif\" height=\"18\" border=\"0\" />' });
		jQuery("#tabs").tabs({ cache: true, selected: selectedTab });
		var $tabs = jQuery('#tabs');
		jQuery('#tabs').bind('tabsshow', function(event, ui) {
			selectedTab = ui.tab.name;
			tabCache[selectedTab] = true;
		});
	});
	//]]>
	</script>
	<div class="width90" style="margin:0 auto;">
	  <div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
			<li class="ui-state-default ui-corner-top">
				<a name="pagetab0" title="<?php echo i18n::translate('Individuals');?>" href="statistics.php?ged=<?php echo $GEDCOM; ?>&content=1&tab=0"><span>
					<?php echo i18n::translate('Individuals');?></span></a>
			</li>
			<li class="ui-state-default ui-corner-top">
				<a name="pagetab1" title="<?php echo i18n::translate('Families');?>" href="statistics.php?ged=<?php echo $GEDCOM; ?>&content=1&tab=1"><span>
					<?php echo i18n::translate('Families');?></span></a>
			</li>
			<li class="ui-state-default ui-corner-top">
				<a name="pagetab2" title="<?php echo i18n::translate('Others');?>" href="statistics.php?ged=<?php echo $GEDCOM; ?>&content=1&tab=2"><span>
					<?php echo i18n::translate('Others');?></span>	</a>
			</li>
			<li class="ui-state-default ui-corner-top">
				<a name="pagetab3" title="<?php echo i18n::translate('Own charts');?>" href="statistics.php?ged=<?php echo $GEDCOM; ?>&content=1&tab=3"><span>
					<?php echo i18n::translate('Own charts');?></span></a>
			</li>
		</ul>
	  </div> <!-- tabs -->
	</div> <!--  end -->
	<?php
	$ble = true;
	echo "<br/><br/>";
	print_footer();
}
?>
