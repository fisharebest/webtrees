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

namespace Fisharebest\Webtrees\Exceptions;

use Closure;
use ErrorException;
use Fisharebest\Webtrees\Http\Controllers\ErrorController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use function error_reporting;
use function ob_end_clean;
use function ob_get_level;

/**
 * Convert an exception into an HTTP response
 */
class Handler
{
    /**
     * An error handler that can be passed to set_error_handler().
     * Converts errors to exceptions
     *
     * @return Closure
     */
    public static function phpErrorHandler(): Closure
    {
        return static function (int $errno, string $errstr, string $errfile, int $errline): bool {
            // Ignore errors that are silenced with '@'
            if (error_reporting() & $errno) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }

            return true;
        };
    }

    /**
     * A final exception handler that can be passed to set_exception_handler().
     * Display any exception that are not caught by the middleware exception handler.
     * Typically, this will be errors in index.php, errors in the exception handler
     * and errors while displaying errors.
     *
     * @return Closure
     */
    public static function phpExceptionHandler(): Closure
    {
        return static function (Throwable $ex): void {
            $trace = $ex->getTraceAsString();
            $trace = str_replace(WT_ROOT, '…/', $trace);

            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            echo '<html lang="en"><head><title>Error</title><meta charset="UTF-8"></head><body><pre>' . $trace . '</pre></body></html>';
        };
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     *
     * @return ResponseInterface
     */
    public function render(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        $controller = new ErrorController();

        if ($exception instanceof HttpException) {
            if ($request->getHeaderLine('X-Requested-With') !== '') {
                return $controller->ajaxErrorResponse($exception);
            }

            return $controller->errorResponse($exception);
        }

        return $controller->unhandledExceptionResponse($request, $exception);
    }
}
