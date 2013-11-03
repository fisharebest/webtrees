<?php
// Included script file for Interface to edit place locations
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team. All rights reserved.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
?>

<head>
	<script src="<?php echo WT_GM_SCRIPT; ?>"></script>
	<script src="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>googlemap/wt_v3_places_edit_overlays.js.php"></script>
	<link type="text/css" href="<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>googlemap/css/wt_v3_googlemap.css" rel="stylesheet">

	<script>
	var map;
	var marker;
	var zoom;
	var pl_name = '<?php echo WT_Filter::escapeJs($place_name); ?>';
	if (pl_name) {
		var pl_lati = '<?php echo $place_lati; ?>';
		var pl_long = '<?php echo $place_long; ?>';
	} else {
		var pl_lati = '<?php echo $parent_lati; ?>';
		var pl_long = '<?php echo $parent_long; ?>';
	}
	var pl_zoom = <?php echo $zoomfactor; ?>;
	var latlng = new google.maps.LatLng(pl_lati, pl_long);
	var polygon1;
	var geocoder;
	var mapType;

	var infowindow = new google.maps.InfoWindow({
		//
	});

	function geocodePosition(pos) {
		geocoder.geocode({
			latLng: pos
		}, function(responses) {
			if (responses && responses.length > 0) {
				updateMarkerAddress(responses[0].formatted_address);
			} else {
				updateMarkerAddress('Cannot determine address at this location.');
			}
		});
	}

	function updateMap(event) {
		var point;
		var zoom;
		var latitude;
		var longitude;
		var i;

		zoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);

		prec = 20;
		for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
			if (document.editplaces.NEW_PRECISION[i].checked) {
				prec = document.editplaces.NEW_PRECISION[i].value;
			}
		}
		if ((document.editplaces.NEW_PLACE_LATI.value == '') ||
			(document.editplaces.NEW_PLACE_LONG.value == '')) {
			latitude = parseFloat(document.editplaces.parent_lati.value).toFixed(prec);
			longitude = parseFloat(document.editplaces.parent_long.value).toFixed(prec);
			point = new google.maps.LatLng(latitude, longitude);
		} else {
			latitude = parseFloat(document.editplaces.NEW_PLACE_LATI.value).toFixed(prec);
			longitude = parseFloat(document.editplaces.NEW_PLACE_LONG.value).toFixed(prec);
			document.editplaces.NEW_PLACE_LATI.value = latitude;
			document.editplaces.NEW_PLACE_LONG.value = longitude;

			if (event == 'flag_drag') {
				if (longitude < 0.0 ) {
					longitude = longitude * -1;
					document.editplaces.NEW_PLACE_LONG.value = longitude;
					document.editplaces.LONG_CONTROL.value = 'PL_W';
				} else {
					longitude = longitude ;
					document.editplaces.NEW_PLACE_LONG.value = longitude;
					document.editplaces.LONG_CONTROL.value = 'PL_E';
				}
				if (latitude < 0.0 ) {
					latitude = latitude * -1;
					document.editplaces.NEW_PLACE_LATI.value = latitude;
					document.editplaces.LATI_CONTROL.value = 'PL_S';
				} else {
					latitude = latitude ;
					document.editplaces.NEW_PLACE_LATI.value = latitude;
					document.editplaces.LATI_CONTROL.value = 'PL_N';
				}

				if (document.editplaces.LATI_CONTROL.value == 'PL_S') {
					latitude = latitude * -1;
				}
				if (document.editplaces.LONG_CONTROL.value == 'PL_W') {
					longitude = longitude * -1;
				}
				point = new google.maps.LatLng(latitude, longitude);
			} else {
				if (latitude < 0.0) {
					latitude = latitude * -1;
					document.editplaces.NEW_PLACE_LATI.value = latitude;
				}
				if (longitude < 0.0) {
					longitude = longitude * -1;
					document.editplaces.NEW_PLACE_LONG.value = longitude;
				}
				if (document.editplaces.LATI_CONTROL.value == 'PL_S') {
					latitude = latitude * -1;
				}
				if (document.editplaces.LONG_CONTROL.value == 'PL_W') {
					longitude = longitude * -1;
				}
				point = new google.maps.LatLng(latitude, longitude);
			}
		}

		map.setCenter(point);
		map.setZoom(zoom);
		marker.setPosition(point);

	}

	// The HomeControl returns user to original position and style =================
	function HomeControl(controlDiv, map) {
		// Set CSS styles for the DIV containing the control
		// Setting padding to 5 px will offset the control from the edge of the map
		controlDiv.style.paddingTop = '5px';
		controlDiv.style.paddingRight = '0px';

		// Set CSS for the control border
		var controlUI = document.createElement('DIV');
		controlUI.style.backgroundColor = 'white';
		controlUI.style.color = 'black';
		controlUI.style.borderColor = 'black';
		controlUI.style.borderColor = 'black';
		controlUI.style.borderStyle = 'solid';
		controlUI.style.borderWidth = '2px';
		controlUI.style.cursor = 'pointer';
		controlUI.style.textAlign = 'center';
		controlUI.title = '';
		controlDiv.appendChild(controlUI);

		// Set CSS for the control interior
		var controlText = document.createElement('DIV');
		controlText.style.fontFamily = 'Arial,sans-serif';
		controlText.style.fontSize = '12px';
		controlText.style.paddingLeft = '15px';
		controlText.style.paddingRight = '15px';
		controlText.innerHTML = '<b><?php echo WT_I18N::translate('Redraw map')?><\/b>';
		controlUI.appendChild(controlText);

		// Setup the click event listeners: simply set the map to original LatLng
		google.maps.event.addDomListener(controlUI, 'click', function() {
			map.setCenter(latlng),
			map.setZoom(pl_zoom),
			map.setMapTypeId(google.maps.MapTypeId.ROADMAP)
		});
	}

	function loadMap(zoom, mapType) {
		if (mapType) {
			mapTyp = mapType;
		} else {
			mapTyp = google.maps.MapTypeId.ROADMAP;
		}
		geocoder = new google.maps.Geocoder();
		if (zoom) {
			zoom = zoom;
		}else {
			zoom = pl_zoom;
		}
		// Define map
		var myOptions = {
			zoom: zoom,
			center: latlng,
			mapTypeId: mapTyp,											// ROADMAP, SATELLITE, HYBRID, TERRAIN
			// mapTypeId: google.maps.MapTypeId.ROADMAP,				// ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControlOptions: {
			position: google.maps.ControlPosition.TOP_RIGHT,			// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
			style: google.maps.NavigationControlStyle.SMALL				// ANDROID, DEFAULT, SMALL, ZOOM_PAN
			},
			streetViewControl: false,									// Show Pegman or not
			scrollwheel: true
		};

		map = new google.maps.Map(document.getElementById('map_pane'), myOptions);

		// *** === NOTE *** This function creates the UK country overlays ==========================
		overlays();
		// === Above function is located in WT_MODULES_DIR/googlemap/wt_v3_placeOverlays.js.php ====


		// Close any infowindow when map is clicked
		google.maps.event.addListener(map, 'click', function() {
			infowindow.close();
		});

		// Create the DIV to hold the control and call HomeControl() passing in this DIV. --
		var homeControlDiv = document.createElement('DIV');
		var homeControl = new HomeControl(homeControlDiv, map);
		homeControlDiv.index = 1;
		map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
		// ---------------------------------------------------------------------------------

		// Check for zoom changes
		google.maps.event.addListener(map, 'zoom_changed', function() {
			document.editplaces.NEW_ZOOM_FACTOR.value = map.zoom;
		});

		// Create the Main Location Marker
		<?php
		if ($level < 3 && $place_icon != '') {
			echo 'var image = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/',$place_icon,'",';
				echo 'new google.maps.Size(25, 15),';	// Image size
				echo 'new google.maps.Point(0, 0),';	// Image origin
				echo 'new google.maps.Point(0, 44)';	// Image anchor
			echo ');';
			echo 'var iconShadow = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/flag_shadow.png",';
				echo 'new google.maps.Size(35, 45),';	// Shadow size
				echo 'new google.maps.Point(0,0),';		// Shadow origin
				echo 'new google.maps.Point(1, 45)';	// Shadow anchor is base of flagpole
			echo ');';
			echo 'marker = new google.maps.Marker({';
				echo 'icon: image,';
				echo 'shadow: iconShadow,';
				echo 'position: latlng,';
				echo 'map: map,';
				echo 'title: pl_name,';
				echo 'draggable: true,';
				echo 'zIndex:1';
			echo '});';
		} else {
			echo 'marker = new google.maps.Marker({';
				echo 'position: latlng,';
				echo 'map: map,';
				echo 'title: pl_name,';
				echo 'draggable: true,';
				echo 'zIndex: 1';
			echo '});';
		}
		?>

		prec = 20;
		for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
			if (document.editplaces.NEW_PRECISION[i].checked) {
				prec = document.editplaces.NEW_PRECISION[i].value;
			}
		}

		// Set marker by clicking on map ---
		clickset = google.maps.event.addListener(map, 'click', function(event) {
			// alert(pos2);
			clearMarks();
			latlng = event.latLng;
			<?php
				echo 'marker = new google.maps.Marker({';
				echo 'position: latlng,';
				echo 'map: map,';
				echo 'title: pl_name,';
				echo 'draggable: true,';
				echo 'zIndex: 1';
			echo '});';
			?>
			pos3 = marker.getPosition();
			document.getElementById('NEW_PLACE_LATI').value = parseFloat(pos3.lat()).toFixed(prec);
			document.getElementById('NEW_PLACE_LONG').value = parseFloat(pos3.lng()).toFixed(prec);
			updateMap('flag_drag');
			currzoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);
			mapType = map.getMapTypeId();
			loadMap(currzoom, mapType);
		});

		// Set marker by drag-n-drop on map ---
		dragset = google.maps.event.addListener(marker, 'drag', function() {
			pos1 = marker.getPosition();
			document.getElementById('NEW_PLACE_LATI').value = parseFloat(pos1.lat()).toFixed(prec);
			document.getElementById('NEW_PLACE_LONG').value = parseFloat(pos1.lng()).toFixed(prec);
		});
		dropset = google.maps.event.addListener(marker, 'dragend', function() {
			pos2 = marker.getPosition();
			document.getElementById('NEW_PLACE_LATI').value = parseFloat(pos2.lat()).toFixed(prec);
			document.getElementById('NEW_PLACE_LONG').value = parseFloat(pos2.lng()).toFixed(prec);
			updateMap('flag_drag');
		});
	}

	function clearMarks() {
		marker.setMap(null);
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
			document.editplaces.LATI_CONTROL.value = 'PL_S';
		} else {
			document.editplaces.NEW_PLACE_LATI.value = lat.toFixed(prec);
			document.editplaces.LATI_CONTROL.value = 'PL_N';
		}
		if (lng < 0.0) {
			document.editplaces.NEW_PLACE_LONG.value = (lng.toFixed(prec) * -1);
			document.editplaces.LONG_CONTROL.value = 'PL_W';
		} else {
			document.editplaces.NEW_PLACE_LONG.value = lng.toFixed(prec);
			document.editplaces.LONG_CONTROL.value = 'PL_E';
		}
		newval = new google.maps.LatLng (lat.toFixed(prec), lng.toFixed(prec));
		updateMap();
	}

	function createMarker(i, point, name) {
		var contentString = '<div id="iwcontent_edit">'+name+'<\/div>';
		<?php
		echo 'var image = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/marker_yellow.png",';
			echo 'new google.maps.Size(20, 34),';	// Image size
			echo 'new google.maps.Point(0, 0),';	// Image origin
			echo 'new google.maps.Point(10, 34)';	// Image anchor
		echo ');';
		echo 'var iconShadow = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/shadow50.png",';
			echo 'new google.maps.Size(37, 34),';	// Shadow size
			echo 'new google.maps.Point(0, 0),';	// Shadow origin
			echo 'new google.maps.Point(10, 34)';	// Shadow anchor is base of image
		echo ');';
		?>
		var marker = new google.maps.Marker({
			icon: image,
			shadow: iconShadow,
			map: map,
			position: point,
			zIndex: 0
		});

		google.maps.event.addListener(marker, 'click', function() {
			infowindow.close();
			infowindow.setContent(contentString);
			infowindow.open(map, marker);
		});

		google.maps.event.addListener(map, 'click', function() {
			infowindow.close();
		});

		return marker;
	}

	function change_icon() {
		window.open('module.php?mod=googlemap&mod_action=flags&countrySelected=<?php echo $selected_country; ?>', '_blank', indx_window_specs);
		return false;
	}

	function remove_icon() {
		document.editplaces.icon.value = '';
		document.getElementById('flagsDiv').innerHTML = '<a href="#" onclick="change_icon();return false;"><?php echo WT_I18N::translate('Change flag'); ?></a>';
	}

	function addAddressToMap(response) {
		var bounds = new google.maps.LatLngBounds();
		if (!response ) {
			alert('<?php echo WT_I18N::translate('No places found'); ?>');
		} else {
			if (response.length > 0) {
				for (i=0; i<response.length; i++) {
					var name  = '<div id="gname" class="iwstyle">'+response[i].address_components[0].short_name+'<br> '+response[i].geometry.location+''
						name +=	'<br><a href="#" onclick="setLoc(' + response[i].geometry.location.lat() + ', ' + response[i].geometry.location.lng() + ');"><div id="namelink"><?php echo WT_I18N::translate('Use this value'); ?></div></a>'
						name += '</div>'
					var point = response[i].geometry.location;
					var marker = createMarker(i, point, name);
					bounds.extend(response[i].geometry.location);
				}

				<?php if ($level > 0) { ?>
					map.fitBounds(bounds);
				<?php } ?>
				zoomlevel = map.getZoom();

				if (zoomlevel < <?php echo $GOOGLEMAP_MIN_ZOOM; ?>) {
					zoomlevel = <?php echo $GOOGLEMAP_MIN_ZOOM; ?>;
				}
				if (zoomlevel > <?php echo $GOOGLEMAP_MAX_ZOOM; ?>) {
					zoomlevel = <?php echo $GOOGLEMAP_MAX_ZOOM; ?>;
				}
				if (document.editplaces.NEW_ZOOM_FACTOR.value < zoomlevel) {
					zoomlevel = document.editplaces.NEW_ZOOM_FACTOR.value;
					if (zoomlevel < <?php echo $GOOGLEMAP_MIN_ZOOM; ?>) {
						zoomlevel = <?php echo $GOOGLEMAP_MIN_ZOOM; ?>;
					}
					if (zoomlevel > <?php echo $GOOGLEMAP_MAX_ZOOM; ?>) {
						zoomlevel = <?php echo $GOOGLEMAP_MAX_ZOOM; ?>;
					}
				}
				map.setCenter(bounds.getCenter());
				map.setZoom(zoomlevel);
			}
		}
	}

	function showLocation_level(address) {
		address += '<?php if ($level>0) echo ', ', addslashes(implode(', ', array_reverse($where_am_i, true))); ?>';
		geocoder.geocode({'address': address}, addAddressToMap);
	}

	function showLocation_all(address) {
		geocoder.geocode({'address': address}, addAddressToMap);
	}

	function paste_char(value) {
		document.editplaces.NEW_PLACE_NAME.value += value;
	}
</script>
</head>
<body onload="loadMap()" >
<table><tr><td align="center">
</td></tr></table>
</body>
