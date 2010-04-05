<?php
/**
 * Parses gedcom file and displays a pedigree tree.
 *
 * Specify a $rootid to root the pedigree tree at a certain person
 * with id = $rootid in the GEDCOM file.
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
 * This Page Is Valid XHTML 1.0 Transitional! > 22 August 2005
 *
 * $Id$
 * @package webtrees
 * @subpackage Charts
 */

define('WT_SCRIPT_NAME', 'pedigree.php');
require './includes/session.php';
require WT_ROOT.'includes/controllers/pedigree_ctrl.php';

$controller = new PedigreeController();
$controller->init();

// -- echo html header information
print_header($controller->getPageTitle());

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

// LightBox
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}

echo '<table><tr><td valign="middle">';
if ($controller->isPrintPreview()) {
	echo "<h2>", i18n::translate('%s Generation Pedigree Chart', $PEDIGREE_GENERATIONS), ":";
} else {
	echo "<h2>", i18n::translate('Pedigree Tree'), ":";
}
echo '<br />', PrintReady($controller->name);
if ($controller->addname!="") {
	echo '<br />', PrintReady($controller->addname);
}
echo '</h2>';
// -- echo the form to change the number of displayed generations
if (!$controller->isPrintPreview()) {
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
	</script>
	</td><td width="50px">&nbsp;</td><td><form name="people" id="people" method="get" action="?">
	<input type="hidden" name="show_full" value="<?php echo $controller->show_full; ?>" />
		<table class="list_table <?php echo $TEXT_DIRECTION; ?>" width="500" align="center">
			<tr>
				<td colspan="4" class="topbottombar" style="text-align:center; ">
					<?php echo i18n::translate('Options:'); ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Root Person ID'), help_link('rootid'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Generations'), help_link('PEDIGREE_GENERATIONS'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Orientation'), help_link('talloffset'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo i18n::translate('Show Details'), help_link('show_full'); ?>
				</td>
			</tr>

			<tr>
				<td class="optionbox">
					<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->rootid; ?>" />
					<?php print_findindi_link("rootid", ""); ?>
				</td>
				<td class="optionbox">
					<select name="PEDIGREE_GENERATIONS">
					<?php
						for ($i=3; $i<=$MAX_PEDIGREE_GENERATIONS; $i++) {
							echo "<option value=\"", $i, "\"" ;
							if ($i == $controller->PEDIGREE_GENERATIONS) echo " selected=\"selected\"";
							echo ">", $i, "</option>";
						}
					?>
					</select>
				</td>
				<td class="optionbox">
					<select name="talloffset">
					<?php
						echo '<option value="0"';
							if ($talloffset==0) echo ' selected="selected"';
							echo '>', i18n::translate('Portrait'), '</option>';
						echo '<option value="1"';
							if ($talloffset==1) echo ' selected="selected"';
							echo '>', i18n::translate('Landscape'), '</option>';
						echo '<option value="2"';
							if ($talloffset==2) echo ' selected="selected"';
							echo '>', i18n::translate('Oldest at top'), '</option>';
						echo '<option value="3"';
							if ($talloffset==3) echo ' selected="selected"';
							echo '>', i18n::translate('Oldest at bottom'), '</option>';
					?>
					</select>
				</td>
				<td class="optionbox">
					<input type="checkbox" value="<?php
					if ($controller->show_full) echo "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
					else echo "0\" onclick=\"document.people.show_full.value='1';";?>"/>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="4">
					<input type="submit" value="<?php echo i18n::translate('View'); ?>" />
				</td>
			</tr>
		</table>
	</form>
<?php
	if ($show_full==0) {
		echo '<span class="details2">', i18n::translate('Click on any of the boxes to get more information about that person.'), '</span><br />';
	}
} ?>
	</td></tr>
</table>
<div id="pedigree_chart<?php if ($TEXT_DIRECTION=="rtl") echo '_rtl'; ?>" <?php
	if ($controller->isPrintPreview()) echo ' style="top: 1px;"';
	else echo 'style="z-index: 1;"'; ?> >
<?php
//-- echo the boxes
$curgen = 1;
$xoffset = 0;
$yoffset = 0;			// -- used to offset the position of each box as it is generated
$prevxoffset = 0;		// -- used to track the x position of the previous box
$prevyoffset = 0;		// -- used to track the y position of the previous box
$maxyoffset = 0;
$linesize = 3;
if (!isset($brborder)) $brborder = 1;	// Avoid errors from old custom themes
for($i=($controller->treesize-1); $i>=0; $i--) {
	// -- check to see if we have moved to the next generation
	if ($i < floor($controller->treesize / (pow(2, $curgen)))) {
		$curgen++;
	}
	$prevxoffset = $xoffset;
	$prevyoffset = $yoffset;
	if ($talloffset < 2) {
		$xoffset = $controller->offsetarray[$i]["x"];
		$yoffset = $controller->offsetarray[$i]["y"];
	}
	else {
		$xoffset = $controller->offsetarray[$i]["y"];
		$yoffset = $controller->offsetarray[$i]["x"];
	}
	// -- if we are in the middle generations then we need to draw the connecting lines
	if (($curgen > 0 && $talloffset > 1) || (($curgen > $talloffset) && ($curgen < $controller->PEDIGREE_GENERATIONS))) {
		if ($i%2==1) {
			if ($SHOW_EMPTY_BOXES || ($controller->treeid[$i]) || ($controller->treeid[$i+1])) {
				if ($talloffset < 2) {
					$vlength = $prevyoffset-$yoffset;
				}
				else {
					$vlength = $prevxoffset-$xoffset;
				}
				if (!$SHOW_EMPTY_BOXES && (empty($controller->treeid[$i+1]))) {
					$parent = ceil(($i-1)/2);
					$vlength = $controller->offsetarray[$parent]["y"]-$yoffset;
				}
				$linexoffset = $xoffset;
				if ($talloffset < 2) {
					echo '<div id="line' .$i . '" dir="';
					if ($TEXT_DIRECTION=="rtl") echo 'rtl" style="position:absolute; right:';
					else echo 'ltr" style="position:absolute; left:';
					echo $linexoffset, 'px; top:', ($yoffset+1+$controller->pbheight/2), 'px; z-index: 0;">';
					echo '<img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["vline"]["other"], '" width="', $linesize, '" height="', ($vlength-1), '\" alt="" />';
					echo '</div>';
				}
				else {
					echo '<div id="vline$i" dir="';
					if ($TEXT_DIRECTION=="rtl") echo 'rtl" style="position:absolute; right:';
					else echo 'ltr" style="position:absolute; left:';
					if ($talloffset > 2) {
						echo ($linexoffset-2+$controller->pbwidth/2+$vlength/2), 'px; top:', ($yoffset+1-$controller->pbheight/2), 'px; z-index: 0;">';
						echo '<img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["vline"]["other"], '" width="', $linesize, '" height="', ($controller->pbheight), '" alt="" />';
					}
					else {
						echo ($linexoffset-2+$controller->pbwidth/2+$vlength/2), "px; top:", ($yoffset+1+$controller->pbheight/2), "px; z-index: 0;\">";
						echo '<img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["vline"]["other"], '" width="', $linesize, '" height="', ($controller->pbheight), '" alt="" />';
					}
					echo '</div>';
					echo '<div id="line$i" dir="';
					if ($TEXT_DIRECTION=="rtl") echo 'rtl" style="position:absolute; right:';
					else echo 'ltr" style="position:absolute; left:';
					echo ($linexoffset+$controller->pbwidth), 'px; top:', ($yoffset+1+$controller->pbheight/2), 'px; z-index: 0;\">';
					echo '<img src="', $WT_IMAGE_DIR, '/', $WT_IMAGES["hline"]["other"], '" width="', ($vlength-$controller->pbwidth), '" height="', $linesize, '" alt="" />';
					echo '</div>';
				}
			}
		}
	}
	// -- draw the box
	if (!empty($controller->treeid[$i]) || $SHOW_EMPTY_BOXES) {
		// Work around a bug in FireFox that mis-places some boxes in Portrait RTL, resulting in
		// vertical lines that themselves appear to be mis-placed.
		if ($TEXT_DIRECTION=="rtl" && $BROWSERTYPE=="mozilla" && ($curgen <= 2)) $xoffset += 10;
		if ($TEXT_DIRECTION=="rtl") $xoffset += $brborder;		// Account for thickness of right box border

		if ($yoffset>$maxyoffset) $maxyoffset=$yoffset;
		$widthadd = 0;
		if ($i==0) $iref = rand();
		else $iref = $i;

		if (($curgen==1)&&(!empty($controller->treeid[$i]))&&(count(find_family_ids($controller->treeid[$i]))>0)) $widthadd = 20;
		if (($curgen >2) && ($curgen < $controller->PEDIGREE_GENERATIONS)) $widthadd = 10;
		if ($talloffset == 2 && $view!="preview") {
			echo '<div id="uparrow" dir="';
			if ($TEXT_DIRECTION=="rtl") echo 'rtl" style="position:absolute; right:';
			else echo 'ltr" style="position:absolute; left:';
			echo ($xoffset+$controller->pbwidth/2-5), 'px; top:', ($yoffset-20), 'px; width:10px; height:10px; ">';
			if (($curgen==1)&&(count(find_family_ids($controller->treeid[$i]))>0)) {
				$did = 1;
				if ($i > ($controller->treesize/2) + ($controller->treesize/4)-1) $did++;
				echo '<a href="', encode_url("pedigree.php?PEDIGREE_GENERATIONS={$controller->PEDIGREE_GENERATIONS}&rootid={$controller->treeid[$did]}&show_full={$controller->show_full}&talloffset={$controller->talloffset}"), '" ';
				echo "onmouseover=\"swap_image('arrow$i', 2);\" onmouseout=\"swap_image('arrow$i', 2);\">";
				echo "<img id=\"arrow$i\" src=\"", $WT_IMAGE_DIR, '/', $WT_IMAGES["uarrow"]["other"], '" border="0" alt="" />';
				echo '</a>';
			}
			echo "\n\t\t</div>";
		}
		echo "\n\t\t<div id=\"box";
		if (empty($controller->treeid[$i])) echo "$iref";
		else echo $controller->treeid[$i];
		if ($TEXT_DIRECTION=="rtl") echo ".1.$iref\" style=\"position:absolute; right:";
		else echo ".1.$iref\" style=\"position:absolute; left:";

		if ($talloffset == 2) $zindex = $PEDIGREE_GENERATIONS-$curgen;
		else $zindex = 0;

		echo $xoffset, "px; top:", $yoffset, "px; width:", ($controller->pbwidth+$widthadd), "px; height:", $controller->pbheight, "px; ";
		echo "z-index: ", $zindex, ";\">";
		echo "\n\t\t\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" dir=\"$TEXT_DIRECTION\">";
		if (($talloffset < 2) && ($curgen > $talloffset) && ($curgen < $controller->PEDIGREE_GENERATIONS)) {
			echo "<tr><td>";
			echo "\n\t\t\t<img src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["hline"]["other"], "\" align=\"left\" hspace=\"0\" vspace=\"0\" alt=\"\" />";
			echo "\n\t\t\t</td><td width=\"100%\">";
		}
		else echo "<tr><td width=\"100%\">";
		if (!isset($controller->treeid[$i])) $controller->treeid[$i] = false;
		print_pedigree_person($controller->treeid[$i], 1, $controller->show_famlink, $iref, 1);
		if (($curgen==1) && (count(find_family_ids($controller->treeid[$i]))>0) && $view!="preview") {
			$did = 1;
			if ($i > ($controller->treesize/2) + ($controller->treesize/4)-1) $did++;
			if ($talloffset==3) {
				echo "\n\t\t\t\t</td></tr><tr><td align=\"center\">";
				echo "<a href=\"", encode_url("pedigree.php?PEDIGREE_GENERATIONS={$controller->PEDIGREE_GENERATIONS}&rootid={$controller->treeid[$did]}&show_full={$controller->show_full}&talloffset={$controller->talloffset}"), "\" ";
				echo "onmouseover=\"swap_image('arrow$i', 3);\" onmouseout=\"swap_image('arrow$i', 3);\">";
				echo "<img id=\"arrow$i\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["darrow"]["other"], "\" border=\"0\" alt=\"\" />";
			}
			else if ($talloffset < 2) {
				echo "\n\t\t\t\t</td><td valign=\"middle\">";
				echo "<a href=\"", encode_url("pedigree.php?PEDIGREE_GENERATIONS={$controller->PEDIGREE_GENERATIONS}&rootid={$controller->treeid[$did]}&show_full={$controller->show_full}&talloffset={$talloffset}"), "\" ";
				if ($TEXT_DIRECTION=="rtl") {
					echo "onmouseover=\"swap_image('arrow$i', 0);\" onmouseout=\"swap_image('arrow$i', 0);\">";
					echo "<img id=\"arrow$i\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["larrow"]["other"], "\" border=\"0\" alt=\"\" />";
				}
				else {
					echo "onmouseover=\"swap_image('arrow$i', 1);\" onmouseout=\"swap_image('arrow$i', 1);\">";
					echo "<img id=\"arrow$i\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["rarrow"]["other"], "\" border=\"0\" alt=\"\" />";
				}
				echo "</a>";
			}
		}
		echo "\n\t\t\t</td></tr></table>\n\t\t</div>";
	}
}

if ($controller->rootPerson->canDisplayDetails()) {
	// -- echo left arrow for decendants so that we can move down the tree
	$yoffset += ($controller->pbheight / 2)-10;
	$famids = $controller->rootPerson->getSpouseFamilies();
	//-- make sure there is more than 1 child in the family with parents
	$cfamids = $controller->rootPerson->getChildFamilies();
	if (count($famids)>0) {
		echo "<div id=\"childarrow\" dir=\"";
		if ($TEXT_DIRECTION=="rtl") echo "rtl\" style=\"position:absolute; right:";
		else echo "ltr\" style=\"position:absolute; left:";
		if ($talloffset == 0) {
			if ($PEDIGREE_GENERATIONS<6) {
				$addxoffset = 60*(5-$PEDIGREE_GENERATIONS);
			}
			else {
				$addxoffset = 0;
			}
			echo $addxoffset, "px; top:", $yoffset, "px; width:10px; height:10px; \">";
		}
		else if ($talloffset == 1) {
			if ($PEDIGREE_GENERATIONS<4)	$basexoffset += 60;
			echo $basexoffset, "px; top:", $yoffset, "px; width:10px; height:10px; \">";
		}
		else if ($talloffset==3) {
			echo ($linexoffset-10+$controller->pbwidth/2+$vlength/2), "px; top:", ($yoffset-$controller->pbheight/2-10), "px; width:10px; height:10px; \">";
		}
		else {
			echo ($linexoffset-10+$controller->pbwidth/2+$vlength/2), "px; top:", ($yoffset+$controller->pbheight/2+10), "px; width:10px; height:10px; \">";
		}
		if ($view!="preview") {
			if ($talloffset < 2) {
				if ($TEXT_DIRECTION=="rtl") echo "<a href=\"javascript: ", i18n::translate('Show'), "\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow', 1);\" onmouseout=\"swap_image('larrow', 1);\">";
				else echo "<a href=\"javascript: ", i18n::translate('Show'), "\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('larrow', 0);\" onmouseout=\"swap_image('larrow', 0);\">";
				if ($TEXT_DIRECTION=="rtl") echo "<img id=\"larrow\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["rarrow"]["other"], "\" border=\"0\" alt=\"\" />";
				else echo "<img id=\"larrow\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["larrow"]["other"], "\" border=\"0\" alt=\"\" />";
			}
			else if ($talloffset==3) {
				echo "<a href=\"javascript: ", i18n::translate('Show'), "\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('uarrow', 2);\" onmouseout=\"swap_image('uarrow', 2);\">";
				echo "<img id=\"uarrow\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["uarrow"]["other"], "\" border=\"0\" alt=\"\" />";
			}
			else {
				echo "<a href=\"javascript: ", i18n::translate('Show'), "\" onclick=\"togglechildrenbox(); return false;\" onmouseover=\"swap_image('darrow', 3);\" onmouseout=\"swap_image('darrow', 3);\">";
				echo "<img id=\"darrow\" src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["darrow"]["other"], "\" border=\"0\" alt=\"\" />";
			}
			echo "</a>";
		}
		echo "\n\t\t</div>";
		$yoffset += ($controller->pbheight / 2)+10;
		echo "\n\t\t<div id=\"childbox\" dir=\"";
		if ($TEXT_DIRECTION=="rtl") echo "rtl\" style=\"position:absolute; right:";
		else echo "ltr\" style=\"position:absolute; left:";
		echo $xoffset, "px; top:", $yoffset, "px; width:", $controller->pbwidth, "px; height:", $controller->pbheight, "px; visibility: hidden;\">";
		echo "\n\t\t\t<table class=\"person_box\"><tr><td>";
		foreach($famids as $ind=>$family) {
			if ($family!=null) {
				$husb = $family->getHusbId();
				$wife = $family->getWifeId();
				if($controller->rootid!=$husb) $spid=$family->getHusband();
				else $spid=$family->getWife();
				if (!empty($spid)) {
					echo "\n\t\t\t\t<a href=\"", encode_url("pedigree.php?PEDIGREE_GENERATIONS={$controller->PEDIGREE_GENERATIONS}&rootid=".$spid->getXref()."&show_full={$controller->show_full}&talloffset={$talloffset}"), "\"><span ";
					if ($spid->canDisplayName()) {
						$name = $spid->getFullName();
						$name = rtrim($name);
					} else $name = i18n::translate('Private');
					if (hasRTLText($name)) echo 'class="name2">';
						else echo 'class="name1">';
					echo PrintReady($name);
					echo '<br /></span></a>';
				}

				$children = $family->getChildren();
				foreach($children as $ind2=>$child) {
					echo "\n\t\t\t\t&nbsp;&nbsp;<a href=\"", encode_url("pedigree.php?PEDIGREE_GENERATIONS={$controller->PEDIGREE_GENERATIONS}&rootid=".$child->getXref()."&show_full={$controller->show_full}&talloffset={$talloffset}"), "\"><span ";
					if ($child->canDisplayName()) {
						$name = $child->getFullName();
						$name = rtrim($name);
					} else $name = i18n::translate('Private');
					if (hasRTLText($name)) echo "class=\"name2\">&lt; ";
						else echo "class=\"name1\">&lt; ";
					echo PrintReady($name);
					echo '<br /></span></a>';
				}
			}
		}
		//-- echo the siblings
		foreach($cfamids as $ind=>$family) {
			if ($family!=null) {
				$children = $family->getChildren();
				if (count($children)>2) echo '<span class="name1"><br />', i18n::translate('Siblings'), '<br /></span>';
				if (count($children)==2) echo '<span class="name1"><br />', i18n::translate('Sibling'), '<br /></span>';
				foreach($children as $ind2=>$child) {
					if (!$controller->rootPerson->equals($child) && !is_null($child)) {
						echo "\n\t\t\t\t&nbsp;&nbsp;<a href=\"", encode_url("pedigree.php?PEDIGREE_GENERATIONS={$controller->PEDIGREE_GENERATIONS}&rootid=".$child->getXref()."&show_full={$controller->show_full}&talloffset={$talloffset}"), "\"><span ";
						if ($child->canDisplayName()) {
							$name = $child->getFullName();
							$name = rtrim($name);
						} else $name = i18n::translate('Private');
						if (hasRTLText($name)) echo 'class="name2"> ';
							else echo 'class="name1"> ';
						echo PrintReady($name);
						echo '<br /></span></a>';
					}
				}
			}
		}
		echo "\n\t\t\t</td></tr></table>";
		echo "\n\t\t</div>";
	}
}
// -- print html footer
$maxyoffset+=30;
?>
</div>
<script language="JavaScript" type="text/javascript">
	content_div = document.getElementById("content");
	if (content_div) {
		content_div.style.height = <?php echo $maxyoffset; ?> + "px";
	}
	<?php if ($view=="preview") { ?>
	pedigree_div = document.getElementById("pedigree_chart");
	if (pedigree_div) {
		pedigree_div.style.height = <?php echo $maxyoffset; ?> + "px";
	}
	<?php } ?>
</script>
<?php
if ($controller->isPrintPreview()) echo '<br /><br /><br />';
print_footer();
?>
