<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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

namespace Fisharebest\Webtrees\Http;

use Fisharebest\Webtrees\Http\RequestHandlers\NotFound;
use Fisharebest\Webtrees\Registry;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_reduce;
use function array_reverse;
use function is_string;

readonly class Dispatcher
{
    /**
     * @param list<class-string|MiddlewareInterface> $middleware
     */
    public static function dispatch(array $middleware, ServerRequestInterface $request): ResponseInterface
    {
        $pipeline = array_reduce(
            array: array_reverse(array: $middleware),
            callback: self::reduceMiddleware(...),
            initial: new NotFound(),
        );

        return $pipeline->handle(request: $request);
    }

    /**
     * @param class-string|MiddlewareInterface $item
     */
    private static function reduceMiddleware(RequestHandlerInterface $carry, string|MiddlewareInterface $item): RequestHandlerInterface
    {
        return new readonly class (carry: $carry, item: $item) implements RequestHandlerInterface {
            /**
             * @param class-string|MiddlewareInterface $item
             */
            public function __construct(
                private RequestHandlerInterface $carry,
                private string|MiddlewareInterface $item,
            ) {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $item = $this->item;

                if (is_string($item)) {
                    $item = Registry::container()->get(id: $item);
                }

                if ($item instanceof MiddlewareInterface) {
                    return $item->process(request: $request, handler: $this->carry);
                }

                throw new LogicException(message: 'Invalid or undefined middleware');
            }
        };
    }
}
