<?php
// Update the news/blog module database schema from version 2 to 3
// - add foreign key constraints
//
// The script should assume that it can be interrupted at
// any point, and be able to continue by re-running the script.
// Fatal errors, however, should be allowed to throw exceptions,
// which will be caught by the framework.
// It shouldn't do anything that might take more than a few
// seconds, for systems with low timeout values.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

// Delete any data that might violate the new constraints
WT_DB::exec(
	"DELETE FROM `##news`".
	" WHERE user_id   NOT IN (SELECT user_id   FROM `##user`  )".
	" OR    gedcom_id NOT IN (SELECT gedcom_id FROM `##gedcom`)"
);

// Add the new constraints
try {
	WT_DB::exec(
		"ALTER TABLE `##news`".
		" ADD FOREIGN KEY news_fk1 (user_id  ) REFERENCES `##user`   (user_id)   ON DELETE CASCADE,".
		" ADD FOREIGN KEY news_fk2 (gedcom_id) REFERENCES `##gedcom` (gedcom_id) ON DELETE CASCADE"
	);
} catch (PDOException $ex) {
	// Already updated?
}

// Update the version to indicate success
WT_Site::setPreference($schema_name, $next_version);
