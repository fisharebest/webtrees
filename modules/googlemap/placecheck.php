<?php
/**
 * Check a GEDCOM file for compliance with the 5.5.1 specification
 * and other common errors.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team. All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License or,
 * at your discretion, any later version.
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
 * @author Nigel Osborne 26 Mar 2007
 * @package webtrees
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

global $TBLPREFIX;
$action   =safe_POST     ('action'                                              );
$gedcom_id=safe_POST     ('gedcom_id', array_keys(get_all_gedcoms()), WT_GED_ID);
$openinnew=safe_POST_bool('openinnew'                                           );
$state    =safe_POST     ('state',     WT_REGEX_UNSAFE,              'XYZ'     );
$country  =safe_POST     ('country',   WT_REGEX_UNSAFE,              'XYZ'     );

// Must be an admin user to use this module
if (!WT_USER_GEDCOM_ADMIN) {
	header("Location: login.php?url=placelist.php");
	exit;
}
print_header(i18n::translate('Place Check').' - '.WT_GEDCOM);

$target=$openinnew ? "target='_blank'" : "";

echo "<div align=\"center\" style=\"width: 99%;\"><h1>", i18n::translate('Place Check'), "</h1></div>";

//Start of User Defined options
echo "<table border='0' width='100%' height='100px' overflow='auto';>";
echo "<form method='post' name='placecheck' action='module.php?mod=googlemap&amp;mod_action=placecheck'>";
echo "<tr valign='top'>";
echo "<td>";
echo "<table align='left'>";
echo "<tr><td colspan='2'class='descriptionbox' align='center'><strong>", i18n::translate('PlaceCheck List Options'), "</strong></td></tr>";
//Option box to select gedcom
echo "<tr><td class='descriptionbox'>".i18n::translate('GEDCOM File:')."</td>";
echo "<td class='optionbox'><select name='gedcom_id'>";
foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
	echo '<option value="', $ged_id, '"', $ged_id==$gedcom_id?' selected="selected"':'', '>', htmlspecialchars($gedcom), '</option>';
}
echo "</select></td></tr>";
//Option box for 'Open in new window'
echo "<tr><td class='descriptionbox'>".i18n::translate('Open links in')."</td>";
echo "<td class='optionbox'><select name='openinnew'>";
echo "<option value='0' ", $openinnew?" selected='selected'":"", ">".i18n::translate('Same tab/window')."</option>";
echo "<option value='1' ", $openinnew?" selected='selected'":"", ">".i18n::translate('New tab/window')."</option>";
echo "</select></td></tr>";
//Option box to select top level place within Gedcom
echo "<tr><td class='descriptionbox'>", i18n::translate('Top Level Place'), "</td>";
echo "<td class='optionbox'><select name='country'>";
echo "<option value='XYZ' selected='selected'>", i18n::translate('Select Top Level...'), "</option>";
echo "<option value='XYZ'>", i18n::translate('ALL'), "</option>";
$rows=
	WT_DB::prepare("SELECT pl_id, pl_place FROM {$TBLPREFIX}placelocation WHERE pl_level=0 ORDER BY pl_place")
	->fetchAssoc();
foreach ($rows as $id=>$place) {
	echo "<option value='{$place}'";
	if ($place==$country) {
		echo " selected='selected'";
		$par_id=$id;
	}
	echo ">{$place}</option>";
}
echo "</select></td></tr>";

//Option box to select level one place within the selected top level
if ($country!='XYZ') {
	echo "<tr><td class='descriptionbox'>", i18n::translate('Level One Place'), "</td>";
	echo "<td class='optionbox'><select name='state'>";
	echo "<option value='XYZ' selected='selected'>", i18n::translate('Select Next Level...'), "</option>";
	echo "<option value='XYZ'>", i18n::translate('ALL'), "</option>";
	$places=
		WT_DB::prepare("SELECT pl_place FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=? ORDER BY pl_place")
		->execute(array($par_id))
		->fetchOneColumn();
	foreach ($places as $place) {
		echo "<option value='{$place}'", $place==$state?" selected='selected'":"", ">{$place}</option>";
	}
	echo "</select></td></tr>";
}
echo "</table>";
echo "</td>";
//Show Filter table
if (!isset ($_POST["matching"])) {$matching=0;} else {$matching=1;}
echo "<td>";
echo "<table>";
echo "<tr><td colspan='2' class='descriptionbox' align='center'>";
echo "<strong>", i18n::translate('List filtering options'), "</strong>", help_link('PLACECHECK_FILTER','googlemap');
echo "</td></tr><tr><td class='descriptionbox'>";
echo i18n::translate('Include fully matched places: '), help_link('PLACECHECK_MATCH','googlemap');
echo "</td><td class='optionbox'><input type=\"checkbox\" name=\"matching\" value=\"active\"";
if($matching==1) {
	echo " checked=\"checked\"";
}
echo "></td></tr>";
echo "</table>";
echo "</td>";

//Show Key table
echo "<td rowspan='2'>";
echo "<table align='right'>";
echo "<tr><td colspan='4' align='center' class='descriptionbox'><strong>", i18n::translate('Key to colors used below'), "</strong></td></tr>";
echo "<tr><td class='facts_value error'>", i18n::translate('PLAC'), "</td><td class='facts_value error' align='center '><strong>X</strong></td><td align='center' class='facts_value error'><strong>X</strong></td><td class='facts_value'><font size=\"-2\">", i18n::translate('this place and its coordinates do not exist in the GoogleMap tables'), "</font></td></tr>";
echo "<tr><td class='facts_value'><a>", i18n::translate('PLAC'), "</a></td><td class='facts_value error' align='center '><strong>X</strong></td><td align='center' class='facts_value error'><strong>X</strong></td><td class='facts_value'><font size=\"-2\">", i18n::translate('this place exists in the GoogleMap tables, but has no coordinates'), "</font></td></tr>";
echo "<tr><td class='facts_value'><strong>".i18n::translate('Unknown')."</font></td><td class='facts_value error' align='center '><strong>X</strong></td><td align='center' class='facts_value error'><strong>X</strong></td><td class='facts_value'><font size=\"-2\">", i18n::translate('this place level is blank in your GEDCOM file. It should be added to<br />GoogleMap places as "unknown" with coordinates from its parent<br />level before you add any place to the next level'), "</font></td></tr>";
echo "<tr><td class='facts_value'><a>".i18n::translate('Unknown')."</a></td><td class='facts_value' align='center'>N55.0</td><td align='center' class='facts_value'>W75.0</td><td class='facts_value'><font size=\"-2\">", i18n::translate('this place level is blank in your GEDCOM file, but exists as \'unknown\'<br />in the GoogleMap places table with coordinates. No action required<br />until the missing level can be entered'), "</font></td></tr>";
echo "</table>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan='2'>";
echo "<input type='submit' value='".i18n::translate('Show')."' $target><input type='hidden' name='action' value='go'>";
echo "</td>";
echo "</tr>";
echo "</form>";
echo "</table>";
echo "<hr />";

switch ($action) {
case 'go':
	//Identify gedcom file
	echo "<strong>", i18n::translate('Place list for GEDCOM file'), ": </strong>", htmlspecialchars(get_gedcom_setting($gedcom_id, 'title')), "<br /><br />";
	//Select all '2 PLAC ' tags in the file and create array
	$place_list=array();
	$ged_data=WT_DB::prepare("SELECT i_gedcom FROM {$TBLPREFIX}individuals WHERE i_gedcom LIKE ? AND i_file=?")
		->execute(array("%\n2 PLAC %", $gedcom_id))
		->fetchOneColumn();
	foreach ($ged_data as $ged_datum) {
		preg_match_all('/\n2 PLAC (.+)/', $ged_datum, $matches);
		foreach ($matches[1] as $match) {
			$place_list[$match]=true;
		}
	}
	$ged_data=WT_DB::prepare("SELECT f_gedcom FROM {$TBLPREFIX}families WHERE f_gedcom LIKE ? AND f_file=?")
		->execute(array("%\n2 PLAC %", $gedcom_id))
		->fetchOneColumn();
	foreach ($ged_data as $ged_datum) {
		preg_match_all('/\n2 PLAC (.+)/', $ged_datum, $matches);
		foreach ($matches[1] as $match) {
			$place_list[$match]=true;
		}
	}
	// Unique list of places
	$place_list=array_keys($place_list);

	// Apply_filter
	if ($country=='XYZ') {
		$filter='.*$';
	} else {
		$filter=preg_quote($country).'$';
		if ($state!='XYZ') {
			$filter=preg_quote($state).', '.$filter;
		}
	}
	$place_list=preg_grep('/'.$filter.'/', $place_list);
	
	//sort the array, limit to unique values, and count them
	$place_parts=array();
	usort($place_list, "utf8_strcasecmp");
	$i=count($place_list);
	
	//calculate maximum no. of levels to display
	$x=0;
	$max=0;
	while ($x<$i) {
		$levels=explode(",", $place_list[$x]);
		$parts=count($levels);
		if ($parts>$max) $max=$parts;
	$x++;}
	$x=0;
	
	//scripts for edit, add and refresh
	?>
	<script language="JavaScript" type="text/javascript">
	<!--
	function edit_place_location(placeid) {
		window.open('module.php?mod=googlemap&mod_action=places_edit&action=update&placeid='+placeid+"&"+sessionname+"="+sessionid, '_blank', 'top=50, left=50, width=680, height=550, resizable=1, scrollbars=1');
		return false;
	}
	
	function add_place_location(placeid) {
		window.open('module.php?mod=googlemap&mod_action=places_edit&action=add&placeid='+placeid+"&"+sessionname+"="+sessionid, '_blank', 'top=50, left=50, width=680, height=550, resizable=1, scrollbars=1');
		return false;
	}
	function showchanges() {
		window.location='<?php echo $_SERVER["REQUEST_URI"]; ?>&show_changes=yes';
	}
	//-->
	</script>
	<?php
	
	//start to produce the display table
	$cols=0;
	$span=$max*3+3;
	echo "<table class='facts_table' width='100%'><tr>";
	echo "<td rowspan='3' class='descriptionbox' align='center'><strong>", i18n::translate('GEDCOM File Place Data<br />(2 PLAC tag)'), "</strong></td>";
	echo "<td class='descriptionbox' colspan='", $span, "' align='center'><strong>", i18n::translate('GoogleMap Places Table Data'), "</strong></td></tr>";
	echo "<tr>";
	while ($cols<$max) {
		echo "<td class='descriptionbox' colspan='3' align='center'><strong>", PrintReady(i18n::translate('Level')), "&nbsp;", $cols, "</strong></td>";
		$cols++;
	}
	echo "</tr><tr>";
	$cols=0;
	while ($cols<$max) {
		echo "<td class='descriptionbox' align='center'><strong>", i18n::translate('PLAC'), "</strong></td><td class='descriptionbox' align='center'><strong>", i18n::translate('Latitude'), "</strong><td class='descriptionbox' align='center'><strong>", i18n::translate('Longitude'), "</strong></td></td>";
		$cols++;
	}
	echo "</tr>";
	$countrows=0;
	while ($x<$i) {
		$placestr="";
		$levels=explode(",", $place_list[$x]);
		$parts=count($levels);
		$levels=array_reverse($levels);
		$placestr.="<a href=\"placelist.php?action=show&amp;";
		foreach($levels as $pindex=>$ppart) {
			$ppart=urlencode(trim($ppart));
			$placestr.="parent[$pindex]=".$ppart."&amp;";
		}
		$placestr.="level=".count($levels);
		$placestr.="\">".$place_list[$x]."</a>";
		$gedplace="<tr><td class='facts_value'>".$placestr."</td>";
		$z=0;
		$y=0;
		$id=0;
		$level=0;
		$matched[$x]=0;// used to exclude places where the gedcom place is matched at all levels
		$mapstr_edit="<a href=\"javascript:;\" onclick=\"edit_place_location('";
		$mapstr_add="<a href=\"javascript:;\" onclick=\"add_place_location('";
		$mapstr3="";
		$mapstr4="";
		$mapstr5="')\" title='";
		$mapstr6="' >";
		$mapstr7="')\">";
		$mapstr8="</a>";
		while ($z<$parts) {
			if ($levels[$z]==' ' || $levels[$z]=='')
				$levels[$z]="unknown";// GoogleMap module uses "unknown" while GEDCOM uses , ,
	
			$levels[$z]=rtrim(ltrim($levels[$z]));
	
			$placelist=create_possible_place_names($levels[$z], $z+1); // add the necessary prefix/postfix values to the place name
			foreach ($placelist as $key=>$placename) {
				$row=
					WT_DB::prepare("SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom FROM {$TBLPREFIX}placelocation WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
					->execute(array($z, $id, $placename))
					->fetchOneRow(PDO::FETCH_ASSOC);
				if (!empty($row['pl_id'])) {
					$row['pl_placerequested']=$levels[$z]; // keep the actual place name that was requested so we can display that instead of what is in the db
					break;
				}
			}
			if ($row['pl_id']!='') {
				$id=$row['pl_id'];
			}
	
			if ($row['pl_place']!='') {
				$placestr2=$mapstr_edit.$id."&amp;level=".$level.$mapstr3.$mapstr5.i18n::translate('Zoom=').$row['pl_zoom'].$mapstr6.$row['pl_placerequested'].$mapstr8;
				if ($row['pl_place']=='unknown')
					$matched[$x]++;
			} else {
				if ($levels[$z]=="unknown") {
					$placestr2=$mapstr_add.$id."&amp;level=".$level.$mapstr3.$mapstr7."<strong>".rtrim(ltrim(i18n::translate('Unknown')))."</strong>".$mapstr8;$matched[$x]++;
				} else {
					$placestr2=$mapstr_add.$id."&amp;place_name=".urlencode($levels[$z])."&amp;level=".$level.$mapstr3.$mapstr7.'<span class="error">'.rtrim(ltrim($levels[$z])).'</span>'.$mapstr8;$matched[$x]++;
				}
			}
			$plac[$z]="<td class='facts_value'>".$placestr2."</td>\n";
			if ($row['pl_lati']=='0'){
				$lati[$z]="<td class='facts_value error'><strong>".$row['pl_lati']."</strong></td>";
			} else if ($row['pl_lati']!='') {
				$lati[$z]="<td class='facts_value'>".$row['pl_lati']."</td>";
			} else {
				$lati[$z]="<td class='facts_value error' align='center'><strong>X</strong></td>";$matched[$x]++;
			}
			if ($row['pl_long']=='0'){
				$long[$z]="<td class='facts_value error'><strong>".$row['pl_long']."</strong></td>";
			} else if ($row['pl_long']!='') {
				$long[$z]="<td class='facts_value'>".$row['pl_long']."</td>";
			} else {
				$long[$z]="<td class='facts_value error' align='center'><strong>X</strong></td>";$matched[$x]++;
			}
			$level++;
			$mapstr3=$mapstr3."&amp;parent[".$z."]=".addslashes(PrintReady($row['pl_placerequested']));
			$mapstr4=$mapstr4."&amp;parent[".$z."]=".addslashes(PrintReady(rtrim(ltrim($levels[$z]))));
			$z++;
		}
		if ($matching==1) {
			$matched[$x]=1;
		}
		if ($matched[$x]!=0) {
			echo $gedplace;
			$z=0;
			while ($z<$max) {
			if ($z<$parts) {
				echo $plac[$z];
				echo $lati[$z];
				echo $long[$z];
			} else {
				echo "<td class='facts_value'>&nbsp;</td><td class='facts_value'>&nbsp;</td><td class='facts_value'>&nbsp;</td>";}
				$z++;
			}
			echo "</tr>";
			$countrows++;
		}
		$x++;
	}
	
	// echo final row of table
	echo "<tr><td colspan=\"2\" class=\"list_label\">", i18n::translate('Total unique places'), ": ", $countrows, "</td></tr></table><br /><br />";
	break;	
default:
	// Do not run until user selects a gedcom/place/etc.
	// Instead, show some useful help info.
	echo "<p>", i18n::translate('This will list all the places from the selected GEDCOM file. By default this will NOT INCLUDE places that are fully matched between the GEDCOM file and the GoogleMap tables'), "</p><hr />";
	break;	
}

//echo footers
echo "<hr />";
print_footer();
?>
