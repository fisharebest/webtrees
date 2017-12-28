<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\DescendancyController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

require 'includes/session.php';

$controller = new DescendancyController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'descendancy_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	switch ($controller->chart_style) {
		case 0: // List
			echo '<ul id="descendancy_chart" class="chart_common">';
			$controller->printChildDescendancy($controller->root, $controller->generations);
			echo '</ul>';
			break;
		case 1: // Booklet
			$show_cousins = true;
			echo '<div id="descendancy_booklet">';
			$controller->printChildFamily($controller->root, $controller->generations);
			echo '</div>';
			break;
		case 2: // Individual list
			$descendants = $controller->individualDescendancy($controller->root, $controller->generations, []);
			echo '<div id="descendancy-list">', FunctionsPrintLists::individualTable($descendants), '</div>';
			break;
		case 3: // Family list
			$descendants = $controller->familyDescendancy($controller->root, $controller->generations, []);
			echo '<div id="descendancy-list">', FunctionsPrintLists::familyTable($descendants), '</div>';
			break;
	}
	echo $controller->getJavascript();

	return;
}

$ajax_url = Html::url('descendancy.php', [
	'ged'         => $controller->tree()->getName(),
	'rootid'      => $controller->root->getXref(),
	'chart_style' => $controller->chart_style,
	'generations' => $controller->generations,
	'ajax'        => 1,
]);

$controller->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-descendants-chart d-print-none">
	<input type="hidden" name="ged" value="<?= $controller->tree()->getNameHtml() ?>">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="rootid">
			<?= I18N::translate('Individual') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($controller->root, ['id' => 'rootid', 'name' => 'rootid']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions(range(2, $controller->tree()->getPreference('MAX_DESCENDANCY_GENERATIONS'))), $controller->generations, ['id' => 'generations', 'name' => 'generations']) ?>
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3 wt-page-options-label">
				<?= I18N::translate('Layout') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::radioButtons('chart_style', ['0' => I18N::translate('List'), '1' => I18N::translate('Booklet'), '2' => I18N::translate('Individuals'), '3' => I18N::translate('Families')], $controller->chart_style, true, ['onclick' => 'statusDisable("show_cousins");']) ?>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */
			I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-descendants-chart" data-ajax-url="<?= Html::escape($ajax_url) ?>"></div>
