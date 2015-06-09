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
 * Record webtrees events in the database
 */
class Log {
	// We can log the following types of message in the wt_log table.
	const TYPE_AUTHENTICATION = 'auth';
	const TYPE_CONFIGURATION  = 'config';
	const TYPE_DEBUG          = 'debug';
	const TYPE_EDIT           = 'edit';
	const TYPE_ERROR          = 'error';
	const TYPE_MEDIA          = 'media';
	const TYPE_SEARCH         = 'search';

	/**
	 * Store a new message (of the appropriate type) in the message log.
	 *
	 * @param string    $message
	 * @param string    $log_type
	 * @param Tree|null $tree
	 */
	private static function addLog($message, $log_type, Tree $tree = null) {
		global $WT_TREE;

		if (!$tree) {
			$tree = $WT_TREE;
		}

		Database::prepare(
			"INSERT INTO `##log` (log_type, log_message, ip_address, user_id, gedcom_id) VALUES (?, ?, ?, ?, ?)"
		)->execute(array(
					$log_type,
					$message,
					WT_CLIENT_IP,
					Auth::id(),
					$tree ? $tree->getTreeId() : null,
		));
	}

	/**
	 * Store an authentication message in the message log.
	 *
	 * @param string $message
	 */
	public static function addAuthenticationLog($message) {
		self::addLog($message, self::TYPE_AUTHENTICATION);
	}

	/**
	 * Store a configuration message in the message log.
	 *
	 * @param string       $message
	 * @param Tree|null $tree
	 */
	public static function addConfigurationLog($message, Tree $tree = null) {
		self::addLog($message, self::TYPE_CONFIGURATION, $tree);
	}

	/**
	 * Store a debug message in the message log.
	 *
	 * @param string $message
	 */
	public static function addDebugLog($message) {
		self::addLog($message, self::TYPE_DEBUG);
	}

	/**
	 * Store an edit message in the message log.
	 *
	 * @param string $message
	 */
	public static function addEditLog($message) {
		self::addLog($message, self::TYPE_EDIT);
	}

	/**
	 * Store an error message in the message log.
	 *
	 * @param string $message
	 */
	public static function addErrorLog($message) {
		self::addLog($message, self::TYPE_ERROR);
	}

	/**
	 * Store an media management message in the message log.
	 *
	 * @param string $message
	 */
	public static function addMediaLog($message) {
		self::addLog($message, self::TYPE_MEDIA);
	}

	/**
	 * Store a search event in the message log.
	 *
	 * Unlike most webtrees activity, search is not restricted to a single tree,
	 * so we need to record which trees were searchecd.
	 *
	 * @param string    $message
	 * @param Tree[] $trees Which trees were searched
	 */
	public static function addSearchLog($message, array $trees) {
		foreach ($trees as $tree) {
			self::addLog($message, self::TYPE_SEARCH, $tree);
		}
	}
}
