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

use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function preg_match;
use function preg_replace_callback;
use function rawurldecode;

use const ARRAY_FILTER_USE_KEY;

/**
 * Matches a ServerRequestInterface against the RouteCollection by path only.
 * Only dispatchable routes (where class_exists($route->name)) are considered.
 */
class RouteMatcher
{
    public function __construct(private readonly RouteCollection $routes)
    {
    }

    /**
     * Match the request path against registered routes.
     */
    public function match(ServerRequestInterface $request): MatchResult
    {
        $path = $request->getUri()->getPath();

        foreach ($this->routes->all() as $route) {
            $regex = $this->compile($route->url);

            if (preg_match($regex, $path, $matches)) {
                // Extract only named captures
                $attributes = array_filter(
                    $matches,
                    static fn (string $key): bool => !is_numeric($key),
                    ARRAY_FILTER_USE_KEY,
                );

                // Decode URL-encoded values
                $attributes = array_map(rawurldecode(...), $attributes);

                // Remove empty optional parameters
                $attributes = array_filter($attributes, static fn (string $value): bool => $value !== '');

                // isDispatchable() uses the autoloader, which is relatively slow,
                // so only call it for matched classes.
                if ($route->isDispatchable()) {
                    return MatchResult::success($route, $attributes);
                }
            }
        }

        return MatchResult::notFound();
    }

    /**
     * Compile a route path pattern into a regex.
     *
     * Path syntax:
     * - {param}  — required segment, matches [^/]+
     * - {/param} — optional segment preceded by a slash, matches (?:/(?P<param>[^/]+))?
     */
    private function compile(string $path): string
    {
        $regex = preg_replace_callback(
            '#\{(/?)(\w+)\}#',
            static function (array $matches): string {
                $optional = $matches[1] === '/';
                $name     = $matches[2];

                if ($optional) {
                    return '(?:/(?P<' . $name . '>[^/]+))?';
                }

                return '(?P<' . $name . '>[^/]+)';
            },
            $path,
        );

        return '#^' . $regex . '$#';
    }
}
