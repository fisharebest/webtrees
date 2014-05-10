<?php namespace WT;

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

use Exception;
use WT_Tree;
use Zend_Session;

/**
 * Class Auth - authentication functions
 */
class Auth {
	// Reasons why a user’s authentication attempt failed
	const ACCOUNT_NOT_APPROVED = 'ACCOUNT_NOT_APPROVED';
	const ACCOUNT_NOT_VERIFIED = 'ACCOUNT_NOT_VERIFIED';
	const INCORRECT_PASSWORD   = 'INCORRECT_PASSWORD';
	const NO_SESSION_COOKIES   = 'NO_SESSION_COOKIES';
	const NO_SUCH_USER         = 'NO_SUCH_USER';

	/**
	 * Attempt to authenticate a user’s credentials, and log them in if successful.
	 *
	 * @param array $credentials Array with keys "identifier" and "password"
	 *
	 * @throws Exception
	 */
	public static function attempt(array $credentials) {
		global $WT_SESSION;

		// The login form creates a cookie.  If it is not present in the form
		// submission, then the browser does not support cookies.
		if (empty($_COOKIE)) {
			throw new Exception(self::NO_SESSION_COOKIES);
		}

		$user = User::findByIdentifier($credentials['identifier']);

		if (!$user) {
			throw new Exception(self::NO_SUCH_USER);
		}

		if (!$user->checkPassword($credentials['password'])) {
			throw new Exception(self::INCORRECT_PASSWORD);
		}

		if (!$user->getSetting('verified') && !$user->getSetting('canadmin')) {
			throw new Exception(self::ACCOUNT_NOT_VERIFIED);
		}

		if (!$user->getSetting('verified_by_admin') && !$user->getSetting('canadmin')) {
			throw new Exception(self::ACCOUNT_NOT_APPROVED);
		}

		$WT_SESSION->wt_user = $user->getUserId();
		Zend_Session::regenerateId();
	}

	/**
	 * Are we currently logged in?
	 *
	 * @return bool
	 */
	public static function check() {
		global $WT_SESSION;

		return $WT_SESSION->wt_user !== null;
	}

	/**
	 * Is the specified/current user an administrator?
	 *
	 * @param User|null $user
	 *
	 * @return bool
	 */
	public static function isAdmin(User $user = null) {
		if ($user === null) {
			$user = self::user();
		}

		return $user && $user->getSetting('canadmin') === '1';
	}

	/**
	 * Is a user a manager of a tree?
	 *
	 * @param WT_Tree|null $tree
	 * @param User|null    $user
	 *
	 * @return bool
	 */
	public static function isManager(WT_Tree $tree = null, User $user = null) {
		global $WT_TREE;

		if ($tree === null) {
			$tree = $WT_TREE;
		}

		if ($user === null) {
			$user = self::user();
		}

		return self::isAdmin($user) || $user && $tree->userPreference($user->getUserId(), 'canedit') === 'admin';
	}

	/**
	 * Is a user a moderator of a tree?
	 *
	 * @param WT_Tree|null $tree
	 * @param User|null    $user
	 *
	 * @return bool
	 */
	public static function isModerator(WT_Tree $tree = null, User $user = null) {
		global $WT_TREE;

		if ($tree === null) {
			$tree = $WT_TREE;
		}

		if ($user === null) {
			$user = self::user();
		}

		return self::isManager($tree, $user) || $user && $tree->userPreference($user->getUserId(), 'canedit') === 'accept';
	}

	/**
	 * Is a user an editor of a tree?
	 *
	 * @param WT_Tree|null $tree
	 * @param User|null    $user
	 *
	 *
	 * @return bool
	 */
	public static function isEditor(WT_Tree $tree = null, User $user = null) {
		global $WT_TREE;

		if ($tree === null) {
			$tree = $WT_TREE;
		}

		if ($user === null) {
			$user = self::user();
		}

		return self::isModerator($tree, $user) || $user && $tree->userPreference($user->getUserId(), 'canedit') === 'edit';
	}

	/**
	 * Is a user a member of a tree?
	 *
	 * @param WT_Tree|null $tree
	 * @param User|null    $user
	 *
	 * @return bool
	 */
	public static function isMember(WT_Tree $tree = null, User $user=null) {
		global $WT_TREE;

		if ($tree === null) {
			$tree = $WT_TREE;
		}

		if ($user === null) {
			$user = self::user();
		}

		return self::isEditor($tree, $user) || $user && $tree->userPreference($user->getUserId(), 'canedit') === 'access';
	}

	/**
	 * The ID of the authenticated user, from the current session.
	 *
	 * @return int|null
	 */
	public static function id() {
		global $WT_SESSION;

		return $WT_SESSION->wt_user;
	}

	/**
	 * The authenticated user, from the current session.
	 *
	 * @return User|null
	 */
	public static function user() {
		global $WT_SESSION;

		return User::find($WT_SESSION->wt_user);
	}

	/**
	 * Login directly as an explicit user - for masquerading.
	 *
	 * @param User $user
	 */
	public static function login(User $user) {
		global $WT_SESSION;

		$WT_SESSION->wt_user = $user->getUserId();
		Zend_Session::regenerateId();
	}

	/**
	 * End the session for the current user.
	 */
	public static function logout() {
		Zend_Session::regenerateId();
		Zend_Session::destroy();
	}
}