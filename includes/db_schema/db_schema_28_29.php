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

// Update the database schema from version 28-29
// - earlier versions used the wrong month number for Adar in non-leap years

Database::exec(
	"UPDATE `##dates` SET d_mon = 7 WHERE d_mon = 6 && d_type = '@#DHEBREW@' AND MOD(7 * d_year + 1, 19) >= 7"
);

// Update the version to indicate success
Site::setPreference($schema_name, $next_version);
