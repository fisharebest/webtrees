<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2015 webtrees development team
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
namespace Fisharebest\Webtrees;

/**
 * Defined in session.php
 *
 * @global Tree $WT_TREE
 */
global $WT_TREE;

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

define('WT_SCRIPT_NAME', 'placelist.php');
require './includes/session.php';

$controller = new PageController;

$action  = Filter::get('action', 'find|show', 'find');
$display = Filter::get('display', 'hierarchy|list', 'hierarchy');
$parent  = Filter::getArray('parent');

$level = count($parent);

if ($display == 'hierarchy') {
	if ($level) {
		$controller->setPageTitle(I18N::translate('Place hierarchy') . ' - <span dir="auto">' . Filter::escapeHtml(end($parent)) . '</span>');
	} else {
		$controller->setPageTitle(I18N::translate('Place hierarchy'));
	}
} else {
	$controller->setPageTitle(I18N::translate('Place list'));
}

$controller->pageHeader();

echo '<div id="place-hierarchy">';

switch ($display) {
case 'list':
	echo '<h2>', $controller->getPageTitle(), '</h2>';
	$list_places = Place::allPlaces($WT_TREE);
	$num_places  = count($list_places);

	if ($num_places == 0) {
		echo '<b>', I18N::translate('No results found.'), '</b><br>';
	} else {
		echo '<table class="list_table">';
		echo '<tr><td class="list_label" ';
		echo ' colspan="', $num_places > 20 ? 3 : 2, '"><i class="icon-place"></i> ';
		echo I18N::translate('Place list');
		echo '</td></tr><tr><td class="list_value_wrap"><ul>';
		foreach ($list_places as $n => $list_place) {
			echo '<li><a href="', $list_place->getURL(), '">', $list_place->getReverseName(), '</a></li>';
			if ($num_places > 20) {
				if ($n == (int) ($num_places / 3)) {
					echo '</ul></td><td class="list_value_wrap"><ul>';
				}
				if ($n == (int) (($num_places / 3) * 2)) {
					echo '</ul></td><td class="list_value_wrap"><ul>';
				}
			} elseif ($n == (int) ($num_places / 2)) {
				echo '</ul></td><td class="list_value_wrap"><ul>';
			}
		}
		echo '</ul></td></tr>';
		echo '</table>';
	}
	echo '<h4><a href="placelist.php?display=hierarchy">', I18N::translate('Show places in hierarchy'), '</a></h4>';
	break;
case 'hierarchy':
	$gm_module = Module::getModuleByName('googlemap');

	// Find this place and its ID
	$place    = new Place(implode(', ', array_reverse($parent)), $WT_TREE);
	$place_id = $place->getPlaceId();

	$child_places = $place->getChildPlaces();

	$numfound = count($child_places);

	//-- if the number of places found is 0 then automatically redirect to search page
	if ($numfound == 0) {
		$action = 'show';
	}

	echo '<h2>', $controller->getPageTitle();
	// Breadcrumbs
	if ($place_id) {
		$parent_place = $place->getParentPlace();
		while ($parent_place->getPlaceId()) {
			echo ', <a href="', $parent_place->getURL(), '" dir="auto">', $parent_place->getPlaceName(), '</a>';
			$parent_place = $parent_place->getParentPlace();
		}
		echo ', <a href="', WT_SCRIPT_NAME, '">', I18N::translate('Top level'), '</a>';
	}
	echo '</h2>';

	if ($gm_module && $gm_module->getSetting('GM_PLACE_HIERARCHY')) {
		$linklevels  = '';
		$placelevels = '';
		$place_names = array();
		for ($j = 0; $j < $level; $j++) {
			$linklevels .= '&amp;parent[' . $j . ']=' . rawurlencode($parent[$j]);
			if ($parent[$j] == '') {
				$placelevels = ', ' . I18N::translate('unknown') . $placelevels;
			} else {
				$placelevels = ', ' . $parent[$j] . $placelevels;
			}
		}
		$gm_module->createMap($placelevels);
	} elseif (Module::getModuleByName('places_assistant')) {
		// Places Assistant is a custom/add-on module that was once part of the core code.
		\PlacesAssistantModule::display_map($level, $parent);
	}

	// -- echo the array
	foreach ($child_places as $n => $child_place) {
		if ($n == 0) {
			echo '<table id="place_hierarchy" class="list_table"><tr><td class="list_label" ';
			if ($numfound > 20) {
				echo 'colspan="3"';
			} elseif ($numfound > 4) {
				echo 'colspan="2"';
			}
			echo '><i class="icon-place"></i> ';
			if ($place_id) {
				echo /* I18N: %s is a country or region */ I18N::translate('Places in %s', $place->getPlaceName());
			} else {
				echo I18N::translate('Place hierarchy');
			}
			echo '</td></tr><tr><td class="list_value"><ul>';
		}

		echo '<li><a href="', $child_place->getURL(), '" class="list_item">', $child_place->getPlaceName(), '</a></li>';
		if ($gm_module && $gm_module->getSetting('GM_PLACE_HIERARCHY')) {
			list($tmp)       = explode(', ', $child_place->getGedcomName(), 2);
			$place_names[$n] = $tmp;
		}
		$n++;
		if ($numfound > 20) {
			if ($n == (int) ($numfound / 3)) {
				echo '</ul></td><td class="list_value"><ul>';
			}
			if ($n == (int) (($numfound / 3) * 2)) {
				echo '</ul></td><td class="list_value"><ul>';
			}
		} elseif ($numfound > 4 && $n == (int) ($numfound / 2)) {
			echo '</ul></td><td class="list_value"><ul>';
		}
	}
	if ($child_places) {
		echo '</ul></td></tr>';
		if ($action == 'find' && $place_id) {
			echo '<tr><td class="list_label" ';
			if ($numfound > 20) {
				echo 'colspan="3"';
			} elseif ($numfound > 4) {
				echo 'colspan="2"';
			}
			echo '>';
			echo I18N::translate('View all records found in this place');
			echo '</td></tr><tr><td class="list_value" ';
			if ($numfound > 20) {
				echo 'colspan="3"';
			} elseif ($numfound > 4) {
				echo 'colspan="2"';
			}
			echo ' style="text-align: center;">';
			echo '<a href="', $place->getURL(), '&amp;action=show" class="formField">', $place->getPlaceName(), '</a>';
			echo '</td></tr>';
		}
		echo '</table>';
	}
	echo '</td></tr></table>';
	if ($place_id && $action == 'show') {
		// -- array of names
		$myindilist = array();
		$myfamlist  = array();

		$positions =
			Database::prepare("SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id=? AND pl_file=?")
			->execute(array($place_id, $WT_TREE->getTreeId()))
			->fetchOneColumn();

		foreach ($positions as $position) {
			$record = GedcomRecord::getInstance($position, $WT_TREE);
			if ($record && $record->canShow()) {
				if ($record instanceof Individual) {
					$myindilist[] = $record;
				}
				if ($record instanceof Family) {
					$myfamlist[] = $record;
				}
			}
		}
		echo '<br>';

		//-- display results
		$controller
			->addInlineJavascript('jQuery("#places-tabs").tabs();')
			->addInlineJavascript('jQuery("#places-tabs").css("visibility", "visible");')
			->addInlineJavascript('jQuery(".loading-image").css("display", "none");');

		echo '<div class="loading-image">&nbsp;</div>';
		echo '<div id="places-tabs"><ul>';
		if ($myindilist) {
			echo '<li><a href="#places-indi"><span id="indisource">', I18N::translate('Individuals'), '</span></a></li>';
		}
		if ($myfamlist) {
			echo '<li><a href="#places-fam"><span id="famsource">', I18N::translate('Families'), '</span></a></li>';
		}
		echo '</ul>';
		if ($myindilist) {
			echo '<div id="places-indi">', FunctionsPrintLists::individualTable($myindilist), '</div>';
		}
		if ($myfamlist) {
			echo '<div id="places-fam">', FunctionsPrintLists::familyTable($myfamlist), '</div>';
		}
		if (!$myindilist && !$myfamlist) {
			echo '<div id="places-indi">', FunctionsPrintLists::individualTable(array()), '</div>';
		}
		echo '</div>'; // <div id="places-tabs">
	}
	echo '<h4><a href="placelist.php?display=list">', I18N::translate('Show all places in a list'), '</a></h4>';

	if ($gm_module && $gm_module->getSetting('GM_PLACE_HIERARCHY')) {
		echo '<link type="text/css" href="', WT_STATIC_URL, WT_MODULES_DIR, 'googlemap/css/wt_v3_googlemap.css" rel="stylesheet">';
		$gm_module->mapScripts($numfound, $level, $parent, $linklevels, $place_names);
	}
	break;
}

echo '</div>'; // <div id="place-hierarchy">

