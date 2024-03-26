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

namespace Fisharebest\Webtrees\Http\Middleware;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class ErrorHandler implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler(callback: $this->errorHandler(...));

        $response = $handler->handle($request);

        restore_error_handler();

        return $response;
    }

    private function errorHandler(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // Ignore errors that are silenced with '@'
        if ((error_reporting() & $errno) !== 0) {
            throw new ErrorException(message: $errstr, code: 0, severity: $errno, filename: $errfile, line: $errline);
        }

        return true;
    }
}
