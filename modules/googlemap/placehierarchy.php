<?php
/**
 * Displays a place hierachy
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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
 * @subpackage Googlemap
 * @author Brian Holland (for v3 googlemaps version at webtrees)
 *
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'modules/googlemap/googlemap.php';
if (file_exists(WT_ROOT.'modules/googlemap/defaultconfig.php')) {
	require WT_ROOT.'modules/googlemap/defaultconfig.php';
}

$stats = new WT_Stats($GEDCOM);

function check_exist_table() {
	return WT_DB::table_exists("##placelocation");
}


function get_place_list_loc($parent_id, $inactive=false) {
	global $display;
	if ($inactive) {
		$rows=
			WT_DB::prepare("SELECT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place")
			->execute(array($parent_id))
			->fetchAll();
	} else {
		$rows=
			WT_DB::prepare(
				"SELECT DISTINCT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon".
				" FROM `##placelocation`".
				" INNER JOIN `##places` ON `##placelocation`.pl_place=`##places`.p_place AND `##placelocation`.pl_level=`##places`.p_level".
				" WHERE pl_parent_id=? ORDER BY pl_place"
			)
			->execute(array($parent_id))
			->fetchAll();
	}

	$placelist=array();
	foreach ($rows as $row) {
		$placelist[]=array("place_id"=>$row->pl_id, "place"=>$row->pl_place, "lati"=>$row->pl_lati, "long"=>$row->pl_long, "zoom"=>$row->pl_zoom, "icon"=>$row->pl_icon);
	}
	return $placelist;
}

function place_id_to_hierarchy($id) {
	$statement=
		WT_DB::prepare("SELECT pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=?");
	$arr=array();
	while ($id!=0) {
		$row=$statement->execute(array($id))->fetchOneRow();
		$arr=array($id=>$row->pl_place)+$arr;
		$id=$row->pl_parent_id;
	}
	return $arr;
}

function get_placeid($place) {
	$par = explode (",", $place);
	$par = array_reverse($par);
	$place_id = 0;
	if (check_exist_table()) {
		for ($i=0; $i<count($par); $i++) {
			$par[$i] = trim($par[$i]);
			if (empty($par[$i])) $par[$i]="unknown";
			$placelist = create_possible_place_names($par[$i], $i+1);
			foreach ($placelist as $key => $placename) {
				$pl_id=
					WT_DB::prepare("SELECT pl_id FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
					->execute(array($i, $place_id, $placename))
					->fetchOne();
				if (!empty($pl_id)) break;
			}
			if (empty($pl_id)) break;
			$place_id = $pl_id;
		}
	}
	return $place_id;
}

function get_p_id($place) {
	$par = explode (",", $place);
	$par = array_reverse($par);
	$place_id = 0;
	for ($i=0; $i<count($par); $i++) {
		$par[$i] = trim($par[$i]);
		$placelist = create_possible_place_names($par[$i], $i+1);
		foreach ($placelist as $key => $placename) {
			$pl_id=
				WT_DB::prepare("SELECT p_id FROM `##places` WHERE p_level=? AND p_parent_id=? AND p_file=? AND p_place LIKE ? ORDER BY p_place")
				->execute(array($i, $place_id, WT_GED_ID, $placename))
				->fetchOne();
			if (!empty($pl_id)) break;
		}
		if (empty($pl_id)) break;
		$place_id = $pl_id;
	}
	return $place_id;
}

function set_placeid_map($level, $parent) {
	if (!isset($levelm)) {
		$levelm=0;
	}
	$fullplace = "";
	if ($level==0)
		$levelm=0;
	else {
		for ($i=1; $i<=$level; $i++) {
			$fullplace .= $parent[$level-$i].", ";
		}
		$fullplace = substr($fullplace, 0, -2);
		$levelm = get_p_id($fullplace);
	}
	return $levelm;
}

function set_levelm($level, $parent) {
	if (!isset($levelm)) {
		$levelm=0;
	}
	$fullplace = "";
	if ($level==0)
		$levelm=0;
	else {
		for ($i=1; $i<=$level; $i++) {
			if ($parent[$level-$i]!="")
				$fullplace .= $parent[$level-$i].", ";
			else
				$fullplace .= "Unknown, ";
		}
		$fullplace = substr($fullplace, 0, -2);
		$levelm = get_placeid($fullplace);
	}
	return $levelm;
}

function create_map() {
	$level = safe_GET('level');
	global $GOOGLEMAP_PH_XSIZE, $GOOGLEMAP_PH_YSIZE, $GOOGLEMAP_MAP_TYPE, $TEXT_DIRECTION, $levelm;
	
	// *** ENABLE STREETVIEW *** (boolean) =========================================================
	$STREETVIEW = get_module_setting('googlemap', 'GM_USE_STREETVIEW');
	// =============================================================================================
	$parent = safe_GET('parent');
	
	// create the map
	if ($level > 0) {
		echo "<p><table class=\" center\" style=\"margin-top:0px;\"><tr valign=\"top\"><td style=\"background:none;\">", WT_I18N::translate('The red markers show the Place Hierarchy defined after:'), " - ", $parent[$level-1], "</p>";
	}else {
		echo "<p><table class=\" center\" style=\"margin-top:0px;\"><tr valign=\"top\"><td style=\"background:none;\"></p>";
	}
	//<!-- start of map display -->
	echo "\n<br /><br />\n";
	echo "<table style=\"margin-top:-32px;\"><tr valign=\"top\">";
	if ($level>=1) {
		echo "<td class=\"center\" width=\"200px\" style=\"background:none; padding-top:26px; padding-bottom:0px;\">";
	} else {
		echo "<td class=\"center\" width=\"200px\" style=\"padding-top:6px;\">";	
	}
	
	$parent[$level-1] = PrintReady(addslashes($parent[$level-1]));	
	$latlng = WT_DB::prepare("SELECT pl_id, pl_lati, pl_long, pl_zoom, sv_long, sv_lati, sv_bearing, sv_elevation, sv_zoom FROM ##placelocation WHERE pl_place='{$parent[$level-1]}'")->fetchAll(PDO::FETCH_ASSOC);
	
	if (!isset($latlng[0])) {
	
	} else {
	
		if ($latlng[0]['sv_lati']==null && WT_USER_IS_ADMIN && $STREETVIEW) {		
			?>
			<style>
  			#warning {
    			margin: 0 auto;
    			width: 520px;
    			height: 100px;
				text-align: left;
				margin-top:-5px;
  				padding-bottom: 140px; 
  			}
  			#warning h5 {
  				font: 12px verdana; color:red;
  				font-weight: normal;
  			}
  			</style>
			<div id="warning">
			<h5>
				<br /><br /><br />
			<?php	
			echo WT_I18n::translate('
				<b>No Streetview coordinates are saved yet.</b><br />
				<br />
				a. 	If no Streetview is displayed in the pane below right,  <br />&nbsp;&nbsp;&nbsp;
					drag the "Pegman" in the Map pane to the right to a "blue" Street on the map. <br />
				b. 	When the Streetview is displayed, adjust as necessary to enable the required view. <br />
					&nbsp;&nbsp;&nbsp; (Right mouse click the "Steetview" pane to toggle Street view navigation arrows.) <br /> 
				c. 	When the required view is displayed, click the button "Save View".
		    ');
			?>
			</h5>
			</div>
			<?php
		}
		
	}
	
	echo "<div id=\"place_map\" style=\"border: 1px solid gray; width: ", $GOOGLEMAP_PH_XSIZE, "px; height: ", $GOOGLEMAP_PH_YSIZE, "px; ";
	echo "background-image: url('images/loading.gif'); background-position: center; background-repeat: no-repeat; overflow: hidden;\"></div>";

	echo '<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>';
	echo '<script src="modules/googlemap/wt_v3_googlemap.js" type="text/javascript"></script>';

	echo "</td>";

	if (WT_USER_IS_ADMIN) {
	echo "</tr><tr><td>";
		echo "<table style=\"width:", $GOOGLEMAP_PH_XSIZE, "px; margin-top:0px; background:none;\" >";
		echo "<tr><td align=\"left\" style=\"margin-top:0px; \">\n";
	echo "<a href=\"module.php?mod=googlemap&amp;mod_action=admin_editconfig\">", WT_I18N::translate('Google Maps configuration'), "</a>";
		echo "</td>\n";
		echo "<td align=\"center\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=admin_places\">", WT_I18N::translate('Edit geographic place locations'), "</a>";
		echo "</td>\n";
		echo "<td align=\"right\">\n";
	echo "<a href=\"module.php?mod=googlemap&mod_action=admin_placecheck\">", WT_I18N::translate('Place Check'), "</a>";
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	
	echo "</tr></table>";

	echo "</td>";
	echo "<td style=\"margin-left:15; padding-top: 7px; float:right; \">";
	
	if ($STREETVIEW) {
	?>
		<script>
		function update_sv_params(placeid) {
			var svlati = document.getElementById('sv_latiText').value.slice(0, -1);
			var svlong = document.getElementById('sv_longText').value.slice(0, -1);
			var svbear = document.getElementById('sv_bearText').value.slice(0, -1);
			var svelev = document.getElementById('sv_elevText').value.slice(0, -1);
			var svzoom = document.getElementById('sv_zoomText').value;
			win03  = window.open('module.php?mod=googlemap&mod_action=places_edit&action=update_sv_params&placeid='+placeid+"&"+sessionname+"="+sessionid+
				'&svlati='+svlati+
				'&svlong='+svlong+
				'&svbear='+svbear+
				'&svelev='+svelev+
				'&svzoom='+svzoom, 
			'win03', 'top=50, left=50, width=680, height=550, resizable=1, scrollbars=1' );	
			if (window.focus) {win03.focus();}
		}
		</script>
		<?php
	
		$parent = safe_GET('parent');
		global $TBLPREFIX, $pl_lati, $pl_long;
		if ($level>=1) {
	
			$parent[$level-1] = PrintReady(addslashes($parent[$level-1]));	
			$latlng = WT_DB::prepare("SELECT pl_id, pl_lati, pl_long, pl_zoom, sv_long, sv_lati, sv_bearing, sv_elevation, sv_zoom FROM ##placelocation WHERE pl_place='{$parent[$level-1]}'")->fetchAll(PDO::FETCH_ASSOC);
		
    	  	if (!isset($latlng[0])) {
		    	echo "<br /><br />";
		    	echo "<br /><br /><br /><br />";
		    	echo "<font color='red'>";
		    	echo WT_I18n::translate('<b>This place has not been defined in the Googlemaps Geographic Place Locations</b><br /><br />');
		    	if ( WT_USER_IS_ADMIN )  {
		    		echo WT_I18n::translate('
		    			First, Click "Edit geographic place locations".<br />
		    			<br />
		    			Then, if you are using google Maps Streetview,<br />		    		
		    			return here to position Streetview afterwards.
		    		');
		    	} else {
		    		echo WT_I18n::translate('Please contact your administrator.');
		    	}
		    	echo "</font>";
    	  	} else {
				$pl_lati = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlng[0]['pl_lati']);	// WT_placelocation lati
				$pl_long = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlng[0]['pl_long']);	// WT_placelocation long
				
				// Check if Streetview location parameters are stored in database		
				$placeid	= $latlng[0]['pl_id'];			// Placelocation place id
				$sv_lat		= $latlng[0]['sv_lati']; 		// StreetView Point of View Latitude
				$sv_lng 	= $latlng[0]['sv_long'];		// StreetView Point of View Longitude	
				$sv_dir 	= $latlng[0]['sv_bearing'];		// StreetView Point of View Direction (degrees from North)
				$sv_pitch	= $latlng[0]['sv_elevation'];	// StreetView Point of View Elevation (+90 to -90 degrees (+=down, -=up)
				$sv_zoom	= $latlng[0]['sv_zoom'];		// StreetView Point of View Zoom (0, 1, 2 or 3)
				
				// If Streetview coordinates are stored, bring up the regular Streetview -------
		  		if ($latlng[0]['sv_lati']!=null) {
		  			$_map = WT_I18N::translate('Google Maps');
		  			$_reset = WT_I18N::translate('Reset');
						$_streetview = /* I18N: http://en.wikipedia.org/wiki/Google_street_view */ WT_I18N::translate('Google Street View');
					?>
					<div>
					<iframe style="background:transparent; margin-top:-3px; margin-left:2px; width:530px;height:405px;padding:0;border:solid 0px black" src="modules/googlemap/wt_v3_street_view.php?x=<?php echo $sv_lng; ?>&y=<?php echo $sv_lat; ?>&z=18&t=2&c=1&s=1&b=<?php echo $sv_dir; ?>&p=<?php echo $sv_pitch; ?>&m=<?php echo $sv_zoom; ?>&j=1&k=1&v=1&map=<?php echo $_map; ?>&reset=<?php echo $_reset; ?>&streetview=<?php echo $_streetview; ?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
					</div>
					<?php			
						$list_latlon = ("
							lati<input 		name='sv_latiText' id='sv_latiText' type='text' style='width:67px; background:none; border:none;' value='".$sv_lat."' 	/>
							long<input 		name='sv_longText' id='sv_longText' type='text' style='width:67px; background:none; border:none;' value='".$sv_lng."' 	/>
							bear<input 		name='sv_bearText' id='sv_bearText' type='text' style='width:50px; background:none; border:none;' value='".$sv_dir."' 	/>
							elev<input 		name='sv_elevText' id='sv_elevText' type='text' style='width:45px; background:none; border:none;' value='".$sv_pitch."'	/>
							zoom<input 		name='sv_zoomText' id='sv_zoomText' type='text' style='width:30px; background:none; border:none;' value='".$sv_zoom."'	/>
						");
						if (WT_USER_IS_ADMIN) {
							echo "<table align=\"center\" style=\"margin-left:6px; border:solid 1px black; width:522px; margin-top:-28px; background:#cccccc; \">";
						} else {
							echo "<table align=\"center\" style=\"display:none; \">";
						}
						echo "<tr><td>\n";
						echo "<form style=\"text-align:left; margin-left:5px; font:11px verdana; color:blue;\" method=\"post\" action=\"\">";
						echo $list_latlon;
						echo "";
						echo "<input type=\"submit\" name=\"Submit\" onClick=\"update_sv_params($placeid);\" value=\"", WT_I18N::translate('Save'), "\">";
						echo "</form>";
						echo "</td></tr>\n";
						echo "</table>\n";	
			
				// Else, if Admin, bring up StreetView adjustment Map --------------------------
	  			} else if ( WT_USER_IS_ADMIN )  {
	  				$sv_lat = $pl_lati; 	// Place Latitude
					$sv_lng = $pl_long;		// Place Longitude
					?>
					<iframe style="background:transparent; margin-top:-2px; margin-left: 2px; width:530px;height:650px;padding:0;border:solid 0px black" src="modules/googlemap/wt_v3_street_view_setup.php?x=<?php echo $sv_lng; ?>&y=<?php echo $sv_lat; ?>" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
					<?php			
					if (WT_USER_IS_ADMIN) {
						$list_latlon = ("
							lati<input 		name='sv_latiText' id='sv_latiText' type='text' style='width:67px; background:none; border:none;' value='".$sv_lat."' 	/>
							long<input 		name='sv_longText' id='sv_longText' type='text' style='width:67px; background:none; border:none;' value='".$sv_lng."' 	/>
							bearing<input 	name='sv_bearText' id='sv_bearText' type='text' style='width:50px; background:none; border:none;' value='".$sv_dir."' 	/>
							elev<input 		name='sv_elevText' id='sv_elevText' type='text' style='width:43px; background:none; border:none;' value='".$sv_pitch."'	/>
							zoom<input 		name='sv_zoomText' id='sv_zoomText' type='text' style='width:26px; background:none; border:none;' value='".$sv_zoom."'	/>
						");
						echo "<table align=\"center\" style=\" margin-left: 6px; border:1px solid black; width:522px; margin-top:-18px; background:#cccccc; \">";
						echo "<tr><td>\n";
							echo "<form style=\"text-align:left; margin-left:5px; font:11px verdana; color:blue;\" method=\"post\" action=\"\">";
							echo $list_latlon;
							echo "";
							echo "<input type=\"submit\" name=\"Submit\" onClick=\"update_sv_params($placeid);\" value=\"", WT_I18n::translate('Save'), "\">";
							echo "</form>";
						echo "</td></tr>\n";
						echo "</table>\n";			
					}		
	  			}		
	  	  	}
		}
		// Next line puts Place hierarchy on new row -----
		echo "</td></tr><tr>";
		
	}	// End Streetview window ===================================================================

}
	

function check_were_am_i($numls, $levelm) {
	$where_am_i=place_id_to_hierarchy($levelm);
	$i=$numls+1;
	if (!isset($levelo)) {
		$levelo[0]=0;
	}
	foreach (array_reverse($where_am_i, true) as $id=>$place2) {
		$levelo[$i]=$id;
		$i--;
	}
	return $levelo;
}

function check_place($place_names, $place) {
	if ($place == "Unknown") $place="";
	if (in_array($place, $place_names)) {
		return true;
	}
}

function print_how_many_people($level, $parent) {
	global $GEDCOM, $stats;

	$place_count_indi = 0;
	$place_count_fam = 0;
	if (!isset($parent[$level-1])) $parent[$level-1]="";
	$p_id = set_placeid_map($level, $parent);
	$indi = $stats->_statsPlaces('INDI', false, $p_id);
	$fam = $stats->_statsPlaces('FAM', false, $p_id);
	if (!empty($indi)) {
		foreach ($indi as $place) {
			$place_count_indi=$place['tot'];
		}
	}
	if (!empty($fam)) {
		foreach ($fam as $place) {
			$place_count_fam=$place['tot'];
		}
	}
	echo "<br /><br />", WT_I18N::translate('Individuals'), ": ", $place_count_indi, ", ", WT_I18N::translate('Families'), ": ", $place_count_fam;
}

function print_gm_markers($place2, $level, $parent, $levelm, $linklevels, $placelevels, $lastlevel=false) {
	global $GOOGLEMAP_COORD, $GOOGLEMAP_PH_MARKER, $GM_DISP_SHORT_PLACE, $GM_DISP_COUNT;
	
	if (($place2['lati'] == NULL) || ($place2['long'] == NULL) || (($place2['lati'] == "0") && ($place2['long'] == "0"))) {
		//echo "var icon_type = new GIcon();\n";
		echo "var icon_type = new google.maps.MarkerImage();\n";
			echo " icon_type.image = \"modules/googlemap/images/marker_yellow.png\";\n";
			echo " icon_type.shadow = \"modules/googlemap/images/shadow50.png\";\n";
			echo " icon_type.iconSize = google.maps.Size(20, 34);\n";
			echo " icon_type.shadowSize = google.maps.Size(37, 34);\n";
		//	echo " icon_type.iconAnchor = new google.maps.LatLng(6, 20);\n";
		//	echo " icon_type.infoWindowAnchor = new GPoint(5, 1);\n";
		echo "var point = new google.maps.LatLng(0, 0);\n";
		if ($lastlevel)
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", $level, $linklevels, "'><br />";
		else {
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", ($level+1), $linklevels, "&amp;parent[{$level}]=";
			if ($place2['place'] == "Unknown") echo "'><br />";
			else echo addslashes($place2['place']), "'><br />";
		}
		if (($place2["icon"] != NULL) && ($place2['icon'] != "")) {
			echo "<img src=\'", $place2['icon'], "'>&nbsp;&nbsp;";
		}
		if ($lastlevel) {
			$placename = substr($placelevels, 2);
			if ($place2['place'] == "Unknown") {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(substr($placelevels, 2));
				} else {
					echo WT_I18N::translate('unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(substr($placelevels, 2));
				} else {
					echo addslashes($place2['place']);
				}
			}
		} else {
			$placename = $place2['place'].$placelevels;
			if ($place2['place'] == "Unknown") {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(WT_I18N::translate('unknown').$placelevels);
				} else {
					echo WT_I18N::translate('unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes($place2['place'].$placelevels);
				} else {
					echo addslashes($place2['place']);
				}
			}
		}
		echo "</a>";
		if ($GM_DISP_COUNT) {
			if ($lastlevel) {
				print_how_many_people($level, $parent);
			} else {
				$parent[$level]=$place2['place'];
				print_how_many_people($level+1, $parent);
			}
		}
		echo "<br />", WT_I18N::translate('This place has no coordinates');
		if (WT_USER_IS_ADMIN)
			echo "<br /><a href='module.php?mod=googlemap&mod_action=admin_places&parent=", $levelm, "&display=inactive'>", WT_I18N::translate('Edit geographic location'), "</a>";
		echo "</div></td>\", icon_type, \"", str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), addslashes($place2['place'])), "\");\n";
	} else {
		$lati = str_replace(array('N', 'S', ','), array('', '-', '.'), $place2['lati']);
		$long = str_replace(array('E', 'W', ','), array('', '-', '.'), $place2['long']);
		//delete leading zero
		if ($lati >= 0) {
			$lati = abs($lati);
		} elseif ($lati < 0) {
			$lati = "-".abs($lati);
		}
		if ($long >= 0) {
			$long = abs($long);
		} elseif ($long < 0) {
			$long = "-".abs($long);
		}
		
		// flags by kiwi_pgv
		// echo 'alert("', $place2["icon"], '");';
		
		if (($place2["icon"] == NULL) || ($place2['icon'] == "") || ($GOOGLEMAP_PH_MARKER != "G_FLAG")) {
			echo "var icon_type = new google.maps.MarkerImage();\n";
		} else {
			echo "var icon_type = new google.maps.MarkerImage();\n";
			echo " icon_type.image = \"", $place2['icon'], "\";\n";
			echo " icon_type.shadow = \"modules/googlemap/images/flag_shadow.png\";\n";
			echo " icon_type.iconSize = new google.maps.Size(25, 15);\n";
			echo " icon_type.shadowSize = new google.maps.Size(35, 45);\n";
		//	echo " icon_type.iconAnchor = new GPoint(1, 45);\n";
		//	echo " icon_type.infoWindowAnchor = new GPoint(5, 1);\n";
		}
		echo "var point = new google.maps.LatLng({$lati}, {$long});\n";
		if ($lastlevel) {
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", $level, $linklevels, "'><br />";
		} else {
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", ($level+1), $linklevels, "&amp;parent[{$level}]=";
			if ($place2['place'] == "Unknown") {
				echo "'><br />";
			} else {
				echo addslashes($place2['place']), "'><br />";
			}
		}
		if (($place2['icon'] != NULL) && ($place2['icon'] != "")) {
			echo "<img src=\'", $place2['icon'], "'>&nbsp;&nbsp;";
		}
		if ($lastlevel) {
			$placename = substr($placelevels, 2);
			if ($place2['place'] == "Unknown") {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(substr($placelevels, 2));
				} else {
					echo WT_I18N::translate('unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(substr($placelevels, 2));
				} else {
					echo addslashes($place2['place']);
				}
			}
		} else {
			$placename = $place2['place'].$placelevels;
			if ($place2['place'] == "Unknown") {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(WT_I18N::translate('unknown').$placelevels);
				} else {
					echo WT_I18N::translate('unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes($place2['place'].$placelevels);
				} else {
					echo addslashes($place2['place']);
				}
			}
		}
		echo "</a>";
		if ($GM_DISP_COUNT) {
			if ($lastlevel) {
				print_how_many_people($level, $parent);
			} else {
				$parent[$level]=$place2['place'];
				print_how_many_people($level+1, $parent);
			}
		}
		$temp=addslashes($place2['place']);
		$temp=str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), $temp);
		if (!$GOOGLEMAP_COORD) {
			echo "<br /><br /></div></td>\", icon_type, \"", $temp, "\");\n";
		} else {
			echo "<br /><br />", $place2['lati'], ", ", $place2['long'], "</div></td>\", icon_type, \"", $temp, "\");\n";	
		}
	}
	//echo "map.addOverlay(marker);\n";

	//		echo "gmarks.push(marker);";
	// echo "bounds.extend(point);\n";
}

function map_scripts($numfound, $level, $parent, $linklevels, $placelevels, $place_names) {
	global $GOOGLEMAP_MAP_TYPE, $GM_MAX_NOF_LEVELS, $GOOGLEMAP_PH_WHEEL, $GOOGLEMAP_PH_CONTROLS, $GOOGLEMAP_PH_MARKER;

	?>
	<link type="text/css" href ="modules/googlemap/css/googlemap_style.css" rel="stylesheet" />
	<script type="text/javascript">
	// <![CDATA[
	
/*
	if (window.attachEvent) {
		window.attachEvent("onunload", function() {
			GUnload(); // Internet Explorer
		});
	} else {
		window.addEventListener("unload", function() {
			GUnload(); // Firefox and standard browsers
		}, false);
	}
*/

	var infowindow = new google.maps.InfoWindow( { 
    	// size: new google.maps.Size(150,50),
    	// maxWidth: 600
  	});
  	

	var map_center = new google.maps.LatLng(0,0);
	var map = "";
	var bounds = new google.maps.LatLngBounds ();
	var markers = [];
	var gmarkers = [];
	var i = 0;
	
	
	
 	// Create the map and mapOptions
 	var mapOptions = {
		zoom: 8,
		center: map_center,
		mapTypeId: google.maps.MapTypeId.TERRAIN,					// ROADMAP, SATELLITE, HYBRID, TERRAIN
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU 	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
		},
		navigationControl: true,
      	navigationControlOptions: {
   			position: google.maps.ControlPosition.TOP_RIGHT,		// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
   			style: google.maps.NavigationControlStyle.SMALL			// ANDROID, DEFAULT, SMALL, ZOOM_PAN
      	},
      	streetViewControl: false,									// Show Pegman or not
      	scrollwheel: false  		
	};
	map = new google.maps.Map(document.getElementById("place_map"), mapOptions);

	// Close any infowindow when map is clicked
    google.maps.event.addListener(map, 'click', function() {
        infowindow.close();   	
    });
    

	// if (GBrowserIsCompatible()) {
	
	// Creates a marker whose info window displays the given name
	function createMarker(point, html, icon, name) 	{
	
		// Choose icon and shadow ============
		<?php
		// echo "alert(icon.image);";
		if ($level==0) {
  			echo 'var iconImage = new google.maps.MarkerImage(icon.image,'; 
      		echo 'new google.maps.Size(25, 15),';
    		echo 'new google.maps.Point(0,0),';
    		echo 'new google.maps.Point(1, 45));';
  			echo 'var iconShadow = new google.maps.MarkerImage("modules/googlemap/images/flag_shadow.png",';
    	  	echo 'new google.maps.Size(35, 45),';
    	  	echo 'new google.maps.Point(0,0),';
    	  	echo 'new google.maps.Point(1, 45));';
  		} else {
  			echo 'var iconImage = new google.maps.MarkerImage("http://maps.google.com/mapfiles/marker.png",';
      		echo 'new google.maps.Size(20, 34),';
    		echo 'new google.maps.Point(0,0),';
    		echo 'new google.maps.Point(9, 34));';
  			echo 'var iconShadow = new google.maps.MarkerImage("http://www.google.com/mapfiles/shadow50.png",';
    	  	echo 'new google.maps.Size(37, 34),';
    	  	echo 'new google.maps.Point(0,0),';
    	  	echo 'new google.maps.Point(9, 34));';
  		}
   		
    	//if ($level==0) {

  		//} else {

  		//} 		
    	?>
      	
		var posn = new google.maps.LatLng(0,0);
		var marker = new google.maps.Marker({
		    position: point,
		    icon: iconImage,
         	shadow: iconShadow,
        	map: map,
        	title: name,
        });
		
		// Show this markers name in the info window when it is clicked
    	google.maps.event.addListener(marker, 'click', function() {
   			infowindow.close();
       		infowindow.setContent(html);
       		infowindow.open(map, marker);
       	});
       	
       	// === Store the tab, category and event info as marker properties ===
        marker.mypoint = point;
        marker.mytitle = name;
        marker.myposn = posn;
        
        gmarkers.push(marker); 
        
        bounds.extend(marker.position);	
		map.fitBounds(bounds);
 			
		return marker;
	}
	

/*
	function Map_type() {}
	Map_type.prototype = new GControl();

	Map_type.prototype.refresh = function()
	{
		this.button1.className = 'non_active';
		if (this.place_map.getCurrentMapType() != G_NORMAL_MAP) {
			this.button2.className = 'non_active';
		} else {
			this.button2.className = 'active';
		}
		if (this.place_map.getCurrentMapType() != G_SATELLITE_MAP) {
			this.button3.className = 'non_active';
		} else {
			this.button3.className = 'active';
		}
		if (this.place_map.getCurrentMapType() != G_HYBRID_MAP) {
			this.button4.className = 'non_active';
		} else {
			this.button4.className = 'active';
		}
		if (this.place_map.getCurrentMapType() != G_PHYSICAL_MAP) {
			this.button5.className = 'non_active';
		} else {
			this.button5.className = 'active';
		}
	}

	Map_type.prototype.initialize = function(place_map)
	{
		var list = document.createElement("ul");
		list.id	= 'map_type';

		var button1 = document.createElement('li');
		var button2 = document.createElement('li');
		var button3 = document.createElement('li');
		var button4 = document.createElement('li');
		var button5 = document.createElement('li');

		button1.innerHTML = '<?php echo WT_I18N::translate('Redraw map'); ?>';
		button2.innerHTML = '<?php echo WT_I18N::translate('Map'); ?>';
		button3.innerHTML = '<?php echo WT_I18N::translate('Satellite'); ?>';
		button4.innerHTML = '<?php echo WT_I18N::translate('Hybrid'); ?>';
		button5.innerHTML = '<?php echo WT_I18N::translate('Terrain'); ?>';

		button1.onclick = function() { <?php
			if ($numfound>1) {
				echo "place_map.setCenter(bounds.getCenter(), place_map.getBoundsZoomLevel(bounds));";
			} elseif ($level==1) {
				echo "place_map.setCenter(bounds.getCenter(), place_map.getBoundsZoomLevel(bounds)-8);";
			} elseif ($level==2) {
				echo "place_map.setCenter(bounds.getCenter(), place_map.getBoundsZoomLevel(bounds)-5);";
			} else {
				echo "place_map.setCenter(bounds.getCenter(), place_map.getBoundsZoomLevel(bounds)-3);";
			}
			?>; return false; };
		button2.onclick = function() { place_map.setMapType(G_NORMAL_MAP); return false; };
		button3.onclick = function() { place_map.setMapType(G_SATELLITE_MAP); return false; };
		button4.onclick = function() { place_map.setMapType(G_HYBRID_MAP); return false; };
		button5.onclick = function() { place_map.setMapType(G_PHYSICAL_MAP); return false; };

		list.appendChild(button1);
		list.appendChild(button2);
		list.appendChild(button3);
		list.appendChild(button4);
		list.appendChild(button5);

		this.button1 = button1;
		this.button2 = button2;
		this.button3 = button3;
		this.button4 = button4;
		this.button5 = button5;
		this.place_map = place_map;
		place_map.getContainer().appendChild(list);
		return list;
	}
*/







/*
	// create the map	
	Map_type.prototype.getDefaultPosition = function()
	{
		return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(2, 2));
	}
	var map_type;
	var place_map = new GMap2(document.getElementById("place_map"));
	map_type = new Map_type();
	place_map.addControl(map_type);
	GEvent.addListener(place_map, 'maptypechanged', function()
	{
		map_type.refresh();
	});
	// var bounds = new GLatLngBounds();
	var bounds = new google.maps.LatLngBounds ();
	// for further street view
	//place_map.addControl(new GLargeMapControl3D(true));

	place_map.addControl(new GLargeMapControl3D());
	place_map.addControl(new GScaleControl());
	var mini = new GOverviewMapControl();
	place_map.addControl(mini);

	// displays blank minimap - probably google api's bug
	//mini.hide();
*/


	<?php
	if (check_exist_table()) {
		$levelm = set_levelm($level, $parent);
		if (isset($levelo[0])) $levelo[0]=0;
		$numls = count($parent)-1;
		$levelo=check_were_am_i($numls, $levelm);
		if ($numfound<2 && ($level==1 || !(isset($levelo[($level-1)])))) {
			echo "map.maxZoom=6;";
		//	echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
		//	echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+5);\n";
		}
		else if ($numfound<2 && !isset($levelo[($level-2)])) {

	//		echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
	//		echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+6);\n";
		}
		else if ($level==2) {
		echo "map.maxZoom=8;";
	//		echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
	//		echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+8);\n";
		}
		else if ($numfound<2 && $level>1) {
	//		echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
	//		echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+18);\n";
		}
		else
	//		echo "map.setCenter(new google.maps.LatLng(0, 0), 1);\n";
		// if ($GOOGLEMAP_PH_WHEEL) echo "map.enableScrollWheelZoom();\n";
		// echo " map.setMapType($GOOGLEMAP_MAP_TYPE);\n";
		?>

		//create markers
		<?php
		if ($numfound==0 && $level>0) {
			if (isset($levelo[($level-1)])) {
				$placelist2=get_place_list_loc($levelo[($level-1)]);
				if (!empty($placelist2)) {
					//lastlevel place
					foreach ($placelist2 as $place2) {
						if (isset($levelo[$level])) {
							if ($place2['place_id']==$levelo[$level]) {
								print_gm_markers($place2, $level, $parent, $levelo[($level-1)], $linklevels, $placelevels, true);
							}
						} else {
							echo "var icon_type = new google.maps.MarkerImage();\n";
							echo "icon_type.image = \"modules/googlemap/images/marker_yellow.png\"\n";
							echo "icon_type.shadow = \"modules/googlemap/images/shadow50.png\";\n";
							echo "icon_type.iconSize = new google.maps.Size(20, 34);\n";
							echo "icon_type.shadowSize = new google.maps.Size(37, 34);\n";
				//			echo "icon_type.iconAnchor = new google.maps.LatLng(6, 20);\n";
				//			echo "icon_type.infoWindowAnchor = new GPoint(5, 1);\n";
				
				//  		echo 'var iconShape = {';
      			//			echo 'coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],';
      			//			echo 'type: "poly"';
  				//			echo '};';  
				
							echo "var point = new google.maps.LatLng(0, 0);\n";
							echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><b>";

							echo substr($placelevels, 2), "</b><br />", WT_I18N::translate('This place has no coordinates');
							if (WT_USER_IS_ADMIN)
								echo "<br /><a href='module.php?mod=googlemap&mod_action=admin_places&parent=0&display=inactive'>", WT_I18N::translate('Edit geographic location'), "</a>";
							echo "<br /></div></td>\", icon_type, \"", WT_I18N::translate('Edit geographic location'), "\");\n";
							
				//			echo "place_map.addOverlay(marker);\n";
							echo "bounds.extend(point);\n";
							break;
						}
					}
				} else {
					// lastlevel place not in table
					$placelist2=get_place_list_loc($levelo[($level-1)], true);
					foreach ($placelist2 as $place2) {
						if (isset($levelo[$level])) {
							if ($place2['place_id']==$levelo[$level]) {
								print_gm_markers($place2, $level, $parent, $levelo[$level], $linklevels, $placelevels, true);
							}
						}
					}
				}
			}
		} else {
			//place from table
			$placelist2=get_place_list_loc($levelm);
			if (!empty($placelist2)) {
				foreach ($placelist2 as $place2) {
					if (check_place($place_names, $place2['place'])) { 
						print_gm_markers($place2, $level, $parent, $levelm, $linklevels, $placelevels);
					}
				}
			}
			else if ($level>0) { //if unknown place display the upper level place
				$placelevels = ", ".WT_I18N::translate('unknown').$placelevels;
				$linklevels .= "&amp;parent[".$level."]=";
				$break = false;
				for ($i=1;$i<=$GM_MAX_NOF_LEVELS;$i++) {
					if (($level-$i)>=0 && isset($levelo[($level-$i)])) {
						$placelist2=get_place_list_loc($levelo[($level-$i)], true);
						foreach ($placelist2 as $place2) {
							if ($place2['place']!="Unknown" || (($place2['lati'] != NULL) && ($place2['long'] != NULL))) {
								if (isset ($levelo[$level-$i+1]) && $place2['place_id']==$levelo[$level-$i+1]) {
									print_gm_markers($place2, ($level+1), $parent, $levelm, $linklevels, $placelevels, true);
									$break = true;
								}
							}
						}
						if ($break) break;
					}
				}
			}
		}
	} else {
		echo "var icon_type = new google.maps.MarkerImage();\n";
		echo "icon_type.image = \"modules/googlemap/images/marker_yellow.png\"";
		echo "var point = new google.maps.LatLng(0, 0);\n";
		echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'>";
		echo "<br />", WT_I18N::translate('This place has no coordinates');
		if (WT_USER_IS_ADMIN)
			echo "<br /><a href='module.php?mod=googlemap&mod_action=admin_places&parent=0&display=inactive'>", WT_I18N::translate('Edit geographic location'), "</a>";
		echo "<br /></div></td>\", icon_type, \"", WT_I18N::translate('Edit geographic location'), "\");\n";
		
//		echo "place_map.addOverlay(marker);\n";
//		echo "bounds.extend(point);\n";
	}
	?>
	//end markers
	// map.setCenter(bounds.getCenter());
	
// map.fitBounds(bounds);

	
	<?php if ($GOOGLEMAP_PH_CONTROLS) { ?>
		// hide controls
//		GEvent.addListener(place_map, 'mouseout', function() {place_map.hideControls();});
		// show controls
//		GEvent.addListener(place_map, 'mouseover', function() {place_map.showControls();});
//		GEvent.trigger(place_map, 'mouseout');
		<?php
	}
//	if ($numfound>1)
//		echo "map.setZoom(map.getBoundsZoomLevel(bounds));\n";
	?>
	//} else {
	//  alert("Sorry, the Google Maps API is not compatible with this browser");
	//}
	// This Javascript is based on code provided by the
	// Blackpool Community Church Javascript Team
	// http://www.commchurch.freeserve.co.uk/
	// http://econym.googlepages.com/index.htm
	//]]>
	</script>
	<?php
}





