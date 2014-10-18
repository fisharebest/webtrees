<?php
// Update the GM module database schema from version 4 to version 5
//
// Delete some old/unused configuration settings
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 Greg Roach
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

WT_DB::exec(
	"DELETE FROM `##module_setting` WHERE module_name='googlemap' AND setting_name IN (
	'GM_API_KEY', 'GM_DEFAULT_TOP_VALUE', 'GM_DISP_COUNT', 'GM_MAX_NOF_LEVELS', 'GM_PH_CONTROLS', 'GM_PH_WHEEL', 'GM_PRE_POST_MODE_1', 'GM_PRE_POST_MODE_2', 'GM_PRE_POST_MODE_3', 'GM_PRE_POST_MODE_4', 'GM_PRE_POST_MODE_5', 'GM_PRE_POST_MODE_6', 'GM_PRE_POST_MODE_7', 'GM_PRE_POST_MODE_8', 'GM_PRE_POST_MODE_9')"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
