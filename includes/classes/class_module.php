<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 John Finlay
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
	public function getConfigLink(); // URL of page to edit config
}

interface WT_Module_Menu {
	public function defaultMenuOrder(); // 0-127.  Use multiples of 10 to allow third-party modules to choose position
}

interface WT_Module_Sidebar {
	public function defaultSidebarOrder(); // 0-127.  Use multiples of 10 to allow third-party modules to choose position
	public function getSidebarContent();
	public function getSidebarAjaxContent();
	public function hasSidebarContent();
}

interface WT_Module_Tab {
	public function defaultTabOrder(); // 0-127.  Use multiples of 10 to allow third-party modules to choose position
	public function getTabContent();
	public function hasTabContent();
	public function canLoadAjax();
	public function getPreLoadContent();
	public function getJSCallbackAllTabs();
	public function getJSCallback();
}

abstract class WT_Module {
	private $id = 0;
	private $accessLevel = array();
	private $menuEnabled = array();
	private $tabEnabled = array();
	private $sidebarEnabled = array();
	private $taborder = 99;
	private $menuorder = 99;
	private $sidebarorder = 99;

	protected $controller;

	public static $default_tabs = array('family_nav', 'personal_facts', 'sources_tab', 'notes', 'media', 'lightbox', 'tree', 'googlemap', 'relatives', 'all_tab');
	public static $default_sidebars = array('descendancy', 'family_nav', 'clippings', 'individuals', 'families');
	public static $default_menus = array('page_menu');

	// Each module must provide the following functions
	abstract public function getTitle();       // To label tabs, etc.
	abstract public function getDescription(); // A sentence describing what this module does

	// This is the default for all the module's components.
	// Returns one of: WT_PRIV_HIDE, WT_PRIV_PUBLIC, WT_PRIV_USER, WT_PRIV_ADMIN
	public function defaultAccessLevel() {
		return WT_PRIV_PUBLIC;
	}

	// This is an internal name, used to generate identifiers
	final public function getName() {
		return str_replace('_WT_Module', '', get_class($this));
	}

	// Reference the parent page's controller
	public function &getController() {
		return $this->controller;
	}
	public function setController(&$c) {
		$this->controller=$c;
	}

	/**
	 * Get an instance of the desired module class based on a db row
	 * @param $row
	 * @return WT_Module
	 */
	static function &getInstance($row) {
		$entry=$row->mod_name;
		if (file_exists("modules/$entry/pgv_module.php")) {
			include_once("modules/$entry/pgv_module.php");
			$menu_class = $entry."_WT_Module";
			$obj = new $menu_class();
			$obj->setId($row->mod_id);
			$obj->setTaborder($row->mod_taborder);
			$obj->setMenuorder($row->mod_menuorder);
			$obj->setSidebarorder($row->mod_sidebarorder);
			return $obj;
		}
		return null;
	}

	//-- getters and setters
	public function getId() { return $this->id; }
	public function getTaborder() { return $this->taborder; }
	public function getMenuorder() { return $this->menuorder; }
	public function getSidebarorder() { return $this->sidebarorder; }
	public function getAccessLevel($gedId = WT_GED_ID) {
		if (!isset($this->accessLevel[$gedId])) $this->accessLevel[$gedId] = WT_PRIV_PUBLIC;
		return $this->accessLevel[$gedId];
	}
	public function getMenuEnabled($gedId = WT_GED_ID) {
		if (!isset($this->menuEnabled[$gedId])) $this->menuEnabled[$gedId] = WT_PRIV_PUBLIC;
		return $this->menuEnabled[$gedId];
	}
	public function getTabEnabled($gedId = WT_GED_ID) {
		if (!isset($this->tabEnabled[$gedId])) $this->tabEnabled[$gedId] = WT_PRIV_PUBLIC;
		return $this->tabEnabled[$gedId];
	}
	public function getSidebarEnabled($gedId = WT_GED_ID) {
		if (!isset($this->sidebarEnabled[$gedId])) $this->sidebarEnabled[$gedId] = WT_PRIV_PUBLIC;
		return $this->sidebarEnabled[$gedId];
	}
	public function getAccessLevelArray() {
		return $this->accessLevel;
	}
	public function getMenuEnabledArray() {
		return $this->menuEnabled;
	}
	public function getTabEnabledArray() {
		return $this->tabEnabled;
	}
	public function getSidebarEnabledArray() {
		return $this->sidebarEnabled;
	}
	public function setId($id) { $this->id = $id; }
	public function setMenuorder($o) { $this->menuorder = $o; }
	public function setTaborder($o) { $this->taborder = $o; }
	public function setSidebarorder($o) { $this->sidebarorder = $o; }

	public function setAccessLevel($access, $gedId=WT_GED_ID) {
		$this->accessLevel[$gedId] = $access;
	}
	public function setMenuEnabled($access, $gedId=WT_GED_ID) {
		$this->menuEnabled[$gedId] = $access;
	}
	public function setTabEnabled($access, $gedId=WT_GED_ID) {
		$this->tabEnabled[$gedId] = $access;
	}
	public function setSidebarEnabled($access, $gedId=WT_GED_ID) {
		$this->sidebarEnabled[$gedId] = $access;
	}
	public function setGeneralAccess($type, $access, $gedId) {
		switch($type) {
			case 'A':
				$this->setAccessLevel($access, $gedId);
				break;
			case 'T':
				$this->setTabEnabled($access, $gedId);
				break;
			case 'M':
				$this->setMenuEnabled($access, $gedId);
				break;
			case 'S':
				$this->setSidebarEnabled($access, $gedId);
				break;
		}
	}

	static function compare_tab_order(&$a, &$b) {
		return $a->getTaborder() - $b->getTaborder();
	}

	static function compare_menu_order(&$a, &$b) {
		return $a->getMenuorder() - $b->getMenuorder();
	}

	static function compare_sidebar_order(&$a, &$b) {
		return $a->getSidebarorder() - $b->getSidebarorder();
	}

	static function compare_name(&$a, &$b) {
		return strcmp($a->getName(), $b->getName());
	}

	static function getActiveList($type='A', $access = WT_USER_ACCESS_LEVEL, $ged_id = WT_GED_ID) {
		global $TBLPREFIX;

		$modules = array();
		$statement=WT_DB::prepare(
			"SELECT * FROM {$TBLPREFIX}module JOIN {$TBLPREFIX}module_privacy ON mod_id=mp_mod_id WHERE mp_access>=? AND mp_type='{$type}' AND mp_file=?"
		);
		$statement->execute(array($access, $ged_id));
		$entry = "";
		while($row = $statement->fetch()) {
			if ($row->mod_name!=$entry) {
				$entry = $row->mod_name;
				$mod = WT_Module::getInstance($row);
				if ($mod) {
					$modules[$entry] = $mod;
					$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
				}
				else AddToLog("Invalide module ".$entry);
			}
			else {
				$mod = $modules[$entry];
				$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
			}

		}
		return $modules;
	}

	static function getActiveListAllGeds($access = WT_USER_ACCESS_LEVEL) {
		global $TBLPREFIX;

		$modules = array();
		$statement=WT_DB::prepare(
			"SELECT * FROM {$TBLPREFIX}module JOIN {$TBLPREFIX}module_privacy ON mod_id=mp_mod_id WHERE mp_access>=?"
		);
		$statement->execute(array($access));
		$entry = "";
		while($row = $statement->fetch()) {
			if ($row->mod_name!=$entry) {
				$entry = $row->mod_name;
				$mod = WT_Module::getInstance($row);
				if ($mod) { 
					$modules[$entry] = $mod;
					$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
				}
				else AddToLog("Invalide module ".$entry);
			}
			else {
				$mod = $modules[$entry];
				$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
			}

		}
		return $modules;
	}

	static function getInstalledList() {
		static $modules;
		if ($modules==null) {
			$modules = array();
			if (!file_exists("modules")) return $this->modules;
			$d = dir("modules");
			while (false !== ($entry = $d->read())) {
				if ($entry{0}!="." && $entry!=".svn" && is_dir("modules/$entry")) {
					if (file_exists("modules/$entry/pgv_module.php")) {
						include_once("modules/$entry/pgv_module.php");
						$menu_class = $entry."_WT_Module";
						$obj = new $menu_class();
						$mod = WT_Module::getModuleByName($entry);
						if ($mod!=null) {
							$modules[$entry] = $mod;
						} else {
							$modules[$entry] = $obj;
						}
					}
				}
			}
			$d->close();
		}
		return $modules;
	}

	static function getModuleByName($name) {
		global $TBLPREFIX;

		$stmt = WT_DB::prepare("SELECT * FROM {$TBLPREFIX}module JOIN {$TBLPREFIX}module_privacy ON mod_id=mp_mod_id WHERE mod_name=?");
		$stmt->execute(array($name));
		$row = $stmt->fetchOne();
		$entry = "";
		$mod = null;
		while($row = $stmt->fetch()) {
			if ($row->mod_name!=$entry) {
				$entry = $row->mod_name;
				$mod = WT_Module::getInstance($row);
				$modules[$entry] = $mod;
				$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
			}
			else {
				$mod = $modules[$entry];
				$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
			}

		}
		return $mod;
	}

	static function getById($id) {
		global $TBLPREFIX;

		$stmt = WT_DB::prepare("SELECT * FROM {$TBLPREFIX}module JOIN {$TBLPREFIX}module_privacy ON mod_id=mp_mod_id WHERE mod_id=?");
		$stmt->execute(array($id));
		$row = $stmt->fetchOne();
		$entry = "";
		$mod = null;
		while($row = $stmt->fetch()) {
			if ($row->mod_name!=$entry) {
				$entry = $row->mod_name;
				$mod = WT_Module::getInstance($row);
				$modules[$entry] = $mod;
				$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
			}
			else {
				$mod = $modules[$entry];
				$mod->setGeneralAccess($row->mp_type, $row->mp_access, $row->mp_file);
			}

		}
		return $mod;
	}

	/**
	 * Insert or Update a module in the database
	 * @param $mod WT_Module
	 * @return null
	 */
	static function updateModule(&$mod) {
		global $TBLPREFIX;
		if ($mod->getId()==0) {
			$sql = "insert into {$TBLPREFIX}module (mod_id, mod_name, mod_description, mod_taborder, mod_menuorder, mod_sidebarorder) values(?,?,?,?,?,?)";
			$stmt = WT_DB::prepare($sql);
			$mod->setId(get_next_id("module","mod_id"));
			$stmt->execute(array($mod->getId(),$mod->getName(), $mod->getDescription(), $mod->getTaborder(), $mod->getMenuorder(), $mod->getSidebarorder()));
			$sql = "insert into {$TBLPREFIX}module_privacy (mp_mod_id,mp_file,mp_access,mp_type) values(?,?,?,?)";
			$stmt = WT_DB::prepare($sql);
			foreach ($mod->getAccessLevelArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'A'));
			}
			foreach ($mod->getMenuEnabledArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'M'));
			}
			foreach ($mod->getTabEnabledArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'T'));
			}
			foreach ($mod->getSidebarEnabledArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'S'));
			}
		}
		else {
			$sql = "UPDATE {$TBLPREFIX}module SET mod_name=?, mod_description=?, mod_taborder=?, mod_menuorder=?, mod_sidebarorder=? WHERE mod_id=?";
			$stmt = WT_DB::prepare($sql);
			$stmt->execute(array($mod->getName(), $mod->getDescription(), $mod->getTaborder(), $mod->getMenuorder(), $mod->getSidebarorder(), $mod->getId()));

			//-- delete the old privacy settings
			$sql = "delete from {$TBLPREFIX}module_privacy where mp_mod_id=?";
			$stmt = WT_DB::prepare($sql);
			$stmt->execute(array($mod->getId()));

			//-- store the new privacy settings
			$sql = "insert into {$TBLPREFIX}module_privacy (mp_mod_id,mp_file,mp_access,mp_type) values(?,?,?,?)";
			$stmt = WT_DB::prepare($sql);
			foreach ($mod->getAccessLevelArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'A'));
			}
			foreach ($mod->getMenuEnabledArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'M'));
			}
			foreach ($mod->getTabEnabledArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'T'));
			}
			foreach ($mod->getSidebarEnabledArray() as $ged_id=>$mp) {
				$stmt->execute(array($mod->getId(), $ged_id, $mp, 'S'));
			}
		}
	}

	static function setDefaultTabs($ged_id) {
		$modules = WT_Module::getInstalledList();
		$taborder = 1;
		foreach(self::$default_tabs as $modname) {
			if (isset($modules[$modname])) {
				$mod = $modules[$modname];
				if ($mod instanceof WT_Module_Tab) {
					$mod->setTaborder($taborder);
					$mod->setAccessLevel(WT_PRIV_PUBLIC, $ged_id);
					$mod->setTabEnabled(WT_PRIV_PUBLIC, $ged_id);
					WT_Module::updateModule($mod);
					$taborder++;
				}
			}
		}
	}

	static function setDefaultMenus($ged_id) {
		$modules = WT_Module::getInstalledList();
		$taborder = 0;
		foreach(self::$default_menus as $modname) {
			if (isset($modules[$modname])) {
				$mod = $modules[$modname];
				if ($mod instanceof WT_Module_Menu) {
					$mod->setMenuorder($taborder);
					$mod->setAccessLevel(WT_PRIV_PUBLIC, $ged_id);
					$mod->setMenuEnabled(WT_PRIV_PUBLIC, $ged_id);
					WT_Module::updateModule($mod);
					$taborder++;
				}
			}
		}
	}

	static function setDefaultSidebars($ged_id) {
		$modules = WT_Module::getInstalledList();
		$taborder = 0;
		foreach(self::$default_sidebars as $modname) {
			if (isset($modules[$modname])) {
				$mod = $modules[$modname];
				if ($mod instanceof WT_Module_Sidebar) {
					$mod->setSidebarorder($taborder);
					$mod->setAccessLevel(WT_PRIV_PUBLIC, $ged_id);
					$mod->setSidebarEnabled(WT_PRIV_PUBLIC, $ged_id);
					WT_Module::updateModule($mod);
					$taborder++;
				}
			}
		}
	}
}
?>
