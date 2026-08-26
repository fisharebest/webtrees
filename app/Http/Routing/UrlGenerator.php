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

use function array_filter;
use function array_map;
use function http_build_query;
use function is_object;
use function preg_replace_callback;

/**
 * Generates a URL from a route name and parameters.
 * Works with all routes — both dispatchable and generation-only.
 */
final readonly class UrlGenerator
{
    public function __construct(
        private RouteCollection $routes,
        private string $base_path = '',
        private ParameterResolverInterface|null $parameter_resolver = null,
    ) {
    }

    /**
     * Generate a URL for the given route name.
     *
     * Parameters matching {param} tokens are interpolated into the path.
     * Any remaining parameters are appended as a query string.
     * Optional {/param} segments are omitted when the value is null/empty.
     *
     * @param array<string, mixed> $parameters
     */
    public function generate(string $name, array $parameters = []): string
    {
        $route = $this->routes->get($name);
        $path  = $route->url;

        // Resolve complex types to scalar values
        $parameters = array_map($this->resolveValue(...), $parameters);

        // Track which parameters are consumed by the path
        $consumed = [];

        // Replace optional parameters {/param}
        $path = preg_replace_callback(
            '#\{/(\w+)\}#',
            static function (array $matches) use ($parameters, &$consumed): string {
                $param = $matches[1];
                $value = $parameters[$param] ?? null;

                if ($value === null || $value === '') {
                    $consumed[$param] = true;
                    return '';
                }

                $consumed[$param] = true;

                return '/' . rawurlencode((string) $value);
            },
            $path,
        );

        // Replace required parameters {param}
        $path = preg_replace_callback(
            '#\{(\w+)\}#',
            static function (array $matches) use ($parameters, &$consumed): string {
                $param = $matches[1];
                $value = $parameters[$param] ?? '';

                $consumed[$param] = true;

                return rawurlencode((string) $value);
            },
            $path,
        );

        // Remaining parameters become query string
        $remaining = array_filter(
            $parameters,
            static fn (string $key): bool => !isset($consumed[$key]),
            ARRAY_FILTER_USE_KEY,
        );

        $url = $this->base_path . $path;

        if ($remaining !== []) {
            $url .= '?' . http_build_query($remaining);
        }

        return $url;
    }

    /**
     * Resolve a parameter value to a type suitable for URL generation.
     */
    private function resolveValue(mixed $value): mixed
    {
        if ($this->parameter_resolver !== null && is_object($value)) {
            return $this->parameter_resolver->serialize($value);
        }

        return $value;
    }
}
