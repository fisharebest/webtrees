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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\Tree;

/**
 * Class AbstractModule - common functions for blocks
 */
abstract class AbstractModule {
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
	 * Get a block setting.
	 *
	 * @param int         $block_id
	 * @param string      $setting_name
	 * @param string|null $default_value
	 *
	 * @return null|string
	 */
	public function getBlockSetting($block_id, $setting_name, $default_value = null) {
		$setting_value = Database::prepare(
			"SELECT SQL_CACHE setting_value FROM `##block_setting` WHERE block_id = :block_id AND setting_name = :setting_name"
		)->execute(array(
			'block_id'     => $block_id,
			'setting_name' => $setting_name,
		))->fetchOne();

		return $setting_value === null ? $default_value : $setting_value;
	}

	/**
	 * Set a block setting.
	 *
	 * @param int         $block_id
	 * @param string      $setting_name
	 * @param string|null $setting_value
	 *
	 * @return $this
	 */
	public function setBlockSetting($block_id, $setting_name, $setting_value) {
		if ($setting_value === null) {
			Database::prepare(
				"DELETE FROM `##block_setting` WHERE block_id = :block_id AND setting_name = :setting_name"
			)->execute(array(
					'block_id'     => $block_id,
					'setting_name' => $setting_name,
			));
		} else {
			Database::prepare(
				"REPLACE INTO `##block_setting` (block_id, setting_name, setting_value) VALUES (:block_id, :setting_name, :setting_value)"
			)->execute(array(
				'block_id'      => $block_id,
				'setting_name'  => $setting_name,
				'setting_value' => $setting_value,
			));
		}

		return $this;
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
	 * @return int
	 */
	public function defaultAccessLevel() {
		// Returns one of: Auth::PRIV_HIDE, Auth::PRIV_PRIVATE, Auth::PRIV_USER, WT_PRIV_ADMIN
		return Auth::PRIV_PRIVATE;
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
	 * @param string $component tab, block, menu, etc
	 *
	 * @return int
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
}
