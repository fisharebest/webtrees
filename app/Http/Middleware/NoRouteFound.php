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

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Http\RequestHandlers\HomePage;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function redirect;
use function route;

/**
 * Middleware to generate a response when no route was matched.
 */
class NoRouteFound implements MiddlewareInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Bind the request into the container.  We'll need it to generate an error page.
        app()->instance(ServerRequestInterface::class, $request);

        if ($request->getMethod() !== RequestMethodInterface::METHOD_GET) {
            throw new HttpNotFoundException();
        }

        return redirect(route(HomePage::class));
    }
}
