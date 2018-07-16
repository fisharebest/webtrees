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
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\FactLocation;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PlacesMapModule
 */
class PlacesModule extends AbstractModule implements ModuleTabInterface
{
    const OSM_MIN_ZOOM = 2;
    const LINE_COLORS  = [
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
    public function getTitle()
    {
        return /* I18N: Name of a module */
            I18N::translate('Places');
    }

    /** {@inheritdoc} */
    public function getDescription()
    {
        return /* I18N: Description of the “OSM” module */
            I18N::translate('Show the location of events on a map.');
    }

    /** {@inheritdoc} */
    public function defaultAccessLevel()
    {
        return Auth::PRIV_PRIVATE;
    }

    /** {@inheritdoc} */
    public function defaultTabOrder()
    {
        return 4;
    }

    /** {@inheritdoc} */
    public function hasTabContent(Individual $individual)
    {
        return true;
    }

    /** {@inheritdoc} */
    public function isGrayedOut(Individual $individual)
    {
        return false;
    }

    /** {@inheritdoc} */
    public function canLoadAjax()
    {
        return true;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function assets($type = 'user')
    {
        $dir = WT_MODULES_DIR . $this->getName();
        if ($type === 'admin') {
            return [
                'css' => [
                    $dir . '/assets/css/osm-module.css',
                ],
                'js'  => [
                    $dir . '/assets/js/osm-admin.js',
                ],
            ];
        } else {
            return [
                'css' => [
                    $dir . '/assets/css/osm-module.css',
                ],
                'js'  => [
                    $dir . '/assets/js/osm-module.js',
                ],
            ];
        }
    }

    /** {@inheritdoc} */
    public function getTabContent(Individual $individual)
    {

        return view(
            'modules/openstreetmap/map',
            [
                'assets' => $this->assets(),
                'module' => $this->getName(),
                'ref'    => $individual->getXref(),
                'type'   => 'individual',
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getBaseDataAction(Request $request): JsonResponse
    {
        $provider = $this->getMapProviderData($request);
        $style    = $provider['selectedStyleName'] = '' ? '' : '.' . $provider['selectedStyleName'];

        switch ($provider['selectedProvIndex']) {
            case 'mapbox':
                $providerOptions = [
                    'id'          => $this->getPreference('mapbox_id'),
                    'accessToken' => $this->getPreference('mapbox_token'),
                ];
                break;
            case 'here':
                $providerOptions = [
                    'app_id'   => $this->getPreference('here_appid'),
                    'app_code' => $this->getPreference('here_appcode'),
                ];
                break;
            default:
                $providerOptions = [];
        };

        $options = [
            'minZoom'         => self::OSM_MIN_ZOOM,
            'providerName'    => $provider['selectedProvName'] . $style,
            'providerOptions' => $providerOptions,
            'animate'         => $this->getPreference('map_animate', 0),
            'I18N'            => [
                'zoomInTitle'  => I18N::translate('Zoom in'),
                'zoomOutTitle' => I18N::translate('Zoom out'),
                'reset'        => I18N::translate('Reset to initial map state'),
                'noData'       => I18N::translate('No mappable items'),
                'error'        => I18N::translate('An unknown error occurred'),
            ],
        ];

        return new JsonResponse($options);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function getMapDataAction(Request $request): JsonResponse
    {
        $xref        = $request->get('reference');
        $tree        = $request->attributes->get('tree');
        $indi        = Individual::getInstance($xref, $tree);

        $facts = $this->getPersonalFacts($request);

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
                    $polyline = null;
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
                            'summary'  => view(
                                'modules/openstreetmap/event-sidebar',
                                $event->shortSummary('individual', $id)
                            ),
                            'zoom'     => (int)$event->getZoom(),
                        ],
                    ];
                }
            }
        }

        return new JsonResponse($geojson, $code);
    }

    /**
     * @param Request $request
     *
     * @return array
     * @throws Exception
     */
    private function getPersonalFacts(Request $request)
    {
        $xref       = $request->get('reference');
        $tree       = $request->attributes->get('tree');
        $individual = Individual::getInstance($xref, $tree);
        $facts      = $individual->getFacts();
        foreach ($individual->getSpouseFamilies() as $family) {
            $facts = array_merge($facts, $family->getFacts());
            // Add birth of children from this family to the facts array
            foreach ($family->getChildren() as $child) {
                $childsBirth = $child->getFirstFact('BIRT');
                if ($childsBirth && !$childsBirth->getPlace()->isEmpty()) {
                    $facts[] = $childsBirth;
                }
            }
        }

        Functions::sortFacts($facts);

        $useable_facts = array_filter(
            $facts,
            function (Fact $item) {
                return !$item->getPlace()->isEmpty();
            }
        );

        return array_values($useable_facts);
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
            $providersFile        = WT_ROOT . WT_MODULES_DIR . $this->getName() . '/providers/providers.xml';
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
                        (array)$provider->styles
                    );

                    $key = preg_replace('/[^a-z\d]/i', '', strtolower((string)$provider->name));

                    self::$map_providers[$key] = [
                        'name'   => (string)$provider->name,
                        'styles' => array_combine($style_keys, (array)$provider->styles),
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
     * @param $parent_id
     * @param $placename
     * @param $places
     *
     * @throws Exception
     */
    private function buildLevel($parent_id, $placename, &$places)
    {
        $level = array_search('', $placename);
        $rows  = (array)Database::prepare(
            "SELECT pl_level, pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place"
        )
            ->execute([$parent_id])
            ->fetchAll(\PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                $index             = $row['pl_id'];
                $placename[$level] = $row['pl_place'];
                $places[]          = array_merge([$row['pl_level']], $placename, array_splice($row, 3));
                $this->buildLevel($index, $placename, $places);
            }
        }
    }
}
