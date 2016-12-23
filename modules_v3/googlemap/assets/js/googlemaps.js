/**
 * webtrees: online genealogy
 * Copyright (C) 2016 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/* Create a singleton object - avoids polluting global space with lots of variables */
window.WT_GM_MODULE = (function() {
	'use strict';

	var params = {
		mapDiv:     '.gm-map',
		maxZoom:    0,
		title:      'Error: Title not set',
		mapOptions: {
			centre:  {lat: 0, lng: 0},
			mapType: 'ROADMAP',
			pegMan:  true
		}
	};

	var gmarkers       = []; // array to hold copy of each marker
	var infowindow     = new google.maps.InfoWindow({});
	var bounds         = new google.maps.LatLngBounds();
	var map;
	var panorama;
	var oms;
	var lineColors     = ['#000000', '#FF0000', '#0000FF', '#00FF00', '#FFFF00', '#00FFFF', '#FF00FF', '#C0C0FF', '#808000'];
	var iconColors     = ['blue', 'green', 'ltblue', 'pink', 'purple', 'red', 'yellow'];
	var clusterMarkers = typeof OverlappingMarkerSpiderfier == 'function';
	
	/**
	 * create the map
	 * @private
	 */
	var _createMap = function() {

		map = new google.maps.Map(document.querySelector(params.mapDiv), {
			zoom:                     6,
			center:                   params.mapOptions.centre,
			mapTypeId:                google.maps.MapTypeId[params.mapOptions.mapType],  // ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions:    {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
				style:    google.maps.NavigationControlStyle.SMALL   // ANDROID, DEFAULT, SMALL, ZOOM_PAN
			},
			streetViewControl:        params.mapOptions.pegMan,  // Show Pegman or not
			scrollwheel:              true
		});

		if (clusterMarkers) {
			oms = new OverlappingMarkerSpiderfier(map, {
				markersWontMove: true,
				markersWontHide: true,
				nearbyDistance:  5
			});
		}
	};

	/**
	 * Add the markers
	 * @param markers
	 * @param lines
	 * @private
	 */
	var _addMarkers = function(markers, lines) {
		var point;
		var sv_point;
		var marker;
		var id;

		// Add the markers
		jQuery.each(markers, function(index, item) {
			point = new google.maps.LatLng(item.lati, item.long);
			id = item.id || index;
			if (item.sv_lati && item.sv_long) {
				sv_point = new google.maps.LatLng(item.sv_lati, item.sv_long);
			} else {
				sv_point = point;
			}
			bounds.extend(point);

			var baseIcon;
			if (item.iconURL) {
				baseIcon = {
					url:    item.iconURL,
					origin: google.maps.Point(0, 0)
				};
			} else {
				baseIcon = {
					url:    'http://maps.google.com/mapfiles/ms/micons/' + iconColors[index % iconColors.length] + '-dot.png',
					size:   google.maps.Size(20, 34),
					origin: google.maps.Point(0, 0),
					anchor: google.maps.Point(9, 34)
				}
			}
			//Create a marker
			marker = new google.maps.Marker({
				icon:         baseIcon,
				map:          map,
				position:     point,
				title:        item.name,
				html:         item.html,
				zIndex:       100 + index,
				id:           id,
				baseicon:     baseIcon,
				name:         item.name,
				sv_point:     sv_point,
				sv_bearing:   parseFloat(item.sv_bearing || 0),
				sv_elevation: parseFloat(item.sv_elevation || 5),
				sv_zoom:      parseFloat(item.sv_zoom > 1.2 ? item.sv_zoom : 1.2)
			});

			if (clusterMarkers) {
				oms.addMarker(marker);
			}
			gmarkers[id] = marker;
		});

		// Add connecting lines if necessary
		if (typeof lines !== 'undefined') {
			var line;
			var plines;

			jQuery.each(lines, function(index, item) {
					line   = [
						new google.maps.LatLng(item.parent_lati, item.parent_long),
						new google.maps.LatLng(item.child_lati, item.child_long)
					];
					plines = new google.maps.Polygon({
						paths:         line,
						strokeColor:   lineColors[item.generation % lineColors.length],
						strokeOpacity: 0.8,
						strokeWeight:  3,
						fillColor:     "#FF0000",
						fillOpacity:   0.1
					});
					plines.setMap(map);
				}
			);
		}
		map.setCenter(bounds.getCenter());
		map.fitBounds(bounds);
	};

	/**
	 *
	 * @param marker
	 * @param collapse
	 * @private
	 */
	var _toggleIcon = function(marker, collapse) {
		var gm_icon;
		var title;
		if (collapse) {
			var grpsize = oms.markersNearMarker(marker).length + 1; // add in self
			var iconimg = grpsize > 29 ? 'marker_orange+.png' : 'marker_orange' + grpsize + '.png';
			gm_icon     = {
				url:    WT_STATIC_URL + WT_MODULES_DIR + 'googlemap/images/' + iconimg,
				size:   google.maps.Size(20, 34),
				origin: google.maps.Point(0, 0),
				anchor: google.maps.Point(9, 34)
			};
			title       = params.title;
		} else {
			gm_icon = marker.baseicon;
			title   = marker.name;
		}
		gmarkers[marker.id].setIcon(gm_icon);
		gmarkers[marker.id].setTitle(title);
	};

	/**
	 *
	 * @private
	 */
	var _spiderfyMarkerIcons = function() {
		var spiderfied = oms.markersNearAnyOtherMarker();
		var result;
		var count;
		// Toggle icons depending upon whether marker is in a cluster or not
		jQuery.each(gmarkers, function(index, item) {
			if (typeof item !== 'undefined') { // For Pedigree Map gmarkers[0] is undefined (index is SOSA number)
				result = jQuery.grep(spiderfied, function(n, i) {
					return n.id == item.id;
				});
				_toggleIcon(item, result.length);
			}
		});
	};

	/**
	 * Event listeners
	 * @private
	 */
	var _addListeners = function() {
		jQuery(function() {
			if (clusterMarkers) {
				oms
					.addListener('click', function(marker, event) {
						infowindow.setContent('<div class="gm-info-window">' + marker.html + '</div>');
						infowindow.open(map, marker);
						jQuery('.gm-ancestor.person_box')
							.addClass('gm-ancestor-visited')
							.removeClass('person_box');
						jQuery('.gm-ancestor[data-marker=' + marker.id + ']')
							.addClass('person_box')
							.removeClass('gm-ancestor-visited');
					})
					.addListener('spiderfy', function(s_markers, n_markers) {
						jQuery.each(s_markers, function(index, item) {
							_toggleIcon(item, false);
						});
					})
					.addListener('unspiderfy', function(s_markers, n_markers) {
						jQuery.each(oms.markersNearAnyOtherMarker(), function(index, item) {
							_toggleIcon(item, true);
						});
					});

				google.maps.event.addListener(map, 'idle', function() {
					_spiderfyMarkerIcons();
				});
			} else {
				jQuery.each(gmarkers, function(index, item) {
					google.maps.event.addListener(item, "click", function() {
						infowindow.close();
						infowindow.setContent(item.html);
						infowindow.open(map, item);
					});
				});
			}

			// Resize map on load
			google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
				if (this.getZoom() > params.maxZoom) {
					this.setZoom(params.maxZoom);
				}
			});

			google.maps.event.addListener(map, 'click', function() {
				infowindow.close();
				jQuery('.gm-ancestor.person_box')
					.removeClass('person_box')
					.addClass('gm-ancestor-visited');
			});

			google.maps.event.addListener(infowindow, 'domready', function() {
				jQuery('#gm-tab-events, #gm-tab-streetview')
					.off('click')
					.on('click', function(e) {
						var self = jQuery(this);
						var marker;
						jQuery('.gm-tabs').children().toggleClass('gm-tab-active');
						jQuery('.gm-panes').children().toggle();
						if (self.attr('id') == 'gm-tab-streetview' && typeof marker == 'undefined') {
							marker = infowindow.anchor;
							new google.maps.StreetViewPanorama(document.querySelector(".gm-streetview"), {
								mode:              'html5',
								navigationControl: false,
								linksControl:      false,
								addressControl:    false,
								position:          marker.sv_point,
								pov:               {
									heading: marker.sv_bearing,
									pitch:   marker.sv_elevation,
									zoom:    marker.sv_zoom
								}
							});
						}
					});
			});

			google.maps.event.addListener(infowindow, 'closeclick', function() {
				google.maps.event.trigger(map, "click");
			});

		});
	};

	// Public access point
	return {
		// Do all the work here
		createMap: function(p, markers, lines) {
			jQuery.extend(params, p);
			_createMap();
			_addMarkers(markers, lines);
			_addListeners();
		},
		markerClick: function(markerId) {
			google.maps.event.trigger(gmarkers[markerId], "click");
		}
	};
});