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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Carbon;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function session_destroy;
use function session_status;

use const PHP_SESSION_ACTIVE;

/**
 * Middleware to activate sessions.
 */
class UseSession implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Some sites (e.g. Wordpress/NinjaFirewall) use the PHP auto_prepend_file
        // setting to run their own startup code - which may start a session.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Sessions
        Session::start($request);

        $user = Auth::user();

        // Update the last-login time no more than once a minute.
        if (Session::get('masquerade') === null) {
            $last = Carbon::createFromTimestamp((int) $user->getPreference(User::PREF_TIMESTAMP_ACTIVE));

            if (Carbon::now()->subMinute()->gt($last)) {
                $user->setPreference(User::PREF_TIMESTAMP_ACTIVE, (string) Carbon::now()->unix());
            }
        }

        $request = $request->withAttribute('user', $user);

        $response = $handler->handle($request);

        Session::save();

        return $response;
    }
}
