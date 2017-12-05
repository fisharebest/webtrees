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

use Fisharebest\Webtrees\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for help text.
 */
class HelpTextController extends BaseController {
	/**
	 * Help for dates.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function helpText(Request $request): Response {
		$help_topic = $request->get('topic');
		$title      = '';
		$text       = '';

		require 'help_text.php';

		$help_text = view('modals/help', [
			'title' => $title,
			'text'  => $text,
		]);

		return new Response($help_text);
	}

	private function dateHelp() {

	}
}
