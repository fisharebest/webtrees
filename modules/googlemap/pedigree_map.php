<?php
// Print pedigree map using Googlemaps.
// It requires that your place coordinates are stored on the Google Map
// 'place_locations' table. It will NOT find coordinates stored only as tags in
// your GEDCOM file. As in the Google Maps module, it can only display place
// markers where the location exists with identical spelling in both your
// GEDCOM '2 PLAC' tag (within the '1 BIRT' event) and the place_locations table.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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

require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';

global $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $ENABLE_AUTOCOMPLETE, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $WT_IMAGES;

// Default is show for both of these.
$hideflags = safe_GET('hideflags');
$hidelines = safe_GET('hidelines');

$controller = new WT_Controller_Pedigree();
$controller->init();

// Default of 5
$clustersize = 5;
if (!empty($_REQUEST['clustersize'])) {
	if ($_REQUEST['clustersize'] == '3')
		$clustersize = 3;
	else if ($_REQUEST['clustersize'] == '1')
		$clustersize = 1;
}

// Start of internal configuration variables

// Limit this to match available number of icons.
// 8 generations equals 255 individuals
$MAX_PEDIGREE_GENERATIONS = min($MAX_PEDIGREE_GENERATIONS, 8);

// End of internal configuration variables

global $TEXT_DIRECTION;

// -- print html header information
print_header($controller->getPersonName().' - '.WT_I18N::translate('Pedigree Map'));

if (!$GOOGLEMAP_ENABLED) {
	echo "<table class=\"facts_table\">\n";
	echo "<tr><td class=\"facts_value\">", WT_I18N::translate('GoogleMap module disabled'), "</td></tr>\n";
	if (WT_USER_IS_ADMIN) {
		echo "<tr><td align=\"center\">\n";
		echo "<a href=\"module.php?mod=googlemap&mod_action=admin_editconfig\">", WT_I18N::translate('Google Maps configuration'), "</a>";
		echo "</td></tr>\n";
	}
	echo "</table><br />";
	print_footer();
	return;
}

echo '<link type="text/css" href ="', WT_MODULES_DIR, 'googlemap/css/googlemap_style.css" rel="stylesheet" />';

if ($ENABLE_AUTOCOMPLETE) require WT_ROOT.'js/autocomplete.js.htm';
echo '<div><table><tr><td valign="middle">';
echo "<h2>" . WT_I18N::translate('Pedigree Map') . " " . WT_I18N::translate('for') . " ";
echo PrintReady($controller->getPersonName())."</h2>";

// -- print the form to change the number of displayed generations
?>
<script type="text/javascript">
	<!--
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
	//-->
</script>
</td><td width="50px">&nbsp;</td><td>
	  <form name="people" method="get" action="module.php?ged=<?php echo WT_GEDURL; ?>&amp;mod=googlemap&amp;mod_action=pedigree_map">
		<input type="hidden" name="mod" value="googlemap" />
		<input type="hidden" name="mod_action" value="pedigree_map" />
		<table class="pedigree_table <?php echo $TEXT_DIRECTION; ?>" width="555">
			<tr>
				<td colspan="5" class="topbottombar" style="text-align:center; ">
					<?php echo WT_I18N::translate('Pedigree Map Options'); ?>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Root Person ID'), help_link('rootid'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Generations'), help_link('PEDIGREE_GENERATIONS'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php echo WT_I18N::translate('Cluster size'), help_link('PEDIGREE_MAP_clustersize','googlemap'); ?>
				</td>
				<td class="descriptionbox wrap">
					<?php
					echo WT_I18N::translate('Hide flags'), help_link('PEDIGREE_MAP_hideflags','googlemap');
					?>
				</td>
				<td class="descriptionbox wrap">
					<?php
					echo WT_I18N::translate('Hide lines'), help_link('PEDIGREE_MAP_hidelines','googlemap');
					?>
				</td>
			</tr>
			<tr>
				<td class="optionbox">
					<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->rootid; ?>" />
					<?php print_findindi_link("rootid",""); ?>
				</td>
				<td class="optionbox">
					<select name="PEDIGREE_GENERATIONS">
					<?php
						for ($p=3; $p<=$MAX_PEDIGREE_GENERATIONS; $p++) {
							echo "<option value=\"".$p."\" " ;
							if ($p == $controller->PEDIGREE_GENERATIONS) echo "selected=\"selected\" ";
							echo ">".$p."</option>";
						}
					?>
					</select>
				</td>
				<td class="optionbox">
					<select name="clustersize">
					<?php
						for ($p=1; $p<6; $p = $p+2) {
							echo "<option value=\"".$p."\" " ;
							if ($p == $clustersize) echo "selected=\"selected\" ";
							echo ">".$p."</option>";
						}
					?>
					</select>
				</td>
				<td class="optionbox">
					<?php
					echo "<input name=\"hideflags\" type=\"checkbox\"";
					if ($hideflags) {echo " checked=\"checked\"";}
						echo " />";
					?>
				</td>
				<td class="optionbox">
					<?php
					echo "<input name=\"hidelines\" type=\"checkbox\"";
					if ($hidelines) {echo " checked=\"checked\"";}
					echo " />";
					?>
				</td>
			</tr>
			<tr>
				<td class="topbottombar" colspan="5">
					<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>" />
				</td>
			</tr>
		</table>
	  </form>
	</td></tr>
</table>

<!-- end of form -->

<!-- count records by type -->
<?php
$curgen=1;
$priv=0;
$count=0;
$miscount=0;
$missing = "";

for ($i=0; $i<($controller->treesize); $i++) {
	// -- check to see if we have moved to the next generation
	if ($i+1 >= pow(2, $curgen)) {$curgen++;}
	$person = WT_Person::getInstance($controller->treeid[$i]);
	if (!empty($person)) {
		$pid = $controller->treeid[$i];
		$name = $person->getFullName();
		if ($name == WT_I18N::translate('Private')) $priv++;
		$place = $person->getBirthPlace();
		if (empty($place)) {
			$latlongval[$i] = NULL;
		} else {
			$latlongval[$i] = get_lati_long_placelocation($person->getBirthPlace());
			if ($latlongval[$i] != NULL && $latlongval[$i]["lati"]=='0' && $latlongval[$i]["long"]=='0') {
				$latlongval[$i] = NULL;
			}
		}
		if ($latlongval[$i] != NULL) {
			$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]["lati"]);
			$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]["long"]);
			if (($lat[$i] != NULL) && ($lon[$i] != NULL)) {
				$count++;
			}
			else { // The place is in the table but has empty values
				if (!empty($name)) {
					if (!empty($missing)) $missing .= ",\n ";
					$addlist = '<a href="'.$person->getHtmlUrl().'">'. $name . '</a>';
					$missing .= $addlist;
					$miscount++;
				}
			}
		}
		else { // There was no place, or not listed in the map table
			if (!empty($name)) {
				if (!empty($missing)) $missing .= ",\n ";
				$addlist = '<a href="'.$person->getHtmlUrl().'">'. $name . '</a>';
				$missing .= $addlist;
				$miscount++;
			}
		}
	}
}
//<!-- end of count records by type -->

//<!-- start of map display -->
echo '<table class="tabs_table" cellspacing="0" cellpadding="0" border="0" width="100%">';
echo "<tr>\n";
echo "<td valign=\"top\">\n";
echo "<img src=\"images/spacer.gif\" width=\"".$GOOGLEMAP_XSIZE."\" height=\"0\" alt=\"\" border=\"0\"/>\n";
echo "<div id=\"pm_map\" style=\"border: 1px solid gray; height: ".$GOOGLEMAP_YSIZE."px; font-size: 0.9em;";
echo " background-image: url('images/loading.gif'); background-position: center; background-repeat: no-repeat; overflow: hidden;\"></div>\n";
if (WT_USER_IS_ADMIN) {
	echo "<table width=\"100%\">";
	echo "<tr><td align=\"left\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=admin_editconfig\">", WT_I18N::translate('Google Maps configuration'), "</a>";
	echo "</td>\n";
	echo "<td align=\"center\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=admin_places\">", WT_I18N::translate('Edit geographic place locations'), "</a>";
	echo "</td>\n";
	echo "<td align=\"right\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=admin_placecheck\">", WT_I18N::translate('Place Check'), "</a>";
	echo "</td></tr>\n";
	echo "</table>\n";
}
echo "</td><td width=\"15px\">&nbsp;</td>\n";
echo "<td width=\"310px\" valign=\"top\">\n";
echo "<div id=\"side_bar\" style=\"width: 300px; font-size: 0.9em; overflow: auto; overflow-x: hidden; overflow-y: auto; height: ".$GOOGLEMAP_YSIZE."px; \"></div></td>\n";
echo "</tr>\n";
echo "</table>\n";
// display info under map
echo "<hr />";
echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">";
echo "  <tr>";
echo " <td valign=\"top\">";
// print summary statistics
if (isset($curgen)) {
	$total=pow(2,$curgen)-1;
	$miss=$total-$count-$priv;
	echo WT_I18N::plural(
		'%1$d individual displayed, out of the normal total of %2$d, from %3$d generations.',
		'%1$d individuals displayed, out of the normal total of %2$d, from %3$d generations.',
		$count,
		$count, $total, $curgen
	), '<br/>';
	echo "</td>\n";
	echo "  </tr>\n";
	echo "  <tr>\n";
	echo " <td valign=\"top\">\n";
	if ($priv) {
		echo WT_I18N::plural('%s individual is private.', '%s individuals are private.', $priv, $priv), '<br/>';
	}
	if ($count+$priv != $total) {
		if ($miscount == 0) {
			echo WT_I18N::translate('No ancestors in the database.'), "<br />\n";
		} else {
			// I18N: %1$d is a count of individuals, %2$s is a list of their names
			echo " ".WT_I18N::plural(
				'%1$d individual is missing birthplace map coordinates: %2$s.',
				'%1$d individuals are missing birthplace map coordinates: %2$s.',
				$miscount, $miscount, $missing),
				'<br />';
		}
	}
}
echo " </td>\n";
echo "  </tr>\n";
echo "</table>\n";
echo "</div>";
?>
<!-- end of map display -->

<!-- Start of map scripts -->
<?php
echo '<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>';
require_once WT_ROOT.WT_MODULES_DIR.'googlemap/wt_v3_pedigree_map.js.php';

print_footer();
