<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
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
use function view;

/**
 * Class PedigreeChartModule
 */
class PedigreeChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL = '/tree/{tree}/pedigree-{style}-{generations}/{xref}';

    // Chart styles
    public const STYLE_LEFT  = 'left';
    public const STYLE_RIGHT = 'right';
    public const STYLE_UP    = 'up';
    public const STYLE_DOWN  = 'down';

    // Defaults
    public const    DEFAULT_GENERATIONS = '4';
    public const    DEFAULT_STYLE       = self::STYLE_RIGHT;
    protected const DEFAULT_PARAMETERS  = [
        'generations' => self::DEFAULT_GENERATIONS,
        'style'       => self::DEFAULT_STYLE,
    ];

    // Limits
    protected const MINIMUM_GENERATIONS = 2;
    protected const MAXIMUM_GENERATIONS = 12;

    // For RTL languages
    protected const MIRROR_STYLE = [
        self::STYLE_UP    => self::STYLE_DOWN,
        self::STYLE_DOWN  => self::STYLE_UP,
        self::STYLE_LEFT  => self::STYLE_RIGHT,
        self::STYLE_RIGHT => self::STYLE_LEFT,
    ];

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
            ] + $parameters + static::DEFAULT_PARAMETERS);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $user        = Validator::attributes($request)->user();
        $xref        = Validator::attributes($request)->isXref()->string('xref');
        $style       = Validator::attributes($request)->isInArrayKeys($this->styles('ltr'))->string('style');
        $generations = Validator::attributes($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations');
        $ajax        = Validator::queryParams($request)->boolean('ajax', false);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(self::class, [
                'tree'        => $tree->name(),
                'xref'        => Validator::parsedBody($request)->isXref()->string('xref'),
                'style'       => Validator::parsedBody($request)->isInArrayKeys($this->styles('ltr'))->string('style'),
                'generations' => Validator::parsedBody($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations'),
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);

        if ($ajax) {
            $this->layout = 'layouts/ajax';

            $ancestors = $this->chart_service->sosaStradonitzAncestors($individual, $generations);

            // Father’s ancestors link to the father’s pedigree
            // Mother’s ancestors link to the mother’s pedigree..
            $links = $ancestors->map(function (?Individual $individual, $sosa) use ($ancestors, $style, $generations): string {
                if ($individual instanceof Individual && $sosa >= 2 ** $generations / 2 && $individual->childFamilies()->isNotEmpty()) {
                    // The last row/column, and there are more generations.
                    if ($sosa >= 2 ** $generations * 3 / 4) {
                        return $this->nextLink($ancestors->get(3), $style, $generations);
                    }

                    return $this->nextLink($ancestors->get(2), $style, $generations);
                }

                // A spacer to fix the "Left" layout.
                return '<span class="invisible px-2">' . view('icons/arrow-' . $style) . '</span>';
            });

            // Root individual links to their children.
            $links->put(1, $this->previousLink($individual, $style, $generations));

            return $this->viewResponse('modules/pedigree-chart/chart', [
                'ancestors'   => $ancestors,
                'generations' => $generations,
                'style'       => $style,
                'layout'      => 'right',
                'links'       => $links,
                'spacer'      => $this->spacer(),
            ]);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'generations' => $generations,
            'style'       => $style,
            'xref'        => $xref,
        ]);

        return $this->viewResponse('modules/pedigree-chart/page', [
            'ajax_url'           => $ajax_url,
            'generations'        => $generations,
            'individual'         => $individual,
            'module'             => $this->name(),
            'max_generations'    => self::MAXIMUM_GENERATIONS,
            'min_generations'    => self::MINIMUM_GENERATIONS,
            'style'              => $style,
            'styles'             => $this->styles(I18N::direction()),
            'title'              => $this->chartTitle($individual),
            'tree'               => $tree,
        ]);
    }

    /**
     * A link-sized spacer, to maintain the chart layout
     *
     * @return string
     */
    public function spacer(): string
    {
        return '<span class="px-2">' . view('icons/spacer') . '</span>';
    }

    /**
     * Build a menu for the chart root individual
     *
     * @param Individual $individual
     * @param string     $style
     * @param int        $generations
     *
     * @return string
     */
    public function nextLink(Individual $individual, string $style, int $generations): string
    {
        $icon  = view('icons/arrow-' . $style);
        $title = $this->chartTitle($individual);
        $url   = $this->chartUrl($individual, [
            'style'       => $style,
            'generations' => $generations,
        ]);

        return '<a class="px-2" href="' . e($url) . '" title="' . strip_tags($title) . '">' . $icon . '<span class="visually-hidden">' . $title . '</span></a>';
    }

    /**
     * Build a menu for the chart root individual
     *
     * @param Individual $individual
     * @param string     $style
     * @param int        $generations
     *
     * @return string
     */
    public function previousLink(Individual $individual, string $style, int $generations): string
    {
        $icon = view('icons/arrow-' . self::MIRROR_STYLE[$style]);

        $siblings = [];
        $spouses  = [];
        $children = [];

        foreach ($individual->childFamilies() as $family) {
            foreach ($family->children() as $child) {
                if ($child !== $individual) {
                    $siblings[] = $this->individualLink($child, $style, $generations);
                }
            }
        }

        foreach ($individual->spouseFamilies() as $family) {
            foreach ($family->spouses() as $spouse) {
                if ($spouse !== $individual) {
                    $spouses[] = $this->individualLink($spouse, $style, $generations);
                }
            }

            foreach ($family->children() as $child) {
                $children[] = $this->individualLink($child, $style, $generations);
            }
        }

        return view('modules/pedigree-chart/previous', [
            'icon'        => $icon,
            'individual'  => $individual,
            'generations' => $generations,
            'style'       => $style,
            'chart'       => $this,
            'siblings'    => $siblings,
            'spouses'     => $spouses,
            'children'    => $children,
        ]);
    }

    /**
     * @param Individual $individual
     * @param string     $style
     * @param int        $generations
     *
     * @return string
     */
    protected function individualLink(Individual $individual, string $style, int $generations): string
    {
        $text  = $individual->fullName();
        $title = $this->chartTitle($individual);
        $url   = $this->chartUrl($individual, [
            'style'       => $style,
            'generations' => $generations,
        ]);

        return '<a class="dropdown-item" href="' . e($url) . '" title="' . strip_tags($title) . '">' . $text . '</a>';
    }

    /**
     * This chart can display its output in a number of styles
     *
     * @param string $direction
     *
     * @return array<string>
     */
    protected function styles(string $direction): array
    {
        // On right-to-left pages, the CSS will mirror the chart, so we need to mirror the label.
        if ($direction === 'rtl') {
            return [
                self::STYLE_RIGHT => view('icons/pedigree-left') . I18N::translate('left'),
                self::STYLE_LEFT  => view('icons/pedigree-right') . I18N::translate('right'),
                self::STYLE_UP    => view('icons/pedigree-up') . I18N::translate('up'),
                self::STYLE_DOWN  => view('icons/pedigree-down') . I18N::translate('down'),
            ];
        }

        return [
            self::STYLE_LEFT  => view('icons/pedigree-left') . I18N::translate('left'),
            self::STYLE_RIGHT => view('icons/pedigree-right') . I18N::translate('right'),
            self::STYLE_UP    => view('icons/pedigree-up') . I18N::translate('up'),
            self::STYLE_DOWN  => view('icons/pedigree-down') . I18N::translate('down'),
        ];
    }
}
