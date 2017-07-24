<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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
namespace Fisharebest\Webtrees;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Report\ReportHtml;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Report\ReportParserSetup;
use Fisharebest\Webtrees\Report\ReportPdf;

/** @global Tree $WT_TREE */
global $WT_TREE;

require 'includes/session.php';

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
	$vars = [];
}
if (!is_array($varnames)) {
	$varnames = [];
}
if (!is_array($type)) {
	$type = [];
}

//-- setup the arrays
$newvars = [];
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

foreach ($varnames as $name) {
	if (!isset($vars[$name])) {
		$vars[$name]['id'] = '';
	}
}

$reports = [];
foreach (Module::getActiveReports($WT_TREE) as $rep) {
	$menu = $rep->getReportMenu();
	if (preg_match('/report=(' . preg_quote(WT_MODULES_DIR, '/') . '[a-z0-9_]+\/[a-z0-9_]+\.xml)/', $menu->getLink(), $match)) {
		$reports[$match[1]] = $menu->getLabel();
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
		<h2 class="wt-page-title">', I18N::translate('Choose a report to run'), '</h2>
		<form name="choosereport" action="reportengine.php">
		<input type="hidden" name="action" value="setup">
		<input type="hidden" name="output" value="', Html::escape($output), '">
		<table class="facts_table width40">
		<tr><td class="descriptionbox wrap width33 vmiddle">', I18N::translate('Report'), '</td>
		<td class="optionbox"><select name="report">';
	foreach ($reports as $file => $report) {
		echo '<option value="', Html::escape($file), '">', Html::escape($report), '</option>';
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
		->pageHeader();

	FunctionsPrint::initializeCalendarPopup();

	echo '<div id="reportengine-page">
		<h2 class="wt-page-title">', $report_array['title'], '</h2>
		<form name="setupreport" action="reportengine.php">
		<input type="hidden" name="action" value="run">
		<input type="hidden" name="report" value="', Html::escape($report), '">
		<table class="facts_table width50">
		<tr><td class="descriptionbox width30 wrap">', I18N::translate('Report'), '</td><td class="optionbox">', $report_array['description'], '</td></tr>';

	if (!isset($report_array['inputs'])) {
		$report_array['inputs'] = [];
	}
	foreach ($report_array['inputs'] as $input) {
		echo '<tr><td class="descriptionbox wrap">';
		echo '<input type="hidden" name="varnames[]" value="', Html::escape($input['name']), '">';
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

		$attributes = [
			'id'   => 'vars[' . $input['name'] . ']',
			'name' => 'vars[' . $input['name'] . ']',
		];

		if ($input['lookup'] === 'INDI') {
			$individual = Individual::getInstance($pid, $WT_TREE) ?: $controller->getSignificantIndividual();
			echo FunctionsEdit::formControlIndividual($individual, $attributes);
		} elseif ($input['lookup'] === 'FAM') {
			$family = Family::getInstance($pid, $WT_TREE) ?: $controller->getSignificantFamily();
			echo FunctionsEdit::formControlFamily($family, $attributes);
		} elseif ($input['lookup'] === 'SOUR') {
			$source = Source::getInstance($pid, $WT_TREE);
			echo FunctionsEdit::formControlSource($source, $attributes);
		} elseif ($input['type'] == 'text') {
			echo '<input type="text" name="vars[', Html::escape($input['name']), ']" id="', Html::escape($input['name']), '" value="', Html::escape($input['default']), '" style="direction: ltr;">';
		}
		if ($input['type'] == 'checkbox') {
			echo '<input type="checkbox" name="vars[', Html::escape($input['name']), ']" id="', Html::escape($input['name']), '" value="1" ';
			echo $input['default'] == '1' ? 'checked' : '';
			echo '>';
		}
		if ($input['type'] == 'select') {
			$options = [];
			foreach (preg_split('/[|]+/', $input['options']) as $option) {
				list($key, $value) = explode('=>', $option);
				if (preg_match('/^I18N::number\((.+?)(,([\d+]))?\)$/', $value, $match)) {
					$options[$key] = I18N::number($match[1], isset($match[3]) ? $match[3] : 0);
				} elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $value, $match)) {
					$options[$key] = I18N::translate($match[1]);
				} elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $value, $match)) {
					$options[$key] = I18N::translateContext($match[1], $match[2]);
				}
			}
			echo Bootstrap4::select($options, $input['default'], $attributes);
		}
		if (isset($input['lookup'])) {
			echo '<input type="hidden" name="type[', Html::escape($input['name']), ']" value="', Html::escape($input['lookup']), '">';
			if ($input['lookup'] == 'DATE') {
				echo FontAwesome::linkIcon('calendar', I18N::translate('Select a date'), ['class' => 'btn btn-link', 'href' => '#', 'onclick' => 'return calendarWidget("div_' . $input['name'] . '", "' . $input['name'] . '");']);
				echo '<div id="div_', Html::escape($input['name']), '" style="position:absolute;visibility:hidden;background-color:white;"></div>';
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
	// Only generate the content for interactive users (not search robots).
	if (Session::has('initiated')) {
		if (strstr($report, 'report_singlepage.xml') !== false) {
			// This is a custom module.
			new \ReportPedigree;
			break;
		}

		switch ($output) {
		case 'HTML':
			header('Content-type: text/html; charset=UTF-8');
			new ReportParserGenerate($report, new ReportHtml, $vars);
			break;
		case 'PDF':
			new ReportParserGenerate($report, new ReportPdf, $vars);
			break;
		}
	}
}
