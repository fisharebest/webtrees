<?php
// Merge Two Gedcom Records
//
// This page will allow you to merge 2 gedcom records
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'admin_site_merge.php');
require './includes/session.php';
require_once WT_ROOT.'includes/functions/functions_edit.php';
require_once WT_ROOT.'includes/functions/functions_import.php';

$ged=$GEDCOM;
$gid1=safe_POST_xref('gid1');
$gid2=safe_POST_xref('gid2');
$action=safe_POST('action', WT_REGEX_ALPHA, 'choose');
$ged2=safe_POST('ged2', WT_REGEX_NOSCRIPT, $GEDCOM);
$keep1=safe_POST('keep1', WT_REGEX_UNSAFE);
$keep2=safe_POST('keep2', WT_REGEX_UNSAFE);
if (empty($keep1)) $keep1=array();
if (empty($keep2)) $keep2=array();

print_header(WT_I18N::translate('Merge records'));

if (get_gedcom_count()==1) { //Removed becasue it doesn't work here for multiple GEDCOMs. Can be reinstated when fixed (https://bugs.launchpad.net/webtrees/+bug/613235)
	if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm'; 
}

//-- make sure they have accept access privileges
if (!WT_USER_CAN_ACCEPT) {
	echo "<span class=\"error\">", WT_I18N::translate('<b>Access Denied</b><br />You do not have access to this resource.'), "</span>";
	print_footer();
	exit;
}

if ($action!="choose") {
	if ($gid1==$gid2 && $GEDCOM==$ged2) {
		$action="choose";
		echo "<span class=\"error\">", WT_I18N::translate('You entered the same IDs.  You cannot merge the same records.'), "</span>\n";
	} else {
		$gedrec1 = find_gedcom_record($gid1, WT_GED_ID, true);
		$gedrec2 = find_gedcom_record($gid2, get_id_from_gedcom($ged2), true);

		// Fetch the original XREF - may differ in case from the supplied value
		$tmp=new WT_Person($gedrec1); $gid1=$tmp->getXref();
		$tmp=new WT_Person($gedrec2); $gid2=$tmp->getXref();

		if (empty($gedrec1)) {
			echo '<span class="error">', WT_I18N::translate('Unable to find record with ID'), ':</span> ', $gid1, ', ', $ged;
			$action="choose";
		} elseif (empty($gedrec2)) {
			echo '<span class="error">', WT_I18N::translate('Unable to find record with ID'), ':</span> ', $gid2, ', ', $ged2;
			$action="choose";
		} else {
			$type1 = "";
			$ct = preg_match("/0 @$gid1@ (.*)/", $gedrec1, $match);
			if ($ct>0) {
				$type1 = trim($match[1]);
			}
			$type2 = "";
			$ct = preg_match("/0 @$gid2@ (.*)/", $gedrec2, $match);
			if ($ct>0) $type2 = trim($match[1]);
			if (!empty($type1) && ($type1!=$type2)) {
				echo "<span class=\"error\">", WT_I18N::translate('Records are not the same type.  Cannot merge records that are not the same type.'), "</span>\n";
				$action="choose";
			} else {
				$facts1 = array();
				$facts2 = array();
				$prev_tags = array();
				$ct = preg_match_all('/\n1 (\w+)/', $gedrec1, $match, PREG_SET_ORDER);
				for ($i=0; $i<$ct; $i++) {
					$fact = trim($match[$i][1]);
					if (isset($prev_tags[$fact])) {
						$prev_tags[$fact]++;
					} else {
						$prev_tags[$fact] = 1;
					}
					$subrec = get_sub_record(1, "1 $fact", $gedrec1, $prev_tags[$fact]);
					$facts1[] = array("fact"=>$fact, "subrec"=>trim($subrec));
				}
				$prev_tags = array();
				$ct = preg_match_all('/\n1 (\w+)/', $gedrec2, $match, PREG_SET_ORDER);
				for ($i=0; $i<$ct; $i++) {
					$fact = trim($match[$i][1]);
					if (isset($prev_tags[$fact])) {
						$prev_tags[$fact]++;
					} else {
						$prev_tags[$fact] = 1;
					}
					$subrec = get_sub_record(1, "1 $fact", $gedrec2, $prev_tags[$fact]);
					$facts2[] = array("fact"=>$fact, "subrec"=>trim($subrec));
				}
				if ($action=="select") {
					echo "<div id=\"merge2\"><h3>", WT_I18N::translate('Merge Step 2 of 3'), "</h3>";
					echo "<form method=\"post\" action=\"admin_site_merge.php\">\n";
					echo WT_I18N::translate('The following facts were exactly the same in both records and will be merged automatically.'), "<br />\n";
					echo "<input type=\"hidden\" name=\"gid1\" value=\"", $gid1, "\">\n";
					echo "<input type=\"hidden\" name=\"gid2\" value=\"", $gid2, "\">\n";
					echo "<input type=\"hidden\" name=\"ged\" value=\"", $GEDCOM, "\">\n";
					echo "<input type=\"hidden\" name=\"ged2\" value=\"", $ged2, "\">\n";
					echo "<input type=\"hidden\" name=\"action\" value=\"merge\">\n";
					$equal_count=0;
					$skip1 = array();
					$skip2 = array();
					echo "<table>";
					foreach ($facts1 as $i=>$fact1) {
						foreach ($facts2 as $j=>$fact2) {
							if (utf8_strtoupper($fact1["subrec"])==utf8_strtoupper($fact2["subrec"])) {
								$skip1[] = $i;
								$skip2[] = $j;
								$equal_count++;
								echo "<tr><td>", WT_I18N::translate($fact1['fact']);
								echo "<input type=\"hidden\" name=\"keep1[]\" value=\"", $i, "\" /></td>\n<td>", nl2br($fact1["subrec"]), "</td></tr>\n";
							}
						}
					}
					if ($equal_count==0) {
						echo "<tr><td>", WT_I18N::translate('No matching facts found'), "</td></tr>\n";
					}
					echo "</table><br />\n";
					echo WT_I18N::translate('The following facts did not match.  Select the information you would like to keep.'), "\n";
					echo "<table>\n";
					echo "<tr><th>", WT_I18N::translate('Record'), " ", $gid1, "</th><th>", WT_I18N::translate('Record'), " ", $gid2, "</th></tr>\n";
					echo "<tr><td>\n";
					echo "<table>\n";
					foreach ($facts1 as $i=>$fact1) {
						if (($fact1["fact"]!="CHAN")&&(!in_array($i, $skip1))) {
							echo "<tr><td><input type=\"checkbox\" name=\"keep1[]\" value=\"", $i, "\" checked=\"checked\" /></td>";
							echo "<td>", nl2br($fact1["subrec"]), "</td></tr>\n";
						}
					}
					echo "</table>\n";
					echo "</td><td>\n";
					echo "<table>\n";
					foreach ($facts2 as $j=>$fact2) {
						if (($fact2["fact"]!="CHAN")&&(!in_array($j, $skip2))) {
							echo "<tr><td><input type=\"checkbox\" name=\"keep2[]\" value=\"", $j, "\" checked=\"checked\" /></td>";
							echo "<td>", nl2br($fact2["subrec"]), "</td></tr>\n";
						}
					}
					echo "</table>";
					echo "</td></tr>";
					echo "</table>\n";
					echo "<input type=\"submit\" value=\"", WT_I18N::translate('Merge records'), "\">\n";
					echo "</form></div>";
				} elseif ($action=="merge") {
					$manual_save = true;
					echo "<div id=\"merge3\"><h3>", WT_I18N::translate('Merge Step 3 of 3'), "</h3>\n";
					if ($GEDCOM==$ged2) {
						$success = delete_gedrec($gid2, WT_GED_ID);
						if ($success) {
							echo "<br />", WT_I18N::translate('GEDCOM record successfully deleted.'), "<br />\n";
						}

						//-- replace all the records that linked to gid2
						$ids=fetch_all_links($gid2, WT_GED_ID);

						foreach ($ids as $id) {
							$record=find_gedcom_record($id, WT_GED_ID, true);
							echo WT_I18N::translate('Updating linked record'), " {$id}<br />\n";
							$newrec=str_replace("@$gid2@", "@$gid1@", $record);
							$newrec=preg_replace(
								'/(\n1.*@.+@.*(?:(?:\n[2-9].*)*))((?:\n1.*(?:\n[2-9].*)*)*\1)/',
								'$2',
								$newrec
							);
							replace_gedrec($id, WT_GED_ID, $newrec);
						}
						// Update any linked user-accounts
						WT_DB::prepare(
							"UPDATE `##user_gedcom_setting`".
							" SET setting_value=?".
							" WHERE gedcom_id=? AND setting_name='gedcomid' AND setting_value=?"
						)->execute(array($gid2, WT_GED_ID, $gid1));

						// Merge hit counters
						$hits=WT_DB::prepare(
							"SELECT page_name, SUM(page_count)".
							" FROM `##hit_counter`".
							" WHERE gedcom_id=? AND page_parameter IN (?, ?)".
							" GROUP BY page_name"
						)->execute(array(WT_GED_ID, $gid1, $gid2))->fetchAssoc();
						foreach ($hits as $page_name=>$page_count) {
							WT_DB::prepare(
								"UPDATE `##hit_counter` SET page_count=?".
								" WHERE gedcom_id=? AND page_name=? AND page_parameter=?"
							)->execute(array($page_count, WT_GED_ID, $page_name, $gid1));
						}
						WT_DB::prepare(
							"DELETE FROM `##hit_counter`".
							" WHERE gedcom_id=? AND page_parameter=?"
						)->execute(array(WT_GED_ID, $gid2));
					}
					$newgedrec = "0 @$gid1@ $type1\n";
					for ($i=0; ($i<count($facts1) || $i<count($facts2)); $i++) {
						if (isset($facts1[$i])) {
							if (in_array($i, $keep1)) {
								$newgedrec .= $facts1[$i]["subrec"]."\n";
								echo WT_I18N::translate('Adding'), " ", $facts1[$i]["fact"], " ", WT_I18N::translate('from'), " ", $gid1, "<br />\n";
							}
						}
						if (isset($facts2[$i])) {
							if (in_array($i, $keep2)) {
								$newgedrec .= $facts2[$i]["subrec"]."\n";
								echo WT_I18N::translate('Adding'), " ", $facts2[$i]["fact"], " ", WT_I18N::translate('from'), " ", $gid2, "<br />\n";
							}
						}
					}

					replace_gedrec($gid1, WT_GED_ID, $newgedrec);
					$rec=WT_GedcomRecord::getInstance($gid1);
					echo '<br />', WT_I18N::translate('Record %s successfully updated.', $rec->getXrefLink()), '<br />';
					$fav_count=update_favorites($gid2, $gid1);
					if ($fav_count > 0) {
						echo '<br />', $fav_count, ' ', WT_I18N::translate('favorites updated.'), '<br />';
					}
					echo "<br /><a href=\"admin_site_merge.php?action=choose\">", WT_I18N::translate('Merge more records.'), "</a><br />\n";
					echo "</div>\n";
				}
			}
		}
	}
}
if ($action=="choose") {
	?>
	<script type="text/javascript">
	<!--
	var pasteto;
	function iopen_find(textbox, gedselect) {
		pasteto = textbox;
		ged = gedselect.options[gedselect.selectedIndex].value;
		findwin = window.open('find.php?type=indi&ged='+ged, '_blank', 'left=50, top=50, width=600, height=500, resizable=1,  scrollbars=1');
	}
	function fopen_find(textbox, gedselect) {
		pasteto = textbox;
		ged = gedselect.options[gedselect.selectedIndex].value;
		findwin = window.open('find.php?type=fam&ged='+ged, '_blank', 'left=50, top=50, width=600, height=500, resizable=1, scrollbars=1');
	}
	function sopen_find(textbox, gedselect) {
		pasteto = textbox;
		ged = gedselect.options[gedselect.selectedIndex].value;
		findwin = window.open('find.php?type=source&ged='+ged, '_blank', 'left=50, top=50, width=600, height=500, resizable=1, scrollbars=1');
	}
	function paste_id(value) {
		pasteto.value=value;
	}
	//-->
	</script>
	<?php
	echo 
		'<script type="text/javascript">document.getElementById("gid1").focus();</script>
		<div id="merge"><h3>', WT_I18N::translate('Merge Step 1 of 3'), '</h3>
		<form method="post" name="merge" action="admin_site_merge.php">
		<input type="hidden" name="action" value="select" />
		<p>', WT_I18N::translate('Select two GEDCOM records to merge.  The records must be of the same type.'), '</p>
		<table class="', $TEXT_DIRECTION, '"><tr>
		<td>',
		WT_I18N::translate('Merge To ID:'),
		'</td><td>
		<input type="text" name="gid1" id="gid1" value="', $gid1, '" size="10" tabindex="1" />&nbsp;';
	if (get_gedcom_count()>1) {
		echo '<select name="ged" tabindex="4">';
		$all_gedcoms=get_all_gedcoms();
		asort($all_gedcoms);
		foreach ($all_gedcoms as $ged_id=>$ged_name) {
			echo '<option value="', $ged_name, '"';
			if (empty($ged) && $ged_id==WT_GED_ID || !empty($ged) && $ged==$ged_name) {
				echo ' selected="selected"';
			}
			echo '>', PrintReady(strip_tags(get_gedcom_setting($ged_id, 'title'))), '</option>';
		}
	} else if (get_gedcom_count()==1) {
		echo '<input type="hidden" name="ged" value="', WT_GEDCOM, '" />';
	}
	$inditext = WT_I18N::translate('Find individual ID');
	if (isset($WT_IMAGES["button_indi"])) $inditext = "<img src=\"".$WT_IMAGES["button_indi"]."\" alt=\"".$inditext."\" title=\"".$inditext."\" border=\"0\" align=\"middle\" />";
	$famtext = WT_I18N::translate('Find Family ID');
	if (isset($WT_IMAGES["button_family"])) $famtext = "<img src=\"".$WT_IMAGES["button_family"]."\" alt=\"".$famtext."\" title=\"".$famtext."\" border=\"0\" align=\"middle\" />";
	$sourtext = WT_I18N::translate('Find Source ID');
	if (isset($WT_IMAGES["button_source"])) $sourtext = "<img src=\"".$WT_IMAGES["button_source"]."\" alt=\"".$sourtext."\" title=\"".$sourtext."\" border=\"0\" align=\"middle\" />";
	echo
		'</select>
		<a href="javascript:iopen_find(document.merge.gid1, document.merge.ged);" tabindex="6">', $inditext, '</a>
		<a href="javascript:fopen_find(document.merge.gid1, document.merge.ged);" tabindex="8">', $famtext, '</a>
		<a href="javascript:sopen_find(document.merge.gid1, document.merge.ged);" tabindex="10">', $sourtext, '</a>
		</td></tr><tr><td>',
		WT_I18N::translate('Merge From ID:'),
		'</td><td>
		<input type="text" name="gid2" id="gid2" value="', $gid2, '" size="10" tabindex="2" />&nbsp;';
	if (get_gedcom_count()>1) {
		echo '<select name="ged2" tabindex="5">';
		foreach ($all_gedcoms as $ged_id=>$ged_name) {
			echo '<option value="', $ged_name, '"';
			if (empty($ged2) && $ged_id==WT_GED_ID || !empty($ged2) && $ged2==$ged_name) {
				echo ' selected="selected"';
			}
			echo '>', PrintReady(strip_tags(get_gedcom_setting($ged_id, 'title'))), '</option>';
		}
	} else if (get_gedcom_count()==1) {
		echo '<input type="hidden" name="ged2" value="', WT_GEDCOM, '" />';
	}
	echo
		'</select>
		<a href="javascript:iopen_find(document.merge.gid2, document.merge.ged2);" tabindex="7">', $inditext, '</a>
		<a href="javascript:fopen_find(document.merge.gid2, document.merge.ged2);" tabindex="9">', $famtext, '</a>
		<a href="javascript:sopen_find(document.merge.gid2, document.merge.ged2);" tabindex="11">',  $sourtext, '</a>
		</td></tr></table>
		<input type="submit" value="', WT_I18N::translate('Merge records'), '" tabindex="3" />
		</form></div>';
}
print_footer();
