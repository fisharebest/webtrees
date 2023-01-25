<?php

/**
 * webtrees: online genealogy
 * 'Copyright (C) 2023 webtrees development team
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

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Aura\Router\Rule\Accepts;
use Aura\Router\Rule\Allows;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Fisharebest\Webtrees\Webtrees;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function explode;
use function implode;
use function str_contains;

/**
 * Simple class to help migrate to a third-party routing library.
 */
class Router implements MiddlewareInterface
{
    private ModuleService $module_service;

    private RouterContainer $router_container;

    private TreeService $tree_service;

    /**
     * Router constructor.
     *
     * @param ModuleService   $module_service
     * @param RouterContainer $router_container
     * @param TreeService     $tree_service
     */
    public function __construct(
        ModuleService $module_service,
        RouterContainer $router_container,
        TreeService $tree_service
    ) {
        $this->module_service   = $module_service;
        $this->router_container = $router_container;
        $this->tree_service     = $tree_service;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Ugly URLs store the path in a query parameter.
        $url_route = Validator::queryParams($request)->string('route', '');

        if (Validator::attributes($request)->boolean('rewrite_urls', false)) {
            // We are creating pretty URLs, but received an ugly one. Probably a search-engine. Redirect it.
            if ($url_route !== '') {
                $uri = $request->getUri()
                    ->withPath($url_route)
                    ->withQuery(explode('&', $request->getUri()->getQuery(), 2)[1] ?? '');

                return Registry::responseFactory()->redirectUrl($uri, StatusCodeInterface::STATUS_PERMANENT_REDIRECT);
            }
        } else {
            // Turn the ugly URL into a pretty one, so the router can parse it.
            $uri     = $request->getUri()->withPath($url_route);
            $request = $request->withUri($uri);
        }

        // Match the request to a route.
        $matcher = $this->router_container->getMatcher();
        $route   = $matcher->match($request);

        // No route matched?
        if ($route === false) {
            $failed_route = $matcher->getFailedRoute();

            if ($failed_route instanceof Route) {
                if ($failed_route->failedRule === Allows::class) {
                    return Registry::responseFactory()->response('', StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED, [
                        'Allow' => implode(', ', $failed_route->allows),
                    ]);
                }

                if ($failed_route->failedRule === Accepts::class) {
                    return Registry::responseFactory()->response('Negotiation failed', StatusCodeInterface::STATUS_NOT_ACCEPTABLE);
                }
            }

            // Not found
            return $handler->handle($request);
        }

        // Add the route as attribute of the request
        $request = $request->withAttribute('route', $route);

        // This middleware cannot run until after the routing, as it needs to know the route.
        $post_routing_middleware = [CheckCsrf::class];

        // Firstly, apply the route middleware
        $route_middleware = $route->extras['middleware'] ?? [];

        // Secondly, apply any module middleware
        $module_middleware = $this->module_service->findByInterface(MiddlewareInterface::class)->all();

        // Finally, run the handler using middleware
        $handler_middleware = [RequestHandler::class];

        $middleware = array_merge(
            $post_routing_middleware,
            $route_middleware,
            $module_middleware,
            $handler_middleware
        );

        // Add the matched attributes to the request.
        foreach ($route->attributes as $key => $value) {
            if ($key === 'tree') {
                $value = $this->tree_service->all()->get($value);
                app()->instance(Tree::class, $value);

                // Missing mandatory parameter? Let the default handler take care of it.
                if ($value === null && str_contains($route->path, '{tree}')) {
                    return $handler->handle($request);
                }
            }

            $request = $request->withAttribute((string) $key, $value);
        }

        // Bind the updated request into the container
        app()->instance(ServerRequestInterface::class, $request);

        return Webtrees::dispatch($request, $middleware);
    }
}
