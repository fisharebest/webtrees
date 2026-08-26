<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2026 webtrees development team
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

use Fisharebest\Webtrees\Enums\HttpRequestMethod;
use Fisharebest\Webtrees\Enums\HttpStatusCode;
use Fisharebest\Webtrees\Exceptions\ImageException;
use Fisharebest\Webtrees\Http\Exceptions\HttpException;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Throwable;

use function e;
use function response;
use function view;

final readonly class HandleApplicationExceptions extends AbstractExceptionHandler implements MiddlewareInterface
{
    public function __construct(
        private ModuleService $module_service,
        private ModuleThemeInterface $theme,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $exception) {
            // The router added attributes to the request. We need it, not the original request that we have here.
            return $this->httpExceptionResponse(Registry::container()->get(ServerRequestInterface::class), $exception);
        } catch (ImageException $exception) {
            return $this->imageExceptionResponse($exception);
        } catch (Throwable $exception) {
            // The router added attributes to the request. We need it, not the original request that we have here.
            return $this->unhandledExceptionResponse(Registry::container()->get(ServerRequestInterface::class), $exception);
        }
    }

    private function httpExceptionResponse(
        ServerRequestInterface $request,
        HttpException $exception,
    ): ResponseInterface {
        $status_code = HttpStatusCode::from($exception->getCode());

        $html = view('components/alert-danger', ['alert' => $exception->getMessage()]);

        if ($this->isAjaxRequest($request)) {
            return response($html, $status_code);
        }

        $html = view('layouts/default', [
            'content' => $html,
            'modules' => $this->module_service->all(),
            'request' => $request,
            'theme'   => $this->theme,
            'tree'    => $request->getAttribute('tree'),
        ]);

        return response($html, $status_code);
    }

    private function imageExceptionResponse(ImageException $exception): ResponseInterface
    {
        // We can't send the actual status code, as browsers won't show images with 4xx/5xx.
        return response(content: $exception->toSvg())
            ->withHeader('content-type', 'image/svg+xml')
            ->withHeader('content-security-policy', 'default-src none');
    }

    private function unhandledExceptionResponse(ServerRequestInterface $request, Throwable $exception): ResponseInterface
    {
        $trace = $this->stackTrace($exception);

        try {
            Log::addErrorLog($trace);
        } catch (Throwable) {
        }

        $html = view('components/alert-danger', ['alert' => $trace]);

        if ($this->isAjaxRequest($request)) {
            return response($html, HttpStatusCode::InternalServerError);
        }

        $modules = $this->module_service->all();

        try {
            $html = view('layouts/default', [
                'content' => $html,
                'modules' => $modules,
                'request' => $request,
                'theme'   => $this->theme,
                'tree'    => $request->getAttribute('tree'),
            ]);
        } catch (Throwable) {
            // Try again, but without custom modules
            $modules = $modules->filter(
                static fn (ModuleInterface $module): bool => !$module instanceof ModuleCustomInterface
            );

            try {
                $html = view('layouts/default', [
                    'content' => $html,
                    'modules' => $modules,
                    'request' => $request,
                    'theme'   => $this->theme,
                    'tree'    => $request->getAttribute('tree'),
                ]);
            } catch (Throwable) {
                // Try again, but without a tree and the default theme
                $html = view('layouts/default', [
                    'content' => $html,
                    'modules' => $modules,
                    'request' => $request,
                    'theme'   => $this->theme,
                    'tree'    => null,
                ]);
            }
        }

        return response($html, HttpStatusCode::InternalServerError);
    }
}
