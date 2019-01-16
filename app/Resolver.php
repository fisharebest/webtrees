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

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use function array_map;
use function is_object;
use function is_string;

/**
 * Simple dependency injection.
 */
class Resolver
{
    /** @var object[] */
    private $bindings = [];

    /**
     * For some classes (e.g. Request, Tree, User), we inject a specific instance
     * of an object, rather than a newly instantiated object
     *
     * @param string      $class
     * @param object|null $object
     *
     * @return void
     */
    public function bind(string $class, $object): void
    {
        $this->bindings[$class] = $object;
    }

    /**
     * Create an instance of a class, injecting all its dependencies.
     *
     * @param string $class
     *
     * @return object (can't type-hint this until PHP 7.2)
     */
    public function make(string $class)
    {
        $thing = $this->bindings[$class] ?? null;

        if (is_object($thing)) {
            return $thing;
        }

        if (is_string($thing) && class_exists($thing)) {
            return new $thing;
        }

        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            // No constructor?  Nothing to inject.
            $parameters = [];
        } else {
            // Recursively resolve the parameters.
            $parameters = $this->makeParameters($constructor->getParameters());
        }

        return $reflector->newInstanceArgs($parameters);
    }

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
