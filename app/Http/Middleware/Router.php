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

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Dispatcher;
use Fisharebest\Webtrees\Http\Routing\ControllerDispatcher;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\RouteMatcher;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;
use function str_contains;

readonly class Router implements MiddlewareInterface
{
    public function __construct(
        private ModuleService $module_service,
        private RouteCollection $route_collection,
        private TreeService $tree_service,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Ugly URLs store the path in a query parameter.
        $url_route = Validator::queryParams($request)->string('route', '');

        if (Validator::attributes($request)->boolean('rewrite_urls', false)) {
            // We are creating pretty URLs, but received an ugly one. Probably a search-engine. Redirect it.
            if ($url_route !== '') {
                $uri = $request->getUri()
                    ->withPath($url_route)
                    ->withQuery(explode('&', $request->getUri()->getQuery(), 2)[1] ?? '');

                return Registry::responseFactory()
                    ->redirectUrl($uri, HttpStatusCode::PermanentRedirect)
                    ->withHeader('Link', '<' . $uri . '>; rel="canonical"');
            }

            $pretty = $request;
        } else {
            // Turn the ugly URL into a pretty one, so the router can parse it.
            $uri    = $request->getUri()->withPath($url_route);
            $pretty = $request->withUri($uri);
        }

        // Match the request to a route.
        $matcher = new RouteMatcher($this->route_collection);
        $result  = $matcher->match($pretty);

        // No route matched?
        if (!$result->isSuccess()) {
            return $handler->handle($request);
        }

        $route = $result->route;

        // Add the route as attribute of the request
        $request = $request->withAttribute('route', $route);

        $route_middleware = $route->middleware;

        $module_middleware = $this->module_service->findByInterface(MiddlewareInterface::class)->all();

        $middleware = [
            ...$route_middleware,
            CheckCsrf::class,
            ...$module_middleware,
            ControllerDispatcher::class,
        ];

        // Add the matched attributes to the request.
        foreach ($result->attributes as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        // Legacy code expects the tree attribute to be a Tree object.
        $tree    = $request->getAttribute('tree');
        $tree    = $this->tree_service->all()->get($tree);
        $request = $request->withAttribute('tree', $tree);

        // Some old code expects to find these in the container.
        if ($tree instanceof Tree) {
            Registry::container()->set(Tree::class, $tree);
        }
        Registry::container()->set(ServerRequestInterface::class, $request);

        return Dispatcher::dispatch(middleware: $middleware, request: $request);
    }
}
