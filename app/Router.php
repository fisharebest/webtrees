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

namespace Fisharebest\Webtrees;

use Fig\Http\Message\RequestMethodInterface;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function explode;

/**
 * Simple class to help migrate to a third-party routing library.
 */
class Router implements MiddlewareInterface, RequestMethodInterface
{
    private const CONTROLLER_NAMESPACE = __NAMESPACE__ . '\\Http\\Controllers\\';

    // To parse Controller::action
    private const SCOPE_OPERATOR = '::';

    /** @var string[][] */
    private $routes = [
        self::METHOD_GET  => [],
        self::METHOD_POST => [],
    ];

    /**
     * @param string $path
     * @param string $handler
     *
     * @return Router
     */
    public function get(string $path, string $handler): Router
    {
        return $this->add(self::METHOD_GET, $path, $handler);
    }

    /**
     * @param string $method
     * @param string $path
     * @param string $handler
     *
     * @return Router
     */
    private function add(string $method, string $path, string $handler): Router
    {
        $this->routes[$method][$path] = $handler;

        return $this;
    }

    /**
     * @param string $path
     * @param string $handler
     *
     * @return Router
     */
    public function post(string $path, string $handler): Router
    {
        return $this->add(self::METHOD_POST, $path, $handler);
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        app()->instance(self::class, $this);
        require 'routes/web.php';

        $method  = $request->getMethod();
        $route   = $request->getQueryParams()['route'] ?? '';
        $routing = $this->routes[$method][$route] ?? '';

        // Bind the request into the container
        app()->instance(ServerRequestInterface::class, $request);

        // No route matched?
        if ($routing === '') {
            return $handler->handle($request);
        }

        // Routes defined using controller::action
        if (Str::contains($routing, self::SCOPE_OPERATOR)) {
            [$class, $method] = explode(self::SCOPE_OPERATOR, $routing);

            return app(self::CONTROLLER_NAMESPACE . $class)->$method($request);
        }

        // Routes defined using a request handler
        return app($routing)->handle($request);
    }
}
