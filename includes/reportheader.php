<?php
// Report Header Parser
// used by the SAX parser to generate PDF reports from the XML report file.
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.
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

/**
 * element handlers array
 *
 * An array of element handler functions
 * @global array $elementHandler
 */
$elementHandler = array();
$elementHandler["Report"]["start"]   ="reportStartHandler";
$elementHandler["var"]["start"]      ="varStartHandler";
$elementHandler["Title"]["start"]    ="titleStartHandler";
$elementHandler["Title"]["end"]      ="titleEndHandler";
$elementHandler["Description"]["end"]="descriptionEndHandler";
$elementHandler["Input"]["start"]    ="inputStartHandler";
$elementHandler["Input"]["end"]      ="inputEndHandler";

$text = "";
$report_array = array();

/**
 * xml start element handler
 *
 * this function is called whenever a starting element is reached

 * @param resource $parser the resource handler for the xml parser
 * @param string   $name the name of the xml element parsed
 * @param string[] $attrs an array of key value pairs for the attributes
 */
function startElement($parser, $name, $attrs) {
	global $elementHandler, $processIfs;

	if (($processIfs==0) || ($name=="if")) {
		if (isset($elementHandler[$name]["start"])) {
			call_user_func($elementHandler[$name]["start"], $attrs);
		}
	}
}

/**
 * xml end element handler
 *
 * this function is called whenever an ending element is reached
 * @param resource $parser the resource handler for the xml parser
 * @param string $name the name of the xml element parsed
 */
function endElement($parser, $name) {
	global $elementHandler, $processIfs;

	if (($processIfs==0) || ($name=="if")) {
		if (isset($elementHandler[$name]["end"])) {
			call_user_func($elementHandler[$name]["end"]);
		}
	}
}

/**
 * xml character data handler
 *
 * this function is called whenever raw character data is reached
 * just print it to the screen
 * @param resource $parser the resource handler for the xml parser
 * @param string $data the name of the xml element parsed
 */
function characterData($parser, $data) {
	global $text;

	$text .= $data;
}

/**
 * @param string[] $attrs
 */
function reportStartHandler($attrs) {
	global $report_array;

	$access = WT_PRIV_PUBLIC;
	if (isset($attrs["access"])) {
		if (isset($$attrs["access"])) {
			$access = $$attrs["access"];
		}
	}
	$report_array["access"] = $access;

	if (isset($attrs["icon"])) {
		$report_array["icon"] = $attrs["icon"];
	} else {
		$report_array["icon"] = "";
	}
}

/**
 * @param string[] $attrs
 */
function varStartHandler($attrs) {
	global $text, $fact, $desc, $type;

	$var = $attrs["var"];
	if (!empty($var)) {
		$tfact = $fact;
		if ($fact=="EVEN") {
			$tfact = $type;
		}
		$var = str_replace(array("@fact", "@desc"), array($tfact, $desc), $var);
		if (substr($var, 0, 18)=='WT_I18N::translate' || substr($var, 0, 23)=='WT_Gedcom_Tag::getLabel') {
			eval("\$var=$var;");
		}
		$text .= $var;
	}
}

function titleStartHandler() {
	global $text;

	$text = "";
}

function titleEndHandler() {
	global $report_array, $text;

	$report_array["title"] = $text;
	$text = "";
}

function descriptionEndHandler() {
	global $report_array, $text;

	$report_array["description"] = $text;
	$text = "";
}

/**
 * @param string[] $attrs
 */
function inputStartHandler($attrs) {
	global $input, $text;

	$text ="";
	$input = array();
	$input["name"] = "";
	$input["type"] = "";
	$input["lookup"] = "";
	$input["default"] = "";
	$input["value"] = "";
	$input["options"] = "";
	if (isset($attrs["name"])) {
		$input["name"] = $attrs["name"];
	}
	if (isset($attrs["type"])) {
		$input["type"] = $attrs["type"];
	}
	if (isset($attrs["lookup"])) {
		$input["lookup"] = $attrs["lookup"];
	}
	if (isset($attrs["default"])) {
		if ($attrs["default"]=="NOW") {
			$input["default"] = date("d M Y");
		} else {
			$match = array();
			if (preg_match("/NOW\s*([+\-])\s*(\d+)/", $attrs['default'], $match)>0) {
				$plus = 1;
				if ($match[1]=="-") {
					$plus = -1;
				}
				$input["default"] = date("d M Y", WT_TIMESTAMP + $plus*60*60*24*$match[2]);
			} else {
				$input["default"] = $attrs["default"];
			}
		}
	}
	if (isset($attrs["options"])) {
		$input["options"] = $attrs["options"];
	}
}

function inputEndHandler() {
	global $report_array, $text, $input;

	$input["value"] = $text;
	if (!isset($report_array["inputs"])) {
		$report_array["inputs"] = array();
	}
	$report_array["inputs"][] = $input;
	$text = "";
}
