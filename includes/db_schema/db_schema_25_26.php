<?php
// Update the database schema from version 25-26
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

// Site settings that are no longer used.
WT_DB::exec(
	"DELETE FROM `##site_setting` WHERE setting_name IN ('WELCOME_TEXT_CUST_HEAD')"
);

// Modern versions of Internet Explorer use a different
WT_DB::exec(
	"INSERT IGNORE INTO `##site_access_rule` (user_agent_pattern, rule, comment) VALUES".
	" ('Mozilla/% (Windows%; Trident%; rv:%) like Gecko', 'allow', 'Modern Internet Explorer')"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
