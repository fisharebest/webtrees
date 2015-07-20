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

use Fisharebest\Webtrees\Schema\MigrationInterface;
use PDO;
use PDOException;

/**
 * Extend PHP's native PDO class.
 */
class Database {
	/** @var Database Implement the singleton pattern */
	private static $instance;

	/** @var PDO Native PHP database driver */
	private static $pdo;

	/** @var array Keep a log of all the SQL statements that we execute */
	private static $log;

	/** @var Statement[] Cache of prepared statements */
	private static $prepared = array();

	/**
	 * Prevent instantiation via new Database
	 */
	private function __construct() {
		self::$log = array();
	}

	/**
	 * Begin a transaction.
	 *
	 * @return bool
	 */
	public static function beginTransaction() {
		return self::$pdo->beginTransaction();
	}

	/**
	 * Commit this transaction.
	 *
	 * @return bool
	 */
	public static function commit() {
		return self::$pdo->commit();
	}

	/**
	 * Disconnect from the server, so we can connect to another one
	 */
	public static function disconnect() {
		self::$pdo = null;
	}

	/**
	 * Implement the singleton pattern, using a static accessor.
	 *
	 * @param string $DBHOST
	 * @param string $DBPORT
	 * @param string $DBNAME
	 * @param string $DBUSER
	 * @param string $DBPASS
	 *
	 * @throws \Exception
	 */
	public static function createInstance($DBHOST, $DBPORT, $DBNAME, $DBUSER, $DBPASS) {
		if (self::$pdo instanceof PDO) {
			throw new \Exception('Database::createInstance() can only be called once.');
		}
		// Create the underlying PDO object
		self::$pdo = new PDO(
			(substr($DBHOST, 0, 1) === '/' ?
				"mysql:unix_socket={$DBHOST};dbname={$DBNAME}" :
				"mysql:host={$DBHOST};dbname={$DBNAME};port={$DBPORT}"
			),
			$DBUSER, $DBPASS,
			array(
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_CASE               => PDO::CASE_LOWER,
				PDO::ATTR_AUTOCOMMIT         => true,
			)
		);
		self::$pdo->exec("SET NAMES UTF8");

		self::$instance = new self;
	}

	/**
	 * We don't access $instance directly, only via query(), exec() and prepare()
	 *
	 * @throws \Exception
	 *
	 * @return Database
	 */
	public static function getInstance() {
		if (self::$pdo instanceof PDO) {
			return self::$instance;
		} else {
			throw new \Exception('createInstance() must be called before getInstance().');
		}
	}

	/**
	 * Are we currently connected to a database?
	 *
	 * @return bool
	 */
	public static function isConnected() {
		return self::$pdo instanceof PDO;
	}

	/**
	 * Log the details of a query, for debugging and analysis.
	 *
	 * @param string   $query
	 * @param int      $rows
	 * @param float    $microtime
	 * @param string[] $bind_variables
	 */
	public static function logQuery($query, $rows, $microtime, $bind_variables) {
		if (WT_DEBUG_SQL) {
			// Full logging
			// Trace
			$trace = debug_backtrace();
			array_shift($trace);
			array_shift($trace);
			foreach ($trace as $n => $frame) {
				if (isset($frame['file']) && isset($frame['line'])) {
					$trace[$n] = basename($frame['file']) . ':' . $frame['line'] . ' ' . $frame['function'];
				} else {
					unset($trace[$n]);
				}
			}
			$stack = '<abbr title="' . Filter::escapeHtml(implode(" / ", $trace)) . '">' . (count(self::$log) + 1) . '</abbr>';
			// Bind variables
			foreach ($bind_variables as $key => $value) {
				if (is_null($value)) {
					$value = 'NULL';
				} elseif (!is_integer($value)) {
					$value = '\'' . $value . '\'';
				}
				if (is_integer($key)) {
					$query = preg_replace('/\?/', $value, $query, 1);
				} else {
					$query = str_replace(':' . $key, $value, $query);
				}
			}
			// Highlight slow queries
			$microtime *= 1000; // convert to milliseconds
			if ($microtime > 1000) {
				$microtime = sprintf('<span style="background-color: #ff0000;">%.3f</span>', $microtime);
			} elseif ($microtime > 100) {
				$microtime = sprintf('<span style="background-color: #ffa500;">%.3f</span>', $microtime);
			} elseif ($microtime > 1) {
				$microtime = sprintf('<span style="background-color: #ffff00;">%.3f</span>', $microtime);
			} else {
				$microtime = sprintf('%.3f', $microtime);
			}
			self::$log[] = "<tr><td>{$stack}</td><td>{$query}</td><td>{$rows}</td><td>{$microtime}</td></tr>";
		} else {
			// Just log query count for statistics
			self::$log[] = true;
		}
	}

	/**
	 * Determine the number of queries executed, for the page statistics.
	 *
	 * @return int
	 */
	public static function getQueryCount() {
		return count(self::$log);
	}

	/**
	 * Convert the query log into an HTML table.
	 *
	 * @return string
	 */
	public static function getQueryLog() {
		$html      = '<table border="1" style="table-layout: fixed; width: 960px;word-wrap: break-word;"><col span="3"><col align="char"><thead><tr><th>#</th><th style="width: 800px;">Query</th><th>Rows</th><th>Time (ms)</th></tr></thead><tbody>' . implode('', self::$log) . '</tbody></table>';
		self::$log = array();

		return $html;
	}

	/**
	 * Determine the most recently created value of an AUTO_INCREMENT field.
	 *
	 * @return string
	 */
	public static function lastInsertId() {
		return self::$pdo->lastInsertId();
	}

	/**
	 * Quote a string for embedding in a MySQL statement.
	 *
	 * The native quote() function does not convert PHP nulls to DB nulls
	 *
	 * @param  $string
	 *
	 * @return string
	 *
	 * @deprecated We should use bind-variables instead.
	 */
	public static function quote($string) {
		if (is_null($string)) {
			return 'NULL';
		} else {
			return self::$pdo->quote($string, PDO::PARAM_STR);
		}
	}

	/**
	 * Execute an SQL statement, and log the result.
	 *
	 * @param string $sql The SQL statement to execute
	 *
	 * @return int The number of rows affected by this SQL query
	 */
	public static function exec($sql) {
		$sql   = str_replace('##', WT_TBLPREFIX, $sql);
		$start = microtime(true);
		$rows  = self::$pdo->exec($sql);
		$end   = microtime(true);
		self::logQuery($sql, $rows, $end - $start, array());

		return $rows;
	}

	/**
	 * Prepare an SQL statement for execution.
	 *
	 * @param $sql
	 *
	 * @throws \Exception
	 *
	 * @return Statement
	 */
	public static function prepare($sql) {
		if (!self::$pdo instanceof PDO) {
			throw new \Exception("No Connection Established");
		}
		$sql = str_replace('##', WT_TBLPREFIX, $sql);

		$hash = md5($sql);
		if (!array_key_exists($hash, self::$prepared)) {
			self::$prepared[$hash] = new Statement(self::$pdo->prepare($sql));
		}

		return self::$prepared[$hash];
	}

	/**
	 * Roll back this transaction.
	 *
	 * @return bool
	 */
	public static function rollBack() {
		return self::$pdo->rollBack();
	}

	/**
	 * Run a series of scripts to bring the database schema up to date.
	 *
	 * @param string $namespace      Where to find our MigrationXXX classes
	 * @param string $schema_name    Where to find our MigrationXXX classes
	 * @param int    $target_version updade/downgrade to this version
	 *
	 * @throws PDOException
	 */
	public static function updateSchema($namespace, $schema_name, $target_version) {
		try {
			$current_version = (int) Site::getPreference($schema_name);
		} catch (PDOException $e) {
			// During initial installation, the site_preference table won’t exist.
			$current_version = 0;
		}

		try {
			// Update the schema, one version at a time.
			while ($current_version < $target_version) {
				$class = $namespace . '\\Migration' . $current_version;
				/** @var MigrationInterface $migration */
				$migration = new $class;
				$migration->upgrade();
				Site::setPreference($schema_name, ++$current_version);
			}
		} catch (PDOException $ex) {
			// The schema update scripts should never fail.  If they do, there is no clean recovery.
			FlashMessages::addMessage($ex->getMessage(), 'danger');
			header('Location: ' . WT_BASE_URL . 'site-unavailable.php');
			throw $ex;
		}
	}
}
