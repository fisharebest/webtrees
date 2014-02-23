<?php
// Configuration file required by GoogleMap module
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// Create GM tables, if not already present
try {
	WT_DB::updateSchema(WT_ROOT.WT_MODULES_DIR.'/googlemap/db_schema/', 'GM_SCHEMA_VERSION', 5);
} catch (PDOException $ex) {
	// The schema update scripts should never fail.  If they do, there is no clean recovery.
	die($ex);
}

// Load all module settings at once using one database request
$settings = loadModuleSettings(
	'googlemap',
	// Name of module setting to load => default value
	array(
		'GM_MAP_TYPE'         => 'G_NORMAL_MAP', // G_PHYSICAL_MAP, G_NORMAL_MAP, G_SATELLITE_MAP or G_HYBRID_MAP.
		'GM_MIN_ZOOM'         => '2',            // min zoom level
		'GM_MAX_ZOOM'         => '20',           // max zoom level
		'GM_XSIZE'            => '600',          // X-size of Google map
		'GM_YSIZE'            => '400',          // Y-size of Google map
		'GM_PRECISION_0'      => '0',            // Country level
		'GM_PRECISION_1'      => '1',            // State level
		'GM_PRECISION_2'      => '2',            // City level
		'GM_PRECISION_3'      => '3',            // Neighborhood level
		'GM_PRECISION_4'      => '4',            // House level
		'GM_PRECISION_5'      => '9',            // Max prcision level
		'GM_COORD'            => '0',            // Enable or disable Display Map Co-ordinates

		// Place hierarchy
		'GM_PH_XSIZE'         => '500',          // X-size of Place Hierarchy Google map
		'GM_PH_YSIZE'         => '350',          // Y-size of Place Hierarchy Google map
		'GM_PH_MARKER'        => 'G_FLAG',       // Possible values: G_FLAG = Flag, G_DEFAULT_ICON = Standard icon
		'GM_DISP_SHORT_PLACE' => '0',            // Display full place name or only the actual level name

		'GM_USE_STREETVIEW'   => '0',            // Use street map
		'GM_PLACE_HIERARCHY'  => '0',

		// Configuration-options per location-level
		'GM_MARKER_COLOR_1'   => 'Red',          // Marker to be used
		'GM_MARKER_SIZE_1'    => 'Large',        // 'Small' or 'Large'
		'GM_PREFIX_1'         => '',             // Text to be placed in front of the name
		'GM_POSTFIX_1'        => '',             // Text to be placed after the name

		'GM_MARKER_COLOR_2'   => 'Red',
		'GM_MARKER_SIZE_2'    => 'Large',
		'GM_PREFIX_2'         => '',
		'GM_POSTFIX_2'        => '',

		'GM_MARKER_COLOR_3'   => 'Red',
		'GM_MARKER_SIZE_3'    => 'Large',
		'GM_PREFIX_3'         => '',
		'GM_POSTFIX_3'        => '',

		'GM_MARKER_COLOR_4'   => 'Red',
		'GM_MARKER_SIZE_4'    => 'Large',
		'GM_PREFIX_4'         => '',
		'GM_POSTFIX_4'        => '',

		'GM_MARKER_COLOR_5'   => 'Red',
		'GM_MARKER_SIZE_5'    => 'Large',
		'GM_PREFIX_5'         => '',
		'GM_POSTFIX_5'        => '',

		'GM_MARKER_COLOR_6'   => 'Red',
		'GM_MARKER_SIZE_6'    => 'Large',
		'GM_PREFIX_6'         => '',
		'GM_POSTFIX_6'        => '',

		'GM_MARKER_COLOR_7'   => 'Red',
		'GM_MARKER_SIZE_7'    => 'Large',
		'GM_PREFIX_7'         => '',
		'GM_POSTFIX_7'        => '',

		'GM_MARKER_COLOR_8'   => 'Red',
		'GM_MARKER_SIZE_8'    => 'Large',
		'GM_PREFIX_8'         => '',
		'GM_POSTFIX_8'        => '',

		'GM_MARKER_COLOR_9'   => 'Red',
		'GM_MARKER_SIZE_9'    => 'Large',
		'GM_PREFIX_9'         => '',
		'GM_POSTFIX_9'        => '',
	)
);

// Map loaded settings to global variable names
$globalVars = array(
	'GOOGLEMAP_MAP_TYPE'    => $settings['GM_MAP_TYPE'],
	'GOOGLEMAP_MIN_ZOOM'    => $settings['GM_MIN_ZOOM'],
	'GOOGLEMAP_MAX_ZOOM'    => $settings['GM_MAX_ZOOM'],
	'GOOGLEMAP_XSIZE'       => $settings['GM_XSIZE'],
	'GOOGLEMAP_YSIZE'       => $settings['GM_YSIZE'],
	'GOOGLEMAP_PRECISION_0' => $settings['GM_PRECISION_0'],
	'GOOGLEMAP_PRECISION_1' => $settings['GM_PRECISION_1'],
	'GOOGLEMAP_PRECISION_2' => $settings['GM_PRECISION_2'],
	'GOOGLEMAP_PRECISION_3' => $settings['GM_PRECISION_3'],
	'GOOGLEMAP_PRECISION_4' => $settings['GM_PRECISION_4'],
	'GOOGLEMAP_PRECISION_5' => $settings['GM_PRECISION_5'],
	'GOOGLEMAP_COORD'       => $settings['GM_COORD'],
	'GOOGLEMAP_PH_XSIZE'    => $settings['GM_PH_XSIZE'],
	'GOOGLEMAP_PH_YSIZE'    => $settings['GM_PH_YSIZE'],
	'GOOGLEMAP_PH_MARKER'   => $settings['GM_PH_MARKER'],
	'GM_DISP_SHORT_PLACE'   => $settings['GM_DISP_SHORT_PLACE'],
	'GM_USE_STREETVIEW'     => $settings['GM_USE_STREETVIEW'],
	'GM_PLACE_HIERARCHY'    => $settings['GM_PLACE_HIERARCHY'],

	'GM_MARKER_COLOR' => array(
		1 => $settings['GM_MARKER_COLOR_1'],
		2 => $settings['GM_MARKER_COLOR_2'],
		3 => $settings['GM_MARKER_COLOR_3'],
		4 => $settings['GM_MARKER_COLOR_4'],
		5 => $settings['GM_MARKER_COLOR_5'],
		6 => $settings['GM_MARKER_COLOR_6'],
		7 => $settings['GM_MARKER_COLOR_7'],
		8 => $settings['GM_MARKER_COLOR_8'],
		9 => $settings['GM_MARKER_COLOR_9'],
	),

	'GM_MARKER_SIZE' => array(
		1 => $settings['GM_MARKER_SIZE_1'],
		2 => $settings['GM_MARKER_SIZE_2'],
		3 => $settings['GM_MARKER_SIZE_3'],
		4 => $settings['GM_MARKER_SIZE_4'],
		5 => $settings['GM_MARKER_SIZE_5'],
		6 => $settings['GM_MARKER_SIZE_6'],
		7 => $settings['GM_MARKER_SIZE_7'],
		8 => $settings['GM_MARKER_SIZE_8'],
		9 => $settings['GM_MARKER_SIZE_9'],
	),

	'GM_PREFIX' => array(
		1 => $settings['GM_PREFIX_1'],
		2 => $settings['GM_PREFIX_2'],
		3 => $settings['GM_PREFIX_3'],
		4 => $settings['GM_PREFIX_4'],
		5 => $settings['GM_PREFIX_5'],
		6 => $settings['GM_PREFIX_6'],
		7 => $settings['GM_PREFIX_7'],
		8 => $settings['GM_PREFIX_8'],
		9 => $settings['GM_PREFIX_9'],
	),

	'GM_POSTFIX' => array(
		1 => $settings['GM_POSTFIX_1'],
		2 => $settings['GM_POSTFIX_2'],
		3 => $settings['GM_POSTFIX_3'],
		4 => $settings['GM_POSTFIX_4'],
		5 => $settings['GM_POSTFIX_5'],
		6 => $settings['GM_POSTFIX_6'],
		7 => $settings['GM_POSTFIX_7'],
		8 => $settings['GM_POSTFIX_8'],
		9 => $settings['GM_POSTFIX_9'],
	),
);

// Create global vars out of array
foreach ($globalVars as $globalVarName => $globalVarValue) {
	// Create new var from given name
	global $$globalVarName;

	$$globalVarName = $globalVarValue;
}
