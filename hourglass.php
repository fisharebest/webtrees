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

use Fisharebest\Webtrees\Controller\HourglassController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

require 'includes/session.php';

$controller = new HourglassController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'hourglass_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	?>
	<div id="hourglass_chart" style="width:98%; z-index:1;">
		<table>
			<tr>
				<td style="vertical-align:middle">
					<?php $controller->printDescendency($controller->root, 1) ?>
				</td>
				<td style="vertical-align:middle">
					<?php $controller->printPersonPedigree($controller->root, 1) ?>
				</td>
			</tr>
		</table>
	</div>
	<script>
		<?= $controller->setupJavascript() ?>
	</script>
	<?php

	return;
}

$controller
	->addInlineJavascript('$(".wt-page-content").load(document.location + "&ajax=1");')
	->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-hourglass-chart hidden-print">
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
			<legend class="col-form-legend col-sm-3 wt-page-options-label">
				<?= I18N::translate('Layout') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?= Bootstrap4::checkbox(I18N::translate('Show spouses'), true, ['name' => 'show_spouse']) ?>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-hourglass-chart"></div>
