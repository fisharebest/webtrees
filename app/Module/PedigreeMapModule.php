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

use Exception;
use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\FactLocation;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Services\ChartService;
use Fisharebest\Webtrees\Tree;
use Fisharebest\Webtrees\Webtrees;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PedigreeMapModule
 */
class PedigreeMapModule extends AbstractModule implements ModuleInterface, ModuleChartInterface
{
    use ModuleChartTrait;

    private const LINE_COLORS = [
        '#FF0000',
        // Red
        '#00FF00',
        // Green
        '#0000FF',
        // Blue
        '#FFB300',
        // Gold
        '#00FFFF',
        // Cyan
        '#FF00FF',
        // Purple
        '#7777FF',
        // Light blue
        '#80FF80'
        // Light green
    ];

    private static $map_providers  = null;
    private static $map_selections = null;

    /**
     * How should this module be labelled on tabs, menus, etc.?
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
        /* I18N: Description of the “OSM” module */
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
        return I18N::translate('Pedigree map of %s', $individual->getFullName());
    }

    /**
     * The URL for this chart.
     *
     * @param Individual $individual
     * @param string[]   $parameters
     *
     * @return string
     */
    public function chartUrl(Individual $individual, array $parameters = []): string
    {
        return route('module', [
                'module' => $this->name(),
                'action' => 'PedigreeMap',
                'xref'   => $individual->xref(),
                'ged'    => $individual->tree()->name(),
        ] + $parameters);
    }

    /**
     * @param Request      $request
     * @param Tree         $tree
     * @param ChartService $chart_service
     *
     * @return JsonResponse
     */
    public function getMapDataAction(Request $request, Tree $tree, ChartService $chart_service): JsonResponse
    {
        $xref        = $request->get('reference');
        $indi        = Individual::getInstance($xref, $tree);
        $color_count = count(self::LINE_COLORS);

        $facts = $this->getPedigreeMapFacts($request, $tree, $chart_service);

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];

        $sosa_points = [];

        foreach ($facts as $id => $fact) {
            $event = new FactLocation($fact, $indi);
            $icon  = $event->getIconDetails();
            if ($event->knownLatLon()) {
                $polyline         = null;
                $color            = self::LINE_COLORS[log($id, 2) % $color_count];
                $icon['color']    = $color; //make icon color the same as the line
                $sosa_points[$id] = $event->getLatLonJSArray();
                $sosa_parent      = intdiv($id, 2);
                if (array_key_exists($sosa_parent, $sosa_points)) {
                    // Would like to use a GeometryCollection to hold LineStrings
                    // rather than generate polylines but the MarkerCluster library
                    // doesn't seem to like them
                    $polyline = [
                        'points'  => [
                            $sosa_points[$sosa_parent],
                            $event->getLatLonJSArray(),
                        ],
                        'options' => [
                            'color' => $color,
                        ],
                    ];
                }
                $geojson['features'][] = [
                    'type'       => 'Feature',
                    'id'         => $id,
                    'valid'      => true,
                    'geometry'   => [
                        'type'        => 'Point',
                        'coordinates' => $event->getGeoJsonCoords(),
                    ],
                    'properties' => [
                        'polyline' => $polyline,
                        'icon'     => $icon,
                        'tooltip'  => $event->toolTip(),
                        'summary'  => view('modules/pedigree-map/events', $event->shortSummary('pedigree', $id)),
                        'zoom'     => (int) $event->getZoom(),
                    ],
                ];
            }
        }

        $code = empty($facts) ? Response::HTTP_NO_CONTENT : Response::HTTP_OK;

        return new JsonResponse($geojson, $code);
    }

    /**
     * @param Request      $request
     * @param Tree         $tree
     * @param ChartService $chart_service
     *
     * @return array
     */
    private function getPedigreeMapFacts(Request $request, Tree $tree, ChartService $chart_service): array
    {
        $xref        = $request->get('reference');
        $individual  = Individual::getInstance($xref, $tree);
        $generations = (int) $request->get(
            'generations',
            $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS')
        );
        $ancestors   = $chart_service->sosaStradonitzAncestors($individual, $generations);
        $facts       = [];
        foreach ($ancestors as $sosa => $person) {
            if ($person->canShow()) {
                $birth = $person->getFirstFact('BIRT');
                if ($birth instanceof Fact && !$birth->place()->isEmpty()) {
                    $facts[$sosa] = $birth;
                }
            }
        }

        return $facts;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getProviderStylesAction(Request $request): JsonResponse
    {
        $styles = $this->getMapProviderData($request);

        return new JsonResponse($styles);
    }

    /**
     * @param Request $request
     *
     * @return array|null
     */
    private function getMapProviderData(Request $request)
    {
        if (self::$map_providers === null) {
            $providersFile        = WT_ROOT . Webtrees::MODULES_PATH . 'openstreetmap/providers/providers.xml';
            self::$map_selections = [
                'provider' => $this->getPreference('provider', 'openstreetmap'),
                'style'    => $this->getPreference('provider_style', 'mapnik'),
            ];

            try {
                $xml = simplexml_load_file($providersFile);
                // need to convert xml structure into arrays & strings
                foreach ($xml as $provider) {
                    $style_keys = array_map(
                        function (string $item): string {
                            return preg_replace('/[^a-z\d]/i', '', strtolower($item));
                        },
                        (array) $provider->styles
                    );

                    $key = preg_replace('/[^a-z\d]/i', '', strtolower((string) $provider->name));

                    self::$map_providers[$key] = [
                        'name'   => (string) $provider->name,
                        'styles' => array_combine($style_keys, (array) $provider->styles),
                    ];
                }
            } catch (Exception $ex) {
                // Default provider is OpenStreetMap
                self::$map_selections = [
                    'provider' => 'openstreetmap',
                    'style'    => 'mapnik',
                ];
                self::$map_providers  = [
                    'openstreetmap' => [
                        'name'   => 'OpenStreetMap',
                        'styles' => ['mapnik' => 'Mapnik'],
                    ],
                ];
            };
        }

        //Ugly!!!
        switch ($request->get('action')) {
            case 'BaseData':
                $varName = (self::$map_selections['style'] === '') ? '' : self::$map_providers[self::$map_selections['provider']]['styles'][self::$map_selections['style']];
                $payload = [
                    'selectedProvIndex' => self::$map_selections['provider'],
                    'selectedProvName'  => self::$map_providers[self::$map_selections['provider']]['name'],
                    'selectedStyleName' => $varName,
                ];
                break;
            case 'ProviderStyles':
                $provider = $request->get('provider', 'openstreetmap');
                $payload  = self::$map_providers[$provider]['styles'];
                break;
            case 'AdminConfig':
                $providers = [];
                foreach (self::$map_providers as $key => $provider) {
                    $providers[$key] = $provider['name'];
                }
                $payload = [
                    'providers'     => $providers,
                    'selectedProv'  => self::$map_selections['provider'],
                    'styles'        => self::$map_providers[self::$map_selections['provider']]['styles'],
                    'selectedStyle' => self::$map_selections['style'],
                ];
                break;
            default:
                $payload = null;
        }

        return $payload;
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return object
     */
    public function getPedigreeMapAction(Request $request, Tree $tree)
    {
        $xref           = $request->get('xref', '');
        $individual     = Individual::getInstance($xref, $tree);
        $maxgenerations = $tree->getPreference('MAX_PEDIGREE_GENERATIONS');
        $generations    = $request->get('generations', $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        return $this->viewResponse('modules/pedigree-map/page', [
            'module_name'    => $this->name(),
            /* I18N: %s is an individual’s name */
            'title'          => I18N::translate('Pedigree map of %s', $individual->getFullName()),
            'tree'           => $tree,
            'individual'     => $individual,
            'generations'    => $generations,
            'maxgenerations' => $maxgenerations,
            'map'            => view(
                'modules/pedigree-map/chart',
                [
                    'module'      => $this->name(),
                    'ref'         => $individual->xref(),
                    'type'        => 'pedigree',
                    'generations' => $generations,
                ]
            ),
        ]);
    }
}
