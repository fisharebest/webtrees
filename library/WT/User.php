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
	const ERROR_ACCOUNT_NOT_VERIFIED = -1;
	const ERROR_ACCOUNT_NOT_APPROVED = -2;
	const ERROR_INCORRECT_PASSWORD   = -3;
	const ERROR_NO_SUCH_USER         = -4;
	const ERROR_NO_SESSION_COOKIES   = -5;
	// Reasons why a user account cannot be created
	const ERROR_DUPLICATE_USER_NAME  = -6;
	const ERROR_DUPLICATE_EMAIL      = -7;

	// Attributes of the user, from the wt_user table
	private $user_id;
	private $user_name;
	private $real_name;
	private $email;

	// Settings for the user, from the wt_user_setting table
	private $settings;

	public function __construct($user_id) {
		$row = WT_DB::prepare(
			"SELECT SQL_CACHE user_name, real_name, email FROM `##user` WHERE user_id = ?"
		)->execute(array($user_id))->fetchOneRow();

		$this->user_id   = $user_id;
		$this->user_name = $row->user_name;
		$this->real_name = $row->real_name;
		$this->email     = $row->email;
	}

	// Create a new user.
	//
	// On success, return the user_id of the account.
	// On failure, return the reason for failure.
	public function create($user_name, $real_name, $email, $password) {
		self::passwordCompatibility();

		if (WT_DB::prepare("SELECT 1 FROM `##user` WHERE user_name = ?")->execute(array($user_name))->fetchOne()) {
			return self::ERROR_DUPLICATE_USER_NAME;
		}

		if (WT_DB::prepare("SELECT 1 FROM `##user` WHERE email = ?")->execute(array($email))->fetchOne()) {
			return self::ERROR_DUPLICATE_EMAIL;
		}

		WT_DB::prepare(
			"INSERT INTO `##user` (user_name, real_name, email, password) VALUES (?, ?, ?, ?)"
		)->execute(array($user_name, $real_name, $email, password_hash($password, PASSWORD_DEFAULT)));

		return WT_DB::prepare("SELECT LAST_INSERT_ID()")->fetchOne();
	}

	// Getters and setters for user attributes
	public function getUserId() {
		return $this->user_id;
	}

	public function getUserName() {
		return $this->user_name;
	}

	public function setUserName($user_name) {
		$this->user_name = $user_name;
		WT_DB::prepare(
			"UPDATE `##user` SET user_name = ? WHERE user_id = ?"
		)->execute(array($user_name, $this->user_id));
	}

	public function getRealName() {
		return $this->real_name;
	}

	public function setRealName($real_name) {
		$this->real_name = $real_name;
		WT_DB::prepare(
			"UPDATE `##user` SET real_name = ? WHERE user_id = ?"
		)->execute(array($real_name, $this->user_id));
	}

	public function getEmail() {
		return $this->email;
	}

	public function setEmail($email) {
		$this->email = $email;
		WT_DB::prepare(
			"UPDATE `##user` SET email = ? WHERE user_id = ?"
		)->execute(array($email, $this->user_id));
	}

	public function setPassword($password) {
		self::passwordCompatibility();

		WT_DB::prepare(
			"UPDATE `##user` SET password = ? WHERE user_id = ?"
		)->execute(array(password_hash($password, PASSWORD_DEFAULT), $this->user_id));
	}

	// Fetch a user option/setting from the wt_user_setting table
	//
	// Since we'll fetch several settings for each user, and since there aren’t
	// that many of them, fetch them all in one database query
	public function getSetting($setting_name, $default=null) {
		if ($this->settings === null) {
			$this->settings = WT_DB::prepare(
				"SELECT SQL_CACHE setting_name, setting_value FROM `##user_setting` WHERE user_id = ?"
			)->execute(array($this->user_id))->fetchAssoc();
		}

		if (array_key_exists($setting_name, $this->settings)) {
			return $this->settings[$setting_name];
		} else {
			return $default;
		}
	}

	// Update a setting for the user.
	public function setSetting($setting_name, $setting_value) {
		if ($setting_value===null) {
			WT_DB::prepare("DELETE FROM `##user_setting` WHERE user_id=? AND setting_name=?")
				->execute(array($this->user_id, $setting_name));
			unset($this->settings[$setting_name]);
		} else {
			WT_DB::prepare("REPLACE INTO `##user_setting` (user_id, setting_name, setting_value) VALUES (?, ?, LEFT(?, 255))")
				->execute(array($this->user_id, $setting_name, $setting_value));
			$this->settings[$setting_name] = $setting_value;
		}
	}

	// PHP5.5 provides new password hash functions.
	// For earlier versions, use a compatibility library.
	private static function passwordCompatibility() {
		if (!function_exists('password_hash')) {
			// The compatibility library requires the $2$y salt prefix, which is available in
			// PHP5.3.7 and *some* earlier/patched versions.
			$hash = '$2y$04$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
			if (crypt("password", $hash) === $hash) {
				require 'library/ircmaxell/password-compat/lib/password.php';
			} else {
				// For older/unpatched versions of PHP, use the default crypt behaviour.
				function password_hash($password, $algo, $options=array()) {
					$salt = '$2a$12$';
					$salt_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789./';
					for ($i = 0; $i < 22; ++$i) {
						$salt .= setsubstr($salt_chars, mt_rand(0, 63), 1);
					}
					return crypt($password, $salt);
				}
				function password_needs_rehash($password, $algo, $options=array()) {
					return false;
				}
				function password_verify($password, $hash) {
					return crypt($password, $hash) === $hash;
				}
				define('PASSWORD_DEFAULT', 1);
			}
		}
	}

	// Authenticate a username/password combination.
	//
	// On success, return the user_id of the account.
	// On failure, return the reason for failure.
	public static function authenticate($user_name, $password) {
		self::passwordCompatibility();

		// If no cookies are available, then we cannot log in.
		if (empty($_COOKIE)) {
			return self::NO_SESSION_COOKIES;
		}

		$row = WT_DB::prepare(
			"SELECT SQL_CACHE user_id, password FROM `wt_user` WHERE user_name = ? OR email = ?"
		)->execute(array($user_name, $user_name))->fetchOneRow();

		if (!$row) {
			// No such user with that username or email address
			return self::NO_SUCH_USER;
		}

		$user = new WT_User($row->user_id);
		if (!password_verify($password, $row->password)) {
			// Incorrect password
			return self::INCORRECT_PASSWORD;
		}

		// Was the password hash created using an old or insecure algorithm?
		if (password_needs_rehash($row->password, PASSWORD_DEFAULT)) {
			// Generate a new hash
			$user->setPassword(password_hash($password, PASSWORD_DEFAULT));
		}

		if (!$user->getSetting('verified') && !$user->getSetting('canadmin')) {
			// The user has not verified their email address.
			return self::ACCOUNT_NOT_VERIFIED;
		}

		if (!$user->getSetting('verified_by_admin') && !$user->getSetting('canadmin')) {
			// An administrator has not approved the account.
			// Admins do not need to be approved.
			return self::ACCOUNT_NOT_APPROVED;
		}

		// All checks passed.  The user is permitted to log in.
		return $row->user_id;
	}
}
