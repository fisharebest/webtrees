<?php
namespace Fisharebest\Webtrees;

/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Report\ReportBaseElement;
use Fisharebest\Webtrees\Report\ReportHtml;
use Fisharebest\Webtrees\Report\ReportPdf;
use Fisharebest\Webtrees\Report\ReportParserSetup;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

define('WT_SCRIPT_NAME', 'reportengine.php');
require './includes/session.php';

$controller = new PageController;

$famid    = Filter::get('famid', WT_REGEX_XREF);
$pid      = Filter::get('pid', WT_REGEX_XREF);
$action   = Filter::get('action', 'choose|setup|run', 'choose');
$report   = Filter::get('report');
$output   = Filter::get('output', 'HTML|PDF', 'PDF');
$vars     = Filter::get('vars');
$varnames = Filter::get('varnames');
$type     = Filter::get('type');
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
			$record = Individual::getInstance($var, $WT_TREE);
			if ($record && $record->canShowName()) {
				$newvars[$name]['gedcom'] = $record->privatizeGedcom(Auth::accessLevel($WT_TREE));
			} else {
				$action = 'setup';
			}
			break;
		case 'FAM':
			$record = Family::getInstance($var, $WT_TREE);
			if ($record && $record->canShowName()) {
				$newvars[$name]['gedcom'] = $record->privatizeGedcom(Auth::accessLevel($WT_TREE));
			} else {
				$action = 'setup';
			}
			break;
		case 'SOUR':
			$record = Source::getInstance($var, $WT_TREE);
			if ($record && $record->canShowName()) {
				$newvars[$name]['gedcom'] = $record->privatizeGedcom(Auth::accessLevel($WT_TREE));
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
foreach (Module::getActiveReports($WT_TREE) as $rep) {
	foreach ($rep->getReportMenus() as $menu) {
		if (preg_match('/report=(' . preg_quote(WT_MODULES_DIR, '/') . '[a-z0-9_]+\/[a-z0-9_]+\.xml)/', $menu->getLink(), $match)) {
			$reports[$match[1]] = $menu->getLabel();
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
		->setPageTitle(I18N::translate('Choose a report to run'))
		->pageHeader();

	echo '<div id="reportengine-page">
		<form name="choosereport" method="get" action="reportengine.php">
		<input type="hidden" name="action" value="setup">
		<input type="hidden" name="output" value="', Filter::escapeHtml($output), '">
		<table class="facts_table width40">
		<tr><td class="topbottombar" colspan="2">', I18N::translate('Choose a report to run'), '</td></tr>
		<tr><td class="descriptionbox wrap width33 vmiddle">', I18N::translate('Report'), '</td>
		<td class="optionbox"><select name="report">';
	foreach ($reports as $file => $report) {
		echo '<option value="', Filter::escapeHtml($file), '">', Filter::escapeHtml($report), '</option>';
	}
	echo '</select></td></tr>
		<tr><td class="topbottombar" colspan="2"><input type="submit" value="', I18N::translate('continue'), '"></td></tr>
		</table></form></div>';
	break;

case 'setup':
	$report_setup = new ReportParserSetup($report);
	$report_array = $report_setup->reportProperties();

	$controller
		->setPageTitle($report_array['title'])
		->pageHeader()
		->addExternalJavascript(WT_AUTOCOMPLETE_JS_URL)
		->addInlineJavascript('autocomplete();');

	init_calendar_popup();

	echo '<div id="reportengine-page">
		<form name="setupreport" method="get" action="reportengine.php" onsubmit="if (this.output[1].checked) {this.target=\'_blank\';}">
		<input type="hidden" name="action" value="run">
		<input type="hidden" name="report" value="', Filter::escapeHtml($report), '">
		<table class="facts_table width50">
		<tr><td class="topbottombar" colspan="2">', I18N::translate('Enter report values'), '</td></tr>
		<tr><td class="descriptionbox width30 wrap">', I18N::translate('Report'), '</td><td class="optionbox">', $report_array['title'], '<br>', $report_array['description'], '</td></tr>';

	if (!isset($report_array['inputs'])) {
		$report_array['inputs'] = array();
	}
	foreach ($report_array['inputs'] as $input) {
		echo '<tr><td class="descriptionbox wrap">';
		echo '<input type="hidden" name="varnames[]" value="', Filter::escapeHtml($input["name"]), '">';
		echo I18N::translate($input['value']), '</td><td class="optionbox">';
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

			echo ' type="text" name="vars[', Filter::escapeHtml($input['name']), ']" id="', Filter::escapeHtml($input['name']), '" value="', Filter::escapeHtml($input['default']), '" style="direction: ltr;">';
		}
		if ($input['type'] == 'checkbox') {
			echo '<input type="checkbox" name="vars[', Filter::escapeHtml($input['name']), ']" id="', Filter::escapeHtml($input['name']), '" value="1" ';
			echo $input['default'] == '1' ? 'checked' : '';
			echo '>';
		}
		if ($input['type'] == 'select') {
			echo '<select name="vars[', Filter::escapeHtml($input['name']), ']" id="', Filter::escapeHtml($input['name']), '_var">';
			$options = preg_split('/[|]+/', $input['options']);
			foreach ($options as $option) {
				$opt                   = explode('=>', $option);
				list($value, $display) = $opt;
				if (preg_match('/^I18N::number\((.+)\)$/', $display, $match)) {
					$display = I18N::number($match[1]);
				} elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $display, $match)) {
					$display = I18N::translate($match[1]);
				} elseif (preg_match('/^I18N::translate_c\(\'(.+)\', *\'(.+)\'\)$/', $display, $match)) {
					$display = I18N::translateContext($match[1], $match[2]);
				}
				echo '<option value="', Filter::escapeHtml($value), '" ';
				if ($opt[0] == $input['default']) {
					echo 'selected';
				}
				echo '>', Filter::escapeHtml($display), '</option>';
			}
			echo '</select>';
		}
		if (isset($input['lookup'])) {
			echo '<input type="hidden" name="type[', Filter::escapeHtml($input['name']), ']" value="', Filter::escapeHtml($input['lookup']), '">';
			if ($input['lookup'] == 'INDI') {
				echo print_findindi_link('pid');
			} elseif ($input['lookup'] == 'PLAC') {
				echo print_findplace_link($input['name']);
			} elseif ($input['lookup'] == 'FAM') {
				echo print_findfamily_link('famid');
			} elseif ($input['lookup'] == 'SOUR') {
				echo print_findsource_link($input['name']);
			} elseif ($input['lookup'] == 'DATE') {
				echo ' <a href="#" onclick="cal_toggleDate(\'div_', Filter::EscapeJs($input['name']), '\', \'', Filter::EscapeJs($input['name']), '\'); return false;" class="icon-button_calendar" title="', I18N::translate('Select a date'), '"></a>';
				echo '<div id="div_', Filter::EscapeHtml($input['name']), '" style="position:absolute;visibility:hidden;background-color:white;"></div>';
			}
		}
		echo '</td></tr>';
	}
	echo '<tr>
		<td colspan="2" class="optionbox">
		<div class="report-type">
		<div>
		<label for="PDF"><i class="icon-mime-application-pdf"></i></label>
		<p><input type="radio" name="output" value="PDF" id="PDF" checked></p>
		</div>
		<div>
		<label for="HTML"><i class="icon-mime-text-html"></i></label>
		<p><input type="radio" name="output" id="HTML" value="HTML"></p>
		</div>
		</div>
		</td>
		</tr>
		<tr><td class="topbottombar" colspan="2">
		<input type="submit" value="', I18N::translate('continue'), '">
		</td></tr></table></form></div>';
	break;

case 'run':
	if (strstr($report, 'report_singlepage.xml') !== false) {
		$DEBUG    = false;
		$pedigree = new \ReportPedigree;

		return;
	}

	switch ($output) {
	case 'HTML':
		header('Content-type: text/html; charset=UTF-8');
		$wt_report  = new ReportHtml;
		$ReportRoot = $wt_report;
		break;
	case 'PDF':
		$wt_report  = new ReportPdf;
		$ReportRoot = $wt_report;
		break;
	}

	/**
	 * element handlers array
	 *
	 * Converts XML element names into functions
	 *
	 * @global string[][] $elementHandler
	 */
	$elementHandler = array(
		'AgeAtDeath' => array(
			'start' => '\Fisharebest\Webtrees\Report\\ageAtDeathStartHandler',
		),
		'br' => array(
			'start' => '\Fisharebest\Webtrees\Report\\brStartHandler',
		),
		'Body' => array(
			'start' => '\Fisharebest\Webtrees\Report\\bodyStartHandler',
		),
		'Cell' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\cellEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\cellStartHandler',
		),
		'Description' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\descriptionEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\descriptionStartHandler',
		),
		'Doc' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\docEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\docStartHandler',
		),
		'Report' => array(
			'end'   => '',
			'start' => '',
		),
		'Facts' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\factsEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\factsStartHandler',
		),
		'Footer' => array(
			'start' => '\Fisharebest\Webtrees\Report\\footerStartHandler',
		),
		'Footnote' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\footnoteEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\footnoteStartHandler',
		),
		'FootnoteTexts' => array(
			'start' => '\Fisharebest\Webtrees\Report\\footnoteTextsStartHandler',
		),
		'Gedcom' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\gedcomEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\gedcomStartHandler',
		),
		'GedcomValue' => array(
			'start' => '\Fisharebest\Webtrees\Report\\gedcomValueStartHandler',
		),
		'Generation' => array(
			'start' => '\Fisharebest\Webtrees\Report\\generationStartHandler',
		),
		'GetPersonName' => array(
			'start' => '\Fisharebest\Webtrees\Report\\getPersonNameStartHandler',
		),
		'Header' => array(
			'start' => '\Fisharebest\Webtrees\Report\\headerStartHandler',
		),
		'HighlightedImage' => array(
			'start' => '\Fisharebest\Webtrees\Report\\highlightedImageStartHandler',
		),
		'if' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\ifEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\ifStartHandler',
		),
		'Image' => array(
			'start' => '\Fisharebest\Webtrees\Report\\imageStartHandler',
		),
		'Input' => array(
			'end'  => '',
			'start'=> '',
		),
		'Line' => array(
			'start' => '\Fisharebest\Webtrees\Report\\lineStartHandler',
		),
		'List' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\listEndHandler',
			'start'=> '\Fisharebest\Webtrees\Report\\listStartHandler',
		),
		'ListTotal' => array(
			'start' => '\Fisharebest\Webtrees\Report\\listTotalStartHandler',
		),
		'NewPage' => array(
			'start' => '\Fisharebest\Webtrees\Report\\newPageStartHandler',
		),
		'Now' => array(
			'start' => '\Fisharebest\Webtrees\Report\\nowStartHandler',
		),
		'PageHeader' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\pageHeaderEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\pageHeaderStartHandler',
		),
		'PageNum' => array(
			'start' => '\Fisharebest\Webtrees\Report\\pageNumStartHandler',
		),
		'Relatives' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\relativesEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\relativesStartHandler',
		),
		'RepeatTag' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\repeatTagEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\repeatTagStartHandler',
		),
		'SetVar' => array(
			'start' => '\Fisharebest\Webtrees\Report\\setVarStartHandler',
		),
		'Style' => array(
			'start' => '\Fisharebest\Webtrees\Report\\styleStartHandler',
		),
		'Text' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\textEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\textStartHandler',
		),
		'TextBox' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\textBoxEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\textBoxStartHandler',
		),
		'Title' => array(
			'end'   => '\Fisharebest\Webtrees\Report\\titleEndHandler',
			'start' => '\Fisharebest\Webtrees\Report\\titleStartHandler',
		),
		'TotalPages' => array(
			'start' => '\Fisharebest\Webtrees\Report\\totalPagesStartHandler',
		),
		'var' => array(
			'start' => '\Fisharebest\Webtrees\Report\\varStartHandler',
		),
		'sp' => array(
			'start' => '\Fisharebest\Webtrees\Report\\spStartHandler',
		),
	);

	/**
	 * A new object of the currently used element class
	 *
	 * @global ReportBaseElement $currentElement
	 */
	$currentElement = new ReportBaseElement;

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
	xml_set_element_handler($xml_parser, '\Fisharebest\Webtrees\Report\\startElement', '\Fisharebest\Webtrees\Report\\endElement');
	//-- set the character data handler
	xml_set_character_data_handler($xml_parser, '\Fisharebest\Webtrees\Report\\characterData');

	$fp = fopen($report, 'r');
	while (($data = fread($fp, 4096))) {
		if (!xml_parse($xml_parser, $data, feof($fp))) {
			throw new \DomainException(sprintf(
				'XML error: %s at line %d',
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)
			));
		}
	}
	xml_parser_free($xml_parser);
}

