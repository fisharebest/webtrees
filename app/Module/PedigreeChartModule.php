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
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Theme;
use Fisharebest\Webtrees\Tree;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PedigreeChartModule
 */
class PedigreeChartModule extends AbstractModule implements  ModuleChartInterface
{
    use ModuleChartTrait;

    // With more than 8 generations, we run out of pixels on the <canvas>
    protected const MAX_GENERATIONS = 8;
    protected const MIN_GENERATIONS = 2;

    protected const DEFAULT_GENERATIONS = '4';

    /**
     * Chart orientation codes
     * Dont change them! the offset calculations rely on this order
     */
    public const PORTRAIT         = 0;
    public const LANDSCAPE        = 1;
    public const OLDEST_AT_TOP    = 2;
    public const OLDEST_AT_BOTTOM = 3;

    protected const DEFAULT_ORIENTATION = self::LANDSCAPE;

    /** @var int Number of generation to display */
    protected $generations;

    /** @var array data pertaining to each chart node */
    protected $nodes = [];

    /** @var int Number of nodes in the chart */
    protected $treesize;

    /** @var stdClass Determine which arrows to use for each of the chart orientations */
    protected $arrows;

    /** @var Individual */
    protected $root;

    /**
     * Next and previous generation arrow size in pixels.
     */
    protected const ARROW_SIZE = 22;

    /**
     * How should this module be labelled on tabs, menus, etc.?
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
        return I18N::translate('Pedigree tree of %s', $individual->getFullName());
    }

    /**
     * A form to request the chart parameters.
     *
     * @param Request      $request
     * @param Tree         $tree
     * @param ChartService $chart_service
     *
     * @return Response
     */
    public function getChartAction(Request $request, Tree $tree, ChartService $chart_service): Response
    {
        $ajax       = (bool) $request->get('ajax');
        $xref       = $request->get('xref', '');
        $individual = Individual::getInstance($xref, $tree);

        Auth::checkIndividualAccess($individual);

        $orientation = (int) $request->get('orientation', static::DEFAULT_ORIENTATION);
        $generations = (int) $request->get('generations', static::DEFAULT_GENERATIONS);

        $generations = min(static::MAX_GENERATIONS, $generations);
        $generations = max(static::MIN_GENERATIONS, $generations);

        $generation_options = $this->generationOptions();

        if ($ajax) {
            return $this->chart($individual, $generations, $orientation, $chart_service);
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
     * @param int          $generations
     * @param int          $orientation
     * @param ChartService $chart_service
     *
     * @return Response
     */
    public function chart(Individual $individual, int $generations, int $orientation, ChartService $chart_service): Response
    {
        $bxspacing = Theme::theme()->parameter('chart-spacing-x');
        $byspacing = Theme::theme()->parameter('chart-spacing-y');
        $curgen    = 1; // Track which generation the algorithm is currently working on
        $addoffset = [];

        $this->root = $individual;

        $this->treesize = (2 ** $generations) - 1;

        $this->nodes = [];

        $ancestors = $chart_service->sosaStradonitzAncestors($individual, $generations);

        // $ancestors starts array at index 1 we need to start at 0
        for ($i = 0; $i < $this->treesize; ++$i) {
            $this->nodes[$i] = [
                'indi' => $ancestors->get($i + 1),
                'x'    => 0,
                'y'    => 0,
            ];
        }

        // Are there ancestors beyond the bounds of this chart
        $chart_has_ancestors = false;

        // Check earliest generation for any ancestors
        for ($i = (int) ($this->treesize / 2); $i < $this->treesize; $i++) {
            $chart_has_ancestors = $chart_has_ancestors || ($this->nodes[$i]['indi'] && $this->nodes[$i]['indi']->getChildFamilies());
        }

        $this->arrows = new stdClass();
        switch ($orientation) {
            default:
            case static::PORTRAIT:
            case static::LANDSCAPE:
                $this->arrows->prevGen = 'fas fa-arrow-end wt-icon-arrow-end';
                $this->arrows->menu    = 'fas fa-arrow-start wt-icon-arrow-start';
                $addoffset['x']        = $chart_has_ancestors ? static::ARROW_SIZE : 0;
                $addoffset['y']        = 0;
                break;

            case static::OLDEST_AT_TOP:
                $this->arrows->prevGen = 'fas fa-arrow-up wt-icon-arrow-up';
                $this->arrows->menu    = 'fas fa-arrow-down wt-icon-arrow-down';
                $addoffset['x']        = 0;
                $addoffset['y']        = $this->root->getSpouseFamilies() ? static::ARROW_SIZE : 0;
                break;

            case static::OLDEST_AT_BOTTOM:
                $this->arrows->prevGen = 'fas fa-arrow-down wt-icon-arrow-down';
                $this->arrows->menu    = 'fas fa-arrow-up wt-icon-arrow-up';
                $addoffset['x']        = 0;
                $addoffset['y']        = $chart_has_ancestors ? static::ARROW_SIZE : 0;
                break;
        }

        // Create and position the DIV layers for the pedigree tree
        for ($i = ($this->treesize - 1); $i >= 0; $i--) {
            // Check to see if we have moved to the next generation
            if ($i < (int) ($this->treesize / (2 ** $curgen))) {
                $curgen++;
            }

            // Box position in current generation
            $boxpos = $i - (2 ** ($this->generations - $curgen));
            // Offset multiple for current generation
            if ($orientation < static::OLDEST_AT_TOP) {
                $genoffset  = 2 ** ($curgen - $orientation);
                $boxspacing = Theme::theme()->parameter('chart-box-y') + $byspacing;
            } else {
                $genoffset  = 2 ** ($curgen - 1);
                $boxspacing = Theme::theme()->parameter('chart-box-x') + $byspacing;
            }
            // Calculate the yoffset position in the generation put child between parents
            $yoffset = ($boxpos * ($boxspacing * $genoffset)) + (($boxspacing / 2) * $genoffset) + ($boxspacing * $genoffset);

            // Calculate the xoffset
            switch ($orientation) {
                default:
                case static::PORTRAIT:
                    $xoffset = ($this->generations - $curgen) * ((Theme::theme()->parameter('chart-box-x') + $bxspacing) / 1.8);
                    if (!$i && $this->root->getSpouseFamilies()) {
                        $xoffset -= static::ARROW_SIZE;
                    }
                    // Compact the tree
                    if ($curgen < $this->generations) {
                        if ($i % 2 == 0) {
                            $yoffset = $yoffset - (($boxspacing / 2) * ($curgen - 1));
                        } else {
                            $yoffset = $yoffset + (($boxspacing / 2) * ($curgen - 1));
                        }
                        $parent = (int) (($i - 1) / 2);
                        $pgen   = $curgen;
                        while ($parent > 0) {
                            if ($parent % 2 == 0) {
                                $yoffset = $yoffset - (($boxspacing / 2) * $pgen);
                            } else {
                                $yoffset = $yoffset + (($boxspacing / 2) * $pgen);
                            }
                            $pgen++;
                            if ($pgen > 3) {
                                $temp = 0;
                                for ($j = 1; $j < ($pgen - 2); $j++) {
                                    $temp += ((2 ** $j) - 1);
                                }
                                if ($parent % 2 == 0) {
                                    $yoffset = $yoffset - (($boxspacing / 2) * $temp);
                                } else {
                                    $yoffset = $yoffset + (($boxspacing / 2) * $temp);
                                }
                            }
                            $parent = (int) (($parent - 1) / 2);
                        }
                        if ($curgen > 3) {
                            $temp = 0;
                            for ($j = 1; $j < ($curgen - 2); $j++) {
                                $temp += ((2 ** $j) - 1);
                            }
                            if ($i % 2 == 0) {
                                $yoffset = $yoffset - (($boxspacing / 2) * $temp);
                            } else {
                                $yoffset = $yoffset + (($boxspacing / 2) * $temp);
                            }
                        }
                    }
                    $yoffset -= (($boxspacing / 2) * (2 ** ($this->generations - 2)) - ($boxspacing / 2));
                    break;

                case static::LANDSCAPE:
                    $xoffset = ($this->generations - $curgen) * (Theme::theme()->parameter('chart-box-x') + $bxspacing);
                    if ($curgen == 1) {
                        $xoffset += 10;
                    }
                    break;

                case static::OLDEST_AT_TOP:
                    // Swap x & y offsets as chart is rotated
                    $xoffset = $yoffset;
                    $yoffset = $curgen * (Theme::theme()->parameter('chart-box-y') + ($byspacing * 4));
                    break;

                case static::OLDEST_AT_BOTTOM:
                    // Swap x & y offsets as chart is rotated
                    $xoffset = $yoffset;
                    $yoffset = ($this->generations - $curgen) * (Theme::theme()->parameter('chart-box-y') + ($byspacing * 2));
                    if ($i && $this->root->getSpouseFamilies()) {
                        $yoffset += static::ARROW_SIZE;
                    }
                    break;
            }
            $this->nodes[$i]['x'] = (int) $xoffset;
            $this->nodes[$i]['y'] = (int) $yoffset;
        }

        // Find the minimum x & y offsets and deduct that number from
        // each value in the array so that offsets start from zero
        $min_xoffset = min(array_map(function (array $item): int {
            return $item['x'];
        }, $this->nodes));
        $min_yoffset = min(array_map(function (array $item): int {
            return $item['y'];
        }, $this->nodes));

        array_walk($this->nodes, function (&$item) use ($min_xoffset, $min_yoffset) {
            $item['x'] -= $min_xoffset;
            $item['y'] -= $min_yoffset;
        });

        // Calculate chart & canvas dimensions
        $max_xoffset = max(array_map(function ($item) {
            return $item['x'];
        }, $this->nodes));
        $max_yoffset = max(array_map(function ($item) {
            return $item['y'];
        }, $this->nodes));

        $canvas_width   = $max_xoffset + $bxspacing + Theme::theme()->parameter('chart-box-x') + $addoffset['x'];
        $canvas_height  = $max_yoffset + $byspacing + Theme::theme()->parameter('chart-box-y') + $addoffset['y'];
        $posn           = I18N::direction() === 'rtl' ? 'right' : 'left';
        $last_gen_start = (int) floor($this->treesize / 2);
        if ($orientation === static::OLDEST_AT_TOP || $orientation === static::OLDEST_AT_BOTTOM) {
            $flex_direction = ' flex-column';
        } else {
            $flex_direction = '';
        }

        foreach ($this->nodes as $n => $node) {
            if ($n >= $last_gen_start) {
                $this->nodes[$n]['previous_gen'] = $this->gotoPreviousGen($n, $generations, $orientation, $chart_has_ancestors);
            } else {
                $this->nodes[$n]['previous_gen'] = '';
            }
        }

        $html = view('modules/pedigree-chart/chart', [
            'canvas_height'    => $canvas_height,
            'canvas_width'     => $canvas_width,
            'child_menu'       => $this->getMenu($individual, $generations, $orientation),
            'flex_direction'   => $flex_direction,
            'last_gen_start'   => $last_gen_start,
            'orientation'      => $orientation,
            'nodes'            => $this->nodes,
            'landscape'        => static::LANDSCAPE,
            'oldest_at_top'    => static::OLDEST_AT_TOP,
            'oldest_at_bottom' => static::OLDEST_AT_BOTTOM,
            'portrait'         => static::PORTRAIT,
            'posn'             => $posn,
        ]);

        return new Response($html);
    }

    /**
     * Build a menu for the chart root individual
     *
     * @param Individual $root
     * @param int        $generations
     * @param int        $orientation
     *
     * @return string
     */
    public function getMenu(Individual $root, int $generations, int $orientation): string
    {
        $families = $root->getSpouseFamilies();
        $html     = '';
        if (!empty($families)) {
            $html = sprintf('<div id="childarrow"><a href="#" class="menuselect %s"></a><div id="childbox-pedigree">', $this->arrows->menu);

            foreach ($families as $family) {
                $html   .= '<span class="name1">' . I18N::translate('Family') . '</span>';
                $spouse = $family->getSpouse($root);
                if ($spouse) {
                    $html .= '<a class="name1" href="' . e($this->chartUrl($spouse, ['generations' => $generations, 'orientation' => $orientation])) . '">' . $spouse->getFullName() . '</a>';
                }
                $children = $family->getChildren();
                foreach ($children as $sibling) {
                    $html .= '<a class="name1" href="' . e($this->chartUrl($sibling, ['generations' => $generations, 'orientation' => $orientation])) . '">' . $sibling->getFullName() . '</a>';
                }
            }

            foreach ($root->getChildFamilies() as $family) {
                $siblings = array_filter($family->getChildren(), function (Individual $item) use ($root): bool {
                    return $root->xref() !== $item->xref();
                });
                if (!empty($siblings)) {
                    $html .= '<span class="name1">';
                    $html .= count($siblings) > 1 ? I18N::translate('Siblings') : I18N::translate('Sibling');
                    $html .= '</span>';
                    foreach ($siblings as $sibling) {
                        $html .= '<a class="name1" href="' . e($this->chartUrl($sibling, ['generations' => $generations, 'orientation' => $orientation])) . '">' . $sibling->getFullName() . '</a>';
                    }
                }
            }
            $html .= '</div></div>';
        }

        return $html;
    }

    /**
     * Function gotoPreviousGen
     * Create a link to generate a new chart based on the correct parent of the individual with this index
     *
     * @param int  $index
     * @param int  $generations
     * @param int  $orientation
     * @param bool $chart_has_ancestors
     *
     * @return string
     */
    public function gotoPreviousGen(int $index, int $generations, int $orientation, bool $chart_has_ancestors): string
    {
        $html = '';
        if ($chart_has_ancestors) {
            if ($this->nodes[$index]['indi'] && $this->nodes[$index]['indi']->getChildFamilies()) {
                $html         .= '<div class="ancestorarrow">';
                $rootParentId = 1;
                if ($index > (int) ($this->treesize / 2) + (int) ($this->treesize / 4)) {
                    $rootParentId++;
                }
                $html .= '<a class="' . $this->arrows->prevGen . '" href="' . e($this->chartUrl($this->nodes[$rootParentId]['indi'], ['generations' => $generations, 'orientation' => $orientation])) . '"></a>';
                $html .= '</div>';
            } else {
                $html .= '<div class="spacer"></div>';
            }
        }

        return $html;
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
            0 => I18N::translate('Portrait'),
            1 => I18N::translate('Landscape'),
            2 => I18N::translate('Oldest at top'),
            3 => I18N::translate('Oldest at bottom'),
        ];
    }
}
