<?php
// Update the GM module database schema from version 1 to version 2
//
// Version 0: empty database
// Version 1: create the tables, as per PGV 4.2.1
// Version 2: update the tables to support streetview
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

// Create all of the tables needed for this module
try {
	WT_DB::exec(
		"ALTER TABLE `##placelocation` ADD (".
		" pl_media      VARCHAR(60)     NULL,".
		" sv_long       FLOAT           NOT NULL DEFAULT 0,".
		" sv_lati       FLOAT           NOT NULL DEFAULT 0,".
		" sv_bearing    FLOAT           NOT NULL DEFAULT 0,".
		" sv_elevation  FLOAT           NOT NULL DEFAULT 0,".
		" sv_zoom       FLOAT           NOT NULL DEFAULT 1".
		")"
	);
} catch (PDOException $ex) {
	// Already done this?
}

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
