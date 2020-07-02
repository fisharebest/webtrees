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
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
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
use function view;

/**
 * Class PedigreeChartModule
 */
class PedigreeChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL  = '/tree/{tree}/pedigree-{style}-{generations}/{xref}';

    // Chart styles
    public const STYLE_LEFT  = 'left';
    public const STYLE_RIGHT = 'right';
    public const STYLE_UP    = 'up';
    public const STYLE_DOWN  = 'down';

    // Defaults
    protected const DEFAULT_GENERATIONS = '4';
    protected const DEFAULT_STYLE       = self::STYLE_RIGHT;
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

    /** @var ChartService */
    private $chart_service;

    /**
     * PedigreeChartModule constructor.
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
     * @param Individual $individual
     * @param mixed[]    $parameters
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $individual = Factory::individual()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, false, true);

        $ajax        = $request->getQueryParams()['ajax'] ?? '';
        $generations = (int) $request->getAttribute('generations');
        $style       = $request->getAttribute('style');
        $user        = $request->getAttribute('user');

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $params = (array) $request->getParsedBody();

            return redirect(route(self::class, [
                'tree'        => $request->getAttribute('tree')->name(),
                'xref'        => $params['xref'],
                'style'       => $params['style'],
                'generations' => $params['generations'],
            ]));
        }

        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        if ($ajax === '1') {
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
            'styles'             => $this->styles(),
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

        return '<a class="px-2" href="' . e($url) . '" title="' . strip_tags($title) . '">' . $icon . '<span class="sr-only">' . $title . '</span></a>';
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
     * @return string[]
     */
    protected function styles(): array
    {
        return [
            self::STYLE_LEFT  => I18N::translate('Left'),
            self::STYLE_RIGHT => I18N::translate('Right'),
            self::STYLE_UP    => I18N::translate('Up'),
            self::STYLE_DOWN  => I18N::translate('Down'),
        ];
    }
}
