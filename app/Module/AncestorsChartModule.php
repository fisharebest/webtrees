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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function view;

/**
 * Class AncestorsChartModule
 */
class AncestorsChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Chart styles
    protected const CHART_STYLE_TREE        = 'tree';
    protected const CHART_STYLE_INDIVIDUALS = 'individuals';
    protected const CHART_STYLE_FAMILIES    = 'families';

    // Defaults
    protected const DEFAULT_STYLE               = self::CHART_STYLE_TREE;
    protected const DEFAULT_GENERATIONS         = '4';

    // Limits
    protected const MINIMUM_GENERATIONS = 2;
    protected const MAXIMUM_GENERATIONS = 10;

    /** @var ChartService */
    private $chart_service;

    /**
     * CompactTreeChartModule constructor.
     *
     * @param ChartService $chart_service
     */
    public function __construct(ChartService $chart_service) {
        $this->chart_service = $chart_service;
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Ancestors');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “AncestorsChart” module */
        return I18N::translate('A chart of an individual’s ancestors.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-ancestry';
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
        return I18N::translate('Ancestors of %s', $individual->fullName());
    }

    /**
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree       = $request->getAttribute('tree');
        $user       = $request->getAttribute('user');
        $ajax       = $request->getQueryParams()['ajax'] ?? '';
        $xref       = $request->getQueryParams()['xref'] ?? '';
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $chart_style  = $request->getQueryParams()['chart_style'] ?? self::DEFAULT_STYLE;
        $generations  = (int) ($request->getQueryParams()['generations'] ?? self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        if ($ajax === '1') {
            $ancestors = $this->chart_service->sosaStradonitzAncestors($individual, $generations);

            switch ($chart_style) {
                default:
                case self::CHART_STYLE_TREE:
                    return response(view('modules/ancestors-chart/tree', ['individual' => $individual, 'parents' => $individual->primaryChildFamily(), 'generations' => $generations, 'sosa' => 1]));

                case self::CHART_STYLE_INDIVIDUALS:
                    return $this->ancestorsIndividuals($tree, $ancestors);

                case self::CHART_STYLE_FAMILIES:
                    return $this->ancestorsFamilies($tree, $ancestors);
            }
        }

        $ajax_url = $this->chartUrl($individual, [
            'generations'  => $generations,
            'chart_style'  => $chart_style,
            'ajax'         => true,
        ]);

        return $this->viewResponse('modules/ancestors-chart/page', [
            'ajax_url'            => $ajax_url,
            'chart_style'         => $chart_style,
            'chart_styles'        => $this->chartStyles(),
            'default_generations' => self::DEFAULT_GENERATIONS,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module_name'         => $this->name(),
            'title'               => $this->chartTitle($individual),
        ]);
    }

    /**
     * Show a tabular list of individual ancestors.
     *
     * @param Tree       $tree
     * @param Collection $ancestors
     *
     * @return ResponseInterface
     */
    protected function ancestorsIndividuals(Tree $tree, Collection $ancestors): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('lists/individuals-table', [
            'individuals' => $ancestors,
            'sosa'        => true,
            'tree'        => $tree,
        ]);
    }

    /**
     * Show a tabular list of individual ancestors.
     *
     * @param Tree       $tree
     * @param Collection $ancestors
     *
     * @return ResponseInterface
     */
    protected function ancestorsFamilies(Tree $tree, Collection $ancestors): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $families = [];
        foreach ($ancestors as $individual) {
            foreach ($individual->childFamilies() as $family) {
                $families[$family->xref()] = $family;
            }
        }

        return $this->viewResponse('lists/families-table', [
            'families' => $families,
            'tree'     => $tree,
        ]);
    }

    /**
     * This chart can display its output in a number of styles
     *
     * @return array
     */
    protected function chartStyles(): array
    {
        return [
            self::CHART_STYLE_TREE        => I18N::translate('Tree'),
            self::CHART_STYLE_INDIVIDUALS => I18N::translate('Individuals'),
            self::CHART_STYLE_FAMILIES    => I18N::translate('Families'),
        ];
    }
}
