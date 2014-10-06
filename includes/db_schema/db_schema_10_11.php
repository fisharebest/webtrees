<?php
// Update the database schema from version 10 to 11
// - delete old configuration setting
// - increase password field from 64 to 128 chars (some versions of PHP use
//   the SHA512 algorithm for crypt() which generates 98 digit hashes)
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

// Delete some old/unused config settings
WT_DB::exec("DELETE FROM `##gedcom_setting` WHERE setting_name IN ('SEARCH_FACTS_DEFAULT', 'DISPLAY_JEWISH_GERESHAYIM', 'DISPLAY_JEWISH_THOUSANDS')");

// Increase the password column from 64 to 128 characters
WT_DB::exec("ALTER TABLE `##user` CHANGE password password VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL");

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
