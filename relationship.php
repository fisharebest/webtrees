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

use Fisharebest\Webtrees\Controller\RelationshipController;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Module\RelationshipsChartModule;

require 'includes/session.php';

$controller     = new RelationshipController;
$max_recursion  = (int) $controller->tree()->getPreference('RELATIONSHIP_RECURSION', RelationshipsChartModule::DEFAULT_RECURSION);
$ancestors_only = $controller->tree()->getPreference('RELATIONSHIP_ANCESTORS', RelationshipsChartModule::DEFAULT_ANCESTORS);

$pid1      = Filter::get('pid1', WT_REGEX_XREF);
$pid2      = Filter::get('pid2', WT_REGEX_XREF);
$recursion = Filter::getInteger('recursion', 0, $max_recursion, 0);
$ancestors = Filter::get('ancestors', '[01]', '0');

$person1 = Individual::getInstance($pid1, $controller->tree());
$person2 = Individual::getInstance($pid2, $controller->tree());

$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'relationships_chart'));

if ($person1 && $person2) {
	$controller
		->setPageTitle(I18N::translate(/* I18N: %s are individual’s names */ 'Relationships between %1$s and %2$s', $person1->getFullName(), $person2->getFullName()))
		->pageHeader();
	$paths = $controller->calculateRelationships($person1, $person2, $recursion, (bool) $ancestors);
} else {
	$controller
		->setPageTitle(I18N::translate('Relationships'))
		->pageHeader();
	$paths = [];
}

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-relationships-chart d-print-none">
	<input type="hidden" name="ged" value="<?= $controller->tree()->getNameHtml() ?>">

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="pid1">
			<?= I18N::translate('Individual 1') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($person1, ['id' => 'pid1', 'name' => 'pid1']) ?>
			<a href="#" onclick="var x = $('#pid1').val(); $('#pid1').val($('#pid2').val()); $('#pid2').val(x); return false;"><?= /* I18N: Reverse the order of two individuals */ I18N::translate('Swap individuals') ?></a>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="pid2">
			<?= I18N::translate('Individual 2') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= FunctionsEdit::formControlIndividual($person2, ['id' => 'pid2', 'name' => 'pid2']) ?>
		</div>
	</div>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-legend col-sm-3 wt-page-options-label">
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?php if ($ancestors_only === '1'): ?>
					<input type="hidden" name="ancestors" value="1">
					<?= I18N::translate('Find relationships via ancestors') ?>
				<?php else: ?>
					<?= Bootstrap4::radioButtons('ancestors', ['0' => I18N::translate('Find any relationship'), '1' => I18N::translate('Find relationships via ancestors')], $ancestors, false) ?>
			<?php endif ?>
			</div>
		</div>
	</fieldset>

	<fieldset class="form-group">
		<div class="row">
			<legend class="col-form-legend col-sm-3 wt-page-options-label">
			</legend>
			<div class="col-sm-9 wt-page-options-value">
				<?php if ($max_recursion === 0): ?>
					<?= I18N::translate('Find the closest relationships') ?>
					<input type="hidden" name="recursion" value="0">
				<?php else: ?>
					<?= Bootstrap4::radioButtons('recursion', ['0' => I18N::translate('Find the closest relationships'), $max_recursion => $max_recursion == RelationshipsChartModule::UNLIMITED_RECURSION ? I18N::translate('Find all possible relationships') : I18N::translate('Find other relationships')], $ancestors, false) ?>
			<?php endif ?>
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
<?php

if ($person1 && $person2) {
	if (I18N::direction() === 'ltr') {
		$diagonal1 = Theme::theme()->parameter('image-dline');
		$diagonal2 = Theme::theme()->parameter('image-dline2');
	} else {
		$diagonal1 = Theme::theme()->parameter('image-dline2');
		$diagonal2 = Theme::theme()->parameter('image-dline');
	}

	$num_paths = 0;
	foreach ($paths as $path) {
		// Extract the relationship names between pairs of individuals
		$relationships = $controller->oldStyleRelationshipPath($path);
		if (empty($relationships)) {
			// Cannot see one of the families/individuals, due to privacy;
			continue;
		}
		echo '<h3>', I18N::translate('Relationship: %s', Functions::getRelationshipNameFromPath(implode('', $relationships), $person1, $person2)), '</h3>';
		$num_paths++;

		// Use a table/grid for layout.
		$table = [];
		// Current position in the grid.
		$x = 0;
		$y = 0;
		// Extent of the grid.
		$min_y = 0;
		$max_y = 0;
		$max_x = 0;
		// For each node in the path.
		foreach ($path as $n => $xref) {
			if ($n % 2 === 1) {
				switch ($relationships[$n]) {
				case 'hus':
				case 'wif':
				case 'spo':
				case 'bro':
				case 'sis':
				case 'sib':
					$table[$x + 1][$y] = '<div style="background:url(' . Theme::theme()->parameter('image-hline') . ') repeat-x center;  width: 94px; text-align: center"><div class="hline-text" style="height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $controller->tree()), Individual::getInstance($path[$n + 1], $controller->tree())) . '</div><div style="height: 32px;">' . FontAwesome::decorativeIcon('arrow-end') . '</div></div>';
					$x += 2;
					break;
				case 'son':
				case 'dau':
				case 'chi':
					if ($n > 2 && preg_match('/fat|mot|par/', $relationships[$n - 2])) {
						$table[$x + 1][$y - 1] = '<div style="background:url(' . $diagonal2 . '); width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: end;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $controller->tree()), Individual::getInstance($path[$n + 1], $controller->tree())) . '</div><div style="height: 32px; text-align: start;">' . FontAwesome::decorativeIcon('arrow-down') . '</div></div>';
						$x += 2;
					} else {
						$table[$x][$y - 1] = '<div style="background:url(' . Theme::theme()
								->parameter('image-vline') . ') repeat-y center; height: 64px; text-align: center;"><div class="vline-text" style="display: inline-block; width:50%; line-height: 64px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $controller->tree()), Individual::getInstance($path[$n + 1], $controller->tree())) . '</div><div style="display: inline-block; width:50%; line-height: 64px;">' . FontAwesome::decorativeIcon('arrow-down') . '</div></div>';
					}
					$y -= 2;
					break;
				case 'fat':
				case 'mot':
				case 'par':
					if ($n > 2 && preg_match('/son|dau|chi/', $relationships[$n - 2])) {
						$table[$x + 1][$y + 1] = '<div style="background:url(' . $diagonal1 . '); background-position: top right; width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: start;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $controller->tree()), Individual::getInstance($path[$n + 1], $controller->tree())) . '</div><div style="height: 32px; text-align: end;">' . FontAwesome::decorativeIcon('arrow-down') . '</div></div>';
						$x += 2;
					} else {
						$table[$x][$y + 1] = '<div style="background:url(' . Theme::theme()
								->parameter('image-vline') . ') repeat-y center; height: 64px; text-align:center; "><div class="vline-text" style="display: inline-block; width: 50%; line-height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $controller->tree()), Individual::getInstance($path[$n + 1], $controller->tree())) . '</div><div style="display: inline-block; width: 50%; line-height: 32px">' . FontAwesome::decorativeIcon('arrow-up') . '</div></div>';
					}
					$y += 2;
					break;
				}
				$max_x = max($max_x, $x);
				$min_y = min($min_y, $y);
				$max_y = max($max_y, $y);
			} else {
				$individual = Individual::getInstance($xref, $controller->tree());
				ob_start();
				FunctionsPrint::printPedigreePerson($individual);
				$table[$x][$y] = ob_get_clean();
			}
		}
		echo '<table id="relationship-page" style="border-collapse: collapse; margin: 20px 50px;">';
		for ($y = $max_y; $y >= $min_y; --$y) {
			echo '<tr>';
			for ($x = 0; $x <= $max_x; ++$x) {
				echo '<td style="padding: 0;">';
				if (isset($table[$x][$y])) {
					echo $table[$x][$y];
				}
				echo '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}

	if (!$num_paths) {
		echo '<p>', I18N::translate('No link between the two individuals could be found.'), '</p>';
	}
}
