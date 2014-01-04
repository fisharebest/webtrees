<?php
// Google map module for webtrees
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010 PGV Development Team.  All rights reserved.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

function rem_prefix_from_placename($prefix_list, $place, $placelist) {
	if ($prefix_list) {
		foreach (explode(';', $prefix_list) as $prefix) {
			if ($prefix && substr($place, 0, strlen($prefix)+1)==$prefix.' ') {
				$placelist[] = substr($place, strlen($prefix)+1);
			}
		}
	}
	return $placelist;
}

function rem_postfix_from_placename($postfix_list, $place, $placelist) {
	if ($postfix_list) {
		foreach (explode (';', $postfix_list) as $postfix) {
			if ($postfix && substr($place, -strlen($postfix)-1)==' '.$postfix) {
				$placelist[] = substr($place, 0, strlen($place)-strlen($postfix)-1);
			}
		}
	}
	return $placelist;
}

function rem_prefix_postfix_from_placename($prefix_list, $postfix_list, $place, $placelist) {
	if ($prefix_list && $postfix_list) {
		foreach (explode (";", $prefix_list) as $prefix) {
			foreach (explode (";", $postfix_list) as $postfix) {
				if ($prefix && $postfix && substr($place, 0, strlen($prefix)+1)==$prefix.' ' && substr($place, -strlen($postfix)-1)==' '.$postfix) {
					$placelist[] = substr($place, strlen($prefix)+1, strlen($place)-strlen($prefix)-strlen($postfix)-2);
				}
			}
		}
	}
	return $placelist;
}

function create_possible_place_names ($placename, $level) {
	global $GM_PREFIX, $GM_POSTFIX;

	$retlist = array();
	if ($level<=9) {
		$retlist = rem_prefix_postfix_from_placename($GM_PREFIX[$level], $GM_POSTFIX[$level], $placename, $retlist); // Remove both
		$retlist = rem_prefix_from_placename($GM_PREFIX[$level], $placename, $retlist); // Remove prefix
		$retlist = rem_postfix_from_placename($GM_POSTFIX[$level], $placename, $retlist); // Remove suffix
	}
	$retlist[]=$placename; // Exact

	return $retlist;
}

function abbreviate($text) {
	if (utf8_strlen($text)>13) {
		if (trim(utf8_substr($text, 10, 1))!='') {
			$desc = utf8_substr($text, 0, 11).'.';
		} else {
			$desc = trim(utf8_substr($text, 0, 11));
		}
	}
	else $desc = $text;
	return $desc;
}

function get_lati_long_placelocation ($place) {
	$parent = explode (',', $place);
	$parent = array_reverse($parent);
	$place_id = 0;
	for ($i=0; $i<count($parent); $i++) {
		$parent[$i] = trim($parent[$i]);
		if (empty($parent[$i])) $parent[$i]='unknown';// GoogleMap module uses "unknown" while GEDCOM uses , ,
		$placelist = create_possible_place_names($parent[$i], $i+1);
		foreach ($placelist as $placename) {
			$pl_id=
				WT_DB::prepare("SELECT pl_id FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
				->execute(array($i, $place_id, $placename))
				->fetchOne();
			if (!empty($pl_id)) break;
		}
		if (empty($pl_id)) break;
		$place_id = $pl_id;
	}

	return
		WT_DB::prepare("SELECT sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, pl_lati, pl_long, pl_zoom, pl_icon, pl_level FROM `##placelocation` WHERE pl_id=? ORDER BY pl_place")
		->execute(array($place_id))
		->fetchOneRow();

}

function setup_map() {
	global $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM;

	?>
	<script src="<?php echo WT_GM_SCRIPT; ?>"></script>
	<script>
		var minZoomLevel = <?php echo $GOOGLEMAP_MIN_ZOOM;?>;
		var maxZoomLevel = <?php echo $GOOGLEMAP_MAX_ZOOM;?>;
		var startZoomLevel = <?php echo $GOOGLEMAP_MAX_ZOOM;?>;
	</script>
	<?php
}

function build_indiv_map(WT_Individual $indi, $indifacts, $famids) {
	global $controller, $GOOGLEMAP_MAX_ZOOM, $GOOGLEMAP_YSIZE;

	// Create the markers list array
	$gmarks = array();
	sort_facts($indifacts);
	$i = 0;
	foreach ($indifacts as $fact) {
		if (!$fact->getPlace()->isEmpty()) {
			$ctla = preg_match("/\d LATI (.*)/", $fact->getGedcom(), $match1);
			$ctlo = preg_match("/\d LONG (.*)/", $fact->getGedcom(), $match2);

			if ($fact->getParent() instanceof WT_Family) {
				$spouse = $fact->getParent()->getSpouse($indi);
			} else {
				$spouse = null;
			}
			if ($ctla && $ctlo) {
				$i++;
				$gmarks[$i]=array(
					'class'        => 'optionbox',
					'date'         => $fact->getDate()->Display(true),
					'fact_label'   => $fact->getLabel(),
					'image'        => $spouse ? $spouse->displayImage() : $fact->Icon(),
					'info'         => $fact->getValue(),
					'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.') , $match1[1]),
					'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.') , $match2[1]),
					'name'         => $spouse ? '<a href="' . $spouse->getHtmlUrl() . '"' . $spouse->getFullName() . '</a>' : '',
					'pl_icon'      => '',
					'place'        => $fact->getPlace()->getFullName(),
					'sv_bearing'   => '0',
					'sv_elevation' => '0',
					'sv_lati'      => '0',
					'sv_long'      => '0',
					'sv_zoom'      => '0',
					'tooltip'      => $fact->getPlace()->getGedcomName(),
				);
			} else {
				$latlongval = get_lati_long_placelocation($fact->getPlace()->getGedcomName());
				if ($latlongval && $latlongval->pl_lati && $latlongval->pl_long) {
					$i++;
					$gmarks[$i] = array(
						'class'        => 'optionbox',
						'date'         => $fact->getDate()->Display(true),
						'fact_label'   => $fact->getLabel(),
						'image'        => $spouse ? $spouse->displayImage() : $fact->Icon(),
						'info'         => $fact->getValue(),
						'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval->pl_lati),
						'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval->pl_long),
						'name'         => $spouse ? '<a href="' . $spouse->getHtmlUrl() . '"' . $spouse->getFullName() . '</a>' : '',
						'pl_icon'      => $latlongval->pl_icon,
						'place'        => $fact->getPlace()->getFullName(),
						'sv_bearing'   => $latlongval->sv_bearing,
						'sv_elevation' => $latlongval->sv_elevation,
						'sv_lati'      => $latlongval->sv_lati,
						'sv_long'      => $latlongval->sv_long,
						'sv_zoom'      => $latlongval->sv_zoom,
						'tooltip'      => $fact->getPlace()->getGedcomName(),
					);
					if ($GOOGLEMAP_MAX_ZOOM > $latlongval->pl_zoom) {
						$GOOGLEMAP_MAX_ZOOM = $latlongval->pl_zoom;
					}
				}
			}
		}
	}

	// Add children to the markers list array
	foreach ($famids as $xref) {
		$family = WT_Family::getInstance($xref);
		foreach ($family->getChildren() as $child) {
			$birth = $child->getFirstFact('BIRT');
			if ($birth) {
				$birthrec = $birth->getGedcom();
				if (!$birth->getPlace()->isEmpty()) {
					$ctla = preg_match('/\n4 LATI (.+)/', $birthrec, $match1);
					$ctlo = preg_match('/\n4 LONG (.+)/', $birthrec, $match2);
					if ($ctla && $ctlo) {
						$i++;
						$gmarks[$i]=array(
							'date'         => $birth->getDate()->Display(true),
							'image'        => $child->displayImage(),
							'info'         => '',
							'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.'), $match1[1]),
							'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.'), $match2[1]),
							'name'         => '<a href="' . $child->getHtmlUrl() . '"' . $child->getFullName() . '</a>',
							'pl_icon'      => '',
							'place'        => $birth->getPlace()->getFullName(),
							'sv_bearing'   => '0',
							'sv_elevation' => '0',
							'sv_lati'      => '0',
							'sv_long'      => '0',
							'sv_zoom'      => '0',
							'tooltip'      => $birth->getPlace()->getGedcomName(),
						);
						switch ($child->getSex()) {
						case'F':
							$gmarks[$i]['fact_label'] = WT_I18N::translate('daughter');
							$gmarks[$i]['class']      = 'person_boxF';
							break;
						case 'M':
							$gmarks[$i]['fact_label'] = WT_I18N::translate('son');
							$gmarks[$i]['class']      = 'person_box';
							break;
						default:
							$gmarks[$i]['fact_label'] = WT_I18N::translate('child');
							$gmarks[$i]['class']      = 'person_boxNN';
							break;
						}
					} else {
						$latlongval = get_lati_long_placelocation($birth->getPlace()->getGedcomName());
						if ($latlongval && $latlongval->pl_lati && $latlongval->pl_long) {
							$i++;
							$gmarks[$i] = array(
								'date'         => $birth->getDate()->Display(true),
								'image'        => $child->displayImage(),
								'info'         => '',
								'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval->pl_lati),
								'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval->pl_long),
								'name'         => '<a href="' . $child->getHtmlUrl() . '"' . $child->getFullName() . '</a>',
								'pl_icon'      => $latlongval->pl_icon,
								'place'        => $birth->getPlace()->getFullName(),
								'sv_bearing'   => $latlongval->sv_bearing,
								'sv_elevation' => $latlongval->sv_elevation,
								'sv_lati'      => $latlongval->sv_lati,
								'sv_long'      => $latlongval->sv_long,
								'sv_zoom'      => $latlongval->sv_zoom,
								'tooltip'      => $birth->getPlace()->getGedcomName(),
							);
							switch ($child->getSex()) {
							case 'M':
								$gmarks[$i]['fact_label'] = WT_I18N::translate('son');
								$gmarks[$i]['class']      = 'person_box';
								break;
							case 'F':
								$gmarks[$i]['fact_label'] = WT_I18N::translate('daughter');
								$gmarks[$i]['class']      = 'person_boxF';
								break;
							default:
								$gmarks[$i]['fact_label'] = WT_I18N::translate('child');
								$gmarks[$i]['class']      = 'option_boxNN';
								break;
							}
							if ($GOOGLEMAP_MAX_ZOOM > $latlongval->pl_zoom) {
								$GOOGLEMAP_MAX_ZOOM = $latlongval->pl_zoom;
							}
						}
					}
				}
			}
		}
	}

	// Group markers by location
	$location_groups = array();
	foreach ($gmarks as $gmark) {
		$key = $gmark['lat'] . $gmark['lng'];
		if (isset($location_groups[$key])) {
			$location_groups[$key][] = $gmark;
		} else {
			$location_groups[$key] = array($gmark);
		}
	}
	$location_groups = array_values($location_groups);

	// *** ENABLE STREETVIEW ***
	$STREETVIEW=get_module_setting('googlemap', 'GM_USE_STREETVIEW');
	?>

	<script>
		// this variable will collect the html which will eventually be placed in the side_bar
		var side_bar_html = '';
		var map_center = new google.maps.LatLng(0,0);
		var gmarkers = [];
		var gicons = [];
		var map = null;
		var head = '';
		var dir = '';
		var svzoom = '';

		var infowindow = new google.maps.InfoWindow({});

		gicons["red"] = new google.maps.MarkerImage("https://maps.google.com/mapfiles/marker.png",
			new google.maps.Size(20, 34),
			new google.maps.Point(0,0),
			new google.maps.Point(9, 34)
		);

		var iconImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/marker.png",
			new google.maps.Size(20, 34),
			new google.maps.Point(0,0),
			new google.maps.Point(9, 34)
		);

		var iconShadow = new google.maps.MarkerImage("https://www.google.com/mapfiles/shadow50.png",
			new google.maps.Size(37, 34),
			new google.maps.Point(0,0),
			new google.maps.Point(9, 34)
		);

		var iconShape = {
			coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
			type: "poly"
		};

		function getMarkerImage(iconColor) {
			if ((typeof(iconColor)=='undefined') || (iconColor==null)) {
				iconColor = 'red';
			}
			if (!gicons[iconColor]) {
				gicons[iconColor] = new google.maps.MarkerImage('//maps.google.com/mapfiles/marker'+ iconColor +'.png',
				new google.maps.Size(20, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34));
			}
			return gicons[iconColor];
		}

		var sv2_bear = null;
		var sv2_elev = null;
		var sv2_zoom = null;
		var placer   = null;

		// A function to create the marker and set up the event window
		function createMarker(latlng, html, tooltip, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, sv_point, marker_icon) {
			var contentString = '<div id="iwcontent">'+html+'</div>';

			// Use flag icon (if defined) instead of regular marker icon
			if (marker_icon) {
				var icon_image = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/'+marker_icon,
					new google.maps.Size(25, 15),
					new google.maps.Point(0,0),
					new google.maps.Point(0, 44));
				var icon_shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/flag_shadow.png',
					new google.maps.Size(35, 45), // Shadow size
					new google.maps.Point(0,0),   // Shadow origin
					new google.maps.Point(1, 45)  // Shadow anchor is base of flagpole
				);
			} else {
				var icon_image = getMarkerImage('red');
				var icon_shadow = iconShadow;
			}

			// Decide if marker point is Regular (latlng) or StreetView (sv_point) derived
			if (sv_point == '(0, 0)' || sv_point == '(null, null)') {
				placer = latlng;
			} else {
				placer = sv_point;
			}

			// Define the marker
			var marker = new google.maps.Marker({
				position: placer,
				icon:     icon_image,
				shadow:   icon_shadow,
				map:      map,
				title:    tooltip,
				zIndex:   Math.round(latlng.lat()*-100000)<<5
			});

			// Store the tab and event info as marker properties
			marker.sv_lati  = sv_lati;
			marker.sv_long  = sv_long;
			marker.sv_point = sv_point;

			if (sv_bearing == '') {
				marker.sv_bearing = 0;
			} else {
				marker.sv_bearing = sv_bearing;
			}
			if (sv_elevation == '') {
				marker.sv_elevation = 5;
			} else {
				marker.sv_elevation = sv_elevation;
			}
			if (sv_zoom == '' || sv_zoom == 0 || sv_zoom == 1) {
				marker.sv_zoom = 1.2;
			} else {
				marker.sv_zoom = sv_zoom;
			}

			marker.sv_latlng = new google.maps.LatLng(sv_lati, sv_long);
			gmarkers.push(marker);

			// Open infowindow when marker is clicked
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.close();
				infowindow.setContent(contentString);
				infowindow.open(map, marker);
				var panoramaOptions = {
					position:          marker.position,
					mode:              'html5',
					navigationControl: false,
					linksControl:      false,
					addressControl:    false,
					pov: {
						heading: sv_bearing,
						pitch:   sv_elevation,
						zoom:    sv_zoom
					}
				};

				// Use jquery for info window tabs
				google.maps.event.addListener(infowindow, 'domready', function() {
	  	    //jQuery code here
					jQuery('#EV').click(function() {
						document.tabLayerEV = eval('document.getElementById("EV")');
						document.tabLayerEV.style.background = '#ffffff';
						document.tabLayerEV.style.paddingBottom = '1px';
						<?php if ($STREETVIEW) { ?>
						document.tabLayerSV = eval('document.getElementById("SV")');
						document.tabLayerSV.style.background = '#cccccc';
						document.tabLayerSV.style.paddingBottom = '0px';
						<?php } ?>
						document.panelLayer1 = eval('document.getElementById("pane1")');
						document.panelLayer1.style.display = 'block';
						<?php if ($STREETVIEW) { ?>
						document.panelLayer2 = eval('document.getElementById("pane2")');
						document.panelLayer2.style.display = 'none';
						<?php } ?>
					});

					jQuery('#SV').click(function() {
						document.tabLayerEV = eval('document.getElementById("EV")');
						document.tabLayerEV.style.background = '#cccccc';
						document.tabLayerEV.style.paddingBottom = '0px';
						<?php if ($STREETVIEW) { ?>
						document.tabLayerSV = eval('document.getElementById("SV")');
						document.tabLayerSV.style.background = '#ffffff';
						document.tabLayerSV.style.paddingBottom = '1px';
						<?php } ?>
						document.panelLayer1 = eval('document.getElementById("pane1")');
						document.panelLayer1.style.display = 'none';
						<?php if ($STREETVIEW) { ?>
						document.panelLayer2 = eval('document.getElementById("pane2")');
						document.panelLayer2.style.display = 'block';
						<?php } ?>
						var panorama = new google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
						setTimeout(function() { panorama.setVisible(true); }, 100);
						setTimeout(function() { panorama.setVisible(true); }, 500);
					});
				});
			});
		}

		// Opens Marker infowindow when corresponding Sidebar item is clicked
		function myclick(i) {
			infowindow.close();
			google.maps.event.trigger(gmarkers[i], 'click');
		}

		// Home control
		// returns the user to the original map position ... loadMap() function
		// This constructor takes the control DIV as an argument.
		function HomeControl(controlDiv, map) {
			// Set CSS styles for the DIV containing the control
			// Setting padding to 5 px will offset the control from the edge of the map
			controlDiv.style.paddingTop = '5px';
			controlDiv.style.paddingRight = '0px';

			// Set CSS for the control border
			var controlUI = document.createElement('DIV');
			controlUI.style.backgroundColor = 'white';
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
			controlText.innerHTML = '<b><?php echo WT_I18N::translate('Redraw map')?></b>';
			controlUI.appendChild(controlText);

			// Setup the click event listeners: simply set the map to original LatLng
			google.maps.event.addDomListener(controlUI, 'click', function() {
				loadMap();
			});
		}

		function loadMap() {
			<?php
				global $GOOGLEMAP_MAP_TYPE, $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $SHOW_HIGHLIGHT_IMAGES;
			?>

			// Create the map and mapOptions
			var mapOptions = {
				zoom: 7,
				center: map_center,
				mapTypeId: google.maps.MapTypeId.<?php echo $GOOGLEMAP_MAP_TYPE; ?>,  // ROADMAP, SATELLITE, HYBRID, TERRAIN
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
				},
				navigationControl: true,
				navigationControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
				style: google.maps.NavigationControlStyle.SMALL  // ANDROID, DEFAULT, SMALL, ZOOM_PAN
				},
				streetViewControl: false,  // Show Pegman or not
				scrollwheel: false
			};
			map = new google.maps.Map(document.getElementById('map_pane'), mapOptions);

			// Close any infowindow when map is clicked
			google.maps.event.addListener(map, 'click', function() {
				infowindow.close();
			});

			// Create the Home DIV and call the HomeControl() constructor in this DIV.
			var homeControlDiv = document.createElement('DIV');
			var homeControl = new HomeControl(homeControlDiv, map);
			homeControlDiv.index = 1;
			map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);

			// Add the markers to the map from the $gmarks array
			var locations = [
				<?php foreach($gmarks as $n=>$gmark) { ?>
				<?php echo $n ? ',' : ''; ?>
				{
					"event":        "<?php echo WT_Filter::escapeJs($gmark['fact_label']  ); ?>",
					"lat":          "<?php echo WT_Filter::escapeJs($gmark['lat']         ); ?>",
					"lng":          "<?php echo WT_Filter::escapeJs($gmark['lng']         ); ?>",
					"date":         "<?php echo WT_Filter::escapeJs($gmark['date']        ); ?>",
					"info":         "<?php echo WT_Filter::escapeJs($gmark['info']        ); ?>",
					"name":         "<?php echo WT_Filter::escapeJs($gmark['name']        ); ?>",
					"place":        "<?php echo WT_Filter::escapeJs($gmark['place']       ); ?>",
					"tooltip":      "<?php echo WT_Filter::escapeJs($gmark['tooltip']     ); ?>",
					"image":        "<?php echo WT_Filter::escapeJs($gmark['image']       ); ?>",
					"pl_icon":      "<?php echo WT_Filter::escapeJs($gmark['pl_icon']     ); ?>",
					"sv_lati":      "<?php echo WT_Filter::escapeJs($gmark['sv_lati']     ); ?>",
					"sv_long":      "<?php echo WT_Filter::escapeJs($gmark['sv_long']     ); ?>",
					"sv_bearing":   "<?php echo WT_Filter::escapeJs($gmark['sv_bearing']  ); ?>",
					"sv_elevation": "<?php echo WT_Filter::escapeJs($gmark['sv_elevation']); ?>",
					"sv_zoom":      "<?php echo WT_Filter::escapeJs($gmark['sv_zoom']     ); ?>"
				}
				<?php } ?>
			];

			// Group the markers by location
			var location_groups = new Array();
			for (var key in locations) {
				if (!location_groups.hasOwnProperty(locations[key].place)) {
					location_groups[locations[key].place] = new Array();
				}
				location_groups[locations[key].place].push(locations[key]);
			}
			// TODO: why doesn't this next line work?
			//var location_groups = <?php echo json_encode($location_groups); ?>;

			// Set the Marker bounds
			var bounds = new google.maps.LatLngBounds ();

			var key;
			// Iterate over each location
			for (key in location_groups) {
				var locations = location_groups[key];
				// Iterate over each marker at this location
				var event_details = '';
				for (var j in locations) {
					var location = locations[j];
					if (location.info && location.name) {
						event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span> ' + location.info + '<br><b>' + location.name + '</b><br>' + location.date + '<br></p></td></tr></table>';
					} else if (location.name) {
						event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span><br><b>' + location.name + '</b><br>' + location.date + '<br></p></td></tr></table>';
					} else if (location.info) {
						event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span> ' + location.info + '<br>' + location.date + '<br></p></td></tr></table>';
					} else {
						event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span><br>' + location.date + '<br></p></td></tr></table>';
					}
				}
				// All locations are the same in each group, so create a marker with the first
				var location = location_groups[key][0];
				var html =
				'<div class="infowindow">' +
					'<div id="gmtabs">' +
						'<ul class="tabs" >' +
							'<li><a href="#event" id="EV"><?php echo WT_I18N::translate('Events'); ?></a></li>' +
							<?php if ($STREETVIEW) { ?>
							'<li><a href="#sview" id="SV"><?php echo WT_I18N::translate('Google Street View™'); ?></a></li>' +
							<?php } ?>
						'</ul>' +
						'<div class="panes">' +
							'<div id="pane1">' +
								'<h4 id="iwhead">' + location.place + '</h4>' +
								event_details +
							'</div>' +
							<?php if ($STREETVIEW) { ?>
							'<div id="pane2">' +
								'<h4 id="iwhead">' + location.place + '</h4>' +
								'<div id="pano"></div>' +
							'</div>' +
							<?php } ?>
						'</div>' +
					'</div>' +
				'</div>';

				// create the marker
				var point        = new google.maps.LatLng(location.lat,     location.lng);     // Place Latitude, Longitude
				var sv_point     = new google.maps.LatLng(location.sv_lati, location.sv_long); // StreetView Latitude and Longitide

				var zoomLevel = <?php echo $GOOGLEMAP_MAX_ZOOM; ?>;
				var marker    = createMarker(point, html, location.tooltip, location.sv_lati, location.sv_long, location.sv_bearing, location.sv_elevation, location.sv_zoom, sv_point, location.pl_icon);

				// if streetview coordinates are available, use them for marker,
				// else use the place coordinates
				if (sv_point && sv_point != "(0, 0)") {
					var myLatLng = sv_point;
				} else {
					var myLatLng = point;
				}

				// Correct zoom level when only one marker is present
				if (location_groups.length == 1) {
					bounds.extend(myLatLng);
					map.setZoom(zoomLevel);
					map.setCenter(myLatLng);
				} else {
					bounds.extend(myLatLng);
					map.fitBounds(bounds);
					// Correct zoom level when multiple markers have the same coordinates
					var listener1 = google.maps.event.addListenerOnce(map, "idle", function() {
						if (map.getZoom() > zoomLevel) {
							map.setZoom(zoomLevel);
						}
						google.maps.event.removeListener(listener1);
					});
				}
			} // end loop through location markers
		} // end loadMap()

	</script>
	<?php
	// Create the normal googlemap sidebar of events and children
	echo '<div style="overflow: auto; overflow-x: hidden; overflow-y: auto; height:', $GOOGLEMAP_YSIZE, 'px;"><table class="facts_table">';

	foreach ($location_groups as $key=>$location_group) {
		foreach ($location_group as $gmark) {
			echo '<tr>';
			echo '<td class="facts_label">';
			echo '<a href="#" onclick="myclick(\'', WT_Filter::escapeHtml($key), '\')">', $gmark['fact_label'], '</a></td>';
			echo '<td class="', $gmark['class'], '" style="white-space: normal">';
			if ($gmark['info']) {
				echo '<span class="field">', WT_Filter::escapeHtml($gmark['info']), '</span><br>';
			}
			if ($gmark['name']) {
				echo $gmark['name'], '<br>';
			}
			echo $gmark['place'], '<br>';
			if ($gmark['date']) {
				echo $gmark['date'], '<br>';
			}
			echo '</td>';
			echo '</tr>';
		}
	}
	echo '</table></div><br>';
}
