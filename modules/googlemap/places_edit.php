<?php
/**
 * Interface to edit place locations
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @subpackage Edit
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require WT_ROOT.'modules/googlemap/defaultconfig.php';
require WT_ROOT.'includes/functions/functions_edit.php';

if (isset($_REQUEST['placeid'])) $placeid = $_REQUEST['placeid'];
if (isset($_REQUEST['place_name'])) $place_name = $_REQUEST['place_name'];
if (isset($_REQUEST['action']))  $action  = $_REQUEST['action'];

print_simple_header(i18n::translate('Edit geographic place locations'));

if (!WT_USER_IS_ADMIN) {
	echo "<table class=\"facts_table\">\n";
	echo "<tr><td colspan=\"2\" class=\"facts_value\">", i18n::translate('Page only for Administrators');
	echo "</td></tr></table>\n";
	echo "<br /><br /><br />\n";
	print_simple_footer();
	exit;
}
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
<script type="text/javascript">
<!--
	function edit_close() {
		if (window.opener.showchanges) window.opener.showchanges();
		window.close();
	}

function showchanges() {
	updateMap();
}
//-->
</script>
<?php

// Take a place id and find its place in the hierarchy
// Input: place ID
// Output: ordered array of id=>name values, starting with the Top Level
// e.g. array(0=>"Top Level", 16=>"England", 19=>"London", 217=>"Westminster");
// NB This function exists in both places.php and places_edit.php
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

// NB This function exists in both places.php and places_edit.php
function getHighestIndex() {
	global $TBLPREFIX;

	return (int)WT_DB::prepare("SELECT MAX(pl_id) FROM {$TBLPREFIX}placelocation")->fetchOne();
}

$where_am_i=place_id_to_hierarchy($placeid);
$level=count($where_am_i);

if ($action=='addrecord' && WT_USER_IS_ADMIN) {
	$statement=
		WT_DB::prepare("INSERT INTO {$TBLPREFIX}placelocation (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

	if (($_POST['LONG_CONTROL'] == '') || ($_POST['NEW_PLACE_LONG'] == '') || ($_POST['NEW_PLACE_LATI'] == '')) {
		$statement->execute(array(getHighestIndex()+1, $placeid, $level, stripLRMRLM($_POST['NEW_PLACE_NAME']), null, null, $_POST['NEW_ZOOM_FACTOR'], $_POST['icon']));
	} else {
		$statement->execute(array(getHighestIndex()+1, $placeid, $level, stripLRMRLM($_POST['NEW_PLACE_NAME']), $_POST['LONG_CONTROL'][3].$_POST['NEW_PLACE_LONG'], $_POST['LATI_CONTROL'][3].$_POST['NEW_PLACE_LATI'], $_POST['NEW_ZOOM_FACTOR'], $_POST['icon']));
	}

	if ($EDIT_AUTOCLOSE && !WT_DEBUG) {
		echo "\n<script type=\"text/javascript\">\n<!--\nedit_close();\n//-->\n</script>";
	}
	echo "<div class=\"center\"><a href=\"javascript:;\" onclick=\"edit_close();return false;\">", i18n::translate('Close Window'), "</a></div><br />\n";
	print_simple_footer();
	exit;
}

if ($action=='updaterecord' && WT_USER_IS_ADMIN) {
	$statement=
		WT_DB::prepare("UPDATE {$TBLPREFIX}placelocation SET pl_place=?, pl_lati=?, pl_long=?, pl_zoom=?, pl_icon=? WHERE pl_id=?");

	if (($_POST['LONG_CONTROL'] == '') || ($_POST['NEW_PLACE_LONG'] == '') || ($_POST['NEW_PLACE_LATI'] == '')) {
		$statement->execute(array(stripLRMRLM($_POST['NEW_PLACE_NAME']), null, null, $_POST['NEW_ZOOM_FACTOR'], $_POST['icon'], $placeid));
	} else {
		$statement->execute(array(stripLRMRLM($_POST['NEW_PLACE_NAME']), $_POST['LATI_CONTROL'][3].$_POST['NEW_PLACE_LATI'], $_POST['LONG_CONTROL'][3].$_POST['NEW_PLACE_LONG'], $_POST['NEW_ZOOM_FACTOR'], $_POST['icon'], $placeid));
	}

	if ($EDIT_AUTOCLOSE && !WT_DEBUG) {
		echo "\n<script type=\"text/javascript\">\n<!--\nedit_close();\n//-->\n</script>";
	}
	echo "<div class=\"center\"><a href=\"javascript:;\" onclick=\"edit_close();return false;\">", i18n::translate('Close Window'), "</a></div><br />\n";
	print_simple_footer();
	exit;
}

if ($action=="update") {
	// --- find the place in the file
	$row=
		WT_DB::prepare("SELECT pl_place, pl_lati, pl_long, pl_icon, pl_parent_id, pl_level, pl_zoom FROM {$TBLPREFIX}placelocation WHERE pl_id=?")
		->execute(array($placeid))
		->fetchOneRow();
	$place_name = $row->pl_place;
	$place_icon = $row->pl_icon;
	$selected_country = explode("/", $place_icon);
	if (isset($selected_country[1]) && $selected_country[1]!="flags")
		$selected_country = $selected_country[1];
	else
		$selected_country = "Countries";
	$parent_id	= $row->pl_parent_id;
	$level		= $row->pl_level;
	$zoomfactor	= $row->pl_zoom;
	$parent_lati = "0.0";
	$parent_long = "0.0";
	if ($row->pl_lati!==null && $row->pl_long!==null) {
		$place_lati = (float)(str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati));
		$place_long = (float)(str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long));
		$show_marker = true;
	} else {
		$place_lati = null;
		$place_long = null;
		$zoomfactor = 1;
		$show_marker = false;
	}

	do {
		$row=
			WT_DB::prepare("SELECT pl_lati, pl_long, pl_parent_id, pl_zoom FROM {$TBLPREFIX}placelocation WHERE pl_id=?")
			->execute(array($parent_id))
			->fetchOneRow();
		if (!$row) {
			break;
		}
		if ($row->pl_lati!==null && $row->pl_long!==null) {
			$parent_lati = (float)(str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati));
			$parent_long = (float)(str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long));
			if ($zoomfactor == 1) {
				$zoomfactor = $row->pl_zoom;
			}
		}
		$parent_id = $row->pl_parent_id;
	} while ($row->pl_parent_id!=0 && $row->pl_lati===null && $row->pl_long===null);

	$success = false;

	echo "<b>", str_replace("Unknown", i18n::translate('Unknown'), PrintReady(implode(', ', array_reverse($where_am_i, true)))), "</b><br />\n";
}

if ($action=="add") {
	// --- find the parent place in the file
	if ($placeid != 0) {
		if (!isset($place_name)) $place_name  = "";
		$place_lati = null;
		$place_long = null;
		$zoomfactor = 1;
		$parent_lati = "0.0";
		$parent_long = "0.0";
		$place_icon = "";
		$parent_id=$placeid;
		do {
			$row=
				WT_DB::prepare("SELECT pl_lati, pl_long, pl_parent_id, pl_zoom, pl_level FROM {$TBLPREFIX}placelocation WHERE pl_id=?")
				->execute(array($parent_id))
				->fetchOneRow();
			if ($row->pl_lati!==null && $row->pl_long!==null) {
				$parent_lati=str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati);
				$parent_long=str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long);
				$zoomfactor=$row->pl_zoom+2;
				if ($zoomfactor>$GOOGLEMAP_MAX_ZOOM) {
					$zoomfactor=$GOOGLEMAP_MAX_ZOOM;
				}
				$level=$row->pl_level+1;
			}
			$parent_id = $row->pl_parent_id;
		} while ($row->pl_parent_id!=0 && $row->pl_lati===null && $row->pl_long===null);
	}
	else {
		if (!isset($place_name)) $place_name  = "";
		$place_lati  = null;
		$place_long  = null;
		$parent_lati = "0.0";
		$parent_long = "0.0";
		$place_icon  = "";
		$parent_id   = 0;
		$level		 = 0;
		$zoomfactor  = $GOOGLEMAP_MIN_ZOOM;
	}
	$selected_country = "Countries";
	$show_marker = false;
	$success = false;

	if (!isset($place_name) || $place_name=="") echo "<b>", i18n::translate('Unknown');
	else echo "<b>", $place_name;
	if (count($where_am_i)>0)
		echo ", ", str_replace("Unknown", i18n::translate('Unknown'), PrintReady(implode(', ', array_reverse($where_am_i, true)))), "</b><br />\n";
	echo "</b><br />";
}

?>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $GOOGLEMAP_API_KEY?>" type="text/javascript"></script>
<script type="text/javascript">
<!--
	if (window.attachEvent) {
		window.attachEvent("onload", function() {
			loadMap();	   // Internet Explorer
		});
		window.attachEvent("onunload", function() {
			GUnload();	   // Internet Explorer
		});
	} else {
		window.addEventListener("load", function() {
			loadMap(); // Firefox and standard browsers
		}, false);
		window.addEventListener("unload", function() {
			GUnload(); // Firefox and standard browsers
		}, false);
	}
	var childplaces = [];
	var geocoder = new GClientGeocoder();

	function updateMap() {
		var point;
		var zoom;
		var latitude;
		var longitude;
		var i;

		document.editplaces.save1.disabled = "";
		document.editplaces.save2.disabled = "";
		zoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);

		prec = 20;
		for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
			if (document.editplaces.NEW_PRECISION[i].checked) {
				prec = document.editplaces.NEW_PRECISION[i].value;
			}
		}
		if ((document.editplaces.NEW_PLACE_LATI.value == "") ||
			(document.editplaces.NEW_PLACE_LONG.value == "")) {
			latitude = parseFloat(document.editplaces.parent_lati.value).toFixed(prec);
			longitude = parseFloat(document.editplaces.parent_long.value).toFixed(prec);
			point = new GLatLng (latitude, longitude);
			map.clearOverlays();
		} else {
			latitude = parseFloat(document.editplaces.NEW_PLACE_LATI.value).toFixed(prec);
			longitude = parseFloat(document.editplaces.NEW_PLACE_LONG.value).toFixed(prec);
			document.editplaces.NEW_PLACE_LATI.value = latitude;
			document.editplaces.NEW_PLACE_LONG.value = longitude;

			if (latitude < 0.0) {
				latitude = latitude * -1;
				document.editplaces.NEW_PLACE_LATI.value = latitude;
			}
			if (longitude < 0.0) {
				longitude = longitude * -1;
				document.editplaces.NEW_PLACE_LONG.value = longitude;
			}
			if(document.editplaces.LATI_CONTROL.value == "PL_S") {
				latitude = latitude * -1;
			}
			if(document.editplaces.LONG_CONTROL.value == "PL_W") {
				longitude = longitude * -1;
			}

			point = new GLatLng (latitude, longitude);

			map.clearOverlays();

			if (document.editplaces.icon.value == "") {
				map.addOverlay(new GMarker(point));
			}
			else {
				var flagicon = new GIcon();
				flagicon.image = document.editplaces.icon.value;
				flagicon.shadow = "modules/googlemap/images/flag_shadow.png";
				flagicon.iconSize = new GSize(25, 15);
				flagicon.shadowSize = new GSize(35, 45);
				flagicon.iconAnchor = new GPoint(1, 45);
				flagicon.infoWindowAnchor = new GPoint(5, 1);
				map.addOverlay(new GMarker(point, flagicon));
			}
		}

		map.setCenter(point, zoom);
		//document.getElementById('resultDiv').innerHTML = "";

		var childicon = new GIcon();
		childicon.image = "http://labs.google.com/ridefinder/images/mm_20_green.png";
		childicon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
		childicon.iconSize = new GSize(12, 20);
		childicon.shadowSize = new GSize(22, 20);
		childicon.iconAnchor = new GPoint(6, 20);
		childicon.infoWindowAnchor = new GPoint(5, 1);
		for (i=0; i < childplaces.length; i++) {
			map.addOverlay(childplaces[i]);
		}
	}

	function Map_type() {}
	Map_type.prototype = new GControl();

	Map_type.prototype.refresh = function()
	{
		if(this.map.getCurrentMapType() != G_NORMAL_MAP)
			this.button1.className = 'non_active';
		else
			this.button1.className = 'active';
		if(this.map.getCurrentMapType() != G_SATELLITE_MAP)
			this.button2.className = 'non_active';
		else
			this.button2.className = 'active';
		if(this.map.getCurrentMapType() != G_HYBRID_MAP)
			this.button3.className = 'non_active';
		else
			this.button3.className = 'active';
		if(this.map.getCurrentMapType() != G_PHYSICAL_MAP)
			this.button4.className = 'non_active';
		else
			this.button4.className = 'active';
	}

	Map_type.prototype.initialize = function(place_map)
	{
		var list 	= document.createElement("ul");
		list.id	= 'map_type';

		var button1 = document.createElement('li');
		var button2 = document.createElement('li');
		var button3 = document.createElement('li');
		var button4 = document.createElement('li');

		button1.innerHTML = '<?php echo i18n::translate('Map')?>';
		button2.innerHTML = '<?php echo i18n::translate('Satellite')?>';
		button3.innerHTML = '<?php echo i18n::translate('Hybrid')?>';
		button4.innerHTML = '<?php echo i18n::translate('Terrain')?>';

		button1.onclick = function() { map.setMapType(G_NORMAL_MAP); return false; };
		button2.onclick = function() { map.setMapType(G_SATELLITE_MAP); return false; };
		button3.onclick = function() { map.setMapType(G_HYBRID_MAP); return false; };
		button4.onclick = function() { map.setMapType(G_PHYSICAL_MAP); return false; };

		list.appendChild(button1);
		list.appendChild(button2);
		list.appendChild(button3);
		list.appendChild(button4);

		this.button1 = button1;
		this.button2 = button2;
		this.button3 = button3;
		this.button4 = button4;
		this.map = map;
		map.getContainer().appendChild(list);
		return list;
	}

	Map_type.prototype.getDefaultPosition = function()
	{
		return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(2, 2));
	}

	function loadMap() {
		var zoom;
		if (GBrowserIsCompatible()) {
			map = new GMap2(document.getElementById("map_pane"));
			map.addControl(new GSmallZoomControl3D());
			map.addControl(new GScaleControl()) ;
			var bounds = new GLatLngBounds();
			var map_type;
			map_type = new Map_type();
			map.addControl(map_type);
			GEvent.addListener(map, 'maptypechanged', function()
			{
				map_type.refresh();
			});
			GEvent.addListener(map, 'click', function(overlay, point) {
				if (overlay) {
					//probably not needed in this case
					//map.removeOverlay(overlay);
				} else if (point) {
					map.clearOverlays();
					// Create our "tiny" yellow marker icon where the user clicked,
					// The full size red marker is at the stored coordinates.
					var smicon = new GIcon();
					smicon.image = "http://labs.google.com/ridefinder/images/mm_20_yellow.png";
					smicon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
					smicon.iconSize = new GSize(12, 20);
					smicon.shadowSize = new GSize(22, 20);
					smicon.iconAnchor = new GPoint(6, 20);
					smicon.infoWindowAnchor = new GPoint(5, 1);

					map.panTo(point);
					prec = 20;
					for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
						if (document.editplaces.NEW_PRECISION[i].checked) {
							prec = document.editplaces.NEW_PRECISION[i].value;
						}
					}

					if (point.y < 0.0) {
						document.editplaces.NEW_PLACE_LATI.value = (point.y.toFixed(prec) * -1);
						document.editplaces.LATI_CONTROL.value = "PL_S";
					} else {
						document.editplaces.NEW_PLACE_LATI.value = point.y.toFixed(prec);
						document.editplaces.LATI_CONTROL.value = "PL_N";
					}
					if (point.x < 0.0) {
						document.editplaces.NEW_PLACE_LONG.value = (point.x.toFixed(prec) * -1);
						document.editplaces.LONG_CONTROL.value = "PL_W";
					} else {
						document.editplaces.NEW_PLACE_LONG.value = point.x.toFixed(prec);
						document.editplaces.LONG_CONTROL.value = "PL_E";
					}
					newval = new GLatLng (point.y.toFixed(prec), point.x.toFixed(prec));
					if (document.editplaces.icon.value == "") {
						map.addOverlay(new GMarker(newval));
					}
					else {
						var flagicon = new GIcon();
						flagicon.image = document.editplaces.icon.value;
						flagicon.shadow = "modules/googlemap/images/flag_shadow.png";
						flagicon.iconSize = new GSize(25, 15);
						flagicon.shadowSize = new GSize(35, 45);
						flagicon.iconAnchor = new GPoint(1, 45);
						flagicon.infoWindowAnchor = new GPoint(5, 1);
						map.addOverlay(new GMarker(newval, flagicon));
					}
					// Trying to get the smaller yellow icon drawn in front.
					map.addOverlay(new GMarker(point, smicon));
					//document.getElementById('resultDiv').innerHTML = "";
					document.editplaces.save1.disabled = "";
					document.editplaces.save2.disabled = "";
					var childicon = new GIcon();
					childicon.image = "http://labs.google.com/ridefinder/images/mm_20_green.png";
					childicon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
					childicon.iconSize = new GSize(12, 20);
					childicon.shadowSize = new GSize(22, 20);
					childicon.iconAnchor = new GPoint(6, 20);
					childicon.infoWindowAnchor = new GPoint(5, 1);
					for (i=0; i < childplaces.length; i++) {
						map.addOverlay(childplaces[i]);
					}
			}});
			GEvent.addListener(map, "moveend", function() {
				document.editplaces.NEW_ZOOM_FACTOR.value = map.getZoom();
			});
<?php if(($place_long == null) || ($place_lati == null)) { ?>
			map.setCenter(new GLatLng( <?php echo $parent_lati, ", ", $parent_long, "), ", $zoomfactor;?>, G_NORMAL_MAP );
<?php }else { ?>
			map.setCenter(new GLatLng( <?php echo $place_lati, ", ", $place_long, "), ", $zoomfactor;?>, G_NORMAL_MAP );
<?php } ?>

<?php   if ($level < 3) { ?>
			var childicon = new GIcon();
			childicon.image = "http://labs.google.com/ridefinder/images/mm_20_green.png";
			childicon.shadow = "http://labs.google.com/ridefinder/images/mm_20_shadow.png";
			childicon.iconSize = new GSize(12, 20);
			childicon.shadowSize = new GSize(22, 20);
			childicon.iconAnchor = new GPoint(6, 20);
			childicon.infoWindowAnchor = new GPoint(5, 1);
<?php
			$rows=
				WT_DB::prepare("SELECT pl_place, pl_lati, pl_long, pl_icon FROM {$TBLPREFIX}placelocation WHERE pl_parent_id=?")
				->execute(array($placeid))
				->fetchAll();
			$i = 0;
			foreach ($rows as $row) {
				if ($row->pl_lati!==null && $row->pl_long!==null) {
					//delete leading zero
					$pl_lati = str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati);
					$pl_long = str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long);
					if ($pl_lati >= 0) {
						$row->pl_lati = abs($pl_lati);
					} elseif ($pl_lati < 0) {
						$row->pl_lati = "-".abs($pl_lati);
					}
					if ($pl_long >= 0) {
						$row->pl_long = abs($pl_long);
					} elseif ($pl_long < 0) {
						$row->pl_long = "-".abs($pl_long);
					}

					echo "	 	 	 childplaces.push(new GMarker(new GLatLng(", $row->pl_lati, ", ", $row->pl_long, "), childicon));\n";
					echo "			 GEvent.addListener(childplaces[", $i, "], \"click\", function() {\n";
					echo "             childplaces[", $i, "].openInfoWindowHtml(\"<td width='100%'><div class='iwstyle' style='width: 250px;'><br />", addslashes($row->pl_place), "<br /><br /></div>\")});\n";
					echo "	 	 	 map.addOverlay(childplaces[", $i, "]);\n";
					echo "	 	 	 bounds.extend(new GLatLng(", $row->pl_lati, ", ", $row->pl_long, "));\n";
					$i++;
					echo "	 	 	 map.setCenter(bounds.getCenter());\n";
				}
			}
		}
		if ($show_marker == true) {
			if (($place_icon == NULL) || ($place_icon == "")) {
				if (($place_lati == null) || ($place_long == null)) {?>
					var icon_type = new GIcon();
					icon_type.image = "modules/googlemap/images/marker_yellow.png";
					icon_type.shadow = "modules/googlemap/images/shadow50.png";
					icon_type.iconSize = new GSize(20, 34);
					icon_type.shadowSize = new GSize(37, 34);
					icon_type.iconAnchor = new GPoint(10, 34);
					icon_type.infoWindowAnchor = new GPoint(5, 1);
					map.addOverlay(new GMarker(new GLatLng(<?php echo $parent_lati, ", ", $parent_long;?>), icon_type));
<?php			} else { ?>
					map.addOverlay(new GMarker(new GLatLng(<?php echo $place_lati, ", ", $place_long;?>)));
<?php			}
			}
			else { ?>
			var flagicon = new GIcon();
			flagicon.image = "<?php echo $place_icon;?>";
			flagicon.shadow = "modules/googlemap/images/flag_shadow.png";
			flagicon.iconSize = new GSize(25, 15);
			flagicon.shadowSize = new GSize(35, 45);
			flagicon.iconAnchor = new GPoint(1, 45);
			flagicon.infoWindowAnchor = new GPoint(5, 1);
<?php			if (($place_lati == null) || ($place_long == null)) {?>
			map.addOverlay(new GMarker(new GLatLng(<?php echo $parent_lati, ", ", $parent_long;?>), flagicon));
<?php			} else { ?>
			map.addOverlay(new GMarker(new GLatLng(<?php echo $place_lati, ", ", $place_long;?>), flagicon));
<?php			}
			}
		} ?>
			// Our info window content
		}
	}

	function edit_close() {
		if (window.opener.showchanges) window.opener.showchanges();
		GUnload();
		window.close();
	}

	function setLoc(lat, lng) {
		prec = 20;
		for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
			if (document.editplaces.NEW_PRECISION[i].checked) {
				prec = document.editplaces.NEW_PRECISION[i].value;
			}
		}

		if (lat < 0.0) {
			document.editplaces.NEW_PLACE_LATI.value = (lat.toFixed(prec) * -1);
			document.editplaces.LATI_CONTROL.value = "PL_S";
		} else {
			document.editplaces.NEW_PLACE_LATI.value = lat.toFixed(prec);
			document.editplaces.LATI_CONTROL.value = "PL_N";
		}
		if (lng < 0.0) {
			document.editplaces.NEW_PLACE_LONG.value = (lng.toFixed(prec) * -1);
			document.editplaces.LONG_CONTROL.value = "PL_W";
		} else {
			document.editplaces.NEW_PLACE_LONG.value = lng.toFixed(prec);
			document.editplaces.LONG_CONTROL.value = "PL_E";
		}
		newval = new GLatLng (lat.toFixed(prec), lng.toFixed(prec));
		updateMap();
	}

	function createMarker(point, name, coordinates) {
		var icon = new GIcon();
		icon.image = "modules/googlemap/images/marker_yellow.png";
		icon.shadow = "modules/googlemap/images/shadow50.png";
		icon.iconSize = new GSize(20, 34);
		icon.shadowSize = new GSize(37, 34);
		icon.iconAnchor = new GPoint(10, 34);
		icon.infoWindowAnchor = new GPoint(5, 1);

		var marker = new GMarker(point, icon);
		GEvent.addListener(marker, "click", function() {
		marker.openInfoWindowHtml(name + "<br /><a href=\"javascript:;\" onclick=\"setLoc(" + coordinates[1] + ", " + coordinates[0] + ");\"><?php
 echo PrintReady(i18n::translate('Use this value'))?></a></div>");
		});
		return marker;
	}

	function change_icon() {
	window.open('module.php?mod=googlemap&pgvaction=flags&countrySelected=<?php echo $selected_country ?>', '_blank', 'top=50, left=50, width=600, height=500, resizable=1, scrollbars=1');
	return false;
	}

	function remove_icon() {
	document.editplaces.icon.value = "";
	document.getElementById('flagsDiv').innerHTML = "<a href=\"javascript:;\" onclick=\"change_icon();return false;\"><?php echo i18n::translate('Change flag')?></a>";
	}

	function addAddressToMap(response) {
	   map.clearOverlays();
	   var bounds = new GLatLngBounds();
	   if (!response || response.Status.code != 200) {
	 	 alert("<?php echo i18n::translate('No places found');?>");
	   } else {
		if(response.Placemark.length>0) {
			for (i=0;i<response.Placemark.length;i++) {
			place = response.Placemark[i];
			point = new GLatLng(place.Point.coordinates[1], place.Point.coordinates[0]);
			var name = '<td width=\'100%\'><div class=\'iwstyle\' style=\'width: 250px;\'>' + place.address + '<br />' + '<b><?php echo i18n::translate('Country')?>:</b> ' + place.AddressDetails.Country.CountryNameCode;
			var marker = createMarker(point, name, place.Point.coordinates);
			map.addOverlay(marker);
			bounds.extend(point);
			}
			zoomlevel = map.getBoundsZoomLevel(bounds)-1;
			if (zoomlevel < <?php echo $GOOGLEMAP_MIN_ZOOM;?>) zoomlevel = <?php echo $GOOGLEMAP_MIN_ZOOM;?>;
			if (zoomlevel > <?php echo $GOOGLEMAP_MAX_ZOOM;?>) zoomlevel = <?php echo $GOOGLEMAP_MAX_ZOOM;?>;
			if (document.editplaces.NEW_ZOOM_FACTOR.value<zoomlevel) {
				zoomlevel = document.editplaces.NEW_ZOOM_FACTOR.value;
				if (zoomlevel < <?php echo $GOOGLEMAP_MIN_ZOOM;?>) zoomlevel = <?php echo $GOOGLEMAP_MIN_ZOOM;?>;
				if (zoomlevel > <?php echo $GOOGLEMAP_MAX_ZOOM;?>) zoomlevel = <?php echo $GOOGLEMAP_MAX_ZOOM;?>;

			}
			map.setCenter(bounds.getCenter(), zoomlevel);
		}
	  }
	 }

	function showLocation_level(address) {
		address += '<?php if ($level>0) echo ", ", addslashes(PrintReady(implode(', ', array_reverse($where_am_i, true))));?>';
		geocoder.getLocations(address, addAddressToMap);
	}

	function showLocation_all(address) {
		geocoder.getLocations(address, addAddressToMap);
	}

	function updatewholename() {
	}

	function paste_char(value, lang, mag) {
		document.editplaces.NEW_PLACE_NAME.value += value;
		language_filter = lang;
		magnify = mag;
	}

	function edit_close(newurl) {
		if (newurl)
			window.opener.location=newurl;
		else
			if (window.opener.showchanges)
				window.opener.showchanges();
		window.close();
	}
	//-->
</script>

<form method="post" id="editplaces" name="editplaces" action="module.php?mod=googlemap&amp;pgvaction=places_edit">
	<input type="hidden" name="action" value="<?php echo $action;?>record" />
	<input type="hidden" name="placeid" value="<?php echo $placeid;?>" />
	<input type="hidden" name="level" value="<?php echo $level;?>" />
	<input type="hidden" name="icon" value="<?php echo $place_icon;?>" />
	<input type="hidden" name="parent_id" value="<?php echo $parent_id;?>" />
	<input type="hidden" name="place_long" value="<?php echo $place_long;?>" />
	<input type="hidden" name="place_lati" value="<?php echo $place_lati;?>" />
	<input type="hidden" name="parent_long" value="<?php echo $parent_long;?>" />
	<input type="hidden" name="parent_lati" value="<?php echo $parent_lati;?>" />
	<input name="save1" type="submit" value="<?php echo i18n::translate('Save');?>" /><br />

	<table class="facts_table">
	<tr>
		<td class="optionbox" colspan="2">
		<center><div id="map_pane" style="width: 100%; height: 300px"></div></center>
		</td>
	</tr>
	<tr>
		<td class="optionbox" colspan="2">
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('PLAC'), help_link('PLE_PLACES','googlemap');?></td>
		 <td class="optionbox"><input type="text" id="new_pl_name" name="NEW_PLACE_NAME" value="<?php echo htmlspecialchars($place_name) ?>" size="25" class="address_input" tabindex="<?php echo ++$i;?>" />
		<div id="INDI_PLAC_pop" style="display: inline;">
		<?php print_specialchar_link("NEW_PLACE_NAME", false);?></div>
		<label for="new_pl_name"><a href="javascript:;" onclick="showLocation_level(document.getElementById('new_pl_name').value); return false">&nbsp;<?php echo i18n::translate('Search on this level')?></a></label>&nbsp;&nbsp;|
	 	<label for="new_pl_name"><a href="javascript:;" onclick="showLocation_all(document.getElementById('new_pl_name').value); return false">&nbsp;<?php echo i18n::translate('Search all')?></a></label>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Precision'), help_link('PLE_PRECISION','googlemap');?></td>
		<?php
			$exp = explode(".", $place_lati);
			if (isset($exp[1])) {
				$precision1 = strlen($exp[1]);
			} else {
				$precision1 = -1;
			}
			$exp = explode(".", $place_long);
			if (isset($exp[1])) {
				$precision2 = strlen($exp[1]);
			} else {
				$precision2 = -1;
			}
			($precision1 > $precision2) ? ($precision = $precision1) : ($precision = $precision2);
			if ($precision == -1 ) ($level > 3) ? ($precision = 3) : ($precision = $level);
			elseif ($precision > 5) {
				$precision = 5;
			}
		?>
		<td class="optionbox">
			<input type="radio" id="new_prec_0" name="NEW_PRECISION" onchange="updateMap();" <?php if($precision==$GOOGLEMAP_PRECISION_0) echo "checked=\"checked\"";?> value="<?php echo $GOOGLEMAP_PRECISION_0;?>" tabindex="<?php echo ++$i;?>" />
			<label for="new_prec_0"><?php echo i18n::translate('Country');?></label>
			<input type="radio" id="new_prec_1" name="NEW_PRECISION" onchange="updateMap();" <?php if($precision==$GOOGLEMAP_PRECISION_1) echo "checked=\"checked\"";?> value="<?php echo $GOOGLEMAP_PRECISION_1;?>" tabindex="<?php echo ++$i;?>" />
			<label for="new_prec_1"><?php echo i18n::translate('State');?></label>
			<input type="radio" id="new_prec_2" name="NEW_PRECISION" onchange="updateMap();" <?php if($precision==$GOOGLEMAP_PRECISION_2) echo "checked=\"checked\"";?> value="<?php echo $GOOGLEMAP_PRECISION_2;?>" tabindex="<?php echo ++$i;?>" />
			<label for="new_prec_2"><?php echo i18n::translate('City');?></label>
			<input type="radio" id="new_prec_3" name="NEW_PRECISION" onchange="updateMap();" <?php if($precision==$GOOGLEMAP_PRECISION_3) echo "checked=\"checked\"";?> value="<?php echo $GOOGLEMAP_PRECISION_3;?>" tabindex="<?php echo ++$i;?>" />
			<label for="new_prec_3"><?php echo i18n::translate('Neighborhood');?></label>
			<input type="radio" id="new_prec_4" name="NEW_PRECISION" onchange="updateMap();"<?php if($precision==$GOOGLEMAP_PRECISION_4) echo "checked=\"checked\"";?> value="<?php echo $GOOGLEMAP_PRECISION_4;?>" tabindex="<?php echo ++$i;?>" />
			<label for="new_prec_4"><?php echo i18n::translate('House');?></label>
			<input type="radio" id="new_prec_5" name="NEW_PRECISION" onchange="updateMap();"<?php if($precision>$GOOGLEMAP_PRECISION_4) echo "checked=\"checked\"";?> value="<?php echo $GOOGLEMAP_PRECISION_5;?>" />
			<label for="new_prec_5"><?php echo i18n::translate('Max');?></label>
		</td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('LATI'), help_link('PLE_LATLON_CTRL','googlemap'); ?></td>
		<td class="optionbox">
			<select name="LATI_CONTROL" tabindex="<?php echo ++$i;?>" onchange="updateMap();">
				<option value="" <?php if ($place_lati == null) echo " selected=\"selected\"";?>></option>
				<option value="PL_N" <?php if ($place_lati > 0) echo " selected=\"selected\""; echo ">", i18n::translate_c('North', 'N'); ?></option>
				<option value="PL_S" <?php if ($place_lati < 0) echo " selected=\"selected\""; echo ">", i18n::translate_c('South', 'S'); ?></option>
			</select>
			<input type="text" name="NEW_PLACE_LATI" value="<?php if ($place_lati != null) echo abs($place_lati);?>" size="20" tabindex="<?php echo ++$i;?>" onchange="updateMap();" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('LONG'), help_link('PLE_LATLON_CTRL','googlemap'); ?></td>
		<td class="optionbox">
			<select name="LONG_CONTROL" tabindex="<?php echo ++$i;?>" onchange="updateMap();">
				<option value="" <?php if ($place_long == null) echo " selected=\"selected\"";?>></option>
				<option value="PL_E" <?php if ($place_long > 0) echo " selected=\"selected\""; echo ">", i18n::translate_c('East', 'E'); ?></option>
				<option value="PL_W" <?php if ($place_long < 0) echo " selected=\"selected\""; echo ">", i18n::translate_c('West', 'W'); ?></option>
			</select>
			<input type="text" name="NEW_PLACE_LONG" value="<?php if ($place_long != null) echo abs($place_long);?>" size="20" tabindex="<?php echo ++$i;?>" onchange="updateMap();" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Zoom factor'), help_link('PLE_ZOOM','googlemap'); ?></td>
		<td class="optionbox">
			<input type="text" name="NEW_ZOOM_FACTOR" value="<?php echo $zoomfactor;?>" size="20" tabindex="<?php echo ++$i;?>" onchange="updateMap();" /></td>
	</tr>
	<tr>
		<td class="descriptionbox"><?php echo i18n::translate('Flag'), help_link('PLE_ICON','googlemap'); ?></td>
		<td class="optionbox">
			<div id="flagsDiv">
<?php
		if (($place_icon == NULL) || ($place_icon == "")) { ?>
				<a href="javascript:;" onclick="change_icon();return false;"><?php echo i18n::translate('Change flag')?></a>
<?php   }
		else { ?>
				<img alt="<?php echo i18n::translate('Flag')?>" src="<?php echo $place_icon;?>"/>&nbsp;&nbsp;
				<a href="javascript:;" onclick="change_icon();return false;"><?php echo i18n::translate('Change flag')?></a>&nbsp;&nbsp;
				<a href="javascript:;" onclick="remove_icon();return false;"><?php echo i18n::translate('Remove flag')?></a>
<?php   } ?>
			</div></td>
	</tr>
	</table>
	<input name="save2" type="submit" value="<?php echo i18n::translate('Save');?>" /><br />
</form>
<?php
$link = 'module.php?mod=googlemap&pgvaction=places&parent='.$placeid;
echo "<center><br /><br /><br /><a href=\"javascript:;\" onclick=\"edit_close('{$link}')\">", i18n::translate('Close Window'), "</a><br /></center>\n";

print_simple_footer();
?>
