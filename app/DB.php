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

use Closure;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use PDO;
use PDOException;
use RuntimeException;
use SensitiveParameter;

final class DB extends Manager
{
    // Supported drivers
    public const string MARIADB    = 'mariadb';
    public const string MYSQL      = 'mysql';
    public const string POSTGRESQL = 'pgsql';
    public const string SQLITE     = 'sqlite';
    public const string SQL_SERVER = 'sqlsrv';

    private const array COLLATION_UTF8 = [
        self::MARIADB    => 'utf8mb4_bin',
        self::MYSQL      => 'utf8mb4_bin',
        self::POSTGRESQL => 'und-x-icu',
        self::SQLITE     => null,
        self::SQL_SERVER => 'Latin1_General_100_BIN2_UTF8',
    ];

    private const array REGEX_OPERATOR = [
        self::MARIADB    => 'REGEXP',
        self::MYSQL      => 'REGEXP',
        self::POSTGRESQL => '~',
        self::SQLITE     => 'REGEXP',
        self::SQL_SERVER => 'REGEXP',
    ];

    private const array GROUP_CONCAT_FUNCTION = [
        self::MARIADB    => 'GROUP_CONCAT(%s)',
        self::MYSQL      => 'GROUP_CONCAT(%s)',
        self::POSTGRESQL => "STRING_AGG(%s, ',')",
        self::SQLITE     => 'GROUP_CONCAT(%s)',
        self::SQL_SERVER => "STRING_AGG(%s, ',')",
    ];

    private const array DRIVER_INITIALIZATION = [
        self::MARIADB    => "SET NAMES utf8mb4, sql_mode := 'ANSI,STRICT_ALL_TABLES', TIME_ZONE := '+00:00', SQL_BIG_SELECTS := 1, GROUP_CONCAT_MAX_LEN := 1048576",
        self::MYSQL      => "SET NAMES utf8mb4, sql_mode := 'ANSI,STRICT_ALL_TABLES', TIME_ZONE := '+00:00', SQL_BIG_SELECTS := 1, GROUP_CONCAT_MAX_LEN := 1048576",
        self::POSTGRESQL => '',
        self::SQLITE     => 'PRAGMA foreign_keys = ON',
        self::SQL_SERVER => 'SET language us_english', // For timestamp columns
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
            $key !== '' && $certificate !== '' && $ca !== ''
        ) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $verify_certificate;
            $options[PDO::MYSQL_ATTR_SSL_KEY]                = Webtrees::ROOT_DIR . 'data/' . $key;
            $options[PDO::MYSQL_ATTR_SSL_CERT]               = Webtrees::ROOT_DIR . 'data/' . $certificate;
            $options[PDO::MYSQL_ATTR_SSL_CA]                 = Webtrees::ROOT_DIR . 'data/' . $ca;
        }

        if ($driver === self::SQLITE && $database !== ':memory:') {
            $database = Webtrees::ROOT_DIR . 'data/' . $database . '.sqlite';
        }

        $capsule = new self();
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

        // Eager-load the connection to prevent database credentials appearing in error logs.
        try {
            self::pdo();
        } catch (PDOException $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        $sql = self::DRIVER_INITIALIZATION[$driver];

        if ($sql !== '') {
            self::exec($sql);
        }
    }

    public static function driverName(): string
    {
        return self::pdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public static function exec(string $sql): int|false
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

    /**
     * @param non-empty-string $identifier
     *
     * @return non-empty-string
     */
    public static function prefix(string $identifier): string
    {
        return parent::connection()->getTablePrefix() . $identifier;
    }

    public static function collation(): string|null
    {
        return self::COLLATION_UTF8[self::driverName()];
    }

    /**
     * SQL-Server needs to be told that we are going to insert into an identity column.
     *
     * @param non-empty-string $table
     * @param Closure(): void  $callback
     */
    public static function identityInsert(string $table, Closure $callback): void
    {
        if (self::driverName() === self::SQL_SERVER) {
            self::exec(sql: 'SET IDENTITY_INSERT [' . self::prefix(identifier: $table) . '] ON');
        }

        $callback();

        if (self::driverName() === self::SQL_SERVER) {
            self::exec(sql: 'SET IDENTITY_INSERT [' . self::prefix(identifier: $table) . '] OFF');
        }
    }

    public static function rollBack(): void
    {
        parent::connection()->rollBack();
    }

    /**
     * @param list<string> $expressions
     *
     * @internal
     *
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
     * @return Expression<string>
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

    /**
     * PHPSTAN can't detect the magic methods in the parent class.
     */
    public static function query(): Builder
    {
        return parent::connection()->query();
    }
}
