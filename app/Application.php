<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Fisharebest\Webtrees;

use Illuminate\Container\Container;
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
     * @param array $parameters
     *
     * @return array
     */
    private function makeParameters(array $parameters): array
    {
        return array_map(function (ReflectionParameter $parameter) {
            return $this->make($parameter->getClass()->name);
        }, $parameters);
    }
}
