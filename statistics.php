<?php
// Creates some statistics out of the GEDCOM information.
// We will start with the following possibilities
// number of persons -> periodes of 50 years from 1700-2000
// age -> periodes of 10 years (different for 0-1,1-5,5-10,10-20 etc)
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.
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

use WT\Auth;

define('WT_SCRIPT_NAME', 'statistics.php');
require './includes/session.php';

// check for on demand content loading
$tab  = WT_Filter::getInteger('tab', 0, 3);
$ajax = WT_Filter::getBool('ajax');

if (!$ajax) {
	$controller = new WT_Controller_Page();
	$controller->setPageTitle(WT_I18N::translate('Statistics'))
		->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
		->addInlineJavascript('
			jQuery("#statistics_chart").css("visibility", "visible");
			jQuery("#statistics_chart").tabs({
				load: function() {
					jQuery("#loading-indicator").removeClass("loading-image");
				},
				beforeLoad: function(event, ui) {
					// Only load each tab once
					if (ui.tab.data("loaded")) {
						event.preventDefault();
						return;
					}
					else {
						jQuery("#loading-indicator").addClass("loading-image");
					}
					ui.jqXHR.success(function() {
						ui.tab.data("loaded", true);
					});
				}
			});
		')
		->pageHeader();

	echo '<div id="statistics-page"><h2>', WT_I18N::translate('Statistics'), '</h2>',
		'<div id="statistics_chart">',
		'<ul>',
			'<li><a href="statistics.php?ged=', WT_GEDURL, '&amp;ajax=1&amp;tab=0">',
			'<span id="stats-indi">', WT_I18N::translate('Individuals'), '</span></a></li>',
			'<li><a href="statistics.php?ged=', WT_GEDURL, '&amp;ajax=1&amp;tab=1">',
			'<span id="stats-fam">', WT_I18N::translate('Families'), '</span></a></li>',
			'<li><a href="statistics.php?ged=', WT_GEDURL, '&amp;ajax=1&amp;tab=2">',
			'<span id="stats-other">', WT_I18N::translate('Others'), '</span></a></li>',
			'<li><a href="statistics.php?ged=', WT_GEDURL, '&amp;ajax=1&amp;tab=3">',
			'<span id="stats-own">', WT_I18N::translate('Own charts'), '</span></a></li>',
		'</ul>',
		'<div id="loading-indicator" style="margin:auto;width:100%;"></div>',
		'</div>', // statistics_chart
		'</div>', // statistics-page
	'<br><br>';
} else {
	$controller = new WT_Controller_Ajax();
	$controller
		->pageHeader()
		->addInlineJavascript('autocomplete();')
		->addInlineJavascript('jQuery("#loading-indicator").removeClass("loading-image");');
	$stats = new WT_Stats($GEDCOM);
	if ($tab==0) {
		echo '<fieldset>
		<legend>', WT_I18N::translate('Total individuals: %s', $stats->totalIndividuals()), '</legend>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Total males'), '</td>
				<td class="facts_label">', WT_I18N::translate('Total females'), '</td>
				<td class="facts_label">', WT_I18N::translate('Total living'), '</td>
				<td class="facts_label">', WT_I18N::translate('Total dead'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->totalSexMales(), '</td>
				<td class="facts_value" align="center">', $stats->totalSexFemales(), '</td>
				<td class="facts_value" align="center">', $stats->totalLiving(), '</td>
				<td class="facts_value" align="center">', $stats->totalDeceased(), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page" colspan="2">', $stats->chartSex(), '</td>
				<td class="facts_value statistics-page" colspan="2">', $stats->chartMortality(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Events'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Total births'), '</td>
				<td class="facts_label">', WT_I18N::translate('Total deaths'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->totalBirths(), '</td>
				<td class="facts_value" align="center">', $stats->totalDeaths(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Births by century'), '</td>
				<td class="facts_label">', WT_I18N::translate('Deaths by century'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->statsBirth(), '</td>
				<td class="facts_value statistics-page">', $stats->statsDeath(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Earliest birth'), '</td>
				<td class="facts_label">', WT_I18N::translate('Earliest death'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->firstBirth(), '</td>
				<td class="facts_value">', $stats->firstDeath(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Latest birth'), '</td>
				<td class="facts_label">', WT_I18N::translate('Latest death'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->lastBirth(), '</td>
				<td class="facts_value">', $stats->lastDeath(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Lifespan'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Average age at death'), '</td>
				<td class="facts_label">', WT_I18N::translate('Males'), '</td>
				<td class="facts_label">', WT_I18N::translate('Females'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->averageLifespan(true), '</td>
				<td class="facts_value" align="center">', $stats->averageLifespanMale(true), '</td>
				<td class="facts_value" align="center">', $stats->averageLifespanFemale(true), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page" colspan="3">', $stats->statsAge(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Greatest age at death'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Males'), '</td>
				<td class="facts_label">', WT_I18N::translate('Females'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topTenOldestMaleList(), '</td>
				<td class="facts_value">', $stats->topTenOldestFemaleList(), '</td>
			</tr>
		</table>
		<br>';
		if (Auth::check()) {
			echo '<b>', WT_I18N::translate('Oldest living individuals'), '</b>
			<table class="facts_table">
				<tr>
					<td class="facts_label">', WT_I18N::translate('Males'), '</td>
					<td class="facts_label">', WT_I18N::translate('Females'), '</td>
				</tr>
				<tr>
					<td class="facts_value">', $stats->topTenOldestMaleListAlive(), '</td>
					<td class="facts_value">', $stats->topTenOldestFemaleListAlive(), '</td>
				</tr>
			</table>
			<br>';
		}
		echo '<b>', WT_I18N::translate('Names'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Total surnames'), '</td>
				<td class="facts_label">', WT_I18N::translate('Total given names'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->totalSurnames(), '</td>
				<td class="facts_value" align="center">', $stats->totalGivennames(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Top surnames'), '</td>
				<td class="facts_label">', WT_I18N::translate('Top given names'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->chartCommonSurnames(), '</td>
				<td class="facts_value statistics-page">', $stats->chartCommonGiven(), '</td>
			</tr>
		</table>
		</fieldset>';
	} else if ($tab==1) {
		echo '<fieldset>
		<legend>', WT_I18N::translate('Total families: %s', $stats->totalFamilies()), '</legend>
		<b>', WT_I18N::translate('Events'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Total marriages'), '</td>
				<td class="facts_label">', WT_I18N::translate('Total divorces'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->totalMarriages(), '</td>
				<td class="facts_value" align="center">', $stats->totalDivorces(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Marriages by century'), '</td>
				<td class="facts_label">', WT_I18N::translate('Divorces by century'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->statsMarr(), '</td>
				<td class="facts_value statistics-page">', $stats->statsDiv(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Earliest marriage'), '</td>
				<td class="facts_label">', WT_I18N::translate('Earliest divorce'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->firstMarriage(), '</td>
				<td class="facts_value">', $stats->firstDivorce(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Latest marriage'), '</td>
				<td class="facts_label">', WT_I18N::translate('Latest divorce'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->lastMarriage(), '</td>
				<td class="facts_value">', $stats->lastDivorce(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Length of marriage'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Longest marriage'), ' - ', $stats->topAgeOfMarriage(), '</td>
				<td class="facts_label">', WT_I18N::translate('Shortest marriage'), ' - ', $stats->minAgeOfMarriage(), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topAgeOfMarriageFamily(), '</td>
				<td class="facts_value">', $stats->minAgeOfMarriageFamily(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Age in year of marriage'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Youngest male'), ' - ', $stats->youngestMarriageMaleAge(true), '</td>
				<td class="facts_label">', WT_I18N::translate('Youngest female'), ' - ', $stats->youngestMarriageFemaleAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->youngestMarriageMale(), '</td>
				<td class="facts_value">', $stats->youngestMarriageFemale(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Oldest male'), ' - ', $stats->oldestMarriageMaleAge(true), '</td>
				<td class="facts_label">', WT_I18N::translate('Oldest female'), ' - ', $stats->oldestMarriageFemaleAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->oldestMarriageMale(), '</td>
				<td class="facts_value">', $stats->oldestMarriageFemale(), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page" colspan="2">', $stats->statsMarrAge(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Age at birth of child'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Youngest father'), ' - ', $stats->youngestFatherAge(true), '</td>
				<td class="facts_label">', WT_I18N::translate('Youngest mother'), ' - ', $stats->youngestMotherAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->youngestFather(), '</td>
				<td class="facts_value">', $stats->youngestMother(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Oldest father'), ' - ', $stats->oldestFatherAge(true), '</td>
				<td class="facts_label">', WT_I18N::translate('Oldest mother'), ' - ', $stats->oldestMotherAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->oldestFather(), '</td>
				<td class="facts_value">', $stats->oldestMother(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Children in family'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Average number of children per family'), '</td>
				<td class="facts_label">', WT_I18N::translate('Number of families without children'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->averageChildren(), '</td>
				<td class="facts_value" align="center">', $stats->noChildrenFamilies(), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->statsChildren(), '</td>
				<td class="facts_value statistics-page">', $stats->chartNoChildrenFamilies(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Largest families'), '</td>
				<td class="facts_label">', WT_I18N::translate('Largest number of grandchildren'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topTenLargestFamilyList(), '</td>
				<td class="facts_value">', $stats->topTenLargestGrandFamilyList(), '</td>
			</tr>
		</table>
		<br>
		<b>', WT_I18N::translate('Age difference'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Age between siblings'), '</td>
				<td class="facts_label">', WT_I18N::translate('Greatest age between siblings'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topAgeBetweenSiblingsList(), '</td>
				<td class="facts_value">', $stats->topAgeBetweenSiblingsFullName(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Age between husband and wife'), '</td>
				<td class="facts_label">', WT_I18N::translate('Age between wife and husband'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->ageBetweenSpousesMFList(), '</td>
				<td class="facts_value">', $stats->ageBetweenSpousesFMList(), '</td>
			</tr>
		</table>
		</fieldset>';
	} else if ($tab==2) {
		echo '<fieldset>
		<legend>', WT_I18N::translate('Records'), ': ', $stats->totalRecords(), '</legend>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Media objects'), '</td>
				<td class="facts_label">', WT_I18N::translate('Sources'), '</td>
				<td class="facts_label">', WT_I18N::translate('Notes'), '</td>
				<td class="facts_label">', WT_I18N::translate('Repositories'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->totalMedia(), '</td>
				<td class="facts_value" align="center">', $stats->totalSources(), '</td>
				<td class="facts_value" align="center">', $stats->totalNotes(), '</td>
				<td class="facts_value" align="center">', $stats->totalRepositories(), '</td>
			</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', WT_I18N::translate('Total events'), ': ', $stats->totalEvents(), '</legend>
			<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('First event'), ' - ', $stats->firstEventType(), '</td>
				<td class="facts_label">', WT_I18N::translate('Last event'), ' - ', $stats->lastEventType(), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->firstEvent(), '</td>
				<td class="facts_value">', $stats->lastEvent(), '</td>
			</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', WT_I18N::translate('Media objects'), ': ', $stats->totalMedia(), '</legend>
			<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Media objects'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->chartMedia(), '</td>
			</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', WT_I18N::translate('Sources'), ': ', $stats->totalSources(), '</legend>
			<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Individuals with sources'), '</td>
				<td class="facts_label">', WT_I18N::translate('Families with sources'), '</td>
			</tr>
			<tr>
				<td class="facts_value" align="center">', $stats->totalIndisWithSources(), '</td>
				<td class="facts_value" align="center">', $stats->totalFamsWithSources(), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->chartIndisWithSources(), '</td>
				<td class="facts_value statistics-page">', $stats->chartFamsWithSources(), '</td>
			</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', WT_I18N::translate('Places'), ': ', $stats->totalPlaces(), '</legend>
			<table class="facts_table">
			<tr>
				<td class="facts_label">', WT_I18N::translate('Birth places'), '</td>
				<td class="facts_label">', WT_I18N::translate('Death places'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->commonBirthPlacesList(), '</td>
				<td class="facts_value">', $stats->commonDeathPlacesList(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', WT_I18N::translate('Marriage places'), '</td>
				<td class="facts_label">', WT_I18N::translate('Events in countries'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->commonMarriagePlacesList(), '</td>
				<td class="facts_value">', $stats->commonCountriesList(), '</td>
			</tr>
			<tr>
				<td class="facts_value" colspan="2">', $stats->chartDistribution(), '</td>
			</tr>
		</table>
		</fieldset>';
	} else if ($tab==3) {
		echo '<fieldset>
		<legend>', WT_I18N::translate('Create your own chart'), '</legend>';
		?>
		<script>
			function statusHide(sel) {
				var box = document.getElementById(sel);
				box.style.display = 'none';
				var box_m = document.getElementById(sel+'_m');
				if (box_m) {
					box_m.style.display = 'none';
				}
				if (sel=='map_opt') {
					var box_axes = document.getElementById('axes');
					if (box_axes) {
						box_axes.style.display = '';
					}
					var box_zyaxes = document.getElementById('zyaxes');
					if (box_zyaxes) {
						box_zyaxes.style.display = '';
					}
				}
			}
			function statusShow(sel) {
				var box = document.getElementById(sel);
				box.style.display = '';
				var box_m = document.getElementById(sel+'_m');
				if (box_m) {
					box_m.style.display = 'none';
				}
				if (sel=='map_opt') {
					var box_axes = document.getElementById('axes');
					if (box_axes) {
						box_axes.style.display = 'none';
					}
					var box_zyaxes = document.getElementById('zyaxes');
					if (box_zyaxes) {
						box_zyaxes.style.display = 'none';
					}
				}
			}
			function statusShowSurname(x) {
				if (x.value == 'surname_distribution_chart') {
					var box = document.getElementById('surname_opt');
					box.style.display = '';
				}
				else if (x.value !== 'surname_distribution_chart') {
					var box = document.getElementById('surname_opt');
					box.style.display = 'none';
				}
			}
			function statsModalDialog(url, title) {
				var form = jQuery('#own-stats-form');
				jQuery.post(form.attr('action'), form.serialize(), function(response) {
					jQuery(response).dialog({
						modal: true,
						width: 964,
						open: function() {
							var self = this;
							// Close the window when we click outside it.
							jQuery(".ui-widget-overlay").on("click", function () {
								$(self).dialog('close');
							});
						}
					});
				});
				return false;
			}
		</script>
		<?php
		echo '<div id="own-stats"><form method="post" id="own-stats-form" name="form" action="statisticsplot.php" onsubmit="statsModalDialog(\'statisticsplot.php?action=newform\', \'', WT_I18N::translate('Statistics plot'), '\'); return false;">';
		echo '<input type="hidden" name="action" value="update">';
		echo '<table width="100%">';
		if (!isset($plottype)) $plottype = 11;
		if (!isset($charttype)) $charttype = 1;
		if (!isset($plotshow)) $plotshow = 302;
		if (!isset($plotnp)) $plotnp = 201;
		if (isset($WT_SESSION->statTicks[$GEDCOM])) {
			$xasGrLeeftijden = $WT_SESSION->statTicks[$GEDCOM]['xasGrLeeftijden'];
			$xasGrMaanden = $WT_SESSION->statTicks[$GEDCOM]['xasGrMaanden'];
			$xasGrAantallen = $WT_SESSION->statTicks[$GEDCOM]['xasGrAantallen'];
			$zasGrPeriode = $WT_SESSION->statTicks[$GEDCOM]['zasGrPeriode'];
		} else {
			$xasGrLeeftijden = '1,5,10,20,30,40,50,60,70,80,90,100';
			$xasGrMaanden = '-24,-12,0,8,12,18,24,48';
			$xasGrAantallen = '1,2,3,4,5,6,7,8,9,10';
			$zasGrPeriode = '1700,1750,1800,1850,1900,1950,2000';
		}
		if (isset($WT_SESSION->statTicks1[$GEDCOM])) {
			$chart_shows = $WT_SESSION->statTicks1[$GEDCOM]['chart_shows'];
			$chart_type = $WT_SESSION->statTicks1[$GEDCOM]['chart_type'];
			$surname = $WT_SESSION->statTicks1[$GEDCOM]['surname'];
		} else {
			$chart_shows = 'world';
			$chart_type = 'indi_distribution_chart';
			$surname = $stats->getCommonSurname();
		}

		echo '<tr>
			<td class="descriptionbox width25 wrap">', WT_I18N::translate('Select chart type:'), '</td>
			<td class="optionbox">
			<input type="radio" id="stat_11" name="x-as" value="11"';
			if ($plottype == '11') echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_11">', WT_I18N::translate('Month of birth'), '</label><br>';
			echo '<input type="radio" id="stat_12" name="x-as" value="12"';
			if ($plottype == '12') echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_12">', WT_I18N::translate('Month of death'), '</label><br>';
			echo '<input type="radio" id="stat_13" name="x-as" value="13"';
			if ($plottype == "13") echo ' checked="checked"';
			echo " onclick=\"{statusChecked('z_none'); statusDisable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_13">', WT_I18N::translate('Month of marriage'), '</label><br>';
			echo '<input type="radio" id="stat_15" name="x-as" value="15"';
			if ($plottype == "15") echo ' checked="checked"';
			echo " onclick=\"{statusChecked('z_none'); statusDisable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_15">', WT_I18N::translate('Month of first marriage'), '</label><br>';
			echo '<input type="radio" id="stat_14" name="x-as" value="14"';
			if ($plottype == "14") echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_14">', WT_I18N::translate('Month of birth of first child in a relation'), '</label><br>';
			echo '<input type="radio" id="stat_17" name="x-as" value="17"';
			if ($plottype == "17") echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusShow('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_17">', WT_I18N::translate('Age related to birth year'), '</label><br>';
			echo '<input type="radio" id="stat_18" name="x-as" value="18"';
			if ($plottype == "18") echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusShow('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_18">', WT_I18N::translate('Age related to death year'), '</label><br>';
			echo '<input type="radio" id="stat_19" name="x-as" value="19"';
			if ($plottype == "19") echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusShow('x_years_m'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_19">', WT_I18N::translate('Age in year of marriage'), '</label><br>';
			echo '<input type="radio" id="stat_20" name="x-as" value="20"';
			if ($plottype == "20") echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusShow('x_years_m'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_20">', WT_I18N::translate('Age in year of first marriage'), '</label><br>';
			echo '<input type="radio" id="stat_21" name="x-as" value="21"';
			if ($plottype == "21") echo ' checked="checked"';
			echo " onclick=\"{statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusShow('x_numbers'); statusHide('map_opt');}";
			echo '"><label for="stat_21">', WT_I18N::translate('Number of children'), '</label><br>';
			echo '<input type="radio" id="stat_1" name="x-as" value="1"';
			if ($plottype == "1") echo ' checked="checked"';
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusShow('chart_type'); statusHide('axes');}";
			echo '"><label for="stat_1">', WT_I18N::translate('Individual distribution'), '</label><br>';
			echo '<input type="radio" id="stat_2" name="x-as" value="2"';
			if ($plottype == "2") echo ' checked="checked"';
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');}";
			echo '"><label for="stat_2">', WT_I18N::translate('Birth by country'), '</label><br>';
			echo '<input type="radio" id="stat_4" name="x-as" value="4"';
			if ($plottype == "4") echo ' checked="checked"';
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');}";
			echo '"><label for="stat_4">', WT_I18N::translate('Marriage by country'), '</label><br>';
			echo '<input type="radio" id="stat_3" name="x-as" value="3"';
			if ($plottype == "3") echo ' checked="checked"';
			echo " onclick=\"{statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');}";
			echo '"><label for="stat_3">', WT_I18N::translate('Death by country'), '</label><br>';
			echo '<br><div id="x_years" style="display:none;">';
			echo WT_I18N::translate('Select the desired age interval');
			echo '<br><select id="xas-grenzen-leeftijden" name="xas-grenzen-leeftijden">
				<option value="1,5,10,20,30,40,50,60,70,80,90,100" selected="selected">',
				WT_I18N::plural('interval %s year', 'interval %s years', 10, WT_I18N::number(10)), '</option>
				<option value="5,20,40,60,75,80,85,90">',
				WT_I18N::plural('interval %s year', 'interval %s years', 20, WT_I18N::number(20)), '</option>
				<option value="10,25,50,75,100">',
				WT_I18N::plural('interval %s year', 'interval %s years', 25, WT_I18N::number(25)), '</option>
			</select><br>
			</div>
			<div id="x_years_m" style="display:none;">';
			echo WT_I18N::translate('Select the desired age interval');
			echo '<br><select id="xas-grenzen-leeftijden_m" name="xas-grenzen-leeftijden_m">
				<option value="16,18,20,22,24,26,28,30,32,35,40,50" selected="selected">',
				WT_I18N::plural('interval %s year', 'interval %s years', 2, WT_I18N::number(2)), '</option>
				<option value="20,25,30,35,40,45,50">',
				WT_I18N::plural('interval %s year', 'interval %s years', 5, WT_I18N::number(5)), '</option>
			</select><br>
			</div>
			<div id="x_months" style="display:none;">';
			echo WT_I18N::translate('Select the desired age interval');
			echo '<br><select id="xas-grenzen-maanden" name="xas-grenzen-maanden">
				<option value="0,8,12,15,18,24,48" selected="selected">', WT_I18N::translate('months after marriage'), '</option>
				<option value="-24,-12,0,8,12,18,24,48">', WT_I18N::translate('months before and after marriage'), '</option>
				<option value="0,6,9,12,15,18,21,24">', WT_I18N::translate('quarters after marriage'), '</option>
				<option value="0,6,12,18,24">', WT_I18N::translate('half-year after marriage'), '</option>
			</select><br>
			</div>
			<div id="x_numbers" style="display:none;">';
			echo WT_I18N::translate('Select the desired count interval');
			echo '<br><select id="xas-grenzen-aantallen" name="xas-grenzen-aantallen">
				<option value="1,2,3,4,5,6,7,8,9,10" selected="selected">', WT_I18N::translate('interval one child'), '</option>
				<option value="2,4,6,8,10,12">', WT_I18N::translate('interval two children'), '</option>
			</select>
			<br>
			</div>
			<div id="map_opt" style="display:none;">
			<div id="chart_type">';
			echo WT_I18N::translate('Chart type');
			echo '<br><select name="chart_type" onchange="statusShowSurname(this);">
				<option value="indi_distribution_chart" selected="selected">', WT_I18N::translate('Individual distribution chart'), '</option>
				<option value="surname_distribution_chart">', WT_I18N::translate('Surname distribution chart'), '</option>
			</select>
			<br>
			</div>
			<div id="surname_opt" style="display:none;">';
			echo WT_Gedcom_Tag::getLabel('SURN'), help_link('google_chart_surname'), '<br><input data-autocomplete-type="SURN" type="text" name="SURN" size="20">';
			echo '<br>
			</div>';
			echo WT_I18N::translate('Geographical area');
			echo '<br><select id="chart_shows" name="chart_shows">
				<option value="world" selected="selected">', WT_I18N::translate('World'), '</option>
				<option value="europe">', WT_I18N::translate('Europe'), '</option>
				<option value="south_america">', WT_I18N::translate('South America'), '</option>
				<option value="asia">', WT_I18N::translate('Asia'), '</option>
				<option value="middle_east">', WT_I18N::translate('Middle East'), '</option>
				<option value="africa">', WT_I18N::translate('Africa'), '</option>
			</select>
			</div>
			</td>
			<td class="descriptionbox width20 wrap" id="axes">', WT_I18N::translate('Categories:'), '</td>
			<td class="optionbox width30" id="zyaxes">
			<input type="radio" id="z_none" name="z-as" value="300"';
			if ($plotshow == "300") echo ' checked="checked"';
			echo " onclick=\"statusDisable('zas-grenzen-periode');";
			echo '"><label for="z_none">', WT_I18N::translate('overall'), '</label><br>';
			echo '<input type="radio" id="z_sex" name="z-as" value="301"';
			if ($plotshow == "301") echo ' checked="checked"';
			echo " onclick=\"statusDisable('zas-grenzen-periode');";
			echo '"><label for="z_sex">', WT_I18N::translate('gender'), '</label><br>';
			echo '<input type="radio" id="z_time" name="z-as" value="302"';
			if ($plotshow == "302") echo ' checked="checked"';
			echo " onclick=\"statusEnable('zas-grenzen-periode');";
			echo '"><label for="z_time">', WT_I18N::translate('date periods'), '</label><br><br>';
			echo WT_I18N::translate('Date range'), '<br>';
			echo '<select id="zas-grenzen-periode" name="zas-grenzen-periode">
				<option value="1700,1750,1800,1850,1900,1950,2000" selected="selected">',
					/* I18N: from 1700 interval 50 years */ WT_I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, WT_I18N::digits(1700), WT_I18N::number(50)), '</option>
				<option value="1800,1840,1880,1920,1950,1970,2000">',
					WT_I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 40, WT_I18N::digits(1800), WT_I18N::number(40)), '</option>
				<option value="1800,1850,1900,1950,2000">',
					WT_I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, WT_I18N::digits(1800), WT_I18N::number(50)), '</option>
				<option value="1900,1920,1940,1960,1980,1990,2000">',
					WT_I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 20, WT_I18N::digits(1900), WT_I18N::number(20)), '</option>
				<option value="1900,1925,1950,1975,2000">',
					WT_I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 25, WT_I18N::digits(1900), WT_I18N::number(25)), '</option>
				<option value="1940,1950,1960,1970,1980,1990,2000">',
					WT_I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 10, WT_I18N::digits(1940), WT_I18N::number(10)), '</option>
			</select>
			<br><br>';
			echo WT_I18N::translate('results:'), '<br>';
			echo '<input type="radio" id="y_num" name="y-as" value="201"';
			if ($plotnp == "201") echo ' checked="checked"';
			echo '><label for="y_num">', WT_I18N::translate('numbers'), '</label><br>';
			echo '<input type="radio" id="y_perc" name="y-as" value="202"';
			if ($plotnp == "202") echo ' checked="checked"';
			echo '><label for="y_perc">', WT_I18N::translate('percentage'), '</label><br>';
			echo '</td>
			</tr>
			</table>
			<table width="100%">
			<tr align="center"><td>
				<br>
				<input type="submit" value="', WT_I18N::translate('show the plot'), ' ">
				<input type="reset"  value=" ', WT_I18N::translate('reset'), ' " onclick="{statusEnable(\'z_sex\'); statusHide(\'x_years\'); statusHide(\'x_months\'); statusHide(\'x_numbers\'); statusHide(\'map_opt\');}"><br>
			</td>
			</tr>
		</table>
		</form></div>
		</fieldset>';
	}
}
