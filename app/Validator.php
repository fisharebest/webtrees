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

use Aura\Router\Route;
use Closure;
use Fisharebest\Webtrees\Contracts\UserInterface;
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
    /** @var array<int|string|Tree|UserInterface|array<int|string>> */
    private array $parameters;

    /** @var array<Closure> */
    private array $rules = [];

    /**
     * @param array<int|string|Tree|UserInterface|array<int|string>> $parameters
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
    public static function attributes(ServerRequestInterface $request): self
    {
        return new self($request->getAttributes());
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
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public static function serverParams(ServerRequestInterface $request): self
    {
        return new self($request->getServerParams());
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
     * @param array<string> $values
     *
     * @return self
     */
    public function isInArray(array $values): self
    {
        $this->rules[] = static fn (?string $value): ?string => $value !== null && in_array($value, $values, true) ? $value : null;

        return $this;
    }

    /**
     * @param array<string> $values
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
        $this->rules[] = static fn (?string $value): ?string => $value !== null && $value !== '' ? $value : null;

        return $this;
    }

    /**
     * @param string $base_url
     *
     * @return self
     */
    public function isLocalUrl(string $base_url): self
    {
        $this->rules[] = static function (?string $value) use ($base_url): ?string {
            if ($value !== null) {
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
     * @return self
     */
    public function isTag(): self
    {
        $this->rules[] = static function (?string $value): ?string {
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
        $this->rules[] = static function (?string $value): ?string {
            if ($value !== null && preg_match('/^' . Gedcom::REGEX_XREF . '$/', $value) === 1) {
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
    public function boolean(string $parameter, bool $default = null): bool
    {
        $value = $this->parameters[$parameter] ?? null;

        if (in_array($value, ['1', true], true)) {
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

        $callback = static fn (?array $value, Closure $rule): ?array => $rule($value);

        $value = array_reduce($this->rules, $callback, $value);
        $value ??= [];

        $check_utf8 = static function ($v, $k) use ($parameter) {
            if (is_string($k) && !preg_match('//u', $k) || is_string($v) && !preg_match('//u', $v)) {
                throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
            }
        };

        array_walk_recursive($value, $check_utf8);

        return $value;
    }

    /**
     * @param string   $parameter
     * @param int|null $default
     *
     * @return int
     */
    public function integer(string $parameter, int $default = null): int
    {
        $value = $this->parameters[$parameter] ?? null;

        if (is_string($value) && ctype_digit($value)) {
            $value = (int) $value;
        } elseif (!is_int($value)) {
            $value = null;
        }

        $callback = static fn (?int $value, Closure $rule): ?int => $rule($value);

        $value = array_reduce($this->rules, $callback, $value);

        $value ??= $default;

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
    public function string(string $parameter, string $default = null): string
    {
        $value = $this->parameters[$parameter] ?? null;

        if (!is_string($value)) {
            $value = null;
        }

        $callback = static fn (?string $value, Closure $rule): ?string => $rule($value);

        $value =  array_reduce($this->rules, $callback, $value);
        $value ??= $default;

        if ($value === null || preg_match('//u', $value) !== 1) {
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

    /**
     * @param string $parameter
     *
     * @return Tree|null
     */
    public function treeOptional(string $parameter = 'tree'): ?Tree
    {
        $value = $this->parameters[$parameter] ?? null;

        if ($value === null || $value instanceof Tree) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }

    /**
     * @param string $parameter
     *
     * @return UserInterface
     */
    public function user(string $parameter = 'user'): UserInterface
    {
        $value = $this->parameters[$parameter] ?? null;

        if ($value instanceof UserInterface) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }
}
