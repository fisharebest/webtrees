<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTASET NAMES 'utf8' COLLATE 'utf8_unicode_ci'LITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Exception;
use Fisharebest\Webtrees\Schema\MigrationInterface;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Extend PHP's native PDO class.
 */
class Database
{
    private const PDO_OPTIONS = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE               => PDO::CASE_LOWER,
        PDO::ATTR_AUTOCOMMIT         => true,
    ];

    /** @var Database Implement the singleton pattern */
    private static $instance;

    /** @var PDO Native PHP database driver */
    private static $pdo;

    /** @var Statement[] Cache of prepared statements */
    private static $prepared = [];

    /** @var string Prefix allows multiple instances in one database */
    private static $table_prefix = '';

    /**
     * Implement the singleton pattern, using a static accessor.
     *
     * @param string[] $config
     *
     * @return void
     * @throws Exception
     */
    public static function createInstance(array $config)
    {
        self::$table_prefix = $config['tblpfx'];

        $dsn = (substr($config['dbhost'], 0, 1) === '/' ?
            "mysql:unix_socket='{$config['dbhost']};dbname={$config['dbname']}" : "mysql:host={$config['dbhost']};dbname={$config['dbname']};port={$config['dbport']}"
        );

        // Create the underlying PDO object.
        self::$pdo = new PDO($dsn, $config['dbuser'], $config['dbpass'], self::PDO_OPTIONS);
        self::$pdo->exec("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
        self::$pdo->prepare("SET time_zone = :time_zone")->execute(['time_zone' => date('P')]);
        self::$instance = new self();

        $capsule = new DB();
        $capsule->addConnection([
            'driver'         => 'mysql',
            'host'           => $config['dbhost'],
            'port'           => $config['dbport'],
            'database'       => $config['dbname'],
            'username'       => $config['dbuser'],
            'password'       => $config['dbpass'],
            'prefix'         => $config['tblpfx'],
            'prefix_indexes' => true,
            'charset'        => 'utf8',
            'collation'      => 'utf8_unicode_ci',
            'enigne'         => 'InnoDB',
        ]);
        $capsule->setAsGlobal();

        // Add logging/debugging.
        DebugBar::initPDO($capsule->getConnection()->getPdo());

        self::registerMacros();
    }

    /**
     * Register macros to help search for substrings
     *
     * @return void
     */
    public static function registerMacros(): void
    {
        Builder::macro('whereContains', function (string $column, string $search, string $boolean = 'and') {
            $search = strtr($search, ['\\' => '\\\\', '%'  => '\\%', '_'  => '\\_', ' ' => '%']);

            return $this->where($column, 'LIKE', '%' . $search . '%', $boolean);
        });
    }

    /**
     * We don't access $instance directly, only via query(), exec() and prepare()
     *
     * @throws Exception
     *
     * @return Database
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Determine the most recently created value of an AUTO_INCREMENT field.
     *
     * @return int
     */
    public static function lastInsertId(): int
    {
        return (int) self::$pdo->lastInsertId();
    }

    /**
     * Execute an SQL statement, and log the result.
     *
     * @param string $sql The SQL statement to execute
     *
     * @return int The number of rows affected by this SQL query
     */
    public static function exec($sql): int
    {
        $sql = str_replace('##', self::$table_prefix, $sql);

        return self::$pdo->exec($sql);
    }

    /**
     * Prepare an SQL statement for execution.
     *
     * @param string $sql
     *
     * @throws Exception
     *
     * @return Statement
     */
    public static function prepare(string $sql): Statement
    {
        if (self::$pdo === null) {
            throw new Exception('No Connection Established');
        }
        $sql = str_replace('##', self::$table_prefix, $sql);

        $hash = md5($sql);

        if (!array_key_exists($hash, self::$prepared)) {
            $prepared_statement = self::$pdo->prepare($sql);
            
            if ($prepared_statement instanceof PDOStatement) {
                self::$prepared[$hash] = new Statement($prepared_statement);
            } else {
                throw new PDOException("Unable to prepare statement " . $sql);
            }
        }

        return self::$prepared[$hash];
    }

    /**
     * Run a series of scripts to bring the database schema up to date.
     *
     * @param string $namespace      Where to find our MigrationXXX classes
     * @param string $schema_name    Where to find our MigrationXXX classes
     * @param int    $target_version updade/downgrade to this version
     *
     * @throws PDOException
     *
     * @return bool  Were any updates applied
     */
    public static function updateSchema($namespace, $schema_name, $target_version): bool
    {
        try {
            $current_version = (int)Site::getPreference($schema_name);
        } catch (PDOException $ex) {
            // During initial installation, the site_preference table won’t exist.
            $current_version = 0;
        }

        $updates_applied = false;

        // Update the schema, one version at a time.
        while ($current_version < $target_version) {
            $class = $namespace . '\\Migration' . $current_version;
            /** @var MigrationInterface $migration */
            $migration = new $class();
            $migration->upgrade();
            $current_version++;
            Site::setPreference($schema_name, (string) $current_version);
            $updates_applied = true;
        }

        return $updates_applied;
    }

    /**
     * Escape a string for use in a SQL "LIKE" clause
     *
     * @param string $string
     *
     * @return string
     */
    public static function escapeLike($string): string
    {
        return strtr(
            $string,
            [
                '\\' => '\\\\',
                '%'  => '\%',
                '_'  => '\_',
            ]
        );
    }
}
