<?php
/**
 * Update the GM module database schema from version 1 to version 2
 *
 * Version 0: empty database
 * Version 1: create the tables, as per PGV 4.2.1
 * Version 2: move the configuration from config.php (PGV 4.2.3 and earlier) to the pgv_site_setting table
 *
 * The script should assume that it can be interrupted at
 * any point, and be able to continue by re-running the script.
 * Fatal errors, however, should be allowed to throw exceptions,
 * which will be caught by the framework.
 * It shouldn't do anything that might take more than a few
 * seconds, for systems with low timeout values.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 Greg Roach
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

if (!defined('WT_WEBTREES')) {
header('HTTP/1.0 403 Forbidden');
exit;
}

define('WT_GM_DB_SCHEMA_1_2', '');

if (file_exists(WT_ROOT.'modules/googlemap/config.php')) {
	// Use @, in case the config.php file is incomplete/corrupt
	@require WT_ROOT.'modules/googlemap/config.php';
	// Rename settings from GOOGLEMAP_ to GM_ for consistency.
	@set_module_setting('googlemap', 'GM_ENABLED',           (int)$GOOGLEMAP_ENABLED);
	@set_module_setting('googlemap', 'GM_API_KEY',           $GOOGLEMAP_API_KEY);
	@set_module_setting('googlemap', 'GM_MAP_TYPE',          $GOOGLEMAP_MAP_TYPE);
	@set_module_setting('googlemap', 'GM_MIN_ZOOM',          $GOOGLEMAP_MIN_ZOOM);
	@set_module_setting('googlemap', 'GM_MAX_ZOOM',          $GOOGLEMAP_MAX_ZOOM);
	@set_module_setting('googlemap', 'GM_XSIZE',             $GOOGLEMAP_XSIZE);
	@set_module_setting('googlemap', 'GM_YSIZE',             $GOOGLEMAP_YSIZE);
	@set_module_setting('googlemap', 'GM_PRECISION_0',       $GOOGLEMAP_PRECISION_0);
	@set_module_setting('googlemap', 'GM_PRECISION_1',       $GOOGLEMAP_PRECISION_1);
	@set_module_setting('googlemap', 'GM_PRECISION_2',       $GOOGLEMAP_PRECISION_2);
	@set_module_setting('googlemap', 'GM_PRECISION_3',       $GOOGLEMAP_PRECISION_3);
	@set_module_setting('googlemap', 'GM_PRECISION_4',       $GOOGLEMAP_PRECISION_4);
	@set_module_setting('googlemap', 'GM_PRECISION_5',       $GOOGLEMAP_PRECISION_5);
	@set_module_setting('googlemap', 'GM_DEFAULT_TOP_VALUE', $GM_DEFAULT_TOP_VALUE);
	@set_module_setting('googlemap', 'GM_MAX_NOF_LEVELS',    $GM_MAX_NOF_LEVELS);
	@set_module_setting('googlemap', 'GM_COORD',             (int)$GOOGLEMAP_COORD);
	@set_module_setting('googlemap', 'GM_PLACE_HIERARCHY',   (int)$GOOGLEMAP_PLACE_HIERARCHY);
	@set_module_setting('googlemap', 'GM_PH_XSIZE',          $GOOGLEMAP_PH_XSIZE);
	@set_module_setting('googlemap', 'GM_PH_YSIZE',          $GOOGLEMAP_PH_YSIZE);
	@set_module_setting('googlemap', 'GM_PH_MARKER',         $GOOGLEMAP_PH_MARKER);
	@set_module_setting('googlemap', 'GM_DISP_SHORT_PLACE',  (int)$GM_DISP_SHORT_PLACE);
	@set_module_setting('googlemap', 'GM_PH_WHEEL',          (int)$GOOGLEMAP_PH_WHEEL);
	@set_module_setting('googlemap', 'GM_PH_CONTROLS',       (int)$GOOGLEMAP_PH_CONTROLS);
	@set_module_setting('googlemap', 'GM_DISP_COUNT',        (int)$GM_DISP_COUNT);

	for ($i=1; $i<=9; $i++) {
		@set_module_setting('googlemap', 'GM_MARKER_COLOR_'.$i,  $GM_MARKER_COLOR[$i]);
		@set_module_setting('googlemap', 'GM_MARKER_SIZE_'.$i,   $GM_MARKER_SIZE[$i]);
		@set_module_setting('googlemap', 'GM_PREFIX_'.$i,        $GM_PREFIX[$i]);
		@set_module_setting('googlemap', 'GM_POSTFIX_'.$i,       $GM_POSTFIX[$i]);
		@set_module_setting('googlemap', 'GM_PRE_POST_MODE_'.$i, $GM_PRE_POST_MODE[$i]);
	}
	@unlink(WT_ROOT.'modules/googlemap/config.php');
}

// Update the version to indicate sucess
set_site_setting($schema_name, $next_version);
