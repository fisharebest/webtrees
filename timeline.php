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

use Fisharebest\Webtrees\Controller\TimelineController;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsPrint;

$basexoffset = 0;
$baseyoffset = 0;

define('WT_SCRIPT_NAME', 'timeline.php');
require './includes/session.php';

$controller = new TimelineController;
$controller
	->pageHeader()
	->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
	->addInlineJavascript('autocomplete();');

?>
<script>
var internet_explorer = typeof(document.all) !== 'undefined'; // mouse handling is different in IE
var ob = null;
var Y = 0;
var X = 0;
var oldx = 0;
var oldlinew = 0;
var personnum = 0;
var type = 0;
var boxmean = 0;

function ageCursorMouseDown(divbox, num) {
	ob = divbox;
	personnum = num;
	type = 0;
	X = ob.offsetLeft;
	Y = ob.offsetTop;
	if (internet_explorer) {
		oldx = event.clientX + document.documentElement.scrollLeft;
	}
}

function factMouseDown(divbox, num, mean) {
	ob = divbox;
	personnum = num;
	boxmean = mean;
	type = 1;
	oldx = ob.offsetLeft;
	if (internet_explorer) {
		oldlinew = event.clientX + document.documentElement.scrollLeft;
	} else {
		oldlinew = 0;
	}
}

document.onmousemove = function (e) {
	if (ob === null) {
		return true;
	}
	var tldiv = document.getElementById("timeline_chart");
	var newx = 0;
	var newy = 0;
	if (type === 0) {
		// age boxes
		if (internet_explorer) {
			newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
			newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
		} else {
			newy = e.pageY - tldiv.offsetTop;
			newx = e.pageX - tldiv.offsetLeft;
			if (oldx === 0) {
				oldx = newx;
			}
		}
		if (newy < topy - bheight / 2) {
			newy = topy - bheight / 2;
		}
		if (newy > bottomy) {
			newy = bottomy - 1;
		}
		ob.style.top = newy + "px";
		var tyear = (newy + bheight - 4 - topy + scale) / scale + baseyear;
		var year = Math.floor(tyear);
		var month = Math.floor(tyear * 12 - year * 12);
		var day = Math.floor(tyear * 365 - year * 365 - month * 30);
		var mstamp = year * 365 + month * 30 + day;
		var bdstamp = birthyears[personnum] * 365 + birthmonths[personnum] * 30 + birthdays[personnum];
		var daydiff = mstamp - bdstamp;
		var ba = 1;
		if (daydiff < 0) {
			ba = -1;
			daydiff = (bdstamp - mstamp);
		}
		var yage = Math.floor(daydiff / 365);
		var mage = Math.floor((daydiff - yage * 365) / 30);
		var dage = Math.floor(daydiff - yage * 365 - mage * 30);
		if (dage < 0) {
			mage = mage - 1;
		}
		if (dage < -30) {
			dage = 30 + dage;
		}
		if (mage < 0) {
			yage = yage - 1;
		}
		if (mage < -11) {
			mage = 12 + mage;
		}
		var yearform = document.getElementById('yearform' + personnum);
		var ageform = document.getElementById('ageform' + personnum);
		yearform.innerHTML = year + "      " + month + " <?php echo mb_substr(I18N::translate('Month:'), 0, 1); ?>   " + day + " <?php echo mb_substr(I18N::translate('Day:'), 0, 1); ?>";
		if (ba * yage > 1 || ba * yage < -1 || ba * yage === 0) {
			ageform.innerHTML = (ba * yage) + " <?php echo mb_substr(I18N::translate('years'), 0, 1); ?>   " + (ba * mage) + " <?php echo mb_substr(I18N::translate('Month:'), 0, 1); ?>   " + (ba * dage) + " <?php echo mb_substr(I18N::translate('Day:'), 0, 1); ?>";
		} else {
			ageform.innerHTML = (ba * yage) + " <?php echo mb_substr(I18N::translate('Year:'), 0, 1); ?>   " + (ba * mage) + " <?php echo mb_substr(I18N::translate('Month:'), 0, 1); ?>   " + (ba * dage) + " <?php echo mb_substr(I18N::translate('Day:'), 0, 1); ?>";
		}
		var line = document.getElementById('ageline' + personnum);
		var temp = newx - oldx;

		var textDirection = jQuery('html').attr('dir');
		if (textDirection === 'rtl') {
			temp = temp * -1;
		}
		line.style.width = (line.width + temp) + "px";
		oldx = newx;
		return false;
	} else {
		// fact boxes
		var linewidth;
		if (internet_explorer) {
			newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
			newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
			linewidth = event.clientX + document.documentElement.scrollLeft;
		} else {
			newy = e.pageY - tldiv.offsetTop;
			newx = e.pageX - tldiv.offsetLeft;
			if (oldx === 0) {
				oldx = newx;
			}
			linewidth = e.pageX;
		}
		// get diagnal line box
		var dbox = document.getElementById('dbox' + personnum);
		var etopy;
		var ebottomy;
		// set up limits
		if (boxmean - 175 < topy) {
			etopy = topy;
		} else {
			etopy = boxmean - 175;
		}
		if (boxmean + 175 > bottomy) {
			ebottomy = bottomy;
		} else {
			ebottomy = boxmean + 175;
		}
		// check if in the bounds of the limits
		if (newy < etopy) {
			newy = etopy;
		}
		if (newy > ebottomy) {
			newy = ebottomy;
		}
		// calculate the change in Y position
		var dy = newy - ob.offsetTop;
		// check if we are above the starting point and switch the background image
		var textDirection = jQuery('html').attr('dir');

		if (newy < boxmean) {
			if (textDirection === 'rtl') {
				dbox.style.backgroundImage = "url('<?php echo Theme::theme()->parameter('image-dline2'); ?>')";
				dbox.style.backgroundPosition = "0% 0%";
			} else {
				dbox.style.backgroundImage = "url('<?php echo Theme::theme()->parameter('image-dline'); ?>')";
				dbox.style.backgroundPosition = "0% 100%";
			}
			dy = -dy;
			dbox.style.top = (newy + bheight / 3) + "px";
		} else {
			if (textDirection === 'rtl') {
				dbox.style.backgroundImage = "url('<?php echo Theme::theme()->parameter('image-dline'); ?>')";
				dbox.style.backgroundPosition = "0% 100%";
			} else {
				dbox.style.backgroundImage = "url('<?php echo Theme::theme()->parameter('image-dline2'); ?>')";
				dbox.style.backgroundPosition = "0% 0%";
			}

			dbox.style.top = (boxmean + bheight / 3) + "px";
		}
		// the new X posistion moves the same as the y position
		if (textDirection === 'rtl') {
			newx = dbox.offsetRight + Math.abs(newy - boxmean);
		} else {
			newx = dbox.offsetLeft + Math.abs(newy - boxmean);
		}
		// set the X position of the box
		if (textDirection === 'rtl') {
			ob.style.right = newx + "px";
		} else {
			ob.style.left = newx + "px";
		}
		// set new top positions
		ob.style.top = newy + "px";
		// get the width for the diagnal box
		var newwidth = (ob.offsetLeft - dbox.offsetLeft);
		// set the width
		dbox.style.width = newwidth + "px";
		if (textDirection === 'rtl') {
			dbox.style.right = (dbox.offsetRight - newwidth) + 'px';
		}
		dbox.style.height = newwidth + "px";
		// change the line width to the change in the mouse X position
		line = document.getElementById('boxline' + personnum);
		if (oldlinew !== 0) {
			line.width = line.width + (linewidth - oldlinew);
		}
		oldlinew = linewidth;
		oldx = newx;
		return false;
	}
};

document.onmouseup = function () {
	ob = null;
	oldx = 0;
}

</script>
<h2><?php echo I18N::translate('Timeline'); ?></h2>
<form name="people" action="?">
	<input type="hidden" name="ged" value="<?php echo $WT_TREE->getNameHtml(); ?>">
	<table>
		<tr>
			<?php
			$i     = 0;
			$count = count($controller->people);
			$half  = $count;
			if ($count > 5) {
				$half = ceil($count / 2);
			}
			$half++;
			foreach ($controller->people as $p => $indi) {
				$pid = $indi->getXref();
				$col = $p % 6;
				if ($i === $half) {
					echo "</tr><tr>";
				}
				$i++;
				?>
				<td class="person<?php echo $col; ?>" style="padding: 5px;">
					<?php
					if ($indi && $indi->canShow()) {
						echo $indi->getSexImage('large');
						?>
						<a href="<?php echo $indi->getHtmlUrl(); ?>">&nbsp;<?php echo $indi->getFullName(); ?><br>
							<?php echo $indi->getAddName(); ?><br>
						</a>
						<input type="hidden" name="pids[<?php echo $p; ?>]" value="<?php echo Filter::escapeHtml($pid); ?>">
						<a href="timeline.php?<?php echo $controller->pidlinks; ?>&amp;scale=<?php echo $controller->scale; ?>&amp;remove=<?php echo $pid; ?>&amp;ged=<?php echo $WT_TREE->getNameUrl(); ?>" >
						<span class="details1"><?php echo I18N::translate('Remove individual'); ?></span></a>
						<?php if (!empty($controller->birthyears[$pid])) { ?>
							<span class="details1"><br>
								<?php echo /* I18N: an age indicator, which can be dragged around the screen */ I18N::translate('Show an age cursor?'); ?>
								<input type="checkbox" name="agebar<?php echo $p; ?>" value="ON" onclick="jQuery('#agebox<?php echo $p; ?>').toggle();">
							</span>
						<?php } ?>
						<br>
					<?php
					} else {
						echo '<div class="error">', I18N::translate('This information is private and cannot be shown.'), '</div>';
						?>
						<input type="hidden" name="pids[<?php echo $p; ?>]" value="<?php echo Filter::escapeHtml($pid); ?>">
						<br>
				<a href="timeline.php?<?php echo $controller->pidlinks; ?>&amp;scale=<?php echo $controller->scale; ?>&amp;remove=<?php echo $pid; ?>&amp;ged=<?php echo $WT_TREE->getNameUrl(); ?>" >
							<span class="details1"><?php echo I18N::translate('Remove individual'); ?></span></a>
						<br>
					<?php } ?>
				</td>
			<?php } ?>
			<td class="list_value" style="padding: 5px;" valign="top">
				<?php echo I18N::translate('Add another individual to the chart'), '<br>'; ?>
				<input class="pedigree_form" data-autocomplete-type="INDI" type="text" size="5" id="newpid" name="newpid">
				<?php echo FunctionsPrint::printFindIndividualLink('newpid'); ?>
				<br>
				<br>

				<div style="text-align: center;"><input type="submit" value="<?php echo I18N::translate('Add'); ?>"></div>
			</td>
			<?php
			if (count($controller->people) > 0) {
				$scalemod = round($controller->scale * .2) + 1;
				?>
				<td class="list_value" style="padding: 5px;">
					<a href="<?php echo WT_SCRIPT_NAME . "?" . $controller->pidlinks . "scale=" . ($controller->scale + $scalemod); ?>&amp;ged=<?php echo $WT_TREE->getNameUrl(); ?>" class="icon-zoomin" title="<?php echo I18N::translate('Zoom in'); ?>"></a><br>
					<a href="<?php echo WT_SCRIPT_NAME . "?" . $controller->pidlinks . "scale=" . ($controller->scale - $scalemod); ?>&amp;ged=<?php echo $WT_TREE->getNameUrl(); ?>" class="icon-zoomout" title="<?php echo I18N::translate('Zoom out'); ?>"></a><br>
					<input type="button" value="<?php echo I18N::translate('reset'); ?>" onclick="window.location = 'timeline.php?ged=<?php echo $WT_TREE->getNameUrl(); ?>';">
				</td>
			<?php } ?>
		</tr>
	</table>
	<br><a href="lifespan.php?ged=<?php echo $WT_TREE->getNameUrl(); ?>"><b><?php echo I18N::translate('Show lifespans'); ?></b></a>
</form>
<?php
if (count($controller->people) > 0) {
	?>
	<div id="timeline_chart">
		<!-- print the timeline line image -->
		<div id="line" style="position:absolute; <?php echo I18N::direction() === 'ltr' ? 'left: ' . ($basexoffset + 22) : "right: " . ($basexoffset + 22); ?>px; top: <?php echo $baseyoffset; ?>px;">
		<img src="<?php echo Theme::theme()->parameter('image-vline'); ?>" width="3" height="<?php echo $baseyoffset + ($controller->topyear - $controller->baseyear) * $controller->scale; ?>" alt="">
		</div>
		<!-- print divs for the grid -->
		<div id="scale<?php echo $controller->baseyear; ?>" style="position:absolute; <?php echo I18N::direction() === 'ltr' ? "left: $basexoffset" : "right: $basexoffset"; ?>px; top: <?php echo $baseyoffset - 5; ?>px; font-size: 7pt; text-align: <?php echo I18N::direction() === 'ltr' ? 'left' : 'right'; ?>;">
			<?php echo $controller->baseyear . '—'; ?>
		</div>
		<?php
		// at a scale of 25 or higher, show every year
		$mod = 25 / $controller->scale;
		if ($mod < 1) {
			$mod = 1;
		}
		for ($i = $controller->baseyear + 1; $i < $controller->topyear; $i++) {
			if ($i % $mod === 0) {
				echo "<div id=\"scale$i\" style=\"position:absolute; " . (I18N::direction() === 'ltr' ? "left: $basexoffset" : "right: $basexoffset") . "px; top:" . ($baseyoffset + (($i - $controller->baseyear) * $controller->scale) - $controller->scale / 2) . "px; font-size: 7pt; text-align:" . (I18N::direction() === 'ltr' ? 'left' : 'right') . ";\">";
				echo $i . '—';
				echo '</div>';
			}
		}
		echo "<div id=\"scale{$controller->topyear}\" style=\"position:absolute; " . (I18N::direction() === 'ltr' ? "left: $basexoffset" : "right: $basexoffset") . "px; top:" . ($baseyoffset + (($controller->topyear - $controller->baseyear) * $controller->scale)) . "px; font-size: 7pt; text-align:" . (I18N::direction() === 'ltr' ? 'left' : 'right') . ";\">";
		echo $controller->topyear . '—';
		echo '</div>';
		Functions::sortFacts($controller->indifacts);
		$factcount = 0;
		foreach ($controller->indifacts as $fact) {
			$controller->printTimeFact($fact);
			$factcount++;
		}

		// print the age boxes
		foreach ($controller->people as $p => $indi) {
			$pid        = $indi->getXref();
			$ageyoffset = $baseyoffset + ($controller->bheight * $p);
			$col        = $p % 6;
			?>
			<div id="agebox<?php echo $p; ?>" style="cursor:move; position:absolute; <?php echo I18N::direction() === 'ltr' ? 'left: ' . ($basexoffset + 20) : 'right: ' . ($basexoffset + 20); ?>px; top:<?php echo $ageyoffset; ?>px; height:<?php echo $controller->bheight; ?>px; display:none;" onmousedown="ageCursorMouseDown(this, <?php echo $p; ?>);">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<img src="<?php echo Theme::theme()->parameter('image-hline'); ?>" name="ageline<?php echo $p; ?>" id="ageline<?php echo $p; ?>" align="left" width="25" height="3" alt="">
						</td>
						<td valign="top">
							<?php
							$tyear = round(($ageyoffset + ($controller->bheight / 2)) / $controller->scale) + $controller->baseyear;
							if (!empty($controller->birthyears[$pid])) {
								$tage = $tyear - $controller->birthyears[$pid];
								?>
								<table class="person<?php echo $col; ?>" style="cursor: hand;">
									<tr>
										<td valign="top" width="120"><?php echo I18N::translate('Year:'); ?>
											<span id="yearform<?php echo $p; ?>" class="field">
									<?php echo $tyear; ?>
									</span>
										</td>
										<td valign="top" width="130">(<?php echo I18N::translate('Age'); ?>
											<span id="ageform<?php echo $p; ?>" class="field"><?php echo $tage; ?></span>)
										</td>
									</tr>
								</table>
							<?php } ?>
						</td>
					</tr>
				</table>
				<br><br><br>
			</div>
			<br><br><br><br>
		<?php } ?>
		<script>
			var bottomy = <?php echo $baseyoffset + ($controller->topyear - $controller->baseyear) * $controller->scale; ?>-5;
			var topy = <?php echo $baseyoffset; ?>;
			var baseyear = <?php echo $controller->baseyear - (25 / $controller->scale); ?>;
			var birthyears = [];
			var birthmonths = [];
			var birthdays = [];
			<?php
			foreach ($controller->people as $c => $indi) {
				$pid = $indi->getXref();
				if (!empty($controller->birthyears[$pid])) {
					echo 'birthyears[' . $c . ']=' . $controller->birthyears[$pid] . ';';
				}
				if (!empty($controller->birthmonths[$pid])) {
					echo 'birthmonths[' . $c . ']=' . $controller->birthmonths[$pid] . ';';
				}
				if (!empty($controller->birthdays[$pid])) {
					echo 'birthdays[' . $c . ']=' . $controller->birthdays[$pid] . ';';
				}
			}
			?>

			var bheight = <?php echo $controller->bheight; ?>;
			var scale = <?php echo $controller->scale; ?>;
		</script>
	</div>
<?php } ?>
<script>
	timeline_chart_div = document.getElementById("timeline_chart");
	if (timeline_chart_div) {
		timeline_chart_div.style.height = '<?php echo $baseyoffset + ($controller->topyear - $controller->baseyear) * $controller->scale * 1.1; ?>px';
	}
</script>
