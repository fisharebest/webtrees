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
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class PedigreeChartModule
 */
class PedigreeChartModule extends AbstractModule implements ModuleChartInterface
{
    use ModuleChartTrait;

    // Defaults
    protected const DEFAULT_GENERATIONS = '4';

    // Limits
    protected const MAX_GENERATIONS     = 12;
    protected const MIN_GENERATIONS     = 2;

    // Chart orientation options.  These are used to generate icons, views, etc.
    public const ORIENTATION_LEFT  = 'left';
    public const ORIENTATION_RIGHT = 'right';
    public const ORIENTATION_UP    = 'up';
    public const ORIENTATION_DOWN  = 'down';

    protected const MIRROR_ORIENTATION = [
        self::ORIENTATION_UP    => self::ORIENTATION_DOWN,
        self::ORIENTATION_DOWN  => self::ORIENTATION_UP,
        self::ORIENTATION_LEFT  => self::ORIENTATION_RIGHT,
        self::ORIENTATION_RIGHT => self::ORIENTATION_LEFT,
    ];

    protected const DEFAULT_ORIENTATION = self::ORIENTATION_RIGHT;

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        /* I18N: Name of a module/chart */
        return I18N::translate('Pedigree');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “PedigreeChart” module */
        return I18N::translate('A chart of an individual’s ancestors, formatted as a tree.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-pedigree';
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
        return I18N::translate('Pedigree tree of %s', $individual->fullName());
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
        $ajax       = (bool) $request->get('ajax');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);
        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $orientation = $request->get('orientation', static::DEFAULT_ORIENTATION);
        $generations = (int) $request->get('generations', static::DEFAULT_GENERATIONS);

        $generations = min(static::MAX_GENERATIONS, $generations);
        $generations = max(static::MIN_GENERATIONS, $generations);

        $generation_options = $this->generationOptions();

        if ($ajax) {
            return $this->chart($individual, $orientation, $generations, $chart_service);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'generations' => $generations,
            'orientation' => $orientation,
        ]);

        return $this->viewResponse('modules/pedigree-chart/page', [
            'ajax_url'           => $ajax_url,
            'generations'        => $generations,
            'generation_options' => $generation_options,
            'individual'         => $individual,
            'module_name'        => $this->name(),
            'orientation'        => $orientation,
            'orientations'       => $this->orientations(),
            'title'              => $this->chartTitle($individual),
        ]);
    }

    /**
     * @param Individual   $individual
     * @param string       $orientation
     * @param int          $generations
     * @param ChartService $chart_service
     *
     * @return ResponseInterface
     */
    public function chart(Individual $individual, string $orientation, int $generations, ChartService $chart_service): ResponseInterface
    {
        $ancestors = $chart_service->sosaStradonitzAncestors($individual, $generations);

        // Father’s ancestors link to the father’s pedigree
        // Mother’s ancestors link to the mother’s pedigree..
        $links = $ancestors->map(function (?Individual $individual, $sosa) use ($ancestors, $orientation, $generations): string {
            if ($individual instanceof Individual && $sosa >= 2 ** $generations / 2 && $individual->childFamilies()->isNotEmpty()) {
                // The last row/column, and there are more generations.
                if ($sosa >= 2 ** $generations * 3 / 4) {
                    return $this->nextLink($ancestors->get(3), $orientation, $generations);
                }

                return $this->nextLink($ancestors->get(2), $orientation, $generations);
            }

            // A spacer to fix the "Left" layout.
            return '<span class="invisible px-2">' . view('icons/arrow-' . $orientation) . '</span>';
        });

        // Root individual links to their children.
        $links->put(1, $this->previousLink($individual, $orientation, $generations));

        $html = view('modules/pedigree-chart/chart', [
            'ancestors'   => $ancestors,
            'generations' => $generations,
            'orientation' => $orientation,
            'layout'      => 'right',
            'links'       => $links,
        ]);

        return response($html);
    }

    /**
     * Build a menu for the chart root individual
     *
     * @param Individual $individual
     * @param string     $orientation
     * @param int        $generations
     *
     * @return string
     */
    public function nextLink(Individual $individual, string $orientation, int $generations): string
    {
        $icon  = view('icons/arrow-' . $orientation);
        $title = $this->chartTitle($individual);
        $url   = $this->chartUrl($individual, [
            'orientation' => $orientation,
            'generations' => $generations,
        ]);

        return '<a class="px-2" href="' . e($url) . '" title="' . strip_tags($title) . '">' . $icon . '<span class="sr-only">' . $title . '</span></a>';
    }

    /**
     * Build a menu for the chart root individual
     *
     * @param Individual $individual
     * @param string     $orientation
     * @param int        $generations
     *
     * @return string
     */
    public function previousLink(Individual $individual, string $orientation, int $generations): string
    {
        $icon = view('icons/arrow-' . self::MIRROR_ORIENTATION[$orientation]);

        $siblings = [];
        $spouses  = [];
        $children = [];

        foreach ($individual->childFamilies() as $family) {
            foreach ($family->children() as $child) {
                if ($child !== $individual) {
                    $siblings[] = $this->individualLink($child, $orientation, $generations);
                }
            }
        }

        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->spouses() as $spouse) {
                if ($spouse !== $individual) {
                    $spouses[] = $this->individualLink($spouse, $orientation, $generations);
                }
            }

            foreach ($family->children() as $child) {
                $children[] = $this->individualLink($child, $orientation, $generations);
            }
        }

        return view('modules/pedigree-chart/previous', [
            'icon'        => $icon,
            'individual'  => $individual,
            'generations' => $generations,
            'orientation' => $orientation,
            'chart'       => $this,
            'siblings'    => $siblings,
            'spouses'     => $spouses,
            'children'    => $children,
        ]);
    }

    /**
     * @param Individual $individual
     * @param string     $orientation
     * @param int        $generations
     *
     * @return string
     */
    protected function individualLink(Individual $individual, string $orientation, int $generations): string
    {
        $text  = $individual->fullName();
        $title = $this->chartTitle($individual);
        $url   = $this->chartUrl($individual, [
            'orientation' => $orientation,
            'generations' => $generations,
        ]);

        return '<a class="dropdown-item" href="' . e($url) . '" title="' . strip_tags($title) . '">' . $text . '</a>';
    }

    /**
     * @return string[]
     */
    protected function generationOptions(): array
    {
        return FunctionsEdit::numericOptions(range(static::MIN_GENERATIONS, static::MAX_GENERATIONS));
    }

    /**
     * @return string[]
     */
    protected function orientations(): array
    {
        return [
            self::ORIENTATION_LEFT  => I18N::translate('Left'),
            self::ORIENTATION_RIGHT => I18N::translate('Right'),
            self::ORIENTATION_UP    => I18N::translate('Up'),
            self::ORIENTATION_DOWN  => I18N::translate('Down'),
        ];
    }
}
