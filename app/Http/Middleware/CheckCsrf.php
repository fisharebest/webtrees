<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Http\RequestHandlers\Logout;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectLanguage;
use Fisharebest\Webtrees\Http\RequestHandlers\SelectTheme;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function in_array;

/**
 * Middleware to wrap a request in a transaction.
 */
class CheckCsrf implements MiddlewareInterface
{
    private const EXCLUDE_ROUTES = [
        Logout::class,
        SelectLanguage::class,
        SelectTheme::class,
    ];

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $route = Validator::attributes($request)->route();

            if (!in_array($route->name, self::EXCLUDE_ROUTES, true)) {
                $params        = (array) $request->getParsedBody();
                $client_token  = $params['_csrf'] ?? $request->getHeaderLine('X-CSRF-TOKEN');
                $session_token = Session::get('CSRF_TOKEN');

                unset($params['_csrf']);

                $request = $request->withParsedBody($params);

                if ($client_token !== $session_token) {
                    if ($client_token === '') {
                        FlashMessages::addMessage(I18N::translate('The form data is incomplete. Perhaps you need to increase max_input_vars on your server?'));
                    } else {
                        FlashMessages::addMessage(I18N::translate('This form has expired. Try again.'));
                    }

                    return redirect((string) $request->getUri());
                }
            }
        }

        return $handler->handle($request);
    }
}
