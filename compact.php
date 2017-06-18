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

use Fisharebest\Webtrees\Controller\CompactController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;

require 'includes/session.php';

$controller = new CompactController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'compact_tree_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	?>
	<div class="compact-chart">
		<table width="100%" style="text-align:center;">
			<tr>
				<?= $controller->sosaIndividual(16) ?>
				<td></td>
				<td></td>
				<td></td>
				<?= $controller->sosaIndividual(18) ?>
				<td></td>
				<?= $controller->sosaIndividual(24) ?>
				<td></td>
				<td></td>
				<td></td>
				<?= $controller->sosaIndividual(26) ?>
			</tr>
			<tr>
				<td><?= $controller->sosaArrow(16, 'arrow-up') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(18, 'arrow-up') ?></td>
				<td></td>
				<td><?= $controller->sosaArrow(24, 'arrow-up') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(26, 'arrow-up') ?></td>
			</tr>
			<tr>
				<?= $controller->sosaIndividual(8) ?>
				<td><?= $controller->sosaArrow(8, 'arrow-start') ?></td>
				<?= $controller->sosaIndividual(4) ?>
				<td><?= $controller->sosaArrow(9, 'arrow-end') ?></td>
				<?= $controller->sosaIndividual(9) ?>
				<td></td>
				<?= $controller->sosaIndividual(12) ?>
				<td><?= $controller->sosaArrow(12, 'arrow-start') ?></td>
				<?= $controller->sosaIndividual(6) ?>
				<td><?php  echo $controller->sosaArrow(13, 'arrow-end') ?></td>
				<?= $controller->sosaIndividual(13) ?>
			</tr>
			<tr>
				<td><?= $controller->sosaArrow(17, 'arrow-down') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(19, 'arrow-down') ?></td>
				<td></td>
				<td><?= $controller->sosaArrow(25, 'arrow-down') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(27, 'arrow-down') ?></td>
			</tr>
			<tr>
				<?= $controller->sosaIndividual(17) ?>
				<td></td>
				<td><?= $controller->sosaArrow(4, 'arrow-up') ?></td>
				<td></td>
				<?= $controller->sosaIndividual(19) ?>
				<td></td>
				<?= $controller->sosaIndividual(25) ?>
				<td></td>
				<td><?= $controller->sosaArrow(6, 'arrow-up') ?></td>
				<td></td>
				<?= $controller->sosaIndividual(27) ?>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<?= $controller->sosaIndividual(2) ?>
				<td></td>
				<td colspan="3">
					<table width="100%">
						<tr>
							<td width='25%'><?= $controller->sosaArrow(2, 'arrow-start') ?></td>
							<?= $controller->sosaIndividual(1) ?>
							<td width='25%'><?= $controller->sosaArrow(3, 'arrow-end') ?></td>
						</tr>
					</table>
				</td>
				<td></td>
				<?= $controller->sosaIndividual(3) ?>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<?= $controller->sosaIndividual(20) ?>
				<td></td>
				<td><?= $controller->sosaArrow(5, 'arrow-down') ?></td>
				<td></td>
				<?= $controller->sosaIndividual(22) ?>
				<td></td>
				<?= $controller->sosaIndividual(28) ?>
				<td></td>
				<td><?= $controller->sosaArrow(7, 'arrow-down') ?></td>
				<td></td>
				<?= $controller->sosaIndividual(30) ?>
			</tr>
			<tr>
				<td><?= $controller->sosaArrow(20, 'arrow-up') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(22, 'arrow-up') ?></td>
				<td></td>
				<td><?= $controller->sosaArrow(28, 'arrow-up') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(30, 'arrow-up') ?></td>
			</tr>
			<tr>
				<?= $controller->sosaIndividual(10) ?>
				<td><?= $controller->sosaArrow(10, 'arrow-start') ?></td>
				<?= $controller->sosaIndividual(5) ?>
				<td><?= $controller->sosaArrow(11, 'arrow-end') ?></td>
				<?= $controller->sosaIndividual(11) ?>
				<td></td>
				<?= $controller->sosaIndividual(14) ?>
				<td><?= $controller->sosaArrow(14, 'arrow-start') ?></td>
				<?= $controller->sosaIndividual(7) ?>
				<td><?= $controller->sosaArrow(15, 'arrow-end') ?></td>
				<?= $controller->sosaIndividual(15) ?>
			</tr>
			<tr>
				<td><?= $controller->sosaArrow(21, 'arrow-down') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(23, 'arrow-down') ?></td>
				<td></td>
				<td><?= $controller->sosaArrow(29, 'arrow-down') ?></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?= $controller->sosaArrow(31, 'arrow-down') ?></td>
			</tr>
			<tr>
				<?= $controller->sosaIndividual(21) ?>
				<td></td>
				<td></td>
				<td></td>
				<?= $controller->sosaIndividual(23) ?>
				<td></td>
				<?= $controller->sosaIndividual(29) ?>
				<td></td>
				<td></td>
				<td></td>
				<?= $controller->sosaIndividual(31) ?>
			</tr>
		</table>
	</div>
	<?php
	return;
}

$controller
	->addInlineJavascript('$(".wt-page-content").load(document.location + "&ajax=1");')
	->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-compact-chart hidden-print">
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
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-compact-chart"></div>
