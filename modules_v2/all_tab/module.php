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

class all_tab_WT_Module extends WT_Module implements WT_Module_Tab {
	// Extend class WT_Module
	public function getTitle() {
		return WT_I18N::translate('All');
	}

	// Extend class WT_Module
	public function getDescription() {
		return WT_I18N::translate('Adds a tab to the individual page which displays the contents of all other active tabs.');
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 90;
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {

		$out = '<div id="'.$this->getName().'_content">';
		$out .= "<!-- all tab doesn't have it's own content -->";
		$out .= "</div>";
		return $out;
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		return '';
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		$out='if (jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title")=="'.$this->getName().'") {';
		foreach ($this->controller->tabs as $tab) {
			if ($tab->getName()!=$this->getName()) {
				$out.=
					' if (!tabCache["'.$tab->getName().'"]) {'.
					'  jQuery("#'.$tab->getName().'").load("'.$this->controller->indi->getRawUrl().'&action=ajax&module='.$tab->getName().'");'.
					'  tabCache["'.$tab->getName().'"] = true;'.
					' }';
			}
		}
		$out.=
			' jQuery("#tabs > div").each(function() {'.
			'  if (this.name!="'.$this->getName().'") {'.
			'   jQuery(this).removeClass("ui-tabs-hide");'.
			'  }'.
			' });'.
			'}';
		if (array_key_exists('googlemap', WT_Module::getActiveModules())) {
			global $GOOGLEMAP_PH_CONTROLS;
			$out.='if (jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title")=="'.$this->getName().'") {'.
				' loadMap();';
			if ($GOOGLEMAP_PH_CONTROLS) {
				$out.=' GEvent.addListener(map,"mouseout", function() { map.hideControls(); });'.
					' GEvent.addListener(map,"mouseover",function() { map.showControls(); });'.
					' GEvent.trigger    (map,"mouseout");';
			}
			$out.=' map.setMapType(GOOGLEMAP_MAP_TYPE);'.
				' SetMarkersAndBounds();'.
				' ResizeMap();'.
				'}';
		}
		return $out;
	}
}
