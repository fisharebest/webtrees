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

namespace Fisharebest\Webtrees;

use Closure;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use PDO;
use RuntimeException;

/**
 * Database abstraction
 */
class DB extends Manager
{
    // Supported drivers
    public const MYSQL      = 'mysql';
    public const POSTGRES   = 'pgsql';
    public const SQLITE     = 'sqlite';
    public const SQL_SERVER = 'sqlsrv';

    private const COLLATION_ASCII = [
        self::MYSQL      => 'ascii_bin',
        self::POSTGRES   => 'C',
        self::SQLITE     => 'BINARY',
        self::SQL_SERVER => 'Latin1_General_Bin',
    ];

    private const COLLATION_UTF8 = [
        self::MYSQL      => 'utf8mb4_unicode_ci',
        self::POSTGRES   => 'und-x-icu',
        self::SQLITE     => 'NOCASE',
        self::SQL_SERVER => 'utf8_CI_AI',
    ];

    private const REGEX_OPERATOR = [
        self::MYSQL      => 'REGEXP',
        self::POSTGRES   => '~',
        self::SQLITE     => 'REGEXP',
        self::SQL_SERVER => 'REGEXP',
    ];

    private const DRIVER_INITIALIZATION = [
        self::MYSQL      => "SET NAMES utf8mb4, sql_mode := 'ANSI,STRICT_ALL_TABLES', TIME_ZONE := '+00:00', SQL_BIG_SELECTS := 1, GROUP_CONCAT_MAX_LEN := 1048576",
        self::POSTGRES   => '',
        self::SQLITE     => 'PRAGMA foreign_keys = ON',
        self::SQL_SERVER => 'SET language us_english', // For timestamp columns
    ];

    public static function driverName(): string
    {
        return self::pdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * @return int|false
     */
    public static function exec(string $sql)
    {
        return self::pdo()->exec($sql);
    }

    public static function lastInsertId(): int
    {
        $return = self::pdo()->lastInsertId();

        if ($return === false) {
            throw new RuntimeException('Unable to retrieve last insert ID');
        }

        // All IDs are integers in our schema.
        return (int) $return;
    }

    private static function pdo(): PDO
    {
        return parent::connection()->getPdo();
    }

    public static function prefix(string $identifier): string
    {
        return parent::connection()->getTablePrefix() . $identifier;
    }

    /**
     * SQL-Server needs to be told that we are going to insert into an identity column.
     *
     * @param Closure(): void $callback
     */
    public static function identityInsert(string $table, Closure $callback): void
    {
        if (self::driverName() === self::SQL_SERVER) {
            self::exec('SET IDENTITY_INSERT [' . self::prefix($table) . '] ON');
        }

        $callback();

        if (self::driverName() === self::SQL_SERVER) {
            self::exec('SET IDENTITY_INSERT [' . self::prefix($table) . '] OFF');
        }
    }

    public static function rollBack(): void
    {
        parent::connection()->rollBack();
    }

    /**
     * @internal
     *
     * @param list<string> $expressions
     */
    public static function concat(array $expressions): string
    {
        if (self::driverName() === self::SQL_SERVER) {
            return 'CONCAT(' . implode(', ', $expressions) . ')';
        }

        // ANSI standard.  MySQL uses this with ANSI mode
        return '(' . implode(' || ', $expressions) . ')';
    }

    /**
     * @internal
     */
    public static function iLike(): string
    {
        if (self::driverName() === self::POSTGRES) {
            return 'ILIKE';
        }

        return 'LIKE';
    }

    /**
     * @internal
     */
    public static function groupConcat(string $column): string
    {
        switch (self::driverName()) {
            case self::POSTGRES:
            case self::SQL_SERVER:
                return 'STRING_AGG(' . $column . ", ',')";

            case self::MYSQL:
            case self::SQLITE:
            default:
                return 'GROUP_CONCAT(' . $column . ')';
        }
    }

    /**
     * @return Expression
     */
    public static function binaryColumn(string $column, ?string $alias = null): Expression
    {
        if (self::driverName() === self::MYSQL) {
            $sql = 'CAST(' . $column . ' AS binary)';
        } else {
            $sql = $column;
        }

        if ($alias !== null) {
            $sql .= ' AS ' . $alias;
        }

        return new Expression($sql);
    }

    public static function regexOperator(): string
    {
        return self::REGEX_OPERATOR[self::driverName()];
    }

    /**
     * PHPSTAN can't detect the magic methods in the parent class.
     */
    public static function query(): Builder
    {
        return parent::connection()->query();
    }
}
