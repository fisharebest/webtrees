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

use PDOException;

// Update the favorites module database schema from version 3 to 4
// - an earlier update mistakenly made the fv_note column too short.
// Add the new constraints

try {
	Database::exec("ALTER TABLE `##favorite` CHANGE note note VARCHAR(1000) NULL");
} catch (PDOException $ex) {
	// Already updated?
}

// Update the version to indicate success
Site::setPreference($schema_name, $next_version);
