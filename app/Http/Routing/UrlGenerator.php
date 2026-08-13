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

use BackedEnum;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Tree;

use function array_filter;
use function array_map;
use function http_build_query;
use function preg_replace_callback;

/**
 * Generates a URL from a route name and parameters.
 * Works with all routes — both dispatchable and generation-only.
 */
class UrlGenerator
{
    public function __construct(
        private readonly RouteCollection $routes,
        private readonly string $basePath = '',
    ) {
    }

    /**
     * Generate a URL for the given route name.
     *
     * Parameters matching {param} tokens are interpolated into the path.
     * Remaining parameters are appended as a query string.
     * Optional {/param} segments are omitted when the value is null/empty.
     *
     * @param array<string, BackedEnum|bool|int|string|array<string>|Tree|GedcomRecord|null> $parameters
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

        $url = $this->basePath . $path;

        if ($remaining !== []) {
            $url .= '?' . http_build_query($remaining);
        }

        return $url;
    }

    /**
     * Resolve a parameter value to a type suitable for URL generation.
     * @param BackedEnum|bool|int|string|array<string>|Tree|GedcomRecord|null $value
     * @return bool|int|string|array<string>|null
     */
    private function resolveValue(BackedEnum|bool|int|string|array|Tree|GedcomRecord|null $value): bool|int|string|array|null
    {
        if ($value instanceof Tree) {
            return $value->name();
        }

        if ($value instanceof GedcomRecord) {
            return $value->xref();
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}
