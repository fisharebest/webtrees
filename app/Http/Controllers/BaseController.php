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

use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\View;
use Symfony\Component\HttpFoundation\Response;
use Fisharebest\Webtrees\Controller\PageController as LegacyBaseController;

/**
 * Common functions for all controllers
 *
 * The "Legacy" base controller was used to inject Javascript into responses.
 * Once this is updated, we can remove it.
 */
class BaseController extends LegacyBaseController {
	protected $layout = 'layouts/default';

	/**
	 * Create a response object from a view.
	 *
	 * @param string  $name
	 * @param mixed[] $data
	 *
	 * @return Response
	 */
	protected function viewResponse($name, $data): Response {
		$theme = Theme::theme();

		$html = View::make($this->layout, [
			'content'                => View::make($name, $data),
			'tree'                   => $this->tree(),
			'theme_head'             => $theme->head($this),
			'theme_body_header'      => $theme->bodyHeader(),
			'theme_footer_container' => $theme->footerContainer(),
			'javascript'             => $this->getJavascript() . $theme->hookFooterExtraJavascript(),
		]);

		return new Response($html);
	}
}
