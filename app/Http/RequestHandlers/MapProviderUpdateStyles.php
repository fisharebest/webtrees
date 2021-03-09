<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Fisharebest\Webtrees\Services\MapProviderService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * get styles for selected map provider.
 */
class MapProviderUpdateStyles implements RequestHandlerInterface
{

    /** @var MapProviderService */
    private $map_provider_service;

    /**
     * Dependency injection.
     *
     * @param MapProviderService $map_provider_service
     */
    public function __construct(MapProviderService $map_provider_service)
    {
        $this->map_provider_service = $map_provider_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data     = (array) $request->getParsedBody();
        $provider = $data['provider'] ?? '';
        $styles   = $this->map_provider_service->styles($provider);

        return response($styles);
    }
}
