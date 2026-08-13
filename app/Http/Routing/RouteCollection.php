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

namespace Fisharebest\Webtrees\Http\Routing;

use InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;

use function sprintf;

/**
 * Stores all registered routes and provides the registration API.
 */
class RouteCollection
{
    /** @var array<string, Route> name => Route */
    private array $routes = [];

    // State for the current group
    private string $url_prefix = '';

    /** @var array<string|MiddlewareInterface> */
    private array $middleware = [];

    /**
     * @param array<string|MiddlewareInterface> $middleware
     */
    public function add(string $url, string $controller, array $middleware = []): void
    {
        $this->routes[$controller] = new Route(
            url: $this->url_prefix . $url,
            controller: $controller,
            middleware: [...$this->middleware, ...$middleware],
        );
    }

    /**
     * Define a group of routes sharing a common prefix and middleware.
     *
     * @param string        $prefix     URI prefix for all routes in the group.
     * @param array<string> $middleware Middleware class names to apply to all routes in the group.
     * @param callable      $callback   Receives this RouteCollection to register routes within the group.
     */
    public function group(string $prefix, array $middleware, callable $callback): void
    {
        // Save current state
        $previous_prefix     = $this->url_prefix;
        $previous_middleware = $this->middleware;

        // Apply group state
        $this->url_prefix = $previous_prefix . $prefix;
        $this->middleware = [...$previous_middleware, ...$middleware];

        // Execute the group callback
        $callback($this);

        // Restore previous state
        $this->url_prefix = $previous_prefix;
        $this->middleware = $previous_middleware;
    }

    /**
     * Get a route by name.
     *
     * @throws InvalidArgumentException if the route does not exist.
     */
    public function get(string $name): Route
    {
        return $this->routes[$name] ?? throw new InvalidArgumentException(
            sprintf('Route "%s" not found.', $name)
        );
    }

    /**
     * Get all registered routes.
     *
     * @return array<string, Route>
     */
    public function all(): array
    {
        return $this->routes;
    }

    /**
     * Check if a route with the given name exists.
     */
    public function has(string $name): bool
    {
        return isset($this->routes[$name]);
    }
}
