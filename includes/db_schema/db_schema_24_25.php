<?php
// Update the database schema from version 24-25
// - delete unused settings and update indexes
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

// Tree settings become site settings
WT_DB::exec(
	"INSERT IGNORE INTO `##site_setting` (setting_name, setting_value)" .
	" SELECT setting_name, setting_value" .
	" FROM `##gedcom_setting`" .
	" WHERE setting_name IN ('SHOW_REGISTER_CAUTION', 'WELCOME_TEXT_CUST_HEAD') OR setting_name like 'WELCOME_TEXT_AUTH_MODE%'" .
	" GROUP BY setting_name"
);

WT_DB::exec(
	"DELETE FROM `##gedcom_setting` WHERE setting_name IN ('ALLOW_EDIT_GEDCOM', 'SHOW_REGISTER_CAUTION', 'WELCOME_TEXT_CUST_HEAD') OR setting_name like 'WELCOME_TEXT_AUTH_MODE%'"
);

WT_DB::exec(
	"DELETE FROM `##site_setting` WHERE setting_name IN ('STORE_MESSAGES')"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
