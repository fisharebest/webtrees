"use strict";

/* global L, console*/

/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

/**
 *
 * @type {{drawMap}}
 */
window.WT_OSM_ADMIN = (function() {

	let baseData = {};
	let map      = null;
	let marker   = L.marker([0,0], {
		draggable: true
	});
	/**
	 *
	 * @private
	 */
	let _drawMap = function () {
		map = L.map('osm-map', {
				center     : [0, 0],
				minZoom    : baseData.minZoom, // maxZoom set by leaflet-providers.js
				zoomControl: false, // remove default
			}
		);
		L.tileLayer.provider(baseData.providerName, baseData.providerOptions).addTo(map);
		L.control.zoom({ // Add zoom with localised text
			zoomInTitle : baseData.I18N.zoomInTitle,
			zoomOutTitle: baseData.I18N.zoomOutTitle,
		}).addTo(map);

		marker
			.on('dragend', function (e) {
				let coords = marker.getLatLng();
				map.panTo(coords);
				_update_Controls({
					place : '',
					coords: coords,
					zoom  : map.getZoom(),
				});
			})
			.addTo(map);
		let searchControl = new window.GeoSearch.GeoSearchControl({
			provider       : new window.GeoSearch.OpenStreetMapProvider(),
			retainZoomLevel: true,
			autoClose      : true,
			showMarker     : false,
		});

		map
			.addControl(searchControl)
			.on('geosearch/showlocation', function (result) {
				let lat   = result.location.y;
				let lng   = result.location.x;
				let place = result.location.label.split(',', 1);

				marker.setLatLng([lat, lng]);
				map.panTo([lat, lng]);

				_update_Controls({
					place : place.shift(),
					coords: {
						'lat' : lat,
						'lng' : lng
					},
					zoom  : map.getZoom(),
				});
			})
			.on('zoomend', function (e) {
				$('#new_zoom_factor').val(map.getZoom());
				map.panTo(marker.getLatLng());
			});
	};

	/**
	 *
	 * @param id
	 * @private
	 */
	let _addLayer = function (id) {
		$.getJSON('index.php?route=admin-module', {
			module: 'openstreetmap',
			action: 'AdminMapData',
			id    : id,
		})
			.done(function (data, textStatus, jqXHR) {
				marker.setLatLng(data.coordinates);
				map.setView(data.coordinates, data.zoom);
			})

			.fail(function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR, textStatus, errorThrown);
			})
			.always(function (data_jqXHR, textStatus, jqXHR_errorThrown) {
				switch (jqXHR_errorThrown.status) {
					case 200: // Success
						break;
					case 204: // No data
						map.fitWorld();
						break;
					default: // Anything else
						map.fitWorld();
				}
			});
	};

	/**
	 *
	 * @param newData
	 * @private
	 */
	let _update_Controls = function (newData) {
		let placeEl = $('#new_place_name');
		if (!placeEl.val().length && newData.place.length) {
			placeEl.val(newData.place);
		}
		$('#new_place_lati').val(Number(newData.coords.lat).toFixed(5)); // 5 decimal places (about 1 metre accuracy)
		$('#new_place_long').val(Number(newData.coords.lng).toFixed(5));
		$('#new_zoom_factor').val(Number(newData.zoom));
	};

	$(function () {
		$('.editable').on('change', function (e) {
			let lat	= $('#new_place_lati').val();
			let lng	= $('#new_place_long').val();
			marker.setLatLng([lat, lng]);
			map.panTo([lat, lng]);
		});
	});

	/**
	 *
	 * @param id
	 */
	let initialize = function (id) {
		$.getJSON('index.php?route=admin-module', {
			module: 'openstreetmap',
			action: 'BaseData',
		})
			.done(function (data, textStatus, jqXHR) {
				$.extend(true, baseData, data);
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR, textStatus, errorThrown);
			})
			.always(function (data_jqXHR, textStatus, jqXHR_errorThrown) {
				_drawMap();
				_addLayer(id);
			});
	};

	return {
		/**
		 *
		 * @param id
		 */
		drawMap: function (id) {
			initialize(id);
		}
	};
})();
