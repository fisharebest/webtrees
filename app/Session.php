<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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

use Symfony\Component\HttpFoundation\Request;

/**
 * Session handling
 */
class Session {
	/**
	 * Start a session
	 *
	 * @param array $config
	 */
	public static function start(array $config = []) {
		$default_config = [
			'use_cookies'     => '1',
			'name'            => 'WT_SESSION',
			'cookie_lifetime' => '0',
			'gc_maxlifetime'  => '7200',
			'gc_probability'  => '1',
			'gc_divisor'      => '100',
			'cookie_path'     => '',
			'cookie_httponly' => '1',
		];
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
		return $_SESSION[$name] ?? $default;
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
		return isset($_SESSION[$name]);
	}

	/**
	 * Remove all stored data from the session.
	 */
	public static function clear() {
		$_SESSION = [];
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
	 * Set an explicit session ID. Typically used for search robots.
	 *
	 * @param string $id
	 */
	public static function setId($id) {
		session_id($id);
	}

	/**
	 * Initialise our session save handler
	 */
	public static function setSaveHandler() {
		session_set_save_handler(
			function (): bool {
				return Session::open();
			},
			function ():bool {
				return Session::close();
			},
			function (string $id): string {
				return Session::read($id);
			},
			function (string $id, string $data): bool {
				return Session::write($id, $data);
			},
			function (string $id): bool {
				return Session::destroy($id);
			},
			function (int $maxlifetime):bool {
				return Session::gc($maxlifetime);
			}
		);
	}

	/**
	 * For session_set_save_handler()
	 *
	 * @return bool
	 */
	private static function close() {
		return true;
	}

	/**
	 * For session_set_save_handler()
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	private static function destroy(string $id) {
		Database::prepare(
			"DELETE FROM `##session` WHERE session_id = :session_id"
		)->execute([
			'session_id' => $id
		]);

		return true;
	}

	/**
	 * For session_set_save_handler()
	 *
	 * @param int $maxlifetime
	 *
	 * @return bool
	 */
	private static function gc(int $maxlifetime) {
		Database::prepare(
			"DELETE FROM `##session` WHERE session_time < DATE_SUB(NOW(), INTERVAL :maxlifetime SECOND)"
		)->execute([
			'maxlifetime' => $maxlifetime
		]);

		return true;
	}

	/**
	 * For session_set_save_handler()
	 *
	 * @return bool
	 */
	private static function open() {
		return true;
	}

	/**
	 * For session_set_save_handler()
	 *
	 * @param string $id
	 *
	 * @return string
	 */
	private static function read(string $id): string {
		return (string) Database::prepare(
			"SELECT session_data FROM `##session` WHERE session_id = :session_id"
		)->execute([
			'session_id' => $id
		])->fetchOne();
	}

	/**
	 * For session_set_save_handler()
	 *
	 * @param string $id
	 * @param string $data
	 *
	 * @return bool
	 */
	private static function write(string $id, string $data): bool {
		$request = Request::createFromGlobals();

		// Only update the session table once per minute, unless the session data has actually changed.
		Database::prepare(
			"INSERT INTO `##session` (session_id, user_id, ip_address, session_data, session_time)" .
			" VALUES (:session_id, :user_id, :ip_address, :data, CURRENT_TIMESTAMP - SECOND(CURRENT_TIMESTAMP))" .
			" ON DUPLICATE KEY UPDATE" .
			" user_id      = VALUES(user_id)," .
			" ip_address   = VALUES(ip_address)," .
			" session_data = VALUES(session_data)," .
			" session_time = CURRENT_TIMESTAMP - SECOND(CURRENT_TIMESTAMP)"
		)->execute([
			'session_id' => $id,
			'user_id'    => (int) Auth::id(),
			'ip_address' => $request->getClientIp(),
			'data'       => $data,
		]);

		return true;
	}
}
