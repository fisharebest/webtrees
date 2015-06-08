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

/**
 * Authentication.
 */
class Auth {
	// Privacy constants
	const PRIV_PRIVATE = 2; // Allows visitors to view the item
	const PRIV_USER    = 1; // Allows members to access the item
	const PRIV_NONE    = 0; // Allows managers to access the item
	const PRIV_HIDE    = -1; // Hide the item to all users

	/**
	 * Are we currently logged in?
	 *
	 * @return bool
	 */
	public static function check() {
		return self::id() !== null;
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

		return $user && $user->getPreference('canadmin') === '1';
	}

	/**
	 * Is the specified/current user a manager of a tree?
	 *
	 * @param Tree      $tree
	 * @param User|null $user
	 *
	 * @return bool
	 */
	public static function isManager(Tree $tree, User $user = null) {
		if ($user === null) {
			$user = self::user();
		}

		return self::isAdmin($user) || $user && $tree->getUserPreference($user, 'canedit') === 'admin';
	}

	/**
	 * Is the specified/current user a moderator of a tree?
	 *
	 * @param Tree      $tree
	 * @param User|null $user
	 *
	 * @return bool
	 */
	public static function isModerator(Tree $tree, User $user = null) {
		if ($user === null) {
			$user = self::user();
		}

		return self::isManager($tree, $user) || $user && $tree->getUserPreference($user, 'canedit') === 'accept';
	}

	/**
	 * Is the specified/current user an editor of a tree?
	 *
	 * @param Tree      $tree
	 * @param User|null $user
	 *
	 * @return bool
	 */
	public static function isEditor(Tree $tree, User $user = null) {
		if ($user === null) {
			$user = self::user();
		}

		return self::isModerator($tree, $user) || $user && $tree->getUserPreference($user, 'canedit') === 'edit';
	}

	/**
	 * Is the specified/current user a member of a tree?
	 *
	 * @param Tree      $tree
	 * @param User|null $user
	 *
	 * @return bool
	 */
	public static function isMember(Tree $tree, User $user = null) {
		if ($user === null) {
			$user = self::user();
		}

		return self::isEditor($tree, $user) || $user && $tree->getUserPreference($user, 'canedit') === 'access';
	}

	/**
	 * What is the specified/current user's access level within a tree?
	 *
	 * @param Tree      $tree
	 * @param User|null $user
	 *
	 * @return int
	 */
	public static function accessLevel(Tree $tree, User $user = null) {
		if ($user === null) {
			$user = self::user();
		}

		if (self::isManager($tree, $user)) {
			return self::PRIV_NONE;
		} elseif (self::isMember($tree, $user)) {
			return self::PRIV_USER;
		} else {
			return self::PRIV_PRIVATE;
		}
	}

	/**
	 * Is the current visitor a search engine?  The global is set in session.php
	 *
	 * @return bool
	 */
	public static function isSearchEngine() {
		global $SEARCH_SPIDER;

		return $SEARCH_SPIDER;
	}

	/**
	 * The ID of the authenticated user, from the current session.
	 *
	 * @return string|null
	 */
	public static function id() {
		return Session::get('wt_user');
	}

	/**
	 * The authenticated user, from the current session.
	 *
	 * @return User
	 */
	public static function user() {
		$user = User::find(self::id());
		if ($user === null) {
			$visitor            = new \stdClass;
			$visitor->user_id   = '';
			$visitor->user_name = '';
			$visitor->real_name = '';
			$visitor->email     = '';

			return new User($visitor);
		} else {
			return $user;
		}
	}

	/**
	 * Login directly as an explicit user - for masquerading.
	 *
	 * @param User $user
	 */
	public static function login(User $user) {
		Session::put('wt_user', $user->getUserId());
		Session::regenerate(false);
	}

	/**
	 * End the session for the current user.
	 */
	public static function logout() {
		Session::regenerate(true);
	}
}
