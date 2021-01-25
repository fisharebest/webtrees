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

namespace Fisharebest\Webtrees\Http\Middleware;

use Aura\Router\RouterContainer;
use Fisharebest\Webtrees\Http\Routes\ApiRoutes;
use Fisharebest\Webtrees\Http\Routes\WebRoutes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function parse_url;

use const PHP_URL_PATH;

/**
 * Load the routing table.
 */
class LoadRoutes implements MiddlewareInterface
{
    /** @var ApiRoutes */
    private $api_routes;

    /** @var WebRoutes */
    private $web_routes;

    /**
     * @param ApiRoutes $api_routes
     * @param WebRoutes $web_routes
     */
    public function __construct(ApiRoutes $api_routes, WebRoutes $web_routes)
    {
        $this->api_routes = $api_routes;
        $this->web_routes = $web_routes;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $base_url         = $request->getAttribute('base_url');
        $base_path        = parse_url($base_url, PHP_URL_PATH);
        $router_container = new RouterContainer($base_path);

        // Load the core routing tables. Modules will load their own routes later.
        $map = $router_container->getMap();
        $this->api_routes->load($map);
        $this->web_routes->load($map);

        // Save the router in the container, as we'll need it to generate URLs.
        app()->instance(RouterContainer::class, $router_container);

        return $handler->handle($request);
    }
}
