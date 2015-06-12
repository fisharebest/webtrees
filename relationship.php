<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
 * @global Tree    $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\RelationshipController;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'relationship.php');
require './includes/session.php';

$controller = new RelationshipController;
$pid1       = Filter::get('pid1', WT_REGEX_XREF);
$pid2       = Filter::get('pid2', WT_REGEX_XREF);
$show_full  = Filter::getInteger('show_full', 0, 1, $WT_TREE->getPreference('PEDIGREE_FULL_DETAILS'));
$find_all   = Filter::getBool('find_all');

$person1 = Individual::getInstance($pid1, $WT_TREE);
$person2 = Individual::getInstance($pid2, $WT_TREE);

$controller
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

if ($person1 && $person2) {
	$controller
		->setPageTitle(I18N::translate(/* I18N: %s are individual’s names */ 'Relationships between %1$s and %2$s', $person1->getFullName(), $person2->getFullName()))
		->PageHeader();
	$paths = $controller->calculateRelationships($person1, $person2, $find_all);
} else {
	$controller
		->setPageTitle(I18N::translate('Relationships'))
		->PageHeader();
	$paths = array();
}

?>
<h2><?php echo $controller->getPageTitle(); ?></h2>
<form name="people" method="get" action="?">
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
	<table class="list_table">
		<tbody>
			<tr>
				<td class="descriptionbox">
					<?php echo I18N::translate('Individual 1'); ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="pid1" id="pid1" size="3" value="<?php echo $pid1; ?>">
					<?php echo FunctionsPrint::printFindIndividualLink('pid1'); ?>
				</td>
				<td class="optionbox">
					<label>
						<?php echo FunctionsEdit::twoStateCheckbox('show_full', $show_full); ?>
						<?php echo I18N::translate('Show details'); ?>
					</label>
				</td>
				<td class="optionbox vmiddle" rowspan="2">
					<input type="submit" value="<?php echo I18N::translate('View'); ?>">
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo I18N::translate('Individual 2'); ?>
				</td>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" name="pid2" id="pid2" size="3" value="<?php echo $pid2; ?>">
					<?php echo FunctionsPrint::printFindIndividualLink('pid2'); ?>
					<br>
					<a href="#" onclick="var x = jQuery('#pid1').val(); jQuery('#pid1').val(jQuery('#pid2').val()); jQuery('#pid2').val(x); return false;"><?php /* I18N: Reverse the order of two individuals */ echo I18N::translate('Swap individuals'); ?></a>
				</td>
				<td class="optionbox">
					<label>
						<input type="radio" name="find_all" value="0" <?php echo $find_all ? '' : 'checked'; ?>>
						<?php echo I18N::translate('Find the closest relationships'); ?>
					</label>
					<br>
					<label>
						<input type="radio" name="find_all" value="1"<?php echo $find_all ? 'checked' : ''; ?>>
						<?php echo I18N::translate('Find all possible relationships'); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</form>
<?php

if ($person1 && $person2) {
	if (I18N::direction() === 'ltr') {
		$horizontal_arrow = '<br><i class="icon-rarrow"></i>';
		$diagonal1        = Theme::theme()->parameter('image-dline');
		$diagonal2        = Theme::theme()->parameter('image-dline2');
	} else {
		$horizontal_arrow = '<br><i class="icon-larrow"></i>';
		$diagonal1        = Theme::theme()->parameter('image-dline2');
		$diagonal2        = Theme::theme()->parameter('image-dline');
	}
	$up_arrow   = ' <i class="icon-uarrow"></i>';
	$down_arrow = ' <i class="icon-darrow"></i>';

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
		$table = array();
		// Current position in the grid.
		$x     = 0;
		$y     = 0;
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
					$table[$x + 1][$y] = '<div style="background:url(' . Theme::theme()->parameter('image-hline') . ') repeat-x center; width: 64px; text-align: center"><div style="height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $WT_TREE), Individual::getInstance($path[$n + 1], $WT_TREE)) . '</div><div style="height: 32px;">' . $horizontal_arrow . '</div></div>';
					$x += 2;
					break;
				case 'son':
				case 'dau':
				case 'chi':
					if ($n > 2 && preg_match('/fat|mot|par/', $relationships[$n - 2])) {
						$table[$x + 1][$y - 1] = '<div style="background:url(' . $diagonal2 . '); width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: end;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $WT_TREE), Individual::getInstance($path[$n + 1], $WT_TREE)) . '</div><div style="height: 32px; text-align: start;">' . $down_arrow . '</div></div>';
						$x += 2;
					} else {
						$table[$x][$y - 1] = '<div style="background:url(' . Theme::theme()
								->parameter('image-vline') . ') repeat-y center; height: 64px; text-align: center;"><div style="display: inline-block; width:50%; line-height: 64px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $WT_TREE), Individual::getInstance($path[$n + 1], $WT_TREE)) . '</div><div style="display: inline-block; width:50%; line-height: 64px;">' . $down_arrow . '</div></div>';
					}
					$y -= 2;
					break;
				case 'fat':
				case 'mot':
				case 'par':
					if ($n > 2 && preg_match('/son|dau|chi/', $relationships[$n - 2])) {
						$table[$x + 1][$y + 1] = '<div style="background:url(' . $diagonal1 . '); background-position: top right; width: 64px; height: 64px; text-align: center;"><div style="height: 32px; text-align: start;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $WT_TREE), Individual::getInstance($path[$n + 1], $WT_TREE)) . '</div><div style="height: 32px; text-align: end;">' . $up_arrow . '</div></div>';
						$x += 2;
					} else {
						$table[$x][$y + 1] = '<div style="background:url(' . Theme::theme()
								->parameter('image-vline') . ') repeat-y center; height: 64px; text-align:center; "><div style="display: inline-block; width: 50%; line-height: 32px;">' . Functions::getRelationshipNameFromPath($relationships[$n], Individual::getInstance($path[$n - 1], $WT_TREE), Individual::getInstance($path[$n + 1], $WT_TREE)) . '</div><div style="display: inline-block; width: 50%; line-height: 32px">' . $up_arrow . '</div></div>';
					}
					$y += 2;
					break;
				}
				$max_x = max($max_x, $x);
				$min_y = min($min_y, $y);
				$max_y = max($max_y, $y);
			} else {
				$individual = Individual::getInstance($xref, $WT_TREE);
				ob_start();
				FunctionsPrint::printPedigreePerson($individual, $show_full);
				$table[$x][$y] = ob_get_clean();
			}
		}
		echo '<table style="border-collapse: collapse; margin: 20px 50px;">';
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
