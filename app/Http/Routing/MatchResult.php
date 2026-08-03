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

/**
 * Value object representing the result of a route match attempt.
 */
readonly class MatchResult
{
    /**
     * @param Route|null          $route         The matched route, or null on failure.
     * @param array<string,mixed> $attributes    Extracted URL parameters.
     * @param string|null         $failureReason null on success, 'not_found' on failure.
     */
    private function __construct(
        public Route|null $route,
        public array $attributes,
        public string|null $failureReason,
    ) {
    }

    /**
     * Create a successful match result.
     *
     * @param array<string,mixed> $attributes
     */
    public static function success(Route $route, array $attributes): self
    {
        return new self(route: $route, attributes: $attributes, failureReason: null);
    }

    /**
     * Create a failed match result.
     */
    public static function notFound(): self
    {
        return new self(route: null, attributes: [], failureReason: 'not_found');
    }

    /**
     * Did the match succeed?
     */
    public function isSuccess(): bool
    {
        return $this->failureReason === null;
    }
}
