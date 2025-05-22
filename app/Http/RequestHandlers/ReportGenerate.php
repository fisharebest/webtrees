<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Report\HtmlRenderer;
use Fisharebest\Webtrees\Report\PdfRenderer;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function addcslashes;
use function ob_get_clean;
use function ob_start;
use function redirect;
use function response;
use function route;

/**
 * Show all available reports.
 */
class ReportGenerate implements RequestHandlerInterface
{
    use ViewResponseTrait;

    private ModuleService $module_service;

    /**
     * @param ModuleService $module_service
     */
    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * A list of available reports.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree   = Validator::attributes($request)->tree();
        $user   = Validator::attributes($request)->user();
        $report = Validator::attributes($request)->string('report');
        $module = $this->module_service->findByName($report);

        if (!$module instanceof ModuleReportInterface) {
            return redirect(route(ReportListPage::class, ['tree' => $tree->name()]));
        }

        Auth::checkComponentAccess($module, ModuleReportInterface::class, $tree, $user);

        $varnames  = Validator::queryParams($request)->array('varnames');
        $vars      = Validator::queryParams($request)->array('vars');
        $variables = [];

        foreach ($varnames as $name) {
            $variables[$name]['id'] = $vars[$name] ?? '';
        }

        $xml_filename = $module->resourcesFolder() . $module->xmlFilename();
        $format       = Validator::queryParams($request)->string('format');
        $destination  = Validator::queryParams($request)->string('destination');

        $user->setPreference('default-report-destination', $destination);
        $user->setPreference('default-report-format', $format);

        switch ($format) {
            default:
            case 'HTML':
                ob_start();
                new ReportParserGenerate($xml_filename, new HtmlRenderer(), $variables, $tree);
                $html = ob_get_clean();

                $this->layout = 'layouts/report';

                $response = $this->viewResponse('report-page', [
                    'content' => $html,
                    'title'   => I18N::translate('Report'),
                ]);

                if ($destination === 'download') {
                    $response = $response->withHeader('content-disposition', 'attachment; filename="' . addcslashes($report, '"') . '.html"');
                }

                return $response;

            case 'PDF':
                ob_start();
                new ReportParserGenerate($xml_filename, new PdfRenderer(), $variables, $tree);
                $pdf = ob_get_clean();

                $headers = ['content-type' => 'application/pdf'];

                if ($destination === 'download') {
                    $headers['content-disposition'] = 'attachment; filename="' . addcslashes($report, '"') . '.pdf"';
                }

                return response($pdf, StatusCodeInterface::STATUS_OK, $headers);
        }
    }
}
