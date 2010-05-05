<?php
/**
 * Calculates the relationship between two individuals in the gedcom
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
 * This Page Is Valid XHTML 1.0 Transitional! > 20 August 2005
 *
 * @package webtrees
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'relationship.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';
require_once WT_ROOT.'includes/classes/class_person.php';

$show_full=$PEDIGREE_FULL_DETAILS;
if (isset($_REQUEST['show_full'])) $show_full = $_REQUEST['show_full'];
if (!isset($_REQUEST['path_to_find'])) {
	$path_to_find = 0;
	$pretty = 1;
	unset($_SESSION["relationships"]);
}
else $path_to_find = $_REQUEST['path_to_find'];
if ($path_to_find == -1) {
	$path_to_find = 0;
	unset($_SESSION["relationships"]);
}

//-- previously these variables were set in theme.php, now they are no longer required to be set there
$Dbasexoffset = 0;
$Dbaseyoffset = 0;

if ($show_full==false) {
	$Dbheight=25;
	$Dbwidth-=40;
}

$bwidth = $Dbwidth;
$bheight = $Dbheight;

$title_string = "";

$pid1=safe_GET_xref('pid1');
$pid2=safe_GET_xref('pid2');

if (!isset($_REQUEST['followspouse'])) $followspouse = 0;
else $followspouse = $_REQUEST['followspouse'];
if (!isset($_REQUEST['pretty'])) $pretty = 0;
else $pretty = $_REQUEST['pretty'];
if (!isset($_REQUEST['asc'])) $asc=1;
else $asc = $_REQUEST['asc'];
if ($asc=="") $asc=1;
if (empty($pid1)) {
	$followspouse = 1;
	$pretty = 1;
}
$check_node = true;
$disp = true;

$title_string .= i18n::translate('Relationship Chart');
// -- print html header information
print_header($title_string);

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

// Lbox additions if installed ---------------------------------------------------------------------------------------------
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require_once WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// ------------------------------------------------------------------------------------------------------------------------------

if ($pid1) {
	//-- check if the id is valid
	$indirec = Person::getInstance($pid1);
	// Allow entry of i123 instead of I123
	if (!$indirec && $pid1!=strtoupper($pid1)) {
		$pid1=strtoupper($pid1);
		$indirec=Person::getInstance($pid1);
	}
	// Allow user to specify person without the prefix
	if (!$indirec && $GEDCOM_ID_PREFIX) {
		$pid1=$GEDCOM_ID_PREFIX.$pid1;
		$indirec=Person::getInstance($pid1);
	}
	if ($indirec) {
		$title_string.=':<br />'.$indirec->getFullName();
	} else {
		$pid1='';
	}
	if (!empty($_SESSION["pid1"]) && ($_SESSION["pid1"]!=$pid1)) {
		unset($_SESSION["relationships"]);
		$path_to_find=0;
	}
}
if ($pid2) {
	//-- check if the id is valid
	$indirec = Person::getInstance($pid2);
	// Allow entry of i123 instead of I123
	if (!$indirec && $pid2!=strtoupper($pid2)) {
		$pid1=strtoupper($pid2);
		$indirec=Person::getInstance($pid2);
	}
	// Allow user to specify person without the prefix
	if (!$indirec && $GEDCOM_ID_PREFIX) {
		$pid2=$GEDCOM_ID_PREFIX.$pid2;
		$indirec = Person::getInstance($pid2);
	}
	if ($indirec) {
		$title_string.=' '.i18n::translate('and').' '.$indirec->getFullName();
	} else {
		$pid2='';
	}
	if (!empty($_SESSION["pid2"]) && ($_SESSION["pid2"]!=$pid2)) {
		unset($_SESSION["relationships"]);
		$path_to_find=0;
	}
}
?>
<script language="JavaScript" type="text/javascript">
var pastefield;
function paste_id(value) {
	pastefield.value=value;
}
</script>
<div id="relationship_chart_options<?php print ($TEXT_DIRECTION=="ltr")?"":"_rtl";?>" style="position: relative; z-index:90; width:98%;">
<h2><?php print PrintReady($title_string);?></h2><br />
<!-- // Print the form to change the number of displayed generations -->
<?php
if ($view!="preview") {
	$Dbaseyoffset += 110; ?>
	<form name="people" method="get" action="relationship.php">
	<input type="hidden" name="path_to_find" value="<?php print $path_to_find ?>" />

	<table class="list_table <?php print $TEXT_DIRECTION ?>" style="align:<?php print ($TEXT_DIRECTION=="ltr"?"left":"right");?>; margin:0;">

	<!-- // Relationship header -->
	<tr><td colspan="2" class="topbottombar center">
	<?php echo i18n::translate('Relationship Chart')?>
	</td>

	<!-- // Empty space -->
	<td>&nbsp;</td>

	<!-- // Options header -->
	<td colspan="2" class="topbottombar center">
	<?php echo i18n::translate('Options:')?>
	</td></tr>

	<!-- // Person 1 -->
	<tr><td class="descriptionbox">
	<?php echo i18n::translate('Person 1'), help_link('relationship_id'); ?>
	</td>
	<td class="optionbox vmiddle">
	<input tabindex="1" class="pedigree_form" type="text" name="pid1" id="pid1" size="3" value="<?php print $pid1 ?>" />
	<?php
	print_findindi_link("pid1","");?>
	</td>

	<!-- // Empty space -->
	<td></td>

	<!-- // Show details -->
	<td class="descriptionbox">
	<?php echo i18n::translate('Show Details'), help_link('show_full'); ?>
	</td>
	<td class="optionbox vmiddle">
	<input type="hidden" name="show_full" value="<?php print $show_full ?>" />
		<?php
	if (!$pretty && $asc==-1) print "<input type=\"hidden\" name=\"asc\" value=\"$asc\" />";
	print "<input tabindex=\"3\" type=\"checkbox\" name=\"showfull\" value=\"0\"";
	if ($show_full) print " checked=\"checked\"";
	print " onclick=\"document.people.show_full.value='".(!$show_full)."';\" />";?>
	</td></tr>

	<!-- // Person 2 -->
	<tr><td class="descriptionbox">
	<?php echo i18n::translate('Person 2'), help_link('relationship_id'); ?>
	</td>
	<td class="optionbox vmiddle">
	<input tabindex="2" class="pedigree_form" type="text" name="pid2" id="pid2" size="3" value="<?php print $pid2 ?>" />
		<?php
		print_findindi_link("pid2","");?>
	</td>

	<!-- // Empty space -->
	<td>&nbsp;</td>

	<!-- // Line up generations -->
	<td class="descriptionbox">
	<?php
	echo i18n::translate('Line up the same generations'), help_link('line_up_generations'); ?>
	</td>
	<td class="optionbox">
	<input tabindex="5" type="checkbox" name="pretty" value="2"
	<?php
	if ($pretty) print " checked=\"checked\"";
	print " onclick=\"expand_layer('oldtop1'); expand_layer('oldtop2');\"" ?> />
	</td></tr>

	<!-- // Empty line -->
	<tr><td class="descriptionbox">&nbsp;</td>
	<td class="optionbox">&nbsp;</td>

	<!-- // Empty space -->
	<td>&nbsp;</td>

	<!-- // Show oldest top -->
	<td class="descriptionbox">
	<div id="oldtop1" style="display:
	<?php
	if ($pretty) print "block";
	else print "none";
	?>">
	<?php echo i18n::translate('Show oldest top'), help_link('oldest_top'); ?>
	</div>
	</td><td class="optionbox">
	<div id="oldtop2" style="display:
	<?php
	if ($pretty) print "block";
	else print "none";?>">
	<input tabindex="4" type="checkbox" name="asc" value="-1"
	<?php
	if ($asc==-1) print " checked=\"checked\"";?>
	/>
	</div></td></tr>

	<!-- // Show path -->
	<tr><td class="descriptionbox">
	<?php $pass = false;
	if ((isset($_SESSION["relationships"]))&&((!empty($pid1))&&(!empty($pid2)))) {
		$pass = true;
		$i=0;
		$new_path=true;
		if (isset($_SESSION["relationships"][$path_to_find])) $node = $_SESSION["relationships"][$path_to_find];
		else $node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
		if (!$node) {
			$path_to_find--;
			$check_node=$node;
		}
		foreach($_SESSION["relationships"] as $indexval => $node) {
			if ($i==0) print i18n::translate('Show path').": </td><td class=\"list_value\" style=\"padding: 3px;\">";
			if ($i>0) print " | ";
			if ($i==$path_to_find){
				print "<span class=\"error\" style=\"valign: middle\">".($i+1)."</span>";
				$new_path=false;
			}
			else {
				print "<a href=\"".encode_url("relationship.php?pid1={$pid1}&pid2={$pid2}&path_to_find={$i}&followspouse={$followspouse}&pretty={$pretty}&show_full={$show_full}&asc={$asc}")."\">".($i+1)."</a>\n";
			}
			$i++;
		}
		if (($new_path)&&($path_to_find<$i+1)&&($check_node)) print " | <span class=\"error\">".($i+1)."</span>";
		print "</td>";
	} else {
		if ((!empty($pid1))&&(!empty($pid2))) {
			if ((!displayDetailsById($pid1))&&(!showLivingNameById($pid1))) {
				$disp = false;
			} elseif ((!displayDetailsById($pid2))&&(!showLivingNameById($pid2))) {
				$disp = false;
			}
			if ($disp) {
				echo i18n::translate('Show path'), ": </td>";
				echo "\n\t\t<td class=\"optionbox\">";
				echo " <span class=\"error vmmiddle\">";
				$check_node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
				echo $check_node ? "1" : "&nbsp;".i18n::translate('No results found.'), "</span></td>";
				$prt = true;
			}
		}
		if (!isset($prt)) {
			echo "&nbsp;</td><td class=\"optionbox\">&nbsp;</td>";
		}
	}
?>
	<!-- // Empty space -->
	<td></td>

	<!-- // Check relationships by marriage -->
	<td class="descriptionbox">
	<?php echo i18n::translate('Check relationships by marriage'), help_link('follow_spouse'); ?>
	</td>
	<td class="optionbox" id="followspousebox">
	<input tabindex="6" type="checkbox" name="followspouse" value="1"
	<?php
	if ($followspouse) {
		echo " checked=\"checked\"";
	}
	echo " onclick=\"document.people.path_to_find.value='-1';\""?> />
	</td>
	<?php
	if ((!empty($pid1))&&(!empty($pid2))&&($disp)) {
		echo "</tr><tr>";
		if (($disp)&&(!$check_node)) {
			echo "<td class=\"topbottombar wrap vmiddle center\" colspan=\"2\">";
			if (isset($_SESSION["relationships"])) {
				if ($path_to_find==0) {
					echo "<span class=\"error\">", i18n::translate('No link between the two individuals could be found.'), "</span><br />";
				} else {
					echo "<span class=\"error\">", i18n::translate('No other link between the two individuals could be found.'), "</span><br />";
				}
			}
			if (!$followspouse) {
				?>
				<script language="JavaScript" type="text/javascript">
				document.getElementById("followspousebox").className='facts_valuered';
				</script>
				<?php
				echo "<input class=\"error\" type=\"submit\" value=\"", i18n::translate('Check relationships by marriage'), "\" onclick=\"people.followspouse.checked='checked';\"/>";
			}
			echo "</td>";
		} else {
			echo "<td class=\"topbottombar vmiddle center\" colspan=\"2\"><input type=\"submit\" value=\"", i18n::translate('Find next path'), "\" onclick=\"document.people.path_to_find.value='", $path_to_find+1, "';\" /></td>\n";
		}
		$pass = true;
	}

	if ($pass == false) echo "</tr><tr><td colspan=\"2\" class=\"topbottombar wrap\">&nbsp;</td>";?>

	<!-- // Empty space -->
	<td></td>

	<!-- // View button -->
	<td class="topbottombar vmiddle center" colspan="2">
	<input tabindex="7" type="submit" value="<?php print i18n::translate('View')?>" />
	</td></tr>


	</table></form>
	<?php
}
else {
	$Dbaseyoffset=55;
	$Dbasexoffset=10;
}
?>
</div>
<?php
if ($show_full==0) {
	echo '<br /><span class="details2">', i18n::translate('Click on any of the boxes to get more information about that person.'), '</span><br />';
}
?>
<div id="relationship_chart<?php print ($TEXT_DIRECTION=="ltr")?"":"_rtl";?>" style="position:relative; z-index:1; width:98%;">
<?php
$maxyoffset = $Dbaseyoffset;
if ((!empty($pid1))&&(!empty($pid2))) {
	if (!$disp) {
		print "<br /><br />";
		print_privacy_error();
	}
	else {
		if (isset($_SESSION["relationships"][$path_to_find])) $node = $_SESSION["relationships"][$path_to_find];
		else $node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
		if ($node!==false) {
			$_SESSION["pid1"] = $pid1;
			$_SESSION["pid2"] = $pid2;
			if (!isset($_SESSION["relationships"])) $_SESSION["relationships"] = array();
			$_SESSION["relationships"][$path_to_find] = $node;
			$yoffset = $Dbaseyoffset + 20;
			$xoffset = $Dbasexoffset;
			$colNum = 0;
			$rowNum = 0;
			$boxNum = 0;
			$previous="";
			$previous2="";
			$xs = $Dbxspacing+70;
			$ys = $Dbyspacing+50;
			// step1 = tree depth calculation
			if ($pretty) {
				$dmin=0;
				$dmax=0;
				$depth=0;
				foreach($node["path"] as $index=>$pid) {
					if ($node["relations"][$index]=="father" || $node["relations"][$index]=="mother" || $node["relations"][$index]=="parent") {

					$depth++;
					if ($depth>$dmax) $dmax=$depth;
					if ($asc==0) $asc=1; // the first link is a parent link
					}
					if ($node["relations"][$index]=="son" || $node["relations"][$index]=="daughter" || $node["relations"][$index]=="child") {
						$depth--;
						if ($depth<$dmin) $dmin=$depth;
						if ($asc==0) $asc=-1; // the first link is a child link
					}
				}
				$depth=$dmax+$dmin;
				// need more yoffset before the first box ?
				if ($asc==1) $yoffset -= $dmin*($Dbheight+$ys);
				if ($asc==-1) $yoffset += $dmax*($Dbheight+$ys);
				$rowNum = ($asc==-1) ? $depth : 0;
			}
			$maxxoffset = -1*$Dbwidth-20;
			$maxyoffset = $yoffset;
			if ($TEXT_DIRECTION=="ltr") {
				$rArrow = $WT_IMAGES["rarrow"]["other"];
				$lArrow = $WT_IMAGES["larrow"]["other"];
			} else {
				$rArrow = $WT_IMAGES["larrow"]["other"];
				$lArrow = $WT_IMAGES["rarrow"]["other"];
			}
			foreach($node["path"] as $index=>$pid) {
				print "\r\n\r\n<!-- Node:{$index} -->\r\n";
				$linex = $xoffset;
				$liney = $yoffset;
				$mfstyle = "NN";
				$indirec = find_person_record($pid, WT_GED_ID);
				if (strpos($indirec, "1 SEX F")!==false) $mfstyle="F";
				if (strpos($indirec, "1 SEX M")!==false) $mfstyle="";
				$arrow_img = $WT_IMAGE_DIR."/".$WT_IMAGES["darrow"]["other"];
				if ($node["relations"][$index]=="father" || $node["relations"][$index]=="mother" || $node["relations"][$index]=="parent") {
					$line = $WT_IMAGES["vline"]["other"];
					$liney += $Dbheight;
					$linex += $Dbwidth/2;
					$lh = 54;
					$lw = 3;
					//check for paternal grandparent relationship
					if ($pretty) {
						if ($asc==0) $asc=1;
						if ($asc==-1) $arrow_img = $WT_IMAGE_DIR."/".$WT_IMAGES["uarrow"]["other"];
						$lh=$ys;
						$linex=$xoffset+$Dbwidth/2;
						// put the box up or down ?
						$yoffset += $asc*($Dbheight+$lh);
						$rowNum += $asc;
						if ($asc==1) $liney = $yoffset-$lh; else $liney = $yoffset+$Dbheight;
						// need to draw a joining line ?
						if ($previous=="child" and $previous2!="parent") {
							$joinh = 3;
							$joinw = $xs/2+2;
							$xoffset += $Dbwidth+$xs;
							$colNum ++;
							//$rowNum is inherited from the box immediately to the left
							$linex = $xoffset-$xs/2;
							if ($asc==-1) $liney=$yoffset+$Dbheight; else $liney=$yoffset-$lh;
							$joinx = $xoffset-$xs;
							$joiny = $liney-2-($asc-1)/2*$lh;
							echo "<div id=\"joina", $index, "\" style=\"position:absolute; ", $TEXT_DIRECTION=="ltr"?"left":"right", ":", $joinx + $Dbxspacing, "px; top:", $joiny + $Dbyspacing, "px; z-index:-100; \" align=\"center\"><img src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["hline"]["other"], "\" align=\"left\" width=\"", $joinw, "\" height=\"", $joinh, "\" alt=\"\" /></div>\n";
							$joinw = $xs/2+2;
							$joinx = $joinx+$xs/2;
							$joiny = $joiny+$asc*$lh;
							echo "<div id=\"joinb", $index, "\" style=\"position:absolute; ", $TEXT_DIRECTION=="ltr"?"left":"right", ":", $joinx + $Dbxspacing, "px; top:", $joiny + $Dbyspacing, "px; z-index:-100; \" align=\"center\"><img src=\"", $WT_IMAGE_DIR, "/", $WT_IMAGES["hline"]["other"], "\" align=\"left\" width=\"", $joinw, "\" height=\"", $joinh, "\" alt=\"\" /></div>\n";
						}
						$previous2=$previous;
						$previous="parent";
					}
					else $yoffset += $Dbheight+$Dbyspacing+50;
				}
				if ($node["relations"][$index]=="brother" || $node["relations"][$index]=="sister" || $node["relations"][$index]=="sibling") {
					$arrow_img = $WT_IMAGE_DIR."/".$rArrow;
					$xoffset += $Dbwidth+$Dbxspacing+70;
					$colNum ++;
					//$rowNum is inherited from the box immediately to the left
					$line = $WT_IMAGES["hline"]["other"];
					$linex += $Dbwidth;
					$liney += $Dbheight/2;
					$lh = 3;
					$lw = 70;
					if ($pretty) {
						$lw = $xs;
						$linex = $xoffset-$lw;
						$liney = $yoffset+$Dbheight/4;
						$previous2=$previous;
						$previous="";
					}
				}
				if ($node["relations"][$index]=="husband" || $node["relations"][$index]=="wife" || $node["relations"][$index]=="spouse") {
					$arrow_img = $WT_IMAGE_DIR."/".$rArrow;
					$xoffset += $Dbwidth+$Dbxspacing+70;
					$colNum ++;
					//$rowNum is inherited from the box immediately to the left
					$line = $WT_IMAGES["hline"]["other"];
					$linex += $Dbwidth;
					$liney += $Dbheight/2;
					$lh = 3;
					$lw = 70;
					if ($pretty) {
						$lw = $xs;
						$linex = $xoffset-$lw;
						$liney = $yoffset+$Dbheight/4;
						$previous2=$previous;
						$previous="";
					}
				}
				if ($node["relations"][$index]=="son" || $node["relations"][$index]=="daughter" || $node["relations"][$index]=="child") {
					$line = $WT_IMAGES["vline"]["other"];
					$liney += $Dbheight;
					$linex += $Dbwidth/2;
					$lh = 54;
					$lw = 3;
					if ($pretty) {
						if ($asc==0) $asc=-1;
						if ($asc==1) $arrow_img = $WT_IMAGE_DIR."/".$WT_IMAGES["uarrow"]["other"];
						$lh=$ys;
						$linex = $xoffset+$Dbwidth/2;
						// put the box up or down ?
						$yoffset -= $asc*($Dbheight+$lh);
						$rowNum -= $asc;
						if ($asc==-1) $liney = $yoffset-$lh; else $liney = $yoffset+$Dbheight;
						// need to draw a joining line ?
						if ($previous=="parent" and $previous2!="child") {
							$joinh = 3;
							$joinw = $xs/2+2;
							$xoffset += $Dbwidth+$xs;
							$colNum ++;
							//$rowNum is inherited from the box immediately to the left
							$linex = $xoffset-$xs/2;
							if ($asc==1) $liney=$yoffset+$Dbheight; else $liney=$yoffset-($lh+$Dbyspacing);
							$joinx = $xoffset-$xs;
							$joiny = $liney-2+($asc+1)/2*$lh;
							print "<div id=\"joina$index\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($joinx+$Dbxspacing)."px; top:".($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\" /></div>\n";
							$joinw = $xs/2+2;
							$joinx = $joinx+$xs/2;
							$joiny = $joiny-$asc*$lh;
							print "<div id=\"joinb$index\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($joinx+$Dbxspacing)."px; top:".($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\" /></div>\n";
						}
						$previous2=$previous;
						$previous="child";
					}
					else $yoffset += $Dbheight+$Dbyspacing+50;
				}
				if ($yoffset > $maxyoffset) $maxyoffset = $yoffset;
				$plinex = $linex;
				$pxoffset = $xoffset;

				// Adjust all box positions for proper placement with respect to other page elements
				if ($BROWSERTYPE=="mozilla" && $TEXT_DIRECTION=="rtl") $pxoffset += 10;
				else $pxoffset -= 3;
				$pyoffset = $yoffset - 2;

				if ($index>0) {
					if ($TEXT_DIRECTION=="rtl" && $line!=$WT_IMAGES["hline"]["other"]) {
						print "<div id=\"line$index\" dir=\"ltr\" style=\"background:none; position:absolute; right:".($plinex+$Dbxspacing)."px; top:".($liney+$Dbyspacing)."px; width:".($lw+$lh*2)."px; z-index:-100; \" align=\"right\">";
						print "<img src=\"$WT_IMAGE_DIR/$line\" align=\"right\" width=\"$lw\" height=\"$lh\" alt=\"\" />\n";
						print "<br />";
						print i18n::translate($node["relations"][$index])."\n";
						print "<img src=\"$arrow_img\" border=\"0\" align=\"middle\" alt=\"\" />\n";
					}
					else {
						print "<div id=\"line$index\" style=\"background:none;  position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".($plinex+$Dbxspacing)."px; top:".($liney+$Dbyspacing)."px; width:".($lw+$lh*2)."px; z-index:-100; \" align=\"".($lh==3?"center":"left")."\"><img src=\"$WT_IMAGE_DIR/$line\" align=\"left\" width=\"$lw\" height=\"$lh\" alt=\"\" />\n";
						print "<br />";
						print "<img src=\"$arrow_img\" border=\"0\" align=\"middle\" alt=\"\" />\n";
						if ($lh == 3) print "<br />"; // note: $lh==3 means horiz arrow
						print i18n::translate($node["relations"][$index])."\n";
					}
					print "</div>\n";
				}
				// Determine the z-index for this box
				$boxNum ++;
				if ($TEXT_DIRECTION=="rtl" && $BROWSERTYPE=="mozilla") {
					if ($pretty) $zIndex = ($colNum * $depth - $rowNum + $depth);
						else $zIndex = $boxNum;
				} else {
					if ($pretty) $zIndex = 200 - ($colNum * $depth + $rowNum);
					else $zIndex = 200 - $boxNum;
				}

				print "<div id=\"box$pid.0\" style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":".$pxoffset."px; top:".$pyoffset."px; width:".$Dbwidth."px; height:".$Dbheight."px; z-index:".$zIndex."; \"><table><tr><td colspan=\"2\" width=\"$Dbwidth\" height=\"$Dbheight\">";
				print_pedigree_person($pid, 1, ($view!="preview"));
				print "</td></tr></table></div>\n";
			}

			print "<div style=\"position:absolute; ".($TEXT_DIRECTION=="ltr"?"left":"right").":1px; top:".abs($Dbaseyoffset-70)."px; z-index:1;\">";
			echo '<h4>', i18n::translate('Relationship: %s', get_relationship_name($node)), '</h4></div>';
		}
	}
}

$maxyoffset += 100;
?>
</div>
<script language="JavaScript" type="text/javascript">
	relationship_chart_div = document.getElementById("relationship_chart");
	if (!relationship_chart_div) relationship_chart_div = document.getElementById("relationship_chart_rtl");
	if (relationship_chart_div) {
		relationship_chart_div.style.height = <?php print ($maxyoffset-50); ?> + "px";
		relationship_chart_div.style.width = "100%";
	}
</script>
<?php
print_footer();

?>
