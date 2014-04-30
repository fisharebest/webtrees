<?php
// Classes and libraries for module system
//
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class wooc_singlepage_pedigree_WT_Module extends WT_Module implements WT_Module_Report {

	public function __construct() {
		parent::__construct();
		if (file_exists(WT_ROOT.WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo')) {
			Zend_Registry::get('Zend_Translate')->addTranslation(new Zend_Translate('gettext', WT_ROOT.WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo', WT_LOCALE));
		}
	}

	// Extend class WT_Module
	public function getTitle() {
		// This text also appears in the .XML file - update both together
		return /* I18N: Name of a report */ WT_I18N::translate('Pedigree - single page');
	}

	// Extend class WT_Module
	public function getDescription() {
		// This text also appears in the .XML file - update both together
		return /* I18N: Description of the “Pedigree” module */ WT_I18N::translate('Print a pedigree chart on a single page.');
	}

	// Extend class WT_Module
	public function defaultAccessLevel() {
		return WT_PRIV_PUBLIC;
	}

	// Implement WT_Module_Report - a module can provide many reports
	public function getReportMenus() {
		global $controller;
		require_once WT_ROOT.WT_MODULES_DIR.$this->getName().'/class_pedigree.php';

		$menus=array();
		$menu=new WT_Menu(
			$this->getTitle(),
			'reportengine.php?ged='.urlencode(WT_GEDCOM).'&amp;action=setup&amp;report='.WT_MODULES_DIR.$this->getName().'/report_singlepage.xml&amp;pid='.$controller->getSignificantIndividual()->getXref(),
			'menu-report-single'.$this->getName()
		);
		$menus[]=$menu;
		return $menus;
	}
}
