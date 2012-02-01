<?php
// Calculates the relationship between two individuals in the gedcom
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

define('WT_SCRIPT_NAME', 'relationship.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

$controller=new WT_Controller_Base();

if (isset($_REQUEST['show_full'])) {
	$show_full = $_REQUEST['show_full'];
} else {
	$show_full=$PEDIGREE_FULL_DETAILS;
}
if (!isset($_REQUEST['path_to_find'])) {
	$path_to_find = 0;
	unset($_SESSION['relationships']);
} else {
	$path_to_find = $_REQUEST['path_to_find'];
}
if ($path_to_find == -1) {
	$path_to_find = 0;
	unset($_SESSION['relationships']);
}
if ($show_full==false) {
	$bwidth  = $cbwidth;
	$bheight = $cbheight;
	$Dbwidth = $cbwidth;
}

$Dbheight     = $bheight;
$Dbxspacing		= 0;
$Dbasexoffset	= 0;
$Dbaseyoffset	= 110;

$pid1=safe_GET_xref('pid1');
$pid2=safe_GET_xref('pid2');

if (!isset($_REQUEST['followspouse'])) {
	$followspouse = 0;
} else {
	$followspouse = $_REQUEST['followspouse'];
}
if (!isset($_REQUEST['asc'])) {
	$asc=1;
} else {
	$asc = $_REQUEST['asc'];
}
if ($asc=='') {
	$asc=1;
}
if (empty($pid1)) {
	$followspouse = 1;
}
$check_node = true;
$disp = true;

// ------------------------------------------------------------------------------------------------------------------------------

$person1=WT_Person::getInstance($pid1);
$person2=WT_Person::getInstance($pid2);

if ($person1 && $person2) {
	$controller->setPageTitle(WT_I18N::translate(/* I18N: %s are people's names */ 'Relationships between %1$s and %2$s', $person1->getFullName(), $person2->getFullName()));
} else {
	$controller->setPageTitle(WT_I18N::translate('Relationships'));
}

if ($person1) {
	$pid1=$person1->getXref(); // i1 => I1
} else {
	$pid1='';
}
if (!empty($_SESSION['pid1']) && $_SESSION['pid1']!=$pid1) {
	unset($_SESSION['relationships']);
	$path_to_find=0;
}
if ($person2) {
	$pid2=$person2->getXref(); // i2 => I2
} else {
	$pid2='';
}
if (!empty($_SESSION['pid2']) && $_SESSION['pid2']!=$pid2) {
	unset($_SESSION['relationships']);
	$path_to_find=0;
}

// -- print html header information
$controller
	->addInlineJavaScript('var pastefield; function paste_id(value) { pastefield.value=value; }') // For the 'find indi' link
	->pageHeader();

if ($ENABLE_AUTOCOMPLETE) {
	require WT_ROOT.'js/autocomplete.js.htm';
}

if (WT_USE_LIGHTBOX) {
	require_once WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

?>
<div id="relationship_chart_options" style="position: relative; z-index:90; width:98%;">
	<h2><?php echo $controller->getPageTitle(); ?></h2>
	<form name="people" method="get" action="relationship.php">
		<input type="hidden" name="ged" value="<?php echo WT_GEDCOM; ?>">
		<input type="hidden" name="path_to_find" value="<?php echo $path_to_find; ?>">
		<table class="list_table">
			<tr>
				<td colspan="2" class="topbottombar center">
					<?php echo WT_I18N::translate('Relationships'); ?>
				</td>
				<td>
					&nbsp;
				</td>
				<td colspan="2" class="topbottombar center">
					<?php echo WT_I18N::translate('Options:'); ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Person 1'); ?>
				</td>
				<td class="optionbox vmiddle">
					<input tabindex="1" class="pedigree_form" type="text" name="pid1" id="pid1" size="3" value="<?php echo $pid1; ?>">
					<?php print_findindi_link('pid1',''); ?>
				</td>
				<td>
					&nbsp;
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Show Details'); ?>
				</td>
				<td class="optionbox vmiddle">
					<input type="hidden" name="show_full" value="<?php echo $show_full; ?>">
					<input tabindex="3" type="checkbox" name="showfull" value="0" <?php if ($show_full) { echo ' checked="checked"'; } ?> onclick="document.people.show_full.value='<?php echo !$show_full; ?>';">
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Person 2'); ?>
				</td>
				<td class="optionbox vmiddle">
					<input tabindex="2" class="pedigree_form" type="text" name="pid2" id="pid2" size="3" value="<?php echo $pid2; ?>">
					<?php print_findindi_link('pid2',''); ?>
				</td>
				<td>
					&nbsp;
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Show oldest top'), help_link('oldest_top'); ?>
				</td>
				<td class="optionbox">
					<input tabindex="4" type="checkbox" name="asc" value="-1"
					<?php if ($asc==-1) echo " checked=\"checked\""; ?>>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox">
					<?php
					$pass = false;
					if (isset($_SESSION['relationships']) && !empty($pid1) && !empty($pid2)) {
						$pass = true;
						$i=0;
						$new_path=true;
						if (isset($_SESSION['relationships'][$path_to_find])) {
							$node = $_SESSION['relationships'][$path_to_find];
						} else {
							$node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
						}
						if (!$node) {
							$path_to_find--;
							$check_node=$node;
						}
						foreach ($_SESSION['relationships'] as $node) {
							if ($i==0) {
								echo WT_I18N::translate('Show path').": </td><td class=\"list_value\" style=\"padding: 3px;\">";
							}
							if ($i>0) {
								echo ' | ';
							}
							if ($i==$path_to_find) {
								echo "<span class=\"error\" style=\"valign: middle\">".($i+1).'</span>';
								$new_path=false;
							} else {
								echo "<a href=\"relationship.php?pid1={$pid1}&amp;pid2={$pid2}&amp;path_to_find={$i}&amp;followspouse={$followspouse}&amp;show_full={$show_full}&amp;asc={$asc}\">".($i+1).'</a>';
							}
							$i++;
						}
						if ($new_path && $path_to_find<$i+1 && $check_node) {
							echo " | <span class=\"error\">".($i+1).'</span>';
						}
						echo '</td>';
					} else {
						if ($person1 && $person2) {
							$disp=$person1->canDisplayName() && $person2->canDisplayName();
							if ($disp) {
								echo WT_I18N::translate('Show path'), ': </td>';
								echo "<td class=\"optionbox\">";
								echo " <span class=\"error vmmiddle\">";
								$check_node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
								echo $check_node ? '1' : '&nbsp;'.WT_I18N::translate('No results found.'), '</span></td>';
								$prt = true;
							}
						}
						if (!isset($prt)) {
							echo "&nbsp;</td><td class=\"optionbox\">&nbsp;</td>";
						}
					}
					?>
				<td>
					&nbsp;
				</td>
				<td class="descriptionbox">
					<?php echo WT_I18N::translate('Check relationships by marriage'), help_link('CHECK_MARRIAGE_RELATIONS'); ?>
				</td>
				<td class="optionbox" id="followspousebox">
					<input tabindex="6" type="checkbox" name="followspouse" value="1" <?php if ($followspouse) { echo ' checked="checked"'; } ?> onclick="document.people.path_to_find.value='-1';" >
				</td>
				<?php
				if ($person1 && $person2 && $disp) {
					echo '</tr><tr>';
					if (($disp)&&(!$check_node)) {
						echo "<td class=\"topbottombar wrap vmiddle center\" colspan=\"2\">";
						if (isset($_SESSION["relationships"])) {
							if ($path_to_find==0) {
								echo "<span class=\"error\">", WT_I18N::translate('No link between the two individuals could be found.'), "</span><br>";
							} else {
								echo "<span class=\"error\">", WT_I18N::translate('No other link between the two individuals could be found.'), "</span><br>";
							}
						}
						if (!$followspouse) {
							$controller->addInlineJavaScript('document.getElementById("followspousebox").className="facts_valuered";');
							echo "<input class=\"error\" type=\"submit\" value=\"", WT_I18N::translate('Check relationships by marriage'), "\" onclick=\"people.followspouse.checked='checked';\">";
						}
						echo '</td>';
					} else {
						echo "<td class=\"topbottombar vmiddle center\" colspan=\"2\"><input type=\"submit\" value=\"", WT_I18N::translate('Find next path'), "\" onclick=\"document.people.path_to_find.value='", $path_to_find+1, "';\">";
						echo help_link('next_path');
						echo '</td>';
					}
					$pass = true;
				}

				if ($pass == false) {
					echo "</tr><tr><td colspan=\"2\" class=\"topbottombar wrap\">&nbsp;</td>";
				}
				?>
				<td>
					&nbsp;
				</td>
				<td class="topbottombar vmiddle center" colspan="2">
					<input tabindex="7" type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
if ($check_node===false) {
	exit;
}
?>

<div id="relationship_chart">

<?php
$maxyoffset = $Dbaseyoffset;
if ((!empty($pid1))&&(!empty($pid2))) {
	if (!$disp) {
		echo '<div style="position:absolute; ', ($TEXT_DIRECTION=='ltr'?'left':'right'), ':1px; top:', abs($Dbaseyoffset-70), 'px; z-index:1;">';
		print_privacy_error();
		echo '</div>';
	} else {
		if (isset($_SESSION['relationships'][$path_to_find])) {
			$node = $_SESSION['relationships'][$path_to_find];
		} else {
			$node = get_relationship($pid1, $pid2, $followspouse, 0, true, $path_to_find);
		}
		if ($node!==false) {
			$_SESSION['pid1'] = $pid1;
			$_SESSION['pid2'] = $pid2;
			if (!isset($_SESSION['relationships'])) {
				$_SESSION['relationships'] = array();
			}
			$_SESSION['relationships'][$path_to_find] = $node;
			$yoffset = $Dbaseyoffset + 20;
			$xoffset = $Dbasexoffset;
			$colNum = 0;
			$rowNum = 0;
			$previous='';
			$previous2='';
			$xs = $Dbxspacing+70;
			$ys = $Dbyspacing+50;
			// step1 = tree depth calculation
			$dmin=0;
			$dmax=0;
			$depth=0;
			foreach ($node['path'] as $index=>$pid) {
				if ($node['relations'][$index]=='father' || $node['relations'][$index]=='mother' || $node['relations'][$index]=='parent') {
					$depth++;
					if ($depth>$dmax) {
						$dmax=$depth;
					}
					if ($asc==0) {
						$asc=1; // the first link is a parent link
					}
				}
				if ($node['relations'][$index]=='son' || $node['relations'][$index]=='daughter' || $node['relations'][$index]=='child') {
					$depth--;
					if ($depth<$dmin) {
						$dmin=$depth;
					}
					if ($asc==0) {
						$asc=-1; // the first link is a child link
					}
				}
			}
			$depth=$dmax+$dmin;
			// need more yoffset before the first box ?
			if ($asc==1) {
				$yoffset -= $dmin*($Dbheight+$ys);
			}
			if ($asc==-1) {
				$yoffset += $dmax*($Dbheight+$ys);
			}
			$rowNum = ($asc==-1) ? $depth : 0;
			$maxxoffset = -1*$Dbwidth-20;
			$maxyoffset = $yoffset;
			if ($TEXT_DIRECTION=='ltr') {
				$rArrow = $WT_IMAGES['rarrow'];
				$lArrow = $WT_IMAGES['larrow'];
			} else {
				$rArrow = $WT_IMAGES['larrow'];
				$lArrow = $WT_IMAGES['rarrow'];
			}
			foreach ($node['path'] as $index=>$pid) {
				$linex = $xoffset;
				$liney = $yoffset;
				$mfstyle = 'NN';
				$person=WT_Person::getInstance($pid);
				switch ($person->getSex()) {
				case 'M': $mfstyle='';   break;
				case 'F': $mfstyle='F';  break;
				case 'U': $mfstyle='NN'; break;
				}
				$arrow_img = $WT_IMAGES['darrow'];
				if ($node['relations'][$index]=='father' || $node['relations'][$index]=='mother' || $node['relations'][$index]=='parent') {
					$line = $WT_IMAGES['vline'];
					$liney += $Dbheight;
					$linex += $Dbwidth/2;
					$lh = 54;
					$lw = 3;
					//check for paternal grandparent relationship
					if ($asc==0) {
						$asc=1;
					}
					if ($asc==-1) {
						$arrow_img = $WT_IMAGES['uarrow'];
					}
					$lh=$ys;
					$linex=$xoffset+$Dbwidth/2;
					// put the box up or down ?
					$yoffset += $asc*($Dbheight+$lh);
					$rowNum += $asc;
					if ($asc==1) {
						$liney = $yoffset-$lh;
					}	else {
						$liney = $yoffset+$Dbheight;
					}
					// need to draw a joining line ?
					if ($previous=='child' && $previous2!='parent') {
						$joinh = 3;
						$joinw = $xs/2+2;
						$xoffset += $Dbwidth+$xs;
						$colNum ++;
						//$rowNum is inherited from the box immediately to the left
						$linex = $xoffset-$xs/2;
						if ($asc==-1) {
							$liney=$yoffset+$Dbheight;
						}	else {
							$liney=$yoffset-$lh;
						}
						$joinx = $xoffset-$xs;
						$joiny = $liney-2-($asc-1)/2*$lh;
						echo "<div id=\"joina", $index, "\" style=\"position:absolute; ", $TEXT_DIRECTION=='ltr'?'left':'right', ':', $joinx + $Dbxspacing, 'px; top:', $joiny + $Dbyspacing, "px; z-index:-100; \" align=\"center\"><img src=\"", $WT_IMAGES['hline'], "\" align=\"left\" width=\"", $joinw, "\" height=\"", $joinh, "\" alt=\"\"></div>";
						$joinw = $xs/2+2;
						$joinx = $joinx+$xs/2;
						$joiny = $joiny+$asc*$lh;
						echo "<div id=\"joinb", $index, "\" style=\"position:absolute; ", $TEXT_DIRECTION=='ltr'?'left':'right', ':', $joinx + $Dbxspacing, 'px; top:', $joiny + $Dbyspacing, "px; z-index:-100; \" align=\"center\"><img src=\"", $WT_IMAGES["hline"], "\" align=\"left\" width=\"", $joinw, "\" height=\"", $joinh, "\" alt=\"\"></div>";
					}
					$previous2=$previous;
					$previous='parent';
				}
				if ($node['relations'][$index]=='brother' || $node['relations'][$index]=='sister' || $node['relations'][$index]=='sibling') {
					$arrow_img = $rArrow;
					$xoffset += $Dbwidth+$Dbxspacing+70;
					$colNum ++;
					//$rowNum is inherited from the box immediately to the left
					$line = $WT_IMAGES['hline'];
					$linex += $Dbwidth;
					$liney += $Dbheight/2;
					$lh = 3;
					$lw = 70;
					$lw = $xs;
					$linex = $xoffset-$lw;
					$liney = $yoffset+$Dbheight/4;
					$previous2=$previous;
					$previous='';
				}
				if ($node['relations'][$index]=='husband' || $node['relations'][$index]=='wife' || $node['relations'][$index]=='spouse') {
					$arrow_img = $rArrow;
					$xoffset += $Dbwidth+$Dbxspacing+70;
					$colNum ++;
					//$rowNum is inherited from the box immediately to the left
					$line = $WT_IMAGES['hline'];
					$linex += $Dbwidth;
					$liney += $Dbheight/2;
					$lh = 3;
					$lw = 70;
					$lw = $xs;
					$linex = $xoffset-$lw;
					$liney = $yoffset+$Dbheight/4;
					$previous2=$previous;
					$previous='';
				}
				if ($node['relations'][$index]=='son' || $node['relations'][$index]=='daughter' || $node['relations'][$index]=='child') {
					$line = $WT_IMAGES['vline'];
					$liney += $Dbheight;
					$linex += $Dbwidth/2;
					$lh = 54;
					$lw = 3;
					if ($asc==0) $asc=-1;
					if ($asc==1) $arrow_img = $WT_IMAGES['uarrow'];
					$lh=$ys;
					$linex = $xoffset+$Dbwidth/2;
					// put the box up or down ?
					$yoffset -= $asc*($Dbheight+$lh);
					$rowNum -= $asc;
					if ($asc==-1) {
						$liney = $yoffset-$lh;
					}	else {
						$liney = $yoffset+$Dbheight;
					}
					// need to draw a joining line ?
					if ($previous=='parent' && $previous2!='child') {
						$joinh = 3;
						$joinw = $xs/2+2;
						$xoffset += $Dbwidth+$xs;
						$colNum ++;
						//$rowNum is inherited from the box immediately to the left
						$linex = $xoffset-$xs/2;
						if ($asc==1) {
							$liney=$yoffset+$Dbheight;
						}	else {
							$liney=$yoffset-($lh+$Dbyspacing);
						}
						$joinx = $xoffset-$xs;
						$joiny = $liney-2+($asc+1)/2*$lh;
						echo "<div id=\"joina$index\" style=\"position:absolute; ".($TEXT_DIRECTION=='ltr'?'left':'right').':'.($joinx+$Dbxspacing).'px; top:'.($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$WT_IMAGES["hline"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\"></div>";
						$joinw = $xs/2+2;
						$joinx = $joinx+$xs/2;
						$joiny = $joiny-$asc*$lh;
						echo "<div id=\"joinb$index\" style=\"position:absolute; ".($TEXT_DIRECTION=='ltr'?'left':'right').':'.($joinx+$Dbxspacing).'px; top:'.($joiny+$Dbyspacing)."px; z-index:-100; \" align=\"center\"><img src=\"".$WT_IMAGES["hline"]."\" align=\"left\" width=\"".$joinw."\" height=\"".$joinh."\" alt=\"\"></div>";
					}
					$previous2=$previous;
					$previous='child';
				}
				if ($yoffset > $maxyoffset) {
					$maxyoffset = $yoffset;
				}
				$plinex = $linex;
				$pxoffset = $xoffset;

				// Adjust all box positions for proper placement with respect to other page elements
				$pyoffset = $yoffset - 2;

				if ($index>0) {
					if ($TEXT_DIRECTION=='rtl' && $line!=$WT_IMAGES['hline']) {
						echo "<div id=\"line$index\" dir=\"ltr\" style=\"background:none; position:absolute; right:".($plinex+$Dbxspacing).'px; top:'.($liney+$Dbyspacing).'px; width:'.($lw+$lh*2)."px; z-index:-100;\" align=\"right\">";
						echo "<img src=\"$line\" align=\"right\" width=\"$lw\" height=\"$lh\" alt=\"\">";
						echo "<br>";
						echo WT_I18N::translate($node['relations'][$index]);
						echo "<img src=\"$arrow_img\" align=\"middle\" alt=\"\">";
					} else {
						echo "<div id=\"line$index\" style=\"background:none;  position:absolute; ".($TEXT_DIRECTION=='ltr'?'left':'right').':'.($plinex+$Dbxspacing).'px; top:'.($liney+$Dbyspacing).'px; width:'.($lw+$lh*2)."px; z-index:-100;\" align=\"".($lh==3?"center":"left")."\"><img src=\"$line\" align=\"left\" width=\"$lw\" height=\"$lh\" alt=\"\">";
						echo '<br>';
						echo "<img src=\"$arrow_img\" align=\"middle\" alt=\"\">";
						if ($lh == 3) {
							echo '<br>'; // note: $lh==3 means horiz arrow
						}
						echo WT_I18N::translate($node['relations'][$index]);
					}
					echo '</div>';
				}

				// Determine the z-index for this box
				$zIndex = 200 - ($colNum * $depth + $rowNum);

				echo "<div id=\"box$pid.0\" style=\"position:absolute; ".($TEXT_DIRECTION=='ltr'?'left':'right').':'.$pxoffset.'px; top:'.$pyoffset.'px; width:'.$Dbwidth.'px; height:'.$Dbheight.'px; z-index:'.$zIndex.";\"><table><tr><td colspan=\"2\" width=\"$Dbwidth\" height=\"$Dbheight\">";
				print_pedigree_person(WT_Person::getInstance($pid), 1);
				echo '</td></tr></table></div>';
			}

			echo '<div style="position:absolute; '.($TEXT_DIRECTION=='ltr'?'left':'right').':1px; top:'.abs($Dbaseyoffset-70).'px; z-index:1;">';
			echo '<h4>', WT_I18N::translate('Relationship: %s', get_relationship_name($node)), '</h4></div>';
		}
	}
}

$maxyoffset += 100;
?>
</div>
<?php

$controller
	->addInlineJavaScript('
		relationship_chart_div = document.getElementById("relationship_chart");
		if (relationship_chart_div) {
			relationship_chart_div.style.height = "'.($maxyoffset-50).'px";
			relationship_chart_div.style.width = "100%";
		}'
	);
