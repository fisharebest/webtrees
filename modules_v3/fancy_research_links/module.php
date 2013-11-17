<?php
/*
 * webtrees: Web based Family History software
 * Copyright (C) 2013 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
*/

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_research_links_WT_Module extends WT_Module implements WT_Module_Sidebar {
	
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
	
	// Extend WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */ 'Fancy Research Links';
	}
	
	public function getSidebarTitle() {
		return /* Title used in the sidebar */ WT_I18N::translate('Research links');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the module */ WT_I18N::translate('A sidebar tool to provide quick links to popular research web sites.');
	}

	// Implement WT_Module_Sidebar
	public function defaultSidebarOrder() {
		return 9;
	}

	// Implement WT_Module_Sidebar
	public function hasSidebarContent() {
		return true;
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarAjaxContent() {
		return false;
	}
	
	// Implement WT_Module_Sidebar
	public function getSidebarContent() {
		// code based on similar in function_print_list.php
		global $controller;
		
		// load the module stylesheet
		$html = $this->includeCss(WT_MODULES_DIR.$this->getName().'/style.css');	
		
		$controller->addInlineJavascript('
			jQuery("#'.$this->getName().' a").text("'.$this->getSidebarTitle().'");
			jQuery("a.research_link").each(function(){
				var url = jQuery(this).attr("href");
				jQuery(this).colorbox({
					width:		"75%",
					height:		"90%",
					fixed:		true,
					iframe:		true,
					modal:		true,
					title:		"<a href=\"" + url + "\" target=\"_blank\">" + url + "</a>"
				});					
			});
			jQuery("#research_status a.mainlink").click(function(e){
				e.preventDefault();
				jQuery(this).parent().find(".sublinks").toggle();
			});
		');	 
		
		$html .= '<ul id="research_status">';
		foreach ($this->getPluginList() as $plugin) {
			foreach ($controller->record->getFacts() as $key=>$value) {
				$fact = $value->getTag();
				if ($fact=="NAME") {
					$link = $plugin->create_link($value);
					$sublinks = $plugin->create_sublink($value);
				}
			}			
			if($sublinks) {
				$html.='<li><span class="ui-icon ui-icon-triangle-1-e left"></span><a class="mainlink" href="'.$link.'">'.$plugin->getName().'</a>';
				$html .= '<ul class="sublinks">';
				foreach ($sublinks as $sublink) {
					$html.='<li><span class="ui-icon ui-icon-triangle-1-e left"></span><a class="research_link" href="'.$sublink['link'].'">'.$sublink['title'].'</a></li>';				
				}				
				$html .= '</ul></li>';			
			}
			else { // default
				$html.='<li><span class="ui-icon ui-icon-triangle-1-e left"></span><a class="research_link" href="'.$link.'">'.$plugin->getName().'</a></li>';
			}
		}
		$html.= '</ul>';
		return $html;
	}
	
	// Scan the plugin folder for a list of plugins
	private function getPluginList() {
		$array=array();
		$dir=dirname(__FILE__).'/plugins/';
		$dir_handle=opendir($dir);
		while ($file=readdir($dir_handle)) {
			if (substr($file, -4)=='.php') {
				require dirname(__FILE__).'/plugins/'.$file;
				$class=basename($file, '.php').'_plugin';
				$array[$class]=new $class;
			}
		}
		closedir($dir_handle);
		ksort($array);
		return $array;
	}
	
	private function includeCss($css) {
		return
			'<script>
				if (document.createStyleSheet) {
					document.createStyleSheet("'.$css.'"); // For Internet Explorer
				} else {
					var newSheet=document.createElement("link");
					newSheet.setAttribute("href","'.$css.'");
					newSheet.setAttribute("type","text/css");
					newSheet.setAttribute("rel","stylesheet");
					document.getElementsByTagName("head")[0].appendChild(newSheet);
				}
			</script>';
	}

}

// Each plugin should extend the base_plugin class, and implement any functions included here
class research_base_plugin {
}

