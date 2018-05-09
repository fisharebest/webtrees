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
declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\Controllers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Report\ReportBase;
use Fisharebest\Webtrees\Report\ReportHtml;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Report\ReportParserSetup;
use Fisharebest\Webtrees\Report\ReportPdf;
use Fisharebest\Webtrees\Source;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Controller for help text.
 */
class ReportEngineController extends AbstractBaseController {
	/**
	 * A list of available reports.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reportList(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$reports = $this->allReports($tree);
		$title   = I18N::translate('Choose a report to run');

		return $this->viewResponse('report-select-page', [
			'reports' => $reports,
			'title'   => $title,
		]);

	}

	/**
	 * Fetch the options/parameters for a report.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reportSetup(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$pid     = $request->get('pid');
		$report  = $request->get('report');
		$reports = $this->allReports($tree);

		if (!array_key_exists($report, $reports)) {
			return $this->reportList($request);
		}

		$report_xml = WT_ROOT . WT_MODULES_DIR . $report . '/report.xml';

		$report_array = (new ReportParserSetup($report_xml, new ReportBase, [], $tree))->reportProperties();
		$description  = $report_array['description'];
		$title        = $report_array['title'];

		//FunctionsPrint::initializeCalendarPopup();

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
					$individual       = Individual::getInstance($pid, $tree);
					$input['control'] = FunctionsEdit::formControlIndividual($tree, $individual, $attributes);
					break;
				case 'FAM':
					$family           = Family::getInstance($pid, $tree);
					$input['control'] = FunctionsEdit::formControlFamily($tree, $family, $attributes);
					break;
				case 'SOUR':
					$source           = Source::getInstance($pid, $tree);
					$input['control'] = FunctionsEdit::formControlSource($tree, $source, $attributes);
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

		return $this->viewResponse('report-setup-page', [
			'description' => $description,
			'inputs'      => $inputs,
			'report'      => $report,
			'title'       => $title,
		]);

	}

	/**
	 * Generate a report.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function reportRun(Request $request): Response {
		/** @var Tree $tree */
		$tree = $request->attributes->get('tree');

		$report   = $request->get('report');
		$output   = $request->get('output');
		$vars     = $request->get('vars');
		$varnames = $request->get('varnames');
		$type     = $request->get('type');

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
						$record = Individual::getInstance($var, $tree);
						if ($record && $record->canShowName()) {
							$newvars[$name]['gedcom'] = $record->privatizeGedcom(Auth::accessLevel($tree));
						} else {
							return $this->reportSetup($request);
						}
						break;
					case 'FAM':
						$record = Family::getInstance($var, $tree);
						if ($record && $record->canShowName()) {
							$newvars[$name]['gedcom'] = $record->privatizeGedcom(Auth::accessLevel($tree));
						} else {
							return $this->reportSetup($request);
						}
						break;
					case 'SOUR':
						$record = Source::getInstance($var, $tree);
						if ($record && $record->canShowName()) {
							$newvars[$name]['gedcom'] = $record->privatizeGedcom(Auth::accessLevel($tree));
						} else {
							return $this->reportSetup($request);
						}
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

		$report_xml = WT_ROOT . WT_MODULES_DIR . $report . '/report.xml';


		switch ($output) {
			default:
			case 'HTML':
				ob_start();
				new ReportParserGenerate($report_xml, new ReportHtml, $vars, $tree);
				$html = ob_get_clean();

				//$this->layout = 'layouts/report';

				return $this->viewResponse('report-page', [
					'content' => $html,
					'title'   => I18N::translate('Report'),
				]);

				break;
			case 'PDF':
				ob_start();
				new ReportParserGenerate($report_xml, new ReportPdf, $vars, $tree);
				$pdf = ob_get_clean();

				$response = new Response($pdf);

				$disposition = $response->headers->makeDisposition(
					ResponseHeaderBag::DISPOSITION_ATTACHMENT,
					$report . '.pdf'
				);

				$response->headers->set('Content-Disposition', $disposition);
				$response->headers->set('Content-Type', 'application/pdf');

				return $response;
		}
	}

	/**
	 * A list of all available reports.
	 *
	 * @param Tree $tree
	 *
	 * @return string[]
	 */
	private function allReports(Tree $tree) {
		$reports = [];

		foreach (Module::getActiveReports($tree) as $report) {
			$reports[$report->getName()] = $report->getTitle();
		}

		return $reports;
	}
}
