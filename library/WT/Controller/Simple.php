<?php
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

/**
 * Class WT_Controller_Simple - Controller for all popup pages
 */
class WT_Controller_Simple extends WT_Controller_Page {
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
	 * @return WT_Controller_Simple
	 */
	public function pageHeader() {
		global $view;

		$view = 'simple';
		parent::pageHeader();

		return $this;
	}

	/**
	 * Restrict access
	 *
	 * @param boolean $condition
	 *
	 * @return WT_Controller_Simple
	 */
	public function restrictAccess($condition) {
		if ($condition !== true) {
			$this->addInlineJavascript('opener.window.location.reload(); window.close();');
			exit;
		}

		return $this;
	}
}
