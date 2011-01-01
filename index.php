<?php
// My Page page allows a logged in user the abilty
// to keep bookmarks, see a list of upcoming events, etc.
//
// webtrees: Web based Family History software
// Copyright (C) 2011 webtrees development team.
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
// @version $Id$

define('WT_SCRIPT_NAME', 'index.php');
require './includes/session.php';

// The only option for action is "ajax"
$action=safe_REQUEST($_REQUEST, 'action', 'ajax');

// The default view depends on whether we are logged in
$ctype=safe_REQUEST($_REQUEST, 'ctype', array('gedcom', 'user'), WT_USER_ID ? 'user' : 'gedcom');

// A request to see a user page, but not logged in?
if (!WT_USER_ID && $ctype=='user') {
	header('Location: '.WT_SERVER_NAME.WT_SCRIPT_PATH.'login.php?url='.rawurlencode('index.php?ctype=user'));
	exit;
}

//-- get the blocks list
if ($ctype=='user') {
	$blocks=get_user_blocks(WT_USER_ID);
} else {
	$blocks=get_gedcom_blocks(WT_GED_ID);
}

$all_blocks=WT_Module::getActiveBlocks();

// We generate individual blocks using AJAX
if ($action=='ajax') {
	header('Content-Type: text/html; charset=UTF-8');
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

// We have finished writing session data, so release the lock
Zend_Session::writeClose();

if ($ctype=='user') {
	$helpindex = 'mypage_portal';
	print_header(i18n::translate('My Page'));
} else {
	$helpindex = 'index_portal';
	print_header(get_gedcom_setting(WT_GED_ID, 'title'));
}

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}

// TODO: these should be moved to their respective module/block
echo WT_JS_START;
?>
	function refreshpage() {
		window.location = 'index.php?ctype=<?php echo $ctype; ?>';
	}
	function addnews(uname) {
		window.open('editnews.php?username='+uname, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	function editnews(news_id) {
		window.open('editnews.php?news_id='+news_id, '_blank', 'top=50,left=50,width=600,height=500,resizable=1,scrollbars=1');
	}
	var pastefield;
	function paste_id(value) {
		pastefield.value=value;
	}
<?php
echo WT_JS_END;
//-- start of main content section
if ($ctype=='user') {
	echo '<div align="center">';
	echo '<h1>', i18n::translate('My Page'), '</h1>';
	echo i18n::translate('My Page allows you to keep bookmarks of your favorite people, track upcoming events, and collaborate with other webtrees users.');
	echo '<br /><br /></div>';
}
echo '<script src="js/jquery/jquery.min.js" type="text/javascript"></script>';
echo '<script type="text/javascript">jQuery.noConflict();</script>';
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
			echo '<div id="block_', $block_id, '"><img src="images/loading.gif" alt="', htmlspecialchars(i18n::translate('Loading...')),  '"/></div>';
			echo WT_JS_START, "jQuery('#block_{$block_id}').load('index.php?ctype={$ctype}&action=ajax&block_id={$block_id}');", WT_JS_END;
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
			echo '<div id="block_', $block_id, '"><img src="images/loading.gif" alt="', htmlspecialchars(i18n::translate('Loading...')),  '"/></div>';
			echo WT_JS_START, "jQuery('#block_{$block_id}').load('index.php?ctype={$ctype}&action=ajax&block_id={$block_id}');", WT_JS_END;
		}
	}
	echo '</div>';
}

// Ensure there is always way to configure the blocks
if ($ctype=='user' && !in_array('user_welcome', $blocks['main']) && !in_array('user_welcome', $blocks['side'])) {
	echo '<div align="center">';
	echo "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=".rawurlencode(WT_USER_NAME)."&amp;ctype=user', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\">".i18n::translate('Customize My Page').'</a>';
	echo help_link('mypage_customize');
	echo '</div>';
}
if (WT_USER_IS_ADMIN && $ctype=='gedcom' && !in_array('gedcom_block', $blocks['main']) && !in_array('gedcom_block', $blocks['side'])) {
	echo '<div align="center">';
	echo "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=".WT_GEDURL."&amp;ctype=gedcom', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\">".i18n::translate('Customize this GEDCOM Home Page').'</a>';
	echo '</div>';
}

print_footer();
