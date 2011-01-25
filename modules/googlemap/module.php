<?php
// Classes and libraries for module system
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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

class googlemap_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return WT_I18N::translate('Googlemap');
	}

	// Extend WT_Module
	public function getDescription() {
		return WT_I18N::translate('Adds a tab to the individual page which maps the events of an individual and their close relatives on a Google map.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
		case 'admin_editconfig':
		case 'flags':
		case 'pedigree_map':
		case 'admin_placecheck':
		case 'admin_places':
		case 'places_edit':
			// TODO: these files should be methods in this class
			require_once WT_ROOT.'modules/googlemap/googlemap.php';
			require_once WT_ROOT.'modules/googlemap/defaultconfig.php';
			require WT_ROOT.'modules/'.$this->getName().'/'.$mod_action.'.php';
			break;
		}
	}

	// Implement WT_Module_Config
	public function getConfigLink() {
		return 'module.php?mod='.$this->getName().'&mod_action=admin_config';
	}

	// Implement WT_Module_Tab
	public function defaultTabOrder() {
		return 80;
	}

	// Implement WT_Module_Tab
	public function getPreLoadContent() {
		ob_start();
		require_once WT_ROOT.'modules/googlemap/googlemap.php';
		require_once WT_ROOT.'modules/googlemap/defaultconfig.php';
		setup_map();
		return ob_get_clean();
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $SEARCH_SPIDER, $WT_IMAGES;
		global $GOOGLEMAP_ENABLED, $GOOGLEMAP_API_KEY, $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM, $GEDCOM;
		global $GOOGLEMAP_XSIZE, $GOOGLEMAP_YSIZE, $SHOW_LIVING_NAMES;
		global $TEXT_DIRECTION, $GM_DEFAULT_TOP_VALUE, $GOOGLEMAP_COORD, $GOOGLEMAP_PH_CONTROLS;
		global $GM_MARKER_COLOR, $GM_MARKER_SIZE, $GM_PREFIX, $GM_POSTFIX, $GM_PRE_POST_MODE;

		ob_start();
		require_once WT_ROOT.'modules/googlemap/googlemap.php';
		require_once WT_ROOT.'modules/googlemap/defaultconfig.php';
		?>
<table border="0" width="100%">
	<tr>
		<td><?php
		if (!array_key_exists('googlemap', WT_Module::getActiveModules())) {
			echo "<table class=\"facts_table\">";
			echo "<tr><td id=\"no_tab8\" colspan=\"2\" class=\"facts_value\">".WT_I18N::translate('GoogleMap module disabled')."</td></tr>";
			if (WT_USER_IS_ADMIN) {
				echo "<tr><td align=\"center\" colspan=\"2\">";
				echo "<a href=\"module.php?mod=".$this->getName()."&amp;mod_action=admin_editconfig\">".WT_I18N::translate('Manage GoogleMap configuration')."</a>";
				echo "</td>";
				echo "</tr>";
			}
			echo "</table><br />";
			?> <script type="text/javascript">
			<!--
				function ResizeMap () {}
				function SetMarkersAndBounds () {}
			//-->
			</script> <?php
		} else {
			if (!$this->controller->indi->canDisplayName()) {
				echo "<table class=\"facts_table\">";
				echo "<tr><td class=\"facts_value\">";
				print_privacy_error();
				echo "</td></tr>";
				echo "</table><br />";
				echo "<script type=\"text/javascript\">";
				echo "function ResizeMap () {}</script>";
			} else {
					echo "<table width=\"100%\" border=\"0\" class=\"facts_table\">";
					echo "<tr><td valign=\"top\">";
					echo "<div id=\"googlemap_left\">";
					echo "<img src=\"images/hline.gif\" width=\"".$GOOGLEMAP_XSIZE."\" height=\"0\" alt=\"\" />";
					echo "<div id=\"map_pane\" style=\"border: 1px solid gray; color:black; width: 100%; height: ".$GOOGLEMAP_YSIZE."px\"></div>";
					if (WT_USER_IS_ADMIN) {
						echo "<table width=\"100%\"><tr>";
						echo "<td width=\"33%\" align=\"left\">";
						echo "<a href=\"module.php?mod=".$this->getName()."&amp;mod_action=admin_editconfig\">".WT_I18N::translate('Manage GoogleMap configuration')."</a>";
						echo "</td>";
						echo "<td width=\"34%\" align=\"center\">";
						echo "<a href=\"module.php?mod=".$this->getName()."&amp;mod_action=admin_places\">".WT_I18N::translate('Edit geographic place locations')."</a>";
						echo "</td>";
						echo "<td width=\"33%\" align=\"right\">";
						echo "<a href=\"module.php?mod=".$this->getName()."&amp;mod_action=admin_placecheck\">".WT_I18N::translate('Place Check')."</a>";
						echo "</td>";
						echo "</tr></table>";
					}
					echo "</div>";
					echo "</td>";
					echo "<td valign=\"top\" width=\"30%\">";
					echo "<div id=\"map_content\">";
					$famids = array();
					$families = $this->controller->indi->getSpouseFamilies();
					foreach ($families as $family) {
						$famids[] = $family->getXref();
					}
					$this->controller->indi->add_family_facts(false);
					create_indiv_buttons();
					build_indiv_map($this->controller->indi->getIndiFacts(), $famids);
					echo "</div>";
					echo "</td>";
					echo "</tr></table>";

				
			}
		}
		// start
		echo "<img src=\"".$WT_IMAGES["spacer"]."\" id=\"marker6\" width=\"1\" height=\"1\" alt=\"\" />";
		// end
		?>
		</td>
	</tr>
</table>
		<?php
		return '<div id="'.$this->getName().'_content">'.ob_get_clean().'</div>';
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $GOOGLEMAP_ENABLED, $SEARCH_SPIDER;

		return !$SEARCH_SPIDER && (array_key_exists('googlemap', WT_Module::getActiveModules()) || WT_USER_IS_ADMIN);
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		global $GOOGLEMAP_PH_CONTROLS;
		$out=
			'if (jQuery("#tabs li:eq("+jQuery("#tabs").tabs("option", "selected")+") a").attr("title")=="'.$this->getName().'") {'.
			' loadMap();';
		if ($GOOGLEMAP_PH_CONTROLS) {
			$out.=
				' GEvent.addListener(map,"mouseout", function() { map.hideControls(); });'.
				' GEvent.addListener(map,"mouseover",function() { map.showControls(); });'.
				' GEvent.trigger    (map,"mouseout");';
		}
		$out.=
			' map.setMapType(GOOGLEMAP_MAP_TYPE);'.
			' SetMarkersAndBounds();'.
			' ResizeMap();'.
			'}';
		return $out;
	}
}
