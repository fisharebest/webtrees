<?php
/**
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
 * @package webtrees
 * @subpackage DataModel
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_CLASS_LOCALCLIENT_PHP', '');

require_once WT_ROOT.'includes/classes/class_serviceclient.php';

class LocalClient extends ServiceClient {
	/**
	 * constructor
	 * @param string $gedrec	the gedcom record
	 */
	function __construct($gedrec) {
		parent::__construct($gedrec);
		$this->type = "local";
	}

	/**
	 * authenticate the client
	 */
	function authenticate() {
		//-- nothing to do in a local client
	}

	/**
	 * Get a record from the remote site
	 * @param string $remoteid	the id of the record to get
	 */
	function getRemoteRecord($remoteid) {
		$rec = find_gedcom_record($remoteid, WT_GED_ID);
		$rec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $rec);
		return $rec;
	}

	/**
	 * merge a local gedcom record with the information from the remote site
	 * @param string $xref		the remote ID to merge with
	 * @param string $localrec	the local gedcom record to merge the remote record with
	 * @param boolean $isStub	whether or not this is a stub record
	 * @param boolean $firstLink	is this the first time this record is being linked
	 */
	function mergeGedcomRecord($xref, $localrec, $isStub=false, $firstLink=false) {
		//-- get the record from the database
		$gedrec = find_gedcom_record($xref, WT_GED_ID);
		$gedrec = preg_replace("/@(.*)@/", "@".$this->xref.":$1@", $gedrec);
		$gedrec = $this->checkIds($gedrec);
		if (empty($localrec)) return $gedrec;
		$localrec = $this->_merge($localrec, $gedrec);

		//-- used to force an update on the first time linking a person
		if ($firstLink) {
			require_once WT_ROOT.'includes/functions/functions_edit.php';
			$ct=preg_match("/0 @(.*)@/", $localrec, $match);
			if ($ct>0)
			{
				$pid = trim($match[1]);
				$localrec = $this->UpdateFamily($localrec,$gedrec);
				//-- restore the correct id since it may have been changed by the UpdateFamily method
				$localrec = preg_replace("/0 @(.*)@/", "0 @$pid@", $localrec);
				replace_gedrec($pid, WT_GED_ID, $localrec);
			}
		}
		return $localrec;
	}

	/**
	 * get a singleton instance of the results
	 * returned by the soapClient search method
	 *
	 * @param string $query - the query to search on
	 * @param integer $start - the start index of the results to return
	 * @param integer $max - the maximum number of results to return
	 */
	function &search($query, $start=0, $max=100) {
		//$this->authenticate();
		//$result = $this->soapClient->search($this->SID, $query, $start, $max);
		$search_results = search_indis(array($query), array($this->gedfile), 'AND', true);

		// loop thru the returned result of the method call
		foreach($search_results as $gid=>$indi)
		{
			// privatize the gedcoms returned
			$gedrec = privatize_gedcom($indi["gedcom"]);
			// set the fields that exist and return all the results that are not private
			if(preg_match("~".$query."~i",$gedrec)>0)
			{
				$person = new SOAP_Value('person', 'person', "");
				$person->PID = $gid;
				$person->gedcomName = get_gedcom_value("NAME", 1, $gedrec, '', false);
				$person->birthDate = get_gedcom_value("BIRT:DATE", 1, $gedrec, '', false);
				$person->birthPlace = get_gedcom_value("BIRT:PLAC", 1, $gedrec, '', false);
				$person->deathDate = get_gedcom_value("DEAT:DATE", 1, $gedrec, '', false);
				$person->deathPlace = get_gedcom_value("DEAT:PLAC", 1, $gedrec, '', false);
				$person->gender = get_gedcom_value("SEX", 1, $gedrec, '', false);
				//$search_result_element['gedcom'] = $gedrec;
				$results_array[] = $person;
			}
		}
		// set the number of possible results
		//$results[0]['totalResults'] = count($results_array);
		$results_array = array_slice($results_array,$start,$max);
		//$results[0]['persons'] = $results_array;
		$return = new SOAP_Value('searchResult', 'searchResult', "");
		$return->totalResults = count($results_array);
		$return->persons = $results_array;
		return $return;
	}
}
?>
