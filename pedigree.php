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
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PedigreeController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

define('WT_SCRIPT_NAME', 'pedigree.php');
require './includes/session.php';

$controller = new PedigreeController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('
	(function() {
		autocomplete();
// I dont think this is still a problem with version 41.0.2272.76 m
//		jQuery("html").css("overflow","visible"); // workaround for chrome v37 canvas bugs

		jQuery("#childarrow").on("click", ".menuselect", function(e) {
			e.preventDefault();
			jQuery("#childbox").slideToggle("fast");
		});

		jQuery("#pedigree_chart")
			.width('  . $controller->chartsize['x'] . ')
			.height(' . $controller->chartsize['y'] . ');

		// Set variables
		var	p0, p1, p2,  // Holds the ids of the boxes used in the join calculations
			canvas       = jQuery("#pedigree_canvas"),
			ctx          = canvas[0].getContext("2d"),
			nodes        = jQuery(".shadow").length,
			gen1Start    = Math.ceil(nodes / 2),
			boxWidth     = jQuery(".person_box_template").first().outerWidth(),
			boxHeight    = jQuery(".person_box_template").first().outerHeight(),
			useOffset    = true,
			extraOffsetX = Math.floor(boxWidth / 15), // set offsets to be sensible fractions of the box size
			extraOffsetY = Math.floor(boxHeight / 10),
			addOffset;

		// Draw joining lines on the <canvas>
		function drawLines(context, x1, y1, x2, y2) {
		    x1 = Math.floor(x1);
	        y1 = Math.floor(y1);
		    x2 = Math.floor(x2);
		    y2 = Math.floor(y2);
			if (' . json_encode($controller->orientation < $controller::OLDEST_AT_TOP) . ') {
				context.moveTo(x1, y1);
				context.lineTo(x2, y1);
				context.lineTo(x2, y2);
				context.lineTo(x1, y2);
			} else {
				context.moveTo(x1, y1);
				context.lineTo(x1, y2);
				context.lineTo(x2, y2);
				context.lineTo(x2, y1);
			}
		}

		//Plot the lines
		switch (' . $controller->orientation . ') {
		case ' . $controller::PORTRAIT . ':
			useOffset = false;
			// Drop through
		case ' . $controller::LANDSCAPE . ':
			for (var i = 2; i < nodes; i+=2) {
				p0 = jQuery("#sosa_" + i);
				p1 = jQuery("#sosa_" + (i+1));
				// change line y position if within 10% of box top/bottom
				addOffset = boxHeight / (p1.position().top - p0.position().top) > 0.9 ? extraOffsetY: 0;
				if (' . json_encode(I18N::direction() === "rtl") . ') {
					drawLines(
						ctx,
						p0.position().left + p0.width(),
						p0.position().top + (boxHeight / 2) + addOffset,
						p0.position().left + p0.width() + extraOffsetX,
						p1.position().top + (boxHeight / 2) - addOffset
					);
				} else {
					drawLines(
						ctx,
						p0.position().left,
						p0.position().top + (boxHeight / 2) + addOffset,
						p0.position().left - extraOffsetX,
						p1.position().top + (boxHeight / 2) - addOffset
					);
				}
			}
			break;
		case ' . $controller::OLDEST_AT_TOP . ':
				useOffset = false;
				// Drop through
		case ' . $controller::OLDEST_AT_BOTTOM . ':
			for (var i = 1; i < gen1Start; i++) {
				p0 = jQuery("#sosa_" + i);
				p1 = jQuery("#sosa_" + (i*2));
				p2 = jQuery("#sosa_" + (i*2+1));
				addOffset = i*2 >= gen1Start ? extraOffsetX : 0;
				var templateHeight = p0.children(".person_box_template").outerHeight(),
					// bHeight taks account of offset when root person has a menu icon
					bHeight = useOffset ? (p0.outerHeight() - templateHeight) + (templateHeight / 2) : templateHeight / 2;
				drawLines(
					ctx,
					p1.position().left + (boxWidth / 2) + addOffset,
					p1.position().top + boxHeight,
					p2.position().left + (boxWidth / 2) - addOffset,
					p0.position().top + bHeight
				);
			}
			break;
		}

		// Set line styles & draw them
		ctx.strokeStyle   = canvas.css("color");
		ctx.lineWidth     = ' . Theme::theme()->parameter('line-width') . ';
		ctx.shadowColor   = ' . json_encode(Theme::theme()->parameter('shadow-color')) . ';
		ctx.shadowBlur    = ' . Theme::theme()->parameter('shadow-blur') . ';
		ctx.shadowOffsetX = ' . Theme::theme()->parameter('shadow-offset-x') . ';
		ctx.shadowOffsetY = ' . Theme::theme()->parameter('shadow-offset-y') . ';
		ctx.stroke();
	})();
	');

?>
<div id="pedigree-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<form name="people" id="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
		<input type="hidden" name="show_full" value="<?php echo $controller->showFull(); ?>">
		<table class="list_table">
			<tbody>
				<tr>
					<th class="descriptionbox wrap">
						<?php echo I18N::translate('Individual'); ?>
					</th>
					<th class="descriptionbox wrap">
						<?php echo I18N::translate('Generations'); ?>
					</th>
					<th class="descriptionbox wrap">
						<?php echo I18N::translate('Layout'); ?>
					</th>
					<th class="descriptionbox wrap">
						<?php echo I18N::translate('Show details'); ?>
					</th>
					<th rowspan="2" class="facts_label03">
						<input type="submit" value="<?php echo I18N::translate('View'); ?>">
					</th>
				</tr>
				<tr>
					<td class="optionbox">
						<input class="pedigree_form" data-autocomplete-type="INDI" type="text" id="rootid" name="rootid"
							size="3" value="<?php echo $controller->root->getXref(); ?>">
						<?php echo FunctionsPrint::printFindIndividualLink('rootid'); ?>
					</td>
					<td class="optionbox center">
						<?php echo FunctionsEdit::editFieldInteger('PEDIGREE_GENERATIONS', $controller->generations, 3, $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS')); ?>
					</td>
					<td class="optionbox center">
						<?php echo FunctionsEdit::selectEditControl('orientation', array(0 => I18N::translate('Portrait'), 1 => I18N::translate('Landscape'), 2 => I18N::translate('Oldest at top'), 3 => I18N::translate('Oldest at bottom')), null, $controller->orientation); ?>
					</td>
					<td class="optionbox center">
						<?php echo FunctionsEdit::twoStateCheckbox('show_full', $controller->showFull()); ?>
					</td>
				</tr>
			</table>
		</tbody>
	</form>
<?php
if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';

	return;
}

$posn         = I18N::direction() === 'rtl' ? 'right' : 'left';
$lastgenStart = (int) floor($controller->treesize / 2);

echo '<div id="pedigree_chart" class="layout', $controller->orientation, '">';

//Output the chart
foreach ($controller->nodes as $i => $node) {

	// -- draw the box
	printf('<div id="sosa_%s" class="shadow" style="%s:%spx; top:%spx">', $i + 1, $posn, $node["x"], $node["y"]);

	if ($controller->orientation === $controller::OLDEST_AT_TOP) {
		if ($i >= $lastgenStart) {
			echo $controller->gotoPreviousGen($i);
		}
	} else {
		if (!$i) {
			echo $controller->getMenu();
		}
	}

	FunctionsPrint::printPedigreePerson($controller->nodes[$i]['indi'], $controller->showFull());

	if ($controller->orientation === $controller::OLDEST_AT_TOP) {
		if (!$i) {
			echo $controller->getMenu();
		}
	} else {
		if ($i >= $lastgenStart) {
			echo $controller->gotoPreviousGen($i);
		}
	}
	echo '</div>';
}

echo '<canvas id="pedigree_canvas" width="' . $controller->chartsize['x'] . '" height="' . $controller->chartsize['y'] . '"><p>No lines between boxes? Unfortunately your browser does not support the HTML5 canvas feature.</p></canvas>';
echo '</div>'; //close #pedigree_chart
echo '</div>'; //close #pedigree-page

