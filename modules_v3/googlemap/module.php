<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
// $Id$

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

define('WT_GM_SCRIPT', 'https://maps.google.com/maps/api/js?v=3.2&amp;sensor=false&amp;language='.WT_LOCALE);

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
	// Extend WT_Module
	public function getTitle() {
		return /* I18N: The name of a module.  Google Maps™ is a trademark.  Do not translate it? http://en.wikipedia.org/wiki/Google_maps */ WT_I18N::translate('Google Maps™');
	}

	// Extend WT_Module
	public function getDescription() {
		return /* I18N: Description of the "Google Maps™" module */ WT_I18N::translate('Show the location of places and events using the Google Maps™ mapping service.');
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
			$this->pedigree_map();
			break;
		case 'admin_placecheck':
			$this->admin_placecheck();
			break;
		case 'admin_places':
		case 'places_edit':
			// TODO: these files should be methods in this class
			require_once WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';
			require_once WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
			require WT_ROOT.WT_MODULES_DIR.$this->getName().'/'.$mod_action.'.php';
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
		require_once WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';
		require_once WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
		setup_map();
		return ob_get_clean();
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $WT_IMAGES, $controller, $GOOGLEMAP_XSIZE, $GOOGLEMAP_YSIZE;

		if ($this->checkMapData()) {
			ob_start();
			require_once WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';
			require_once WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
			echo '<link type="text/css" href ="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';
			echo '<table border="0" width="100%"><tr><td>';
			echo '<table width="100%" border="0" class="facts_table">';
			echo '<tr><td valign="top">';
			echo '<div id="googlemap_left">';
			echo '<img src="', $WT_IMAGES['hline'], '" width="', $GOOGLEMAP_XSIZE, '" height="3" alt="">';
			echo '<div id="map_pane" style="border: 1px solid gray; color: black; width: 100%; height: ', $GOOGLEMAP_YSIZE, 'px"></div>';
			if (WT_USER_IS_ADMIN) {
				echo '<table width="100%"><tr>';
				echo '<td width="40%" align="left">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
				echo '</td>';
				echo '<td width="35%" class="center">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_places">', WT_I18N::translate('Geographic data'), '</a>';
				echo '</td>';
				echo '<td width="25%" align="right">';
				echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_placecheck">', WT_I18N::translate('Place Check'),'</a>';
				echo '</td>';
				echo '</tr></table>';
			}
			echo '</div>';
			echo '</td>';
			echo '<td valign="top" width="30%">';
			echo '<div id="map_content">';
			$famids = array();
			$families = $controller->record->getSpouseFamilies();
			foreach ($families as $family) {
				$famids[] = $family->getXref();
			}
			$controller->record->add_family_facts(false);
			build_indiv_map($controller->record->getIndiFacts(), $famids);
			echo '</div>';
			echo '</td>';
			echo '</tr></table>';
			// start
			echo '<img src="', $WT_IMAGES['spacer'], '" id="marker6" width="1" height="1" alt="">';
			// end
			echo '</td></tr></table>';
			return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
		} else {
			$html='<table class="facts_table">';
			$html.='<tr><td colspan="2" class="facts_value">'.WT_I18N::translate('No map data for this person');
			$html.='</td></tr>';
			if (WT_USER_IS_ADMIN) {
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

		return !$SEARCH_SPIDER && (array_key_exists('googlemap', WT_Module::getActiveModules()) || WT_USER_IS_ADMIN);
	}

	// Implement WT_Module_Tab
	public function isGrayedOut() {
		return false;
	}
	// Implement WT_Module_Tab
	public function getJSCallback() {
		if ($this->checkMapData()) {
			$out=
			'if (jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title")=="'.$this->getName().'") {loadMap();}';
		} else {
			$out='';
		}
		return $out;
	}

	private function config() {
		require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
		require WT_ROOT.'includes/functions/functions_edit.php';

		$action=safe_REQUEST($_REQUEST, 'action');

		$controller=new WT_Controller_Base();
		$controller
			->requireAdminLogin()
			->setPageTitle(WT_I18N::translate('Google Maps™'))
			->pageHeader()
			->addInlineJavascript('jQuery("#tabs").tabs();');


		if ($action=='update') {
			set_module_setting('googlemap', 'GM_MAP_TYPE',          $_POST['NEW_GM_MAP_TYPE']);
			set_module_setting('googlemap', 'GM_USE_STREETVIEW',    $_POST['NEW_GM_USE_STREETVIEW']);
			set_module_setting('googlemap', 'GM_MIN_ZOOM',          $_POST['NEW_GM_MIN_ZOOM']);
			set_module_setting('googlemap', 'GM_MAX_ZOOM',          $_POST['NEW_GM_MAX_ZOOM']);
			set_module_setting('googlemap', 'GM_XSIZE',             $_POST['NEW_GM_XSIZE']);
			set_module_setting('googlemap', 'GM_YSIZE',             $_POST['NEW_GM_YSIZE']);
			set_module_setting('googlemap', 'GM_PRECISION_0',       $_POST['NEW_GM_PRECISION_0']);
			set_module_setting('googlemap', 'GM_PRECISION_1',       $_POST['NEW_GM_PRECISION_1']);
			set_module_setting('googlemap', 'GM_PRECISION_2',       $_POST['NEW_GM_PRECISION_2']);
			set_module_setting('googlemap', 'GM_PRECISION_3',       $_POST['NEW_GM_PRECISION_3']);
			set_module_setting('googlemap', 'GM_PRECISION_4',       $_POST['NEW_GM_PRECISION_4']);
			set_module_setting('googlemap', 'GM_PRECISION_5',       $_POST['NEW_GM_PRECISION_5']);
			set_module_setting('googlemap', 'GM_DEFAULT_TOP_VALUE', $_POST['NEW_GM_DEFAULT_TOP_LEVEL']);
			set_module_setting('googlemap', 'GM_COORD',             $_POST['NEW_GM_COORD']);
			set_module_setting('googlemap', 'GM_PLACE_HIERARCHY',   $_POST['NEW_GM_PLACE_HIERARCHY']);
			set_module_setting('googlemap', 'GM_PH_XSIZE',          $_POST['NEW_GM_PH_XSIZE']);
			set_module_setting('googlemap', 'GM_PH_YSIZE',          $_POST['NEW_GM_PH_YSIZE']);
			set_module_setting('googlemap', 'GM_PH_MARKER',         $_POST['NEW_GM_PH_MARKER']);
			set_module_setting('googlemap', 'GM_DISP_SHORT_PLACE',  $_POST['NEW_GM_DISP_SHORT_PLACE']);
			set_module_setting('googlemap', 'GM_PH_WHEEL',          $_POST['NEW_GM_PH_WHEEL']);
			set_module_setting('googlemap', 'GM_PH_CONTROLS',       $_POST['NEW_GM_PH_CONTROLS']);
			set_module_setting('googlemap', 'GM_DISP_COUNT',        $_POST['NEW_GM_DISP_COUNT']);

			for ($i=1; $i<=9; $i++) {
				set_module_setting('googlemap', 'GM_PREFIX_'.$i,  $_POST['NEW_GM_PREFIX_'.$i]);
				set_module_setting('googlemap', 'GM_POSTFIX_'.$i, $_POST['NEW_GM_POSTFIX_'.$i]);
			}

			AddToLog('Googlemap config updated', 'config');
			// read the config file again, to set the vars
			require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
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
						<?php echo WT_I18N::translate('Place Check'); ?>
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
									<option value="ROADMAP" <?php if ($GOOGLEMAP_MAP_TYPE=="ROADMAP") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Map'); ?></option>
									<option value="SATELLITE" <?php if ($GOOGLEMAP_MAP_TYPE=="SATELLITE") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Satellite'); ?></option>
									<option value="HYBRID" <?php if ($GOOGLEMAP_MAP_TYPE=="HYBRID") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Hybrid'); ?></option>
									<option value="TERRAIN" <?php if ($GOOGLEMAP_MAP_TYPE=="TERRAIN") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Terrain'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Google Street View™'); ?></th>
							<td><?php echo radio_buttons('NEW_GM_USE_STREETVIEW', array(false=>WT_I18N::translate('hide'),true=>WT_I18N::translate('show')), get_module_setting('googlemap', 'GM_USE_STREETVIEW')); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Size of map (in pixels)'); ?></th>
							<td>
								<?php echo WT_I18N::translate('Width'); ?>
								<input type="text" name="NEW_GM_XSIZE" value="<?php echo $GOOGLEMAP_XSIZE; ?>" size="10">
								<?php echo WT_I18N::translate('Height'); ?>
								<input type="text" name="NEW_GM_YSIZE" value="<?php echo $GOOGLEMAP_YSIZE; ?>" size="10">
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Zoom factor of map'), help_link('GOOGLEMAP_MAP_ZOOM','googlemap'); ?></th>
							<td>
								<?php echo WT_I18N::translate('minimum'); ?>: <select name="NEW_GM_MIN_ZOOM">
								<?php for ($j=1; $j < 15; $j++) { ?>
								<option value="<?php echo $j, "\""; if ($GOOGLEMAP_MIN_ZOOM==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
								<?php } ?>
								</select>
								<?php echo WT_I18N::translate('maximum'); ?>: <select name="NEW_GM_MAX_ZOOM">
								<?php for ($j=1; $j < 21; $j++) { ?>
								<option value="<?php echo $j, "\""; if ($GOOGLEMAP_MAX_ZOOM==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
								<?php } ?>
								</select>
							</td>
						</tr>
					</table>
				</div>

				<div id="gm_advanced">
					<table class="gm_edit_config">
						<tr>
							<th colspan="2"><?php echo WT_I18N::translate('Precision of the latitude and longitude'), help_link('GOOGLEMAP_PRECISION','googlemap'); ?></th>
							<td>
								<table>
									<tr>
										<td><?php echo WT_I18N::translate('Country'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_0">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_0==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr>
										<td><?php echo WT_I18N::translate('State'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_1">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_1==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr>
										<td><?php echo WT_I18N::translate('City'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_2">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_2==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr><td><?php echo WT_I18N::translate('Neighborhood'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_3">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_3==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr><td><?php echo WT_I18N::translate('House'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_4">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_4==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
									<tr><td><?php echo WT_I18N::translate('Max'); ?>&nbsp;&nbsp;</td>
										<td><select name="NEW_GM_PRECISION_5">
											<?php for ($j=0; $j < 10; $j++) { ?>
											<option value="<?php echo $j; ?>"<?php if ($GOOGLEMAP_PRECISION_5==$j) echo " selected=\"selected\""; echo ">", $j; ?></option>
											<?php } ?>
											</select>&nbsp;&nbsp;<?php echo WT_I18N::translate('digits'); ?>
										</td>
									</tr>
								</table>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<th colspan="2"><?php echo WT_I18N::translate('Default value for top-level'), help_link('GM_DEFAULT_LEVEL_0','googlemap'); ?></th>
							<td><input type="text" name="NEW_GM_DEFAULT_TOP_LEVEL" value="<?php echo $GM_DEFAULT_TOP_VALUE; ?>" size="20"></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
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
							<td><input type="text" size="30" name="NEW_GM_PREFIX_<?php echo $level; ?>" value="<?php echo $GM_PREFIX[$level]; ?>"></td>
							<td><input type="text" size="30" name="NEW_GM_POSTFIX_<?php echo $level; ?>" value="<?php echo $GM_POSTFIX[$level]; ?>"></td>
						</tr>
						<?php } ?>
					</table>
				</div>

				<div id="gm_ph">
					<table class="gm_edit_config">
						<tr>
							<th><?php echo WT_I18N::translate('Use Google Maps™ for the place hierarchy'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_PLACE_HIERARCHY', get_module_setting('googlemap', 'GM_PLACE_HIERARCHY')); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Size of map (in pixels)'); ?></th>
							<td>
								<?php echo WT_I18N::translate('Width'); ?>
								<input type="text" name="NEW_GM_PH_XSIZE" value="<?php echo $GOOGLEMAP_PH_XSIZE; ?>" size="10">
								<?php echo WT_I18N::translate('Height'); ?>
								<input type="text" name="NEW_GM_PH_YSIZE" value="<?php echo $GOOGLEMAP_PH_YSIZE; ?>" size="10">
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Type of place markers in Place Hierarchy'); ?></th>
							<td>
								<select name="NEW_GM_PH_MARKER">
									<option value="G_DEFAULT_ICON" <?php if ($GOOGLEMAP_PH_MARKER=="G_DEFAULT_ICON") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Standard'); ?></option>
									<option value="G_FLAG" <?php if ($GOOGLEMAP_PH_MARKER=="G_FLAG") echo "selected=\"selected\""; ?>><?php echo WT_I18N::translate('Flag'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Display short placenames'), help_link('GM_DISP_SHORT_PLACE','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_DISP_SHORT_PLACE', $GM_DISP_SHORT_PLACE); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Use mouse wheel for zoom'), help_link('GOOGLEMAP_PH_WHEEL','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_PH_WHEEL', $GOOGLEMAP_PH_WHEEL); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Display indis and families count'), help_link('GM_DISP_COUNT','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_DISP_COUNT', $GM_DISP_COUNT); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Display Map Coordinates'), help_link('GOOGLEMAP_COORD','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_COORD', $GOOGLEMAP_COORD); ?></td>
						</tr>
						<tr>
							<th><?php echo WT_I18N::translate('Hide map controls'), help_link('GOOGLEMAP_PH_CONTROLS','googlemap'); ?></th>
							<td><?php echo edit_field_yes_no('NEW_GM_PH_CONTROLS', $GOOGLEMAP_PH_CONTROLS); ?></td>
						</tr>
					</table>
				</div>
			</div>
			<p>
				<input type="submit" value="<?php echo WT_I18N::translate('Save'); ?>">
			</p>
		</form>
		<?php
	}

	private function flags() {
		require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
		require WT_ROOT.'includes/functions/functions_edit.php';

		$controller=new WT_Controller_Simple();
		$controller
				->setPageTitle(WT_I18N::translate('Select flag'))
				->pageHeader();

		$countries=WT_Stats::get_all_countries();
		$action=safe_REQUEST($_REQUEST, 'action');

		if (isset($_REQUEST['countrySelected'])) $countrySelected = $_REQUEST['countrySelected'];
		if (!isset($countrySelected)) $countrySelected='Countries';
		if (isset($_REQUEST['stateSelected'])) $stateSelected = $_REQUEST['stateSelected'];
		if (!isset($stateSelected)) $stateSelected='States';

		$country = array();
		$rep = opendir(WT_ROOT.WT_MODULES_DIR.'googlemap/places/flags/');
		while ($file = readdir($rep)) {
			if (stristr($file, '.png')) {
				$country[] = substr($file, 0, strlen($file)-4);
			}
		}
		closedir($rep);
		sort($country);

		if ($countrySelected == 'Countries') {
			$flags = $country;
		}
		else {
			$flags = array();
			$rep = opendir(WT_ROOT.WT_MODULES_DIR.'googlemap/places/'.$countrySelected.'/flags/');
			while ($file = readdir($rep)) {
				if (stristr($file, '.png')) {
					$flags[] = substr($file, 0, strlen($file)-4);
				}
			}
			closedir($rep);
			sort($flags);
		}
		$flags_s = array();
		if ($stateSelected != 'States' && is_dir(WT_ROOT.WT_MODULES_DIR.'googlemap/places/'.$countrySelected.'/flags/'.$stateSelected.'/')) {
			$rep = opendir(WT_ROOT.WT_MODULES_DIR.'googlemap/places/'.$countrySelected.'/flags/'.$stateSelected.'/');
			while ($file = readdir($rep)) {
				if (stristr($file, '.png')) {
					$flags_s[] = substr($file, 0, strlen($file)-4);
				}
			}
			closedir($rep);
			sort($flags_s);
		}

		if ($action == 'ChangeFlag') {
		?>
			<script>
		<?php if ($_POST['selcountry'] == 'Countries') { ?>
					window.opener.document.editplaces.icon.value = 'places/flags/<?php echo $flags[$_POST['FLAGS']]; ?>.png';
					window.opener.document.getElementById('flagsDiv').innerHTML = "<img src=\"<?php echo WT_STATIC_URL, WT_MODULES_DIR; ?>googlemap/places/flags/<?php echo $country[$_POST['FLAGS']]; ?>.png\">&nbsp;&nbsp;<a href=\"#\" onclick=\"change_icon();return false;\"><?php echo WT_I18N::translate('Change flag'); ?></a>&nbsp;&nbsp;<a href=\"#\" onclick=\"remove_icon();return false;\"><?php echo WT_I18N::translate('Remove flag'); ?></a>";
		<?php } else if ($_POST['selstate'] != "States"){ ?>
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
		}
		else {
		?>
		<script>
			function enableButtons() {
				document.flags.save1.disabled = '';
				document.flags.save2.disabled = '';
			}

			function selectCountry() {
				if (document.flags.COUNTRYSELECT.value == 'Countries') {
					window.location="module.php?mod=googlemap&mod_action=flags";
				}
				else if (document.flags.STATESELECT.value != 'States') {
					window.location="module.php?mod=googlemap&mod_action=flags&countrySelected=" + document.flags.COUNTRYSELECT.value + "&stateSelected=" + document.flags.STATESELECT.value;
				}
				else {
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
						$countryList[$country[$i]] = $countries[$country[$i]];
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
		<form method="post" id="flags" name="flags" action="module.php?mod=googlemap&amp;mod_action=flags&amp;countrySelected=<?php echo $countrySelected; ?>&amp;stateSelected=<?php echo $stateSelected; ?>">
			<input type="hidden" name="action" value="ChangeFlag">
			<input type="hidden" name="selcountry" value="<?php echo $countrySelected; ?>">
			<input type="hidden" name="selstate" value="<?php echo $stateSelected; ?>">
			<input id="savebutton" name="save1" type="submit" disabled="true" value="<?php echo WT_I18N::translate('Save'); ?>"><br>
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
						$tempstr = '<td><input type="radio" dir="ltr" name="FLAGS" value="'.$i.'" onchange="enableButtons();"><img src="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/places/flags/'.$flags[$i].'.png" alt="'.$flags[$i].'"  title="';
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
						echo '<td><input type="radio" dir="ltr" name="FLAGS" value="', $i, '" onchange="enableButtons();"><img src="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/places/', $countrySelected, '/flags/', $flags[$i], '.png">&nbsp;&nbsp;', $flags[$i], '</input></td>';
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
								if ($stateSelected == $state_key) echo ' selected="selected" ';
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
						echo '<td><input type="radio" dir="ltr" name="FLAGS" value="', $i, '" onchange="enableButtons();"><img src="', WT_STATIC_URL.WT_MODULES_DIR, 'googlemap/places/', $countrySelected, '/flags/', $stateSelected, '/', $flags_s[$i], '.png">&nbsp;&nbsp;', $flags_s[$i], '</input></td>';
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
			<input id="savebutton" name="save2" type="submit" disabled="true" value="<?php echo WT_I18N::translate('Save'); ?>"><br>
		</form>
		<?php
		echo '<div class="center"><a href="#" onclick="closePopupAndReloadParent();">', WT_I18N::translate('Close Window'), '</a></div><br>';
	}

	private function pedigree_map() {
		global $controller, $PEDIGREE_GENERATIONS, $MAX_PEDIGREE_GENERATIONS;

		require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
		require_once WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';

		// Default is show for both of these.
		$hideflags = safe_GET('hideflags');
		$hidelines = safe_GET('hidelines');

		$controller=new WT_Controller_Pedigree();

		// Default of 5
		$clustersize = 5;
		if (!empty($_REQUEST['clustersize'])) {
			if ($_REQUEST['clustersize'] == '3')
				$clustersize = 3;
			else if ($_REQUEST['clustersize'] == '1')
				$clustersize = 1;
		}

		// Start of internal configuration variables
		// Limit this to match available number of icons.
		// 8 generations equals 255 individuals
		$MAX_PEDIGREE_GENERATIONS = min($MAX_PEDIGREE_GENERATIONS, 8);

		// End of internal configuration variables
		$controller
			->setPageTitle(/* I18N: %s is a person's name */ WT_I18N::translate('Pedigree map of %s', $controller->getPersonName()))
			->pageHeader()
			->addExternalJavascript(WT_STATIC_URL.'js/autocomplete.js')
			->addInLineJavascript('var pastefield; function paste_id(value) {pastefield.value=value;}');

		echo '<link type="text/css" href ="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';
		echo '<div><table><tr><td valign="middle">';
		echo '<h2>', $controller->getPageTitle(), '</h2>';

		// -- print the form to change the number of displayed generations
		?>
		</td><td width="50px">&nbsp;</td><td>
			  <form name="people" method="get" action="module.php?ged=<?php echo WT_GEDURL; ?>&amp;mod=googlemap&amp;mod_action=pedigree_map">
				<input type="hidden" name="mod" value="googlemap">
				<input type="hidden" name="mod_action" value="pedigree_map">
				<table class="pedigree_table" width="555">
					<tr>
						<td class="descriptionbox wrap">
							<?php echo WT_I18N::translate('Individual'); ?>
						</td>
						<td class="descriptionbox wrap">
							<?php echo WT_I18N::translate('Generations'); ?>
						</td>
						<td class="descriptionbox wrap">
							<?php echo WT_I18N::translate('Cluster size'), help_link('PEDIGREE_MAP_clustersize','googlemap'); ?>
						</td>
						<td class="descriptionbox wrap">
							<?php
							echo WT_I18N::translate('Hide flags'), help_link('PEDIGREE_MAP_hideflags','googlemap');
							?>
						</td>
						<td class="descriptionbox wrap">
							<?php
							echo WT_I18N::translate('Hide lines'), help_link('PEDIGREE_MAP_hidelines','googlemap');
							?>
						</td>
					</tr>
					<tr>
						<td class="optionbox">
							<input class="pedigree_form" type="text" id="rootid" name="rootid" size="3" value="<?php echo $controller->root->getXref(); ?>">
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
							<select name="clustersize">
							<?php
								for ($p=1; $p<6; $p = $p+2) {
									echo '<option value="', $p, '" ';
									if ($p == $clustersize) {
										echo 'selected="selected"';
									}
									echo '>', $p, '</option>';
								}
							?>
							</select>
						</td>
						<td class="optionbox">
							<?php
							echo '<input name="hideflags" type="checkbox"';
							if ($hideflags) {
								echo ' checked="checked"';
							}
							echo '>';
							?>
						</td>
						<td class="optionbox">
							<?php
							echo '<input name="hidelines" type="checkbox"';
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
			</td></tr>
		</table>
		<!-- end of form -->

		<!-- count records by type -->
		<?php
		$curgen=1;
		$priv=0;
		$count=0;
		$miscount=0;
		$missing = '';

		for ($i=0; $i<($controller->treesize); $i++) {
			// -- check to see if we have moved to the next generation
			if ($i+1 >= pow(2, $curgen)) {$curgen++;}
			$person = WT_Person::getInstance($controller->treeid[$i]);
			if (!empty($person)) {
				$pid = $controller->treeid[$i];
				$name = $person->getFullName();
				if ($name == WT_I18N::translate('Private')) $priv++;
				$place = $person->getBirthPlace();
				if (empty($place)) {
					$latlongval[$i] = NULL;
				} else {
					$latlongval[$i] = get_lati_long_placelocation($person->getBirthPlace());
					if ($latlongval[$i] != NULL && $latlongval[$i]['lati']=='0' && $latlongval[$i]['long']=='0') {
						$latlongval[$i] = NULL;
					}
				}
				if ($latlongval[$i] != NULL) {
					$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]['lati']);
					$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]['long']);
					if (($lat[$i] != NULL) && ($lon[$i] != NULL)) {
						$count++;
					}
					else { // The place is in the table but has empty values
						if (!empty($name)) {
							if (!empty($missing)) $missing .= ', ';
							$addlist = '<a href="'.$person->getHtmlUrl().'">'. $name . '</a>';
							$missing .= $addlist;
							$miscount++;
						}
					}
				}
				else { // There was no place, or not listed in the map table
					if (!empty($name)) {
						if (!empty($missing)) $missing .= ', ';
						$addlist = '<a href="'.$person->getHtmlUrl().'">'. $name . '</a>';
						$missing .= $addlist;
						$miscount++;
					}
				}
			}
		}
		//<!-- end of count records by type -->
		//<!-- start of map display -->
		echo '<table class="tabs_table" cellspacing="0" cellpadding="0" border="0" width="100%">';
		echo '<tr>';
		echo '<td valign="top">';
		echo '<div id="pm_map" style="border: 1px solid gray; height: ', $GOOGLEMAP_YSIZE, 'px; font-size: 0.9em;';
		echo '"><i class="icon-loading-large"></i></div>';
		if (WT_USER_IS_ADMIN) {
			echo '<table width="100%">';
			echo '<tr><td align="left">';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_config">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
			echo '</td>';
			echo '<td align="center">';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_places">', WT_I18N::translate('Geographic data'), '</a>';
			echo '</td>';
			echo '<td align="right">';
			echo '<a href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">', WT_I18N::translate('Place Check'), '</a>';
			echo '</td></tr>';
			echo '</table>';
		}
		echo '</td><td width="15px">&nbsp;</td>';
		echo '<td width="310px" valign="top">';
		echo '<div id="side_bar" style="width:300px; font-size:0.9em; overflow:auto; overflow-x:hidden; overflow-y:auto; height:', $GOOGLEMAP_YSIZE, 'px;"></div></td>';
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
			$miss=$total-$count-$priv;
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
		echo '</div>';
		?>
		<!-- end of map display -->
		<!-- Start of map scripts -->
		<?php
		echo '<script src="', WT_GM_SCRIPT, '"></script>';
		$controller->addInlineJavascript($this->pedigree_map_js($hideflags, $hidelines, $clustersize));
	}

	private function pedigree_map_js($hideflags, $hidelines, $clustersize) {
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
		gicons["1"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon1.png")
		gicons["1"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["2"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2.png")
		gicons["2"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["2L"]		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["2L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["2R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["2R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["2Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["2Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon2Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);
		gicons["3"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3.png")
		gicons["3"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["3L"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["3L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["3R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["3R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["3Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["3Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon3Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);
		gicons["4"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4.png")
		gicons["4"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["4L"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["4L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["4R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["4R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["4Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["4Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon4Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);
		gicons["5"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5.png")
		gicons["5"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["5L"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["5L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["5R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["5R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["5Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["5Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon5Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);
		gicons["6"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6.png")
		gicons["6"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["6L"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["6L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["6R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["6R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["6Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["6Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon6Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);
		gicons["7"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7.png")
		gicons["7"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["7L"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["7L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["7R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["7R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["7Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["7Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon7Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);
		gicons["8"]			= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8.png")
		gicons["8"].shadow 	= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow50.png",
									new google.maps.Size(37, 34),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(10, 34)	// Shadow anchor is base of image
								);
		gicons["8L"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8L.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(28, 28)	// Image anchor
								);
		gicons["8L"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-left-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(32, 27)	// Shadow anchor is base of image
								);
		gicons["8R"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8R.png",
									new google.maps.Size(32, 32),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(4, 28)	// Image anchor
								);
		gicons["8R"].shadow = 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/shadow-right-large.png",
									new google.maps.Size(49, 32),	// Shadow size
									new google.maps.Point(0, 0),	// Shadow origin
									new google.maps.Point(15, 27)	// Shadow anchor is base of image
								);
		gicons["8Ls"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8Ls.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(22, 22)	// Image anchor
								);
		gicons["8Rs"] 		= 	new google.maps.MarkerImage(WT_STATIC_URL+WT_MODULES_DIR+"googlemap/images/icon8Rs.png",
									new google.maps.Size(24, 24),	// Image size
									new google.maps.Point(0, 0),	// Image origin
									new google.maps.Point(2, 22)	// Image anchor
								);'.
		// / A function to create the marker and set up the event window
		'function createMarker(point, name, html, mhtml, icontype) {
			// alert(i+". "+name+", "+icontype);
			var contentString = "<div id=\'iwcontent_edit\'>"+mhtml+"<\/div>";'.
			//create a marker with the requested icon
			'var marker = new google.maps.Marker({
				icon: 		gicons[icontype],
				shadow: 	gicons[icontype].shadow,
				map: 		pm_map,
				position: 	point,
				zIndex: 	0
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
			'side_bar_html += "<br><div id=\'"+linkid+"\'><a href=\'#\' onclick=\'myclick(" + i + ")\'>" + html +"</a><br></div>";
			i++;
			return marker;
		};'.
		// create the map
		'var myOptions = {
			zoom: 6,
			center: new google.maps.LatLng(0, 0),
			mapTypeId: google.maps.MapTypeId.TERRAIN,					// ROADMAP, SATELLITE, HYBRID, TERRAIN
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU 	// DEFAULT, DROPDOWN_MENU, HORIZONTAL_BAR
			},
			navigationControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT,		// BOTTOM, BOTTOM_LEFT, LEFT, TOP, etc
				style: google.maps.NavigationControlStyle.SMALL			// ANDROID, DEFAULT, SMALL, ZOOM_PAN
			},
			streetViewControl: false,									// Show Pegman or not
			scrollwheel: true
		};
		var pm_map = new google.maps.Map(document.getElementById("pm_map"), myOptions);
		google.maps.event.addListener(pm_map, "maptypechanged", function() {
			map_type.refresh();
		});
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
		$curgen=1;
		$priv=0;
		$count=0;
		$event = '<img src="'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/sq1.png" width="10" height="10">'.
			 '<strong>&nbsp;'.WT_I18N::translate('Root').':&nbsp;</strong>';
		$colored_line = array('1'=>'#FF0000','2'=>'#0000FF','3'=>'#00FF00',
						'4'=>'#FFFF00','5'=>'#00FFFF','6'=>'#FF00FF',
						'7'=>'#C0C0FF','8'=>'#808000');

		for ($i=0; $i<($controller->treesize); $i++) {
			// moved up to grab the sex of the individuals
			$person = WT_Person::getInstance($controller->treeid[$i]);
			if (!empty($person)) {
				$pid = $controller->treeid[$i];
				$indirec = $person->getGedcomRecord();
				$sex = $person->getSex();
				$bplace = trim($person->getBirthPlace());
				$bdate = $person->getBirthDate();
				$name = $person->getFullName();

				// -- check to see if we have moved to the next generation
				if ($i+1 >= pow(2, $curgen)) {
					$curgen++;
				}
				$relationship=get_relationship_name(get_relationship($controller->root->getXref(), $pid, false, 0, true));
				if (empty($relationship)) $relationship=WT_I18N::translate('self');
				$event = '<img src=\"'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/sq'.$curgen.'.png\" width=\"10\" height=\"10\">'.
					 '<strong>&nbsp;'.$relationship.':&nbsp;</strong>';
				// add thumbnail image
				$image = '';
				if ($SHOW_HIGHLIGHT_IMAGES) {
					$object = find_highlighted_object($pid, WT_GED_ID, $indirec);
					if (!empty($object)) {
						$mediaobject=WT_Media::getInstance($object['mid']);
						$image=$mediaobject->displayMedia(array('display_type'=>'googlemap'));
					} else {
						$sex=$person->getSex();
						$image=display_silhouette(array('sex'=>$sex,'display_type'=>'googlemap')); // may return ''
					}
				}
				// end of add image

				$dataleft  = $image . $event . addslashes($name);
				$datamid   = " <span><a href='".$person->getHtmlUrl()."' id='alturl' title='" . WT_I18N::translate('Individual information') . "'>";
				$datamid .= '('.WT_I18N::translate('View Person').')';
				$datamid  .= '</a></span>';
				$dataright = '<br><strong>'. WT_I18N::translate('Birth:') . '&nbsp;</strong>' .
						addslashes($bdate->Display(false)).'<br>'.$bplace;

				$latlongval[$i] = get_lati_long_placelocation($person->getBirthPlace());
				if ($latlongval[$i] != NULL) {
					$lat[$i] = str_replace(array('N', 'S', ','), array('', '-', '.'), $latlongval[$i]['lati']);
					$lon[$i] = str_replace(array('E', 'W', ','), array('', '-', '.'), $latlongval[$i]['long']);
					if (!($lat[$i] == NULL && $lon[$i] == NULL) && !($lat[$i] == 0 && $lon[$i] == 0)) {
						if ((!$hideflags) && ($latlongval[$i]['icon'] != NULL)) {
							$flags[$i] = $latlongval[$i]['icon'];
							$ffile = strrchr($latlongval[$i]['icon'], '/');
							$ffile = substr($ffile,1, strpos($ffile, '.')-1);
							if (empty($flags[$ffile])) {
								$flags[$ffile] = $i; // Only generate the flag once
								if (($lat[$i] != NULL) && ($lon[$i] != NULL)) {
									$js.= 'var point = new google.maps.LatLng(' . $lat[$i] . ',' . $lon[$i]. ');';
									$js.= 'var Marker1_0_flag = new google.maps.MarkerImage();';
									$js.= 'Marker1_0_flag.image = "'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/'.$flags[$i].'";';
									$js.= 'Marker1_0_flag.shadow = "'.WT_STATIC_URL.WT_MODULES_DIR.'googlemap/images/flag_shadow.png";';
									$js.= 'Marker1_0_flag.iconSize = new google.maps.Size(25, 15);';
									$js.= 'Marker1_0_flag.shadowSize = new google.maps.Size(35, 45);';
									$js.= 'Marker1_0_flag.iconAnchor = new google.maps.Point(1, 45);';
									//BH	$js.= 'Marker1_0_flag.infoWindowAnchor = new google.maps.Point(5, 1);';
									$js.= 'var Marker1_0 = new google.maps.LatLng(point, {icon:Marker1_0_flag});';
									//BH  $js.= 'pm_map.addOverlay(Marker1_0);';
								}
							}
						}
						$marker_number = $curgen;
						$dups=0;
						for ($k=0; $k<$i; $k++) {
							if ($latlongval[$i] == $latlongval[$k]) {
								if ($clustersize == 1) {
									$lon[$i] = $lon[$i]+0.0025;
									$lat[$i] = $lat[$i]+0.0025;
								} else {
									$dups++;
									switch($dups) {
										case 1: $marker_number = $curgen . 'L'; break;
										case 2: $marker_number = $curgen . 'R'; break;
										case 3: if ($clustersize==5) {
											$marker_number = $curgen . 'Ls'; break;
											}
										case 4: if ($clustersize==5) {
											$marker_number = $curgen . 'Rs'; break;
											}
										case 5: //adjust position where markers have same coodinates
										default: $marker_number = $curgen;
											$lon[$i] = $lon[$i]+0.0025;
											$lat[$i] = $lat[$i]+0.0025;
											break;
									}
								}
							}
						}
						$js.= 'var point = new google.maps.LatLng('.$lat[$i].','.$lon[$i].');';
						$js.= "var marker = createMarker(point, \"".addslashes($name)."\",\n\t\"<div>".$dataleft.$datamid.$dataright."</div>\", \"";
						$js.= "<div class='iwstyle'>";
						$js.= "<a href='module.php?ged=".WT_GEDURL."&amp;mod=googlemap&amp;mod_action=pedigree_map&amp;rootid={$pid}&amp;PEDIGREE_GENERATIONS={$PEDIGREE_GENERATIONS}";
						if ($hideflags) $js.= '&amp;hideflags=1';
						if ($hidelines) $js.= '&amp;hidelines=1';
						if ($clustersize != 5) $js.= '&amp;clustersize='.$clustersize; // ignoring the default of 5
						$js.= "' title='".WT_I18N::translate('Pedigree map')."'>".$dataleft."</a>".$datamid.$dataright."</div>\", \"".$marker_number."\");";
						//BH $js.= "pm_map.addOverlay(marker);\n";
						// Construct the polygon lines
						if (!$hidelines) {
							$to_child = (intval(($i-1)/2)); // Draw a line from parent to child
							if (isset($lat[$to_child]) && isset($lon[$to_child])) {
								$js.='
								var linecolor;
								var plines;
								var lines = [new google.maps.LatLng('.$lat[$i].','.$lon[$i].'),
									new google.maps.LatLng('.$lat[$to_child].','.$lon[$to_child].')];
								linecolor = "'.$colored_line[$curgen].'";
								plines = new google.maps.Polygon({
									paths: lines,
									strokeColor: linecolor,
									// strokeColor: '.$colored_line[$curgen].',
									strokeOpacity: 0.8,
									strokeWeight: 3,
									fillColor: "#FF0000",
									//fillOpacity: 0.35
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
				$latlongval[$i] = NULL;
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

	private function admin_placecheck() {
		require WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';
		require_once WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';
		$action   =safe_POST     ('action'                                              );
		$gedcom_id=safe_POST     ('gedcom_id', array_keys(get_all_gedcoms()), WT_GED_ID );
		$country  =safe_POST     ('country',   WT_REGEX_UNSAFE,              ''         );
		if (!$country) {
			// allow placelist to link directly to a specific country/state
			$country=safe_GET('country', WT_REGEX_UNSAFE, 'XYZ');
		}
		$state=safe_POST('state', WT_REGEX_UNSAFE, '');
		if (!$state) {
			$state=safe_GET('state', WT_REGEX_UNSAFE, 'XYZ');
		}

		$controller=new WT_Controller_Base();
		$controller
			->requireAdminLogin()
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
					<a href="module.php?mod=googlemap&amp;mod_action=admin_places">
						<?php echo WT_I18N::translate('Geographic data'); ?>
					</a>
				</th>
				<th>
					<a class="current" href="module.php?mod=googlemap&amp;mod_action=admin_placecheck">
						<?php echo WT_I18N::translate('Place Check'); ?>
					</a>
				</th>
			</tr>
		</table>

		<?php

		//Start of User Defined options
		echo '<table id="gm_check_outer">';
		echo '<form method="post" name="placecheck" action="module.php?mod=googlemap&amp;mod_action=admin_placecheck">';
		echo '<tr valign="top">';
		echo '<td>';
		echo '<table class="gm_check_top" align="left">';
		echo '<tr><th colspan="2">', WT_I18N::translate('PlaceCheck List Options'), '</th></tr>';
		//Option box to select gedcom
		echo '<tr><td>', WT_I18N::translate('Family tree'), '</td>';
		echo '<td><select name="gedcom_id">';
		foreach (get_all_gedcoms() as $ged_id=>$gedcom) {
			echo '<option value="', $ged_id, '"', $ged_id==$gedcom_id?' selected="selected"':'', '>', get_gedcom_setting($ged_id, 'title'), '</option>';
		}
		echo '</select></td></tr>';
		//Option box to select Country within Gedcom
		echo '<tr><td>', WT_I18N::translate('Country'), '</td>';
		echo '<td><select name="country">';
		echo '<option value="XYZ" selected="selected">', /* I18N: first/default option in a drop-down listbox */ WT_I18N::translate('&lt;select&gt;'), '</option>';
		echo '<option value="XYZ">', WT_I18N::translate('All'), '</option>';
		$rows=
			WT_DB::prepare("SELECT pl_id, pl_place FROM `##placelocation` WHERE pl_level=0 ORDER BY pl_place")
			->fetchAssoc();
		foreach ($rows as $id=>$place) {
			echo '<option value="', $place, '"';
			if ($place==$country) {
				echo ' selected="selected"';
				$par_id=$id;
			}
			echo '>', $place, '</option>';
		}
		echo '</select></td></tr>';

		//Option box to select level 2 place within the selected Country
		if ($country!='XYZ') {
			echo '<tr><td>', /* I18N: Part of a country, state/region/county */ WT_I18N::translate('Subdivision'), '</td>';
			echo '<td><select name="state">';
			echo '<option value="XYZ" selected="selected">', WT_I18N::translate('&lt;select&gt;'), '</option>';
			echo '<option value="XYZ">', WT_I18N::translate('All'), '</option>';
			$places=
				WT_DB::prepare("SELECT pl_place FROM `##placelocation` WHERE pl_parent_id=? ORDER BY pl_place")
				->execute(array($par_id))
				->fetchOneColumn();
			foreach ($places as $place) {
				echo '<option value="', $place, '"', $place==$state?' selected="selected"':'', '>', $place, '</option>';
			}
			echo '</select></td></tr>';
		}
		echo '</table>';
		echo '</td>';
		//Show Filter table
		if (!isset ($_POST['matching'])) {$matching=false;} else {$matching=true;}
		echo '<td>';
		echo '<table class="gm_check_top"  align="center">';
		echo '<tr><th colspan="2">';
		echo WT_I18N::translate('List filtering options');
		echo '</th></tr><tr><td>';
		echo WT_I18N::translate('Include fully matched places: ');
		echo '</td><td><input type="checkbox" name="matching" value="active"';
		if ($matching) {
			echo ' checked="checked"';
		}
		echo '></td></tr>';
		echo '</table>';
		echo '</td>';
		echo '<td>';
		echo '<input type="submit" value="', WT_I18N::translate('Show'), '"><input type="hidden" name="action" value="go">';
		echo '</td>';
		echo '</tr>';
		echo '</form>';
		echo '</table>';
		echo '<hr>';

		switch ($action) {
		case 'go':
			//Identify gedcom file
			echo '<div id="gm_check_title"><span>', htmlspecialchars(get_gedcom_setting($gedcom_id, 'title')), '</span></div>';
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
			$place_parts=array();
			usort($place_list, "utf8_strcasecmp");
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
				window.open('module.php?mod=googlemap&mod_action=places_edit&action=update&placeid='+placeid, '_blank', indx_window_specs);
				return false;
			}

			function add_place_location(placeid) {
				window.open('module.php?mod=googlemap&mod_action=places_edit&action=add&placeid='+placeid, '_blank', indx_window_specs);
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
				echo '<th>', WT_Gedcom_Tag::getLabel('PLAC'), '</th><th>', WT_I18N::translate('Latitude'), '</th><th>', WT_I18N::translate('Longitude'), '</th></td>';
				$cols++;
			}
			echo '</tr>';
			$countrows=0;
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
				$y=0;
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
				while ($z<$parts) {
					if ($levels[$z]==' ' || $levels[$z]=='')
						$levels[$z]="unknown";// GoogleMap module uses "unknown" while GEDCOM uses , ,

					$levels[$z]=rtrim(ltrim($levels[$z]));

					$placelist=create_possible_place_names($levels[$z], $z+1); // add the necessary prefix/postfix values to the place name
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
					} else if ($row['pl_lati']!='') {
						$lati[$z]="<td>".$row['pl_lati']."</td>";
					} else {
						$lati[$z]="<td class='error' align='center'><strong>X</strong></td>";$matched[$x]++;
					}
					if ($row['pl_long']=='0') {
						$long[$z]="<td class='error'><strong>".$row['pl_long']."</strong></td>";
					} else if ($row['pl_long']!='') {
						$long[$z]="<td>".$row['pl_long']."</td>";
					} else {
						$long[$z]="<td class='error' align='center'><strong>X</strong></td>";$matched[$x]++;
					}
					$level++;
					$mapstr3=$mapstr3."&amp;parent[".$z."]=".addslashes($row['pl_placerequested']);
					$mapstr4=$mapstr4."&amp;parent[".$z."]=".addslashes(rtrim(ltrim($levels[$z])));
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
			echo '<div class="gm_check_top accepted">', WT_I18N::translate('This will list all the places from the selected GEDCOM file. By default this will NOT INCLUDE places that are fully matched between the GEDCOM file and the GoogleMap tables'), '</div>';
			break;
		}
	}

	private function checkMapData() {
		global $controller;
		$xrefs="'".$controller->record->getXref()."'";
		$families = $controller->record->getSpouseFamilies();
		foreach ($families as $family) {
			$xrefs.=", '".$family->getXref()."'";
		}
		return WT_DB::prepare("SELECT COUNT(*) AS tot FROM `##placelinks` WHERE pl_gid IN (".$xrefs.") AND pl_file=?")
			->execute(array(WT_GED_ID))
			->fetchOne();
	}
}
