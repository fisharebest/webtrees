<?php
declare(strict_types = 1);
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
namespace Fisharebest\Webtrees;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

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
     * @param string $class
     * @param object $object
     *
     * @return void
     */
    public function bind(string $class, $object)
    {
        $this->bindings[$class] = $object;
    }

    /**
     * Create an instance of a class, injecting all its dependencies.
     *
     * @param string $class
     *
     * @return object
     */
    public function resolve(string $class)
    {
        if (array_key_exists($class, $this->bindings)) {
            return $this->bindings[$class];
        }

        $reflector = new ReflectionClass($class);

        $constructor = $reflector->getConstructor();

        // No constructor?  Nothing to inject.
        if ($constructor === null) {
            return new $class();
        }

        $parameters = $this->resolveParameters($constructor->getParameters());

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

        $parameters = $this->resolveParameters($reflector->getParameters());

        return $reflector->invoke($object, ...$parameters);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    private function resolveParameters(array $parameters): array
    {
        return array_map(function (ReflectionParameter $parameter) {
            return $this->resolve($parameter->getClass()->name);
        }, $parameters);
    }
}
