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

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

$famid=safe_GET("famid");
$pid  =safe_GET("pid");

/**
 * function to get the values for the given tag
 */
function get_tag_values($tag) {
	global $tags, $values;

	$indexes = $tags[$tag];
	$vals = array();
	foreach ($indexes as $i) {
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
foreach ($vars as $name=>$var) {
	$newvars[$name]["id"] = $var;
	if (!empty($type[$name]) && (($type[$name]=="INDI") || ($type[$name]=="FAM") || ($type[$name]=="SOUR"))) {
		$gedcom = find_gedcom_record($var, WT_GED_ID);
		if (empty($gedcom)) {
			$action="setup";
		}
		// If we wanted a FAM, and were given an INDI, look for a spouse
		if ($type[$name]=="FAM") {
			if (preg_match("/0 @.+@ INDI/", $gedcom)>0) {
				if (preg_match('/\n1 FAMS @(.+)@/', $gedcom, $match)) {
					$gedcom = find_family_record($match[1], WT_GED_ID);
					if (!empty($gedcom)) {
						$vars[$name] = $match[1];
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

foreach ($varnames as $indexval => $name) {
	if (!isset($vars[$name])) {
		$vars[$name]["id"] = "";
	}
}

$reports=array();
foreach (WT_Module::getActiveReports() as $rep) {
	foreach ($rep->getReportMenus() as $menu) {
		if (preg_match('/report=('.preg_quote(WT_MODULES_DIR, '/').'[a-z0-9_]+\/[a-z0-9_]+\.xml)/', $menu->link, $match)) {
			$reports[$match[1]]=$menu->label;
		}
	}
}

if (!empty($report)) {
	if (!array_key_exists($report, $reports)) {
		$action = "choose";
	}
}

//-- choose a report to run
if ($action=="choose") {
	print_header(WT_I18N::translate('Choose a report to run'));

	echo "<br /><br />\n<form name=\"choosereport\" method=\"get\" action=\"reportengine.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"setup\" />\n";
	echo "<input type=\"hidden\" name=\"output\" value=\"", $output, "\" />\n";
	echo "<table class=\"facts_table width40 center ", $TEXT_DIRECTION, " \">";
	echo "<tr><td class=\"topbottombar\" colspan=\"2\">", WT_I18N::translate('Choose a report to run'), "</td></tr>";
	echo "<tr><td class=\"descriptionbox wrap width33 vmiddle\">", WT_I18N::translate('Select report'), "</td>";
	echo "<td class=\"optionbox\"><select onchange=\"this.form.submit();\" name=\"report\">\n";
	foreach ($reports as $file=>$report) {
			echo "<option value=\"", $file, "\">", $report, "</option>\n";
	}
	echo "</select></td></tr>\n";
	echo "<tr><td class=\"topbottombar\" colspan=\"2\"><input type=\"submit\" value=\"", WT_I18N::translate('Click here to continue'), "\" /></td></tr>";
	echo "</table></form>\n<br /><br />\n";

	print_footer();
}

//-- setup report to run
elseif ($action=="setup") {
	print_header(WT_I18N::translate('Enter report values'));

	if ($ENABLE_AUTOCOMPLETE) {
		require_once WT_ROOT."js/autocomplete.js.htm";
	}

	//-- make sure the report exists
	if (!file_exists($report)) {
		echo "<span class=\"error\">", WT_I18N::translate('File not found.'), "</span> ", $report, "\n";
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

		echo "<table class=\"facts_table width50 center ", $TEXT_DIRECTION, " \">";
		echo "<tr><td class=\"topbottombar\" colspan=\"2\">", WT_I18N::translate('Enter report values'), "</td></tr>";
		echo "<tr><td class=\"descriptionbox width30 wrap\">", WT_I18N::translate('Selected Report'), "</td><td class=\"optionbox\">", $report_array["title"], "</td></tr>\n";

		$doctitle = trim($report_array["title"]);
		if (!isset($report_array["inputs"])) {
			$report_array["inputs"] = array();
		}
		foreach ($report_array["inputs"] as $indexval => $input) {
			if ($input["name"] == "sources" || $input["name"] != "sources") {
				if ($input["name"] != "photos" || $MULTI_MEDIA) {
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
					echo WT_I18N::translate($input["value"]), "</td><td class=\"optionbox\">";
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
								// Default the FAM to the first spouse family of the default INDI
								$person=WT_Person::getInstance(check_rootid($input["default"]));
								if ($person) {
									$sfams=$person->getSpouseFamilies();
									if ($sfams) {
										$input["default"] = reset($sfams)->getXref();
									}
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
						$options = preg_split("/[|]+/", $input["options"]);
						foreach ($options as $indexval => $option) {
							$opt = explode('=>', $option);
							list($value, $display)=$opt;
							if (substr($display, 0, 18)=='WT_I18N::translate' || substr($display, 0, 14)=='translate_fact') {
								eval("\$display=$display;");
							}
							echo "\t<option value=\"", htmlspecialchars($value), "\"";
							if ($opt[0]==$input["default"]) {
								echo " selected=\"selected\"";
							}
							echo '>', $display, '</option>';
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
							$text = WT_I18N::translate('Select a date');
							if (isset($WT_IMAGES["button_calendar"])) {
								$Link = "<img src=\"".$WT_IMAGES["button_calendar"]."\" name=\"a_".$input["name"]."\" id=\"a_".$input["name"]."\" alt=\"".$text."\" title=\"".$text."\" border=\"0\" align=\"middle\" />";
							} else {
								$Link = $text;
							}

							?>
							<a href="javascript: <?php echo $input["name"]; ?>" onclick="cal_toggleDate('div_<?php echo $input["name"]; ?>', '<?php echo $input["name"]; ?>'); return false;">
							<?php echo $Link; ?>
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
		<td><img src="<?php echo $WT_IMAGES["media_pdf"]; ?>" alt="PDF" title="PDF" /></td>
		<td><img src="<?php echo $WT_IMAGES["media_html"]; ?>" alt="HTML" title="HTML" /></td>
		</tr><tr>
		<td><center><input type="radio" name="output" value="PDF" checked="checked" /></center></td>
		<td><center><input type="radio" name="output" value="HTML" <?php if ($output=="HTML") echo " checked=\"checked\""; ?> /></center></td>
		</tr></table>
		</td></tr>
		<?php
		echo "<tr><td class=\"topbottombar\" colspan=\"2\">";
		echo " <input type=\"submit\" value=\"", WT_I18N::translate('Download report'), "\" ;\"/>";
		echo " <input type=\"submit\" value=\"", WT_I18N::translate('Cancel'), "\" onclick=\"document.setupreport.elements['action'].value='setup'; \"/>";
		echo "</td></tr></table></form><br /><br />\n";
		echo WT_JS_START, "document.title = \"", $doctitle, "\"", WT_JS_END;
	}
	print_footer();
}
//-- run the report
elseif ($action=="run") {
	if (strstr($report, "report_singlepage.xml")!==false) {
		$DEBUG=false;
		$pedigree=new ReportPedigree();
		exit;
	}
	//-- load the report generator
	switch ($output) {
		case "HTML":
			header('Content-type: text/html; charset=UTF-8');
			$wt_report = new WT_Report_HTML();
			$ReportRoot = $wt_report;
			break;
		case "PDF":
		default:
			$wt_report = new WT_Report_PDF();
			$ReportRoot = $wt_report;
			break;
	}

	$ascii_langs = array("en", "da", "nl", "fr", "he", "hu", "de", "nn", "es");

	//-- setup special characters array to force embedded fonts
	$SpecialOrds = $RTLOrd;
	for ($i=195; $i<215; $i++) $SpecialOrds[] = $i;

	if (!isset($embed_fonts)) {
		if (in_array(WT_LOCALE, $ascii_langs)) {
			$embed_fonts = false;
		} else {
			$embed_fonts = true;
		}
	}

	/**
	 * element handlers array
	 *
	 * Converts XML element names into functions
	 * @global array $elementHandler
	 */
	$elementHandler = array();
	$elementHandler["AgeAtDeath"]["start"]       = "AgeAtDeathSHandler";
	$elementHandler["br"]["start"]               = "brSHandler";
	$elementHandler["Body"]["start"]             = "BodySHandler";
	$elementHandler["Cell"]["end"]               = "CellEHandler";
	$elementHandler["Cell"]["start"]             = "CellSHandler";
	$elementHandler["Description"]["end"]        = "DescriptionEHandler";
	$elementHandler["Description"]["start"]      = "DescriptionSHandler";
	$elementHandler["Doc"]["end"]                = "DocEHandler";
	$elementHandler["Doc"]["start"]              = "DocSHandler";
	$elementHandler["Report"]["end"]             = "";
	$elementHandler["Report"]["start"]           = "";
	$elementHandler["Facts"]["end"]              = "FactsEHandler";
	$elementHandler["Facts"]["start"]            = "FactsSHandler";
	$elementHandler["Footer"]["start"]           = "FooterSHandler";
	$elementHandler["Footnote"]["end"]           = "FootnoteEHandler";
	$elementHandler["Footnote"]["start"]         = "FootnoteSHandler";
	$elementHandler["FootnoteTexts"]["start"]    = "FootnoteTextsSHandler";
	$elementHandler["Gedcom"]["end"]             = "GedcomEHandler";
	$elementHandler["Gedcom"]["start"]           = "GedcomSHandler";
	$elementHandler["GedcomValue"]["start"]      = "GedcomValueSHandler";
	$elementHandler["Generation"]["start"]       = "GenerationSHandler";
	$elementHandler["GetPersonName"]["start"]    = "GetPersonNameSHandler";
	$elementHandler["Header"]["start"]           = "HeaderSHandler";
	$elementHandler["HighlightedImage"]["start"] = "HighlightedImageSHandler";
	$elementHandler["if"]["end"]                 = "ifEHandler";
	$elementHandler["if"]["start"]               = "ifSHandler";
	$elementHandler["Image"]["start"]            = "ImageSHandler";
	$elementHandler["Input"]["end"]              = "";
	$elementHandler["Input"]["start"]            = "";
	$elementHandler["Line"]["start"]             = "LineSHandler";
	$elementHandler["List"]["end"]               = "ListEHandler";
	$elementHandler["List"]["start"]             = "ListSHandler";
	$elementHandler["ListTotal"]["start"]        = "ListTotalSHandler";
	$elementHandler["NewPage"]["start"]          = "NewPageSHandler";
	$elementHandler["Now"]["start"]              = "NowSHandler";
	$elementHandler["PageHeader"]["end"]         = "PageHeaderEHandler";
	$elementHandler["PageHeader"]["start"]       = "PageHeaderSHandler";
	$elementHandler["PageNum"]["start"]          = "PageNumSHandler";
	$elementHandler["Relatives"]["end"]          = "RelativesEHandler";
	$elementHandler["Relatives"]["start"]        = "RelativesSHandler";
	$elementHandler["RepeatTag"]["end"]          = "RepeatTagEHandler";
	$elementHandler["RepeatTag"]["start"]        = "RepeatTagSHandler";
	$elementHandler["SetVar"]["start"]           = "SetVarSHandler";
	$elementHandler["Style"]["start"]            = "StyleSHandler";
	$elementHandler["Text"]["end"]               = "TextEHandler";
	$elementHandler["Text"]["start"]             = "TextSHandler";
	$elementHandler["TextBox"]["end"]            = "TextBoxEHandler";
	$elementHandler["TextBox"]["start"]          = "TextBoxSHandler";
	$elementHandler["Title"]["end"]              = "TitleEHandler";
	$elementHandler["Title"]["start"]            = "TitleSHandler";
	$elementHandler["TotalPages"]["start"]       = "TotalPagesSHandler";
	$elementHandler["var"]["start"]              = "varSHandler";
	$elementHandler["varLetter"]["start"]        = "varLetterSHandler";
	$elementHandler["sp"]["start"]               = "spSHandler";

	/**
	* A new object of the currently used element class
	*
	* @global object $currentElement
	*/
	$currentElement = new Element();

	/**
	 * Should character data be printed
	 *
	 * This variable is turned on or off by the element handlers to tell whether the inner character
	 * Data should be printed
	 * @global boolean $printData
	 */
	$printData = false;

	/**
	* Title collector. Mark it if it has already been used
	*
	* @global boolean $reportTitle
	*/
	$reportTitle = false;

	/**
	* Description collector. Mark it if it has already been used
	*
	* @global boolean $reportDescription
	*/
	$reportDescription = false;

	/**
	 * Print data stack
	 *
	 * As the XML is being processed there will be times when we need to turn on and off the
	 * <var>$printData</var> variable as we encounter entinties in the XML.  The stack allows us to
	 * keep track of when to turn <var>$printData</var> on and off.
	 * @global array $printDataStack
	 */
	$printDataStack = array();

	/**
	* @todo add info
	* @global array $wt_reportStack
	*/
	$wt_reportStack = array();

	/**
	* @todo add info
	* @global array $gedrecStack
	*/
	$gedrecStack = array();

	/**
	* @todo add info
	* @global array $repeatsStack
	*/
	$repeatsStack = array();

	/**
	* @todo add info
	* @global array $parserStack
	*/
	$parserStack = array();

	/**
	* @todo add info
	* @global array $repeats
	*/
	$repeats = array();

	/**
	* @todo add info
	* @global string $gedrec
	*/
	$gedrec = "";

	/**
	* @todo add info
	* @global ???? $repeatBytes
	*/
	$repeatBytes = 0;

	/**
	* @todo add info
	* @global resource $parser
	*/
	$parser = "";

	/**
	* @todo add info
	* @global int $processRepeats
	*/
	$processRepeats = 0;

	/**
	* @todo add info
	* @global ???? $processIfs
	*/
	$processIfs = 0;

	/**
	* @todo add info
	* @global ???? $processGedcoms
	*/
	$processGedcoms = 0;

	/**
	 * Wether or not to print footnote
	 * true = print, false = don't print
	 */
	$processFootnote = true;

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
