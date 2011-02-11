<?php
/**
 * Google map module for phpGedView
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2011 webtrees development team.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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
 * $Id$
 *
 * @author Brian Holland
 */
?>

<script type="text/javascript">var ie = 0;</script>
<!--[if IE]>
<script type="text/javascript">ie = 1;</script>
<![endif]-->
<script type="text/javascript">

//<![CDATA[
    
    // this variable will collect the html which will eventually be placed in the side_bar 
    var side_bar_html = ""; 
	var map_center = new google.maps.LatLng(53.8403,-2.0377);	
    var gmarkers = [];
    var gicons = [];
    var map = null;
   	var head = "";
   	var dir = "";
   	var svzoom = "";

	var infowindow = new google.maps.InfoWindow( { 
    	// size: new google.maps.Size(150,50),
    	// maxWidth: 600
  	});
  	
  	<?php 
	echo 'gicons["red"] = new google.maps.MarkerImage("http://maps.google.com/mapfiles/marker.png",';
    	echo 'new google.maps.Size(20, 34),';
    	echo 'new google.maps.Point(0,0),';
    	echo 'new google.maps.Point(9, 34));';

  	echo 'var iconImage = new google.maps.MarkerImage("http://maps.google.com/mapfiles/marker.png",';
    	echo 'new google.maps.Size(20, 34),';
    	echo 'new google.maps.Point(0,0),';
    	echo 'new google.maps.Point(9, 34));';
    
  	echo 'var iconShadow = new google.maps.MarkerImage("http://www.google.com/mapfiles/shadow50.png",';
      	echo 'new google.maps.Size(37, 34),';
      	echo 'new google.maps.Point(0,0),';
      	echo 'new google.maps.Point(9, 34));';
      	
  	echo 'var iconShape = {';
      	echo 'coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],';
      	echo 'type: "poly"';
  	echo '};';  	
  	?>
  	
	function getMarkerImage(iconColor) {
   		if ((typeof(iconColor)=="undefined") || (iconColor==null)) { 
      		iconColor = "red"; 
   		}
   		if (!gicons[iconColor]) {
      		gicons[iconColor] = new google.maps.MarkerImage("http://maps.google.com/mapfiles/marker"+ iconColor +".png",
      		new google.maps.Size(20, 34),
      		new google.maps.Point(0,0),
      		new google.maps.Point(9, 34));
   		} 
   		return gicons[iconColor];
	}
	
	function category2color(category) {
   		var color = "red";
   		switch(category) {
    	 	case "theatre": color = "";
    	    	break;
    	 	case "golf":    color = "_green";
    	    	break;
    	 	case "info":    color = "_yellow";
    	    	break;
    	 	default:   color = "";
    	    	break;
   		}
   		return color;
	}

    gicons["theatre"] = getMarkerImage(category2color("theatre"));
    gicons["golf"] = getMarkerImage(category2color("golf"));
    gicons["info"] = getMarkerImage(category2color("info"));
    
    var sv2_bear = null;
    var sv2_elev = null;
    var sv2_zoom = null;
	var placer = null; 
	
	// A function to create the marker and set up the event window
	function createMarker(i, latlng, event, html, category, placed, index, tab, address, media, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, sv_point) {
    	var contentString = '<div id="iwcontent">'+html+'<\/div>';
    	
    	// === Decide if marker point is Regular (latlng) or StreetView (sv_point) derived ===
		if (sv_point == "(0, 0)") {
			placer = latlng;
		} else {
			placer = sv_point;
		}
    	
    	// === Define the marker ===
    	var marker = new google.maps.Marker({
        	position: placer,
        	icon: gicons[category],
        	shadow: iconShadow,
        	map: map,
        	title: address,
        	zIndex: Math.round(latlng.lat()*-100000)<<5
        });
        
        // === Store the tab, category and event info as marker properties ===
        marker.myindex = index;
        marker.mytab = tab;
        marker.myplaced = placed;
        marker.mycategory = category;                                 
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
		
		// == Open infowindow when marker is clicked ==
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
        		
			// === Use jquery for tabs ===	
			// var $fred = jQuery("#gmtabs").tabs("div.panes > div");
			// jQuery("#gmtabs").tabs('select', '#SV');
			
			jQuery('#EV').click(function() {
           		document.tabLayerEV = eval('document.getElementById("EV")');
            	document.tabLayerEV.style.background = "#ffffff";
            	document.tabLayerEV.style.paddingBottom = "1px";
        //    	document.tabLayerSV = eval('document.getElementById("SV")');
        //    	document.tabLayerSV.style.background = "#cccccc";
        //    	document.tabLayerSV.style.paddingBottom = "0px";
        //    	document.tabLayerPH = eval('document.getElementById("PH")');
        //    	document.tabLayerPH.style.background = "#cccccc";
        //    	document.tabLayerPH.style.paddingBottom = "0px";
            	document.panelLayer1 = eval('document.getElementById("pane1")');
            	document.panelLayer1.style.display = "block";
        //    	document.panelLayer2 = eval('document.getElementById("pane2")');
        //    	document.panelLayer2.style.display = "none";
        //    	document.panelLayer3 = eval('document.getElementById("pane3")');
        //    	document.panelLayer3.style.display = "none";
            	
          	});	
			
			jQuery('#SV').click(function() { 
            	document.tabLayerEV = eval('document.getElementById("EV")');
            	document.tabLayerEV.style.background = "#cccccc";
            	document.tabLayerEV.style.paddingBottom = "0px";
        //    	document.tabLayerSV = eval('document.getElementById("SV")');
        //    	document.tabLayerSV.style.background = "#ffffff";
        //    	document.tabLayerSV.style.paddingBottom = "1px";
        //    	document.tabLayerPH = eval('document.getElementById("PH")');
        //    	document.tabLayerPH.style.background = "#cccccc";
        //    	document.tabLayerPH.style.paddingBottom = "0px";
            	document.panelLayer1 = eval('document.getElementById("pane1")');
            	document.panelLayer1.style.display = "none";
        //    	document.panelLayer2 = eval('document.getElementById("pane2")');
        //    	document.panelLayer2.style.display = "block";
        //    	document.panelLayer3 = eval('document.getElementById("pane3")');
        //    	document.panelLayer3.style.display = "none";
       			var panorama = new google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
            	// map.setStreetView(panorama);  // **** If you uncomment this, the pegman will appear ****
          	});	
          		
			jQuery('#PH').click(function() {
            	document.tabLayerEV = eval('document.getElementById("EV")');
            	document.tabLayerEV.style.background = "#cccccc";
            	document.tabLayerEV.style.paddingBottom = "0px";
        //    	document.tabLayerSV = eval('document.getElementById("SV")');
        //    	document.tabLayerSV.style.background = "#cccccc";
        //    	document.tabLayerSV.style.paddingBottom = "0px";
        //    	document.tabLayerPH = eval('document.getElementById("PH")');
        //    	document.tabLayerPH.style.background = "#ffffff";
        //    	document.tabLayerPH.style.paddingBottom = "1px";
            	document.panelLayer1 = eval('document.getElementById("pane1")');
            	document.panelLayer1.style.display = "none";
        //    	document.panelLayer2 = eval('document.getElementById("pane2")');
        //    	document.panelLayer2.style.display = "none";
        //    	document.panelLayer3 = eval('document.getElementById("pane3")');
        //    	document.panelLayer3.style.display = "block";
          	});	
          	     	
    	});
    		
	}
	
    // == shows all markers of a particular category, and ensures the checkbox is checked ==
    function show(category) {
        for (var i=0; i<gmarkers.length; i++) {
          	if (gmarkers[i].mycategory == category) {
            	gmarkers[i].setVisible(true);
          	}
        }
        // == close any info window for clarity
        infowindow.close();
    }

    // == hides all markers of a particular category, and ensures the checkbox is cleared ==
    function hide(category) {
        for (var i=0; i<gmarkers.length; i++) {
          	if (gmarkers[i].mycategory == category) {
            	gmarkers[i].setVisible(false);
          	}
        }
        // == close the info window, in case its open on a marker that we just hid
        infowindow.close();
    }

    // == a checkbox has been clicked ==
    function boxclick(box,category) {
        if (box.checked) {
          	show(category);         	
        } else {
          	hide(category);
        }
        // == rebuild the side bar ==
       makeSidebar();
       loadMap();
    }	
    
	// == Opens Marker infowindow when corresponding Sidebar item is clicked ==
    function myclick(i, index, tab) { 
		infowindow.close();
		google.maps.event.trigger(gmarkers[i], "click");
    }
    
    // == rebuild sidebar (hidden item) when any marker's infowindow is closed ==
    google.maps.event.addListener(infowindow, 'closeclick', function() {
       	makeSidebar();
    });
    
    // == rebuilds the sidebar (hidden item) to match the markers currently displayed ==
    function makeSidebar(x) {
        var html = "";
        //var tab = gmarkers.mytab;
        for (var i=0; i<gmarkers.length; i++) {
          	if (gmarkers[i].getVisible()) {
          		// if (x==gmarkers[i].myindex) {
          		if (x==i ) {
            		html += '<a style="text-decoration:none; color:black; background:white; " href="javascript:myclick('+i+', '+gmarkers[i].mytab+')">' + gmarkers[i].myevent + '<\/a><br>';         		
          		} else if (gmarkers[i].mycategory=='theatre') {
            		html += '<a style="text-decoration:none; color:red;" href="javascript:myclick('+i+', '+gmarkers[i].mytab+')">' + gmarkers[i].myevent + '<\/a><br>';
          		} else if (gmarkers[i].mycategory=='golf') {
            		html += '<a style="text-decoration:none; color:green;" href="javascript:myclick('+i+', '+gmarkers[i].mytab+')">' + gmarkers[i].myevent + '<\/a><br>';
          		} else if (gmarkers[i].mycategory=='info') {
            		html += '<a style="text-decoration:none; color:yellow;" href="javascript:myclick('+i+', '+gmarkers[i].mytab+')">' + gmarkers[i].myevent + '<\/a><br>';
            	} else {
            		html += '<a style="text-decoration:none; color:black;" href="javascript:myclick('+i+', '+gmarkers[i].mytab+')">'+ gmarkers[i].myevent + '<\/a><br>';            	
            	}
          	}
        }
        document.getElementById("side_bar").innerHTML = html;
        x=null;
    }
    
	// Home control ----------------------------------------------------------------
	/* returns the user to the original map position ... loadMap() function
	 * This constructor takes the control DIV as an argument.
	 */ 
	function HomeControl(controlDiv, map) {
  		// Set CSS styles for the DIV containing the control
  		// Setting padding to 5 px will offset the control
  		// from the edge of the map
  		controlDiv.style.paddingTop = '5px';
  		controlDiv.style.paddingRight = '0px';

  		// Set CSS for the control border
  		var controlUI = document.createElement('DIV');
  		controlUI.style.backgroundColor = 'white';
  		controlUI.style.borderStyle = 'solid';
  		controlUI.style.borderWidth = '2px';
  		controlUI.style.cursor = 'pointer';
  		controlUI.style.textAlign = 'center';
  		controlUI.title = '<?php echo WT_i18n::translate('Click to reset the map to the original view')?>';
  		controlDiv.appendChild(controlUI);

  		// Set CSS for the control interior
  		var controlText = document.createElement('DIV');
  		controlText.style.fontFamily = 'Arial,sans-serif';
  		controlText.style.fontSize = '12px';
  		controlText.style.paddingLeft = '15px';
  		controlText.style.paddingRight = '15px';
  		controlText.innerHTML = '<b><?php echo WT_i18n::translate('Redraw map')?><\/b>';
  		controlUI.appendChild(controlText);

  		// Setup the click event listeners: simply set the map to original LatLng
  		google.maps.event.addDomListener(controlUI, 'click', function() {    		
    		loadMap();
  		});
	}
	
  	function loadMap() { 
		<?php 	
			global $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $ENABLE_AUTOCOMPLETE, $MULTI_MEDIA, $SHOW_HIGHLIGHT_IMAGES, $WT_IMAGES, $GEDCOM;
		?>
		
  		// Create the map and mapOptions
		var mapOptions = {
			zoom: 7,
			center: map_center,
			mapTypeId: google.maps.MapTypeId.TERRAIN,					// ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU 	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControl: true,
      		navigationControlOptions: {
   				position: google.maps.ControlPosition.TOP_RIGHT,		// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
   				style: google.maps.NavigationControlStyle.SMALL			// ANDROID, DEFAULT, SMALL, ZOOM_PAN
      		},
      		streetViewControl: false,									// Show Pegman or not
      		scrollwheel: false
      		
		};
		map = new google.maps.Map(document.getElementById("map_pane"), mapOptions);

		// Close any infowindow when map is clicked
    	google.maps.event.addListener(map, 'click', function() {
        	infowindow.close();
        	// == rebuild sidebar (hidden item) ==
        	makeSidebar();       	
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
						
				// create thumbnail images of highlighted images ===========================
				if (!empty($pid)) {
					$this_person = WT_Person::getInstance($pid);
				}
				if (!empty($gmark['name'])) {
					$person = WT_Person::getInstance($gmark['name']);
				}
				
				// The current indi -----------------------------
				if (!empty($this_person)) {
					$class = "pedigree_image_portrait";
					if ($gmark['fact'] == 'Census') {
						$image = "<img class='icon_cens' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Birth') {
						$image = "<img class='icon_birt' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Baptism' || $gmark['fact'] == 'Christening') {
						$image = "<img class='icon_bapm' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Marriage') {
						$image = "<img class='icon_marr' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Occupation') {
						$image = "<img class='icon_occu' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Residence') {
						$image = "<img class='icon_resi' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Death') {
						$image = "<img class='icon_deat' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Burial' || $gmark['fact'] == 'Cremation') {
						$image = "<img class='icon_buri' src='././images/pix1.gif' />";
					} else if ($gmark['fact'] == 'Retirement' ) {
						$image = "<img class='icon_reti' src='././images/pix1.gif' />";
					} else {
						$indirec = $this_person->getGedcomRecord();
						$image = "";							
						if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES) {
							if (!empty($pid)) {
								$object = find_highlighted_object($pid, WT_GED_ID, $indirec);
							} else {
								$object = "";
							}
							if (!empty($object["thumb"])) {
								$size = findImageSize($object["thumb"]);
								$class = "pedigree_image_portrait";
								if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
								if ($TEXT_DIRECTION=="rtl") $class .= "_rtl";
								$image = "<img src='{$object["thumb"]}' vspace='0' hspace='0' class='{$class}' alt ='' title='' >";
							} else {
								$class = "pedigree_image_portrait";
								if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
								$sex = $this_person->getSex();
								$image = "<img src=\'./";
								if ($sex == 'F') { $image .= $WT_IMAGES["default_image_F"]; }
								elseif ($sex == 'M') { $image .= $WT_IMAGES["default_image_M"]; }
								else { $image .= $WT_IMAGES["default_image_U"]; }
								$image .="\' align=\'left\' class=\'".$class."\' border=\'none\' alt=\'\' />";
							}
						} // end of add image
					}
				}
				
				// Other people ----------------------------
				if (!empty($person)) {
					$indirec2 = $person->getGedcomRecord();
					$image2 = "";							
					if ($MULTI_MEDIA && $SHOW_HIGHLIGHT_IMAGES) {
						if (!empty($gmark['name'])) {
							$object2 = find_highlighted_object($gmark['name'], WT_GED_ID, $indirec2);
						} else {
							$object2 = "";
						}

						if (!empty($object2["thumb"])) {
							$size = findImageSize($object2["thumb"]);
							$class = "pedigree_image_portrait";
							if ($size[0]>$size[1]) $class = "pedigree_image_landscape";
							if ($TEXT_DIRECTION=="rtl") $class .= "_rtl";
							$image2 = "<img src='{$object2["thumb"]}' vspace='0' hspace='0' class='{$class}' alt ='' title='' >";
						} else {
							$class = "pedigree_image_portrait";
							if ($TEXT_DIRECTION == "rtl") $class .= "_rtl";
							$sex = $person->getSex();
							$image2 = "<img src=\'./";
							if ($sex == 'F') { $image2 .= $WT_IMAGES["default_image_F"]; }
							elseif ($sex == 'M') { $image2 .= $WT_IMAGES["default_image_M"]; }
							else { $image2 .= $WT_IMAGES["default_image_U"]; }
							$image2 .="\' align=\'left\' class=\'".$class."\' border=\'none\' alt=\'\' />";
						}
					} // end of add image
					
				}								
			?>

    			[
    				// Elements 0-9. Basic parameters 
					"<?php echo $gmark['fact'].''; ?>", 
    				"<?php echo $gmark['lati']; ?>", 
    				"<?php echo $gmark['lng']; ?>", 
    				"<?php if (!empty($gmark['date'])) { $date=new WT_Date($gmark['date']); echo addslashes($date->Display(true)); } else { echo i18n::translate('Date not known'); } ?>", 
    				"<?php if (!empty($gmark['info'])) 	{ echo ''.$gmark['info']; } else { echo NULL; } ?>", 
    				"<?php if (!empty($gmark['name'])) { $person=WT_Person::getInstance($gmark['name']); if ($person) { echo '<a href=\"', $person->getHtmlUrl(), '\">', $person->canDisplayName() ? PrintReady(addcslashes($person->getFullName(), '"')) : i18n::translate('Private'), '<\/a>'; } } ?>", 
    				"<?php if (preg_match('/2 PLAC (.*)/', $gmark['placerec']) == 0) { print_address_structure_map($gmark['placerec'], 1); } else { echo preg_replace('/\"/', '\\\"', print_fact_place_map($gmark['placerec'])); } ?>",
					"<?php echo $gmark['index'].''; ?>", 
					"<?php echo $gmark['tabindex'].''; ?>",
					"<?php echo $gmark['placed'].''; ?>",
					
					// Element 10. location marker tooltip - extra printable item for marker title.
					"<?php echo strip_tags(preg_replace('/\"/', '\\\"', print_fact_place_map($gmark['placerec']))); ?>",
					
					// Element 11. persons Name  
					"<?php if (!empty($gmark['name'])) { $person=WT_Person::getInstance($gmark['name']); if ($person) { echo $person->canDisplayName() ? PrintReady(addcslashes($person->getFullName(), '"')) : i18n::translate('Private'); } } ?>",
					
					// Element 12. Other people's Highlighted image.
					"<?php if (!empty($gmark['name'])) { echo $image2; } else { echo ''; } ?>",
					
					// Element 13. This Individual's Highlighted image.
					"<?php if (!empty($pid)) { echo $image; } else { echo ''; } ?>",
					
					// Elements 14-19 Streetview parameters 
					"<?php if (!empty($gmark['media'])) { echo $gmark['media']; } ?>",
					"<?php if (!empty($gmark['sv_lati'])) { echo $gmark['sv_lati']; } ?>",
					"<?php if (!empty($gmark['sv_long'])) { echo $gmark['sv_long']; } ?>",
					"<?php if (!empty($gmark['sv_bearing'])) { echo $gmark['sv_bearing']; } ?>",
					"<?php if (!empty($gmark['sv_elevation'])) { echo $gmark['sv_elevation']; } ?>",
					"<?php if (!empty($gmark['sv_zoom'])) { echo $gmark['sv_zoom']; } ?>"
					// "<?php if (!empty($gmark['sv_point'])) { echo $gmark['sv_point']; } ?>"
    			],

    		<?php } ?> 
    	];    	
    	// Fix IE bug reporting one too many in locations.length statement -----
		if (ie==1) {
			locations.length=locations.length - 1;
		}
		
		// Set the Marker bounds -----------------------------------------------
    	var bounds = new google.maps.LatLngBounds ();
    	
		// Calculate tabs to be placed for each marker -------------------------
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

    	// Loop through all location markers -----------------------------------    	
        for (var i = 0; i < locations.length; i++) {
        	// obtain the attributes of each marker
        	var event = locations[i][0];							// Event or Fact
        	var lat = locations[i][1];								// Latitude
        	var lng = locations[i][2];								// Longitude
        	var date = locations[i][3];								// Date of event or fact
        	var info = locations[i][4];								// info on occupation, or 
       		var name = locations[i][5];								// Persons name
       		var address = locations[i][6];							// Address of event or fact
      		var index = locations[i][7];							// index
       		var tab = locations[i][8];								// tab index
       		var placed = locations[i][9];							// Yes indicates multitab item
       		var name2 = locations[i][11];							// printable name for marker title
       		var point = new google.maps.LatLng(lat,lng);			// Latitude, Longitude
       		       		
       		var media = locations[i][14];							// media item
      		var sv_lati = locations[i][15];							// Street View latitude
       		var sv_long = locations[i][16];							// Street View longitude
       		var sv_bearing = locations[i][17];						// Street View bearing
       		var sv_elevation = locations[i][18];					// Street View elevation
       		var sv_zoom = locations[i][19];							// Street View zoom
       		
			// Employ of image tab function using an information image -----       		
       		if (media == null || media == "") {
       			media = "modules/googlemap/images/facts/v3_image_info.png";
       		} else {       			
       			media = media;
       		}
			// -------------------------------------------------------------
       		
       		var sv_point = new google.maps.LatLng(sv_lati,sv_long); // StreetView Latitude and Longitide       		       		
       		if (document.getElementById("golfbox").checked == false) { 
       			var category = "theatre";							// Category for future pedigree map use etc
       			var addr2 = locations[i][10];						// printable address for marker title
       		} else {
       			var category = "golf";
       			var addr2 = locations[i][10];						// printable address for marker title
       		}

       		// === Use this variable if a multitab marker ===
   			// If a fact with info or a persons name ---  			    		
       		var event_item ="";
       		var event_tab ="";       			
			var tabcontid = "";			
			var divhead = '<h4 id="iwhead" >'+address+'<\/h4>';
			
			for (var n = 0; n < locations.length; n++) {
				//if (i==npo[n][i]) {
       			if (i==npo[n][0] || i==npo[n][1] || i==npo[n][2] || i==npo[n][3] || i==npo[n][4] || i==npo[n][5] || i==npo[n][6] || i==npo[n][7] || i==npo[n][8] || i==npo[n][9] || i==npo[n][10] || i==npo[n][11] || i==npo[n][12] || i==npo[n][13] || i==npo[n][14] || i==npo[n][15] || i==npo[n][16] || i==npo[n][17] || i==npo[n][18] || i==npo[n][19] || i==npo[n][20] || i==npo[n][21] || i==npo[n][22] || i==npo[n][23] || i==npo[n][24] || i==npo[n][25]) {
       				for (var x=0; x<numtabs[n]; x++) { 			
       					tabcontid=npo[n][x];
       					// If a fact with a persons name ---
       					if (locations[tabcontid][5]) {
       						event_tab 	+=	[ '<table><tr><td class="highlt_img">'+locations[tabcontid][12]+'<\/td><td><p><span id="sp1">'+locations[tabcontid][0]+'<\/span><br />'+locations[tabcontid][4]+'<b>'+locations[tabcontid][5]+'<\/b><br />'+locations[tabcontid][3]+'<br /><\/p><\/td><\/tr><\/table>' ]; 
						// or if a fact with extra info ---
   						} else if (locations[tabcontid][4]) {
   							event_tab 	+=	[ '<table><tr><td class="highlt_img">'+locations[tabcontid][13]+'<\/td><td><p><span id="sp1">'+locations[tabcontid][0]+'<\/span><br />'+locations[tabcontid][4]+'<b>'+locations[tabcontid][5]+'<\/b><br />'+locations[tabcontid][3]+'<br /><\/p><\/td><\/tr><\/table>' ]; 
   						// or just a simple fact ---
   						} else {
       						event_tab 	+=	[ '<table><tr><td class="highlt_img">'+locations[tabcontid][13]+'<\/td><td><p><span id="sp1">'+locations[tabcontid][0]+'<\/span><br />'+locations[tabcontid][3]+'<br /><\/p><\/td><\/tr><\/table>' ];    						
   						}
   					}
     			}
     		} 
		
     		var multitabs = [
     		'<div class="infowindow">',
 			 	'<div id = "gmtabs">',
 			 	
          				'<ul class="tabs" >',
         					'<li><a href="#event" id="EV">Events<\/a><\/li>',
          			//		'<li><a href="#sview" id="SV">Street View<\/a><\/li>',
          			//		'<li><a href="#image" id="PH">Image<\/a><\/li>',
          			//		'<li><a href="#" id="SP">Aerial<\/a><\/li>',
       					'<\/ul>',
       					
          				'<div class="panes">',
          					'<div id = "pane1">',
          						divhead,
          						event_tab,
          					'<\/div>',
          		/*	
       						'<div id = "pane2">',
       							divhead,
								'<div id="pano"><\/div>',
          					'<\/div>',
          		*/	
          		/*
          					'<div id = "pane3">',
          						divhead,
          						'<div id = "pane3_text">',
          							'<img style="margin-left: -10px;" src="'+media+'" height= "217px" width= "298px" />',
       							'<\/div>',
       						'<\/div>',
       			*/
       			
				/*	
       						'<div id = "pane4">',
          						divhead,
          						'<div id = "pane4_text">',
          							'<br />',
          							'<br /> Spare Tab Content',
       								'<br />',
       							'<\/div>',
          					'<\/div>',
          		*/	

         				'<\/div>',
         				
  	  			'<\/div>',
  	  		'<\/div>'
   			].join('');
		  	
      		// create the marker -----------------------------------------------
       		var html = multitabs;
       		var marker = createMarker(i, point, event, html, category, placed, index, tab, addr2, media, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, sv_point);
    		var myLatLng = new google.maps.LatLng(locations[i][1], locations[i][2]);
    		bounds.extend(myLatLng);
    		map.fitBounds(bounds);
  		
      	}  // end loop through location markers
      	
    }	// end loadMap()
  	
//]]>
</script>


