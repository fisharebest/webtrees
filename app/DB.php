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
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\DefaultExpression;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\ForeignKeyConstraint\ReferentialAction;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Index\IndexType;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Types\AsciiStringType;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Expression;
use PDO;
use PDOException;
use RuntimeException;
use SensitiveParameter;

use function str_starts_with;

final class DB extends Manager
{
    // Supported drivers
    public const string MYSQL      = 'mysql';
    public const string POSTGRES   = 'pgsql';
    public const string SQLITE     = 'sqlite';
    public const string SQL_SERVER = 'sqlsrv';

    private const array COLLATION_ASCII = [
        self::MYSQL      => 'ascii_bin',
        self::POSTGRES   => 'C',
        self::SQLITE     => 'BINARY',
        self::SQL_SERVER => 'Latin1_General_Bin',
    ];

    // MySQL 5.x uses utf8mb4_unicode_ci (Unicode 4.0) for utf8mb4
    // MySQL 5.7 uses utf8mb4_unicode_520_ci (Unicode 5.2) for utf8mb4
    // MySQL 8.x uses utf8mb4_0900_ai_ci (Unicode 9.0) for utf8mb4
    // MySQL 9.x uses utf8mb4_uca1400_ai_ci (Unicode 14.0) for utf8mb4
    // Just specify the character set and let MySQL choose the latest collation
    private const array CHARSET_UTF8 = [
        self::MYSQL      => 'utf8mb4',
        self::POSTGRES   => null,
        self::SQLITE     => null,
        self::SQL_SERVER => null,
    ];

    private const array COLLATION_UTF8 = [
        self::MYSQL      => null,
        self::POSTGRES   => 'und-x-icu',
        self::SQLITE     => 'NOCASE',
        self::SQL_SERVER => 'utf8_CI_AI',
    ];

    private const array TABLE_OPTIONS = [
        self::MYSQL      => ['charset' => 'utf8mb4'],
        self::POSTGRES   => [],
        self::SQLITE     => [],
        self::SQL_SERVER => [],
    ];

    private const array REGEX_OPERATOR = [
        self::MYSQL      => 'REGEXP',
        self::POSTGRES   => '~',
        self::SQLITE     => 'REGEXP',
        self::SQL_SERVER => 'REGEXP',
    ];

    private const array GROUP_CONCAT_FUNCTION = [
        self::MYSQL      => 'GROUP_CONCAT(%s)',
        self::POSTGRES   => "STRING_AGG(%s, ',')",
        self::SQLITE     => 'GROUP_CONCAT(%s)',
        self::SQL_SERVER => "STRING_AGG(%s, ',')",
    ];

    private const array DRIVER_INITIALIZATION = [
        self::MYSQL      => "SET NAMES utf8mb4, sql_mode := 'ANSI,STRICT_ALL_TABLES', TIME_ZONE := '+00:00', SQL_BIG_SELECTS := 1, GROUP_CONCAT_MAX_LEN := 1048576",
        self::POSTGRES   => "CREATE COLLATION IF NOT EXISTS webtrees_ci_ai (provider=icu, locale='und', deterministic=false)",
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

        // doctrine/dbal

        $parameters = match ($driver) {
            self::MYSQL => [
                'driver'   => 'pdo_mysql',
                'dbname'   => $database,
                'user'     => $username,
                'password' => $password,
                'port'     => $port,
                'charset'  => 'utf8mb4',
            ],
            self::POSTGRES => [
                'driver'   => 'pdo_pgsql',
                'dbname'   => $database,
                'user'     => $username,
                'password' => $password,
                'port'     => $port,
                'charset'  => 'utf8',
            ],
            self::SQLITE => [
                'driver'   => 'pdo_sqlite',
                'path'     => $database,
            ],
            self::SQL_SERVER => [
                'driver'   => 'pdo_sqlsrv',
                'dbname'   => $database,
                'user'     => $username,
                'password' => $password,
                'port'     => $port,
            ],
        };

        $configuration = new Configuration();
        $configuration->setSchemaAssetsFilter(schemaAssetsFilter: self::schemaAssetsFilter(...));

        self::$dbal_connection = DriverManager::getConnection(params: $parameters, config: $configuration);

        // illuminate/database

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

    private static function schemaAssetsFilter(string $asset): bool
    {
        return str_starts_with(haystack: $asset, needle: parent::connection()->getTablePrefix());
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
        return sprintf(self::GROUP_CONCAT_FUNCTION[self::driverName()], $column);
    }

    /**
     * @return Expression<string>
     */
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
     * @return array<string,string>
     */
    public static function tableOptions(): array
    {
        return self::TABLE_OPTIONS[self::driverName()];
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

    /**
     * @param non-empty-string                                     $table
     * @param array<array-key,array<string,int|float|string|null>> $rows
     */
    public static function insert(string $table, array $rows): void
    {
        foreach ($rows as $row) {
            self::getDBALConnection()->insert(table: self::prefix($table), data: $row);
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

    /**
     * @param non-empty-string $name
     */
    public static function char(string $name, int $length, bool $nullable = false, string|null $default = null): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new AsciiStringType())
            ->setLength(length: $length)
            ->setFixed(fixed: true)
            ->setNotNull(notNull: !$nullable)
            ->setDefaultValue(defaultValue: $default)
            ->setCollation(collation: self::COLLATION_ASCII[self::driverName()])
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function varchar(string $name, int $length, bool $nullable = false, string|null $default = null): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new AsciiStringType())
            ->setLength(length: $length)
            ->setFixed(fixed: false)
            ->setNotNull(notNull: !$nullable)
            ->setDefaultValue(defaultValue: $default)
            ->setCollation(collation: self::COLLATION_ASCII[self::driverName()])
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function nchar(string $name, int $length, bool $nullable = false, string|null $default = null): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new StringType())
            ->setLength(length: $length)
            ->setFixed(fixed: true)
            ->setNotNull(notNull: !$nullable)
            ->setDefaultValue(defaultValue: $default)
            ->setCharset(charset: self::CHARSET_UTF8[self::driverName()])
            ->setCollation(collation: self::COLLATION_UTF8[self::driverName()])
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function nvarchar(string $name, int $length, bool $nullable = false, string|null $default = null): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new StringType())
            ->setLength(length: $length)
            ->setFixed(fixed: false)
            ->setNotNull(notNull: !$nullable)
            ->setDefaultValue(defaultValue: $default)
            ->setCharset(charset: self::CHARSET_UTF8[self::driverName()])
            ->setCollation(collation: self::COLLATION_UTF8[self::driverName()])
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function integer(string $name, bool $autoincrement = false, bool $nullable = false, int|null $default = null): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new IntegerType())
            ->setAutoincrement($autoincrement)
            ->setNotNull(notNull: !$nullable)
            ->setDefaultValue(defaultValue: $default)
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function float(string $name, bool $nullable = false): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new FloatType())
            ->setNotNull(notNull: !$nullable)
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function text(string $name): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new TextType())
            ->setCharset(charset: self::CHARSET_UTF8[self::driverName()])
            ->setCollation(collation: self::COLLATION_UTF8[self::driverName()])
            ->create();
    }

    /**
     * @param non-empty-string $name
     */
    public static function timestamp(string $name, int $precision = 0, DefaultExpression|null $default = null): Column
    {
        return Column::editor()
            ->setUnquotedName(name: $name)
            ->setType(type: new DateTimeImmutableType())
            ->setPrecision($precision)
            ->setDefaultValue(defaultValue: $default)
            ->create();
    }

    /**
     * @param non-empty-list<non-empty-string> $columns
     */
    public static function primaryKey(array $columns): PrimaryKeyConstraint
    {
        return PrimaryKeyConstraint::editor()
            ->setUnquotedColumnNames(...$columns)
            ->create();
    }

    /**
     * @param non-empty-string                 $name
     * @param non-empty-list<non-empty-string> $columns
     */
    public static function index(string $name, array $columns): Index
    {
        return Index::editor()
            ->setType(IndexType::REGULAR)
            ->setUnquotedName(self::prefix($name))
            ->setUnquotedColumnNames(...$columns)
            ->create();
    }

    /**
     * @param non-empty-string                 $name
     * @param non-empty-list<non-empty-string> $columns
     */
    public static function uniqueIndex(string $name, array $columns): Index
    {
        return Index::editor()
            ->setType(IndexType::UNIQUE)
            ->setUnquotedName(self::prefix($name))
            ->setUnquotedColumnNames(...$columns)
            ->create();
    }

    /**
     * @param non-empty-string                  $name
     * @param non-empty-array<non-empty-string> $local_columns
     * @param non-empty-string                  $foreign_table
     * @param non-empty-array<non-empty-string> $foreign_columns
     */
    public static function foreignKey(
        string $name,
        array $local_columns,
        string $foreign_table,
        array|null $foreign_columns = null,
        ReferentialAction $on_delete = ReferentialAction::NO_ACTION,
        ReferentialAction $on_update = ReferentialAction::NO_ACTION,
    ): ForeignKeyConstraint {
        $foreign_columns ??= $local_columns;

        return ForeignKeyConstraint::editor()
            ->setUnquotedName(self::prefix($name))
            ->setUnquotedReferencingColumnNames(...$local_columns)
            ->setUnquotedReferencedTableName(self::prefix($foreign_table))
            ->setUnquotedReferencedColumnNames(...$foreign_columns)
            ->setOnDeleteAction($on_delete)
            ->setOnUpdateAction($on_update)
            ->create();
    }
}
