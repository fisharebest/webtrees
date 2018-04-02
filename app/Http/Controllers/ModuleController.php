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

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for module actions.
 */
class ModuleController extends AbstractBaseController {
	/**
	 * Perform an HTTP action for one of the modules.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function action(Request $request): Response {
		/** @var User $user */
		$user = $request->attributes->get('user');

		$module_name = $request->get('module');

		// Check that the module is enabled.
		// The module itself will need to check any tree-level access,
		// which may be different for each component (tab, menu, etc.) of the module.
		$module = Module::getModuleByName($module_name);

		// We'll call a function such as Module::getFooBarAction()
		$verb   = strtolower($request->getMethod());
		$action = $request->get('action');
		$method = $verb . $action . 'Action';

		// Actions with "Admin" in the name are for administrators only.
		if (strpos($action, 'Admin') !== false && !Auth::isAdmin($user)) {
			throw new AccessDeniedHttpException;
		}

		if (method_exists($module, $method)) {
			return $module->$method($request);
		} else {
			throw new NotFoundHttpException('Module not found');
		}
	}
}
