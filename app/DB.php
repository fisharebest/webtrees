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
use PDO;

use function str_starts_with;

/**
 * Static access to doctrine/dbal and laravel database
 */
class DB extends Manager
{
    private static Connection $connection;

    private static string $prefix;

    private static Driver&DriverInterface $driver;

    public static function connect(PDO $pdo, string $prefix): void
    {
        $driver_name = $pdo->getAttribute(attribute: PDO::ATTR_DRIVER_NAME);

        self::$driver = match ($driver_name) {
            'mysql'    => new MySQLDriver(pdo: $pdo),
            'postgres' => new PostgreSQLDriver(pdo: $pdo),
            'sqlite'   => new SQLiteDriver(pdo: $pdo),
            'sqlsrv'   => new SQLServerDriver(pdo: $pdo),
            default    => throw new DomainException(message: 'No driver available for ' . $driver_name),
        };

        self::$driver->initialize();

        $prefix_filter = static fn (string $name): bool => str_starts_with(haystack: $name, needle: $prefix);
        $configuration = new Configuration();
        $configuration->setSchemaAssetsFilter(schemaAssetsFilter: $prefix_filter);

        self::$connection = new Connection(params: [], driver: self::$driver, config: $configuration);
        self::$prefix     = $prefix;
    }

    public static function driverName(): string
    {
        return self::$driver->driverName();
    }

    public static function getDBALConnection(): Connection
    {
        return self::$connection;
    }

    public static function prefix(string $identifier = ''): string
    {
        return self::$prefix . $identifier;
    }

    public static function lastInsertId(): int
    {
        return (int) self::$connection->lastInsertId();
    }

    public static function select(string ...$expressions): QueryBuilder
    {
        return self::$connection
            ->createQueryBuilder()
            ->select(...$expressions);
    }

    public static function update(string $table): QueryBuilder
    {
        return self::$connection->update(DB::prefix($table));
    }

    /**
     * @param string                                                $table
     * @param array<array-key,array<string,int|float|string|null>>  $rows
     */
    public static function insert(string $table, array $rows): void
    {
        foreach ($rows as $row) {
            DB::getDBALConnection()->insert(DB::prefix($table), $row);
        }
    }

    public static function delete(string ...$expressions): QueryBuilder
    {
        return self::$connection
            ->createQueryBuilder()
            ->delete(...$expressions);
    }

    public static function expression(): ExpressionBuilder
    {
        return self::$connection->createExpressionBuilder();
    }

    public static function char(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::Char,
            length: $length,
            fixed: true,
            collation: self::$driver->collationASCII(),
        );
    }

    public static function varchar(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::Char,
            length: $length,
            collation: self::$driver->collationASCII(),
        );
    }

    public static function nchar(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::NChar,
            length: $length,
            fixed: true,
            collation: self::$driver->collationUTF8(),
        );
    }

    public static function nvarchar(string $name, int $length): Column
    {
        return new Column(
            name: $name,
            type: ColumnType::NVarChar,
            length: $length,
            collation: self::$driver->collationUTF8(),
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
        return new Column(
            name: $name,
            type: ColumnType::Text,
            collation: self::$driver->collationUTF8(),
        );
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
        switch (DB::driverName()) {
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
        return self::connection()->query();
    }
}
