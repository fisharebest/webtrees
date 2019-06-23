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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Log;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use function dirname;
use function response;
use function str_replace;
use function view;
use const PHP_EOL;

/**
 * Middleware to handle and render errors.
 */
class HandleExceptions implements MiddlewareInterface, RequestMethodInterface, StatusCodeInterface
{
    use ViewResponseTrait;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            try {
                return $handler->handle($request);
            } catch (HttpException $exception) {
                $original_exception = $exception;

                return $this->httpExceptionResponse($request, $exception);
            } catch (Throwable $exception) {
                $original_exception = $exception;

                return $this->unhandledExceptionResponse($request, $exception);
            }
        } catch (Throwable $exception) {
            // If we can't handle the exception, rethrow it.
            throw $original_exception;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param HttpException          $exception
     *
     * @return ResponseInterface
     */
    private function httpExceptionResponse(ServerRequestInterface $request, HttpException $exception): ResponseInterface
    {
        if ($request->getHeaderLine('X-Requested-With') !== '') {
            $this->layout = 'layouts/ajax';
        }

        return $this->viewResponse('components/alert-danger', [
            'alert' => $exception->getMessage(),
            'title' => $exception->getMessage(),
        ], $exception->getStatusCode());
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     *
     * @return ResponseInterface
     */
    private function unhandledExceptionResponse(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        // Create a stack dump for the exception
        $base_path = dirname(__DIR__, 3);
        $trace     = $exception->getMessage() . ' ' . $exception->getFile() . ':' . $exception->getLine() . PHP_EOL . $exception->getTraceAsString();
        $trace     = str_replace($base_path, '…', $trace);

        try {
            Log::addErrorLog($trace);
        } catch (Throwable $exception) {
            // Must have been a problem with the database.  Nothing we can do here.
        }

        if ($request->getHeaderLine('X-Requested-With') !== '') {
            // If this was a GET request, then we were probably fetching HTML to display, for
            // example a chart or tab.
            if ($request->getMethod() === self::METHOD_GET) {
                $status_code = self::STATUS_OK;
            } else {
                $status_code = self::STATUS_INTERNAL_SERVER_ERROR;
            }

            return response(view('components/alert-danger', ['alert' => $trace]), $status_code);
        }

        return $this->viewResponse('errors/unhandled-exception', [
            'title' => 'Error',
            'error' => $trace,
        ], self::STATUS_INTERNAL_SERVER_ERROR);
    }
}
