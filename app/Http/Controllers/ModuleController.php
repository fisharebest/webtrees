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

use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for module actions.
 */
class ModuleController extends BaseController {
	/**
	 * Perform an HTTP action for one of the modules.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function action(Request $request): Response {
		$module_name   = $request->get('module');
		$module_action = $request->get('action');
		$module        = Module::getModuleByName($module_name);

		if ($module === null) {
			throw new NotFoundHttpException('Not found');
		}

		$function = $request->getMethod() ;
	}
}
