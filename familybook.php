<?php
/**
 * Display an family book chart
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
 * @package webtrees
 * @subpackage Charts
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'familybook.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_charts.php';

// Extract form variables
$pid        =safe_GET_xref('pid');
$show_full  =safe_GET('show_full',     array('0', '1'), $PEDIGREE_FULL_DETAILS);
$show_spouse=safe_GET('show_spouse',   '1', '0');
$descent    =safe_GET_integer('descent',       0, 9, 5);
$generations=safe_GET_integer('generations',   2, $MAX_DESCENDANCY_GENERATIONS, 2);
$box_width  =safe_GET_integer('box_width',     50, 300, 100);


// -- size of the boxes
if (!$show_full) $bwidth = ($bwidth / 1.5);
$bwidth = (int) ($bwidth * $box_width/100);

if ($show_full==false) {
	$bheight = (int) ($bheight / 2.5);
}
$bhalfheight = (int) ($bheight / 2);

// -- root id
$pid   =check_rootid($pid);
$person=Person::getInstance($pid);
$name  =$person->getFullName();

function print_descendency($person, $count) {
	global $show_spouse, $dgenerations, $bwidth, $bheight, $bhalfheight;
	global $TEXT_DIRECTION, $WT_IMAGES, $generations, $box_width, $show_full;

	if ($count>=$dgenerations) {
		return 0;
	}

	$pid=$person->getXref();

	echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
	echo "<tr>";
	echo "<td width=\"".($bwidth-2)."\">";
	$numkids = 0;
	$sfamilies=$person->getSpouseFamilies();
	if (count($sfamilies)>0) {
		$firstkids = 0;
		foreach ($sfamilies as $family) {
			$children=$family->getChildren();
			if (count($children)>0) {
				echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
				foreach ($children as $i=>$child) {
					$rowspan = 2;
					if ($i>0 && $i<count($children)-1) {
						$rowspan=1;
					}
					echo "<tr><td rowspan=\"$rowspan\" width=\"$bwidth\" style=\"padding-top: 2px;\">";
					if ($count < $dgenerations-1) {
						$kids = print_descendency($child, $count+1);
						if ($i==0) $firstkids = $kids;
						$numkids += $kids;
					} else {
						print_pedigree_person($child->getXref());
						$numkids++;
					}
					echo "</td>";
					$twidth = 7;
					if (count($children)==1) {
						$twidth+=3;
					}
					echo "<td rowspan=\"$rowspan\"><img src=\"".$WT_IMAGES["hline"]."\" width=\"$twidth\" height=\"3\" alt=\"\" /></td>";
					if (count($children)>1) {
						if ($i==0) {
							echo "<td height=\"".($bhalfheight+3)."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td></tr>";
							echo "<tr><td height=\"".($bhalfheight+3)."\" style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td>";
						}
						else if ($i==count($children)-1) {
							echo "<td height=\"".($bhalfheight+4)."\" style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td></tr>";
							echo "<tr><td height=\"".($bhalfheight+4)."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td>";
						}
						else {
							echo "<td style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td>";
						}
					}
					echo "</tr>";
				}
				echo "</table>";
			}
		}
		echo "</td>";
		echo "<td width=\"$bwidth\">";
	}

	if ($numkids==0) {
		$numkids = 1;
		$tbwidth = $bwidth+16;
		for ($j=$count; $j<$dgenerations; $j++) {
			echo "</td><td width=\"$bwidth\">";
		}
	}
	//-- add offset divs to make things line up better
	if ($show_spouse) {
		foreach ($sfamilies as $family) {
			echo '<br /><div style="height:', $bheight, 'px;width:', $bwidth, 'px;"><br /></div>';
		}
	}
	print_pedigree_person($pid);

	if ($show_spouse) {
		foreach ($sfamilies as $family) {
			echo '<br/>', $family->getMarriage()->print_simple_fact();
			print_pedigree_person($family->getSpouseId($pid));
		}
	}

	echo "</td></tr>";
	echo "</table>";
	return $numkids;
}

function print_person_pedigree($pid, $count) {
	global $generations, $SHOW_EMPTY_BOXES, $WT_IMAGES, $bheight, $bhalfheight;
	if ($count>=$generations) return;
	$hheight = ($bhalfheight+3) * pow(2,($generations-$count-1));
	foreach (Person::getInstance($pid)->getChildFamilies() as $family) {
		$famid=$family->getXref();
		echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">";
		$parents = find_parents($famid);
		$height="100%";
		echo "<tr>";
		if ($count<$generations-1) {
			echo "<td height=\"".$hheight."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td>";
			echo "<td rowspan=\"2\"><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>";
		}
		echo "<td rowspan=\"2\">";
		print_pedigree_person($parents["HUSB"]);
		echo "</td>";
		echo "<td rowspan=\"2\">";
		print_person_pedigree($parents["HUSB"], $count+1);
		echo "</td>";
		echo "</tr><tr><td height=\"".$hheight."\"";
		if ($count<$generations-1) {
			echo " style=\"background: url('".$WT_IMAGES["vline"]."');\" ";
		}
		echo "><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td></tr><tr>";
		if ($count<$generations-1) {
			echo "<td height=\"".$hheight."\" style=\"background: url('".$WT_IMAGES["vline"]."');\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td>";
			echo "<td rowspan=\"2\"><img src=\"".$WT_IMAGES["hline"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>";
		}
		echo "<td rowspan=\"2\">";
		print_pedigree_person($parents["WIFE"]);
		echo "</td>";
		echo "<td rowspan=\"2\">";
		print_person_pedigree($parents["WIFE"], $count+1);
		echo "</td>";
		echo "</tr>";
		if ($count<$generations-1) {
			echo "<tr><td height=\"".$hheight."\"><img src=\"".$WT_IMAGES["spacer"]."\" width=\"3\" alt=\"\" /></td></tr>";
		}
		echo "</table>";
	}
}

function print_family_book($person, $descent) {
	global $generations, $dgenerations, $firstrun;

	if ($descent==0 || !$person->canDisplayName()) {
		return;
	}
	$families=$person->getSpouseFamilies();
	if (count($families)>0 || empty($firstrun)) {
		$firstrun=true;
		echo
			'<h2 style="text-align:center">',
			/* I18N: A title/heading. %s is a person's name */ i18n::translate('Family of %s', $person->getFullName()),
			'</h2><table cellspacing="0" cellpadding="0" border="0"><tr><td valign="middle">';
		$dgenerations = $generations;
		print_descendency($person, 1);
		echo '</td><td valign="middle">';
		print_person_pedigree($person->getXref(), 1);
		echo '</td></tr></table><br /><br /><hr style="page-break-after:always;"/><br /><br />';

		foreach ($families as $family) {
			foreach ($family->getChildren() as $child) {
				print_family_book($child, $descent-1);
			}
		}
	}
}

// -- print html header information
print_header(PrintReady($name)." ".i18n::translate('Family book chart'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

// LBox =====================================================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// ==========================================================================================

echo '<table><tr><td valign="top">';
echo '<h2>'.i18n::translate('Family book chart'), help_link('family_book_chart'), '<br />', PrintReady($name), '</h2>';
?>

<script language="JavaScript" type="text/javascript">
<!--
	var pastefield;
	function open_find(textbox) {
		pastefield = textbox;
		findwin = window.open('find.php?type=indi', '_blank', 'left=50,top=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	function paste_id(value) {
		pastefield.value=value;
	}
//-->
</script>

<?php
$gencount=0;
?>
</td><td width="50px">&nbsp;</td><td><form method="get" name="people" action="?">
<table><tr>

<td class="descriptionbox">
	<?php echo i18n::translate('Root Person ID'), help_link('desc_rootid'); ?>
</td>
<td class="optionbox">
	<input class="pedigree_form" type="text" name="pid" id="pid" size="3" value="<?php echo $pid; ?>" />
	<?php print_findindi_link("pid",""); ?>
</td>

<td class="descriptionbox">
<?php echo i18n::translate('Show Details'), help_link('show_full'); ?>
</td>
<td class="optionbox">
<input type="hidden" name="show_full" value="<?php echo $show_full; ?>" />
<input type="checkbox" value="<?php
	if ($show_full) echo "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
else echo "0\" onclick=\"document.people.show_full.value='1';"; ?>" />
</td>

<td rowspan="4" class="topbottombar vmiddle">
<input type="submit" value="<?php echo i18n::translate('View'); ?>" />
</td></tr>

<tr><td class="descriptionbox">
<?php echo i18n::translate('Generations'), help_link('desc_generations'); ?>
</td>
<td class="optionbox">
<select name="generations">
<?php
for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
	echo "<option value=\"".$i."\"" ;
	if ($i == $generations) echo " selected=\"selected\"";
	echo ">".$i."</option>";
}
?>
</select>
</td>

<td class="descriptionbox">
	<?php echo i18n::translate('Show spouses'), help_link('show_spouse'); ?>
</td>
<td class="optionbox">
<input type="checkbox" value="1" name="show_spouse"
<?php
if ($show_spouse) echo " checked=\"checked\""; ?> />
</td></tr>

<tr><td class="descriptionbox">
	<?php echo i18n::translate('Box width'), help_link('box_width'); ?>
</td>
<td class="optionbox"><input type="text" size="3" name="box_width" value="<?php echo $box_width; ?>" />
<b>%</b>
</td>

<td class="descriptionbox">&nbsp;</td><td class="optionbox">&nbsp;</td></tr>

<tr><td class="descriptionbox">
	<?php echo i18n::translate('Descent Steps'), help_link('fambook_descent'); ?>
</td>
<td class="optionbox"><input type="text" size="3" name="descent" value="<?php echo $descent; ?>" />
</td>

<td class="descriptionbox">&nbsp;</td><td class="optionbox">&nbsp;</td></tr>
</table></form>
</td></tr></table>

<?php

echo
	'<div id="familybook_chart',
	($TEXT_DIRECTION=="ltr")?"":"_rtl",
	'" style="width:98%; direction:"', $TEXT_DIRECTION, ';z-index:1;">';

print_family_book($person, $descent);

echo '</div>';
print_footer();
