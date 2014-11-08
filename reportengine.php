<?php
// Report Engine
//
// Processes webtrees XML Reports and generates a report
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

define('WT_SCRIPT_NAME', 'reportengine.php');
require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_rtl.php';

$controller = new WT_Controller_Page();

$famid = WT_Filter::get('famid', WT_REGEX_XREF);
$pid = WT_Filter::get('pid', WT_REGEX_XREF);
$action = WT_Filter::get('action', 'choose|setup|run', 'choose');
$report = WT_Filter::get('report');
$output = WT_Filter::get('output', 'HTML|PDF', 'PDF');
$vars = WT_Filter::get('vars');
$varnames = WT_Filter::get('varnames');
$type = WT_Filter::get('type');
if (!is_array($vars)) {
	$vars = array();
}
if (!is_array($varnames)) {
	$varnames = array();
}
if (!is_array($type)) {
	$type = array();
}

//-- setup the arrays
$newvars = array();
foreach ($vars as $name => $var) {
	$newvars[$name]['id'] = $var;
	if (!empty($type[$name])) {
		switch ($type[$name]) {
		case 'INDI':
			$record = WT_Individual::getInstance($var);
			if ($record && $record->canShowName()) {
				$newvars[$name]['gedcom'] = $record->privatizeGedcom(WT_USER_ACCESS_LEVEL);
			} else {
				$action = 'setup';
			}
			break;
		case 'FAM':
			$record = WT_Family::getInstance($var);
			if ($record && $record->canShowName()) {
				$newvars[$name]['gedcom'] = $record->privatizeGedcom(WT_USER_ACCESS_LEVEL);
			} else {
				$action = 'setup';
			}
			break;
		case 'SOUR':
			$record = WT_Source::getInstance($var);
			if ($record && $record->canShowName()) {
				$newvars[$name]['gedcom'] = $record->privatizeGedcom(WT_USER_ACCESS_LEVEL);
			} else {
				$action = 'setup';
			}
			break;
		default:
			break;
		}
	}
}
$vars = $newvars;
unset($newvars);

foreach ($varnames as $name) {
	if (!isset($vars[$name])) {
		$vars[$name]['id'] = '';
	}
}

$reports = array();
foreach (WT_Module::getActiveReports() as $rep) {
	foreach ($rep->getReportMenus() as $menu) {
		if (preg_match('/report=(' . preg_quote(WT_MODULES_DIR, '/') . '[a-z0-9_]+\/[a-z0-9_]+\.xml)/', $menu->link, $match)) {
			$reports[$match[1]] = $menu->label;
		}
	}
}

if (!empty($report)) {
	if (!array_key_exists($report, $reports)) {
		$action = 'choose';
	}
}

//-- choose a report to run
switch ($action) {
case 'choose':
	$controller
		->setPageTitle(WT_I18N::translate('Choose a report to run'))
		->pageHeader();

	echo '<div id="reportengine-page">
		<form name="choosereport" method="get" action="reportengine.php">
		<input type="hidden" name="action" value="setup">
		<input type="hidden" name="output" value="', WT_Filter::escapeHtml($output), '">
		<table class="facts_table width40">
		<tr><td class="topbottombar" colspan="2">', WT_I18N::translate('Choose a report to run'), '</td></tr>
		<tr><td class="descriptionbox wrap width33 vmiddle">', WT_I18N::translate('Report'), '</td>
		<td class="optionbox"><select name="report">';
	foreach ($reports as $file => $report) {
		echo '<option value="', WT_Filter::escapeHtml($file), '">', WT_Filter::escapeHtml($report), '</option>';
	}
	echo '</select></td></tr>
		<tr><td class="topbottombar" colspan="2"><input type="submit" value="', WT_I18N::translate('continue'), '"></td></tr>
		</table></form></div>';
	break;

case 'setup':
	require_once WT_ROOT . 'includes/reportheader.php';
	$report_array = array();
	// Start the sax parser
	$xml_parser = xml_parser_create();
	// Make sure everything is case sensitive
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false);
	// Set the main element handler functions
	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	// Set the character data handler
	xml_set_character_data_handler($xml_parser, 'characterData');

	// Open the file
	if (!($fp = fopen($report, 'r'))) {
		die('could not open XML input');
	}
	while (($data = fread($fp, 4096))) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data . ' XML error: %s at line %d', xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);

	$controller
		->setPageTitle($report_array['title'])
		->pageHeader()
		->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
		->addInlineJavascript('autocomplete();');

	init_calendar_popup();

	echo '<div id="reportengine-page">
		<form name="setupreport" method="get" action="reportengine.php" onsubmit="if (this.output[1].checked) {this.target=\'_blank\';}">
		<input type="hidden" name="action" value="run">
		<input type="hidden" name="report" value="', WT_Filter::escapeHtml($report), '">
		<table class="facts_table width50">
		<tr><td class="topbottombar" colspan="2">', WT_I18N::translate('Enter report values'), '</td></tr>
		<tr><td class="descriptionbox width30 wrap">', WT_I18N::translate('Report'), '</td><td class="optionbox">', $report_array['title'], '<br>', $report_array['description'], '</td></tr>';

	if (!isset($report_array['inputs'])) {
		$report_array['inputs'] = array();
	}
	foreach ($report_array['inputs'] as $input) {
		echo '<tr><td class="descriptionbox wrap">';
		echo '<input type="hidden" name="varnames[]" value="', WT_Filter::escapeHtml($input["name"]), '">';
		echo WT_I18N::translate($input['value']), '</td><td class="optionbox">';
		if (!isset($input['type'])) {
			$input['type'] = 'text';
		}
		if (!isset($input['default'])) {
			$input['default'] = '';
		}
		if (!isset($input['lookup'])) {
			$input['lookup'] = '';
		}

		if ($input['type'] == 'text') {
			echo '<input';

			switch ($input['lookup']) {
			case 'INDI':
				echo ' data-autocomplete-type="INDI"';
				if (!empty($pid)) {
					$input['default'] = $pid;
				} else {
					$input['default'] = $controller->getSignificantIndividual()->getXref();
				}
				break;
			case 'FAM':
				echo ' data-autocomplete-type="FAM"';
				if (!empty($famid)) {
					$input['default'] = $famid;
				} else {
					$input['default'] = $controller->getSignificantFamily()->getXref();
				}
				break;
			case 'SOUR':
				echo ' data-autocomplete-type="SOUR"';
				if (!empty($sid)) {
					$input['default'] = $sid;
				}
				break;
			case 'DATE':
				if (isset($input['default'])) {
					$input['default'] = strtoupper($input['default']);
				}
				break;
			}

			echo ' type="text" name="vars[', WT_Filter::escapeHtml($input['name']), ']" id="', WT_Filter::escapeHtml($input['name']), '" value="', WT_Filter::escapeHtml($input['default']), '" style="direction: ltr;">';
		}
		if ($input['type'] == 'checkbox') {
			echo '<input type="checkbox" name="vars[', WT_Filter::escapeHtml($input['name']), ']" id="', WT_Filter::escapeHtml($input['name']), '" value="1"';
			if ($input['default'] == '1') {
				echo ' checked="checked"';
			}
			echo '>';
		}
		if ($input['type'] == 'select') {
			echo '<select name="vars[', WT_Filter::escapeHtml($input['name']), ']" id="', WT_Filter::escapeHtml($input['name']), '_var">';
			$options = preg_split('/[|]+/', $input['options']);
			foreach ($options as $option) {
				$opt = explode('=>', $option);
				list($value, $display) = $opt;
				if (substr($display, 0, 18) == 'WT_I18N::translate' || substr($display, 0, 15) == 'WT_I18N::number' || substr($display, 0, 23) == 'WT_Gedcom_Tag::getLabel') {
					eval("\$display=$display;");
				}
				echo '<option value="', WT_Filter::escapeHtml($value), '"';
				if ($opt[0] == $input['default']) {
					echo ' selected="selected"';
				}
				echo '>', WT_Filter::escapeHtml($display), '</option>';
			}
			echo '</select>';
		}
		if (isset($input['lookup'])) {
			echo '<input type="hidden" name="type[', WT_Filter::escapeHtml($input['name']), ']" value="', WT_Filter::escapeHtml($input['lookup']), '">';
			if ($input['lookup'] == 'INDI') {
				echo print_findindi_link('pid');
			} elseif ($input['lookup'] == 'PLAC') {
				echo print_findplace_link($input['name']);
			} elseif ($input['lookup'] == 'FAM') {
				echo print_findfamily_link('famid');
			} elseif ($input['lookup'] == 'SOUR') {
				echo print_findsource_link($input['name']);
			} elseif ($input['lookup'] == 'DATE') {
				echo ' <a href="#" onclick="cal_toggleDate(\'div_', WT_Filter::EscapeJs($input['name']), '\', \'', WT_Filter::EscapeJs($input['name']), '\'); return false;" class="icon-button_calendar" title="', WT_I18N::translate('Select a date'), '"></a>';
				echo '<div id="div_', WT_Filter::EscapeHtml($input['name']), '" style="position:absolute;visibility:hidden;background-color:white;"></div>';
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
	break;

case 'run':
	if (strstr($report, 'report_singlepage.xml') !== false) {
		$DEBUG = false;
		$pedigree = new ReportPedigree();
		exit;
	}

	switch ($output) {
	case 'HTML':
		header('Content-type: text/html; charset=UTF-8');
		$wt_report = new WT_Report_HTML();
		$ReportRoot = $wt_report;
		break;
	case 'PDF':
		$wt_report = new WT_Report_PDF();
		$ReportRoot = $wt_report;
		break;
	}

	/**
	 * element handlers array
	 *
	 * Converts XML element names into functions
	 *
	 * @global array $elementHandler
	 */
	$elementHandler = array();
	$elementHandler['AgeAtDeath']['start'] = 'ageAtDeathStartHandler';
	$elementHandler['br']['start'] = 'brStartHandler';
	$elementHandler['Body']['start'] = 'bodyStartHandler';
	$elementHandler['Cell']['end'] = 'cellEndHandler';
	$elementHandler['Cell']['start'] = 'cellStartHandler';
	$elementHandler['Description']['end'] = 'descriptionEndHandler';
	$elementHandler['Description']['start'] = 'descriptionStartHandler';
	$elementHandler['Doc']['end'] = 'docEndHandler';
	$elementHandler['Doc']['start'] = 'docStartHandler';
	$elementHandler['Report']['end'] = '';
	$elementHandler['Report']['start'] = '';
	$elementHandler['Facts']['end'] = 'factsEndHandler';
	$elementHandler['Facts']['start'] = 'factsStartHandler';
	$elementHandler['Footer']['start'] = 'footerStartHandler';
	$elementHandler['Footnote']['end'] = 'footnoteEndHandler';
	$elementHandler['Footnote']['start'] = 'footnoteStartHandler';
	$elementHandler['FootnoteTexts']['start'] = 'footnoteTextsStartHandler';
	$elementHandler['Gedcom']['end'] = 'gedcomEndHandler';
	$elementHandler['Gedcom']['start'] = 'gedcomStartHandler';
	$elementHandler['GedcomValue']['start'] = 'gedcomValueStartHandler';
	$elementHandler['Generation']['start'] = 'generationStartHandler';
	$elementHandler['GetPersonName']['start'] = 'getPersonNameStartHandler';
	$elementHandler['Header']['start'] = 'headerStartHandler';
	$elementHandler['HighlightedImage']['start'] = 'highlightedImageStartHandler';
	$elementHandler['if']['end'] = 'ifEndHandler';
	$elementHandler['if']['start'] = 'ifStartHandler';
	$elementHandler['Image']['start'] = 'imageStartHandler';
	$elementHandler['Input']['end'] = '';
	$elementHandler['Input']['start'] = '';
	$elementHandler['Line']['start'] = 'lineStartHandler';
	$elementHandler['List']['end'] = 'listEndHandler';
	$elementHandler['List']['start'] = 'listStartHandler';
	$elementHandler['ListTotal']['start'] = 'listTotalStartHandler';
	$elementHandler['NewPage']['start'] = 'newPageStartHandler';
	$elementHandler['Now']['start'] = 'nowStartHandler';
	$elementHandler['PageHeader']['end'] = 'pageHeaderEndHandler';
	$elementHandler['PageHeader']['start'] = 'pageHeaderStartHandler';
	$elementHandler['PageNum']['start'] = 'pageNumStartHandler';
	$elementHandler['Relatives']['end'] = 'relativesEndHandler';
	$elementHandler['Relatives']['start'] = 'relativesStartHandler';
	$elementHandler['RepeatTag']['end'] = 'repeatTagEndHandler';
	$elementHandler['RepeatTag']['start'] = 'repeatTagStartHandler';
	$elementHandler['SetVar']['start'] = 'setVarStartHandler';
	$elementHandler['Style']['start'] = 'styleStartHandler';
	$elementHandler['Text']['end'] = 'textEndHandler';
	$elementHandler['Text']['start'] = 'textStartHandler';
	$elementHandler['TextBox']['end'] = 'textBoxEndHandler';
	$elementHandler['TextBox']['start'] = 'textBoxStartHandler';
	$elementHandler['Title']['end'] = 'titleEndHandler';
	$elementHandler['Title']['start'] = 'titleStartHandler';
	$elementHandler['TotalPages']['start'] = 'totalPagesStartHandler';
	$elementHandler['var']['start'] = 'varStartHandler';
	$elementHandler['sp']['start'] = 'spStartHandler';

	/**
	 * A new object of the currently used element class
	 *
	 * @global object $currentElement
	 */
	$currentElement = new WT_Report_Base_Element();

	/**
	 * Should character data be printed
	 *
	 * This variable is turned on or off by the element handlers to tell whether the inner character
	 * Data should be printed
	 *
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
	 *
	 * @global array $printDataStack
	 */
	$printDataStack = array();

	/**
	 * @global array $wt_reportStack
	 */
	$wt_reportStack = array();

	/**
	 * @global array $gedrecStack
	 */
	$gedrecStack = array();

	/**
	 * @global array $repeatsStack
	 */
	$repeatsStack = array();

	/**
	 * @global array $parserStack
	 */
	$parserStack = array();

	/**
	 * @global array $repeats
	 */
	$repeats = array();

	/**
	 * @global string $gedrec
	 */
	$gedrec = '';

	/**
	 * @global ???? $repeatBytes
	 */
	$repeatBytes = 0;

	/**
	 * @global resource $parser
	 */
	$parser = '';

	/**
	 * @global int $processRepeats
	 */
	$processRepeats = 0;

	/**
	 * @global ???? $processIfs
	 */
	$processIfs = 0;

	/**
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

	if (!($fp = fopen($report, 'r'))) {
		die('could not open XML input');
	}
	while (($data = fread($fp, 4096))) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			die(sprintf($data . ' XML error: %s at line %d', xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
}

// We cannot add translation comments inside the XML files.
// These messages are all used in the reports.  We repeat them
// here, so we can add comments
/* I18N: An option in a list-box */
WT_I18N::translate('sort by date of birth');
/* I18N: An option in a list-box */
WT_I18N::translate('sort by date of marriage');
/* I18N: An option in a list-box */
WT_I18N::translate('sort by date of death');
