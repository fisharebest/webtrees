<?php
// Displays a place hierachy
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';
if (file_exists(WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php')) {
	require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
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
	$par = explode (",", strip_tags($place));
	$par = array_reverse($par);
	$place_id = 0;
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
				WT_DB::prepare("SELECT p_id FROM `##places` WHERE p_parent_id=? AND p_file=? AND p_place LIKE ? ORDER BY p_place")
				->execute(array($place_id, WT_GED_ID, $placename))
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

function create_map($placelevels) {
	global $level;
	global $GOOGLEMAP_PH_XSIZE, $GOOGLEMAP_PH_YSIZE, $GOOGLEMAP_MAP_TYPE, $levelm, $plzoom, $controller;
	
	// *** ENABLE STREETVIEW *** (boolean) =========================================================
	$STREETVIEW = get_module_setting('googlemap', 'GM_USE_STREETVIEW');
	// =============================================================================================
	$parent = safe_GET('parent', WT_REGEX_UNSAFE);
	
	// create the map
	echo '<table style="margin:20px auto 0 auto;"><tr valign="top"><td>';
	//<!-- start of map display -->
	echo '<table><tr valign="top">';
	echo '<td class="center" width="200px">';

	$levelm = set_levelm($level, $parent);
	$latlng = 
		WT_DB::prepare("SELECT pl_place, pl_id, pl_lati, pl_long, pl_zoom, sv_long, sv_lati, sv_bearing, sv_elevation, sv_zoom FROM `##placelocation` WHERE pl_id=?")
		->execute(array($levelm))
		->fetch(PDO::FETCH_ASSOC);
	if ($STREETVIEW && $level!=0 ) {
		echo '<div id="place_map" style="margin-top:20px; border:1px solid gray; width: ', $GOOGLEMAP_PH_XSIZE, 'px; height: ', $GOOGLEMAP_PH_YSIZE, 'px; ';
	} else {
		echo '<div id="place_map" style="border:1px solid gray; width:', $GOOGLEMAP_PH_XSIZE, 'px; height:', $GOOGLEMAP_PH_YSIZE, 'px; ';	
	}
	echo "\"><i class=\"icon-loading-large\"></i></div>";
	echo '<script src="', WT_GM_SCRIPT, '"></script>';
	echo '</td>';
	
	$plzoom	= $latlng['pl_zoom'];		// Map zoom level

	if (WT_USER_IS_ADMIN) {
		$placecheck_url = 'module.php?mod=googlemap&amp;mod_action=admin_placecheck';
		if ($parent && isset($parent[0]) ) {
			$placecheck_url .= '&amp;country='.$parent[0];
			if (isset($parent[1])) {
				$placecheck_url .= '&amp;state='.$parent[1];
			}
		}
		$adminplaces_url = 'module.php?mod=googlemap&amp;mod_action=admin_places';
		if ($latlng && isset($latlng['pl_id'])) {
			$adminplaces_url .= '&amp;parent='.$latlng['pl_id'];
		}
		echo '</tr><tr><td>';
		echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
		echo '&nbsp;|&nbsp;';
		echo '<a href="'.$adminplaces_url.'">', WT_I18N::translate('Geographic data'), '</a>';
		echo '&nbsp;|&nbsp;';
		echo '<a href="'.$placecheck_url.'">', WT_I18N::translate('Place Check'), '</a>';
		if (array_key_exists('batch_update', WT_Module::getActiveModules())) {
			$placelevels=preg_replace('/, '.WT_I18N::translate('unknown').'/', ', ', $placelevels); // replace ", unknown" with ", " 
			$placelevels=substr($placelevels, 2); // remove the leading ", "
			if ($placelevels) {
				$batchupdate_url='module.php?mod=batch_update&amp;mod_action=admin_batch_update&amp;plugin=search_replace_bu_plugin&amp;method=exact&amp;ged='.WT_GEDCOM.'&amp;search='.urlencode($placelevels); // exact match
				echo '&nbsp;|&nbsp;';
				echo '<a href="'.$batchupdate_url.'">', WT_I18N::translate('Batch update'), '</a>';
			}
		}
	}
	echo '</td></tr></table>';
	echo '</td>';
	echo '<td style="margin-left:15px; float:right;">';

	if ($STREETVIEW) {
		$controller->addInlineJavascript('
			function update_sv_params(placeid) {
				var svlati = document.getElementById("sv_latiText").value.slice(0, -1);
				var svlong = document.getElementById("sv_longText").value.slice(0, -1);
				var svbear = document.getElementById("sv_bearText").value.slice(0, -1);
				var svelev = document.getElementById("sv_elevText").value.slice(0, -1);
				var svzoom = document.getElementById("sv_zoomText").value;
				win03 = window.open("module.php?mod=googlemap&mod_action=places_edit&action=update_sv_params&placeid="+placeid+"&svlati="+svlati+"&svlong="+svlong+"&svbear="+svbear+"&svelev="+svelev+"&svzoom="+svzoom, "win03", indx_window_specs);	
				if (window.focus) {win03.focus();}
			}
		');
	
		$parent = safe_GET('parent');
		global $TBLPREFIX, $pl_lati, $pl_long;
		if ($level>=1) {
			$pl_lati = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlng['pl_lati']);	// WT_placelocation lati
			$pl_long = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlng['pl_long']);	// WT_placelocation long
			
			// Check if Streetview location parameters are stored in database		
			$placeid	= $latlng['pl_id'];			// Placelocation place id
			$sv_lat		= $latlng['sv_lati'];		// StreetView Point of View Latitude
			$sv_lng		= $latlng['sv_long'];		// StreetView Point of View Longitude	
			$sv_dir		= $latlng['sv_bearing'];	// StreetView Point of View Direction (degrees from North)
			$sv_pitch	= $latlng['sv_elevation'];	// StreetView Point of View Elevation (+90 to -90 degrees (+=down, -=up)
			$sv_zoom	= $latlng['sv_zoom'];		// StreetView Point of View Zoom (0, 1, 2 or 3)
			
			// Check if Street View Lati/Long are the default of 0 or null, if so use regular Place Lati/Long to set an initial location for the panda ------------
			if (($latlng['sv_lati']==null && $latlng['sv_long']==null) || ($latlng['sv_lati']==0 && $latlng['sv_long']==0)) {
					$sv_lat = $pl_lati;
					$sv_lng = $pl_long;
			}
			// Set Street View parameters to numeric value if NULL (avoids problem with Google Street View™ Pane not rendering)
			if ($sv_dir==null) {
				$sv_dir=0;
			}
			if ($sv_pitch==null) {
				$sv_pitch=0;
			}				
			if ($sv_zoom==null) {
				$sv_zoom=1;
			}
			?>
			<div>
			<iframe style="background:transparent; margin-top:-3px; margin-left:2px; width:530px;height:405px;padding:0;border:solid 0px black" src="module.php?mod=googlemap&amp;mod_action=wt_v3_street_view&amp;x=<?php echo $sv_lng; ?>&amp;y=<?php echo $sv_lat; ?>&amp;z=18&amp;t=2&amp;c=1&amp;s=1&amp;b=<?php echo $sv_dir; ?>&amp;p=<?php echo $sv_pitch; ?>&amp;m=<?php echo $sv_zoom; ?>&amp;j=1&amp;k=1&amp;v=1" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
			</div>
			
			<?php			
			$list_latlon = (
				WT_Gedcom_Tag::getLabel('LATI')."<input name='sv_latiText' id='sv_latiText' type='text' style='width:42px; background:none; border:none;' value='".$sv_lat."'>".
				WT_Gedcom_Tag::getLabel('LONG')."<input name='sv_longText' id='sv_longText' type='text' style='width:42px; background:none; border:none;' value='".$sv_lng."'>".
				/* I18N: Compass bearing (in degrees), for street-view mapping */ WT_I18N::translate('Bearing')."<input name='sv_bearText' id='sv_bearText' type='text' style='width:46px; background:none; border:none;' value='".$sv_dir."'>".
				/* I18N: Angle of elevation (in degrees), for street-view mapping */ WT_I18N::translate('Elevation')."<input name='sv_elevText' id='sv_elevText' type='text' style='width:30px; background:none; border:none;' value='".$sv_pitch."'>".
				WT_I18N::translate('Zoom')."<input name='sv_zoomText' id='sv_zoomText' type='text' style='width:30px; background:none; border:none;' value='".$sv_zoom."'>
			");
			if (WT_USER_IS_ADMIN) {
				echo "<table align=\"center\" style=\"margin-left:6px; border:solid 1px black; width:522px; margin-top:-28px; background:#cccccc; \">";
			} else {
				echo "<table align=\"center\" style=\"display:none; \">";
			}
			echo "<tr><td>";
			echo "<form style=\"text-align:left; margin-left:5px; font:11px verdana; color:blue;\" method=\"post\" action=\"\">";
			echo $list_latlon;
			echo "<input type=\"submit\" name=\"Submit\" onclick=\"update_sv_params($placeid);\" value=\"", WT_I18N::translate('save'), "\">";
			echo "</form>";
			echo "</td></tr>";
			echo "</table>";
		}
		// Next line puts Place hierarchy on new row -----
		echo '</td></tr><tr>';
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
	$stats = new WT_Stats(WT_GEDCOM);

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
	echo "<br><br>", WT_I18N::translate('Individuals'), ": ", $place_count_indi, ", ", WT_I18N::translate('Families'), ": ", $place_count_fam;
}

function print_gm_markers($place2, $level, $parent, $levelm, $linklevels, $placelevels, $lastlevel=false) {
	global $GOOGLEMAP_COORD, $GOOGLEMAP_PH_MARKER, $GM_DISP_SHORT_PLACE;
	
	if (($place2['lati'] == NULL) || ($place2['long'] == NULL) || (($place2['lati'] == '0') && ($place2['long'] == '0'))) {
		echo 'var icon_type = new google.maps.MarkerImage();';
		echo 'icon_type.image = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/marker_yellow.png";';
		echo 'icon_type.shadow = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/shadow50.png";';
		echo 'icon_type.iconSize = google.maps.Size(20, 34);';
		echo 'icon_type.shadowSize = google.maps.Size(37, 34);';
		echo 'var point = new google.maps.LatLng(0, 0);';
		if ($lastlevel)
			echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "'><br>";
		else {
			echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "&amp;parent[{$level}]=";
			if ($place2['place'] == "Unknown") echo "'><br>";
			else echo addslashes($place2['place']), "'><br>";
		}
		if (($place2['icon'] != NULL) && ($place2['icon'] != '')) {
			echo '<img src=\"', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place2['icon'], '\">&nbsp;&nbsp;';
		}
		if ($lastlevel) {
			$placename = substr($placelevels, 2);
			if ($place2['place'] == 'Unknown') {
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
			if ($place2['place'] == 'Unknown') {
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
		echo '</a>';
		if ($lastlevel) {
			print_how_many_people($level, $parent);
		} else {
			$parent[$level]=$place2['place'];
			print_how_many_people($level+1, $parent);
		}
		echo '<br>', WT_I18N::translate('This place has no coordinates');
		if (WT_USER_IS_ADMIN)
			echo "<br><a href='module.php?mod=googlemap&amp;mod_action=admin_places&amp;parent=", $levelm, "&amp;display=inactive'>", WT_I18N::translate('Geographic data'), "</a>";
		echo "</div>\", icon_type, \"", str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), addslashes($place2['place'])), "\");\n";
	} else {
		$lati = str_replace(array('N', 'S', ','), array('', '-', '.'), $place2['lati']);
		$long = str_replace(array('E', 'W', ','), array('', '-', '.'), $place2['long']);
		//delete leading zero
		if ($lati >= 0) {
			$lati = abs($lati);
		} elseif ($lati < 0) {
			$lati = '-'.abs($lati);
		}
		if ($long >= 0) {
			$long = abs($long);
		} elseif ($long < 0) {
			$long = '-'.abs($long);
		}
		
		// flags by kiwi3685 ---
		if (($place2['icon'] == NULL) || ($place2['icon'] == '') || ($GOOGLEMAP_PH_MARKER != 'G_FLAG')) {
			echo 'var icon_type = new google.maps.MarkerImage();';
		} else {
			echo 'var icon_type = new google.maps.MarkerImage();';
			echo ' icon_type.image = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place2['icon'], '";';
			echo ' icon_type.shadow = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/flag_shadow.png";';
			echo ' icon_type.iconSize = new google.maps.Size(25, 15);';
			echo ' icon_type.shadowSize = new google.maps.Size(35, 45);';
		}
		echo "var point = new google.maps.LatLng({$lati}, {$long});";
		if ($lastlevel) {
			echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "'><br>";
		} else {
			echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "&amp;parent[{$level}]=";
			if ($place2['place'] == 'Unknown') {
				echo "'><br>";
			} else {
				echo addslashes($place2['place']), "'><br>";
			}
		}
		if (($place2['icon'] != NULL) && ($place2['icon'] != "")) {
			echo '<img src=\"', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place2['icon'], '\">&nbsp;&nbsp;';
		}
		if ($lastlevel) {
			$placename = substr($placelevels, 2);
			if ($place2['place'] == 'Unknown') {
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
			if ($place2['place'] == 'Unknown') {
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
		echo '</a>';
		if ($lastlevel) {
			print_how_many_people($level, $parent);
		} else {
			$parent[$level]=$place2['place'];
			print_how_many_people($level+1, $parent);
		}
		$temp=addslashes($place2['place']);
		$temp=str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), $temp);
		if (!$GOOGLEMAP_COORD) {
			echo "<br><br></div>\", icon_type, \"", $temp, "\");";
		} else {
			echo "<br><br>", $place2['lati'], ", ", $place2['long'], "</div>\", icon_type, \"", $temp, "\");";
		}
	}
}

function map_scripts($numfound, $level, $parent, $linklevels, $placelevels, $place_names) {
	global $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_PH_MARKER, $plzoom, $controller;

	$controller->addInlineJavascript('
		jQuery("head").append(\'<link rel="stylesheet" type="text/css" href="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/css/wt_v3_googlemap.css" />\');
		var numMarkers = "'.$numfound.'";
		var mapLevel   = "'.$level.   '";
		var placezoom  = "'.$plzoom.  '";
		var infowindow = new google.maps.InfoWindow({ 
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
			mapTypeId: google.maps.MapTypeId.'.$GOOGLEMAP_MAP_TYPE.', // ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControl: true,
			navigationControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT, // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
				style: google.maps.NavigationControlStyle.SMALL  // ANDROID, DEFAULT, SMALL, ZOOM_PAN
			},
			streetViewControl: false, // Show Pegman or not
			scrollwheel: false
		};
		map = new google.maps.Map(document.getElementById("place_map"), mapOptions);
	
		// Close any infowindow when map is clicked
		google.maps.event.addListener(map, "click", function() {
			infowindow.close();
		});
	
		// If only one marker, set zoom level to that of place in database
		if (mapLevel != 0) {
			var pointZoom = placezoom;
		} else {
			var pointZoom = 1;
		}
	
		// Creates a marker whose info window displays the given name
		function createMarker(point, html, icon, name) {
			// Choose icon and shadow ============
			if (icon.image && '.$level.'<=3) {
				if (icon.image!="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/marker_yellow.png") {
					var iconImage = new google.maps.MarkerImage(icon.image,
					new google.maps.Size(25, 15),
					new google.maps.Point(0,0),
					new google.maps.Point(1, 45));
					var iconShadow = new google.maps.MarkerImage("'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/flag_shadow.png",
					new google.maps.Size(35, 45),
					new google.maps.Point(0,0),
					new google.maps.Point(1, 45));
				 } else {
					var iconImage = new google.maps.MarkerImage(icon.image,
					new google.maps.Size(20, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(9, 34));
					var iconShadow = new google.maps.MarkerImage("https://www.google.com/mapfiles/shadow50.png",
					new google.maps.Size(37, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(9, 34));
				}
				//	*** Clickable area of icon - To be refined later *** ===================================
				//	var iconShape = {
				//	coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
				//	type: "poly"
				//	};
			} else {
				var iconImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/marker.png",
				new google.maps.Size(20, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34));
				var iconShadow = new google.maps.MarkerImage("https://www.google.com/mapfiles/shadow50.png",
				new google.maps.Size(37, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34));
				//	*** Clickable area of icon - To be refined later *** ===================================
				//	var iconShape = {
				//	coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
				//	type: "poly"
				//	};
			}
			var posn = new google.maps.LatLng(0,0);
			var marker = new google.maps.Marker({
				position: point,
				icon: iconImage,
				shadow: iconShadow,
				map: map,
				title: name
			});
			// Show this markers name in the info window when it is clicked
			google.maps.event.addListener(marker, "click", function() {
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
		
			// If only one marker use database place zoom level rather than fitBounds of markers
			if (numMarkers > 1) {
				map.fitBounds(bounds);
			} else {
				map.setCenter(bounds.getCenter());
				map.setZoom(parseFloat(pointZoom));
			}
			return marker;
		}
	');
	
	global $GOOGLEMAP_MAX_ZOOM;
	$levelm = set_levelm($level, $parent);
	if (isset($levelo[0])) $levelo[0]=0;
	$numls = count($parent)-1;
	$levelo=check_were_am_i($numls, $levelm);
	if ($numfound<2 && ($level==1 || !(isset($levelo[($level-1)])))) {
		$controller->addInlineJavascript('map.maxZoom=6;');
		// echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
		// echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+5);\n";
	} else if ($numfound<2 && !isset($levelo[($level-2)])) {
		// echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
		// echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+6);\n";
	} else if ($level==2) {
		$controller->addInlineJavascript('map.maxZoom=10;');
		// echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
		// echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+8);\n";
	} else if ($numfound<2 && $level>1) {
		// echo "map.maxZoom=".$GOOGLEMAP_MAX_ZOOM.";";
		// echo "zoomlevel = map.getBoundsZoomLevel(bounds);\n";
		// echo " map.setCenter(new google.maps.LatLng(0, 0), zoomlevel+18);\n";
	} 
	//create markers

	ob_start(); // TODO: rewrite print_gm_markers, and the functions called therein, to either return text or add JS directly.

	if ($numfound==0 && $level>0) {
		if (isset($levelo[($level-1)])) {  // ** BH not sure yet what this if statement is for ... TODO **
			// show the current place on the map

			$place = WT_DB::prepare("SELECT pl_id as place_id, pl_place as place, pl_lati as lati, pl_long as `long`, pl_zoom as zoom, pl_icon as icon FROM `##placelocation` WHERE pl_id=?")
			->execute(array($levelm))
			->fetch(PDO::FETCH_ASSOC);

			if ($place) {
				// re-calculate the hierarchy information required to display the current place
				$thisloc = $parent;
				$xx = array_pop($thisloc);
				$thislevel = $level-1 ;
				$thislinklevels = substr($linklevels,0,strrpos($linklevels,'&amp;'));
				if (strpos($placelevels,',',1)) {
					$thisplacelevels = substr($placelevels,strpos($placelevels,',',1));
				} else {
					// this is the top level, remove everything
					$thisplacelevels = '';
				}

				print_gm_markers($place, $thislevel, $thisloc, $place['place_id'], $thislinklevels, $thisplacelevels);
			}
		}
	}

	// display any sub-places
	$placeidlist=array();
	foreach ($place_names as $placename) {
		$thisloc = $parent;
		$thisloc[] = $placename;
		$this_levelm = set_levelm($level+1, $thisloc);
		if ($this_levelm) $placeidlist[] = $this_levelm;
	}

	if ($placeidlist) {
		// flip the array (thus removing duplicates)
		$placeidlist=array_flip($placeidlist);
		// remove entry for parent location
		unset($placeidlist[$levelm]);
	}
	if ($placeidlist) {
		// the keys are all we care about (this reverses the earlier array_flip, and ensures there are no "holes" in the array)
		$placeidlist=array_keys($placeidlist);
		// note: this implode/array_fill code generates one '?' for each entry in the $placeidlist array
		$placelist =
			WT_DB::prepare('SELECT pl_id as place_id, pl_place as place, pl_lati as lati, pl_long as `long`, pl_zoom as zoom, pl_icon as icon FROM `##placelocation` WHERE pl_id IN ('.implode(',', array_fill(0, count($placeidlist), '?')).')')
			->execute($placeidlist)
			->fetchAll(PDO::FETCH_ASSOC);

		foreach ($placelist as $place) {
			print_gm_markers($place, $level, $parent, $place['place_id'], $linklevels, $placelevels);
		}
	}
	$controller->addInlineJavascript(ob_get_clean());
}
