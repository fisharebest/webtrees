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
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
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

/**
 * Class FamilyBookChartModule
 */
class FamilyBookChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL  = '/tree/{tree}/family-book-{book_size}-{generations}-{spouses}/{xref}';

    // Defaults
    public const    DEFAULT_GENERATIONS            = '2';
    public const    DEFAULT_DESCENDANT_GENERATIONS = '5';
    public const    DEFAULT_MAXIMUM_GENERATIONS    = '9';
    protected const DEFAULT_PARAMETERS             = [
        'book_size'   => self::DEFAULT_GENERATIONS,
        'generations' => self::DEFAULT_DESCENDANT_GENERATIONS,
        'spouses'     => false,
    ];

    // Limits
    protected const MINIMUM_GENERATIONS = 2;
    protected const MAXIMUM_GENERATIONS = 10;

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
                'book_size'   => '\d+',
                'generations' => '\d+',
                'spouses'     => '1?',
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
            ] + $parameters + self::DEFAULT_PARAMETERS);
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

        $individual = Registry::individualFactory()->make($xref, $tree);
        $individual = Auth::checkIndividualAccess($individual, false, true);

        $user        = $request->getAttribute('user');
        $book_size   = (int) $request->getAttribute('book_size');
        $generations = (int) $request->getAttribute('generations');
        $spouses     = (bool) $request->getAttribute('spouses');
        $ajax        = $request->getQueryParams()['ajax'] ?? '';

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $params = (array) $request->getParsedBody();

            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'xref'        => $params['xref'],
                'book_size'   => $params['book_size'],
                'generations' => $params['generations'],
                'spouses'     => $params['spouses'] ?? false,
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $generations = min($generations, self::MAXIMUM_GENERATIONS);
        $generations = max($generations, self::MINIMUM_GENERATIONS);

        // Generations of ancestors/descendants in each mini-tree.
        $book_size = min($book_size, 5);
        $book_size = max($book_size, 2);

        if ($ajax === '1') {
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
            'maximum_generations' => self::MAXIMUM_GENERATIONS,
            'minimum_generations' => self::MINIMUM_GENERATIONS,
            'module'              => $this->name(),
            'spouses'             => $spouses,
            'title'               => $this->chartTitle($individual),
            'tree'                => $tree,
        ]);
    }
}
