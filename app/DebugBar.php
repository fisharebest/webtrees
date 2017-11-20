<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Closure;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use DebugBar\Storage\FileStorage;
use Fisharebest\Webtrees\DebugBar\ViewCollector;
use PDO;
use Throwable;

/**
 * A static wrapper for maximebf/php-debugbar.
 *
 * @see https://github.com/maximebf/php-debugbar
 */
class DebugBar {
	/** @var StandardDebugbar */
	private static $debugbar;

	/** @var JavascriptRenderer */
	private static $renderer;

	/**
	 * Prevent instantiation.
	 */
	private final function __construct() {
	}

	/**
	 * Initialize the Debugbar.
	 */
	public static function init(bool $enable = true) {
		if ($enable) {
			self::$debugbar = new StandardDebugBar;
			self::$debugbar->addCollector(new ViewCollector);

			self::$renderer = self::$debugbar->getJavascriptRenderer('./vendor/maximebf/debugbar/src/DebugBar/Resources/');

			// We can't use WT_DATA_DIR as it does not exist yet
			$storage_dir = 'data/debugbar';

			if (File::mkdir($storage_dir)) {
				$storage = new FileStorage($storage_dir);
				self::$debugbar->setStorage($storage);
			}
		}
	}

	/**
	 * Initialize the PDO collector.
	 *
	 * @param PDO $pdo
	 *
	 * @return PDO
	 */
	public static function initPDO(PDO $pdo): PDO {
		if (self::$debugbar !== null) {
			$pdo = new TraceablePDO($pdo);
			self::$debugbar->addCollector(new PDOCollector($pdo));
		}

		return $pdo;
	}

	/**
	 * Render the body content.
	 *
	 * @return string
	 */
	public static function render(): string {
		if (self::$debugbar !== null) {
			return self::$renderer->render();
		} else {
			return '';
		}
	}

	/**
	 * Render the head content.
	 *
	 * @return string
	 */
	public static function renderHead(): string {
		if (self::$debugbar !== null) {
			return self::$renderer->renderHead();
		} else {
			return '';
		}
	}

	/**
	 * For POST/redirect responses, we "stack" the data onto the next GET request.
	 */
	public static function stackData() {
		if (self::$debugbar !== null) {
			self::$debugbar->stackData();
		}
	}

	/**
	 * For JSON responses, we send the data in HTTP headers.
	 */
	public static function sendDataInHeaders() {
		if (self::$debugbar !== null) {
			self::$debugbar->sendDataInHeaders();
		}
	}

	/**
	 * Add a message.
	 *
	 * @param string $message
	 * @param string $label
	 * @param bool   $isString
	 *
	 * @return void
	 */
	public static function addMessage($message, $label = 'info', $isString = true) {
		if (self::$debugbar !== null) {
			self::$debugbar['messages']->addMessage($message, $label, $isString);
		}
	}

	/**
	 * Start a timer.
	 *
	 * @param      $name
	 * @param null $label
	 * @param null $collector
	 */
	public static function startMeasure($name, $label = null, $collector = null) {
		if (self::$debugbar !== null) {
			self::$debugbar['time']->startMeasure($name, $label, $collector);
		}
	}

	/**
	 * Stop a timer.
	 *
	 * @param       $name
	 * @param array $params
	 */
	public static function stopMeasure($name, $params = []) {
		if (self::$debugbar !== null) {
			self::$debugbar['time']->stopMeasure($name, $params);
		}
	}

	/**
	 * Time a closure.
	 *
	 * @param         $label
	 * @param Closure $closure
	 * @param null    $collector
	 */
	public static function measure($label, Closure $closure, $collector = null) {
		if (self::$debugbar !== null) {
			self::$debugbar['time']->measure($label, $closure, $collector);
		}
	}

	/**
	 * Log an exception/throwable
	 *
	 * @param Throwable $throwable
	 */
	public static function addThrowable(Throwable $throwable) {
		if (self::$debugbar !== null) {
			self::$debugbar['exceptions']->addThrowable($throwable);
		}
	}

	/**
	 * Log an exception/throwable
	 *
	 * @param Throwable $throwable
	 */
	public static function addView(string $view, array $data) {
		if (self::$debugbar !== null) {
			self::$debugbar['views']->addView($view, $data);
		}
	}
}
