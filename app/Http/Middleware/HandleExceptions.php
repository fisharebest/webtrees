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
use Fisharebest\Localization\Locale\LocaleEnUs;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

use function app;
use function dirname;
use function ob_end_clean;
use function ob_get_level;
use function response;
use function str_replace;
use function view;

use const PHP_EOL;

/**
 * Middleware to handle and render errors.
 */
class HandleExceptions implements MiddlewareInterface, StatusCodeInterface
{
    use ViewResponseTrait;

    /** @var TreeService */
    private $tree_service;

    /**
     * HandleExceptions constructor.
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
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $exception) {
            // The router added the tree attribute to the request, and we need it for the error response.
            $request = app(ServerRequestInterface::class) ?? $request;

            return $this->httpExceptionResponse($request, $exception);
        } catch (Throwable $exception) {
            // Exception thrown while buffering output?
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // The Router middleware may have added a tree attribute to the request.
            // This might be usable in the error page.
            if (app()->has(ServerRequestInterface::class)) {
                $request = app(ServerRequestInterface::class) ?? $request;
            }

            // No locale set in the request?
            if ($request->getAttribute('locale') === null) {
                $request = $request->withAttribute('locale', new LocaleEnUs());
                app()->instance(ServerRequestInterface::class, $request);
            }

            // Show the exception in a standard webtrees page (if we can).
            try {
                return $this->unhandledExceptionResponse($request, $exception);
            } catch (Throwable $e) {
                // That didn't work.  Try something else.
            }

            // Show the exception in a tree-less webtrees page (if we can).
            try {
                $request = $request->withAttribute('tree', null);

                return $this->unhandledExceptionResponse($request, $exception);
            } catch (Throwable $e) {
                // That didn't work.  Try something else.
            }

            // Show the exception in an error page (if we can).
            try {
                $this->layout = 'layouts/error';

                return $this->unhandledExceptionResponse($request, $exception);
            } catch (Throwable $e) {
                // That didn't work.  Try something else.
            }

            // Show a stack dump.
            return response((string) $exception, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
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
        $tree = $request->getAttribute('tree');

        $default = Site::getPreference('DEFAULT_GEDCOM');
        $tree = $tree ?? $this->tree_service->all()[$default] ?? $this->tree_service->all()->first();

        if ($request->getHeaderLine('X-Requested-With') !== '') {
            $this->layout = 'layouts/ajax';
        }

        return $this->viewResponse('components/alert-danger', [
            'alert' => $exception->getMessage(),
            'title' => $exception->getMessage(),
            'tree'  => $tree,
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
        $this->layout = 'layouts/default';

        // Create a stack dump for the exception
        $base_path = dirname(__DIR__, 3);
        $trace     = $exception->getMessage() . ' ' . $exception->getFile() . ':' . $exception->getLine() . PHP_EOL . $exception->getTraceAsString();
        $trace     = str_replace($base_path, '…', $trace);
        $trace     = e($trace);
        $trace     = preg_replace('/^.*modules_v4.*$/m', '<b>$0</b>', $trace);

        try {
            Log::addErrorLog($trace);
        } catch (Throwable $exception) {
            // Must have been a problem with the database.  Nothing we can do here.
        }

        if ($request->getHeaderLine('X-Requested-With') !== '') {
            // If this was a GET request, then we were probably fetching HTML to display, for
            // example a chart or tab.
            if ($request->getMethod() === RequestMethodInterface::METHOD_GET) {
                $status_code = StatusCodeInterface::STATUS_OK;
            } else {
                $status_code = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
            }

            return response(view('components/alert-danger', ['alert' => $trace]), $status_code);
        }

        try {
            // Try with a full header/menu
            return $this->viewResponse('errors/unhandled-exception', [
                'title'   => 'Error',
                'error'   => $trace,
                'request' => $request,
                'tree'    => $request->getAttribute('tree'),
            ], StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        } catch (Throwable $ex) {
            // Try with a minimal header/menu
            return $this->viewResponse('errors/unhandled-exception', [
                'title'   => 'Error',
                'error'   => $trace,
                'request' => $request,
                'tree'    => null,
            ], StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
