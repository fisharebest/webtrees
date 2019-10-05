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

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Webtrees;
use Middleland\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function array_map;

/**
 * Simple class to help migrate to a third-party routing library.
 */
class Router implements MiddlewareInterface, RequestMethodInterface
{
    /** @var string[][] */
    private $routes = [
        self::METHOD_GET  => [],
        self::METHOD_POST => [],
    ];

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
     * @param string $route
     * @param string $url
     * @param string $handler
     *
     * @return Router
     */
    public function get(string $route, string $url, string $handler): Router
    {
        return $this->add(self::METHOD_GET, $route, $handler);
    }

    /**
     * @param string $method
     * @param string $route
     * @param string $handler
     *
     * @return Router
     */
    private function add(string $method, string $route, string $handler): Router
    {
        $this->routes[$method][$route] = $handler;

        return $this;
    }

    /**
     * @param string $route
     * @param string $url
     * @param string $handler
     *
     * @return Router
     */
    public function post(string $route, string $url, string $handler): Router
    {
        return $this->add(self::METHOD_POST, $route, $handler);
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Save the router in the container, as we'll need it to generate URLs.
        app()->instance(self::class, $this);

        // Load the routing table.
        require Webtrees::ROOT_DIR . 'routes/web.php';

        // Match the request to a route.
        $method  = $request->getMethod();
        $route   = $request->getQueryParams()['route'] ?? '';
        $routing = $this->routes[$method][$route] ?? '';

        // Bind the request into the container
        app()->instance(ServerRequestInterface::class, $request);

        // No route matched?
        if ($routing === '') {
            return $handler->handle($request);
        }

        // Firstly, apply the route middleware
        $route_middleware = [];
        $route_middleware = array_map('app', $route_middleware);

        // Secondly, apply any module middleware
        $module_middleware = $this->module_service->findByInterface(MiddlewareInterface::class)->all();

        // Finally, run the handler using middleware
        $handler_middleware = [new WrapHandler($routing)];

        $middleware = array_merge($route_middleware, $module_middleware, $handler_middleware);

        $dispatcher = new Dispatcher($middleware, app());

        return $dispatcher->dispatch($request);
    }
}
