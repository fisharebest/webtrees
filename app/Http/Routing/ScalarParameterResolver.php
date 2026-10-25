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
use ReflectionEnum;
use ReflectionMethod;

use function is_array;
use function is_numeric;
use function is_string;
use function is_subclass_of;
use function mb_check_encoding;

/**
 * Handles scalar types (string, int, float, bool) and BackedEnum.
 * This resolver is generic and has no webtrees domain dependencies.
 */
final readonly class ScalarParameterResolver implements ParameterResolverInterface
{
    private const SCALAR_TYPES = ['string', 'int', 'float', 'bool', 'array'];

    public function supports(string $type_name): bool
    {
        if (in_array($type_name, self::SCALAR_TYPES, true)) {
            return true;
        }

        return is_subclass_of($type_name, BackedEnum::class);
    }

    public function serialize(mixed $value): bool|int|string|array|null
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }

    public function deserialize(string $value, string $type_name, array $context = []): mixed
    {
        if (is_subclass_of($type_name, BackedEnum::class)) {
            $reflection_enum = new ReflectionEnum($type_name);

            if ($reflection_enum->getBackingType()->getName() === 'int') {
                $value = (int) $value;
            }

            $method = new ReflectionMethod($type_name, 'tryFrom');

            return $method->invoke(null, $value);
        }

        return match ($type_name) {
            'string' => $this->castToUtf8String($value),
            'bool'   => $this->castToBool($value),
            'int'    => $this->castToInt($value),
            'float'  => $this->castToFloat($value),
            'array'  => null,
            default  => null,
        };
    }

    private function castToBool(string $value): bool|null
    {
        return match ($value) {
            ''  => false,
            '1' => true, // From generated URLs
            'on' => true, // From checkboxes in forms
            default => null,
        };
    }

    private function castToUtf8String(string $value): string|null
    {
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        return null;
    }

    private function castToInt(string $value): int|null
    {
        if (is_numeric($value) && (string) (int) $value === $value) {
            return (int) $value;
        }

        return null;
    }

    private function castToFloat(string $value): float|null
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    /**
     * Validate that the value is an array with valid UTF-8 keys and values.
     *
     * @return array<mixed>|null
     */
    public function castToArray(mixed $value): array|null
    {
        if (!is_array($value)) {
            return null;
        }

        return $this->validateArrayUtf8($value) ? $value : null;
    }

    /**
     * @param array<mixed|array<mixed>> $array
     */
    private function validateArrayUtf8(array $array): bool
    {
        foreach ($array as $key => $item) {
            if (is_string($key) && !mb_check_encoding($key, 'UTF-8')) {
                return false;
            }

            if (is_array($item)) {
                if (!$this->validateArrayUtf8($item)) {
                    return false;
                }
            } elseif (is_string($item) && !mb_check_encoding($item, 'UTF-8')) {
                return false;
            }
        }

        return true;
    }
}
