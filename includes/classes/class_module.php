<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
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
 *
 * @package webtrees
 * @subpackage Modules
 * @version $Id: class_media.php 5451 2009-05-05 22:15:34Z fisharebest $
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_MODULE_PHP', '');

// Modules can optionally implement the following interfaces.
interface WT_Module_Config {
	public function getConfigLink();
}

interface WT_Module_Menu {
	public function defaultMenuOrder();
}

interface WT_Module_Sidebar {
	public function defaultSidebarOrder();
	public function getSidebarContent();
	public function getSidebarAjaxContent();
	public function hasSidebarContent();
}

interface WT_Module_Tab {
	public function defaultTabOrder();
	public function getTabContent();
	public function hasTabContent();
	public function canLoadAjax();
	public function getPreLoadContent();
	public function getJSCallbackAllTabs();
	public function getJSCallback();
}

abstract class WT_Module {
	// Each module must provide the following functions
	abstract public function getTitle();       // To label tabs, etc.
	abstract public function getDescription(); // A sentence describing what this module does

	// This is the default for the module and all its components.
	public function defaultAccessLevel() {
		// Returns one of: WT_PRIV_HIDE, WT_PRIV_PUBLIC, WT_PRIV_USER, WT_PRIV_ADMIN
		return WT_PRIV_PUBLIC;
	}

	// This is an internal name, used to generate identifiers
	final public function getName() {
		return str_replace('_WT_Module', '', get_class($this));
	}

	// Some modules may use the page's controller
	protected $controller;
	final public function &getController()   { return $this->controller; }
	final public function setController(&$c) { $this->controller=$c;     }

	final static private function getActiveModulesByComponent($component, $ged_id, $access_level) {
		global $TBLPREFIX;

		$module_names=WT_DB::prepare(
			"SELECT module_name".
			" FROM {$TBLPREFIX}module".
			" JOIN {$TBLPREFIX}module_privacy USING (module_name)".
			" WHERE gedcom_id=? AND component=? AND status='enabled' AND access_level>=?".
			" ORDER BY CASE component WHEN 'menu' THEN menu_order WHEN 'sidebar' THEN sidebar_order WHEN 'tab' THEN tab_order END"
		)->execute(array($ged_id, $component, $access_level))->fetchOneColumn();
		$array=array();
		foreach ($module_names as $module_name) {
			if (file_exists(WT_ROOT.'modules/'.$module_name.'/module.php')) {
				require_once WT_ROOT.'modules/'.$module_name.'/module.php';
				$class=$module_name.'_WT_Module';
				$array[$module_name]=new $class();
			} else {
				// Module has been deleted from disk?  Remove it from the database.
				AddToLog("Module {$module_name} has been deleted from disk - deleting from database");
				WT_DB::prepare("DELETE FROM {$TBLPREFIX}module_privacy WHERE module_name=?")->execute(array($module_name));
				WT_DB::prepare("DELETE FROM {$TBLPREFIX}module WHERE module_name=?")->execute(array($module_name));
			}
		}
		return $array;
	}

	// Get a list of all the active, authorised sidebars
	final static public function getActiveMenus($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $menus=null;
		if ($menus===null) {
			$menus=self::getActiveModulesByComponent('menu', $ged_id, $access_level);
		}
		return $menus;
	}

	// Get a list of all the active, authorised sidebars
	final static public function getActiveSidebars($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $sidebars=null;
		if ($sidebars===null) {
			$sidebars=self::getActiveModulesByComponent('sidebar', $ged_id, $access_level);
		}
		return $sidebars;
	}

	// Get a list of all the active, authorised tabs
	final static public function getActiveTabs($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $tabs=null;
		if ($tabs===null) {
			$tabs=self::getActiveModulesByComponent('tab', $ged_id, $access_level);
		}
		return $tabs;
	}

	// Get installed modules
	final static public function getInstalledModules() {
		static $modules=null;
		if ($modules===null) {
			$dir=opendir(WT_ROOT.'modules');
			while (($file=readdir($dir))!==false) {
				if (preg_match('/^[a-z_]+$/', $file) && file_exists(WT_ROOT.'modules/'.$file.'/module.php')) {
					require_once WT_ROOT.'modules/'.$file.'/module.php';
					$class=$file.'_WT_Module';
					$modules[]=new $class();					
				}
			}
		}
		return $modules;
	}

	// Get installed menus
	final static public function getInstalledMenus() {
		global $TBLPREFIX;
		$modules=array();
		foreach (self::getInstalledModules() as $module) {
			if ($module instanceof WT_Module_Menu) {
				$module->sort=WT_DB::prepare(
					"SELECT menu_order FROM {$TBLPREFIX}module WHERE module_name=?"
				)->execute(array($module->getName()))->fetchOne();
				$modules[]=$module;
			}
		}
		usort($modules, create_function('$x,$y', 'return $x->sort-$y->sort;'));
		return $modules;
	}

	// Get installed sidebars
	final static public function getInstalledSidebars() {
		global $TBLPREFIX;
		$modules=array();
		foreach (self::getInstalledModules() as $module) {
			if ($module instanceof WT_Module_Sidebar) {
				$module->sort=WT_DB::prepare(
					"SELECT sidebar_order FROM {$TBLPREFIX}module WHERE module_name=?"
				)->execute(array($module->getName()))->fetchOne();
				$modules[]=$module;
			}
		}
		usort($modules, create_function('$x,$y', 'return $x->sort-$y->sort;'));
		return $modules;
	}

	// Get installed tabs
	final static public function getInstalledTabs() {
		global $TBLPREFIX;
		$modules=array();
		foreach (self::getInstalledModules() as $module) {
			if ($module instanceof WT_Module_Tab) {
				$module->sort=WT_DB::prepare(
					"SELECT tab_order FROM {$TBLPREFIX}module WHERE module_name=?"
				)->execute(array($module->getName()))->fetchOne();
				$modules[]=$module;
			}
		}
		usort($modules, create_function('$x,$y', 'return $x->sort-$y->sort;'));
		return $modules;
	}

	//
	final static public function setDefaultAccess($ged_id) {
		global $TBLPREFIX;
		foreach (self::getInstalledModules() as $module) {
			WT_DB::prepare("INSERT IGNORE INTO {$TBLPREFIX}module (module_name, menu_order, sidebar_order, tab_order) VALUES (?, ?, ?, ?)")
				->execute(array(
					$module->getName(),
					$module instanceof WT_Module_Menu    ? $module->defaultMenuOrder   () : null,
					$module instanceof WT_Module_Sidebar ? $module->defaultSidebarOrder() : null,
					$module instanceof WT_Module_Tab     ? $module->defaultTabOrder    () : null
				));
		}
		WT_DB::prepare("DELETE FROM {$TBLPREFIX}module_privacy WHERE gedcom_id=?")->execute(array($ged_id));
		foreach (self::getInstalledMenus() as $module) {
			WT_DB::prepare(
				"INSERT INTO {$TBLPREFIX}module_privacy (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
			)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
		}
		foreach (self::getInstalledSidebars() as $module) {
			WT_DB::prepare(
				"INSERT INTO {$TBLPREFIX}module_privacy (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'sidebar', ?)"
			)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
		}
		foreach (self::getInstalledTabs() as $module) {
			WT_DB::prepare(
				"INSERT INTO {$TBLPREFIX}module_privacy (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'tab', ?)"
			)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
		}
	}

	static public function compare_name(&$a, &$b) {
		return strcmp($a->getName(), $b->getName());
	}
}
