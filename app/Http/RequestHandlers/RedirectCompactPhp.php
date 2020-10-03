<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2020 webtrees development team
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
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\CompactTreeChartModule;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\TreeService;
use Fisharebest\Webtrees\Site;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function redirect;

/**
 * Redirect URLs created by webtrees 1.x (and PhpGedView).
 */
class RedirectCompactPhp implements RequestHandlerInterface
{
    /** @var TreeService */
    private $tree_service;

    /** @var CompactTreeChartModule */
    private $chart;

    /**
     * @param CompactTreeChartModule $chart
     * @param TreeService            $tree_service
     */
    public function __construct(CompactTreeChartModule $chart, TreeService $tree_service)
    {
        $this->chart        = $chart;
        $this->tree_service = $tree_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $query   = $request->getQueryParams();
        $ged     = $query['ged'] ?? Site::getPreference('DEFAULT_GEDCOM');
        $root_id = $query['rootid'] ?? '';

        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $individual = Registry::individualFactory()->make($root_id, $tree) ?? $tree->significantIndividual(Auth::user());

            $url = $this->chart->chartUrl($individual, []);

            return redirect($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        throw new HttpNotFoundException();
    }
}
