<?php
// Base controller for all popup pages
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_Controller_Simple extends WT_Controller_Base {

	// Popup windows don't always need a title
	public function __construct() {
		$this->setTitle(WT_WEBTREES);
	}

	// Simple (i.e. popup) windows are deprecated.
	public function pageHeader() {
		global $view;
		$view='simple';
		parent::pageHeader();
	}
	
	// Restrict access
	public function requireAdminLogin() {
		if (!WT_USER_IS_ADMIN) {
			$this->addInlineJavaScript('opener.window.location.reload(); window.close();');
			exit;
		}
	}
	
	// Restrict access
	public function requireManagerLogin() {
		if (!WT_USER_GEDCOM_ADMIN) {
			$this->addInlineJavaScript('opener.window.location.reload(); window.close();');
			exit;
		}
	}
	
	// Restrict access
	public function requireMemberLogin() {
		if (!WT_USER_ID) {
			$this->addInlineJavaScript('opener.window.location.reload(); window.close();');
			exit;
		}
	}
}
