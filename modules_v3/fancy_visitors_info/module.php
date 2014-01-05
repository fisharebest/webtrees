<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
// Copyright (C) 2014 JustCarmen.
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_visitors_info_WT_Module extends WT_Module implements WT_Module_Sidebar {
	// Extend WT_Module
	public function getTitle() {
		return  /* Name of a module (not translatable) */ 'Fancy Visitors Info';
	}
	
	public function getSidebarTitle() {
		return /* Title used in the sidebar */ WT_I18N::translate('Visitors information');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the â€œExtra informationâ€� module */ WT_I18N::translate('A sidebar showing extra information for visitors (non members).');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 10;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() { // only show the sidebare block to non logged in visitors.
		if (getUserId() == 0) return true;
	}
	
	protected $sourceCount = null;
	protected $mediaCount = null;
	
	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		global $controller;	
		
		ob_start();
		
		if ($this->get_source_count()==0) {
			echo '<p>'.WT_I18N::translate('There are no Source citations for this individual.').'</p>';
		}
		else {
			echo '<p>'.WT_I18N::plural('There is %s Source citation for this individual', 'There are %s Source citations for this individual.', $this->get_source_count(), $this->get_source_count()).'</p>';
		}
		if ($this->get_media_count()==0) {
			echo '<p>'.WT_I18N::translate('There are no media objects for this individual.').'</p>';
		}
		else {
			echo '<p>'.WT_I18N::plural('There is %s media object (photo and/or document) linked to this individual.', 'There are %s media objects (photos and/or documents) linked to this individual.', $this->get_media_count(), $this->get_media_count()).'</p>';
		}
		if($this->get_source_count() > 0 || $this->get_media_count() > 0) {
			echo '<p>'.WT_I18N::translate('Only members can see all sources and media objects for this individual.').'</p>';
		}
		
		$controller
			->addInlineJavascript('
				jQuery(document)
					.ajaxSend(function(){
						jQuery("#'.$this->getName().' a").text("'.$this->getSidebarTitle().'");
					})
					.ajaxComplete(function() { 
					jQuery("#sidebarAccordion").accordion({
						active:0,
						heightStyle: "content",
						collapsible: true,
						icons:{ "header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" }
					});
				});
			');
		
		return '<div id="sb_'.$this->getName().'_content">'.ob_get_clean().'</div>';			
	}

	private function get_source_count() {
		global $controller;
		// source: WT_Fact::getCitations()
		$ct = preg_match_all('/\n([1-2] SOUR @(' . WT_REGEX_XREF . ')@(?:\n[3-9] .*)*)/', $controller->record->getGedcom(), $matches, PREG_SET_ORDER);
		foreach ($controller->record->getSpouseFamilies() as $sfam) {
			$ct += preg_match_all('/\n([1-2] SOUR @(' . WT_REGEX_XREF . ')@(?:\n[3-9] .*)*)/', $sfam->getGedcom(), $matches, PREG_SET_ORDER);
		}		
		return $ct;
	}
	
	private function get_media_count() {
		global $controller;
		// source: WT_Fact::getMedia().
		$ct = preg_match_all('/\n[1-2] OBJE @(' . WT_REGEX_XREF . ')@/', $controller->record->getGedcom(), $matches);
		foreach ($controller->record->getSpouseFamilies() as $sfam) {
			$ct += preg_match_all('/\n[1-2] OBJE @(' . WT_REGEX_XREF . ')@/', $sfam->getGedcom(), $matches);
		}
		return $ct;
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return '';
	}
}