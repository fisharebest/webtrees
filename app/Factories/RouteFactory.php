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

namespace Fisharebest\Webtrees\Factories;

use Fisharebest\Webtrees\Contracts\RouteFactoryInterface;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Http\Routing\RouteCollection;
use Fisharebest\Webtrees\Http\Routing\UrlGenerator;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_map;
use function is_bool;
use function parse_url;
use function str_contains;
use function strlen;
use function substr;

use const ARRAY_FILTER_USE_KEY;
use const PHP_URL_PATH;

/**
 * Make a URL for a route.
 */
class RouteFactory implements RouteFactoryInterface
{
    /**
     * Generate a URL for a named route.
     *
     * @param array<bool|int|string|array<string>|null> $parameters
     */
    public function route(string $route_name, array $parameters = []): string
    {
        $request  = Registry::container()->get(ServerRequestInterface::class);
        $base_url = Validator::attributes($request)->string('base_url');

        // Map booleans to integers for URL generation.
        $parameters = array_map(static fn ($var) => is_bool($var) ? (int) $var : $var, $parameters);

        $url_generator = Registry::container()->get(UrlGenerator::class);
        $route         = $this->routeMap()->get($route_name);

        if (Validator::attributes($request)->boolean('rewrite_urls', false)) {
            // Generate the pretty URL (includes query string for non-path parameters).
            $url = $url_generator->generate($route_name, $parameters);

            // Make it absolute.
            $base_path = parse_url($base_url, PHP_URL_PATH);
            $base_path = is_string($base_path) ? $base_path : '';

            return $base_url . substr($url, strlen($base_path));
        }

        // Ugly URLs: generate path, then wrap in index.php?route=...
        $url = $url_generator->generate($route_name, $parameters);

        // Extract path portion only (without query string that UrlGenerator may add)
        $path = parse_url($url, PHP_URL_PATH);

        // All parameters that weren't consumed by the path become query params
        $parameters = array_filter($parameters, static fn (string $key): bool => !str_contains($route->url, '{' . $key . '}') && !str_contains($route->url, '{/' . $key . '}'), ARRAY_FILTER_USE_KEY);
        $parameters = ['route' => $path] + $parameters;
        $url        = $base_url . '/index.php';

        return Html::url($url, $parameters);
    }

    /**
     * Get the route collection.
     */
    public function routeMap(): RouteCollection
    {
        return Registry::container()->get(RouteCollection::class);
    }
}
