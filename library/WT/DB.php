<?php
//
// Class file for the database access.  Extend PHP's native PDO and
// PDOStatement classes to provide database access with logging, etc.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (c) 2009-2010 Greg Roach
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

class WT_DB {
	//////////////////////////////////////////////////////////////////////////////
	// CONSTRUCTION
	// Implement a singleton to decorate a PDO object.
	// See http://en.wikipedia.org/wiki/Singleton_pattern
	// See http://en.wikipedia.org/wiki/Decorator_pattern
	//////////////////////////////////////////////////////////////////////////////
	private static $instance=null;
	private static $pdo=null;

	// Prevent instantiation via new WT_DB
	private final function __construct() {
	}

	// Prevent instantiation via clone()
	public final function __clone() {
		trigger_error('WT_DB::clone() is not allowed.', E_USER_ERROR);
	}

	// Prevent instantiation via serialize()
	public final function __wakeup() {
		trigger_error('WT_DB::unserialize() is not allowed.', E_USER_ERROR);
	}

	// Disconnect from the server, so we can connect to another one
	public static function disconnect() {
		self::$pdo=null;
	}

	// Implement the singleton pattern
	public static function createInstance($DBHOST, $DBPORT, $DBNAME, $DBUSER, $DBPASS) {
		if (self::$pdo instanceof PDO) {
			trigger_error('WT_DB::createInstance() can only be called once.', E_USER_ERROR);
		}
		// Create the underlying PDO object
		self::$pdo=new PDO(
			(substr($DBHOST, 0, 1)=='/' ?
				"mysql:unix_socket={$DBHOST};dbname={$DBNAME}" :
				"mysql:host={$DBHOST};dbname={$DBNAME};port={$DBPORT}"
			),
			$DBUSER, $DBPASS,
			array(
				PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ,
				PDO::ATTR_CASE=>PDO::CASE_LOWER,
				PDO::ATTR_AUTOCOMMIT=>true
			)
		);
		self::$pdo->exec("SET NAMES UTF8");

		// Assign the singleton
		self::$instance=new self;
	}

	// We don't access this directly, only via query(), exec() and prepare()
	public static function getInstance() {
		if (self::$pdo instanceof PDO) {
			return self::$instance;
		} else {
			trigger_error('WT_DB::createInstance() must be called before WT_DB::getInstance().', E_USER_ERROR);
		}
	}

	public static function isConnected() {
		return (self::$pdo instanceof PDO);
	}

	//////////////////////////////////////////////////////////////////////////////
	// LOGGING
	// Keep a log of the statements executed using this connection
	//////////////////////////////////////////////////////////////////////////////
	private static $log=array();

	// Add an entry to the log
	public static function logQuery($query, $rows, $microtime, $bind_variables) {
		if (WT_DEBUG_SQL) {
			// Full logging
			// Trace
			$trace=debug_backtrace();
			array_shift($trace);
			array_shift($trace);
			foreach ($trace as $n=>$frame) {
				if (isset($frame['file']) && isset($frame['line'])) {
					$trace[$n]=basename($frame['file']).':'.$frame['line'].' '.$frame['function'].'('./*implode(',', $frame['args']).*/')';
				} else {
					unset($trace[$n]);
				}
			}
			$stack='<abbr title="'.WT_Filter::escapeHtml(implode(" / ", $trace)).'">'.(count(self::$log)+1).'</abbr>';
			// Bind variables
			$query2='';
			foreach ($bind_variables as $key=>$value) {
				if (is_null($value)) {
					$bind_variables[$key]='[NULL]';
				}
			}
			foreach (str_split(WT_Filter::escapeHtml($query)) as $char) {
				if ($char=='?') {
					$query2.='<abbr title="'.WT_Filter::escapeHtml(array_shift($bind_variables)).'">'.$char.'</abbr>';
				} else {
					$query2.=$char;
				}
			}
			// Highlight embedded literal strings.
			if (preg_match('/[\'"]/', $query)) {
				$query2='<span style="background-color:yellow;">'.$query2.'</span>';
		}
			// Highlight slow queries
			$microtime*=1000; // convert to milliseconds
			if ($microtime>1000) {
				$microtime=sprintf('<span style="background-color:red">%.3f</span>', $microtime);
			} elseif ($microtime>100) {
				$microtime=sprintf('<span style="background-color:orange">%.3f</span>', $microtime);
			} elseif ($microtime>1) {
				$microtime=sprintf('<span style="background-color:yellow">%.3f</span>', $microtime);
			} else {
			$microtime=sprintf('%.3f', $microtime);
			}
			self::$log[]="<tr><td>{$stack}</td><td>{$query2}</td><td>{$rows}</td><td>{$microtime}</td></tr>";
		} else {
			// Just log query count for statistics
			self::$log[]=true;
		}
	}

	// Total number of queries executed, for the page statistics
	public static function getQueryCount() {
		return count(self::$log);
	}

	// Display the query log as a table, for debugging
	public static function getQueryLog() {
		$html='<table border="1"><col span="3"><col align="char"><thead><tr><th>#</th><th>Query</th><th>Rows</th><th>Time (ms)</th></tr></thead><tbody>'.implode('', self::$log).'</tbody></table>';
		self::$log=array();
		return $html;
	}

	//////////////////////////////////////////////////////////////////////////////
	// FUNCTIONALITY ENHANCEMENTS
	//////////////////////////////////////////////////////////////////////////////

	// The native quote() function does not convert PHP nulls to DB nulls
	public static function quote($string, $parameter_type=PDO::PARAM_STR) {
		if (is_null($string)) {
			return 'NULL';
		} else {
			return self::$pdo->quote($string, $parameter_type);
		}
	}

	// Add logging to query()
	public static function query($statement, $parameter_type= PDO::PARAM_STR) {
		$statement=str_replace('##', WT_TBLPREFIX, $statement);
		$start=microtime(true);
		$result=self::$pdo->query($statement, $parameter_type);
		$end=microtime(true);
		self::logQuery($statement, count($result), $end-$start, array());
		return $result;
	}

	// Add logging to exec()
	public static function exec($statement) {
		$statement=str_replace('##', WT_TBLPREFIX, $statement);
		$start=microtime(true);
		$result=self::$pdo->exec($statement);
		$end=microtime(true);
		self::logQuery($statement, $result, $end-$start, array());
		return $result;
	}

	// Add logging/functionality to prepare()
	public static function prepare($statement) {
		if (!self::$pdo instanceof PDO) {
			throw new PDOException("No Connection Established");
		}
		$statement=str_replace('##', WT_TBLPREFIX, $statement);
		return new WT_DBStatement(self::$pdo->prepare($statement));
	}

	// Map all other functions onto the base PDO object
	public function __call($function, $params) {
		return call_user_func_array(array(self::$pdo, $function), $params);
	}

	//////////////////////////////////////////////////////////////////////////////
	// Create/update tables, indexes, etc.
	//////////////////////////////////////////////////////////////////////////////
	public static function updateSchema($schema_dir, $schema_name, $target_version) {
		try {
			$current_version=(int)WT_Site::preference($schema_name);
		} catch (PDOException $e) {
			// During initial installation, this table won’t exist.
			// It will only be a problem if we can’t subsequently create it.
			$current_version=0;
		}

		// The update scripts can set these to indicate that we need to run a
		// "post update" script.  It saves from having to store/maintain lots
		// of separate versions at each schema version.
		$need_to_delete_old_files=false;
		$need_to_update_config_data=false;
		$need_to_update_stored_procedures=false;

		// During installation, the current version is set to a special value of
		// -1 (v1.2.5 to v1.2.7) or -2 (v1.3.0 onwards).  This indicates that the tables have
		// been created, but that we still need to install/update configuration data
		// and/or stored procedures.
		switch ($current_version) {
		case -1:
			// Due to a bug in webtrees 1.2.5 - 1.2.7, the setup value of "-1"
			// wasn't being updated.
			$current_version=12;
			WT_Site::preference($schema_name, $current_version);
			break;
		case -2:
			// Because of the above bug, we now set the version to -2 during setup.
			$current_version=$target_version;
			WT_Site::preference($schema_name, $current_version);
			break;
		}

		// Update the schema, one version at a time.
		while ($current_version<$target_version) {
			$next_version=$current_version+1;
			require $schema_dir.'db_schema_'.$current_version.'_'.$next_version.'.php';
			// The updatescript should update the version or throw an exception
			$current_version=(int)WT_Site::preference($schema_name);
			if ($current_version!=$next_version) {
				die("Internal error while updating {$schema_name} to {$next_version}");
			}
		}

		if ($need_to_delete_old_files) {
			require $schema_dir.'delete_old_files.php';
		}
		if ($need_to_update_config_data) {
			require $schema_dir.'config_data.php';
		}
		if ($need_to_update_stored_procedures) {
			require $schema_dir.'stored_procedures.php';
		}
	}
}
