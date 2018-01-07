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

use Fisharebest\Webtrees\Controller\PedigreeController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

require 'includes/session.php';

$controller = new PedigreeController;
$controller->restrictAccess(Module::isActiveChart($controller->tree(), 'pedigree_chart'));

// Only generate the content for interactive users (not search robots).
if (Filter::getBool('ajax') && Session::has('initiated')) {
	$posn         = I18N::direction() === 'rtl' ? 'right' : 'left';
	$lastgenStart = (int) floor($controller->treesize / 2);

	echo '<div id="pedigree_chart" class="layout', $controller->orientation, '">';

	//Output the chart
	foreach ($controller->nodes as $i => $node) {
		// -- draw the box
		$flex_direction = '';
		if ($controller->orientation === $controller::OLDEST_AT_TOP || $controller->orientation === $controller::OLDEST_AT_BOTTOM) {
			$flex_direction = ' flex-column';
		}
		printf('<div id="sosa_%s" class="shadow d-flex align-items-center' . $flex_direction . '" style="%s:%spx; top:%spx; position:absolute;">', $i + 1, $posn, $node['x'], $node['y']);

		if ($controller->orientation === $controller::OLDEST_AT_TOP) {
			if ($i >= $lastgenStart) {
				echo $controller->gotoPreviousGen($i);
			}
		} else {
			if (!$i) {
				echo $controller->getMenu();
			}
		}

		FunctionsPrint::printPedigreePerson($controller->nodes[$i]['indi']);

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

	echo '<canvas id="pedigree_canvas" width="' . $controller->chartsize['x'] . '" height="' . $controller->chartsize['y'] . '"></canvas>';
	echo '</div>';

	echo '
		<script>
		(function() {
			$("#childarrow").on("click", ".menuselect", function(e) {
				e.preventDefault();
				$("#childbox-pedigree").slideToggle("fast");
			});
	
			$("#pedigree_chart")
				.width(' . $controller->chartsize['x'] . ')
				.height(' . $controller->chartsize['y'] . ');
	
			// Set variables
			var p0, p1, p2,  // Holds the ids of the boxes used in the join calculations
				canvas       = $("#pedigree_canvas"),
				ctx          = canvas[0].getContext("2d"),
				nodes        = $(".shadow").length,
				gen1Start    = Math.ceil(nodes / 2),
				boxWidth     = $(".person_box_template").first().outerWidth(),
				boxHeight    = $(".person_box_template").first().outerHeight(),
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
					p0 = $("#sosa_" + i);
					p1 = $("#sosa_" + (i+1));
					// change line y position if within 10% of box top/bottom
					addOffset = boxHeight / (p1.position().top - p0.position().top) > 0.9 ? extraOffsetY: 0;
					if (' . json_encode(I18N::direction() === 'rtl') . ') {
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
					p0 = $("#sosa_" + i);
					p1 = $("#sosa_" + (i*2));
					p2 = $("#sosa_" + (i*2+1));
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
		</script>';

	return;
}

$controller->pageHeader();

?>
<h2 class="wt-page-title"><?= $controller->getPageTitle() ?></h2>

<form class="wt-page-options wt-page-options-pedigree-chart d-print-none">
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
		<label class="col-sm-3 col-form-label wt-page-options-label" for="PEDIGREE_GENERATIONS">
			<?= I18N::translate('Generations') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select(FunctionsEdit::numericOptions(range(2, $controller->tree()->getPreference('MAX_PEDIGREE_GENERATIONS'))), $controller->generations, ['id' => 'PEDIGREE_GENERATIONS', 'name' => 'PEDIGREE_GENERATIONS']) ?>
		</div>
	</div>

	<div class="row form-group">
		<label class="col-sm-3 col-form-label wt-page-options-label" for="orientation">
			<?= I18N::translate('Layout') ?>
		</label>
		<div class="col-sm-9 wt-page-options-value">
			<?= Bootstrap4::select([0 => I18N::translate('Portrait'), 1 => I18N::translate('Landscape'), 2 => I18N::translate('Oldest at top'), 3 => I18N::translate('Oldest at bottom')], $controller->orientation, ['id' => 'orientation', 'name' => 'orientation']) ?>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-sm-3 wt-page-options-label"></div>
		<div class="col-sm-9 wt-page-options-value">
			<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
		</div>
	</div>
</form>

<div class="wt-ajax-load wt-page-content wt-chart wt-pedigree-chart"></div>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    $(".wt-page-content").load(location.search + "&ajax=1");
  });
</script>
