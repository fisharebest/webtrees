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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function app;
use function response;
use function view;

/**
 * Class HourglassChartModule
 */
class HourglassChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Defaults
    private const DEFAULT_GENERATIONS         = '3';
    private const DEFAULT_MAXIMUM_GENERATIONS = '9';

    // Limits
    private const MAXIMUM_GENERATIONS = 10;
    private const MINIMUM_GENERATIONS = 2;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Hourglass chart');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “HourglassChart” module */
        return I18N::translate('An hourglass chart of an individual’s ancestors and descendants.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-hourglass';
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
     * A form to request the chart parameters.
     *
     * @param ServerRequestInterface $request
     * @param Tree                   $tree
     * @param UserInterface          $user
     *
     * @return ResponseInterface
     */
    public function getChartAction(ServerRequestInterface $request, Tree $tree, UserInterface $user): ResponseInterface
    {
        $ajax       = (bool) $request->get('ajax');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $generations = (int) $request->get('generations', self::DEFAULT_GENERATIONS);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        $show_spouse = (bool) $request->get('show_spouse');

        if ($ajax) {
            return $this->chart($individual, $generations, $show_spouse);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'generations' => $generations,
            'show_spouse' => $show_spouse,
        ]);

        return $this->viewResponse('modules/hourglass-chart/page', [
            'ajax_url'            => $ajax_url,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module_name'         => $this->name(),
            'show_spouse'         => $show_spouse,
            'title'               => $this->chartTitle($individual),
        ]);
    }

    /**
     * Generate the initial generations of the chart
     *
     * @param Individual $individual
     * @param int        $generations
     * @param bool       $show_spouse
     *
     * @return ResponseInterface
     */
    protected function chart(Individual $individual, int $generations, bool $show_spouse): ResponseInterface
    {
        $this->layout = 'layouts/ajax';

        return $this->viewResponse('modules/hourglass-chart/chart', [
            'generations' => $generations,
            'individual'  => $individual,
            'show_spouse' => $show_spouse,
        ]);
    }

    /**
     * Generate an extension to the chart
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getAncestorsAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = app(Tree::class);
        $xref = $request->getQueryParams()['xref'] ?? '';

        $family = Family::getInstance($xref, $tree);
        Auth::checkFamilyAccess($family);

        return response(view('modules/hourglass-chart/parents', [
            'family'      => $family,
            'generations' => 1,
        ]));
    }

    /**
     * Generate an extension to the chart
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function getDescendantsAction(ServerRequestInterface $request): ResponseInterface
    {
        $tree = app(Tree::class);
        $xref = $request->getQueryParams()['xref'] ?? '';

        $show_spouse = (bool) ($request->getQueryParams()['show_spouse'] ?? false);
        $individual  = Individual::getInstance($xref, $tree);
        Auth::checkIndividualAccess($individual);
        $children = $individual->spouseFamilies()->map(static function (Family $family): Collection { return $family->children(); })->flatten();

        return response(view('modules/hourglass-chart/children', [
            'children'    => $children,
            'generations' => 1,
            'show_spouse' => $show_spouse,
        ]));
    }
}
