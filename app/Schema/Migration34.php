<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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
use Fisharebest\Webtrees\Module;

/**
 * Upgrade the database schema from version 34 to version 35.
 */
class Migration34 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     */
    public function upgrade()
    {
        // New modules (charts) have been added.
        Module::getInstalledModules('enabled');

        // Delete old/unused settings
        Database::exec(
            "DELETE FROM `##gedcom_setting` WHERE setting_name IN ('COMMON_NAMES_ADD', 'COMMON_NAMES_REMOVE', 'COMMON_NAMES_THRESHOLD')"
        );
    }
}
