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

namespace Fisharebest\Webtrees\Http\Middleware;

use Closure;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Middleware to wrap a request in a transaction.
 */
class CheckCsrf {
	/**
	 * @param Request $request
	 * @param Closure $next
	 *
	 * @return Response
	 * @throws AccessDeniedHttpException
	 */
	public function handle(Request $request, Closure $next): Response {
		$client_token  = $request->get('csrf', $request->headers->get('X_CSRF_TOKEN'));
		$session_token = Session::get('CSRF_TOKEN');

		if ($client_token !== $session_token) {
			throw new AccessDeniedHttpException(I18N::translate('This form has expired. Try again.'));
		}

		return $next($request);
	}
}
