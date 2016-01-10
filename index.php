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

use Fisharebest\Webtrees\Controller\AjaxController;
use Fisharebest\Webtrees\Controller\PageController;
use Fisharebest\Webtrees\Functions\Functions;
use Fisharebest\Webtrees\Functions\FunctionsDb;

define('WT_SCRIPT_NAME', 'index.php');
require './includes/session.php';

// The only option for action is "ajax"
$action = Filter::get('action');

// The default view depends on whether we are logged in
if (Auth::check()) {
	$ctype = Filter::get('ctype', 'gedcom|user', 'user');
} else {
	$ctype = 'gedcom';
}

// Get the blocks list
if ($ctype === 'user') {
	$blocks = FunctionsDb::getUserBlocks(Auth::id());
} else {
	$blocks = FunctionsDb::getTreeBlocks($WT_TREE->getTreeId());
}

$active_blocks = Module::getActiveBlocks($WT_TREE);

// The latest version is shown on the administration page.  This updates it every day.
Functions::fetchLatestVersion();

// We generate individual blocks using AJAX
if ($action === 'ajax') {
	$controller = new AjaxController;
	$controller->pageHeader();

	// Check we’re displaying an allowable block.
	$block_id = Filter::getInteger('block_id');
	if (array_key_exists($block_id, $blocks['main'])) {
		$module_name = $blocks['main'][$block_id];
	} elseif (array_key_exists($block_id, $blocks['side'])) {
		$module_name = $blocks['side'][$block_id];
	} else {
		return;
	}
	if (array_key_exists($module_name, $active_blocks)) {
		echo $active_blocks[$module_name]->getBlock($block_id);
	}

	return;
}

// Redirect search engines to the full URL
if (Filter::get('ctype') !== $ctype || Filter::get('ged') !== $WT_TREE->getName()) {
	header('Location: ' . WT_BASE_URL . 'index.php?ctype=' . $ctype . '&ged=' . $WT_TREE->getNameUrl());

	return;
}

$controller = new PageController;
if ($ctype === 'user') {
	$controller->restrictAccess(Auth::check());
}
$controller
	->setPageTitle($ctype === 'user' ? I18N::translate('My page') : $WT_TREE->getTitle())
	->setMetaRobots('index,follow')
	->pageHeader()
	// By default jQuery modifies AJAX URLs to disable caching, causing JS libraries to be loaded many times.
	->addInlineJavascript('jQuery.ajaxSetup({cache:true});');

if ($ctype === 'user') {
	echo '<div id="my-page">';
	echo '<h1 class="center">', I18N::translate('My page'), '</h1>';
} else {
	echo '<div id="home-page">';
}
if ($blocks['main']) {
	if ($blocks['side']) {
		echo '<div id="index_main_blocks">';
	} else {
		echo '<div id="index_full_blocks">';
	}
	foreach ($blocks['main'] as $block_id => $module_name) {
		if (array_key_exists($module_name, $active_blocks)) {
			if (Auth::isSearchEngine() || !$active_blocks[$module_name]->loadAjax()) {
				// Load the block directly
				echo $active_blocks[$module_name]->getBlock($block_id);
			} else {
				// Load the block asynchronously
				echo '<div id="block_', $block_id, '"><div class="loading-image">&nbsp;</div></div>';
				$controller->addInlineJavascript(
					'jQuery("#block_' . $block_id . '").load("index.php?ctype=' . $ctype . '&action=ajax&block_id=' . $block_id . '");'
				);
			}
		}
	}
	echo '</div>';
}
if ($blocks['side']) {
	if ($blocks['main']) {
		echo '<div id="index_small_blocks">';
	} else {
		echo '<div id="index_full_blocks">';
	}
	foreach ($blocks['side'] as $block_id => $module_name) {
		if (array_key_exists($module_name, $active_blocks)) {
			if (Auth::isSearchEngine() || !$active_blocks[$module_name]->loadAjax()) {
				// Load the block directly
				echo $active_blocks[$module_name]->getBlock($block_id);
			} else {
				// Load the block asynchronously
				echo '<div id="block_', $block_id, '"><div class="loading-image">&nbsp;</div></div>';
				$controller->addInlineJavascript(
					'jQuery("#block_' . $block_id . '").load("index.php?ctype=' . $ctype . '&action=ajax&block_id=' . $block_id . '");'
				);
			}
		}
	}
	echo '</div>';
}
echo '</div>';
