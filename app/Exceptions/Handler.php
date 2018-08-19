<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Exception;
use Fisharebest\Webtrees\Http\Controllers\ErrorController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Convert an exception into an HTTP response
 */
class Handler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request   $request
     * @param  Exception $exception
     *
     * @return Response
     */
    public function render(Request $request, Exception $exception): Response
    {
        if ($exception instanceof HttpException) {
            // Show a friendly page for expected exceptions.
            if ($request->isXmlHttpRequest()) {
                $response = new Response($exception->getMessage(), $exception->getStatusCode());
            } else {
                $controller = new ErrorController();
                $response   = $controller->errorResponse($exception);
            }
        } else {
            // Show an error page for unexpected exceptions.
            if (getenv('DEBUG')) {
                // Local dev environment?  Show full debug.
                $whoops = new Run();
                $whoops->pushHandler(new PrettyPageHandler());
                $whoops->handleException($exception);
            } else {
                // Running remotely?  Show a friendly error page.
                $controller = new ErrorController();
                $response   = $controller->unhandledExceptionResponse($request, $exception);
            }
        }

        return $response;
    }
}
