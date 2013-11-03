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
// $Id: module.php 13838 2013-03-24 v1.0 $

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class fancy_branches_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Menu {
		
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module  */ WT_I18N::translate('Fancy Branches').'<span class="nowrap">'.help_link('test').'</span>';
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the module */ WT_I18N::translate('A replacement for the standard webtrees Branches list');
	}
	
	// Extend WT_Module_Config
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
								
			$controller=new WT_Controller_Page;
			$controller
				->requireAdminLogin()
				->setPageTitle(WT_I18N::translate('Configuration page for the Simpl Branches Module'))
				->pageHeader()
				->addInlineJavaScript ('
					jQuery("form input:checkbox").click(function(){
						this.value = this.checked ? 1 : 0;      
					});
				');	
			
			$save = WT_Filter::postBool('save');
			if (isset($save)) {
				isset($_POST['NEW_SB']) ? $value = 1 : $value = 0;
				set_module_setting($this->getName(), 'SB',  $value);				
				AddToLog($this->getTitle().' config updated', 'config');
			}			
			
			$SB = get_module_setting($this->getName(), 'SB');
			if (!isset($SB)) $SB = '1';
			$SB == 1 ? $checked = 'checked="checked"' : $checked = "";
			
			echo '
				<h2>'.$controller->getPageTitle().'</h2>
				<form method="post" name="configform" action="'.$this->getConfigLink().'">
					<label>'.WT_I18N::translate('Use d\'Aboville numbering system').'</label>
					<input type="checkbox" name="NEW_SB" value="'.$SB.'" '.$checked.' />
					<input type="submit" name="save" value="'.WT_I18N::translate('Save').'" />
				</form>';				
			break;
		}
	}
	
	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}		
	
	// Implement WT_Module_Menu
	public function defaultMenuOrder() {
		return 999;
	}   

	// Implement WT_Module_Menu
	public function getMenu() {
		// We don't actually have a menu - this is just a convenient "hook" to execute code at the right time during page execution
		global $controller;    
		
		if (WT_SCRIPT_NAME == 'branches.php') {
			echo $this->includeCss();		
			$controller
				->addExternalJavaScript(WT_MODULES_DIR.$this->getName().'/js/jquery.treeview.js')
				->addInlineJavaScript('			
				jQuery("#branch-list")
					.before("<div id=\"treecontrol\"><a href=\"#\">'.WT_I18N::translate('Collapse all').'</a> | <a href=\"#\">'.WT_I18N::translate('Expand all').'</a></div>")
					.before("<div class=\"loading-image\">&nbsp;</div>");
				
				jQuery("legend").remove();
				jQuery(jQuery("#branches-page ol").get().reverse()).each(function(){
					var html = jQuery(this).html();
					if (html == "") {
						jQuery(this).remove();
					}
					else { 
  						jQuery(this).replaceWith("<ul>" + html +"</ul>") 
					}
				});
				jQuery("#branches-page ul:first").attr("id", "branch-list");
									
				jQuery("li[title='.WT_I18N::translate('Private').']").hide();
			');
			
			// Instigate the "d'Aboville" numbering system. Use it by default. The user can change it on the configuration page.
			$SB = get_module_setting($this->getName(), 'SB');
			if (!isset($SB) || $SB == 1) {
				$controller->addInlineJavaScript('
					jQuery("#branches-page ul, #branches-page ul li").addClass("aboville");
				');
			}
			
			$controller->addInlineJavaScript('									
				var content = jQuery("fieldset").html();
				jQuery("fieldset").remove();
				jQuery(".loading-image").after(content);
				
				jQuery("#branch-list").treeview({
					collapsed: true,
					animated: "slow",
					control:"#treecontrol"
				});
				jQuery("#branch-list").css("visibility", "visible");
				jQuery(".loading-image").css("display", "none");
			');	
		}
		return null;
	}
	
	// Implement the css stylesheet for this module	
	private function includeCss() {
		return $this->getScript(WT_MODULES_DIR.$this->getName().'/style.css');	
	}	
	
	private function getScript($css) {
		return
			'<script>
				if (document.createStyleSheet) {
					document.createStyleSheet("'.$css.'"); // For Internet Explorer
				} else {
					var newSheet=document.createElement("link");
					newSheet.setAttribute("rel","stylesheet");
					newSheet.setAttribute("type","text/css");
					newSheet.setAttribute("href","'.$css.'");
					document.getElementsByTagName("head")[0].appendChild(newSheet);
				}
			</script>';
	}	
}