<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use PDO;
use PDOException;

/**
 * Extend PHP's native PDO class.
 */
class Database
{
    const PDO_OPTIONS = [
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
     * Begin a transaction.
     *
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::$pdo->beginTransaction();
    }

    /**
     * Commit this transaction.
     *
     * @return bool
     */
    public static function commit(): bool
    {
        return self::$pdo->commit();
    }

    /**
     * Disconnect from the server, so we can connect to another one
     *
     * @return void
     */
    public static function disconnect()
    {
        self::$pdo = null;
    }

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
        if (self::$pdo !== null) {
            throw new Exception('Database::createInstance() can only be called once.');
        }

        self::$table_prefix = $config['tblpfx'];

        $dsn = (substr($config['dbhost'], 0, 1) === '/' ?
            "mysql:unix_socket='{$config['dbhost']};dbname={$config['dbname']}" : "mysql:host={$config['dbhost']};dbname={$config['dbname']};port={$config['dbport']}"
        );

        // Create the underlying PDO object.
        self::$pdo = new PDO($dsn, $config['dbuser'], $config['dbpass'], self::PDO_OPTIONS);

        // Add logging/debugging.
        self::$pdo = DebugBar::initPDO(self::$pdo);

        self::$pdo->exec("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
        self::$pdo->prepare("SET time_zone = :time_zone")->execute(['time_zone' => date('P')]);

        self::$instance = new self();
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
        if (self::$pdo !== null) {
            return self::$instance;
        }

        throw new Exception('createInstance() must be called before getInstance().');
    }

    /**
     * Are we currently connected to a database?
     *
     * @return bool
     */
    public static function isConnected(): bool
    {
        return self::$pdo !== null;
    }

    /**
     * Determine the most recently created value of an AUTO_INCREMENT field.
     *
     * @return string
     */
    public static function lastInsertId(): string
    {
        return self::$pdo->lastInsertId();
    }

    /**
     * Quote a string for embedding in a MySQL statement.
     *
     * The native quote() function does not convert PHP nulls to DB nulls
     *
     * @param  string|null $string
     *
     * @return string
     *
     * @deprecated We should use bind-variables instead.
     */
    public static function quote($string)
    {
        if ($string === null) {
            return 'NULL';
        }

        return self::$pdo->quote($string, PDO::PARAM_STR);
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
            self::$prepared[$hash] = new Statement(self::$pdo->prepare($sql));
        }

        return self::$prepared[$hash];
    }

    /**
     * Roll back this transaction.
     *
     * @return bool
     */
    public static function rollBack(): bool
    {
        return self::$pdo->rollBack();
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
            DebugBar::addThrowable($ex);

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
