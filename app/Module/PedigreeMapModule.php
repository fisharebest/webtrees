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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Services\LeafletJsService;
use Fisharebest\Webtrees\Services\RelationshipService;
use Fisharebest\Webtrees\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_key_exists;
use function intdiv;
use function redirect;
use function route;
use function ucfirst;
use function view;

/**
 * Class PedigreeMapModule
 */
class PedigreeMapModule extends AbstractModule implements ModuleChartInterface, RequestHandlerInterface
{
    use ModuleChartTrait;

    protected const ROUTE_URL = '/tree/{tree}/pedigree-map-{generations}/{xref}';

    // Defaults
    public const DEFAULT_GENERATIONS = '4';
    public const DEFAULT_PARAMETERS  = [
        'generations' => self::DEFAULT_GENERATIONS,
    ];

    // Limits
    public const MINIMUM_GENERATIONS = 1;
    public const MAXIMUM_GENERATIONS = 10;

    // CSS colors for each generation
    protected const COUNT_CSS_COLORS = 12;

    protected ChartService $chart_service;

    protected LeafletJsService $leaflet_js_service;

    protected RelationshipService $relationship_service;

    /**
     * @param ChartService        $chart_service
     * @param LeafletJsService    $leaflet_js_service
     * @param RelationshipService $relationship_service
     */
    public function __construct(
        ChartService $chart_service,
        LeafletJsService $leaflet_js_service,
        RelationshipService $relationship_service
    ) {
        $this->chart_service      = $chart_service;
        $this->leaflet_js_service = $leaflet_js_service;
        $this->relationship_service = $relationship_service;
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
        /* I18N: Name of a module */
        return I18N::translate('Pedigree map');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        /* I18N: Description of the “Pedigree map” module */
        return I18N::translate('Show the birthplace of ancestors on a map.');
    }

    /**
     * CSS class for the URL.
     *
     * @return string
     */
    public function chartMenuClass(): string
    {
        return 'menu-chart-pedigreemap';
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
        return I18N::translate('Pedigree map of %s', $individual->fullName());
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
                'tree' => $individual->tree()->name(),
                'xref' => $individual->xref(),
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
        $generations = Validator::attributes($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations');
        $xref        = Validator::attributes($request)->isXref()->string('xref');

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'xref'        => Validator::parsedBody($request)->isXref()->string('xref'),
                'generations' => Validator::parsedBody($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations'),
            ]));
        }

        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);

        $map = view('modules/pedigree-map/chart', [
            'data'           => $this->getMapData($request),
            'leaflet_config' => $this->leaflet_js_service->config(),
        ]);

        return $this->viewResponse('modules/pedigree-map/page', [
            'module'         => $this->name(),
            /* I18N: %s is an individual’s name */
            'title'          => I18N::translate('Pedigree map of %s', $individual->fullName()),
            'tree'           => $tree,
            'individual'     => $individual,
            'generations'    => $generations,
            'maxgenerations' => self::MAXIMUM_GENERATIONS,
            'map'            => $map,
        ]);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array<mixed> $geojson
     */
    protected function getMapData(ServerRequestInterface $request): array
    {
        $facts = $this->getPedigreeMapFacts($request, $this->chart_service);

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];

        $sosa_points = [];

        foreach ($facts as $sosa => $fact) {
            $location = new PlaceLocation($fact->place()->gedcomName());

            // Use the co-ordinates from the fact (if they exist).
            $latitude  = $fact->latitude();
            $longitude = $fact->longitude();

            // Use the co-ordinates from the location otherwise.
            if ($latitude === null || $longitude === null) {
                $latitude  = $location->latitude();
                $longitude = $location->longitude();
            }

            if ($latitude !== null && $longitude !== null) {
                $polyline           = null;
                $sosa_points[$sosa] = [$latitude, $longitude];
                $sosa_child         = intdiv($sosa, 2);
                $generation         = (int) log($sosa, 2);
                $color              = 'var(--wt-pedigree-map-gen-' . $generation % self::COUNT_CSS_COLORS . ')';
                $class              = 'wt-pedigree-map-gen-' . $generation % self::COUNT_CSS_COLORS;

                if (array_key_exists($sosa_child, $sosa_points)) {
                    // Would like to use a GeometryCollection to hold LineStrings
                    // rather than generate polylines but the MarkerCluster library
                    // doesn't seem to like them
                    $polyline = [
                        'points'  => [
                            $sosa_points[$sosa_child],
                            [$latitude, $longitude],
                        ],
                        'options' => [
                            'color' => $color,
                        ],
                    ];
                }
                $geojson['features'][] = [
                    'type'       => 'Feature',
                    'id'         => $sosa,
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => [$longitude, $latitude],
                    ],
                    'properties' => [
                        'polyline'  => $polyline,
                        'iconcolor' => $color,
                        'tooltip'   => null,
                        'summary'   => view('modules/pedigree-map/events', [
                            'class'        => $class,
                            'fact'         => $fact,
                            'relationship' => $this->getSosaName($sosa),
                            'sosa'         => $sosa,
                        ]),
                    ],
                ];
            }
        }

        return $geojson;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ChartService           $chart_service
     *
     * @return array<Fact>
     */
    protected function getPedigreeMapFacts(ServerRequestInterface $request, ChartService $chart_service): array
    {
        $tree        = Validator::attributes($request)->tree();
        $generations = Validator::attributes($request)->isBetween(self::MINIMUM_GENERATIONS, self::MAXIMUM_GENERATIONS)->integer('generations');
        $xref        = Validator::attributes($request)->isXref()->string('xref');
        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);
        $ancestors   = $chart_service->sosaStradonitzAncestors($individual, $generations);
        $facts       = [];

        foreach ($ancestors as $sosa => $person) {
            if ($person->canShow()) {
                $birth = $person->facts(Gedcom::BIRTH_EVENTS, true)
                    ->first(static fn(Fact $fact): bool => $fact->place()->gedcomName() !== '');

                if ($birth instanceof Fact) {
                    $facts[$sosa] = $birth;
                }
            }
        }

        return $facts;
    }

    /**
     * builds and returns sosa relationship name in the active language
     *
     * @param int $sosa Sosa number
     *
     * @return string
     */
    protected function getSosaName(int $sosa): string
    {
        $path = '';

        while ($sosa > 1) {
            if ($sosa % 2 === 1) {
                $path = 'mot' . $path;
            } else {
                $path = 'fat' . $path;
            }
            $sosa = intdiv($sosa, 2);
        }

        return ucfirst($this->relationship_service->legacyNameAlgorithm($path));
    }
}
