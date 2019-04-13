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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use function str_replace;

/**
 * Controller for error handling.
 */
class ErrorController extends AbstractBaseController implements StatusCodeInterface
{
    /**
     * Convert an exception into an error message
     *
     * @param HttpException $ex
     *
     * @return ResponseInterface
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
     * @return ResponseInterface
     */
    public function ajaxErrorResponse(HttpException $ex): ResponseInterface
    {
        return response(view('components/alert-danger', [
            'alert' => $ex->getMessage(),
        ]), $ex->getStatusCode());
    }

    /**
     * Convert an exception into an error message
     *
     * @param ServerRequestInterface $request
     * @param Throwable              $ex
     *
     * @return ResponseInterface
     */
    public function unhandledExceptionResponse(ServerRequestInterface $request, Throwable $ex): ResponseInterface
    {
        // Create a stack dump for the exception
        $trace = $ex->getMessage() . PHP_EOL . $ex->getTraceAsString();
        $trace = str_replace(WT_ROOT, '…/', $trace);

        try {
            Log::addErrorLog($trace);
        } catch (Throwable $ex2) {
            // Must have been a problem with the database.  Nothing we can do here.
        }

        if ($request->getHeaderLine('X-Requested-With') !== '') {
            return response(view('components/alert-danger', ['alert' => $trace]), self::STATUS_INTERNAL_SERVER_ERROR);
        }

        return $this->viewResponse('errors/unhandled-exception', [
            'title' => 'Error',
            'error' => $trace,
        ], self::STATUS_INTERNAL_SERVER_ERROR);
    }
}
