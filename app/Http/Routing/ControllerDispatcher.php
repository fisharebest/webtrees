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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\Header;
use Fisharebest\Webtrees\Http\Exceptions\HttpBadRequestException;
use Fisharebest\Webtrees\Http\Exceptions\HttpInternalServerErrorException;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Repository;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\SharedNote;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Submission;
use Fisharebest\Webtrees\Submitter;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionEnum;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

use function is_numeric;
use function method_exists;
use function strtolower;

class ControllerDispatcher implements MiddlewareInterface
{
    public function __construct(
        private TreeService $tree_service,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = Validator::attributes($request)->route();

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
     * @return array<ServerRequestInterface|Tree|GedcomRecord|string|int|float|array<string|array<string>>|BackedEnum|null>
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

            $message = sprintf('The parameter “%s” is missing.', $name);
            throw new HttpBadRequestException($message);
        }

        // @TODO - this may be a Tree object, which is set for compatibility
        // with legacy code. Remove when the migration is complete.
        if (is_object($value)) {
            return $value;
        }

        if ($type instanceof ReflectionNamedType) {
            if (is_subclass_of($type->getName(), BackedEnum::class)) {
                $reflection_enum = new ReflectionEnum($type->getName());

                if ($reflection_enum->getBackingType()->getName() === 'int') {
                    $value = (int) $value;
                }

                $method   = new ReflectionMethod($type->getName(), 'tryFrom');
                $resolved = $method->invoke(null, $value);
            } else {
                $resolved = match ($type->getName()) {
                    Tree::class          => $this->tree_service->all()->get($value),
                    UserInterface::class => Auth::user(),
                    Individual::class    => Registry::individualFactory()->make($value, $tree),
                    Family::class        => Registry::familyFactory()->make($value, $tree),
                    Source::class        => Registry::sourceFactory()->make($value, $tree),
                    Repository::class    => Registry::repositoryFactory()->make($value, $tree),
                    Media::class         => Registry::mediaFactory()->make($value, $tree),
                    Note::class          => Registry::noteFactory()->make($value, $tree),
                    SharedNote::class    => Registry::sharedNoteFactory()->make($value, $tree),
                    Location::class      => Registry::locationFactory()->make($value, $tree),
                    Header::class        => Registry::headerFactory()->make($value, $tree),
                    Submission::class    => Registry::submissionFactory()->make($value, $tree),
                    Submitter::class     => Registry::submitterFactory()->make($value, $tree),
                    GedcomRecord::class  => Registry::gedcomRecordFactory()->make($value, $tree),
                    'string'             => $value,
                    'bool'               => (bool) $value,
                    'int'                => $this->castToInt((string) $value, $name),
                    'float'              => $this->castToFloat((string) $value, $name),
                    'array'              => is_array($value) ? $value : [$value],
                    default              => null,
                };
            }

            if ($resolved === null && !$type->allowsNull()) {
                $message = sprintf('The parameter “%s” could not be resolved.', $name);
                throw new HttpBadRequestException($message);
            }

            return $resolved;
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

    private function castToInt(string $value, string $name): int|null
    {
        if ($value === '') {
            return null;
        }

        if (!is_numeric($value) || (string) (int) $value !== $value) {
            throw new HttpNotFoundException('Invalid integer parameter: ' . $name);
        }

        return (int) $value;
    }

    private function castToFloat(string $value, string $name): float|null
    {
        if ($value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            throw new HttpNotFoundException('Invalid numeric parameter: ' . $name);
        }

        return (float) $value;
    }
}
