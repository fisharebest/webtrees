<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fisharebest\Database\DB as BaseDB;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use PDO;
use SensitiveParameter;

final class DB extends BaseDB
{
    // Supported drivers
    public const string MARIADB    = 'mariadb';
    public const string MYSQL      = 'mysql';
    public const string POSTGRESQL = 'pgsql';
    public const string SQLITE     = 'sqlite';
    public const string SQL_SERVER = 'sqlsrv';
    // Unsupported drivers ;-)
    public const string FIREBIRD   = 'firebird';

    private const array REGEX_OPERATOR = [
        self::MARIADB    => 'REGEXP',
        self::MYSQL      => 'REGEXP',
        self::POSTGRESQL => '~',
        self::SQLITE     => 'REGEXP',
        self::SQL_SERVER => 'REGEXP',
        self::FIREBIRD   => '~',
    ];

    private const array GROUP_CONCAT_FUNCTION = [
        self::MARIADB    => 'GROUP_CONCAT(%s)',
        self::MYSQL      => 'GROUP_CONCAT(%s)',
        self::POSTGRESQL => "STRING_AGG(%s, ',')",
        self::SQLITE     => 'GROUP_CONCAT(%s)',
        self::SQL_SERVER => "STRING_AGG(%s, ',')",
        self::FIREBIRD   => 'LIST(%s)',
    ];

    private const array DRIVER_INITIALIZATION = [
        self::MARIADB    =>
            'SET NAMES utf8mb4;' .
            "SET sql_mode             := 'ANSI,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ONLY_FULL_GROUP_BY';" .
            "SET TIME_ZONE            := '+00:00';" .
            'SET SQL_BIG_SELECTS      := 1;' .
            'SET GROUP_CONCAT_MAX_LEN := 1048576;',
        self::MYSQL      =>
            'SET NAMES utf8mb4;' .
            "SET sql_mode             := 'ANSI,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ONLY_FULL_GROUP_BY';" .
            "SET TIME_ZONE            := '+00:00';" .
            'SET SQL_BIG_SELECTS      := 1;' .
            'SET GROUP_CONCAT_MAX_LEN := 1048576;',
        self::POSTGRESQL =>
            "SET timezone  = 'UTC';" .
            "SET datestyle = 'ISO, YMD';",
        self::SQLITE     =>
            'PRAGMA journal_mode = WAL;' .
            'PRAGMA foreign_keys = ON;' .
            'PRAGMA synchronous  = NORMAL;' .
            'PRAGMA busy_timeout = 5000;' .
            'PRAGMA cache_size   = -16000;',
        self::SQL_SERVER =>
            'SET language us_english;', // For timestamp columns
        self::FIREBIRD   =>
            'SET NAMES UTF8;',
    ];

    public static function connect(
        #[SensitiveParameter]
        string $driver,
        #[SensitiveParameter]
        string $host,
        #[SensitiveParameter]
        string $port,
        #[SensitiveParameter]
        string $database,
        #[SensitiveParameter]
        string $username,
        #[SensitiveParameter]
        string $password,
        #[SensitiveParameter]
        string $prefix,
        #[SensitiveParameter]
        string $key,
        #[SensitiveParameter]
        string $certificate,
        #[SensitiveParameter]
        string $ca,
        #[SensitiveParameter]
        bool $verify_certificate,
    ): void {
        $options = [
            // Some drivers do this and some don't. Make them consistent.
            PDO::ATTR_STRINGIFY_FETCHES => true,
        ];

        // MySQL/MariaDB support encrypted connections
        if (
            ($driver === self::MYSQL || $driver === self::MARIADB) &&
            $key !== '' && $certificate !== ''
        ) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $verify_certificate;
            $options[PDO::MYSQL_ATTR_SSL_KEY]                = Webtrees::ROOT_DIR . 'data/' . $key;
            $options[PDO::MYSQL_ATTR_SSL_CERT]               = Webtrees::ROOT_DIR . 'data/' . $certificate;

            if ($ca !== '') {
                $options[PDO::MYSQL_ATTR_SSL_CA] = Webtrees::ROOT_DIR . 'data/' . $ca;
            }
        }

        if ($driver === self::SQLITE && $database !== ':memory:') {
            $database = Webtrees::ROOT_DIR . 'data/' . $database . '.sqlite';
        }

        $capsule = new Manager();
        $capsule->addConnection([
            'driver'                   => $driver,
            'host'                     => $host,
            'port'                     => $port,
            'database'                 => $database,
            'username'                 => $username,
            'password'                 => $password,
            'prefix'                   => $prefix,
            'prefix_indexes'           => true,
            'options'                  => $options,
            'trust_server_certificate' => true, // For SQL-Server - #5246
        ]);
        $capsule->setAsGlobal();

        parent::attach(pdo: Manager::connection()->getPdo(), prefix: $prefix);

        $sql = self::DRIVER_INITIALIZATION[$driver];

        self::exec($sql);
    }

    public static function driverName(): string
    {
        return parent::connection()->pdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public static function exec(string $sql): int
    {
        return parent::connection()->exec($sql);
    }

    public static function lastInsertId(): int
    {
        return parent::connection()->lastInsertId();
    }

    /**
     * @param non-empty-string $identifier
     *
     * @return non-empty-string
     */
    public static function prefix(string $identifier): string
    {
        return parent::connection()->prefix($identifier);
    }

    public static function rollBack(): void
    {
        Manager::connection()->rollBack();
    }

    /**
     * @param list<string> $expressions
     *
     * @internal
     *
     */
    public static function concatenate(array $expressions): string
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
        if (self::driverName() === self::POSTGRESQL) {
            return 'ILIKE';
        }

        return 'LIKE';
    }

    /**
     * @internal
     */
    public static function groupConcat(string $column): string
    {
        return sprintf(self::GROUP_CONCAT_FUNCTION[self::driverName()], $column);
    }

    /**
     * @param literal-string $column
     * @param literal-string|null $alias
     * @return Expression<literal-string>
     */
    public static function binaryColumn(string $column, string|null $alias = null): Expression
    {
        if (self::driverName() === self::MYSQL || self::driverName() === self::MARIADB) {
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

    public static function queryBuilder(): QueryBuilder
    {
        return Manager::connection()->query();
    }

    public static function schemaBuilder(): SchemaBuilder
    {
        return Manager::schema();
    }

    public static function enableQueryLog(): void
    {
        Manager::connection()->enableQueryLog();
    }

    /**
     * @return array<array{query:string,bindings:array<int|string|float|null>,time:float|null}>
     */
    public static function getQueryLog(): array
    {
        return Manager::connection()->getQueryLog();
    }
}
