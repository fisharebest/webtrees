<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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

use Closure;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use LogicException;
use Psr\Http\Message\ServerRequestInterface;

use function array_reduce;
use function ctype_digit;
use function is_array;
use function is_int;
use function is_string;
use function parse_url;
use function preg_match;
use function str_starts_with;

/**
 * Validate a parameter from an HTTP request
 */
class Validator
{
    /** @var array<string|array<string>> */
    private array $parameters;

    /** @var array<Closure> */
    private array $rules = [];

    /**
     * @param array<string|array<string>> $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function parsedBody(ServerRequestInterface $request): self
    {
        return new self((array) $request->getParsedBody());
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function queryParams(ServerRequestInterface $request): self
    {
        return new self($request->getQueryParams());
    }

    /**
     * @param int $minimum
     * @param int $maximum
     *
     * @return self
     */
    public function isBetween(int $minimum, int $maximum): self
    {
        $this->rules[] = static function (?int $value) use ($minimum, $maximum): ?int {
            if (is_int($value) && $value >= $minimum && $value <= $maximum) {
                return $value;
            }

            return null;
        };

        return $this;
    }

    /**
     * @param string $base_url
     *
     * @return $this
     */
    public function isLocalUrl(string $base_url): self
    {
        $this->rules[] = static function (?string $value) use ($base_url): ?string {
            if (is_string($value)) {
                $value_info    = parse_url($value);
                $base_url_info = parse_url($base_url);

                if (!is_array($base_url_info)) {
                    throw new LogicException(__METHOD__ . ' needs a valid URL');
                }

                if (is_array($value_info)) {
                    $scheme_ok = ($value_info['scheme'] ?? 'http') === ($base_url_info['scheme'] ?? 'http');
                    $host_ok   = ($value_info['host'] ?? '') === ($base_url_info['host'] ?? '');
                    $port_ok   = ($value_info['port'] ?? '') === ($base_url_info['port'] ?? '');
                    $user_ok   = ($value_info['user'] ?? '') === ($base_url_info['user'] ?? '');
                    $path_ok   = str_starts_with($value_info['path'] ?? '/', $base_url_info['path'] ?? '/');

                    if ($scheme_ok && $host_ok && $port_ok && $user_ok && $path_ok) {
                        return $value;
                    }
                }
            }

            return null;
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function isXref(): self
    {
        $this->rules[] = static function (?string $value): ?string {
            if (is_string($value) && preg_match('/^' . Gedcom::REGEX_XREF . '$/', $value) === 1) {
                return $value;
            }

            return null;
        };

        return $this;
    }

    /**
     * @param string $parameter
     *
     * @return array<string>|null
     */
    public function array(string $parameter): ?array
    {
        $value = $this->parameters[$parameter] ?? null;

        if (!is_array($value)) {
            $value = null;
        }

        $callback = static fn (?array $value, Closure $rule): ?array => $rule($value);

        return array_reduce($this->rules, $callback, $value);
    }

    /**
     * @param string $parameter
     *
     * @return int|null
     */
    public function integer(string $parameter): ?int
    {
        $value = $this->parameters[$parameter] ?? null;

        if (is_string($value) && ctype_digit($value)) {
            $value = (int) $value;
        } else {
            $value = null;
        }

        $callback = static fn (?int $value, Closure $rule): ?int => $rule($value);

        return array_reduce($this->rules, $callback, $value);
    }

    /**
     * @param string $parameter
     *
     * @return string|null
     */
    public function string(string $parameter): ?string
    {
        $value = $this->parameters[$parameter] ?? null;

        if (!is_string($value)) {
            $value = null;
        }

        $callback = static fn (?string $value, Closure $rule): ?string => $rule($value);

        return array_reduce($this->rules, $callback, $value);
    }

    /**
     * @param string $parameter
     *
     * @return array<string>
     */
    public function requiredArray(string $parameter): array
    {
        $value = $this->array($parameter);

        if ($value === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $value;
    }

    /**
     * @param string $parameter
     *
     * @return int
     */
    public function requiredInteger(string $parameter): int
    {
        $value = $this->integer($parameter);

        if ($value === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $value;
    }

    /**
     * @param string $parameter
     *
     * @return string
     */
    public function requiredString(string $parameter): string
    {
        $value = $this->string($parameter);

        if ($value === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $value;
    }
}
