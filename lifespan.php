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

use Fisharebest\Webtrees\Controller\LifespanController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

require 'includes/session.php';

$controller = new LifespanController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'lifespans_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	?>
	<div id="lifespan-chart">
		<h4><?= $controller->subtitle ?></h4>
		<div id="lifespan-scale" class="text-nowrap">
			<?php $controller->printTimeline() ?>
		</div>
		<div id="lifespan-people">
			<?php $maxY = $controller->fillTimeline() ?>
		</div>
	</div>
	<script>
		var scale = $('#lifespan-scale');
		var barHeight = $('#lifespan-people').children().first().outerHeight();
		$('#lifespan-chart')
			.width(scale.width())
			.height(Math.ceil($('h4').outerHeight() + scale.height() + barHeight + <?= $maxY ?>));
		$('form').on('reset', function() {
			$('#clear').val(1);
			$(this).submit();
		});
	</script>
	<?php

	return;
}

$ajax_url = Html::url('lifespan.php', [
	'ged'  => $controller->tree()->getName(),
	'ajax' => 1,
]);

$controller->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-lifespan-chart d-print-none">
	<input type="hidden" name="ged" value="<?= $controller->tree()->getNameHtml() ?>">
	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="newpid">
			<?= I18N::translate('Add individuals') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual(null, ['id' => 'newpid', 'name' => 'newpid']) ?>
			<?= Bootstrap4::checkbox(/* I18N: Label for a configuration option */ I18N::translate('Include the individual’s immediate family'), false, ['name' => 'addFamily']) ?>
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-label col-sm-3 wt-page-options-label">
				<?= I18N::translate('Select individuals by place or date') ?>
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<label for="place">
					<?= I18N::translate('Place') ?>
				</label>
				<?= FunctionsEdit::formControlPlace('', ['id' => 'place', 'name' => 'place']) ?>
			</div>
		</div>
		<div class="row">
			<div class="col-form-label col-sm-3 wt-page-options-label"></div>
			<div class="col-sm-3 wt-page-options-value">
				<label for="beginYear">
					<?= /* I18N: The earliest year in a range */ I18N::translate('Start year') ?>
				</label>
				<input class="form-control" type="text" name="beginYear" placeholder="<?= I18N::translate('Year') ?>">
			</div>
			<div class="col-sm-3 wt-page-options-value">
				<label for="endYear">
					<?= /* I18N: The latest year in a range */ I18N::translate('End year') ?>
				</label>
				<input class="form-control" type="text" name="endYear" placeholder="<?= I18N::translate('Year') ?>">
			</div>
			<div class="col-sm-3 wt-page-options-value">
				<label for="calendar">
					<?= I18N::translate('Calendar') ?>
				</label>
				<?= Bootstrap4::select(Date::calendarNames(), 'gregorian', ['id' => 'calendar', 'name' => 'calendar']) ?>
				<?= Bootstrap4::checkbox(I18N::translate('Match calendar'), false, ['name' => 'strictDate']) ?>
			</div>
		</div>
	</fieldset>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input id="clear" type="hidden" name="clear" value="0">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
			<input class="btn btn-default" type="reset" value="<?= /* I18N: A button label. */ I18N::translate('reset') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-lifespans-chart" data-ajax-url="<?= e($ajax_url) ?>"></div>
