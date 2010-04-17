<?php
/**
 * Report Engine
 *
 * Processes PGV XML Reports and generates a report
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
 * @subpackage Reports
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'reportengine.php');
require './includes/session.php';

// We have finished writing to $_SESSION, so release the lock
session_write_close();

$famid=safe_GET("famid");
$pid  =safe_GET("pid");

/**
 * function to get the values for the given tag
 */
function get_tag_values($tag) {
	global $tags, $values;

	$indexes = $tags[$tag];
	$vals = array();
	foreach($indexes as $i) {
		$vals[] = $values[$i];
	}
	return $vals;
}

if (isset($_REQUEST["action"])) {
	$action = $_REQUEST["action"];
	if (empty($action)) {
		$action = "choose";
	}
} else {
	$action = "choose";
}
if (isset($_REQUEST["report"])) {
	$report = $_REQUEST["report"];
} else {
	$report = "";
}
if (isset($_REQUEST["output"])) {
	$output = $_REQUEST["output"];
} else {
	$output = "PDF";
}
if (isset($_REQUEST["vars"])) {
	$vars = $_REQUEST["vars"];
} else {
	$vars = array();
}
if (isset($_REQUEST["varnames"])) {
	$varnames = $_REQUEST["varnames"];
} else {
	$varnames = array();
}
if (isset($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
} else {
	$type = array();
}

//-- setup the arrays
$newvars = array();
foreach($vars as $name=>$var) {
	$newvars[$name]["id"] = $var;
	if (!empty($type[$name]) && (($type[$name]=="INDI") || ($type[$name]=="FAM") || ($type[$name]=="SOUR"))) {
		$gedcom = find_gedcom_record($var, WT_GED_ID);
		if (empty($gedcom)) {
			$action="setup";
		}
		if ($type[$name]=="FAM") {
			if (preg_match("/0 @.*@ INDI/", $gedcom)>0) {
				$fams = find_sfamily_ids($var);
				if (!empty($fams[0])) {
					$gedcom = find_family_record($fams[0], WT_GED_ID);
					if (!empty($gedcom)) {
						$vars[$name] = $fams[0];
					} else {
						$action="setup";
					}
				}
			}
		}
		$newvars[$name]["gedcom"] = $gedcom;
	}
}
$vars = $newvars;
unset($newvars);

foreach($varnames as $indexval => $name) {
	if (!isset($vars[$name])) {
		$vars[$name]["id"] = "";
	}
}

$reports = get_report_list();
if (!empty($report)) {
	$r = basename($report);
	if (!isset($reports[$r]["access"])) {
		$action = "choose";
	} elseif ($reports[$r]["access"]<WT_USER_ACCESS_LEVEL) {
		$action = "choose";
	}
}

//-- choose a report to run
if ($action=="choose") {
	// Get the list of available reports in sorted localized title order
	$reportList = get_report_list(true);
	$reportTitles = array();
	foreach ($reportList as $file=>$report) {
		$reportTitles[$file] = $report["title"][WT_LOCALE];
	}
	asort($reportTitles);
	$reports = array();
	foreach ($reportTitles as $file=>$title) {
		$reports[$file] = $reportList[$file];
	}
	
	print_header(i18n::translate('Choose a report to run'));

	echo "<br /><br />\n<form name=\"choosereport\" method=\"get\" action=\"reportengine.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"setup\" />\n";
	echo "<input type=\"hidden\" name=\"output\" value=\"", $output, "\" />\n";
	echo "<table class=\"facts_table width40 center ", $TEXT_DIRECTION, " \">";
	echo "<tr><td class=\"topbottombar\" colspan=\"2\">", i18n::translate('Choose a report to run'), "</td></tr>";
	echo "<tr><td class=\"descriptionbox wrap width33 vmiddle\">", i18n::translate('Select report'), "</td>";
	echo "<td class=\"optionbox\"><select onchange=\"this.form.submit();\" name=\"report\">\n";
	foreach($reports as $file=>$report) {
		if ($report["access"] >= WT_USER_ACCESS_LEVEL) {
			echo "<option value=\"", $report["file"], "\">", $report["title"][WT_LOCALE], "</option>\n";
		}
	}
	echo "</select></td></tr>\n";
	echo "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"", i18n::translate('Click here to continue'), "\" /></td></tr>";
	echo "</table></form>\n<br /><br />\n";

	print_footer();
}

//-- setup report to run
elseif ($action=="setup") {
	print_header(i18n::translate('Enter report values'));

	if ($ENABLE_AUTOCOMPLETE) {
		require_once WT_ROOT."js/autocomplete.js.htm";
	}

	//-- make sure the report exists
	if (!file_exists($report)) {
		echo "<span class=\"error\">", i18n::translate('File not found.'), "</span> ", $report, "\n";
	} else {
		require_once WT_ROOT."includes/reportheader.php";
		$report_array = array();
		//-- start the sax parser
		$xml_parser = xml_parser_create();
		//-- make sure everything is case sensitive
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
		//-- set the main element handler functions
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		//-- set the character data handler
		xml_set_character_data_handler($xml_parser, "characterData");

		//-- open the file
		if (!($fp = fopen($report, "r"))) {
			die("could not open XML input");
		}
		//-- read the file and parse it 4kb at a time
		while (($data = fread($fp, 4096))) {
			if (!xml_parse($xml_parser, $data, feof($fp))) {
				die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
			}
		}
		xml_parser_free($xml_parser);
		// Paste Found ID from a pop-up window
		echo WT_JS_START;
			?>
			var pastefield;
			function paste_id(value) {
				pastefield.value=value;
			}
			<?php
		echo WT_JS_END;
		
		init_calendar_popup();
		echo "<form name=\"setupreport\" method=\"get\" target=\"_blank\" action=\"reportengine.php\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"run\" />\n";
		echo "<input type=\"hidden\" name=\"report\" value=\"", $report, "\" />\n";
		echo "<input type=\"hidden\" name=\"download\" value=\"\" />\n";

		echo "<table class=\"facts_table width50 center ", $TEXT_DIRECTION, " \">";
		echo "<tr><td class=\"topbottombar\" colspan=\"2\">", i18n::translate('Enter report values'), "</td></tr>";
		echo "<tr><td class=\"descriptionbox width30 wrap\">", i18n::translate('Selected Report'), "</td><td class=\"optionbox\">", $report_array["title"], "</td></tr>\n";

		$doctitle = trim($report_array["title"]);
		if (!isset($report_array["inputs"])) {
			$report_array["inputs"] = array();
		}
		foreach($report_array["inputs"] as $indexval => $input) {
			if ((($input["name"] == "sources") && ($SHOW_SOURCES >= WT_USER_ACCESS_LEVEL)) || ($input["name"] != "sources")) {
				if (($input["name"] != "photos") || ($MULTI_MEDIA)) {
					// url forced default value ?
					if (isset($_REQUEST[$input["name"]])) {
						$input["default"]=$_REQUEST[$input["name"]];
						// update doc title for bookmarking
						$doctitle .= " ";
						if (strpos($input["name"],"date2")!==false) {
							$doctitle .= "-";
						}
						$doctitle .= $input["default"];
						if (strpos($input["name"],"date1")!==false) {
							$doctitle .= "-";
						}
					}
					echo "<tr><td class=\"descriptionbox wrap\">\n";
					echo "<input type=\"hidden\" name=\"varnames[]\" value=\"", $input["name"], "\" />\n";
					echo i18n::translate($input["value"]), "</td><td class=\"optionbox\">";
					if (!isset($input["type"])) {
						$input["type"] = "text";
					}
					if (!isset($input["default"])) {
						$input["default"] = "";
					}
					if (isset($input["lookup"])) {
						if ($input["lookup"]=="INDI") {
							if (!empty($pid)) {
								$input["default"] = $pid;
							} else {
								$input["default"] = check_rootid($input["default"]);
							}
						}
						if ($input["lookup"]=="FAM") {
							if (!empty($famid)) {
								$input["default"] = $famid;
							} else {
								$famid = find_sfamily_ids(check_rootid($input["default"]));
								if (empty($famid)) {
									$famid = find_family_ids(check_rootid($input["default"]));
								}
								if (isset($famid[0])) {
									$input["default"] = $famid[0];
								}
							}
						}
						if ($input["lookup"]=="SOUR") {
							if (!empty($sid)) {
								$input["default"] = $sid;
							}
						}
					}
					if ($input["type"]=="text") {
						echo "<input type=\"text\" name=\"vars[", $input["name"], "]\" id=\"", $input["name"], "\" ";
						echo "value=\"", $input["default"], "\" style=\"direction: ltr;\" />";
					}
					if ($input["type"]=="checkbox") {
						echo "<input type=\"checkbox\" name=\"vars[", $input["name"], "]\" id=\"", $input["name"], "\" value=\"1\"";
						if ($input["default"]=="1") {
							echo " checked=\"checked\"";
						}
						echo " />";
					}
					if ($input["type"]=="select") {
						echo "<select name=\"vars[", $input["name"], "]\" id=\"", $input["name"], "_var\">\n";
						$options = preg_split("/[, ]+/", $input["options"]);
						foreach($options as $indexval => $option) {
							echo "\t<option value=\"", $option, "\"";
							if ($option==$input["default"]) {
								echo " selected=\"selected\"";
							}
							echo '>', i18n::translate($option), '</option>';
						}
						echo "</select>\n";
					}
					if (isset($input["lookup"])) {
						echo "<input type=\"hidden\" name=\"type[", $input["name"], "]\" value=\"", $input["lookup"], "\" />";
						if ($input["lookup"]=="INDI") {
							print_findindi_link("pid","");
						} elseif ($input["lookup"]=="PLAC") {
							print_findplace_link($input["name"]);
						} elseif ($input["lookup"]=="FAM") {
							print_findfamily_link("famid");
						} elseif ($input["lookup"]=="SOUR") {
							print_findsource_link($input["name"]);
						} elseif ($input["lookup"]=="DATE") {
							$text = i18n::translate('Select a date');
							if (isset($WT_IMAGES["calendar"]["button"])) {
								$Link = "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["calendar"]["button"]."\" name=\"a_".$input["name"]."\" id=\"a_".$input["name"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
							} else {
								$Link = $text;
							}

							?>
							<a href="javascript: <?php echo $input["name"]; ?>" onclick="cal_toggleDate('div_<?php echo $input["name"]; ?>', '<?php echo $input["name"]; ?>'); return false;">
							<?php echo $Link;?>
							</a>
							<div id="div_<?php echo $input["name"]; ?>" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
							<?php
						}
					}
					echo "</td></tr>\n";
				}
			}
		}
		?>
		<tr><td class="descriptionbox width30 wrap"></td>
		<td class="optionbox">
		<table><tr>
		<td><img src="<?php echo isset($WT_IMAGES["media"]["pdf"]) ? $WT_IMAGE_DIR."/".$WT_IMAGES["media"]["pdf"] : "images/media/pdf.gif";?>" alt="PDF" title="PDF" /></td>
		<td><img src="<?php echo isset($WT_IMAGES["media"]["html"]) ? $WT_IMAGE_DIR."/".$WT_IMAGES["media"]["html"] : "images/media/html.gif";?>" alt="HTML" title="HTML" /></td>
		</tr><tr>
		<td><center><input type="radio" name="output" value="PDF" checked="checked" /></center></td>
		<td><center><input type="radio" name="output" value="HTML" <?php if ($output=="HTML") echo " checked=\"checked\"";?> /></center></td>
		</tr></table>
		</td></tr>
		<?php
		echo "<tr><td class=\"topbottombar\" colspan=\"2\">";
		echo " <input type=\"submit\" value=\"", i18n::translate('Download report'), "\" onclick=\"document.setupreport.elements['download'].value='1';\"/>";
		echo " <input type=\"submit\" value=\"", i18n::translate('Cancel'), "\" onclick=\"document.setupreport.elements['action'].value='setup';document.setupreport.target='';\"/>";
		echo "</td></tr></table></form><br /><br />\n";
		echo WT_JS_START, "document.title = \"", $doctitle, "\"", WT_JS_END;
	}
	print_footer();
}
//-- run the report
elseif ($action=="run") {
	//-- load the report generator
	switch ($output) {
		case "HTML":
			header('Content-type: text/html; charset=UTF-8');
			require_once WT_ROOT."includes/classes/class_reporthtml.php";
			break;
		case "PDF":
		default:
			require_once WT_ROOT."includes/classes/class_reportpdf.php";
			break;
	}

	//-- start the sax parser
	$xml_parser = xml_parser_create();
	//-- make sure everything is case sensitive
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	//-- set the main element handler functions
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	//-- set the character data handler
	xml_set_character_data_handler($xml_parser, "characterData");

	//-- open the file
	if (!($fp = fopen($report, "r"))) {
		die("could not open XML input");
	}
	//-- read the file and parse it 4kb at a time
	while (($data = fread($fp, 4096))) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data."\nXML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
}

?>
