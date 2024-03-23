<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

use Aura\Router\Route;
use Closure;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;

use function array_reduce;
use function array_walk_recursive;
use function ctype_digit;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function parse_url;
use function preg_match;
use function str_starts_with;
use function substr;

/**
 * Validate a parameter from an HTTP request
 */
class Validator
{
    /** @var array<int|string|Tree|UserInterface|array<int|string>> */
    private array $parameters;

    private ServerRequestInterface $request;

    /** @var array<Closure> */
    private array $rules = [];

    /**
     * @param array<int|string|Tree|UserInterface|array<int|string>> $parameters
     * @param ServerRequestInterface                                 $request
     * @param string                                                 $encoding
     */
    private function __construct(array $parameters, ServerRequestInterface $request, string $encoding)
    {
        if ($encoding === 'UTF-8') {
            // All keys and values must be valid UTF-8
            $check_utf8 = static function ($value, $key): void {
                if (is_string($key) && preg_match('//u', $key) !== 1) {
                    throw new HttpBadRequestException('Invalid UTF-8 characters in request');
                }
                if (is_string($value) && preg_match('//u', $value) !== 1) {
                    throw new HttpBadRequestException('Invalid UTF-8 characters in request');
                }
            };

            array_walk_recursive($parameters, $check_utf8);
        }

        $this->parameters = $parameters;
        $this->request    = $request;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function attributes(ServerRequestInterface $request): self
    {
        return new self($request->getAttributes(), $request, 'UTF-8');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function parsedBody(ServerRequestInterface $request): self
    {
        return new self((array) $request->getParsedBody(), $request, 'UTF-8');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function queryParams(ServerRequestInterface $request): self
    {
        return new self($request->getQueryParams(), $request, 'UTF-8');
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function serverParams(ServerRequestInterface $request): self
    {
        // Headers should be ASCII.
        // However, we cannot enforce this as some servers add GEOIP headers with non-ASCII placenames.
        return new self($request->getServerParams(), $request, 'ASCII');
    }

    /**
     * @param int $minimum
     * @param int $maximum
     *
     * @return self
     */
    public function isBetween(int $minimum, int $maximum): self
    {
        $this->rules[] = static function (int|null $value) use ($minimum, $maximum): int|null {
            if (is_int($value) && $value >= $minimum && $value <= $maximum) {
                return $value;
            }

            return null;
        };

        return $this;
    }

    /**
     * @param array<int|string,int|string> $values
     *
     * @return self
     */
    public function isInArray(array $values): self
    {
        $this->rules[] = static fn (int|string|null $value): int|string|null => in_array($value, $values, true) ? $value : null;

        return $this;
    }

    /**
     * @param array<int|string,int|string> $values
     *
     * @return self
     */
    public function isInArrayKeys(array $values): self
    {
        return $this->isInArray(array_keys($values));
    }

    /**
     * @return self
     */
    public function isNotEmpty(): self
    {
        $this->rules[] = static fn (string|null $value): string|null => $value !== null && $value !== '' ? $value : null;

        return $this;
    }

    /**
     * @return self
     */
    public function isLocalUrl(): self
    {
        $base_url = $this->request->getAttribute('base_url', '');

        $this->rules[] = static function (string|null $value) use ($base_url): string|null {
            if ($value !== null) {
                $value_info    = parse_url($value);
                $base_url_info = parse_url($base_url);

                if (is_array($value_info) && is_array($base_url_info)) {
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
     * @return self
     */
    public function isTag(): self
    {
        $this->rules[] = static function (string|null $value): string|null {
            if ($value !== null && preg_match('/^' . Gedcom::REGEX_TAG . '$/', $value) === 1) {
                return $value;
            }

            return null;
        };

        return $this;
    }

    /**
     * @return self
     */
    public function isXref(): self
    {
        $this->rules[] = static function ($value) {
            if (is_string($value) && preg_match('/^' . Gedcom::REGEX_XREF . '$/', $value) === 1) {
                return $value;
            }

            if (is_array($value)) {
                foreach ($value as $v) {
                    if (!is_string($v) || preg_match('/^' . Gedcom::REGEX_XREF . '$/', $v) !== 1) {
                        return null;
                    }
                }

                return $value;
            }

            return null;
        };

        return $this;
    }

    /**
     * @param string    $parameter
     * @param bool|null $default
     *
     * @return bool
     */
    public function boolean(string $parameter, bool|null $default = null): bool
    {
        $value = $this->parameters[$parameter] ?? null;

        if (in_array($value, ['1', 'on', true], true)) {
            return true;
        }

        if (in_array($value, ['0', '', false], true)) {
            return false;
        }

        if ($default === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $default;
    }

    /**
     * @param string $parameter
     *
     * @return array<string>
     */
    public function array(string $parameter): array
    {
        $value = $this->parameters[$parameter] ?? null;

        if (!is_array($value) && $value !== null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        $callback = static fn (array|null $value, Closure $rule): array|null => $rule($value);

        return array_reduce($this->rules, $callback, $value) ?? [];
    }

    /**
     * @param string   $parameter
     * @param float|null $default
     *
     * @return float
     */
    public function float(string $parameter, float|null $default = null): float
    {
        $value = $this->parameters[$parameter] ?? null;

        if (is_numeric($value)) {
            $value = (float) $value;
        } else {
            $value = null;
        }

        $callback = static fn (?float $value, Closure $rule): float|null => $rule($value);

        $value = array_reduce($this->rules, $callback, $value) ?? $default;

        if ($value === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $value;
    }

    /**
     * @param string   $parameter
     * @param int|null $default
     *
     * @return int
     */
    public function integer(string $parameter, int|null $default = null): int
    {
        $value = $this->parameters[$parameter] ?? null;

        if (is_string($value)) {
            if (ctype_digit($value)) {
                $value = (int) $value;
            } elseif (str_starts_with($value, '-') && ctype_digit(substr($value, 1))) {
                $value = (int) $value;
            }
        }

        if (!is_int($value)) {
            $value = null;
        }

        $callback = static fn (int|null $value, Closure $rule): int|null => $rule($value);

        $value = array_reduce($this->rules, $callback, $value) ?? $default;

        if ($value === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $value;
    }

    /**
     * @param string $parameter
     *
     * @return Route
     */
    public function route(string $parameter = 'route'): Route
    {
        $value = $this->parameters[$parameter] ?? null;

        if ($value instanceof Route) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }

    /**
     * @param string      $parameter
     * @param string|null $default
     *
     * @return string
     */
    public function string(string $parameter, string|null $default = null): string
    {
        $value = $this->parameters[$parameter] ?? null;

        if (!is_string($value)) {
            $value = null;
        }

        $callback = static fn (string|null $value, Closure $rule): string|null => $rule($value);

        $value =  array_reduce($this->rules, $callback, $value) ?? $default;

        if ($value === null) {
            throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
        }

        return $value;
    }

    /**
     * @param string $parameter
     *
     * @return Tree
     */
    public function tree(string $parameter = 'tree'): Tree
    {
        $value = $this->parameters[$parameter] ?? null;

        if ($value instanceof Tree) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }

    public function treeOptional(string $parameter = 'tree'): Tree|null
    {
        $value = $this->parameters[$parameter] ?? null;

        if ($value === null || $value instanceof Tree) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }

    public function user(string $parameter = 'user'): UserInterface
    {
        $value = $this->parameters[$parameter] ?? null;

        if ($value instanceof UserInterface) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }
}
