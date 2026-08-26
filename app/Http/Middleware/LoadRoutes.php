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
use Fisharebest\Webtrees\Http\Routing\GedcomRecordParameterResolver;
use Fisharebest\Webtrees\Http\Routing\ParameterResolverInterface;
use Fisharebest\Webtrees\Http\Routing\ParameterResolver;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\ScalarParameterResolver;
use Fisharebest\Webtrees\Http\Routing\TreeParameterResolver;
use Fisharebest\Webtrees\Http\Routing\UrlGenerator;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
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

        // Build the parameter resolver aggregate with all known resolvers.
        $tree_service = Registry::container()->get(TreeService::class);
        $parameter_resolver = new ParameterResolver([
            new TreeParameterResolver($tree_service),
            new GedcomRecordParameterResolver(),
            new ScalarParameterResolver(),
        ]);

        // Save the route collection, parameter resolver, and URL generator in the container.
        Registry::container()->set(RouteCollection::class, $routes);
        Registry::container()->set(ParameterResolverInterface::class, $parameter_resolver);
        Registry::container()->set(UrlGenerator::class, new UrlGenerator($routes, $base_path, $parameter_resolver));

        return $handler->handle($request);
    }
}
