<?php
/*
 * Fancy Branches Module
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2014 webtrees development team.
 * Copyright (C) 2014 JustCarmen.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 */

use WT\Auth;
use WT\Log;

class fancy_branches_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Menu {

	// Extend WT_Module
	public function getTitle() {
		return /* Name of a module (not translatable) */  'Fancy Branches';
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the module */ WT_I18N::translate('A replacement for the standard webtrees Branches list');
	}

	// Extend WT_Module_Config
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':

			require WT_ROOT.'includes/functions/functions_edit.php';

			$controller=new WT_Controller_Page;
			$controller
				->restrictAccess(Auth::isAdmin())
				->setPageTitle(WT_I18N::translate('Configuration page for the Simpl Branches Module'))
				->pageHeader()
				->addInlineJavaScript ('
					jQuery("form input:checkbox").click(function(){
						this.value = this.checked ? 1 : 0;
					});
				');

			$save = WT_Filter::postBool('save');
			if (isset($save)) {
				$this->setSetting('SB',  WT_Filter::postInteger('NEW_SB'));
				Log::addConfigurationLog($this->getTitle().' config updated');
			}

			$SB = $this->getSetting('SB');
			echo '
				<h2>'.$controller->getPageTitle().'</h2>
				<form method="post" name="configform" action="'.$this->getConfigLink().'">
					<label>'.WT_I18N::translate('Use d\'Aboville numbering system').'</label>'
					.two_state_checkbox('NEW_SB', $SB).'
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
			// load the module stylesheet
			echo $this->includeCss(WT_MODULES_DIR.$this->getName().'/style.css');

			$controller
				->addExternalJavaScript(WT_MODULES_DIR.$this->getName().'/js/jquery.treeview.js')
				->addInlineJavaScript('
				jQuery("#branches-page form")
					.after("<div id=\"treecontrol\"><a href=\"#\">'.WT_I18N::translate('Collapse all').'</a> | <a href=\"#\">'.WT_I18N::translate('Expand all').'</a></div>")
					.after("<div class=\"loading-image\">&nbsp;</div>");

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
			$SB = $this->getSetting('SB');
			if (!isset($SB) || $SB == 1) {
				$controller->addInlineJavaScript('
					jQuery("#branch-list, #branch-list ul, #branch-list li").addClass("aboville");
				');
			}

			$controller->addInlineJavaScript('
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