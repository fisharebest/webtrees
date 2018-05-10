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

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\FactLocation;
use Fisharebest\Webtrees\Filter;
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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class OpenStreetMapModule
 */
class OpenStreetMapModule extends AbstractModule implements ModuleConfigInterface, ModuleTabInterface, ModuleChartInterface {

	// How to update the database schema for this module
	const SCHEMA_TARGET_VERSION   = 4;
	const SCHEMA_SETTING_NAME     = 'OSM_SCHEMA_VERSION';
	const SCHEMA_MIGRATION_PREFIX = '\Fisharebest\Webtrees\Module\OpenStreetMap\Schema';

	// Package version numbers
	const LEAFLET      = '1.3.1';
	const LEAFLET_BEAU = '1.0.5';
	const LEAFLET_MC   = '1.3.0';
	const LEAFLET_PROV = '1.1.17';
	const LEAFLET_GEO  = '2.6.0';

	const OSM_MIN_ZOOM = 2;
	const LINE_COLORS  = [
		'#FF0000', // Red
		'#00FF00', // Green
		'#0000FF', // Blue
		'#FFB300', // Gold
		'#00FFFF', // Cyan
		'#FF00FF', // Purple
		'#7777FF', // Light blue
		'#80FF80'  // Light green
	];

	private static $map_providers  = null;
	private static $map_selections = null;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Event Map');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “OSM” module */
			I18N::translate('Show the location of events on a map using Open Street Maps');
	}

	/** {@inheritdoc} */
	public function defaultAccessLevel() {
		# Auth::PRIV_PRIVATE actually means public.
		# Auth::PRIV_NONE - no acces to anybody.
		return Auth::PRIV_PRIVATE;
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 4;
	}

	/** {@inheritdoc} */
	public function hasTabContent(Individual $individual) {
		return true;
	}

	/** {@inheritdoc} */
	public function isGrayedOut(Individual $individual) {
		return false;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function modAction($mod_action) {
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		return route('admin-module', ['module' => $this->getName(), 'action' => 'AdminConfig']);
	}

	/**
	 * Return a menu item for this chart.
	 *
	 * @param Individual $individual
	 *
	 * @return Menu
	 */
	public function getChartMenu(Individual $individual) {
		return new Menu(
			I18N::translate('Pedigree Map'),
			route('module', ['module' => $this->getName(), 'action' => 'Pedigreemap', 'xref' => $individual->getXref()]),
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
	public function getBoxChartMenu(Individual $individual) {
		return $this->getChartMenu($individual);
	}

	/**
	 * @param string $type
	 * @return array
	 */
	public function assets($type = 'user') {
		$dir = WT_MODULES_DIR . $this->getName();
		if ($type === 'admin') {
			$files = [
				'css' => [
					$dir . '/packages/leaflet-' . self::LEAFLET . '/leaflet.css',
					$dir . '/packages/leaflet-geosearch-' . self::LEAFLET_GEO . '/leaflet-geosearch.css',
					$dir . '/assets/css/osm-module.css',
				],
				'js' => [
					$dir . '/packages/leaflet-' . self::LEAFLET . '/leaflet.js',
					$dir . '/packages/leaflet-providers-' . self::LEAFLET_PROV . '/leaflet-providers.js',
					$dir . '/packages/leaflet-geosearch-' . self::LEAFLET_GEO . '/leaflet-geosearch.min.js',
					$dir . '/assets/js/osm-admin.js',
				],
			];
		} else {
			$files = [
				'css' => [
					$dir . '/packages/leaflet-' . self::LEAFLET . '/leaflet.css',
					$dir . '/packages/BeautifyMarker-' . self::LEAFLET_BEAU . '/leaflet-beautify-marker-icon.css',
					$dir . '/packages/Leaflet.markercluster-' . self::LEAFLET_MC . '/dist/MarkerCluster.Default.css',
					$dir . '/packages/Leaflet.markercluster-' . self::LEAFLET_MC . '/dist/MarkerCluster.css',
					$dir . '/assets/css/osm-module.css',
				],
				'js' => [
					$dir . '/packages/leaflet-' . self::LEAFLET . '/leaflet.js',
					$dir . '/packages/leaflet-providers-' . self::LEAFLET_PROV . '/leaflet-providers.js',
					$dir . '/packages/BeautifyMarker-' . self::LEAFLET_BEAU . '/leaflet-beautify-marker-icon.js',
					$dir . '/packages/leaflet.markercluster-' . self::LEAFLET_MC . '/dist/leaflet.markercluster.js',
					$dir . '/assets/js/osm-module.js',
				],
			];
		}

		return $files;
	}

	/** {@inheritdoc} */
	public function getTabContent(Individual $individual) {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

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
	 * @return JsonResponse
	 */
	public function getBaseDataAction(Request $request) {
		$provider = $this->getMapProviderData($request);
		$style    = $provider['selectedStyleName']    = '' ? '' : '.' . $provider['selectedStyleName'];

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

		return new jsonResponse($options);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @throws \Exception
	 */
	public function getMapDataAction(Request $request) {
		switch ($request->get('type')) {
			case 'placelist':
				$response = $this->placelistGetMapData($request);
				break;
			default:
				$response = $this->getMapData($request);
		}

		return $response;
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @throws \Exception
	 */
	private function getMapData(Request $request) {
		$mapType     = $request->get('type');
		$xref        = $request->get('reference');
		$tree        = $request->attributes->get('tree');
		$indi        = Individual::getInstance($xref, $tree);
		$color_count = count(self::LINE_COLORS);

		switch ($mapType) {
			case 'pedigree':
				$facts = $this->getPedigreeMapFacts($request);
				break;
			default:
				$facts = $this->getPersonalFacts($request);
		}

		$geojson = ['type' => 'FeatureCollection', 'features' => []];
		if (empty($facts)) {
			$code = 204;
		} else {
			$code = 200;
			foreach ($facts as $id => $fact) {
				$event = new FactLocation($fact, $indi);
				$icon  = $event->getIconDetails();
				if ($event->knownLatLon()) {
					$polyline = null;
					if ($mapType === 'pedigree') {
						$color            = self::LINE_COLORS[log($id, 2) % $color_count];
						$icon['color']    = $color; //make icon color the same as the line
						$sosa_points[$id] = $event->getLatLonJSArray();
						$sosa_parent      = (int)floor($id / 2);
						if (array_key_exists($sosa_parent, $sosa_points)) {
							// Would like to use a GeometryCollection to hold LineStrings
							// rather than generate polylines but the MarkerCluster library
							// doesn't seem to like them
							$polyline = [
								'points' => [
									$sosa_points[$sosa_parent],
									$event->getLatLonJSArray(),
								],
								'options' => [
									'color' => $color,
								],
							];
						}
					}
					$geojson['features'][] = [
						'type'     => 'Feature',
						'id'       => $id,
						'valid'    => true,
						'geometry' => [
							'type'        => 'Point',
							'coordinates' => $event->getGeoJsonCoords(),
						],
						'properties' => [
							'polyline' => $polyline,
							'icon'     => $icon,
							'tooltip'  => $event->toolTip(),
							'summary'  => view(
								'modules/openstreetmap/event-sidebar',
								$event->shortSummary($mapType, $id)
							),
							'zoom' => (int)$event->getZoom(),
						],
					];
				}
			}
		}

		return new jsonResponse($geojson, $code);
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @throws \Exception
	 */
	private function placelistGetMapData(Request $request) {
		$reference = $request->get('reference');
		$tree      = $request->attributes->get('tree');
		$placeObj  = new Place($reference, $tree);
		$places    = $placeObj->getChildPlaces();
		$features  = [];
		$flag_path = WT_MODULES_DIR . $this->getName() . '/';
		$stats     = new Stats($tree);
		$showlink  = true;

		if (empty($places)) {
			$places[] = $placeObj;
			$showlink = false;
		}

		foreach ($places as $id => $place) {
			$location = new Location($place->getGedcomName());

			//Stats
			$placeStats = [];
			foreach (['INDI', 'FAM'] as $type) {
				$tmp               = $stats->statsPlaces($type, false, $place->getPlaceId());
				$placeStats[$type] = empty($tmp) ? 0 : $tmp[0]['tot'];
			}

			//Flag
			if ($location->getIcon() !== null && is_file($flag_path . $location->getIcon())) {
				$flag = $flag_path . $location->getIcon();
			} else {
				$flag = '';
			}

			$features[] = [
				'type'     => 'Feature',
				'id'       => $id,
				'valid'    => $location->isValid() && $location->knownLatLon(),
				'geometry' => [
					'type'        => 'Point',
					'coordinates' => $location->getGeoJsonCoords(),
				],
				'properties' => [
					'icon' => [
						'name'  => 'globe',
						'color' => '#1e90ff',
					],
					'tooltip' => strip_tags($place->getFullName()),
					'summary' => view(
						'modules/openstreetmap/place-sidebar',
						[
							'showlink' => $showlink,
							'flag'     => $flag,
							'place'    => $place,
							'stats'    => $placeStats,
						]
					),
					'zoom' => (int)($location->getZoom() ?? 2),
				],
			];
		}

		$code = empty($features) ? 204 : 200;

		return new jsonResponse(['type' => 'FeatureCollection', 'features' => $features], $code);
	}

	/**
	 * @param Request $request
	 * @return array
	 * @throws \Exception
	 */
	private function getPersonalFacts(Request $request) {
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
	 * @return array
	 * @throws \Exception
	 */
	private function getPedigreeMapFacts(Request $request) {
		$xref        = $request->get('reference');
		$tree        = $request->attributes->get('tree');
		$individual  = Individual::getInstance($xref, $tree);
		$generations = (int)$request->get(
			'generations',
			$tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS')
		);
		$ancestors = $this->sosaStradonitzAncestors($individual, $generations);
		$facts     = [];
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
	 * @return JsonResponse
	 */
	public function getProviderStylesAction(Request $request) {
		$styles = $this->getMapProviderData($request);

		return new jsonResponse($styles);
	}

	/**
	 * @param Request $request
	 * @return array|null
	 */
	private function getMapProviderData(Request $request) {
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
			} catch (\Exception $ex) {
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
				$payload = ['selectedProvIndex' => self::$map_selections['provider'],
							'selectedProvName'           => self::$map_providers[self::$map_selections['provider']]['name'],
							'selectedStyleName'          => $varName,
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
				$payload = ['providers' => $providers,
							'selectedProv'       => self::$map_selections['provider'],
							'styles'             => self::$map_providers[self::$map_selections['provider']]['styles'],
							'selectedStyle'      => self::$map_selections['style'],
				];
				break;
			default:
				$payload = null;
		}

		return $payload;
	}

	/**
	 * @param Request $request
	 * @return object
	 * @throws \Exception
	 */
	public function getPedigreemapAction(Request $request) {
		/** @var Tree $tree */
		$tree           = $request->attributes->get('tree');
		$xref           = $request->get('xref');
		$individual     = Individual::getInstance($xref, $tree);
		$maxgenerations = $tree->getPreference('MAX_PEDIGREE_GENERATIONS');
		$generations    = $request->get('generations', $tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));

		return (object)[
			'name' => 'modules/openstreetmap/pedigreemap',
			'data' => [
				'assets' => $this->assets(),
				'module' => $this->getName(),
				'title'  => /* I18N: %s is an individual’s name */
					I18N::translate('Pedigree map of %s', $individual->getFullName()),
				'tree'           => $tree,
				'individual'     => $individual,
				'generations'    => $generations,
				'maxgenerations' => $maxgenerations,
				'map'            => view(
					'modules/openstreetmap/map',
					[
						'assets'      => $this->assets(),
						'module'      => $this->getName(),
						'ref'         => $individual->getXref(),
						'type'        => 'pedigree',
						'generations' => $generations,
					]
				),
			],
		];
	}

	/*
	 * Admin functions called via admin-module route
	 */

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @throws \Exception
	 */
	public function getAdminMapDataAction(Request $request) {
		$id  = $request->get('id', 0);
		$row = Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id = :id")
			->execute(['id' => $id])
			->fetchOneRow();

		if (empty($row)) {
			$json = [
				'zoom'        => self::OSM_MIN_ZOOM,
				'coordinates' => [0, 0],
			];
		} else {
			$json = [
				'zoom'        => $row->pl_zoom ? $row->pl_zoom : self::OSM_MIN_ZOOM,
				'coordinates' => [
					$row->pl_lati ? strtr($row->pl_lati, ['N' => '', 'S' => '-', ',' => '.']) : 0,
					$row->pl_long ? strtr($row->pl_long, ['E' => '', 'W' => '-', ',' => '.']) : 0,
				],
			];
		}

		return new jsonResponse($json);
	}

	/**
	 * @param Request $request
	 * @return object
	 */
	public function getAdminConfigAction(Request $request) {
		return (object)[
			'name' => 'modules/openstreetmap/admin-config',
			'data' => [
				'title'        => I18N::translate('Open Street Maps (Configuration)'),
				'module'       => $this->getName(),
				'provider'     => $this->getMapProviderData($request),
				'mapboxId'     => $this->getPreference('mapbox_id'),
				'mapboxToken'  => $this->getPreference('mapbox_token'),
				'here_Appid'   => $this->getPreference('here_appid'),
				'here_Appcode' => $this->getPreference('here_appcode'),
				'hierarchy'    => $this->getPreference('place_hierarchy', '0'),
				'animate'      => $this->getPreference('map_animate', '0'),
			],
		];
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function postAdminUpdateConfigAction(Request $request): RedirectResponse {
		$this->setPreference('mapbox_id', $request->get('mapbox_id', ''));
		$this->setPreference('mapbox_token', $request->get('mapbox_token', ''));
		$this->setPreference('here_appid', $request->get('here_appid', ''));
		$this->setPreference('here_appcode', $request->get('here_appcode', ''));
		$this->setPreference('provider', $request->get('provider'));
		$this->setPreference('provider_style', $request->get('provider_style', ''));
		$this->setPreference('place_hierarchy', $request->get('place_hierarchy'));
		$this->setPreference('map_animate', $request->get('map_animate'));

		FlashMessages::addMessage(
			I18N::translate(
				'The preferences for the module “%s” have been updated.',
				$this->getTitle()
			),
			'success'
		);

		return new RedirectResponse(route('admin-module', ['module' => $this->getName(), 'action' => 'AdminConfig']));
	}

	/**
	 * @param Request $request
	 * @return object
	 * @throws \Exception
	 */
	public function postAdminPlacesAction(Request $request) {
		return $this->getAdminPlacesAction($request);
	}

	/**
	 * @param Request $request
	 * @return object
	 * @throws \Exception
	 */
	public function getAdminPlacesAction(Request $request) {
		$parent_id   = (int)$request->get('parent_id', 0);
		$inactive    = (bool)$request->get('inactive');
		$hierarchy   = $this->gethierarchy($parent_id);
		$title       = I18N::translate('Open Street Maps (Geographic data)');
		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			route(
				'admin-module',
				['module' => $this->getName(), 'action' => 'AdminPlaces', 'inactive' => $inactive]
			)          => $title,
		];

		foreach ($hierarchy as $row) {
			$breadcrumbs[route(
				'admin-module',
				['module'    => $this->getName(),
				 'action'    => 'AdminPlaces',
				 'parent_id' => $row->pl_id,
				 'inactive'  => $inactive]
			)] = $row->pl_place;
		}
		$breadcrumbs[] = array_pop($breadcrumbs);

		return (object)[
			'name' => 'modules/openstreetmap/admin-places',
			'data' => [
				'title'       => $title,
				'breadcrumbs' => $breadcrumbs,
				'inactive'    => $inactive,
				'parent_id'   => $parent_id,
				'placelist'   => $this->getPlaceListLocation($parent_id, $inactive),
				'module'      => $this->getName(),
			],
		];
	}

	/**
	 * Create or edit a geographic place.
	 *
	 * @param Request $request
	 * @return object
	 * @throws \Exception
	 */
	public function getAdminPlaceEditAction(Request $request) {
		$parent_id = (int)$request->get('parent_id', 0);
		$place_id  = (int)$request->get('place_id');
		$inactive  = (int)$request->get('inactive');
		$hierarchy = $this->gethierarchy($place_id);
		$fqpn      = empty($hierarchy) ? '' : $hierarchy[0]->fqpn;
		$location  = new Location($fqpn);
		if ($location->isValid()) {
			$lat = $location->getLat();
			$lng = $location->getLon();
			$id  = $place_id;
		} else {
			$lat = '';
			$lng = '';
			$id  = $parent_id;
		}

		$title = I18N::translate('Open Street Maps (Geographic data)');

		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			route(
				'admin-module',
				['module' => $this->getName(), 'action' => 'AdminPlaces', 'inactive' => $inactive]
			)          => $title,
		];

		foreach ($hierarchy as $row) {
			$breadcrumbs[route(
				'admin-module',
				['module'    => $this->getName(),
				 'action'    => 'AdminPlaces',
				 'parent_id' => $row->pl_id,
				 'inactive'  => $inactive]
			)] = $row->pl_place;
		}
		$breadcrumbs[] = $place_id === 0 ? I18N::translate('Add') : I18N::translate('Edit');

		return (object)[
			'name' => 'modules/openstreetmap/admin-place-edit',
			'data' => [
				'module'      => $this->getName(),
				'assets'      => $this->assets('admin'),
				'breadcrumbs' => $breadcrumbs,
				'title'       => $title,
				'location'    => $location,
				'place_id'    => $place_id,
				'parent_id'   => $parent_id,
				'hierarchy'   => $hierarchy,
				'inactive'    => $inactive,
				'lat'         => $lat,
				'lng'         => $lng,
				'ref'         => $id,
			],
		];
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	public function postAdminSaveAction(Request $request) {
		if (Filter::checkCsrf()) {
			$parent_id = (int)$request->get('parent_id');
			$place_id  = (int)$request->get('place_id');
			$inactive  = (int)$request->get('inactive');
			$lat       = round($request->get('new_place_lati'), 5); // 5 decimal places (locate to within about 1 metre)
			$lat       = ($lat < 0 ? 'S' : 'N') . abs($lat);
			$lng       = round($request->get('new_place_long'), 5);
			$lng       = ($lng < 0 ? 'W' : 'E') . abs($lng);
			$hierarchy = $this->gethierarchy($parent_id);
			$level     = count($hierarchy);
			$icon      = $request->get('icon', null);
			$icon      = $icon === '' ? null : $icon;
			$zoom      = $request->get('new_zoom_factor');
			$zoom      = $zoom === '' ? null : $zoom;


			if ($place_id === 0) {
				Database::prepare(
					"INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)
						  VALUES (:id, :parent, :level, :place, :lng, :lat, :zoom, :icon)"
				)->execute(
					[
					'id'     => (int)Database::prepare("SELECT MAX(pl_id)+1 FROM `##placelocation`")->fetchOne(),
					'parent' => $parent_id,
					'level'  => $level,
					'place'  => $request->get('new_place_name'),
					'lat'    => $request->get('lati_control') . $lat,
					'lng'    => $request->get('long_control') . $lng,
					'zoom'   => $zoom,
					'icon'   => $icon,
				]
				);
			} else {
				Database::prepare(
					"UPDATE `##placelocation` SET pl_place = :place, pl_lati = :lat, pl_long = :lng, pl_zoom = :zoom, pl_icon = :icon WHERE pl_id = :id"
				)->execute(
					[
					'id'    => $place_id,
					'place' => $request->get('new_place_name'),
					'lat'   => $request->get('lati_control') . $lat,
					'lng'   => $request->get('long_control') . $lng,
					'zoom'  => (int)$request->get('new_zoom_factor'),
					'icon'  => $icon,
				]
				);
			}
			FlashMessages::addMessage(
				I18N::translate(
					'The details for “%s” have been updated.',
					$request->get('new_place_name')
				),
				'success'
			);

			return new RedirectResponse(
				route(
				'admin-module',
				['module' => $this->getName(), 'action' => 'AdminPlaces', 'inactive' => $inactive]
			)
			);
		} else {
			throw new AccessDeniedHttpException();
		}
	}

	/**
	 * Delete a geographic place.
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	public function postAdminDeleteRecordAction(Request $request): RedirectResponse {
		if (Filter::checkCsrf()) {
			$place_id  = (int)$request->get('place_id');
			$parent_id = (int)$request->get('parent_id');
			$inactive  = (int)$request->get('inactive');

			try {
				Database::prepare(
					"DELETE FROM `##placelocation` WHERE pl_id = :id"
				)->execute(
					[
						'id' => $place_id,
					]
				);
			} catch (\Exception $ex) {
				DebugBar::addThrowable($ex);

				FlashMessages::addMessage(
					I18N::translate('Location not removed: this location contains sub-locations'),
					'danger'
				);
			}
			// If after deleting there are no more places at this level then go up a level
			$children = (int)Database::prepare(
				"SELECT COUNT(pl_id) FROM `##placelocation` WHERE pl_parent_id = :parent_id"
			)
				->execute(['parent_id' => $parent_id])
				->fetchOne();

			if ($children === 0) {
				$row = Database::prepare('SELECT pl_parent_id FROM `##placelocation` WHERE pl_id = :parent_id')
					->execute(['parent_id' => $parent_id])
					->fetchOneRow();
				$parent_id = $row->pl_parent_id;
			}

			return new RedirectResponse(
				route(
				'admin-module',
				['module'    => $this->getName(),
				 'action'    => 'AdminPlaces',
				 'parent_id' => $parent_id,
				 'inactive'  => $inactive]
			)
			);
		} else {
			throw new AccessDeniedHttpException();
		}
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	public function getAdminExportAction(Request $request) {
		$parent_id = (int)$request->get('parent_id');
		$format    = $request->get('format', 'csv');
		$maxlevel  = (int)Database::prepare("SELECT max(pl_level) FROM `##placelocation`")->execute()->fetchOne();
		$startfqpn = [];
		$hierarchy = $this->gethierarchy($parent_id);
		$geojson   = [];

		// Create the file name
		$place_name = empty($hierarchy) ? 'Global' : $hierarchy[0]->fqpn; // $hierarchy[0] always holds the full placename
		$place_name = str_replace(Place::GEDCOM_SEPARATOR, '-', $place_name);
		$filename   = 'Places-' . preg_replace('/[^a-zA-Z0-9\-\.]/', '', $place_name) . '.' . $format;

		// Fill in the place names for the starting conditions
		foreach ($hierarchy as $level => $record) {
			$startfqpn[$level] = $record->pl_place;
		}
		$startfqpn = array_pad($startfqpn, $maxlevel + 1, '');

		// Generate an array containing the data to output
		$this->buildLevel($parent_id, $startfqpn, $places);

		if ($format === 'csv') {
			// Create the header line for the output file (always English)
			$placenames[] = I18N::translate('Level');
			for ($i = 0; $i <= $maxlevel; $i++) {
				$placenames[] = 'Place' . $i;
			}
			$header = array_merge($placenames, ['Longitude', 'Latitude', 'Zoom', 'Icon']);
			array_unshift($places, $header);
		} else {
			$geojson = ['type' => 'FeatureCollection', 'features' => []];
		}
		// Output the data
		try {
			$fp = fopen('php://output', 'wb');
			header_remove();
			header("Content-Type: application/download charset=utf-8");
			header("Content-Disposition: attachment; filename=$filename");

			foreach ($places as $place) {
				if ($format === 'csv') {
					fputcsv($fp, $place);
				} else {
					if (!$place['pl_long'] || !$place['pl_lati']) {
						continue;
					}
					$fqpn = implode(
						Place::GEDCOM_SEPARATOR,
						array_reverse(
							array_filter(
								array_slice($place, 1, $maxlevel + 1)
							)
						)
					);
					$long = (float)strtr($place['pl_long'], ['E' => '', 'W' => '-', ',' => '.']);
					$lati = (float)strtr($place['pl_lati'], ['N' => '', 'S' => '-', ',' => '.']);

					$geojson['features'][] = [
						'type'     => 'Feature',
						'geometry' => [
							'type'        => 'Point',
							'coordinates' => [$long, $lati],
						],
						'properties' => [
							'level' => $place[0],
							'name'  => $fqpn,
							'zoom'  => $place['pl_zoom'],
							'icon'  => $place['pl_icon'],
						],
					];
				}
			}
			if ($format === 'geojson') {
				$jsonstr = json_encode($geojson, JSON_PRETTY_PRINT);
				header("Content-Length: " . strlen($jsonstr));
				fwrite($fp, $jsonstr);
			}
			fclose($fp);
		} catch (\Exception $e) {
			Log::addErrorLog($e->getMessage());
			FlashMessages::addMessage($e->getMessage(), 'error');
		}

		return new RedirectResponse(route('admin-module', ['module' => $this->getName(), 'action' => 'AdminPlaces']));
	}

	public function getAdminImportFormAction(Request $request) {
		$parent_id   = (int)$request->get('parent_id');
		$inactive    = (int)$request->get('inactive');
		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			route(
				'admin-module',
				[
					'module'    => $this->getName(),
					'action'    => 'AdminPlaces',
					'parent_id' => 0,
					'inactive'  => $inactive,
				]
			) => $this->getTitle() . ' (' . I18N::translate('Geographic data') . ')',
			I18N::translate('Import file'),
		];
		$files = $this->findFiles(WT_MODULES_DIR . $this->getName() . '/extra', ['csv', 'geojson', 'json']);
		uasort(
			$files,
			function ($a, $b) {
				$la = strlen($a);
				$lb = strlen($b);

				return $la === $lb ? I18N::strcasecmp($a, $b) : $la - $lb;
			}
		);

		return (object)[
			'name' => 'modules/openstreetmap/admin-import-form',
			'data' => [
				'title'       => I18N::translate('Import geographic data'),
				'module'      => $this->getName(),
				'breadcrumbs' => $breadcrumbs,
				'parent_id'   => $parent_id,
				'inactive'    => $inactive,
				'files'       => $files,
			],
		];
	}

	/**
	 * This function assumes the input file layout is
	 * level followed by a variable number of placename fields
	 * followed by Longitude, Latitude, Zoom & Icon
	 *
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	public function postAdminImportAction(Request $request) {
		$serverfile  = $request->get('serverfile');
		$options     = $request->get('import-options');
		$inactive    = $request->get('inactive');
		$filename    = '';
		$places      = [];
		$input_array = [];
		$fields      = 0;
		$delimiter   = '';
		$field_names = ['pl_level', 'pl_long', 'pl_lati', 'pl_zoom', 'pl_icon', 'fqpn'];

		if ($serverfile !== '') {  // first choice is file on server
			$filename = WT_MODULES_DIR . $this->getName() . '/extra/' . $serverfile;
		} elseif ($_FILES['localfile']['error'] === UPLOAD_ERR_OK) { // 2nd choice is local file
			$filename = $_FILES['localfile']['tmp_name'];
		}

		if (is_file($filename)) {
			$string   = file_get_contents($filename);
			$filetype = '?';

			// Check the filetype
			if (stripos($string, 'FeatureCollection') !== false) {
				$filetype = 'geojson';
			} else {
				$input_array = preg_split("/\r?\n/", $string, -1, PREG_SPLIT_NO_EMPTY);
				$record      = $input_array[0];

				if (strpos($record, ';') !== false) {
					$delimiter = ';';
				} elseif (strpos($record, ',') !== false) {
					$delimiter = ',';
				}
				if ($delimiter !== '') {
					if (!is_numeric($record[0])) { // lose the header
						array_shift($input_array);
					}

					// are the records in a format we can read
					$row    = explode($delimiter, $input_array[0]);
					$fields = count($row);
					if ($fields >= 6 &&
						(bool)preg_match("/[SN][0-9]*\.?[0-9]*/", $row[$fields - 3]) &&
						(bool)preg_match("/[EW][0-9]*\.?[0-9]*/", $row[$fields - 4])) {
						$filetype = 'csv';
					}
				}
			}

			switch ($filetype) {
				case 'geojson':
					$input_array = json_decode($string);
					foreach ($input_array->features as $feature) {
						$places[] = array_combine(
							$field_names,
							[
								isset($feature->properties->level) ? $feature->properties->level : substr_count(
									$feature->properties->name,
									','
								),
								($feature->geometry->coordinates[0] < 0 ? 'W' : 'E') . abs(
									$feature->geometry->coordinates[0]
								),
								($feature->geometry->coordinates[1] < 0 ? 'S' : 'N') . abs(
									$feature->geometry->coordinates[1]
								),
								isset($feature->properties->zoom) ? $feature->properties->zoom : null,
								isset($feature->properties->icon) ? $feature->properties->icon : null,
								$feature->properties->name,
							]
						);
					}
					break;
				case 'csv':
					foreach ($input_array as $line) {
						$row = explode($delimiter, $line);
						array_walk(
							$row,
							function (&$item) {
								$item = ($item === '') ? null : trim($item, '"\'');
							}
						);
						// convert separate place fields into a comma separated placename
						$row[] = implode(
							Place::GEDCOM_SEPARATOR,
							array_filter(
								array_reverse(
									array_splice($row, 1, $fields - 5)
								)
							)
						);
						$places[] = array_combine($field_names, $row);
					}
					break;
				default:
					//invalid file type
			}

			if ($filetype !== '?') {
				if ((bool)$request->get('cleardatabase')) {
					Database::exec("TRUNCATE TABLE `##placelocation`");
				}
				//process places
				$added   = 0;
				$updated = 0;

				//sort places by level
				usort(
					$places,
					function (array $a, array $b) {
						if ((int)$a['pl_level'] === (int)$b['pl_level']) {
							return I18N::strcasecmp($a['fqpn'], $b['fqpn']);
						} else {
							return (int)$a['pl_level'] - (int)$b['pl_level'];
						}
					}
				);

				foreach ($places as $place) {
					$location = new Location($place['fqpn']);
					$valid    = $location->isValid();

					// can't match data type here because default table values are null
					// but csv file return empty string
					if ($valid && $options !== 'add' && (
							$place['pl_level'] != $location->getLevel() ||
							$place['pl_long'] != $location->getLon('DMS+') ||
							$place['pl_lati'] != $location->getLat('DMS+') ||
							$place['pl_zoom'] != $location->getZoom() ||
							$place['pl_icon'] != $location->getIcon()
						)) {

						// overwrite
						$location->update((object)$place);
						$updated++;
					} elseif (!$valid && $options !== 'update') {
						//add
						$place_parts = explode(Place::GEDCOM_SEPARATOR, $place['fqpn']);
						// work throught the place parts starting at level 0,
						// looking for a record in the database, if not found then add it
						$parent_id = 0;
						for ($i = count($place_parts) - 1; $i >= 0; $i--) {
							$new_parts    = array_slice($place_parts, $i);
							$new_fqpn     = implode(Place::GEDCOM_SEPARATOR, $new_parts);
							$new_location = new Location($new_fqpn,
								[
									'fqpn'         => $new_fqpn,
									'pl_id'        => 0,
									'pl_parent_id' => $parent_id,
									'pl_level'     => count($new_parts) - 1,
									'pl_place'     => $new_parts[0],
									'pl_long'      => $i === 0 ? $place['pl_long'] : null,
									'pl_lati'      => $i === 0 ? $place['pl_lati'] : null,
									'pl_zoom'      => $i === 0 ? $place['pl_zoom'] : null,
									'pl_icon'      => $i === 0 ? $place['pl_icon'] : null,
								]
							);

							if ($new_location->isValid()) {
								$parent_id = $new_location->getId();
							} else {
								$parent_id = $new_location->add();
								$added++;
							}
						}
					}
				}
				Log::addEditLog(I18N::translate("Geographic places upload from %s: updated: %s, added: %s",
					$filename,
					I18N::number($updated),
					I18N::number($added)
				)
				);
				FlashMessages::addMessage(
					I18N::translate(
						'locations updated: %s, locations added: %s',
						I18N::number($updated),
						I18N::number($added)
					),
					$added + $updated === 0 ? 'info' : 'success'
				);
			} else {
				FlashMessages::addMessage(I18N::translate('Unable to detect the file format: %s', $filename), 'danger');
			}
		} else {
			FlashMessages::addMessage(I18N::translate('Unable to open file: %s', $filename), 'danger');
		}

		return new RedirectResponse(
			route(
			'admin-module',
			['module' => $this->getName(), 'action' => 'AdminPlaces', 'inactive' => $inactive]
		)
		);
	}

	/**
	 * @param Request $request
	 * @return RedirectResponse
	 * @throws \Exception
	 */
	public function postAdminImportPlacesAction(Request $request) {
		$gedcomName = $request->get('ged');
		$inactive   = (int)$request->get('inactive');
		$tree       = Tree::findByName($gedcomName);

		// Get all the places from the places table ...
		$places = Database::prepare(
			"
				SELECT
				CONCAT_WS(:separator, t1.p_place, t2.p_place, t3.p_place, t4.p_place, t5.p_place, t6.p_place, t7.p_place, t8.p_place) AS fqpn
				FROM `##places` t1
				LEFT JOIN `##places` t2 ON t1.p_parent_id = t2.p_id
				LEFT JOIN `##places` t3 ON t2.p_parent_id = t3.p_id
				LEFT JOIN `##places` t4 ON t3.p_parent_id = t4.p_id
				LEFT JOIN `##places` t5 ON t4.p_parent_id = t5.p_id
				LEFT JOIN `##places` t6 ON t5.p_parent_id = t6.p_id
				LEFT JOIN `##places` t7 ON t6.p_parent_id = t7.p_id
				LEFT JOIN `##places` t8 ON t7.p_parent_id = t8.p_id
				WHERE t1.p_file = :gedcom
				ORDER BY t1.p_parent_id
			   "
		)->execute(
			[
				'separator' => Place::GEDCOM_SEPARATOR,
				'gedcom'    => $tree->getTreeId(),
			]
		)
			->fetchOneColumn();

		// ... and the placelocation table
		$locations = Database::prepare(
			"
				SELECT
				CONCAT_WS(:separator, t1.pl_place, t2.pl_place, t3.pl_place, t4.pl_place, t5.pl_place, t6.pl_place, t7.pl_place, t8.pl_place) AS fqpn
				FROM `##placelocation` AS t1
				LEFT JOIN `##placelocation` AS t2 ON t1.pl_parent_id = t2.pl_id
				LEFT JOIN `##placelocation` AS t3 ON t2.pl_parent_id = t3.pl_id
				LEFT JOIN `##placelocation` AS t4 ON t3.pl_parent_id = t4.pl_id
				LEFT JOIN `##placelocation` AS t5 ON t4.pl_parent_id = t5.pl_id
				LEFT JOIN `##placelocation` AS t6 ON t5.pl_parent_id = t6.pl_id
				LEFT JOIN `##placelocation` AS t7 ON t6.pl_parent_id = t7.pl_id
				LEFT JOIN `##placelocation` AS t8 ON t8.pl_parent_id = t8.pl_id
				ORDER BY t1.pl_parent_id
			   "
		)->execute(['separator' => Place::GEDCOM_SEPARATOR])
			->fetchOneColumn();

		// Compare the two ...
		$diff = array_diff($places, $locations);
		// ... and process the differences
		if (!empty($diff)) {
			unset($places, $locations);
			$inserted        = 0;
			$nextRecordId    = Database::prepare("SELECT MAX(pl_id)+1 FROM `##placelocation`")->fetchOne();
			$insertRecordQry = Database::prepare(
				"INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place)" .
				" VALUES (:id, :parent, :level, :place)"
			);
			$checkRecordQry = Database::prepare(
				"SELECT pl1.pl_id" .
				" FROM	  `##placelocation` AS pl1" .
				" LEFT JOIN `##placelocation` AS pl2 ON (pl1.pl_parent_id = pl2.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl3 ON (pl2.pl_parent_id = pl3.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl4 ON (pl3.pl_parent_id = pl4.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl5 ON (pl4.pl_parent_id = pl5.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl6 ON (pl5.pl_parent_id = pl6.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl7 ON (pl6.pl_parent_id = pl7.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl8 ON (pl7.pl_parent_id = pl8.pl_id)" .
				" LEFT JOIN `##placelocation` AS pl9 ON (pl8.pl_parent_id = pl9.pl_id)" .
				" WHERE CONCAT_WS(:separator1, pl1.pl_place, pl2.pl_place, pl3.pl_place, pl4.pl_place, pl5.pl_place, pl6.pl_place, pl7.pl_place, pl8.pl_place, pl9.pl_place) LIKE CONCAT('%', :f1, '%')" .
				" AND CONCAT_WS(:separator2, pl1.pl_place, pl2.pl_place, pl3.pl_place, pl4.pl_place, pl5.pl_place, pl6.pl_place, pl7.pl_place, pl8.pl_place, pl9.pl_place) NOT LIKE CONCAT('%,%', :f1, '%')"
			);

			foreach ($diff as $place) {
				$parent_id   = 0;
				$place_parts = array_reverse(explode(Place::GEDCOM_SEPARATOR, $place));
				$search      = '';
				for ($i = 0, $count = count($place_parts); $i < $count; $i++) {
					$place_part = $place_parts[$i];
					$search     = $place_part . Place::GEDCOM_SEPARATOR . $search;
					$search     = trim($search, Place::GEDCOM_SEPARATOR);
					$id         = $checkRecordQry->execute(
						[
							'separator1' => Place::GEDCOM_SEPARATOR,
							'separator2' => Place::GEDCOM_SEPARATOR,
							'f1'         => $search,
							'f2'         => $search,
						]
					)->fetchOne();

					if ($id === null) {
						$inserted++;
						$insertRecordQry->execute(
							[
								'id'     => $nextRecordId++,
								'parent' => $parent_id,
								'level'  => $i,
								'place'  => $place_part,
							]
						);
					} else {
						$parent_id = $id;
					}
				}
			}
			FlashMessages::addMessage(
				I18N::translate(
					'%s Records added. Now use the edit page to add the coordinates etc.',
					$inserted
				),
				'success'
			);
		} else {
			FlashMessages::addMessage(I18N::translate('No Records added.'));
		}

		return new RedirectResponse(
			route(
			'admin-module',
			['module' => $this->getName(), 'action' => 'AdminPlaces', 'inactive' => $inactive]
		)
		);
	}

	/*
	 * Utility Functions
	 */

	/**
	 * Find all of the places in the hierarchy
	 *
	 * @param int $id
	 * @param bool $show_inactive
	 * @return \stdClass[]
	 * @throws \Exception
	 */
	public function getPlaceListLocation($id, $show_inactive = false) {

		$child_qry = Database::prepare(
			"SELECT SQL_CACHE COUNT(*) AS child_count, SUM(" .
			" p1.pl_place IS NOT NULL AND (p1.pl_lati IS NULL OR p1.pl_long IS NULL) OR " .
			" p2.pl_place IS NOT NULL AND (p2.pl_lati IS NULL OR p2.pl_long IS NULL) OR " .
			" p3.pl_place IS NOT NULL AND (p3.pl_lati IS NULL OR p3.pl_long IS NULL) OR " .
			" p4.pl_place IS NOT NULL AND (p4.pl_lati IS NULL OR p4.pl_long IS NULL) OR " .
			" p5.pl_place IS NOT NULL AND (p5.pl_lati IS NULL OR p5.pl_long IS NULL) OR " .
			" p6.pl_place IS NOT NULL AND (p6.pl_lati IS NULL OR p6.pl_long IS NULL) OR " .
			" p7.pl_place IS NOT NULL AND (p7.pl_lati IS NULL OR p7.pl_long IS NULL) OR " .
			" p8.pl_place IS NOT NULL AND (p8.pl_lati IS NULL OR p8.pl_long IS NULL) OR " .
			" p9.pl_place IS NOT NULL AND (p9.pl_lati IS NULL OR p9.pl_long IS NULL)) AS no_coord" .
			" FROM `##placelocation` AS p1" .
			" LEFT JOIN `##placelocation` AS p2 ON (p2.pl_parent_id = p1.pl_id)" .
			" LEFT JOIN `##placelocation` AS p3 ON (p3.pl_parent_id = p2.pl_id)" .
			" LEFT JOIN `##placelocation` AS p4 ON (p4.pl_parent_id = p3.pl_id)" .
			" LEFT JOIN `##placelocation` AS p5 ON (p5.pl_parent_id = p4.pl_id)" .
			" LEFT JOIN `##placelocation` AS p6 ON (p6.pl_parent_id = p5.pl_id)" .
			" LEFT JOIN `##placelocation` AS p7 ON (p7.pl_parent_id = p6.pl_id)" .
			" LEFT JOIN `##placelocation` AS p8 ON (p8.pl_parent_id = p7.pl_id)" .
			" LEFT JOIN `##placelocation` AS p9 ON (p9.pl_parent_id = p8.pl_id)" .
			" WHERE p1.pl_parent_id = :parent_id"
		);

		// We know the id of the place in the placelocation table,
		// now get the id of the same place in the places table
		if ($id === 0) {
			$place_id = 0;
		} else {
			$hierarchy = $this->gethierarchy($id);
			$fqpn      = preg_quote($hierarchy[0]->fqpn);
			$place_id  = Database::prepare(
				"SELECT SQL_CACHE p1.p_id" .
				" FROM      `##places` AS p1" .
				" LEFT JOIN `##places` AS p2 ON (p1.p_parent_id = p2.p_id)" .
				" LEFT JOIN `##places` AS p3 ON (p2.p_parent_id = p3.p_id)" .
				" LEFT JOIN `##places` AS p4 ON (p3.p_parent_id = p4.p_id)" .
				" LEFT JOIN `##places` AS p5 ON (p4.p_parent_id = p5.p_id)" .
				" LEFT JOIN `##places` AS p6 ON (p5.p_parent_id = p6.p_id)" .
				" LEFT JOIN `##places` AS p7 ON (p6.p_parent_id = p7.p_id)" .
				" LEFT JOIN `##places` AS p8 ON (p7.p_parent_id = p8.p_id)" .
				" LEFT JOIN `##places` AS p9 ON (p8.p_parent_id = p9.p_id)" .
				" WHERE CONCAT_WS(', ', p1.p_place, p2.p_place, p3.p_place, p4.p_place, p5.p_place, p6.p_place, p7.p_place, p8.p_place, p9.p_place)=:place_name"
			)->execute(
				[
					'place_name' => $fqpn,
				]
			)->fetchOne();
		}

		// Get the names of the child places from the places table
		// then we'll create a join to the temporary table (used for speed)
		// to determine inactive places
		Database::prepare("DROP TEMPORARY TABLE IF EXISTS `place_subset`")->execute();
		Database::prepare(
			"CREATE TEMPORARY TABLE `place_subset`" .
			" SELECT p_place" .
			" FROM `##places`" .
			" WHERE p_parent_id = :id"
		)
			->execute([
					'id' => $place_id,
				]
			);
		Database::prepare("CREATE INDEX nx1 ON place_subset (p_place)")->execute();

		$rows = Database::prepare(
			"SELECT SQL_CACHE pl_id, pl_parent_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon," .
			" IF(p_place IS NULL, 1, 0) AS inactive" .
			" FROM `##placelocation`" .
			" LEFT JOIN `place_subset` ON pl_place = p_place" .
			" WHERE pl_parent_id=:id" .
			" ORDER BY pl_place COLLATE :collation"
		)
			->execute(
				[
					'id'        => $id,
					'collation' => I18N::collation(),
				]
			)->fetchAll(\PDO::FETCH_ASSOC);

		$list = [];
		/** @var array $rows */
		foreach ($rows as $row) {
			if ((bool)$row['inactive'] && !$show_inactive) {
				continue;
			}

			// Find/count places without co-ordinates
			$children = $child_qry->execute(
				[
					'parent_id' => $row['pl_id'],
				]
			)->fetchOneRow();

			if ((bool)$row['inactive']) {
				$badge = 'danger';
			} elseif ((int)$children->no_coord > 0) {
				$badge = 'warning';
			} elseif ((int)$children->child_count > 0) {
				$badge = 'info';
			} else {
				$badge = 'secondary';
			}

			$list[] = (object)array_merge(
				$row,
				[
					'child_count' => (int)$children->child_count,
					'badge'       => $badge,
				]
			);
		}

		return $list;
	}

	/**
	 * @param $id
	 * @return array
	 * @throws \Exception
	 */
	public function gethierarchy($id) {
		$statement = Database::prepare("SELECT pl_id, pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=:id");
		$arr       = [];
		$fqpn      = [];
		while ($id !== 0) {
			$row       = $statement->execute(['id' => $id])->fetchOneRow();
			$fqpn[]    = $row->pl_place;
			$row->fqpn = implode(Place::GEDCOM_SEPARATOR, $fqpn);
			$id        = (int)$row->pl_parent_id;
			$arr[]     = $row;
		}

		return array_reverse($arr);
	}

	/**
	 * @param $parent_id
	 * @param $placename
	 * @param $places
	 * @throws \Exception
	 */
	private function buildLevel($parent_id, $placename, &$places) {
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

	/**
	 * recursively find all of the files of specified types on the server
	 *
	 * @param string $path
	 * @param string[] $filetypes
	 * @return array
	 */
	private function findFiles($path, $filetypes) {
		$placefiles = [];

		try {
			$di = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
			$it = new \RecursiveIteratorIterator($di);

			foreach ($it as $file) {
				if (in_array($file->getExtension(), $filetypes)) {
					$placefiles[] = $file->getFilename();
				}
			}
		} catch (\Exception $ex) {
			DebugBar::addThrowable($ex);
			Log::addErrorLog(basename($ex->getFile()) . ' - line: ' . $ex->getLine() . ' - ' . $ex->getMessage());
		}

		return $placefiles;
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
	private function sosaStradonitzAncestors(Individual $individual, int $generations): array {
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

return new OpenStreetMapModule(__DIR__);
