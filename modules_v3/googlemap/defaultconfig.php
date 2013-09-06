<?php
// Configuration file required by GoogleMap module
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team. All rights reserved.
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

if (!defined('WT_WEBTREES')) {
 header('HTTP/1.0 403 Forbidden');
 exit;
}

// Create GM tables, if not already present
try {
	WT_DB::updateSchema(WT_ROOT.WT_MODULES_DIR.'/googlemap/db_schema/', 'GM_SCHEMA_VERSION', 4);
} catch (PDOException $ex) {
	// The schema update scripts should never fail.  If they do, there is no clean recovery.
	die($ex);
}

global $GOOGLEMAP_MAP_TYPE;
$GOOGLEMAP_MAP_TYPE    = get_module_setting('googlemap', 'GM_MAP_TYPE',    'G_NORMAL_MAP');  // possible values: G_PHYSICAL_MAP, G_NORMAL_MAP, G_SATELLITE_MAP or G_HYBRID_MAP.

global $GOOGLEMAP_MIN_ZOOM;
$GOOGLEMAP_MIN_ZOOM    = get_module_setting('googlemap', 'GM_MIN_ZOOM',    '2');  // min zoom level
global $GOOGLEMAP_MAX_ZOOM;
$GOOGLEMAP_MAX_ZOOM    = get_module_setting('googlemap', 'GM_MAX_ZOOM',    '20'); // max zoom level

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

global $GOOGLEMAP_COORD;
$GOOGLEMAP_COORD       = get_module_setting('googlemap', 'GM_COORD',             '0'); // Enable or disable Display Map Co-ordinates

//Place Hierarchy
global $GOOGLEMAP_PH_XSIZE;
$GOOGLEMAP_PH_XSIZE       =get_module_setting('googlemap', 'GM_PH_XSIZE',         '500'   ); // X-size of Place Hierarchy Google map
global $GOOGLEMAP_PH_YSIZE;
$GOOGLEMAP_PH_YSIZE       =get_module_setting('googlemap', 'GM_PH_YSIZE',         '350'   ); // Y-size of Place Hierarchy Google map
global $GOOGLEMAP_PH_MARKER;
$GOOGLEMAP_PH_MARKER      =get_module_setting('googlemap', 'GM_PH_MARKER',        'G_FLAG'); // Possible values: G_FLAG = Flag, G_DEFAULT_ICON = Standard icon
global $GM_DISP_SHORT_PLACE;
$GM_DISP_SHORT_PLACE      =get_module_setting('googlemap', 'GM_DISP_SHORT_PLACE', '0'); // Display full place name or only the actual level name

// Configuration-options per location-level
global $GM_MARKER_COLOR;
global $GM_MARKER_SIZE;
global $GM_PREFIX;
global $GM_POSTFIX;

$GM_MARKER_COLOR [1]=get_module_setting('googlemap', 'GM_MARKER_COLOR_1',  'Red'  ); // Marker to be used
$GM_MARKER_SIZE  [1]=get_module_setting('googlemap', 'GM_MARKER_SIZE_1',   'Large'); // 'Small' or 'Large'
$GM_PREFIX       [1]=get_module_setting('googlemap', 'GM_PREFIX_1',        ''     ); // Text to be placed in front of the name
$GM_POSTFIX      [1]=get_module_setting('googlemap', 'GM_POSTFIX_1',       ''     ); // Text to be placed after the name

$GM_MARKER_COLOR [2]=get_module_setting('googlemap', 'GM_MARKER_COLOR_2',  'Red'  );
$GM_MARKER_SIZE  [2]=get_module_setting('googlemap', 'GM_MARKER_SIZE_2',   'Large');
$GM_PREFIX       [2]=get_module_setting('googlemap', 'GM_PREFIX_2',        ''     );
$GM_POSTFIX      [2]=get_module_setting('googlemap', 'GM_POSTFIX_2',       ''     );

$GM_MARKER_COLOR [3]=get_module_setting('googlemap', 'GM_MARKER_COLOR_3',  'Red'  );
$GM_MARKER_SIZE  [3]=get_module_setting('googlemap', 'GM_MARKER_SIZE_3',   'Large');
$GM_PREFIX       [3]=get_module_setting('googlemap', 'GM_PREFIX_3',        ''     );
$GM_POSTFIX      [3]=get_module_setting('googlemap', 'GM_POSTFIX_3',       ''     );

$GM_MARKER_COLOR [4]=get_module_setting('googlemap', 'GM_MARKER_COLOR_4',  'Red'  );
$GM_MARKER_SIZE  [4]=get_module_setting('googlemap', 'GM_MARKER_SIZE_4',   'Large');
$GM_PREFIX       [4]=get_module_setting('googlemap', 'GM_PREFIX_4',        ''     );
$GM_POSTFIX      [4]=get_module_setting('googlemap', 'GM_POSTFIX_4',       ''     );

$GM_MARKER_COLOR [5]=get_module_setting('googlemap', 'GM_MARKER_COLOR_5',  'Red'  );
$GM_MARKER_SIZE  [5]=get_module_setting('googlemap', 'GM_MARKER_SIZE_5',   'Large');
$GM_PREFIX       [5]=get_module_setting('googlemap', 'GM_PREFIX_5',        ''     );
$GM_POSTFIX      [5]=get_module_setting('googlemap', 'GM_POSTFIX_5',       ''     );

$GM_MARKER_COLOR [6]=get_module_setting('googlemap', 'GM_MARKER_COLOR_6',  'Red'  );
$GM_MARKER_SIZE  [6]=get_module_setting('googlemap', 'GM_MARKER_SIZE_6',   'Large');
$GM_PREFIX       [6]=get_module_setting('googlemap', 'GM_PREFIX_6',        ''     );
$GM_POSTFIX      [6]=get_module_setting('googlemap', 'GM_POSTFIX_6',       ''     );

$GM_MARKER_COLOR [7]=get_module_setting('googlemap', 'GM_MARKER_COLOR_7',  'Red'  );
$GM_MARKER_SIZE  [7]=get_module_setting('googlemap', 'GM_MARKER_SIZE_7',   'Large');
$GM_PREFIX       [7]=get_module_setting('googlemap', 'GM_PREFIX_7',        ''     );
$GM_POSTFIX      [7]=get_module_setting('googlemap', 'GM_POSTFIX_7',       ''     );

$GM_MARKER_COLOR [8]=get_module_setting('googlemap', 'GM_MARKER_COLOR_8',  'Red'  );
$GM_MARKER_SIZE  [8]=get_module_setting('googlemap', 'GM_MARKER_SIZE_8',   'Large');
$GM_PREFIX       [8]=get_module_setting('googlemap', 'GM_PREFIX_8',        ''     );
$GM_POSTFIX      [8]=get_module_setting('googlemap', 'GM_POSTFIX_8',       ''     );

$GM_MARKER_COLOR [9]=get_module_setting('googlemap', 'GM_MARKER_COLOR_9',  'Red'  );
$GM_MARKER_SIZE  [9]=get_module_setting('googlemap', 'GM_MARKER_SIZE_9',   'Large');
$GM_PREFIX       [9]=get_module_setting('googlemap', 'GM_PREFIX_9',        ''     );
$GM_POSTFIX      [9]=get_module_setting('googlemap', 'GM_POSTFIX_9',       ''     );
