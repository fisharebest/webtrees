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

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpException;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\PhpService;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Validator;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function dirname;
use function error_get_last;
use function nl2br;
use function ob_end_clean;
use function ob_get_level;
use function register_shutdown_function;
use function response;
use function str_replace;
use function view;

use const E_ERROR;
use const PHP_EOL;

/**
 * Middleware to handle and render errors.
 */
class HandleExceptions implements MiddlewareInterface, StatusCodeInterface
{
    use ViewResponseTrait;

    public function __construct(private PhpService $php_service, private TreeService $tree_service)
    {
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
        // Fatal errors.  We may be out of memory, so do not create any variables.
        register_shutdown_function(callback: function (): void {
            if (error_get_last() !== null && error_get_last()['type'] & E_ERROR) {
                // If PHP does not display the error, then we must display it.
                if (!$this->php_service->displayErrors()) {
                    echo
                        error_get_last()['message'],
                        '<br><br>',
                        error_get_last()['file'],
                        ': ',
                        error_get_last()['line'];
                }
            }
        });

        try {
            return $handler->handle($request);
        } catch (HttpException $exception) {
            // The router added the tree attribute to the request, and we need it for the error response.
            if (Registry::container()->has(ServerRequestInterface::class)) {
                $request = Registry::container()->get(ServerRequestInterface::class);
            } else {
                Registry::container()->set(ServerRequestInterface::class, $request);
            }

            return $this->httpExceptionResponse($request, $exception);
        } catch (FilesystemException $exception) {
            // The router added the tree attribute to the request, and we need it for the error response.
            $request = Registry::container()->get(ServerRequestInterface::class) ?? $request;

            return $this->thirdPartyExceptionResponse($request, $exception);
        } catch (Throwable $exception) {
            // Exception thrown while buffering output?
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            // The Router middleware may have added a tree attribute to the request.
            // This might be usable in the error page.
            if (Registry::container()->has(ServerRequestInterface::class)) {
                $request = Registry::container()->get(ServerRequestInterface::class);
            }

            // Show the exception in a standard webtrees page (if we can).
            try {
                return $this->unhandledExceptionResponse($request, $exception);
            } catch (Throwable) {
                // That didn't work.  Try something else.
            }

            // Show the exception in a tree-less webtrees page (if we can).
            try {
                $request = $request->withAttribute('tree', null);

                return $this->unhandledExceptionResponse($request, $exception);
            } catch (Throwable) {
                // That didn't work.  Try something else.
            }

            // Show the exception in an error page (if we can).
            try {
                $this->layout = 'layouts/error';

                return $this->unhandledExceptionResponse($request, $exception);
            } catch (Throwable) {
                // That didn't work.  Try something else.
            }

            // Show a stack dump.
            return response(nl2br((string) $exception), StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
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
        $tree    = Validator::attributes($request)->treeOptional();
        $default = Site::getPreference('DEFAULT_GEDCOM');
        $tree    ??= $this->tree_service->all()[$default] ?? $this->tree_service->all()->first();

        $status_code = $exception->getCode();

        // If this was a GET request, then we were probably fetching HTML to display, for
        // example a chart or tab.
        if (
            $request->getHeaderLine('X-Requested-With') !== '' &&
            $request->getMethod() === RequestMethodInterface::METHOD_GET
        ) {
            $this->layout = 'layouts/ajax';
            $status_code = StatusCodeInterface::STATUS_OK;
        }

        return $this->viewResponse('components/alert-danger', [
            'alert' => $exception->getMessage(),
            'title' => $exception->getMessage(),
            'tree'  => $tree,
        ], $status_code);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     *
     * @return ResponseInterface
     */
    private function thirdPartyExceptionResponse(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        $tree = Validator::attributes($request)->treeOptional();

        $default = Site::getPreference('DEFAULT_GEDCOM');
        $tree ??= $this->tree_service->all()[$default] ?? $this->tree_service->all()->first();

        if ($request->getHeaderLine('X-Requested-With') !== '') {
            $this->layout = 'layouts/ajax';
        }

        return $this->viewResponse('components/alert-danger', [
            'alert' => $exception->getMessage(),
            'title' => $exception->getMessage(),
            'tree'  => $tree,
        ], StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
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
        // User data may contain non UTF-8 characters.
        $trace     = mb_convert_encoding($trace, 'UTF-8', 'UTF-8');
        $trace     = e($trace);
        $trace     = preg_replace('/^.*modules_v4.*$/m', '<b>$0</b>', $trace);

        try {
            Log::addErrorLog($trace);
        } catch (Throwable) {
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
                'tree'    => Validator::attributes($request)->treeOptional(),
            ], StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        } catch (Throwable) {
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
