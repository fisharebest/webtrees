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
 * Class WT_Controller_Ajax - Base controller for all popup pages
 */
class WT_Controller_Ajax extends WT_Controller_Base {

	/**
	 * @return $this
	 */
	public function pageHeader() {
		// We have finished writing session data, so release the lock
		Zend_Session::writeClose();
		// Ajax responses are always UTF8
		header('Content-Type: text/html; charset=UTF-8');
		$this->page_header=true;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function pageFooter() {
		// Ajax responses may have Javascript
		echo $this->getJavascript();
		return $this;
	}

	/**
	 * Restrict access.
	 *
	 * @param boolean $condition
	 *
	 * @return $this
	 */
	public function restrictAccess($condition) {
		if ($condition !== true) {
			header('HTTP/1.0 403 Access Denied');
			exit;
		}

		return $this;
	}
}
