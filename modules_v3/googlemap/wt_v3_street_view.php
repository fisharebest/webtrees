<?php
// Displays a streetview map
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
//
// $Id$

header('Content-type: text/html; charset=UTF-8');

?>
 
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">

	// Following function creates an array of the google map parameters passed ---------------------    
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
	// ---------------------------------------------------------------------------------------------

	
	// ---------------------------------------------------------------------------------------------

var geocoder = new google.maps.Geocoder();

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

function updateMarkerStatus(str) {
  	document.getElementById('markerStatus').innerHTML = str;
}



//function updateMarkerBearing(bearing) {
//  	document.getElementById('sv_bearText').value; = str;
//}

//    var bearing = document.getElementById('sv_bearText').value;

function updateMarkerPosition(latLng) {
    //    document.getElementById('sv_latiText').value = pos.lat()+"\u00B0";
    //    document.getElementById('sv_longText').value = pos.lng()+"\u00B0";
  	document.getElementById('info').innerHTML = [
    	latLng.lat(),
    	latLng.lng()
  	].join(', ');
}

function updateMarkerAddress(str) {
  	document.getElementById('address').innerHTML = str;
}

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
    	
  	var latLng = new google.maps.LatLng(y, x);
  	  
  	// Create the map and mapOptions
	var mapOptions = {
		zoom: 16,
		center: latLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,					// ROADMAP, SATELLITE, HYBRID, TERRAIN
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU 	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
		},
		navigationControl: true,
      	navigationControlOptions: {
   			position: google.maps.ControlPosition.TOP_RIGHT,		// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
   			style: google.maps.NavigationControlStyle.SMALL			// ANDROID, DEFAULT, SMALL, ZOOM_PAN
      	},
      	streetViewControl: false,									// Show Pegman or not
      	scrollwheel: true     		
	};
	
    var map = new google.maps.Map(document.getElementById('mapCanvas'), mapOptions);

    var bearing = b;
    if (bearing < 0) {
    	bearing=bearing+360;
    }
    var pitch = p;
    var svzoom = m;
    
	var imageNum = Math.round(bearing/22.5) % 16;  
      
  	var image = new google.maps.MarkerImage('images/panda-icons/panda-' + imageNum + '.png',
      	// This marker is 50 pixels wide by 50 pixels tall.
      	new google.maps.Size(50, 50),
      	// The origin for this image is 0,0.
      	new google.maps.Point(0, 0),
      	// The anchor for this image is the base of the flagpole at 0,32.
      	new google.maps.Point(26, 36)
    );

  	var shape = {
      	coord: [1, 1, 1, 20, 18, 20, 18 , 1],
     	type: 'poly'
  	};
 	
  	var marker = new google.maps.Marker({
        icon: image,
        // shape: shape, 
    	position: latLng,
    	title: 'Drag me to a Blue Street',
    	map: map,
    	draggable: true
  	});
  	
	
    // ===Next, get the map's default panorama and set up some defaults. ===========================
    
    // --- First check if Browser supports html5 ---
    var browserName=navigator.appName;
    if (browserName=='Microsoft Internet Explorer') {
        var render_type = '';
    } else {
       	var render_type = 'html5';
    }

	// --- Create the panorama ---
    var panoramaOptions = {
      	navigationControl: false,
      	navigationControlOptions: {
      		position: google.maps.ControlPosition.TOP_RIGHT,	// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
      		style: google.maps.NavigationControlStyle.SMALL  	// ANDROID, DEFAULT, SMALL, ZOOM_PAN
      	},
      	linksControl: false,
      	addressControl: false,
      	addressControlOptions: {
      		style: {
      			// display: 'none',									// USE CSS notation here
      			// backgroundColor: 'red'
      		}
      	},
        position: latLng,
        mode: render_type, 
       	pov: {
          	heading: bearing,
          	pitch: pitch,
          	zoom: svzoom
        }
    };
	panorama = new google.maps.StreetViewPanorama(document.getElementById('mapCanvas'), panoramaOptions);
	panorama.setPosition(latLng);
	setTimeout(function() { panorama.setVisible(true); }, 1000);
    setTimeout(function() { panorama.setVisible(true); }, 2000);
    setTimeout(function() { panorama.setVisible(true); }, 3000);


    	// Enable navigator contol and address control to be toggled with right mouse button -------
		var aLink = document.createElement('a');
		aLink.href = 'javascript:void(0)'; onmousedown=function(e) {
 			if (parseInt(navigator.appVersion)>3) {
  				var clickType=1;
  				if (navigator.appName=='Netscape') {
  					clickType=e.which;
  				} else {
  					clickType=event.button;
  				}
  				if (clickType==1) {
  					self.status='Left button!';
  				}
  				if (clickType!=1) {
					if (panorama.get('addressControl') == false) {
						panorama.set('navigationControl', false);
						panorama.set('addressControl', true);
						panorama.set('linksControl', true);
					} else {
						panorama.set('navigationControl', false);
						panorama.set('addressControl', false);
						panorama.set('linksControl', false);
					}
  				}
 			}
 			return true;
			if (parseInt(navigator.appVersion)>3) {
 				document.onmousedown = mouseDown;
 			if (navigator.appName=='Netscape') 
  				document.captureEvents(Event.MOUSEDOWN);
			}
		};
		panorama.controls[google.maps.ControlPosition.TOP_RIGHT].push(aLink);
		// -----------------------------------------------------------------------------------------


  	// Update current position info.
  	updateMarkerPosition(latLng);
  	geocodePosition(latLng);
  
  	// Add dragging event listeners.
  	google.maps.event.addListener(marker, 'dragstart', function() {
    	updateMarkerAddress('Dragging...');
  	});
  
  	google.maps.event.addListener(marker, 'drag', function() {
    	updateMarkerStatus('Dragging...');
    	updateMarkerPosition(marker.getPosition());
    	panorama.setPosition(marker.getPosition());
  	});
  
  	google.maps.event.addListener(marker, 'dragend', function() {
    	updateMarkerStatus('Drag ended');
    	geocodePosition(marker.getPosition());
  	});
  	
	google.maps.event.addListener(panorama, 'pov_changed', function() {
		povLevel = panorama.getPov();
        parent.document.getElementById('sv_bearText').value = roundNumber(povLevel.heading, 2)+"\u00B0";
        parent.document.getElementById('sv_elevText').value = roundNumber(povLevel.pitch, 2)+"\u00B0";
        parent.document.getElementById('sv_zoomText').value = roundNumber(povLevel.zoom, 2);
	});	
  	
	google.maps.event.addListener(panorama, 'position_changed', function() {		
		pos = panorama.getPosition();
		marker.setPosition(pos);
        parent.document.getElementById('sv_latiText').value = pos.lat()+"\u00B0";
        parent.document.getElementById('sv_longText').value = pos.lng()+"\u00B0";
	});
	
	
	//==============================================================================================
	//  CREATE THE MAP PANE STREETVIEW BLUE STREETS
	//======================================================================================
	//	First lets create the Traffic ImageMap
	//--------------------------------------------------------------------------------------
	var traffic = new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			var X = coord.x % (1 << zoom);  // wrap
			return 'http://mt3.google.com/mapstt?' +
				'zoom=' + zoom + '&x=' + X + '&y=' + coord.y + '&client=api';
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
			return 'http://cbk0.google.com/cbk?output=overlay&' +
				'zoom=' + zoom + '&x=' + X + '&y=' + coord.y + '&cb_client=api';
		},
		tileSize: new google.maps.Size(256, 256),
		isPng: true
	});
	//======================================================================================
	//  Add the Street view Image Map
	//--------------------------------------------------------------------------------------
	map.overlayMapTypes.setAt(1, street);
	//==============================================================================================
	

}  // end init

var mapbutt = "<?php echo $_GET['map']; ?>";
var svbutt = "<?php echo $_GET['streetview']; ?>";  	
function toggleStreetView() { 
    var toggle = panorama.getVisible();
    if (toggle == false) {
      	panorama.setVisible(true);
	  	document.myForm.butt1.value=mapbutt;	  	
    } else {
      	panorama.setVisible(false);
      	document.myForm.butt1.value=svbutt;
    }
}

function toggleStreetViewControls() { 
	if (panorama.get('addressControl') == false) {
		panorama.set('navigationControl', true);
		panorama.set('addressControl', true);
		panorama.set('linksControl', true);
		document.myForm.butt0.value='SV controls OFF';
	} else {
		panorama.set('navigationControl', false);
		panorama.set('addressControl', false);
		panorama.set('linksControl', false);
		document.myForm.butt0.value='SV controls ON ';
	}
}



function resetview() {
	initialize();
	document.myForm.butt0.value='SV controls ON ';
}

// Onload handler to fire off the app.
google.maps.event.addDomListener(window, 'load', initialize);

</script>
</head>

<body>

  	<style>
/* Fixed widths don't work well with different DPI settings or some translations
  	#butt0 {
  		width:105px;
  	}
  	#butt1{
  		width:120px;
  	}  	  	
		#butt2 {
  		width:90px;
  	}
*/
		#mapCanvas {
    	width: 520px;
    	height: 350px;
    	margin: 0 auto;
    	margin-top: -10px;
		border:1px solid black;
    	// float: left;
  	}
  	#infoPanel {
  		display: none;
    	margin: 0 auto;
    	margin-top: 5px;
    	//float: left;
    	//margin-left: 10px;
  	}
  	#infoPanel div {
  		display: none;
    	margin-bottom: 5px;
    	background: #ffffff;
  	}
  	div {
  		text-align: center;
  	}
  	</style>
  	
  	<div id="toggle">
  		<form name="myForm" title="myForm">
<!--
			<input type="button" value="street" name="sv_btn" onclick="addStreetViewOverlay()"></input>
-->

  			<?php
  			$map = $_GET['map'];
  			$reset = $_GET['reset'];
  			echo '<input id="butt0" name ="butt0" type="button" value="SV controls ON " onclick="toggleStreetViewControls();"></input>';
  			echo '<input id="butt1" name ="butt1" type="button" value="', $map, '" onclick="toggleStreetView();"></input>';
  			echo '<input id="butt2" name ="butt2" type="button" value="', $reset, '" onclick="resetview();"></input>';
  			?>
  			
  		</form>
  	</div>
  		
  	<div id="mapCanvas">
  	
  	</div>

  	<div id="infoPanel">
    	<!-- <b>Marker status:</b> -->
    	<div id="markerStatus"><em>Click and drag the marker.</em></div>
<!--    	<b>Current position:</b> -->
    	<div id="info" ></div> 
<!--    	<b>Closest matching address:</b> -->
    	<div id="address"></div>
  	</div> 

</body>
</html>
