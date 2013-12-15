<?php
// Report Engine
//
// Processes webtrees XML Reports and generates a report
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'reportengine.php');
require './includes/session.php';
require WT_ROOT.'includes/functions/functions_rtl.php';

$controller=new WT_Controller_Page();

$famid   =WT_Filter::get('famid');
$pid     =WT_Filter::get('pid');
$action  =WT_Filter::get('action', 'choose|setup|run', 'choose');
$report  =WT_Filter::get('report');
$output  =WT_Filter::get('output', 'HTML|PDF', 'PDF');
$vars    =WT_Filter::get('vars');
$varnames=WT_Filter::get('varnames');
$type    =WT_Filter::get('type');
if (!is_array($vars)) {
	$vars=array();
}
if (!is_array($varnames)) {
	$varnames=array();
}
if (!is_array($type)) {
	$type=array();
}

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

//-- setup the arrays
$newvars = array();
foreach ($vars as $name=>$var) {
	$newvars[$name]['id'] = $var;
	if (!empty($type[$name]) && (($type[$name]=='INDI') || ($type[$name]=='FAM') || ($type[$name]=='SOUR'))) {
		$record = WT_GedcomRecord::getInstance($var);
		if (!$record) {
			$action='setup';
		}
		$gedcom = $record->getGedcom();
		// If we wanted a FAM, and were given an INDI, look for a spouse
		if ($type[$name]=='FAM' && $record instanceof WT_Individual) {
			$tmp = false;
			foreach ($record->getSpouseFamilies() as $family) {
				$gedcom = $family->getGedcom();
				$tmp = true;
			}
			if (!$tmp) {
				$action='setup';
			}
		}
		$newvars[$name]['gedcom'] = $gedcom;
	}
}
$vars = $newvars;
unset($newvars);

foreach ($varnames as $indexval => $name) {
	if (!isset($vars[$name])) {
		$vars[$name]['id'] = '';
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
		$action = 'choose';
	}
}

//-- choose a report to run
if ($action=='choose') {
	$controller->setPageTitle(WT_I18N::translate('Choose a report to run'));
	$controller->pageHeader();

	echo '<div id="reportengine-page">
		<form name="choosereport" method="get" action="reportengine.php">
		<input type="hidden" name="action" value="setup">
		<input type="hidden" name="output" value="', WT_Filter::escapeHtml($output), '">
		<table class="facts_table width40">
		<tr><td class="topbottombar" colspan="2">', WT_I18N::translate('Choose a report to run'), '</td></tr>
		<tr><td class="descriptionbox wrap width33 vmiddle">', WT_I18N::translate('Select report'), '</td>
		<td class="optionbox"><select name="report">';
	foreach ($reports as $file=>$report) {
			echo '<option value="', WT_Filter::escapeHtml($file), '">', WT_Filter::escapeHtml($report), '</option>';
	}
	echo '</select></td></tr>
		<tr><td class="topbottombar" colspan="2"><input type="submit" value="', WT_I18N::translate('continue'), '"></td></tr>
		</table></form></div>';
}

//-- setup report to run
elseif ($action=='setup') {
	require_once WT_ROOT.'includes/reportheader.php';
	$report_array = array();
	//-- start the sax parser
	$xml_parser = xml_parser_create();
	//-- make sure everything is case sensitive
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	//-- set the main element handler functions
	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	//-- set the character data handler
	xml_set_character_data_handler($xml_parser, 'characterData');

	//-- open the file
	if (!($fp = fopen($report, 'r'))) {
		die('could not open XML input');
	}
	//-- read the file and parse it 4kb at a time
	while (($data = fread($fp, 4096))) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data.' XML error: %s at line %d', xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);

	$controller
		->setPageTitle($report_array['title'])
		->pageHeader()
		->addInlineJavascript('var pastefield; function paste_id(value) { pastefield.value=value; }') // For the 'find indi' link
		->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js');

	init_calendar_popup();
	echo '<div id="reportengine-page">
		<form name="setupreport" method="get" action="reportengine.php" onsubmit="if (this.output[1].checked) {this.target=\'_blank\';}">
		<input type="hidden" name="action" value="run">
		<input type="hidden" name="report" value="', WT_Filter::escapeHtml($report), '">
		<table class="facts_table width50">
		<tr><td class="topbottombar" colspan="2">', WT_I18N::translate('Enter report values'), '</td></tr>
		<tr><td class="descriptionbox width30 wrap">', WT_I18N::translate('Selected Report'), '</td><td class="optionbox">', $report_array['title'], '<br>', $report_array['description'], '</td></tr>';

	if (!isset($report_array['inputs'])) {
		$report_array['inputs'] = array();
	}
	foreach ($report_array['inputs'] as $indexval => $input) {
		echo '<tr><td class="descriptionbox wrap">';
		echo '<input type="hidden" name="varnames[]" value="', WT_Filter::escapeHtml($input["name"]), '">';
		echo WT_I18N::translate($input['value']), '</td><td class="optionbox">';
		if (!isset($input['type'])) {
			$input['type'] = 'text';
		}
		if (!isset($input['default'])) {
			$input['default'] = '';
		}
		if (isset($input['lookup'])) {
			if ($input['lookup']=='INDI') {
				if (!empty($pid)) {
					$input['default'] = $pid;
				} else {
					$input['default'] = $controller->getSignificantIndividual()->getXref();
				}
			}
			if ($input['lookup']=='FAM') {
				if (!empty($famid)) {
					$input['default'] = $famid;
				} else {
					$input['default'] = $controller->getSignificantFamily()->getXref();
				}
			}
			if ($input['lookup']=='SOUR') {
				if (!empty($sid)) {
					$input['default'] = $sid;
				}
			}
			if ($input['lookup']=='DATE') {
				if (isset($input['default'])) {
					$input['default'] = strtoupper($input['default']);
				}
			}
		}
		if ($input['type']=='text') {
			echo '<input type="text" name="vars[', WT_Filter::escapeHtml($input['name']), ']" id="', WT_Filter::escapeHtml($input['name']), '" value="', WT_Filter::escapeHtml($input['default']), '" style="direction: ltr;">';
		}
		if ($input['type']=='checkbox') {
			echo '<input type="checkbox" name="vars[', WT_Filter::escapeHtml($input['name']), ']" id="', WT_Filter::escapeHtml($input['name']), '" value="1"';
			if ($input['default']=='1') {
				echo ' checked="checked"';
			}
			echo '>';
		}
		if ($input['type']=='select') {
			echo '<select name="vars[', WT_Filter::escapeHtml($input['name']), ']" id="', WT_Filter::escapeHtml($input['name']), '_var">';
			$options = preg_split('/[|]+/', $input['options']);
			foreach ($options as $indexval => $option) {
				$opt = explode('=>', $option);
				list($value, $display)=$opt;
				if (substr($display, 0, 18)=='WT_I18N::translate' || substr($display, 0, 15) == 'WT_I18N::number' || substr($display, 0, 23)=='WT_Gedcom_Tag::getLabel') {
					eval("\$display=$display;");
				}
				echo '<option value="', WT_Filter::escapeHtml($value), '"';
				if ($opt[0]==$input['default']) {
					echo ' selected="selected"';
				}
				echo '>', WT_Filter::escapeHtml($display), '</option>';
			}
			echo '</select>';
		}
		if (isset($input['lookup'])) {
			echo '<input type="hidden" name="type[', WT_Filter::escapeHtml($input['name']), ']" value="', WT_Filter::escapeHtml($input['lookup']), '">';
			if ($input['lookup']=='INDI') {
				echo print_findindi_link('pid');
			} elseif ($input['lookup']=='PLAC') {
				echo print_findplace_link($input['name']);
			} elseif ($input['lookup']=='FAM') {
				echo print_findfamily_link('famid');
			} elseif ($input['lookup']=='SOUR') {
				echo print_findsource_link($input['name']);
			} elseif ($input['lookup']=='DATE') {
				echo ' <a href="#" onclick="cal_toggleDate(\'div_', WT_Filter::EscapeJs($input['name']), '\', \'', WT_Filter::EscapeJs($input['name']), '\'); return false;" class="icon-button_calendar" title="', WT_I18N::translate('Select a date'), '"></a>';
				echo '<div id="div_', WT_Filter::EscapeHtml($input['name']), '" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>';
			}
		}
		echo '</td></tr>';
	}
	echo '<tr>
		<td colspan="2" class="optionbox">
		<div class="report-type">
		<div>
		<label for="PDF"><i class="icon-mime-application-pdf"></i></label>
		<p><input type="radio" name="output" value="PDF" id="PDF" checked="checked"></p>
		</div>
		<div>
		<label for="HTML"><i class="icon-mime-text-html"></i></label>
		<p><input type="radio" name="output" id="HTML" value="HTML"></p>
		</div>
		</div>
		</td>
		</tr>
		<tr><td class="topbottombar" colspan="2">
		<input type="submit" value="', WT_I18N::translate('continue'), '">
		</td></tr></table></form></div>';
}
//-- run the report
elseif ($action=='run') {
	if (strstr($report, 'report_singlepage.xml')!==false) {
		$DEBUG=false;
		$pedigree=new ReportPedigree();
		exit;
	}
	//-- load the report generator
	switch ($output) {
		case 'HTML':
			header('Content-type: text/html; charset=UTF-8');
			$wt_report = new WT_Report_HTML();
			$ReportRoot = $wt_report;
			break;
		case 'PDF':
		default:
			$wt_report = new WT_Report_PDF();
			$ReportRoot = $wt_report;
			break;
	}

	$ascii_langs = array('en', 'da', 'nl', 'fr', 'he', 'hu', 'de', 'nn', 'es');

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
	$elementHandler['AgeAtDeath']['start']       = 'AgeAtDeathSHandler';
	$elementHandler['br']['start']               = 'brSHandler';
	$elementHandler['Body']['start']             = 'BodySHandler';
	$elementHandler['Cell']['end']               = 'CellEHandler';
	$elementHandler['Cell']['start']             = 'CellSHandler';
	$elementHandler['Description']['end']        = 'DescriptionEHandler';
	$elementHandler['Description']['start']      = 'DescriptionSHandler';
	$elementHandler['Doc']['end']                = 'DocEHandler';
	$elementHandler['Doc']['start']              = 'DocSHandler';
	$elementHandler['Report']['end']             = '';
	$elementHandler['Report']['start']           = '';
	$elementHandler['Facts']['end']              = 'FactsEHandler';
	$elementHandler['Facts']['start']            = 'FactsSHandler';
	$elementHandler['Footer']['start']           = 'FooterSHandler';
	$elementHandler['Footnote']['end']           = 'FootnoteEHandler';
	$elementHandler['Footnote']['start']         = 'FootnoteSHandler';
	$elementHandler['FootnoteTexts']['start']    = 'FootnoteTextsSHandler';
	$elementHandler['Gedcom']['end']             = 'GedcomEHandler';
	$elementHandler['Gedcom']['start']           = 'GedcomSHandler';
	$elementHandler['GedcomValue']['start']      = 'GedcomValueSHandler';
	$elementHandler['Generation']['start']       = 'GenerationSHandler';
	$elementHandler['GetPersonName']['start']    = 'GetPersonNameSHandler';
	$elementHandler['Header']['start']           = 'HeaderSHandler';
	$elementHandler['HighlightedImage']['start'] = 'HighlightedImageSHandler';
	$elementHandler['if']['end']                 = 'ifEHandler';
	$elementHandler['if']['start']               = 'ifSHandler';
	$elementHandler['Image']['start']            = 'ImageSHandler';
	$elementHandler['Input']['end']              = '';
	$elementHandler['Input']['start']            = '';
	$elementHandler['Line']['start']             = 'LineSHandler';
	$elementHandler['List']['end']               = 'ListEHandler';
	$elementHandler['List']['start']             = 'ListSHandler';
	$elementHandler['ListTotal']['start']        = 'ListTotalSHandler';
	$elementHandler['NewPage']['start']          = 'NewPageSHandler';
	$elementHandler['Now']['start']              = 'NowSHandler';
	$elementHandler['PageHeader']['end']         = 'PageHeaderEHandler';
	$elementHandler['PageHeader']['start']       = 'PageHeaderSHandler';
	$elementHandler['PageNum']['start']          = 'PageNumSHandler';
	$elementHandler['Relatives']['end']          = 'RelativesEHandler';
	$elementHandler['Relatives']['start']        = 'RelativesSHandler';
	$elementHandler['RepeatTag']['end']          = 'RepeatTagEHandler';
	$elementHandler['RepeatTag']['start']        = 'RepeatTagSHandler';
	$elementHandler['SetVar']['start']           = 'SetVarSHandler';
	$elementHandler['Style']['start']            = 'StyleSHandler';
	$elementHandler['Text']['end']               = 'TextEHandler';
	$elementHandler['Text']['start']             = 'TextSHandler';
	$elementHandler['TextBox']['end']            = 'TextBoxEHandler';
	$elementHandler['TextBox']['start']          = 'TextBoxSHandler';
	$elementHandler['Title']['end']              = 'TitleEHandler';
	$elementHandler['Title']['start']            = 'TitleSHandler';
	$elementHandler['TotalPages']['start']       = 'TotalPagesSHandler';
	$elementHandler['var']['start']              = 'varSHandler';
	$elementHandler['sp']['start']               = 'spSHandler';

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
	$gedrec = '';

	/**
	* @todo add info
	* @global ???? $repeatBytes
	*/
	$repeatBytes = 0;

	/**
	* @todo add info
	* @global resource $parser
	*/
	$parser = '';

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
	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	//-- set the character data handler
	xml_set_character_data_handler($xml_parser, 'characterData');

	//-- open the file
	if (!($fp = fopen($report, 'r'))) {
		die('could not open XML input');
	}
	//-- read the file and parse it 4kb at a time
	while (($data = fread($fp, 4096))) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data.' XML error: %s at line %d', xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
}

exit;

// We cannot add translation comments inside the XML files.
// These messages are all used in the reports.  We repeat them
// here, so we can add comments
$x=/* I18N: An option in a list-box */ WT_I18N::translate('sort by date of birth');
$x=/* I18N: An option in a list-box */ WT_I18N::translate('sort by date of marriage');
$x=/* I18N: An option in a list-box */ WT_I18N::translate('sort by date of death');
