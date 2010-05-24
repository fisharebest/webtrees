<?php
/**
 * Configuration file required by GoogleMap module
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
 * @subpackage GoogleMap
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
 header('HTTP/1.0 403 Forbidden');
 exit;
}

// Create GM tables, if not already present
try {
	WT_DB::updateSchema('./modules/googlemap/db_schema/', 'GM_SCHEMA_VERSION', 2);
} catch (PDOException $ex) {
	// The schema update scripts should never fail.  If they do, there is no clean recovery.
	die($ex);
}

// TODO: it will be more efficient to fetch all GM_% settings in a single DB query
global $GOOGLEMAP_ENABLED;
$GOOGLEMAP_ENABLED     = get_module_setting('googlemap', 'GM_ENABLED',     '0'); // Enable or disable Googlemap

global $GOOGLEMAP_API_KEY;
$GOOGLEMAP_API_KEY     = get_module_setting('googlemap', 'GM_API_KEY',     'Fill in your key here. Request key from http://www.google.com/apis/maps/');  // Fill in your key here. Request key from http://www.google.com/apis/maps/

global $GOOGLEMAP_MAP_TYPE;
$GOOGLEMAP_MAP_TYPE    = get_module_setting('googlemap', 'GM_MAP_TYPE',    'G_NORMAL_MAP');  // possible values: G_PHYSICAL_MAP, G_NORMAL_MAP, G_SATELLITE_MAP or G_HYBRID_MAP.

global $GOOGLEMAP_MIN_ZOOM;
$GOOGLEMAP_MIN_ZOOM    = get_module_setting('googlemap', 'GM_MIN_ZOOM',    '2');  // min zoom level
global $GOOGLEMAP_MAX_ZOOM;
$GOOGLEMAP_MAX_ZOOM    = get_module_setting('googlemap', 'GM_MAX_ZOOM',    '13'); // max zoom level

global $GOOGLEMAP_XSIZE;
$GOOGLEMAP_XSIZE       = get_module_setting('googlemap', 'GM_XSIZE',       '600');      // X-size of Google map
global $GOOGLEMAP_YSIZE; 
$GOOGLEMAP_YSIZE       = get_module_setting('googlemap', 'GM_YSIZE',       '400');      // Y-size of Google map

global $GOOGLEMAP_PRECISION_0;
$GOOGLEMAP_PRECISION_0 = get_module_setting('googlemap', 'GM_PRECISION_0', '0');  // Country level
global $GOOGLEMAP_PRECISION_1;
$GOOGLEMAP_PRECISION_1 = get_module_setting('googlemap', 'GM_PRECISION_1', '1');  // State level
global $GOOGLEMAP_PRECISION_2;
$GOOGLEMAP_PRECISION_2 = get_module_setting('googlemap', 'GM_PRECISION_2', '2');  // City level
global $GOOGLEMAP_PRECISION_3;
$GOOGLEMAP_PRECISION_3 = get_module_setting('googlemap', 'GM_PRECISION_3', '3');  // Neighborhood level
global $GOOGLEMAP_PRECISION_4;
$GOOGLEMAP_PRECISION_4 = get_module_setting('googlemap', 'GM_PRECISION_4', '4');  // House level
global $GOOGLEMAP_PRECISION_5;
$GOOGLEMAP_PRECISION_5 = get_module_setting('googlemap', 'GM_PRECISION_5', '9');  // Max prcision level

global $GM_MAX_NOF_LEVELS;
$GM_MAX_NOF_LEVELS     = get_module_setting('googlemap', 'GM_MAX_NOF_LEVELS',    '4'); // Max nr of levels to use in Googlemap
global $GM_DEFAULT_TOP_VALUE;
$GM_DEFAULT_TOP_VALUE  = get_module_setting('googlemap', 'GM_DEFAULT_TOP_VALUE', '' ); // Default value, inserted when no location can be found

global $GOOGLEMAP_COORD;
$GOOGLEMAP_COORD       = get_module_setting('googlemap', 'GM_COORD',             '0'); // Enable or disable Display Map Co-ordinates

//Place Hierarchy
global $GOOGLEMAP_PLACE_HIERARCHY;
$GOOGLEMAP_PLACE_HIERARCHY=get_module_setting('googlemap', 'GM_PLACE_HIERARCHY',  '1'     ); // Enable or disable Display Map in place herarchy
global $GOOGLEMAP_PH_XSIZE; 
$GOOGLEMAP_PH_XSIZE       =get_module_setting('googlemap', 'GM_PH_XSIZE',         '500'   ); // X-size of Place Hierarchy Google map
global $GOOGLEMAP_PH_YSIZE;
$GOOGLEMAP_PH_YSIZE       =get_module_setting('googlemap', 'GM_PH_YSIZE',         '350'   ); // Y-size of Place Hierarchy Google map
global $GOOGLEMAP_PH_MARKER;
$GOOGLEMAP_PH_MARKER      =get_module_setting('googlemap', 'GM_PH_MARKER',        'G_FLAG'); // Possible values: G_FLAG = Flag, G_DEFAULT_ICON = Standard icon
global $GM_DISP_SHORT_PLACE;
$GM_DISP_SHORT_PLACE      =get_module_setting('googlemap', 'GM_DISP_SHORT_PLACE', '0'); // Display full place name or only the actual level name
global $GM_DISP_COUNT;
$GM_DISP_COUNT            =get_module_setting('googlemap', 'GM_DISP_COUNT',       '0'); // Display the count of individuals and families connected to the place
global $GOOGLEMAP_PH_WHEEL;
$GOOGLEMAP_PH_WHEEL       =get_module_setting('googlemap', 'GM_PH_WHEEL',         '0'); // Use mouse wheel for zooming
global $GOOGLEMAP_PH_CONTROLS;
$GOOGLEMAP_PH_CONTROLS    =get_module_setting('googlemap', 'GM_PH_CONTROLS',      '1'); // Hide map controls when mouse is out


// Configuration-options per location-level
global $GM_MARKER_COLOR;
global $GM_MARKER_SIZE;
global $GM_PREFIX;
global $GM_POSTFIX;
global $GM_PRE_POST_MODE;

$GM_MARKER_COLOR [1]=get_module_setting('googlemap', 'GM_MARKER_COLOR_1',  'Red'  ); // Marker to be used
$GM_MARKER_SIZE  [1]=get_module_setting('googlemap', 'GM_MARKER_SIZE_1',   'Large'); // 'Small' or 'Large'
$GM_PREFIX       [1]=get_module_setting('googlemap', 'GM_PREFIX_1',        ''     ); // Text to be placed in front of the name
$GM_POSTFIX      [1]=get_module_setting('googlemap', 'GM_POSTFIX_1',       ''     ); // Text to be placed after the name
$GM_PRE_POST_MODE[1]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_1', '0'    ); // Prefix/Postfix mode. Possible value are:
                                                                      // 0 = no pre/postfix
                                                                      // 1 = Normal name, Prefix, Postfix, Both
                                                                      // 2 = Normal name, Postfix, Prefxi, Both
                                                                      // 3 = Prefix, Postfix, Both, Normal name
                                                                      // 4 = Postfix, Prefix, Both, Normal name
                                                                      // 5 = Prefix, Postfix, Normal name, Both
                                                                      // 6 = Postfix, Prefix, Normal name, Both

$GM_MARKER_COLOR [2]=get_module_setting('googlemap', 'GM_MARKER_COLOR_2',  'Red'  );
$GM_MARKER_SIZE  [2]=get_module_setting('googlemap', 'GM_MARKER_SIZE_2',   'Large');
$GM_PREFIX       [2]=get_module_setting('googlemap', 'GM_PREFIX_2',        ''     );
$GM_POSTFIX      [2]=get_module_setting('googlemap', 'GM_POSTFIX_2',       ''     );
$GM_PRE_POST_MODE[2]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_2', '0'    );

$GM_MARKER_COLOR [3]=get_module_setting('googlemap', 'GM_MARKER_COLOR_3',  'Red'  );
$GM_MARKER_SIZE  [3]=get_module_setting('googlemap', 'GM_MARKER_SIZE_3',   'Large');
$GM_PREFIX       [3]=get_module_setting('googlemap', 'GM_PREFIX_3',        ''     );
$GM_POSTFIX      [3]=get_module_setting('googlemap', 'GM_POSTFIX_3',       ''     );
$GM_PRE_POST_MODE[3]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_3', '0'    );

$GM_MARKER_COLOR [4]=get_module_setting('googlemap', 'GM_MARKER_COLOR_4',  'Red'  );
$GM_MARKER_SIZE  [4]=get_module_setting('googlemap', 'GM_MARKER_SIZE_4',   'Large');
$GM_PREFIX       [4]=get_module_setting('googlemap', 'GM_PREFIX_4',        ''     );
$GM_POSTFIX      [4]=get_module_setting('googlemap', 'GM_POSTFIX_4',       ''     );
$GM_PRE_POST_MODE[4]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_4', '0'    );

$GM_MARKER_COLOR [5]=get_module_setting('googlemap', 'GM_MARKER_COLOR_5',  'Red'  );
$GM_MARKER_SIZE  [5]=get_module_setting('googlemap', 'GM_MARKER_SIZE_5',   'Large');
$GM_PREFIX       [5]=get_module_setting('googlemap', 'GM_PREFIX_5',        ''     );
$GM_POSTFIX      [5]=get_module_setting('googlemap', 'GM_POSTFIX_5',       ''     );
$GM_PRE_POST_MODE[5]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_5', '0'    );

$GM_MARKER_COLOR [6]=get_module_setting('googlemap', 'GM_MARKER_COLOR_6',  'Red'  );
$GM_MARKER_SIZE  [6]=get_module_setting('googlemap', 'GM_MARKER_SIZE_6',   'Large');
$GM_PREFIX       [6]=get_module_setting('googlemap', 'GM_PREFIX_6',        ''     );
$GM_POSTFIX      [6]=get_module_setting('googlemap', 'GM_POSTFIX_6',       ''     );
$GM_PRE_POST_MODE[6]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_6', '0'    );

$GM_MARKER_COLOR [7]=get_module_setting('googlemap', 'GM_MARKER_COLOR_7',  'Red'  );
$GM_MARKER_SIZE  [7]=get_module_setting('googlemap', 'GM_MARKER_SIZE_7',   'Large');
$GM_PREFIX       [7]=get_module_setting('googlemap', 'GM_PREFIX_7',        ''     );
$GM_POSTFIX      [7]=get_module_setting('googlemap', 'GM_POSTFIX_7',       ''     );
$GM_PRE_POST_MODE[7]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_7', '0'    );

$GM_MARKER_COLOR [8]=get_module_setting('googlemap', 'GM_MARKER_COLOR_8',  'Red'  );
$GM_MARKER_SIZE  [8]=get_module_setting('googlemap', 'GM_MARKER_SIZE_8',   'Large');
$GM_PREFIX       [8]=get_module_setting('googlemap', 'GM_PREFIX_8',        ''     );
$GM_POSTFIX      [8]=get_module_setting('googlemap', 'GM_POSTFIX_8',       ''     );
$GM_PRE_POST_MODE[8]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_8', '0'    );

$GM_MARKER_COLOR [9]=get_module_setting('googlemap', 'GM_MARKER_COLOR_9',  'Red'  );
$GM_MARKER_SIZE  [9]=get_module_setting('googlemap', 'GM_MARKER_SIZE_9',   'Large');
$GM_PREFIX       [9]=get_module_setting('googlemap', 'GM_PREFIX_9',        ''     );
$GM_POSTFIX      [9]=get_module_setting('googlemap', 'GM_POSTFIX_9',       ''     );
$GM_PRE_POST_MODE[9]=get_module_setting('googlemap', 'GM_PRE_POST_MODE_9', '0'    );


?>
