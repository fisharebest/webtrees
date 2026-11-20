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
use Fisharebest\Database\Driver;
use Fisharebest\Database\Query\AliasedExpression;
use Fisharebest\Database\Query\Expression;
use PDO;
use Pdo\Firebird;
use Pdo\Mysql;
use Pdo\Pgsql;
use Pdo\Sqlite;
use SensitiveParameter;

final class DB extends BaseDB
{
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
        $options = [];

        // MySQL/MariaDB support encrypted connections
        if (
            ($driver === Driver::MySQL->value || $driver === Driver::MariaDB->value) &&
            $key !== '' && $certificate !== ''
        ) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $verify_certificate;
            $options[PDO::MYSQL_ATTR_SSL_KEY]                = Webtrees::ROOT_DIR . 'data/' . $key;
            $options[PDO::MYSQL_ATTR_SSL_CERT]               = Webtrees::ROOT_DIR . 'data/' . $certificate;

            if ($ca !== '') {
                $options[PDO::MYSQL_ATTR_SSL_CA] = Webtrees::ROOT_DIR . 'data/' . $ca;
            }
        }

        if ($driver === Driver::SQLite->value && $database !== ':memory:') {
            $database = Webtrees::ROOT_DIR . 'data/' . $database . '.sqlite';
        }

        $dsn = match (Driver::from($driver)) {
            Driver::MariaDB,
            Driver::MySQL      => 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=utf8mb4',
            Driver::PostgreSQL => 'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $database,
            Driver::SQLite     => 'sqlite:' . $database,
            Driver::SqlServer  => 'sqlsrv:Server=' . $host . ($port === '' ? '' : ',' . $port) .
                ';Database=' . $database . ';TrustServerCertificate=1',
            Driver::Firebird   => 'firebird:dbname=' . ($host === '' ? '' : $host . ($port === '' ? '' : '/' . $port) . ':') .
                $database . ';charset=UTF8',
        };

        $pdo = match (Driver::from($driver)) {
            Driver::MariaDB,
            Driver::MySQL      => new Mysql($dsn, $username, $password, $options),
            Driver::PostgreSQL => new Pgsql($dsn, $username, $password, $options),
            Driver::SQLite     => new Sqlite($dsn, $username, $password, $options),
            Driver::Firebird   => new Firebird($dsn, $username, $password, $options),
            Driver::SqlServer  => new PDO($dsn, $username, $password, $options),
        };

        $sql = match (Driver::from($driver)) {
            Driver::MariaDB,
            Driver::MySQL      =>
                'SET NAMES utf8mb4;' .
                "SET sql_mode             := 'ANSI,STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ONLY_FULL_GROUP_BY';" .
                "SET TIME_ZONE            := '+00:00';" .
                'SET SQL_BIG_SELECTS      := 1;' .
                'SET GROUP_CONCAT_MAX_LEN := 1048576;', // Update MySQL's 1024 to match MariaDB
            Driver::PostgreSQL =>
                "SET timezone  = 'UTC';" .
                "SET datestyle = 'ISO, YMD';",
            Driver::SQLite     =>
                'PRAGMA foreign_keys = ON;' .
                'PRAGMA journal_mode = WAL;' .
                'PRAGMA synchronous  = NORMAL;' .
                'PRAGMA busy_timeout = 5000;' .
                'PRAGMA cache_size   = -16000;',
            Driver::SqlServer  =>
                'SET language us_english;', // For timestamp columns
            Driver::Firebird   =>
                'SET NAMES UTF8;',
        };

        $pdo->exec($sql);

        parent::attach(pdo: $pdo, prefix: $prefix);
    }

    /**
     * Some code relies on a case-sensitive collation in the database.
     * Until we can convert MySQL/MariaDB databases, we need to cast them in queries.
     */
    public static function binaryColumn(string $column, string|null $alias = null): Expression|AliasedExpression
    {
        $driver = self::connection()->driver();

        if ($driver === Driver::MariaDB || $driver === Driver::MySQL) {
            if ($alias === null) {
                return self::raw('CAST(' . $column . ' AS binary)');
            }

            return self::alias(self::raw('CAST(' . $column . ' AS binary)'), $alias);
        }

        return self::column($column);
    }

    public static function driverName(): string
    {
        return parent::connection()->driver()->value;
    }

    public static function exec(string $sql): int|false
    {
        return parent::connection()->pdo()->exec($sql);
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
        parent::connection()->rollBack();
    }
}
