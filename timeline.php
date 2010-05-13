<?php
/**
 * Display a timeline chart for a group of individuals
 *
 * Use the $pids array to set which individuals to show on the chart
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * This Page Is Valid XHTML 1.0 Transitional! > 08 August 2005
 *
 * @package webtrees
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'timeline.php');
require './includes/session.php';
require_once WT_ROOT.'includes/controllers/timeline_ctrl.php';

$controller = new TimelineController();
$controller->init();

print_header(i18n::translate('Timeline'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
?>
<script language="JavaScript" type="text/javascript">
<!--
function showhide(divbox, checkbox) {
	if (checkbox.checked) {
		MM_showHideLayers(divbox, ' ', 'show', ' ');
	}
	else {
		MM_showHideLayers(divbox, ' ', 'hide', ' ');
	}
}

var pastefield = null;
function paste_id(value) {
	pastefield.value=value;
}

var N = (document.all) ? 0 : 1;
var ob=null;
var Y=0;
var X=0;
var oldx=0;
var personnum=0;
var type=0;
var state=0;
var oldstate=0;
var boxmean = 0;

function ageMD(divbox, num) {
	ob=divbox;
	personnum=num;
	type=0;
	if (N) {
		X=ob.offsetLeft;
		Y=ob.offsetTop;
	}
	else {
		X=ob.offsetLeft;
		Y=ob.offsetTop;
		oldx = event.clientX + document.documentElement.scrollLeft;
	}
}

function factMD(divbox, num, mean) {
	if (ob!=null) return;
	ob=divbox;
	personnum=num;
	boxmean = mean;
	type=1;
	if (N) {
		oldx=ob.offsetLeft;
		oldlinew=0;
	}
	else {
		oldx = ob.offsetLeft;
		oldlinew = event.clientX + document.documentElement.scrollLeft;
	}
}

function MM(e) {
	if (ob) {
		tldiv = document.getElementById("timeline_chart");
		if (!tldiv) tldiv = document.getElementById("timeline_chart_rtl");
		if (type==0) {
			// age boxes
			newy = 0;
			newx = 0;
			if (N) {
				newy = e.pageY - tldiv.offsetTop;
				newx = e.pageX - tldiv.offsetLeft;
				if (oldx==0) oldx=newx;
			}
			else {
				newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
				newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
			}
			if ((newy >= topy-bheight/2)&&(newy<=bottomy)) newy = newy;
			else if (newy < topy-bheight/2) newy = topy-bheight/2;
			else newy = (bottomy-1);
			ob.style.top = newy+"px";
			tyear = ((newy+bheight-4 - topy) + scale)/scale + baseyear
			year = Math.floor(tyear);
			month = Math.floor((tyear*12)-(year*12));
			day = Math.floor((tyear*365)-(year*365 + month*30));
			mstamp = (year*365)+(month*30)+day;
			bdstamp = (birthyears[personnum]*365)+(birthmonths[personnum]*30)+birthdays[personnum];
			daydiff = mstamp - bdstamp;
			ba = 1;
			if (daydiff < 0 ) {
				ba = -1;
				daydiff = (bdstamp - mstamp);
			}
			yage = Math.floor(daydiff / 365);
			mage = Math.floor((daydiff-(yage*365))/30);
			dage = Math.floor(daydiff-(yage*365)-(mage*30));
			if (dage<0) mage = mage -1;
			if (dage<-30) {
				dage = 30+dage;
			}
			if (mage<0) yage = yage-1;
			if (mage<-11) {
				mage = 12+mage;
			}
			yearform = document.getElementById('yearform'+personnum);
			ageform = document.getElementById('ageform'+personnum);
			yearform.innerHTML = year+"      "+month+" <?php print utf8_substr(i18n::translate('Month:'), 0, 1);?>   "+day+" <?php print utf8_substr(i18n::translate('Day:'), 0, 1);?>";
			if (ba*yage>1 || ba*yage<-1 || ba*yage==0)
				 ageform.innerHTML = (ba*yage)+" <?php print utf8_substr(i18n::translate('years'), 0, 1);?>   "+(ba*mage)+" <?php print utf8_substr(i18n::translate('Month:'), 0, 1);?>   "+(ba*dage)+" <?php print utf8_substr(i18n::translate('Day:'), 0, 1);?>";
			else ageform.innerHTML = (ba*yage)+" <?php print utf8_substr(i18n::translate('Year:'), 0, 1);?>   "+(ba*mage)+" <?php print utf8_substr(i18n::translate('Month:'), 0, 1);?>   "+(ba*dage)+" <?php print utf8_substr(i18n::translate('Day:'), 0, 1);?>";
			var line = document.getElementById('ageline'+personnum);
			temp = newx-oldx;
			if (textDirection=='rtl') temp = temp * -1;
			line.style.width=(line.width+temp)+"px";
			oldx=newx;
			return false;
		}
		else {
			newy = 0;
			newx = 0;
			if (N) {
				newy = e.pageY - tldiv.offsetTop;
				newx = e.pageX - tldiv.offsetLeft;
				if (oldx==0) oldx=newx;
				linewidth = e.pageX;
			}
			else {
				newy = event.clientY + document.documentElement.scrollTop - tldiv.offsetTop;
				newx = event.clientX + document.documentElement.scrollLeft - tldiv.offsetLeft;
				linewidth = event.clientX + document.documentElement.scrollLeft;
			}
			// get diagnal line box
			dbox = document.getElementById('dbox'+personnum);
			// set up limits
			if (boxmean-175 < topy) etopy = topy;
			else etopy = boxmean-175;
			if (boxmean+175 > bottomy) ebottomy = bottomy;
			else ebottomy = boxmean+175;
			// check if in the bounds of the limits
			if ((newy >= etopy)&&(newy<=ebottomy)) newy = newy;
			else if (newy < etopy) newy = etopy;
			else if (newy >ebottomy) newy = ebottomy;
			// calculate the change in Y position
			dy = newy-ob.offsetTop;
			// check if we are above the starting point and switch the background image
			if (newy < boxmean) {
				if (textDirection=='ltr') {
					dbox.style.backgroundImage = "url('<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["dline"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 100%";
				}
				else {
					dbox.style.backgroundImage = "url('<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["dline2"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 0%";
				}
				dy = (-1)*dy;
				state=1;
				dbox.style.top = (newy+bheight/3)+"px";
			}
			else {
				if (textDirection=='ltr') {
					dbox.style.backgroundImage = "url('<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["dline2"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 0%";
				}
				else {
					dbox.style.backgroundImage = "url('<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["dline"]["other"]; ?>')";
					dbox.style.backgroundPosition = "0% 100%";
				}

				dbox.style.top = (boxmean+(bheight/3))+"px";
				state=0;
			}
			// the new X posistion moves the same as the y position
			if (textDirection=='ltr') newx = dbox.offsetLeft+Math.abs(newy-boxmean);
			else newx = dbox.offsetRight+Math.abs(newy-boxmean);
			// set the X position of the box
			if (textDirection=='ltr') ob.style.left=newx+"px";
			else ob.style.right=newx+"px";
			// set new top positions
			ob.style.top = newy+"px";
			// get the width for the diagnal box
			newwidth = (ob.offsetLeft-dbox.offsetLeft);
			// set the width
			dbox.style.width=newwidth+"px";
			if (textDirection=='rtl') dbox.style.right = (dbox.offsetRight - newwidth) + 'px';
			dbox.style.height=newwidth+"px";
			// change the line width to the change in the mouse X position
			line = document.getElementById('boxline'+personnum);
			if (oldlinew!=0) line.width=line.width+(linewidth-oldlinew);
			oldlinew = linewidth;
			oldx=newx;
			oldstate=state;
			return false;
		}
	}
}

function MU() {
	ob = null;
	oldx=0;
}

if (N) {
	document.captureEvents(Event.MOUSEDOWN | Event.MOUSEMOVE | Event.MOUSEUP);
	//document.onmousedown = MD;
}
document.onmousemove = MM;
document.onmouseup = MU;
//-->
</script>
<h2><?php echo i18n::translate('Timeline chart'); ?></h2>
<?php if (!$controller->isPrintPreview()) { ?><form name="people" action="timeline.php"><?php } ?>
<?php
$controller->checkPrivacy();
?>
<table class="<?php echo $TEXT_DIRECTION; ?>">
	<tr>
	<?php
	$i=0;
	$count = count($controller->people);
	$half = $count;
	if ($count>5) {
		$half = ceil($count/2);
	}
	if (!$controller->isPrintPreview()) {
		$half++;
	}
	foreach($controller->people as $p=>$indi) {
		$pid = $indi->getXref();
		$col = $p % 6;
		if ($i==$half) {
			echo "</tr><tr>";
		}
		$i++;
		?>
		<td class="person<?php echo $col; ?>" style="padding: 5px;">
		<?php
		if ($indi && $indi->canDisplayDetails()) {
			if ($indi->getSex()=="M") {
				echo $indi->getSexImage('large', '', i18n::translate('Male'));
			} elseif ($indi->getSex()=="F") {
				echo $indi->getSexImage('large', '', i18n::translate('Female'));
			} else {
				echo $indi->getSexImage('large', '', i18n::translate('unknown'));
			}
		?>
 			<a href="individual.php?pid=<?php echo $pid; ?>">&nbsp;<?php echo PrintReady($indi->getFullName()); ?><br /><br />
 			<?php $addname = $indi->getAddName(); if (strlen($addname) > 0) echo PrintReady($addname); ?>
			</a>
			<input type="hidden" name="pids[<?php echo $p; ?>]" value="<?php echo htmlentities($pid, ENT_COMPAT, 'UTF-8'); ?>" />
			<?php if (!$controller->isPrintPreview()) {
				?>
				<a href="timeline.php?<?php echo $controller->pidlinks; ?>&amp;scale=<?php echo $controller->scale; ?>&amp;remove=<?php echo $pid;?>" >
				<span class="details1"><?php echo i18n::translate('Remove Person'), help_link('remove_person'); ?></span></a>
			<?php if (!empty($controller->birthyears[$pid])) { ?>
				<span class="details1"><br />
				<?php echo i18n::translate('Show Age Marker'), help_link('show_age_marker'); ?>
				<input type="checkbox" name="agebar<?php echo $p; ?>" value="ON" onclick="showhide('agebox<?php echo $p; ?>', this);" />
				</span>
			<?php }
			} ?>
			<br />
		<?php
		} else {
			print_privacy_error();
			?>
			<input type="hidden" name="pids[<?php echo $p; ?>]" value="<?php echo htmlentities($pid, ENT_COMPAT, 'UTF-8'); ?>" />
			<?php if (!$controller->isPrintPreview()) {
				echo "<br />";
				?>
				<a href="timeline.php?<?php echo $controller->pidlinks; ?>&amp;scale=<?php echo $controller->scale; ?>&amp;remove=<?php echo $pid;?>" >
				<span class="details1"><?php echo i18n::translate('Remove Person'), help_link('remove_person'); ?></span></a>
			<?php } ?>
			<br />
		<?php } ?>
		</td>
	<?php }
	if (!$controller->isPrintPreview()) {
		if (!isset($col)) $col = 0;
		?>
		<td class="person<?php print $col; ?>" style="padding: 5px" valign="top">
			<?php echo i18n::translate('Add another person to chart:<br />Person ID:'), help_link('add_person');?>&nbsp;
			<input class="pedigree_form" type="text" size="5" id="newpid" name="newpid" />&nbsp;
			<?php print_findindi_link("newpid",""); ?>
			<br />
			<br />
			<div style="text-align: center"><input type="submit" value="<?php print i18n::translate('Show'); ?>" /></div>
		</td>
	<?php }
	if ((count($controller->people)>0)&&(!$controller->isPrintPreview())) {
		$scalemod = round($controller->scale*.2) + 1;
		?>
		<td class="list_value" style="padding: 5px">
			<a href="<?php echo WT_SCRIPT_NAME."?".$controller->pidlinks."scale=".($controller->scale+$scalemod); ?>"><img src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES['zoomin']['other']; ?>" title="<?php print i18n::translate('Zoom in'); ?>" alt="<?php print i18n::translate('Zoom in'); ?>" border="0" /></a><br />
			<a href="<?php echo WT_SCRIPT_NAME."?".$controller->pidlinks."scale=".($controller->scale-$scalemod); ?>"><img src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES['zoomout']['other']; ?>" title="<?php print i18n::translate('Zoom out'); ?>" alt="<?php print i18n::translate('Zoom out'); ?>" border="0" /></a><br />
			<input type="button" value="<?php print i18n::translate('Clear Chart'); ?>" onclick="window.location = 'timeline.php?clear=1';" />
		</td>
	<?php } ?>
	</tr>
</table>
<?php if (!$controller->isPrintPreview()) { ?>
<br /><a href="lifespan.php"><b><?php print i18n::translate('Show Lifespan chart'); ?></b></a>
</form><?php } ?>
<?php
if (count($controller->people)>0) {
	?>
	<?php if ($controller->isPrintPreview()) print "\n\t".i18n::translate('In most recent browsers you can click and drag the boxes around on the chart.')."<br /><br />"; ?>
<div id="timeline_chart">
	<!-- print the timeline line image -->
	<div id="line" style="position:absolute; <?php print $TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+22):"right: ".($basexoffset+22); ?>px; top: <?php print $baseyoffset; ?>px; ">
		<img src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["vline"]["other"]; ?>" width="3" height="<?php print ($baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale)); ?>" alt="" />
	</div>
	<!-- print divs for the grid -->
	<div id="scale<?php print $controller->baseyear; ?>" style="font-family: Arial; position:absolute; <?php print ($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset"); ?>px; top: <?php print ($baseyoffset-5); ?>px; font-size: 7pt; text-align: <?php print ($TEXT_DIRECTION =="ltr"?"left":"right"); ?>;">
	<?php print $controller->baseyear."--"; ?>
	</div>
	<?php
	//-- at a scale of 25 or higher, show every year
	$mod = 25/$controller->scale;
	if ($mod<1) $mod = 1;
	for($i=$controller->baseyear+1; $i<$controller->topyear; $i++) {
		if ($i % $mod == 0)  {
			print "\n\t\t<div id=\"scale$i\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".floor($baseyoffset+(($i-$controller->baseyear)*$controller->scale)-$controller->scale/2)."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
			print $i."--";
			print "</div>";
		}
	}
	print "\n\t\t<div id=\"scale{$controller->topyear}\" style=\"font-family: Arial; position:absolute; ".($TEXT_DIRECTION =="ltr"?"left: $basexoffset":"right: $basexoffset")."px; top:".floor($baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale))."px; font-size: 7pt; text-align:".($TEXT_DIRECTION =="ltr"?"left":"right").";\">\n";
	print $controller->topyear."--";
	print "</div>";
	sort_facts($controller->indifacts);
	$factcount=0;
	foreach($controller->indifacts as $indexval => $fact) {
		$controller->print_time_fact($fact);
		$factcount++;
	}

	// print the age boxes
	foreach($controller->people as $p=>$indi) {
		$pid = $indi->getXref();
		$ageyoffset = $baseyoffset + ($controller->bheight*$p);
		$col = $p % 6;
		?>
		<div id="agebox<?php print $p; ?>" style="position:absolute; <?php print ($TEXT_DIRECTION =="ltr"?"left: ".($basexoffset+20):"right: ".($basexoffset+20)); ?>px; top:<?php print $ageyoffset; ?>px; height:<?php print $controller->bheight; ?>px; visibility: hidden;" onmousedown="ageMD(this, <?php print $p; ?>);">
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<img src="<?php print $WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]; ?>" name="ageline<?php print $p; ?>" id="ageline<?php print $p; ?>" align="left" hspace="0" vspace="0" width="25" height="3" alt="" />
					</td>
					<td valign="top">
						<?php
						$tyear = round(($ageyoffset+($controller->bheight/2))/$controller->scale)+$controller->baseyear;
						if (!empty($controller->birthyears[$pid])) {
						$tage = $tyear-$controller->birthyears[$pid];
						?>
						<table class="person<?php print $col; ?>" style="cursor: hand;">
							<tr>
								<td valign="top" width="120"><?php print i18n::translate('Year:'); ?>
									<span id="yearform<?php print $p; ?>" class="field">
									<?php print $tyear; ?>
									</span>
								</td>
								<td valign="top" width="130">(<?php print i18n::translate('Age');?>
									<span id="ageform<?php print $p; ?>" class="field"><?php print $tage; ?></span>)
								</td>
							</tr>
						</table>
						<?php } ?>
					</td>
				</tr>
			</table><br /><br /><br />
		</div><br /><br /><br /><br />
	<?php } ?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var bottomy = <?php print ($baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale)); ?>-5;
	var topy = <?php print $baseyoffset;?>;
	var baseyear = <?php print $controller->baseyear-(25/$controller->scale); ?>;
	var birthyears = new Array();
	var birthmonths = new Array();
	var birthdays = new Array();
	<?php
	foreach($controller->people as $c=>$indi) {
		$pid = $indi->getXref();
		if (!empty($controller->birthyears[$pid])) print "\nbirthyears[".$c."]=".$controller->birthyears[$pid].";";
		if (!empty($controller->birthmonths[$pid])) print "\nbirthmonths[".$c."]=".$controller->birthmonths[$pid].";";
		if (!empty($controller->birthdays[$pid])) print "\nbirthdays[".$c."]=".$controller->birthdays[$pid].";";
	}
	?>

	var bheight=<?php print $controller->bheight;?>;
	var scale=<?php print $controller->scale;?>;
	//-->
	</script>
</div>
<?php } ?>
<script language="JavaScript" type="text/javascript">
<!--
	timeline_chart_div = document.getElementById("timeline_chart");
	if (!timeline_chart_div) timeline_chart_div = document.getElementById("timeline_chart_rtl");
	if (timeline_chart_div) timeline_chart_div.style.height = '<?php print $baseyoffset+(($controller->topyear-$controller->baseyear)*$controller->scale*1.1); ?>px';
//-->
</script>
<?php
print_footer();
?>
