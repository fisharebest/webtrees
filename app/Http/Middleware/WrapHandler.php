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

namespace Fisharebest\Webtrees\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;

/**
 * Middleware to run a request-handler.
 */
class WrapHandler implements MiddlewareInterface
{
    /** @var string|RequestHandlerInterface */
    private $handler;

    /**
     * WrapController constructor.
     *
     * @param string|RequestHandlerInterface $handler
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // A request handler object?
        if ($this->handler instanceof RequestHandlerInterface) {
            return $this->handler->handle($request);
        }

        // A string containing a request handler class name
        return app($this->handler)->handle($request);
    }
}
