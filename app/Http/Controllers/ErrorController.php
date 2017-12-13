<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for error handling.
 */
class ErrorController extends BaseController {
	/**
	 * No route was match?  Send the user somewhere sensible, if we can.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function noRouteFound(Request $request): Response {
		$tree = $request->attributes->get('tree');

		// The tree exists, we have access to it, and it is fully imported.
		if ($tree instanceof Tree && $tree->getPreference('imported') === '1') {
			return new RedirectResponse(route('tree-page', ['ged' => $tree->getName()]));
		}

		// Not logged in?
		if (!Auth::check()) {
			return new RedirectResponse(Html::url('login.php', ['url' => $request->getRequestUri()]));
		}

		// No tree or tree not imported?
		if (Auth::isAdmin()) {
			$response = new RedirectResponse(Html::url('admin_trees_manage.php', []));
		}

		return $this->viewResponse('errors/no-tree-access', []);
	}
}
