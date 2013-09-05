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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

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

	$row=
		WT_DB::prepare("SELECT pl_media, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, pl_lati, pl_long, pl_zoom, pl_icon, pl_level FROM `##placelocation` WHERE pl_id=? ORDER BY pl_place")
		->execute(array($place_id))
		->fetchOneRow();
	if ($row) {
		return array('media'=>$row->pl_media, 'sv_lati'=>$row->sv_lati, 'sv_long'=>$row->sv_long, 'sv_bearing'=>$row->sv_bearing, 'sv_elevation'=>$row->sv_elevation, 'sv_zoom'=>$row->sv_zoom, 'lati'=>$row->pl_lati, 'long'=>$row->pl_long, 'zoom'=>$row->pl_zoom, 'icon'=>$row->pl_icon, 'level'=>$row->pl_level);
	} else {
		return array();
	}
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
	global $controller, $GOOGLEMAP_MAX_ZOOM, $GOOGLEMAP_YSIZE, $GM_DEFAULT_TOP_VALUE;

	// Create the markers list array
	$markers = array();
	sort_facts($indifacts);
	$i = 0;
	foreach ($indifacts as $fact) {
		if ($fact->getPlace()) {
			$ctla = preg_match("/\d LATI (.*)/", $fact->getGedcom(), $match1);
			$ctlo = preg_match("/\d LONG (.*)/", $fact->getGedcom(), $match2);
			if ($fact->getParent() instanceof WT_Family) {
				$spouse = $fact->getParent()->getSpouse($indi);
			} else {
				$spouse = null;
			}
			if ($spouse) {
				$useThisItem = $spouse->canShow();
			} else {
				$useThisItem = true;
			}
			if (($ctla>0) && ($ctlo>0) && ($useThisItem==true)) {
				$i++;
				$markers[$i]=array(
					'class'      => 'optionbox',
					'index'      => '',
					'tabindex'   => '',
					'placed'     => 'no',
					'fact'       => $fact->getTag(),
					'fact_label' => $fact->getLabel(),
					'info'       => $fact->getValue(),
					'placerec'   => $fact->getPlace(),
					'lati'       => str_replace(array('N', 'S', ','), array('', '-', '.') , $match1[1]),
					'lng'        => str_replace(array('E', 'W', ','), array('', '-', '.') , $match2[1]),
				);
				$ctd = preg_match("/2 DATE (.+)/", $fact->getGedcom(), $match);
				if ($ctd>0) {
					$markers[$i]['date'] = $match[1];
				}
				if ($spouse) {
					$markers[$i]['name'] = $spouse->getXref();
				}
			} else {
				if ($useThisItem) {
					$latlongval = get_lati_long_placelocation($fact->getPlace());
					if ((count($latlongval) == 0) && (!empty($GM_DEFAULT_TOP_VALUE))) {
						$latlongval = get_lati_long_placelocation($match1[1].', '.$GM_DEFAULT_TOP_VALUE);
						if ((count($latlongval) != 0) && ($latlongval['level'] == 0)) {
							$latlongval['lati'] = null;
							$latlongval['long'] = null;
						}
					}
					if ((count($latlongval) != 0) && ($latlongval['lati'] != null) && ($latlongval['long'] != null)) {
						$i++;
						$markers[$i] = array(
							'class'      => 'optionbox',
							'index'      => '',
							'tabindex'   => '',
							'placed'     => 'no',
							'fact'       => $fact->getTag(),
							'fact_label' => $fact->getLabel(),
							'info'       => $fact->getValue(),
							'placerec'   => $fact->getPlace(),
						);
						$markers[$i]['icon'] = $latlongval['icon'];
						if ($GOOGLEMAP_MAX_ZOOM > $latlongval['zoom']) {
							$GOOGLEMAP_MAX_ZOOM = $latlongval['zoom'];
						}
						$markers[$i]['lati']         = str_replace(array('N', 'S', ','), array('', '-', '.') , $latlongval['lati']);
						$markers[$i]['lng']          = str_replace(array('E', 'W', ','), array('', '-', '.') , $latlongval['long']);
						$markers[$i]['media']        = $latlongval['media'];
						$markers[$i]['sv_lati']      = $latlongval['sv_lati'];
						$markers[$i]['sv_long']      = $latlongval['sv_long'];
						$markers[$i]['sv_bearing']   = $latlongval['sv_bearing'];
						$markers[$i]['sv_elevation'] = $latlongval['sv_elevation'];
						$markers[$i]['sv_zoom']      = $latlongval['sv_zoom'];
						$ctd = preg_match("/2 DATE (.+)/", $fact->getGedcom(), $match);
						if ($ctd>0) {
							$markers[$i]['date'] = $match[1];
						}
						if ($spouse) {
							$markers[$i]['name'] = $spouse->getXref();
						}
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
				if ($birth->getPlace()) {
					$ctd = preg_match('/\n2 DATE (.*)/',  $birthrec, $matchd);
					$ctla = preg_match('/\n4 LATI (.*)/', $birthrec, $match1);
					$ctlo = preg_match('/\n4 LONG (.*)/', $birthrec, $match2);
					if (($ctla>0) && ($ctlo>0)) {
						$i++;
						$markers[$i]=array(
							'index'    => '',
							'tabindex' => '',
							'placed'   => 'no',
							'fact'     => 'BIRT',
							'placerec' => $birth->getPlace(),
						);
						switch ($child->getSex()) {
						case'F':
							$markers[$i]['fact_label'] = WT_I18N::translate('daughter');
							$markers[$i]['class']      = 'person_boxF';
							break;
						case 'M':
							$markers[$i]['fact_label'] = WT_I18N::translate('son');
							$markers[$i]['class']      = 'person_box';
							break;
						default:
							$markers[$i]['fact_label'] = WT_I18N::translate('child');
							$markers[$i]['class']      = 'person_boxNN';
							break;
						}
						$match1[1] = trim($match1[1]);
						$match2[1] = trim($match2[1]);
						$markers[$i]['lati'] = str_replace(array('N', 'S', ','), array('', '-', '.'), $match1[1]);
						$markers[$i]['lng'] = str_replace(array('E', 'W', ','), array('', '-', '.'), $match2[1]);
						if ($ctd > 0) {
							$markers[$i]['date'] = $matchd[1];
						}
						$markers[$i]['name'] = $smatch[$j][1];
					} else {
						$latlongval = get_lati_long_placelocation($birth->getPlace());
						if ((count($latlongval) == 0) && (!empty($GM_DEFAULT_TOP_VALUE))) {
							$latlongval = get_lati_long_placelocation($birth->getPlace().', '.$GM_DEFAULT_TOP_VALUE);
							if ((count($latlongval) != 0) && ($latlongval['level'] == 0)) {
								$latlongval['lati'] = null;
								$latlongval['long'] = null;
							}
						}
						if ((count($latlongval) != 0) && ($latlongval['lati'] != null) && ($latlongval['long'] != null)) {
							$i++;
							$markers[$i] = array(
								'index'    => '',
								'tabindex' => '',
								'placed'   => 'no',
								'fact'     => 'BIRT',
								'placerec' => $birth->getPlace(),
							);
							switch ($child->getSex()) {
							case 'M':
								$markers[$i]['fact_label'] = WT_I18N::translate('son');
								$markers[$i]['class']      = 'person_box';
								break;
							case 'F':
								$markers[$i]['fact_label'] = WT_I18N::translate('daughter');
								$markers[$i]['class']      = 'person_boxF';
								break;
							default:
								$markers[$i]['fact_label'] = WT_I18N::translate('child');
								$markers[$i]['class']      = 'option_boxNN';
								break;
							}
							$markers[$i]['icon'] = $latlongval['icon'];
							if ($GOOGLEMAP_MAX_ZOOM > $latlongval['zoom']) {
								$GOOGLEMAP_MAX_ZOOM = $latlongval['zoom'];
							}
							$markers[$i]['lati'] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval['lati']);
							$markers[$i]['lng']  = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval['long']);
							if ($ctd > 0) {
								$markers[$i]['date'] = $matchd[1];
							}
							$markers[$i]['name'] = $child->getXref();
							$markers[$i]['media'] = $latlongval['media'];
							$markers[$i]['sv_lati'] = $latlongval['sv_lati'];
							$markers[$i]['sv_long'] = $latlongval['sv_long'];
							$markers[$i]['sv_bearing'] = $latlongval['sv_bearing'];
							$markers[$i]['sv_elevation'] = $latlongval['sv_elevation'];
							$markers[$i]['sv_zoom'] = $latlongval['sv_zoom'];
						}
					}
				}
			}
		}
	}

	// Prepare the $markers array for use by the following "required" file/files
	if ($i != 0) {
		$indexcounter = 0;
		for ($j=1; $j<=$i; $j++) {
			if ($markers[$j]['placed'] == 'no') {
				$multimarker = -1;
				// Count nr of locations where the long/lati is identical
				for ($k=$j; $k<=$i; $k++) {
					if (($markers[$j]['lati'] == $markers[$k]['lati']) && ($markers[$j]['lng'] == $markers[$k]['lng'])) {
						$multimarker = $multimarker + 1;
					}
				}
				// If only one location with this long/lati combination
				if ($multimarker == 0) {
					// --- NOTE for V3 api, following line is changed from "yes" to "no"
					// --- This aids in identifying multi-event locations
					$markers[$j]['placed'] = 'no';
					$markers[$j]['index'] = $indexcounter;
					$markers[$j]['tabindex'] = 0;
					$indexcounter = $indexcounter + 1;
				} else {
					$tabcounter = 0;
					$markersindex = 0;
					$markers[$j]['placed'] = 'yes';
					$markers[$j]['index'] = $indexcounter;
					$markers[$j]['tabindex'] = $tabcounter;
					$tabcounter = $tabcounter + 1;
					for ($k=$j+1; $k<=$i; $k++) {
						if (($markers[$j]['lati'] == $markers[$k]['lati']) && ($markers[$j]['lng'] == $markers[$k]['lng'])) {
							$markers[$k]['placed'] = 'yes';
							$markers[$k]['index'] = $indexcounter;
							if ($tabcounter == 30) {
								$indexcounter = $indexcounter + 1;
								$tabcounter = 0;
								$markersindex = $markersindex + 1;
							}
							$markers[$k]['index'] = $indexcounter;
							$markers[$k]['tabindex'] = $tabcounter;
							$tabcounter = $tabcounter + 1;
						}
					}
					$indexcounter = $indexcounter + 1;
				}
			}
		}
		// add $gmarks array
		$gmarks = $markers;
		$pid=$controller->record->getXref();
		// *** ENABLE STREETVIEW ***
		$STREETVIEW=get_module_setting('googlemap', 'GM_USE_STREETVIEW');
		?>
		
		<script>var ie = 0;</script>
		<!--[if IE]>
		<script>ie = 1;</script>
		<![endif]-->
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
		
			gicons["red"] = new google.maps.MarkerImage("//maps.google.com/mapfiles/marker.png",
				new google.maps.Size(20, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34)
			);
		
			var iconImage = new google.maps.MarkerImage("//maps.google.com/mapfiles/marker.png",
				new google.maps.Size(20, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34)
			);
		
			var iconShadow = new google.maps.MarkerImage("//www.google.com/mapfiles/shadow50.png",
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
			function createMarker(i, latlng, event, html, placed, index, tab, address, media, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, sv_point, marker_icon) {
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
					icon: icon_image,
					shadow: icon_shadow,
					map: map,
					title: address,
					zIndex: Math.round(latlng.lat()*-100000)<<5
				});
		
				// Store the tab and event info as marker properties
				marker.myindex = index;
				marker.mytab = tab;
				marker.myplaced = placed;
				marker.myevent = event;
				marker.myaddress = address;
				marker.mymedia = media;
				marker.sv_lati = sv_lati;
				marker.sv_long = sv_long;
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
					// marker.sv_elevation = 5;
				}
				if (sv_zoom == '' || sv_zoom == 0 || sv_zoom == 1) {
					marker.sv_zoom = 1.2;
				} else {
					marker.sv_zoom = sv_zoom;
				}
		
				marker.sv_latlng = new google.maps.LatLng(sv_lati, sv_long);
				gmarkers.push(marker);
		
				var sv_dir = [];
				sv_dir[i] = parseFloat(gmarkers[i].sv_bearing);

				var sv_elev = [];
				sv_elev[i] = parseFloat(gmarkers[i].sv_elevation);

				var sv_zoom = [];
				sv_zoom[i] = parseFloat(gmarkers[i].sv_zoom);
		
				// Open infowindow when marker is clicked
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.close();
					infowindow.setContent(contentString);
					infowindow.open(map, marker);
					var panoramaOptions = {
						position: marker.position,
						mode: 'html5',
						// mode: 'webgl',
						navigationControl: false,
						linksControl: false,
						addressControl: false,
						pov: {
							heading: sv_dir[i],
							pitch: sv_elev[i],
							// pitch: 5,
							zoom: sv_zoom[i]
						}
					};
		
					// Use jquery for info window tabs
					google.maps.event.addListener(infowindow, 'domready', function(){
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
		
					jQuery('#PH').click(function() {
						document.tabLayerEV = eval('document.getElementById("EV")');
						document.tabLayerEV.style.background = '#cccccc';
						document.tabLayerEV.style.paddingBottom = '0px';
						<?php if ($STREETVIEW) { ?>
						document.tabLayerSV = eval('document.getElementById("SV")');
						document.tabLayerSV.style.background = '#cccccc';
						document.tabLayerSV.style.paddingBottom = '0px';
						<?php } ?>
						document.panelLayer1 = eval('document.getElementById("pane1")');
						document.panelLayer1.style.display = 'none';
						<?php if ($STREETVIEW) { ?>
						document.panelLayer2 = eval('document.getElementById("pane2")');
						document.panelLayer2.style.display = 'none';
						<?php } ?>
					});
				});
			});
		}
		
			// Opens Marker infowindow when corresponding Sidebar item is clicked
			function myclick(i, index, tab) {
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
					<?php
					foreach($gmarks as $gmark) {
		
						// create thumbnail images of highlighted images
						if (!empty($pid)) {
							$this_person = WT_Individual::getInstance($pid);
						}
						if (!empty($gmark['name'])) {
							$person = WT_Individual::getInstance($gmark['name']);
						} else {
							$person = null;
						}
		
						// The current indi
						if (!empty($this_person)) {
							$class = 'pedigree_image';
							if (in_array($gmark['fact'], array('CENS', 'BIRT', 'BAPM', 'CHR', '_MILI', 'OCCU', 'RESI', 'DEAT', 'CREM', 'BURI', 'RETI'))) {
								$image = addslashes('<i class="icon_'.$gmark['fact'].'"></i>');
							} else {
								if ($SHOW_HIGHLIGHT_IMAGES) {
									$image = addslashes($this_person->displayImage());
									$image = str_replace("\n", "\\\n", $image); // May contain multi-line notes
								} else {
									$image = '';
								}
							}
						}
		
						// Other people
						if ($person) {
							if ($SHOW_HIGHLIGHT_IMAGES) {
								if (!empty($gmark['name'])) {
									$image2 = addslashes($person->displayImage());
									$image2 = str_replace("\n", "\\\n", $image2); // May contain multi-line notes
								} else {
									$image2 = '';
								}
							} else {
								$image2 = '';
							}
						} else {
							$image2 = '';
						}
					?>
						[
							// Elements 0-9. Basic parameters
							"<?php echo $gmark['fact_label']; ?>",
							"<?php echo $gmark['lati']; ?>",
							"<?php echo $gmark['lng']; ?>",
							"<?php if (!empty($gmark['date'])) { $date=new WT_Date($gmark['date']); echo addslashes($date->Display(true)); } else { echo WT_I18N::translate('Date not known'); } ?>",
							"<?php if (!empty($gmark['info'])) { echo addslashes($gmark['info']); } ?>",
							"<?php if (!empty($gmark['name'])) { $person=WT_Individual::getInstance($gmark['name']); if ($person) { echo '<a href=\"', $person->getHtmlUrl(), '\">', addslashes($person->getFullName()), '</a>'; } } ?>",
							"<?php echo addslashes($gmark['placerec']); ?>",
							"<?php echo $gmark['index']; ?>",
							"<?php echo $gmark['tabindex']; ?>",
							"<?php echo $gmark['placed']; ?>",
		
							// Element 10. location marker tooltip - extra printable item for marker title.
							"<?php echo WT_Filter::escapeJs($gmark['placerec']); ?>",
		
							// Element 11. persons Name
							"<?php if (!empty($gmark['name'])) { $person=WT_Individual::getInstance($gmark['name']); if ($person) { echo addslashes($person->getFullName()); } } ?>",
		
							// Element 12. Other people's Highlighted image.
							"<?php if (!empty($gmark['name'])) { echo $image2; } else { echo ''; } ?>",
		
							// Element 13. This Individual's Highlighted image.
							"<?php if (!empty($pid)) { echo $image; } else { echo ''; } ?>",
		
							// Elements 14-20 Streetview parameters
							"<?php if (!empty($gmark['media'])) { echo $gmark['media']; } ?>",
							"<?php if (!empty($gmark['sv_lati'])) { echo $gmark['sv_lati']; } ?>",
							"<?php if (!empty($gmark['sv_long'])) { echo $gmark['sv_long']; } ?>",
							"<?php if (!empty($gmark['sv_bearing'])) { echo $gmark['sv_bearing']; } ?>",
							"<?php if (!empty($gmark['sv_elevation'])) { echo $gmark['sv_elevation']; } ?>",
							"<?php if (!empty($gmark['sv_zoom'])) { echo $gmark['sv_zoom']; } ?>",
							"<?php if (!empty($gmark['icon'])) { echo $gmark['icon']; } ?>"
						],
		
					<?php } ?>
				];
				// Fix IE bug reporting one too many in locations.length statement
				if (ie==1) {
					locations.length=locations.length - 1;
				}
		
				// Set the Marker bounds
				var bounds = new google.maps.LatLngBounds ();
		
				// Calculate tabs to be placed for each marker
				var np = new Array();
				var numtabs = new Array();
				var npo = new Array();
		
				for (var p = 0; p < locations.length; p++) {
					np[p] = ''+p+'';
					numtabs[p] = 0;
					npo[p] = new Array();
					for (var q = 0; q < locations.length; q++) {
						if (jQuery.inArray(np[p], locations[q][7])==0) {
							npo[p][numtabs[p]] = q;
							numtabs[p]++;
						}
					}
				}
		
				// Loop through all location markers
				for (var i = 0; i < locations.length; i++) {
					// obtain the attributes of each marker
					var event        = locations[i][0];                         // Event or Fact
					var lat          = locations[i][1];                         // Latitude
					var lng          = locations[i][2];                         // Longitude
					var date         = locations[i][3];                         // Date of event or fact
					var info         = ''//locations[i][4];                     // info on occupation, or
					var name         = locations[i][5];                         // Persons name
					var address      = locations[i][6];                         // Address of event or fact
					var index        = locations[i][7];                         // index
					var tab          = locations[i][8];                         // tab index
					var placed       = locations[i][9];                         // Yes indicates multitab item
					var addr2        = locations[i][10];                        // printable address for marker title
					var name2        = locations[i][11];                        // printable name for marker title
					var point        = new google.maps.LatLng(lat,lng);         // Place Latitude, Longitude
					var media        = locations[i][14];                        // media item
					var sv_lati      = locations[i][15];                        // Street View latitude
					var sv_long      = locations[i][16];                        // Street View longitude
					var sv_bearing   = locations[i][17];                        // Street View bearing
					var sv_elevation = locations[i][18];                        // Street View elevation
					var sv_zoom      = locations[i][19];                        // Street View zoom
					var marker_icon  = locations[i][20];                        // Marker icon image (flag)
					var sv_point     = new google.maps.LatLng(sv_lati,sv_long); // StreetView Latitude and Longitide
		
					// Employ of image tab function using an information image
					if (media == null || media == '') {
						media = WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/facts/v3_image_info.png';
					} else {
						media = media;
					}
		
					// If a fact with info or a persons name
					var event_item = '';
					var event_tab  = '';
					var tabcontid  = '';
					var divhead    = '<h4 id="iwhead" >'+address+'</h4>';
		
					for (var n = 0; n < locations.length; n++) {
						if (i==npo[n][0] || i==npo[n][1] || i==npo[n][2] || i==npo[n][3] || i==npo[n][4] || i==npo[n][5] || i==npo[n][6] || i==npo[n][7] || i==npo[n][8] || i==npo[n][9] || i==npo[n][10] || i==npo[n][11] || i==npo[n][12] || i==npo[n][13] || i==npo[n][14] || i==npo[n][15] || i==npo[n][16] || i==npo[n][17] || i==npo[n][18] || i==npo[n][19] || i==npo[n][20] || i==npo[n][21] || i==npo[n][22] || i==npo[n][23] || i==npo[n][24] || i==npo[n][25]) {
							for (var x=0; x<numtabs[n]; x++) {
								tabcontid=npo[n][x];
								// If a fact with a persons name and extra info
								if (locations[tabcontid][4] && locations[tabcontid][5]) {
									event_tab+= [ '<table><tr><td class="highlt_img">'+locations[tabcontid][12]+'</td><td><p><span id="sp1">'+locations[tabcontid][0]+'</span><br>'+locations[tabcontid][4]+'<br><b>'+locations[tabcontid][5]+'</b><br>'+locations[tabcontid][3]+'<br></p></td></tr></table>' ];
								// or if a fact with a persons name
								} else if (locations[tabcontid][5]) {
									event_tab+= [ '<table><tr><td class="highlt_img">'+locations[tabcontid][12]+'</td><td><p><span id="sp1">'+locations[tabcontid][0]+'</span><br><b>'+locations[tabcontid][5]+'</b><br>'+locations[tabcontid][3]+'<br></p></td></tr></table>' ];
								// or if a fact with extra info
								} else if (locations[tabcontid][4]) {
									event_tab+= [ '<table><tr><td class="highlt_img">'+locations[tabcontid][13]+'</td><td><p><span id="sp1">'+locations[tabcontid][0]+'</span><br>'+locations[tabcontid][4]+'<br>'+locations[tabcontid][3]+'<br></p></td></tr></table>' ];
								// or just a simple fact
								} else {
									event_tab+= [ '<table><tr><td class="highlt_img">'+locations[tabcontid][13]+'</td><td><p><span id="sp1">'+locations[tabcontid][0]+'</span><br>'+locations[tabcontid][3]+'<br></p></td></tr></table>' ];
								}
							}
						}
					}
					var multitabs = [
					'<div class="infowindow">',
						'<div id = "gmtabs">',
							'<ul class="tabs" >',
								'<li><a href="#event" id="EV"><?php echo WT_I18N::translate('Events'); ?></a></li>',
								<?php if ($STREETVIEW) { ?>
								'<li><a href="#sview" id="SV"><?php echo WT_I18N::translate('Google Street View™'); ?></a></li>',
								<?php } ?>
		
							// To be used later === Do not delete
							//	'<li><a href="#image" id="PH">Image</a></li>',
							//	'<li><a href="#" id="SP">Aerial</a></li>',
		
							'</ul>',
		
							'<div class="panes">',
								'<div id = "pane1">',
									divhead,
									event_tab,
								'</div>',
								<?php if ($STREETVIEW) { ?>
								'<div id = "pane2">',
									divhead,
									'<div id="pano"></div>',
								'</div>',
								<?php } ?>
							'</div>',
						'</div>',
					'</div>'
					].join('');
		
					// create the marker
					var html      = multitabs;
					var zoomLevel = <?php echo $GOOGLEMAP_MAX_ZOOM; ?>;
					var marker    = createMarker(i, point, event, html, placed, index, tab, addr2, media, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, sv_point, marker_icon);
		
					// if streetview coordinates are available, use them for marker,
					// else use the place coordinates
					if (sv_point && sv_point != "(0, 0)") {
						var myLatLng = sv_point;
					} else {
						var myLatLng = point;
					}
		
					// Correct zoom level when only one marker is present
					if (i < 1) {
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
		$z = 0;

		foreach($markers as $marker) {
			echo '<tr>';
			echo '<td class="facts_label">';
			echo '<a href="#" onclick="myclick(', $z, ', ', $marker['index'], ', ', $marker['tabindex'], ')">', $marker['fact_label'], '</a></td>';
			$z++;
			echo '<td class="', $marker['class'], '" style="white-space: normal">';
			if (!empty($marker['info'])) {
				echo '<span class="field">', $marker['info'], '</span><br>';
			}
			if (!empty($marker['name'])) {
				$person=WT_Individual::getInstance($marker['name']);
				if ($person) {
					echo '<a href="', $person->getHtmlUrl(), '">', $person->getFullName(), '</a>';
				}
				echo '<br>';
			}
			echo WT_Filter::escapeHtml($marker['placerec']), '<br>';
			if (!empty($marker['date'])) {
				$date=new WT_Date($marker['date']);
				echo $date->Display(true), '<br>';
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table></div><br>';
	} // end prepare markers array

	echo '<br>';
	return $i;
}
