<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fisharebest\Webtrees\Contracts\ContainerInterface;
use Fisharebest\Webtrees\Exceptions\NotFoundInContainerException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

use function array_key_exists;
use function array_map;

/**
 * @template T of object
 *
 * A PSR-11 compatible container and dependency injector.
 */
class Container implements ContainerInterface
{
    /** @var array<class-string<T>,T> */
    private array $container = [];

    /**
     * @param class-string<T> $id
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->container);
    }

    /**
     * @param class-string<T> $id
     *
     * @return T
     */
    public function get(string $id): object
    {
        return $this->container[$id] ??= $this->make($id);
    }

    /**
     * @param class-string<T> $id
     * @param T               $object
     *
     * @return $this
     */
    public function set(string $id, object $object): static
    {
        $this->container[$id] = $object;

        return $this;
    }

    /**
     * @param class-string<T> $id
     *
     * @return T
     */
    private function make(string $id): object
    {
        $reflector   = new ReflectionClass($id);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $id();
        }

        $parameters = array_map(self::makeParameter(...), $constructor->getParameters());

        return new $id(...$parameters);
    }

    private function makeParameter(ReflectionParameter $reflection_parameter): mixed
    {
        if ($reflection_parameter->isOptional()) {
            return $reflection_parameter->getDefaultValue();
        }

        $type = $reflection_parameter->getType();

        if ($type instanceof ReflectionNamedType) {
            return $this->get($type->getName());
        }

        throw new NotFoundInContainerException('Cannot make ' . $reflection_parameter->getName());
    }
}
