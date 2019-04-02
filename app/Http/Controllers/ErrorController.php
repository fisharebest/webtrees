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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\RedirectResponse;
use Fisharebest\Webtrees\Http\Response;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

/**
 * Controller for error handling.
 */
class ErrorController extends AbstractBaseController
{
    /**
     * No route was match?  Send the user somewhere sensible, if we can.
     *
     * @param ServerRequestInterface $request
     * @param Tree|null              $tree
     *
     * @return Response
     */
    public function noRouteFound(ServerRequestInterface $request, ?Tree $tree): ResponseInterface
    {
        // The tree exists, we have access to it, and it is fully imported.
        if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
            return new RedirectResponse(route('tree-page', ['ged' => $tree->name()]));
        }

        // Not logged in?
        if (!Auth::check()) {
            return new RedirectResponse(route('login', ['url' => $request->getRequestUri()]));
        }

        // No tree or tree not imported?
        if (Auth::isAdmin()) {
            return new RedirectResponse(route('admin-trees'));
        }

        return $this->viewResponse('errors/no-tree-access', ['title' => '']);
    }

    /**
     * Convert an exception into an error message
     *
     * @param HttpException $ex
     *
     * @return Response
     */
    public function errorResponse(HttpException $ex): ResponseInterface
    {
        return $this->viewResponse('components/alert-danger', [
            'alert' => $ex->getMessage(),
            'title' => $ex->getMessage(),
        ], $ex->getStatusCode());
    }

    /**
     * Convert an exception into an error message
     *
     * @param HttpException $ex
     *
     * @return Response
     */
    public function ajaxErrorResponse(HttpException $ex): ResponseInterface
    {
        return new Response(view('components/alert-danger', [
            'alert' => $ex->getMessage(),
        ]), $ex->getStatusCode());
    }

    /**
     * Convert an exception into an error message
     *
     * @param ServerRequestInterface $request
     * @param Throwable              $ex
     *
     * @return Response
     */
    public function unhandledExceptionResponse(ServerRequestInterface $request, Throwable $ex): ResponseInterface
    {
        // Create a stack dump for the exception
        $whoops = new Run();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new PlainTextHandler());
        $error = $whoops->handleException($ex);

        // We do not need to show the full path.
        $error = str_replace(' ' . WT_ROOT, ' /', $error);

        try {
            Log::addErrorLog($error);
        } catch (Throwable $ex2) {
            // Must have been a problem with the database.  Nothing we can do here.
        }

        if ($request->isXmlHttpRequest()) {
            return new Response(view('components/alert-danger', ['alert' => $error]), Response::STATUS_INTERNAL_SERVER_ERROR);
        }

        try {
            return $this->viewResponse('errors/unhandled-exception', [
                'title' => 'Error',
                'error' => $error,
            ], Response::STATUS_INTERNAL_SERVER_ERROR);
        } catch (Throwable $ex2) {
            // An error occured in the layout?  Just show the error.
            return new Response('<html><body><pre>' . e($error) . '</pre></body></html>', Response::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
