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

use DebugBar\StandardDebugBar;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\DebugBar;
use Illuminate\Database\Capsule\Manager as DB;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function class_exists;

/**
 * Middleware to add debugging info to the PHP debugbar.
 * Use `composer install --dev` on a development build to enable.
 * Note that you may need to increase the size of the fcgi buffers on nginx.
 * e.g. add these lines to your fastcgi_params file:
 * fastcgi_buffers 16 16m;
 * fastcgi_buffer_size 32m;
 */
class UseDebugbar implements MiddlewareInterface, StatusCodeInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (class_exists(StandardDebugBar::class)) {
            DebugBar::enable($request->getAttribute('base_url'));

            DebugBar::initPDO(DB::connection()->getPdo());

            $response = $handler->handle($request);

            if ($this->shouldSendDataOnNextPage($response)) {
                DebugBar::stackData();
            } elseif ($this->shouldSendDataInHeaders($request)) {
                DebugBar::sendDataInHeaders();
            }

            return $response;
        }

        return $handler->handle($request);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool
     */
    private function shouldSendDataOnNextPage(ResponseInterface $response): bool
    {
        $status_code = $response->getStatusCode();

        return $status_code === StatusCodeInterface::STATUS_FOUND || $status_code === StatusCodeInterface::STATUS_MOVED_PERMANENTLY;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function shouldSendDataInHeaders(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') !== '';
    }
}
