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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
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
        DB::connect(
            driver: Validator::attributes($request)->string('dbtype', DB::MYSQL),
            host: Validator::attributes($request)->string('dbhost'),
            port: Validator::attributes($request)->string('dbport'),
            database: Validator::attributes($request)->string('dbname'),
            username: Validator::attributes($request)->string('dbuser'),
            password: Validator::attributes($request)->string('dbpass'),
            prefix: Validator::attributes($request)->string('tblpfx'),
            key: Validator::attributes($request)->string('dbkey', ''),
            certificate: Validator::attributes($request)->string('dbcert', ''),
            ca: Validator::attributes($request)->string('dbca', ''),
            verify_certificate: Validator::attributes($request)->boolean('dbverify', false),
        );

        return $handler->handle($request);
    }
}
