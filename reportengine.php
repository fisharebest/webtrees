<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
	if (preg_match('/report=(' . preg_quote(WT_MODULES_DIR, '/') . '[a-zA-Z0-9_-]+\/[a-zA-Z0-9_-]+\.xml)/', $menu->getLink(), $match)) {
		$reports[$match[1]] = $menu->getLabel();
	}
}

if (!empty($report)) {
	if (!array_key_exists($report, $reports)) {
		$action = 'choose';
	}
}

switch ($action) {
case 'choose':
	$controller
		->setPageTitle(I18N::translate('Choose a report to run'))
		->pageHeader();

	echo View::make('report-select-page', [
		'reports' => $reports,
	]);

	break;

case 'setup':
	$report_array = (new ReportParserSetup($report))->reportProperties();

	$controller
		->setPageTitle($report_array['title'])
		->pageHeader();

	FunctionsPrint::initializeCalendarPopup();

	$inputs = [];

	foreach ($report_array['inputs'] ?? [] as $n => $input) {
		$input += [
			'type'    => 'text',
			'default' => '',
			'lookup'  => '',
			'extra'   => '',
		];

		$attributes = [
			'id'   => 'input-' . $n,
			'name' => 'vars[' . $input['name'] . ']',
		];

		switch ($input['lookup']) {
			case 'INDI':
				$individual       = Individual::getInstance($pid, $WT_TREE) ?: $controller->getSignificantIndividual();
				$input['control'] = FunctionsEdit::formControlIndividual($individual, $attributes);
				break;
			case 'FAM':
				$family           = Family::getInstance($pid, $WT_TREE) ?: $controller->getSignificantFamily();
				$input['control'] = FunctionsEdit::formControlFamily($family, $attributes);
				break;
			case 'SOUR':
				$source           = Source::getInstance($pid, $WT_TREE);
				$input['control'] = FunctionsEdit::formControlSource($source, $attributes);
				break;
			case 'DATE':
				$attributes += [
					'type'  => 'text',
					'value' => $input['default'],
				];
				$input['control'] = '<input ' . Html::attributes($attributes) . '>';
				$input['extra']   = FontAwesome::linkIcon('calendar', I18N::translate('Select a date'), [
					'class'   => 'btn btn-link',
					'href'    => '#',
					'onclick' => 'return calendarWidget("calendar-widget-' . $n . '", "input-' . $n . '");',
				]) . '<div id="calendar-widget-' . $n . '" style="position:absolute;visibility:hidden;background-color:white;z-index:1000;"></div>';
				break;
			default:
				switch ($input['type']) {
					case 'text':
						$attributes += [
							'type'  => 'text',
							'value' => $input['default'],
						];
						$input['control'] = '<input ' . Html::attributes($attributes) . '>';
						break;
					case 'checkbox':
						$attributes += [
							'type'    => 'checkbox',
							'checked' => (bool) $input['default'],
						];
						$input['control'] = '<input ' . Html::attributes($attributes) . '>';
						break;
					case 'select':
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
						$input['control'] = Bootstrap4::select($options, $input['default'], $attributes);
						break;
				}
		}

		$inputs[] = $input;
	}

	echo View::make('report-setup-page', [
		'title'       => $report_array['title'],
		'description' => $report_array['description'],
		'inputs'      => $inputs,
		'report'      => $report,
	]);

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
