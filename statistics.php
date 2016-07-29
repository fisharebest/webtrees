<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\AjaxController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'statistics.php');
require './includes/session.php';

// check for on demand content loading
$tab  = Filter::getInteger('tab', 0, 3);
$ajax = Filter::getBool('ajax');

if (!$ajax) {
	$controller = new PageController;
	$controller
		->restrictAccess(Module::isActiveChart($WT_TREE, 'statistics_chart'))
		->setPageTitle(I18N::translate('Statistics'))
		->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
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

	echo '<div id="statistics-page"><h2>', I18N::translate('Statistics'), '</h2>',
	'<div id="statistics_chart">',
	'<ul>',
	'<li><a href="statistics.php?ged=', $WT_TREE->getNameUrl(), '&amp;ajax=1&amp;tab=0">',
	'<span id="stats-indi">', I18N::translate('Individuals'), '</span></a></li>',
	'<li><a href="statistics.php?ged=', $WT_TREE->getNameUrl(), '&amp;ajax=1&amp;tab=1">',
	'<span id="stats-fam">', I18N::translate('Families'), '</span></a></li>',
	'<li><a href="statistics.php?ged=', $WT_TREE->getNameUrl(), '&amp;ajax=1&amp;tab=2">',
	'<span id="stats-other">', I18N::translate('Others'), '</span></a></li>',
	'<li><a href="statistics.php?ged=', $WT_TREE->getNameUrl(), '&amp;ajax=1&amp;tab=3">',
	'<span id="stats-own">', I18N::translate('Own charts'), '</span></a></li>',
	'</ul>',
	'<div id="loading-indicator" style="margin:auto;width:100%;"></div>',
	'</div>', // statistics_chart
	'</div>', // statistics-page
	'<br><br>';
} else {
	$controller = new AjaxController;
	$controller
		->pageHeader()
		->addInlineJavascript('autocomplete();')
		->addInlineJavascript('jQuery("#loading-indicator").removeClass("loading-image");');
	$stats = new Stats($WT_TREE);
	if ($tab == 0) {
		echo '<fieldset>
		<legend>', I18N::translate('Total individuals: %s', $stats->totalIndividuals()), '</legend>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Total males'), '</td>
				<td class="facts_label">', I18N::translate('Total females'), '</td>
				<td class="facts_label">', I18N::translate('Total living'), '</td>
				<td class="facts_label">', I18N::translate('Total dead'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->totalSexMales(), '</td>
				<td class="facts_value statistics-page">', $stats->totalSexFemales(), '</td>
				<td class="facts_value statistics-page">', $stats->totalLiving(), '</td>
				<td class="facts_value statistics-page">', $stats->totalDeceased(), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page" colspan="2">', $stats->chartSex(), '</td>
				<td class="facts_value statistics-page" colspan="2">', $stats->chartMortality(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Events'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Total births'), '</td>
				<td class="facts_label">', I18N::translate('Total deaths'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->totalBirths(), '</td>
				<td class="facts_value statistics-page">', $stats->totalDeaths(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Births by century'), '</td>
				<td class="facts_label">', I18N::translate('Deaths by century'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->statsBirth(), '</td>
				<td class="facts_value statistics-page">', $stats->statsDeath(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Earliest birth'), '</td>
				<td class="facts_label">', I18N::translate('Earliest death'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->firstBirth(), '</td>
				<td class="facts_value">', $stats->firstDeath(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Latest birth'), '</td>
				<td class="facts_label">', I18N::translate('Latest death'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->lastBirth(), '</td>
				<td class="facts_value">', $stats->lastDeath(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Lifespan'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Average age at death'), '</td>
				<td class="facts_label">', I18N::translate('Males'), '</td>
				<td class="facts_label">', I18N::translate('Females'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->averageLifespan(true), '</td>
				<td class="facts_value statistics-page">', $stats->averageLifespanMale(true), '</td>
				<td class="facts_value statistics-page">', $stats->averageLifespanFemale(true), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page" colspan="3">', $stats->statsAge(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Greatest age at death'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Males'), '</td>
				<td class="facts_label">', I18N::translate('Females'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topTenOldestMaleList(), '</td>
				<td class="facts_value">', $stats->topTenOldestFemaleList(), '</td>
			</tr>
		</table>
		<br>';
		if (Auth::check()) {
			echo '<b>', I18N::translate('Oldest living individuals'), '</b>
			<table class="facts_table">
				<tr>
					<td class="facts_label">', I18N::translate('Males'), '</td>
					<td class="facts_label">', I18N::translate('Females'), '</td>
				</tr>
				<tr>
					<td class="facts_value">', $stats->topTenOldestMaleListAlive(), '</td>
					<td class="facts_value">', $stats->topTenOldestFemaleListAlive(), '</td>
				</tr>
			</table>
			<br>';
		}
		echo '<b>', I18N::translate('Names'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Total surnames'), '</td>
				<td class="facts_label">', I18N::translate('Total given names'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->totalSurnames(), '</td>
				<td class="facts_value statistics-page">', $stats->totalGivennames(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Top surnames'), '</td>
				<td class="facts_label">', I18N::translate('Top given names'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->chartCommonSurnames(), '</td>
				<td class="facts_value statistics-page">', $stats->chartCommonGiven(), '</td>
			</tr>
		</table>
		</fieldset>';
	} elseif ($tab == 1) {
		echo '<fieldset>
		<legend>', I18N::translate('Total families: %s', $stats->totalFamilies()), '</legend>
		<b>', I18N::translate('Events'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Total marriages'), '</td>
				<td class="facts_label">', I18N::translate('Total divorces'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->totalMarriages(), '</td>
				<td class="facts_value statistics-page">', $stats->totalDivorces(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Marriages by century'), '</td>
				<td class="facts_label">', I18N::translate('Divorces by century'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->statsMarr(), '</td>
				<td class="facts_value statistics-page">', $stats->statsDiv(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Earliest marriage'), '</td>
				<td class="facts_label">', I18N::translate('Earliest divorce'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->firstMarriage(), '</td>
				<td class="facts_value">', $stats->firstDivorce(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Latest marriage'), '</td>
				<td class="facts_label">', I18N::translate('Latest divorce'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->lastMarriage(), '</td>
				<td class="facts_value">', $stats->lastDivorce(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Length of marriage'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Longest marriage'), ' - ', $stats->topAgeOfMarriage(), '</td>
				<td class="facts_label">', I18N::translate('Shortest marriage'), ' - ', $stats->minAgeOfMarriage(), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topAgeOfMarriageFamily(), '</td>
				<td class="facts_value">', $stats->minAgeOfMarriageFamily(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Age in year of marriage'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Youngest male'), ' - ', $stats->youngestMarriageMaleAge(true), '</td>
				<td class="facts_label">', I18N::translate('Youngest female'), ' - ', $stats->youngestMarriageFemaleAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->youngestMarriageMale(), '</td>
				<td class="facts_value">', $stats->youngestMarriageFemale(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Oldest male'), ' - ', $stats->oldestMarriageMaleAge(true), '</td>
				<td class="facts_label">', I18N::translate('Oldest female'), ' - ', $stats->oldestMarriageFemaleAge(true), '</td>
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
		<b>', I18N::translate('Age at birth of child'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Youngest father'), ' - ', $stats->youngestFatherAge(true), '</td>
				<td class="facts_label">', I18N::translate('Youngest mother'), ' - ', $stats->youngestMotherAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->youngestFather(), '</td>
				<td class="facts_value">', $stats->youngestMother(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Oldest father'), ' - ', $stats->oldestFatherAge(true), '</td>
				<td class="facts_label">', I18N::translate('Oldest mother'), ' - ', $stats->oldestMotherAge(true), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->oldestFather(), '</td>
				<td class="facts_value">', $stats->oldestMother(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Children in family'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Average number of children per family'), '</td>
				<td class="facts_label">', I18N::translate('Number of families without children'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->averageChildren(), '</td>
				<td class="facts_value statistics-page">', $stats->noChildrenFamilies(), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->statsChildren(), '</td>
				<td class="facts_value statistics-page">', $stats->chartNoChildrenFamilies(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Largest families'), '</td>
				<td class="facts_label">', I18N::translate('Largest number of grandchildren'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topTenLargestFamilyList(), '</td>
				<td class="facts_value">', $stats->topTenLargestGrandFamilyList(), '</td>
			</tr>
		</table>
		<br>
		<b>', I18N::translate('Age difference'), '</b>
		<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Age between siblings'), '</td>
				<td class="facts_label">', I18N::translate('Greatest age between siblings'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->topAgeBetweenSiblingsList(), '</td>
				<td class="facts_value">', $stats->topAgeBetweenSiblingsFullName(), '</td>
			</tr>
			<tr>
				<td class="facts_label">', I18N::translate('Age between husband and wife'), '</td>
				<td class="facts_label">', I18N::translate('Age between wife and husband'), '</td>
			</tr>
			<tr>
				<td class="facts_value">', $stats->ageBetweenSpousesMFList(), '</td>
				<td class="facts_value">', $stats->ageBetweenSpousesFMList(), '</td>
			</tr>
		</table>
		</fieldset>';
	} elseif ($tab == 2) {
		echo '
		<fieldset>
			<legend>', I18N::translate('Records'), ': ', $stats->totalRecords(), '</legend>
			<table class="facts_table">
			<tr>
				<td class="facts_label">', I18N::translate('Media objects'), '</td>
				<td class="facts_label">', I18N::translate('Sources'), '</td>
				<td class="facts_label">', I18N::translate('Notes'), '</td>
				<td class="facts_label">', I18N::translate('Repositories'), '</td>
			</tr>
			<tr>
				<td class="facts_value statistics-page">', $stats->totalMedia(), '</td>
				<td class="facts_value statistics-page">', $stats->totalSources(), '</td>
				<td class="facts_value statistics-page">', $stats->totalNotes(), '</td>
				<td class="facts_value statistics-page">', $stats->totalRepositories(), '</td>
			</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', I18N::translate('Total events'), ': ', $stats->totalEvents(), '</legend>
			<table class="facts_table">
				<tr>
					<td class="facts_label">', I18N::translate('First event'), ' - ', $stats->firstEventType(), '</td>
					<td class="facts_label">', I18N::translate('Last event'), ' - ', $stats->lastEventType(), '</td>
				</tr>
				<tr>
					<td class="facts_value">', $stats->firstEvent(), '</td>
					<td class="facts_value">', $stats->lastEvent(), '</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', I18N::translate('Media objects'), ': ', $stats->totalMedia(), '</legend>
			<table class="facts_table">
				<tr>
					<td class="facts_label">', I18N::translate('Media objects'), '</td>
				</tr>
				<tr>
					<td class="facts_value statistics-page">', $stats->chartMedia(), '</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', I18N::translate('Sources'), ': ', $stats->totalSources(), '</legend>
			<table class="facts_table">
				<tr>
					<td class="facts_label">', I18N::translate('Individuals with sources'), '</td>
					<td class="facts_label">', I18N::translate('Families with sources'), '</td>
				</tr>
				<tr>
					<td class="facts_value statistics-page">', $stats->totalIndisWithSources(), '</td>
					<td class="facts_value statistics-page">', $stats->totalFamsWithSources(), '</td>
				</tr>
				<tr>
					<td class="facts_value statistics-page">', $stats->chartIndisWithSources(), '</td>
					<td class="facts_value statistics-page">', $stats->chartFamsWithSources(), '</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend>', I18N::translate('Places'), ': ', $stats->totalPlaces(), '</legend>
			<table class="facts_table">
				<tr>
					<td class="facts_label">', I18N::translate('Birth places'), '</td>
					<td class="facts_label">', I18N::translate('Death places'), '</td>
				</tr>
				<tr>
					<td class="facts_value">', $stats->commonBirthPlacesList(), '</td>
					<td class="facts_value">', $stats->commonDeathPlacesList(), '</td>
				</tr>
				<tr>
					<td class="facts_label">', I18N::translate('Marriage places'), '</td>
					<td class="facts_label">', I18N::translate('Events in countries'), '</td>
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
	} elseif ($tab == 3) {
		?>
		<script>
			function statusHide(sel) {
				var box = document.getElementById(sel);
				box.style.display = 'none';
				var box_m = document.getElementById(sel + '_m');
				if (box_m) {
					box_m.style.display = 'none';
				}
				if (sel == 'map_opt') {
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
				var box_m = document.getElementById(sel + '_m');
				if (box_m) {
					box_m.style.display = 'none';
				}
				if (sel == 'map_opt') {
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
					document.getElementById('surname_opt').style.display = '';
				} else if (x.value !== 'surname_distribution_chart') {
					document.getElementById('surname_opt').style.display = 'none';
				}
			}
			function statsModalDialog() {
				var form = jQuery('#own-stats-form');
				jQuery.get(form.attr('action'), form.serialize(), function (response) {
					jQuery(response).dialog({
						modal: true,
						width: 964,
						open: function () {
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
		<fieldset>
			<legend>
				<?php echo I18N::translate('Create your own chart') ?>
			</legend>
			<div id="own-stats">
				<form method="post" id="own-stats-form" action="statisticsplot.php" onsubmit="return statsModalDialog();">
					<input type="hidden" name="action" value="update">
					<table style="width:100%">
						<tr>
							<td class="descriptionbox width25 wrap">
								<?php echo I18N::translate('Chart type') ?>
							</td>
							<td class="optionbox">
								<label>
									<input type="radio" name="x-as" value="11" checked onclick="statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Month of birth') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="12" onclick="statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Month of death') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="13" onclick="statusChecked('z_none'); statusDisable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Month of marriage'); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="15" onclick="statusChecked('z_none'); statusDisable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Month of first marriage') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="14" onclick="statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Month of birth of first child in a relation') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="17" onclick="statusEnable('z_sex'); statusShow('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Age related to birth year') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="18" onclick="statusEnable('z_sex'); statusShow('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Age related to death year') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="19" onclick="statusEnable('z_sex'); statusHide('x_years'); statusShow('x_years_m'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Age in year of marriage') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="20" onclick="statusEnable('z_sex'); statusHide('x_years'); statusShow('x_years_m'); statusHide('x_months'); statusHide('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Age in year of first marriage') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="21" onclick="statusEnable('z_sex'); statusHide('x_years'); statusHide('x_months'); statusShow('x_numbers'); statusHide('map_opt');">
									<?php echo I18N::translate('Number of children') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="1" onclick="statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusShow('chart_type'); statusHide('axes');">
									<?php echo I18N::translate('Individual distribution') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="2" onclick="statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');">
									<?php echo I18N::translate('Birth by country') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="4" onclick="statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');">
									<?php echo I18N::translate('Marriage by country') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="x-as" value="3" onclick="statusHide('x_years'); statusHide('x_months'); statusHide('x_numbers'); statusShow('map_opt'); statusHide('chart_type'); statusHide('surname_opt');">
									<?php echo I18N::translate('Death by country') ?>
								</label>
								<br>
								<br>
								<div id="x_years" style="display:none;">
									<label for="x-axis-boundaries-ages">
										<?php echo I18N::translate('Select the desired age interval') ?>
									</label>
									<br>
										<select id="x-axis-boundaries-ages" name="x-axis-boundaries-ages">
											<option value="1,5,10,20,30,40,50,60,70,80,90,100" selected>
												<?php echo I18N::plural('interval %s year', 'interval %s years', 10, I18N::number(10)) ?>
											</option>
											<option value="5,20,40,60,75,80,85,90">
												<?php echo I18N::plural('interval %s year', 'interval %s years', 20, I18N::number(20)) ?>
											</option>
											<option value="10,25,50,75,100">
												<?php echo I18N::plural('interval %s year', 'interval %s years', 25, I18N::number(25)) ?>
											</option>
										</select>
									<br>
								</div>
								<div id="x_years_m" style="display:none;">
									<label for="x-axis-boundaries-ages_m">
										<?php echo I18N::translate('Select the desired age interval') ?>
									</label>
									<br>
									<select id="x-axis-boundaries-ages_m" name="x-axis-boundaries-ages_m">
										<option value="16,18,20,22,24,26,28,30,32,35,40,50" selected>
											<?php echo I18N::plural('interval %s year', 'interval %s years', 2, I18N::number(2)) ?>
										</option>
										<option value="20,25,30,35,40,45,50">
											<?php echo I18N::plural('interval %s year', 'interval %s years', 5, I18N::number(5)) ?>
										</option>
									</select>
									<br>
								</div>
								<div id="x_months" style="display:none;">
									<label for="x-axis-boundaries-months">
										<?php I18N::translate('Select the desired age interval') ?>
									</label>
									<br>
									<select id="x-axis-boundaries-months" name="x-axis-boundaries-months">
										<option value="0,8,12,15,18,24,48" selected>
											<?php echo I18N::translate('months after marriage') ?>
										</option>
										<option value="-24,-12,0,8,12,18,24,48">
											<?php echo I18N::translate('months before and after marriage') ?>
										</option>
										<option value="0,6,9,12,15,18,21,24">
											<?php echo I18N::translate('quarters after marriage') ?>
										</option>
										<option value="0,6,12,18,24">
											<?php echo I18N::translate('half-year after marriage') ?>
										</option>
									</select>
									<br>
								</div>
								<div id="x_numbers" style="display:none;">
									<label for="x-axis-boundaries-numbers">
										<?php echo I18N::translate('Select the desired count interval') ?>
									</label>
									<br>
									<select id="x-axis-boundaries-numbers" name="x-axis-boundaries-numbers">
										<option value="1,2,3,4,5,6,7,8,9,10" selected>
											<?php echo I18N::translate('interval one child') ?>
										</option>
										<option value="2,4,6,8,10,12">
											<?php echo I18N::translate('interval two children') ?>
										</option>
									</select>
									<br>
								</div>
								<div id="map_opt" style="display:none;">
									<div id="chart_type">
										<label>
											<?php echo I18N::translate('Chart type') ?>
											<br>
											<select name="chart_type" onchange="statusShowSurname(this);">
												<option value="indi_distribution_chart" selected>
													<?php echo I18N::translate('Individual distribution chart') ?>
												</option>
												<option value="surname_distribution_chart">
													<?php echo I18N::translate('Surname distribution chart') ?>
												</option>
											</select>
										</label>
										<br>
									</div>
									<div id="surname_opt" style="display:none;">
										<label for="SURN">
											<?php echo GedcomTag::getLabel('SURN') ?>
										</label>
										<?php echo FunctionsPrint::helpLink('google_chart_surname'); ?>
										<br>
										<input data-autocomplete-type="SURN" type="text" id="SURN" name="SURN" size="20">
										<br>
									</div>
										<label for="chart_shows">
											<?php echo I18N::translate('Geographic area') ?>
										</label>
										<br>
										<select id="chart_shows" name="chart_shows">
											<option value="world" selected>
												<?php echo I18N::translate('World') ?>
											</option>
											<option value="europe">
												<?php echo I18N::translate('Europe') ?>
											</option>
											<option value="usa">
												<?php echo I18N::translate('United States') ?>
											</option>
											<option value="south_america">
												<?php echo I18N::translate('South America') ?>
											</option>
											<option value="asia">
												<?php echo I18N::translate('Asia') ?>
											</option>
											<option value="middle_east">
												<?php echo I18N::translate('Middle East') ?>
											</option>
											<option value="africa">
												<?php echo I18N::translate('Africa') ?>
											</option>
										</select>
									</div>
								</td>
							<td class="descriptionbox width20 wrap" id="axes">
									<?php echo I18N::translate('Categories') ?>
							</td>
							<td class="optionbox width30" id="zyaxes">
								<label>
									<input type="radio" id="z_none" name="z-as" value="300" onclick="statusDisable('z-axis-boundaries-periods');">
									<?php echo I18N::translate('overall') ?>
								</label>
								<br>
								<label>
									<input type="radio" id="z_sex" name="z-as" value="301" onclick="statusDisable('z-axis-boundaries-periods');">
									<?php echo I18N::translate('gender') ?>
								</label>
								<br>
								<label>
									<input type="radio" id="z_time" name="z-as" value="302" checked onclick="statusEnable('z-axis-boundaries-periods');">
									<?php echo I18N::translate('date periods') ?>
								</label>
								<br>
								<br>
								<label for="z-axis-boundaries-periods">
									<?php echo I18N::translate('Date range') ?>
								</label>
								<br>
								<select id="z-axis-boundaries-periods" name="z-axis-boundaries-periods">
									<option value="1700,1750,1800,1850,1900,1950,2000" selected>
										<?php echo /* I18N: from 1700 interval 50 years */ I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, I18N::digits(1700), I18N::number(50)) ?>
									</option>
									<option value="1800,1840,1880,1920,1950,1970,2000">
										<?php echo I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 40, I18N::digits(1800), I18N::number(40)) ?>
									</option>
									<option value="1800,1850,1900,1950,2000">
										<?php echo I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, I18N::digits(1800), I18N::number(50)) ?>
									</option>
									<option value="1900,1920,1940,1960,1980,1990,2000">
										<?php echo I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 20, I18N::digits(1900), I18N::number(20)) ?>
									</option>
									<option value="1900,1925,1950,1975,2000">
										<?php echo I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 25, I18N::digits(1900), I18N::number(25)) ?>
									</option>
									<option value="1940,1950,1960,1970,1980,1990,2000">
										<?php echo I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 10, I18N::digits(1940), I18N::number(10)) ?>
									</option>
								</select>
								<br>
								<br>
								<?php echo I18N::translate('Results') ?>
								<br>
								<label>
									<input type="radio" name="y-as" value="201" checked>
									<?php echo I18N::translate('numbers') ?>
								</label>
								<br>
								<label>
									<input type="radio" name="y-as" value="202">
									<?php echo I18N::translate('percentage') ?>
								</label>
								<br>
							</td>
						</tr>
					</table>
					<p class="center">
						<button type="submit">
							<?php echo I18N::translate('show the chart') ?>
						</button>
					</p>
				</form>
			</div>
		</fieldset>
	<?php
	}
}
