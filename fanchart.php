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

use Fisharebest\Webtrees\Controller\FanchartController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

require 'includes/session.php';

$controller = new FanchartController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'fan_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	echo '<div id="fan_chart">', $controller->generateFanChart('html'), '</div>';
	echo '
		<script>
		(function() {
			$("area")
				.click(function (e) {
					e.stopPropagation();
					e.preventDefault();
					var target = $(this.hash);
					target
						// position the menu centered immediately above the mouse click position and
						// make sure it doesn’t end up off the screen
						.css({
							left: Math.max(0 ,e.pageX - (target.outerWidth()/2)),
							top:  Math.max(0, e.pageY - target.outerHeight())
						})
						.toggle()
						.siblings(".fan_chart_menu").hide();
				});
			$(".fan_chart_menu")
				.on("click", "a", function(e) {
					e.stopPropagation();
				});
			$("#fan_chart")
				.click(function(e) {
					$(".fan_chart_menu").hide();
				});
			return "' . strip_tags($controller->root->getFullName()) . '";
		})();
		</script>
	';

	return;
}

if (Filter::getBool('img') && Session::has('initiated')) {
	header('Content-Type: image/png');
	echo $controller->generateFanChart('png');

	return;
}

$controller->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-fan-chart d-print-none">
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
		<label class="col-sm-3 col-form-label wt-page-options-label">
			<?= I18N::translate('Layout') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select($controller->getFanStyles(), $controller->fan_style, ['id' => 'fan_style', 'name' => 'fan_style']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="generations">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions(range(2, 9)), $controller->generations, ['id' => 'generations', 'name' => 'generations']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="fan_width">
			<?= I18N::translate('Zoom') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<div class="input-group">
				<input class="form-control" type="text" size="3" id="fan_width" name="fan_width" value="<?= $controller->fan_width ?>">
				<div class="input-group-append">
					<span class="input-group-text">
						%
					</span>
				</div>
			</div>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-fan-chart"></div>
<script>
	document.addEventListener("DOMContentLoaded", function () {
    $(".wt-page-content").load(location.search + "&ajax=1");
	});
</script>
