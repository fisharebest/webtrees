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

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;

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
     */
    public static function connect(array $config): void
    {
        if ($config['dbtype'] === 'sqlite') {
            $config['dbname'] = WT_ROOT . 'data/' . $config['dbname'] . '.sqlite';
        }

        $capsule = new DB();
        $capsule->addConnection([
            'driver'                  => $config['dbtype'],
            'host'                    => $config['dbhost'],
            'port'                    => $config['dbport'],
            'database'                => $config['dbname'],
            'username'                => $config['dbuser'],
            'password'                => $config['dbpass'],
            'prefix'                  => $config['tblpfx'],
            'prefix_indexes'          => true,
            'charset'                 => 'utf8',
            'collation'               => 'utf8_unicode_ci',
            'engine'                  => 'InnoDB',
            'foreign_key_constraints' => true,
            'modes'                   => [
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
}
