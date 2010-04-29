<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2010 John Finlay
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Modules
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}
require_once WT_ROOT.'includes/classes/class_module.php';

class individual_ext_report_WT_Module extends WT_Module implements WT_Module_Report {
	// Extend class WT_Module
	public function getTitle() {
		return i18n::translate('Close Relatives Report');
	}

	// Extend class WT_Module
	public function getDescription() {
		return i18n::translate('Close Relatives Report');
	}

	// Extend class WT_Module
	public function defaultAccessLevel() {
		return WT_PRIV_USER;
	}

	// Implement WT_Module_Report - a module can provide many reports
	public function getReportMenus() {
		global $controller, $WT_IMAGE_DIR, $WT_IMAGES, $TEXT_DIRECTION;

		if ($TEXT_DIRECTION=="rtl") $ff="_rtl"; else $ff="";

		if ($controller && isset($controller->pid)) {
			$pid='&amp;pid='.$controller->pid;
		} elseif ($controller && isset($controller->rootid)) {
			$pid='&amp;pid='.$controller->rootid;
		} else {
			$pid='';
		}
		
		$menus=array();
		$menu=new Menu($this->getTitle(), 'reportengine.php?ged='.urlencode(WT_GEDCOM).'&amp;action=setup&amp;report=modules/'.$this->getName().'/report.xml'.$pid);
		$menu->addIcon($WT_IMAGE_DIR."/".$WT_IMAGES['indis']['small']);
		$menu->addClass("submenuitem$ff", "submenuitem_hover$ff", "submenu$ff", "icon_small_reports");
		$menus[]=$menu;

		return $menus;
	}
}
