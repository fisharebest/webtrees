<?php
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\ModuleMenuInterface;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Module\ModuleSidebarInterface;
use Fisharebest\Webtrees\Module\ModuleTabInterface;
use Fisharebest\Webtrees\Module\ModuleThemeInterface;

/**
 * Functions for managing and maintaining modules.
 */
class Module {
	/**
	 * Get a list of all active (enabled) modules.
	 *
	 * @return AbstractModule[]
	 */
	private static function getActiveModules() {
		/** @var AbstractModule[] - Only query the database once. */
		static $modules;

		if ($modules === null) {
			$module_names = Database::prepare(
				"SELECT SQL_CACHE module_name FROM `##module` WHERE status = 'enabled'"
			)->fetchOneColumn();

			$modules = array();
			foreach ($module_names as $module_name) {
				try {
					$module = include WT_ROOT . WT_MODULES_DIR . $module_name . '/module.php';
					if ($module instanceof AbstractModule) {
						$modules[$module->getName()] = $module;
					} else {
						throw new \Exception;
					}
				} catch (\Exception $ex) {
					// The module has been deleted or is broken?  Disable it.
					Log::addConfigurationLog("Module {$module_name} is missing or broken - disabling it");
					Database::prepare(
						"UPDATE `##module` SET status = 'disabled' WHERE module_name = :module_name"
					)->execute(array(
						'module_name' => $module_name,
					));
				}
			}
		}

		return $modules;
	}

	/**
	 * Get a list of modules which (a) provide a specific function and (b) we have permission to see.
	 *
	 * We cannot currently use auto-loading for modules, as there may be user-defined
	 * modules about which the auto-loader knows nothing.
	 *
	 * @param Tree   $tree
	 * @param string $component The type of module, such as "tab", "report" or "menu"
	 *
	 * @return AbstractModule[]
	 */
	private static function getActiveModulesByComponent(Tree $tree, $component) {
		$module_names = Database::prepare(
			"SELECT SQL_CACHE module_name" .
			" FROM `##module`" .
			" JOIN `##module_privacy` USING (module_name)" .
			" WHERE gedcom_id = :tree_id AND component = :component AND status = 'enabled' AND access_level >= :access_level" .
			" ORDER BY CASE component WHEN 'menu' THEN menu_order WHEN 'sidebar' THEN sidebar_order WHEN 'tab' THEN tab_order ELSE 0 END, module_name"
		)->execute(array(
			'tree_id'      => $tree->getTreeId(),
			'component'    => $component,
			'access_level' => Auth::accessLevel($tree),
		))->fetchOneColumn();

		$array = array();
		foreach ($module_names as $module_name) {
			$interface = '\Fisharebest\Webtrees\Module\Module' . ucfirst($component) . 'Interface';
			$module    = self::getModuleByName($module_name);
			if ($module instanceof $interface) {
				$array[$module_name] = $module;
			}
		}

		// The order of menus/sidebars/tabs is defined in the database.  Others are sorted by name.
		if ($component !== 'menu' && $component !== 'sidebar' && $component !== 'tab') {
			uasort($array, function (AbstractModule $x, AbstractModule $y) {
				return I18N::strcasecmp($x->getTitle(), $y->getTitle());
			});
		}

		return $array;
	}

	/**
	 * Get a list of all modules, enabled or not, which provide a specific function.
	 *
	 * We cannot currently use auto-loading for modules, as there may be user-defined
	 * modules about which the auto-loader knows nothing.
	 *
	 * @param string $component The type of module, such as "tab", "report" or "menu"
	 *
	 * @return AbstractModule[]
	 */
	public static function getAllModulesByComponent($component) {
		$module_names = Database::prepare(
			"SELECT SQL_CACHE module_name" .
			" FROM `##module`" .
			" ORDER BY CASE :component WHEN 'menu' THEN menu_order WHEN 'sidebar' THEN sidebar_order WHEN 'tab' THEN tab_order ELSE 0 END, module_name"
		)->execute(array(
			'component'    => $component,
		))->fetchOneColumn();

		$array = array();
		foreach ($module_names as $module_name) {
			$interface = '\Fisharebest\Webtrees\Module\Module' . ucfirst($component) . 'Interface';
			$module    = self::getModuleByName($module_name);
			if ($module instanceof $interface) {
				$array[$module_name] = $module;
			}
		}

		// The order of menus/sidebars/tabs is defined in the database.  Others are sorted by name.
		if ($component !== 'menu' && $component !== 'sidebar' && $component !== 'tab') {
			uasort($array, function (AbstractModule $x, AbstractModule $y) {
				return I18N::strcasecmp($x->getTitle(), $y->getTitle());
			});
		}

		return $array;
	}

	/**
	 * Get a list of modules which (a) provide a block and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleBlockInterface[]
	 */
	public static function getActiveBlocks(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'block');
	}

	/**
	 * Get a list of modules which (a) provide a chart and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleChartInterface[]
	 */
	public static function getActiveCharts(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'chart');
	}

	/**
	 * Get a list of modules which (a) provide a menu and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleMenuInterface[]
	 */
	public static function getActiveMenus(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'menu');
	}

	/**
	 * Get a list of modules which (a) provide a report and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleReportInterface[]
	 */
	public static function getActiveReports(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'report');
	}

	/**
	 * Get a list of modules which (a) provide a sidebar and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleSidebarInterface[]
	 */
	public static function getActiveSidebars(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'sidebar');
	}

	/**
	 * Get a list of modules which (a) provide a tab and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleTabInterface[]
	 */
	public static function getActiveTabs(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'tab');
	}

	/**
	 * Get a list of modules which (a) provide a theme and (b) we have permission to see.
	 *
	 * @param Tree $tree
	 *
	 * @return ModuleThemeInterface[]
	 */
	public static function getActiveThemes(Tree $tree) {
		return self::getActiveModulesByComponent($tree, 'theme');
	}

	/**
	 * Find a specified module, if it is currently active.
	 *
	 * @param string $module_name
	 *
	 * @return AbstractModule|null
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
	 * @return AbstractModule[]
	 */
	public static function getInstalledModules($default_status) {
		$modules = array();

		foreach (glob(WT_ROOT . WT_MODULES_DIR . '*/module.php') as $file) {
			try {
				$module = include $file;
				if ($module instanceof AbstractModule) {
					$modules[$module->getName()] = $module;
					Database::prepare("INSERT IGNORE INTO `##module` (module_name, status, menu_order, sidebar_order, tab_order) VALUES (?, ?, ?, ?, ?)")->execute(array(
						$module->getName(),
						$default_status,
						$module instanceof ModuleMenuInterface ? $module->defaultMenuOrder() : null,
						$module instanceof ModuleSidebarInterface ? $module->defaultSidebarOrder() : null,
						$module instanceof ModuleTabInterface ? $module->defaultTabOrder() : null,
					));
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
				}
			} catch (\Exception $ex) {
				// Old or invalid module?
			}
		}

		uasort($modules, function (AbstractModule $x, AbstractModule $y) {
			return I18N::strcasecmp($x->getTitle(), $y->getTitle());
		});

		return $modules;
	}

	/**
	 * After creating a new family tree, we need to assign the default access
	 * rights for each module.
	 *
	 * @param int $tree_id
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
