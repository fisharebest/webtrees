<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2018 webtrees development team
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
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Exceptions\IndividualAccessDeniedException;
use Fisharebest\Webtrees\Exceptions\IndividualNotFoundException;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\FactLocation;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PedigreeMapModule
 */
class PedigreeMapModule extends AbstractModule implements ModuleChartInterface
{
    const LINE_COLORS = [
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

    /** {@inheritdoc} */
    public function getTitle(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('Pedigree map');
    }

    /** {@inheritdoc} */
    public function getDescription(): string
    {
        /* I18N: Description of the “OSM” module */
        return I18N::translate('Show the birthplace of ancestors on a map.');
    }

    /** {@inheritdoc} */
    public function defaultAccessLevel(): int
    {
        return Auth::PRIV_PRIVATE;
    }

    /**
     * Return a menu item for this chart.
     *
     * @param Individual $individual
     *
     * @return Menu
     */
    public function getChartMenu(Individual $individual): Menu
    {
        return new Menu(
            I18N::translate('Pedigree map'),
            route('module', [
                'module' => $this->getName(),
                'action' => 'PedigreeMap',
                'xref'   => $individual->getXref(),
                'ged'    => $individual->getTree()->getName(),
            ]),
            'menu-chart-pedigreemap',
            ['rel' => 'nofollow']
        );
    }

    /**
     * Return a menu item for this chart - for use in individual boxes.
     *
     * @param Individual $individual
     *
     * @return Menu
     */
    public function getBoxChartMenu(Individual $individual): Menu
    {
        return $this->getChartMenu($individual);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return JsonResponse
     */
    public function getMapDataAction(Request $request, Tree $tree): JsonResponse
    {
        $xref        = $request->get('reference');
        $indi        = Individual::getInstance($xref, $tree);
        $color_count = count(self::LINE_COLORS);

        $facts = $this->getPedigreeMapFacts($request, $tree);

        $geojson = [
            'type'     => 'FeatureCollection',
            'features' => [],
        ];
        if (empty($facts)) {
            $code = 204;
        } else {
            $code = 200;
            foreach ($facts as $id => $fact) {
                $event = new FactLocation($fact, $indi);
                $icon  = $event->getIconDetails();
                if ($event->knownLatLon()) {
                    $polyline         = null;
                    $color            = self::LINE_COLORS[log($id, 2) % $color_count];
                    $icon['color']    = $color; //make icon color the same as the line
                    $sosa_points[$id] = $event->getLatLonJSArray();
                    $sosa_parent      = (int) floor($id / 2);
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
                            'summary'  => view('modules/pedigree-map/event-sidebar', $event->shortSummary('pedigree', $id)),
                            'zoom'     => (int) $event->getZoom(),
                        ],
                    ];
                }
            }
        }

        return new JsonResponse($geojson, $code);
    }

    /**
     * @param Request $request
     * @param Tree    $tree
     *
     * @return array
     */
    private function getPedigreeMapFacts(Request $request, Tree $tree): array
    {
        $xref        = $request->get('reference');
        $individual  = Individual::getInstance($xref, $tree);
        $generations = (int) $request->get(
            'generations',
            $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS')
        );
        $ancestors   = $this->sosaStradonitzAncestors($individual, $generations);
        $facts       = [];
        foreach ($ancestors as $sosa => $person) {
            if ($person !== null && $person->canShow()) {
                /** @var Fact $birth */
                $birth = $person->getFirstFact('BIRT');
                if ($birth && !$birth->getPlace()->isEmpty()) {
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
            $providersFile        = WT_ROOT . WT_MODULES_DIR . 'openstreetmap/providers/providers.xml';
            self::$map_selections = [
                'provider' => $this->getPreference('provider', 'openstreetmap'),
                'style'    => $this->getPreference('provider_style', 'mapnik'),
            ];

            try {
                $xml = simplexml_load_file($providersFile);
                // need to convert xml structure into arrays & strings
                foreach ($xml as $provider) {
                    $style_keys = array_map(
                        function ($item) {
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
                self::$map_providers = [
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
        $xref           = $request->get('xref');
        $individual     = Individual::getInstance($xref, $tree);
        $maxgenerations = $tree->getPreference('MAX_PEDIGREE_GENERATIONS');
        $generations    = $request->get('generations', $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));

        if ($individual === null) {
            throw new IndividualNotFoundException();
        }

        if (!$individual->canShow()) {
            throw new IndividualAccessDeniedException();
        }

        return (object)[
            'name' => 'modules/pedigree-map/pedigree-map-page',
            'data' => [
                'module'         => $this->getName(),
                /* I18N: %s is an individual’s name */
                'title'          => I18N::translate('Pedigree map of %s', $individual->getFullName()),
                'tree'           => $tree,
                'individual'     => $individual,
                'generations'    => $generations,
                'maxgenerations' => $maxgenerations,
                'map'            => view(
                    'modules/pedigree-map/pedigree-map',
                    [
                        'module'      => $this->getName(),
                        'ref'         => $individual->getXref(),
                        'type'        => 'pedigree',
                        'generations' => $generations,
                    ]
                ),
            ],
        ];
    }

    // @TODO shift the following function to somewhere more appropriate during restructure

    /**
     * Copied from AbstractChartController.php
     *
     * Find the ancestors of an individual, and generate an array indexed by
     * Sosa-Stradonitz number.
     *
     * @param Individual $individual  Start with this individual
     * @param int        $generations Fetch this number of generations
     *
     * @return Individual[]
     */
    private function sosaStradonitzAncestors(Individual $individual, int $generations): array
    {
        /** @var Individual[] $ancestors */
        $ancestors = [
            1 => $individual,
        ];

        for ($i = 1, $max = 2 ** ($generations - 1); $i < $max; $i++) {
            $ancestors[$i * 2]     = null;
            $ancestors[$i * 2 + 1] = null;

            $individual = $ancestors[$i];

            if ($individual !== null) {
                $family = $individual->getPrimaryChildFamily();
                if ($family !== null) {
                    if ($family->getHusband() !== null) {
                        $ancestors[$i * 2] = $family->getHusband();
                    }
                    if ($family->getWife() !== null) {
                        $ancestors[$i * 2 + 1] = $family->getWife();
                    }
                }
            }
        }

        return $ancestors;
    }
}
