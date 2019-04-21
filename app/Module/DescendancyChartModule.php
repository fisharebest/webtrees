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
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsPrint;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\GedcomTag;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class DescendancyChartModule
 */
class DescendancyChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Chart styles
    public const CHART_STYLE_TREE        = 0;
    public const CHART_STYLE_BOOKLET     = 1;
    public const CHART_STYLE_INDIVIDUALS = 2;
    public const CHART_STYLE_FAMILIES    = 3;

    // Defaults
    public const DEFAULT_STYLE               = self::CHART_STYLE_TREE;
    public const DEFAULT_GENERATIONS         = '3';

    // Limits
    public const MINIMUM_GENERATIONS = 2;
    public const MAXIMUM_GENERATIONS = 10;

    /** @var int[] */
    protected $dabo_num = [];

    /** @var string[] */
    protected $dabo_sex = [];

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
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     * @param ChartService           $chart_service
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request, Tree $tree, UserInterface $user, ChartService $chart_service): ResponseInterface
    {
        $ajax       = (bool) ($request->getQueryParams()['ajax'] ?? false);
        $xref       = $request->getQueryParams()['xref'] ?? '';
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $chart_style = (int) ($request->getQueryParams()['chart_style'] ?? self::DEFAULT_STYLE);
        $generations = (int) ($request->getQueryParams()['generations'] ?? self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        if ($ajax) {
            return $this->chart($request, $tree, $chart_service);
        }

        $ajax_url = $this->chartUrl($individual, [
            'chart_style' => $chart_style,
            'generations' => $generations,
            'ajax'        => true,
        ]);

        return $this->viewResponse('modules/descendancy_chart/page', [
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
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param ChartService           $chart_service
     *
     * @return ResponseInterface
     */
    public function chart(ServerRequestInterface $request, Tree $tree, ChartService $chart_service): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        $xref       = $request->getQueryParams()['xref'];
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        $chart_style = (int) $request->getQueryParams()['chart_style'];
        $generations = (int) $request->getQueryParams()['generations'];

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        switch ($chart_style) {
            case self::CHART_STYLE_TREE:
            default:
                return response(view('modules/descendancy_chart/tree', ['individual' => $individual, 'generations' => $generations, 'daboville' => '1']));

            case self::CHART_STYLE_BOOKLET:
                return $this->descendantsBooklet($individual, $generations);

            case self::CHART_STYLE_INDIVIDUALS:
                $individuals = $chart_service->descendants($individual, $generations - 1);

                return $this->descendantsIndividuals($tree, $individuals);

            case self::CHART_STYLE_FAMILIES:
                $families = $chart_service->descendantFamilies($individual, $generations - 1);

                return $this->descendantsFamilies($tree, $families);
        }
    }

    /**
     * Show a tabular list of individual descendants.
     *
     * @param Tree       $tree
     * @param Collection $individuals
     *
     * @return ResponseInterface
     */
    private function descendantsIndividuals(Tree $tree, Collection $individuals): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('lists/individuals-table', [
            'individuals' => $individuals,
            'sosa'        => false,
            'tree'        => $tree,
        ]);
    }

    /**
     * Show a tabular list of individual descendants.
     *
     * @param Tree       $tree
     * @param Collection $families
     *
     * @return ResponseInterface
     */
    private function descendantsFamilies(Tree $tree, Collection $families): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('lists/families-table', [
            'families' => $families,
            'tree'     => $tree,
        ]);
    }

    /**
     * Show a booklet view of descendants
     *
     * @TODO replace ob_start() with views.
     *
     * @param Individual $individual
     * @param int        $generations
     *
     * @return ResponseInterface
     */
    private function descendantsBooklet(Individual $individual, int $generations): ResponseInterface
    {
        ob_start();

        $this->printChildFamily($individual, $generations);

        $html = ob_get_clean();

        return response($html);
    }

    /**
     * Print a child family
     *
     * @param Individual $individual
     * @param int        $depth     - the descendancy depth to show
     * @param string     $daboville - d'Aboville number
     * @param string     $gpid
     *
     * @return void
     */
    private function printChildFamily(Individual $individual, $depth, $daboville = '1.', $gpid = ''): void
    {
        if ($depth < 2) {
            return;
        }

        $i = 1;

        foreach ($individual->spouseFamilies() as $family) {
            FunctionsCharts::printSosaFamily($family, '', -1, $daboville, $individual->xref(), $gpid, false);
            foreach ($family->children() as $child) {
                $this->printChildFamily($child, $depth - 1, $daboville . ($i++) . '.', $individual->xref());
            }
        }
    }

    /**
     * This chart can display its output in a number of styles
     *
     * @return string[]
     */
    private function chartStyles(): array
    {
        return [
            self::CHART_STYLE_TREE        => I18N::translate('Tree'),
            self::CHART_STYLE_BOOKLET     => I18N::translate('Booklet'),
            self::CHART_STYLE_INDIVIDUALS => I18N::translate('Individuals'),
            self::CHART_STYLE_FAMILIES    => I18N::translate('Families'),
        ];
    }
}
