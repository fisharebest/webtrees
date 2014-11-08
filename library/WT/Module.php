<?php
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

use WT\Log;

/**
 * Class WT_Module - base class for modules, and static functions for managing
 * and maintaining modules.
 */
abstract class WT_Module {
	/** @var string A user-friendly, localized name for this module */
	private $_title = null;

	/** @var string[] A cached copy of the module settings */
	private $settings;

	/**
	 * Create a new module
	 */
	public function __construct() {
		$this->_title = $this->getTitle();
	}

	/**
	 * Create a name for this module.
	 *
	 * Earlier modules did not have a constructor, and hence a number of custom
	 * modules fail to call parent::__construct().  If this happens, choose a
	 * default name, rather than erroring.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->_title ?: get_class();
	}

	/**
	 * How should this module be labelled on tabs, menus, etc.?
	 *
	 * @return string
	 */
	abstract public function getTitle();

	/**
	 * A sentence describing what this module does.
	 *
	 * @return string
	 */
	abstract public function getDescription();

	/**
	 * What is the default access level for this module?
	 *
	 * Some modules are aimed at admins or managers, and are not generally shown to users.
	 *
	 * @return integer
	 */
	public function defaultAccessLevel() {
		// Returns one of: WT_PRIV_HIDE, WT_PRIV_PUBLIC, WT_PRIV_USER, WT_PRIV_ADMIN
		return WT_PRIV_PUBLIC;
	}

	/**
	 * Provide a localized name for this module
	 *
	 * @return string
	 */
	public function getName() {
		return str_replace('_WT_Module', '', get_class($this));
	}

	/**
	 * Load all the settings for the module into a cache.
	 *
	 * Since modules may have many settings, and will probably want to use
	 * lots of them, load them all at once and cache them.
	 *
	 * @return void
	 */
	private function loadAllSettings() {
		if ($this->settings === null) {
			$this->settings = WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##module_setting` WHERE module_name = ?"
			)->execute(array($this->getName()))->fetchAssoc();
		}
	}

	/**
	 * Get a module setting.  Return a default if the setting is not set.
	 *
	 * @param string $setting_name
	 * @param string $default
	 *
	 * @return string|null
	 */
	public function getSetting($setting_name, $default = null) {
		$this->loadAllSettings();

		if (array_key_exists($setting_name, $this->settings)) {
			return $this->settings[$setting_name];
		} else {
			return $default;
		}
	}

	/**
	 * Set a module setting.
	 *
	 * Since module settings are NOT NULL, setting a value to NULL will cause
	 * it to be deleted.
	 *
	 * @param string $setting_name
	 * @param string $setting_value
	 */
	public function setSetting($setting_name, $setting_value) {
		$this->loadAllSettings();

		if ($setting_value === null) {
			WT_DB::prepare(
				"DELETE FROM `##module_setting` WHERE module_name = ? AND setting_name = ?"
			)->execute(array($this->getName(), $setting_name));
			unset($this->settings[$setting_name]);
		} elseif (!array_key_exists($setting_name, $this->settings)) {
			WT_DB::prepare(
				"INSERT INTO `##module_setting` (module_name, setting_name, setting_value) VALUES (?, ?, ?)"
			)->execute(array($this->getName(), $setting_name, $setting_value));
			$this->settings[$setting_name] = $setting_value;
		} elseif ($setting_value != $this->settings[$setting_name]) {
			WT_DB::prepare(
				"UPDATE `##module_setting` SET setting_value = ? WHERE module_name = ? AND setting_name = ?"
			)->execute(array($setting_value, $this->getName(), $setting_name));
			$this->settings[$setting_name] = $setting_value;
		} else {
			// Setting already exists, but with the same value - do nothing.
		}
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
	}

	/**
	 * Get a list of all active (enabled) modules.
	 *
	 * @param boolean $sort Sort the module by the (localised) name
	 *
	 * @return WT_Module[]
	 */
	public static function getActiveModules($sort = false) {
		/** @var WT_Module[] - We call this function several times, so cache the results. */
		static $modules = null;

		/** @var boolean - Sorting is slow, so only do it when requested. */
		static $sorted = false;

		if ($modules === null) {
			$module_names = WT_DB::prepare(
				"SELECT SQL_CACHE module_name FROM `##module` WHERE status='enabled'"
			)->fetchOneColumn();
			$modules = array();
			foreach ($module_names as $module_name) {
				if (file_exists(WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php')) {
					require_once WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php';
					$class                 = $module_name . '_WT_Module';
					$modules[$module_name] = new $class;
				} else {
					// Module has been deleted from disk?  Disable it.
					Log::addConfigurationLog("Module {$module_name} has been deleted from disk - disabling it");
					WT_DB::prepare(
						"UPDATE `##module` SET status='disabled' WHERE module_name=?"
					)->execute(array($module_name));
				}
			}
		}
		if ($sort && !$sorted) {
			uasort($modules, create_function('$x,$y', 'return WT_I18N::strcasecmp((string)$x, (string)$y);'));
			$sorted = true;
		}

		return $modules;
	}

	/**
	 * Get a list of modules which (a) provide a specific function chart and (b) we have permission to see.
	 *
	 * We cannot currently use auto-loading for modules, as there may be user-defined
	 * modules about which the auto-loader knows nothing.
	 *
	 * @param string  $component The type of module, such as "tab", "report" or "menu"
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module[]
	 */
	private static function getActiveModulesByComponent($component, $tree_id, $access_level) {
		$module_names = WT_DB::prepare(
			"SELECT SQL_CACHE module_name" .
			" FROM `##module`" .
			" JOIN `##module_privacy` USING (module_name)" .
			" WHERE gedcom_id=? AND component=? AND status='enabled' AND access_level>=?" .
			" ORDER BY CASE component WHEN 'menu' THEN menu_order WHEN 'sidebar' THEN sidebar_order WHEN 'tab' THEN tab_order ELSE 0 END, module_name"
		)->execute(array($tree_id, $component, $access_level))->fetchOneColumn();

		$array = array();
		foreach ($module_names as $module_name) {
			if (file_exists(WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php')) {
				require_once WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php';
				$class     = $module_name . '_WT_Module';
				$interface = 'WT_Module_' . ucfirst($component);
				$module    = new $class;
				// Check that this module is still implementing the desired interface.
				if ($module instanceof $interface) {
					$array[$module_name] = new $module;
				}
			} else {
				// Module has been deleted from disk?  Disable it.
				Log::addConfigurationLog("Module {$module_name} has been deleted from disk - disabling it");
				WT_DB::prepare(
					"UPDATE `##module` SET status='disabled' WHERE module_name=?"
				)->execute(array($module_name));
			}
		}

		// The order of some modules is defined by the user.  Others are sorted by name.
		if ($component != 'menu' && $component != 'sidebar' && $component != 'tab') {
			uasort($array, function ($x, $y) {
				return WT_I18N::strcasecmp((string)$x, (string)$y);
			});
		}

		return $array;
	}

	/**
	 * Get a list of modules which (a) provide a block and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Block[]
	 */
	public static function getActiveBlocks($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $blocks = null;

		if ($blocks === null) {
			$blocks = self::getActiveModulesByComponent('block', $tree_id, $access_level);
		}

		return $blocks;
	}

	/**
	 * Get a list of modules which (a) provide a chart and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Chart[]
	 */
	public static function getActiveCharts($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $charts = null;

		if ($charts === null) {
			$charts = self::getActiveModulesByComponent('chart', $tree_id, $access_level);
		}

		return $charts;
	}

	/**
	 * Get a list of modules which (a) provide a menu and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Menu[]
	 */
	public static function getActiveMenus($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $menus = null;

		if ($menus === null) {
			$menus = self::getActiveModulesByComponent('menu', $tree_id, $access_level);
		}

		return $menus;
	}

	/**
	 * Get a list of modules which (a) provide a report and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Report[]
	 */
	public static function getActiveReports($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $reports = null;

		if ($reports === null) {
			$reports = self::getActiveModulesByComponent('report', $tree_id, $access_level);
		}

		return $reports;
	}

	/**
	 * Get a list of modules which (a) provide a sidebar and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Sidebar[]
	 */
	public static function getActiveSidebars($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $sidebars = null;

		if ($sidebars === null) {
			$sidebars = self::getActiveModulesByComponent('sidebar', $tree_id, $access_level);
		}

		return $sidebars;
	}

	/**
	 * Get a list of modules which (a) provide a tab and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Tab[]
	 */
	public static function getActiveTabs($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $tabs = null;

		if ($tabs === null) {
			$tabs = self::getActiveModulesByComponent('tab', $tree_id, $access_level);
		}

		return $tabs;
	}

	/**
	 * Get a list of modules which (a) provide a theme and (b) we have permission to see.
	 *
	 * @param integer $tree_id
	 * @param integer $access_level
	 *
	 * @return WT_Module_Theme[]
	 */
	public static function getActiveThemes($tree_id = WT_GED_ID, $access_level = WT_USER_ACCESS_LEVEL) {
		static $themes = null;

		if ($themes === null) {
			$themes = self::getActiveModulesByComponent('theme', $tree_id, $access_level);
		}

		return $themes;
	}

	/**
	 * Scan the source code to find a list of all installed modules.
	 *
	 * During setup, new modules need a status of “enabled”.
	 * In admin->modules, new modules need status of “disabled”.
	 *
	 * @param string $default_status
	 *
	 * @return WT_Module[]
	 */
	public static function getInstalledModules($default_status) {
		$modules = array();

		$dir = opendir(WT_ROOT . WT_MODULES_DIR);
		while (($module_name = readdir($dir)) !== false) {
			if (preg_match('/^[a-zA-Z0-9_]+$/', $module_name) && file_exists(WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php')) {
				require_once WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php';
				$class                       = $module_name . '_WT_Module';
				$module                      = new $class;
				$modules[$module->getName()] = $module;
				WT_DB::prepare("INSERT IGNORE INTO `##module` (module_name, status, menu_order, sidebar_order, tab_order) VALUES (?, ?, ?, ?, ?)")
					->execute(array(
						$module->getName(),
						$default_status,
						$module instanceof WT_Module_Menu ? $module->defaultMenuOrder() : null,
						$module instanceof WT_Module_Sidebar ? $module->defaultSidebarOrder() : null,
						$module instanceof WT_Module_Tab ? $module->defaultTabOrder() : null
					));
				// Set the default privcy for this module.  Note that this also sets it for the
				// default family tree, with a gedcom_id of -1
				if ($module instanceof WT_Module_Menu) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'menu', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Sidebar) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'sidebar', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Tab) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'tab', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Block) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'block', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Chart) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'chart', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Report) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'report', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof WT_Module_Theme) {
					WT_DB::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'theme', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
			}
		}
		uasort($modules, create_function('$x,$y', 'return WT_I18N::strcasecmp((string)$x, (string)$y);'));

		return $modules;
	}

	/**
	 * After creating a new family tree, we need to assign the default access
	 * rights for each module.
	 *
	 * @param integer $tree_id
	 *
	 * @return void
	 */
	public static function setDefaultAccess($tree_id) {
		foreach (self::getInstalledModules('disabled') as $module) {
			if ($module instanceof WT_Module_Menu) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Sidebar) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'sidebar', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Tab) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'tab', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Block) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'block', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Chart) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'chart', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Report) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'report', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof WT_Module_Theme) {
				WT_DB::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'theme', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
		}
	}
}
