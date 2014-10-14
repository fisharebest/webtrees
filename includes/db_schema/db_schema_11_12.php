<?php
// Update the database schema from version 11 to 12
// - delete the wt_name.n_list column; it has never been used
// - a bug in webtrees 1.1.2 caused the wt_name.n_full column
// to include slashes around the surname.  These are unnecessary,
// and cause problems when we try to match the name from the
// gedcom with the name from the table.
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

// Remove slashes from INDI names
WT_DB::exec("UPDATE `##name` SET n_full=REPLACE(n_full, '/', '') WHERE n_surn IS NOT NULL");

// Remove the n_list column
try {
	WT_DB::exec("ALTER TABLE `##name` DROP n_list");
} catch (PDOException $x) {
	// Already done?
}

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
