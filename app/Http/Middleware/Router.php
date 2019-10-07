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

use Aura\Router\RouterContainer;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use Middleland\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function app;
use function array_map;
use function parse_url;
use const PHP_URL_PATH;

/**
 * Simple class to help migrate to a third-party routing library.
 */
class Router implements MiddlewareInterface, RequestMethodInterface
{
    /** @var ModuleService */
    private $module_service;

    /**
     * Router constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
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
        $base_path        = parse_url($base_url, PHP_URL_PATH) ?? '';
        $router_container = new RouterContainer($base_path);

        // Save the router in the container, as we'll need it to generate URLs.
        app()->instance(RouterContainer::class, $router_container);

        // Load the routing table.
        require __DIR__ . '/../../../routes/web.php';

        if ($request->getAttribute('rewrite_urls') !== '1') {
            // Turn the ugly URL into a pretty one.
            $params = $request->getQueryParams();
            $route   = $params['route'] ?? '';
            unset($params['route']);
            $uri     = $request->getUri()->withPath($route);
            $request = $request->withUri($uri)->withQueryParams($params);
        }

        // Bind the request into the container and the layout
        app()->instance(ServerRequestInterface::class, $request);
        View::share('request', $request);

        // Match the request to a route.
        $route = $router_container->getMatcher()->match($request);

        // No route matched?
        if ($route === false) {
            return $handler->handle($request);
        }

        // Firstly, apply the route middleware
        $route_middleware = $route->extras['middleware'] ?? [];
        $route_middleware = array_map('app', $route_middleware);

        // Secondly, apply any module middleware
        $module_middleware = $this->module_service->findByInterface(MiddlewareInterface::class)->all();

        // Add the route as attribute of the request
        $request = $request->withAttribute('route', $route->name);

        // Finally, run the handler using middleware
        $handler_middleware = [new WrapHandler($route->handler)];

        $middleware = array_merge($route_middleware, $module_middleware, $handler_middleware);

        // Add the matched attributes to the request.
        foreach ($route->attributes as $key => $value) {
            if ($key === 'tree') {
                $value = Tree::findByName($value);
            }
            $request = $request->withAttribute($key, $value);
        }

        $dispatcher = new Dispatcher($middleware, app());

        return $dispatcher->dispatch($request);
    }
}
