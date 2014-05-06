<?php
// Provide an interface to the wt_user table
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team
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

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_User {
	// Reasons why a user authentication attempt failed
	const ACCOUNT_NOT_APPROVED = 'ACCOUNT_NOT_APPROVED';
	const ACCOUNT_NOT_VERIFIED = 'ACCOUNT_NOT_VERIFIED';
	const INCORRECT_PASSWORD   = 'INCORRECT_PASSWORD';
	const NO_SESSION_COOKIES   = 'NO_SESSION_COOKIES';
	const NO_SUCH_USER         = 'NO_SUCH_USER';
	// Reasons why a user account cannot be created
	const DUPLICATE_EMAIL      = 'DUPLICATE_EMAIL';
	const DUPLICATE_USER_NAME  = 'DUPLICATE_USER_NAME';
	const PASSWORD_MISMATCH    = 'PASSWORD_MISMATCH';

	// Attributes of the user, from the wt_user table
	private $user_id;
	private $user_name;
	private $real_name;
	private $email;

	/** @var array $settings Settings for the user, from the wt_user_setting table */
	private $settings;

	// The current user
	private static $current_user;

	/**
	 * Who is the currently logged in user?
	 *
	 * @return WT_User
	 */
	public static function currentUser() {
		global $WT_SESSION;

		if (self::$current_user === null) {
			self::$current_user = new WT_User($WT_SESSION->wt_user);
		}

		return self::$current_user;
	}

	/**
	 * Is a user currently logged in?
	 *
	 * @return bool
	 */
	public function isLoggedIn() {
		global $WT_SESSION;

		return $this->getUserId() !== null && $this->getUserId() === $WT_SESSION->wt_user;
	}


	/** Authenticate a username/password combination.
	 *
	 * @param string $identity Username or email address
	 * @param string $password
	 *
	 * @return WT_User
	 * @throws Exception
	 *
	 */
	public static function login($identity, $password) {
		global $WT_SESSION;

		self::passwordCompatibility(); // For PHP <= 5.4

		// If no cookies are available, then we cannot log in.
		if (empty($_COOKIE)) {
			throw new Exception(self::NO_SESSION_COOKIES);
		}

		$row = WT_DB::prepare(
			"SELECT SQL_CACHE user_id, password FROM `wt_user` WHERE user_name = ? OR email = ?"
		)->execute(array($identity, $identity))->fetchOneRow();

		if (!$row) {
			// No such user with that username or email address
			throw new Exception(self::NO_SUCH_USER);
		}

		$user = new WT_User($row->user_id);
		if (!password_verify($password, $row->password)) {
			// Incorrect password
			throw new Exception(self::INCORRECT_PASSWORD);
		}

		// Was the password hash created using an old or insecure algorithm?
		if (password_needs_rehash($row->password, PASSWORD_DEFAULT)) {
			// Generate a new hash
			$user->setPassword($row->password);
		}

		if (!$user->getSetting('verified') && !$user->getSetting('canadmin')) {
			// The user has not verified their email address.  (Administrators do not need to be approved.)
			throw new Exception(self::ACCOUNT_NOT_VERIFIED);
		}

		if (!$user->getSetting('verified_by_admin') && !$user->getSetting('canadmin')) {
			// An administrator has not approved the account.  (Administrators do not need to be approved.)
			throw new Exception(self::ACCOUNT_NOT_APPROVED);
		}

		// All checks passed.  The user is permitted to log in.
		$WT_SESSION->wt_user = $user->getUserId();

		// Whenever we change our authorization level, change the session ID.
		Zend_Session::regenerateId();

		return $user;
	}

	/**
	 * End the session for the current user
	 *
	 * @return void
	 */
	public static function logout() {
		if (WT_User::currentUser()->isLoggedIn()) {
			self::$current_user = null;
			Zend_Session::destroy();
		}
	}

	/**
	 * Are we currently logged in as an administrator?
	 *
	 * @return bool
	 */
	public function isAdmin() {
		return $this->getSetting('canadmin');
	}

	/**
	 * Are we logged in as a manager of a particular tree?
	 *
	 * @param WT_Tree $tree
	 *
	 * @return bool
	 */
	public function isManager(WT_Tree $tree) {
		return $this->isAdmin() || $tree->userPreference($this->currentUser()->getUserId(), 'canedit') === 'admin';
	}

	/**
	 * Are we logged in as a moderator of a particular tree?
	 *
	 * @param WT_Tree $tree
	 *
	 * @return bool
	 */
	public function isModerator(WT_Tree $tree) {
		return $this->isManager($tree) || $tree->userPreference($this->currentUser()->getUserId(), 'canedit') === 'accept';
	}

	/**
	 * Are we logged in as an editor of a particular tree?
	 *
	 * @param WT_Tree $tree
	 *
	 * @return bool
	 */
	public function isEditor(WT_Tree $tree) {
		return $this->isModerator($tree) || $tree->userPreference($this->currentUser()->getUserId(), 'canedit') === 'edit';
	}

	/**
	 * Are we logged in as a member of a particular tree?
	 *
	 * @param WT_Tree $tree
	 *
	 * @return bool
	 */
	public function isMember(WT_Tree $tree) {
		return $this->isEditor($tree) || $tree->userPreference($this->currentUser()->getUserId(), 'canedit') === 'access';
	}

	/**
	 * Create a new user.
	 *
	 * @param string $user_name
	 * @param string $real_name
	 * @param string $email
	 * @param string $password1
	 * @param string $password2
	 *
	 * @return WT_User
	 *
	 * @throws Exception
	 */
	public static function create($user_name, $real_name, $email, $password1, $password2) {
		self::passwordCompatibility(); // For PHP <= 5.4

		if ($password1 !== $password2) {
			throw new Exception(self::PASSWORD_MISMATCH);
		}

		if (WT_User::userNameExists($user_name)) {
			throw new Exception(self::DUPLICATE_USER_NAME);
		}

		if (WT_User::emailExists($email)) {
			throw new Exception(self::DUPLICATE_EMAIL);
		}

		WT_DB::prepare(
			"INSERT INTO `##user` (user_name, real_name, email, password) VALUES (?, ?, ?, ?)"
		)->execute(array($user_name, $real_name, $email, password_hash($password1, PASSWORD_DEFAULT)));

		$user = new WT_User();
		$user->user_id = WT_DB::prepare("SELECT LAST_INSERT_ID()")->fetchOne();
		$user->user_name = $user_name;
		$user->real_name = $real_name;
		$user->email = $email;

		return $user;
	}

	/**
	 * Get a list of all users.
	 *
	 * @return array
	 */
	public static function getAll() {
		$users = array();

		$rows = WT_DB::prepare(
			"SELECT SQL_CACHE user_id, user_name, real_name, email" .
			" FROM `##user`" .
			" WHERE user_id > 0" .
			" ORDER BY user_name"
		)->fetchAll();

		foreach ($rows as $row) {
			$users[] = new WT_User($row->user_id, $row->user_name, $row->real_name, $row->email);
		}

		return $users;
	}

	/**
	 * Get a list of all administrators.
	 *
	 * @return array
	 */
	public static function getAdmins() {
		$users = array();

		$rows = WT_DB::prepare(
			"SELECT SQL_CACHE user_id, user_name, real_name, email" .
			" FROM `##user`" .
			" JOIN `##user_setting` USING (user_id)" .
			" WHERE user_id > 0" .
			"   AND setting_name = 'canadmin'" .
			"   AND setting_value = '1'" .
			" ORDER BY user_name"
		)->fetchAll();

		foreach ($rows as $row) {
			$users[] = new WT_User($row->user_id, $row->user_name, $row->real_name, $row->email);
		}

		return $users;
	}

	/**
	 * Check whether a username is already used by an existing user.
	 *
	 * @param string $user_name
	 *
	 * @return bool
	 */
	public static function userNameExists($user_name) {
		return WT_DB::prepare(
			"SELECT SQL_CACHE 1 FROM `##user` WHERE user_name = ?"
		)->execute(array($user_name))->fetchOne() !== null;
	}

	/**
	 * Check whether an email address is already used by an existing user.
	 *
	 * @param string $email
	 *
	 * @return bool
	 */
	public static function emailExists($email) {
		return WT_DB::prepare(
			"SELECT SQL_CACHE 1 FROM `##user` WHERE email = ?"
		)->execute(array($email))->fetchOne() !== null;
	}

	/**
	 * Create a new user object from a row in the database.
	 *
	 * @param integer|null $user_id
	 */
	public function __construct($user_id = null, $user_name = null, $real_name = null, $email = null) {
		if ($user_id !== null) {
			if ($user_name === null) {
				$row = WT_DB::prepare(
					"SELECT SQL_CACHE user_name, real_name, email FROM `##user` WHERE user_id = ?"
				)->execute(array($user_id))->fetchOneRow();

				if ($row) {
					$this->user_id   = $user_id;
					$this->user_name = $row->user_name;
					$this->real_name = $row->real_name;
					$this->email     = $row->email;
				}
			} else {
				$this->user_id   = $user_id;
				$this->user_name = $user_name;
				$this->real_name = $real_name;
				$this->email     = $email;
			}
		}
	}

	/**
	 * Delete a user
	 *
	 * @param $user_id
	 */
	function delete() {
		// Don't delete the logs.
		WT_DB::prepare("UPDATE `##log` SET user_id=NULL   WHERE user_id =?")->execute(array($this->getUserId()));
		// Take over the user’s pending changes.
		// TODO: perhaps we should prevent deletion of users with pending changes?
		WT_DB::prepare("DELETE FROM `##change` WHERE user_id=? AND status='accepted'")->execute(array($this->getUserId()));
		WT_DB::prepare("UPDATE `##change` SET user_id=? WHERE user_id=?")->execute(array(WT_USER_ID, $this->getUserId()));

		WT_DB::prepare("DELETE `##block_setting` FROM `##block_setting` JOIN `##block` USING (block_id) WHERE user_id=?")->execute(array($this->getUserId()));
		WT_DB::prepare("DELETE FROM `##block`               WHERE user_id=?"    )->execute(array($this->getUserId()));
		WT_DB::prepare("DELETE FROM `##user_gedcom_setting` WHERE user_id=?"    )->execute(array($this->getUserId()));
		WT_DB::prepare("DELETE FROM `##user_setting`        WHERE user_id=?"    )->execute(array($this->getUserId()));
		WT_DB::prepare("DELETE FROM `##message`             WHERE user_id=?"    )->execute(array($this->getUserId()));
		WT_DB::prepare("DELETE FROM `##user`                WHERE user_id=?"    )->execute(array($this->getUserId()));
	}

	// Getters and setters for user attributes
	public function getUserId() {
		return $this->user_id;
	}

	private function setUserId($user_id) {
		$this->user_id = $user_id;
	}

	public function getUserName() {
		return $this->user_name;
	}

	public function setUserName($user_name) {
		if ($this->user_name !== $user_name) {
			$this->user_name = $user_name;
			WT_DB::prepare(
				"UPDATE `##user` SET user_name = ? WHERE user_id = ?"
			)->execute(array($user_name, $this->user_id));
		}

		return $this;
	}

	public function getRealName() {
		return $this->real_name;
	}

	public function setRealName($real_name) {
		if ($this->real_name !== $real_name) {
			$this->real_name = $real_name;
			WT_DB::prepare(
				"UPDATE `##user` SET real_name = ? WHERE user_id = ?"
			)->execute(array($real_name, $this->user_id));
		}

		return $this;
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		if ($this->email !== $email) {
			$this->email = $email;
			WT_DB::prepare(
				"UPDATE `##user` SET email = ? WHERE user_id = ?"
			)->execute(array($email, $this->user_id));
		}

		return $this;
	}

	public function setPassword($password) {
		self::passwordCompatibility(); // For PHP <= 5.4

		WT_DB::prepare(
			"UPDATE `##user` SET password = ? WHERE user_id = ?"
		)->execute(array(password_hash($password, PASSWORD_DEFAULT), $this->user_id));

		return $this;
	}

	/**
	 * Fetch a user option/setting from the wt_user_setting table
	 *
	 * Since we'll fetch several settings for each user, and since there aren’t
	 * that many of them, fetch them all in one database query
	 *
	 * @param string      $setting_name
	 * @param string|null $default
	 *
	 * @return string
	 */
	public function getSetting($setting_name, $default = null) {
		if ($this->settings === null) {
			if ($this->getUserId()) {
				$this->settings = WT_DB::prepare(
					"SELECT SQL_CACHE setting_name, setting_value FROM `##user_setting` WHERE user_id = ?"
				)->execute(array($this->user_id))->fetchAssoc();
			} else {
				$this->settings = array();
			}
		}

		if (array_key_exists($setting_name, $this->settings)) {
			return $this->settings[$setting_name];
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
	 * @return WT_User
	 */
	public function setSetting($setting_name, $setting_value) {
		if ($setting_value === null) {
			WT_DB::prepare("DELETE FROM `##user_setting` WHERE user_id=? AND setting_name=?")
				->execute(array($this->user_id, $setting_name));
			unset($this->settings[$setting_name]);
		} elseif ($this->settings[$setting_name] !== $setting_value) {
			WT_DB::prepare("REPLACE INTO `##user_setting` (user_id, setting_name, setting_value) VALUES (?, ?, LEFT(?, 255))")
				->execute(array($this->user_id, $setting_name, $setting_value));
			$this->settings[$setting_name] = $setting_value;
		}

		return $this;
	}

	/**
	 * Allow old versions of PHP to use the new password functions
	 *
	 * PHP5.5 provides new password hash functions.
	 * For earlier versions, use a compatibility library.
	 */
	private static function passwordCompatibility() {
		if (!function_exists('password_hash')) {
			// The compatibility library requires the $2$y salt prefix, which is available in
			// PHP5.3.7 and *some* earlier/patched versions.
			$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
			if (crypt("password", $hash) === $hash) {
				require 'library/ircmaxell/password-compat/lib/password.php';
			} else {
				// For older/unpatched versions of PHP, use the default crypt behaviour.
				function password_hash($password) {
					$salt = '$2a$12$';
					$salt_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
					for ($i = 0; $i < 22; ++$i) {
						$salt .= substr($salt_chars, mt_rand(0, 63), 1);
					}
					return crypt($password, $salt);
				}

				function password_needs_rehash() {
					return false;
				}

				function password_verify($password, $hash) {
					return crypt($password, $hash) === $hash;
				}

				define('PASSWORD_DEFAULT', 1);
			}
		}
	}
}