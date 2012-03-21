<?php
// Provides a way to compare places in your family tree file with the matching
// entries in the Google Maps™ 'placelocations' table.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License or,
// at your discretion, any later version.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

$action   =safe_POST     ('action'                                              );
$gedcom_id=safe_POST     ('gedcom_id', array_keys(get_all_gedcoms()), WT_GED_ID );
$country  =safe_POST     ('country',   WT_REGEX_UNSAFE,              ''         );
if (!$country) {
	// allow placelist to link directly to a specific country/state
	$country=safe_GET    ('country',   WT_REGEX_UNSAFE,              'XYZ'      );
}
$state    =safe_POST     ('state',     WT_REGEX_UNSAFE,              ''         );
if (!$state) {
	$state=safe_GET      ('state',     WT_REGEX_UNSAFE,              'XYZ'      );
}
if (isset($_REQUEST['show_changes']) && $_REQUEST['show_changes']=='yes') {
	$show_changes = true;
} else {
	$show_changes = false;
}

if ($show_changes && !empty($_SESSION['placecheck_gedcom_id'])) {
	$gedcom_id = $_SESSION['placecheck_gedcom_id'];
} else {
	$_SESSION['placecheck_gedcom_id'] = $gedcom_id;
}
if ($show_changes && !empty($_SESSION['placecheck_country'])) {
	$country = $_SESSION['placecheck_country'];
} else {
	$_SESSION['placecheck_country'] = $country;
}
if ($show_changes && !empty($_SESSION['placecheck_state'])) {
	$state = $_SESSION['placecheck_state'];
} else {
	$_SESSION['placecheck_state'] = $state;
}

$controller=new WT_Controller_Base();
$controller
	->requireAdminLogin()
	->setPageTitle(WT_I18N::translate('Google Maps™'))
	->pageHeader();

?>
<table id="gm_config">
	<tr>
		<th>
			<a href="module.php?mod=googlemap&amp;mod_action=admin_editconfig">
				<?php echo WT_I18N::translate('Google Maps™ preferences'); ?>
			</a>
		</th>
		<th>
			<a href="module.php?mod=googlemap&amp;mod_action=admin_places">
				<?php echo WT_I18N::translate('Geographic data'); ?>
			</a>
		</th>
		<th>
			<a class="current" href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">
				<?php echo WT_I18N::translate('Place Check'); ?>
			</a>
		</th>
	</tr>
</table>

<?php

//Start of User Defined options
echo '<table id="gm_check_outer">';
echo '<form method="post" name="placecheck" action="module.php?mod=googlemap&amp;mod_action=admin_placecheck">';
echo '<tr valign="top">';
echo '<td>';
echo '<table class="gm_check_top" align="left">';
echo '<tr><th colspan="2">', WT_I18N::translate('PlaceCheck List Options'), '</th></tr>';
//Option box to select gedcom
echo '<tr><td>', WT_I18N::translate('Family tree'), '</td>';
echo '<td><select name="gedcom_id">';
foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
	echo '<option value="', $ged_id, '"', $ged_id==$gedcom_id?' selected="selected"':'', '>', get_gedcom_setting($ged_id, 'title'), '</option>';
}
echo '</select></td></tr>';
//Option box to select Country within Gedcom
echo '<tr><td>', WT_I18N::translate('Country'), '</td>';
echo '<td><select name="country">';
echo '<option value="XYZ" selected="selected">', /* I18N: first/default option in a drop-down listbox */ WT_I18N::translate('&lt;select&gt;'), '</option>';
echo '<option value="XYZ">', WT_I18N::translate('All'), '</option>';
$rows=
	WT_DB::prepare("SELECT pl_id, pl_place FROM `##placelocation` WHERE pl_level=0 ORDER BY pl_place")
	->fetchAssoc();
foreach ($rows as $id=>$place) {
	echo '<option value="', $place, '"';
	if ($place==$country) {
		echo ' selected="selected"';
		$par_id=$id;
	}
	echo '>', $place, '</option>';
}
echo '</select></td></tr>';

//Option box to select level 2 place within the selected Country
if ($country!='XYZ') {
	echo '<tr><td>', /* I18N: Part of a country, state/region/county */ WT_I18N::translate('Subdivision'), '</td>';
	echo '<td><select name="state">';
	echo '<option value="XYZ" selected="selected">', WT_I18N::translate('&lt;select&gt;'), '</option>';
	echo '<option value="XYZ">', WT_I18N::translate('All'), '</option>';
	$places=
		WT_DB::prepare("SELECT pl_place FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place")
		->execute(array($par_id))
		->fetchOneColumn();
	foreach ($places as $place) {
		echo '<option value="', $place, '"', $place==$state?' selected="selected"':'', '>', $place, '</option>';
	}
	echo '</select></td></tr>';
}
echo '</table>';
echo '</td>';
//Show Filter table
if (!isset ($_POST['matching'])) {$matching=false;} else {$matching=true;}
echo '<td>';
echo '<table class="gm_check_top"  align="center">';
echo '<tr><th colspan="2">';
echo WT_I18N::translate('List filtering options');
echo '</th></tr><tr><td>';
echo WT_I18N::translate('Include fully matched places: ');
echo '</td><td><input type="checkbox" name="matching" value="active"';
if ($matching) {
	echo ' checked="checked"';
}
if ($show_changes) {
	$action = 'go';
}
echo '></td></tr>';
echo '</table>';
echo '</td>';
echo '<td>';
echo '<input type="submit" value="', WT_I18N::translate('Show'), '"><input type="hidden" name="action" value="go">';
echo '</td>';
echo '</tr>';
echo '</form>';
echo '</table>';
echo '<hr>';

switch ($action) {
case 'go':
	//Identify gedcom file
	echo '<div id="gm_check_title"><span>', htmlspecialchars(get_gedcom_setting($gedcom_id, 'title')), '</span></div>';
	//Select all '2 PLAC ' tags in the file and create array
	$place_list=array();
	$ged_data=WT_DB::prepare("SELECT i_gedcom FROM `##individuals` WHERE i_gedcom LIKE ? AND i_file=?")
		->execute(array("%\n2 PLAC %", $gedcom_id))
		->fetchOneColumn();
	foreach ($ged_data as $ged_datum) {
		preg_match_all('/\n2 PLAC (.+)/', $ged_datum, $matches);
		foreach ($matches[1] as $match) {
			$place_list[$match]=true;
		}
	}
	$ged_data=WT_DB::prepare("SELECT f_gedcom FROM `##families` WHERE f_gedcom LIKE ? AND f_file=?")
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
	<script type="text/javascript">
	<!--
	function edit_place_location(placeid) {
		window.open('module.php?mod=googlemap&mod_action=places_edit&action=update&placeid='+placeid, '_blank', indx_window_specs);
		return false;
	}

	function add_place_location(placeid) {
		window.open('module.php?mod=googlemap&mod_action=places_edit&action=add&placeid='+placeid, '_blank', indx_window_specs);
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
	echo '<div class="gm_check_details">';
	echo '<table class="gm_check_details"><tr>';
	echo '<th rowspan="3">', WT_I18N::translate('Place'), '</th>';
	echo '<th colspan="', $span, '">', WT_I18N::translate('Geographic data'), '</th></tr>';
	echo '<tr>';
	while ($cols<$max) {
		if ($cols == 0) {
			echo '<th colspan="3">', WT_I18N::translate('Country'), '</th>';
		} else {
			echo '<th colspan="3">', WT_I18N::translate('Level'), '&nbsp;', $cols+1, '</th>';
		}
		$cols++;
	}
	echo '</tr><tr>';
	$cols=0;
	while ($cols<$max) {
		echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th><th>', WT_I18N::translate('Latitude'), '</th><th>', WT_I18N::translate('Longitude'), '</th></td>';
		$cols++;
	}
	echo '</tr>';
	$countrows=0;
	while ($x<$i) {
		$placestr="";
		$levels=explode(",", $place_list[$x]);
		$parts=count($levels);
		$levels=array_reverse($levels);
		$placestr.="<a href=\"placelist.php?action=show&amp;";
		foreach ($levels as $pindex=>$ppart) {
			$ppart=urlencode(trim($ppart));
			$placestr.="parent[$pindex]=".$ppart."&amp;";
		}
		$placestr.="level=".count($levels);
		$placestr.="\">".$place_list[$x]."</a>";
		$gedplace="<tr><td>".$placestr."</td>";
		$z=0;
		$y=0;
		$id=0;
		$level=0;
		$matched[$x]=0;// used to exclude places where the gedcom place is matched at all levels
		$mapstr_edit="<a href=\"#\" onclick=\"edit_place_location('";
		$mapstr_add="<a href=\"#\" onclick=\"add_place_location('";
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
					WT_DB::prepare("SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
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
				$placestr2=$mapstr_edit.$id."&amp;level=".$level.$mapstr3.$mapstr5.WT_I18N::translate('Zoom=').$row['pl_zoom'].$mapstr6.$row['pl_placerequested'].$mapstr8;
				if ($row['pl_place']=='unknown')
					$matched[$x]++;
			} else {
				if ($levels[$z]=="unknown") {
					$placestr2=$mapstr_add.$id."&amp;level=".$level.$mapstr3.$mapstr7."<strong>".rtrim(ltrim(WT_I18N::translate('unknown')))."</strong>".$mapstr8;$matched[$x]++;
				} else {
					$placestr2=$mapstr_add.$id."&amp;place_name=".urlencode($levels[$z])."&amp;level=".$level.$mapstr3.$mapstr7.'<span class="error">'.rtrim(ltrim($levels[$z])).'</span>'.$mapstr8;$matched[$x]++;
				}
			}
			$plac[$z]="<td>".$placestr2."</td>\n";
			if ($row['pl_lati']=='0') {
				$lati[$z]="<td class='error'><strong>".$row['pl_lati']."</strong></td>";
			} else if ($row['pl_lati']!='') {
				$lati[$z]="<td>".$row['pl_lati']."</td>";
			} else {
				$lati[$z]="<td class='error' align='center'><strong>X</strong></td>";$matched[$x]++;
			}
			if ($row['pl_long']=='0') {
				$long[$z]="<td class='error'><strong>".$row['pl_long']."</strong></td>";
			} else if ($row['pl_long']!='') {
				$long[$z]="<td>".$row['pl_long']."</td>";
			} else {
				$long[$z]="<td class='error' align='center'><strong>X</strong></td>";$matched[$x]++;
			}
			$level++;
			$mapstr3=$mapstr3."&amp;parent[".$z."]=".addslashes($row['pl_placerequested']);
			$mapstr4=$mapstr4."&amp;parent[".$z."]=".addslashes(rtrim(ltrim($levels[$z])));
			$z++;
		}
		if ($matching) {
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
				echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";}
				$z++;
			}
			echo "</tr>";
			$countrows++;
		}
		$x++;
	}

	// echo final row of table
	echo "<tr><td colspan=\"2\" class=\"accepted\">", /* I18N: A count of places */ WT_I18N::translate('Total places: %s', WT_I18N::number($countrows)), "</td></tr></table></div>";
	break;
default:
	// Do not run until user selects a gedcom/place/etc.
	// Instead, show some useful help info.
	echo "<div class=\"gm_check_top accepted\">", WT_I18N::translate('This will list all the places from the selected GEDCOM file. By default this will NOT INCLUDE places that are fully matched between the GEDCOM file and the GoogleMap tables'), "</div>";
	break;
}
