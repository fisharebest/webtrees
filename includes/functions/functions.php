<?php
/**
 * Core Functions that can be used by any page in PGV
 *
 * The functions in this file are common to all PGV pages and include date conversion
 * routines and sorting functions.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
 *
 * Modifications Copyright (c) 2010 Greg Roach
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

define('WT_FUNCTIONS_PHP', '');

require_once WT_ROOT.'includes/classes/class_mutex.php';
require_once WT_ROOT.'includes/classes/class_media.php';
require_once WT_ROOT.'includes/functions/functions_utf-8.php';

////////////////////////////////////////////////////////////////////////////////
// Extract, sanitise and validate FORM (POST), URL (GET) and COOKIE variables.
//
// Request variables should ALWAYS be accessed through these functions, to
// protect against XSS (cross-site-scripting) attacks.
//
// $var     - The variable to check
// $regex   - Regular expression to validate the variable (or an array of
//            regular expressions).  A number of common regexes are defined in
//            session.php as constants WT_REGEX_*.  If no value is specified,
//            the default blocks all characters that could introduce scripts.
// $default - A value to use if $var is undefined or invalid.
//
// You should always know whether your variables are coming from GET or POST,
// and always use the correct function.
//
// NOTE: when using checkboxes, $var is either set (checked) or unset (not
// checked).  This lets us use the syntax safe_GET('my_checkbox', 'yes', 'no')
//
// NOTE: when using listboxes, $regex can be an array of valid values.  For
// example, you can use safe_POST('lang', array_keys($pgv_language), WT_LOCALE)
// to validate against a list of valid languages and supply a sensible default.
////////////////////////////////////////////////////////////////////////////////

function safe_POST($var, $regex=WT_REGEX_NOSCRIPT, $default=null) {
	return safe_REQUEST($_POST, $var, $regex, $default);
}
function safe_GET($var, $regex=WT_REGEX_NOSCRIPT, $default=null) {
	return safe_REQUEST($_GET, $var, $regex, $default);
}
function safe_COOKIE($var, $regex=WT_REGEX_NOSCRIPT, $default=null) {
	return safe_REQUEST($_COOKIE, $var, $regex, $default);
}

function safe_GET_integer($var, $min, $max, $default) {
	$num=safe_GET($var, WT_REGEX_INTEGER, $default);
	$num=max($num, $min);
	$num=min($num, $max);
	return (int)$num;
}
function safe_POST_integer($var, $min, $max, $default) {
	$num=safe_POST($var, WT_REGEX_INTEGER, $default);
	$num=max($num, $min);
	$num=min($num, $max);
	return (int)$num;
}

function safe_GET_bool($var, $true='(y|Y|1|yes|YES|Yes|true|TRUE|True|on)') {
	return !is_null(safe_GET($var, $true));
}
function safe_POST_bool($var, $true='(y|Y|1|yes|YES|Yes|true|TRUE|True|on)') {
	return !is_null(safe_POST($var, $true));
}

function safe_GET_xref($var, $default=null) {
	return safe_GET($var, WT_REGEX_XREF, $default);
}
function safe_POST_xref($var, $default=null) {
	return safe_POST($var, WT_REGEX_XREF, $default);
}

function safe_REQUEST($arr, $var, $regex=WT_REGEX_NOSCRIPT, $default=null) {
	if (is_array($regex)) {
		$regex='(?:'.join('|', $regex).')';
	}
	if (array_key_exists($var, $arr) && preg_match_recursive('~^'.addcslashes($regex, '~').'$~', $arr[$var])) {
		return trim_recursive($arr[$var]);
	} else {
		return $default;
	}
}

function encode_url($url, $entities=true) {
	$url = decode_url($url, $entities); // Make sure we don't do any double conversions
	$url = str_replace(array(' ', '+', '@#', '"', "'"), array('%20', '%2b', '@%23', '%22', '%27'), $url);
	if ($entities) {
		$url = htmlspecialchars($url, ENT_COMPAT, 'UTF-8');
	}
	return $url;
}

function decode_url($url, $entities=true) {
	if ($entities) {
		$url = html_entity_decode($url, ENT_COMPAT, 'UTF-8');
	}
	$url = rawurldecode($url); // GEDCOM names can legitimately contain " " and "+"
	return $url;
}

function preg_match_recursive($regex, $var) {
	if (is_scalar($var)) {
		return preg_match($regex, $var);
	} else {
		if (is_array($var)) {
			foreach ($var as $k=>$v) {
				if (!preg_match_recursive($regex, $v)) {
					return false;
				}
			}
			return true;
		} else {
			// Neither scalar nor array.  Object?
			return false;
		}
	}
}

function trim_recursive($var) {
	if (is_scalar($var)) {
		return trim($var);
	} else {
		if (is_array($var)) {
			foreach ($var as $k=>$v) {
				$var[$k]=trim_recursive($v);
			}
			return $var;
		} else {
			// Neither scalar nor array.  Object?
			return $var;
		}
	}
}

// Update the variable definitions in a PHP config file, such as config.php
function update_config(&$text, $var, $value) {
	// NULL values probably wouldn't hurt, but empty strings are probably better
	if (is_null($value)) {
		$value='';
	}
	$regex='/^[ \t]*[$]'.$var.'[ \t]*=.*;[ \t]*/m';
	$assign='$'.$var.'='.var_export($value, true).'; ';
	if (preg_match($regex, $text)) {
		// Variable found in file - update it
		$text=preg_replace($regex, $assign, $text);
	} else {
		// Variable not found in file - insert it
		$text=preg_replace('/^(.*[\r\n]+)([ \t]*[$].*)$/s', '$1'.$assign." // new config variable\n".'$2', $text);
	}
}

// Convert a file upload PHP error code into user-friendly text
function file_upload_error_text($error_code) {
	switch ($error_code) {
	case UPLOAD_ERR_OK:
		return i18n::translate('File successfully uploaded');
	case UPLOAD_ERR_INI_SIZE:
	case UPLOAD_ERR_FORM_SIZE:
		return i18n::translate('Uploaded file exceeds the allowed size');
	case UPLOAD_ERR_PARTIAL:
		return i18n::translate('File was only partially uploaded, please try again');
	case UPLOAD_ERR_NO_FILE:
		return i18n::translate('No file was received. Please upload again.');
	case UPLOAD_ERR_NO_TMP_DIR:
		return i18n::translate('Missing PHP temporary directory');
	case UPLOAD_ERR_CANT_WRITE:
		return i18n::translate('PHP failed to write to disk');
	case UPLOAD_ERR_EXTENSION:
		return i18n::translate('PHP blocked file by extension');
	}
}

/**
 * get gedcom configuration file
 *
 * this function returns the path to the currently active GEDCOM configuration file
 * @return string path to gedcom.ged_conf.php configuration file
 */
function get_config_file($ged_id=WT_GED_ID) {
	global $INDEX_DIRECTORY;

	$config=get_gedcom_setting($ged_id, 'config');
	// Compatibility with non-php based storage, (PGV 4.3.0 onwards)
	$config=str_replace('${INDEX_DIRECTORY}', $INDEX_DIRECTORY, $config);

	if (!file_exists($config)) {
		return 'config_gedcom.php';
	} else {
		return $config;
	}
}

/**
 * print write_access option
 *
 * @param string $checkVar
 */
function write_access_option($checkVar) {
	echo "<option value=\"WT_PRIV_PUBLIC\"";
	echo $checkVar==WT_PRIV_PUBLIC ? " selected=\"selected\"" : '';
	echo ">", i18n::translate('Show to public'), "</option>\n";

	echo "<option value=\"WT_PRIV_USER\"";
	echo $checkVar==WT_PRIV_USER ? " selected=\"selected\"" : '';
	echo ">", i18n::translate('Show only to authenticated users'), "</option>\n";

	echo "<option value=\"WT_PRIV_NONE\"";
	echo $checkVar==WT_PRIV_NONE ? " selected=\"selected\"" : '';
	echo ">", i18n::translate('Show only to admin users'), "</option>\n";

	echo "<option value=\"WT_PRIV_HIDE\"";
	echo $checkVar==WT_PRIV_HIDE ? " selected=\"selected\"" : '';
	echo ">", i18n::translate('Hide even from admin users'), "</option>\n";
}

/**
 * Get the path to the privacy file
 *
 * Get the path to the privacy file for the currently active GEDCOM
 * @return string path to the privacy file
 */
function get_privacy_file($ged_id=WT_GED_ID) {
	global $INDEX_DIRECTORY;

	$privfile=get_gedcom_setting($ged_id, 'privacy');
	// Compatibility with non-php based storage
	$privfile=str_replace('${INDEX_DIRECTORY}', $INDEX_DIRECTORY, $privfile);

	if (!file_exists($privfile)) {
		return 'privacy.php';
	} else {
		return $privfile;
	}
}

function load_privacy_file($ged_id=WT_GED_ID) {
	// Load the privacy settings into global scope
	global $SHOW_DEAD_PEOPLE, $SHOW_LIVING_NAMES, $SHOW_SOURCES, $MAX_ALIVE_AGE;
	global $ENABLE_CLIPPINGS_CART, $SHOW_MULTISITE_SEARCH;
	global $USE_RELATIONSHIP_PRIVACY, $MAX_RELATION_PATH_LENGTH, $CHECK_MARRIAGE_RELATIONS;
	global $PRIVACY_BY_YEAR, $PRIVACY_BY_RESN, $SHOW_PRIVATE_RELATIONSHIPS;
	global $person_privacy, $user_privacy, $global_facts, $person_facts;

	// Load default settings
	require WT_ROOT.'privacy.php';

	// Load settings for the specified gedcom
	$privacy_file=get_privacy_file($ged_id);
	if ($privacy_file && file_exists($privacy_file)) {
		require $privacy_file;
	}
}

/**
 * Update the site configuration settings
 * New settings are passed in as an array of key value pairs
 * The key in the array should be the name of the setting to change
 * the value should be the new value
 * $newconfig['CONFIGURED'] = true;
 *
 * @param array	$newconfig
 * @param boolean $return	return the text or try to write the file
 * @return mixed	returns true on success, or returns an array of error messages on failure
 */
function update_site_config($newconfig, $return = false) {
	$errors = array();

	//-- load the configuration file text
	if (file_exists("config.php")) {
		$configtext = file_get_contents("config.php");
	} else {
		$configtext = file_get_contents("config.dist");
	}

	foreach($newconfig as $setting=>$value) {
		update_config($configtext, $setting, $value);
	}

	//-- check if the configtext is valid PHP
	$res = @eval($configtext);
	if ($res===false) {
		if ($return) {
			return $configtext;
		}

		$fp = @fopen("config.php", "wb");
		if (!$fp) {
			$error['msg'] = i18n::translate('Error!!! Cannot write to the webtrees configuration file.  Please check file and directory permissions and try again.');
			$errors[] = $error;
		} else {
			fwrite($fp, $configtext);
			fclose($fp);
		}
	} else {
		$error['msg'] = "There was an error in the generated config.php. ".htmlentities($configtext);
		$errors[] = $error;
	}

	if (count($errors)>0) {
		return $errors;
	}
	return true;
}

// This functions checks if an existing file is physically writeable
// The standard PHP function only checks for the R/O attribute and doesn't
// detect authorisation by ACL.
function file_is_writeable($file) {
	$err_write = false;
	$handle = @fopen($file, "r+");
	if ($handle) {
		$i = fclose($handle);
		$err_write = true;
	}
	return($err_write);
}

/**
 * PGV Error Handling function
 *
 * This function will be called by PHP whenever an error occurs.  The error handling
 * is set in the session.php
 * @see http://us2.php.net/manual/en/function.set-error-handler.php
 */
function pgv_error_handler($errno, $errstr, $errfile, $errline) {
	if ((error_reporting() > 0)&&($errno<2048)) {
		if (WT_ERROR_LEVEL==0) {
			return;
		}
		if (stristr($errstr, "by reference")==true) {
			return;
		}
		$fmt_msg="\n<br />ERROR {$errno}: {$errstr}<br />\n";
		$log_msg="ERROR {$errno}: {$errstr}; ";
		// Although debug_backtrace should always exist in PHP5, without this check, PHP sometimes crashes.
		// Possibly calling it generates an error, which causes infinite recursion??
		if ($errno<16 && function_exists("debug_backtrace") && strstr($errstr, "headers already sent by")===false) {
			$backtrace=debug_backtrace();
			$num=count($backtrace);
			if (WT_ERROR_LEVEL==1) {
				$num=1;
			}
			for ($i=0; $i<$num; $i++) {
				if ($i==0) {
					$fmt_msg.="0 Error occurred on ";
					$log_msg.="0 Error occurred on ";
				} else {
					$fmt_msg.="{$i} called from ";
					$log_msg.="{$i} called from ";
				}
				if (isset($backtrace[$i]["line"]) && isset($backtrace[$i]["file"])) {
					$fmt_msg.="line <b>{$backtrace[$i]['line']}</b> of file <b>".basename($backtrace[$i]['file'])."</b>";
					$log_msg.="line {$backtrace[$i]['line']} of file ".basename($backtrace[$i]['file']);
				}
				if ($i<$num-1) {
					$fmt_msg.=" in function <b>".$backtrace[$i+1]['function']."</b>";
					$log_msg.=" in function ".$backtrace[$i+1]['function'];
				}
				$fmt_msg.="<br />\n";
			}
		}
		echo $fmt_msg;
		if (function_exists('AddToLog')) {
			AddToLog($log_msg);
		}
		if ($errno==1) {
			die();
		}
	}
	return false;
}

// ************************************************* START OF GEDCOM FUNCTIONS ********************************* //

/**
 * Get first tag in GEDCOM sub-record
 *
 * This routine uses function get_sub_record to retrieve the specified sub-record
 * and then returns the first tag.
 *
 */
function get_first_tag($level, $tag, $gedrec, $num=1) {
	$temp = get_sub_record($level, $level." ".$tag, $gedrec, $num)."\n";
	$temp = str_replace("\r\n", "\n", $temp);
	$length = strpos($temp, "\n");
	if ($length===false) {
		$length = strlen($temp);
	}
	return substr($temp, 2, $length-2);
}

/**
 * get a gedcom subrecord
 *
 * searches a gedcom record and returns a subrecord of it.  A subrecord is defined starting at a
 * line with level N and all subsequent lines greater than N until the next N level is reached.
 * For example, the following is a BIRT subrecord:
 * <code>1 BIRT
 * 2 DATE 1 JAN 1900
 * 2 PLAC Phoenix, Maricopa, Arizona</code>
 * The following example is the DATE subrecord of the above BIRT subrecord:
 * <code>2 DATE 1 JAN 1900</code>
 * @author John Finlay (yalnifj)
 * @author Roland Dalmulder (roland-d)
 * @param int $level the N level of the subrecord to get
 * @param string $tag a gedcom tag or string to search for in the record (ie 1 BIRT or 2 DATE)
 * @param string $gedrec the parent gedcom record to search in
 * @param int $num this allows you to specify which matching <var>$tag</var> to get.  Oftentimes a
 * gedcom record will have more that 1 of the same type of subrecord.  An individual may have
 * multiple events for example.  Passing $num=1 would get the first 1.  Passing $num=2 would get the
 * second one, etc.
 * @return string the subrecord that was found or an empty string "" if not found.
 */
function get_sub_record($level, $tag, $gedrec, $num=1) {
	if (empty($gedrec)) {
		return "";
	}
	// -- adding \n before and after gedrec
	$gedrec = "\n".$gedrec."\n";
	$pos1=0;
	$subrec = "";
	$tag = trim($tag);
	$searchTarget = "~[\r\n]".$tag."[\s]~";
	$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
	if ($ct==0) {
		$tag = preg_replace('/(\w+)/', "_$1", $tag);
		$ct = preg_match_all($searchTarget, $gedrec, $match, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		if ($ct==0) {
			return "";
		}
	}
	if ($ct<$num) {
		return "";
	}
	$pos1 = $match[$num-1][0][1];
	$pos2 = strpos($gedrec, "\n$level", $pos1+1);
	if (!$pos2) {
		$pos2 = strpos($gedrec, "\n1", $pos1+1);
	}
	if (!$pos2) {
		$pos2 = strpos($gedrec, "\nWT_", $pos1+1); // WT_SPOUSE, WT_FAMILY_ID ...
	}
	if (!$pos2) {
		return ltrim(substr($gedrec, $pos1));
	}
	$subrec = substr($gedrec, $pos1, $pos2-$pos1);
	return ltrim($subrec);
}

/**
 * find all of the level 1 subrecords of the given record
 * @param string $gedrec the gedcom record to get the subrecords from
 * @param string $ignore a list of tags to ignore
 * @param boolean $families whether to include any records from the family
 * @param boolean $sort whether or not to sort the record by date
 * @param boolean $ApplyPriv whether to apply privacy right now or later
 * @return array an array of the raw subrecords to return
 */
function get_all_subrecords($gedrec, $ignore="", $families=true, $ApplyPriv=true) {
	global $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	$repeats = array();

	$id = "";
	$gt = preg_match('/0 @('.WT_REGEX_XREF.')@/', $gedrec, $gmatch);
	if ($gt > 0) {
		$id = $gmatch[1];
	}

	$hasResn = strstr($gedrec, " RESN ");
	$prev_tags = array();
	$ct = preg_match_all('/\n1 ('.WT_REGEX_TAG.')(.*)/', $gedrec, $match, PREG_SET_ORDER|PREG_OFFSET_CAPTURE);
	for ($i=0; $i<$ct; $i++) {
		$fact = trim($match[$i][1][0]);
		$pos1 = $match[$i][0][1];
		if ($i<$ct-1) {
			$pos2 = $match[$i+1][0][1];
		} else {
			$pos2 = strlen($gedrec);
		}
		if (empty($ignore) || strpos($ignore, $fact)===false) {
			if (!$ApplyPriv || (showFact($fact, $id)&& showFactDetails($fact, $id))) {
				if (isset($prev_tags[$fact])) {
					$prev_tags[$fact]++;
				} else {
					$prev_tags[$fact] = 1;
				}
				$subrec = substr($gedrec, $pos1, $pos2-$pos1);
				if (!$ApplyPriv || !$hasResn || !FactViewRestricted($id, $subrec)) {
					if ($fact=="EVEN") {
						$tt = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
						if ($tt>0) {
							$type = trim($tmatch[1]);
							if (!$ApplyPriv || (showFact($type, $id)&&showFactDetails($type, $id))) {
								$repeats[] = trim($subrec)."\n";
							}
						} else
							$repeats[] = trim($subrec)."\n";
					} else {
						$repeats[] = trim($subrec)."\n";
					}
				}
			}
		}
	}

	//-- look for any records in FAMS records
	if ($families) {
		$ft = preg_match_all('/\n1 FAMS @('.WT_REGEX_XREF.')@/', $gedrec, $fmatch, PREG_SET_ORDER);
		for ($f=0; $f<$ft; $f++) {
			$famid = $fmatch[$f][1];
			$famrec = find_family_record($fmatch[$f][1], $ged_id);
			$parents = find_parents_in_record($famrec);
			if ($id==$parents["HUSB"]) {
				$spid = $parents["WIFE"];
			} else {
				$spid = $parents["HUSB"];
			}
			$prev_tags = array();
			$ct = preg_match_all('/\n1 ('.WT_REGEX_TAG.')(.*)/', $famrec, $match, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				$fact = trim($match[$i][1]);
				if (empty($ignore) || strpos($ignore, $fact)===false) {
					if (!$ApplyPriv || (showFact($fact, $id)&&showFactDetails($fact, $id))) {
						if (isset($prev_tags[$fact])) {
							$prev_tags[$fact]++;
						} else {
							$prev_tags[$fact] = 1;
						}
						$subrec = get_sub_record(1, "1 $fact", $famrec, $prev_tags[$fact]);
						$subrec .= "\n2 _PGVS @$spid@\n2 _PGVFS @$famid@\n";
						if ($fact=="EVEN") {
							$ct = preg_match("/2 TYPE (.*)/", $subrec, $tmatch);
							if ($ct>0) {
								$type = trim($tmatch[1]);
								if (!$ApplyPriv or (showFact($type, $id)&&showFactDetails($type, $id))) {
									$repeats[] = trim($subrec)."\n";
								}
							} else {
								$repeats[] = trim($subrec)."\n";
							}
						} else {
							$repeats[] = trim($subrec)."\n";
						}
					}
				}
			}
		}
	}

	return $repeats;
}

/**
 * get gedcom tag value
 *
 * returns the value of a gedcom tag from the given gedcom record
 * @param string $tag	The tag to find, use : to delineate subtags
 * @param int $level	The gedcom line level of the first tag to find, setting level to 0 will cause it to use 1+ the level of the incoming record
 * @param string $gedrec	The gedcom record to get the value from
 * @param int $truncate	Should the value be truncated to a certain number of characters
 * @param boolean $convert	Should data like dates be converted using the configuration settings
 * @return string
 */
function get_gedcom_value($tag, $level, $gedrec, $truncate='', $convert=true) {
	global $SHOW_PEDIGREE_PLACES, $GEDCOM;
	$ged_id=get_id_from_gedcom($GEDCOM);

	if (empty($gedrec)) {
		return "";
	}
	$tags = explode(':', $tag);
	$origlevel = $level;
	if ($level==0) {
		$level = $gedrec{0} + 1;
	}

	$subrec = $gedrec;
	foreach ($tags as $indexval => $t) {
		$lastsubrec = $subrec;
		$subrec = get_sub_record($level, "$level $t", $subrec);
		if (empty($subrec) && $origlevel==0) {
			$level--;
			$subrec = get_sub_record($level, "$level $t", $lastsubrec);
		}
		if (empty($subrec)) {
			if ($t=="TITL") {
				$subrec = get_sub_record($level, "$level ABBR", $lastsubrec);
				if (!empty($subrec)) {
					$t = "ABBR";
				}
			}
			if (empty($subrec)) {
				if ($level>0) {
					$level--;
				}
				$subrec = get_sub_record($level, "@ $t", $gedrec);
				if (empty($subrec)) {
					return;
				}
			}
		}
		$level++;
	}
	$level--;
	$ct = preg_match("/$level $t(.*)/", $subrec, $match);
	if ($ct==0) {
		$ct = preg_match("/$level @.+@ (.+)/", $subrec, $match);
	}
	if ($ct==0) {
		$ct = preg_match("/@ $t (.+)/", $subrec, $match);
	}
	if ($ct > 0) {
		$value = trim($match[1]);
		$ct = preg_match("/@(.*)@/", $value, $match);
		if (($ct > 0 ) && ($t!="DATE")) {
			$oldsub = $subrec;
			switch ($t) {
			case 'HUSB':
			case 'WIFE':
			case 'CHIL':
				$subrec = find_person_record($match[1], $ged_id);
				break;
			case 'FAMC':
			case 'FAMS':
				$subrec = find_family_record($match[1], $ged_id);
				break;
			case 'SOUR':
				$subrec = find_source_record($match[1], $ged_id);
				break;
			case 'REPO':
				$subrec = find_other_record($match[1], $ged_id);
				break;
			default:
				$subrec = find_gedcom_record($match[1], $ged_id);
				break;
			}
			if ($subrec) {
				$value=$match[1];
				$ct = preg_match("/0 @$match[1]@ $t (.+)/", $subrec, $match);
				if ($ct>0) {
					$value = $match[1];
					$level = 0;
				} else
					$subrec = $oldsub;
			} else
				//-- set the value to the id without the @
				$value = $match[1];
		}
		if ($level!=0 || $t!="NOTE") {
			$value .= get_cont($level+1, $subrec);
		}
		$value = preg_replace("'\n'", "", $value);
		$value = preg_replace("'<br />'", "\n", $value);
		$value = trim($value);
		//-- if it is a date value then convert the date
		if ($convert && $t=="DATE") {
			$g = new GedcomDate($value);
			$value = $g->Display();
			if (!empty($truncate)) {
				if (utf8_strlen($value)>$truncate) {
					$value = preg_replace("/\(.+\)/", "", $value);
					//if (utf8_strlen($value)>$truncate) {
					//	$value = preg_replace_callback("/([a-zśź]+)/ui", create_function('$matches', 'return utf8_substr($matches[1], 0, 3);'), $value);
					//}
				}
			}
		} else
			//-- if it is a place value then apply the pedigree place limit
			if ($convert && $t=="PLAC") {
				if ($SHOW_PEDIGREE_PLACES>0) {
					$plevels = explode(',', $value);
					$value = "";
					for ($plevel=0; $plevel<$SHOW_PEDIGREE_PLACES; $plevel++) {
						if (!empty($plevels[$plevel])) {
							if ($plevel>0) {
								$value .= ", ";
							}
							$value .= trim($plevels[$plevel]);
						}
					}
				}
				if (!empty($truncate)) {
					if (strlen($value)>$truncate) {
						$plevels = explode(',', $value);
						$value = "";
						for ($plevel=0; $plevel<count($plevels); $plevel++) {
							if (!empty($plevels[$plevel])) {
								if (strlen($plevels[$plevel])+strlen($value)+3 < $truncate) {
									if ($plevel>0) {
										$value .= ", ";
									}
									$value .= trim($plevels[$plevel]);
								} else
									break;
							}
						}
					}
				}
			} else
				if ($convert && $t=="SEX") {
					if ($value=="M") {
						$value = utf8_substr(i18n::translate('Male'), 0, 1);
					} elseif ($value=="F") {
						$value = utf8_substr(i18n::translate('Female'), 0, 1);
					} else {
						$value = utf8_substr(i18n::translate('unknown'), 0, 1);
					}
				} else {
					if (!empty($truncate)) {
						if (strlen($value)>$truncate) {
							$plevels = explode(' ', $value);
							$value = "";
							for ($plevel=0; $plevel<count($plevels); $plevel++) {
								if (!empty($plevels[$plevel])) {
									if (strlen($plevels[$plevel])+strlen($value)+3 < $truncate) {
										if ($plevel>0) {
											$value .= " ";
										}
										$value .= trim($plevels[$plevel]);
									} else
										break;
								}
							}
						}
					}
				}
		return $value;
	}
	return "";
}

/**
 * create CONT lines
 *
 * Break input GEDCOM subrecord into pieces not more than 255 chars long,
 * with CONC and CONT lines as needed.  Routine also pays attention to the
 * word wrapped Notes option.  Routine also avoids splitting UTF-8 encoded
 * characters between lines.
 *
 * @param	string	$newline	Input GEDCOM subrecord to be worked on
 * @return	string	$newged		Output string with all necessary CONC and CONT lines
 */
function breakConts($newline) {
	global $WORD_WRAPPED_NOTES;

	// Determine level number of CONC and CONT lines
	$level = substr($newline, 0, 1);
	$tag = substr($newline, 1, 6);
	if ($tag!=" CONC " && $tag!=" CONT ") {
		$level ++;
	}

	$newged = "";
	$newlines = preg_split("/\r?\n/", rtrim(stripLRMRLM($newline)));
	for ($k=0; $k<count($newlines); $k++) {
		if ($k>0) {
			$newlines[$k] = "{$level} CONT ".$newlines[$k];
		}
		if (strlen($newlines[$k])>255) {
			if ($WORD_WRAPPED_NOTES) {
				while (strlen($newlines[$k])>255) {
					// Make sure this piece ends on a blank, because one blank will be
					// added automatically when everything is put back together
					$lastBlank = strrpos(substr($newlines[$k], 0, 255), " ");
					$thisPiece = rtrim(substr($newlines[$k], 0, $lastBlank+1));
					$newged .= $thisPiece."\n";
					$newlines[$k] = substr($newlines[$k], (strlen($thisPiece)+1));
					$newlines[$k] = "{$level} CONC ".$newlines[$k];
				}
			} else {
				while (strlen($newlines[$k])>255) {
					// Make sure this piece doesn't end on a blank
					// (Blanks belong at the start of the next piece)
					$thisPiece = rtrim(substr($newlines[$k], 0, 255));
					// Make sure this piece doesn't end in the middle of a UTF-8 character
					$nextPieceFirstChar = substr($newlines[$k], strlen($thisPiece), 1);
					if (($nextPieceFirstChar&"\xC0") == "\x80") {
						// Include all of the UTF-8 character in next piece
						while (true) {
							// Find the start of the UTF-8 encoded character
							$nextPieceFirstChar = substr($thisPiece, -1);
							$thisPiece = substr($thisPiece, 0, -1);
							if (($nextPieceFirstChar&"\xC0") != "\x80") {
								break;
							}
						}
						// Make sure we didn't back up to a blank
						$thisPiece = rtrim($thisPiece);
					}
					$newged .= $thisPiece."\n";
					$newlines[$k] = substr($newlines[$k], strlen($thisPiece));
					$newlines[$k] = "{$level} CONC ".$newlines[$k];
				}
			}
			$newged .= trim($newlines[$k])."\n";
		} else {
			$newged .= trim($newlines[$k])."\n";
		}
	}
	return $newged;
}

/**
 * get CONT lines
 *
 * get the N+1 CONT or CONC lines of a gedcom subrecord
 * @param int $nlevel the level of the CONT lines to get
 * @param string $nrec the gedcom subrecord to search in
 * @return string a string with all CONT or CONC lines merged
 */
function get_cont($nlevel, $nrec, $tobr=true) {
	global $WORD_WRAPPED_NOTES;
	$text = "";
	if ($tobr) {
		$newline = "<br />";
	} else {
		$newline = "\n";
	}

	$subrecords = explode("\n", $nrec);
	foreach ($subrecords as $thisSubrecord) {
		if (substr($thisSubrecord, 0, 2)!=$nlevel." ") {
			continue;
		}
		$subrecordType = substr($thisSubrecord, 2, 4);
		if ($subrecordType=="CONT") {
			$text .= $newline;
		}
		if ($subrecordType=="CONC" && $WORD_WRAPPED_NOTES) {
			$text .= " ";
		}
		if ($subrecordType=="CONT" || $subrecordType=="CONC") {
			$text .= rtrim(substr($thisSubrecord, 7));
		}
	}

	return rtrim($text, " ");
}

/**
 * find the parents in a family
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents($famid) {
	$famrec = find_family_record($famid, WT_GED_ID);
	if (empty($famrec)) {
		if (WT_USER_CAN_EDIT) {
			$famrec = find_updated_record($famid, WT_GED_ID);
			if (empty($famrec)) {
				return false;
			}
		} else {
			return false;
		}
	}
	return find_parents_in_record($famrec);
}

/**
 * find the parents in a family record
 *
 * find and return a two element array containing the parents of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @return array returns a two element array with indexes HUSB and WIFE for the parent ids
 */
function find_parents_in_record($famrec) {
	if (empty($famrec)) {
		return false;
	}
	$parents = array();
	$ct = preg_match('/1 HUSB @('.WT_REGEX_XREF.')@/', $famrec, $match);
	if ($ct>0) {
		$parents["HUSB"]=$match[1];
	} else {
		$parents["HUSB"]="";
	}
	$ct = preg_match('/1 WIFE @('.WT_REGEX_XREF.')@/', $famrec, $match);
	if ($ct>0) {
		$parents["WIFE"]=$match[1];
	} else {
		$parents["WIFE"]="";
	}
	return $parents;
}

/**
 * find the children in a family
 *
 * find and return an array containing the children of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famid the gedcom xref id for the family
 * @param string $me	an xref id of a child to ignore, useful when you want to get a person's
 * siblings but do want to include them as well
 * @return array
 */
function find_children($famid, $me='') {
	$famrec = find_family_record($famid, WT_GED_ID);
	if (empty($famrec)) {
		if (WT_USER_CAN_EDIT) {
			$famrec = find_updated_record($famid, WT_GED_ID);
			if (empty($famrec)) {
				return false;
			}
		} else {
			return false;
		}
	}
	return find_children_in_record($famrec);
}

/**
 * find the children in a family record
 *
 * find and return an array containing the children of the given family record
 * @author John Finlay (yalnifj)
 * @param string $famrec the gedcom record of the family to search in
 * @param string $me	an xref id of a child to ignore, useful when you want to get a person's
 * siblings but do want to include them as well
 * @return array
 */
function find_children_in_record($famrec, $me='') {
	$children = array();
	if (empty($famrec)) {
		return $children;
	}

	$num = preg_match_all('/\n1 CHIL @('.WT_REGEX_XREF.')@/', $famrec, $match, PREG_SET_ORDER);
	for ($i=0; $i<$num; $i++) {
		$child = trim($match[$i][1]);
		if ($child!=$me) {
			$children[] = $child;
		}
	}
	return $children;
}

/**
 * find all child family ids
 *
 * searches an individual gedcom record and returns an array of the FAMC ids where this person is a
 * child in the family, but only those families that are allowed to be seen by current user
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_family_ids($pid) {
	$indirec=find_person_record($pid, WT_GED_ID);
	return find_visible_families_in_record($indirec, "FAMC");
}

/**
 * find all spouse family ids
 *
 * searches an individual gedcom record and returns an array of the FAMS ids where this person is a
 * spouse in the family, but only those families that are allowed to be seen by current user
 * @param string $pid the gedcom xref id for the person to look in
 * @return array array of family ids
 */
function find_sfamily_ids($pid) {
	$indirec=find_person_record($pid, WT_GED_ID);
	return find_visible_families_in_record($indirec, "FAMS");
}

/**
 * find all family ids in the given record
 *
 * searches an individual gedcom record and returns an array of the FAMS|C ids
 * @param string $indirec the gedcom record for the person to look in
 * @param string $tag 	The family tag to look for
 * @return array array of family ids
 */
function find_families_in_record($indirec, $tag) {
	preg_match_all("/\n1 {$tag} @(".WT_REGEX_XREF.')@/', $indirec, $match);
	return $match[1];
}

/**
 * find all family ids in the given record that should be visible to the current user
 *
 * searches an individual gedcom record and returns an array of the FAMS|C ids that are visible
 * @param string $indirec the gedcom record for the person to look in
 * @param string $tag 	The family tag to look for, FAMS or FAMC
 * @return array array of family ids
 */
function find_visible_families_in_record($indirec, $tag) {
	$allfams = find_families_in_record($indirec, $tag);
	$visiblefams = array();
	// select only those that are visible to current user
	foreach ($allfams as $key=>$famid) {
		if (displayDetailsById($famid, "FAM")) {
			$visiblefams[] = $famid;
		}
	}
	return $visiblefams;
}

/**
 * find and return an updated gedcom record
 * @param string $gid	the id of the record to find
 * @param string $gedfile	the gedcom file to get the record from.. defaults to currently active gedcom
 */
function find_updated_record($gid, $ged_id) {
	global $pgv_changes;

	// NOTE: when changes are moved to database storage, they will be
	// indexed by gedcom_id, not gedcom_name
	$gedfile=get_gedcom_from_id($ged_id);

	if (isset($pgv_changes[$gid."_".$gedfile])) {
		$change = end($pgv_changes[$gid."_".$gedfile]);
		return $change['undo'];
	}
	return "";
}

// Find out if there are any pending changes that a given user may accept
function exists_pending_change($user_id=WT_USER_ID, $ged_id=WT_GED_ID) {
	global $pgv_changes;

	if (empty($pgv_changes) || !userCanAccept($user_id, $ged_id)) {
		return false;
	}

	// NOTE: when changes are moved to database storage, they will be
	// indexed by gedcom_id, not gedcom_name
	$gedcom=get_gedcom_from_id($ged_id);
	foreach ($pgv_changes as $pgv_change) {
		if ($pgv_change[0]['gedcom']==$gedcom) {
			return true;
		}
	}
	return false;
}

// ************************************************* START OF MULTIMEDIA FUNCTIONS ********************************* //
/**
 * find the highlighted media object for a gedcom entity
 *
 * Rules for finding the highlighted media object:
 * 1. The first _PRIM Y object will be used regardless of level in gedcom record
 * 2. The first level 1 object will be used if there if it doesn't have _PRIM N (level 1 objects appear on the media tab on the individual page)
 *
 * @param string $pid the individual, source, or family id
 * @param string $indirec the gedcom record to look in
 * @return array an object array with indexes "thumb" and "file" for thumbnail and filename
 */
function find_highlighted_object($pid, $ged_id, $indirec) {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $WT_IMAGE_DIR, $WT_IMAGES, $MEDIA_EXTERNAL, $TBLPREFIX;

	if (!showFactDetails("OBJE", $pid)) {
		return false;
	}
	$object = array();
	$media = array();

	//-- handle finding the media of remote objects
	$ct = preg_match("/(.*):(.*)/", $pid, $match);
	if ($ct>0) {
		require_once WT_ROOT.'includes/classes/class_serviceclient.php';
		$client = ServiceClient::getInstance($match[1]);
		if (!is_null($client)) {
			$mt = preg_match_all('/\n\d OBJE @('.WT_REGEX_XREF.')@/', $indirec, $matches, PREG_SET_ORDER);
			for ($i=0; $i<$mt; $i++) {
				$mediaObj = Media::getInstance($matches[$i][1]);
				$mrec = $mediaObj->getGedcomRecord();
				if (!empty($mrec)) {
					$file = get_gedcom_value("FILE", 1, $mrec);
					$row = array($matches[$i][1], $file, $mrec, $matches[$i][0]);
					$media[] = $row;
				}
			}
		}
	}

	//-- find all of the media items for a person
	$media=
		WT_DB::prepare("SELECT m_media, m_file, m_gedrec, mm_gedrec FROM {$TBLPREFIX}media, {$TBLPREFIX}media_mapping WHERE m_media=mm_media AND m_gedfile=mm_gedfile AND m_gedfile=? AND mm_gid=? ORDER BY mm_order")
		->execute(array($ged_id, $pid))
		->fetchAll(PDO::FETCH_NUM);

	foreach ($media as $i=>$row) {
		if (displayDetailsById($row[0], 'OBJE') && !FactViewRestricted($row[0], $row[2])) {
			$level=0;
			$ct = preg_match("/(\d+) OBJE/", $row[3], $match);
			if ($ct>0) {
				$level = $match[1];
			}
			if (strstr($row[3], "_PRIM ")) {
				$thum = get_gedcom_value('_THUM', $level+1, $row[3]);
				$prim = get_gedcom_value('_PRIM', $level+1, $row[3]);
			} else {
				$thum = get_gedcom_value('_THUM', 1, $row[2]);
				$prim = get_gedcom_value('_PRIM', 1, $row[2]);
			}
			if ($prim=='N') continue;		// Skip _PRIM N objects
			if ($prim=='Y') {
				// Take the first _PRIM Y object
				$object["file"] = check_media_depth($row[1]);
				$object["thumb"] = thumbnail_file($row[1], true, false, $pid);
//				$object["_PRIM"] = $prim;	// Not sure whether this is needed.
				$object["_THUM"] = $thum;	// This overrides GEDCOM's "Use main image as thumbnail" option
				$object["level"] = $level;
				$object["mid"] = $row[0];
				break;		// Stop looking: we found a suitable image
			}
			if ($level==1 && empty($object)) {
				// Take the first level 1 object, but keep looking for an overriding _PRIM Y
				$object["file"] = check_media_depth($row[1]);
				$object["thumb"] = thumbnail_file($row[1], true, false, $pid);
//				$object["_PRIM"] = $prim;	// Not sure whether this is needed.
				$object["_THUM"] = $thum;	// This overrides GEDCOM's "Use main image as thumbnail" option
				$object["level"] = $level;
				$object["mid"] = $row[0];
			}
		}
	}
	return $object;
}

/**
 * Determine whether the main image or a thumbnail should be sent to the browser
 */
function thumb_or_main($object) {
	global $USE_THUMBS_MAIN;

	if ($object['_THUM']=='Y' || !$USE_THUMBS_MAIN) $file = 'file';
	else $file = 'thumb';

	// Here we should check whether the selected file actually exists
	return($object[$file]);
}

/**
 * get the full file path
 *
 * get the file path from a multimedia gedcom record
 * @param string $mediarec a OBJE subrecord
 * @return the fullpath from the FILE record
 */
function extract_fullpath($mediarec) {
	preg_match("/(\d) _*FILE (.*)/", $mediarec, $amatch);
	if (empty($amatch[2])) {
		return "";
	}
	$level = trim($amatch[1]);
	$fullpath = trim($amatch[2]);
	$filerec = get_sub_record($level, $amatch[0], $mediarec);
	$fullpath .= get_cont($level+1, $filerec);
	return $fullpath;
}

/**
 * get the relative filename for a media item
 *
 * gets the relative file path from the full media path for a media item.  checks the
 * <var>$MEDIA_DIRECTORY_LEVELS</var> to make sure the directory structure is maintained.
 * @param string $fullpath the full path from the media record
 * @return string a relative path that can be appended to the <var>$MEDIA_DIRECTORY</var> to reference the item
 */
function extract_filename($fullpath) {
	global $MEDIA_DIRECTORY_LEVELS, $MEDIA_DIRECTORY;

	$filename="";
	$regexp = "'[/\\\]'";
	$srch = "/".addcslashes($MEDIA_DIRECTORY, '/.')."/";
	$repl = "";
	if (!isFileExternal($fullpath)) {
		$nomedia = stripcslashes(preg_replace($srch, $repl, $fullpath));
	} else {
		$nomedia = $fullpath;
	}
	$ct = preg_match($regexp, $nomedia, $match);
	if ($ct>0) {
		$subelements = preg_split($regexp, $nomedia);
		$subelements = array_reverse($subelements);
		$max = $MEDIA_DIRECTORY_LEVELS;
		if ($max>=count($subelements)) {
			$max=count($subelements)-1;
		}
		for ($s=$max; $s>=0; $s--) {
			if ($s!=$max) {
				$filename = $filename."/".$subelements[$s];
			} else {
				$filename = $subelements[$s];
			}
		}
	} else {
		$filename = $nomedia;
	}
	return $filename;
}


// ************************************************* START OF SORTING FUNCTIONS ********************************* //
/**
 * Function to sort GEDCOM fact tags based on their tanslations
 */
function factsort($a, $b) {
	return utf8_strcasecmp(i18n::translate($a), i18n::translate($b));
}
/**
 * Function to sort place names array
 */
function placesort($a, $b) {
	return utf8_strcasecmp($a['place'], $b['place']);
}
////////////////////////////////////////////////////////////////////////////////
// Sort a list events for the today/upcoming blocks
////////////////////////////////////////////////////////////////////////////////
function event_sort($a, $b) {
	if ($a['jd']==$b['jd']) {
		if ($a['anniv']==$b['anniv']) {
			return utf8_strcasecmp($a['fact'], $b['fact']);
		}
		else {
			return utf8_strcasecmp($a['anniv'], $b['anniv']);
		}
	} else {
		return $a['jd']-$b['jd'];
	}
}

function event_sort_name($a, $b) {
	if ($a['jd']==$b['jd']) {
		return GedcomRecord::compare($a['record'], $b['record']);
	} else {
		return $a['jd']-$b['jd'];
	}
}

/**
 * sort an array of media items
 *
 */

function mediasort($a, $b) {
	$aKey = "";
	if (!empty($a["TITL"])) {
		$aKey = $a["TITL"];
	} else {
		if (!empty($a["titl"])) {
			$aKey = $a["titl"];
		} else {
			if (!empty($a["NAME"])) {
				$aKey = $a["NAME"];
			} else {
				if (!empty($a["name"])) {
					$aKey = $a["name"];
				} else {
					if (!empty($a["FILE"])) {
						$aKey = basename($a["FILE"]);
					} else {
						if (!empty($a["file"])) {
							$aKey = basename($a["file"]);
						}
					}
				}
			}
		}
	}

	$bKey = "";
	if (!empty($b["TITL"])) {
		$bKey = $b["TITL"];
	} else {
		if (!empty($b["titl"])) {
			$bKey = $b["titl"];
		} else {
			if (!empty($b["NAME"])) {
				$bKey = $b["NAME"];
			} else {
				if (!empty($b["name"])) {
					$bKey = $b["name"];
				} else {
					if (!empty($b["FILE"])) {
						$bKey = basename($b["FILE"]);
					} else {
						if (!empty($b["file"])) {
							$bKey = basename($b["file"]);
						}
					}
				}
			}
		}
	}
	return utf8_strcasecmp($aKey, $bKey, true);		// Case-insensitive compare
}
/**
 * sort an array according to the file name
 *
 */

function filesort($a, $b) {
	$aKey = "";
	if (!empty($a["FILE"])) {
		$aKey = basename($a["FILE"]);
	} else if (!empty($a["file"])) {
		$aKey = basename($a["file"]);
	}

	$bKey = "";
	if (!empty($b["FILE"])) {
		$bKey = basename($b["FILE"]);
	} else if (!empty($b["file"])) {
		$bKey = basename($b["file"]);
	}
	return utf8_strcasecmp($aKey, $bKey, true);		// Case-insensitive compare
}

// Helper function to sort facts.
function compare_facts_date($arec, $brec) {
	if (is_array($arec))
		$arec = $arec[1];
	if (is_array($brec))
		$brec = $brec[1];

	// If either fact is undated, the facts sort equally.
	if (!preg_match("/2 _?DATE (.*)/", $arec, $amatch) || !preg_match("/2 _?DATE (.*)/", $brec, $bmatch)) {
		if (preg_match('/2 _SORT (\d+)/', $arec, $match1) && preg_match('/2 _SORT (\d+)/', $brec, $match2)) {
			return $match1[1]-$match2[1];
		}
		return 0;
	}

	$adate = new GedcomDate($amatch[1]);
	$bdate = new GedcomDate($bmatch[1]);
	// If either date can't be parsed, don't sort.
	if (!$adate->isOK() || !$bdate->isOK()) {
		if (preg_match('/2 _SORT (\d+)/', $arec, $match1) && preg_match('/2 _SORT (\d+)/', $brec, $match2)) {
			return $match1[1]-$match2[1];
		}
		return 0;
	}

	// Remember that dates can be ranges and overlapping ranges sort equally.
	$amin=$adate->MinJD();
	$bmin=$bdate->MinJD();
	$amax=$adate->MaxJD();
	$bmax=$bdate->MaxJD();

	// BEF/AFT XXX sort as the day before/after XXX
	if ($adate->qual1=='BEF') {
		$amin=$amin-1;
		$amax=$amin;
	} elseif ($adate->qual1=='AFT') {
		$amax=$amax+1;
		$amin=$amax;
	}
	if ($bdate->qual1=='BEF') {
		$bmin=$bmin-1;
		$bmax=$bmin;
	} elseif ($bdate->qual1=='AFT') {
		$bmax=$bmax+1;
		$bmin=$bmax;
	}

	if ($amax<$bmin) {
		return -1;
	} else {
		if ($amin>$bmax) {
			return 1;
		} else {
			//-- ranged date... take the type of fact sorting into account
			$factWeight = 0;
			if (preg_match('/2 _SORT (\d+)/', $arec, $match1) && preg_match('/2 _SORT (\d+)/', $brec, $match2)) {
				$factWeight = $match1[1]-$match2[1];
			}
			//-- fact is prefered to come before, so compare using the minimum ranges
			if ($factWeight < 0 && $amin!=$bmin) {
				return ($amin-$bmin);
			} else {
				if ($factWeight > 0 && $bmax!=$amax) {
					//-- fact is prefered to come after, so compare using the max of the ranges
					return ($bmax-$amax);
				} else {
					//-- facts are the same or the ranges don't give enough info, so use the average of the range
					$aavg = ($amin+$amax)/2;
					$bavg = ($bmin+$bmax)/2;
					if ($aavg<$bavg) {
						return -1;
					} else {
						if ($aavg>$bavg) {
							return 1;
						} else {
							return $factWeight;
						}
					}
				}
			}
			return 0;
		}
	}
}

/**
 * A multi-key sort
 * 1. First divide the facts into two arrays one set with dates and one set without dates
 * 2. Sort each of the two new arrays, the date using the compare date function, the non-dated
 * using the compare type function
 * 3. Then merge the arrays back into the original array using the compare type function
 *
 * @param unknown_type $arr
 */
function sort_facts(&$arr) {
	$dated = array();
	$nondated = array();
	//-- split the array into dated and non-dated arrays
	$order = 0;
	foreach($arr as $event) {
		$event->sortOrder = $order;
		$order++;
		if ($event->getValue("DATE")==NULL || !$event->getDate()->isOk()) $nondated[] = $event;
		else $dated[] = $event;
	}

	//-- sort each type of array
	usort($dated, array("Event", "CompareDate"));
	usort($nondated, array("Event", "CompareType"));

	//-- merge the arrays back together comparing by Facts
	$dc = count($dated);
	$nc = count($nondated);
	$i = 0;
	$j = 0;
	$k = 0;
	// while there is anything in the dated array continue merging
	while($i<$dc) {
		// compare each fact by type to merge them in order
		if ($j<$nc && Event::CompareType($dated[$i], $nondated[$j])>0) {
			$arr[$k] = $nondated[$j];
			$j++;
		}
		else {
			$arr[$k] = $dated[$i];
			$i++;
		}
		$k++;
	}

	// get anything that might be left in the nondated array
	while($j<$nc) {
		$arr[$k] = $nondated[$j];
		$j++;
		$k++;
	}

}

function gedcomsort($a, $b) {
	return utf8_strcasecmp($a["title"], $b["title"]);
}

// ************************************************* START OF MISCELLANIOUS FUNCTIONS ********************************* //
/**
 * Get relationship between two individuals in the gedcom
 *
 * function to calculate the relationship between two people it uses hueristics based on the
 * individuals birthdate to try and calculate the shortest path between the two individuals
 * it uses a node cache to help speed up calculations when using relationship privacy
 * this cache is indexed using the string "$pid1-$pid2"
 * @param string $pid1 the ID of the first person to compute the relationship from
 * @param string $pid2 the ID of the second person to compute the relatiohip to
 * @param bool $followspouse whether to add spouses to the path
 * @param int $maxlenght the maximim length of path
 * @param bool $ignore_cache enable or disable the relationship cache
 * @param int $path_to_find which path in the relationship to find, 0 is the shortest path, 1 is the next shortest path, etc
 */
function get_relationship($pid1, $pid2, $followspouse=true, $maxlength=0, $ignore_cache=false, $path_to_find=0) {
	global $start_time, $NODE_CACHE, $NODE_CACHE_LENGTH, $USE_RELATIONSHIP_PRIVACY, $pgv_changes;

	$time_limit=get_site_setting('MAX_EXECUTION_TIME');
	if (isset($pgv_changes[$pid2."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
		$indirec = find_updated_record($pid2, WT_GED_ID);
	else
		$indirec = find_person_record($pid2, WT_GED_ID);
	//-- check the cache
	if ($USE_RELATIONSHIP_PRIVACY && !$ignore_cache) {
		if (isset($NODE_CACHE["$pid1-$pid2"])) {
			if ($NODE_CACHE["$pid1-$pid2"]=="NOT FOUND") return false;
			if (($maxlength==0)||(count($NODE_CACHE["$pid1-$pid2"]["path"])-1<=$maxlength))
				return $NODE_CACHE["$pid1-$pid2"];
			else
				return false;
		}
		//-- check the cache for person 2's children
		$famids = array();
		$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
		for ($i=0; $i<$ct; $i++) {
			$famids[$i]=$match[$i][1];
		}
		foreach ($famids as $indexval => $fam) {
			if (isset($pgv_changes[$fam."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
				$famrec = find_updated_record($fam, WT_GED_ID);
			else
				$famrec = find_family_record($fam, WT_GED_ID);
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				$child = $match[$i][1];
				if (!empty($child)) {
					if (isset($NODE_CACHE["$pid1-$child"])) {
						if (($maxlength==0)||(count($NODE_CACHE["$pid1-$child"]["path"])+1<=$maxlength)) {
							$node1 = $NODE_CACHE["$pid1-$child"];
							if ($node1!="NOT FOUND") {
								$node1["path"][] = $pid2;
								$node1["pid"] = $pid2;
								if (strpos($indirec, "1 SEX F")!==false)
									$node1["relations"][] = "mother";
								else
									$node1["relations"][] = "father";
							}
							$NODE_CACHE["$pid1-$pid2"] = $node1;
							if ($node1=="NOT FOUND")
								return false;
							return $node1;
						} else
							return false;
					}
				}
			}
		}

		if ((!empty($NODE_CACHE_LENGTH))&&($maxlength>0)) {
			if ($NODE_CACHE_LENGTH>=$maxlength)
				return false;
		}
	}
	//-- end cache checking

	//-- get the birth year of p2 for calculating heuristics
	$birthrec = get_sub_record(1, "1 BIRT", $indirec);
	$byear2 = -1;
	if ($birthrec!==false) {
		$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
		if ($dct>0)
			$byear2 = $match[1];
	}
	if ($byear2==-1) {
		$numfams = preg_match_all("/1 FAMS @(.*)@/", $indirec, $fmatch, PREG_SET_ORDER);
		for ($j=0; $j<$numfams; $j++) {
			// Get the family record
			if (isset($pgv_changes[$fmatch[$j][1]."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
				$famrec = find_updated_record($fmatch[$j][1], WT_GED_ID);
			else
				$famrec = find_family_record($fmatch[$j][1], WT_GED_ID);

			// Get the set of children
			$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $cmatch, PREG_SET_ORDER);
			for ($i=0; $i<$ct; $i++) {
				// Get each child's record
				if (isset($pgv_changes[$cmatch[$i][1]."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
					$childrec = find_updated_record($cmatch[$i][1], WT_GED_ID);
				else
					$childrec = find_person_record($cmatch[$i][1], WT_GED_ID);
				$birthrec = get_sub_record(1, "1 BIRT", $childrec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $bmatch);
					if ($dct>0)
						$byear2 = $bmatch[1]-25;
						if ($byear2>2100) $byear2-=3760; // Crude conversion from jewish to gregorian
				}
			}
		}
	}
	//-- end of approximating birth year

	//-- current path nodes
	$p1nodes = array();
	//-- ids visited
	$visited = array();

	//-- set up first node for person1
	$node1 = array();
	$node1["path"] = array();
	$node1["path"][] = $pid1;
	$node1["length"] = 0;
	$node1["pid"] = $pid1;
	$node1["relations"] = array();
	$node1["relations"][] = "self";
	$p1nodes[] = $node1;

	$visited[$pid1] = true;

	$found = false;
	$count=0;
	while (!$found) {
		//-- the following 2 lines ensure that the user can abort a long relationship calculation
		//-- refer to http://www.php.net/manual/en/features.connection-handling.php for more
		//-- information about why these lines are included
		if (headers_sent()) {
			print " ";
			if ($count%100 == 0)
				flush();
		}
		$count++;
		$end_time = microtime(true);
		$exectime = $end_time - $start_time;
		if (($time_limit>1)&&($exectime > $time_limit-1)) {
			echo "<span class=\"error\">", i18n::translate('The script timed out before a relationship could be found.'), "</span>\n";
			return false;
		}
		if (count($p1nodes)==0) {
			if ($maxlength!=0) {
				if (!isset($NODE_CACHE_LENGTH)) {
					$NODE_CACHE_LENGTH = $maxlength;
				} elseif ($NODE_CACHE_LENGTH<$maxlength) {
					$NODE_CACHE_LENGTH = $maxlength;
				}
			}
			if (headers_sent()) {
				//print "\n<!-- Relationship $pid1-$pid2 NOT FOUND | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
				//echo execution_stats();
				//print "-->\n";
			}
			$NODE_CACHE["$pid1-$pid2"] = "NOT FOUND";
			return false;
		}
		//-- search the node list for the shortest path length
		$shortest = -1;
		foreach ($p1nodes as $index=>$node) {
			if ($shortest == -1) {
				$shortest = $index;
			} else {
				$node1 = $p1nodes[$shortest];
				if ($node1["length"] > $node["length"]) {
					$shortest = $index;
				}
			}
		}
		if ($shortest==-1)
			return false;
		$node = $p1nodes[$shortest];
		if (($maxlength==0)||(count($node["path"])<=$maxlength)) {
			if ($node["pid"]==$pid2) {
			} else {
				//-- hueristic values
				$fatherh = 1;
				$motherh = 1;
				$siblingh = 2;
				$spouseh = 2;
				$childh = 3;

				//-- generate heuristic values based of the birthdates of the current node and p2
				if (isset($pgv_changes[$node["pid"]."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
					$indirec = find_updated_record($node["pid"], WT_GED_ID);
				else
					$indirec = find_person_record($node["pid"], WT_GED_ID);
				$byear1 = -1;
				$birthrec = get_sub_record(1, "1 BIRT", $indirec);
				if ($birthrec!==false) {
					$dct = preg_match("/2 DATE .*(\d\d\d\d)/", $birthrec, $match);
					if ($dct>0)
						$byear1 = $match[1];
						if ($byear1>2100) $byear1-=3760; // Crude conversion from jewish to gregorian
				}
				if (($byear1!=-1)&&($byear2!=-1)) {
					$yeardiff = $byear1-$byear2;
					if ($yeardiff < -140) {
						$fatherh = 20;
						$motherh = 20;
						$siblingh = 15;
						$spouseh = 15;
						$childh = 1;
					} else
						if ($yeardiff < -100) {
							$fatherh = 15;
							$motherh = 15;
							$siblingh = 10;
							$spouseh = 10;
							$childh = 1;
						} else
							if ($yeardiff < -60) {
								$fatherh = 10;
								$motherh = 10;
								$siblingh = 5;
								$spouseh = 5;
								$childh = 1;
							} else
								if ($yeardiff < -20) {
									$fatherh = 5;
									$motherh = 5;
									$siblingh = 3;
									$spouseh = 3;
									$childh = 1;
								} else
									if ($yeardiff<20) {
										$fatherh = 3;
										$motherh = 3;
										$siblingh = 1;
										$spouseh = 1;
										$childh = 5;
									} else
										if ($yeardiff<60) {
											$fatherh = 1;
											$motherh = 1;
											$siblingh = 5;
											$spouseh = 2;
											$childh = 10;
										} else
											if ($yeardiff<100) {
												$fatherh = 1;
												$motherh = 1;
												$siblingh = 10;
												$spouseh = 3;
												$childh = 15;
											} else {
												$fatherh = 1;
												$motherh = 1;
												$siblingh = 15;
												$spouseh = 4;
												$childh = 20;
											}
				}
				//-- check all parents and siblings of this node
				$famids = array();
				$ct = preg_match_all("/1 FAMC @(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for ($i=0; $i<$ct; $i++) {
					if (!isset($visited[$match[$i][1]]))
						$famids[$i]=$match[$i][1];
				}
				foreach ($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
						$famrec = find_updated_record($fam, WT_GED_ID);
					else
						$famrec = find_family_record($fam, WT_GED_ID);
					$parents = find_parents_in_record($famrec);
					if ((!empty($parents["HUSB"]))&&(!isset($visited[$parents["HUSB"]]))) {
						$node1 = $node;
						$node1["length"]+=$fatherh;
						$node1["path"][] = $parents["HUSB"];
						$node1["pid"] = $parents["HUSB"];
						$node1["relations"][] = "parent";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0)
								$path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						} else
							$visited[$parents["HUSB"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					if ((!empty($parents["WIFE"]))&&(!isset($visited[$parents["WIFE"]]))) {
						$node1 = $node;
						$node1["length"]+=$motherh;
						$node1["path"][] = $parents["WIFE"];
						$node1["pid"] = $parents["WIFE"];
						$node1["relations"][] = "parent";
						$p1nodes[] = $node1;
						if ($node1["pid"]==$pid2) {
							if ($path_to_find>0)
								$path_to_find--;
							else {
								$found=true;
								$resnode = $node1;
							}
						} else
							$visited[$parents["WIFE"]] = true;
						if ($USE_RELATIONSHIP_PRIVACY) {
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for ($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$siblingh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "sibling";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else
								$visited[$child] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
				//-- check all spouses and children of this node
				$famids = array();
				$ct = preg_match_all("/1 FAMS @(.*)@/", $indirec, $match, PREG_SET_ORDER);
				for ($i=0; $i<$ct; $i++) {
					$famids[$i]=$match[$i][1];
				}
				foreach ($famids as $indexval => $fam) {
					$visited[$fam] = true;
					if (isset($pgv_changes[$fam."_".WT_GEDCOM]) && WT_USER_CAN_EDIT)
						$famrec = find_updated_record($fam, WT_GED_ID);
					else
						$famrec = find_family_record($fam, WT_GED_ID);
					if ($followspouse) {
						$parents = find_parents_in_record($famrec);
						if ((!empty($parents["HUSB"]))&&((!in_arrayr($parents["HUSB"], $node1))||(!isset($visited[$parents["HUSB"]])))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["HUSB"];
							$node1["pid"] = $parents["HUSB"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else
								$visited[$parents["HUSB"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
						if ((!empty($parents["WIFE"]))&&((!in_arrayr($parents["WIFE"], $node1))||(!isset($visited[$parents["WIFE"]])))) {
							$node1 = $node;
							$node1["length"]+=$spouseh;
							$node1["path"][] = $parents["WIFE"];
							$node1["pid"] = $parents["WIFE"];
							$node1["relations"][] = "spouse";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else
								$visited[$parents["WIFE"]] = true;
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
					$ct = preg_match_all("/1 CHIL @(.*)@/", $famrec, $match, PREG_SET_ORDER);
					for ($i=0; $i<$ct; $i++) {
						$child = $match[$i][1];
						if ((!empty($child))&&(!isset($visited[$child]))) {
							$node1 = $node;
							$node1["length"]+=$childh;
							$node1["path"][] = $child;
							$node1["pid"] = $child;
							$node1["relations"][] = "child";
							$p1nodes[] = $node1;
							if ($node1["pid"]==$pid2) {
								if ($path_to_find>0)
									$path_to_find--;
								else {
									$found=true;
									$resnode = $node1;
								}
							} else {
								$visited[$child] = true;
							}
							if ($USE_RELATIONSHIP_PRIVACY) {
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
				}
			}
		}
		unset($p1nodes[$shortest]);
	} //-- end while loop
	if (headers_sent()) {
		//echo "\n<!-- Relationship $pid1-$pid2 | Visited ".count($visited)." nodes | Required $count iterations.<br />\n";
		//echo execution_stats();
		//echo "-->\n";
	}
	
	// Convert "generic" relationships into sex-specific ones.
	foreach ($resnode['path'] as $n=>$pid) {
		switch ($resnode['relations'][$n]) {
		case 'parent':
			switch (Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='father'; break;
			case 'F': $resnode['relations'][$n]='mother'; break;
			}
			break;
		case 'child':
			switch (Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='son'; break;
			case 'F': $resnode['relations'][$n]='daughter'; break;
			}
			break;
		case 'spouse':
			switch (Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='husband'; break;
			case 'F': $resnode['relations'][$n]='wife'; break;
			}
			break;
		case 'sibling':
			switch (Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='brother'; break;
			case 'F': $resnode['relations'][$n]='sister'; break;
			}
			break;
		}
	}
	return $resnode;
}

// Convert the result of get_relationship() into a relationship name.
function get_relationship_name($nodes) {
	if (!is_array($nodes)) {
		return '';
	}
	$pid1=$nodes['path'][0];
	$pid2=$nodes['path'][count($nodes['path'])-1];
	$path=array_slice($nodes['relations'], 1);
	// Look for paths with *specific* names first.
	// Note that every combination must be listed separately, as the same english
	// name can be used for many different relationships.  e.g.
	// brother's wife & husband's sister = sister-in-law.
	//
	// For this reason, we need to use a "generic" english relationships,
	// which will need translating into specific english relationships, even
	// for english.
	//
	// $path is an array of the 12 possible gedcom family relationships:
	// mother/father/parent
	// brother/sister/sibling
	// husband/wife/spouse
	// son/daughter/child
	//
	// This is always the shortest path, so "father, daughter" is "half-sister", not "sister".
	//
	// This is very repetitive in english, but necessary in order to handle the
	// complexities of other languages.
	//
	// TODO: handle unmarried partners, so need male-partner, female-partner, unknown-partner

	// Make each relationship parts the same length, for simpler matching.
	$combined_path='';
	foreach ($path as $rel) {
		$combined_path.=substr($rel, 0, 3);
	}

	return get_relationship_name_from_path($combined_path, $pid1, $pid2);
}

function get_relationship_name_from_path($path, $pid1, $pid2) {
	if (!preg_match('/^(mot|fat|par|hus|wif|spo|son|dau|chi|bro|sis|sib)*$/', $path)) {
		// TODO: Update all the "3 RELA " values in class_person
		return '<span class="error">'.$path.'</span>';
	}

	switch ($path) {
	case '': return i18n::translate('self');
	
		//  Level One relationships
	case 'mot': return i18n::translate('mother');
	case 'fat': return i18n::translate('father');
	case 'par': return i18n::translate('parent');
	case 'hus': return i18n::translate('husband');
	case 'wif': return i18n::translate('wife');
	case 'spo': return i18n::translate('spouse');
	case 'son': return i18n::translate('son');
	case 'dau': return i18n::translate('daughter');
	case 'chi': return i18n::translate('child');
	case 'bro':
		$dob1=Person::GetInstance($pid1)->getBirthDate();
		$dob2=Person::GetInstance($pid2)->getBirthDate();
		if ($dob1->isOK() && $dob2->isOK()) {
			if (abs($dob1->JD()-$dob2->JD())<2) {
				return i18n::translate('twin brother');
			} else if ($dob1->JD()<$dob2->JD()) {
				return i18n::translate('younger brother');
			} else {
				return i18n::translate('elder brother');
			}
		}
		return i18n::translate('brother');
	case 'sis':
		$dob1=Person::GetInstance($pid1)->getBirthDate();
		$dob2=Person::GetInstance($pid2)->getBirthDate();
		if ($dob1->isOK() && $dob2->isOK()) {
			if (abs($dob1->JD()-$dob2->JD())<2) {
				return i18n::translate('twin sister');
			} else if ($dob1->JD()<$dob2->JD()) {
				return i18n::translate('younger sister');
			} else {
				return i18n::translate('elder sister');
			}
		}
		return i18n::translate('sister');
	case 'sib':
		$dob1=Person::GetInstance($pid1)->getBirthDate();
		$dob2=Person::GetInstance($pid2)->getBirthDate();
		if ($dob1->isOK() && $dob2->isOK()) {
			if (abs($dob1->JD()-$dob2->JD())<2) {
				return i18n::translate('twin sibling');
			} else if ($dob1->JD()<$dob2->JD()) {
				return i18n::translate('younger sibling');
			} else {
				return i18n::translate('elder sibling');
			}
		}
		return i18n::translate('sibling');
	
	// Level Two relationships
	case 'motmot': return i18n::translate_c('mother\'s mother', 'grandmother');
	case 'motfat': return i18n::translate_c('mother\'s father', 'grandfather');
	case 'motpar': return i18n::translate_c('mother\'s parent', 'grandparent');
	case 'fatmot': return i18n::translate_c('father\'s mother', 'grandmother');
	case 'fatfat': return i18n::translate_c('father\'s father', 'grandfather');
	case 'fatpar': return i18n::translate_c('father\'s parent', 'grandparent');
	case 'parmot': return i18n::translate_c('parent\'s mother', 'grandmother');
	case 'parfat': return i18n::translate_c('parent\'s father', 'grandfather');
	case 'parpar': return i18n::translate_c('parent\'s parent', 'grandparent');

	case 'daudau': return i18n::translate_c('daughter\'s daughter', 'granddaughter');
	case 'dauson': return i18n::translate_c('daughter\'s son', 'grandson');
	case 'dauchi': return i18n::translate_c('daughter\'s child', 'grandchild');
	case 'sondau': return i18n::translate_c('son\'s daughter', 'granddaughter');
	case 'sonson': return i18n::translate_c('son\'s son', 'grandson');
	case 'sonchi': return i18n::translate_c('son\'s child', 'grandchild');
	case 'chidau': return i18n::translate_c('child\'s daughter', 'granddaughter');
	case 'chison': return i18n::translate_c('child\'s son', 'grandson');
	case 'chichi': return i18n::translate_c('child\'s child', 'grandchild');

	case 'mothus': return i18n::translate_c('mother\'s husband', 'step-father');
	case 'mothus': return i18n::translate_c('mother\'s husband', 'step-father');
	case 'fatwif': return i18n::translate_c('father\'s wife', 'step-mother');
	case 'fatwif': return i18n::translate_c('father\'s wife', 'step-mother');
	case 'parspo': return i18n::translate_c('parent\'s spouse', 'step-parent');
	case 'parspo': return i18n::translate_c('parent\'s spouse', 'step-parent');

	case 'motson': return i18n::translate_c('mother\'s son', 'half-brother');
	case 'motdau': return i18n::translate_c('mother\'s daughter', 'half-sister');
	case 'motchi': return i18n::translate_c('mother\'s child', 'half-sibling');
	case 'fatson': return i18n::translate_c('father\'s son', 'half-brother');
	case 'fatdau': return i18n::translate_c('father\'s daughter', 'half-sister');
	case 'fatchi': return i18n::translate_c('father\'s child', 'half-sibling');
	case 'parson': return i18n::translate_c('parent\'s son', 'half-brother');
	case 'pardau': return i18n::translate_c('parent\'s daughter', 'half-sister');
	case 'parchi': return i18n::translate_c('parent\'s child', 'half-sibling');

	case 'motsis': return i18n::translate_c('mother\'s sister', 'aunt');
	case 'motbro': return i18n::translate_c('mother\'s brother', 'uncle');
	case 'motsib': return i18n::translate_c('mother\'s sibling', 'aunt/uncle');
	case 'fatsis': return i18n::translate_c('father\'s sister', 'aunt');
	case 'fatbro': return i18n::translate_c('father\'s brother', 'uncle');
	case 'fatsib': return i18n::translate_c('father\'s sibling', 'aunt/uncle');
	case 'parsis': return i18n::translate_c('parent\'s sister', 'aunt');
	case 'parbro': return i18n::translate_c('parent\'s brother', 'uncle');
	case 'parsib': return i18n::translate_c('parent\'s sibling', 'aunt/uncle');

	case 'broson': return i18n::translate_c('brother\'s son', 'nephew');
	case 'brodau': return i18n::translate_c('brother\'s daughter', 'niece');
	case 'brochi': return i18n::translate_c('brother\'s child', 'nephew/neice');
	case 'sisson': return i18n::translate_c('sister\'s son', 'nephew');
	case 'sisdau': return i18n::translate_c('sister\'s daughter', 'niece');
	case 'sischi': return i18n::translate_c('sister\'s child', 'nephew/neice');
	case 'sibson': return i18n::translate_c('sibling\'s son', 'nephew');
	case 'sibdau': return i18n::translate_c('sibling\'s daughter', 'niece');
	case 'sibchi': return i18n::translate_c('sibling\'s child', 'nephew/neice');

	case 'wifsis': return i18n::translate_c('wife\'s sister', 'sister-in-law');
	case 'hussis': return i18n::translate_c('husband\'s sister', 'sister-in-law');
	case 'sposis': return i18n::translate_c('spouses\'s sister', 'sister-in-law');
	case 'wifbro': return i18n::translate_c('wife\'s brother', 'brother-in-law');
	case 'husbro': return i18n::translate_c('husband\'s brother', 'brother-in-law');
	case 'spobro': return i18n::translate_c('spouses\'s brother', 'brother-in-law');

	case 'browif': return i18n::translate_c('brother\'s wife', 'sister-in-law');
	case 'sishus': return i18n::translate_c('sister\'s husband', 'brother-in-law');

	case 'husmot': return i18n::translate_c('husband\'s mother', 'mother-in-law');
	case 'wifmot': return i18n::translate_c('wife\'s mother', 'mother-in-law');
	case 'spomot': return i18n::translate_c('spouses\'s mother', 'mother-in-law');
	case 'husfat': return i18n::translate_c('husband\'s father', 'father-in-law');
	case 'wiffat': return i18n::translate_c('wife\'s father', 'father-in-law');
	case 'spofat': return i18n::translate_c('spouses\'s father', 'father-in-law');

	case 'sonwif': return i18n::translate_c('son\'s wife', 'daughter-in-law');
	case 'dauhus': return i18n::translate_c('daughter\'s husband', 'son-in-law');

	case 'husson': return i18n::translate_c('husband\'s son', 'step-son');
	case 'wifson': return i18n::translate_c('wife\'s son', 'step-son');
	case 'sposon': return i18n::translate_c('spouses\'s son', 'step-son');
	case 'husdau': return i18n::translate_c('husband\'s daughter', 'step-daughter');
	case 'wifdau': return i18n::translate_c('wife\'s daughter', 'step-daughter');
	case 'spodau': return i18n::translate_c('spouses\'s daughter', 'step-daughter');
	case 'huschi': return i18n::translate_c('husband\'s child', 'step-child');
	case 'wifchi': return i18n::translate_c('wife\'s child', 'step-child');
	case 'spochi': return i18n::translate_c('spouses\'s child', 'step-child');

	// Level Three relationships
	case 'motmotmot': return i18n::translate_c('mother\'s mother\'s mother', 'great-grandmother');
	case 'motmotfat': return i18n::translate_c('mother\'s mother\'s father', 'great-grandfather');
	case 'motmotpar': return i18n::translate_c('mother\'s mother\'s parent', 'great-grandparent');
	case 'motfatmot': return i18n::translate_c('mother\'s father\'s mother', 'great-grandmother');
	case 'motfatfat': return i18n::translate_c('mother\'s father\'s father', 'great-grandfather');
	case 'motfatpar': return i18n::translate_c('mother\'s father\'s parent', 'great-grandparent');
	case 'motparmot': return i18n::translate_c('mother\'s parent\'s mother', 'great-grandmother');
	case 'motparfat': return i18n::translate_c('mother\'s parent\'s father', 'great-grandfather');
	case 'motparpar': return i18n::translate_c('mother\'s parent\'s parent', 'great-grandparent');
	case 'fatmotmot': return i18n::translate_c('father\'s mother\'s mother', 'great-grandmother');
	case 'fatmotfat': return i18n::translate_c('father\'s mother\'s father', 'great-grandfather');
	case 'fatmotpar': return i18n::translate_c('father\'s mother\'s parent', 'great-grandparent');
	case 'fatfatmot': return i18n::translate_c('father\'s father\'s mother', 'great-grandmother');
	case 'fatfatfat': return i18n::translate_c('father\'s father\'s father', 'great-grandfather');
	case 'fatfatpar': return i18n::translate_c('father\'s father\'s parent', 'great-grandparent');
	case 'fatparmot': return i18n::translate_c('father\'s parent\'s mother', 'great-grandmother');
	case 'fatparfat': return i18n::translate_c('father\'s parent\'s father', 'great-grandfather');
	case 'fatparpar': return i18n::translate_c('father\'s parent\'s parent', 'great-grandparent');
	case 'parmotmot': return i18n::translate_c('parent\'s mother\'s mother', 'great-grandmother');
	case 'parmotfat': return i18n::translate_c('parent\'s mother\'s father', 'great-grandfather');
	case 'parmotpar': return i18n::translate_c('parent\'s mother\'s parent', 'great-grandparent');
	case 'parfatmot': return i18n::translate_c('parent\'s father\'s mother', 'great-grandmother');
	case 'parfatfat': return i18n::translate_c('parent\'s father\'s father', 'great-grandfather');
	case 'parfatpar': return i18n::translate_c('parent\'s father\'s parent', 'great-grandparent');
	case 'parparmot': return i18n::translate_c('parent\'s parent\'s mother', 'great-grandmother');
	case 'parparfat': return i18n::translate_c('parent\'s parent\'s father', 'great-grandfather');
	case 'parparpar': return i18n::translate_c('parent\'s parent\'s parent', 'great-grandparent');

	case 'motmotsis': return i18n::translate_c('mother\'s mother\'s sister', 'great-aunt');
	case 'motmotbro': return i18n::translate_c('mother\'s mother\'s brother', 'great-uncle');
	case 'motmotsib': return i18n::translate_c('mother\'s mother\'s sibling', 'great-aunt/uncle');
	case 'motfatsis': return i18n::translate_c('mother\'s father\'s sister', 'great-aunt');
	case 'motfatbro': return i18n::translate_c('mother\'s father\'s brother', 'great-uncle');
	case 'motfatsib': return i18n::translate_c('mother\'s father\'s sibling', 'great-aunt/uncle');
	case 'motparsis': return i18n::translate_c('mother\'s parent\'s sister', 'great-aunt');
	case 'motparbro': return i18n::translate_c('mother\'s parent\'s brother', 'great-uncle');
	case 'motparsib': return i18n::translate_c('mother\'s parent\'s sibling', 'great-aunt/uncle');
	case 'fatmotsis': return i18n::translate_c('father\'s mother\'s sister', 'great-aunt');
	case 'fatmotbro': return i18n::translate_c('father\'s mother\'s brother', 'great-uncle');
	case 'fatmotsib': return i18n::translate_c('father\'s mother\'s sibling', 'great-aunt/uncle');
	case 'fatfatsis': return i18n::translate_c('father\'s father\'s sister', 'great-aunt');
	case 'fatfatbro': return i18n::translate_c('father\'s father\'s brother', 'great-uncle');
	case 'fatfatsib': return i18n::translate_c('father\'s father\'s sibling', 'great-aunt/uncle');
	case 'fatparsis': return i18n::translate_c('father\'s parent\'s sister', 'great-aunt');
	case 'fatparbro': return i18n::translate_c('father\'s parent\'s brother', 'great-uncle');
	case 'fatparsib': return i18n::translate_c('father\'s parent\'s sibling', 'great-aunt/uncle');
	case 'parmotsis': return i18n::translate_c('parent\'s mother\'s sister', 'great-aunt');
	case 'parmotbro': return i18n::translate_c('parent\'s mother\'s brother', 'great-uncle');
	case 'parmotsib': return i18n::translate_c('parent\'s mother\'s sibling', 'great-aunt/uncle');
	case 'parfatsis': return i18n::translate_c('parent\'s father\'s sister', 'great-aunt');
	case 'parfatbro': return i18n::translate_c('parent\'s father\'s brother', 'great-uncle');
	case 'parfatsib': return i18n::translate_c('parent\'s father\'s sibling', 'great-aunt/uncle');
	case 'parparsis': return i18n::translate_c('parent\'s parent\'s sister', 'great-aunt');
	case 'parparbro': return i18n::translate_c('parent\'s parent\'s brother', 'great-uncle');
	case 'parparsib': return i18n::translate_c('parent\'s parent\'s sibling', 'great-aunt/uncle');

	case 'daudaudau': return i18n::translate_c('daughter\'s daughter\'s daughter', 'great-granddaughter');
	case 'daudauson': return i18n::translate_c('daughter\'s daughter\'s son', 'great-grandson');
	case 'daudauchi': return i18n::translate_c('daughter\'s daughter\'s child', 'great-grandchild');
	case 'dausondau': return i18n::translate_c('daughter\'s son\'s daughter', 'great-granddaughter');
	case 'dausonson': return i18n::translate_c('daughter\'s son\'s son', 'great-grandson');
	case 'dausonchi': return i18n::translate_c('daughter\'s son\'s child', 'great-grandchild');
	case 'dauchidau': return i18n::translate_c('daughter\'s child\'s daughter', 'great-granddaughter');
	case 'dauchison': return i18n::translate_c('daughter\'s child\'s son', 'great-grandson');
	case 'dauchichi': return i18n::translate_c('daughter\'s child\'s child', 'great-grandchild');
	case 'sondaudau': return i18n::translate_c('son\'s daughter\'s daughter', 'great-granddaughter');
	case 'sondauson': return i18n::translate_c('son\'s daughter\'s son', 'great-grandson');
	case 'sondauchi': return i18n::translate_c('son\'s daughter\'s child', 'great-grandchild');
	case 'sonsondau': return i18n::translate_c('son\'s son\'s daughter', 'great-granddaughter');
	case 'sonsonson': return i18n::translate_c('son\'s son\'s son', 'great-grandson');
	case 'sonsonchi': return i18n::translate_c('son\'s son\'s child', 'great-grandchild');
	case 'sonchidau': return i18n::translate_c('son\'s child\'s daughter', 'great-granddaughter');
	case 'sonchison': return i18n::translate_c('son\'s child\'s son', 'great-grandson');
	case 'sonchichi': return i18n::translate_c('son\'s child\'s child', 'great-grandchild');
	case 'chidaudau': return i18n::translate_c('child\'s daughter\'s daughter', 'great-granddaughter');
	case 'chidauson': return i18n::translate_c('child\'s daughter\'s son', 'great-grandson');
	case 'chidauchi': return i18n::translate_c('child\'s daughter\'s child', 'great-grandchild');
	case 'chisondau': return i18n::translate_c('child\'s son\'s daughter', 'great-granddaughter');
	case 'chisonson': return i18n::translate_c('child\'s son\'s son', 'great-grandson');
	case 'chisonchi': return i18n::translate_c('child\'s son\'s child', 'great-grandchild');
	case 'chichidau': return i18n::translate_c('child\'s child\'s daughter', 'great-granddaughter');
	case 'chichison': return i18n::translate_c('child\'s child\'s son', 'great-grandson');
	case 'chichichi': return i18n::translate_c('child\'s child\'s child', 'great-grandchild');

	case 'mothusson': return i18n::translate_c('mother\'s husband\'s son', 'step-brother');
	case 'mothusdau': return i18n::translate_c('mother\'s husband\'s daughter', 'step-sister');
	case 'mothuschi': return i18n::translate_c('mother\'s husband\'s child', 'step-sibling');
	case 'fatwifson': return i18n::translate_c('father\'s wife\'s son', 'step-brother');
	case 'fatwifdau': return i18n::translate_c('father\'s wife\'s daughter', 'step-sister');
	case 'fatwifchi': return i18n::translate_c('father\'s wife\'s child', 'step-sibling');
	case 'parsposon': return i18n::translate_c('parent\'s spouse\'s son', 'step-brother');
	case 'parspodau': return i18n::translate_c('parent\'s spouse\'s daughter', 'step-sister');
	case 'parspochi': return i18n::translate_c('parent\'s spouse\'s child', 'step-sibling');

	case 'motmotsis': return i18n::translate_c('mother\'s mother\'s sister', 'great-aunt');
	case 'motmotbro': return i18n::translate_c('mother\'s mother\'s brother', 'great-uncle');
	case 'motmotsib': return i18n::translate_c('mother\'s mother\'s sibling', 'great-aunt/uncle');
	case 'motfatsis': return i18n::translate_c('mother\'s father\'s sister', 'great-aunt');
	case 'motfatbro': return i18n::translate_c('mother\'s father\'s brother', 'great-uncle');
	case 'motfatsib': return i18n::translate_c('mother\'s father\'s sibling', 'great-aunt/uncle');
	case 'motparsis': return i18n::translate_c('mother\'s parent\'s sister', 'great-aunt');
	case 'motparbro': return i18n::translate_c('mother\'s parent\'s brother', 'great-uncle');
	case 'motparsib': return i18n::translate_c('mother\'s parent\'s sibling', 'great-aunt/uncle');
	case 'fatmotsis': return i18n::translate_c('father\'s mother\'s sister', 'great-aunt');
	case 'fatmotbro': return i18n::translate_c('father\'s mother\'s brother', 'great-uncle');
	case 'fatmotsib': return i18n::translate_c('father\'s mother\'s sibling', 'great-aunt/uncle');
	case 'fatfatsis': return i18n::translate_c('father\'s father\'s sister', 'great-aunt');
	case 'fatfatbro': return i18n::translate_c('father\'s father\'s brother', 'great-uncle');
	case 'fatfatsib': return i18n::translate_c('father\'s father\'s sibling', 'great-aunt/uncle');
	case 'fatparsis': return i18n::translate_c('father\'s parent\'s sister', 'great-aunt');
	case 'fatparbro': return i18n::translate_c('father\'s parent\'s brother', 'great-uncle');
	case 'fatparsib': return i18n::translate_c('father\'s parent\'s sibling', 'great-aunt/uncle');
	case 'parmotsis': return i18n::translate_c('parent\'s mother\'s sister', 'great-aunt');
	case 'parmotbro': return i18n::translate_c('parent\'s mother\'s brother', 'great-uncle');
	case 'parmotsib': return i18n::translate_c('parent\'s mother\'s sibling', 'great-aunt/uncle');
	case 'parfatsis': return i18n::translate_c('parent\'s father\'s sister', 'great-aunt');
	case 'parfatbro': return i18n::translate_c('parent\'s father\'s brother', 'great-uncle');
	case 'parfatsib': return i18n::translate_c('parent\'s father\'s sibling', 'great-aunt/uncle');
	case 'parparsis': return i18n::translate_c('parent\'s parent\'s sister', 'great-aunt');
	case 'parparbro': return i18n::translate_c('parent\'s parent\'s brother', 'great-uncle');
	case 'parparsib': return i18n::translate_c('parent\'s parent\'s sibling', 'great-aunt/uncle');
	
	case 'brodauhus': return i18n::translate_c('brother\'s daughter\'s husband', 'nephew-in-law');
	case 'brosonwif': return i18n::translate_c('brother\'s son\'s wife', 'niece-in-law');
	case 'sisdauhus': return i18n::translate_c('sisters\'s daughter\'s husband', 'nephew-in-law');
	case 'sissonwif': return i18n::translate_c('sisters\'s son\'s wife', 'niece-in-law');
	case 'sibdauhus': return i18n::translate_c('sibling\'s daughter\'s husband', 'nephew-in-law');
	case 'sibsonwif': return i18n::translate_c('sibling\'s son\'s wife', 'niece-in-law');
	
	case 'motbrowif': return i18n::translate_c('mother\'s brother\'s wife', 'aunt');
	case 'motsishus': return i18n::translate_c('mother\'s sister\'s husband', 'uncle');
	case 'fatbrowif': return i18n::translate_c('father\'s brother\'s wife', 'aunt');
	case 'fatsishus': return i18n::translate_c('father\'s sister\'s husband', 'uncle');
	case 'parbrowif': return i18n::translate_c('parent\'s brother\'s wife', 'aunt');
	case 'parsishus': return i18n::translate_c('parent\'s sister\'s husband', 'uncle');

	case 'motbroson': return i18n::translate_c('mother\'s brother\'s son', 'cousin');
	case 'motbrodau': return i18n::translate_c('mother\'s brother\'s daughter', 'cousin');
	case 'motbrochi': return i18n::translate_c('mother\'s brother\'s child', 'cousin');
	case 'fatbroson': return i18n::translate_c('father\'s brother\'s son', 'cousin');
	case 'fatbrodau': return i18n::translate_c('father\'s brother\'s daughter', 'cousin');
	case 'fatbrochi': return i18n::translate_c('father\'s brother\'s child', 'cousin');
	case 'motsisson': return i18n::translate_c('mother\'s sister\'s son', 'cousin');
	case 'motsisdau': return i18n::translate_c('mother\'s sister\'s daughter', 'cousin');
	case 'motsischi': return i18n::translate_c('mother\'s sister\'s child', 'cousin');
	case 'fatsisson': return i18n::translate_c('father\'s sister\'s son', 'cousin');
	case 'fatsisdau': return i18n::translate_c('father\'s sister\'s daughter', 'cousin');
	case 'fatsischi': return i18n::translate_c('father\'s sister\'s child', 'cousin');

	}

	// Look for generic/pattern relationships.
	// TODO: these are heavily based on english relationship names.
	// We need feedback from other languages to improve this.
	// Dutch has special names for 8 generations of great-great-..., so these need explicit naming
	if (preg_match('/^((?:mot|fat|par)*)(bro|sis|sib)$/', $path, $match)) {
		$up=strlen($match[1])/3;
		$last=substr($path, -3, 3);
		switch ($up) {
		case 3:
			switch ($last) {
			case 'bro': return i18n::translate('great-great-uncle');
			case 'sis': return i18n::translate('great-great-aunt');
			case 'sib': return i18n::translate('great-great-aunt/uncle');
			}
			break;
		case 4:
			switch ($last) {
			case 'bro': return i18n::translate('great-great-uncle');
			case 'sis': return i18n::translate('great-great-aunt');
			case 'sib': return i18n::translate('great-great-aunt/uncle');
			}
			break;
		case 5:
			switch ($last) {
			case 'bro': return i18n::translate('great x4 uncle');
			case 'sis': return i18n::translate('great x4 aunt');
			case 'sib': return i18n::translate('great x4 aunt/uncle');
			}
			break;
		case 6:
			switch ($last) {
			case 'bro': return i18n::translate('great x5 uncle');
			case 'sis': return i18n::translate('great x5 aunt');
			case 'sib': return i18n::translate('great x5 aunt/uncle');
			}
			break;
		case 7:
			switch ($last) {
			case 'bro': return i18n::translate('great x6 uncle');
			case 'sis': return i18n::translate('great x6 aunt');
			case 'sib': return i18n::translate('great x6 aunt/uncle');
			}
			break;
		case 8:
			switch ($last) {
			case 'bro': return i18n::translate('great x7 uncle');
			case 'sis': return i18n::translate('great x7 aunt');
			case 'sib': return i18n::translate('great x7 aunt/uncle');
			}
			break;
		default:
			switch ($last) {
			case 'bro': return i18n::translate('great x%d uncle',      $up-1);
			case 'sis': return i18n::translate('great x%d aunt',       $up-1);
			case 'sib': return i18n::translate('great x%d aunt/uncle', $up-1);
			}
			break;
		}
	}
	if (preg_match('/^((?:mot|fat|par)*)$/', $path, $match)) {
		$up=strlen($match[1])/3;
		$last=substr($path, -3, 3);
		switch ($up) {
		case 4:
			switch ($last) {
			case 'mot': return i18n::translate('great-great-grandmother');
			case 'fat': return i18n::translate('great-great-grandfather');
			case 'par': return i18n::translate('great-great-grandparent');
			}
			break;
		case 5:
			switch ($last) {
			case 'mot': return i18n::translate('great-great-great-grandmother');
			case 'fat': return i18n::translate('great-great-great-grandfather');
			case 'par': return i18n::translate('great-great-great-grandparent');
			}
			break;
		case 6:
			switch ($last) {
			case 'mot': return i18n::translate('great x4 grandmother');
			case 'fat': return i18n::translate('great x4 grandfather');
			case 'par': return i18n::translate('great x4 grandparent');
			}
			break;
		case 7:
			switch ($last) {
			case 'mot': return i18n::translate('great x5 grandmother');
			case 'fat': return i18n::translate('great x5 grandfather');
			case 'par': return i18n::translate('great x5 grandparent');
			}
			break;
		case 8:
			switch ($last) {
			case 'mot': return i18n::translate('great x6 grandmother');
			case 'fat': return i18n::translate('great x6 grandfather');
			case 'par': return i18n::translate('great x6 grandparent');
			}
			break;
		case 9:
			switch ($last) {
			case 'mot': return i18n::translate('great x7 grandmother');
			case 'fat': return i18n::translate('great x7 grandfather');
			case 'par': return i18n::translate('great x7 grandparent');
			}
			break;
		default:
			// Different languages have different rules for naming generations.
			// An english great x12 grandfather is a danish great x11 grandfather.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'da':
				switch ($last) {
				case 'mot': return i18n::translate('great x%d grandmother', $up-3);
				case 'fat': return i18n::translate('great x%d grandfather', $up-3);
				case 'par': return i18n::translate('great x%d grandparent', $up-3);
				}
			default:
				switch ($last) {
				case 'mot': return i18n::translate('great x%d grandmother', $up-2);
				case 'fat': return i18n::translate('great x%d grandfather', $up-2);
				case 'par': return i18n::translate('great x%d grandparent', $up-2);
				}
			}
		}
	}
	if (preg_match('/^((?:son|dau|chi)*)$/', $path, $match)) {
		$up=strlen($match[1])/3;
		$last=substr($path, -3, 3);
		switch ($up) {
		case 4:
			switch ($last) {
			case 'son': return i18n::translate('great-great-grandson');
			case 'dau': return i18n::translate('great-great-granddaughter');
			case 'chi': return i18n::translate('great-great-grandchild');
			}
			break;
		case 5:
			switch ($last) {
			case 'son': return i18n::translate('great-great-great-grandson');
			case 'dau': return i18n::translate('great-great-great-granddaughter');
			case 'chi': return i18n::translate('great-great-great-grandchild');
			}
			break;
		case 6:
			switch ($last) {
			case 'son': return i18n::translate('great x4 grandson');
			case 'dau': return i18n::translate('great x4 granddaughter');
			case 'chi': return i18n::translate('great x4 grandchild');
			}
			break;
		case 7:
			switch ($last) {
			case 'son': return i18n::translate('great x5 grandson');
			case 'dau': return i18n::translate('great x5 granddaughter');
			case 'chi': return i18n::translate('great x5 grandchild');
			}
			break;
		case 8:
			switch ($last) {
			case 'son': return i18n::translate('great x6 grandson');
			case 'dau': return i18n::translate('great x6 granddaughter');
			case 'chi': return i18n::translate('great x6 grandchild');
			}
			break;
		case 9:
			switch ($last) {
			case 'son': return i18n::translate('great x7 grandson');
			case 'dau': return i18n::translate('great x7 granddaughter');
			case 'chi': return i18n::translate('great x7 grandchild');
			}
			break;
		default:
			switch ($last) {
			case 'son': return i18n::translate('great x%d grandson', $up-2);
			case 'dau': return i18n::translate('great x%d granddaughter', $up-2);
			case 'chi': return i18n::translate('great x%d grandchild', $up-2);
			}
			break;
		}
	}
	if (preg_match('/^((?:mot|fat|par)+)(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match)) {
		$up  =strlen($match[1])/3;
		$down=strlen($match[2])/3;
		$last=substr($path, -3, 3);
		// Different languages have different rules for naming cousins.  For example,
		// an english "second cousin once removed" is a polish "cousin of 7th degree".
		//
		// Need to find out which languages use which rules.
		switch (WT_LOCALE) {
		case 'pl': // See: Lucasz
		case 'it': // ??? See: http://it.wikipedia.org/wiki/Cugino
			switch ($last) {
			case 'son': return /* I18N: %s is "first", "second", ... */ i18n::translate_c('MALE', 'cousin of the %s degree',   i18n::ordinal_word($up+$down+2));
			case 'dau': return /* I18N: %s is "first", "second", ... */ i18n::translate_c('FEMALE', 'cousin of the %s degree', i18n::ordinal_word($up+$down+2));
			case 'chi': return /* I18N: %s is "first", "second", ... */ i18n::translate('cousin of the %s degree',             i18n::ordinal_word($up+$down+2));
			}
			break;
		case 'en': // See: http://en.wikipedia.org/wiki/File:CousinTree.svg
		case 'en_GB':
		default:
			if ($up==$down) {
				switch ($last) {
				case 'son': return i18n::translate_c('MALE', '%s cousin',   i18n::ordinal_word($up-1));
				case 'dau': return i18n::translate_c('FEMALE', '%s cousin', i18n::ordinal_word($up-1));
				case 'chi': return i18n::translate('%s cousin',             i18n::ordinal_word($up-1));
				}
			} else {
				$removed=abs($down-$up);
				switch ($last) {
				case 'son':
					return i18n::plural(
						'%1$s male cousin, %2$d time removed', '%1$s male cousin, %2$d times removed',
						$removed, i18n::ordinal_word(min($up, $down)), $removed
					);
				case 'dau':
					return i18n::plural(
						'%1$s female cousin, %2$d time removed', '%1$s female cousin, %2$d times removed',
						$removed, i18n::ordinal_word(min($up, $down)), $removed
					);
				case 'chi': return i18n::plural('%1$s cousin, %2$d time removed', '%1$s cousin, %2$d times removed',
					$removed, i18n::ordinal_word(min($up, $down)), $removed
					);
				}
			}
			break;
		}
	}

	// TODO: break the relationship down into sub-relationships.  e.g. cousin's cousin.

	// We don't have a specific name for this relationship, and we can't match it with a pattern.
	// Just spell it out.

	// TODO: long relationships are a bit ridiculous - although tecnically correct.
	// Perhaps translate long paths as "a distant blood relative", or "a distant relative by marriage"
	switch (substr($path, -3, 3)) {
	case 'mot': $relationship=i18n::translate('mother'  ); break;
	case 'fat': $relationship=i18n::translate('father'  ); break;
	case 'par': $relationship=i18n::translate('parent'  ); break;
	case 'hus': $relationship=i18n::translate('husband' ); break;
	case 'wif': $relationship=i18n::translate('wife'    ); break;
	case 'spo': $relationship=i18n::translate('spouse'  ); break;
	case 'bro': $relationship=i18n::translate('brother' ); break;
	case 'sis': $relationship=i18n::translate('sister'  ); break;
	case 'sib': $relationship=i18n::translate('sibling' ); break;
	case 'son': $relationship=i18n::translate('son'     ); break;
	case 'dau': $relationship=i18n::translate('daughter'); break;
	case 'chi': $relationship=i18n::translate('child'   ); break;
	}
	while (($path=substr($path, 0, strlen($path)-3))!='') {
		switch (substr($path, -3, 3)) {
			// I18N: These strings are used to build paths of relationships, such as "father's wife's husband's brother"
		case 'mot': $relationship=i18n::translate('mother\'s %s',   $relationship); break;
		case 'fat': $relationship=i18n::translate('father\'s %s',   $relationship); break;
		case 'par': $relationship=i18n::translate('parent\'s %s',   $relationship); break;
		case 'hus': $relationship=i18n::translate('husband\'s %s',  $relationship); break;
		case 'wif': $relationship=i18n::translate('wife\'s %s',     $relationship); break;
		case 'spo': $relationship=i18n::translate('spouse\'s %s',   $relationship); break;
		case 'bro': $relationship=i18n::translate('brother\'s %s',  $relationship); break;
		case 'sis': $relationship=i18n::translate('sister\'s %s',   $relationship); break;
		case 'sib': $relationship=i18n::translate('sibling\'s %s',  $relationship); break;
		case 'son': $relationship=i18n::translate('son\'s %s',      $relationship); break;
		case 'dau': $relationship=i18n::translate('daughter\'s %s', $relationship); break;
		case 'chi': $relationship=i18n::translate('child\'s %s',    $relationship); break;
		}
	}
	return $relationship;
}

/**
 * write changes
 *
 * this function writes the $pgv_changes back to the <var>$INDEX_DIRECTORY</var>/pgv_changes.php
 * file so that it can be read in and checked to see if records have been updated.  It also stores
 * old records so that they can be undone.
 * @return bool true if successful false if there was an error
 */
function write_changes() {
	global $pgv_changes, $INDEX_DIRECTORY, $CONTACT_EMAIL;

	//-- only allow 1 thread to write changes at a time
	$mutex = new Mutex("pgv_changes");
	$mutex->Wait();
	//-- write the changes file
	$changestext = "<?php\n\$pgv_changes = array();\n";
	foreach ($pgv_changes as $gid=>$changes) {
		if (count($changes)>0) {
			$changestext .= "\$pgv_changes[\"$gid\"] = array();\n";
			foreach ($changes as $indexval => $change) {
				$changestext .= "// Start of change record.\n";
				$changestext .= "\$change = array();\n";
				$changestext .= "\$change[\"gid\"] = '".$change["gid"]."';\n";
				$changestext .= "\$change[\"gedcom\"] = '".$change["gedcom"]."';\n";
				$changestext .= "\$change[\"type\"] = '".$change["type"]."';\n";
				$changestext .= "\$change[\"status\"] = '".$change["status"]."';\n";
				$changestext .= "\$change[\"user\"] = '".$change["user"]."';\n";
				$changestext .= "\$change[\"time\"] = '".$change["time"]."';\n";
				if (isset($change["linkpid"]))
					$changestext .= "\$change[\"linkpid\"] = '".$change["linkpid"]."';\n";
				$changestext .= "\$change[\"undo\"] = '".str_replace("\\\\'", "\\'", preg_replace("/'/", "\\'", $change["undo"]))."';\n";
				$changestext .= "// End of change record.\n";
				$changestext .= "\$pgv_changes[\"$gid\"][] = \$change;\n";
			}
		}
	}
	$fp = fopen($INDEX_DIRECTORY."pgv_changes.php", "wb");
	if ($fp===false) {
		print "ERROR 6: Unable to open changes file resource.  Unable to complete request.\n";
		return false;
	}
	$fw = fwrite($fp, $changestext);
	if ($fw===false) {
		print "ERROR 7: Unable to write to changes file.\n";
		fclose($fp);
		return false;
	}
	fclose($fp);

	//-- release the mutex acquired above
	$mutex->Release();

	$logline = AddToLog("pgv_changes.php updated");
	return true;
}

/**
 * get theme names
 *
 * function to get the names of all of the themes as an array
 * it searches the themes directory and reads the name from the theme_name variable
 * in the theme.php file.
 * @return array and array of theme names and their corresponding directory
 */
function get_theme_names() {
	$themes = array();
	$d = dir("themes");
	while (false !== ($entry = $d->read())) {
		if ($entry{0}!="." && $entry!="CVS" && !stristr($entry, "svn") && is_dir(WT_ROOT.'themes/'.$entry) && file_exists(WT_ROOT.'themes/'.$entry.'/theme.php')) {
			$themefile = implode("", file(WT_ROOT.'themes/'.$entry.'/theme.php'));
			$tt = preg_match("/theme_name\s*=\s*\"(.*)\";/", $themefile, $match);
			if ($tt>0)
				$themename = trim($match[1]);
			else
				$themename = "themes/$entry";
			$themes[$themename] = "themes/$entry/";
		}
	}
	$d->close();
	uksort($themes, "utf8_strcasecmp");
	return $themes;
}

/**
 * decode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to decode the filename
 * before it can be used on the filesystem
 */
function filename_decode($filename) {
	if (DIRECTORY_SEPARATOR=='\\')
		return utf8_decode($filename);
	else
		return $filename;
}

/**
 * encode a filename
 *
 * windows doesn't use UTF-8 for its file system so we have to encode the filename
 * before it can be used in PGV
 */
function filename_encode($filename) {
	if (DIRECTORY_SEPARATOR=='\\')
		return utf8_encode($filename);
	else
		return $filename;
}

////////////////////////////////////////////////////////////////////////////////
// Remove empty and duplicate values from a URL query string
////////////////////////////////////////////////////////////////////////////////
function normalize_query_string($query) {
	$components=array();
	foreach (preg_split('/(^\?|\&(amp;)*)/', urldecode($query), -1, PREG_SPLIT_NO_EMPTY) as $component)
		if (strpos($component, '=')!==false) {
			list ($key, $data)=explode('=', $component, 2);
			if (!empty($data)) $components[$key]=$data;
		}
	$new_query='';
	foreach ($components as $key=>$data)
		$new_query.=(empty($new_query)?'?':'&amp;').$key.'='.$data;

	return $new_query;
}

/**
 * get a list of the reports in the reports directory
 *
 * When $force is false, the function will first try to read the reports list from the$INDEX_DIRECTORY."/reports.dat"
 * data file.  Otherwise the function will parse the report xml files and get the titles.
 * @param boolean $force	force the code to look in the directory and parse the files again
 * @return array 	The array of the found reports with indexes [title] [file]
 */
function get_report_list($force=false) {
	global $INDEX_DIRECTORY, $report_array, $vars, $xml_parser, $elementHandler;

	$files = array();
	if (!$force) {
		//-- check if the report files have been cached
		if (file_exists($INDEX_DIRECTORY."/reports.dat")) {
			$reportdat = "";
			$fp = fopen($INDEX_DIRECTORY."/reports.dat", "r");
			while ($data = fread($fp, 4096)) {
				$reportdat .= $data;
			}
			fclose($fp);
			$files = unserialize($reportdat);
			foreach ($files as $indexval => $file) {
				if (isset($file["title"][WT_LOCALE]) && (strlen($file["title"][WT_LOCALE])>1))
					return $files;
			}
		}
	}

	//-- find all of the reports in the reports directory
	$d = dir("reports");
	while (false !== ($entry = $d->read())) {
		if (($entry{0}!=".") && ($entry!="CVS") && (preg_match('/\.xml$/i', $entry)>0)) {
			if (!isset($files[$entry]["file"]))
				$files[$entry]["file"] = "reports/".$entry;
		}
	}
	$d->close();

	require_once WT_ROOT.'includes/reportheader.php';
	$report_array = array();
	if (!function_exists("xml_parser_create"))
		return $report_array;
	foreach ($files as $file=>$r) {
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		if (file_exists($r["file"])) {
			//-- open the file
			if (!($fp = fopen($r["file"], "r"))) {
				die("could not open XML input");
			}
			//-- read the file and parse it 4kb at a time
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml_parser, $data, feof($fp))) {
					die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
				}
			}
			fclose($fp);
			xml_parser_free($xml_parser);
			if (isset($report_array["title"]) && isset($report_array["access"]) && isset($report_array["icon"])) {
				$files[$file]["title"][WT_LOCALE] = $report_array["title"];
				$files[$file]["access"] = $report_array["access"];
				$files[$file]["icon"] = $report_array["icon"];
			}
		}
	}

	$fp = @fopen($INDEX_DIRECTORY."/reports.dat", "w");
	@fwrite($fp, serialize($files));
	@fclose($fp);
	$logline = AddToLog("reports.dat updated");
	return $files;
}

function getfilesize($bytes) {
	if ($bytes>=1099511627776) {
		return round($bytes/1099511627776, 2)." TB";
	}
	if ($bytes>=1073741824) {
		return round($bytes/1073741824, 2)." GB";
	}
	if ($bytes>=1048576) {
		return round($bytes/1048576, 2)." MB";
	}
	if ($bytes>=1024) {
		return round($bytes/1024, 2)." KB";
	}
	return $bytes." B";
}

/**
 * array merge function for PGV
 * the PHP array_merge function will reindex all numerical indexes
 * This function should only be used for associative arrays
 * @param array $array1
 * @param array $array2
 */
function pgv_array_merge($array1, $array2) {
	foreach ($array2 as $key=>$value) {
		$array1[$key] = $value;
	}
	return $array1;
}

/**
 * checks if the value is in an array recursively
 * @param string $needle
 * @param array $haystack
 */
function in_arrayr($needle, $haystack) {
	foreach ($haystack as $v) {
		if ($needle == $v) return true;
		else if (is_array($v)) {
			if (in_arrayr($needle, $v) === true) return true;
		}
	}
	return false;
}

/**
 * function to build an URL querystring from GET or POST variables
 * @return string
 */
function get_query_string() {
	$qstring = "";
	if (!empty($_GET)) {
		foreach ($_GET as $key => $value) {
			if ($key != "view") {
				if (!is_array($value)) {
					$qstring .= "{$key}={$value}&";
				} else {
					foreach ($value as $k=>$v) {
						$qstring .= "{$key}[{$k}]={$v}&";
					}
				}
			}
		}
	} else {
		if (!empty($_POST)) {
			foreach ($_POST as $key => $value) {
				if ($key != "view") {
					if (!is_array($value)) {
						$qstring .= "{$key}={$value}&";
					} else {
						foreach ($value as $k=>$v) {
							if (!is_array($v)) {
								$qstring .= "{$key}[{$k}]={$v}&";
							}
						}
					}
				}
			}
		}
	}
	$qstring = rtrim($qstring, "&");	// Remove trailing "&"
	return encode_url($qstring);
}

//This function works with a specified generation limit.  It will completely fill
//the pdf witout regard to if a known person exists in each generation.
//ToDo: If a known individual is found in a generation, add prior empty positions
//and add remaining empty spots automatically.
function add_ancestors(&$list, $pid, $children=false, $generations=-1, $show_empty=false) {
	$total_num_skipped = 0;
	$skipped_gen = 0;
	$num_skipped = 0;
	$genlist = array($pid);
	$list[$pid]->generation = 1;
	while (count($genlist)>0) {
		$id = array_shift($genlist);
		if (strpos($id, "empty")===0) continue; // id can be something like "empty7"
		$person = Person::getInstance($id);
		$famids = $person->getChildFamilies();
		if (count($famids)>0) {
			if ($show_empty) {
				for ($i=0;$i<$num_skipped;$i++) {
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					array_push($genlist, "empty" . $total_num_skipped);
					$total_num_skipped++;
				}
			}
			$num_skipped = 0;
			foreach ($famids as $famid => $family) {
				$husband = $family->getHusband();
				$wife = $family->getWife();
				if ($husband) {
					$list[$husband->getXref()] = $husband;
					$list[$husband->getXref()]->generation = $list[$id]->generation+1;
				} elseif ($show_empty) {
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
				}
				if ($wife) {
					$list[$wife->getXref()] = $wife;
					$list[$wife->getXref()]->generation = $list[$id]->generation+1;
				} elseif ($show_empty) {
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
				}
				if ($generations == -1 || $list[$id]->generation+1 < $generations) {
					$skipped_gen = $list[$id]->generation+1;
					if ($husband) {
						array_push($genlist, $husband->getXref());
					} elseif ($show_empty) {
						array_push($genlist, "empty" . $total_num_skipped);
					}
					if ($wife) {
						array_push($genlist, $wife->getXref());
					} elseif ($show_empty) {
						array_push($genlist, "empty" . $total_num_skipped);
					}
				}
				$total_num_skipped++;
				if ($children) {
					$childs = $family->getChildren();
					foreach($childs as $child) {
						$list[$child->getXref()] = $child;
						if (isset($list[$id]->generation))
							$list[$child->getXref()]->generation = $list[$id]->generation;
						else
							$list[$child->getXref()]->generation = 1;
					}
				}
			}
		} else
			if ($show_empty) {
				if ($skipped_gen > $list[$id]->generation) {
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					$total_num_skipped++;
					$list["empty" . $total_num_skipped] = new Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					array_push($genlist, "empty" . ($total_num_skipped - 1));
					array_push($genlist, "empty" . $total_num_skipped);
					$total_num_skipped++;
				} else
					$num_skipped += 2;
		}

	}
}

//--- copied from class_reportpdf.php
function add_descendancy(&$list, $pid, $parents=false, $generations=-1) {
	$person = Person::getInstance($pid);
	if ($person==null) return;
	if (!isset($list[$pid])) {
		$list[$pid] = $person;
	}
	if (!isset($list[$pid]->generation)) {
		$list[$pid]->generation = 0;
	}
	$famids = $person->getSpouseFamilies();
	if (count($famids)>0) {
		foreach ($famids as $famid => $family) {
			if ($family) {
				if ($parents) {
					$husband = $family->getHusband();
					$wife = $family->getWife();
					if ($husband) {
						$list[$husband->getXref()] = $husband;
						if (isset($list[$pid]->generation))
							$list[$husband->getXref()]->generation = $list[$pid]->generation-1;
						else
							$list[$husband->getXref()]->generation = 1;
					}
					if ($wife) {
						$list[$wife->getXref()] = $wife;
						if (isset($list[$pid]->generation))
							$list[$wife->getXref()]->generation = $list[$pid]->generation-1;
						else
							$list[$wife->getXref()]->generation = 1;
					}
				}
				$children = $family->getChildren();
				foreach($children as $child) {
					if ($child) {
						$list[$child->getXref()] = $child;
						if (isset($list[$pid]->generation))
							$list[$child->getXref()]->generation = $list[$pid]->generation+1;
						else
							$list[$child->getXref()]->generation = 2;
					}
				}
				if ($generations == -1 || $list[$pid]->generation+1 < $generations) {
					foreach($children as $child) {
						add_descendancy($list, $child->getXref(), $parents, $generations);	// recurse on the childs family
					}
				}
			}
		}
	}
}

/**
 * check if the page view rate for a session has been exeeded.
 */
function CheckPageViews() {
	global $SEARCH_SPIDER, $MAX_VIEWS, $MAX_VIEW_TIME;

	if ($MAX_VIEW_TIME == 0 || $MAX_VIEWS == 0 || !empty($SEARCH_SPIDER))
		return;

	// The media firewall should not be throttled
	if (WT_SCRIPT_NAME=='mediafirewall.php')
		return;

	if (!empty($_SESSION["pageviews"]["time"]) && !empty($_SESSION["pageviews"]["number"])) {
		$_SESSION["pageviews"]["number"] ++;
		if ($_SESSION["pageviews"]["number"] < $MAX_VIEWS)
			return;
		$sleepTime = $MAX_VIEW_TIME - time() + $_SESSION["pageviews"]["time"];
		if ($sleepTime > 0) {
			// The configured page view rate has been exceeded
			// - Log a message and then sleep to slow things down
			$text = "Permitted page view rate of {$MAX_VIEWS} per {$MAX_VIEW_TIME} seconds exceeded.";
			AddToLog($text);
			sleep($sleepTime);
		}
	}
	$_SESSION["pageviews"] = array("time"=>time(), "number"=>1);
}

/**
 * get the next available xref
 * calculates the next available XREF id for the given type of record
 * @param string $type	the type of record, defaults to 'INDI'
 * @return string
 */
function get_new_xref($type='INDI', $ged_id=WT_GED_ID, $use_cache=false) {
	global $fcontents, $SOURCE_ID_PREFIX, $REPO_ID_PREFIX, $pgv_changes, $TBLPREFIX;
	global $MEDIA_ID_PREFIX, $FAM_ID_PREFIX, $GEDCOM_ID_PREFIX, $MAX_IDS;

	$num = null;
	//-- check if an id is stored in MAX_IDS used mainly during the import
	//-- the number stored in the max_id is the next number to use... no need to increment it
	if ($use_cache && !empty($MAX_IDS)&& isset($MAX_IDS[$type])) {
		$num = 1;
		$num = $MAX_IDS[$type];
		$MAX_IDS[$type] = $num+1;
	} else {
		//-- check for the id in the nextid table
		$num=
			WT_DB::prepare("SELECT ni_id FROM {$TBLPREFIX}nextid WHERE ni_type=? AND ni_gedfile=?")
			->execute(array($type, $ged_id))
			->fetchOne();

		//-- the id was not found in the table so try and find it in the file
		if (is_null($num) && !empty($fcontents)) {
			$ct = preg_match_all("/0 @(.*)@ $type/", $fcontents, $match, PREG_SET_ORDER);
			$num = 0;
			for ($i=0; $i<$ct; $i++) {
				$ckey = $match[$i][1];
				$bt = preg_match("/(\d+)/", $ckey, $bmatch);
				if ($bt>0) {
					$bnum = trim($bmatch[1]);
					if ($num < $bnum)
						$num = $bnum;
				}
			}
			$num++;
		}
		//-- type wasn't found in database or in file so make a new one
		if (is_null($num)) {
			$num = 1;
			WT_DB::prepare("INSERT INTO {$TBLPREFIX}nextid VALUES(?, ?, ?)")
				->execute(array($num+1, $type, $ged_id));
		}
	}

	switch ($type) {
	case "INDI":
		$prefix = $GEDCOM_ID_PREFIX;
		break;
	case "FAM":
		$prefix = $FAM_ID_PREFIX;
		break;
	case "OBJE":
		$prefix = $MEDIA_ID_PREFIX;
		break;
	case "SOUR":
		$prefix = $SOURCE_ID_PREFIX;
		break;
	case "REPO":
		$prefix = $REPO_ID_PREFIX;
		break;
	default:
		$prefix = $type{0};
		break;
	}

	//-- make sure this number has not already been used
	if ($num>=2147483647 || $num<=0) { // Popular databases are only 32 bits (signed)
		$num=1;
	}
	while (find_gedcom_record($prefix.$num, $ged_id) || find_updated_record($prefix.$num, $ged_id)) {
		++$num;
		if ($num>=2147483647 || $num<=0) { // Popular databases are only 32 bits (signed)
			$num=1;
		}
	}

	//-- the key is the prefix and the number
	$key = $prefix.$num;

	//-- during the import we won't update the database at this time so return now
	if ($use_cache && isset($MAX_IDS[$type])) {
		return $key;
	}
	//-- update the next id number in the DB table
	WT_DB::prepare("UPDATE {$TBLPREFIX}nextid SET ni_id=? WHERE ni_type=? AND ni_gedfile=?")
		->execute(array($num+1, $type, $ged_id));
	return $key;
}

/**
 * check if the given string has UTF-8 characters
 *
 */
function has_utf8($string) {
	$len = strlen($string);
	for ($i=0; $i<$len; $i++) {
		$letter = substr($string, $i, 1);
		$ord = ord($letter);
		if ($ord==95 || $ord>=195)
			return true;
	}
	return false;
}

/**
 * determines whether the passed in filename is a link to an external source (i.e. contains '://')
 */
function isFileExternal($file) {
	return strpos($file, '://') !== false;
}

/*
 * Encrypt the input string
 *
 * This function is used when a file name needs to be passed to another script by means of the
 * GET method.  This method passes parameters to the script through the URL that launches the
 * script.
 *
 * File names could themselves be legitimate URLs.  These legitimate URLs would normally be
 * killed by the hacker detection code in "includes/session_spider.php".  This method avoids
 * that problem.
 *
 */
function encrypt($string, $key='') {
	if (empty($key)) $key = session_id();
	$result = '';

	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$newOrd = ord($char) + ord($keychar);
		if ($newOrd > 255) $newOrd -= 256;		// Make sure we stay within the 8-bit code table
		$result .= chr($newOrd);
	}
	$result = '*'.strtr(base64_encode($result), '+/=', '-_#');		// Avoid characters that mess up URLs

	return $result;
}

/*
 * Decrypt the input string
 *
 * See above.
 *
 */
function decrypt($string, $key='') {
	if (empty($key)) $key = session_id();

	if (substr($string, 0, 1)!='*') return $string;		// Input is not a valid encrypted string
	$string = base64_decode(strtr(substr($string, 1), '-_#', '+/='));

	$result = '';
	for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$newOrd = ord($char) - ord($keychar);
		if ($newOrd < 0) $newOrd += 256;		// Make sure we stay within the 8-bit code table
		$result .= chr($newOrd);
	}

	return $result;
}

/*
 * Get useful information on how to handle this media file
 */
function mediaFileInfo($fileName, $thumbName, $mid, $name='', $notes='', $obeyViewerOption=true) {
	global $THUMBNAIL_WIDTH, $WT_IMAGE_DIR, $WT_IMAGES;
	global $LB_URL_WIDTH, $LB_URL_HEIGHT;
	global $SERVER_URL, $GEDCOM, $USE_MEDIA_VIEWER, $USE_MEDIA_FIREWALL, $MEDIA_FIREWALL_THUMBS;

	$result = array();

	// -- Classify the incoming media file
	if (preg_match('~^https?://~i', $fileName)) $type = 'url_';
	else $type = 'local_';
	if ((preg_match('/\.flv$/i', $fileName) || preg_match('~^https?://.*\.youtube\..*/watch\?~i', $fileName)) && is_dir(WT_ROOT.'modules/JWplayer')) {
		$type .= 'flv';
	} else if (preg_match('~^https?://picasaweb*\.google\..*/.*/~i', $fileName)) {
		$type .= 'picasa';
	} else if (preg_match('/\.(jpg|jpeg|gif|png)$/i', $fileName)) {
		$type .= 'image';
	} else if (preg_match('/\.(pdf|avi|txt)$/i', $fileName)) {
		$type .= 'page';
	} else if (preg_match('/\.mp3$/i', $fileName)) {
		$type .= 'audio';
	} else if (preg_match('/\.wmv$/i', $fileName)) {
		$type .= 'wmv';
	} else $type .= 'other';
	// $type is now: (url | local) _ (flv | picasa | image | page | audio | other)
	$result['type'] = $type;

	// -- Determine the correct URL to open this media file
 	while (true) {
		if (WT_USE_LIGHTBOX) {
			// Lightbox is installed
			require_once WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
			switch ($type) {
			case 'url_flv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($fileName)) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "::" . htmlspecialchars($notes, ENT_COMPAT, 'UTF-8');
				break 2;
			case 'local_flv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($SERVER_URL.$fileName)) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "::" . htmlspecialchars($notes, ENT_COMPAT, 'UTF-8');
				break 2;
			case 'url_wmv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($fileName)) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "::" . htmlspecialchars($notes, ENT_COMPAT, 'UTF-8');
				break 2;
			case 'local_audio':
			case 'local_wmv':
				$url = encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($SERVER_URL.$fileName)) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "::" . htmlspecialchars($notes, ENT_COMPAT, 'UTF-8');
				break 2;
			case 'url_image':
			case 'local_image':
				$url = encode_url($fileName) . "\" rel=\"clearbox[general]\" rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "::" . htmlspecialchars($notes, ENT_COMPAT, 'UTF-8');
				break 2;
			case 'url_picasa':
			case 'url_page':
			case 'url_other':
			case 'local_page':
			// case 'local_other':
				$url = encode_url($fileName) . "\" rel='clearbox({$LB_URL_WIDTH}, {$LB_URL_HEIGHT}, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . PrintReady(htmlspecialchars($name, ENT_COMPAT, 'UTF-8')) . "::" . htmlspecialchars($notes, ENT_COMPAT, 'UTF-8');
				break 2;
			}
		}

		// Lightbox is not installed or Lightbox is not appropriate for this media type
		switch ($type) {
		case 'url_flv':
			$url = "javascript:;\" onclick=\" var winflv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($fileName)) . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
			break 2;
		case 'local_flv':
			$url = "javascript:;\" onclick=\" var winflv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=flvVideo&flvVideo='.encrypt($SERVER_URL.$fileName)) . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
			break 2;
		case 'url_wmv':
			$url = "javascript:;\" onclick=\" var winwmv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($fileName)) . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
			break 2;
		case 'local_wmv':
		case 'local_audio':
			$url = "javascript:;\" onclick=\" var winwmv = window.open('".encode_url('module.php?mod=JWplayer&pgvaction=wmvVideo&wmvVideo='.encrypt($SERVER_URL.$fileName)) . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
			break 2;
		case 'url_image':
			$imgsize = findImageSize($fileName);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$url = "javascript:;\" onclick=\"var winimg = window.open('".encode_url($fileName)."', 'winimg', 'width=".$imgwidth.", height=".$imgheight.", left=200, top=200'); if (window.focus) {winimg.focus();}";
			break 2;
		case 'url_picasa':
		case 'url_page':
		case 'url_other':
		case 'local_other';
			$url = "javascript:;\" onclick=\"var winurl = window.open('".encode_url($fileName)."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
			break 2;
		case 'local_page':
			$url = "javascript:;\" onclick=\"var winurl = window.open('".encode_url($SERVER_URL.$fileName)."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
			break 2;
		}
		if ($USE_MEDIA_VIEWER && $obeyViewerOption) {
			$url = encode_url('mediaviewer.php?mid='.$mid);
		} else {
			$imgsize = findImageSize($fileName);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$url = "javascript:;\" onclick=\"return openImage('".encode_url(encrypt($fileName))."', $imgwidth, $imgheight);";
		}
		break;
	}
	// At this point, $url describes how to handle the image when its thumbnail is clicked
	$result['url'] = $url;

	// -- Determine the correct thumbnail or pseudo-thumbnail
	$width = '';
	switch ($type) {
		case 'url_flv':
			$thumb = isset($WT_IMAGES["media"]["flashrem"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["flashrem"] : 'images/media/flashrem.png';
			break;
		case 'local_flv':
			$thumb = isset($WT_IMAGES["media"]["flash"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["flash"] : 'images/media/flash.png';
			break;
		case 'url_wmv':
			$thumb = isset($WT_IMAGES["media"]["wmvrem"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["wmvrem"] : 'images/media/wmvrem.png';
			break;
		case 'local_wmv':
			$thumb = isset($WT_IMAGES["media"]["wmv"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["wmv"] : 'images/media/wmv.png';
			break;
		case 'url_picasa':
			$thumb = isset($WT_IMAGES["media"]["picasa"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["picasa"] : 'images/media/picasa.png';
			break;
		case 'url_page':
		case 'url_other':
			$thumb = isset($WT_IMAGES["media"]["globe"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["globe"] : 'images/media/globe.png';
			break;
		case 'local_page':
			$thumb = ($WT_IMAGES["media"]["doc"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["doc"] : 'images/media/doc.gif';
			break;
		case 'url_audio':
		case 'local_audio':
			$thumb = isset($WT_IMAGES["media"]["audio"]) ? $WT_IMAGE_DIR.'/'.$WT_IMAGES["media"]["audio"] : 'images/media/audio.png';
			break;
		default:
			$thumb = $thumbName;
			if (substr($type, 0, 4)=='url_') {
				$width = ' width="'.$THUMBNAIL_WIDTH.'"';
			}
	}

	// -- Use an overriding thumbnail if one has been provided
	// Don't accept any overriding thumbnails that are in the "images" or "themes" directories
	if (substr($thumbName, 0, 7)!='images/' && substr($thumbName, 0, 7)!='themes/') {
		if ($USE_MEDIA_FIREWALL && $MEDIA_FIREWALL_THUMBS) {
			$tempThumbName = get_media_firewall_path($thumbName);
		} else {
			$tempThumbName = $thumbName;
		}
		if (file_exists($tempThumbName)) {
			$thumb = $thumbName;
		}
	}

	// -- Use the theme-specific media icon if nothing else works
	$realThumb = $thumb;
	if (substr($type, 0, 6)=='local_' && !file_exists($thumb)) {
		if (!$USE_MEDIA_FIREWALL || !$MEDIA_FIREWALL_THUMBS) {
			$thumb = $WT_IMAGE_DIR.'/'.$WT_IMAGES['media']['large'];
			$realThumb = $thumb;
		} else {
			$realThumb = get_media_firewall_path($thumb);
			if (!file_exists($realThumb)) {
				$thumb = $WT_IMAGE_DIR.'/'.$WT_IMAGES['media']['large'];
				$realThumb = $thumb;
			}
		}
		$width = '';
	}

	// At this point, $width, $realThumb, and $thumb describe the thumbnail to be displayed
	$result['thumb'] = $thumb;
	$result['realThumb'] = $realThumb;
	$result['width'] = $width;

	return $result;
}

// PHP's native pathinfo() function does not work with filenames that contain UTF8 characters.
// See http://uk.php.net/pathinfo
function pathinfo_utf($path) {
	if (empty($path)) {
		return array('dirname'=>'', 'basename'=>'', 'extension'=>'', 'filename'=>'');
	}
	if (strpos($path, '/')!==false) {
		$tmp=explode('/', $path);
		$basename=end($tmp);
		$dirname=substr($path, 0, strlen($path) - strlen($basename) - 1);
	} else if (strpos($path, '\\') !== false) {
		$tmp=explode('\\', $path);
		$basename=end($tmp);
		$dirname=substr($path, 0, strlen($path) - strlen($basename) - 1);
	} else {
		$basename=$path;		// We have just a file name
		$dirname='.';       // For compatibility with pathinfo()
	}

	if (strpos($basename, '.')!==false) {
		$tmp=explode('.', $path);
		$extension=end($tmp);
		$filename=substr($basename, 0, strlen($basename) - strlen($extension) - 1);
	} else {
		$extension='';
		$filename=$basename;
	}

	return array('dirname'=>$dirname, 'basename'=>$basename, 'extension'=>$extension, 'filename'=>$filename);
}

// optional extra file
if (file_exists(WT_ROOT.'includes/functions.extra.php')) {
	require WT_ROOT.'includes/functions.extra.php';
}

?>
