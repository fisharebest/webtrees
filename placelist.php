<?php
/**
 * webtrees: online genealogy
 * Copyright (C) 2017 webtrees development team
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

use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\FunctionsPrintLists;

require 'includes/session.php';

/** @global Tree $WT_TREE */
global $WT_TREE;

$controller = new PageController;

$action  = Filter::get('action', 'find|show', 'find');
$display = Filter::get('display', 'hierarchy|list', 'hierarchy');
$parent  = Filter::getArray('parent');

$level = count($parent);

if ($display == 'hierarchy') {
	if ($level) {
		$controller->setPageTitle(I18N::translate('Place hierarchy') . ' - <span dir="auto">' . Filter::escapeHtml($parent[$level - 1]) . '</span>');
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
	$numfound    = count($list_places);

	$divisor = $numfound > 20 ? 3 : 2;

	if ($numfound === 0) {
		echo '<b>', I18N::translate('No results found.'), '</b><br>';
	} else {
		$columns = array_chunk($list_places, ceil($numfound / $divisor));

		$html = '<table class="list_table"><thead>';
		$html .= '<tr><th class="list_label" colspan="' . $divisor . '">';
		$html .= '<i class="icon-place"></i> ' . I18N::translate('Place list');
		$html .= '</th></tr></thead>';
		$html .= '<tbody><tr>';
		foreach ($columns as $column) {
			$html .= '<td class="list_value_wrap"><ul>';
			foreach ($column as $item) {
				$html .= '<li><a href="' . $item->getURL() . '">' . $item->getReverseName() . '</a></li>';
			}
			$html .= '</ul></td>';
		}
		$html .= '</tr></tbody></table>';
		echo $html;
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
	if ($numfound === 0) {
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

	if ($gm_module && $gm_module->getPreference('GM_PLACE_HIERARCHY')) {
		$linklevels  = '';
		$place_names = [];
		for ($j = 0; $j < $level; $j++) {
			$linklevels .= '&amp;parent[' . $j . ']=' . rawurlencode($parent[$j]);
		}

		$gm_module->createMap();
	} elseif (Module::getModuleByName('places_assistant')) {
		// Places Assistant is a custom/add-on module that was once part of the core code.
		\PlacesAssistantModule::display_map($level, $parent);
	}

	if ($numfound > 0) {
		if ($numfound > 20) {
			$divisor = 3;
		} elseif ($numfound > 4) {
			$divisor = 2;
		} else {
			$divisor = 1;
		}

		$columns = array_chunk($child_places, ceil($numfound / $divisor));
		$html    = '<table id="place_hierarchy" class="list_table"><thead><tr><th class="list_label" colspan="' . $divisor . '">';
		$html .= '<i class="icon-place"></i> ';
		if ($place_id) {
			$html .= I18N::translate('Places in %s', $place->getPlaceName());
		} else {
			$html .= I18N::translate('Place hierarchy');
		}
		$html .= '</th></tr></thead>';
		$html .= '<tbody><tr>';
		foreach ($columns as $column) {
			$html .= '<td class="list_value"><ul>';
			foreach ($column as $item) {
				$html .= '<li><a href="' . $item->getURL() . '" class="list_item">' . $item->getPlaceName() . '</a></li>';
				if ($gm_module && $gm_module->getPreference('GM_PLACE_HIERARCHY')) {
					list($tmp)     = explode(', ', $item->getGedcomName(), 2);
					$place_names[] = $tmp;
				}
			}
			$html .= '</ul></td>';
		}
		$html .= '</tr></tbody>';
		if ($numfound > 0 && $action == 'find' && $place_id) {
			$html .= '<tfoot><tr><td class="list_label" colspan="' . $divisor . '">';
			$html .= I18N::translate('View all records found in this place');
			$html .= '</td></tr><tr><td class="list_value" colspan="' . $divisor . '" style="text-align: center;">';
			$html .= '<a href="' . $place->getURL() . '&amp;action=show" class="formField">' . $place->getPlaceName() . '</a>';
			$html .= '</td></tr></tfoot>';
		}
		$html .= '</table>';
		// -- echo the array
		echo $html;
	}
	if ($place_id && $action == 'show') {
		// -- array of names
		$myindilist = [];
		$myfamlist  = [];

		$positions =
			Database::prepare("SELECT DISTINCT pl_gid FROM `##placelinks` WHERE pl_p_id=? AND pl_file=?")
			->execute([$place_id, $WT_TREE->getTreeId()])
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

		?>
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active<?= empty($myindilist) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#individuals">
					<?= I18N::translate('Individuals') ?>
					<?= Bootstrap4::badgeCount($myindilist) ?>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link<?= empty($myfamlist) ? ' text-muted' : '' ?>" data-toggle="tab" role="tab" href="#families">
					<?= I18N::translate('Families') ?>
					<?= Bootstrap4::badgeCount($myfamlist) ?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane fade show active" role="tabpanel" id="individuals">
				<?php if (empty($myindilist)): ?>
					<p><?= I18N::translate('No results found.') ?></p>
				<?php else: ?>
					<?= FunctionsPrintLists::individualTable($myindilist) ?>
				<?php endif ?>
			</div>

			<div class="tab-pane fade" role="tabpanel" id="families">
				<?php if (empty($myfamlist)): ?>
					<p><?= I18N::translate('No results found.') ?></p>
				<?php else: ?>
					<?= FunctionsPrintLists::familyTable($myfamlist) ?>
				<?php endif ?>
			</div>
		</div>
		<?php
	}
	echo '<h4><a href="placelist.php?display=list">', I18N::translate('Show all places in a list'), '</a></h4>';

	if ($gm_module && $gm_module->getPreference('GM_PLACE_HIERARCHY')) {
		$gm_module->mapScripts($numfound, $level, $parent, $linklevels, $place_names);
	}
	break;
}

echo '</div>'; // <div id="place-hierarchy">
