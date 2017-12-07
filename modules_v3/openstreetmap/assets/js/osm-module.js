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
window.WT_OSM = (function() {

	let baseData     = {};
	let map          = null;
	let zoom         = null;
	let markers      = L.markerClusterGroup({
		showCoverageOnHover: false
	});

	let resetControl = L.Control.extend({
		options: {
			position: 'topleft'
			//control position - allowed: 'topleft', 'topright', 'bottomleft', 'bottomright'
		},

		onAdd: function (map) {
			let container     = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
			container.onclick = function () {
				if (zoom && baseData.animate) {
					map.flyTo(markers.getBounds().getCenter(), zoom);
				} else if (zoom) {
					map.setView(markers.getBounds().getCenter(), zoom);
				} else if (baseData.animate) {
					map.flyToBounds(markers.getBounds().pad(0.2));
				} else {
					map.fitBounds(markers.getBounds().pad(0.2));
				}
				return false;
			};
			let anchor   = L.DomUtil.create('a', 'leaflet-control-reset', container);
			anchor.href  = '#';
			anchor.title = baseData.I18N.reset;
			anchor.role  = 'button';
			$(anchor).attr('aria-label', 'reset');
			let image = L.DomUtil.create('i', 'fas fa-redo', anchor);
			image.alt = baseData.I18N.reset;

			return container;
		},
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
	});
		L.tileLayer.provider(baseData.providerName, baseData.providerOptions).addTo(map);
		L.control.zoom({ // Add zoom with localised text
			zoomInTitle : baseData.I18N.zoomInTitle,
			zoomOutTitle: baseData.I18N.zoomOutTitle,
		}).addTo(map);
	};

	/**
	 *
	 * @param xref string
	 * @param tree integer
	 * @param mapType string
	 * @param Generations integer
	 * @private
	 */
	let _addLayer = function (xref, tree, mapType, Generations) {
		let geoJsonLayer;
		let domObj  = '.osm-sidebar';
		let sidebar = '';

		$.getJSON('module.php', {
			mod        : 'openstreetmap',
			mod_action : 'get_mapdata',
			rootid     : xref,
			tree       : tree,
			type       : mapType,
			generations: Generations,
		})

		.done(function (data, textStatus, jqXHR) {
			if (jqXHR.status === 200 && data.features.length === 1) {
				zoom = data.features[0].properties.zoom;
			}
			geoJsonLayer = L.geoJson(data, {
				pointToLayer : function (feature, latlng) {
					return new L.Marker(latlng, {
						icon : L.BeautifyIcon.icon({
							icon           : feature.properties.icon['name'],
							borderColor    : 'transparent',
							backgroundColor: feature.properties.icon['color'],
							iconShape      : 'marker',
							textColor      : 'white',
						}),
						title: feature.properties.tooltip,
						alt  : feature.properties.tooltip,
						id   : feature.id
					})
						.on('popupopen', _onPopupOpen)
						.on('popupclose', _onPopupClose);
				},
				onEachFeature: function (feature, layer) {
					if (feature.properties.polyline) {
						let pline = L.polyline(feature.properties.polyline.points, feature.properties.polyline.options);
						markers.addLayer(pline);
					}
					layer.bindPopup('<span class="label">' + feature.properties.name + '</span>' + feature.properties.summary);
					sidebar += '<div class="gchart osm-event" data-id="' + feature.id + '">' + feature.properties.name + feature.properties.summary + '</div>';
				}
			});
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			console.log(jqXHR, textStatus, errorThrown);
		})
		.always(function (data_jqXHR, textStatus, jqXHR_errorThrown) {
			switch (jqXHR_errorThrown.status) {
				case 200: // Success
					$(domObj).append(sidebar);
					markers.addLayer(geoJsonLayer);
					map
						.addControl(new resetControl())
						.addLayer(markers)
						.fitBounds(markers.getBounds().pad(0.2));
					if (zoom) {
						map.setView(markers.getBounds().getCenter(), zoom);
					}
					break;
				case 204: // No data
					map.fitWorld();
					$(domObj).append('<div role="alert" class="alert alert-info">' + baseData.I18N.noData + '</div>');
					break;
				default: // Anything else
					map.fitWorld();
					$(domObj).append('<div role="alert" class="alert alert-danger">' + baseData.I18N.error + '</div>');
			}
			$(domObj).slideDown(300);
		});
	};

	/**
	 *
	 * @param e
	 * @private
	 */
	let _onPopupOpen = function (e) {
		let sidebar = $('.osm-sidebar').filter(':visible');
		let item	= sidebar.children(".osm-event[data-id=" + e.target.feature.id + "]");
		item.addClass('alert alert-success');
		sidebar.scrollTo(item);
	};

	/**
	 *
	 * @private
	 */
	let _onPopupClose = function () {
		$(".osm-event")
			.removeClass('alert alert-success');
	};

	/**
	 *
	 * @param elem
	 * @returns {$}
	 */

	$.fn.scrollTo = function (elem) {
		let _this = $(this);
		_this.animate({
			scrollTop: elem.offset().top - _this.offset().top + _this.scrollTop()
		});
		return this;
	};

	/**
	 *
	 * @param individual string
	 * @param tree integer
	 * @param mapType string
	 * @param generations integer
	 * @private
	 */
	let _initialize = function (individual, tree, mapType, generations) {
		// Activate marker popup when sidebar entry clicked
		$(function () {
			$('.osm-sidebar')
			// open marker popup if sidebar event is clicked
				.on('click', '.osm-event', function (e) {
					// first close any existing
					map.closePopup();
					let eventId = $(this).data('id');
					//find the marker corresponding to the clicked event
					let mkrLayer = markers.getLayers().filter(function (v) {
						return typeof(v.feature) !== 'undefined' && v.feature.id === eventId;
					});
					let mkr = mkrLayer.pop();
					// Unfortunately zoomToShowLayer zooms to maxZoom
					markers.zoomToShowLayer(mkr, function (e) {
						mkr.openPopup();
					});
					return false;
				})
				.on('click', 'a', function (e) { // stop click on a person also opening the popup
					e.stopPropagation();
				});
		});

		$.getJSON('module.php', {
			mod       : 'openstreetmap',
			mod_action: 'get_basedata',
		})
			.done(function (data, textStatus, jqXHR) {
				$.extend(true, baseData, data);
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				console.log(jqXHR, textStatus, errorThrown);
			})
			.always(function (data_jqXHR, textStatus, jqXHR_errorThrown) {
				_drawMap();
				_addLayer(individual, tree, mapType, generations);
			});
	};

	return {
		/**
		 *
		 * @param individual string
		 * @param tree integer
		 * @param mapType string
		 * @param generations integer
		 */
		drawMap: function (individual, tree, mapType, generations) {
			mapType     = typeof mapType !== 'undefined' ? mapType : 'individual';
			generations = typeof generations !== 'undefined' ? generations : null;
			_initialize(individual, tree, mapType, generations);
		}
	};
})();
