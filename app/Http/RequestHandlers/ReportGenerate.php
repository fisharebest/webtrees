<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2019 webtrees development team
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

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\ViewResponseTrait;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\ModuleReportInterface;
use Fisharebest\Webtrees\Report\ReportHtml;
use Fisharebest\Webtrees\Report\ReportParserGenerate;
use Fisharebest\Webtrees\Report\ReportPdf;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function addcslashes;
use function assert;
use function ob_get_clean;
use function ob_start;
use function redirect;
use function response;
use function route;

use const PHP_VERSION_ID;

/**
 * Show all available reports.
 */
class ReportGenerate implements RequestHandlerInterface
{
    use ViewResponseTrait;

    /**
     * @var ModuleService
     */
    private $module_service;

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
     * A list of available reports.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $user = $request->getAttribute('user');
        assert($user instanceof UserInterface);

        $data_filesystem = $request->getAttribute('filesystem.data');
        assert($data_filesystem instanceof FilesystemInterface);

        $report = $request->getAttribute('report');
        $module = $this->module_service->findByName($report);

        if (!$module instanceof ModuleReportInterface) {
            return redirect(route(ReportListPage::class, ['tree' => $tree->name()]));
        }

        Auth::checkComponentAccess($module, 'report', $tree, $user);

        $varnames  = $request->getQueryParams()['varnames'] ?? [];
        $vars      = $request->getQueryParams()['vars'] ?? [];
        $variables = [];

        foreach ($varnames as $name) {
            $variables[$name]['id'] = $vars[$name] ?? '';
        }

        $xml_filename = $module->resourcesFolder() . $module->xmlFilename();

        $format      = $request->getQueryParams()['format'] ?? '';
        $destination = $request->getQueryParams()['destination'] ?? '';

        switch ($format) {
            default:
            case 'HTML':
                ob_start();
                new ReportParserGenerate($xml_filename, new ReportHtml(), $variables, $tree, $data_filesystem);
                $html = ob_get_clean();

                $this->layout = 'layouts/report';

                $response = $this->viewResponse('report-page', [
                    'content' => $html,
                    'title'   => I18N::translate('Report'),
                ]);

                if ($destination === 'download') {
                    $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . addcslashes($report, '"') . '.html"');
                }

                return $response;

            case 'PDF':
                if (PHP_VERSION_ID >= 70400) {
                    $pr    = 'https://github.com/tecnickcom/TCPDF/pull/137';
                    $error = 'PDF reports do not currently work on PHP >= 7.4';
                    $error .= '<br>';
                    $error .= 'Waiting for <a href="' . $pr . '" class="alert-link">' . $pr . '</a>';

                    return $this->viewResponse('errors/unhandled-exception', [
                        'error' => $error,
                        'title' => 'TCPDF error',
                        'tree' => $tree,
                    ]);
                }

                ob_start();
                new ReportParserGenerate($xml_filename, new ReportPdf(), $variables, $tree, $data_filesystem);
                $pdf = ob_get_clean();

                $headers = ['Content-Type' => 'application/pdf'];

                if ($destination === 'download') {
                    $headers['Content-Disposition'] = 'attachment; filename="' . addcslashes($report, '"') . '.pdf"';
                }

                return response($pdf, StatusCodeInterface::STATUS_OK, $headers);
        }
    }
}
