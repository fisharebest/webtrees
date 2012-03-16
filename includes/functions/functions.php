<?php
// Core Functions
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2010  PGV Development Team.  All rights reserved.
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
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

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
//
// If the values are plain text, pass them through preg_quote_array() to
// escape any regex special characters:
// $export = safe_GET('export', preg_quote_array($gedcoms));
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
		return $arr[$var];
	} else {
		return $default;
	}
}

function preg_quote_array($var) {
	if (is_scalar($var)) {
		return preg_quote($var);
	} else {
		if (is_array($var)) {
			foreach ($var as &$v) {
				$v = preg_quote($v);
			}
			return $var;
		} else {
			// Neither scalar nor array.  Object?
			return false;
		}
	}
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

// Fetch a remote file.  Stream wrappers are disabled on
// many hosts, and do not allow the detection of timeout.
function fetch_remote_file($host, $path, $timeout=3) {
	$fp=@fsockopen($host, '80', $errno, $errstr, $timeout );
	if (!$fp) {
		return null;
	}

	fputs($fp, "GET $path HTTP/1.0\r\nHost: $host\r\nConnection: Close\r\n\r\n");

	$response='';
	while ($data=fread($fp, 65536)) {
		$response.=$data;
	}
	fclose($fp);

	// Take account of a "moved" response.
	if (substr($response, 0, 12)=='HTTP/1.1 303' && preg_match('/\nLocation: http:\/\/([a-z0-9.-]+)(.+)/', $response, $match)) {
		return fetch_remote_file($match[1], $match[2]);
	} else {
		// The response includes headers, a blank line, then the content
		return substr($response, strpos($response, "\r\n\r\n") + 4);
	}
}

// Check with the webtrees.net server for the latest version of webtrees.
// Fetching the remote file can be slow, and place an excessive load on
// the webtrees.net server, so only check it infrequently, and cache the result.
function fetch_latest_version() {
	$last_update_timestamp=get_site_setting('LATEST_WT_VERSION_TIMESTAMP');
	if ($last_update_timestamp < time()-24*60*60*3) {
		$latest_version_txt=fetch_remote_file('webtrees.net', '/latest-version.txt');
		if ($latest_version_txt) {
			set_site_setting('LATEST_WT_VERSION', $latest_version_txt);
			set_site_setting('LATEST_WT_VERSION_TIMESTAMP', time());
			return $latest_version_txt;
		} else {
			// Cannot connect to server - use cached version (if we have one)
			return get_site_setting('LATEST_WT_VERSION');
		}
	} else {
		return get_site_setting('LATEST_WT_VERSION');
	}
}

// Convert a file upload PHP error code into user-friendly text
function file_upload_error_text($error_code) {
	switch ($error_code) {
	case UPLOAD_ERR_OK:
		return WT_I18N::translate('File successfully uploaded');
	case UPLOAD_ERR_INI_SIZE:
	case UPLOAD_ERR_FORM_SIZE:
		return WT_I18N::translate('Uploaded file exceeds the allowed size');
	case UPLOAD_ERR_PARTIAL:
		return WT_I18N::translate('File was only partially uploaded, please try again');
	case UPLOAD_ERR_NO_FILE:
		return WT_I18N::translate('No file was received. Please upload again.');
	case UPLOAD_ERR_NO_TMP_DIR:
		return WT_I18N::translate('Missing PHP temporary directory');
	case UPLOAD_ERR_CANT_WRITE:
		return WT_I18N::translate('PHP failed to write to disk');
	case UPLOAD_ERR_EXTENSION:
		return WT_I18N::translate('PHP blocked file by extension');
	}
}

function load_gedcom_settings($ged_id=WT_GED_ID) {
	// Load the configuration settings into global scope
	// TODO: some of these are used infrequently - just load them when we need them
	global $ABBREVIATE_CHART_LABELS;      $ABBREVIATE_CHART_LABELS      =get_gedcom_setting($ged_id, 'ABBREVIATE_CHART_LABELS');
	global $ADVANCED_NAME_FACTS;          $ADVANCED_NAME_FACTS          =get_gedcom_setting($ged_id, 'ADVANCED_NAME_FACTS');
	global $ADVANCED_PLAC_FACTS;          $ADVANCED_PLAC_FACTS          =get_gedcom_setting($ged_id, 'ADVANCED_PLAC_FACTS');
	global $ALLOW_EDIT_GEDCOM;            $ALLOW_EDIT_GEDCOM            =get_gedcom_setting($ged_id, 'ALLOW_EDIT_GEDCOM');
	global $CALENDAR_FORMAT;              $CALENDAR_FORMAT              =get_gedcom_setting($ged_id, 'CALENDAR_FORMAT');
	global $CHART_BOX_TAGS;               $CHART_BOX_TAGS               =get_gedcom_setting($ged_id, 'CHART_BOX_TAGS');
	global $CONTACT_USER_ID;              $CONTACT_USER_ID              =get_gedcom_setting($ged_id, 'CONTACT_USER_ID');
	global $DEFAULT_PEDIGREE_GENERATIONS; $DEFAULT_PEDIGREE_GENERATIONS =get_gedcom_setting($ged_id, 'DEFAULT_PEDIGREE_GENERATIONS');
	global $ENABLE_AUTOCOMPLETE;          $ENABLE_AUTOCOMPLETE          =get_gedcom_setting($ged_id, 'ENABLE_AUTOCOMPLETE');
	global $EXPAND_NOTES;                 $EXPAND_NOTES                 =get_gedcom_setting($ged_id, 'EXPAND_NOTES');
	global $EXPAND_RELATIVES_EVENTS;      $EXPAND_RELATIVES_EVENTS      =get_gedcom_setting($ged_id, 'EXPAND_RELATIVES_EVENTS');
	global $EXPAND_SOURCES;               $EXPAND_SOURCES               =get_gedcom_setting($ged_id, 'EXPAND_SOURCES');
	global $FAM_ID_PREFIX;                $FAM_ID_PREFIX                =get_gedcom_setting($ged_id, 'FAM_ID_PREFIX');
	global $FULL_SOURCES;                 $FULL_SOURCES                 =get_gedcom_setting($ged_id, 'FULL_SOURCES');
	global $GEDCOM_ID_PREFIX;             $GEDCOM_ID_PREFIX             =get_gedcom_setting($ged_id, 'GEDCOM_ID_PREFIX');
	global $GENERATE_UIDS;                $GENERATE_UIDS                =get_gedcom_setting($ged_id, 'GENERATE_UIDS');
	global $HIDE_GEDCOM_ERRORS;           $HIDE_GEDCOM_ERRORS           =get_gedcom_setting($ged_id, 'HIDE_GEDCOM_ERRORS');
	global $HIDE_LIVE_PEOPLE;             $HIDE_LIVE_PEOPLE             =get_gedcom_setting($ged_id, 'HIDE_LIVE_PEOPLE');
	global $KEEP_ALIVE_YEARS_BIRTH;       $KEEP_ALIVE_YEARS_BIRTH       =get_gedcom_setting($ged_id, 'KEEP_ALIVE_YEARS_BIRTH');
	global $KEEP_ALIVE_YEARS_DEATH;       $KEEP_ALIVE_YEARS_DEATH       =get_gedcom_setting($ged_id, 'KEEP_ALIVE_YEARS_DEATH');
	global $LANGUAGE;                     $LANGUAGE                     =get_gedcom_setting($ged_id, 'LANGUAGE');
	global $MAX_ALIVE_AGE;                $MAX_ALIVE_AGE                =get_gedcom_setting($ged_id, 'MAX_ALIVE_AGE');
	global $MAX_DESCENDANCY_GENERATIONS;  $MAX_DESCENDANCY_GENERATIONS  =get_gedcom_setting($ged_id, 'MAX_DESCENDANCY_GENERATIONS');
	global $MAX_PEDIGREE_GENERATIONS;     $MAX_PEDIGREE_GENERATIONS     =get_gedcom_setting($ged_id, 'MAX_PEDIGREE_GENERATIONS');
	global $MEDIA_DIRECTORY;              $MEDIA_DIRECTORY              =get_gedcom_setting($ged_id, 'MEDIA_DIRECTORY');
	global $MEDIA_DIRECTORY_LEVELS;       $MEDIA_DIRECTORY_LEVELS       =get_gedcom_setting($ged_id, 'MEDIA_DIRECTORY_LEVELS');
	global $MEDIA_EXTERNAL;               $MEDIA_EXTERNAL               =get_gedcom_setting($ged_id, 'MEDIA_EXTERNAL');
	global $MEDIA_FIREWALL_ROOTDIR;       $MEDIA_FIREWALL_ROOTDIR       =get_gedcom_setting($ged_id, 'MEDIA_FIREWALL_ROOTDIR', WT_DATA_DIR);
	global $MEDIA_FIREWALL_THUMBS;        $MEDIA_FIREWALL_THUMBS        =get_gedcom_setting($ged_id, 'MEDIA_FIREWALL_THUMBS');
	global $MEDIA_ID_PREFIX;              $MEDIA_ID_PREFIX              =get_gedcom_setting($ged_id, 'MEDIA_ID_PREFIX');
	global $NOTE_ID_PREFIX;               $NOTE_ID_PREFIX               =get_gedcom_setting($ged_id, 'NOTE_ID_PREFIX');
	global $NO_UPDATE_CHAN;               $NO_UPDATE_CHAN               =get_gedcom_setting($ged_id, 'NO_UPDATE_CHAN');
	global $PEDIGREE_FULL_DETAILS;        $PEDIGREE_FULL_DETAILS        =get_gedcom_setting($ged_id, 'PEDIGREE_FULL_DETAILS');
	global $PEDIGREE_LAYOUT;              $PEDIGREE_LAYOUT              =get_gedcom_setting($ged_id, 'PEDIGREE_LAYOUT');
	global $PEDIGREE_SHOW_GENDER;         $PEDIGREE_SHOW_GENDER         =get_gedcom_setting($ged_id, 'PEDIGREE_SHOW_GENDER');
	global $POSTAL_CODE;                  $POSTAL_CODE                  =get_gedcom_setting($ged_id, 'POSTAL_CODE');
	global $PREFER_LEVEL2_SOURCES;        $PREFER_LEVEL2_SOURCES        =get_gedcom_setting($ged_id, 'PREFER_LEVEL2_SOURCES');
	global $QUICK_REQUIRED_FACTS;         $QUICK_REQUIRED_FACTS         =get_gedcom_setting($ged_id, 'QUICK_REQUIRED_FACTS');
	global $QUICK_REQUIRED_FAMFACTS;      $QUICK_REQUIRED_FAMFACTS      =get_gedcom_setting($ged_id, 'QUICK_REQUIRED_FAMFACTS');
	global $REPO_ID_PREFIX;               $REPO_ID_PREFIX               =get_gedcom_setting($ged_id, 'REPO_ID_PREFIX');
	global $REQUIRE_AUTHENTICATION;       $REQUIRE_AUTHENTICATION       =get_gedcom_setting($ged_id, 'REQUIRE_AUTHENTICATION');
	global $SAVE_WATERMARK_IMAGE;         $SAVE_WATERMARK_IMAGE         =get_gedcom_setting($ged_id, 'SAVE_WATERMARK_IMAGE');
	global $SAVE_WATERMARK_THUMB;         $SAVE_WATERMARK_THUMB         =get_gedcom_setting($ged_id, 'SAVE_WATERMARK_THUMB');
	global $SHOW_AGE_DIFF;                $SHOW_AGE_DIFF                =get_gedcom_setting($ged_id, 'SHOW_AGE_DIFF');
	global $SHOW_COUNTER;                 $SHOW_COUNTER                 =get_gedcom_setting($ged_id, 'SHOW_COUNTER');
	global $SHOW_DEAD_PEOPLE;             $SHOW_DEAD_PEOPLE             =get_gedcom_setting($ged_id, 'SHOW_DEAD_PEOPLE');
	global $SHOW_EMPTY_BOXES;             $SHOW_EMPTY_BOXES             =get_gedcom_setting($ged_id, 'SHOW_EMPTY_BOXES');
	global $SHOW_FACT_ICONS;              $SHOW_FACT_ICONS              =get_gedcom_setting($ged_id, 'SHOW_FACT_ICONS');
	global $SHOW_GEDCOM_RECORD;           $SHOW_GEDCOM_RECORD           =get_gedcom_setting($ged_id, 'SHOW_GEDCOM_RECORD');
	global $SHOW_HIGHLIGHT_IMAGES;        $SHOW_HIGHLIGHT_IMAGES        =get_gedcom_setting($ged_id, 'SHOW_HIGHLIGHT_IMAGES');
	global $SHOW_LAST_CHANGE;             $SHOW_LAST_CHANGE             =get_gedcom_setting($ged_id, 'SHOW_LAST_CHANGE');
	global $SHOW_LDS_AT_GLANCE;           $SHOW_LDS_AT_GLANCE           =get_gedcom_setting($ged_id, 'SHOW_LDS_AT_GLANCE');
	global $SHOW_LEVEL2_NOTES;            $SHOW_LEVEL2_NOTES            =get_gedcom_setting($ged_id, 'SHOW_LEVEL2_NOTES');
	global $SHOW_LIVING_NAMES;            $SHOW_LIVING_NAMES            =get_gedcom_setting($ged_id, 'SHOW_LIVING_NAMES');
	global $SHOW_MEDIA_DOWNLOAD;          $SHOW_MEDIA_DOWNLOAD          =get_gedcom_setting($ged_id, 'SHOW_MEDIA_DOWNLOAD');
	global $SHOW_NO_WATERMARK;            $SHOW_NO_WATERMARK            =get_gedcom_setting($ged_id, 'SHOW_NO_WATERMARK');
	global $SHOW_PARENTS_AGE;             $SHOW_PARENTS_AGE             =get_gedcom_setting($ged_id, 'SHOW_PARENTS_AGE');
	global $SHOW_PEDIGREE_PLACES;         $SHOW_PEDIGREE_PLACES         =get_gedcom_setting($ged_id, 'SHOW_PEDIGREE_PLACES');
	global $SHOW_PEDIGREE_PLACES_SUFFIX;  $SHOW_PEDIGREE_PLACES_SUFFIX  =get_gedcom_setting($ged_id, 'SHOW_PEDIGREE_PLACES_SUFFIX');
	global $SHOW_PRIVATE_RELATIONSHIPS;   $SHOW_PRIVATE_RELATIONSHIPS   =get_gedcom_setting($ged_id, 'SHOW_PRIVATE_RELATIONSHIPS');
	global $SHOW_REGISTER_CAUTION;        $SHOW_REGISTER_CAUTION        =get_gedcom_setting($ged_id, 'SHOW_REGISTER_CAUTION');
	global $SHOW_RELATIVES_EVENTS;        $SHOW_RELATIVES_EVENTS        =get_gedcom_setting($ged_id, 'SHOW_RELATIVES_EVENTS');
	global $SOURCE_ID_PREFIX;             $SOURCE_ID_PREFIX             =get_gedcom_setting($ged_id, 'SOURCE_ID_PREFIX');
	global $SURNAME_LIST_STYLE;           $SURNAME_LIST_STYLE           =get_gedcom_setting($ged_id, 'SURNAME_LIST_STYLE');
	global $THUMBNAIL_WIDTH;              $THUMBNAIL_WIDTH              =get_gedcom_setting($ged_id, 'THUMBNAIL_WIDTH');
	global $UNDERLINE_NAME_QUOTES;        $UNDERLINE_NAME_QUOTES        =get_gedcom_setting($ged_id, 'UNDERLINE_NAME_QUOTES');
	global $USE_GEONAMES;                 $USE_GEONAMES                 =get_gedcom_setting($ged_id, 'USE_GEONAMES');
	global $USE_MEDIA_FIREWALL;           $USE_MEDIA_FIREWALL           =get_gedcom_setting($ged_id, 'USE_MEDIA_FIREWALL');
	global $USE_MEDIA_VIEWER;             $USE_MEDIA_VIEWER             =get_gedcom_setting($ged_id, 'USE_MEDIA_VIEWER');
	global $USE_RIN;                      $USE_RIN                      =get_gedcom_setting($ged_id, 'USE_RIN');
	global $USE_SILHOUETTE;               $USE_SILHOUETTE               =get_gedcom_setting($ged_id, 'USE_SILHOUETTE');
	global $WATERMARK_THUMB;              $WATERMARK_THUMB              =get_gedcom_setting($ged_id, 'WATERMARK_THUMB');
	global $WEBMASTER_USER_ID;            $WEBMASTER_USER_ID            =get_gedcom_setting($ged_id, 'WEBMASTER_USER_ID');
	global $WEBTREES_EMAIL;               $WEBTREES_EMAIL               =get_gedcom_setting($ged_id, 'WEBTREES_EMAIL');
	global $WELCOME_TEXT_AUTH_MODE;       $WELCOME_TEXT_AUTH_MODE       =get_gedcom_setting($ged_id, 'WELCOME_TEXT_AUTH_MODE');
	global $WELCOME_TEXT_CUST_HEAD;       $WELCOME_TEXT_CUST_HEAD       =get_gedcom_setting($ged_id, 'WELCOME_TEXT_CUST_HEAD');
	global $WORD_WRAPPED_NOTES;           $WORD_WRAPPED_NOTES           =get_gedcom_setting($ged_id, 'WORD_WRAPPED_NOTES');

	global $person_privacy; $person_privacy=array();
	global $person_facts;   $person_facts  =array();
	global $global_facts;   $global_facts  =array();

	$rows=WT_DB::prepare(
		"SELECT SQL_CACHE xref, tag_type, CASE resn WHEN 'none' THEN ? WHEN 'privacy' THEN ? WHEN 'confidential' THEN ? WHEN 'hidden' THEN ? END AS resn FROM `##default_resn` WHERE gedcom_id=?"
	)->execute(array(WT_PRIV_PUBLIC, WT_PRIV_USER, WT_PRIV_NONE, WT_PRIV_HIDE, $ged_id))->fetchAll();

	foreach ($rows as $row) {
		if ($row->xref!==null) {
			if ($row->tag_type!==null) {
				$person_facts[$row->xref][$row->tag_type]=(int)$row->resn;
			} else {
				$person_privacy[$row->xref]=(int)$row->resn;
			}
		} else {
			$global_facts[$row->tag_type]=(int)$row->resn;
		}
	}
}

/**
 * Webtrees Error Handling function
 *
 * This function will be called by PHP whenever an error occurs.  The error handling
 * is set in the session.php
 * @see http://us2.php.net/manual/en/function.set-error-handler.php
 */
function wt_error_handler($errno, $errstr, $errfile, $errline) {
	if ((error_reporting() > 0)&&($errno<2048)) {
		if (WT_ERROR_LEVEL==0) {
			return;
		}
		if (stristr($errstr, "by reference")==true) {
			return;
		}
		$fmt_msg="<br>ERROR {$errno}: {$errstr}<br>";
		$log_msg="ERROR {$errno}: {$errstr};";
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
					$log_msg.="\n0 Error occurred on ";
				} else {
					$fmt_msg.="{$i} called from ";
					$log_msg.="\n{$i} called from ";
				}
				if (isset($backtrace[$i]["line"]) && isset($backtrace[$i]["file"])) {
					$fmt_msg.="line <b>{$backtrace[$i]['line']}</b> of file <b>".basename($backtrace[$i]['file'])."</b>";
					$log_msg.="line {$backtrace[$i]['line']} of file ".basename($backtrace[$i]['file']);
				}
				if ($i<$num-1) {
					$fmt_msg.=" in function <b>".$backtrace[$i+1]['function']."</b>";
					$log_msg.=" in function ".$backtrace[$i+1]['function'];
				}
				$fmt_msg.="<br>";
			}
		}
		echo $fmt_msg;
		if (function_exists('AddToLog')) {
			AddToLog($log_msg, 'error');
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
	$searchTarget = "~[\n]".$tag."[\s]~";
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
			$subrec = substr($gedrec, $pos1, $pos2-$pos1);
			if (!$ApplyPriv || canDisplayFact($id, $ged_id, $subrec)) {
				if (isset($prev_tags[$fact])) {
					$prev_tags[$fact]++;
				} else {
					$prev_tags[$fact] = 1;
				}
				$repeats[] = trim($subrec)."\n";
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
					$subrec = get_sub_record(1, "1 $fact", $famrec, $prev_tags[$fact]);
					$subrec .= "\n2 _WTS @$spid@\n2 _WTFS @$famid@\n";
					if (!$ApplyPriv || canDisplayFact($id, $ged_id, $subrec)) {
						if (isset($prev_tags[$fact])) {
							$prev_tags[$fact]++;
						} else {
							$prev_tags[$fact] = 1;
						}
						$repeats[] = trim($subrec)."\n";
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
 * @param string $tag The tag to find, use : to delineate subtags
 * @param int $level The gedcom line level of the first tag to find, setting level to 0 will cause it to use 1+ the level of the incoming record
 * @param string $gedrec The gedcom record to get the value from
 * @param int $truncate Should the value be truncated to a certain number of characters
 * @param boolean $convert Should data like dates be converted using the configuration settings
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
		$value = preg_replace("'<br>'", "\n", $value);
		$value = trim($value);
		//-- if it is a date value then convert the date
		if ($convert && $t=="DATE") {
			$g = new WT_Date($value);
			$value = $g->Display();
			if (!empty($truncate)) {
				if (utf8_strlen($value)>$truncate) {
					$value = preg_replace("/\(.+\)/", "", $value);
					//if (utf8_strlen($value)>$truncate) {
						//$value = preg_replace_callback("/([a-zśź]+)/ui", create_function('$matches', 'return utf8_substr($matches[1], 0, 3);'), $value);
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
						$value = utf8_substr(WT_I18N::translate('Male'), 0, 1);
					} elseif ($value=="F") {
						$value = utf8_substr(WT_I18N::translate('Female'), 0, 1);
					} else {
						$value = utf8_substr(WT_I18N::translate_c('unknown gender', 'Unknown'), 0, 1);
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
 * @param string $newline Input GEDCOM subrecord to be worked on
 * @return string $newged Output string with all necessary CONC and CONT lines
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
	$newlines = preg_split("/\n/", rtrim($newline));
	for ($k=0; $k<count($newlines); $k++) {
		if ($k>0) {
			$newlines[$k] = "{$level} CONT ".$newlines[$k];
		}
		$newged .= trim($newlines[$k])."\n";
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
		$newline = "<br>";
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
	$famrec = find_gedcom_record($famid, WT_GED_ID, WT_USER_CAN_EDIT);
	if (empty($famrec)) {
		return false;
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

// ************************************************* START OF MULTIMEDIA FUNCTIONS ********************************* //
/**
 * find the highlighted media object for a gedcom entity
 * 1. Ignore all media objects that are not displayable because of Privacy rules
 * 2. Ignore all media objects with the Highlight option set to "N"
 * 3. Pick the first media object that matches these criteria, in order of preference:
 *    (a) Level 1 object with the Highlight option set to "Y"
 *    (b) Level 1 object with the Highlight option missing or set to other than "Y" or "N"
 *    (c) Level 2 or higher object with the Highlight option set to "Y"
 *    (d) Level 2 or higher object with the Highlight option missing or set to other than "Y" or "N"
 * Criterion (d) will be present in the code but will be commented out for now.
 *
 * @param string $pid the individual, source, or family id
 * @param string $indirec the gedcom record to look in
 * @return array an object array with indexes "thumb" and "file" for thumbnail and filename
 */
function find_highlighted_object($pid, $ged_id, $indirec) {
	global $MEDIA_DIRECTORY, $MEDIA_DIRECTORY_LEVELS, $WT_IMAGES, $MEDIA_EXTERNAL;

	$media   = array();
	$objectA = array();
	$objectB = array();
	$objectC = array();
	$objectD = array();

	//-- find all of the media items for a person
	$media=
		WT_DB::prepare("SELECT m_media, m_file, m_gedrec, mm_gedrec FROM `##media`, `##media_mapping` WHERE m_media=mm_media AND m_gedfile=mm_gedfile AND m_gedfile=? AND mm_gid=? ORDER BY mm_order")
		->execute(array($ged_id, $pid))
		->fetchAll(PDO::FETCH_NUM);

	foreach ($media as $i=>$row) {
		$obj=WT_Media::getInstance($row[0]);
		if ($obj->canDisplayDetails() && canDisplayFact($row[0], $ged_id, $row[3])) {
			$level=0;
			$ct = preg_match("/(\d+) OBJE/", $row[3], $match);
			if ($ct>0) {
				$level = $match[1];
			}
			if (strstr($row[3], "_PRIM ")) {
				$prim = get_gedcom_value('_PRIM', $level+1, $row[3]);
			} else {
				$prim = get_gedcom_value('_PRIM', 1, $row[2]);
			}

			if ($prim=='N') continue; // Skip _PRIM N objects
			$file = check_media_depth($row[1]);
			$thumb = thumbnail_file($row[1], true, false, $pid);
			if ($level == 1) {
				if ($prim == 'Y') {
					if (empty($objectA)) {
						$objectA['file'] = $file;
						$objectA['thumb'] = $thumb;
						$objectA['level'] = $level;
						$objectA['mid'] = $row[0];
					}
				} else {
					if (empty($objectB)) {
						$objectB['file'] = $file;
						$objectB['thumb'] = $thumb;
						$objectB['level'] = $level;
						$objectB['mid'] = $row[0];
					}
				}
			} else {
				if ($prim == 'Y') {
					if (empty($objectC)) {
						$objectC['file'] = $file;
						$objectC['thumb'] = $thumb;
						$objectC['level'] = $level;
						$objectC['mid'] = $row[0];
					}
				} else {
					if (empty($objectD)) {
						$objectD['file'] = $file;
						$objectD['thumb'] = $thumb;
						$objectD['level'] = $level;
						$objectD['mid'] = $row[0];
					}
				}
			}
		}
	}

	if (!empty($objectA)) return $objectA;
	if (!empty($objectB)) return $objectB;
	if (!empty($objectC)) return $objectC;
	//if (!empty($objectD)) return $objectD;

	return array();
}

/**
 * get the full file path
 *
 * get the file path from a media gedcom record
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
	return utf8_strcasecmp(WT_I18N::translate($a), WT_I18N::translate($b));
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
			return $a['anniv']-$b['anniv'];
		}
	} else {
		return $a['jd']-$b['jd'];
	}
}

function event_sort_name($a, $b) {
	if ($a['jd']==$b['jd']) {
		return WT_GedcomRecord::compare($a['record'], $b['record']);
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
	if (!empty($a["MEDIASORT"])) {
		$aKey = $a["MEDIASORT"]; // set in get_medialist2 and Media->printLinkedRecords()
	} else {
		if (!empty($a["TITL"])) {
			$aKey = $a["TITL"]; // set in get_medialist
		} else {
			if (!empty($a["titl"])) {
				$aKey = $a["titl"];
			} else {
				if (!empty($a["NAME"])) {
					$aKey = $a["NAME"];
				} else {
					if (!empty($a["name"])) { // set in PrintMediaLinks
						$aKey = $a["name"];
					} else {
						if (!empty($a["FILE"])) {
							$aKey = basename($a["FILE"]); // set in get_medialist
						} else {
							if (!empty($a["file"])) {
								$aKey = basename($a["file"]);
							}
						}
					}
				}
			}
		}
	}

	$bKey = "";
	if (!empty($b["MEDIASORT"])) {
		$bKey = $b["MEDIASORT"];
	} else {
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
	}
	return utf8_strcasecmp($aKey, $bKey, true); // Case-insensitive compare
}
/**
 * sort an array according to the filename
 *
 */

function filesort($a, $b) {
	$aKey = "";
	if (!empty($a["FILESORT"])) {
		$aKey = $a["FILESORT"]; // set in get_medialist2, has already been basename'd
	} else {
		if (!empty($a["FILE"])) {
			$aKey = basename($a["FILE"]); // set in get_medialist
		} else if (!empty($a["file"])) {
			$aKey = basename($a["file"]);
		}
	}

	$bKey = "";
	if (!empty($b["FILESORT"])) {
		$bKey = $b["FILESORT"];
	} else {
		if (!empty($b["FILE"])) {
			$bKey = basename($b["FILE"]);
		} else if (!empty($b["file"])) {
			$bKey = basename($b["file"]);
		}
	}
	return utf8_strcasecmp($aKey, $bKey, true); // Case-insensitive compare
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

	$adate = new WT_Date($amatch[1]);
	$bdate = new WT_Date($bmatch[1]);
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
	foreach ($arr as $event) {
		$event->sortOrder = $order;
		$order++;
		if ($event->getValue("DATE")==NULL || !$event->getDate()->isOk()) $nondated[] = $event;
		else $dated[] = $event;
	}

	//-- sort each type of array
	usort($dated, array("WT_Event", "CompareDate"));
	usort($nondated, array("WT_Event", "CompareType"));

	//-- merge the arrays back together comparing by Facts
	$dc = count($dated);
	$nc = count($nondated);
	$i = 0;
	$j = 0;
	$k = 0;
	// while there is anything in the dated array continue merging
	while ($i<$dc) {
		// compare each fact by type to merge them in order
		if ($j<$nc && WT_Event::CompareType($dated[$i], $nondated[$j])>0) {
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
	while ($j<$nc) {
		$arr[$k] = $nondated[$j];
		$j++;
		$k++;
	}

}

function gedcomsort($a, $b) {
	return utf8_strcasecmp($a["title"], $b["title"]);
}

// ************************************************* START OF MISCELLANEOUS FUNCTIONS ********************************* //
/**
 * Get relationship between two individuals in the gedcom
 *
 * function to calculate the relationship between two people.  It uses heuristics based on the
 * individual's birthyears to try and calculate the shortest path between the two individuals.
 * It uses a node cache to help speed up calculations when using relationship privacy.
 * This cache is indexed using the string "$pid1-$pid2"
 * @param string $pid1 - the ID of the first person to compute the relationship from
 * @param string $pid2 - the ID of the second person to compute the relatiohip to
 * @param bool $followspouse = whether to add spouses to the path
 * @param int $maxlength - the maximum length of path
 * @param bool $ignore_cache - enable or disable the relationship cache
 * @param int $path_to_find - which path in the relationship to find, 0 is the shortest path, 1 is the next shortest path, etc
 */
function get_relationship($pid1, $pid2, $followspouse=true, $maxlength=0, $ignore_cache=false, $path_to_find=0) {
	global $start_time;
	static $NODE_CACHE, $NODE_CACHE_LENGTH;
	if (is_null($NODE_CACHE)) {
		$NODE_CACHE=array();
	}

	$indi = WT_Person::getInstance($pid2);
	//-- check the cache
	if (!$ignore_cache) {
		if (isset($NODE_CACHE["$pid1-$pid2"])) {
			if ($NODE_CACHE["$pid1-$pid2"]=="NOT FOUND") return false;
			if (($maxlength==0)||(count($NODE_CACHE["$pid1-$pid2"]["path"])-1<=$maxlength))
				return $NODE_CACHE["$pid1-$pid2"];
			else
				return false;
		}
		//-- check the cache for person 2's children
		foreach ($indi->getSpouseFamilies(WT_PRIV_HIDE) as $family) {
			foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
				if (isset($NODE_CACHE["$pid1-".$child->getXref()])) {
					if (($maxlength==0)||(count($NODE_CACHE["$pid1-".$child->getXref()]["path"])+1<=$maxlength)) {
						$node1 = $NODE_CACHE["$pid1-".$child->getXref()];
						if ($node1!="NOT FOUND") {
							$node1["path"][] = $pid2;
							$node1["pid"] = $pid2;
							if ($child->getSex()=='F') {
								$node1["relations"][] = "mother";
							} else {
								$node1["relations"][] = "father";
							}
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

		if ((!empty($NODE_CACHE_LENGTH))&&($maxlength>0)) {
			if ($NODE_CACHE_LENGTH>=$maxlength)
				return false;
		}
	}
	//-- end cache checking

	//-- get the birth date of p2 for calculating heuristics
	// removed (temporarily) to fix #880475
	//$bdate2=$indi->getBirthDate();

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
			echo " ";
			if ($count%100 == 0)
				flush();
		}
		$count++;
		if (count($p1nodes)==0) {
			if ($maxlength!=0) {
				if (!isset($NODE_CACHE_LENGTH)) {
					$NODE_CACHE_LENGTH = $maxlength;
				} elseif ($NODE_CACHE_LENGTH<$maxlength) {
					$NODE_CACHE_LENGTH = $maxlength;
				}
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
				//-- heuristic values
				$fatherh = 1;
				$motherh = 1;
				$siblingh = 2;
				$spouseh = 2;
				$childh = 3;

	// removed (temporarily) to fix #880475
	//			//-- generate heuristic values based on the birthdates of the current node and p2
				$indi = WT_Person::getInstance($node['pid']);
	//			$bdate1=$indi->getBirthDate();
	//			if ($bdate1->isOK() && $bdate2->isOK()) {
	//				$yeardiff = ($bdate1->minJD() - $bdate2->minJD()) / 365;
	//				if ($yeardiff < -140) {
	//					$fatherh = 20;
	//					$motherh = 20;
	//					$siblingh = 15;
	//					$spouseh = 15;
	//					$childh = 1;
	//				} else
	//					if ($yeardiff < -100) {
	//						$fatherh = 15;
	//						$motherh = 15;
	//						$siblingh = 10;
	//						$spouseh = 10;
	//						$childh = 1;
	//					} else
	//						if ($yeardiff < -60) {
	//							$fatherh = 10;
	//							$motherh = 10;
	//							$siblingh = 5;
	//							$spouseh = 5;
	//							$childh = 1;
	//						} else
	//							if ($yeardiff < -20) {
	//								$fatherh = 5;
	//								$motherh = 5;
	//								$siblingh = 3;
	//								$spouseh = 3;
	//								$childh = 1;
	//							} else
	//								if ($yeardiff<20) {
	//									$fatherh = 3;
	//									$motherh = 3;
	//									$siblingh = 1;
	//									$spouseh = 1;
	//									$childh = 5;
	//								} else
	//									if ($yeardiff<60) {
	//										$fatherh = 1;
	//										$motherh = 1;
	//										$siblingh = 5;
	//										$spouseh = 2;
	//										$childh = 10;
	//									} else
	//										if ($yeardiff<100) {
	//											$fatherh = 1;
	//											$motherh = 1;
	//											$siblingh = 10;
	//											$spouseh = 3;
	//											$childh = 15;
	//										} else {
	//											$fatherh = 1;
	//											$motherh = 1;
	//											$siblingh = 15;
	//											$spouseh = 4;
	//											$childh = 20;
	//										}
	//			}
				//-- check all parents and siblings of this node
				foreach ($indi->getChildFamilies(WT_PRIV_HIDE) as $family) {
					$visited[$family->getXref()] = true;
					foreach ($family->getSpouses(WT_PRIV_HIDE) as $spouse) {
						if (!isset($visited[$spouse->getXref()])) {
							$node1 = $node;
							$node1["length"]+=$fatherh;
							$node1["path"][] = $spouse->getXref();
							$node1["pid"] = $spouse->getXref();
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
								$visited[$spouse->getXref()] = true;
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
					foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
						if (!isset($visited[$child->getXref()])) {
							$node1 = $node;
							$node1["length"]+=$siblingh;
							$node1["path"][] = $child->getXref();
							$node1["pid"] = $child->getXref();
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
								$visited[$child->getXref()] = true;
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
				}
				//-- check all spouses and children of this node
				foreach ($indi->getSpouseFamilies(WT_PRIV_HIDE) as $family) {
					$visited[$family->getXref()] = true;
					if ($followspouse) {
						foreach ($family->getSpouses(WT_PRIV_HIDE) as $spouse) {
							if (!in_arrayr($spouse->getXref(), $node1) || !isset($visited[$spouse->getXref()])) {
								$node1 = $node;
								$node1["length"]+=$spouseh;
								$node1["path"][] = $spouse->getXref();
								$node1["pid"] = $spouse->getXref();
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
									$visited[$spouse->getXref()] = true;
								$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
							}
						}
					}
					foreach ($family->getChildren(WT_PRIV_HIDE) as $child) {
						if (!isset($visited[$child->getXref()])) {
							$node1 = $node;
							$node1["length"]+=$childh;
							$node1["path"][] = $child->getXref();
							$node1["pid"] = $child->getXref();
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
								$visited[$child->getXref()] = true;
							}
							$NODE_CACHE["$pid1-".$node1["pid"]] = $node1;
						}
					}
				}
			}
		}
		unset($p1nodes[$shortest]);
	} //-- end while loop

	// Convert "generic" relationships into sex-specific ones.
	foreach ($resnode['path'] as $n=>$pid) {
		switch ($resnode['relations'][$n]) {
		case 'parent':
			switch (WT_Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='father'; break;
			case 'F': $resnode['relations'][$n]='mother'; break;
			}
			break;
		case 'child':
			switch (WT_Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='son'; break;
			case 'F': $resnode['relations'][$n]='daughter'; break;
			}
			break;
		case 'spouse':
			switch (WT_Person::getInstance($pid)->getSex()) {
			case 'M': $resnode['relations'][$n]='husband'; break;
			case 'F': $resnode['relations'][$n]='wife'; break;
			}
			break;
		case 'sibling':
			switch (WT_Person::getInstance($pid)->getSex()) {
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
	// Note that every combination must be listed separately, as the same English
	// name can be used for many different relationships.  e.g.
	// brother's wife & husband's sister = sister-in-law.
	//
	// $path is an array of the 12 possible gedcom family relationships:
	// mother/father/parent
	// brother/sister/sibling
	// husband/wife/spouse
	// son/daughter/child
	//
	// This is always the shortest path, so "father, daughter" is "half-sister", not "sister".
	//
	// This is very repetitive in English, but necessary in order to handle the
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

function cousin_name($n, $sex) {
	switch ($sex) {
	case 'M':
		switch ($n) {
		case  1: // I18N: Note that for Italian and Polish, "N'th cousins" are different from English "N'th cousins", and the software has already generated the correct "N" for your language.  You only need to translate - you do not need to convert.  For other languages, if your cousin rules are different from English, please contact the developers.
		         return WT_I18N::translate_c('MALE', 'first cousin');
		case  2: return WT_I18N::translate_c('MALE', 'second cousin');
		case  3: return WT_I18N::translate_c('MALE', 'third cousin');
		case  4: return WT_I18N::translate_c('MALE', 'fourth cousin');
		case  5: return WT_I18N::translate_c('MALE', 'fifth cousin');
		case  6: return WT_I18N::translate_c('MALE', 'sixth cousin');
		case  7: return WT_I18N::translate_c('MALE', 'seventh cousin');
		case  8: return WT_I18N::translate_c('MALE', 'eighth cousin');
		case  9: return WT_I18N::translate_c('MALE', 'ninth cousin');
		case 10: return WT_I18N::translate_c('MALE', 'tenth cousin');
		case 11: return WT_I18N::translate_c('MALE', 'eleventh cousin');
		case 12: return WT_I18N::translate_c('MALE', 'twelfth cousin');
		case 13: return WT_I18N::translate_c('MALE', 'thirteenth cousin');
		case 14: return WT_I18N::translate_c('MALE', 'fourteenth cousin');
		case 15: return WT_I18N::translate_c('MALE', 'fifteenth cousin');
		default: return WT_I18N::translate_c('MALE', '%d x cousin', $n);
		}
	case 'F':
		switch ($n) {
		case  1: return WT_I18N::translate_c('FEMALE', 'first cousin');
		case  2: return WT_I18N::translate_c('FEMALE', 'second cousin');
		case  3: return WT_I18N::translate_c('FEMALE', 'third cousin');
		case  4: return WT_I18N::translate_c('FEMALE', 'fourth cousin');
		case  5: return WT_I18N::translate_c('FEMALE', 'fifth cousin');
		case  6: return WT_I18N::translate_c('FEMALE', 'sixth cousin');
		case  7: return WT_I18N::translate_c('FEMALE', 'seventh cousin');
		case  8: return WT_I18N::translate_c('FEMALE', 'eighth cousin');
		case  9: return WT_I18N::translate_c('FEMALE', 'ninth cousin');
		case 10: return WT_I18N::translate_c('FEMALE', 'tenth cousin');
		case 11: return WT_I18N::translate_c('FEMALE', 'eleventh cousin');
		case 12: return WT_I18N::translate_c('FEMALE', 'twelfth cousin');
		case 13: return WT_I18N::translate_c('FEMALE', 'thirteenth cousin');
		case 14: return WT_I18N::translate_c('FEMALE', 'fourteenth cousin');
		case 15: return WT_I18N::translate_c('FEMALE', 'fifteenth cousin');
		default: return WT_I18N::translate_c('FEMALE', '%d x cousin', $n);
		}
	case 'U':
		switch ($n) {
		case  1: return WT_I18N::translate_c('MALE/FEMALE', 'first cousin');
		case  2: return WT_I18N::translate_c('MALE/FEMALE', 'second cousin');
		case  3: return WT_I18N::translate_c('MALE/FEMALE', 'third cousin');
		case  4: return WT_I18N::translate_c('MALE/FEMALE', 'fourth cousin');
		case  5: return WT_I18N::translate_c('MALE/FEMALE', 'fifth cousin');
		case  6: return WT_I18N::translate_c('MALE/FEMALE', 'sixth cousin');
		case  7: return WT_I18N::translate_c('MALE/FEMALE', 'seventh cousin');
		case  8: return WT_I18N::translate_c('MALE/FEMALE', 'eighth cousin');
		case  9: return WT_I18N::translate_c('MALE/FEMALE', 'ninth cousin');
		case 10: return WT_I18N::translate_c('MALE/FEMALE', 'tenth cousin');
		case 11: return WT_I18N::translate_c('MALE/FEMALE', 'eleventh cousin');
		case 12: return WT_I18N::translate_c('MALE/FEMALE', 'twelfth cousin');
		case 13: return WT_I18N::translate_c('MALE/FEMALE', 'thirteenth cousin');
		case 14: return WT_I18N::translate_c('MALE/FEMALE', 'fourteenth cousin');
		case 15: return WT_I18N::translate_c('MALE/FEMALE', 'fifteenth cousin');
		default: return WT_I18N::translate_c('MALE/FEMALE', '%d x cousin', $n);
		}
	}
}

// A variation on cousin_name(), for constructs such as "sixth great-nephew"
// Currently used only by Spanish relationship names.
function cousin_name2($n, $sex, $relation) {
	switch ($sex) {
	case 'M':
		switch ($n) {
		case  1: // I18N: A Spanish relationship name, such as third great-nephew
		         return WT_I18N::translate_c('MALE', 'first %s', $relation);
		case  2: return WT_I18N::translate_c('MALE', 'second %s', $relation);
		case  3: return WT_I18N::translate_c('MALE', 'third %s', $relation);
		case  4: return WT_I18N::translate_c('MALE', 'fourth %s', $relation);
		case  5: return WT_I18N::translate_c('MALE', 'fifth %s', $relation);
		default: // I18N: A Spanish relationship name, such as third great-nephew
		         return WT_I18N::translate_c('MALE', '%1$d x %2$s', $n, $relation);
		}
	case 'F':
		switch ($n) {
		case  1: // I18N: A Spanish relationship name, such as third great-nephew
		         return WT_I18N::translate_c('FEMALE', 'first %s', $relation);
		case  2: return WT_I18N::translate_c('FEMALE', 'second %s', $relation);
		case  3: return WT_I18N::translate_c('FEMALE', 'third %s', $relation);
		case  4: return WT_I18N::translate_c('FEMALE', 'fourth %s', $relation);
		case  5: return WT_I18N::translate_c('FEMALE', 'fifth %s', $relation);
		default: // I18N: A Spanish relationship name, such as third great-nephew
		         return WT_I18N::translate_c('FEMALE', '%1$d x %2$s', $n, $relation);
		}
	case 'U':
		switch ($n) {
		case  1: // I18N: A Spanish relationship name, such as third great-nephew
		         return WT_I18N::translate_c('MALE/FEMALE', 'first %s', $relation);
		case  2: return WT_I18N::translate_c('MALE/FEMALE', 'second %s', $relation);
		case  3: return WT_I18N::translate_c('MALE/FEMALE', 'third %s', $relation);
		case  4: return WT_I18N::translate_c('MALE/FEMALE', 'fourth %s', $relation);
		case  5: return WT_I18N::translate_c('MALE/FEMALE', 'fifth %s', $relation);
		default: // I18N: A Spanish relationship name, such as third great-nephew
		         return WT_I18N::translate_c('MALE/FEMALE', '%1$d x %2$s', $n, $relation);
		}
	}
}


function get_relationship_name_from_path($path, $pid1, $pid2) {
	if (!preg_match('/^(mot|fat|par|hus|wif|spo|son|dau|chi|bro|sis|sib)*$/', $path)) {
		// TODO: Update all the "3 RELA " values in class_person
		return '<span class="error">'.$path.'</span>';
	}
	$person1=$pid1 ? WT_Person::GetInstance($pid1) : null;
	$person2=$pid2 ? WT_Person::GetInstance($pid2) : null;
	// The path does not include the starting person.  In some languages, the
	// translation for a man's (relative) is different to a woman's (relative),
	// due to inflection.
	$sex1=$person1 ? $person1->getSex() : 'U';

	// The sex of the last person in the relationship determines the name in
	// many cases.  e.g. great-aunt / great-uncle
	if (preg_match('/(fat|hus|son|bro)$/', $path)) {
		$sex2='M';
	} elseif (preg_match('/(mot|wif|dau|sis)$/', $path)) {
		$sex2='F';
	} else {
		$sex2='U';
	}

	switch ($path) {
	case '': return WT_I18N::translate('self');

	//  Level One relationships
	case 'mot': return WT_I18N::translate('mother');
	case 'fat': return WT_I18N::translate('father');
	case 'par': return WT_I18N::translate('parent');
	case 'hus': return WT_I18N::translate('husband');
	case 'wif': return WT_I18N::translate('wife');
	case 'spo': return WT_I18N::translate('spouse');
	case 'son': return WT_I18N::translate('son');
	case 'dau': return WT_I18N::translate('daughter');
	case 'chi': return WT_I18N::translate('child');
	case 'bro':
		if ($person1 && $person2) {
			$dob1=$person1->getBirthDate();
			$dob2=$person2->getBirthDate();
			if ($dob1->isOK() && $dob2->isOK()) {
				if (abs($dob1->JD()-$dob2->JD())<2) {
					return WT_I18N::translate('twin brother');
				} else if ($dob1->JD()<$dob2->JD()) {
					return WT_I18N::translate('younger brother');
				} else {
					return WT_I18N::translate('elder brother');
				}
			}
		}
		return WT_I18N::translate('brother');
	case 'sis':
		if ($person1 && $person2) {
			$dob1=$person1->getBirthDate();
			$dob2=$person2->getBirthDate();
			if ($dob1->isOK() && $dob2->isOK()) {
				if (abs($dob1->JD()-$dob2->JD())<2) {
					return WT_I18N::translate('twin sister');
				} else if ($dob1->JD()<$dob2->JD()) {
					return WT_I18N::translate('younger sister');
				} else {
					return WT_I18N::translate('elder sister');
				}
			}
		}
		return WT_I18N::translate('sister');
	case 'sib':
		if ($person1 && $person2) {
			$dob1=$person1->getBirthDate();
			$dob2=$person2->getBirthDate();
			if ($dob1->isOK() && $dob2->isOK()) {
				if (abs($dob1->JD()-$dob2->JD())<2) {
					return WT_I18N::translate('twin sibling');
				} else if ($dob1->JD()<$dob2->JD()) {
					return WT_I18N::translate('younger sibling');
				} else {
					return WT_I18N::translate('elder sibling');
				}
			}
		}
		return WT_I18N::translate('sibling');

	// Level Two relationships
	case 'brochi': return WT_I18N::translate_c('brother\'s child', 'nephew/niece');
	case 'brodau': return WT_I18N::translate_c('brother\'s daughter', 'niece');
	case 'broson': return WT_I18N::translate_c('brother\'s son', 'nephew');
	case 'browif': return WT_I18N::translate_c('brother\'s wife', 'sister-in-law');
	case 'chichi': return WT_I18N::translate_c('child\'s child', 'grandchild');
	case 'chidau': return WT_I18N::translate_c('child\'s daughter', 'granddaughter');
	case 'chihus': return WT_I18N::translate_c('child\'s husband', 'son-in-law');
	case 'chison': return WT_I18N::translate_c('child\'s son', 'grandson');
	case 'chispo': return WT_I18N::translate_c('child\'s spouse', 'son/daughter-in-law');
	case 'chiwif': return WT_I18N::translate_c('child\'s wife', 'daughter-in-law');
	case 'dauchi': return WT_I18N::translate_c('daughter\'s child', 'grandchild');
	case 'daudau': return WT_I18N::translate_c('daughter\'s daughter', 'granddaughter');
	case 'dauhus': return WT_I18N::translate_c('daughter\'s husband', 'son-in-law');
	case 'dauson': return WT_I18N::translate_c('daughter\'s son', 'grandson');
	case 'fatbro': return WT_I18N::translate_c('father\'s brother', 'uncle');
	case 'fatchi': return WT_I18N::translate_c('father\'s child', 'half-sibling');
	case 'fatdau': return WT_I18N::translate_c('father\'s daughter', 'half-sister');
	case 'fatfat': return WT_I18N::translate_c('father\'s father', 'paternal grandfather');
	case 'fatmot': return WT_I18N::translate_c('father\'s mother', 'paternal grandmother');
	case 'fatpar': return WT_I18N::translate_c('father\'s parent', 'paternal grandparent');
	case 'fatsib': return WT_I18N::translate_c('father\'s sibling', 'aunt/uncle');
	case 'fatsis': return WT_I18N::translate_c('father\'s sister', 'aunt');
	case 'fatson': return WT_I18N::translate_c('father\'s son', 'half-brother');
	case 'fatwif': return WT_I18N::translate_c('father\'s wife', 'step-mother');
	case 'husbro': return WT_I18N::translate_c('husband\'s brother', 'brother-in-law');
	case 'huschi': return WT_I18N::translate_c('husband\'s child', 'step-child');
	case 'husdau': return WT_I18N::translate_c('husband\'s daughter', 'step-daughter');
	case 'husfat': return WT_I18N::translate_c('husband\'s father', 'father-in-law');
	case 'husmot': return WT_I18N::translate_c('husband\'s mother', 'mother-in-law');
	case 'hussib': return WT_I18N::translate_c('husband\'s sibling', 'brother/sister-in-law');
	case 'hussis': return WT_I18N::translate_c('husband\'s sister', 'sister-in-law');
	case 'husson': return WT_I18N::translate_c('husband\'s son', 'step-son');
	case 'motbro': return WT_I18N::translate_c('mother\'s brother', 'uncle');
	case 'motchi': return WT_I18N::translate_c('mother\'s child', 'half-sibling');
	case 'motdau': return WT_I18N::translate_c('mother\'s daughter', 'half-sister');
	case 'motfat': return WT_I18N::translate_c('mother\'s father', 'maternal grandfather');
	case 'mothus': return WT_I18N::translate_c('mother\'s husband', 'step-father');
	case 'motmot': return WT_I18N::translate_c('mother\'s mother', 'maternal grandmother');
	case 'motpar': return WT_I18N::translate_c('mother\'s parent', 'maternal grandparent');
	case 'motsib': return WT_I18N::translate_c('mother\'s sibling', 'aunt/uncle');
	case 'motsis': return WT_I18N::translate_c('mother\'s sister', 'aunt');
	case 'motson': return WT_I18N::translate_c('mother\'s son', 'half-brother');
	case 'parbro': return WT_I18N::translate_c('parent\'s brother', 'uncle');
	case 'parchi': return WT_I18N::translate_c('parent\'s child', 'half-sibling');
	case 'pardau': return WT_I18N::translate_c('parent\'s daughter', 'half-sister');
	case 'parfat': return WT_I18N::translate_c('parent\'s father', 'grandfather');
	case 'parmot': return WT_I18N::translate_c('parent\'s mother', 'grandmother');
	case 'parpar': return WT_I18N::translate_c('parent\'s parent', 'grandparent');
	case 'parsib': return WT_I18N::translate_c('parent\'s sibling', 'aunt/uncle');
	case 'parsis': return WT_I18N::translate_c('parent\'s sister', 'aunt');
	case 'parson': return WT_I18N::translate_c('parent\'s son', 'half-brother');
	case 'parspo': return WT_I18N::translate_c('parent\'s spouse', 'step-parent');
	case 'sibchi': return WT_I18N::translate_c('sibling\'s child', 'nephew/niece');
	case 'sibdau': return WT_I18N::translate_c('sibling\'s daughter', 'niece');
	case 'sibson': return WT_I18N::translate_c('sibling\'s son', 'nephew');
	case 'sibspo': return WT_I18N::translate_c('sibling\'s spouse', 'brother/sister-in-law');
	case 'sischi': return WT_I18N::translate_c('sister\'s child', 'nephew/niece');
	case 'sisdau': return WT_I18N::translate_c('sister\'s daughter', 'niece');
	case 'sishus': return WT_I18N::translate_c('sister\'s husband', 'brother-in-law');
	case 'sisson': return WT_I18N::translate_c('sister\'s son', 'nephew');
	case 'sonchi': return WT_I18N::translate_c('son\'s child', 'grandchild');
	case 'sondau': return WT_I18N::translate_c('son\'s daughter', 'granddaughter');
	case 'sonson': return WT_I18N::translate_c('son\'s son', 'grandson');
	case 'sonwif': return WT_I18N::translate_c('son\'s wife', 'daughter-in-law');
	case 'spobro': return WT_I18N::translate_c('spouses\'s brother', 'brother-in-law');
	case 'spochi': return WT_I18N::translate_c('spouses\'s child', 'step-child');
	case 'spodau': return WT_I18N::translate_c('spouses\'s daughter', 'step-daughter');
	case 'spofat': return WT_I18N::translate_c('spouses\'s father', 'father-in-law');
	case 'spomot': return WT_I18N::translate_c('spouses\'s mother', 'mother-in-law');
	case 'sposis': return WT_I18N::translate_c('spouses\'s sister', 'sister-in-law');
	case 'sposon': return WT_I18N::translate_c('spouses\'s son', 'step-son');
	case 'spopar': return WT_I18N::translate_c('spouses\'s parent', 'mother/father-in-law');
	case 'sposib': return WT_I18N::translate_c('spouses\'s sibling', 'brother/sister-in-law');
	case 'wifbro': return WT_I18N::translate_c('wife\'s brother', 'brother-in-law');
	case 'wifchi': return WT_I18N::translate_c('wife\'s child', 'step-child');
	case 'wifdau': return WT_I18N::translate_c('wife\'s daughter', 'step-daughter');
	case 'wiffat': return WT_I18N::translate_c('wife\'s father', 'father-in-law');
	case 'wifmot': return WT_I18N::translate_c('wife\'s mother', 'mother-in-law');
	case 'wifsib': return WT_I18N::translate_c('wife\'s sibling', 'brother/sister-in-law');
	case 'wifsis': return WT_I18N::translate_c('wife\'s sister', 'sister-in-law');
	case 'wifson': return WT_I18N::translate_c('wife\'s son', 'step-son');

	// Level Three relationships
	// I have commented out some of the unknown-sex relationships that are unlikely to to occur.
	// Feel free to add them in, if you think they might be needed
	case 'brochichi': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s child\'s child',       'great-nephew/niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s child\'s child',     'great-nephew/niece');
	case 'brochidau': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s child\'s daughter',    'great-niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s child\'s daughter',  'great-niece');
	case 'brochison': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s child\'s son',         'great-nephew');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s child\'s son',       'great-nephew');
	case 'brodauchi': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s daughter\'s child',    'great-nephew/niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s daughter\'s child',  'great-nephew/niece');
	case 'brodaudau': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s daughter\'s daughter', 'great-niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s daughter\'s daughter', 'great-niece');
	case 'brodauhus': return WT_I18N::translate_c('brother\'s daughter\'s husband',   'nephew-in-law');
	case 'brodauson': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s daughter\'s son',      'great-nephew');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s daughter\'s son',    'great-nephew');
	case 'brosonchi': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s son\'s child',         'great-nephew/niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s son\'s child',       'great-nephew/niece');
	case 'brosondau': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s son\'s daughter',      'great-niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s son\'s daughter',    'great-niece');
	case 'brosonson': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) brother\'s son\'s son',           'great-nephew');
	                  else            return WT_I18N::translate_c('(a woman\'s) brother\'s son\'s son',         'great-nephew');
	case 'brosonwif': return WT_I18N::translate_c('brother\'s son\'s wife',           'niece-in-law');
	case 'browifbro': return WT_I18N::translate_c('brother\'s wife\'s brother',       'brother-in-law');
	case 'browifsib': return WT_I18N::translate_c('brother\'s wife\'s sibling',       'brother/sister-in-law');
	case 'browifsis': return WT_I18N::translate_c('brother\'s wife\'s sister',        'sister-in-law');
	case 'chichichi': return WT_I18N::translate_c('child\'s child\'s child',          'great-grandchild');
	case 'chichidau': return WT_I18N::translate_c('child\'s child\'s daughter',       'great-granddaughter');
	case 'chichison': return WT_I18N::translate_c('child\'s child\'s son',            'great-grandson');
	case 'chidauchi': return WT_I18N::translate_c('child\'s daughter\'s child',       'great-grandchild');
	case 'chidaudau': return WT_I18N::translate_c('child\'s daughter\'s daughter',    'great-granddaughter');
	case 'chidauhus': return WT_I18N::translate_c('child\'s daughter\'s husband',     'granddaughter\'s husband');
	case 'chidauson': return WT_I18N::translate_c('child\'s daughter\'s son',         'great-grandson');
	case 'chisonchi': return WT_I18N::translate_c('child\'s son\'s child',            'great-grandchild');
	case 'chisondau': return WT_I18N::translate_c('child\'s son\'s daughter',         'great-granddaughter');
	case 'chisonson': return WT_I18N::translate_c('child\'s son\'s son',              'great-grandson');
	case 'chisonwif': return WT_I18N::translate_c('child\'s son\'s wife',             'grandson\'s wife');
//case 'chispomot': return WT_I18N::translate_c('child\'s spouse\'s mother',        'daughter/son-in-law\'s father');
//case 'chispofat': return WT_I18N::translate_c('child\'s spouse\'s father',        'daughter/son-in-law\'s father');
//case 'chispopar': return WT_I18N::translate_c('child\'s spouse\'s parent',        'daughter/son-in-law\'s parent');
	case 'dauchichi': return WT_I18N::translate_c('daughter\'s child\'s child',       'great-grandchild');
	case 'dauchidau': return WT_I18N::translate_c('daughter\'s child\'s daughter',    'great-granddaughter');
	case 'dauchison': return WT_I18N::translate_c('daughter\'s child\'s son',         'great-grandson');
	case 'daudauchi': return WT_I18N::translate_c('daughter\'s daughter\'s child',    'great-grandchild');
	case 'daudaudau': return WT_I18N::translate_c('daughter\'s daughter\'s daughter', 'great-granddaughter');
	case 'daudauhus': return WT_I18N::translate_c('daughter\'s daughter\'s husband',  'granddaughter\'s husband');
	case 'daudauson': return WT_I18N::translate_c('daughter\'s daughter\'s son',      'great-grandson');
	case 'dauhusfat': return WT_I18N::translate_c('daughter\'s husband\'s father',    'son-in-law\'s father');
	case 'dauhusmot': return WT_I18N::translate_c('daughter\'s husband\'s mother',    'son-in-law\'s mother');
	case 'dauhuspar': return WT_I18N::translate_c('daughter\'s husband\'s parent',    'son-in-law\'s parent');
	case 'dausonchi': return WT_I18N::translate_c('daughter\'s son\'s child',         'great-grandchild');
	case 'dausondau': return WT_I18N::translate_c('daughter\'s son\'s daughter',      'great-granddaughter');
	case 'dausonson': return WT_I18N::translate_c('daughter\'s son\'s son',           'great-grandson');
	case 'dausonwif': return WT_I18N::translate_c('daughter\'s son\'s wife',          'grandson\'s wife');
	case 'fatbrochi': return WT_I18N::translate_c('father\'s brother\'s child',       'first cousin');
	case 'fatbrodau': return WT_I18N::translate_c('father\'s brother\'s daughter',    'first cousin');
	case 'fatbroson': return WT_I18N::translate_c('father\'s brother\'s son',         'first cousin');
	case 'fatbrowif': return WT_I18N::translate_c('father\'s brother\'s wife',        'aunt');
	case 'fatfatbro': return WT_I18N::translate_c('father\'s father\'s brother',      'great-uncle');
	case 'fatfatfat': return WT_I18N::translate_c('father\'s father\'s father',       'great-grandfather');
	case 'fatfatmot': return WT_I18N::translate_c('father\'s father\'s mother',       'great-grandmother');
	case 'fatfatpar': return WT_I18N::translate_c('father\'s father\'s parent',       'great-grandparent');
	case 'fatfatsib': return WT_I18N::translate_c('father\'s father\'s sibling',      'great-aunt/uncle');
	case 'fatfatsis': return WT_I18N::translate_c('father\'s father\'s sister',       'great-aunt');
	case 'fatmotbro': return WT_I18N::translate_c('father\'s mother\'s brother',      'great-uncle');
	case 'fatmotfat': return WT_I18N::translate_c('father\'s mother\'s father',       'great-grandfather');
	case 'fatmotmot': return WT_I18N::translate_c('father\'s mother\'s mother',       'great-grandmother');
	case 'fatmotpar': return WT_I18N::translate_c('father\'s mother\'s parent',       'great-grandparent');
	case 'fatmotsib': return WT_I18N::translate_c('father\'s mother\'s sibling',      'great-aunt/uncle');
	case 'fatmotsis': return WT_I18N::translate_c('father\'s mother\'s sister',       'great-aunt');
	case 'fatparbro': return WT_I18N::translate_c('father\'s parent\'s brother',      'great-uncle');
	case 'fatparfat': return WT_I18N::translate_c('father\'s parent\'s father',       'great-grandfather');
	case 'fatparmot': return WT_I18N::translate_c('father\'s parent\'s mother',       'great-grandmother');
	case 'fatparpar': return WT_I18N::translate_c('father\'s parent\'s parent',       'great-grandparent');
	case 'fatparsib': return WT_I18N::translate_c('father\'s parent\'s sibling',      'great-aunt/uncle');
	case 'fatparsis': return WT_I18N::translate_c('father\'s parent\'s sister',       'great-aunt');
	case 'fatsischi': return WT_I18N::translate_c('father\'s sister\'s child',        'first cousin');
	case 'fatsisdau': return WT_I18N::translate_c('father\'s sister\'s daughter',     'first cousin');
	case 'fatsishus': return WT_I18N::translate_c('father\'s sister\'s husband',      'uncle');
	case 'fatsisson': return WT_I18N::translate_c('father\'s sister\'s son',          'first cousin');
	case 'fatwifchi': return WT_I18N::translate_c('father\'s wife\'s child',          'step-sibling');
	case 'fatwifdau': return WT_I18N::translate_c('father\'s wife\'s daughter',       'step-sister');
	case 'fatwifson': return WT_I18N::translate_c('father\'s wife\'s son',            'step-brother');
	case 'husbrowif': return WT_I18N::translate_c('husband\'s brother\'s wife',       'sister-in-law');
//case 'hussibspo': return WT_I18N::translate_c('husband\'s sibling\'s spouse',     'brother/sister-in-law');
	case 'hussishus': return WT_I18N::translate_c('husband\'s sister\'s husband',     'brother-in-law');
	case 'motbrochi': return WT_I18N::translate_c('mother\'s brother\'s child',       'first cousin');
	case 'motbrodau': return WT_I18N::translate_c('mother\'s brother\'s daughter',    'first cousin');
	case 'motbroson': return WT_I18N::translate_c('mother\'s brother\'s son',         'first cousin');
	case 'motbrowif': return WT_I18N::translate_c('mother\'s brother\'s wife',        'aunt');
	case 'motfatbro': return WT_I18N::translate_c('mother\'s father\'s brother',      'great-uncle');
	case 'motfatfat': return WT_I18N::translate_c('mother\'s father\'s father',       'great-grandfather');
	case 'motfatmot': return WT_I18N::translate_c('mother\'s father\'s mother',       'great-grandmother');
	case 'motfatpar': return WT_I18N::translate_c('mother\'s father\'s parent',       'great-grandparent');
	case 'motfatsib': return WT_I18N::translate_c('mother\'s father\'s sibling',      'great-aunt/uncle');
	case 'motfatsis': return WT_I18N::translate_c('mother\'s father\'s sister',       'great-aunt');
	case 'mothuschi': return WT_I18N::translate_c('mother\'s husband\'s child',       'step-sibling');
	case 'mothusdau': return WT_I18N::translate_c('mother\'s husband\'s daughter',    'step-sister');
	case 'mothusson': return WT_I18N::translate_c('mother\'s husband\'s son',         'step-brother');
	case 'motmotbro': return WT_I18N::translate_c('mother\'s mother\'s brother',      'great-uncle');
	case 'motmotfat': return WT_I18N::translate_c('mother\'s mother\'s father',       'great-grandfather');
	case 'motmotmot': return WT_I18N::translate_c('mother\'s mother\'s mother',       'great-grandmother');
	case 'motmotpar': return WT_I18N::translate_c('mother\'s mother\'s parent',       'great-grandparent');
	case 'motmotsib': return WT_I18N::translate_c('mother\'s mother\'s sibling',      'great-aunt/uncle');
	case 'motmotsis': return WT_I18N::translate_c('mother\'s mother\'s sister',       'great-aunt');
	case 'motparbro': return WT_I18N::translate_c('mother\'s parent\'s brother',      'great-uncle');
	case 'motparfat': return WT_I18N::translate_c('mother\'s parent\'s father',       'great-grandfather');
	case 'motparmot': return WT_I18N::translate_c('mother\'s parent\'s mother',       'great-grandmother');
	case 'motparpar': return WT_I18N::translate_c('mother\'s parent\'s parent',       'great-grandparent');
	case 'motparsib': return WT_I18N::translate_c('mother\'s parent\'s sibling',      'great-aunt/uncle');
	case 'motparsis': return WT_I18N::translate_c('mother\'s parent\'s sister',       'great-aunt');
	case 'motsischi': return WT_I18N::translate_c('mother\'s sister\'s child',        'first cousin');
	case 'motsisdau': return WT_I18N::translate_c('mother\'s sister\'s daughter',     'first cousin');
	case 'motsishus': return WT_I18N::translate_c('mother\'s sister\'s husband',      'uncle');
	case 'motsisson': return WT_I18N::translate_c('mother\'s sister\'s son',          'first cousin');
	case 'parbrowif': return WT_I18N::translate_c('parent\'s brother\'s wife',        'aunt');
	case 'parfatbro': return WT_I18N::translate_c('parent\'s father\'s brother',      'great-uncle');
	case 'parfatfat': return WT_I18N::translate_c('parent\'s father\'s father',       'great-grandfather');
	case 'parfatmot': return WT_I18N::translate_c('parent\'s father\'s mother',       'great-grandmother');
	case 'parfatpar': return WT_I18N::translate_c('parent\'s father\'s parent',       'great-grandparent');
	case 'parfatsib': return WT_I18N::translate_c('parent\'s father\'s sibling',      'great-aunt/uncle');
	case 'parfatsis': return WT_I18N::translate_c('parent\'s father\'s sister',       'great-aunt');
	case 'parmotbro': return WT_I18N::translate_c('parent\'s mother\'s brother',      'great-uncle');
	case 'parmotfat': return WT_I18N::translate_c('parent\'s mother\'s father',       'great-grandfather');
	case 'parmotmot': return WT_I18N::translate_c('parent\'s mother\'s mother',       'great-grandmother');
	case 'parmotpar': return WT_I18N::translate_c('parent\'s mother\'s parent',       'great-grandparent');
	case 'parmotsib': return WT_I18N::translate_c('parent\'s mother\'s sibling',      'great-aunt/uncle');
	case 'parmotsis': return WT_I18N::translate_c('parent\'s mother\'s sister',       'great-aunt');
	case 'parparbro': return WT_I18N::translate_c('parent\'s parent\'s brother',      'great-uncle');
	case 'parparfat': return WT_I18N::translate_c('parent\'s parent\'s father',       'great-grandfather');
	case 'parparmot': return WT_I18N::translate_c('parent\'s parent\'s mother',       'great-grandmother');
	case 'parparpar': return WT_I18N::translate_c('parent\'s parent\'s parent',       'great-grandparent');
	case 'parparsib': return WT_I18N::translate_c('parent\'s parent\'s sibling',      'great-aunt/uncle');
	case 'parparsis': return WT_I18N::translate_c('parent\'s parent\'s sister',       'great-aunt');
	case 'parsishus': return WT_I18N::translate_c('parent\'s sister\'s husband',      'uncle');
	case 'parspochi': return WT_I18N::translate_c('parent\'s spouse\'s child',        'step-sibling');
	case 'parspodau': return WT_I18N::translate_c('parent\'s spouse\'s daughter',     'step-sister');
	case 'parsposon': return WT_I18N::translate_c('parent\'s spouse\'s son',          'step-brother');
	case 'sibchichi': return WT_I18N::translate_c('sibling\'s child\'s child',        'great-nephew/niece');
	case 'sibchidau': return WT_I18N::translate_c('sibling\'s child\'s daughter',     'great-niece');
	case 'sibchison': return WT_I18N::translate_c('sibling\'s child\'s son',          'great-nephew');
	case 'sibdauchi': return WT_I18N::translate_c('sibling\'s daughter\'s child',     'great-nephew/niece');
	case 'sibdaudau': return WT_I18N::translate_c('sibling\'s daughter\'s daughter',  'great-niece');
	case 'sibdauhus': return WT_I18N::translate_c('sibling\'s daughter\'s husband',   'nephew-in-law');
	case 'sibdauson': return WT_I18N::translate_c('sibling\'s daughter\'s son',       'great-nephew');
	case 'sibsonchi': return WT_I18N::translate_c('sibling\'s son\'s child',          'great-nephew/niece');
	case 'sibsondau': return WT_I18N::translate_c('sibling\'s son\'s daughter',       'great-niece');
	case 'sibsonson': return WT_I18N::translate_c('sibling\'s son\'s son',            'great-nephew');
	case 'sibsonwif': return WT_I18N::translate_c('sibling\'s son\'s wife',           'niece-in-law');
//case 'sibspobro': return WT_I18N::translate_c('sibling\'s spouse\'s brother',     'brother-in-law');
//case 'sibsposib': return WT_I18N::translate_c('sibling\'s spouse\'s sibling',     'brother/sister-in-law');
//case 'sibsposis': return WT_I18N::translate_c('sibling\'s spouse\'s sister',      'sister-in-law');
	case 'sischichi': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s child\'s child',          'great-nephew/niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s child\'s child',        'great-nephew/niece');
	case 'sischidau': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s child\'s daughter',       'great-niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s child\'s daughter',     'great-niece');
	case 'sischison': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s child\'s son',            'great-nephew');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s child\'s son',          'great-nephew');
	case 'sisdauchi': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s daughter\'s child',       'great-nephew/niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s daughter\'s child',     'great-nephew/niece');
	case 'sisdaudau': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s daughter\'s daughter',    'great-niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s daughter\'s daughter',  'great-niece');
	case 'sisdauhus': return WT_I18N::translate_c('sisters\'s daughter\'s husband',   'nephew-in-law');
	case 'sisdauson': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s daughter\'s son',         'great-nephew');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s daughter\'s son',       'great-nephew');
	case 'sishusbro': return WT_I18N::translate_c('sister\'s husband\'s brother',     'brother-in-law');
	case 'sishussib': return WT_I18N::translate_c('sister\'s husband\'s sibling',     'brother/sister-in-law');
	case 'sishussis': return WT_I18N::translate_c('sister\'s husband\'s sister',      'sister-in-law');
	case 'sissonchi': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s son\'s child',            'great-nephew/niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s son\'s child',          'great-nephew/niece');
	case 'sissondau': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s son\'s daughter',         'great-niece');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s son\'s daughter',       'great-niece');
	case 'sissonson': if ($sex1=='M') return WT_I18N::translate_c('(a man\'s) sister\'s son\'s son',              'great-nephew');
	                  else            return WT_I18N::translate_c('(a woman\'s) sister\'s son\'s son',            'great-nephew');
	case 'sissonwif': return WT_I18N::translate_c('sisters\'s son\'s wife',           'niece-in-law');
	case 'sonchichi': return WT_I18N::translate_c('son\'s child\'s child',            'great-grandchild');
	case 'sonchidau': return WT_I18N::translate_c('son\'s child\'s daughter',         'great-granddaughter');
	case 'sonchison': return WT_I18N::translate_c('son\'s child\'s son',              'great-grandson');
	case 'sondauchi': return WT_I18N::translate_c('son\'s daughter\'s child',         'great-grandchild');
	case 'sondaudau': return WT_I18N::translate_c('son\'s daughter\'s daughter',      'great-granddaughter');
	case 'sondauhus': return WT_I18N::translate_c('son\'s daughter\'s husband',       'granddaughter\'s husband');
	case 'sondauson': return WT_I18N::translate_c('son\'s daughter\'s son',           'great-grandson');
	case 'sonsonchi': return WT_I18N::translate_c('son\'s son\'s child',              'great-grandchild');
	case 'sonsondau': return WT_I18N::translate_c('son\'s son\'s daughter',           'great-granddaughter');
	case 'sonsonson': return WT_I18N::translate_c('son\'s son\'s son',                'great-grandson');
	case 'sonsonwif': return WT_I18N::translate_c('son\'s son\'s wife',               'grandson\'s wife');
	case 'sonwiffat': return WT_I18N::translate_c('son\'s wife\'s father',            'daughter-in-law\'s father');
	case 'sonwifmot': return WT_I18N::translate_c('son\'s wife\'s mother',            'daughter-in-law\'s mother');
	case 'sonwifpar': return WT_I18N::translate_c('son\'s wife\'s parent',            'daughter-in-law\'s parent');
//case 'spobrowif': return WT_I18N::translate_c('spouse\'s brother\'s wife',        'sister-in-law');
//case 'sposibspo': return WT_I18N::translate_c('spouse\'s sibling\'s spouse',      'brother/sister-in-law');
//case 'sposishus': return WT_I18N::translate_c('spouse\'s sister\'s husband',      'brother-in-law');
	case 'wifbrowif': return WT_I18N::translate_c('wife\'s brother\'s wife',          'sister-in-law');
//case 'wifsibspo': return WT_I18N::translate_c('wife\'s sibling\'s spouse',        'brother/sister-in-law');
	case 'wifsishus': return WT_I18N::translate_c('wife\'s sister\'s husband',        'brother-in-law');

	// Some "special case" level four relationships that have specific names in certain languages
	case 'fatfatbrowif': return WT_I18N::translate_c('father\'s father\'s brother\'s wife',    'great-aunt');
	case 'fatfatsibspo': return WT_I18N::translate_c('father\'s father\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'fatfatsishus': return WT_I18N::translate_c('father\'s father\'s sister\'s husband',  'great-uncle');
	case 'fatmotbrowif': return WT_I18N::translate_c('father\'s mother\'s brother\'s wife',    'great-aunt');
	case 'fatmotsibspo': return WT_I18N::translate_c('father\'s mother\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'fatmotsishus': return WT_I18N::translate_c('father\'s mother\'s sister\'s husband',  'great-uncle');
	case 'fatparbrowif': return WT_I18N::translate_c('father\'s parent\'s brother\'s wife',    'great-aunt');
	case 'fatparsibspo': return WT_I18N::translate_c('father\'s parent\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'fatparsishus': return WT_I18N::translate_c('father\'s parent\'s sister\'s husband',  'great-uncle');
	case 'motfatbrowif': return WT_I18N::translate_c('mother\'s father\'s brother\'s wife',    'great-aunt');
	case 'motfatsibspo': return WT_I18N::translate_c('mother\'s father\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'motfatsishus': return WT_I18N::translate_c('mother\'s father\'s sister\'s husband',  'great-uncle');
	case 'motmotbrowif': return WT_I18N::translate_c('mother\'s mother\'s brother\'s wife',    'great-aunt');
	case 'motmotsibspo': return WT_I18N::translate_c('mother\'s mother\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'motmotsishus': return WT_I18N::translate_c('mother\'s mother\'s sister\'s husband',  'great-uncle');
	case 'motparbrowif': return WT_I18N::translate_c('mother\'s parent\'s brother\'s wife',    'great-aunt');
	case 'motparsibspo': return WT_I18N::translate_c('mother\'s parent\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'motparsishus': return WT_I18N::translate_c('mother\'s parent\'s sister\'s husband',  'great-uncle');
	case 'parfatbrowif': return WT_I18N::translate_c('parent\'s father\'s brother\'s wife',    'great-aunt');
	case 'parfatsibspo': return WT_I18N::translate_c('parent\'s father\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'parfatsishus': return WT_I18N::translate_c('parent\'s father\'s sister\'s husband',  'great-uncle');
	case 'parmotbrowif': return WT_I18N::translate_c('parent\'s mother\'s brother\'s wife',    'great-aunt');
	case 'parmotsibspo': return WT_I18N::translate_c('parent\'s mother\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'parmotsishus': return WT_I18N::translate_c('parent\'s mother\'s sister\'s husband',  'great-uncle');
	case 'parparbrowif': return WT_I18N::translate_c('parent\'s parent\'s brother\'s wife',    'great-aunt');
	case 'parparsibspo': return WT_I18N::translate_c('parent\'s parent\'s sibling\'s spouse',  'great-aunt/uncle');
	case 'parparsishus': return WT_I18N::translate_c('parent\'s parent\'s sister\'s husband',  'great-uncle');
	case 'fatfatbrodau': return WT_I18N::translate_c('father\'s father\'s brother\'s daughter','first cousin once removed ascending');
	case 'fatfatbroson': return WT_I18N::translate_c('father\'s father\'s brother\'s son',     'first cousin once removed ascending');
	case 'fatfatbrochi': return WT_I18N::translate_c('father\'s father\'s brother\'s child', 'first cousin once removed ascending');
	case 'fatfatsisdau': return WT_I18N::translate_c('father\'s father\'s sister\'s daughter', 'first cousin once removed ascending');
	case 'fatfatsisson': return WT_I18N::translate_c('father\'s father\'s sister\'s son',      'first cousin once removed ascending');
	case 'fatfatsischi': return WT_I18N::translate_c('father\'s father\'s sister\'s child',    'first cousin once removed ascending');
	case 'fatmotbrodau': return WT_I18N::translate_c('father\'s mother\'s brother\'s daughter','first cousin once removed ascending');
	case 'fatmotbroson': return WT_I18N::translate_c('father\'s mother\'s brother\'s son',     'first cousin once removed ascending');
	case 'fatmotbrochi': return WT_I18N::translate_c('father\'s mother\'s brother\'s child',   'first cousin once removed ascending');
	case 'fatmotsisdau': return WT_I18N::translate_c('father\'s mother\'s sister\'s daughter', 'first cousin once removed ascending');
	case 'fatmotsisson': return WT_I18N::translate_c('father\'s mother\'s sister\'s son',      'first cousin once removed ascending');
	case 'fatmotsischi': return WT_I18N::translate_c('father\'s mother\'s sister\'s child',    'first cousin once removed ascending');
	case 'motfatbrodau': return WT_I18N::translate_c('mother\'s father\'s brother\'s daughter','first cousin once removed ascending');
	case 'motfatbroson': return WT_I18N::translate_c('mother\'s father\'s brother\'s son',     'first cousin once removed ascending');
	case 'motfatbrochi': return WT_I18N::translate_c('mother\'s father\'s brother\'s child',   'first cousin once removed ascending');
	case 'motfatsisdau': return WT_I18N::translate_c('mother\'s father\'s sister\'s daughter', 'first cousin once removed ascending');
	case 'motfatsisson': return WT_I18N::translate_c('mother\'s father\'s sister\'s son',      'first cousin once removed ascending');
	case 'motfatsischi': return WT_I18N::translate_c('mother\'s father\'s sister\'s child',    'first cousin once removed ascending');
	case 'motmotbrodau': return WT_I18N::translate_c('mother\'s mother\'s brother\'s daughter','first cousin once removed ascending');
	case 'motmotbroson': return WT_I18N::translate_c('mother\'s mother\'s brother\'s son',     'first cousin once removed ascending');
	case 'motmotbrochi': return WT_I18N::translate_c('mother\'s mother\'s brother\'s child',   'first cousin once removed ascending');
	case 'motmotsisdau': return WT_I18N::translate_c('mother\'s mother\'s sister\'s daughter', 'first cousin once removed ascending');
	case 'motmotsisson': return WT_I18N::translate_c('mother\'s mother\'s sister\'s son',      'first cousin once removed ascending');
	case 'motmotsischi': return WT_I18N::translate_c('mother\'s mother\'s sister\'s child',    'first cousin once removed ascending');
	}

	// Some "special case" level five relationships that have specific names in certain languages
	if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s brother\'s granddaughter',  'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s brother\'s grandson',       'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatbro(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s brother\'s grandchild',     'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s sister\'s granddaughter',   'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s sister\'s grandson',        'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatsis(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s sister\'s grandchild',      'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s sibling\'s granddaughter',  'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s sibling\'s grandson',       'second cousin');
	} else if (preg_match('/^(mot|fat|par)fatsib(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandfather\'s sibling\'s grandchild',     'second cousin');
	} else if (preg_match('/^(mot|fat|par)motbro(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s brother\'s granddaughter',  'second cousin');
	} else if (preg_match('/^(mot|fat|par)motbro(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s brother\'s grandson',       'second cousin');
	} else if (preg_match('/^(mot|fat|par)motbro(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s brother\'s grandchild',     'second cousin');
	} else if (preg_match('/^(mot|fat|par)motsis(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s sister\'s granddaughter',   'second cousin');
	} else if (preg_match('/^(mot|fat|par)motsis(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s sister\'s grandson',        'second cousin');
	} else if (preg_match('/^(mot|fat|par)motsis(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s sister\'s grandchild',      'second cousin');
	} else if (preg_match('/^(mot|fat|par)motsib(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s sibling\'s granddaughter',  'second cousin');
	} else if (preg_match('/^(mot|fat|par)motsib(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s sibling\'s grandson',       'second cousin');
	} else if (preg_match('/^(mot|fat|par)motsib(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandmother\'s sibling\'s grandchild',     'second cousin');
	} else if (preg_match('/^(mot|fat|par)parbro(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s brother\'s granddaughter',  'second cousin');
	} else if (preg_match('/^(mot|fat|par)parbro(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s brother\'s grandson',       'second cousin');
	} else if (preg_match('/^(mot|fat|par)parbro(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s brother\'s grandchild',     'second cousin');
	} else if (preg_match('/^(mot|fat|par)parsis(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s sister\'s granddaughter',   'second cousin');
	} else if (preg_match('/^(mot|fat|par)parsis(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s sister\'s grandson',        'second cousin');
	} else if (preg_match('/^(mot|fat|par)parsis(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s sister\'s grandchild',      'second cousin');
	} else if (preg_match('/^(mot|fat|par)parsib(son|dau|chi)dau$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s sibling\'s granddaughter',  'second cousin');
	} else if (preg_match('/^(mot|fat|par)parsib(son|dau|chi)son$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s sibling\'s grandson',       'second cousin');
	} else if (preg_match('/^(mot|fat|par)parsib(son|dau|chi)chi$/', $path)) {
		return WT_I18N::translate_c('grandparent\'s sibling\'s grandchild',     'second cousin');
	}

	// Look for generic/pattern relationships.
	// TODO: these are heavily based on English relationship names.
	// We need feedback from other languages to improve this.
	// Dutch has special names for 8 generations of great-great-..., so these need explicit naming
	// Spanish has special names for four but also has two different numbering patterns

	if (preg_match('/^((?:mot|fat|par)+)(bro|sis|sib)$/', $path, $match)) {
		// siblings of direct ancestors
		$up=strlen($match[1])/3;
		$bef_last=substr($path, -6, 3);
		switch ($up) {
		case 3:
			switch ($sex2) {
			case 'M':
				if ($bef_last=='fat')      return WT_I18N::translate_c('great-grandfather\'s brother', 'great-great-uncle');
				else if ($bef_last=='mot') return WT_I18N::translate_c('great-grandmother\'s brother', 'great-great-uncle');
				else                       return WT_I18N::translate_c('great-grandparent\'s brother', 'great-great-uncle');
			case 'F': return WT_I18N::translate('great-great-aunt');
			case 'U': return WT_I18N::translate('great-great-aunt/uncle');
			}
			break;
		case 4:
			switch ($sex2) {
			case 'M':
				if ($bef_last=='fat')      return WT_I18N::translate_c('great-great-grandfather\'s brother', 'great-great-great-uncle');
				else if ($bef_last=='mot') return WT_I18N::translate_c('great-great-grandmother\'s brother', 'great-great-great-uncle');
				else                       return WT_I18N::translate_c('great-great-grandparent\'s brother', 'great-great-great-uncle');
			case 'F': return WT_I18N::translate('great-great-great-aunt');
			case 'U': return WT_I18N::translate('great-great-great-aunt/uncle');
			}
			break;
		case 5:
			switch ($sex2) {
			case 'M':
				if ($bef_last=='fat')      return WT_I18N::translate_c('great-great-great-grandfather\'s brother', 'great x4 uncle');
				else if ($bef_last=='mot') return WT_I18N::translate_c('great-great-great-grandmother\'s brother', 'great x4 uncle');
				else                       return WT_I18N::translate_c('great-great-great-grandparent\'s brother', 'great x4 uncle');
			case 'F': return WT_I18N::translate('great x4 aunt');
			case 'U': return WT_I18N::translate('great x4 aunt/uncle');
			}
			break;
		case 6:
			switch ($sex2) {
			case 'M':
				if ($bef_last=='fat')      return WT_I18N::translate_c('great x4 grandfather\'s brother', 'great x5 uncle');
				else if ($bef_last=='mot') return WT_I18N::translate_c('great x4 grandmother\'s brother', 'great x5 uncle');
				else                       return WT_I18N::translate_c('great x4 grandparent\'s brother', 'great x5 uncle');
			case 'F': return WT_I18N::translate('great x5 aunt');
			case 'U': return WT_I18N::translate('great x5 aunt/uncle');
			}
			break;
		case 7:
			switch ($sex2) {
			case 'M':
				if ($bef_last=='fat')      return WT_I18N::translate_c('great x5 grandfather\'s brother', 'great x6 uncle');
				else if ($bef_last=='mot') return WT_I18N::translate_c('great x5 grandmother\'s brother', 'great x6 uncle');
				else                       return WT_I18N::translate_c('great x5 grandparent\'s brother', 'great x6 uncle');
			case 'F': return WT_I18N::translate('great x6 aunt');
			case 'U': return WT_I18N::translate('great x6 aunt/uncle');
			}
			break;
		case 8:
			switch ($sex2) {
			case 'M':
				if ($bef_last=='fat')      return WT_I18N::translate_c('great x6 grandfather\'s brother', 'great x7 uncle');
				else if ($bef_last=='mot') return WT_I18N::translate_c('great x6 grandmother\'s brother', 'great x7 uncle');
				else                       return WT_I18N::translate_c('great x6 grandparent\'s brother', 'great x7 uncle');
			case 'F': return WT_I18N::translate('great x7 aunt');
			case 'U': return WT_I18N::translate('great x7 aunt/uncle');
			}
			break;
		default:
			// Different languages have different rules for naming generations.
			// An English great x12 uncle is a Danish great x10 uncle.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'da':
				switch ($sex2) {
				case 'M': return WT_I18N::translate('great x%d uncle', $up-4);
				case 'F': return WT_I18N::translate('great x%d aunt', $up-4);
				case 'U': return WT_I18N::translate('great x%d aunt/uncle', $up-4);
				}
			case 'pl':
				switch ($sex2) {
				case 'M':
					if ($bef_last=='fat')      return WT_I18N::translate_c('great x(%d-1) grandfather\'s brother', 'great x%d uncle', $up-2);
					else if ($bef_last=='mot') return WT_I18N::translate_c('great x(%d-1) grandmother\'s brother', 'great x%d uncle', $up-2);
					else                       return WT_I18N::translate_c('great x(%d-1) grandparent\'s brother', 'great x%d uncle', $up-2);
				case 'F': return WT_I18N::translate('great x%d aunt', $up-2);
				case 'U': return WT_I18N::translate('great x%d aunt/uncle', $up-2);
				}
			case 'it': // Source: Michele Locati
			case 'en':
			default:
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
				          return WT_I18N::translate('great x%d uncle', $up-2);
				case 'F': return WT_I18N::translate('great x%d aunt', $up-2);
				case 'U': return WT_I18N::translate('great x%d aunt/uncle', $up-2);
				}
			}
		}
	}
	if (preg_match('/^(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match)) {
		// direct descendants of siblings
		$down=strlen($match[1])/3+1; // Add one, as we count generations from the common ancestor
		$first=substr($path, 0, 3);
		switch ($down) {
		case 4:
			switch ($sex2) {
			case 'M':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-grandson', 'great-great-nephew');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-grandson',  'great-great-nephew');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great-great-nephew', 'great-great-nephew');
				}
			case 'F':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-granddaughter', 'great-great-niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-granddaughter',  'great-great-niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great-great-niece', 'great-great-niece');
				}
			case 'U':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-grandchild', 'great-great-nephew/niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-grandchild',  'great-great-nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great-great-nephew/niece', 'great-great-nephew/niece');
				}
			}
		case 5:
			switch ($sex2) {
			case 'M':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-great-grandson', 'great-great-great-nephew');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-great-grandson',  'great-great-great-nephew');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great-great-great-nephew',  'great-great-great-nephew');
				}
			case 'F':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-great-granddaughter', 'great-great-great-niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-great-granddaughter',  'great-great-great-niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great-great-great-niece',  'great-great-great-niece');
				}
			case 'U':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-great-grandchild', 'great-great-great-nephew/niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-great-grandchild',  'great-great-great-nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great-great-great-nephew/niece',  'great-great-great-nephew/niece');
				}
			}
		case 6:
			switch ($sex2) {
			case 'M':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-great-great-grandson', 'great x4 nephew');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-great-great-grandson',  'great x4 nephew');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great x4 nephew',  'great x4 nephew');
				}
			case 'F':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-great-great-granddaughter', 'great x4 niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-great-great-granddaughter',  'great x4 niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great x4 niece',  'great x4 niece');
				}
			case 'U':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great-great-great-grandchild', 'great x4 nephew/niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great-great-great-grandchild',  'great x4 nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great x4 nephew/niece',  'great x4 nephew/niece');
				}
			}
		case 7:
			switch ($sex2) {
			case 'M':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great x4 grandson', 'great x5 nephew');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great x4 grandson',  'great x5 nephew');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great x5 nephew',  'great x5 nephew');
				}
			case 'F':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great x4 granddaughter', 'great x5 niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great x4 granddaughter',  'great x5 niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great x5 niece',  'great x5 niece');
				}
			case 'U':
				if ($first=='bro' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) brother\'s great x4 grandchild', 'great x5 nephew/niece');
				} else if ($first=='sis' && $sex1=='M') {
					return WT_I18N::translate_c('(a man\'s) sister\'s great x4 grandchild',  'great x5 nephew/niece');
				} else {
					return WT_I18N::translate_c('(a woman\'s) great x5 nephew/niece',  'great x5 nephew/niece');
				}
			}
		default:
			// Different languages have different rules for naming generations.
			// An English great x12 nephew is a Polish great x11 nephew.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'pl': // Source: Lukasz Wilenski
				switch ($sex2) {
				case 'M':
					if ($first=='bro' && $sex1=='M') {
						return WT_I18N::translate_c('(a man\'s) brother\'s great x(%d-1) grandson', 'great x%d nephew', $down-3);
					} else if ($first=='sis' && $sex1=='M') {
						return WT_I18N::translate_c('(a man\'s) sister\'s great x(%d-1) grandson',  'great x%d nephew', $down-3);
					} else
						return WT_I18N::translate_c('(a woman\'s) great x%d nephew',  'great x%d nephew', $down-3);
				case 'F':
					if ($first=='bro' && $sex1=='M') {
						return WT_I18N::translate_c('(a man\'s) brother\'s great x(%d-1) granddaughter', 'great x%d niece', $down-3);
					} else if ($first=='sis' && $sex1=='M') {
						return WT_I18N::translate_c('(a man\'s) sister\'s great x(%d-1) granddaughter',  'great x%d niece', $down-3);
					} else {
						return WT_I18N::translate_c('(a woman\'s) great x%d niece',  'great x%d niece', $down-3);
					}
				case 'U':
					if ($first=='bro' && $sex1=='M') {
						return WT_I18N::translate_c('(a man\'s) brother\'s great x(%d-1) grandchild', 'great x%d nephew/niece', $down-3);
					} else if ($first=='sis' && $sex1=='M') {
						return WT_I18N::translate_c('(a man\'s) sister\'s great x(%d-1) grandchild',  'great x%d nephew/niece', $down-3);
					} else {
						return WT_I18N::translate_c('(a woman\'s) great x%d nephew/niece',  'great x%d nephew/niece', $down-3);
					}
				}
			case 'he': // Source: Meliza Amity
				switch ($sex2) {
				case 'M': return WT_I18N::translate('great x%d nephew', $down-1);
				case 'F': return WT_I18N::translate('great x%d niece', $down-1);
				case 'U': return WT_I18N::translate('great x%d nephew/niece', $down-1);
				}
			case 'it': // Source: Michele Locati.
			case 'en':
			default:
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
				          return WT_I18N::translate('great x%d nephew', $down-2);
				case 'F': return WT_I18N::translate('great x%d niece', $down-2);
				case 'U': return WT_I18N::translate('great x%d nephew/niece', $down-2);
				}
			}
		}
	}
	if (preg_match('/^((?:mot|fat|par)*)$/', $path, $match)) {
		// direct ancestors
		$up=strlen($match[1])/3;
		switch ($up) {
		case 4:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great-great-grandfather');
			case 'F': return WT_I18N::translate('great-great-grandmother');
			case 'U': return WT_I18N::translate('great-great-grandparent');
			}
			break;
		case 5:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great-great-great-grandfather');
			case 'F': return WT_I18N::translate('great-great-great-grandmother');
			case 'U': return WT_I18N::translate('great-great-great-grandparent');
			}
			break;
		case 6:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x4 grandfather');
			case 'F': return WT_I18N::translate('great x4 grandmother');
			case 'U': return WT_I18N::translate('great x4 grandparent');
			}
			break;
		case 7:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x5 grandfather');
			case 'F': return WT_I18N::translate('great x5 grandmother');
			case 'U': return WT_I18N::translate('great x5 grandparent');
			}
			break;
		case 8:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x6 grandfather');
			case 'F': return WT_I18N::translate('great x6 grandmother');
			case 'U': return WT_I18N::translate('great x6 grandparent');
			}
			break;
		case 9:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x7 grandfather');
			case 'F': return WT_I18N::translate('great x7 grandmother');
			case 'U': return WT_I18N::translate('great x7 grandparent');
			}
			break;
		default:
			// Different languages have different rules for naming generations.
			// An English great x12 grandfather is a Danish great x11 grandfather.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'da': // Source: Patrick Sorensen
				switch ($sex2) {
				case 'M': return WT_I18N::translate('great x%d grandfather', $up-3);
				case 'F': return WT_I18N::translate('great x%d grandmother', $up-3);
				case 'U': return WT_I18N::translate('great x%d grandparent', $up-3);
				}
			case 'it': // Source: Michele Locati
			case 'es': // Source: Wes Groleau
				switch ($sex2) {
				case 'M': return WT_I18N::translate('great x%d grandfather', $up);
				case 'F': return WT_I18N::translate('great x%d grandmother', $up);
				case 'U': return WT_I18N::translate('great x%d grandparent', $up);
				}
			case 'fr': // Source: Jacqueline Tetreault
				switch ($sex2) {
				case 'M': return WT_I18N::translate('great x%d grandfather', $up-1);
				case 'F': return WT_I18N::translate('great x%d grandmother', $up-1);
				case 'U': return WT_I18N::translate('great x%d grandparent', $up-1);
				}
			case 'en':
			default:
				switch ($sex2) {
				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
				          return WT_I18N::translate('great x%d grandfather', $up-2);
				case 'F': return WT_I18N::translate('great x%d grandmother', $up-2);
				case 'U': return WT_I18N::translate('great x%d grandparent', $up-2);
				}
			}
		}
	}
	if (preg_match('/^((?:son|dau|chi)*)$/', $path, $match)) {
		// direct descendants
		$up=strlen($match[1])/3;
		switch ($up) {
		case 4:
			switch ($sex2) {
			case 'son': return WT_I18N::translate('great-great-grandson');
			case 'dau': return WT_I18N::translate('great-great-granddaughter');
			case 'chi': return WT_I18N::translate('great-great-grandchild');
			}
			break;
		case 5:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great-great-great-grandson');
			case 'F': return WT_I18N::translate('great-great-great-granddaughter');
			case 'U': return WT_I18N::translate('great-great-great-grandchild');
			}
			break;
		case 6:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x4 grandson');
			case 'F': return WT_I18N::translate('great x4 granddaughter');
			case 'U': return WT_I18N::translate('great x4 grandchild');
			}
			break;
		case 7:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x5 grandson');
			case 'F': return WT_I18N::translate('great x5 granddaughter');
			case 'U': return WT_I18N::translate('great x5 grandchild');
			}
			break;
		case 8:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x6 grandson');
			case 'F': return WT_I18N::translate('great x6 granddaughter');
			case 'U': return WT_I18N::translate('great x6 grandchild');
			}
			break;
		case 9:
			switch ($sex2) {
			case 'M': return WT_I18N::translate('great x7 grandson');
			case 'F': return WT_I18N::translate('great x7 granddaughter');
			case 'U': return WT_I18N::translate('great x7 grandchild');
			}
			break;
		default:
			// Different languages have different rules for naming generations.
			// An English great x12 grandson is a Danish great x11 grandson.
			//
			// Need to find out which languages use which rules.
			switch (WT_LOCALE) {
			case 'da': // Source: Patrick Sorensen
				switch ($sex2) {
				case 'M': return WT_I18N::translate('great x%d grandson',      $up-3);
				case 'F': return WT_I18N::translate('great x%d granddaughter', $up-3);
				case 'U': return WT_I18N::translate('great x%d grandchild',    $up-3);
				}
			case 'en':
			case 'it': // Source: Michele Locati
			case 'es': // Source: Wes Groleau (adding doesn't change behavior, but needs to be better researched)
			default:
				switch ($sex2) {

				case 'M': // I18N: if you need a different number for %d, contact the developers, as a code-change is required
				            return WT_I18N::translate('great x%d grandson',      $up-2);
				case 'F': return WT_I18N::translate('great x%d granddaughter', $up-2);
				case 'U': return WT_I18N::translate('great x%d grandchild',    $up-2);
				}
			}
		}
	}
	if (preg_match('/^((?:mot|fat|par)+)(?:bro|sis|sib)((?:son|dau|chi)+)$/', $path, $match)) {
		// cousins in English
		$ascent  = $match[1];
		$descent = $match[2];
		$up      = strlen($ascent)/3;
		$down    = strlen($descent)/3;
		$cousin=min($up, $down);  // Moved out of switch (en/default case) so that
		$removed=abs($down-$up);  // Spanish (and other languages) can use it, too.

		// Different languages have different rules for naming cousins.  For example,
		// an English "second cousin once removed" is a Polish "cousin of 7th degree".
		//
		// Need to find out which languages use which rules.
		switch (WT_LOCALE) {
		case 'pl': // Source: Lukasz Wilenski
			return cousin_name($up+$down+2, $sex2);
		case 'it':
			// Source: Michele Locati.  See italian_cousins_names.zip
			// http://webtrees.net/forums/8-translation/1200-great-xn-grandparent?limit=6&start=6
			return cousin_name($up+$down-3, $sex2);
		case 'es':
			// Source: Wes Groleau.  See http://UniGen.us/Parentesco.html & http://UniGen.us/Parentesco-D.html
			if ($down==$up) {
				return cousin_name($cousin, $sex2);
			} elseif ($down<$up) {
				return cousin_name2($cousin+1, $sex2, get_relationship_name_from_path('sib' . $descent, null, null));
			} else {
				switch ($sex2) {
				case 'M': return cousin_name2($cousin+1, $sex2, get_relationship_name_from_path('bro' . $descent, null, null));
				case 'F': return cousin_name2($cousin+1, $sex2, get_relationship_name_from_path('sis' . $descent, null, null));
				case 'U': return cousin_name2($cousin+1, $sex2, get_relationship_name_from_path('sib' . $descent, null, null));
				}
			}
		case 'en': // See: http://en.wikipedia.org/wiki/File:CousinTree.svg
		default:
			switch ($removed) {
			case 0:
				return cousin_name($cousin, $sex2);
			case 1:
				if ($up>$down) {
					/* I18N: %s="fifth cousin", etc. http://www.ancestry.com/learn/library/article.aspx?article=2856 */
					return WT_I18N::translate('%s once removed ascending', cousin_name($cousin, $sex2));
				} else {
					/* I18N: %s="fifth cousin", etc. http://www.ancestry.com/learn/library/article.aspx?article=2856 */
					return WT_I18N::translate('%s once removed descending', cousin_name($cousin, $sex2));
				}
			case 2:
				if ($up>$down) {
					/* I18N: %s="fifth cousin", etc. */
					return WT_I18N::translate('%s twice removed ascending', cousin_name($cousin, $sex2));
				} else {
					/* I18N: %s="fifth cousin", etc. */
					return WT_I18N::translate('%s twice removed descending', cousin_name($cousin, $sex2));
				}
			case 3:
				if ($up>$down) {
					/* I18N: %s="fifth cousin", etc. */
					return WT_I18N::translate('%s thrice removed ascending', cousin_name($cousin, $sex2));
				} else {
					/* I18N: %s="fifth cousin", etc. */
					return WT_I18N::translate('%s thrice removed descending', cousin_name($cousin, $sex2));
				}
			default:
				if ($up>$down) {
					/* I18N: %1$s="fifth cousin", etc., %2$d>=4 */
					return WT_I18N::translate('%1$s %2$d times removed ascending', cousin_name($cousin, $sex2), $removed);
				} else {
					/* I18N: %1$s="fifth cousin", etc., %2$d>=4 */
					return WT_I18N::translate('%1$s %2$d times removed descending', cousin_name($cousin, $sex2), $removed);
				}
			}
		}
	}

	// Split the relationship into sub-relationships, e.g., third-cousin's great-uncle.
	// Try splitting at every point, and choose the path with the shorted translated name.

	$relationship=null;
	$path1=substr($path, 0, 3);
	$path2=substr($path, 3);
	while ($path2) {
		$tmp=WT_I18N::translate(
			// I18N: A complex relationship, such as "third-cousin's great-uncle"
			'%1$s\'s %2$s',
			get_relationship_name_from_path($path1, null, null), // TODO: need the actual people
			get_relationship_name_from_path($path2, null, null)
		);
		if (!$relationship || strlen($tmp)<strlen($relationship)) {
			$relationship=$tmp;
		}
		$path1.=substr($path2, 0, 3);
		$path2=substr($path2, 3);
	}
	return $relationship;
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
	static $themes;
	if ($themes===null) {
		$themes = array();
		$d = dir(WT_ROOT.WT_THEMES_DIR);
		while (false !== ($entry = $d->read())) {
			if ($entry[0]!='.' && $entry[0]!='_' && is_dir(WT_ROOT.WT_THEMES_DIR.$entry) && file_exists(WT_ROOT.WT_THEMES_DIR.$entry.'/theme.php')) {
				$themefile = implode('', file(WT_ROOT.WT_THEMES_DIR.$entry.'/theme.php'));
				if (preg_match('/theme_name\s*=\s*"(.*)";/', $themefile, $match)) {
					$themes[WT_I18N::translate('%s', $match[1])] = $entry;
				}
			}
		}
		$d->close();
		uksort($themes, 'utf8_strcasecmp');
	}
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
 * before it can be used
 */
function filename_encode($filename) {
	if (DIRECTORY_SEPARATOR=='\\')
		return utf8_encode($filename);
	else
		return $filename;
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

// Function to build an URL querystring from GET variables
// Optionally, add/replace specified values
function get_query_url($overwrite=null, $separator='&') {
	if (empty($_GET)) {
		$get=array();
	} else {
		$get=$_GET;
	}
	if (is_array($overwrite)) {
		foreach ($overwrite as $key=>$value) {
			$get[$key]=$value;
		}
	}

	$query_string='';
	foreach ($get as $key=>$value) {
		if (!is_array($value)) {
			$query_string.=$separator . rawurlencode($key) . '=' . rawurlencode($value);
		} else {
			foreach ($value as $k=>$v) {
				$query_string.=$separator . rawurlencode($key) . '%5B' . rawurlencode($k) . '%5D=' . rawurlencode($v);
			}
		}
	}
	$query_string=substr($query_string, strlen($separator)); // Remove leading '&amp;'
	if ($query_string) {
		return WT_SCRIPT_NAME.'?'.$query_string;
	} else {
		return WT_SCRIPT_NAME;
	}
}

//This function works with a specified generation limit.  It will completely fill
//the PDF without regard to whether a known person exists in each generation.
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
		$person = WT_Person::getInstance($id);
		$famids = $person->getChildFamilies();
		if (count($famids)>0) {
			if ($show_empty) {
				for ($i=0;$i<$num_skipped;$i++) {
					$list["empty" . $total_num_skipped] = new WT_Person('');
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
					$list["empty" . $total_num_skipped] = new WT_Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
				}
				if ($wife) {
					$list[$wife->getXref()] = $wife;
					$list[$wife->getXref()]->generation = $list[$id]->generation+1;
				} elseif ($show_empty) {
					$list["empty" . $total_num_skipped] = new WT_Person('');
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
					foreach ($childs as $child) {
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
					$list["empty" . $total_num_skipped] = new WT_Person('');
					$list["empty" . $total_num_skipped]->generation = $list[$id]->generation+1;
					$total_num_skipped++;
					$list["empty" . $total_num_skipped] = new WT_Person('');
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
	$person = WT_Person::getInstance($pid);
	if ($person==null) return;
	if (!isset($list[$pid])) {
		$list[$pid] = $person;
	}
	if (!isset($list[$pid]->generation)) {
		$list[$pid]->generation = 0;
	}
	foreach ($person->getSpouseFamilies() as $family) {
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
		foreach ($children as $child) {
			if ($child) {
				$list[$child->getXref()] = $child;
				if (isset($list[$pid]->generation))
					$list[$child->getXref()]->generation = $list[$pid]->generation+1;
				else
					$list[$child->getXref()]->generation = 2;
			}
		}
		if ($generations == -1 || $list[$pid]->generation+1 < $generations) {
			foreach ($children as $child) {
				add_descendancy($list, $child->getXref(), $parents, $generations); // recurse on the childs family
			}
		}
	}
}

/**
 * get the next available xref
 * calculates the next available XREF id for the given type of record
 * @param string $type the type of record, defaults to 'INDI'
 * @return string
 */
function get_new_xref($type='INDI', $ged_id=WT_GED_ID) {
	global $SOURCE_ID_PREFIX, $REPO_ID_PREFIX, $MEDIA_ID_PREFIX, $FAM_ID_PREFIX, $GEDCOM_ID_PREFIX;

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

	$num=
		WT_DB::prepare("SELECT next_id FROM `##next_id` WHERE record_type=? AND gedcom_id=?")
		->execute(array($type, $ged_id))
		->fetchOne();

	// TODO?  If a gedcom file contains *both* inline and object based media, then
	// we could be generating an XREF that we will find later.  Need to scan the
	// entire gedcom for them?

	if (is_null($num)) {
		$num = 1;
		WT_DB::prepare("INSERT INTO `##next_id` (gedcom_id, record_type, next_id) VALUES(?, ?, 1)")
			->execute(array($ged_id, $type));
	}

	while (find_gedcom_record($prefix.$num, $ged_id, true)) {
		// Applications such as ancestry.com generate XREFs with numbers larger than
		// PHP's signed integer.  MySQL can handle large integers.
		$num=WT_DB::prepare("SELECT 1+?")->execute(array($num))->fetchOne();
	}

	//-- the key is the prefix and the number
	$key = $prefix.$num;

	//-- update the next id number in the DB table
	WT_DB::prepare("UPDATE `##next_id` SET next_id=? WHERE record_type=? AND gedcom_id=?")
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
 * Get useful information on how to handle this media file
 */
function mediaFileInfo($fileName, $thumbName, $mid, $name='', $notes='', $admin='', $obeyViewerOption=true) {
	global $THUMBNAIL_WIDTH, $WT_IMAGES;
	global $GEDCOM, $USE_MEDIA_VIEWER;

	$result = array();

	// -- Classify the incoming media file
	if (preg_match('~^https?://~i', $fileName)) $type = 'url_';
	else $type = 'local_';
	if ((preg_match('/\.flv$/i', $fileName) || preg_match('~^https?://.*\.youtube\..*/watch\?~i', $fileName)) && is_dir(WT_ROOT.'js/jw_player')) {
		$type .= 'flv';
	} else if (preg_match('~^https?://picasaweb*\.google\..*/.*/~i', $fileName)) {
		$type .= 'picasa';
	} else if (preg_match('/\.(jpg|jpeg|gif|png)$/i', $fileName)) {
		$type .= 'image';
	} else if (preg_match('/\.(avi|txt)$/i', $fileName)) {
		$type .= 'page';
	} else if (preg_match('/\.mp3$/i', $fileName)) {
		$type .= 'audio';
	} else if (preg_match('/\.pdf$/i', $fileName)) {
		$type .= 'pdf';
	} else if (preg_match('/\.wmv$/i', $fileName)) {
		$type .= 'wmv';
	} else if (strpos($fileName, 'http://maps.google.')===0) {
		$type .= 'streetview';
	} else {
		$type .= 'other';
	}
	// $type is now: (url | local) _ (flv | picasa | image | page | audio | wmv | streetview |other)
	$result['type'] = $type;

	// -- Determine the correct URL to open this media file
	while (true) {
		if (WT_USE_LIGHTBOX && $admin!="ADMIN") {
			// Lightbox is installed
			switch ($type) {
			case 'url_flv':
				$url = 'js/jw_player/flvVideo.php?flvVideo='.rawurlencode($fileName) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . htmlspecialchars($name) . "::" . htmlspecialchars($notes);
				break 2;
			case 'local_flv':
				$url = 'js/jw_player/flvVideo.php?flvVideo='.rawurlencode(WT_SERVER_NAME.WT_SCRIPT_PATH.$fileName) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . htmlspecialchars($name) . "::" . htmlspecialchars($notes);
				break 2;
			case 'url_wmv':
				$url = 'js/jw_player/wmvVideo.php?wmvVideo='.rawurlencode($fileName) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . htmlspecialchars($name) . "::" . htmlspecialchars($notes);
				break 2;
			case 'local_audio':
			case 'local_wmv':
				$url = 'js/jw_player/wmvVideo.php?wmvVideo='.rawurlencode(WT_SERVER_NAME.WT_SCRIPT_PATH.$fileName) . "\" rel='clearbox(500, 392, click)' rev=\"" . $mid . "::" . $GEDCOM . "::" . htmlspecialchars($name) . "::" . htmlspecialchars($notes);
				break 2;
			case 'url_image':
			case 'local_image':
				$url = $fileName . "\" rel=\"clearbox[general]\" rev=\"" . $mid . "::" . $GEDCOM . "::" . htmlspecialchars($name) . "::" . htmlspecialchars($notes);
				break 2;
			case 'url_picasa':
			case 'url_page':
			case 'url_pdf':
			case 'url_other':
			case 'local_page':
			case 'local_pdf':
			// case 'local_other':
				$url = $fileName . "\" rel='clearbox(" . get_module_setting('lightbox', 'LB_URL_WIDTH',  '1000') . ',' . get_module_setting('lightbox', 'LB_URL_HEIGHT', '600') . ", click)' rev=\"" . $mid . '::' . $GEDCOM . '::' . htmlspecialchars($name) . "::" . htmlspecialchars($notes);
				break 2;
			case 'url_streetview':
				if (WT_SCRIPT_NAME != "admin_media.php") {
					echo  '<iframe style="float:left; padding:5px;" width="264" height="176" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'. $fileName. '&amp;output=svembed"></iframe>';
				}
				break 2;
			}
		}

		// Lightbox is not installed or Lightbox is not appropriate for this media type
		switch ($type) {
		case 'url_flv':
			$url = "#\" onclick=\" var winflv = window.open('".'js/jw_player/flvVideo.php?flvVideo='.rawurlencode($fileName) . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
			break 2;
		case 'local_flv':
			$url = "#\" onclick=\" var winflv = window.open('".'js/jw_player/flvVideo.php?flvVideo='.rawurlencode(WT_SERVER_NAME.WT_SCRIPT_PATH.$fileName) . "', 'winflv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winflv.focus();}";
			break 2;
		case 'url_wmv':
			$url = "#\" onclick=\" var winwmv = window.open('".'js/jw_player/wmvVideo.php?wmvVideo='.rawurlencode($fileName) . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
			break 2;
		case 'local_wmv':
		case 'local_audio':
			$url = "#\" onclick=\" var winwmv = window.open('".'js/jw_player/wmvVideo.php?wmvVideo='.rawurlencode(WT_SERVER_NAME.WT_SCRIPT_PATH.$fileName) . "', 'winwmv', 'width=500, height=392, left=600, top=200'); if (window.focus) {winwmv.focus();}";
			break 2;
		case 'url_image':
		case 'local_image':
			$imgsize = findImageSize($fileName);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$url = "#\" onclick=\"var winimg = window.open('".$fileName."', 'winimg', 'width=".$imgwidth.", height=".$imgheight.", left=200, top=200'); if (window.focus) {winimg.focus();}";
			break 2;
		case 'url_picasa':
		case 'url_page':
		case 'url_pdf':
		case 'url_other':
		case 'local_other';
			$url = "#\" onclick=\"var winurl = window.open('".$fileName."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
			break 2;
		case 'local_page':
		case 'local_pdf':
			$url = "#\" onclick=\"var winurl = window.open('".WT_SERVER_NAME.WT_SCRIPT_PATH.$fileName."', 'winurl', 'width=900, height=600, left=200, top=200'); if (window.focus) {winurl.focus();}";
			break 2;
		case 'url_streetview':
			//echo '<iframe style="float:left; padding:5px;" width="264" height="176" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="', $fileName, '&amp;output=svembed"></iframe>';
			//$url = "#";
			break 2;
		}
		if ($USE_MEDIA_VIEWER && $obeyViewerOption) {
			$url = 'mediaviewer.php?mid='.$mid.'&amp;ged='.WT_GEDURL;
		} else {
			$imgsize = findImageSize($fileName);
			$imgwidth = $imgsize[0]+40;
			$imgheight = $imgsize[1]+150;
			$url = "#\" onclick=\"return openImage('".urlencode($fileName)."', $imgwidth, $imgheight);";
		}
		break;
	}
	// At this point, $url describes how to handle the image when its thumbnail is clicked
	if ($type == 'url_streetview') {
		$result['url'] = "#";
	} else {
		$result['url'] = $url;
	}

	// -- Determine the correct thumbnail or pseudo-thumbnail
	$width = '';
	switch ($type) {
		case 'url_flv':
			$thumb = $WT_IMAGES['media_flashrem'];
			break;
		case 'local_flv':
			$thumb = $WT_IMAGES['media_flash'];
			break;
		case 'url_wmv':
			$thumb = $WT_IMAGES['media_wmvrem'];
			break;
		case 'local_wmv':
			$thumb = $WT_IMAGES['media_wmv'];
			break;
		case 'url_picasa':
			$thumb = $WT_IMAGES['media_picasa'];
			break;
		case 'url_page':
		case 'url_other':
			$thumb = $WT_IMAGES['media_globe'];
			break;
		case 'local_page':
			$thumb = $WT_IMAGES['media_doc'];
			break;
		case 'url_pdf':
		case 'local_pdf':
			$thumb = $WT_IMAGES['media_pdf'];
			break;
		case 'url_audio':
		case 'local_audio':
			$thumb = $WT_IMAGES['media_audio'];
			break;
		case 'url_streetview':
			$thumb = null;
			break;
		default:
			$thumb = $thumbName;
			if (substr($type, 0, 4)=='url_') {
				$width = ' width="'.$THUMBNAIL_WIDTH.'"';
			}
	}

	// -- Use an overriding thumbnail if one has been provided
	// Don't accept any overriding thumbnails that are in the "images" or "themes" directories
	$realThumb = $thumb;
	if (strpos($thumbName, 'images/')!==0 && strpos($thumbName, WT_THEMES_DIR)!==0) {
		switch (media_exists($thumbName)) {
			case false: // file doesn't exist
				$thumb = $WT_IMAGES['media'];
				$realThumb = $WT_IMAGES['media'];
				break;
			case 1: // external file
				// do nothing
				break;
			case 2: // file in standard media directory
				$thumb = $thumbName;
				$realThumb = $thumbName;
				break;
			case 3: // file in protected media directory
				$thumb = $thumbName;
				$realThumb = get_media_firewall_path($thumbName);
				break;
		} 
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
		$basename=$path; // We have just a filename
		$dirname='.';    // For compatibility with pathinfo()
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

// Turn URLs in text into HTML links.  Insert breaks into long URLs
// so that the browser can word-wrap.
function expand_urls($text) {
	// Some versions of RFC3987 have an appendix B which gives the following regex
	// (([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?
	// This matches far too much while a "precise" regex is several pages long.
	// This is a compromise.
	$URL_REGEX='((https?|ftp]):)(//([^\s/?#<>]*))?([^\s?#<>]*)(\?([^\s#<>]*))?(#[^\s?#<>]+)?';

	return preg_replace_callback(
		'/'.addcslashes("(?!>)$URL_REGEX(?!</a>)", '/').'/i',
		create_function( // Insert soft hyphens into the replaced string
			'$m',
			'return "<a href=\"".$m[0]."\" target=\"blank\">".preg_replace("/\b/", "&shy;", $m[0])."</a>";'
		),
		preg_replace("/<(?!br)/i", "&lt;", $text) // no html except br
	);
}

// Returns the part of the haystack before the first occurrence of the needle.
// Use it to emulate the before_needle php 5.3.0 strstr function
function strstrb($haystack, $needle){
	return substr($haystack, 0, strpos($haystack, $needle));
}
