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
use function gettype;
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
    /** @var array<string|array> */
    private array $parameters;

    /** @var array<Closure> */
    private array $rules = [];

    /**
     * @param array<string|array> $parameters
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
        $this->rules[] = static function ($value) use ($minimum, $maximum): ?int {
            if (is_int($value)) {
                if ($value >= $minimum && $value <= $maximum) {
                    return $value;
                }

                return null;
            }

            if ($value === null) {
                return null;
            }

            throw new LogicException(__METHOD__ . ' does not accept ' . gettype($value));
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
        $this->rules[] = static function ($value) use ($base_url): ?string {
            if ($value === null) {
                return null;
            }

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

                return null;
            }

            throw new LogicException(__METHOD__ . ' does not accept ' . gettype($value));
        };

        return $this;
    }

    /**
     * @return $this
     */
    public function isXref(): self
    {
        $this->rules[] = static function ($value) {
            if (is_string($value)) {
                if (preg_match('/^' . Gedcom::REGEX_XREF . '$/', $value) === 1) {
                    return $value;
                }

                return null;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    if (preg_match('/^' . Gedcom::REGEX_XREF . '$/', $item) !== 1) {
                        return null;
                    }
                }

                return $value;
            }

            if ($value === null) {
                return null;
            }

            throw new LogicException(__METHOD__ . ' does not accept ' . gettype($value));
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

        return array_reduce($this->rules, static fn ($value, $rule) => $rule($value), $value);
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

        return array_reduce($this->rules, static fn ($value, $rule) => $rule($value), $value);
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

        return array_reduce($this->rules, static fn ($value, $rule) => $rule($value), $value);
    }

    /**
     * @param string $parameter
     *
     * @return array<string>
     */
    public function requiredArray(string $parameter): array
    {
        $value = $this->array($parameter);

        if (is_array($value)) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }

    /**
     * @param string $parameter
     *
     * @return int
     */
    public function requiredInteger(string $parameter): int
    {
        $value = $this->integer($parameter);

        if (is_int($value)) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }

    /**
     * @param string $parameter
     *
     * @return string
     */
    public function requiredString(string $parameter): string
    {
        $value = $this->string($parameter);

        if (is_string($value)) {
            return $value;
        }

        throw new HttpBadRequestException(I18N::translate('The parameter “%s” is missing.', $parameter));
    }
}
