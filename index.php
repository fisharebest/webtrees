<?php
// My page page allows a logged in user the abilty
// to keep bookmarks, see a list of upcoming events, etc.
//
// webtrees: Web based Family History software
// Copyright (C) 2012 webtrees development team.
//
// Derived from PhpGedView
// Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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

define('WT_SCRIPT_NAME', 'index.php');
require './includes/session.php';

// The only option for action is "ajax"
$action=safe_REQUEST($_REQUEST, 'action', 'ajax');

// The default view depends on whether we are logged in
$ctype=safe_REQUEST($_REQUEST, 'ctype', array('gedcom', 'user'), WT_USER_ID ? 'user' : 'gedcom');

//-- get the blocks list
if (WT_USER_ID && $ctype=='user') {
	$blocks=get_user_blocks(WT_USER_ID);
} else {
	$blocks=get_gedcom_blocks(WT_GED_ID);
}

$all_blocks=WT_Module::getActiveBlocks();

// We generate individual blocks using AJAX
if ($action=='ajax') {
	$controller=new WT_Controller_Ajax();
	$controller
		->pageHeader()
		->addExternalJavaScript(WT_JQUERY_URL)
		->addExternalJavaScript(WT_JQUERYUI_URL);

	// Check we're displaying an allowable block.
	$block_id=safe_GET('block_id');
	if (array_key_exists($block_id, $blocks['main'])) {
		$module_name=$blocks['main'][$block_id];
	} elseif (array_key_exists($block_id, $blocks['side'])) {
		$module_name=$blocks['side'][$block_id];
	} else {
		exit;
	}
	if (array_key_exists($module_name, $all_blocks)) {
		$class_name=$module_name.'_WT_Module';
		$module=new $class_name;
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

$controller=new WT_Controller_Base();
if ($ctype=='user') {
	$controller->requireMemberLogin();
}
$controller
	->setPageTitle($ctype=='user' ? WT_I18N::translate('My page') : get_gedcom_setting(WT_GED_ID, 'title'))
	->setMetaRobots('index,follow')
	->pageHeader()
	// By default jQuery modifies AJAX URLs to disable caching, causing JS libraries to be loaded many times.
	->addInlineJavaScript('jQuery.ajaxSetup({cache:true});');

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.WT_MODULES_DIR.'lightbox/functions/lb_call_js.php';
}

echo '<div id="home-page">';
if ($ctype=='user') {
	echo '<h1 class="center">', WT_I18N::translate('My page'), '</h1>';
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
			$controller->addInlineJavaScript(
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
			$controller->addInlineJavaScript(
				'jQuery("#block_'.$block_id.'").load("index.php?ctype='.$ctype.'&action=ajax&block_id='.$block_id.'");'
			);
		}
	}
	echo '</div>';
}

// Ensure there is always way to configure the blocks
if ($ctype=='user' && !in_array('user_welcome', $blocks['main']) && !in_array('user_welcome', $blocks['side'])) {
	echo '<div align="center">';
	echo "<a href=\"#\" onclick=\"window.open('index_edit.php?name=".rawurlencode(WT_USER_NAME)."&amp;ctype=user', '_blank', indx_window_specs);\">".WT_I18N::translate('Change the blocks on this page').'</a>';
	echo '</div>';
}
if (WT_USER_IS_ADMIN && $ctype=='gedcom' && !in_array('gedcom_block', $blocks['main']) && !in_array('gedcom_block', $blocks['side'])) {
	echo '<div align="center">';
	echo "<a href=\"#\" onclick=\"window.open('index_edit.php?name=".WT_GEDURL."&amp;ctype=gedcom', '_blank', indx_window_specs);\">".WT_I18N::translate('Change the blocks on this page').'</a>';
	echo '</div>';
}
echo '</div>'; // <div id="home-page">
