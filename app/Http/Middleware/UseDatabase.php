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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Illuminate\Database\Capsule\Manager as DB;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

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
        $driver = Validator::attributes($request)->string('dbtype', 'mysql');

        $dbname = Validator::attributes($request)->string('dbname');

        if ($driver === 'sqlite') {
            $dbname = Webtrees::ROOT_DIR . 'data/' . $dbname . '.sqlite';
        }

        $capsule = new DB();

        // Newer versions of webtrees support utf8mb4.  Older ones only support 3-byte utf8
        if ($driver === 'mysql' && Validator::attributes($request)->boolean('mysql_utf8mb4', false)) {
            $charset   = 'utf8mb4';
            $collation = 'utf8mb4_unicode_ci';
        } else {
            $charset   = 'utf8';
            $collation = 'utf8_unicode_ci';
        }

        $options = [
            // Some drivers do this and some don't.  Make them consistent.
            PDO::ATTR_STRINGIFY_FETCHES => true,
        ];

        $dbkey    = Validator::attributes($request)->string('dbkey', '');
        $dbcert   = Validator::attributes($request)->string('dbcert', '');
        $dbca     = Validator::attributes($request)->string('dbca', '');
        $dbverify = Validator::attributes($request)->boolean('dbverify', false);

        // MySQL/MariaDB support encrypted connections
        if ($dbkey !== '' && $dbcert !== '' && $dbca !== '') {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = $dbverify;
            $options[PDO::MYSQL_ATTR_SSL_KEY]                = Webtrees::ROOT_DIR . 'data/' . $dbkey;
            $options[PDO::MYSQL_ATTR_SSL_CERT]               = Webtrees::ROOT_DIR . 'data/' . $dbcert;
            $options[PDO::MYSQL_ATTR_SSL_CA]                 = Webtrees::ROOT_DIR . 'data/' . $dbca;
        }

        $capsule->addConnection([
            'driver'                  => $driver,
            'host'                    => Validator::attributes($request)->string('dbhost'),
            'port'                    => Validator::attributes($request)->string('dbport'),
            'database'                => $dbname,
            'username'                => Validator::attributes($request)->string('dbuser'),
            'password'                => Validator::attributes($request)->string('dbpass'),
            'prefix'                  => Validator::attributes($request)->string('tblpfx'),
            'prefix_indexes'          => true,
            'options'                 => $options,
            // For MySQL
            'charset'                 => $charset,
            'collation'               => $collation,
            'timezone'                => '+00:00',
            'engine'                  => 'InnoDB',
            'modes'                   => [
                'ANSI',
                'STRICT_ALL_TABLES',
                // Use SQL injection(!) to override MAX_JOIN_SIZE and GROUP_CONCAT_MAX_LEN settings.
                "', SQL_BIG_SELECTS=1, GROUP_CONCAT_MAX_LEN=1048576, @foobar='"
            ],
            // For SQLite
            'foreign_key_constraints' => true,
        ]);

        $capsule->setAsGlobal();

        if ($driver === 'sqlsrv') {
            DB::connection()->unprepared('SET language us_english'); // For timestamp columns
        }

        try {
            // Eager-load the connection, to prevent database credentials appearing in error logs.
            DB::connection()->getPdo();
        } catch (PDOException $exception) {
            throw new RuntimeException($exception->getMessage());
        }

        return $handler->handle($request);
    }
}
