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
declare(strict_types = 1);

namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Controller\ChartController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\FactLocation;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Location;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Place;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;

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

	const HERE_REGISTRATION_URL   = "https://developer.here.com/plans/api/consumer-mapping/";
	const MAPBOX_REGISTRATION_URL = "https://www.mapbox.com/studio/";
	const LINE_COLORS             = [
		'#FF0000',
		'#0000FF',
		'#00FF00',
		'#FFCC00',
		'#00FFFF',
		'#FF00FF',
		'#7777FF',
		'#808000'];

	private static $map_providers  = null;
	private static $map_selections = null;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: Name of a module */
			I18N::translate('Open Street Maps');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “OSM” module */
			I18N::translate('Show the location of events on a map');
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
		switch ($mod_action) {
			case 'get_basedata':
				$this->getBaseData();
				break;
			case 'get_mapdata':
				switch (Filter::get('type')) {
					case 'placelist':
						$this->placelistGetMapData();
						break;
					default:
						$this->getMapData();
				}
				break;
			case 'get_provider_styles':
				$this->getProviderStyles();
				break;
			case 'admin_config':
				$this->config();
				break;
			case 'admin_places':
				$this->adminPlaces();
				break;
			case 'admin_place_edit':
				$this->adminPlaceEdit();
				break;
			case 'admin_get_mapdata':
				$this->adminGetMapData();
				break;
			case 'admin_save_record':
				$this->saveRecord();
				break;
			case 'admin_delete_record':
				$this->deleteRecord();
				break;
			case 'admin_import_places':
				$this->importFromPlaces();
				break;
			case 'admin_export':
				$this->adminExport();
				break;
			case 'admin_import':
				$this->adminImportForm();
				break;
			case 'admin_import_action':
				$this->adminImport();
				break;
			default:
				http_response_code(404);
		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		return Html::url(
			'module.php',
			[
				'mod'        => $this->getName(),
				'mod_action' => 'admin_config',
			]
		);
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
			e(route('pedigreemap', ['xref' => $individual->getXref(), 'ged' => $individual->getTree()->getName()])),
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
		$dir        = WT_MODULES_DIR . 'openstreetmap';
		$common_css = [
			$dir . '/packages/leaflet-' . self::LEAFLET . '/leaflet.css',
			$dir . '/assets/css/osm-module.css',
		];
		$common_js = [
			$dir . '/packages/leaflet-' . self::LEAFLET . '/leaflet.js',
			$dir . '/packages/leaflet-providers-' . self::LEAFLET_PROV . '/leaflet-providers.js',
		];

		if ($type === 'admin') {
			$files = [
				'css_files' => array_merge(
					$common_css,
					[
						$dir . '/packages/leaflet-geosearch-' . self::LEAFLET_GEO . '/leaflet-geosearch.css',
					]
				),
				'js_files' => array_merge(
					$common_js,
					[
						$dir . '/assets/js/osm-admin.js',
						$dir . '/packages/leaflet-geosearch-' . self::LEAFLET_GEO . '/leaflet-geosearch.min.js',
					]
				),
			];
		} else {
			$files = [
				'css_files' => array_merge(
					$common_css,
					[
						$dir . '/packages/BeautifyMarker-' . self::LEAFLET_BEAU . '/leaflet-beautify-marker-icon.css',
						$dir . '/packages/Leaflet.markercluster-' . self::LEAFLET_MC . '/dist/MarkerCluster.Default.css',
						$dir . '/packages/Leaflet.markercluster-' . self::LEAFLET_MC . '/dist/MarkerCluster.css',
					]
				),
				'js_files' => array_merge(
					$common_js,
					[
						$dir . '/packages/BeautifyMarker-' . self::LEAFLET_BEAU . '/leaflet-beautify-marker-icon.js',
						$dir . '/packages/leaflet.markercluster-' . self::LEAFLET_MC . '/dist/leaflet.markercluster.js',
						$dir . '/assets/js/osm-module.js',
					]
				),
			];
		}

		return $files;
	}

	/**
	 * @param PageController $controller
	 * @param string $type
	 */
	private function loadAssets(PageController $controller, $type = 'user') {
		$assets = $this->assets($type);

		foreach ($assets['js_files'] as $js_file) {
			$controller->addExternalJavascript($js_file);
		}

		$controller
			->addInlineJavascript(
				'
				var link = document.createElement("link");
				link.setAttribute("rel", "stylesheet");
				link.setAttribute("type", "text/css");
				var tmp;
			'
			);
		foreach ($assets['css_files'] as $css_file) {
			$controller
				->addInlineJavascript(
					'
					tmp = link.cloneNode(true);
					tmp.setAttribute("href", "' . $css_file . '");
					document.head.appendChild(tmp);
				'
				);
		}
	}

	/** {@inheritdoc} */
	public function getTabContent(Individual $individual) {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		return view(
			'modules/openstreetmap/map',
			[
				'assets' => self::assets(),
				'ref'    => $individual->getXref(),
				'tree'   => $individual->getTree()->getTreeId(),
				'type'   => 'individual',
			]
		);
	}

	/**
	 * @param PageController $controller
	 */
    public function insertMap(PageController $controller) {
		$tree   = $controller->tree();
		$parent = Filter::getArray('parent');
		$parent = array_reverse($parent);

		$this->loadAssets($controller);
		$controller->addInlineJavascript(
			sprintf("WT_OSM.drawMap('%s', '%s', 'placelist')", implode(Place::GEDCOM_SEPARATOR, $parent), $tree->getTreeId())
		)
		?>
		<div class="wt-map-tab py-4">
			<div class="gchart osm-wrapper">
				<div id="osm-map" class="wt-ajax-load osm-user-map"></div>
				<div class="osm-sidebar"></div>
			</div>

			<?php if (Auth::isAdmin()): ?>
				<div class="osm-options">
					<a href="<?= e(
						Html::url('module.php', ['mod' => 'openstreetmap', 'mod_action' => 'admin_config'])
					) ?>">
						<?= I18N::translate('Open Street Maps preferences') ?>
					</a>
					|
					<a href="<?= e(Html::url('admin_trees_places.php', ['ged' => $tree->getName()])) ?>">
						<?= I18N::translate('Update place names') ?>
					</a>
				</div>
			<?php endif ?>
		</div>
		<?php
	}

	/**
	 *
	 */
	private function placelistGetMapData() {
		$geojson = ['type' => 'FeatureCollection', 'features' => []];
		$code    = 200;

		//rootid is a misnomer here - it actually contains the gedcomName
		//can't easily change the parameter name without changes to other files
		$rootid = Filter::get('rootid');
		$tree   = Tree::findById(Filter::getInteger('tree'));

		$location = new Location($rootid);

		if ($location->hasChildren()) {
			$places = $location->getChildren();
		} elseif ($location->isValid()) {
			$places[] = $location->getRecord();
		} else {
			$places = [];
		}

		if (empty($places)) {
			$code = 204;
		} else {
			$flag_path = WT_MODULES_DIR . $this->getName() . '/';
			$stats     = new Stats($tree);
			$root_arr  = array_filter(preg_split('/, ?/', $rootid));
			$url_base  = array_reverse($root_arr);
			$html      = "<div>" . I18N::translate('Individuals:') . " %s</div>" .
						 "<div>" . I18N::translate('Families:') . " %s</div>";

			foreach ($places as $id => $place) {
				//Stats
				if ($rootid === '') {
					$ged_place = $place->pl_place;
				} elseif (false === array_search($place->pl_place, $root_arr)) {
					$ged_place = implode(Place::GEDCOM_SEPARATOR, array_merge([$place->pl_place], $root_arr));
				} else {
					$ged_place = implode(Place::GEDCOM_SEPARATOR, $root_arr);
				}

				$pl         = new Place($ged_place, $tree);
				$placeStats = [];
				foreach (['INDI', 'FAM'] as $type) {
					$tmp               = $stats->statsPlaces($type, false, $pl->getPlaceId());
					$placeStats[$type] = empty($tmp) ? 0 : $tmp[0]['tot'];
				}
				//Flag
				if ($place->pl_icon !== null && is_file($flag_path . $place->pl_icon)) {
					$img = "<img src='" . $flag_path . $place->pl_icon . "'>";
				} else {
					$img = '';
				}

				//Url parameter
				$url_parent = array_unique(array_merge($url_base, [$place->pl_place]));
				$url = 'placelist.php?ged=' . $tree->getName();
				foreach($url_parent as $key=>$value) {
				    $url .= '&parent[' . $key .']=' . $value;
                }

				if ($location->hasChildren()) {
					$name = '<div class="osm-name">
							    <a href="' . $url . '">' . $img . ' ' . $place->pl_place  . '</a>
							  </div>';
				} else {
					$name = '<div class="osm-name"><a href="#">' . $img . ' ' . $place->pl_place . '</a></div>';
				}

				$geojson['features'][] = [
					'type'     => 'Feature',
					'id'       => $id,
					'geometry' => [
						'type'        => 'Point',
						'coordinates' => [
							(float) strtr($place->pl_long, ['E' => '', 'W' => '-', ',' => '.']),
							(float) strtr($place->pl_lati, ['N' => '', 'S' => '-', ',' => '.']),
						],
					],
					'properties' => [
						'icon' => [
							'name'  => 'globe',
							'color' => '#1e90ff',
						],
						'tooltip' => $place->pl_place,
						'name'    => $name,
						'summary' => sprintf($html, $placeStats['INDI'], $placeStats['FAM']),
						'zoom'    => (int) $place->pl_zoom,
					],
				];
			}
		}
		$this->ajaxResponse($code, $geojson);
	}

	/**
	 *
	 */
	private function getBaseData() {
		$code     = 200;
		$provider = $this->getMapProviderData(__FUNCTION__);
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
				'noData'       => I18N::translate('No mappable facts exist for this individual'),
				'error'        => I18N::translate('An unknown error occurred'),
			],
		];

		$this->ajaxResponse($code, $options);
	}

	/**
	 *
	 */
	private function getProviderStyles() {
		$code   = 200;
		$styles = $this->getMapProviderData(__FUNCTION__);

		$this->ajaxResponse($code, $styles);
	}

	/**
	 * @throws \Exception
	 */
	private function getMapData() {
		$mapType     = Filter::get('type');
		$xref        = Filter::get('rootid', WT_REGEX_XREF);
		$tree        = Tree::findById(Filter::getInteger('tree'));
		$indi        = Individual::getInstance($xref, $tree);
		$color_count = count(self::LINE_COLORS);

		switch ($mapType) {
			case 'pedigree':
				$facts = $this->getPedigreeMapFacts();
				break;
			default:
				$facts = $this->getPersonalFacts($indi);
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
						$sosa_parent      = (int) floor($id / 2);
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
						'geometry' => [
							'type'        => 'Point',
							'coordinates' => $event->getGeoJsonCoords(),
						],
						'properties' => [
							'polyline' => $polyline,
							'icon'     => $icon,
							'tooltip'  => $event->toolTip(),
							'name'     => $event->getRelativesName($mapType, $id),
							'summary'  => $event->shortSummary(),
							'zoom'     => (int) $event->getZoom(),
						],
					];
				}
			}
		}

		$this->ajaxResponse($code, $geojson);
	}

	/**
	 * @param integer $httpResponse
	 * @param array $payload
	 */
	private function ajaxResponse($httpResponse, $payload) {
		if (!Filter::checkCsrf()) {
			$httpResponse = 404;
		}
		header_remove();
		http_response_code($httpResponse);
		header('Content-Type: application/json');
		echo json_encode($payload, JSON_NUMERIC_CHECK);
	}

	/**
	 * @param Individual $individual
	 * @return array|\Fisharebest\Webtrees\Fact[]
	 */
	private function getPersonalFacts(Individual $individual) {
		$facts = $individual->getFacts();
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
	 *
	 * @return array
	 */
	private function getPedigreeMapFacts() {
		$controller  = new ChartController();
		$tree        = $controller->root->getTree();
		$generations = Filter::get(
			'generations',
			null,
			$tree->getPreference('DEFAULT_PEDIGREE_GENERATIONS')
		);
		$ancestors = $controller->sosaAncestors($generations);

		$facts = [];
		foreach ($ancestors as $sosa => $person) {
			if ($person !== null && $person->canShow()) {
				$birth = $person->getFirstFact('BIRT');
				if ($birth && !$birth->getPlace()->isEmpty()) {
					$facts[$sosa] = $birth;
				}
			}
		}

		return $facts;
	}

	/**
	 * @param string $caller
	 * @return array|null
	 */
	private function getMapProviderData($caller) {
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
						(array) $provider->styles
					);

					$key = preg_replace('/[^a-z\d]/i', '', strtolower((string)$provider->name));

					self::$map_providers[$key] = [
						'name'   => (string) $provider->name,
						'styles' => array_combine($style_keys, (array) $provider->styles),
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
		switch ($caller) {
			case 'getBaseData':
				$varName = (self::$map_selections['style'] === '') ? '' :
					self::$map_providers[self::$map_selections['provider']]['styles'][self::$map_selections['style']];
				$payload = ['selectedProvIndex' => self::$map_selections['provider'],
							'selectedProvName'  => self::$map_providers[self::$map_selections['provider']]['name'],
							'selectedStyleName' => $varName,
				];
				break;
			case 'getProviderStyles':
				$provider = Filter::get('provider', null, 'openstreetmap');
				$payload  = self::$map_providers[$provider]['styles'];
				break;
			case 'config':
				$providers = [];
				foreach (self::$map_providers as $key => $provider) {
					$providers[$key] = $provider['name'];
				}
				$payload = ['providers'     => $providers,
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
	 *
	 */
	private function adminPlaces() {
		$controller = new PageController;
		$tree       = $controller->tree();

		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(I18N::translate('OpenStreetMaps'))
			->pageHeader();

		$parent_id = Filter::getInteger('parent_id', 0);
		$inactive  = Filter::getBool('inactive');
		$placelist = $this->getPlaceListLocation($parent_id, $inactive);
		$icon_path = WT_MODULES_DIR . $this->getName() . '/';
		$hierarchy = $this->gethierarchy($parent_id);

		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			Html::url(
				'module.php',
				[
					'mod'        => $this->getName(),
					'mod_action' => 'admin_places',
					'parent_id'  => 0,
					'inactive'   => $inactive,
				]
			) => $this->getTitle() . ' (' . I18N::translate('Geographic data') . ')',
		];

		foreach ($hierarchy as $row) {
			$breadcrumbs[Html::url('module.php', [
					'mod'        => $this->getName(),
					'mod_action' => 'admin_places',
					'parent_id'  => $row->pl_id,
					'inactive'   => $inactive,
				])] = $row->pl_place;
		}

		$active = array_pop($breadcrumbs);
		echo Bootstrap4::breadcrumbs($breadcrumbs, $active); ?>
		<div class="form-group row">
			<div class="col-sm-3 col-form-label">
				<?= I18N::translate('Module configuration') ?>
			</div>
			<div class="col-sm-9">
				<a class="btn btn-primary" href="<?=
				e(
					Html::url('module.php', [
						'mod'        => $this->getName(),
						'mod_action' => 'admin_config',
					])
				)
				?>">
					<?= FontAwesome::decorativeIcon('edit') ?>
					<?= I18N::translate('edit') ?>
				</a>
			</div>
		</div>
		<form class="form-inline">
			<input type="hidden" name="mod" value="<?= $this->getName() ?>">
			<input type="hidden" name="mod_action" value="admin_places">
			<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
			<?= Bootstrap4::checkbox(
					I18N::translate('Show inactive places'),
				false,
				['name' => 'inactive', 'checked' => $inactive, 'onclick' => 'this.form.submit()']
			) ?>
			<p class="small text-muted">
				<?= I18N::translate(
				'By default, the list shows only those places which can be found in your family trees. You may have details for other places, such as those imported in bulk from an external file. Selecting this option will show all places, including ones that are not currently used.'
				) ?>
				<?= I18N::translate(
					'If you have a large number of inactive places, it can be slow to generate the list.'
				) ?>
			</p>
		</form>

		<table class="table table-bordered table-striped table-sm table-hover">
			<thead class="thead-dark">
			<tr>
				<th><?= I18N::translate('Place') ?></th>
				<th><?= I18N::translate('Latitude') ?></th>
				<th><?= I18N::translate('Longitude') ?></th>
				<th><?= I18N::translate('Zoom level') ?></th>
				<th><?= I18N::translate('Flag') ?> </th>
				<th><?= I18N::translate('Edit') ?></th>
				<th><?= I18N::translate('Delete') ?></th>
			</tr>
			</thead>
			<tbody>

			<?php foreach ($placelist as $place): ?>
				<tr>
					<td>
						<a href="<?=
						e(
							Html::url('module.php', [
								'mod'        => $this->getName(),
								'mod_action' => 'admin_places',
								'parent_id'  => $place->place_id,
								'inactive'   => $inactive,
							])
						)
						?>">
							<?php if ($place->place === 'Unknown'): ?>
								<?= I18N::translate('unknown') ?>
							<?php else: ?>
								<?= e($place->place) ?>
							<?php endif ?>
							<?php if ($place->unused): ?>
								<span class="badge badge-pill badge-danger">
									<?= I18N::translate('unused') ?>
								</span>
							<?php elseif ($place->missing > 0): ?>
								<span class="badge badge-pill badge-warning">
									<?= I18N::number($place->children) ?>
								</span>
							<?php elseif ($place->children === 0): ?>
								<span class="badge badge-pill badge-secondary">
									<?= I18N::number(0) ?>
								</span>
							<?php elseif ($place->children > 0): ?>
								<span class="badge badge-pill badge-info">
									<?= I18N::number($place->children) ?>
								</span>
							<?php endif ?>
						</a>
					</td>
					<td>
						<?= $place->is_empty ? FontAwesome::decorativeIcon('warning') : strtr(
							$place->lati,
							['N' => '', 'S' => '-', ',' => '.']
						) ?>
					</td>
					<td>
						<?= $place->is_empty ? FontAwesome::decorativeIcon('warning') : strtr(
							$place->long,
							['E' => '', 'W' => '-', ',' => '.']
						) ?>
					</td>
					<td>
						<?= $place->zoom ?>
					</td>
					<td>
						<?php if ($place->icon && is_file($icon_path . $place->icon)): ?>
							<img src="<?= $icon_path . e($place->icon) ?>" width="25" height="15"
								 title="<?= e($place->icon) ?>" alt="<?= I18N::translate('Flag') ?>">
						<?php endif ?>
					</td>
					<td>
						<?=
						FontAwesome::linkIcon(
							'edit',
							I18N::translate('Edit'),
							[
								'href' => Html::url('module.php', [
									'mod'        => $this->getName(),
									'mod_action' => 'admin_place_edit',
									'place_id'   => $place->place_id,
									'parent_id'  => $place->parent_id,
								]),
								'class' => 'btn btn-primary']
						)
						?>
					</td>
					<td>
						<?php if ($place->children === 0): ?>
							<form method="POST" action="<?=
							Html::url('module.php', [
									'mod'        => $this->getName(),
									'mod_action' => 'admin_delete_record',
								])
							?>"
								  data-confirm="<?= I18N::translate('Remove this location?') ?>"
								  onsubmit="return confirm(this.dataset.confirm)">
								<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
								<input type="hidden" name="place_id" value="<?= $place->place_id ?>">
								<input type="hidden" name="inactive" value="<?= $inactive ?>">
								<button type="submit" class="btn btn-danger">
									<?= FontAwesome::semanticIcon('delete', I18N::translate('Delete')) ?>
								</button>
							</form>
						<?php else: ?>
							<button type="button" class="btn btn-danger" disabled>
								<?= FontAwesome::decorativeIcon('delete') ?>
							</button>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="7">
                    <a href="<?=
					e(
						Html::url('module.php', [
								'mod'        => $this->getName(),
								'mod_action' => 'admin_place_edit',
								'parent_id'  => $parent_id,
							])
					)
					?>" class="btn btn-primary">
						<?= FontAwesome::decorativeIcon('add') ?>
						<?= /* I18N: A button label. */
						I18N::translate('add') ?>
					</a>
                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<?= FontAwesome::decorativeIcon('download') ?>
						<?= /* I18N: A button label. */
						I18N::translate('export') ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="<?=
							e(
								Html::url('module.php', [
									'mod'        => $this->getName(),
									'mod_action' => 'admin_export',
									'parent_id'  => $parent_id,
									'format'     => 'csv',
								])
							)
						?>">csv
                        </a>
                        <a class="dropdown-item" href="<?=
							e(
								Html::url('module.php', [
									'mod'        => $this->getName(),
									'mod_action' => 'admin_export',
									'parent_id'  => $parent_id,
									'format'     => 'geojson',
								])
							)
						?>">geoJSON
                        </a>
                    </div>
                    <a href="<?=
						e(
							Html::url('module.php', [
								'mod'        => $this->getName(),
								'mod_action' => 'admin_import',
								'parent_id'  => $parent_id,
								'inactive'   => $inactive,
							])
						)
					?>" class="btn btn-primary">
						<?= FontAwesome::decorativeIcon('upload') ?>
						<?= /* I18N: A button label. */
						I18N::translate('import') ?>
                    </a>
				</td>
			</tr>
			</tfoot>
		</table>

		<form action="module.php">
			<input type="hidden" name="mod" value="<?= $this->getName() ?>">
			<input type="hidden" name="mod_action" value="admin_import_places">
			<div class="form-group row">
				<label class="form-control-plaintext col-sm-4" for="ged">
					<?= I18N::translate('Import all places from a family tree') ?>
				</label>
				<div class="col-sm-6">
					<?= Bootstrap4::select(
							Tree::getNameList(),
						$tree->getName(),
						['id' => 'ged', 'name' => 'ged']
					) ?>
				</div>
				<div class="col-sm-2">
					<button type="submit" class="btn btn-primary">
						<?= FontAwesome::decorativeIcon('add') ?>
						<?= /* I18N: A button label. */
						I18N::translate('import') ?>
					</button>
				</div>
			</div>
		</form>

		<?php
	}

	/**
	 * Create or edit a geographic place.
	 */
	private function adminPlaceEdit() {
		$parent_id = (int) Filter::post('parent_id', null, Filter::get('parent_id'));
		$place_id  = (int) Filter::post('place_id', null, Filter::get('place_id'));
		$inactive  = (int) Filter::post('inactive', null, Filter::get('inactive'));
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
		$breadcrumb_hierarchy = $this->gethierarchy($id);

		$controller = new PageController;
		$this->loadAssets($controller, 'admin');
		$controller
			->setPageTitle(I18N::translate('Geographic data'))
			->pageHeader()
			->addInlineJavascript('WT_OSM_ADMIN.drawMap(' . $id . ');');

		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			Html::url('module.php', [
					'mod'        => $this->getName(),
					'mod_action' => 'admin_places',
					'parent_id'  => 0,
					'inactive'   => $inactive,
				]) => $this->getTitle() . ' (' . I18N::translate('Geographic data') . ')',
		];
		foreach ($breadcrumb_hierarchy as $row) {
			$breadcrumbs[Html::url('module.php', [
					'mod'        => $this->getName(),
					'mod_action' => 'admin_places',
					'parent_id'  => $row->pl_id,
					'inactive'   => $inactive,
				])] = $row->pl_place;
		}

		echo Bootstrap4::breadcrumbs($breadcrumbs, $place_id === 0 ? I18N::translate('Add') : I18N::translate('Edit')); ?>

		<div class="form-group row">
			<div class="col-sm-10 offset-sm-1">
				<div id="osm-map" class="wt-ajax-load osm-admin-map"></div>
			</div>
		</div>

		<form method="post" id="editplaces" name="editplaces"
			  action="<?=
				  Html::url('module.php', [
						  'mod'        => $this->getName(),
						  'mod_action' => 'admin_save_record'
					  ])
				  ?>">
			<input type="hidden" name="place_id" value="<?= $place_id ?>">
			<input type="hidden" name="level" value="<?= count($hierarchy) ?>">
			<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
			<input type="hidden" name="place_long" value="<?= $lng ?>">
			<input type="hidden" name="place_lati" value="<?= $lat ?>">

			<div class="form-group row">
				<label class="col-form-label col-sm-1" for="new_place_name">
					<?= I18N::translate('Place') ?>
				</label>
				<div class="col-sm-5">
					<input type="text" id="new_place_name" name="new_place_name" value="<?= e($location->getPlace()) ?>"
						   class="form-control" required>
				</div>
				<label class="col-form-label col-sm-1" for="icon">
					<?= I18N::translate('Flag') ?>
				</label>
				<div class="col-sm-4">
					<div class="input-group" dir="ltr">
						<?= FunctionsEdit::formControlFlag(
							$location->getIcon(),
							['name' => 'icon', 'id' => 'icon', 'class' => 'form-control']
						)
					    ?>
					</div>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-form-label col-sm-1">
					<?= I18N::translate('Latitude') ?>
				</label>
				<div class="col-sm-3">
					<div class="input-group">
						<input type="text" id="new_place_lati" class="editable form-control" name="new_place_lati" required
							   placeholder="<?= I18N::translate('degrees') ?>" value="<?= $lat ?>"
                        >
					</div>
				</div>

				<label class="col-form-label col-sm-1">
					<?= I18N::translate('Longitude') ?>
				</label>
				<div class="col-sm-3">
					<div class="input-group">
						<input type="text" id="new_place_long" class="editable form-control" name="new_place_long" required
							   placeholder="<?= I18N::translate('degrees') ?>" value="<?= $lng ?>"
                        >
					</div>
				</div>
				<label class="col-form-label col-sm-1" for="new_zoom_factor">
					<?= I18N::translate('Zoom') ?>
				</label>
				<div class="col-sm-2">
					<input type="text" id="new_zoom_factor" name="new_zoom_factor" value="<?= $location->getZoom() ?>"
						   class="form-control" required readonly>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-sm-10 offset-sm-1">
					<button class="btn btn-primary" type="submit">
						<?= /* I18N: A button label. */
						I18N::translate('save')
						?>
					</button>
					<a class="btn btn-secondary" href="<?=
					e(
						Html::url('module.php', [
							'mod'        => $this->getName(),
							'mod_action' => 'admin_places',
							'parent_id'  => $parent_id,
							'inactive'   => $inactive,
						])
					)
					?>">
						<?= /* I18N: A button label. */
						I18N::translate('cancel')
						?>
					</a>
				</div>
			</div>
		</form>
		<?php
	}

	/**
	 * @throws \Exception
	 */
	private function adminGetMapData() {
		$code = 200;
		$id   = Filter::get('id', 0);
		$row  = Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id = :id")
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

		$this->ajaxResponse($code, $json);
	}

	/**
	 * @throws \Exception
	 */
	private function saveRecord() {
		if (Filter::checkCsrf()) {
			$parent_id = Filter::postInteger('parent_id');
			$place_id  = Filter::postInteger('place_id');
			$inactive  = Filter::postInteger('inactive');
			$lat       = round(Filter::post('new_place_lati'), 5); // 5 decimal places (locate to within about 1 metre)
			$lat       = ($lat < 0 ? 'S' : 'N') . abs($lat);
			$lng       = round(Filter::post('new_place_long'), 5);
			$lng       = ($lng < 0 ? 'W' : 'E') . abs($lng);
			$hierarchy = $this->gethierarchy($parent_id);
			$level     = count($hierarchy);
			$icon      = Filter::post('icon', null, null);
			$icon      = $icon === '' ? null : $icon;
			$zoom      = Filter::post('new_zoom_factor');
			$zoom      = $zoom      = '' ? null : $zoom;


			if ($place_id === 0) {
				Database::prepare(
					"INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon)
						  VALUES (:id, :parent, :level, :place, :lng, :lat, :zoom, :icon)"
				)->execute([
						'id'     => (int) Database::prepare("SELECT MAX(pl_id)+1 FROM `##placelocation`")->fetchOne(),
						'parent' => $parent_id,
						'level'  => $level,
						'place'  => Filter::post('new_place_name'),
						'lat'    => Filter::post('lati_control') . $lat,
						'lng'    => Filter::post('long_control') . $lng,
						'zoom'   => $zoom,
						'icon'   => $icon,
				]);
			} else {
				Database::prepare(
					"UPDATE `##placelocation` SET pl_place = :place, pl_lati = :lat, pl_long = :lng, pl_zoom = :zoom, pl_icon = :icon WHERE pl_id = :id"
				)->execute([
						'id'    => $place_id,
						'place' => Filter::post('new_place_name'),
						'lat'   => Filter::post('lati_control') . $lat,
						'lng'   => Filter::post('long_control') . $lng,
						'zoom'  => Filter::postInteger('new_zoom_factor'),
						'icon'  => $icon,
					]);
			}
			header(
				'Location:' . Html::url('module.php', [
						'mod'        => $this->getName(),
						'mod_action' => 'admin_places',
						'parent_id'  => $parent_id,
						'inactive'   => $inactive,
					])
			);
		} else {
			http_response_code(403);
		}
	}

	/**
	 * Delete a geographic place.
	 */
	private function deleteRecord() {
		if (Filter::checkCsrf()) {
			$place_id  = (int) Filter::post('place_id');
			$parent_id = (int) Filter::post('parent_id');
			$inactive  = (int) Filter::post('inactive');

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
			$children = (int) Database::prepare(
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
			header(
				'Location:' . Html::url('module.php', [
						'mod'        => $this->getName(),
						'mod_action' => 'admin_places',
						'parent_id'  => $parent_id,
						'inactive'   => $inactive,
					])
			);
		} else {
			http_response_code(403);
		}
	}

	/**
	 * @throws \Exception
	 */
	private function importFromPlaces() {
		$gedcomName = Filter::get('ged');
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
		header(
			'Location:' . Html::url('module.php', [
				'mod'        => $this->getName(),
				'mod_action' => 'admin_places',
				])
		);
	}

	/**
	 * A form to edit the module configuration.
	 */
	private function config() {
		$controller = new PageController();
		$controller
			->setPageTitle($this->getTitle())
			->addInlineJavascript(
				"
				'use strict';
				let domSelect = $('#provider');
				$(function() {
					if($('#mapbox_id').val().length === 0 || !$('#mapbox_token').length === 0) {
						domSelect.children('option[value=\"mapbox\"]').attr('disabled', 'disabled');
					}
					if($('#here_appid').val().length === 0 || !$('#here_appcode').length === 0) {
						domSelect.children('option[value=\"here\"]').attr('disabled', 'disabled');
					}
				});
				domSelect.change(function () {
					let newProvider = this.value;
					$.getJSON('module.php', {
						mod         : '" . $this->getName() . "',
						mod_action  : 'get_provider_styles',
						provider    : newProvider,
					})
                    .done(function (data, textStatus, jqXHR) {
                        let html = '';
                        Object.keys(data).forEach(function(key) {
                            html += '<option value=' + key +'>' + data[key] + '</option>';
                        });
                        $('#provider_style').html(html);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR, textStatus, errorThrown);
                    })
				});
			"
			);

		if (Filter::post('action') === 'update') {
			$this->setPreference('mapbox_id', Filter::post('mapbox_id'));
			$this->setPreference('mapbox_token', Filter::post('mapbox_token'));
			$this->setPreference('here_appid', Filter::post('here_appid'));
			$this->setPreference('here_appcode', Filter::post('here_appcode'));
			$this->setPreference('provider', Filter::post('provider'));
			$this->setPreference('provider_style', Filter::post('provider_style'));
			$this->setPreference('place_hierarchy', Filter::post('place_hierarchy'));
			$this->setPreference('map_animate', Filter::post('map_animate'));

			FlashMessages::addMessage(I18N::translate(
				'The preferences for the module “%s” have been updated.',
				$this->getTitle()
			), 'success');
			header(
				'Location:' . Html::url('module.php', [
						'mod'        => $this->getName(),
						'mod_action' => 'admin_config',
					])
			);

			return;
		}

		$controller->pageHeader();

		echo Bootstrap4::breadcrumbs(
			[
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
		],
			$controller->getPageTitle() . ' (' . I18N::translate('Configuration') . ')'
		);

		$provider     = $this->getMapProviderData(__FUNCTION__);
		$mapboxId     = $this->getPreference('mapbox_id');
		$mapboxToken  = $this->getPreference('mapbox_token');
		$here_Appid   = $this->getPreference('here_appid');
		$here_Appcode = $this->getPreference('here_appcode');
		$hierarchy    = $this->getPreference('place_hierarchy', '0');
		$animate      = $this->getPreference('map_animate', '0'); ?>

		<form method="post" name="configform" action="<?=
		Html::url('module.php', [
				'mod'        => $this->getName(),
				'mod_action' => 'admin_config',
			])
		?>">
			<input type="hidden" name="action" value="update">

			<div class="form-group row">
				<div class="col-sm-3 col-form-label">
					<?= I18N::translate('Geographic data') ?>
				</div>
				<div class="col-sm-9">
					<a class="btn btn-primary"
					   href="module.php?mod=<?= $this->getName() ?>&amp;mod_action=admin_places">
						<?= FontAwesome::decorativeIcon('edit') ?>
						<?= I18N::translate('edit') ?>
					</a>
				</div>
			</div>

			<h4><?= I18N::translate('General') ?></h4>

            <!-- Providers -->
            <fieldset class="form-group">
                <div class="form-row">
                    <legend class="col-form-label col-sm-3">
						<?= I18N::translate('Map provider and style') ?>
                    </legend>
                    <div class="col-sm-9">
                        <div class="form-row">
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="provider">
											<?= I18N::translate('Provider') ?>
                                        </label>
                                    </div>
									<?= Bootstrap4::select(
										$provider['providers'],
										$provider['selectedProv'],
										['id' => 'provider', 'name' => 'provider']
									) ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="provider_style">
											<?= I18N::translate('Style') ?>
                                        </label>
                                    </div>
									<?= Bootstrap4::select(
										$provider['styles'],
										$provider['selectedStyle'],
										['id' => 'provider_style', 'name' => 'provider_style']
									) ?>
                                </div>
                            </div>
                        </div>
                        <p class="small text-muted"><?= I18N::translate('Select map provider and style') ?></p>
                    </div>
                </div>
            </fieldset>

            <!-- Animate map reset-->
            <fieldset class="form-group">
                <div class="form-row">
                    <legend class="col-form-label col-sm-3">
						<?= I18N::translate('Animate map reset') ?>
                    </legend>
                    <div class="col-sm-9">
                        <div class="form-row">
                            <div class="col-sm-4">
                                <div class="input-group">
									<?= Bootstrap4::radioButtons(
										'map_animate',
										[I18N::translate('no'), I18N::translate('yes')],
										$animate,
										true
									) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </Div>
            </fieldset>
            
			<!-- Place Hierarchy -->
            <fieldset class="form-group">
                <div class="form-row">
                    <legend class="col-form-label col-sm-3">
						<?= I18N::translate('Show a map on the place hierarchy') ?>
                    </legend>
                    <div class="col-sm-9">
                        <div class="form-row">
                            <div class="col-sm-4">
                                <div class="input-group">
									<?= Bootstrap4::radioButtons(
										'place_hierarchy',
										[I18N::translate('no'), I18N::translate('yes')],
										$hierarchy,
										true
									) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </Div>
            </fieldset>

			<h4><?= I18N::translate('Provider credentials') ?></h4>

			<!-- Mapbox -->
			<fieldset class="form-group">
				<div class="form-row">
					<legend class="col-form-label col-sm-3">
						Mapbox
					</legend>
					<div class="col-sm-9">
						<div class="form-row">
							<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-prepend">
										<label class="input-group-text" for="mapbox_id">
											<?= I18N::translate('Id') ?>
										</label>
									</div>
									<input id="mapbox_id" class="form-control" type="text" name="mapbox_id"
										   value="<?= $mapboxId ?>">
								</div>
							</div>
							<div class="col-sm-9">
								<div class="input-group">
									<div class="input-group-prepend">
										<label class="input-group-text" for="mapbox_token">
											<?= I18N::translate('Token') ?>
										</label>
									</div>
									<input id="mapbox_token" class="form-control" type="text"
										   name="mapbox_token"
										   value="<?= $mapboxToken ?>">
								</div>
							</div>
						</div>
						<p class="small text-muted"><?= I18N::translate(
										'Mapbox&copy; requires that you register and obtain an ID and token before the service can be used'
							) ?>
							<a href=<?= self::MAPBOX_REGISTRATION_URL ?>><?= I18N::translate('Get Mapbox codes') ?></a>
						</p>
					</div>
				</div>
			</fieldset>


			<!-- HERE -->
			<fieldset class="form-group">
				<div class="form-row">
					<legend class="col-form-label col-sm-3">
						HERE WeGo
					</legend>
					<div class="col-sm-9">
						<div class="form-row">
							<div class="col-sm-6">
								<div class="input-group">
									<div class="input-group-prepend">
										<label class="input-group-text" for="here_appid">
											<?= I18N::translate('App Id') ?>
										</label>
									</div>
									<input id="here_appid" class="form-control" type="text" name="here_appid"
										   value="<?= $here_Appid ?>">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="input-group">
									<div class="input-group-prepend">
										<label class="input-group-text" for="here_appcode">
											<?= I18N::translate('App Code') ?>
										</label>
									</div>
									<input id="here_appcode" class="form-control" type="text"
										   name="here_appcode"
										   value="<?= $here_Appcode ?>">
								</div>
							</div>
						</div>
						<p class="small text-muted"><?= I18N::translate(
								'HERE WeGo&copy; requires that you register and obtain both an App ID and an App Code before the service can be used'
							) ?>
							<a href=<?= self::HERE_REGISTRATION_URL ?>><?= I18N::translate('Get HERE WeGo codes') ?></a>
						</p>
					</div>
				</div>
			</fieldset>

			<!-- SAVE BUTTON -->
			<div class="form-group row">
				<div class="offset-sm-3 col-sm-9">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-check"></i>
						<?= I18N::translate('save') ?>
					</button>
				</div>
			</div>
		</form>
		<?php
	}

	/**
	 * @throws \Exception
	 */
	private function adminExport() {
		$parent_id = (int) Filter::get('parent_id');
		$format    = Filter::get('format', null, 'csv');
		$maxlevel  = (int) Database::prepare("SELECT max(pl_level) FROM `##placelocation`")->execute()->fetchOne();
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
					$long = (float) strtr($place['pl_long'], ['E' => '', 'W' => '-', ',' => '.']);
					$lati = (float) strtr($place['pl_lati'], ['N' => '', 'S' => '-', ',' => '.']);

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
	}

	/**
	 * @param $parent_id
	 * @param $placename
	 * @param $places
	 * @throws \Exception
	 */
	private function buildLevel($parent_id, $placename, &$places) {
		$level = array_search('', $placename);
		$rows  = (array) Database::prepare(
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
	 * Show a form with options to upload a CSV file
	 */
	private function adminImportForm() {
		$parent_id = (int) Filter::get('parent_id');
		$inactive  = (int) Filter::get('inactive');

		$controller = new PageController;
		$controller
			->setPageTitle(I18N::translate('Import geographic data'))
			->pageHeader()
			->addInlineJavascript("
				$('#upload_form').on('submit', function(e) {
					let self = this;
					e.preventDefault();
					if($('input[name=\"cleardatabase\"]:checked').val() === '1') {
						if (!confirm('" . I18N::translate('Really delete all geographic data?') . "')) {
						   return false;
						}
					}
					self.submit();
				});
			");

		echo Bootstrap4::breadcrumbs(
			[
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			Html::url('module.php', [
					'mod'        => $this->getName(),
					'mod_action' => 'admin_places',
					'parent_id'  => 0,
					'inactive'   => $inactive,
				]) => $this->getTitle() . ' (' . I18N::translate('Geographic data') . ')'
		],
			I18N::translate('Upload file')
		);

		$files = $this->findFiles(WT_MODULES_DIR . $this->getName() . '/extra', ['csv', 'geojson', 'json']);
		uasort(
			$files,
			function ($a, $b) {
				$la = strlen($a);
				$lb = strlen($b);
				return $la === $lb ? I18N::strcasecmp($a, $b) : $la - $lb;
			}
		);

		//Can't use Bootstrap4::select because we want a disabled placeholder option
		$options = "<option class='custom-select' selected disabled value=''>" . I18N::translate('choose a file&hellip;') . "</option>";
		foreach ($files as $file) {
			$options .= sprintf('<option value="%1$s">%1$s</option>', e(trim($file, '/')));
		} ?>
		<h2><?= $controller->getPageTitle() ?></h2>

		<form id="upload_form" method="post" enctype="multipart/form-data" action="<?=
		Html::url('module.php', [
				'mod'        => $this->getName(),
				'mod_action' => 'admin_import_action',
			])
		?>">
			<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
			<input type="hidden" name="inactive" value="<?= $inactive ?>">
			<input type="hidden" name="MAX_FILE_SIZE" value="131072">

			<!-- Server file -->
			<div class="row form-group">
				<label class="col-form-label col-sm-4" for="serverfile">
					<?= I18N::translate('A file on the server') ?>
				</label>
				<div class="col-sm-8">
					<div class="input-group" dir="ltr">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<?= WT_MODULES_DIR . $this->getName() . '/extra/' ?>
							</span>
						</div>
						<select id="serverfile" name="serverfile" class="form-control">
							<?= $options ?>
						</select>
					</div>
				</div>
			</div>

			<!-- local file -->
			<div class="row form-group">
				<label class="col-form-label col-sm-4" for="localfile">
					<?= I18N::translate('A file on your computer') ?>
				</label>
				<div class="col-sm-8">
					<input id="localfile" type="file" name="localfile" class="form-control-file">
				</div>
			</div>

			<!-- CLEAR DATABASE -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-4">
						<?= I18N::translate('Delete all existing geographic data before importing the file.') ?>
					</legend>
					<div class="col-sm-8">
						<?= Bootstrap4::radioButtons(
			'cleardatabase',
							[I18N::translate('no'), I18N::translate('yes')],
							'0',
							true
						) ?>
					 </div>
				</div>
			</fieldset>

			<!-- Import options -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-4" for="import-options">
						<?= I18N::translate('Import Options.') ?>
					</legend>
					<div class="col-sm-8">
						<?= Bootstrap4::select(
							[
								'addupdate' => I18N::translate('Add new, and update existing records'),
								'add'       => I18N::translate('Only add new records'),
								'update'    => I18N::translate('Only update existing records'),
							],
							'0',
							['id' => 'import-options', 'name' => 'import-options']
						) ?>
					</div>
				</div>
			</fieldset>

			<!-- SAVE BUTTON -->
			<div class="row form-group">
				<div class="offset-sm-4 col-sm-8">
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-check"></i>
						<?= I18N::translate('continue') ?>
					</button>
				</div>
			</div>
		</form>
		<?php
	}

	/**
	 * This function assumes the input file layout is
	 * level followed by a variable number of placename fields
	 * followed by Longitude, Latitude, Zoom & Icon
	 */
	private function adminImport() {
		$serverfile  = Filter::post('serverfile');
		$options     = Filter::post('import-options');
		$filename    = '';
		$places      = [];
		$input_array = [];
		$fields      = 0;
		$delimiter   = '';
		$field_names = ['level', 'longitude', 'latitude', 'zoom', 'icon', 'fqpn'];

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
						(bool) preg_match("/[SN][0-9]*\.?[0-9]*/", $row[$fields - 3]) &&
						(bool) preg_match("/[EW][0-9]*\.?[0-9]*/", $row[$fields - 4])) {
						$filetype = 'csv';
					}
				}
			}

			switch ($filetype) {
				case 'geojson':
					$input_array = json_decode($string);
					foreach ($input_array->features as $feature) {
						$places[] = array_combine($field_names, [
							isset($feature->properties->level) ? $feature->properties->level : substr_count($feature->properties->name, ','),
							($feature->geometry->coordinates[0] < 0 ? 'W' : 'E') . abs(
								$feature->geometry->coordinates[0]
							),
							($feature->geometry->coordinates[1] < 0 ? 'S' : 'N') . abs(
								$feature->geometry->coordinates[1]
							),
							isset($feature->properties->zoom) ? $feature->properties->zoom : null,
							isset($feature->properties->icon) ? $feature->properties->icon : null,
							$feature->properties->name,
						]);
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
				if (Filter::postBool('cleardatabase')) {
					Database::exec("TRUNCATE TABLE `##placelocation`");
				}
				//process places
				$added   = 0;
				$updated = 0;

				//sort places by level - guarantees parent exists when adding a place
				usort(
					$places,
					function (array $a, array $b) {
						if ((int) $a['level'] === (int) $b['level']) {
							return I18N::strcasecmp($a['fqpn'], $b['fqpn']);
						} else {
							return (int)$a['level'] - (int)$b['level'];
						}
					}
				);

				foreach ($places as $place) {
					$location = new Location($place['fqpn']);
					$valid    = $location->isValid();

					// can't match data type here because default table values are null
					// but csv file return empty string
					if ($valid && $options !== 'add' && (
							$place['level'] != $location->getLevel() ||
							$place['longitude'] != $location->getLon('DMS+') ||
							$place['latitude'] != $location->getLat('DMS+') ||
							$place['zoom'] != $location->getZoom() ||
							$place['icon'] != $location->getIcon()
						)) {
						// overwrite
						$location->update($place);
						$updated++;
						Log::addEditLog(I18N::translate("Geographic places upload: %s updated", $place['fqpn']));
					} elseif (!$valid && $options !== 'update') {
						//add
						$location->add($place);
						$added++;
						Log::addEditLog(I18N::translate("Geographic places upload: %s added", $place['fqpn']));
					}
				}
				$status = ($added + $updated === 0) ? 'info' : 'success';
				FlashMessages::addMessage(
					I18N::translate(
						'locations updated: %s, locations added: %s',
						I18N::number($updated),
						I18N::number($added)
					),
					$status
				);
			} else {
				FlashMessages::addMessage(I18N::translate('Unable to detect the file format: %s', $filename), 'danger');
			}
		} else {
			FlashMessages::addMessage(I18N::translate('Unable to open file: %s', $filename), 'danger');
		}
		header(
			'Location:' . Html::url(
				'module.php',
				[
					'mod'        => $this->getName(),
					'mod_action' => 'admin_places',
				]
			)
		);
	}

	/**
	 * Find all of the places in the hierarchy
	 *
	 * NOTE: the "inactive" filter ignores the hierarchy, so that "Paris, France"
	 * will match "Paris, Texas, United States".  A fully accurate match would be slow.
	 * @param $parent_id
	 * @param bool $inactive
	 * @return array
	 * @throws \Exception
	 */
	private function getPlaceListLocation($parent_id, $inactive = false) {
		$rows = Database::prepare(
			"SELECT DISTINCT pl_id, pl_parent_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon, 0 as pl_unused" .
			" FROM `##placelocation`" .
			" JOIN `##places` ON `##placelocation`.pl_place = `##places`.p_place" .
			" WHERE pl_parent_id = :parent_id" .
			" ORDER BY pl_place COLLATE :collation"
		)->execute(
			[
			'parent_id' => $parent_id,
			'collation' => I18N::collation(),
		]
		)->fetchAll();

		if ($inactive) {
			$all = Database::prepare(
				"SELECT pl_id, pl_parent_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon, 1 AS pl_unused" .
				" FROM `##placelocation`" .
				" WHERE pl_parent_id = :parent_id" .
				" ORDER BY pl_place COLLATE :collation"
			)->execute(
				[
				'parent_id' => $parent_id,
				'collation' => I18N::collation(),
			]
			)->fetchAll();

			$unused = array_udiff(
				$all,
				$rows,
				function (\stdClass $a, \stdClass $b) {
					return I18N::strcasecmp($a->pl_place, $b->pl_place);
				}
			);

			$rows = array_merge($rows, $unused);

			uasort(
				$rows,
				function (\stdClass $a, \stdClass $b) {
					return I18N::strcasecmp($a->pl_place, $b->pl_place);
				}
			);
		}

		$placelist = [];
		foreach ($rows as $row) {
			// Find/count places without co-ordinates
			$children = Database::prepare(
				"SELECT SQL_CACHE COUNT(*) AS total, SUM(" .
				" p1.pl_place IS NOT NULL AND IFNULL(p1.pl_lati, '') IN ('N0', '') AND IFNULL(p1.pl_long, '') IN ('E0', '') OR " .
				" p2.pl_place IS NOT NULL AND IFNULL(p2.pl_lati, '') IN ('N0', '') AND IFNULL(p2.pl_long, '') IN ('E0', '') OR " .
				" p3.pl_place IS NOT NULL AND IFNULL(p3.pl_lati, '') IN ('N0', '') AND IFNULL(p3.pl_long, '') IN ('E0', '') OR " .
				" p4.pl_place IS NOT NULL AND IFNULL(p4.pl_lati, '') IN ('N0', '') AND IFNULL(p4.pl_long, '') IN ('E0', '') OR " .
				" p5.pl_place IS NOT NULL AND IFNULL(p5.pl_lati, '') IN ('N0', '') AND IFNULL(p5.pl_long, '') IN ('E0', '') OR " .
				" p6.pl_place IS NOT NULL AND IFNULL(p6.pl_lati, '') IN ('N0', '') AND IFNULL(p6.pl_long, '') IN ('E0', '') OR " .
				" p7.pl_place IS NOT NULL AND IFNULL(p7.pl_lati, '') IN ('N0', '') AND IFNULL(p7.pl_long, '') IN ('E0', '') OR " .
				" p8.pl_place IS NOT NULL AND IFNULL(p8.pl_lati, '') IN ('N0', '') AND IFNULL(p8.pl_long, '') IN ('E0', '') OR " .
				" p9.pl_place IS NOT NULL AND IFNULL(p9.pl_lati, '') IN ('N0', '') AND IFNULL(p9.pl_long, '') IN ('E0', '')) AS missing" .
				" FROM	  `##placelocation` AS p1" .
				" LEFT JOIN `##placelocation` AS p2 ON (p2.pl_parent_id = p1.pl_id)" .
				" LEFT JOIN `##placelocation` AS p3 ON (p3.pl_parent_id = p2.pl_id)" .
				" LEFT JOIN `##placelocation` AS p4 ON (p4.pl_parent_id = p3.pl_id)" .
				" LEFT JOIN `##placelocation` AS p5 ON (p5.pl_parent_id = p4.pl_id)" .
				" LEFT JOIN `##placelocation` AS p6 ON (p6.pl_parent_id = p5.pl_id)" .
				" LEFT JOIN `##placelocation` AS p7 ON (p7.pl_parent_id = p6.pl_id)" .
				" LEFT JOIN `##placelocation` AS p8 ON (p8.pl_parent_id = p7.pl_id)" .
				" LEFT JOIN `##placelocation` AS p9 ON (p9.pl_parent_id = p8.pl_id)" .
				" WHERE p1.pl_parent_id = :parent_id"
			)
				->execute(
					[
					'parent_id' => $row->pl_id,
				]
				)->fetchOneRow();

			$placelist[] = (object) [
				'place_id'  => (int) $row->pl_id,
				'parent_id' => (int) $row->pl_parent_id,
				'place'     => $row->pl_place,
				'lati'      => $row->pl_lati,
				'long'      => $row->pl_long,
				'zoom'      => (int) $row->pl_zoom,
				'icon'      => $row->pl_icon,
				'is_empty'  => ($row->pl_lati === null || $row->pl_lati === 'N0') && ($row->pl_long === null || $row->pl_long === 'E0'),
				'children'  => (int) $children->total,
				'missing'   => (int) $children->missing,
				'unused'    => (bool) $row->pl_unused,
			];
		}

		return $placelist;
	}

	/**
	 * @param $id
	 * @return array
	 * @throws \Exception
	 */
	private function gethierarchy($id) {
		$statement = Database::prepare("SELECT pl_id, pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=:id");
		$arr       = [];
		$fqpn      = [];
		while ($id !== 0) {
			$row       = $statement->execute(['id' => $id])->fetchOneRow();
			$fqpn[]    = $row->pl_place;
			$row->fqpn = implode(Place::GEDCOM_SEPARATOR, $fqpn);
			$id        = (int) $row->pl_parent_id;
			$arr[]     = $row;
		}

		return array_reverse($arr);
	}

	/**
	 * recursively find all of the csv files on the server
	 *
	 * @param string $path
	 * @param array $filetypes
	 * @return array
	 */
	private function findFiles($path, $filetypes) {
		$placefiles = [];

		try {
			$di = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
			$it = new \RecursiveIteratorIterator($di);

			foreach ($it as $file) {
				if (in_array($file->getExtension(), $filetypes)) {
					$placefiles[] = '/' . $file->getFilename();
				}
			}
		} catch (\Exception $ex) {
			DebugBar::addThrowable($ex);
			Log::addErrorLog(basename($ex->getFile()) . ' - line: ' . $ex->getLine() . ' - ' . $ex->getMessage());
		}

		return $placefiles;
	}
}
return new OpenStreetMapModule(__DIR__);
