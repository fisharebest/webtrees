<?php
/**
 * Classes and libraries for module system
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2009 John Finlay
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package webtrees
 * @subpackage Modules
 * @version $Id$
 */

if (!defined('WT_WEBTREES')) {
	header('HTTP/1.0 403 Forbidden');
	exit;
}

require_once WT_ROOT.'includes/classes/class_module.php';
require_once WT_ROOT.'modules/googlemap/googlemap.php';

class googlemap_WT_Module extends WT_Module implements WT_Module_Config, WT_Module_Tab {
	// Extend WT_Module
	public function getTitle() {
		return i18n::translate('Googlemap');
	}

	// Extend WT_Module
	public function getDescription() {
		return i18n::translate('Adds a tab to the individual page which maps the events of an individual and their close relatives on a Google map.');
	}

	// Extend WT_Module
	public function modAction($mod_action) {
		switch($mod_action) {
		case 'admin_config':
		case 'editconfig':
		case 'flags':
		case 'pedigree_map':
		case 'placecheck':
		case 'places':
		case 'places_edit':
			// TODO: these files should be methods in this class
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
		setup_map();
		return ob_get_clean();
	}

	// Implement WT_Module_Tab
	public function canLoadAjax() {
		return true;
	}

	// Implement WT_Module_Tab
	public function getTabContent() {
		global $SEARCH_SPIDER, $SESSION_HIDE_GOOGLEMAP, $WT_IMAGE_DIR, $WT_IMAGES;
		global $TBLPREFIX;
		global $GOOGLEMAP_ENABLED, $GOOGLEMAP_API_KEY, $GOOGLEMAP_MAP_TYPE, $GOOGLEMAP_MIN_ZOOM, $GOOGLEMAP_MAX_ZOOM, $GEDCOM;
		global $GOOGLEMAP_XSIZE, $GOOGLEMAP_YSIZE, $SHOW_LIVING_NAMES;
		global $TEXT_DIRECTION, $GM_DEFAULT_TOP_VALUE, $GOOGLEMAP_COORD, $GOOGLEMAP_PH_CONTROLS;
		global $GM_MARKER_COLOR, $GM_MARKER_SIZE, $GM_PREFIX, $GM_POSTFIX, $GM_PRE_POST_MODE;

		ob_start();
		?>
<div id="gg_map_content">
<table border="0" width="100%">
	<tr>
		<td><?php 
		if (!$GOOGLEMAP_ENABLED) {
			print "<table class=\"facts_table\">\n";
			print "<tr><td id=\"no_tab8\" colspan=\"2\" class=\"facts_value\">".i18n::translate('GoogleMap module disabled')."</td></tr>\n";
			if (WT_USER_IS_ADMIN) {
				print "<tr><td align=\"center\" colspan=\"2\">\n";
				print "<a href=\"module.php?mod=googlemap&amp;mod_action=editconfig\">".i18n::translate('Manage GoogleMap configuration')."</a>";
				print "</td>";
				print "</tr>\n";
			}
			print "\n\t</table>\n<br />";
			?> <script language="JavaScript" type="text/javascript">
			<!--
				function ResizeMap () {}
				function SetMarkersAndBounds () {}
			//-->
			</script> <?php
		} else {
			$tNew = str_replace(array("&HIDE_GOOGLEMAP=true", "&HIDE_GOOGLEMAP=false", "action=ajax&module=googlemap&"), "", $_SERVER["REQUEST_URI"]);
			$tNew .= "&tab=googlemap";
			$tNew = str_replace("&", "&amp;", $tNew);
			if($SESSION_HIDE_GOOGLEMAP=="true") {
				print "&nbsp;&nbsp;&nbsp;<span class=\"font9\"><a href=\"".$tNew."&amp;HIDE_GOOGLEMAP=false\">";
				print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["plus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".i18n::translate('Activate')."\" title=\"".i18n::translate('Activate')."\" />";
				print " ".i18n::translate('Activate')."</a></span>\n";
			} else {
				print "&nbsp;&nbsp;&nbsp;<span class=\"font9\"><a href=\"" .$tNew."&amp;HIDE_GOOGLEMAP=true\">";
				print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["minus"]["other"]."\" border=\"0\" width=\"11\" height=\"11\" alt=\"".i18n::translate('Deactivate')."\" title=\"".i18n::translate('Deactivate')."\" />";
				print " ".i18n::translate('Deactivate')."</a></span>\n";
			}

			if (!$this->controller->indi->canDisplayName()) {
				print "\n\t<table class=\"facts_table\">";
				print "<tr><td class=\"facts_value\">";
				print_privacy_error();
				print "</td></tr>";
				print "\n\t</table>\n<br />";
				print "<script type=\"text/javascript\">\n";
				print "function ResizeMap ()\n{\n}\n</script>\n";
			} else {
				if($SESSION_HIDE_GOOGLEMAP=="false") {
					print "<table width=\"100%\" border=\"0\" class=\"facts_table\">\n";
					print "<tr><td valign=\"top\">\n";
					print "<div id=\"googlemap_left\">\n";
					print "<img src=\"images/hline.gif\" width=\"".$GOOGLEMAP_XSIZE."\" height=\"0\" alt=\"\" /><br/>";
					print "<div id=\"map_pane\" style=\"border: 1px solid gray; color:black; width: 100%; height: ".$GOOGLEMAP_YSIZE."px\"></div>\n";
					if (WT_USER_IS_ADMIN) {
						print "<table width=\"100%\"><tr>\n";
						print "<td width=\"33%\" align=\"left\">\n";
						print "<a href=\"module.php?mod=googlemap&amp;mod_action=editconfig\">".i18n::translate('Manage GoogleMap configuration')."</a>";
						print "</td>\n";
						print "<td width=\"33%\" align=\"center\">\n";
						print "<a href=\"module.php?mod=googlemap&amp;mod_action=places\">".i18n::translate('Edit geographic place locations')."</a>";
						print "</td>\n";
						print "<td width=\"33%\" align=\"right\">\n";
						print "<a href=\"module.php?mod=googlemap&amp;mod_action=placecheck\">".i18n::translate('Place Check')."</a>";
						print "</td>\n";
						print "</tr></table>\n";
					}
					print "</div>\n";
					print "</td>\n";
					print "<td valign=\"top\" width=\"30%\">\n";
					print "<div id=\"googlemap_content\">\n";
					//setup_map();

					$famids = array();
					$families = $this->controller->indi->getSpouseFamilies();
					foreach ($families as $famid=>$family) {
						$famids[] = $family->getXref();
					}
					$this->controller->indi->add_family_facts(false);
					create_indiv_buttons();
					build_indiv_map($this->controller->getIndiFacts(), $famids);
					print "</div>\n";
					print "</td>";
					print "</tr></table>\n";

				}
			}
		}
		// start
		print "<img src=\"".$WT_IMAGE_DIR."/".$WT_IMAGES["spacer"]["other"]."\" id=\"marker6\" width=\"1\" height=\"1\" alt=\"\" />";
		// end
		?>
		</td>
	</tr>
</table>
</div>
</div>
		<?php
		return ob_get_clean();
	}

	// Implement WT_Module_Tab
	public function hasTabContent() {
		global $GOOGLEMAP_ENABLED, $SEARCH_SPIDER;

		return !$SEARCH_SPIDER && ($GOOGLEMAP_ENABLED || WT_USER_IS_ADMIN);
	}

	// Implement WT_Module_Tab
	public function getJSCallback() {
		global $GOOGLEMAP_PH_CONTROLS;
		$out = "loadMap();\n";
		if ($GOOGLEMAP_PH_CONTROLS) {
			$out .= '// hide controls
					GEvent.addListener(map,"mouseout",function()
					{
						map.hideControls();
					});
					// show controls
					GEvent.addListener(map,"mouseover",function()
					{
						map.showControls();
					});
					GEvent.trigger(map,"mouseout");
					';

		}
		$out.='map.setMapType(GOOGLEMAP_MAP_TYPE);
				SetMarkersAndBounds();
				ResizeMap();
				';
		return $out;
	}
	
	// Implement WT_Module_Tab
	public function getJSCallbackAllTabs() {
		global $GOOGLEMAP_PH_CONTROLS;
		$out = "loadMap();\n";
		if ($GOOGLEMAP_PH_CONTROLS) {
			$out .= '// hide controls
					GEvent.addListener(map,"mouseout",function()
					{
						map.hideControls();
					});
					// show controls
					GEvent.addListener(map,"mouseover",function()
					{
						map.showControls();
					});
					GEvent.trigger(map,"mouseout");
					';

		}
		$out.='map.setMapType(GOOGLEMAP_MAP_TYPE);
				SetMarkersAndBounds();
				ResizeMap();
				';
		return $out;
	}
}
