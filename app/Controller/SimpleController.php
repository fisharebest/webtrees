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
 * Controller for all popup pages
 */
class SimpleController extends PageController {
	/**
	 * Create content for a popup window.
	 * The page title is not used by all browsers.
	 */
	public function __construct() {
		parent::__construct();
		$this->setPageTitle(WT_WEBTREES);
	}

	/**
	 * Simple (i.e. popup) windows are deprecated.
	 *
	 * @param bool $popup
	 *
	 * @return $this
	 */
	public function pageHeader($popup = true) {
		return parent::pageHeader($popup);
	}

	/**
	 * Restrict access
	 *
	 * @param bool $condition
	 *
	 * @return $this
	 */
	public function restrictAccess($condition) {
		if ($condition !== true) {
			$this->addInlineJavascript('opener.window.location.reload(); window.close();');
			exit;
		}

		return $this;
	}
}
