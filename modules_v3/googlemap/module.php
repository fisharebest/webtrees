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
		case 'admin_editconfig':
		case 'flags':
		case 'pedigree_map':
		case 'admin_placecheck':
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
		return 'module.php?mod='.$this->getName().'&amp;mod_action=admin_editconfig';
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
		global $SEARCH_SPIDER, $WT_IMAGES, $controller;
		global $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM, $GEDCOM;
		global $GOOGLEMAP_XSIZE, $GOOGLEMAP_YSIZE, $SHOW_LIVING_NAMES;
		global $GM_DEFAULT_TOP_VALUE, $GOOGLEMAP_COORD, $GOOGLEMAP_PH_CONTROLS;
		global $GM_MARKER_COLOR, $GM_MARKER_SIZE, $GM_PREFIX, $GM_POSTFIX;

		ob_start();
		require_once WT_ROOT.WT_MODULES_DIR.'googlemap/googlemap.php';
		require_once WT_ROOT.WT_MODULES_DIR.'googlemap/defaultconfig.php';

		echo '<table border="0" width="100%"><tr><td>';
		echo '<table width="100%" border="0" class="facts_table">';
		echo '<tr><td valign="top">';
		echo '<div id="googlemap_left">';
		echo '<img src="', $WT_IMAGES['hline'], '" width="', $GOOGLEMAP_XSIZE, '" height="0" alt="">';
		echo '<div id="map_pane" style="border: 1px solid gray; color: black; width: 100%; height: ', $GOOGLEMAP_YSIZE, 'px"></div>';
		if (WT_USER_IS_ADMIN) {
			echo '<table width="100%"><tr>';
			echo '<td width="40%" align="left">';
			echo '<a href="module.php?mod=', $this->getName(), '&amp;mod_action=admin_editconfig">', WT_I18N::translate('Google Maps™ preferences'), '</a>';
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
		global $GOOGLEMAP_PH_CONTROLS;
		$out=
			'if (jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title")=="'.$this->getName().'") {'.
				'loadMap();'.				
			'}';
		return $out;
	}
}
