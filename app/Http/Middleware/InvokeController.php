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

namespace Fisharebest\Webtrees\Http\Middleware;

use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Http\Exceptions\HttpInternalServerErrorException;
use Fisharebest\Webtrees\Http\Routing\ParameterResolverInterface;
use Fisharebest\Webtrees\Http\Routing\Route;
use Fisharebest\Webtrees\Http\Routing\ScalarParameterResolver;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

use function is_array;
use function method_exists;
use function strtolower;

class InvokeController implements MiddlewareInterface
{
    public function __construct(
        private readonly ParameterResolverInterface $parameter_resolver,
        private readonly ScalarParameterResolver $scalar_parameter_resolver,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route      = Validator::attributes($request)->route();
        $controller = Registry::container()->get($route->controller);
        $verb       = strtolower($request->getMethod());

        // Determine which method to call
        if ($controller instanceof RequestHandlerInterface) {
            return $controller->handle($request);
        }

        if (method_exists($controller, $verb)) {
            $method     = new ReflectionMethod($controller, $verb);
            $parameters = $this->resolveParameters($method, $request);

            return $method->invoke($controller, ...$parameters);
        }

        // This method is not allowed.
        $allowed_verbs = [];

        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $allowed_verb) {
            if (method_exists($controller, strtolower($allowed_verb))) {
                $allowed_verbs[] = $allowed_verb;
            }
        }

        return Registry::responseFactory()->response(
            '',
            HttpStatusCode::MethodNotAllowed,
            ['Allow' => implode(', ', $allowed_verbs)],
        );
    }

    /**
     * Resolve all parameters for a controller method.
     *
     * @return array<mixed>
     */
    private function resolveParameters(ReflectionMethod $method, ServerRequestInterface $request): array
    {
        // To resolve GedcomRecord parameters from XREFs, we will need to
        // have previously resolved a Tree parameter.
        $tree = null;

        $parameters = [];

        foreach ($method->getParameters() as $parameter) {
            $resolved = $this->resolveParameter($parameter, $request, $tree);

            if ($resolved instanceof Tree) {
                $tree = $resolved;
            }

            $parameters[] = $resolved;
        }

        return $parameters;
    }

    private function resolveParameter(
        ReflectionParameter $parameter,
        ServerRequestInterface $request,
        Tree|null $tree,
    ): mixed {
        $type  = $parameter->getType();
        $name  = $parameter->getName();
        $value = $this->findParameterValue($name, $request);

        if ($type instanceof ReflectionNamedType && $type->getName() === ServerRequestInterface::class) {
            return $request;
        }

        // We don't have a value for this parameter...
        if ($value === null) {
            // ...but it has a default
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            // ...but it allows null
            if ($type->allowsNull()) {
                return null;
            }

            $message = sprintf('The parameter "%s" is missing.', $name);
            throw new HttpBadRequestException($message);
        }

        // Middleware sets these attributes as objects for legacy compatibility.
        // Remove when the migration to string-based resolution is complete.
        if ($value instanceof Tree && $name === 'tree') {
            // Set by middleware: Router
            return $value;
        }

        if ($value instanceof UserInterface && $name === 'user') {
            // Set by middleware: UseSession
            return $value;
        }

        if ($value instanceof ModuleThemeInterface && $name === 'theme') {
            // Set by middleware: UseTheme
            return $value;
        }

        if ($value instanceof Route && $name === 'route') {
            // Set by middleware: Router
            return $value;
        }

        if ($type instanceof ReflectionNamedType) {
            $type_name = $type->getName();

            // Delegate to parameter resolvers
            if ($this->parameter_resolver->supports($type_name)) {
                // Arrays cannot be coerced to string - validate directly.
                if ($type_name === 'array') {
                    return $this->scalar_parameter_resolver->castToArray($value);
                }

                $context  = ['tree' => $tree];
                $resolved = $this->parameter_resolver->deserialize((string) $value, $type_name, $context);


                if ($resolved === null && !$type->allowsNull()) {
                    $message = sprintf('The parameter "%s" could not be resolved.', $name);
                    throw new HttpBadRequestException($message);
                }

                return $resolved;
            }

            // Unknown type
            return null;
        } else {
            throw new HttpInternalServerErrorException('Cannot resolve union/intersection type for: ' . $name);
        }
    }

    /**
     * Find a parameter value from request attributes, query params, or parsed body.
     */
    private function findParameterValue(string $name, ServerRequestInterface $request): mixed
    {
        // Route attributes (populated by the router)
        $value = $request->getAttribute($name);

        if ($value !== null) {
            return $value;
        }

        // Query parameters
        $query = $request->getQueryParams();

        if (isset($query[$name])) {
            return $query[$name];
        }

        // Parsed body
        $body = $request->getParsedBody();

        if (is_array($body) && isset($body[$name])) {
            return $body[$name];
        }

        return null;
    }
}
