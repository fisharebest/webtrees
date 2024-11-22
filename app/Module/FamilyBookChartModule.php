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
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function route;

/**
 * Class FamilyBookChartModule
 */
class FamilyBookChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const string ROUTE_URL = '/tree/{tree}/family-book-{book_size}-{generations}-{spouses}/{xref}';

    // Defaults
    public const    string DEFAULT_GENERATIONS            = '2';
    public const    string DEFAULT_DESCENDANT_GENERATIONS = '5';
    protected const array  DEFAULT_PARAMETERS             = [
        'book_size'   => self::DEFAULT_GENERATIONS,
        'generations' => self::DEFAULT_DESCENDANT_GENERATIONS,
        'spouses'     => false,
    ];

    // Limits
    protected const int MINIMUM_BOOK_SIZE = 2;
    protected const int MAXIMUM_BOOK_SIZE = 5;

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
        return I18N::translate('Family book');
    }

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
        return I18N::translate('Family book of %s', $individual->fullName());
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
        $user        = Validator::attributes($request)->user();
        $xref        = Validator::attributes($request)->isXref()->string('xref');
        $book_size   = Validator::attributes($request)->isBetween(self::MINIMUM_BOOK_SIZE, self::MAXIMUM_BOOK_SIZE)->integer('book_size');
        $generations = Validator::attributes($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations');
        $spouses     = Validator::attributes($request)->boolean('spouses', false);
        $ajax        = Validator::queryParams($request)->boolean('ajax', false);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'xref'        => Validator::parsedBody($request)->isXref()->string('xref'),
                'book_size'   => Validator::parsedBody($request)->isBetween(self::MINIMUM_BOOK_SIZE, self::MAXIMUM_BOOK_SIZE)->integer('book_size'),
                'generations' => Validator::parsedBody($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations'),
                'spouses'     => Validator::parsedBody($request)->boolean('spouses', false),
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);

        if ($ajax) {
            $this->layout = 'layouts/ajax';

            return $this->viewResponse('modules/family-book-chart/chart', [
                'individual'  => $individual,
                'generations' => $generations,
                'book_size'   => $book_size,
                'spouses'     => $spouses,
            ]);
        }

        $ajax_url = $this->chartUrl($individual, [
            'ajax'        => true,
            'book_size'   => $book_size,
            'generations' => $generations,
            'spouses'     => $spouses,
        ]);

        return $this->viewResponse('modules/family-book-chart/page', [
            'ajax_url'            => $ajax_url,
            'book_size'           => $book_size,
            'generations'         => $generations,
            'individual'          => $individual,
            'maximum_book_size'   => self::MAXIMUM_BOOK_SIZE,
            'minimum_book_size'   => self::MINIMUM_BOOK_SIZE,
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module'              => $this->name(),
            'spouses'             => $spouses,
            'title'               => $this->chartTitle($individual),
            'tree'                => $tree,
        ]);
    }
}
