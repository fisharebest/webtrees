<?php
// View for the pedigree tree.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

define('WT_SCRIPT_NAME', 'pedigree.php');
require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

define("ARROW_WRAPPER", "<div class='ancestorarrow' style='%s:%spx; top:%spx;'>");
define("MENU_WRAPPER" , "<div id='childarrow' style='%s:%spx; top:%spx'><div><a href='#' class='menuselect %s'></a><div id='childbox'>");
define("MENU_ITEM"    , "<a href='pedigree.php?rootid=%s&amp;show_full=%s&amp;PEDIGREE_GENERATIONS=%s&amp;talloffset=%s' class='%s'>%s</a>");
define("BOX_WRAPPER"  , "<div class='shadow' style='%s:%spx; top:%spx; width:%spx; height:%spx'>");

$controller = new WT_Controller_Pedigree();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();');

?>
<div id="pedigree-page">
	<h2><?php echo $controller->getPageTitle(); ?></h2>

	<form name="people" id="people" method="get" action="?">
		<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
		<input type="hidden" name="show_full" value="<?php echo $controller->show_full; ?>">
		<table class="list_table">
			<tr>
				<th class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Individual'); ?>
				</th>
				<th class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Generations'); ?>
				</th>
				<th class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Layout'); ?>
				</th>
				<th class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Show details'); ?>
				</th>
				<th rowspan="2" class="facts_label03">
					<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
				</th>
			</tr>
			<tr>
				<td class="optionbox">
					<input class="pedigree_form" data-autocomplete-type="INDI" type="text" id="rootid" name="rootid"
					       size="3" value="<?php echo $controller->root->getXref(); ?>">
					<?php echo print_findindi_link('rootid'); ?>
				</td>
				<td class="optionbox center">
					<?php echo edit_field_integers('PEDIGREE_GENERATIONS', $controller->PEDIGREE_GENERATIONS, 3, $MAX_PEDIGREE_GENERATIONS); ?>
				</td>
				<td class="optionbox center">
					<?php echo select_edit_control('talloffset', array(0 => WT_I18N::translate('Portrait'), 1 => WT_I18N::translate('Landscape'), 2 => WT_I18N::translate('Oldest at top'), 3 => WT_I18N::translate('Oldest at bottom')), null, $talloffset); ?>
				</td>
				<td class="optionbox center">
					<input type="checkbox" value="<?php if ($controller->show_full) {
						echo "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
					} else {
						echo "0\" onclick=\"document.people.show_full.value='1';";
					} ?>">
				</td>
			</tr>
		</table>
	</form>
<?php
if ($controller->error_message) {
	echo '<p class="ui-state-error">', $controller->error_message, '</p>';
	exit;
}

$posn = $TEXT_DIRECTION == 'rtl' ? 'right' : 'left';

echo '<div id="pedigree_chart" class="layout', $talloffset, '">';
//-- echo the boxes
$curgen      = 1;
$xoffset     = 0;
$yoffset     = 0;     // -- used to offset the position of each box as it is generated
$prevxoffset = 0; // -- used to track the horizontal x position of the previous box
$prevyoffset = 0; // -- used to track the vertical y position of the previous box
$maxyoffset  = 0;
$lineDrawx   = array(); // -- used to position joining lines on <canvas>
$lineDrawy   = array(); // -- used to position joining lines on <canvas>

for ($i = ($controller->treesize - 1); $i >= 0; $i--) {
	// set positions for joining lines
	$lineDrawx[$i] = $xoffset;
	$lineDrawy[$i] = $yoffset - 200; // 200 adjustment necessary to move canvas below menus and options. Matched to similar amount on canvas style.
	// -- check to see if we have moved to the next generation
	if ($i < (int)($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}
	$prevxoffset = $xoffset;
	$prevyoffset = $yoffset;
	if ($talloffset < 2) { // Portrait 0 Landscape 1 top 2 bottom 3
		$xoffset = $controller->offsetarray[$i]["x"];
		$yoffset = $controller->offsetarray[$i]["y"];
	} else {
		$xoffset = $controller->offsetarray[$i]["y"];
		$yoffset = $controller->offsetarray[$i]["x"];
	}
	// -- draw the box
	if ($yoffset > $maxyoffset) {
		$maxyoffset = $yoffset;
	}
	// Can we go back to an earlier generation?
	$can_go_back = $curgen == 1 && $controller->ancestors[$i] && $controller->ancestors[$i]->getChildFamilies();

	if ($talloffset == 2) { // oldest at top
		if ($can_go_back) {
			printf(ARROW_WRAPPER, $posn, $xoffset + $controller->pbwidth / 2, $yoffset-22);
			$did = 1;
			if ($i > (int)($controller->treesize / 2) + (int)($controller->treesize / 4)) {
				$did++;
			}
			printf(MENU_ITEM, $controller->ancestors[$did]->getXref(), $controller->show_full, $controller->PEDIGREE_GENERATIONS, $talloffset, 'icon-uarrow noprint', '');
			echo '</div>';
		}
	}
	// beginning of box setup and display
	//Correct box spacing for different layouts

	if (($talloffset == 3) && ($curgen == 1)) {
		$yoffset += 25;
	}
	if (($talloffset == 3) && ($curgen == 2)) {
		$yoffset += 10;
	}

	printf(BOX_WRAPPER, $posn, $xoffset, $yoffset, $controller->pbwidth, $controller->pbheight);

	print_pedigree_person($controller->ancestors[$i]);
	if ($can_go_back) {
		$did = 1;
		if ($i > (int)($controller->treesize / 2) + (int)($controller->treesize / 4)) {
			$did++;
		}
		if ($TEXT_DIRECTION == "rtl") {
			$arrow = 'icon-larrow';
		} else {
			$arrow = 'icon-rarrow';
		}
		if ($talloffset == 3) {
			printf(ARROW_WRAPPER, $posn, $controller->pbwidth / 2, $controller->pbheight+5);
			printf(MENU_ITEM, $controller->ancestors[$did]->getXref(), $controller->show_full, $controller->PEDIGREE_GENERATIONS, $talloffset, 'icon-darrow noprint', '');
			echo '</div>';
		} elseif ($talloffset < 2) {
			printf(ARROW_WRAPPER, $posn, $controller->pbwidth +5, $controller->pbheight / 2 - 10);
			printf(MENU_ITEM, $controller->ancestors[$did]->getXref(), $controller->show_full, $controller->PEDIGREE_GENERATIONS, $talloffset, "$arrow noprint", '');
			echo '</div>';
		}
	}
	echo '</div>';
}
// -- echo left arrow for decendants so that we can move down the tree
$yoffset += ($controller->pbheight / 2) - 10;
$famids = $controller->root->getSpouseFamilies();
//-- make sure there is more than 1 child in the family with parents
$cfamids = $controller->root->getChildFamilies();

if (count($famids) > 0) {
	if ($TEXT_DIRECTION == 'rtl') {
		$arrow = 'icon-rarrow';
	} else {
		$arrow = 'icon-larrow';
	}
	switch ($talloffset) {
		case 0:
			$offsetx = $PEDIGREE_GENERATIONS < 6 ? $offsetx = 60 * (5 - $PEDIGREE_GENERATIONS) : 0;
			$offsety = $yoffset;
			break;
		case 1:
			$offsetx = $PEDIGREE_GENERATIONS < 4 ? $basexoffset + 60 : $basexoffset;
			$offsety = $yoffset;
			break;
		case 2:
			$offsetx = $xoffset - 10 + $controller->pbwidth / 2;
			$offsety = $yoffset + $controller->pbheight / 2 + 10;
			$arrow = 'icon-darrow';
			break;
		case 3:
			$offsetx = $xoffset - 10 + $controller->pbwidth / 2;
			$offsety = $yoffset - $controller->pbheight / 2 - 10;
			$arrow = 'icon-uarrow';
			break;
	}
	printf(MENU_WRAPPER, $posn, $offsetx, $offsety, $arrow);

	foreach ($famids as $family) {
		echo '<span class="name1">', WT_I18N::translate('Family'), '</span>';
		$spouse = $family->getSpouse($controller->root);
		if ($spouse) {
			printf(MENU_ITEM, $spouse->getXref(), $controller->show_full, $controller->PEDIGREE_GENERATIONS, $talloffset, 'name1', $spouse->getFullName());
		}
		$children = $family->getChildren();
		foreach ($children as $child) {
			printf(MENU_ITEM, $child->getXref(), $controller->show_full, $controller->PEDIGREE_GENERATIONS, $talloffset, 'name1', $child->getFullName());
		}
	}
	//-- echo the siblings
	foreach ($cfamids as $family) {
		if ($family != null) {
			$siblings = array_filter($family->getChildren(), function (WT_Individual $item) use ($controller) {
				return $controller->rootid != $item->getXref();
			});
			$num      = count($siblings);
			if ($num) {
				echo "<span class='name1'>";
				echo $num > 1 ? WT_I18N::translate('Siblings') : WT_I18N::translate('Sibling');
				echo "</span>";
				foreach ($siblings as $child) {
					printf(MENU_ITEM, $child->getXref(), $controller->show_full, $controller->PEDIGREE_GENERATIONS, $talloffset, 'name1', $child->getFullName());
				}
			}
		}
	}
	echo
		'</div>', // #childbox
		'</div>',
		'</div>'; // #childarrow
}
// calculate canvas width
if ($talloffset < 2) {
	$canvaswidth = $PEDIGREE_GENERATIONS * ($controller->pbwidth + 20);
} else {
	$canvaswidth = pow(2, $PEDIGREE_GENERATIONS - 1) * ($controller->pbwidth + 20);
}
echo '<canvas id="pedigree_canvas" width="' . (int)($canvaswidth) . '" height="' . (int)($maxyoffset) . '"><p>No lines between boxes? Unfortunately your browser does not support he HTML5 canvas feature.</p></canvas>';
echo '</div>'; //close #pedigree_chart
echo '</div>'; //close #pedigree-page

// Expand <div id="content"> to include the absolutely-positioned elements.
$controller->addInlineJavascript('
	var WT_PEDIGREE_CHART = (function() {
	jQuery("html").css("overflow","visible"); // workaround for chrome v37 canvas bugs
	jQuery("#content").css("height", "' . ($maxyoffset + 30) . '");

	// Draw joining lines in <canvas>
	// Set variables
	var textdirection = "' . $TEXT_DIRECTION . '",
		talloffset = ' . $talloffset . ',
		canvaswidth = ' . ($canvaswidth) . ',
		offset_x = 20,
		offset_y = ' . $controller->pbheight . '/2+' . $controller->linewidth . ',
		lineDrawx = new Array("' . join(array_reverse($lineDrawx), '","') . '"),
		lineDrawy = new Array("' . join(array_reverse($lineDrawy), '","') . '"),
		offset_x2 = ' . $controller->pbwidth . '/2+' . $controller->linewidth . ',
		offset_y2 = ' . $controller->pbheight . '*2,
		lineDrawx2 = new Array("' . join($lineDrawx, '","') . '"),
		lineDrawy2 = new Array("' . join($lineDrawy, '","') . '"),
		maxjoins = Math.pow(2,' . $PEDIGREE_GENERATIONS . '),
		ctx = jQuery("#pedigree_canvas")[0].getContext("2d");

	// Set line styles
	ctx.strokeStyle = jQuery("#pedigree_canvas").css("color");
	ctx.lineWidth = ' . $controller->linewidth . ';
	ctx.shadowColor = "' . $controller->shadowcolor . '";
	ctx.shadowBlur = ' . $controller->shadowblur . ';
	ctx.shadowOffsetX = ' . $controller->shadowoffsetX . ';
	ctx.shadowOffsetY = ' . $controller->shadowoffsetY . ';

	//Draw the lines
	switch (talloffset) {
	case 0: // portrait
		// drop through
	case 1: // landscape
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
	case 2: // oldest at top
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
	case 3: // oldest at bottom
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
