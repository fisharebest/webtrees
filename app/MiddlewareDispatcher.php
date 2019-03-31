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

use function is_string;

/**
 * Temporary class, to support migration to PSR-7, PSR-15 and PSR-17
 */
class MiddlewareDispatcher implements RequestHandlerInterface
{
    /** @var MiddlewareInterface[] */
    private $queue;

    /** @var RequestHandlerInterface */
    private $handler;

    /**
     * Dispatcher constructor.
     *
     * @param MiddlewareInterface[]   $queue
     * @param RequestHandlerInterface $handler
     */
    public function __construct(array $queue, RequestHandlerInterface $handler)
    {
        $this->queue   = $queue;
        $this->handler = $handler;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = array_shift($this->queue);

        if (is_string($middleware)) {
            $middleware = app($middleware);
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        return $this->handler->handle($request);
    }
}
