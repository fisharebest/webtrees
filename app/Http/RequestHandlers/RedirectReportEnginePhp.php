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

use Fig\Http\Message\StatusCodeInterface;
use Fisharebest\Webtrees\Http\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function basename;
use function dirname;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectReportEnginePhp implements RequestHandlerInterface
{
    private TreeService $tree_service;

    /**
     * @param TreeService $tree_service
     */
    public function __construct(TreeService $tree_service)
    {
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $ged    = Validator::queryParams($request)->string('ged', Site::getPreference('DEFAULT_GEDCOM'));
        $action = Validator::queryParams($request)->string('action', '');
        $report = Validator::queryParams($request)->string('report');
        $tree   = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree && $action === 'run') {
            $params = [
                'report'   => basename(dirname($report)),
                'tree'     => $tree->name(),
                'varnames' => Validator::queryParams($request)->array('varnames'),
                'vars'     => Validator::queryParams($request)->array('vars'),
            ];

            $url = route(ReportGenerate::class, $params);

            return Registry::responseFactory()->redirectUrl($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        throw new HttpNotFoundException();
    }
}
