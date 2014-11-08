<?php
// Welcome page for the administration module
//
// webtrees: Web based Family History software
// Copyright (C) 2014 webtrees development team.
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
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

use WT\Auth;
use WT\User;

define('WT_SCRIPT_NAME', 'admin.php');

require './includes/session.php';
require WT_ROOT . 'includes/functions/functions_edit.php';

$controller = new WT_Controller_Page();
$controller
	->restrictAccess(Auth::isManager())
	->addInlineJavascript('jQuery("#x").accordion({heightStyle: "content"});')
	->addInlineJavascript('jQuery("#tree_stats").accordion();')
	->addInlineJavascript('jQuery("#changes").accordion();')
	->addInlineJavascript('jQuery("#content_container").css("visibility", "visible");')
	->setPageTitle(WT_I18N::translate('Administration'))
	->pageHeader();

// Check for updates
$latest_version_txt = fetch_latest_version();
if (preg_match('/^[0-9.]+\|[0-9.]+\|/', $latest_version_txt)) {
	list($latest_version, $earliest_version, $download_url) = explode('|', $latest_version_txt);
} else {
	// Cannot determine the latest version
	$latest_version = '';
}

// Delete old files (if we can).
$old_files = array();
foreach (old_paths() as $path) {
	if (file_exists($path)) {
		if (!WT_File::delete($path)) {
			// We may be unable to delete it.  If so, tell the user to delete it manually.
			$old_files[] = $path;
		}
	}
}

// Total number of users
$total_users = User::count();

// Total number of administrators
$total_administrators = WT_DB::prepare(
	"SELECT COUNT(*) FROM `##user_setting` WHERE setting_name='canadmin' AND setting_value=1"
)->fetchOne();

// Total numbers of managers
$total_managers = WT_DB::prepare(
	"SELECT gs.setting_value, COUNT(*)" .
	" FROM `##gedcom_setting` gs" .
	" JOIN `##user_gedcom_setting` ugs USING (gedcom_id)" .
	" WHERE ugs.setting_name = 'canedit' AND ugs.setting_value='admin'" .
	" AND   gs.setting_name ='title'" .
	" GROUP BY gedcom_id" .
	" ORDER BY gs.setting_value"
)->fetchAssoc();

// Number of users who have not verified their email address
$unverified = WT_DB::prepare(
	"SELECT COUNT(*) FROM `##user_setting` WHERE setting_name = 'verified' AND setting_value = 0"
)->fetchOne();

// Number of users whose accounts are not approved by an administrator
$unapproved = WT_DB::prepare(
	"SELECT COUNT(*) FROM `##user_setting` WHERE setting_name = 'verified_by_admin' AND setting_value = 0"
)->fetchOne();

// Number of users of each language
$user_languages = WT_DB::prepare(
	"SELECT setting_value, COUNT(*)" .
	" FROM `##user_setting`" .
	" WHERE setting_name = 'language'" .
	" GROUP BY setting_value"
)->fetchAssoc();

$stats = new WT_Stats(WT_GEDCOM);

?>
<div id="content_container" style="visibility: hidden;">
	<div id="x">
		<h2><?php echo WT_WEBTREES, ' ', WT_VERSION; ?></h2>
		<div id="about">
			<p>
				<?php echo WT_I18N::translate('These pages provide access to all the configuration settings and management tools for this webtrees site.'); ?>
			</p>
			<p>
				<?php echo /* I18N: %s is a URL/link to the project website */ WT_I18N::translate('Support and documentation can be found at %s.', ' <a class="current" href="http://webtrees.net/">webtrees.net</a>'); ?>
			</p>
			<?php if (Auth::isAdmin() && $latest_version && version_compare(WT_VERSION, $latest_version) < 0) { ?>
			<p>
				<?php echo WT_I18N::translate('A new version of webtrees is available.'); ?>
				<a href="admin_site_upgrade.php" class="error">
					<?php echo /* I18N: %s is a version number */ WT_I18N::translate('Upgrade to webtrees %s', WT_Filter::escapeHtml($latest_version)); ?>
				</a>
			</p>
			<?php } ?>
		</div>

		<?php if (Auth::isAdmin() && $old_files) { ?>
		<h2><span class="warning"><?php echo WT_I18N::translate('Old files found'); ?></span></h2>
		<div>
			<p>
				<?php echo WT_I18N::translate('Files have been found from a previous version of webtrees.  Old files can sometimes be a security risk.  You should delete them.'); ?>
			</p>
			<ul>
				<?php foreach ($old_files as $old_file) { ?>
				<li dir="ltr"><?php echo $old_file; ?></li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>

		<h2><?php echo WT_I18N::translate('Users'); ?></h2>
		<div id="users">
			<table>
				<tbody>
					<tr>
						<td><?php echo WT_I18N::translate('Total number of users'); ?></td>
						<td><?php echo $total_users; ?></td>
					</tr>
					<tr>
						<td><?php echo WT_I18N::translate('Administrators'); ?></td>
						<td><?php echo $total_administrators; ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo WT_I18N::translate('Managers'); ?></td>
					</tr>
					<?php foreach ($total_managers as $gedcom_title => $n) { ?>
					<tr>
						<td>&nbsp;&nbsp;<?php echo WT_Filter::escapeHtml($gedcom_title); ?></td>
						<td><?php echo $n; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td><?php echo WT_I18N::translate('Not verified by the user'); ?></td>
						<td><?php echo $unverified; ?></td>
					</tr>
					<tr>
						<td><?php echo WT_I18N::translate('Not approved by an administrator'); ?></td>
						<td><?php echo $unapproved; ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo WT_I18N::translate('Users’ languages'); ?></td>
					</tr>
					<?php foreach ($user_languages as $language => $n) { ?>
					<tr>
						<td>&nbsp;&nbsp;<?php echo WT_I18N::languageName($language); ?></td>
						<td><?php echo $n; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="2"><?php echo WT_I18N::translate('Users logged in'); ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo $stats->usersLoggedInList(); ?></td>
					</tr>
				</tbody>
			</table>
		</div>

		<h2><?php echo WT_I18N::translate('Family trees'); ?></h2>
		<div id="trees">
			<div id="tree_stats">
				<?php foreach (WT_Tree::getAll() as $tree) { ?>
				<?php $stats = new WT_Stats($tree->tree_name); ?>
				<h3><?php echo $stats->gedcomTitle(); ?></h3>
				<div>
					<table>
						<thead>
							<tr>
								<th><?php echo WT_I18N::translate('Records'); ?></th>
								<th><?php echo WT_I18N::translate('Count'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th><a href="indilist.php?ged=<?php echo $tree->tree_name_url; ?>"><?php echo WT_I18N::translate('Individuals'); ?></a></th>
								<td><?php echo $stats->totalIndividuals(); ?></td>
							</tr>
							<tr>
								<th><a href="famlist.php?ged=<?php echo $tree->tree_name_url; ?>"><?php echo WT_I18N::translate('Families'); ?></a></th>
								<td><?php echo $stats->totalFamilies(); ?></td>
							</tr>
							<tr>
								<th><a href="sourcelist.php?ged=<?php echo $tree->tree_name_url; ?>"><?php echo WT_I18N::translate('Sources'); ?></a></th>
								<td><?php echo$stats->totalSources(); ?></td>
							</tr>
							<tr><th><a href="repolist.php?ged=<?php echo $tree->tree_name_url; ?>"><?php echo WT_I18N::translate('Repositories'); ?></a></th>
								<td><?php echo$stats->totalRepositories(); ?></td>
							</tr>
							<tr><th><a href="medialist.php?ged=<?php echo $tree->tree_name_url; ?>"><?php echo WT_I18N::translate('Media objects'); ?></a></th>
								<td><?php echo$stats->totalMedia(); ?></td>
							</tr>
							<tr><th><a href="notelist.php?ged=<?php echo $tree->tree_name_url; ?>"><?php echo WT_I18N::translate('Notes'); ?></a></th>
								<td><?php echo$stats->totalNotes(); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php } ?>
			</div>
		</div>

		<h2><?php echo WT_I18N::translate('Recent changes'); ?></h2>
		<div id="recent2">
			<div id="changes">
				<?php foreach (WT_Tree::GetAll() as $tree) { ?>
				<h3><span dir="auto"><?php echo $tree->tree_title_html; ?></span></h3>
				<div>
					<table>
						<thead>
							<tr>
								<th><?php echo WT_I18N::translate('Records'); ?></th>
								<th><?php echo WT_I18N::translate('Day'); ?></th>
								<th><?php echo WT_I18N::translate('Week'); ?></th>
								<th><?php echo WT_I18N::translate('Month'); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th><?php echo WT_I18N::translate('Individuals'); ?></th>
								<td><?php echo WT_Query_Admin::countIndiChangesToday($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countIndiChangesWeek($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countIndiChangesMonth($tree->tree_id); ?></td>
							</tr>
							<tr>
								<th><?php echo WT_I18N::translate('Families'); ?></th>
								<td><?php echo WT_Query_Admin::countFamChangesToday($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countFamChangesWeek($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countFamChangesMonth($tree->tree_id); ?></td>
							</tr>
							<tr>
								<th><?php echo WT_I18N::translate('Sources'); ?></th>
								<td><?php echo WT_Query_Admin::countSourChangesToday($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countSourChangesWeek($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countSourChangesMonth($tree->tree_id); ?></td>
							</tr>
							<tr>
								<th><?php echo WT_I18N::translate('Repositories'); ?></th>
								<td><?php echo WT_Query_Admin::countRepoChangesToday($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countRepoChangesWeek($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countRepoChangesMonth($tree->tree_id); ?></td>
							</tr>
							<tr>
								<th><?php echo WT_I18N::translate('Media objects'); ?></th>
								<td><?php echo WT_Query_Admin::countObjeChangesToday($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countObjeChangesWeek($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countObjeChangesMonth($tree->tree_id); ?></td>
							</tr>
							<tr>
								<th><?php echo WT_I18N::translate('Notes'); ?></th>
								<td><?php echo WT_Query_Admin::countNoteChangesToday($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countNoteChangesWeek($tree->tree_id); ?></td>
								<td><?php echo WT_Query_Admin::countNoteChangesMonth($tree->tree_id); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php

/**
 * This is a list of old files and directories, from earlier versions of webtrees, that can be deleted.
 * It was generated with the help of a command like this:
 * git diff 1.6.0..master --name-status | grep ^D
 *
 * @return string[]
 */
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
		WT_ROOT.'themes/_administration/css-1.5.0',
		WT_ROOT.'themes/clouds/css-1.5.0',
		WT_ROOT.'themes/colors/css-1.5.0',
		WT_ROOT.'themes/fab/css-1.5.0',
		WT_ROOT.'themes/minimal/css-1.5.0',
		WT_ROOT.'themes/webtrees/css-1.5.0',
		WT_ROOT.'themes/xenea/css-1.5.0',
		// Removed in 1.5.2
		WT_ROOT.'js/webtrees-1.5.1.js',
		WT_ROOT.'themes/_administration/css-1.5.1',
		WT_ROOT.'themes/clouds/css-1.5.1',
		WT_ROOT.'themes/colors/css-1.5.1',
		WT_ROOT.'themes/fab/css-1.5.1',
		WT_ROOT.'themes/minimal/css-1.5.1',
		WT_ROOT.'themes/webtrees/css-1.5.1',
		WT_ROOT.'themes/xenea/css-1.5.1',
		// Removed in 1.5.3
		WT_ROOT.'js/jquery-1.10.2.js',
		WT_ROOT.'js/jquery-ui-1.10.3.js',
		WT_ROOT.'js/webtrees-1.5.2.js',
		WT_ROOT.'library/htmlpurifier-4.6.0',
		//WT_ROOT.'library/Michelf', On windows, this would delete library/michelf
		WT_ROOT.'library/tcpdf',
		WT_ROOT.'library/Zend',
		WT_ROOT.'modules_v3/GEDFact_assistant/_CENS/census_asst_help.php',
		WT_ROOT.'modules_v3/googlemap/admin_places.php',
		WT_ROOT.'modules_v3/googlemap/defaultconfig.php',
		WT_ROOT.'modules_v3/googlemap/googlemap.php',
		WT_ROOT.'modules_v3/googlemap/placehierarchy.php',
		WT_ROOT.'modules_v3/googlemap/places_edit.php',
		WT_ROOT.'modules_v3/googlemap/util.js',
		WT_ROOT.'modules_v3/googlemap/wt_v3_places_edit.js.php',
		WT_ROOT.'modules_v3/googlemap/wt_v3_places_edit_overlays.js.php',
		WT_ROOT.'modules_v3/googlemap/wt_v3_street_view.php',
		WT_ROOT.'readme.html',
		WT_ROOT.'themes/_administration/css-1.5.2',
		WT_ROOT.'themes/clouds/css-1.5.2',
		WT_ROOT.'themes/colors/css-1.5.2',
		WT_ROOT.'themes/fab/css-1.5.2',
		WT_ROOT.'themes/minimal/css-1.5.2',
		WT_ROOT.'themes/webtrees/css-1.5.2',
		WT_ROOT.'themes/xenea/css-1.5.2',
		// Removed in 1.6.0
		WT_ROOT.'downloadbackup.php',
		WT_ROOT.'includes/functions/functions_utf-8.php',
		WT_ROOT.'js/jquery.colorbox-1.4.15.js',
		WT_ROOT.'js/jquery.cookie-1.4.0.js',
		WT_ROOT.'js/jquery.datatables-1.9.4.js',
		WT_ROOT.'js/jquery.jeditable-1.7.1.js',
		WT_ROOT.'js/jquery.wheelzoom-1.1.2.js',
		WT_ROOT.'js/jquery-1.11.0.js',
		WT_ROOT.'js/webtrees-1.5.3.js',
		WT_ROOT.'library/WT/Debug.php',
		WT_ROOT.'modules_v3/ckeditor/ckeditor-4.3.2-custom',
		WT_ROOT.'site-php-version.php',
		WT_ROOT.'themes/_administration/css-1.5.3',
		WT_ROOT.'themes/clouds/css-1.5.3',
		WT_ROOT.'themes/colors/css-1.5.3',
		WT_ROOT.'themes/fab/css-1.5.3',
		WT_ROOT.'themes/minimal/css-1.5.3',
		WT_ROOT.'themes/webtrees/css-1.5.3',
		WT_ROOT.'themes/xenea/css-1.5.3',
		// Removed in 1.6.1
		WT_ROOT.'includes/authentication.php',
	);
}
