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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Schema;

use Fisharebest\Webtrees\Database;

/**
 * Upgrade the database schema from version 25 to version 26.
 */
class Migration25 implements MigrationInterface
{
    /**
     * Upgrade to to the next version
     *
     * @return void
     */
    public function upgrade()
    {
        // - delete unused settings and update indexes
        Database::exec(
            "DELETE FROM `##site_setting` WHERE setting_name IN ('WELCOME_TEXT_CUST_HEAD')"
        );

        // Originally, this inserted entries into wt_site_access_rule,
        // however this table now gets deleted in Migration37.
    }
}
