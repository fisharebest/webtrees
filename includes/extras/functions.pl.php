<?php
/**
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_PL_PHP', '');

//-- functions to calculate polish specific genitive names
function getFirstRelationsName_pl($pid) {
	// In Polish we want the genitive form of the name
	$person=Person::getInstance($pid);
	if ($person) {
		$fname=$person->getFullName();
	}
	else {
		$fname='';
	}
	//return $fname;
	
	// tested
	$pname='';
	$sex = Person::getInstance($pid)->getSex();
	if ($sex == "M") {
		$names = explode(" ", $fname);
		foreach ($names as $name) {
			if (preg_match('/ski$/', $name)) {
				$pname .= " ".preg_replace('/ski$/', 'skiego', $name);
			}
			else if (preg_match('/cki$/', $name)) {
				$pname .= " ".preg_replace('/cki$/', 'ckiego', $name);
			}
			else if (preg_match('/dzki$/', $name)) {
				$pname .= " ".preg_replace('/dzki$/', 'dzkiego', $name);
			}
			else if (preg_match('/żki$/', $name)) {
				$pname .= " ".preg_replace('/żki$/', 'żkiego', $name);
			}
			else if (preg_match('/y$/', $name)) {
				$pname .= " ".preg_replace('/y$/','ego', $name);
			}
			else if (preg_match('/i$/', $name)) {
				$pname .= " ".preg_replace('/i$/','iego', $name);
			}
			else if (preg_match('/ek$/', $name)) {
				$pname .= " ".preg_replace('/ek$/','ka', $name);
			}
			else if (preg_match('/eł$/', $name)) {
				$pname .= " ".preg_replace('/eł$/','ła', $name);
			}
			else if (preg_match('/el$/', $name)) {
				$pname .= " ".preg_replace('/el$/','la', $name);
			}
			else if (preg_match('/ń$/', $name)) {
				$pname .= " ".preg_replace('/ń$/','nia', $name);
			}
			else if (preg_match('/ź$/', $name)) {
				$pname .= " ".preg_replace('/ź$/','zia', $name);
			}
			else if (preg_match('/niec$/', $name)) {
				$pname .= " ".preg_replace('/niec$/','ńca', $name);
			}
			else if (preg_match('/iec$/', $name)) {
				$pname .= " ".preg_replace('/iec$/','ca', $name);
			}
			else if (preg_match('/ec$/', $name)) {
				$pname .= " ".preg_replace('/ec$/','ca', $name);
			}
			else if (preg_match('/er$/', $name)) {
				$pname .= " ".preg_replace('/er$/','ra', $name);
			}
			// go
			else if (preg_match('/go$/', $name)) {
				$pname .= " ".preg_replace('/go$/','gi', $name);
			}
			// io
			else if (preg_match('/io$/', $name)) {
				$pname .= " ".preg_replace('/io$/','ii', $name);
			}
			// jo
			else if (preg_match('/jo$/', $name)) {
				$pname .= " ".preg_replace('/jo$/','ji', $name);
			}
			// ko
			else if (preg_match('/ko$/', $name)) {
				$pname .= " ".preg_replace('/ko$/','ki', $name);
			}
			// bo, co, do, fo, ho, lo, ło, mo, no, po, ro, so, to, wo, zo
			else if (preg_match('/o$/', $name)) {
				$pname .= " ".preg_replace('/o$/','y', $name);
			}
			// ga
			else if (preg_match('/ga$/', $name)) {
				$pname .= " ".preg_replace('/ga$/','gi', $name);
			}
			// ia
			else if (preg_match('/ia$/', $name)) {
				$pname .= " ".preg_replace('/ia$/','i', $name);
			}
			// ja
			else if (preg_match('/ja$/', $name)) {
				$pname .= " ".preg_replace('/ja$/','ji', $name);
			}
			// ka
			else if (preg_match('/ka$/', $name)) {
				$pname .= " ".preg_replace('/ka$/','ki', $name);
			}
			// ba, ca, da, fa, ha, la, ła, ma, na, pa, ra, sa, ta, wa, za
			else if (preg_match('/a$/', $name)) {
				$pname .= " ".preg_replace('/a$/','y', $name);
			}
			else if (preg_match('/ek]$/', $name)) {
				$pname .= " ".preg_replace('/ek]$/','ka]', $name);
			}
			else if (preg_match('/"$/', $name)) {
				$pname .= " ".preg_replace('/"$/','a"', $name);
			}
			else
				$pname .= " ".$name."a";
		}
	}
	else if ($sex == "F") {
		$names = explode(" ", $fname);
		foreach ($names as $name) {
			if (preg_match('/raska$/', $name)) {
				$pname .= " ".preg_replace('/ska$/', 'ski', $name);
			}
			else if (preg_match('/ska$/', $name)) {
				$pname .= " ".preg_replace('/ska$/', 'skiej', $name);
			}
			else if (preg_match('/cka$/', $name)) {
				$pname .= " ".preg_replace('/cka$/', 'ckiej', $name);
			}
			else if (preg_match('/dzka$/', $name)) {
				$pname .= " ".preg_replace('/dzka$/', 'dzkiej', $name);
			}
			else if (preg_match('/żka$/', $name)) {
				$pname .= " ".preg_replace('/żka$/', 'żkiej', $name);
			}
			else if (preg_match('/ka"$/', $name)) {
				$pname .= " ".preg_replace('/ka"$/', 'ki"', $name);
			}
			else if (preg_match('/ska]$/', $name)) {
				$pname .= " ".preg_replace('/ska]$/', 'skiej]', $name);
			}
			else if (preg_match('/cka]$/', $name)) {
				$pname .= " ".preg_replace('/cka]$/', 'ckiej]', $name);
			}
			else if (preg_match('/dzka]$/', $name)) {
				$pname .= " ".preg_replace('/dzka]$/', 'dzkiej]', $name);
			}
			else if (preg_match('/żka]$/', $name)) {
				$pname .= " ".preg_replace('/żka]$/', 'żkiej]', $name);
			}
			else if (preg_match('/ka]$/', $name)) {
				$pname .= " ".preg_replace('/ka]$/', 'ki]', $name);
			}
			else if (preg_match('/a]$/', $name)) {
				$pname .= " ".preg_replace('/a]$/','y]', $name);
			}
			else
				$pname .= " ".preg_replace(array('/eja$/','/ja$/','/ia$/','/la$/','/ga$/','/ea$/','/ka$/','/a$/'), array('ei','ji','ii','li','gi','ei','ki','y'), $name);
		}
	}
	else {
		$pname = "osoby: ".$pname;
	}
	if (!empty($pname)) return trim($pname);
	else return $fname;
}
