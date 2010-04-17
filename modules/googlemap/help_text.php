<?php
/**
 * Googlemap Module help text.
 *
 * This file is included from the application help_text.php script.
 * It simply needs to set $title and $text for the help topic $help_topic
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 * @version $Id$
 */
 
if (!defined('WT_WEBTREES') || !defined('WT_SCRIPT_NAME') || WT_SCRIPT_NAME!='help_text.php') {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

switch ($help) {

case 'GOOGLEMAP_CONFIG':
	$title=i18n::translate('Configure Google-map');
	$text=i18n::translate('Configure all aspects of the Google Map module here.');
	break;

case 'GOOGLEMAP_ENABLE':
	$title=i18n::translate('Enable Google-map');
	$text=i18n::translate('Using this option the functionality of the Googlemap can be enabled or disabled.<br />When disabled the Map-tab on the individual page is still shown, but will be left empty. The configuration link for administrators is still available. When disabled the Place Hierarchy has standard show.');
	break;

case 'GOOGLEMAP_API_KEY':
	$title=i18n::translate('Google-map API key');
	$text=i18n::translate('Insert your Google Map API key here.  You can request a key here: <a target=\"_blank\" href=\"http://www.google.com/apis/maps/\">http://www.google.com/apis/maps/</a>');
	break;

case 'GOOGLEMAP_MAP_TYPE':
	$title=i18n::translate('Google-map type');
	$text=i18n::translate('The type of map that will be shown by default. This can be Map, Satellite, Hybrid or Terrain.');
	break;

case 'GOOGLEMAP_MAP_SIZE':
	$title=i18n::translate('Google-map size');
	$text=i18n::translate('The size of the map (in pixels) as shown on the Individual pages.');
	break;

case 'GOOGLEMAP_MAP_ZOOM':
	$title=i18n::translate('Google-map zoom factor');
	$text=i18n::translate('Minimum and maximum zoom factor for the Google map. 1 is the full map, 15 is single house. Note that 15 is only available in certain areas.');
	break;

case 'GOOGLEMAP_PRECISION':
	$title=i18n::translate('Precision of the latitude and longitude');
	$text=i18n::translate('This specifies the precision of the different levels when entering new geographic locations. For example a country will be specified with precision 0 (=0 digits after the decimal point), while a town needs 3 or 4 digits.');
	break;

case 'GM_DEFAULT_LEVEL_0':
	$title=i18n::translate('Default value for top-level');
	$text=i18n::translate('Here the default level for the highest level in the place-hierarchy can be defined. If a place cannot be found this name is added as the highest level (country) and the database is searched again.');
	break;

case 'GM_NOF_LEVELS':
	$title=i18n::translate('This indicates the number of levels used within Googlemap');
	$text=i18n::translate('This field indicates the number of levels in the places-hierarchy that is being used by the Googlemap modules.<br />The default value is 4 (Country, State, County, Place), which is usually good enough. If you want to add an extra level (for example to add specific location like cemeteries or schools) change this value. If you want to remove a level (for example county) you can also change this value, but keep in mind that the files containing the place-locations contain a 4-level structure.');
	break;

case 'GM_NAME_PREFIX':
	$title=i18n::translate('Prefix for names used on this level');
	$text=i18n::translate('This value will be added to the front of the names on this level. Multiple values can be used, seperated by semicolons.');
	break;

case 'GM_NAME_POSTFIX':
	$title=i18n::translate('Postfix for names used on this level');
	$text=i18n::translate('This value will be added to the back of the names on this level. Multiple values can be used, seperated by semilcolons.');
	break;

case 'GM_NAME_PRE_POST':
	$title=i18n::translate('Order of the pre/postfix to use');
	$text=i18n::translate('This field indicates the order in which names are tried using the prefix and postfix. The possible values are:<br /><ul><li>No pre/postfix</li><li>Normal name, Prefix, Postfix, both</li><li>Normal name, Postfix, Prefix, both</li><li>Prefix, Postfix, both, Normal name</li><li>Postfix, Prefix, both, Normal name</li><li>Prefix, Postfix, Normal name, both</li><li>Postfix, Prefix, Normal name, both</li></ul>');
	break;

case 'PL_EDIT_LOCATION':
	$title=i18n::translate('Edit or delete location');
	$text=i18n::translate('Here you can edit the location or delete the location. When you click on Edit a new window will open where you can change the values of the geographic location.<br />If you click on the delete-icon the record will be deleted. This can only be done if there are no records connected to this location. If no records are connected the delete-icon is active, otherwise it is inactive.');
	break;

case 'PL_ADD_LOCATION':
	$title=i18n::translate('Add geographic location');
	$text=i18n::translate('Use this to add a place to the location table. The location will be added at this level.');
	break;

case 'PL_IMPORT_GEDCOM':
	$title=i18n::translate('Import geographic locations from GEDCOM');
	$text=i18n::translate('Import geographic location-data from current GEDCOM. The current GEDCOM will be scanned and all places will be added to the table. If latitude and longitude are available these will also be imported.');
	break;

case 'PL_IMPORT_ALL_GEDCOM':
	$title=i18n::translate('Import geographic locations from all GEDCOMs');
	$text=i18n::translate('Import geographic location-data from all GEDCOMs. All GEDCOMs will be scanned and all places will be added to the table. If latitude and longitude are available these will also be imported.');
	break;

case 'PL_IMPORT_FILE':
	$title=i18n::translate('Import geographic locations from file');
	$text=i18n::translate('Import geographic location data from a file. The file should be formatted as CSV file on the local computer. The record separator used within the lines is \';\'.');
	break;

case 'PL_EXPORT_FILE':
	$title=i18n::translate('Export locations to file');
	$text=i18n::translate('Export location data to a file. This option will save the data from the current view and all dependant data to a file. This means that if a country is selected and the states are shown, this option will save the data of the states, all the counties that are defined in those states and all places within those counties.');
	break;

case 'PL_EXPORT_ALL_FILE':
	$title=i18n::translate('Export all locations to file');
	$text=i18n::translate('Export all location data to a file. This option will save all location data and transfer it to the local computer.');
	break;

case 'GOOGLEMAP_COORD':
	$title=i18n::translate('Display Map Coordinates');
	$text=i18n::translate('This options sets whether Latitude and Longitude are displayed on the pop-up window attached to map markers.');
	break;

// Help texts for places_edit.php

case 'PLE_EDIT':
	$title=i18n::translate('Edit Google Map Places');
	$text=i18n::translate('Here you can add, edit or delete Google Map place details.');
	break;

case 'PLE_PLACES':
	$title=i18n::translate('Enter place name');
	$text=i18n::translate('Here you can enter or change the name of the place.<br />The \'Search on this level\' option allow to search the latitude and longitude of entered place name only among the places with that level.<br />The \'Search on this level\' option allow to search the latitude and longitude of all places having entered name. Some the lower levels places can not be displayed with this method of search.');
	break;

case 'PLE_PRECISION':
	$title=i18n::translate('Enter precision');
	$text=i18n::translate('Here you can enter the precision. Based on this setting the number of digits that will be used in the latitude and longitude is determined.');
	break;

case 'PLE_LATLON_CTRL':
	$title=i18n::translate('Enter latitude or Longitude');
	$text=i18n::translate('Here the latitude and longitude can be entered. First select the area you want to set (E/W or N/S). Next enter the value for latitude or longitude. This should be a decimal value.<br />The decimal value can be determined by converting the minutes and seconds using the following formula:<br />degrees_decimal = ((seconds / 60) + minutes) / 60 + degrees.');
	break;

case 'PLE_ZOOM':
	$title=i18n::translate('Enter zoom level');
	$text=i18n::translate('Here the zoom level can be entered. This value will be used as the minimal value when displaying this geographic location on a map.');
	break;

case 'PLE_ICON':
	$title=i18n::translate('Select an icon');
	$text=i18n::translate('Here an icon can be set or removed. Using this link a flag can be selected. When this geographic location is shown, this flag will be displayed.');
	break;

case 'PLE_FLAGS':
	$title=i18n::translate('Select flag');
	$text=i18n::translate('Using the pull down menu it is possible to select a country, of which a flag can be selected. If no flags are shown, then there are no flags defined for this country.');
	break;

case 'PLIF_FILENAME':
	$title=i18n::translate('Enter filename');
	$text=i18n::translate('Browse for the file on your computer which contains the place locations in CSV format.');
	break;

case 'PLIF_LOCALFILE':
	$title=i18n::translate('Enter filename');
	$text=i18n::translate('Select a file from the list of files already on the server which contains the place locations in CSV format.');
	break;

case 'PLIF_CLEAN':
	$title=i18n::translate('Clean placelocation database');
	$text=i18n::translate('When this option is selected the placelocation database will be cleared. This means that only the location stored in this table will be deleted. This will not change anything in the GEDCOM.');
	break;

case 'PLIF_UPDATE':
	$title=i18n::translate('Update existing records');
	$text=i18n::translate('Only update existing records.<br />When this option is selected only existing records will be updated. This can be used to fill in latitude and longitude of places that have been imported from a GEDCOM. No new places will be added to the database.');
	break;

case 'PLIF_OVERWRITE':
	$title=i18n::translate('Overwrite location data');
	$text=i18n::translate('Overwrite location data in the database with data from the file.<br />When this option is selected, the location data in the database (latitude, longitude, zoomlevel and flag) are overwritten with the data in the file, if available. If the record is not already in the database a new record will be created, unless the Update-only  option is also selected.');
	break;

case 'PLE_ACTIVE':
	$title=i18n::translate('List inactive places');
	$text=i18n::translate('<strong>List places in the GoogleMaps table that are not used by any current GEDCOM(s).</strong><br /><br />The display is set, by default, to only display for editing here those places that exist on BOTH your GEDCOM files and your GoogleMap tables.<br /><br />When this option is checked, and \"View\" clicked, the list of places will display ALL places at this level.<br /><br />This is designed to speed up the display of the list when large place lists have been imported, but not all used.<br /><br />NOTE - if the option is checked the full list may take a few minutes to display.');
	break;

// Help text for placecheck.php

case 'GOOGLEMAP_PLACECHECK':
	$title=i18n::translate('Place Checking Tool');
	$text=i18n::translate('<strong>This tool</strong> provides a way to compare places in your gedcom file with the matching entries in the googlemaps \'placelocations\' table.<br /><br /><strong>The display</strong> can be structured for a specific gedcom file; for a specific country within that file; and for a particular area (e.g. state or county) within that country.<br /><br /><strong>Places</strong>are listed alphabetically so that minor spelling differences can be easily spotted, and corrected.<br /><br /><strong>From</strong> the results of the comparison you can click on place names for one of these three options:<br /><br /><strong>1 - </strong>For gedcom file places you will be taken to the Place Heirarchy view. Here you will see all records that are linked to that place.<br /><br /><strong>2 - </strong>For places that exist in the gedcom file, but not in the googlemap table (highlighted in red), you will get the googlemap \"Add place\" screen.<br /><br /><strong>3 - </strong>For places that exist in both the gedcom file and the googlemap table (perhaps without coordinates) you will get the googlemap \"edit place\" screen. Here you can edit any aspect of the place record for the googlemap display.<br /><br /><strong>Hovering</strong> over any place in the googlemap table columns will display the zoom level curently set for that place.');
	break;

case 'PLACECHECK_FILTER':
	$title=i18n::translate('Place Check - List Filtering Optons');
	$text=i18n::translate('This section includes options to limit or extend the scope of the listed places.<br /><br />It is hoped to add more options in the future.');
	break;

case 'PLACECHECK_MATCH':
	$title=i18n::translate('Include matched places');
	$text=i18n::translate('By default the list does NOT INCLUDE places that are fully matched between the GEDCOM file and the GoogleMap tables.<br />Fully matched means all levels exist in both the gedcom file and the GoogleMap tables; and the GoogleMap places have coordinates for every level.<br /><br />Check this block to include those matched places.');
	break;

//wooc Options for Place Hierarchy display

case 'GOOGLEMAP_PH':
	$title=i18n::translate('Use Googlemap for Place Hierarchy');
	$text=i18n::translate('Use this option to enable (Yes) or disable (No) the ability to substitute Googlemap for <strong>webtrees</strong> usual Place Hierarchy. To be able to set this option to YES, the Googlemap module must be also be enabled. CAUTION: Before using this option, it is recommended that you insert all places currently existing in your GED into the Googlemap tables.');
	break;

case 'GOOGLEMAP_PH_MAP_SIZE':
	$title=i18n::translate('Size of Place Hierarchy map (in pixels)');
	$text=i18n::translate('The size of the map (in pixels) as shown on the Place Hierarchy pages.');
	break;

case 'GOOGLEMAP_PH_MARKER':
	$title=i18n::translate('Type of place markers in Place Hierarchy');
	$text=i18n::translate('Here you can specify what type of marker be able to use (standard or flag). If place has no flag, use standard marker.');
	break;

case 'GM_DISP_SHORT_PLACE':
	$title=i18n::translate('Display short placenames');
	$text=i18n::translate('Here you can choose between two types of displaying places names in hierarchy. If set Yes the place has short name or actual level name, if No - full name.<br /><b>Examples:<br />Full name: </b>Chicago, Illinois, USA<br /><b>Short name: </b>Chicago<br /><b>Full name: </b>Illinois, USA<br /><b>Short name: </b>Illinois');
	break;

case 'GM_DISP_COUNT':
	$title=i18n::translate('Display indis and families counts');
	$text=i18n::translate('Here you can specify if the counts of indis and families connected to the place is displayed. Now if GEDCOM file contains many people is recomended to turn it off.');
	break;

case 'GOOGLEMAP_PH_WHEEL':
	$title=i18n::translate('Use mouse wheel for zoom');
	$text=i18n::translate('Here you can specify if the mouse wheel is enebled for zooming.');
	break;

case 'GOOGLEMAP_PH_CONTROLS':
	$title=i18n::translate('Hide map controls');
	$text=i18n::translate('This option allow to hide map controls (i.e. the map type choice) if mouse is outside the map.');
	break;

// Pedigree map

case 'PEDIGREE_MAP_clustersize':
	$title=i18n::translate('Cluster Size');
	$text=i18n::translate('The number of markers to be placed at one point before a trail of pins is started in a north east line behind the younger generations.  The \'trail\' is usually only visable at high zoom values.');
	break;

case 'PEDIGREE_MAP_hidelines':
	$title=i18n::translate('Hide lines');
	$text=i18n::translate('Hide the lines connecting the child to each parent if they exist on the map.');
	break;

case 'PEDIGREE_MAP_hideflags':
	$title=i18n::translate('Hide flags');
	$text=i18n::translate('Hide the flags that are configured in the googlemap module.  Ususally these are for countries and states. This serves as a visual cue that the markers around the flag are from the general area, and not the specific spot.');
	break;
}
