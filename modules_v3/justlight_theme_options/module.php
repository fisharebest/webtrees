<?php
/*
 * JustLight Theme Options Module
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

class justlight_theme_options_WT_Module extends WT_Module implements WT_Module_Config {

	public function __construct() {
		parent::__construct();
		// Load any local user translations
		if (is_dir(WT_MODULES_DIR.$this->getName().'/language')) {
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo')) {
				WT_I18N::addTranslation(
					new Zend_Translate('gettext', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.mo', WT_LOCALE)
				);
			}
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php')) {
				WT_I18N::addTranslation(
					new Zend_Translate('array', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.php', WT_LOCALE)
				);
			}
			if (file_exists(WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.csv')) {
				WT_I18N::addTranslation(
					new Zend_Translate('csv', WT_MODULES_DIR.$this->getName().'/language/'.WT_LOCALE.'.csv', WT_LOCALE)
				);
			}
		}
	}

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: Name of a module  */ WT_I18N::translate('JustLight Theme Options');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the module */ WT_I18N::translate('Set options for the JustLight theme within the admin interface');
	}

	// Set default module options
	private function setDefault($key) {
		$JL_DEFAULT = array(			
			'COMPACT_MENU'			=> '0',
			'COMPACT_MENU_REPORTS'	=> '1',
			'MEDIA_MENU'			=> '0',
			'MEDIA_LINK'			=> '',
			'SUBFOLDERS'			=> '1'
		);
		return $JL_DEFAULT[$key];
	}

	// Get module options
	public function options($key) {
		$JL_OPTIONS = unserialize($this->getSetting('JL_OPTIONS'));

		$key = strtoupper($key);
		if(empty($JL_OPTIONS) || (is_array($JL_OPTIONS) && !array_key_exists($key, $JL_OPTIONS))) {
			return $key === 'MENU' ? $this->getMenu() : $this->setDefault($key);
		} else {
			return $JL_OPTIONS[$key];
		}
	}

	private function getMenu() {
		$menulist = array(
			array(
				'title'		=> WT_I18N::translate('View'),
				'label'		=> 'compact',
				'sort' 		=> '0',
				'function' 	=> 'getCompactMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Media'),
				'label'		=> 'media',
				'sort' 		=> '0',
				'function' 	=> 'getMediaMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Home page'),
				'label'		=> 'homepage',
				'sort' 		=> '1',
				'function' 	=> 'getGedcomMenu'
			),
			array(
				'title'		=> WT_I18N::translate('My page'),
				'label'		=> 'mypage',
				'sort' 		=> '2',
				'function' 	=> 'getMyPageMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Charts'),
				'label'		=> 'charts',
				'sort' 		=> '3',
				'function' 	=> 'getChartsMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Lists'),
				'label'		=> 'lists',
				'sort' 		=> '4',
				'function' 	=> 'getListsMenu'
			),
			array(
				'title'		=>	WT_I18N::translate('Calendar'),
				'label'		=> 'calendar',
				'sort' 		=> '5',
				'function' 	=> 'getCalendarMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Reports'),
				'label'		=> 'reports',
				'sort' 		=> '6',
				'function' 	=> 'getReportsMenu'
			),
			array(
				'title'		=> WT_I18N::translate('Search'),
				'label'		=> 'search',
				'sort' 		=> '7',
				'function' 	=> 'getSearchMenu'
			),
		);
		
		$modules = $this->getActiveMenu(8);
		if ($modules) {
			return array_merge($menulist, $modules);
		}
		else {
			return $menulist;
		}
	}

	private function getActiveMenu($sort) {
		$modules=WT_Module::getActiveMenus();
		
		if ( count($modules) > 0) {
			$fakeMenus 	= array('custom_js', 'fancy_imagebar', 'fancy_branches');

			foreach ($modules as $module) {
				$msort = in_array($module->getName(), $fakeMenus) ? 99 : $sort;
				$menulist[] = array(
					'title'		=> $module->getTitle(),
					'label'		=> $module->getName(),
					'sort' 		=> $msort,
					'function' 	=> 'getModuleMenu'
				);
				$sort++;
			}
			return $this->sortArray($menulist, 'sort');
		}
	}

	// function to check if a module menu is still active (after options are set)
	public function checkModule($menulist) {
		$lastItem = end($menulist);
		$sort = $lastItem['sort'] + 1;
		$modules=$this->getActiveMenu($sort);
		
		// delete deactivated modules from the list
		foreach ($menulist as $menu) {
			if	($menu['function'] !== 'getModuleMenu') {
				$new_list[] = $menu;
			}
			if	($modules && $menu['function'] == 'getModuleMenu' && $this->searchArray($modules, 'label', $menu['label'])) {
				$new_list[] = $menu;
			}
		}

		// add newly activated modules to the list
		if($modules) {
			foreach ($modules as $module) {
				if(!$this->searchArray($menulist, 'label', $module['label'])) {
					$new_list[] = $module;
				}
			}
		}
		return $new_list;
	}

	public function getFolderList() {
		global $MEDIA_DIRECTORY;
		$folders = WT_Query_Media::folderList();
		foreach ($folders as $key => $value) {
			if($key == null && empty($value)) {
				$folderlist[$MEDIA_DIRECTORY] = strtoupper(WT_I18N::translate(substr($MEDIA_DIRECTORY,0,-1)));
			} else {
				if (count(glob(WT_DATA_DIR.$MEDIA_DIRECTORY.$value.'*')) > 0 ) {
					$folder = array_filter(explode("/", $value));
					// only list first level folders
					if (!empty($folder) && !array_search($folder[0], $folderlist)) {
						$folderlist[$folder[0] . '/'] = WT_I18N::translate($folder[0]);
					}
				}
			}
		}
		return $folderlist;
	}

	// Search within a multiple dimensional array
	private function searchArray($array, $key, $value) {
		$results = array();
		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}
			foreach ($array as $subarray) {
				$results = array_merge($results, $this->searchArray($subarray, $key, $value));
			}
		}
		return $results;
	}

	// Sort the array according to the $key['SORT'] input.
	private function sortArray($array, $sort_by){
		foreach ($array as $pos =>  $val) {
			$tmp_array[$pos] = $val[$sort_by];
		}
		asort($tmp_array);
		
		$return_array = array();
		foreach ($tmp_array as $pos =>  $val){
			$return_array[$pos]['title'] = $array[$pos]['title'];
			$return_array[$pos]['label'] = $array[$pos]['label'];
			$return_array[$pos]['sort'] = $array[$pos]['sort'];
			$return_array[$pos]['function'] = $array[$pos]['function'];
		}
		return $return_array;
    }

	// set an extra class for some menuitems
	private function getStatus($label) {
		if ($label == 'homepage' || $label == 'mypage') {
		 	$status = ' ui-state-disabled';
		} elseif ($label == 'charts' || $label == 'lists' || $label == 'calendar') {
			$status = ' menu_extended';
		} elseif ($label == 'reports') {
			$status = ' menu_extended menu_reports';
		} elseif ($label == 'compact') {
			$status = ' menu_compact';
		} elseif ($label == 'media') {
			$status = ' menu_media';
		} else {
			$status = '';
		}
		return $status;
	}	

	// Extend WT_Module_Config
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			$this->config();
			break;
		case 'admin_reset':
			$this->jb_reset();			
			$this->config();
			break;
		default:
			header('HTTP/1.0 404 Not Found');
		}
	}

	// Reset all settings to default
	private function jb_reset() {
		WT_DB::prepare("DELETE FROM `##module_setting` WHERE setting_name LIKE 'JL%'")->execute();
		Log::addConfigurationLog($this->getTitle().' reset to default values');
	}

	private function config() {

		if (WT_Filter::postBool('save') && WT_Filter::checkCsrf()) {
			$NEW_JL_OPTIONS = WT_Filter::postArray('NEW_JL_OPTIONS');
			$NEW_JL_OPTIONS['MENU'] = $this->sortArray(WT_Filter::postArray('NEW_JB_MENU'), 'sort');		
			
			$this->setSetting('JL_OPTIONS',  serialize($NEW_JL_OPTIONS));
			Log::addConfigurationLog($this->getTitle().' config updated');
		}

		require WT_ROOT.'includes/functions/functions_edit.php';
		$controller=new WT_Controller_Page;
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(WT_I18N::translate('Options for the JustLight theme'))
			->pageHeader();

		$controller->addInlineJavaScript ('
			function include_css(css_file) {
				var html_doc = document.getElementsByTagName("head")[0];
				var css = document.createElement("link");
				css.setAttribute("rel", "stylesheet");
				css.setAttribute("type", "text/css");
				css.setAttribute("href", css_file);
				html_doc.appendChild(css);
			}
			include_css("'.WT_MODULES_DIR.$this->getName().'/style.css");

			function toggleFields(checkbox, field, reverse) {
				var checkbox = jQuery(checkbox).find("input[type=checkbox]");
				var field = jQuery(field)
				if(!reverse) {
					if ((checkbox).is(":checked")) field.show("slow");
					else field.hide("slow");
					checkbox.click(function(){
						if (this.checked) field.show("slow");
						else field.hide("slow");
					});
				}
				else {
					if ((checkbox).is(":checked")) field.hide("slow");
					else field.show("slow");
					checkbox.click(function(){
						if (this.checked) field.hide("slow");
						else field.show("slow");
					});
				}
			}

			toggleFields("#compact_menu", "#reports");
			toggleFields("#media_menu", "#media_link, #subfolders");

			jQuery("#compact_menu input[type=checkbox]").click(function() {
				if (jQuery("#reports input[type=checkbox]").is(":checked")) var menu_extended = jQuery(".menu_extended");
				else var menu_extended = jQuery(".menu_extended:not(.menu_reports)");

				if (this.checked) {
					jQuery(".menu_compact").insertAfter(jQuery(".menu_extended:last")).show();
					jQuery(menu_extended).appendTo(jQuery("#trashMenu")).hide();
				}
				else {
					jQuery(menu_extended).insertAfter(jQuery(".menu_compact")).show();
					jQuery(".menu_compact").appendTo(jQuery("#trashMenu")).hide();

				}
				jQuery("#sortMenu, #trashMenu").trigger("sortupdate")
			});

			jQuery("#reports input[type=checkbox]").click(function() {
				if (this.checked) jQuery(".menu_reports").appendTo(jQuery("#trashMenu")).hide();
				else jQuery(".menu_reports").insertAfter(jQuery(".menu_compact")).show();
				jQuery("#sortMenu, #trashMenu").trigger("sortupdate")
			});

			jQuery("#media_menu input[type=checkbox]").click(function() {
				if (this.checked) {
					jQuery(".menu_media").appendTo(jQuery("#sortMenu")).show();
				}
				else {
					jQuery(".menu_media").appendTo(jQuery("#trashMenu")).hide();
				}
				jQuery("#sortMenu, #trashMenu").trigger("sortupdate")
			});

			jQuery("#media_link select").each(function() {
				if(jQuery(this).val() == "'.$this->options('media_link').'") {
					jQuery(this).prop("selected", true);
				}
			});

			 jQuery("#sortMenu").sortable({
				items: "li:not(.ui-state-disabled)"
			}).disableSelection();

			//-- update the order numbers after drag-n-drop sorting is complete
			jQuery("#sortMenu").bind("sortupdate", function(event, ui) {
				jQuery("#"+jQuery(this).attr("id")+" input[name*=sort]").each(
					function (index, value) {
						if(value.value < 99) value.value = index+1;
					}
				);
				jQuery("#trashMenu input[name*=sort]").attr("value", "0");
			});
		');

		// Admin page content
		$html = '<div id="jl_options"><div id="error" style="display:none"></div><h2>'.$this->getTitle().'</h2>
				<form id="jl-options-form" method="post" name="configform" action="'.$this->getConfigLink().'" enctype="multipart/form-data">
					<input type="hidden" name="save" value="1">'.WT_Filter::getCsrf().'
					<input type="hidden" name="remove-image" value="0">
					<div class="block_left">						
						<div id="compact_menu" class="field">
							<label>'.WT_I18N::translate('Use a compact menu?').'</label>'.
							two_state_checkbox('NEW_JL_OPTIONS[COMPACT_MENU]', $this->options('compact_menu')).'
						</div>
						<div id="reports" class="field">
							<label>'.WT_I18N::translate('Include the reports topmenu in the compact \'View\' topmenu?').'</label>'.
							two_state_checkbox('NEW_JL_OPTIONS[COMPACT_MENU_REPORTS]', $this->options('compact_menu_reports')).'
						</div>
						<div id="media_menu" class="field">
							<label>'.WT_I18N::translate('Media menu in topmenu').help_link('media_menu', $this->getName()).'</label>'.
							two_state_checkbox('NEW_JL_OPTIONS[MEDIA_MENU]', $this->options('media_menu')).'
						</div>
						<div id="media_link" class="field">
							<label>'.WT_I18N::translate('Choose a folder as default for the main menu link').help_link('media_folder', $this->getName()).'</label>'.
							select_edit_control('NEW_JL_OPTIONS[MEDIA_LINK]', $this->getFolderList(), null, $this->options('media_link')).'
						</div>
						<div id="subfolders" class="field">
							<label>'.WT_I18N::translate('Include subfolders').help_link('subfolders', $this->getName()).'</label>'.
							two_state_checkbox('NEW_JL_OPTIONS[SUBFOLDERS]', $this->options('subfolders')).'
						</div>						
						<div id="buttons">
							<input type="submit" name="update" value="'.WT_I18N::translate('Save').'" />&nbsp;&nbsp;
							<input type="reset" value="'.WT_I18N::translate('Reset').'" onclick="if (confirm(\''.WT_I18N::translate('The settings will be reset to default. Are you sure you want to do this?').'\')) window.location.href=\'module.php?mod='.$this->getName().'&amp;mod_action=admin_reset\';">
						</div>
					</div>
					<div class="block_right">
						<h3>'.WT_I18N::translate('Sort Topmenu items').help_link('sort_topmenu', $this->getName()).'</h3>';
						$menulist 	= $this->checkModule($this->options('menu'));
						foreach($menulist as $menu) {
							$menu['sort'] == 0 ? $trashMenu[] = $menu : $activeMenu[] = $menu;
						}
						$i=0;
						if (isset($activeMenu)) {
		$html .= '			<ul id="sortMenu">';
							foreach ($activeMenu as $menu) {
								if ($menu['sort'] < 99) {
									$html .= '<li class="ui-state-default' . $this->getStatus($menu['label']) . '">';
								}
								foreach ($menu as $key => $val) {
									$html .= '<input type="hidden" name="NEW_JB_MENU['.$i.']['.$key.']" value="'.$val.'"/>';
								}
								if ($menu['sort'] < 99) {
									$html .= '<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>' . $menu['title'] . '</li>';
								}
								$i++;
							}
		$html .= '			</ul>';
						}
						if (isset($trashMenu)) {
		$html .= '			<ul id="trashMenu">'; // trashcan for toggling the compact menu.
							foreach ($trashMenu as $menu) {
								$html .= '<li class="ui-state-default'.$this->getStatus($menu['label']).'">';
								foreach ($menu as $key => $val) {
									$html .= '<input type="hidden" name="NEW_JB_MENU['.$i.']['.$key.']" value="'.$val.'"/>';
								}
		$html .= '				<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$menu['title'].'</li>';
								$i++;
							}
		$html .= '			</ul>';
						}
		$html .= '	</div>
				</form>
			</div>';

		// output
		ob_start();
		$html .= ob_get_clean();
		echo $html;
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}
}
