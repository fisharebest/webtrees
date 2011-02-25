<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
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
// @version $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class tree_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('Tree');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('Adds a tab to the individual page which displays the interactive tree for the given individual.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 70;
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		return
			'if (jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title")=="'.$this->getName().'") {'.
			' treetab.sizeLines();'.
			' var outdiv = document.getElementById("out_treetab");'.
			' var parent = document.getElementById("subtab");'.
			' if (!parent) parent = document.getElementById("tabs");'.
			' outdiv.style.width = (parent.offsetWidth-30) + "px";'.
			'}';
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		ob_start();
		$inav = new WT_TreeNav($this->controller->pid,'treetab');
		$inav->generations = 5;
		$inav->zoomLevel = -1;
		$inav->drawViewport('treetab', "auto", "600px");
		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return true;
	}
	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}
}
