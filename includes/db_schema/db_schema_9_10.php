<?php
// Update the database schema from version 9 to 10
// - change index on name table
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

// A bug in the original version of db_schema_8_9 failed to update this :-(
// Do it again....
try {
	WT_DB::exec(
		"ALTER TABLE `##dates` CHANGE d_type d_type ENUM('@#DGREGORIAN@', '@#DJULIAN@', '@#DHEBREW@', '@#DFRENCH R@', '@#DHIJRI@', '@#DROMAN@', '@#DJALALI@')"
	);
} catch (PDOException $ex) {
	// Already been run?
}


try {
	// The INDILIST and FAMLIST scripts have been rewritten to use this index
	WT_DB::exec(
		"ALTER TABLE `##name` DROP INDEX ix2, ADD INDEX ix2 (n_surn, n_file, n_type, n_id), ADD INDEX ix3 (n_givn, n_file, n_type, n_id)"
	);
} catch (PDOException $ex) {
	// Already been run?
}

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
