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
 * Temporary class to migrate to Symfony-based sessions, which need PHP 5.4.
 */
class Session {
	/**
	 * Start a session
	 *
	 * @param array $config
	 */
	public static function start(array $config = array()) {
		$default_config = array(
			'use_cookies'     => 1,
			'name'            => 'WT_SESSION',
			'cookie_lifetime' => 0,
			'gc_maxlifetime'  => 7200,
			'gc_probability'  => 1,
			'gc_divisor'      => 100,
			'cookie_path'     => '',
			'cookie_httponly' => true,
		);
		session_register_shutdown();
		foreach ($config + $default_config as $key => $value) {
			ini_set('session.' . $key, $value);
		}
		session_start();
	}

	/**
	 * Read a value from the session
	 *
	 * @param string $name
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public static function get($name, $default = null) {
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		} else {
			return $default;
		}
	}

	/**
	 * Write a value to the session
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public static function put($name, $value) {
		$_SESSION[$name] = $value;
	}

	/**
	 * Remove a value from the session
	 *
	 * @param string $name
	 */
	public static function forget($name) {
		unset($_SESSION[$name]);
	}

	/**
	 * Does a session variable exist?
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function has($name) {
		return array_key_exists($name, $_SESSION);
	}

	/**
	 * Remove all stored data from the session.
	 */
	public static function clear() {
		$_SESSION = array();
	}

	/**
	 * After any change in authentication level, we should use a new session ID.
	 *
	 * @param bool $destroy
	 */
	public static function regenerate($destroy = false) {
		if ($destroy) {
			self::clear();
		}
		session_regenerate_id($destroy);
	}

	/**
	 * Set an explicit session ID.  Typically used for search robots.
	 *
	 * @param string $id
	 */
	public static function setId($id) {
		session_id($id);
	}
}
