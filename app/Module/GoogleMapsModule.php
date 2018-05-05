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
namespace Fisharebest\Webtrees\Module;

use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Bootstrap4;
use Fisharebest\Webtrees\Controller\ChartController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Database;
use Fisharebest\Webtrees\DebugBar;
use Fisharebest\Webtrees\Fact;
use Fisharebest\Webtrees\Family;
use Fisharebest\Webtrees\Filter;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\FontAwesome;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsCharts;
use Fisharebest\Webtrees\Functions\FunctionsEdit;
use Fisharebest\Webtrees\Html;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Log;
use Fisharebest\Webtrees\Menu;
use Fisharebest\Webtrees\Module;
use Fisharebest\Webtrees\Session;
use Fisharebest\Webtrees\Stats;
use Fisharebest\Webtrees\Tree;
use PDO;
use stdClass;

/**
 * Class GoogleMapsModule
 *
 * @link http://www.google.com/permissions/guidelines.html
 *
 * "... an unregistered Google Brand Feature should be followed by
 * the superscripted letters TM or SM ..."
 *
 * Hence, use "Google Maps™"
 *
 * "... Use the trademark only as an adjective"
 *
 * "... Use a generic term following the trademark, for example:
 * GOOGLE search engine, Google search"
 *
 * Hence, use "Google Maps™ mapping service" where appropriate.
 */
class GoogleMapsModule extends AbstractModule implements ModuleConfigInterface, ModuleTabInterface, ModuleChartInterface {
	// How to update the database schema for this module
	const SCHEMA_TARGET_VERSION   = 6;
	const SCHEMA_SETTING_NAME     = 'GM_SCHEMA_VERSION';
	const SCHEMA_MIGRATION_PREFIX = '\Fisharebest\Webtrees\Module\GoogleMaps\Schema';

	const GM_MIN_ZOOM_MINIMUM = 1;
	const GM_MIN_ZOOM_DEFAULT = 2;
	const GM_MIN_ZOOM_MAXIMUM = 14;

	const GM_MAX_ZOOM_MINIMUM = 1;
	const GM_MAX_ZOOM_DEFAULT = 15;
	const GM_MAX_ZOOM_MAXIMUM = 20;

	/** @var Individual[] of ancestors of root person */
	private $ancestors = [];

	/** @var int Number of nodes in the chart */
	private $treesize;

	/** {@inheritdoc} */
	public function getTitle() {
		return /* I18N: The name of a module. Google Maps™ is a trademark. Do not translate it? http://en.wikipedia.org/wiki/Google_maps */ I18N::translate('Google Maps™');
	}

	/** {@inheritdoc} */
	public function getDescription() {
		return /* I18N: Description of the “Google Maps™” module */ I18N::translate('Show the location of places and events using the Google Maps™ mapping service.');
	}

	/**
	 * This is a general purpose hook, allowing modules to respond to routes
	 * of the form module.php?mod=FOO&mod_action=BAR
	 *
	 * @param string $mod_action
	 */
	public function modAction($mod_action) {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		// Some actions ard admin-only.
		if (strpos($mod_action, 'admin') === 0 && !Auth::isAdmin()) {
			header('Location: index.php');

			return;
		}

		switch ($mod_action) {
			case 'admin_config':
				$this->config();
				break;
			case 'pedigree_map':
				$this->pedigreeMap();
				break;
			case 'admin_places':
				$this->adminPlaces();
				break;
			case 'admin_place_edit':
				$this->adminPlaceEdit();
				break;
			case 'admin_place_save':
				$this->adminPlaceSave();
				break;
			case 'admin_download':
				$this->adminDownload();
				break;
			case 'admin_upload':
				$this->adminUploadForm();
				break;
			case 'admin_upload_action':
				$this->adminUploadAction();
				break;
			case 'admin_import_action':
				$this->adminImportAction();
				break;
			case 'admin_delete_action':
				$this->adminDeleteAction();
				break;
			default:
				http_response_code(404);
				break;
		}
	}

	/** {@inheritdoc} */
	public function getConfigLink() {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		return Html::url('module.php', [
			'mod'        => $this->getName(),
			'mod_action' => 'admin_config',
		]);
	}

	/** {@inheritdoc} */
	public function defaultTabOrder() {
		return 80;
	}

	/** {@inheritdoc} */
	public function canLoadAjax() {
		return true;
	}

	/** {@inheritdoc} */
	public function getTabContent(Individual $individual) {
		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		if ($this->checkMapData($individual)) {
			// This call can return an empty string if no facts with map co-ordinates exist
			$map_data = $this->buildIndividualMap($individual);
		} else {
			$map_data = '';
		}

		return view('tabs/map', [
			'google_map_css'       => WT_MODULES_DIR . 'googlemap/css/wt_v3_googlemap.css',
			'google_map_js'        => $this->googleMapsScript(),
			'individual'           => $individual,
			'is_admin'             => Auth::isAdmin(),
			'map_data'             => $map_data,
		]);
	}

	/** {@inheritdoc} */
	public function hasTabContent(Individual $individual) {
		return Module::getModuleByName('googlemap') || Auth::isAdmin();
	}

	/** {@inheritdoc} */
	public function isGrayedOut(Individual $individual) {
		return false;
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
			I18N::translate('Pedigree map'),
			'module.php?mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=' . $individual->getXref() . '&amp;ged=' . $individual->getTree()->getNameUrl(),
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
	 * A form to edit the module configuration.
	 */
	private function config() {
		$controller = new PageController;
		$controller->setPageTitle(I18N::translate('Google Maps™'));

		if (Filter::post('action') === 'update') {
			$this->setPreference('GM_API_KEY', Filter::post('GM_API_KEY'));
			$this->setPreference('GM_MIN_ZOOM', Filter::post('GM_MIN_ZOOM'));
			$this->setPreference('GM_MAX_ZOOM', Filter::post('GM_MAX_ZOOM'));
			$this->setPreference('GM_PLACE_HIERARCHY', Filter::post('GM_PLACE_HIERARCHY'));
			$this->setPreference('GM_PH_MARKER', Filter::post('GM_PH_MARKER'));
			$this->setPreference('GM_PREFIX_1', Filter::post('GM_PREFIX_1'));
			$this->setPreference('GM_PREFIX_2', Filter::post('GM_PREFIX_2'));
			$this->setPreference('GM_PREFIX_3', Filter::post('GM_PREFIX_3'));
			$this->setPreference('GM_PREFIX_4', Filter::post('GM_PREFIX_4'));
			$this->setPreference('GM_PREFIX_5', Filter::post('GM_PREFIX_5'));
			$this->setPreference('GM_PREFIX_6', Filter::post('GM_PREFIX_6'));
			$this->setPreference('GM_PREFIX_7', Filter::post('GM_PREFIX_7'));
			$this->setPreference('GM_PREFIX_8', Filter::post('GM_PREFIX_8'));
			$this->setPreference('GM_PREFIX_9', Filter::post('GM_PREFIX_9'));
			$this->setPreference('GM_POSTFIX_1', Filter::post('GM_POSTFIX_1'));
			$this->setPreference('GM_POSTFIX_2', Filter::post('GM_POSTFIX_2'));
			$this->setPreference('GM_POSTFIX_3', Filter::post('GM_POSTFIX_3'));
			$this->setPreference('GM_POSTFIX_4', Filter::post('GM_POSTFIX_4'));
			$this->setPreference('GM_POSTFIX_5', Filter::post('GM_POSTFIX_5'));
			$this->setPreference('GM_POSTFIX_6', Filter::post('GM_POSTFIX_6'));
			$this->setPreference('GM_POSTFIX_7', Filter::post('GM_POSTFIX_7'));
			$this->setPreference('GM_POSTFIX_8', Filter::post('GM_POSTFIX_8'));
			$this->setPreference('GM_POSTFIX_9', Filter::post('GM_POSTFIX_9'));

			FlashMessages::addMessage(I18N::translate('The preferences for the module “%s” have been updated.', $this->getTitle()), 'success');
			header('Location: module.php?mod=googlemap&mod_action=admin_config');

			return;
		}

		$controller->pageHeader();

		echo Bootstrap4::breadcrumbs([
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
		], $controller->getPageTitle());
		?>

		<h2><?= I18N::translate('Google Maps™ preferences') ?></h2>

		<form class="form-horizontal" method="post" name="configform" action="module.php?mod=googlemap&mod_action=admin_config">
			<input type="hidden" name="action" value="update">

			<div class="row form-group">
				<div class="col-sm-3 col-form-label">
					<?= I18N::translate('Geographic data') ?>
				</div>
				<div class="col-sm-9">
					<a class="btn btn-primary" href="module.php?mod=googlemap&amp;mod_action=admin_places">
						<?= FontAwesome::decorativeIcon('edit') ?>
						<?= I18N::translate('edit') ?>
					</a>
				</div>
			</div>

			<!-- GM_API_KEY -->
			<div class="row form-group">
				<label class="col-sm-3 col-form-label" for="GM_API_KEY">
					<?= /* I18N: https://en.wikipedia.org/wiki/API_key */ I18N::translate('API key') ?>
				</label>
				<div class="col-sm-9">
					<input id="GM_API_KEY" class="form-control" type="text" name="GM_API_KEY" value="<?= $this->getPreference('GM_API_KEY') ?>">
					<p class="small text-muted"><?= I18N::translate('Google allows a small number of anonymous map requests per day. If you need more than this, you will need a Google account and an API key.') ?>
						<a href="https://developers.google.com/maps/documentation/javascript/get-api-key">
							<?= /* I18N: https://en.wikipedia.org/wiki/API_key */ I18N::translate('Get an API key from Google.') ?>
						</a>
					</p>
				</div>
			</div>

			<!-- GM_MIN_ZOOM / GM_MAX_ZOOM -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-3">
						<?= I18N::translate('Zoom level of map') ?>
					</legend>
					<div class="col-sm-9">
						<div class="row">
							<div class="col-sm-6">
								<div class="input-group">
									<div class="input-group-prepend">
									<label class="input-group-text" for="GM_MIN_ZOOM">
									<?= I18N::translate('minimum') ?>
									</label>
									</div>
									<?= Bootstrap4::select(array_combine(range(self::GM_MIN_ZOOM_MINIMUM, self::GM_MIN_ZOOM_MAXIMUM), range(self::GM_MIN_ZOOM_MINIMUM, self::GM_MIN_ZOOM_MAXIMUM)), $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT), ['id' => 'GM_MIN_ZOOM', 'name' => 'GM_MIN_ZOOM']) ?>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="input-group">
									<div class="input-group-prepend">
									<label class="input-group-text" for="GM_MAX_ZOOM">
									<?= I18N::translate('maximum') ?>
									</label>
									</div>
									<?= Bootstrap4::select(array_combine(range(self::GM_MAX_ZOOM_MINIMUM, self::GM_MAX_ZOOM_MAXIMUM), range(self::GM_MAX_ZOOM_MINIMUM, self::GM_MAX_ZOOM_MAXIMUM)), $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT), ['id' => 'GM_MAX_ZOOM', 'name' => 'GM_MAX_ZOOM']) ?>
								</div>
							</div>
						</div>
						<p class="small text-muted"><?= I18N::translate('Minimum and maximum zoom level for the Google map. 1 is the full map, 15 is single house. Note that 15 is only available in certain areas.') ?></p>
					</div>
				</div>
			</fieldset>

			<!-- GM_PREFIX / GM_POSTFIX -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-3">
						<?= I18N::translate('Optional prefixes and suffixes') ?>
					</legend>
					<div class="col-sm-9">
						<div class="row">
							<div class ="col-sm-6">
								<p class="form-control-static"><strong><?= I18N::translate('Prefixes') ?></strong></p>
								<?php for ($level = 1; $level < 10; $level++): ?>
								<?php
								if ($level == 1) {
									$label = I18N::translate('Country');
								} else {
									$label = I18N::translate('Level') . ' ' . $level;
								}
								?>
								<div class="input-group">
									<div class="input-group-prepend">
									<label class="input-group-text" for="GM_PREFIX_<?= $level ?>">
									<?= $label ?>
									</label>
									</div>
									<input class="form-control" type="text" name="GM_PREFIX_<?= $level ?>" value="<?= $this->getPreference('GM_PREFIX_' . $level) ?>">
								</div>
								<?php endfor ?>
							</div>
							<div class="col-sm-6">
								<p class="form-control-static"><strong><?= I18N::translate('Suffixes') ?></strong></p>
								<?php for ($level = 1; $level < 10; $level++): ?>
								<?php
								if ($level == 1) {
									$label = I18N::translate('Country');
								} else {
									$label = I18N::translate('Level') . ' ' . $level;
								}
								?>
								<div class="input-group">
									<div class="input-group-prepend">
									<label class="input-group-text" for="GM_POSTFIX_<?= $level ?>">
									<?= $label ?>
									</label>
									</div>
									<input class="form-control" type="text" name="GM_POSTFIX_<?= $level ?>" value="<?= $this->getPreference('GM_POSTFIX_' . $level) ?>">
								</div>
								<?php endfor ?>
							</div>
						</div>
						<p class="small text-muted"><?= I18N::translate('Some place names may be written with optional prefixes and suffixes. For example “Orange” versus “Orange County”. If the family tree contains the full place names, but the geographic database contains the short place names, then you should specify a list of the prefixes and suffixes to be disregarded. Multiple values should be separated with semicolons. For example “County;County of” or “Township;Twp;Twp.”.') ?></p>
					</div>
				</div>
			</fieldset>

			<h3><?= I18N::translate('Place hierarchy') ?></h3>

			<!-- GM_PLACE_HIERARCHY -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-3">
						<?= I18N::translate('Use Google Maps™ for the place hierarchy') ?>
					</legend>
					<div class="col-sm-9">
						<?= Bootstrap4::radioButtons('GM_PLACE_HIERARCHY', [I18N::translate('no'), I18N::translate('yes')], $this->getPreference('GM_PLACE_HIERARCHY', '0'), true) ?>
					</div>
				</div>
			</fieldset>

			<!-- GM_PH_MARKER -->
			<div class="row form-group">
				<label class="col-sm-3 col-form-label" for="GM_PH_MARKER">
					<?= I18N::translate('Type of place markers in the place hierarchy') ?>
				</label>
				<div class="col-sm-9">
					<?php
					echo Bootstrap4::select(['G_DEFAULT_ICON' => I18N::translate('Standard'), 'G_FLAG' => I18N::translate('Flag')], $this->getPreference('GM_PH_MARKER'), ['id' => 'GM_PH_MARKER', 'name' => 'GM_PH_MARKER']);
					?>
				</div>
			</div>

			<!-- SAVE BUTTON -->
			<div class="row form-group">
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
	 * Google Maps API script
	 *
	 * @return string
	 */
	private function googleMapsScript() {
		$key = $this->getPreference('GM_API_KEY');

		return 'https://maps.googleapis.com/maps/api/js?v=3&amp;key=' . $key . '&amp;language=' . WT_LOCALE;
	}

	/**
	 * Display a map showing the origins of ones ancestors.
	 */
	private function pedigreeMap() {
		global $controller, $WT_TREE;

		$controller = new ChartController();
		$controller->restrictAccess(Module::isActiveChart($WT_TREE, 'googlemap'));

		// Limit this to match available number of icons.
		// 8 generations equals 255 individuals
		$MAX_PEDIGREE_GENERATIONS = $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS');
		$MAX_PEDIGREE_GENERATIONS = min($MAX_PEDIGREE_GENERATIONS, 8);
		$generations              = Filter::getInteger('PEDIGREE_GENERATIONS', 2, $MAX_PEDIGREE_GENERATIONS, $WT_TREE->getPreference('DEFAULT_PEDIGREE_GENERATIONS'));
		$this->treesize           = pow(2, $generations) - 1;
		$this->ancestors          = array_values($controller->sosaAncestors($generations));

		// Only generate the content for interactive users (not search robots).
		if (Filter::getBool('ajax') && Session::has('initiated')) {
			// count records by type
			$curgen   = 1;
			$priv     = 0;
			$count    = 0;
			$miscount = 0;
			$missing  = [];

			$latlongval = [];
			$lat        = [];
			$lon        = [];
			for ($i = 0; $i < ($this->treesize); $i++) {
				// -- check to see if we have moved to the next generation
				if ($i + 1 >= pow(2, $curgen)) {
					$curgen++;
				}
				$person = $this->ancestors[$i];
				if (!empty($person)) {
					$name = $person->getFullName();
					if ($name == I18N::translate('Private')) {
						$priv++;
					}
					$place = $person->getBirthPlace()->getGedcomName();
					if (empty($place)) {
						$latlongval[$i] = null;
					} else {
						$latlongval[$i] = $this->getLatitudeAndLongitudeFromPlaceLocation($person->getBirthPlace()->getGedcomName());
					}
					if ($latlongval[$i]) {
						$lat[$i] = strtr($latlongval[$i]->pl_lati, ['N' => '', 'S' => '-', ',' => '.']);
						$lon[$i] = strtr($latlongval[$i]->pl_long, ['N' => '', 'S' => '-', ',' => '.']);
						if ($lat[$i] && $lon[$i]) {
							$count++;
						} else {
							// The place is in the table but has empty values
							if ($name) {
								$missing[] = '<a href="' . e($person->url()) . '">' . $name . '</a>';
								$miscount++;
							}
						}
					} else {
						// There was no place, or not listed in the map table
						if ($name) {
							$missing[] = '<a href="' . e($person->url()) . '">' . $name . '</a>';
							$miscount++;
						}
					}
				}
			}

			//<!-- end of count records by type -->
			//<!-- start of map display -->
			echo '<div class="gm-pedigree-map">';
			echo '<div class="gm-wrapper">';
			echo '<div class="gm-map wt-ajax-load"></div>';
			echo '<div class="gm-ancestors"></div>';
			echo '</div>';

			if (Auth::isAdmin()) {
				echo '<div class="gm-options">';
				echo '<a href="module.php?mod=' . $this->getName() . '&amp;mod_action=admin_config">' . I18N::translate('Google Maps™ preferences') . '</a>';
				echo ' | <a href="module.php?mod=' . $this->getName() . '&amp;mod_action=admin_places">' . I18N::translate('Geographic data') . '</a>';
				echo '</div>';
			}
			// display info under map
			echo '<hr>';

			// print summary statistics
			if (isset($curgen)) {
				$total = pow(2, $curgen) - 1;
				echo '<div>';
				echo I18N::plural(
					'%1$s individual displayed, out of the normal total of %2$s, from %3$s generations.',
					'%1$s individuals displayed, out of the normal total of %2$s, from %3$s generations.',
					$count,
					I18N::number($count), I18N::number($total), I18N::number($curgen)
				);
				echo '</div>';
				if ($priv) {
					echo '<div>' . I18N::plural('%s individual is private.', '%s individuals are private.', $priv, $priv), '</div>';
				}
				if ($count + $priv != $total) {
					if ($miscount == 0) {
						echo '<div>' . I18N::translate('No ancestors in the database.'), '</div>';
					} else {
						echo '<div>' . /* I18N: %1$s is a count of individuals, %2$s is a list of their names */ I18N::plural(
								'%1$s individual is missing birthplace map coordinates: %2$s.',
								'%1$s individuals are missing birthplace map coordinates: %2$s.',
								$miscount, I18N::number($miscount), implode(I18N::$list_separator, $missing)),
						'</div>';
					}
				}
			}

			echo '</div>';
			echo '</div>';
			?>
			<script>
				function initialiZePedigreeMap() {
					// this variable will collect the html which will eventually be placed in the side bar
					var gm_ancestors_html = "";
					// arrays to hold copies of the markers and html used by the side bar
					// because the function closure trick doesnt work there
					var gmarkers = [];
					var index = 0;
					var lastlinkid;
					var infowindow = new google.maps.InfoWindow({});
					// === Create an associative array of GIcons()
					var gicons = [];
					gicons["1"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon1.png"
					};
					gicons["2"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon2.png"
					};
					gicons["2L"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon2L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["2R"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon2R.png",
						size:  new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["2Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon2Ls.png",
						size:  new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["2Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon2Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					gicons["3"]   = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon3.png"
					};
					gicons["3L"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon3L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["3R"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon3R.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["3Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon3Ls.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["3Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon3Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					gicons["4"]   = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon4.png"
					};
					gicons["4L"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon4L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["4R"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon4R.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["4Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon4Ls.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["4Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon4Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					gicons["5"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon5.png"
					};
					gicons["5L"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon5L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["5R"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon5R.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["5Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon5Ls.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["5Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon5Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					gicons["6"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon6.png"
					};
					gicons["6L"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon6L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["6R"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon6R.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["6Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon6Ls.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["6Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon6Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					gicons["7"]   = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon7.png"
					};
					gicons["7L"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon7L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["7R"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon7R.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["7Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon7Ls.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["7Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon7Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					gicons["8"]   = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon8.png"
					};
					gicons["8L"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon8L.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(28, 28)
					};
					gicons["8R"]  = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon8R.png",
						size: new google.maps.Size(32, 32),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(4, 28)
					};
					gicons["8Ls"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon8Ls.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(22, 22)
					};
					gicons["8Rs"] = {
						url: "<?= WT_MODULES_DIR ?>googlemap/images/icon8Rs.png",
						size: new google.maps.Size(24, 24),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(2, 22)
					};
					// / A function to create the marker and set up the event window
					function createMarker(point, name, html, mhtml, icontype) {
						// Create a marker with the requested icon
						var marker = new google.maps.Marker({
							icon:     gicons[icontype],
							map:      pm_map,
							position: point,
							id:       index,
							zIndex:   0
						});
						google.maps.event.addListener(marker, "click", function() {
							infowindow.close();
							infowindow.setContent(mhtml);
							infowindow.open(pm_map, marker);
							var el = $(".gm-ancestor[data-marker=" + marker.id + "]");
							if(el.hasClass("person_box")) {
								el
									.removeClass("person_box")
									.addClass("gm-ancestor-visited");
								infowindow.close();
							} else {
								el
									.addClass("person_box")
									.removeClass("gm-ancestor-visited");
							}
							var anchor = infowindow.getAnchor();
							lastlinkid = anchor ? anchor.id : null;
						});
						// save the info we need to use later for the side bar
						gmarkers[index] = marker;
						gm_ancestors_html += "<div data-marker =" + index++ + " class=\"gm-ancestor\">" + html +"</div>";

						return marker;
					}
					// create the map
					var myOptions = {
					  gestureHandling:          'cooperative',
						zoom:                     6,
						minZoom:                  <?= $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) ?>,
						maxZoom:                  <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>,
						center:                   new google.maps.LatLng(0, 0),
						mapTypeId:                google.maps.MapTypeId.TERRAIN,  // ROADMAP, SATELLITE, HYBRID, TERRAIN
						mapTypeControlOptions:    {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
						},
						navigationControlOptions: {
							position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
							style:    google.maps.NavigationControlStyle.SMALL   // ANDROID, DEFAULT, SMALL, ZOOM_PAN
						},
						scrollwheel:              true
					};
					var pm_map = new google.maps.Map(document.querySelector(".gm-map"), myOptions);
					google.maps.event.addListener(pm_map, "click", function() {
						$(".gm-ancestor.person_box")
							.removeClass("person_box")
							.addClass("gm-ancestor-visited");
						infowindow.close();
						lastlinkid = null;
					});
					// create the map bounds
					var bounds = new google.maps.LatLngBounds();
					<?php
					// add the points
					$curgen       = 1;
					$count        = 0;
					$colored_line = [
						'1' => '#FF0000',
						'2' => '#0000FF',
						'3' => '#00FF00',
						'4' => '#FFFF00',
						'5' => '#00FFFF',
						'6' => '#FF00FF',
						'7' => '#C0C0FF',
						'8' => '#808000',
					];
					$lat        = [];
					$lon        = [];
					$latlongval = [];
					for ($i = 0; $i < $this->treesize; $i++) {
						// moved up to grab the sex of the individuals
						$person = $this->ancestors[$i];
						if ($person) {
							$name = $person->getFullName();

							// -- check to see if we have moved to the next generation
							if ($i + 1 >= pow(2, $curgen)) {
								$curgen++;
							}

							$relationship = FunctionsCharts::getSosaName($i + 1);

							// get thumbnail image
							if ($person->getTree()->getPreference('SHOW_HIGHLIGHT_IMAGES')) {
								$image = $person->displayImage(40, 50, 'crop', []);
							} else {
								$image = '';
							}

							$event = '<img src="' . WT_MODULES_DIR . 'googlemap/images/sq' . $curgen . '.png" width="10" height="10"> ';
							$event .= '<strong>' . $relationship . '</strong>';

							$birth = $person->getFirstFact('BIRT');
							$data  = addslashes($image . '<div class="gm-ancestor-link">' . $event . ' <span><a href="' . e($person->url()) . '">' . $name . '</a></span>');
							$data .= $birth ? addslashes($birth->summary()) : '';
							$data .= '</div>';

							$latlongval[$i] = $this->getLatitudeAndLongitudeFromPlaceLocation($person->getBirthPlace()->getGedcomName());
							if ($latlongval[$i]) {
								$lat[$i] = (float) strtr($latlongval[$i]->pl_lati, ['N' => '', 'S' => '-', ',' => '.']);
								$lon[$i] = (float) strtr($latlongval[$i]->pl_long, ['E' => '', 'W' => '-', ',' => '.']);
								if ($lat[$i] || $lon[$i]) {
									$marker_number = $curgen;
									$dups          = 0;
									for ($k = 0; $k < $i; $k++) {
										if ($latlongval[$i] == $latlongval[$k]) {
											$dups++;
											switch ($dups) {
												case 1:
													$marker_number = $curgen . 'L';
													break;
												case 2:
													$marker_number = $curgen . 'R';
													break;
												case 3:
													$marker_number = $curgen . 'Ls';
													break;
												case 4:
													$marker_number = $curgen . 'Rs';
													break;
												case 5: //adjust position where markers have same coodinates
												default:
													$marker_number = $curgen;
													$lon[$i] += 0.0025;
													$lat[$i] += 0.0025;
													break;
											}
										}
									}

									?>
									var point = new google.maps.LatLng(<?= $lat[$i] ?>, <?= $lon[$i] ?>);
									var marker = createMarker(point, "<?= addslashes($name) ?>", "<?= $data ?>", "<div class=\"gm-info-window\"><?= $data ?></div>", "<?= $marker_number ?>");
									<?php
									// Construct the polygon lines
									$to_child = (intval(($i - 1) / 2)); // Draw a line from parent to child
									if (array_key_exists($to_child, $lat) && $lat[$to_child] != 0 && $lon[$to_child] != 0) {
										?>
										var linecolor;
										var plines;
										var lines = [
											new google.maps.LatLng(<?= $lat[$i] ?>, <?= $lon[$i] ?>),
											new google.maps.LatLng(<?= $lat[$to_child] ?>, <?= $lon[$to_child] ?>)
										];
										linecolor = "<?= $colored_line[$curgen] ?>";
										plines = new google.maps.Polygon({
											paths: lines,
											strokeColor: linecolor,
											strokeOpacity: 0.8,
											strokeWeight: 3,
											fillColor: "#FF0000",
											fillOpacity: 0.1
										});
										plines.setMap(pm_map);
										<?php
									}
									// Extend and fit marker bounds

									?>
									bounds.extend(point);
									<?php
									$count++;
								}
							}
						} else {
							$latlongval[$i] = null;
						}
					}
					?>
					pm_map.setCenter(bounds.getCenter());
					pm_map.fitBounds(bounds);
					google.maps.event.addListenerOnce(pm_map, "bounds_changed", function(event) {
						var maxZoom = <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>;
						if (this.getZoom() > maxZoom) {
							this.setZoom(maxZoom);
						}
					});

					// Close the sidebar highlight when the infowindow is closed
					google.maps.event.addListener(infowindow, "closeclick", function() {
						$(".gm-ancestor[data-marker=" + lastlinkid + "]").toggleClass("gm-ancestor-visited person_box");
						lastlinkid = null;
					});
					// put the assembled gm_ancestors_html contents into the gm-ancestors div
					document.querySelector(".gm-ancestors").innerHTML = gm_ancestors_html;

					$(".gm-ancestor-link")
						.on("click", "a", function(e) {
							e.stopPropagation();
						})
						.on("click", function(e) {
							if (lastlinkid !== null) {
								$(".gm-ancestor[data-marker=" + lastlinkid + "]").toggleClass("person_box gm-ancestor-visited");
							}
							var target = $(this).closest(".gm-ancestor").data("marker");
							google.maps.event.trigger(gmarkers[target], "click");
						});
				}
			</script>
			<script src="<?= $this->googleMapsScript() ?>&amp;callback=initialiZePedigreeMap"></script>
			<?php

			return;
		}

		$controller
			->setPageTitle(/* I18N: %s is an individual’s name */ I18N::translate('Pedigree map of %s', $controller->root->getFullName()))
			/* prepending the module css in the page head allows the theme to over-ride it*/
			->addInlineJavascript('$("head").prepend(\'<link type="text/css" href ="' . WT_MODULES_DIR . 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">\');')
			->pageHeader();
?>

	<div id="pedigreemap-page">
		<h2><?= $controller->getPageTitle() ?></h2>

		<form class="wt-page-options wt-page-options-pedigree-map d-print-none">
			<input type="hidden" name="ged" value="<?= e($WT_TREE->getName()) ?>">
			<input type="hidden" name="mod" value="googlemap">
			<input type="hidden" name="mod_action" value="pedigree_map">

			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label" for="rootid">
					<?= I18N::translate('Individual') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value">
					<?= FunctionsEdit::formControlIndividual($WT_TREE, $controller->root, ['id' => 'rootid', 'name' => 'rootid']) ?>
				</div>
			</div>

			<div class="row form-group">
				<label class="col-sm-3 col-form-label wt-page-options-label" for="PEDIGREE_GENERATIONS">
					<?= I18N::translate('Generations') ?>
				</label>
				<div class="col-sm-9 wt-page-options-value">
					<?= Bootstrap4::select(FunctionsEdit::numericOptions(range(2, $WT_TREE->getPreference('MAX_PEDIGREE_GENERATIONS'))), $generations, ['id' => 'PEDIGREE_GENERATIONS', 'name' => 'PEDIGREE_GENERATIONS']) ?>
				</div>
			</div>

			<div class="row form-group">
				<div class="col-sm-3 wt-page-options-label"></div>
				<div class="col-sm-9 wt-page-options-value">
					<input class="btn btn-primary" type="submit" value="<?= /* I18N: A button label. */ I18N::translate('view') ?>">
				</div>
			</div>
		</form>

		<div class="wt-ajax-load wt-page-content"></div>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
		    $(".wt-page-content").load(location.search + "&ajax=1");
			});
		</script>
		<?php
	}

	/**
	 * Does an individual (or their spouse-families) have any facts with places?
	 *
	 * @param Individual $individual
	 *
	 * @return bool
	 */
	private function checkMapData(Individual $individual) {
		$statement = Database::prepare(
			"SELECT COUNT(*) FROM `##placelinks` WHERE pl_gid = :xref AND pl_file = :tree_id"
		);
		$args = [
			'xref'    => $individual->getXref(),
			'tree_id' => $individual->getTree()->getTreeId(),
		];

		if ($statement->execute($args)->fetchOne()) {
			return true;
		}

		foreach ($individual->getSpouseFamilies() as $family) {
			$args['xref'] = $family->getXref();
			if ($statement->execute($args)->fetchOne()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Remove prefixes from a place name to allow it to be matched.
	 *
	 * @param string   $prefix_list
	 * @param string   $place
	 * @param string[] $placelist
	 *
	 * @return string[]
	 */
	private function removePrefixFromPlaceName($prefix_list, $place, $placelist) {
		if ($prefix_list) {
			foreach (explode(';', $prefix_list) as $prefix) {
				if ($prefix && substr($place, 0, strlen($prefix) + 1) == $prefix . ' ') {
					$placelist[] = substr($place, strlen($prefix) + 1);
				}
			}
		}

		return $placelist;
	}

	/**
	 * Remove suffixes from a place name to allow it to be matched.
	 *
	 * @param string   $suffix_list
	 * @param string   $place
	 * @param string[] $placelist
	 *
	 * @return string[]
	 */
	private function removeSuffixFromPlaceName($suffix_list, $place, $placelist) {
		if ($suffix_list) {
			foreach (explode(';', $suffix_list) as $postfix) {
				if ($postfix && substr($place, -strlen($postfix) - 1) == ' ' . $postfix) {
					$placelist[] = substr($place, 0, strlen($place) - strlen($postfix) - 1);
				}
			}
		}

		return $placelist;
	}

	/**
	 * Remove prefixes and sufixes to allow place names to be matched.
	 *
	 * @param string   $prefix_list
	 * @param string   $suffix_list
	 * @param string   $place
	 * @param string[] $placelist
	 *
	 * @return string[]
	 */
	private function removePrefixAndSuffixFromPlaceName($prefix_list, $suffix_list, $place, $placelist) {
		if ($prefix_list && $suffix_list) {
			foreach (explode(';', $prefix_list) as $prefix) {
				foreach (explode(';', $suffix_list) as $postfix) {
					if ($prefix && $postfix && substr($place, 0, strlen($prefix) + 1) == $prefix . ' ' && substr($place, -strlen($postfix) - 1) == ' ' . $postfix) {
						$placelist[] = substr($place, strlen($prefix) + 1, strlen($place) - strlen($prefix) - strlen($postfix) - 2);
					}
				}
			}
		}

		return $placelist;
	}

	/**
	 * Match placenames with different prefixes and suffixes.
	 *
	 * @param string $placename
	 * @param int    $level
	 *
	 * @return string[]
	 */
	private function createPossiblePlaceNames($placename, $level) {
		$retlist = [];
		if ($level <= 9) {
			$retlist = $this->removePrefixAndSuffixFromPlaceName($this->getPreference('GM_PREFIX_' . $level), $this->getPreference('GM_POSTFIX_' . $level), $placename, $retlist); // Remove both
			$retlist = $this->removePrefixFromPlaceName($this->getPreference('GM_PREFIX_' . $level), $placename, $retlist); // Remove prefix
			$retlist = $this->removeSuffixFromPlaceName($this->getPreference('GM_POSTFIX_' . $level), $placename, $retlist); // Remove suffix
		}
		$retlist[] = $placename; // Exact

		return $retlist;
	}

	/**
	 * Get the map co-ordinates of a place.
	 *
	 * @param string $place
	 *
	 * @return null|\stdClass
	 */
	private function getLatitudeAndLongitudeFromPlaceLocation($place) {
		$parent     = explode(',', $place);
		$parent     = array_reverse($parent);
		$place_id   = 0;
		$num_parent = count($parent);
		for ($i = 0; $i < $num_parent; $i++) {
			$parent[$i] = trim($parent[$i]);
			if (empty($parent[$i])) {
				$parent[$i] = 'unknown'; // GoogleMap module uses "unknown" while GEDCOM uses , ,
			}
			$placelist = $this->createPossiblePlaceNames($parent[$i], $i + 1);
			foreach ($placelist as $placename) {
				$pl_id = Database::prepare(
					"SELECT pl_id FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place"
				)->execute([$i, $place_id, $placename])->fetchOne();
				if (!empty($pl_id)) {
					break;
				}
			}
			if (empty($pl_id)) {
				break;
			}
			$place_id = $pl_id;
		}

		return Database::prepare(
			"SELECT pl_lati, pl_long, pl_zoom, pl_icon, pl_level" .
			" FROM `##placelocation`" .
			" WHERE pl_id = ?" .
			" ORDER BY pl_place"
		)->execute([$place_id])->fetchOneRow();
	}

	/**
	 * @param Fact $fact
	 *
	 * @return array
	 */
	private function getPlaceData(Fact $fact) {
		$result = [];

		$has_latitude  = preg_match('/\n4 LATI (.+)/', $fact->getGedcom(), $match1);
		$has_longitude = preg_match('/\n4 LONG (.+)/', $fact->getGedcom(), $match2);

		// If co-ordinates are stored in the GEDCOM then use them
		if ($has_latitude && $has_longitude) {
			$result = [
				'index'   => 'ID' . $match1[1] . $match2[1],
				'mapdata' => [
					'class'   => 'optionbox',
					'place'   => $fact->getPlace()->getFullName(),
					'tooltip' => $fact->getPlace()->getGedcomName(),
					'lat'     => strtr($match1[1], ['N' => '', 'S' => '-', ',' => '.']),
					'lng'     => strtr($match2[1], ['E' => '', 'W' => '-', ',' => '.']),
					'pl_icon' => '',
					'pl_zoom' => '0',
					'events'  => '',
				],
			];
		} else {
			$place_location = $this->getLatitudeAndLongitudeFromPlaceLocation($fact->getPlace()->getGedcomName());
			if ($place_location && $place_location->pl_lati && $place_location->pl_long) {
				$result = [
					'index'   => 'ID' . $place_location->pl_lati . $place_location->pl_long,
					'mapdata' => [
						'class'   => 'optionbox',
						'place'   => $fact->getPlace()->getFullName(),
						'tooltip' => $fact->getPlace()->getGedcomName(),
						'lat'     => strtr($place_location->pl_lati, ['N' => '', 'S' => '-', ',' => '.']),
						'lng'     => strtr($place_location->pl_long, ['E' => '', 'W' => '-', ',' => '.']),
						'pl_icon' => $place_location->pl_icon,
						'pl_zoom' => $place_location->pl_zoom,
						'events'  => '',
					],
				];
			}
		}

		return $result;
	}

	/**
	 * Build a map for an individual.
	 *
	 * @param Individual $indi
	 *
	 * @return string
	 */
	private function buildIndividualMap(Individual $indi) {
		$GM_MAX_ZOOM = $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT);
		$facts       = $indi->getFacts();
		foreach ($indi->getSpouseFamilies() as $family) {
			$facts = array_merge($facts, $family->getFacts());
			// Add birth of children from this family to the facts array
			foreach ($family->getChildren() as $child) {
				foreach ($child->getFacts(WT_EVENTS_BIRT, true) as $fact) {
					if ($fact->getPlace() !== null) {
						$facts[] = $fact;
						break;
					}
				}
			}
		}

		Functions::sortFacts($facts);

		// At this point we have an array of valid sorted facts
		// so now build the data structures needed for the map display
		$events        = [];
		$unique_places = [];

		foreach ($facts as $fact) {
			$place_data = $this->getPlaceData($fact);

			if (!empty($place_data)) {
				$index = $place_data['index'];

				if ($place_data['mapdata']['pl_zoom']) {
					$GM_MAX_ZOOM = min($GM_MAX_ZOOM, $place_data['mapdata']['pl_zoom']);
				}
				// Produce the html for the sidebar
				$parent = $fact->getParent();
				if ($parent instanceof Individual && $parent->getXref() !== $indi->getXref()) {
					// Childs birth
					$name   = '<a href="' . e($parent->url()) . '">' . $parent->getFullName() . '</a>';
					$label  = strtr($parent->getSex(), ['F' => I18N::translate('Birth of a daughter'), 'M' => I18N::translate('Birth of a son'), 'U' => I18N::translate('Birth of a child')]);
					$class  = 'wt-gender-' . $parent->getSex();
					$evtStr = '<div class="gm-event">' . $label . '<div><strong>' . $name . '</strong></div>' . $fact->getDate()->display(true) . '</div>';
				} else {
					$spouse = $parent instanceof Family ? $parent->getSpouse($indi) : null;
					$name   = $spouse ? '<a href="' . e($spouse->url()) . '">' . $spouse->getFullName() . '</a>' : '';
					$label  = $fact->getLabel();
					$class  = '';
					if ($fact->getValue() && $spouse) {
						$evtStr = '<div class="gm-event">' . $label . '<div>' . $fact->getValue() . '</div><strong>' . $name . '</strong>' . $fact->getDate()->display(true) . '</div>';
					} elseif ($spouse) {
						$evtStr = '<div class="gm-event">' . $label . '<div><strong>' . $name . '</strong></div>' . $fact->getDate()->display(true) . '</div>';
					} elseif ($fact->getValue()) {
						$evtStr = '<div class="gm-event">' . $label . '<div> ' . $fact->getValue() . '</div>' . $fact->getDate()->display(true) . '</div>';
					} else {
						$evtStr = '<div class="gm-event">' . $label . '<div>' . $fact->getDate()->display(true) . '</div></div>';
					}
				}

				if (empty($unique_places[$index])) {
					$unique_places[$index] = $place_data['mapdata'];
				}
				$unique_places[$index]['events'] .= $evtStr;
				$events[] = [
					'class'      => $class,
					'fact_label' => $label,
					'date'       => $fact->getDate()->display(true),
					'info'       => $fact->getValue(),
					'name'       => $name,
					'place'      => '<a href="' . $fact->getPlace()->getURL() . '">' . $fact->getPlace()->getFullName() . '</a>',
					'placeid'    => $index,
				];
			}
		}

		if (!empty($events)) {
			$places = array_keys($unique_places);
			ob_start();
			// Create the normal googlemap sidebar of events and children
			echo '<div class="gm-events">';
			echo '<table class="wt-facts-table">';
			echo '<caption class="sr-only">' . I18N::translate('Facts and events') . '</caption>';
			echo '<tbody>';

			foreach ($events as $event) {
				$index = array_search($event['placeid'], $places);
				echo '<tr class="', $event['class'], '">';
				echo '<th scope="row">';
				echo '<a href="#" onclick="return openInfowindow(\'', $index, '\')">';
				echo $event['fact_label'];
				echo '</a>';
				echo '</th>';
				echo '<td>';
				if ($event['info']) {
					echo '<div><span class="field">', e($event['info']), '</span></div>';
				}
				if ($event['name']) {
					echo '<div>', $event['name'], '</div>';
				}
				echo '<div>', $event['place'], '</div>';
				if ($event['date']) {
					echo '<div>', $event['date'], '</div>';
				}
				echo '</td>';
				echo '</tr>';
			}

			echo '</tbody>';
			echo '</table>';
			echo '</div>';
			?>

			<script>
				var gmarkers   = [];
				var infowindow;

				// Opens Marker infowindow when corresponding Sidebar item is clicked
				function openInfowindow(i) {
					infowindow.close();
					google.maps.event.trigger(gmarkers[i], 'click');
					return false;
				}

				function loadMap() {
				var map_center = new google.maps.LatLng(0, 0);
				var gicons     = [];
				var map        = null;

				infowindow = new google.maps.InfoWindow({});

				gicons["red"] = {
					url:    "https://maps.google.com/mapfiles/marker.png",
					size:   google.maps.Size(20, 34),
					origin: google.maps.Point(0, 0),
					anchor: google.maps.Point(9, 34)
				};

				function getMarkerImage(iconColor) {
					if (typeof(iconColor) === 'undefined' || iconColor === null) {
						iconColor = 'red';
					}
					if (!gicons[iconColor]) {
						gicons[iconColor] = {
							url:    '//maps.google.com/mapfiles/marker' + iconColor + '.png',
							size:   new google.maps.Size(20, 34),
							origin: new google.maps.Point(0, 0),
							anchor: google.maps.Point(9, 34)
						};
					}
					return gicons[iconColor];
				}

				var placer   = null;

				// A function to create the marker and set up the event window
				function createMarker(latlng, html, tooltip, marker_icon) {
					// Use flag icon (if defined) instead of regular marker icon
					if (marker_icon) {
						var icon_image = {
							url:    WT_MODULES_DIR + 'googlemap/' + marker_icon,
							size:   new google.maps.Size(25, 15),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(12, 15)
						};
					} else {
						var icon_image = getMarkerImage('red');
					}

					placer = latlng;

					// Define the marker
					var marker = new google.maps.Marker({
						position: placer,
						icon:     icon_image,
						map:      map,
						title:    tooltip,
						zIndex:   Math.round(latlng.lat() * -100000) << 5
					});

					// Store the tab and event info as marker properties
					gmarkers.push(marker);

					// Open infowindow when marker is clicked
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.close();
						infowindow.setContent(html);
						infowindow.open(map, marker);
					});
				}

					// Create the map and mapOptions
					var mapOptions = {
						zoom:                     7,
						minZoom:                  <?= $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) ?>,
						maxZoom:                  <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>,
						center:                   map_center,
						mapTypeId:                google.maps.MapTypeId.ROADMAP,
						mapTypeControlOptions:    {
							style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
						},
						navigationControl:        true,
						navigationControlOptions: {
							position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
							style:    google.maps.NavigationControlStyle.SMALL  // ANDROID, DEFAULT, SMALL, ZOOM_PAN
						},
						scrollwheel:              true
					};
					map = new google.maps.Map(document.querySelector('.gm-map'), mapOptions);

					// Close any infowindow when map is clicked
					google.maps.event.addListener(map, 'click', function() {
						infowindow.close();
					});

					// Add the markers to the map

					// Group the markers by location
					var locations = <?= json_encode($unique_places) ?>;

					// Set the Marker bounds
					var bounds = new google.maps.LatLngBounds();
					var zoomLevel = <?= $GM_MAX_ZOOM ?>;

					jQuery.each(locations, function(index, location) {
						var point = new google.maps.LatLng(location.lat, location.lng); // Place Latitude, Longitude
						var html  =
							'<div class="gm-info-window">' +
							'<div class="gm-info-window-header">' + location.place + '</div>' +
							'<ul class="gm-tabs">' +
							'<li class="gm-tab gm-tab-active" id="gm-tab-events"><a href="#"><?= I18N::translate('Events') ?></a></li>' +
							'</ul>' +
							'<div class="gm-panes">' +
							'<div class="gm-pane" id="gm-pane-events">' + location.events + '</div>' +
							'</div>' +
							'</div>';

						createMarker(point, html, location.tooltip, location.pl_icon);
						bounds.extend(point);
					}); // end loop through location markers

					map.setCenter(bounds.getCenter());
					map.fitBounds(bounds);
					google.maps.event.addListenerOnce(map, "bounds_changed", function(event) {
						if (this.getZoom() > zoomLevel) {
							this.setZoom(zoomLevel);
						}
					});
				} // end loadMap()

			</script>
			<?php
			$html = ob_get_clean();
		} else {
			$html = '';
		}

		return $html;
	}

	/**
	 * Get the Location ID.
	 *
	 * @param string $place
	 *
	 * @return int
	 */
	private function getPlaceLocationId($place) {
		$par      = explode(',', $place);
		$par      = array_reverse($par);
		$place_id = 0;
		$pl_id    = 0;
		$num_par  = count($par);
		for ($i = 0; $i < $num_par; $i++) {
			$par[$i] = trim($par[$i]);
			if (empty($par[$i])) {
				$par[$i] = 'unknown';
			}
			$placelist = $this->createPossiblePlaceNames($par[$i], $i + 1);
			foreach ($placelist as $key => $placename) {
				$pl_id = (int) Database::prepare(
					"SELECT pl_id FROM `##placelocation` WHERE pl_level = :level AND pl_parent_id = :parent_id AND pl_place LIKE :placename"
				)->execute([
					'level'     => $i,
					'parent_id' => $place_id,
					'placename' => $placename,
				])->fetchOne();
				if ($pl_id) {
					break;
				}
			}
			if (!$pl_id) {
				break;
			}
			$place_id = $pl_id;
		}

		return $place_id;
	}

	/**
	 * Get the place ID.
	 *
	 * @param string $place
	 *
	 * @return int
	 */
	private function getPlaceId($place) {
		global $WT_TREE;

		$par      = explode(',', $place);
		$par      = array_reverse($par);
		$place_id = 0;
		$pl_id    = 0;
		$num_par  = count($par);
		for ($i = 0; $i < $num_par; $i++) {
			$par[$i]   = trim($par[$i]);
			$placelist = $this->createPossiblePlaceNames($par[$i], $i + 1);
			foreach ($placelist as $placename) {
				$pl_id = (int) Database::prepare(
					"SELECT p_id FROM `##places` WHERE p_parent_id = :place_id AND p_file = :tree_id AND p_place = :placename"
				)->execute([
					'place_id'  => $place_id,
					'tree_id'   => $WT_TREE->getTreeId(),
					'placename' => $placename,
				])->fetchOne();
				if ($pl_id) {
					break;
				}
			}
			if (!$pl_id) {
				break;
			}
			$place_id = $pl_id;
		}

		return $place_id;
	}

	/**
	 * Set the place IDs.
	 *
	 * @param int      $level
	 * @param string[] $parent
	 *
	 * @return int
	 */
	private function setPlaceIdMap($level, $parent) {
		$fullplace = '';
		if ($level == 0) {
			return 0;
		} else {
			for ($i = 1; $i <= $level; $i++) {
				$fullplace .= $parent[$level - $i] . ', ';
			}
			$fullplace = substr($fullplace, 0, -2);

			return $this->getPlaceId($fullplace);
		}
	}

	/**
	 * Set the map level.
	 *
	 * @param int      $level
	 * @param string[] $parent
	 *
	 * @return int
	 */
	private function setLevelMap($level, $parent) {
		$fullplace = '';
		if ($level == 0) {
			return 0;
		} else {
			for ($i = 1; $i <= $level; $i++) {
				if ($parent[$level - $i] != '') {
					$fullplace .= $parent[$level - $i] . ', ';
				} else {
					$fullplace .= 'Unknown, ';
				}
			}
			$fullplace = substr($fullplace, 0, -2);

			return $this->getPlaceLocationId($fullplace);
		}
	}

	/**
	 * Called by placelist.php
	 */
	public function createMap() {
		global $level, $levelm, $plzoom, $WT_TREE;

		Database::updateSchema(self::SCHEMA_MIGRATION_PREFIX, self::SCHEMA_SETTING_NAME, self::SCHEMA_TARGET_VERSION);

		$parent = Filter::getArray('parent');
		$levelm = $this->setLevelMap($level, $parent);

		$latlng =
			Database::prepare("SELECT pl_place, pl_id, pl_lati, pl_long, pl_zoom FROM `##placelocation` WHERE pl_id=?")
			->execute([$levelm])
			->fetch(PDO::FETCH_ASSOC);

		echo '<table style="margin:auto; border-collapse: collapse;">';
		echo '<tr style="vertical-align:top;"><td>';
		echo '<div id="gm-hierarchy-map" class="wt-ajax-load"></div>';
		echo '<script src="', $this->googleMapsScript(), '"></script>';

		$plzoom = $latlng['pl_zoom']; // Map zoom level

		if (Auth::isAdmin()) {
			$adminplaces_url = 'module.php?mod=googlemap&amp;mod_action=admin_places';
			if ($latlng && isset($latlng['pl_id'])) {
				$adminplaces_url .= '&amp;parent=' . $latlng['pl_id'];
			}
			$update_places_url = 'admin_trees_places.php?ged=' . e($WT_TREE->getName()) . '&amp;search=' . urlencode(implode(', ', array_reverse($parent)));
			echo '<div class="gm-options">';
			echo '<a href="' . $adminplaces_url . '">' . I18N::translate('Geographic data') . '</a>';
			echo ' | <a href="' . $update_places_url . '">' . I18N::translate('Update place names') . '</a>';
			echo '</div>';
		}
		echo '</td>';
		echo '</tr></table>';
	}

	/**
	 * Print the numbers of individuals.
	 *
	 * @param int      $level
	 * @param string[] $parent
	 */
	private function printHowManyPeople($level, $parent) {
		global $WT_TREE;

		$stats = new Stats($WT_TREE);

		$place_count_indi = 0;
		$place_count_fam  = 0;
		if (!isset($parent[$level - 1])) {
			$parent[$level - 1] = '';
		}
		$p_id = $this->setPlaceIdMap($level, $parent);
		$indi = $stats->statsPlaces('INDI', false, $p_id);
		$fam  = $stats->statsPlaces('FAM', false, $p_id);
		foreach ($indi as $place) {
			$place_count_indi = $place['tot'];
		}
		foreach ($fam as $place) {
			$place_count_fam = $place['tot'];
		}
		echo '<br><br>', I18N::translate('Individuals'), ': ', $place_count_indi, ', ', I18N::translate('Families'), ': ', $place_count_fam;
	}

	/**
	 * Print the flags and markers.
	 *
	 * @param stdClass $place2
	 * @param int      $level
	 * @param string[] $parent
	 * @param int      $levelm
	 * @param string   $linklevels
	 */
	private function printGoogleMapMarkers(stdClass $place2, $level, $parent, $levelm, $linklevels) {
		echo 'var icon_url = null;';
		if (!$place2->pl_lati || !$place2->pl_long) {
			echo 'var icon_url ="' . WT_MODULES_DIR . 'googlemap/images/marker_yellow.png";';
			echo 'var point = new google.maps.LatLng(0, 0);';
			echo 'var marker = createMarker(point, "<div style=\"width: 250px;\"><a href=\"?action=find', $linklevels, '&amp;parent[' . $level . ']=';

			if ($place2->pl_place == 'Unknown') {
				echo '\"><br>';
			} else {
				echo addslashes($place2->pl_place), '\"><br>';
			}
			if ($place2->pl_icon !== null && $place2->pl_icon !== '') {
				echo '<img src=\"', WT_MODULES_DIR, 'googlemap/', $place2->pl_icon, '\">&nbsp;&nbsp;';
			}
			if ($place2->pl_place == 'Unknown') {
				echo I18N::translate('unknown');
			} else {
				echo addslashes($place2->pl_place);
			}
			echo '</a>';
			$parent[$level] = $place2->pl_place;
			$this->printHowManyPeople($level + 1, $parent);
			echo '<br>', I18N::translate('This place has no coordinates');
			if (Auth::isAdmin()) {
				echo '<br><a href=\"module.php?mod=googlemap&amp;mod_action=admin_places&amp;parent=', $levelm, '&amp;display=inactive\">', I18N::translate('Geographic data'), '</a>';
			}
			echo '</div>", icon_url, "', str_replace(['&lrm;', '&rlm;'], [WT_UTF8_LRM, WT_UTF8_RLM], addslashes($place2->pl_place)), '");';
		} else {
			$lati = strtr($place2->pl_lati, ['N' => '', 'S' => '-', ',' => '.']);
			$long = strtr($place2->pl_long, ['E' => '', 'W' => '-', ',' => '.']);
			//delete leading zero
			if ($lati >= 0) {
				$lati = abs($lati);
			} elseif ($lati < 0) {
				$lati = '-' . abs($lati);
			}
			if ($long >= 0) {
				$long = abs($long);
			} elseif ($long < 0) {
				$long = '-' . abs($long);
			}

			if ($place2->pl_icon !== null && $place2->pl_icon !== '' && $this->getPreference('GM_PH_MARKER') === 'G_FLAG') {
				echo 'icon_url = "', WT_MODULES_DIR, 'googlemap/', $place2->pl_icon, '";';
			}
			echo 'var point = new google.maps.LatLng(', $lati, ', ', $long, ');';
			echo 'var marker = createMarker(point, "<div style=\"width: 250px;\"><a href=\"?action=find', $linklevels;
			echo '&amp;parent[', $level, ']=';
			if ($place2->pl_place !== 'Unknown') {
				echo rawurlencode($place2->pl_place);
			}
			echo '\"><br>';
			if ($place2->pl_icon !== null && $place2->pl_icon !== '') {
				echo '<img src=\"', WT_MODULES_DIR, 'googlemap/', $place2->pl_icon, '\">&nbsp;&nbsp;';
			}
			if ($place2->pl_place === 'Unknown') {
				echo I18N::translate('unknown');
			} else {
				echo e($place2->pl_place);
			}
			echo '</a>';
			$parent[$level] = $place2->pl_place;
			$this->printHowManyPeople($level + 1, $parent);
			echo '</div>", icon_url, ', json_encode($place2->pl_place), ');';
		}
	}

	/**
	 * Called by placelist.php
	 *
	 * @param int      $numfound
	 * @param int      $level
	 * @param string[] $parent
	 * @param string   $linklevels
	 * @param string[] $place_names
	 */
	public function mapScripts($numfound, $level, $parent, $linklevels, $place_names) {
		global $plzoom, $controller;

		$controller->addInlineJavascript('
			$("head").append(\'<link rel="stylesheet" type="text/css" href="' . WT_MODULES_DIR . 'googlemap/css/wt_v3_googlemap.css" />\');
			var numMarkers = "' . $numfound . '";
			var mapLevel   = "' . $level . '";
			var placezoom  = "' . $plzoom . '";
			var infowindow = new google.maps.InfoWindow({
				// size: new google.maps.Size(150,50),
				// maxWidth: 600
			});

			var map_center = new google.maps.LatLng(0,0);
			var map = "";
			var bounds = new google.maps.LatLngBounds ();
			var markers = [];
			var gmarkers = [];
			var i = 0;

			// Create the map and mapOptions
			var mapOptions = {
				minZoom: ' . $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) . ',
				maxZoom: ' . $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) . ',
				zoom: 8,
				center: map_center,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
				},
				navigationControl: true,
				navigationControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT, // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
					style: google.maps.NavigationControlStyle.SMALL  // ANDROID, DEFAULT, SMALL, ZOOM_PAN
				},
				scrollwheel: true
			};
			map = new google.maps.Map(document.getElementById("gm-hierarchy-map"), mapOptions);

			// Close any infowindow when map is clicked
			google.maps.event.addListener(map, "click", function() {
				infowindow.close();
			});

			// If only one marker, set zoom level to that of place in database
			if (mapLevel != 0) {
				var pointZoom = placezoom;
			} else {
				var pointZoom = 1;
			}

			// Creates a marker whose info window displays the given name
			function createMarker(point, html, icon, name) {
				// Choose icon ============
				if (icon && ' . $level . '<=3) {
					if (icon != "' . WT_MODULES_DIR . 'googlemap/images/marker_yellow.png") {
						var iconImage = {
							url:    icon,
							size:   new google.maps.Size(25, 15),
							origin: new google.maps.Point(0,0),
							anchor: new google.maps.Point(12, 15)
						};
					} else {
						var iconImage = {
							url:    icon,
							size:   new google.maps.Size(20, 34),
							origin: new google.maps.Point(0,0),
							anchor: new google.maps.Point(9, 34)
						};
					}
				} else {
					var iconImage = {
						url:    "https://maps.google.com/mapfiles/marker.png",
						size:   new google.maps.Size(20, 34),
						origin: new google.maps.Point(0,0),
						anchor: new google.maps.Point(9, 34)
					};
				}
				var posn = new google.maps.LatLng(0,0);
				var marker = new google.maps.Marker({
					position: point,
					icon: iconImage,
					map: map,
					title: name
				});
				// Show this markers name in the info window when it is clicked
				google.maps.event.addListener(marker, "click", function() {
					infowindow.close();
					infowindow.setContent(html);
					infowindow.open(map, marker);
				});
				// === Store the tab, category and event info as marker properties ===
				marker.mypoint = point;
				marker.mytitle = name;
				marker.myposn = posn;
				gmarkers.push(marker);
				bounds.extend(marker.position);

				// If only one marker use database place zoom level rather than fitBounds of markers
				if (numMarkers > 1) {
					map.fitBounds(bounds);
				} else {
					map.setCenter(bounds.getCenter());
					map.setZoom(parseFloat(pointZoom));
				}
				return marker;
			}
		');

		$levelm = $this->setLevelMap($level, $parent);

		//create markers
		ob_start();

		if ($numfound == 0 && $level > 0) {
			// show the current place on the map

			$place = Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id = ?")
				->execute([$levelm])
				->fetchOneRow();

			if ($place !== null) {
				// re-calculate the hierarchy information required to display the current place
				$thisloc = $parent;
				array_pop($thisloc);
				$thislevel      = $level - 1;
				$thislinklevels = substr($linklevels, 0, strrpos($linklevels, '&amp;'));

				$this->printGoogleMapMarkers($place, $thislevel, $thisloc, $place->pl_id, $thislinklevels);
			}
		}

		// display any sub-places
		$placeidlist = [];
		foreach ($place_names as $placename) {
			$thisloc     = $parent;
			$thisloc[]   = $placename;
			$this_levelm = $this->setLevelMap($level + 1, $thisloc);
			if ($this_levelm) {
				$placeidlist[] = $this_levelm;
			}
		}

		// flip the array (thus removing duplicates)
		$placeidlist = array_flip($placeidlist);
		// remove entry for parent location
		unset($placeidlist[$levelm]);

		if (!empty($placeidlist)) {
			// the keys are all we care about (this reverses the earlier array_flip, and ensures there are no "holes" in the array)
			$placeidlist = array_keys($placeidlist);
			// note: this implode/array_fill code generates one '?' for each entry in the $placeidlist array
			$placelist =
				Database::prepare(
					"SELECT * FROM `##placelocation` WHERE pl_id IN (" . implode(',', array_fill(0, count($placeidlist), '?')) . ')'
				)->execute($placeidlist)
				->fetchAll();

			foreach ($placelist as $place) {
				$this->printGoogleMapMarkers($place, $level, $parent, $place->pl_id, $linklevels);
			}
		}
		$controller->addInlineJavascript(ob_get_clean());
	}

	/**
	 * Take a place id and find its place in the hierarchy
	 * Input: place ID
	 * Output: ordered array of id=>name values, starting with the Top level
	 * e.g. 0=>"Top level", 16=>"England", 19=>"London", 217=>"Westminster"
	 *
	 * @param int $id
	 *
	 * @return string[]
	 */
	private function placeIdToHierarchy($id) {
		$statement = Database::prepare("SELECT pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=?");
		$arr       = [];
		while ($id != 0) {
			$row = $statement->execute([$id])->fetchOneRow();
			$arr = [$id => $row->pl_place] + $arr;
			$id  = $row->pl_parent_id;
		}

		return $arr;
	}

	/**
	 * Get the highest index.
	 *
	 * @return int
	 */
	private function getHighestIndex() {
		return (int) Database::prepare("SELECT MAX(pl_id) FROM `##placelocation`")->fetchOne();
	}

	/**
	 * Get the highest level.
	 *
	 * @return int
	 */
	private function getHighestLevel() {
		return (int) Database::prepare("SELECT MAX(pl_level) FROM `##placelocation`")->fetchOne();
	}

	/**
	 * Find all of the places in the hierarchy
	 *
	 * NOTE: the "inactive" filter ignores the hierarchy, so that "Paris, France"
	 * will match "Paris, Texas, United States".  A fully accurate match would be slow.
	 *
	 * @param int  $parent_id
	 * @param bool $inactive
	 *
	 * @return array[]
	 */
	private function getPlaceListLocation($parent_id, $inactive = false) {
		if ($inactive) {
			$rows = Database::prepare(
					"SELECT pl_id, pl_parent_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon" .
					" FROM `##placelocation`" .
					" WHERE pl_parent_id = :parent_id" .
					" ORDER BY pl_place COLLATE :collation"
				)->execute([
					'parent_id' => $parent_id,
					'collation' => I18N::collation(),
				])->fetchAll();
		} else {
			$rows = Database::prepare(
				"SELECT DISTINCT pl_id, pl_parent_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon" .
				" FROM `##placelocation`" .
				" JOIN `##places` ON `##placelocation`.pl_place = `##places`.p_place" .
				" WHERE pl_parent_id = :parent_id" .
				" ORDER BY pl_place COLLATE :collation"
			)->execute([
				'parent_id' => $parent_id,
				'collation' => I18N::collation(),
			])->fetchAll();
		}

		$placelist = [];
		foreach ($rows as $row) {
			// Find/count places without co-ordinates
			$children =
				Database::prepare(
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
				" FROM      `##placelocation` AS p1" .
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
			->execute([
				'parent_id' => $row->pl_id,
			])->fetchOneRow();

			$placelist[] = [
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
			];
		}

		return $placelist;
	}

	/**
	 * Set the output level.
	 *
	 * @param int $parent_id
	 */
	private function outputLevel($parent_id) {
		$tmp      = $this->placeIdToHierarchy($parent_id);
		$maxLevel = $this->getHighestLevel();
		if ($maxLevel > 8) {
			$maxLevel = 8;
		}
		$prefix = implode(';', $tmp);
		if ($prefix != '') {
			$prefix .= ';';
		}
		$suffix = str_repeat(';', $maxLevel - count($tmp));
		$level  = count($tmp);

		$rows = Database::prepare(
			"SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place"
		)->execute([$parent_id])->fetchAll();

		foreach ($rows as $row) {
			echo $level, ';', $prefix, $row->pl_place, $suffix, ';', $row->pl_long, ';', $row->pl_lati, ';', $row->pl_zoom, ';', $row->pl_icon, "\r\n";
			if ($level < $maxLevel) {
				$this->outputLevel($row->pl_id);
			}
		}
	}

	/**
	 * recursively find all of the csv files on the server
	 *
	 * @param string $path
	 *
	 * @return string[]
	 */
	private function findFiles($path) {
		$placefiles = [];

		try {
			$di = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
			$it = new \RecursiveIteratorIterator($di);

			foreach ($it as $file) {
				if ($file->getExtension() == 'csv') {
					$placefiles[] = '/' . $file->getFilename();
				}
			}
		} catch (\Exception $ex) {
		DebugBar::addThrowable($ex);

			Log::addErrorLog(basename($ex->getFile()) . ' - line: ' . $ex->getLine() . ' - ' . $ex->getMessage());
		}

		return $placefiles;
	}

	/**
	 * Show a form with options to upload a CSV file
	 */
	private function adminUploadForm() {
		$parent_id = (int) Filter::get('parent_id');
		$inactive  = (int) Filter::get('inactive');

		$controller = new PageController;
		$controller
			->setPageTitle(I18N::translate('Upload geographic data'))
			->pageHeader();

		echo Bootstrap4::breadcrumbs([
			route('admin-control-panel')                         => I18N::translate('Control panel'),
			route('admin-modules')                               => I18N::translate('Module administration'),
			$this->getConfigLink()                             => $this->getTitle(),
			'module.php?mod=googlemap&mod_action=admin_places' => I18N::translate('Geographic data'),
		], $controller->getPageTitle());

			$placefiles = $this->findFiles(WT_MODULES_DIR . 'googlemap/extra');
			sort($placefiles);

		?>
		<h1><?= $controller->getPageTitle() ?></h1>

		<form method="post" action="module.php?mod=googlemap&amp;mod_action=admin_upload_action" enctype="multipart/form-data">
			<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
			<input type="hidden" name="inactive" value="<?= $inactive ?>">

			<!-- PLACES FILE -->
			<div class="row form-group">
				<label class="col-form-label col-sm-4" for="placesfile">
					<?= I18N::translate('A file on your computer') ?>
				</label>
				<div class="col-sm-8">
					<input id="placesfile" type="file" name="placesfile" class="form-control">
				</div>
			</div>

			<!-- LOCAL FILE -->
			<div class="row form-group">
				<label class="col-form-label col-sm-4" for="localfile">
					<?= I18N::translate('A file on the server') ?>
				</label>
				<div class="col-sm-8">
					<div class="input-group" dir="ltr">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<?= WT_MODULES_DIR . 'googlemap/extra/' ?>
							</span>
						</div>
						<?php
						foreach ($placefiles as $p => $placefile) {
							unset($placefiles[$p]);
							$p = e($placefile);
							if (substr($placefile, 0, 1) == '/') {
								$placefiles[$p] = substr($placefile, 1);
							} else {
								$placefiles[$p] = $placefile;
							}
						}
						echo Bootstrap4::select($placefiles, '', ['id' => 'localfile', ['id' => 'localfile']]);
						?>
					</div>
				</div>
			</div>

			<!-- CLEAR DATABASE -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-4">
						<?= I18N::translate('Delete all existing geographic data before importing the file.') ?>
					</legend>
					<div class="col-sm-8">
						<?= Bootstrap4::radioButtons('cleardatabase', [I18N::translate('no'), I18N::translate('yes')], '0', true) ?>
					</div>
				</div>
			</fieldset>

			<!-- UPDATE ONLY -->
			<fieldset class="form-group">

				<div class="row">
					<legend class="col-form-label col-sm-4">
						<?= I18N::translate('Do not create new locations, just import coordinates for existing locations.') ?>
					</legend>
					<div class="col-sm-8">
						<?= Bootstrap4::radioButtons('updateonly', [I18N::translate('no'), I18N::translate('yes')], '0', true) ?>
					</div>
				</div>
			</fieldset>

			<!-- OVERWRITE DATA -->
			<fieldset class="form-group">
				<div class="row">
					<legend class="col-form-label col-sm-4">
						<?= I18N::translate('Overwrite existing coordinates.') ?>
					</legend>
					<div class="col-sm-8">
						<?= Bootstrap4::radioButtons('overwritedata', [I18N::translate('no'), I18N::translate('yes')], '0', true) ?>
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
	 * Delete a geographic place.
	 */
	private function adminDeleteAction() {
		$place_id  = (int) Filter::post('place_id');
		$parent_id = (int) Filter::post('parent_id');
		$inactive  = (int) Filter::post('inactive');

		try {
			Database::prepare(
				"DELETE FROM `##placelocation` WHERE pl_id = :place_id"
			)->execute([
				'place_id' => $place_id,
			]);
		} catch (\Exception $ex) {
		DebugBar::addThrowable($ex);

			FlashMessages::addMessage(I18N::translate('Location not removed: this location contains sub-locations'), 'danger');
		}

		header('Location: module.php?mod=googlemap&mod_action=admin_places&parent_id=' . $parent_id . '&inactive=' . $inactive);
	}

	/**
	 * Import places from GEDCOM data.
	 */
	private function adminImportAction() {
	}

	/**
	 * Upload a CSV file.
	 */
	private function adminUploadAction() {
		global $WT_TREE;

		$country_names = [];
		$stats         = new Stats($WT_TREE);
		foreach ($stats->iso3166() as $key => $value) {
			$country_names[$key] = I18N::translate($key);
		}
		if (Filter::postBool('cleardatabase')) {
			Database::exec("DELETE FROM `##placelocation` WHERE 1=1");
		}
		if (!empty($_FILES['placesfile']['tmp_name'])) {
			$lines = file($_FILES['placesfile']['tmp_name']);
		} elseif (!empty($_REQUEST['localfile'])) {
			$lines = file(WT_MODULES_DIR . 'googlemap/extra' . $_REQUEST['localfile']);
		} else {
			FlashMessages::addMessage(I18N::translate('No file was received. Please try again.'), 'danger');
			$lines = [''];
		}
		// Strip BYTE-ORDER-MARK, if present
		if (!empty($lines[0]) && substr($lines[0], 0, 3) === WT_UTF8_BOM) {
			$lines[0] = substr($lines[0], 3);
		}
		asort($lines);
		$highestIndex = $this->getHighestIndex();
		$placelist    = [];
		$j            = 0;
		$maxLevel     = 0;
		foreach ($lines as $p => $placerec) {
			$fieldrec = explode(';', $placerec);
			if ($fieldrec[0] > $maxLevel) {
				$maxLevel = $fieldrec[0];
			}
		}
		$fields   = count($fieldrec);
		$set_icon = true;
		if (!is_dir(WT_MODULES_DIR . 'googlemap/places/flags/')) {
			$set_icon = false;
		}
		foreach ($lines as $p => $placerec) {
			$fieldrec = explode(';', $placerec);
			if (is_numeric($fieldrec[0]) && $fieldrec[0] <= $maxLevel) {
				$placelist[$j]          = [];
				$placelist[$j]['place'] = '';
				for ($ii = $fields - 4; $ii > 1; $ii--) {
					if ($fieldrec[0] > $ii - 2) {
						$placelist[$j]['place'] .= $fieldrec[$ii] . ',';
					}
				}
				foreach ($country_names as $countrycode => $countryname) {
					if ($countrycode == strtoupper($fieldrec[1])) {
						$fieldrec[1] = $countryname;
						break;
					}
				}
				$placelist[$j]['place'] .= $fieldrec[1];
				$placelist[$j]['long'] = $fieldrec[$fields - 4];
				$placelist[$j]['lati'] = $fieldrec[$fields - 3];
				$placelist[$j]['zoom'] = $fieldrec[$fields - 2];
				if ($set_icon) {
					$placelist[$j]['icon'] = trim($fieldrec[$fields - 1]);
				} else {
					$placelist[$j]['icon'] = '';
				}
				$j = $j + 1;
			}
		}

		$prevPlace     = '';
		$prevLati      = '';
		$prevLong      = '';
		$placelistUniq = [];
		$j             = 0;
		foreach ($placelist as $k => $place) {
			if ($place['place'] != $prevPlace) {
				$placelistUniq[$j]          = [];
				$placelistUniq[$j]['place'] = $place['place'];
				$placelistUniq[$j]['lati']  = $place['lati'];
				$placelistUniq[$j]['long']  = $place['long'];
				$placelistUniq[$j]['zoom']  = $place['zoom'];
				$placelistUniq[$j]['icon']  = $place['icon'];
				$j                          = $j + 1;
			} elseif (($place['place'] == $prevPlace) && (($place['lati'] != $prevLati) || ($place['long'] != $prevLong))) {
				if (($placelistUniq[$j - 1]['lati'] == 0) || ($placelistUniq[$j - 1]['long'] == 0)) {
					$placelistUniq[$j - 1]['lati'] = $place['lati'];
					$placelistUniq[$j - 1]['long'] = $place['long'];
					$placelistUniq[$j - 1]['zoom'] = $place['zoom'];
					$placelistUniq[$j - 1]['icon'] = $place['icon'];
				} elseif (($place['lati'] != '0') || ($place['long'] != '0')) {
					echo 'Difference: previous value = ', $prevPlace, ', ', $prevLati, ', ', $prevLong, ' current = ', $place['place'], ', ', $place['lati'], ', ', $place['long'], '<br>';
				}
			}
			$prevPlace = $place['place'];
			$prevLati  = $place['lati'];
			$prevLong  = $place['long'];
		}

		$default_zoom_level    = [];
		$default_zoom_level[0] = 4;
		$default_zoom_level[1] = 7;
		$default_zoom_level[2] = 10;
		$default_zoom_level[3] = 12;
		foreach ($placelistUniq as $k => $place) {
			$parent     = explode(',', $place['place']);
			$parent     = array_reverse($parent);
			$parent_id  = 0;
			$num_parent = count($parent);
			for ($i = 0; $i < $num_parent; $i++) {
				$escparent = $parent[$i];
				if ($escparent == '') {
					$escparent = 'Unknown';
				}
				$row =
					Database::prepare("SELECT pl_id, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
					->execute([$i, $parent_id, $escparent])
					->fetchOneRow();
				if (empty($row)) {
					// this name does not yet exist: create entry
					if (!Filter::postBool('updateonly')) {
						$highestIndex = $highestIndex + 1;
						if (($i + 1) == $num_parent) {
							$zoomlevel = $place['zoom'];
						} elseif (isset($default_zoom_level[$i])) {
							$zoomlevel = $default_zoom_level[$i];
						} else {
							$zoomlevel = $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT);
						}
						if (($place['lati'] == '0') || ($place['long'] == '0') || (($i + 1) < $num_parent)) {
							Database::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?)")
								->execute([$highestIndex, $parent_id, $i, $escparent, $zoomlevel, $place['icon']]);
						} else {
							//delete leading zero
							$pl_lati = str_replace(['N', 'S', ','], ['', '-', '.'], $place['lati']);
							$pl_long = str_replace(['E', 'W', ','], ['', '-', '.'], $place['long']);
							if ($pl_lati >= 0) {
								$place['lati'] = 'N' . abs($pl_lati);
							} elseif ($pl_lati < 0) {
								$place['lati'] = 'S' . abs($pl_lati);
							}
							if ($pl_long >= 0) {
								$place['long'] = 'E' . abs($pl_long);
							} elseif ($pl_long < 0) {
								$place['long'] = 'W' . abs($pl_long);
							}
							Database::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
								->execute([$highestIndex, $parent_id, $i, $escparent, $place['long'], $place['lati'], $zoomlevel, $place['icon']]);
						}
						$parent_id = $highestIndex;
					}
				} else {
					$parent_id = $row->pl_id;
					if (Filter::postBool('overwritedata') && ($i + 1 == count($parent))) {
						Database::prepare("UPDATE `##placelocation` SET pl_lati = ?, pl_long = ?, pl_zoom = ?, pl_icon = ? WHERE pl_id = ?")
							->execute([$place['lati'], $place['long'], $place['zoom'], $place['icon'], $parent_id]);
					} else {
						// Update only if existing data is missing
						if (!$row->pl_long && !$row->pl_lati) {
							Database::prepare("UPDATE `##placelocation` SET pl_lati = ?, pl_long = ? WHERE pl_id = ?")
								->execute([$place['lati'], $place['long'], $parent_id]);
						}
						if (!$row->pl_icon && $place['icon']) {
							Database::prepare("UPDATE `##placelocation` SET pl_icon = ? WHERE pl_id = ?")
								->execute([$place['icon'], $parent_id]);
						}
					}
				}
			}
		}

		$parent_id = (int) Filter::post('parent_id');
		$inactive  = (int) Filter::post('inactive');

		header('Location: module.php?mod=googlemap&mod_action=admin_places&parent_id=' . $parent_id . '&inactive=' . $inactive);
	}

	/**
	 * Export/download the place hierarchy, or a prt of it.
	 */
	private function adminDownload() {
		$parent_id = (int) Filter::get('parent_id');
		$hierarchy = $this->placeIdToHierarchy($parent_id);
		$maxLevel  = min(8, $this->getHighestLevel());

		if (empty($hierarchy)) {
			$filename = 'places.csv';
		} else {
			$filename = 'places-' . preg_replace('/[:;\/\\\(\)\{\}\[\] $]/', '_', implode('-', $hierarchy)) . '.csv';
		}

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: inline; filename="' . $filename . '"');

		echo '"', I18N::translate('Level'), '";';
		echo '"', I18N::translate('Country'), '";';
		if ($maxLevel > 0) {
			echo '"', I18N::translate('State'), '";';
		}
		if ($maxLevel > 1) {
			echo '"', I18N::translate('County'), '";';
		}
		if ($maxLevel > 2) {
			echo '"', I18N::translate('City'), '";';
		}
		if ($maxLevel > 3) {
			echo '"', I18N::translate('Place'), '";';
		}
		if ($maxLevel > 4) {
			echo '"', I18N::translate('Place'), '";';
		}
		if ($maxLevel > 5) {
			echo '"', I18N::translate('Place'), '";';
		}
		if ($maxLevel > 6) {
			echo '"', I18N::translate('Place'), '";';
		}
		if ($maxLevel > 7) {
			echo '"', I18N::translate('Place'), '";';
		}
		echo '"', I18N::translate('Longitude'), '";';
		echo '"', I18N::translate('Latitude'), '";';
		echo '"', I18N::translate('Zoom level'), '";';
		echo '"', I18N::translate('Icon'), '";';
		$this->outputLevel($parent_id);
	}

	/**
	 * Save a new/updated geographic place.
	 */
	private function adminPlaceSave() {
		$parent_id = (int) Filter::post('parent_id');
		$place_id  = (int) Filter::post('place_id');
		$inactive  = (int) Filter::post('inactive');
		$level     = count($this->placeIdToHierarchy($parent_id));

		if ($place_id === 0) {
			Database::prepare(
				"INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
			)->execute([
				$this->getHighestIndex() + 1,
				$parent_id,
				$level,
				Filter::post('NEW_PLACE_NAME'),
				Filter::post('LONG_CONTROL') . Filter::post('NEW_PLACE_LONG'),
				Filter::post('LATI_CONTROL') . Filter::post('NEW_PLACE_LATI'),
				Filter::post('NEW_ZOOM_FACTOR'),
				Filter::post('icon'),
			]);
		} else {
			Database::prepare(
			"UPDATE `##placelocation` SET pl_place = ?, pl_lati = ?, pl_long = ?, pl_zoom = ?, pl_icon = ? WHERE pl_id = ?"
			)->execute([
				Filter::post('NEW_PLACE_NAME'),
				Filter::post('LATI_CONTROL') . Filter::post('NEW_PLACE_LATI'),
				Filter::post('LONG_CONTROL') . Filter::post('NEW_PLACE_LONG'),
				Filter::post('NEW_ZOOM_FACTOR'),
				Filter::post('icon'),
				$place_id,
			]);
		}

		header('Location: module.php?mod=googlemap&mod_action=admin_places&parent_id=' . $parent_id . '&inactive=' . $inactive);
	}

	/**
	 * Create or edit a geographic place.
	 */
	private function adminPlaceEdit() {
		$parent_id  = (int) Filter::post('parent_id', null, Filter::get('parent_id'));
		$place_id   = (int) Filter::post('place_id', null, Filter::get('place_id'));
		$inactive   = (int) Filter::post('inactive', null, Filter::get('inactive'));
		$where_am_i = $this->placeIdToHierarchy($place_id);
		$level      = count($where_am_i);

		$controller = new PageController;
		$controller
			->setPageTitle(I18N::translate('Geographic data'))
			->addInlineJavascript('$("<link>", {rel: "stylesheet", type: "text/css", href: "' . WT_MODULES_DIR . 'googlemap/css/wt_v3_googlemap.css"}).appendTo("head");')
			->pageHeader();

		// Find (or create) the record we are editing.
		$record =
			Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id=?")
			->execute([$place_id])
			->fetchOneRow();

		$parent_record =
			Database::prepare("SELECT * FROM `##placelocation` WHERE pl_id=?")
			->execute([$parent_id])
			->fetchOneRow();

		if ($parent_record === null) {
			$parent_record = (object) [
				'pl_id'        => 0,
				'pl_parent_id' => 0,
				'pl_place'     => '',
				'pl_lati'      => 'N0',
				'pl_long'      => 'E0',
				'pl_level'     => $level - 1,
				'pl_icon'      => '',
				'pl_zoom'      => self::GM_MIN_ZOOM_DEFAULT,
			];
		}

		if ($record === null || $place_id === 0) {
			$record = (object) [
				'pl_id'        => 0,
				'pl_parent_id' => $parent_id,
				'pl_place'     => '',
				'pl_lati'      => 'N0',
				'pl_long'      => 'E0',
				'pl_level'     => $level,
				'pl_icon'      => '',
				'pl_zoom'      => $parent_record === null ? self::GM_MIN_ZOOM_DEFAULT : $parent_record->pl_zoom,
			];
		}

		// Convert to floating point for the map.
		$latitude  = (float) (str_replace(['N', 'S'], ['', '-'], $record->pl_lati));
		$longitude = (float) (str_replace(['E', 'W'], ['', '-'], $record->pl_long));
		if ($latitude === 0 && $longitude === 0) {
			$latitude  = (float) (str_replace(['N', 'S'], ['', '-'], $record->pl_lati));
			$longitude = (float) (str_replace(['E', 'W'], ['', '-'], $record->pl_long));
		}

		$parent_url = 'module.php?mod=googlemap&mod_action=admin_places&parent_id=' . $parent_id . '&inactive=' . $inactive;

		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			$this->getConfigLink()     => $this->getTitle(),
		];
		$hierarchy =
			[0 => I18N::translate('Geographic data')] +
			$this->placeIdToHierarchy($place_id === 0 ? $parent_id : $place_id);
		foreach ($hierarchy as $id => $name) {
			$breadcrumbs += ['module.php?mod=googlemap&mod_action=admin_places&parent_id=' . $id . '&inactive=' . $inactive => e($name)];
		}
		echo Bootstrap4::breadcrumbs($breadcrumbs, $place_id === 0 ? I18N::translate('Add') : I18N::translate('Edit'));

		?>
		<script src="<?= $this->googleMapsScript() ?>"></script>
		<script>
		var map;
		var marker;
		var zoom;
		var pl_name = <?= json_encode($record->pl_place) ?>;
			var latlng = new google.maps.LatLng(<?= $latitude ?>, <?= $longitude ?>);
		var pl_zoom = <?= $record->pl_zoom ?>;
		var polygon1;
		var polygon2;
		var geocoder;
		var mapType;

		var infowindow = new google.maps.InfoWindow({});

		function geocodePosition(pos) {
			geocoder.geocode({
				latLng: pos
			}, function(responses) {
				if (responses && responses.length > 0) {
					updateMarkerAddress(responses[0].formatted_address);
				} else {
					updateMarkerAddress('Cannot determine address at this location.');
				}
			});
		}

		/**
		 * Redraw the map, centered and zoomed on the selected point.
		 *
		 * @param event
		 */
		function updateMap(event) {
			var point;
			var zoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);
			var latitude;
			var longitude;

			if ((document.editplaces.NEW_PLACE_LATI.value === '') ||
				(document.editplaces.NEW_PLACE_LONG.value === '')) {
				latitude = parseFloat(document.editplaces.parent_lati.value).toFixed(5);
				longitude = parseFloat(document.editplaces.parent_long.value).toFixed(5);
				point = new google.maps.LatLng(latitude, longitude);
			} else {
				latitude = parseFloat(document.editplaces.NEW_PLACE_LATI.value).toFixed(5);
				longitude = parseFloat(document.editplaces.NEW_PLACE_LONG.value).toFixed(5);
				document.editplaces.NEW_PLACE_LATI.value = latitude;
				document.editplaces.NEW_PLACE_LONG.value = longitude;

				if (event === 'flag_drag') {
					if (longitude < 0.0 ) {
						longitude = longitude * -1;
						document.editplaces.NEW_PLACE_LONG.value = longitude;
						document.editplaces.LONG_CONTROL.value = 'W';
					} else {
						document.editplaces.NEW_PLACE_LONG.value = longitude;
						document.editplaces.LONG_CONTROL.value = 'E';
					}
					if (latitude < 0.0 ) {
						latitude = latitude * -1;
						document.editplaces.NEW_PLACE_LATI.value = latitude;
						document.editplaces.LATI_CONTROL.value = 'S';
					} else {
						document.editplaces.NEW_PLACE_LATI.value = latitude;
						document.editplaces.LATI_CONTROL.value = 'N';
					}

					if (document.editplaces.LATI_CONTROL.value === 'S') {
						latitude = latitude * -1;
					}
					if (document.editplaces.LONG_CONTROL.value === 'W') {
						longitude = longitude * -1;
					}
					point = new google.maps.LatLng(latitude, longitude);
				} else {
					if (latitude < 0.0) {
						latitude = latitude * -1;
						document.editplaces.NEW_PLACE_LATI.value = latitude;
					}
					if (longitude < 0.0) {
						longitude = longitude * -1;
						document.editplaces.NEW_PLACE_LONG.value = longitude;
					}
					if (document.editplaces.LATI_CONTROL.value === 'S') {
						latitude = latitude * -1;
					}
					if (document.editplaces.LONG_CONTROL.value === 'W') {
						longitude = longitude * -1;
					}
					point = new google.maps.LatLng(latitude, longitude);
				}
			}

			map.setCenter(point);
			map.setZoom(zoom);
			marker.setPosition(point);
		}

		// === Create Borders for the UK Countries =========================================================
		function overlays() {
			// Define place LatLng arrays

			<?php
			$coordsAsStr = [];
			switch ($record->pl_place) {
				case 'England':
					$coordsAsStr[] = '-4.74361,50.66750|-4.78361,50.59361|-4.91584,50.57722|-5.01750,50.54264|-5.02569,50.47271|-5.04729,50.42750|-5.15208,50.34374|-5.26805,50.27389|-5.43194,50.19326|-5.49584,50.21695|-5.54639,50.20527|-5.71000,50.12916|-5.71681,50.06083|-5.66174,50.03631|-5.58278,50.04777|-5.54166,50.07055|-5.53416,50.11569|-5.47055,50.12499|-5.33361,50.09138|-5.27666,50.05972|-5.25674,50.00514|-5.19306,49.95527|-5.16070,50.00319|-5.06555,50.03750|-5.07090,50.08166|-5.04806,50.17111|-4.95278,50.19333|-4.85750,50.23166|-4.76250,50.31138|-4.67861,50.32583|-4.54334,50.32222|-4.48278,50.32583|-4.42972,50.35139|-4.38000,50.36388|-4.16555,50.37028|-4.11139,50.33027|-4.05708,50.29791|-3.94389,50.31346|-3.87764,50.28139|-3.83653,50.22972|-3.78944,50.21222|-3.70666,50.20972|-3.65195,50.23111|-3.55139,50.43833|-3.49416,50.54639|-3.46181,50.58792|-3.41139,50.61610|-3.24416,50.67444|-3.17347,50.68833|-3.09445,50.69222|-2.97806,50.70638|-2.92750,50.73125|-2.88278,50.73111|-2.82305,50.72027|-2.77139,50.70861|-2.66195,50.67334|-2.56305,50.63222|-2.45861,50.57500|-2.44666,50.62639|-2.39097,50.64166|-2.19722,50.62611|-2.12195,50.60722|-2.05445,50.58569|-1.96437,50.59674|-1.95441,50.66536|-2.06681,50.71430|-1.93416,50.71277|-1.81639,50.72306|-1.68445,50.73888|-1.59278,50.72416|-1.33139,50.79138|-1.11695,50.80694|-1.15889,50.84083|-1.09445,50.84584|-0.92842,50.83966|-0.86584,50.79965|-0.90826,50.77396|-0.78187,50.72722|-0.74611,50.76583|-0.67528,50.78111|-0.57722,50.79527|-0.25500,50.82638|-0.19084,50.82583|-0.13805,50.81833|0.05695,50.78083|0.12334,50.75944|0.22778,50.73944|0.28695,50.76500|0.37195,50.81638|0.43084,50.83111|0.56722,50.84777|0.67889,50.87681|0.71639,50.90500|0.79334,50.93610|0.85666,50.92556|0.97125,50.98111|0.99778,51.01903|1.04555,51.04944|1.10028,51.07361|1.26250,51.10166|1.36889,51.13583|1.41111,51.20111|1.42750,51.33111|1.38556,51.38777|1.19195,51.37861|1.05278,51.36722|0.99916,51.34777|0.90806,51.34069|0.70416,51.37749|0.61972,51.38304|0.55945,51.40596|0.64236,51.44042|0.69750,51.47084|0.59195,51.48777|0.53611,51.48806|0.48916,51.48445|0.45215,51.45562|0.38894,51.44822|0.46500,51.50306|0.65195,51.53680|0.76695,51.52138|0.82084,51.53556|0.87528,51.56110|0.95250,51.60923|0.94695,51.72556|0.90257,51.73465|0.86306,51.71166|0.76140,51.69164|0.70111,51.71847|0.86211,51.77361|0.93236,51.80583|0.98278,51.82527|1.03569,51.77416|1.08834,51.77056|1.13222,51.77694|1.18139,51.78972|1.22361,51.80888|1.26611,51.83916|1.28097,51.88096|1.20834,51.95083|1.16347,52.02361|1.27750,51.98555|1.33125,51.92875|1.39028,51.96999|1.58736,52.08388|1.63000,52.19527|1.68576,52.32630|1.73028,52.41138|1.74945,52.45583|1.74590,52.62021|1.70250,52.71583|1.64528,52.77111|1.50361,52.83749|1.43222,52.87472|1.35250,52.90972|1.28222,52.92750|1.18389,52.93889|0.99472,52.95111|0.94222,52.95083|0.88472,52.96638|0.66722,52.97611|0.54778,52.96618|0.49139,52.93430|0.44431,52.86569|0.42903,52.82403|0.36334,52.78027|0.21778,52.80694|0.16125,52.86250|0.05778,52.88916|0.00211,52.87985|0.03222,52.91722|0.20389,53.02805|0.27666,53.06694|0.33916,53.09236|0.35389,53.18722|0.33958,53.23472|0.23555,53.39944|0.14347,53.47527|0.08528,53.48638|0.02694,53.50972|-0.10084,53.57306|-0.20722,53.63083|-0.26445,53.69083|-0.30166,53.71319|-0.39022,53.70794|-0.51972,53.68527|-0.71653,53.69638|-0.65445,53.72527|-0.60584,53.72972|-0.54916,53.70611|-0.42261,53.71755|-0.35728,53.73056|-0.29389,53.73666|-0.23139,53.72166|-0.10584,53.63166|-0.03472,53.62555|0.04416,53.63916|0.08916,53.62666|0.14945,53.58847|0.12639,53.64527|0.06264,53.70389|-0.12750,53.86388|-0.16916,53.91847|-0.21222,54.00833|-0.20569,54.05153|-0.16111,54.08806|-0.11694,54.13222|-0.20053,54.15171|-0.26250,54.17444|-0.39334,54.27277|-0.42166,54.33222|-0.45750,54.37694|-0.51847,54.44749|-0.56472,54.48000|-0.87584,54.57027|-1.06139,54.61722|-1.16528,54.64972|-1.30445,54.77138|-1.34556,54.87138|-1.41278,54.99944|-1.48292,55.08625|-1.51500,55.14972|-1.56584,55.28722|-1.58097,55.48361|-1.63597,55.58194|-1.69000,55.60556|-1.74695,55.62499|-1.81764,55.63306|-1.97681,55.75416|-2.02166,55.80611|-2.08361,55.78054|-2.22000,55.66499|-2.27916,55.64472|-2.27416,55.57527|-2.21528,55.50583|-2.18278,55.45985|-2.21236,55.42777|-2.46305,55.36111|-2.63055,55.25500|-2.69945,55.17722|-2.96278,55.03889|-3.01500,55.05222|-3.05103,54.97986|-3.13292,54.93139|-3.20861,54.94944|-3.28931,54.93792|-3.39166,54.87639|-3.42916,54.81555|-3.56916,54.64249|-3.61306,54.48861|-3.49305,54.40333|-3.43389,54.34806|-3.41056,54.28014|-3.38055,54.24444|-3.21472,54.09555|-3.15222,54.08194|-2.93097,54.15333|-2.81361,54.22277|-2.81750,54.14277|-2.83361,54.08500|-2.93250,53.95055|-3.05264,53.90764|-3.03708,53.74944|-2.99278,53.73277|-2.89979,53.72499|-2.97729,53.69382|-3.07306,53.59805|-3.10563,53.55993|-3.00678,53.41738|-2.95389,53.36027|-2.85736,53.32083|-2.70493,53.35062|-2.77639,53.29250|-2.89972,53.28916|-2.94250,53.31056|-3.02889,53.38191|-3.07248,53.40936|-3.16695,53.35708|-3.12611,53.32500|-3.08860,53.26001|-3.02000,53.24722|-2.95528,53.21555|-2.91069,53.17014|-2.89389,53.10416|-2.85695,53.03249|-2.77792,52.98514|-2.73109,52.96873|-2.71945,52.91902|-2.79278,52.90207|-2.85069,52.93875|-2.99389,52.95361|-3.08639,52.91611|-3.13014,52.88486|-3.13708,52.79312|-3.06806,52.77027|-3.01111,52.71166|-3.06666,52.63527|-3.11750,52.58666|-3.07089,52.55702|-3.00792,52.56902|-2.98028,52.53083|-3.02736,52.49792|-3.11916,52.49194|-3.19514,52.46722|-3.19611,52.41027|-3.02195,52.34027|-2.95486,52.33117|-2.99750,52.28139|-3.05125,52.23347|-3.07555,52.14804|-3.12222,52.11805|-3.11250,52.06945|-3.08500,52.01930|-3.04528,51.97639|-2.98889,51.92555|-2.91757,51.91569|-2.86639,51.92889|-2.77861,51.88583|-2.65944,51.81806|-2.68334,51.76957|-2.68666,51.71889|-2.66500,51.61500|-2.62916,51.64416|-2.57889,51.67777|-2.46056,51.74666|-2.40389,51.74041|-2.47166,51.72445|-2.55305,51.65722|-2.65334,51.56389|-2.77055,51.48916|-2.85278,51.44472|-2.96000,51.37499|-3.00695,51.30722|-3.01278,51.25632|-3.02834,51.20611|-3.30139,51.18111|-3.39361,51.18138|-3.43729,51.20638|-3.50722,51.22333|-3.57014,51.23027|-3.63222,51.21805|-3.70028,51.23000|-3.79250,51.23916|-3.88389,51.22416|-3.98472,51.21695|-4.11666,51.21222|-4.22805,51.18777|-4.22028,51.11054|-4.23702,51.04659|-4.30361,51.00416|-4.37639,50.99110|-4.42736,51.00958|-4.47445,51.01416|-4.52132,51.01424|-4.54334,50.92694|-4.56139,50.77625|-4.65139,50.71527|-4.74361,50.66750';
					break;
				case 'Scotland':
					$coordsAsStr[] = '-2.02166,55.80611|-2.07972,55.86722|-2.13028,55.88583|-2.26028,55.91861|-2.37528,55.95694|-2.65722,56.05972|-2.82028,56.05694|-2.86618,56.02840|-2.89555,55.98861|-2.93500,55.96944|-3.01805,55.94944|-3.06750,55.94444|-3.25472,55.97166|-3.45472,55.99194|-3.66416,56.00652|-3.73722,56.05555|-3.57139,56.05360|-3.44111,56.01916|-3.39584,56.01083|-3.34403,56.02333|-3.13903,56.11084|-2.97611,56.19472|-2.91666,56.20499|-2.84695,56.18638|-2.78805,56.18749|-2.67937,56.21465|-2.58403,56.28264|-2.67208,56.32277|-2.76861,56.33180|-2.81528,56.37360|-2.81208,56.43958|-2.91653,56.45014|-2.99555,56.41416|-3.19042,56.35958|-3.27805,56.35750|-3.04055,56.45472|-2.95861,56.45611|-2.72084,56.48888|-2.64084,56.52250|-2.53126,56.57611|-2.48861,56.61416|-2.47805,56.71527|-2.39000,56.77166|-2.31986,56.79638|-2.21972,56.86777|-2.19708,56.94388|-2.16695,57.00055|-2.09334,57.07027|-2.05416,57.21861|-1.95889,57.33250|-1.85584,57.39889|-1.77334,57.45805|-1.78139,57.50555|-1.82195,57.57861|-1.86000,57.62138|-1.92972,57.67777|-2.02222,57.69388|-2.07555,57.69944|-2.14028,57.69056|-2.18611,57.66861|-2.39626,57.66638|-2.51000,57.67166|-2.78639,57.70222|-2.89806,57.70694|-2.96750,57.68027|-3.03847,57.66249|-3.12334,57.67166|-3.22334,57.69166|-3.28625,57.72499|-3.33972,57.72333|-3.48805,57.70945|-3.52222,57.66333|-3.59542,57.63666|-3.64063,57.63881|-3.75414,57.62504|-4.03986,57.55569|-4.19666,57.48584|-4.22889,57.51554|-4.17945,57.56249|-4.11139,57.59833|-4.08078,57.66533|-4.19139,57.67139|-4.25945,57.65527|-4.34361,57.60777|-4.41639,57.60166|-4.29666,57.67444|-4.08528,57.72611|-4.01908,57.70226|-3.96861,57.70250|-3.86556,57.76861|-3.81945,57.80458|-3.80681,57.85819|-3.85055,57.82000|-3.92639,57.80749|-4.04322,57.81438|-4.14973,57.82527|-4.29750,57.84638|-4.36250,57.89777|-4.24306,57.87028|-4.10666,57.85195|-4.01500,57.86777|-3.99166,57.90611|-3.99695,57.95056|-3.84500,58.02000|-3.56611,58.13916|-3.51319,58.16374|-3.45916,58.20305|-3.42028,58.24361|-3.33750,58.27694|-3.20555,58.30625|-3.10972,58.38166|-3.05792,58.45083|-3.02264,58.64653|-3.17639,58.64944|-3.35389,58.66055|-3.36931,58.59555|-3.57611,58.62194|-3.66028,58.61972|-3.71166,58.60374|-3.78264,58.56750|-3.84834,58.56000|-4.08056,58.55527|-4.27722,58.53361|-4.43653,58.54902|-4.50666,58.56777|-4.56055,58.57584|-4.59910,58.53027|-4.66805,58.48833|-4.76146,58.44604|-4.70195,58.50999|-4.70166,58.55861|-4.77014,58.60264|-5.00153,58.62416|-5.10945,58.50833|-5.16472,58.32527|-5.12639,58.28750|-5.07166,58.26472|-5.20361,58.25083|-5.39764,58.25055|-5.27389,58.11722|-5.31514,58.06416|-5.38416,58.08361|-5.45285,58.07416|-5.39805,58.03111|-5.26278,57.97111|-5.19334,57.95069|-5.12750,57.86944|-5.21750,57.90084|-5.33861,57.92083|-5.42876,57.90104|-5.45750,57.85889|-5.64445,57.89972|-5.62555,57.85222|-5.58153,57.81945|-5.60674,57.76618|-5.66305,57.78889|-5.71695,57.86944|-5.76695,57.86472|-5.81708,57.81944|-5.81084,57.63958|-5.69555,57.55944|-5.64361,57.55222|-5.53084,57.52833|-5.65305,57.50875|-5.75000,57.54834|-5.81569,57.57923|-5.85042,57.54972|-5.86695,57.46777|-5.81806,57.36250|-5.75111,57.34333|-5.50334,57.40111|-5.45126,57.41805|-5.49250,57.37083|-5.59884,57.33049|-5.57116,57.28411|-5.51266,57.27745|-5.40514,57.23097|-5.44972,57.22138|-5.49472,57.23888|-5.56066,57.25477|-5.64611,57.23499|-5.64751,57.16161|-5.55028,57.11639|-5.48166,57.11222|-5.40305,57.11062|-5.55945,57.09250|-5.65111,57.11611|-5.72472,57.11306|-5.77361,57.04556|-5.63139,56.98499|-5.56916,56.98972|-5.52403,56.99735|-5.57916,56.98000|-5.64611,56.97222|-5.73374,57.00909|-5.82584,57.00346|-5.91958,56.88708|-5.86528,56.87944|-5.74278,56.89374|-5.66292,56.86924|-5.73306,56.83916|-5.78584,56.83955|-5.85590,56.81430|-5.80208,56.79180|-5.84958,56.74444|-5.90500,56.75666|-5.96694,56.78027|-6.14000,56.75777|-6.19208,56.74888|-6.23452,56.71673|-6.19139,56.67972|-5.91916,56.67388|-5.82622,56.69156|-5.73945,56.71166|-5.55240,56.68886|-5.64861,56.68027|-5.69916,56.68278|-5.88261,56.65666|-5.97472,56.65138|-5.99584,56.61138|-5.93056,56.56972|-5.88416,56.55333|-5.79056,56.53805|-5.67695,56.49389|-5.56389,56.54056|-5.36334,56.66195|-5.23416,56.74333|-5.13236,56.79403|-5.31473,56.65666|-5.37405,56.55925|-5.31826,56.55633|-5.25080,56.55753|-5.37718,56.52112|-5.39866,56.47866|-5.19111,56.46194|-5.11556,56.51277|-5.07014,56.56069|-5.13555,56.48499|-5.22084,56.43583|-5.32764,56.43574|-5.42439,56.43091|-5.52611,56.37360|-5.57139,56.32833|-5.59653,56.25695|-5.57389,56.16000|-5.52000,56.16485|-5.56334,56.11333|-5.60139,56.07638|-5.64222,56.04305|-5.66039,55.98263|-5.62555,56.02055|-5.58014,56.01319|-5.63361,55.96611|-5.67697,55.88844|-5.64750,55.78139|-5.60986,55.75930|-5.66916,55.66166|-5.70166,55.58861|-5.71805,55.51500|-5.75916,55.41750|-5.79528,55.36027|-5.78166,55.29902|-5.73778,55.29222|-5.56694,55.31666|-5.51528,55.36347|-5.55520,55.41440|-5.48639,55.64306|-5.44597,55.70680|-5.38000,55.75027|-5.41889,55.90666|-5.39924,55.99972|-5.33895,56.03456|-5.30594,56.06922|-5.23889,56.11889|-5.03222,56.23250|-4.92229,56.27111|-4.97416,56.23333|-5.07222,56.18695|-5.20069,56.11861|-5.30906,56.00570|-5.34000,55.90201|-5.29250,55.84750|-5.20805,55.84444|-5.22458,55.90175|-5.17334,55.92916|-5.11000,55.90306|-5.01222,55.86694|-4.96195,55.88000|-4.89824,55.98145|-4.84623,56.08632|-4.86636,56.03178|-4.85461,55.98648|-4.77659,55.97977|-4.62723,55.94555|-4.52305,55.91861|-4.70972,55.93403|-4.75166,55.94611|-4.82406,55.94950|-4.87826,55.93653|-4.91639,55.70083|-4.87584,55.68194|-4.81361,55.64555|-4.68722,55.59750|-4.61361,55.49069|-4.63958,55.44264|-4.68250,55.43388|-4.74847,55.41055|-4.83715,55.31882|-4.84778,55.26944|-4.86542,55.22340|-4.93500,55.17860|-5.01250,55.13347|-5.05361,55.04902|-5.17834,54.98888|-5.18563,54.93622|-5.17000,54.89111|-5.11666,54.83180|-5.00500,54.76333|-4.96229,54.68125|-4.92250,54.64055|-4.85723,54.62958|-4.96076,54.79687|-4.92431,54.83708|-4.85222,54.86861|-4.80125,54.85556|-4.74055,54.82166|-4.68084,54.79972|-4.59861,54.78027|-4.55792,54.73903|-4.49639,54.69888|-4.37584,54.67666|-4.34569,54.70916|-4.35973,54.77111|-4.41111,54.82583|-4.42445,54.88152|-4.38479,54.90555|-4.35056,54.85903|-4.09555,54.76777|-3.95361,54.76749|-3.86972,54.80527|-3.81222,54.84888|-3.69250,54.88110|-3.61584,54.87527|-3.57111,54.99083|-3.44528,54.98638|-3.36056,54.97138|-3.14695,54.96500|-3.05103,54.97986|-3.01500,55.05222|-2.96278,55.03889|-2.69945,55.17722|-2.63055,55.25500|-2.46305,55.36111|-2.21236,55.42777|-2.18278,55.45985|-2.21528,55.50583|-2.27416,55.57527|-2.27916,55.64472|-2.22000,55.66499|-2.08361,55.78054|-2.02166,55.80611';
					break;
				case 'Ireland':
					$coordsAsStr[] = '-8.17166,54.46388|-8.06555,54.37277|-7.94139,54.29944|-7.87576,54.28499|-7.86834,54.22764|-7.81805,54.19916|-7.69972,54.20250|-7.55945,54.12694|-7.31334,54.11250|-7.14584,54.22527|-7.17555,54.28916|-7.16084,54.33666|-7.05834,54.41000|-6.97445,54.40166|-6.92695,54.37916|-6.87305,54.34208|-6.85111,54.28972|-6.73473,54.18361|-6.65556,54.06527|-6.60584,54.04444|-6.44750,54.05833|-6.33889,54.11555|-6.26697,54.09983|-6.17403,54.07222|-6.10834,54.03638|-6.04389,54.03139|-5.96834,54.06389|-5.88500,54.11639|-5.87347,54.20916|-5.82500,54.23958|-5.74611,54.24806|-5.65556,54.22701|-5.60834,54.24972|-5.55916,54.29084|-5.57334,54.37704|-5.64502,54.49267|-5.70472,54.53361|-5.68055,54.57306|-5.59972,54.54194|-5.55097,54.50083|-5.54216,54.44903|-5.54643,54.40527|-5.50672,54.36444|-5.46111,54.38555|-5.43132,54.48596|-5.47945,54.53638|-5.53521,54.65090|-5.57431,54.67722|-5.62916,54.67945|-5.73674,54.67383|-5.80305,54.66138|-5.88257,54.60652|-5.92445,54.63180|-5.86681,54.68972|-5.81903,54.70972|-5.74672,54.72452|-5.68775,54.76335|-5.70931,54.83166|-5.74694,54.85361|-5.79139,54.85139|-6.03611,55.05778|-6.04250,55.10277|-6.03444,55.15458|-6.10125,55.20945|-6.14584,55.22069|-6.25500,55.21194|-6.37639,55.23916|-6.51556,55.23305|-6.61334,55.20722|-6.73028,55.18027|-6.82472,55.16806|-6.88972,55.16777|-6.96695,55.15611|-6.99416,55.11027|-7.05139,55.04680|-7.09500,55.03694|-7.25251,55.07059|-7.32639,55.04527|-7.40639,54.95333|-7.45805,54.85777|-7.55334,54.76277|-7.73916,54.71054|-7.82576,54.73416|-7.92639,54.70054|-7.85236,54.63388|-7.77750,54.62694|-7.83361,54.55389|-7.95084,54.53222|-8.04695,54.50722|-8.17166,54.46388';
					break;
				case 'Wales':
					$coordsAsStr[] = '-3.08860,53.26001|-3.33639,53.34722|-3.38806,53.34361|-3.60986,53.27944|-3.73014,53.28944|-3.85445,53.28444|-4.01861,53.23750|-4.06639,53.22639|-4.15334,53.22556|-4.19639,53.20611|-4.33028,53.11222|-4.36097,53.02888|-4.55278,52.92889|-4.61889,52.90916|-4.72195,52.83611|-4.72778,52.78139|-4.53945,52.79306|-4.47722,52.85500|-4.41416,52.88472|-4.31292,52.90499|-4.23334,52.91499|-4.13569,52.87888|-4.13056,52.77777|-4.05334,52.71666|-4.10639,52.65084|-4.12597,52.60375|-4.08056,52.55333|-4.05972,52.48584|-4.09666,52.38583|-4.14305,52.32027|-4.19361,52.27638|-4.23166,52.24888|-4.52722,52.13083|-4.66945,52.13027|-4.73695,52.10361|-4.76778,52.06444|-4.84445,52.01388|-5.09945,51.96056|-5.23916,51.91638|-5.25889,51.87056|-5.18500,51.86958|-5.11528,51.83333|-5.10257,51.77895|-5.16111,51.76222|-5.24694,51.73027|-5.19111,51.70888|-5.00739,51.70349|-4.90875,51.71249|-4.86111,51.71334|-4.97061,51.67577|-5.02128,51.66861|-5.05139,51.62028|-5.00528,51.60638|-4.94139,51.59416|-4.89028,51.62694|-4.83569,51.64534|-4.79063,51.63340|-4.69028,51.66666|-4.64584,51.72666|-4.57445,51.73416|-4.43611,51.73722|-4.26222,51.67694|-4.19750,51.67916|-4.06614,51.66804|-4.11639,51.63416|-4.17750,51.62235|-4.25055,51.62861|-4.29208,51.60743|-4.27778,51.55666|-4.20486,51.53527|-3.94972,51.61278|-3.83792,51.61999|-3.78166,51.56750|-3.75160,51.52931|-3.67194,51.47388|-3.54250,51.39777|-3.40334,51.37972|-3.27097,51.38014|-3.16458,51.40909|-3.15166,51.45305|-3.11875,51.48750|-3.02111,51.52527|-2.95472,51.53972|-2.89278,51.53861|-2.84778,51.54500|-2.71472,51.58083|-2.66500,51.61500|-2.68666,51.71889|-2.68334,51.76957|-2.65944,51.81806|-2.77861,51.88583|-2.86639,51.92889|-2.91757,51.91569|-2.98889,51.92555|-3.04528,51.97639|-3.08500,52.01930|-3.11250,52.06945|-3.12222,52.11805|-3.07555,52.14804|-3.05125,52.23347|-2.99750,52.28139|-2.95486,52.33117|-3.02195,52.34027|-3.19611,52.41027|-3.19514,52.46722|-3.11916,52.49194|-3.02736,52.49792|-2.98028,52.53083|-3.00792,52.56902|-3.07089,52.55702|-3.11750,52.58666|-3.06666,52.63527|-3.01111,52.71166|-3.06806,52.77027|-3.13708,52.79312|-3.13014,52.88486|-3.08639,52.91611|-2.99389,52.95361|-2.85069,52.93875|-2.79278,52.90207|-2.71945,52.91902|-2.73109,52.96873|-2.77792,52.98514|-2.85695,53.03249|-2.89389,53.10416|-2.91069,53.17014|-2.95528,53.21555|-3.02000,53.24722|-3.08860,53.26001';
					break;
				case 'NC':
					$coordsAsStr[] = '-81.65876,36.60938|-81.70390,36.55513|-81.70639,36.50804|-81.74665,36.39777|-81.90723,36.30804|-82.03195,36.12694|-82.08416,36.10146|-82.12826,36.11020|-82.21500,36.15833|-82.36375,36.11347|-82.43472,36.06013|-82.46236,36.01708|-82.56006,35.96263|-82.60042,35.99638|-82.62308,36.06121|-82.73500,36.01833|-82.84612,35.94944|-82.90451,35.88819|-82.93555,35.83846|-83.16000,35.76236|-83.24222,35.71944|-83.49222,35.57111|-83.56847,35.55861|-83.64416,35.56471|-83.73499,35.56638|-83.88222,35.51791|-83.98361,35.44944|-84.03639,35.35444|-84.04964,35.29117|-84.09042,35.25986|-84.15084,35.25388|-84.20521,35.25722|-84.29284,35.22596|-84.32471,34.98701|-83.09778,35.00027|-82.77722,35.09138|-82.59639,35.14972|-82.37999,35.21500|-82.27362,35.20583|-81.41306,35.17416|-81.05915,35.15333|-80.92666,35.10695|-80.78751,34.95610|-80.79334,34.82555|-79.66777,34.80694|-79.11555,34.34527|-78.57222,33.88166|-78.51806,33.87999|-78.43721,33.89804|-78.23735,33.91986|-78.15389,33.91471|-78.06974,33.89500|-78.02597,33.88936|-77.97611,33.94276|-77.95299,33.99243|-77.94499,34.06499|-77.92728,34.11756|-77.92250,33.99194|-77.92264,33.93715|-77.88215,34.06166|-77.86222,34.15083|-77.83501,34.19194|-77.75724,34.28527|-77.68222,34.36555|-77.63667,34.39805|-77.57363,34.43694|-77.45527,34.50403|-77.38173,34.51646|-77.37905,34.56294|-77.38572,34.61260|-77.40944,34.68916|-77.38847,34.73304|-77.33097,34.63992|-77.35024,34.60099|-77.30958,34.55972|-77.09424,34.67742|-76.75994,34.76659|-76.68325,34.79749|-76.66097,34.75781|-76.62611,34.71014|-76.50063,34.73617|-76.48138,34.77638|-76.38305,34.86423|-76.34326,34.88194|-76.27181,34.96263|-76.35125,35.02221|-76.32354,34.97429|-76.45319,34.93524|-76.43395,34.98782|-76.45356,35.06676|-76.52917,35.00444|-76.63382,34.98242|-76.69722,34.94887|-76.75306,34.90526|-76.81636,34.93944|-76.89000,34.95388|-76.93180,34.96957|-76.96501,34.99777|-77.06816,35.14978|-76.97639,35.06806|-76.86722,35.00000|-76.80531,34.98559|-76.72708,35.00152|-76.60402,35.07416|-76.56555,35.11486|-76.57305,35.16013|-76.66489,35.16694|-76.56361,35.23361|-76.48750,35.22582|-76.46889,35.27166|-76.50298,35.30791|-76.83251,35.39222|-77.02305,35.48694|-77.04958,35.52694|-76.91292,35.46166|-76.65250,35.41499|-76.61611,35.45888|-76.63195,35.52249|-76.58820,35.55104|-76.51556,35.53194|-76.56711,35.48494|-76.52251,35.40416|-76.46195,35.37221|-76.13319,35.35986|-76.04111,35.42416|-76.00223,35.46610|-75.97958,35.51666|-75.89362,35.57555|-75.83834,35.56694|-75.78944,35.57138|-75.74076,35.61846|-75.72084,35.69263|-75.72084,35.81451|-75.74917,35.87791|-75.78333,35.91972|-75.85083,35.97527|-75.94333,35.91777|-75.98944,35.88054|-75.98854,35.79110|-75.99388,35.71027|-76.02875,35.65409|-76.10320,35.66041|-76.13563,35.69239|-76.04475,35.68436|-76.04167,35.74916|-76.05305,35.79361|-76.05305,35.87375|-76.02653,35.96222|-76.07751,35.99319|-76.17472,35.99596|-76.27917,35.91915|-76.37986,35.95763|-76.42014,35.97874|-76.55375,35.93971|-76.66222,35.93305|-76.72952,35.93984|-76.73392,36.04760|-76.75384,36.09477|-76.76028,36.14513|-76.74610,36.22818|-76.70458,36.24673|-76.72764,36.16736|-76.71021,36.11752|-76.69117,36.07165|-76.65979,36.03312|-76.49527,36.00958|-76.37138,36.07694|-76.37084,36.14999|-76.21417,36.09471|-76.07591,36.17910|-76.18361,36.26915|-76.19965,36.31739|-76.13986,36.28805|-76.04274,36.21974|-76.00465,36.18110|-75.95287,36.19241|-75.97604,36.31138|-75.93895,36.28381|-75.85271,36.11069|-75.79315,36.07385|-75.79639,36.11804|-75.88333,36.29554|-75.94665,36.37194|-75.98694,36.41166|-76.03473,36.49666|-76.02899,36.55000|-78.44234,36.54986|-78.56594,36.55799|-80.27556,36.55110|-81.15361,36.56499|-81.38722,36.57695|-81.65876,36.60938';
					break;
				default:
			}
			?>
			var coordStr = <?= json_encode($coordsAsStr) ?>;
			jQuery.each(coordStr, function(index, value) {
				var coordXY = value.split('|');
				var coords  = [];
				var points  = [];
				jQuery.each(coordXY, function(i, v) {
					coords = v.split(',');
					points.push(new google.maps.LatLng(parseFloat(coords[1]), parseFloat(coords[0])));
				});
				// Construct the polygon
				new google.maps.Polygon({
					paths:         points,
					strokeColor:   "#888888",
					strokeOpacity: 0.8,
					strokeWeight:  1,
					fillColor:     "#ff0000",
					fillOpacity:   0.15
				}).setMap(map);
			});
		}

		function loadMap(zoom, mapType) {
			var mapTyp;

			if (mapType) {
				mapTyp = mapType;
			} else {
				mapTyp = google.maps.MapTypeId.ROADMAP;
			}
			geocoder = new google.maps.Geocoder();
			if (!zoom) {
				zoom = pl_zoom;
			}
			// Define map
			var myOptions = {
				zoom: zoom,
				center: latlng,
				mapTypeId: mapTyp,// ROADMAP, SATELLITE, HYBRID, TERRAIN
				// mapTypeId: google.maps.MapTypeId.ROADMAP, // ROADMAP, SATELLITE, HYBRID, TERRAIN
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
				},
				navigationControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT, // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
				style: google.maps.NavigationControlStyle.SMALL // ANDROID, DEFAULT, SMALL, ZOOM_PAN
				},
				scrollwheel: true
			};

			map = new google.maps.Map(document.querySelector('.gm-map'), myOptions);

			overlays();

			// Close any infowindow when map is clicked
			google.maps.event.addListener(map, 'click', function() {
				infowindow.close();
			});

			// Check for zoom changes
			google.maps.event.addListener(map, 'zoom_changed', function() {
				document.editplaces.NEW_ZOOM_FACTOR.value = map.zoom;
			});

			// Create the Main Location Marker
			<?php
			if ($level < 3 && $record->pl_icon != '') {
				echo 'var image = {
						"url"    : "' . WT_MODULES_DIR .  'googlemap/" + "' . $record->pl_icon . '",
						"size"   : new google.maps.Size(25, 15),
						"origin" : new google.maps.Point(0, 0),
						"anchor" : new google.maps.Point(12, 15)
					};';
				echo 'marker = new google.maps.Marker({';
				echo 'icon: image,';
				echo 'position: latlng,';
				echo 'map: map,';
				echo 'title: pl_name,';
				echo 'draggable: true,';
				echo 'zIndex:1';
				echo '});';
			} else {
				echo 'marker = new google.maps.Marker({';
				echo 'position: latlng,';
				echo 'map: map,';
				echo 'title: pl_name,';
				echo 'draggable: true,';
				echo 'zIndex: 1';
				echo '});';
			}
			?>

			// Set marker by clicking on map ---
			google.maps.event.addListener(map, 'click', function(event) {
				clearMarks();
				latlng = event.latLng;
				marker = new google.maps.Marker({
					position: latlng,
					map: map,
					title: pl_name,
					draggable: true,
					zIndex: 1
				});
				document.getElementById('NEW_PLACE_LATI').value = marker.getPosition().lat().toFixed(5);
				document.getElementById('NEW_PLACE_LONG').value = marker.getPosition().lng().toFixed(5);
				updateMap('flag_drag');
				var currzoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);
				mapType = map.getMapTypeId();
				loadMap(currzoom, mapType);
			});

			// If the marker is moved, update the location fields
			google.maps.event.addListener(marker, 'drag', function() {
				document.getElementById('NEW_PLACE_LATI').value = marker.getPosition().lat().toFixed(5);
				document.getElementById('NEW_PLACE_LONG').value = marker.getPosition().lng().toFixed(5);
			});
			google.maps.event.addListener(marker, 'dragend', function() {
				updateMap('flag_drag');
			});
		}

		function clearMarks() {
			marker.setMap(null);
		}

		/**
		 * Called when we select one of the search results.
		 *
		 * @param lat
		 * @param lng
		 */
		function setLoc(lat, lng) {
			if (lat < 0.0) {
				document.editplaces.NEW_PLACE_LATI.value = (lat.toFixed(5) * -1);
				document.editplaces.LATI_CONTROL.value = 'S';
			} else {
				document.editplaces.NEW_PLACE_LATI.value = lat.toFixed(5);
				document.editplaces.LATI_CONTROL.value = 'N';
			}
			if (lng < 0.0) {
				document.editplaces.NEW_PLACE_LONG.value = (lng.toFixed(5) * -1);
				document.editplaces.LONG_CONTROL.value = 'W';
			} else {
				document.editplaces.NEW_PLACE_LONG.value = lng.toFixed(5);
				document.editplaces.LONG_CONTROL.value = 'E';
			}
			new google.maps.LatLng (lat.toFixed(5), lng.toFixed(5));
			infowindow.close();
			updateMap();
		}

		function createMarker(i, point, name) {
			 var image = {
				 url:    WT_MODULES_DIR + 'googlemap/images/marker_yellow.png',
				 size:   new google.maps.Size(20, 34),
				 origin: new google.maps.Point(0, 0),
				 anchor: new google.maps.Point(10, 34)
			 };

			var marker = new google.maps.Marker({
				icon:     image,
				map:      map,
				position: point,
				zIndex:   0
			});

			google.maps.event.addListener(marker, 'click', function() {
				infowindow.close();
				infowindow.setContent(name);
				infowindow.open(map, marker);
			});

			google.maps.event.addListener(map, 'click', function() {
				infowindow.close();
			});

			return marker;
		}

		function change_icon() {
			window.open('module.php?mod=googlemap&mod_action=admin_flags&countrySelected=', '_blank', indx_window_specs);
			return false;
		}

		function remove_icon() {
			document.editplaces.icon.value = '';
			document.getElementById('flagsDiv').innerHTML = '<a href="#" onclick="change_icon();return false;"><?= I18N::translate('Change flag') ?></a>';
		}

		function addAddressToMap(response) {
			var bounds = new google.maps.LatLngBounds();
			if (!response) {
				alert('<?= I18N::translate('No places found') ?>');
			} else {
				if (response.length > 0) {
					for (var i=0; i<response.length; i++) {
						// 5 decimal places is approx 1 metre accuracy.
						var name =
							'<div>' + response[i].address_components[0].short_name +
							'<br><a href="#" onclick="setLoc(' + response[i].geometry.location.lat() + ', ' + response[i].geometry.location.lng() + ');">' +
							'<?= I18N::translate('Use this value') ?></a>' +
							'</div>';
						var point = response[i].geometry.location;
						var marker = createMarker(i, point, name);
						bounds.extend(response[i].geometry.location);
					}

					<?php if ($level > 0) { ?>
						map.fitBounds(bounds);
					<?php } ?>
					var zoomlevel = map.getZoom();

					if (zoomlevel < <?= $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) ?>) {
						zoomlevel = <?= $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) ?>;
					}
					if (zoomlevel > <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>) {
						zoomlevel = <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>;
					}
					if (document.editplaces.NEW_ZOOM_FACTOR.value < zoomlevel) {
						zoomlevel = document.editplaces.NEW_ZOOM_FACTOR.value;
						if (zoomlevel < <?= $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) ?>) {
							zoomlevel = <?= $this->getPreference('GM_MIN_ZOOM', self::GM_MIN_ZOOM_DEFAULT) ?>;
						}
						if (zoomlevel > <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>) {
							zoomlevel = <?= $this->getPreference('GM_MAX_ZOOM', self::GM_MAX_ZOOM_DEFAULT) ?>;
						}
					}
					map.setCenter(bounds.getCenter());
					map.setZoom(zoomlevel);
				}
			}
		}

		function showLocation_level(address) {
			<?php if ($level > 0) { ?>
				address += '<?= ', ' . addslashes(implode(', ', array_reverse($where_am_i, true))) ?>';
			<?php } ?>
				geocoder.geocode({'address': address}, addAddressToMap);
		}

		function showLocation_all(address) {
			geocoder.geocode({'address': address}, addAddressToMap);
		}

		function paste_char(value) {
			document.editplaces.NEW_PLACE_NAME.value += value;
		}
		window.onload = function() {
			loadMap();
		};
	</script>

		<form method="post" id="editplaces" name="editplaces" action="module.php?mod=googlemap&amp;mod_action=admin_place_save">
			<input type="hidden" name="place_id" value="<?= $place_id ?>">
			<input type="hidden" name="level" value="<?= $level ?>">
			<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
			<input type="hidden" name="place_long" value="<?= $longitude ?>">
			<input type="hidden" name="place_lati" value="<?= $latitude ?>">

			<div class="form-group row">
				<div class="col-sm-10 offset-sm-1">
					<div class="gm-map" style="width: 100%; height: 350px;"></div>
				</div>
			</div>

			<div class="form-group row">
				<label class="col-form-label col-sm-2">
					<?= I18N::translate('Place') ?>
				</label>
				<div class="col-sm-6">
					<input type="text" id="new_pl_name" name="NEW_PLACE_NAME" value="<?= e($record->pl_place) ?>" class="form-control" required>

					<label for="new_pl_name">
						<a href="#" onclick="showLocation_all(document.getElementById('new_pl_name').value); return false">
							<?= I18N::translate('Search globally') ?>
						</a>
					</label>
					|
					<label for="new_pl_name">
						<a href="#" onclick="showLocation_level(document.getElementById('new_pl_name').value); return false">
							<?= I18N::translate('Search locally') ?>
						</a>
					</label>
				</div>

				<label class="col-form-label col-sm-2" for="NEW_ZOOM_FACTOR">
					<?= I18N::translate('Zoom level') ?>
				</label>
				<div class="col-sm-2">
					<input type="text" id="NEW_ZOOM_FACTOR" name="NEW_ZOOM_FACTOR" value="<?= $record->pl_zoom ?>" class="form-control" onchange="updateMap();" required readonly>
				</div>
			</div class="form-group row">

			<div class="form-group row">
				<label class="col-form-label col-sm-2">
					<?= I18N::translate('Latitude') ?>
				</label>
				<div class="col-sm-4">
					<div class="input-group">
						<input type="text" id="NEW_PLACE_LATI" name="NEW_PLACE_LATI" placeholder="<?= /* I18N: Measure of latitude/longitude */ I18N::translate('degrees') ?>" value="<?= abs($latitude) ?>" class="form-control" onchange="updateMap();" required>
						<select name="LATI_CONTROL" id="LATI_CONTROL" onchange="updateMap();" class="form-control">
							<option value="N"<?= $latitude >= 0 ? ' selected' : '' ?>>
								<?= I18N::translate('north') ?>
							</option>
							<option value="S"<?= $latitude < 0 ? ' selected' : '' ?>>
								<?= I18N::translate('south') ?>
							</option>
						</select>
					</div>
				</div>

				<label class="col-form-label col-sm-2">
					<?= I18N::translate('Longitude') ?>
				</label>
				<div class="col-sm-4">
					<div class="input-group">
						<input type="text" id="NEW_PLACE_LONG" name="NEW_PLACE_LONG" placeholder="<?= I18N::translate('degrees') ?>" value="<?= abs($longitude) ?>" class="form-control" onchange="updateMap();" required>
						<select name="LONG_CONTROL" id="LONG_CONTROL" onchange="updateMap();" class="form-control">
							<option value="E"<?= $longitude >= 0 ? ' selected' : '' ?>>
								<?= I18N::translate('east') ?>
							</option>
							<option value="W"<?= $longitude < 0 ? ' selected' : '' ?>>
								<?= I18N::translate('west') ?>
							</option>
						</select>
					</div>
				</div>
			</div>

			<div class="row form-group">
				<label class="col-form-label col-sm-2" for="icon">
					<?= I18N::translate('Flag') ?>
				</label>
				<div class="col-sm-10">
					<div class="input-group" dir="ltr">
						<div class="input-group-prepend">
						<span class="input-group-text">
						<?= WT_MODULES_DIR ?>googlemap/places/flags/
						</span>
						</div>
						<?= FunctionsEdit::formControlFlag($record->pl_icon, ['name' => 'icon', 'id' => 'icon', 'class' => 'form-control']) ?>
					</div>
				</div>
			</div>

			<div class="row form-group">
				<div class="col-sm-10 offset-sm-2">
					<button class="btn btn-primary" type="submit">
						<?= /* I18N: A button label. */ I18N::translate('save') ?>
					</button>
					<a class="btn btn-secondary" href="<?= $parent_url ?>">
						<?= /* I18N: A button label. */ I18N::translate('cancel') ?>
					</a>
				</div>
			</div>
		</form>
		<?php
	}

	/**
	 * Places administration.
	 */
	private function adminPlaces() {
		global $WT_TREE;

		$action    = Filter::get('action');
		$parent_id = (int) Filter::get('parent_id');
		$inactive  = Filter::getBool('inactive');

		$controller = new PageController;
		$controller
			->setPageTitle(I18N::translate('Geographic data'))
			->pageHeader();

		$breadcrumbs = [
			route('admin-control-panel') => I18N::translate('Control panel'),
			route('admin-modules')       => I18N::translate('Module administration'),
			$this->getConfigLink()     => $this->getTitle(),
		];
		$hierarchy =
			[0 => I18N::translate('Geographic data')] +
			$this->placeIdToHierarchy($parent_id);
		foreach (array_slice($hierarchy, 0, -1, true) as $id => $name) {
			$breadcrumbs += ['module.php?mod=googlemap&mod_action=admin_places&parent_id=' . $id . '&inactive=' . $inactive => e($name)];
		}
		echo Bootstrap4::breadcrumbs($breadcrumbs, end($hierarchy));

		if ($action == 'ImportGedcom') {
			echo '<h2>' . I18N::translate('Geographic data') . '</h2>';
			$placelist      = [];
			$j              = 0;
			$gedcom_records =
				Database::prepare("SELECT i_gedcom FROM `##individuals` WHERE i_file=? UNION ALL SELECT f_gedcom FROM `##families` WHERE f_file=?")
				->execute([$WT_TREE->getTreeId(), $WT_TREE->getTreeId()])
				->fetchOneColumn();
			foreach ($gedcom_records as $gedrec) {
				$i        = 1;
				$placerec = Functions::getSubRecord(2, '2 PLAC', $gedrec, $i);
				while (!empty($placerec)) {
					if (preg_match('/2 PLAC (.+)/', $placerec, $match)) {
						$placelist[$j]          = [];
						$placelist[$j]['place'] = trim($match[1]);
						if (preg_match('/4 LATI (.*)/', $placerec, $match)) {
							$placelist[$j]['lati'] = trim($match[1]);
							if (($placelist[$j]['lati'][0] != 'N') && ($placelist[$j]['lati'][0] != 'S')) {
								if ($placelist[$j]['lati'] < 0) {
									$placelist[$j]['lati'][0] = 'S';
								} else {
									$placelist[$j]['lati'] = 'N' . $placelist[$j]['lati'];
								}
							}
						} else {
							$placelist[$j]['lati'] = null;
						}
						if (preg_match('/4 LONG (.*)/', $placerec, $match)) {
							$placelist[$j]['long'] = trim($match[1]);
							if (($placelist[$j]['long'][0] != 'E') && ($placelist[$j]['long'][0] != 'W')) {
								if ($placelist[$j]['long'] < 0) {
									$placelist[$j]['long'][0] = 'W';
								} else {
									$placelist[$j]['long'] = 'E' . $placelist[$j]['long'];
								}
							}
						} else {
							$placelist[$j]['long'] = null;
						}
						$j = $j + 1;
					}
					$i        = $i + 1;
					$placerec = Functions::getSubRecord(2, '2 PLAC', $gedrec, $i);
				}
			}
			asort($placelist);

			$prevPlace     = '';
			$prevLati      = '';
			$prevLong      = '';
			$placelistUniq = [];
			$j             = 0;
			foreach ($placelist as $k => $place) {
				if ($place['place'] != $prevPlace) {
					$placelistUniq[$j]          = [];
					$placelistUniq[$j]['place'] = $place['place'];
					$placelistUniq[$j]['lati']  = $place['lati'];
					$placelistUniq[$j]['long']  = $place['long'];
					$j                          = $j + 1;
				} elseif (($place['place'] == $prevPlace) && (($place['lati'] != $prevLati) || ($place['long'] != $prevLong))) {
					if (($placelistUniq[$j - 1]['lati'] == 0) || ($placelistUniq[$j - 1]['long'] == 0)) {
						$placelistUniq[$j - 1]['lati'] = $place['lati'];
						$placelistUniq[$j - 1]['long'] = $place['long'];
					} elseif (($place['lati'] != '0') || ($place['long'] != '0')) {
						echo 'Difference: previous value = ', $prevPlace, ', ', $prevLati, ', ', $prevLong, ' current = ', $place['place'], ', ', $place['lati'], ', ', $place['long'], '<br>';
					}
				}
				$prevPlace = $place['place'];
				$prevLati  = $place['lati'];
				$prevLong  = $place['long'];
			}

			$highestIndex = $this->getHighestIndex();

			$default_zoom_level = [4, 7, 10, 12];
			foreach ($placelistUniq as $k => $place) {
				$parent     = preg_split('/ *, */', $place['place']);
				$parent     = array_reverse($parent);
				$parent_id  = 0;
				$num_parent = count($parent);
				for ($i = 0; $i < $num_parent; $i++) {
					if (!isset($default_zoom_level[$i])) {
						$default_zoom_level[$i] = $default_zoom_level[$i - 1];
					}
					$escparent = $parent[$i];
					if ($escparent == '') {
						$escparent = 'Unknown';
					}
					$row =
						Database::prepare("SELECT pl_id, pl_long, pl_lati, pl_zoom FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ?")
						->execute([$i, $parent_id, $escparent])
						->fetchOneRow();
					if ($i < $num_parent - 1) {
						// Create higher-level places, if necessary
						if (empty($row)) {
							$highestIndex++;
							Database::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom) VALUES (?, ?, ?, ?, ?)")
								->execute([$highestIndex, $parent_id, $i, $escparent, $default_zoom_level[$i]]);
							echo e($escparent), '<br>';
							$parent_id = $highestIndex;
						} else {
							$parent_id = $row->pl_id;
						}
					} else {
						// Create lowest-level place, if necessary
						if (empty($row->pl_id)) {
							$highestIndex++;
							Database::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom) VALUES (?, ?, ?, ?, ?, ?, ?)")
								->execute([$highestIndex, $parent_id, $i, $escparent, $place['long'], $place['lati'], $default_zoom_level[$i]]);
							echo e($escparent), '<br>';
						} else {
							if (empty($row->pl_long) && empty($row->pl_lati) && $place['lati'] != '0' && $place['long'] != '0') {
								Database::prepare("UPDATE `##placelocation` SET pl_lati=?, pl_long=? WHERE pl_id=?")
									->execute([$place['lati'], $place['long'], $row->pl_id]);
								echo e($escparent), '<br>';
							}
						}
					}
				}
			}
		}

		$placelist = $this->getPlaceListLocation($parent_id, $inactive);
		?>
		<form class="form-inline">
		<input type="hidden" name="mod" value="googlemap">
		<input type="hidden" name="mod_action" value="admin_places">
		<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
			<?= Bootstrap4::checkbox(I18N::translate('Show inactive places'), false, ['name' => 'inactive', 'checked' => $inactive, 'onclick' => 'this.form.submit()']) ?>
			<p class="small text-muted">
				<?= I18N::translate('By default, the list shows only those places which can be found in your family trees. You may have details for other places, such as those imported in bulk from an external file. Selecting this option will show all places, including ones that are not currently used.') ?>
				<?= I18N::translate('If you have a large number of inactive places, it can be slow to generate the list.') ?>
			</p>
		</form>

		<div class="gm_plac_edit">
			<table class="table table-bordered table-sm table-hover">
				<thead>
					<tr>
						<th><?= I18N::translate('Place') ?></th>
						<th><?= I18N::translate('Latitude') ?></th>
						<th><?= I18N::translate('Longitude') ?></th>
						<th><?= I18N::translate('Zoom level') ?></th>
						<th><?= I18N::translate('Icon') ?> / <?= I18N::translate('Flag') ?></th>
						<th><?= I18N::translate('Edit') ?></th>
						<th><?= I18N::translate('Delete') ?></th>
					</tr>
				</thead>
				<tbody>

				<?php foreach ($placelist as $place): ?>
					<?php $noRows = Database::prepare("SELECT COUNT(pl_id) FROM `##placelocation` WHERE pl_parent_id=?")->execute([$place['place_id']])->fetchOne(); ?>
					<tr>
						<td>
							<a href="module.php?mod=googlemap&mod_action=admin_places&amp;parent_id=<?= $place['place_id'] ?>&inactive=<?= $inactive ?>">
								<?php if ($place['place'] === 'Unknown'): ?>
									<?= I18N::translate('unknown') ?>
								<?php else: ?>
									<?= e($place['place']) ?>
								<?php endif ?>
								<?php if ($place['missing'] > 0): ?>
								<span class="badge badge-pill badge-warning">
									<?= I18N::number($place['children']) ?>
								</span>
								<?php elseif ($place['children'] > 0): ?>
								<span class="badge badge-pill badge-default">
									<?= I18N::number($place['children']) ?>
								</span>
								<?php endif ?>
							</a>
						</td>
						<td>
							<?= $place['is_empty'] ? FontAwesome::decorativeIcon('warning') : $place['lati'] ?>
						</td>
						<td>
							<?= $place['is_empty'] ? FontAwesome::decorativeIcon('warning') : $place['long'] ?>
						</td>
						<td>
							<?= $place['zoom'] ?>
						</td>
						<td>
								<?php if ($place['icon']): ?>
									<img src="<?= WT_MODULES_DIR ?>googlemap/places/flags/<?= e($place['icon']) ?>" width="25" height="15" title="<?= e($place['icon']) ?>" alt="<?= I18N::translate('Flag') ?>">
								<?php else: ?>
									<img src="<?= WT_MODULES_DIR ?>googlemap/images/mm_20_red.png">
								<?php endif ?>
						</td>
						<td>
							<?= FontAwesome::linkIcon('edit', I18N::translate('Edit'), ['href' => 'module.php?mod=googlemap&mod_action=admin_place_edit&action=update&place_id=' . $place['place_id'] . '&parent_id=' . $place['parent_id'], 'class' => 'btn btn-primary']) ?>
						</td>
						<td>
							<?php if ($noRows == 0): ?>
								<form method="POST" action="module.php?mod=googlemap&amp;mod_action=admin_delete_action" data-confirm="<?= I18N::translate('Remove this location?') ?>" onsubmit="return confirm(this.dataset.confirm)">
									<input type="hidden" name="parent_id" value="<?= $parent_id ?>">
									<input type="hidden" name="place_id" value="<?= $place['place_id'] ?>">
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
							<a href="module.php?mod=googlemap&mod_action=admin_place_edit&parent_id=<?= $parent_id ?>" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('add') ?>
								<?= /* I18N: A button label. */ I18N::translate('add') ?>
							</a>

							<a href="module.php?mod=googlemap&mod_action=admin_download&parent_id=<?= $parent_id ?>" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('download') ?>
								<?= /* I18N: A button label. */ I18N::translate('download') ?>
							</a>

							<a href="module.php?mod=googlemap&amp;mod_action=admin_upload&amp;parent_id=<?= $parent_id ?>&amp;inactive=<?= $inactive ?>" class="btn btn-primary">
								<?= FontAwesome::decorativeIcon('upload') ?>
								<?= /* I18N: A button label. */ I18N::translate('upload') ?>
							</a>
						</td>
					</tr>
				</tfoot>
			</table>
		</div>

		<hr>

		<form class="form-horizontal" action="module.php">
			<input type="hidden" name="mod" value="googlemap">
			<input type="hidden" name="mod_action" value="admin_places">
			<input type="hidden" name="action" value="ImportGedcom">
			<div class="row form-group">
				<label class="form-control-static col-sm-4" for="ged">
					<?= I18N::translate('Import all places from a family tree') ?>
				</label>
				<div class="col-sm-6">
					<?= Bootstrap4::select(Tree::getNameList(), $WT_TREE->getName(), ['id' => 'ged', 'name' => 'ged']) ?>
				</div>
				<div class="col-sm-2">
					<button type="submit" class="btn btn-primary">
						<?= FontAwesome::decorativeIcon('add') ?>
						<?= /* I18N: A button label. */ I18N::translate('import') ?>
					</button>
				</div>
			</div>
		</form>
		<?php
	}
}
