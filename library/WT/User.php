<?php
namespace WT;

use WT_DB;
use WT_Individual;
use WT_Tree;

/**
 * Class User - Provide an interface to the wt_user table.
 *
 * @copyright (c) 2014 webtrees development team
 * @license   This program is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU General Public License as published by
 *            the Free Software Foundation, either version 2 of the License, or
 *            (at your option) any later version.
 *
 *            This program is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU General Public License for more details.
 *
 *            You should have received a copy of the GNU General Public License
 *            along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class User {
	/** @var  string The primary key of this user. */
	private $user_id;

	/** @var  string The login name of this user. */
	private $user_name;

	/** @var  string The real (display) name of this user. */
	private $real_name;

	/** @var  string The email address of this user. */
	private $email;

	/** @var array Cached copy of the wt_user_setting table. */
	private $preferences;

	/** @var  User[] Only fetch users from the database once. */
	private static $cache = array();

	/**
	 * Find the user with a specified user_id.
	 *
	 * @param integer|null $user_id
	 *
	 * @return User|null
	 */
	public static function find($user_id) {
		if (!array_key_exists($user_id, self::$cache)) {
			$row = WT_DB::prepare(
				"SELECT SQL_CACHE user_id, user_name, real_name, email FROM `##user` WHERE user_id = ?"
			)->execute(array($user_id))->fetchOneRow();
			if ($row) {
				self::$cache[$user_id] = new User($row);
			} else {
				self::$cache[$user_id] = null;
			}
		}

		return self::$cache[$user_id];
	}

	/**
	 * Find the user with a specified user_id.
	 *
	 * @param string $identifier
	 *
	 * @return User|null
	 */
	public static function findByIdentifier($identifier) {
		$user_id = WT_DB::prepare(
			"SELECT SQL_CACHE user_id FROM `##user` WHERE ? IN (user_name, email)"
		)->execute(array($identifier))->fetchOne();

		return self::find($user_id);
	}

	/**
	 * Find the user with a specified genealogy record.
	 *
	 * @param WT_Tree       $tree
	 * @param WT_Individual $individual
	 *
	 * @return User|null
	 */
	public static function findByGenealogyRecord(WT_Tree $tree, WT_Individual $individual) {
		$user_id = WT_DB::prepare(
			"SELECT SQL_CACHE user_id" .
			" FROM `##user_gedcom_setting`" .
			" WHERE gedcom_id = ? AND setting_name = 'gedcomid' AND setting_value = ?"
		)->execute(array($tree->tree_id, $individual->getXref()))->fetchOne();

		return self::find($user_id);
	}

	/**
	 * Find the latest user to register.
	 *
	 * @return User|null
	 */
	public static function findLatestToRegister() {
		$user_id = WT_DB::prepare(
			"SELECT SQL_CACHE u.user_id" .
			" FROM `##user` u" .
			" LEFT JOIN `##user_setting` us ON (u.user_id=us.user_id AND us.setting_name='reg_timestamp') " .
			" ORDER BY us.setting_value DESC LIMIT 1"
		)->execute()->fetchOne();

		return self::find($user_id);
	}

	/**
	 * Create a new user.
	 *
	 * The calling code needs to check for duplicates identifiers before calling
	 * this function.
	 *
	 * @param string $user_name
	 * @param string $real_name
	 * @param string $email
	 * @param string $password
	 *
	 * @return User
	 */
	public static function create($user_name, $real_name, $email, $password) {
		WT_DB::prepare(
			"INSERT INTO `##user` (user_name, real_name, email, password) VALUES (?, ?, ?, ?)"
		)->execute(array($user_name, $real_name, $email, password_hash($password, PASSWORD_DEFAULT)));

		return User::findByIdentifier($user_name);
	}

	/**
	 * Get a count of all users.
	 *
	 * @return integer
	 */
	public static function count() {
		return (int)WT_DB::prepare(
			"SELECT SQL_CACHE COUNT(*)" .
			" FROM `##user`" .
			" WHERE user_id > 0"
		)->fetchOne();
	}

	/**
	 * Get a list of all users.
	 *
	 * @return User[]
	 */
	public static function all() {
		$users = array();

		$rows = WT_DB::prepare(
			"SELECT SQL_CACHE user_id, user_name, real_name, email" .
			" FROM `##user`" .
			" WHERE user_id > 0" .
			" ORDER BY user_name"
		)->fetchAll();

		foreach ($rows as $row) {
			$users[] = new User($row);
		}

		return $users;
	}

	/**
	 * Get a list of all administrators.
	 *
	 * @return User[]
	 */
	public static function allAdmins() {
		$rows = WT_DB::prepare(
			"SELECT SQL_CACHE user_id, user_name, real_name, email" .
			" FROM `##user`" .
			" JOIN `##user_setting` USING (user_id)" .
			" WHERE user_id > 0" .
			"   AND setting_name = 'canadmin'" .
			"   AND setting_value = '1'"
		)->fetchAll();

		$users = array();
		foreach ($rows as $row) {
			$users[] = new User($row);
		}

		return $users;
	}

	/**
	 * Get a list of all users who are currently logged in.
	 *
	 * @return User[]
	 */
	public static function allLoggedIn() {
		$rows = WT_DB::prepare(
			"SELECT SQL_NO_CACHE DISTINCT user_id, user_name, real_name, email".
			" FROM `##user`".
			" JOIN `##session` USING (user_id)"
		)->fetchAll();

		$users = array();
		foreach ($rows as $row) {
			$users[] = new User($row);
		}

		return $users;
	}

	/**
	 * Create a new user object from a row in the database.
	 *
	 * @param \stdclass $user A row from the wt_user table
	 */
	public function __construct(\stdClass $user) {
		$this->user_id   = $user->user_id;
		$this->user_name = $user->user_name;
		$this->real_name = $user->real_name;
		$this->email     = $user->email;
	}

	/**
	 * Delete a user
	 */
	function delete() {
		// Don't delete the logs.
		WT_DB::prepare("UPDATE `##log` SET user_id=NULL WHERE user_id =?")->execute(array($this->user_id));
		// Take over the user’s pending changes. (What else could we do with them?)
		WT_DB::prepare("DELETE FROM `##change` WHERE user_id=? AND status='accepted'")->execute(array($this->user_id));
		WT_DB::prepare("UPDATE `##change` SET user_id=? WHERE user_id=?")->execute(array($this->user_id, $this->user_id));
		WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE user_id=?")->execute(array($this->user_id));
		WT_DB::prepare("DELETE FROM `##block` WHERE user_id=?")->execute(array($this->user_id));
		WT_DB::prepare("DELETE FROM `##user_gedcom_setting` WHERE user_id=?")->execute(array($this->user_id));
		WT_DB::prepare("DELETE FROM `##gedcom_setting` WHERE setting_value=? AND setting_name in ('CONTACT_USER_ID', 'WEBMASTER_USER_ID')")->execute(array($this->user_id));
		WT_DB::prepare("DELETE FROM `##user_setting` WHERE user_id=?")->execute(array($this->user_id));
		WT_DB::prepare("DELETE FROM `##message` WHERE user_id=?")->execute(array($this->user_id));
		WT_DB::prepare("DELETE FROM `##user` WHERE user_id=?")->execute(array($this->user_id));
	}

	/** Validate a supplied password
	 *
	 * @param string $password
	 *
	 * @return boolean
	 */
	public function checkPassword($password) {
		$password_hash = WT_DB::prepare(
			"SELECT password FROM `##user` WHERE user_id = ?"
		)->execute(array($this->user_id))->fetchOne();

		if (password_verify($password, $password_hash)) {
			if (password_needs_rehash($password_hash, PASSWORD_DEFAULT)) {
				$this->setPassword($password);
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the numeric ID for this user.
	 *
	 * @return string
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * Get the login name for this user.
	 *
	 * @return string
	 */
	public function getUserName() {
		return $this->user_name;
	}

	/**
	 * Set the login name for this user.
	 *
	 * @param string $user_name
	 *
	 * @return $this
	 */
	public function setUserName($user_name) {
		if ($this->user_name !== $user_name) {
			$this->user_name = $user_name;
			WT_DB::prepare(
				"UPDATE `##user` SET user_name = ? WHERE user_id = ?"
			)->execute(array($user_name, $this->user_id));
		}

		return $this;
	}

	/**
	 * Get the real name of this user.
	 *
	 * @return string
	 */
	public function getRealName() {
		return $this->real_name;
	}

	/**
	 * Set the real name of this user.
	 *
	 * @param string $real_name
	 *
	 * @return User
	 */
	public function setRealName($real_name) {
		if ($this->real_name !== $real_name) {
			$this->real_name = $real_name;
			WT_DB::prepare(
				"UPDATE `##user` SET real_name = ? WHERE user_id = ?"
			)->execute(array($real_name, $this->user_id));
		}

		return $this;
	}

	/**
	 * Get the email address of this user.
	 *
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * Set the email address of this user.
	 *
	 * @param string $email
	 *
	 * @return User
	 */
	public function setEmail($email) {
		if ($this->email !== $email) {
			$this->email = $email;
			WT_DB::prepare(
				"UPDATE `##user` SET email = ? WHERE user_id = ?"
			)->execute(array($email, $this->user_id));
		}

		return $this;
	}

	/**
	 * Set the password of this user.
	 *
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword($password) {
		WT_DB::prepare(
			"UPDATE `##user` SET password = ? WHERE user_id = ?"
		)->execute(array(password_hash($password, PASSWORD_DEFAULT), $this->user_id));

		return $this;
	}

	/**
	 * Fetch a user option/setting from the wt_user_setting table.
	 *
	 * Since we'll fetch several settings for each user, and since there aren’t
	 * that many of them, fetch them all in one database query
	 *
	 * @param string      $setting_name
	 * @param string|null $default
	 *
	 * @return string|null
	 */
	public function getPreference($setting_name, $default = null) {
		if ($this->preferences === null) {
			if ($this->user_id) {
				$this->preferences = WT_DB::prepare(
					"SELECT SQL_CACHE setting_name, setting_value FROM `##user_setting` WHERE user_id = ?"
				)->execute(array($this->user_id))->fetchAssoc();
			} else {
				// Not logged in?  We have no preferences.
				$this->preferences = array();
			}
		}

		if (array_key_exists($setting_name, $this->preferences)) {
			return $this->preferences[$setting_name];
		} else {
			return $default;
		}
	}

	/**
	 * Update a setting for the user.
	 *
	 * @param string $setting_name
	 * @param string $setting_value
	 *
	 * @return User
	 */
	public function setPreference($setting_name, $setting_value) {
		if ($this->user_id && $this->getPreference($setting_name) !== $setting_value) {
			WT_DB::prepare("REPLACE INTO `##user_setting` (user_id, setting_name, setting_value) VALUES (?, ?, LEFT(?, 255))")
				->execute(array($this->user_id, $setting_name, $setting_value));
			$this->preferences[$setting_name] = $setting_value;
		}

		return $this;
	}

	/**
	 * Delete a setting for the user.
	 *
	 * @param string $setting_name
	 *
	 * @return User
	 */
	public function deletePreference($setting_name) {
		if ($this->user_id && $this->getPreference($setting_name) !== null) {
			WT_DB::prepare("DELETE FROM `##user_setting` WHERE user_id = ? AND setting_name = ?")
				->execute(array($this->user_id, $setting_name));
			unset($this->preferences[$setting_name]);
		}

		return $this;
	}
}
