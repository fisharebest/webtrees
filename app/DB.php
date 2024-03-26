<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use PDO;

/**
 * Database abstraction
 */
class DB extends Manager
{
    public static function driverName(): string
    {
        return parent::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public static function prefix(string $identifier = ''): string
    {
        return parent::connection()->getTablePrefix() . $identifier;
    }

    /**
     * @internal
     */
    public static function caseInsensitiveLikeOperator(): string
    {
        if (self::driverName() === 'pgsql') {
            return 'ILIKE';
        }

        if (self::driverName() === 'sqlsrv') {
            return 'COLLATE SQL_UTF8_General_CI_AI LIKE';
        }

        return 'LIKE';
    }

    /**
     * @internal
     */
    public static function groupConcat(string $column): string
    {
        switch (self::driverName()) {
            case 'pgsql':
            case 'sqlsrv':
                return 'STRING_AGG(' . $column . ", ',')";

            case 'mysql':
            case 'sqlite':
            default:
                return 'GROUP_CONCAT(' . $column . ')';
        }
    }

    /**
     * PHPSTAN can't detect the magic methods in the parent class.
     */
    public static function query(): Builder
    {
        return parent::connection()->query();
    }
}
