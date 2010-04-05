<?php
/**
 * Mutex Class
 *
 * This class provides a simple mutex lock
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author John Finlay
 * @package webtrees
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_MUTEX_PHP', '');

class Mutex {
	var $name; 	//-- the name of the mutex
	var $waitCount;	//-- the number of cycles it we waited while trying to acquire the mutex

	function checkDBCONN() {
		global $CONFIGURED;

		if (!WT_DB::isConnected()) {
			if ($CONFIGURED) {
				//-- fix bad configuration prevents edit config page from loading
				//die("Cannot use mutex without database");
				$CONFIGURED = false;
			}
			return false;
		}
		return true;
	}
	/**
	 * Create a mutex for enforcing mutual exclusion
	 *
	 * @param string $name	the system wide name of the mutex
	 * @param boolane $acquire	whether or not to acquire the mutex after it is created
	 * @return Mutex
	 */
	function __construct($name, $acquire=false) {
		global $TBLPREFIX;

		if (!Mutex::checkDBCONN()) return false;
		$this->name = $name;
		//-- check if this mutex already exists
		try {
			$one=
				WT_DB::prepare("SELECT 1 FROM {$TBLPREFIX}mutex WHERE mx_name=?")
				->execute(array($name))
				->fetchOne();
		} catch (PDOException $ex) {
			return false;
		}
		//-- mutex doesn't exist so create it
		if ($one!=1) {
			WT_DB::prepare("INSERT INTO {$TBLPREFIX}mutex (mx_id, mx_name, mx_thread) VALUES (?, ?, ?)")
				->execute(array(get_next_id("mutex", "mx_id"), $this->name, 0));
		}
		$this->waitCount = 0;
		if ($acquire) $this->Wait();
	}

	/**
	 * Try to acquire the mutex.  Block the thread if it cannot obtain it.
	 *
	 * @param int $time		[optional] parameter to only wait for $time secs before giving up
	 * @return boolean		true if successful, false if failed
	 */
	function Wait($time=-1) {
		global $TBLPREFIX;

		if (!Mutex::checkDBCONN()) return false;
		//-- check if mutex is available
		$available = false;
		while(!$available) {
			//-- do not allow a thread to hold the mutex for more than 5 minutes (300 secs), should not be a problem with PHP
			//--- this will allow another thread to access the mutex if another thread that held it crashed
			//-- allow the same session to get the mutex more than once
			$one=
				WT_DB::prepare("SELECT 1 FROM {$TBLPREFIX}mutex WHERE mx_name=? AND (mx_thread=? OR mx_thread=? OR mx_time < ?)")
				->execute(array($this->name, '0', session_id(), time()-300))
				->fetchOne();
			if ($one==1) {
				$available = true;
			}
			//-- sleep for 1 second between checks
			else {
				if ($time==0) return false;
				else $time--;
				$this->waitCount++;
				sleep(1);
			}
		}

		WT_DB::prepare("UPDATE {$TBLPREFIX}mutex SET mx_time=?, mx_thread=? WHERE mx_name=?")
			->execute(array(time(), session_id(), $this->name));
		return true;
	}

	/**
	 * Release the mutex previously acquired
	 *
	 */
	function Release() {
		global $TBLPREFIX;

		if (!Mutex::checkDBCONN()) return false;

		WT_DB::prepare("UPDATE {$TBLPREFIX}mutex SET mx_time=?, mx_thread=? WHERE mx_name=? AND mx_thread=?")
			->execute(array(0, '0', $this->name, session_id()));
		$this->waitCount = 0;
	}

	/**
	 * Get the number of times thread waited to acquire mutex
	 *
	 * @return int
	 */
	function getWaitCount() {
		return $this->waitCount;
	}

	/**
	 * Get the name of this mutex
	 *
	 * @return string
	 */
	function getName() {
		return $this->name;
	}
}
?>
