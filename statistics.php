<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Fisharebest\Webtrees\Controller\AjaxController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

// check for on demand content loading
$tab  = Filter::get('tab', '[0123]', '');
$ajax = Filter::getBool('ajax');

if (!$ajax) {
	$controller = new PageController;
	$controller
		->restrictAccess(Module::isActiveChart($WT_TREE, 'statistics_chart'))
		->setPageTitle(I18N::translate('Statistics'))
		->addInlineJavascript('$(function() {$("a[data-toggle=tab]:first").tab("show"); /* Activate the first tab */});')
		->pageHeader();

	?>
	<h2><?= I18N::translate('Statistics') ?></h2>

	<div class="wt-page-content wt-chart wt-timeline-chart">
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link" href="#individual-statistics" data-toggle="tab" data-href="statistics.php?ged=<?= $WT_TREE->getNameUrl() ?>&amp;ajax=1&amp;tab=0" role="tab">
					<?= I18N::translate('Individuals') ?>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#family-statistics" data-toggle="tab" data-href="statistics.php?ged=<?= $WT_TREE->getNameUrl() ?>&amp;ajax=1&amp;tab=1" role="tab">
					<?= I18N::translate('Families') ?>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#other-statistics" data-toggle="tab" data-href="statistics.php?ged=<?= $WT_TREE->getNameUrl() ?>&amp;ajax=1&amp;tab=2" role="tab">
					<?= I18N::translate('Others') ?>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#custom-statistics" data-toggle="tab" data-href="statistics.php?ged=<?= $WT_TREE->getNameUrl() ?>&amp;ajax=1&amp;tab=3" role="tab">
					<?= I18N::translate('Own charts') ?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="individual-statistics"></div>
			<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="family-statistics"></div>
			<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="other-statistics"></div>
			<div class="tab-pane fade wt-ajax-load" role="tabpanel" id="custom-statistics"></div>
		</div>
	</div>

	<?php
	return;
}

$controller = new AjaxController;
$controller->pageHeader();
$stats = new Stats($WT_TREE);
?>

<?php if ($tab === '0'): ?>

	<h3><?= I18N::translate('Total individuals: %s', $stats->totalIndividuals()) ?></h3>

	<table class="table table-sm table-bordered">
		<thead>
			<tr>
				<th><?= I18N::translate('Total males') ?></th>
				<th><?= I18N::translate('Total females') ?></th>
				<th><?= I18N::translate('Total living') ?></th>
				<th><?= I18N::translate('Total dead') ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?= $stats->totalSexMales() ?></td>
				<td><?= $stats->totalSexFemales() ?></td>
				<td><?= $stats->totalLiving() ?></td>
				<td><?= $stats->totalDeceased() ?></td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><?= $stats->chartSex() ?></td>
				<td colspan="2"><?= $stats->chartMortality() ?></td>
			</tr>
		</tfoot>
	</table>

	<h3><?= I18N::translate('Events') ?></h3>

	<table class="table table-sm table-bordered">
		<tbody>
			<tr>
				<th><?= I18N::translate('Total births') ?></th>
				<th><?= I18N::translate('Total deaths') ?></th>
			</tr>
			<tr>
				<td><?= $stats->totalBirths() ?></td>
				<td><?= $stats->totalDeaths() ?></td>
			</tr>
			<tr>
				<td><?= I18N::translate('Births by century') ?></td>
				<td><?= I18N::translate('Deaths by century') ?></td>
			</tr>
			<tr>
				<td><?= $stats->statsBirth() ?></td>
				<td><?= $stats->statsDeath() ?></td>
			</tr>
			<tr>
				<td><?= I18N::translate('Earliest birth') ?></td>
				<td><?= I18N::translate('Earliest death') ?></td>
			</tr>
			<tr>
				<td><?= $stats->firstBirth() ?></td>
				<td><?= $stats->firstDeath() ?></td>
			</tr>
			<tr>
				<td><?= I18N::translate('Latest birth') ?></td>
				<td><?= I18N::translate('Latest death') ?></td>
			</tr>
			<tr>
				<td><?= $stats->lastBirth() ?></td>
				<td><?= $stats->lastDeath() ?></td>
			</tr>
		</tbody>
	</table>

	<h3><?= I18N::translate('Lifespan') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Average age at death') ?></td>
			<td><?= I18N::translate('Males') ?></td>
			<td><?= I18N::translate('Females') ?></td>
		</tr>
		<tr>
			<td><?= $stats->averageLifespan(true) ?></td>
			<td><?= $stats->averageLifespanMale(true) ?></td>
			<td><?= $stats->averageLifespanFemale(true) ?></td>
		</tr>
		<tr>
			<td colspan="3"><?= $stats->statsAge() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Greatest age at death') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Males') ?></td>
			<td><?= I18N::translate('Females') ?></td>
		</tr>
		<tr>
			<td><?= $stats->topTenOldestMaleList() ?></td>
			<td><?= $stats->topTenOldestFemaleList() ?></td>
		</tr>
	</table>

	<?php if (Auth::check()): ?>
	<h3><?= I18N::translate('Oldest living individuals') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Males') ?></td>
			<td><?= I18N::translate('Females') ?></td>
		</tr>
		<tr>
			<td><?= $stats->topTenOldestMaleListAlive() ?></td>
			<td><?= $stats->topTenOldestFemaleListAlive() ?></td>
		</tr>
	</table>
	<?php endif ?>

	<h3><?= I18N::translate('Names') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Total surnames') ?></td>
			<td><?= I18N::translate('Total given names') ?></td>
		</tr>
		<tr>
			<td><?= $stats->totalSurnames() ?></td>
			<td><?= $stats->totalGivennames() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Top surnames') ?></td>
			<td><?= I18N::translate('Top given names') ?></td>
		</tr>
		<tr>
			<td><?= $stats->chartCommonSurnames() ?></td>
			<td><?= $stats->chartCommonGiven() ?></td>
		</tr>
	</table>

<?php elseif ($tab === '1'): ?>

	<h3><?= I18N::translate('Total families: %s', $stats->totalFamilies()) ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Total marriages') ?></td>
			<td><?= I18N::translate('Total divorces') ?></td>
		</tr>
		<tr>
			<td><?= $stats->totalMarriages() ?></td>
			<td><?= $stats->totalDivorces() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Marriages by century') ?></td>
			<td><?= I18N::translate('Divorces by century') ?></td>
		</tr>
		<tr>
			<td><?= $stats->statsMarr() ?></td>
			<td><?= $stats->statsDiv() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Earliest marriage') ?></td>
			<td><?= I18N::translate('Earliest divorce') ?></td>
		</tr>
		<tr>
			<td><?= $stats->firstMarriage() ?></td>
			<td><?= $stats->firstDivorce() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Latest marriage') ?></td>
			<td><?= I18N::translate('Latest divorce') ?></td>
		</tr>
		<tr>
			<td><?= $stats->lastMarriage() ?></td>
			<td><?= $stats->lastDivorce() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Length of marriage') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Longest marriage'), ' - ', $stats->topAgeOfMarriage() ?></td>
			<td><?= I18N::translate('Shortest marriage'), ' - ', $stats->minAgeOfMarriage() ?></td>
		</tr>
		<tr>
			<td><?= $stats->topAgeOfMarriageFamily() ?></td>
			<td><?= $stats->minAgeOfMarriageFamily() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Age in year of marriage') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Youngest male'), ' - ', $stats->youngestMarriageMaleAge(true) ?></td>
			<td><?= I18N::translate('Youngest female'), ' - ', $stats->youngestMarriageFemaleAge(true) ?></td>
		</tr>
		<tr>
			<td><?= $stats->youngestMarriageMale() ?></td>
			<td><?= $stats->youngestMarriageFemale() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Oldest male'), ' - ', $stats->oldestMarriageMaleAge(true) ?></td>
			<td><?= I18N::translate('Oldest female'), ' - ', $stats->oldestMarriageFemaleAge(true) ?></td>
		</tr>
		<tr>
			<td><?= $stats->oldestMarriageMale() ?></td>
			<td><?= $stats->oldestMarriageFemale() ?></td>
		</tr>
		<tr>
			<td colspan="2"><?= $stats->statsMarrAge() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Age at birth of child') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Youngest father'), ' - ', $stats->youngestFatherAge(true) ?></td>
			<td><?= I18N::translate('Youngest mother'), ' - ', $stats->youngestMotherAge(true) ?></td>
		</tr>
		<tr>
			<td><?= $stats->youngestFather() ?></td>
			<td><?= $stats->youngestMother() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Oldest father'), ' - ', $stats->oldestFatherAge(true) ?></td>
			<td><?= I18N::translate('Oldest mother'), ' - ', $stats->oldestMotherAge(true) ?></td>
		</tr>
		<tr>
			<td><?= $stats->oldestFather() ?></td>
			<td><?= $stats->oldestMother() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Children in family') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Average number of children per family') ?></td>
			<td><?= I18N::translate('Number of families without children') ?></td>
		</tr>
		<tr>
			<td><?= $stats->averageChildren() ?></td>
			<td><?= $stats->noChildrenFamilies() ?></td>
		</tr>
		<tr>
			<td><?= $stats->statsChildren() ?></td>
			<td><?= $stats->chartNoChildrenFamilies() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Largest families') ?></td>
			<td><?= I18N::translate('Largest number of grandchildren') ?></td>
		</tr>
		<tr>
			<td><?= $stats->topTenLargestFamilyList() ?></td>
			<td><?= $stats->topTenLargestGrandFamilyList() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Age difference') ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Age between siblings') ?></td>
			<td><?= I18N::translate('Greatest age between siblings') ?></td>
		</tr>
		<tr>
			<td><?= $stats->topAgeBetweenSiblingsList() ?></td>
			<td><?= $stats->topAgeBetweenSiblingsFullName() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Age between husband and wife') ?></td>
			<td><?= I18N::translate('Age between wife and husband') ?></td>
		</tr>
		<tr>
			<td><?= $stats->ageBetweenSpousesMFList() ?></td>
			<td><?= $stats->ageBetweenSpousesFMList() ?></td>
		</tr>
	</table>

<?php elseif ($tab === '2'): ?>

	<h3><?= I18N::translate('Records'), ': ', $stats->totalRecords() ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Media objects') ?></td>
			<td><?= I18N::translate('Sources') ?></td>
			<td><?= I18N::translate('Notes') ?></td>
			<td><?= I18N::translate('Repositories') ?></td>
		</tr>
		<tr>
			<td><?= $stats->totalMedia() ?></td>
			<td><?= $stats->totalSources() ?></td>
			<td><?= $stats->totalNotes() ?></td>
			<td><?= $stats->totalRepositories() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Total events'), ': ', $stats->totalEvents() ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('First event'), ' - ', $stats->firstEventType() ?></td>
			<td><?= I18N::translate('Last event'), ' - ', $stats->lastEventType() ?></td>
		</tr>
		<tr>
			<td><?= $stats->firstEvent() ?></td>
			<td><?= $stats->lastEvent() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Media objects'), ': ', $stats->totalMedia() ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Media objects') ?></td>
		</tr>
		<tr>
			<td><?= $stats->chartMedia() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Sources'), ': ', $stats->totalSources() ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Individuals with sources') ?></td>
			<td><?= I18N::translate('Families with sources') ?></td>
		</tr>
		<tr>
			<td><?= $stats->totalIndisWithSources() ?></td>
			<td><?= $stats->totalFamsWithSources() ?></td>
		</tr>
		<tr>
			<td><?= $stats->chartIndisWithSources() ?></td>
			<td><?= $stats->chartFamsWithSources() ?></td>
		</tr>
	</table>

	<h3><?= I18N::translate('Places'), ': ', $stats->totalPlaces() ?></h3>

	<table>
		<tr>
			<td><?= I18N::translate('Birth places') ?></td>
			<td><?= I18N::translate('Death places') ?></td>
		</tr>
		<tr>
			<td><?= $stats->commonBirthPlacesList() ?></td>
			<td><?= $stats->commonDeathPlacesList() ?></td>
		</tr>
		<tr>
			<td><?= I18N::translate('Marriage places') ?></td>
			<td><?= I18N::translate('Events in countries') ?></td>
		</tr>
		<tr>
			<td><?= $stats->commonMarriagePlacesList() ?></td>
			<td><?= $stats->commonCountriesList() ?></td>
		</tr>
		<tr>
			<td colspan="2"><?= $stats->chartDistribution() ?></td>
		</tr>
	</table>

<?php elseif ($tab === '3'): ?>

	<script>
		function statusHide(sel) {
			var box = document.getElementById(sel);
			box.style.display = 'none';
			var box_m = document.getElementById(sel + '_m');
			if (box_m) {
				box_m.style.display = 'none';
			}
			if (sel === 'map_opt') {
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
			if (sel === 'map_opt') {
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
			if (x.value === 'surname_distribution_chart') {
				document.getElementById('surname_opt').style.display = '';
			} else if (x.value !== 'surname_distribution_chart') {
				document.getElementById('surname_opt').style.display = 'none';
			}
		}
		function statsModalDialog() {
			var form = $('#own-stats-form');
			jQuery.get(form.attr('action'), form.serialize(), function (response) {
				$(response).dialog({
					modal: true,
					width: 964,
					open: function () {
						var self = this;
						// Close the window when we click outside it.
						$(".ui-widget-overlay").on("click", function () {
							$(self).dialog('close');
						});
					}
				});
			});
			return false;
		}
	</script>
	<h3><?= I18N::translate('Create your own chart') ?></h3>
	<div id="own-stats">
		<form method="post" id="own-stats-form" action="statisticsplot.php" onsubmit="return statsModalDialog();" class="wt-page-options wt-page-options-statistics">
			<input type="hidden" name="action" value="update">
			<div class="form-group row">
				<div class="col-sm-2 wt-page-options-label">
					<?= I18N::translate('Chart type') ?>
				</div>
				<div class="col-sm-4 wt-page-options-value">
					<?= Bootstrap4::radioButtons('x-as', ['11' => I18N::translate('Month of birth')], true, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['12' => I18N::translate('Month of death')], false, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['13' => I18N::translate('Month of marriage'), '14' => I18N::translate('Month of first marriage')], false, false, ['onchange' => 'statusChecked("z_none"); statusDisable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['14' => I18N::translate('Month of birth of first child in a relation')], false, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['18' => I18N::translate('Longevity versus time')], false, false, ['onchange' => 'statusEnable("z_sex"); statusShow("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['19' => I18N::translate('Age in year of marriage')], false, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusShow("x_years_m"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['19' => I18N::translate('Age in year of first marriage')], false, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusShow("x_years_m"); statusHide("x_months"); statusHide("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['21' => I18N::translate('Number of children')], false, false, ['onchange' => 'statusEnable("z_sex"); statusHide("x_years"); statusHide("x_months"); statusShow("x_numbers"); statusHide("map_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['1' => I18N::translate('Individual distribution')], false, false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusShow("map_opt"); statusShow("chart_type"); statusHide("axes");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['2' => I18N::translate('Birth by country')], false, false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusShow("map_opt"); statusHide("chart_type"); statusHide("surname_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['4' => I18N::translate('Marriage by country')], false, false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusShow("map_opt"); statusHide("chart_type"); statusHide("surname_opt");']) ?>
					<?= Bootstrap4::radioButtons('x-as', ['3' => I18N::translate('Death by country')], false, false, ['onchange' => 'statusHide("x_years"); statusHide("x_months"); statusHide("x_numbers"); statusShow("map_opt"); statusHide("chart_type"); statusHide("surname_opt");']) ?>

					<div id="x_years" style="display:none;">
						<label for="x-axis-boundaries-ages">
							<?= I18N::translate('Select the desired age interval') ?>
						</label>
						<br>
						<?= Bootstrap4::select(['1,5,10,20,30,40,50,60,70,80,90,100' => I18N::plural('%s year', '%s years', 10, I18N::number(10)), '5,20,40,60,75,80,85,90' => I18N::plural('%s year', '%s years', 20, I18N::number(20)), '10,25,50,75,100' => I18N::plural('%s year', '%s years', 25, I18N::number(25)), ], '1,5,10,20,30,40,50,60,70,80,90,100', ['id' => 'x-axis-boundaries-ages', 'name' => 'x-axis-boundaries-ages']) ?>
					</div>

					<div id="x_years_m" style="display:none;">
						<label for="x-axis-boundaries-ages_m">
							<?= I18N::translate('Select the desired age interval') ?>
						</label>
						<?= Bootstrap4::select(['16,18,20,22,24,26,28,30,32,35,40,50' => I18N::plural('%s year', '%s years', 2, I18N::number(2)), '20,25,30,35,40,45,50' => I18N::plural('%s year', '%s years', 5, I18N::number(5))], '16,18,20,22,24,26,28,30,32,35,40,50', ['id' => 'x-axis-boundaries-ages_m', 'name' => 'x-axis-boundaries-ages_m']) ?>
					</div>

					<div id="x_months" style="display:none;">
						<label for="x-axis-boundaries-months">
							<?php I18N::translate('Select the desired age interval') ?>
						</label>
						<br>
						<select id="x-axis-boundaries-months" name="x-axis-boundaries-months">
							<option value="0,8,12,15,18,24,48" selected>
								<?= I18N::translate('months after marriage') ?>
							</option>
							<option value="-24,-12,0,8,12,18,24,48">
								<?= I18N::translate('months before and after marriage') ?>
							</option>
							<option value="0,6,9,12,15,18,21,24">
								<?= I18N::translate('quarters after marriage') ?>
							</option>
							<option value="0,6,12,18,24">
								<?= I18N::translate('half-year after marriage') ?>
							</option>
						</select>
						<br>
					</div>
					<div id="x_numbers" style="display:none;">
						<label for="x-axis-boundaries-numbers">
							<?= I18N::translate('Select the desired count interval') ?>
						</label>
						<br>
						<select id="x-axis-boundaries-numbers" name="x-axis-boundaries-numbers">
							<option value="1,2,3,4,5,6,7,8,9,10" selected>
								<?= I18N::translate('interval one child') ?>
							</option>
							<option value="2,4,6,8,10,12">
								<?= I18N::translate('interval two children') ?>
							</option>
						</select>
						<br>
					</div>
					<div id="map_opt" style="display:none;">
						<div id="chart_type">
							<label>
								<?= I18N::translate('Chart type') ?>
								<br>
								<select name="chart_type" onchange="statusShowSurname(this);">
									<option value="indi_distribution_chart" selected>
										<?= I18N::translate('Individual distribution chart') ?>
									</option>
									<option value="surname_distribution_chart">
										<?= I18N::translate('Surname distribution chart') ?>
									</option>
								</select>
							</label>
							<br>
						</div>
						<div id="surname_opt" style="display:none;">
							<label for="SURN">
								<?= I18N::translate('Surname') ?>
							</label>
							<?= FunctionsPrint::helpLink('google_chart_surname') ?>
							<br>
							<input data-autocomplete-type="SURN" type="text" id="SURN" name="SURN" size="20">
							<br>
						</div>
						<label for="chart_shows">
							<?= I18N::translate('Geographic area') ?>
						</label>
						<br>
						<select id="chart_shows" name="chart_shows">
							<option value="world" selected>
								<?= I18N::translate('World') ?>
							</option>
							<option value="europe">
								<?= I18N::translate('Europe') ?>
							</option>
							<option value="usa">
								<?= I18N::translate('United States') ?>
							</option>
							<option value="south_america">
								<?= I18N::translate('South America') ?>
							</option>
							<option value="asia">
								<?= I18N::translate('Asia') ?>
							</option>
							<option value="middle_east">
								<?= I18N::translate('Middle East') ?>
							</option>
							<option value="africa">
								<?= I18N::translate('Africa') ?>
							</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2 wt-page-options-label"id="axes">
					<?= I18N::translate('Categories') ?>
				</div>
				<div class="col-sm-4 wt-page-options-value" id="zyaxes">
					<label>
						<input type="radio" id="z_none" name="z-as" value="300" onclick="statusDisable('z-axis-boundaries-periods');">
						<?= I18N::translate('overall') ?>
					</label>
					<br>
					<label>
						<input type="radio" id="z_sex" name="z-as" value="301" onclick="statusDisable('z-axis-boundaries-periods');">
						<?= I18N::translate('gender') ?>
					</label>
					<br>
					<label>
						<input type="radio" id="z_time" name="z-as" value="302" checked onclick="statusEnable('z-axis-boundaries-periods');">
						<?= I18N::translate('date periods') ?>
					</label>
					<br>
					<br>
					<label for="z-axis-boundaries-periods">
						<?= I18N::translate('Date range') ?>
					</label>
					<br>
					<select id="z-axis-boundaries-periods" name="z-axis-boundaries-periods">
						<option value="1700,1750,1800,1850,1900,1950,2000" selected>
							<?= /* I18N: from 1700 interval 50 years */ I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, I18N::digits(1700), I18N::number(50)) ?>
						</option>
						<option value="1800,1840,1880,1920,1950,1970,2000">
							<?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 40, I18N::digits(1800), I18N::number(40)) ?>
						</option>
						<option value="1800,1850,1900,1950,2000">
							<?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 50, I18N::digits(1800), I18N::number(50)) ?>
						</option>
						<option value="1900,1920,1940,1960,1980,1990,2000">
							<?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 20, I18N::digits(1900), I18N::number(20)) ?>
						</option>
						<option value="1900,1925,1950,1975,2000">
							<?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 25, I18N::digits(1900), I18N::number(25)) ?>
						</option>
						<option value="1940,1950,1960,1970,1980,1990,2000">
							<?= I18N::plural('from %1$s interval %2$s year', 'from %1$s interval %2$s years', 10, I18N::digits(1940), I18N::number(10)) ?>
						</option>
					</select>
					<br>
					<br>
					<?= I18N::translate('Results') ?>
					<br>
					<label>
						<input type="radio" name="y-as" value="201" checked>
						<?= I18N::translate('numbers') ?>
					</label>
					<br>
					<label>
						<input type="radio" name="y-as" value="202">
						<?= I18N::translate('percentage') ?>
					</label>
					<br>
				</div>
			</div>
			<p class="center">
				<button type="submit" class="btn btn-primary">
					<?= I18N::translate('show the chart') ?>
				</button>
			</p>
		</form>
	</div>
<?php endif ?>

