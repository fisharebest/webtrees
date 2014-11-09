<?php
// Display a timeline chart for a group of individuals
//
// Use the $pids array to set which individuals to show on the chart
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

define('WT_SCRIPT_NAME', 'lifespan.php');
require './includes/session.php';

$controller = new WT_Controller_Lifespan();
$controller
	->pageHeader()
	->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
	->addInlineJavascript('autocomplete();')
	->addInlineJavascript('
	var timer;
	var offSetNum = 20; // amount timeline moves with each mouse click
	var speed;

	// method for scrolling timeline around in portal. takes in a string for the direction the timeline is moving "Left" "Right" "Top" "Down"
	function startScroll(move) {
		speed = parseInt(document.buttons.speedMenu.options[document.buttons.speedMenu.selectedIndex].value) * 25; //Sets the speed of the scroll feature
		timer = 1;
		scroll(move);
	}
	function scroll(move) {
		if (timer==null) return;  // If timer is not set timeline doesn\'t scroll
		timer = setTimeout("scroll(\'"+move+"\')",speed); // Keeps the timeline moving as long as the user holds down the mouse button on one of the direction arrows
		topInnerDiv = document.getElementById("topInner");
		innerDiv = document.getElementById("inner");
		myouterDiv = document.getElementById("lifespan_chart");
		//compares the direction the timeline is moving and how far it can move in each direction.
		if (move == "left" && ((maxX+topInnerDiv.offsetLeft+350) > (myouterDiv.offsetLeft+myouterDiv.offsetWidth))) {
			left = (innerDiv.offsetLeft - offSetNum)+"px";
			innerDiv.style.left = left;
			topInnerDiv.style.left = left;
		}
		else if (move == "right" && topInnerDiv.offsetLeft < (-10)) {
			right = (innerDiv.offsetLeft + offSetNum)+"px";
			innerDiv.style.left = right;
			topInnerDiv.style.left = right;
		}
		else if (move == "up" && innerDiv.offsetTop > maxY) {
			up = (innerDiv.offsetTop - offSetNum)+"px";
			innerDiv.style.top = up;
		}
		else if (move == "down" && innerDiv.offsetTop < -60) {
			down = (innerDiv.offsetTop + offSetNum)+"px";
			innerDiv.style.top = down;
		}
	}

	//method used to stop scrolling
	function stopScroll() {
		if (timer) clearTimeout(timer);
		timer=null;
	}

	var oldMx = 0;
	var oldMy = 0;
	var movei1 = "";
	var movei2 = "";
	function pandiv() {
		if (movei1=="") {
			oldMx = msX;
			oldMy = msY;
		}
		i = document.getElementById("topInner");
		movei1 = i;
		i = document.getElementById("inner");
		movei2 = i;
		return false;
	}
	function releaseimage() {
		movei1 = "";
		movei2 = "";
		return true;
	}
	// Main function to retrieve mouse x-y pos.s
	function getMouseXY(e) {
		var event = e || window.event;
		if (typeof event.pageX === "undefined" || typeof event.pageY === "undefined") {
			msX = event.clientX + document.documentElement.scrollLeft;
			msY = event.clientY + document.documentElement.scrollTop;
		} else {
			msX = e.pageX;
			msY = e.pageY;
		}
		// catch possible negative values in NS4
		if (msX < 0) {msX = 0;}
		if (msY < 0) {msY = 0;}
		if (movei1!="") {
		//ileft = parseInt(movei1.style.left);
		//itop = parseInt(movei2.style.top);
		var ileft = movei2.offsetLeft+1;
		var itop = movei2.offsetTop+1;
		ileft = ileft - (oldMx-msX);
		itop = itop - (oldMy-msY);
		movei1.style.left = ileft+"px";
		movei2.style.left = ileft+"px";
		movei2.style.top = itop+"px";
		oldMx = msX;
		oldMy = msY;
		return false;
		}
	}

	document.onmousemove = getMouseXY;
	document.onmouseup = releaseimage;
');

$people = count($controller->people);

?>
<div id="lifespan-page">
<h2><?php echo WT_I18N::translate('Lifespans'), help_link('lifespan_chart'); ?></h2>
	<table>
		<tr>
			<td>
				<form name="people" action="?">
					<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
					<table>
						<tr>
							<td class="person0" style="padding: 5px;" valign="top">
								<?php echo WT_I18N::translate('Add another individual to the chart'); ?>
								<br>
								<input class="pedigree_form" data-autocomplete-type="INDI" type="text" size="5" id="newpid" name="newpid">
								<?php print_findindi_link('newpid'); ?>
								<br>
								<div style="text-align: center;">
									<?php echo WT_I18N::translate('Include the individual’s immediate family?'); ?>
									<input type="checkbox" checked="checked" value="yes" name="addFamily">
								</div>
								<div style="text-align: center;">
									<input type="submit" value="<?php echo WT_I18N::translate('Add'); ?>">
								</div>
							</td>
						</tr>
					</table>
				</form>
			</td>
			<td>
				<form name="buttons" action="lifespan.php" method="get">
					<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
					<table>
						<tr>
							<td align="center"><?php echo WT_I18N::translate('Speed'); ?></td>
							<td align="center"><?php echo WT_I18N::translate('Begin year'); ?></td>
							<td align="center"><?php echo WT_I18N::translate('End year'); ?></td>
							<td align="center"><?php echo WT_Gedcom_Tag::getLabel('PLAC'); ?></td>
						</tr>
						<tr>
							<td>
								<select name="speedMenu" size="1">
									<option value="4">1</option>
									<option value="3">2</option>
									<option value="2">3</option>
									<option value="1">4</option>
								</select>
							</td>
							<td>
								<input type="text" name="beginYear" size="5" value="<?php echo $controller->beginYear==0 ? '' : $controller->beginYear; ?>">
							</td>
							<td>
								<input type="text" name="endYear" size="5" value="<?php echo $controller->endYear==0 ? '' : $controller->endYear; ?>">
							</td>
							<td>
								<input data-autocomplete-type="PLAC" type="text" name="place" size="15" value="<?php echo WT_Filter::escapeHtml($controller->place); ?>">
							</td>
							<td>
								<input type="submit" name="search" value="<?php echo WT_I18N::translate('Search'); ?>">
							</td>
							<td>
								<input type="button" value="<?php echo WT_I18N::translate('Clear chart'); ?>" onclick="window.location='lifespan.php?clear=1';">
							</td>
						</tr>
					</table>
					<b><?php echo WT_I18N::plural('%s individual', '%s individuals', $people, $people); ?></b>
				</form>
			</td>
		</tr>
	</table>
	<div dir="ltr" id="lifespan_chart" class="lifespan_outer">
		<div dir="ltr" id="topInner"  class="lifespan_timeline" onmousedown="pandiv(); return false;">';
			<?php $controller->printTimeline($controller->timelineMinYear,$controller->timelineMaxYear); ?>
		</div>
		<div id="inner" class="lifespan_people" onmousedown="pandiv(); return false;">
			<?php $maxY = $controller->fillTimeline($controller->people, $controller->YrowLoc); ?>
		</div>
		<!--  Floating div controls START -->
		<div dir="ltr" style="position:relative; z-index: 100; filter: alpha(opacity=67); -moz-opacity: 0.67;  opacity: 0.67; width:180px; top: 80px;">
			<table style="margin-left: 20px;" dir="ltr" border="0" cellpadding="0">
				<tr>
					<td></td>
					<td align="center"><a href="#" onclick="return false;" onmousedown="startScroll('down')" onmouseup="stopScroll()" class="icon-lsuparrow"></a></td>
					<td></td>
				</tr>
				<tr>
					<td><a href="#" onclick="return false;" onmousedown="startScroll('right')" onmouseup="stopScroll()" class="icon-lsltarrow"></a></td>
					<td align="center"></td>
					<td><a href="#" onclick="return false;" onmousedown="startScroll('left')" onmouseup="stopScroll()" class="icon-lsrtarrow"></a></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td align="center"><a href="#" onclick="return false;" onmousedown="startScroll('up')" onmouseup="stopScroll()" class="icon-lsdnarrow"></a></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
		<!--  Floating div controls END-->
	</div>
</div>
<?php
// Sets the boundaries for how far the timeline can move in the up direction
$controller->addInlineJavascript('var maxY = 80-' . $maxY . ';');
// Sets the boundaries for how far the timeline can move in the left direction
$controller->addInlineJavascript('var maxX = ' . (isset($maxX)?$maxX:0) . ';');
