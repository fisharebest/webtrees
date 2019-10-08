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

namespace Fisharebest\Webtrees\Module;

use Aura\Router\RouterContainer;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
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

    private const ROUTE_NAME = 'compact-chart';
    private const ROUTE_URL  = '/tree/{tree}/compact/{xref}';

    /** @var ChartService */
    private $chart_service;

    /**
     * CompactTreeChartModule constructor.
     *
     * @param ChartService $chart_service
     */
    public function __construct(ChartService $chart_service)
    {
        $this->chart_service = $chart_service;
    }

    /**
     * Initialization.
     *
     * @param RouterContainer $router_container
     */
    public function boot(RouterContainer $router_container)
    {
        $router_container->getMap()
            ->get(self::ROUTE_NAME, self::ROUTE_URL, self::class)
            ->allows(RequestMethodInterface::METHOD_POST);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
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
     * @param Individual $individual
     * @param string[]   $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(self::ROUTE_NAME, [
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
        $tree       = $request->getAttribute('tree');
        $user       = $request->getAttribute('user');
        $xref       = $request->getAttribute('xref');
        $ajax       = $request->getQueryParams()['ajax'] ?? '';
        $individual = Individual::getInstance($xref, $tree);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(self::ROUTE_NAME, [
                'tree' => $request->getAttribute('tree')->name(),
                'xref' => $request->getParsedBody()['xref'],
            ]));
        }

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        if ($ajax === '1') {
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
        ]);
    }
}
