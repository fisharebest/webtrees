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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to connect to the database.
 */
class UseDatabase implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Earlier versions of webtrees did not have a dbtype config option.  They always used mysql.
        $driver = $request->getAttribute('dbtype', 'mysql');

        $dbname = $request->getAttribute('dbname');

        if ($driver === 'sqlite') {
            $dbname = Webtrees::ROOT_DIR . 'data/' . $dbname . '.sqlite';
        }

        $capsule = new DB();

        $capsule->addConnection([
            'driver'                  => $driver,
            'host'                    => $request->getAttribute('dbhost'),
            'port'                    => $request->getAttribute('dbport'),
            'database'                => $dbname,
            'username'                => $request->getAttribute('dbuser'),
            'password'                => $request->getAttribute('dbpass'),
            'prefix'                  => $request->getAttribute('tblpfx'),
            'prefix_indexes'          => true,
            // For MySQL
            'charset'                 => 'utf8',
            'collation'               => 'utf8_unicode_ci',
            'timezone'                => '+00:00',
            'engine'                  => 'InnoDB',
            'modes'                   => [
                'ANSI',
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
            ],
            // For SQLite
            'foreign_key_constraints' => true,
        ]);
        
        $capsule->setAsGlobal();

        Builder::macro('whereContains', function ($column, string $search, string $boolean = 'and'): Builder {
            $search = strtr($search, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);

            return $this->where($column, 'LIKE', '%' . $search . '%', $boolean);
        });

        return $handler->handle($request);
    }
}
