<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;

/**
 * Upgrade the database schema from version 36 to version 37.
 */
class Migration36 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     */
    public function upgrade()
    {
        // IPv6 addresses can be up to 45 characters.
        Database::exec("ALTER TABLE `##log`     CHANGE ip_address ip_address VARCHAR(45) NOT NULL");
        Database::exec("ALTER TABLE `##message` CHANGE ip_address ip_address VARCHAR(45) NOT NULL");
        Database::exec("ALTER TABLE `##session` CHANGE ip_address ip_address VARCHAR(45) NOT NULL");
    }
}
