<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Schema;

/**
 * Upgrade the database schema from version 17 to version 18 (webtrees 1.3.0).
 */
class Migration17 implements MigrationInterface
{
    /**
     * Upgrade to the next version.
     *
     * @return void
     */
    public function upgrade(): void
    {
        // Originally, this created wt_site_access_rule,
        // however this table now gets deleted in Migration37.
    }
}
