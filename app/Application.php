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

use Illuminate\Container\Container;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

use function array_map;

/**
 * Application container.
 */
class Application extends Container
{
    /**
     * Call an object's method, injecting all its dependencies.
     *
     * @param object $object
     * @param string $method
     *
     * @return mixed
     */
    public function dispatch($object, string $method)
    {
        $reflector = new ReflectionMethod($object, $method);

        $parameters = $this->makeParameters($reflector->getParameters());

        return $reflector->invoke($object, ...$parameters);
    }

    /**
     * @param array<ReflectionParameter> $parameters
     *
     * @return array<mixed>
     */
    private function makeParameters(array $parameters): array
    {
        return array_map(function (ReflectionParameter $parameter) {
            $class = $parameter->getClass();

            if ($class instanceof ReflectionClass) {
                return $this->make($class->getName());
            }

            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            return null;
        }, $parameters);
    }
}
