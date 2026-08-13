<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fisharebest\Webtrees\Http\Routes\ApiRoutes;
use Fisharebest\Webtrees\Http\Routes\WebRoutes;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\UrlGenerator;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function parse_url;

use const PHP_URL_PATH;

class LoadRoutes implements MiddlewareInterface
{
    public function __construct(
        private ApiRoutes $api_routes,
        private WebRoutes $web_routes,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $base_url  = Validator::attributes($request)->string('base_url');
        $base_path = parse_url($base_url, PHP_URL_PATH);
        $base_path = is_string($base_path) ? $base_path : '';

        // Load the core routing tables. Modules will load their own routes later.
        $routes = new RouteCollection();
        $this->api_routes->load($routes);
        $this->web_routes->load($routes);

        // Save the route collection and URL generator in the container.
        Registry::container()->set(RouteCollection::class, $routes);
        Registry::container()->set(UrlGenerator::class, new UrlGenerator($routes, $base_path));

        return $handler->handle($request);
    }
}
