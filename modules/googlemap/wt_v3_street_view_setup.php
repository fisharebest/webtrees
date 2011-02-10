<?php
/**
 * Displays the streetview map setup page
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
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
 * @subpackage Googlemaps v3
 * @version $Id$
 *
 * @author Brian Holland (windmillway)
 */
 ?>

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps JavaScript API V3: Street View Layer</title>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script type="text/javascript">
    

    
	var qsParm = new Array();
	function qs() {
		var query = window.location.search.substring(1);
		var parms = query.split('&');
		for (var i=0; i<parms.length; i++) {
			var pos = parms[i].indexOf('=');
			if (pos > 0) {
				var key = parms[i].substring(0,pos);
				var val = parms[i].substring(pos+1);
				qsParm[key] = val;
			}
		}
	} 	
	qsParm['x'] = null;
	qsParm['y'] = null;
	qs();
    
    var map;
    
	function roundNumber(num, dec) {
		var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
		return result;
	}
    
    function initialize() {
    	
		var x = qsParm['x'];
    	var y = qsParm['y'];
    	var b = parseFloat(qsParm['b']);
    	var p = parseFloat(qsParm['p']);
    	var m = parseFloat(qsParm['m']);
    
      	var sv_location = new google.maps.LatLng(y, x);
   	
		var mapOptions = {
			zoom: 14,
			center: sv_location,
			mapTypeId: google.maps.MapTypeId.ROADMAP,					// ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU 	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControl: true,
      		navigationControlOptions: {
   				position: google.maps.ControlPosition.TOP_RIGHT,		// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
   				style: google.maps.NavigationControlStyle.SMALL			// ANDROID, DEFAULT, SMALL, ZOOM_PAN
      		},
      		streetViewControl: true,									// Show Pegman or not
      		scrollwheel: false
      		
		};
      	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);


    	// === Next, get the map's default panorama and set up some defaults. ======================
    	var bearing = b;
    	var pitch = p;
    	var svzoom = m;
    	
    	// --- First check if Browser supports html5 ---
    	var browserName=navigator.appName;
    	if (browserName=="Microsoft Internet Explorer") {
    	    var render_type = '';
    	} else {
    	   	var render_type = 'html5';
    	}

		// --- Create the panorama ---   	 	
      	var panoramaOptions = {
      		navigationControl: false,
      		navigationControlOptions: {
      			position: google.maps.ControlPosition.TOP_LEFT,			// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
      			style: google.maps.NavigationControlStyle.ANDROID  		// ANDROID, DEFAULT, SMALL, ZOOM_PAN
      		},
      		linksControl: false,
      		addressControl: false,
      		addressControlOptions: {
      			style: {
      				// display: 'none',									// USE CSS notation here
      				// backgroundColor: 'red'
      			}
      		},
        	position: sv_location,
        	mode: render_type, 
       		pov: {
          		heading: 0,
          		pitch: 0,
          		zoom: 1
          		//heading: bearing,
          		//pitch: pitch,
          		//zoom: svzoom
        	}
      	};  
      	var panorama = new google.maps.StreetViewPanorama(document.getElementById("pano"),panoramaOptions);
      	
    	map.setStreetView(panorama);
    	
    	// Enable navigator contol and address control to be toggled with right mouse button -------
		var aLink = document.createElement('a');
		aLink.href = "javascript:void(0)"; onmousedown=function(e) {
 			if (parseInt(navigator.appVersion)>3) {
  				var clickType=1;
  				if (navigator.appName=="Netscape") {
  					clickType=e.which;
  				} else {
  					clickType=event.button;
  				}
  				if (clickType==1) {
  					self.status='Left button!';
  				}
  				if (clickType!=1) {
					if (panorama.get('addressControl') == false) {
						panorama.set('addressControl', true);
						panorama.set('linksControl', true);
					} else {
						panorama.set('addressControl', false);
						panorama.set('linksControl', false);
					}
  				}
 			}
 			return true;
			if (parseInt(navigator.appVersion)>3) {
 				document.onmousedown = mouseDown;
 			if (navigator.appName=="Netscape") 
  				document.captureEvents(Event.MOUSEDOWN);
			}
		};
		panorama.controls[google.maps.ControlPosition.TOP_RIGHT].push(aLink);
		// -----------------------------------------------------------------------------------------
		
		google.maps.event.addListener(panorama, 'pov_changed', function() {
			povLevel = panorama.getPov();
        	parent.document.getElementById('sv_bearText').value = roundNumber(povLevel.heading, 2)+"\u00B0";
        	parent.document.getElementById('sv_elevText').value = roundNumber(povLevel.pitch, 2)+"\u00B0";
        	parent.document.getElementById('sv_zoomText').value = roundNumber(povLevel.zoom, 2);
		});	
		google.maps.event.addListener(panorama, 'position_changed', function() {
			pos = panorama.getPosition();		
        	parent.document.getElementById('sv_latiText').value = pos.lat()+"\u00B0";
        	parent.document.getElementById('sv_longText').value = pos.lng()+"\u00B0";
		});	
		
		//==========================================================================================
		//  CREATE THE MAP PANE STREETVIEW BLUE STREETS
		//======================================================================================
		//	First lets create the Traffic ImageMap
		//--------------------------------------------------------------------------------------
		var traffic = new google.maps.ImageMapType({
			getTileUrl: function(coord, zoom) {
				var X = coord.x % (1 << zoom);  // wrap
				return "http://mt3.google.com/mapstt?" +
					"zoom=" + zoom + "&x=" + X + "&y=" + coord.y + "&client=api";
			},
			tileSize: new google.maps.Size(256, 256),
			isPng: true
		});
		//======================================================================================
		//	Now add the ImageMapType overlay to the map
		//--------------------------------------------------------------------------------------
		map.overlayMapTypes.push(traffic);
		map.overlayMapTypes.push(null);
		//======================================================================================
		//	Now create the StreetView ImageMap
		//--------------------------------------------------------------------------------------
		// http://cbk0.google.com/cbk?output=overlay&zoom=12&x=2045&y=1361&cb_client=api
		var street = new google.maps.ImageMapType({
			getTileUrl: function(coord, zoom) {
				var X = coord.x % (1 << zoom);  // wrap
				return "http://cbk0.google.com/cbk?output=overlay&" +
					"zoom=" + zoom + "&x=" + X + "&y=" + coord.y + "&cb_client=api";
			},
			tileSize: new google.maps.Size(256, 256),
			isPng: true
		});
		//======================================================================================
		//  Add the Street view Image Map
		//--------------------------------------------------------------------------------------
		map.overlayMapTypes.setAt(1, street);
		//==========================================================================================
	
    }
    
	function resetview() {
		initialize();
	}
    
    // Instead of handling events directly in the tags attributes (ie. <body onload="...">),
    // listen to the events programmatically:
	google.maps.event.addDomListener(window, 'load', initialize);

    </script>
</head>
  
<body>  
  	<style>

  	#butt1, #butt2 {
  		width:90px;
  	}
  	#map_canvas {
    	width: 520px;
    	height: 250px;
    	margin: 0 auto;
    	margin-top: -10px;
		border:1px solid black;
  	}
  	#warning {
    	margin: 0 auto;
    	width: 520px;
    	height: 100px;
		border:0px solid black;
		text-align: left;
		font: 12px verdana; color:red;
		padding-left: 4px;
		margin-top:-5px;
		font-weight: normal;
  	}
  	#warning h5 {
  		font: 11px verdana; color:red;
  		font-weight: normal;
  	}
  	#pano {
    	margin: 0 auto;
  		margin-top:3px;
    	width: 520px;
    	height: 350px;
		border:1px solid black;
  	}
  	/*
  	#inputs {
    	margin: 0 auto;
  		background: #cccccc; 
  		margin-top: 0px; 
  		width: 510px; 
  		height: 15px;
  		padding-bottom: 10px; 
  		border:1px solid grey;
  	}
  	#inputs form {
  		margin-left: -8px;
  		font: 11px verdana; color:blue;
  	}
*/
  	#inputs form input {
  		background: #cccccc;
  	}
  	div {
  		text-align: center;
  	}
  	</style>
  	
  	<div id="toggle">
  		<form name="myForm" title="myForm">
  			<!-- <input id="butt1" name ="butt1" type="button" value="Map" onclick="toggleStreetView();"></input> -->
  				<input id="butt2" name ="butt2" type="button" value="Reset" onclick="resetview();"></input>
  		</form>
  	</div>

    <div id="map_canvas"> </div>
<!--    
	<div id="warning">
	<h5>
		<b>No Streetview coordinates are saved yet.</b><br />
		a. 	If no Streetview is displayed below, drag the "Pegman" to a "blue" Street on the map. <br />
		b. 	When the Streetview is displayed, adjust as necessary to enable the required view. <br />
			&nbsp;&nbsp;&nbsp; (Right mouse click to toggle Street view navigation arrows.) <br /> 
		c. 	When the required view is displayed, click the button "Save View".
	</h5>
	</div>
-->
	<div id="pano"></div>
	
<!--
    <div id="inputs">
	<?php
/*
		$placeid	= "";			// Placelocation place id
		$sv_lat		= "";			// StreetView Point of View Latitude
		$sv_lng 	= "";			// StreetView Point of View Longitude	
		$sv_bear 	= "0&#176;";	// StreetView Point of View Bearing/Heading/Yaw	(degrees from North)
		$sv_elev	= "0&#176;";	// StreetView Point of View Elevation/Pitch 	(+90 to -90 degrees (+=down, -=up)
		$sv_zoom	= "1";			// StreetView Point of View Zoom 				(0.00 to 5.00 )

 		$list_latlon = ("
			lati<input 		name='sv_latiText' id='sv_latiText' type='text' style='width:67px; border:none;' value='' 	/>
			long<input 		name='sv_longText' id='sv_longText' type='text' style='width:67px; border:none;' value='' 	/>
			bear<input 		name='sv_bearText' id='sv_bearText' type='text' style='width:50px; border:none;' value='".$sv_bear."' 	/>&nbsp;
			elev<input 		name='sv_elevText' id='sv_elevText' type='text' style='width:43px; border:none;' value='".$sv_elev."' 	/>
			zoom<input 		name='sv_zoomText' id='sv_zoomText' type='text' style='width:26px; border:none;' value='".$sv_zoom."'	/>
			&nbsp;
		");
	
		echo "<table>";
		echo "<tr><td align=\"center\">\n";
			echo "<form method=\"post\" action=\"\">";
			echo "&nbsp; ".$list_latlon;
			echo "<input style=\"background:white;\"type=\"submit\" name=\"Submit\" id=\"Submit\" onClick=\"update_sv_params($placeid);\" value=\"Save View\">";
			echo "</form>";
		echo "</td></tr>\n";
		echo "</table>\n";
*/
	?>
	</div>
-->

</body>  
</html>
