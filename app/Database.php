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
            'modes'          => [
                'ANSI',
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
            ],
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
        Builder::macro('whereContains', function ($column, string $search, string $boolean = 'and') {
            $search = strtr($search, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);

            return $this->where($column, 'LIKE', '%' . $search . '%', $boolean);
        });
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
}
