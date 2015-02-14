<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

// Update the news/blog module database schema from version 0 to version 1
//
// Version 0: empty database
// Version 1: create the tables, as per PGV 4.2.1

Database::exec(
	"CREATE TABLE IF NOT EXISTS `##news` (" .
	" n_id       INTEGER AUTO_INCREMENT NOT NULL," .
	" n_username VARCHAR(100)           NOT NULL," .
	" n_date     INTEGER                NOT NULL," .
	" n_title    VARCHAR(255)           NOT NULL," .
	" n_text     TEXT                   NOT NULL," .
	" PRIMARY KEY     (n_id)," .
	"         KEY ix1 (n_username)" .
	") COLLATE utf8_unicode_ci ENGINE=InnoDB"
);

// Update the version to indicate success
Site::setPreference($schema_name, $next_version);
