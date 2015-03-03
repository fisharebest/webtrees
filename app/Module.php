<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class Module - base class for modules, and static functions for managing
 * and maintaining modules.
 */
abstract class Module {
	/** @var string A user-friendly, localized name for this module */
	private $title;

	/** @var string The directory where the module is installed */
	private $directory;

	/** @var string[] A cached copy of the module settings */
	private $settings;

	/**
	 * Create a new module.
	 *
	 * @param string $directory Where is this module installed
	 */
	public function __construct($directory) {
		$this->directory = $directory;
		$this->title     = $this->getTitle();
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
	 * Provide a unique internal name for this module
	 *
	 * @return string
	 */
	public function getName() {
		return basename($this->directory);
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
			$this->settings = Database::prepare(
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
			Database::prepare(
				"DELETE FROM `##module_setting` WHERE module_name = ? AND setting_name = ?"
			)->execute(array($this->getName(), $setting_name));
			unset($this->settings[$setting_name]);
		} elseif (!array_key_exists($setting_name, $this->settings)) {
			Database::prepare(
				"INSERT INTO `##module_setting` (module_name, setting_name, setting_value) VALUES (?, ?, ?)"
			)->execute(array($this->getName(), $setting_name, $setting_value));
			$this->settings[$setting_name] = $setting_value;
		} elseif ($setting_value != $this->settings[$setting_name]) {
			Database::prepare(
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
	 * Get a the current access level for a module
	 *
	 * @param Tree   $tree
	 * @param string $component - tab, block, menu, etc
	 *
	 * @return integer
	 */
	public function getAccessLevel(Tree $tree, $component) {
		$access_level = Database::prepare(
			"SELECT access_level FROM `##module_privacy` WHERE gedcom_id = :gedcom_id AND module_name = :module_name AND component = :component"
		)->execute(array(
			'gedcom_id'   => $tree->getTreeId(),
			'module_name' => $this->getName(),
			'component'   => $component,
		))->fetchOne();

		if ($access_level === null) {
			return $this->defaultAccessLevel();
		} else {
			return (int) $access_level;
		}
	}

	/**
	 * Get a list of all active (enabled) modules.
	 *
	 * @return Module[]
	 */
	private static function getActiveModules() {
		/** @var Module[] - Only query the database once. */
		static $modules;

		if ($modules === null) {
			$module_names = Database::prepare(
				"SELECT SQL_CACHE module_name FROM `##module` WHERE status = 'enabled'"
			)->fetchOneColumn();

			$modules = array();
			foreach ($module_names as $module_name) {
				try {
					$module = include WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php';
					if ($module instanceof Module) {
						$modules[$module->getName()] = $module;
					} else {
						throw new \Exception;
					}
				} catch (\Exception $ex) {
					// Module has been deleted or is broken?  Disable it.
					Log::addConfigurationLog("Module {$module_name} is missing or broken - disabling it");
					Database::prepare(
						"UPDATE `##module` SET status = 'disabled' WHERE module_name = :module_name"
					)->execute(array(
						'module_name' => $module_name
					));
				}
			}
		}

		return $modules;
	}

	/**
	 * Get a list of modules which (a) provide a specific function chart and (b) we have permission to see.
	 *
	 * We cannot currently use auto-loading for modules, as there may be user-defined
	 * modules about which the auto-loader knows nothing.
	 *
	 * @param Tree    $tree
	 * @param string  $component The type of module, such as "tab", "report" or "menu"
	 * @param integer $access_level
	 *
	 * @return Module[]
	 */
	private static function getActiveModulesByComponent(Tree $tree, $component, $access_level) {
		$module_names = Database::prepare(
			"SELECT SQL_CACHE module_name" .
			" FROM `##module`" .
			" JOIN `##module_privacy` USING (module_name)" .
			" WHERE gedcom_id = :tree_id AND component = :component AND status = 'enabled' AND access_level >= :access_level" .
			" ORDER BY CASE component WHEN 'menu' THEN menu_order WHEN 'sidebar' THEN sidebar_order WHEN 'tab' THEN tab_order ELSE 0 END, module_name"
		)->execute(array(
			'tree_id'      => $tree->getTreeId(),
			'component'    => $component,
			'access_level' => $access_level,
		))->fetchOneColumn();

		$array = array();
		foreach ($module_names as $module_name) {
			$interface = __NAMESPACE__ . '\Module' . ucfirst($component) . 'Interface';
			$module = self::getModuleByName($module_name);
			if ($module instanceof $interface) {
				$array[$module_name] = $module;
			}
		}

		// The order of menus/sidebars/tabs is defined in the database.  Others are sorted by name.
		if ($component !== 'menu' && $component !== 'sidebar' && $component !== 'tab') {
			uasort($array, function(Module $x, Module $y) {
				return I18N::strcasecmp($x->getTitle(), $y->getTitle());
			});
		}

		return $array;
	}

	/**
	 * Get a list of modules which (a) provide a block and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleBlockInterface[]
	 */
	public static function getActiveBlocks(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'block', $access_level);
	}

	/**
	 * Get a list of modules which (a) provide a chart and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleChartInterface[]
	 */
	public static function getActiveCharts(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'chart', $access_level);
	}

	/**
	 * Get a list of modules which (a) provide a menu and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleMenuInterface[]
	 */
	public static function getActiveMenus(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'menu', $access_level);
	}

	/**
	 * Get a list of modules which (a) provide a report and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleReportInterface[]
	 */
	public static function getActiveReports(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'report', $access_level);
	}

	/**
	 * Get a list of modules which (a) provide a sidebar and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleSidebarInterface[]
	 */
	public static function getActiveSidebars(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'sidebar', $access_level);
	}

	/**
	 * Get a list of modules which (a) provide a tab and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleTabInterface[]
	 */
	public static function getActiveTabs(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'tab', $access_level);
	}

	/**
	 * Get a list of modules which (a) provide a theme and (b) we have permission to see.
	 *
	 * @param Tree    $tree
	 * @param integer $access_level
	 *
	 * @return ModuleThemeInterface[]
	 */
	public static function getActiveThemes(Tree $tree, $access_level = WT_USER_ACCESS_LEVEL) {
		return self::getActiveModulesByComponent($tree, 'theme', $access_level);
	}

	/**
	 * Find a specified module, if it is currently active.
	 *
	 * @param string $module_name
	 *
	 * @return Module|null
	 */
	public static function getModuleByName($module_name) {
		$modules = self::getActiveModules();
		if (array_key_exists($module_name, $modules)) {
			return $modules[$module_name];
		} else {
			return null;
		}
	}

	/**
	 * Scan the source code to find a list of all installed modules.
	 *
	 * During setup, new modules need a status of “enabled”.
	 * In admin->modules, new modules need status of “disabled”.
	 *
	 * @param string $default_status
	 *
	 * @return Module[]
	 */
	public static function getInstalledModules($default_status) {
		$modules = array();

		foreach (glob(WT_ROOT . WT_MODULES_DIR . '*/module.php') as $file) {
			try {
				$module = include $file;
				if ($module instanceof Module) {
					$modules[$module->getName()] = $module;
					Database::prepare("INSERT IGNORE INTO `##module` (module_name, status, menu_order, sidebar_order, tab_order) VALUES (?, ?, ?, ?, ?)")->execute(array(
						$module->getName(),
						$default_status,
						$module instanceof ModuleMenuInterface ? $module->defaultMenuOrder() : null,
						$module instanceof ModuleSidebarInterface ? $module->defaultSidebarOrder() : null,
						$module instanceof ModuleTabInterface ? $module->defaultTabOrder() : null,
					));
				}
				// Set the default privcy for this module.  Note that this also sets it for the
				// default family tree, with a gedcom_id of -1
				if ($module instanceof ModuleMenuInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'menu', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof ModuleSidebarInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'sidebar', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof ModuleTabInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'tab', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof ModuleBlockInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'block', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof ModuleChartInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'chart', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof ModuleReportInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'report', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
				if ($module instanceof ModuleThemeInterface) {
					Database::prepare(
						"INSERT IGNORE INTO `##module_privacy` (module_name, gedcom_id, component, access_level)" .
						" SELECT ?, gedcom_id, 'theme', ?" .
						" FROM `##gedcom`"
					)->execute(array($module->getName(), $module->defaultAccessLevel()));
				}
			} catch (\Exception $ex) {
				// Old or invalid module?
			}
		}

		uasort($modules, function(Module $x, Module $y) {
			return I18N::strcasecmp($x->getTitle(), $y->getTitle());
		});

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
			if ($module instanceof ModuleMenuInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'menu', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof ModuleSidebarInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'sidebar', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof ModuleTabInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'tab', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof ModuleBlockInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'block', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof ModuleChartInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'chart', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof ModuleReportInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'report', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
			if ($module instanceof ModuleThemeInterface) {
				Database::prepare(
					"INSERT IGNORE `##module_privacy` (module_name, gedcom_id, component, access_level) VALUES (?, ?, 'theme', ?)"
				)->execute(array($module->getName(), $tree_id, $module->defaultAccessLevel()));
			}
		}
	}
}
