<?php
/**
 * Functions used Tools to cleanup and manipulate Gedcoms before they are imported
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
 * @subpackage Tools
 * @see validategedcom.php
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_FUNCTIONS_TOOLS_PHP', '');

/**
 * check if Gedcom needs BOM cleanup
 *
 * If the first 3 bytes of the GECOM are a Byte Order Mark, the file is UTF-8
 * and the BOM needs to be stripped.
 * @return boolean	returns true if we need to cleanup the head, false if we don't
 * @see BOM_cleanup()
 */
function need_BOM_cleanup() {
	global $fcontents;

	return strpos($fcontents, WT_UTF8_BOM)===0;
}

/**
 * cleanup the BOM
 *
 * Removes the BOM from the front of the file.
 * @return boolean	whether or not the cleanup was successful
 * @see need_BOM_cleanup()
 */
function BOM_cleanup() {
	global $fcontents;

	if (strpos($fcontents, WT_UTF8_BOM)===0) {
		$fcontents=substr($fcontents, strlen(WT_UTF8_BOM));
		return true;
	}
	return false;
}

/**
 * check if Gedcom needs HEAD cleanup
 *
 * Find where position of the 0 HEAD gedcom start element, if one does not exist then complain
 * about the file not being a Gedcom.  If it is not at the first position in the file then
 * we need to trim off all of the extra stuff before the 0 HEAD
 * @return boolean	returns true if we need to cleanup the head, false if we don't
 * @see head_cleanup()
 */
function need_head_cleanup() {
	global $fcontents;

	// Don't report BOM as data preceding 0 HEAD
	return strpos($fcontents, '0 HEAD')>0 && strpos($fcontents, WT_UTF8_BOM.'0 HEAD')!==0;
}

/**
 * cleanup the HEAD
 *
 * Cleans up the Gedcom header making sure that the 0 HEAD record is the very first thing in the file.
 * @return boolean	whether or not the cleanup was successful
 * @see need_head_cleanup()
 */
function head_cleanup() {
	global $fcontents;

	$pos1 = strpos($fcontents, "0 HEAD");
	if ($pos1>0) {
		$fcontents = substr($fcontents, $pos1);
		return true;
	}
	return false;
}

/**
 * check if there are double line endings
 *
 * Normally a gedcom should not have empty lines, this will check if the file has any empty lines in it
 * @return boolean	return true if the cleanup is needed
 * @see line_endings_cleanup()
 */
function need_line_endings_cleanup() {
	global $fcontents;

	$ct = preg_match("/\r\n(\r\n)+/", $fcontents);
	$ct += preg_match("/\r\r+/", $fcontents);
	$ct += preg_match("/\n\n+/", $fcontents);
	if ($ct>0) {
		return true;
	}
	return false;
}

/**
 * cleanup line endings
 *
 * this will remove any empty lines from the file
 * @return boolean	returns true if the operation was successful
 * @see need_line_endings_cleanup()
 */
function line_endings_cleanup() {
	global $fcontents;

	$ct = preg_match("/\r\n(\r\n)+/", $fcontents);
	$ct += preg_match("/\r\r+/", $fcontents);
	$ct += preg_match("/\n\n+/", $fcontents);
	if ($ct>0) {
		$fcontents = preg_replace(array("/(\r\n)+/", "/\r+/", "/\n+/"), array("\r\n", "\r", "\n"), $fcontents);
		return true;
	}
	else return false;
}

/**
 * check if we need to cleanup the places
 *
 * some programs, most notoriously FTM, put data in the PLAC field when it should be on the same line
 * as the event.  For example:<code>1 SSN
 * 2 PLAC 123-45-6789</code> Should really be: <code>1 SSN 123-45-6789</code>
 * this function checks if this exists
 * @return boolean	returns true if the cleanup is needed
 * @see place_cleanup()
 */
function need_place_cleanup()
{
	global $fcontents;
	//$ct = preg_match("/SOUR.+(Family Tree Maker|FTW)/", $fcontents);
	//if ($ct==0) return false;
	$ct = preg_match_all ("/^1 (CAST|DSCR|IDNO|NATI|NCHI|NMR|OCCU|PROP|RELI|SSN|TITL|_FA1|_FA2|_FA3|_FA4|_FA5|_FA6)(\s*)$[\s]+(^2 TYPE(.*)[\s]+)?(^2 DATE(.*)[\s]+)?^2 PLAC (.*)$/m",$fcontents,$matches, PREG_SET_ORDER);
	if($ct>0)
		return $matches[0];
	return false;
}

/**
 * clean up the bad places found by the need_place_cleanup() function
 * @return boolean	returns true if cleanup was successful
 * @see need_place_cleanup()
 */
function place_cleanup()
{
	global $fcontents;

//searchs for '1 CAST|DSCR|IDNO|NATI|NCHI|NMR|OCCU|PROP|RELI|SSN|TITL #chars\n'
//            'optional 2 TYPE #chars\n'
//            'optional 2 DATE #chars\n'
//            '2 PLAC #chars'
// and replaces the 1 level #chars with the PLAC #chars and blanks out the PLAC
$fcontents = preg_replace("/^1 (CAST|DSCR|IDNO|NATI|NCHI|NMR|OCCU|PROP|RELI|SSN|TITL|_FA1|_FA2|_FA3|_FA4|_FA5|_FA6)(\s*)$[\s]+(^2 TYPE(.*)[\s]+)?(^2 DATE(.*)[\s]+)?^2 PLAC (.*)$/m",
					 fixreplaceval('$1','$7','$3','$5'),$fcontents);
return true;
}

//used to create string to be replaced back into GEDCOM
function fixreplaceval($val1,$val7,$val3,$val5)
{
	$val = "1 ".$val1." ".trim($val7)."\n";
	//trim off trailing spaces
	$val3 = rtrim($val3);
	if(!empty($val3))
		$val = $val.$val3;

	//trim off trailing spaces
	$val5 = rtrim($val5);
	if(!empty($val5))
	{
		$val = $val.$val5;
	}

	//$val = $val."\r\n2 PLAC";
	return trim($val);
}


/**
 * check if we need to cleanup the dates
 *
 * Valid gedcom dates are in the form DD MMM YYYY (ie 01 JAN 2004).  However many people will enter
 * dates in an incorrect format.  This function checks if dates have been entered incorrectly.
 * This function will detect dates in the form YYYY-MM-DD, DD-MM-YYYY, and MM-DD-YYYY.  It will also
 * look for \ / - and . as delimeters.
 * @return boolean	returns true if the cleanup is needed
 * @see date_cleanup()
 */
function need_date_cleanup()
{
	global $fcontents;
	$ct = preg_match_all ("/\n\d DATE[^\d]+(\d\d\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d)/",$fcontents,$matches, PREG_SET_ORDER);
	if($ct>0) {
			return $matches[0];
		}
	else
	{
			$ct = preg_match_all ("/\n\d DATE[^\d]+(\d\d)[\/\\\\\-\.](\d\d)[\/\\\\\-\.](\d\d\d\d)/",$fcontents,$matches, PREG_SET_ORDER);
		if($ct>0) {
			// The user needs to choose between DMY and MDY
			$matches[0]["choose"] = true;
			return $matches[0];
		}
		else {
			$ct = preg_match_all ("/\n\d DATE ([^\d]+) [0-9]{1,2}, (\d\d\d\d)/",$fcontents,$matches, PREG_SET_ORDER);
			if($ct>0) {
				return $matches[0];
			}
			else {
				$ct = preg_match_all("/\n\d DATE (\d\d)[^\s]([^\d]+)[^\s](\d\d\d\d)/", $fcontents, $matches, PREG_SET_ORDER);
				if($ct>0) {
					return $matches[0];
				} else {
					if (preg_match_all("/^\d DATE (BET|FROM) \d\d? (AND|TO) \d\d? \w\w\w \d\d\d\d/m", $fcontents, $matches, PREG_SET_ORDER)) {
						return $matches[0];
					}
				}
			}
		}
	}
	return false;
}

function changemonth($monval)
{
		if($monval=="01") return "JAN";
		elseif($monval=="02") return "FEB";
		elseif($monval=="03") return "MAR";
		elseif($monval=="04") return "APR";
		elseif($monval=="05") return "MAY";
		elseif($monval=="06") return "JUN";
		elseif($monval=="07") return "JUL";
		elseif($monval=="08") return "AUG";
		elseif($monval=="09") return "SEP";
		elseif($monval=="10") return "OCT";
		elseif($monval=="11") return "NOV";
		elseif($monval=="12") return "DEC";
		return $monval;
}

/**
 * clean up the bad dates found by the need_date_cleanup() function
 * @return boolean	returns true if cleanup was successful
 * @see need_date_cleanup()
 */
function date_cleanup($dayfirst=1)
{
	global $fcontents;
	// Run all fixes twice, as there can be two dates in each DATE record

	// Convert ISO/Japanese style dates "2000-12-31"
	$fcontents=preg_replace("/2 DATE (.*)(\d\d\d\d)\W(0?[1-9]|1[0-2])\W(\d\d)/e", "'2 DATE $1$4 '.changemonth('$3').' $2'", $fcontents);
	$fcontents=preg_replace("/2 DATE (.*)(\d\d\d\d)\W(0?[1-9]|1[0-2])\W(\d\d)/e", "'2 DATE $1$4 '.changemonth('$3').' $2'", $fcontents);
	// Convert US style dates "FEB 14, 2000" or "February 5, 2000"
	$fcontents=preg_replace("/2 DATE (.*)((JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[A-Z]*) (\d{1,2}), (\d{4})/i", "2 DATE $1$4 $3 $5", $fcontents);
	$fcontents=preg_replace("/2 DATE (.*)((JAN|FEB|MAR|APR|MAY|JUN|JUL|AUG|SEP|OCT|NOV|DEC)[A-Z]*) (\d{1,2}), (\d{4})/i", "2 DATE $1$4 $3 $5", $fcontents);

	// Convert non-space delimiters "12-DEC-2000" or "01/02/2000"
	// Without the "ungreedy" qualifier, this regex won't match the first of a pair of dates and
	// with it, it won't match the second.  Not sure why.
	$fcontents=preg_replace("/2 DATE (.*?)(\d\d?)\W(\w+)\W(\d\d\d\d)/", "2 DATE $1$2 $3 $4", $fcontents);
	$fcontents=preg_replace("/2 DATE (.*)(\d\d?)\W(\w+)\W(\d\d\d\d)/", "2 DATE $1$2 $3 $4", $fcontents);

	if ($dayfirst==1) {
		// Interpret numeric dates as DD MM YYYY
		$fcontents=preg_replace("/2 DATE (.*)(\d\d?) (0?[1-9]|1[0-2]) (\d\d\d\d)/e", "'2 DATE $1$2 '.changemonth('$3').' $4'", $fcontents);
		$fcontents=preg_replace("/2 DATE (.*)(\d\d?) (0?[1-9]|1[0-2]) (\d\d\d\d)/e", "'2 DATE $1$2 '.changemonth('$3').' $4'", $fcontents);
	} else if ($dayfirst==2) {
		// Interpret numeric dates as MM DD YYYY
		$fcontents=preg_replace("/2 DATE (.*)(0?[1-9]|1[0-2]) (\d\d?) (\d\d\d\d)/e", "'2 DATE $1$3 '.changemonth('$2').' $4'", $fcontents);
		$fcontents=preg_replace("/2 DATE (.*)(0?[1-9]|1[0-2]) (\d\d?) (\d\d\d\d)/e", "'2 DATE $1$3 '.changemonth('$2').' $4'", $fcontents);
	}

	// Convert "BET 1 AND 11 JUN 1900" to "BET 1 JUN 1900 AND 11 JUN 1900"
	$fcontents=preg_replace("/^(\d DATE) (BET|FROM) (\d\d?) (AND|TO) (\d\d?) (\w\w\w \d\d\d\d)/m", '$1 $2 $3 $6 $4 $5 $6', $fcontents);
	return true;
}

/**
 * check if we need to cleanup the MAC style line endings
 *
 * PGV runs better with DOS (\r\n) or UNIX (\n) style line endings.  This function checks if
 * Mac (\r) style line endings are used in the gedcom file.
 * @return boolean	returns true if the cleanup is needed
 * @see macfile_cleanup()
 */
function need_macfile_cleanup()
{
	global $fcontents;
	//check to see if need macfile cleanup
	$ct = preg_match_all ("/\x0d[\d]/m",$fcontents,$matches);
	if($ct > 0)
		return true;
	return false;
}

/**
 * clean up the Mac (\r) line endings found by the need_macfile_cleanup() function
 * @return boolean	returns true if cleanup was successful
 * @see need_macfile_cleanup()
 */
function macfile_cleanup()
{
	global $fcontents;
	//replace all only \r (MAC files) with \r\n (DOS files)
	$fcontents = preg_replace("/\x0d([\d])/","\x0d\x0a$1", $fcontents);
	return true;
}

/**
 * convert XREFs to the value of another tag in the gedcom record
 *
 * Some genealogy applications do not maintain the gedcom XREF IDs between gedcom exports
 * but instead use another Identifying tag in the Gedcom record.  This function will allow
 * the admin to replace the XREF IDs with the value of another tag.  So for example you could replace
 * the 0 @I1@ INDI with the value of the RIN tag R101 making the line look like this 0 @R101@ INDI
 * @param String $tag	the alternate tag in the gedcom record to use when replacing the xref id, defaults to RIN
 */
function xref_change($tag="RIN")
{
	global $fcontents;
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	//-- find all of the XREFS in the file
	$ct = preg_match_all("/0 @(.*)@ INDI/", $fcontents, $match, PREG_SET_ORDER);
	for($i=0; $i<$ct; $i++) {
		$xref = trim($match[$i][1]);
		$indirec = find_gedcom_record($xref, $ged_id, true);
		if ($indirec) {
			$rt = preg_match("/1 NAME (.*)/", $indirec, $rmatch);
			if($rt>0)
			{
				$name = trim($rmatch[1])." (".$xref.")";
				$name = str_replace("/","",$name);
			}
			else
				$name = $xref;
//  		print "Found record $i - $name: ";
			$rt = preg_match("/1 $tag (.*)/", $indirec, $rmatch);
			if ($rt>0) {
				$rin = trim($rmatch[1]);
				$fcontents = str_replace("@$xref@", "@$rin@", $fcontents);
//  			print "successfully set to $rin<br />\n";
			}
			else   print "<span class=\"error\">No $tag found in record<br /></span>\n";
		}
	}
	return true;
}

/**
 * Check for ANSI encoded file
 *
 * Check the gedcom for an ansi encoded file to convert to UTF-8
 * @return boolean 	returns true if the file claims to be ANSI encoded
 * @see convert_ansi_utf8()
 */
function is_ansi() {
	global $fcontents;

	return preg_match("/1 CHAR (ANSI|ANSEL)/", $fcontents);
}

/**
 * Convert an ANSI encoded file to UTF8
 *
 * converts an ANSI or ANSEL encoded file to UTF-8
 * @see is_ansi()
 */
function convert_ansi_utf8() {
	global $fcontents;

	$fcontents = utf8_encode($fcontents);
	$fcontents = preg_replace("/1 CHAR (ANSI|ANSEL)/", "1 CHAR UTF-8", $fcontents);
}

?>
