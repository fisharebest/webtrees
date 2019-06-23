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
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function view;

/**
 * Class FamilyBookChartModule
 */
class FamilyBookChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Defaults
    private const DEFAULT_GENERATIONS            = '2';
    private const DEFAULT_DESCENDANT_GENERATIONS = '5';
    private const DEFAULT_MAXIMUM_GENERATIONS    = '9';

    // Limits
    public const MINIMUM_GENERATIONS = 2;
    public const MAXIMUM_GENERATIONS = 10;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Family book');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “FamilyBookChart” module */
        return I18N::translate('A chart of an individual’s ancestors and descendants, as a family book.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-familybook';
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
        return I18N::translate('Family book of %s', $individual->fullName());
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
        $ajax       = $request->getQueryParams()['ajax'] ?? '';
        $xref       = $request->getQueryParams()['xref'] ?? '';
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $show_spouse = (bool) ($request->getQueryParams()['show_spouse'] ?? false);
        $generations = (int) ($request->getQueryParams()['generations'] ?? self::DEFAULT_GENERATIONS);
        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        // Generations of ancestors/descendants in each mini-tree.
        $book_size = (int) ($request->getQueryParams()['book_size'] ?? 2);
        $book_size = min($book_size, 5);
        $book_size = max($book_size, 2);

        if ($ajax === '1') {
            return $this->chart($individual, $generations, $book_size, $show_spouse);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'book_size'   => $book_size,
            'generations' => $generations,
            'show_spouse' => $show_spouse,
        ]);

        return $this->viewResponse('modules/family-book-chart/page', [
            'ajax_url'            => $ajax_url,
            'book_size'           => $book_size,
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
     * @param Individual $individual
     * @param int        $generations
     * @param int        $book_size
     * @param bool       $show_spouse
     *
     * @return ResponseInterface
     */
    public function chart(Individual $individual, int $generations, int $book_size, bool $show_spouse): ResponseInterface
    {
        $html = view('modules/family-book-chart/chart', ['individual' => $individual, 'generations' => $generations, 'book_size' => $book_size, 'show_spouse' => $show_spouse]);

        return response($html);
    }
}
