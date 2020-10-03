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

namespace Fisharebest\Webtrees\Module;

use Aura\Router\RouterContainer;
use Fig\Http\Message\RequestMethodInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function assert;
use function is_string;
use function max;
use function min;
use function route;

/**
 * Class DescendancyChartModule
 */
class DescendancyChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL  = '/tree/{tree}/descendants-{style}-{generations}/{xref}';

    // Chart styles
    public const CHART_STYLE_TREE        = 'tree';
    public const CHART_STYLE_INDIVIDUALS = 'individuals';
    public const CHART_STYLE_FAMILIES    = 'families';

    // Defaults
    public const    DEFAULT_STYLE       = self::CHART_STYLE_TREE;
    public const    DEFAULT_GENERATIONS = '3';
    protected const DEFAULT_PARAMETERS  = [
        'generations' => self::DEFAULT_GENERATIONS,
        'style'       => self::DEFAULT_STYLE,
    ];

    // Limits
    protected const MINIMUM_GENERATIONS = 2;
    protected const MAXIMUM_GENERATIONS = 10;

    /** @var ChartService */
    private $chart_service;

    /**
     * DescendancyChartModule constructor.
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
     * @return void
     */
    public function boot(): void
    {
        $router_container = app(RouterContainer::class);
        assert($router_container instanceof RouterContainer);

        $router_container->getMap()
            ->get(static::class, static::ROUTE_URL, $this)
            ->allows(RequestMethodInterface::METHOD_POST)
            ->tokens([
                'generations' => '\d+',
                'style'       => implode('|', array_keys($this->styles())),
            ]);
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Descendants');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “DescendancyChart” module */
        return I18N::translate('A chart of an individual’s descendants.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-descendants';
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
        return I18N::translate('Descendants of %s', $individual->fullName());
    }

    /**
     * The URL for a page showing chart options.
     *
     * @param Individual $individual
     * @param mixed[]    $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(static::class, [
                'tree' => $individual->tree()->name(),
                'xref' => $individual->xref(),
            ] + $parameters + self::DEFAULT_PARAMETERS);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, false, true);

        $user        = $request->getAttribute('user');
        $style       = $request->getAttribute('style');
        $generations = (int) $request->getAttribute('generations');
        $ajax        = $request->getQueryParams()['ajax'] ?? '';

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $params = (array) $request->getParsedBody();

            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'xref'        => $params['xref'],
                'style'       => $params['style'],
                'generations' => $params['generations'],
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        if ($ajax === '1') {
            $this->layout = 'layouts/ajax';

            switch ($style) {
                case self::CHART_STYLE_TREE:
                    return $this->viewResponse('modules/descendancy_chart/tree', [
                        'individual'  => $individual,
                        'generations' => $generations,
                        'daboville'   => '1',
                    ]);

                case self::CHART_STYLE_INDIVIDUALS:
                    return $this->viewResponse('lists/individuals-table', [
                        'individuals' => $this->chart_service->descendants($individual, $generations - 1),
                        'sosa'        => false,
                        'tree'        => $tree,
                    ]);

                case self::CHART_STYLE_FAMILIES:
                    $families = $this->chart_service->descendantFamilies($individual, $generations - 1);

                    return $this->viewResponse('lists/families-table', ['families' => $families, 'tree' => $tree]);
            }
        }

        $ajax_url = $this->chartUrl($individual, [
            'generations' => $generations,
            'style'       => $style,
            'ajax'        => true,
        ]);

        return $this->viewResponse('modules/descendancy_chart/page', [
            'ajax_url'            => $ajax_url,
            'style'               => $style,
            'styles'              => $this->styles(),
            'default_generations' => self::DEFAULT_GENERATIONS,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module'              => $this->name(),
            'title'               => $this->chartTitle($individual),
            'tree'                => $tree,
        ]);
    }

    /**
     * This chart can display its output in a number of styles
     *
     * @return string[]
     */
    protected function styles(): array
    {
        return [
            self::CHART_STYLE_TREE        => I18N::translate('Tree'),
            self::CHART_STYLE_INDIVIDUALS => I18N::translate('Individuals'),
            self::CHART_STYLE_FAMILIES    => I18N::translate('Families'),
        ];
    }
}
