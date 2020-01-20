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

namespace Fisharebest\Webtrees\Http\Middleware;

use Aura\Router\Route;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\RequestHandlers\HomePage;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function redirect;

/**
 * Transfer route parameters to the request.
 */
class LoadRouteParameters implements MiddlewareInterface
{
    /** @var TreeService */
    private $tree_service;

    /**
     * AddRouteParameters constructor.
     *
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');
        assert($route instanceof Route);

        foreach ($route->attributes as $key => $value) {
            if ($key === 'tree') {
                // Convert a tree name to a tree object.
                $value = $this->tree_service->all()->get($value);

                // Not a valid tree, and parameter is required?
                if ($value === null && strpos($route->path, '{tree}') !== false) {
                    if (Auth::check() || $request->getMethod() === RequestMethodInterface::METHOD_POST) {
                        throw new HttpNotFoundException();
                    }

                    return redirect(HomePage::class);
                }

                app()->instance(Tree::class, $value);
            }

            $request = $request->withAttribute((string) $key, $value);
        }

        return $handler->handle($request);
    }
}
