<?php
/**
 * Display an family book chart
 *
 * Set the root person using the $pid variable
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

function print_descendency($pid, $count) {
	global $show_spouse, $dgenerations, $bwidth, $bheight, $bhalfheight;
	global $TEXT_DIRECTION, $WT_IMAGE_DIR, $WT_IMAGES, $generations, $box_width, $view, $show_full;
	if ($count>=$dgenerations) return 0;
	print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
	print "<tr>";
	print "<td width=\"".($bwidth-2)."\">\n";
	$numkids = 0;
	$famids = find_sfamily_ids($pid);
	if (count($famids)>0) {
		$firstkids = 0;
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid, WT_GED_ID);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			if ($ct>0) {
			print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
			for($i=0; $i<$ct; $i++) {
				$rowspan = 2;
				if (($i>0)&&($i<$ct-1)) $rowspan=1;
				$chil = trim($match[$i][1]);
				print "<tr><td rowspan=\"$rowspan\" width=\"$bwidth\" style=\"padding-top: 2px;\">\n";
				if ($count < $dgenerations-1) {
					$kids = print_descendency($chil, $count+1);
					if ($i==0) $firstkids = $kids;
					$numkids += $kids;
				}
				else {
					print_pedigree_person($chil);
					$numkids++;
				}
				print "</td>\n";
				$twidth = 7;
				if ($ct==1) $twidth+=3;
				print "<td rowspan=\"$rowspan\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]."\" width=\"$twidth\" height=\"3\" alt=\"\" /></td>\n";
				if ($ct>1) {
					if ($i==0) {
						print "<td height=\"".($bhalfheight+3)."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
						print "<tr><td height=\"".($bhalfheight+3)."\" style=\"background: url('".$WT_IMAGE_DIR."/".$WT_IMAGES["vline"]["other"]."');\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
					else if ($i==$ct-1) {
						print "<td height=\"".($bhalfheight+4)."\" style=\"background: url('".$WT_IMAGE_DIR."/".$WT_IMAGES["vline"]["other"]."');\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
						print "<tr><td height=\"".($bhalfheight+4)."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
					else {
						print "<td style=\"background: url('".$WT_IMAGE_DIR."/".$WT_IMAGES["vline"]["other"]."');\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
					}
				}
				print "</tr>\n";
			}
			print "</table>\n";
			}
		}
		print "</td>\n";
		print "<td width=\"$bwidth\">\n";
	}
	// NOTE: If statement OK
	if ($numkids==0) {
		$numkids = 1;
		$tbwidth = $bwidth+16;
		for($j=$count; $j<$dgenerations; $j++) {
			print "</td>\n<td width=\"$bwidth\">\n";
		}
	}
	//-- add offset divs to make things line up better
	if ($show_spouse) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid, WT_GED_ID);
			if (!empty($famrec)) {
				$marrec = get_sub_record(1, "1 MARR", $famrec);
				if (!empty($marrec)) {
					print "<br />";
				}
				print "<div style=\"height: ".$bheight."px; width: ".$bwidth."px;\"><br /></div>\n";
			}
		}
	}
	print_pedigree_person($pid);
	// NOTE: If statement OK
	if ($show_spouse) {
		foreach($famids as $indexval => $famid) {
			$famrec = find_family_record($famid, WT_GED_ID);
			if (!empty($famrec)) {
				$parents = find_parents_in_record($famrec);
				$marrec = get_sub_record(1, "1 MARR", $famrec);
				if (!empty($marrec)) {
					print "<br />";
					$marriage = new Event($marrec);
					$marriage->print_simple_fact();
				}
				if ($parents["HUSB"]!=$pid) print_pedigree_person($parents["HUSB"]);
				else print_pedigree_person($parents["WIFE"]);
			}
		}
	}
	// NOTE: If statement OK
	if ($count==0) {
		$indirec = find_person_record($pid, WT_GED_ID);
		// NOTE: If statement OK
		if (displayDetailsById($pid, 'INDI') || showLivingNameById($pid)) {
			// -- print left arrow for decendants so that we can move down the tree
			$famids = find_sfamily_ids($pid);
			//-- make sure there is more than 1 child in the family with parents
			$cfamids = find_family_ids($pid);
			$num=0;
			// NOTE: For statement OK
			for($f=0; $f<count($cfamids); $f++) {
				$famrec = find_family_record($cfamids[$f], WT_GED_ID);
				if ($famrec) {
					$num += preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
				}
			}
			// NOTE: If statement OK
			if ($famids||($num>1)) {
				print "\n\t\t<div class=\"center\" id=\"childarrow.$pid\" dir=\"".$TEXT_DIRECTION."\"";
				print " style=\"position:absolute; width:".$bwidth."px; \">";
				if ($view!="preview") {
					print "<a href=\"javascript: ".i18n::translate('Show')."\" onclick=\"return togglechildrenbox('$pid');\" onmouseover=\"swap_image('larrow.$pid',3);\" onmouseout=\"swap_image('larrow.$pid',3);\">";
					print "<img id=\"larrow.$pid\" src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["darrow"]["other"]."\" border=\"0\" alt=\"\" />";
					print "</a>";
				}
				print "\n\t\t<div id=\"childbox.$pid\" dir=\"".$TEXT_DIRECTION."\" style=\"width:".$bwidth."px; height:".$bheight."px; visibility: hidden;\">";
				print "\n\t\t\t<table class=\"person_box\"><tr><td>";
				for($f=0; $f<count($famids); $f++) {
					$famrec = find_family_record(trim($famids[$f]), WT_GED_ID);
					if ($famrec) {
						$parents = find_parents($famids[$f]);
						if($parents) {
							if($pid!=$parents["HUSB"]) $spid=$parents["HUSB"];
							else $spid=$parents["WIFE"];
							$spouse=Person::getInstance($spid);
							if ($spouse) {
								$name=$spouse->getFullName();
								print "\n\t\t\t\t<a href=\"".encode_url("familybook.php?pid={$spid}&show_spouse={$show_spouse}&show_full={$show_full}&generations={$generations}&box_width={$box_width}")."\"><span class=\"";
								if (hasRTLText($name)) print "name2";
									else print "name1";
									print "\">";
								print PrintReady($name);
								print "<br /></span></a>";
							}
						}
						$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
						for($i=0; $i<$num; $i++) {
							//-- add the following line to stop a bad PHP bug
							if ($i>=$num) break;
							$cid = $smatch[$i][1];
							$child=Person::getInstance($cid);
							$name=$child->getFullName();
							print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"".encode_url("familybook.php?pid={$cid}&show_spouse={$show_spouse}&show_full={$show_full}&generations={$generations}&box_width={$box_width}")."\"><span class=\"";
							if (hasRTLText($name)) print "name2";
							else print "name1";
							print "\">&lt; ";
							print PrintReady($name);
							print "<br /></span></a>";
						}
					}
				}
				//-- print the siblings
				for($f=0; $f<count($cfamids); $f++) {
					$famrec = find_family_record($cfamids[$f], WT_GED_ID);
					if ($famrec) {
						$parents = find_parents($cfamids[$f]);
						if($parents) {
							print "<span class=\"name1\"><br />".i18n::translate('Parents')."<br /></span>";
							if (!empty($parents["HUSB"])) {
								$spid = $parents["HUSB"];
								$spouse=Person::getInstance($spid);
								$name=$spouse->getFullName();
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"".encode_url("familybook.php?pid={$spid}&show_spouse={$show_spouse}&show_full={$show_full}&generations={$generations}&box_width={$box_width}")."\"><span class=\"";
								if (hasRTLText($name)) print "name2";
							else print "name1";
							print "\">";
								print PrintReady($name);
								print "<br /></span></a>";
							}
							if (!empty($parents["WIFE"])) {
								$spid = $parents["WIFE"];
								$spouse=Person::getInstance($spid);
								$name=$spouse->getFullName();
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"".encode_url("familybook.php?pid={$spid}&show_spouse={$show_spouse}&show_full={$show_full}&generations={$generations}&box_width={$box_width}")."\"><span class=\"";
								if (hasRTLText($name)) print "name2";
							else print "name1";
							print "\">";
								print PrintReady($name);
								print "<br /></span></a>";
							}
						}
						$num = preg_match_all("/1\s*CHIL\s*@(.*)@/", $famrec, $smatch,PREG_SET_ORDER);
						if ($num>2) print "<span class=\"name1\"><br />".i18n::translate('Siblings')."<br /></span>";
						if ($num==2) print "<span class=\"name1\"><br />".i18n::translate('Sibling')."<br /></span>";
						for($i=0; $i<$num; $i++) {
							//-- add the following line to stop a bad PHP bug
							if ($i>=$num) break;
							$cid = $smatch[$i][1];
							if ($cid!=$pid) {
								$child=Person::getInstance($cid);
								$name=$child->getFullName();
								print "\n\t\t\t\t&nbsp;&nbsp;<a href=\"familybook.php?pid=$cid&amp;show_spouse=$show_spouse&amp;show_full=$show_full&amp;generations=$generations&amp;box_width=$box_width\"><span class=\"";
								if (hasRTLText($name)) print "name2";
								else print "name1";
								print "\"> ";
								print PrintReady($name);
								print "<br /></span></a>";
							}
						}
					}
				}
				print "\n\t\t\t</td></tr></table>";
				print "\n\t\t</div>";
				print "\n\t\t</div>";
			}
		}
	}
	print "</td></tr>\n";
	print "</table>\n";
	return $numkids;
}

function max_descendency_generations($pid, $depth) {
	global $generations;
	if ($depth >= $generations) return $depth;
	$famids = find_sfamily_ids($pid);
	$maxdc = $depth;
	foreach($famids as $indexval => $famid) {
		$famrec = find_family_record($famid, WT_GED_ID);
		$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
		for($i=0; $i<$ct; $i++) {
			$chil = trim($match[$i][1]);
			$dc = max_descendency_generations($chil, $depth+1);
			if ($dc >= $generations) return $dc;
			if ($dc > $maxdc) $maxdc = $dc;
		}
	}
	if ($maxdc==0) $maxdc++;
	return $maxdc;
}

function print_person_pedigree($pid, $count) {
	global $generations, $SHOW_EMPTY_BOXES, $WT_IMAGE_DIR, $WT_IMAGES, $bheight, $bhalfheight;
	if ($count>=$generations) return;
	$famids = find_family_ids($pid);
	$hheight = ($bhalfheight+3) * pow(2,($generations-$count-1));
	foreach($famids as $indexval => $famid) {
		print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"empty-cells: show;\">\n";
		$parents = find_parents($famid);
		$height="100%";
		print "<tr>";
		if ($count<$generations-1) {
			print "<td height=\"".$hheight."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>\n";
			print "<td rowspan=\"2\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>\n";
		}
		print "<td rowspan=\"2\">\n";
		print_pedigree_person($parents["HUSB"]);
		print "</td>\n";
		print "<td rowspan=\"2\">\n";
		print_person_pedigree($parents["HUSB"], $count+1);
		print "</td>\n";
		print "</tr>\n<tr>\n<td height=\"".$hheight."\"";
		if ($count<$generations-1) print " style=\"background: url('".$WT_IMAGE_DIR."/".$WT_IMAGES["vline"]["other"]."');\" ";
		print "><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n<tr>\n";
		if ($count<$generations-1) {
			print "<td height=\"".$hheight."\" style=\"background: url('".$WT_IMAGE_DIR."/".$WT_IMAGES["vline"]["other"]."');\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td>";
			print "<td rowspan=\"2\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["hline"]["other"]."\" width=\"7\" height=\"3\" alt=\"\" /></td>\n";
		}
		print "<td rowspan=\"2\">\n";
		print_pedigree_person($parents["WIFE"]);
		print "</td>\n";
		print "<td rowspan=\"2\">\n";
		print_person_pedigree($parents["WIFE"], $count+1);
		print "</td>\n";
		print "</tr>\n";
		if ($count<$generations-1) print "<tr>\n<td height=\"".$hheight."\"><img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" width=\"3\" alt=\"\" /></td></tr>\n";
		print "</table>\n";
	}
}
function print_family_book($pid, $descent)
{
	global $generations, $dgenerations, $firstrun;
	if ($descent==0) return;
		$famids = find_sfamily_ids($pid);
		if (count($famids)>0 || empty($firstrun)) {
			$firstrun = true;
				$pid=check_rootid($pid);
				$person=Person::getInstance($pid);
				$name=$person->getFullName();
				print "\n\t<h2 style=\"text-align: center\">".i18n::translate('Family of:&nbsp;').PrintReady($name)."</h2>";
				print "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr>\n";

				//-- descendancy
				print "<td valign=\"middle\">\n";
				$dgenerations = $generations;
// $dgenerations = max_descendency_generations($pid, 0);
				print_descendency($pid, 1);
				print "</td>\n";
				//-- pedigree
				print "<td valign=\"middle\">\n";
				print_person_pedigree($pid, 1);
				print "</td>\n";
				print "</tr></table>\n";
				print "<br /><br />\n";
				print "<hr style=\"page-break-after:always;\"/>\n";
				print "<br /><br />\n";

				foreach($famids as $indexval => $famid) {
						$famrec = find_family_record($famid, WT_GED_ID);
						$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
						for($i=0; $i<$ct; $i++) {
							$chil = trim($match[$i][1]);
							if (showLivingNameById($chil) || displayDetailsById($chil)) print_family_book($chil, $descent-1);
						}
				}
		}

}

// -- print html header information
print_header(PrintReady($name)." ".i18n::translate('Family Book Chart'));

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';

// LBox =====================================================================================
if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}
// ==========================================================================================

if ($view=="preview") {
	print "<h2 style=\"text-align: center\">".i18n::translate('Family Book Chart').":&nbsp;&nbsp;&nbsp;".PrintReady($name)."</h2>";
} else {
	print "<!-- // NOTE: Start table header -->";
	print "<table><tr><td valign=\"top\">";
	print "<h2>".i18n::translate('Family Book Chart').":<br />".PrintReady($name)."</h2>";
}
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
if ($view!="preview") {
?>
<!--	// NOTE: Start form and table -->
	</td><td width="50px">&nbsp;</td><td><form method="get" name="people" action="?">
	<table><tr>

	<!-- // NOTE: Root ID -->
	<td class="descriptionbox">
		<?php echo i18n::translate('Root Person ID'), help_link('desc_rootid'); ?>
	</td>
	<td class="optionbox">
		<input class="pedigree_form" type="text" name="pid" id="pid" size="3" value="<?php print $pid ?>"	/>
		<?php print_findindi_link("pid","");?>
	</td>

	<!-- // NOTE: Show Details -->
	<td class="descriptionbox">
	<?php echo i18n::translate('Show Details'), help_link('show_full'); ?>
	</td>
	<td class="optionbox">
	<input type="hidden" name="show_full" value="<?php print $show_full;?>" />
	<input type="checkbox" value="<?php
		if ($show_full) print "1\" checked=\"checked\" onclick=\"document.people.show_full.value='0';";
	else print "0\" onclick=\"document.people.show_full.value='1';";?>" />
	</td>

	<!-- // NOTE: Submit button -->
	<td rowspan="4" class="topbottombar vmiddle">
	<input type="submit" value="<?php print i18n::translate('View') ?>" />
	</td></tr>

	<!-- // NOTE: Generations -->
	<tr><td class="descriptionbox">
	<?php echo i18n::translate('Generations'), help_link('desc_generations'); ?>
	</td>
	<td class="optionbox">
	<select name="generations">
	<?php
	for ($i=2; $i<=$MAX_DESCENDANCY_GENERATIONS; $i++) {
		print "<option value=\"".$i."\"" ;
		if ($i == $generations) print " selected=\"selected\"";
		print ">".$i."</option>";
	}
	?>
	</select>
	</td>

	<!-- // NOTE: Show spouses -->
	<td class="descriptionbox">
		<?php echo i18n::translate('Show spouses'), help_link('show_spouse'); ?>
	</td>
	<td class="optionbox">
	<input type="checkbox" value="1" name="show_spouse"
	<?php
	if ($show_spouse) print " checked=\"checked\""; ?> />
	</td></tr>

	<!-- // NOTE: Box width -->
	<tr><td class="descriptionbox">
		<?php echo i18n::translate('Box width'), help_link('box_width'); ?>
	</td>
	<td class="optionbox"><input type="text" size="3" name="box_width" value="<?php print $box_width; ?>" />
	<b>%</b>
	</td>

	<!-- // NOTE: Empty field -->
	<td class="descriptionbox">&nbsp;</td><td class="optionbox">&nbsp;</td></tr>

	<!-- // NOTE: Descent steps -->
	<tr><td class="descriptionbox">
		<?php echo i18n::translate('Descent Steps'), help_link('fambook_descent'); ?>
	</td>
	<td class="optionbox"><input type="text" size="3" name="descent" value="<?php print $descent;?>" />
	</td>

	<!-- // NOTE: Empty field -->
	<td class="descriptionbox">&nbsp;</td><td class="optionbox">&nbsp;</td></tr>

	<!-- // NOTE: End table and form -->
	</table></form>

	<!-- // NOTE: Close table header -->
	</td></tr></table>
<?php
	if ($show_full==0) {
		echo '<br /><span class="details2">', i18n::translate('Click on any of the boxes to get more information about that person.'), '</span><br />';
	}
} ?>
<div id="familybook_chart<?php print ($TEXT_DIRECTION=="ltr")?"":"_rtl"; ?>" style="<?php if ($view=="preview") print "top: 1px;"; else print "width:98%; direction:".$TEXT_DIRECTION."; z-index:1;";?>" >
<?php

print_family_book($pid, $descent);

print "</div>";
print "<br /><br />\n";
print_footer();
?>
