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
use Fisharebest\Webtrees\Date\GregorianDate;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Factory;
use Fisharebest\Webtrees\GedcomRecord;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function assert;
use function redirect;
use function route;

/**
 * Class TimelineChartModule
 */
class TimelineChartModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL  = '/tree/{tree}/timeline-{scale}';

    // Defaults
    protected const DEFAULT_SCALE      = 10;
    protected const DEFAULT_PARAMETERS = [
        'scale' => self::DEFAULT_SCALE,
    ];

    // Limits
    protected const MINIMUM_SCALE = 1;
    protected const MAXIMUM_SCALE = 200;

    // GEDCOM events that may have DATE data, but should not be displayed
    protected const NON_FACTS = [
        'BAPL',
        'ENDL',
        'SLGC',
        'SLGS',
        '_TODO',
        'CHAN',
    ];
    protected const BHEIGHT   = 30;

    // Box height

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
        return I18N::translate('Timeline');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “TimelineChart” module */
        return I18N::translate('A timeline displaying individual events.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-timeline';
    }

    /**
     * The URL for this chart.
     *
     * @param Individual $individual
     * @param mixed[]    $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route(static::class, [
                'tree'  => $individual->tree()->name(),
                'xrefs' => [$individual->xref()],
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

        $user  = $request->getAttribute('user');
        $scale = (int) $request->getAttribute('scale');
        $xrefs = $request->getQueryParams()['xrefs'] ?? [];
        $ajax  = $request->getQueryParams()['ajax'] ?? '';


        $params = (array) $request->getParsedBody();

        $add  = $params['add'] ?? '';

        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        $scale = min($scale, self::MAXIMUM_SCALE);
        $scale = max($scale, self::MINIMUM_SCALE);

        $xrefs[] = $add;
        $xrefs = array_filter(array_unique($xrefs));

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'scale' => $scale,
                'tree'  => $tree->name(),
                'xrefs' => $xrefs,
            ]));
        }

        // Find the requested individuals.
        $individuals = (new Collection($xrefs))
            ->uniqueStrict()
            ->map(static function (string $xref) use ($tree): ?Individual {
                return Factory::individual()->make($xref, $tree);
            })
            ->filter()
            ->filter(GedcomRecord::accessFilter());

        // Generate URLs omitting each xref.
        $remove_urls = [];

        foreach ($individuals as $exclude) {
            $xrefs_1 = $individuals
                ->filter(static function (Individual $individual) use ($exclude): bool {
                    return $individual->xref() !== $exclude->xref();
                })
                ->map(static function (Individual $individual): string {
                    return $individual->xref();
                });

            $remove_urls[$exclude->xref()] = route(static::class, [
                'tree'  => $tree->name(),
                'scale' => $scale,
                'xrefs' => $xrefs_1->all(),
            ]);
        }

        $individuals = array_map(static function (string $xref) use ($tree): ?Individual {
            return Factory::individual()->make($xref, $tree);
        }, $xrefs);

        $individuals = array_filter($individuals, static function (?Individual $individual): bool {
            return $individual instanceof Individual && $individual->canShow();
        });

        Auth::checkComponentAccess($this, 'chart', $tree, $user);

        if ($ajax === '1') {
            $this->layout = 'layouts/ajax';

            return $this->chart($tree, $xrefs, $scale);
        }

        $reset_url = route(static::class, [
            'scale' => self::DEFAULT_SCALE,
            'tree'  => $tree->name(),
        ]);

        $zoom_in_url = route(static::class, [
            'scale' => min(self::MAXIMUM_SCALE, $scale + (int) ($scale * 0.2 + 1)),
            'tree'  => $tree->name(),
            'xrefs' => $xrefs,
        ]);

        $zoom_out_url = route(static::class, [
            'scale' => max(self::MINIMUM_SCALE, $scale - (int) ($scale * 0.2 + 1)),
            'tree'  => $tree->name(),
            'xrefs' => $xrefs,
        ]);

        $ajax_url = route(static::class, [
            'ajax'  => true,
            'scale' => $scale,
            'tree'  => $tree->name(),
            'xrefs' => $xrefs,
        ]);

        return $this->viewResponse('modules/timeline-chart/page', [
            'ajax_url'     => $ajax_url,
            'individuals'  => $individuals,
            'module'       => $this->name(),
            'remove_urls'  => $remove_urls,
            'reset_url'    => $reset_url,
            'scale'        => $scale,
            'title'        => $this->title(),
            'tree'         => $tree,
            'zoom_in_url'  => $zoom_in_url,
            'zoom_out_url' => $zoom_out_url,
        ]);
    }

    /**
     * @param Tree  $tree
     * @param array $xrefs
     * @param int   $scale
     *
     * @return ResponseInterface
     */
    protected function chart(Tree $tree, array $xrefs, int $scale): ResponseInterface
    {
        /** @var Individual[] $individuals */
        $individuals = array_map(static function (string $xref) use ($tree): ?Individual {
            return Factory::individual()->make($xref, $tree);
        }, $xrefs);

        $individuals = array_filter($individuals, static function (?Individual $individual): bool {
            return $individual instanceof Individual && $individual->canShow();
        });

        $baseyear    = (int) date('Y');
        $topyear     = 0;
        $indifacts   = new Collection();
        $birthyears  = [];
        $birthmonths = [];
        $birthdays   = [];

        foreach ($individuals as $individual) {
            $bdate = $individual->getBirthDate();
            if ($bdate->isOK()) {
                $date = new GregorianDate($bdate->minimumJulianDay());

                $birthyears [$individual->xref()] = $date->year;
                $birthmonths[$individual->xref()] = max(1, $date->month);
                $birthdays  [$individual->xref()] = max(1, $date->day);
            }
            // find all the fact information
            $facts = $individual->facts();
            foreach ($individual->spouseFamilies() as $family) {
                foreach ($family->facts() as $fact) {
                    $facts->push($fact);
                }
            }
            foreach ($facts as $event) {
                // get the fact type
                $fact = $event->getTag();
                if (!in_array($fact, self::NON_FACTS, true)) {
                    // check for a date
                    $date = $event->date();
                    if ($date->isOK()) {
                        $date     = new GregorianDate($date->minimumJulianDay());
                        $baseyear = min($baseyear, $date->year);
                        $topyear  = max($topyear, $date->year);

                        if (!$individual->isDead()) {
                            $topyear = max($topyear, (int) date('Y'));
                        }

                        $indifacts->push($event);
                    }
                }
            }
        }

        // do not add the same fact twice (prevents marriages from being added multiple times)
        $indifacts = $indifacts->uniqueStrict(static function (Fact $fact): string {
            return $fact->id();
        });

        if ($scale === 0) {
            $scale = (int) (($topyear - $baseyear) / 20 * $indifacts->count() / 4);
            if ($scale < 6) {
                $scale = 6;
            }
        }
        if ($scale < 2) {
            $scale = 2;
        }
        $baseyear -= 5;
        $topyear  += 5;

        $indifacts = Fact::sortFacts($indifacts);

        $html = view('modules/timeline-chart/chart', [
            'baseyear'    => $baseyear,
            'bheight'     => self::BHEIGHT,
            'birthdays'   => $birthdays,
            'birthmonths' => $birthmonths,
            'birthyears'  => $birthyears,
            'indifacts'   => $indifacts,
            'individuals' => $individuals,
            'placements'  => [],
            'scale'       => $scale,
            'topyear'     => $topyear,
        ]);

        return response($html);
    }
}
