<?php
// Update the GM module database schema from version 0 to version 1
//
// Version 0: empty database
// Version 1: create the tables, as per PGV 4.2.1
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
WT_DB::exec(
	"CREATE TABLE IF NOT EXISTS `##placelocation` (".
	" pl_id        INTEGER      NOT NULL,".
	" pl_parent_id INTEGER          NULL,".
	" pl_level     INTEGER          NULL,".
	" pl_place     VARCHAR(255)     NULL,".
	" pl_long      VARCHAR(30)      NULL,".
	" pl_lati      VARCHAR(30)      NULL,".
	" pl_zoom      INTEGER          NULL,".
	" pl_icon      VARCHAR(255)     NULL,".
	" PRIMARY KEY     (pl_id),".
	"         KEY ix1 (pl_level),".
	"         KEY ix2 (pl_long),".
	"         KEY ix3 (pl_lati),".
	"         KEY ix4 (pl_place),".
	"         KEY ix5 (pl_parent_id)".
	") COLLATE utf8_unicode_ci ENGINE=InnoDB"
);

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
