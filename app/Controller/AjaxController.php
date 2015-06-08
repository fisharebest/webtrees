<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees\Controller;

/**
 * Base controller for all popup pages
 */
class AjaxController extends BaseController {
	/**
	 * Print the page header, using the theme
	 *
	 * @return $this
	 */
	public function pageHeader() {
		// Ajax responses are always UTF8
		header('Content-Type: text/html; charset=UTF-8');
		// We've displayed the header - display the footer automatically
		register_shutdown_function(array($this, 'pageFooter'));

		return $this;
	}

	/**
	 * Print the page footer, using the theme
	 */
	public function pageFooter() {
		// Ajax responses may have Javascript
		echo $this->getJavascript();
	}

	/**
	 * Restrict access.
	 *
	 * @param bool $condition
	 *
	 * @return $this
	 */
	public function restrictAccess($condition) {
		if ($condition !== true) {
			http_response_code(403);
			exit;
		}

		return $this;
	}
}
