<?php
// Welcome page for the administration module
//
// webtrees: Web based Family History software
// Copyright (C) 2013 webtrees development team.
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

define('WT_SCRIPT_NAME', 'admin.php');

require './includes/session.php';
require WT_ROOT.'includes/functions/functions_edit.php';

$controller=new WT_Controller_Page();
$controller
	->requireManagerLogin()
	->setPageTitle(WT_I18N::translate('Administration'))
	->pageHeader();

// Check for updates
$latest_version_txt=fetch_latest_version();
if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
	list($latest_version, $earliest_version, $download_url)=explode('|', $latest_version_txt);
} else {
	// Cannot determine the latest version
	$latest_version='';
}

$stats=new WT_Stats(WT_GEDCOM);
	$totusers  =0;       // Total number of users
	$warnusers =0;       // Users with warning
	$applusers =0;       // Users who have not verified themselves
	$nverusers =0;       // Users not verified by admin but verified themselves
	$adminusers=0;       // Administrators
	$userlang  =array(); // Array for user languages
	$gedadmin  =array(); // Array for managers

// Display a series of "blocks" of general information, vary according to admin or manager.

echo '<div id="content_container" style="visibility:hidden">';

echo '<div id="x">';// div x - manages the accordion effect

echo '<h2>', WT_WEBTREES, ' ', WT_VERSION, '</h2>',
	'<div id="about">',
	'<p>', WT_I18N::translate('These pages provide access to all the configuration settings and management tools for this <b>webtrees</b> site.'), '</p>',
	'<p>',  /* I18N: %s is a URL/link to the project website */ WT_I18N::translate('Support and documentation can be found at %s.', ' <a class="current" href="http://webtrees.net/">webtrees.net</a>'), '</p>';

// Accordion block for UPGRADE - only shown when upgrades are available
if (WT_USER_IS_ADMIN && $latest_version && version_compare(WT_VERSION, $latest_version)<0) {
	echo '<p>', WT_I18N::translate('A new version of webtrees is available.'), ' <a href="admin_site_upgrade.php"><span class="error">',  WT_I18N::translate('Upgrade to webtrees %s', WT_Filter::escapeHtml($latest_version)), '</span></a></p>';
}

echo '</div>';

// Accordion block for DELETE OLD FILES - only shown when old files are found
$old_files_found=false;
foreach (old_paths() as $path) {
	if (file_exists($path)) {
		delete_recursively($path);
		// we may not have permission to delete.  Is it still there?
		if (file_exists($path)) {
			$old_files_found=true;
		}
	}
}

if (WT_USER_IS_ADMIN && $old_files_found) {
	echo
		'<h2><span class="warning">', WT_I18N::translate('Old files found'), '</span></h2>',
		'<div>',
		'<p>', WT_I18N::translate('Files have been found from a previous version of webtrees.  Old files can sometimes be a security risk.  You should delete them.'), '</p>',
		'<ul>';
		foreach (old_paths() as $path) {
			if (file_exists($path)) {
				echo '<li>', $path, '</li>';
			}
		}
	echo
		'</ul>',
		'</div>';
}

echo
	'<h2>', WT_I18N::translate('Users'), '</h2>',
	'<div id="users">'; //id = users

		foreach(get_all_users() as $user_id=>$user_name) {
			$totusers = $totusers + 1;
			if (((date("U") - (int)get_user_setting($user_id, 'reg_timestamp')) > 604800) && !get_user_setting($user_id, 'verified')) {
				$warnusers++;
			}
			if (!get_user_setting($user_id, 'verified_by_admin') && get_user_setting($user_id, 'verified')) {
				$nverusers++;
			}
			if (!get_user_setting($user_id, 'verified')) {
				$applusers++;
			}
			if (get_user_setting($user_id, 'canadmin')) {
				$adminusers++;
			}
			foreach (WT_Tree::getAll() as $tree) {
				if ($tree->userPreference($user_id, 'canedit')=='admin') {
					if (isset($gedadmin[$tree->tree_id])) {
						$gedadmin[$tree->tree_id]["number"]++;
					} else {
						$gedadmin[$tree->tree_id]["number"] = 1;
						$gedadmin[$tree->tree_id]["ged"] = $tree->tree_name;
						$gedadmin[$tree->tree_id]["title"] = $tree->tree_title_html;
					}
				}
			}
			if ($user_lang=get_user_setting($user_id, 'language')) {
				if (isset($userlang[$user_lang]))
					$userlang[$user_lang]["number"]++;
				else {
					$userlang[$user_lang]["langname"] = Zend_Locale::getTranslation($user_lang, 'language', WT_LOCALE);
					$userlang[$user_lang]["number"] = 1;
				}
			}
		}

	echo
		'<table>',
		'<tr><td>', WT_I18N::translate('Total number of users'), '</td><td>', $totusers, '</td></tr>',
		'<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin_users.php?action=listusers&amp;filter=adminusers">', WT_I18N::translate('Administrators'), '</a></td><td>', $adminusers, '</td></tr>',
		'<tr><td colspan="2">', WT_I18N::translate('Managers'), '</td></tr>';
		foreach ($gedadmin as $ged_id=>$geds) {
			echo '<tr><td><div><a href="admin_users.php?action=listusers&amp;filter=gedadmin&amp;ged='.rawurlencode($geds['ged']), '" dir="auto">', $geds['title'], '</a></div></td><td>', $geds['number'], '</td></tr>';
		}
	echo '<tr><td>';
	if ($warnusers == 0) {
		echo WT_I18N::translate('Users with warnings');
	} else {
		echo '<a href="admin_users.php?action=listusers&amp;filter=warnings">', WT_I18N::translate('Users with warnings'), '</a>';
	}
	echo '</td><td>', $warnusers, '</td></tr><tr><td>';
	if ($applusers == 0) {
		echo WT_I18N::translate('Unverified by User');
	} else {
		echo '<a href="admin_users.php?action=listusers&amp;filter=usunver">', WT_I18N::translate('Unverified by User'), '</a>';
	}
	echo '</td><td>', $applusers, '</td></tr><tr><td>';
	if ($nverusers == 0) {
		echo WT_I18N::translate('Unverified by Administrator');
	} else {
		echo '<a href="admin_users.php?action=listusers&amp;filter=admunver">', WT_I18N::translate('Unverified by Administrator'), '</a>';
	}
	echo '</td><td>', $nverusers, '</td></tr>';
	echo '<tr><td colspan="2">', WT_I18N::translate('Users’ languages'), '</td></tr>';
	foreach ($userlang as $key=>$ulang) {
		echo '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin_users.php?action=listusers&amp;filter=language&amp;usrlang=', $key, '">', $ulang['langname'], '</a></td><td>', $ulang['number'], '</td></tr>';
	}
	echo
		'<tr><td colspan="2">', WT_I18N::translate('Users logged in'), '</td></tr>',
		'<tr><td colspan="2"><div>', $stats->_usersLoggedIn('list'), '</div></td></tr>',
		'</table>';
echo '</div>'; // id = users

echo
	'<h2>', WT_I18N::translate('Family trees'), '</h2>',
	'<div id="trees">',// id=trees
	'<div id="tree_stats">';
$n=0;
foreach (WT_Tree::getAll() as $tree) {
	$stats = new WT_Stats($tree->tree_name);
	if ($tree->tree_id==WT_GED_ID) {
		$accordion_element=$n;
	}
	++$n;
	echo
		'<h3>', $stats->gedcomTitle(), '</h3>',
		'<div>',
		'<table>',
		'<tr><td>&nbsp;</td><td><span>', WT_I18N::translate('Count'), '</span></td></tr>',
		'<tr><th><a href="indilist.php?ged=', $tree->tree_name_url, '">',
		WT_I18N::translate('Individuals'), '</a></th><td>', $stats->totalIndividuals(),
		'</td></tr>',
		'<tr><th><a href="famlist.php?ged=', $tree->tree_name_url, '">',
		WT_I18N::translate('Families'), '</a></th><td>', $stats->totalFamilies(),
		'</td></tr>',
		'<tr><th><a href="sourcelist.php?ged=', $tree->tree_name_url, '">',
		WT_I18N::translate('Sources'), '</a></th><td>', $stats->totalSources(),
		'</td></tr>',
		'<tr><th><a href="repolist.php?ged=', $tree->tree_name_url, '">',
		WT_I18N::translate('Repositories'), '</a></th><td>', $stats->totalRepositories(),
		'</td></tr>',
		'<tr><th><a href="medialist.php?ged=', $tree->tree_name_url, '">',
		WT_I18N::translate('Media objects'), '</a></th><td>', $stats->totalMedia(),
		'</td></tr>',
		'<tr><th><a href="notelist.php?ged=', $tree->tree_name_url, '">',
		WT_I18N::translate('Notes'), '</a></th><td>', $stats->totalNotes(),
		'</td></tr>',
		'</table>',
		'</div>';
}
echo
	'</div>', // id=tree_stats
	'</div>'; // id=trees

$controller->addInlineJavascript('jQuery("#tree_stats").accordion({active:'.$accordion_element.', icons:{ "header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" }});');

echo
	'<h2>', WT_I18N::translate('Recent changes'), '</h2>',
	'<div id="recent2">'; //id=recent
	echo
	'<div id="changes">';
$n=0;
foreach (WT_Tree::GetAll() as $tree) {
	if ($tree->tree_id==WT_GED_ID) {
		$accordion_element=$n;
	}
	++$n;
	echo
		'<h3><span dir="auto">', $tree->tree_title_html, '</span></h3>',
		'<div>',
		'<table>',
		'<tr><td>&nbsp;</td><td><span>', WT_I18N::translate('Day'), '</span></td><td><span>', WT_I18N::translate('Week'), '</span></td><td><span>', WT_I18N::translate('Month'), '</span></td></tr>',
		'<tr><th>', WT_I18N::translate('Individuals'), '</th><td>', WT_Query_Admin::countIndiChangesToday($tree->tree_id), '</td><td>', WT_Query_Admin::countIndiChangesWeek($tree->tree_id), '</td><td>', WT_Query_Admin::countIndiChangesMonth($tree->tree_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Families'), '</th><td>', WT_Query_Admin::countFamChangesToday($tree->tree_id), '</td><td>', WT_Query_Admin::countFamChangesWeek($tree->tree_id), '</td><td>', WT_Query_Admin::countFamChangesMonth($tree->tree_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Sources'), '</th><td>',  WT_Query_Admin::countSourChangesToday($tree->tree_id), '</td><td>', WT_Query_Admin::countSourChangesWeek($tree->tree_id), '</td><td>', WT_Query_Admin::countSourChangesMonth($tree->tree_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Repositories'), '</th><td>',  WT_Query_Admin::countRepoChangesToday($tree->tree_id), '</td><td>', WT_Query_Admin::countRepoChangesWeek($tree->tree_id), '</td><td>', WT_Query_Admin::countRepoChangesMonth($tree->tree_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Media objects'), '</th><td>', WT_Query_Admin::countObjeChangesToday($tree->tree_id), '</td><td>', WT_Query_Admin::countObjeChangesWeek($tree->tree_id), '</td><td>', WT_Query_Admin::countObjeChangesMonth($tree->tree_id), '</td></tr>',
		'<tr><th>', WT_I18N::translate('Notes'), '</th><td>', WT_Query_Admin::countNoteChangesToday($tree->tree_id), '</td><td>', WT_Query_Admin::countNoteChangesWeek($tree->tree_id), '</td><td>', WT_Query_Admin::countNoteChangesMonth($tree->tree_id), '</td></tr>',
		'</table>',
		'</div>';
	}
echo
	'</div>', // id=changes
	'</div>', // id=recent
	'</div>', //id = "x"
	'</div>'; //id = content_container

$controller
	->addInlineJavascript('jQuery("#changes").accordion({active:' . $accordion_element . ', icons:{ "header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" }});')
	->addInlineJavascript('jQuery("#x").accordion({active:0, icons:{ "header": "ui-icon-triangle-1-s", "headerSelected": "ui-icon-triangle-1-n" }, heightStyle: "content"});')
	->addInlineJavascript('jQuery("#content_container").css("visibility", "visible");');

// This is a list of old files and directories, from earlier versions of webtrees, that can be deleted
// It was generated with the help of a command like this
// git diff 1.4.3..master --name-status | grep ^D
function old_paths() {
	return array(
		// Removed in 1.0.2
		WT_ROOT.'language/en.mo',
		// Removed in 1.0.3
		WT_ROOT.'themechange.php',
		// Removed in 1.0.4
		// Removed in 1.0.5
		// Removed in 1.0.6
		WT_ROOT.'includes/extras',
		// Removed in 1.1.0
		WT_ROOT.'addremotelink.php',
		WT_ROOT.'addsearchlink.php',
		WT_ROOT.'client.php',
		WT_ROOT.'dir_editor.php',
		WT_ROOT.'editconfig_gedcom.php',
		WT_ROOT.'editgedcoms.php',
		WT_ROOT.'edit_merge.php',
		WT_ROOT.'genservice.php',
		WT_ROOT.'includes/classes',
		WT_ROOT.'includes/controllers',
		WT_ROOT.'includes/family_nav.php',
		WT_ROOT.'includes/functions/functions_lang.php',
		WT_ROOT.'includes/functions/functions_tools.php',
		WT_ROOT.'js/conio',
		WT_ROOT.'logs.php',
		WT_ROOT.'manageservers.php',
		WT_ROOT.'media.php',
		WT_ROOT.'module_admin.php',
		//WT_ROOT.'modules', // Do not delete - users may have stored custom modules/data here
		WT_ROOT.'opensearch.php',
		WT_ROOT.'PEAR.php',
		WT_ROOT.'pgv_to_wt.php',
		WT_ROOT.'places',
		//WT_ROOT.'robots.txt', // Do not delete this - it may contain user data
		WT_ROOT.'serviceClientTest.php',
		WT_ROOT.'siteconfig.php',
		WT_ROOT.'SOAP',
		WT_ROOT.'themes/clouds/mozilla.css',
		WT_ROOT.'themes/clouds/netscape.css',
		WT_ROOT.'themes/colors/mozilla.css',
		WT_ROOT.'themes/colors/netscape.css',
		WT_ROOT.'themes/fab/mozilla.css',
		WT_ROOT.'themes/fab/netscape.css',
		WT_ROOT.'themes/minimal/mozilla.css',
		WT_ROOT.'themes/minimal/netscape.css',
		WT_ROOT.'themes/webtrees/mozilla.css',
		WT_ROOT.'themes/webtrees/netscape.css',
		WT_ROOT.'themes/webtrees/style_rtl.css',
		WT_ROOT.'themes/xenea/mozilla.css',
		WT_ROOT.'themes/xenea/netscape.css',
		WT_ROOT.'uploadmedia.php',
		WT_ROOT.'useradmin.php',
		WT_ROOT.'webservice',
		WT_ROOT.'wtinfo.php',
		// Removed in 1.1.1
		// Removed in 1.1.2
		WT_ROOT.'js/treenav.js',
		WT_ROOT.'library/WT/TreeNav.php',
		WT_ROOT.'treenav.php',
		// Removed in 1.2.0
		WT_ROOT.'themes/clouds/jquery',
		WT_ROOT.'themes/colors/jquery',
		WT_ROOT.'themes/fab/jquery',
		WT_ROOT.'themes/minimal/jquery',
		WT_ROOT.'themes/webtrees/jquery',
		WT_ROOT.'themes/xenea/jquery',
		// Removed in 1.2.1
		// Removed in 1.2.2
		WT_ROOT.'themes/clouds/chrome.css',
		WT_ROOT.'themes/clouds/opera.css',
		WT_ROOT.'themes/clouds/print.css',
		WT_ROOT.'themes/clouds/style_rtl.css',
		WT_ROOT.'themes/colors/chrome.css',
		WT_ROOT.'themes/colors/opera.css',
		WT_ROOT.'themes/colors/print.css',
		WT_ROOT.'themes/colors/style_rtl.css',
		WT_ROOT.'themes/fab/chrome.css',
		WT_ROOT.'themes/fab/opera.css',
		WT_ROOT.'themes/minimal/chrome.css',
		WT_ROOT.'themes/minimal/opera.css',
		WT_ROOT.'themes/minimal/print.css',
		WT_ROOT.'themes/minimal/style_rtl.css',
		WT_ROOT.'themes/xenea/chrome.css',
		WT_ROOT.'themes/xenea/opera.css',
		WT_ROOT.'themes/xenea/print.css',
		WT_ROOT.'themes/xenea/style_rtl.css',
		// Removed in 1.2.3
		//WT_ROOT.'modules_v2', // Do not delete - users may have stored custom modules/data here
		// Removed in 1.2.4
		WT_ROOT.'includes/cssparser.inc.php',
		WT_ROOT.'js/strings.js',
		WT_ROOT.'modules_v3/gedcom_favorites/help_text.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_3_find.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_3_search_add.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_5_input.js',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_5_input.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_7_parse_addLinksTbl.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_query_1a.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_query_2a.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_MEDIA/media_query_3a.php',
		WT_ROOT.'modules_v3/lightbox/css/album_page_RTL2.css',
		WT_ROOT.'modules_v3/lightbox/css/album_page_RTL.css',
		WT_ROOT.'modules_v3/lightbox/css/album_page_RTL_ff.css',
		WT_ROOT.'modules_v3/lightbox/css/clearbox_music.css',
		WT_ROOT.'modules_v3/lightbox/css/clearbox_music_RTL.css',
		WT_ROOT.'modules_v3/user_favorites/db_schema',
		WT_ROOT.'modules_v3/user_favorites/help_text.php',
		WT_ROOT.'search_engine.php',
		WT_ROOT.'themes/clouds/modules.css',
		WT_ROOT.'themes/colors/modules.css',
		WT_ROOT.'themes/fab/modules.css',
		WT_ROOT.'themes/minimal/modules.css',
		WT_ROOT.'themes/webtrees/modules.css',
		WT_ROOT.'themes/xenea/modules.css',
		// Removed in 1.2.5
		WT_ROOT.'includes/media_reorder_count.php',
		WT_ROOT.'includes/media_tab_head.php',
		WT_ROOT.'js/behaviour.js.htm',
		WT_ROOT.'js/bennolan',
		WT_ROOT.'js/bosrup',
		WT_ROOT.'js/kryogenix',
		WT_ROOT.'js/overlib.js.htm',
		WT_ROOT.'js/scriptaculous',
		WT_ROOT.'js/scriptaculous.js.htm',
		WT_ROOT.'js/sorttable.js.htm',
		WT_ROOT.'library/WT/JS.php',
		WT_ROOT.'modules_v3/clippings/index.php',
		WT_ROOT.'modules_v3/googlemap/css/googlemap_style.css',
		WT_ROOT.'modules_v3/googlemap/css/wt_v3_places_edit.css',
		WT_ROOT.'modules_v3/googlemap/index.php',
		WT_ROOT.'modules_v3/lightbox/index.php',
		WT_ROOT.'modules_v3/recent_changes/help_text.php',
		WT_ROOT.'modules_v3/todays_events/help_text.php',
		WT_ROOT.'sidebar.php',
		// Removed in 1.2.6
		WT_ROOT.'modules_v3/sitemap/admin_index.php',
		WT_ROOT.'modules_v3/sitemap/help_text.php',
		WT_ROOT.'modules_v3/tree/css/styles',
		WT_ROOT.'modules_v3/tree/css/treebottom.gif',
		WT_ROOT.'modules_v3/tree/css/treebottomleft.gif',
		WT_ROOT.'modules_v3/tree/css/treebottomright.gif',
		WT_ROOT.'modules_v3/tree/css/tree.jpg',
		WT_ROOT.'modules_v3/tree/css/treeleft.gif',
		WT_ROOT.'modules_v3/tree/css/treeright.gif',
		WT_ROOT.'modules_v3/tree/css/treetop.gif',
		WT_ROOT.'modules_v3/tree/css/treetopleft.gif',
		WT_ROOT.'modules_v3/tree/css/treetopright.gif',
		WT_ROOT.'modules_v3/tree/css/treeview_print.css',
		WT_ROOT.'modules_v3/tree/help_text.php',
		WT_ROOT.'modules_v3/tree/images/print.png',
		// Removed in 1.2.7
		WT_ROOT.'login_register.php',
		WT_ROOT.'modules_v3/top10_givnnames/help_text.php',
		WT_ROOT.'modules_v3/top10_surnames/help_text.php',
		// Removed in 1.3.0
		WT_ROOT.'admin_site_ipaddress.php',
		WT_ROOT.'downloadgedcom.php',
		WT_ROOT.'export_gedcom.php',
		WT_ROOT.'gedcheck.php',
		WT_ROOT.'images',
		WT_ROOT.'includes/dmsounds_UTF8.php',
		WT_ROOT.'includes/functions/functions_name.php',
		WT_ROOT.'includes/grampsxml.rng',
		WT_ROOT.'includes/session_spider.php',
		WT_ROOT.'js/autocomplete.js.htm',
		WT_ROOT.'js/prototype',
		WT_ROOT.'js/prototype.js.htm',
		WT_ROOT.'modules_v3/googlemap/admin_editconfig.php',
		WT_ROOT.'modules_v3/googlemap/admin_placecheck.php',
		WT_ROOT.'modules_v3/googlemap/flags.php',
		WT_ROOT.'modules_v3/googlemap/images/pedigree_map.gif',
		WT_ROOT.'modules_v3/googlemap/pedigree_map.php',
		WT_ROOT.'modules_v3/lightbox/admin_config.php',
		WT_ROOT.'modules_v3/lightbox/album.php',
		WT_ROOT.'modules_v3/tree/css/vline.jpg',
		// Removed in 1.3.1
		WT_ROOT.'imageflush.php',
		WT_ROOT.'includes/functions/functions_places.php',
		WT_ROOT.'js/html5.js',
		WT_ROOT.'modules_v3/googlemap/wt_v3_pedigree_map.js.php',
		WT_ROOT.'modules_v3/lightbox/js/tip_balloon_RTL.js',
		// Removed in 1.3.2
		WT_ROOT.'includes/set_gedcom_defaults.php',
		WT_ROOT.'modules_v3/address_report',
		WT_ROOT.'modules_v3/lightbox/functions/lb_horiz_sort.php',
		WT_ROOT.'modules_v3/random_media/help_text.php',
		// Removed in 1.4.0
		WT_ROOT.'imageview.php',
		WT_ROOT.'includes/functions/functions_media_reorder.php',
		WT_ROOT.'js/jquery',
		WT_ROOT.'js/jw_player',
		WT_ROOT.'js/modernizr.custom-2.6.1.js',
		WT_ROOT.'js/webtrees.js',
		WT_ROOT.'media/MediaInfo.txt',
		WT_ROOT.'media/thumbs/ThumbsInfo.txt',
		WT_ROOT.'modules_v3/GEDFact_assistant/css/media_0_inverselink.css',
		WT_ROOT.'modules_v3/lightbox/help_text.php',
		WT_ROOT.'modules_v3/lightbox/images/blank.gif',
		WT_ROOT.'modules_v3/lightbox/images/close_1.gif',
		WT_ROOT.'modules_v3/lightbox/images/image_add.gif',
		WT_ROOT.'modules_v3/lightbox/images/image_copy.gif',
		WT_ROOT.'modules_v3/lightbox/images/image_delete.gif',
		WT_ROOT.'modules_v3/lightbox/images/image_edit.gif',
		WT_ROOT.'modules_v3/lightbox/images/image_link.gif',
		WT_ROOT.'modules_v3/lightbox/images/images.gif',
		WT_ROOT.'modules_v3/lightbox/images/image_view.gif',
		WT_ROOT.'modules_v3/lightbox/images/loading.gif',
		WT_ROOT.'modules_v3/lightbox/images/next.gif',
		WT_ROOT.'modules_v3/lightbox/images/nextlabel.gif',
		WT_ROOT.'modules_v3/lightbox/images/norm_2.gif',
		WT_ROOT.'modules_v3/lightbox/images/overlay.png',
		WT_ROOT.'modules_v3/lightbox/images/prev.gif',
		WT_ROOT.'modules_v3/lightbox/images/prevlabel.gif',
		WT_ROOT.'modules_v3/lightbox/images/private.gif',
		WT_ROOT.'modules_v3/lightbox/images/slideshow.jpg',
		WT_ROOT.'modules_v3/lightbox/images/transp80px.gif',
		WT_ROOT.'modules_v3/lightbox/images/zoom_1.gif',
		WT_ROOT.'modules_v3/lightbox/js',
		WT_ROOT.'modules_v3/lightbox/music',
		WT_ROOT.'modules_v3/lightbox/pic',
		WT_ROOT.'themes/_administration/jquery',
		WT_ROOT.'themes/webtrees/chrome.css',
		// Removed in 1.4.1
		WT_ROOT.'js/webtrees-1.4.0.js',
		WT_ROOT.'modules_v3/lightbox/images/image_edit.png',
		WT_ROOT.'modules_v3/lightbox/images/image_view.png',
		// Removed in 1.4.2
		WT_ROOT.'modules_v3/lightbox/images/image_view.png',
		WT_ROOT.'js/jquery.colorbox-1.4.3.js',
		WT_ROOT.'js/jquery-ui-1.10.0.js',
		WT_ROOT.'js/webtrees-1.4.1.js',
		WT_ROOT.'modules_v3/top10_pageviews/help_text.php',
		WT_ROOT.'themes/_administration/jquery-ui-1.10.0',
		WT_ROOT.'themes/clouds/jquery-ui-1.10.0',
		WT_ROOT.'themes/colors/jquery-ui-1.10.0',
		WT_ROOT.'themes/fab/jquery-ui-1.10.0',
		WT_ROOT.'themes/minimal/jquery-ui-1.10.0',
		WT_ROOT.'themes/webtrees/jquery-ui-1.10.0',
		WT_ROOT.'themes/xenea/jquery-ui-1.10.0',
		// Removed in 1.5.0
		WT_ROOT.'includes/functions/functions_mail.php',
		WT_ROOT.'includes/functions/functions_privacy.php',
		WT_ROOT.'includes/media_reorder.php',
		WT_ROOT.'includes/old_messages.php',
		WT_ROOT.'js/jquery-1.9.1.js',
		WT_ROOT.'js/jquery.cookie-1.3.1.js',
		WT_ROOT.'js/webtrees-1.4.2.js',
		WT_ROOT.'library/WT/Event.php',
		WT_ROOT.'library/WT/Person.php',
		WT_ROOT.'library/phpmailer',
		WT_ROOT.'modules_v3/GEDFact_assistant/_CENS/census_note_decode.php',
		WT_ROOT.'modules_v3/GEDFact_assistant/_CENS/census_asst_date.php',
		WT_ROOT.'modules_v3/googlemap/wt_v3_googlemap.js.php',
		WT_ROOT.'modules_v3/lightbox/functions/lightbox_print_media.php',
		WT_ROOT.'modules_v3/upcoming_events/help_text.php',
		WT_ROOT.'modules_v3/stories/help_text.php',
		WT_ROOT.'modules_v3/user_messages/help_text.php',
		WT_ROOT.'themes/_administration/favicon.png',
		WT_ROOT.'themes/_administration/images',
		WT_ROOT.'themes/_administration/msie.css',
		WT_ROOT.'themes/_administration/style.css',
		WT_ROOT.'themes/clouds/favicon.png',
		WT_ROOT.'themes/clouds/images',
		WT_ROOT.'themes/clouds/msie.css',
		WT_ROOT.'themes/clouds/style.css',
		WT_ROOT.'themes/colors/css',
		WT_ROOT.'themes/colors/favicon.png',
		WT_ROOT.'themes/colors/images',
		WT_ROOT.'themes/colors/ipad.css',
		WT_ROOT.'themes/colors/msie.css',
		WT_ROOT.'themes/fab/favicon.png',
		WT_ROOT.'themes/fab/images',
		WT_ROOT.'themes/fab/msie.css',
		WT_ROOT.'themes/fab/style.css',
		WT_ROOT.'themes/minimal/favicon.png',
		WT_ROOT.'themes/minimal/images',
		WT_ROOT.'themes/minimal/msie.css',
		WT_ROOT.'themes/minimal/style.css',
		WT_ROOT.'themes/webtrees/favicon.png',
		WT_ROOT.'themes/webtrees/images',
		WT_ROOT.'themes/webtrees/msie.css',
		WT_ROOT.'themes/webtrees/style.css',
		WT_ROOT.'themes/xenea/favicon.png',
		WT_ROOT.'themes/xenea/images',
		WT_ROOT.'themes/xenea/msie.css',
		WT_ROOT.'themes/xenea/style.css',
		// Removed in 1.5.1
		WT_ROOT.'js/webtrees-1.5.0.js',
		WT_ROOT.'themes/clouds/css-1.5.0',
		WT_ROOT.'themes/colors/css-1.5.0',
		WT_ROOT.'themes/fab/css-1.5.0',
		WT_ROOT.'themes/minimal/css-1.5.0',
		WT_ROOT.'themes/webtrees/css-1.5.0',
		WT_ROOT.'themes/xenea/css-1.5.0',
	);
}

// Delete a file or folder, ignoring errors
function delete_recursively($path) {
	@chmod($path, 0777);
	if (is_dir($path)) {
		$dir=opendir($path);
		while ($dir!==false && (($file=readdir($dir))!==false)) {
			if ($file!='.' && $file!='..') {
				delete_recursively($path.'/'.$file);
			}
		}
		closedir($dir);
		@rmdir($path);
	} else {
		@unlink($path);
	}
}
