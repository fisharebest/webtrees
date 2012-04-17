<?php
// A sidebar to show extra/non-genealogical information about an individual
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// $Id: module.php 11459 2011-05-04 23:37:08Z nigel $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class extra_info_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module/sidebar */ WT_I18N::translate('Extra information');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Extra information" module */ WT_I18N::translate('A sidebar showing non-genealogical information about an indivdual.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 10;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}

	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $SHOW_COUNTER, $controller;
		
		ob_start();
		$indifacts = $controller->getIndiFacts();
		if (count($indifacts)==0) {
			echo WT_I18N::translate('There are no Facts for this individual.');
		}
		foreach ($indifacts as $fact) {
			if (in_array($fact->getTag(), WT_Gedcom_Tag::getReferenceFacts())) {
				print_fact($fact, $controller->record);
			}
		}

		echo '<div id="hitcounter">';
		if ($SHOW_COUNTER && (empty($SEARCH_SPIDER))) {
			//print indi counter only if displaying a non-private person
			require WT_ROOT.'includes/hitcount.php';
			echo WT_I18N::translate('Hit Count:'). ' '. $hitCount;
		}
		echo '</div>';// close #hitcounter
		return ob_get_clean();
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}
}
