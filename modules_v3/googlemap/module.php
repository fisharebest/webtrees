<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2010 John Finlay
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\Log;

// http://www.google.com/permissions/guidelines.html
//
// "... an unregistered Google Brand Feature should be followed by
// the superscripted letters TM or SM ..."
//
// Hence, use "Google Maps™"
//
// "... Use the trademark only as an adjective"
//
// "... Use a generic term following the trademark, for example:
// GOOGLE search engine, Google search"
//
// Hence, use "Google Maps™ mapping service" where appropriate.

class googlemap_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	public function __construct() {
		parent::__construct();

		// Create GM tables, if not already present
		try {
			WT_DB::updateSchema(WT_ROOT.WT_MODULES_DIR.'/googlemap/db_schema/', 'GM_SCHEMA_VERSION', 5);
		} catch (PDOException $ex) {
			// The schema update scripts should never fail.  If they do, there is no clean recovery.
			die($ex);
		}

		// Set default values
		try {
			// TODO: do this once only, in a db_schema upgrade script?
			$this->setSetting('GM_MAP_TYPE',         $this->getSetting('GM_MAP_TYPE',         'G_NORMAL_MAP'));  // G_PHYSICAL_MAP, G_NORMAL_MAP, G_SATELLITE_MAP, G_HYBRID_MAP
			$this->setSetting('GM_MAX_ZOOM',         $this->getSetting('GM_MAX_ZOOM',         '20'));     // max zoom level
			$this->setSetting('GM_MIN_ZOOM',         $this->getSetting('GM_MIN_ZOOM',         '2'));      // min zoom level
			$this->setSetting('GM_PRECISION_0',      $this->getSetting('GM_PRECISION_0',      '0'));      // Country level
			$this->setSetting('GM_PRECISION_1',      $this->getSetting('GM_PRECISION_1',      '1'));      // State level
			$this->setSetting('GM_PRECISION_2',      $this->getSetting('GM_PRECISION_2',      '2'));      // City level
			$this->setSetting('GM_PRECISION_3',      $this->getSetting('GM_PRECISION_3',      '3'));      // Neighborhood level
			$this->setSetting('GM_PRECISION_4',      $this->getSetting('GM_PRECISION_4',      '4'));      // House level
			$this->setSetting('GM_PRECISION_5',      $this->getSetting('GM_PRECISION_5',      '5'));      // Max prcision level
			$this->setSetting('GM_XSIZE',            $this->getSetting('GM_XSIZE',            '600'));    // X-size of Google map
			$this->setSetting('GM_YSIZE',            $this->getSetting('GM_YSIZE',            '400'));    // Y-size of Google map
			$this->setSetting('GM_PH_XSIZE',         $this->getSetting('GM_PH_XSIZE',         '500'));    // X-size of Place Hierarchy Google map
			$this->setSetting('GM_PH_YSIZE',         $this->getSetting('GM_PH_YSIZE',         '350'));    // Y-size of Place Hierarchy Google map
			$this->setSetting('GM_PH_MARKER',        $this->getSetting('GM_PH_MARKER',        'G_FLAG')); // Possible values: G_FLAG = Flag, G_DEFAULT_ICON = Standard icon
			$this->setSetting('GM_DISP_SHORT_PLACE', $this->getSetting('GM_DISP_SHORT_PLACE', '0'));      // Display full place name or only the actual level name
		} catch (Exception $ex) {
			// Perhaps the module hasn't been installed yet (no entry in wt_modules)
		}
	}

	// Extend WT_Module
	public function getTitle() {
		return /* I18N: The name of a module.  Google Maps™ is a trademark.  Do not translate it? http://en.wikipedia.org/wiki/Google_maps */ WT_I18N::translate('Google Maps™');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the “Google Maps™” module */ WT_I18N::translate('Show the location of places and events using the Google Maps™ mapping service.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
			$this->config();
			break;
		case 'flags':
			$this->flags();
			break;
		case 'pedigree_map':
			$this->pedigreeMap();
			break;
		case 'admin_placecheck':
			$this->adminPlaceCheck();
			break;
		case 'admin_places':
			$this->adminPlaces();
			break;
		case 'places_edit':
			$this->placesEdit();
			break;
		case 'wt_street_view':
			$this->wtStreetView();
			break;
		default:
			header('HTTP/1.0 404 Not Found');
			break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_config';
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 80;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		ob_start();
		?>
		<script src="<?php echo $this->googleMapsScript(); ?>"></script>
		<script>
			var minZoomLevel   = <?php echo $this->getSetting('GM_MIN_ZOOM'); ?>;
			var maxZoomLevel   = <?php echo $this->getSetting('GM_MAX_ZOOM'); ?>;
			var startZoomLevel = maxZoomLevel;
		</script>
		<?php
		return ob_get_clean();
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $WT_IMAGES, $controller;

		if ($this->checkMapData()) {
			ob_start();
			echo '<link type="text/css" href ="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';
			echo '<table border="0" width="100%"><tr><td>';
			echo '<table width="100%" border="0" class="facts_table">';
			echo '<tr><td valign="top">';
			echo '<div id="map_pane" style="border: 1px solid gray; color: black; width: 100%; height: ', $this->getSetting('GM_YSIZE'), 'px"></div>';
			if (Auth::isAdmin()) {
				echo '<table width="100%"><tr>';
				echo '<td width="40%" align="left">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
				echo '</td>';
				echo '<td width="35%" class="center">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_places">', WT_I18N::translate('Geographic data'), '</a>';
				echo '</td>';
				echo '<td width="25%" align="right">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_placecheck">', WT_I18N::translate('Place check'),'</a>';
				echo '</td>';
				echo '</tr></table>';
			}
			echo '</td>';
			echo '<td valign="top" width="30%">';
			echo '<div id="map_content">';
			$famids = array();
			$families = $controller->record->getSpouseFamilies();
			foreach ($families as $family) {
				$famids[] = $family->getXref();
			}
			$indifacts = array();
			foreach ($controller->record->getFacts() as $fact) {
				$indifacts[] = $fact;
			}
			foreach ($controller->record->getSpouseFamilies() as $family) {
				foreach ($family->getFacts() as $fact) {
					$indifacts[] = $fact;
				}
			}
			sort_facts($indifacts);

			$this->buildIndividualMap($controller->record, $indifacts, $famids);
			echo '</div>';
			echo '</td>';
			echo '</tr></table>';
			// start
			echo '<img src="', $WT_IMAGES['spacer'], '" id="marker6" width="1" height="1" alt="">';
			// end
			echo '</td></tr></table>';
			echo '<script>loadMap();</script>';
			return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
		} else {
			$html='<table class="facts_table">';
			$html.='<tr><td colspan="2" class="facts_value">'.WT_I18N::translate('No map data for this person');
			$html.='</td></tr>';
			if (Auth::isAdmin()) {
				$html.='<tr><td class="center" colspan="2">';
				$html.='<a href="module.php?mod=googlemap&amp;mod_action=admin_config">'.WT_I18N::translate('Google Maps™ preferences'). '</a>';
				$html.='</td></tr>';
			}
			return $html;
		}
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $SEARCH_SPIDER;

		return !$SEARCH_SPIDER && (array_key_exists('googlemap', WT_Module::getActiveModules()) || Auth::isAdmin());
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return false;
	}

	private function config() {
		require WT_ROOT.'includes/functions/functions_edit.php';

		$action = WT_Filter::post('action');

		$controller = new WT_Controller_Page();
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(WT_I18N::translate('Google Maps™'))
			->pageHeader()
			->addInlineJavascript('jQuery("#tabs").tabs();');

		if ($action=='update') {
			$this->setSetting('GM_MAP_TYPE',          WT_Filter::post('NEW_GM_MAP_TYPE'));
			$this->setSetting('GM_USE_STREETVIEW',    WT_Filter::post('NEW_GM_USE_STREETVIEW'));
			$this->setSetting('GM_MIN_ZOOM',          WT_Filter::post('NEW_GM_MIN_ZOOM'));
			$this->setSetting('GM_MAX_ZOOM',          WT_Filter::post('NEW_GM_MAX_ZOOM'));
			$this->setSetting('GM_XSIZE',             WT_Filter::post('NEW_GM_XSIZE'));
			$this->setSetting('GM_YSIZE',             WT_Filter::post('NEW_GM_YSIZE'));
			$this->setSetting('GM_PRECISION_0',       WT_Filter::post('NEW_GM_PRECISION_0'));
			$this->setSetting('GM_PRECISION_1',       WT_Filter::post('NEW_GM_PRECISION_1'));
			$this->setSetting('GM_PRECISION_2',       WT_Filter::post('NEW_GM_PRECISION_2'));
			$this->setSetting('GM_PRECISION_3',       WT_Filter::post('NEW_GM_PRECISION_3'));
			$this->setSetting('GM_PRECISION_4',       WT_Filter::post('NEW_GM_PRECISION_4'));
			$this->setSetting('GM_PRECISION_5',       WT_Filter::post('NEW_GM_PRECISION_5'));
			$this->setSetting('GM_COORD',             WT_Filter::post('NEW_GM_COORD'));
			$this->setSetting('GM_PLACE_HIERARCHY',   WT_Filter::post('NEW_GM_PLACE_HIERARCHY'));
			$this->setSetting('GM_PH_XSIZE',          WT_Filter::post('NEW_GM_PH_XSIZE'));
			$this->setSetting('GM_PH_YSIZE',          WT_Filter::post('NEW_GM_PH_YSIZE'));
			$this->setSetting('GM_PH_MARKER',         WT_Filter::post('NEW_GM_PH_MARKER'));
			$this->setSetting('GM_DISP_SHORT_PLACE',  WT_Filter::post('NEW_GM_DISP_SHORT_PLACE'));

			for ($i=1; $i<=9; $i++) {
				$this->setSetting('GM_PREFIX_'  . $i, WT_Filter::post('NEW_GM_PREFIX_'  . $i));
				$this->setSetting('GM_POSTFIX_' . $i, WT_Filter::post('NEW_GM_POSTFIX_' . $i));
			}

			Log::addConfigurationLog('Googlemap config updated');
		}

		?>
		<table id="gm_config">
			<tr>
				<th>
					<a class="current" href="module.php?mod=googlemap&amp;mod_action=admin_config">
						<?php echo WT_I18N::translate('Google Maps™ preferences'); ?>
					</a>
				</th>
				<th>
					<a href="module.php?mod=googlemap&amp;mod_action=admin_places">
						<?php echo WT_I18N::translate('Geographic data'); ?>
					</a>
				</th>
				<th>
					<a href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">
						<?php echo WT_I18N::translate('Place check'); ?>
					</a>
				</th>
			</tr>
		</table>

		<form method="post" name="configform" action="module.php?mod=googlemap&mod_action=admin_config">
			<input type="hidden" name="action" value="update">
			<div id="tabs">
				<ul>
				<li><a href="#gm_basic"><span><?php echo WT_I18N::translate('Basic'); ?></span></a></li>
					<li><a href="#gm_advanced"><span><?php echo WT_I18N::translate('Advanced'); ?></span></a></li>
					<li><a href="#gm_ph"><span><?php echo WT_I18N::translate('Place hierarchy'); ?></span></a></li>
				</ul>
				<div id="gm_basic">
					<table class="gm_edit_config">
						<tr>
							<th><?php echo WT_I18N::translate('Default map type'); ?></th>
							<td>
								<select name="NEW_GM_MAP_TYPE">
									<option value="ROADMAP" <?php if ($this->getSetting('GM_MAP_TYPE') == 'ROADMAP') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Map'); ?></option>
									<option value="SATELLITE" <?php if ($this->getSetting('GM_MAP_TYPE') == 'SATELLITE') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Satellite'); ?></option>
									<option value="HYBRID" <?php if ($this->getSetting('GM_MAP_TYPE') == 'HYBRID') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Hybrid'); ?></option>
									<option value="TERRAIN" <?php if ($this->getSetting('GM_MAP_TYPE') == 'TERRAIN') echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Terrain'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php echo /* I18N: http://en.wikipedia.org/wiki/Google_street_view */ WT_I18N::translate('Google Street View™'); ?></th>
							<td><?php echo radio_buttons('NEW_GM_USE_STREETVIEW', array(false=>WT_I18N::translate('hide'),true=>WT_I18N::translate('show')), $this->getSetting('GM_USE_STREETVIEW')); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Size of map (in pixels)'); ?></th>
							<td>
								<?php echo WT_I18N::translate('Width'); ?>
								<input type="text" name="NEW_GM_XSIZE" value="<?php echo $this->getSetting('GM_XSIZE'); ?>" size="10">
								<?php echo WT_I18N::translate('Height'); ?>
								<input type="text" name="NEW_GM_YSIZE" value="<?php echo $this->getSetting('GM_YSIZE'); ?>" size="10">
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Zoom level of map'), help_link('GM_MAP_ZOOM','googlemap'); ?></th>
							<td>
								<?php echo WT_I18N::translate('minimum'); ?>: <select name="NEW_GM_MIN_ZOOM">
								<?php for ($j=1; $j < 15; $j++) { ?>
								<option value="<?php echo $j, "\""; if ($this->getSetting('GM_MIN_ZOOM')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
								<?php } ?>
								</select>
								<?php echo WT_I18N::translate('maximum'); ?>: <select name="NEW_GM_MAX_ZOOM">
								<?php for ($j=1; $j < 21; $j++) { ?>
								<option value="<?php echo $j, "\""; if ($this->getSetting('GM_MAX_ZOOM')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
								<?php } ?>
								</select>
							</td>
						</tr>
					</table>
				</div>

				<div id="gm_advanced">
					<table class="gm_edit_config">
						<tr>
							<th colspan="2"><?php echo WT_I18N::translate('Precision of the latitude and longitude'), help_link('GM_PRECISION','googlemap'); ?></th>
							<td>
								<table>
									<tr>
										<td><?php echo WT_I18N::translate('Country'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_0">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($this->getSetting('GM_PRECISION_0')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr>
										<td><?php echo WT_I18N::translate('State'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_1">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($this->getSetting('GM_PRECISION_1')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr>
										<td><?php echo WT_I18N::translate('City'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_2">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($this->getSetting('GM_PRECISION_2')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr><td><?php echo WT_I18N::translate('Neighborhood'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_3">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($this->getSetting('GM_PRECISION_3')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr><td><?php echo WT_I18N::translate('House'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_4">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($this->getSetting('GM_PRECISION_4')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr><td><?php echo WT_I18N::translate('Max'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_5">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($this->getSetting('GM_PRECISION_5')==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
								</table>
							</td>
							<td>&nbsp;</td>
						</tr>
							<th class="gm_prefix" colspan="3"><?php echo WT_I18N::translate('Optional prefixes and suffixes'), help_link('GM_NAME_PREFIX_SUFFIX','googlemap');?></th>
						</tr>
						<tr id="gm_level_titles">
							<th>&nbsp;</th>
							<th><?php echo WT_I18N::translate('Prefixes'); ?></th>
							<th><?php echo WT_I18N::translate('Suffixes'); ?></th>
						<?php for ($level=1; $level < 10; $level++) { ?>
						<tr  class="gm_levels">
							<th>
								<?php
								if ($level==1) {
									echo WT_I18N::translate('Country');
								} else {
									echo WT_I18N::translate('Level'), " ", $level;
								}
								?>
							</th>
							<td><input type="text" size="30" name="NEW_GM_PREFIX_<?php echo $level; ?>" value="<?php echo $this->getSetting('GM_PREFIX_' . $level); ?>"></td>
							<td><input type="text" size="30" name="NEW_GM_POSTFIX_<?php echo $level; ?>" value="<?php echo $this->getSetting('GM_POSTFIX_' . $level); ?>"></td>
						</tr>
						<?php } ?>
					</table>
				</div>

				<div id="gm_ph">
					<table class="gm_edit_config">
						<tr>
							<th><?php echo WT_I18N::translate('Use Google Maps™ for the place hierarchy'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_PLACE_HIERARCHY', $this->getSetting('GM_PLACE_HIERARCHY')); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Size of map (in pixels)'); ?></th>
							<td>
								<?php echo WT_I18N::translate('Width'); ?>
								<input type="text" name="NEW_GM_PH_XSIZE" value="<?php echo $this->getSetting('GM_PH_XSIZE'); ?>" size="10">
								<?php echo WT_I18N::translate('Height'); ?>
								<input type="text" name="NEW_GM_PH_YSIZE" value="<?php echo $this->getSetting('GM_PH_YSIZE'); ?>" size="10">
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Type of place markers in Place Hierarchy'); ?></th>
							<td>
								<select name="NEW_GM_PH_MARKER">
									<option value="G_DEFAULT_ICON" <?php if ($this->getSetting('GM_PH_MARKER')=="G_DEFAULT_ICON") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Standard'); ?></option>
									<option value="G_FLAG" <?php if ($this->getSetting('GM_PH_MARKER')=="G_FLAG") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Flag'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Display short placenames'), help_link('GM_DISP_SHORT_PLACE','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_DISP_SHORT_PLACE', $this->getSetting('GM_DISP_SHORT_PLACE')); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Display map coordinates'), help_link('GM_COORD','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_COORD', $this->getSetting('GM_COORD')); ?></td>
						</tr>
					</table>
				</div>
			</div>
			<p>
				<input type="submit" value="<?php echo WT_I18N::translate('save'); ?>">
			</p>
		</form>
		<?php
	}

	/**
	 * Google Maps API script
	 *
	 * @return string
	 */
	private function googleMapsScript() {
		return 'https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false&amp;language=' . WT_LOCALE;
	}

	private function flags() {
		require WT_ROOT . 'includes/functions/functions_edit.php';

		$controller = new WT_Controller_Simple();
		$controller
			->setPageTitle(WT_I18N::translate('Select flag'))
			->pageHeader();

		$stats = new WT_Stats(WT_GEDCOM);
		$countries = $stats->getAllCountries();
		$action    = WT_Filter::post('action');

		$countrySelected = WT_Filter::get('countrySelected', null, 'Countries');
		$stateSelected   = WT_Filter::get('stateSelected',   null, 'States');

		$country = array();
		if (is_dir(WT_ROOT . WT_MODULES_DIR . 'googlemap/places/flags')) {
			$rep = opendir(WT_ROOT . WT_MODULES_DIR . 'googlemap/places/flags');
			while ($file = readdir($rep)) {
				if (stristr($file, '.png')) {
					$country[] = substr($file, 0, strlen($file) - 4);
				}
			}
			closedir($rep);
			sort($country);
		}

		if ($countrySelected == 'Countries') {
			$flags = $country;
		} else {
			$flags = array();
			if (is_dir(WT_ROOT . WT_MODULES_DIR . 'googlemap/places/' . $countrySelected . '/flags')) {
				$rep = opendir(WT_ROOT . WT_MODULES_DIR . 'googlemap/places/' . $countrySelected . '/flags');
				while ($file = readdir($rep)) {
					if (stristr($file, '.png')) {
						$flags[] = substr($file, 0, strlen($file) - 4);
					}
				}
				closedir($rep);
				sort($flags);
			}
		}
		$flags_s = array();
		if ($stateSelected != 'States' && is_dir(WT_ROOT . WT_MODULES_DIR . 'googlemap/places/' . $countrySelected . '/flags/' . $stateSelected)) {
			$rep = opendir(WT_ROOT . WT_MODULES_DIR . 'googlemap/places/' . $countrySelected . '/flags/' . $stateSelected);
			while ($file = readdir($rep)) {
				if (stristr($file, '.png')) {
					$flags_s[] = substr($file, 0, strlen($file)-4);
				}
			}
			closedir($rep);
			sort($flags_s);
		}

		if ($action == 'ChangeFlag' && isset($_POST['FLAGS'])) {
		?>
			<script>
		<?php if ($_POST['selcountry'] == 'Countries') { ?>
					window.opener.document.editplaces.icon.value = 'places/flags/<?php echo $flags[$_POST['FLAGS']]; ?>.png';
					window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>googlemap/places/flags/<?php echo $country[$_POST['FLAGS']]; ?>.png\">&nbsp;&nbsp;<a href=\"#\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
		<?php } elseif ($_POST['selstate'] != "States"){ ?>
					window.opener.document.editplaces.icon.value = 'places/<?php echo $countrySelected, '/flags/', $_POST['selstate'], '/', $flags_s[$_POST['FLAGS']]; ?>.png';
					window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>googlemap/places/<?php echo $countrySelected, "/flags/", $_POST['selstate'], "/", $flags_s[$_POST['FLAGS']]; ?>.png\">&nbsp;&nbsp;<a href=\"#\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
		<?php } else { ?>
					window.opener.document.editplaces.icon.value = "places/<?php echo $countrySelected, "/flags/", $flags[$_POST['FLAGS']]; ?>.png";
					window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>googlemap/places/<?php echo $countrySelected, "/flags/", $flags[$_POST['FLAGS']]; ?>.png\">&nbsp;&nbsp;<a href=\"#\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
		<?php } ?>
					window.opener.updateMap();
					window.close();
			</script>
		<?php
			exit;
		} else {
		?>
		<script>
			function selectCountry() {
				if (document.flags.COUNTRYSELECT.value == 'Countries') {
					window.location="module.php?mod=googlemap&mod_action=flags";
				} else if (document.flags.STATESELECT.value != 'States') {
					window.location="module.php?mod=googlemap&mod_action=flags&countrySelected=" + document.flags.COUNTRYSELECT.value + "&stateSelected=" + document.flags.STATESELECT.value;
				} else {
					window.location="module.php?mod=googlemap&mod_action=flags&countrySelected=" + document.flags.COUNTRYSELECT.value;
				}
			}
		</script>
		<?php
		}
		$countryList = array();
		$placesDir = scandir(WT_MODULES_DIR.'googlemap/places/');
		for ($i = 0; $i < count($country); $i++) {
			if (count(preg_grep('/'.$country[$i].'/', $placesDir)) != 0) {
				$rep = opendir(WT_MODULES_DIR.'googlemap/places/'.$country[$i].'/');
				while ($file = readdir($rep)) {
					if (stristr($file, 'flags')) {
						if (isset($countries[$country[$i]])) {
							$countryList[$country[$i]] = $countries[$country[$i]];
						} else {
							$countryList[$country[$i]] = $country[$i];
						}
					}
				}
				closedir($rep);
			}
		}
		asort($countryList);
		$stateList = array();
		if ($countrySelected != 'Countries') {
			$placesDir = scandir(WT_MODULES_DIR.'googlemap/places/'.$countrySelected.'/flags/');
			for ($i = 0; $i < count($flags); $i++) {
				if (in_array($flags[$i], $placesDir)) {
					$rep = opendir(WT_MODULES_DIR.'googlemap/places/'.$countrySelected.'/flags/'.$flags[$i].'/');
					while ($file = readdir($rep)) {
						$stateList[$flags[$i]] = $flags[$i];
					}
					closedir($rep);
				}
			}
			asort($stateList);
		}
		?>
		<h4><?php echo WT_I18N::translate('Change flag'); ?></h4>
		<form method="post" id="flags" name="flags" action="module.php?mod=googlemap&amp;mod_action=flags&amp;countrySelected=<?php echo $countrySelected; ?>&amp;stateSelected=<?php echo $stateSelected; ?>">
			<input type="hidden" name="action" value="ChangeFlag">
			<input type="hidden" name="selcountry" value="<?php echo $countrySelected; ?>">
			<input type="hidden" name="selstate" value="<?php echo $stateSelected; ?>">
			<table class="facts_table">
				<tr>
					<td class="optionbox" colspan="4">
						<?php echo help_link('PLE_FLAGS','googlemap'); ?>
						<select name="COUNTRYSELECT" dir="ltr" onchange="selectCountry()">
							<option value="Countries"><?php echo WT_I18N::translate('Countries'); ?></option>
							<?php foreach ($countryList as $country_key=>$country_name) {
								echo '<option value="', $country_key, '"';
								if ($countrySelected == $country_key) echo ' selected="selected" ';
								echo '>', $country_name, '</option>';
							} ?>
						</select>
					</td>
				</tr>
				<tr>
		<?php
				$j = 1;
				for ($i = 0; $i < count($flags); $i++) {
					if ($countrySelected == 'Countries') {
						$tempstr = '<td><input type="radio" dir="ltr" name="FLAGS" value="'.$i.'"><img src="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/places/flags/'.$flags[$i].'.png" alt="'.$flags[$i].'"  title="';
						if ($flags[$i]!='blank') {
							if (isset($countries[$flags[$i]])) {
								$tempstr.=$countries[$flags[$i]];
							} else {
								$tempstr.=$flags[$i];
							}
						} else {
							$tempstr.=$countries['???'];
						}
						echo $tempstr, '">&nbsp;&nbsp;', $flags[$i], '</input></td>';
					} else {
						echo '<td><input type="radio" dir="ltr" name="FLAGS" value="', $i, '"><img src="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/places/', $countrySelected, '/flags/', $flags[$i], '.png">&nbsp;&nbsp;', $flags[$i], '</input></td>';
					}
					if ($j == 4) {
						echo '</tr><tr>';
						$j = 0;
					}
					$j++;
				}
				echo '</tr><tr';
				if ($countrySelected == 'Countries' || count($stateList)==0) {
					echo ' style=" visibility: hidden"';
				}
				echo '>';
		?>
					<td class="optionbox" colspan="4">
						<?php echo help_link('PLE_FLAGS','googlemap'); ?>
						<select name="STATESELECT" dir="ltr" onchange="selectCountry()">
							<option value="States"><?php echo /* I18N: Part of a country, state/region/county */ WT_I18N::translate('Subdivision'); ?></option>
							<?php foreach ($stateList as $state_key=>$state_name) {
								echo '<option value="', $state_key, '"';
								if ($stateSelected == $state_key) echo ' selected="selected"';
								echo '>', $state_name, '</option>';
							} ?>
						</select>
					</td>
				</tr>
				<tr>
		<?php
				$j = 1;
				for ($i = 0; $i < count($flags_s); $i++) {
					if ($stateSelected != 'States') {
						echo '<td><input type="radio" dir="ltr" name="FLAGS" value="', $i, '"><img src="', WT_STATIC_URL.WT_MODULES_DIR, 'googlemap/places/', $countrySelected, '/flags/', $stateSelected, '/', $flags_s[$i], '.png">&nbsp;&nbsp;', $flags_s[$i], '</input></td>';
					}
					if ($j == 4) {
						echo '</tr><tr>';
						$j = 0;
					}
					$j++;
				}
		?>
				</tr>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
		<?php
	}

	private function pedigreeMap() {
		global $controller, $MAX_PEDIGREE_GENERATIONS;

		// Default is show for both of these.
		$hideflags = WT_Filter::getBool('hideflags');
		$hidelines = WT_Filter::getBool('hidelines');

		$controller = new WT_Controller_Pedigree();

		// Start of internal configuration variables
		// Limit this to match available number of icons.
		// 8 generations equals 255 individuals
		$MAX_PEDIGREE_GENERATIONS = min($MAX_PEDIGREE_GENERATIONS, 8);

		// End of internal configuration variables
		$controller
			->setPageTitle(/* I18N: %s is an individual’s name */ WT_I18N::translate('Pedigree map of %s', $controller->getPersonName()))
			->pageHeader()
			->addExternalJavascript(WT_STATIC_URL . 'js/autocomplete.js')
			->addInlineJavascript('autocomplete();');

		echo '<link type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';
		echo '<div id="pedigreemap-page">
				<h2>', $controller->getPageTitle(), '</h2>';

		// -- print the form to change the number of displayed generations
		?>
		<form name="people" method="get" action="?">
			<input type="hidden" name="ged" value="<?php echo WT_Filter::escapeHtml(WT_GEDCOM); ?>">
			<input type="hidden" name="mod" value="googlemap">
			<input type="hidden" name="mod_action" value="pedigree_map">
			<table class="list_table" width="555">
				<tr>
					<td class="descriptionbox wrap">
						<?php echo WT_I18N::translate('Individual'); ?>
					</td>
					<td class="descriptionbox wrap">
						<?php echo WT_I18N::translate('Generations'); ?>
					</td>
					<td class="descriptionbox wrap">
						<?php echo WT_I18N::translate('Hide flags'), help_link('PEDIGREE_MAP_hideflags','googlemap'); ?>
					</td>
					<td class="descriptionbox wrap">
						<?php echo WT_I18N::translate('Hide lines'), help_link('PEDIGREE_MAP_hidelines','googlemap'); ?>
					</td>
				</tr>
				<tr>
					<td class="optionbox">
						<input class="pedigree_form" data-autocomplete-type="INDI" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
						<?php echo print_findindi_link('rootid'); ?>
					</td>
					<td class="optionbox">
						<select name="PEDIGREE_GENERATIONS">
						<?php
							for ($p=3; $p<=$MAX_PEDIGREE_GENERATIONS; $p++) {
								echo '<option value="', $p, '" ';
								if ($p == $controller->PEDIGREE_GENERATIONS) {
									echo 'selected="selected"';
								}
								echo '>', $p, '</option>';
							}
						?>
						</select>
					</td>
					<td class="optionbox">
						<?php
						echo '<input name="hideflags" type="checkbox" value="1"';
						if ($hideflags) {
							echo ' checked="checked"';
						}
						echo '>';
						?>
					</td>
					<td class="optionbox">
						<?php
						echo '<input name="hidelines" type="checkbox" value="1"';
						if ($hidelines) {
							echo ' checked="checked"';
						}
						echo '>';
						?>
					</td>
				</tr>
				<tr>
					<td class="topbottombar" colspan="5">
						<input type="submit" value="<?php echo WT_I18N::translate('View'); ?>">
					</td>
				</tr>
			</table>
		</form>
		<!-- end of form -->

		<!-- count records by type -->
		<?php
		$curgen=1;
		$priv=0;
		$count=0;
		$miscount=0;
		$missing = '';

		$latlongval = array();
		$lat = array();
		$lon = array();
		for ($i=0; $i<($controller->treesize); $i++) {
			// -- check to see if we have moved to the next generation
			if ($i+1 >= pow(2, $curgen)) {$curgen++;}
			$person = $controller->ancestors[$i];
			if (!empty($person)) {
				$name = $person->getFullName();
				if ($name == WT_I18N::translate('Private')) $priv++;
				$place = $person->getBirthPlace();
				if (empty($place)) {
					$latlongval[$i] = null;
				} else {
					$latlongval[$i] = $this->getLatitudeAndLongitudeFromPlaceLocation($person->getBirthPlace());
				}
				if ($latlongval[$i]) {
					$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]->pl_lati);
					$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]->pl_long);
					if (($lat[$i] != null) && ($lon[$i] != null)) {
						$count++;
					} else { // The place is in the table but has empty values
						if ($name) {
							if ($missing) {
								$missing .= ', ';
							}
							$missing .= '<a href="' . $person->getHtmlUrl() . '">' . $name . '</a>';
							$miscount++;
						}
					}
				} else { // There was no place, or not listed in the map table
					if ($name) {
						if ($missing) {
							$missing .= ', ';
						}
						$missing .= '<a href="' . $person->getHtmlUrl() . '">' . $name . '</a>';
						$miscount++;
					}
				}
			}
		}
		//<!-- end of count records by type -->
		//<!-- start of map display -->
		echo '<div id="pedigreemap_chart">';
		echo '<table class="tabs_table" cellspacing="0" cellpadding="0" border="0" width="100%">';
		echo '<tr>';
		echo '<td valign="top">';
		echo '<div id="pm_map" style="border: 1px solid gray; height: ', $this->getSetting('GM_YSIZE'), 'px; font-size: 0.9em;';
		echo '"><i class="icon-loading-large"></i></div>';
		if (Auth::isAdmin()) {
			echo '<table width="100%">';
			echo '<tr><td align="left">';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
			echo '</td>';
			echo '<td align="center">';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_places">', WT_I18N::translate('Geographic data'), '</a>';
			echo '</td>';
			echo '<td align="right">';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">', WT_I18N::translate('Place check'), '</a>';
			echo '</td></tr>';
			echo '</table>';
		}
		echo '</td><td width="15px">&nbsp;</td>';
		echo '<td width="310px" valign="top">';
		echo '<div id="side_bar" style="width:300px; font-size:0.9em; overflow:auto; overflow-x:hidden; overflow-y:auto; height:', $this->getSetting('GM_YSIZE'), 'px;"></div></td>';
		echo '</tr>';
		echo '</table>';
		// display info under map
		echo '<hr>';
		echo '<table cellspacing="0" cellpadding="0" border="0" width="100%">';
		echo '<tr>';
		echo '<td valign="top">';
		// print summary statistics
		if (isset($curgen)) {
			$total=pow(2,$curgen)-1;
			echo WT_I18N::plural(
				'%1$d individual displayed, out of the normal total of %2$d, from %3$d generations.',
				'%1$d individuals displayed, out of the normal total of %2$d, from %3$d generations.',
				$count,
				$count, $total, $curgen
			), '<br>';
			echo '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td valign="top">';
			if ($priv) {
				echo WT_I18N::plural('%s individual is private.', '%s individuals are private.', $priv, $priv), '<br>';
			}
			if ($count+$priv != $total) {
				if ($miscount == 0) {
					echo WT_I18N::translate('No ancestors in the database.'), "<br>";
				} else {
					echo /* I18N: %1$d is a count of individuals, %2$s is a list of their names */ WT_I18N::plural(
						'%1$d individual is missing birthplace map coordinates: %2$s.',
						'%1$d individuals are missing birthplace map coordinates: %2$s.',
						$miscount, $miscount, $missing),
						'<br>';
				}
			}
		}
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';// close #pedigreemap_chart
		echo '</div>';// close #pedigreemap-page
		?>
		<!-- end of map display -->
		<!-- Start of map scripts -->
		<?php
		echo '<script src="', $this->googleMapsScript(), '"></script>';
		$controller->addInlineJavascript($this->pedigreeMapJavascript($hideflags, $hidelines));
	}

	/**
	 * @param boolean $hideflags
	 * @param boolean $hidelines
	 *
	 * @return string
	 */
	private function pedigreeMapJavascript($hideflags, $hidelines) {
		global $controller, $SHOW_HIGHLIGHT_IMAGES, $PEDIGREE_GENERATIONS;

		// The HomeControl returns the map to the original position and style
		$js='function HomeControl(controlDiv, pm_map) {'.
			// Set CSS styles for the DIV containing the control
			// Setting padding to 5 px will offset the control from the edge of the map
			'controlDiv.style.paddingTop = "5px";
			controlDiv.style.paddingRight = "0px";'.
			// Set CSS for the control border
			'var controlUI = document.createElement("DIV");
			controlUI.style.backgroundColor = "white";
			controlUI.style.color = "black";
			controlUI.style.borderColor = "black";
			controlUI.style.borderColor = "black";
			controlUI.style.borderStyle = "solid";
			controlUI.style.borderWidth = "2px";
			controlUI.style.cursor = "pointer";
			controlUI.style.textAlign = "center";
			controlUI.title = "";
			controlDiv.appendChild(controlUI);'.
			// Set CSS for the control interior
			'var controlText = document.createElement("DIV");
			controlText.style.fontFamily = "Arial,sans-serif";
			controlText.style.fontSize = "12px";
			controlText.style.paddingLeft = "15px";
			controlText.style.paddingRight = "15px";
			controlText.innerHTML = "<b>'.WT_I18N::translate('Redraw map').'<\/b>";
			controlUI.appendChild(controlText);'.
			// Setup the click event listeners: simply set the map to original LatLng
			'google.maps.event.addDomListener(controlUI, "click", function() {
				pm_map.setMapTypeId(google.maps.MapTypeId.TERRAIN),
				pm_map.fitBounds(bounds),
				pm_map.setCenter(bounds.getCenter()),
				infowindow.close()
				if (document.getElementById(lastlinkid) != null) {
					document.getElementById(lastlinkid).className = "person_box:target";
				}
			});
		}'.
		// This function picks up the click and opens the corresponding info window
		'function myclick(i) {
			if (document.getElementById(lastlinkid) != null) {
				document.getElementById(lastlinkid).className = "person_box:target";
			}
			google.maps.event.trigger(gmarkers[i], "click");
		}'.
		// this variable will collect the html which will eventually be placed in the side_bar
		'var side_bar_html = "";'.
		// arrays to hold copies of the markers and html used by the side_bar
		// because the function closure trick doesnt work there
		'var gmarkers = [];
		var i = 0;
		var lastlinkid;
		var infowindow = new google.maps.InfoWindow({});'.
		// === Create an associative array of GIcons()
		'var gicons = [];
		gicons["1"]        = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon1.png")
		gicons["1"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["2"]         = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2.png")
		gicons["2"].shadow  = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["2L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["2L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["2R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["2R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["2Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["2Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);
		gicons["3"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3.png")
		gicons["3"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["3L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["3L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["3R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["3R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["3Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["3Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);
		gicons["4"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4.png")
		gicons["4"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["4L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["4L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["4R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["4R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["4Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["4Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);
		gicons["5"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5.png")
		gicons["5"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["5L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["5L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["5R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["5R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["5Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["5Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);
		gicons["6"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6.png")
		gicons["6"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["6L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["6L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["6R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["6R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["6Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["6Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);
		gicons["7"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7.png")
		gicons["7"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["7L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["7L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["7R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["7R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["7Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["7Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);
		gicons["8"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8.png")
		gicons["8"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(10, 34) // Shadow anchor is base of image
								);
		gicons["8L"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8L.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(28, 28) // Image anchor
								);
		gicons["8L"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(32, 27) // Shadow anchor is base of image
								);
		gicons["8R"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8R.png",
									new google.maps.Size(32, 32), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(4, 28)  // Image anchor
								);
		gicons["8R"].shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32), // Shadow size
									new google.maps.Point(0, 0),  // Shadow origin
									new google.maps.Point(15, 27) // Shadow anchor is base of image
								);
		gicons["8Ls"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8Ls.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(22, 22) // Image anchor
								);
		gicons["8Rs"] = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8Rs.png",
									new google.maps.Size(24, 24), // Image size
									new google.maps.Point(0, 0),  // Image origin
									new google.maps.Point(2, 22)  // Image anchor
								);'.
		// / A function to create the marker and set up the event window
		'function createMarker(point, name, html, mhtml, icontype) {
			var contentString = "<div id=\'iwcontent_edit\'>"+mhtml+"<\/div>";'.
			// Create a marker with the requested icon
			'var marker = new google.maps.Marker({
				icon:     gicons[icontype],
				shadow:   gicons[icontype].shadow,
				map:      pm_map,
				position: point,
				zIndex:   0
			});
			var linkid = "link"+i;
			google.maps.event.addListener(marker, "click", function() {
				infowindow.close();
				infowindow.setContent(contentString);
				infowindow.open(pm_map, marker);
				document.getElementById(linkid).className = "person_box";
				if (document.getElementById(lastlinkid) != null) {
					document.getElementById(lastlinkid).className = "person_box:target";
				}
				lastlinkid=linkid;
			});'.
			// save the info we need to use later for the side_bar
			'gmarkers[i] = marker;'.
			// add a line to the side_bar html
			'side_bar_html += "<br><div id=\'"+linkid+"\' onclick=\'myclick(" + i + ")\'>" + html +"<br></div>";
			i++;
			return marker;
		};'.
		// create the map
		'var myOptions = {
			zoom: 6,
			center: new google.maps.LatLng(0, 0),
			mapTypeId: google.maps.MapTypeId.TERRAIN,  // ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
				style: google.maps.NavigationControlStyle.SMALL   // ANDROID, DEFAULT, SMALL, ZOOM_PAN
			},
			streetViewControl: false,  // Show Pegman or not
			scrollwheel: true
		};
		var pm_map = new google.maps.Map(document.getElementById("pm_map"), myOptions);
		google.maps.event.addListener(pm_map, "click", function() {
			if (document.getElementById(lastlinkid) != null) {
				document.getElementById(lastlinkid).className = "person_box:target";
			}
		infowindow.close();
		});'.
		// Create the DIV to hold the control and call HomeControl() passing in this DIV. --
		'var homeControlDiv = document.createElement("DIV");
		var homeControl = new HomeControl(homeControlDiv, pm_map);
		homeControlDiv.index = 1;
		pm_map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);'.
		// create the map bounds
		'var bounds = new google.maps.LatLngBounds();';
		// add the points
		$curgen       = 1;
		$count        = 0;
		$colored_line = array(
			'1' => '#FF0000',
			'2' => '#0000FF',
			'3' => '#00FF00',
			'4' => '#FFFF00',
			'5' => '#00FFFF',
			'6' => '#FF00FF',
			'7' => '#C0C0FF',
			'8' => '#808000',
		);
		$lat        = array();
		$lon        = array();
		$latlongval = array();
		$flags      = array();
		for ($i = 0; $i < $controller->treesize; $i++) {
			// moved up to grab the sex of the individuals
			$person = $controller->ancestors[$i];
			if ($person) {
				$name = $person->getFullName();

				// -- check to see if we have moved to the next generation
				if ($i+1 >= pow(2, $curgen)) {
					$curgen++;
				}

				$relationship = get_close_relationship_name($controller->root, $person);

				$event = '<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/sq'.$curgen.'.png" width="10" height="10"> '.
					'<strong>'.$relationship.'</strong>';
				// add thumbnail image
				if ($SHOW_HIGHLIGHT_IMAGES) {
					$image = $person->displayImage();
				} else {
					$image = '';
				}
				// end of add image

				$dataleft  = WT_Filter::escapeJs($image . $event . ' — ' . $name);
				$datamid   = WT_Filter::escapeJs(' <span><a href="' . $person->getHtmlUrl() . '">('.WT_I18N::translate('View person').')</a></span>');
				$dataright = WT_Filter::escapeJs('<br><strong>'. WT_I18N::translate('Birth:') . '&nbsp;</strong>' .  $person->getBirthDate()->display() . ' — ' . $person->getBirthPlace());

				$latlongval[$i] = $this->getLatitudeAndLongitudeFromPlaceLocation($person->getBirthPlace());
				if ($latlongval[$i]) {
					$lat[$i] = (double)str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]->pl_lati);
					$lon[$i] = (double)str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]->pl_long);
					if ($lat[$i] || $lon[$i]) {
						if (!$hideflags && $latlongval[$i]->pl_icon) {
							$flags[$i] = $latlongval[$i]->pl_icon;
							$ffile = strrchr($latlongval[$i]->pl_icon, '/');
							$ffile = substr($ffile,1, strpos($ffile, '.')-1);
							if (empty($flags[$ffile])) {
								$flags[$ffile] = $i; // Only generate the flag once
								$js .= 'var point = new google.maps.LatLng(' . $lat[$i] . ',' . $lon[$i] . ');';
								$js .= 'var Marker1_0_flag = new google.maps.MarkerImage();';
								$js .= 'Marker1_0_flag.image = "' . WT_STATIC_URL . WT_MODULES_DIR . 'googlemap/' . $flags[$i] . '";';
								$js .= 'Marker1_0_flag.shadow = "' . WT_STATIC_URL . WT_MODULES_DIR . 'googlemap/images/flag_shadow.png";';
								$js .= 'Marker1_0_flag.iconSize = new google.maps.Size(25, 15);';
								$js .= 'Marker1_0_flag.shadowSize = new google.maps.Size(35, 45);';
								$js .= 'Marker1_0_flag.iconAnchor = new google.maps.Point(1, 45);';
								$js .= 'var Marker1_0 = new google.maps.LatLng(point, {icon:Marker1_0_flag});';
							}
						}
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
									$lon[$i]       = $lon[$i] + 0.0025;
									$lat[$i]       = $lat[$i] + 0.0025;
									break;
								}
							}
						}
						$js .= 'var point = new google.maps.LatLng(' . $lat[$i] . ',' . $lon[$i] . ');';
						$js .= "var marker = createMarker(point, \"" . WT_Filter::escapeJs($name) . "\",\"<div>" . $dataleft . $datamid . $dataright . "</div>\", \"";
						$js .= "<div class='iwstyle'>";
						$js .= "<a href='module.php?ged=" . WT_GEDURL . "&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid=" . $person->getXref() . "&amp;PEDIGREE_GENERATIONS={$PEDIGREE_GENERATIONS}";
						if ($hideflags) {
							$js .= '&amp;hideflags=1';
						}
						if ($hidelines) {
							$js .= '&amp;hidelines=1';
						}
						$js .= "' title='" . WT_I18N::translate('Pedigree map') . "'>" . $dataleft . "</a>" . $datamid . $dataright . "</div>\", \"" . $marker_number . "\");";
						// Construct the polygon lines
						if (!$hidelines) {
							$to_child = (intval(($i-1)/2)); // Draw a line from parent to child
							if (array_key_exists($to_child, $lat) && $lat[$to_child]!=0 && $lon[$to_child]!=0) {
								$js.='
								var linecolor;
								var plines;
								var lines = [new google.maps.LatLng('.$lat[$i].','.$lon[$i].'),
									new google.maps.LatLng('.$lat[$to_child].','.$lon[$to_child].')];
								linecolor = "'.$colored_line[$curgen].'";
								plines = new google.maps.Polygon({
									paths: lines,
									strokeColor: linecolor,
									strokeOpacity: 0.8,
									strokeWeight: 3,
									fillColor: "#FF0000",
									fillOpacity: 0.1
								});
								plines.setMap(pm_map);';
							}
						}
						// Extend and fit marker bounds
						$js.='bounds.extend(point);';
						$js.='pm_map.fitBounds(bounds);';
						$count++;
					}
				}
			} else {
				$latlongval[$i] = null;
			}
		}
		$js.='pm_map.setCenter(bounds.getCenter());'.
		// Close the sidebar highlight when the infowindow is closed
		'google.maps.event.addListener(infowindow, "closeclick", function() {
			document.getElementById(lastlinkid).className = "person_box:target";
		});'.
		// put the assembled side_bar_html contents into the side_bar div
		'document.getElementById("side_bar").innerHTML = side_bar_html;'.
		// create the context menu div
		'var contextmenu = document.createElement("div");
			contextmenu.style.visibility="hidden";
			contextmenu.innerHTML = "<a href=\'#\' onclick=\'zoomIn()\'><div class=\'optionbox\'>&nbsp;&nbsp;'.WT_I18N::translate('Zoom in').'&nbsp;&nbsp;</div></a>"
								+ "<a href=\'#\' onclick=\'zoomOut()\'><div class=\'optionbox\'>&nbsp;&nbsp;'.WT_I18N::translate('Zoom out').'&nbsp;&nbsp;</div></a>"
								+ "<a href=\'#\' onclick=\'zoomInHere()\'><div class=\'optionbox\'>&nbsp;&nbsp;'.WT_I18N::translate('Zoom in here').'</div></a>"
								+ "<a href=\'#\' onclick=\'zoomOutHere()\'><div class=\'optionbox\'>&nbsp;&nbsp;'.WT_I18N::translate('Zoom out here').'&nbsp;&nbsp;</div></a>"
								+ "<a href=\'#\' onclick=\'centreMapHere()\'><div class=\'optionbox\'>&nbsp;&nbsp;'.WT_I18N::translate('Center map here').'&nbsp;&nbsp;</div></a>";'.
		// listen for singlerightclick
		'google.maps.event.addListener(pm_map,"singlerightclick", function(pixel,tile) {'.
			// store the "pixel" info in case we need it later
			// adjust the context menu location if near an egde
			// create a GControlPosition
			// apply it to the context menu, and make the context menu visible
			'clickedPixel = pixel;
			var x=pixel.x;
			var y=pixel.y;
			if (x > pm_map.getSize().width - 120) { x = pm_map.getSize().width - 120 }
			if (y > pm_map.getSize().height - 100) { y = pm_map.getSize().height - 100 }
			var pos = new GControlPosition(G_ANCHOR_TOP_LEFT, new GSize(x,y));
			pos.apply(contextmenu);
			contextmenu.style.visibility = "visible";
		});
		'.
		// functions that perform the context menu options
		'function zoomIn() {'.
			// perform the requested operation
			'pm_map.zoomIn();'.
			// hide the context menu now that it has been used
			'contextmenu.style.visibility="hidden";
		}
		function zoomOut() {'.
			// perform the requested operation
			'pm_map.zoomOut();'.
			// hide the context menu now that it has been used
			'contextmenu.style.visibility="hidden";
		}
		function zoomInHere() {'.
			// perform the requested operation
			'var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
			pm_map.zoomIn(point,true);'.
			// hide the context menu now that it has been used
			'contextmenu.style.visibility="hidden";
		}
		function zoomOutHere() {'.
			// perform the requested operation
			'var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
			pm_map.setCenter(point,pm_map.getZoom()-1);'.
			// There is no pm_map.zoomOut() equivalent
			// hide the context menu now that it has been used
			'contextmenu.style.visibility="hidden";
		}
		function centreMapHere() {'.
			// perform the requested operation
			'var point = pm_map.fromContainerPixelToLatLng(clickedPixel)
			pm_map.setCenter(point);'.
			// hide the context menu now that it has been used
			'contextmenu.style.visibility="hidden";
		}'.
		// If the user clicks on the map, close the context menu
		'google.maps.event.addListener(pm_map, "click", function() {
			contextmenu.style.visibility="hidden";
		});';

		return $js;
	}

	private function adminPlaceCheck() {
		require_once WT_ROOT.'includes/functions/functions_edit.php';

		$action    = WT_Filter::get('action', '','go');
		$gedcom_id = WT_Filter::get('gedcom_id', null, WT_GED_ID);
		$country   = WT_Filter::get('country', '.+', 'XYZ');
		$state     = WT_Filter::get('state', '.+', 'XYZ');
		$matching  = WT_Filter::getBool('matching');

		$controller = new WT_Controller_Page();
		$controller
			->restrictAccess(Auth::isAdmin())
			->setPageTitle(WT_I18N::translate('Google Maps™'))
			->pageHeader();

		echo '
			<table id="gm_config">
				<tr>
					<th>
						<a href="module.php?mod=googlemap&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'),'</a>
					</th>
					<th>
						<a href="module.php?mod=googlemap&amp;mod_action=admin_places">
							', WT_I18N::translate('Geographic data'),'
						</a>
					</th>
					<th>
						<a class="current" href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">
							', WT_I18N::translate('Place check'),'
						</a>
					</th>
				</tr>
			</table>';

		//Start of User Defined options
		echo '
			<form method="get" name="placecheck" action="module.php">
				<input type="hidden" name="mod" value="', $this->getName(), '">
				<input type="hidden" name="mod_action" value="admin_placecheck">
				<div class="gm_check">
					<label>', WT_I18N::translate('Family tree'), '</label>';
					echo select_edit_control('gedcom_id', WT_Tree::getIdList(), null, $gedcom_id, ' onchange="this.form.submit();"');
					echo '<label>', WT_I18N::translate('Country'), '</label>
					<select name="country" onchange="this.form.submit();">
						<option value="XYZ" selected="selected">', /* I18N: first/default option in a drop-down listbox */ WT_I18N::translate('&lt;select&gt;'), '</option>
						<option value="XYZ">', WT_I18N::translate('All'), '</option>';
							$rows=WT_DB::prepare("SELECT pl_id, pl_place FROM `##placelocation` WHERE pl_level=0 ORDER BY pl_place")
								->fetchAssoc();
							foreach ($rows as $id=>$place) {
								echo '<option value="', WT_Filter::escapeHtml($place), '"';
								if ($place==$country) {
									echo ' selected="selected"';
									$par_id=$id;
								}
								echo '>', WT_Filter::escapeHtml($place), '</option>';
							}
					echo '</select>';
					if ($country!='XYZ') {
						echo '<label>', /* I18N: Part of a country, state/region/county */ WT_I18N::translate('Subdivision'), '</label>
							<select name="state" onchange="this.form.submit();">
								<option value="XYZ" selected="selected">', WT_I18N::translate('&lt;select&gt;'), '</option>
								<option value="XYZ">', WT_I18N::translate('All'), '</option>';
								$places=WT_DB::prepare("SELECT pl_place FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place")
									->execute(array($par_id))
									->fetchOneColumn();
								foreach ($places as $place) {
									echo '<option value="', WT_Filter::escapeHtml($place), '"', $place==$state?' selected="selected"':'', '>', WT_Filter::escapeHtml($place), '</option>';
								}
								echo '</select>';
							}
					echo '<label>', WT_I18N::translate('Include fully matched places: '), '</label>';
					echo '<input type="checkbox" name="matching" value="1" onchange="this.form.submit();"';
					if ($matching) {
						echo ' checked="checked"';
					}
					echo '>';
				echo '</div>';// close div gm_check
				echo '<input type="hidden" name="action" value="go">';
			echo '</form>';//close form placecheck
			echo '<hr>';

		switch ($action) {
		case 'go':
			//Identify gedcom file
			$trees=WT_Tree::getAll();
			echo '<div id="gm_check_title">', $trees[$gedcom_id]->tree_title_html, '</div>';
			//Select all '2 PLAC ' tags in the file and create array
			$place_list=array();
			$ged_data=WT_DB::prepare("SELECT i_gedcom FROM `##individuals` WHERE i_gedcom LIKE ? AND i_file=?")
				->execute(array("%\n2 PLAC %", $gedcom_id))
				->fetchOneColumn();
			foreach ($ged_data as $ged_datum) {
				preg_match_all('/\n2 PLAC (.+)/', $ged_datum, $matches);
				foreach ($matches[1] as $match) {
					$place_list[$match]=true;
				}
			}
			$ged_data=WT_DB::prepare("SELECT f_gedcom FROM `##families` WHERE f_gedcom LIKE ? AND f_file=?")
				->execute(array("%\n2 PLAC %", $gedcom_id))
				->fetchOneColumn();
			foreach ($ged_data as $ged_datum) {
				preg_match_all('/\n2 PLAC (.+)/', $ged_datum, $matches);
				foreach ($matches[1] as $match) {
					$place_list[$match]=true;
				}
			}
			// Unique list of places
			$place_list=array_keys($place_list);

			// Apply_filter
			if ($country=='XYZ') {
				$filter='.*$';
			} else {
				$filter=preg_quote($country).'$';
				if ($state!='XYZ') {
					$filter=preg_quote($state).', '.$filter;
				}
			}
			$place_list=preg_grep('/'.$filter.'/', $place_list);

			//sort the array, limit to unique values, and count them
			usort($place_list, array('WT_I18N', 'strcasecmp'));
			$i=count($place_list);

			//calculate maximum no. of levels to display
			$x=0;
			$max=0;
			while ($x<$i) {
				$levels=explode(",", $place_list[$x]);
				$parts=count($levels);
				if ($parts>$max) $max=$parts;
			$x++;}
			$x=0;

			//scripts for edit, add and refresh
			?>
			<script>
			function edit_place_location(placeid) {
				window.open('module.php?mod=googlemap&mod_action=places_edit&action=update&placeid='+placeid, '_blank', gmap_window_specs);
				return false;
			}

			function add_place_location(placeid) {
				window.open('module.php?mod=googlemap&mod_action=places_edit&action=add&placeid='+placeid, '_blank', gmap_window_specs);
				return false;
			}
			</script>
			<?php

			//start to produce the display table
			$cols=0;
			$span=$max*3+3;
			echo '<div class="gm_check_details">';
			echo '<table class="gm_check_details"><tr>';
			echo '<th rowspan="3">', WT_I18N::translate('Place'), '</th>';
			echo '<th colspan="', $span, '">', WT_I18N::translate('Geographic data'), '</th></tr>';
			echo '<tr>';
			while ($cols<$max) {
				if ($cols == 0) {
					echo '<th colspan="3">', WT_I18N::translate('Country'), '</th>';
				} else {
					echo '<th colspan="3">', WT_I18N::translate('Level'), '&nbsp;', $cols+1, '</th>';
				}
				$cols++;
			}
			echo '</tr><tr>';
			$cols=0;
			while ($cols<$max) {
				echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th><th>', WT_I18N::translate('Latitude'), '</th><th>', WT_I18N::translate('Longitude'), '</th>';
				$cols++;
			}
			echo '</tr>';
			$countrows=0;
			$matched = array();
			while ($x<$i) {
				$placestr="";
				$levels=explode(",", $place_list[$x]);
				$parts=count($levels);
				$levels=array_reverse($levels);
				$placestr.="<a href=\"placelist.php?action=show";
				foreach ($levels as $pindex=>$ppart) {
					$ppart=urlencode(trim($ppart));
					$placestr.="&amp;parent[$pindex]=".$ppart."";
				}
				$placestr.="\">".$place_list[$x]."</a>";
				$gedplace="<tr><td>".$placestr."</td>";
				$z=0;
				$id=0;
				$level=0;
				$matched[$x]=0;// used to exclude places where the gedcom place is matched at all levels
				$mapstr_edit="<a href=\"#\" onclick=\"edit_place_location('";
				$mapstr_add="<a href=\"#\" onclick=\"add_place_location('";
				$mapstr3="";
				$mapstr4="";
				$mapstr5="')\" title='";
				$mapstr6="' >";
				$mapstr7="')\">";
				$mapstr8="</a>";
				$plac = array();
				$lati = array();
				$long = array();
				while ($z<$parts) {
					if ($levels[$z]==' ' || $levels[$z]=='')
						$levels[$z]="unknown";// GoogleMap module uses "unknown" while GEDCOM uses , ,

					$levels[$z]=rtrim(ltrim($levels[$z]));

					$placelist = $this->createPossiblePlaceNames($levels[$z], $z+1); // add the necessary prefix/postfix values to the place name
					foreach ($placelist as $key=>$placename) {
						$row=
							WT_DB::prepare("SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
							->execute(array($z, $id, $placename))
							->fetchOneRow(PDO::FETCH_ASSOC);
						if (!empty($row['pl_id'])) {
							$row['pl_placerequested']=$levels[$z]; // keep the actual place name that was requested so we can display that instead of what is in the db
							break;
						}
					}
					if ($row['pl_id']!='') {
						$id=$row['pl_id'];
					}

					if ($row['pl_place']!='') {
						$placestr2=$mapstr_edit.$id."&amp;level=".$level.$mapstr3.$mapstr5.WT_I18N::translate('Zoom=').$row['pl_zoom'].$mapstr6.$row['pl_placerequested'].$mapstr8;
						if ($row['pl_place']=='unknown')
							$matched[$x]++;
					} else {
						if ($levels[$z]=="unknown") {
							$placestr2=$mapstr_add.$id."&amp;level=".$level.$mapstr3.$mapstr7."<strong>".rtrim(ltrim(WT_I18N::translate('unknown')))."</strong>".$mapstr8;$matched[$x]++;
						} else {
							$placestr2=$mapstr_add.$id."&amp;place_name=".urlencode($levels[$z])."&amp;level=".$level.$mapstr3.$mapstr7.'<span class="error">'.rtrim(ltrim($levels[$z])).'</span>'.$mapstr8;$matched[$x]++;
						}
					}
					$plac[$z]="<td>".$placestr2."</td>\n";
					if ($row['pl_lati']=='0') {
						$lati[$z]="<td class='error'><strong>".$row['pl_lati']."</strong></td>";
					} elseif ($row['pl_lati']!='') {
						$lati[$z]="<td>".$row['pl_lati']."</td>";
					} else {
						$lati[$z]="<td class='error center'><strong>X</strong></td>";$matched[$x]++;
					}
					if ($row['pl_long']=='0') {
						$long[$z]="<td class='error'><strong>".$row['pl_long']."</strong></td>";
					} elseif ($row['pl_long']!='') {
						$long[$z]="<td>".$row['pl_long']."</td>";
					} else {
						$long[$z]="<td class='error center'><strong>X</strong></td>";$matched[$x]++;
					}
					$level++;
					$mapstr3=$mapstr3."&amp;parent[".$z."]=".WT_Filter::escapeJs($row['pl_placerequested']);
					$mapstr4=$mapstr4."&amp;parent[".$z."]=".WT_Filter::escapeJs($levels[$z]);
					$z++;
				}
				if ($matching) {
					$matched[$x]=1;
				}
				if ($matched[$x]!=0) {
					echo $gedplace;
					$z=0;
					while ($z<$max) {
						if ($z<$parts) {
							echo $plac[$z];
							echo $lati[$z];
							echo $long[$z];
						} else {
							echo '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
						}
						$z++;
					}
					echo '</tr>';
					$countrows++;
				}
				$x++;
			}
			// echo final row of table
			echo '<tr><td colspan="2" class="accepted">', /* I18N: A count of places */ WT_I18N::translate('Total places: %s', WT_I18N::number($countrows)), '</td></tr></table></div>';
			break;
		default:
			// Do not run until user selects a gedcom/place/etc.
			// Instead, show some useful help info.
			echo '<div class="gm_check_top accepted">', WT_I18N::translate('This will list all the places from the selected GEDCOM file.  By default this will NOT INCLUDE places that are fully matched between the GEDCOM file and the GoogleMap tables'), '</div>';
			break;
		}
	}

	/**
	 * @return string
	 */
	private function checkMapData() {
		global $controller;
		$xrefs    = "'" . $controller->record->getXref() . "'";
		$families = $controller->record->getSpouseFamilies();
		foreach ($families as $family) {
			$xrefs .= ", '" . $family->getXref() . "'";
		}

		return WT_DB::prepare(
			"SELECT COUNT(*) AS tot FROM `##placelinks` WHERE pl_gid IN (" . $xrefs . ") AND pl_file=?"
		)->execute(array(WT_GED_ID))->fetchOne();
	}

	/**
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
	 * @param string  $placename
	 * @param integer $level
	 *
	 * @return string[]
	 */
	private function createPossiblePlaceNames($placename, $level) {
		$retlist = array();
		if ($level <= 9) {
			$retlist = $this->removePrefixAndSuffixFromPlaceName($this->getSetting('GM_PREFIX_' . $level), $this->getSetting('GM_POSTFIX_' . $level), $placename, $retlist); // Remove both
			$retlist = $this->removePrefixFromPlaceName($this->getSetting('GM_PREFIX_' . $level), $placename, $retlist); // Remove prefix
			$retlist = $this->removeSuffixFromPlaceName($this->getSetting('GM_POSTFIX_' . $level), $placename, $retlist); // Remove suffix
		}
		$retlist[] = $placename; // Exact

		return $retlist;
	}

	/**
	 * @param string $place
	 *
	 * @return null|stdClass
	 */
	private function getLatitudeAndLongitudeFromPlaceLocation($place) {
		$parent   = explode(',', $place);
		$parent   = array_reverse($parent);
		$place_id = 0;
		for ($i = 0; $i < count($parent); $i++) {
			$parent[$i] = trim($parent[$i]);
			if (empty($parent[$i])) {
				$parent[$i] = 'unknown'; // GoogleMap module uses "unknown" while GEDCOM uses , ,
			}
			$placelist = $this->createPossiblePlaceNames($parent[$i], $i + 1);
			foreach ($placelist as $placename) {
				$pl_id = WT_DB::prepare(
					"SELECT pl_id FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place"
				)->execute(array($i, $place_id, $placename))->fetchOne();
				if (!empty($pl_id)) {
					break;
				}
			}
			if (empty($pl_id)) {
				break;
			}
			$place_id = $pl_id;
		}

		return WT_DB::prepare(
			"SELECT sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, pl_lati, pl_long, pl_zoom, pl_icon, pl_level" .
			" FROM `##placelocation`" .
			" WHERE pl_id = ?" .
			" ORDER BY pl_place"
		)->execute(array($place_id))->fetchOneRow();
	}

	/**
	 * @param WT_Individual $indi
	 * @param WT_Fact[]     $indifacts
	 * @param string[]      $famids
	 */
	private function buildIndividualMap(WT_Individual $indi, $indifacts, $famids) {
		$GM_MAX_ZOOM = $this->getSetting('GM_MAX_ZOOM');

		// Create the markers list array
		$gmarks = array();
		sort_facts($indifacts);
		$i = 0;
		foreach ($indifacts as $fact) {
			if (!$fact->getPlace()->isEmpty()) {
				$ctla = preg_match("/\d LATI (.*)/", $fact->getGedcom(), $match1);
				$ctlo = preg_match("/\d LONG (.*)/", $fact->getGedcom(), $match2);

				if ($fact->getParent() instanceof WT_Family) {
					$spouse = $fact->getParent()->getSpouse($indi);
				} else {
					$spouse = null;
				}
				if ($ctla && $ctlo) {
					$i++;
					$gmarks[$i]=array(
						'class'        => 'optionbox',
						'date'         => $fact->getDate()->display(true),
						'fact_label'   => $fact->getLabel(),
						'image'        => $spouse ? $spouse->displayImage() : $fact->icon(),
						'info'         => $fact->getValue(),
						'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.') , $match1[1]),
						'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.') , $match2[1]),
						'name'         => $spouse ? '<a href="' . $spouse->getHtmlUrl() . '"' . $spouse->getFullName() . '</a>' : '',
						'pl_icon'      => '',
						'place'        => $fact->getPlace()->getFullName(),
						'sv_bearing'   => '0',
						'sv_elevation' => '0',
						'sv_lati'      => '0',
						'sv_long'      => '0',
						'sv_zoom'      => '0',
						'tooltip'      => $fact->getPlace()->getGedcomName(),
					);
				} else {
					$latlongval = $this->getLatitudeAndLongitudeFromPlaceLocation($fact->getPlace()->getGedcomName());
					if ($latlongval && $latlongval->pl_lati && $latlongval->pl_long) {
						$i++;
						$gmarks[$i] = array(
							'class'        => 'optionbox',
							'date'         => $fact->getDate()->display(true),
							'fact_label'   => $fact->getLabel(),
							'image'        => $spouse ? $spouse->displayImage() : $fact->icon(),
							'info'         => $fact->getValue(),
							'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval->pl_lati),
							'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval->pl_long),
							'name'         => $spouse ? '<a href="' . $spouse->getHtmlUrl() . '"' . $spouse->getFullName() . '</a>' : '',
							'pl_icon'      => $latlongval->pl_icon,
							'place'        => $fact->getPlace()->getFullName(),
							'sv_bearing'   => $latlongval->sv_bearing,
							'sv_elevation' => $latlongval->sv_elevation,
							'sv_lati'      => $latlongval->sv_lati,
							'sv_long'      => $latlongval->sv_long,
							'sv_zoom'      => $latlongval->sv_zoom,
							'tooltip'      => $fact->getPlace()->getGedcomName(),
						);
						if ($GM_MAX_ZOOM > $latlongval->pl_zoom) {
							$GM_MAX_ZOOM = $latlongval->pl_zoom;
						}
					}
				}
			}
		}

		// Add children to the markers list array
		foreach ($famids as $xref) {
			$family = WT_Family::getInstance($xref);
			foreach ($family->getChildren() as $child) {
				$birth = $child->getFirstFact('BIRT');
				if ($birth) {
					$birthrec = $birth->getGedcom();
					if (!$birth->getPlace()->isEmpty()) {
						$ctla = preg_match('/\n4 LATI (.+)/', $birthrec, $match1);
						$ctlo = preg_match('/\n4 LONG (.+)/', $birthrec, $match2);
						if ($ctla && $ctlo) {
							$i++;
							$gmarks[$i]=array(
								'date'         => $birth->getDate()->display(true),
								'image'        => $child->displayImage(),
								'info'         => '',
								'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.'), $match1[1]),
								'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.'), $match2[1]),
								'name'         => '<a href="' . $child->getHtmlUrl() . '"' . $child->getFullName() . '</a>',
								'pl_icon'      => '',
								'place'        => $birth->getPlace()->getFullName(),
								'sv_bearing'   => '0',
								'sv_elevation' => '0',
								'sv_lati'      => '0',
								'sv_long'      => '0',
								'sv_zoom'      => '0',
								'tooltip'      => $birth->getPlace()->getGedcomName(),
							);
							switch ($child->getSex()) {
							case'F':
								$gmarks[$i]['fact_label'] = WT_I18N::translate('daughter');
								$gmarks[$i]['class']      = 'person_boxF';
								break;
							case 'M':
								$gmarks[$i]['fact_label'] = WT_I18N::translate('son');
								$gmarks[$i]['class']      = 'person_box';
								break;
							default:
								$gmarks[$i]['fact_label'] = WT_I18N::translate('child');
								$gmarks[$i]['class']      = 'person_boxNN';
								break;
							}
						} else {
							$latlongval = $this->getLatitudeAndLongitudeFromPlaceLocation($birth->getPlace()->getGedcomName());
							if ($latlongval && $latlongval->pl_lati && $latlongval->pl_long) {
								$i++;
								$gmarks[$i] = array(
									'date'         => $birth->getDate()->display(true),
									'image'        => $child->displayImage(),
									'info'         => '',
									'lat'          => str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval->pl_lati),
									'lng'          => str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval->pl_long),
									'name'         => '<a href="' . $child->getHtmlUrl() . '"' . $child->getFullName() . '</a>',
									'pl_icon'      => $latlongval->pl_icon,
									'place'        => $birth->getPlace()->getFullName(),
									'sv_bearing'   => $latlongval->sv_bearing,
									'sv_elevation' => $latlongval->sv_elevation,
									'sv_lati'      => $latlongval->sv_lati,
									'sv_long'      => $latlongval->sv_long,
									'sv_zoom'      => $latlongval->sv_zoom,
									'tooltip'      => $birth->getPlace()->getGedcomName(),
								);
								switch ($child->getSex()) {
								case 'M':
									$gmarks[$i]['fact_label'] = WT_I18N::translate('son');
									$gmarks[$i]['class']      = 'person_box';
									break;
								case 'F':
									$gmarks[$i]['fact_label'] = WT_I18N::translate('daughter');
									$gmarks[$i]['class']      = 'person_boxF';
									break;
								default:
									$gmarks[$i]['fact_label'] = WT_I18N::translate('child');
									$gmarks[$i]['class']      = 'option_boxNN';
									break;
								}
								if ($GM_MAX_ZOOM > $latlongval->pl_zoom) {
									$GM_MAX_ZOOM = $latlongval->pl_zoom;
								}
							}
						}
					}
				}
			}
		}

		// Group markers by location
		$location_groups = array();
		foreach ($gmarks as $gmark) {
			$key = $gmark['lat'] . $gmark['lng'];
			if (isset($location_groups[$key])) {
				$location_groups[$key][] = $gmark;
			} else {
				$location_groups[$key] = array($gmark);
			}
		}
		$location_groups = array_values($location_groups);

		// *** ENABLE STREETVIEW ***
		$STREETVIEW = $this->getSetting('GM_USE_STREETVIEW');
		?>

		<script>
			// this variable will collect the html which will eventually be placed in the side_bar
			var side_bar_html = '';
			var map_center = new google.maps.LatLng(0,0);
			var gmarkers = [];
			var gicons = [];
			var map = null;
			var head = '';
			var dir = '';
			var svzoom = '';

			var infowindow = new google.maps.InfoWindow({});

			gicons["red"] = new google.maps.MarkerImage("https://maps.google.com/mapfiles/marker.png",
				new google.maps.Size(20, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34)
			);

			var iconImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/marker.png",
				new google.maps.Size(20, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34)
			);

			var iconShadow = new google.maps.MarkerImage("https://www.google.com/mapfiles/shadow50.png",
				new google.maps.Size(37, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(9, 34)
			);

			var iconShape = {
				coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
				type: "poly"
			};

			function getMarkerImage(iconColor) {
				if (typeof(iconColor) === 'undefined' || iconColor === null) {
					iconColor = 'red';
				}
				if (!gicons[iconColor]) {
					gicons[iconColor] = new google.maps.MarkerImage('//maps.google.com/mapfiles/marker'+ iconColor +'.png',
					new google.maps.Size(20, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(9, 34));
				}
				return gicons[iconColor];
			}

			var sv2_bear = null;
			var sv2_elev = null;
			var sv2_zoom = null;
			var placer   = null;

			// A function to create the marker and set up the event window
			function createMarker(latlng, html, tooltip, sv_lati, sv_long, sv_bearing, sv_elevation, sv_zoom, sv_point, marker_icon) {
				var contentString = '<div id="iwcontent">'+html+'</div>';

				// Use flag icon (if defined) instead of regular marker icon
				if (marker_icon) {
					var icon_image = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/'+marker_icon,
						new google.maps.Size(25, 15),
						new google.maps.Point(0,0),
						new google.maps.Point(0, 44));
					var icon_shadow = new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+'googlemap/images/flag_shadow.png',
						new google.maps.Size(35, 45), // Shadow size
						new google.maps.Point(0,0),   // Shadow origin
						new google.maps.Point(1, 45)  // Shadow anchor is base of flagpole
					);
				} else {
					var icon_image = getMarkerImage('red');
					var icon_shadow = iconShadow;
				}

				// Decide if marker point is Regular (latlng) or StreetView (sv_point) derived
				if (sv_point == '(0, 0)' || sv_point == '(null, null)') {
					placer = latlng;
				} else {
					placer = sv_point;
				}

				// Define the marker
				var marker = new google.maps.Marker({
					position: placer,
					icon:     icon_image,
					shadow:   icon_shadow,
					map:      map,
					title:    tooltip,
					zIndex:   Math.round(latlng.lat()*-100000)<<5
				});

				// Store the tab and event info as marker properties
				marker.sv_lati  = sv_lati;
				marker.sv_long  = sv_long;
				marker.sv_point = sv_point;

				if (sv_bearing == '') {
					marker.sv_bearing = 0;
				} else {
					marker.sv_bearing = sv_bearing;
				}
				if (sv_elevation == '') {
					marker.sv_elevation = 5;
				} else {
					marker.sv_elevation = sv_elevation;
				}
				if (sv_zoom == '' || sv_zoom == 0 || sv_zoom == 1) {
					marker.sv_zoom = 1.2;
				} else {
					marker.sv_zoom = sv_zoom;
				}

				marker.sv_latlng = new google.maps.LatLng(sv_lati, sv_long);
				gmarkers.push(marker);

				// Open infowindow when marker is clicked
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.close();
					infowindow.setContent(contentString);
					infowindow.open(map, marker);
					var panoramaOptions = {
						position:          marker.position,
						mode:              'html5',
						navigationControl: false,
						linksControl:      false,
						addressControl:    false,
						pov: {
							heading: sv_bearing,
							pitch:   sv_elevation,
							zoom:    sv_zoom
						}
					};

					// Use jquery for info window tabs
					google.maps.event.addListener(infowindow, 'domready', function() {
		  	    //jQuery code here
						jQuery('#EV').click(function() {
							document.tabLayerEV = document.getElementById("EV");
							document.tabLayerEV.style.background = '#ffffff';
							document.tabLayerEV.style.paddingBottom = '1px';
							<?php if ($STREETVIEW) { ?>
							document.tabLayerSV = document.getElementById("SV");
							document.tabLayerSV.style.background = '#cccccc';
							document.tabLayerSV.style.paddingBottom = '0px';
							<?php } ?>
							document.panelLayer1 = document.getElementById("pane1");
							document.panelLayer1.style.display = 'block';
							<?php if ($STREETVIEW) { ?>
							document.panelLayer2 = document.getElementById("pane2");
							document.panelLayer2.style.display = 'none';
							<?php } ?>
						});

						jQuery('#SV').click(function() {
							document.tabLayerEV = document.getElementById("EV");
							document.tabLayerEV.style.background = '#cccccc';
							document.tabLayerEV.style.paddingBottom = '0px';
							<?php if ($STREETVIEW) { ?>
							document.tabLayerSV = document.getElementById("SV");
							document.tabLayerSV.style.background = '#ffffff';
							document.tabLayerSV.style.paddingBottom = '1px';
							<?php } ?>
							document.panelLayer1 = document.getElementById("pane1");
							document.panelLayer1.style.display = 'none';
							<?php if ($STREETVIEW) { ?>
							document.panelLayer2 = document.getElementById("pane2");
							document.panelLayer2.style.display = 'block';
							<?php } ?>
							var panorama = new google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
							setTimeout(function() { panorama.setVisible(true); }, 100);
							setTimeout(function() { panorama.setVisible(true); }, 500);
						});
					});
				});
			}

			// Opens Marker infowindow when corresponding Sidebar item is clicked
			function myclick(i) {
				infowindow.close();
				google.maps.event.trigger(gmarkers[i], 'click');
			}

			// Home control
			// returns the user to the original map position ... loadMap() function
			// This constructor takes the control DIV as an argument.
			function HomeControl(controlDiv, map) {
				// Set CSS styles for the DIV containing the control
				// Setting padding to 5 px will offset the control from the edge of the map
				controlDiv.style.paddingTop = '5px';
				controlDiv.style.paddingRight = '0px';

				// Set CSS for the control border
				var controlUI = document.createElement('DIV');
				controlUI.style.backgroundColor = 'white';
				controlUI.style.borderStyle = 'solid';
				controlUI.style.borderWidth = '2px';
				controlUI.style.cursor = 'pointer';
				controlUI.style.textAlign = 'center';
				controlUI.title = '';
				controlDiv.appendChild(controlUI);

				// Set CSS for the control interior
				var controlText = document.createElement('DIV');
				controlText.style.fontFamily = 'Arial,sans-serif';
				controlText.style.fontSize = '12px';
				controlText.style.paddingLeft = '15px';
				controlText.style.paddingRight = '15px';
				controlText.innerHTML = '<b><?php echo WT_I18N::translate('Redraw map')?></b>';
				controlUI.appendChild(controlText);

				// Setup the click event listeners: simply set the map to original LatLng
				google.maps.event.addDomListener(controlUI, 'click', function() {
					loadMap();
				});
			}

			function loadMap() {
				<?php
					global $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS, $SHOW_HIGHLIGHT_IMAGES;
				?>

				// Create the map and mapOptions
				var mapOptions = {
					zoom: 7,
					center: map_center,
					mapTypeId: google.maps.MapTypeId.<?php echo $this->getSetting('GM_MAP_TYPE'); ?>,
					mapTypeControlOptions: {
						style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
					},
					navigationControl: true,
					navigationControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
					style: google.maps.NavigationControlStyle.SMALL  // ANDROID, DEFAULT, SMALL, ZOOM_PAN
					},
					streetViewControl: false,  // Show Pegman or not
					scrollwheel: false
				};
				map = new google.maps.Map(document.getElementById('map_pane'), mapOptions);

				// Close any infowindow when map is clicked
				google.maps.event.addListener(map, 'click', function() {
					infowindow.close();
				});

				// Create the Home DIV and call the HomeControl() constructor in this DIV.
				var homeControlDiv = document.createElement('DIV');
				var homeControl = new HomeControl(homeControlDiv, map);
				homeControlDiv.index = 1;
				map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);

				// Add the markers to the map from the $gmarks array
				var locations = [
					<?php foreach($gmarks as $n=>$gmark) { ?>
					<?php echo $n ? ',' : ''; ?>
					{
						"event":        "<?php echo WT_Filter::escapeJs($gmark['fact_label']  ); ?>",
						"lat":          "<?php echo WT_Filter::escapeJs($gmark['lat']         ); ?>",
						"lng":          "<?php echo WT_Filter::escapeJs($gmark['lng']         ); ?>",
						"date":         "<?php echo WT_Filter::escapeJs($gmark['date']        ); ?>",
						"info":         "<?php echo WT_Filter::escapeJs($gmark['info']        ); ?>",
						"name":         "<?php echo WT_Filter::escapeJs($gmark['name']        ); ?>",
						"place":        "<?php echo WT_Filter::escapeJs($gmark['place']       ); ?>",
						"tooltip":      "<?php echo WT_Filter::escapeJs($gmark['tooltip']     ); ?>",
						"image":        "<?php echo WT_Filter::escapeJs($gmark['image']       ); ?>",
						"pl_icon":      "<?php echo WT_Filter::escapeJs($gmark['pl_icon']     ); ?>",
						"sv_lati":      "<?php echo WT_Filter::escapeJs($gmark['sv_lati']     ); ?>",
						"sv_long":      "<?php echo WT_Filter::escapeJs($gmark['sv_long']     ); ?>",
						"sv_bearing":   "<?php echo WT_Filter::escapeJs($gmark['sv_bearing']  ); ?>",
						"sv_elevation": "<?php echo WT_Filter::escapeJs($gmark['sv_elevation']); ?>",
						"sv_zoom":      "<?php echo WT_Filter::escapeJs($gmark['sv_zoom']     ); ?>"
					}
					<?php } ?>
				];

				// Group the markers by location
				var location_groups = new Array();
				for (var key in locations) {
					if (!location_groups.hasOwnProperty(locations[key].place)) {
						location_groups[locations[key].place] = new Array();
					}
					location_groups[locations[key].place].push(locations[key]);
				}
				// TODO: why doesn't this next line work?
				//var location_groups = <?php echo json_encode($location_groups); ?>;

				// Set the Marker bounds
				var bounds = new google.maps.LatLngBounds ();

				var key;
				// Iterate over each location
				for (key in location_groups) {
					var locations = location_groups[key];
					// Iterate over each marker at this location
					var event_details = '';
					for (var j in locations) {
						var location = locations[j];
						if (location.info && location.name) {
							event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span> ' + location.info + '<br><b>' + location.name + '</b><br>' + location.date + '<br></p></td></tr></table>';
						} else if (location.name) {
							event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span><br><b>' + location.name + '</b><br>' + location.date + '<br></p></td></tr></table>';
						} else if (location.info) {
							event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span> ' + location.info + '<br>' + location.date + '<br></p></td></tr></table>';
						} else {
							event_details += '<table><tr><td class="highlt_img">' + location.image + '</td><td><p><span id="sp1">' + location.event + '</span><br>' + location.date + '<br></p></td></tr></table>';
						}
					}
					// All locations are the same in each group, so create a marker with the first
					var location = location_groups[key][0];
					var html =
					'<div class="infowindow">' +
						'<div id="gmtabs">' +
							'<ul class="tabs" >' +
								'<li><a href="#event" id="EV"><?php echo WT_I18N::translate('Events'); ?></a></li>' +
								<?php if ($STREETVIEW) { ?>
								'<li><a href="#sview" id="SV"><?php echo WT_I18N::translate('Google Street View™'); ?></a></li>' +
								<?php } ?>
							'</ul>' +
							'<div class="panes">' +
								'<div id="pane1">' +
									'<h4 id="iwhead">' + location.place + '</h4>' +
									event_details +
								'</div>' +
								<?php if ($STREETVIEW) { ?>
								'<div id="pane2">' +
									'<h4 id="iwhead">' + location.place + '</h4>' +
									'<div id="pano"></div>' +
								'</div>' +
								<?php } ?>
							'</div>' +
						'</div>' +
					'</div>';

					// create the marker
					var point        = new google.maps.LatLng(location.lat,     location.lng);     // Place Latitude, Longitude
					var sv_point     = new google.maps.LatLng(location.sv_lati, location.sv_long); // StreetView Latitude and Longitide

					var zoomLevel = <?php echo $GM_MAX_ZOOM; ?>;
					var marker    = createMarker(point, html, location.tooltip, location.sv_lati, location.sv_long, location.sv_bearing, location.sv_elevation, location.sv_zoom, sv_point, location.pl_icon);

					// if streetview coordinates are available, use them for marker,
					// else use the place coordinates
					if (sv_point && sv_point != "(0, 0)") {
						var myLatLng = sv_point;
					} else {
						var myLatLng = point;
					}

					// Correct zoom level when only one marker is present
					if (location_groups.length == 1) {
						bounds.extend(myLatLng);
						map.setZoom(zoomLevel);
						map.setCenter(myLatLng);
					} else {
						bounds.extend(myLatLng);
						map.fitBounds(bounds);
						// Correct zoom level when multiple markers have the same coordinates
						var listener1 = google.maps.event.addListenerOnce(map, "idle", function() {
							if (map.getZoom() > zoomLevel) {
								map.setZoom(zoomLevel);
							}
							google.maps.event.removeListener(listener1);
						});
					}
				} // end loop through location markers
			} // end loadMap()

		</script>
		<?php
		// Create the normal googlemap sidebar of events and children
		echo '<div style="overflow: auto; overflow-x: hidden; overflow-y: auto; height:', $this->getSetting('GM_YSIZE'), 'px;"><table class="facts_table">';

		foreach ($location_groups as $key=>$location_group) {
			foreach ($location_group as $gmark) {
				echo '<tr>';
				echo '<td class="facts_label">';
				echo '<a href="#" onclick="myclick(\'', WT_Filter::escapeHtml($key), '\')">', $gmark['fact_label'], '</a></td>';
				echo '<td class="', $gmark['class'], '" style="white-space: normal">';
				if ($gmark['info']) {
					echo '<span class="field">', WT_Filter::escapeHtml($gmark['info']), '</span><br>';
				}
				if ($gmark['name']) {
					echo $gmark['name'], '<br>';
				}
				echo $gmark['place'], '<br>';
				if ($gmark['date']) {
					echo $gmark['date'], '<br>';
				}
				echo '</td>';
				echo '</tr>';
			}
		}
		echo '</table></div><br>';
	}

	/**
	 * @param string $place
	 *
	 * @return integer
	 */
	private function getPlaceLocationId($place) {
		$par      = explode(',', strip_tags($place));
		$par      = array_reverse($par);
		$place_id = 0;
		for ($i = 0; $i < count($par); $i++) {
			$par[$i] = trim($par[$i]);
			if (empty($par[$i])) {
				$par[$i] = 'unknown';
			}
			$placelist = $this->createPossiblePlaceNames($par[$i], $i + 1);
			foreach ($placelist as $key => $placename) {
				$pl_id = WT_DB::prepare(
					"SELECT pl_id FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place"
				)->execute(array($i, $place_id, $placename))->fetchOne();
				if (!empty($pl_id)) {
					break;
				}
			}
			if (empty($pl_id)) {
				break;
			}
			$place_id = $pl_id;
		}

		return $place_id;
	}

	/**
	 * @param string $place
	 *
	 * @return integer
	 */
	private function getPlaceId($place) {
		$par      = explode(',', $place);
		$par      = array_reverse($par);
		$place_id = 0;
		for ($i = 0; $i < count($par); $i++) {
			$par[$i]   = trim($par[$i]);
			$placelist = $this->createPossiblePlaceNames($par[$i], $i + 1);
			foreach ($placelist as $key => $placename) {
				$pl_id = WT_DB::prepare(
					"SELECT p_id FROM `##places` WHERE p_parent_id=? AND p_file=? AND p_place LIKE ? ORDER BY p_place"
				)->execute(array($place_id, WT_GED_ID, $placename))->fetchOne();
				if (!empty($pl_id)) {
					break;
				}
			}
			if (empty($pl_id)) {
				break;
			}
			$place_id = $pl_id;
		}

		return $place_id;
	}

	/**
	 * @param integer  $level
	 * @param string[] $parent
	 *
	 * @return integer
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
	 * @param integer  $level
	 * @param string[] $parent
	 *
	 * @return integer
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
	 *
	 * @param string $placelevels
	 */
	public function createMap($placelevels) {
		global $level, $levelm, $plzoom, $controller;

		$STREETVIEW = $this->getSetting('GM_USE_STREETVIEW');
		$parent = WT_Filter::getArray('parent');

		// create the map
		echo '<table style="margin:20px auto 0 auto;"><tr valign="top"><td>';
		//<!-- start of map display -->
		echo '<table><tr valign="top">';
		echo '<td class="center" width="200px">';

		$levelm = $this->setLevelMap($level, $parent);
		$latlng =
			WT_DB::prepare("SELECT pl_place, pl_id, pl_lati, pl_long, pl_zoom, sv_long, sv_lati, sv_bearing, sv_elevation, sv_zoom FROM `##placelocation` WHERE pl_id=?")
			->execute(array($levelm))
			->fetch(PDO::FETCH_ASSOC);
		if ($STREETVIEW && $level!=0 ) {
			echo '<div id="place_map" style="margin-top:20px; border:1px solid gray; width: ', $this->getSetting('GM_PH_XSIZE'), 'px; height: ', $this->getSetting('GM_PH_YSIZE'), 'px; ';
		} else {
			echo '<div id="place_map" style="border:1px solid gray; width:', $this->getSetting('GM_PH_XSIZE'), 'px; height:', $this->getSetting('GM_PH_YSIZE'), 'px; ';
		}
		echo "\"><i class=\"icon-loading-large\"></i></div>";
		echo '</td>';
		echo '<script src="', $this->googleMapsScript(), '"></script>';

		$plzoom	= $latlng['pl_zoom'];		// Map zoom level

		if (Auth::isAdmin()) {
			$placecheck_url = 'module.php?mod=googlemap&amp;mod_action=admin_placecheck';
			if ($parent && isset($parent[0]) ) {
				$placecheck_url .= '&amp;country='.$parent[0];
				if (isset($parent[1])) {
					$placecheck_url .= '&amp;state='.$parent[1];
				}
			}
			$adminplaces_url = 'module.php?mod=googlemap&amp;mod_action=admin_places';
			if ($latlng && isset($latlng['pl_id'])) {
				$adminplaces_url .= '&amp;parent='.$latlng['pl_id'];
			}
			echo '</tr><tr><td>';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
			echo '&nbsp;|&nbsp;';
			echo '<a href="'.$adminplaces_url.'">', WT_I18N::translate('Geographic data'), '</a>';
			echo '&nbsp;|&nbsp;';
			echo '<a href="'.$placecheck_url.'">', WT_I18N::translate('Place check'), '</a>';
			if (array_key_exists('batch_update', WT_Module::getActiveModules())) {
				$placelevels=preg_replace('/, '.WT_I18N::translate('unknown').'/', ', ', $placelevels); // replace ", unknown" with ", "
				$placelevels=substr($placelevels, 2); // remove the leading ", "
				if ($placelevels) {
					$batchupdate_url='module.php?mod=batch_update&amp;mod_action=admin_batch_update&amp;plugin=search_replace_bu_plugin&amp;method=exact&amp;ged='.WT_GEDCOM.'&amp;search='.urlencode($placelevels); // exact match
					echo '&nbsp;|&nbsp;';
					echo '<a href="'.$batchupdate_url.'">', WT_I18N::translate('Batch update'), '</a>';
				}
			}
		}
		echo '</td></tr></table>';
		echo '</td>';
		echo '<td style="margin-left:15px; float:right;">';

		if ($STREETVIEW) {
			$controller->addInlineJavascript('
				function update_sv_params(placeid) {
					var svlati = document.getElementById("sv_latiText").value.slice(0, -1);
					var svlong = document.getElementById("sv_longText").value.slice(0, -1);
					var svbear = document.getElementById("sv_bearText").value.slice(0, -1);
					var svelev = document.getElementById("sv_elevText").value.slice(0, -1);
					var svzoom = document.getElementById("sv_zoomText").value;
					win03 = window.open("module.php?mod=googlemap&mod_action=places_edit&action=update_sv_params&placeid="+placeid+"&svlati="+svlati+"&svlong="+svlong+"&svbear="+svbear+"&svelev="+svelev+"&svzoom="+svzoom, "win03", indx_window_specs);
					if (window.focus) {win03.focus();}
				}
			');

			global $pl_lati, $pl_long;
			if ($level>=1) {
				$pl_lati = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlng['pl_lati']);	// WT_placelocation lati
				$pl_long = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlng['pl_long']);	// WT_placelocation long

				// Check if Streetview location parameters are stored in database
				$placeid	= $latlng['pl_id'];			// Placelocation place id
				$sv_lat		= $latlng['sv_lati'];		// StreetView Point of View Latitude
				$sv_lng		= $latlng['sv_long'];		// StreetView Point of View Longitude
				$sv_dir		= $latlng['sv_bearing'];	// StreetView Point of View Direction (degrees from North)
				$sv_pitch	= $latlng['sv_elevation'];	// StreetView Point of View Elevation (+90 to -90 degrees (+=down, -=up)
				$sv_zoom	= $latlng['sv_zoom'];		// StreetView Point of View Zoom (0, 1, 2 or 3)

				// Check if Street View Lati/Long are the default of 0 or null, if so use regular Place Lati/Long to set an initial location for the panda ------------
				if (($latlng['sv_lati']==null && $latlng['sv_long']==null) || ($latlng['sv_lati']==0 && $latlng['sv_long']==0)) {
						$sv_lat = $pl_lati;
						$sv_lng = $pl_long;
				}
				// Set Street View parameters to numeric value if NULL (avoids problem with Google Street View™ Pane not rendering)
				if ($sv_dir==null) {
					$sv_dir=0;
				}
				if ($sv_pitch==null) {
					$sv_pitch=0;
				}
				if ($sv_zoom==null) {
					$sv_zoom=1;
				}

				?>
				<div>
				<iframe style="background: transparent; margin-top: -3px; margin-left: 2px; width: 530px; height: 405px; padding: 0; border: 0;" src="module.php?mod=googlemap&amp;mod_action=wt_street_view&amp;x=<?php echo $sv_lng; ?>&amp;y=<?php echo $sv_lat; ?>&amp;z=18&amp;t=2&amp;c=1&amp;s=1&amp;b=<?php echo $sv_dir; ?>&amp;p=<?php echo $sv_pitch; ?>&amp;m=<?php echo $sv_zoom; ?>&amp;j=1&amp;k=1&amp;v=1" marginwidth="0" marginheight="0" frameborder="0" scrolling="no"></iframe>
				</div>

				<?php
				$list_latlon = (
					WT_Gedcom_Tag::getLabel('LATI')."<input name='sv_latiText' id='sv_latiText' type='text' style='width:42px; background:none; border:none;' value='".$sv_lat."'>".
					WT_Gedcom_Tag::getLabel('LONG')."<input name='sv_longText' id='sv_longText' type='text' style='width:42px; background:none; border:none;' value='".$sv_lng."'>".
					/* I18N: Compass bearing (in degrees), for street-view mapping */ WT_I18N::translate('Bearing')."<input name='sv_bearText' id='sv_bearText' type='text' style='width:46px; background:none; border:none;' value='".$sv_dir."'>".
					/* I18N: Angle of elevation (in degrees), for street-view mapping */ WT_I18N::translate('Elevation')."<input name='sv_elevText' id='sv_elevText' type='text' style='width:30px; background:none; border:none;' value='".$sv_pitch."'>".
					WT_I18N::translate('Zoom')."<input name='sv_zoomText' id='sv_zoomText' type='text' style='width:30px; background:none; border:none;' value='".$sv_zoom."'>
				");
				if (Auth::isAdmin()) {
					echo "<table align=\"center\" style=\"margin-left:6px; border:solid 1px black; width:522px; margin-top:-28px; background:#cccccc; \">";
				} else {
					echo "<table align=\"center\" style=\"display:none; \">";
				}
				echo "<tr><td>";
				echo "<form style=\"text-align:left; margin-left:5px; font:11px verdana; color:blue;\" method=\"post\" action=\"\">";
				echo $list_latlon;
				echo "<input type=\"submit\" name=\"Submit\" onclick=\"update_sv_params($placeid);\" value=\"", WT_I18N::translate('save'), "\">";
				echo "</form>";
				echo "</td></tr>";
				echo "</table>";
			}
			// Next line puts Place hierarchy on new row -----
			echo '</td></tr><tr>';
		}	// End Streetview window ===================================================================
	}

	/**
	 * @param integer $numls
	 * @param integer $levelm
	 *
	 * @return integer[]
	 */
	private function checkWhereAmI($numls, $levelm) {
		$where_am_i = $this->placeIdToHierarchy($levelm);
		$i          = $numls + 1;
		$levelo     = array(0 => 0);
		foreach (array_reverse($where_am_i, true) as $id => $place2) {
			$levelo[$i] = $id;
			$i--;
		}

		return $levelo;
	}

	/**
	 * @param integer  $level
	 * @param string[] $parent
	 */
	private function printHowManyPeople($level, $parent) {
		$stats = new WT_Stats(WT_GEDCOM);

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
		echo '<br><br>', WT_I18N::translate('Individuals'), ': ', $place_count_indi, ', ', WT_I18N::translate('Families'), ': ', $place_count_fam;
	}

	private function printGoogleMapMarkers($place2, $level, $parent, $levelm, $linklevels, $placelevels, $lastlevel=false) {
		if (($place2['lati'] == NULL) || ($place2['long'] == NULL) || (($place2['lati'] == '0') && ($place2['long'] == '0'))) {
			echo 'var icon_type = new google.maps.MarkerImage();';
			echo 'icon_type.image = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/marker_yellow.png";';
			echo 'icon_type.shadow = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/shadow50.png";';
			echo 'icon_type.iconSize = google.maps.Size(20, 34);';
			echo 'icon_type.shadowSize = google.maps.Size(37, 34);';
			echo 'var point = new google.maps.LatLng(0, 0);';
			if ($lastlevel)
				echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "'><br>";
			else {
				echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "&amp;parent[{$level}]=";
				if ($place2['place'] == "Unknown") echo "'><br>";
				else echo addslashes($place2['place']), "'><br>";
			}
			if (($place2['icon'] != NULL) && ($place2['icon'] != '')) {
				echo '<img src=\"', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place2['icon'], '\">&nbsp;&nbsp;';
			}
			if ($lastlevel) {
				if ($place2['place'] == 'Unknown') {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes(substr($placelevels, 2));
					} else {
						echo WT_I18N::translate('unknown');
					}
				} else {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes(substr($placelevels, 2));
					} else {
						echo addslashes($place2['place']);
					}
				}
			} else {
				if ($place2['place'] == 'Unknown') {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes(WT_I18N::translate('unknown').$placelevels);
					} else {
						echo WT_I18N::translate('unknown');
					}
				} else {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes($place2['place'].$placelevels);
					} else {
						echo addslashes($place2['place']);
					}
				}
			}
			echo '</a>';
			if ($lastlevel) {
				$this->printHowManyPeople($level, $parent);
			} else {
				$parent[$level]=$place2['place'];
				$this->printHowManyPeople($level+1, $parent);
			}
			echo '<br>', WT_I18N::translate('This place has no coordinates');
			if (Auth::isAdmin())
				echo "<br><a href='module.php?mod=googlemap&amp;mod_action=admin_places&amp;parent=", $levelm, "&amp;display=inactive'>", WT_I18N::translate('Geographic data'), "</a>";
			echo "</div>\", icon_type, \"", str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), addslashes($place2['place'])), "\");\n";
		} else {
			$lati = str_replace(array('N', 'S', ','), array('', '-', '.'), $place2['lati']);
			$long = str_replace(array('E', 'W', ','), array('', '-', '.'), $place2['long']);
			//delete leading zero
			if ($lati >= 0) {
				$lati = abs($lati);
			} elseif ($lati < 0) {
				$lati = '-'.abs($lati);
			}
			if ($long >= 0) {
				$long = abs($long);
			} elseif ($long < 0) {
				$long = '-'.abs($long);
			}

			// flags by kiwi3685 ---
			if (($place2['icon'] == NULL) || ($place2['icon'] == '') || ($this->getSetting('GM_PH_MARKER') != 'G_FLAG')) {
				echo 'var icon_type = new google.maps.MarkerImage();';
			} else {
				echo 'var icon_type = new google.maps.MarkerImage();';
				echo ' icon_type.image = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place2['icon'], '";';
				echo ' icon_type.shadow = "', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/flag_shadow.png";';
				echo ' icon_type.iconSize = new google.maps.Size(25, 15);';
				echo ' icon_type.shadowSize = new google.maps.Size(35, 45);';
			}
			echo "var point = new google.maps.LatLng({$lati}, {$long});";
			if ($lastlevel) {
				echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "'><br>";
			} else {
				echo "var marker = createMarker(point, \"<div class='iwstyle' style='width: 250px;'><a href='?action=find", $linklevels, "&amp;parent[{$level}]=";
				if ($place2['place'] == 'Unknown') {
					echo "'><br>";
				} else {
					echo addslashes($place2['place']), "'><br>";
				}
			}
			if (($place2['icon'] != NULL) && ($place2['icon'] != "")) {
				echo '<img src=\"', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place2['icon'], '\">&nbsp;&nbsp;';
			}
			if ($lastlevel) {
				if ($place2['place'] == 'Unknown') {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes(substr($placelevels, 2));
					} else {
						echo WT_I18N::translate('unknown');
					}
				} else {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes(substr($placelevels, 2));
					} else {
						echo addslashes($place2['place']);
					}
				}
			} else {
				if ($place2['place'] == 'Unknown') {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes(WT_I18N::translate('unknown').$placelevels);
					} else {
						echo WT_I18N::translate('unknown');
					}
				} else {
					if (!$this->getSetting('GM_DISP_SHORT_PLACE')) {
						echo addslashes($place2['place'].$placelevels);
					} else {
						echo addslashes($place2['place']);
					}
				}
			}
			echo '</a>';
			if ($lastlevel) {
				$this->printHowManyPeople($level, $parent);
			} else {
				$parent[$level]=$place2['place'];
				$this->printHowManyPeople($level+1, $parent);
			}
			$temp=addslashes($place2['place']);
			$temp=str_replace(array('&lrm;', '&rlm;'), array(WT_UTF8_LRM, WT_UTF8_RLM), $temp);
			if (!$this->getSetting('GM_COORD')) {
				echo "<br><br></div>\", icon_type, \"", $temp, "\");";
			} else {
				echo "<br><br>", $place2['lati'], ", ", $place2['long'], "</div>\", icon_type, \"", $temp, "\");";
			}
		}
	}

	/**
	 * Called by placelist.php
	 *
	 * @param integer  $numfound
	 * @param integer  $level
	 * @param string[] $parent
	 * @param string   $linklevels
	 * @param string   $placelevels
	 * @param string[] $place_names
	 */
	public function mapScripts($numfound, $level, $parent, $linklevels, $placelevels, $place_names) {
		global $plzoom, $controller;

		$controller->addInlineJavascript('
			jQuery("head").append(\'<link rel="stylesheet" type="text/css" href="' . WT_STATIC_URL . WT_MODULES_DIR . 'googlemap/css/wt_v3_googlemap.css" />\');
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
				zoom: 8,
				center: map_center,
				mapTypeId: google.maps.MapTypeId.' . $this->getSetting('GM_MAP_TYPE') . ',
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
				},
				navigationControl: true,
				navigationControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT, // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
					style: google.maps.NavigationControlStyle.SMALL  // ANDROID, DEFAULT, SMALL, ZOOM_PAN
				},
				streetViewControl: false, // Show Pegman or not
				scrollwheel: false
			};
			map = new google.maps.Map(document.getElementById("place_map"), mapOptions);

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
				// Choose icon and shadow ============
				if (icon.image && '.$level.'<=3) {
					if (icon.image!="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/marker_yellow.png") {
						var iconImage = new google.maps.MarkerImage(icon.image,
						new google.maps.Size(25, 15),
						new google.maps.Point(0,0),
						new google.maps.Point(1, 45));
						var iconShadow = new google.maps.MarkerImage("'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/flag_shadow.png",
						new google.maps.Size(35, 45),
						new google.maps.Point(0,0),
						new google.maps.Point(1, 45));
					 } else {
						var iconImage = new google.maps.MarkerImage(icon.image,
						new google.maps.Size(20, 34),
						new google.maps.Point(0,0),
						new google.maps.Point(9, 34));
						var iconShadow = new google.maps.MarkerImage("https://www.google.com/mapfiles/shadow50.png",
						new google.maps.Size(37, 34),
						new google.maps.Point(0,0),
						new google.maps.Point(9, 34));
					}
					//	*** Clickable area of icon - To be refined later *** ===================================
					//	var iconShape = {
					//	coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
					//	type: "poly"
					//	};
				} else {
					var iconImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/marker.png",
					new google.maps.Size(20, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(9, 34));
					var iconShadow = new google.maps.MarkerImage("https://www.google.com/mapfiles/shadow50.png",
					new google.maps.Size(37, 34),
					new google.maps.Point(0,0),
					new google.maps.Point(9, 34));
					//	*** Clickable area of icon - To be refined later *** ===================================
					//	var iconShape = {
					//	coord: [9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0],
					//	type: "poly"
					//	};
				}
				var posn = new google.maps.LatLng(0,0);
				var marker = new google.maps.Marker({
					position: point,
					icon: iconImage,
					shadow: iconShadow,
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
		if (isset($levelo[0])) $levelo[0]=0;
		$numls = count($parent)-1;
		$levelo = $this->checkWhereAmI($numls, $levelm);
		if ($numfound<2 && ($level==1 || !isset($levelo[$level-1]))) {
			$controller->addInlineJavascript('map.maxZoom=6;');
		} elseif ($numfound<2 && !isset($levelo[$level-2])) {
		} elseif ($level==2) {
			$controller->addInlineJavascript('map.maxZoom=10;');
		}
		//create markers

		ob_start(); // TODO: rewrite print_gm_markers, and the functions called therein, to either return text or add JS directly.

		if ($numfound==0 && $level>0) {
			if (isset($levelo[($level-1)])) {  // ** BH not sure yet what this if statement is for ... TODO **
				// show the current place on the map

				$place = WT_DB::prepare("SELECT pl_id as place_id, pl_place as place, pl_lati as lati, pl_long as `long`, pl_zoom as zoom, pl_icon as icon FROM `##placelocation` WHERE pl_id=?")
				->execute(array($levelm))
				->fetch(PDO::FETCH_ASSOC);

				if ($place) {
					// re-calculate the hierarchy information required to display the current place
					$thisloc = $parent;
					array_pop($thisloc);
					$thislevel = $level-1 ;
					$thislinklevels = substr($linklevels,0,strrpos($linklevels,'&amp;'));
					if (strpos($placelevels,',',1)) {
						$thisplacelevels = substr($placelevels,strpos($placelevels,',',1));
					} else {
						// this is the top level, remove everything
						$thisplacelevels = '';
					}

					$this->printGoogleMapMarkers($place, $thislevel, $thisloc, $place['place_id'], $thislinklevels, $thisplacelevels);
				}
			}
		}

		// display any sub-places
		$placeidlist=array();
		foreach ($place_names as $placename) {
			$thisloc = $parent;
			$thisloc[] = $placename;
			$this_levelm = $this->setLevelMap($level+1, $thisloc);
			if ($this_levelm) $placeidlist[] = $this_levelm;
		}

		if ($placeidlist) {
			// flip the array (thus removing duplicates)
			$placeidlist=array_flip($placeidlist);
			// remove entry for parent location
			unset($placeidlist[$levelm]);
		}
		if ($placeidlist) {
			// the keys are all we care about (this reverses the earlier array_flip, and ensures there are no "holes" in the array)
			$placeidlist=array_keys($placeidlist);
			// note: this implode/array_fill code generates one '?' for each entry in the $placeidlist array
			$placelist =
				WT_DB::prepare('SELECT pl_id as place_id, pl_place as place, pl_lati as lati, pl_long as `long`, pl_zoom as zoom, pl_icon as icon FROM `##placelocation` WHERE pl_id IN ('.implode(',', array_fill(0, count($placeidlist), '?')).')')
				->execute($placeidlist)
				->fetchAll(PDO::FETCH_ASSOC);

			foreach ($placelist as $place) {
				$this->printGoogleMapMarkers($place, $level, $parent, $place['place_id'], $linklevels, $placelevels);
			}
		}
		$controller->addInlineJavascript(ob_get_clean());
	}

	/**
	 * Take a place id and find its place in the hierarchy
	 * Input: place ID
	 * Output: ordered array of id=>name values, starting with the Top level
	 * e.g. 0=>"Top level", 16=>"England", 19=>"London", 217=>"Westminster"
	 * NB This function exists in both places.php and places_edit.php
	 *
	 * @param integer $id
	 *
	 * @return string[]
	 */
	private function placeIdToHierarchy($id) {
		$statement = WT_DB::prepare("SELECT pl_parent_id, pl_place FROM `##placelocation` WHERE pl_id=?");
		$arr       = array();
		while ($id != 0) {
			$row = $statement->execute(array($id))->fetchOneRow();
			$arr = array($id => $row->pl_place) + $arr;
			$id  = $row->pl_parent_id;
		}

		return $arr;
	}

	/**
	 * @return int
	 */
	private function getHighestIndex() {
		return (int)WT_DB::prepare("SELECT MAX(pl_id) FROM `##placelocation`")->fetchOne();
	}

	/**
	 * @return int
	 */
	private function getHighestLevel() {
		return (int)WT_DB::prepare("SELECT MAX(pl_level) FROM `##placelocation`")->fetchOne();
	}

	/**
	 * Find all of the places in the hierarchy
	 *
	 * @param integer $parent_id
	 * @param boolean $inactive
	 *
	 * @return array[]
	 */
	private function getPlaceListLocation($parent_id, $inactive=false) {
		if ($inactive) {
			$rows=
				WT_DB::prepare("SELECT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place COLLATE ".WT_I18N::$collation)
				->execute(array($parent_id))
				->fetchAll();
		} else {
			$rows = WT_DB::prepare(
				"SELECT DISTINCT pl_id, pl_place, pl_lati, pl_long, pl_zoom, pl_icon".
				" FROM `##placelocation`".
				" INNER JOIN `##places` ON `##placelocation`.pl_place=`##places`.p_place".
				" WHERE pl_parent_id=? ORDER BY pl_place COLLATE " . WT_I18N::$collation
			)->execute(array($parent_id))->fetchAll();
		}

		$placelist = array();
		foreach ($rows as $row) {
			$placelist[] = array(
				'place_id' => $row->pl_id,
				'place' => $row->pl_place,
				'lati' => $row->pl_lati,
				'long' => $row->pl_long,
				'zoom' => $row->pl_zoom,
				'icon' => $row->pl_icon,
			);
		}

		return $placelist;
	}

	/**
	 * @param integer $parent_id
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

		$rows = WT_DB::prepare(
			"SELECT pl_id, pl_place, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place"
		)->execute(array($parent_id))->fetchAll();

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
	 */
	private function findFiles($path) {
		global $placefiles;
		if (file_exists($path)) {
			$dir = dir($path);
			while (false !== ($entry = $dir->read())) {
				if ($entry != '.' && $entry != '..' && $entry != '.svn') {
					if (is_dir($path . '/' . $entry)) {
						$this->findFiles($path . '/' . $entry);
					} elseif (strstr($entry, '.csv') !== false) {
						$placefiles[] = preg_replace('~' . WT_MODULES_DIR . 'googlemap/extra~', '', $path) . '/' . $entry;
					}
				}
			}
			$dir->close();
		}
	}

	private function placesEdit() {
		require WT_ROOT.'includes/functions/functions_edit.php';

		$GM_MAX_ZOOM = $this->getSetting('GM_MAX_ZOOM');

		$action     = WT_Filter::post('action',     null, WT_Filter::get('action'));
		$placeid    = WT_Filter::post('placeid',    null, WT_Filter::get('placeid'));
		$place_name = WT_Filter::post('place_name', null, WT_Filter::get('place_name'));

		$controller = new WT_Controller_Simple();
		$controller
				->restrictAccess(Auth::isAdmin())
				->setPageTitle(WT_I18N::translate('Geographic data'))
				->addInlineJavascript('$("<link>", {rel: "stylesheet", type: "text/css", href: "' . WT_STATIC_URL . WT_MODULES_DIR . 'googlemap/css/wt_v3_googlemap.css"}).appendTo("head");')
				->pageHeader();

		$where_am_i=$this->placeIdToHierarchy($placeid);
		$level=count($where_am_i);

		if ($action=='addrecord' && Auth::isAdmin()) {
			$statement=
				WT_DB::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

			if (($_POST['LONG_CONTROL'] == '') || ($_POST['NEW_PLACE_LONG'] == '') || ($_POST['NEW_PLACE_LATI'] == '')) {
				$statement->execute(array($this->getHighestIndex()+1, $placeid, $level, $_POST['NEW_PLACE_NAME'], null, null, $_POST['NEW_ZOOM_FACTOR'], $_POST['icon']));
			} else {
				$statement->execute(array($this->getHighestIndex()+1, $placeid, $level, $_POST['NEW_PLACE_NAME'], $_POST['LONG_CONTROL'][3].$_POST['NEW_PLACE_LONG'], $_POST['LATI_CONTROL'][3].$_POST['NEW_PLACE_LATI'], $_POST['NEW_ZOOM_FACTOR'], $_POST['icon']));
			}

			// autoclose window when update successful unless debug on
			if (!WT_DEBUG) {
				$controller->addInlineJavaScript('closePopupAndReloadParent();');
			}
			echo "<div class=\"center\"><button onclick=\"closePopupAndReloadParent();return false;\">", WT_I18N::translate('close'), "</button></div>";
			exit;
		}

		if ($action=='updaterecord' && Auth::isAdmin()) {
			$statement=
				WT_DB::prepare("UPDATE `##placelocation` SET pl_place=?, pl_lati=?, pl_long=?, pl_zoom=?, pl_icon=? WHERE pl_id=?");

			if (($_POST['LONG_CONTROL'] == '') || ($_POST['NEW_PLACE_LONG'] == '') || ($_POST['NEW_PLACE_LATI'] == '')) {
				$statement->execute(array($_POST['NEW_PLACE_NAME'], null, null, $_POST['NEW_ZOOM_FACTOR'], $_POST['icon'], $placeid));
			} else {
				$statement->execute(array($_POST['NEW_PLACE_NAME'], $_POST['LATI_CONTROL'][3].$_POST['NEW_PLACE_LATI'], $_POST['LONG_CONTROL'][3].$_POST['NEW_PLACE_LONG'], $_POST['NEW_ZOOM_FACTOR'], $_POST['icon'], $placeid));
			}

			// autoclose window when update successful unless debug on
			if (!WT_DEBUG) {
				$controller->addInlineJavaScript('closePopupAndReloadParent();');
			}
			echo "<div class=\"center\"><button onclick=\"closePopupAndReloadParent();return false;\">", WT_I18N::translate('close'), "</button></div>";
			exit;
		}

		// Update placelocation STREETVIEW fields
		// TODO: This ought to be a POST request, rather than a GET request
		if ($action == 'update_sv_params' && Auth::isAdmin()) {
			WT_DB::prepare(
				"UPDATE `##placelocation` SET sv_lati=?, sv_long=?, sv_bearing=?, sv_elevation=?, sv_zoom=? WHERE pl_id=?"
			)->execute(array(
				WT_Filter::get('svlati'),
				WT_Filter::get('svlong'),
				WT_Filter::get('svbear'),
				WT_Filter::get('svelev'),
				WT_Filter::get('svzoom'),
				$placeid
			));
			$controller->addInlineJavaScript('window.close();');
			exit;
		}

		if ($action=="update") {
			// --- find the place in the file
			$row=
				WT_DB::prepare("SELECT pl_place, pl_lati, pl_long, pl_icon, pl_parent_id, pl_level, pl_zoom FROM `##placelocation` WHERE pl_id=?")
				->execute(array($placeid))
				->fetchOneRow();
			$place_name = $row->pl_place;
			$place_icon = $row->pl_icon;
			$selected_country = explode("/", $place_icon);
			if (isset($selected_country[1]) && $selected_country[1]!="flags")
				$selected_country = $selected_country[1];
			else
				$selected_country = "Countries";
			$parent_id = $row->pl_parent_id;
			$level = $row->pl_level;
			$zoomfactor = $row->pl_zoom;
			$parent_lati = "0.0";
			$parent_long = "0.0";
			if ($row->pl_lati!==null && $row->pl_long!==null) {
				$place_lati = (float)(str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati));
				$place_long = (float)(str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long));
			} else {
				$place_lati = null;
				$place_long = null;
				$zoomfactor = 1;
			}

			do {
				$row=
					WT_DB::prepare("SELECT pl_lati, pl_long, pl_parent_id, pl_zoom FROM `##placelocation` WHERE pl_id=?")
					->execute(array($parent_id))
					->fetchOneRow();
				if (!$row) {
					break;
				}
				if ($row->pl_lati!==null && $row->pl_long!==null) {
					$parent_lati = (float)(str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati));
					$parent_long = (float)(str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long));
					if ($zoomfactor == 1) {
						$zoomfactor = $row->pl_zoom;
					}
				}
				$parent_id = $row->pl_parent_id;
			}
			while ($row->pl_parent_id!=0 && $row->pl_lati===null && $row->pl_long===null);

			echo '<b>', WT_Filter::escapeHtml(str_replace('Unknown', WT_I18N::translate('unknown'), implode(WT_I18N::$list_separator, array_reverse($where_am_i, true)))), '</b><br>';
		}

		if ($action=='add') {
			// --- find the parent place in the file
			if ($placeid != 0) {
				if (!isset($place_name)) $place_name  = '';
				$place_lati = null;
				$place_long = null;
				$zoomfactor = 1;
				$parent_lati = '0.0';
				$parent_long = '0.0';
				$place_icon = '';
				$parent_id=$placeid;
				do {
					$row=
						WT_DB::prepare("SELECT pl_lati, pl_long, pl_parent_id, pl_zoom, pl_level FROM `##placelocation` WHERE pl_id=?")
						->execute(array($parent_id))
						->fetchOneRow();
					if ($row->pl_lati!==null && $row->pl_long!==null) {
						$parent_lati=str_replace(array('N', 'S', ','), array('', '-', '.') , $row->pl_lati);
						$parent_long=str_replace(array('E', 'W', ','), array('', '-', '.') , $row->pl_long);
						$zoomfactor=$row->pl_zoom;
						if ($zoomfactor>$GM_MAX_ZOOM) {
							$zoomfactor=$GM_MAX_ZOOM;
						}
						$level=$row->pl_level+1;
					}
					$parent_id = $row->pl_parent_id;
				} while ($row->pl_parent_id!=0 && $row->pl_lati===null && $row->pl_long===null);
			}
			else {
				if (!isset($place_name)) $place_name  = '';
				$place_lati  = null;
				$place_long  = null;
				$parent_lati = "0.0";
				$parent_long = "0.0";
				$place_icon  = '';
				$parent_id   = 0;
				$level = 0;
				$zoomfactor  = $this->getSetting('GM_MIN_ZOOM');
			}
			$selected_country = 'Countries';

			if (!isset($place_name) || $place_name=="") echo '<b>', WT_I18N::translate('unknown');
			else echo '<b>', $place_name;
			if (count($where_am_i)>0)
				echo ', ', WT_Filter::escapeHtml(str_replace('Unknown', WT_I18N::translate('unknown'), implode(WT_I18N::$list_separator, array_reverse($where_am_i, true)))), '</b><br>';
			echo '</b><br>';
		}

		?>

			<script src="<?php echo $this->googleMapsScript(); ?>"></script>
			<script>
			var map;
			var marker;
			var zoom;
			var pl_name = '<?php echo WT_Filter::escapeJs($place_name); ?>';
			if (pl_name) {
				var pl_lati = '<?php echo $place_lati; ?>';
				var pl_long = '<?php echo $place_long; ?>';
			} else {
				var pl_lati = '<?php echo $parent_lati; ?>';
				var pl_long = '<?php echo $parent_long; ?>';
			}
			var pl_zoom = <?php echo $zoomfactor; ?>;
			var latlng = new google.maps.LatLng(pl_lati, pl_long);
			var polygon1;
			var geocoder;
			var mapType;

			var infowindow = new google.maps.InfoWindow({
				//
			});

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

			function updateMap(event) {
				var point;
				var zoom;
				var latitude;
				var longitude;
				var i;

				zoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);

				prec = 20;
				for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
					if (document.editplaces.NEW_PRECISION[i].checked) {
						prec = document.editplaces.NEW_PRECISION[i].value;
					}
				}
				if ((document.editplaces.NEW_PLACE_LATI.value == '') ||
					(document.editplaces.NEW_PLACE_LONG.value == '')) {
					latitude = parseFloat(document.editplaces.parent_lati.value).toFixed(prec);
					longitude = parseFloat(document.editplaces.parent_long.value).toFixed(prec);
					point = new google.maps.LatLng(latitude, longitude);
				} else {
					latitude = parseFloat(document.editplaces.NEW_PLACE_LATI.value).toFixed(prec);
					longitude = parseFloat(document.editplaces.NEW_PLACE_LONG.value).toFixed(prec);
					document.editplaces.NEW_PLACE_LATI.value = latitude;
					document.editplaces.NEW_PLACE_LONG.value = longitude;

					if (event == 'flag_drag') {
						if (longitude < 0.0 ) {
							longitude = longitude * -1;
							document.editplaces.NEW_PLACE_LONG.value = longitude;
							document.editplaces.LONG_CONTROL.value = 'PL_W';
						} else {
							document.editplaces.NEW_PLACE_LONG.value = longitude;
							document.editplaces.LONG_CONTROL.value = 'PL_E';
						}
						if (latitude < 0.0 ) {
							latitude = latitude * -1;
							document.editplaces.NEW_PLACE_LATI.value = latitude;
							document.editplaces.LATI_CONTROL.value = 'PL_S';
						} else {
							document.editplaces.NEW_PLACE_LATI.value = latitude;
							document.editplaces.LATI_CONTROL.value = 'PL_N';
						}

						if (document.editplaces.LATI_CONTROL.value == 'PL_S') {
							latitude = latitude * -1;
						}
						if (document.editplaces.LONG_CONTROL.value == 'PL_W') {
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
						if (document.editplaces.LATI_CONTROL.value == 'PL_S') {
							latitude = latitude * -1;
						}
						if (document.editplaces.LONG_CONTROL.value == 'PL_W') {
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
				var polygon1;
				var num_arrays = "";
				if (pl_name == 'Scotland') {
					var returnGeom1 = '-2.02166,55.80611|-2.07972,55.86722|-2.13028,55.88583|-2.26028,55.91861|-2.37528,55.95694|-2.65722,56.05972|-2.82028,56.05694|-2.86618,56.02840|-2.89555,55.98861|-2.93500,55.96944|-3.01805,55.94944|-3.06750,55.94444|-3.25472,55.97166|-3.45472,55.99194|-3.66416,56.00652|-3.73722,56.05555|-3.57139,56.05360|-3.44111,56.01916|-3.39584,56.01083|-3.34403,56.02333|-3.13903,56.11084|-2.97611,56.19472|-2.91666,56.20499|-2.84695,56.18638|-2.78805,56.18749|-2.67937,56.21465|-2.58403,56.28264|-2.67208,56.32277|-2.76861,56.33180|-2.81528,56.37360|-2.81208,56.43958|-2.91653,56.45014|-2.99555,56.41416|-3.19042,56.35958|-3.27805,56.35750|-3.04055,56.45472|-2.95861,56.45611|-2.72084,56.48888|-2.64084,56.52250|-2.53126,56.57611|-2.48861,56.61416|-2.47805,56.71527|-2.39000,56.77166|-2.31986,56.79638|-2.21972,56.86777|-2.19708,56.94388|-2.16695,57.00055|-2.09334,57.07027|-2.05416,57.21861|-1.95889,57.33250|-1.85584,57.39889|-1.77334,57.45805|-1.78139,57.50555|-1.82195,57.57861|-1.86000,57.62138|-1.92972,57.67777|-2.02222,57.69388|-2.07555,57.69944|-2.14028,57.69056|-2.18611,57.66861|-2.39626,57.66638|-2.51000,57.67166|-2.78639,57.70222|-2.89806,57.70694|-2.96750,57.68027|-3.03847,57.66249|-3.12334,57.67166|-3.22334,57.69166|-3.28625,57.72499|-3.33972,57.72333|-3.48805,57.70945|-3.52222,57.66333|-3.59542,57.63666|-3.64063,57.63881|-3.75414,57.62504|-4.03986,57.55569|-4.19666,57.48584|-4.22889,57.51554|-4.17945,57.56249|-4.11139,57.59833|-4.08078,57.66533|-4.19139,57.67139|-4.25945,57.65527|-4.34361,57.60777|-4.41639,57.60166|-4.29666,57.67444|-4.08528,57.72611|-4.01908,57.70226|-3.96861,57.70250|-3.86556,57.76861|-3.81945,57.80458|-3.80681,57.85819|-3.85055,57.82000|-3.92639,57.80749|-4.04322,57.81438|-4.14973,57.82527|-4.29750,57.84638|-4.36250,57.89777|-4.24306,57.87028|-4.10666,57.85195|-4.01500,57.86777|-3.99166,57.90611|-3.99695,57.95056|-3.84500,58.02000|-3.56611,58.13916|-3.51319,58.16374|-3.45916,58.20305|-3.42028,58.24361|-3.33750,58.27694|-3.20555,58.30625|-3.10972,58.38166|-3.05792,58.45083|-3.02264,58.64653|-3.17639,58.64944|-3.35389,58.66055|-3.36931,58.59555|-3.57611,58.62194|-3.66028,58.61972|-3.71166,58.60374|-3.78264,58.56750|-3.84834,58.56000|-4.08056,58.55527|-4.27722,58.53361|-4.43653,58.54902|-4.50666,58.56777|-4.56055,58.57584|-4.59910,58.53027|-4.66805,58.48833|-4.76146,58.44604|-4.70195,58.50999|-4.70166,58.55861|-4.77014,58.60264|-5.00153,58.62416|-5.10945,58.50833|-5.16472,58.32527|-5.12639,58.28750|-5.07166,58.26472|-5.20361,58.25083|-5.39764,58.25055|-5.27389,58.11722|-5.31514,58.06416|-5.38416,58.08361|-5.45285,58.07416|-5.39805,58.03111|-5.26278,57.97111|-5.19334,57.95069|-5.12750,57.86944|-5.21750,57.90084|-5.33861,57.92083|-5.42876,57.90104|-5.45750,57.85889|-5.64445,57.89972|-5.62555,57.85222|-5.58153,57.81945|-5.60674,57.76618|-5.66305,57.78889|-5.71695,57.86944|-5.76695,57.86472|-5.81708,57.81944|-5.81084,57.63958|-5.69555,57.55944|-5.64361,57.55222|-5.53084,57.52833|-5.65305,57.50875|-5.75000,57.54834|-5.81569,57.57923|-5.85042,57.54972|-5.86695,57.46777|-5.81806,57.36250|-5.75111,57.34333|-5.50334,57.40111|-5.45126,57.41805|-5.49250,57.37083|-5.59884,57.33049|-5.57116,57.28411|-5.51266,57.27745|-5.40514,57.23097|-5.44972,57.22138|-5.49472,57.23888|-5.56066,57.25477|-5.64611,57.23499|-5.64751,57.16161|-5.55028,57.11639|-5.48166,57.11222|-5.40305,57.11062|-5.55945,57.09250|-5.65111,57.11611|-5.72472,57.11306|-5.77361,57.04556|-5.63139,56.98499|-5.56916,56.98972|-5.52403,56.99735|-5.57916,56.98000|-5.64611,56.97222|-5.73374,57.00909|-5.82584,57.00346|-5.91958,56.88708|-5.86528,56.87944|-5.74278,56.89374|-5.66292,56.86924|-5.73306,56.83916|-5.78584,56.83955|-5.85590,56.81430|-5.80208,56.79180|-5.84958,56.74444|-5.90500,56.75666|-5.96694,56.78027|-6.14000,56.75777|-6.19208,56.74888|-6.23452,56.71673|-6.19139,56.67972|-5.91916,56.67388|-5.82622,56.69156|-5.73945,56.71166|-5.55240,56.68886|-5.64861,56.68027|-5.69916,56.68278|-5.88261,56.65666|-5.97472,56.65138|-5.99584,56.61138|-5.93056,56.56972|-5.88416,56.55333|-5.79056,56.53805|-5.67695,56.49389|-5.56389,56.54056|-5.36334,56.66195|-5.23416,56.74333|-5.13236,56.79403|-5.31473,56.65666|-5.37405,56.55925|-5.31826,56.55633|-5.25080,56.55753|-5.37718,56.52112|-5.39866,56.47866|-5.19111,56.46194|-5.11556,56.51277|-5.07014,56.56069|-5.13555,56.48499|-5.22084,56.43583|-5.32764,56.43574|-5.42439,56.43091|-5.52611,56.37360|-5.57139,56.32833|-5.59653,56.25695|-5.57389,56.16000|-5.52000,56.16485|-5.56334,56.11333|-5.60139,56.07638|-5.64222,56.04305|-5.66039,55.98263|-5.62555,56.02055|-5.58014,56.01319|-5.63361,55.96611|-5.67697,55.88844|-5.64750,55.78139|-5.60986,55.75930|-5.66916,55.66166|-5.70166,55.58861|-5.71805,55.51500|-5.75916,55.41750|-5.79528,55.36027|-5.78166,55.29902|-5.73778,55.29222|-5.56694,55.31666|-5.51528,55.36347|-5.55520,55.41440|-5.48639,55.64306|-5.44597,55.70680|-5.38000,55.75027|-5.41889,55.90666|-5.39924,55.99972|-5.33895,56.03456|-5.30594,56.06922|-5.23889,56.11889|-5.03222,56.23250|-4.92229,56.27111|-4.97416,56.23333|-5.07222,56.18695|-5.20069,56.11861|-5.30906,56.00570|-5.34000,55.90201|-5.29250,55.84750|-5.20805,55.84444|-5.22458,55.90175|-5.17334,55.92916|-5.11000,55.90306|-5.01222,55.86694|-4.96195,55.88000|-4.89824,55.98145|-4.84623,56.08632|-4.86636,56.03178|-4.85461,55.98648|-4.77659,55.97977|-4.62723,55.94555|-4.52305,55.91861|-4.70972,55.93403|-4.75166,55.94611|-4.82406,55.94950|-4.87826,55.93653|-4.91639,55.70083|-4.87584,55.68194|-4.81361,55.64555|-4.68722,55.59750|-4.61361,55.49069|-4.63958,55.44264|-4.68250,55.43388|-4.74847,55.41055|-4.83715,55.31882|-4.84778,55.26944|-4.86542,55.22340|-4.93500,55.17860|-5.01250,55.13347|-5.05361,55.04902|-5.17834,54.98888|-5.18563,54.93622|-5.17000,54.89111|-5.11666,54.83180|-5.00500,54.76333|-4.96229,54.68125|-4.92250,54.64055|-4.85723,54.62958|-4.96076,54.79687|-4.92431,54.83708|-4.85222,54.86861|-4.80125,54.85556|-4.74055,54.82166|-4.68084,54.79972|-4.59861,54.78027|-4.55792,54.73903|-4.49639,54.69888|-4.37584,54.67666|-4.34569,54.70916|-4.35973,54.77111|-4.41111,54.82583|-4.42445,54.88152|-4.38479,54.90555|-4.35056,54.85903|-4.09555,54.76777|-3.95361,54.76749|-3.86972,54.80527|-3.81222,54.84888|-3.69250,54.88110|-3.61584,54.87527|-3.57111,54.99083|-3.44528,54.98638|-3.36056,54.97138|-3.14695,54.96500|-3.05103,54.97986|-3.01500,55.05222|-2.96278,55.03889|-2.69945,55.17722|-2.63055,55.25500|-2.46305,55.36111|-2.21236,55.42777|-2.18278,55.45985|-2.21528,55.50583|-2.27416,55.57527|-2.27916,55.64472|-2.22000,55.66499|-2.08361,55.78054|-2.02166,55.80611';
					num_arrays = 1;
				} else if (pl_name == 'England') {
					// England
					var returnGeom1 = '-4.74361,50.66750|-4.78361,50.59361|-4.91584,50.57722|-5.01750,50.54264|-5.02569,50.47271|-5.04729,50.42750|-5.15208,50.34374|-5.26805,50.27389|-5.43194,50.19326|-5.49584,50.21695|-5.54639,50.20527|-5.71000,50.12916|-5.71681,50.06083|-5.66174,50.03631|-5.58278,50.04777|-5.54166,50.07055|-5.53416,50.11569|-5.47055,50.12499|-5.33361,50.09138|-5.27666,50.05972|-5.25674,50.00514|-5.19306,49.95527|-5.16070,50.00319|-5.06555,50.03750|-5.07090,50.08166|-5.04806,50.17111|-4.95278,50.19333|-4.85750,50.23166|-4.76250,50.31138|-4.67861,50.32583|-4.54334,50.32222|-4.48278,50.32583|-4.42972,50.35139|-4.38000,50.36388|-4.16555,50.37028|-4.11139,50.33027|-4.05708,50.29791|-3.94389,50.31346|-3.87764,50.28139|-3.83653,50.22972|-3.78944,50.21222|-3.70666,50.20972|-3.65195,50.23111|-3.55139,50.43833|-3.49416,50.54639|-3.46181,50.58792|-3.41139,50.61610|-3.24416,50.67444|-3.17347,50.68833|-3.09445,50.69222|-2.97806,50.70638|-2.92750,50.73125|-2.88278,50.73111|-2.82305,50.72027|-2.77139,50.70861|-2.66195,50.67334|-2.56305,50.63222|-2.45861,50.57500|-2.44666,50.62639|-2.39097,50.64166|-2.19722,50.62611|-2.12195,50.60722|-2.05445,50.58569|-1.96437,50.59674|-1.95441,50.66536|-2.06681,50.71430|-1.93416,50.71277|-1.81639,50.72306|-1.68445,50.73888|-1.59278,50.72416|-1.33139,50.79138|-1.11695,50.80694|-1.15889,50.84083|-1.09445,50.84584|-0.92842,50.83966|-0.86584,50.79965|-0.90826,50.77396|-0.78187,50.72722|-0.74611,50.76583|-0.67528,50.78111|-0.57722,50.79527|-0.25500,50.82638|-0.19084,50.82583|-0.13805,50.81833|0.05695,50.78083|0.12334,50.75944|0.22778,50.73944|0.28695,50.76500|0.37195,50.81638|0.43084,50.83111|0.56722,50.84777|0.67889,50.87681|0.71639,50.90500|0.79334,50.93610|0.85666,50.92556|0.97125,50.98111|0.99778,51.01903|1.04555,51.04944|1.10028,51.07361|1.26250,51.10166|1.36889,51.13583|1.41111,51.20111|1.42750,51.33111|1.38556,51.38777|1.19195,51.37861|1.05278,51.36722|0.99916,51.34777|0.90806,51.34069|0.70416,51.37749|0.61972,51.38304|0.55945,51.40596|0.64236,51.44042|0.69750,51.47084|0.59195,51.48777|0.53611,51.48806|0.48916,51.48445|0.45215,51.45562|0.38894,51.44822|0.46500,51.50306|0.65195,51.53680|0.76695,51.52138|0.82084,51.53556|0.87528,51.56110|0.95250,51.60923|0.94695,51.72556|0.90257,51.73465|0.86306,51.71166|0.76140,51.69164|0.70111,51.71847|0.86211,51.77361|0.93236,51.80583|0.98278,51.82527|1.03569,51.77416|1.08834,51.77056|1.13222,51.77694|1.18139,51.78972|1.22361,51.80888|1.26611,51.83916|1.28097,51.88096|1.20834,51.95083|1.16347,52.02361|1.27750,51.98555|1.33125,51.92875|1.39028,51.96999|1.58736,52.08388|1.63000,52.19527|1.68576,52.32630|1.73028,52.41138|1.74945,52.45583|1.74590,52.62021|1.70250,52.71583|1.64528,52.77111|1.50361,52.83749|1.43222,52.87472|1.35250,52.90972|1.28222,52.92750|1.18389,52.93889|0.99472,52.95111|0.94222,52.95083|0.88472,52.96638|0.66722,52.97611|0.54778,52.96618|0.49139,52.93430|0.44431,52.86569|0.42903,52.82403|0.36334,52.78027|0.21778,52.80694|0.16125,52.86250|0.05778,52.88916|0.00211,52.87985|0.03222,52.91722|0.20389,53.02805|0.27666,53.06694|0.33916,53.09236|0.35389,53.18722|0.33958,53.23472|0.23555,53.39944|0.14347,53.47527|0.08528,53.48638|0.02694,53.50972|-0.10084,53.57306|-0.20722,53.63083|-0.26445,53.69083|-0.30166,53.71319|-0.39022,53.70794|-0.51972,53.68527|-0.71653,53.69638|-0.65445,53.72527|-0.60584,53.72972|-0.54916,53.70611|-0.42261,53.71755|-0.35728,53.73056|-0.29389,53.73666|-0.23139,53.72166|-0.10584,53.63166|-0.03472,53.62555|0.04416,53.63916|0.08916,53.62666|0.14945,53.58847|0.12639,53.64527|0.06264,53.70389|-0.12750,53.86388|-0.16916,53.91847|-0.21222,54.00833|-0.20569,54.05153|-0.16111,54.08806|-0.11694,54.13222|-0.20053,54.15171|-0.26250,54.17444|-0.39334,54.27277|-0.42166,54.33222|-0.45750,54.37694|-0.51847,54.44749|-0.56472,54.48000|-0.87584,54.57027|-1.06139,54.61722|-1.16528,54.64972|-1.30445,54.77138|-1.34556,54.87138|-1.41278,54.99944|-1.48292,55.08625|-1.51500,55.14972|-1.56584,55.28722|-1.58097,55.48361|-1.63597,55.58194|-1.69000,55.60556|-1.74695,55.62499|-1.81764,55.63306|-1.97681,55.75416|-2.02166,55.80611|-2.08361,55.78054|-2.22000,55.66499|-2.27916,55.64472|-2.27416,55.57527|-2.21528,55.50583|-2.18278,55.45985|-2.21236,55.42777|-2.46305,55.36111|-2.63055,55.25500|-2.69945,55.17722|-2.96278,55.03889|-3.01500,55.05222|-3.05103,54.97986|-3.13292,54.93139|-3.20861,54.94944|-3.28931,54.93792|-3.39166,54.87639|-3.42916,54.81555|-3.56916,54.64249|-3.61306,54.48861|-3.49305,54.40333|-3.43389,54.34806|-3.41056,54.28014|-3.38055,54.24444|-3.21472,54.09555|-3.15222,54.08194|-2.93097,54.15333|-2.81361,54.22277|-2.81750,54.14277|-2.83361,54.08500|-2.93250,53.95055|-3.05264,53.90764|-3.03708,53.74944|-2.99278,53.73277|-2.89979,53.72499|-2.97729,53.69382|-3.07306,53.59805|-3.10563,53.55993|-3.00678,53.41738|-2.95389,53.36027|-2.85736,53.32083|-2.70493,53.35062|-2.77639,53.29250|-2.89972,53.28916|-2.94250,53.31056|-3.02889,53.38191|-3.07248,53.40936|-3.16695,53.35708|-3.12611,53.32500|-3.08860,53.26001|-3.02000,53.24722|-2.95528,53.21555|-2.91069,53.17014|-2.89389,53.10416|-2.85695,53.03249|-2.77792,52.98514|-2.73109,52.96873|-2.71945,52.91902|-2.79278,52.90207|-2.85069,52.93875|-2.99389,52.95361|-3.08639,52.91611|-3.13014,52.88486|-3.13708,52.79312|-3.06806,52.77027|-3.01111,52.71166|-3.06666,52.63527|-3.11750,52.58666|-3.07089,52.55702|-3.00792,52.56902|-2.98028,52.53083|-3.02736,52.49792|-3.11916,52.49194|-3.19514,52.46722|-3.19611,52.41027|-3.02195,52.34027|-2.95486,52.33117|-2.99750,52.28139|-3.05125,52.23347|-3.07555,52.14804|-3.12222,52.11805|-3.11250,52.06945|-3.08500,52.01930|-3.04528,51.97639|-2.98889,51.92555|-2.91757,51.91569|-2.86639,51.92889|-2.77861,51.88583|-2.65944,51.81806|-2.68334,51.76957|-2.68666,51.71889|-2.66500,51.61500|-2.62916,51.64416|-2.57889,51.67777|-2.46056,51.74666|-2.40389,51.74041|-2.47166,51.72445|-2.55305,51.65722|-2.65334,51.56389|-2.77055,51.48916|-2.85278,51.44472|-2.96000,51.37499|-3.00695,51.30722|-3.01278,51.25632|-3.02834,51.20611|-3.30139,51.18111|-3.39361,51.18138|-3.43729,51.20638|-3.50722,51.22333|-3.57014,51.23027|-3.63222,51.21805|-3.70028,51.23000|-3.79250,51.23916|-3.88389,51.22416|-3.98472,51.21695|-4.11666,51.21222|-4.22805,51.18777|-4.22028,51.11054|-4.23702,51.04659|-4.30361,51.00416|-4.37639,50.99110|-4.42736,51.00958|-4.47445,51.01416|-4.52132,51.01424|-4.54334,50.92694|-4.56139,50.77625|-4.65139,50.71527|-4.74361,50.66750'; //|-3.08860,53.26001|-3.33639,53.34722|-3.38806,53.34361|-3.60986,53.27944|-3.73014,53.28944|-3.85445,53.28444|-4.01861,53.23750|-4.06639,53.22639|-4.15334,53.22556|-4.19639,53.20611|-4.33028,53.11222|-4.36097,53.02888|-4.55278,52.92889|-4.61889,52.90916|-4.72195,52.83611|-4.72778,52.78139|-4.53945,52.79306|-4.47722,52.85500|-4.41416,52.88472|-4.31292,52.90499|-4.23334,52.91499|-4.13569,52.87888|-4.13056,52.77777|-4.05334,52.71666|-4.10639,52.65084|-4.12597,52.60375|-4.08056,52.55333|-4.05972,52.48584|-4.09666,52.38583|-4.14305,52.32027|-4.19361,52.27638|-4.23166,52.24888|-4.52722,52.13083|-4.66945,52.13027|-4.73695,52.10361|-4.76778,52.06444|-4.84445,52.01388|-5.09945,51.96056|-5.23916,51.91638|-5.25889,51.87056|-5.18500,51.86958|-5.11528,51.83333|-5.10257,51.77895|-5.16111,51.76222|-5.24694,51.73027|-5.19111,51.70888|-5.00739,51.70349|-4.90875,51.71249|-4.86111,51.71334|-4.97061,51.67577|-5.02128,51.66861|-5.05139,51.62028|-5.00528,51.60638|-4.94139,51.59416|-4.89028,51.62694|-4.83569,51.64534|-4.79063,51.63340|-4.69028,51.66666|-4.64584,51.72666|-4.57445,51.73416|-4.43611,51.73722|-4.26222,51.67694|-4.19750,51.67916|-4.06614,51.66804|-4.11639,51.63416|-4.17750,51.62235|-4.25055,51.62861|-4.29208,51.60743|-4.27778,51.55666|-4.20486,51.53527|-3.94972,51.61278|-3.83792,51.61999|-3.78166,51.56750|-3.75160,51.52931|-3.67194,51.47388|-3.54250,51.39777|-3.40334,51.37972|-3.27097,51.38014|-3.16458,51.40909|-3.15166,51.45305|-3.11875,51.48750|-3.02111,51.52527|-2.95472,51.53972|-2.89278,51.53861|-2.84778,51.54500|-2.71472,51.58083|-2.66500,51.61500|-2.68666,51.71889|-2.68334,51.76957|-2.65944,51.81806|-2.77861,51.88583|-2.86639,51.92889|-2.91757,51.91569|-2.98889,51.92555|-3.04528,51.97639|-3.08500,52.01930|-3.11250,52.06945|-3.12222,52.11805|-3.07555,52.14804|-3.05125,52.23347|-2.99750,52.28139|-2.95486,52.33117|-3.02195,52.34027|-3.19611,52.41027|-3.19514,52.46722|-3.11916,52.49194|-3.02736,52.49792|-2.98028,52.53083|-3.00792,52.56902|-3.07089,52.55702|-3.11750,52.58666|-3.06666,52.63527|-3.01111,52.71166|-3.06806,52.77027|-3.13708,52.79312|-3.13014,52.88486|-3.08639,52.91611|-2.99389,52.95361|-2.85069,52.93875|-2.79278,52.90207|-2.71945,52.91902|-2.73109,52.96873|-2.77792,52.98514|-2.85695,53.03249|-2.89389,53.10416|-2.91069,53.17014|-2.95528,53.21555|-3.02000,53.24722|-3.08860,53.26001';
					// Wales Test
			//		var returnGeom2 = '-3.08860,53.26001|-3.33639,53.34722|-3.38806,53.34361|-3.60986,53.27944|-3.73014,53.28944|-3.85445,53.28444|-4.01861,53.23750|-4.06639,53.22639|-4.15334,53.22556|-4.19639,53.20611|-4.33028,53.11222|-4.36097,53.02888|-4.55278,52.92889|-4.61889,52.90916|-4.72195,52.83611|-4.72778,52.78139|-4.53945,52.79306|-4.47722,52.85500|-4.41416,52.88472|-4.31292,52.90499|-4.23334,52.91499|-4.13569,52.87888|-4.13056,52.77777|-4.05334,52.71666|-4.10639,52.65084|-4.12597,52.60375|-4.08056,52.55333|-4.05972,52.48584|-4.09666,52.38583|-4.14305,52.32027|-4.19361,52.27638|-4.23166,52.24888|-4.52722,52.13083|-4.66945,52.13027|-4.73695,52.10361|-4.76778,52.06444|-4.84445,52.01388|-5.09945,51.96056|-5.23916,51.91638|-5.25889,51.87056|-5.18500,51.86958|-5.11528,51.83333|-5.10257,51.77895|-5.16111,51.76222|-5.24694,51.73027|-5.19111,51.70888|-5.00739,51.70349|-4.90875,51.71249|-4.86111,51.71334|-4.97061,51.67577|-5.02128,51.66861|-5.05139,51.62028|-5.00528,51.60638|-4.94139,51.59416|-4.89028,51.62694|-4.83569,51.64534|-4.79063,51.63340|-4.69028,51.66666|-4.64584,51.72666|-4.57445,51.73416|-4.43611,51.73722|-4.26222,51.67694|-4.19750,51.67916|-4.06614,51.66804|-4.11639,51.63416|-4.17750,51.62235|-4.25055,51.62861|-4.29208,51.60743|-4.27778,51.55666|-4.20486,51.53527|-3.94972,51.61278|-3.83792,51.61999|-3.78166,51.56750|-3.75160,51.52931|-3.67194,51.47388|-3.54250,51.39777|-3.40334,51.37972|-3.27097,51.38014|-3.16458,51.40909|-3.15166,51.45305|-3.11875,51.48750|-3.02111,51.52527|-2.95472,51.53972|-2.89278,51.53861|-2.84778,51.54500|-2.71472,51.58083|-2.66500,51.61500|-2.68666,51.71889|-2.68334,51.76957|-2.65944,51.81806|-2.77861,51.88583|-2.86639,51.92889|-2.91757,51.91569|-2.98889,51.92555|-3.04528,51.97639|-3.08500,52.01930|-3.11250,52.06945|-3.12222,52.11805|-3.07555,52.14804|-3.05125,52.23347|-2.99750,52.28139|-2.95486,52.33117|-3.02195,52.34027|-3.19611,52.41027|-3.19514,52.46722|-3.11916,52.49194|-3.02736,52.49792|-2.98028,52.53083|-3.00792,52.56902|-3.07089,52.55702|-3.11750,52.58666|-3.06666,52.63527|-3.01111,52.71166|-3.06806,52.77027|-3.13708,52.79312|-3.13014,52.88486|-3.08639,52.91611|-2.99389,52.95361|-2.85069,52.93875|-2.79278,52.90207|-2.71945,52.91902|-2.73109,52.96873|-2.77792,52.98514|-2.85695,53.03249|-2.89389,53.10416|-2.91069,53.17014|-2.95528,53.21555|-3.02000,53.24722|-3.08860,53.26001';
					num_arrays = 2;
				} else if (pl_name == 'Wales') {
					var returnGeom1 = '-3.08860,53.26001|-3.33639,53.34722|-3.38806,53.34361|-3.60986,53.27944|-3.73014,53.28944|-3.85445,53.28444|-4.01861,53.23750|-4.06639,53.22639|-4.15334,53.22556|-4.19639,53.20611|-4.33028,53.11222|-4.36097,53.02888|-4.55278,52.92889|-4.61889,52.90916|-4.72195,52.83611|-4.72778,52.78139|-4.53945,52.79306|-4.47722,52.85500|-4.41416,52.88472|-4.31292,52.90499|-4.23334,52.91499|-4.13569,52.87888|-4.13056,52.77777|-4.05334,52.71666|-4.10639,52.65084|-4.12597,52.60375|-4.08056,52.55333|-4.05972,52.48584|-4.09666,52.38583|-4.14305,52.32027|-4.19361,52.27638|-4.23166,52.24888|-4.52722,52.13083|-4.66945,52.13027|-4.73695,52.10361|-4.76778,52.06444|-4.84445,52.01388|-5.09945,51.96056|-5.23916,51.91638|-5.25889,51.87056|-5.18500,51.86958|-5.11528,51.83333|-5.10257,51.77895|-5.16111,51.76222|-5.24694,51.73027|-5.19111,51.70888|-5.00739,51.70349|-4.90875,51.71249|-4.86111,51.71334|-4.97061,51.67577|-5.02128,51.66861|-5.05139,51.62028|-5.00528,51.60638|-4.94139,51.59416|-4.89028,51.62694|-4.83569,51.64534|-4.79063,51.63340|-4.69028,51.66666|-4.64584,51.72666|-4.57445,51.73416|-4.43611,51.73722|-4.26222,51.67694|-4.19750,51.67916|-4.06614,51.66804|-4.11639,51.63416|-4.17750,51.62235|-4.25055,51.62861|-4.29208,51.60743|-4.27778,51.55666|-4.20486,51.53527|-3.94972,51.61278|-3.83792,51.61999|-3.78166,51.56750|-3.75160,51.52931|-3.67194,51.47388|-3.54250,51.39777|-3.40334,51.37972|-3.27097,51.38014|-3.16458,51.40909|-3.15166,51.45305|-3.11875,51.48750|-3.02111,51.52527|-2.95472,51.53972|-2.89278,51.53861|-2.84778,51.54500|-2.71472,51.58083|-2.66500,51.61500|-2.68666,51.71889|-2.68334,51.76957|-2.65944,51.81806|-2.77861,51.88583|-2.86639,51.92889|-2.91757,51.91569|-2.98889,51.92555|-3.04528,51.97639|-3.08500,52.01930|-3.11250,52.06945|-3.12222,52.11805|-3.07555,52.14804|-3.05125,52.23347|-2.99750,52.28139|-2.95486,52.33117|-3.02195,52.34027|-3.19611,52.41027|-3.19514,52.46722|-3.11916,52.49194|-3.02736,52.49792|-2.98028,52.53083|-3.00792,52.56902|-3.07089,52.55702|-3.11750,52.58666|-3.06666,52.63527|-3.01111,52.71166|-3.06806,52.77027|-3.13708,52.79312|-3.13014,52.88486|-3.08639,52.91611|-2.99389,52.95361|-2.85069,52.93875|-2.79278,52.90207|-2.71945,52.91902|-2.73109,52.96873|-2.77792,52.98514|-2.85695,53.03249|-2.89389,53.10416|-2.91069,53.17014|-2.95528,53.21555|-3.02000,53.24722|-3.08860,53.26001';
					num_arrays = 1;
				} else if (pl_name == 'Ireland') {
					var returnGeom1 = '-8.17166,54.46388|-8.06555,54.37277|-7.94139,54.29944|-7.87576,54.28499|-7.86834,54.22764|-7.81805,54.19916|-7.69972,54.20250|-7.55945,54.12694|-7.31334,54.11250|-7.14584,54.22527|-7.17555,54.28916|-7.16084,54.33666|-7.05834,54.41000|-6.97445,54.40166|-6.92695,54.37916|-6.87305,54.34208|-6.85111,54.28972|-6.73473,54.18361|-6.65556,54.06527|-6.60584,54.04444|-6.44750,54.05833|-6.33889,54.11555|-6.26697,54.09983|-6.17403,54.07222|-6.10834,54.03638|-6.04389,54.03139|-5.96834,54.06389|-5.88500,54.11639|-5.87347,54.20916|-5.82500,54.23958|-5.74611,54.24806|-5.65556,54.22701|-5.60834,54.24972|-5.55916,54.29084|-5.57334,54.37704|-5.64502,54.49267|-5.70472,54.53361|-5.68055,54.57306|-5.59972,54.54194|-5.55097,54.50083|-5.54216,54.44903|-5.54643,54.40527|-5.50672,54.36444|-5.46111,54.38555|-5.43132,54.48596|-5.47945,54.53638|-5.53521,54.65090|-5.57431,54.67722|-5.62916,54.67945|-5.73674,54.67383|-5.80305,54.66138|-5.88257,54.60652|-5.92445,54.63180|-5.86681,54.68972|-5.81903,54.70972|-5.74672,54.72452|-5.68775,54.76335|-5.70931,54.83166|-5.74694,54.85361|-5.79139,54.85139|-6.03611,55.05778|-6.04250,55.10277|-6.03444,55.15458|-6.10125,55.20945|-6.14584,55.22069|-6.25500,55.21194|-6.37639,55.23916|-6.51556,55.23305|-6.61334,55.20722|-6.73028,55.18027|-6.82472,55.16806|-6.88972,55.16777|-6.96695,55.15611|-6.99416,55.11027|-7.05139,55.04680|-7.09500,55.03694|-7.25251,55.07059|-7.32639,55.04527|-7.40639,54.95333|-7.45805,54.85777|-7.55334,54.76277|-7.73916,54.71054|-7.82576,54.73416|-7.92639,54.70054|-7.85236,54.63388|-7.77750,54.62694|-7.83361,54.55389|-7.95084,54.53222|-8.04695,54.50722|-8.17166,54.46388';
					num_arrays = 1;
				} else if (pl_name == 'NC') {
					var returnGeom1 = '-81.65876,36.60938|-81.70390,36.55513|-81.70639,36.50804|-81.74665,36.39777|-81.90723,36.30804|-82.03195,36.12694|-82.08416,36.10146|-82.12826,36.11020|-82.21500,36.15833|-82.36375,36.11347|-82.43472,36.06013|-82.46236,36.01708|-82.56006,35.96263|-82.60042,35.99638|-82.62308,36.06121|-82.73500,36.01833|-82.84612,35.94944|-82.90451,35.88819|-82.93555,35.83846|-83.16000,35.76236|-83.24222,35.71944|-83.49222,35.57111|-83.56847,35.55861|-83.64416,35.56471|-83.73499,35.56638|-83.88222,35.51791|-83.98361,35.44944|-84.03639,35.35444|-84.04964,35.29117|-84.09042,35.25986|-84.15084,35.25388|-84.20521,35.25722|-84.29284,35.22596|-84.32471,34.98701|-83.09778,35.00027|-82.77722,35.09138|-82.59639,35.14972|-82.37999,35.21500|-82.27362,35.20583|-81.41306,35.17416|-81.05915,35.15333|-80.92666,35.10695|-80.78751,34.95610|-80.79334,34.82555|-79.66777,34.80694|-79.11555,34.34527|-78.57222,33.88166|-78.51806,33.87999|-78.43721,33.89804|-78.23735,33.91986|-78.15389,33.91471|-78.06974,33.89500|-78.02597,33.88936|-77.97611,33.94276|-77.95299,33.99243|-77.94499,34.06499|-77.92728,34.11756|-77.92250,33.99194|-77.92264,33.93715|-77.88215,34.06166|-77.86222,34.15083|-77.83501,34.19194|-77.75724,34.28527|-77.68222,34.36555|-77.63667,34.39805|-77.57363,34.43694|-77.45527,34.50403|-77.38173,34.51646|-77.37905,34.56294|-77.38572,34.61260|-77.40944,34.68916|-77.38847,34.73304|-77.33097,34.63992|-77.35024,34.60099|-77.30958,34.55972|-77.09424,34.67742|-76.75994,34.76659|-76.68325,34.79749|-76.66097,34.75781|-76.62611,34.71014|-76.50063,34.73617|-76.48138,34.77638|-76.38305,34.86423|-76.34326,34.88194|-76.27181,34.96263|-76.35125,35.02221|-76.32354,34.97429|-76.45319,34.93524|-76.43395,34.98782|-76.45356,35.06676|-76.52917,35.00444|-76.63382,34.98242|-76.69722,34.94887|-76.75306,34.90526|-76.81636,34.93944|-76.89000,34.95388|-76.93180,34.96957|-76.96501,34.99777|-77.06816,35.14978|-76.97639,35.06806|-76.86722,35.00000|-76.80531,34.98559|-76.72708,35.00152|-76.60402,35.07416|-76.56555,35.11486|-76.57305,35.16013|-76.66489,35.16694|-76.56361,35.23361|-76.48750,35.22582|-76.46889,35.27166|-76.50298,35.30791|-76.83251,35.39222|-77.02305,35.48694|-77.04958,35.52694|-76.91292,35.46166|-76.65250,35.41499|-76.61611,35.45888|-76.63195,35.52249|-76.58820,35.55104|-76.51556,35.53194|-76.56711,35.48494|-76.52251,35.40416|-76.46195,35.37221|-76.13319,35.35986|-76.04111,35.42416|-76.00223,35.46610|-75.97958,35.51666|-75.89362,35.57555|-75.83834,35.56694|-75.78944,35.57138|-75.74076,35.61846|-75.72084,35.69263|-75.72084,35.81451|-75.74917,35.87791|-75.78333,35.91972|-75.85083,35.97527|-75.94333,35.91777|-75.98944,35.88054|-75.98854,35.79110|-75.99388,35.71027|-76.02875,35.65409|-76.10320,35.66041|-76.13563,35.69239|-76.04475,35.68436|-76.04167,35.74916|-76.05305,35.79361|-76.05305,35.87375|-76.02653,35.96222|-76.07751,35.99319|-76.17472,35.99596|-76.27917,35.91915|-76.37986,35.95763|-76.42014,35.97874|-76.55375,35.93971|-76.66222,35.93305|-76.72952,35.93984|-76.73392,36.04760|-76.75384,36.09477|-76.76028,36.14513|-76.74610,36.22818|-76.70458,36.24673|-76.72764,36.16736|-76.71021,36.11752|-76.69117,36.07165|-76.65979,36.03312|-76.49527,36.00958|-76.37138,36.07694|-76.37084,36.14999|-76.21417,36.09471|-76.07591,36.17910|-76.18361,36.26915|-76.19965,36.31739|-76.13986,36.28805|-76.04274,36.21974|-76.00465,36.18110|-75.95287,36.19241|-75.97604,36.31138|-75.93895,36.28381|-75.85271,36.11069|-75.79315,36.07385|-75.79639,36.11804|-75.88333,36.29554|-75.94665,36.37194|-75.98694,36.41166|-76.03473,36.49666|-76.02899,36.55000|-78.44234,36.54986|-78.56594,36.55799|-80.27556,36.55110|-81.15361,36.56499|-81.38722,36.57695|-81.65876,36.60938';
					num_arrays = 1;
				} else {
					// show nothing
				}

			/*
				// Set borders and fill parameters ------- Not finished yet. ---------------
				if (map.mapTypeId == 'roadmap') {
					var colorStroke 	= "#444444";
					var weightStroke	= 1.2;
					var opacityFill		= 0.1;
				} else if (map.mapTypeId == 'satellite') {
					var colorStroke 	= "#000000";
					var weightStroke	= 0.1;
					var opacityFill		= 0;
				} else if (map.mapTypeId == 'hybrid') {
					var colorStroke 	= "#ffffff";
					var weightStroke	= 1.2;
					var opacityFill		= 0;
				} else if (map.mapTypeId == 'terrain') {
					var colorStroke 	= "#ffffff";
					var weightStroke	= 1.2;
					var opacityFill		= 0;
				}
				// -------------------------------------------------------------------------
			*/

			    // If showing one country only (num_arrays == 1) ---------------------------
				// Calculate polygon
				if (num_arrays == 1 ) {
					var geomAry1 = new Array();
					geomAry1 = returnGeom1.split('|');
					var XY1 = new Array();
					var points1 = [];
					for (var i = 0; i < geomAry1.length; i++) {
						XY1 = geomAry1[i].split(',');
						points1.push( new google.maps.LatLng(parseFloat(XY1[1]),parseFloat(XY1[0]))) ;
					}
			    	// Construct the polygon
			    	polygon1 = new google.maps.Polygon({
			    	  paths: points1,
			    	  strokeColor: "#888888",
			    	  strokeOpacity: 0.8,
			    	  strokeWeight: 1,
			    	  fillColor: "#ff0000",
			    	  fillOpacity: 0.15
			    	});
			    	polygon1.setMap(map);
			    }

			    // If showing two countries at the same time (num_arrays == 2) --------------
				if (num_arrays == 2) {
					// Calculate polygon1
					var geomAry1 = new Array();
					geomAry1 = returnGeom1.split('|');
					var XY1 = new Array();
					var points1 = [];
					for (var i = 0; i < geomAry1.length; i++) {
						XY1 = geomAry1[i].split(',');
						points1.push( new google.maps.LatLng(parseFloat(XY1[1]),parseFloat(XY1[0]))) ;
					}

			    	// Construct polygon1
			    	polygon1 = new google.maps.Polygon({
			    	  paths: points1,
			    	  strokeColor: "#888888",
			    	  strokeOpacity: 0.8,
			    	  strokeWeight: 1,
			    	  fillColor: "#ff0000",
			    	  fillOpacity: 0.15
			    	});
			    	polygon1.setMap(map);

			    	// Calculate polygon2
					var geomAry2 = new Array();
					geomAry2 = returnGeom2.split('|');
					var XY2 = new Array();
					var points2 = [];
					for (var i = 0; i < geomAry2.length; i++) {
						XY2 = geomAry2[i].split(',');
						points2.push( new google.maps.LatLng(parseFloat(XY2[1]),parseFloat(XY2[0]))) ;
					}

			    	// Construct polygon2
			    	polygon2 = new google.maps.Polygon({
			    	  paths: points2,
			    	  strokeColor: "#888888",
			    	  strokeOpacity: 0.8,
			    	  strokeWeight: 1,
			    	  fillColor: "#ff0000",
			    	  fillOpacity: 0.15
			    	});
			    	polygon2.setMap(map);
				}
			}

			// The HomeControl returns user to original position and style =================
			function HomeControl(controlDiv, map) {
				// Set CSS styles for the DIV containing the control
				// Setting padding to 5 px will offset the control from the edge of the map
				controlDiv.style.paddingTop = '5px';
				controlDiv.style.paddingRight = '0px';

				// Set CSS for the control border
				var controlUI = document.createElement('DIV');
				controlUI.style.backgroundColor = 'white';
				controlUI.style.color = 'black';
				controlUI.style.borderColor = 'black';
				controlUI.style.borderColor = 'black';
				controlUI.style.borderStyle = 'solid';
				controlUI.style.borderWidth = '2px';
				controlUI.style.cursor = 'pointer';
				controlUI.style.textAlign = 'center';
				controlUI.title = '';
				controlDiv.appendChild(controlUI);

				// Set CSS for the control interior
				var controlText = document.createElement('DIV');
				controlText.style.fontFamily = 'Arial,sans-serif';
				controlText.style.fontSize = '12px';
				controlText.style.paddingLeft = '15px';
				controlText.style.paddingRight = '15px';
				controlText.innerHTML = '<b><?php echo WT_I18N::translate('Redraw map')?><\/b>';
				controlUI.appendChild(controlText);

				// Setup the click event listeners: simply set the map to original LatLng
				google.maps.event.addDomListener(controlUI, 'click', function() {
					map.setCenter(latlng);
					map.setZoom(pl_zoom);
					map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
				});
			}

			function loadMap(zoom, mapType) {
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
					mapTypeId: mapTyp,											// ROADMAP, SATELLITE, HYBRID, TERRAIN
					// mapTypeId: google.maps.MapTypeId.ROADMAP,				// ROADMAP, SATELLITE, HYBRID, TERRAIN
					mapTypeControlOptions: {
						style: google.maps.MapTypeControlStyle.DROPDOWN_MENU	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
					},
					navigationControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT,			// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
					style: google.maps.NavigationControlStyle.SMALL				// ANDROID, DEFAULT, SMALL, ZOOM_PAN
					},
					streetViewControl: false,									// Show Pegman or not
					scrollwheel: true
				};

				map = new google.maps.Map(document.getElementById('map_pane'), myOptions);

				overlays();

				// Close any infowindow when map is clicked
				google.maps.event.addListener(map, 'click', function() {
					infowindow.close();
				});

				// Create the DIV to hold the control and call HomeControl() passing in this DIV. --
				var homeControlDiv = document.createElement('DIV');
				var homeControl = new HomeControl(homeControlDiv, map);
				homeControlDiv.index = 1;
				map.controls[google.maps.ControlPosition.TOP_RIGHT].push(homeControlDiv);
				// ---------------------------------------------------------------------------------

				// Check for zoom changes
				google.maps.event.addListener(map, 'zoom_changed', function() {
					document.editplaces.NEW_ZOOM_FACTOR.value = map.zoom;
				});

				// Create the Main Location Marker
				<?php
				if ($level < 3 && $place_icon != '') {
					echo 'var image = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/',$place_icon,'",';
						echo 'new google.maps.Size(25, 15),';	// Image size
						echo 'new google.maps.Point(0, 0),';	// Image origin
						echo 'new google.maps.Point(0, 44)';	// Image anchor
					echo ');';
					echo 'var iconShadow = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/flag_shadow.png",';
						echo 'new google.maps.Size(35, 45),';	// Shadow size
						echo 'new google.maps.Point(0,0),';		// Shadow origin
						echo 'new google.maps.Point(1, 45)';	// Shadow anchor is base of flagpole
					echo ');';
					echo 'marker = new google.maps.Marker({';
						echo 'icon: image,';
						echo 'shadow: iconShadow,';
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

				prec = 20;
				for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
					if (document.editplaces.NEW_PRECISION[i].checked) {
						prec = document.editplaces.NEW_PRECISION[i].value;
					}
				}

				// Set marker by clicking on map ---
				clickset = google.maps.event.addListener(map, 'click', function(event) {
					clearMarks();
					latlng = event.latLng;
					<?php
						echo 'marker = new google.maps.Marker({';
						echo 'position: latlng,';
						echo 'map: map,';
						echo 'title: pl_name,';
						echo 'draggable: true,';
						echo 'zIndex: 1';
					echo '});';
					?>
					pos3 = marker.getPosition();
					document.getElementById('NEW_PLACE_LATI').value = parseFloat(pos3.lat()).toFixed(prec);
					document.getElementById('NEW_PLACE_LONG').value = parseFloat(pos3.lng()).toFixed(prec);
					updateMap('flag_drag');
					currzoom = parseInt(document.editplaces.NEW_ZOOM_FACTOR.value);
					mapType = map.getMapTypeId();
					loadMap(currzoom, mapType);
				});

				// Set marker by drag-n-drop on map ---
				dragset = google.maps.event.addListener(marker, 'drag', function() {
					pos1 = marker.getPosition();
					document.getElementById('NEW_PLACE_LATI').value = parseFloat(pos1.lat()).toFixed(prec);
					document.getElementById('NEW_PLACE_LONG').value = parseFloat(pos1.lng()).toFixed(prec);
				});
				dropset = google.maps.event.addListener(marker, 'dragend', function() {
					pos2 = marker.getPosition();
					document.getElementById('NEW_PLACE_LATI').value = parseFloat(pos2.lat()).toFixed(prec);
					document.getElementById('NEW_PLACE_LONG').value = parseFloat(pos2.lng()).toFixed(prec);
					updateMap('flag_drag');
				});
			}

			function clearMarks() {
				marker.setMap(null);
			}

			function setLoc(lat, lng) {
				prec = 20;
				for (i=0;i<document.editplaces.NEW_PRECISION.length;i++) {
					if (document.editplaces.NEW_PRECISION[i].checked) {
						prec = document.editplaces.NEW_PRECISION[i].value;
					}
				}
				if (lat < 0.0) {
					document.editplaces.NEW_PLACE_LATI.value = (lat.toFixed(prec) * -1);
					document.editplaces.LATI_CONTROL.value = 'PL_S';
				} else {
					document.editplaces.NEW_PLACE_LATI.value = lat.toFixed(prec);
					document.editplaces.LATI_CONTROL.value = 'PL_N';
				}
				if (lng < 0.0) {
					document.editplaces.NEW_PLACE_LONG.value = (lng.toFixed(prec) * -1);
					document.editplaces.LONG_CONTROL.value = 'PL_W';
				} else {
					document.editplaces.NEW_PLACE_LONG.value = lng.toFixed(prec);
					document.editplaces.LONG_CONTROL.value = 'PL_E';
				}
				newval = new google.maps.LatLng (lat.toFixed(prec), lng.toFixed(prec));
				updateMap();
			}

			function createMarker(i, point, name) {
				var contentString = '<div id="iwcontent_edit">'+name+'<\/div>';
				<?php
				echo 'var image = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/marker_yellow.png",';
					echo 'new google.maps.Size(20, 34),';	// Image size
					echo 'new google.maps.Point(0, 0),';	// Image origin
					echo 'new google.maps.Point(10, 34)';	// Image anchor
				echo ');';
				echo 'var iconShadow = new google.maps.MarkerImage("', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/shadow50.png",';
					echo 'new google.maps.Size(37, 34),';	// Shadow size
					echo 'new google.maps.Point(0, 0),';	// Shadow origin
					echo 'new google.maps.Point(10, 34)';	// Shadow anchor is base of image
				echo ');';
				?>
				var marker = new google.maps.Marker({
					icon: image,
					shadow: iconShadow,
					map: map,
					position: point,
					zIndex: 0
				});

				google.maps.event.addListener(marker, 'click', function() {
					infowindow.close();
					infowindow.setContent(contentString);
					infowindow.open(map, marker);
				});

				google.maps.event.addListener(map, 'click', function() {
					infowindow.close();
				});

				return marker;
			}

			function change_icon() {
				window.open('module.php?mod=googlemap&mod_action=flags&countrySelected=<?php echo $selected_country; ?>', '_blank', indx_window_specs);
				return false;
			}

			function remove_icon() {
				document.editplaces.icon.value = '';
				document.getElementById('flagsDiv').innerHTML = '<a href="#" onclick="change_icon();return false;"><?php echo WT_I18N::translate('Change flag'); ?></a>';
			}

			function addAddressToMap(response) {
				var bounds = new google.maps.LatLngBounds();
				if (!response ) {
					alert('<?php echo WT_I18N::translate('No places found'); ?>');
				} else {
					if (response.length > 0) {
						for (i=0; i<response.length; i++) {
							// 5 decimal places is approx 1 metre accuracy.
							var name  = '<div id="gname" class="iwstyle">'+response[i].address_components[0].short_name+'<br>('+response[i].geometry.location.lng().toFixed(5)+','+response[i].geometry.location.lat().toFixed(5)+'';
								name +=	'<br><a href="#" onclick="setLoc(' + response[i].geometry.location.lat() + ', ' + response[i].geometry.location.lng() + ');"><div id="namelink"><?php echo WT_I18N::translate('Use this value'); ?></div></a>';
								name += '</div>';
							var point = response[i].geometry.location;
							var marker = createMarker(i, point, name);
							bounds.extend(response[i].geometry.location);
						}

						<?php if ($level > 0) { ?>
							map.fitBounds(bounds);
						<?php } ?>
						zoomlevel = map.getZoom();

						if (zoomlevel < <?php echo $this->getSetting('GM_MIN_ZOOM'); ?>) {
							zoomlevel = <?php echo $this->getSetting('GM_MIN_ZOOM'); ?>;
						}
						if (zoomlevel > <?php echo $this->getSetting('GM_MAX_ZOOM'); ?>) {
							zoomlevel = <?php echo $this->getSetting('GM_MAX_ZOOM'); ?>;
						}
						if (document.editplaces.NEW_ZOOM_FACTOR.value < zoomlevel) {
							zoomlevel = document.editplaces.NEW_ZOOM_FACTOR.value;
							if (zoomlevel < <?php echo $this->getSetting('GM_MIN_ZOOM'); ?>) {
								zoomlevel = <?php echo $this->getSetting('GM_MIN_ZOOM'); ?>;
							}
							if (zoomlevel > <?php echo $this->getSetting('GM_MAX_ZOOM'); ?>) {
								zoomlevel = <?php echo $this->getSetting('GM_MAX_ZOOM'); ?>;
							}
						}
						map.setCenter(bounds.getCenter());
						map.setZoom(zoomlevel);
					}
				}
			}

			function showLocation_level(address) {
				address += '<?php if ($level>0) echo ', ', addslashes(implode(', ', array_reverse($where_am_i, true))); ?>';
				geocoder.geocode({'address': address}, addAddressToMap);
			}

			function showLocation_all(address) {
				geocoder.geocode({'address': address}, addAddressToMap);
			}

			function paste_char(value) {
				document.editplaces.NEW_PLACE_NAME.value += value;
			}
			window.onload = function() { loadMap(); };
		</script>
		<table><tr><td align="center">
		</td></tr></table>
		</body>
		<form method="post" id="editplaces" name="editplaces" action="module.php?mod=googlemap&amp;mod_action=places_edit">
			<input type="hidden" name="action" value="<?php echo $action; ?>record">
			<input type="hidden" name="placeid" value="<?php echo $placeid; ?>">
			<input type="hidden" name="level" value="<?php echo $level; ?>">
			<input type="hidden" name="icon" value="<?php echo $place_icon; ?>">
			<input type="hidden" name="parent_id" value="<?php echo $parent_id; ?>">
			<input type="hidden" name="place_long" value="<?php echo $place_long; ?>">
			<input type="hidden" name="place_lati" value="<?php echo $place_lati; ?>">
			<input type="hidden" name="parent_long" value="<?php echo $parent_long; ?>">
			<input type="hidden" name="parent_lati" value="<?php echo $parent_lati; ?>">

			<table class="facts_table">
			<tr>
				<td class="optionbox" colspan="3">
				<center><div id="map_pane" style="width: 100%; height: 300px;"></div></center>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox"><?php echo WT_Gedcom_Tag::getLabel('PLAC'); ?></td>
				 <td class="optionbox"><input type="text" id="new_pl_name" name="NEW_PLACE_NAME" value="<?php echo WT_Filter::escapeHtml($place_name); ?>" size="25" class="address_input">
					<div id="INDI_PLAC_pop" style="display: inline;">
					<?php echo print_specialchar_link('new_pl_name'); ?></div></td><td class="optionbox">
					<label for="new_pl_name"><a href="#" onclick="showLocation_all(document.getElementById('new_pl_name').value); return false">&nbsp;<?php echo WT_I18N::translate('Search globally'); ?></a></label>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<label for="new_pl_name"><a href="#" onclick="showLocation_level(document.getElementById('new_pl_name').value); return false">&nbsp;<?php echo WT_I18N::translate('Search locally'); ?></a></label>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox"><?php echo WT_I18N::translate('Precision'), help_link('PLE_PRECISION','googlemap'); ?></td>
				<?php
					$exp = explode(".", $place_lati);
					if (isset($exp[1])) {
						$precision1 = strlen($exp[1]);
					} else {
						$precision1 = -1;
					}
					$exp = explode(".", $place_long);
					if (isset($exp[1])) {
						$precision2 = strlen($exp[1]);
					} else {
						$precision2 = -1;
					}
					($precision1 > $precision2) ? ($precision = $precision1) : ($precision = $precision2);
					if ($precision == -1 ) ($level > 3) ? ($precision = 3) : ($precision = $level);
					elseif ($precision > 5) {
						$precision = 5;
					}
				?>
				<td class="optionbox" colspan="2">
					<input type="radio" id="new_prec_0" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$this->getSetting('GM_PRECISION_0')) echo "checked=\"checked\""; ?> value="<?php echo $this->getSetting('GM_PRECISION_0'); ?>">
					<label for="new_prec_0"><?php echo WT_I18N::translate('Country'); ?></label>
					<input type="radio" id="new_prec_1" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$this->getSetting('GM_PRECISION_1')) echo "checked=\"checked\""; ?> value="<?php echo $this->getSetting('GM_PRECISION_1'); ?>">
					<label for="new_prec_1"><?php echo WT_I18N::translate('State'); ?></label>
					<input type="radio" id="new_prec_2" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$this->getSetting('GM_PRECISION_2')) echo "checked=\"checked\""; ?> value="<?php echo $this->getSetting('GM_PRECISION_2'); ?>">
					<label for="new_prec_2"><?php echo WT_I18N::translate('City'); ?></label>
					<input type="radio" id="new_prec_3" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$this->getSetting('GM_PRECISION_3')) echo "checked=\"checked\""; ?> value="<?php echo $this->getSetting('GM_PRECISION_3'); ?>">
					<label for="new_prec_3"><?php echo WT_I18N::translate('Neighborhood'); ?></label>
					<input type="radio" id="new_prec_4" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision==$this->getSetting('GM_PRECISION_4')) echo "checked=\"checked\""; ?> value="<?php echo $this->getSetting('GM_PRECISION_4'); ?>">
					<label for="new_prec_4"><?php echo WT_I18N::translate('House'); ?></label>
					<input type="radio" id="new_prec_5" name="NEW_PRECISION" onchange="updateMap();" <?php if ($precision>=$this->getSetting('GM_PRECISION_5')) echo "checked=\"checked\""; ?> value="<?php echo $this->getSetting('GM_PRECISION_5'); ?>">
					<label for="new_prec_5"><?php echo WT_I18N::translate('Max'); ?></label>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox"><?php echo WT_Gedcom_Tag::getLabel('LATI'); ?></td>
				<td class="optionbox" colspan="2">
					<input type="text" id="NEW_PLACE_LATI" name="NEW_PLACE_LATI" placeholder="<?php echo /* I18N: Measure of latitude/longitude */ WT_I18N::translate('degrees') ?>" value="<?php if ($place_lati != null) echo abs($place_lati); ?>" size="20" onchange="updateMap();">
					<select name="LATI_CONTROL" onchange="updateMap();">
						<option value="PL_N" <?php if ($place_lati > 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('north'); ?></option>
						<option value="PL_S" <?php if ($place_lati < 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('south'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox"><?php echo WT_Gedcom_Tag::getLabel('LONG'); ?></td>
				<td class="optionbox" colspan="2">
					<input type="text" id="NEW_PLACE_LONG" name="NEW_PLACE_LONG" placeholder="<?php echo WT_I18N::translate('degrees') ?>" value="<?php if ($place_long != null) echo abs($place_long); ?>" size="20" onchange="updateMap();">
					<select name="LONG_CONTROL" onchange="updateMap();">
						<option value="PL_E" <?php if ($place_long > 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('east'); ?></option>
						<option value="PL_W" <?php if ($place_long < 0) echo " selected=\"selected\""; echo ">", WT_I18N::translate('west'); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="descriptionbox"><?php echo WT_I18N::translate('Zoom level'), help_link('PLE_ZOOM','googlemap'); ?></td>
				<td class="optionbox" colspan="2">
					<input type="text" id="NEW_ZOOM_FACTOR" name="NEW_ZOOM_FACTOR" value="<?php echo $zoomfactor; ?>" size="20" onchange="updateMap();"></td>
			</tr>
			<tr>
				<td class="descriptionbox"><?php echo WT_I18N::translate('Flag'), help_link('PLE_ICON','googlemap'); ?></td>
				<td class="optionbox" colspan="2">
					<div id="flagsDiv">
		<?php
				if (($place_icon == NULL) || ($place_icon == "")) { ?>
						<a href="#" onclick="change_icon();return false;"><?php echo WT_I18N::translate('Change flag'); ?></a>
		<?php   }
				else { ?>
						<img alt="<?php echo /* I18N: The emblem of a country or region */ WT_I18N::translate('Flag'); ?>" src="<?php echo WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place_icon; ?>">&nbsp;&nbsp;
						<a href="#" onclick="change_icon();return false;"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;
						<a href="#" onclick="remove_icon();return false;"><?php echo WT_I18N::translate('Remove flag'); ?></a>
		<?php   } ?>
					</div></td>
			</tr>
			</table>
			<p id="save-cancel">
				<input type="submit" class="save" value="<?php echo WT_I18N::translate('save'); ?>">
				<input type="button" class="cancel" value="<?php echo WT_I18N::translate('close'); ?>" onclick="window.close();">
			</p>
		</form>
		<?php
	}

	private function adminPlaces() {
		require WT_ROOT.'includes/functions/functions_edit.php';

		$action       = WT_Filter::get('action');
		$parent       = WT_Filter::get('parent');
		$inactive     = WT_Filter::getBool('inactive');
		$deleteRecord = WT_Filter::get('deleteRecord');

		if (!isset($parent)) {
			$parent=0;
		}

		$controller = new WT_Controller_Page();
		$controller->restrictAccess(Auth::isAdmin());

		if ($action=='ExportFile' && Auth::isAdmin()) {
			Zend_Session::writeClose();
			$tmp = $this->placeIdToHierarchy($parent);
			$maxLevel = $this->getHighestLevel();
			if ($maxLevel>8) $maxLevel=8;
			$tmp[0] = 'places';
			$outputFileName=preg_replace('/[:;\/\\\(\)\{\}\[\] $]/', '_', implode('-', $tmp)).'.csv';
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$outputFileName.'"');
			echo '"', WT_I18N::translate('Level'), '";"', WT_I18N::translate('Country'), '";';
			if ($maxLevel>0) echo '"', WT_I18N::translate('State'), '";';
			if ($maxLevel>1) echo '"', WT_I18N::translate('County'), '";';
			if ($maxLevel>2) echo '"', WT_I18N::translate('City'), '";';
			if ($maxLevel>3) echo '"', WT_I18N::translate('Place'), '";';
			if ($maxLevel>4) echo '"', WT_I18N::translate('Place'), '";';
			if ($maxLevel>5) echo '"', WT_I18N::translate('Place'), '";';
			if ($maxLevel>6) echo '"', WT_I18N::translate('Place'), '";';
			if ($maxLevel>7) echo '"', WT_I18N::translate('Place'), '";';
			echo '"', WT_I18N::translate('Longitude'), '";"', WT_I18N::translate('Latitude'), '";';
			echo '"', WT_I18N::translate('Zoom level'), '";"', WT_I18N::translate('Icon'), '";', WT_EOL;
			$this->outputLevel($parent);
			exit;
		}

		$controller
			->setPageTitle(WT_I18N::translate('Google Maps™'))
			->pageHeader();

		?>
		<table id="gm_config">
			<tr>
				<th>
					<a href="module.php?mod=googlemap&amp;mod_action=admin_config">
						<?php echo WT_I18N::translate('Google Maps™ preferences'); ?>
					</a>
				</th>
				<th>
					<a class="current" href="module.php?mod=googlemap&amp;mod_action=admin_places">
						<?php echo WT_I18N::translate('Geographic data'); ?>
					</a>
				</th>
				<th>
					<a href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">
						<?php echo WT_I18N::translate('Place check'); ?>
					</a>
				</th>
			</tr>
		</table>
		<?php

		if ($action=='ImportGedcom') {
			$placelist=array();
			$j=0;
			$gedcom_records =
				WT_DB::prepare("SELECT i_gedcom FROM `##individuals` WHERE i_file=? UNION ALL SELECT f_gedcom FROM `##families` WHERE f_file=?")
				->execute(array(WT_GED_ID, WT_GED_ID))
				->fetchOneColumn();
			foreach ($gedcom_records as $gedrec) {
				$i = 1;
				$placerec = get_sub_record(2, '2 PLAC', $gedrec, $i);
				while (!empty($placerec)) {
					if (preg_match("/2 PLAC (.+)/", $placerec, $match)) {
						$placelist[$j] = array();
						$placelist[$j]['place'] = trim($match[1]);
						if (preg_match("/4 LATI (.*)/", $placerec, $match)) {
							$placelist[$j]['lati'] = trim($match[1]);
							if (($placelist[$j]['lati'][0] != 'N') && ($placelist[$j]['lati'][0] != 'S')) {
								if ($placelist[$j]['lati'] < 0) {
									$placelist[$j]['lati'][0] = 'S';
								} else {
									$placelist[$j]['lati'] = 'N'.$placelist[$j]['lati'];
								}
							}
						}
						else $placelist[$j]['lati'] = NULL;
						if (preg_match("/4 LONG (.*)/", $placerec, $match)) {
							$placelist[$j]['long'] = trim($match[1]);
							if (($placelist[$j]['long'][0] != 'E') && ($placelist[$j]['long'][0] != 'W')) {
								if ($placelist[$j]['long'] < 0) {
									$placelist[$j]['long'][0] = 'W';
								} else {
									$placelist[$j]['long'] = 'E'.$placelist[$j]['long'];
								}
							}
						}
						else $placelist[$j]['long'] = NULL;
						$j = $j + 1;
					}
					$i = $i + 1;
					$placerec = get_sub_record(2, '2 PLAC', $gedrec, $i);
				}
			}
			asort($placelist);

			$prevPlace = '';
			$prevLati = '';
			$prevLong = '';
			$placelistUniq = array();
			$j = 0;
			foreach ($placelist as $k=>$place) {
				if ($place['place'] != $prevPlace) {
					$placelistUniq[$j] = array();
					$placelistUniq[$j]['place'] = $place['place'];
					$placelistUniq[$j]['lati'] = $place['lati'];
					$placelistUniq[$j]['long'] = $place['long'];
					$j = $j + 1;
				} elseif (($place['place'] == $prevPlace) && (($place['lati'] != $prevLati) || ($place['long'] != $prevLong))) {
					if (($placelistUniq[$j-1]['lati'] == 0) || ($placelistUniq[$j-1]['long'] == 0)) {
						$placelistUniq[$j-1]['lati'] = $place['lati'];
						$placelistUniq[$j-1]['long'] = $place['long'];
					} elseif (($place['lati'] != '0') || ($place['long'] != '0')) {
						echo 'Difference: previous value = ', $prevPlace, ', ', $prevLati, ', ', $prevLong, ' current = ', $place['place'], ', ', $place['lati'], ', ', $place['long'], '<br>';
					}
				}
				$prevPlace = $place['place'];
				$prevLati = $place['lati'];
				$prevLong = $place['long'];
			}

			$highestIndex = $this->getHighestIndex();

			$default_zoom_level=array(4, 7, 10, 12);
			foreach ($placelistUniq as $k=>$place) {
		        $parent=preg_split('/ *, */', $place['place']);
				$parent=array_reverse($parent);
				$parent_id=0;
				for ($i=0; $i<count($parent); $i++) {
					if (!isset($default_zoom_level[$i]))
						$default_zoom_level[$i]=$default_zoom_level[$i-1];
					$escparent=$parent[$i];
					if ($escparent == '') {
						$escparent = 'Unknown';
					}
					$row=
						WT_DB::prepare("SELECT pl_id, pl_long, pl_lati, pl_zoom FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ?")
						->execute(array($i, $parent_id, $escparent))
						->fetchOneRow();
					if ($i < count($parent)-1) {
						// Create higher-level places, if necessary
						if (empty($row)) {
							$highestIndex++;
							WT_DB::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom) VALUES (?, ?, ?, ?, ?)")
								->execute(array($highestIndex, $parent_id, $i, $escparent, $default_zoom_level[$i]));
							echo WT_Filter::escapeHtml($escparent), '<br>';
							$parent_id=$highestIndex;
						} else {
							$parent_id=$row->pl_id;
						}
					} else {
						// Create lowest-level place, if necessary
						if (empty($row->pl_id)) {
							$highestIndex++;
							WT_DB::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom) VALUES (?, ?, ?, ?, ?, ?, ?)")
								->execute(array($highestIndex, $parent_id, $i, $escparent, $place['long'], $place['lati'], $default_zoom_level[$i]));
							echo WT_Filter::escapeHtml($escparent), '<br>';
						} else {
							if (empty($row->pl_long) && empty($row->pl_lati) && $place['lati']!='0' && $place['long']!='0') {
								WT_DB::prepare("UPDATE `##placelocation` SET pl_lati=?, pl_long=? WHERE pl_id=?")
									->execute(array($place['lati'], $place['long'], $row->pl_id));
								echo WT_Filter::escapeHtml($escparent), '<br>';
							}
						}
					}
				}
			}
			$parent=0;
		}

		if ($action=='ImportFile') {
			$placefiles = array();
			$this->findFiles(WT_MODULES_DIR.'googlemap/extra');
			sort($placefiles);
		?>
		<form method="post" enctype="multipart/form-data" id="importfile" name="importfile" action="module.php?mod=googlemap&amp;mod_action=admin_places&amp;action=ImportFile2">
			<table class="gm_plac_edit">
				<tr>
					<th><?php echo WT_I18N::translate('File containing places (CSV)'); ?></th>
					<td><input type="file" name="placesfile" size="50"></td>
				</tr>
				<?php if (count($placefiles)>0) { ?>
				<tr>
					<th><?php echo WT_I18N::translate('Server file containing places (CSV)'), help_link('PLIF_LOCALFILE','googlemap'); ?></th>
					<td>
						<select name="localfile">
							<option></option>
							<?php foreach ($placefiles as $p=>$placefile) { ?>
							<option value="<?php echo WT_Filter::escapeHtml($placefile); ?>"><?php
								if (substr($placefile, 0, 1)=="/") echo substr($placefile, 1);
								else echo $placefile; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<th><?php echo WT_I18N::translate('Delete all existing geographic data before importing the file.'); ?></th>
					<td><input type="checkbox" name="cleardatabase"></td>
				</tr>
				<tr>
					<th><?php echo WT_I18N::translate('Do not create new locations, just import coordinates for existing locations.'); ?></th>
					<td><input type="checkbox" name="updateonly"></td>
				</tr>
				<tr>
					<th><?php echo WT_I18N::translate('Overwrite existing coordinates.'); ?></th>
					<td><input type="checkbox" name="overwritedata"></td>
				</tr>
			</table>
			<input id="savebutton" type="submit" value="<?php echo WT_I18N::translate('Continue adding'); ?>"><br>
		</form>
		<?php
			exit;
		}

		if ($action=='ImportFile2') {
			$country_names=array();
			foreach (WT_Stats::iso3166() as $key=>$value) {
				$country_names[$key]=WT_I18N::translate($key);
			}
			if (isset($_POST['cleardatabase'])) {
				WT_DB::exec("DELETE FROM `##placelocation` WHERE 1=1");
			}
			if (!empty($_FILES['placesfile']['tmp_name'])) {
				$lines = file($_FILES['placesfile']['tmp_name']);
			} elseif (!empty($_REQUEST['localfile'])) {
				$lines = file(WT_MODULES_DIR.'googlemap/extra'.$_REQUEST['localfile']);
			}
			// Strip BYTE-ORDER-MARK, if present
			if (!empty($lines[0]) && substr($lines[0], 0, 3)==WT_UTF8_BOM) $lines[0]=substr($lines[0], 3);
			asort($lines);
			$highestIndex = $this->getHighestIndex();
			$placelist = array();
			$j = 0;
			$maxLevel = 0;
			foreach ($lines as $p => $placerec) {
				$fieldrec = explode(';', $placerec);
				if ($fieldrec[0] > $maxLevel) $maxLevel = $fieldrec[0];
			}
			$fields = count($fieldrec);
			$set_icon = true;
			if (!is_dir(WT_MODULES_DIR.'googlemap/places/flags/')) {
				$set_icon = false;
			}
			foreach ($lines as $p => $placerec) {
				$fieldrec = explode(';', $placerec);
				if (is_numeric($fieldrec[0]) && $fieldrec[0]<=$maxLevel) {
					$placelist[$j] = array();
					$placelist[$j]['place'] = '';
					for ($ii=$fields-4; $ii>1; $ii--) {
						if ($fieldrec[0] > $ii-2) $placelist[$j]['place'] .= $fieldrec[$ii].',';
					}
					foreach ($country_names as $countrycode => $countryname) {
						if ($countrycode == strtoupper($fieldrec[1])) {
							$fieldrec[1] = $countryname;
							break;
						}
					}
					$placelist[$j]['place'] .= $fieldrec[1];
					$placelist[$j]['long'] = $fieldrec[$fields-4];
					$placelist[$j]['lati'] = $fieldrec[$fields-3];
					$placelist[$j]['zoom'] = $fieldrec[$fields-2];
					if($set_icon) {
						$placelist[$j]['icon'] = trim($fieldrec[$fields-1]);
					} else {
						$placelist[$j]['icon'] = '';
					}
					$j = $j + 1;
				}
			}

			$prevPlace = '';
			$prevLati = '';
			$prevLong = '';
			$placelistUniq = array();
			$j = 0;
			foreach ($placelist as $k=>$place) {
				if ($place['place'] != $prevPlace) {
					$placelistUniq[$j] = array();
					$placelistUniq[$j]['place'] = $place['place'];
					$placelistUniq[$j]['lati'] = $place['lati'];
					$placelistUniq[$j]['long'] = $place['long'];
					$placelistUniq[$j]['zoom'] = $place['zoom'];
					$placelistUniq[$j]['icon'] = $place['icon'];
					$j = $j + 1;
				} elseif (($place['place'] == $prevPlace) && (($place['lati'] != $prevLati) || ($place['long'] != $prevLong))) {
					if (($placelistUniq[$j-1]['lati'] == 0) || ($placelistUniq[$j-1]['long'] == 0)) {
						$placelistUniq[$j-1]['lati'] = $place['lati'];
						$placelistUniq[$j-1]['long'] = $place['long'];
						$placelistUniq[$j-1]['zoom'] = $place['zoom'];
						$placelistUniq[$j-1]['icon'] = $place['icon'];
					} elseif (($place['lati'] != '0') || ($place['long'] != '0')) {
						echo 'Difference: previous value = ', $prevPlace, ', ', $prevLati, ', ', $prevLong, ' current = ', $place['place'], ', ', $place['lati'], ', ', $place['long'], '<br>';
					}
				}
				$prevPlace = $place['place'];
				$prevLati = $place['lati'];
				$prevLong = $place['long'];
			}

			$default_zoom_level = array();
			$default_zoom_level[0] = 4;
			$default_zoom_level[1] = 7;
			$default_zoom_level[2] = 10;
			$default_zoom_level[3] = 12;
			foreach ($placelistUniq as $k=>$place) {
				$parent = explode(',', $place['place']);
				$parent = array_reverse($parent);
				$parent_id=0;
				for ($i=0; $i<count($parent); $i++) {
					$escparent=$parent[$i];
					if ($escparent == '') {
						$escparent = 'Unknown';
					}
					$row=
						WT_DB::prepare("SELECT pl_id, pl_long, pl_lati, pl_zoom, pl_icon FROM `##placelocation` WHERE pl_level=? AND pl_parent_id=? AND pl_place LIKE ? ORDER BY pl_place")
						->execute(array($i, $parent_id, $escparent))
						->fetchOneRow();
					if (empty($row)) {       // this name does not yet exist: create entry
						if (!isset($_POST['updateonly'])) {
							$highestIndex = $highestIndex + 1;
							if (($i+1) == count($parent)) {
								$zoomlevel = $place['zoom'];
							} elseif (isset($default_zoom_level[$i])) {
								$zoomlevel = $default_zoom_level[$i];
							} else {
								$zoomlevel = $this->getSetting('GM_MAX_ZOOM');
							}
							if (($place['lati'] == '0') || ($place['long'] == '0') || (($i+1) < count($parent))) {
								WT_DB::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?)")
									->execute(array($highestIndex, $parent_id, $i, $escparent, $zoomlevel, $place['icon']));
							} else {
								//delete leading zero
								$pl_lati = str_replace(array('N', 'S', ','), array('', '-', '.') , $place['lati']);
								$pl_long = str_replace(array('E', 'W', ','), array('', '-', '.') , $place['long']);
								if ($pl_lati >= 0) {
									$place['lati'] = 'N'.abs($pl_lati);
								} elseif ($pl_lati < 0) {
									$place['lati'] = 'S'.abs($pl_lati);
								}
								if ($pl_long >= 0) {
									$place['long'] = 'E'.abs($pl_long);
								} elseif ($pl_long < 0) {
									$place['long'] = 'W'.abs($pl_long);
								}
								WT_DB::prepare("INSERT INTO `##placelocation` (pl_id, pl_parent_id, pl_level, pl_place, pl_long, pl_lati, pl_zoom, pl_icon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
									->execute(array($highestIndex, $parent_id, $i, $escparent, $place['long'], $place['lati'], $zoomlevel, $place['icon']));
							}
							$parent_id = $highestIndex;
						}
					} else {
						$parent_id = $row->pl_id;
						if ((isset($_POST['overwritedata'])) && ($i+1 == count($parent))) {
							WT_DB::prepare("UPDATE `##placelocation` SET pl_lati=?, pl_long=?, pl_zoom=?, pl_icon=? WHERE pl_id=?")
								->execute(array($place['lati'], $place['long'], $place['zoom'], $place['icon'], $parent_id));
						} else {
							if ((($row->pl_long == '0') || ($row->pl_long == null)) && (($row->pl_lati == '0') || ($row->pl_lati == null))) {
								WT_DB::prepare("UPDATE `##placelocation` SET pl_lati=?, pl_long=? WHERE pl_id=?")
									->execute(array($place['lati'], $place['long'], $parent_id));
							}
							if (empty($row->pl_icon) && !empty($place['icon'])) {
								WT_DB::prepare("UPDATE `##placelocation` SET pl_icon=? WHERE pl_id=?")
									->execute(array($place['icon'], $parent_id));
							}
						}
					}
				}
			}
			$parent=0;
		}

		if ($action=='DeleteRecord') {
			$exists=
				WT_DB::prepare("SELECT 1 FROM `##placelocation` WHERE pl_parent_id=?")
				->execute(array($deleteRecord))
				->fetchOne();

			if (!$exists) {
				WT_DB::prepare("DELETE FROM `##placelocation` WHERE pl_id=?")
					->execute(array($deleteRecord));
			} else {
				echo '<table class="facts_table"><tr><td>', WT_I18N::translate('Location not removed: this location contains sub-locations'), '</td></tr></table>';
			}
		}

		?>
		<script>
		function updateList(inactive) {
			window.location.href='<?php if (strstr($_SERVER['REQUEST_URI'], '&inactive', true)) { $uri=strstr($_SERVER['REQUEST_URI'], '&inactive', true);} else { $uri=$_SERVER['REQUEST_URI']; } echo $uri, '&inactive='; ?>'+inactive;
		}

		function edit_place_location(placeid) {
			window.open('module.php?mod=googlemap&mod_action=places_edit&action=update&placeid='+placeid, '_blank', gmap_window_specs);
			return false;
		}

		function add_place_location(placeid) {
			window.open('module.php?mod=googlemap&mod_action=places_edit&action=add&placeid='+placeid, '_blank', gmap_window_specs);
			return false;
		}

		function delete_place(placeid) {
			var answer=confirm('<?php echo WT_I18N::translate('Remove this location?'); ?>');
			if (answer == true) {
				window.location = '<?php echo $_SERVER['REQUEST_URI']; ?>&action=DeleteRecord&deleteRecord=' + placeid;
			}
		}
		</script>
		<?php
		echo '<div id="gm_breadcrumb">';
		$where_am_i=$this->placeIdToHierarchy($parent);
		foreach (array_reverse($where_am_i, true) as $id=>$place) {
			if ($id==$parent) {
				if ($place != 'Unknown') {
					echo WT_Filter::escapeHtml($place);
				} else {
					echo WT_I18N::translate('unknown');
				}
			} else {
				echo '<a href="module.php?mod=googlemap&mod_action=admin_places&parent=', $id, '&inactive=', $inactive, '">';
				if ($place != 'Unknown') {
					echo WT_Filter::escapeHtml($place), '</a>';
				} else {
					echo WT_I18N::translate('unknown'), '</a>';
				}
			}
			echo ' - ';
		}
		echo '<a href="module.php?mod=googlemap&mod_action=admin_places&parent=0&inactive=', $inactive, '">', WT_I18N::translate('Top level'), '</a></div>';
		echo '<form name="active" method="post" action="module.php?mod=googlemap&mod_action=admin_places&parent=', $parent, '&inactive=', $inactive, '"><div id="gm_active">';
		echo '<label for="inactive">', WT_I18N::translate('Show inactive places'), '</label>';
		echo '<input type="checkbox" name="inactive" id="inactive"';
		if ($inactive) echo ' checked="checked"';
		echo ' onclick="updateList(this.checked)"';
		echo '>',  help_link('PLE_ACTIVE','googlemap'), '</div></form>';

		$placelist=$this->getPlaceListLocation($parent, $inactive);
		echo '<div class="gm_plac_edit">';
		echo '<table class="gm_plac_edit"><tr>';
		echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th>';
		echo '<th>', WT_Gedcom_Tag::getLabel('LATI'), '</th>';
		echo '<th>', WT_Gedcom_Tag::getLabel('LONG'), '</th>';
		echo '<th>', WT_I18N::translate('Zoom level'), '</th>';
		echo '<th>', WT_I18N::translate('Icon'), '</th>';
		echo '<th>';
		echo WT_I18N::translate('Edit'), '</th><th>', WT_I18N::translate('Delete'), '</th></tr>';
		if (count($placelist) == 0)
			echo '<tr><td colspan="7" class="accepted">', WT_I18N::translate('No places found'), '</td></tr>';
		foreach ($placelist as $place) {
			echo '<tr><td><a href="module.php?mod=googlemap&mod_action=admin_places&parent=', $place['place_id'], '&inactive=', $inactive, '">';
			if ($place['place'] != 'Unknown')
					echo WT_Filter::escapeHtml($place['place']), '</a></td>';
				else
					echo WT_I18N::translate('unknown'), '</a></td>';
			echo '<td>', $place['lati'], '</td>';
			echo '<td>', $place['long'], '</td>';
			echo '<td>', $place['zoom'], '</td>';
			echo '<td>';
			if (($place['icon'] == NULL) || ($place['icon'] == '')) {
				if (($place['lati'] == NULL) || ($place['long'] == NULL) || (($place['lati'] == '0') && ($place['long'] == '0'))) {
					echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/mm_20_yellow.png">';
				}
				else {
					echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/images/mm_20_red.png">';
				}
			} else {
				echo '<img src="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/', $place['icon'], '" width="25" height="15">';
			}
			echo '</td>';
			echo '<td class="narrow"><a href="#" onclick="edit_place_location(', $place['place_id'], ');return false;" class="icon-edit" title="', WT_I18N::translate('Edit'), '"></a></td>';
			$noRows=
				WT_DB::prepare("SELECT COUNT(pl_id) FROM `##placelocation` WHERE pl_parent_id=?")
				->execute(array($place['place_id']))
				->fetchOne();
			if ($noRows==0) { ?>
				<td><a href="#" onclick="delete_place(<?php echo $place['place_id']?>);return false;" class="icon-delete" title="<?php echo WT_I18N::translate('Remove'); ?>"></a></td>
		<?php       } else { ?>
				<td><i class="icon-delete-grey"></i></td>
		<?php       } ?>
			</tr>
			<?php
		}
		?>
		</table>
		</div>

		<table id="gm_manage">
			<tr>
				<td>
					<?php echo WT_I18N::translate('Add  a new geographic location'); ?>
				</td>
				<td>
					<form action="?" onsubmit="add_place_location(this.parent_id.options[this.parent_id.selectedIndex].value); return false;">
						<?php echo select_edit_control('parent_id', $where_am_i, WT_I18N::translate('Top level'), $parent); ?>
						<input type="submit" value="<?php echo WT_I18N::translate('Add'); ?>">
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Import all places from a family tree'); ?>
				</td>
				<td>
					<form action="module.php" method="get">
						<input type="hidden" name="mod" value="googlemap">
						<input type="hidden" name="mod_action" value="admin_places">
						<input type="hidden" name="action" value="ImportGedcom">
						<?php echo select_edit_control('ged', WT_Tree::getNameList(), null, WT_GEDCOM); ?>
						<input type="submit" value="<?php echo WT_I18N::translate('Import'); ?>">
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Upload geographic data'); ?>
				</td>
				<td>
					<form action="module.php" method="get">
						<input type="hidden" name="mod" value="googlemap">
						<input type="hidden" name="mod_action" value="admin_places">
						<input type="hidden" name="action" value="ImportFile">
						<input type="submit" value="<?php echo WT_I18N::translate('Upload'); ?>">
					</form>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo WT_I18N::translate('Download geographic data'); ?>
				</td>
				<td>
					<form action="module.php" method="get">
						<input type="hidden" name="mod" value="googlemap">
						<input type="hidden" name="mod_action" value="admin_places">
						<input type="hidden" name="action" value="ExportFile">
						<?php echo select_edit_control('parent', $where_am_i, WT_I18N::translate('All'), WT_GED_ID); ?>
						<input type="submit" value="<?php echo WT_I18N::translate('Download'); ?>">
					</form>
				</td>
			</tr>
		</table>
		<?php
	}

	private function wtStreetView() {
	header('Content-type: text/html; charset=UTF-8');

		?>
		<html>
			<head>
				<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
				<script src="https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false"></script>
				<script>

		// Following function creates an array of the google map parameters passed ---------------------
		var qsParm = new Array();
		function qs() {
			var query = window.location.search.substring(1);
			var parms = query.split('&');
			for (var i=0; i<parms.length; i++) {
				var pos = parms[i].indexOf('=');
				if (pos > 0) {
					var key = parms[i].substring(0,pos);
					qsParm[key] = parms[i].substring(pos + 1);
				}
			}
		}
		qsParm['x'] = null;
		qsParm['y'] = null;
		qs();

		var geocoder = new google.maps.Geocoder();

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

		function updateMarkerStatus(str) {
			document.getElementById('markerStatus').innerHTML = str;
		}

		function updateMarkerPosition(latLng) {
			document.getElementById('info').innerHTML = [
				latLng.lat(),
				latLng.lng()
			].join(', ');
		}

		function updateMarkerAddress(str) {
			document.getElementById('address').innerHTML = str;
		}

		function roundNumber(num, dec) {
			return Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
		}

		function initialize() {
			var x = qsParm['x'];
			var y = qsParm['y'];
			var b = parseFloat(qsParm['b']);
			var p = parseFloat(qsParm['p']);
			var m = parseFloat(qsParm['m']);

			var latLng = new google.maps.LatLng(y, x);

			// Create the map and mapOptions
			var mapOptions = {
				zoom: 16,
				center: latLng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,  // ROADMAP, SATELLITE, HYBRID, TERRAIN
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.DROPDOWN_MENU  // DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
				},
				navigationControl: true,
				navigationControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
					style: google.maps.NavigationControlStyle.SMALL   // ANDROID, DEFAULT, SMALL, ZOOM_PAN
				},
				streetViewControl: false,  // Show Pegman or not
				scrollwheel: true
			};

			var map = new google.maps.Map(document.getElementById('mapCanvas'), mapOptions);

			var bearing = b;
			if (bearing < 0) {
				bearing=bearing+360;
			}
			var pitch = p;
			var svzoom = m;

			var imageNum = Math.round(bearing/22.5) % 16;

			var image = new google.maps.MarkerImage('<?php echo WT_SCRIPT_PATH . WT_MODULES_DIR; ?>/googlemap/images/panda-icons/panda-' + imageNum + '.png',
				// This marker is 50 pixels wide by 50 pixels tall.
				new google.maps.Size(50, 50),
				// The origin for this image is 0,0.
				new google.maps.Point(0, 0),
				// The anchor for this image is the base of the flagpole at 0,32.
				new google.maps.Point(26, 36)
			);

			var shape = {
				coord: [1, 1, 1, 20, 18, 20, 18 , 1],
				type: 'poly'
			};

			var marker = new google.maps.Marker({
				icon: image,
				// shape: shape,
				position: latLng,
				title: 'Drag me to a Blue Street',
				map: map,
				draggable: true
			});

			// ===Next, get the map’s default panorama and set up some defaults. ===========================

			// --- First check if Browser supports html5 ---
			var browserName=navigator.appName;
			if (browserName=='Microsoft Internet Explorer') {
				var render_type = '';
			} else {
				var render_type = 'html5';
			}

			// --- Create the panorama ---
			var panoramaOptions = {
				navigationControl: true,
				navigationControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT,  // BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
					style: google.maps.NavigationControlStyle.SMALL   // ANDROID, DEFAULT, SMALL, ZOOM_PAN
				},
				linksControl: true,
				addressControl: true,
				addressControlOptions: {
					style: {
						// display: 'none'
						// backgroundColor: 'red'
					}
				},
				position: latLng,
				mode: render_type,
				pov: {
					heading: bearing,
					pitch: pitch,
					zoom: svzoom
				}
			};
			panorama = new google.maps.StreetViewPanorama(document.getElementById('mapCanvas'), panoramaOptions);
			panorama.setPosition(latLng);
			setTimeout(function() { panorama.setVisible(true); }, 1000);
			setTimeout(function() { panorama.setVisible(true); }, 2000);
			setTimeout(function() { panorama.setVisible(true); }, 3000);

			// Enable navigator contol and address control to be toggled with right mouse button -------
			var aLink = document.createElement('a');
			aLink.href = 'javascript:void(0)'; onmousedown=function(e) {
				if (parseInt(navigator.appVersion)>3) {
					var clickType=1;
					if (navigator.appName=='Netscape') {
						clickType=e.which;
					} else {
						clickType=event.button;
					}
					if (clickType==1) {
						self.status='Left button!';
					}
					if (clickType!=1) {
						if (panorama.get('addressControl') == false) {
							panorama.set('navigationControl', false);
							panorama.set('addressControl', true);
							panorama.set('linksControl', true);
						} else {
							panorama.set('navigationControl', false);
							panorama.set('addressControl', false);
							panorama.set('linksControl', false);
						}
					}
				}
				return true;
			};
			panorama.controls[google.maps.ControlPosition.TOP_RIGHT].push(aLink);

			// Update current position info.
			updateMarkerPosition(latLng);
			geocodePosition(latLng);

			// Add dragging event listeners.
			google.maps.event.addListener(marker, 'dragstart', function() {
				updateMarkerAddress('Dragging...');
			});

			google.maps.event.addListener(marker, 'drag', function() {
				updateMarkerStatus('Dragging...');
				updateMarkerPosition(marker.getPosition());
				panorama.setPosition(marker.getPosition());
			});

			google.maps.event.addListener(marker, 'dragend', function() {
				updateMarkerStatus('Drag ended');
				geocodePosition(marker.getPosition());
			});

			google.maps.event.addListener(panorama, 'pov_changed', function() {
				povLevel = panorama.getPov();
				parent.document.getElementById('sv_bearText').value = roundNumber(povLevel.heading, 2)+"\u00B0";
				parent.document.getElementById('sv_elevText').value = roundNumber(povLevel.pitch, 2)+"\u00B0";
				parent.document.getElementById('sv_zoomText').value = roundNumber(povLevel.zoom, 2);
			});

			google.maps.event.addListener(panorama, 'position_changed', function() {
				pos = panorama.getPosition();
				marker.setPosition(pos);
				parent.document.getElementById('sv_latiText').value = pos.lat()+"\u00B0";
				parent.document.getElementById('sv_longText').value = pos.lng()+"\u00B0";
			});

			//======================================================================================
			// Now add the ImageMapType overlay to the map
			//--------------------------------------------------------------------------------------
			map.overlayMapTypes.push(null);

			//======================================================================================
			// Now create the StreetView ImageMap
			//--------------------------------------------------------------------------------------
			var street = new google.maps.ImageMapType({
				getTileUrl: function(coord, zoom) {
					var X = coord.x % (1 << zoom);  // wrap
					return 'http://cbk0.google.com/cbk?output=overlay&zoom=' + zoom + '&x=' + X + '&y=' + coord.y + '&cb_client=api';
				},
				tileSize: new google.maps.Size(256, 256),
				isPng: true
			});

			//======================================================================================
			//  Add the Street view Image Map
			//--------------------------------------------------------------------------------------
			map.overlayMapTypes.setAt(1, street);
			//==============================================================================================
		}

		function toggleStreetView() {
			var toggle = panorama.getVisible();
			if (toggle == false) {
				panorama.setVisible(true);
				document.myForm.butt1.value = "<?php echo WT_I18N::translate('Google Maps™'); ?>";
			} else {
				panorama.setVisible(false);
				document.myForm.butt1.value = "<?php echo WT_I18N::translate('Google Street View™'); ?>";
			}
		}

		// Onload handler to fire off the app.
		google.maps.event.addDomListener(window, 'load', initialize);

		</script>
		</head>
			<body>
				<style>
					#mapCanvas {
						width: 520px;
						height: 350px;
						margin: -10px auto 0;
						border:1px solid black;
					}
					#infoPanel {
						display: none;
						margin: 5px auto 0;
					}
					#infoPanel div {
						display: none;
						margin-bottom: 5px;
						background: #ffffff;
					}
					div {
						text-align: center;
					}
				</style>

				<div id="toggle">
					<form name="myForm" title="myForm">
						<?php
						echo '<input id="butt1" name ="butt1" type="button" value="', WT_I18N::translate('Google Maps™'), '" onclick="toggleStreetView();"></input>';
						echo '<input id="butt2" name ="butt2" type="button" value="', WT_I18N::translate('Reset'), '" onclick="initialize();"></input>';
						?>
					</form>
				</div>

				<div id="mapCanvas">
				</div>

				<div id="infoPanel">
					<div id="markerStatus"><em>Click and drag the marker.</em></div>
					<div id="info" ></div>
					<div id="address"></div>
				</div>
			</body>
		</html>
		<?php
	}
}
