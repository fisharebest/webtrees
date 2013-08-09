<?php
// Class file for the database access.  Extend PHP's native PDO and
// PDOStatement classes to provide database access with logging, etc.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

class WT_DBStatement {
	//////////////////////////////////////////////////////////////////////////////
	// CONSTRUCTION
	// Decorate a PDOStatement object.
	// See http://en.wikipedia.org/wiki/Decorator_pattern
	//////////////////////////////////////////////////////////////////////////////
	private $pdostatement=null;

	// Keep track of calls to execute(), so we can do it automatically
	private $executed=false;

	// Keep a copy of the bind variables, for logging
	private $bind_variables=array();

	// Our constructor just takes a copy of the object to be decorated
	public function __construct(PDOStatement $statement) {
		$this->pdostatement=$statement;
	}

	// Need this function to load BLOB values from streams
	public function bindParam($num, &$value, $type) {
		$this->pdostatement->bindParam($num, $value, $type);
		return $this;
	}

	//////////////////////////////////////////////////////////////////////////////
	// FLUENT INTERFACE
	// Add automatic calling of execute() and closeCursor()
	// See http://en.wikipedia.org/wiki/Fluent_interface
	//////////////////////////////////////////////////////////////////////////////
	public function __call($function, $params) {
		switch ($function) {
		case 'closeCursor':
			$this->executed=false;
			// no break;
		case 'bindColumn':
		case 'bindParam':
		case 'bindValue':
			// TODO: bind variables need to be stored in $this->bind_variables so we can log them
		case 'setAttribute':
		case 'setFetchMode':
			// Functions that return no values become fluent
			call_user_func_array(array($this->pdostatement, $function), $params);
			return $this;
		case 'execute':
			if ($this->executed) {
				trigger_error('WT_DBStatement::execute() called twice.', E_USER_ERROR);
			} else {
				if ($params) {
					$this->bind_variables=$params[0];
					foreach ($params[0] as &$param) {
						if ($param===false) {
							// For consistency, otherwise true=>'1' and false=>''
							$param=0;
						}
					}
				}
				$start=microtime(true);
				$result=call_user_func_array(array($this->pdostatement, $function), $params);
				$end=microtime(true);
				$this->executed=!preg_match('/^(insert|delete|update|create|alter) /i', $this->pdostatement->queryString);
				WT_DB::logQuery($this->pdostatement->queryString, $this->pdostatement->rowCount(), $end-$start, $this->bind_variables);
				return $this;
			}
		case 'fetch':
		case 'fetchColumn':
		case 'fetchObject':
		case 'fetchAll':
			// Automatically execute the query
			if (!$this->executed) {
				$this->execute();
				$this->executed=true;
			}
			// no break;
		default:
			return call_user_func_array(array($this->pdostatement, $function), $params);
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	// FUNCTIONALITY ENHANCEMENTS
	//////////////////////////////////////////////////////////////////////////////

	// Fetch one row, and close the cursor.  e.g. SELECT * FROM foo WHERE pk=bar
	public function fetchOneRow($fetch_style=PDO::FETCH_OBJ) {
		if (!$this->executed) {
			$this->execute();
		}
		$row=$this->pdostatement->fetch($fetch_style);
		$this->pdostatement->closeCursor();
		$this->executed=false;
		return $row ? $row : null;
	}

	// Fetch one value and close the cursor.  e.g. SELECT MAX(foo) FROM bar
	public function fetchOne() {
		if (!$this->executed) {
			$this->execute();
		}
		$value=$this->pdostatement->fetchColumn();
		$this->pdostatement->closeCursor();
		$this->executed=false;
		return $value===false ? null : $value;
	}

	// Fetch two columns, and return an associative array of col1=>col2
	public function fetchAssoc() {
		if (!$this->executed) {
			$this->execute();
		}
		$rows=array();
		while ($row=$this->pdostatement->fetch(PDO::FETCH_NUM)) {
			$rows[$row[0]]=$row[1];
		}
		$this->pdostatement->closeCursor();
		$this->executed=false;
		return $rows;
	}

	// Fetch all the first column, as an array
	public function fetchOneColumn() {
		if (!$this->executed) {
			$this->execute();
		}
		$list=array();
		while ($row=$this->pdostatement->fetch(PDO::FETCH_NUM)) {
			$list[]=$row[0];
		}
		$this->pdostatement->closeCursor();
		$this->executed=false;
		return $list;
	}
}
