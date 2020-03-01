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

use Fisharebest\Webtrees\Exceptions\HttpServerErrorException;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Query\Builder;
use LogicException;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to connect to the database.
 */
class UseDatabase implements MiddlewareInterface
{
    // The following errors are likely to be caused by server issues, not by webtrees.
    private const SERVER_ERRORS = [
        'mysql'  => [1203],
        'pgsql'  => [],
        'sqlite' => [],
        'sqlsvr' => [],
    ];

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
            'options'                 => [
                // Some drivers do this and some don't.  Make them consistent.
                PDO::ATTR_STRINGIFY_FETCHES => true,
            ],
            // For MySQL
            'charset'                 => 'utf8',
            'collation'               => 'utf8_unicode_ci',
            'timezone'                => '+00:00',
            'engine'                  => 'InnoDB',
            'modes'                   => [
                'ANSI',
                'STRICT_ALL_TABLES',
                // Use SQL injection(!) to override MAX_JOIN_SIZE setting.
                "', SQL_BIG_SELECTS=1, @dummy='"
            ],
            // For SQLite
            'foreign_key_constraints' => true,
        ]);
        
        $capsule->setAsGlobal();

        Builder::macro('whereContains', function ($column, string $search, string $boolean = 'and'): Builder {
            // Assertion helps static analysis tools understand where we will be using this closure.
            assert($this instanceof Builder, new LogicException());

            $search = strtr($search, ['\\' => '\\\\', '%' => '\\%', '_' => '\\_', ' ' => '%']);

            return $this->where($column, 'LIKE', '%' . $search . '%', $boolean);
        });

        try {
            return $handler->handle($request);
        } catch (PDOException $exception) {
            if (in_array($exception->errorInfo[1], self::SERVER_ERRORS[$driver], true)) {
                $message = 'A database error occurred.  This is most likely caused by an issue with your server.' . PHP_EOL . PHP_EOL;
                $message .= $exception->getMessage() . PHP_EOL . PHP_EOL;
                $message .= $exception->getFile() . ':' . $exception->getLine();
                throw new HttpServerErrorException($message);
            }

            throw $exception;
        }
    }
}
