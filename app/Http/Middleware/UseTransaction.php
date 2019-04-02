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

use Closure;
use Illuminate\Database\Capsule\Manager as DB;
use Fisharebest\Webtrees\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

/**
 * Middleware to wrap a request in a transaction.
 */
class UseTransaction implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param Closure                $next
     *
     * @return Response
     * @throws Throwable
     */
    public function handle(ServerRequestInterface $request, Closure $next): ResponseInterface
    {
        try {
            DB::connection()->beginTransaction();

            $response = $next($request);

            DB::connection()->commit();

            return $response;
        } catch (Throwable $exception) {
            DB::connection()->rollBack();

            throw $exception;
        }
    }
}
