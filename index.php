<?php
// My page page allows a logged in user the abilty
// to keep bookmarks, see a list of upcoming events, etc.
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009 PGV Development Team.  All rights reserved.
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

// webtrees requires a modern version of PHP
// Note - maintaining this check requires that this file can be parsed by PHP5.2
if (version_compare(PHP_VERSION, '5.3.2', '<')) {
	// RFC2616 requires an absolute URL, but we don’t have it here.
	header('Location: site-php-version.php');
}

define('WT_SCRIPT_NAME', 'index.php');
require './includes/session.php';

// The only option for action is "ajax"
$action = WT_Filter::get('action');

// The default view depends on whether we are logged in
$ctype = WT_Filter::get('ctype', 'gedcom|user', WT_USER_ID ? 'user' : 'gedcom');

// Get the blocks list
if (WT_USER_ID && $ctype=='user') {
	$blocks = get_user_blocks(WT_USER_ID);
} else {
	$blocks = get_gedcom_blocks(WT_GED_ID);
}

$all_blocks = WT_Module::getActiveBlocks();

// The latest version is shown on the administration page.  This updates it every day.
// TODO: send an email notification to the admin when new versions are available.
fetch_latest_version();

// We generate individual blocks using AJAX
if ($action == 'ajax') {
	$controller = new WT_Controller_Ajax();
	$controller->pageHeader();

	// Check we’re displaying an allowable block.
	$block_id = WT_Filter::getInteger('block_id');
	if (array_key_exists($block_id, $blocks['main'])) {
		$module_name = $blocks['main'][$block_id];
	} elseif (array_key_exists($block_id, $blocks['side'])) {
		$module_name = $blocks['side'][$block_id];
	} else {
		exit;
	}
	if (array_key_exists($module_name, $all_blocks)) {
		$class_name = $module_name.'_WT_Module';
		$module = new $class_name;
		$module->getBlock($block_id);
	}
	if (WT_DEBUG) {
		echo execution_stats();
	}
	if (WT_DEBUG_SQL) {
		echo WT_DB::getQueryLog();
	}
	exit;
}

$controller=new WT_Controller_Page();
if ($ctype=='user') {
	$controller->requireMemberLogin();
}
$controller
	->setPageTitle($ctype=='user' ? WT_I18N::translate('My page') : WT_TREE_TITLE)
	->setMetaRobots('index,follow')
	->pageHeader()
	// By default jQuery modifies AJAX URLs to disable caching, causing JS libraries to be loaded many times.
	->addInlineJavascript('jQuery.ajaxSetup({cache:true});');

if ($ctype=='user') {
	echo '<div id="my-page">';
	echo '<h1 class="center">', WT_I18N::translate('My page'), '</h1>';
} else {
	echo '<div id="home-page">';
}	
if ($blocks['main']) {
	if ($blocks['side']) {
		echo '<div id="index_main_blocks">';
	} else {
		echo '<div id="index_full_blocks">';
	}
	foreach ($blocks['main'] as $block_id=>$module_name) {
		$class_name=$module_name.'_WT_Module';
		$module=new $class_name;
		if ($SEARCH_SPIDER || !$module->loadAjax()) {
			// Load the block directly
			$module->getBlock($block_id);
		} else {
			// Load the block asynchronously
			echo '<div id="block_', $block_id, '"><div class="loading-image">&nbsp;</div></div>';
			$controller->addInlineJavascript(
				'jQuery("#block_'.$block_id.'").load("index.php?ctype='.$ctype.'&action=ajax&block_id='.$block_id.'");'
			);
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
	foreach ($blocks['side'] as $block_id=>$module_name) {
		$class_name=$module_name.'_WT_Module';
		$module=new $class_name;
		if ($SEARCH_SPIDER || !$module->loadAjax()) {
			// Load the block directly
			$module->getBlock($block_id);
		} else {
			// Load the block asynchronously
			echo '<div id="block_', $block_id, '"><div class="loading-image">&nbsp;</div></div>';
			$controller->addInlineJavascript(
				'jQuery("#block_'.$block_id.'").load("index.php?ctype='.$ctype.'&action=ajax&block_id='.$block_id.'");'
			);
		}
	}
	echo '</div>';
}

// link for changing blocks
echo '<div id="link_change_blocks">';
	if ($ctype=='user') echo '<a href="index_edit.php?user_id='.WT_USER_ID.'" onclick="return modalDialog(\'index_edit.php?user_id='.WT_USER_ID.'\', \'', WT_I18N::translate('Change the blocks on this page'), '\');">', WT_I18N::translate('Change the blocks on this page'), '</a>';
	if (WT_USER_GEDCOM_ADMIN && $ctype=='gedcom') echo '<a href="index_edit.php?gedcom_id='.WT_GED_ID.'" onclick="return modalDialog(\'index_edit.php?gedcom_id='.WT_GED_ID.'\', \'', WT_I18N::translate('Change the blocks on this page'), '\');">', WT_I18N::translate('Change the blocks on this page'), '</a>';
	if ($SHOW_COUNTER) {echo '<span>'.WT_I18N::translate('hit count:').' '.$hitCount.'</span>';}
echo '</div>', // <div id="link_change_blocks">
	 '</div>'; // <div id="home-page">