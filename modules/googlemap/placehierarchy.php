<?php
/**
 * Displays a place hierachy
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * @author Łukasz Wileński <wooc@users.sourceforge.net>
 * $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

if (file_exists(WT_ROOT.'modules/googlemap/defaultconfig.php')) {
	require WT_ROOT.'modules/googlemap/defaultconfig.php';
	require WT_ROOT.'modules/googlemap/googlemap.php';
}
require_once WT_ROOT.'includes/classes/class_stats.php';
$stats = new stats($GEDCOM);

function check_exist_table() {
	global $TBLPREFIX;
	return WT_DB::table_exists("{$TBLPREFIX}placelocation");
}


function get_place_list_loc($parent_id, $inactive=false) {
	global $display, $TBLPREFIX;
	if ($inactive) {
		$rows=
			WT_DB::prepare("SELECT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=? ORDER BY pl_place")
			->execute(array($parent_id))
			->fetchAll();
	} else {
		$rows=
			WT_DB::prepare(
				"SELECT DISTINCT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon".
				" FROM {$TBLPREFIX}placelocation".
				" INNER JOIN {$TBLPREFIX}places ON {$TBLPREFIX}placelocation.pl_place={$TBLPREFIX}places.p_place AND {$TBLPREFIX}placelocation.pl_level={$TBLPREFIX}places.p_level".
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
	global $TBLPREFIX;

	$statement=
		WT_DB::prepare("SELECT pl_parent_id, pl_place FROM {$TBLPREFIX}placelocation WHERE pl_id=?");
	$arr=array();
	while ($id!=0) {
		$row=$statement->execute(array($id))->fetchOneRow();
		$arr=array($id=>$row->pl_place)+$arr;
		$id=$row->pl_parent_id;
	}
	return $arr;
}

function get_placeid($place) {
	global $TBLPREFIX;
	$par = explode (",", $place);
	$par = array_reverse($par);
	$place_id = 0;
	if (check_exist_table()) {
		for($i=0; $i<count($par); $i++) {
			$par[$i] = trim($par[$i]);
			if (empty($par[$i])) $par[$i]="unknown";
			$placelist = create_possible_place_names($par[$i], $i+1);
			foreach ($placelist as $key => $placename) {
				$pl_id=
					WT_DB::prepare("SELECT pl_id FROM {$TBLPREFIX}placelocation WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
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
	global $TBLPREFIX;
	$par = explode (",", $place);
	$par = array_reverse($par);
	$place_id = 0;
	for($i=0; $i<count($par); $i++) {
		$par[$i] = trim($par[$i]);
		$placelist = create_possible_place_names($par[$i], $i+1);
		foreach ($placelist as $key => $placename) {
			$pl_id=
				WT_DB::prepare("SELECT p_id FROM {$TBLPREFIX}places WHERE p_level=? AND p_parent_id=? AND p_file=? AND p_place LIKE ? ORDER BY p_place")
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
	global $GOOGLEMAP_API_KEY, $GOOGLEMAP_PH_XSIZE, $GOOGLEMAP_PH_YSIZE, $GOOGLEMAP_MAP_TYPE, $TEXT_DIRECTION;
	// create the map
	//<!-- start of map display -->
	echo "\n<br /><br />\n";
	echo "<table class=\"width80\"><tr valign=\"top\"><td class=\"center\">";
	echo "<div id=\"place_map\" style=\"border: 1px solid gray; width: ", $GOOGLEMAP_PH_XSIZE, "px; height: ", $GOOGLEMAP_PH_YSIZE, "px; ";
	echo "background-image: url('images/loading.gif'); background-position: center; background-repeat: no-repeat; overflow: hidden;\"></div>";
	?>
	<!-- Start of map scripts -->
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $GOOGLEMAP_API_KEY; ?>" type="text/javascript"></script>
	<script src="modules/googlemap/pgvGoogleMap.js" type="text/javascript"></script>
	<?php
	if (WT_USER_IS_ADMIN) {
		echo "<table style=\"width: ", $GOOGLEMAP_PH_XSIZE, "px\">";
		echo "<tr><td align=\"left\">\n";
		echo "<a href=\"module.php?mod=googlemap&amp;pgvaction=editconfig\">", i18n::translate('Manage GoogleMap configuration'), "</a>";
		echo "</td>\n";
		echo "<td align=\"center\">\n";
		echo "<a href=\"module.php?mod=googlemap&pgvaction=places\">", i18n::translate('Edit geographic place locations'), "</a>";
		echo "</td>\n";
		echo "<td align=\"right\">\n";
		echo "<a href=\"module.php?mod=googlemap&pgvaction=placecheck\">", i18n::translate('Place Check'), "</a>";
		echo "</td></tr>\n";
		echo "</table>\n";
	}
	echo "</td><td style=\"margin-left:15; vertical-align: top;\">";
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
	echo "<br /><br />", i18n::translate('Individuals'), ": ", $place_count_indi, ", ", i18n::translate('Families'), ": ", $place_count_fam;
}

function print_gm_markers($place2, $level, $parent, $levelm, $linklevels, $placelevels, $lastlevel=false){
	global $GOOGLEMAP_COORD, $GOOGLEMAP_PH_MARKER, $GM_DISP_SHORT_PLACE, $GM_DISP_COUNT;
	if (($place2['lati'] == NULL) || ($place2['long'] == NULL) || (($place2['lati'] == "0") && ($place2['long'] == "0"))) {
		echo "var icon_type = new GIcon();\n";
			echo "	icon_type.image = \"modules/googlemap/images/marker_yellow.png\";\n";
			echo "	icon_type.shadow = \"modules/googlemap/images/shadow50.png\";\n";
			echo "	icon_type.iconSize = new GSize(20, 34);\n";
			echo "	icon_type.shadowSize = new GSize(37, 34);\n";
			echo "	icon_type.iconAnchor = new GPoint(6, 20);\n";
			echo "	icon_type.infoWindowAnchor = new GPoint(5, 1);\n";
		echo "var point = new GLatLng(0, 0);\n";
		if ($lastlevel)
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", $level, $linklevels, "'><br />";
		else {
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", ($level+1), $linklevels, "&amp;parent[{$level}]=";
			if ($place2['place'] == "Unknown") echo "'><br />";
			else echo urlencode($place2['place']), "'><br />";
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
					echo i18n::translate('Unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(substr($placelevels, 2));
				} else {
					echo PrintReady(addslashes($place2['place']));
				}
			}
		} else {
			$placename = $place2['place'].$placelevels;
			if ($place2['place'] == "Unknown") {
				if (!$GM_DISP_SHORT_PLACE) {
					echo PrintReady(addslashes(i18n::translate('Unknown').$placelevels));
				} else {
					echo i18n::translate('Unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo PrintReady(addslashes($place2['place'].$placelevels));
				} else {
					echo PrintReady(addslashes($place2['place']));
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
		echo "<br />", i18n::translate('This place has no coordinates');
		if (WT_USER_IS_ADMIN)
			echo "<br /><a href='module.php?mod=googlemap&pgvaction=places&parent=", $levelm, "&display=inactive'>", i18n::translate('Edit geographic location'), "</a>";
		echo "</div></td>\", icon_type, \"", str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), PrintReady(addslashes($place2['place']))), "\");\n";
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
		if (($place2["icon"] == NULL) || ($place2['icon'] == "") || ($GOOGLEMAP_PH_MARKER != "G_FLAG")) {
			echo "var icon_type = new GIcon(G_DEFAULT_ICON);\n";
		} else {
			echo "var icon_type = new GIcon();\n";
			echo "	icon_type.image = \"", $place2['icon'], "\";\n";
			echo "	icon_type.shadow = \"modules/googlemap/images/flag_shadow.png\";\n";
			echo "	icon_type.iconSize = new GSize(25, 15);\n";
			echo "	icon_type.shadowSize = new GSize(35, 45);\n";
			echo "	icon_type.iconAnchor = new GPoint(1, 45);\n";
			echo "	icon_type.infoWindowAnchor = new GPoint(5, 1);\n";
		}
		echo "var point = new GLatLng({$lati}, {$long});\n";
		if ($lastlevel) {
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", $level, $linklevels, "'><br />";
		} else {
			echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><a href='?level=", ($level+1), $linklevels, "&amp;parent[{$level}]=";
			if ($place2['place'] == "Unknown") {
				echo "'><br />";
			} else {
				echo urlencode($place2['place']), "'><br />";
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
					echo i18n::translate('Unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo addslashes(substr($placelevels, 2));
				} else {
					echo PrintReady(addslashes($place2['place']));
				}
			}
		} else {
			$placename = $place2['place'].$placelevels;
			if ($place2['place'] == "Unknown"){
				if (!$GM_DISP_SHORT_PLACE) {
					echo PrintReady(addslashes(i18n::translate('Unknown').$placelevels));
				} else {
					echo i18n::translate('Unknown');
				}
			} else {
				if (!$GM_DISP_SHORT_PLACE) {
					echo PrintReady(addslashes($place2['place'].$placelevels));
				} else {
					echo PrintReady(addslashes($place2['place']));
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
		$temp=PrintReady(addslashes($place2['place']));
		$temp=str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), $temp);
		if (!$GOOGLEMAP_COORD){
			echo "<br /><br /></div></td>\", icon_type, \"", $temp, "\");\n";
		} else { 
			echo "<br /><br />", $place2['lati'], ", ", $place2['long'], "</div></td>\", icon_type, \"", $temp, "\");\n";	
		}		
	}
	echo "place_map.addOverlay(marker);\n";
	echo "bounds.extend(point);\n";
}

function create_buttons($numfound, $level) {
	?>
	<style type="text/css">
	#map_type
	{
		margin: 0;
		padding: 0;
		font-family: Arial;
		font-size: 10px;
		list-style: none;
	}
	#map_type li
	{
		display: block;
		width: 70px;
		text-align: center;
		padding: 2px;
		border: 1px solid black;
		cursor: pointer;
		float: left;
		margin-left: 2px;
	}
	#map_type li.non_active
	{
		background: white;
		color: black;
		font-weight: normal;
	}
	#map_type li.active
	{
		background: gray;
		color: white;
		font-weight: bold;
	}
	#map_type li:hover
	{
		background: #ddd;
	}
	</style>
	<?php
}

function map_scripts($numfound, $level, $parent, $linklevels, $placelevels, $place_names) {
	global $GOOGLEMAP_MAP_TYPE, $GM_MAX_NOF_LEVELS, $GOOGLEMAP_PH_WHEEL, $GOOGLEMAP_PH_CONTROLS;
	?>
	<?php echo create_buttons($numfound, $level);?>
	<script type="text/javascript">
	// <![CDATA[
	if (window.attachEvent) {
		window.attachEvent("onunload", function() {
			GUnload();	  // Internet Explorer
		});
	} else {
		window.addEventListener("unload", function() {
			GUnload(); // Firefox and standard browsers
		}, false);
	}
	if (GBrowserIsCompatible()) {
	// Creates a marker whose info window displays the given name
	function createMarker(point, html, icon, name)
	{
		var marker = new GMarker(point, {icon:icon, title:name});
		// Show this markers name in the info window when it is clicked
		GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(html);});
		return marker;
	};

	function Map_type() {}
	Map_type.prototype = new GControl();

	Map_type.prototype.refresh = function()
	{
		this.button1.className = 'non_active';
		if(this.place_map.getCurrentMapType() != G_NORMAL_MAP) {
			this.button2.className = 'non_active';
		} else {
			this.button2.className = 'active';
		}
		if(this.place_map.getCurrentMapType() != G_SATELLITE_MAP) {
			this.button3.className = 'non_active';
		} else {
			this.button3.className = 'active';
		}
		if(this.place_map.getCurrentMapType() != G_HYBRID_MAP) {
			this.button4.className = 'non_active';
		} else {
			this.button4.className = 'active';
		}
		if(this.place_map.getCurrentMapType() != G_PHYSICAL_MAP) {
			this.button5.className = 'non_active';
		} else {
			this.button5.className = 'active';
		}
	}

	Map_type.prototype.initialize = function(place_map)
	{
		var list 	= document.createElement("ul");
		list.id	= 'map_type';

		var button1 = document.createElement('li');
		var button2 = document.createElement('li');
		var button3 = document.createElement('li');
		var button4 = document.createElement('li');
		var button5 = document.createElement('li');

		button1.innerHTML = '<?php echo i18n::translate('Redraw map')?>';
		button2.innerHTML = '<?php echo i18n::translate('Map')?>';
		button3.innerHTML = '<?php echo i18n::translate('Satellite')?>';
		button4.innerHTML = '<?php echo i18n::translate('Hybrid')?>';
		button5.innerHTML = '<?php echo i18n::translate('Terrain')?>';

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
	var bounds = new GLatLngBounds();
	// for further street view
	//place_map.addControl(new GLargeMapControl3D(true));
	place_map.addControl(new GLargeMapControl3D());
	place_map.addControl(new GScaleControl());
	var mini = new GOverviewMapControl();
	place_map.addControl(mini);
	// displays blank minimap - probably google api's bug
	//mini.hide();
	<?php
	if (check_exist_table()) {
		$levelm = set_levelm($level, $parent);
		if (isset($levelo[0])) $levelo[0]=0;
		$numls = count($parent)-1;
		$levelo=check_were_am_i($numls, $levelm);
		if ($numfound<2 && ($level==1 || !(isset($levelo[($level-1)])))){
			echo "zoomlevel = place_map.getBoundsZoomLevel(bounds);\n";
			echo "	place_map.setCenter(new GLatLng(0, 0), zoomlevel+5);\n";
		}
		else if ($numfound<2 && !isset($levelo[($level-2)])){
			echo "zoomlevel = place_map.getBoundsZoomLevel(bounds);\n";
			echo "	place_map.setCenter(new GLatLng(0, 0), zoomlevel+6);\n";
		}
		else if ($level==2){
			echo "zoomlevel = place_map.getBoundsZoomLevel(bounds);\n";
			echo "	place_map.setCenter(new GLatLng(0, 0), zoomlevel+8);\n";
		}
		else if ($numfound<2 && $level>1){
			echo "zoomlevel = place_map.getBoundsZoomLevel(bounds);\n";
			echo "	place_map.setCenter(new GLatLng(0, 0), zoomlevel+10);\n";
		}
		else
			echo "place_map.setCenter(new GLatLng(0, 0), 1);\n";
		if ($GOOGLEMAP_PH_WHEEL) echo "place_map.enableScrollWheelZoom();\n";
		echo "	place_map.setMapType($GOOGLEMAP_MAP_TYPE);\n";
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
							if ($place2['place_id']==$levelo[$level])
								print_gm_markers($place2, $level, $parent, $levelo[($level-1)], $linklevels, $placelevels, true);
						}
						else {
							echo "var icon_type = new GIcon();\n";
							echo "icon_type.image = \"modules/googlemap/images/marker_yellow.png\"\n";
							echo "icon_type.shadow = \"modules/googlemap/images/shadow50.png\";\n";
							echo "icon_type.iconSize = new GSize(20, 34);\n";
							echo "icon_type.shadowSize = new GSize(37, 34);\n";
							echo "icon_type.iconAnchor = new GPoint(6, 20);\n";
							echo "icon_type.infoWindowAnchor = new GPoint(5, 1);\n";
							echo "var point = new GLatLng(0, 0);\n";
							echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'><b>";
							echo substr($placelevels, 2), "</b><br />", i18n::translate('This place has no coordinates');
							if (WT_USER_IS_ADMIN)
								echo "<br /><a href='module.php?mod=googlemap&pgvaction=places&parent=0&display=inactive'>", i18n::translate('Edit geographic location'), "</a>";
							echo "<br /></div></td>\", icon_type, \"", i18n::translate('Edit geographic location'), "\");\n";
							echo "place_map.addOverlay(marker);\n";
							echo "bounds.extend(point);\n";
							break;
						}
					}
				}
				else {
					//lastlevel place not in table
					$placelist2=get_place_list_loc($levelo[($level-1)], true);
					foreach ($placelist2 as $place2) {
						if (isset($levelo[$level])) {
							if ($place2['place_id']==$levelo[$level])
								print_gm_markers($place2, $level, $parent, $levelo[$level], $linklevels, $placelevels, true);
						}
					}
				}
			}
		}
		else {
			//place from table
			$placelist2=get_place_list_loc($levelm);
			if (!empty($placelist2)) {
				foreach ($placelist2 as $place2) {
					if (check_place($place_names, $place2['place']))
						print_gm_markers($place2, $level, $parent, $levelm, $linklevels, $placelevels);
				}
			}
			else if ($level>0){ //if unknown place display the upper level place
				$placelevels = ", ".i18n::translate('Unknown').$placelevels;
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
	}
	else {
		echo "var icon_type = new GIcon();\n";
		echo "icon_type.image = \"modules/googlemap/images/marker_yellow.png\"";
		echo "var point = new GLatLng(0, 0);\n";
		echo "var marker = createMarker(point, \"<td width='100%'><div class='iwstyle' style='width: 250px;'>";
		echo "<br />", i18n::translate('This place has no coordinates');
		if (WT_USER_IS_ADMIN)
			echo "<br /><a href='module.php?mod=googlemap&pgvaction=places&parent=0&display=inactive'>", i18n::translate('Edit geographic location'), "</a>";
		echo "<br /></div></td>\", icon_type, \"", i18n::translate('Edit geographic location'), "\");\n";
		echo "place_map.addOverlay(marker);\n";
		echo "bounds.extend(point);\n";
	}
	?>
	//end markers
	place_map.setCenter(bounds.getCenter());
	<?php if ($GOOGLEMAP_PH_CONTROLS) {?>
		// hide controls
		GEvent.addListener(place_map, 'mouseout', function() {place_map.hideControls();});
		// show controls
		GEvent.addListener(place_map, 'mouseover', function() {place_map.showControls();});
		GEvent.trigger(place_map, 'mouseout');
		<?php
	}
	if ($numfound>1)
		echo "place_map.setZoom(place_map.getBoundsZoomLevel(bounds));\n";
	?>
	} else {
	  alert("Sorry, the Google Maps API is not compatible with this browser");
	}
	// This Javascript is based on code provided by the
	// Blackpool Community Church Javascript Team
	// http://www.commchurch.freeserve.co.uk/
	// http://econym.googlepages.com/index.htm
	//]]>
	</script>
	<?php
}
?>
