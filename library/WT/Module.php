<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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

abstract class WT_Module {
	private $_title = null;

	private $settings;

	public function __construct() {
		$this->_title = $this->getTitle();
	}

	public function __toString() {
		return $this->_title;
	}

	// Each module must provide the following functions
	abstract public function getTitle();       // To label tabs, etc.
	abstract public function getDescription(); // A sentence describing what this module does

	// This is the default for the module and all its components.
	public function defaultAccessLevel() {
		// Returns one of: WT_PRIV_HIDE, WT_PRIV_PUBLIC, WT_PRIV_USER, WT_PRIV_ADMIN
		return WT_PRIV_PUBLIC;
	}

	// This is an internal name, used to generate identifiers
	public function getName() {
		return str_replace('_WT_Module', '', get_class($this));
	}

	// Some modules use many settings.  Load them in one query for performance.
	private function loadAllSettings() {
		if ($this->settings === null) {
			$this->settings = WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##module_setting` WHERE module_name = ?"
			)->execute(array($this->getName()))->fetchAssoc();
		}
	}

	// Get a module setting.
	// If it is not set, use the supplied default.
	public function getSetting($setting_name, $default=null) {
		$this->loadAllSettings();

		if (array_key_exists($setting_name, $this->settings)) {
			return $this->settings[$setting_name];
		} else {
			return $default;
		}
	}

	// Set/update/delete a module setting
	public function setSetting($setting_name, $setting_value) {
		$this->loadAllSettings();

		if ($setting_value === null) {
			// Settings are not allowed to be null.  Delete it instead.
			WT_DB::prepare(
				"DELETE FROM `##module_setting` WHERE module_name = ? AND setting_name = ?"
			)->execute(array($this->getName(), $setting_name));
			unset($this->settings[$setting_name]);
		} elseif (!array_key_exists($setting_name, $this->settings)) {
			// Setting does not already exist - insert it.
			WT_DB::prepare(
				"INSERT INTO `##module_setting` (module_name, setting_name, setting_value) VALUES (?, ?, ?)"
			)->execute(array($this->getName(), $setting_name, $setting_value));
			$this->settings[$setting_name] = $setting_value;
		} elseif ($setting_value != $this->settings[$setting_name]) {
			// Setting already exists, but with a different value - update it.
			WT_DB::prepare(
				"UPDATE `##module_setting` SET setting_value = ? WHERE module_name = ? AND setting_name = ?"
			)->execute(array($setting_value, $this->getName(), $setting_name));
			$this->settings[$setting_name] = $setting_value;
		} else {
			// Setting already exists, but with the same value - do nothing.
		}
	}

	// Run an action specified on the URL through module.php?mod=FOO&mod_action=BAR
	public function modAction($mod_action) {
	}

	public static function getActiveModules($sort=false) {
		// We call this function several times, so cache the results.
		// Sorting is slow, so only do it when requested.
		static $modules=null;
		static $sorted =false;

		if ($modules===null) {
			$module_names=WT_DB::prepare(
				"SELECT SQL_CACHE module_name FROM `##module` WHERE status='enabled'"
			)->fetchOneColumn();
			$modules=array();
			foreach ($module_names as $module_name) {
				if (file_exists(WT_ROOT.WT_MODULES_DIR.$module_name.'/module.php')) {
					require_once WT_ROOT.WT_MODULES_DIR.$module_name.'/module.php';
					$class=$module_name.'_WT_Module';
					$modules[$module_name]=new $class();
				} else {
					// Module has been deleted from disk?  Disable it.
					AddToLog("Module {$module_name} has been deleted from disk - disabling it", 'config');
					WT_DB::prepare(
						"UPDATE `##module` SET status='disabled' WHERE module_name=?"
					)->execute(array($module_name));
				}
			}
		}
		if ($sort && !$sorted) {
			uasort($modules, create_function('$x,$y', 'return utf8_strcasecmp((string)$x, (string)$y);'));
			$sorted=true;
		}
		return $modules;
	}

	private static function getActiveModulesByComponent($component, $ged_id, $access_level) {
		$module_names=WT_DB::prepare(
			"SELECT SQL_CACHE module_name".
			" FROM `##module`".
			" JOIN `##module_privacy` USING (module_name)".
			" WHERE gedcom_id=? AND component=? AND status='enabled' AND access_level>=?".
			" ORDER BY CASE component WHEN 'menu' THEN menu_order WHEN 'sidebar' THEN sidebar_order WHEN 'tab' THEN tab_order ELSE 0 END, module_name"
		)->execute(array($ged_id, $component, $access_level))->fetchOneColumn();
		$array=array();
		foreach ($module_names as $module_name) {
			if (file_exists(WT_ROOT.WT_MODULES_DIR.$module_name.'/module.php')) {
				require_once WT_ROOT.WT_MODULES_DIR.$module_name.'/module.php';
				$class=$module_name.'_WT_Module';
				$array[$module_name]=new $class();
			} else {
				// Module has been deleted from disk?  Disable it.
				AddToLog("Module {$module_name} has been deleted from disk - disabling it", 'config');
				WT_DB::prepare(
					"UPDATE `##module` SET status='disabled' WHERE module_name=?"
				)->execute(array($module_name));
			}
		}
		if ($component!='menu' && $component!='sidebar' && $component!='tab') {
			uasort($array, create_function('$x,$y', 'return utf8_strcasecmp((string)$x, (string)$y);'));
		}
		return $array;
	}

	// Get a list of all the active, authorised blocks
	public static function getActiveBlocks($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $blocks=null;
		if ($blocks===null) {
			$blocks=self::getActiveModulesByComponent('block', $ged_id, $access_level);
		}
		return $blocks;
	}

	// Get a list of all the active, authorised charts
	public static function getActiveCharts($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $charts=null;
		if ($charts===null) {
			$charts=self::getActiveModulesByComponent('chart', $ged_id, $access_level);
		}
		return $charts;
	}

	// Get a list of all the active, authorised menus
	public static function getActiveMenus($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $menus=null;
		if ($menus===null) {
			$menus=self::getActiveModulesByComponent('menu', $ged_id, $access_level);
		}
		return $menus;
	}

	// Get a list of all the active, authorised reports
	public static function getActiveReports($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $reports=null;
		if ($reports===null) {
			$reports=self::getActiveModulesByComponent('report', $ged_id, $access_level);
		}
		return $reports;
	}

	// Get a list of all the active, authorised sidebars
	public static function getActiveSidebars($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $sidebars=null;
		if ($sidebars===null) {
			$sidebars=self::getActiveModulesByComponent('sidebar', $ged_id, $access_level);
		}
		return $sidebars;
	}

	// Get a list of all the active, authorised tabs
	public static function getActiveTabs($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $tabs=null;
		if ($tabs===null) {
			$tabs=self::getActiveModulesByComponent('tab', $ged_id, $access_level);
		}
		return $tabs;
	}

	// Get a list of all the active, authorised themes
	public static function getActiveThemes($ged_id=WT_GED_ID, $access_level=WT_USER_ACCESS_LEVEL) {
		static $themes=null;
		if ($themes===null) {
			$themes=self::getActiveModulesByComponent('theme', $ged_id, $access_level);
		}
		return $themes;
	}

	// Get a list of all installed modules.
	// During setup, new modules need status of 'enabled'
	// In admin->modules, new modules need status of 'disabled'
	public static function getInstalledModules($status) {
		$modules=array();
		$dir=opendir(WT_ROOT.WT_MODULES_DIR);
		while (($file=readdir($dir))!==false) {
			if (preg_match('/^[a-zA-Z0-9_]+$/', $file) && file_exists(WT_ROOT.WT_MODULES_DIR.$file.'/module.php')) {
				require_once WT_ROOT.WT_MODULES_DIR.$file.'/module.php';
				$class=$file.'_WT_Module';
				$module=new $class();
				$modules[$module->getName()]=$module;
				WT_DB::prepare("INSERT IGNORE INTO `##module` (module_name, status, menu_order, sidebar_order, tab_order) VALUES (?, ?, ?, ?, ?)")
					->execute(array(
						$module->getName(),
						$status,
						$module instanceof WT_Module_Menu    ? $module->defaultMenuOrder   () : null,
						$module instanceof WT_Module_Sidebar ? $module->defaultSidebarOrder() : null,
						$module instanceof WT_Module_Tab     ? $module->defaultTabOrder    () : null
					));
				// Set the default privcy for this module.  Note that this also sets it for the
				// default family tree, with a gedcom_id of -1
				if ($module instanceof WT_Module_Menu) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'menu', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Sidebar) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'sidebar', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Tab) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'tab', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Block) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'block', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Chart) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'chart', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Report) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'report', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Theme) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)".
						" SELECT ?, gedcom_id, 'theme', ?".
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
			}
		}
		uasort($modules, create_function('$x,$y', 'return utf8_strcasecmp((string)$x, (string)$y);'));
		return $modules;
	}

	// We have a new family tree - assign default access rights to it.
	public static function setDefaultAccess($ged_id) {
		foreach (self::getInstalledModules('disabled') as $module) {
			if ($module instanceof WT_Module_Menu) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Sidebar) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'sidebar', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Tab) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'tab', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Block) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'block', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Chart) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'chart', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Report) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'report', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Theme) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'theme', ?)"
				)->execute(array($module->getName(), $ged_id, $module->defaultAccessLevel()));
			}
		}
	}
}
