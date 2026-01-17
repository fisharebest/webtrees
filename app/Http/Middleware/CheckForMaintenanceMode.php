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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Services\MaintenanceModeService;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to check whether the site is offline.
 */
readonly class CheckForMaintenanceMode implements MiddlewareInterface, StatusCodeInterface
{
    public function __construct(
        private MaintenanceModeService $maintenance_mode_service,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->maintenance_mode_service->isOffline()) {
            $html = view('layouts/offline', [
                'message' => $this->maintenance_mode_service->message(),
                'url'     => (string) $request->getUri(),
            ]);

            return response($html, StatusCodeInterface::STATUS_SERVICE_UNAVAILABLE);
        }

        return $handler->handle($request);
    }
}
