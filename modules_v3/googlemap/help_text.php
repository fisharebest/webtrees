<?php
// Googlemap Module help text.
//
// This file is included from the application help_text.php script.
// It simply needs to set $title and $text for the help topic $help_topic
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

if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {
case 'GOOGLEMAP_MAP_ZOOM':
	$title=WT_I18N::translate('Zoom factor of map');
	$text=WT_I18N::translate('Minimum and maximum zoom factor for the Google map. 1 is the full map, 15 is single house. Note that 15 is only available in certain areas.');
	break;

case 'GOOGLEMAP_PRECISION':
	$title=WT_I18N::translate('Precision of the latitude and longitude');
	$text=WT_I18N::translate('This specifies the precision of the different levels when entering new geographic locations. For example a country will be specified with precision 0 (=0 digits after the decimal point), while a town needs 3 or 4 digits.');
	break;

case 'GM_DEFAULT_LEVEL_0':
	$title=WT_I18N::translate('Default value for top-level');
	$text=WT_I18N::translate('Here the default level for the highest level in the place-hierarchy can be defined. If a place cannot be found this name is added as the highest level (country) and the database is searched again.');
	break;

case 'GM_NAME_PREFIX_SUFFIX':
	$title=WT_I18N::translate('Optional prefixes and suffixes');
	$text=WT_I18N::translate('Some place names may be written with optional prefixes and suffixes.  For example “Orange” versus “Orange County”.  If the family tree contains the full place names, but the geographic database contains the short place names, then you should specify a list of the prefixes and suffixes to be disregarded.  Multiple options should be separated with semicolons.  For example “County;County of” or “Township;Twp;Twp.”.');
	break;

case 'GOOGLEMAP_COORD':
	$title=WT_I18N::translate('Display Map Coordinates');
	$text=WT_I18N::translate('This options sets whether Latitude and Longitude are displayed on the pop-up window attached to map markers.');
	break;

// Help texts for places_edit.php

case 'PLE_PRECISION':
	$title=WT_I18N::translate('Enter precision');
	$text=WT_I18N::translate('Here you can enter the precision. Based on this setting the number of digits that will be used in the latitude and longitude is determined.');
	break;

case 'PLE_ZOOM':
	$title=WT_I18N::translate('Enter zoom level');
	$text=WT_I18N::translate('Here the zoom level can be entered. This value will be used as the minimal value when displaying this geographic location on a map.');
	break;

case 'PLE_ICON':
	$title=WT_I18N::translate('Select an icon');
	$text=WT_I18N::translate('Here an icon can be set or removed. Using this link a flag can be selected. When this geographic location is shown, this flag will be displayed.');
	break;

case 'PLE_FLAGS':
	$title=WT_I18N::translate('Select flag');
	$text=WT_I18N::translate('Using the pull down menu it is possible to select a country, of which a flag can be selected. If no flags are shown, then there are no flags defined for this country.');
	break;

case 'PLIF_FILENAME':
	$title=WT_I18N::translate('Enter filename');
	$text=WT_I18N::translate('Browse for the file on your computer which contains the place locations in CSV format.');
	break;

case 'PLIF_LOCALFILE':
	$title=WT_I18N::translate('Enter filename');
	$text=WT_I18N::translate('Select a file from the list of files already on the server which contains the place locations in CSV format.');
	break;

case 'PLIF_CLEAN':
	$title=WT_I18N::translate('Clear all place-locations before import?');
	$text=WT_I18N::translate('Delete all the geographic data before importing the new data.');
	break;

case 'PLIF_UPDATE':
	$title=WT_I18N::translate('Update existing records');
	$text=WT_I18N::translate('When this option is selected only existing records will be updated. This can be used to fill in latitude and longitude of places that have been imported from a family tree. No new places will be added to the database.');
	break;

case 'PLIF_OVERWRITE':
	$title=WT_I18N::translate('Overwrite location data');
	$text=WT_I18N::translate('Overwrite location data in the database with data from the file.<br />When this option is selected, the location data in the database (latitude, longitude, zoomlevel and flag) are overwritten with the data in the file, if available. If the record is not already in the database a new record will be created, unless the Update-only  option is also selected.');
	break;

case 'PLE_ACTIVE':
	$title=WT_I18N::translate('Show inactive places');
	$text=
		'<p>'.
		WT_I18N::translate('By default, the list shows only those places which can be found in your family trees.  You may have details for other places, such as those imported in bulk from an external file.  Selecting this option will show all places, including ones that are not currently used.').
		'</p><p class="warning">'.
		WT_I18N::translate('If you have a large number of inactive places, it can be slow to generate the list.').
		'</p>';
	break;

// Help text for placecheck.php

case 'GOOGLEMAP_PLACECHECK':
	$title=WT_I18N::translate('Place Check');
	$text=WT_I18N::translate('This tool provides a way to compare places in your gedcom file with the matching entries in the Google Maps™ “placelocations” table.<br /><br /><strong>The display</strong> can be structured for a specific gedcom file; for a specific country within that file; and for a particular area (e.g. state or county) within that country.<br /><br /><strong>Places</strong>are listed alphabetically so that minor spelling differences can be easily spotted, and corrected.<br /><br /><strong>From</strong> the results of the comparison you can click on place names for one of these three options:<br /><br /><strong>1 - </strong>For gedcom file places you will be taken to the Place Heirarchy view. Here you will see all records that are linked to that place.<br /><br /><strong>2 - </strong>For places that exist in the gedcom file, but not in the Google Maps™ table (highlighted in red), you will get the Google Maps™ “Add place” screen.<br /><br /><strong>3 - </strong>For places that exist in both the gedcom file and the Google Maps™ table (perhaps without coordinates) you will get the Google Maps™ “edit place” screen. Here you can edit any aspect of the place record for the Google Maps™ display.<br /><br /><strong>Hovering</strong> over any place in the Google Maps™ table columns will display the zoom level curently set for that place.');
	break;

case 'PLACECHECK_MATCH':
	$title=WT_I18N::translate('Include matched places');
	$text=WT_I18N::translate('By default the list does NOT INCLUDE places that are fully matched between the family tree and the Google Maps™ tables.<br />Fully matched means all levels exist in both the gedcom file and the Google Maps™ tables; and the Google Maps™ places have coordinates for every level.<br /><br />Check this block to include those matched places.');
	break;

// Help text for Place Hierarchy display

case 'GOOGLEMAP_PH_MARKER':
	$title=WT_I18N::translate('Type of place markers in Place Hierarchy');
	$text=WT_I18N::translate('Here you can specify what type of marker be able to use (standard or flag). If place has no flag, use standard marker.');
	break;

case 'GM_DISP_SHORT_PLACE':
	$title=WT_I18N::translate('Display short placenames');
	$text=WT_I18N::translate('Here you can choose between two types of displaying places names in hierarchy. If set Yes the place has short name or actual level name, if No - full name.<br /><b>Examples:<br />Full name: </b>Chicago, Illinois, USA<br /><b>Short name: </b>Chicago<br /><b>Full name: </b>Illinois, USA<br /><b>Short name: </b>Illinois');
	break;

case 'GM_DISP_COUNT':
	$title=WT_I18N::translate('Display indis and families counts');
	$text=WT_I18N::translate('Here you can specify if the counts of indis and families connected to the place is displayed. If the family tree contains many people is recomended to turn it off.');
	break;

case 'GOOGLEMAP_PH_WHEEL':
	$title=WT_I18N::translate('Use mouse wheel for zoom');
	$text=WT_I18N::translate('Here you can specify if the mouse wheel is enebled for zooming.');
	break;

case 'GOOGLEMAP_PH_CONTROLS':
	$title=WT_I18N::translate('Hide map controls');
	$text=WT_I18N::translate('This option allow to hide map controls (i.e. the map type choice) if mouse is outside the map.');
	break;

// Pedigree map

case 'PEDIGREE_MAP_clustersize':
	$title=WT_I18N::translate('Cluster size');
	$text=WT_I18N::translate('The number of markers to be placed at one point before a trail of pins is started in a north east line behind the younger generations.  The “trail” is usually only visable at high zoom values.');
	break;

case 'PEDIGREE_MAP_hidelines':
	$title=WT_I18N::translate('Hide lines');
	$text=WT_I18N::translate('Hide the lines connecting the child to each parent if they exist on the map.');
	break;

case 'PEDIGREE_MAP_hideflags':
	$title=WT_I18N::translate('Hide flags');
	$text=WT_I18N::translate('Hide the flags that are configured in the Google Maps™ module.  Usually these are for countries and states. This serves as a visual cue that the markers around the flag are from the general area, and not the specific spot.');
	break;
}
