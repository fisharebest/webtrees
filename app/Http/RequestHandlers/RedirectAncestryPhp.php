<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Exceptions\HttpNotFoundException;
use Fisharebest\Webtrees\Module\AncestorsChartModule;
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
class RedirectAncestryPhp implements RequestHandlerInterface
{
    private const CHART_STYLES = [
        '0' => 'tree',
        '1' => 'tree',
        '2' => 'individuals',
        '3' => 'families',
    ];

    /** @var TreeService */
    private $tree_service;

    /** @var AncestorsChartModule */
    private $chart;

    /**
     * @param AncestorsChartModule $chart
     * @param TreeService          $tree_service
     */
    public function __construct(AncestorsChartModule $chart, TreeService $tree_service)
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
        $query       = $request->getQueryParams();
        $ged         = $query['ged'] ?? Site::getPreference('DEFAULT_GEDCOM');
        $root_id     = $query['rootid'] ?? '';
        $generations = $query['PEDIGREE_GENERATIONS'] ?? '4';
        $chart_style = $query['chart_style'] ?? '';

        $tree = $this->tree_service->all()->get($ged);

        if ($tree instanceof Tree) {
            $individual = Registry::individualFactory()->make($root_id, $tree) ?? $tree->significantIndividual(Auth::user());

            $url = $this->chart->chartUrl($individual, [
                'generations' => $generations,
                'style'       => self::CHART_STYLES[$chart_style] ?? 'tree',
            ]);

            return redirect($url, StatusCodeInterface::STATUS_MOVED_PERMANENTLY);
        }

        throw new HttpNotFoundException();
    }
}
