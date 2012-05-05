<?php
//
// Script file for pedigree_map.php
//
// Print pedigree map using Googlemaps.
// It requires that your place coordinates are stored on the Google Map
// 'place_locations' table. It will NOT find coordinates stored only as tags in
// your GEDCOM file. As in the Google Maps™ module, it can only display place
// markers where the location exists with identical spelling in both your
// GEDCOM '2 PLAC' tag (within the '1 BIRT' event) and the place_locations table.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team. All rights reserved.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License or,
// at your discretion, any later version.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
global $SHOW_HIGHLIGHT_IMAGES;
?>

<script>
// The HomeControl returns the map to the original position and style ==============
function HomeControl(controlDiv, pm_map) {
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
		pm_map.setMapTypeId(google.maps.MapTypeId.TERRAIN),
		pm_map.fitBounds(bounds),
		pm_map.setCenter(bounds.getCenter()),
		infowindow.close()
		if (document.getElementById(lastlinkid) != null) {
			document.getElementById(lastlinkid).className = 'person_box:target';
		}
	});
}
	
// This function picks up the click and opens the corresponding info window
function myclick(i) {
	if (document.getElementById(lastlinkid) != null) {
		document.getElementById(lastlinkid).className = 'person_box:target';
	}
	google.maps.event.trigger(gmarkers[i], 'click');
}

// this variable will collect the html which will eventually be placed in the side_bar
var side_bar_html = '';

// arrays to hold copies of the markers and html used by the side_bar
// because the function closure trick doesnt work there
var gmarkers = [];
var i = 0;
var lastlinkid;

var infowindow = new google.maps.InfoWindow({ 
	//	
});	

// === Create an associative array of GIcons() =====================================================
var gicons = [];

// -------------------------------------------------------------------------------------------------
gicons['1']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon1.png')
gicons['1'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
// -------------------------------------------------------------------------------------------------
gicons['2']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon2.png')
gicons['2'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['2L']		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon2L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);	
gicons['2L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['2R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon2R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['2R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['2Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon2Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['2Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon2Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------
gicons['3']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon3.png')
gicons['3'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['3L'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon3L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);
gicons['3L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['3R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon3R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['3R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['3Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon3Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['3Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon3Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------
gicons['4']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon4.png')
gicons['4'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['4L'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon4L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);
gicons['4L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['4R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon4R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['4R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['4Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon4Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['4Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon4Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------
gicons['5']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon5.png')
gicons['5'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['5L'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon5L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);
gicons['5L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['5R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon5R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['5R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['5Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon5Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['5Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon5Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------
gicons['6']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon6.png')
gicons['6'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['6L'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon6L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);
gicons['6L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['6R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon6R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['6R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['6Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon6Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['6Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon6Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------
gicons['7']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon7.png')
gicons['7'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['7L'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon7L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);
gicons['7L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['7R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon7R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['7R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['7Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon7Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['7Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon7Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------
gicons['8']			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon8.png')
gicons['8'].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow50.png',
							new google.maps.Size(37, 34),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(10, 34)	// Shadow anchor is base of image
						);
gicons['8L'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon8L.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(28, 28)	// Image anchor
						);
gicons['8L'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-left-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(32, 27)	// Shadow anchor is base of image
						);
gicons['8R'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon8R.png',
							new google.maps.Size(32, 32),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(4, 28)	// Image anchor
						);
gicons['8R'].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/shadow-right-large.png',
							new google.maps.Size(49, 32),	// Shadow size
							new google.maps.Point(0, 0),	// Shadow origin
							new google.maps.Point(15, 27)	// Shadow anchor is base of image
						);
gicons['8Ls'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon8Ls.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(22, 22)	// Image anchor
						);
gicons['8Rs'] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/icon8Rs.png',
							new google.maps.Size(24, 24),	// Image size
							new google.maps.Point(0, 0),	// Image origin
							new google.maps.Point(2, 22)	// Image anchor
						);
// -------------------------------------------------------------------------------------------------

// / A function to create the marker and set up the event window
function createMarker(point, name, html, mhtml, icontype) {
	// alert(i+'. '+name+', '+icontype);
	var contentString = '<div id="iwcontent_edit">'+mhtml+'<\/div>';
	// === create a marker with the requested icon ===
	var marker = new google.maps.Marker({
		icon: 		gicons[icontype],
		shadow: 	gicons[icontype].shadow,
		map: 		pm_map, 
		position: 	point,
		zIndex: 	0
	});		
	var linkid = 'link'+i;	
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.close();
		infowindow.setContent(contentString);
		infowindow.open(pm_map, marker);
		document.getElementById(linkid).className = 'person_box';
		if (document.getElementById(lastlinkid) != null) {
			document.getElementById(lastlinkid).className = 'person_box:target';
		}
		lastlinkid=linkid;
	});

	// save the info we need to use later for the side_bar
	gmarkers[i] = marker;
	// add a line to the side_bar html
	side_bar_html += '<br><div id="'+linkid+'"><a href="#" onclick="myclick(' + i + ')">' + html +'</a><br></div>';
	i++;
	return marker;	
};

// create the map
var myOptions = {
	zoom: 6,
	center: new google.maps.LatLng(0, 0),	
	mapTypeId: google.maps.MapTypeId.TERRAIN,					// ROADMAP, SATELLITE, HYBRID, TERRAIN
	mapTypeControlOptions: {
		style: google.maps.MapTypeControlStyle.DROPDOWN_MENU 	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
	},
	navigationControlOptions: {
		position: google.maps.ControlPosition.TOP_RIGHT,		// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
		style: google.maps.NavigationControlStyle.SMALL			// ANDROID, DEFAULT, SMALL, ZOOM_PAN
	},
	streetViewControl: false,									// Show Pegman or not
	scrollwheel: true	
};
	
var pm_map = new google.maps.Map(document.getElementById('pm_map'), myOptions);

google.maps.event.addListener(pm_map, 'maptypechanged', function() {
	map_type.refresh();
});

google.maps.event.addListener(pm_map, 'click', function() {
	if (document.getElementById(lastlinkid) != null) {
		document.getElementById(lastlinkid).className = 'person_box:target';
	}
infowindow.close();
});

// Create the DIV to hold the control and call HomeControl() passing in this DIV. --
var homeControlDiv = document.createElement('DIV');
var homeControl = new HomeControl(homeControlDiv, pm_map);
homeControlDiv.index = 1;
pm_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
// ---------------------------------------------------------------------------------

// create the map bounds
var bounds = new google.maps.LatLngBounds();

<?php
// add the points
$curgen=1;
$priv=0;
$count=0;
$event = '<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/sq1.png" width="10" height="10">' .
	 '<strong>&nbsp;'.WT_I18N::translate('Root').':&nbsp;</strong>';
$colored_line = array('1'=>'#FF0000','2'=>'#0000FF','3'=>'#00FF00',
				'4'=>'#FFFF00','5'=>'#00FFFF','6'=>'#FF00FF',
				'7'=>'#C0C0FF','8'=>'#808000');

for ($i=0; $i<($controller->treesize); $i++) {
	// moved up to grab the sex of the individuals
	$person = WT_Person::getInstance($controller->treeid[$i]);
	if (!empty($person)) {
		$pid = $controller->treeid[$i];
		$indirec = $person->getGedcomRecord();
		$sex = $person->getSex();
		$bplace = trim($person->getBirthPlace());
		$bdate = $person->getBirthDate();
		$name = $person->getFullName();

		// -- check to see if we have moved to the next generation
		if ($i+1 >= pow(2, $curgen)) {
			$curgen++;
		}
		$relationship=get_relationship_name(get_relationship($controller->root->getXref(), $pid, false, 0, true));
		if (empty($relationship)) $relationship=WT_I18N::translate('self');
		$event = '<img src=\"'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/sq'.$curgen.'.png\" width=\"10\" height=\"10\">'.
			 '<strong>&nbsp;'.$relationship.':&nbsp;</strong>';

		// add thumbnail image
		$image = '';
		if ($SHOW_HIGHLIGHT_IMAGES) {
			$object = find_highlighted_object($pid, WT_GED_ID, $indirec);
			if (!empty($object)) {
				$mediaobject=WT_Media::getInstance($object['mid']);
				$image=$mediaobject->displayMedia(array('display_type'=>'googlemap'));
			} else {
				$sex=$person->getSex();
				$image=display_silhouette(array('sex'=>$sex,'display_type'=>'googlemap')); // may return ''
			}
		}
		// end of add image

		$dataleft  = $image . $event . addslashes($name);
		$datamid   = " <span><a href='".$person->getHtmlUrl()."' id='alturl' title='" . WT_I18N::translate('Individual information') . "'>";
		
		$datamid .= '('.WT_I18N::translate('View Person').')';
		$datamid  .= '</a></span>';
		$dataright = '<br><strong>'. WT_I18N::translate('Birth:') . '&nbsp;</strong>' .
				addslashes($bdate->Display(false)).'<br>'.$bplace;

		$latlongval[$i] = get_lati_long_placelocation($person->getBirthPlace());
		if ($latlongval[$i] != NULL) {
			$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]['lati']);
			$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]['long']);
			if (!($lat[$i] == NULL && $lon[$i] == NULL) && !($lat[$i] == 0 && $lon[$i] == 0)) {
				if ((!$hideflags) && ($latlongval[$i]['icon'] != NULL)) {
					$flags[$i] = $latlongval[$i]['icon'];
					$ffile = strrchr($latlongval[$i]['icon'], '/');
					$ffile = substr($ffile,1, strpos($ffile, '.')-1);
					if (empty($flags[$ffile])) {
						$flags[$ffile] = $i; // Only generate the flag once
						if (($lat[$i] != NULL) && ($lon[$i] != NULL)) {
							echo 'var point = new google.maps.LatLng(' . $lat[$i] . ',' . $lon[$i]. ');';
							echo 'var Marker1_0_flag = new google.maps.MarkerImage();';
							echo 'Marker1_0_flag.image = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $flags[$i], '";';
							echo 'Marker1_0_flag.shadow = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/flag_shadow.png";';
							echo 'Marker1_0_flag.iconSize = new google.maps.Size(25, 15);';
							echo 'Marker1_0_flag.shadowSize = new google.maps.Size(35, 45);';
							echo 'Marker1_0_flag.iconAnchor = new google.maps.Point(1, 45);';
						//BH	echo 'Marker1_0_flag.infoWindowAnchor = new google.maps.Point(5, 1);';
							echo 'var Marker1_0 = new google.maps.LatLng(point, {icon:Marker1_0_flag});';
						//BH 	echo 'pm_map.addOverlay(Marker1_0);';
						}
					}
				}
				$marker_number = $curgen;
				$dups=0;
				for ($k=0; $k<$i; $k++) {
					if ($latlongval[$i] == $latlongval[$k]) {
						if ($clustersize == 1) {
							$lon[$i] = $lon[$i]+0.0025;
							$lat[$i] = $lat[$i]+0.0025;
						} else {
							$dups++;
							switch($dups) {
								case 1: $marker_number = $curgen . 'L'; break;
								case 2: $marker_number = $curgen . 'R'; break;
								case 3: if ($clustersize==5) {
									$marker_number = $curgen . 'Ls'; break;
									}
								case 4: if ($clustersize==5) {
									$marker_number = $curgen . 'Rs'; break;
									}
								case 5: //adjust position where markers have same coodinates
								default: $marker_number = $curgen;
									$lon[$i] = $lon[$i]+0.0025;
									$lat[$i] = $lat[$i]+0.0025;
									break;
							}
						}
					}
				}
				echo 'var point = new google.maps.LatLng(', $lat[$i], ',', $lon[$i], ');';
				echo "var marker = createMarker(point, \"", addslashes($name), "\",\n\t\"<div>",$dataleft,$datamid,$dataright,"</div>\", \"";
				echo "<div class='iwstyle'>";
				echo "<a href='module.php?ged=", WT_GEDURL, "&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid={$pid}&amp;PEDIGREE_GENERATIONS={$PEDIGREE_GENERATIONS}";
				if ($hideflags) echo '&amp;hideflags=1';
				if ($hidelines) echo '&amp;hidelines=1';
				if ($clustersize != 5) echo '&amp;clustersize=', $clustersize; // ignoring the default of 5
				echo "' title='", WT_I18N::translate('Pedigree map'), "'>", $dataleft, "</a>", $datamid, $dataright, "</div>\", \"", $marker_number, "\");\n";
				//BH echo "pm_map.addOverlay(marker);\n";

				// Construct the polygon lines ---------------------------------
				if (!$hidelines) {
					$to_child = (intval(($i-1)/2)); // Draw a line from parent to child
					if (isset($lat[$to_child]) && isset($lon[$to_child])) {
					?>					
					var linecolor;
					var plines;			
					var lines = [
						<?php
						echo 'new google.maps.LatLng(', $lat[$i], ',', $lon[$i], '), ';
						echo 'new google.maps.LatLng(', $lat[$to_child], ',', $lon[$to_child], ')';
						?>
					];
					linecolor = <?php echo "'$colored_line[$curgen]'"; ?>;
					plines = new google.maps.Polygon({
						paths: lines,
						strokeColor: linecolor,
						// strokeColor: <?php echo $colored_line[$curgen]; ?>,
						strokeOpacity: 0.8,
						strokeWeight: 3,
						fillColor: '#FF0000',
						//fillOpacity: 0.35
						fillOpacity: 0.1
					}); 
					plines.setMap(pm_map);		
				<?php
					}
				}
				// Extend and fit marker bounds---------------------------------
				echo 'bounds.extend(point);';
				echo 'pm_map.fitBounds(bounds);';
				echo "\n";
				$count++;
			}
		}
	} else {
		$latlongval[$i] = NULL;
	}
}
?>

pm_map.setCenter(bounds.getCenter());

// Close the sidebar highlight when the infowindow is closed ------
google.maps.event.addListener(infowindow, 'closeclick', function() {
	document.getElementById(lastlinkid).className = 'person_box:target';
});

// put the assembled side_bar_html contents into the side_bar div
document.getElementById('side_bar').innerHTML = side_bar_html;

// === create the context menu div ===
var contextmenu = document.createElement('div');
	contextmenu.style.visibility='hidden';
	contextmenu.innerHTML = '<a href="#" onclick="zoomIn()"><div class="optionbox">&nbsp;&nbsp;<?php echo WT_I18N::translate('Zoom in'); ?>&nbsp;&nbsp;</div></a>'
						  + '<a href="#" onclick="zoomOut()"><div class="optionbox">&nbsp;&nbsp;<?php echo WT_I18N::translate('Zoom out'); ?>&nbsp;&nbsp;</div></a>'
						  + '<a href="#" onclick="zoomInHere()"><div class="optionbox">&nbsp;&nbsp;<?php echo WT_I18N::translate('Zoom in here'); ?>&nbsp;&nbsp;</div></a>'
						  + '<a href="#" onclick="zoomOutHere()"><div class="optionbox">&nbsp;&nbsp;<?php echo WT_I18N::translate('Zoom out here'); ?>&nbsp;&nbsp;</div></a>'
						  + '<a href="#" onclick="centreMapHere()"><div class="optionbox">&nbsp;&nbsp;<?php echo WT_I18N::translate('Center map here'); ?>&nbsp;&nbsp;</div></a>';
//BH pm_map.getContainer().appendChild(contextmenu);

// === listen for singlerightclick ===
google.maps.event.addListener(pm_map,'singlerightclick',function(pixel,tile) {
	// store the 'pixel' info in case we need it later
	// adjust the context menu location if near an egde
	// create a GControlPosition
	// apply it to the context menu, and make the context menu visible
	clickedPixel = pixel;
	var x=pixel.x;
	var y=pixel.y;
	if (x > pm_map.getSize().width - 120) { x = pm_map.getSize().width - 120 }
	if (y > pm_map.getSize().height - 100) { y = pm_map.getSize().height - 100 }
	var pos = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(x,y));
	pos.apply(contextmenu);
	contextmenu.style.visibility = 'visible';
});

// === functions that perform the context menu options ===
function zoomIn() {
	// perform the requested operation
	pm_map.zoomIn();
	// hide the context menu now that it has been used
	contextmenu.style.visibility='hidden';
}
function zoomOut() {
	// perform the requested operation
	pm_map.zoomOut();
	// hide the context menu now that it has been used
	contextmenu.style.visibility='hidden';
}
function zoomInHere() {
	// perform the requested operation
	var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
	pm_map.zoomIn(point,true);
	// hide the context menu now that it has been used
	contextmenu.style.visibility='hidden';
}
function zoomOutHere() {
	// perform the requested operation
	var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
	pm_map.setCenter(point,pm_map.getZoom()-1); // There is no pm_map.zoomOut() equivalent
	// hide the context menu now that it has been used
	contextmenu.style.visibility='hidden';
}
function centreMapHere() {
	// perform the requested operation
	var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
	pm_map.setCenter(point);
	// hide the context menu now that it has been used
	contextmenu.style.visibility='hidden';
}

// === If the user clicks on the map, close the context menu ===
google.maps.event.addListener(pm_map, 'click', function() {
	contextmenu.style.visibility='hidden';
});

</script>
