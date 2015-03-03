<?php
namespace Fisharebest\Webtrees;

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

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'pedigree.php');
require './includes/session.php';

define('ARROW_WRAPPER', '<div class="ancestorarrow" style="%s:%spx; top:%spx;">');
define('MENU_WRAPPER', '<div id="childarrow" style="%s:%spx; top:%spx"><div><a href="#" class="menuselect %s"></a><div id="childbox">');
define('MENU_ITEM', '<a href="pedigree.php?rootid=%s&amp;show_full=%s&amp;PEDIGREE_GENERATIONS=%s&amp;orientation=%s" class="%s">%s</a>');
define('BOX_WRAPPER', '<div class="shadow" style="%s:%spx; top:%spx">');

$controller = new PedigreeController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<div id="pedigree-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<form name="people" id="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo Filter::escapeHtml(WT_GEDCOM); ?>">
		<input type="hidden" name="show_full" value="<?php echo $controller->showFull(); ?>">
		<table class="list_table">
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
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="optionbox center">
					<?php echo edit_field_integers('PEDIGREE_GENERATIONS', $controller->generations, 3, $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS')); ?>
				</td>
				<td class="optionbox center">
					<?php echo select_edit_control('orientation', array(0 => I18N::translate('Portrait'), 1 => I18N::translate('Landscape'), 2 => I18N::translate('Oldest at top'), 3 => I18N::translate('Oldest at bottom')), null, $controller->orientation); ?>
				</td>
				<td class="optionbox center">
					<?php echo two_state_checkbox("show_full", $controller->showFull());?>
				</td>
			</tr>
		</table>
	</form>
<?php
if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	
	return;
}

$posn = I18N::direction() === 'rtl' ? 'right' : 'left';

echo '<div id="pedigree_chart" class="layout', $controller->orientation, '">';
//-- echo the boxes
$curgen    = 1;
$xoffset   = 0;
$yoffset   = 0; // -- used to offset the position of each box as it is generated
$lineDrawx = array(); // -- used to position joining lines on <canvas>
$lineDrawy = array(); // -- used to position joining lines on <canvas>
$bxspacing = Theme::theme()->parameter('chart-spacing-x');
$byspacing = Theme::theme()->parameter('chart-spacing-y');

for ($i = ($controller->treesize - 1); $i >= 0; $i--) {
	// set positions for joining lines
	$lineDrawx[$i] = $xoffset;
	$lineDrawy[$i] = $yoffset;
	// -- check to see if we have moved to the next generation
	if ($i < (int) ($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}

	if ($controller->orientation < $controller::OLDEST_AT_TOP) {
		// Portrait 0 Landscape 1 top 2 bottom 3
		$xoffset = $controller->offsetarray[$i]["x"];
		$yoffset = $controller->offsetarray[$i]["y"];
	} else {
		$xoffset = $controller->offsetarray[$i]["y"];
		$yoffset = $controller->offsetarray[$i]["x"];
	}
	// -- draw the box

	// Can we go back to an earlier generation?
	$can_go_back = $curgen == 1 && $controller->ancestors[$i] && $controller->ancestors[$i]->getChildFamilies();

	if ($controller->orientation === $controller::OLDEST_AT_TOP) {
		// oldest at top
		if ($can_go_back) {
			printf(ARROW_WRAPPER, $posn, $xoffset + $controller->getBoxDimensions()->width / 2, $yoffset - $controller::ARROW_SIZE);
			$did = 1;
			if ($i > (int) ($controller->treesize / 2) + (int) ($controller->treesize / 4)) {
				$did++;
			}
			printf(MENU_ITEM, $controller->ancestors[$did]->getXref(), $controller->showFull(), $controller->generations, $controller->orientation, 'icon-uarrow noprint', '');
			echo '</div>';
		}
	}
	// beginning of box setup and display

	printf(BOX_WRAPPER, $posn, $xoffset, $yoffset);

	print_pedigree_person($controller->ancestors[$i], $controller->showFull());
	if ($can_go_back) {
		$did = 1;
		if ($i > (int) ($controller->treesize / 2) + (int) ($controller->treesize / 4)) {
			$did++;
		}
		if (I18N::direction() === 'rtl') {
			$arrow = 'icon-larrow';
		} else {
			$arrow = 'icon-rarrow';
		}
		if ($controller->orientation === $controller::OLDEST_AT_BOTTOM) {
			printf(ARROW_WRAPPER, $posn, $controller->getBoxDimensions()->width / 2, $controller->getBoxDimensions()->height + $byspacing);
			printf(MENU_ITEM, $controller->ancestors[$did]->getXref(), $controller->showFull(), $controller->generations, $controller->orientation, 'icon-darrow noprint', '');
			echo '</div>';
		} elseif ($controller->orientation < $controller::OLDEST_AT_TOP) {
			printf(ARROW_WRAPPER, $posn, $controller->getBoxDimensions()->width + ($bxspacing * 2), $controller->getBoxDimensions()->height / 2 - $byspacing);
			printf(MENU_ITEM, $controller->ancestors[$did]->getXref(), $controller->showFull(), $controller->generations, $controller->orientation, "$arrow noprint", '');
			echo '</div>';
		}
	}
	echo '</div>';
}
// -- echo left arrow for decendants so that we can move down the tree
$yoffset += ($controller->getBoxDimensions()->height / 2) - $byspacing;
$famids = $controller->root->getSpouseFamilies();
//-- make sure there is more than 1 child in the family with parents
$cfamids = $controller->root->getChildFamilies();

if (count($famids) > 0) {
	if (I18N::direction() === 'rtl') {
		$arrow = 'icon-rarrow';
	} else {
		$arrow = 'icon-larrow';
	}
	switch ($controller->orientation) {
		case $controller::PORTRAIT:
			//drop through
		case $controller::LANDSCAPE:
			$offsetx = 0;
			$offsety = $yoffset;
			break;
		case $controller::OLDEST_AT_TOP:
			$offsetx = $xoffset + ($controller->getBoxDimensions()->width / 2);
			$offsety = $yoffset + $controller->getBoxDimensions()->height / 2 + $controller::ARROW_SIZE;
			$arrow   = 'icon-darrow';
			break;
		case $controller::OLDEST_AT_BOTTOM:
			$offsetx = $xoffset + ($controller->getBoxDimensions()->width / 2);
			$offsety = $yoffset - $controller->getBoxDimensions()->height / 2 - $byspacing;
			$arrow   = 'icon-uarrow';
			break;
	}
	printf(MENU_WRAPPER, $posn, $offsetx, $offsety, $arrow);

	foreach ($famids as $family) {
		echo '<span class="name1">', I18N::translate('Family'), '</span>';
		$spouse = $family->getSpouse($controller->root);
		if ($spouse) {
			printf(MENU_ITEM, $spouse->getXref(), $controller->showFull(), $controller->generations, $controller->orientation, 'name1', $spouse->getFullName());
		}
		$children = $family->getChildren();
		foreach ($children as $child) {
			printf(MENU_ITEM, $child->getXref(), $controller->showFull(), $controller->generations, $controller->orientation, 'name1', $child->getFullName());
		}
	}
	//-- echo the siblings
	foreach ($cfamids as $family) {
		$siblings = array_filter($family->getChildren(), function (Individual $item) use ($controller) {
			return $controller->root->getXref() !== $item->getXref();
		});
		$num      = count($siblings);
		if ($num) {
			echo '<span class="name1">';
			echo $num > 1 ? I18N::translate('Siblings') : I18N::translate('Sibling');
			echo '</span>';
			foreach ($siblings as $child) {
				printf(MENU_ITEM, $child->getXref(), $controller->showFull(), $controller->generations, $controller->orientation, 'name1', $child->getFullName());
			}
		}
	}
	echo
		'</div>', // #childbox
		'</div>',
		'</div>'; // #childarrow
}
// calculate chart & canvas dimensions
$max_xoffset = max(array_map(function($item) {return $item['x'];}, $controller->offsetarray));
$max_yoffset = max(array_map(function($item) {return $item['y'];}, $controller->offsetarray));
if ($controller->orientation < $controller::OLDEST_AT_TOP) {
	$chartHeight = $max_yoffset + $byspacing + $controller->getBoxDimensions()->height;
	$chartWidth  = $max_xoffset + $bxspacing + $controller->getBoxDimensions()->width + $controller::ARROW_SIZE;
} else {
	$chartWidth  = $max_yoffset + $bxspacing + $controller->getBoxDimensions()->width;
	$chartHeight = $max_xoffset + $byspacing + $controller->getBoxDimensions()->height + $controller::ARROW_SIZE;
}

echo '<canvas id="pedigree_canvas" width="' . $chartWidth . '" height="' . $chartHeight . '"><p>No lines between boxes? Unfortunately your browser does not support the HTML5 canvas feature.</p></canvas>';
echo '</div>'; //close #pedigree_chart
echo '</div>'; //close #pedigree-page

// Give <div id="pedigree_chart"> a size to envelop the absolutely-positioned elements.
$controller->addInlineJavascript('
	var WT_PEDIGREE_CHART = (function() {
	jQuery("html").css("overflow","visible"); // workaround for chrome v37 canvas bugs
	jQuery("#pedigree_chart")
		.width(' . $chartWidth . ')
		.height(' . $chartHeight . ');
	// Draw joining lines in <canvas>
	// Set variables
	var textdirection = "' . I18N::direction() . '",
		orientation = ' . $controller->orientation . ',
		canvaswidth = ' . $chartWidth . ',
		offset_x = 20,
		offset_y = ' . $controller->getBoxDimensions()->height . '/2+' . Theme::theme()->parameter('line-width') . ',
		lineDrawx = new Array("' . join(array_reverse($lineDrawx), '","') . '"),
		lineDrawy = new Array("' . join(array_reverse($lineDrawy), '","') . '"),
		offset_x2 = ' . $controller->getBoxDimensions()->width . '/2+' . Theme::theme()->parameter('line-width') . ',
		offset_y2 = ' . $controller->getBoxDimensions()->height . '*2,
		lineDrawx2 = new Array("' . join($lineDrawx, '","') . '"),
		lineDrawy2 = new Array("' . join($lineDrawy, '","') . '"),
		maxjoins = Math.pow(2,' . $controller->generations . '),
		ctx = jQuery("#pedigree_canvas")[0].getContext("2d");

	// Set line styles
	ctx.strokeStyle = jQuery("#pedigree_canvas").css("color");
	ctx.lineWidth = ' . Theme::theme()->parameter('line-width') . ';
	ctx.shadowColor = "' . Theme::theme()->parameter('shadow-color') . '";
	ctx.shadowBlur = ' . Theme::theme()->parameter('shadow-blur') . ';
	ctx.shadowOffsetX = ' . Theme::theme()->parameter('shadow-offset-x') . ';
	ctx.shadowOffsetY = ' . Theme::theme()->parameter('shadow-offset-y') . ';

	//Draw the lines
	switch (orientation) {
	case ' . $controller::PORTRAIT . ':
		// drop through
	case ' . $controller::LANDSCAPE . ':
		for (var i = 0; i <= maxjoins-3; i+=2) {
			if (textdirection == "rtl") {
				ctx.moveTo(canvaswidth-lineDrawx[i],lineDrawy[i]-0+offset_y+offset_x/2);
				ctx.lineTo(canvaswidth-lineDrawx[i]+offset_x,lineDrawy[i]-0+offset_y+offset_x/2);
				ctx.lineTo(canvaswidth-lineDrawx[i+1]+offset_x,lineDrawy[i+1]-0+offset_y-offset_x/2);
				ctx.lineTo(canvaswidth-lineDrawx[i+1],lineDrawy[i+1]-0+offset_y-offset_x/2);
			} else {
				ctx.moveTo(lineDrawx[i],lineDrawy[i]-0+offset_y+offset_x/2);
				ctx.lineTo(lineDrawx[i]-offset_x,lineDrawy[i]-0+offset_y+offset_x/2);
				ctx.lineTo(lineDrawx[i+1]-offset_x,lineDrawy[i+1]-0+offset_y-offset_x/2);
				ctx.lineTo(lineDrawx[i+1],lineDrawy[i+1]-0+offset_y-offset_x/2);
			}
		}
		break;
	case ' . $controller::OLDEST_AT_TOP . ':
		for (var i = 1; i <= maxjoins; i+=2) {
			if (textdirection == "rtl") {
				ctx.moveTo(lineDrawx2[i]-0+offset_x2-offset_x,lineDrawy2[i]);
				ctx.lineTo(lineDrawx2[i]-0+offset_x2-offset_x,lineDrawy2[i]-0+offset_y2);
				ctx.lineTo(lineDrawx2[i+1]-0+offset_x2+offset_x/2,lineDrawy2[i]-0+offset_y2);
				ctx.lineTo(lineDrawx2[i+1]-0+offset_x2+offset_x/2,lineDrawy2[i]);
			} else {
				ctx.moveTo(lineDrawx2[i]-0+offset_x2-offset_x/2,lineDrawy2[i]);
				ctx.lineTo(lineDrawx2[i]-0+offset_x2-offset_x/2,lineDrawy2[i]-0+offset_y2);
				ctx.lineTo(lineDrawx2[i+1]-0+offset_x2+offset_x/2,lineDrawy2[i]-0+offset_y2);
				ctx.lineTo(lineDrawx2[i+1]-0+offset_x2+offset_x/2,lineDrawy2[i]);
			}
		}
		break;
	case ' . $controller::OLDEST_AT_BOTTOM . ':
		// drop through
	default:  // anything else
		for (var i = 1; i <= maxjoins; i+=2) {
			ctx.moveTo(lineDrawx2[i]-0+offset_x2-offset_x,lineDrawy2[i]);
			ctx.lineTo(lineDrawx2[i]-0+offset_x2-offset_x,lineDrawy2[i]-offset_y2/2);
			ctx.lineTo(lineDrawx2[i+1]-0+offset_x2+offset_x/2,lineDrawy2[i]-offset_y2/2);
			ctx.lineTo(lineDrawx2[i+1]-0+offset_x2+offset_x/2,lineDrawy2[i]);
		}
	}
	ctx.stroke();

	jQuery("#childarrow").on("click", ".menuselect", function(e) {
		e.preventDefault();
		jQuery("#childbox").slideToggle("fast");
	});
	return "' . strip_tags($controller->root->getFullName()) . '";
	})();
');
