<?php

/*
 * Functions for the JustLight theme
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

// This theme comes with an optional module to set a few theme options
function getThemeOption($setting) {
	if (array_key_exists('justlight_theme_options', WT_Module::getActiveModules())) {
		$module = new justlight_theme_options_WT_Module;
		return $module->options($setting);
	}
}

// variables needed in justlight.js
function getJLScriptVars() {
	global $controller;
	$controller->addInlineJavascript('
			// JustLight Theme variables
			var WT_SERVER_NAME = "' . WT_SERVER_NAME . '";
			var WT_SCRIPT_PATH = "' . WT_SCRIPT_PATH . '";
			var WT_TREE_TITLE = "' . strip_tags(WT_TREE_TITLE) . '";
			var JL_COLORBOX_URL = "' . JL_COLORBOX_URL . '";
	', WT_Controller_Base::JS_PRIORITY_HIGH);
}

// Menus
function getJLNavMenu() {
	global $controller, $SEARCH_SPIDER;

	$menus = getThemeOption('menu');
	if ($menus && WT_GED_ID && !$SEARCH_SPIDER) {
		$jl_controller	 = new justlight_theme_options_WT_Module;
		$menus			 = $jl_controller->checkModule($menus);
		$list			 = null;
		foreach ($menus as $menu) {
			$label		 = $menu['label'];
			$sort		 = $menu['sort'];
			$function	 = $menu['function'];
			if ($sort > 0) {
				if ($function == 'getModuleMenu') {
					$module	 = $label . '_WT_Module';
					$item	 = JL_NavMenu::getSingleModuleMenu($module);
				} elseif ($label == 'compact') {
					$item = JL_NavMenu::getCompactMenu();
				} elseif ($label == 'media') {
					$folders = $jl_controller->getFolderList();
					$item	 = JL_NavMenu::getMediaMenu($folders);
				} else {
					if (method_exists('JL_NavMenu', $function)) {
						$item = JL_NavMenu::$function();
					}
				}
				$list[] = $item;
			}
		}
		$output = implode('', $list);
	} else {
		$output = JL_NavMenu::getGedcomMenu() .
			JL_NavMenu::getMyPageMenu() .
			JL_NavMenu::getChartsMenu() .
			JL_NavMenu::getListsMenu() .
			JL_NavMenu::getCalendarMenu() .
			JL_NavMenu::getReportsMenu() .
			JL_NavMenu::getSearchMenu() .
			JL_NavMenu::getModuleMenus();
	}
	return $output;
}

function getJLMediaList() {
	global $WT_TREE;

	if (!$WT_TREE->getPreference('EXPAND_NOTES_DEFAULT')) {
		$WT_TREE->setPreference('EXPAND_NOTES_DEFAULT', $WT_TREE->getPreference('EXPAND_NOTES'));
	}

	if (WT_SCRIPT_NAME === 'medialist.php') {
		if ($WT_TREE->getPreference('EXPAND_NOTES')) {
			$WT_TREE->setPreference('EXPAND_NOTES', 0);
		}
	} else {
		$WT_TREE->setPreference('EXPAND_NOTES', $WT_TREE->getPreference('EXPAND_NOTES_DEFAULT'));
		$WT_TREE->setPreference('EXPAND_NOTES_DEFAULT', 0);
	}
}
