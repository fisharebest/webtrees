<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2022 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Report\ReportParserSetup;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;
use function route;

/**
 * Get parameters for a report.
 */
class ReportSetupPage implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private ModuleService $module_service;

    /**
     * ReportEngineController constructor.
     *
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = Validator::attributes($request)->tree();
        $user = Validator::attributes($request)->user();

        $report = Validator::attributes($request)->string('report');
        $module = $this->module_service->findByName($report);

        if (!$module instanceof ModuleReportInterface) {
            return redirect(route(ReportListPage::class, ['tree' => $tree->name()]));
        }

        Auth::checkComponentAccess($module, ModuleReportInterface::class, $tree, $user);

        $xref = Validator::queryParams($request)->isXref()->string('xref', '');

        $xml_filename = $module->resourcesFolder() . $module->xmlFilename();

        $report_array = (new ReportParserSetup($xml_filename))->reportProperties();
        $description  = $report_array['description'];
        $title        = $report_array['title'];

        $inputs = [];

        foreach ($report_array['inputs'] ?? [] as $n => $input) {
            $input += [
                'type'    => 'text',
                'default' => '',
                'lookup'  => '',
                'extra'   => '',
            ];

            $attributes = [
                'id'    => 'input-' . $n,
                'name'  => 'vars[' . $input['name'] . ']',
                'class' => $input['type'] === 'checkbox' ? 'form-control-check' : 'form-control',
            ];

            switch ($input['lookup']) {
                case 'INDI':
                    $input['control'] = view('components/select-individual', [
                        'id'         => 'input-' . $n,
                        'name'       => 'vars[' . $input['name'] . ']',
                        'individual' => Registry::individualFactory()->make($xref, $tree),
                        'tree'       => $tree,
                        'required'   => true,
                    ]);
                    break;

                case 'FAM':
                    $input['control'] = view('components/select-family', [
                        'id'       => 'input-' . $n,
                        'name'     => 'vars[' . $input['name'] . ']',
                        'family'   => Registry::familyFactory()->make($xref, $tree),
                        'tree'     => $tree,
                        'required' => true,
                    ]);
                    break;

                case 'SOUR':
                    $input['control'] = view('components/select-source', [
                        'id'       => 'input-' . $n,
                        'name'     => 'vars[' . $input['name'] . ']',
                        'family'   => Registry::sourceFactory()->make($xref, $tree),
                        'tree'     => $tree,
                        'required' => true,
                    ]);
                    break;

                case 'DATE':
                    // Need to know if the user prefers DMY/MDY/YMD so we can validate dates properly.
                    $dmy = I18N::language()->dateOrder();

                    $attributes += [
                        'type'     => 'text',
                        'value'    => $input['default'],
                        'dir'      => 'ltr',
                        'onchange' => 'webtrees.reformatDate(this, "' . $dmy . '")'
                    ];
                    $input['control'] = '<input ' . Html::attributes($attributes) . '>';
                    $input['extra'] = view('edit/input-addon-calendar', ['id' => 'input-' . $n]);
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
                            foreach (explode('|', $input['options']) as $option) {
                                [$key, $value] = explode('=>', $option);
                                if (preg_match('/^I18N::number\((.+?)(,([\d+]))?\)$/', $value, $match)) {
                                    $number        = (float) $match[1];
                                    $precision     = (int) ($match[3] ?? 0);
                                    $options[$key] = I18N::number($number, $precision);
                                } elseif (preg_match('/^I18N::translate\(\'(.+)\'\)$/', $value, $match)) {
                                    $options[$key] = I18N::translate($match[1]);
                                } elseif (preg_match('/^I18N::translateContext\(\'(.+)\', *\'(.+)\'\)$/', $value, $match)) {
                                    $options[$key] = I18N::translateContext($match[1], $match[2]);
                                }
                            }
                            $input['control'] = view('components/select', ['name' => 'vars[' . $input['name'] . ']', 'id' => 'input-' . $n, 'selected' => $input['default'], 'options' => $options]);
                            break;
                    }
            }

            $inputs[] = $input;
        }

        $destination = $user->getPreference('default-report-destination', 'view');
        $format      = $user->getPreference('default-report-format', 'PDF');

        return $this->viewResponse('report-setup-page', [
            'description' => $description,
            'destination' => $destination,
            'format'      => $format,
            'inputs'      => $inputs,
            'report'      => $report,
            'title'       => $title,
            'tree'        => $tree,
        ]);
    }
}
