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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Services\ServerCheckService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;

/**
 * Check the server is up.
 */
class Ping implements RequestHandlerInterface
{
    private ServerCheckService $server_check_service;

    /**
     * @param ServerCheckService $server_check_service
     */
    public function __construct(ServerCheckService $server_check_service)
    {
        $this->server_check_service = $server_check_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->server_check_service->serverErrors()->isNotEmpty()) {
            return response('ERROR');
        }

        if ($this->server_check_service->serverWarnings()->isNotEmpty()) {
            return response('WARNING');
        }

        return response('OK');
    }
}
