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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use DomainException;
use Fisharebest\Webtrees\DB\Column;
use Fisharebest\Webtrees\DB\ColumnType;
use Fisharebest\Webtrees\DB\Drivers\DriverInterface;
use Fisharebest\Webtrees\DB\Drivers\MySQLDriver;
use Fisharebest\Webtrees\DB\Drivers\PostgreSQLDriver;
use Fisharebest\Webtrees\DB\Drivers\SQLiteDriver;
use Fisharebest\Webtrees\DB\Drivers\SQLServerDriver;
use Fisharebest\Webtrees\DB\ForeignKey;
use Fisharebest\Webtrees\DB\Index;
use Fisharebest\Webtrees\DB\PrimaryKey;
use Fisharebest\Webtrees\DB\UniqueIndex;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use PDO;
use PDOException;
use RuntimeException;
use SensitiveParameter;

use function str_starts_with;

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

    private static Connection $dbal_connection;

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
        if ($driver === self::MYSQL && $key !== '' && $certificate !== '' && $ca !== '') {
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
            'driver'         => $driver,
            'host'           => $host,
            'port'           => $port,
            'database'       => $database,
            'username'       => $username,
            'password'       => $password,
            'prefix'         => $prefix,
            'prefix_indexes' => true,
            'options'        => $options,
        ]);
        $capsule->setAsGlobal();

        // Eager-load the connection, to prevent database credentials appearing in error logs.
        try {
            self::pdo();
        } catch (PDOException $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        $sql = self::DRIVER_INITIALIZATION[$driver];

        if ($sql !== '') {
            self::exec($sql);
        }

        $dbal_driver = match ($driver) {
            self::MYSQL      => new MySQLDriver(pdo: self::pdo()),
            self::POSTGRES   => new PostgreSQLDriver(pdo: self::pdo()),
            self::SQLITE     => new SQLiteDriver(pdo: self::pdo()),
            self::SQL_SERVER => new SQLServerDriver(pdo: self::pdo()),
        };

        $configuration = new Configuration();
        $configuration->setSchemaAssetsFilter(schemaAssetsFilter: self::schemaAssetsFilter(...));

        self::$dbal_connection = new Connection(params: [], driver: $dbal_driver, config: $configuration);
    }

    private static function schemaAssetsFilter(string $asset): bool
    {
        return str_starts_with(haystack: $asset, needle: self::prefix());
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

    public static function prefix(string $identifier = ''): string
    {
        return parent::connection()->getTablePrefix() . $identifier;
    }

    public static function rollBack(): void
    {
        parent::connection()->rollBack();
    }

    /**
     * @internal
     */
    public static function iLike(): string
    {
        if (self::driverName() === self::POSTGRES) {
            return 'ILIKE';
        }

        if (self::driverName() === self::SQL_SERVER) {
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
            case self::POSTGRES:
            case self::SQL_SERVER:
                return 'STRING_AGG(' . $column . ", ',')";

            case self::MYSQL:
            case self::SQLITE:
            default:
                return 'GROUP_CONCAT(' . $column . ')';
        }
    }

    public static function binaryColumn(string $column, string|null $alias = null): Expression
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

    public static function getDBALConnection(): Connection
    {
        return self::$dbal_connection;
    }

    public static function select(string ...$expressions): QueryBuilder
    {
        return self::$dbal_connection
            ->createQueryBuilder()
            ->select(...$expressions);
    }

    public static function update(string $table): QueryBuilder
    {
        return parent::connection()->update(self::prefix($table));
    }

    /**
     * @param string                                                $table
     * @param array<array-key,array<string,int|float|string|null>>  $rows
     */
    public static function insert(string $table, array $rows): void
    {
        foreach ($rows as $row) {
            self::getDBALConnection()->insert(self::prefix($table), $row);
        }
    }

    public static function delete(string ...$expressions): QueryBuilder
    {
        return self::$dbal_connection
            ->createQueryBuilder()
            ->delete(...$expressions);
    }

    public static function expression(): ExpressionBuilder
    {
        return self::$dbal_connection->createExpressionBuilder();
    }

    public static function char(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::Char,
            length: $length,
            fixed: true,
            collation: self::COLLATION_ASCII[self::driverName()],
        );
    }

    public static function varchar(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::Char,
            length: $length,
            collation: self::COLLATION_ASCII[self::driverName()],
        );
    }

    public static function nchar(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::NChar,
            length: $length,
            fixed: true,
            collation: self::COLLATION_UTF8[self::driverName()],
        );
    }

    public static function nvarchar(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::NVarChar,
            length: $length,
            collation: self::COLLATION_UTF8[self::driverName()],
        );
    }

    public static function integer(string $name): Column
    {
        return new Column(name: $name, type: ColumnType::Integer);
    }

    public static function float(string $name): Column
    {
        return new Column(name: $name, type: ColumnType::Float);
    }

    public static function text(string $name): Column
    {
        return new Column(name: $name, type: ColumnType::Text, collation: self::COLLATION_UTF8[self::driverName()]);
    }

    public static function timestamp(string $name, int $precision = 0): Column
    {
        return new Column(name: $name, type: ColumnType::Timestamp, precision: $precision);
    }

    /**
     * @param array<array-key,string> $columns
     *
     * @return PrimaryKey
     */
    public static function primaryKey(array $columns): PrimaryKey
    {
        return new PrimaryKey(columns: $columns);
    }

    /**
     * @param array<array-key,string> $columns
     *
     * @return Index
     */
    public static function index(array $columns): Index
    {
        return new Index(columns: $columns);
    }

    /**
     * @param array<array-key,string> $columns
     *
     * @return UniqueIndex
     */
    public static function uniqueIndex(array $columns): UniqueIndex
    {
        return new UniqueIndex(columns: $columns);
    }

    /**
     * @param array<array-key,string> $local_columns
     * @param string                  $foreign_table
     * @param array<array-key,string> $foreign_columns
     *
     * @return ForeignKey
     */
    public static function foreignKey(array $local_columns, string $foreign_table, array $foreign_columns = null): ForeignKey
    {
        return new ForeignKey(
            local_columns: $local_columns,
            foreign_table: $foreign_table,
            foreign_columns: $foreign_columns ?? $local_columns,
        );
    }
}
