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

namespace Fisharebest\Webtrees\Module;

use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Http\Middleware\AuthNotRobot;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

/**
 * Class CompactTreeChartModule
 */
class CompactTreeChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL = '/tree/{tree}/compact/{xref}';

    private ChartService $chart_service;

    /**
     * @param ChartService $chart_service
     */
    public function __construct(ChartService $chart_service)
    {
        $this->chart_service = $chart_service;
    }

    /**
     * Initialization.
     *
     * @return void
     */
    public function boot(): void
    {
        Registry::routeFactory()->routeMap()
            ->get(static::class, static::ROUTE_URL, $this)
            ->allows(RequestMethodInterface::METHOD_POST)
            ->extras(['middleware' => [AuthNotRobot::class]]);
    }

    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Compact tree');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “CompactTreeChart” module */
        return I18N::translate('A chart of an individual’s ancestors, as a compact tree.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-compact';
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu|null
     */
    public function chartBoxMenu(Individual $individual): ?Menu
    {
        return $this->chartMenu($individual);
    }

    /**
     * The title for a specific instance of this chart.
     *
     * @param Individual $individual
     *
     * @return string
     */
    public function chartTitle(Individual $individual): string
    {
        /* I18N: %s is an individual’s name */
        return I18N::translate('Compact tree of %s', $individual->fullName());
    }

    /**
     * The URL for a page showing chart options.
     *
     * @param Individual                                $individual
     * @param array<bool|int|string|array<string>|null> $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(static::class, [
                'xref' => $individual->xref(),
                'tree' => $individual->tree()->name(),
            ] + $parameters);
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
        $xref = Validator::attributes($request)->isXref()->string('xref');
        $ajax = Validator::queryParams($request)->boolean('ajax', false);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree' => $tree->name(),
                'xref' => Validator::parsedBody($request)->string('xref', ''),
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, false, true);

        if ($ajax) {
            $this->layout = 'layouts/ajax';

            return $this->viewResponse('modules/compact-chart/chart', [
                'ancestors' => $this->chart_service->sosaStradonitzAncestors($individual, 5),
                'module'    => $this,
            ]);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax' => true,
        ]);

        return $this->viewResponse('modules/compact-chart/page', [
            'ajax_url'   => $ajax_url,
            'individual' => $individual,
            'module'     => $this->name(),
            'title'      => $this->chartTitle($individual),
            'tree'       => $tree,
        ]);
    }
}
