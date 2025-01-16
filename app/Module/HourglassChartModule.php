<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2025 webtrees development team
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
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Validator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function response;
use function view;

class HourglassChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const string ROUTE_URL = '/tree/{tree}/hourglass-{generations}-{spouses}/{xref}';

    // Defaults
    public const    string DEFAULT_GENERATIONS = '3';
    public const    bool   DEFAULT_SPOUSES     = false;
    protected const array  DEFAULT_PARAMETERS  = [
        'generations' => self::DEFAULT_GENERATIONS,
        'spouses'     => self::DEFAULT_SPOUSES,
    ];

    // Limits
    protected const int MINIMUM_GENERATIONS = 2;
    protected const int MAXIMUM_GENERATIONS = 10;

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
        return I18N::translate('Hourglass chart');
    }

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
    public function chartBoxMenu(Individual $individual): Menu|null
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
        return I18N::translate('Hourglass chart of %s', $individual->fullName());
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
            ] + $parameters + self::DEFAULT_PARAMETERS);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $tree        = Validator::attributes($request)->tree();
        $xref        = Validator::attributes($request)->isXref()->string('xref');
        $user        = Validator::attributes($request)->user();
        $generations = Validator::attributes($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations');
        $spouses     = Validator::attributes($request)->boolean('spouses', self::DEFAULT_SPOUSES);
        $ajax        = Validator::queryParams($request)->boolean('ajax', false);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'xref'        => Validator::parsedBody($request)->isXref()->string('xref'),
                'generations' => Validator::parsedBody($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations'),
                'spouses'     => Validator::parsedBody($request)->boolean('spouses', self::DEFAULT_SPOUSES),
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);

        if ($ajax) {
            $this->layout = 'layouts/ajax';

            return $this->viewResponse('modules/hourglass-chart/chart', [
                'generations' => $generations,
                'individual'  => $individual,
                'spouses'     => $spouses,
            ]);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'generations' => $generations,
            'spouses'     => $spouses,
        ]);

        return $this->viewResponse('modules/hourglass-chart/page', [
            'ajax_url'            => $ajax_url,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module'              => $this->name(),
            'spouses'             => $spouses,
            'title'               => $this->chartTitle($individual),
            'tree'                => $tree,
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
        $tree   = Validator::attributes($request)->tree();
        $xref   = Validator::queryParams($request)->isXref()->string('xref');
        $family = Registry::familyFactory()->make($xref, $tree);
        $family = Auth::checkFamilyAccess($family);

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
        $tree       = Validator::attributes($request)->tree();
        $xref       = Validator::queryParams($request)->isXref()->string('xref');
        $spouses    = Validator::queryParams($request)->boolean('spouses', false);
        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, false, true);

        $children = $individual->spouseFamilies()->map(static fn (Family $family): Collection => $family->children())->flatten();

        return response(view('modules/hourglass-chart/children', [
            'children'    => $children,
            'generations' => 1,
            'spouses'     => $spouses,
        ]));
    }
}
