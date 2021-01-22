<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
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
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Gedcom;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\PlaceLocation;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function app;
use function array_key_exists;
use function assert;
use function count;
use function intdiv;
use function is_string;
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

    protected const ROUTE_URL  = '/tree/{tree}/pedigree-map-{generations}/{xref}';

    // Defaults
    public const DEFAULT_GENERATIONS = '4';
    public const DEFAULT_PARAMETERS  = [
        'generations' => self::DEFAULT_GENERATIONS,
    ];

    // Limits
    public const MAXIMUM_GENERATIONS = 10;

    // CSS colors for each generation
    private const COLORS = [
        'Red',
        'Green',
        'Blue',
        'Gold',
        'Cyan',
        'Orange',
        'DarkBlue',
        'LightGreen',
        'Magenta',
        'Brown',
    ];

    private const DEFAULT_ZOOM = 2;

    /** @var ChartService */
    private $chart_service;

    /**
     * PedigreeMapModule constructor.
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
            ]);
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
        return I18N::translate('Pedigree map of %s', $individual->fullName());
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
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $xref = $request->getAttribute('xref');
        assert(is_string($xref));

        $individual  = Registry::individualFactory()->make($xref, $tree);
        $individual  = Auth::checkIndividualAccess($individual, false, true);

        $user        = $request->getAttribute('user');
        $generations = (int) $request->getAttribute('generations');
        Auth::checkComponentAccess($this, ModuleChartInterface::class, $tree, $user);

        // Convert POST requests into GET requests for pretty URLs.
        if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
            $params = (array) $request->getParsedBody();

            return redirect(route(static::class, [
                'tree'        => $tree->name(),
                'xref'        => $params['xref'],
                'generations' => $params['generations'],
            ]));
        }

        $map = view('modules/pedigree-map/chart', [
            'data'     => $this->getMapData($request),
            'provider' => [
                'url'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                'options' => [
                    'attribution' => '<a href="https://www.openstreetmap.org/copyright">&copy; OpenStreetMap</a> contributors',
                    'max_zoom'    => 19
                ]
            ]
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
    private function getMapData(ServerRequestInterface $request): array
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $color_count = count(self::COLORS);

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
                $color              = self::COLORS[$sosa_child % $color_count];

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
                        'tooltip'   => $fact->place()->gedcomName(),
                        'summary'   => view('modules/pedigree-map/events', [
                            'fact'         => $fact,
                            'relationship' => ucfirst($this->getSosaName($sosa)),
                            'sosa'         => $sosa,
                        ]),
                        'zoom'      => self::DEFAULT_ZOOM,
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
    private function getPedigreeMapFacts(ServerRequestInterface $request, ChartService $chart_service): array
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $generations = (int) $request->getAttribute('generations');
        $xref        = $request->getAttribute('xref');
        $individual  = Registry::individualFactory()->make($xref, $tree);
        $ancestors   = $chart_service->sosaStradonitzAncestors($individual, $generations);
        $facts       = [];
        foreach ($ancestors as $sosa => $person) {
            if ($person->canShow()) {
                $birth = $person->facts(Gedcom::BIRTH_EVENTS, true)
                    ->first(static function (Fact $fact): bool {
                        return $fact->place()->gedcomName() !== '';
                    });

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
    private function getSosaName(int $sosa): string
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

        return Functions::getRelationshipNameFromPath($path);
    }
}
