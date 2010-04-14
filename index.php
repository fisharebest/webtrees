<?php
/**
 * My Page page allows a logged in user the abilty
 * to keep bookmarks, see a list of upcoming events, etc.
 *
 * webtrees: Web based Family History software
 * Copyright (C) 2010 webtrees development team.
 *
 * Derived from PhpGedView
 * Copyright (C) 2002 to 2009  PGV Development Team.  All rights reserved.
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
 * @subpackage Display
 * @version $Id$
 */

define('WT_SCRIPT_NAME', 'index.php');
require './includes/session.php';
require_once WT_ROOT.'includes/index_cache.php';
require_once WT_ROOT.'includes/functions/functions_print_facts.php';  //--needed for the expand url function in some of the blocks

if (isset($_REQUEST['action'])) $action = $_REQUEST['action'];
if (isset($_REQUEST['ctype'])) $ctype = $_REQUEST['ctype'];
$message_id = safe_GET('message_id');
$gid = safe_POST('gid');
$favnote = safe_POST('favnote');
$favtype = safe_POST('favtype');
$url = safe_POST('url', WT_REGEX_URL);
$favtitle = safe_POST('favtitle');
$fv_id = safe_GET('fv_id');
$news_id = safe_GET('news_id');

/**
 * Block definition array
 *
 * The following block definition array defines the
 * blocks that can be used to customize the portals
 * their names and the function to call them
 * 'name' is the name of the block in the lists
 * 'descr' is a description of this block
 * 'type' the options are 'user' or 'gedcom' or 'both'
 * - The type determines which lists the block is available in.
 * - Leaving the type undefined allows it to be on both the user and gedcom portal
 * @global $WT_BLOCKS
 */

/**
 * Load List of Blocks in blocks directory (unchanged)
 */
$WT_BLOCKS = array();
$d = opendir('blocks');
while (false !== ($f=readdir($d))) {
	if (preg_match('/\.php$/', $f)>0) {
		require_once WT_ROOT.'blocks/'.$f;
	}
}
closedir($d);
/**
 * End loading list of Blocks in blocks directory
 *
 * Load List of Blocks in modules/XX/blocks directories
 */
if (file_exists(WT_ROOT.'modules')) {
	$dir=dir(WT_ROOT.'modules');
	while (false !== ($entry = $dir->read())) {
		if (!strstr($entry,'.') && ($entry!='..') && ($entry!='CVS')&& !strstr($entry, 'svn')) {
			$path = WT_ROOT.'modules/' . $entry.'/blocks';
			if (is_readable($path)) {
				$d=dir($path);
				while (false !== ($entry = $d->read())) {
					if (($entry!='.') && ($entry!='..') && ($entry!='CVS')&& !strstr($entry, 'svn')&&(preg_match('/\.php$/', $entry)>0)) {
						$p=$path.'/'.$entry;
						require_once $p;
					}
				}
			}
		}
	}
}
/**
 * End loading list of Blocks in modules/XX/blocks directories
*/

$time = client_time();

if (!isset($action)) $action='';

// Visitors should see any links to a user page, but they may be
// following a link after an inactivity logout.
if (!WT_USER_ID) {
	if (!empty($ctype) && $ctype=='user') {
		header('Location: login.php&url=index.php?ctype=user');
		exit;
	} else {
		$ctype = 'gedcom';
	}
}

if (empty($ctype)) {
	if ($PAGE_AFTER_LOGIN == 'welcome') $ctype = 'gedcom';
	else $ctype = 'user';
}

if (WT_USER_ID) {
	//-- add favorites action
	if ($action=='addfav' && !empty($gid)) {
		$gid = strtoupper($gid);
		$indirec = find_gedcom_record($gid, WT_GED_ID);
		$ct = preg_match('/0 @(.*)@ (.*)/', $indirec, $match);
		if ($indirec && $ct>0) {
			$favorite = array();
			if (empty($favtype)) {
				if ($ctype=='user') $favtype = 'user';
				else $favtype = 'gedcom';
			}
			if ($favtype=='gedcom') {
				$favtype = $GEDCOM;
				$_SESSION['clearcache'] = true;
			}
			else $favtype=WT_USER_NAME;
			$favorite['username'] = $favtype;
			$favorite['gid'] = $gid;
			$favorite['type'] = trim($match[2]);
			$favorite['file'] = $GEDCOM;
			$favorite['url'] = '';
			$favorite['note'] = $favnote;
			$favorite['title'] = '';
			addFavorite($favorite);
		}
	}
	if (($action=='addfav')&&(!empty($url))) {
		if (empty($favtitle)) $favtitle = $url;
		$favorite = array();
		if (!isset($favtype)) {
			if ($ctype=='user') $favtype = 'user';
			else $favtype = 'gedcom';
		}
		if ($favtype=='gedcom') {
			$favtype = $GEDCOM;
			$_SESSION['clearcache'] = true;
		}
		else $favtype=WT_USER_NAME;
		$favorite['username'] = $favtype;
		$favorite['gid'] = '';
		$favorite['type'] = 'URL';
		$favorite['file'] = $GEDCOM;
		$favorite['url'] = $url;
		$favorite['note'] = $favnote;
		$favorite['title'] = $favtitle;
		addFavorite($favorite);
	}
	if (($action=='deletefav')&&(!empty($fv_id))) {
		deleteFavorite($fv_id);
		if ($ctype=='gedcom') $_SESSION['clearcache'] = true;
	}
	else if ($action=='deletemessage') {
		if (isset($message_id)) {
			if (!is_array($message_id)) deleteMessage($message_id);
			else {
				foreach($message_id as $indexval => $mid) {
					if (isset($mid)) deleteMessage($mid);
				}
			}
			if ($ctype=='gedcom') $_SESSION['clearcache'] = true;
		}
	}
	else if (($action=='deletenews')&&(isset($news_id))) {
		deleteNews($news_id);
		if ($ctype=='gedcom') $_SESSION['clearcache'] = true;
	}
}

//-- get the blocks list
if ($ctype=='user') {
	$ublocks = getBlocks(WT_USER_NAME);
	if ((count($ublocks['main'])==0) && (count($ublocks['right'])==0)) {
		$ublocks['main'][] = array('print_todays_events', '');
		$ublocks['main'][] = array('print_user_messages', '');
		$ublocks['main'][] = array('print_user_favorites', '');

		$ublocks['right'][] = array('print_welcome_block', '');
		$ublocks['right'][] = array('print_random_media', '');
		$ublocks['right'][] = array('print_upcoming_events', '');
		$ublocks['right'][] = array('print_logged_in_users', '');
	}
}
else {
	$ublocks = getBlocks($GEDCOM);
	if ((count($ublocks['main'])==0) && (count($ublocks['right'])==0)) {
		$ublocks['main'][] = array('print_gedcom_stats', '');
		$ublocks['main'][] = array('print_gedcom_news', '');
		$ublocks['main'][] = array('print_gedcom_favorites', '');
		$ublocks['main'][] = array('review_changes_block', '');

		$ublocks['right'][] = array('print_gedcom_block', '');
		$ublocks['right'][] = array('print_random_media', '');
		$ublocks['right'][] = array('print_todays_events', '');
		$ublocks['right'][] = array('print_logged_in_users', '');
	}
}

//-- Set some behaviour controls that depend on which blocks are selected
$welcome_block_present = false;
$gedcom_block_present = false;
$top10_block_present = false;
$login_block_present = false;
foreach($ublocks['right'] as $block) {
	if ($block[0]=='print_welcome_block') $welcome_block_present = true;
	if ($block[0]=='print_gedcom_block') $gedcom_block_present = true;
	if ($block[0]=='print_block_name_top10') $top10_block_present = true;
	if ($block[0]=='print_login_block') $login_block_present = true;
}
foreach($ublocks['main'] as $block) {
	if ($block[0]=='print_welcome_block') $welcome_block_present = true;
	if ($block[0]=='print_gedcom_block') $gedcom_block_present = true;
	if ($block[0]=='print_block_name_top10') $top10_block_present = true;
	if ($block[0]=='print_login_block') $login_block_present = true;
}

//-- clear the GEDCOM cache files
if (!empty($_SESSION['clearcache'])) {
	$_SESSION['clearcache'] = false;
	clearCache();
}

// We have finished writing to $_SESSION, so release the lock
session_write_close();

//-- handle block AJAX calls
/**
 * In order for a block to make an AJAX call the following request parameters must be set
 * block = the method name of the block to call (e.g. 'print_random_media')
 * side = the side of the page the block is on (e.g. 'main' or 'right')
 * bindex = the number of the block on that side, first block = 0
 */
if ($action=='ajax') {
	header('Content-Type: text/html; charset=UTF-8');
	//--  if a block wasn't sent then exit with nothing
	if (!isset($_REQUEST['block'])) {
		echo 'Block not sent';
		exit;
	}
	$block = $_REQUEST['block'];
	//-- set which side the block is on
	$side = 'main';
	if (isset($_REQUEST['side'])) $side = $_REQUEST['side'];
	//-- get the block number
	if (isset($_REQUEST['bindex'])) {
		if (isset($ublocks[$side][$_REQUEST['bindex']])) {
			$blockval = $ublocks[$side][$_REQUEST['bindex']];
			if ($blockval[0]==$block && array_key_exists($blockval[0], $WT_BLOCKS)) {
				if ($side=='main') {
					$param1 = 'false';
				} else {
					$param1 = 'true';
				}
				if (array_key_exists($blockval[0], $WT_BLOCKS) && !loadCachedBlock($blockval, $side.$_REQUEST['bindex'])) {
					ob_start();
					eval($blockval[0]."($param1, \$blockval[1], \"$side\", ".$_REQUEST['bindex'].");");
					$content = ob_get_contents();
					saveCachedBlock($blockval, $side.$_REQUEST['bindex'], $content);
					ob_end_flush();
				}
				if (WT_DEBUG) {
					echo execution_stats();
				}
				if (WT_DEBUG_SQL) {
					echo WT_DB::getQueryLog();
				}
				exit;
			}
		}
	}

	//-- not sure which block to call so call the first one we find
	foreach($ublocks['main'] as $bindex=>$blockval) {
		if ($blockval[0]==$block && array_key_exists($blockval[0], $WT_BLOCKS)) {
			eval($blockval[0]."(false, \$blockval[1], \"main\", $bindex);");
		}
	}
	foreach($ublocks['right'] as $bindex=>$blockval) {
		if ($blockval[0]==$block && array_key_exists($blockval[0], $WT_BLOCKS)) {
			eval($blockval[0]."(true, \$blockval[1], \"right\", $bindex);");
		}
	}
	exit;
}
//-- end of ajax call handler

if ($ctype=='user') {
	$helpindex = 'index_myged_help';
	print_header(i18n::translate('My Page'));
} else {
	print_header(get_gedcom_setting(WT_GED_ID, 'title'));
}

if (WT_USE_LIGHTBOX) {
	require WT_ROOT.'modules/lightbox/lb_defaultconfig.php';
	require WT_ROOT.'modules/lightbox/functions/lb_call_js.php';
}

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
/**
 * blocks may use this JS function to update themselves using AJAX technology
 * @param string targetId	the id of the block to target the results too
 * @param string block 	the method name of the block to call (e.g. 'print_random_media')
 * @param string side 	the side of the page the block is on (e.g. 'main' or 'right')
 * @param int bindex 	the number of the block on that side, first block = 0
 * @param string ctype 	shows whether block is on Welcome or My Page ('gedcom' or 'user')
 * @param boolean loading  Whether or not to show the loading message
 */
	function ajaxBlock(targetId, block, side, bindex, ctype, loading) {
		target = document.getElementById(targetId);
		if (!target) return false;

		target.style.height = (target.offsetHeight) + "px";
		if (loading) target.innerHTML = "<br /><br /><?php echo i18n::translate('Loading...'); ?><br /><br />";

		var oXmlHttp = createXMLHttp();
		link = "index.php?action=ajax&block="+block+"&side="+side+"&bindex="+bindex+"&ctype="+ctype;
		oXmlHttp.open("get", link, true);
		oXmlHttp.onreadystatechange=function()
		{
			if (oXmlHttp.readyState==4)
 			{
 				target.innerHTML = oXmlHttp.responseText;
 				target.style.height = 'auto';
 			}
		};
 		oXmlHttp.send(null);
 		return false;
	}
<?php
echo WT_JS_END;
//-- start of main content section
echo '<table width="100%"><tr><td>';		// This is needed so that page footers print in the right place
if ($ctype=='user') {
	echo '<div align="center" style="width: 99%;">';
	echo '<h1>', i18n::translate('My Page'), '</h1>';
	echo i18n::translate('My Page allows you to keep bookmarks of your favorite people, track upcoming events, and collaborate with other webtrees users.');
	echo '<br /><br /></div>';
}
if (count($ublocks['main'])!=0) {
	if (count($ublocks['right'])!=0) {
		echo '<div id="index_main_blocks">';
	} else {
		echo '<div id="index_full_blocks">';
	}
	echo '<script src="js/jquery/jquery.min.js" type="text/javascript"></script>';
	echo '<script type="text/javascript">jQuery.noConflict();</script>';
	foreach($ublocks['main'] as $bindex=>$block) {
		if (WT_DEBUG) {
			echo execution_stats();
		}
		if (array_key_exists($block[0], $WT_BLOCKS) && !loadCachedBlock($block, 'main'.$bindex)) {
			$url="index.php?action=ajax&block={$block[0]}&side=main&bindex={$bindex}&ctype={$ctype}";
			if ($SEARCH_SPIDER || WT_DEBUG) {
				// Search spiders get the blocks directly
				ob_start();
				eval($block[0]."(false, \$block[1], \"main\", $bindex);");
				$content = ob_get_contents();
				$temp = $SEARCH_SPIDER;
				$SEARCH_SPIDER = false;
				saveCachedBlock($block, 'main'.$bindex, $content);
				$SEARCH_SPIDER = $temp;
				ob_end_flush();
			} else {
				// Interactive users get the blocks via ajax
				echo '<div id="block_main_', $bindex, '"><img src="images/loading.gif" alt="', htmlspecialchars(i18n::translate('Loading...')),  '"/></div>';
				echo WT_JS_START, "jQuery('#block_main_{$bindex}').load('{$url}');", WT_JS_END;
			}
		}
	}
	echo '</div>';
}
//-- end of main content section

//-- start of blocks section
if (count($ublocks['right'])!=0) {
	if (count($ublocks['main'])!=0) {
		echo '<div id="index_small_blocks">';
	} else {
		echo '<div id="index_full_blocks">';
	}
	foreach($ublocks['right'] as $bindex=>$block) {
		if (WT_DEBUG) {
			echo execution_stats();
		}
		if (array_key_exists($block[0], $WT_BLOCKS) && !loadCachedBlock($block, 'right'.$bindex)) {
			$url="index.php?action=ajax&block={$block[0]}&side=right&bindex={$bindex}&ctype={$ctype}";
			if ($SEARCH_SPIDER || WT_DEBUG) {
				// Search spiders get the blocks directly
				ob_start();
				eval($block[0]."(true, \$block[1], \"right\", $bindex);");
				$content = ob_get_contents();
				saveCachedBlock($block, 'right'.$bindex, $content);
				ob_end_flush();
			} else {
				// Interactive users get the blocks via ajax
				echo '<div id="block_right_', $bindex, '"><img src="images/loading.gif" alt="', htmlspecialchars(i18n::translate('Loading...')),  '"/></div>';
				echo WT_JS_START, "jQuery('#block_right_{$bindex}').load('{$url}');", WT_JS_END;
			}
		}
	}
	echo '</div>';
}
//-- end of blocks section

echo '</td></tr></table><br />';		// Close off that table

if ($ctype=='user' && !$welcome_block_present) {
	echo '<div align="center" style="width: 99%;">';
	echo "<a href=\"javascript:;\" onclick=\"window.open('index_edit.php?name=".WT_USER_NAME."&ctype=user', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\">".i18n::translate('Customize My Page').'</a>';
	echo help_link('mygedview_customize');
	echo '</div>';
}
if ($ctype=='gedcom' && !$gedcom_block_present) {
	if (WT_USER_IS_ADMIN) {
		echo '<div align="center" style="width: 99%;">';
		echo "<a href=\"javascript:;\" onclick=\"window.open('".encode_url("index_edit.php?name={$GEDCOM}&ctype=gedcom", false)."', '_blank', 'top=50,left=10,width=600,height=500,scrollbars=1,resizable=1');\">".i18n::translate('Customize this GEDCOM Home Page').'</a>';
		echo '</div>';
	}
}

print_footer();
