<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// $Id: module.php 13642 2012-03-24 13:06:08Z greg $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_search_block_WT_Module extends WT_Module implements WT_Module_Block {
	
	public function __construct() {
		// Load any local user translations
		if (is_dir(WT_MODULES_DIR.$this->getName().'/language')) {			
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php')) {
				Zend_Registry::get('Zend_Translate')->addTranslation(
					new Zend_Translate('array', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php', WT_LOCALE)
				);
			}
		}
	}
	
	// Extend class WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */ 'Fancy Search Block';
	}

	// Extend class WT_Module
	public function getDescription() {
		return /* I18N: Description of the "My page" module */ WT_I18N::translate('A (general) search block on home page or individual page');
	}
	
	// Get the stylesheet
	private function FancySearchBlockStyleSheet() {
		
		$header='if (document.createStyleSheet) {
			document.createStyleSheet("'.WT_MODULES_DIR.$this->getName().'/style.css"); // For Internet Explorer
		} else {
			jQuery("head").append(\'<link rel="stylesheet" href="'.WT_MODULES_DIR.$this->getName().'/style.css" type="text/css">\');
		}';
						
		return $header;		
	}	

	// Implement class WT_Module_Block
	public function getBlock($block_id, $template=true, $cfg=null) {
		global $controller;
		
		$id=$this->getName().$block_id;
		$class=$this->getName().'_block';
		
		$controller
			->addInlineJavaScript($this->FancySearchBlockStyleSheet().'
			jQuery("#'.$this->getName().'").load("search.php?ged=" + WT_GEDCOM + " form[name=\"searchform\"]", function(){	
				jQuery(this).find(".label").each(function(index) {
					switch(index) {
						case 0:
							break;
						case 1:
							var text = jQuery(this).text();	
							jQuery(this).text(text + ":");
							break;											
						default:
							jQuery(this).remove();
							break;						
					}
				});
				var count = jQuery(this).find(".value").length;
				jQuery(this).find(".value").each(function(index) {
					switch(index) {						
						case count - 1:
							jQuery(this).addClass("action").find("a[href*=action]").each(function(){
								var href = jQuery(this).attr("href");
								jQuery(this).attr("href", "search.php" + href);				
							});
							break;
						default:
							break;									
					}
				});				
								
				// Some themes need extra css for textcolor
				var WT_THEME   = WT_THEME_DIR.split("/")[1];
				if (WT_THEME == "justblack") jQuery(this).find("#search-page-table").css("color", "#e2e2e2");
				if (WT_THEME == "clouds") jQuery(this).find("#search-page-table .label").css("color", "#000");
				if (WT_THEME == "colors") jQuery(this).find("#search-page-table .label").css("color", "#333");
				if (WT_THEME == "webtrees") jQuery(this).find("#search-page-table .label").css("color", "#555");
			});
		');
		
		$title = '<span dir="auto">'.WT_I18N::translate('General search').'</span>';		
		$content = '<div id="'.$this->getName().'"><div class="loading-image"></div></div>';
		
		if ($template) {
			require WT_THEME_DIR.'templates/block_main_temp.php';
		} else {
			return $content;
		}
	}

	// Implement class WT_Module_Block
	public function loadAjax() {
		return false;
	}

	// Implement class WT_Module_Block
	public function isUserBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function isGedcomBlock() {
		return true;
	}

	// Implement class WT_Module_Block
	public function configureBlock($block_id) {
	}
}