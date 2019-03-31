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

use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\MiddlewareInterface;
use Fisharebest\Webtrees\RedirectResponse;
use Fisharebest\Webtrees\Request;
use Fisharebest\Webtrees\RequestHandlerInterface;
use Fisharebest\Webtrees\ResponseInterface;
use Fisharebest\Webtrees\ServerRequestInterface;
use Fisharebest\Webtrees\Session;
use function in_array;

/**
 * Middleware to wrap a request in a transaction.
 */
class CheckCsrf implements MiddlewareInterface
{
    private const EXCLUDE_ROUTES = [
        'language',
        'theme',
    ];

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            $route = $request->get('route');

            if (!in_array($route, self::EXCLUDE_ROUTES, true)) {
                $client_token  = $request->get('csrf', $request->headers->get('X_CSRF_TOKEN'));
                $session_token = Session::get('CSRF_TOKEN');

                if ($client_token !== $session_token) {
                    FlashMessages::addMessage(I18N::translate('This form has expired. Try again.'));

                    return new RedirectResponse($request->getRequestUri());
                }
            }
        }

        return $handler->handle($request);
    }
}
