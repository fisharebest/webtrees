/**
 * Javascript module for Googlemap
 *
 * This module contains the Javasript functions needed by the Googlemap
 * module of webtrees
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team. All rights reserved.
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
 * @subpackage Display
 * @version $Id: wt_googlemap.js$
 * $Id$
 */

    var markers   = [];
    var Boundaries = new GLatLngBounds();
    var map;
    var mapready = 0;

    function highlight(index, tab) {
        GEvent.trigger( markers[index], "click", tab);
    } 

    function SetBoundaries(MapBounds) {
        Boundaries = MapBounds;
    }

    function ResizeMap() {
        var clat = 0.0;
        var clng = 0.0;
        var zoomlevel = 1;

        if (mapready == 1)
        {
            clat = (Boundaries.getNorthEast().lat() + Boundaries.getSouthWest().lat())/2;
            clng = (Boundaries.getNorthEast().lng() + Boundaries.getSouthWest().lng())/2;
            zoomlevel = map.getBoundsZoomLevel(Boundaries);
            for(i = 0; ((i < 10) && (zoomlevel == 1)); i++) {
                zoomlevel = map.getBoundsZoomLevel(Boundaries);
            }
            zoomlevel = zoomlevel-1;
            map.setCenter(new GLatLng(clat, clng));
            if (zoomlevel < minZoomLevel) {
                zoomlevel = minZoomLevel;
            }
            if (zoomlevel > startZoomLevel) {
                zoomlevel = startZoomLevel;
            }
            map.checkResize();
            map.setCenter(new GLatLng(clat, clng), zoomlevel);
            map.savePosition();
        }
    }

    function AddMarker(Marker) {
        map.addOverlay(Marker);
        markers.push(Marker);
    }

    function loadMap(maptype) {
        var pointArray = [];
        if (GBrowserIsCompatible()) {
            map = new GMap2(document.getElementById("map_pane"));
			map_type = new Map_type();
			map.addControl(map_type);
			GEvent.addListener(map,'maptypechanged',function()
			{
				map_type.refresh();
			});
			// for further street view
			//map.addControl(new GLargeMapControl3D(true));
            map.addControl(new GLargeMapControl3D());
            map.addControl(new GScaleControl());
			var mini = new GOverviewMapControl();
			map.addControl(mini);
			// displays blank minimap - probably google api's bug
			//mini.hide();
            map.setCenter(new GLatLng( 0.0, 0.0), 0, maptype );
            mapready = 1;
            ResizeMap();
			// Our info window content
		}
	}