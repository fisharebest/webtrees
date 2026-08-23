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

namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use NoDiscard;
use Psr\Http\Message\ServerRequestInterface;

use function in_array;
use function preg_match;
use function str_starts_with;

final class Validate
{
    #[NoDiscard('No validation occurs unless you use the return value.')]
    public function isLocalUrl(string $url): bool
    {
        $base_url = Registry::container()->get(ServerRequestInterface::class)
            ->getAttribute('base_url');

        return $url !== '' && str_starts_with($url, $base_url);
    }

    #[NoDiscard('No validation occurs unless you use the return value.')]
    public function isLoggedIn(): bool
    {
        return Auth::check();
    }

    #[NoDiscard('No validation occurs unless you use the return value.')]
    public function isNotEmpty(string $value): bool
    {
        return $value !== '';
    }

    #[NoDiscard('No validation occurs unless you use the return value.')]
    public function isXref(string $value): bool
    {
        return $value === ''  || preg_match('/^' . Gedcom::REGEX_XREF . '$/', $value) === 1;
    }


    public function localUrl(string $value, string $name): void
    {
        if (!$this->isLocalUrl($value)) {
            throw new HttpBadRequestException(sprintf('The parameter “%s” is invalid.', $name));
        }
    }

    public function notEmpty(string $value, string $name): void
    {
        if (!$this->isNotEmpty($value)) {
            throw new HttpBadRequestException(sprintf('The parameter “%s” is invalid.', $name));
        }
    }

    public function xref(string $value, string $name): void
    {
        if (!$this->isXref($value)) {
            throw new HttpBadRequestException(sprintf('The parameter “%s” is invalid.', $name));
        }
    }

    /**
     * Validate a GEDCOM tag.
     * Returns the value if valid, or null otherwise.
     */
    public static function tag(string $value): string|null
    {
        if (preg_match('/^' . Gedcom::REGEX_TAG . '$/', $value) === 1) {
            return $value;
        }

        return null;
    }

    /**
     * Check whether a value is a valid GEDCOM tag.
     */
    public static function isTag(string $value): bool
    {
        return self::tag($value) !== null;
    }

    /**
     * Validate that an integer is within a range.
     * Returns the value if in range, or null otherwise.
     */
    public static function between(int $value, int $minimum, int $maximum): int|null
    {
        if ($value >= $minimum && $value <= $maximum) {
            return $value;
        }

        return null;
    }

    /**
     * Check whether an integer is within a range.
     */
    public static function isBetween(int $value, int $minimum, int $maximum): bool
    {
        return $value >= $minimum && $value <= $maximum;
    }

    /**
     * Validate that a value is in an array.
     * Returns the value if found, or null otherwise.
     *
     * @param array<int|string,int|string> $values
     */
    public static function inArray(int|string $value, array $values): int|string|null
    {
        return in_array($value, $values, true) ? $value : null;
    }

    /**
     * Check whether a value is in an array.
     *
     * @param array<int|string,int|string> $values
     */
    public static function isInArray(int|string $value, array $values): bool
    {
        return in_array($value, $values, true);
    }

    /**
     * Validate that a value is a key in an array.
     * Returns the value if found, or null otherwise.
     *
     * @param array<int|string,mixed> $values
     */
    public static function inArrayKeys(int|string $value, array $values): int|string|null
    {
        return self::inArray($value, array_keys($values));
    }

    /**
     * Check whether a value is a key in an array.
     *
     * @param array<int|string,mixed> $values
     */
    public static function isInArrayKeys(int|string $value, array $values): bool
    {
        return self::isInArray($value, array_keys($values));
    }
}
